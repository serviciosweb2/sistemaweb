<?php

class reporte_alumnos_activos{
    
    private $id_filial;
    private $fecha;
    private $id_plan_academico;
    private $id_tipo_periodo;
    private $modalidad;
    private $oConnection;    
    private $_exists = false;
    public $cantidad;
    public $nombre_categoria;
    public $cod_categoria;
    
    static private $db = 'reportes_sistema';
    static private $tableName = 'reporte_alumnos_activos';
    
    /* CONSTRUCTOR */
    function __construct(CI_DB_mysqli_driver $conexion, $codFilial, $fecha, $idPlanAcademico, $idPeriodo, $modalidad) {
        $this->id_filial = $codFilial;
        $this->fecha = $fecha;
        $this->id_plan_academico = $idPlanAcademico;
        $this->id_tipo_periodo = $idPeriodo;
        $this->modalidad = $modalidad;
        $this->oConnection = $conexion;
        $arrTemp = self::_constructor($conexion, $codFilial, $fecha, $idPlanAcademico, $idPeriodo, $modalidad);
        if (count($arrTemp) > 0){
            $this->cantidad = $arrTemp[0]['cantidad'];
            $this->cod_categoria = $arrTemp[0]['cod_categoria'];
            $this->nombre_categoria = $arrTemp[0]['nombre_categoria'];
            $this->_exists = true;
        } else {
            $this->_exists = false;
        }
    }
    
    /* PRIVATE FUNCTIONS */
    
    static private function _constructor(CI_DB_mysqli_driver $conexion, $codFilial, $fecha, $idPlanAcademico, $idPeriodo, $modalidad){
        $db = self::$db;
        $tb = self::$tableName;
        $conexion->select("*");
        $conexion->from("$db.$tb");
        $conexion->where("$db.$tb.id_filial", $codFilial);
        $conexion->where("$db.$tb.fecha", $fecha);
        $conexion->where("$db.$tb.id_plan_academico", $idPlanAcademico);
        $conexion->where("$db.$tb.id_tipo_periodo", $idPeriodo);
        $conexion->where("$db.$tb.modalidad", $modalidad);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    private function _actualizar(){
        $arrTemp = array(
            "cantidad" => $this->cantidad,
            "cod_categoria" => $this->cod_categoria,
            "nombre_categoria" => $this->nombre_categoria
        );
        $this->oConnection->where("id_filial", $this->id_filial);
        $this->oConnection->where("fecha", $this->fecha);
        $this->oConnection->where("id_plan_academico", $this->id_plan_academico);
        $this->oConnection->where("id_tipo_periodo", $this->id_tipo_periodo);
        $this->oConnection->where("modalidad", $this->modalidad);
        $db = self::$db;
        $tb = self::$tableName;
        return $this->oConnection->update("$db.$tb", $arrTemp);
    }
    
    private function _insertar(){
        $arrTemp = array(
            "id_filial" => $this->id_filial,
            "fecha" => $this->fecha,
            "id_plan_academico" => $this->id_plan_academico,
            "id_tipo_periodo" => $this->id_tipo_periodo,
            "modalidad" => $this->modalidad,
            "cantidad" => $this->cantidad,
            "cod_categoria" => $this->cod_categoria,
            "nombre_categoria" => $this->nombre_categoria
        );
        $db = self::$db;
        $tb = self::$tableName;
        $this->_exists = $this->oConnection->insert("$db.$tb", $arrTemp);
        return $this->_exists;
    }
    
    /* PUBLIC FUNCTIONS */
    
    public function guardar(){
        if ($this->_exists){
            return $this->_actualizar();
        } else {
            return $this->_insertar();
        }
    }    
    
    /* STATIC FUNCTIONS */
    
    static public function getReporte(CI_DB_mysqli_driver $conexion, $idFilial, $fecha = null, $mes = null, $anio = null){
        $arrResp = array();
        $db = self::$db;
        $tb = self::$tableName;
        if ($mes == null && $anio == null){
            if ($fecha == null){
                $conexion->select("MAX(fecha) AS fecha");
                $conexion->from("$db.$tb");
                $conexion->where("id_filial", $idFilial);
                $query = $conexion->get();
                $temp = $query->resul_array();
                $fecha = isset($temp[0]) && isset($temp[0]['fecha']) ? $temp[0]['fecha'] : null;
            }
        } else {
            $conexion->select("MAX(fecha) AS fecha", false);
            $conexion->from("$db.$tb");
            $conexion->where("id_filial", $idFilial);
            $conexion->where("YEAR(fecha)", $anio);
            $conexion->where("MONTH(fecha)", $mes);
            $query = $conexion->get();
            $temp = $query->result_array();
            $fecha = isset($temp[0]) && isset($temp[0]['fecha']) ? $temp[0]['fecha'] : null;
        }
        if ($fecha != null){
            $conexion->select("$db.$tb.id_tipo_periodo");
            $conexion->select("$db.$tb.id_plan_academico");
            $conexion->select("SUM($db.$tb.cantidad) AS cantidad");
            $conexion->from("$db.$tb");
            $conexion->where("$db.$tb.id_filial", $idFilial);
            $conexion->where("$db.$tb.fecha", $fecha);
            $conexion->group_by("id_plan_academico");
            $conexion->group_by("id_tipo_periodo");
            $query = $conexion->get();
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }
}