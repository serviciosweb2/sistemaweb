<?php

/**
 * Model_salones
 * 
 * Description...
 * 
 * @package model_salones
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_salones extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getSalones() {

        $conexion = $this->load->database($this->codigo_filial, true);
        $condicion = array(
            'estado' => 0
        );
        return Vsalones::listarSalones($conexion, $condicion);
    }

    public function guardar($arrGuardar) {

        $conexion = $this->load->database($this->codigo_filial, true);
        $salones = new Vsalones($conexion, $arrGuardar["codigo"]);
        $salones->setSalones($arrGuardar);
        $respuesta = $salones->guardarSalones();
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    public function getTiposSalones() {
        $resp = array();
        $resp[0]['codigo'] = "COCINA";
        $resp[0]['nombre'] = "COCINA";
        $resp[1]['codigo'] = "AULA";
        $resp[1]['nombre'] = "AULA";
        return $resp;
    }

    public function getSalon($cod_salon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $salon = new Vsalones($conexion, $cod_salon);
        return $salon;
    }

    public function cambiarEstado($cod_salon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $salon = new Vsalones($conexion, $cod_salon);
        $salon->estado = $salon->estado == 1 ? 0 : 1;
        $estado = $salon->guardarSalones();
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    public function getHorarios($cod_salon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $salon = new Vsalones($conexion, $cod_salon);
        return $salon->getHorarios();
    }

    public function getSalonesHorarios() {

        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $salones = Vsalones::getSalonesFrmHorario($conexion);
        foreach($salones as $key=>$salon){
            $salones[$key]['salon'] = inicialesMayusculas($salon['salon']);
        }
        return $salones;
    }
    
    public function getSalonesDesactivados() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condicion = array(
            'estado' => 1
        );
        return Vsalones::listarSalones($conexion, $condicion);
    }
    
    public function getColorNuevoSalon($tipo_salon,$arrColores){
        $conexion = $this->load->database($this->codigo_filial,true);
        $cantidadPorTipo = Vsalones::cantidadSalonesPorTipo($conexion, $tipo_salon);
        
        return $arrColores[$cantidadPorTipo[0]['cantidad_salones_tipo']];
       
    }

}

/* End of file model_salones.php */
/* Location: ./application/models/model_salones.php */