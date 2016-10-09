<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Materias extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        session_method();
        // parametros
    }
    
   
    /* La siguiente function esta siendo accedida por un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function getMaterias(){
        $conexion = $this->load->database("default", true);
        $condiciones = array();
        if ($this->input->post("tipo_materia") && $this->input->post("tipo_materia") <> '') $condiciones["cod_tipo_materia"] = $this->input->post("tipo_materia");
        if ($this->input->post("codigo") && $this->input->post("codigo") <> '') $condiciones['codigo'] = $this->input->post("codigo");
        $arrResp = Vmaterias::listarMaterias($conexion, $condiciones, null, array(array("campo" => "nombre_es", "orden" => "ASC")));
        echo json_encode($arrResp);
    }
    
    /* La siguiente function esta siendo accedida por un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function guardar_materia(){
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myMateria = new Vmaterias($conexion, $this->input->post("codigo"));
        $myMateria->cod_tipo_materia = $this->input->post('cod_tipo_materia');
        $myMateria->descripcion_es = $this->input->post("descripcion_es");
        $myMateria->descripcion_in = $this->input->post("descripcion_in");
        $myMateria->descripcion_pt = $this->input->post("descripcion_pt");
        $myMateria->nombre_es = $this->input->post("nombre_es");
        $myMateria->nombre_in = $this->input->post("nombre_in");
        $myMateria->nombre_pt = $this->input->post("nombre_pt");
        if ($myMateria->guardarMaterias()){
            $arrResp['success'] = "success";
            $arrResp['codigo'] = $myMateria->getCodigo();
        } else {
            $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
}