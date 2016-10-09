<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Boletos extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_boletos", "", false, $filial["codigo"]);
    }

    public function index($codMatricula = null, $codAlumno = null) {
        $this->lang->load(get_idioma(), get_idioma());
        $session = $this->session->userdata('secciones');
        $transferirArchivo = 0;
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        foreach ($session['boletos']['subcategorias'] as $key => $valor) {
            if ($valor['slug'] == 'transferencia_de_archivos' && $valor['habilitado'] == 1) {
                $transferirArchivo = 1;
            }
        }

        $this->load->model("Model_facturantes", "", false, $config);
        $data['page'] = 'boletos/vista_boletos';
        $data['seccion'] = $this->seccion;
        $data['trasferir_archivo'] = $transferirArchivo;
        $data['arrColumnas'] = $this->getColumnas();
        $data['arrColumnasBoleto'] = $this->getColumnasBoleto();
        $data['estados_boletos'] = Vboletos_bancarios::getEstados();        
        $data['cod_matricula'] = $codMatricula != null ? $codMatricula : -1;
        $data['cod_alumno'] = $codAlumno != null ? $codAlumno : -1;        
        $facturanteCuentaBanco = array();
        foreach ($this->Model_facturantes->getFacturantes(false) as $facturante) {   
            if(count($this->Model_facturantes->getCuentasBoleto($facturante["codigofacturante"])) != 0){        
                $facturanteCuentaBanco[]  =  $facturante ;                    
            }
        }
        
        $claves = array(
        "validacion_ok",
        "debe_seleccionar_al_menos_un_items_de_cuenta_corriente",
        "emitir_boletos",
        "transferencia_de_archivos",
        "volver",
        "baja_de_boletos",
        "debe_seleccionar_algun_boleto_para_dar_de_baja",
        "validacion_ok",
        "debe_seleccionar_al_menos_un_items_de_cuenta_corriente",
        "emitir_boletos",
        "transferencia_de_archivos",
        "volver",
        "no_tiene_permiso"
        );
        $arrPermisos = json_decode(getMenuJson('boletos'),true);
        $arrPermisos[] = array(
            'habilitado'=>1,
                'accion'=>'ver_detalle_boleto',
                'text'=>lang('ver_detalle_boleto')
        );
        $data['lang'] = getLang($claves);
        $data['menuJson'] = json_encode($arrPermisos);
        $data['facturantes'] = $facturanteCuentaBanco ;
        $this->load->view('container', $data);
    }

    public function getBoletosDataTable() {
        $this->load->helper("cuentacorriente");
        $crearColumnas = $this->getColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $fechaEmisionDesde = isset($_POST['fecha_emision_desde']) && $_POST['fecha_emision_desde'] <> ''
                ? formatearFecha_mysql($_POST['fecha_emision_desde']) : null;
        $fechaEmisionHasta = isset($_POST['fecha_emision_hasta']) && $_POST['fecha_emision_hasta'] <> ''
                ? formatearFecha_mysql($_POST['fecha_emision_hasta']) : null;
        $fechaVencimientoDesde = isset($_POST['fecha_vencimiento_desde']) && $_POST['fecha_vencimiento_desde'] <> ""
                ? formatearFecha_mysql($_POST['fecha_vencimiento_desde']) : null;
        $fechaVencimientoHasta = isset($_POST['fecha_vencimiento_hasta']) && $_POST['fecha_vencimiento_hasta'] <> ""
                ? formatearFecha_mysql($_POST['fecha_vencimiento_hasta']) : null;
        $estado = isset($_POST['estado']) && $_POST['estado'] != '' && $_POST['estado'] != '-1' ? $_POST['estado'] : null;
        $valores = $this->Model_boletos->listarBoletosDataTable($arrFiltros, $estado, $fechaVencimientoDesde, $fechaVencimientoHasta,
                $fechaEmisionDesde, $fechaEmisionHasta);
        if (isset($_POST['exportar'])){
            $arrTemp = array();
            foreach ($valores['aaData'] as $valores){
                if ($_POST['exportar'] == 'pdf'){
                    $arrTemp[] = array(                    
                        $valores[1], substr($valores[2], 0, 31), $valores[3], substr($valores[4], 0, 63), $valores[5], 
                        $valores[6], $valores[7], $valores[8] 
                    );
                } else {
                    $arrTemp[] = array(                    
                        $valores[1], $valores[2], $valores[3], $valores[4], $valores[5], 
                        $valores[6], $valores[7], $valores[8] 
                    );
                }
            }
            $exp = new export($_POST['exportar']);
            
            if ($_POST['exportar'] == 'pdf'){
                $exp->setTitle(array(lang("fecha"), lang("facturado"), lang("documento"), lang("concepto"),
                    lang("importe"), lang("vencimiento"), lang("nosso_numero"), lang("estado")));
                $exp->setColumnWidth(array(20, 55, 25, 85, 20, 24, 34, 18));
                $exp->setPDFFontSize(8);
            } else {
                $exp->setTitle(array(lang("fecha_emision"), lang("facturado"), lang("documento"), lang("concepto"),
                    lang("importe"), lang("vencimiento"), lang("nosso_numero"), lang("estado")));
            }
            $exp->setContent($arrTemp);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }

    public function getCtactePendienteEmision() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_ctacte", "", false, $config);
        $this->load->helper("Alumnos");
        $crearColumnas = $this->getColumnasBoleto();
        $this->load->model("Model_facturantes", "", false, $config);
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $arrCtacte = $this->input->post("ctacte") ? $this->input->post("ctacte") : null;
        $fecha_desde = $this->input->post('fecha_desde') != ''  ? formatearFecha_mysql($this->input->post('fecha_desde')) : '';
        $fecha_hasta = $this->input->post('fecha_hasta') != ''  ? formatearFecha_mysql($this->input->post('fecha_hasta')) : '';
        $codAlumno = $this->input->post("cod_alumno") && $this->input->post("cod_alumno") <> -1 ? $this->input->post("cod_alumno") : null;
        $codMatricula = $this->input->post("cod_matricula") && $this->input->post("cod_matricula") <> -1 ? $this->input->post("cod_matricula") : null;
        echo json_encode($this->Model_ctacte->getCtacteSinCobrarDatatable($arrFiltros,$separador ,$arrCtacte,$fecha_desde,$fecha_hasta, $codAlumno, $codMatricula));
    }

    public function getCtaCteRematriculaciones(){
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_ctacte", "", false, $config);
        $this->load->helper("Alumnos");
        $crearColumnas = $this->getColumnasBoleto();
        $this->load->model("Model_facturantes", "", false, $config);
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $arrCtacte = $this->input->post("ctacte") ? $this->input->post("ctacte") : null;
        $fecha_desde = $this->input->post('fecha_desde') != ''  ? formatearFecha_mysql($this->input->post('fecha_desde')) : '';
        $fecha_hasta = $this->input->post('fecha_hasta') != ''  ? formatearFecha_mysql($this->input->post('fecha_hasta')) : '';
        $codAlumno = $this->input->post("cod_alumno") && $this->input->post("cod_alumno") <> -1 ? $this->input->post("cod_alumno") : null;
        $codMatricula = $this->input->post("cod_matricula") && $this->input->post("cod_matricula") <> -1 ? $this->input->post("cod_matricula") : null;
        $matriculasEmitir = isset($_POST['alumnosRematricular'])?json_decode($_POST['alumnosRematricular']):null;
        $desde = isset($_POST['desde'])?$_POST['desde']:null;        
        $hasta = isset($_POST['hasta'])?$_POST['hasta']:null;        
        echo json_encode($this->Model_ctacte->getCtacteRematriculacionDataTable($desde, $hasta, $matriculasEmitir, $arrFiltros,$separador ,$arrCtacte,$fecha_desde,$fecha_hasta, $codAlumno, $codMatricula));
    }

    public function getIdsRematriculaciones() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_ctacte", "", false, $config);
        $this->load->helper("Alumnos");
        $crearColumnas = $this->getColumnasBoleto();
        $this->load->model("Model_facturantes", "", false, $config);
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $arrCtacte = $this->input->post("ctacte") ? $this->input->post("ctacte") : null;
        $fecha_desde = $this->input->post('fecha_desde') != ''  ? formatearFecha_mysql($this->input->post('fecha_desde')) : '';
        $fecha_hasta = $this->input->post('fecha_hasta') != ''  ? formatearFecha_mysql($this->input->post('fecha_hasta')) : '';
        $codAlumno = $this->input->post("cod_alumno") && $this->input->post("cod_alumno") <> -1 ? $this->input->post("cod_alumno") : null;
        $codMatricula = $this->input->post("cod_matricula") && $this->input->post("cod_matricula") <> -1 ? $this->input->post("cod_matricula") : null;
        $matriculasEmitir = isset($_POST['alumnosRematricular'])?json_decode($_POST['alumnosRematricular']):null;
        $desde = isset($_POST['desde'])?$_POST['desde']:null;
        $hasta = isset($_POST['hasta'])?$_POST['hasta']:null;
        echo json_encode($this->Model_ctacte->getCtacteRematriculacionDataTable($desde, $hasta, $matriculasEmitir, $arrFiltros,$separador ,$arrCtacte,$fecha_desde,$fecha_hasta, $codAlumno, $codMatricula, true));
    }

    public function generarBoletosBacarios() {
        $arrResp = array();
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true, true );
        $ctact = array();
        parse_str($_POST['datosTabla'], $ctact);
        $movimientosEmitir = $_POST['ctacte'];
        $formulario = array();
        $formulario = Vconfiguracion::getValorConfiguracion($conexion, null, 'etiquetasBoleto');
        $enviarRemessa = isset($formulario['cb-enviarRemessa']);
        $codigoEnviar;
        if (!Vboletos_bancarios::validarEmision($conexion, $movimientosEmitir, $filial['codigo'])){
            $arrResp['error'] = "Error";
            $arrResp['msg'] = "ya_existen_boletos_generados_para_alguna_de_las_cuentas_corrientes_seleccionadas";
        } else {
            $this->load->model("Model_filiales", "", false, $filial['codigo']);
            $arrTemp = $this->Model_filiales->getCuentasBancariasFilial();        
            $conexion->trans_begin();
            $codBanco = $arrTemp[0]['cod_banco'];
            $codConfiguracion = $arrTemp[0]['cod_configuracion'];
            $codFacturante = $arrTemp[0]['cod_facturante'];
            $myBanco = new Vbancos($conexion, $codBanco);
            $myBancoBrasil = new Vbanco_do_brasil($conexion, $arrTemp[0]['cod_configuracion']); // se supone siempre para banco de brasil
            $myCuentaBoleto = new Vcuentas_boletos_bancarios($conexion, $codBanco, $codConfiguracion, $codFacturante);
            $myFacturtante = new Vfacturantes($conexion, $codFacturante);
            $myRazonFacturante = new Vrazones_sociales_general($conexion, $myFacturtante->cod_razon_social);
            $myLocalidad = new Vlocalidades($conexion, $myRazonFacturante->cod_localidad);
            $myProvincia = new Vprovincias($conexion, $myLocalidad->provincia_id);
            $condiciones = array("baja" => 0, "diariamente" => 1);
            $moras = Vmoras::listarMoras($conexion, $condiciones);
            $interesMora = isset($moras[0]) && isset($moras[0]["mora"]) ? $moras[0]["mora"] : 0;
            $myRemesa = new Vremesas($conexion);
            $myRemesa->agencia = $myBancoBrasil->agencia;
            $myRemesa->cartera = $myCuentaBoleto->carteira;
            $myRemesa->cedente_convenio = $myCuentaBoleto->convenio;
            $myRemesa->cedente_cpf_cnpj = $myRazonFacturante->documento;
            $myRemesa->cod_banco = $codBanco;
            $myRemesa->cod_configuracion = $myBancoBrasil->getCodigo();
            $myRemesa->cod_facturante = $codFacturante;
            $myRemesa->digito_agencia = $myBancoBrasil->digito_agencia;
            $myRemesa->digito_cuenta = $myBancoBrasil->digito_cuenta;
            $myRemesa->direccion = $myRazonFacturante->direccion_calle . " " . $myRazonFacturante->direccion_numero;
            $myRemesa->especie_documento = "DM";
            $myRemesa->fecha_documento = date("Y-m-d");
            $myRemesa->nombre_banco = $myBanco->nombre;
            $myRemesa->numero_cuenta = $myBancoBrasil->conta;
            $myRemesa->razon_social = $myRazonFacturante->razon_social;
            $myRemesa->variacion_cartera = $myCuentaBoleto->variacao_carteira;
            $myRemesa->localidad = strtoupper($myLocalidad->nombre);
            $myRemesa->codigo_estado = $myProvincia->get_codigo_estado();
            $myRemesa->enviada = 0;
            $myRemesa->guardarRemesas();
            $estado = isset($ctact['estado']) && $ctact['estado'] == "baja" ? Vboletos_bancarios::getEstadoBajaSolicitada() : Vboletos_bancarios::getEstadoPendiente();
            foreach ($movimientosEmitir as $ctacte) { // ver que si se quiere dar de baja un boleto, es probable que debamos informar un boleto ya emitido (numero seguimiento, nosso numero, etc.)
                if (isset($ctact['estado']) && $ctact['estado'] == "baja") {
                    $myBoleto = new Vboletos_bancarios($conexion, $ctacte);
                    $myBoleto->cod_remesa = $myRemesa->getCodigo();             // cambia de remesa (cuando se trate de imprimir la remesa anterior, este boleto no deberia aparecer)
                    $myBoleto->estado = $estado;                                // cambia de estado
                    $myBoleto->guardarBoletos_bancarios();
                    $myHistorico = new Vboletos_estados_historicos($conexion);  // se guarda el historico (no se puede utilizar la funcotin setEstado ya que no poseemos los segmentos)
                    $myHistorico->cod_boleto = $myBoleto->getCodigo();
                    $myHistorico->cod_usuario = $this->session->userdata('codigo_usuario');
                    $myHistorico->estado = $estado;
                    $myHistorico->fecha = date("Y-m-d H:i:s");
                    $myHistorico->guardarBoletos_estados_historicos();
                } else {
                    $myCtacte = new Vctacte($conexion, $ctacte);
                    $myAlumno = new Valumnos($conexion, $myCtacte->cod_alumno);
                    $mySacado = $myAlumno->getSacado();
                    $myBoleto = new Vboletos_bancarios($conexion);
                    $myBoleto->cod_filial = $filial['codigo'];
                    $myBoleto->cod_remesa = $myRemesa->getCodigo();
                    $myBoleto->fecha_mora = $myCtacte->fechavenc;
                    $myBoleto->fecha_vencimiento = $myCtacte->fechavenc;
                    $myBoleto->numero_documento = $myCtacte->getCodigo();
                    $myBoleto->numero_secuencial = $myCuentaBoleto->numero_secuencia;
                    $myBoleto->porcentaje_mora = $interesMora;
                    $myBoleto->sacado_ciudad = $mySacado->ciudad->nombre;
                    $myBoleto->sacado_cod_postal = $mySacado->cod_postal;
                    $codigoEstado = $mySacado->provincia->get_codigo_estado();
                    $myBoleto->sacado_codigo_estado = $codigoEstado == '' ? "" : $codigoEstado;
                    $myBoleto->sacado_cpf_cnpj = $mySacado->cpf_cnpj;
                    $myBoleto->sacado_direccion = $mySacado->direccion;
                    $myBoleto->sacado_nombre = $mySacado->nombre;
                    $myBoleto->valor_boleto = $myCtacte->importe;
                    $myBoleto->convenio = $myCuentaBoleto->convenio;
                    //Armo los detalles segun el formulario.
                    $lineas = $this->generarEtiquetas($formulario,$myBoleto,$myCtacte);
                    /*
                    $myBoleto->demostrativo1 = $myCuentaBoleto->demostrativo1;
                    $myBoleto->demostrativo2 = $myCuentaBoleto->demostrativo2;
                    $myBoleto->demostrativo3 = $myCuentaBoleto->demostrativo3;
                    $myBoleto->instrucciones1 = $myCuentaBoleto->instrucciones1;
                    $myBoleto->instrucciones2 = $myCuentaBoleto->instrucciones2;
                    $myBoleto->instrucciones3 = $myCuentaBoleto->instrucciones3;
                    $myBoleto->instrucciones4 = $myCuentaBoleto->instrucciones4;
                    */
                    $myBoleto->estado = Vboletos_bancarios::getEstadoPendiente();
                    $myBoleto->guardarBoletos_bancarios($myCuentaBoleto->convenio);
                    $myCuentaBoleto->incremetarNumeroSequencial();
                }
                if($enviarRemessa && $enviarRemessa != 'false'){
                    $codigoEnviar = array($myRemesa->getCodigo());
                }
            }
            if ($conexion->trans_status()) {
                $arrResp['success'] = "success";
                $arrResp['codigo_remesa'] = $myRemesa->getCodigo();
                $conexion->trans_commit();
                if($enviarRemessa){
                    $config = array("codigo_filial" => $filial['codigo']);
                    $this->load->model("Model_facturantes", "", false, $config);
                    $ids = array($myRemesa->getCodigo());
                    $arrResp['remesaEnviada'] = $this->Model_facturantes->moverAFTP($ids);
                }
            } else {
                $arrResp['error'] = "Error";
                $conexion->trans_rollback();
            }

            echo json_encode($arrResp);
        }
    }

    public function generarEtiquetas($formulario, $myBoleto, $myCtacte){
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
/*
        Sobre los descuentos:
            Es muy raro que un movimiento de cuenta tenga mas de un descuento. Siempre los descuentos que se generan al matricular dan
            el importe completo, los que se agregan despues tienen comportamientos raros, tendrian que revisarse. La filosofia de la aplicacion es
            que si hay MAS de un descuento, se pone el valor con descuento y no se recibe el boleto despues del vencimiento.
*/
        $valorDesconto = $myBoleto->valor_boleto;
        $valorCheio = $myBoleto->valor_boleto;
        $fechaPerdidaDescuento = null;
        $valorDescuento = null;
        if($formulario == null){
            $formulario = $_POST;
        }
        if(count($arrDescuentos) == 1){
            $desc = $arrDescuentos[0];
            $valorCheio = floatval($desc['importe']);
            $descuento = floatval($desc['descuento']) / 100;
            $valorDescuento = number_format($valorCheio * $descuento, 2, '.', '');
            $fechaPerdidaDescuento = $desc['fecha_perdida_descuento'];
        }
        $lineas = array();
        if(isset($formulario['juros-cobrar']) 
        && isset($formulario['juros-tipo'])
        && $formulario['juros-cobrar'] == 'on'){
            if($formulario['juros-tipo'] == 'banco'){
                $lineas[] = 'JURO DEFINIDO PELO BANCO (FACP)';
            }
            if($formulario['juros-tipo'] == 'iga' && isset($formulario['juros-valoriga'])){
                $lineas[] = 'JURO DEFINIDO PELA IGA: ' . $formulario['juros-valoriga'] . '%';
            }
        }
        if(isset($formulario['multa-cobrar'])
        && isset($formulario['multa-valor'])
        && isset($formulario['multa-dias'])){
            $diasVencimiento = $formulario['multa-dias'];
            $vencimiento = $myCtacte->fechavenc;
            if($vencimiento == null)
                $vencimiento = date('Y-m-d');
            $fecha = date('d-m-Y', strtotime($vencimiento . ' + '. $formulario['multa-dias'] .' day'));
            $lineas[] = 'MULTA DE ' . $formulario['multa-valor'] . '% A PARTIR DE ' . $fecha;
        }
        if(isset($formulario['juros-cobrar']) 
        && isset($formulario['multa-cobrar'])
        && $formulario['juros-cobrar'] == 'on'
        && $formulario['multa-cobrar'] == 'on'){
            $lineas[] = 'PROCEDA OS AJUSTES DE VALORES PERTINENTES';
        }
        if(isset($formulario['venc-tipo'])){
            if($formulario['venc-tipo'] == 'naor'){
                $lineas[] = 'NAO RECEBER APOSOVENCIMIENTO';
            }
            if($formulario['venc-tipo'] == 'banco'){
                if(isset($formulario['venc-dias'])
                && isset($formulario['venc-limite'])){
                    $lineas[] = 'NAO RECEBER APOS ' . $formulario['venc-dias'] . ' DIAS DE ATRASO';
                }
                $lineas[] = 'RECEBER APOS O VENCIMIENTO SOMENTE NO BANCO EMISOR';
            }
        }
        if(isset($formulario['inclu-apos'])
        && isset($formulario['inclu-dias'])
        && $formulario['inclu-apos'] == 'on'){
            $lineas[] = 'INCLUSAO NO SERASA APOS ' . $formulario['inclu-dias'] . ' DIAS DE ATRASO';
        }
        $desc = "";
        if(isset($formulario['valorBoleto'])){
            if($formulario['valorBoleto'] == 'desconto'){
                if($fechaPerdidaDescuento != null && $valorDescuento != null)
                    $lineas[] = "Multa apos " . $fechaPerdidaDescuento . "  R$ " . $valorDescuento;
            }
            if($formulario['valorBoleto'] == 'cheio'){
                $myBoleto->valor_boleto = $valorCheio;
                if($fechaPerdidaDescuento != null && $valorDescuento != null)
                    $lineas[] = "DESC: Vlr Fixo Dt-" . $fechaPerdidaDescuento . "  R$ " . $valorDescuento;
            }
        }

        if(isset($formulario['descontoFixo'])
        && $formulario['descontoFixo'] == 'on'){
            $lineas[] = 'Descontro valor fixo ate a data de vencimiento';
        }
        $lalinea = 0;
        $myBoleto->demostrativo1 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";
        $myBoleto->demostrativo2 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";
        $myBoleto->demostrativo3 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";
        $myBoleto->instrucciones1 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";
        $myBoleto->instrucciones2 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";
        $myBoleto->instrucciones3 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";
        $myBoleto->instrucciones4 = isset($lineas[$lalinea])?$lineas[$lalinea++]:"";

    }

    public function ver_detalle_boleto() {
        $codBoleto = $this->input->post("codigo_boleto");
        $this->load->helper("cuentacorriente");
        $arrResp = $this->Model_boletos->getDetalleBoleto($codBoleto);
        echo json_encode($arrResp);
    }

    private function getColumnas() {
        $columnas = array(
            array("nombre" => "&nbsp;", "campo" => 'codigo'),
            array("nombre" => lang("fecha_emision"), "campo" => "fecha_emision"),
            array("nombre" => lang("facturado"), "campo" => 'codigo'),
            array("nombre" => lang("documento"), "campo" => 'sacado_cpf_cnpj'),
            array("nombre" => lang("concepto"), "campo" => 'descripcion', "sort" => false),
            array("nombre" => lang("importe"), "campo" => 'valor_boleto'),
            array("nombre" => lang("vencimiento"), "campo" => 'fecha_vencimiento'),
            array("nombre" => lang("nosso_numero"), "campo" => 'numero_secuencial'),
            array("nombre" => lang("estado"), "campo" => 'estado')
        );
        return $columnas;
    }

    private function getColumnasBoleto() {
        $columnas = array(
            array("nombre" => "&nbsp;", "campo" => 'codigo',"sort" => FALSE),
            array("nombre" => lang('nombre'), "campo" => 'nombre_apellido'),
            array("nombre" => lang('descripcion'), "campo" => 'descripcion'),
            array("nombre" => lang('fecha_vencimiento'), "campo" => 'fechavenc'),
            array("nombre" => lang('importe'), "campo" => 'importe')
        );
        return $columnas;
    }

    public function archivosBoletoBancario() {

        $data['seccion'] = session_method();
        $data['page'] = 'bancos/boletobancario/archivos/vista_archivos';


        $this->load->view('container', $data);
    }
    


}
