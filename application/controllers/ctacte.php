<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ctacte extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_ctacte", "", false, $config);
    }

    /**
     * retorna lista de ctacte por alumno para mostrar en index de main panel
     * @access public
     * @return json de listado de cuentas corrientes
     */
    public function index() {
        $this->load->helper("alumnos");
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $data['titulo_pagina'] = '';
        $data['page'] = 'resumenCuenta/vista_resumencuenta';

        $claves = array(
            "habilitado_curso", "codigo", "debe_ctacte", "importe_sin_descuento", "estadoctacte", "estado", "fechas_cambiadas_correctamente",
            "hasta", "perdio_descuento", "no_se_ha_podido_calcular_el_total", "refinanciacion_guardada_correctamente", "debe_indicar_la_cantidad_de_cuotas", 
            "debe_indicar_un_porcentaje_valido",  "debe_indicar_por_lo_menos_un_items_para_financiar", "la_fecha_para_el_primer_pago_no_es_valida",
            'consaldo', 'todas', "sinsaldo", "filtrar", "fecha", "validacion_ok", "tipo", "importe", "numero", "medio_de_pago", 'habilitadas', 'deuda_pasiva', 'inhabilitada', 'no_debe_ctacte',
            'paga_al_momento', 'concepto', 'financiacion', 'fecha_primer_pago', 'seleccione_financiacion', 'paga_al_momento', "condicionado", "no_condicionado",
            "tipo_de_descuento", "porcentaje", "fecha_hasta_", "importe_final", "ver_detalle", "representan_descuentos_del_plan_de_pago",
            "quitar_descuento_condicionado", "reactivar_descuento_condicionado"
        );

        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('ctacte');
        $data['columns'] = $this->getColumns();
        $data['seccion'] = $this->seccion;
        $idioma = $this->session->userdata("idioma");
        $data['condicionado_perdido'] = Vmatriculaciones_ctacte_descuento::getDescuentosMatricula($conexion, $idioma, Vmatriculaciones_ctacte_descuento::getEstadoCondicionadoPerdido(), true, null, true);
        $this->load->view('container', $data);
    }

    public function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'alumnos.codigo'),
            array("nombre" => lang('seleccionar_ctacte'), "campo" => 'seleccionar', 'bVisible' => false),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('saldo_deudor_sin_mora'), "campo" => 'saldo'),
            array("nombre" => lang('proximo_vencimiento'), "campo" => 'proxvenc'),
            array("nombre" => lang('estadoctacte'), "campo" => 'estadoctacte', 'sort' => FALSE),
            array("nombre" => lang('debe_ctacte'), "campo" => 'debe', 'bVisible' => false)
        );
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    public function listar() {
        $crearColumnas = $this->crearColumnas();
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $arrFiltros["debe"] = $this->input->post('debe');
        $valores = $this->Model_alumnos->listarAlumnosDatatableCtaCte($arrFiltros, $separador, $separadorDecimal);
        echo json_encode($valores);
    }

    /**
     * llama a cargar formulario del un alumno con su cta cte
     * @access public
     * @return 
     */
    public function frm_ctacte() {
        $this->load->library('user_agent');
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_conceptos", "", false, $config);
        $this->load->model("Model_configuraciones", "", false, $config);
        $cod_alumno = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        $data = array();
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_alumnos", "", false, $config);
            $ctacte = $this->Model_alumnos->getCtaCteFrm($cod_alumno,null);
            $data['alumno'] = $this->Model_alumnos->getAlumno($cod_alumno);
            $data['cod_alumno'] = $cod_alumno;
            $data['seccion'] = $this->seccion;
            $data['ctacte'] = $ctacte;
            $data['nombre_apellido'] = $this->Model_alumnos->getNombreAlumno($cod_alumno);
            $data['codigo'] = 1;
            $data['conceptos'] = $this->Model_conceptos->getConceptosUsuario();
            $data['periodicidad'] = $this->Model_configuraciones->getPeriodicidad();
            if ($this->agent->referrer() == base_url() . 'alumnos') {//SI INTENTAN ACCEDER DESDE ALUMNOS SE DEVUELVE ESTE MSJ YA QUE EL SIETEMA LO LO PERMITE POR AHORA
                echo lang('seccion_deshabilitada');
            } else {
                echo json_encode($data);
            }
        }
    }

    public function frm_nueva_ctacte() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_conceptos", "", false, $config);
        $this->load->model("Model_configuraciones", "", false, $config);
        $cod_alumno = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        $data = array();
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_alumnos", "", false, $config);
            $data['alumno'] = $this->Model_alumnos->getAlumno($cod_alumno);
            $data['cod_alumno'] = $cod_alumno;
            $data['codigo'] = 1;
            $data['conceptos'] = $this->Model_conceptos->getConceptos(false);
            $data['conceptos'] = $this->Model_conceptos->getConceptosUsuario();
            $data['matriculas'] = $this->Model_alumnos->getMatriculas($cod_alumno);
            $data['periodicidad'] = $this->Model_configuraciones->getPeriodicidad();
            $this->load->view('resumenCuenta/frm_nueva_ctacte', $data);
        }
    }

    /**
     * Retorna un array con las imputaciones realizadas a un linea de cuenta corriente
     * codigo de ctacte
     * @access public
     * @return array imputaciones
     */
    public function getImputaciones_facturas() {
        $cod_ctacte = $this->input->post('cod_ctacte');
        $imputaciones = $this->Model_ctacte->getImputaciones($cod_ctacte, null, false);
        echo json_encode($imputaciones);
    }

    public function getFacturas() {
        $cod_ctacte = $this->input->post('cod_ctacte');
        $facturasCtaCte = $this->Model_ctacte->getFacturas($cod_ctacte);
        if (count($facturasCtaCte) > 0) {
            $respuesta = array(
                "codigo" => 1,
                "facturas" => $facturasCtaCte);
        } else {
            $respuesta = array(
                "codigo" => 0,
                "msgerrors" => lang('ctacte_sin_facturas_emitidas'));
        }
        echo json_encode($respuesta);
    }

    /**
     * Retorna un array con las moras asignadas a una cuenta corriente
     * codigo de ctacte
     * @access public
     * @return array moras
     */
    public function getDescMora() {
        $cod_ctacte = $this->input->post('codigo_ctacte');
        $moras = $this->Model_ctacte->getMoras($cod_ctacte);
        echo json_encode($moras);
    }

    /**
     * Carga formulario de los comentarios de un registro de  cuenta corriente
     * @access public
     * @return 
     */
    public function frm_comentarios() {
        $this->load->library('form_validation');
        $cod_ctacte = $this->input->post('cod_ctacte');
        $this->form_validation->set_rules('cod_ctacte', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $claves = array("validacion_ok", "ERROR", "eliminacion_ok", "sin_comentarios");
            $data['langFrm'] = getLang($claves);
            $data['codigo_ctacte'] = $cod_ctacte;
            $data['comentarios'] = $this->Model_ctacte->getComentarios($cod_ctacte);
            $this->load->view('resumenCuenta/frm_comentarios', $data);
        }
    }

    public function guardarComentario() {
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $data_post = array();
        $this->form_validation->set_rules('comentario', lang('ctacte_comentarios_comentario'), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            $data_post['codigo'] = $this->input->post('codigo');
            $data_post['comentario'] = $this->input->post('comentario');
            $data_post['cod_ctacte'] = $this->input->post('cod_ctacte');
            $data_post['cod_usuario'] = $usuario;
        }
        $resultado = $this->Model_ctacte->guardarComentario($data_post);
        echo json_encode($resultado);
    }

    public function bajaComentario() {
        $data_post = array();
        $data_post['ctacte'] = $this->input->post('ctacte');
        $data_post['codigo'] = $this->input->post('codigo');
        $data_post['filial'] = $this->session->userdata('filial');
        $resultado = $this->Model_ctacte->bajaComentario($data_post);
        echo json_encode($resultado);
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getReporteCobros() {

        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $idCurso = isset($_POST['id_curso']) ? $_POST['id_curso'] : null;
        $idConcepto = isset($_POST['id_concepto']) ? $_POST['id_concepto'] : null;
        $soloConDeuda = isset($_POST['solo_con_deuda']) && $_POST['solo_con_deuda'] == 1 ? true : null;
        $config = array("codigo_filial" => $idFilial);
        $this->load->model("Model_ctacte", "", false, $config);
        if (!isset($_POST['estado'])){
            $estado = array(1);
        } else if ($_POST['estado'] == "todos"){
            $estado = array(1, 2, 0);
        } else if ($_POST['estado'] == 'habilitada'){
            $estado = array(1);
        } else if ($_POST['estado'] == 'pasiva'){
            $estado = array(2);
        } else if ($_POST['estado'] == 'inhabilitada'){
            $estado = array(0);
        } else if ($_POST['estado'] == 'habilitada_pasiva'){
            $estado = array(1, 2);
        } else {
            $estado = array(1);
        }        
        $arrResp = $this->Model_ctacte->getReporteCobros($idFilial, $arrLimit, $arrSort, $search, $searchField, $fechaDesde, $fechaHasta, $idCurso, $idConcepto, $soloConDeuda, $estado);
        echo json_encode($arrResp);
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function getEstadisticasCobro() {
        $idFilial = $_POST['id_filial'];
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $idCurso = isset($_POST['id_curso']) ? $_POST['id_curso'] : null;
        $idConcepto = isset($_POST['id_concepto']) ? $_POST['id_concepto'] : null;
        $estado = null;
        if (isset($_POST['estado'])){
            if ($_POST['estado'] == "todos"){
                $estado = array(1, 2, 0);
            } else if ($_POST['estado'] == 'habilitada'){
                $estado = array(1);
            } else if ($_POST['estado'] == 'pasiva'){
                $estado = array(2);
            } else if ($_POST['estado'] == 'inhabilitada'){
                $estado = array(0);
            } else if ($_POST['estado'] == 'habilitada_pasiva'){
                $estado = array(1, 2);
            }
        }
        $config = array("codigo_filial" => $idFilial);
        $this->load->model("Model_ctacte", "", false, $config);
        $arrResp = $this->Model_ctacte->getEstadisticasCobro($idFilial, $fechaDesde, $fechaHasta, $idCurso, $idConcepto, $estado);
        echo json_encode($arrResp);
    }

    public function frm_refinanciar() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->helper("alumnos");
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $cod_alumno = $this->input->post('codigo_alumno');
        $matriculas = $this->input->post('refinancia') != false ? $this->input->post('refinancia') : null;
        $this->form_validation->set_rules('codigo_alumno', lang('codigo'), 'numeric');
        $this->form_validation->set_rules('refinancia[]', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $objAlumno = $this->Model_alumnos->getAlumno($cod_alumno);
            $data['matriculas'] = $matriculas;
            $data['periodicidad'] = $this->Model_configuraciones->getPeriodicidad();
            $data["conceptos"] = $this->Model_alumnos->getConceptosCtaCteDebe($cod_alumno, true, $matriculas);
            $data['alumno'] = $objAlumno;
            $data["nombreAlumno"] = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
            $this->load->view('resumenCuenta/frm_refinanciar', $data);
        }
    }

    public function getCtaCteRefinanciar() {
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $cod_alumno = $this->input->post('codigo_alumno');
        $cod_concepto = $this->input->post('codigo_concepto');
        $concepto = $this->input->post('concepto');
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $condiciones = array('cod_concepto' => $cod_concepto,
            'concepto' => $concepto,
            'habilitado >' => 0,
            'habilitado <' => 3,
            'pagado' => 0);
        $resultado = $this->Model_alumnos->getCtaCte($cod_alumno, $condiciones, TRUE);
        echo json_encode($resultado);
    }

    public function getSumaRefinanciar() {
        $rowsCtaCte = $this->input->post('codigo_ctacte');
        $resultado = $this->Model_ctacte->getSumaRefinanciar($rowsCtaCte);
        echo json_encode($resultado);
    }

    public function getDetalleRefinanciacion() {
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->library('form_validation');
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->helper("filial");
        $this->form_validation->set_rules('cuotas', lang(''), 'required|numeric');
        $this->form_validation->set_rules('ctacte[]', lang(''), 'required');
        $this->form_validation->set_rules('fechaPrimerPago', lang(''), 'validarFechaFormato');
        $this->form_validation->set_rules("periodicidad", lang(''), "required|numeric");
        $resultado = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            $datos = array();
            $datos['detalle']['decimales'] = 2;
            $datos['detalle']['perioricidad'] = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte', null, $this->input->post('periodicidad'));
            $datos['cuotas'] = $this->input->post('cuotas');
            $datos['ctactes'] = $this->input->post('ctacte');
            $datos['valor_refinanciar'] = $this->input->post('valor_refinanciar');
            $interesPorc = $this->input->post('interesporc');
            if ($this->input->post("porcentaje_aplica") == "descuento") {
                $interesPorc = 0 - $interesPorc;
            }
            $datos['interesporc'] = $interesPorc;
            $datos['fechapago'] = formatearFecha_mysql($this->input->post('fechaPrimerPago'));
            $resultado = $this->Model_ctacte->getDetalleRefinanciacion($datos);
        }
        echo json_encode($resultado);
    }

    public function guardarRefinanciacion() {
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->library('form_validation');
        $this->load->helper("filial");
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $resultado = '';
        $this->form_validation->set_rules('cuotas', lang('cantidad_de_cuotas'), 'required|numeric');
        $this->form_validation->set_rules('ctacte[]', lang(''), 'required|validaCtaCteRefinanciar');
        $this->form_validation->set_rules('fechaPrimerPago', lang('fecha_primer_pago'), 'validarFechaFormato');
        $this->form_validation->set_rules("periodicidad", lang('planpago_periodo'), "required|numeric");
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            $datos['detalle']['cuotas'] = $this->input->post('cuotas');
            $datos['detalle']['ctactes'] = $this->input->post('ctacte');
            $datos['detalle']['valor_refinanciar'] = $this->input->post('valor_refinanciar');
            $interesPorc = $this->input->post('interesporc');
            if ($this->input->post("porcentaje_aplica") == "descuento") {
                $interesPorc = 0 - $interesPorc;
            }
            $datos['detalle']['interesporc'] = $interesPorc;
            $datos['detalle']['fechapago'] = formatearFecha_mysql($this->input->post('fechaPrimerPago'));
            $datos['detalle']['detalle']['perioricidad'] = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte', null, $this->input->post('periodicidad'));
            $datos['alumno'] = $this->input->post('alumno');
            $datos['codconcepto'] = $this->input->post('codconcepto');
            $datos['concepto'] = $this->input->post('concepto');
            $datos['detalle']['detalle']['decimales'] = 2;
            $datos['cod_usuario'] = $this->session->userdata('codigo_usuario');
            $resultado = $this->Model_ctacte->guardarRefinanciacion($datos);
        }
        echo json_encode($resultado);
    }

    /* formulario avisos deudores
     * para avisar las deudas de las ctacte del alumno
     * @acces public
     * @return
     */
    public function frmCtaCteAlumno() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $mostrar = $this->input->post('mostrar');
        $wherein = $this->input->post('cod_alumno');
        $this->form_validation->set_rules('cod_alumno[]', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $DiasAlertasConfiguracion = $this->Model_configuraciones->getValorConfiguracion(null, 'CantAlertasDeudores');
            $data['deudoresCtaCte'] = $this->Model_ctacte->getDeudoresCtaCte($wherein, $DiasAlertasConfiguracion, $separador);
            $data['CantAlertasConfiguracion'] = $DiasAlertasConfiguracion;
            if ($mostrar == 1) {
                $this->load->view('resumenCuenta/frm_detalleDeudor', $data);
            } else {
                echo json_encode($data);
            }
        }
    }

    public function crearColumnasAvisoDeudores() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $crearColumnas = array(
            array("nombre" => lang('seleccionar_ctacte'), "campo" => 'seleccionar', 'sort' => false),
            array("nombre" => lang('codigo'), "campo" => 'codigo', 'bVisible' => false),
            array("nombre" => lang('codigo'), "campo" => 'cod_alumno', 'bVisible' => false),
            array("nombre" => $nombreApellido, "campo" => 'nombre'),
            array("nombre" => lang('saldo_deudor'), "campo" => 'deudaTotal'),
            array("nombre" => lang('proximo_vencimiento'), "campo" => 'fechavenc'),
            array("nombre" => lang('alertar'), "campo" => 'alertar', 'bVisible' => false),
            array("nombre" => lang('detalle'), "campo" => 'detalle', 'sort' => false),
        );
        return $crearColumnas;
    }

    public function frmAvisodeudores() {
        $this->load->helper("datatables");
        $clavesFRM = Array("descripcion", "importe", "saldo", "ver_detalle", "fecha_vencimiento", "alertar", "enviar_alerta", "enviado_correctamente", "ok", "upps");
        $data['langFrm'] = getLang($clavesFRM);
        $data['columnas'] = json_encode(getColumnsDatatable($this->crearColumnasAvisoDeudores()));
        $this->load->view('resumenCuenta/frm_avisoDeudores', $data);
    }

    public function listarAvisoDeudores() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $crearColumnasAviso = $this->crearColumnasAvisoDeudores();
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $diasAlertaConfigurcion = $this->Model_configuraciones->getValorConfiguracion(null, 'CantAlertasDeudores');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnasAviso[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $deudores = $this->Model_ctacte->deudoresAgrupadosCtaCte($arrFiltros, $diasAlertaConfigurcion, $separador);
        echo json_encode($deudores);
    }

    public function guardarAlertasDeudor() {
        session_method();
        $this->load->library('form_validation');
        $arrayCtactes = $this->input->post('ctacte');
        $usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $this->form_validation->set_rules('ctacte', lang('ctacte_alerta'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $resultado = $this->Model_ctacte->guardarAlertasDeudores($arrayCtactes, $usuario);
        }
        echo json_encode($resultado);
    }

    public function guardarAlertasDeudoresGeneral() {
        session_method();
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $resultado = '';
        $this->form_validation->set_rules('ctacte', lang('ctacteDeudor'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $arrayCtactes = $this->input->post('ctacte');
            $diasAlertaConfigurcion = $this->Model_configuraciones->getValorConfiguracion(null, 'CantAlertasDeudores');
            $resultado = $this->Model_ctacte->guardarAlertasDeudoresGeneral($diasAlertaConfigurcion, $arrayCtactes, $usuario, $separador);
        }
        $this->load->model("Model_alertas", "", false, $config);
        $this->Model_alertas->enviarAlertasAlumnosConDeuda($config['codigo_filial']);

        echo json_encode($resultado);
    }

    /**
     * llama a cargar formulario para cambio de vencimiento de la ctacte
     * @access public
     * @return 
     */
    public function frm_cambio_vencimiento() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->helper("alumnos");
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $cod_alumno = $this->input->post('codigo_alumno');
        $this->form_validation->set_rules('codigo_alumno', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            print_r($errors);
        } else {
            $objAlumno = $this->Model_alumnos->getAlumno($cod_alumno);
            $periodos = $this->Model_configuraciones->getValorConfiguracion(1);
            foreach ($periodos as $key => $periodo) {
                if ($periodo['baja'] == 1) {
                    unset($periodos[$key]); //elimina los indices donde la baja sea 1.
                }
            }
            $nombreApellido = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
            $data['periodicidad'] = $this->Model_configuraciones->traducirPeriodos($periodos);
            $data['alumno'] = $this->Model_alumnos->getAlumno($cod_alumno);
            $data["nombreAlumno"] = inicialesMayusculas($nombreApellido);
            $data['conceptos'] = $this->Model_alumnos->getConceptosCtaCteDebe($cod_alumno, true);
            $this->load->view('resumenCuenta/frm_cambio_vencimiento', $data);
        }
    }

    public function getCtaCteCambioVencimiento() {
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $cod_alumno = $this->input->post('codigo_alumno');
        $cod_concepto = $this->input->post('codigo_concepto');
        $concepto = $this->input->post('concepto');
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $respuesta = $this->Model_alumnos->getCtaCteCambioVenc($cod_alumno, $cod_concepto, $concepto);
        if (isset($_POST['cambiar_fechas_vencimiento']) && $_POST['cambiar_fechas_vencimiento'] == 1) {
            $this->load->model("Model_configuraciones", "", false, $arrConf);
            $codigo_ctacte = $this->input->post("codigo_ctacte");
            $fecha = formatearFecha_mysql($this->input->post("fecha"));
            $periodicidad = $this->input->post("periocidad");
            $respuesta = $this->Model_ctacte->cambiarFechasVencimiento($respuesta, $fecha, $codigo_ctacte, $periodicidad);
        }
        echo json_encode($respuesta);
    }

    public function guardarCambioVencimiento() {
        $this->load->library('form_validation');
        $resultado = '';
        $data_post = array();
        $this->form_validation->set_rules('codigo_ctacte[]', lang('codigo_ctacte'), 'required');
        foreach ($_POST["fechas"] as $key => $fechavalidar) {
            $_POST["fecha_validar_$key"] = $fechavalidar;
            $this->form_validation->set_rules("fecha_validar_$key", "fecha_validar_$key", 'required|validarFechaHabil');
        }
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            $data_post['ctacte'] = $this->input->post('codigo_ctacte');
            foreach ($this->input->post('fechas') as $fecha) {
                $data_post['fechas'][] = formatearFecha_mysql($fecha);
            }
            $resultado = $this->Model_ctacte->guardarCambioVencimiento($data_post);
        }
        echo json_encode($resultado);
    }

    public function frm_ctactePagas() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $cod_alumno = $this->input->post('codigo_alumno');
        $this->form_validation->set_rules('codigo_alumno', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_alumnos", "", false, $config);
            $this->load->model("Model_configuraciones", "", false, $config);
            $data['objalumno'] = $this->Model_alumnos->getAlumno($cod_alumno);
            $data['nombreFormateado'] = $this->Model_alumnos->getNombreAlumno($cod_alumno);
            $data['ctacteAlumno'] = $this->Model_ctacte->getCtaCtePagas($cod_alumno);
            $data['moneda'] = $filial["moneda"];
            $data['separador'] = $this->Model_configuraciones->getValorConfiguracion(null, 'SeparadorDecimal');
            $data['decimales'] = 2;
            $this->load->view('resumenCuenta/frm_ctactePagas', $data);
        }
    }

    public function guardarNotaCredito() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $separador = $this->Model_configuraciones->getValorConfiguracion(null, 'SeparadorDecimal');
        $this->load->model("Model_cobros", "", false, $config);
        $usuario = $this->session->userdata('codigo_usuario');
        $ctactecheck = $this->input->post('codigoImputar') ? $this->input->post('codigoImputar') : array();
        $resultado = '';
        $data_post = array();
        $arrayValorCtaCte = $this->input->post('valorImputar') ? $this->input->post('valorImputar') : array();
        foreach ($arrayValorCtaCte as $key => $valor) {
            $_POST['Valorctacte' . $key] = $valor;
            $codigo_ctacte = $ctactecheck[$key];
            $this->form_validation->set_rules('Valorctacte' . $key, 'input' . $key, 'validarExpresionTotal|validarSaldoNotaCredito[' . $codigo_ctacte . ']');
        }
        $this->form_validation->set_rules('motivo', lang('motivo_cobrar_notacredito'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $total = 0;
            foreach ($arrayValorCtaCte as $value) {
                $valor = str_replace($separador, '.', $value);
                $total = $total + $valor;
            }
            $retorno = $total;
            $data_post['cobrar']['codigo'] = -1;
            $data_post['cobrar']['fecha_cobro'] = date('Y-m-d');
            $data_post['cobrar']['cod_alumno'] = $this->input->post('cod_alumno');
            $data_post['cobrar']['medio_cobro'] = 5;
            $data_post['cobrar']['cod_usuario'] = $usuario;
            $data_post['cobrar']['caja'] = 2;
            $data_post['cobrar']['total_cobrar'] = $retorno;
            $data_post['cobrar']['checkctacte'] = array();
            $data_post['cobrar']['estado'] = '1';
            $arrayCtaCte = $this->input->post('codigoImputar');
            $data_post['medio_cobro']['motivo'] = $this->input->post('motivo');
            $resultado = $this->Model_cobros->guardarNotaCredito($arrayCtaCte, $arrayValorCtaCte, $data_post);
        }
        echo json_encode($resultado);
    }

    public function calcularTotal() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->load->helper("filial");
        $separador = $this->Model_configuraciones->getValorConfiguracion(null, 'SeparadorDecimal');
        $arrValores = $this->input->post('valorImputar') ? $this->input->post('valorImputar') : array();
        $ctactecheck = $this->input->post('codigoImputar') ? $this->input->post('codigoImputar') : array();
        $retornar = '';
        if (!$this->input->post('no_verificar_para_nota')) {
            foreach ($arrValores as $key => $valor) {
                $_POST['Valorctacte' . $key] = $valor;
                $codigo_ctacte = $ctactecheck[$key];
                $this->form_validation->set_rules('Valorctacte' . $key, 'input' . $key, 'validarExpresionTotal|validarSaldoNotaCredito[' . $codigo_ctacte . ']');
            }
        }
        if (!$this->input->post('no_verificar_para_nota') && $this->form_validation->run() == false) {
            $errors = validation_errors();
            $retornar = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $total = 0;
            foreach ($arrValores as $value) {
                $valor = str_replace($separador, '.', $value);
                $total = $total + $valor;
            }
            $retorno = $total;
            $totalFormateado = str_replace('.', $separador, $retorno);
            $retornar['codigo'] = 1;
            $retornar['total'] = $totalFormateado;
            $retornar['total_formato'] = formatearImporte($total);
        }
        echo json_encode($retornar);
    }

    public function guardarConfiguracionCtaCte() {
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $resultado = '';
        $this->form_validation->set_rules('vigencia', lang('vigencia'), 'numeric');
        $this->form_validation->set_rules('cantAlertas', lang('alerta'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $nombre = $this->input->post('nombre');
            switch ($nombre) {
                case 'vigencia':
                    $valor = $this->input->post('valor');
                    $codigo = 9;
                    $key = 'DiasVigenciaPresupuesto';
                    $resultado = $this->Model_ctacte->guardarConfiguracion($codigo, $valor, $key, $usuario);
                    break;

                case 'cantAlertas':
                    $valor = $this->input->post('valor');
                    $codigo = 18;
                    $key = 'CantAlertasDeudores';
                    $resultado = $this->Model_ctacte->guardarConfiguracion($codigo, $valor, $key, $usuario);
                    break;

                case 'alertasSugerencia':
                    $valor = $this->input->post('valor');
                    $codigo = 13;
                    $key = 'AlertaSugerenciaBaja';
                    $resultado = $this->Model_ctacte->guardarConfiguracion($codigo, $valor, $key, $usuario);
                    break;

                case 'bajaMorosos':
                    $valor = $this->input->post('valor');
                    $codigo = 23;
                    $key = 'bajaDirectaMorosos';
                    $resultado = $this->Model_ctacte->guardarConfiguracion($codigo, $valor, $key, $usuario);
                    break;

                case 'piePresupuesto':
                    $valor = $this->input->post('valor');
                    $codigo = 16;
                    $key = 'descripcionPiePresupuesto';
                    $resultado = $this->Model_ctacte->guardarConfiguracion($codigo, $valor, $key, $usuario);
                    break;
            }
        }
        echo json_encode($resultado);
    }

    /**
     * formulario baja linea ctacte
     * @access public
     * @return 
     */
    public function frm_baja() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_ctacte', lang('codigo'), 'required|numeric|validarBajaCtaCte');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $motivos = $this->Model_ctacte->getMotivosBaja();
            $data['cod_ctacte'] = $this->input->post('cod_ctacte');
            $data['descripcion'] = $this->Model_ctacte->getDescripcion($data['cod_ctacte']);
            $data['motivos'] = $motivos;
            $this->load->view('resumenCuenta/frm_baja', $data);
        }
    }

    /**
     * baja lineas de ctacte
     * @access public
     * @return json 
     */
    public function baja() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'required|numeric|validarBajaCtaCte');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $respuesta = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $datos = array(
                'ctacte' => $this->input->post('codigo'),
                'motivo' => $this->input->post('motivo'),
                'comentario' => $this->input->post('comentario'),
                'cod_usuario' => $this->session->userdata('codigo_usuario')
            );
            $respuesta = $this->Model_ctacte->baja($datos);
        }
        echo json_encode($respuesta);
    }

    public function guardarNuevaCtaCte() {
        $this->load->library('form_validation');
        $this->load->helper("filial");
        $resultado = '';
        $accion = $this->input->post('accion');
        if ($accion == 'linea') {
            $this->form_validation->set_rules('cuotas', lang('cantidad_de_cuotas'), 'required|numeric');
            $this->form_validation->set_rules('fecha_primer_pago_concepto', lang('fecha_primer_pago'), 'validarFechaFormato');
            $this->form_validation->set_rules("cuota_periodo", lang('planpago_periodo'), "required|numeric");
        } else {
            $this->form_validation->set_rules("matricula", lang('matricula'), "required|numeric");
            $this->form_validation->set_rules('planes', lang('detalleplan_plan'), 'required');
            $this->form_validation->set_rules('codigo-financiacion[]', lang('detalleplan_financiacion'), 'required');
            $codfinanciaciones = $this->input->post('codigo-financiacion');
            $codconceptos = $this->input->post('plan-concepto');
            $fechaprimerpago = $this->input->post('fechaPrimerPago');
            $planpago = $this->input->post('planes');
            for ($i = 0; $i < count($codfinanciaciones); $i++) {
                $_POST['cod_concepto' . $i] = $codconceptos[$i];
                $arrDatosPlan = array('plan' => $planpago, 'financiacion' => $codfinanciaciones[$i], 'fecha' => $fechaprimerpago[$i]);
                $jsDatosPlan = json_encode($arrDatosPlan);
                $this->form_validation->set_rules('cod_concepto' . $i, lang('detalleplan_fecha'), 'validarPrimerPagoMatricula[' . $jsDatosPlan . ']');
                $datos['financiaciones'][$i]['cod_financiacion'] = $codfinanciaciones[$i];
                $datos['financiaciones'][$i]['cod_concepto'] = $codconceptos[$i];
                $datos['financiaciones'][$i]['fecha_primer_pago'] = $fechaprimerpago[$i] != '' ? formatearFecha_mysql($fechaprimerpago[$i]) : null;
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            $datos['cod_usuario'] = $this->session->userdata('codigo_usuario');
            $datos['accion'] = $accion;
            if ($accion == 'linea') {
                $filial = $this->session->userdata('filial');
                $arrConf = array("codigo_filial" => $filial["codigo"]);
                $this->load->model("Model_configuraciones", "", false, $arrConf);
                $datos['perioricidad'] = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte', null, $this->input->post('cuota_periodo'));
                $datos['cuotas'] = $this->input->post('cuotas');
                $datos['cod_concepto'] = $this->input->post('cod_concepto');
                $datos['alumno'] = $this->input->post('alumno');
                $datos['valor_refinanciar'] = $this->input->post('importe_seleccionado');
                $datos['fechapago'] = formatearFecha_mysql($this->input->post('fecha_primer_pago_concepto'));
            } else {
                $datos['plan'] = $planpago;
                $datos['matricula'] = $this->input->post('matricula');
            }
            $resultado = $this->Model_ctacte->guardarCtaCte($datos);
        }
        echo json_encode($resultado);
    }

    public function getDetalleFinanciacionNuevo() {
        $datos = array();
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->library('form_validation');
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->model("Model_planes_financiacion", "", false, $arrConf);
        $this->load->helper("filial");
        $accion = $this->input->post('accion');
        if ($accion == 'linea') {
            $this->form_validation->set_rules('cuotas', lang('cantidad_de_cuotas'), 'required|numeric');
            $this->form_validation->set_rules('fecha_primer_pago_concepto', lang('fecha'), 'required|validarFechaFormato|validarConFechaHoy');
            $this->form_validation->set_rules('importe_seleccionado', lang('importe'), 'required');
            $this->form_validation->set_rules("cuota_periodo", lang(''), "required|numeric");
        } else {
            $this->form_validation->set_rules("matricula", lang('matricula'), "required|numeric");
            $this->form_validation->set_rules('planes', lang('detalleplan_plan'), 'required');
            $this->form_validation->set_rules('codigo-financiacion[]', lang('detalleplan_financiacion'), 'required');
            $codfinanciaciones = $this->input->post('codigo-financiacion');
            $codconceptos = $this->input->post('plan-concepto');
            $fechaprimerpago = $this->input->post('fechaPrimerPago');
            $this->form_validation->set_rules("fechaPrimerPago", lang('fechaPrimerPago'), "required|validarConFechaHoy");
            $planpago = $this->input->post('planes');
            for ($i = 0; $i < count($codfinanciaciones); $i++) {
                $_POST['cod_concepto' . $i] = $codconceptos[$i];
                $arrDatosPlan = array('plan' => $planpago, 'financiacion' => $codfinanciaciones[$i], 'fecha' => $fechaprimerpago[$i]);
                $jsDatosPlan = json_encode($arrDatosPlan);
                $this->form_validation->set_rules('cod_concepto' . $i, lang('detalleplan_fecha'), 'validarPrimerPagoMatricula[' . $jsDatosPlan . ']');
                $datos['financiaciones'][$i]['cod_financiacion'] = $codfinanciaciones[$i];
                $datos['financiaciones'][$i]['cod_concepto'] = $codconceptos[$i];
                $datos['financiaciones'][$i]['fecha_primer_pago'] = $fechaprimerpago[$i] != '' ? formatearFecha_mysql($fechaprimerpago[$i]) : null;
            }
        }

        $resultado = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );

        } else {
            $datos['decimales'] = 2;
            if ($accion == 'linea') {
                $datos['perioricidad'] = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte', null, $this->input->post('cuota_periodo'));
                $datos['cuotas'] = $this->input->post('cuotas');
                $datos['cod_concepto'] = $this->input->post('cod_concepto');
                $datos['valor_refinanciar'] = $this->input->post('importe_seleccionado');
                $datos['fechapago'] = formatearFecha_mysql($this->input->post('fecha_primer_pago_concepto'));
                $resultado = $this->Model_ctacte->getDetalleNuevoConcepto($datos);
            } else {
                $datos['cod_plan'] = $planpago;
                $datos['moneda'] = $filial['moneda'];
                $resultado = $this->Model_planes_financiacion->getDetallesFinanciaciones($datos);
            }
        }
        echo json_encode($resultado);
    }

    /* La siguiente function es compartida con un Web Services MODIFICAR SOLO MANTENIENDO EL FORMATO Y PARAMETROS ACTUALES */
    public function getConceptos($idFilial) {
        session_method();
        $config = array("codigo_filial" => $idFilial);
        $this->load->model("Model_conceptos", "", false, $config);
        $arrResp = $this->Model_conceptos->getConceptos();
        echo json_encode($arrResp);
    }

    public function api_Ctacte() {
        $this->load->library('form_validation');
        $filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $idioma = $_POST['idioma'];
        $this->lang->load($idioma, $idioma);

        $config = array("codigo_filial" => $filial);
        $this->load->model("Model_conceptos", "", false, $config);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        $data = array();
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $config = array("codigo_filial" => $filial);
            $this->load->model("Model_alumnos", "", false, $config);
            $ctacte = $this->Model_alumnos->getCtaCteFrm($cod_alumno, $filial, $idioma);
            if (count($ctacte) > 0) {
                $data['alumno'] = $this->Model_alumnos->getAlumno($cod_alumno);
                $data['cod_alumno'] = $cod_alumno;
                $data['seccion'] = $this->seccion;
                $data['ctacte'] = $ctacte;
                $data['nombre_apellido'] = $this->Model_alumnos->getNombreAlumno($cod_alumno);
                $data['codigo'] = 1;
                $data['conceptos'] = $this->Model_conceptos->getConceptosUsuario();
                $data['periodicidad'] = $this->Model_configuraciones->getPeriodicidad();
            } else {
                $data['codigo'] = 0;
                $data['msgerror'] = lang('alumno_sin_registro_ctacte');
            }
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    public function api_getImputaciones_facturas() {
        $filial = $_POST['filial'];
        $cod_ctacte = $_POST['ctacte'];
        $imputaciones = $this->Model_ctacte->getImputaciones($cod_ctacte, $filial);
        echo json_encode($imputaciones);
    }

    public function api_examenesCheckCtacteMoras()
    {
        $this->load->library('form_validation');
        $filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $config = array("codigo_filial" => $filial);
        $data = array();
        //si tiene deudas no puede inscribirse
        //se saco la validacion porque estaba mal hecha
        /*$this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        $data = array();
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $mora = $this->Model_ctacte->checkMoraCampusExamenes($cod_alumno, $filial);
            //si tiene deudas no puede inscribirse
            if ($mora["saldo"] > 0) {
                $data['habilitado'] = false;
            } else {
                $data['habilitado'] = true;
            }
            header('Content-Type: application/json');
            echo json_encode($data);
        }*/
        $mora = $this->Model_ctacte->checkMoraCampusExamenes($cod_alumno, $filial);
        if ($mora["saldo"] > 0) {
            $data['habilitado'] = false;
        } else {
            $data['habilitado'] = true;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        //}
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function get_pendientes_de_pago() {
        $arrResp = array();
        $codFilial = $this->input->post("cod_filial");
        $conexion = $this->load->database($codFilial, true);
        $habilitado = $this->input->post("habilitado") ? $this->input->post("habilitado") : null;
        $fechaDesde = $this->input->post("fecha_desde") ? $this->input->post("fecha_desde") : null;
        $fechaHasta = $this->input->post("fecha_hasta") ? $this->input->post("fecha_hasta") : null;
        $soloImporte = $this->input->post("solo_importe");
        $arrResp['data']['ctacte_pendiente'] = Vctacte::getCtactePendiente($conexion, $fechaDesde, $fechaHasta, $habilitado, $soloImporte);
        echo json_encode($arrResp);
    }

    /**
     * retorna lista de planes pago vigente
     * @access public
     * @return json de planes
     */
    public function getPlanesPago() {
        $filial = $this->session->userdata('filial');
        $cod_matricula = $this->input->post('cod_matricula');
        $arrConfig = array();
        $arrConfig["filial"]["codigo"] = $filial["codigo"];
        $this->load->model("Model_matriculas", "", false, $arrConfig);
        $planes = $this->Model_matriculas->getPlanesVigentesMatricula($cod_matricula);
        echo json_encode($planes);
    }

    public function frm_agregar_descuento() {
        $data = array();
        $filial = $this->session->userdata("filial");
        $codCtacte = $this->input->post("cod_ctacte");
        $conexion = $this->load->database($filial['codigo'], true);
        $myCtacte = new Vctacte($conexion, $codCtacte);
        $data['arrDescuentos'] = $myCtacte->getMatriculacionesCtacteDto();
        $data['importe_total'] = $myCtacte->importe - $myCtacte->pagado;
        $data['separador_decimal'] = $filial['moneda']['separadorDecimal'];
        $data['simbolo_moneda'] = $filial['moneda']['simbolo'];
        $data['cod_ctacte'] = $myCtacte->getCodigo();
        $data['suma_descuento'] = 0;
        $data['lang'] = getLang(array(
            "el_descuento_condicionado_es_mayor_que_el_total", "validacion_ok", "dias_de_vencida_es_quererido", 
            "valor_no_puede_ser_cero"
        ));
        $this->load->view("resumenCuenta/frm_agregar_descuento", $data);
    }

    public function guardar_descuento() {
        $resp = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('valor', lang('valor'), 'required|numeric');
        $this->form_validation->set_rules('forma_descuento', lang('forma_de_descuento'), 'required');
        $this->form_validation->set_rules('tipo_descuento', lang('tipo_de_descuento'), 'required');
        $this->form_validation->set_rules('cod_ctacte', lang('codigo'), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resp['error'] = $errors;
        } else {
            $codCtacte = $this->input->post("cod_ctacte");
            $valor = $this->input->post("valor");
            $valor = round($valor, 2);
            $valorOriginal = $valor;
            $formaDescuento = $this->input->post("forma_descuento");
            $tipoDescuento = $this->input->post("tipo_descuento");
            $diasVencido = $this->input->post("dias_vencida");
            $filial = $this->session->userdata("filial");
            $conexion = $this->load->database($filial['codigo'], true);
            $myCtacte = new Vctacte($conexion, $codCtacte);
            $importeTotal = $myCtacte->importe - $myCtacte->pagado;
            if ($formaDescuento == "importe") {
                $valor = round(($valor * 100 / $myCtacte->importe), 2);
            }
            if ($formaDescuento == "importe"){
                $valorFinal = $myCtacte->importe - $valorOriginal;
            } else {
                $valorFinal = round($importeTotal - ($importeTotal * $valor / 100), 2);
            }
            if ($valorFinal < 0) {
                $resp['error'] = lang("el_descuento_condicionado_es_mayor_que_el_total");
            } else {
                $codUsuario = $this->session->userdata("codigo_usuario");
                $conexion->trans_begin();
                $myDescuento = new Vmatriculaciones_ctacte_descuento($conexion);
                $myDescuento->cod_ctacte = $myCtacte->getCodigo();
                $myDescuento->cod_usuario = $codUsuario;
                $myDescuento->descuento = $valor;
                $myDescuento->dias_vencido = $tipoDescuento == Vmatriculaciones_ctacte_descuento::getEstadoCondicionado() ? $diasVencido : 0;
                $myDescuento->estado = $tipoDescuento;
                $myDescuento->fecha = date("Y-m-d H:i:s");
                $myDescuento->forma_descuento = Vmatriculaciones_ctacte_descuento::getTipoDescuentoManual();                
                $myDescuento->activo = 1;
                if ($formaDescuento == "importe"){
                    $myDescuento->importe = $valorOriginal;
                    $myCtacte->importe = $myCtacte->importe - $valorOriginal;                    
                } else {
                    $myDescuento->importe = $myCtacte->importe - ($valorFinal + $myCtacte->pagado);
                    $myCtacte->importe = $valorFinal + $myCtacte->pagado;
                    
                }
                $myDescuento->guardarMatriculaciones_ctacte_descuento();
                //  las siguientes lineas se comentan para resolver el ticket 053-04394 (volver a ver si esto es correcto)
//                if ($myCtacte->cod_concepto <> 3){
//                    $myCtacte->eliminarMoras();
//                    $myCtacte->aplicarMora();
//                }                
                $myCtacte->guardarCtacte();
                if ($conexion->trans_status()){
                    $conexion->trans_commit();
                    $resp['success'] = "success";
                    $resp['codigo_condicionado'] = $myDescuento->getCodigo();
                    $resp['codigo_ctacte'] = $myCtacte->getCodigo();
                } else {
                    $conexion->trans_rollback();
                    $resp['error'] = lang("error_al_realizar_la_operacion_solicitada")."<br>".lang("vuelva_a_intentar_mas_tarde");
                    $resp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                }
                
                //mmori - modificaciones en certificados
                //si el descuento es total, puede pasar que se trate una última cuota
                //por lo que hay que verificar que si se cumplen las condiciones 
                //para certificar
                if($valorFinal == 0)
                {
                    if($myCtacte->cod_concepto == 1 || $myCtacte->cod_concepto == 5)
                    {
                        $matricula = new Vmatriculas($conexion, $myCtacte->concepto);
                        $matriculas_periodos = $matricula->getPeriodosMatricula();
                        foreach ($matriculas_periodos AS $periodo)
                        {
                            $objcertificado = new Vcertificados($conexion, $periodo['codigo'], 1);
                            $objcertificado->cambiarEstadoCertificadoIGA();
                        }
                    }  
                }
            }
        }
        echo json_encode($resp);
    }

    public function frm_recuperar_descuentos_condicionados(){
        $data = array();
        $this->load->helper("cuentacorriente");
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myMatricula = new Vmatriculas($conexion, $this->input->post("cod_matricula"));
        $arrDescuentos = $myMatricula->getDescuentos(Vmatriculaciones_ctacte_descuento::getEstadoCondicionadoPerdido(), true);
        formatearCtaCte($conexion, $arrDescuentos);
        $data['arrDescuentos'] = $arrDescuentos;
        $data['simbolo_decimal'] = $filial['moneda']['separadorDecimal'];
        $data['cod_matricula'] = $this->input->post("cod_matricula");
        $data['langFRM'] = getLang(array("validacion_ok"));
        $this->load->view("resumenCuenta/frm_recuperar_descuentos_condicionados", $data);
    }
    
    public function recuperar_descuento_condicionado(){
        $arrResp = array();
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $codMatricula = $this->input->post("cod_matricula");
        $codCtacteInicio = $this->input->post('cod_ctacte_inicio') && $this->input->post('cod_ctacte_inicio') <> -1 ? $this->input->post('cod_ctacte_inicio') : null;
        $myMatricula = new Vmatriculas($conexion, $codMatricula);
        $conexion->trans_begin();
        if ($myMatricula->recuperarDescuentoCondicionado($codCtacteInicio)){
            $arrResp['success'] = "success";
            $arrResp['cod_matricula'] = $myMatricula->getCodigo();
            $conexion->trans_commit();
        } else {
            $arrResp['error'] = "error_al_recuperar_descuentos";
            $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            $conexion->trans_rollback();
        }
        echo json_encode($arrResp);
    }
    
    public function getDescuentos(){
        $arrResp = array();
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $myCtacte = new Vctacte($conexion, $this->input->post("cod_ctacte"));
        $arrEstados = array(
            Vmatriculaciones_ctacte_descuento::getEstadoCondicionado(),
            Vmatriculaciones_ctacte_descuento::getEstadoNoCondicionado()
        );
        $arrDescuentos = $myCtacte->getDescuentos($arrEstados);
        foreach ($arrDescuentos as $key => $descuento){
            if ($descuento['fecha_perdida_descuento'] <> ''){
                $arrDescuentos[$key]['fecha_perdida_descuento'] = formatearFecha_pais($descuento['fecha_perdida_descuento']);
            }
        }
        $arrResp['data']['descuentos'] = $arrDescuentos;
        $arrResp['data']['ctacte_importe'] = $myCtacte->importe;
        $arrResp['data']['simbolo_moneda'] = $filial['moneda']['simbolo'];
        $arrResp['data']['separador_decimal'] = $filial['moneda']['separadorDecimal'];
        echo json_encode($arrResp);
    }
    
    
    public function activar_descuento_condicionado(){
        if ($this->input->post('codigo_descuento_condicionado')){
            $codigoDescuento = $this->input->post("codigo_descuento_condicionado");
            $filial = $this->session->userdata("filial");
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $myDescuento = new Vmatriculaciones_ctacte_descuento($conexion, $codigoDescuento);
            if ( $myDescuento->reactivar() ){
                $conexion->trans_commit();
                //die($conexion->last_query());
                $arrResp['codigo'] = "1";
                $arrResp['success'] = "success";
                $arrResp['codigo_descuento_condicionado'] = $myDescuento->getCodigo();
                $arrResp['cod_ctacte'] = $myDescuento->cod_ctacte;
                $ctacte = new Vctacte($conexion, $myDescuento->cod_ctacte);
                $arrResp['importe'] = $ctacte->importe;
            } else {
                $conexion->rollbackTransaction();
                $arrResp['codigo'] = "0";
                $arrResp['error'] = "error";
                $arrResp['msgError'] = "Error al anular descuento<br>Vuelva a intentar más tarde";
                $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        } else {
            $arrResp['codigo'] = "0";
            $arrResp['error'] = "Error de parámetros";
        }
        
        echo json_encode($arrResp);
    }
    
    public function eliminar_descuento_condicionado(){
        if ($this->input->post('codigo_descuento_condicionado')){
            $codigoDescuento = $this->input->post("codigo_descuento_condicionado");
            $filial = $this->session->userdata("filial");
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $myDescuento = new Vmatriculaciones_ctacte_descuento($conexion, $codigoDescuento);
            if ($myDescuento->desactivar()){
                $conexion->trans_commit();
                $arrResp['codigo'] = "1";
                $arrResp['success'] = "success";
                $arrResp['codigo_descuento_condicionado'] = $myDescuento->getCodigo();
                $arrResp['cod_ctacte'] = $myDescuento->cod_ctacte;
                $ctacte = new Vctacte($conexion, $myDescuento->cod_ctacte);
                $arrResp['importe'] = $ctacte->importe;
            } else {
                $conexion->rollbackTransaction();
                $arrResp['codigo'] = "0";
                $arrResp['error'] = "error";
                $arrResp['msgError'] = "Error al anular descuento<br>Vuelva a intentar más tarde";
                $arrResp['debug'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        } else {
            $arrResp['codigo'] = "0";
            $arrResp['error'] = "Error de parámetros";
        }
        echo json_encode($arrResp);
    }    
    
    public function reporte_morosidad(){
        $arrResp = array();
        if ($this->input->post("cod_filial") && $this->input->post("fecha_desde") && $this->input->post("fecha_hasta")){
            $codFilial = $this->input->post("cod_filial");
            $fechaDesde = $this->input->post("fecha_desde");
            $fechaHasta = $this->input->post("fecha_hasta");
            $conexion = $this->load->database($codFilial, true);
            $myFilial = new Vfiliales($conexion, $codFilial);
            $cotizacion = $myFilial->getMonedaCotizacion();
            $arrResp['transfer']['morosidad']['aaData'] = Vctacte::get_reporte_morosidad($conexion, $fechaDesde, $fechaHasta);
            $arrResp['transfer']['moneda']['codigo'] = $cotizacion[0]['id'];
            $arrResp['transfer']['moneda']['simbolo'] = $cotizacion[0]['simbolo'];
        } else {
            $arrResp['error'] = "error de parametros";
        }
        echo json_encode($arrResp);
    }    
}