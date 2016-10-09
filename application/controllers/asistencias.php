<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Asistencias extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_asistencias", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
    }

    public function index() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_cursos", "", false, $config);
        $cursosHabilitados = $this->Model_cursos->getCursosConComisionesActivas();
        /* TODO: Reemplazar este array por datos en la DB */
        $data['filialesActivas'] = array(51, 18, 67, 64, 52, 70, 73, 66, 23, 19, 21, 56, 16, 38, 53, 28, 61, 50, 39, 22, 3, 69, 24, 1, 2, 10, 30, 93, 55, 40, 62, 13, 20);
        $claves = array('codigo', 'nombre', 'asistencias', 'ultimasAsistencias', 'seleccione_horario_cursado',
            'marcar_todos_como', 'repetir_estado', 'ayer', 'hoy', 'maniana', 'la_semana_pasada', 'a_las', '_idioma', 
            'validacion_ok', 'no_hay_dias_por_ver', 'no_tiene_dias_cargados', 'detalles', 'seleccionar', 'siguiente', 
            'anterior', 'ultima', 'cargar_profesor', 'cargar_un_profesor', 'matricula', 'estado_academico_matriculas');
        $data['lang'] = getLang($claves);
        $data['cursos'] = $cursosHabilitados;
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'asistencias/vista_asistencias';
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }

    public function getAsistenciasReporte() {
        $idFilial = isset($_POST['id_filial']) ? $_POST['id_filial'] : $this->session->userdata('filial');
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $codComision = isset($_POST['cod_comision']) ? $_POST['cod_comision'] : null;
        $codMateria = isset($_POST['cod_materia']) ? $_POST['cod_materia'] : null;
        $codMatricula = isset($_POST['cod_matricula']) ? $_POST['cod_matricula'] : null;
        $config = array("codigo_filial" => $idFilial);
        $this->load->model("Model_horarios", "", false, $config);
        $arrResp = $this->Model_horarios->getAsistencias($idFilial, $codMateria, $codComision, $fechaDesde, $fechaHasta, $codMatricula);
        echo json_encode($arrResp);
    }

    /**
     * Retorna json de comisiones para un curso.
     * @access public
     * @return json de comisiones.
     */
    public function getComisionesCurso() {
        $filial = $this->session->userdata('filial');
        $config = array(
            "codigo_filial" => $filial['codigo']
        );
        $idfilial = $filial['codigo'];
        $cod_curso = $this->input->post('codigo');
        $this->load->model("Model_cursos", "", false, $config);
        $estado = array( Vcomisiones::getEstadoHabilitada(), Vcomisiones::getEstadoAPasar(), Vcomisiones::getEstadoDesuso());
        $comisionesCurso = $this->Model_cursos->getComisiones($idfilial, $cod_curso,null,$estado);
        echo json_encode($comisionesCurso);
    }

    /**
     * Retorna json de materias para una comision.
     * @access public
     * @return json de Materias.
     */
    public function getMateriasComision() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $cod_comision = $this->input->post('codigo');
        $this->load->model('Model_comisiones', "", false, $config);
        $materiasComision = $this->Model_comisiones->getMateriasHorariosComision($cod_comision);
        echo json_encode($materiasComision);
    }

    /**
     * Retorna json de alumnos inscriptos a horarios de materias para poder pasar la asistencia.
     * @access public
     * @return json de alumnos.
     */
    public function getAlumnosAsistencias() {
        $cod_comision = $this->input->post('codigo');
        $cod_materia = $this->input->post('cod_materia');
        $fecha = $this->input->post('fecha');
        $cod_horario = $this->input->post('cod_horario');
        $alumnosAsistencias = $this->Model_asistencias->getInscriptosAsistencias($cod_materia, $cod_comision, $fecha, $cod_horario);
        
        echo json_encode($alumnosAsistencias);
    }

    /**
     * Retorna json de los dias de cursado de la comision.
     * @access public
     * @return json de dias de cursado.
     */
    public function getDiasCursadoComision() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $cod_comision = $this->input->post('codigo');
        $cod_materia = $this->input->post('cod_materia');
        $this->load->model('Model_comisiones', "", false, $config);
        $diasCursadoComision = $this->Model_comisiones->getDiasCursadoComision($cod_comision, $cod_materia);
        echo json_encode($diasCursadoComision);
    }

    /**
     * Retorna array de asistencias con su respectiva traduccion.
     * @access public
     * @return array de asistencias.
     */
    public function getEstadoAsistencias() {
        $asistencias = $this->Model_asistencias->getArrayEstadoAsistencias();
        echo json_encode($asistencias);
    }

    /**
     * Guarda las asistencia del alumno
     * @access public
     * @return json de respuesta
     */
    public function guardarAsistencias() {
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $arrPost = $this->input->post('alumnos');
        $fecha = $this->input->post('fecha');
        $comision = $this->input->post("comisiones");
        $materia = $this->input->post("clases");
        $resultado = '';
        $asistencias = 1;
        
        $c = 0;
        
        foreach ($arrPost as $value) {
            if($value['estado'] == '') 
                $c ++;
        }
        
 
        if($c>0 && $c < count($arrPost)){
            $resultado = array(
                "codigo" => 0,
                "msgerror" => "Debes cargar todas las asistencias" //lang('no_puede_cargarse_asistencias_futuras')
            );
            echo json_encode($resultado);
            return;
        }
        
        if($c == count($arrPost)){
            $asistencias = 0;
        }

        
        if ($fecha > date('Y-m-d')) {
            $resultado = array(
                "codigo" => 0,
                "msgerror" => lang('no_puede_cargarse_asistencias_futuras')
            );
        } else {
            $codEstadoAcademico = $this->input->post("cod_estado_academico") ? $this->input->post("cod_estado_academico") : null;
            $resultado = $this->Model_asistencias->guardarAsistencias($arrPost, $fecha, $cod_usuario, $codEstadoAcademico, $comision, $materia, $asistencias);
        }
        
        
        echo json_encode($resultado);
    }
    
    public function actualizar_estadoacademico () {
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $arrPost = $this->input->post('alumnos');
        $fecha = $this->input->post('fecha');
        $comision = $this->input->post("comisiones");
        $materia = $this->input->post("clases");
        $resultado = '';
        
        if (empty($arrPost)){
           return ;
        }
        
        $resp = $this->Model_asistencias->actualizar_estadoacademico($arrPost, $cod_usuario);
        
        if($resp != true)
            echo json_encode ($resp);
        
        $resp = array("codigo" => 1);
        echo json_encode ($resp);
    }

    /**
     * Retorna json de las asistencias del alumno.
     * @access public
     * @return json de las asistencias del alumno.
     */
    public function getDetallesAsistencias() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $cod_mat_horarios = $this->input->post('codigo');
        $fecha = $this->input->post('fecha');
        $this->load->model("Model_matriculas_horarios", "", false, $config);
        $detalleAsistencia = $this->Model_matriculas_horarios->getDetallesAsistencias($cod_mat_horarios, $fecha);
        echo json_encode($detalleAsistencia);
    }

    public function frm_asistenciasAlumno() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model('Model_alumnos', "", false, $config);
        $this->load->helper("alumnos");
        $this->load->library('form_validation');
        $codEstadoAcademico = $this->input->post("cod_estado_academico");
        if (!$codEstadoAcademico){
            $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        } else {
            $this->form_validation->set_rules('cod_estado_academico', lang('codigo'), 'numeric');
        }

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            if ($codEstadoAcademico){
                $idioma = $this->session->userdata("idioma");
                $idiomaNombre = "nombre_$idioma";
                $conexion = $this->load->database($filial['codigo'], true);
                $arrEstadoAcademico = Vestadoacademico::getEstadoAcademicoDetalles($conexion, "es", null, null, null, null, false, $codEstadoAcademico);
                $cod_alumno = $arrEstadoAcademico[0]['alumno_codigo']; 
                $myMateria = new Vmaterias($conexion, $arrEstadoAcademico[0]['materia_codigo']);
                $data['matricula_periodo_seleccionar'] = $arrEstadoAcademico[0]['matricula_periodo_codigo'];
                $data['materia_seleccionar'] = $codEstadoAcademico;                
                $data['materia'] = $myMateria->$idiomaNombre;                
            } else {
                $cod_alumno = $this->input->post('codigo');
                $data['matricula_periodo_seleccionar'] = -1;
                $data['materia_seleccionar'] = -1;
                $data['materia'] = '';
            }            
            $data['matriculas'] = $this->Model_alumnos->getMatriculasPeriodos($cod_alumno, null, 'migrado');
            if (count($data['matriculas']) == 0) {
                echo lang('sin_matriculas_asistencias');
            } else {
                $data['nombre_alumno'] = $this->Model_alumnos->getNombreAlumno($cod_alumno);
                $arrlang = array('BIEN' => lang('BIEN'), 'registros_guardados_correctamente' => lang('registros_guardados_correctamente'), 'no_esta_inscripto_a_ningun_horario' => lang('no_esta_inscripto_a_ningun_horario'));
                $data['lang'] = json_encode($arrlang);
                $data['cod_alumno'] = $cod_alumno;
                $data['cod_estado_academico'] = $codEstadoAcademico;
                $this->load->view('asistencias/frm_asistenciasAlumno', $data);
            }
        }
    }

    public function getMaterias() {
        $filial = $this->session->userdata('filial');
        $config = array();
        $config["filial"]["codigo"] = $filial['codigo'];
        $this->load->model('Model_matriculas_periodos', "", false, $config);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_matricula_periodo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $codigo = $this->input->post('cod_matricula_periodo');
            $materias = $this->Model_matriculas_periodos->getMaterias($codigo, null, 'migrado');
            echo json_encode($materias);
        }
    }

    public function getHorasEstadoAcademico() {
        $config = array();
        $filial = $this->session->userdata('filial');
        $config["filial"]["codigo"] = $filial['codigo'];
        $this->load->model('Model_estadoacademico', "", false, $config);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cod_estado_academico', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $codigo = $this->input->post('cod_estado_academico');
            $estado = $this->Model_estadoacademico->getEstadoAcademico($codigo)->estado;
            $data['msg'] = lang("no_esta_inscripto_a_ningun_horario");
            //siwakawa: Comento esto para q aparezcan los horarios por mas q el
            //alumno esta inhabilitado
            /*if ($estado != "cursando"){
                $data['horarios'] = array();
                $data['msg'] = lang("estado_academico_matriculas").": ".lang($estado);
            }
            else {*/
                $data['horarios'] = $this->Model_estadoacademico->getHorarios($codigo);
                $data['estados'] = $this->Model_asistencias->getArrayEstadoAsistencias();
            //}
            echo json_encode($data);
        }
    }

    public function getHorariosDiaComisionMateria() {
        $cod_comision = $this->input->post('cod_comision');
        $cod_materia = $this->input->post('cod_materia');
        $dia = $this->input->post('dia');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_horarios", "", false, $config);
        $this->load->model("Model_profesores", "", false, $config);
        $horarios = $this->Model_horarios->getHorariosDiaComisionMateria($cod_comision, $cod_materia, $dia);
        echo json_encode($horarios);
    }

    public function api_getHorasEstadoAcademico() {
        $config = array();
        $filial = $_POST['filial'];
        $codEstadoAcademico = $_POST['codEstadoAcademico'];
        $config["filial"]["codigo"] = $filial;
        $this->load->model('Model_estadoacademico', "", false, $config);
        $codigo = $codEstadoAcademico;
        $data['horarios'] = $this->Model_estadoacademico->getHorarios($codigo);
        $data['estados'] = $this->Model_asistencias->getArrayEstadoAsistencias();
        echo json_encode($data);
    }

    /**
     * Retorna json de los alumnos con sus asistencias por unidad
     * @access public
     * @param int $cod_comision : codigo de la comision [POST]
     * @param int $cod_materia : codigo de la materia equivalente al id del grupo en el campus [POST]
     * @return json de asistencias web.
     */
    public function get_asistencias_web_alumno( $cod_comision = null, $cod_materia = null ) {
        $conexion = $this->load->database('campus', true);
        $filial = $this->session->userdata('filial');
        $cod_filial = $filial["codigo"];
        $config = array("codigo_filial" => $cod_filial);
        $this->load->model("Model_comisiones", "", false, $config);

        $id_comision = $this->input->post('cod_comision');
        $id_materia = $this->input->post('cod_materia');
        $alumnosAsistencias = $this->Model_comisiones->getAlumnosMateriaComision($id_comision, $id_materia);

        //die(var_dump($alumnosAsistencias));
        /*Pasa id de alumnos a cod de usuario en plataforma*/
        if (!empty($alumnosAsistencias)) {

            foreach ($alumnosAsistencias as $key => $alumno) {
                $arrUsuarios[$key] = $alumno;
                $arrUsuarios[$key]['id_usuario'] = "u-" . $cod_filial . "-" . $alumno['cod_alumno'];
            }

            $data['alumnos'] = Vasistencias::getAsistenciaPorUnidad($conexion, $cod_filial, $id_materia, $arrUsuarios);

            if(!empty($data['alumnos'])) {

                if (isset($_POST['action']) && $_POST['action'] == "exportar"){

                    $exp = new export($_POST['tipo_reporte']);
                    $arrTemp = array();
                    $arrTitle = array(lang('ALUMNO'));
                    $arrWidth = array(40);

                    foreach ($data['alumnos'][0]->unidades as $unidad){

                        $arrTitle[] = substr(html_entity_decode($unidad->descripcion), 0, 14);
                        $arrWidth[] = 25;

                    }

                    foreach ($data['alumnos'] as $key => $alumno) {

                        $arrTemp[$key][] = $alumno->nombre;

                        foreach ($alumno->unidades as $unidad){

                            $arrTemp[$key][] = $unidad->asistencia;

                        }

                    }

                    $filial = $this->session->userdata("filial");
                    $arrInfo = array(
                        array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                        array("txt" => "Informe asistencias web", "size" => "8", "align" => "R", "width" => 286, "height" => 4)
                    );
                    $exp->setTitle($arrTitle);
                    $exp->setContent($arrTemp);
                    $exp->setPDFFontSize(7);
                    $exp->setColumnWidth($arrWidth);
                    $file = FCPATH."assents\img\logo.jpg";
                    $exp->setLogo($file);
                    $exp->setInfo($arrInfo);
                    $exp->setContentHeight(6);
                    $exp->setReportTitle($filial['nombre']." - ".lang("asistencia"). " Web");
                    $exp->setMargin(2, 8);
                    $exp->exportar();

                } else {
                    $claves = array("validacion_ok");
                    $data['langFrm'] = getLang($claves);
                    $this->load->view('asistencias/frm_asistencias_web', $data);
                }

            }

        }

        unset($_POST);
    }

    public function frm_profesor_horario() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_horarios", "", false, $config);
        $this->load->model("Model_profesores", "", false, $config);
        $arrayCodigoHorarios = $this->input->post('cod_horario');
        $arrayHorarios = $this->Model_horarios->getDetalleHorariosProfesores($arrayCodigoHorarios);
        $claves = array("validacion_ok");
        $data['langFrm'] = getLang($claves);
        $data['horarios'] = $arrayHorarios;
        $data['profesores'] = $this->Model_profesores->getProfesores();
        $this->load->view('asistencias/frm_profesor_horario', $data);
    }

    public function guardarProfesorHorario() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_horarios", "", false, $config);
        $cod_horario = $this->input->post('cod_horario');
        $cod_profesor = $this->input->post('cod_profesor');
        $accion = $this->input->post('accion');
        $data_post = array(
            "cod_horario" => $cod_horario,
            "cod_profesor" => $cod_profesor,
            "accion" => $accion
        );
        $arrResp = $this->Model_horarios->guardarProfesorHorario($data_post);
        echo json_encode($arrResp);
    }
}