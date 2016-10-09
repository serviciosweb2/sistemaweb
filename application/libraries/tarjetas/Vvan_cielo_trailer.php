<?php

/**
* Class Vvan_cielo_trailer
*
*Class  Vvan_cielo_trailer maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vvan_cielo_trailer extends Tvan_cielo_trailer{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */
    
    static private function _get_tipo_registro($string){
        return substr($string, 1, 1);
    }

    static private function _get_total_registros($string){
        return (integer) (substr($string, 2, 11));
    }

    static private function _get_uso_cielo($string){
        return trim(substr($string, 13, 238));
    }

    
    /* PUBLIC FUNCTIONS */
    
    public function loadFromString($string){
        $string = " ".trim($string);
        $this->tipo_registro = self::_get_tipo_registro($string);
        $this->total_registros = self::_get_total_registros($string);
        $this->uso_cielo = self::_get_uso_cielo($string);
        return $this->validar();
    }
    
    /**
     * Valida si el objeto trailer se creo correctamente
     * 
     * @param int $cantidadRegistros    La cantidad de registros procesados del archivo (sin header ni trailer)
     * @return boolean
     */
    public function validar(){
        $valida = $this->tipo_registro == 9;
        return $valida;
    }
    
    /* STATIC FUNCTIONS */
    
}

?>