<?php

/**
* Class Vseminarios
*
*Class  Vseminarios maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vseminarios extends Tseminarios{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardarForzado($id){
        $arrTemp = $this->_getArrayDeObjeto();
        $arrTemp['id'] = $id;
        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)){
            $this->id = $id;
            return true;
        } else {
            return false;
        }
    }
    
    public function registrar_inscripcion($idInscripto, $fecha){
        $arrTemp = array(
            'id_seminario' => $this->id,
            'id_inscripto' => $idInscripto,
            'asistencia' => 'pendiente',
            'fecha_inscripcion' => $fecha
        );
        return $this->oConnection->insert("seminarios.inscripciones", $arrTemp);
    }    
    
    static function listar(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $idFilial = null){
        $conexion->select("*");
        $conexion->from("seminarios.seminarios");
        if ($fechaDesde != null){
            $conexion->where("DATE(fecha) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(fecha) <=", $fechaHasta);
        }
        if ($idFilial != null){
            $conexion->where("id_filial", $idFilial);
        }
        $query = $conexion->get();
        return $query->result_array();
    }    
    
    static function listarHorariosDataTable(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $idFilial = null,
            $idSeminario = null, $arrCondindicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false){
        $conexion->select("DATE_FORMAT(seminarios.seminarios.fecha, '%d/%m/%Y %H:%i') AS horario", false);
        $conexion->select("seminarios.cupo");
        $conexion->select("CONCAT(inscriptos.nombre, ' ', inscriptos.apellido) AS nombre", false);
        $conexion->select("inscriptos.telefono");
        $conexion->select("inscriptos.documento");
        $conexion->select("inscriptos.email");
        $conexion->select("DATE_FORMAT(seminarios.inscripciones.fecha_inscripcion, '%d/%m/%Y %H:%i') as fecha_inscripto", false);
        $conexion->from("seminarios.seminarios");
        $conexion->join("seminarios.inscripciones", "seminarios.inscripciones.id_seminario = seminarios.seminarios.id");
        $conexion->join("seminarios.inscriptos", "seminarios.inscriptos.id = seminarios.inscripciones.id_inscripto");
        if ($fechaDesde != null){
            $conexion->where("DATE(seminarios.seminarios.fecha) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(seminarios.seminarios.fecha) <=", $fechaHasta);
        }
        if ($idFilial != null){
            $conexion->where("seminarios.seminarios.id_filial", $idFilial);
        }
        if ($idSeminario != null){
            $conexion->where("seminarios.seminarios.id", $idSeminario);
        }        
        if ($arrCondindicioneslike != null) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value) {
                $arrTemp[] = "$key LIKE '%$value%'";                
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
            if (isset($arrSort[2]) && isset($arrSort[3])){
                $conexion->order_by($arrSort["2"], $arrSort["3"]);
            }
        }
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;        
    }    
}