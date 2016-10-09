<?php

/**
 * Class Vsalones
 *
 * Class  Vsalones maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vsalones extends Tsalones {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getColorsalon($conexion, $color,$cod_salon) {
        $conexion->select('salones.color');
        $conexion->from('salones');
        $conexion->where('salones.color', $color);
        $conexion->where('salones.codigo <>',$cod_salon);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getHorarios() {
        $conexion = $this->oConnection;
        $conexion->select('horarios.*');
        $conexion->from('salones');
//        $conexion->where('salones.color', $color);
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getSalonesFrmHorario($conexion) {
        $conexion->select('salones.*');
        $conexion->select('(select count(horarios.codigo) from horarios where horarios.cod_salon = salones.codigo) as tienehorarios');
        $conexion->from('salones');
        $conexion->where('salones.estado', 0);
        $conexion->order_by('salones.tipo','DESC');
        $conexion->order_by('salones.salon','ASC');
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function validarNombreSalon(CI_DB_mysqli_driver $conexion, $nombreSalon, $cod_salon = ''){
        $conexion->select('salones.*');
        $conexion->from('salones');
        $conexion->where('salones.salon',$nombreSalon);
        if($cod_salon != ''){
            $conexion->where('salones.codigo <>',$cod_salon);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function cantidadSalonesPorTipo(CI_DB_mysqli_driver $conexion, $tipo,$cod_salon=''){
        $conexion->select('count(salones.codigo) as cantidad_salones_tipo');
        $conexion->from('salones');
        $conexion->where('salones.tipo',$tipo);
        $conexion->where('salones.estado',0);
        if($cod_salon != ''){
            $conexion->where('salones.codigo <>',$cod_salon);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    public function updateColorSalon($color){
        $arrActualizar = array(
            "color"=>$color
        );
        $this->oConnection->where('salones.codigo',  $this->codigo);
        $this->oConnection->update('salones',$arrActualizar);
    }
    
}

