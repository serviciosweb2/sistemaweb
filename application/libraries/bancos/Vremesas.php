<?php

/**
* Class Vboletos_bancarios
*
*Class  Vboletos_bancarios maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vremesas extends Tremesas{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getLineas(){
        $this->oConnection->select("codigo");
        $this->oConnection->from("bancos.boletos_bancarios");
        $this->oConnection->where("cod_remesa", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        $arrResp = array();
        foreach ($arrTemp as $linea){
            $arrResp[] = new Vboletos_bancarios($this->oConnection, $linea['codigo']);
        }
        return $arrResp;
    }
    
    public function generarArchivoRemessa(){
        $saltoLinea = "\n";
        $myBanco = new Vbancos($this->oConnection, $this->cod_banco);
        $myCuentaBanco = $myBanco->getCuentaBanco($this->cod_configuracion);
        $retorno = $myCuentaBanco->getHeaderRemessa($this);
        $retorno .= $saltoLinea;
        $retorno .= $myCuentaBanco->getHeaderLoteRemessa($this);
        $retorno .= $saltoLinea;
        $arrLineasBoleto = $this->getLineas();
        foreach ($arrLineasBoleto as $key => $myLineaBoleto){
            $retorno .= $myCuentaBanco->getDetalleSegmentoPRemessa($this, $myLineaBoleto);
            $retorno .= $saltoLinea;
            $retorno .= $myCuentaBanco->getDetalleSegmentoQRemessa($myLineaBoleto, $this->cedente_convenio);
            $retorno .= $saltoLinea;
            if ((float) ($myLineaBoleto->valor_boleto) > 0){
                $retorno .= $myCuentaBanco->getDetalleSegmentoRRemessa($myLineaBoleto);
                $retorno .= $saltoLinea;
            }
        }
        $retorno .= $myCuentaBanco->getTrailerLoteRemessa(count($arrLineasBoleto));
        $retorno .= $saltoLinea;
        $retorno .= $myCuentaBanco->getTrailerArchivoRemessa(count($arrLineasBoleto));
        $retorno .= $saltoLinea;
        return $retorno;
    }

    public function esDeFilial($codigoFilial){
        
        $this->oConnection->join("bancos.boletos_bancarios","boletos_bancarios.cod_remesa = remesas.codigo");
        
        
        $condiciones = array("cod_filial"=>$codigoFilial,
            "remesas.codigo"=> $this->codigo);
    
        return  count( $this->listarRemesas($this->oConnection,$condiciones)) > 0 ? true : false ;
        
    }

    public function enviar(){
        $fecha = DateTime::createFromFormat('Y-m-d His', $this->fecha_documento . ' 120000');
        //Ya tengo que entregar esto, me andan apurando, pero una buena hubiera sido agregar otro campo
        //en la tabla remesas con el nombre de archivo, asi nos aseguramos que una remesa enviada se
        //envia una sola vez.
        $nombreArchivo = '/ftp/remessa/ied240.xiga.' . $fecha->format('dmyHis') . '0' . '.bco001';
        while(file_exists($nombreArchivo)){
            $fecha->modify('+1 second');
            $nombreArchivo = '/ftp/remessa/ied240.xiga.' . $fecha->format('dmyHis') . '0' . '.bco001';
        }
        $archivo = $this->generarArchivoRemessa();
        if(file_put_contents($nombreArchivo, $archivo) === false){
            return false;
        } else {
            $this->enviada = '1';
            $this->guardarRemesas();
            return $nombreArchivo;
        }
    }

}

?>
