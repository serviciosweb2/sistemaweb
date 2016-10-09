<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cursos extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_cursos", "", false, $config);
    }

    public function index() {
        $data['page'] = 'cursos/vista_cursos';
        $data['seccion'] = $this->seccion;
        $claves = array("habilitado_curso", "estado", "desuso", "codigo", "curso_corto", "HABILITAR", "INHABILITAR", "codigo_curso", "SI", "NO", "habilitar-curso", "deshabilitar-curso", "confirmar_cambiar_estado_curso", "HABILITADO", "INHABILITADO");
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('cursos');
        $data['columns'] = $this->getColumns();
        $this->load->view('container', $data);
    }

    public function crearColumnas() {

        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo')
            , array("nombre" => lang('nombre'), "campo" => 'nombre_' . get_idioma())
            , array("nombre" => lang('curso_corto'), "campo" => 'curso_corto')
            , array("nombre" => lang('abreviatura_curso'), "campo" => 'abreviatura')
            , array("nombre" => lang('estado'), "campo" => 'baja', "sort" => true)
            , array("nombre" => lang('habilitado_curso'), "campo" => 'baja', "sort" => FALSE, 'bVisible' => FALSE)
            , array("nombre" => lang('en_uso'), "campo" => 'uso', "sort" => FALSE, 'bVisible' => FALSE)
            , array("nombre" => lang('cant_horas'), "campo" => 'cant_horas', "sort" => true));
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    public function listar() {
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sSearch"] = $this->input->post("sSearch");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";

        $valores = $this->Model_cursos->listaCursosDatable($arrFiltros);

        echo json_encode($valores);
    }

    public function habilitarCurso() {

        $curso = $this->input->post("codigo");
        $resultado = $this->Model_cursos->cambioEstadoCurso($curso);
        echo json_encode($resultado);
    }

    public function form_materias() {
        $this->load->library('form_validation');
        $codcurso = $this->input->post('codigo_curso');
        $this->form_validation->set_rules('codigo_curso', lang('codigo'), 'numeric');
        $filial = $this->session->userdata('filial');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $arrfilial = array("filial" => $filial);
            $this->load->model("Model_cursos", "", false, $arrfilial);

            $planesacademicos = $this->Model_cursos->getPlanesAcademicos($codcurso);

            $arrconfig = array("codigo_filial" => $filial['codigo']);
            $this->load->model("Model_planes_academicos", "", false, $arrconfig);
            foreach ($planesacademicos as $key => $plan) {
                $data['plan'][$key] = $plan;
                $data['plan'][$key]['periodos'] = $this->Model_planes_academicos->getMateriasDatatable($plan['codigo'], true);
            }
            $curso = $this->Model_cursos->getArrCurso($codcurso);

            $data["nombreCurso"] = $curso['nombre_' . get_idioma()];
            $this->load->view('cursos/vermaterias', $data);
        }
    }

    public function getMateriasPlan() {
        $filial = $this->session->userdata('filial');
        $cod_plan_academico = $this->input->post('cod_plan_academico');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_plan_academico', lang('codigo'), 'numeric');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {

            $arrconfig = array("codigo_filial" => $filial['codigo']);
            $this->load->model("Model_planes_academicos", "", false, $arrconfig);
            $data["periodos"] = $this->Model_planes_academicos->getMateriasDatatable($cod_plan_academico);
            $objcurso = $this->Model_planes_academicos->getCurso($cod_plan_academico);
            $curso = $this->Model_cursos->getArrCurso($objcurso->getCodigo());

            $data["nombreCurso"] = $curso['nombre_' . get_idioma()];

            $this->load->view('cursos/vermaterias', $data);
        }
    }

    public function getCursosHabilitados() {
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;

        $arrResp = $this->Model_cursos->getCursosHabilitadosFilial($idFilial, $arrLimit, $arrSort, $search, $searchField);
        echo json_encode($arrResp);
    }

    public function getComisiones($idFilial, $curso) {
        $arrResp = $this->Model_cursos->getComisiones($idFilial, $curso);
        echo json_encode($arrResp);
    }

//  REVISAR NO VA MAS  public function getMaterias($codCurso) {
//
//        echo json_encode($this->Model_cursos->getMaterias($codCurso));
//    }

    public function frm_abreviatura() {
        $this->load->library('form_validation');
        $cod_curso = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $claves = array("validacion_ok");
            $data['langFrm'] = getLang($claves);
            $data['curso'] = $this->Model_cursos->getCurso($cod_curso);
            $data['abreviaturaCurso'] = $this->Model_cursos->getAbreviaturaCursoHabilitado($cod_curso);

            $this->load->view('cursos/frm_abreviatura', $data);
        }
    }

    public function guardarAbreviatura() {
        $cod_curso_habilitado = $this->input->post('cod_curso_habilitado');
        $abreviatura = $this->input->post('abreviatura');
        $estado = $this->input->post('estado');
        $this->load->library('form_validation');
        $retorno = '';
//        $this->form_validation->set_rules('abreviatura',lang('abreviatura_curso'),'required|max_length[9]');

        $this->form_validation->set_rules('abreviatura', lang('abreviatura_curso'), 'required|max_length[7]|validarAbreviaturaCurso[' . $cod_curso_habilitado . ']');

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $retorno = array(
                'codigo' => 0,
                'msgerrors' => $errors
            );
        } else {
            $retorno = $this->Model_cursos->guardarAbreviatura($cod_curso_habilitado, $abreviatura);
        }

        echo json_encode($retorno);
    }
    
    /* La siguiente function esta siendo accedida por un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function getCategorias(){
        $conexion = $this->load->database("default", true);
        $condiciones = array();
        if ($this->input->post("estado")) $condiciones['estado'] = $this->input->post("estado");
        $arrCategorias = Vcursos_categorias::listarCursos_categorias($conexion, $condiciones);
        echo json_encode($arrCategorias);
    }
    
    /* La siguiente function esta siendo accedida por un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function getSubcategorias(){
        $conexion = $this->load->database("default", true);
        $condiciones = array();
        if ($this->input->post("estado")) $condiciones['estado'] = $this->input->post("estado");
        if ($this->input->post("id_categoria")) $condiciones['id_categoria'] = $this->input->post("id_categoria");
        $arrSubcategorias = Vcursos_subcategorias::listarCursos_subcategorias($conexion, $condiciones);
        echo json_encode($arrSubcategorias);
    }
 
    /* La siguiente function esta siendo accedida por un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function listar_cursos(){
        $conexion = $this->load->database("default", true);        
        $condiciones = array();
        if ($this->input->post("cod_categoria")) $condiciones['cod_categoria'] = $this->input->post('cod_categoria');
        if ($this->input->post("cod_subcategoria")) $condiciones['cod_subcategoria'] = $this->input->post("cod_subcategoria");
        if ($this->input->post("estado")) $condiciones['cursos.estado'] = $this->input->post("estado");
        if ($this->input->post("codigo")) $condiciones['codigo'] = $this->input->post("codigo");
        if ($this->input->post("tipo_curso")) $condiciones['tipo_curso'] = $this->input->post("tipo_curso");
        if ($this->input->post("updated_at")) $condiciones['updated_at >='] = $this->input->post("updated_at");
        if ($this->input->post("id_pais")) $condiciones['id_pais'] = $this->input->post("id_pais");
        $conexion->select("general.cursos_categorias.nombre AS categoria_nombre");
        $conexion->select("general.cursos_subcategorias.nombre AS subcategoria_nombre");
        $conexion->join("general.cursos_categorias", "general.cursos_categorias.id = general.cursos.cod_categoria");
        $conexion->join("general.cursos_subcategorias", "general.cursos_subcategorias.id = general.cursos.cod_subcategoria", "left");
        if($this->input->post("id_pais")) {
            $conexion->join("general.cursos_paises", "general.cursos_paises.id_curso = general.cursos.codigo");
        }
        $arrResp = Vcursos::listarCursos($conexion, $condiciones);
        if ($this->input->post("agregar_cursos_paises") && !$this->input->post("id_pais")){
            foreach ($arrResp as $key => $curso){
                $myCurso = new Vcursos($conexion, $curso['codigo']);
                $arrTemp = $myCurso->getCursosPaises();
                $arrHoras = array();
                foreach ($arrTemp as $horas) {
                    $arrHoras[$horas['id_pais']] = $horas;
                }
                $arrResp[$key]["cursos_paises"] = $arrHoras;
            }
        }
        echo json_encode($arrResp);
    }
    
    /* La siguiente function esta siendo accedida por un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */
    function guardar_cursos(){
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();
        $arrResp = array();
        $myCurso = new Vcursos($conexion, $this->input->post("codigo"));
        $myCurso->cod_categoria = $this->input->post("cod_categoria");
        $myCurso->cod_subcategoria = $this->input->post("cod_subcategoria");
        $myCurso->descripcion_corta_es = $this->input->post("descripcion_corta_es");
        $myCurso->descripcion_corta_in = $this->input->post("descripcion_corta_in");
        $myCurso->descripcion_corta_pt = $this->input->post("descripcion_corta_pt");
        $myCurso->descripcion_es = $this->input->post("descripcion_es");
        $myCurso->descripcion_in = $this->input->post("descripcion_in");
        $myCurso->descripcion_pt = $this->input->post("descripcion_pt");
        $myCurso->descripcion_venta_es = $this->input->post("descripcion_venta_es");
        $myCurso->descripcion_venta_in = $this->input->post("descripcion_venta_in");
        $myCurso->descripcion_venta_pt = $this->input->post("descripcion_venta_pt");
        $myCurso->estado = $this->input->post("estado");
        $myCurso->listar_en_asuntos_mails_consultas = $this->input->post("listar_en_asuntos_mails_consultas");
        $myCurso->nombre_es = $this->input->post("nombre_es");
        $myCurso->nombre_in = $this->input->post("nombre_in");
        $myCurso->nombre_pt = $this->input->post("nombre_pt");
        $myCurso->tags = $this->input->post("tags");
        $myCurso->tipo_curso = $this->input->post("tipo_curso");
        $myCurso->guardarCursos();
        $cursos_paises = $this->input->post("cursos_paises");
        if ($cursos_paises && is_array($cursos_paises)){
            foreach ($cursos_paises as $codPais => $cursoPais){
                $myCurso->setCursoPais($codPais, $cursoPais['horas'], $cursoPais['meses']);
            }
        }
        if ($conexion->trans_status()){
            $arrResp['success'] = "success";
            $arrResp['codigo'] = $myCurso->getCodigo();
            $conexion->trans_commit();
        } else {
            $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            $conexion->trans_rollback();
        }
        echo json_encode($arrResp);
    }
    
    public function get_materias(){
        $codCurso = $this->input->post("cod_curso") && $this->input->post("cod_curso") <> -1 ? $this->input->post("cod_curso") : null;
        $arrResp = $this->Model_cursos->getMaterias($codCurso);
        echo json_encode($arrResp);
    }
    
	public function api_getDatosDeCurso() {
		$codigo_curso = $this->input->post("codigo_curso");

		//$codigo_curso = $_GET["codigo_curso"];
		
		$conexion = $this->load->database("general", true);
        $condiciones = array('codigo' => $codigo_curso);
        $curso = Vcursos::listarCursos($conexion, $condiciones);
		
        echo json_encode($curso[0]);
	}

    public function api_getCursosFaqCampus()
    {
        $conexion = $this->load->database($this->input->post("filial"), true);
        $condiciones = array();
        if ($this->input->post("codigo_usuario")) $condiciones['codigo_usuario'] = $this->input->post("codigo_usuario");
        if ($this->input->post("tipo_usuario")) $condiciones['tipo_usuario'] = $this->input->post("tipo_usuario");
        $cursos = Vcursos::getCursosFaqCampus($conexion, $condiciones);
        echo json_encode($cursos);
    }

    public function api_getCursosGruposCampus()
    {
        $conexion = $this->load->database($this->input->post("filial"), true);
        $idioma = $this->input->post("idioma");
        $cursos = Vcursos::getCursosConComisionesActivas($conexion, $idioma);
        echo json_encode($cursos);
    }

    public function api_getComisionesCursosGruposCampus()
    {
        if ($this->input->post("idFilial")) $idFilial = $this->input->post("idFilial");
        if ($this->input->post("curso")) $codigoCurso = $this->input->post("curso");
        $habilitadas = 1;
        $comisiones = $this->Model_cursos->getComisiones($idFilial, $codigoCurso, $habilitadas);

        echo json_encode($comisiones);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

