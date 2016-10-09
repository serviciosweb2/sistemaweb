<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Configuracion extends CI_Controller {

    public $columnas = array();

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
    }

    public function configplan() {
        $validar_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cuentas_google", "", false, $configAlumnos);
        $this->load->model("Model_configuraciones", "", false, $configAlumnos);
        $data['page'] = 'configuracion/configuracion_general';
        $data['seccion'] = $validar_session;
        $myGoogleAccount = $this->Model_cuentas_google->getGoogleAccount($filial["codigo"]);
        $data['myGoogleAccount'] = $myGoogleAccount;
        $this->load->model("Model_impresiones");
        $arrScript = $this->Model_impresiones->getScriptsImpresiones($filial['codigo']);
        $arrDias = array("lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo");
        $data['arrDias'] = $arrDias;
        $data['scripts'] = $arrScript;
        $arrHorariosAtencion = $this->Model_configuraciones->get_horarios_atencion();
        $data['arrHorariosAtencion'] = $arrHorariosAtencion;
        $data['pieHojaMembretada'] = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimir_pie_pagina_hoja_membretada');
        $data['repetirEncabezadoInformes'] = $this->Model_configuraciones->getValorConfiguracion(null, 'repetirEncabezadoInformes');
        $claves = array('Utilizar_en_todas_las_impresiones', 'Esta_impresora_se_utiliza_en_todas_las_impresiones',
            'Imprimir_por_el_navegador', 'Esta_es_la_impresora_por_default', 'No_hay_impresoras_registradas_en_cloud_print',
            'horario_de_entrada', 'del_dia', 'horario_de_salida', 'debe_ser_menor_que', 'datos_actualizados_correctamente',
            'ocurrio_error', 'validacion_ok', "fecha_desde", "fecha_hasta", "estado", "nombre",  "detalle", "ver_editar", "validacion_ok", "ocurrio_error",
            "horadesde_horario", "horaHasta_horario", "documento");
        $data['lang'] = getLang($claves);
        $data['checkcloudprint'] = $this->session->userdata('checkcloudprint');
        $this->load->view('container', $data);
    }

    public function index() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $conf['method'] = 'vistaFiliales';
        header('location:' . base_url('configuracion/' . $conf['method']));
    }

    public function guardarGoogleAccount() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $arrResp = array();
        if ($_POST['utiliza_cloud'] == 1) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_pass', 'Password', 'required');
            $this->form_validation->set_rules('user_name', 'Email', 'required|valid_email');
            if ($this->form_validation->run() == FALSE) {
                $arrResp['error'] = validation_errors();
            } else {
                $config = array("codigo_filial" => $filial["codigo"]);
                $this->load->model("Model_cuentas_google", "", false, $config);
                if (!$this->Model_cuentas_google->saveGoogleAccount($filial['codigo'], $_POST['user_name'], $_POST['user_pass'], 0)) {
                    $arrResp['error'] = "Error al guardar los datos<br>Vuelva a intentar en unos momentos";
                } else {
                    $arrResp['success'] = "success";
                    $arrResp['successMSG'] = "Datos Actualizados Correctamente";
                }
            }
        } else {
            $config = array("codigo_filial" => $filial["codigo"]);
            $this->load->model("Model_cuentas_google", "", false, $config);
            if ($this->Model_cuentas_google->disableAccount($filial['codigo'])) {
                $arrResp['success'] = "success";
                $arrResp['successMSG'] = "Cuenta Inactiva Correctamente";
            } else {
                $arrResp['error'] = "Error al dar de baja la cuenta<br>Vuelva a intentar mas tarde";
            }
        }
        echo json_encode($arrResp);
    }

    public function guardarConfiguracionPapel() {
        $agregar_pie = $this->input->post("agregar_pie");
        $repetir_encabezado_informes = $this->input->post("repetir_encabezado_informes");
        $codUsuario = $this->session->userdata("codigo_usuario");
        $arrFilial = $this->session->userdata("filial");
        $codFilial = $arrFilial['codigo'];
        $arrResp = $this->Model_configuraciones->guardarConfiguracion($codUsuario, "imprimir_pie_pagina_hoja_membretada", $agregar_pie, $codFilial);
        $arrResp = $arrResp && $this->Model_configuraciones->guardarConfiguracion($codUsuario, "repetirEncabezadoInformes", $repetir_encabezado_informes, $codFilial);
        echo json_encode($arrResp);
    }

    public function getPrintersList() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $config);
        $arrImpresoras = $this->Model_impresiones->getPrintersList($_POST['id_filial']);
        echo json_encode($arrImpresoras);
    }

    public function addDefaultPrinter() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $idFilial = $_POST['id_filial'];
        $printerID = $_POST['printer_id'];
        $name = $_POST['name'];
        $displayName = $_POST['displayName'];
        $proxy = $_POST['proxy'];
        $default = 1;
        $this->load->model("Model_impresiones", "", false, $config);
        $arrResp = array();
        if (!$this->Model_impresiones->guardarPrinterCloud($idFilial, $printerID, $name, $displayName, $proxy, $default)) {
            $arrResp['error'] = "Error al guardar la impresora como impresora por defecto";
        } else {
            $arrResp['success'] = "success";
        }
        echo json_encode($arrResp);
    }

    public function saveAdvancedSetting() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $config);
        $arrResp = array();
        $arrScriptPrinter = array();
        foreach ($_POST['settings'] as $setting) {
            $arrScriptPrinter[$setting['script']]['printer_id'] = $setting['printer_id'];
            if ($setting['printer_id'] <> -1) {
                $arrScriptPrinter[$setting['script']]['name'] = $setting['name'];
                $arrScriptPrinter[$setting['script']]['display'] = $setting['display_name'];
                $arrScriptPrinter[$setting['script']]['proxy'] = $setting['proxy'];
            }
            $arrScriptPrinter[$setting['script']]['metodo'] = $setting['metodo'];
        }
        if ($this->Model_impresiones->setPrinterScript($filial['codigo'], $arrScriptPrinter)) {
            $arrResp['success'] = "success";
            $arrResp['successMSG'] = lang("datos_actualizados_correctamente");
        } else {
            $arrResp['error'] = "Error al setear impresoras con script de impresion\nvuelva a intentar mas tarde";
        }
        echo json_encode($arrResp);
    }

    function guardar_horarios_laborales() {
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $configuracionHorario = $_POST;
        $arrResp = array();
        if ($this->Model_configuraciones->guardar_horarios_atencion($configuracionHorario)) {
            $arrResp['success'] = lang("datos_actualizados_correctamente");
        } else {
            $arrResp['error'] = lang("error_al_guardar_los_horarios_de_atencion");
        }
        echo json_encode($arrResp);
    }

    function configuracion_impresion_extra($idScript, $scriptName) {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
        $data['id_script'] = $idScript;
        $data['script_name'] = $scriptName;
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, $idScript);
        $data['configuracion'] = $arrConfiguracion;
        $this->load->view('configuracion/configuracion_impresion_extra', $data);
    }

    function guardar_configuracion_impresion_extra() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
        $id_script = $_POST['id_script'];
        $cantidad_copias = isset($_POST['cantidad_copias']) ? $_POST['cantidad_copias'] : null;
        $tamanioPapel = isset($_POST['tamanio_papel']) ? $_POST['tamanio_papel'] : null;
        $imprimeRazonSocial = isset($_POST['imprime_razon_social']) && $_POST['imprime_razon_social'] <> -1 ? $_POST['imprime_razon_social'] : null;
        $muestraCuotasTotal = isset($_POST['muestra_cuotas_total']) ? $_POST['muestra_cuotas_total'] : null;
        $imprimePlan = isset($_POST['imprimeCurso']) ? $_POST['imprimeCurso'] : null;
        $imprimeTitulo = isset($_POST['imprimeTitulo']) ? $_POST['imprimeTitulo'] : null;
        $localidadForo = isset($_POST['localidadForo']) ? $_POST['localidadForo'] : null;
        $texto = isset($_POST['texto']) ? $_POST['texto'] : null;
        $mostrarPrecioListaYDescuento = isset($_POST['mostrar_precio_lista_descuento']) ? $_POST['mostrar_precio_lista_descuento'] : 0;
        $modelo_factura_electronica = isset($_POST['modelo_factura_electronica']) && $_POST['modelo_factura_electronica'] <> ''
                ? $_POST['modelo_factura_electronica'] : null;
        $mostrarRUC = isset($_POST['mostrar_ruc']) && $_POST['mostrar_ruc'] <> -1 ? $_POST['mostrar_ruc'] : null;
        $mostrarCOM = isset($_POST['mostrar_com']) && $_POST['mostrar_com'] <> -1 ? $_POST['mostrar_com'] : null;
        $arrResp = array();
        if (!$this->Model_configuraciones->guardar_configuracion_impresion_extra($id_script, $cantidad_copias, $tamanioPapel, $imprimeRazonSocial, 
                $muestraCuotasTotal, $texto, $imprimePlan, $imprimeTitulo, $localidadForo, $mostrarPrecioListaYDescuento, 
                $modelo_factura_electronica, $mostrarRUC, $mostrarCOM)) {
            $arrResp['error'] = "error_al_guardar_la_configuracion_de_impresion";
        } else {
            $arrResp['success'] = "success";
        }
        echo json_encode($arrResp);
    }

    function configuracion_impresion_facturacion($idScript, $scriptName) {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
        $data['id_script'] = $idScript;
        $data['script_name'] = $scriptName;
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(null, "configuracionImpresiones", $idScript);
        $data['configuracion'] = $arrConfiguracion;
        $data['agregarConfiguracionRUT'] = $filial['codigo'] == 36;
        $this->load->view('configuracion/configuracion_impresion_facturacion', $data);
    }

    function configuracion_impresion_con_texto($idScript, $scriptName) {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
        $data['id_script'] = $idScript;
        $data['script_name'] = $scriptName;
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, $idScript);
        switch ($idScript) {
            case 5:
                $texto = $this->Model_configuraciones->getValorConfiguracion(11);
                $data['imprime_plan'] = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimirMatriculaPlanAcademico');
                $data['imprime_titulo'] = $this->Model_configuraciones->getValorConfiguracion(null, 'imprimirMatriculaTitulo');
                $this->load->model("Model_filiales", "", false, $filial['codigo']);
                $data['muestra_foro'] = $this->Model_filiales->imprimeReglamento('prestacion_de_servicio');
                if ($data['muestra_foro']) {
                    $this->load->model("Model_paises", "", false, $filial['pais']);
                    $data['provincias'] = $this->Model_paises->getprovincias();
                    $data['localidad_foro'] = $this->Model_configuraciones->getValorConfiguracion(null, 'localidadContratoForo');
                    $data['provincia_foro'] = '';
                    $data['localidades'] = array();
                    if ($data['localidad_foro'] != '0' && $data['localidad_foro'] != null) {
                        $this->load->model("Model_localidades", "", false, $filial['codigo']);
                        $data['provincia_foro'] = $this->Model_localidades->getProvincia($data['localidad_foro']);
                        $this->load->model("Model_provincias", "", false, $data['provincia_foro']);
                        $data['localidades'] = $this->Model_provincias->getLocalidades();
                    }
                }
                break;
            case 1;
                $texto = $this->Model_configuraciones->getValorConfiguracion(null, "descripcionPiePresupuesto");
                $data["mostrarPrecioListaYDescuento"] = $this->Model_configuraciones->getValorConfiguracion(null, "mostrarPrecioListaYDescuento");
                break;

            default:
                $texto = "";
                break;
        }
        $data['configuracion'] = $arrConfiguracion;
        $data['texto'] = $texto;
        $this->load->view('configuracion/configuracion_impresion_con_texto', $data);
    }

    public function vistaImpresiones() {
        $validar_session = session_method();
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/configuracion_general';
        $data['seccion'] = $validar_session;
        $this->load->view('container', $data);
    }

    public function vistaFiliales() {
        $validar_session = session_method();
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cuentas_google", "", false, $configAlumnos);
        $this->load->model("Model_configuraciones", "", false, $configAlumnos);
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        $data['seccion'] = $validar_session;
        $arrayDiasFilial = array("0", "1", "2", "3", "4", "5", "6");
        $arrDias = array("lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo");
        $data['arrDias'] = $arrDias;
        $arrHorariosAtencion = $this->Model_configuraciones->get_horarios_atencion();
        $data['arrHorariosAtencion'] = $arrHorariosAtencion;
        $data['token'] = $this->Model_configuraciones->getValorConfiguracion('', 'modoOffline', '');
        $data['cobro_filial_dias'] = $this->Model_configuraciones->getDiasCobrosFilial();
        $data['lista_dias_filial'] = $arrayDiasFilial;
        $data['listado_receso_filial'] = $this->Model_filiales->getListadoRecesoFilial();
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/vistaFiliales';
        $data['seccion'] = $validar_session;
        $claves = array('Utilizar_en_todas_las_impresiones', 'Esta_impresora_se_utiliza_en_todas_las_impresiones',
            'Imprimir_por_el_navegador', 'Esta_es_la_impresora_por_default', 'No_hay_impresoras_registradas_en_cloud_print',
            'horario_de_entrada', 'del_dia', 'horario_de_salida', 'debe_ser_menor_que', 'datos_actualizados_correctamente',
            'ocurrio_error', 'validacion_ok', "fecha_desde", "fecha_hasta", "estado", "nombre", "detalle",
            "ver_editar", "validacion_ok", "ocurrio_error", "horadesde_horario", "horaHasta_horario", "documento"
        );
        $data['lang'] = getLang($claves);
        $this->load->view('container', $data);
    }

    public function vistaUsuarios() {
        $validar_session = session_method();
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/vistaUsuarios';
        $data['seccion'] = $validar_session;
        $claves = array('cambio_estado_usuario', "codigo_cobro", "mediopago_cobro", "habilitar-factura", "deshabilitar-factura", "MATRICULA_HABILITADA", "facturacion_codigo", "facturacion_estado", "facturacion_anular", "HABILITADO", "INHABILITADO", "caja_default", "INHABILITAR", "HABILITAR");
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('configuracion');
        $this->load->view('container', $data);
    }

    public function frm_usuarios() {
        $valida_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $cod_usuario = $this->input->post('cod_usuario');
        $tab = 1;
        $data = '';
        $idiomas = array(
            array('id' => 'es',
                'nombre' => lang('es')),
            array('id' => 'pt',
                'nombre' => lang('pt')),
            array('id' => 'en',
                'nombre' => lang('en'))
        );
        $data['listaIdiomas'] = $idiomas;
        if ($cod_usuario != -1) {
            $data['objUsuario'] = $this->Model_usuario->getObjUsuario($cod_usuario);
        }
        $data['vista_tab'] = $tab;
        $data['page'] = 'configuracion/frm_usuarios';
        $data['seccion'] = $valida_session;
        $data['cajas'] = $this->Model_usuario->getCajas($cod_usuario, 0);
        $this->load->view('container', $data);
    }

    public function getSeccionesPermisos() {
        session_method();
        $filial = $this->session->userdata('filial');
        $secciones = $this->session;
        $permisoSeccion = $secciones->userdata('secciones');
        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $cod_usuario = $this->input->post('cod_usuario');
        $retorno = false;
        $respuesta = '';
        $mensaje = '';
        $codigo = '';
        foreach ($permisoSeccion['configuracion']['subcategorias'] as $subcategoria) {
            if ($subcategoria['slug'] == 'modificar_usuario' && $subcategoria['habilitado'] == 1) {

                $retorno = true;
                $respuesta = $this->Model_usuario->getPermisosSecciones($cod_usuario, $filial['codigo']);
            }
        }
        if ($retorno == false) {
            $codigo = 0;
            $mensaje = lang('no_tiene_permiso');
        } else {
            $codigo = 1;
            $mensaje = $respuesta;
        }
        $retornar = array(
            'codigo' => $codigo,
            'msg' => $mensaje,
        );
        echo json_encode($retornar);
    }

    public function configFacturacion() {
        $validar_session = session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial['codigo']);
        $data = array();
        $data['page'] = 'configuracion/configFacturacion';
        $data['seccion'] = $validar_session;
        $data['MesesBajaDeudores'] = $this->Model_configuraciones->getValorConfiguracion("", "MesesBajaDeudores", "");
        $data['mesesVencidaBaja'] = $this->Model_configuraciones->getValorConfiguracion("", "mesesVencidaBaja", "");
        $data['facturacion_nominada'] = $this->Model_configuraciones->getValorConfiguracion('', 'facturacionNominada', '');
        $data['facturacion_segmentada'] = $this->Model_configuraciones->getValorConfiguracion('', 'facturacionSegmentada', '');
        $data['monto_segmento'] = $this->Model_configuraciones->getValorConfiguracion('', 'montoSegmento', '');
        $data['pais'] = $filial['pais'];
        $data['edita_mora'] = $this->session->userdata('codigo_usuario') == 41; // unico usuario no configurable para evitar más errores
        $claves = array("validacion_ok", "nombre", "cajas", "codigo", "estado", "detalle", "ver_editar", "puntos_venta", 
            "razon_social", "Facturas", "ultimo_numero", "detalle", "ver_editar", "inicio_de_actividades", "dia_desde", 
            "dia_hasta", "MORA", "porcentaje", "diariamente", "detalle", "tipo", "ERROR", "error_al_deshabilitar_boletos_bancarios", 
            "valor", "medio_pago", "conciliacion_automatica", "confirmacion_automatica", "no_puede_refinanciar_ctacte", 
            "codigo_interno", "error_al_actualizar_los_puntos_de_venta", "tipo", "concepto", "operador", "HABILITADO", "INHABILITADO", 
            "fecha_contrato", "otro", "manual", "internet", "pos", "tipo_captura", "documento", "dias_despues_de_vencimiento");
        $data['lang'] = getLang($claves);
        if ($filial['pais'] == 2) {
            $data['bancos'] = $this->Model_bancos->listarBancos(2);
            $data['utilizaBoletoBancario'] = $this->Model_configuraciones->getValorConfiguracion("", "utilizaBoletosBancarios", "");
            $data['cuentas_bancarias'] = $this->Model_bancos->listarCuentas();
        }
        $this->load->view('container', $data);
    }

    public function resetear_moras(){
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $arrResp = array();
        if (Vctacte::resetear_moras($conexion)){
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "error";
            $arrResp['dbg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($arrResp);
    }
    
    public function getImpuestos() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_impuestos", "", false, $config);
        $formato = true;
        $data = $this->Model_impuestos->getImpuestos($formato);
        echo json_encode($data);
    }

    public function frm_impuesto() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_conceptos", "", false, $config);
        $cod_impuesto = $this->input->post('cod_impuesto');
        $this->load->model("Model_impuestos", "", false, $config);
        $data['detalleImpuesto'] = $this->Model_impuestos->getDetallesImpuestos($cod_impuesto);
        $data['objImpuesto'] = $this->Model_impuestos->objImpuesto($cod_impuesto);
        $claves = array("validacion_ok", "codigo", "nombre", "detalle", "activo", "concepto", "valor", "impuesto_sin_detalles", "ocurrio_error");
        $data['langFrm'] = getLang($claves);
        $data['listaImpuestos'] = $this->Model_impuestos->getImpuestos();
        $data['conceptos'] = $this->Model_conceptos->getAllConceptos($cod_impuesto);
        $this->load->view('configuracion/frm_impuesto', $data);
    }

    public function getPuntosVentas() {
        session_method();
        $estado = $this->input->post('estado');
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_puntos_venta", "", false, array('codigo_filial' => $filial["codigo"]));
        $data = $this->Model_puntos_venta->getPuntosVentas($estado);
        echo json_encode($data);
    }

    public function getFacturantes() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_facturantes", "", false, $config);
        $data = $this->Model_facturantes->getFacturantes(false, true, true);
        echo json_encode($data);
    }

    public function getCajas() {
        session_method();
        $estado = $this->input->post('estado');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_caja", "", false, $config);
        $data = $this->Model_caja->getCajas(null, $estado);
        echo json_encode($data);
    }

    public function frm_nuevoImpuesto() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array('codigo_filial' => $filial['codigo']);

        $cod_impuesto = $this->input->post('cod_impuesto');
        $this->load->model('Model_impuestos', '', false, $config);

        $arrTiposImpuestos = array(
            array('codigo' => 'compras', 'nombre' => lang('compras')),
            array('codigo' => 'ventas', 'nombre' => lang('ventas')),
        );

        if ($filial['pais'] == 1) { // Sólo para Argentina.
            $conexion = $this->load->database('general', true);
            $Vimp_gral = new Vimpuestos_general($conexion);
            $arrImpGenerales = $Vimp_gral->getImpuestos_operacionIVA();
        }else {
            $arrImpGenerales = null;
        }

        $claves = array('validacion_ok', 'codigo', 'nombre', 'detalle', 'activo', 'concepto', 'valor', 'impuesto_sin_detalles', 'ocurrio_error');
        $data = array(
            'langFrm' => getLang($claves),
            'tipos_impuestos' => $arrTiposImpuestos,
            'impuestos_general' => $arrImpGenerales,
            'objImpuesto' => $this->Model_impuestos->objImpuesto($cod_impuesto)
        );

        $this->load->view('configuracion/frm_nuevoImpuesto', $data);
    }

    public function frm_caja() {        
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_caja", "", false, $config);
        $caja = $this->input->post("codigo");
        $objCaja = $this->Model_caja->getCaja($caja);
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $data["caja"] = $objCaja;
        $data["mediosAsignados"] = array();
        $data["usuariosAsignados"] = array();
        if ($objCaja->getCodigo() != -1) {
            $data["usuariosAsignados"] = $objCaja->getUsuarios();
            $monedaDefault = $objCaja->cod_moneda;
        } else {
            $monedaDefault = $filial['moneda']['id'];
        }
        $data["medios"] = $this->Model_caja->getMediosPagosConfiguracion($caja);
        $conexion = $this->load->database($filial['codigo'], true);
        $data['arrCotizaciones'] = Vcotizaciones::listarCotizaciones($conexion);
        $data['moneda_default'] = $monedaDefault;
        $config = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $config);
        $data["usuarios"] = $this->Model_usuario->getUsuarios(0);
        $this->load->view('configuracion/frm_caja', $data);
    }

    public function frm_puntoDeVenta() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_puntos_venta", "", false, array("codigo_filial" => $filial["codigo"]));
        $this->load->model("Model_usuario", "", false, array("filial" => $filial['codigo']));
        $codigo = $this->input->post('codigo');
        $data['usuarios'] = $this->Model_usuario->getUsuarios();
        $data['myPuntoVenta'] = $this->Model_puntos_venta->getObjPuntoVenta($codigo);
        $data['usuarios_habilitados'] = $this->Model_puntos_venta->getUsuariosHabilitados($codigo);
        $claves = array("validacion_ok", "ERROR", "datos_actualizados_correctamente", "error_al_actualizar_los_puntos_de_venta");
        $data['langFrm'] = getLang($claves);
        $this->load->view('configuracion/frm_puntoDeVenta', $data);
    }

    public function guardarPuntoVenta() {
        $arrResp = array();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_puntos_venta", "", false, array("codigo_filial" => $filial["codigo"]));
        $codigo = $this->input->post("codigo");
        $proximoNumero = $this->input->post("proximo_numero");
        $activo = $this->input->post("activo");
        $arrUsuariosPermisos = $this->input->post("usuarios_permisos");
        if ($this->Model_puntos_venta->guardarPuntoVenta($codigo, $proximoNumero, $activo, $arrUsuariosPermisos)) {
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "error";
        }
        echo json_encode($arrResp);
    }

    public function frm_facturante() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_razones_sociales", "", false, $config);
        $this->load->model("Model_facturantes", "", false, $config);
        $this->load->model("Model_localidades", "", false, $filial['pais']);
        $codigos = json_decode($this->input->post('codigos'), true);
        $data['provincias'] = $this->Model_paises->getprovincias();
        $data['tipoDni'] = $this->Model_paises->getDocumentosPersonasFisicas();
        $data['condiciones'] = $this->Model_paises->getCondicionesSociales();
        $data['empresaTel'] = $this->Model_paises->getEmpresasTelefonicas();
        $objFacturante = $this->Model_facturantes->getFacturante($codigos['cod_facturante']);
        $objRazonSocial = $this->Model_razones_sociales->getRazonSocial($codigos['cod_razon_social']);
        if ($codigos['cod_razon_social'] != -1 && $codigos['cod_facturante'] != -1) {
            $data['objRazonSocial'] = $objRazonSocial;
            $data['razonTelefono'] = $this->Model_razones_sociales->getTelefonoRazon($codigos['cod_razon_social']);
            $data['objFacturantes'] = $objFacturante;
            $data['provincia'] = $this->Model_localidades->getProvincia($objRazonSocial->cod_localidad);
        }
        $this->load->view('configuracion/frm_facturante', $data);
    }

    public function getlocalidades() {
        session_method();
        $nombreProv = $this->input->post('idprovincia');
        $this->load->model("Model_provincias", "", false, $nombreProv);
        $localidades = $this->Model_provincias->getLocalidades();
        echo json_encode($localidades);
    }

    public function configPlanPago() {
        $validar_session = session_method();
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/vistaPlanPago';
        $dtocondicionado = $this->Model_configuraciones->getValorConfiguracion(null, 'descuentosCondicionados');
        $data['dtoCondicionado'] = $dtocondicionado['activo'];
        $data['dtoCondicionado_dias'] = $dtocondicionado['dias_prorroga'];
        $data['bajaMorosos'] = $this->Model_configuraciones->getValorConfiguracion(null, 'bajaDirectaMorosos');
        $data['vigenciaPresupuesto'] = $this->Model_configuraciones->getValorConfiguracion(null, 'DiasVigenciaPresupuesto');
        $data['alertaDeudores'] = $this->Model_configuraciones->getValorConfiguracion(null, 'CantAlertasDeudores');
        $data['descripcionPiePresupuesto'] = $this->Model_configuraciones->getValorConfiguracion(null, 'descripcionPiePresupuesto');
        $data['alertaSugerenciaBaja'] = $this->Model_configuraciones->getValorConfiguracion(null, 'AlertaSugerenciaBaja');
        $claves = array("validacion_ok", "ver_editar", "planpago_periodo", "activo");
        $data['lang'] = getLang($claves);
        $data['seccion'] = $validar_session;
        $this->load->view('container', $data);
    }

    public function periodicidadPago() {
        $periodos = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte');
        $data = $this->Model_configuraciones->traducirPeriodos($periodos);
        echo json_encode($data);
    }

    public function frm_periodicidad() {
        session_method();
        $codigo = $this->input->post('codigo');
        $selecPerioros = array(
            array('id' => 'day',
                'nombre' => lang('dia')),
            array('id' => 'month',
                'nombre' => lang('mes')),
            array('id' => 'year',
                'nombre' => lang('año'))
        );
        $data['codigo'] = $codigo;
        $data['selectPeriodos'] = json_encode($selecPerioros);
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $this->load->view('configuracion/frm_periodicidad', $data);
    }

    public function limpiarLang() {
        $lang = array();
        include APPPATH . "language/es/es_lang.php";
        $arrayLimpio = array();
        $keyRepetidos = array();
        foreach ($lang as $key => $value) {
            if (in_array($value, $arrayLimpio)) {
                $keyRepetidos[] = $key;
            } else {
                $arrayLimpio[$key] = $value;
            }
        }
        echo "<pre>";
        echo 'array Limpio';
        print_r($arrayLimpio);
        echo "</pre>";

        echo "<pre>";
        echo 'keys repetidos';
        print_r($keyRepetidos);
        echo "</pre>";
    }

    public function config_igacloud() {
        $validar_session = session_method();
        $config = array("idioma" => get_idioma());
        $this->load->model("Model_como_nos_conocio", "", false, $config);
        $data['como_nos_conocio'] = $this->Model_como_nos_conocio->listarArbolComoNosConocio();
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/vistaIgaCloud';
        $data['seccion'] = $validar_session;
        $claves = array();
        $data['lang'] = getLang($claves);
        $this->load->view('container', $data);
    }

    public function config_academico() {
        $session = $this->session->userdata('filial');
        $config = array("codigo_filial" => $session["codigo"]);
        $this->load->model("Model_salones", "", false, $config);
        $validar_session = session_method();
        $arrTipoNotas = array(
            "numerico" => lang('numerico'),
            "alfabetico" => lang('alfabetico')
        );
        $arrConfigNotas = $this->Model_configuraciones->getValorConfiguracion(null, 'configuracionNotaExamen');
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/vistaAcademicos'; 
        $data['seccion'] = $validar_session;
        $data['matriculas_sin_cupo'] = $this->Model_configuraciones->getValorConfiguracion(2);
        $data['nota_aprueba'] = $this->Model_configuraciones->getValorConfiguracion(6);
        $data['cursos_periodos'] = $this->Model_configuraciones->getValorConfiguracion(null, 'CursosTodosPeriodos', null);
        $data['porcentaje_asistencia_regular'] = $this->Model_configuraciones->getValorConfiguracion(10);
        $data['alerta_examen_sin_nota'] = $this->Model_configuraciones->getValorConfiguracion(12);
        $data['cant_maxima_examen'] = $this->Model_configuraciones->getValorConfiguracion(14);
        $data['meses_duracion_regularidad'] = $this->Model_configuraciones->getValorConfiguracion(15);
        $data['nombre_formato'] = $this->Model_configuraciones->getValorConfiguracion(4);
        $data['nombre_separador'] = $this->Model_configuraciones->getValorConfiguracion(5);
        $data['config_notas_examenes'] = $this->Model_configuraciones->getValorConfiguracion(null, 'configuracionNotaExamen');
        $data['ver_nombre_viejo_comision'] = $this->Model_configuraciones->getValorConfiguracion(null, 'verNombreViejoComision');
        $data['comisiones_sin_cupo'] = $this->Model_configuraciones->getValorConfiguracion(null, 'comisionesSinCupo');
        $data['horasInscripcionExamen'] = $this->Model_configuraciones->getValorConfiguracion(null, 'horasCierreInscripcionExamen');
        if ($arrConfigNotas['formato_nota'] == 'alfabetico') {
            $data['arrayEscalaNotas'] = $this->Model_configuraciones->getArrayEscalaNotasExamen('configuracionNotaExamen', $arrConfigNotas['formato_nota']);
        }
        $claves = array("validacion_ok", "alertar", "activo", "nota_desde_hasta", "SALON_GUARDADO_CORRECTAMENTE", "BIEN");
        $data['lang'] = getLang($claves);
        $data['tipos_notas'] = $arrTipoNotas;
        $data["salones"] = $this->Model_salones->getSalonesHorarios();
        $conexion = $this->load->database('general', true);
        $data['arrComoNosConocio'] = Vcomo_nos_conocio::listarComo_nos_conocio_config($conexion, null, $session['codigo']);
        $this->load->view('container', $data);
    }

    public function getConfiguracionAlertaExamen() {
        session_method();
        $alertaExamen = $this->Model_configuraciones->getValorConfiguracion(21);
        foreach ($alertaExamen as $key => $value) {
            $alertaExamen[$key]['tipo_traduccion'] = lang($value['tipo']);
            if ($value['baja'] == 1) {
                unset($alertaExamen[$key]);
            }
        }
        sort($alertaExamen);
        $data = $this->Model_configuraciones->traducirPeriodos($alertaExamen);
        echo json_encode($data);
    }

    public function frm_alertaExamen() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_examenes", "", false, $config);
        $selecPerioros = array(
            array('id' => 'day',
                'nombre' => lang('dia')),
            array('id' => 'month',
                'nombre' => lang('mes')),
            array('id' => 'year',
                'nombre' => lang('año'))
        );
        $data['selectPeriodos'] = json_encode($selecPerioros);
        $data['tipo_examen'] = $this->Model_examenes->getTiposExamenes();
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $this->load->view('configuracion/frm_alertaExamen', $data);
    }

    public function guardarConfiguracionAcademicos() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = $this->input->post('nombre');
        $valor = $this->input->post('valor');
        $resultado = '';
        $alertaExamen = $this->Model_configuraciones->getValorConfiguracion(21);
        if ($nombre == 'ConfiguracionAlertaExamenes') {
            if ($valor == -1) {
                $this->form_validation->set_rules('cantidad', lang('cantidad'), 'required|numeric');
                $this->form_validation->set_rules('unidadTiempo', lang('unidad_tiempo'), 'required');
                $this->form_validation->set_rules('tipo', lang('tipo'), 'required');
                if ($this->form_validation->run() == false) {
                    $errors = validation_errors();
                    $resultado = array(
                        'codigo' => '0',
                        'msgerror' => $errors,
                        'errNo' => '',
                    );
                } else {
                    $proxCodigo = count($alertaExamen);
                    $alertaExamen[] = array(
                        "tipo" => $this->input->post('tipo'),
                        "codigo" => $proxCodigo,
                        "baja" => 0,
                        "valor" => $this->input->post('cantidad'),
                        "unidadTiempo" => $this->input->post('unidadTiempo')
                    );
                    $resultado = $this->Model_configuraciones->guardarConfiguracionAlertaExamen($alertaExamen, $nombre, $usuario);
                }
            } else {
                foreach ($alertaExamen as $key => $value) {
                    if ($value['codigo'] == $valor) {
                        $alertaExamen[$key]['baja'] = '1';
                    }
                }
                $resultado = $this->Model_configuraciones->guardarConfiguracionAlertaExamen($alertaExamen, $nombre, $usuario);
            }
        } else {
            $resultado = $this->Model_configuraciones->guardarConfiguracion($usuario, $nombre, $valor, $filial['codigo']);
        }
        echo json_encode($resultado);
    }

    public function frm_usuario() {
        $valida_session = session_method();
        $codUsuario = $this->session->userdata('codigo_usuario');
        $filial = $this->session->userdata('filial');
        $this->lang->load(get_idioma(), get_idioma());
        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $tab = 0;
        $idiomas = array(
            array('id' => 'es',
                'nombre' => lang('es')),
            array('id' => 'pt',
                'nombre' => lang('pt')),
            array('id' => 'en',
                'nombre' => lang('en'))
        );
        $seccion = array(
            'titulo' => 'configuracion',
            'categoria' => 'ajustes'
        );
        $data['listaIdiomas'] = $idiomas;
        $data['seccion'] = $seccion;
        $data['vista_tab'] = $tab;
        $data['seccion'] = $valida_session;
        $data['objUsuario'] = $this->Model_usuario->getObjUsuario($codUsuario);
        $data['cajas'] = $this->Model_usuario->getCajas($codUsuario, 0);
        $data['page'] = 'configuracion/frm_usuarios';
        $this->load->view('container', $data);
    }

    public function guardarSeparadores() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = $this->input->post('nombre');
        $valor = $this->input->post('valor');
        $retornar = $this->Model_configuraciones->guardarConfiguracion($usuario, $nombre, $valor, $filial['codigo']);
        echo json_encode($retornar);
    }

    public function guardarConfiguracionSugerencia() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = $this->input->post('nombre');
        $valor = $this->input->post('valor');
        $retornar = $this->Model_configuraciones->guardarConfiguracion($usuario, $nombre, $valor, $filial['codigo']);
        echo json_encode($retornar);
    }

    public function guardarConfiguracionDescuentos() {
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = $this->input->post('nombre');
        $valor = $this->input->post('valor');
        $dias = $this->input->post('dias_prorroga');
        $datos = array('activo' => $valor, 'dias_prorroga' => $dias);
        $retornar = $this->Model_configuraciones->guardarConfiguracionDescuentos($datos, $nombre, $usuario);
        echo json_encode($retornar);
    }

    public function frm_moras() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_moras", "", false, $config);
        $cod_mora = $this->input->post('codigo');
        $data = '';
        if ($cod_mora != -1) {
            $data['objMora'] = $this->Model_moras->getObjMora($cod_mora);
        }
        $arrayTipo_Moras = array(
            "MORA" => lang('MORA'),
            "MULTA" => lang('MULTA')
        );
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $data['tipo_moras'] = $arrayTipo_Moras;
        $this->load->view('configuracion/frm_moras', $data);
    }

    public function frm_moras_cursos_cortos() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_moras_cursos_cortos", "", false, $config);
        $cod_mora = $this->input->post('codigo');
        $data = '';
        if ($cod_mora != -1) {
            $data['objMora'] = $this->Model_moras_cursos_cortos->getObjMora($cod_mora);
        }
        $arrayTipo_Moras = array(
            "MORA" => lang('MORA'),
            "MULTA" => lang('MULTA')
        );
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $data['tipo_moras'] = $arrayTipo_Moras;
        $this->load->view('configuracion/frm_moras_cursos_cortos', $data);
    }

    // IGAC-539 - Lista Moras Cursos Largos
    public function getListaMoras() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_moras", "", false, $config);
        $listaMoras = $this->Model_moras->getMoras();
        echo json_encode($listaMoras);
    }

    // IGAC-539 - Lista Moras Cursos Cortos
    public function getListaMorasCursosCortos() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_moras_cursos_cortos", "", false, $config);
        $listaMoras = $this->Model_moras_cursos_cortos->getMoras();
        echo json_encode($listaMoras);
    }

    public function config_compras() {
        $validar_session = session_method();
        $data['titulo_pagina'] = '';
        $data['page'] = 'configuracion/config_compras';
        $data['seccion'] = $validar_session;
        $claves = array("validacion_ok");
        $data['lang'] = getLang($claves);
        $this->load->view('container', $data);
    }

    public function getConceptosCtaCte() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_conceptos", "", false, $config);
        $data = $this->Model_conceptos->getConceptosUsuario();
        echo json_encode($data);
    }

    public function frm_conceptos() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_conceptos", "", false, $config);
        $this->load->model("Model_impuestos", "", false, $config);
        $cod_concepto = $this->input->post('cod_concepto');
        $data['concepto'] = $this->Model_conceptos->getConcepto($cod_concepto);
        $data['impuestos'] = $this->Model_impuestos->getImpuestos(true);
        $data['impuestosAsignados'] = $this->Model_conceptos->getImpuestosConceptos($cod_concepto);
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $this->load->view('configuracion/frm_conceptos', $data);
    }

    public function guardarConcepto() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_conceptos", "", false, $config);
        $this->load->library('form_validation');
        $data['cod_concepto'] = $this->input->post('codigo');
        $data['cod_usuario'] = $this->session->userdata('codigo_usuario');
        $data['nombre'] = $this->input->post('nombre');
        $data['impuestosAsignar'] = $this->input->post('impuestos_asignados');
        $respuesta = $this->Model_conceptos->guardarConcepto($data);
        echo json_encode($respuesta);
    }

    public function cambiar_estado_banco() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial['codigo']);
        $codBanco = $this->input->post("cod_banco");
        $codCuenta = $this->input->post("cod_cuenta");
        $estado = $this->input->post("estado");
        $arrResp = array();
        if (!$this->Model_bancos->cambiar_estado_cuenta($codBanco, $codCuenta, $estado)) {
            $arrResp['error'] = "error";
        } else {
            $arrResp['success'] = "success";
            $arrResp['cod_banco'] = $codBanco;
            $arrResp['cod_cuenta'] = $codCuenta;
            $arrResp['estado'] = $estado;
        }
        echo json_encode($arrResp);
    }

    function frm_modificar_cuenta_bancaria() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial['codigo']);
        $this->load->model("Model_facturantes", "", false, array("codigo_filial" => $filial['codigo']));
        $codBanco = $this->input->post("cod_banco");
        $codCuenta = $this->input->post("cod_cuenta");
        $cartera = $this->input->post("cartera");
        $data['cuenta'] = $this->Model_bancos->listarCuentas($codBanco, $codCuenta, $cartera);
        $data['razones_sociales'] = $this->Model_facturantes->listarFacturantesRazones();
        $this->load->view('bancos/frm_cuentas_bancarias', $data);
    }

    function guardar_cuenta_bancaria() {
        $arrResp = array();
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $conexion->trans_begin();
        $myBancoDoBrasil = new Vbanco_do_brasil($conexion, $this->input->post("cod_configuracion"));
        $myBancoDoBrasil->agencia = $this->input->post("agencia");
        $myBancoDoBrasil->conta = $this->input->post("conta");
        $myBancoDoBrasil->contrato = $this->input->post("contrato");
        $myBancoDoBrasil->digito_agencia = $this->modulo_11($this->input->post("agencia"));
        $myBancoDoBrasil->digito_cuenta = $this->modulo_11($this->input->post("conta"));
        $myBancoDoBrasil->formatacao_convenio = $this->input->post("formato_convenio");
        $myBancoDoBrasil->formatacao_nosso_numero = $this->input->post("formato_nosso_numero");
        $myBancoDoBrasil->identificacao = $this->input->post("identificacao");
        $myBancoDoBrasil->estado = "habilitada";
        $myBancoDoBrasil->guardar();
        $myCuentaBoleto = new Vcuentas_boletos_bancarios($conexion, $this->input->post("cod_banco"), $myBancoDoBrasil->getCodigo(), $this->input->post("cod_facturante"));
        $myCuentaBoleto->cantidad_copias = $this->input->post("cantidad_copias");
        $myCuentaBoleto->numero_secuencia = $this->input->post("numero_secuencia");
        $myCuentaBoleto->variacao_carteira = $this->input->post("variacao_carteira");
        $myCuentaBoleto->convenio = $this->input->post("convenio");
        $myCuentaBoleto->carteira = $this->input->post("carteira");
        $myCuentaBoleto->demostrativo1 = $this->input->post("demostrativo1");
        $myCuentaBoleto->demostrativo2 = $this->input->post("demostrativo2");
        $myCuentaBoleto->demostrativo3 = $this->input->post("demostrativo3");
        $myCuentaBoleto->instrucciones1 = $this->input->post("instrucciones1");
        $myCuentaBoleto->instrucciones2 = $this->input->post("instrucciones2");
        $myCuentaBoleto->instrucciones3 = $this->input->post("instrucciones3");
        $myCuentaBoleto->instrucciones4 = $this->input->post("instrucciones4");
        $myCuentaBoleto->guardar();
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            $myBanco = new Vbancos($conexion, $this->input->post("cod_banco"));
            $arrResp['success'] = "success";
            $arrResp['codigo_banco'] = $myBanco->getCodigo();
            $arrResp['nombre_banco'] = $myBanco->nombre;
            $arrResp['cuenta'] = $myBancoDoBrasil->conta;
            $arrResp['cartera'] = $myCuentaBoleto->variacao_carteira;
            $arrResp['codigo_cuenta'] = $myBancoDoBrasil->getCodigo();
        } else {
            $conexion->trans_rollback();
            $arrResp['error'] = "error";
        }
        echo json_encode($arrResp);
    }

    function agregar_cuenta_bancaria() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial['codigo']);
        $this->load->model("Model_facturantes", "", false, array("codigo_filial" => $filial['codigo']));
        $data['bancos'] = $this->Model_bancos->listarBancos(2);
        $data['cuenta'] = $this->Model_bancos->getMaquetaCuenta(1);
        $data['registro_nuevo'] = true;
        $data['razones_sociales'] = $this->Model_facturantes->listarFacturantesRazones();
        $this->load->view('bancos/frm_cuentas_bancarias', $data);
    }

    function modulo_11($num, $base = 9, $r = 0) {
        $soma = 0;
        $fator = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num, $i - 1, 1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = "X";
            }
            if (strlen($num) == "43") {
                if ($digito == "0" or $digito == "X" or $digito > 9) {
                    $digito = 1;
                }
            }
            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;
            return $resto;
        }
    }

    function guardar_configuracion_boleto_bancario() {
        $codUsuario = $this->session->userdata("codigo_usuario");
        $filial = $this->session->userdata('filial');
        $respuesta = $this->Model_configuraciones->guardarConfiguracion($codUsuario, "utilizaBoletosBancarios", $this->input->post("utiliza_boleto"), $filial['codigo'], false);
        echo json_encode($respuesta);
    }

    function cargar_vista_banco() {
        $codBanco = $this->input->post("cod_banco");
        switch ($codBanco) {
            case 1:
                $this->load->view('bancos/vista_banco_do_brasil', array());
                break;

            default:
                break;
        }
    }

    public function frm_offline() {
        $data['token'] = $this->Model_configuraciones->getValorConfiguracion('', 'modoOffline', '');
        $this->load->view('configuracion/frm_offline', $data);
    }

    public function guardarConfiguracionOffline() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = $this->input->post('nombre');
        $valor = $this->input->post('valor');
        $nombreEquipo = $this->input->post('nombreEquipo') ? $this->input->post('nombreEquipo') : '';
        $pin = $this->input->post('pin') ? $this->input->post('pin') : '';
        $token = date("Y-m-d H:m:s");
        $token.=$nombreEquipo;
        $token.= $filial['codigo'];
        $json = json_encode(array('estado' => $valor,
            'token' => $valor == 1 ? md5($token) : '',
            'nombreEquipo' => $nombreEquipo,
            'pin' => $pin
        ));
        $retornar = $this->Model_configuraciones->guardarConfiguracion($usuario, $nombre, $json, $filial['codigo']);
        if ($valor == 1) {
            $retornar['token'] = md5($token);
            $retornar['pin'] = md5($pin);
            $this->Model_usuario->setOffline(1);
        }
        echo json_encode($retornar);
    }

    public function guardarDiasCobroFilial() {
        $data_post['dias_cobro_filial'] = $this->input->post('dia');
        $resultado = $this->Model_configuraciones->guardarDiasCobroFilial($data_post);
        echo json_encode($resultado);
    }

    public function guardarConfiguracionNotasExamen() {
        $this->load->library('form_validation');
        $tipo_formato = $this->input->post('NombreFormato');
        $escala_notas = $this->input->post('escala_notas');
        $this->form_validation->set_rules('NombreFormato', lang('tipo_formato'), 'required|validarTipoFormatoNota');
        $respuesta = '';
        if ($tipo_formato == 'alfabetico') {
            $this->form_validation->set_rules('escala_notas', lang('escalas_notas'), 'required');
            $this->form_validation->set_rules('nota_aprueba_final', lang('nota_aprueba_final'), 'required|validarNotaAprubaExamenAlbetico[' . $escala_notas . ']');
            $this->form_validation->set_rules('nota_aprueba_parcial', lang('nota_aprueba_parcial'), 'required|validarNotaAprubaExamenAlbetico[' . $escala_notas . ']');
        } else {
            $notas = array(
                "nota_desde" => $this->input->post('numero_desde'),
                "nota_hasta" => $this->input->post('numero_hasta')
            );
            $arrNotas = json_encode($notas);
            $this->form_validation->set_rules('numero_desde', lang('numero_nota_desde'), 'required');
            $this->form_validation->set_rules('numero_hasta', lang('numero_nota_hasta'), 'required');
            $this->form_validation->set_rules('nota_aprueba_final', lang('nota_aprueba_final'), 'required|validarNotaAprubaExamenNumerico[' . $arrNotas . ']');
            $this->form_validation->set_rules('nota_aprueba_parcial', lang('nota_aprueba_parcial'), 'required|validarNotaAprubaExamenNumerico[' . $arrNotas . ']');
        }
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta = array(
                "codigo" => 0,
                "msgError" => $errors
            );
        } else {
            $data_post['NombreFormato'] = $this->input->post('NombreFormato');
            $data_post['numero_desde'] = $this->input->post('numero_desde');
            $data_post['numero_hasta'] = $this->input->post('numero_hasta');
            $data_post['escala_notas'] = $this->input->post('escala_notas');
            $data_post['nota_aprueba_final'] = $this->input->post('nota_aprueba_final');
            $data_post['nota_aprueba_parcial'] = $this->input->post('nota_aprueba_parcial');
            $respuesta = $this->Model_configuraciones->guardarConfiguracionNotasExamen($data_post);
        }
        echo json_encode($respuesta);
    }

    public function getListadoRecesoFilial() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        $listadoRecesoFilial = $this->Model_filiales->getListadoRecesoFilial();
        echo json_encode($listadoRecesoFilial);
    }

    public function frm_receso_filial() {
        $cod_receso = $this->input->post('cod_receso');
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        if ($cod_receso != -1) {
            $data['array_lista_receso'] = $this->Model_filiales->getArrayRecesoFilial($cod_receso);
            $data['cod_receso'] = $cod_receso;
        } else {
            $data['array_lista_receso'] = '';
            $data['cod_receso'] = $cod_receso;
        }
        $this->load->view('configuracion/frm_nuevo_receso', $data);
    }

    public function guardarRecesoFilial() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        $this->load->library('form_validation');
        $fecha_desde = $this->input->post('fecha_desde');
        $fecha_hasta = $this->input->post('fecha_hasta') != '' ? formatearFecha_mysql($this->input->post('fecha_hasta')) : '';
        $arrayFechas = array(
            "fecha_desde" => $this->input->post('fecha_desde') != '' ? formatearFecha_mysql($this->input->post('fecha_desde')) : '',
            "fecha_hasta" => $fecha_hasta
        );
        $this->form_validation->set_rules('nombre_receso_filial', lang('nombre'), 'required');
        $this->form_validation->set_rules('fecha_desde', lang('fecha_desde'), 'required|validarDiasReceso[' . json_encode($arrayFechas) . ']');
        $this->form_validation->set_rules('fecha_hasta', lang('fecha_hasta'), 'required|validarDia[' . $fecha_desde . ']');
        $this->form_validation->set_rules('horaInicio', lang('horadesde_horario'), 'required');
        $this->form_validation->set_rules('horaFin', lang('horaHasta_horario'), 'required');
        $resultado = '';
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                "codigo" => 0,
                "msgError" => $errors
            );
        } else {
            $data_post['nombre_receso'] = $this->input->post('nombre_receso_filial');
            $data_post['fecha_desde'] = $this->input->post('fecha_desde');
            $data_post['fecha_hasta'] = $this->input->post('fecha_hasta');
            $data_post['hora_desde'] = $this->input->post('horaInicio');
            $data_post['hora_hasta'] = $this->input->post('horaFin');
            $data_post['cod_receso'] = $this->input->post('cod_receso');
            $resultado = $this->Model_filiales->guardarRecesoFilial($data_post);
        }
        echo json_encode($resultado);
    }

    public function baja_receso_filial() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        $cod_receso = $this->input->post('cod_receso');
        $arrResp = $this->Model_filiales->baja_receso_filial($cod_receso);
        echo json_encode($arrResp);
    }

    public function getTerminalesPos() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array('codigo' => $filial['codigo']);
        $this->load->model("Model_medio_tarjetas", "", false, $config);
        $arrResp = $this->Model_medio_tarjetas->getTerminales();
        echo json_encode($arrResp);
    }

    public function frm_terminal() {
        $filial = $this->session->userdata('filial');
        $config = array('codigo' => $filial['codigo']);
        $config2 = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_medio_tarjetas", "", false, $config);
        $this->load->model("Model_facturantes", "", false, $config2);
        $codigo = $this->input->post('codigo');
        $data = '';
        $data['tarjetas_terminal'] = array();
        $data['tarjetas_debito_terminal'] = array();
        if ($codigo != '-1') {
            $data['terminal'] = $this->Model_medio_tarjetas->getTerminal($codigo);
            $data['tarjetas_terminal'] = $this->Model_medio_tarjetas->getTarjetasTerminal($codigo);
            $data['tarjetas_debito_terminal'] = $this->Model_medio_tarjetas->getTarjetasDebitoTerminal($codigo);
        }
        $data['contratos'] = $this->Model_facturantes->getContratosPosFacturantes();
        $data['tarjetas'] = $this->Model_medio_tarjetas->getTiposTarjetasPais();
        $data['tarjetasDebito'] = $this->Model_medio_tarjetas->getTiposDebitoPais();
        $data['capturas'] = $this->Model_medio_tarjetas->getTiposCapturaTerminales();
        $data['codigo'] = $codigo;
        $claves = Array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $this->load->view('configuracion/frm_terminal', $data);
    }

    public function guardarTerminal() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo" => $filial["codigo"]);
        $this->load->model("Model_medio_tarjetas", "", false, $config);
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        $operador = $this->input->post('operador_pos');
        $this->form_validation->set_rules('codigo', lang('tipo_formato'), 'required');
        if ($codigo == '-1') {
            $this->form_validation->set_rules('operador_pos', lang('operador'), 'required');
        }
        $this->form_validation->set_rules('codigo_interno', lang('codigo_interno'), 'required|validarCodigoInternoTerminal[' . json_encode(array('terminal' => $codigo, 'operador' => $operador)) . ']');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta = array(
                "codigo" => 0,
                "msgError" => $errors
            );
        } else {
            $data['codigo'] = $codigo;
            $data['codigo_interno'] = $this->input->post('codigo_interno');
            $data['operador_pos'] = $operador;
            $data['tipo_captura'] = $this->input->post('tipo_captura');
            $data['tarjetas'] = $this->input->post('tarjetas');
            $data['debitos'] = $this->input->post('tarjetasDebito');
            $data['estado'] = $this->input->post('estado');
            $respuesta = $this->Model_medio_tarjetas->guardarTerminal($data);
        }
        echo json_encode($respuesta);
    }
    
    public function guardarHorasCierreInscripcionExamen() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = 'horasCierreInscripcionExamen';
        $valor = $this->input->post('horas');
        $retornar = $this->Model_configuraciones->guardarConfiguracion($usuario, $nombre, $valor, $filial['codigo']);
        echo json_encode($retornar);
    }

    public function descargarCsrFacturante() {
        $facturante = (int) $this->input->post('facturante');
        if ($facturante > 0) {
            if( $pkcs10 = $this->Model_configuraciones->generarCsrFacturante($facturante)){
                header('Content-Type: application/pkcs10');
                header('Content-Length: ' . strlen($pkcs10));
                header('Content-Disposition: attachment; filename="facturante_' . $facturante . '.csr"');
                echo $pkcs10;
            } else {            
                echo "El cuit en la razon social es invalido. Revise los datos de la razon social";
            }
        }
    }

    public function subirCrtFacturante() {
        $facturante = $this->input->post('facturante');
        $resultado = false;
        if (is_uploaded_file($_FILES['crtfile']['tmp_name'])) {
            $resultado = $this->Model_configuraciones->registrarCrtFacturante($facturante, file_get_contents($_FILES['crtfile']['tmp_name']));
        }
       echo  json_encode($resultado);
    }

    public function frm_certificado() {
        $facturante = $this->input->post('facturante');
        $resultado = $this->Model_configuraciones->getInfoCrtFacturante($facturante);        
        $this->load->view('configuracion/frm_certificado', $resultado);
    }

    public function googleLogIn(){
        require_once APPPATH.'libraries/impresion/cloudprint/Config.php';
        $this->session->set_userdata('checkcloudprint', true);        
        header("Location: ".$urlconfig['authorization_url']."?".http_build_query($redirectConfig));
    }
    
    public function googleLogOut(){
        session_start();
        $this->session->set_userdata('accessToken', '');
        $this->session->set_userdata('checkcloudprint', false);
    }
    
    public function googleCallback(){
        $this->load->helper('url');
        require_once APPPATH.'libraries/impresion/cloudprint/Config.php';
        session_start();
        if(isset($_GET['code']) && !empty($_GET['code'])) {
            $code = $_GET['code'];
            $httpRequest = new HttpRequest($urlconfig['accesstoken_url']);
            $authConfig['code'] = $code;
            $httpRequest->setPostData($authConfig);
            $httpRequest->send();
            $response = $httpRequest->getResponse();
            $responseObj = json_decode($response);
            $accessToken = $responseObj->access_token;
            $this->session->set_userdata('accessToken', $accessToken); 
            echo '<script>window.close();window.opener.document.location.reload();</script>';         
        }       
    }
    
    public function actualizarPuntosVenta() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_facturantes", "", false, array('codigo_filial' => $filial["codigo"]));
        $data = $this->Model_facturantes->actualizarPuntosVenta();
        $this->load->model("Model_puntos_venta", "", false, array('codigo_filial' => $filial["codigo"]));
        $this->Model_puntos_venta->getPuntosVentas('habilitado');
        echo json_encode($data);
    }
    
    public function set_como_nos_conocio(){
        $id_conocio = $this->input->post("id_conocio");
        $resp = array();
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myFilial = new Vfiliales($conexion, $filial['codigo']);
        if ($myFilial->set_como_nos_conocio($id_conocio)){
            $resp['success'] = "success";
        } else {
            $resp['error'] = "Error al asignar Como nos conocio";
            $resp['dbg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($resp);
    }
    
    public function unset_como_nos_conocio(){
        $id_conocio = $this->input->post("id_conocio");
        $resp = array();
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $myFilial = new Vfiliales($conexion, $filial['codigo']);
        if ($myFilial->unset_como_nos_conocio($id_conocio)){
            $resp['success'] = "success";
        } else {
            $resp['error'] = "Error al desasignar como nos conocio";
            $resp['dbg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
        }
        echo json_encode($resp);
    }
    public function getEtiquetasBoleto(){
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        echo json_encode(Vconfiguracion::getValorConfiguracion($conexion, null, 'etiquetasBoleto'));
    }



    public function setEtiquetasBoleto(){
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $preferencias = isset($_POST['preferencias'])?$_POST['preferencias']:die("Faltan parametros");
        $usuario = $this->session->userdata('codigo_usuario');
        $nombre = 'etiquetasBoleto';
        $retornar = $this->Model_configuraciones->guardarConfiguracion($usuario, $nombre, $preferencias, $filial['codigo']);
        echo json_encode($retornar);;
    }

    public function guardarConfiguracionFacturacionSegmentada() {
        $monto_segmento = $this->input->post('monto_segmento');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('monto_segmento', lang('monto_segmento'), 'integer');

        $respuesta = '';
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta = array(
                'codigo'    => 0,
                'msgError'  => $errors
            );
        }else {
            $data_post['facturacion_nominada']      = (bool) $this->input->post('facturacion_nominada');
            $data_post['facturacion_segmentada']    = (bool) $this->input->post('facturacion_segmentada');
            $data_post['monto_segmento']            = $this->input->post('monto_segmento');
            
            $respuesta = $this->Model_configuraciones->guardarConfiguracionFacturacionSegmentada($data_post);
        }

        echo json_encode($respuesta);
    }
}
