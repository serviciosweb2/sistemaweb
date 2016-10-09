<?php

/**
* Class Vvan_cielo_header
*
*Class  Vvan_cielo_header maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vvan_cielo_header extends Tvan_cielo_header{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */
    
    static private function _get_tipo_registro($string){
        return substr($string, 1, 1);
    }

    static private function _get_establecimiento_matriz($string){
        return (integer) (substr($string, 2, 10));
    }

    static private function _get_fecha_procesamiento($string){
        $temp = substr($string, 12, 8);
        return substr($temp, 0, 4)."-".substr($temp, 4, 2)."-".substr($temp, 6, 2);
    }

    static private function _get_periodo_inicial($string){
        $temp = substr($string, 20, 8);
        return substr($temp, 0, 4)."-".substr($temp, 4, 2)."-".substr($temp, 6, 2);
    }

    static private function _get_periodo_final($string){
        $temp = substr($string, 28, 8);
        return substr($temp, 0, 4)."-".substr($temp, 4, 2)."-".substr($temp, 6, 2);
    }

    static private function _get_secuencia($string){
        return (integer) (substr($string, 36, 7));
    }

    static private function _get_empresa($string){
        return trim(substr($string, 43, 5));
    }

    static private function _get_opcion_extracto($string){
        return (integer) (substr($string, 48, 2));
    }

    static private function _get_van($string){
        return trim(substr($string, 50, 1));
    }

    static private function _get_caja_postal($string){
        return trim(substr($string, 51, 20));
    }

    static private function _get_version_layout($string){
        return trim(substr($string, 71, 3));
    }

    static private function _get_uso_cielo($string){
        return trim(substr($string, 74, 177));
    }

    
    /* PUBLIC FUNCTIONS */
    
    public function loadFromString($string){
        $string = " ".trim($string); // (pos 0 de PHP = pos 1 en Manual cielo)
        $this->tipo_registro = self::_get_tipo_registro($string);
        $this->establecimiento_matriz = self::_get_establecimiento_matriz($string);
        $this->fecha_procesamiento = self::_get_fecha_procesamiento($string);
        $this->periodo_inicial = self::_get_periodo_inicial($string);
        $this->periodo_final = self::_get_periodo_final($string);
        $this->secuencia = self::_get_secuencia($string);
        $this->empresa = self::_get_empresa($string);
        $this->opcion_extracto = self::_get_opcion_extracto($string);
        $this->van = self::_get_van($string);
        $this->caja_postal = self::_get_caja_postal($string);
        $this->version_layout = self::_get_version_layout($string);
        $this->uso_cielo = self::_get_uso_cielo($string);
        return $this->validar();
    }
    
    public function validar(){
        $arrFechaProcesamiento = explode("-", $this->fecha_procesamiento);
        $arrFechaFinal = explode("-", $this->periodo_final);
        $arrFechaInicial = explode("-", $this->periodo_inicial);
        $valida = $this->version_layout == "001";
        $valida = $valida && $this->tipo_registro == 0;
        // valida que las fechas sean validas y que el año de cada una de ellas sea el año anterior, el año corriente o el año proximo
        $valida = $valida && count($arrFechaFinal) == 3 && checkdate($arrFechaFinal[1], $arrFechaFinal[2], $arrFechaFinal[0]);
        $valida = $valida && ($arrFechaFinal[0] == date("Y") - 1 || $arrFechaFinal[0] == date("Y") || $arrFechaFinal[0] == date("Y") + 1);
        $valida = $valida && count($arrFechaInicial) == 3 && checkdate($arrFechaInicial[1], $arrFechaInicial[2], $arrFechaInicial[0]);
        $valida = $valida && ($arrFechaInicial[0] == date("Y") - 1 || $arrFechaInicial[0] == date("Y") || $arrFechaInicial[0] == date("Y") + 1);
        $valida = $valida && count($arrFechaProcesamiento) == 3 && checkdate($arrFechaProcesamiento[1], $arrFechaProcesamiento[2], $arrFechaProcesamiento[0]);
        $valida = $valida && ($arrFechaProcesamiento[0] == date("Y") - 1 || $arrFechaInicial[0] == date("Y") || date($arrFechaProcesamiento[0] == date("Y") + 1));
        return $valida;
    }
    
    /* STATIC FUNCTIONS */
    
}

?>