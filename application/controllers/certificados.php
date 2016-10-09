<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Certificados extends CI_Controller {

    public $columnas = array();

    public function __construct() {
        parent::__construct();
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("filial" => $filial);
        $this->load->model("Model_certificados", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
    }

    private function  crearColumnas2(){
        $columnas = array(
            array("nombre" => ' ', "campo" => '', "sort" => false),
            array("nombre" => lang('matricula'), "campo" => 'cod_matricula'),
            array("nombre" => lang('nombre'), "campo" => 'nombre_apellido'),
            array("nombre" => lang('tipo_documento'), "campo" => 'documento_alumno', "sort" => false),
            array("nombre" => lang('curso'), "campo" => 'nombre', "sort" => false),
            array("nombre" => lang('fecha_matricula'), "campo" => 'cod_tipo_periodo'),
            array("nombre" => lang('fecha_desde'), "campo" => 'fecha_inicio'),
            array("nombre" => lang('fecha_fin'), "campo" => 'fecha_fin'),
            array("nombre" => lang('titulo'), "campo" => 'titulo', "sort" => false),
            array("nombre" => lang('fecha_pedido'), "campo" => 'fecha_pedido'),
            array("nombre" => lang('pedido'), "campo" => 'id_producto_pedido'),
            array("nombre" => lang('estado'), "campo" => 'estado', "sort" => false),
            array("nombre" => lang('detalles'), "campo" => 'detalles', "sort" => false),
            array("nombre" => lang('usuario'), "campo" => 'usuario_aprueba'),
            array("nombre" => lang('recibido'), "campo" => 'recibido', "sort" => false),
            array("nombre" => lang('entregado'), "campo" => 'entregado', "sort" => false)
        );
        return $columnas; 
    }


    
    private function crearColumnas() {
        $columnas = array(
            array("nombre" => ' ', "campo" => '', "sort" => false),
            array("nombre" => lang('nombre'), "campo" => 'nombre_apellido'),
            array("nombre" => lang('tipo_documento'), "campo" => 'documentos_tipos.nombre', "sort" => false),
            array("nombre" => lang('documento'), "campo" => 'alumnos.documento', "sort" => false),
            array("nombre" => lang('curso'), "campo" => 'nombre', "sort" => false),
            array("nombre" => lang('fecha_matricula'), "campo" => 'matriculas.fecha_emision'),
            array("nombre" => lang('titulo'), "campo" => 'titulo', "sort" => false),
            array("nombre" => lang('certifica'), "campo" => 'general.certificados_certificantes.nombre'),
            array("nombre" => lang('detalles'), "campo" => '', "sort" => false),
            array("nombre" => lang('estado'), "campo" => 'baja', "sort" => false, 'bVisible' => false),
            array("nombre" => lang('estado_certificado'), "campo" => 'certificados.estado', "sort" => false),
        );
        return $columnas;
    }

    public function index() {
        $data = array();
        $this->lang->load(get_idioma(), get_idioma());
        $valida_session = session_method();
        $filial = $this->session->userdata('filial');
        $codFilial = $filial['codigo'];
        $conexion = $this->load->database($codFilial, true);
        $myFilial = new Vfiliales($conexion, $codFilial);
        $data['certifica_ucel'] = $myFilial->certifica(2);
        $claves = array("validacion_ok", "finalizado", "en_proceso", "pendiente", "cancelado", "pendiente_impresion", "pendiente_aprobar", "acciones", "seleccione_certificado", "aprobar", "cancelar", "modificar", "HABILITAR", "cancelar_certificado");
        $data['lang'] = getLang($claves);
        $data['columns'] = $this->getColumns();
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'certificados/vista_certificados'; // pasamos la vista a utilizar como parámetro
        $data['seccion'] = $valida_session;
        $data['arrComisiones'] = Vcomisiones::listarComisiones($conexion);
        $data['arrCursos'] = Vcursos::getAllCursosDatatable($conexion);
        $this->load->view('container', $data);
    }

    public function listar() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $columnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['start']) ? $_POST['start'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['length']) ? $_POST['length'] : "";
        $arrFiltros["sSearch"] = isset($_POST['search']['value']) ? $_POST['search']['value'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $order = isset($_POST['order']) ? $_POST['order'] : array();
        foreach ($order as $value) {
            $arrFiltros["SortCol"] = $columnas[$value['column']]["campo"];
            $arrFiltros["sSortDir"] = $value['dir'];
        }
        $pestania = $this->input->post('pestania');
        $certificados = $this->Model_certificados->listarCertificadosDataTable($arrFiltros, $pestania, $separador);

        echo json_encode($certificados);
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    public function getDetalleRequerimientos() {
        $cod_matricula_periodo = $this->input->post('cod_matricula_periodo');
        $cod_certificante = $this->input->post('cod_certificante');

        $detalles = $this->Model_certificados->getDetalleRequerimientos($cod_matricula_periodo, $cod_certificante);

        echo json_encode($detalles);
    }

    public function frm_cambiar_detalles() {
        $this->load->library('form_validation');
        $arrcertificados = $this->input->post('certificados');
        $this->form_validation->set_rules('certificados', '', '');
        if ($this->form_validation->run() == FALSE){
            $errors = validation_errors();
            $data = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            for ($i = 0; $i < count($arrcertificados); $i++) {
                $datos = json_decode($arrcertificados[$i], true);
                $data['certificados'][$i] = $datos;
            }
            $claves = array("validacion_ok", "algun_campo_vacio");
            $data['langFrm'] = getLang($claves);
            $data['fechas']['fecha_inicio'] = '';
            $data['fechas']['fecha_fin'] = '';
            $data['certificacion'] = isset($_POST['certificacion']) ? $_POST['certificacion'] : '';
            if (count($arrcertificados) == 1) {
                $data['fechas'] = $this->Model_certificados->getPropiedadesFechasCertificado($data['certificados'][0]['cod_matricula_periodo'], $data['certificados'][0]['cod_certificante']);
            }
        }
        $this->load->view('certificados/frm_cambiar_detalles', $data);
    }

    public function guardarDetalles(){
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $arrcertificados = $this->input->post('certificados');
        $this->form_validation->set_rules('certificados', '', '');
        $this->form_validation->set_rules('fecha_inicio', '', '');
        $this->form_validation->set_rules('fecha_fin', '', '');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $datos = array('certificados' => $arrcertificados,
                'fecha_inicio' => $this->input->post('fecha_inicio'),
                'fecha_fin' => $this->input->post('fecha_fin'));
            $resultado = $this->Model_certificados->cambiarPropiedades($datos, $cod_usuario);
        }
        echo json_encode($resultado);
    }

    public function aprobarCertificados() {
        $this->load->library('form_validation');
        $arrcertificados = $this->input->post('certificados');
        $i = 1;
        foreach ($arrcertificados as $cerficado) {
            $datos = json_decode($cerficado, TRUE);
            $_POST['cod_matricula_periodo' . $i] = $datos['cod_matricula_periodo'];
            $_POST['cod_certificante' . $i] = $datos['cod_certificante'];
            $this->form_validation->set_rules('cod_matricula_periodo' . $i, lang('cod_matricula_periodo') . ' ' . lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('cod_certificante' . $i, lang('cod_certificante') . ' ' . lang('linea') . ' ' . $i, 'required|validarExistenciaEstadoCertificadoAprobar[' . $datos['cod_matricula_periodo'] . ']');
            $i++;
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $cod_usuario = $this->session->userdata('codigo_usuario');
            $resultado = $this->Model_certificados->aprobarCertificados($arrcertificados, $cod_usuario);
        }
        echo json_encode($resultado);
    }

    public function frm_certificado() {
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $claves = array('buscando', 'no_hay_resultados');
        $data['langFrm'] = getLang($claves);
        $this->load->view('certificados/frm_certificado', $data);
    }

    public function getCertificados() {
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_certificados_plan_filial", "", false, $arrConf);
        $cod_alumno = $this->input->post('cod_alumno');
        $data['certificados'] = $this->Model_certificados_plan_filial->getCertificadosCertificar($cod_alumno);
        echo json_encode($data);
    }

    public function guardarCertificado() {
        $this->load->library('form_validation');

        $arrcertificados = $this->input->post('certificados');
        $cod_alumno = $this->input->post('cod_alumno');

//        $i = 1;
//        foreach ($arrcertificados as $cerficado) {
//            $datos = json_decode($cerficado, TRUE);
//            $_POST['cod_plan_academico' . $i] = $datos['cod_plan_academico'];
//            $_POST['cod_tipo_periodo' . $i] = $datos['cod_tipo_periodo'];
//            $_POST['cod_certificante' . $i] = $datos['cod_certificante'];
//
//            $this->form_validation->set_rules('cod_plan_academico' . $i, 'cod_matricula_periodo ' . lang('linea') . ' ' . $i, 'required');
//            $this->form_validation->set_rules('cod_tipo_periodo' . $i, 'cod_tipo_periodo ' . lang('linea') . ' ' . $i, 'required');
//            $this->form_validation->set_rules('cod_certificante' . $i, 'cod_certificante ' . lang('linea') . ' ' . $i, 'required');
//            $i++;
//        }
        $this->form_validation->set_rules('certificados[]', lang('seleccione_curso'), 'required');
        $this->form_validation->set_rules('cod_alumno', lang('alumnos_cobro'), 'required');

        if ($this->form_validation->run() == FALSE) {

            $errors = validation_errors();

            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $datos['cod_usuario'] = $this->session->userdata('codigo_usuario');
            $datos['cod_alumno'] = $cod_alumno;
            $datos['certificados'] = $arrcertificados;
            $resultado = $this->Model_certificados->guardarCertificado($datos);
        }
        echo json_encode($resultado);
    }

    public function cancelarCertificados(){        
        $this->load->library('form_validation');
        $arrcertificados = $this->input->post('certificados');
        $i = 1;
        foreach ($arrcertificados as $cerficado) {
            $datos = json_decode($cerficado, TRUE);
            $_POST['cod_matricula_periodo' . $i] = $datos['cod_matricula_periodo'];
            $_POST['cod_certificante' . $i] = $datos['cod_certificante']; 
            $this->form_validation->set_rules('cod_matricula_periodo' . $i, lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('cod_certificante' . $i, lang('linea') . ' ' . $i, 'validarExistenciaEstadoCertificadoCancelar[' . $datos['cod_matricula_periodo'] . ']');
            $i++;
        }

        if (!$this->form_validation->run() == TRUE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $cod_usuario = $this->session->userdata('codigo_usuario');
            $resultado = $this->Model_certificados->cancelarCertificados($arrcertificados, $cod_usuario);
        }
        echo json_encode($resultado);
    }
    
    public function revertirCertificados(){        
        $this->load->library('form_validation');
        $arrcertificados = $this->input->post('certificados');
        $i = 1;
        foreach ($arrcertificados as $cerficado) {
            $datos = json_decode($cerficado, TRUE);
            $_POST['cod_matricula_periodo' . $i] = $datos['cod_matricula_periodo'];
            $_POST['cod_certificante' . $i] = $datos['cod_certificante']; 
            $this->form_validation->set_rules('cod_matricula_periodo' . $i, lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('cod_certificante' . $i, lang('linea') . ' ' . $i, 'validarExistenciaEstadoCertificadoRevertir[' . $datos['cod_matricula_periodo'] . ']');
            $i++;
        }

        if (!$this->form_validation->run() == TRUE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $cod_usuario = $this->session->userdata('codigo_usuario');
            $resultado = $this->Model_certificados->revertirCertificados($arrcertificados, $cod_usuario);
        }
        echo json_encode($resultado);
    }
    
    public function habilitarCertificados() {
        $this->load->library('form_validation');

        $arrcertificados = $this->input->post('certificados');

        $i = 1;
        foreach ($arrcertificados as $cerficado) {
            $datos = json_decode($cerficado, TRUE);
            $_POST['cod_matricula_periodo' . $i] = $datos['cod_matricula_periodo'];
            $_POST['cod_certificante' . $i] = $datos['cod_certificante'];

            $this->form_validation->set_rules('cod_matricula_periodo' . $i, lang('linea') . ' ' . $i, 'required');
            $this->form_validation->set_rules('cod_certificante' . $i, lang('linea') . ' ' . $i, 'required|validarExistenciaEstadoCertificadoHabilitar[' . $datos['cod_matricula_periodo'] . ']');
            $i++;
        }

        if ($this->form_validation->run() == FALSE) {

            $errors = validation_errors();

            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $cod_usuario = $this->session->userdata('codigo_usuario');
            $resultado = $this->Model_certificados->habilitarCertificados($arrcertificados, $cod_usuario);
        }
        echo json_encode($resultado);
    }

    /* esta funcion está siendo accedida desde un web services */

    public function get_certificados_pendientes() {
        if ($this->input->post("cod_filial")) {
            $this->load->model("Model_configuraciones", "", false, array("codigo_filial" => $this->input->post("cod_filial")));
            $arrResp = array();
            $this->load->helper("alumnos");
            $arrResp['certificados'] = $this->Model_certificados->getCertificadosPendientesImprimir($this->input->post("cod_filial"));
            $arrResp['configuracion']['NombreFormato'] = $this->Model_configuraciones->getValorConfiguracion(null, 'NombreFormato');
            $arrResp['configuracion']['NombreSeparador'] = $this->Model_configuraciones->getValorConfiguracion(null, 'NombreSeparador');
            echo json_encode($arrResp);
        } else {
            echo json_encode(array("error" => "error de transmicion", "estatus" => "error"));
        }
    }

    /* esta funcion está siendo accedida desde un web services */

    public function acusar_recibo_certificado_ws() {
        if ($this->input->post("cod_filial") && $this->input->post("arr_datos")) {
            $codFilial = $this->input->post("cod_filial");
            $arrDatos = $this->input->post("arr_datos");
            $arrResp = $this->Model_certificados->registrarCertificadoRecibidoWS($codFilial, $arrDatos);
            echo json_encode($arrResp);
        }
    }

    /* esta function está siendo accedida desde un web services */

    public function informar_certificados_finalizados() {
        if ($this->input->post("cod_filial") && $this->input->post("arrData") && is_array($this->input->post("arrData"))) {
            $codFilial = $this->input->post("cod_filial");
            $configMatriculas = array("filial" => array("codigo" => $codFilial));
            $this->load->model("Model_matriculas", "", false, $configMatriculas);
            $arrCertificados = $this->input->post("arrData");
            $arrResp = array();
            foreach ($arrCertificados as $certificado) {
                $codigo = $certificado['codigo_interno'];
                $codMatricula = $certificado['cod_matricula'];
                $codTipoPeriodo = $certificado['cod_tipo_periodo'];
                $codCertificante = $certificado['cod_certificante'];
                $codMatriculaPeriodo = $this->Model_matriculas->getCodigoMatriculaPeriodo($codMatricula, $codTipoPeriodo);
                if ($this->Model_certificados->informar_certificados_finalizados($codFilial, $codMatriculaPeriodo, $codCertificante)) {
                    $arrResp[$codigo] = 'success';
                } else {
                    $arrResp[$codigo] = 'error';
                }
            }
            echo json_encode($arrResp);
        }
    }

    /* esta function está siendo accedida desde un web services */

    public function get_estado_certificados() {
        if ($this->input->post("cod_filial") && $this->input->post("arrData")) {
            $codFilial = $this->input->post("cod_filial");
            $configMatriculas = array("filial" => array("codigo" => $codFilial));
            $this->load->model("Model_matriculas", "", false, $configMatriculas);
            $arrResp = array();
            foreach ($this->input->post("arrData") as $certificado) {
                $codMatricula = $certificado['cod_matricula'];
                $codCertificante = $certificado['cod_certificante'];
                $codTipoPeriodo = $certificado['cod_tipo_periodo'];
                $codigo = $certificado['codigo_interno'];
                $codMatriculaPeriodo = $this->Model_matriculas->getCodigoMatriculaPeriodo($codMatricula, $codTipoPeriodo);
                $arrResp[$codigo] = $this->Model_certificados->get_estado_certificados($codFilial, $codMatriculaPeriodo, $codCertificante);
            }
            echo json_encode($arrResp);
        }
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, ELIMINAR NI MODIFICAR */

    public function reinsertar_certificados() {
        $arrResp = array();
        if ($this->input->post("cod_matricula") && $this->input->post("cod_filial") && $this->input->post("cod_tipo_periodo") && $this->input->post("cod_certificante")) {
            $codFilial = $this->input->post("cod_filial");
            $codMatricula = $this->input->post("cod_matricula");
            $codTipoPeriodo = $this->input->post("cod_tipo_periodo");
            $codCertificante = $this->input->post("cod_certificante");
            $conexion = $this->load->database($codFilial, true);
            $conexion->trans_begin();
            $myMatricula = new Vmatriculas($conexion, $codMatricula);
            if ($myMatricula->reinsertarCertificado($codTipoPeriodo, $codCertificante)) {
                $arrResp['success'] = "success";
                $arrResp['cod_filial'] = $codFilial;
                $arrResp['cod_tipo_periodo'] = $codTipoPeriodo;
                $arrResp['cod_matricula'] = $codMatricula;
                $arrResp['cod_certificante'] = $codCertificante;
                $conexion->trans_commit();
            } else {
                $arrResp['error'] = "[" . $conexion->_error_number() . "] " . $conexion->_error_message();
                $conexion->trans_rollback();
            }
        } else {
            $arrResp['error'] = "Wrong parameters";
        }
        echo json_encode($arrResp);
    }

    public function errores_migracion_certificados_1() {
        $arrResp = $this->Model_certificados->errores_migracion_certificados_1();

        echo json_encode($arrResp);
    }

    public function errores_migracion_certificados_vista() {
        $arrResp = $this->Model_certificados->errores_migracion_certificados_vista();

        echo json_encode($arrResp);
    }

    public function errores_migracion_certificados_2() {
        $arrResp = $this->Model_certificados->errores_migracion_certificados_2();

        echo json_encode($arrResp);
    }

    function listar_certificaciones(){
        $columnas = $this->crearColumnas2();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros['SortCol'] = isset($_POST['iSortCol_0']) ? $columnas[$_POST['iSortCol_0']]['campo'] : null;
        $arrFiltros['sSortDir'] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : null;
        $order = isset($_POST['order']) ? $_POST['order'] : array();
        foreach ($order as $value) {
            $arrFiltros["SortCol"] = $columnas[$value['column']]["campo"];
            $arrFiltros["sSortDir"] = $value['dir'];
        }
        $estado = isset($_POST['estado']) && $_POST['estado'] <> -1 ? $_POST['estado'] : null;
        if ($estado == 'pendiente_impresion'){ // cambio pedido por agustina (quitar de la vista el filtro en proceso y que este sea lo mismo que pendiente_impresion)
            $estado = array("pendiente_impresion", "en_proceso");
        }
        $comision = isset($_POST['comision']) && $_POST['comision'] <> -1 ? $_POST['comision'] : null;
        $curso = isset($_POST['curso']) && $_POST['curso'] <> -1 ? $_POST['curso'] : null;
        $certificante = isset($_POST['certificante']) &&$_POST['certificante'] <> -1 ? $_POST['certificante'] : null;
        $arrCertificados = $this->Model_certificados->listarCertificados2DataTable($arrFiltros, $estado, $certificante, false, $comision, $curso);
        echo json_encode($arrCertificados);
    }
    
    function cambiar_estado_entregado(){
        $arrResp = array();
        $filial = $this->session->userdata('filial');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $conexion = $this->load->database($filial["codigo"], true);
        $cod_matricula_periodo = $this->input->post("cod_matricula_periodo");
        $cod_certificante = $this->input->post("cod_certificante");
        $estado = $this->input->post("estado");
        $myCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        if ($estado == 1){
            $resp = $myCertificado->setEntregado($cod_usuario);
        } else {
            $resp = $myCertificado->unsetEntragado($cod_usuario);
        }
        if ($resp){
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "error";
            $arrResp['msg'] = "Error al marcar el certificado como entragado";
            $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
    
    function cambiar_estado_recibido(){
        $arrResp = array();
        $filial = $this->session->userdata('filial');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $conexion = $this->load->database($filial['codigo'], true);
        $cod_matricula_periodo = $this->input->post("cod_matricula_periodo");
        $cod_certificante = $this->input->post("cod_certificante");
        $estado = $this->input->post("estado");
        $myCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        if ($estado == 1){
            $resp = $myCertificado->setRecibido($cod_usuario);
        } else {
            $resp = $myCertificado->unsetRecibido($cod_usuario);
        }
        if ($resp){
            $arrResp['success'] = "success";
        } else {
           $arrResp['error'] = "error";
           $arrResp['msg'] = "Error al marcar el certificado como recibido";
           $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
    
    function getMaxCodigoPedido(){
        $arrResp = array();
        $codFilial = $this->input->post("id_filial");
        if ($codFilial){
            $conexion = $this->load->database($codFilial, true);
            $codPedido = Vcertificados::getMaxCodigoPedido($conexion);
            $arrResp['cod_filial'] = $codFilial;
            $arrResp['id_producto_pedido'] = $codPedido;
        } else {
            $arrResp['error'] = "Error de parámetros";
        }
        echo json_encode($arrResp);
    }
    
    function set_codigo_producto_pedido_certificado(){
        $arrResp = array();
        $codFilial = $this->input->post("id_filial", true);
        $certificados = $this->input->post("certificados");
        if ($codFilial && $certificados && is_array($certificados) && count($certificados) > 0){
            $conexion = $this->load->database($codFilial, true);
            $conexion->trans_begin();
            foreach ($certificados as $certificado){
                $cod_producto_pedido = $certificado['cod_producto_pedido'];
                if (isset($certificado['cod_producto_pedido'])){
                    $cod_tipo_periodo = $certificado['cod_tipo_periodo'];
                    $cod_matricula = $certificado['cod_matricula'];
                    $cod_certificante = $certificado['cod_certificante'];
                    $myMatricula = new Vmatriculas($conexion, $cod_matricula);
                    $arrPeriodos = $myMatricula->getPeriodosMatricula(null, $cod_tipo_periodo);
                    if (isset($arrPeriodos[0]) && isset($arrPeriodos[0]['codigo'])){
                        $myCertificado = new Vcertificados($conexion, $arrPeriodos[0]['codigo'], $cod_certificante);
                        $myCertificado->id_producto_pedido = $cod_producto_pedido;
                        if (!$myCertificado->guardarCertificados()){
                            $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                            $conexion->trans_rollback();
                            echo json_encode($arrResp);
                            die();
                        }
                    } else {
                        $arrResp['error'] = "No se encuentra la matricula_periodo para matricula $cod_matricula y cod_tipo_periodo $cod_tipo_periodo";
                        $conexion->trans_rollback();
                        echo json_encode($arrResp);
                        die();
                    }
                }
            }
            $conexion->trans_commit();
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "Error en parametros";
        }
        echo json_encode($arrResp);
    }
    
    function habilitarCertificadosCancelados(){
        $arrResp = array();
        $certificados = $this->input->post('certificados');
        if ($certificados && is_array($certificados) && count($certificados) > 0){
            $filial = $this->session->userdata('filial');
            $conexion = $this->load->database($filial['codigo'], true);
            $cod_usuario = $this->session->userdata('codigo_usuario');
            $procesados = array();
            $conexion->trans_begin();
            foreach ($certificados as $certificado){
                $arrTemp = json_decode($certificado, true);
                $cod_matricula_periodo = $arrTemp['cod_matricula_periodo'];
                $cod_certificante = $arrTemp['cod_certificante'];
                $myCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
                if ($myCertificado->estado == Vcertificados::getEstadoCancelado()){
                    $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
                    if ($myMatriculaPeriodo->estado == Vmatriculas_periodos::getEstadoFinalizada()){
                        $myCertificado->setPendienteAprobar($cod_usuario, date("Y-m-d H:i:s"));
                        $procesados[] = array("cod_matricula_periodo" => $cod_matricula_periodo, "cod_certificante" => $cod_certificante);
                    } else if ($cod_certificante == 1 && $myMatriculaPeriodo->estado == Vmatriculas_periodos::getEstadoHabilitada()){
                        $myCertificado->setPendiente($cod_usuario, date("Y-m-d H:i:s"));
                        $procesados[] = array("cod_matricula_periodo" => $cod_matricula_periodo, "cod_certificante" => $cod_certificante);
                    } else if ($cod_certificante == 2 && (Vmatriculas_periodos::getEstadoHabilitada() || Vmatriculas_periodos::getEstadoCertificada())){
                        $myCertificado->setPendienteAprobar($cod_usuario, date("Y-m-d H:i:s"));
                        $procesados[] = array("cod_matricula_periodo" => $cod_matricula_periodo, "cod_certificante" => $cod_certificante);
                    } else {
                        $conexion->trans_rollback();
                        $arrResp['codigo'] = "0";
                        $arrResp['respuesta'] = lang("alguno_de_los_certificados_seleccionados_pertenecen_a_matriculas_no_finalizadas");
                        echo json_encode($arrResp);
                        die();
                    }
                } else {
                    $conexion->trans_rollback();
                    $arrResp['codigo'] = "0";
                    $arrResp['respuesta'] = lang("selecciones_solo_certificados_cancelados");
                    echo json_encode($arrResp);
                    die();
                }
            }
            if ($conexion->trans_status()){
                $conexion->trans_commit();
                $arrResp['codigo'] = "1";
                $arrResp['success'] = "success";
                $arrResp['procesados'] = $procesados;
            } else {
                $arrResp["codigo"] = "0";
                $arrResp['respuesta'] = lang("error_al_procesar_certificados")."<br>".lang("vuelva_a_intentar_mas_tarde");
                $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                $conexion->trans_rollback();
            }
        } else {
            $arrResp['codigo'] = "0";
            $arrResp['respuesta'] = lang('seleccione_certificado');
        }
        echo json_encode($arrResp);
    }
    
}