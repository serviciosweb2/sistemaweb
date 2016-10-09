<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportes extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $this->load->model("Model_reportes", "", false, $codFilial);
    }
    
    public function documentacion_faltante_y_materiales_no_entregados(){
        $this->inicializarReporte(lang("documentacion_faltante_y_materiales_no_entregados"), "documentacion_faltante_y_materiales_no_entregados");
    }
    
    public function estado_alumnos_certificados(){
        $this->inicializarReporte(lang("estado_alumnos_certificados"), "estado_alumnos_certificados");
    }

    public function cajas() {
        $this->inicializarReporte(lang("reporte_de_cajas"), "cajas");
    }

    public function facturas() {
        $this->inicializarReporte(lang("reporte_de_facturas"), "facturas");
    }

    public function movimientos_cajas() {
        $this->inicializarReporte(lang("reporte_de_movimientos_de_caja"), "movimientos_cajas");
    }

    public function cobros() {
        $this->inicializarReporte(lang("reporte_de_cobros"), "cobros");
    }

    public function consultas_web() {
        $this->inicializarReporte(lang("reporte_de_consultas_web"), "consultas_web");
    }

    public function presupuestos() {
        $this->inicializarReporte(lang("reporte_de_presupuestos"), "presupuestos");
    }

    public function comisiones() {
        $this->inicializarReporte(lang("reporte_de_comisiones"), "comisiones");
    }

    public function profesores() {
        $this->inicializarReporte(lang("reporte_de_profesores"), "profesores");
    }

    public function matriculas() {
        $this->inicializarReporte(lang("reporte_de_matriculas"), "matriculas");
    }

    public function aspirantes() {
        $this->inicializarReporte(lang("reporte_de_aspirantes"), "aspirantes");
    }

    public function alumnos() {
        $this->inicializarReporte(lang("reporte_de_alumnos"), "alumnos");   
    }

    public function ctacte_pendientes() {
        $this->inicializarReporte(lang("ctacte_pendientes"), "ctacte_pendientes");
    }

    public function reporte_boleto_bancarios() {
        $this->inicializarReporte(lang('reporte_boletos_bancarios'), 'reporte_boletos_bancarios');
    }

    public function reporte_alumnos_activos_por_comision(){
        $this->inicializarReporte(lang('reporte_alumnos_activos_por_comision'), 'reporte_alumnos_activos_por_comision');
    }

    /* Inicio Nuevos Reportes */
    public function cobros_estimados(){
        $this->inicializarReporte(lang('cobros_estimados'), 'cobros_estimados');
    }
    /* Fin Nuevos Reportes */
    
    public function getReporte() {
        $this->load->library('form_validation');
        $currentPage = $this->input->post("iCurrentPage") ? $this->input->post("iCurrentPage") : null;
        $pageDisplay = $this->input->post("iPaginationLength") ? $this->input->post("iPaginationLength") : null;
        $sortName = $this->input->post("iSortCol") ? $this->input->post("iSortCol") : null;
        $sortDir = $this->input->post("iSortDir") ? $this->input->post("iSortDir") : null;
        $reportName = $this->input->post("report_name");
        $sSearch = $this->input->post("sSearch") ? $this->input->post("sSearch") : null;
        $iFieldView = $this->input->post("iFieldView") ? $this->input->post("iFieldView") : null;
        $applyCommonFilters = $this->input->post("apply_common_filters") ? $this->input->post("apply_common_filters") : null;
        $filters = $this->input->post("filters") ? $this->input->post("filters") : null;
        $resultado = true;
        if ($filters[0]['field'] == 'fecha_factura' && $filters[0]['filter'] == 'entre') {
            $resultado = $this->validarFechasReporte($filters); //Funcion que valida las fechas siempre que el filtro elegido sea entre, retorno true o false;
        }
        if ($resultado) {
            $arrResp = $this->Model_reportes->getReporte($reportName, false, $currentPage, $pageDisplay, $sortName, $sortDir, $sSearch, $iFieldView, $applyCommonFilters, $filters);
            $arrResp['codigo'] = 1;
        } else {
            $arrResp = array(
                "codigo" => 0,
                "msgerror" => lang('validacion_fecha_reporte')
            );
        }
        echo json_encode($arrResp);
    }

    public function getFiltrosCondiciones() {
        $reportName = $this->input->post("report_name");
        $fieldName = $this->input->post("field_name");
        $arrResp = $this->Model_reportes->getFiltrosCondiciones($reportName, $fieldName);
        echo json_encode($arrResp);
    }

    function eliminarFiltroGuardado() {
        $codFiltro = $this->input->post("id_filtro");
        $arrResp = array();
        if ($this->Model_reportes->eliminarFiltroGuardado($codFiltro)) {
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = lang("error_al_eliminar_el_filtro_personalizado");
        }
        echo json_encode($arrResp);
    }

    function guardarFiltros() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('report_name', lang("nombre_del_reporte"), 'required');
        $this->form_validation->set_rules('usar_defecto', lang("usar_por_defecto"), 'required');
        $this->form_validation->set_rules('compartir', lang("compartir_con_todos"), 'required');
        $this->form_validation->set_rules('iFieldView', lang("campos_de_vista"), 'required');
        $this->form_validation->set_rules('filter_save_name', lang('nombre'), 'required|validarNombreFiltro');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'error' => $errors
            );
            echo json_encode($resultado);
        } else {
            $filtrosAvanzados = $this->input->post("filters") ? $this->input->post("filters") : null;
            $filtrosComunes = $this->input->post("apply_common_filters") ? $this->input->post("apply_common_filters") : null;
            $reporte = $this->input->post("report_name");
            $nombreFiltro = $this->input->post("filter_save_name");
            $default = $this->input->post("usar_defecto");
            $compartir = $this->input->post("compartir");
            $camposMostrar = $this->input->post("iFieldView");
            $usuario = $this->session->userdata('codigo_usuario');
            $arrResp = array();
            $arrResp = $this->Model_reportes->guardarFiltros($reporte, $nombreFiltro, $usuario, $camposMostrar, $filtrosComunes, $filtrosAvanzados, $compartir, $default);
            echo json_encode($arrResp);
        }
    }

    public function getFiltroGuardado() {
        $reporte = $this->input->post("report_name");
        $usuario = $this->session->userdata("codigo_usuario");
        $codigoReporte = $this->input->post("codigo_filtro");
        $arrFiltro = $this->Model_reportes->getFiltros($reporte, $usuario, $codigoReporte);
        echo json_encode($arrFiltro);
    }

    //se le puede pasar un array con los nombres de los filtros que se ejecutan al inicializar el reporte ($filtrar_al_inicio)
    //$this->inicializarReporte(lang("reporte_de_inscripciones"), "inscripciones", "false", array("anio"));
    private function inicializarReporte($title, $nombreReporte, $solo_lectura = null, $filtrar_al_inicio = false) {
        //Funcion que inicializa todos los reportes
        $filial = $this->session->userdata('filial');
        $separador = ",";//$filial['nombreFormato']['separadorNombre'];

        $cargarDatos = true;
//        if($nombreReporte == 'cobros_estimados') {
//            $cargarDatos = false;//solo va cargar el reporte al busca con filtro
//        }

        $data = array();
        $data['nombre_reporte'] = $nombreReporte;
        $data['titulo_pagina'] = $title;
        $data['page'] = 'reportes/vista_reporte';
        $data['separador_decimal_configuracion'] = $separador;
        $claves = array(
            "BIEN", "ERROR", "recuperando", "no_se_encuentran_filtros_definidos_para_guardar",
            "debe_indicar_el_nombre_del_filtro_a_guardar", "filtros_guardados_correctamente", "filtro_eliminado_correctamente",
            "SELECCIONE_UNA_OPCION", "no_hay_datos_disponivles_pata_mostrar", "imprimir_informe", "exportar_informe",
            "anio", "mes", "deuda", "deuda_activa", "deuda_pasiva", "cercana",
            "falta_carga_asistencia", "error_en_filtro_aplicado", "fecha_desde_es_mayor_a_fecha_hasta", "habilitadas",
            "contiene", "es_mayor_a", "es_menor_a", "entre", "es_igual_a", "es_mayor_igual_a", "es_menor_igual_a", "no_es_igual"
        );
        $data['lang'] = getLang($claves);
        //siwakawa 
        $ci = & get_instance(); // -- billete
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        //llamada al modelo model_reportes.php para la carga de datos
        $data['reporte'] = $this->Model_reportes->getReporte($nombreReporte, true, null, null, null, null, null, null, null, null, true, $solo_lectura, $filtrar_al_inicio, $cargarDatos);
        $data['filters'] = $this->Model_reportes->getFiltros($nombreReporte, $this->session->userdata('codigo_usuario'));
        $data['filtrar_al_inicio'] = $filtrar_al_inicio;
        $this->load->view('container', $data);
    }

    public function comprobantes_compras() {
        $this->inicializarReporte(lang("reporte_comprobantes_compras"), "comprobantes_compras");
    }

    public function inscripciones() {
        $this->inicializarReporte(lang("reporte_de_inscripciones"), "inscripciones", "false");
    }
    
    //cursos_venta
    public function cursos_venta(){
        $this->lang->load(get_idioma(), get_idioma());
        $data['page'] = 'reportes/cursos_venta';
        $data['seccion']['categoria'] = "reportes";
        $data['seccion']['slug'] = "cursos_venta";
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $this->load->helper("datatables");
        $claves = array("gastoseingresos", "INGRESOS", "GASTOS", "TOTALINGRESOS", "TOTALGASTOS","gastos","ingresos", "Imprimir_por_el_navegador",
                        "Rentabilidad", "fechaDesde_horario", "fecha_hasta_", "periodo", "afavor", "encontra", "combinados");
        $data['lang'] = getLang($claves);
        $data['idioma'] = get_idioma();
        $this->load->view('container', $data);
    }
    
    //reportes rentabilidad
    public function rentabilidad(){
        $this->lang->load(get_idioma(), get_idioma());
        $data['page'] = 'reportes/rentabilidad';
        $data['seccion']['categoria'] = "reportes";
        $data['seccion']['slug'] = "rentabilidad";
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $this->load->helper("datatables");
        $filial = $this->session->userdata('filial');
        $data['pesos'] = $filial['moneda']['simbolo'];
        $claves = array("gastoseingresos", "INGRESOS", "GASTOS", "TOTALINGRESOS", "TOTALGASTOS","gastos","ingresos", "Imprimir_por_el_navegador",
                        "Rentabilidad", "fechaDesde_horario", "fecha_hasta_", "periodo", "afavor", "encontra", "combinados", "por_periodos", "por_fechas", "todos");
        $data['lang'] = getLang($claves);
        $data['idioma'] = get_idioma();
        $this->load->view('container', $data);
    }

    public function rentabilidad2(){
        $data['page'] = 'reportes/vista_rentabilidad';
        $data['seccion']['categoria'] = "reportes";
        $data['seccion']['slug'] = "rentabilidad";
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $claves = array('cod_mov', 'hoy', 'ayer', 'este_mes', 'ultimo_mes', 'ultimos_30_dias', 'ultima_semana', 'id_mov_caja_vacio', 'error', 'no_tiene_permiso', 'concepto', 'cancelar', 'aceptar', 'rango_perzonalizado', 'valor', 'no_ingresos_para_rango_fechas', 'descripcion', 'imputado', 'no_egresos_para_rango_fechas');
        $data['lang'] = getLang($claves);
        $this->load->view('container', $data);
    }

    //reporte rentabilidad distinta estructura
    public function rentabilidadTabla(){
        $this->lang->load(get_idioma(), get_idioma());
        $data['page'] = 'reportes/rentabilidadTabla';
        $data['seccion']['categoria'] = "reportes";
        $data['seccion']['slug'] = "rentabilidad";
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $this->load->helper("datatables");
        $filial = $this->session->userdata('filial');
        $data['pesos'] = $filial['moneda']['simbolo'];
        $claves = array("gastoseingresos", "INGRESOS", "GASTOS", "TOTALINGRESOS", "TOTALGASTOS","gastos","ingresos", "Imprimir_por_el_navegador",
            "Rentabilidad", "fechaDesde_horario", "fecha_hasta_", "periodo", "afavor", "encontra", "combinados", "por_periodos", "por_fechas", "todos");
        $data['lang'] = getLang($claves);
        $data['idioma'] = get_idioma();
        $this->load->view('container', $data);
    }


    /** ****************************
    Nuevo reporte rentabilidad2
     **************************** **/


    public function  getReporteRentabilidad2(){
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $respuesta['main'] = $this->Model_reportes->getReporteRentabilidad($fecha_desde, $fecha_hasta);
        $respuesta['detEgreso'] = $this->Model_reportes->getReporteRentGastos($fecha_desde, $fecha_hasta);
        $respuesta['detIngreso'] = $this->Model_reportes->getReporteRentIngresos($fecha_desde, $fecha_hasta);

        echo json_encode($respuesta);
    }

    public function getDetallesIngreso(){
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";

        $concepto = (isset($_POST["concepto"])) ? $_POST["concepto"] : "";
       
        $respuesta['detIngreso'] = $this->Model_reportes->getDetIngresos($concepto, $fecha_desde, $fecha_hasta);

        echo json_encode($respuesta);

    }

    public function getDetalleGasto()
    {
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $gasto = (isset($_POST["gasto"])) ? $_POST["gasto"] : "";
        $cod_sub = (isset($_POST["cod_sub"])) ? $_POST["cod_sub"] : "";

        $respuesta['detEgreso'] = $this->Model_reportes->getDetGastos($gasto,$fecha_desde, $fecha_hasta, $cod_sub);
        $respuesta['menu'] = array("contextual" => array(
            "habilitado" => "1",
            "accion" => "editar_movimiento_caja_subrubro",
            "text" => lang("editar_movimiento_caja_subrubro"),
            "cod_sub" => $cod_sub,
            "gasto" => $gasto));

        echo json_encode($respuesta);
    }

    /** ****************************
     *
     *******************************/
    
    public function getReporteRentabiliadGastosEingresos(){
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $gastos_ingresos = $this->Model_reportes->getReporteRentabiliadGastosEingresos($fecha_desde, $fecha_hasta);
       

        echo json_encode($gastos_ingresos);
    }
    
    public function getReporteRentabiliadIngresos(){
        

        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $mes = (isset($_POST["periodo"]) && $_POST["periodo"] != 0) ? $_POST["periodo"] : "";
        if($mes){
        $fecha_desde = date('Y-'.(string)$mes.'-01');
        if($mes == 01 || $mes == 03 ||$mes == 05||$mes == 07|| $mes == 8 ||$mes == 10||$mes == 12)
            $fecha_hasta = date('Y-'.(string)$mes.'-31');
        elseif($mes != 02){
            $fecha_hasta = date('Y-'.(string)$mes.'-30');
        }else {
            $fecha_hasta = date('Y-'.(string)$mes.'-28');
        }
        }
        $ingresos = $this->Model_reportes->getReporteRentabilidadIngresos($fecha_desde, $fecha_hasta);
        echo json_encode($ingresos);
    }
    
    public function getReporteRentabilidadGastos(){
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $mes = (isset($_POST["periodo"]) && $_POST["periodo"] != 0) ? $_POST["periodo"] : "";
       

        if($mes){
            $fecha_desde = date('Y-'.(string)$mes.'-01');
            if($mes == 01 || $mes == 03 ||$mes == 05||$mes == 07|| $mes == 8 ||$mes == 10||$mes == 12)
            $fecha_hasta = date('Y-'.(string)$mes.'-31');
            elseif($mes != 02){
                $fecha_hasta = date('Y-'.(string)$mes.'-30');
            }else {
                $fecha_hasta = date('Y-'.(string)$mes.'-28');
            }
        }
        $gastos = $this->Model_reportes->getReporteRentabilidadGastos($fecha_desde, $fecha_hasta);
        echo json_encode($gastos);
    }
    
    public function getReporteRentabilidad(){
        $periodo = (isset($_POST["periodo"]) && $_POST["periodo"] !== lang('periodo')) ? $_POST["periodo"] : date("Y");
        $respuesta = $this->Model_reportes->getDataReporteRentabilidad($periodo);
     /*   $ticks = $this->Model_reportes->getTicksReporteRentabilidad();
        $respuesta = array("d1"=>$data, "ticks"=>$ticks);*/
        echo json_encode($respuesta);
    }

    public function getReporteGasto()
    {
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $gasto = (isset($_POST["gasto"])) ? $_POST["gasto"] : "";
        $cod_sub = (isset($_POST["cod_sub"])) ? $_POST["cod_sub"] : "";
        
        $respuesta['datos'] = $this->Model_reportes->getReporteGastos($gasto,$fecha_desde, $fecha_hasta, $cod_sub);
        $respuesta['menu'] = array("contextual" => array(
        "habilitado" => "1",
        "accion" => "editar_movimiento_caja_subrubro",
        "text" => lang("editar_movimiento_caja_subrubro")));
    
        echo json_encode($respuesta);
    }
    
    public function getReporteIngreso()
    {
        $fecha_desde = (isset($_POST["fecha_desde"]) && $_POST["fecha_desde"] != lang('fechaDesde_horario')) ? $_POST["fecha_desde"] : "";
        $fecha_hasta = (isset($_POST["fecha_hasta"]) && $_POST["fecha_hasta"] != lang('fecha_hasta_')) ? $_POST["fecha_hasta"] : "";
        $ingreso = (isset($_POST["ingreso"])) ? $_POST["ingreso"] : "";

        $respuesta = $this->Model_reportes->getReporteIngresos($ingreso, $fecha_desde, $fecha_hasta);
        echo json_encode( $respuesta);
    }



    //reporte deudas por alumnos
    public function imprimir_reporte_deudas_por_alumnos(){
        $html = $this->input->post("html");
        $fechaDesde = $this->input->post("fechaDesde");
        $fechaHasta = $this->input->post("fechaHasta");
        $periodo = $this->input->post("periodo");
        $gastoEingreso = $this->input->post("gastoEingreso");
        $gastos = $this->input->post("gastos");
        $ingresos = $this->input->post("ingresos");
        $rentabilidad = $this->input->post("rentabilidad");
        $data['html'] = $html;
        $data['fechaDesde'] = $fechaDesde;
        $data['fechaHasta'] = $fechaHasta;
        $data['gastoEingreso'] = $gastoEingreso;
        $data['gastos'] = $gastos;
        $data['ingresos'] = $ingresos;
        $data['rentabilidad'] = $rentabilidad;
        $data['periodo'] = $periodo;
        $this->load->view('reportes/impresiones', $data);
    }
    
    public function crearColumnas_deudas_por_alumnos(){
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('cod_alumno'), "campo" => 'cod_alumno'),
            array("nombre" => lang('cod_matricula'), "campo" => 'cod_matricula'),
            array("nombre" => $nombreApellido, "campo" => 'nombre'),
            array("nombre" => lang('nro_documento'), "campo" => 'documento'),
            array("nombre" => lang('fecha_ultimo_pago'), "campo" => 'fechapago'),
            array("nombre" => lang('total'), "campo" => 'total'),
            array("nombre" => lang('curso'), "campo" => 'curso'),
            array("nombre" => lang('comision'), "campo" => 'comision'),
            array("nombre" => lang('tipo_de_deuda'), "campo" => 'tipo_de_deuda'),
            array("nombre" => "Cuotas adeudadas", "campo" => 'cant_cuotas_debe', 'bVisible' => false)
        );
        return $columnas;
    }

    public function deudas_por_alumno(){
        $this->lang->load(get_idioma(), get_idioma());
        $ci = & get_instance();
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;
        $data['page'] = 'reportes/deudas_por_alumno';
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas_deudas_por_alumnos()));
        $data['columns'] = $aoColumnDefs;
        $filial = $this->session->userdata('filial');
        $data['pesos'] = $filial['moneda']['simbolo'];
        $this->load->view('container', $data);
    }
    
    public function listarDeudasPorAlumnos(){
        $filial = $this->session->userdata('filial');
        $crearColumnas =   $this->crearColumnas_deudas_por_alumnos();
        $arrFiltros["iDisplayStart"] = isset($_POST['start']) ? $_POST['start'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['length']) ? $_POST['length'] : "";
        $arrFiltros["sSearch"] = isset($_POST['search']['value']) ? $_POST['search']['value'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        if(isset($_POST['order'][0]['column'])){
            $arrFiltros["SortCol"] = $crearColumnas[$_POST['order'][0]['column']]['campo'];
        } else if(isset($_POST['iSortCol_0'])){
            $arrFiltros["SortCol"] = $crearColumnas[$_POST['iSortCol_0']]['campo'];
        }
        
        if(isset($_POST['order'][0]['dir'])){
            $arrFiltros["sSortDir"] = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : "";
        } else if(isset($_POST['sSortDir_0'])){
            $arrFiltros["sSortDir"] = $_POST['sSortDir_0'];
        }
        
        $arrFiltros["cant_cuotas"] = isset($_POST['cant_cuotas']) ? $_POST['cant_cuotas'] : "";
        $arrFiltros["ultimo_pago_select"] = isset($_POST['ultimo_pago_select']) ? $_POST['ultimo_pago_select'] : "";
        $arrFiltros["fecha_pago_desde"] = isset($_POST['fecha_pago_desde']) ? $_POST['fecha_pago_desde'] : "";
        $arrFiltros["fecha_pago_hasta"] = isset($_POST['fecha_pago_hasta']) ? $_POST['fecha_pago_hasta'] : "";
        $arrFiltros["saldo_acumulado"] = isset($_POST['saldo_acumulado']) ? $_POST['saldo_acumulado'] : "";
        $arrFiltros["desd"] = isset($_POST['desd']) ? $_POST['desd'] : "";
        $arrFiltros["hast"] = isset($_POST['hast']) ? $_POST['hast'] : "";
        $arrFiltros["cursos"] = isset($_POST['cursos']) ? $_POST['cursos'] : "";
        $arrFiltros["periodo"] = isset($_POST['periodo']) ? $_POST['periodo'] : "";
        $arrFiltros["anio"] = isset($_POST['anio']) ? $_POST['anio'] : "";
        $arrFiltros["comision"] = isset($_POST['comision']) ? $_POST['comision'] : "";
        $arrFiltros["turno"] = isset($_POST['turno']) ? $_POST['turno'] : "";
        $arrFiltros["tipo_deuda"] = isset($_POST['tipo_deuda']) ? $_POST['tipo_deuda'] : "";
        $valores = $this->Model_reportes->listarDeudasPorAlumnosDataTable($arrFiltros, $filial);
        if(isset($_POST['tipo_reporte']) && $_POST['tipo_reporte'] != ""){
            $this->load->helper('alumnos');
            $exp = new export($_POST['tipo_reporte']);
            $columnas = $this->crearColumnas_deudas_por_alumnos();
            foreach ($columnas as $titles){
                $arrTitle[] =  $titles['nombre'];
            }
            
            $arrWidth = array(18, 25, 44, 27, 36, 14, 36, 26, 23, 45);
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            );
            
            $exp->setTitle($arrTitle);
            $exp->setContent($valores['aaData']);
            $exp->setPDFFontSize(8);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("informe_deudas_por_alumnos"));
            $exp->setMargin(2, 8);
            $exp->setContentAcumulable(array('5'));
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }
    
    public function validarFechasReporte($filters) {
        $retorno = '';
        $date1_array = explode('/', $filters[0]['value1']);
        $date2_array = explode('/', $filters[0]['value2']);
        $date1 = $date1_array[1].'/'.$date1_array[0].'/'.$date1_array[2];
        $date2 = $date2_array[1].'/'.$date2_array[0].'/'.$date2_array[2];
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        if ( $date1 > $date2) {
            $retorno = false;
        } else {
            $retorno = true;
        }
        return $retorno;
    }

    public function exportarReporte() {
        $datos_reporte = json_decode($this->input->post('exportar_reporte'), true);
        $currentPage = $datos_reporte['iCurrentPage'] ? $datos_reporte['iCurrentPage'] : null;
        $pageDisplay = $datos_reporte['iPaginationLength'] ? $datos_reporte['iPaginationLength'] : null;
        $sortName = $datos_reporte['iSortCol'] ? $datos_reporte['iSortCol'] : null;
        $sortDir = $datos_reporte['iSortDir'] ? $datos_reporte['iSortDir'] : null;
        $reportName = $datos_reporte['report_name'];
        $sSearch = $datos_reporte['sSearch'] ? $datos_reporte['sSearch'] : null;
        $iFieldView = $datos_reporte['iFieldView'] ? $datos_reporte['iFieldView'] : null;
        $applyCommonFilters = $datos_reporte['apply_common_filters'] ? $datos_reporte['apply_common_filters'] : null;
        $filters = $datos_reporte['filters'] ? $datos_reporte['filters'] : null;
        $arrResp = $this->Model_reportes->exportarReportes($currentPage, $pageDisplay, $sortName, $sortDir, $reportName, $sSearch, $iFieldView, $applyCommonFilters, $filters);
        $nombreReporte = $reportName . date("Y-m-d h:i:s");
        header('Content-Description: File Transfer');
        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=$nombreReporte.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo implode("\n", $arrResp);
    }

    /* esta function está siendo accedida desde un web services */
    public function getReporteSeguimientoFiliales() {
        $idFilial = $this->input->post("id_filial");
        $arrResp = $this->Model_reportes->getReporteSeguimientoFiliales($idFilial);
        echo json_encode($arrResp);
    }

    public function asistencia() {
        $solo_lectura = $this->input->get('solo_lectura', TRUE) ? $this->input->get('solo_lectura', TRUE) : null;
        $this->inicializarReporte(lang("reporte_de_asistencias"), "asistencia", $solo_lectura);
    }

    public function inscripciones_comisiones() {
        $filtros[] = 'habilitadas';
        $this->inicializarReporte(lang("reporte_inscripciones_comisiones"), "inscripciones_comisiones",null,$filtros);
    }
    
    public function inscripciones_comisiones2() {
        $filtros[] = 'habilitadas';
        $this->inicializarReporte(lang("reporte_inscripciones_comisiones"), "inscripciones_comisiones2",null,$filtros);
    }

    public function reporte_cupones_generados() {
        $this->inicializarReporte(lang("reporte_cupones_generados"), "reporte_cupones_generados");
    }

    public function ctacte_factura_cobro() {
        $this->inicializarReporte(lang("ctacte_factura_cobro"), "ctacte_factura_cobro");
    }
    
    public function  reporte_alumnos_por_materias(){
        $this->inicializarReporte(lang("reporte_alumnos_por_materias"), "reporte_alumnos_por_materias");
    }
    
    public function reporte_alumnos_por_curso(){
        $this->inicializarReporte(lang("reporte_alumnos_por_curso"), "reporte_alumnos_por_curso");
    }

    public function reporte_inscriptos_por_materia(){
        $this->inicializarReporte(lang("reporte_inscriptos_por_materia"), "reporte_inscriptos_por_materia");
    }
 
    public function reporte_horarios(){
        $this->inicializarReporte(lang("repote_horarios"), "reporte_horarios");
    }



    /* Fin Nuevos Reportes */
    public function listar_alumnos_activos_curso(){
        $mes = $this->input->post("mes");
        $anio = $this->input->post("anio");
        $arrResp = $this->Model_reportes->getReporteAlumnosActivos(null, $mes, $anio);
        if ($this->input->post("accion") && $this->input->post("accion") == "exportar"){
            $tipoReporte = $this->input->post('tipo_reporte');
            $periodo = getMesNombre($mes)." ".$anio;
            $exp = new export($tipoReporte);
            $exp->setPageFormat("P");
            $arrTitle = array(
                lang("cursos"),
                lang("alumnos_activos")
            );
            $arrCocineritos = $arrResp['cocineritos'] ;
            $arrCursosCortos = $arrResp['cursos_cortos'];
            $arrCarreras = $arrResp['carreras'];
            $arrBodyCocineritos = array("0" => array());
            $arrBodyCursosCortos = array("0" => array());
            $arrBodyCarreras = array("0" => array());
            $cantidadCocineritos = 0;
            $cantidadCursosCortos = 0;
            $cantidadCarreras = 0;
            foreach ($arrCocineritos as $cocineritos){
                $arrBodyCocineritos[] = array($cocineritos['titulo'], $cocineritos['cantidad']);
                $cantidadCocineritos += $cocineritos['cantidad'];
            }
            foreach ($arrCursosCortos as $cursoCorto){
                $arrBodyCursosCortos[] = array($cursoCorto['titulo'], $cursoCorto['cantidad']);
                $cantidadCursosCortos += $cursoCorto['cantidad'];
            }
            foreach ($arrCarreras as $carrera){
                $arrBodyCarreras[] = array($carrera['titulo'], $carrera['cantidad']);
                $cantidadCarreras += $carrera['cantidad'];
            }
            if ($tipoReporte == 'pdf'){
                $arrBodyCarreras[0] = array(lang("carreras"), $cantidadCarreras);
                $arrBodyCocineritos[0] = array(lang("cocineritos"), $cantidadCocineritos);
                $arrBodyCursosCortos[0] = array(lang("cursos_cortos"), $cantidadCursosCortos);
            }
            $cantidadRegistros = $cantidadCarreras + $cantidadCocineritos + $cantidadCursosCortos;
            $arrWidth = array(120, 40);
            $arrBody = array_merge($arrBodyCarreras, $arrBodyCursosCortos, $arrBodyCocineritos);
            if ($tipoReporte == 'pdf'){
                $arrBody[] = array(lang("total"), $cantidadRegistros);
            }
            $arrResaltar = array(1, 2 + count($arrCarreras), 3 + count($arrCarreras) + count($arrCursosCortos), 
                4 + count($arrCarreras) + count($arrCursosCortos) + count($arrCocineritos));
            $exp->setTitle($arrTitle);
            $exp->setContent($arrBody);
            $exp->setColumnWidth($arrWidth);
            $exp->setContentResaltar($arrResaltar);
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 162, "height" => 4),
                array("txt" => lang("periodo").": ".$periodo, "size" => "8", "align" => "R", "width" => 162, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 162, "height" => 4)
            );
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file, 172);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("reporte_alumnos_activos_por_curso"));
            $exp->setMargin(26);
            $exp->exportar();
        } else {
            echo json_encode($arrResp);
        }
    }
    
    public function alumnos_activos_curso(){
        $ci = & get_instance();
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['page'] = "reportes/alumnos_activos_por_curso";
        $data['meses'] = getMeses();
        $this->load->view('container', $data);
    }
    
    public function crearColumnas_reporte_bajas(){
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('cod_alumno'), "campo" => 'cod_alumno'),
            array("nombre" => lang('cod_Mat'), "campo" => 'cod_matricula'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('fecha_baja'), "campo" => 'fecha_hora'),
            array("nombre" => lang('curso'), "campo" => 'nombre_curso'),
            array("nombre" => lang('comentario'), "campo" => 'comentario'),
            array("nombre" => lang('lbl_motivo'), "campo" => 'motivo')
        );
        return $columnas;
    }
    
    public function bajas(){
        $ci = & get_instance();
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['page'] = "reportes/bajas";
        $data['fechaDesde'] = $this->input->get("fecha_desde") && $this->input->get("fecha_desde") <> '' 
                ? $this->input->get('fecha_desde') : null;
        $data['fechaHasta'] = $this->input->get("fecha_hasta") && $this->input->get("fecha_hasta") <> ''
                ? $this->input->get("fecha_hasta") : null;
        $data['clausulaFechas'] = '3';
        $data['codCurso'] = $this->input->get("codCurso") && $this->input->get("codCurso") <> '' ? $this->input->get('codCurso') : null;
        $data['desdeInscripcionesYbajas'] = $this->input->get("desdeInscripcionesYbajas") && $this->input->get("desdeInscripcionesYbajas") <> '' ? $this->input->get('desdeInscripcionesYbajas') : null;
        $data['cod_plan_academico'] = $this->input->get("cod_plan_academico") && $this->input->get("cod_plan_academico") <> '' ? $this->input->get('cod_plan_academico') : null;
        $data['codigo'] = $this->input->get("codigo") && $this->input->get("codigo") <> '' ? $this->input->get('codigo') : null;
        $data['titulo'] = $this->input->get("titulo") && $this->input->get("titulo") <> '' ? $this->input->get('titulo') : null;
        $data['cod_tipo_periodo'] = $this->input->get("cod_tipo_periodo") && $this->input->get("cod_tipo_periodo") <> '' ? $this->input->get('cod_tipo_periodo') : null;
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas_reporte_bajas()));
        $data['columns'] = $aoColumnDefs;
        $claves = array(
            "BIEN", "ERROR", "SELECCIONE_UNA_OPCION", "imprimir_informe", "exportar_informe", "fecha_emision", "todos", "_idioma",
            "fecha_desde_es_mayor_a_fecha_hasta", "contiene", "es_mayor_a", "es_menor_a", "entre", "es_igual_a", "es_mayor_igual_a", "es_menor_igual_a", "no_es_igual"
        );
        $data['lang'] = getLang($claves);
        $this->load->view('container', $data);
    }
    
    public function listarReporteBajas(){
        $crearColumnas = $this->crearColumnas_reporte_bajas();
        $arrFiltros["iDisplayStart"] = isset($_POST['start']) ? $_POST['start'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['length']) ? $_POST['length'] : "";
        $arrFiltros["sSearch"] = isset($_POST['search']['value']) ? $_POST['search']['value'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = "";
        $arrFiltros["sSortDir"] = "";
        if(isset($_POST['order'][0]['column'])){
            $arrFiltros["SortCol"] = $crearColumnas[$_POST['order'][0]['column']]["campo"];
        } else if(isset($_POST['iSortCol_0'])){
            $arrFiltros["SortCol"] = $crearColumnas[$_POST['iSortCol_0']]["campo"];
        }
        if(isset($_POST['order'][0]['dir'])){
            $arrFiltros["sSortDir"] = $_POST['order'][0]['dir'];
        } else if(isset($_POST['order'][0]['dir'])){
            $arrFiltros["sSortDir"] = $_POST['sSortDir_0'];
        }
        $fechaDesde = $this->input->post("fechaDesde") && $this->input->post("fechaDesde") <> '' 
                ? formatearFecha_mysql($this->input->post('fechaDesde')) : null;
        $fechaHasta = $this->input->post("fechaHasta") && $this->input->post("fechaHasta") <> ''
                ? formatearFecha_mysql($this->input->post("fechaHasta")) : null;
        $clausulaFechas = $this->input->post("clausulaFechas") && $this->input->post("clausulaFechas") <> ''
                ? $this->input->post("clausulaFechas") : null;
        $codCurso = $this->input->post("codCurso") && $this->input->post("codCurso") <> '' ? $this->input->post('codCurso') : null;
        $cod_plan_academico = $this->input->post("cod_plan_academico") && $this->input->post("cod_plan_academico") <> '' ? $this->input->post('cod_plan_academico') : null;
        $codigo_alumno = $this->input->post("cod_alumno") && $this->input->post("cod_alumno") <> '' ? $this->input->post('cod_alumno') : null;
        $titulo = $this->input->post("titulo") && $this->input->post("titulo") <> '' ? $this->input->post('titulo') : null;
        $cod_tipo_periodo = $this->input->post("cod_tipo_periodo") && $this->input->post("cod_tipo_periodo") <> '' ? $this->input->post('cod_tipo_periodo') : null;
        $cod_mat_periodo = $this->input->post("cod_mat_periodo") && $this->input->post("cod_mat_periodo") <> '' ? $this->input->post('cod_mat_periodo') : null;
        $this->load->helper('alumnos');
        $nombreApellido = formatearNomApeQuery();
        $arrResp = $this->Model_reportes->getReporteBajas($clausulaFechas, $fechaDesde, $fechaHasta, $codCurso, $cod_plan_academico, $codigo_alumno, $titulo, $cod_tipo_periodo, $cod_mat_periodo, $nombreApellido, $arrFiltros);
        if ($this->input->post("action") && $this->input->post("action") == "exportar"){
            $tipoReporte = $this->input->post('tipo_reporte');
            $exp = new export($tipoReporte);
            $exp->setPageFormat("L");
            $this->load->helper('alumnos');
            $columnas = $this->crearColumnas_reporte_bajas();
            foreach ($columnas as $titles){
                $arrTitle[] =  $titles['nombre'];
            }
            $arrWidth = array(20, 30, 65, 30, 60, 30, 0);
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrResp['aaData']);
            $exp->setPDFFontSize(8);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("informe_de_bajas"));
            $exp->setMargin(2, 8);
            //$exp->setContentAcumulable(array('6'));
            $exp->exportar();
        } else {
            echo json_encode($arrResp);
        }
    }
    
    public function inscripciones_y_bajas(){
        $ci = & get_instance();
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['page'] = "reportes/inscripciones_y_bajas";
        $this->load->view('container', $data);
    }
    
    public function listado_inscripciones_y_bajas(){
        $fechaDesde = $this->input->post("fecha_desde") && $this->input->post("fecha_desde") <> '' 
                ? formatearFecha_mysql($this->input->post('fecha_desde')) : null;
        $fechaHasta = $this->input->post("fecha_hasta") && $this->input->post("fecha_hasta") <> ''
                ? formatearFecha_mysql($this->input->post("fecha_hasta")) : null;
        $arrResp = $this->Model_reportes->getReporteInscripcionesYBajas($fechaDesde, $fechaHasta);
        if ($this->input->post("accion") && $this->input->post("accion") == "exportar"){
            $tipoReporte = $this->input->post('tipo_reporte');
            $exp = new export($tipoReporte);
            $exp->setPageFormat("P");
            $arrTitle = array(
                lang("cursos"),
                lang("inscripciones"),
                lang("bajas")
            );
            $arrCocineritos = $arrResp['cocineritos'] ;
            $arrCursosCortos = $arrResp['cursos_cortos'];
            $arrCarreras = $arrResp['carreras'];
            $arrSeminarios = $arrResp['seminarios'];
            $arrBodyCocineritos = array("0" => array());
            $arrBodyCursosCortos = array("0" => array());
            $arrBodyCarreras = array("0" => array());
            $cantidadCocineritos = 0;
            $cantidadCocineritosBajas = 0;
            $cantidadCursosCortos = 0;
            $cantidadCursosCortosBajas = 0;
            $cantidadCarreras = 0;
            $cantidadCarrerasBajas = 0;
            foreach ($arrCocineritos as $cocineritos){
                $arrBodyCocineritos[] = array($cocineritos['titulo'], $cocineritos['inscriptos'], $cocineritos['bajas']);
                $cantidadCocineritos += $cocineritos['inscriptos'];
                $cantidadCocineritosBajas += $cocineritos['bajas'];
            }
            foreach ($arrCursosCortos as $cursoCorto){
                $arrBodyCursosCortos[] = array($cursoCorto['titulo'], $cursoCorto['inscriptos'], $cursoCorto['bajas']);
                $cantidadCursosCortos += $cursoCorto['inscriptos'];
                $cantidadCursosCortosBajas += $cursoCorto['bajas'];
            }
            foreach ($arrCarreras as $carrera){
                $arrBodyCarreras[] = array($carrera['titulo'], $carrera['plan'], $carrera['inscriptos'], $carrera['bajas']);
                $cantidadCarreras += $carrera['inscriptos'];
                $cantidadCarrerasBajas += $carrera['bajas'];
            }
            $cantidadSeminarios = 0;
            $cantidadSeminariosBajas = 0;
            foreach ($arrSeminarios as $seminario){
                $arrBodySeminarios[] = array($seminario['titulo'], $seminario['inscriptos'], $seminario['bajas']);
                $cantidadSeminarios += $seminario['inscriptos'];
                $cantidadSeminariosBajas += $seminario['bajas'];
            }
            if ($tipoReporte == 'pdf'){
                $arrBodyCarreras[0] = array(lang("carreras"), $cantidadCarreras, $cantidadCarrerasBajas);
                $arrBodyCocineritos[0] = array(lang("cocineritos"), $cantidadCocineritos, $cantidadCocineritosBajas);
                $arrBodyCursosCortos[0] = array(lang("cursos_cortos"), $cantidadCursosCortos, $cantidadCursosCortosBajas);
                $arrBodySeminarios[0] = array(lang("seminarios"), $cantidadSeminarios, $cantidadSeminariosBajas);
            }
            
            $cantidadRegistros = $cantidadCarreras + $cantidadCocineritos + $cantidadCursosCortos + $cantidadSeminarios;
            $cantidadRegistrosBajas = $cantidadCarrerasBajas + $cantidadCocineritosBajas + $cantidadCursosCortosBajas;
            $arrWidth = array(100, 40, 40);
            $arrBody = array_merge($arrBodyCarreras, $arrBodyCursosCortos, $arrBodyCocineritos, $arrBodySeminarios);
            if ($tipoReporte == 'pdf'){
                $arrBody[] = array(lang("total"), $cantidadRegistros, $cantidadRegistrosBajas);
            }
            $arrResaltar = array(1, 2 + count($arrCarreras), 3 + count($arrCarreras) + count($arrCursosCortos), 
                4 + count($arrCarreras) + count($arrCursosCortos) + count($arrCocineritos));
            $exp->setTitle($arrTitle);
            $exp->setContent($arrBody);
            $exp->setColumnWidth($arrWidth);
            $exp->setContentResaltar($arrResaltar);
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $periodo = '';
            if ($fechaDesde != ''){
                $periodo .= lang("desde")." ".formatearFecha_pais($fechaDesde);
            }
            if ($fechaHasta != ''){
                $periodo .= " ".lang("al")." ".formatearFecha_pais($fechaHasta);
            }
            if ($periodo == ''){
                $periodo = lang("todas_las_fechas");
            }
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 182, "height" => 4),
                array("txt" => lang("periodo").": ".$periodo, "size" => "8", "align" => "R", "width" => 182, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 182, "height" => 4)
            );
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file, 182);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("reporte_inscripciones_y_bajas"));
            $exp->setMargin(16);
            $exp->exportar();
        } else {
            echo json_encode($arrResp);
        }


    }

    public function cupos_abiertos_curso(){
        $ci = & get_instance();
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['page'] = "reportes/cupos_abiertos_curso";
        //Anda, yo que sé.
        $data['cursos'] = $this->Model_reportes->getPlanesConCuposDisponibles();
        $this->load->view('container', $data);
    }

/*
    Este método es candidato al refactor, le da una estructura a los datos que realmente no hacía falta.
*/
    public function listar_cupos_abiertos_curso(){
        $curso = $this->input->post('curso');
        $response = $this->Model_reportes->getReporteCuposDisponibles($curso);

        $cursos = [];
        $curso = []; 

        $lastCourse = null;
        foreach($response as $comision){
            if($lastCourse == null || ($comision['codigo_curso'] != $lastCourse)){
                $j = 0;
                if($lastCourse != null){
                    $cursos[] = $curso;
                }
                $lastCourse = $comision['codigo_curso'];
                $curso = [];
                $curso['curso'] = $comision['nombre_curso'];
                $curso['tipo_curso'] = $comision['tipo_curso'];
                $curso['comisiones'] = [];
            }
            $com = [];
            $com['nombre'] = $comision['comision'];
            $com['inicio'] = $comision['iniciado'];
            $com['cierre'] = $comision['fecha_cierre'];
            $com['cupo'] = $comision['cupo'];
            $curso['comisiones'][] = $com;
        }
        if($lastCourse != null)
            $cursos[] = $curso;
        if ($this->input->post("accion") && $this->input->post("accion") == "exportar"){
            $tipoReporte = $this->input->post('tipo_reporte');
            $exp = new export($tipoReporte);
            $exp->setPageFormat("P");
            $arrWidth = array(40, 45, 25, 50);
            $arrTitle = array(
                lang("iniciado"),
                lang("comision"),
                lang("cupo"),
                lang("cierre_inscripcion")
            );
            $arrBodyReporte = array();
            $countReporte = 0;
            $cupoReporte = 0;
            $arrResaltar = array();
            $j = 2;
            foreach($cursos as $muchascom)
            {
                $cupoCarrera = 0;
                if($countReporte != 0){
                    $arrBodyReporte[] = array();
                }
                if($tipoReporte == 'pdf')
                    $arrBodyReporte[] = array(wordwrap($muchascom['curso'], $arrWidth[0]/2 + 2));
                else
                    $arrBodyReporte[] = array($muchascom['curso']);
//                $arrResaltar[] = $countReporte +$j -1 ;
                foreach($muchascom['comisiones'] as $cur)
                {
                    $arrBodyReporte[] = array($cur['inicio'], $cur['nombre'], $cur['cupo'], $cur['cierre']);
                    $countReporte++;
                    $cupoCarrera += intval($cur['cupo']);
                    $cupoReporte += intval($cur['cupo']);
                }
                $arrBodyReporte[] = array(lang("total"), $cupoCarrera);
                $arrResaltar[] = $countReporte + $j;
                $j += 3;
            }
            $arrBodyReporte[] = array();
            if($tipoReporte == 'pdf')
            {
                $arrBodyReporte[] = array(lang("total"), $countReporte);
            }

            $exp->setTitle($arrTitle);
            $exp->setContent($arrBodyReporte);
            $exp->setColumnWidth($arrWidth);
            $exp->setContentResaltar($arrResaltar);
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo[] = array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 162, "height" => 4);
            if($curso != '')
                $arrInfo[] = array("txt" => lang("curso").": ".$curso, "size" => "8", "align" => "R", "width" => 162, "height" => 4);
            $arrInfo[] = array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 162, "height" => 4);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file, 172);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("reporte_cupos_abiertos_por_curso"));
            $exp->setMargin(26);
            $exp->exportar();
        }
        else {
            echo json_encode($cursos);
        }
    }

    /*
    * Webservice para poder ver los cupos desde el panel de control.
    */
    public function ws_listar_cupos_abiertos_curso()
    {
        $cursos = isset($_POST['cursos'])?$_POST['cursos']:null;   
        $filiales = isset($_POST['filiales'])?$_POST['filiales']:null;
        $cupos = $this->Model_reportes->reporte_cupos_panelControl($cursos, $filiales);
        echo json_encode($cupos);
    }

    public function reporte_habilitaciones_rematriculacion()
    {
        $this->inicializarReporte(lang("reporte_habilitaciones_rematriculacion"), "reporte_habilitaciones_rematriculacion"); 
    }


    /* Inicio Indice Morosidad*/

    public function imprimir_reporte_indice_morosidad(){
        $html = $this->input->post("html");
        $fechaDesde = $this->input->post("fechaDesde");
        $fechaHasta = $this->input->post("fechaHasta");
        $periodo = $this->input->post("periodo");
        $gastoEingreso = $this->input->post("gastoEingreso");
        $gastos = $this->input->post("gastos");
        $ingresos = $this->input->post("ingresos");
        $rentabilidad = $this->input->post("rentabilidad");
        $data['html'] = $html;
        $data['fechaDesde'] = $fechaDesde;
        $data['fechaHasta'] = $fechaHasta;
        $data['gastoEingreso'] = $gastoEingreso;
        $data['gastos'] = $gastos;
        $data['ingresos'] = $ingresos;
        $data['rentabilidad'] = $rentabilidad;
        $data['periodo'] = $periodo;
        $this->load->view('reportes/impresiones', $data);
    }

    public function crearColumnas_indice_morosidad(){
        $columnas = array(
            array("nombre" => lang('mes'), "campo" => 'mes'),
            array("nombre" => lang('importe'), "campo" => 'importe'),
            array("nombre" => lang('imputado'), "campo" => 'imputado'),
            array("nombre" => lang('saldo'), "campo" => 'saldo'),
            array("nombre" => lang('morosidad'), "campo" => 'morosidad'),
            array("nombre" => lang('imputado_total'), "campo" => 'imputado_total'),
            array("nombre" => lang('morosidad_total'), "campo" => 'morosidad_total')
        );
        return $columnas;
    }

    public function indice_morosidad(){
        $this->lang->load(get_idioma(), get_idioma());
        $ci = & get_instance();
        $session = $ci->session->all_userdata();
        $seccion = $ci->router->uri->uri_string;

        $data['page'] = 'reportes/indice_morosidad';
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];

        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas_indice_morosidad()));
        $data['columns'] = $aoColumnDefs;
        $filial = $this->session->userdata('filial');
        $data['pesos'] = $filial['moneda']['simbolo'];

        $this->load->view('container', $data);
    }

    public function listar_indice_morosidad(){
        $year = $this->input->post('year');
        $Filial = $this->session->userdata("filial");
        $codFilial = $Filial["codigo"];
        $fechaDesde = "{$year}-01-01";
        $fechaHasta = "{$year}-12-31";
        $conexion = $this->load->database($codFilial, true);
        $valores = Vctacte::get_reporte_morosidad_nuevo($conexion, $fechaDesde, $fechaHasta);
        echo json_encode($valores);
    }

    public function exportar_indice_morosidad(){
        $year = $this->input->post('year');
        $Filial = $this->session->userdata("filial");
        $codFilial = $Filial["codigo"];
        $fechaDesde = "{$year}-01-01";
        $fechaHasta = "{$year}-12-31";
        $conexion = $this->load->database($codFilial, true);
        $valores = Vctacte::get_reporte_morosidad_nuevo($conexion, $fechaDesde, $fechaHasta);

        $tipoReporte = $this->input->post('tipo_reporte');
        $exp = new export($tipoReporte);
        $exp->setPageFormat("L");
        $columnas = $this->crearColumnas_indice_morosidad();
        foreach ($columnas as $titles){
            $arrTitle[] =  $titles['nombre'];
        }
        $arrWidth = array(20, 30, 65, 30, 60, 30, 30);
        $usuario = $this->session->userdata("nombre");
        $filial = $this->session->userdata("filial");
        $arrInfo = array(
            array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
            array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 286, "height" => 4)
        );
        $exp->setTitle($arrTitle);
        $exp->setContent($valores);
        $exp->setPDFFontSize(8);
        $exp->setColumnWidth($arrWidth);
        $file = FCPATH."assents\img\logo.jpg";
        $exp->setLogo($file);
        $exp->setInfo($arrInfo);
        $exp->setContentHeight(6);
        $exp->setReportTitle($filial['nombre']." - ".lang("indice_morosidad"));
        $exp->setMargin(2, 8);
        $exp->exportar();
    }

    public function google_adwords_formulario() {
        require_once APPPATH . "libraries/google-api-client/vendor/autoload.php";
        $session = $this->session->all_userdata();
        $seccion = $this->router->uri->uri_string;
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);        
        
        $this->load->model('Model_google_adwords_goal_completions', "", false, $arrConf);
        $this->load->model('Model_publicidad_campanas', "", false, $arrConf);
           
        $this->Model_google_adwords_goal_completions->sincroniza_tabla();
       
        $campanas_filter = $this->Model_publicidad_campanas->buscarCampanasPermitidasParaUsuario($this->session->userdata('codigo_usuario'));
        
        $claves = array('alerta', 'no_hay_datos_para_imprimir_o_exportar', 'matriculas','hoy', 'ayer', 'este_mes', 'ultimo_mes', 'ultimos_30_dias', 'ultima_semana', 'cancelar', 'aceptar', 'rango_perzonalizado');
        $data['lang'] = getLang($claves);
        $data['columns'] = $this->getColumns_google_adwords_formulario();
        $data['campanas_filter'] = $campanas_filter;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['page'] = "reportes/google_adwords_formulario";
        $this->load->view('container', $data);
    }
    
    public function listar_google_adwords_formulario() {
        $this->load->helper("database");
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);        
        $this->load->model('Model_usuario', "", false, array('filial' => 'general'));
        $this->load->model('Model_google_adwords_goal_completions', "", false, $arrConf);
        $this->load->model('Model_publicidad_campanas', "", false, $arrConf);

        $crearColumnas = $this->crearColumnas_google_adwords_formulario();
        $campanas = $this->Model_publicidad_campanas->buscarCampanasCodPermitidasParaUsuario($this->session->userdata('codigo_usuario'));
                
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $campana = isset($_POST['campana']) && $_POST['campana'] <> -1 ? $_POST['campana'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) && $_POST['fecha_desde'] <> '' ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] <> '' ? $_POST['fecha_hasta'] : null;
        $valores = $this->Model_google_adwords_goal_completions->listarGoalCompletionsBusca($arrFiltros, $fechaDesde, $fechaHasta, $campana, $campanas);
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $usuario = $this->Model_usuario->getObjUsuario($this->session->userdata('codigo_usuario'));
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            $linea = 1;
            foreach ($valores['aaData'] as $valor) {
                $arrTemp[] = array(
                    $linea,
                    $valor[0],
                    $valor[1],
                    $valor[2],
                    $valor[3]
                );
                $linea++;
            }
            
            $arrTitle = array(
                lang("codigo"),
                lang("campana"),
                lang("clics"),
                lang("accesos_al_formulario"),
                lang("matriculas")
            );
            $arrWidth = array(20, 110, 60, 54, 50);
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang('google_adwords_formulario'), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang('usuario').": ".$usuario->nombre.' '.$usuario->apellido, "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrTemp);
            $exp->setPDFFontSize(7);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle(lang('google_adwords_formulario'));
            $exp->setMargin(2, 8);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }      
    }
    
    public function crearColumnas_google_adwords_formulario() {
        $columnas = array(
            array("nombre" => lang('campana'), "campo" => 'nombre'),
            array("nombre" => lang('clics'), "campo" => 'clics'),
            array("nombre" => lang('accesos_al_formulario'), "campo" => 'envios'),
            array("nombre" => lang('matriculas'), "campo" => 'matriculados', "sort" => false));
        return $columnas;
    }
    
    public function getColumns_google_adwords_formulario() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas_google_adwords_formulario()));
        return $aoColumnDefs;
    }
    
    public function facebook_ads_campanas() {
        $session = $this->session->all_userdata();
        $seccion = $this->router->uri->uri_string;
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);    
        
        $this->load->model('Model_publicidad_campanas', "", false, $arrConf);
                
        $campanas_filter = $this->Model_publicidad_campanas->buscarCampanasPermitidasParaUsuario($this->session->userdata('codigo_usuario'), 'facebook');
        
        $claves = array('alerta', 'no_hay_datos_para_imprimir_o_exportar', 'datos_facebook_ads_son_actualizados', 'hoy', 'ayer', 'este_mes', 'ultimo_mes', 'ultimos_30_dias', 'ultima_semana', 'cancelar', 'aceptar', 'rango_perzonalizado');
        $data['lang'] = getLang($claves);
        $data['columns'] = $this->getColumns_facebook_ads_campanas();
        $data['campanas_filter'] = $campanas_filter;
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['page'] = "reportes/facebook_ads_campanas";
        $this->load->view('container', $data);
    }
    
    public function listar_facebook_ads_campanas() {
        $this->load->helper("database");
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);        
        $this->load->model('Model_usuario', "", false, array('filial' => 'general'));
        $this->load->model('Model_facebook_ads_datos', "", false, $arrConf);
        $this->load->model('Model_publicidad_campanas', "", false, $arrConf);

        $crearColumnas = $this->crearColumnas_facebook_ads_campanas();
        $campanas = $this->Model_publicidad_campanas->buscarCampanasCodPermitidasParaUsuario($this->session->userdata('codigo_usuario'), 'facebook');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $campana = isset($_POST['campana']) && $_POST['campana'] <> -1 ? $_POST['campana'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) && $_POST['fecha_desde'] <> '' ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] <> '' ? $_POST['fecha_hasta'] : null;
        $valores = $this->Model_facebook_ads_datos->listarFacebookDatosBusca($arrFiltros, $fechaDesde, $fechaHasta, $campana, $campanas);
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $usuario = $this->Model_usuario->getObjUsuario($this->session->userdata('codigo_usuario'));
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            $linea = 1;
            foreach ($valores['aaData'] as $valor) {
                $arrTemp[] = array(
                    $linea,
                    $valor[0],
                    $valor[1],
                    $valor[2],
                    $valor[3]
                );
                $linea++;
            }
            
            $arrTitle = array(
                lang("codigo"),
                lang("campana"),
                lang("alcance"),
                lang("resultados"),
                lang("matriculas")
            );
            $arrWidth = array(20, 100, 70, 54, 50);
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang('facebook_ads_campanas'), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang('usuario').": ".$usuario->nombre.' '.$usuario->apellido, "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrTemp);
            $exp->setPDFFontSize(7);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle(lang('facebook_ads_campanas'));
            $exp->setMargin(2, 8);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }
    
    public function crearColumnas_facebook_ads_campanas() {
        $columnas = array(
            array("nombre" => lang('campana'), "campo" => 'nombre'),
            array("nombre" => lang('alcance'), "campo" => 'alcance'),
            array("nombre" => lang('resultados'), "campo" => 'resultados'),
            array("nombre" => lang('matriculas'), "campo" => 'matriculados', "sort" => false)
        );
        return $columnas;
    }
    
    public function getColumns_facebook_ads_campanas() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas_facebook_ads_campanas()));
        return $aoColumnDefs;
    }
}