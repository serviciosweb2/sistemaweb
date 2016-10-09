<?php

/**
* Class Vcupones_landing_comentarios
*
*Class  Vcupones_landing_comentarios maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vcupones_landing_comentarios extends Tcupones_landing_comentarios{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static public function listar(CI_DB_mysqli_driver $conexion, $idCupon = null, $idUsuario = null, $fechaDesde = null, $fechaHasta = null){
        $conexion->select("publicidad.cupones_landing_comentarios.*", false);
        $conexion->select("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) as usuario_nombre", false);
        $conexion->from("publicidad.cupones_landing_comentarios");
        $conexion->join("general.usuarios_sistema", "general.usuarios_sistema.codigo = publicidad.cupones_landing_comentarios.id_usuario");
        if ($idCupon != null){
            $conexion->where("publicidad.cupones_landing_comentarios.id_cupon_landing", $idCupon);
        }
        if ($idUsuario != null){
            $conexion->where("publicidad.cupones_landing_comentarios.id_usuario", $idUsuario);
        }
        if ($fechaDesde != null){
            $conexion->where("DATE(publicidad.cupones_landing_comentarios.fecha) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(publicidad.cupones_landing_comentarios.fecha) <=", $fechaHasta);
        }
        $query = $conexion->get();
        return $query->result_array();
    }    
}