<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vtipos_telefonos
 *
 * @author ivan
 */
class Vtipos_telefonos {
    private static $array =array(
                 array('codigo'=>'fijo','nombre'=>'fijo'),
                 array('codigo'=>'celular','nombre'=>'celular')
             );

    public static function getArray($index = false) {
        return $index !== false ? self::$array[$index] : self::$array;
    }
}
