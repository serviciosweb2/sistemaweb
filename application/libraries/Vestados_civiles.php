<?php

class Vestados_civiles {
    private static $array =array(
                            
                                array('id'=>'Soltero/a', 'nombre'=>'Soltero/a'),
                                array('id'=>'Casado/a', 'nombre'=>'Casado/a'),
                                array('id'=>'Divorciado/a', 'nombre'=>'Divorciado/a'),
                                array('id'=>'Viudo/a' ,'nombre'=>'Viudo/a')
                                
                            
             );

    public static function getArray($index = false) {
        return $index !== false ? self::$array[$index] : self::$array;
    }
}
