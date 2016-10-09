<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comisiones extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_comisiones", "", false, $config);
    }

    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        $claves = Array("PLANES_COMISION_GUARDADA","COMISION_GUARDADA","codigo_comision_cabecera","habilitada_comision_cabecera",
            "HABILITAR","BIEN","COMISION_INHABILITADA","COMISION_HABILITADA","INHABILITAR","INHABILITADA","HABILITADA",
            "cantInscriptos_comision","sin_salon","cargue_horarios", "desuso", "a_pasar");
        $valida_session = session_method();
        //modificacion ticket 5149->
        $idfil = $this->Model_comisiones->codigo_filial;
        $data['comisiones'] = $this->Model_comisiones->getComisiones($idfil,1);
        $this->load->helper("datatables");
        $data['columnas'] = getColumnsDatatable($this->crearColumnas());
        //<-modificacion ticket 5149
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('comisiones');
        
        $data['columns'] = $this->getColumns();	
        $data['page'] = 'comisiones/vista_comisiones';
        $data['seccion'] = $valida_session;
        $this->load->view('container', $data);
    }

    public function crearColumnas() {
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang('curso'), "campo" => 'general.cursos.nombre_' . get_idioma()),
            array("nombre" => lang('cantidad_inscriptos'), "campo" => 'inscriptos'),
            array("nombre" => lang('capacidad'), "campo" => 'cupo_disponible'),
            array("nombre" => lang('estado'), "campo" => 'estado', "sort" => false),
            array("nombre" => lang('habilitada_comision_cabecera'), "campo" => 'activa', "sort" => false, 'bVisible' => false));
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    //modificacion ticket 5149->
    public function condicionesfiltro(){
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_comisiones", "", false, $config);
        $valores = $this->Model_comisiones->getComisiones($filial["codigo"]);
        switch($_POST['campo']){
            case 'codigo':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'integer';
            break;
            case 'nombre':
                $filtros=array("a"=>array('id'=>"contiene",'display'=>"contiene","cant"=>1));
                $data['dataType'] = 'string';
            break;                    
            case 'curso':
                $filtros=array("a"=>array('id'=>"contiene",'display'=>"contiene","cant"=>1));
                $data['dataType'] = 'string';
            break;
            case 'cant_inscriptos':
                $filtros=array("a" => array('id'=>"es_igual_a",'display'=>"es igual a"),"b" => array('id'=>"mayor_o_igual_a",'display'=>"mayor o igual a"),"c" => array('id'=>"mayor",'display'=>"mayor"),"d" => array('id'=>"menor_o_igual_a",'display'=>"menor o igual a"),"e" => array('id'=>"menor",'display'=>"menor",),"cant"=>5);
                $data['dataType'] = 'integer';
            break;
            case 'capacidad':    
                $filtros=array("a" => array('id'=>"es_igual_a",'display'=>"es igual a"),"b" => array('id'=>"mayor_o_igual_a",'display'=>"mayor o igual a"),"c" => array('id'=>"mayor",'display'=>"mayor"),"d" => array('id'=>"menor_o_igual_a",'display'=>"menor o igual a"),"e" => array('id'=>"menor",'display'=>"menor",),"cant"=>5);
                $data['dataType'] = 'integer';
            break;
            case 'estado':
                $filtros=array("a"=> array('id'=>"es_igual_a",'display'=>"es igual a","cant"=>1));
                $data['dataType'] = 'string';
                break;
            default:
                $filtros = array("a"=>array('id'=>"-1",'display'=>"(".strtolower(lang('SELECCIONE_UNA_OPCION')).")","cant"=>1));
        }
        //deberia setear la variables de vista_comisiones para que recupere los valores correspondientes
        echo json_encode($filtros);
    //<-modificacion ticket 5149  
        
    }
    /**
     * retorna lista de comisiones para mostrar en index de main panel
     * @access public
     * @return json de listado de comisiones
     */
    public function listar() {
        //listar
        $crearColumnas = $this->crearColumnas();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);        
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sSearch"] = $this->input->post("sSearch");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        // modificacion ticket 5149 -> tomo los datos de los filtros enviados al cargar el dataTable
        $arrFiltros["codigo"] = isset($_POST['codigo']) && $_POST['codigo'] <> -1 ? $_POST['codigo'] : "";
        $arrFiltros["nombre"] = isset($_POST['nombre']) && $_POST['nombre'] <> -1 ? $_POST['nombre'] : "";
        $arrFiltros["curso"] = isset($_POST['curso']) && $_POST['curso'] <> -1 ? $_POST['curso'] : "";
        $arrFiltros["cant_inscriptos"] = isset($_POST['cant_inscriptos']) && $_POST['cant_inscriptos'] <> -1 ? $_POST['cant_inscriptos'] : "";
        $arrFiltros["estado"] = isset($_POST['estado']) && $_POST['estado'] <> -1 ? $_POST['estado'] : "";
        $arrFiltros["condiciones_cod"]  = isset($_POST['condiciones_cod']) && $_POST['condiciones_cod'] <> -1 ? $_POST['condiciones_cod'] : "";//se pone el mayor o igual,menor,mayor, etc
        $arrFiltros["condiciones_nom"]  = isset($_POST['condiciones_nom']) && $_POST['condiciones_nom'] <> -1 ? $_POST['condiciones_nom'] : "";
        $arrFiltros["condiciones_cur"]  = isset($_POST['condiciones_cur']) && $_POST['condiciones_cur'] <> -1 ? $_POST['condiciones_cur'] : "";
        $arrFiltros["condiciones_cant_ins"]  = isset($_POST['condiciones_cant_ins']) && $_POST['condiciones_cant_ins'] <> -1 ? $_POST['condiciones_cant_ins'] : "";
        $arrFiltros["condiciones_capac"]  = isset($_POST['condiciones_capac']) && $_POST['condiciones_capac'] <> -1 ? $_POST['condiciones_capac'] : "";
        $arrFiltros["capacidad"] = isset($_POST['capacidad']) && $_POST['capacidad'] <> -1 ? $_POST['capacidad'] : "";
        $arrFiltros["condiciones_est"]  = isset($_POST['condiciones_est']) && $_POST['condiciones_est'] <> -1 ? $_POST['condiciones_est'] : "";//se pone el mayor o igual,menor,mayor, etc
        $valores = $this->Model_comisiones->listaComisionesDatable($arrFiltros);
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $this->load->helper('comisiones');
           // $this->load->helper('alumnos');       
           // $nombreApellido = formatearNombreColumnaAlumno();
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            foreach ($valores['aaData'] as $valor) {
                
                $arrTemp[] = array(
                    $valor[0],                  // codigo
                    $valor[1],   // nombre y apellido
                    $valor[2],
                    $valor[3],                 // documento                    
                    $valor[4],                     // localidad              
                    $valor[6]);
            }
            
            $arrTitle = array(
                lang("codigo"),                
                lang('nombre'),
                lang('curso'),               
                lang('cantidad_inscriptos'), 
                lang('capacidad'),
                lang('estado_alumno')
            );
            $arrWidth = array(14, 35, 85, 35, 20, 20);
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
                array("txt" => "Informe comisiones alumnos", "size" => "8", "align" => "R", "width" => 286, "height" => 4)
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
            $exp->setReportTitle($filial['nombre']." - ".lang("reportes_de_comisiones"));
            $exp->setMargin(2, 8);
            $exp->exportar();
        } else {
            // <-modificacion ticket 5149 
            echo json_encode($valores);
        }
    }

    
    /**
     * carga la vista del formulario comisiones
     * @access public
     * @return vista form comisiones
     */
    public function frm_comisiones(){
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $arg["codigo_filial"] = $filial["codigo"];
        $cod_comision = $this->input->post('cod_comision');
        $this->load->model("Model_cursos", "", false, $arg);
        $this->form_validation->set_rules('cod_comision', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $comision = $this->Model_comisiones->getObjComision($cod_comision);
            $forzarPlan = $cod_comision > 0 ? $comision->cod_plan_academico : null;
            $cursosPlanes = $this->Model_cursos->getCursosHabilitados(true, null, 0, $forzarPlan);                        
            $data['comision'] = $comision;
            $data["cursos"] = $cursosPlanes;
            $data['periodos'] = array();
            if ($cod_comision != -1){
                $arrConfig = array(
                    "codigo" => $comision->cod_plan_academico,
                    "codigo_filial" => $filial["codigo"]
                );
                $this->load->model("Model_planes_academicos", "", false, $arrConfig);
                $periodos = $this->Model_planes_academicos->getPeriodosPlanAcademico($comision->cod_plan_academico,null,true);                
                $data['periodos'] = $periodos;
                $data['ciclos_lectivos'] = $this->Model_comisiones->getCicloLectivosFilial($comision->ciclo);
                $data['tiene_inscriptos'] = $this->Model_comisiones->getInscriptosComision($cod_comision);
                $arrHorarios = $comision->getHorarios(null, 1);
                $data['tieneHorarios'] = count($arrHorarios) > 0;
            }
            $data['prefijo'] = $comision->getPrefijo();
            $data['nombre'] = $comision->getNombre();
            $this->load->view('comisiones/frm_comisiones', $data);
        }
    }

    /**
     * Guarda todos los datos la comision
     * @access public
     * @return json de respuesta
     */
    public function guardar() {
        $this->load->library('form_validation');
        $cod_comision = $this->input->post('cod_comision');
        $nombreTemp = $this->input->post("prefijo")." ".$this->input->post("comision_descipcion");
        $this->form_validation->set_rules('ciclo', lang('ciclo'), 'max_length[255]');
        $this->form_validation->set_rules('periodos', lang('periodos_comisiones'), 'required|integer');
        $this->form_validation->set_rules('cod_plan_academico', lang('plan_academico'), 'required');       
        $resultado = '';
        if ($this->form_validation->run() == FALSE || strlen($this->input->post("comision_descipcion")) > 14) {
            $errors = validation_errors();
            //if (strlen($nombreTemp) > 20){
                //$cantidadPermitida = 19 - strlen($this->input->post("prefijo"));
                $cantidadPermitida = 14;
                $texto = str_replace("###", $cantidadPermitida, lang("alias_de_comision_demasiado_largo__caracteres_maximo"));
                $errors .= $texto;
            //}
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $modalidad = $this->input->post('modalidad') == '' ? 'normal' : $this->input->post('modalidad');
            $guardarComision = array(
                'prefijo' => $this->input->post(),
                'cod_tipo_periodo' => $this->input->post('periodos'),
                'ciclo' => $this->input->post('ciclo'),
                'estado' => Vcomisiones::getEstadoHabilitada(),
                'usuario_creador' => $this->session->userdata('codigo_usuario'),
                'fecha_creacion' => date("Y-m-d H:i:s"),
                'cod_plan_academico' => $this->input->post("cod_plan_academico"),
                'nombre' => $this->input->post('comision_descipcion'),
                'descripcion' => '',
                'modalidad' => $modalidad
            );            
            $resultado = $this->Model_comisiones->guardarComision($guardarComision, $cod_comision);
        }
        echo json_encode($resultado);
    }

    /**
     * cambia el estado de la comision
     * @access public
     * @return json con la comision que ah cambiado de estado
     */
    public function cambiarEstado() {
        $this->load->library('form_validation');
        $codigo = $this->input->post('codigo_comision');
        $this->form_validation->set_rules('codigo_comision', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $fechaDesde = $this->input->post("fecha_desde") ? $this->input->post("fecha_desde") : null;
            $resultado = $this->Model_comisiones->cambiarEstado($codigo, $fechaDesde);
            echo json_encode($resultado);
        }
    }

    /**
     * carga la vista del formulario asignar planes
     * @access public
     * @return vista form asignar planes
     */
    public function frm_asignarPlanes() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $this->load->helper('comisiones');
        $this->load->helper('array');
        $id_comision = $this->input->post('cod_comision');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->form_validation->set_rules('cod_comision', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {            
            $planAsignado = $this->Model_comisiones->getPlanesAsignados($id_comision);
            $planesNoAsignados = $this->Model_comisiones->getPlanesNoAsignados($id_comision);
            $planesNoAsignados = mergeArrayWithDistinctValue($planesNoAsignados,$planAsignado, 'codigo');
            $objcomision = $this->Model_comisiones->getObjComision($id_comision);
            $data["planAsignado"] = $planAsignado;
            $data["planesNoAsignados"] = $planesNoAsignados;
            $data['id_comision'] = $id_comision;
            $data['fecha_inicio_comision'] = $this->Model_comisiones->getFechaInicioComision($id_comision);
            $data['horario_comision'] = $this->Model_comisiones->formatearHorarioComisionPlanes($id_comision);
            $claves=array("validacion_ok", "planes", "planes_asignados", "no_activar_mostrar_web", 
                "no_habilitar_mostrar_web_2_comision", "debe_activar_mostrar_web");
            $data['langfRM'] = getLang($claves);
            $data['comision'] = $objcomision;
            $data['nombre_comision']  = $objcomision->nombre;
            $this->load->view('comisiones/asignarplanesdepagos', $data);
        }
    }

    /**
     * Guarda todos los datos de los planes
     * @access public
     * @return los planes a guardar
     */
    public function guardarPlanes() {
        $cod_comision = $this->input->post('cod_comision');
        $cod_plan_pago = $this->input->post('cod_plan_pago');
        $accion = $this->input->post('accion');
        $data_post['cod_comision'] = $cod_comision;
        $data_post['cod_plan_pago'] = $cod_plan_pago;
        $data_post['accion'] = $accion;
        $asignar = $this->Model_comisiones->guardarPlanes($data_post);
        echo json_encode($asignar);
    }

    public function setMostrarFinanciacionWeb(){
        $arrResp = array();
        $codComision = $this->input->post("cod_comision");
        $codPlan = $this->input->post("cod_plan");
        $activo = $this->input->post("activo");
        $resp = true;
        if (is_numeric($codComision) && is_numeric($codPlan) && is_numeric($activo)){
            $filial = $this->session->userdata('filial');
            $conexion = $this->load->database($filial['codigo'], true);
            $myComision = new Vcomisiones($conexion, $codComision);
            if ($activo == 1){
                $resp = $myComision->setMostrarFinanciacionWeb($codPlan);
            } else {
                $resp = $myComision->unSetMostrarFinanciacionWeb($codPlan);
            }
            if ($resp){
                $arrResp['codigo'] = 1;
                $arrResp['success'] = "success";
                $arrResp['transport'] = array(
                        "cod_comision" => $myComision->getCodigo(),
                        "cod_plan" => $codPlan,
                        "activo" => $activo
                    );
            } else {
                $arrResp['codigo'] = 0;
                $arrResp['error'] = "error";
                $arrResp['msgError'] = "Error al actualizar los registros<br>Intente más tarde";
            }
        } else {
            $arrResp['codigo'] = 0;
            $arrResp['error'] = "error";
            $arrResp['msgError'] = "Error de parametros";
        }
        echo json_encode($arrResp);
    }
    
    public function mostrarPlanWeb() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_comision',lang('comision'),'validarHorariosComision');
        $resultado = '';
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            $resultado = array(
                "codigo" => 0,
                "msgError" => $errors
            );
        } else {
            $cod_comision = $this->input->post('cod_comision');
            $cod_plan_pago = $this->input->post('cod_plan_pago');
            $estado = $this->input->post('accion');
            $data_post['cod_comision'] = $cod_comision;
            $data_post['cod_plan_pago'] = $cod_plan_pago;
            $data_post['accion'] = $estado;
            $resultado = $this->Model_comisiones->mostrarPlanWeb($data_post);
        }        
        echo json_encode($resultado);
    }

    public function getPlanes($idFilial, $idComision) {
        $arrResp = $this->Model_comisiones->getPlanes($idFilial, $idComision);
        echo json_encode($arrResp);
    }

    public function getComisiones($idFilial, $activas) {
        if ($activas == -1){
            $activas = null;
        } else if ($activas == 1){
            $activas = Vcomisiones::getEstadoHabilitada();
        } else {
            $activas = Vcomisiones::getEstadoInhabilitada();
        }
        $arrResp = $this->Model_comisiones->getComisiones($idFilial, $activas);
        echo json_encode($arrResp);
    }

    public function getComisionesSelect() {
        $idFilial = $this->session->userdata('filial');
        //var_dump($idFilial['codigo']);
        $activas = 'habilitado';
        //$activas = Vcomisiones::getEstadoHabilitada();
        $arrResp = $this->Model_comisiones->getComisiones($idFilial['codigo'], $activas);
        echo json_encode($arrResp);
        //var_dump($arrResp); die();
    }

    public function getPlanesAcademicos() {
        $cod_curso = $this->input->post('cod_curso');
        $arrPlanesAcademicos = $this->Model_comisiones->getPlanesAcademicos($cod_curso);
        echo json_encode($arrPlanesAcademicos);
    }

    public function getPeriodos() {
        $cod_plan_academico = $this->input->post('codigo');
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo" => $cod_plan_academico,
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_planes_academicos", "", false, $arrConfig);
        $periodos = $this->Model_planes_academicos->getPeriodosPlanAcademico($cod_plan_academico,null, true);        
        echo json_encode($periodos);
    }

    public function getPrefijo() {
        session_method();        
        $cod_plan_academico = $this->input->post('cod_plan_academico');
        $cod_periodo = $this->input->post('periodos');
        $ciclo = $this->input->post('ciclo');
        $cod_comision = $this->input->post('cod_comision');
        $modalidad = $this->input->post('modalidad') == 'intensiva' ? $this->input->post('modalidad') : '';
        $resultado = $this->Model_comisiones->getPrefijo($cod_comision,$cod_plan_academico, $cod_periodo, $ciclo,$modalidad);
        echo json_encode($resultado);
    }

    public function frm_comunicadoEmail() {
        $this->load->library('form_validation');
        $this->load->helper('comisiones');
        $cod_comision = $this->input->post('cod_comision');
        $this->form_validation->set_rules('cod_comision', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $myComision = $this->Model_comisiones->getComision($cod_comision);
            $data['materias'] = $this->Model_comisiones->getMateriasComision($cod_comision);
            $data['comision'] = $myComision;
            $data['nombre_comision'] = $myComision->nombre;
            $data['cod_comision'] = $cod_comision;
            $data['cantAlumnos'] = $this->Model_comisiones->getAlumnosComision($cod_comision);
            $this->load->view('comisiones/frm_comunicadoEmail', $data);
        }
    }

    public function getAlumnosMateriaComision() {
        $cod_comision = $this->input->post('cod_comision');
        $cod_materia = $this->input->post('cod_materia');
        $aluComMat = $this->Model_comisiones->getAlumnosMateriaComision($cod_comision, $cod_materia);
        echo json_encode($aluComMat);
    }

    public function guardarComunicados() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->load->model("Model_comunicados", "", false, $config);
        $this->form_validation->set_rules('mensaje', lang('mensaje_comunicados'), 'min_length[15]');
        $this->form_validation->set_rules('alumnos', lang('alumnosComunicados'), 'required');
        $this->form_validation->set_rules('asunto', lang('asunto_comunicado'), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $mensaje = $this->input->post('mensaje');            
            $alumnosMandarComunicado = $this->input->post('alumnos');
            $asunto = $this->input->post('asunto');
            $cod_comision = $this->input->post('cod_comision');
            $cod_materia = $this->input->post('codMateria');
            if (count($alumnosMandarComunicado) > 0){
                $resultado = $this->Model_comunicados->guardarComunicados($mensaje, $alumnosMandarComunicado, $cod_usuario, $asunto, $cod_comision, $cod_materia);
            } else {
                $resultado = array(
                    "codigo" => '0',
                    "respuesta" => lang('alumnos_inscriptos_sin_email')
                );
            }
        }
        echo json_encode($resultado);
    }

    public function listarComunicadosEmail() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_comunicados", "", false, $config);
        $cod_comision = $this->input->post('cod_comision');
        $filtro = $this->input->post('cod_materia');
        $length = 5;
        $arrFiltros["iDisplayStart"] = $_POST['iDisplayStart'] != '' ? $_POST['iDisplayStart'] : 0;
        $arrFiltros["iDisplayLength"] = isset($length) ? $length : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $valores = $this->Model_comunicados->getComunicadosEmailComisionMateria($cod_comision, $arrFiltros, $filtro);
        echo json_encode($valores);
    }

    public function getAlumnosComunicado() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_comunicados", "", false, $config);
        $cod_comision = $this->input->post('cod_comision');
        $cod_materia = $this->input->post('cod_materia');
        $cod_mensaje = $this->input->post('idAsunto');
        $alumnosComunicados = $this->Model_comunicados->getAlumnosComunicado($cod_comision, $cod_materia, $cod_mensaje);
        echo json_encode($alumnosComunicados);
    }
    
    public function getComisionesActivasWeb() {
        $cod_filial = $this->input->post('id_filial');
        $simbolo = $this->input->post('simbolo_moneda');
        $moneda = $this->input->post('id_moneda');	        
//        $cod_filial = "20";
//        $simbolo = "$";
//        $moneda = "1";
        $resultado = $this->Model_comisiones->reporteComisionesActivas($cod_filial, $simbolo, $moneda);
        echo json_encode($resultado);
    }
    
    public function guardarPeriodoProrroga(){
        $cod_comision = $this->input->post('cod_comision');
        $valor = $this->input->post('valor');
        $respuesta = $this->Model_comisiones->guardarPeriodoProrroga($cod_comision,$valor);
        echo json_encode($respuesta);
    }
    
    public function getCiclos(){
        $arrRespuesta = $this->Model_comisiones->getCicloLectivosFilial(null, date("Y-m-d"));
        echo json_encode($arrRespuesta);
    }
    
    public function cambios_periodos(){
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $data = array();
        $claves = Array("sin_registros", "algunas_comisiones_seleccionadas_no_poseen_comision_de_destino", "validacion_ok", 
            "debe_seleccionar_alguna_comision_para_realizar_el_pasaje", "recuperando", "alumnos_con_estado_academico_cursando");
        $data['lang'] = getLang($claves);
        if ($this->input->post('cod_comision') && $this->input->post('cod_comision') <> -1){
            $codComision = $this->input->post('cod_comision');
            $data['validar_cursando'] = false;
            $myComision = new Vcomisiones($conexion, $codComision);
            $codTipoPeriodo = $myComision->estado == Vcomisiones::getEstadoAPasar() ? $myComision->cod_tipo_periodo + 1 : $myComision->cod_tipo_periodo;
            $arrComisiones = Vcomisiones::getComisiones($conexion, array("comisiones.estado" => Vcomisiones::getEstadoHabilitada()), null, false, null, null, $myComision->cod_plan_academico, $codTipoPeriodo);
            $data['arrComisiones'] = $arrComisiones;
        } else {
            $codComision = null;
            $data['validar_cursando'] = true;            
        }
        $data['cod_comision_origen'] = $codComision;
        $data['comisiones_cambiar'] = $this->Model_comisiones->getComisionesCambiar($conexion, $codComision);

        $data['debug']= $this->input->post('cod_comision');
        $data['fecha_desde'] = formatearFecha_pais(date("Y-m-d"));
        $this->load->view('comisiones/frm_cambio_periodo', $data);
    }
    
    //Ticket 4581 -mmori- cambio de comision inicio
    public function cambios_comision()
    {
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $data = array();
        $claves = Array("sin_registros", "algunas_comisiones_seleccionadas_no_poseen_comision_de_destino", "validacion_ok", 
            "debe_seleccionar_alguna_comision_para_realizar_el_pasaje", "recuperando", "alumnos_con_estado_academico_cursando");
        $data['lang'] = getLang($claves);
        
        $codComision = $this->input->post('cod_comision');
        
        $data['validar_cursando'] = false;
        $myComision = new Vcomisiones($conexion, $codComision);
        $codTipoPeriodo = $myComision->estado == Vcomisiones::getEstadoAPasar() ? $myComision->cod_tipo_periodo + 1 : $myComision->cod_tipo_periodo;
        $arrComisiones = Vcomisiones::getComisiones($conexion, array("comisiones.estado" => Vcomisiones::getEstadoHabilitada()), null, false, null, null, $myComision->cod_plan_academico, $codTipoPeriodo);
        $data['arrComisiones'] = $arrComisiones;
        
        $data['cod_comision_origen'] = $codComision;
        $data["nombre_comision_origen"] = $myComision->nombre;
        $data['comisiones_cambiar'] = $this->Model_comisiones->getComisionesCambiar($conexion, $codComision);
        
        $data['totaAlumnosOrigen'] = $this->get_alumnos_cursando(true);
        
        
        
        $data['debug']= $this->input->post('cod_comision');
        $data['fecha_desde'] = formatearFecha_pais(date("Y-m-d"));
        $this->load->view('comisiones/frm_cambio_comision', $data);
    }
    
    public function guardar_cambios_comision()
    {
        $codUsuario = $this->session->userdata("codigo_usuario");
        $fechaDesde = $this->input->post("fecha_desde");
        $arrComisiones = $this->input->post("comisiones");
        
        $this->load->helper("alumnos");
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        
        $retorno = array();
        // Se quito validacion Cursando para poder cambiar la comision
        if (!validaciones::validarComisionesParaPasajePeriodo($conexion, $arrComisiones, true, true, true, true, false, $retorno))
        {
            $arrResp['error'] = $retorno;
        }
        else
        {
            $conexion->trans_begin();
            foreach ($arrComisiones as $comisiones)
            {
                $myComision = new Vcomisiones($conexion, $comisiones['origen']);
                $myComision->pasarComision($comisiones['destino'], $codUsuario, $fechaDesde);
            }
            if ($conexion->trans_status())
            {
                $arrResp['success'] = "success";
                $arrResp['comisiones'] = $arrComisiones;
                $conexion->trans_commit(); // cambiar a trans_commit al terminar el debug
            }
            else
            {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                $conexion->trans_rollback();
            }
        }
        
        echo json_encode($arrResp);
    }
    //Ticket 4581 -mmori- cambio de comision fin
    
    public function getComisionesPeriodo(){
        $arrResp = array();
        $codComision = $this->input->post("cod_comision");
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $myComision = new Vcomisiones($conexion, $codComision);
        $codTipoPeriodo = $myComision->estado == Vcomisiones::getEstadoAPasar() ? $myComision->cod_tipo_periodo + 1 : $myComision->cod_tipo_periodo;
        $arrComisiones = Vcomisiones::getComisiones($conexion, array("comisiones.estado" => Vcomisiones::getEstadoHabilitada()), null, false, null, null, $myComision->cod_plan_academico, $codTipoPeriodo);
        $arrResp['data']['comisiones'] = $arrComisiones;
        echo json_encode($arrResp);
    }
    
    public function pasar_comision(){
    	
        $codUsuario = $this->session->userdata("codigo_usuario");
        $fechaDesde = $this->input->post("fecha_desde");
        $this->load->helper("alumnos");
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $arrComisiones = $this->input->post("comisiones");
        $retorno = array();
        // Se quito validacion Cursando para poder cambiar la comision
        if (!validaciones::validarComisionesParaPasajePeriodo($conexion, $arrComisiones, true, true, true, true, false, $retorno)){
            $arrResp['error'] = $retorno;
        } else {
            $conexion->trans_begin();
            foreach ($arrComisiones as $comisiones){
                $myComision = new Vcomisiones($conexion, $comisiones['origen']);
                $myComision->pasarComision($comisiones['destino'], $codUsuario, $fechaDesde);
            }
            if ($conexion->trans_status()){
                $arrResp['success'] = "success";
                $arrResp['comisiones'] = $arrComisiones;
                $conexion->trans_commit(); // cambiar a trans_commit al terminar el debug
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
                $conexion->trans_rollback();
            }
        }
        
        echo json_encode($arrResp);
    }
    
    public function get_alumnos_cursando($count = false){
        $arrResp = array();
        $filial = $this->session->userdata("filial");
        $conexion = $this->load->database($filial['codigo'], true);
        $this->load->helper("alumnos");
        $codComision = $this->input->post("cod_comision");
        $arrResp['alumnos'] = Vmatriculas_inscripciones::getInscripcionesComision($conexion, $codComision, null, true, array(Vestadoacademico::getEstadoCursando(),Vestadoacademico::getEstadolibre(), Vestadoacademico::getEstadoRegular(),Vestadoacademico::getEstadoHomologado(), Vestadoacademico::getEstadoRecursa(),Vestadoacademico::getEstadoAprobado()));
        if($count) return count($arrResp['alumnos']);
        echo json_encode($arrResp);
    }
     // modificacion ticket 5149 ->
    public function getFiltrosCondiciones() {
        $reportName = $this->input->post("report_name");
        $fieldName = $this->input->post("field_name");
        $arrResp = $this->Model_reportes->getFiltrosCondiciones($reportName, $fieldName);
        echo json_encode($arrResp);
    }
     // <-modificacion ticket 5149 
}