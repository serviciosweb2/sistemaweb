<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_conceptos extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        // print_r($arg);
        parent::__construct();
        //   $this->codigo = $arg["codigo"];
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getConceptos($sololectura = null, $arrConceptos = null) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $Conceptos = new Vconceptos($conexion);
        $arrconceptos = $Conceptos->getConceptos($sololectura, $arrConceptos);
       
        $arrResp = array();
        foreach ($arrconceptos as $concepto) {

            $arrResp[$concepto['codigo']] = lang($concepto['key']) != '' ? lang($concepto['key']) : $concepto['key'];
        }
        return $arrResp;
    }

    public function getAllConceptos($cod_impuesto) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conceptos = Vconceptos::getConceptosImpuesto($conexion, $cod_impuesto);
        foreach ($conceptos as $key => $concepto) {
            $conceptos[$key]['conceptoTraducido'] = lang($concepto['key']);
        }
        return $conceptos;
    }

    public function getConceptosUsuario() {
        $conexion = $this->load->database($this->codigo_filial, true);

        $condiciones = array('key' => 'USUARIO_CREADOR');
        $arrconcusuario = Vconceptos::listarConceptos($conexion, $condiciones);
        
        $wherein = array();
        foreach ($arrconcusuario as $row) {
            $wherein[] = $row['codigo_padre'];
        }
        
        $Conceptos = new Vconceptos($conexion);
        $arrconceptos = $Conceptos->getConceptos(null, $wherein);
        
        $arrResp = array();
        foreach ($arrconceptos as $concepto) {
            $nombre = lang($concepto['key']) != '' ? lang($concepto['key']) : $concepto['key'];
            $arrResp[] = array('codigo' => $concepto['codigo'], 'nombre' => $nombre);
        }
        return $arrResp;
    }

    public function guardarConcepto($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $objConcepto = new Vconceptos($conexion, $datos['cod_concepto']);
        $objConcepto->guardar($datos['nombre'], null, '0');

        if ($datos['cod_concepto'] != '') {//si es nuevo
            $objPropiedad = new Vconceptos($conexion);
            $objPropiedad->guardar('USUARIO_CREADOR', $datos['cod_usuario'], $objConcepto->getCodigo());
        }
        
        $objConcepto->dessetearConceptoImpuesto();
        if (isset($datos['impuestosAsignar']) && is_array($datos['impuestosAsignar'])){
            foreach($datos['impuestosAsignar'] as $impuesto){
                $objConcepto->setearConceptoImpuesto($impuesto);
            }
        }
        
        
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getConcepto($cod_concepto) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objConcepto = new Vconceptos($conexion, $cod_concepto);
        return $objConcepto;
    }
    
    public function getImpuestosConceptos($cod_concepto){
        $conexion = $this->load->database($this->codigo_filial,true,NULL,true);
        
        $myConcepto = new Vconceptos($conexion, $cod_concepto);
        $arrImpuestosConcepto = $myConcepto->getImpuestosConceptos();
       return $arrImpuestosConcepto;
    }

}
