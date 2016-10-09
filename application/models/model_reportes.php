<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_reportes extends CI_Model {

    private $codigofilial;

    public function __construct($codigoFilial = null) {
        parent::__construct();
        $this->codigofilial = $codigoFilial;
    }


    private function getObjectReport($reportName, CI_DB_mysqli_driver $conexion) {
        $myReporte = new reportes_sistema($conexion, $reportName);
        $this->load->helper('alumnos');
        switch ($reportName) {

            case "documentacion_faltante_y_materiales_no_entregados":
                $this->get_documentacion_faltante_y_materiales_no_entregados($myReporte);
                break;

            case "estado_alumnos_certificados":
                $this->getReporteAlumnosCertificados($myReporte);
                break;

            case "reporte_horarios":
                $this->getReporteHorarios($myReporte);
                break;

            case "reporte_inscriptos_por_materia":
                $this->getReporteInscriptosPorMaterias($myReporte);
                break;

            case "reporte_alumnos_por_curso":
                $this->getReporteAlumnosPorCurso($myReporte);
                break;

            case "reporte_alumnos_por_materias":
                $this->getReporteAlumnosPorMaterias($myReporte);
                break;

            case "reporte_cupones_generados":
                $this->getReporteCuponesGenerados($myReporte);
                break;

            case "alumnos":
                $this->getReporteAlumnos($myReporte);
                break;

            case "aspirantes":
                $this->getReporteAspirantes($myReporte);
                break;

            case "profesores":
                $this->getReporteProfesores($myReporte);
                break;

            case "comisiones":
                $this->getReporteComisiones($myReporte);
                break;

            case "presupuestos":
                $this->getReportePresupuestos($myReporte);
                break;

            case "consultas_web":
                $this->getReporteConsultasWeb($myReporte);
                break;

            case "cobros":
                $this->getReporteCobros($myReporte);
                break;

            case "movimientos_cajas":
                $this->reporte_movimientos_cajas($myReporte);
                break;

            case "facturas":
                $this->getReporteFacturas($myReporte);
                break;

            case "cajas":
                $this->getReporteCaja($myReporte);
                break;

            case "comprobantes_compras":
                $this->getReporteComprobantesCompras($myReporte);
                break;

            case "ctacte_pendientes":
                $this->getReporteCtactePendientes($myReporte);
                break;

            case "inscripciones":
                $this->getReporteInscripciones($myReporte);
                break;

            case "asistencia":
                $this->getReporteAsistencia($myReporte);
                break;

            case "inscripciones_comisiones":
                $this->getReporteInscripcionesComisiones($myReporte);
                break;

            case 'reporte_boletos_bancarios':
                $this->getReporteBoletosBancarios($myReporte);
                break;
            case "ctacte_factura_cobro":
                $this->getReporteCtacteFacturaCobro($myReporte);
                break;

            case "reporte_alumnos_activos_por_comision":
                $this->reporte_alumnos_activos_por_comision($myReporte);
                break;

            case "reporte_habilitaciones_rematriculacion":
                $this->getRereporteHabilitacionesRematriculacion($myReporte);
                break;
            /* Nuevos Reportes */
            case "cobros_estimados":
                $this->getCobrosEstimados($myReporte);
                break;
            case "indice_morosidad":
                $this->getIndiceMorosidad($myReporte);
                break;
            /* Nuevos Reportes */
        }

        return $myReporte;
    }

    public function getFiltros($reporte, $usuario, $codigoFiltro = null) {
        $condiciones = null;
        if ($codigoFiltro != null) {
            $condiciones = array("codigo" => $codigoFiltro);
        }
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $conexion->where("reporte", $reporte);
        $conexion->where("(codigo_usuario = $usuario OR compartido = 1)");
        $orden = array();
        $orden[0] = array("campo" => "default", "orden" => "DESC");
        $orden[1] = array("campo" => "codigo", "orden" => "ASC");
        $arrfiltros = Vfiltros_reportes::listarFiltros_reportes($conexion, $condiciones, null, $orden);
        $arrTemp = array();
        $filtrosDefault = -1;
        foreach ($arrfiltros as $key => $filtros) {
            if ($filtros['default'] == 1 && $filtros['codigo_usuario'] == $usuario) {
                $filtrosDefault = $key;
            }
            $arrTemp['filters'][$key] = json_decode($filtros['valores'], true);
            $arrTemp['filters'][$key]['solo_lectura'] = $filtros['solo_lectura'];
            if (isset($arrTemp['filters'][$key]['advanced_filters'])) {
                foreach ($arrTemp['filters'][$key]['advanced_filters'] as $idx => $filtroAvanzado) {
                    $arrTemp['filters'][$key]['advanced_filters'][$idx]['data_set'] = $this->getFiltrosCondiciones($reporte, $filtroAvanzado['field'], $conexion);
                    $arrTemp['filters'][$key]['advanced_filters'][$idx]['filter_name'] = $filtros['nombre'];
                    $arrTemp['filters'][$key]['advanced_filters'][$idx]['filter_code'] = $filtros['codigo'];
                }
            }
        }
        $arrTemp['default'] = $filtrosDefault;
        return $arrTemp;
    }

    public function eliminarFiltroGuardado($codFiltro) {
        $conexion = $this->load->database($this->codigofilial, true);
        $myFiltro = new Vfiltros_reportes($conexion, $codFiltro);
        return $myFiltro->remove();
    }

    public function guardarFiltros($reporte, $nombreFiltro, $usuario, array $camposMostrar, array $filtrosComunes = null, array $filtrosAvanzados = null, $compartido = 0, $default = 0) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrResp = array();
        $arrTemp = array();
        $arrTemp['field_view'] = $camposMostrar;
        if ($filtrosAvanzados != null) {
            $arrTemp['advanced_filters'] = $filtrosAvanzados;
        } else {
            $arrTemp['advanced_filters'] = array(array("field" => "", "filter" => "", "value1" => "", "dataType" => ""));
        }
        if ($filtrosComunes != null) {
            $arrTemp['common_filters'] = $filtrosComunes;
        }
        $conexion->trans_begin();
        $myFiltro = new Vfiltros_reportes($conexion);
        $myFiltro->nombre = $nombreFiltro;
        $myFiltro->default = $default;
        $myFiltro->compartido = $compartido;
        $myFiltro->codigo_usuario = $usuario;
        $myFiltro->reporte = $reporte;
        $myFiltro->valores = json_encode($arrTemp);
        $myFiltro->guardarFiltros_reportes();
        if ($default == 1) {
            $myFiltro->setDefault($usuario);
        }
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            $arrResp['success'] = "success";
            $arrResp['codigo_filtro'] = $myFiltro->getCodigo();
        } else {
            $conexion->trans_rollback();
            $arrResp['error'] = lang("error_al_agregar_el_filtro");
        }
        return $arrResp;
    }

    public function getReporte($nombreReporte, $agregarColumnas = false, $numeroPagina = null, $cantidadPagina = null, $columnOrder = null, $orderType = "desc", $sSearch = null, $fieldView = null, $applyCommonFilters = null, $filters = null, $applyDefaultFilters = false, $applySoloLecturaFilters = null, $filtrar_al_inicio = false, $cargarDatos = true) {
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $myReporte = $this->getObjectReport($nombreReporte, $conexion);
        if ($fieldView) {
            $myReporte->setCamposVisibles($fieldView);
        }
        if ($sSearch != null) {
            $myReporte->setSearchLike($sSearch);
        }
        if ($applyDefaultFilters) {
            $arrCondiciones = array();
            $arrCondiciones['default'] = 1;
            $arrCondiciones['codigo_usuario'] = $this->session->userdata('codigo_usuario');
            $arrCondiciones['reporte'] = $nombreReporte;
            $arrFiltros = Vfiltros_reportes::listarFiltros_reportes($conexion, $arrCondiciones);
            if (isset($arrFiltros[0])) {
                $filtros = json_decode($arrFiltros[0]['valores'], true);
                if (isset($filtros['advanced_filters'])) {
                    $filters = $filtros['advanced_filters'];
                }
                if (isset($filtros['common_filters'])) {
                    $applyCommonFilters = $filtros['common_filters'];
                }
            }
        }

        if ($applySoloLecturaFilters) {
            $arrCondiciones = array();
            $arrCondiciones['solo_lectura'] = 1;
            $arrCondiciones['codigo_usuario'] = $this->session->userdata('codigo_usuario');
            $arrCondiciones['reporte'] = $nombreReporte;
            $arrFiltros = Vfiltros_reportes::listarFiltros_reportes($conexion, $arrCondiciones);
            if (isset($arrFiltros[0])) {
                $filtros = json_decode($arrFiltros[0]['valores'], true);
                if (isset($filtros['advanced_filters'])) {
                    $filters = $filtros['advanced_filters'];
                }
                if (isset($filtros['common_filters'])) {
                    $applyCommonFilters = $filtros['common_filters'];
                }
            }
        }
        if ($applyCommonFilters != null) {
            $myReporte->setApplyCommonFilters($applyCommonFilters);
        }
        else if($filtrar_al_inicio)
        {
            $myReporte->setApplyCommonFilters($filtrar_al_inicio);
        }

        if ($filters !== null && is_array($filters)) {
            foreach ($filters as $filter) {
                if ($filter['dataType'] == "date") {
                    $value1 = formatearFecha_mysql($filter['value1'],null,false);
                    $value2 = isset($filter['value2']) ? formatearFecha_mysql($filter['value2'],null,false) : null;
                } else {
                    $value1 = $filter['value1'];
                    $value2 = isset($filter['value2']) ? $filter['value2'] : null;
                }
                $myReporte->setUserFilters($filter['field'], $filter['filter'], $value1, $value2);
            }
        }

        if ($cantidadPagina != null && $numeroPagina != null) {
            $limitMin = $cantidadPagina * ($numeroPagina - 1);
            $limitCant = $cantidadPagina;
            $myReporte->setLimit($limitCant, $limitMin);
        }
        if ($columnOrder != null) {
            $myReporte->setOrder($columnOrder, $orderType);
        }
        return $myReporte->getReporte($agregarColumnas, $cargarDatos);
    }

    public function getFiltrosCondiciones($reportName, $filedName, CI_DB_mysqli_driver $conexion = null) {
        if ($conexion == null) {
            $conexion = $this->load->database($this->codigofilial, true, null, true);
        }
        $myReporte = $this->getObjectReport($reportName, $conexion);
        $arrColumns = $myReporte->getColumns();
        $arrResp = array();
        if (isset($arrColumns[$filedName])) {
            $arrFilters = $arrColumns[$filedName]->filtros;
            foreach ($arrFilters as $filter) {
                $arrResp['filters'][] = array("id" => $filter, "display" => lang($filter));  // lang
            }
            $arrResp['data_type'] = $arrColumns[$filedName]->type;
            switch ($arrColumns[$filedName]->type) {

                case ESTADO_ASPIRANTE_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[0]['id'] = lang("es_alumno");
                    $arrTemp[0]['value'] = lang("es_alumno");
                    $arrTemp[1]['id'] = lang("no_es_alumno");
                    $arrTemp[1]['value'] = lang("no_es_alumno");
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case DOCUMENTACION_ESTADO_CRTPE:
                    $arrTemp = array();
                    $arrTemp[0]['id'] = lang("entregado");
                    $arrTemp[0]['value'] = lang("entregado");
                    $arrTemp[1]['id'] = lang("no_entregado");
                    $arrTemp[1]['value'] = lang("no_entregado");
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case MODALIDAD_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[0]['id'] = "normal";
                    $arrTemp[0]['value'] = lang("modalidad_normal");
                    $arrTemp[1]['id'] = "intensiva";
                    $arrTemp[1]['value'] = lang("modalidad_intensiva");
                    $arrResp['type'] = "array";
                    $arrResp["set"] = $arrTemp;
                    break;

                case CICLOS_LECTIVOS_CRTYPE:
                    $arrCiclos = Vciclos::getCiclos($conexion, $this->codigofilial, null, false);
                    $arrTemp = array();
                    foreach ($arrCiclos as $key => $ciclo){
                        $arrTemp[$key]['id'] = $ciclo['nombre'];
                        $arrTemp[$key]['value'] = $ciclo['nombre'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case CICLOS_HABILITADOS_CRTYPE:
                    $arrCiclos = Vciclos::getCiclos($conexion, $this->codigofilial, null, true);
                    $arrTemp = array();
                    foreach ($arrCiclos as $key => $ciclo){
                        $arrTemp[$key]['id'] = $ciclo['codigo'];
                        $arrTemp[$key]['value'] = $ciclo['nombre'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case TITULOS_CRTYPE:
                    $arrTitutlos = Vtitulos::getTitulos($conexion, $this->codigofilial);
                    $arrTemp = array();
                    foreach ($arrTitutlos as $key => $titulo) {
                        $arrTemp[$key]['id'] = $titulo['codigo'];
                        $arrTemp[$key]['value'] = $titulo["nombre"];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case ESTADO_CUPONES_CRTYPE:
                    $arrResp['type'] = "array";
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "pendiente", "value" => "Pendiente");
                    $arrTemp[] = array("id" => "verificado", "value" => "Verificado");
                    $arrTemp[] = array("id" => "concretado", "value" => "Concretado");
                    $arrResp['set'] = $arrTemp;
                    break;

                case MEDIO_CUPONES_CRTYPE:
                    $arrResp['type'] = "array";
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "facebook", "value" => "Facebook");
                    $arrTemp[] = array("id" => "google", "value" => "Google");
                    $arrResp['set'] = $arrTemp;
                    break;

                case SINO_CRTYPE:
                    $arrResp['type'] = "array";
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "si", "value" => lang("SI"));
                    $arrTemp[] = array("id" => "no", "value" => lang("NO"));
                    $arrResp['set'] = $arrTemp;
                    break;

                case SEXO_CRTYPE:
                    $arrResp['type'] = "array";
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "Masculino", "value" => lang("masculino"));
                    $arrTemp[] = array("id" => "Femenino", "value" => lang("femenino"));
                    $arrResp['set'] = $arrTemp;
                    break;

                case COMONOSCONOCIO_CRTYPE:
                    $conexionGeneral = $this->load->database("general", true, null, true);
                    $arrComoNosconocio = Vcomo_nos_conocio::listarComo_nos_conocio($conexionGeneral, array("cnc_node.activo" => 1), null,
                            array(array("campo" => "descripcion_".get_idioma(), "orden" => "ASC")), null, false, null, $this->codigofilial);
                    $arrTemp = array();
                    $extension = lang("_idioma");
                    foreach ($arrComoNosconocio as $key => $conocio) {
                        $arrTemp[$key]['id'] = $conocio['codigo'];
                        $arrTemp[$key]['value'] = $conocio["descripcion_$extension"];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case TIPO_CONTACTO_CRTYPE:
                    $arrResp['type'] = "array";
                    $arrResp['set'] = array(
                        1 => array("id" => "PRESENCIAL", "value" => lang("PRESENCIAL")),
                        2 => array("id" => "EMAIL", "value" => lang("EMAIL")),
                        3 => array("id" => "TELEFONO", "value" => lang("TELEFONO"))
                    );
                    break;

                case LOCALIDADES_PAIS_CRTYPE:
                    $myPais = new Vpaises($conexion, $this->session->userdata['filial']['pais']);
                    $arrCiudades = $myPais->getCiudades();
                    $arrTemp = array();
                    foreach ($arrCiudades as $key => $ciudad) {
                        $arrTemp[$key]['id'] = $ciudad['id'];
                        $arrTemp[$key]['value'] = "{$ciudad['nombre']} ({$ciudad['nombre_provincia']})";
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case TALLES_CRTYPE:
                    $arrTalles = Vtalles::listarTalles($conexion);
                    $arrTemp = array();
                    foreach ($arrTalles as $key => $talle) {
                        $arrTemp[$key]['id'] = $talle['codigo'];
                        $arrTemp[$key]['value'] = $talle['talle'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case MATRIUCLAS_ESTADOS_CRTYPE:
                    $arrTemp = array();
                    $arrEstados = Vmatriculas_periodos::getEstados();

                    foreach ($arrEstados as $estado) {
                        $arrTemp[] = array("id" => $estado, "value" => lang($estado));
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case COMISIONES_NN_CRTYPE:
                    $arrComisiones = Vcomisiones::listarComisiones($conexion);
                    $arrTemp[] = array();
                    foreach ($arrComisiones as $key => $comision) {
                        $arrTemp[$key]['id'] = $comision['nombre'];
                        $arrTemp[$key]['value'] = $comision['nombre'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case COMISIONES_CRTYPE:
                    $arrComisiones = Vcomisiones::listarComisiones($conexion);
                    $arrTemp[] = array();
                    foreach ($arrComisiones as $key => $comision) {
                        $arrTemp[$key]['id'] = $comision['codigo'];
                        $arrTemp[$key]['value'] = $comision['nombre'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case PERIODOS_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "1", "value" => "1");
                    $arrTemp[] = array("id" => "2", "value" => "2");
                    $arrTemp[] = array("id" => "3", "value" => "3");
                    $arrTemp[] = array("id" => "4", "value" => "4");
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case CONCEPTOS_CTACTE_CRTYPE:
                    $arrConceptos = Vconceptos::listarConceptos($conexion, array("codigo_padre" => 0));
                    $arrTemp = array();
                    foreach ($arrConceptos as $key => $concepto) {
                        if (lang($concepto['key']) <> ''){
                            $arrTemp[$key]['id'] = $concepto['codigo'];
                            $arrTemp[$key]['value'] = lang($concepto['key']);
                        }
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case PLANES_PAGO_CRTYPE:
                    $arrPlanesPago = Vplanes_pago::listarPlanes_pago($conexion);
                    $arrTemp = array();
                    foreach ($arrPlanesPago as $key => $plan) {
                        $arrTemp[$key]['id'] = $plan['codigo'];
                        $arrTemp[$key]['value'] = $plan['nombre'];
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADOS_CONSULTAS_WEB_CRTYPE:
                    $arrTemp = array();
                    $arrEstados = Vmails_consultas::getNuevosEstados();
                    foreach ($arrEstados as $estado) {
                        $arrTemp[] = array("id" => $estado, "value" => lang($estado));
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case USUARIOS_FILIAL_CRTYPE:
                    $arrUsuarios = Vusuarios_sistema::listarUsuarios_sistema($conexion, array("cod_filial" => $this->codigofilial));
                    $arrTemp = array();
                    foreach ($arrUsuarios as $key => $usuario) {
                        $nombre = ucwords(strtolower($usuario['nombre']));
                        $apellido = ucwords(strtolower($usuario['apellido']));
                        $arrTemp[$key]['id'] = $usuario['codigo'];
                        $arrTemp[$key]['value'] = "{$nombre} {$apellido}";
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case MEDIOS_PAGO_CRTYPE:
                    $arrMediosPago = Vmedios_pago::listarMedios_pago($conexion);
                    $arrTemp = array();
                    foreach ($arrMediosPago as $key => $medioPago) {
                        $arrTemp[$key]['id'] = $medioPago['codigo'];
                        $arrTemp[$key]['value'] = lang($medioPago['medio']);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADOS_COBROS_CRTYPE:
                    $arrTemp = array();
                    $arrEstados = Vcobros::getEstados();
                    foreach ($arrEstados as $estado) {
                        $arrTemp[] = array("id" => $estado, "value" => lang($estado));
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case CONCEPTOS_CAJA_CRTYPE:
                    $arrTemp = array();
                    $arrConceptos = Vmovimientos_caja::getConceptos();
                    foreach ($arrConceptos as $concepto) {
                        $arrTemp[] = array("id" => $concepto, "value" => lang($concepto));
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case CAJAS_CRTYPE:
                    $arrCajas = Vcaja::listarCaja($conexion);
                    $arrTemp = array();
                    foreach ($arrCajas as $caja) {
                        $arrTemp[] = array("id" => $caja['codigo'], "value" => $caja['nombre']);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADO_FACTURAS_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "habilitada", "value" => lang("habilitada"));
                    $arrTemp[] = array("id" => "anulada", "value" => lang("inhabilitada"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case TIPOS_FACTURAS_CRTYPE:
                    $arrTiposFacturas = Vtipos_facturas::listarTipos_facturas($conexion);
                    $arrTemp = array();
                    foreach ($arrTiposFacturas as $tipoFactura) {
                        $arrTemp[] = array("id" => $tipoFactura['codigo'], "value" => $tipoFactura['factura']);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case FACTURANTES_CRTYPE:
                    $arrFacturantes = Vfacturantes::getFacturantes($conexion, $this->codigofilial);
                    $arrTemp = array();
                    foreach ($arrFacturantes as $facturante) {
                        $arrTemp[] = array("id" => $facturante['codigofacturante'], "value" => $facturante['razon_social']);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case CURSOS_CRTYPE:
                    $extension = lang("_idioma");
                    $arrCursos = Vcursos::listarCursos($conexion);
                    $arrTemp = array();
                    foreach ($arrCursos as $curso) {
                        $arrTemp[] = array("id" => $curso['codigo'], "value" => $curso["nombre_{$extension}"]);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case COMPROBANTES_CRTYPE:
                    $filial = $this->session->userdata('filial');
                    $arrCondiciones = array("id_pais" => $filial['pais']);
                    $arrComprobantes = Vcomprobantes::listarComprobantes($conexion, $arrCondiciones);
                    $arrTemp = array();
                    foreach ($arrComprobantes as $key => $comprobante) {
                        $arrTemp[$key]['id'] = $comprobante['id'];
                        $arrTemp[$key]['value'] = $comprobante['nombre'];
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ALUMNOS_NOMBRES_CRTYPE:
                    $arrAlumnos = Valumnos::listarAlumnos($conexion);
                    //Ticket 4778 -mmori- Informes// Filtros, // Búsqueda avanzada, Apellido y nombre, no busca.-
                    $ci = &get_instance();
                    $filial = $ci->session->userdata('filial');
                    $separador = $filial['nombreFormato']['separadorNombre'];
                    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
                    if($apellidoPrimero == 1)
                    {
                        foreach ($arrAlumnos as $key => $alumno)
                        {
                            $arrTemp[$key]['id'] = $alumno['apellido'] . $separador ." ". $alumno['nombre'];
                            $arrTemp[$key]['value'] = $alumno['apellido'] . $separador ." ". $alumno['nombre'];
                        }
                    }
                    if($apellidoPrimero == 0)
                    {
                        foreach ($arrAlumnos as $key => $alumno)
                        {
                            $arrTemp[$key]['id'] = $alumno['nombre'] . $separador . $alumno['apellido'] ;
                            $arrTemp[$key]['value'] = $alumno['nombre'] . $separador . $alumno['apellido'] ;
                        }
                    }



                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case ALUMNOS_NOMBRES_POR_ID_CRTYPE:
                    $arrAlumnos = Valumnos::listarAlumnos($conexion);
                    $ci = &get_instance();
                    $filial = $ci->session->userdata('filial');
                    $separador = $filial['nombreFormato']['separadorNombre'];
                    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
                    if($apellidoPrimero == 1)
                    {
                        foreach ($arrAlumnos as $key => $alumno)
                        {
                            $arrTemp[$key]['id'] = $alumno['codigo'];
                            $arrTemp[$key]['value'] = $alumno['apellido'] . $separador ." ". $alumno['nombre'];
                        }
                    }
                    if($apellidoPrimero == 0)
                    {
                        foreach ($arrAlumnos as $key => $alumno)
                        {
                            $arrTemp[$key]['id'] = $alumno['codigo'];
                            $arrTemp[$key]['value'] = $alumno['nombre'] . $separador . $alumno['apellido'] ;
                        }
                    }



                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case RAZONES_SOCIALES_CRTYPE:
                    $arrRazones_Sociales = Vrazones_sociales::listarRazones_sociales($conexion);
                    $arrTemp = array();
                    foreach ($arrRazones_Sociales as $key => $razon) {
                        $arrTemp[$key]['id'] = $razon['codigo'];
                        $arrTemp[$key]['value'] = $razon['razon_social'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case MATERIAS_CRTYPE:
                    $extension = lang("_idioma");
                    $arrMaterias = Vmaterias::listarMaterias($conexion);
                    $arrTemp = array();
                    foreach ($arrMaterias as $materia) {
                        $arrTemp[] = array("id" => $materia['codigo'], "value" => $materia["nombre_{$extension}"]);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case SALONES_CRTYPE:
                    $extension = lang("_idioma");
                    $arrSalones = Vsalones::listarSalones($conexion);
                    $arrTemp = array();
                    foreach ($arrSalones as $salon) {
                        $arrTemp[] = array("id" => $salon['codigo'], "value" => $salon["salon"]);
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case DIA_DE_SEMANA_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => lang("dia_lunes"), "value" => lang("dia_lunes"));
                    $arrTemp[] = array("id" => lang("dia_martes"), "value" => lang("dia_martes"));
                    $arrTemp[] = array("id" => lang("dia_miercoles"), "value" => lang("dia_miercoles"));
                    $arrTemp[] = array("id" => lang("dia_jueves"), "value" => lang("dia_jueves"));
                    $arrTemp[] = array("id" => lang("dia_viernes"), "value" => lang("dia_viernes"));
                    $arrTemp[] = array("id" => lang("dia_sabado"), "value" => lang("dia_sabado"));
                    $arrTemp[] = array("id" => lang("dia_domingo"), "value" => lang("dia_domingo"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case PROFESORES_NOMBRES_CRTYPE:
                    $arrProfesores = Vprofesores::listarProfesores($conexion);
                    foreach ($arrProfesores as $key => $profesor) {
                        $arrTemp[$key]['id'] = $profesor['apellido'] . ", " . $profesor['nombre'];
                        $arrTemp[$key]['value'] = $profesor['apellido'] . ", " . $profesor['nombre'];
                    }
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case ESTADO_MATRICULAS_INSCRIPCIONES_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "habilitada", "value" => lang("habilitada"));
                    $arrTemp[] = array("id" => "inhabilitada", "value" => lang("inhabilitada"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ASISTENCIA_ALUMNO_CRTYPE:
                    $asistencia = Vasistencias::getArrayEstadoAsistencias();
                    foreach ($asistencia as $key => $asis) {
                        $arrTemp[$key]['id'] = $asis['id'];
                        $arrTemp[$key]['value'] = $asis['nombre'];
                    }
                    $sinAsis['id'] = '';
                    $sinAsis['value'] = 'Sin Asistencias';
                    $arrTemp[] = $sinAsis;
                    $arrResp['type'] = "array";
                    $arrResp['set'] = $arrTemp;
                    break;

                case CTACTE_ESTADO_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => lang('debe_ctacte'), "value" => lang('debe_ctacte'));
                    $arrTemp[] = array("id" => lang("no_debe_ctacte"), "value" => lang("no_debe_ctacte"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADO_ACADEMICO_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "regular", "value" => lang("regular"));
                    $arrTemp[] = array("id" => "no_curso", "value" => lang("no_curso"));
                    $arrTemp[] = array("id" => "aprobado", "value" => lang("aprobado"));
                    $arrTemp[] = array("id" => "cursando", "value" => lang("cursando"));
                    $arrTemp[] = array("id" => "homologado", "value" => lang("homologado"));
                    $arrTemp[] = array("id" => "recursa", "value" => lang("recursa"));
                    $arrTemp[] = array("id" => "libre", "value" => lang("libre"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case TIPO_DEUDA_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => lang('deuda_activa'), "value" => lang('deuda_activa'));
                    $arrTemp[] = array("id" => lang('deuda_pasiva'), "value" => lang('deuda_pasiva'));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADO_BOLETOS_BANCARIOS:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => 'pendiente', "value" => lang('pendiente'));
                    $arrTemp[] = array("id" => 'entrada_confirmada', "value" => lang('CONFIRMADA'));
                    $arrTemp[] = array("id" => 'entrada_rechazada', "value" => lang('entrada_rechazada'));
                    $arrTemp[] = array("id" => 'baja', "value" => lang('baja'));
                    $arrTemp[] = array("id" => 'liquidado', "value" => lang('liquidado'));
                    $arrTemp[] = array("id" => 'baja_solicitada', "value" => lang('baja_solicitada'));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADO_COMISIONES_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "habilitado", "value" => lang("HABILITADO"));
                    $arrTemp[] = array("id" => "inhabilitado", "value" => lang("inhabilitadas"));
                    $arrTemp[] = array("id" => "desuso", "value" => lang("desuso"));
                    $arrTemp[] = array("id" => "a_pasar", "value" => lang("a_pasar"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADO_MATRICULAS_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "habilitada", "value" => lang("HABILITADO"));
                    $arrTemp[] = array("id" => "inhabilitada", "value" => lang("inhabilitada"));
                    $arrTemp[] = array("id" => "certificada", "value" => lang("certificada"));
                    $arrTemp[] = array("id" => "finalizada", "value" => lang("finalizada"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                case ESTADO_CERTIFICADOS_CRTYPE:
                    $arrTemp = array();
                    $arrTemp[] = array("id" => "finalizado", "value" => lang("finalizado"));
                    $arrTemp[] = array("id" => "pendiente", "value" => lang("pendiente"));
                    $arrTemp[] = array("id" => "cancelado", "value" => lang("cancelado"));
                    $arrTemp[] = array("id" => "en_proceso", "value" => lang("en_proceso"));
                    $arrTemp[] = array("id" => "pendiente_aprobar", "value" => lang("pendiente_aprobar"));
                    $arrTemp[] = array("id" => "pendiente_impresion", "value" => lang("pendiente_impresion"));
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] =  "array";
                    break;

                case PERIODOS_ROYALTYS_CRTYPE:
                    $arrTemp = array();
                    for ($anio = 2005; $anio <= date("Y"); $anio++){
                        for ($mes = 1; $mes <= 12; $mes ++){
                            if ($mes == date("m") && $anio == date("Y")){
                                $arrTemp[] = array("id" => $anio.str_pad($mes, 2, "0", STR_PAD_LEFT),
                                "value" => getMesNombre($mes)." ".$anio,
                                "selected" => "true");
                            } else {
                                $arrTemp[] = array("id" => $anio.str_pad($mes, 2, "0", STR_PAD_LEFT),
                                    "value" => getMesNombre($mes)." ".$anio);
                            }
                        }
                    }
                    $arrResp['set'] = $arrTemp;
                    $arrResp['type'] = "array";
                    break;

                default:
                    $arrResp['type'] = "simple";
                    break;
            }
        } else {
            $arrResp['error'] = "field not found";
        }
        return $arrResp;
    }

    private function getReporteAlumnosPorMaterias(reportes_sistema $myReporte){
        $myReporte->setTable("alumnos");
        $no_curso = lang("no_curso");
        $regular = lang("regular");
        $aprobado = lang("aprobado");
        $cursando = lang("cursando");
        $homologado = lang("homologado");
        $recursa = lang("recursa");
        $libre = lang("libre");
        $idioma = get_idioma();
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("alumnos.codigo", "desc");

        $myReporte->setField("matriculas.codigo as codigo_matricula");
        $myReporte->setField("YEAR(matriculas.fecha_emision) AS anio_emision");
        $myReporte->setField("CONCAT(alumnos.nombre, ' ',alumnos.apellido) AS nombre_alumno");
        $myReporte->setField("(SELECT comisiones.nombre
                                    FROM matriculas_inscripciones
                                    INNER JOIN comisiones on comisiones.codigo = matriculas_inscripciones.cod_comision
                                    WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo
                                    ORDER BY matriculas_inscripciones.codigo DESC LIMIT 0, 1) AS nombre_comision");
        $myReporte->setField("general.materias.nombre_$idioma AS nombre_materia");
        $myReporte->setField("IF (estadoacademico.estado = 'no_curso', '$no_curso',
                                    IF (estadoacademico.estado = 'regular', '$regular',
                                    IF (estadoacademico.estado = 'aprobado', '$aprobado',
                                    IF (estadoacademico.estado = 'cursando', '$cursando',
                                    IF (estadoacademico.estado = 'homologado', '$homologado',
                                    IF (estadoacademico.estado = 'recursa', '$recursa', '$libre')))))) AS estado");
        $myReporte->setField("IFNULL(estadoacademico.porcasistencia, '-') AS porcasistencia");
        $myReporte->setField("DATE_FORMAT(matriculas.fecha_emision,'%d/%m/%Y') as fecha_emi");
        $myReporte->setField("(SELECT comisiones.nombre
                                    FROM matriculas_inscripciones
                                    INNER JOIN comisiones on comisiones.codigo = matriculas_inscripciones.cod_comision
                                    WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo
                                    ORDER BY matriculas_inscripciones.codigo DESC LIMIT 0, 1) AS nombre_comision");
        $myReporte->setJOIN("matriculas", "matriculas.cod_alumno = alumnos.codigo");
        $myReporte->setJOIN("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $myReporte->setJOIN("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.cod_matricula");
        $myReporte->setJOIN("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $myReporte->setPermanentWhere(array("matriculas.estado" => 'habilitada'));

        $myReporte->setCampo("codigo_matricula", lang("matricula"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "matriculas.codigo", "codigo_matricula", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("anio_emision", lang("periodos_anio"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), true, "anio_emision", "anio_emision", HAVING_CRMETHOD, 20, false, false);
        $myReporte->setCampo("nombre_alumno", lang("Alumno"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "nombre_alumno", "nombre_alumno", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("nombre_comision", lang("comision"), true, COMISIONES_NN_CRTYPE, array(ES_IGUAL_CRFILTER), true, "nombre_comision", "nombre_comision", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("nombre_materia", lang("materias"), true, MATERIAS_CRTYPE, array(ES_IGUAL_CRFILTER), true, "nombre_materia", "nombre_materia", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("estado", lang("estado"), true, ESTADO_ACADEMICO_CRTYPE, array(ES_IGUAL_CRFILTER), true, "estadoacademico.estado", "estado", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("porcasistencia", lang("porc_asistencia"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), false, "estadoacademico.porcasistencia", "estadoacademico.porcasistencia", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("fecha_emi", lang("fecha_inscripcion"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER), true, 'fecha_emi', "STR_TO_DATE(fecha_emi,'%d/%m/%Y')", HAVING_CRMETHOD, 20, true, false);

    }

    private function getRereporteHabilitacionesRematriculacion(reportes_sistema $myReporte){
        $filial = $this->session->userdata('filial');
        $nombreApellido = formatearNomApeQuery();
        $myReporte->setTable("general.habilitaciones_rematriculacion");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("fecha_hora", "asc");
        $myReporte->setField("matriculas.cod_alumno as codigo_alumno");
        $myReporte->setField("habilitaciones_rematriculacion.cod_matricula as codigo_matricula");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) AS usuario_habilitacion");
        $myReporte->setField("habilitaciones_rematriculacion.fecha_desde");
        $myReporte->setField("habilitaciones_rematriculacion.fecha_hasta");
        $myReporte->setField("habilitaciones_rematriculacion.fecha_hora");
        $myReporte->setField("habilitaciones_rematriculacion.motivo");
        $myReporte->setField("comisiones.nombre as comision");
        $myReporte->setField("cursos.nombre_pt as nombre_curso");
        $myReporte->setField("habilitaciones_rematriculacion.tipo");
        $myReporte->setJOIN("general.usuarios_sistema", "habilitaciones_rematriculacion.cod_usuario = usuarios_sistema.codigo");
        $myReporte->setJOIN("matriculas", "habilitaciones_rematriculacion.cod_matricula = matriculas.codigo");
        $myReporte->setJOIN("alumnos", "matriculas.cod_alumno = alumnos.codigo");
        $myReporte->setJOIN("general.filiales", "filiales.codigo = habilitaciones_rematriculacion.cod_filial");
        $myReporte->setJOIN("comisiones", "comisiones.codigo = habilitaciones_rematriculacion.cod_comision");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = habilitaciones_rematriculacion.cod_curso");
        $myReporte->setCampo("codigo_matricula", lang("matricula"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "matriculas.codigo", "codigo_matricula", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("nombre_alumno", lang("Alumno"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "nombre_alumno", "nombre_alumno", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("usuario_habilitacion", "Usuario", true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "usuario_habilitacion", "usuario_habilitacion", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("fecha_desde", "inicio periodo", true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER), true, 'fecha_desde', "STR_TO_DATE(fecha_desde,'%d/%m/%Y')", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("fecha_hasta", "final periodo", true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER), true, 'fecha_hasta', "STR_TO_DATE(fecha_hasta,'%d/%m/%Y')", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("fecha_hora", "Fecha habilitacion", true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER), true, 'fecha_hora', "STR_TO_DATE(fecha_hora,'%d/%m/%Y %H:%M')", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("motivo", "Motivo", true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "motivo", "motivo", HAVING_CRMETHOD, 20, true, false);
        $myReporte->setCampo("comision", lang("comision"), true, COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "comisiones.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("nombre_curso", lang("nombre_curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.cursos.codigo", "nombre_curso", WHERE_CRMETHOD);
        $myReporte->setCampo("tipo", "Tipo", true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "tipo", "tipo", HAVING_CRMETHOD, 20, true, false);

    }

    /* REPORTE DE ALUMNOS */

    private function getReporteAlumnos(reportes_sistema $myReporte) {
        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        
        $myReporte->setTable("alumnos");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("codigo", "asc");
        $nombreApellido = formatearNomApeQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
        //agrega telefono
        $myReporte->setField("(SELECT general.empresas_telefonicas.nombre
                                FROM general.empresas_telefonicas
                                JOIN telefonos ON general.empresas_telefonicas.codigo = telefonos.empresa
                                JOIN alumnos_telefonos ON alumnos_telefonos.cod_telefono = telefonos.codigo
                                    WHERE alumnos_telefonos.cod_alumno = alumnos.codigo
                                    ORDER BY `default` DESC LIMIT 0, 1) AS telefono_alumno_empresa");

        $myReporte->setField("(SELECT CONCAT(telefonos.cod_area, ' ', telefonos.numero )
                                FROM telefonos
                                JOIN alumnos_telefonos ON alumnos_telefonos.cod_telefono = telefonos.codigo
                                    WHERE alumnos_telefonos.cod_alumno = alumnos.codigo
                                    ORDER BY `default` DESC LIMIT 0, 1) AS telefono_alumno");
        $myReporte->setField("CONCAT(LPAD(DAY(alumnos.fechanaci), 2, 0), '/', LPAD(MONTH(alumnos.fechanaci), 2, 0), '/', YEAR(alumnos.fechanaci)) AS alumno_fecha_nacimiento");
        $myReporte->setField("CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento) AS documento_alumno");
        $myReporte->setField("general.localidades.nombre AS alumno_localidad");
        $myReporte->setField("CONCAT(LPAD(DAY(alumnos.fechaalta), 2, 0), '/', LPAD(MONTH(alumnos.fechaalta), 2, 0), '/', YEAR(alumnos.fechaalta)) AS alumno_fecha_alta");
        $myReporte->setField("alumnos.sexo");
        $myReporte->setField("CONCAT(alumnos.calle, ' ', IFNULL(alumnos.calle_numero,''), ' ', IFNULL(alumnos.calle_complemento,'')) AS alumno_domicilio");
        $myReporte->setField("general.como_nos_conocio.descripcion_es AS alumno_como_nos_conocio");
        $myReporte->setField("alumnos.email");
        $myReporte->setField("razones_sociales.razon_social");
        $myReporte->setField("CONCAT(dt1.nombre, ' ', razones_sociales.documento) AS razon_social_documento");
        $myReporte->setField("general.talles.talle as talleAlumno");
        $myReporte->setField("(SELECT count(alumnos_responsables.cod_responsable) from alumnos_responsables where alumnos_responsables.cod_alumno = alumnos.codigo) as cant_responsable", false);
        $myReporte->setField("alumnos.barrio");
        $myReporte->setField("alumnos.codpost");

        /* JOIN */
        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
        $myReporte->setJOIN("general.localidades", "general.localidades.id = alumnos.id_localidad");
        $myReporte->setJOIN("general.como_nos_conocio", "general.como_nos_conocio.codigo = alumnos.comonosconocio", "left");
        $myReporte->setJOIN("alumnos_razones", "alumnos_razones.cod_alumno = alumnos.codigo AND alumnos_razones.`default` = 1");
        $myReporte->setJOIN("razones_sociales", "razones_sociales.codigo = alumnos_razones.cod_razon_social");
        $myReporte->setJOIN("general.documentos_tipos AS dt1", "dt1.codigo = razones_sociales.tipo_documentos");
        $myReporte->setJOIN("general.talles", "general.talles.codigo = alumnos.id_talle", "left");


        // ORIGIN LAMBDA
        // DONDE SE AJUSTAN LOS CAMPOS A MOSTRAR EN EL REPORTE
        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        //El tercer parametro de setCampo permite la visibilidad del campo
        $myReporte->setCampo("codigo", lang("codigo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "alumnos.codigo", null, WHERE_CRMETHOD, 15);
        $myReporte->setCampo("nombre_alumno", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 45);
        //correo electronico
        $myReporte->setCampo("email", lang("email"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "alumnos.email", null, WHERE_CRMETHOD, 50);
        //documento del alumno
        $myReporte->setCampo("documento_alumno", lang("documento"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        //fecha de nacimiento
        $myReporte->setCampo("alumno_fecha_nacimiento", lang("fecha_nacimiento"), true, DATE_CRTYPE, array(MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "alumno_fecha_nacimiento", "STR_TO_DATE(alumno_fecha_nacimiento,'%d/%m/%Y')", HAVING_CRMETHOD, 20);
        //Telefono del alumno
        $myReporte->setCampo("telefono_alumno", lang('telefono'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        //telefono telefono_alumno_empresa
        $myReporte->setCampo("telefono_alumno_empresa", lang('tel_empresa'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        //domicilio
        $myReporte->setCampo("alumno_domicilio", lang("domicilio"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 50);
        //localidad
        $myReporte->setCampo("alumno_localidad", lang("localidad"), true, LOCALIDADES_PAIS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.localidades.id", null, WHERE_CRMETHOD, 45);
        //fecha de alta
        $myReporte->setCampo("alumno_fecha_alta", lang("fecha_alta"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "alumno_fecha_alta", "STR_TO_DATE(alumno_fecha_alta,'%d/%m/%Y')", HAVING_CRMETHOD, 20);
        //como nos conocio
        $myReporte->SetCampo("alumno_como_nos_conocio", lang("como_nos_conocio"), true, COMONOSCONOCIO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.como_nos_conocio.codigo", null, WHERE_CRMETHOD, 40);   
        //talle
        $myReporte->setCampo("talleAlumno", lang('datos_talle'), true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.talles.talle", null, WHERE_CRMETHOD, 15);

       $myReporte->SetCampo("sexo", lang("sexo"), false, SEXO_CRTYPE, array(ES_IGUAL_CRFILTER), false, "alumnos.sexo", null, WHERE_CRMETHOD, 20);

/*
        $myReporte->setCampo("razon_social", lang("razon_social"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "razones_sociales.razon_social", null, WHERE_CRMETHOD, 50);
        $myReporte->SetCampo("razon_social_documento", lang("id_fiscal"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 40);
        $myReporte->setCampo("cant_responsable", lang('cant_responsable'), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false . null, null, HAVING_CRMETHOD, 25);
        $myReporte->setCampo("barrio", lang("datos_barrio"), false, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, null, WHERE_CRMETHOD, null, true, false);
        $myReporte->setCampo("codpost", lang("codigo_postal"), false, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, null, WHERE_CRMETHOD, null, true, false);
*/

        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("anio"), "anio", array("YEAR(alumnos.fechaalta) = YEAR(CURDATE())"), WHERE_CRMETHOD, "Inscriptos este año");
    }

    /* REPORTE DE ASPIRANTES */

    private function getReporteAspirantes(reportes_sistema $myReporte) {
        $myReporte->setTable("aspirantes");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("codigo", "asc");
        $presencial = lang("PRESENCIAL");
        $telefono = lang("TELEFONO");
        $email = lang("EMAIL");
        $nombreApellido = formatearNombreAspQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("aspirantes.codigo");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_aspirantes");
        $myReporte->setField("aspirantes.email");
        $myReporte->setField("IF (aspirantes.tipo_contacto = 'PRESENCIAL', '$presencial', IF(aspirantes.tipo_contacto = 'EMAIL', '$email', '$telefono')) AS tipo_contacto");
        $myReporte->setField("general.como_nos_conocio.descripcion_es AS aspirantes_como_nos_conocio");
        $myReporte->setField("CONCAT(LPAD(DAY(aspirantes.fechaalta), 2, 0), '/', LPAD(MONTH(aspirantes.fechaalta), 2, 0), '/', YEAR(aspirantes.fechaalta)) AS aspirantes_fecha_alta");
        $myReporte->setField("IFNULL(CONCAT(LPAD(DAY(aspirantes.fechanaci), 2, 0), '/', LPAD(MONTH(aspirantes.fechanaci), 2, 0), '/', YEAR(aspirantes.fechanaci)),'') AS aspirantes_fecha_nacimiento");
        $myReporte->setField("IFNULL(CONCAT(general.documentos_tipos.nombre, ' ', aspirantes.documento),'') AS documento_aspirantes");
        $myReporte->setField("IFNULL(general.localidades.nombre,'') AS aspirantes_localidad");
        $myReporte->setField("IFNULL(CONCAT(aspirantes.calle, ' ', aspirantes.calle_numero, ' ', aspirantes.calle_complemento),'') AS aspirantes_domicilio");
        $myReporte->setField("(SELECT CONCAT(general.empresas_telefonicas.nombre, ' ', telefonos.cod_area, ' ', telefonos.numero )
                                    FROM telefonos
                                    JOIN aspirantes_telefonos ON aspirantes_telefonos.cod_telefono = telefonos.codigo
                                    JOIN general.empresas_telefonicas ON general.empresas_telefonicas.codigo = telefonos.empresa
                                        WHERE aspirantes_telefonos.cod_aspirante = aspirantes.codigo
                                        ORDER BY `default` DESC LIMIT 0, 1) AS telefono_aspirante");
        $myReporte->setField('IFNULL(aspirantes.observaciones,"") as observaciones');
        $myReporte->setField("IF ((SELECT COUNT(id_alumno) FROM aspirantes_alumnos WHERE aspirantes_alumnos.id_aspirante = aspirantes.codigo) > 0, 'si', 'no') AS es_alumno");
        $myReporte->setField("IFNULL (general.cursos.nombre_es, '') AS nombre_curso");
        //Ticket 4524 -mmori- agregar turnos
        $myReporte->setField("IFNULL((SELECT GROUP_CONCAT(general.turnos.nombre) FROM general.turnos
                                INNER JOIN  aspirantes_turnos ON aspirantes_turnos.cod_turno = general.turnos.id
                                WHERE aspirantes_turnos.cod_aspirante = aspirantes.codigo),'') as turno");
        $myReporte->setField("aspirantes.barrio");
        $myReporte->setField("aspirantes.codpost");
        /* JOIN */
        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = aspirantes.tipo", "left");
        $myReporte->setJOIN("general.localidades", "general.localidades.id = aspirantes.cod_localidad", "left");
        $myReporte->setJOIN("general.como_nos_conocio", "general.como_nos_conocio.codigo = aspirantes.comonosconocio", "left");
        $myReporte->setJOIN("aspirantes_cursos", "aspirantes_cursos.cod_aspirante = aspirantes.codigo", "left");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = aspirantes_cursos.cod_curso", "left");


        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang("codigo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "aspirantes.codigo", null, WHERE_CRMETHOD, 10);
        $myReporte->setCampo("nombre_aspirantes", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 50);
        $myReporte->setCampo("email", lang("email"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "aspirantes.email", null, WHERE_CRMETHOD, 60);
        $myReporte->setCampo("tipo_contacto", lang("tipo_contacto"), true, TIPO_CONTACTO_CRTYPE, array(ES_IGUAL_CRFILTER), false, "aspirantes.tipo_contacto", null, WHERE_CRMETHOD);
        $myReporte->SetCampo("aspirantes_como_nos_conocio", lang("como_nos_conocio"), true, COMONOSCONOCIO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.como_nos_conocio.codigo", null, WHERE_CRMETHOD, 36);
        $myReporte->setCampo("aspirantes_fecha_alta", lang("fecha_alta"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "aspirantes.fechaalta", "aspirantes.fechaalta", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("aspirantes_fecha_nacimiento", lang("fecha_nacimiento"), false, DATE_CRTYPE, array(MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "aspirantes.fechanaci", "aspirantes.fechanaci", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("documento_aspirantes", lang("documento"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 25);
        $myReporte->setCampo("aspirantes_localidad", lang("localidad"), false, LOCALIDADES_PAIS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.localidades.id", null, WHERE_CRMETHOD, 25);
        $myReporte->setCampo("aspirantes_domicilio", lang("domicilio"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 50);
        $myReporte->setCampo("telefono_aspirante", lang('telefono'), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        $myReporte->setCampo("observaciones", lang("observaciones"), false, STRING_CRTYPE, array(LIKE_CRFILTER), false, "aspirantes.observaciones", null, WHERE_CRMETHOD, 60);
        $myReporte->setCampo("es_alumno", lang("es_alumno"), true, SINO_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, null, HAVING_CRMETHOD, 10);
        $myReporte->setCampo("nombre_curso", lang("nombre_curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.cursos.codigo", "nombre_curso", WHERE_CRMETHOD);
        //Ticket 4524 -mmori- agregar turnos
        $myReporte->setCampo("turno", lang("turno"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "turno", "turno", HAVING_CRMETHOD, 10);
        $myReporte->setCampo("barrio", lang("datos_barrio"), false, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, null, WHERE_CRMETHOD, null, true, false);
        $myReporte->setCampo("codpost", lang("codigo_postal"), false, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, null, WHERE_CRMETHOD, null, true, false);

        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("anio"), "anio", array("YEAR(aspirantes.fechaalta) = YEAR(CURDATE())"), WHERE_CRMETHOD, "Aspirantes de este año");
    }

    /* REPORTE DE MATRICULAS */

    private function getReporteInscripciones(reportes_sistema $myReporte) {//matriculas_periodos
        $extension = lang("_idioma");
        $myReporte->setTable("alumnos");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("matriculas.codigo", "asc");
        $nombreApellido = formatearNomApeQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo as cod_alumno");
        $myReporte->setField("matriculas.codigo");
        $myReporte->setField("CONCAT($nombreApellido) AS alumno_nombre");
        $myReporte->setField("general.cursos.nombre_$extension AS curso_nombre");
        $myReporte->setField("comisiones.nombre AS comision");
        $myReporte->setField("DATE_FORMAT(matriculas.fecha_emision,'%d/%m/%Y') AS fecha_matricula");
        $myReporte->setField("matriculas.estado as estado");
        // IGAC- 813
        $myReporte->setField("(SELECT CONCAT(telefonos.cod_area, ' ', telefonos.numero )
                                FROM telefonos
                                JOIN alumnos_telefonos ON alumnos_telefonos.cod_telefono = telefonos.codigo
                                    WHERE alumnos_telefonos.cod_alumno = alumnos.codigo
                                    ORDER BY `default` DESC LIMIT 0, 1) AS telefono_alumno");
        $myReporte->setJOIN("matriculas", "matriculas.cod_alumno = alumnos.codigo");
        $myReporte->setJOIN("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $myReporte->setJOIN("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.codigo");
        $myReporte->setJOIN("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo AND estadoacademico.codigo = (SELECT MAX(estaaca.codigo) FROM estadoacademico AS estaaca WHERE estaaca.cod_matricula_periodo = matriculas_periodos.codigo)");
        $myReporte->setJOIN("matriculas_inscripciones", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo and matriculas_inscripciones.baja = 0", "LEFT");
        $myReporte->setJOIN("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision", "LEFT");
        /* CAMPOS MOSTRAR
            WIDI
        */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("cod_alumno", lang("cod_alumno"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("codigo", lang("cod_matricula"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, null, null, HAVING_CRMETHOD, 40);
        $myReporte->setCampo("alumno_nombre", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 50);
        //$myReporte->setCampo("telefono_alumno_empresa", lang('tel_empresa'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        $myReporte->setCampo("curso_nombre", lang("curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.cursos.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("comision", lang("comision"), true, COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "comisiones.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("fecha_matricula", lang("fecha_matricula"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "matriculas.fecha_emision", "matriculas.fecha_emision", WHERE_CRMETHOD, 28);
        $myReporte->setGroup("cod_alumno");
        $myReporte->setCampo("estado", lang("estado"),false,ESTADO_MATRICULAS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false , "matriculas.estado", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("telefono_alumno", lang('telefono'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, WHERE_CRMETHOD, 35);
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("anio"), "anio", array("YEAR(matriculas.fecha_emision) = YEAR(CURDATE())"), WHERE_CRMETHOD, lang("matriculas_de_este_anio"));

    }

    /* REPORTE DE PROFERSORES */

    private function getReporteProfesores(reportes_sistema $myReporte) {
        $myReporte->setTable("profesores");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("codigo", "asc");
        $nombre_apellido = formatearNomApeProf();
        /* CAMPOS CONSULTA */
        $myReporte->setField("profesores.codigo");
        $myReporte->setField("CONCAT($nombre_apellido) AS nombre_profesores");
        $myReporte->setField("CONCAT(LPAD(DAY(profesores.fechanac), 2, 0), '/', LPAD(MONTH(profesores.fechanac), 2, 0), '/', YEAR(profesores.fechanac)) AS profesores_fecha_nacimiento");
        $myReporte->setField("CONCAT(general.documentos_tipos.nombre, ' ', profesores.nrodocumento) AS documento_profesores");
        $myReporte->setField("general.localidades.nombre AS profesores_localidad");
        $myReporte->setField("CONCAT(LPAD(DAY(profesores.fechaalta), 2, 0), '/', LPAD(MONTH(profesores.fechaalta), 2, 0), '/', YEAR(profesores.fechaalta)) AS profesores_fecha_alta");
        $myReporte->setField("CONCAT(profesores.calle, ' ', profesores.numero, ' ', profesores.complemento) AS profesores_domicilio");
        $myReporte->setField("profesores.mail");
        $myReporte->setField("(SELECT CONCAT(telefonos.cod_area, ' ', telefonos.numero )
                                    FROM telefonos
                                    JOIN profesores_telefonos ON profesores_telefonos.id_telefono = telefonos.codigo
                                        WHERE profesores_telefonos.id_profesor = profesores.codigo
                                        ORDER BY `default` DESC LIMIT 0, 1) AS telefono_profesor");
        $myReporte->setField('profesores.estado');
        /* JOIN */
        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = profesores.tipodocumento");
        $myReporte->setJOIN("general.localidades", "general.localidades.id = profesores.cod_localidad");
        //ahora hacemos un left join para mostrar los alumnos asi no haya consultado ningun curso
        //$myReporte->setJOIN("general.cursos.id", "20.aspirantes.codigo","left");

        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang("codigo"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "profesores.codigo", null, WHERE_CRMETHOD, 20);
        $myReporte->setCampo("nombre_profesores", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 48);
        $myReporte->setCampo("profesores_fecha_nacimiento", lang("fecha_nacimiento"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "profesores.fechanac", "profesores.fechanac", WHERE_CRMETHOD, 22);
        $myReporte->setCampo("documento_profesores", lang("documento"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        $myReporte->setCampo("profesores_localidad", lang("localidad"), true, LOCALIDADES_PAIS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.localidades.id", null, WHERE_CRMETHOD, 40);
        $myReporte->setCampo("profesores_fecha_alta", lang("fecha_de_alta"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "profesores.fechaalta", "profesores.fechaalta", WHERE_CRMETHOD, 25);
        $myReporte->setCampo("profesores_domicilio", lang("domicilio"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 50);
        $myReporte->setCampo("mail", lang("email"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "profesores.email", null, WHERE_CRMETHOD, 60);
        $myReporte->setCampo("telefono_profesor", lang('telefono'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 60);
        $myReporte->setCampo("estado", lang('estado'), true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, null, WHERE_CRMETHOD, 25);
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("anio"), "anio", array("YEAR(aspirantes.fechaalta) = YEAR(CURDATE())"), WHERE_CRMETHOD, lang("aspirantes_de_este_anio"));
    }

    /* REPORTE DE COMISIONES */

    private function getReporteComisiones(reportes_sistema $myReporte){
        $extension = lang("_idioma");
        $myReporte->setTable("comisiones");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("codigo", "asc");
        /* CAMPOS CONSULTA */
        $myReporte->setField("comisiones.codigo");
        $myReporte->setField("general.cursos.nombre_$extension AS curso_nombre");
        $myReporte->setField("general.ciclos.nombre AS ciclo");
        $myReporte->setField("comisiones.nombre");
        $myReporte->setField("comisiones.cod_tipo_periodo");
        $myReporte->setField("IFNULL((SELECT min(salones.cupo)
                                    FROM (`salones`)
                                    WHERE `salones`.`codigo` = horarios.cod_salon), 'sin_salon') as cupo");
        $myReporte->setField("(SELECT COUNT(distinct estadoacademico.cod_matricula_periodo)
                                    FROM matriculas_inscripciones
                                    INNER JOIN estadoacademico on estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
                                    INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo
                                    INNER JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado <> 'prematricula'
                                    WHERE matriculas_inscripciones.cod_comision = comisiones.codigo
                                        AND matriculas_inscripciones.baja = 0) AS inscriptos");
        $myReporte->setField("IFNULL((SELECT MIN(salones.cupo)
                                    FROM (salones)
                                    WHERE salones.codigo = horarios.cod_salon) -
                                (SELECT COUNT(distinct estadoacademico.cod_matricula_periodo)
                                    FROM matriculas_inscripciones
                                    INNER JOIN estadoacademico on estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
                                    WHERE matriculas_inscripciones.cod_comision = comisiones.codigo
                                        AND matriculas_inscripciones.baja = 0), '-') AS vacantes");
        $myReporte->setField("IFNULL((SELECT DATE_FORMAT(MIN(horarios.dia),'%d/%m/%Y') FROM horarios WHERE horarios.cod_comision = comisiones.codigo AND horarios.baja = 0),'-') AS inicio");
        $myReporte->setField("IFNULL((SELECT DATE_FORMAT(MAX(horarios.dia), '%d/%m/%Y') FROM horarios WHERE horarios.cod_comision = comisiones.codigo AND horarios.baja = 0),'-') AS fin");
        $myReporte->setField("comisiones.estado");
        /* JOIN */
        $myReporte->setJOIN("general.planes_academicos", "general.planes_academicos.codigo = comisiones.cod_plan_academico");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $myReporte->setJOIN('horarios', 'horarios.cod_comision = comisiones.codigo and horarios.baja = 0', 'left');
        $myReporte->setJOIN("general.ciclos", "general.ciclos.codigo = comisiones.ciclo");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("codigo", lang("codigo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "comisiones.codigo", null, WHERE_CRMETHOD, 20);
        $myReporte->setCampo("curso_nombre", lang("curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.cursos.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("ciclo", lang("ciclo"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "ciclo", null, HAVING_CRMETHOD, 15);
        $myReporte->setCampo("nombre", lang("nombre"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "comisiones.nombre", null, WHERE_CRMETHOD, 45);
        $myReporte->setCampo("cod_tipo_periodo", lang("periodo"), true, PERIODOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, null, null, WHERE_CRMETHOD, 25);
        $myReporte->setCampo("cupo", lang("cupo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), false, "cupo", null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("inscriptos", lang("inscriptos"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), false, "inscriptos", "inscriptos", HAVING_CRMETHOD, 30);
        $myReporte->setCampo("vacantes", lang("vacantes"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), false, "vacantes", "vacantes", HAVING_CRMETHOD, 30);
        //siwakawa
        $myReporte->setCampo("inicio", lang("fecha_inicio"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "inicio", "STR_TO_DATE(inicio,'%d/%m/%Y')", HAVING_CRMETHOD, 25);
        $myReporte->setCampo("fin", lang("fecha_fin"), false, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "fin", "STR_TO_DATE(fin,'%d/%m/%Y')", HAVING_CRMETHOD, 25);
        $myReporte->setCampo("estado", lang("estado"), false, ESTADO_COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER), false, "comisiones.estado", "estado", WHERE_CRMETHOD, 30);
        /* GRUPO*/
        $myReporte->setGroup("comisiones.codigo");
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("anio"), "anio", array("YEAR(comisiones.fecha_creacion) = YEAR(CURDATE())"), WHERE_CRMETHOD, lang("comisiones_de_este_anio"));
        //print_r($myReporte);
    }

    /* PRESUPUESTOS */

    private function getReportePresupuestos(reportes_sistema $myReporte) {
        $myReporte->setTable("aspirantes_presupuestos");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("presupuestos.codigo", "desc");
        $nombreApellido = formatearNombreAspQuery();
        $no_es_alumno = lang("no_es_alumno");
        $es_alumno = lang("es_alumno");
        /* CAMPOS CONMSULTA */
        $myReporte->setField("presupuestos.codigo");
        $myReporte->setField("IF((SELECT count(aspirantes_alumnos.id_alumno) ".
                                "FROM aspirantes_alumnos ".
                                "WHERE aspirantes_alumnos.id_aspirante = aspirantes_presupuestos.cod_aspirante), ".
                                "'$es_alumno', '$no_es_alumno') AS estado_alumno");
        $myReporte->setField("DATE_FORMAT(presupuestos.fecha,'%d/%m/%Y') AS fecha_presupuesto");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_aspirante");
        $myReporte->setField("comisiones.nombre AS nombre_comision");
        $myReporte->setField("planes_pago.nombre AS nombre_plan");
        $myReporte->setField("general.cursos.nombre_es AS nombre_curso");
        $myReporte->setField("DATE_FORMAT(presupuestos.fecha_vigencia, '%d/%m/%Y') AS fecha_vigencia_presupuesto");
        /* JOIN */
        $myReporte->setJOIN("aspirantes", "aspirantes.codigo = aspirantes_presupuestos.cod_aspirante");
        $myReporte->setJOIN("presupuestos", "presupuestos.codigo = aspirantes_presupuestos.cod_presupuesto");
        $myReporte->setJOIN("comisiones", "comisiones.codigo = presupuestos.codcomision");
        $myReporte->setJOIN("planes_cursos_periodos", "planes_cursos_periodos.cod_plan_pago = presupuestos.cod_plan");
        $myReporte->setJOIN("planes_pago", "planes_pago.codigo = planes_cursos_periodos.cod_plan_pago");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = planes_cursos_periodos.cod_curso");
        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang("codigo"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "presupuestos.codigo", "presupuestos.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("estado_alumno", lang("estado_aspirante"), true, ESTADO_ASPIRANTE_CRTYPE, array(ES_IGUAL_CRFILTER), true, "estado_alumno", "estado_alumno", HAVING_CRMETHOD);
        $myReporte->setCampo("fecha_presupuesto", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "fecha_presupuesto", "STR_TO_DATE(fecha_presupuesto,'%d/%m/%Y')", HAVING_CRMETHOD, 25);
        $myReporte->setCampo("nombre_aspirante", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, null, HAVING_CRMETHOD, 55);
        $myReporte->setCampo("nombre_comision", lang("comision"), true, COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "comisiones.codigo", "comisiones.nombre", WHERE_CRMETHOD, 40);
        $myReporte->setCampo("nombre_plan", lang("frm_nuevaMatricula_plan"), true, PLANES_PAGO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "planes_pago.codigo", "planes_pago.nombre", WHERE_CRMETHOD, 45);
        $myReporte->setCampo("nombre_curso", lang('curso'), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "cursos.codigo", "cursos.nombre_es", WHERE_CRMETHOD, 50);
        $myReporte->setCampo("fecha_vigencia_presupuesto", lang('fecha_vigencia'), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "fecha_vigencia_presupuesto", "STR_TO_DATE(fecha_vigencia_presupuesto,'%d/%m/%Y')", HAVING_CRMETHOD, 30);
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("este_año"), "anio", array("YEAR(presupuestos.fecha) = YEAR(CURDATE())"), WHERE_CRMETHOD, lang("presupuestos_de_este_anio"));
        $myReporte->setFiltrosComunes(lang("mes"), "mes", array("MONTH(presupuestos.fecha) = MONTH(CURDATE())"), WHERE_CRMETHOD, lang("presupuestos_de_este_mes"));
        /* GRUPOS */
        $myReporte->setGroup("presupuestos.codigo");
        $myReporte->getColumns();
    }

    /*Rentabiliad - Gastos e Ingresos*/
    //array(array("label"=>"GASTOS",  "data"=>"45.0", "color"=>"#AA4643"),
    //      array("label"=>"INGRESOS",  "data"=>"55.0", "color"=>"#4572A7"));

    public function getReporteRentabiliadGastosEingresos($fecha_desde = false, $fecha_hasta = false, $jsone = null)
    {
        $conexion = $this->load->database($this->codigofilial, true, null, true);

        $gastos = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $fecha_desde, $fecha_hasta);
//        $ingresos = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'INGRESOS', $fecha_desde, $fecha_hasta);
        $ingresos = Vcobros::getCobrosEntreFechas($conexion, $fecha_desde, $fecha_hasta);
        $total = $gastos[0]['total'] + $ingresos[0]['total'];

        if($total == 0) $total = 1;

        $gastosPorcent = round(($gastos[0]['total'] * 100) / $total, 2);
        $ingresosPorcent = round(($ingresos[0]['total'] * 100) / $total, 2);

        if($gastosPorcent == 0)
        {
          $ingresosPorcent = 50;
          $gastosPorcent = 50;
        }

        $respuesta['grafico'] = array(array("label"=>lang("GASTOS"),  "data"=>$gastosPorcent, "color"=>"#AA4643"),
                           array("label"=>lang("INGRESOS"),  "data"=>$ingresosPorcent, "color"=>"#4572A7"));

       /* $respuesta['totalGastos'] = $gastos[0]['total'];
        $respuesta['totalIngresos'] = $ingresos[0]['total'];
        $respuesta['rentabilidad'] = round($ingresos[0]['total'] - $gastos[0]['total'], 2);
*/
        

//creo el json con el formato que me pide xCharts

        $respuesta['principal'] = array(
            'xScale'=>"ordinal",
            'yScale'=>"linear",
            'main'=> ''
        );
        $respuesta['tableR']['test'] = array(array($fecha_desde. " - ". $fecha_hasta,"$". number_format((float) $ingresos[0]['total'],2,',','.') ,"$".number_format((float) $gastos[0]['total'],2,',','.'), "$".number_format((float) ($ingresos[0]['total'] - $gastos[0]['total']),2,',','.')));
        $respuesta['principal']['main'] = array(
            array('className' => ".rentIngGas",
                'data'=>array(
                    array('x'=>lang("INGRESOS"), 'y'=>(float) $ingresos[0]['total'], 'z'=>'INGRESO'),
                )
            ),
            array('className' => ".rentIngGas2",
                'data'=>array(
                    array('x'=>lang("INGRESOS"), 'y'=>(float) $gastos[0]['total'], 'z'=>lang('GASTOS')),
                )

            ),
            array('className' => ".rentIngGas3",
                'data'=>array(
                    array('x'=>lang("INGRESOS"), 'y'=>(float) ($ingresos[0]['total'] - $gastos[0]['total']), 'z'=>lang('rentabilidad')),
                )

            )
        );
      
        return $respuesta;
    }

    /** ****************************
        Nuevo reporte rentabilidad2
        **************************** **/

    public function getReporteRentabilidad($fecha_desde = false, $fecha_hasta = false){
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $gastos = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $fecha_desde, $fecha_hasta);
        $ingresos = Vcobros::getCobrosEntreFechas($conexion, $fecha_desde, $fecha_hasta);
        $respuesta['ingreso'] = "$".number_format(round($ingresos[0]['total'],2),2,",",".");
        $respuesta['egreso'] = "$".number_format(round($gastos[0]['total'],2),2,",",".");
        $respuesta['utilidad'] = "$".number_format(round(($ingresos[0]['total'] - $gastos[0]['total']),2),2,",",".");
        if($ingresos[0]['total'] != 0){
            $respuesta['porc_total'] =round((($ingresos[0]['total'] - $gastos[0]['total']) * 100 / $ingresos[0]['total']),2);
        }else{
            $respuesta['porc_total'] = 0;
        }
        return $respuesta;

    }

    public function getReporteRentGastos($fecha_desde = false, $fecha_hasta = false)
    {
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $gastos = Vmovimientos_caja::getReporteRentabilidadGastos($conexion,true, $fecha_desde, $fecha_hasta, $this->codigofilial);
        
        $insertGasto = array();
        foreach ($gastos as $gasto) {
            array_push($insertGasto, array($gasto['label'] == null ? lang('REDEFINIR') : lang($gasto['label']),"$".number_format((float) $gasto['data'],2,",","."),$gasto['label'],$gasto['sub']));
        }
        $respuesta = $insertGasto;
        if(sizeof($respuesta) != 0){
        return $respuesta;
        }else{
            return array();
        }
    }

    public function  getReporteRentIngresos($fecha_desde = false, $fecha_hasta = false)
    {
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $ingresos = Vcobros::getCobrosEntreFechas($conexion, $fecha_desde, $fecha_hasta, true);

        $insertIngreso = array();
        foreach ($ingresos as $ingreso) {
            array_push($insertIngreso, array((lang($ingreso['label']) != null) ? lang($ingreso['label']) : $ingreso['label'], "$".number_format((float) $ingreso['data'],2,",","."),$ingreso['cod_concepto']));
        }
        $respuesta = $insertIngreso;
        if(sizeof($respuesta) != 0){
            return $respuesta;
        }else{
            return array();
        }
    }

    public function getDetGastos($gasto, $fecha_desde, $fecha_hasta, $cod_sub)
    {

        $conexion = $this->load->database($this->codigofilial, true, null, true);


        return Vmovimientos_caja::getReporteGastos2($conexion, $gasto, $fecha_desde, $fecha_hasta, $cod_sub, $this->codigofilial);
    }

    public function getDetIngresos($concepto, $fecha_desde, $fecha_hasta)
    {
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        
        // Traigo los cobros con el concepto ingresado
        $array_conceptos = Vcobros::getReporteIngresos2($conexion, $concepto, $fecha_desde, $fecha_hasta);

        //Para poder usar metodos del model de cobros
        $filial = $this->session->userdata('filial');
        $configCobros = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cobros", "", false, $configCobros);

        $respuesta = array();
        foreach ($array_conceptos as $concep){
            //Con el codigo de cobro, traigo descripciones de la imputacion del cobro
            $detalle = $this->Model_cobros->getImputacionesCobro($concep[0]);
            foreach ($detalle as $valor){
                //armo un array para introducirlo en la respuesta con los datos q necesito
                if($valor['cod_concepto'] == (string) $concepto){
                    $test = array($concep[2]. ", ". $concep[1]. ", " . $valor['descripcion'], $valor['valorImputacion']);
                    array_push($respuesta, $test);
                }
            }
            
        }

        
        return $respuesta;
    }



    /** ****************************
     *
     *******************************/

    public function  getReporteRentabilidadIngresos($fecha_desde = false, $fecha_hasta = false)
    {
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $ingresos = Vcobros::getCobrosEntreFechas($conexion, $fecha_desde, $fecha_hasta, true);
 //       $totalIngreso = Vcobros::getCobrosEntreFechas($conexion, $fecha_desde, $fecha_hasta);
//        echo "<pre>"; print_r($ingresos); echo "</pre>";
       /* $respuesta = array();
        foreach ($ingresos as $ingreso)
        {
            $data = round(($ingreso['data'] * 100) / $totalIngreso[0]['total'], 2);
            $label = ($ingreso['label']!=null) ? $ingreso['label'] : "otros";
            $respuesta[] = array("label"=>lang($label),  "data"=>$data, "total"=>$ingreso['data']);
        }

        if(count($respuesta) < 1)
        {
            $respuesta[] = array("label"=>"No existen registros",  "data"=>"100");
        }*/

        $insertIngreso = array();
        foreach ($ingresos as $ingreso) {
            array_push($insertIngreso, array('x' => (lang($ingreso['label']) != null) ? lang($ingreso['label']) : $ingreso['label'], 'y' => (float) $ingreso['data'], 'z' => $ingreso['label'], 'd' => $fecha_desde, 'h' => $fecha_hasta));
        }
        $respuesta = array(
            'xScale'=>"ordinal",
            'yScale'=>"linear",
            'main'=> array(
                array('className' => ".gasto",
                    'data'=> $insertIngreso))
        );

        return $respuesta;
    }

    public function getReporteRentabilidadGastos($fecha_desde = false, $fecha_hasta = false)
    {
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $gastos = Vmovimientos_caja::getReporteRentabilidadGastos($conexion,true, $fecha_desde, $fecha_hasta, $this->codigofilial);

       // $gastosTotal = Vmovimientos_caja::getReporteRentabilidadGastos($conexion, false, $fecha_desde, $fecha_hasta);
        /*
        $respuesta = array();
//        echo "<pre>"; print_r($gastos); echo "</pre>";
        foreach ($gastos as $gasto)
        {
            $data = round(($gasto['data'] * 100) / $gastosTotal[0]['data'], 2);
            $label = ($gasto['label'] != null) ? $gasto['label'] : "otros";
            $respuesta[] = array("label"=>lang($label),  "data"=>$data, "total"=>$gasto['data']);
        }

        if(count($respuesta) < 1)
        {
            $respuesta[] = array("label"=>"No existen registros",  "data"=>"100");
        }*/



        $insertGasto = array();
        foreach ($gastos as $gasto) {

            array_push($insertGasto, array('x' => lang($gasto['label']), 'y' => (float) $gasto['data'], 'z' => $gasto['label'], 'd' => $fecha_desde, 'h' => $fecha_hasta, 's' => $gasto['sub']));


            }
        $respuesta = array(
            'xScale'=>"ordinal",
            'yScale'=>"linear",
            'main'=> array(
                array('className' => ".gasto",
                    'data'=> $insertGasto))
        );
        
        return $respuesta;
    }

    public function getDataReporteRentabilidad($periodo = null){
        $periodo = date("Y");
        $conexion = $this->load->database($this->codigofilial, true, null, true);
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-01-01', $periodo.'-01-31');
        if(($periodo % 4 == 0) && (($periodo % 100 != 0) || ($periodo % 400 == 0))){
            $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-02-01', $periodo.'-02-29');
        } else {
            $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-02-01', $periodo.'-02-28');
        }
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-03-01', $periodo.'-03-31');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-04-01', $periodo.'-04-30');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-05-01', $periodo.'-05-31');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-06-01', $periodo.'-06-30');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-07-01', $periodo.'-07-31');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-08-01', $periodo.'-08-31');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-09-01', $periodo.'-09-30');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-10-01', $periodo.'-10-31');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-11-01', $periodo.'-11-30');
        $gastos[] = Vmovimientos_caja::getReporteRentabiliadGastosEingresos($conexion, 'GASTOS', $periodo.'-12-01', $periodo.'-12-31');

        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-01-01', $periodo.'-01-31');
        if(($periodo % 4 == 0) && (($periodo % 100 != 0) || ($periodo % 400 == 0))){
            $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-02-01', $periodo.'-02-29');
        } else {
            $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-02-01', $periodo.'-02-28');
        }
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-03-01', $periodo.'-03-31');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-04-01', $periodo.'-04-30');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-05-01', $periodo.'-05-31');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-06-01', $periodo.'-06-30');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-07-01', $periodo.'-07-31');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-08-01', $periodo.'-08-31');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-09-01', $periodo.'-09-30');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-10-01', $periodo.'-10-31');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-11-01', $periodo.'-11-30');
        $ingresos[] = Vcobros::getCobrosEntreFechas($conexion, $periodo.'-12-01', $periodo.'-12-31');
       /* $respueta = array();
        $respuesta['graph'] = array(
            'xScale'=>"ordinal",
            'yScale'=>"linear",
            'main'=> ''
        );
        $respueta['tableRen'] = array();
        $respuesta['graph']['main'] = array(
            array('className' => ".rentIngGas",
                'data'=>array()
            ),
            array('className' => ".rentIngGas2",
                'data'=>array()
            ),
            array('className' => ".rentIngGas3",
                'data'=>array()
            )
        );*/
        
        $i = 1;
        $insertGasto = array();
        $insertIng = array();
        $insertRent = array();
        $insertMes = array();
        $respuesta['tableRen']['table'] = array();
        foreach ($gastos as $clave => $valor) {

                $ingreso1 = $ingresos[$clave][0]['total'];
                $gasto1 = $valor[0]['total'];
               if(((float)$ingreso1) != 0 && ((float)$gasto1) != 0){
                $test = array('x' => (string) $i, 'y' => (float) $ingreso1, 'z'=>lang('INGRESOS'), 'gasOing'=> 'i');

                $test2 = array('x' =>(string) $i, 'y' => (float) $gasto1, 'z'=>lang('GASTOS'), 'gasOing'=> 'g');

                   $test3 = array('x' =>(string) $i, 'y' => (float)($ingreso1 - $gasto1), 'z'=>lang('rentabilidad'), 'gasOing'=> 'r');


                   array_push($insertIng, $test);
                   array_push($insertGasto, $test2);
                   array_push($insertRent, $test3);




                   $respuesta['graph'] = array(
                       'xScale'=>"ordinal",
                       'yScale'=>"linear",
                       'main'=> array(
                           array('className' => ".rentIngGas",
                               'data'=>$insertIng
                           ),
                           array('className' => ".rentIngGas2",
                               'data'=>$insertGasto
                           ),
                           array('className' => ".rentIngGas3",
                               'data'=>$insertRent
                           )
                   ));

                   $unarow = array(lang('0'.((string) $i)),"$".number_format($insertIng[$i-1]['y'],2,',','.'),"$".number_format($insertGasto[$i-1]['y'],2,',','.'),"$".number_format($insertRent[$i-1]['y'],2,',','.'));
                 //   var_dump($unarow);
                   array_push($respuesta['tableRen']['table'], $unarow);
               }
                $i++;
        }
        return $respuesta;
    }

    public function getReporteGastos($gasto, $fecha_desde, $fecha_hasta, $cod_sub)
    {

        $conexion = $this->load->database($this->codigofilial, true, null, true);


        return Vmovimientos_caja::getReporteGastos($conexion, $gasto, $fecha_desde, $fecha_hasta, $cod_sub);
    }
    public function getReporteIngresos($ingresos, $fecha_desde, $fecha_hasta)
    {

        $conexion = $this->load->database($this->codigofilial, true, null, true);


        return Vcobros::getReporteIngresos($conexion, $ingresos, $fecha_desde, $fecha_hasta);
    }
    
    

    public function getTicksReporteRentabilidad()
    {
        $ticks = array(array("0",lang("enero")), array("1",lang("febrero")), array("2",lang("marzo")), array("3",lang("abril")),
                 array("4",lang("mayo")), array("5",lang("junio")), array("6",lang("julio")),array("7",lang("agosto")), array("8",lang("septiembre")),
                 array("9",lang("octubre")), array("10",lang("noviembre")), array("11",lang("diciembre")));

        return $ticks;
    }


    /* CONSULTAS WEB */

    private function getReporteConsultasWeb(reportes_sistema $myReporte) {
        $myReporte->setTable("mails_consultas.mails_consultas");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("mails_consultas.fechahora", "desc");
        $myReporte->setPermanentWhere(array("mails_consultas.mails_consultas.cod_filial" => $this->codigofilial));
        /* CAMPOS CONSULTA */
        $myReporte->setField("DATE_FORMAT(mails_consultas.mails_consultas.fechahora ,'%d/%m/%Y') as fecha");
        $myReporte->setField("mails_consultas.mails_consultas.nombre");
        $myReporte->setField("mails_consultas.mails_consultas.asunto");
        $myReporte->setField("(SELECT mails_consultas.mails_respuesta_consultas.html_respuesta
                                    FROM mails_consultas.mails_respuesta_consultas
                                    WHERE mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.codigo
                                    AND mails_consultas.mails_respuesta_consultas.emisor = 0
                                    ORDER BY mails_consultas.mails_respuesta_consultas.codigo DESC LIMIT 0, 1) AS mensaje");
        $myReporte->setField("mails_consultas.mails_consultas.mail");
        $myReporte->setField("mails_consultas.mails_consultas.telefono");
        $myReporte->setField("IF (mails_consultas.mails_consultas.estado = 'noconcretada', '" . lang('noconcretada') . "',
                                IF (mails_consultas.mails_consultas.estado = 'eliminada', '" . lang('eliminada') . "',
                                IF (mails_consultas.mails_consultas.estado = 'abirta', '" . lang('abierta') . "',
                                IF (mails_consultas.mails_consultas.estado = 'concretada', '" . lang('concretada') . "',
                                IF (mails_consultas.mails_consultas.estado = 'cerrado', '" . lang('cerrado') . "',
                                IF (mails_consultas.mails_consultas.estado = 'pendiente', '" . lang('pendiente') . "',
                                mails_consultas.mails_consultas.estado)))))) AS estado");
        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) AS usuario_respuesta");
        $myReporte->setField("DATE_FORMAT(mails_consultas.mails_respuesta_consultas.fecha_hora, '%d/%m/%Y') AS fecha_consulta");
        $myReporte->setJOIN("mails_consultas.mails_respuesta_consultas", " mails_consultas.mails_respuesta_consultas.codigo = ".
                            " (select MAX(mrc.codigo) FROM mails_consultas.mails_respuesta_consultas AS mrc ".
                                    " WHERE mrc.cod_consulta = mails_consultas.mails_consultas.codigo".
                                    " AND mrc.emisor = 1)", "left");
        $myReporte->setJOIN("general.usuarios_sistema", "general.usuarios_sistema.codigo = mails_consultas.mails_respuesta_consultas.id_usuario", "left");

        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("fecha", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "fecha", "STR_TO_DATE(fecha,'%d/%m/%Y')", HAVING_CRMETHOD, 16);
        $myReporte->setCampo("nombre", lang("nombre"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "mails_consultas.mails_consultas.nombre", "mails_consultas.mails_consultas.nombre", WHERE_CRMETHOD, 34);
        $myReporte->setCampo("asunto", lang("asunto"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "mails_consultas.mails_consultas.asunto", "mails_consultas.mails_consultas.asunto", WHERE_CRMETHOD, 52);
        $myReporte->setCampo("mensaje", lang("mensaje_comunicados"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, "mensaje", HAVING_CRMETHOD, 60);
        $myReporte->setCampo("mail", lang("email"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "mails_consultas.mails_consultas.mail", "mails_consultas.mails_consultas.mail", WHERE_CRMETHOD, 35);
        $myReporte->setCampo("telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "mails_consultas.mails_consultas.telefono", "mails_consultas.mails_consultas.telefono", WHERE_CRMETHOD, 18);
        $myReporte->setCampo("estado", lang("estado_certificado"), true, ESTADOS_CONSULTAS_WEB_CRTYPE, array(ES_IGUAL_CRFILTER), false, "mails_consultas.mails_consultas.estado", "mails_consultas.mails_consultas.estado", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("usuario_respuesta", lang("usuario"), true, USUARIOS_FILIAL_CRTYPE, array(ES_IGUAL_CRFILTER), true, "general.usuarios_sistema.codigo", "usuario_respuesta", WHERE_CRMETHOD, 25);
        $myReporte->setCampo("fecha_consulta", lang("fecha_consulta"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "fecha_consulta", "STR_TO_DATE(fecha_consulta,'%d/%m/%Y')", HAVING_CRMETHOD, 22);
    }

    /* REPORTE DE COBROS */

    private function getReporteCobros(reportes_sistema $myReporte) {
        $extension = lang("_idioma");
        $myReporte->setTable("cobros");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("cobros.fechareal", "desc");
        $nombreApellido = formatearNomApeQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo AS cod_alumno");
        $myReporte->setField("matriculas.codigo AS cod_matricula");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
        $myReporte->setField("cobros.codigo");

        $fieldConcepto = "IF ( cod_concepto = 1,
            'Curso',
            IF( cod_concepto = 3,
               'Mora',
               IF( cod_concepto IN (SELECT codigo FROM (`conceptos`) WHERE `codigo_padre` =  0 AND `codigo` IN (SELECT codigo_padre FROM conceptos WHERE `key`='USUARIO_CREADOR')),
                  (SELECT `key` FROM (`conceptos`) WHERE codigo=cod_concepto),
                  'Matricula'
               )
            )
         ) AS concepto";

        $myReporte->setField($fieldConcepto);
        $myReporte->setField("general.cursos.nombre_$extension AS curso_nombre");
        //$myReporte->setField("IF ( cod_concepto = 1, 'Curso', IF( cod_concepto = 3, 'Mora', 'Matrícula')) AS concepto");
        $myReporte->setField("nrocuota");
        //mmori - recupero el importe desde imputaciones para no duplicar el valor cuando se paga más de una cuota junta
        $myReporte->setField("ctacte_imputaciones.valor as importe");
        $myReporte->setField("IF (general.medios_pago.medio = 'EFECTIVO', '" . lang('EFECTIVO') . "', IF (general.medios_pago.medio = 'BOLETO_BANCARIO', '" . lang('BOLETO_BANCARIO') . "', IF (general.medios_pago.medio = 'TARJETA', '" . lang('TARJETA') . "', IF (general.medios_pago.medio = 'CHEQUE', '" . lang('CHEQUE') . "', IF (general.medios_pago.medio = 'DEPOSITO_BANCARIO', '" . lang('DEPOSITO_BANCARIO') . "', IF (general.medios_pago.medio = 'TRANSFERENCIA', '" . lang('TRANSFERENCIA') . "', general.medios_pago.medio)))))) AS medio");
        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) as nombre_usuario");
        $myReporte->setField("DATE_FORMAT(cobros.fechareal,'%d/%m/%Y') AS fecha");
        $myReporte->setField("cobros.estado");
        $myReporte->setField("CONCAT (general.documentos_tipos.nombre, ': ',alumnos.documento) AS documento");

       // $myReporte->setField("alumnos.calle as calle");
        $myReporte->setField("IF (alumnos.calle_numero = 0, alumnos.calle, CONCAT (alumnos.calle, ', ', alumnos.calle_numero)) AS calle");
        $myReporte->setField("alumnos.barrio as barrio");
        $myReporte->setField("alumnos.codpost as cp");
        $myReporte->setField("general.localidades.nombre as localidad");



        $myReporte->setJOIN("ctacte_imputaciones", "ctacte_imputaciones.cod_cobro = cobros.codigo");
        $myReporte->setJOIN("ctacte", "ctacte.codigo = ctacte_imputaciones.cod_ctacte");
        $myReporte->setJOIN("alumnos", "alumnos.codigo = cobros.cod_alumno");
        $myReporte->setJOIN("matriculas", "matriculas.cod_alumno = alumnos.codigo AND matriculas.codigo = ctacte.concepto","LEFT");
        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
        $myReporte->setJOIN("general.medios_pago", "general.medios_pago.codigo = cobros.medio_pago");
        $myReporte->setJOIN("general.usuarios_sistema", "general.usuarios_sistema.codigo = cobros.cod_usuario","LEFT");
        $myReporte->setJOIN("caja", "caja.codigo = cobros.cod_caja","LEFT");
        $myReporte->setJOIN("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico", "LEFT");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso", "LEFT");

        $myReporte->setJOIN("general.localidades", "localidades.id = alumnos.id_localidad");





        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("cod_alumno", lang("cod_alumno"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "alumnos.codigo", "alumnos.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("cod_matricula", lang("cod_matricula"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "matriculas.codigo", "matriculas.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("nombre_alumno", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "nombre_alumno", "nombre_alumno", HAVING_CRMETHOD, 48);
        $myReporte->setCampo("codigo", lang("codigo"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "cobros.codigo", "cobros.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("concepto", lang("concepto"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "concepto", HAVING_CRMETHOD, 58);
        $myReporte->setCampo("curso_nombre", lang("curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.cursos.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("nrocuota", lang("n_de_cuotas"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "nrocuota", WHERE_CRMETHOD, 58);
        $myReporte->setCampo("importe", lang("importe"), true,  FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "cobros.importe", "cobros.importe", WHERE_CRMETHOD, 20,true, true);
        $myReporte->setCampo("medio", lang("medio_pago"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "medio", "medio", HAVING_CRMETHOD, 48);
        $myReporte->setCampo("nombre_usuario", lang("nombre_usuario"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, "nombre_usuario", "nombre_usuario", HAVING_CRMETHOD, 48);
        $myReporte->setCampo("fecha", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "cobros.fechareal", "cobros.fechareal", WHERE_CRMETHOD, 25);
        $myReporte->setCampo("estado", lang("estado"), true, ESTADOS_COBROS_CRTYPE, array(ES_IGUAL_CRFILTER), true, "cobros.estado", "cobros.estado", WHERE_CRMETHOD, 48);
        $myReporte->setCampo("documento", lang("documento"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "documento", HAVING_CRMETHOD, 58);

        $myReporte->setCampo("calle", lang("calle_alumno"),false, STRING_CRTYPE, array(ES_IGUAL_CRFILTER),false, "calle", "calle", WHERE_CRMETHOD,58  );
        $myReporte->setCampo("barrio", lang("datos_barrio"),false, STRING_CRTYPE, array(ES_IGUAL_CRFILTER),false, "barrio", "barrio", WHERE_CRMETHOD,58  );
        $myReporte->setCampo("cp", lang("codigo_postal"), false, STRING_CRTYPE, array(ES_IGUAL_CRFILTER), false, "cp", "cp", WHERE_CRMETHOD, 58);
        $myReporte->setCampo("localidad", lang("localidad"), false, STRING_CRTYPE, array(ES_IGUAL_CRFILTER), false, "localidad", "localidad", WHERE_CRMETHOD, 58);


        /* CAMPOS CONSULTA */
//        $myReporte->setField("cobros.codigo");
//        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
//         //siwakawa
//        $curso = lang("curso");
//        $mora = lang("MORA");
//        $matricula = lang("matricula");
//        $myReporte->setField('CONCAT (IF ( cod_concepto = 1,"'.$curso.'",'
//                                        .'IF( cod_concepto = 3,"'.$mora.'","'.$matricula.'")) ,'
//                                     . '": ",'
//                                     . '"'.lang("cuota").' ",'
//                                     . 'nrocuota) AS descripcion');
//        //ticket 4563 -mmori- se agrega una columna al reporte
//        $myReporte->setField("CONCAT (general.documentos_tipos.nombre, ': ',alumnos.documento) AS documento");
//        $myReporte->setField("ctacte_imputaciones.valor as importe");
//        $myReporte->setField("ctacte_imputaciones.valor as total_imputado");
//        //$myReporte->setField("(SELECT IFNULL(SUM(ctacte_imputaciones.valor), 0) FROM ctacte_imputaciones where ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.estado = 'confirmado') AS total_imputado");
//        $myReporte->setField("0 as saldo");
//        //$myReporte->setField("cobros.importe - (SELECT IFNULL(SUM(ctacte_imputaciones.valor), 0) FROM ctacte_imputaciones where ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.estado = 'confirmado' AND ctacte_imputaciones.tipo = 'COBRO') as saldo");
//        $myReporte->setField("IF (general.medios_pago.medio = 'EFECTIVO', '" . lang('EFECTIVO') . "',
//                                    IF (general.medios_pago.medio = 'BOLETO_BANCARIO', '" . lang('BOLETO_BANCARIO') . "',
//                                    IF (general.medios_pago.medio = 'TARJETA', '" . lang('TARJETA') . "',
//                                    IF (general.medios_pago.medio = 'CHEQUE', '" . lang('CHEQUE') . "',
//                                    IF (general.medios_pago.medio = 'DEPOSITO_BANCARIO', '" . lang('DEPOSITO_BANCARIO') . "',
//                                    IF (general.medios_pago.medio = 'TRANSFERENCIA', '" . lang('TRANSFERENCIA') . "',
//                                    general.medios_pago.medio)))))) AS medio");
//        $myReporte->setField("IFNULL(caja.nombre, '(" . lang("sin_caja") . ")') AS nombre_caja");
//        $myReporte->setField("CONCAT(LPAD(DAY(cobros.fechareal), 2, 0), '/', LPAD(MONTH(cobros.fechareal), 2, 0), '/', YEAR(cobros.fechareal)) AS fecha_cobro");
//        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) as nombre_usuario");
//        $myReporte->setField("IF (cobros.estado = 'confirmado', '" . lang('confirmado') . "',
//                                    IF (cobros.estado = 'anulado', '" . lang('anulado') . "',
//                                    IF (cobros.estado = 'pendiente', '" . lang('pendiente') . "',
//                                    cobros.estado))) AS estado");
//        $myReporte->setField("cobros.periodo");
//        /* JOIN */
//        $myReporte->setJOIN("ctacte_imputaciones", "ctacte_imputaciones.cod_cobro = cobros.codigo");
//        $myReporte->setJOIN("ctacte", "ctacte.codigo = ctacte_imputaciones.cod_ctacte");
//        $myReporte->setJOIN("alumnos", "alumnos.codigo = cobros.cod_alumno");
//        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
//        $myReporte->setJOIN("general.medios_pago", "general.medios_pago.codigo = cobros.medio_pago");
//        $myReporte->setJOIN("general.usuarios_sistema", "general.usuarios_sistema.codigo = cobros.cod_usuario", "LEFT");
//        $myReporte->setJOIN("caja", "caja.codigo = cobros.cod_caja", "LEFT");
//        /* CAMPOS MOSTRAR */
//        $nombreApellido = formatearNombreColumnaAlumno();
//        $myReporte->setCampo("codigo", lang("codigo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "cobros.codigo", "cobros.codigo", WHERE_CRMETHOD, 20);
//        $myReporte->setCampo("nombre_alumno", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "nombre_alumno", HAVING_CRMETHOD, 58);
//        $myReporte->setCampo("descripcion", lang("concepto"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "descripcion", HAVING_CRMETHOD, 58);
//        //ticket 4563 -mmori- se agrega una columna al reporte
//        $myReporte->setCampo("documento", lang("documento"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "documento", HAVING_CRMETHOD, 58);
//        $myReporte->setCampo("importe", lang("importe"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "ctacte_imputaciones.valor", "ctacte_imputaciones.valor", WHERE_CRMETHOD, 25, true, true);
//        $myReporte->setCampo("total_imputado", lang("imputado"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "total_imputado", HAVING_CRMETHOD, 25);
//        $myReporte->setCampo("saldo", lang("saldo"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "saldo", HAVING_CRMETHOD, 20);
//        $myReporte->setCampo("medio", lang("medio_de_pago"), true, MEDIOS_PAGO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.medios_pago.codigo", "general.medios_pago.medio", WHERE_CRMETHOD, 40);
//        $myReporte->setCampo("nombre_caja", lang("caja"), true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "caja.nombre", "caja.nombre", WHERE_CRMETHOD, 40);
//        $myReporte->setCampo("fecha_cobro", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "cobros.fechareal", "cobros.fechareal", WHERE_CRMETHOD, 25);
//        $myReporte->setCampo("nombre_usuario", lang("nombre_usuario"), false, USUARIOS_FILIAL_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.usuarios_sistema.codigo", "nombre_usuario", WHERE_CRMETHOD, 45);
//        $myReporte->setCampo("estado", lang("estado_academico_estado"), true, ESTADOS_COBROS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "cobros.estado", "cobros.estado", WHERE_CRMETHOD, 25);
//        $myReporte->setCampo("periodo", lang("periodo"), true, PERIODOS_ROYALTYS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "cobros.periodo", "cobros.periodo", WHERE_CRMETHOD, 40, false);
    }

    /* REPORTE DE CAJAS */

    private function reporte_movimientos_cajas(reportes_sistema $myReporte) {
        $myReporte->setTable("movimientos_caja");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("movimientos_caja.codigo", "desc");
        /* CAMPOS CONSULTA */
        $myReporte->setField("movimientos_caja.codigo");
        $myReporte->setField("CONCAT(LPAD(DAY(movimientos_caja.fecha_hora) , 2, 0), '/', LPAD(MONTH(movimientos_caja.fecha_hora), 2, 0), '/', YEAR(movimientos_caja.fecha_hora), ' ', TIME(movimientos_caja.fecha_hora)) AS fecha");
        $myReporte->setField("caja.nombre");
        //Ticket 4678 -mmori- agrego concatenacion de las observaciones guardadas en movimientos_caja
        $myReporte->setField(
                "IF (movimientos_caja.cod_concepto = 'PAGOS', CONCAT('" . lang("compra_a_proveedor") . ' ' . "',(SELECT razones_sociales.razon_social FROM razones_sociales JOIN proveedores ON proveedores.cod_razon_social = razones_sociales.codigo JOIN pagos ON pagos.concepto = 'PROVEEDOR' AND pagos.cod_concepto = proveedores.codigo WHERE movimientos_caja.concepto = pagos.codigo), '(', IFNULL(movimientos_caja.observacion,'Sin observacion'),')'),
                IF(movimientos_caja.cod_concepto = 'COBROS',CONCAT('" . lang("cobro_alumno") . ' ' . "',(SELECT CONCAT(alumnos.nombre, ' ' , alumnos.apellido) FROM alumnos JOIN cobros ON cobros.cod_alumno = alumnos.codigo  WHERE movimientos_caja.concepto = cobros.codigo),'(', IFNULL(movimientos_caja.observacion,'Sin observacion'),')'),
                IF (movimientos_caja.cod_concepto = 'APERTURA',CONCAT('" . lang("apertura_de_caja") . ' ' . "', '(', IFNULL(movimientos_caja.observacion,'Sin observacion'),')'),
                IF (movimientos_caja.cod_concepto = 'PARTICULARES',CONCAT('" . lang("movimiento_particular") . ' ' . "', '(', IFNULL(movimientos_caja.observacion,'Sin observacion'),')'),
                IF (movimientos_caja.cod_concepto = 'CIERRE',CONCAT('" . lang("cierre_de_caja") . ' ' . "', '(', IFNULL(movimientos_caja.observacion,'Sin observacion'),')'),
                IF (movimientos_caja.cod_concepto = 'TRANFERENCIA',CONCAT('" . lang("transferencia_entre_cajas") . ' ' . "', '(', IFNULL(movimientos_caja.observacion,'Sin observacion'),')'),'-')))))) AS descripcion");
        $myReporte->setField("movimientos_caja.haber");
        $myReporte->setField("movimientos_caja.debe");
        $myReporte->setField("movimientos_caja.saldo");
        $myReporte->setField("IF (movimientos_caja.cod_concepto = 'APERTURA', '" . lang('APERTURA') . "',
                                IF (movimientos_caja.cod_concepto = 'PARTICULARES', '" . lang('PARTICULARES') . "',
                                IF (movimientos_caja.cod_concepto = 'CIERRE', '" . lang('CIERRE') . "',
                                IF (movimientos_caja.cod_concepto = 'PAGOS', CONCAT('" . lang('PAGOS') . "',' ',movimientos_caja.concepto),
                                IF (movimientos_caja.cod_concepto = 'COBROS', CONCAT('" . lang('COBROS') . "',' ',movimientos_caja.concepto),
                                IF (movimientos_caja.cod_concepto = 'TRANSFERENCIA', '" . lang('TRANSFERENCIA') . "',
                                movimientos_caja.cod_concepto)))))) AS cod_concepto");
        $myReporte->setField("IF (general.medios_pago.medio = 'EFECTIVO', '" . lang('EFECTIVO') . "',
                                IF (general.medios_pago.medio = 'BOLETO_BANCARIO', '" . lang('BOLETO_BANCARIO') . "',
                                IF (general.medios_pago.medio = 'TARJETA', '" . lang('TARJETA') . "',
                                IF (general.medios_pago.medio = 'CHEQUE', '" . lang('CHEQUE') . "',
                                IF (general.medios_pago.medio = 'NOTA_CREDITO', '" . lang('NOTA_CREDITO') . "',
                                IF (general.medios_pago.medio = 'DEPOSITO_BANCARIO', '" . lang('DEPOSITO_BANCARIO') . "',
                                IF (general.medios_pago.medio = 'TRANSFERENCIA', '" . lang('TRANSFERENCIA') . "',
                                general.medios_pago.medio))))))) AS medio");
        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) AS nombre_usuario");
        /* JOIN */
        $myReporte->setJOIN("caja", "caja.codigo = movimientos_caja.cod_caja");
        $myReporte->setJOIN("general.usuarios_sistema", "general.usuarios_sistema.codigo = movimientos_caja.cod_user");
        $myReporte->setJOIN("general.medios_pago", "general.medios_pago.codigo = movimientos_caja.cod_medio");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("codigo", lang("codigo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER), false, "movimientos_caja.codigo", "movimientos_caja.codigo", WHERE_CRMETHOD, 14);
        $myReporte->setCampo("fecha", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "DATE(movimientos_caja.fecha_hora)", "movimientos_caja.fecha_hora", WHERE_CRMETHOD, 35);
        $myReporte->setCampo("nombre", lang("nombre"), true, CAJAS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "caja.codigo", "caja.nombre", WHERE_CRMETHOD, 28);
        $myReporte->setCampo("descripcion", lang("descripcion"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 65);
        $myReporte->setCampo("haber", lang("entrada_caja_cabecera"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "movimientos_caja.haber", "movimientos_caja.haber", WHERE_CRMETHOD, 19, true, true);
        $myReporte->setCampo("debe", lang("salida_caja_cabecera"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "movimientos_caja.debe", "movimientos_caja.debe", WHERE_CRMETHOD, 19, true, true);
        $myReporte->setCampo("saldo", lang("saldo"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "movimientos_caja.saldo", "movimientos_caja.saldo", WHERE_CRMETHOD, 19);
        $myReporte->setCampo("cod_concepto", lang("concepto"), true, CONCEPTOS_CAJA_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "movimientos_caja.cod_concepto", "movimientos_caja.cod_concepto", WHERE_CRMETHOD, 26);
        $myReporte->setCampo("medio", lang("medio_de_pago"), true, MEDIOS_PAGO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.medios_pago.codigo", "general.medios_pago.medio", WHERE_CRMETHOD, 34);
        //ticket 4602 - mmori - se agrega parametro a la llamada del metodo setCampo para el campo nombre_usuario ya que faltaba uno entre $whereField y $tipoConsulta.
        $myReporte->setCampo("nombre_usuario", lang("usuario"), true, USUARIOS_FILIAL_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.usuarios_sistema.codigo", "general.usuarios_sistema.nombre", WHERE_CRMETHOD, 25);
    }

    /* REPORTE DE FACTURAS */

    private function getReporteFacturas(reportes_sistema $myReporte) {
        $myReporte->setTable("facturas");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("facturas.fecha", "desc");
        $nombreApellido = formatearNomApeQuery();

        $myReporte->setField("CONCAT(LPAD(DAY(facturas.fecha), 2, 0), '/', LPAD(MONTH(facturas.fecha), 2, 0), '/', YEAR(facturas.fecha)) AS fecha_factura");
        $myReporte->setField("facturas_propiedades.valor AS nrofact");
        $myReporte->setField("general.puntos_venta.prefijo");

        $myReporte->setField("general.razones_sociales_general.razon_social");

        $myReporte->setField("razones_sociales.razon_social AS cliente");
        $myReporte->setField("(SELECT CONCAT($nombreApellido) FROM alumnos
                                INNER JOIN alumnos_razones ON alumnos_razones.cod_alumno = alumnos.codigo
                                WHERE alumnos_razones.cod_razon_social = facturas.codrazsoc limit 0,1) AS nombre_alumno");
        $myReporte->setField("razones_sociales.documento AS cuit");
        $myReporte->setField("(SELECT general.condiciones_sociales.condicion FROM general.condiciones_sociales WHERE general.condiciones_sociales.codigo = razones_sociales.condicion) AS condicion");
        $myReporte->setField("(SELECT SUM(facturas_renglones.importe) FROM facturas_renglones where facturas_renglones.cod_factura = facturas.codigo and facturas_renglones.anulada = 0) AS importe");
        $myReporte->setField("(SELECT general.medios_pago.medio FROM general.medios_pago WHERE general.medios_pago.codigo = cobros.medio_pago) AS medio_pago");
        $myReporte->setField("IF (facturas.estado = 'habilitada', 'Habilitada', 'anulada') AS estado");

        $myReporte->setJOIN("facturas_propiedades", "facturas_propiedades.cod_factura = facturas.codigo AND facturas_propiedades.propiedad = 'numero_factura'");
        $myReporte->setJOIN("razones_sociales", "razones_sociales.codigo = facturas.codrazsoc");
        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = razones_sociales.tipo_documentos");
        $myReporte->setJOIN("facturas_cobros", "facturas_cobros.cod_factura = facturas.codigo", "LEFT");
        $myReporte->setJOIN("cobros", "cobros.codigo = facturas_cobros.cod_cobro", "LEFT");
        $myReporte->setJOIN("general.puntos_venta", "general.puntos_venta.codigo = facturas.punto_venta");
        $myReporte->setJOIN("general.facturantes", "general.facturantes.codigo = general.puntos_venta.cod_facturante");
        $myReporte->setJOIN("general.razones_sociales_general", "general.razones_sociales_general.codigo = general.facturantes.cod_razon_social");

        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("fecha_factura", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "facturas.fecha", "facturas.fecha", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("nrofact", lang("n_de_comprobante"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "facturas_propiedades.valor", "facturas_propiedades.valor", WHERE_CRMETHOD, 25);
        $myReporte->setCampo("prefijo", lang("punto_venta"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "prefijo", "prefijo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("razon_social", lang("facturante"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "general.razones_sociales_general.razon_social", "general.razones_sociales_general.razon_social", WHERE_CRMETHOD, 20);

        $myReporte->setCampo("cliente", lang("razon_social"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "cliente", "cliente", HAVING_CRMETHOD, 50);
        $myReporte->setCampo("nombre_alumno", $nombreApellido, false, ALUMNOS_NOMBRES_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, 'nombre_alumno', HAVING_CRMETHOD, 70);
        $myReporte->setCampo("cuit", lang("cuit"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "razones_sociales.documento", "razones_sociales.documento", HAVING_CRMETHOD, 25);
        $myReporte->setCampo("condicion", lang("razon_condicion"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "condicion", "condicion", HAVING_CRMETHOD, 35);
        $myReporte->setCampo("importe", lang("total"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "facturas_renglones.importe", "facturas_renglones.importe", WHERE_CRMETHOD, 20, true, true);
        $myReporte->setCampo("medio_pago", lang("medio_pago"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "medio_pago", "medio_pago", HAVING_CRMETHOD, 35);
        $myReporte->setCampo("estado", lang("estado"), true, ESTADO_FACTURAS_CRTYPE, array(LIKE_CRFILTER), true, "estado", "estado", HAVING_CRMETHOD, 35);
    }

    private function getReporteCaja(reportes_sistema $myReporte) {
        $nombreTabla = "reportes_sistema.reportes_cajas_{$this->codigofilial}";
        $myReporte->setTable($nombreTabla);
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("{$nombreTabla}.fecha", "desc");
        /* CAMPOS CONSULTA */
        $myReporte->setField("CONCAT(LPAD(DAY($nombreTabla.fecha), 2, 0), '/', "
                . "LPAD(MONTH($nombreTabla.fecha), 2, 0), '/', YEAR($nombreTabla.fecha), ' ', "
                . "TIME($nombreTabla.fecha)) AS fecha");
        $myReporte->setField("caja.nombre");
        $myReporte->setField("IF(general.medios_pago.medio = 'EFECTIVO', '" . lang('EFECTIVO') . "',
                                IF (general.medios_pago.medio = 'BOLETO_BANCARIO', '" . lang('BOLETO_BANCARIO') . "',
                                IF (general.medios_pago.medio = 'TARJETA', '" . lang('TARJETA') . "',
                                IF (general.medios_pago.medio = 'CHEQUE', '" . lang('CHEQUE') . "',
                                IF (general.medios_pago.medio = 'NOTA_CREDITO', '" . lang('NOTA_CREDITO') . "',
                                IF (general.medios_pago.medio = 'DEPOSITO_BANCARIO', '" . lang('DEPOSITO_BANCARIO') . "',
                                IF (general.medios_pago.medio = 'TRANSFERENCIA', '" . lang('TRANSFERENCIA') . "',
                                general.medios_pago.medio))))))) AS medio");
        $myReporte->setField("$nombreTabla.debe");
        $myReporte->setField("$nombreTabla.haber");
        $myReporte->setField("$nombreTabla.saldo");
        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) AS usuario_nombre");
        /* JOIN */
        $myReporte->setJOIN("general.usuarios_sistema", "general.usuarios_sistema.codigo = {$nombreTabla}.cod_usuario");
        $myReporte->setJOIN("general.medios_pago", "general.medios_pago.codigo = {$nombreTabla}.cod_medio");
        $myReporte->setJOIN("caja", "caja.codigo = {$nombreTabla}.cod_caja");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("fecha", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "{$nombreTabla}.fecha", "{$nombreTabla}.fecha", WHERE_CRMETHOD, 40);
        $myReporte->setCampo("nombre", lang("caja"), true, CAJAS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "caja.codigo", "caja.nombre", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("medio", lang("medio_de_pago"), true, MEDIOS_PAGO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.medios_pago.codigo", "general.medios_pago.medio", WHERE_CRMETHOD, 35);
        $myReporte->setCampo("debe", lang("debe_ctacte"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "{$nombreTabla}.debe", "{$nombreTabla}.debe", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("haber", lang("haber"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "{$nombreTabla}.haber", "{$nombreTabla}.haber", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("saldo", lang("saldo"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "{$nombreTabla}.saldo", "{$nombreTabla}.saldo", WHERE_CRMETHOD, 35);
        $myReporte->setCampo("usuario_nombre", lang("usuario_caja_cabecera"), true, USUARIOS_FILIAL_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.usuarios_sistema.codigo", "usuarios_nombre", WHERE_CRMETHOD, 50);
    }

    function crons_reportes_caja() {
        $conexion_default = $this->load->database("default", true, null, false);
        $conexion_reportes_sistema = $this->load->database("reportes_sistema", true, null, false);
        $arrFiliales = Vfiliales::listarFiliales($conexion_default, array("version_sistema" => 2));
        foreach ($arrFiliales as $filial) {
            $codFilial = $filial['codigo'];
            if (reportes_cajas::validarTabla($conexion_reportes_sistema, $codFilial)) {  // chequea que la tabla del reporte actual exista, caso contrario intenta crearla y continuar el script
                $conexion = $this->load->database($codFilial, true, null, true);
                $arrCajas = Vcaja::listarCaja($conexion);
                $ultimosRegistros = reportes_cajas::getUltimosRegistrados($conexion_reportes_sistema, $codFilial);
                foreach ($arrCajas as $caja) {
                    $codCaja = $caja['codigo'];
                    $ultimoRegistro = isset($ultimosRegistros[$codCaja]) ? $ultimosRegistros[$codCaja] : 0;
                    $arrRegistros = reportes_cajas::getRegistrosActualizar($conexion, $codCaja, $ultimoRegistro);
                    $conexion->trans_begin();
                    foreach ($arrRegistros as $registro) {
                        $codCaja = $registro['cod_caja'];
                        $codApertura = $registro['codigo_apertura'];
                        $myReporte = new reportes_cajas($conexion_reportes_sistema, $codFilial, $codCaja, $codApertura);
                        $myReporte->cod_medio = $registro['cod_medio'];
                        $myReporte->cod_usuario = $registro['cod_user'];
                        $myReporte->debe = (double) $registro['cierre_debe'];
                        $myReporte->fecha = $registro['fecha_hora_real'];
                        $myReporte->haber = (double) $registro['cierre_haber'];
                        $myReporte->saldo = (double) $registro['cierre_saldo'];
                        $myReporte->guardar();
                    }
                    if ($conexion->trans_status()) {
                        $conexion->trans_commit();
                    } else {
                        $conexion->trans_rollback();
                    }
                }
            }
        }
    }

    private function getReporteComprobantesCompras(reportes_sistema $myReporte) {
        $myReporte->setTable("compras_comprobantes");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("compras_comprobantes.fecha_comprobante", "desc");
        /* CAMPOS CONSULTA */
        $myReporte->setField("compras_comprobantes.cod_compra");
        $myReporte->setField("CONCAT(LPAD(DAY(compras_comprobantes.fecha_comprobante), 2, 0), '/', LPAD(MONTH(compras_comprobantes.fecha_comprobante), 2, 0), '/', YEAR(compras_comprobantes.fecha_comprobante)) AS fecha_comprobante");
        $myReporte->setField("general.comprobantes.nombre AS comprobante");
        $myReporte->setField("IF(general.tipos_facturas.factura IS NULL, '-',general.tipos_facturas.factura) AS tipo_factura");
        $myReporte->setField("compras_comprobantes.nro_comprobante");
        $myReporte->setField("IFNULL(compras_tipos_factura.punto_venta, ' ') as punto_venta");
        $myReporte->setField("(SELECT razones_sociales.razon_social from razones_sociales where razones_sociales.codigo = proveedores.cod_razon_social) as proveedor");
        $myReporte->setField("general.condiciones_sociales.condicion AS condicion_social");
        $myReporte->setField("CONCAT(general.documentos_tipos.nombre, ' ', razones_sociales.documento) AS documento");
        $myReporte->setField('(SELECT CONCAT(razones_sociales.direccion_calle, " ", IFNULL(razones_sociales.direccion_numero, " ")," ", IFNULL(razones_sociales.direccion_complemento, " ")) from razones_sociales where razones_sociales.codigo = proveedores.cod_razon_social )AS direccion');
        $myReporte->setField("proveedores.cod_postal");
        $myReporte->setField("general.localidades.nombre AS localidad");
        $myReporte->setField("general.provincias.nombre AS provincia");
        //agrego categoria
        $myReporte->setField("(SELECT GROUP_CONCAT(articulos_categorias.nombre, ' ') FROM articulos_categorias WHERE articulos_categorias.codigo IN
                                (SELECT articulos.cod_categoria
                                    FROM articulos JOIN compras_renglones ON compras_renglones.cod_articulo = articulos.codigo
                                    WHERE compras_renglones.cod_compra = compras_comprobantes.cod_compra GROUP BY articulos.cod_categoria)) AS categoria");
        $myReporte->setField("(SELECT GROUP_CONCAT(IF(articulos.nombre IS NULL, ' ',articulos.nombre), ' ') FROM articulos JOIN compras_renglones ON compras_renglones.cod_articulo = articulos.codigo WHERE compras_renglones.cod_compra = compras_comprobantes.cod_compra) AS articulos");
        $myReporte->setField("compras_comprobantes.total");
        $myReporte->setField("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido) as nombre_usuario");
        /* JOIN */
        $myReporte->setJOIN("general.comprobantes", "general.comprobantes.id = compras_comprobantes.cod_comprobante");
        $myReporte->setJOIN("compras_tipos_factura", "compras_tipos_factura.cod_compra_comprobante = compras_comprobantes.codigo", "LEFT");
        $myReporte->setJOIN("general.tipos_facturas", "general.tipos_facturas.codigo = compras_tipos_factura.cod_tipo_factura", "LEFT");
        $myReporte->setJOIN("compras", "compras.codigo = compras_comprobantes.cod_compra");
        $myReporte->setJOIN("proveedores", "proveedores.codigo = compras.cod_proveedor");
        $myReporte->setJOIN("razones_sociales", "razones_sociales.codigo = proveedores.cod_razon_social");
        $myReporte->setJOIN("general.condiciones_sociales", "general.condiciones_sociales.codigo = razones_sociales.condicion");
        $myReporte->setJOIN("general.documentos_tipos", "general.documentos_tipos.codigo = razones_sociales.tipo_documentos", "LEFT");
        $myReporte->setJOIN("general.localidades", "general.localidades.id = razones_sociales.cod_localidad");
        $myReporte->setJOIN("general.provincias", "general.provincias.id = general.localidades.provincia_id");
        $myReporte->setJOIN("general.usuarios_sistema", "general.usuarios_sistema.codigo = compras.cod_usuario_creador");
        /* CONDICIONES PERMANENTES */
        $myReporte->setPermaneteWhereLineal(array("compras.estado = 'confirmada'"));
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("cod_compra", lang("compra"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "compras.codigo", "compras.codigo", WHERE_CRMETHOD, 15);
        $myReporte->setCampo("fecha_comprobante", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "compras_comprobantes.fecha_comprobante", "compras_comprobantes.fecha_comprobante", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("comprobante", lang("comprobante"), true, COMPROBANTES_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.comprobantes.id", "general.comprobantes.nombre", WHERE_CRMETHOD, 26); //VER TIPO
        $myReporte->setCampo("tipo_factura", lang("FACTURA"), true, TIPOS_FACTURAS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.tipos_facturas.codigo", "general.tipos_facturas.factura", WHERE_CRMETHOD, 15);
        $myReporte->setCampo("nro_comprobante", lang("nro_comprobante"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "compras_comprobantes.nro_comprobante", "compras_comprobantes.nro_comprobante", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("punto_venta", lang("punto_venta"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "compras_tipos_factura.punto_venta", "compras_tipos_factura.punto_venta", WHERE_CRMETHOD, 18);
        $myReporte->setCampo("proveedor", lang("proveedor"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "proveedor", HAVING_CRMETHOD, 60);
        $myReporte->SetCampo("documento", lang("id_fiscal"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 35);
        $myReporte->setCampo("direccion", lang("domicilio"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 40);
        $myReporte->setCampo("localidad", lang("localidad"), true, LOCALIDADES_PAIS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.localidades.id", null, WHERE_CRMETHOD, 20);
        $myReporte->setCampo("provincia", lang("Provincia"), true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.provincias.id", null, WHERE_CRMETHOD, 20);
        $myReporte->setCampo("categoria", lang("categoria"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "categoria", HAVING_CRMETHOD, 50);
        $myReporte->setCampo("articulos", lang("articulos"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "articulos", HAVING_CRMETHOD, 50);
        $myReporte->setCampo("total", lang("importe"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, "compras_comprobantes.total", "compras_comprobantes.total", WHERE_CRMETHOD, 20, true, true);
        $myReporte->setCampo("nombre_usuario", lang("usuario"), true, USUARIOS_FILIAL_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.usuarios_sistema.codigo", "nombre_usuario", WHERE_CRMETHOD, 30);
    }

    private function getReporteCtactePendientes(reportes_sistema $myReporte) {
        $extension = lang("_idioma");
        $myReporte->setTable("ctacte");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("nombre_alumno", "asc");
        $nombreApellido = formatearNomApeQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
        $myReporte->setField("(SELECT CONCAT(telefonos.cod_area, ' ', telefonos.numero)
                                    FROM telefonos
                                    INNER JOIN alumnos_telefonos ON alumnos_telefonos.cod_telefono = telefonos.codigo
                                WHERE alumnos_telefonos.cod_alumno = alumnos.codigo AND alumnos_telefonos.`default` = 1
                                ORDER BY telefonos.codigo DESC LIMIT 0, 1) AS telefono");
        $myReporte->setField("IFNULL(DATE_FORMAT(ctacte.fechavenc, '%d/%m/%Y'), ' ') AS fecha_vencimiento");
         $cMatricula = lang("MATRICULA");
        $cMora = lang("MORA");
        $cCurso = lang("VALORCURSO");
        $myReporte->setField("IF (ctacte.cod_concepto = 1, '$cCurso', IF (ctacte.cod_concepto = 5, '$cMatricula', '$cMora')) AS nombre_concepto");
        $myReporte->setField("ctacte.nrocuota");
        $myReporte->setField("(ctacte.importe - ctacte.pagado) AS saldo");
        $myReporte->setField("(SELECT IFNULL(SUM(ctacte_moras.precio), 0) FROM ctacte_moras WHERE ctacte_moras.cod_ctacte = ctacte.codigo) AS mora");
        $myReporte->setField("ctacte.importe - ctacte.pagado + IFNULL((SELECT IFNULL(SUM(ctacte_moras.precio), 0) FROM ctacte_moras WHERE ctacte_moras.cod_ctacte = ctacte.codigo) ,0) AS importe_total");
        $myReporte->setField("(SELECT IF(ctacte.habilitado =  2,'" . lang('deuda_pasiva') . "','" . lang('deuda_activa') . "')) AS deuda_alumno");
        $myReporte->setField("IF (ctacte.cod_concepto = 1 OR ctacte.cod_concepto = 5,
                                    (SELECT general.cursos.nombre_$extension FROM general.cursos
                                    JOIN general.planes_academicos ON general.planes_academicos.cod_curso = general.cursos.codigo
                                    INNER JOIN matriculas ON matriculas.cod_plan_academico = general.planes_academicos.codigo
                                    WHERE matriculas.codigo = ctacte.concepto)
                                    , '') AS nombre_curso");
        //Ticket 4576 - mmori - Se agrega columna "comision" al reporte
        $myReporte->setField("(IFNULL((SELECT  comisiones.nombre
                                FROM `comisiones`
                                JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_comision = comisiones.codigo
                                JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
                                JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo
                                JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula
                                WHERE  ctacte.concepto = matriculas.codigo ORDER BY comisiones.codigo DESC LIMIT 1),'sin comision'))
                                as comision
                                ");
       /* JOIN */
        $myReporte->setJOIN("alumnos", "alumnos.codigo = ctacte.cod_alumno");
        /* CONDICION PERMANENTE */
        $myReporte->setPermaneteWhereLineal(array(
            "ctacte.habilitado in (1,2)",
            "ctacte.importe > ctacte.pagado", "(ctacte.fechavenc IS NULL OR ctacte.fechavenc < CURDATE())")
        );
        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang("cod_alumno"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "alumnos.codigo", "alumnos.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("nombre_alumno", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "nombre_alumno", HAVING_CRMETHOD, 60);
        $myReporte->setCampo("telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "telefono", HAVING_CRMETHOD, 30);
        $myReporte->setCampo("fecha_vencimiento", lang("vencimiento"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "ctacte.fechavenc", "ctacte.fechavenc", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("nombre_concepto", lang("concepto"), true, CONCEPTOS_CTACTE_CRTYPE, array(ES_IGUAL_CRFILTER), false, "ctacte.cod_concepto", "nombre_concepto", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("nrocuota", lang("cuota"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "ctacte.nrocuota", "ctacte.nrocuota", WHERE_CRMETHOD, 15);
        $myReporte->setCampo("saldo", lang("saldo"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "saldo", HAVING_CRMETHOD, 28);
        $myReporte->setCampo("mora", lang("MORA"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "mora", HAVING_CRMETHOD, 28);
        $myReporte->setCampo("importe_total", lang("total"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "importe_total", HAVING_CRMETHOD, 20, true, true);
        $myReporte->setCampo("deuda_alumno", lang('tipo_deuda'), true, TIPO_DEUDA_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, 'deuda_alumno', HAVING_CRMETHOD, 28);
        $myReporte->setCampo("nombre_curso", lang('curso_presu_as'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, 'general.cursos.nombre_es', HAVING_CRMETHOD, 60);
        $myReporte->setCampo("comision", lang('comision'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, 'comision', HAVING_CRMETHOD, 60);
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("deuda_mensual"), "deuda", array("MONTH(ctacte.fechavenc) = MONTH(CURDATE()) AND YEAR(ctacte.fechavenc) = year(CURDATE())"), WHERE_CRMETHOD, lang("deudas_de_este_mes"));
        $myReporte->setFiltrosComunes(lang('deuda_activa'), "deuda_activa", array("deuda_alumno = '" . lang('deuda_activa') . "'"), HAVING_CRMETHOD, lang('deuda_activa'));
        $myReporte->setFiltrosComunes(lang('deuda_pasiva'), "deuda_pasiva", array("deuda_alumno = '" . lang('deuda_pasiva') . "'"), HAVING_CRMETHOD, lang('deuda_pasiva'));
    }

    public function exportarReportes($currentPage, $pageDisplay, $sortName, $sortDir, $reportName, $sSearch, $iFieldView, $applyCommonFilters, $filters) {
        $this->load->helper('alumnos');
        $reporte = $this->getReporte($reportName, true, $currentPage, $pageDisplay, $sortName, $sortDir, $sSearch, $iFieldView, $applyCommonFilters, $filters);
        $arrDatosExportar = array();
        $nombreColumna = '';
        $totalColumas = count($iFieldView);
        $totRegistroColumnas = 0;
        $separador = '; ';
        $nombreColumnaAcumulable = array();
        foreach ($reporte['columns'] as $key => $columna) {
            if ($columna->acumulable == 1) {
                $nombreColumnaAcumulable[$key] = array("total_acumulado" => '');
            }
            if (in_array($key, $iFieldView)) {
                $totRegistroColumnas++;
                if ($totalColumas == $totRegistroColumnas) {
                    $separador = '';
                }
                $nombreColumna .= $columna->display . $separador;
            }
        }
        $arrDatosExportar[] = $nombreColumna;
        $dato = '';
        $sep = '';
        foreach ($reporte['aaData'] as $informe) {
            if (count($nombreColumnaAcumulable) > 0) {
                foreach ($nombreColumnaAcumulable as $key => $valor) {
                    $nombreColumnaAcumulable[$key]['total_acumulado'] = $nombreColumnaAcumulable[$key]['total_acumulado'] + $informe[$key];
                }
            }
            $sep = count($informe);
            foreach ($informe as $col => $valor) {
                if (in_array($col, $iFieldView))
                {
                    //Ticket 053-04549 - mmori - se quitan los enter para que no se desacomoden las colomnas al exportar
                    $str = str_replace("\r\n", "", $valor);
                    $str = str_replace("\n", "", $str);
                    $str = str_replace("\r", "", $str);
                    $column_properties = $reporte['columns'][$col];
                    $tipo = $column_properties->type;
                    if($tipo == 'float'){
                        $str = str_replace(".", ",", $str);
                    }
                    $dato[] = inicialesMayusculas(utf8_decode($str));
                }
            }
            $string = implode(";", $dato);
            $arrDatosExportar[] = $string;
            $dato = '';
        }
        $datosTotFact = '';
        foreach ($iFieldView as $colum) {
            if (array_key_exists($colum, $nombreColumnaAcumulable)) {
                $datosTotFact[] = $nombreColumnaAcumulable[$colum]['total_acumulado'];
            } else {
                $datosTotFact[] = '';
            }
        }
        $totFacturado = implode(";", $datosTotFact);
        $arrDatosExportar[] = $totFacturado;
        return $arrDatosExportar;
    }

    /* esta function esta siendo accedida desde un web services */

    public function getReporteSeguimientoFiliales($idFilial) {
        $conexion = $this->load->database($idFilial, true, null, true);
        $arrResp = array();
        $reporteActivos = Vmatriculas::getReporteSeguimientoFiliales($conexion);
        $cantidadActivos = 0;
        if (is_array($reporteActivos) && count($reporteActivos) > 0) {
            foreach ($reporteActivos as $reporte) {
                $cantidadActivos += $reporte['cantidad'];
            }
        }
        $arrResp['asistencias_alumnos_activos_primer_periodo'] = Vasistencias::getReporteSeguimientoFiliales($conexion);
        $arrResp['alumnos_activos_primer_periodo'] = $cantidadActivos;
        $arrResp['pago_cuotas_ultimo_mes'] = Vctacte::getReporteSeguimientoFiliales($conexion);
        return $arrResp;
    }

    public function getReporteAsistencia(reportes_sistema $myReporte) {
        $extension = lang("_idioma");
        $myReporte->setTable("matriculas_horarios");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("alumno_nombre", "asc");

        /* CAMPOS CONSULTA */
        /*$myReporte->setField("(SELECT alumnos.codigo
                                    FROM alumnos
                                    JOIN matriculas ON matriculas.cod_alumno = alumnos.codigo
                                    JOIN matriculas_periodos ON matriculas_periodos.cod_matricula = matriculas.codigo
                                    JOIN estadoacademico ON estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo
                                    WHERE estadoacademico.codigo = matriculas_horarios.cod_estado_academico) AS cod_alumno", false);
        $myReporte->setField("(SELECT CONCAT(alumnos.apellido, ', ', alumnos.nombre)
                                    FROM alumnos
                                    JOIN matriculas ON matriculas.cod_alumno = alumnos.codigo
                                    JOIN matriculas_periodos ON matriculas_periodos.cod_matricula = matriculas.codigo
                                    JOIN estadoacademico ON estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo
                                    WHERE estadoacademico.codigo = matriculas_horarios.cod_estado_academico) AS alumno_nombre ", false);
        $myReporte->setField("(SELECT CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento)
                                    FROM alumnos
                                    JOIN matriculas ON matriculas.cod_alumno = alumnos.codigo
                                    JOIN matriculas_periodos ON matriculas_periodos.cod_matricula = matriculas.codigo
                                    JOIN estadoacademico ON estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo
                                    JOIN general.documentos_tipos ON alumnos.tipo = general.documentos_tipos.codigo
                                    WHERE estadoacademico.codigo = matriculas_horarios.cod_estado_academico) AS alumno_documento ", false);*/

        $myReporte->setField("alumnos.codigo AS cod_alumno");
        $myReporte->setField("CONCAT(alumnos.apellido, ', ', alumnos.nombre) AS alumno_nombre");
        $myReporte->setField("CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento) AS alumno_documento");
        $myReporte->setField("(SELECT GROUP_CONCAT(IF(telefonos.cod_area = 0,' ',telefonos.cod_area), '', telefonos.numero ) FROM telefonos JOIN alumnos_telefonos on alumnos_telefonos.cod_telefono = telefonos.codigo where alumnos_telefonos.cod_alumno = alumnos.codigo and alumnos_telefonos.`default` = 1) AS alumno_telefono");

        //$myReporte->setField("SELECT DISTINCT  alumnos.codigo AS cod_alumno, CONCAT(alumnos.apellido, ', ', alumnos.nombre) AS alumno_nombre, CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento) AS alumno_documento");

        $myReporte->setField("general.materias.nombre_$extension AS materia_nombre");
        $myReporte->setField("comisiones.codigo AS cod_comision");
        $myReporte->setField("comisiones.ciclo AS ciclo_comision");
        $myReporte->setField("comisiones.nombre AS comision_nombre");
        $myReporte->setField("CONCAT(LPAD(DAY(horarios.dia), 2, 0), '/', LPAD(MONTH(horarios.dia), 2, 0), '/', YEAR(horarios.dia)) AS dia_cursado");
        $myReporte->setField("CONCAT(horarios.horadesde,' / ', horarios.horahasta) AS horas");
        $myReporte->setField("(SELECT salon FROM salones WHERE horarios.cod_salon = salones.codigo) AS salon");
        $myReporte->setField("(SELECT GROUP_CONCAT(profesores.apellido, ', ', profesores.nombre)
                                    FROM profesores
                                    JOIN horarios_profesores ON horarios_profesores.cod_profesor = profesores.codigo
                                    WHERE horarios_profesores.cod_horario = horarios.codigo) AS profesor_nombre ", false);
        $myReporte->setField("IF (matriculas_horarios.estado = 'ausente', '" . lang('ausente') . "',
                                    IF (matriculas_horarios.estado = 'presente', '" . lang('presente') . "',
                                        IF (matriculas_horarios.estado = 'justificado', '" . lang('justificado') . "',
                                            IF (matriculas_horarios.estado = 'media_falta', '" . lang('media_falta') . "',
                                                IF (matriculas_horarios.estado IS NULL, '-',
                                    matriculas_horarios.estado))))) AS asistencia");

        /* JOIN */
        $myReporte->setJOIN("estadoacademico", "estadoacademico.codigo = matriculas_horarios.cod_estado_academico", "left");
        $myReporte->setJOIN("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo", "left");
        $myReporte->setJOIN("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula", "left");
        $myReporte->setJOIN("alumnos", "alumnos.codigo = matriculas.cod_alumno", "left");

        $myReporte->setJOIN("horarios", "horarios.codigo = matriculas_horarios.cod_horario AND matriculas_horarios.estado IS NOT NULL", "left");
        $myReporte->setJOIN("comisiones", "comisiones.codigo = horarios.cod_comision", "left");
        $myReporte->setJOIN("general.materias", "general.materias.codigo = horarios.cod_materia", "left");

        $myReporte->setJOIN("general.documentos_tipos", "alumnos.tipo = general.documentos_tipos.codigo", "left");
        /* CONDICION PERMANENTE */
        $myReporte->setPermaneteWhereLineal(array("matriculas_horarios.baja = 0", "horarios.dia <= CURDATE()"));
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("cod_alumno", lang("cod_alumno"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "cod_alumno", "cod_alumno", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("alumno_nombre", lang("ALUMNO"), true, ALUMNOS_NOMBRES_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, 'alumno_nombre', HAVING_CRMETHOD, 70);
        $myReporte->setCampo("alumno_documento", lang("documento"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("alumno_telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("materia_nombre", lang("materia"), true, MATERIAS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "general.materias.codigo", "general.materias.codigo", WHERE_CRMETHOD, 50);
        $myReporte->setCampo("cod_comision", lang("cod_comision_horario"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "cod_comision", "cod_comision", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("comision_nombre", lang("comision"), true, COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "comisiones.codigo", "comisiones.codigo", WHERE_CRMETHOD, 40);
        $myReporte->setCampo("ciclo_comision", lang("ciclo"), false, INTEGER_CRTYPE, array(LIKE_CRFILTER), true, "ciclo_comision", "ciclo_comision", HAVING_CRMETHOD, 20);
        $myReporte->setCampo("dia_cursado", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "fecha_hora", "STR_TO_DATE(fecha_hora,'%d/%m/%Y')", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("horas", lang("horarios"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "horas", "horas", HAVING_CRMETHOD, 50);
        $myReporte->setCampo("salon", lang("salon_cocina"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "salon", "salon", HAVING_CRMETHOD, 40);
        $myReporte->setCampo("profesor_nombre", lang("profesores"), true, PROFESORES_NOMBRES_CRTYPE, array(LIKE_CRFILTER), true, null, 'profesor_nombre', HAVING_CRMETHOD, 70);
        $myReporte->setCampo("asistencia", lang("asistencia"), true, ASISTENCIA_ALUMNO_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "asistencia", "asistencia", WHERE_CRMETHOD, 20);
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("menos_tres_meses"), "cercana", array("horarios.dia >= DATE_ADD(CURDATE(), INTERVAL -3 MONTH) "), WHERE_CRMETHOD, lang("menos_tres_meses"));
        $myReporte->setFiltrosComunes(lang("sin_asistencia"), "falta_carga_asistencia", array("matriculas_horarios.estado IS NULL"), WHERE_CRMETHOD, lang("sin_asistencia"));

        //$myReporte->setGroup('cod_alumno');
    }

    public function getReporteInscripcionesComisiones(reportes_sistema $myReporte) {

        $extension = lang("_idioma");
        $myReporte->setTable("matriculas_inscripciones");

        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("matriculas_inscripciones.codigo", "desc");
        $nombreApellido = formatearNomApeQuery();

        //Filial de Rosario muestra la nota final
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $nota = false;
        if($filial['codigo'] == 20){
            $nota = true;
        }

        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo AS cod_alumno");
        $myReporte->setField("CONCAT($nombreApellido) AS alumno_nombre ");
        $myReporte->setField("(SELECT general.documentos_tipos.nombre FROM general.documentos_tipos WHERE general.documentos_tipos.codigo = alumnos.tipo) AS alumno_tipo_documento");
        $myReporte->setField("alumnos.documento AS alumno_documento");
        $myReporte->setField('general.localidades.nombre as localidad');
        $myReporte->setField("CONCAT(alumnos.calle,' ', IF(alumnos.calle_numero = 0 or ISNULL(alumnos.calle_numero), '',alumnos.calle_numero), ' ',IF(alumnos.calle_complemento = 0 or ISNULL(alumnos.calle_complemento), '',alumnos.calle_complemento)) AS alumno_domicilio");
        $myReporte->setField("CONCAT(LPAD(DAY(alumnos.fechanaci), 2, 0), '/', LPAD(MONTH(alumnos.fechanaci), 2, 0), '/', YEAR(alumnos.fechanaci)) AS alumno_nacimiento");
        $myReporte->setField("(SELECT GROUP_CONCAT(IF(telefonos.cod_area = 0,' ',telefonos.cod_area), '', telefonos.numero ) FROM telefonos JOIN alumnos_telefonos on alumnos_telefonos.cod_telefono = telefonos.codigo where alumnos_telefonos.cod_alumno = alumnos.codigo and alumnos_telefonos.`default` = 1) AS alumno_telefono");
        $myReporte->setField("general.cursos.nombre_$extension as curso_nombre");
        $myReporte->setField("general.materias.nombre_$extension AS materia_nombre");
        $myReporte->setField("comisiones.codigo AS cod_comision");
        $myReporte->setField("(SELECT general.ciclos.nombre FROM general.ciclos where general.ciclos.codigo = comisiones.ciclo) as ciclo_comision");
        $myReporte->setField("comisiones.nombre AS comision_nombre");
        $myReporte->setField("IFNULL(estadoacademico.porcasistencia,'-') AS porcasistencia");

        $myReporte->setField("alumnos.email");
        $myReporte->setField("matriculas.estado AS estado");

        $myReporte->setField("(SELECT general.talles.talle FROM general.talles WHERE alumnos.id_talle = general.talles.codigo ) as talle_alumno");

        $myReporte->setField("(SELECT GROUP_CONCAT( general.documentacion.nombre_". get_idioma() .") FROM documentacion_alumnos
 JOIN matriculas ON matriculas.codigo = documentacion_alumnos.cod_matricula
 JOIN general.documentacion ON general.documentacion.codigo = documentacion_alumnos.documentacion
 WHERE matriculas.codigo = matriculas_periodos.cod_matricula
GROUP BY documentacion_alumnos.cod_matricula) as documentacion_presentada");

        if($nota)
            $myReporte->setField("(SELECT MAX(nota) FROM notas_resultados, examenes_estado_academico, examenes
                                WHERE notas_resultados.cod_inscripcion = examenes_estado_academico.codigo
                                AND examenes_estado_academico.cod_examen = examenes.codigo
                                AND estadoacademico.codigo = examenes_estado_academico.cod_estado_academico
                                AND tipo_resultado = 'definitivo'
                                AND tipoexamen LIKE '%FINAL%') as nota");

        /* JOIN */

        $myReporte->setJOIN("estadoacademico", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo and estadoacademico.estado NOT IN ('migrado', 'recursa')");
        $myReporte->setJOIN("general.materias", "estadoacademico.codmateria = general.materias.codigo");
        $myReporte->setJOIN("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $myReporte->setJOIN("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $myReporte->setJOIN("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $myReporte->setJOIN("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision AND comisiones.estado = 'habilitado'");
        $myReporte->setJOIN("general.planes_academicos", 'general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $myReporte->setJOIN('general.cursos', 'general.cursos.codigo = general.planes_academicos.cod_curso');
        $myReporte->setJOIN('general.localidades', 'general.localidades.id = alumnos.id_localidad');

        //$myReporte->setJOIN("general.documentacion", "general.documentacion.codigo = documentacion_presentada.documentacion");

        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("cod_alumno", lang("cod_alumno"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "alumnos.codigo", "alumnos.codigo", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("alumno_nombre", $nombreApellido, true, ALUMNOS_NOMBRES_POR_ID_CRTYPE, array(ES_IGUAL_CRFILTER), true, 'cod_alumno', null, WHERE_CRMETHOD, 70);
        $myReporte->setCampo("alumno_tipo_documento", lang("tipo_documento"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("alumno_documento", lang("documento"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("localidad", lang("localidad"), true, LOCALIDADES_PAIS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.localidades.id", null, WHERE_CRMETHOD, 35);
        $myReporte->setCampo("alumno_domicilio", lang("domicilio"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("alumno_nacimiento", lang("fecha_nacimiento"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), true, "alumno_nacimiento", "STR_TO_DATE(alumno_nacimiento,'%d/%m/%Y')", HAVING_CRMETHOD, 15);
        $myReporte->setCampo("alumno_telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo("curso_nombre", lang("curso"), false, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.cursos.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("materia_nombre", lang("materia"), true, MATERIAS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.materias.codigo", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("cod_comision", lang("cod_comision_horario"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "cod_comision", "cod_comision", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("ciclo_comision", lang("ciclo"), false, INTEGER_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 20);
        $myReporte->setCampo("comision_nombre", lang("comision"), true, COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "comisiones.codigo", "comisiones.codigo", WHERE_CRMETHOD, 40);
        $myReporte->setCampo("porcasistencia", lang("porc_asistencia"), false, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), false, "estadoacademico.porcasistencia", "estadoacademico.porcasistencia", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("email", lang("email"), false, STRING_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "email", null, WHERE_CRMETHOD, 50);
        $myReporte->setCampo("estado", lang("estado"), false, MATRIUCLAS_ESTADOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "estado", null, HAVING_CRMETHOD, 50);
        $myReporte->setCampo("talle_alumno", lang("datos_talle"), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);


        $myReporte->setCampo("documentacion_presentada", lang("documentacion_entregada"), false, STRING_CRTYPE, array(LIKE_CRFILTER), false, null, null, HAVING_CRMETHOD, 50);

        $myReporte->setGroup("alumnos.codigo, comisiones.codigo");
        if($nota)
            $myReporte->setCampo("nota", lang("nota"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);

        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("habilitadas"), "habilitadas", array("matriculas_inscripciones.baja = 0"), WHERE_CRMETHOD, lang("habilitadas"));
    }

    public function getReporteBoletosBancarios(reportes_sistema $myReporte) {
        $myReporte->setTable('bancos.boletos_bancarios');
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("bancos.boletos_bancarios.fecha_vencimiento", "desc");
        $nombreTabla = 'bancos.boletos_bancarios';
        /* CAMPOS CONSULTA */
        $myReporte->setField("bancos.boletos_bancarios.codigo");
        $myReporte->setField("bancos.boletos_bancarios.sacado_nombre");
        $myReporte->setField("bancos.boletos_bancarios.sacado_cpf_cnpj");
        $myReporte->setField("bancos.boletos_bancarios.valor_boleto");
        $myReporte->setField("CONCAT(LPAD(DAY($nombreTabla.fecha_vencimiento), 2, 0), '/', "
                . "LPAD(MONTH($nombreTabla.fecha_vencimiento), 2, 0), '/', YEAR($nombreTabla.fecha_vencimiento)) AS fecha_vencimiento");
        $myReporte->setField("bancos.boletos_bancarios.nosso_numero");
        $myReporte->setField("IF (bancos.boletos_bancarios.estado = 'pendiente', '" . lang('pendiente') . "',
                                IF (bancos.boletos_bancarios.estado = 'entrada_confirmada', '" . lang('CONFIRMADA') . "',
                                IF (bancos.boletos_bancarios.estado = 'entrada_rechazada', '" . lang('entrada_rechazada') . "',
                                IF (bancos.boletos_bancarios.estado = 'baja', '" . lang('baja') . "',
                                IF (bancos.boletos_bancarios.estado = 'liquidado', '" . lang('liquidado') . "',
                                IF (bancos.boletos_bancarios.estado = 'baja_solicitada', '" . lang('baja_solicitada') . "',
                                bancos.boletos_bancarios.estado)))))) AS estado");
        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang('codigo'), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, 'bancos.boletos_bancarios.codigo', 'bancos.boletos_bancarios.codigo', WHERE_CRMETHOD, 20);
        $myReporte->setCampo("sacado_nombre", $nombreApellido, true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, 'bancos.boletos_bancarios.sacado_nombre', HAVING_CRMETHOD, 60);
        $myReporte->setCampo("sacado_cpf_cnpj", lang('documento'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 30);
        $myReporte->setCampo('valor_boleto', lang('importe'), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), false, 'bancos.boletos_bancarios.valor_boleto', 'bancos.boletos_bancarios.valor_boleto', WHERE_CRMETHOD, 30);
        $myReporte->setCampo('fecha_vencimiento', lang('fecha_venc'), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, 'bancos.boletos_bancarios.fecha_vencimiento', 'bancos.boletos_bancarios.fecha_vencimiento', WHERE_CRMETHOD, 25);
        $myReporte->setCampo('nosso_numero', lang('nosso_numero'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, null, HAVING_CRMETHOD, 50);
        $myReporte->setCampo('estado', lang('estado'), true, ESTADO_BOLETOS_BANCARIOS, array(ES_IGUAL_CRFILTER), true, null, 'bancos.boletos_bancarios.estado', HAVING_CRMETHOD, 30);
    }

    public function getReporteCuponesGenerados(reportes_sistema $myReporte) {
        $myReporte->setTable('general.cupones');
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("general.cupones.fecha", "desc");
        $myReporte->setPermanentWhere(array("general.cupones.id_filial" => $this->codigofilial));
        /* CAMPOS CONSULTA */
        $myReporte->setField("general.cupones.nombre");
        $myReporte->setField("general.cupones.email");
        $myReporte->setField("general.cupones.telefono");
        $myReporte->setField("general.cupones.documento");
        $myReporte->setField("general.cupones.medio");
        $myReporte->setField("general.cupones.estado");
        $myReporte->setField("DATE_FORMAT(general.cupones.fecha,'%d/%m/%Y') AS fecha");
        $myReporte->setField("general.landing.descuento");
        $myReporte->setField("general.landing.descuento_curso");
        $myReporte->setField("general.cursos.nombre_es AS nombre_curso");
        $myReporte->setField("IF ((SELECT DATE_ADD(general.cupones.fecha, INTERVAL 72 HOUR)) < CURDATE(), 'expirado', 'valido') AS valido");
        /* JOIN */
        $myReporte->setJoin("general.landing", "general.landing.id = general.cupones.id_landing");
        $myReporte->setJoin("general.cursos", "general.cursos.codigo = general.landing.id_curso");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("nombre", lang("nombre"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "general.cupones.nombre", "general.cupones.nombre", WHERE_CRMETHOD, 50, true, false);
        $myReporte->setCampo("email", lang("email"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "general.cupones.email", "general.cupones.email", WHERE_CRMETHOD, 68, true, false);
        $myReporte->setCampo("telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "general.cupones.telefono", "general.cupones.telefono", WHERE_CRMETHOD, 30, true);
        $myReporte->setCampo("documento", lang("documento"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "general.cupones.documento", "general.cupones.documento", WHERE_CRMETHOD, 30, false);
        $myReporte->setCampo("medio", lang("medio"), true, MEDIO_CUPONES_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.cupones.medio", "general.cupones.medio", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("estado", lang("estado"), true, ESTADO_CUPONES_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.cupones.estado", "general.cupones.estado", WHERE_CRMETHOD, 25, true, false);
        $myReporte->setCampo("fecha", lang("fecha"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, 'general.cupones.fecha', 'general.cupones.fecha', WHERE_CRMETHOD, 25, true);
        $myReporte->setCampo("descuento", lang("descuento_matricula"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, 'general.landing.descuento', 'general.landing.descuento', WHERE_CRMETHOD, 20, true);
        $myReporte->setCampo("descuento_curso", lang("descuento_curso"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, 'general.landing.descuento_curso', 'general.landing.descuento_curso', WHERE_CRMETHOD, 20, true);
        $myReporte->setCampo("nombre_curso", lang('curso'), true, CURSOS_CRTYPE, array(LIKE_CRFILTER), false, "general.cursos.codigo", "nombre_curso", WHERE_CRMETHOD, 50, true, false);
        $myReporte->setCampo("valido", lang("validez_cupon"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "valido", "valido", HAVING_CRMETHOD, 28, true, false);
    }

    private function getReporteCtacteFacturaCobro(reportes_sistema $myReporte) {
        $extension = lang("_idioma");
        $myReporte->setTable("ctacte");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("ctacte.fechavenc", "desc");
        $nombreApellido = formatearNomApeQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
        $myReporte->setField("IF(ctacte.cod_concepto = 1,'" . lang('VALORCURSO') . "',
                            IF(ctacte.cod_concepto = 5, '" . lang('MATRICULA') . "',
                             IF(ctacte.cod_concepto = 3, '" . lang('MORA') . "',
                               ,'" . lang('otro') . "'))) AS concepto");
        $myReporte->setField("ctacte.nrocuota");
        $myReporte->setField("IF (ctacte.cod_concepto = 1 OR ctacte.cod_concepto = 5,
                                    (SELECT general.cursos.nombre_$extension FROM general.cursos
                                    JOIN general.planes_academicos ON general.planes_academicos.cod_curso = general.cursos.codigo
                                    INNER JOIN matriculas ON matriculas.cod_plan_academico = general.planes_academicos.codigo
                                    WHERE matriculas.codigo = ctacte.concepto),
                                    IF(ctacte.cod_concepto = 3,CONCAT('" . lang('cuota') . " ',(SELECT cta2.nrocuota FROM ctacte AS cta2 WHERE cta2.codigo = ctacte.concepto),' ',
                                        (SELECT (SELECT general.cursos.nombre_$extension FROM general.cursos
                                    JOIN general.planes_academicos ON general.planes_academicos.cod_curso = general.cursos.codigo
                                    INNER JOIN matriculas ON matriculas.cod_plan_academico = general.planes_academicos.codigo
                                    WHERE matriculas.codigo = cta2.concepto)FROM ctacte AS cta2 WHERE cta2.codigo = ctacte.concepto))
                                    ,(SELECT conceptos.`key` FROM conceptos WHERE conceptos.codigo = ctacte.cod_concepto))) AS descripcion");
        $myReporte->setField("IFNULL(DATE_FORMAT(ctacte.fechavenc, '%d/%m/%Y'),'-') AS fecha_vencimiento");
        $myReporte->setField("facturas.total as importe");
        //
        $myReporte->setField("(SELECT facturas_propiedades.valor FROM facturas_propiedades
        JOIN facturas_renglones ON facturas_renglones.cod_factura = facturas_propiedades.cod_factura AND facturas_propiedades.propiedad = 'numero_factura'
        WHERE facturas_renglones.cod_factura = facturas.codigo
        GROUP BY facturas.codigo)
        as nro_factura");
        //

        //
        $myReporte->setField("IFNULL(DATE_FORMAT(facturas.fecha, '%d/%m/%Y'),'-') as fecha_factura");
        //

        $myReporte->setField("ROUND(IFNULL((SELECT SUM(facturas_renglones.importe) FROM facturas_renglones WHERE anulada = 0 AND facturas_renglones.cod_factura = facturas.codigo),0) * 100 / ctacte.importe,2) AS porc_facturado");
        $myReporte->setField("ROUND(IFNULL((SELECT SUM(cobros.importe) FROM cobros WHERE estado = 'confirmado' AND facturas_cobros.cod_cobro = cobros.codigo),0) * 100 / facturas.total,2) AS porc_cobrado");

        /* JOIN */
        $myReporte->setJOIN("facturas_renglones", "facturas_renglones.cod_ctacte = ctacte.codigo");
        $myReporte->setJOIN("facturas", "facturas.codigo = facturas_renglones.cod_factura");
        $myReporte->setJOIN("facturas_cobros", "facturas.codigo = facturas_cobros.cod_factura", 'left');
        $myReporte->setJOIN("cobros", "cobros.codigo = facturas_cobros.cod_cobro", 'left');

        $myReporte->setJOIN("alumnos", "alumnos.codigo = ctacte.cod_alumno");
        /* CONDICION PERMANENTE */
        $myReporte->setPermaneteWhereLineal(array(
            "ctacte.habilitado in (1,2)")
        );
        $myReporte->setPermaneteHavingLineal(array(
            "(porc_facturado > 0 OR porc_cobrado >0)")
        );
        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang("cod_alumno"), false, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "alumnos.codigo", "alumnos.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("nombre_alumno", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "nombre_alumno", HAVING_CRMETHOD, 60);
        $myReporte->setCampo("concepto", lang('concepto'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, 'ctacte.cod_concepto', HAVING_CRMETHOD, 60);
        $myReporte->setCampo("nrocuota", lang("cuota"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "ctacte.nrocuota", "ctacte.nrocuota", WHERE_CRMETHOD, 15);
        $myReporte->setCampo("descripcion", lang('descripcion'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, 'descripcion', HAVING_CRMETHOD, 60);
        $myReporte->setCampo("fecha_vencimiento", lang("vencimiento"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "ctacte.fechavenc", "ctacte.fechavenc", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("importe", lang("importe"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "importe", HAVING_CRMETHOD, 28, true, true);
        //
        $myReporte->setCampo("nro_factura", lang("nro_factura"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, 'nro_factura', HAVING_CRMETHOD, 15);
        $myReporte->setCampo("fecha_factura", lang("fecha_factura"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, 'facturas.fecha', 'facturas.fecha', WHERE_CRMETHOD, 30);
        //
        $myReporte->setCampo("porc_facturado", lang("porcentaje_facturado"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "porc_facturado", HAVING_CRMETHOD, 28, true, false);
        $myReporte->setCampo("porc_cobrado", lang("porcentaje_cobrado"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "porc_cobrado", HAVING_CRMETHOD, 28, true, false);

    }

    public function getReporteAlumnosPorCurso(reportes_sistema $myReporte){
        $extension = lang("_idioma");
        $myReporte->setTable("general.planes_academicos");
        $myReporte->setLimit(10, 0);
        $primero = lang("1_PERIODO");
        $segundo = lang("2_PERIODO");
        $tercero = lang("3_PERIODO");
        $myReporte->setField("general.cursos.nombre_$extension AS nombre_curso");
        $myReporte->setField("IF (general.tipos_periodos.nombre = '1_PERIODO', '$primero',
                                IF (general.tipos_periodos.nombre = '2_PERIODO', '$segundo', '$tercero')) AS periodo");
        $myReporte->setField("(SELECT COUNT(matriculas_periodos.codigo)
                                    FROM matriculas
                                    INNER JOIN matriculas_periodos ON matriculas_periodos.cod_matricula = matriculas.codigo
                                    WHERE matriculas.cod_plan_academico = general.planes_academicos.codigo
                                        AND matriculas_periodos.estado = 'habilitada'
                                        AND matriculas_periodos.cod_tipo_periodo = general.planes_academicos_periodos.cod_tipo_periodo) AS cantidad");

        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $myReporte->setJOIN("general.planes_academicos_periodos", "general.planes_academicos_periodos.cod_plan_academico = general.planes_academicos.codigo");
        $myReporte->setJOIN("general.tipos_periodos", "general.tipos_periodos.codigo = general.planes_academicos_periodos.cod_tipo_periodo");
        $myReporte->setCampo("nombre_curso", lang("curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "general.planes_academicos.cod_curso", "nombre_curso", WHERE_CRMETHOD, 110, true, false);
        $myReporte->setCampo("periodo", lang("periodo"), true, PERIODOS_CRTYPE, array(ES_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), true, "general.planes_academicos_periodos.cod_tipo_periodo", "periodo", WHERE_CRMETHOD, 50, true, false);
        $myReporte->setCampo("cantidad", lang("inscriptos"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), false, "cantidad", "cantidad", HAVING_CRMETHOD, 30, true, false);

        $myReporte->setPermanentWhere(array("general.planes_academicos.estado" => "habilitado"));
    }

    public function getReporteInscriptosPorMaterias(reportes_sistema $myReporte){
        $extension = lang("_idioma");
        $myReporte->setTable("estadoacademico");
        $myReporte->setLimit(10, 0);
        /* CAMPOS CONSULTA */
        $myReporte->setField("general.materias.nombre_$extension AS nombre_materia");
        $myReporte->setField("COUNT(estadoacademico.codmateria) AS cantidad");
        /* JOIN */
        $myReporte->setJOIN("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $myReporte->setJOIN("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND matriculas_periodos.estado = 'habilitada'");
        $myReporte->setJOIN("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado = 'habilitada'");
        /* CONDICION PERMANENTE */
        $myReporte->setPermanentWhere("estadoacademico.estado = '".Vestadoacademico::getEstadoCursando()."'");
        /* GRUPO */
        $myReporte->setGroup("estadoacademico.codmateria");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("nombre_materia", lang("materia"), true, MATERIAS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.materias.codigo", 'nombre_materia', WHERE_CRMETHOD, 80, true, false);
        $myReporte->setCampo("cantidad", lang("inscriptos"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER), true, "cantidad", "cantidad", HAVING_CRMETHOD, 30, true, false);
    }

    public function getReporteHorarios(reportes_sistema $myReporte){
        $lunes = lang("dia_lunes");
        $martes = lang("dia_martes");
        $miercoles = lang("dia_miercoles");
        $jueves = lang("dia_jueves");
        $viernes = lang("dia_viernes");
        $sabado = lang("dia_sabado");
        $domingo = lang("dia_domingo");
        $extension = lang("_idioma");
        $myReporte->setTable("horarios");
        $myReporte->setLimit(10, 0);
        /* CAMPOS CONSULTA */
        $myReporte->setField("horarios.cod_comision");
        $myReporte->setField("general.cursos.nombre_$extension AS nombre_curso");
        $myReporte->setField("comisiones.nombre AS nombre_comision");
        $myReporte->setField("general.materias.nombre_$extension AS nombre_materia");
        $myReporte->setField("salones.salon");
        $myReporte->setField("ELT(WEEKDAY(horarios.dia) + 1, '$lunes', '$martes', '$miercoles', '$jueves', '$viernes', '$sabado', '$domingo') AS fecha");
        $myReporte->setField("DATE_FORMAT(horarios.horadesde, '%H:%m') AS hora_desde");
        $myReporte->setField("DATE_FORMAT(horarios.horahasta, '%H:%m') AS hora_hasta");
        /* JOIN */
        $myReporte->setJOIN("comisiones", "comisiones.codigo = horarios.cod_comision");
        $myReporte->setJOIN("general.planes_academicos", "general.planes_academicos.codigo = comisiones.cod_plan_academico");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $myReporte->setJOIN("general.materias", "general.materias.codigo = horarios.cod_materia");
        $myReporte->setJOIN("salones", "salones.codigo = horarios.cod_salon");
        /* CONDICION PERMANENTE */
        $myReporte->setPermanentWhere("horarios.baja = 0");
        $myReporte->setGroup("cod_comision, fecha, hora_desde");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("cod_comision", lang("codigo_comision"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "horarios.cod_comision", "horarios.cod_comision", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("nombre_curso", lang("curso"), true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.cursos.codigo", "nombre_curso", WHERE_CRMETHOD, 60, true, false);
        $myReporte->setCampo("nombre_comision", lang("comision"), true, COMISIONES_CRTYPE, array(ES_IGUAL_CRFILTER), false, "horarios.cod_comision", "nombre_comision", WHERE_CRMETHOD, 30, true, false);
        $myReporte->setCampo("nombre_materia", lang("materia"), true, MATERIAS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "horarios.cod_materia", "nombre_materia", WHERE_CRMETHOD, 30, true, false);
        $myReporte->setCampo("salon", lang("salon"), true, SALONES_CRTYPE, array(ES_IGUAL_CRFILTER), false, "horarios.cod_salon", "salones.salon", WHERE_CRMETHOD, 40, true, false);
        $myReporte->setCampo("fecha", lang("dia"), true, DIA_DE_SEMANA_CRTYPE, array(ES_IGUAL_CRFILTER), true, "fecha", "fecha", HAVING_CRMETHOD, 30, TRUE, false);
        $myReporte->setCampo("hora_desde", lang("hora_desde"), true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER), true, "hora_desde", "horarios.horadesde", HAVING_CRMETHOD, 30, true, false);
        $myReporte->setCampo("hora_hasta", lang("hora_hasta"), true, STRING_CRTYPE, array(ES_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, MAYOR_IGUAL_CRFILTER), true, "hora_hasta", "horarios.horahasta", HAVING_CRMETHOD, 30, true, false);
    }

    public function listarDeudasPorAlumnosDataTable($arrFiltros, $filial)
    {
        $conexion = $this->load->database($filial['codigo'], true, null, true);
        $arrCondindiciones = array();

        $this->load->helper('alumnos');

        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "cod_alumno" => $arrFiltros["sSearch"],
                "cod_matricula" => $arrFiltros["sSearch"],
                "nombre" => $arrFiltros["sSearch"],
                "documento" => $arrFiltros["sSearch"],
                "fechapago" => $arrFiltros["sSearch"],
                "curso" => $arrFiltros["sSearch"],
                "comision" => $arrFiltros["sSearch"],
                "tipo_de_deuda" => $arrFiltros["sSearch"],
                "cant_cuotas_debe" => $arrFiltros["sSearch"],
            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Valumnos::listarDeudaAlumnosDataTable($conexion, $arrFiltros, $arrCondindiciones, $arrLimit, $arrSort, false);
        $contar = Valumnos::listarDeudaAlumnosDataTable($conexion, $arrFiltros, $arrCondindiciones, null, null, true);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar['filas'],
            "iTotalDisplayRecords" => $contar['filas'],
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row){
            $rows[] = array(
                $row['cod_alumno'],
                $row['cod_matricula'],
                $row['nombre'],
                $row['documento'],
                $row['fechapago'],
                $row['total'],
                $row['curso'],
                $row['comision'],
                $row['tipo_de_deuda'],
                $row['cant_cuotas_debe']
            );
        }
        $retorno['aaData'] = $rows;
        $retorno['totalAcumulado'] = $contar['totalAcumulado'];
        $retorno['cursos'] = Vcursos::listarCursos($conexion);
        $orden = array(array('campo'=>'fecha_creacion', 'orden'=>'desc'));
        $retorno['comisiones'] = Vcomisiones::listarComisiones($conexion, null, null, $orden);
        return $retorno;
    }

    public function getMotivosBaja($id) {
        $conexion = $this->load->database($this->codigofilial, true);
        $matestado = new Vmatriculas_estado_historicos($conexion);
        $motivo = $matestado->getmotivos(false, true, $id);
        return $motivo;
    }

    public function getReporteBajas($clausulaFechas, $fechaDesde, $fechaHasta, $codCurso = null, $cod_plan_academico = null, $codigo_alumno = null, $titulo = null, $cod_tipo_periodo = null, $cod_mat_periodo = null, $nombreApellido = '', $arrFiltros = null)
    {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');

        //die(var_dump($arrFiltros["sSearch"] ));

        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "codigo" => $arrFiltros["sSearch"],
                "cod_alumno" => $arrFiltros["sSearch"],
                "nombre_apellido" => $arrFiltros["sSearch"],
                "fecha_hora" => $arrFiltros["sSearch"],
                "nombre_curso" => $arrFiltros["sSearch"]

            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }

        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];

        $contar = Vmatriculas::getReporteBajas($conexion, $clausulaFechas, $separador, $fechaDesde, $fechaHasta, $codCurso, $cod_plan_academico, $codigo_alumno, $titulo, $cod_tipo_periodo, $cod_mat_periodo, $nombreApellido, true, $arrFiltros, $arrCondindiciones, null, null);
        $arrReporte = Vmatriculas::getReporteBajas($conexion, $clausulaFechas, $separador, $fechaDesde, $fechaHasta, $codCurso, $cod_plan_academico, $codigo_alumno, $titulo, $cod_tipo_periodo, $cod_mat_periodo, $nombreApellido, false, $arrFiltros, $arrCondindiciones, $arrLimit, $arrSort);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
        foreach ($arrReporte as $row){
            if($row['motivo_id']){
                $motivo = $this->getMotivosBaja($row['motivo_id']);
                $row['motivo'] = lang($motivo);
            } else {
                $row['motivo'] = lang('sin_motivo');
            }

            $rows[] = array(
                $row['cod_alumno'],
                $row['codigo'],
                $row['nombre_apellido'],
                formatearFecha_pais($row['fecha_hora']),
                $row['nombre_curso'],
                $row['comentario'],
                $row['motivo']
                );
        }
        $retorno['aaData'] = $rows;

        $retorno['cursos'] = Vcursos::listarCursos($conexion);

        return $retorno;
    }

    public function getReporteInscripcionesYBajas($fechaDesde = null, $fechaHasta = null){
        $conexion = $this->load->database($this->codigofilial, true);
        $arrReporte = Vmatriculas::getReporteInscripcionesYBajas($conexion, $fechaDesde, $fechaHasta);
        $arrCursosCortos = array();
        $arrCocineritos = array();
        $arrCarreras = array();
        $arrSeminarios = array();
        foreach ($arrReporte as $reporte){
            $temp = array("inscriptos" => $reporte['cantidad_inscriptos'],
                            "bajas" => $reporte['cantidad_bajas'],
                            "titulo" => $reporte['nombre_es'],
                            "cod_curso"=> $reporte['cod_curso'],
                            "cod_plan_academico"=>$reporte['codigo'],
                            "plan"=>$reporte['plan']

                );
            $codCurso = $reporte['cod_curso'];
            if ($codCurso == 17 || $codCurso == 18 || $codCurso == 19){
                $arrCocineritos[] = $temp;
            } else if ($reporte['tipo_curso'] == "curso_corto"){
                $arrCursosCortos[] = $temp;
            } else if ($reporte['tipo_curso'] == 'seminario'){
                $arrSeminarios[] = $temp;
            } else {
                $arrCarreras[] = $temp;
            }
        }

        $arrResp = array(
                "cocineritos" => $arrCocineritos,
                "cursos_cortos" => $arrCursosCortos,
                "carreras" => $arrCarreras,
                "seminarios" => $arrSeminarios
            );
        return $arrResp;
    }

    public function getReporteAlumnosActivos($fecha = null, $mes = null, $anio = null){
        $conexion = $this->load->database("general", true);
        $arrReporte = reporte_alumnos_activos::getReporte($conexion, $this->codigofilial, $fecha, $mes, $anio);
        $arrCursosCortos = array();
        $arrCocineritos = array();
        $arrCarreras = array();
        $arrSeminarios = array();
        foreach ($arrReporte as $reporte){
            $idPeriodo = $reporte['id_tipo_periodo'];
            $myPlanAcademico = new Vplanes_academicos($conexion, $reporte['id_plan_academico']);
            $arrTitulos = $myPlanAcademico->getTitulos($this->codigofilial, $idPeriodo);
            $titulo = isset($arrTitulos[0]) && isset($arrTitulos[0]['nombre']) ? $arrTitulos[0]['nombre'] : '';
            $temp = array("cantidad" => $reporte['cantidad'],
                          "titulo" => $titulo);
            $myCurso = new Vcursos($conexion, $myPlanAcademico->cod_curso);
            $codCurso = $myCurso->getCodigo();
            if ($codCurso == 17 || $codCurso == 18 || $codCurso == 19){
                $arrCocineritos[] = $temp;
            } else if ($myCurso->tipo_curso == 'curso_corto'){
                $arrCursosCortos[] = $temp;
            } else if ($myCurso->tipo_curso == 'seminario'){
                $arrSeminarios[] = $temp;
            } else {
                $arrCarreras[] = $temp;
            }
        }
        $arrResp = array(
            "cocineritos" => $arrCocineritos,
            "cursos_cortos" => $arrCursosCortos,
            "seminarios" => $arrSeminarios,
            "carreras" => $arrCarreras
        );
        return $arrResp;
    }


    public function getPlanesConCuposDisponibles(){

        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->select("cod_plan_academico AS id");
        $conexion->select("
                         (SELECT general.cursos.tipo_curso
                         FROM general.planes_academicos
                         INNER JOIN general.cursos
                         ON general.planes_academicos.cod_curso = general.cursos.codigo
                         WHERE general.planes_academicos.codigo = cod_plan_academico)
                         AS tipo_curso
                          ", false);
        $conexion->select("(SELECT `nombre_".get_idioma()."` from general.cursos
                            INNER JOIN general.planes_academicos
                                ON general.cursos.codigo = general.planes_academicos.cod_curso
                            WHERE general.planes_academicos.codigo = cod_plan_academico) AS nombre");
        $conexion->from("comisiones");
        $conexion->join("(SELECT MIN(dia) as inicio, cod_comision as cod FROM horarios group by cod_comision) as subquerysub", "comisiones.codigo = cod AND inicio > DATE_SUB(NOW(),INTERVAL 20 DAY)");
        $conexion->where("comisiones.estado = 'habilitado'");
        $conexion->group_by("id");
        $conexion->order_by("tipo_curso");
        $conexion->order_by("id");
        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado;

    }

    public function getReporteCuposDisponibles($idCurso = null, $idComision = null, $codigoFilial = null, $idsCursos = null){
        /*Hay que cerrar el ticket, no hay tiempo para hacerlo lindo.*/
        $filial = $codigoFilial != null? $codigoFilial : $this->codigofilial;
        $conexion = $this->load->database($filial, true);
        $conexion->select("comisiones.nombre AS comision");
        $conexion->select("(SELECT general.cursos.`codigo` from general.cursos
                            INNER JOIN general.planes_academicos
                                ON general.cursos.codigo = general.planes_academicos.cod_curso
                            WHERE general.planes_academicos.codigo = cod_plan_academico) AS codigo_curso");
        $conexion->select("(SELECT `nombre_".get_idioma()."` from general.cursos
                            INNER JOIN general.planes_academicos
                                ON general.cursos.codigo = general.planes_academicos.cod_curso
                            WHERE general.planes_academicos.codigo = cod_plan_academico) AS nombre_curso");
        $conexion->select("(SELECT MIN(salones.cupo) FROM horarios
                                    INNER JOIN salones ON salones.codigo = horarios.cod_salon
                                        AND salones.estado = 0
                                    WHERE horarios.baja = 0
                                    AND horarios.cod_comision = comisiones.codigo) as cupo");
        $conexion->select("(SELECT COUNT(DISTINCT matriculas_periodos.codigo) as inscriptos
                            FROM matriculas_inscripciones
                            INNER JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
                            INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo
                            AND matriculas_periodos.estado = 'habilitada'
                            WHERE matriculas_inscripciones.baja = 0 AND matriculas_inscripciones.cod_comision = comisiones.codigo
                            ) as inscriptos");
        $conexion->select("CASE
                           WHEN (
                                    SELECT MIN(dia) as fecha_inicio
                                    FROM horarios
                                    WHERE comisiones.codigo = horarios.cod_comision group by cod_comision
                                ) < NOW()
                           THEN 'SI'
                           ELSE 'NO'
                           END AS iniciado", false);
        $conexion->select("DATE_FORMAT(DATE_ADD((SELECT MIN(dia) as fecha_inicio
                           from horarios
                           WHERE comisiones.codigo = horarios.cod_comision
                           group by cod_comision), INTERVAL 20 DAY), '%e/%c/%Y') as fecha_cierre", false);
        $conexion->select("(SELECT paises.id FROM general.paises INNER JOIN general.filiales ON ".
                          " general.filiales.codigo = $filial AND general.paises.id = general.filiales.pais".
                          ") as cod_pais");
        $conexion->select("(SELECT paises.pais FROM general.paises INNER JOIN general.filiales ON ".
                          " general.filiales.codigo = $filial AND general.paises.id = general.filiales.pais".
                          ") as nombre_pais");
        $conexion->select("
              (SELECT general.cursos.tipo_curso
                         FROM general.planes_academicos
                         INNER JOIN general.cursos
                         ON general.planes_academicos.cod_curso = general.cursos.codigo
                         WHERE general.planes_academicos.codigo = cod_plan_academico)
                         AS tipo_curso
                          ", false);
        //Se creía necesario para el agrupamiento arbitrario y no jerárquico del listado de cursos del panel de control.
        // Por ahora se mantiene.
        $conexion->select("
                        (CASE (SELECT general.cursos.`tipo_curso` from general.cursos
                         INNER JOIN general.planes_academicos
                         ON general.cursos.codigo = general.planes_academicos.cod_curso
                         WHERE general.planes_academicos.codigo = cod_plan_academico)
                         WHEN 'curso' THEN ''
                         ELSE 'Cursos cortos' END) AS falso_nombre");
        $conexion->select($filial." as cod_filial", false);
        $conexion->select("(SELECT general.filiales.nombre FROM general.filiales where codigo = $filial) as nombre_filial");
        $conexion->from("comisiones");
        $conexion->join("(SELECT MIN(dia) as inicio, cod_comision as cod FROM horarios group by cod_comision) as subquerysub", "comisiones.codigo = cod AND inicio > DATE_SUB(NOW(),INTERVAL 20 DAY)");

        $conexion->where("estado = 'habilitado'");
        if($idCurso != null && $idCurso != ''){
            $conexion->where("cod_plan_academico = ".$idCurso);
        }
        if($idsCursos !=null){
            //TODO
            //$conexion->where("'general'.'planes_academicos'.codigo in (".join(",",$idsCursos).")");
        }
        $conexion->order_by("cod_pais");
        $conexion->order_by("cod_filial");
        $conexion->order_by("tipo_curso");
        $conexion->order_by("codigo_curso");
        $conexion->order_by("comision");
        $query = $conexion->get();
        $resultado = $query->result_array();
        $ret = array();
        foreach($resultado as $comision){
            $linea = $comision;
            $cupo = intval($linea['cupo']) - intval($linea['inscriptos']);
            if($cupo <= 0)
                continue;
            $linea['cupo'] = "" + $cupo;
            $ret[] = $linea;
        }


        return $ret;
    }

    public function reporte_cupos_panelControl($idCursos, $filiales){
        $ret = array();
        if($filiales == null){
            $conexion = $this->load->database("general", true);
            $conexion->select("filiales.codigo as codigo");
            $conexion->from("filiales");
            $conexion->where("filiales.estado = 'activa'");
            $conexion->where("filiales.baja = 0");
            $conexion->order_by("filiales.pais");
            $query = $conexion->get();
            $filiales = $query->result_array();
        }

        foreach($filiales as $filial){
           if(isset($filial['codigo'])){
               $cupos =  $this->getReporteCuposDisponibles(null, null, $filial['codigo'], $idCursos);
           } else {
               $cupos =  $this->getReporteCuposDisponibles(null, null, $filial, $idCursos);
           }
           $ret = array_merge($ret, $cupos);

        }

        return $ret;


    }

    public function reporte_alumnos_activos_por_comision(reportes_sistema $myReporte){
        $codFilial = $this->codigofilial;
        $myReporte->setTable("comisiones");
        $myReporte->setLimit(10, 0);
        /* CAMPOS CONSULTA */
        $sqInscriptos = "SELECT count(DISTINCT matriculas_periodos.codigo) FROM matriculas_inscripciones ".
                "INNER JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico ".
                "INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo ".
                    "AND matriculas_periodos.estado = 'habilitada' ".
                "WHERE matriculas_inscripciones.cod_comision = comisiones.codigo AND matriculas_inscripciones.baja = 0";
        $sqCupo = "SELECT MIN(salones.cupo) FROM horarios ".
                "INNER JOIN salones ON salones.codigo = horarios.cod_salon ".
                    "AND salones.estado = 0 ".
                "WHERE horarios.baja = 0 AND horarios.cod_comision = comisiones.codigo";
        $myReporte->setField("titulos.nombre AS nombre_titulo");
        $myReporte->setField("ciclos.nombre AS nombre_ciclo");
        $normal = lang('modalidad_normal');
        $intensivo = lang('modalidad_intensiva');
        $myReporte->setField("IF(comisiones.modalidad = 'normal', '$normal', '$intensivo') AS modalidad ");
        $myReporte->setField("comisiones.nombre");
        $myReporte->setField("comisiones.codigo");
        $myReporte->setField("($sqInscriptos) AS inscriptos");
        $myReporte->setField("($sqCupo) AS cupo");
        $myReporte->setField("IF(($sqCupo) -($sqInscriptos) < 0, 0, ($sqCupo) -($sqInscriptos)) AS vacantes");
        /* JOIN */
        $myReporte->setJOIN("general.planes_academicos_filiales", "general.planes_academicos_filiales.cod_plan_academico = comisiones.cod_plan_academico ".
                            "AND general.planes_academicos_filiales.cod_tipo_periodo = comisiones.cod_tipo_periodo ".
                            "AND general.planes_academicos_filiales.cod_filial = $codFilial ".
                            "AND general.planes_academicos_filiales.modalidad = comisiones.modalidad");
        $myReporte->setJOIN("general.titulos", "general.titulos.codigo = general.planes_academicos_filiales.cod_titulo");
        $myReporte->setJOIN("general.ciclos", "general.ciclos.codigo = comisiones.ciclo");
        /* CAMPOS MOSTRAR */
        $myReporte->setCampo("nombre_titulo", lang("curso"), true, TITULOS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.titulos.codigo", "nombre_titulo", WHERE_CRMETHOD, 60, true, false);
        $myReporte->setCampo("nombre_ciclo", lang("ciclo"), true, CICLOS_HABILITADOS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.ciclos.codigo", "nombre_ciclo", WHERE_CRMETHOD, 14, true, false);
        $myReporte->setCampo("modalidad", lang("modalidad"), false, MODALIDAD_CRTYPE, array(ES_IGUAL_CRFILTER), false, "comisiones.modalidad", "modalidad", WHERE_CRMETHOD, 20, true, false);
        $myReporte->setCampo("nombre", lang("nombre"), true, STRING_CRTYPE, array(LIKE_CRFILTER), false, "comisiones.nombre", "comisiones.nombre", WHERE_CRMETHOD, 52, true, false);
        $myReporte->setCampo("codigo", lang("codigo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, MENOR_IGUAL_CRFILTER), false, "comisiones.codigo", "comisiones.codigo", WHERE_CRMETHOD, 16, true, false);
        $myReporte->setCampo("inscriptos", lang("inscriptos"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, MENOR_IGUAL_CRFILTER), true, "inscriptos", "inscriptos", HAVING_CRMETHOD, 20, TRUE, true);
        $myReporte->setCampo("cupo", lang("cupo"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, MENOR_IGUAL_CRFILTER), true, "cupo", "cupo", HAVING_CRMETHOD, 20, true, true);
        $myReporte->setCampo("vacantes", lang("vacantes"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, MENOR_IGUAL_CRFILTER), true, "vacantes", "vacantes", HAVING_CRMETHOD, 20, true, true);

        //$myReporte->setCampo("inscriptos_otra_com");
        /* CONDICION PERMANENTE */
        $myReporte->setPermanentWhere("general.ciclos.fecha_fin_ciclo >= CURDATE()");
        $myReporte->setPermanentWhere("comisiones.estado = 'habilitado'");
    }

    public function getReporteAlumnosCertificados(reportes_sistema $myReporte){
        $codFilial = $this->codigofilial;
        $myReporte->setTable("matriculas");
        $myReporte->setLimit(10, 0);    
        $ctacteDebe = lang("debe_ctacte");
        $ctacteNoDebe = lang("no_debe_ctacte");
        $matriculaHabilitada = lang("habilitada");
        $matriculaInhabilitada = lang("inhabilitada");
        $matriculaFinalizada = lang("finalizada");
        $matriculaCertificada = lang("certificada");
        $certificadoFinalizado = lang("finalizado");
        $certificadoPendiente = lang("pendiente");
        $certificadoCancelado = lang("cancelado");
        $certificadoEnProceso = lang("en_proceso");
        $certificadoPendienteAprobar = lang("pendiente_aprobar");
        $certificadoPendienteImpresion = lang("pendiente_impresion");
        $sqCurso = "SELECT general.titulos.nombre FROM ".
                    "general.planes_academicos_filiales ".
                    "INNER JOIN general.titulos ON general.titulos.codigo = general.planes_academicos_filiales.cod_titulo ".
                    "WHERE general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico ".
                        "AND  general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo ".
                        "AND general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad ".
                        "AND general.planes_academicos_filiales.cod_filial = $codFilial";
        $sqCursoCodigo = "SELECT general.titulos.codigo FROM ".
            "general.planes_academicos_filiales ".
            "INNER JOIN general.titulos ON general.titulos.codigo = general.planes_academicos_filiales.cod_titulo ".
            "WHERE general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico ".
            "AND  general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo ".
            "AND general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad ".
            "AND general.planes_academicos_filiales.cod_filial = $codFilial";
        $sqMateriasAprobadas = "SELECT COUNT(distinct estadoacademico.codmateria) FROM estadoacademico ".
                    "WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo ".
                        "AND estadoacademico.estado in ('aprobado', 'homologado')";
        $sqTotalMaterias = "SELECT COUNT(cod_materia) FROM general.materias_plan_academico ".
                    "WHERE general.materias_plan_academico.cod_plan = matriculas.cod_plan_academico ".
                        "AND general.materias_plan_academico.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo";
        $sqCiclo = "SELECT general.ciclos.nombre FROM matriculas_inscripciones
                    INNER JOIN estadoacademico on estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
                    INNER JOIN comisiones on comisiones.codigo = matriculas_inscripciones.cod_comision
                    INNER JOIN general.ciclos on general.ciclos.codigo = comisiones.ciclo
                    WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo
                    ORDER BY general.ciclos.fecha_inicio_ciclo DESC LIMIT 0, 1";
        $myReporte->setField("matriculas.codigo");
        $sqCantidadDeuda = "SELECT COUNT(ctacte.codigo) FROM ctacte ".
                    "WHERE ctacte.cod_alumno = alumnos.codigo and ctacte.concepto = matriculas.codigo ".
                        "AND ctacte.habilitado = 1 AND ctacte.importe > pagado AND ctacte.fechavenc > CURDATE()";
        $sqEstadoCertificado = "IF (certificados.estado = 'finalizado', '$certificadoFinalizado', ".
                    "IF (certificados.estado = 'cancelado', '$certificadoCancelado', ".
                    "IF (certificados.estado = 'pendiente', '$certificadoPendiente', ".
                    "IF (certificados.estado = 'pendiente_aprobar', '$certificadoPendienteAprobar', ".
                    "IF (certificados.estado = 'en_proceso', '$certificadoEnProceso', ".
                    "IF (certificados.estado = 'pendiente_impresion', '$certificadoPendienteImpresion', certificados.estado))))))";
        $sqTelefono = "SELECT CONCAT(telefonos.cod_area, ' ', telefonos.numero) FROM telefonos ".
                    "INNER JOIN alumnos_telefonos on alumnos_telefonos.cod_telefono = telefonos.codigo ".
                    "WHERE alumnos_telefonos.cod_alumno = alumnos.codigo ORDER BY alumnos_telefonos.`default` DESC LIMIT 0, 1";

        $sqFechaPedido = "SELECT DATE_FORMAT( certificados_estado_historico.fecha_hora, '%d/%m/%Y' ) FROM certificados_estado_historico 
                    WHERE certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo 
                    AND certificados_estado_historico.cod_certificante = certificados.cod_certificante 
                    AND certificados_estado_historico.estado = 'en_proceso'
                    ORDER BY codigo DESC LIMIT 1";

        $myReporte->setField("matriculas.cod_alumno");
        $myReporte->setField("CONCAT(alumnos.nombre, ' ', alumnos.apellido) AS alumno");
        $myReporte->setField("IF (matriculas.estado = 'inhabilitada', '$matriculaInhabilitada', IF (matriculas.estado = 'habilitada', '$matriculaHabilitada', IF (matriculas.estado = 'certificada', '$matriculaCertificada', IF (matriculas.estado = 'finalizada', '$matriculaFinalizada', matriculas.estado)))) AS estado_matricula");
        $myReporte->setField("($sqCursoCodigo) AS curso");
        $myReporte->setField("($sqCurso) AS curso_titulo");
        $myReporte->setField("($sqMateriasAprobadas) AS materias_aprobadas");
        $myReporte->setField("($sqTotalMaterias) AS total_materias");
        $myReporte->setField("($sqCiclo) AS ciclo");
        $myReporte->setField("IF (($sqCantidadDeuda) > 0, '$ctacteDebe', '$ctacteNoDebe') AS estado_ctacte");
        $myReporte->setField("$sqEstadoCertificado AS estado_certificado");

        $myReporte->setField("($sqFechaPedido) AS fecha_hora");

        $myReporte->setField("alumnos.email");
        $myReporte->setField("($sqTelefono) AS telefono");
        $myReporte->setJOIN("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $myReporte->setJOIN("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $myReporte->setJOIN("certificados", "certificados.cod_matricula_periodo = matriculas_periodos.codigo and certificados.cod_certificante = 1");
        $myReporte->setCampo("codigo", lang("cod_matricula"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "matriculas.codigo", "matriculas.codigo", WHERE_CRMETHOD);
        $myReporte->setCampo("cod_alumno", lang("cod_alumno"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "matriculas.cod_alumno", "matriculas.cod_alumno", WHERE_CRMETHOD);
        $myReporte->setCampo("alumno", lang("Alumno"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "alumno", "alumno", HAVING_CRMETHOD);
        $myReporte->setCampo("estado_matricula", lang("estado_de_matricula"), true, ESTADO_MATRICULAS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "matriculas.estado", "estado_matricula", WHERE_CRMETHOD);
        $myReporte->setCampo("curso", lang("curso"), false, TITULOS_CRTYPE, array(ES_IGUAL_CRFILTER), true, "curso", "curso", HAVING_CRMETHOD);
        $myReporte->setCampo("curso_titulo", lang("curso"), true, null, array(ES_IGUAL_CRFILTER), true, "curso_titulo", "curso_titulo", HAVING_CRMETHOD);
        $myReporte->setCampo("materias_aprobadas", lang("materias_aprobadas"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, MENOR_IGUAL_CRFILTER), true, "materias_aprobadas", "materias_aprobadas", HAVING_CRMETHOD);
        $myReporte->setCampo("total_materias", lang("total_de_materias"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_CRFILTER, MENOR_IGUAL_CRFILTER), true, "total_materias", "total_materias", HAVING_CRMETHOD);
        $myReporte->setCampo("ciclo", lang("ciclo"), true, CICLOS_LECTIVOS_CRTYPE, array(ES_IGUAL_CRFILTER), true, "ciclo", "ciclo", HAVING_CRMETHOD);
        $myReporte->setCampo("estado_ctacte", lang("estado_ctacte"), true, CTACTE_ESTADO_CRTYPE, array(ES_IGUAL_CRFILTER), true, "estado_ctacte", "estado_ctacte", HAVING_CRMETHOD);
        $myReporte->setCampo("estado_certificado", lang("estado_del_certificado"), true, ESTADO_CERTIFICADOS_CRTYPE, array(ES_IGUAL_CRFILTER), true, "certificados.estado", "estado_certificado", WHERE_CRMETHOD);

        $myReporte->setCampo("fecha_hora", lang("fecha_pedido"), true, DATE_CRTYPE, array(ENTRE_CRFILTER), true, "fecha_hora", "STR_TO_DATE(fecha_hora,'%d/%m/%Y')", HAVING_CRMETHOD, 20);

        $myReporte->setCampo("email", lang("email"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "alumnos.email", "alumnos.email", WHERE_CRMETHOD);
        $myReporte->setCampo("telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "telefono", "telefono", HAVING_CRMETHOD);
        $myReporte->setPermanentWhere("matriculas.estado <> 'migrado'");
        $myReporte->setPermanentWhere("matriculas_periodos.estado <> 'migrado'");
    }

    public function get_documentacion_faltante_y_materiales_no_entregados(reportes_sistema $myReporte){
        $myReporte->setTable("matriculas");
        $myReporte->setLimit(10, 0);
        $entregado = lang("entregado");
        $noEntregado = lang("no_entregado");
        $idioma = get_idioma();
        $sqCiclo = "SELECT general.ciclos.nombre FROM general.ciclos ".
                "INNER JOIN comisiones on comisiones.ciclo = general.ciclos.codigo ".
                "INNER JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_comision = comisiones.codigo ".
                "INNER JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico ".
                "INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo ".
                "WHERE matriculas_periodos.cod_matricula = matriculas.codigo LIMIT 0, 1";
        $sqComision = "SELECT comisiones.nombre FROM comisiones ".
                "INNER JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_comision = comisiones.codigo ".
                "INNER JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico ".
                "INNER JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo ".
                "WHERE matriculas_periodos.cod_matricula = matriculas.codigo LIMIT 0, 1";
        $sqTelefono = "SELECT CONCAT(telefonos.cod_area,' ',telefonos.numero) ".
                "FROM telefonos ".
                "INNER JOIN alumnos_telefonos on alumnos_telefonos.cod_telefono = telefonos.codigo ".
                "WHERE alumnos_telefonos.cod_alumno = alumnos.codigo and telefonos.baja = 0 ".
                "ORDER BY alumnos_telefonos.`default` DESC LIMIT 0, 1";
        $sqLibro1 = "SELECT COUNT(materiales_alumnos.cod_matricula) ".
                "FROM materiales_alumnos ".
                "WHERE materiales_alumnos.cod_matricula = matriculas.codigo ".
                "AND materiales_alumnos.id_material = 1";
        $sqLibro2 = "SELECT COUNT(materiales_alumnos.cod_matricula) ".
                "FROM materiales_alumnos ".
                "WHERE materiales_alumnos.cod_matricula = matriculas.codigo ".
                "AND materiales_alumnos.id_material = 2";
        $sqCuadernillo = "SELECT COUNT(materiales_alumnos.cod_matricula) ".
                "FROM materiales_alumnos ".
                "WHERE materiales_alumnos.cod_matricula = matriculas.codigo ".
                "AND materiales_alumnos.id_material = 3";
        $sqUniforme = "SELECT COUNT(materiales_alumnos.cod_matricula) ".
                "FROM materiales_alumnos ".
                "WHERE materiales_alumnos.cod_matricula = matriculas.codigo ".
                "AND materiales_alumnos.id_material = 4";
        $sqFoto = "SELECT COUNT(documentacion_alumnos.cod_matricula) ".
                "FROM documentacion_alumnos ".
                "WHERE documentacion_alumnos.cod_matricula = matriculas.codigo ".
                "AND documentacion_alumnos.documentacion = 1";
        $sqCertificadoSalud = "SELECT COUNT(documentacion_alumnos.cod_matricula) ".
                "FROM documentacion_alumnos ".
                "WHERE documentacion_alumnos.cod_matricula = matriculas.codigo ".
                "AND documentacion_alumnos.documentacion = 2";
        $sqFotocopiaDNI = "SELECT COUNT(documentacion_alumnos.cod_matricula) ".
                "FROM documentacion_alumnos ".
                "WHERE documentacion_alumnos.cod_matricula = matriculas.codigo ".
                "AND documentacion_alumnos.documentacion = 3";
        $myReporte->setField("matriculas.codigo");
        $myReporte->setField("CONCAT(alumnos.nombre, ' ', alumnos.apellido) AS nombre_alumno");
        $myReporte->setField("($sqCiclo) AS ciclo");
        $myReporte->setField("general.cursos.nombre_$idioma AS nombre_curso");
        $myReporte->setField("($sqComision) AS comision");
        $myReporte->setField("($sqTelefono) AS telefono");
        $myReporte->setField("IF (($sqLibro1) > 0, '$entregado', '$noEntregado') AS libro1");
        $myReporte->setField("IF (($sqLibro2) > 0, '$entregado', '$noEntregado') AS libro2");
        $myReporte->setField("IF (($sqCuadernillo) > 0, '$entregado', '$noEntregado') AS cuadernillo");
        $myReporte->setField("IF (($sqUniforme) > 0, '$entregado', '$noEntregado') AS uniforme");
        $myReporte->setField("IF (($sqFoto) > 0, '$entregado', '$noEntregado') AS foto");
        $myReporte->setField("IF (($sqCertificadoSalud) > 0, '$entregado', '$noEntregado') AS certificado_salud");
        $myReporte->setField("IF (($sqFotocopiaDNI) > 0, '$entregado', '$noEntregado') AS fotocopia_dni");
        $myReporte->setJOIN("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $myReporte->setJOIN("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $myReporte->setJOIN("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $myReporte->setCampo("codigo", "codigo", true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "matriculas.codigo", "matriculas.codigo", WHERE_CRMETHOD);
        $myReporte->setCampo("nombre_alumno", "Nombre", true, ALUMNOS_NOMBRES_POR_ID_CRTYPE, array(ES_IGUAL_CRFILTER), true, "alumnos.codigo", "nombre_alumno", WHERE_CRMETHOD);
        $myReporte->setCampo("ciclo", "Ciclo", true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "ciclo", "ciclo", HAVING_CRMETHOD);
        $myReporte->setCampo("nombre_curso", "Curso", true, CURSOS_CRTYPE, array(ES_IGUAL_CRFILTER), false, "general.cursos.codigo", "nombre_curso", WHERE_CRMETHOD);
        $myReporte->setCampo("comision", "Comision", true, COMISIONES_NN_CRTYPE, array(ES_IGUAL_CRFILTER), true, "comision", "comision", HAVING_CRMETHOD);
        $myReporte->setCampo("telefono", "Telefono", true, STRING_CRTYPE, array(LIKE_CRFILTER), true, "telefono", "telefono", HAVING_CRMETHOD);
        $myReporte->setCampo("libro1", lang("libro1"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "libro1", "libro1", HAVING_CRMETHOD);
        $myReporte->setCampo("libro2", lang("libro2"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "libro2", "libro2", HAVING_CRMETHOD);
        $myReporte->setCampo("cuadernillo", lang("cuadernillo"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "cuadernillo", "cuadernillo", HAVING_CRMETHOD);
        $myReporte->setCampo("uniforme", lang("uniforme"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "uniforme", "uniforme", HAVING_CRMETHOD);
        $myReporte->setCampo("foto", lang("foto"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "foto", "foto", HAVING_CRMETHOD);
        $myReporte->setCampo("certificado_salud", lang("certificado_salud"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "certificado_salud", "certificado_salud", HAVING_CRMETHOD);
        $myReporte->setCampo("fotocopia_dni", lang("fotocopia_dni"), true, DOCUMENTACION_ESTADO_CRTPE, array(ES_IGUAL_CRFILTER), true, "fotocopia_dni", "fotocopia_dni", HAVING_CRMETHOD);
        $myReporte->setPermanentWhere("matriculas.estado <> 'migrado'");
    }


    /* Nuevos Reportes */
    public function getCobrosEstimados(reportes_sistema $myReporte) {
        $extension = lang("_idioma");
        $myReporte->setTable("ctacte");
        $myReporte->setLimit(10, 0);
        $myReporte->setOrder("nombre_alumno", "asc");
        $nombreApellido = formatearNomApeQuery();
        /* CAMPOS CONSULTA */
        $myReporte->setField("alumnos.codigo");
        $myReporte->setField("CONCAT($nombreApellido) AS nombre_alumno");
        $myReporte->setField("(SELECT CONCAT(telefonos.cod_area, ' ', telefonos.numero)
                                    FROM telefonos
                                    INNER JOIN alumnos_telefonos ON alumnos_telefonos.cod_telefono = telefonos.codigo
                                WHERE alumnos_telefonos.cod_alumno = alumnos.codigo AND alumnos_telefonos.`default` = 1
                                ORDER BY telefonos.codigo DESC LIMIT 0, 1) AS telefono");
        $myReporte->setField("IFNULL(DATE_FORMAT(ctacte.fechavenc, '%d/%m/%Y'), ' ') AS fecha_vencimiento");
        $cMatricula = lang("MATRICULA");
        $cMora = lang("MORA");
        $cCurso = lang("VALORCURSO");
        $myReporte->setField("IF (ctacte.cod_concepto = 1, '$cCurso', IF (ctacte.cod_concepto = 5, '$cMatricula', '$cMora')) AS nombre_concepto");
        $myReporte->setField("ctacte.nrocuota");
        $myReporte->setField("(ctacte.importe - ctacte.pagado) AS saldo");
        $myReporte->setField("(SELECT IFNULL(SUM(ctacte_moras.precio), 0) FROM ctacte_moras WHERE ctacte_moras.cod_ctacte = ctacte.codigo) AS mora");
        $myReporte->setField("ctacte.importe AS importe_total");
        $myReporte->setField("(SELECT IF(ctacte.habilitado =  2,'" . lang('deuda_pasiva') . "','" . lang('deuda_activa') . "')) AS deuda_alumno");
        $myReporte->setField("IF (ctacte.cod_concepto = 1 OR ctacte.cod_concepto = 5,
                                    (SELECT general.cursos.nombre_$extension FROM general.cursos
                                    JOIN general.planes_academicos ON general.planes_academicos.cod_curso = general.cursos.codigo
                                    INNER JOIN matriculas ON matriculas.cod_plan_academico = general.planes_academicos.codigo
                                    WHERE matriculas.codigo = ctacte.concepto)
                                    , '') AS nombre_curso");
        $myReporte->setField("(IFNULL((SELECT  comisiones.nombre
                                FROM `comisiones`
                                JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_comision = comisiones.codigo
                                JOIN estadoacademico ON estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
                                JOIN matriculas_periodos ON matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo
                                JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula
                                WHERE  ctacte.concepto = matriculas.codigo ORDER BY comisiones.codigo DESC LIMIT 1),'sin comision'))
                                as comision
                                ");
        /* JOIN */
        $myReporte->setJOIN("alumnos", "alumnos.codigo = ctacte.cod_alumno");
        /* CONDICION PERMANENTE */
        $myReporte->setPermaneteWhereLineal(array(
            "ctacte.habilitado in (1)",
            "ctacte.importe > 0",
            "ctacte.importe > ctacte.pagado") );

        /* CAMPOS MOSTRAR */
        $nombreApellido = formatearNombreColumnaAlumno();
        $myReporte->setCampo("codigo", lang("cod_alumno"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "alumnos.codigo", "alumnos.codigo", WHERE_CRMETHOD, 20);
        $myReporte->setCampo("nombre_alumno", $nombreApellido, true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "nombre_alumno", HAVING_CRMETHOD, 60);
        $myReporte->setCampo("telefono", lang("telefono"), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, "telefono", HAVING_CRMETHOD, 30);
        $myReporte->setCampo("fecha_vencimiento", lang("vencimiento"), true, DATE_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, ENTRE_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, NO_ES_IGUAL_CRFILTER), false, "ctacte.fechavenc", "ctacte.fechavenc", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("nombre_concepto", lang("concepto"), true, CONCEPTOS_CTACTE_CRTYPE, array(ES_IGUAL_CRFILTER), false, "ctacte.cod_concepto", "nombre_concepto", WHERE_CRMETHOD, 30);
        $myReporte->setCampo("nrocuota", lang("cuota"), true, INTEGER_CRTYPE, array(ES_IGUAL_CRFILTER), false, "ctacte.nrocuota", "ctacte.nrocuota", WHERE_CRMETHOD, 15);
        $myReporte->setCampo("saldo", lang("saldo"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "saldo", HAVING_CRMETHOD, 28, true, true);
        $myReporte->setCampo("comision", lang('comision'), false, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, 'comision', HAVING_CRMETHOD, 60);
        $myReporte->setCampo("mora", lang("MORA"), true, FLOAT_CRTYPE,           array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "mora", HAVING_CRMETHOD, 28, true, true);
        $myReporte->setCampo("importe_total", lang("importe"), true, FLOAT_CRTYPE, array(ES_IGUAL_CRFILTER, MAYOR_CRFILTER, MENOR_CRFILTER, MAYOR_IGUAL_CRFILTER, MENOR_IGUAL_CRFILTER, ENTRE_CRFILTER), true, null, "importe_total", HAVING_CRMETHOD, 20, true, true);
        $myReporte->setCampo("deuda_alumno", lang('tipo_deuda'), true, TIPO_DEUDA_CRTYPE, array(ES_IGUAL_CRFILTER), true, null, 'deuda_alumno', HAVING_CRMETHOD, 28);
        $myReporte->setCampo("nombre_curso", lang('curso_presu_as'), true, STRING_CRTYPE, array(LIKE_CRFILTER), true, null, 'general.cursos.nombre_es', HAVING_CRMETHOD, 60);
        /* FILTROS COMUNES */
        $myReporte->setFiltrosComunes(lang("deuda_mensual"), "deuda", array("MONTH(ctacte.fechavenc) = MONTH(CURDATE()) AND YEAR(ctacte.fechavenc) = year(CURDATE())"), WHERE_CRMETHOD, lang("deudas_de_este_mes"));
        $myReporte->setFiltrosComunes(lang('deuda_activa'), "deuda_activa", array("deuda_alumno = '" . lang('deuda_activa') . "'"), HAVING_CRMETHOD, lang('deuda_activa'));
    }

    // public function getMovimientosCajasGastos()
}
