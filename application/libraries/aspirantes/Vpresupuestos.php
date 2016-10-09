<?php

/**
 * Class Vpresupuestos
 *
 * Class  Vpresupuestos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vpresupuestos extends Tpresupuestos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardarDetalles($guardarDetalle) {        
        $this->oConnection->insert('presupuestos_detalle', $guardarDetalle);
    }
    
    public function setPresupuestoAspirante($presupuestoAspirante){
        $this->oConnection->insert('aspirantes_presupuestos',$presupuestoAspirante);
    }

    /**
     * Retorna un objeto aspirante del aspirante que pidio el presupuesto
     * 
     * @return \Vaspirantes|null
     */
    public function getAspirante(){
        $this->oConnection->select("cod_aspirante");
        $this->oConnection->from("aspirantes_presupuestos");
        $this->oConnection->where("cod_presupuesto", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        if (isset($arrTemp[0]['cod_aspirante']) && $arrTemp[0]['cod_aspirante'] > 0){
            $this->oConnection->resetear();
            $myAspirante = new Vaspirantes($this->oConnection, $arrTemp[0]['cod_aspirante']);
            return $myAspirante;
        } else {
            return null;
        }
    }
    
    /**
     * retorna los registros de la tabla presupuestos_detalle para el presupuesto dado
     * 
     * @return array
     */
    public function getPresupuestoDetalles(){
        $this->oConnection->select("*");
        $this->oConnection->from("presupuestos_detalle");
        $this->oConnection->where("codigo_presupuesto", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    static function getDetallePresupuestoPlan(CI_DB_mysqli_driver $conexion, $cod_concepto,$cod_financiacion,$cod_plan){
        $conexion->select('*');
        $conexion->from('planes_financiacion');
        $conexion->where('codigo_concepto',$cod_concepto);
        $conexion->where('codigo_financiacion',$cod_financiacion);
        $conexion->where('codigo_plan',$cod_plan);
        $query = $conexion->get();
       
        return $query->result_array();
    }
}

