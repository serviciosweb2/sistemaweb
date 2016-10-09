<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Matriculas extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_matriculas", "", false, $configMatriculas);
    }

    /**
     * retorna la sugerencia de baja a la vista
     * @access public
     * @return $data
     */
    public function sugerenciabaja() {
        $this->lang->load(get_idioma(), get_idioma());
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'vista_sugerenciabaja'; // pasamos la vista a utilizar como parámetro
        $data['helper'] = 'sugerenciabaja';
        $this->load->view('container', $data);
    }

    /**
     * retorna los valores de la session a la vista
     * @access public
     * @return array $data con los valores
     */
    public function index() {
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $claves = array('estado_matricula_cabecera', 'cantmatriculaciones_matricula_cabecera',"cod_matricula",
            "codigo", "inhabilitar_matriculas", "confirmar_cambiar_estado", "validacion_ok",
            "habilitar_matriculas", "BIEN", "MATRICULA_INHABILITADA", "MATRICULA_HABILITADA",
            "estado_matricula_cabecera", "rematricular_matricula_cabecera", "imprimir_baja_matriculas",
            "cantmatriculaciones_matricula_cabecera", "detalle", "periodos", "fecha_alta",
            "estadovisible_matricula_cabecera", "baja", "estado", "certificada", "finalizada", "ERROR",
            "regularizar_alumnos", "pasar_libres", "observaciones", "letra_habilitada", "modalidad", "intensiva", "normal",
            "letra_inhabilitada", "letra_certificada", "letra_finalizada", "acciones", "modificar_modalidad",
            "letra_prematricula");
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('matriculas');
        $data['columns'] = $this->getColumns();
        $data['page'] = 'matriculas/vista_matriculas';
        $data["seccion"] = $this->seccion;
        $data['seccion_pasar_libres'] = $this->Model_usuario->tienePermisoSeccion("matriculas", "pasar_libres");
        $data['seccion_regularizar_alumnos'] = $this->Model_usuario->tienePermisoSeccion("matriculas", "regularizar_alumnos");
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('cod_alumno'), "campo" => 'alumnos.codigo'),
            array("nombre" => lang('matricula'), "campo" => 'cod_matricula'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('plan_academico'), "campo" => 'general.cursos.nombre_' . get_idioma(), "sort" => FALSE),
            array("nombre" => lang('fecha_alta'), "campo" => 'matriculas.fecha_emision'),
            array("nombre" => lang('detalle'), "sort" => FALSE),
            array("nombre" => lang('cantmatriculaciones_matricula_cabecera'), "campo" => 'cantmatriculaciones', "bVisible" => FALSE),
            array("nombre" => lang('rematricular_matricula_cabecera'), "campo" => 'rematricular', "bVisible" => FALSE),
            array("nombre" => lang('estado'), "campo" => 'estado', "bVisible" => FALSE));
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    /**
     * retorna lista de matriculas para mostrar en index de main panel
     * @access public
     * @return json de listado de matriculas
     */
    public function listar() {
        $crearColumnas = $this->crearColumnas();
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $idioma = $this->session->userdata('idioma');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $valores = $this->Model_matriculas->listarMatriculas($arrFiltros, 1, $idioma, $separador);
        echo json_encode($valores);
    }

    /**
     * llama a cargar formulario con lista de alumnos en combo
     * @access public
     * @return 
     */
    public function frm_Matricula() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_planes_academicos", "", false, $arrConf);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'numeric');
        $this->form_validation->set_rules('cod_plan_academico', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $claves = array("todos", "ERROR", "validacion_ok", "seleccione_financiacion", "NO_HAY_PLANES", "seleccione_modalidad", "matriculado", "no_hay_comisiones_disponibles",
                "SELECCIONE_UNA_OPCION", "alumno_no_puede_matricularse", "NO_HAY_HORARIOS", "intensiva", "normal", "seleccione_comision", "frm_nuevaMatricula_HorariosDeCursado",
                "MATRICULA_GUARDADA", "BIEN", "concepto", "cuota", "vencimiento", "valor", "periodos_dia", "seleccione_una_opcion",
                "horadesde_horario", "horaHasta_horario", "financiacion", "fecha_primer_pago", "no_hay_comisiones_disponibles", "no_hay_comisiones_disponibles_para_el_plan_academico",
                "no_hay_planes_de_pago_definidos_para_esta_seleccion", "alumnos_cobro", "ver_plan_pago", "paga_al_momento",
                "fecha", "medio_cheque_numero_factura", "medio_cheque_emisor_factura", "codigo_cupon", "codigo_autorizacion",
                "terminal", "TARJETA", "TDEBITO", "banco", "tipo_cheque", "DEPOSITO_BANCARIO", "banco", "medio_deposito_cuenta_factura",
                "medio_deposito_transaccion_factura", "caja_cerrada", "cajas_habilitadas_para_este_medio", "recuperando",
                "sin_registros", "no_tiene_cajas_habiertas");
            $cod_alumno = -1;
            $cod_plan_academico = -1;
            $cod_alumno = $this->input->post("cod_alumno") == FALSE || $this->input->post("cod_alumno") == ' ' ? -1 : $this->input->post("cod_alumno");
            $cod_plan_academico = $this->input->post("cod_plan_academico") == FALSE || $this->input->post("cod_alumno") == ' ' ? -1 : $this->input->post("cod_plan_academico");
            $data['cod_alumno'] = $cod_alumno;
            $data['frmLang'] = getLang($claves);
            $data['cod_plan_academico'] = $cod_plan_academico;
            $data['periodos_curso'] = array();
            if ($cod_alumno != -1 && $cod_plan_academico != -1) {
                $data['periodos_curso'] = $this->Model_planes_academicos->getPeriodosPlanAcademico($cod_plan_academico, $cod_alumno, true);
            }
            $data['session_info'] = $this->seccion;
            $data['todosPeriodos'] = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
            $data['capacidadComision'] = $this->Model_configuraciones->getValorConfiguracion(null, 'CapacidadComision');
            $data['metodo_imprimir'] = $this->Model_impresiones->getMetodoImprimirScript($filial['codigo'], 5);
            $data['material_entregado'] = $this->Model_alumnos->getMateriales();
            $data['medios_pago'] = $this->Model_paises->getMediosPagos(true, true, true);
            $data['permite_editar_medios'] = true;
            $this->load->view('matriculas/frm_Matricula', $data);
        }
    }
    
    public function editar_matricula() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'numeric|required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $claves = array("todos", "ERROR", "validacion_ok", "seleccione_financiacion", "NO_HAY_PLANES", "seleccione_modalidad", "matriculado", "no_hay_comisiones_disponibles",
                "SELECCIONE_UNA_OPCION", "alumno_no_puede_matricularse", "NO_HAY_HORARIOS", "intensiva", "normal", "seleccione_comision", "frm_nuevaMatricula_HorariosDeCursado",
                "MATRICULA_GUARDADA", "BIEN", "concepto", "cuota", "vencimiento", "valor", "periodos_dia", "seleccione_una_opcion",
                "horadesde_horario", "horaHasta_horario", "financiacion", "fecha_primer_pago", "no_hay_comisiones_disponibles", "no_hay_comisiones_disponibles_para_el_plan_academico",
                "no_hay_planes_de_pago_definidos_para_esta_seleccion", "alumnos_cobro", "ver_plan_pago", "paga_al_momento",
                "fecha", "medio_cheque_numero_factura", "medio_cheque_emisor_factura", "codigo_cupon", "codigo_autorizacion",
                "terminal", "TARJETA", "banco", "tipo_cheque", "DEPOSITO_BANCARIO", "banco", "medio_deposito_cuenta_factura",
                "medio_deposito_transaccion_factura", "caja_cerrada", "cajas_habilitadas_para_este_medio", "recuperando",
                "sin_registros", "no_tiene_cajas_habiertas");            
            $cod_alumno = $this->input->post("cod_alumno");
            $cod_plan_academico = $this->input->post("cod_plan_academico");
            $data['frmLang'] = getLang($claves);
            $data['cod_alumno'] = $cod_alumno;
            $data['cod_plan_academico'] = $cod_plan_academico;
            $arrMatriculasPeriodos = $this->Model_alumnos->getMatriculasPeriodosAlumno($cod_alumno, $cod_plan_academico);
            $conexion = $this->load->database($filial['codigo'], true);
            $myMatricula = new Vmatriculas($conexion, $arrMatriculasPeriodos[0]['cod_matricula']);
            $data['observaciones_old'] = $myMatricula->observaciones;
            $data['documentacion_entregada_anterior'] = $myMatricula->getDocumentacionEntragada();
            $data['materiales_entregada_anterior'] = $myMatricula->getMaterialesEntregados();            
            $data['materiales_entregados_anterior'] = 
            $data['session_info'] = $this->seccion;
            $data['documentacion'] = $this->Model_alumnos->getDocumentacion($cod_plan_academico);
            $data['material_entregado'] = $this->Model_alumnos->getMateriales();
            $data['medios_pago'] = $this->Model_paises->getMediosPagos(true, true, true);;
            $data['medio_actual'] = $myMatricula->get_medio_pago_cuotas();
            $this->load->view('matriculas/frm_editarMatricula', $data);
        }
    }
    
    public function guardarEdicionMatricula(){
        $arrResp = array();
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->model("Model_matriculas", "", false, $arrConf);        
        $cod_alumno = $this->input->post("cod_alumno");
        $cod_plan_academico = $this->input->post("cod_plan_academico");        
        $material = $this->input->post("material") && 
                $this->input->post('material') != "null" &&
                $this->input->post("material") != '' && 
                is_array($this->input->post("material"))
                        ? $this->input->post("material") : array();
        $documentacion = $this->input->post("documentacion") && 
                $this->input->post("documentacion") != "null" && 
                $this->input->post("documentacion") != '' &&
                is_array($this->input->post("documentacion"))
                        ? $this->input->post("documentacion") : array();
        $obs = $this->input->post("observaciones");
        $medio_pago_cuotas = $this->input->post("medio_pago_cuotas") && $this->input->post("medio_pago_cuotas") <> '' ? $this->input->post("medio_pago_cuotas") : null;
        $arrMatriculasPeriodos = $this->Model_alumnos->getMatriculasPeriodosAlumno($cod_alumno, $cod_plan_academico);
        $conexion = $this->load->database($filial['codigo'], true);
        $myMatricula = new Vmatriculas($conexion, $arrMatriculasPeriodos[0]['cod_matricula']);
        $conexion->trans_begin();
        
        $myMatricula->setDocumentacionEntragada($documentacion);
        
        $myMatricula->setMaterialEntregado($material);
        if ($medio_pago_cuotas != null){
            $myMatricula->set_medio_pago_cuotas($medio_pago_cuotas);
        }
        $myMatricula->observaciones = $obs;       
        $myMatricula->guardarMatriculas();
        if($conexion->trans_status()){
            $conexion->trans_commit();
            $arrResp = array("respuesta" => lang("datos_actualizados_correctamente"), "codigo" => 0);
        } else {
            $conexion->trans_rollback();
            $arrResp = array("respuesta" => lang("ocurrio_error"), "codigo" => 1);
        }
        echo json_encode($arrResp);
    }

    public function getPlanesAcademicos() {
        $cod_alumno = $this->input->post("cod_alumno");
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_alumnos", "", false, $arrConfig);
        $planes = $this->Model_alumnos->getPlanesAcademicos($cod_alumno, true);
        echo json_encode($planes);
    }

    /**
     * retorna lista de comisiones habilitadas para el plan academico
     * @access public
     * @return json de comisiones
     */
    public function getComisiones() {
        $cod_plan_academico = $this->input->post("cod_plan_academico");
        $codigo_periodo = $this->input->post("periodo");
        $cod_alumno = $this->input->post("cod_alumno");
        $modalidad = $this->input->post("modalidad");
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo" => $cod_plan_academico,
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_planes_academicos", "", false, $arrConfig);
        $comisiones = $this->Model_planes_academicos->getComisionesDisponiblesMatricular($codigo_periodo, $modalidad);
        echo json_encode($comisiones);
    }

    /**
     * retorna lista de planes habilitados de la comision
     * @access public
     * @return json de planes
     */
    public function getPlanesPago() {
        $filial = $this->session->userdata('filial');
        $periodos = $this->input->post('periodos');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_comisiones", "", false, $arrConfig);
        $formasDePago = $this->Model_comisiones->getPlanesVigentesMatricular($periodos);
        echo json_encode($formasDePago);
    }

    /**
     * retorna lista de cuotas de comision
     * @access public
     * @return json de cuotas
     */
    public function getCuotasPlan() {
        $this->load->helper('formatearCuotas');
        $filial = $this->session->userdata('filial');
        $arrConfig["codigo_filial"] = $filial["codigo"];
        $this->load->model("Model_planes_pagos", "", false, $arrConfig);
        $this->load->model("Model_conceptos", "", false, $arrConfig);
        $cod_plan = $this->input->post('codigo');
        $orden = array(
            array('campo' => 'orden',
                'orden' => 'ASC'),
            array('campo' => 'financiacion.codigo',
                'orden' => 'ASC'),
            array('campo' => 'nro_cuota',
                'orden' => 'ASC')
        );
        $arrcuotas = $this->Model_planes_pagos->getCuotasPlan($cod_plan, $orden, 'habilitada');
        $codconceptos = array();
        foreach ($arrcuotas as $key => $value) {
            $codconceptos[] = $key;
        }
        $conceptos = $this->Model_conceptos->getConceptos(null, $codconceptos);
        $cuotas = formatearCuotas($arrcuotas, $conceptos);
        echo json_encode($cuotas);
    }

    /**
     * retorna guarda todos los datos de la matricula
     * @access public
     * @return json de $resultado
     */
    public function editar(){
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $cod_alumno = $this->input->post('cod_alumno');        
        $alumno = $this->Model_alumnos->getAlumno($cod_alumno);
        $documento = $alumno->documento;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('guardarmat_alumno'), 'required|integer');
        $this->form_validation->set_rules('observaciones', '', 'max_length[2000]');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $resultado = $this->Model_matriculas->guardarMatricula($nuevaMatricula);            
            if($this->input->post("documentacion") && is_array($this->input->post("documentacion"))){
                $this->Model_alumnos->saveDocumentacion($resultado["custom"]["cod_matricula"], $this->input->post("documentacion"));
            }           
            if($this->input->post("material") && is_array($this->input->post("material"))){
                $this->Model_alumnos->saveDocumentacion($resultado["custom"]["cod_matricula"], $this->input->post("material"));
            }
            echo json_encode($resultado);
        }
    }

    public function guardar(){
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $cod_alumno = $this->input->post('cod_alumno');        
        $nuevaMatricula = array();        
        $cod_plan_academico = $periodos = $this->input->post('cod_plan_academico');
        $periodos = $this->input->post('periodos');
        $periodoplan = array('cod_plan' => $cod_plan_academico, 'periodos' => $periodos);
        $jsperiodos = json_encode($periodoplan);
        $cursoperiodo = array('cod_alumno' => $cod_alumno, 'periodos' => $periodos);
        $jscursoperiodo = json_encode($cursoperiodo);
        $alumno = $this->Model_alumnos->getAlumno($cod_alumno);
        $documento = $alumno->documento;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('guardarmat_alumno'), 'required|integer');
        $this->form_validation->set_rules('cod_plan_academico', lang('plan_academico'), 'required|integer|validarMatricularCursoPeriodo[' . $jscursoperiodo . ']'); //validar plan academico vigente?
        $this->form_validation->set_rules('planes', lang('guardarmat_plan'), 'required|max_length[10]|integer|validarPlanPagoPeriodos[' . $jsperiodos . ']');
        $this->form_validation->set_rules('observaciones', '', 'max_length[2000]');
        if ($this->input->post('numero_cupon') && $this->input->post("numero_cupon") <> '') {
            $this->form_validation->set_rules('numero_cupon', '', 'alpha_numeric|validarCupon[' . $documento . ']');
        }
        $this->form_validation->set_rules('medio_pago_cuotas', lang("medio_de_pago_de_las_cuotas"), 'required');
        $this->form_validation->set_rules('codigo-financiacion[]', lang('detalleplan_financiacion'), 'required');
        $planpago = $this->input->post('planes');
        $nuevaMatricula['cobrarmatricula'] = $this->input->post("cobrarmatricula");
        $codfinanciaciones = $this->input->post('codigo-financiacion');
        $codconceptos = $this->input->post('plan-concepto');
        $fechaprimerpago = $this->input->post('fechaPrimerPago');
        for ($i = 0; $i < count($codfinanciaciones); $i++) {
            $_POST['cod_concepto' . $i] = $codconceptos[$i];
            $arrDatosPlan = array('plan' => $planpago, 'financiacion' => $codfinanciaciones[$i], 'fecha' => $fechaprimerpago[$i]);
            $jsDatosPlan = json_encode($arrDatosPlan);
            $this->form_validation->set_rules('cod_concepto' . $i, lang('detalleplan_fecha'), 'validarPrimerPagoMatricula[' . $jsDatosPlan . ']');
            $nuevaMatricula['ctacte']['financiaciones'][$i]['cod_financiacion'] = $codfinanciaciones[$i];
            $nuevaMatricula['ctacte']['financiaciones'][$i]['cod_concepto'] = $codconceptos[$i];
            $nuevaMatricula['ctacte']['financiaciones'][$i]['fecha_primer_pago'] = $fechaprimerpago[$i] != '' ? formatearFecha_mysql($fechaprimerpago[$i]) : '';
        }
        $cod_medio = $this->input->post("medio_pago_matricula");
        $this->form_validation->set_rules('medio_pago_matricula', lang('medio_cobro'), 'required');
        $this->form_validation->set_rules('cod_caja', lang('medio_caja_cobro'), 'required|validarCobroCajaMedio[' . $cod_medio . ']|validarCajaUsuario[' . $usuario . ']');
        $pago = array();
        $pago['cod_caja'] = $this->input->post("cod_caja");
        switch ($this->input->post("medio_pago_matricula")) {
            case 1:
                $pago['medio_cobro']['medio_cobro'] = $this->input->post("medio_pago_matricula");
                break;
            case 3:
                $cod_terminal = $this->input->post('pos_tarjeta');
                $this->form_validation->set_rules('tarjetas', lang('medio_tarjeta_tipo_cobro'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'required');
                $this->form_validation->set_rules('pos_tarjeta', lang('terminal'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_cupon', lang('medio_tarjeta_cupon_cobro'), 'required');
                $pago['medio_cobro']['pos_tarjeta'] = $cod_terminal;
                $pago['medio_cobro']['cod_tipo'] = $this->input->post("tarjetas");
                $pago['medio_cobro']['cod_autorizacion'] = $this->input->post("medio_tarjeta_autorizacion");
                $pago['medio_cobro']['cod_terminal'] = $this->input->post("pos_tarjeta");
                $pago['medio_cobro']['cupon'] = $this->input->post("medio_tarjeta_cupon");
                if ($cod_terminal != '') {
                    $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'validarCuponTerminal[' . $cod_terminal . ']');
                    $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'validarAutorizacionTerminal[' . $cod_terminal . ']');
                    $pago['medio_cobro']['medio-tajeta-cupon'] = $this->input->post("medio-tajeta-cupon");
                    $pago['medio_cobro']['cod_autorizacion'] = $this->input->post("medio_tarjeta_autorizacion");
                }
                break;

            case 4:
                $this->form_validation->set_rules('medio_cheque_banco', lang('medio_cheque_banco_cobro'), 'required');
                $this->form_validation->set_rules('medio_cheque_tipo', lang('medio_cheque_tipo_cobro'), 'required');
                $this->form_validation->set_rules('medio_cheque_fecha', lang('medio_cheque_fecha_cobro'), 'required|validarFechaFormato');
                $this->form_validation->set_rules('medio_cheque_numero', lang('medio_cheque_numero_cobro'), 'required|numeric');
//                $this->form_validation->set_rules('medio_cheque_emisor', lang('medio_cheque_emisor_cobro'), 'required');
                $pago['medio_cobro']['cod_banco_emisor'] = $this->input->post('medio_cheque_banco');
                $pago['medio_cobro']['tipo_cheque'] = $this->input->post('medio_cheque_tipo');
                $pago['medio_cobro']['fecha_cobro'] = $this->input->post('medio_cheque_fecha');
                $pago['medio_cobro']['nro_cheque'] = $this->input->post('medio_cheque_numero');
                $pago['medio_cobro']['emisor'] = $this->input->post('medio_cheque_emisor');
                break;

            case 6:
                $this->form_validation->set_rules('medio_deposito_banco', lang('medio_deposito_banco_cobro'), 'required');
                $this->form_validation->set_rules('medio_deposito_fecha', lang('medio_deposito_fecha_cobro'), 'required|validarFechaFormato');
                $this->form_validation->set_rules('medio_deposito_transaccion', lang('medio_deposito_transaccion_cobro'), 'required|numeric');
                $this->form_validation->set_rules('medio_deposito_cuenta', lang('medio_deposito_cuenta_cobro'), 'required');
                $pago['medio_cobro']['cod_banco'] = $this->input->post('medio_deposito_banco');
                $pago['medio_cobro']['fecha_hora'] = $this->input->post('medio_deposito_fecha');
                $pago['medio_cobro']['nro_transaccion'] = $this->input->post('medio_deposito_transaccion');
                $pago['medio_cobro']['cuenta_nombre'] = $this->input->post('medio_deposito_cuenta');                
                break;

            case 7:
                $this->form_validation->set_rules('medio_transferencia_banco', lang('medio_transferencia_banco_cobro'), 'required');
                $this->form_validation->set_rules('medio_transferencia_fecha', lang('medio_transferencia_fecha_cobro'), 'required|validarFechaFormato');
                $this->form_validation->set_rules('medio_transferencia_numero', lang('medio_transferencia_numero_cobro'), 'required|numeric');
                $this->form_validation->set_rules('medio_transferencia_cuenta', lang('medio_transferencia_cuenta_cobro'), 'required');
                $pago['medio_cobro']['cod_banco'] = $this->input->post('medio_transferencia_banco');
                $pago['medio_cobro']['fecha_hora'] = $this->input->post('medio_transferencia_fecha');
                $pago['medio_cobro']['cuenta_nombre'] = $this->input->post('medio_transferencia_cuenta');
                $pago['medio_cobro']['nro_transaccion'] = $this->input->post('medio_transferencia_numero');                
                break;
            case 8:
                $cod_terminal = $this->input->post('pos_tarjeta');
                $this->form_validation->set_rules('debito', lang('medio_tarjeta_tipo_cobro'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'required');
                $this->form_validation->set_rules('pos_tarjeta', lang('terminal'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_cupon', lang('medio_tarjeta_cupon_cobro'), 'required');
                $pago['medio_cobro']['pos_tarjeta'] = $cod_terminal;
                $pago['medio_cobro']['cod_tipo'] = $this->input->post("debito");
                $pago['medio_cobro']['cod_autorizacion'] = $this->input->post("medio_tarjeta_autorizacion");
                $pago['medio_cobro']['cod_terminal'] = $this->input->post("pos_tarjeta");
                $pago['medio_cobro']['cupon'] = $this->input->post("medio_tarjeta_cupon");
                if ($cod_terminal != '') {
                    $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'validarCuponTerminal[' . $cod_terminal . ']');
                    $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'validarAutorizacionTerminal[' . $cod_terminal . ']');
                    $pago['medio_cobro']['medio-tajeta-cupon'] = $this->input->post("medio-tajeta-cupon");
                    $pago['medio_cobro']['cod_autorizacion'] = $this->input->post("medio_tarjeta_autorizacion");
                }
                break;

        }
        
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $nuevaMatricula['cod_usuario_creador'] = $usuario;
            $nuevaMatricula['observaciones'] = $this->input->post('observaciones');
            $nuevaMatricula['cupon'] = $this->input->post('numero_cupon');
            $nuevaMatricula['cod_alumno'] = $cod_alumno;
            $nuevaMatricula['cod_plan_academico'] = $cod_plan_academico;
            $nuevaMatricula['periodos'] = $periodos;
            $nuevaMatricula['ctacte']['cod_plan'] = $planpago;
            $nuevaMatricula['medio_pago'] = $this->input->post("medio_pago_matricula"); // en las matriculaciones que no sean por boleto bancario se paga la matricula en el momento (por pedido de Alejandro)
            $nuevaMatricula['pago'] = $pago;
            $medio_pago_cuotas = $this->input->post("medio_pago_cuotas");
            //Listado de fechas de vencimiento modificadas
            $nuevaMatricula['filas_vencimientos'] = explode(',',$this->input->post("filas_vencimientos"));
            $documentacion = $this->input->post("documentacion") && is_array($this->input->post("documentacion"))
                    ? $this->input->post("documentacion") : null;
            $materiales = $this->input->post("material") && is_array($this->input->post("material"))
                    ? $this->input->post("material") : null;
            $resultado = $this->Model_matriculas->guardarMatricula($nuevaMatricula, $documentacion, $materiales, $medio_pago_cuotas);            
            echo json_encode($resultado);
        }
    }
    
    
    /**
     * formulario baja matricula, toda las inscripciones a un plan academico
     * @access public
     * @return 
     */
    public function frm_baja_matriculas() {
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $arrConfig = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arrConfig);
        $this->load->model("Model_planes_academicos", "", false, $arrConf);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->helper("alumnos");
        $cod_alumno = $this->input->post('cod_alumno');
        $cod_plan_academico = $this->input->post('cod_plan_academico');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'required|numeric');
        $this->form_validation->set_rules('cod_plan_academico', lang('codigo'), 'required|numeric|validarMatriculasBaja[' . $cod_alumno . ']');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $Alumno = $this->Model_alumnos->getAlumno($cod_alumno);
            $nbreAlumno = formatearNombreApellido($Alumno->nombre, $Alumno->apellido);
            $motivos = $this->Model_matriculas_periodos->getMotivosBaja();
            $matriculas = $this->Model_alumnos->getMatriculasPeriodosAlumno($cod_alumno, $cod_plan_academico);
            $data['nombre_plan'] = $this->Model_planes_academicos->getNombre($cod_plan_academico);
            $data['cod_alumno'] = $cod_alumno;
            $data['cod_plan_academico'] = $cod_plan_academico;
            $data['matriculas_periodos'] = $matriculas;
            $data['nombreAlumno'] = $nbreAlumno;
            $data['motivos'] = $motivos;
            $this->load->view('matriculas/frm_baja_matriculas', $data);
        }
    }

    /**
     * inhabilita una matricula periodo
     * @access public
     * @return json con la matricula que ah cambiado de estado
     */
    public function bajaMatriculasPeriodos() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arrConfig);
        $arrmatriculasper = $this->input->post('cod_matriculas_periodos');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_matriculas_periodos[]', lang('codigo'), 'required|numeric|validarMatriculaPeriodoBaja|validarMatriculaPeriodoHabilitada');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $respuesta = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $datos = array(
                'arrmatriculasper' => $arrmatriculasper,
                'motivo' => $this->input->post('motivo'),
                'comentario' => $this->input->post('comentario'),
                'cod_usuario' => $this->session->userdata('codigo_usuario'),
                'cod_alumno' => $this->input->post('cod_alumno')
            );
            $respuesta = $this->Model_matriculas_periodos->baja($datos);
        }
        echo json_encode($respuesta);
    }

    /**
     * formulario alta matriculas, toda las inscripciones a un plan academico
     * @access public
     * @return 
     */
    public function frm_alta_matriculas() {
        $filial = $this->session->userdata('filial');
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $arrConfig = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arrConfig);
        $this->load->model("Model_planes_academicos", "", false, $arrConf);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $this->load->helper("alumnos");
        $cod_alumno = $this->input->post('cod_alumno');
        $cod_plan_academico = $this->input->post('cod_plan_academico');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'numeric|validarAlumnoHabilitado');
        $this->form_validation->set_rules('cod_plan_academico', lang('codigo'), 'numeric|validarMatriculasAlta[' . $cod_alumno . ']');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $Alumno = $this->Model_alumnos->getAlumno($cod_alumno);
            $nbreAlumno = formatearNombreApellido($Alumno->nombre, $Alumno->apellido);
            $matriculas = $this->Model_alumnos->getMatriculasPeriodosAlumno($cod_alumno, $cod_plan_academico);
            $data['nombre_plan'] = $this->Model_planes_academicos->getNombre($cod_plan_academico);
            $data['cod_alumno'] = $cod_alumno;
            $data['cod_plan_academico'] = $cod_plan_academico;
            $data['matriculas_alta'] = $matriculas;
            $data['nombreAlumno'] = $nbreAlumno;
            $this->load->view('matriculas/frm_alta_matriculas', $data);
        }
    }

    /**
     * habilita una matriculas_periodos
     * @access public
     * @return json con la matricula que ah cambiado de estado
     */
    public function altaMatriculasPeriodos() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arrConfig);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_matriculas_periodos[]', lang('codigo'), 'required|numeric|validarMatriculaPeriodoAlta');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $datos = array(
                'arrmatriculasper' => $this->input->post('cod_matriculas_periodos'),
                'motivo' => 11,
                'comentario' => $this->input->post('comentario'),
                'cod_usuario' => $this->session->userdata('codigo_usuario'),
                'cod_alumno' => $this->input->post('cod_alumno')
            );
            $respuesta = $this->Model_matriculas_periodos->alta($datos);
            echo json_encode($respuesta);
        }
    }

    /**
     * carga la vista del estado academico
     * @access public
     * @return vista form cursado
     */
    public function frm_cursado() {
        $filial = $this->session->userdata('filial');
        $arr = array("filial" => $filial);
        $arrConf = array("codigo_filial" => $filial["codigo"]);
        $this->load->helper("alumnos");
        $this->load->library('form_validation');
        $cod_alumno = $this->input->post('cod_alumno');
        $cod_plan_academico = $this->input->post('cod_plan_academico');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'numeric');
        $this->form_validation->set_rules('cod_plan_academico', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $this->load->model("Model_estadoacademico", "", false, $arr);
            $this->load->model("Model_planes_academicos", "", false, $arrConf);
            $this->load->model("Model_alumnos", "", false, $arrConf);
            $this->load->model("Model_configuraciones", "", false, $arrConf);
            $objAlumno = $this->Model_alumnos->getAlumno($cod_alumno);
            $data['cod_alumno'] = $cod_alumno;
            $data['cod_plan_academico'] = $cod_plan_academico;
            $data["nombreAlumno"] = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
            $data["curso"] = $this->Model_planes_academicos->getCurso($cod_plan_academico);
            $data["estados"] = $this->Model_estadoacademico->getEstadosMaterias();
            $data["periodos"] = $this->Model_alumnos->getDetalleMateriasPlan($cod_alumno, $cod_plan_academico, null, true);
            $data['capacidadComision'] = $this->Model_configuraciones->getValorConfiguracion(null, 'CapacidadComision');
            $this->load->view('matriculas/frm_cursado', $data);
        }
    }

    /**
     * retorna los horarios de la materia en la comision
     * @access public
     * @return json
     */
    public function getHorarioMateriaComision() {
        $filial = $this->session->userdata('filial');
        $cod_materia = $this->input->post("codigo_materia");
        $cod_comision = $this->input->post("codigo_comision");
        $config = array(
            "codigo_filial" => $filial['codigo'],
            "codigo" => $cod_comision);
        $this->load->model("Model_comisiones", "", false, $config);
        $horariosMaterias = $this->Model_comisiones->getHorario($cod_materia);
        echo json_encode($horariosMaterias);
    }

    /**
     * guarda el cambio de horario de la materia y comision
     * @access public
     * @return json con el array
     */
    public function guardarCursado() {
        $filial = $this->session->userdata('filial');
        $arr = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $arr);
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $cod_estado_academico = $this->input->post('cod_estado_academico');
        $destino = $this->input->post('comision_destino');
        $arrCambiar['cod_comision'] = $destino;
        $arrCambiar['cod_estado_academico'] = $cod_estado_academico;
        $arrCambiar['cod_usuario'] = $cod_usuario;
        echo json_encode($this->Model_estadoacademico->guardarInscripciones($arrCambiar, "cambio_comision"));
    }

    /**
     * Esta function está siendo utilizada desde un web services
     */
    public function reporte() {
        set_time_limit(1200);
        ini_set("memory_limit", "512M");
        $valor = $_POST['id_filial'];
        $arrResp = $this->Model_matriculas->getReporteAlumnosActivos($valor);
        echo json_encode($arrResp);
        
    }

    public function reporte_deserciones($idFilial, $fechaDesde) {
        $fechaDesde = $fechaDesde == 0 ? null : $fechaDesde;
        $arrResp = $this->Model_matriculas->getReporteDeserciones($idFilial);
        echo json_encode($arrResp);
    }

    public function matriculas_fecha_cantidad() {
        $idFilial = $this->input->post("id_filial");
        $fechaDesde = $this->input->post("fecha_desde");
        $fechaDesde = $fechaDesde == 0 ? null : $fechaDesde;
        $arrResp = $this->Model_matriculas->getMatriculasFechaCantidad($idFilial, $fechaDesde);
        echo json_encode($arrResp);
    }

    public function getReporteMatriculas() {
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $searchFields = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $cursos = isset($_POST['cursos']) ? $_POST['cursos'] : null;
        $estado = $this->input->post('baja'); //== 1 ? 'habilitada': 'inhabilitada';//ver
        $estado = $estado === '-1' || $estado === '' || $estado == false ? null : $estado;
        $arrResp = $this->Model_matriculas->getReporteMatriculas($idFilial, $arrLimit, $arrSort, $search, $searchFields, $fechaDesde, $fechaHasta, $cursos, $estado);
        echo json_encode($arrResp);
    }

    public function getHorarioComision() {
        $filial = $this->session->userdata('filial');
        $cod_comision = $this->input->post('codigo-comision');
        $arrConfig = array(
            "codigo" => $cod_comision,
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_comisiones", "", false, $arrConfig);
        $horario = $this->Model_comisiones->getHorario();

        echo json_encode($horario);
    }

    public function getDetallePlan() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_planes_financiacion", "", false, $arrConfig);
        $this->load->library('form_validation');
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
            $detalles['financiaciones'][$i]['cod_financiacion'] = $codfinanciaciones[$i];
            $detalles['financiaciones'][$i]['cod_concepto'] = $codconceptos[$i];
            $detalles['financiaciones'][$i]['fecha_primer_pago'] = $fechaprimerpago[$i] != '' ? formatearFecha_mysql($fechaprimerpago[$i]) : null;
        }
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $detalles['cod_plan'] = $planpago;
            $detalles['moneda'] = $filial['moneda'];
            $datosdetalle = $this->Model_planes_financiacion->getDetallesFinanciaciones($detalles);
            echo json_encode($datosdetalle);
        }
    }

    public function getPeriodosPlanAcademico() {
        $cod_plan_academico = $this->input->post("cod_plan_academico");
        $codigo_alumno = $this->input->post("cod_alumno") == false || $this->input->post("cod_alumno") == 'null' ? -1 : $this->input->post("cod_alumno");
        $arrayCodigos = array(
            'cod_plan_academico' => $cod_plan_academico
        );
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_plan_academico', lang('codigo'), 'required');
        if ($codigo_alumno != -1) {
            $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'validarMatricularAlumnoCurso[' . json_encode($arrayCodigos) . ']');
        }

        if ($this->form_validation->run() == false) {
            $respuesta = array(
                'codigo' => 0,
                'msgerror' => validation_errors()
            );
        } else {
            $filial = $this->session->userdata('filial');
            $arrConfig = array(
                "codigo" => $cod_plan_academico,
                "codigo_filial" => $filial["codigo"]
            );

            $periodos = array();
            $codigo_alumno = $codigo_alumno == '-1' ? null : $codigo_alumno;
            $this->load->model("Model_planes_academicos", "", false, $arrConfig);
            $periodos = $this->Model_planes_academicos->getPeriodosPlanAcademico($cod_plan_academico, $codigo_alumno, true);
            $respuesta = array(
                'codigo' => 1,
                'data' => $periodos
            );
        }
        echo json_encode($respuesta);
    }

    public function getMatriculasPeriodosCursoAlumno() {
        $codigo_plan = $this->input->post("cod_plan_academico");
        $codigo_alumno = $this->input->post("cod_alumno");
        $agruparMatriculas = $this->input->post("agrupar_matriculas") && $this->input->post("agrupar_matriculas") == 1;
        $filial = $this->session->userdata('filial');
        $periodos = array();
        $arrConfig = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConfig);
        $periodos = $this->Model_alumnos->getMatriculasPeriodosAlumno($codigo_alumno, $codigo_plan, $agruparMatriculas);

        echo json_encode($periodos);
    }

    /**
     * formulario cambiar estado estadoacademico
     * @access public
     * @return 
     */
    public function frm_cambioEstadoMateria() {
        $filial = $this->session->userdata('filial');
        $arr = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $arr);
        $cod_estadoacademico = $this->input->post('codigo');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarMatriculaPeriodoHabilitada');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $data['estadosAca'] = $this->Model_estadoacademico->getEstadosCambioMateria($cod_estadoacademico);
            $myEstadoAcademico = $this->Model_estadoacademico->getEstadoAcademico($cod_estadoacademico);
            $data['objEstado'] = $myEstadoAcademico;
            $data['asistenciasPendientes'] = $myEstadoAcademico->getAsistenciasPendientesCaragar(true);
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_configuraciones", "", false, $config);
            $data['asistencia_regular'] = $this->Model_configuraciones->getValorConfiguracion(null, 'PorcentajeAsistenciaRegular');
            $this->load->view('matriculas/frm_cambioEstadoMateria', $data);
        }
    }

    public function cambiarEstadoMateria() {
        session_method();
        $filial = $this->session->userdata('filial');
        $arr = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $arr);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $resultado = '';
        $this->form_validation->set_rules('estado', lang('estado_academico_estado'), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $cod_estado = $this->input->post('codigo');
            $estado = $this->input->post('estado');
            $motivo = $this->input->post('motivo');
            $comentarios = $this->input->post('comentario');
            $cambioestado = array(
                'cod_estado' => $cod_estado,
                'estado' => $estado,
                'motivo' => $motivo,
                'comentario' => $comentarios,
                'cod_usuario' => $codUsuario
            );
            $resultado = $this->Model_estadoacademico->cambiarEstadoMateria($cambioestado);
            echo json_encode($resultado);
        }
    }

    function reimprimirMatriculas() {
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $codAlumno = $this->input->post("cod_alumno");
        $codPlanAcademico = $this->input->post("cod_plan_academico");
        $arrMatriculasPeriodos = $this->Model_alumnos->getMatriculasPeriodosAlumno($codAlumno, $codPlanAcademico);
        $myAlumno = $this->Model_alumnos->getAlumno($codAlumno);
        $data = array("matriculas_periodos" => $arrMatriculasPeriodos, "myAlumno" => $myAlumno);
        $this->load->view("matriculas/reimprimir_matriculas", $data);
    }

    private function getPasarRegular() {
        $columnas = array(
            array("order" => 'alumnos.codigo'),
            array("order" => 'alumno_nombre'),
            array("order" => 'general.cursos.nombre_' . get_idioma()),
            array("order" => 'general.materias.nombre_' . get_idioma()),
            //Ticket 4771 -mmori- agrego columna
            array("order" => 'nombre_comision'),
            array("order" => "estadoacademico.porcasistencia")
        );
        return $columnas;
    }

    function listar_pasar_a_regular() {
        $arrColumnas = $this->getPasarRegular();
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $configMatriculas);
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $idioma = $this->session->userdata('idioma');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $arrColumnas[$_POST['iSortCol_0']]['order'] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $fechaDesde = isset($_POST['fecha_desde']) && $_POST['fecha_desde'] <> '' ? formatearFecha_mysql($_POST['fecha_desde']) : null;
        $fechaHasta = isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] <> '' ? formatearFecha_mysql($_POST['fecha_hasta']) : null;
        $curso = isset($_POST['curso']) && $_POST['curso'] <> -1 ? $_POST['curso'] : null;
        $materia = isset($_POST['materia']) && $_POST['materia'] <> -1 ? $_POST['materia'] : null;
        
        $comision = isset($_POST['comision']) && $_POST['comision'] <> -1 ? $_POST['comision'] : null;
        $todos = isset($_POST['todos']) ? $_POST['todos'] : false;
        
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $valores = $this->Model_estadoacademico->listarEstadoAcademicoDataTable($arrFiltros, $idioma, $separador, false, true,
                $curso, $materia, $fechaDesde, $fechaHasta, $comision, $todos);
        echo json_encode($valores);
    }

    function listar_pasar_a_libres() {
        $arrColumnas = $this->getPasarRegular();
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $configMatriculas);
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $idioma = $this->session->userdata('idioma');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $arrColumnas[$_POST['iSortCol_0']]['order'] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $valores = $this->Model_estadoacademico->listarEstadoAcademicoDataTable($arrFiltros, $idioma, $separador, true);
        echo json_encode($valores);
    }

    function frm_pasar_a_libres() {
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $claves = array("BIEN", "ERROR", "validacion_ok", "debe_seleccionar_un_alumno", "volver", "pasar_libres");
        $data['langFrm'] = getLang($claves);
        $data['page'] = 'matriculas/frm_pasar_a_libres';
        $data ["seccion"] = array("titulo" => "matriculas", "categoria" => "academicos", "subcategoria" => "pasar_libres");
        $this->load->view('container', $data);
    }

    function frm_pasar_a_regular() {
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("filial" => $filial["codigo"]);
        $conexion = $this->load->database($filial['codigo'], true);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $claves = array("BIEN", "ERROR", "validacion_ok", "debe_seleccionar_un_alumno", "volver", "regularizar_alumnos", "falta_cargar",
            "todos", "todas", "recuperando");
        
        $data['todos'] = false; 
        if ($this->input->get("todos"))
            $data['todos'] = true; 
        
        $data['frmLang'] = getLang($claves);
        $data['page'] = 'matriculas/frm_pasar_regular';
        $data ["seccion"] = array("titulo" => "matriculas", "categoria" => "academicos", "subcategoria" => "regularizar_alumnos");
        $data['arrCursos'] = Vcursos::listarCursos($conexion, array("estado" => "habilitado"));
        $data['arrMaterias'] = Vmaterias::listarMaterias($conexion);
        $data['arrComisiones'] = Vcomisiones::getComisiones($conexion, array("comisiones.estado" => "habilitado"));
        $this->load->view('container', $data);
    }

    function pasar_a_libres() {
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $configMatriculas);
        $arrResp = array();
        if ($this->Model_estadoacademico->cambiarAEstadoLibre($this->input->post("estadosacademicos")))
            $arrResp['success'] = "success";
        else
            $arrResp['error'] = "error";
        echo json_encode($arrResp);
    }

    function pasar_a_regular() {
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_estadoacademico", "", false, $configMatriculas);
        $arrResp = array();
        if ($this->Model_estadoacademico->cambiarAEstadoRegular($this->input->post("estadosacademicos")))
            $arrResp['success'] = "success";
        else
            $arrResp['error'] = "error";
        echo json_encode($arrResp);
    }

    public function api_estadoAcademico() {
        $filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $estadosAcademicos = array();
        $arr = array("filial" => array('codigo' => $filial));
        $arrConf = array("codigo_filial" => $filial);
        $this->load->helper("alumnos");
        $this->load->library('form_validation');
        $this->load->model("Model_estadoacademico", "", false, $arr);
        $this->load->model("Model_planes_academicos", "", false, $arrConf);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $objAlumno = $this->Model_alumnos->getAlumno($cod_alumno);
        $matriculas = $objAlumno->getMatriculas();
        foreach ($matriculas as $k => $matricula) {
            $cod_plan_academico = $matricula['cod_plan_academico'];
            $estadosAcademicos[$k]['cod_alumno'] = $cod_alumno;
            $estadosAcademicos[$k]['cod_plan_academico'] = $cod_plan_academico;
            $estadosAcademicos[$k]["nombreAlumno"] = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
            $estadosAcademicos[$k]["curso"] = $this->Model_planes_academicos->getCurso($cod_plan_academico);
            $estadosAcademicos[$k]["estados"] = $this->Model_estadoacademico->getEstadosMaterias();
            $estadosAcademicos[$k]["periodos"] = $this->Model_alumnos->getDetalleMateriasPlan($cod_alumno, $cod_plan_academico, null);
        }
        echo json_encode($estadosAcademicos);
    }

    public function frm_cambiarModalidad() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arrConfig);
        $this->load->helper("alumnos");
        $cod_mat_per = $this->input->post('codigo');
        $modalidadActual = isset ($_POST['modalidad']) ? $this->input->post('modalidad') : false;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $matricula = $this->Model_matriculas_periodos->getNombreMatriculaPeriodo($cod_mat_per);
            $alumno = $this->Model_matriculas_periodos->getAlumno($cod_mat_per);
            $nbreAlumno = formatearNombreApellido($alumno->nombre, $alumno->apellido);
            $modalidades = $this->Model_matriculas_periodos->getModalidades($cod_mat_per, $modalidadActual);
            $data["materias"] = $this->Model_matriculas_periodos->getDetalleMateriasCambioModalidad($cod_mat_per, $modalidades[0]['codigo']);
            $data['codigo'] = $cod_mat_per;
            $data['matricula_periodo'] = $matricula;
            $data['modalidades'] = $modalidades;
            $data['modalidadActual'] = $modalidadActual;
            $data['nombreAlumno'] = $nbreAlumno;
            $this->load->view('matriculas/frm_cambiarModalidad', $data);
        }
    }

    public function cambiarModalidad() {
        session_method();
        $filial = $this->session->userdata('filial');
        $arr = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arr);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $resultado = '';
        $estadosaca = $this->input->post('cod_estado_academico');
        $comisiones = $this->input->post('comision_destino');
        $arrComisiones = array();
        foreach ($estadosaca as $key => $value) {
            $_POST['estaca' . $key] = $estadosaca[$key];
            $_POST['comision' . $key] = $comisiones[$key];
            $this->form_validation->set_rules('estaca' . $key, lang(''), '');
            $arrComisiones[] = array('estaca' => $estadosaca[$key], 'comision' => $comisiones[$key]);
        }
        $this->form_validation->set_rules('cod_matricula_periodo', lang('codigo'), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $datos = array(
                'cod_mat_periodo' => $this->input->post('cod_matricula_periodo'),
                'modalidad' => $this->input->post('modalidad'),
                'cod_usuario' => $codUsuario,
                'arrcomisiones' => $arrComisiones
            );
            $resultado['codigo'] = $this->Model_matriculas_periodos->cambiarModalidad($datos) ? 1 : 0;
            echo json_encode($resultado);
        }
    }

    public function getDetalleMateriasCambioModalidad() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array("filial" => $filial);
        $this->load->model("Model_matriculas_periodos", "", false, $arrConfig);
        $cod_mat_per = $this->input->post('codigo');
        $modalidad = $this->input->post('modalidad');
        $resultado = $this->Model_matriculas_periodos->getDetalleMateriasCambioModalidad($cod_mat_per, $modalidad);
        echo json_encode($resultado);
    }

    public function agregar_comentarios_matriculas() {
        $this->load->library('form_validation');
        $cod_alumno = $this->input->post('cod_alumno');
        $cod_plan_academico = $this->input->post('cod_plan_academico');
        $this->form_validation->set_rules('cod_alumno', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $claves = array("validacion_ok", "ERROR", "eliminacion_ok", "sin_comentarios");
            $data['langFrm'] = getLang($claves);
            $data['cod_alumno'] = $cod_alumno;
            $data['cod_plan_academico'] = $cod_plan_academico;
            $data['comentarios'] = $this->Model_matriculas->getComentarios($cod_alumno, $cod_plan_academico);
            $data['idioma'] = $this->session->userdata('idioma');
            $this->load->view('matriculas/frm_comentarios_matriculas', $data);
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
            $data_post['comentario'] = $this->input->post('comentario');
            $data_post['cod_alumno'] = $this->input->post('cod_alumno');
            $data_post['cod_plan_academico'] = $this->input->post('cod_plan_academico');
            $data_post['cod_usuario'] = $usuario;
            $resultado = $this->Model_matriculas->guardarComentario($data_post);
        }
        echo json_encode($resultado);
    }

    public function bajaComentario() {
        $usuario = $this->session->userdata('codigo_usuario');
        $data_post = array();
        $data_post['codigo'] = $this->input->post('codigo');
        $resultado = $this->Model_matriculas->bajaComentario($data_post);
        echo json_encode($resultado);
    }

    //script para actualizar comentarios de matriculas.
    public function actualizarMatriculasComentarios() {
        $arrResp = $this->Model_matriculas->actualizarMatriculasComentarios();
        echo '<pre>';
        print_r($arrResp);
        echo '<pre>';
    }

    public function cambios_estado_academico() {
        $data = array();
        $this->load->helper("alumnos");
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $idioma = $this->session->userdata("idioma");
        $data['page'] = 'matriculas/cambios_estado_academico';
        $data["seccion"] = $this->seccion;
        $data['arrEstadosAcademicos'] = Vestadoacademico::getEstadoAcademicoDetalles($conexion, $idioma, true, Vmatriculas_periodos::getEstadoHabilitada(), Vmatriculas::getEstadoHabilitada(), Vestadoacademico::getEstadoCursando(), false);
        $data['porcasistencia'] = Vconfiguracion::getValorConfiguracion($conexion, null, "PorcentajeAsistenciaRegular");
        $this->load->view('container', $data);
    }

    function cambiar_estado_academico() {
        $resp = array();
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $codUsuario = $this->session->userdata("codigo_usuario");
        $cod_estado_academico = $this->input->post("cod_estado_academico");
        $estado = $this->input->post("estado") == "regular" ? Vestadoacademico::getEstadoRegular() : Vestadoacademico::getEstadoRecursa();
        $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);
        $cambio = $myEstadoAcademico->guardarCambioEstado($estado, $codUsuario);
        if ($cambio === true) {
            $resp['success'] = "success";
            $resp['estado'] = $cambio;
        } else {
            $resp['error'] = $cambio;
        }
        echo json_encode($resp);
    }

    function documentacionPlan(){
        $filial = $this->session->userdata('filial');
        $plan = $_POST["plan_academico"];
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $arrConf);
        $documentacion = $this->Model_alumnos->getDocumentacion($plan);
        echo json_encode($documentacion);
    }

    public function getMatriculasAlumnosSelect() {
        $filial = $this->session->userdata('filial');
        $buscar = $this->input->post('cod_alumno');
        $matriculas = $this->Model_matriculas->getMatriculasAlumnosSelect($buscar);
        echo json_encode($matriculas);
    }

    /**
     * api que devuelve al campus las matriculas periodo de un alumno que cursa onile.
     * @access public
     *
     * @return json $array_matriculas
     */
    function api_getMatriculaPeriodo(){
        $cod_alumno = (string)$_POST['codigo'];
        $cod_filial = (string)$_POST['filial'];
        $conexion = $this->load->database($cod_filial, true);
        $filial = new Vfiliales($conexion, $cod_filial);
        $idioma = $filial->idioma;

        $alumno = new Valumnos($conexion, $cod_alumno);
        $arrayMatriculas = $alumno->getMatriculas();
        $arrOnline = array();
        //recorro los arrays para ver cuales son matriculas de cursos online
        $j = 0;
        foreach ($arrayMatriculas as $matricula){

            //creo el objeto Vmatricula con los codigos de cada matricula.
            $objMatricula = new Vmatriculas($conexion, $matricula['codigo']);

            //me fijo q pertenezca a un curso online y obtengo las matriculas periodos
            if($objMatricula->isOnline($cod_filial)){
                $curso = $objMatricula->getCurso();
                $cursObj = new Vcursos($conexion, $curso[0]['codigo']);
                switch ($idioma){
                    case 'es':
                        $nombreCurso = $cursObj->nombre_es;
                        break;
                    case 'pt':
                        $nombreCurso = $cursObj->nombre_pt;
                        break;
                    case 'in':
                        $nombreCurso = $cursObj->nombre_in;
                        break;
                    default:
                        $nombreCurso = $cursObj->nombre_es;
                        break;
                }
                $arrOnline['curso'.$j]['nombre'] = array();
                $arrOnline['curso'.$j]['nombre']=$nombreCurso;
                $arrayMatPeriodos = $objMatricula->getMatriculasPeriodo();

                $i = 0;
                foreach ($arrayMatPeriodos as $matPeriodo){
                    $matPeriodoObj = new Vmatriculas_periodos($conexion, $matPeriodo['codigo']);
                    $arrOnline['curso'.$j]['matPeriodo'.$i] = array();
                    $arrOnline['curso'.$j]['matPeriodo'.$i]['codigo'] = $matPeriodoObj->getCodigo();
                    $arrOnline['curso'.$j]['matPeriodo'.$i]['estado'] = $matPeriodoObj->estado;
                    $arrOnline['curso'.$j]['matPeriodo'.$i]['periodo'] = $matPeriodoObj->cod_tipo_periodo;
                    $arrayEstadoAcademicos = $matPeriodoObj->getEstadosAcademicos();
                    foreach ($arrayEstadoAcademicos as $estadoAcademico){
                        $estadoAcademicoObj = new Vestadoacademico($conexion, $estadoAcademico['codigo']);
                        $arrOnline['curso'.$j]['matPeriodo'.$i]['grupos'] = array();
                        $materia = new Vmaterias($conexion, $estadoAcademicoObj->codmateria);

                        //    $grupo = $materia->getGrupo($materia->getCodigo(), $objMatricula->cod_plan_academico);
                        //    if($grupo != 0)
                        //    array_push($arrOnline['curso'.$j]['matPeriodo'.$i]['grupos'],$grupo);
                    }

                    $i++;
                }

            }
            $j++;
        }
        echo json_encode($arrOnline);
    }

}



/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
