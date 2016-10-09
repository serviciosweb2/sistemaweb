<?php

/**
 * Class Vplanes_financiacion
 *
 * Class  Vplanes_financiacion maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vplanes_financiacion extends Tplanes_financiacion {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo_plan = null, $codigo_concepto = null, $codigo_financiacion = null, $nro_cuota = null) {
        parent::__construct($conexion, $codigo_plan, $codigo_concepto, $codigo_financiacion, $nro_cuota);
    }

//    function getCuotasPlanRelacionadas() {
//        $condiciones = array(
//            'codigoplan' => $this->codigoplan,
//            'cuota' => $this->cuota//financiacion
//        );
//        return Vplanes_cuotas::listarPlanes_cuotas($this->oConnection, $condiciones);
//    }
    
    public function guardar($valorcuota, $orden) {
        $this->valor = $valorcuota;
        $this->orden = $orden;

        $this->guardarPlanes_financiacion();
    }

    public function cambiarEstado($estado) {
        
    }

   

}

