<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Voffline_sincronizacion extends Toffline_sincronizacion {

        static function getRegistrosSincronizarTest($conexion,$ultimoid, $limite = 50, $base = null) {

        //$condiciones = array("offline_sincronizacion.id >" => $ultimoid);
        
        $from = 'offline_sincronizacion';
        
        // "$base" hace referencia a la base en donde se encuentra la tabla que contiene los registros deseados
        if($base != null)
        {
           $from = "$base.offline_sincronizacion"; 
        }
            
        //calcula el total de registros y lo guarda en una subquery
        $conexion->select('max(offline_sincronizacion.id)', false);
        $conexion->from($from);
        $subquery1 = $conexion->return_query();
        
        // resetea
        $conexion->resetear();
       
        //trae los registros mayores al id que se le pase
        $conexion->from($from);
        $conexion->limit($limite);
        $conexion->select("offline_sincronizacion.*,($subquery1) as total_registros");
        $conexion->where('offline_sincronizacion.id >',$ultimoid);
        
        $query  = $conexion->get();
        $result = $query->result_array();
        return $result;
        
        //return Voffline_sincronizacion::listarOffline_sincronizacion($conexion, $condiciones);
    }
    
    
    static function getRegistrosSincronizar($conexion,$ultimoid, $limite = 50) {//backUp

        $condiciones = array("offline_sincronizacion.id >" => $ultimoid);

        
        


        $conexion->select('max(offline_sincronizacion.id)', false);
        $conexion->from('offline_sincronizacion');
        $subquery1 = $conexion->return_query();
        $conexion->resetear();


        $conexion->limit($limite);
        $conexion->select("offline_sincronizacion.*,($subquery1) as total_registros");
        return Voffline_sincronizacion::listarOffline_sincronizacion($conexion, $condiciones);
    }
    
    
    static function getRegistrosSincronizarBancos($conexion,$ultimoid, $limite = 50) {

        $condiciones = array("offline_sincronizacion.id >" => $ultimoid);





        $conexion->select('max(offline_sincronizacion.id)', false);
        $conexion->from('offline_sincronizacion');
        $subquery1 = $conexion->return_query();
        $conexion->resetear();


        $conexion->limit($limite);
        $conexion->select("offline_sincronizacion.*,($subquery1) as total_registros");
        return Voffline_sincronizacion::listarOffline_sincronizacion($conexion, $condiciones);
    }
    
    
    
    static function getRegistrosSincronizarTarjetas($conexion,$ultimoid, $limite = 50) {

        $condiciones = array("offline_sincronizacion.id >" => $ultimoid);





        $conexion->select('max(offline_sincronizacion.id)', false);
        $conexion->from('offline_sincronizacion');
        $subquery1 = $conexion->return_query();
        $conexion->resetear();


        $conexion->limit($limite);
        $conexion->select("offline_sincronizacion.*,($subquery1) as total_registros");
        return Voffline_sincronizacion::listarOffline_sincronizacion($conexion, $condiciones);
    }

}
