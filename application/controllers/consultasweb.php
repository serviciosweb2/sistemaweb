<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Consultasweb extends CI_Controller {
    private $seccion;
    public function __construct() {
            parent::__construct();
            $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config =  array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_consultasweb", "", false, $config);
        $this->load->model("Model_inboxexterna", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());

        $this->load->model("Model_configuraciones", "", false, $config);
    }


    public function index($accion = null, $dataAccion = null) {
        $this->lang->load(get_idioma(), get_idioma());
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'consultasweb/vista_consultasweb'; // pasamos la vista a utilizar como parámetro
        $data['seccion'] = $this->seccion;

        $filial = $this->session->userdata("filial");
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_cursos", "", false, $arrConf);
        $data['arrCursos'] = $this->Model_cursos->listar();

        $claves = Array(
            'no_respondida',
            '_idioma',
            'ayer',
            'hoy',
            'maniana',
            'no_concretadas',
            'concretadas',
            'proxima_semana',
            'a_las',
            'la_semana_pasada',
            'debe_especificar_el_asunto_del_mensaje',
            'debe_especificar_el_nombre_y_apellido',
            'debe_indicar_un_email_valido',
            'debe_especificar_la_consulta',
            'caracteres_minimo',
            'los_campos_en_rojo_son_requeridos',
            'debe_seleccionar_al_menos_un_template',
            'error_al_recuperar_registros',
            'no_tiene_mensajes',
            'no_leidos',
            'mensajes',
            'validadion_ok',
            'modificacion_ok',
            'eliminacion_ok',
            'enviar',
            'ok',
            'siguiente',
            'upps',
            'ocurrio_error',
            'template_error_titulo',
            'template_error_limite_descripcion',
            'template_error_vacio_descripcion',
            'label_contador_caracteres',
            'template_warning_limite_titulo',
            'template_warning_limite_descripcion',
            'nombre',
            'EMAIL',
            'TELEFONO',
            'asunto',
            'consulta',
            'guardar_consulta',

                'notif_inbox_externa_habilitada_titulo',
                'notif_inbox_externa_habilitada_mensaje',
                'form_password_inbox_externa_titulo',
                'form_password_inbox_externa_mensaje_parte_1',
                'form_password_inbox_externa_mensaje_parte_2',
                'form_password_inbox_externa_mensaje_parte_3',
                'password',
                'enviar_contrasenia',
                'inbox_externa',

                'agregar_cuotas',
                'eliminar_plan_de_pagos',
                'agregar_plan_de_pagos',
            'debe_especificar_como_nos_conocio',
            'como_nos_conocio'
                
            );
            $data['lang'] = getLang($claves);
            if ($accion != null && $dataAccion != null){
                $data['accion'] = $accion;
                $data['codigo'] = $dataAccion;
            }
            $this->load->view('container',$data);
    }

    public function listar() {
		$filial = $this->session->userdata('filial');
        $idfilial = $filial['codigo'];
        $tipoConsulta = ($this->input->post('tipoConsulta')) ? $this->input->post('tipoConsulta') : 'inbox';

        $length = 10;
        $arrFiltros["iDisplayStart"] =$_POST['iDisplayStart'] != '' ? $_POST['iDisplayStart'] : 0;
        $arrFiltros["iDisplayLength"] = isset($length) ? $length : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";


        $arrFiltros["Estado"] = isset($_POST['Estado']) ? $_POST['Estado'] : "";
        $arrFiltros["Curso"] = isset($_POST['Curso']) ? $_POST['Curso'] : "";
        $arrFiltros["FechaDesde"] = $this->input->post("FechaDesde") && $this->input->post("FechaDesde") <> ''
            ? formatearFecha_mysql($this->input->post("FechaDesde"))
            : null;

        $arrFiltros["FechaHasta"] = $this->input->post("FechaHasta") && $this->input->post("FechaHasta") <> ''
            ? formatearFecha_mysql($this->input->post("FechaHasta"))
            : null;

        $arrFiltros["FechaDesdeConsulta"] = $this->input->post("FechaDesdeConsulta") && $this->input->post("FechaDesdeConsulta") <> ''
            ? formatearFecha_mysql($this->input->post("FechaDesdeConsulta"))
            : null;

        $arrFiltros["FechaHastaConsulta"] = $this->input->post("FechaHastaConsulta") && $this->input->post("FechaHastaConsulta") <> ''
            ? formatearFecha_mysql($this->input->post("FechaHastaConsulta"))
            : null;

        if(!empty($this->input->post("orderField")) && $this->input->post("orderField") != '') {
            $order['field'] = $this->input->post("orderField");
            if(!empty($this->input->post("order")) && $this->input->post("order") != '') {
                $order['order'] = $this->input->post("order");
            }
            else {
                $order['order'] = null;
            }
        }
        else {
            $order = null;
        }

        if ($tipoConsulta === 'inbox_externa') {
            $valores = $this->Model_inboxexterna->listarMailsInboxExterna($idfilial, $arrFiltros);

            if ( !$this->Model_inboxexterna->inboxExternaEstaHabilitada() ) {
                $valores['noInboxPassword'] = true;

                $this->load->model("Model_filiales", "", false, $idfilial);

                $filial_data = $this->Model_filiales->getFilial();
                if (isset($filial_data["email"]) && $filial_data["email"] != "") {
                    $valores['email_filial'] = $filial_data["email"];
                }
            }
        }
        else {
            $valores = $this->Model_consultasweb->listarMailsConsultas($idfilial, $tipoConsulta, $arrFiltros, null, $order);
        }
		
		/*
		 * Parche para corregir fecha y hora. Se debería localizar completamente la aplicación.
		 */
		foreach ($valores["aaData"] as $key => $consulta_actual) {
			//echo "\n\nDate before: " . $valores["aaData"][$key]["fechahora"];

			if (isset($consulta_actual["fechahora"])) {
				$date_consulta_actual = new DateTime($consulta_actual["fechahora"]);
				$date_consulta_actual->sub(new DateInterval('PT3H'));
				$valores["aaData"][$key]["fechahora"] = $date_consulta_actual->format('Y-m-d H:i:s');
			}

            if (isset($consulta_actual["fechahoraconsulta"])) {
                $date_consulta_actual = new DateTime($consulta_actual["fechahoraconsulta"]);
                $date_consulta_actual->sub(new DateInterval('PT3H'));
                $valores["aaData"][$key]["fechahoraconsulta"] = $date_consulta_actual->format('Y-m-d H:i:s');
            }
			//echo "\n Date after: " . $valores["aaData"][$key]["fechahora"];
		}
		
		//print_r($valores);
		//die();

        echo json_encode($valores);
    }

    public function cambiarEstadoAsunto(){

        $arrAsuntosCodigos = $this->input->post('idAsunto');
        $estado = $this->input->post('cambiarEstado');

        $resultado = $this->Model_consultasweb->cambiarEstadoAsunto($arrAsuntosCodigos,$estado);
        echo json_encode($resultado);
    }

    public function destacarAsunto(){

        $destacar = $this->input->post('destacar');
        $id_asunto = $this->input->post('idAsunto');
        $resultado = $this->Model_consultasweb->destacarAsunto($destacar, $id_asunto);
        echo json_encode($resultado);
    }

    /**
     * retorna json del seguimiento y las respuestas de una consulta en particular
     */
    public function ver_consulta(){

        $codConsulta = $this->input->post("cod_consulta");
        $arrResp = $this->Model_consultasweb->listar_seguimiento_consulta($codConsulta);
        echo json_encode($arrResp);
    }

    /**
     * Retorna JSON con los datos del email especificado por POST.
     */
    public function ver_email_inbox_externa() {
        $uid_email = $this->input->post("email_uid");
        $campos_email = $this->Model_inboxexterna->getEmailData($uid_email);

        echo json_encode($campos_email);
    }

    /**
     * Retorna JSON con los cursos habilitados
     */
    public function get_cursos_json() {
        $filial = $this->session->userdata('filial');
        $filial = $filial["codigo"];

        $config = array("codigo_filial" => $filial);
        $this->load->model("Model_cursos", "", false, $config);
        $arrCursos = $this->Model_cursos->getCursosHabilitados(null, null, 0);

        echo json_encode($arrCursos);
    }

    /**
     * Marca una consulta como leida
     */
    public function marcar_leida(){

        $codConsulta = $this->input->post("cod_consulta");
        $arrResp = array();
        if ($this->Model_consultasweb->marcar_leida($codConsulta, true)){
            $arrResp["success"] = "success";
        } else {
            $arrResp['error'] = "error al marcar la consulta como leida"; // ver que mensaje de error enviar a la vista
        }
        echo json_encode($arrResp);
    }

    /**
     * Guarda el password de la cuenta externa via AJAX
     */
    public function setInboxExternaPassword() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('inbox_password', lang('password'), 'required|validarPassword');

        $response = array();
        if ($this->form_validation->run() === false) {
            $errors = validation_errors();
            $response['success'] = false;
            $response['errors'] = $errors;
        } else {
            if ( $this->Model_inboxexterna->testInboxLoginWithPassword($this->input->post('inbox_password')) ) {
                if ( $this->Model_inboxexterna->saveInboxExternaPassword($this->input->post('inbox_password')) ) {
                    $response['success'] = true;
                }
                else {
                    $response['success'] = false;
                }
            }
            else {
                $response['success'] = false;
                $response['errors'] = array(0 => lang('error_login_imap'));
            }
        }

        echo json_encode($response);
    }

    /**
     * Marca la lectura de un email en inbox externa.
     */
    public function marcar_leido_inbox_externa() {
        $uid_email = $this->input->post("uid_email");

        $response = array();
        if ($this->Model_inboxexterna->marcarLeidos([$uid_email])){
            $response["success"] = "success";
        } else {
            $response['error'] = "error al marcar el email como leido"; // ver que mensaje de error enviar a la vista
        }

        echo json_encode($response);
    }

    /**
     * Marcar la lectura de emails especificados en inbox externa.
     */
    public function marcar_leidos_inbox_externa(){
        $uids_emails = $this->input->post("uids_emails");

        $response = array();
        if ($this->Model_inboxexterna->marcarLeidos($uids_emails)){
            $response["success"] = "success";
        } else {
            $response['error'] = "error al marcar el email como leido"; // ver que mensaje de error enviar a la vista
        }

        echo json_encode($response);
    }

    /**
     * Oculta emails especificados en inbox externa.
     */
    public function marcar_ocultos_inbox_externa() {
        $uids_emails = $this->input->post("uids_emails");

        $response = array();
        if ( $this->Model_inboxexterna->marcarOcultos($uids_emails) ){
            $response["success"] = "success";
        } else {
            $response['error'] = "error al marcar el email como oculto"; // ver que mensaje de error enviar a la vista
        }

        echo json_encode($response);
    }

    /**
     * carga la vista del formulario de generar nueva consulta
     */
    public function nueva_consulta(){
        $validar_session= session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $data['page_title'] = '';
        $data['seccion'] = $validar_session;
        $filial = $this->session->userdata('filial');
        $config =  array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cursos", "", false,$config);
        $configComoNosConocio = array("idioma" => get_idioma());
        $this->load->model("Model_como_nos_conocio", "", false, $configComoNosConocio);
        $data['arrMedios'] = $this->Model_como_nos_conocio->getComoNosConocio($filial["codigo"]);
        $arrCursos = $this->Model_cursos->getCursosHabilitados(null, null, 0);
        $data['arrCursos'] = $arrCursos;
        $data['campo_curso'] = "nombre_".get_idioma();
        $this->load->view('consultasweb/nueva_consulta',$data);
    }

    /* la siguiente function está siendo accedida desde un web services *** NO MODIFICAR, COMENTAR O ELIMINAR *** */
    public function guardar_consulta_ws(){
        $arrResp = array();
        $codFilial = $this->input->post("cod_filial");
        $nombre = $this->input->post("nombre");
        if ($this->input->post('apellido')){
            $nombre .= " ".$this->input->post("apellido");
        }
        $conexion = $this->load->database($codFilial, true);
        $conexion->trans_begin();
        $myConsulta = new Vmails_consultas($conexion);
        $myConsulta->asunto = $this->input->post("asunto");
        $myConsulta->cod_curso_asunto = $this->input->post("cod_curso_asunto");
        $myConsulta->cod_filial = $codFilial;
        $myConsulta->destacar = $this->input->post("destacar");
        $myConsulta->estado = $this->input->post("estado");
        $myConsulta->fechahora = $this->input->post("fechahora");
        $myConsulta->generado_por_filial = $this->input->post("generado_por_filial");
        $myConsulta->mail = $this->input->post("mail");
        $myConsulta->nombre =$nombre;
        $myConsulta->notificar = $this->input->post("notificar");
        $myConsulta->respuesta_automatica_enviada = $this->input->post("respuesta_automatica_enviada");
        if(!empty($this->input->post('como_nos_conocio'))) {
            $myConsulta->como_nos_conocio_codigo = $this->input->post('como_nos_conocio');
        }
        $telefono = $this->input->post("telefono");
        if ($this->input->post("telefono")){
            $telefono = preg_replace("/[^0-9]/", "", $telefono);
        }
        $myConsulta->telefono = $telefono;
        $myConsulta->tipo_asunto = $this->input->post("tipo_asunto");
        if ($this->input->post("codigo") <> -1 ){
            $myConsulta->guardadoForzado($this->input->post("codigo"));
            $codConsulta = $this->input->post("codigo");
        } else {
            $myConsulta->guardarMails_consultas();
            $codConsulta = $myConsulta->getCodigo();
        }

        $myRespuesta = new Vmails_respuesta_consultas($conexion);
        $myRespuesta->cod_consulta = $codConsulta;
        $myRespuesta->emisor = 0;
        $myRespuesta->estado = Vmails_respuesta_consultas::getEstadoNoEnviar();
        $myRespuesta->fecha_hora = $this->input->post("fechahora");
        $myRespuesta->html_respuesta = $this->input->post("html_respuesta");
        $myRespuesta->guardarMails_respuesta_consultas();
        if ($this->input->post("agregar_reserva") && $this->input->post("agregar_reserva") == "true"){
            $myConsultaReserva = new Vmails_consultas_reservas($conexion);
            $myConsultaReserva->id_comision = $this->input->post("cod_comision");
            $myConsultaReserva->id_mails_consultas = $codConsulta;
            $myConsultaReserva->id_plan = $this->input->post("cod_plan");
            $myConsultaReserva->guardarMails_consultas_reservas();
        }
        $email = $this->input->post("mail");
        if (trim($email) <> ''){
            $condiciones = array("email" => $email);
            $cantidad = Vaspirantes::listarAspirantes($conexion, $condiciones, null, null, null, true);
            if ($cantidad == 0){
                if ($this->input->post("apellido")){
                    $apellido = $this->input->post("apellido");
                    $nombre = $this->input->post("nombre");
                } else {
                    $temp = $this->input->post("nombre") ? $this->input->post("nombre") : '';
                    $arrTemp = explode(" ", $temp);
                    if (count($arrTemp) == 1){
                        $arrTemp = explode(",", $temp);
                    }
                    if (count($arrTemp) > 1){
                       $nombre = $arrTemp[0];
                       $apellido = $arrTemp[1];
                    } else {
                        $nombre = $this->input->post("nombre");
                        $apellido = ' ';
                    }
                }
                $myFilial = new Vfiliales($conexion, $codFilial);
                $tipoDocumento = Vpaises::getDocumentoDefaultPais($myFilial->pais);
                $myAspirante = new Vaspirantes($conexion);
                $myAspirante->apellido = $apellido;
                $myAspirante->calle = '';
                $myAspirante->calle_numero = 0;
                if(!empty($this->input->post('como_nos_conocio'))) {
                    $myAspirante->comonosconocio = $this->input->post('como_nos_conocio');
                }
                $myAspirante->documento = ' ';
                $myAspirante->email = $email;
                $myAspirante->email_enviado = 1;
                $myAspirante->fechaalta = $this->input->post("fechahora");
                $myAspirante->nombre = $nombre;
                $myAspirante->tipo = $tipoDocumento;
                $myAspirante->tipo_contacto = 'EMAIL';
                $myAspirante->usuario_creador = 868;
                $myAspirante->guardarAspirantes();
                if ($telefono){
                    $myTelefono = new Vtelefonos($conexion);
                    $myTelefono->baja = 0;
                    $myTelefono->cod_area = 0;
                    $myTelefono->numero = $telefono;
                    $myTelefono->tipo_telefono = 'fijo';
                    $myTelefono->guardarTelefonos();
                    $myAspirante->setTelefonosAspirante($myTelefono->getCodigo(), 1);
                }
                if ($this->input->post("tipo_asunto") == 'curso' && $this->input->post("cod_curso_asunto")){
                    $myAspirante->setCursosDeInteres(array($this->input->post("cod_curso_asunto")), array(4), array(0), array('normal'));
                }
            }
        }
        
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            $arrResp['success'] = "success";
            $arrResp['cod_filial'] = $this->input->post("cod_filial");
            $arrResp['codigo_consulta'] = $this->input->post("codigo") <> -1 ? $this->input->post("codigo") : $myConsulta->getCodigo();
            $arrResp['codigo_respuesta'] = $myRespuesta->getCodigo();
        } else {
            $conexion->trans_rollback();
            $arrResp['error'] = "Error";
            $arrResp['msg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }

    /* La siguiente function está siendo accedida desde un web services (NO BORRAR, COMENTAR NI MODIFICAR) */
    public function getMaxCodigo(){
        $idFilial = $this->input->post("cod_filial");
        echo json_encode($this->Model_consultasweb->getMaxCodigo($idFilial));
    }

    /* La siguiente fnuction está siendo accedida desde un web services (NO BORRAR, COMENTAR NI MODIFICAR) */
    public function guardar_respuesta_consulta(){
        $conexion = $this->load->database("mails_consultas", true);
        $codigoOrigen = $this->input->post("id");
        $resp = array();
        if (!Vmails_respuesta_consultas::consultaYaRegistrada($conexion, $codigoOrigen)){
            $myRespuesta = new Vmails_respuesta_consultas($conexion);
            $myRespuesta->cod_consulta = $this->input->post("id_consulta");
            $myRespuesta->emisor = $this->input->post("emisor");
            $myRespuesta->fecha_hora = $this->input->post("fecha_hora");
            $myRespuesta->html_respuesta = $this->input->post("html_respuesta");
            $myRespuesta->estado = Vmails_respuesta_consultas::getEstadoNoEnviar();
            $myRespuesta->id_respuesta_origen = $codigoOrigen;
            if ($myRespuesta->guardarMails_respuesta_consultas()){
                $resp['success'] = "success";
            } else {
                $resp['error'] = "error al guardar mails_respuesta_consutlas";
            }
        } else {
            $resp['success'] = "success";
        }
        echo json_encode($resp);
    }

    public function registrar_respuestas_consulta(){
        $conexion = $this->load->database("mails_consultas", true);
        $codConsulta = $this->input->post("cod_consulta");
        $htmlRespuesta = $this->input->post("html_respuesta");
        $emisor = $this->input->post("emisor");
        $vista = $this->input->post("vista") ? $this->input->post("vista") : null;
        $fechaHora = $this->input->post("fecha_hora") ? $this->input->post("fecha_hora") : date("Y-m-d H:i:s");
        $estado = $this->input->post("estado");
        $idUsuario = $this->input->post("id_usuario") ? $this->input->post("id_usuario") : null;
        $idRespuestaOrigen = $this->input->post("id_respuesta_origen") ? $this->input->post("id_respuesta_origen") : null;
        $emailOrigen = $this->input->post("email");
        $myConsulta = new Vmails_consultas($conexion, $codConsulta);
        if ($myConsulta->getCodigo() > -1){
            $codConsulta = $myConsulta->getCodigo();
        } else {
            $codConsulta = Vmails_consultas::getCodigoConsutaDesdeEmail($conexion, $emailOrigen);
        }
        $arrResp = array();
        if ($codConsulta > -1){
            if (Vmails_consultas::respuestaYaRegistrada($conexion, $codConsulta, $fechaHora)){
                $arrResp['success'] = "success";
                $arrResp['msg'] = "Consulta ya registrada - Actualizado";
            } else {
                $myRespuesta = new Vmails_respuesta_consultas($conexion);
                $myRespuesta->cod_consulta = $codConsulta;
                $myRespuesta->emisor = $emisor;
                $myRespuesta->estado = $estado;
                $myRespuesta->fecha_hora = $fechaHora;
                $myRespuesta->html_respuesta = base64_decode($htmlRespuesta);
                $myRespuesta->id_respuesta_origen = $idRespuestaOrigen;
                $myRespuesta->id_usuario = $idUsuario;
                $myRespuesta->vista = $vista;
                if ($myRespuesta->guardarMails_respuesta_consultas()){
                    $arrResp['success'] = "success";
                    $arrResp['msg'] = 'actualizado';
                    $arrResp['codigo'] = $myRespuesta->getCodigo();
                } else {
                    $arrResp['error'] = "error";
                    $arrResp['msg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                }
            }
        } else {
            $arrResp['error'] = "error";
            $arrResp['msg'] = "No se encuentra la consulta de origen para el email $emailOrigen";
        }
        echo json_encode($arrResp);
    }

    /**
     * guarda una nueva consulta
     */
    public function guardar_consulta(){

        $asunto = $this->input->post("asunto");
        $nombreApellido = $this->input->post("nombre_apellido");
        $telefono = $this->input->post("telefono");
        $email = $this->input->post("email");
        $consulta = $this->input->post("consulta");
        $como_nos_conocio_codigo = null;
        if (!empty($this->input->post("como_nos_conocio_codigo"))) {
            $como_nos_conocio_codigo = $this->input->post("como_nos_conocio_codigo");
        }
        $arrResp = array();
        if (!$this->Model_consultasweb->guardarNuevaConsulta($asunto, $nombreApellido, $telefono, $email, $consulta, null, null, $como_nos_conocio_codigo)){
            $arrResp['error'] = "error al guardar la consulta";
        } else {
            $arrResp['success'] = "success";
        }
        echo json_encode($arrResp);
    }

    /**
     * carga la vista de responder consulta web
     */
    public function responder_consulta(){
        $filial = $this->session->userdata('filial');
        $idfilial = $filial['codigo'];
        $idConsulta = $this->input->post("id_consulta");
        $arrConsulta = $this->Model_consultasweb->listarMailsConsultas($idfilial, null, null, array("mails_consultas.mails_consultas.codigo" => $idConsulta));
        $data = array();
        if (isset($_POST['templates'])) $data['templates_seleccionados'] = $_POST['templates'];
        $data['arrConsulta'] = $arrConsulta['aaData'][0];
        $nombreLike = $this->input->post("nombre_search") ? $this->input->post("nombre_search") : null;
        $arrTemplates = $this->Model_consultasweb->listarTemplatesCantidades($nombreLike, true);
        $data['arrTemplates'] = $arrTemplates;
        $data['nombre_campo'] = "nombre_".get_idioma();
        $this->load->view('consultasweb/responder_consulta',$data);
    }

    /**
     * carga la vista para completar los valores de los templates
     */
    public function responder_consulta_completar_valores(){

        $data = array();
        $arrTemplates = $this->input->post("templates");
        $codConsulta = $this->input->post("cod_consulta");
        $data['cod_consulta'] = $codConsulta;
        $html = $this->Model_consultasweb->getHTMLTemplates($arrTemplates, $codConsulta, true);
        $data["html"] = $html;
        $data["templates_seleccionados"] = $arrTemplates;
        $this->load->view('consultasweb/responder_consulta_completar_valores',$data);
    }

    /**
     * retorna la lista de templates filtrando por el nombre del template
     */
    public function buscar_templates(){

        $nombreLike = $this->input->post("nombre_search") ? $this->input->post("nombre_search") : null;
        $arrTemplates = $this->Model_consultasweb->listarTemplatesCantidades($nombreLike);
        if (is_array($arrTemplates)){
            $arrResp['data'] = $arrTemplates;
        } else {
            $arrResp['error'] = "Error al filtrar templates por nombre";
        }
        echo json_encode($arrResp);
    }

    public function mostrar_vista_previa(){
        if ($this->Model_consultasweb->guardar_valores_por_defecto($this->input->post("param"))){
            $html = $this->Model_consultasweb->getHTMLTemplates($this->input->post("templates"), $this->input->post("cod_consulta"), false);
            $data["html"] = html_entity_decode($html);
            $data['templates_seleccionados'] = $this->input->post("templates");
            $data['cod_consulta'] = $this->input->post("cod_consulta");
            $this->load->view('consultasweb/vista_previa_envio',$data);
        } else {
            echo "error al guardar los valores del template";
        }
    }

    public function guardar_respuesta_template(){
        $arrResp = array();
        $session = $this->session;
        if ($this->Model_consultasweb->guardar_respuesta_template($this->input->post("templates"), $this->input->post("cod_consulta"), $session->userdata['codigo_usuario'])){
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "Error al guardar la respuesta";
        }
        echo json_encode($arrResp);
    }

    /* La siguiente function está siendo accedida desde un web services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function reporte_consultas_por_filiales(){
        $fechaDesde = $this->input->post("fecha_desde") ? $this->input->post("fecha_desde") : null;
        $fechaHasta = $this->input->post("fecha_hasta") ? $this->input->post("fecha_hasta") : null;
        $conexion = $this->load->database("general", true);
        $arrResp = Vmails_consultas::getCantidadesPorFilial($conexion, $fechaDesde, $fechaHasta);
        echo json_encode($arrResp);
    }

    /* La siguiente function está siendo accedida desde un web services NO MODIFICAR; COMENTAR NI ELIMINAR */
    public function reporte_asuntos_cantidades(){
        $fechaDesde = $this->input->post("fecha_desde") ? $this->input->post("fecha_desde") : null;
        $fechaHasta = $this->input->post("fecha_hasta") ? $this->input->post("fecha_hasta") : null;
        $codFilial = $this->input->post("cod_filial") ? $this->input->post("cod_filial") : null;
        $conexion = $this->load->database("general", true);
        $arrResp = Vmails_consultas::getCantidadesPorAsunto($conexion, $fechaDesde, $fechaHasta, $codFilial);
        echo json_encode($arrResp);
    }

    /* La siguiente function está siendo accedida desde un web services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function reporte_cantidades_por_curso(){
        $fechaDesde = $this->input->post("fecha_desde") ? $this->input->post("fecha_desde") : null;
        $fechaHasta = $this->input->post("fecha_hasta") ? $this->input->post("fecha_hasta") : null;
        $codFilial = $this->input->post("cod_filial") ? $this->input->post("cod_filial") : null;
        $conexion = $this->load->database("general", true);
        $arrResp = Vmails_consultas::getConsultasPorCurso($conexion, $fechaDesde, $fechaHasta, $codFilial);
        echo json_encode($arrResp);
    }

    /* La siguiente function está siendo accedida desde un web services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function reporte_de_estados(){
        $conexion = $this->load->database("general", true);
        $fechaDesde = $this->input->post("fecha_desde") ? $this->input->post("fecha_desde") : null;
        $fechaHasta = $this->input->post("fecha_hasta") ? $this->input->post("fecha_hasta") : null;
        $codFilial = $this->input->post("cod_filial") ? $this->input->post("cod_filial") : null;
        $arrConsultas = Vmails_consultas::getFilialesEstados($conexion, $fechaDesde, $fechaHasta, $codFilial);
        $arrResp = array();
        foreach ($arrConsultas as $consulta){
            $arrResp[$consulta['codigo']]['nombre'] = $consulta['nombre'];
            $arrResp[$consulta['codigo']][$consulta['estado']] = $consulta['cantidad'];
        }
        echo json_encode($arrResp);
    }

    public function reporte_consultas_data_table(){
        $idFilial = isset($_POST['id_filial']) && $_POST['id_filial'] <> '' ? $_POST['id_filial'] : null;
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $estado = isset($_POST['estado']) && $_POST['estado'] <> -1 ? $_POST['estado'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $codConsulta = isset($_POST['cod_consulta']) && $_POST['cod_consulta'] <> '' ? $_POST['cod_consulta'] : null;
        $arrResp = $this->Model_consultasweb->listarDataTables($idFilial, $arrLimit, $arrSort, $search, $searchField, $fechaDesde, $fechaHasta, $estado, $codConsulta);
        echo json_encode($arrResp);
    }
    
    public function reporte_consultasweb_datatable() {
        $fechaDesde = isset($_POST['fechaDesde'])?$_POST['fechaDesde']:null;
        $fechaHasta = isset($_POST['fechaHasta'])?$_POST['fechaHasta']:null;
        $cursos = isset($_POST['cursos'])?$_POST['cursos']:null;
        $cursosCortos = isset($_POST['cursosCortos'])?$_POST['cursosCortos']:null;
        $tipo = isset($_POST['tipo'])?$_POST['tipo']:null;
        if($cursos != null && !is_array($cursos)){
            $cursos = array($cursos);
        } else if ($cursos == null) {
            $cursos = array();
        }

        if($cursosCortos != null && !is_array($cursosCortos)){
            $cursosCortos = array($cursosCortos);
        }

        /*Logs:*/
        $log = "";
        $log = $log . "Fecha desde: " . $fechaDesde . "\n";
        $log = $log . "Fecha hasta: " . $fechaHasta . "\n";
        $log = ($cursos == null)?$log:($log . "Cursos: (" . implode(",", $cursos) . ")\n");
        $log = ($cursosCortos == null)?$log:($log . "Cursos cortos: (" . implode(",", $cursosCortos) . ")\n");
        $log = $log . "Tipo: " . $tipo;
        $arrResp = $this->Model_consultasweb->listarConsultasWebWS($fechaDesde, $fechaHasta, $cursos, $cursosCortos, $tipo);

        echo json_encode($arrResp);
    }

    public function reporte_consultafb_alumno($fechaDesde = null, $fechaHasta = null, $filial = null)
    {
        $this->load->helper('formatearfecha');

        echo '<meta charset="utf-8" />';
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
        echo '<div class="container"><h2>Alumnos matriculados desde Facebook<small class="pull-right">'.formatearFecha_pais($fechaDesde).' - '.formatearFecha_pais($fechaHasta).'</small></h2><hr>';

        $conexion = $this->load->database('mails_consultas', true);

        $fechaDesde = isset($fechaDesde) ? $fechaDesde : null;
        $fechaHasta = isset($fechaHasta) ? $fechaHasta : null;

        $this->load->model("Model_filiales", "", false, '');
        $filiales = $this->Model_filiales->getFiliales(1);


        if($filial) {

            echo '<div class="list-group">';

            $filialSeleccionada = '';

            foreach ($filiales as $fili){

                if($fili['codigo'] == $filial){
                    $filialSeleccionada = $fili;
                };

            }

            if(!empty($fechaDesde) || !empty($fechaHasta)) {

                $conexion->select('mc.codigo,
                    general.filiales.nombre as filial,
                    mc.nombre as nombre_consultante,
                    mc.mail as email_consultante,
                    DATE(mc.fechahora) as fecha_consulta,
                    aspi.codigo as cod_aspirante,
                    CONCAT(aspi.nombre," ",aspi.apellido) as aspirante,
                    alu.codigo as cod_alumno,
                    CONCAT(alu.nombre," ", alu.apellido) as alumno', FALSE);
                $conexion->from('mails_consultas.mails_consultas mc');
                $conexion->join('general.filiales', 'general.filiales.codigo = mc.cod_filial');
                $conexion->join('' . $filialSeleccionada['codigo'] . '.aspirantes aspi', 'aspi.email = mc.mail', 'RIGHT');
                $conexion->join('' . $filialSeleccionada['codigo'] . '.aspirantes_alumnos aa', 'aa.id_aspirante = aspi.codigo');
                $conexion->join('' . $filialSeleccionada['codigo'] . '.alumnos alu', 'alu.codigo = aa.id_alumno');
                $conexion->where('mc.cod_filial = ' . $filialSeleccionada['codigo'] . '
                    AND DATE(mc.fechahora) >= "' . $fechaDesde . '"
                    AND DATE(mc.fechahora) <= "' . $fechaHasta . '"');
                $conexion->group_by('mc.mail');
                $conexion->order_by('mc.fechahora DESC');

                $data = $conexion->get()->result();

                if (!empty($data)) {
                    $cantidad = count($data);

                    echo '<a data-toggle="collapse" href="#collapseExample" class="list-group-item">';
                    echo '<span class="badge">' . $cantidad . '</span>';
                    echo $filialSeleccionada['nombre'];
                    echo '</a>';
                    echo '<div class="collapse" id="collapseExample">';

                    echo '<div class="list-group">';

                    foreach ($data as $alumno){
                        echo '<li class="list-group-item">'.$alumno->alumno . '</li>';
                    }

                    echo '</div>';

                    echo '</div>';

                } else {
                    //echo $filialSeleccionada['nombre'] .": Ninguna conversión <hr />";
                }

            } else {
                echo "filial o fechas vacias";
            }

            echo '</div>';

        } else {

            if(!empty($filiales) || !empty($fechaDesde) || !empty($fechaHasta)) {

                echo '<div class="list-group">';

                foreach ($filiales as $filial) {

                    $filiales_desactivadas = array(9, 33, 65, 74);

                    if(!in_array($filial['codigo'], $filiales_desactivadas)){

                        $conexion->select('mc.codigo,
                        general.filiales.nombre as filial,
                        mc.nombre as nombre_consultante,
                        mc.mail as email_consultante,
                        DATE(mc.fechahora) as fecha_consulta,
                        aspi.codigo as cod_aspirante,
                        CONCAT(aspi.nombre," ",aspi.apellido) as aspirante,
                        alu.codigo as cod_alumno,
                        CONCAT(alu.nombre," ", alu.apellido) as alumno', FALSE);
                        $conexion->from('mails_consultas.mails_consultas mc');
                        $conexion->join('general.filiales', 'general.filiales.codigo = mc.cod_filial');
                        $conexion->join( $filial['codigo'] . '.aspirantes aspi', 'aspi.email = mc.mail', 'RIGHT');
                        $conexion->join( $filial['codigo'] . '.aspirantes_alumnos aa', 'aa.id_aspirante = aspi.codigo');
                        $conexion->join( $filial['codigo'] . '.alumnos alu', 'alu.codigo = aa.id_alumno');
                        $conexion->where('mc.cod_filial = ' . $filial['codigo'] . '
                        AND DATE(mc.fechahora) >= "' . $fechaDesde . '"
                        AND DATE(mc.fechahora) <= "' . $fechaHasta . '"');
                        $conexion->group_by('mc.mail');
                        $conexion->order_by('mc.fechahora DESC');

                        $data = $conexion->get()->result();

                        if (!empty($data)) {
                            $cantidad = count($data);

                            echo '<a data-toggle="collapse" href="#collapseExample'.$filial['codigo'].'" class="list-group-item">';
                            echo '<span class="badge">' . $cantidad . '</span>';
                            echo $filial['nombre'];
                            echo '</a>';
                            echo '<div class="collapse" id="collapseExample'.$filial['codigo'].'">';

                            echo '<div class="list-group">';

                            foreach ($data as $alumno){
                                echo '<li class="list-group-item">'.$alumno->alumno . '</li>';
                            }

                            echo '</div>';

                            echo '</div>';

                        } else {

                            //echo $filial['nombre'] .": Ninguna conversión <hr />";

                        }

                    }

                }

                echo '</div>';

            } else {

            }

        }

        echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js"></script>';
        echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>';
        echo "</div>";

    }

    public function get_como_nos_conocio_filial() {
        $idioma = $this->input->post("idioma") ? $this->input->post("idioma") : 'es';
        $filial = $this->input->post("filial");
        $configComoNosConocio = array("idioma" => $idioma);
        $this->load->model("Model_Como_nos_conocio", "", false, $configComoNosConocio);
        $data = $this->Model_Como_nos_conocio->getComoNosConocio($filial, 1);
        echo json_encode($data);
    }

}
