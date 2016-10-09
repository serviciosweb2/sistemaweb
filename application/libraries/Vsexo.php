<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vsexo
 *
 * @author ivan
 */
class Vsexo {
       private static $array =array(
                 array('id'=>'Masculino','nombre'=>'Masculino'),
                 array('id'=>'Femenino' ,'nombre'=>'Femenino')
             );

    public static function getArray($index = false) {
        return $index !== false ? self::$array[$index] : self::$array;
    }
}
