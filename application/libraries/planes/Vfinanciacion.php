<?php

/**
 * Class Vfinanciacion
 *
 * Class  Vfinanciacion maneja todos los aspectos de las financiaciones
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vfinanciacion extends Tfinanciacion {

    var $estadoeliminada = 'eliminada';         // esto se debe cambiar (es cualquiera)
    var $estadohabilitada = 'habilitada';       // esto se debe cambiar
    var $estadoinhabilitada = 'inhabilitada';   // esto se debe cambiar
    
    static private $estado_eliminada = "eliminada";
    static private $estado_habilitada = "habilitada";
    static private $estado_inhabilitada = "inhabilitada";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardar($nombre, $interes, $estado, $numeroCuotas) {
        $this->nombre = $nombre;
        $this->interes = $interes;
        $this->estado = $estado;
        $this->numero_cuotas = $numeroCuotas;
        
        $this->guardarFinanciacion();
    }

    public function eliminar() {
        $this->estado = $this->estadoeliminada;
        $this->guardarFinanciacion();
    }

    static function getEstadoEliminada(){
        return self::$estado_eliminada;
    }
    
    static function getEstadoHabilitada(){
        return self::$estado_habilitada;
    }
    
    static function getEstadoInhabilitada(){
        return self::$estado_inhabilitada;
    }
    
    
}


