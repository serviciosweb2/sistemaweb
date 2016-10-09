<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vmedios_cheque
 *
 * @author Vane
 */
class Vmedio_cheques extends Tmedio_cheques {

    private static $array = array(
        array('codigo' =>'orden', 'nombre' =>'de orden'),
        array('codigo' => 'diferido', 'nombre' =>'diferido')
        
    );

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public static function getTipos($codigo = false) {
        $devolver = '';
        if ($codigo != false) {
            $array = self::$array;
            foreach ($array as $value) {
                
                foreach($codigo as $tipoCheque){
                if ($value['codigo'] == $tipoCheque) {
                    
                $devolver[]=array(
                    'codigo'=>$value['codigo'], 
                    'nombre'=>lang($value['codigo'])
                    );    
                }
                }
            }
        } else {
            
            $tiposCheques = self::$array;
            foreach($tiposCheques as$key=>$chequeTipo){
                $tiposCheques[$key] = array('id'=>$chequeTipo['codigo'],'nombre'=>lang($chequeTipo['codigo']));
            }
            return $tiposCheques;
        }
        //print_r($devolver);
        return $devolver;
    }

}
