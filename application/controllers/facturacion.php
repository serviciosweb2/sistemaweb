<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Facturacion extends CI_Controller {

    private $seccion;
    public $columnas = array();
    public $columnasfacturarserie = array();

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_facturas", "", false, $config);
        $this->load->helper("datatables");
    }

    private function ini_factura_serie() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $this->columnasfacturarserie = array(
            array("nombre" => lang('factserie_selectctacte'), "campo" => '', "sort" => FALSE, "sWidth" => "50px"),
            array("nombre" => lang('factserie_codigo'), "campo" => 'ctacte.codigo', "bVisible" => false),
            array("nombre" => lang('factserie_codigo'), "campo" => 'razones_sociales.razon_social', "bVisible" => false),
            array("nombre" => "cobro", "campo" => "cod_cobro"),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('descripcion'), "campo" => '', "sort" => FALSE, "sWidth" => "250px"),
            array("nombre" => lang('fecha_vencimiento'), "campo" => 'fechavenc'),
            array("nombre" => lang('factserie_importe'), "campo" => 'importe', "bVisible" => false),
            array("nombre" => lang("medio_pago"), "campo" => 'medio_pago', 'sort' => FALSE),
            array("nombre" => lang('importe'), "campo" => '', "sort" => FALSE),            
            array("nombre" => lang('detalle'), "campo" => '', "sort" => FALSE));
    }

    public function index($estado = null) {
        $seccion = $this->session->userdata('filial');
        $data['facturacion_electronica'] = $seccion['pais'] == 2;
        $data['columnasTabla'] = $this->crearColumnas();
        $data['page'] = 'facturacion/vista_facturacion'; // pasamos la vista a utilizar como parámetr
        $data['seccion'] = $this->seccion;
        $data['arrEstados'] = array(
            "-1" => "(" . lang("todos") . ")",
            Vfacturas::getEstadoHabilitado() => lang("CONFIRMADA"),
            Vfacturas::getEstadoInhabilitado() => lang("cancelada"),
            Vfacturas::getEstadoPendiente() => lang("pendiente"),
            Vfacturas::getEstadoError() => lang("error"),
            Vfacturas::getEstadoPendienteCancelar() => lang("pendiente_cancelar")
        );
        if ($estado == null) {
            $estado = -1;
        }
        //Ticket 4555 -mmori- modifico clave deshabilitar-factura por anular_cobro
        $claves = array("habilitar-factura", "anular_cobro", "codigo", "facturacion_estado", "facturacion_anular",
            "ANULADA", "CONFIRMADA", "pendiente", "debe_seleccionar_facturas_para_enviar", "BIEN", "mostrar", "registros",
            "ERROR", "facturas_enviadas_correctamente", "los_siguientes_destinatarios_han_fallado",
            "enviar_facturas_por_email", "descripcion", "importe", "iva", "informe_contable", "exportar",
            "respuesta", "deuda_pasiva", "no_posible_baja_factura", "baja_factura",
            "enviada", "error", "pendiente_cancelar", "reenviar_factura"
        );
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('facturacion');
        $data['columns'] = $this->getColumns();
        $data['estadoSeleccionar'] = $estado;
        $data['envio_factura_mail'] = $this->Model_facturas->puedeEnviarFacturasMail();
        $this->load->view('container', $data);
    }

    public function crearColumnas() {
        $htmlDef = '<label class="inline middle"><input class="ace id-toggle-all" type="checkbox">
                    <span class="lbl"></span></label>';
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo', "bVisible" => false),
            array("nombre" => $htmlDef, "campo" => '', "sort" => FALSE),
            array("nombre" => lang('fecha'), "campo" => 'fecha'),
            array("nombre" => lang('nro_factura'), "campo" => 'nrofact'),
            array("nombre" => str_replace(" ", "<br>", lang('punto_venta')), "campo" => 'punto_venta'),
            array("nombre" => lang('facturante'), "campo" => 'cod_facturante'),
            array("nombre" => lang('tipo_factura'), "campo" => 'factura'),
            array("nombre" => lang('razon_social'), "campo" => 'razon_social'),
            array("nombre" => lang('documento'), "campo" => 'documento'),
            array("nombre" => lang('total'), "campo" => 'total', "sort" => false),
            array("nombre" => lang('facturacion_estado'), "campo" => 'estado', "bVisible" => true, "sort" => FALSE));
        return $columnas;
    }

    public function getColumns() {
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    public function getColumnsFacturacionSerie() {
        $this->ini_factura_serie();
        $aoColumnDefs = json_encode(getColumnsDatatable($this->columnasfacturarserie));
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
        $estado = $this->input->post("estado") && $this->input->post("estado") <> -1 ? $this->input->post("estado") : null;
        $fechaDesde = $this->input->post("fecha_desde") && $this->input->post("fecha_desde") <> '' 
                ? formatearFecha_mysql($this->input->post("fecha_desde"))
                : null;
        $fechaHasta = $this->input->post("fecha_hasta") && $this->input->post("fecha_hasta") <> ''
                ? formatearFecha_mysql($this->input->post("fecha_hasta"))
                : null;
        $medio = $this->input->post("medio") ? $this->input->post("medio") : null;
        $tipoFactura = $this->input->post("tipo_factura") && $this->input->post("tipo_factura") <> -1 ? $this->input->post("tipo_factura") : null;
        $valores = $this->Model_facturas->listarFacturasDataTable($arrFiltros, $estado, $fechaDesde, $fechaHasta, $medio, $tipoFactura);
        echo json_encode($valores);
    }

    public function frm_facturar_cobrar() {
        $facturantes = array();
        $filial = $this->session->userdata('filial');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $this->load->model("Model_alumnos", "", false, $arrConFact);
        $this->load->model("Model_puntos_venta", "", false, $arrConFact);
        $mediosPago = $this->Model_paises->getMediosPagos(true, true, true);
        $facturantes = $this->Model_puntos_venta->getFacturantesFacturar($cod_usuario);
        $data['validaciones'] = '';
        if (count($facturantes) < 1) {
            $data['validaciones'] = validaciones::validarAusenciaFacturantes($cod_usuario);
        }
        $claves = array(
            "FACTURA_EMITIDA_CORRECTAMENTE", "BIEN", "ERROR", "debe_seleccionar_al_menos_un_items_de_cuenta_corriente",
            "fecha_de_factura_invalida", "debe_seleccionar_un_alumno", "no_se_seleccionado_razon_social", "cajas_habilitadas_para_este_medio",
            "indique_el_tipo_de_factura", "no_se_selecciono_un_facturante", "descripcion", "fecha", "importe",
            "seleccione_caja", "seleccione_tarjeta", "seleccione_banco", "seleccione_cheque", "seleccione_fecha",
            "escriba_numero_cheque", "escriba_emisor", "deuda_pasiva", "caja", "tipo", "banco", "fecha", "numero_cupon",
            "escriba_numero_cupon", "numero", "emisor", "medio_deposito_transaccion_factura", "medio_deposito_cuenta_factura",
            "la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100",
            "ha_seleccionado_tipos_de_datos_de_facturas_repetidos", "terminal", "seleccione_terminal", "escriba_codigo_cupon",
            "tipo_tarjeta", "tipo_tarjeta", "recuperando", "sin_registros", "codigo_cupon", "codigo_autorizacion", "escriba", "TARJETA","TDEBITO",
            "numero_transaccion", "nombre_de_cuenta", "caja_cerrada", "el_maximo_de_renglones_a_dacturar_no_debe_ser_mayor_a", "validacion_cpf_invalida"
        );
        $data['langFrm'] = getLang($claves);
        $data['mediosPago'] = $mediosPago;
        $data['facturantes'] = $facturantes;
        $data['moneda'] = $filial["moneda"];
        $data['fecha'] = date('Y-m-d');
        $data['filial_pais'] = $filial['pais'];
        $this->load->view('facturacion/frm_facturacion_cobro', $data);
    }

    public function frm_facturar() {
        $filial = $this->session->userdata('filial');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->load->model("Model_alumnos", "", false, $arrConFact);
        $this->load->model("Model_puntos_venta", "", false, $arrConFact);
        $facturantes = $this->Model_puntos_venta->getFacturantesFacturar($cod_usuario);
        $data['validaciones'] = '';
        if (count($facturantes) < 1) {
            $data['validaciones'] = validaciones::validarAusenciaFacturantes($cod_usuario);
        }
        $claves = array("titulo_menu_Interesados",
            "BIEN",
            "FACTURA_EMITIDA_CORRECTAMENTE",
            "ERROR",
            "descripcion_ctacte_facturar",
            "fecha_vencimiento",
            "deuda_pasiva",
            "saldo",
            "indique_el_tipo_de_factura",
            "ha_seleccionado_tipos_de_datos_de_facturas_repetidos",
            "la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100",
            "indique_el_tipo_de_factura",
            "el_maximo_de_renglones_a_dacturar_no_debe_ser_mayor_a");
        $data['langFrm'] = getLang($claves);
        $data['facturantes'] = $facturantes;
        $data['moneda'] = $filial["moneda"];
        $data['fecha'] = date('Y-m-d');
        $data['pais'] = $filial['pais'];
        $this->load->view('facturacion/frm_facturacion', $data);
    }

    public function frm_facturar_serie() {
        $filial = $this->session->userdata('filial');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->load->model("Model_puntos_venta", "", false, $arrConFact);
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $facturantes = $this->Model_puntos_venta->getFacturantesFacturar($cod_usuario);
        $data['validaciones'] = '';
        if (count($facturantes) < 1) {
            $data['validaciones'] = validaciones::validarAusenciaFacturantes($cod_usuario);
        }
        $claves = array(
            "linea_de_cuenta_corriente_es_requerido",
            "tipo_factura_es_requerido",
            "facturante_es_requerido", "indique_el_tipo_de_factura",
            "numero", "ha_seleccionado_tipos_de_datos_de_facturas_repetidos", "el_maximo_de_renglones_a_dacturar_no_debe_ser_mayor_a",
            "importe", "la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100"
        );
        $data['medios_pago'] = $this->Model_paises->getMediosPagos(true, true, true);
        $data['langFrm'] = getLang($claves);
        $data['facturantes'] = $facturantes;
        $data['columnsFacturacionSerie'] = $this->getColumnsFacturacionSerie();
        $fechaInicio = formatearFecha_pais(date("Y-m-01"));
        $fechaFin = formatearFecha_pais(date("Y-m-") . cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y")));
        $data['fecha_inicio'] = $fechaInicio;
        $data['fecha_fin'] = $fechaFin;
        $data['pais'] = $filial['pais'];
        $this->load->view('facturacion/frm_facturar_serie', $data);
    }

    public function getRazonesAlumno() {
        $filial = $this->session->userdata('filial');
        $cod_alumno = $this->input->post('cod_alumno');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_alumnos", "", false, $arrConFact);
        $razonesAlumno = $this->Model_alumnos->getRazonSocialAlumno($cod_alumno, $this->session->userdata('filial')['pais']);
        echo json_encode($razonesAlumno);
    }

    public function getCtaCteFacturaCobroAlumno() {
        $filial = $this->session->userdata('filial');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $cod_alumno = $this->input->post('cod_alumno');
        $this->load->model("Model_alumnos", "", false, $arrConFact);
        $ctacteAlu = $this->Model_alumnos->getCtaCteFacturaCobro($cod_alumno);
        echo json_encode($ctacteAlu);
    }

    public function guardarFacturaCobro() {
        $usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $data_post = array();
        $this->load->library('form_validation');
        $ctactecheck = $this->input->post('chk_ctacte_selected') ? $this->input->post('chk_ctacte_selected') : array();
        ksort($ctactecheck);
        $cod_medio = $this->input->post('medio_pago');
        $cod_caja = $this->input->post('medio-caja');
        $datos = array('cod_medio' => $cod_medio, 'caja' => $cod_caja);
        $this->form_validation->set_rules('fecha-factura', lang('fecha'), 'required|validarFechaFormato|validarFechaCobro[' . json_encode($datos) . ']');
        $this->form_validation->set_rules('alumnos', lang('Alumno'), 'required');
        $this->form_validation->set_rules('razones_sociales', lang('razon_social'), 'required');
        $this->form_validation->set_rules('tipo_factura', lang('tipo_factura'), 'required');
        $this->form_validation->set_rules('chk_ctacte_selected', lang('checkctacte_factura'), 'required');
        $this->form_validation->set_rules('total_general', lang('total_factura'), 'required|validarExpresionTotal|validarImporteFacturarCobrar[' . json_encode($ctactecheck) . ']|validarImporteMayorA[0]');
        $this->form_validation->set_rules('medio_pago', lang('medioPago_factura'), 'required');
        $this->form_validation->set_rules('medio-caja', lang('medio_caja_factura'), 'required|validarCobroCajaMedio[' . $cod_medio . ']');
        $this->form_validation->set_rules('facturante', lang(''), 'required');
        $medioPago = $this->input->post('medio_pago');
        $data_post['medioPago'] = array();
        switch ($medioPago) {
            case 3 :
                $this->form_validation->set_rules('medios_terminales', lang('seleccione_terminal'), 'required');
                $cod_terminal = $this->input->post('medios_terminales');
                $this->form_validation->set_rules('medio-tajeta-tipo', lang('seleccione_tarjeta'), 'required');
                $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'required');
                if ($cod_terminal != '') {
                    $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'validarCuponTerminal[' . $cod_terminal . ']');
                    $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'validarAutorizacionTerminal[' . $cod_terminal . ']');
                }
                $data_post['medioPago']['cod_terminal'] = $cod_terminal;
                $data_post['medioPago']['cod_tipo'] = $this->input->post('medio-tajeta-tipo');
                $data_post['medioPago']['cupon'] = $this->input->post('medio-tajeta-cupon');
                $data_post['medioPago']['cod_autorizacion'] = $this->input->post("medio_tarjeta_autorizacion");
                break;
            case 4:
                $this->form_validation->set_rules('medio-cheque-banco', lang('medio-cheque-banco-factura'), 'required');
                $this->form_validation->set_rules('medio-cheque-tipo', lang('medio-cheque-tipo-factura'), 'required');
                $this->form_validation->set_rules('medio-cheque-fecha', lang('fecha'), 'required');
                $this->form_validation->set_rules('medio-cheque-numero', lang('medio_cheque_numero_factura'), 'required|numeric');
                $this->form_validation->set_rules('medio-cheque-emisor', lang('medio_cheque_emisor_factura'), 'required');
                $data_post['medioPago']['cod_banco_emisor'] = $this->input->post('medio-cheque-banco');
                $data_post['medioPago']['tipo_cheque'] = $this->input->post('medio-cheque-tipo');
                $data_post['medioPago']['fecha_cobro'] = $this->input->post('medio-cheque-fecha');
                $data_post['medioPago']['nro_cheque'] = $this->input->post('medio-cheque-numero');
                $data_post['medioPago']['emisor'] = $this->input->post('medio-cheque-emisor');
                break;
            case 6:
                $this->form_validation->set_rules('medio-deposito-banco', lang('medio-deposito-banco-factura'), 'required');
                $this->form_validation->set_rules('medio-deposito-fecha', lang('fecha'), 'required');
                $this->form_validation->set_rules('medio-deposito-transaccion', lang('medio-deposito-transaccion-factura'), 'required|numeric');
                $this->form_validation->set_rules('medio-deposito-cuenta', lang('medio-deposito-cuenta-factura'), 'required');
                $data_post['medioPago']['cod_banco'] = $this->input->post('medio-deposito-banco');
                $data_post['medioPago']['fecha_hora'] = $this->input->post('medio-deposito-fecha');
                $data_post['medioPago']['nro_transaccion'] = $this->input->post('medio-deposito-transaccion');
                $data_post['medioPago']['cuenta_nombre'] = $this->input->post('medio-deposito-cuenta');
                break;
            case 7:
                $this->form_validation->set_rules('medio-tranferencia-banco', lang('medio-tranferencia-banco-factura'), 'required');
                $this->form_validation->set_rules('medio-tranferencia-fecha', lang('fecha'), 'required');
                $this->form_validation->set_rules('medio-tranferencia-nro-transaccion', lang('medio-tranferencia-nro-transaccion-factura'), 'required');
                $this->form_validation->set_rules('medio-tranferencia-cuenta', lang('medio-tranferencia-cuenta_factura'), 'required');
                $data_post['medioPago']['cod_banco'] = $this->input->post('medio-tranferencia-banco');
                $data_post['medioPago']['fecha_hora'] = $this->input->post('medio-tranferencia-fecha');
                $data_post['medioPago']['nro_transaccion'] = $this->input->post('medio-tranferencia-nro-transaccion');
                $data_post['medioPago']['cuenta_nombre'] = $this->input->post('medio-tranferencia-cuenta');
                break;
            case 8 :
                $this->form_validation->set_rules('medios_terminales', lang('seleccione_terminal'), 'required');
                $cod_terminal = $this->input->post('medios_terminales');
                $this->form_validation->set_rules('medio-tajeta-tipo', lang('seleccione_tarjeta'), 'required');
                $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'required');
                if ($cod_terminal != '') {
                    $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'validarCuponTerminal[' . $cod_terminal . ']');
                    $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'validarAutorizacionTerminal[' . $cod_terminal . ']');
                }
                $data_post['medioPago']['cod_terminal'] = $cod_terminal;
                $data_post['medioPago']['cod_tipo'] = $this->input->post('medio-tajeta-tipo');
                $data_post['medioPago']['cupon'] = $this->input->post('medio-tajeta-cupon');
                $data_post['medioPago']['cod_autorizacion'] = $this->input->post("medio_tarjeta_autorizacion");
                break;
        }
        $fecha_factura = formatearFecha_mysql(trim($this->input->post('fecha-factura')));
        $total = $this->input->post('total_general');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {
            $datos = explode('|', $this->input->post('facturante'));
            $data_post['facturar']['fecha-factura'] = $fecha_factura;
            $data_post['facturar']['alumno'] = $this->input->post("alumnos");
            $data_post['facturar']['razon_social'] = $this->input->post('razones_sociales');
            $data_post['facturar']['tipo_factura'] = $this->input->post('tipo_factura');
            $data_post['facturar']['total-general'] = $total;
            $data_post['facturar']['checkctacte'] = $ctactecheck;
            $data_post['facturar']['cod_usuario'] = $usuario;
            $data_post['facturar']['facturante'] = $datos[0];
            $data_post['facturar']['punto_venta'] = $this->input->post("puntos_venta");
            $data_post['facturar']['cod_alumno'] = $this->input->post('alumnos');
            $data_post['cobro']['medio_pago'] = $cod_medio;
            $data_post['cobro']['medio-caja'] = $cod_caja;
            $resultado = $this->Model_facturas->guardarFacturaCobro($data_post);
        }
        echo json_encode($resultado);
    }

    public function guardarFactura() {
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $data_post = array();
        $ctactecheck = $this->input->post('checkctacte') ? $this->input->post('checkctacte') : array();
        $jsctacte = json_encode($ctactecheck);
        $separador = $filial['moneda']['separadorDecimal'];
        $this->form_validation->set_rules('fecha-factura', lang('fecha'), 'required|validarFechaFormato|validarfecha'); //ver despues si es necesario validarfecha
        $this->form_validation->set_rules('alumnos', lang('Alumno'), 'required');
        $this->form_validation->set_rules('razones_sociales', lang('razon_social'), 'required');
        $this->form_validation->set_rules('tipo_factura', lang('tipo_factura'), 'required');
        $this->form_validation->set_rules('checkctacte[]', lang('checkctacte_factura'), 'required|validarSaldoFacturar');
        $this->form_validation->set_rules('facturante', lang(''), 'required');
        $this->form_validation->set_rules('total_facturar', lang('total'), 'validarExpresionTotal|validarImporteFacturar[' . $jsctacte . ']');
        $retornoMensaje = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else if (!validaciones::validarRazonSocialFacturar(array($ctactecheck[0]), $retornoMensaje)){
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $retornoMensaje,
                'errNo' => '',
            );
        } else {
            $datos = explode('|', $this->input->post('facturante'));
            $fecha_factura = formatearFecha_mysql(trim($this->input->post('fecha-factura')));
            $data_post['fecha'] = $fecha_factura;
            $data_post['alumno'] = $this->input->post("alumnos");
            $data_post['codrazsoc'] = $this->input->post('razones_sociales');
            $data_post['tipofact'] = $this->input->post('tipo_factura');
            $data_post['cod_usuario'] = $usuario;
            $data_post['facturante'] = $datos[0];
            $data_post['puntos_venta'] = $this->input->post("puntos_venta");
            $total = $this->input->post('total_facturar');
            $totalFacturar = str_replace($separador, '.', $total);
            $data_post['total_facturar'] = $totalFacturar;
            $data_post['checkctacte'] = $ctactecheck;
            $resultado = $this->Model_facturas->guardarFactura($data_post);
        }
        echo json_encode($resultado);
    }

    public function getCajas() {
        $filial = $this->session->userdata('filial');
        $arrConFact = array('filial' => $filial['codigo']);
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->model("Model_usuario", "", false, $arrConFact);
        $cajas = $this->Model_usuario->getCajas($usuario, '0', '1');
        echo json_encode($cajas);
    }

    public function getBancos() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial['codigo']);
        $bancos = $this->Model_bancos->listarBancos($filial['pais']);
        echo json_encode($bancos);
    }

    public function getTiposTarjeta() {
        $filial = $this->session->userdata('filial');
        $arrConf = array('pais' => $filial['pais']);
        $this->load->model("Model_tipos_tarjetas", "", false, $arrConf);
        $tarjetas = $this->Model_tipos_tarjetas->getTipos();
        echo json_encode($tarjetas);
    }

    public function getTiposCheque() {
        $tiposcheque = Vmedio_cheques::getTipos();
        echo json_encode($tiposcheque);
    }

    public function frm_baja() {
        $this->load->library('form_validation');
        $cod_factura = $this->input->post('cod_factura');
        $this->form_validation->set_rules('cod_factura', lang('codigo'), 'numeric|validarCambioEstado');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
            echo json_encode($resultado);
        } else {
            $motivos = $this->Model_facturas->getMotivosBaja();
            $data['cod_factura'] = $cod_factura;
            $data['cobro'] = $this->Model_facturas->getCobroAsociadoAnular($cod_factura, $this->session->userdata('codigo_usuario'));
            $data['motivos'] = $motivos;
            $data['codigo'] = 1;
            $this->load->view('facturacion/frm_baja', $data);
        }
    }

    public function alta() {
        $codUsuario = $this->session->userdata('codigo_usuario');
        $cod_factura = $this->input->post('cod_factura');
        $cambioestado = array(
            'cod_factura' => $cod_factura,
            'cod_usuario' => $codUsuario
        );
        $factCambioEstado = $this->Model_facturas->altaFactura($cambioestado);
        echo json_encode($factCambioEstado);
    }

    public function baja() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_factura', lang('codigo'), 'numeric');
        $cobro = $this->input->post('cobro_check') == false ? false : true;
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $cambioestado = array(
                'cod_factura' => $this->input->post('cod_factura'),
                'motivo' => $this->input->post('motivo'),
                'comentario' => $this->input->post('comentario'),
                'cod_usuario' => $this->session->userdata('codigo_usuario'),
                'cobro' => $cobro
            );
            $factCambioEstado = $this->Model_facturas->bajaFactura($cambioestado);
            echo json_encode($factCambioEstado);
        }
    }

    public function getRenglonesDescripcion() {
        $cod_factura = $this->input->post('cod_factura');
        $facRenglonDesc = $this->Model_facturas->getRenglonesDescripcion($cod_factura);
        echo json_encode($facRenglonDesc);
    }

    public function getCtaCteFacturarAlumno() {
        $this->load->helper("alumnos");        
        $filial = $this->session->userdata('filial');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $cod_alumno = $this->input->post('cod_alumno');
        $this->load->model("Model_alumnos", "", false, $arrConFact);
        $arrCondicion = array(
            'habilitado >' => 0,
            'habilitado <' => 3
        );     
        $orden = array(
            'campo' => 'fechavenc',
            'orden' => 'asc'
        );
        $separador = $filial['nombreFormato']['separadorNombre'];        
        /* Se agrega parametro para que solo se pueda listar ctacte cobradas - TICKET 4536 
         variacion del ticket anterior por TICKET 4645 (solo para brasil) */
        $pais = $filial['pais'];
        $soloCobradas = $pais == 2;
        $ctacteAlu = $this->Model_alumnos->getCtaCteSinFacturar($cod_alumno, $arrCondicion, $orden, $separador, $soloCobradas);
        echo json_encode($ctacteAlu);
    }

    public function getCtaCteFacturar() {
        $this->ini_factura_serie();
        $ctacte = array();
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_ctacte", "", false, $arrConFact);
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $this->columnasfacturarserie[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $arrFiltros["FechaIni"] = isset($_POST['fecha-inicio']) ? $_POST['fecha-inicio'] : "";
        $arrFiltros["FechaFin"] = isset($_POST['fecha-fin']) ? $_POST['fecha-fin'] : "";
        $arrFiltros["tipoFactura"] = $this->input->post("tipo_factura") ? $this->input->post("tipo_factura") : "";
        $arrFiltros['cobradas_nofacturadas'] = $this->input->post('cobradas_nofacturadas');
        $arrFiltros['medio_pago'] = isset($_POST['medio_pago']) && $_POST['medio_pago'] <> -1 && $_POST['medio_pago'] <> ''
                ? $_POST['medio_pago'] : null;
        $factptoventa = $this->input->post("facturante") ? $this->input->post("facturante") : "";
        if ($factptoventa != '') {
            $datos = explode('|', $factptoventa);
            $facturante = $datos[0];
        } else {
            $facturante = null;
        }
        $arrFiltros["facturante"] = $facturante;
        $ctacte = $this->Model_ctacte->getCtaCteSinFacturar($arrFiltros, $separador);
        echo json_encode($ctacte);
    }

    public function guardarFacturaSerie() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $this->load->helper('alumnos');
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $data_post = array();
        $this->form_validation->set_rules('tipo-factura', lang('tipofact_factura'), 'required');
        $this->form_validation->set_rules('ctacteID', lang('checkctacte_factura'), 'required');
        $ctactecheck = $this->input->post('ctacteID') ? $this->input->post('ctacteID') : array();
        $retornoMensaje = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else if (!validaciones::validarRazonSocialFacturar($ctactecheck, $retornoMensaje)){
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $retornoMensaje,
                'errNo' => '',
            );
        } else {
            $datos = explode('|', $this->input->post('facturante-serie'));
            $data_post['tipofact'] = $this->input->post('tipo-factura');
            $data_post['facturante'] = $datos[0];
            $data_post['punto_venta'] = $this->input->post("puntos_venta");
            $data_post['cod_usuario'] = $usuario;
            $data_post['checkctacte'] = $ctactecheck;
            $resultado = $this->Model_facturas->guardarFacturasSerie($data_post, $separador);
        }
        echo json_encode($resultado);
    }

    public function getTiposFacturaFacturante() {
        $filial = $this->session->userdata('filial');
        $codPais = $filial['pais'];
        $datos = explode('|', $this->input->post('cod_facturante'));
        $facturante = $datos[0];
        $usuario = $this->session->userdata('codigo_usuario');
        if ($this->input->post("cod_razon_social")) {
            $razonsocialalu = $this->input->post('cod_razon_social');
            $facturasTipos = $this->Model_facturas->getTiposFacturaHabilitadas($facturante, $razonsocialalu, null, $usuario, $codPais, true);
        } else {
            $facturasTipos = $this->Model_facturas->getTiposFacturasFacturante($facturante, null, $usuario, $codPais, true);
        }
        echo json_encode($facturasTipos);
    }

    public function getListadoFacturacion() {
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $tipoFactura = isset($_POST['tipo_factura']) ? $_POST['tipo_factura'] : null;
        $anulada = isset($_POST['anulada']) ? $_POST['anulada'] : null;
        $facturaDesde = isset($_POST['factura_desde']) ? $_POST['factura_desde'] : null;
        $facturaHasta = isset($_POST['factura_hasta']) ? $_POST['factura_hasta'] : null;
        $idCtaCte = isset($_POST['id_ctacte']) ? $_POST['id_ctacte'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $arrResp = $this->Model_facturas->getListadoFacturacion($idFilial, $arrLimit, $arrSort, $search, $searchField, $fechaDesde, $fechaHasta, $tipoFactura, $anulada, $facturaDesde, $facturaHasta, $idCtaCte);
        echo json_encode($arrResp);
    }

    public function getTiposFacturas($idFilial) {
        $arrResp = $this->Model_facturas->getTiposFacturas($idFilial);
        echo json_encode($arrResp);
    }

    public function getRenglones($idFilial, $idFactura) {
        $arrResp = $this->Model_facturas->getRenglones($idFilial, $idFactura);
        echo json_encode($arrResp);
    }

    public function getSaldo() {
        $filial = $this->session->userdata('filial');
        $arrConFact = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_ctacte", "", false, $arrConFact);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_ctacte_selected', lang('checkctacte_factura'), 'required');
        $resultado = array();
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado['codigo'] = '0';
            $resultado['msgerror'] = $errors;
            $resultado['errNo'] = '';
        } else {
            $ctactecheck = $this->input->post("chk_ctacte_selected");
            $saldo = $this->Model_ctacte->getSaldoFacturacion("facturarcobrar", $ctactecheck);
            $resultado['codigo'] = '1';
            $resultado['saldo_facturacion'] = $saldo;
        }
        echo json_encode($resultado);
    }

    public function validarDatosCobrar() {
        $this->load->library('email');
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial['codigo']);
        $resultado = array();
        $this->load->library('form_validation');
        $this->load->model("Model_ctacte", "", false, $arrConf);
        $ctactecheck = $this->input->post('chk_ctacte_selected') ? $this->input->post('chk_ctacte_selected') : array();
        $this->form_validation->set_rules('fecha-factura', lang('fecha'), 'required|validarFechaFormato|validarFechaPeriodoCerrado');
        $this->form_validation->set_rules('alumnos', lang('Alumno'), 'required');
        $this->form_validation->set_rules('razones_sociales', lang('razon_social_factura'), 'required');
        $this->form_validation->set_rules('tipo_factura', lang('tipofact_factura'), 'required');
        $this->form_validation->set_rules('chk_ctacte_selected', lang('checkctacte_factura'), 'required');
        $this->form_validation->set_rules('total_general', lang('total_factura'), 'validarExpresionTotal|validarImporteFacturarCobrar[' . json_encode($ctactecheck) . ']');
        $this->form_validation->set_rules('facturante', lang(''), 'required');
        $retornoMensaje = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado['codigo'] = '0';
            $resultado['msgerror'] = $errors;
            $resultado['errNo'] = '';
        } else if (!validaciones::validarRazonSocialFacturar(array($ctactecheck[0]), $retornoMensaje)){
            $resultado['codigo'] = '0';
            $resultado['msgerror'] = $retornoMensaje;
            $resultado['errNo'] = '';            
        } else {
            $resultado['codigo'] = '1';
            $resultado['saldo_facturacion'] = $this->input->post("total_general") == 0 ? $this->Model_ctacte->getSaldoFacturacion("facturarcobrar", $ctactecheck) : $this->input->post('total_general');
        }
        echo json_encode($resultado);
    }

    function enviar_por_mail_confirmar() {
        $data = array();
        $data['arrFacturasEnviar'] = $this->Model_facturas->getFacturas($this->input->post("facturas"));
        $this->load->view('facturacion/vista_confirmacion_envio_facturas.php', $data);
    }

    function enviar_facturas() {
        $this->load->library('email');
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->load->helper("cuentacorriente");
        $arrResp = array();
        $arrSendError = array();
        if ($this->Model_facturas->enviar_facturas($this->Model_facturas->input->post("facturas"), $arrSendError)) {
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "Error al enviar las facturas";
            $arrResp['failue'] = $arrSendError;
        }
        echo json_encode($arrResp);
    }

    function preguntar_imprimir_facturacion_cobro() {
        $filial = $this->session->userdata('filial');
        $codFilial = $filial["codigo"];
        $arrConf = array('codigo_filial' => $codFilial);
        $this->load->model("Model_impresiones", "", false, $arrConf);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $this->lang->load(get_idioma(), get_idioma());
        $idScriptImpresion = 11;
        $arrImpresoras = $this->Model_impresiones->getImpresorasUtilizadas($codFilial);
        $arrResp = $this->Model_impresiones->getMetodoImprimirScript($codFilial, $idScriptImpresion);
        if ($arrResp['metodo'] == "no_imprimir") {
            $printerID = -2;
        } else {
            $printerID = $this->Model_impresiones->getPrinterScript($codFilial, $idScriptImpresion);
        }
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, $idScriptImpresion);
        $cantidadCopias = isset($arrConfiguracion['copias']) ? $arrConfiguracion['copias'] : 1;
        $data = array();
        $data['printer_default_facturas'] = $printerID;
        $data['arrImpresoras'] = $arrImpresoras;
        $data['id_script_inicio_facturas'] = $idScriptImpresion;
        $data['cantidad_copias_facturas'] = $cantidadCopias;
        $data['factura'] = $this->input->post("factura");
        $data['cobro'] = $this->input->post("cobro");
        $idScriptImpresion = 10;
        $arrImpresoras = $this->Model_impresiones->getImpresorasUtilizadas($codFilial);
        $arrConfiguracion = $this->Model_configuraciones->getValorConfiguracion(20, null, $idScriptImpresion);
        $arrResp = $this->Model_impresiones->getMetodoImprimirScript($codFilial, $idScriptImpresion);
        if ($arrResp['metodo'] == "no_imprimir") {
            $printerID = -2;
        } else {
            $printerID = $this->Model_impresiones->getPrinterScript($codFilial, $idScriptImpresion);
        }
        $cantidadCopias = isset($arrConfiguracion['copias']) ? $arrConfiguracion['copias'] : 1;
        $data['printer_default_cobros'] = $printerID;
        $data['id_script_inicio_cobros'] = $idScriptImpresion;
        $data['cantidad_copias_cobros'] = $cantidadCopias;
        $this->load->view('facturacion/preguntar_imprimir_facturacion_cobro.php', $data);
    }

    function getDetalleFactura() {
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->helper("cuentacorriente");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigoFactura', lang(''), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $arrDescripcion = $this->Model_facturas->getRenglonesDescripcion($this->input->post("codigoFactura"));
            echo json_encode($arrDescripcion);
        }
    }

    function habilitar_factura() {
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_factura', lang(''), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $arrResp = $this->Model_facturas->habilitar_factura($this->input->post("cod_factura"));
            echo json_encode($arrResp);
        }
    }

    public function get_terminales() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_medio_tarjetas", "", false, $filial);
        $arrTerminales = $this->Model_medio_tarjetas->getTerminales(true, true);
        echo json_encode($arrTerminales);
    }

    public function get_tipo_tarjetas() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_tipos_tarjetas", "", false, $filial);
        $arrTipos = $this->Model_tipos_tarjetas->getTiposTarjetas();
        echo json_encode($arrTipos);
    }

    public function get_tarjetas() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_tipos_tarjetas", "", false, $filial);
        $terminal = $this->input->post("terminal");
        $arrTarjetas = $this->Model_tipos_tarjetas->getTiposTarjetasTerminal($terminal, $filial['codigo']);
        echo json_encode($arrTarjetas);
    }
    public function get_tarjetas_debito() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_tipos_tarjetas", "", false, $filial);
        $terminal = $this->input->post("terminal");
        $arrTarjetas = $this->Model_tipos_tarjetas->getTiposDebitoTerminal($terminal, $filial['codigo']);
        echo json_encode($arrTarjetas);
    }
    public function getCajasCobrar() {
        $filial = $this->session->userdata('filial');
        $config = array('codigo_filial' => $filial['codigo']);
        $usuario = $this->session->userdata('codigo_usuario');
        $cod_medio = $this->input->post("cod_medio");
        $this->load->model("Model_caja", "", false, $config);
        $cajas = $this->Model_caja->getCajasMedio($usuario, $cod_medio, 0);
        echo json_encode($cajas);
    }

    public function calcularTotal() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $arrValores = $this->input->post('importes') ? $this->input->post('importes') : array();
        $retornar = array();
        $monedaSimbolo = $filial['moneda']['simbolo'];
        foreach ($arrValores as $key => $valor) {
            $_POST['Valorctacte' . $key] = strlen($valor) > 0 ? str_replace($monedaSimbolo, '', $valor) : $valor;
            $this->form_validation->set_rules('Valorctacte' . $key, 'input' . $key, 'validarExpresionTotal');
        }
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            $retornar = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $retornar['codigo'] = 1;
            $retornar['total'] = $this->Model_facturas->calcularTotal($arrValores);
        }
        echo json_encode($retornar);
    }
    
    public function exportar(){
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_impresiones", "", false, $config);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->load->helper("cuentacorriente");
        $conexion = $this->load->database($filial['codigo'], true);
        $dir = sys_get_temp_dir();
        $directorioPDF = $dir. "/";
        $directorioXML = $dir. "/";
        $fileXML = array();
        $filePDF = array();
        foreach ($_POST['factura_exportar'] as $factura){
            $myFactura = new Vfacturas($conexion, $factura);
            $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
            $condiciones = array("cod_punto_venta" => $myFactura->punto_venta);
            $xml = '';
            if ($myPuntoVenta->tipo_factura == 15){
                $arrPrestadores = Vprestador_toolsnfe::listarPrestador_toolsnfe($conexion, $condiciones);
                $myTools = new Vprestador_toolsnfe($conexion, $arrPrestadores[0]['codigo']);
                $arrSeguimiento = Vseguimiento_toolsnfe::listarSeguimiento_toolsnfe($conexion, array("cod_factura" => $factura, "cod_filial" => $filial['codigo'], "estado" => "habilitada"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
                $nfe = $arrSeguimiento[0]['nfe'];
                $xml = $myTools->getXMLFacturaAprobadaRevision($conexion, $factura, $filial['codigo']);
            } else {
                $myFilial = new Vfiliales($conexion, $filial["codigo"]);
                $metodos_facturacion = $myFilial->getMetodoFacturacion();
                switch ($metodos_facturacion[0]['facturacion_servicios']) {
                    case 'ginfes':
                        $arrPrestadores = Vprestador_ginfes::listarPrestador_ginfes($conexion, $condiciones);
                        $myTools = new Vprestador_ginfes($conexion, $arrPrestadores[0]['codigo']);
                        $arrSeguimiento = Vseguimiento_ginfes::listarSeguimiento_ginfes($conexion, array("cod_factura" => $factura, "cod_filial" => $filial['codigo'], "estado" => "enviado"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
                        $nfe = $arrSeguimiento[0]['numero_lote'];
                        $xml = $myTools->getXMLFacturaAprobada($conexion, $factura, $filial['codigo']);
                        break;

                    case 'paulistana':
                        $arrPrestadores = Vprestador_paulistana::listarPrestador_paulistana($conexion, $condiciones);
                        $myTools = new Vprestador_paulistana($conexion, $arrPrestadores[0]['codigo']);
                        $arrSeguimiento = Vseguimiento_paulistana::listarSeguimiento_paulistana($conexion, array("cod_factura" => $factura, "cod_filial" => $filial['codigo'], "estado" => "habilitada"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
                        $nfe = $arrSeguimiento[0]['numero_lote'];
                        $xml = $myTools->getXMLFacturaAprobada($arrSeguimiento[0], $myTools);
                        
                        break;

                    case 'dsf':
                        $arrPrestadores = Vprestador_dsf::listarPrestador_dsf($conexion, $condiciones);
                        $myTools = new Vprestador_dsf($conexion, $arrPrestadores[0]['codigo']);
                        $xml = $myTools->getXMLFacturaAprobada($conexion, $factura);
                        break;
                }
            }
            file_put_contents($directorioXML.$nfe.".xml", $xml);
            $fileXML[] = $directorioXML.$nfe.".xml";
            $pdf = $this->Model_impresiones->getPDFFacturas($conexion, array($factura), 1, false, null);
            $pdf->output($directorioPDF.$nfe.".pdf", "F");
            $filePDF[] = $directorioPDF.$nfe.".pdf";
        }
        $nombreZip = $dir."/informe_".date("YmdHis")."_".$filial['codigo'].".zip";
        $zip = new ZipArchive();
        if ($zip->open($nombreZip, ZIPARCHIVE::CREATE)==TRUE) {
            foreach ($fileXML as $file){
                $info = pathinfo($file);
                $nombreArchivo = $info['basename'];
                $zip->addFile($file, "XML/$nombreArchivo");
            }
            foreach ($filePDF as $file){
                $info = pathinfo($file);
                $nombreArchivo = $info['basename'];
                $zip->addFile($file, "PDF/$nombreArchivo");
            }
            $zip -> close();
            $size = filesize($nombreZip);
            $info = pathinfo($nombreZip);
            $nombreArchivo = $info['basename'];
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=$nombreArchivo");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $size);
            readfile($nombreZip);
        }
    }
}