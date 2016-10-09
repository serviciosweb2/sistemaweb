<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
class Rematriculaciones extends CI_Controller{
    
    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_rematriculaciones", "", false, $configMatriculas);
    }

    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        $session = $this->session->userdata('secciones');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $data['page'] = 'rematriculaciones/vista_rematriculaciones';
        $data['seccion'] = $this->seccion;
        $trimestre = isset($_GET['trimestre'])?$_GET['trimestre']:1;
        $anio = isset($_GET['anio'])?$_GET['anio']:intval(date('Y'));
        $curso = (isset($_GET['curso']) && $_GET['curso'] != '-1')?explode(',',$_GET['curso']):null;
        $comision = (isset($_GET['comision']) && $_GET['comision'] != '-1')?explode(',',$_GET['comision']):null;
        $facturanteCuentaBanco = array();

        $arrPermisos = json_decode(getMenuJson('rematriculaciones'),true);

        //Reveer claves.
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
            "no_tiene_permiso",
            "no_hay_boletos_imprimir",
            "habilitar_rematriculacion",
            "deshabilitar_rematriculcion"
        );
        $data['columns'] = $this->getColumns();
        $data['lang'] = getLang($claves);
        $data['menuJson'] = json_encode($arrPermisos);
        $data['facturantes'] = $facturanteCuentaBanco ;
        $data['trimestre'] = $trimestre;
        $data['anio'] = $anio;
        $data['curso'] = ($curso == null)?array('-1'):$curso;
        $data['comision'] = ($comision==null)?array('-1'):$comision;
        $data['comisiones'] = $this->Model_rematriculaciones->getComisionesRematricular();
        $data['cursos'] = $this->Model_rematriculaciones->getCursosRematricular($anio, $trimestre, $curso, $comision, $data['comisiones']);
        $this->load->view('container', $data);
    }

    public function firmaRematricula() {
        $this->lang->load(get_idioma(), get_idioma());
        $session = $this->session->all_userdata();
        $seccion = $this->router->uri->uri_string;
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->helper("datatables");
        $data['page'] = 'rematriculaciones/firma_rematricula';
        $data['seccion'] = $session['secciones'][$seccion];
        $data['seccion']['titulo'] = $data['seccion']['slug'];
        $data['columnas'] = getColumnsDatatable($this->crearColumnas_firma_rematricula());
        $data['columns'] = $this->getColumns_firma_rematricula();

        $claves = array(
            "SI",
            "NO",
            "año"
        );
        $data['lang'] = getLang($claves);

        $this->load->view('container', $data);
    }

    public function listar_firmaRematricula() {
        $filial = $this->session->userdata('filial');
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model('Model_usuario', "", false, array('filial' => 'general'));
        $this->load->model('Model_rematriculaciones', "", false, $arrConf);

        $crearColumnas = $this->crearColumnas_firma_rematricula();

        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";

        $arrFiltros["documento"] = isset($_POST['documento']) && $_POST['documento'] <> -1 ? $_POST['documento'] : "";
        $arrFiltros["nombre"] = isset($_POST['nombre']) && $_POST['nombre'] <> -1 ? $_POST['nombre'] : "";
        $arrFiltros["apellido"] = isset($_POST['apellido']) && $_POST['apellido'] <> -1 ? $_POST['apellido'] : "";
        $arrFiltros["matricula"] = isset($_POST['matricula']) && $_POST['matricula'] <> -1 ? $_POST['matricula'] : "";
        $arrFiltros["comision"] = isset($_POST['comision']) && $_POST['comision'] <> -1 ? $_POST['comision'] : "";
        $arrFiltros["ciclo"] = isset($_POST['ciclo']) && $_POST['ciclo'] <> -1 ? $_POST['ciclo'] : "";
        $arrFiltros["firmo"] = isset($_POST['firmo']) && $_POST['firmo'] <> -1 ? $_POST['firmo'] : "";
        $arrFiltros["ano"] = isset($_POST['ano']) && $_POST['ano'] <> -1 ? $_POST['ano'] : "";
        $arrFiltros["trimestre"] = isset($_POST['trimestre']) && $_POST['trimestre'] <> -1 ? $_POST['trimestre'] : "";
        $arrFiltros["fecha"] = isset($_POST['fecha']) && $_POST['fecha'] <> '' ? formatearFecha_mysql($_POST['fecha']) : '';

        $arrFiltros["condiciones_doc"]  = isset($_POST['condiciones_doc']) && $_POST['condiciones_doc'] <> -1 ? $_POST['condiciones_doc'] : "";//se pone el mayor o igual,menor,mayor, etc
        $arrFiltros["condiciones_nom"]  = isset($_POST['condiciones_nom']) && $_POST['condiciones_nom'] <> -1 ? $_POST['condiciones_nom'] : "";
        $arrFiltros["condiciones_ape"]  = isset($_POST['condiciones_ape']) && $_POST['condiciones_ape'] <> -1 ? $_POST['condiciones_ape'] : "";
        $arrFiltros["condiciones_mat"]  = isset($_POST['condiciones_mat']) && $_POST['condiciones_mat'] <> -1 ? $_POST['condiciones_mat'] : "";
        $arrFiltros["condiciones_com"]  = isset($_POST['condiciones_com']) && $_POST['condiciones_com'] <> -1 ? $_POST['condiciones_com'] : "";
        $arrFiltros["condiciones_cic"]  = isset($_POST['condiciones_cic']) && $_POST['condiciones_cic'] <> -1 ? $_POST['condiciones_cic'] : "";//se pone el mayor o igual,menor,mayor, etc
        $arrFiltros["condiciones_fir"]  = isset($_POST['condiciones_fir']) && $_POST['condiciones_fir'] <> -1 ? $_POST['condiciones_fir'] : "";
        $arrFiltros["condiciones_ano"]  = isset($_POST['condiciones_ano']) && $_POST['condiciones_ano'] <> -1 ? $_POST['condiciones_ano'] : "";
        $arrFiltros["condiciones_tri"]  = isset($_POST['condiciones_tri']) && $_POST['condiciones_tri'] <> -1 ? $_POST['condiciones_tri'] : "";
        $arrFiltros["condiciones_fec"]  = isset($_POST['condiciones_fec']) && $_POST['condiciones_fec'] <> -1 ? $_POST['condiciones_fec'] : "";

        $valores = $this->Model_rematriculaciones->getFirmaRematricula($arrFiltros);

        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            $linea = 1;
            foreach ($valores['aaData'] as $valor) {
                $arrTemp[] = array(
                    $linea,
                    $valor[0],
                    $valor[1],
                    $valor[2],
                    $valor[3],
                    $valor[4],
                    $valor[5],
                    $valor[6],
                    $valor[7],
                    $valor[8],
                    $valor[9]
                );
                $linea++;
            }

            $arrTitle = array(
                lang("N"),
                lang("documento"),
                lang("nombre"),
                lang("apellido"),
                lang("matricula"),
                lang("comision"),
                lang("ciclo"),
                lang("firmo"),
                lang("año"),
                lang("trimestre"),
                lang("fecha")
            );
            $arrWidth = array(10, 30, 35, 40, 30, 40, 20, 20, 20, 20, 20);
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => lang('firma_carta-compromiso_rematriculas'), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => "", "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrTemp);
            $exp->setPDFFontSize(7);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle(lang('rematricula'));
            $exp->setMargin(2, 8);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }


    public function crearColumnas_firma_rematricula() {
        $columnas = array(
            array("nombre" => lang('documento'), "campo" => 'documento'),
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang('apellido'), "campo" => 'apellido'),
            array("nombre" => lang('matricula'), "campo" => 'matricula'),

            array("nombre" => lang('comision'), "campo" => 'comision'),
            array("nombre" => lang('ciclo'), "campo" => 'ciclo'),

            array("nombre" => lang('firmo'), "campo" => 'firmo'),
            array("nombre" => lang('año'), "campo" => 'ano'),
            array("nombre" => lang('trimestre'), "campo" => 'trimestre'),
            array("nombre" => lang('fecha'), "campo" => 'fecha'),);
        return $columnas;
    }

    public function getColumns_firma_rematricula() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas_firma_rematricula()));
        return $aoColumnDefs;
    }

    public function frm_firmas(){
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $arg["codigo_filial"] = $filial["codigo"];
        $data['nombre'] = "Prueba";
        $this->load->view('rematriculaciones/frm_firmas', $data);
    }

    public function guardarFirma() {
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $arg["codigo_filial"] = $filial["codigo"];
        $conexion = $this->load->database($arg["codigo_filial"], true);
        $guardarFirma = array(
            'cod_matricula' => $this->input->post('cod_matricula'),
            'firmo' => $this->input->post('firmo'),
            'trimestre' => $this->input->post("trimestre"),
            'fecha' => date("Y-m-d"),
            'codigo_usuario' => $this->session->userdata('codigo_usuario'),
            'ano' => $this->input->post('ano')
        );
            $resultado = $this->Model_rematriculaciones->guardarFirma($conexion, $guardarFirma);
        echo json_encode($resultado);
    }

    public function condicionesfiltro(){
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_rematriculaciones", "", false, $config);
        switch($_POST['campo']){
            case 'documento':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'string';
                break;
            case 'nombre':
                $filtros=array("a"=>array('id'=>"contiene",'display'=>"contiene","cant"=>1));
                $data['dataType'] = 'string';
                break;
            case 'apellido':
                $filtros=array("a"=>array('id'=>"contiene",'display'=>"contiene","cant"=>1));
                $data['dataType'] = 'string';
                break;
            case 'matricula':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'integer';
                break;
            case 'comision':
                $filtros=array("a"=>array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'string';
                break;
            case 'ciclo':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'integer';
                break;
            case 'firmo':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'string';
                break;
            case 'ano':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'integer';
                break;
            case 'trimestre':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'integer';
                break;
            case 'fecha':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'date';
                break;
            default:
                $filtros = array("a"=>array('id'=>"-1",'display'=>"(".strtolower(lang('SELECCIONE_UNA_OPCION')).")","cant"=>1));
        }
        echo json_encode($filtros);
    }

    private function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }


    private function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => "<input type='checkbox' name='checkBoxGeneral' />", "sort" => FALSE),
            array("nombre" => lang('matricula'), "campo" => 'matricula'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_alumno'),
            array("nombre" => "CPF", "campo" => 'documento'),
            array("nombre" => lang('fecha_alta'), "campo" => 'fecha_matricula'),
            array("nombre" => "Valor pendiente", "campo" => 'valor_debe' ),
            array("nombre" => "Cuotas pendientes", "campo" => 'cuotas_debe' ),
            array("nombre" => "Estado boletos", "campo" => 'estado_boletos'),
            array("nombre" => "Estado rematricula", "campo" => 'estado_rematricula')
        );
        return $columnas;
    }

    public function listar(){
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $idioma = $this->session->userdata('idioma');
        $todos = null;
        $valores = array();
        $todos = isset($_POST['requestType'])?true:false;
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $comision = isset($_POST['comision'])?$_POST['comision']:die("Falta comision");
        $fechaDesde = isset($_POST['fechaDesde'])?$_POST['fechaDesde']:die("Falta triemstre");
        $fechaHasta = isset($_POST['fechaHasta'])?$_POST['fechaHasta']:die("Falta triemstre");
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $valores = $this->Model_rematriculaciones->listarRematriculaciones($arrFiltros, 1, $idioma, $separador, $comision, $todos, $fechaDesde, $fechaHasta); 
        echo json_encode($valores);
    }

    public function emitir(){
        
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
        $matriculas = isset($_POST['matriculas'])?$_POST['matriculas']:array();
        $codMatricula = -1;
        $codAlumno = -1;
        $this->load->model("Model_facturantes", "", false, $config);
        $data['seccion'] = $this->seccion;
        $data['trasferir_archivo'] = $transferirArchivo;
        $data['arrColumnas'] = $this->getColumnasEmitir();
        $data['arrColumnasBoleto'] = $this->getColumnasBoleto();
        $data['estados_boletos'] = Vboletos_bancarios::getEstados();        
        $data['cod_matricula'] = -1;
        $data['cod_alumno'] = -1;
        $data['matriculas'] = json_encode($matriculas);
        $data['desde'] = isset($_POST['desde'])?$_POST['desde']:null;
        $data['hasta'] = isset($_POST['hasta'])?$_POST['hasta']:null;
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
        $data['embed'] = true;
        $this->load->view('rematriculaciones/frm_emitir', $data);
    }

    public function getBoletosReimprimir(){
        $matriculas = isset($_POST['matriculas'])?$_POST['matriculas']:array();
        $desde = isset($_POST['desde'])?$_POST['desde']:array();
        $hasta = isset($_POST['hasta'])?$_POST['hasta']:array();
        echo json_encode($this->Model_rematriculaciones->getBoletosReimprimir($matriculas, $desde, $hasta));
    }

    private function getColumnasEmitir() {
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
    

    public function frm_habilitar(){
        $matricula = $_POST['matricula'];
        $data['fechaDesde']=$_POST['fechaDesde'];
        $data['fechaHasta']=$_POST['fechaHasta'];
        $data['cod_curso']=$_POST['cod_curso'];
        $data['cod_comision']=$_POST['cod_comision'];
        $data['tipo']=$_POST['tipo'];
        $data['matricula'] = $matricula;
        $this->load->view('rematriculaciones/frm_habilitar', $data);
    }

    public function habilitar(){
        $matricula = $_POST['matricula'];
        $texto = $_POST['observaciones'];
        $fechaDesde = $_POST['fechaDesde'];
        $fechaHasta = $_POST['fechaHasta'];
        $cod_curso = $_POST['cod_curso'];
        $cod_comision =  $_POST['cod_comision'];
        $tipo = $_POST['tipo'];
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $codigo = $this->Model_rematriculaciones->habilitarRematriculacion($matricula, $texto, $fechaDesde, $fechaHasta, $cod_usuario, $cod_curso, $cod_comision, $tipo);
        $arrResp = ($codigo > -1)?array('success' => 'success', 'codigo' => $codigo):array('error'=>'error');
        echo json_encode($arrResp);
    }

    public function exportar() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $crearColumnas = $this->crearColumnas();
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $idioma = $this->session->userdata('idioma');
        $todos = null;
        $valores = array();
        $todos = isset($_POST['requestType'])?true:false;
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $comision = isset($_POST['comision'])?$_POST['comision']:die("Falta comision");
        $fechaDesde = isset($_POST['fechaDesde'])?$_POST['fechaDesde']:die("Falta triemstre");
        $fechaHasta = isset($_POST['fechaHasta'])?$_POST['fechaHasta']:die("Falta triemstre");
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
        $valores = $this->Model_rematriculaciones->listarRematriculaciones($arrFiltros, 1, $idioma, $separador, $comision, $todos, $fechaDesde, $fechaHasta); 
        $exp = new export($_POST['tipo_reporte']);
        $arrTemp = array();
        foreach ($valores['aaData'] as $valor) {              
            $arrTemp[] = array(
                $valor[1],                  // codigo
                $valor[2],   // nombre y apellido
                $valor[3],
                $valor[4],                 // documento                    
                $valor[5],                     // localidad              
                $valor[6],
                $valor[7],
                $valor[8]);
        }
            
        $arrTitle = array(
            lang("matricula"),                
            $nombreApellido,
            lang('cuit'),               
            lang("fecha_alta"), 
            lang('deuda_total'),
            lang('cuotas_pendientes'),
            lang('estado_boletos'),
            lang('estado_rematricula')
        );
        $arrWidth = array(20, 85, 25, 35, 20, 35,35,35);
        $periodo = '';
        /*if (isset($_POST['fecha_desde']) && $_POST['fecha_desde'] != ''){
            $periodo .= lang("desde")." ".$_POST['fecha_desde'];
         }
         if (isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] != ''){
            $periodo .= lang("al")." ".$_POST['fecha_hasta'];
         }
         if ($periodo == ''){
            $periodo = lang("todas_las_fechas");
         }*/
        $usuario = $this->session->userdata("nombre");
        $filial = $this->session->userdata("filial");
        $arrInfo = array(
            array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
            array("txt" => "Informe estado rematriculacion", "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            //array("txt" => lang("usuario").": ".$usuario, "size" => "8", "align" => "R", "width" => 286, "height" => 4)                
        );
        $exp->setTitle($arrTitle);
        $exp->setContent($arrTemp);
        $exp->setPDFFontSize(8);
        $exp->setColumnWidth($arrWidth);
        $file = FCPATH."assents\img\logo.jpg";
        $exp->setLogo($file);
        $exp->setInfo($arrInfo);
        $exp->setContentHeight(6);
        $exp->setReportTitle($filial['nombre']." - ". "Reporte rematriculaciones");
        $exp->setMargin(2, 8);
        $exp->exportar();
    }

}
?>
