<?php 

class reportes_cajas{
    
    private $oConnection;
    private $cod_filial;
    private $tableName;
    private $codigo_apertura;
    private $cod_caja;
    public $fecha;
    public $debe;
    public $haber;
    public $saldo;
    public $cod_medio;
    public $cod_usuario;
    
    private $exists = false;
    
    static private $baseTableName = "reportes_cajas_";
    static private $dataBase = "reportes_sistema";
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codFilial, $codCaja, $codApertura){
        $this->oConnection = $conexion;
        $this->cod_filial = $codFilial;
        $this->cod_caja = $codCaja;
        $this->codigo_apertura = $codApertura;
        $this->tableName = self::$baseTableName.$codFilial;
        $arrReporte = reportes_cajas::constructor($conexion, $codFilial, $codCaja, $codApertura);
        if (count($arrReporte) > 0){
            $this->exists = true;
            $this->fecha = $arrReporte[0]['fecha'];
            $this->debe = $arrReporte[0]['debe'];
            $this->haber = $arrReporte[0]['haber'];
            $this->saldo = $arrReporte[0]['saldo'];
            $this->cod_caja = $arrReporte[0]['cod_caja'];
            $this->cod_medio = $arrReporte[0]['cod_medio'];
            $this->cod_usuario = $arrReporte[0]['cod_usuario'];
            $this->codigo_apertura = $arrReporte[0]['codigo_apertura'];
        } else {
            $this->exists = false;
        }
    }
    
    /* PRIVTAE FNUCTIONS */
    
    /**
     * Retorna el objeto en formato array
     * 
     * @return array
     */
    private function getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['debe'] = $this->debe;
        $arrTemp['haber'] = $this->haber;
        $arrTemp['saldo'] = $this->saldo;
        $arrTemp['cod_caja'] = $this->cod_caja;
        $arrTemp['cod_medio'] = $this->cod_medio;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['codigo_apertura'] = $this->codigo_apertura;
        return  $arrTemp;
    }
    
    /**
     * inserta un registro de reporte nuevo
     * 
     * @return boolean
     */
    private function _insert(){
        if ($this->oConnection->insert($this->tableName, $this->getArrayDeObjeto())){
            $this->codigo = $this->oConnection->insert_id();
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * actualiza un registro existente
     * 
     * @return boolean
     */
    private function _update(){
        return $this->oConnection->update($this->tableName, $this->getArrayDeObjeto(), array("cod_caja" => $this->cod_caja, "codigo_apertura" => $this->codigo_apertura));
    }
    
    /**
     * constructor interno
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param integer $codFilial
     * @param integer $codCaja
     * @param integer $codApertura
     * @return array
     */
    static private function constructor(CI_DB_mysqli_driver $conexion, $codFilial, $codCaja, $codApertura){
        $conexion->select("*");
        $conexion->from(self::$baseTableName.$codFilial);
        $conexion->where("cod_caja", $codCaja);
        $conexion->where("codigo_apertura", $codApertura);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /* PUBLIC FUNCIOTNS */
    
    /**
     * guarda un registro insertando o actualizando segun sea el caso
     * 
     * @return boolean
     */
    public function guardar(){
        if ($this->exists){
            return $this->_update();
        } else {
            return $this->_insert();
        }
    }
    
    /* STATIC FUNCTIONS */
    
    /**
     * valida que la tabla donde se guarda el reporte para la filial  exista, caso contrario crea la tabla
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param integer $codFilial
     * @return boolean
     */
    static function validarTabla(CI_DB_mysqli_driver $conexion, $codFilial){
        if ($conexion->database <> self::$dataBase){
            die("se debe seleccionar la base de datos de reportes");
        } else {
            if (!$conexion->table_exists("reportes_cajas_{$codFilial}")){
                $query = "CREATE TABLE `reportes_cajas_{$codFilial}` (
                                `cod_caja` int(11) NOT NULL,
                                `codigo_apertura` int(11) NOT NULL,
                                `fecha` datetime NOT NULL,
                                `debe` float(15,2) NOT NULL,
                                `haber` float(15,2) NOT NULL,
                                `saldo` float(15,2) NOT NULL,
                                `cod_medio` int(11) NOT NULL,
                                `cod_usuario` int(11) NOT NULL,
                                PRIMARY KEY  (`cod_caja`,`codigo_apertura`)
                              ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                return $conexion->query($query);
            } else {
                return true;
            }
        }
    }
    
    /**
     * retorna los ultimos registros del informe para una filial separados por por codigo de caja
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param integer $codFilial
     * @return array
     */
    static function getUltimosRegistrados(CI_DB_mysqli_driver $conexion, $codFilial){
        $conexion->select("cod_caja");
        $conexion->select_max("codigo_apertura", "codigo_apertura");
        $conexion->from(self::$baseTableName.$codFilial);
        $conexion->group_by("cod_caja");
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        $arrResp = array();
        foreach ($arrTemp as $valores){
            $arrResp[$valores['cod_caja']] = $valores['codigo_apertura'];
        }
        return $arrResp;
    }
    
    /**
     * retorna los registros que deben agregarse al reporte
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param integer $codCaja
     * @param integer $ultimoRegistrado
     * @return array
     */
    static function getRegistrosActualizar(CI_DB_mysqli_driver $conexion, $codCaja, $ultimoRegistrado){
        $conceptoCierre = Vmovimientos_caja::getConceptoCierre();
        $conexion->select("SUM(debe)");
        $conexion->from("movimientos_caja AS mc1");
        $conexion->where("mc1.cod_caja = movimientos_caja.cod_caja");
        $conexion->where("mc1.codigo_apertura = movimientos_caja.codigo_apertura");
        $conexion->where("mc1.cod_concepto <>", $conceptoCierre);
        $subqueryDebe = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("SUM(haber)");
        $conexion->from("movimientos_caja AS mc1");
        $conexion->where("mc1.cod_caja = movimientos_caja.cod_caja");
        $conexion->where("mc1.codigo_apertura = movimientos_caja.codigo_apertura");
        $conexion->where("mc1.cod_concepto <>", $conceptoCierre);
        $subqueryHaber = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("movimientos_caja.*");
        $conexion->select("($subqueryDebe) AS cierre_debe");
        $conexion->select("($subqueryHaber) AS cierre_haber");
        $conexion->select("(($subqueryHaber) - ($subqueryDebe)) AS cierre_saldo");
        $conexion->from("movimientos_caja");
        $conexion->where("cod_concepto", $conceptoCierre);
        $conexion->where("cod_caja", $codCaja);
        $conexion->where("codigo_apertura >", $ultimoRegistrado);
        $query = $conexion->get();
        return $query->result_array();
    }
}

