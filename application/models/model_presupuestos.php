<?php

/**
 * Model_presupuestos
 * 
 * Description...
 * 
 * @package model_presupuestos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_presupuestos extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function guardarPresupuesto($arrPresupuesto) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
    
        $presupuestos = new Vpresupuestos($conexion);
        $guardarPresupuesto= array(
            'codcomision'=>$arrPresupuesto['presupuesto']['codcomision'],
            'fecha'=>$arrPresupuesto['presupuesto']['fecha'],
            'observaciones'=>$arrPresupuesto['presupuesto']['observaciones'],
            'cod_plan'=>$arrPresupuesto['presupuesto']['cod_plan'],
            'fecha_vigencia'=>$arrPresupuesto['presupuesto']['fechavigencia']
        );
        $presupuestos->setPresupuestos($guardarPresupuesto);
        $presupuestos->guardarPresupuestos();
        //SETEO PRESUPUESTO A ASPIRANTE
        $presupuestoAspirante = array(
            'cod_aspirante'=>$arrPresupuesto['cod_aspirante'],
            'cod_presupuesto'=>$presupuestos->getCodigo()
        );
        $presupuestos->setPresupuestoAspirante($presupuestoAspirante);
                
                
        
        //GUARDO DETALLES
        $cod_presupuesto = $presupuestos->getCodigo();
        foreach($arrPresupuesto['detalle'] as $detallePresupuesto){
            $guardarDetalle=array(
                'codigo_financiacion'=>$detallePresupuesto['financiacion'],
                'codigo_concepto'=>$detallePresupuesto['concepto'],
                'codigo_presupuesto'=>$cod_presupuesto
                );
            $presupuestos->guardarDetalles($guardarDetalle);
            
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {

            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $presupuestos->getCodigo());
    }
    
    public function getDetallePresupuestoPlan($codigos){
        $conexion = $this->load->database($this->codigo_filial, true);
        $detalles = Vpresupuestos::getDetallePresupuestoPlan($conexion, $codigos['codigo_concepto'], $codigos['codigo_financiacion'], $codigos['codigo_plan']);
        echo json_encode($detalles);
    }

}

/* End of file model_presupuestos.php */
/* Location: ./application/models/model_presupuestos.php */