<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Planes_academicos extends CI_Controller {
    
    public function __construct() {
        parent::__construct();          // agregar parametros
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
    }
    
    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function get_reporte(){
        $conexion = $this->load->database("default", true);
        $estado = $this->input->post("estado") ? $this->input->post("estado") : null;
        $curso = $this->input->post("curso") ? $this->input->post("curso") : null;
        $cantidadPeriodos = $this->input->post("cantidad_periodos") ? $this->input->post("cantidad_periodos") : null;
        $codigo = $this->input->post("codigo") ? $this->input->post("codigo") : null;
        $arrResp = Vplanes_academicos::getReporte($conexion, $curso, $estado, $cantidadPeriodos, $codigo);
        echo json_encode($arrResp);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function habilitar(){
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myPlanacademico = new Vplanes_academicos($conexion, $this->input->post("codigo"));
        if ($myPlanacademico->habilitar()){
            $arrResp['success'] = "success";
            $arrResp['estado'] = $myPlanacademico->estado;
            $arrResp['codigo'] = $myPlanacademico->getCodigo();
        } else {
             $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function inhabilitar(){
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $myPlanAcademico = new Vplanes_academicos($conexion, $this->input->post("codigo"));
        if ($myPlanAcademico->inhabilitar()){
            $arrResp['success'] ="success";
            $arrResp['estado'] = $myPlanAcademico->estado;
            $arrResp['codigo'] = $myPlanAcademico->getCodigo();
        } else {
             $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getPeriodos(){
        $this->load->model("Model_planes_academicos", "", false, array("codigo" => $this->input->post("codigo"), "codigo_filial" => "default"));
        $arrPeriodos = $this->Model_planes_academicos->getPeriodos();
        echo json_encode($arrPeriodos);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getTiposPeriodos(){
        $this->load->model("Model_planes_academicos", "", false, array("codigo" => $this->input->post("codigo"), "codigo_filial" => "default"));
        $arrPeriodos = $this->Model_planes_academicos->getTiposPeriodos();
        echo json_encode($arrPeriodos);
    }
 
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getTitulos(){
        $conexion = $this->load->database("default", true);
        $arrResp = Vtitulos::listarTitulos($conexion);
        echo json_encode($arrResp);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getMaterias(){
        $conexion = $this->load->database("default", true);
        $myPlanAcademico = new Vplanes_academicos($conexion, $this->input->post("codigo"));
        $codPeriodo = $this->input->post('codigo_tipo_periodo') ? $this->input->post('codigo_tipo_periodo') : null;
        $arrMaterias = $myPlanAcademico->getMaterias($codPeriodo);
        echo json_encode($arrMaterias);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getModalidades(){
        $conexion = $this->load->database("default", true);
        $enum = Vplanes_academicos::getModalidades($conexion);
        echo json_encode($enum);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getNombresPeriodos(){
        $conexion = $this->load->database("default", true);
        $enum = Vplanes_academicos::getNombresPeriodos($conexion);
        echo json_encode($enum);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getPlanAcademicoFiliales(){
        $this->load->model("Model_planes_academicos", "", false, array("codigo" => $this->input->post("codigo"), "codigo_filial" => "default"));
        $codFilial = $this->input->post("codigo_filial") ? $this->input->post("codigo_filial") : null;
        $codTipoPeriodo = $this->input->post("codigo_tipo_periodo") ? $this->input->post("codigo_tipo_periodo") : null;
        $modalidad = $this->input->post("modalidad") ? $this->input->post("modalidad") : null;
        $estado = $this->input->post("estado") ? $this->input->post("estado") : null;
        $arrResp = $this->Model_planes_academicos->getPlanAcademicoFiliales($codFilial, $codTipoPeriodo, $modalidad, $estado);
        echo json_encode($arrResp);
    }
    
    /* La siguiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function guardarPlanesAcademicosFiliales(){
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();
        $myPlanAcademico = new Vplanes_academicos($conexion, $this->input->post('codigo'));
        $myPlanAcademico->cod_curso = $this->input->post("cod_curso");
        $myPlanAcademico->estado = $this->input->post("estado");
        $myPlanAcademico->nombre = $this->input->post("nombre");
        $myPlanAcademico->guardarPlanes_academicos();
        if ($this->input->post("periodos") && is_array($this->input->post("periodos"))){
            $myPlanAcademico->setPeriodos($this->input->post("periodos"));
        }
        if ($this->input->post('periodos_modalidades_filiales') && is_array($this->input->post('periodos_modalidades_filiales'))){
            $myPlanAcademico->setFiliales($this->input->post('periodos_modalidades_filiales'));
        }
        $arrResp = array();
        if ($conexion->trans_status()){
            $conexion->trans_commit(); 
            $arrResp['success'] = "success";
            $arrResp['codigo'] = $myPlanAcademico->getCodigo();
        } else {
            $conexion->trans_rollback();
            $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }        
        echo json_encode($arrResp);
    }
    
    /* La siguiente function esta siendo accedida desde un web services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function get_titulos(){
        $conexion = $this->load->database("general", true);
        $condiciones = array();
        if ($this->input->post("codigo")){
            $condiciones['codigo'] = $this->input->post("codigo");
        }
        $arrTitulos = Vtitulos::listarTitulos($conexion, $condiciones);
        $arrResp = array();
        $arrResp['transport']['aaData'] = $arrTitulos;
        $arrResp['transport']['iTotalRecords'] = count($arrTitulos);
        echo json_encode($arrResp);
    }
    
    public function guardar_titulo(){
        $arrResp = array();
        if ($this->input->post("codigo") && $this->input->post("nombre")){
            $conexion = $this->load->database("general", true);
            $myTitulo = new Vtitulos($conexion, $this->input->post("codigo"));
            $myTitulo->nombre = $this->input->post("nombre");
            if ($myTitulo->guardarTitulos()){
                $arrResp['success'] = "success";
                $arrResp['codigo'] = $myTitulo->getCodigo();
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        } else {
            $arrResp['error'] = "Error de parametros";
        }
        echo json_encode($arrResp);
    }
    
    public function get_materias_planes_academicos(){
        $conexion = $this->load->database("default", true);
        $codPlan = $this->input->post("cod_plan_academico") ? $this->input->post("cod_plan_academico") : null;
        $materias = Vplanes_academicos::listar_materias($conexion, $codPlan);
        echo json_encode($materias);
    }
    
    public function listar_planes_academicos(){
        $conexion = $this->load->database("general", true);
        $arrPlanesAcademicos = Vplanes_academicos::listar($conexion);
        $arrResp = array();
        $arrResp['transport']['aaData'] = $arrPlanesAcademicos;
        $arrResp['transport']['iTotalRecords'] = count($arrPlanesAcademicos);
        echo json_encode($arrResp);
    }
    
}