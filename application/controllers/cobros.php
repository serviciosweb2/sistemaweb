<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class Cobros extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configCobros = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cobros", "", false, $configCobros);
    }

    public function index() {
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $data = array();
        $data['moneda_simbolo'] = $filial['moneda']['simbolo'];
        $data['separador_decimal'] = $filial['moneda']['separadorDecimal'];
        $data['titulo_pagina'] = '';
        $data['page'] = 'cobros/vista_cobros';
        $data['seccion'] = $this->seccion;
        $data['columnas_boleto'] = $this->getColumnsBoleto();
        $data['menuJson'] = getMenuJson('cobros');
        $myFilial = new Vfiliales($conexion, $filial['codigo']);
        $arrContratosTarjetas = $myFilial->getContratosTarjetas();
        $data['contratos_tarjeta'] = count($arrContratosTarjetas) > 0;
        $data['arrEstados'] = Vcobros::getEstados();
        $data['arrMedios'] = Vmedios_pago::listarMedios_pago($conexion, null, null, null, null, null, $filial['pais']);
        $data['arrCajas'] = Vcaja::listarCaja($conexion);
        $data['arrMeses'] = getMeses();
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('codigo_cobro'), "campo" => 'codigo'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('documento'), "campo" => "documento_completo"),
            array("nombre" => lang('importe'), "campo" => 'importe'),
            array("nombre" => lang('imputado'), "campo" => 'total_imputado'),
            array("nombre" => lang('saldo'), "campo" => 'saldoRestante'),
            array("nombre" => lang('medio_pago'), "campo" => 'medio'),
            array("nombre" => lang('caja'), "campo" => 'caja'),
            array("nombre" => lang('fecha'), "campo" => 'fechareal'),
            array("nombre" => lang('periodo'), "campo" => 'periodo'),
            array("nombre" => lang('estado_cobro'), "campo" => 'estado', 'bVisible' => false),
            array("nombre" => lang('baja_cobro'), "campo" => 'baja', "sort" => FALSE));
        return $columnas;
    }

    private function crearColumnasBoleto() {
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => lang('nombre'), "campo" => 'nombre_apellido'),
            array("nombre" => lang('fecha_venc'), "campo" => 'fechavenc'),
            array("nombre" => lang('importe'), "campo" => 'importe'));
        return $columnas;
    }

    public function getColumnsBoleto() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnasBoleto()));
        return $aoColumnDefs;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        echo $aoColumnDefs;
    }

    /* ESTA FUNCTION TIENE AGREGADOS YA QUE PUEDE SER ACCEDIDA DESDE UN WEB SERVICES */
    public function listar() {
        $filial = $this->session->userdata('filial');
        if (isset($filial['nombreFormato'])) {
            $separador = $filial['nombreFormato']['separadorNombre'];
            $separadorDecimal = $filial['moneda']['separadorDecimal'];
        } else {                    // SE accede desde un WS y no se tiene session
            $separador = ", ";
            $separadorDecimal = ",";
        }        
        $idFilial = $this->input->post("id_filial") ? $this->input->post("id_filial") : null;
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) && $_POST['iDisplayLength'] <> -1 ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";        
        if (isset($_POST['fecha_desde'])){
            $arrFiltros["fecha_desde"] = $_POST['fecha_desde'];
        } else if (isset($_POST['fecha_desde_t']) && $_POST['fecha_desde_t'] != ''){
            $arrFiltros['fecha_desde'] = formatearFecha_mysql($_POST['fecha_desde_t']);
        } else {
            $arrFiltros["fecha_desde"] = null;
        }        
        if (isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] != ''){
            $arrFiltros['fecha_hasta'] = $_POST['fecha_hasta'];
        } else if (isset($_POST['fecha_hasta_t']) && $_POST['fecha_desde_t'] != ''){
            $arrFiltros['fecha_hasta'] = formatearFecha_mysql($_POST['fecha_hasta_t']);
        } else {
            $arrFiltros['fecha_hasta'] = null;
        }        
        $arrFiltros['saldo'] = isset($_POST['saldo']) && $_POST['saldo'] <> -1 ? $_POST['saldo'] : null;
        $arrFiltros['caja'] = isset($_POST['caja']) && $_POST['caja'] <> -1 ? $_POST['caja'] : null;
        $arrFiltros["medio_pago"] = isset($_POST['medio_pago']) && $_POST['medio_pago'] <> -1 ? $_POST['medio_pago'] : null;
        $arrFiltros["selectEstado"] = isset($_POST['selectEstado']) && $_POST['selectEstado'] <> -1 ? $_POST['selectEstado'] : null;
        $arrFiltros['periodo_mes'] = isset($_POST['periodo_mes']) && $_POST['periodo_mes'] <> '' ? $_POST['periodo_mes'] : null;
        $arrFiltros['periodo_anio'] = isset($_POST['periodo_anio']) && $_POST['periodo_anio'] <> '' ? $_POST['periodo_anio'] : null;
        $valores = $this->Model_cobros->listarCobrosDataTable($arrFiltros, $separador, $separadorDecimal, $idFilial);
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $this->load->helper('alumnos');
            $nombreApellido = formatearNombreColumnaAlumno();
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            foreach ($valores['aaData'] as $valor) {                
                $arrTemp[] = array(
                    $valor[0],                  // codigo
                    $valor[1],                  // nombre  
                    $valor[2],                  // documento
                    $valor[13],                 // importe
                    $valor[14],                 // imputado                    
                    $valor[15],                 // saldo
                    $valor[6],                  // medio
                    $valor[7],                  // caja
                    $valor[8],                  // fechareal
                    $valor[9],                  // periodo                    
                    $valor[11]);                // baja
                $total1 += $valor[13];
                $total2 += $valor[14];
                $total3 += $valor[15];
            }
            if ($_POST['tipo_reporte'] == 'csv'){
                $arrTemp[] = array("", "", "", $total1, $total2, $total3, "", "", "", "", "");
            }
            $arrTitle = array(
                lang('codigo'),
                $nombreApellido,
                lang('documento'),
                lang('importe'),
                lang('imputado'),
                lang('saldo'),
                lang('medio_pago'),
                lang('caja'),
                lang('fecha'),
                lang('periodo'),
                lang('baja_cobro'),
            );
            
            $arrWidth = array(15, 70, 26, 20, 18, 18, 28, 28, 20, 18, 18);
            $periodo = '';
            if (isset($_POST['fecha_desde']) && $_POST['fecha_desde'] != ''){
                $periodo .= lang("desde")." ".$_POST['fecha_desde'];
            }
            if (isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] != ''){
                $periodo .= lang("al")." ".$_POST['fecha_hasta'];
            }
            if ($periodo == ''){
                $periodo = lang("todas_las_fechas");
            }
            $usuario = $this->session->userdata("nombre");
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang("periodo").": ".$periodo, "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 286, "height" => 4)                
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrTemp);
            $exp->setPDFFontSize(8);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("reporte_de_cobros"));
            $exp->setMargin(10, 10);
            $exp->setContentAcumulable(array('3','4','5'));
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }

    public function getDetallesCobro() {
        $this->load->library('form_validation');
        $cod_cobro = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $detalles['imputacionesCobro'] = $this->Model_cobros->getImputacionesCobro($cod_cobro);
            $detalles['errores'] = $this->Model_cobros->getErroresCobro($cod_cobro);
            $detalles['historico'] = $this->Model_cobros->getHistoricoCobro($cod_cobro);
            echo json_encode($detalles);
        }
    }

    public function getCtaCteImputar() {
        $filial = $this->session->userdata('filial');
        $configCobros = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $configCobros);
        $cod_alumno = $this->input->post('cod_alumno');
        $ctaCteSinImutar = $this->Model_alumnos->getCtaCteCobro($cod_alumno);
        echo json_encode($ctaCteSinImutar);
    }

    public function frm_cobros() {
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        if ($codigo != '') {
            $this->form_validation->set_rules('codigo', lang('codigo'), 'validarPeriodoCobro');
            $validacion = $this->form_validation->run();
        } else {
            $validacion = true;
        }
        if ($validacion == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $filial = $this->session->userdata('filial');
            $arrConfig = array('codigo_filial' => $filial['codigo']);
            $data['cobro_imputaciones'] = '';
            $data['ctacte_imputar'] = '';
            $data['medio_cobro'] = '';
            $validar_session = $validar_session = session_method();
            $this->load->model("Model_paises", "", false, $filial["pais"]);
            $this->load->model("Model_alumnos", "", false, $arrConfig);
            $mediosPago = $this->Model_paises->getMediosPagos(true, true, true);
            $data['mediosPago'] = $mediosPago;
            $data['moneda'] = $filial["moneda"];
            $data['codigo'] = $codigo;
            $data['permite_editar_medios'] = true;
            if ($data['codigo'] != '') {
                $cobro = $this->Model_cobros->getCobro($data['codigo']);
                $data['cobro'] = $cobro;
                $data['medio_cobro'] = json_encode($this->Model_cobros->getMedioCobro($data['codigo']));
                $separador = $filial['moneda']['separadorDecimal'];
                $total = str_replace('.', $separador, $cobro->importe);
                $data['total_cobro'] = $total;
                $data['alumno'] = array('codigo' => $cobro->cod_alumno, 'nombre' => $this->Model_alumnos->getNombreAlumno($cobro->cod_alumno));
                $data['permite_editar_medios'] = $cobro->estado <> Vcobros::getEstadoConfirmado();
                $data['ctacte_imputar'] = json_encode($this->Model_alumnos->getCtaCteCobro($cobro->cod_alumno));
            }
            $this->load->view('cobros/frm_cobros', $data);
        }
    }

    public function getRazonesAlumno() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_alumnos", "", false, $arrConfig);
        $cod_alumno = $this->input->post('codigo');
        $razonAlumno = $this->Model_alumnos->getRazonSocialAlumno($cod_alumno);
        echo json_encode($razonAlumno);
    }

    public function guardarCobro() {
        $filial = $this->session->userdata('filial');
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $this->load->helper('formatearfecha');
        $resultado = '';
        $separador = $filial['moneda']['separadorDecimal'];
        $codigo = $this->input->post('codigo');
        $ctactecheck = $this->input->post('checkctacte') ? $this->input->post('checkctacte') : array();
        $cod_medio = $this->input->post('medio_cobro');
        $cod_caja = $this->input->post('caja');
        $offline = $this->input->post('offline') ? true : false;
        $data_post = array();
        $data_post['cobrar']['estado'] = '1';
        $datos = array('cod_medio' => $cod_medio, 'caja' => $cod_caja);
        $this->form_validation->set_rules('fecha_cobro', lang('fecha_cobro'), 'required|validarFechaCobro[' . json_encode($datos) . ']');
        $this->form_validation->set_rules('checkctacte', lang('checkctacte_cobro'), 'required');
        if ($codigo == '') {
            $this->form_validation->set_rules('alumnos', lang('alumnos_cobro'), 'required');
        } else {
            $this->form_validation->set_rules('codigo', lang('cobro'), 'required|validarModificarCobro|validarPeriodoCobro');
        }
        $this->form_validation->set_rules('medio_cobro', lang('medio_cobro'), 'required');
        $this->form_validation->set_rules('caja', lang('medio_caja_cobro'), 'required|validarCobroCajaMedio[' . $cod_medio . ']|validarCajaUsuario[' . $usuario . ']');
        $this->form_validation->set_rules('total_cobrar', lang('total_cobrar'), 'required|validarExpresionTotal|validarImporteCobro[' . $codigo . ']');
        $data_post['medio_cobro'] = array();
        switch ($cod_medio) {

            case 3:
            case 8:
                $cod_terminal = $this->input->post('pos_tarjeta');
                $this->form_validation->set_rules('tarjetas', lang('medio_tarjeta_tipo_cobro'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'required');
                $this->form_validation->set_rules('pos_tarjeta', lang('terminal'), 'required');
                $this->form_validation->set_rules('medio_tarjeta_cupon', lang('medio_tarjeta_cupon_cobro'), 'required');
                if ($cod_terminal != '') {
                    $this->form_validation->set_rules('medio-tajeta-cupon', lang('codigo_cupon'), 'validarCuponTerminal[' . $cod_terminal . ']');
                    $this->form_validation->set_rules('medio_tarjeta_autorizacion', lang('codigo_autorizacion'), 'validarAutorizacionTerminal[' . $cod_terminal . ']');
                }
                break;

            case 4:
                $this->form_validation->set_rules('medio_cheque_banco', lang('medio_cheque_banco_cobro'), 'required');
                $this->form_validation->set_rules('medio_cheque_tipo', lang('medio_cheque_tipo_cobro'), 'required');
                $this->form_validation->set_rules('medio_cheque_fecha', lang('medio_cheque_fecha_cobro'), 'required|validarFechaFormato');
                $this->form_validation->set_rules('medio_cheque_numero', lang('medio_cheque_numero_cobro'), 'required|numeric');
                $this->form_validation->set_rules('medio_cheque_emisor', lang('medio_cheque_emisor_cobro'), 'required');
                break;

            case 6:
                $this->form_validation->set_rules('medio_deposito_banco', lang('medio_deposito_banco_cobro'), 'required');
                $this->form_validation->set_rules('medio_deposito_fecha', lang('medio_deposito_fecha_cobro'), 'required|validarFechaFormato');
                $this->form_validation->set_rules('medio_deposito_transaccion', lang('medio_deposito_transaccion_cobro'), 'required|numeric');
                $this->form_validation->set_rules('medio_deposito_cuenta', lang('medio_deposito_cuenta_cobro'), 'required');
                break;

            case 7:
                $this->form_validation->set_rules('medio_transferencia_banco', lang('medio_transferencia_banco_cobro'), 'required');
                $this->form_validation->set_rules('medio_transferencia_fecha', lang('medio_transferencia_fecha_cobro'), 'required|validarFechaFormato');
                $this->form_validation->set_rules('medio_transferencia_numero', lang('medio_transferencia_numero_cobro'), 'required|numeric');
                $this->form_validation->set_rules('medio_transferencia_cuenta', lang('medio_transferencia_cuenta_cobro'), 'required');
                break;



        }
        $respuesta = $this->form_validation->run();
        if ($respuesta == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
            $data_post['cobrar']['estado'] = '0';
            $data_post['cobrar']['errores'] = $errors;
        }
        if ($respuesta != false || ($respuesta == false && $offline)) {

            switch ($cod_medio) {
                case 3:
                case 8:
                    $data_post['medio_cobro']['cod_tipo'] = $this->input->post('tarjetas');
                    $data_post['medio_cobro']['cod_autorizacion'] = $this->input->post('medio_tarjeta_autorizacion');
                    $data_post['medio_cobro']['cupon'] = $this->input->post('medio_tarjeta_cupon');
                    $data_post['medio_cobro']['cod_terminal'] = $cod_terminal;
                    break;

                case 4:
                    $data_post['medio_cobro']['cod_banco_emisor'] = $this->input->post('medio_cheque_banco');
                    $data_post['medio_cobro']['banco_cheque'] = $this->input->post('medio_cheque_banco');
                    $data_post['medio_cobro']['tipo_cheque'] = $this->input->post('medio_cheque_tipo');
                    $data_post['medio_cobro']['emisor'] = $this->input->post('medio_cheque_emisor');
                    $data_post['medio_cobro']['fecha_cobro'] = formatearFecha_mysql($this->input->post('medio_cheque_fecha'));
                    $data_post['medio_cobro']['nro_cheque'] = $this->input->post('medio_cheque_numero');
                    break;

                case 6:
                    $data_post['medio_cobro']['cod_banco'] = $this->input->post('medio_deposito_banco');
                    $data_post['medio_cobro']['fecha_hora'] = formatearFecha_mysql($this->input->post('medio_deposito_fecha'));
                    $data_post['medio_cobro']['nro_transaccion'] = $this->input->post('medio_deposito_transaccion');
                    $data_post['medio_cobro']['cuenta_nombre'] = $this->input->post('medio_deposito_cuenta');
                    break;

                case 7:
                    $data_post['medio_cobro']['cod_banco'] = $this->input->post('medio_transferencia_banco');
                    $data_post['medio_cobro']['fecha_hora'] = formatearFecha_mysql($this->input->post('medio_transferencia_fecha'));
                    $data_post['medio_cobro']['cuenta_nombre'] = $this->input->post('medio_transferencia_cuenta');
                    $data_post['medio_cobro']['nro_transaccion'] = $this->input->post('medio_transferencia_numero');
                    break;


            }

            $total = $this->input->post('total_cobrar');
            $totalCobrar = str_replace($separador, '.', $total);
            $data_post['cobrar']['codigo'] = $codigo != '' ? $codigo : -1;
            $data_post['cobrar']['fecha_cobro'] = formatearFecha_mysql($this->input->post('fecha_cobro'));
            $data_post['cobrar']['cod_alumno'] = $this->input->post('alumnos');
            $data_post['cobrar']['razones_sociales'] = $this->input->post('razones_sociales');
            $data_post['cobrar']['checkctacte'] = $ctactecheck;
            $data_post['cobrar']['total_cobrar'] = $totalCobrar;
            $data_post['cobrar']['medio_cobro'] = $cod_medio;
            $data_post['cobrar']['cod_usuario'] = $usuario;
            $data_post['cobrar']['caja'] = $cod_caja;
            $resultado = $this->Model_cobros->guardarCobro($data_post);
        }
        echo json_encode($resultado);
    }

    public function getCajas() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array('filial' => $filial['codigo']);
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->model("Model_usuario", "", false, $arrConfig);
        $cajas = $this->Model_usuario->getCajas($usuario, '0');
        echo json_encode($cajas);
    }

    public function getBancos() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial['codigo']);
        $bancos = $this->Model_bancos->listarBancos($filial['pais']);
        echo json_encode($bancos);
    }

    public function getTiposTarjetas() {
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

    public function getDetallesMedio() {
        $cod_cobro = $this->input->post('cod_cobro');
        $detalleCobro = $this->Model_cobros->getDetallesMedio($cod_cobro);
        echo json_encode($detalleCobro);
    }

    public function frm_baja() {
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $this->form_validation->set_rules('cod_cobro', lang('codigo'), 'numeric|validarAnularCobro[' . $usuario . ']');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $claves = array("motivo");
            $data['frm_baja_lang'] = getLang($claves);
            $this->load->helper("filial");
            $cod_cobro = $this->input->post('cod_cobro');
            $motivos = $this->Model_cobros->getMotivosBaja();
            $data['cod_cobro'] = $cod_cobro;
            $objCobro = $this->Model_cobros->getCobro($cod_cobro);
            $data['facturas_asociadas'] = $objCobro->getFacturasAsociadas(true);
            $data['objCobro'] = $objCobro;
            $data['alumnoformateado'] = $this->Model_cobros->getnombreAlumno($cod_cobro);
            $data['movitos'] = $motivos;
            $this->load->view('cobros/frm_baja', $data);
        }
    }

    public function cambiarEstado() {
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $this->form_validation->set_rules('cod_cobro', lang('codigo'), 'numeric|validarAnularCobro[' . $usuario . ']');
        $this->form_validation->set_rules('motivo', lang('motivo'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $codUsuario = $this->session->userdata('codigo_usuario');
            $cod_cobro = $this->input->post('cod_cobro');
            $motivo = $this->input->post('motivo');
            $comentarios = $this->input->post('comentario');
            $cambioEstado = array(
                'cod_cobro' => $cod_cobro,
                'motivo' => $motivo,
                'comentario' => $comentarios,
                'cod_usuario' => $codUsuario
            );
            if ($this->input->post("facturas_anuladas")){
                $cambioEstado['facturas_anuladas'] = $this->input->post("facturas_anuladas");
            }
            $cobroCabioEstado = $this->Model_cobros->cambioEstadoCobro($cambioEstado);
            echo json_encode($cobroCabioEstado);
        }
    }

    public function frm_imputaciones() {
        $this->load->library('form_validation');
        $cod_cobro = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $imputacionesCobro = $this->Model_cobros->getImputacionesCobro($cod_cobro);
            $ctacteImputar = $this->Model_cobros->getCtaImputar($cod_cobro);
            $detalle = $this->Model_cobros->getReferenciaImputaciones($cod_cobro);
            $mostrarImputaciones = $this->Model_cobros->mostrarFrmImputaciones($cod_cobro);
            if ($mostrarImputaciones == 1) {
                $data['codigo'] = 1;
                $data['detalleReferencia'] = $detalle;
                $data['alumnoformateado'] = $this->Model_cobros->getnombreAlumno($cod_cobro);
                $data['imputacionesCobro'] = $imputacionesCobro;
                $data['ctacteImputar'] = $ctacteImputar;
                $this->load->view('cobros/frm_imputaciones', $data);
            } else {
                echo lang('frm_imputaciones_error');
            }
        }
    }

    /* la siguiente fnuction esta siendo accedida desde un web services */

    public function cobros_mensuales() {
        $arrPeriodos = $this->input->post("periodos");
        if (!is_array($arrPeriodos)) {
            $arrPeriodos = null;
        }
        $idFilial = $this->input->post("id_filial");
        $arrResp = $this->Model_cobros->getCobrosMensuales($idFilial, $arrPeriodos);
        echo json_encode($arrResp);
    }

    public function getSaldo() {
        $cod_cobro = $this->input->post('codigo');
        $saldoFavor = $this->Model_cobros->getSaldo($cod_cobro);
        print_r($saldoFavor);
    }

    function getImputacionesCtaCte() {
        $idFilial = $_POST['id_filial'];
        $idCtaCte = $_POST['id_ctacte'];
        $config = array("codigo_filial" => $idFilial);
        $this->load->model("Model_ctacte", "", false, $config);
        $arrResp = $this->Model_ctacte->getImputacionesCtaCte($idFilial, $idCtaCte);
        echo json_encode($arrResp);
    }

    public function calcularTotal() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $arrValores = $this->input->post('importes') ? $this->input->post('importes') : array();
        $retornar = '';
        $monedaSimbolo = $filial['moneda']['simbolo'];
        foreach ($arrValores as $key => $valor) {
            $_POST['Valorctacte' . $key] = strlen($valor) > 0 ? str_replace($monedaSimbolo, '', $valor) : $valor;
            $this->form_validation->set_rules('Valorctacte' . $key, 'input' . $key, 'validarExpresionTotal');
        }
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $retornar = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $retornar['codigo'] = 1;
            $retornar['total'] = $this->Model_cobros->calcularTotal($arrValores);
        }
        echo json_encode($retornar);
    }

    public function actualizarSaldo() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $this->load->helper('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $retornar = '';
        $this->load->model("Model_configuraciones", "", false, $config);
        $cod_cobro = $this->input->post('cod_cobro');
        $saldoCtaCte = $this->input->post('valorImputar');
        foreach ($saldoCtaCte as $key => $valor) {
            $_POST['Valorctacte' . $key] = strlen($valor) > 0 ? str_replace($valor[0], '', $valor) : $valor;
            $this->form_validation->set_rules('Valorctacte' . $key, 'input' . $key, 'validarExpresionTotal');
        }
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $retornar = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $total = 0;
            foreach ($saldoCtaCte as $key => $valor) {
                $valor = desformatearImporte($valor);
                $total = $total + $valor;
            }
            $saldo = $this->Model_cobros->getSaldo($cod_cobro);
            $saldoCta = desformatearImporte($saldo);
            $saldoTotal = $saldoCta - $total;
            if ($saldoTotal < 0) {
                $retornar['codigo'] = 0;
                $retornar['msgerror'] = lang('actualizar_saldo');
            } else {
                $retornar['codigo'] = 1;
                $retornar['saldo'] = formatearImporte($saldoTotal);
                $retornar['total'] = formatearImporte($total);
            }
            echo json_encode($retornar);
        }
    }

    public function getTotalImputaciones() {
        $cod_cobro = $this->input->post('cod_cobro');
        $totImputaciones = $this->Model_cobros->getTotalImputaciones($cod_cobro);
        echo json_encode($totImputaciones);
    }

    public function getPendientesCtacte() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_ctacte", "", false, $config);
        $crearColumnas = $this->crearColumnasBoleto();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        echo json_encode($this->Model_ctacte->getCtacteSinCobrarDatatable($arrFiltros));
    }

    public function generarBoletosBacarios() {
        $cod_ctactes = $this->input->post('ctacte');
        $usuario = $this->session->userdata('codigo_usuario');
        $cod_banco = $this->input->post('cod_banco');
        $cod_cuenta = $this->input->post('cod_cuenta');
        $convenio = $this->input->post('convenio');
        $boleto = $this->Model_cobros->generarBoletoBancario($cod_ctactes, $usuario, $cod_banco, $cod_cuenta, $convenio);
        if ($boleto) {
            echo "<pre>";
            print_r($boleto);
            echo "</pre>";
        } else {
            echo "error al generar boleto bancario";
        }
    }

    public function getBancosHabilitadosFilial() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        echo json_encode($this->Model_filiales->getCuentasBancariasBoletos());
    }

    public function guardarCobrosCola() {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
    }

    public function getTerminales() {
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo' => $filial['codigo']);
        $this->load->model("Model_medio_tarjetas", "", false, $arrConf);
        $pos = $this->Model_medio_tarjetas->getTerminales(true, true);
        echo json_encode($pos);
    }

    public function getTarjetas() {
        $filial = $this->session->userdata('filial');
        $cod_terminal = $this->input->post('codigo');
        $arrConf = array('pais' => $filial['pais']);
        $this->load->model("Model_tipos_tarjetas", "", false, $arrConf);
        $tarjetas = $this->Model_tipos_tarjetas->getTiposTarjetasTerminal($cod_terminal, $filial['codigo']);
        echo json_encode($tarjetas);
    }

    public function getTarjetasDebito() {
        $filial = $this->session->userdata('filial');
        $cod_terminal = $this->input->post('codigo');
        $arrConf = array('pais' => $filial['pais']);
        $this->load->model("Model_tipos_tarjetas", "", false, $arrConf);
        $tarjetas = $this->Model_tipos_tarjetas->getTiposDebitoTerminal($cod_terminal, $filial['codigo']);
        echo json_encode($tarjetas);
    }

    public function getCajasCobrar() {
        $usuario = $this->session->userdata('codigo_usuario');
        $cod_medio = $this->input->post("cod_medio");
        $cod_cobro = $this->input->post("codigo");
        $cajas = $this->Model_cobros->getCajasMedio($usuario, $cod_medio, $cod_cobro);
        echo json_encode($cajas);
    }

    public function eliminarImputacion() {
        $filial = $this->session->userdata('filial');
        $config = array('codigo_filial' => $filial['codigo']);
        $respuesta = '';
        $this->load->model("Model_imputaciones", "", false, $config);
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo');
        $usuario = $this->session->userdata('codigo_usuario');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $respuesta['codigo'] = $this->Model_imputaciones->eliminarImputacion($codigo, $usuario);
        }
        echo json_encode($respuesta);
    }

    public function frm_confirmar_cobro() {
        $this->load->library('form_validation');
        $respuesta = '';
        $usuario = $this->session->userdata('codigo_usuario');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarConfirmarCobro[' . $usuario . ']');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta['codigo'] = 0;
            $respuesta['errors'] = $errors;
            echo json_encode($respuesta);
        } else {
            $cod_cobro = $this->input->post('codigo');
            $datos['cod_cobro'] = $cod_cobro;
            $datos['alumno'] = $this->Model_cobros->getnombreAlumno($cod_cobro);
            $this->load->view('cobros/frm_confirmar_cobro', $datos);
        }
    }

    public function confirmarCobro() {
        $usuario = $this->session->userdata('codigo_usuario');
        $cod_cobro = $this->input->post('codigo');
        $abre = true;
        $datos = array('cod_cobro' => $cod_cobro, 'cod_usuario' => $usuario, 'abrecaja' => $abre);
        $respuesta = $this->Model_cobros->confirmarCobro($datos);
        echo json_encode($respuesta);
    }

    public function getImputacionesCobro() {
        $this->load->library('form_validation');
        $cod_cobro = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $imputacionesCobro = $this->Model_cobros->getImputacionesCobro($cod_cobro);
            echo json_encode($imputacionesCobro);
        }
    }

    public function guardarImputacionesCobro() {
        $usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');
        $this->load->helper('formatearfecha');
        $ctactecheck = $this->input->post('checkctacte') ? $this->input->post('checkctacte') : array();
        $this->form_validation->set_rules('codigo', lang('cobro'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['cod_cobro'] = $this->input->post('codigo');
            $data_post['checkctacte'] = $ctactecheck;
            $data_post['cod_usuario'] = $usuario;
            $resultado = $this->Model_cobros->guardarImputacionesCobro($data_post);
        }
        echo json_encode($resultado);
    }

    public function getRestaImputar() {
        $cod_cobro = $this->input->post('cod_cobro');
        $total = $this->input->post('total');
        $respuesta = $this->Model_cobros->getRestaImputar($cod_cobro, $total);
        echo json_encode($respuesta);
    }

    public function facturarCobro() {
        $data_post = array();
        $facturante = $this->input->post('facturante');
        $this->form_validation->set_rules('cod_cobro', lang('codigo'), 'numeric|validarCobroFacturar');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $data_post['cod_cobro'] = $this->input->post('cod_cobro');
            $data_post['facturante'] = $facturante;
            $data_post['puntos_venta'] = $this->input->post("puntos_venta");
            $respuesta = $this->Model_cobros->facturarCobro($data_post);
            echo json_encode($respuesta);
        }
    }
    
    public function abrir_periodos_cobros(){
        $arrResp = array();
        if (!isset($_POST['filiales']) || !is_array($_POST['filiales']) || !isset($_POST['periodo_mes']) || !isset($_POST['periodo_anio'])){
            $arrResp['error'] = "error de parametros";
        } else {
            $conexion = $this->load->database("general", true);
            if (Vcobros::abrir_periodos_cobros($conexion, $_POST['filiales'], $_POST['periodo_mes'], $_POST['periodo_anio'])){
                $arrResp['success'] = "success";
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        }
        echo json_encode($arrResp);
    }
    
    public function cerrar_periodos_cobros(){
        $arrResp = array();
        if (!isset($_POST['filiales']) || !is_array($_POST['filiales']) || !isset($_POST['periodo_mes']) || !isset($_POST['periodo_anio'])){
            $arrResp['error'] = "error de parametros";
        } else {
            $conexion = $this->load->database("general", true);
            if (Vcobros::cerrar_periodos_cobros($conexion, $_POST['filiales'], $_POST['periodo_mes'], $_POST['periodo_anio'])){
                $arrResp['success'] = "success";
                $periodo = array($_POST['periodo_anio'].$_POST['periodo_mes']);
                $arrFiliales = array();
                foreach ($_POST['filiales'] as $filial){
                    $arrFiliales[$filial] = $this->Model_cobros->getCobrosMensuales($filial, $periodo);
                }
                $arrResp['filiales'] = $arrFiliales;
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        }
        echo json_encode($arrResp);
    }
    
    public function get_medio_pago_cuotas(){
        if ($this->input->post("cod_alumno")){
            $arrResp = array();
            $cod_alumno = $this->input->post("cod_alumno");
            $filial = $this->session->userdata("filial");
            $conexion = $this->load->database($filial['codigo'], true);
            $myAlumno = new Valumnos($conexion, $cod_alumno);
            $arrResp['medios_pago_cuotas'] = $myAlumno->get_medios_pago_cuotas();
            echo json_encode($arrResp);
        }
    }    
}
