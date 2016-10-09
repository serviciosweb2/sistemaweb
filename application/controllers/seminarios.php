<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Seminarios extends CI_Controller {
    
    private $seccion;
    
    public function __construct(){
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $this->load->model("Model_seminarios", "", false, array());
    }
    
    public function index(){
        $claves = array('codigo');
        $data['lang'] = getLang($claves);
        $data['page'] = 'seminarios/vista_seminarios';
        $data['seccion'] = $this->seccion;
        $conexion = $this->load->database("seminarios", true);
        $filial = $this->session->userdata('filial');
        $data['horarios'] = Vseminarios::listar($conexion, date("Y-m-d"), null, $filial['codigo']);
        $this->load->view('container', $data);        
    }
    
    private function crearColumnas(){
        $columnas = array(
            array("nombre" => "horario", "campo" => 'horario'),
            array("nombre" => "cupo", "campo" => 'cupo'),
            array("nombre" => "nombre", "campo" => 'nombre'),
            array("nombre" => "telefono", "campo" => 'telefono'),
            array("nombre" => "documento", "campo" => 'documento'),
            array("nombre" => "email", "campo" => 'email'),
            array("nombre" => "fecha inscripcion", "campo" => 'fecha_inscripto')
        );
        return $columnas;
    }
    
    public function listarSeminariosDataTable(){
        $filial = $this->session->userdata('filial');
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $idSeminario = $_POST['horario'] == -1 ? null : $_POST['horario'];
        $arrInscriptos = $this->Model_seminarios->listarSeminariosDatatable($arrFiltros, date("Y-m-d"), null, $filial['codigo'], $idSeminario);
        //$arrFiltros, $fechaDesde = null, $fechaHasta = null, $idFilial = null, $idSeminario = null
        echo json_encode($arrInscriptos);        
    }
    
    public function guardar_seminario(){
        $arrResp = array();
        $conexion = $this->load->database("general", true);
        $mySeminario = new Vseminarios($conexion);
        $mySeminario->cupo = $_POST['cupo'];
        $mySeminario->fecha = $_POST['fecha'];
        $mySeminario->id_filial = $_POST['id_filial'];
        if ($mySeminario->guardarForzado($_POST['id'])){
            $arrResp['success'] = "success";
            $arrResp['id'] = $mySeminario->getCodigo();
        } else {
            $arrResp['error'] = "error";
            $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
    
    public function guardar_inscripto(){
        $arrResp = array();
        $conexion = $this->load->database("general", true);
        $myInscripto = new Vinscriptos($conexion);
        $myInscripto->apellido = $_POST['apellido'];
        $myInscripto->documento = $_POST['documento'];
        $myInscripto->email = $_POST['email'];
        $myInscripto->fecha_nacimiento = $_POST['fecha_nacimiento'];
        $myInscripto->nombre = $_POST['nombre'];
        $myInscripto->referencia = $_POST['referencia'];
        $myInscripto->telefono = $_POST['telefono'];
        if ($myInscripto->guardarForzado($_POST['id'])){
            $arrResp['success'] = "success";
            $arrResp['id'] = 'id';
        } else {
            $arrResp['error'] = 'error';
            $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }        
        echo json_encode($arrResp);
    }
    
    public function guardar_inscripcion(){
        $arrResp = array();
        $conexion = $this->load->database("general", true);
        $mySeminario = new Vseminarios($conexion, $_POST['id_seminario']);
        if ($mySeminario->registrar_inscripcion($_POST['id_inscripto'], $_POST['fecha_inscripcion'])){
            $arrResp['success'] = "success";
            $arrResp['id_seminario'] = $mySeminario->getCodigo();
            $arrResp['id_inscripto'] = $_POST['id_inscripto'];
        } else {
            $arrResp['error'] = 'error';
            $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
}