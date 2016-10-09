<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Alertas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        session_method();
    }
    public function index()
    {
        $this->notificaciones(null);
    }
    function notificaciones($tipoAlerta = null) {
        $validar_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_alertas", "", false, $arrConf);
        $data['titulo_pagina'] = '';
        $data['page'] = 'alertas/listado_notificaciones';
        $data['seccion'] = array( 'titulo' =>'notificaciones', 
    'categoria' => 'alertas','control'=>'alertas');//$validar_session;
        $data['tipo_alerta'] = $tipoAlerta;
        $this->load->view('container', $data);
    }

    function resumen_alertas_usuario() {
        $this->load->helper("alumnos");
        $arrResp = array();
        $valorAnterior = $this->session->userdata('alerta_timestamp') ? $this->session->userdata('alerta_timestamp') : 0;
        $valorActual = time();        
        if ($valorActual - $valorAnterior >= 30){            // cambiar este valor por el valor correspondiente (30 segundos)
            $filial = $this->session->userdata('filial');
            $codFilial = $filial["codigo"];
            $arrConf = array('codigo_filial' => $codFilial);
            $this->load->model("Model_alertas", "", false, $arrConf);
            $this->load->model("Model_asistencias", "", false, $arrConf);
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_facturas", "", false, $config);
            $this->lang->load(get_idioma(), get_idioma());
            $arrResp['alertas_usuarios']['generales'] = $this->Model_alertas->resumen_alertas_usuario();
            $arrResp['alertas_usuarios']['alertas_envios_fallidos'] = $this->Model_alertas->listarAlertasNoEnviadas(true);
            $arrResp['cantidad_facturas_errores'] = 0; // $this->Model_facturas->getFacturas(null, true, array("facturas.estado" => Vfacturas::getEstadoError()));
            if ($this->input->post("recuperar_consultas_web") == 1) {
                $this->load->model("Model_consultasweb", "", false, $arrConf);
                $arrFiltros = array("iDisplayLength" => 5, "iDisplayStart" => 0);
                $arrConsutasWeb = $this->Model_consultasweb->listarMailsConsultas($codFilial, "inbox", $arrFiltros, array("mails_consultas.mails_consultas.notificar" => 1));
                $arrResp['mails_consultas'] = $arrConsutasWeb;
            }
            $this->session->set_userdata(array("alerta_timestamp" => time()));
        } else {
            $arrResp['check_alert'] = "off ".($valorActual - $valorAnterior);
        }
        echo json_encode($arrResp);
    }

    function listar_notificaciones($tipoAlerta = null) {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_alertas", "", false, $arrConf);
        $visto = $tipoAlerta == null ? null : 0;
        $limit_min = isset($_POST['limit_min']) ? $_POST['limit_min'] : null;
        $limit_cant = $_POST['limit_cant'] == -1 ? null : $_POST['limit_cant'];
        $arrAlertas = $this->Model_alertas->listar_alertas($this->session->userdata['codigo_usuario'], $visto, $tipoAlerta, array("alertas.fecha_hora", "DESC"), $limit_min, $limit_cant);
        echo json_encode($arrAlertas);
    }

    function marcar_como_leida() {
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_alertas", "", false, $arrConf);
        $arrResp = array();
        if (!$this->Model_alertas->marcar_como_leida($_POST['codigo_alerta'], $this->session->userdata['codigo_usuario'])) {
            $arrResp['error'] = $_POST['codigo_alerta'] .lang('error_marcar_alerta');
        } else {
            $arrResp['success'] = "success";
        }
        echo json_encode($arrResp);
    }

    public function envios_fallidos() {
        $validar_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_alertas", "", false, $arrConf);
        $data['titulo_pagina'] = 'Envios Fallidos';
        $data['page'] = 'alertas/envios_fallidos';
        $data['seccion'] = $validar_session;
        $data['registros'] = $this->Model_alertas->listarAlertasNoEnviadas();
        
        $this->load->view('container', $data);
    }

//    public function test() {
//        $filial = $this->session->userdata('filial');
//        $codFilial = $filial["codigo"];
//        $arrConf = array('codigo_filial' => $codFilial);
//        $this->load->model("Model_asistencias", "", false, $arrConf);
//        $arrResp['alertas_usuarios']['asistencias'] = $this->Model_asistencias->getComisionesSinAsistencia(true);
//        print_r($arrResp);
//    }
    
      public function cargar_asistencias() {
//        $validar_session = session_method();
//        $this->lang->load(get_idioma(), get_idioma());
//        $filial = $this->session->userdata('filial');
//        $codFilial = $filial["codigo"];
//        $arrConf = array('codigo_filial' => $codFilial);
//        $this->load->model("Model_asistencias", "", false, $arrConf);
//        $data['titulo_pagina'] = 'Cargar Asistencias';
//        $data['page'] = 'alertas/cargar_asistencias';
//        $data['seccion'] = $validar_session;
//        $data['registros'] =array();// $this->Model_asistencias->getComisionesSinAsistencias();
//        $this->load->view('container', $data);
    }

    public function bajaAlertaAlumnos(){
        session_method();
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_alertas", "", false, $arrConf);
        $arrCodigo_alerta = $this->input->post('eliminar_alerta');
        $arrResp = $this->Model_alertas->bajaAlertaAlumnos($arrCodigo_alerta);
       echo json_encode($arrResp);
    }
}
