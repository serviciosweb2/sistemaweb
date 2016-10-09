<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Caja extends CI_Controller {

    public $columnas = array();

    public function __construct() {
        parent::__construct();

        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);

        $this->load->model("Model_caja", "", false, $config);
        /* CARGO EL LAG */
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->helper("datatables");
        $this->columnas = array(
            array("nombre" => lang('fechahora_caja_cabecera'), "campo" => 'fecha_hora'),
            array("nombre" => lang('descripcion_caja_cabecera'), "campo" => 'descripcion', 'sort' => false),
            array("nombre" => lang('entrada_caja_cabecera'), "campo" => 'haber'),
            array("nombre" => lang('salida_caja_cabecera'), "campo" => 'debe'),
            array("nombre" => lang('medio_de_pago'), "campo" => 'medio'),
            array("nombre" => lang('usuario'), "campo" => 'user_name')
        );
    }

    public function index() {
        $validar_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        
		$data = array();
        $data['session'] = $this->session->all_userdata(); // PASO LOS VALORES DE LA SESSION
        $data['helper'] = 'caja';
        $data['seccion'] = $validar_session;
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $this->load->model("Model_caja", "", false, $config);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $cajasTotales = $this->Model_usuario->getCajas($codUsuario, '0', null, true);
        $data['cajas'] = $this->Model_usuario->getCajas($codUsuario, '0');
        $todacajasabiertas = $this->Model_caja->getCajasAbiertas();
        $caja_default = -1;
        $caja_seleccionada = -1;
        $cajas_abiertas = 0;
        foreach ($data['cajas'] as $caja) {
            if ($caja['estado'] == "abierta") {
                $cajas_abiertas ++;
                $caja_seleccionada = $caja['codigo'];
                if ($caja['default'] == 1) {
                    $caja_default = $caja['codigo'];
                }
            }
        }
        $claves = array("BIEN", "ERROR", "cerrada", "caja_cerrada", "APERTURA", "saldo");
        $data['lang'] = getLang($claves);
        $data['columns'] = $this->getColumns();
        $data['cajas_abiertas'] = count($todacajasabiertas);
        $data['caja_seleccionar'] = $caja_default == -1 ? $caja_seleccionada : $caja_default;
        $data['cajas_totales'] = $cajasTotales;
        $data['permiso_nuevo_movimiento'] = permisoSlug("caja", "frm_nuevo_movimiento");
        $data['permiso_transferencia'] = permisoSlug("caja", "frm_transferencia_cajas");
        $data['permiso_cerrar_caja'] = permisoSlug("caja", "frm_cerrar_caja");
        $data['permiso_abrir_caja'] = permisoSlug("caja", "frm_abrir_caja");

        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'caja/vista_caja_1'; // pasamos la vista a utilizar como parámetro
        $this->load->view('container', $data);
    }

    public function getColumns(){
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->columnas));
        return $aoColumnDefs;
    }

    public function listar(){
        session_method();
        $this->load->helper("caja");
        $arrFiltros["cod_caja"] = $this->input->post('codigo_caja');
        $arrFiltros['limitApertura'] = true;
        $arrFiltros['fechaDesde'] = false;
        $arrFiltros['fechaHasta'] = false;
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $this->columnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $arrFiltros['estado_caja'] = isset($_POST['estado_caja']) ? $_POST['estado_caja'] : '';
        $arrResp = $this->Model_caja->listarMovimientosDatatable($arrFiltros, true);
        $arrResp['saldos'] = $this->Model_caja->get_saldo_por_medios($this->input->post('codigo_caja'), true);
        $arrResp['caja'] = $this->Model_caja->getCaja($this->input->post('codigo_caja'));
        echo json_encode($arrResp);
    }

    public function getCajasUsuario($cajasAbiertas = null) {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $data['cajas'] = $this->Model_usuario->getCajas($codUsuario, 0, $cajasAbiertas);
        $data['cod_usuario'] = $codUsuario;
        echo json_encode($data);
    }

    /* ESTA FUNCTION ESTA SIENDO ACCEDIDA DESDE UN WEB SERVICES, no modificar, eliminar o comentar */

    public function getCajas($idFilial) {
        session_method();
        $configAlumnos = array("codigo_filial" => $idFilial);
        $this->load->model("Model_caja", "", false, $configAlumnos);
        $arrResp = $this->Model_caja->getCajas($idFilial);
        $configAlumnos = array("codigo_filial" => $idFilial);
        $this->load->model("Model_caja", "", false, $configAlumnos);
        echo json_encode($arrResp);
    }

    /* ESTA FUNCTION ESTA SIENDO ACCEDIDA DESDE UN WEB SERVICES, no modificar, eliminar o comentar */

    public function getMediosPagoWS($idPais) {
        session_method();
        $this->load->model("Model_paises", "", false, $idPais);
        $arrResp = $this->Model_paises->getMediosPagoWS(true);
        echo json_encode($arrResp);
    }

    public function getMediosPago($idPais) {
        session_method();
        $this->load->model("Model_paises", "", false, $idPais);
        $arrResp = $this->Model_paises->getMediosPagos(true);
        echo json_encode($arrResp);
    }

    public function frm_abrir_caja() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $data['cajas'] = $this->Model_usuario->getCajas($codUsuario, 0, 0);
        $data['cod_usuario'] = $codUsuario;
        $claves = array(
            "caja_abierta_correctamente"
        );
        $data['langFrm'] = getLang($claves);
        if ($this->input->post("ejecutar_script"))
            $data['ejecutar_script'] = $this->input->post("ejecutar_script");
        $this->load->view('caja/frm_abrir_caja', $data);
    }

    public function get_saldos_de_cierre() {
        session_method();
        $this->load->helper("filial");
        $codigoCaja = $this->input->post("codigo_caja");
        $arrResp = $this->Model_caja->get_saldos_de_cierre($codigoCaja);
        echo json_encode($arrResp);
    }

    public function abrirCaja() {
        session_method();
        $abrir = array();
        $codUsuario = $this->session->userdata('codigo_usuario');
        $filial = $this->session->userdata('filial');
        $config = array("filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $codCaja = $this->input->post('codigo');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo', lang(''), 'required');
        $resultado = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $abrir['cod_caja'] = $codCaja;
            $abrir['usuario'] = $codUsuario;
            $resultado = $this->Model_caja->abrirCaja($abrir);
        }
        echo json_encode($resultado);
    }

    public function frm_nuevo_movimiento() {
        session_method();

        $filial = $this->session->userdata('filial');
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $config = array("filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $data = array();
        $data['medios_pago'] = $this->Model_caja->getMediosCaja($this->input->post("codigo_caja"), 0);

        $conexion = $this->load->database($filial['codigo'], true);
        $data['rubros'] = Vrubros_caja::getRubros($conexion);

        $data['cod_usuario'] = $codUsuario;
        $data['codigo_caja'] = $this->input->post("codigo_caja");
        $data['nombre_caja'] = $this->input->post("nombre_caja");
        $data['fecha'] = formatearFecha_pais(date("Y-m-d H:i:s"), true);
        $data['hora'] = date("H:i:s");
        $ultimoMovimiento = $this->Model_caja->getUltimoMovimiento($data['codigo_caja'], Vmovimientos_caja::getConceptoApertura());
        $fecha = isset($ultimoMovimiento[0]['fecha_hora']) && $ultimoMovimiento[0]['fecha_hora'] <> '' ? substr($ultimoMovimiento[0]['fecha_hora'], 0, 10) : date("Y-m-d");
        $hora = isset($ultimoMovimiento[0]['fecha_hora']) && $ultimoMovimiento[0]['fecha_hora'] <> '' ? substr($ultimoMovimiento[0]['fecha_hora'], 11) : date("H:i:s");
        $horaTemp = explode(":", $hora);
        $fechaTemp = explode("-", $fecha);
        $data['min_date_dia'] = $fechaTemp[2];
        $data['min_date_mes'] = $fechaTemp[1];
        $data['min_date_anio'] = $fechaTemp[0];
        $data['min_date_hora'] = $horaTemp[0];
        $data['min_date_min'] = $horaTemp[1];
        $data['min_date_seg'] = $horaTemp[2];
        $data['separador_decimal'] = $separadorDecimal;
        $claves = Array(
            "debe_ingresar_un_importe_valido",
            "el_importe_debe_ser_mayor_a_cero",
            "debe_especificar_una_descripcion_para_el_nuevo_movimiento_de_caja",
            "movimiento_guardado_correctamente",
            "seleccione_opcion",
            "template_error_titulo",
            "template_error_vacio_descripcion",
            "subrubro"
        );
        $data['langFrm'] = getLang($claves);
        $this->load->view('caja/frm_nuevo_movimiento', $data);
    }

    public function frm_cerrar_caja() {
        session_method();
        $filial = $this->session->userdata('filial');
        $config = array("filial" => $filial['codigo']);
        $claves = array(
            "importe_ingresado_no_valido_para_el_medio",
            "caja_cerrada_correctamente"
        );

        $data['langFrm'] = getLang($claves);
        $this->load->helper("caja");
        $this->load->model("Model_usuario", "", false, $config);
        $data['cajas'] = $this->Model_caja->get_saldo_por_medios($this->input->post("codigo_caja"), true);

        $data['codigo_caja'] = $this->input->post("codigo_caja");
        $this->load->view('caja/frm_cerrar_caja', $data);
    }

    public function cerrarCaja() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $config = array("filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $resultado = '';
        $cerrar = array();
        $cerrar['codigo_usuario'] = $this->session->userdata('codigo_usuario');
        $cerrar['codigo_caja'] = $this->input->post('codigo_caja');
        $valores_medio = $this->input->post('valores_medios');
        foreach ($valores_medio as $key => $valor_medio) {
            $_POST['saldo' . $key] = $valor_medio['saldo_debe'];
            $this->form_validation->set_rules('saldo' . $key, lang('importe'), 'validarExpresionTotal');
        }
        if ($this->form_validation->run() == false) {
            $error = validation_errors();
            $resultado = array(
                "codigo" => 0,
                "msgError" => $error
            );
        } else {
            foreach ($this->input->post("valores_medios") as $valores) {
                $cerrar["valores"][$valores['codigo_medio']]['debe'] = str_replace(',', '.', $valores['saldo_debe']);
            }
            $resultado = $this->Model_caja->cerrarCaja($cerrar);
        }

        echo json_encode($resultado);
    }

    public function guardarNuevoMovimiento() {
        session_method();
        $ultimoMovimientoApertura = $this->Model_caja->getUltimoMovimiento($this->input->post('codigo_caja'), Vmovimientos_caja::getConceptoApertura());
        $fechaHoraApertura = $ultimoMovimientoApertura[0]['fecha_hora'];
        $fechaHoraActual = date("Y-m-d H:i:s");
        $codUsuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo_caja', lang(''), 'required|validarCajaUsuario[' . $codUsuario . ']');
        $this->form_validation->set_rules('codigo_medio', lang(''), 'required');
        $this->form_validation->set_rules('importe', lang(''), 'required|validarExpresionTotal');
        $this->form_validation->set_rules('tipo_movimiento', lang(''), 'required');
        $this->form_validation->set_rules('fecha', lang(''), "validarFechaFormato|validarIntervaloDeFechas[$fechaHoraApertura,$fechaHoraActual]");
        $this->form_validation->set_rules('observacion', lang(''), 'required');
        if ($this->input->post("tipo_movimiento") == 'salida'){
            $this->form_validation->set_rules('subrubro', lang(''), 'required');
        }
        $resultado = '';
        if ($this->form_validation->run() == FALSE ) { // ver porque subrubro viene como cero
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $data_post['cod_caja'] = $this->input->post('codigo_caja');
            $data_post['importe'] = str_replace(',', '.', $this->input->post('importe'));
            $data_post['cod_medio'] = $this->input->post('codigo_medio');
            $fechaHora = formatearFecha_mysql($this->input->post("fecha")) . " {$this->input->post("hora")}";
            $data_post['fecha'] = $fechaHora;
            $data_post['movimiento'] = $this->input->post('tipo_movimiento');
            //mmori - agrego rubro-subrubro
            $data_post['subrubro'] = $this->input->post('subrubro');
            $data_post['observacion'] = $this->input->post('observacion');
            if($this->input->post('subrubro') != 0)
            {
                $data_post['observacion'] .= "-" . $this->input->post('subrubroNombre');
            }

            $data_post['usuario'] = $codUsuario;

            $resultado = $this->Model_caja->guardarMovimiento($data_post);
        }
        echo json_encode($resultado);
    }

    public function getSubRubros()
    {
        session_method();
        $filial = $this->session->userdata('filial');
        $rubro = $this->input->post("rubro");

        $conexion = $this->load->database($filial['codigo'], true);
        $subrubros = Vrubros_caja::getSubRubros($conexion, $rubro);
        $resp = array();
        foreach($subrubros as $subrubro)
        {
            $resp[] = array("nombre"=>lang($subrubro['subrubro']),"codigo"=>$subrubro['codigo']);
        }
        function compareByName($a, $b) {
            return strcmp($a["nombre"], $b["nombre"]);
        }

        usort($resp, 'compareByName');


        echo json_encode($resp);
    }

    public function updateSubRubros(){
        session_method();
        $filial = $this->session->userdata('filial');

        $codMovCaja = $this->input->post("cod_mov_ca");
        $nuevoSubRub = $this->input->post("nuevo_sub");
        $conexion = $this->load->database($filial['codigo'], true);

        $okay = Vmovimientos_caja::updateConcepto($conexion, $codMovCaja, $nuevoSubRub);

        echo json_encode($okay);
    }

    public function frm_transferencia_cajas() {
        session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $filial = $this->session->userdata('filial');
        $config = array("filial" => $filial['codigo']);
        $this->load->model("Model_usuario", "", false, $config);
        $this->load->model("Model_caja", "", false, $config);
        $codUsuario = $this->session->userdata('codigo_usuario');

        $claves = array(
            "la_caja_de_origen_debe_diferir_de_la_caja_destino",
            "debe_ingresar_un_importe_valido",
            "el_importe_debe_ser_mayor_a_cero",
            "debe_especificar_la_descripcion_para_la_transferencia",
            "transferencia_realizada_correctamente"
        );

        $data['langFrm'] = getLang($claves);

        $data['cajas_destino'] = $this->Model_caja->getCajasAbiertas();
        $data['cajas_origen'] = $this->Model_usuario->getCajas($codUsuario, 0, 1);
        $data['caja_salida'] = $this->input->post("caja_salida");
        $data['medios_pago'] = $this->Model_caja->getMediosCaja($this->input->post("caja_salida"), 0);
        $this->load->view("caja/frm_transferencia_cajas", $data);
    }

    public function guardar_transferencia() {
        session_method();
        $codUsuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('caja_origen', lang(''), "required|validarCajaUsuario[$codUsuario]");
        $this->form_validation->set_rules('caja_destino', lang(''), "required"); //|validarCajaUsuario[$codUsuario]");
        $this->form_validation->set_rules("importe", lang(''), "required");
        $this->form_validation->set_rules("descripcion", lang(''), "required");
        $this->form_validation->set_rules("medio_pago", lang(''), "required");
        $resultado = array();

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $codUsuario = $this->session->userdata('codigo_usuario');
            $cajaOrigen = $this->input->post("caja_origen");
            $cajaDestino = $this->input->post("caja_destino");
            $descripcion = $this->input->post("descripcion");
            $importe = $this->input->post("importe");
            $medioPago = $this->input->post("medio_pago");
            $resultado = $this->Model_caja->guardarTransferencia($cajaOrigen, $cajaDestino, $descripcion, $importe, $medioPago, $codUsuario);
        }
        echo json_encode($resultado);
    }

    public function guardarCaja() {
        session_method();
        $resultado = '';
        $this->load->library('form_validation');
        $cod_caja = $this->input->post('codigo');
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myCaja = new Vcaja($conexion, $cod_caja);
        $this->form_validation->set_rules('nombre', lang('nombre'), 'required');
        $this->form_validation->set_rules('form-usuario-checkbox', lang('seleccione_un_usuario'), 'required');
        if ($myCaja->estado == 'cerrada'){
            $this->form_validation->set_rules('medio_habilitado', lang("seleccion_de_medio"), 'required');
        }
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $data_post['cod_caja'] = $cod_caja;
            $data_post['nombre_caja'] = $this->input->post('nombre');
            $data_post['medios']['medio'] = $this->input->post('medio_habilitado');
            $data_post['medios']['entsal'] = $this->input->post('medio_entsal');
            $data_post['medios']['confir'] = $this->input->post('medio_confir_auto');
            $data_post['habilitado'] = $this->input->post('habilitado');
            $data_post['usuarios_caja'] = $this->input->post('form-usuario-checkbox');
            $data_post['cod_moneda'] = $this->input->post("cod_moneda");
            $resultado = $this->Model_caja->guardarCaja($data_post);
        }
        echo json_encode($resultado);
    }

    /* La siguiente function está siendo accedida desde un Web Services NO MODIFICAR, BORRAR O COMENTAR */
    public function getMovimientosCaja() {
        session_method();
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $codCaja = isset($_POST['cod_caja']) ? $_POST['cod_caja'] : null;
        $codUser = isset($_POST['cod_user']) ? $_POST['cod_user'] : null;
        $medioPago = isset($_POST['medio_pago']) ? $_POST['medio_pago'] : null;
        $configAlumnos = array("codigo_filial" => $idFilial);
        $this->load->model("Model_caja", "", false, $configAlumnos);
        $arrResp = $this->Model_caja->getMovimientosCaja($idFilial, $arrLimit, $arrSort, $search, $searchField, $fechaDesde, $fechaHasta, $codCaja, $codUser, $medioPago);
        echo json_encode($arrResp);
    }

    /**
     * La siguiente fnuction está siendo accedida desde un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR
     *
     * recupera el saldo de las cajas por medio
     * acepta como parametros (optativos) codigo_caja y codigo_medio
     */
    function getSaldoCajas(){
        $arrResp = array();
        $codFilial = $this->input->post("cod_filial");
        $conexion = $this->load->database($codFilial, true);
        $condiciones = array();
        $condiciones['desactivada'] = 0;
        if ($this->input->post("codigo_caja")){
            $condiciones['codigo'] = $this->input->post("codigo_caja");
        }
        $arrCajas = Vcaja::listarCaja($conexion, $condiciones);
        foreach ($arrCajas as $key => $caja){
            $myCaja = new Vcaja($conexion, $caja['codigo']);
            $arrResp[$key]['codigo'] = $myCaja->getCodigo();
            $arrResp[$key]['nombre_caja'] = $myCaja->nombre;
            $myCotizacion = new Vcotizaciones($conexion, $myCaja->cod_moneda);
            $arrResp[$key]['moneda']['codigo'] = $myCotizacion->getCodigo();
            $arrResp[$key]['moneda']['simbolo'] = $myCotizacion->simbolo;
            $arrResp[$key]['moneda']['moneda'] = $myCotizacion->moneda;
            if ($this->input->post("codigo_medio")){
                $arrMedios = Vmedios_pago::listarMedios_pago($conexion, array("codigo" => $this->input->post("codigo_medio")));
            } else {
                $arrMedios = $myCaja->getMediosCaja();
            }
            foreach ($arrMedios as $keyMedio => $medio){
                $codigoMedio = $medio['codigo'];
                $nombreMedio = lang($medio['medio']);
                $saldo = $myCaja->getUltimoSaldo($codigoMedio);
                $arrResp[$key]['medios'][$keyMedio]['codigo'] = $codigoMedio;
                $arrResp[$key]['medios'][$keyMedio]['nombre'] = $nombreMedio;
                $arrResp[$key]['medios'][$keyMedio]['saldo'] = $saldo;
            }
        }
        echo json_encode($arrResp);
    }

    function editar_movimiento(){
        $codMovimiento = $this->input->post("cod_movimiento_caja");
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myMovimientoCaja = new Vmovimientos_caja($conexion, $codMovimiento);
        $myCaja = new Vcaja($conexion, $myMovimientoCaja->cod_caja);
        $myMedioPago = new Vmedios_pago($conexion, $myMovimientoCaja->cod_medio);
        $data['myMovimientoCaja'] = $myMovimientoCaja;
        $data['myCaja'] = $myCaja;
        $data['myMedioPago'] = $myMedioPago;
        $claves = array(
            "debe_especificar_el_importe_del_movimiento_de_caja"
        );
        $data['langFrm'] = getLang($claves);
        $this->load->view("caja/vista_modificar_importe_caja", $data);
    }

    function modificar_importe_movimiento_caja(){
        $arrResp = array();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('importe', lang('importe'), 'required');
        $this->form_validation->set_rules('cod_movimiento_caja', lang('codigo'), 'required');
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            $arrResp['codigo'] = 0;
            $arrResp['error'] = $errors;
        } else {
            $filial = $this->session->userdata('filial');
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $myMovimientoCaja = new Vmovimientos_caja($conexion, $this->input->post("cod_movimiento_caja"));
            if ($myMovimientoCaja->cod_concepto != 'PARTICULARES'){
                $arrResp['codigo'] = 0;
                $arrResp['error'] = lang('solo_se_pueden_editar_movimientos_particulares');
            } else {
                if ($myMovimientoCaja->actualizar_importe($this->input->post("importe"))){
                    $conexion->trans_commit();
                    $arrResp['codigo'] = 1;
                    $arrResp["success"] = "successs";
                } else {
                    $conexion->trans_rollback();
                    $arrResp['codigo'] = 0;
                    $arrResp['error'] = "Error al actualizar los movimientos de caja";
                    $arrResp['dbg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                }
            }
        }
        echo json_encode($arrResp);
    }

}
