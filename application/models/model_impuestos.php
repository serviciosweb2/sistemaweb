<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_impuestos extends CI_Model {

    var $cod_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->cod_filial = $arg["codigo_filial"];
    }

    public function getImpuestos($formato = false) {
        $conexion = $this->load->database($this->cod_filial, true);
        $condiciones = array(
            'baja'=>0,
        );
        $impuestos = Vimpuestos::listarImpuestos($conexion,$condiciones);
        foreach($impuestos as $key=>$valor){
            switch ($valor['tipo']) {
                case 'compras':
                    $impuestos[$key]['nombre'] = $valor['nombre'].' '.'('.lang('compras').')';
                    break;

                case 'ventas':
                    $impuestos[$key]['nombre'] = $valor['nombre'].' '.'('.lang('ventas').')';
                    break;
           }
             if($formato){
                    $impuestos[$key]['valor'] = '%'.' '.$valor['valor'];
                }
        }
        
        return $impuestos;
    }

    public function getDetallesImpuestos($cod_impuesto) {
        $conexion = $this->load->database($this->cod_filial, true);
        $this->load->helper('filial');
        $objImpuesto = new Vimpuestos($conexion, $cod_impuesto);
        $detallesImpuestos = $objImpuesto->getDetallesImpuestos();
        
        foreach ($detallesImpuestos as $key=> $detalle) {
            $detallesImpuestos[$key]['valorFormateado'] = formatearPorcentajeIVA($detalle['valor']);
            $detallesImpuestos[$key]['nom_concepto'] = lang($detalle['nom_concepto']);
        }
        return $detallesImpuestos;
    }

    public function guardarImpuesto($data_post) {
        $conexion = $this->load->database($this->cod_filial, true);
        $conexion->trans_begin();
            $guardarImpuesto = array(
                'nombre' => $data_post['nombre_impuesto'],
                'valor'=>$data_post['valor_impuesto'],
                'tipo'=>$data_post['tipo_impuesto'],
                'cod_impuesto'=>$data_post['cod_impuesto_impuesto'],
                'baja'=>$data_post['estado_impuesto'] == 'on' ? 0 : 1
            );
        $objImpuesto = new Vimpuestos($conexion,$data_post['cod_impuesto']);
        $objImpuesto->setImpuestos($guardarImpuesto);
        $objImpuesto->guardarImpuestos();
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function objImpuesto($cod_impuesto) {
        $conexion = $this->load->database($this->cod_filial, true);
        $objImpuesto = new Vimpuestos($conexion, $cod_impuesto);
        return $objImpuesto;
    }

//    public function guardarConceptoImpuesto($cod_impuesto, $conceptos, $separador,$nombreImpuesto,$valorImpuesto) {
//        $conexion = $this->load->database($this->cod_filial, true);
//        $conexion->trans_begin();
//        $objImpuesto = new Vimpuestos($conexion, $cod_impuesto);
//        $objImpuesto->nombre = $nombreImpuesto;
//        $objImpuesto->valor = $valorImpuesto;
//        $objImpuesto->guardarImpuestos();
//        
//        foreach ($conceptos as $concepto) {
//            $estado = isset($concepto['activo']) ? 0 : 1;
//            if($concepto['nuevo_concepto_impuesto'] == -1){
//                $objImpuesto->setearConceptoImpuesto($concepto['cod_concepto'],$estado);
//            }
//           if(!isset($concepto['activo'])){
//               $objImpuesto->updateConceptoImpuesto($concepto['cod_concepto'],$estado);
//           }
//        }
//        $estadotran = $conexion->trans_status();
//        if ($estadotran === FALSE) {
//            $conexion->trans_rollback();
//        } else {
//            $conexion->trans_commit();
//        }
//
//        return class_general::_generarRespuestaModelo($conexion, $estadotran);
//    }
    

}

?>
