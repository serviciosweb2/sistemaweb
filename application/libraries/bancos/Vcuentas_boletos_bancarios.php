<?php

class Vcuentas_boletos_bancarios{
    
    private $cod_banco;
    private $cod_configuracion;
    private $cod_facturante;
    public $numero_secuencia;
    public $cantidad_copias;
    public $convenio;
    public $carteira;
    public $variacao_carteira;
    public $demostrativo1;
    public $demostrativo2;
    public $demostrativo3;
    public $instrucciones1;
    public $instrucciones2;
    public $instrucciones3;
    public $instrucciones4;


    private static $tableName = "bancos.cuentas_boletos_bancarios";
    private $oConnection;
    private $exists = false;
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codBanco, $codConfiguracion, $codFacturante) {
        $this->oConnection = $conexion;
        $this->cod_banco = $codBanco;
        $this->cod_configuracion = $codConfiguracion;
        $this->cod_facturante = $codFacturante;

        $arrCuentas = self::__constructor($conexion, $codBanco, $codConfiguracion, $codFacturante);
        if (count($arrCuentas) > 0){
            $this->carteira = $arrCuentas[0]['carteira'];
            $this->convenio = $arrCuentas[0]['convenio'];
            $this->numero_secuencia = $arrCuentas[0]['numero_secuencia'];
            $this->cantidad_copias = $arrCuentas[0]['cantidad_copias'];
            $this->variacao_carteira = $arrCuentas[0]['variacao_carteira'];
            $this->demostrativo1 = $arrCuentas[0]['demostrativo1'];
            $this->demostrativo2 = $arrCuentas[0]['demostrativo2'];
            $this->demostrativo3 = $arrCuentas[0]['demostrativo3'];
            $this->instrucciones1 = $arrCuentas[0]['instrucciones1'];
            $this->instrucciones2 = $arrCuentas[0]['instrucciones2'];
            $this->instrucciones3 = $arrCuentas[0]['instrucciones3'];
            $this->instrucciones4 = $arrCuentas[0]['instrucciones4'];
            $this->exists = true;
        } else {
            $this->exists = false;
        }
        
    }
    
    /* PRIVATE FUNCIONS */
    /*static function  getCuentas(CI_DB_mysqli_driver $conexion,$cod_banco,$cod_cuenta){
   
            $conexion->select(self::$tableName.".*", false);
        $conexion->from(self::$tableName);
        $conexion->where("cod_banco", $cod_banco);
        $conexion->where("cod_cuenta", $cod_cuenta);
   
        $query = $conexion->get();
        return $query->result_array();
        
    }*/
    static private function __constructor(CI_DB_mysqli_driver $conexion, $codBanco, $codConfiguracion, $codFacturante){
        $conexion->select(self::$tableName.".*", false);
        $conexion->from(self::$tableName);
        $conexion->where("cod_banco", $codBanco);
        $conexion->where("cod_configuracion", $codConfiguracion);
        $conexion->where("cod_facturante", $codFacturante);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    private function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['cod_banco'] = $this->cod_banco;
        $arrTemp['cod_configuracion'] = $this->cod_configuracion;
        $arrTemp['cod_facturante'] = $this->cod_facturante;
        $arrTemp['numero_secuencia'] = $this->numero_secuencia;
        $arrTemp['cantidad_copias'] = $this->cantidad_copias;
        $arrTemp['convenio'] = $this->convenio;
        $arrTemp['carteira'] = $this->carteira;
        $arrTemp['variacao_carteira'] = $this->variacao_carteira;
        $arrTemp['demostrativo1'] = $this->demostrativo1;
        $arrTemp['demostrativo2'] = $this->demostrativo2;
        $arrTemp['demostrativo3'] = $this->demostrativo3;
        $arrTemp['instrucciones1'] = $this->instrucciones1;
        $arrTemp['instrucciones2'] = $this->instrucciones2;
        $arrTemp['instrucciones3'] = $this->instrucciones3;
        $arrTemp['instrucciones4'] = $this->instrucciones4;
        return $arrTemp;
    }
    
    private function _insertar(){
        $this->exists = $this->oConnection->insert(self::$tableName, $this->_getArrayDeObjeto());
        return $this->exists;
    }
    
    private function _actualizar(){
        $this->oConnection->where('cod_banco', $this->cod_banco);
        $this->oConnection->where('cod_configuracion', $this->cod_configuracion);
        $this->oConnection->where('cod_facturante', $this->cod_facturante);
        return $this->oConnection->update(self::$tableName, $this->_getArrayDeObjeto());
    }
    
    /* PUBLIC FUNCTIONS */
    
    function guardar(){
        if ($this->exists){
            return $this->_actualizar();
        } else {
            return $this->_insertar();
        }
    }
    
    public function incremetarNumeroSequencial(){
        $where = array("cod_banco"=>  $this->cod_banco,"cod_facturante"=>  $this->cod_facturante, "cod_configuracion"=> $this->cod_configuracion);
        $datos = array("numero_secuencia"=>  $this->numero_secuencia + 1);
        $this->oConnection->where($where);
        if ($this->oConnection->update("bancos.cuentas_boletos_bancarios",$datos)){
            $this->numero_secuencia ++;
            return true;
        } else {
            return false;
        }
    }
    
   
    /* STATIC FUNCTIONS */  
    static function getCampos(){
        $arrResp = array();
        $arrResp[] = "cod_banco";
        $arrResp[] = "cod_cuenta";
        $arrResp[] = "numero_secuencia";
        $arrResp[] = "cantidad_copias";
        $arrResp[] = "convenio";
        $arrResp[] = "carteira";
        $arrResp[] = "variacao_carteira";
        $arrResp[] = "demostrativo1";
        $arrResp[] = "demostrativo2";
        $arrResp[] = "demostrativo3";
        $arrResp[] = "instrucciones1";
        $arrResp[] = "instrucciones2";
        $arrResp[] = "instrucciones3";
        $arrResp[] = "instrucciones4";
        return $arrResp;
    }
    
    public function getObjectoBanco(){
        $nombreClase = '';
        switch ($this->cod_banco) {
            case "1":
                $nombreClase = "Vbanco_do_brasil";                
                break;
            default:
                throw new Exception("No exsite configuracion de banco para esta cuenta");
                break;
        }

        return new $nombreClase($this->oConnection, $this->cod_configuracion);
        
    }
    
}