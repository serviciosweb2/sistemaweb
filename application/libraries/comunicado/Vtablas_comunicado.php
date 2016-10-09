<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Vtablas_comunicado extends Ttablas_comunicado{
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function getComunicadosFilial(CI_DB_mysqli_driver $conexion,$filial,$codigo=null){
        $conexion->select('*');
        $conexion->from('general.tablas_comunicado');
        $conexion->join('general.filial_comunicado','general.filial_comunicado.id_comunicado = general.tablas_comunicado.id_comunicado');
        $conexion->where('general.filial_comunicado.cod_filial',$filial);
        if($codigo != null){
            $conexion->where('general.tablas_comunicado.id_comunicado >',$codigo);
             $conexion->order_by('general.tablas_comunicado.fecha_hora','asc');
        }else{
            $conexion->order_by('general.tablas_comunicado.fecha_hora','desc');
            $conexion->limit(10,0);
        }
         $query = $conexion->get();
        return $query->result_array();
    }
    
    
}
?>
