<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Examenes extends CI_Controller {

    private $seccion;

    public function __construct() {
        $this->valor = 'prueba';
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configexamen = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_examenes", "", false, $configexamen);
    }

    public function index() {
        $data['page_title'] = 'Título de la Página';
        $data['page'] = 'examenes/vista_examenes'; // pasamos la vista a utilizar como parámetro
        $claves = array("nuevo-examen-final", "codigo", "HABILITADO", "INHABILITADO", "ocurrio_error", "validacion_ok", "INHABILITAR", "HABILITAR");
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('examenes');
        $data['columnsParciales'] = $this->getColumns('parciales');
        $data['columnsFinales'] = $this->getColumns('finales');
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }

    private function crearColumnas($tipo = null) {
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => lang('materia'), "campo" => 'nomMateria'),
            array("nombre" => lang('tipo_examen'), "campo" => 'tipoexamen'),
            array("nombre" => lang('comision'), "campo" => 'nomComision', 'bVisible' => false),
            array("nombre" => lang('fecha_examen'), "campo" => 'fecha'),
            array("nombre" => lang('hora_inicio'), "campo" => 'hora'),
            array("nombre" => lang('hora_fin'), "campo" => 'horafin'),
            array("nombre" => lang('cupo'), "campo" => 'cupo'),
            array("nombre" => lang('cantidad_inscriptos'), "campo" => 'cantinscriptos'),
            array("nombre" => lang('examenEstado'), "campo" => 'estado', 'sort' => false),
            array("nombre" => lang('estado_examen'), "campo" => 'baja', 'bVisible' => false)
        );
        if ($tipo == 'parciales') {
            $columnas[3]['bVisible'] = TRUE;
        }
        return $columnas;
    }

    public function getColumns($estado) {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas($estado)));
        return $aoColumnDefs;
    }

    /**
     * retorna lista de examenes para mostrar en index de main panel.
     * @access public
     * @return json de listado de examenes.
     */
    public function listar() {
        $listar = $this->input->post('tipoExamen');
        $crearColumnas = $this->crearColumnas($listar);
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        if ($listar == 'finales') {
            $examenesFinales = $this->Model_examenes->listarExamenesFinalesDataTable($arrFiltros);
            echo json_encode($examenesFinales);
        } else {
            $examenesParciales = $this->Model_examenes->listarExamenesParcialesDataTable($arrFiltros);
            echo json_encode($examenesParciales);
        }
    }

    /**
     * carga la vista del formulario examen parcial y recuperatorio.
     * @access public
     * @return vista form examen parcial.
     */
    public function frm_examen_parcial() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_cursos", "", false, $config);
        $this->load->model("Model_profesores", "", false, $config);
        $this->load->model("Model_salones", "", false, $config);
        $examenes = $this->Model_examenes->getExamenParcialRecParcal();
        $cursosHabilitados = $this->Model_cursos->getCursosHabilitados(null, null, 0);
        $salones = $this->Model_salones->getSalones();
        $tipoSalones = $this->Model_salones->getTiposSalones();
        $profesores = $this->Model_profesores->getProfesores();
        $data['examenes'] = $examenes;
        $data['cursosHabilitados'] = $cursosHabilitados;
        $claves = array('validacion_ok');
        $data['langFrm'] = getLang($claves);
        $data['salones'] = $salones;
        $data['tipoSalones'] = $tipoSalones;
        $data['profesores'] = $profesores;
        $this->load->view('examenes/frm_examen_parcial', $data);
    }

    public function getProfesoresMateriaHorarios() {
        $cod_materia = $this->input->post('cod_materia');
        $profesoresMateriaHorarios = $this->Model_examenes->getProfesoresMateriaHorarios($cod_materia);
        echo json_encode($profesoresMateriaHorarios);
    }

    /**
     * Retorna json de materias de una comision.
     * @access public
     * @return Json e materias.
     */
    public function getMateriasComision() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $cod_comision = $this->input->post('codigo');
        $this->load->model('Model_comisiones', "", false, $config);
        $materiasComision = $this->Model_comisiones->getMateriasComision($cod_comision);
        echo json_encode($materiasComision);
    }

    /**
     * Retorna json de inscriptos a una materia.
     * @access public
     * @return Json de Inscriptos.
     */
    public function getInscriptosComisionMaterias() {
        $cod_comision = $this->input->post('codigo');
        $cod_materia = $this->input->post('cod_materia');
        $tipo_examen = $this->input->post('tipoExamen');
        $cod_examen_padre = null;

        if (isset($_POST["cod_examen_padre"])) {
            $cod_examen_padre = $this->input->post('cod_examen_padre');
        }
        /*die(
            "cod_comision: " . $cod_comision . "\n".
            "cod_materia: " . $cod_materia . "\n".
            "tipo_examen: " . $tipo_examen . "\n\n"
        );*/
        $inscriptosMatComision = $this->Model_examenes->getDetallesInscriptos($cod_comision, $cod_materia, $tipo_examen, $cod_examen_padre);
        echo json_encode($inscriptosMatComision);
    }

    /**
     * Retorna json de comisiones de un curso.
     * @access public
     * @return Json de Comisiones.
     */
    public function getComisionesCurso() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $cod_plan = $this->input->post('codigo');
        $this->load->model("Model_planes_academicos", "", false, $config);
        $comisiones = $this->Model_planes_academicos->getComisiones($cod_plan);
        echo json_encode($comisiones);
    }
	
	/**
     * Retorna json con parciales pasados de una materia para una comisión.
     * @access public
     * @return Json de Parciales.
     */
    public function getParcialesPasadosDeMateriaParaComision() {
        $cod_materia = $this->input->post('cod_materia');
		$cod_comision = $this->input->post('cod_comision');
		
		$parciales = $this->Model_examenes->getParcialesPasadosDeMateriaParaComision($cod_materia, $cod_comision);
		
		echo json_encode($parciales);
    }

    /**
     * Carga la vista del form examen final y recuperatorio
     * @access public
     * @return Json e materias.
     */
    public function frm_examen_final() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_materias", "", false, $config);
        $this->load->model("Model_salones", "", false, $config);
        $this->load->model("Model_profesores", "", false, $config);
        $examenes = $this->Model_examenes->getExamenFinalRecFinal();
        $materias = $this->Model_materias->getMaterias();
        $salones = $this->Model_salones->getSalones();
        $profesores = $this->Model_profesores->getProfesores();
        $tipoSalones = $this->Model_salones->getTiposSalones();
        $claves = array('validacion_ok', 'solo_se_pueden_inscribir_hasta_un_maximo_de__alumnos_por_examen');
        $data['langFrm'] = getLang($claves);
        $data['examenes'] = $examenes;
        $data['materias'] = $materias;
        $data['salones'] = $salones;
        $data['profesores'] = $profesores;
        $data['tipoSalones'] = $tipoSalones;
        $this->load->view('examenes/frm_examen_final', $data);
    }

    public function getReporteExamenes() {
        session_method();
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $arrResp = $this->Model_examenes->getReporteExamenes('20', $arrLimit, $arrSort, $search, $searchField, $_POST['fecha_desde'], $_POST['fecha_hasta']);
        echo json_encode($arrResp);
    }

    /**
     * Carga la vista del form inscriptos para inscribir alumnos a examenes.
     * @access public
     * @return vista form inscriptos .
     */
    public function frm_inscriptos() {
        $this->load->library('form_validation');
        $cod_examen = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $filial = $this->session->userdata('filial');
            $config = array("codigo_filial" => $filial['codigo']);
            $this->load->model("Model_planes_academicos", "", false, $config);
            $this->load->model("Model_comisiones", "", false, $config);
            $curso = $this->Model_examenes->getCursoInscriptos($cod_examen);
            $datosExamen = $this->Model_examenes->getDatosInscribirExamen($cod_examen);
            $examen = $this->Model_examenes->getExamen($cod_examen);
            $cod_materia = $examen->materia;
            $comisiones = $this->Model_comisiones->getComisionesMateria($cod_materia);            
           
            $claves = array("validacion_ok", "ocurrio_error", "error_seleccionar_inscripcion", "ERROR",
                "inscripto_en_otro_examen_para_la_misma_materia", "solo_puede_inscribir_en_estado_regular_o_libre");
            $data['langFrm'] = getLang($claves);
            $data['cod_examen'] = $cod_examen;
            $data['examen'] = $this->Model_examenes->getExamen($cod_examen);
            $data['curso'] = $curso;
            $data['datosExamen'] = $datosExamen;
            $data['comisiones'] = $comisiones;
            $this->load->view('examenes/frm_inscriptos', $data);
        }
    }

    private function crearColumnasInscriptos() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('codigo_alumno'), "campo" => 'codigo'),
            array("nombre" => lang('matricula'), "campo" => 'matriculas.codigo'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('fecha_inscripcion'), "campo" => 'fechadeinscripcion'),
            array("nombre" => "", "campo" => 'check', 'sort' => false, 'sWidth' => '80px'),
            array("nombre" => lang('estado'), "campo" => 'estado')
        );
        return $columnas;
    }

    public function getColumnsInscriptos() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnasInscriptos()));
        echo $aoColumnDefs;
    }

    public function getInscriptosExamen() {
        $crearColumnas = $this->crearColumnasInscriptos();
        $cod_examen = $this->input->post('codigo');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $inscriptos = $this->Model_examenes->listarInscriptosExamenDataTable($cod_examen, $arrFiltros);
        echo json_encode($inscriptos);
    }

    private function crearColumnasAlumnosInscribir() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => "", "campo" => 'cod_estado_academico', "bVisible" => false),
            array("nombre" => "<input type='checkbox' name='chkAll' class='ace' onclick='checkearTodosLosAlumnos(this);'><span class='lbl'></span>", "campo" => 'check', "sort" => false),
            array("nombre" => lang('matricula'), "campo" => 'cod_matricula'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('estado'), "campo" => 'estado'),
            //array("nombre" => lang('rindio'), "campo" => 'rindio', 'sort' => false),
            array("nombre" => lang('comision'), "campo" => 'nomComision'),
            array("nombre" => '', "campo" => 'nomComision', "bVisible" => false),
        );
        return $columnas;
    }

    public function getColumnsAlumnosInscribir() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnasAlumnosInscribir()));
        echo $aoColumnDefs;
    }

    public function getInscribirAlumnosExamen() {
        $crearColumnas = $this->crearColumnasAlumnosInscribir();
        $cod_examen = $this->input->post('codigo');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $comision = isset($_POST['comision']) ? $_POST['comision'] : "";
        $alumnosInscribir = $this->Model_examenes->listarAlumnosInscribirDataTable($cod_examen, $arrFiltros, $comision);
        echo json_encode($alumnosInscribir);
    }

    /**
     * Retorna carga la vista form baja para dar de baja un alumno
     * @access public
     * @return vista form baja.
     */
    public function frm_baja_examen() {
        $this->load->library('form_validation');
        $cod_examen = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarBajaExamen');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $motivos = $this->Model_examenes->getMotivosBaja();
            $claves = array("validacion_ok", "ok", "upps", "debe_seleccionar_un_motivo");
            $data['langFrm'] = getLang($claves);
            $data['cod_examen'] = $cod_examen;
            $data['motivos'] = $motivos;
            $data['nombre_materia'] = $this->Model_examenes->getMateriaExamen($cod_examen);
            $this->load->view('examenes/frm_baja_examen', $data);
        }
    }

    /**
     * Cambia el estado de un examen.
     * @access public
     * @return Json de respuesta.
     */
    public function cambiarEstado() {
        session_method();
        $this->load->library('form_validation');
        $usuario = $this->session->userdata('codigo_usuario');
        $cod_examen = $this->input->post('codigo');
        $motivo = $this->input->post('motivo') ? $this->input->post('motivo') : null;
        $comentario = $this->input->post('comentario') ? $this->input->post('comentario') : null;
        $cambioEstadoExamen = array('cod_examen' => $cod_examen,
            'motivo' => $motivo,
            'comentario' => $comentario,
            'cod_usuario' => $usuario);
        $resultado = $this->Model_examenes->cambioEstadoExamen($cambioEstadoExamen);
        echo json_encode($resultado);
    }

    /**
     * Guarda todos los del examen
     * @access public
     * @return json de respuesta
     */
    public function guardarExamen() {
        $resultado = '';
        $data_post = array();
        $this->load->library('form_validation');
        $this->load->helper('formatearfecha');
        $cod_examen = $this->input->post('codigo');
        $horainicio = $this->input->post('horaInicio');
        $tipoExamen = $this->input->post('tipoExamen');
        $ver_campus = $this->input->post('ver_campus') ? 1 : 0;
        
        if(!$this->input->post('fecha') || (!$tipoExamen) ){
            $resultado = array(
                'codigo' => '0',
                'msgerror' => lang("campos_vacios"),
                'errNo' => ''
            );
            echo json_encode ($resultado); 
            return; 
        }
        
        $array = array(
            'horaFin' => $this->input->post('horaFin'),
            'salones' => $this->input->post('salonCocina'),
            'fecha' => formatearFecha_mysql(trim($this->input->post('fecha')))
        );
        $validar = json_encode($array);
        $this->form_validation->set_rules('materia', lang('materia_examen_final'), 'required');
        
        $this->form_validation->set_rules('horaFin', lang('horaFin_examen_final'), 'required|validarHora[' . $horainicio . ']');
        $this->form_validation->set_rules('fecha', lang('fecha_examen_final'), 'required');
        $this->form_validation->set_rules('salonCocina[]', lang('salon'), 'required');
      
        switch ($tipoExamen) {
            case 'PARCIAL':
            case 'RECUPERATORIO_PARCIAL':
                $this->form_validation->set_rules('tipoExamen', lang('tipoExamen_final'), 'required');
                $this->form_validation->set_rules('Curso', lang('curso_examen_parcial'), 'required');
                $this->form_validation->set_rules('Comision', lang('comisiones_examen_parcial'), 'required');

                if ($tipoExamen === 'RECUPERATORIO_PARCIAL') {
                    //$this->form_validation->set_rules('examen_padre', lang('examen_padre_input_error'), 'required');
                    $this->form_validation->set_rules('examen_padre', 'Debe seleccionar un examen a recuperar.', 'required');
                }

                if($this->input->post('codigo') != -1){
                     $this->form_validation->set_rules('cupo',lang('cupo'),'validarCupoModificarExamen['.$cod_examen.']');
                }

                $data_post['examen']['codigo_examen_padre'] = $this->input->post('examen_padre');
                $data_post['examen']['cod_examen'] = $this->input->post('codigo');
                $data_post['examen']['tipoExamen'] = $this->input->post('tipoExamen');
                $data_post['examen']['Curso'] = $this->input->post('Curso');
                $data_post['examen']['Comision'] = $this->input->post('Comision');
                $data_post['examen']['materia'] = $this->input->post('materia');
                $data_post['examen']['horaInicio'] = $horainicio;
                $data_post['examen']['fecha'] = formatearFecha_mysql(trim($this->input->post('fecha'))) == '' ? '' : formatearFecha_mysql(trim($this->input->post('fecha')));
                $data_post['examen']['cupo'] = $this->input->post('cupo');
                $data_post['examen']['profesor'] = $this->input->post('profesores');
                $data_post['examen']['horaFin'] = $this->input->post('horaFin');
                $data_post['examen']['salon'] = $this->input->post('salonCocina');
                $data_post['examen']['observaciones'] = $this->input->post('observaciones') == '' ? '' : $this->input->post('observaciones');
                $data_post['examen']['alumnos'] = $this->input->post('alumnos');
                $data_post['examen']['ver_campus'] = $ver_campus;
                $data_post['examen']['codigo_examen_padre'] = $this->input->post('examen_padre');
                break;

            case 'FINAL':
            case 'RECUPERATORIO_FINAL':
                $this->form_validation->set_rules('tipoExamen', lang('tipoExamen_final'), 'required');
                $this->form_validation->set_rules('profesores[]', lang('profesor_examen_final'), 'required');
                $this->form_validation->set_rules('cupo', lang('cupo_examen_final'), 'required');
                if($this->input->post('codigo') != -1){
                     $this->form_validation->set_rules('cupo',lang('cupo'),'validarCupoModificarExamen['.$cod_examen.']');
                }
                $data_post['examen']['cod_examen'] = $this->input->post('codigo');
                $data_post['examen']['tipoExamen'] = $this->input->post('tipoExamen');
                $data_post['examen']['horaInicio'] = $horainicio;
                $data_post['examen']['horaFin'] = $this->input->post('horaFin');
                $data_post['examen']['fecha'] = formatearFecha_mysql(trim($this->input->post('fecha'))) == '' ? '' : formatearFecha_mysql(trim($this->input->post('fecha')));
                $data_post['examen']['materia'] = $this->input->post('materia');
                $data_post['examen']['salon'] = $this->input->post('salonCocina');
                $data_post['examen']['cupo'] = $this->input->post('cupo');
                $data_post['examen']['profesor'] = $this->input->post('profesores');
                $data_post['examen']['preinscripcionWeb'] = $this->input->post('preinscripcionWeb');
                $data_post['examen']['observaciones'] = $this->input->post('observaciones') == '' ? '' : $this->input->post('observaciones');
                $data_post['examen']['ver_campus'] = $ver_campus;
                break;
        }

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            if ($cod_examen == -1) {
                $this->form_validation->set_rules('horaInicio', lang('horaInicio_examen_final'), 'required|validarHoraSalonExamen[' . $validar . ']');
            } else {
                $this->form_validation->set_rules('horaInicio', lang('horaInicio_examen_final'), 'required');
            }
            if ($this->form_validation->run() == false) {
                $errors = validation_errors();
                $resultado = array(
                    'codigo' => '0',
                    'msgerror' => $errors,
                    'errNo' => ''
                );
            }else {
                $resultado = $this->Model_examenes->guardarExamen($data_post);
            }
        }

        echo json_encode($resultado);
    }

    /**
     * Carga la vista del form cargar notas de examenes
     * @access public
     * @return vista form cargar notas examenes .
     */
    public function frm_cargarNotasExamen() {
        $this->load->library('form_validation');
        $cod_examen = $this->input->post('codigo');
        $filial = $this->session->userdata('filial');
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $this->load->model("Model_materias", "", false, $config);
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $objExamen = $this->Model_examenes->getExamen($cod_examen);
            $inscriptosExamen = $this->Model_examenes->getInscriptosExamenes($cod_examen, $separadorDecimal);
            $arrConfiguracionNotas = $this->Model_configuraciones->getValorConfiguracion(null, 'configuracionNotaExamen');
            if ($objExamen->fecha <= date('Y-m-d')) {
                $claves = array("validacion_ok", "alumno_ausente", "notas_guardadas");
                $data['langFrm'] = getLang($claves);
                $data['cod_examen'] = $cod_examen;
                $data['configuracion_notas'] = $arrConfiguracionNotas;
                $data['escala_notas'] = $this->Model_configuraciones->getArrayEscalaNotasExamen('configuracionNotaExamen', $arrConfiguracionNotas['formato_nota']);
                $data['objExamen'] = $objExamen;
                $data['materia'] = $this->Model_materias->getMateriaExamen($objExamen->materia);
                $data['inscriptosExamen'] = $inscriptosExamen;
                $this->load->view('examenes/frm_cargar_nota_examen', $data);
            } else {
                echo lang('examen_que_no_fue_tomado');
            }
        }
    }

    /**
     * Carga la vista del form cargar nota a los examenes de un alumno
     * @access public
     * @return vista form cargar nota alumno .
     */
    public function frm_cargarNotaAlumno() {
        $this->load->library('form_validation');
        $cod_alumno = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $filial = $this->session->userdata('filial');
            $config = array("codigo_filial" => $filial['codigo']);
            $this->load->model('Model_alumnos', "", false, $config);
            $this->load->model("Model_configuraciones", "", false, $config);
            $examenAlumno = $this->Model_alumnos->getExamenAlumno($cod_alumno);
            if (count($examenAlumno) == 0) {
                echo lang('alumno_sin_examen');
            } else {
                $arrConfiguracionNotas = $this->Model_configuraciones->getValorConfiguracion(null, 'configuracionNotaExamen');
                $data['examenAlumno'] = $examenAlumno;
                $data['nombreAluFormateado'] = $this->Model_alumnos->getNombreAlumno($cod_alumno);
                $data['configuracion_notas'] = $arrConfiguracionNotas;
                $data['escala_notas'] = $this->Model_configuraciones->getArrayEscalaNotasExamen('configuracionNotaExamen', $arrConfiguracionNotas['formato_nota']);
                $this->load->view('examenes/frm_cargar_nota_alumno', $data);
            }
        }
    }

    /**
     * Guarda todas las inscripciones a un examen.
     * @access public
     * @return json de respuesta
     */
    public function guardarInscripcionesExamen() {
        session_method();
        $this->load->library('form_validation');
        $resultado = '';
        $inscriptos = json_encode($this->input->post('inscriptos'));
        $cod_examen = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('cod_examen'), 'validarInscriptosExamen[' . $inscriptos . ']');
        $this->form_validation->set_rules('inscriptos', lang('inscribir-examen'), 'required|validarAlumnoInscriptoMismoExamen[' . $cod_examen . ']|validarAlumnoInscriptoExamen[' . $cod_examen . ']|validarEstadoAcademicoInscribirExamen['.$cod_examen.']');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['cod_examen'] = $cod_examen;
            $data_post['inscriptos'] = json_decode($this->input->post('inscriptos'));
            $resultado = $this->Model_examenes->guardarInscriptos($data_post);
        }
        echo json_encode($resultado);
    }

    /**
     * Guarda todas las notas de los examenes.
     * @access public
     * @return json de respuesta
     */
    public function guardarNotaExamen() {
        session_method();
        $this->load->library('form_validation');
        $resultado = '';
        $filial = $this->session->userdata('filial');
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $arrConfigNotasExamen = $this->Model_configuraciones->getValorConfiguracion(null, 'configuracionNotaExamen');
        $this->form_validation->set_rules('escrito', lang('nota-escrito'), 'numeric');
        $this->form_validation->set_rules('oral', lang('oral-escrito'), 'numeric');
        $this->form_validation->set_rules('definitivo', lang('definitivo-escrito'), 'numeric');
        $notas = $this->input->post('alumnos');
        $arrayNotas = '';
        foreach ($notas as $value) {
            $arrayNotas[] = $value['notas'];
        }

        foreach ($arrayNotas as $key => $valor) {
            $_POST['escrito' . $key] = $valor['escrito'];
            $_POST['oral/teorico' . $key] = $valor['oral/teorico'];
            $_POST['definitivo' . $key] = $valor['definitivo'];
            if ($arrConfigNotasExamen['formato_nota'] == 'numerico') {
                $this->form_validation->set_rules('escrito' . $key, lang('escrito'), 'validarExpresionTotal|validarNotaExamen[' . lang('escrito') . ']');
                $this->form_validation->set_rules('oral/teorico' . $key, lang('oral'), 'validarExpresionTotal|validarNotaExamen[' . lang('oral') . ']');
                $this->form_validation->set_rules('definitivo' . $key, lang('definitivo'), 'validarExpresionTotal|validarNotaExamen[' . lang('definitivo') . ']');
            }
        }
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $cod_examen = $this->input->post('codigo');
            $data_post['cod_examen'] = $cod_examen;
            $data_post['guardarnota'] = $this->input->post('alumnos');
            if ($arrConfigNotasExamen['formato_nota'] == 'alfabetico') {
                $arrConfigNotasExamen['array_notas'] = $this->Model_configuraciones->getArrayEscalaNotasExamen('configuracionNotaExamen', $arrConfigNotasExamen['formato_nota']);
            }
            $guardar = $this->validarNotasAusente($data_post['guardarnota']);
            if ($guardar) {
                $resultado = $this->Model_examenes->guardarNotaExamen($data_post, $arrConfigNotasExamen, $separadorDecimal);
                /* Inicio Ticket 747 - Actualizar estado de certificado */
                $this->Model_examenes->actualizarCertificado($data_post, $arrConfigNotasExamen);
                /* Fin Ticket 747 - Actualizar estado de certificado */
            } else {
                $resultado = array(
                    "codigo" => 0,
                    "msgerror" => lang('ausente_con_notas')
                );
            }
        }
        echo json_encode($resultado);
    }

    /**
     * Guarda la baja de una inscripcion al examen.
     * @access public
     * @return json de respuesta.
     */
    public function bajaMatriculaExamen() {
        $this->load->library('form_validation');
        $cod_inscripcion = $this->input->post('codigo');
        $respuesta = '';
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric|validarBajaInscripcionExamen');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $respuesta = array(
                "codigo" => 0,
                "msgerror" => $errors
            );
        } else {
            $respuesta = $this->Model_examenes->bajaMatriculaExamen($cod_inscripcion);
        }
        echo json_encode($respuesta);
    }

    /**
     * Guarda todas las notas de los examenes de un alumno.
     * @access public
     * @return json de respuesta.
     */
    public function guardarNotaAlumno() {
        session_method();
        $this->load->library('form_validation');
        $resultado = '';
        $filial = $this->session->userdata('filial');
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $config = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_configuraciones", "", false, $config);
        $arrConfigNotasExamen = $this->Model_configuraciones->getValorConfiguracion(null, 'configuracionNotaExamen');
        $notas = $this->input->post('examenes');
        $arrNotas = array();

        if($notas != ""){
            foreach ($notas as $value) {
                $arrNotas[] = $value['notas'];
            }
        }

        foreach ($arrNotas as $key => $nota) {
            $_POST['escrito' . $key] = $nota['escrito'];
            $_POST['oral/teorico' . $key] = $nota['oral/teorico'];
            $_POST['definitivo' . $key] = $nota['definitivo'];
            if ($arrConfigNotasExamen['formato_nota'] == 'alfabetico') {
                $this->form_validation->set_rules('escrito' . $key, lang('escrito'), 'required');
                $this->form_validation->set_rules('oral/teorico' . $key, lang('oral'), 'required');
                $this->form_validation->set_rules('definitivo' . $key, lang('definitivo'), 'required');
            } else {
                $this->form_validation->set_rules('escrito' . $key, lang('escrito'), 'validarExpresionTotal|validarNotaExamen[' . lang('escrito') . ']');
                $this->form_validation->set_rules('oral/teorico' . $key, lang('oral'), 'validarExpresionTotal|validarNotaExamen[' . lang('oral') . ']');
                $this->form_validation->set_rules('definitivo' . $key, lang('definitivo'), 'validarExpresionTotal|validarNotaExamen[' . lang('definitivo') . ']');
            }
        }

        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['cod_alumno'] = $this->input->post('codigo');
            $data_post['guardarnota'] = $notas;
            if ($arrConfigNotasExamen['formato_nota'] == 'alfabetico') {
                $arrConfigNotasExamen['array_notas'] = $this->Model_configuraciones->getArrayEscalaNotasExamen('configuracionNotaExamen', $arrConfigNotasExamen['formato_nota']);
            }
            $resultado = $this->Model_examenes->guardarNotaAlumno($data_post, $arrConfigNotasExamen, $separadorDecimal);
        }
        echo json_encode($resultado);
    }

    public function modificarExamen() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $idfilial = $filial['codigo'];
        $this->load->model("Model_materias", "", false, $config);
        $this->load->model("Model_profesores", "", false, $config);
        $this->load->model("Model_salones", "", false, $config);
        $this->load->model("Model_cursos", "", false, $config);
        $cod_examen = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $objExamen = $this->Model_examenes->getExamen($cod_examen);
            $tipoExamen = $objExamen->tipoexamen;
            $data['tieneNotasCargadas'] = $objExamen->getCantidadNotasCargadas() > 0;
            if ($tipoExamen == 'PARCIAL' || $tipoExamen == 'RECUPERATORIO_PARCIAL') {
                $claves = array('validacion_ok');
                $data['langFrm'] = getLang($claves);
                $data['examenes'] = $this->Model_examenes->getExamenParcialRecParcal();
                $data['examen'] = $objExamen;
                $data['salones'] = $this->Model_salones->getSalones();
                $data['salonesExamen'] = $this->Model_examenes->getSalonesExamen($cod_examen);
                $data['comisionCurso'] = $this->Model_examenes->getComisionCursoExamenParcial($cod_examen);
                $cod_curso = $data['comisionCurso'][0]['cod_curso'];
                $data['materias'] = $this->Model_materias->getMaterias();
                $cod_materia = $objExamen->materia;
                $cod_comision = $data['comisionCurso'][0]['cod_comision'];
                $data['alumnos'] = $this->Model_examenes->getDetallesInscriptos($cod_comision, $cod_materia);
                //var_dump($data['alumnos']); die();
                $data['comisiones'] = $this->Model_cursos->getComisiones($idfilial, $cod_curso);
                $data['salonesExamen'] = $this->Model_examenes->getSalonesExamen($cod_examen);
                $data['cursosHabilitados'] = $this->Model_cursos->getCursosHabilitados(null, null, 0);
                $data['profesores'] = $this->Model_profesores->getProfesores();
                $data['profesoresExamen'] = $this->Model_examenes->getProfesoresExamen($cod_examen);

                if (is_array($data['alumnos']) && count($data['alumnos']) < 0) {

                }

                if ($tipoExamen == 'RECUPERATORIO_PARCIAL') {
                    $data['parciales_pasados'] = $this->Model_examenes->getParcialesPasadosDeMateriaParaComision($cod_materia, $cod_comision);
                }
                //var_dump($data); die();
                $this->load->view('examenes/frm_examen_parcial', $data);
            } else {
                $claves = array('validacion_ok', 'solo_se_pueden_inscribir_hasta_un_maximo_de__alumnos_por_examen');
                $data['langFrm'] = getLang($claves);
                $data['examenes'] = $this->Model_examenes->getExamenFinalRecFinal();
                $data['examen'] = $objExamen;
                $data['materias'] = $this->Model_materias->getMaterias();
                $data['salones'] = $this->Model_salones->getSalones();
                $data['salonesExamen'] = $this->Model_examenes->getSalonesExamen($cod_examen);
                $data['profesores'] = $this->Model_profesores->getProfesores();
                $data['profesoresExamen'] = $this->Model_examenes->getProfesoresExamen($cod_examen);
                $this->load->view('examenes/frm_examen_final', $data);
            }
        }
    }

    private function validarNotasAusente($arrNotas) {
        foreach ($arrNotas[0]['notas'] as $nota) {
            if (isset($arrNotas[0]['ausente'])) {
                if ($nota == '') {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }

    public function scriptUpdatePorcentaje_aprobados() {
        $filial = $this->session->userdata('filial');
        $separadorDecimal = $filial['moneda']['separadorDecimal'];
        $resultado = $this->Model_examenes->scriptUpdatePorcentaje_aprobados($separadorDecimal);
        echo json_encode($resultado);
    }
}
