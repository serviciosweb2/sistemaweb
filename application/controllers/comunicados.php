<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comunicados extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        session_method();
    }
    
    public function index(){
        
    }
    
    public function vista_comunicado(){
        $conexion = $this->load->database("general", true);
        $myComunicado = new Vcomunicados($conexion, $this->input->post("id"));
        $imagenes = $myComunicado->getImagenes();
        $data['myComunicado'] = $myComunicado;
        $data['imagenes'] = $imagenes;
        $this->load->view("comunicados/vista_comunicado", $data);
    }
    
    public function get_comunicado(){
        if ($this->input->post("id")){
            $temp = array();
            $arrResp = array();
            $filiales = array();
            $imagenes = array();
            $conexion = $this->load->database("general", true);
            $myComunicado = new Vcomunicados($conexion, $this->input->post("id")); 
            $temp['id'] = $myComunicado->getCodigo();
            foreach ($myComunicado as $key => $value){
                $temp[$key] = $value;
            }
            $arrFiliales = $myComunicado->getFiliales();
            foreach ($arrFiliales as $filial){
                $filiales[] = $filial['id_filial'];
            }
            $temp['filiales'] = $filiales;
            $arrImagenes = $myComunicado->getImagenes();
            foreach ($arrImagenes as $imagen){
                $imagenes[] = $imagen['url'];
            }
            $temp['imagenes'] = $imagenes;            
            $arrResp['transport']['comunicado']['aaData'] = $temp;
            $arrResp['transport']['comunicado']['iTotalRecords'] = 1;
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400);
        }
    }    
    
    public function guardar_comunicado(){
        if ($this->input->post("id") && $this->input->post("titulo") && $this->input->post("mensaje") &&
                $this->input->post("filiales") && is_array($this->input->post("filiales")) && $this->input->post("usuario")
                && $this->input->post("estado")){
            $arrResp = array();
            $id = $this->input->post("id");
            $titulo = $this->input->post("titulo");
            $mensaje = $this->input->post("mensaje");
            $usuario = $this->input->post("usuario");
            $fecha = $this->input->post("fecha_registro") ? $this->input->post("fecha_registro") : date("Y-m-d H:i:s");
            $filiales = $this->input->post("filiales");
            $estado = $this->input->post("estado");
            $conexion = $this->load->database("general", true);
            $conexion->trans_begin();
            $myComunicado = new Vcomunicados($conexion, $id);
            $myComunicado->mensaje = $mensaje;
            $myComunicado->titulo = $titulo;
            $myComunicado->estado = $estado;
            if ($myComunicado->getCodigo() == -1){
                $myComunicado->usuario = $usuario;
                $myComunicado->fecha_creacion = $fecha;
            }
            $myComunicado->guardarComunicados();
            $myComunicado->setFiliales($filiales);
            if ($this->input->post('imagenes') && is_array($this->input->post("imagenes"))){
                $myComunicado->setImagenes($this->input->post("imagenes"));
            } else {
                $myComunicado->clear_imagenes();
            }
            if ($conexion->trans_status()){
                $conexion->trans_commit();
                $arrResp['success'] = "success";
                $arrResp['id'] = $myComunicado->getCodigo();
            } else {
                $conexion->trans_rollback();
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400);
        }
    }
    
    public function listar_comunicados(){
        $arrResp = array();
        $conexion = $this->load->database("default", true);
        $searchLike = array();
        $order = array();
        $iDisplayStart = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $iDisplayLength = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $sSearch = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $SortCol = isset($_POST['iSortCol_0']) ? $_POST['iSortCol_0'] : null;
        $sSortDir = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] :null;
        if ($SortCol !== null && $sSortDir !== null){
            $order = array($SortCol, $sSortDir);
        }
        $sSearchField = isset($_POST['sSearchField']) && is_array($_POST['sSearchField']) ? $_POST['sSearchField'] : null;
        if ($sSearch != '' && is_array($sSearchField)){
            foreach ($sSearchField as $field){
                $searchLike[$field] = $sSearch;
            }
        }
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
        $cantidad = Vcomunicados::listar($conexion, $searchLike, null, null, null, true, $fechaDesde, $fechaHasta, $estado);
        $arrComunicados = Vcomunicados::listar($conexion, $searchLike, $iDisplayStart, $iDisplayLength, $order, false, $fechaDesde, $fechaHasta, $estado);
        $arrResp['transport']['comunicados']['aaData'] = $arrComunicados;
        $arrResp['transport']['comunicados']['iTotalRecords'] = $cantidad;
        echo json_encode($arrResp);
    }
    
    public function get_alertas_alumno(){
        if ($this->input->post("cod_filial") && $this->input->post("cod_alumno")){
            $codFilial = $this->input->post('cod_filial');
            $codAlumno = $this->input->post('cod_alumno');
            $codComunicado = $this->input->post("codigo_comunicado") ? $this->input->post("codigo_comunicado") : null;
            $filial = $this->session->userdata('filial');
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_comunicados", "", false, $config);
            $arrResp = $this->Model_comunicados->listar_comunicados($codFilial, $codAlumno, $codComunicado);
            echo json_encode($arrResp);
        } else {
            $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
            $code = 400;
            $text = "Bad Request";
            if (substr(php_sapi_name(), 0, 3) == 'cgi'){
                header("Status: {$code} {$text}", TRUE);
            } else if ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
                header($server_protocol." {$code} {$text}", TRUE, $code);
            } else {
                header("HTTP/1.1 {$code} {$text}", TRUE, $code);
            }
        }
    }
}