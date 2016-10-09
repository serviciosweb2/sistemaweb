<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Reservas extends CI_Controller {
     private $seccion;

    public function __construct() {
        parent::__construct();
        
        
        $this->lang->load(get_idioma(), get_idioma());
        
        $this->seccion = session_method();
        
        $filial = $this->session->userdata('filial');

        $config = array("codigo_filial" => $filial["codigo"]);

        $this->load->model("Model_reserva_inscripciones", "", false, $config);
        /* CARGO EL LAG */
        
    }
    
     public function index() {
         //
        $this->lang->load(get_idioma(), get_idioma());
        $data['titulo_pagina'] = ''; //$validar_session;
        $data['page'] = 'aspirantes/vista_reserva_inscripciones'; // pasamos la vista a utilizar como parámetr
        $claves = array('estado_razon', 'codigo', "BIEN", "HABILITAR", "INHABILITAR", "HABILITADO", "INHABILITADO", 'ocurrio_error','tel_formato_invalido');
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('reservas');
        $data['columns'] = $this->getColumns();	
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }
    private function crearColumnas() {
        $this->load->helper('alumnos');       
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'id'),
            array("nombre" => lang('fecha'), "campo" => 'fecha'),
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang('email'), "campo" => 'email'),
            array("nombre" => lang('telefono'), "campo" => 'telefono'),
            array("nombre" => lang('comision'), "campo" => 'nombre_comision'),
            array("nombre" => lang('curso'), "campo" => 'nombre_curso')
        );
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }
    
    public function listarReservasInscripcionesDataTable(){
      $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $valores = $this->Model_reserva_inscripciones->listarReservasIncripcionDataTable($arrFiltros);
        echo json_encode($valores);
    }
    
    public function frm_detalle_reserva(){
        $cod_reserva = $this->input->post('cod_reserva');
        $objReserva = $this->Model_reserva_inscripciones->getObjReserva($cod_reserva);
        $data['objReserva'] = $objReserva;
        $data['arrDetalle'] = $this->Model_reserva_inscripciones->getDetalleReservaInscripcion($cod_reserva,$objReserva->id_plan);
        $this->load->view('aspirantes/ver_detalle_reserva_inscripcion',$data);
    }

    
    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function guardarMailsConsultasReservas(){
        $arrResp = array();
        if (!isset($_POST["id_comision"]) || !isset($_POST["id_mails_consultas"]) || !isset($_POST["id_plan"]) || !isset($_POST["id"])){
            $arrResp['error'] = "Parámetros Inválidos";
        } else {
            $conexion = $this->load->database("default", true);
            $myConsultaReserva = new Vmails_consultas_reservas($conexion);
            $myConsultaReserva->id_comision = $this->input->post("id_comision");
            $myConsultaReserva->id_mails_consultas = $this->input->post("id_mails_consultas");
            $myConsultaReserva->id_plan = $this->input->post("id_plan");
            if ($myConsultaReserva->guardadoForzado($this->input->post("id"))){
                $arrResp['success'] = "success";
                $arrResp['id'] = $myConsultaReserva->getCodigo();
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."]" .$conexion->_error_message();
            }
        }
        echo json_encode($arrResp);
    }
    
    /* La siguiente function esta siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR PERO SU USO DE DESACONSEJA
        VER DE UTILIZAR   sincronizar_reservas_inscripciones_web   */
    public function guardarReservaInscripcion(){
        $arrResp = array();
        if (!isset($_POST["confirmacion_enviada"]) || !isset($_POST["email"]) || !isset($_POST["estado"]) || !isset($_POST["fecha"])
             || !isset($_POST["id_comision"]) || !isset($_POST["id_curso"]) || !isset($_POST["id_filial"]) || !isset($_POST['id_plan'])
             || !isset($_POST["nombre"]) || !isset($_POST["telefono"]) || !isset($_POST["telefono"]) || !isset($_POST["id"])  ){            
            $arrResp["error"] = "Parámetros Inválidos";
        } else {
            $conexion = $this->load->database("default", true);
            $myReserva = new Vreserva_inscripciones($conexion);
            $myReserva->confirmacion_enviada = $this->input->post('confirmacion_enviada');
            $myReserva->email = $this->input->post("email");
            $myReserva->estado = $this->input->post("estado");
            $myReserva->fecha = $this->input->post("fecha");
            $myReserva->id_comision = $this->input->post("id_comision");
            $myReserva->id_curso = $this->input->post("id_curso");
            $myReserva->id_filial = $this->input->post("id_filial");
            $myReserva->id_plan = $this->input->post("id_plan");
            $myReserva->nombre = $this->input->post("nombre");
            $myReserva->telefono = $this->input->post("telefono");
            if ($myReserva->guardadoForzado($this->input->post("id"))){
                $arrResp['success'] = "success";
                $arrResp['id'] = $myReserva->getCodigo();
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        }
        echo json_encode($arrResp);
    }    
    
    /* La siguiente function esta siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function sincronizar_reservas_inscripciones_web(){

        $this->load->library('email');
        $arrResp = array();

        if (!isset($_POST["email"]) || !isset($_POST["id_comision"]) || !isset($_POST["id_filial"]) 
                || !isset($_POST['id_plan']) || !isset($_POST["nombre"]) || !isset($_POST["telefono"])){
            $arrResp["error"] = "Parámetros Inválidos";
        } else {
            $conexion = $this->load->database("default", true);
            if (Vreserva_inscripciones::reserva_ya_registrada($conexion, $this->input->post("id_filial"), $this->input->post("id_comision"), $this->input->post("email"))){
                $arrResp['error'] = "Reserva ya registrada";
            } else {
                $conexionFilial = $this->load->database($this->input->post("id_filial"), true);            
                $myComision = new Vcomisiones($conexionFilial, $this->input->post("id_comision"));
                $myPlanAcademico = new Vplanes_academicos($conexionFilial, $myComision->cod_plan_academico);            
                $myReserva = new Vreserva_inscripciones($conexion);
                $myReserva->confirmacion_enviada = 0;
                $myReserva->email = $this->input->post("email");
                $myReserva->estado = Vreserva_inscripciones::getEstadoPendiente();
                $myReserva->fecha = date("Y-m-d H:i:s");
                $myReserva->id_comision = $this->input->post("id_comision");
                $myReserva->id_curso = $myPlanAcademico->cod_curso;
                $myReserva->id_filial = $this->input->post("id_filial");
                $myReserva->id_plan = $this->input->post("id_plan");
                $myReserva->nombre = $this->input->post("nombre");
                $myReserva->telefono = $this->input->post("telefono");
                if ($myReserva->guardarReserva_inscripciones()){
                    $arrResp['success'] = "success";
                    $arrResp['id'] = $myReserva->getCodigo();

                    // Envia Notificacion al Alumno luego de su reserva by, Rodrigo Gliksberg - para mejorar -> templates

                    $myFilial = new Vfiliales($conexion, $this->input->post("id_filial"));
                    $config= array();
                    $config['charset'] = 'UTF-8';
                    $this->email->initialize($config);
                    $this->email->from($myFilial->email, 'IGA '.$myFilial->nombre);
                    $this->email->to($this->input->post("email"));

                    $this->email->reply_to($myFilial->email);


                    switch ($myFilial->idioma) {
                        case "es":
                            $msg = "<center><img src=\"https://www.iga-la.com/landing/assets/img/topmail.png\" alt=\"Instituto Gastronomico de las Americas\"></img></br><p>Estimado/a " . $this->input->post("nombre") . ", <b>tu lugar ya esta reservado!</b></br>".
                                "</br>Codigo de Reserva: ".$myReserva->getCodigo().
                                " </br>Recuerde que este mismo vence 2 dias a partir de la fecha de hoy ".date("d/m/Y")."</br> Rogamos comunicarse brevemente para finalizar la inscripción.</br> ".
                               "IGA -".$myFilial->nombre ."  " .$myFilial->domicilio ." - ".  $myFilial->ciudad  . " - " . $myFilial->provincia ." Tel: ".  $myFilial->telefono."</p></center>";                            ;
                            $sub = " reserva de inscripción";
                            break;
                        case "pt":
                            $msg = "<center><img src=\"https://www.iga-la.com/landing/assets/img/topmail.png\" alt=\"Instituto Gastronomico das Americas\"></img></br><p>Prezado/a " . $this->input->post("nombre") . ", <b>seu lugar já está reservado!!</b></br>".
                                "</br>Código de inscrição: ".$myReserva->getCodigo().
                                " </br>Lembre-se que o mesmo é válido por 2 dias, a contar a partir de hoje ".date("d/m/Y")."</br> Por favor, entre em contato o mais breve possível para finalizar sua inscrição.</br> ".
                                "IGA -".$myFilial->nombre ."  " .$myFilial->domicilio ." - ".  $myFilial->ciudad  . " - " . $myFilial->provincia ." Tel: ".  $myFilial->telefono."</p></center>";
                            $sub = " reserva de inscrição";
                            break;
                        case "in":
                            $msg = "<center><img src=\"https://www.iga-la.com/landing/assets/img/topmail.png\" alt=\"Instituto Gastronomico de las Americas\"></img></br><p>Dear " . $this->input->post("nombre") . ", <b>your place is already booked!</b></br>".
                                "</br>Code of registration: ".$myReserva->getCodigo().
                                " </br>Remember this booking expires two days from today's date ".date("m/d/Y")."</br> Please communicate briefly in order to finish your registration to:</br> ".
                                "IGA -".$myFilial->nombre ."  " .$myFilial->domicilio ." - ".  $myFilial->ciudad  . " - " . $myFilial->provincia ." Phone: ".  $myFilial->telefono."</p></center>";
                            $sub = " registration booking";
                            break;
                    }

                    $this->email->subject('IGA '.$myFilial->nombre. $sub);
                    $this->email->message($msg);
                    $this->email->send();


                } else {
                    $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                }
            }
        }
        echo json_encode($arrResp);
    }
}