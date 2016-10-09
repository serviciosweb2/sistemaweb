<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Vretornos extends class_general {

    public $headerArchivo;
    public $headerLote;
    public $trailerArchivo;
    public $boletosArchivo;
    public $trailerLote;
    private $oConnection;
    public $cod_banco;
    public $cod_configuracion;
    public $cod_facturante;
    private $directorioArchvios = "/var/www/";
    private $bucket = "igacloud/boletos/retornos";
    private $fileNameTemp;

    function __construct($conexion, $file) {




        $this->oConnection = $conexion;
        $this->fileNameTemp = $file;
        $archivo = file_get_contents($this->fileNameTemp);
        $arrLineas = explode("\n", $archivo);


        $headerArchivo = $arrLineas[0];
        $headerRetorno = $arrLineas[1];
        $trailerArchivo = $arrLineas[count($arrLineas) - 2];
        $trailerRetorno = $arrLineas[count($arrLineas) - 3];
        $segmentos = array();
        for ($i = 2; $i < count($arrLineas) - 3; $i = $i + 2) {
            $segmentos[$i / 2]["T"] = $arrLineas[$i];
            $segmentos[$i / 2]["U"] = $arrLineas[$i + 1];
        }



        $this->headerArchivo = new header_archivo($headerArchivo);


        if ($this->headerArchivo->archivo_codigo <> 2) {
            throw new Exception('TIPO_NO_COMPATIBLE');
        }

        //VERIFICA QUE CORRESPONDAN A CUENTAS DE ESTE FACUTANTE
        $facturantes = Vfacturantes::listarFacturantes($conexion);

        $exiteConvenio = false;

        foreach ($facturantes as $facturante) {

            $ObjFacturante = new Vfacturantes($conexion, $facturante["codigo"]);

            $CuentasDeBoleto = $ObjFacturante->getCuentasBoletoBancario();

            foreach ($CuentasDeBoleto as $cuenta) {

                if ($cuenta["convenio"] == $this->headerArchivo->empresa_convenio) {


                    $exiteConvenio = true;
                    $this->cod_banco = $cuenta["cod_banco"];
                    $this->cod_configuracion = $cuenta["cod_configuracion"];
                    $this->cod_facturante = $cuenta["cod_facturante"];
                }
            }
        }
        if ($exiteConvenio === false) {

            throw new Exception('NO_EXISTE_CONVENIO_OMITIR');
        }





        $this->headerLote = new header_lote($headerRetorno);
        $boletos = array();
        $i = 0;
        foreach ($segmentos as $segmento) {
            $boletos[$i]["T"] = $mySegmentoT = new segmento_t($segmento['T']);

            $boletos[$i]["U"] = $mySegmentoU = new segmento_u($segmento['U']);
            $i++;
        }

        $this->boletosArchivo = $boletos;



        $this->trailerLote = new trailer_lote($trailerRetorno);


        $this->trailerArchivo = new trailer_archivo($trailerArchivo);
    }

    function ExisteSecuencial() {
        $this->oConnection->count_all_results("bancos.retornos");


        $this->oConnection->where("cod_facturante", $this->cod_facturante);
        $this->oConnection->where("cod_configuracion", $this->cod_configuracion);
        $this->oConnection->where("cod_banco", $this->cod_banco);
        $this->oConnection->where("archivo_secuencia", $this->headerArchivo->archivo_secuencia);
        $this->oConnection->from('bancos.retornos');
        return $this->oConnection->count_all_results();
    }
    static function testarchivo(){
          var_dump($this->s3->putBucket('ssss', $this->s3->ACL_PUBLIC_READ));
    var_dump($this->s3->listBuckets());     
        
        
    }
    private function saveArchivo($nombreArchivoUsuario) {

      
        $input = S3::inputFile($this->fileNameTemp);
        if (S3::putObject($input, "igacloud", $nombreArchivoUsuario, "private")) {
             $data = array(
                'cod_banco' => $this->cod_banco,
                'cod_configuracion' => $this->cod_configuracion,
                'cod_facturante' => $this->cod_facturante,
                'archivo_secuencia' => $this->headerArchivo->archivo_secuencia,
                'fecha_retorno' => $this->headerArchivo->archivo_fecha_generacion . " " . $this->headerArchivo->archivo_hora_generacion ,
                'nombre_archivo_usuario'=> $nombreArchivoUsuario    
            );
             
                return $this->oConnection->insert('bancos.retornos', $data);
             
        } else {
            echo "Failed to upload file.";
        }

    }

    public function ProcessarArchivo($cod_usuario,$nombreArchivoUsuario,$filial = null, $usarFilial = false) {

        $arrBoletos = array();
        if ($this->headerArchivo->archivo_layaut === "030") {


            foreach ($this->boletosArchivo as $key => $boletoAarchivo) {
                try {
                    $MyBoleto = new Vboletos_bancarios($this->oConnection, null, $boletoAarchivo["T"]->numero_seguimiento);
                    if($usarFilial && $MyBoleto->cod_filial != $filial){
                        $arrBoletos[$key]["error"] = "El boleto no pertenece a esta filial";
                        continue;
                    }
                    $arrBoletos[$key]["ObjBoleto"] = $MyBoleto;
                    $arrBoletos[$key]["seguimiento"] = $boletoAarchivo["T"]->numero_seguimiento;
                    $MyBoleto->setEstado($cod_usuario, $boletoAarchivo["T"], $boletoAarchivo["U"], $this->headerArchivo->archivo_secuencia);
                } catch (Exception $exc) {
                    $arrBoletos[$key]["error"] = $exc->getMessage();
                }
            }
        }
        if (!$this->ExisteSecuencial()) {
            $this->saveArchivo($nombreArchivoUsuario);
        }
        return $arrBoletos;
    }

    static function getSecuenciasFaltantes(CI_DB_mysqli_driver $conexion, $codFacturante, $sinAlertar = true){
        $arrResp = array();
        $conexion->select("IFNULL(MIN(bancos.retornos.archivo_secuencia), 0) AS secuencia_inicial", false);
        $conexion->from("bancos.retornos");
        $conexion->where("secuencia_verificada", 0);
        $conexion->where("cod_facturante", $codFacturante);
        if ($sinAlertar){
            $conexion->where("alertado", 0);
        }
        $query = $conexion->get();
        $resp = $query->result_array();
        $secuenciaInicial = $resp[0]['secuencia_inicial'];
        
        $conexion->select("IFNULL(MAX(bancos.retornos.archivo_secuencia), 0) AS secuencia_final", false);
        $conexion->from("bancos.retornos");
        $conexion->where("secuencia_verificada", 0);
        $conexion->where("cod_facturante", $codFacturante);
        if ($sinAlertar){
            $conexion->where("alertado", 0);
        }
        $query = $conexion->get();
        $resp = $query->result_array();
        $secuenciaFinal = $resp[0]['secuencia_final'];
        
        if ($secuenciaInicial > 0 && $secuenciaFinal > 0){
            $arrQuery = array();
            for ($i = $secuenciaInicial + 1; $i < $secuenciaFinal; $i++){
                $arrQuery[] = "UNION SELECT $i";
            }
            $consulta = "secuencia FROM ( SELECT 0 AS secuencia ".implode(" ", $arrQuery).") AS secuencia 
                                WHERE secuencia <> 0 AND secuencia NOT IN (SELECT bancos.retornos.archivo_secuencia 
                                            FROM bancos.retornos 
                                            WHERE bancos.retornos.cod_facturante = $codFacturante
                                            AND alertado = 1)";
            $query = $conexion->select($consulta)->get();
            $arrSecuencia = $query->result_array();
            foreach ($arrSecuencia as $secuencia){
                $arrResp[] = $secuencia['secuencia'];
            }            
        }
        return $arrResp;
    }
    
    static function marcarAlertadas(CI_DB_mysqli_driver $conexion, $codFacturante, $ultimoCodigoAlertar){
        $conexion->where("cod_facturante", $codFacturante);
        $conexion->where("archivo_secuencia <=", $ultimoCodigoAlertar);
        return $conexion->update("bancos.retornos", array("alertado" => 1));        
    }
        
    static function marcarSecuenciasCorrectas(CI_DB_mysqli_driver $conexion, $codfacturante, $numeroSecuenciasMenores){
        $conexion->where("cod_facturante", $codfacturante);
        $conexion->where("archivo_secuencia <", $numeroSecuenciasMenores);
        return $conexion->update("bancos.retornos", array("secuencia_verificada" => 1));        
    }

}
