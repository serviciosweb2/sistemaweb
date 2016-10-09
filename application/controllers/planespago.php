<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Planespago extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_planes_pagos", "", FALSE, $config);
        $this->load->model("Model_configuraciones", "", false, $config);
    }

    public function index() {
        $validar_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $claves = array("codigo", "confirmacion_plan", "HABILITADO", "INHABILITADO", "HABILITAR", "INHABILITAR", "INHABILITA_PLAN",
            "HABILITA_PLAN", "INHABILITACORRECTO_PLAN", "HABILITACORRECTO_PLAN", "PLAN_GUARDO_CORRECTAMENTE", "BIEN",
            "plan_duplicado_correctamente");
        $data['lang'] = getLang($claves);
        $data['columns'] = $this->getColumns();
        $data['menuJson'] = getMenuJson('planespago');
        $data['page'] = 'planes_pago/vista_planes_pago';
        $data['seccion'] = $validar_session;
        $conexion = $this->load->database($filial['codigo'], true);
        $data['arrPlanesAcademicos'] = Vplanes_academicos::getPlanesAcademicosFilial($conexion, $filial['codigo'], get_idioma());
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $muestraPeriodo = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
        $campocurso = $muestraPeriodo == '1' ? lang('curso_periodo_plan') : lang('nombre_curso');
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo', "sort" => TRUE),
            array("nombre" => lang('nombre'), "campo" => 'nombre', "sort" => TRUE),
            array("nombre" => lang('fecha_inicio'), "campo" => 'fechainicio'),
            array("nombre" => lang('fecha_vigencia'), "campo" => 'fechavigencia'),
            array("nombre" => $campocurso, "campo" => 'general.cursos.nombre_' . get_idioma()),
            array("nombre" => lang('CursosPeriodos'), "campo" => 'CursosPeriodos', "sort" => false),
            array("nombre" => lang('estado_plan'), "campo" => 'baja', "bVisible" => FALSE),
            array("nombre" => lang('estado_plan'), "sort" => FALSE)
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
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $fechaInicioDesde = isset($_POST['fecha_inicio_desde']) && $_POST['fecha_inicio_desde'] <> ''
            ? formatearFecha_mysql($_POST['fecha_inicio_desde']) : null;
        $fechaInicioHasta = isset($_POST['fecha_inicio_hasta']) && $_POST['fecha_inicio_hasta'] <> ''
            ? formatearFecha_mysql($_POST['fecha_inicio_hasta']) : null;
        $fechaVigenciaDesde = isset($_POST['fecha_vigencia_desde']) && $_POST['fecha_vigencia_desde'] <> ''
            ? formatearFecha_mysql($_POST['fecha_vigencia_desde']) : null;
        $fechaVigenciaHasta = isset($_POST['fecha_vigencia_hasta']) && $_POST['fecha_vigencia_hasta'] <> ''
            ? formatearFecha_mysql($_POST['fecha_vigencia_hasta']) : null;
        $periodo = isset($_POST['periodo']) && $_POST['periodo'] <> -1 ? $_POST['periodo'] : null;
        $baja = isset($_POST['baja']) && $_POST['baja'] <> -1 ? $_POST['baja'] : null;
        $modalidad = isset($_POST['modalidad']) && $_POST['modalidad'] <> -1 ? $_POST['modalidad'] : null;
        $planAcademico = isset($_POST['plan_academico']) && $_POST['plan_academico'] <> -1
            ? $_POST['plan_academico'] : null;
        $valores = $this->Model_planes_pagos->listarPlanesPagoDataTable($arrFiltros, $planAcademico, $modalidad, $periodo, $baja,
            $fechaInicioDesde, $fechaInicioHasta, $fechaVigenciaDesde, $fechaVigenciaHasta);
        if (isset($_POST['formato']) && ($_POST['formato'] == 'csv' || $_POST['formato'] == 'pdf')){
            $muestraPeriodo = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
            $campocurso = $muestraPeriodo == '1' ? lang('curso_periodo_plan') : lang('nombre_curso');
            $arrTemp = array();
            foreach ($valores['aaData'] as $valor){
                $estado = $valor[6] == 0 ? lang("HABILITADO") : lang("INHABILITADO");
                if ($_POST['formato'] == 'pdf'){
                    $arrTemp[] = array(
                        substr($valor[0], 0, 20),
                        $valor[1],
                        $valor[2],
                        $valor[3],
                        substr($valor[4], 0, 35),
                        substr($valor[5], 0, 35),
                        $estado
                    );
                } else {
                    $arrTemp[] = array(
                        $valor[0], $valor[1], $valor[2], $valor[3], $valor[4], $valor[5], $estado
                    );
                }
            }
            $exp = new export($_POST['formato']);
            $exp->setColumnWidth(array(16, 35, 30, 33, 60, 60, 30));
            $exp->setTitle(array(lang('codigo'), lang('nombre'), lang('fecha_inicio'), lang('fecha_vigencia'),
                $campocurso, lang('CursosPeriodos'), lang('estado_plan')));
            $exp->setContent($arrTemp);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }

    public function frm_plan_pago() {
        $this->load->library('form_validation');
        $arg = array();
        $filial = $this->session->userdata('filial');
        $arg["codigo_filial"] = $filial['codigo'];
        $this->load->model("Model_planes_cursos_periodos", "", false, $arg);
        $this->load->model("Model_cursos", "", false, $arg);
        $cod_plan = $this->input->post('codigo');
        $data['moneda'] = $filial["moneda"];
        $data['alertas'] = '';
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $plan = $this->Model_planes_pagos->getPlan($cod_plan);
            if ($this->input->post('clonar') && $this->input->post('clonar') == $cod_plan){
                $myClon = $plan->clonar();
                $myClon->nombre .= " (copy)";
                $data['plan'] = $myClon;
                $data['actualizar_periodos'] = true;
            } else {
                $data['plan'] = $plan;
                $data['actualizar_periodos'] = false;
            }
            $periodos = $this->Model_configuraciones->getValorConfiguracion(1);
            foreach ($periodos as $key => $periodo) {
                if ($cod_plan != -1) {
                    if ($periodo['baja'] == 1 && $key <> $plan->periodo) {
                        unset($periodos[$key]);
                    }
                } else {
                    if ($periodo['baja'] == 1) {
                        unset($periodos[$key]);
                    }
                }
            }
            $data['periodicidad'] = $this->Model_configuraciones->traducirPeriodos($periodos);
            $arrdescuento = $this->Model_configuraciones->getValorConfiguracion(22);
            $data['descuento_condicionado'] = $arrdescuento['activo'];
            $claves = array("PLAN_GUARDADO_CORRECTAMENTE", "todos", "detalles", "acciones", "eliminar", "formatearcuotas_cuota", "planpago_descuento",
                "intensiva", "normal", "valor_total_curso_no_coincide", "nombre_del_plan_es_requerido", "plan_academico_es_requerido",
                "periodo_plan_es_requerido", "precio_de_lista_de_matricula_es_requerido", "precio_de_lista_del_curso_es_requerido",
                "precio_lista_matricula_o_precio_lista_curso_no_pueden_ser_cero", "recuperando", "seleccione_modalidad", "importe_neto",
                "fecha_limite_es_obligatorio", "con_XXX_de_descuento", "seleccione_concepto", "valor_curso", "recargo_financiacion",
                "al_momento_de_matricular", "fecha_fin", "el_valor_de_descuento_no_debe_ser_vacio", "sin_fecha_limite_planpago", "con_fecha_limite_planpago");
            $data['langFrm'] = getLang($claves);
            $data["cursos"] = $this->Model_cursos->getCursosHabilitados(true, null, 0, null, $filial['codigo']);
            $data["muestraPeriodo"] = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
            $data['arrPeriodosPlan'] = $this->Model_planes_pagos->getPeriodosPlan($plan);
            $data['arrConceptosPrecios'] = $this->Model_planes_pagos->getConceptosPrecios($plan);
            $data['arrFinanciaciones'] = $this->Model_planes_pagos->getFinanciaciones(Vfinanciacion::getEstadoHabilitada());
            $data['arrCursosAsignados'] = $this->Model_planes_pagos->getPlanesAcademicosAsignados($plan);
            $data['separadorDecimal'] = $filial['moneda']['separadorDecimal'];
            $data['puedeModificar'] = $data["plan"]->getCodigo() > -1 ? count($plan->getVigenciasPresupuesto()) == 0 : true;
            $data['plan_original']= $cod_plan;
            $this->load->view('planes_pago/frm_plan_pago', $data);
        }
    }


    public function frm_nuevo_plan_pago() {
        $this->load->library('form_validation');
        $arg = array();
        $filial = $this->session->userdata('filial');
        $arg["codigo_filial"] = $filial['codigo'];
        $this->load->model("Model_planes_cursos_periodos", "", false, $arg);
        $this->load->model("Model_cursos", "", false, $arg);
        $cod_plan = $this->input->post('codigo');
        $data['moneda'] = $filial["moneda"];
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $plan = $this->Model_planes_pagos->getPlan($cod_plan);
            if ($this->input->post('clonar') && $this->input->post('clonar') == $cod_plan){
                $myClon = $plan->clonar();
                $myClon->nombre .= " (copy)";
                $data['plan'] = $myClon;
                $data['actualizar_periodos'] = true;
            } else {
                $data['plan'] = $plan;
                $data['actualizar_periodos'] = false;
            }
            $periodos = $this->Model_configuraciones->getValorConfiguracion(1);
            foreach ($periodos as $key => $periodo) {
                if ($cod_plan != -1) {
                    if ($periodo['baja'] == 1 && $key <> $plan->periodo) {
                        unset($periodos[$key]);
                    }
                } else {
                    if ($periodo['baja'] == 1) {
                        unset($periodos[$key]);
                    }
                }
            }
            $data['periodicidad'] = $this->Model_configuraciones->traducirPeriodos($periodos);
            $arrdescuento = $this->Model_configuraciones->getValorConfiguracion(22);
            $data['descuento_condicionado'] = $arrdescuento['activo'];
            $claves = array("PLAN_GUARDADO_CORRECTAMENTE", "todos", "detalles", "acciones", "eliminar", "formatearcuotas_cuota", "planpago_descuento",
                "intensiva", "normal", "valor_total_curso_no_coincide", "nombre_del_plan_es_requerido", "plan_academico_es_requerido",
                "periodo_plan_es_requerido", "precio_de_lista_de_matricula_es_requerido", "precio_de_lista_del_curso_es_requerido",
                "precio_lista_matricula_o_precio_lista_curso_no_pueden_ser_cero", "recuperando", "seleccione_modalidad", "importe_neto",
                "fecha_limite_es_obligatorio", "con_XXX_de_descuento", "seleccione_concepto", "valor_curso", "recargo_financiacion",
                "al_momento_de_matricular", "fecha_fin", "el_valor_de_descuento_no_debe_ser_vacio", "sin_fecha_limite_planpago", "con_fecha_limite_planpago");
            $data['langFrm'] = getLang($claves);
            $data["cursos"] = $this->Model_cursos->getCursosHabilitados(true, null, 0, null, $filial['codigo']);
            $data["muestraPeriodo"] = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
            $data['arrPeriodosPlan'] = $this->Model_planes_pagos->getPeriodosPlan($plan);
            $data['arrConceptosPrecios'] = $this->Model_planes_pagos->getConceptosPrecios($plan);
            $data['arrFinanciaciones'] = $this->Model_planes_pagos->getFinanciaciones(Vfinanciacion::getEstadoHabilitada());
            $data['arrCursosAsignados'] = $this->Model_planes_pagos->getPlanesAcademicosAsignados($plan);
            $data['separadorDecimal'] = $filial['moneda']['separadorDecimal'];
            $data['puedeModificar'] = $data["plan"]->getCodigo() > -1 ? count($plan->getVigenciasPresupuesto()) == 0 : true;
            $data['plan_original']= $cod_plan;
            $this->load->view('planes_pago/frm_nuevo_plan_pago', $data);
        }
    }

    public function frm_modificar_plan_pago() {
        $this->load->library('form_validation');
        $arg = array();
        $data['alertas'] = '';
        $data['puedeModificar'] = true;
        $filial = $this->session->userdata('filial');
        $arg["codigo_filial"] = $filial['codigo'];
        $this->load->model("Model_planes_cursos_periodos", "", false, $arg);
        $this->load->model("Model_cursos", "", false, $arg);
        $cod_plan = $this->input->post('codigo');
        $data['moneda'] = $filial["moneda"];
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $plan = $this->Model_planes_pagos->getPlan($cod_plan);
            if ($this->input->post('clonar') && $this->input->post('clonar') == $cod_plan){
                $myClon = $plan->clonar();
                $myClon->nombre .= " (copy)";
                $data['plan'] = $myClon;
                $data['actualizar_periodos'] = true;
            } else {
                $data['plan'] = $plan;
                $data['actualizar_periodos'] = false;
            }
            $periodos = $this->Model_configuraciones->getValorConfiguracion(1);
            foreach ($periodos as $key => $periodo) {
                if ($cod_plan != -1) {
                    if ($periodo['baja'] == 1 && $key <> $plan->periodo) {
                        unset($periodos[$key]);
                    }
                } else {
                    if ($periodo['baja'] == 1) {
                        unset($periodos[$key]);
                    }
                }
            }
            $data['periodicidad'] = $this->Model_configuraciones->traducirPeriodos($periodos);
            $arrdescuento = $this->Model_configuraciones->getValorConfiguracion(22);
            $data['descuento_condicionado'] = $arrdescuento['activo'];
            $claves = array("PLAN_GUARDADO_CORRECTAMENTE", "todos", "detalles", "acciones", "eliminar", "formatearcuotas_cuota", "planpago_descuento",
                "intensiva", "normal", "valor_total_curso_no_coincide", "nombre_del_plan_es_requerido", "plan_academico_es_requerido",
                "periodo_plan_es_requerido", "precio_de_lista_de_matricula_es_requerido", "precio_de_lista_del_curso_es_requerido",
                "precio_lista_matricula_o_precio_lista_curso_no_pueden_ser_cero", "recuperando", "seleccione_modalidad", "importe_neto",
                "fecha_limite_es_obligatorio", "con_XXX_de_descuento", "seleccione_concepto", "valor_curso", "recargo_financiacion",
                "al_momento_de_matricular", "fecha_fin", "el_valor_de_descuento_no_debe_ser_vacio", "sin_fecha_limite_planpago", "con_fecha_limite_planpago");
            $data['langFrm'] = getLang($claves);
            $data["cursos"] = $this->Model_cursos->getCursosHabilitados(true, null, 0, null, $filial['codigo']);
            $data["muestraPeriodo"] = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
            $data['arrPeriodosPlan'] = $this->Model_planes_pagos->getPeriodosPlan($plan);
            $data['arrConceptosPrecios'] = $this->Model_planes_pagos->getConceptosPrecios($plan);
            $data['arrFinanciaciones'] = $this->Model_planes_pagos->getFinanciaciones(Vfinanciacion::getEstadoHabilitada());
            $data['arrCursosAsignados'] = $this->Model_planes_pagos->getPlanesAcademicosAsignados($plan);
            $data['separadorDecimal'] = $filial['moneda']['separadorDecimal'];
            $data['puedeModificar'] = $data["plan"]->getCodigo() > -1 ? count($plan->getVigenciasPresupuesto()) == 0 : true;
            $data['plan_original']= $cod_plan;
            $data['puedeModificar'] = true;
            if($data["plan"]->getCodigo() > -1) {
                $data['puedeModificar'] = count($plan->getVigenciasPresupuesto()) == 0;
            }
            if($data["plan"]->getCodigo() > -1 && $this->Model_planes_pagos->getCantidadMatriculasPlanPago($filial['codigo'], $cod_plan)) {
                $data['puedeModificar'] = false;
                $data['alertas'] = lang('no_se_pueden_realizar_modificaciones_en_el_plan_de_pago_que_tiene_matriculas');
            }
            $this->load->view('planes_pago/frm_modificar_plan_pago', $data);
        }
    }


    public function guardar() {
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $mensaje = '';
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_fin = $this->input->post('fecha_fin');
        $nuevo = $this->input->post("nuevo");

        $this->form_validation->set_rules("codigo_plan", lang('codigo_plan'), 'required');
        // Valida que el plan de pago
        if($nuevo === true) {
            $this->form_validation->set_rules('nombre_plan', lang('planpago_nombre'), 'required|validarNombrePlan');
        }
        $this->form_validation->set_rules('cursos', lang('plan_academico'), 'required');
        $this->form_validation->set_rules('matricula_precio_lista', lang('matricula_precio_lista'), 'required|numeric');
        $this->form_validation->set_rules('curso_precio_lista', lang('curso_precio_lista'), 'required|numeric');
        $this->form_validation->set_rules('periodos', lang('periodos'), 'required');
        $this->form_validation->set_rules('financiaciones', lang('financiaciones'), 'required|validarFinanciaciones');
        if ($fecha_inicio != '' && $fecha_fin != '') {
            $this->form_validation->set_rules('fecha_inicio', lang('fecha_inicio'), 'validarFechaFin_FechaInicio[' . $fecha_fin . ']');
        }
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors . $mensaje,
                'errNo' => '',
            );
        } else {
            $data_post = array();
            $data_post['plan']['codigo'] = $this->input->post("codigo_plan");
            $data_post['plan']['cod_usuario'] = $usuario;
            $data_post['plan']['nombre_plan'] = $this->input->post("nombre_plan");
            $data_post['plan']['descuento_condicionado'] = $this->input->post("descuento_condicionado");
            $data_post['plan']['fecha_inicio'] = $this->input->post("fecha_inicio");
            $data_post['plan']['fecha_fin'] = $this->input->post("fecha_fin");
            $data_post['plan']['plan_academico'] = $this->input->post("cursos");
            $data_post['plan']['matricula_precio_lista'] = $this->input->post('matricula_precio_lista');
            $data_post['plan']['curso_precio_lista'] = $this->input->post('curso_precio_lista');
            $data_post['plan']['periodicidad'] = $this->input->post('periodicidad');
            $data_post['periodos'] = $this->input->post('periodos');
            $data_post['financiacion'] = $this->input->post('financiaciones');
            $resultado = $this->Model_planes_pagos->guardarPlan($data_post);
        }
        echo json_encode($resultado);
    }

    public function getFinanciacionPlan() {
        $cod_plan = $this->input->post('codigo');
        $orden = array(
            array('campo' => 'codigo_concepto', 'orden' => 'desc'),
            array('campo' => 'nro_cuota', 'orden' => 'asc'),
        );
        $cuotas = $this->Model_planes_pagos->getCuotasPlan($cod_plan, $orden, Vfinanciacion::getEstadoHabilitada());
        echo json_encode($cuotas);
    }

    public function getDetalleNuevo() {
        $this->load->library('form_validation');
        $resultado = '';
        $this->form_validation->set_rules('valor_neto', lang('planpago_valorcurso'), 'required|numeric');
        $this->form_validation->set_rules('cuotas_finaciacion', lang('planpago_financiacion'), 'required|validarCuotasPlan');
        $this->form_validation->set_rules('valor_descuento', lang('planpago_descuento'), 'numeric|validarMontoDescuento');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else if (trim($this->input->post('valor_descuento')) === ''){
            $resultado = array(
                'codigo' => '0',
                'msgerror' => lang('el_valor_de_descuento_no_debe_ser_vacio').". ".lang('indicar_cero_para_no_utilizar_descuento'),
                'errNo' => '',
            );
        } else {
            $cuotas = $this->input->post('cuotas_finaciacion');
            $valorNeto = $this->input->post('valor_neto');
            $descuento = $this->input->post('valor_descuento');
            $resultado = $this->Model_planes_pagos->getDetalleNuevo($cuotas, $valorNeto, $descuento);
        }
        echo json_encode($resultado);
    }

    public function getDetallePlanes($idFilial, $codPlan) {
        $arrResp = $this->Model_planes_pagos->detallePlanPago($idFilial, $codPlan);
        echo json_encode($arrResp);
    }

    public function cambiarEstado() {
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        $resultado = '';
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $dejarCambiarEstado = $this->Model_planes_pagos->getPresupuestosVigentes($codigo);
            if ($dejarCambiarEstado == 1) {
                $resultado = $this->Model_planes_pagos->cambiarEstado($codigo);
            } else {
                $resultado = array('codigo' => 0,
                    'msgerror' => lang('cambiar_estado_plan'));
            }
            echo json_encode($resultado);
        }
    }

    public function getPlanesPago($idFilial, $vigente = null) {
        $arrResp = $this->Model_planes_pagos->getPlanesPago($idFilial, $vigente);
        echo json_encode($arrResp);
    }

    public function getPeriodosCurso() {
        $filial = $this->session->userdata('filial');
        $cod_curso = $this->input->post('cod_curso');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_planes_academicos", "", false, $arrConfig);
        $tiposPeriodos = $this->Model_planes_academicos->getPeriodosPlanAcademico($cod_curso, null, true);
        $muestraPlan = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos');
        $arrResp['muestra_peridos'] = $muestraPlan;
        $arrResp['periodos_plan'] = $tiposPeriodos;
        echo json_encode($arrResp);
    }

    public function guardarConfiguracionPeriodicidad() {
        $this->load->library('form_validation');
        $resultado = '';
        $usuario = $this->session->userdata('codigo_usuario');
        $periodos = $this->Model_configuraciones->getValorConfiguracion(1);
        $cod_periodicidad = $this->input->post('codigo');
        $baja = $this->input->post('baja');
        $valor = $this->input->post('valor');
        if ($cod_periodicidad == -1) {
            $this->form_validation->set_rules('valor', lang('valor'), 'required|numeric');
            $this->form_validation->set_rules('unidadTiempo', lang('unidad_tiempo'), 'required|validarPeriodicidad[' . $valor . ']');
            if ($this->form_validation->run() == false) {
                $errors = validation_errors();
                $resultado = array(
                    'codigo' => '0',
                    'msgerror' => $errors,
                    'errNo' => '',
                );
            } else {
                $periodos[] = array(
                    "valor" => $this->input->post('valor'),
                    "unidadTiempo" => $this->input->post('unidadTiempo'),
                    "codigo" => 0,
                    "baja" => 0
                );
                $resultado = $this->Model_planes_pagos->guardarConfigPeriodicidad($periodos, $usuario);
            }
        } else {
            foreach ($periodos as $key => $periodo) {
                if ($periodo['codigo'] == $cod_periodicidad) {
                    $periodos[$key]['baja'] = $baja == 1 ? 1 : 0;
                }
            }
            $resultado = $this->Model_planes_pagos->guardarConfigPeriodicidad($periodos, $usuario);
        }
        echo json_encode($resultado);
    }
}