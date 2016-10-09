<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Horarios extends CI_Controller {

    var $filial = 0;
    private $seccion;
    
    public function __construct(){
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $this->filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_horarios", "", false, $config);
        $this->load->model("Model_salones", "", false, $config);        
    }

    public function index(){
       $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_salones", "", false, $config);
        $this->load->model("Model_feriados", "", false, $config);        
        $claves = Array('nuevo-salon', "ASISTENCIA_GUARDADA_CORRECTAMENTE", "SALON_GUARDADO_CORRECTAMENTE", 
            "EXCEPCION_GUARDADA_CORRECTAMENTE","BIEN","HORARIO_GUARDADO_CORRECTAMENTE","ERROR","TIENE_ASISTENCIAS_CARGADAS",
            "no_puede_modificarse","superpone_con_evento","que_comienza","y_finaliza","tiene_asistencias_cargadas","curso",
            "materia","horario",'ver_alumnos',"eliminar","modificar","tiene_asistencias","comision", "superpone_con_horario_comision","actualizando_grilla_horarios","alumnos");
        $data['lang'] = getLang($claves);
        $data['page'] = 'horarios/vista_horarios';
        $data['diasDeshabiltados'] = $this->Model_horarios->configuracionDiasFilial();
        $data['horarios_filial'] = $this->Model_horarios->getHorariosFilial();
        $data["salones"] = $this->Model_salones->getSalonesHorarios();
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }

    public function getHorarios(){
        $fechaInicio = $this->input->post("start") ? $this->input->post("start") : null;
        $fechaFin = $this->input->post("end") ? $this->input->post("end") : null;
        $salones = $this->input->post("salones") ? $this->input->post("salones") : null;
        $comisiones = $this->input->post("comisiones") ? $this->input->post("comisiones") : null;
        $materias = $this->input->post("materias") ? $this->input->post("materias") : null;
        $profesores = $this->input->post("profesores") ? $this->input->post("profesores") : null;
        $retorno = array();
        if($salones != ''){
            $retorno = $this->Model_horarios->getHorarios($fechaInicio, $fechaFin, $salones, $comisiones, $materias, $profesores);
        }       
        echo json_encode($retorno);
    }

    /**
     * carga la vista del formulario horario
     * @access public
     * @return vista form horario.
     */
    public function frm_horario(){
        $this->load->library('form_validation');
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $arrInscriptos = array();
        $this->load->model("Model_matriculas_horarios", "", false, $config);
        $this->load->model("Model_comisiones", "", false, $config);
        $this->load->model("Model_salones", "", false, $config);
        $this->load->model("Model_cursos", "", false, $config);
        $this->load->model("Model_profesores", "", false, $config);
        $fechaNuevoEvent = $this->input->post('fechaInicio');
        $horaComienzo = $this->input->post('horaComienzo');
        $horaFinal = $this->input->post('horaFinal');
        $codigo_horario = $this->input->post('codigo_horario');
        $this->form_validation->set_rules('codigo_horario',lang('codigo'),'numeric');
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $puedeModificar = true;            
            $arrInscriptos = $this->Model_matriculas_horarios->getInscriptosHorario($codigo_horario);            
            foreach($arrInscriptos as $inscriptos){
                if($inscriptos['estado'] != ''){
                    $puedeModificar = false;
                }
            }
            if($puedeModificar == false){
                echo lang('evento_con_asistencias_cargadas');
            } else {
                $ObjHorario = $this->Model_horarios->getObjHorario($codigo_horario);
                $data['horaComienzo'] = $horaComienzo;
                $data['horaFinal'] = $horaFinal;
                $data["horario"] = $ObjHorario;
                $data["fechaNuevoEvent"] = $fechaNuevoEvent;
                $data["materias"] = array();
                $data["profesores"] = array();
                $data["profesores_horarios"] = array();
                $data["dias"] = array();
                $data["fin_repeticion"] = "";
                if ($codigo_horario != -1) {
                    $data["materias"] = $this->Model_comisiones->getMateriasComision($ObjHorario->cod_comision);
                    $data["profesores"] = $this->Model_horarios->getProfesoresHorario($codigo_horario);
                    $data["dias"] = $this->Model_horarios->getSerieDias($codigo_horario);
                    $data["fin_repeticion"] = $this->Model_horarios->getFechaFinSerie($codigo_horario);
                }
                $data["cursos"] = $this->Model_cursos->getCursosHabilitados(null, null, 0);
                $data["comisiones"] = $this->Model_comisiones->getComisionesActivas($ObjHorario->cod_comision);
                $data["salones"] = $this->Model_salones->getSalones();
                $data["repeticion"] = $this->Model_horarios->getRepeticion();
                $data["tipofinalizacion"] = $this->Model_horarios->getTipoFinalizacion();
                $this->load->view('horarios/frm_horario', $data);
            }
        }
    }

    /**
     * getComisiones para horario general
     * @access public
     * @return json de comisiones
     */
    public function getComisionesCurso(){        
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_cursos", "", false, $config);
        $cod_curso = $this->input->post('cod_curso');
        echo json_encode($this->Model_cursos->getComisiones($this->filial["codigo"], $cod_curso, Vcomisiones::getEstadoHabilitada()));
    }

    /**
     * Retorna las materias de una comision para el frm
     * @access public
     * @return json de materias
     */
    public function getMateriasComision(){        
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_comisiones", "", false, $config);
        $cod_comision = $this->input->post('cod_comision');
        echo json_encode($this->Model_comisiones->getMateriasComision($cod_comision));
    }

    /**
     * getComisiones para horario general
     * @access public
     * @return json de comisiones
     */
    public function getComisiones(){     
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_comisiones", "", false, $config);
        echo json_encode($this->Model_comisiones->getComisionesconHorarios());
    }

    /**
     * retorna materias  para horario general
     * @access public
     * @return json de materias
     */
    public function getMaterias(){
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_materias", "", false, $config);
        echo json_encode($this->Model_materias->getMateriasconHorarios());
    }

    /**
     * retorna profesores  para horario general
     * @access public
     * @return json de profesores
     */
    public function getProfesoresconHorario(){
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_profesores", "", false, $config);
        echo json_encode($this->Model_profesores->getProfesoresconHorarios());
    }

    /**
     * guarda o modifica un horario  del calendario.
     * @access public
     * @return boolean
     */
    public function guardarHorario(){        
        $horaDesde = $this->input->post('horaDesde');
        $this->load->helper('formatearfecha');
        $this->load->library('form_validation');
        $json = json_encode(array("fechaDesde"=>  $this->input->post('fechaDesde'),
                "hora_desde"=> $this->input->post('horaDesde'),
                "hora_hasta"=> $this->input->post('horaHasta'),
                "codigo_horario"=>$this->input->post('codigo_horario'),
                "cod_materia"=> $this->input->post('cod_materia')
                ));
        $jsonFechas = json_encode(array('fecha_desde'=>  $this->input->post('fechaDesde')));
        $jsonHoras = json_encode(array(
            "horaDesde"=>  $this->input->post('horaDesde'),
            "horaHasta"=>  $this->input->post('horaHasta')
        ));
        $this->form_validation->set_rules('fechaDesde', lang('fechaDesde_horario'), 'required|max_length[50]|max_length[255]|validarFeriadoRecesoDia|validarHorarioFilial['.$jsonHoras.']');
        $this->form_validation->set_rules('horaDesde', lang('horadesde_horario'), 'required');
        $this->form_validation->set_rules('horaHasta', lang('horaHasta_horario'), 'required|validarHora[' . $horaDesde . ']');
        $this->form_validation->set_rules('cod_salon', lang('salon'), 'required|validarSalonHorario['.$json.']');
        $this->form_validation->set_rules('cod_comision', lang('comision'), 'required|validarHorarioDiaComision['.$json.']|validarHorarioCicloComision['.$jsonFechas.']');
        $this->form_validation->set_rules('cod_materia', lang('materia'), 'required');
        if ($this->input->post('tipoRepeticion') == 1) {
            $arrayJson = array(
                "fechaDesde"=>  $this->input->post('fechaDesde'),
                "hora_desde"=> $this->input->post('horaDesde'),
                "hora_hasta"=> $this->input->post('horaHasta'),
                "cod_comision"=>  $this->input->post('cod_comision'),
                "cod_salon"=>  $this->input->post('cod_salon'),
                 "codigo_horario"=>$this->input->post('codigo_horario'),
                 "cod_materia"=> $this->input->post('cod_materia')
            );            
            $jsonFechas2 = json_encode(array('cod_comision'=>  $this->input->post('cod_comision'),'fecha_desde'=>  $this->input->post('fechaDesde'), "fecha_hasta"=>  $this->input->post('finalizacion')));
            $jsonDatos = json_encode(array(
                "fechaDesde"=>  $this->input->post('fechaDesde'),
                "hora_desde"=> $this->input->post('horaDesde'),
                "hora_hasta"=> $this->input->post('horaHasta'),
                "fechaHasta"=> $this->input->post('finalizacion'),
                "dias_repeticion" => $this->input->post('idDia')
            ));
            $this->form_validation->set_rules('idDia[]', lang('idDia_horario'), 'required|numeric|validarHorarioCicloComision['.$jsonFechas2.']');
            $this->form_validation->set_rules('finalizacion', lang('finalizacion_horarios'), 'required|validarFeriadoRecesoDesdeHasta[' . $this->input->post('fechaDesde') . ']|validarFechaFinPosterior[' . $this->input->post('fechaDesde') . ']|validarDuracionFechas[' . $this->input->post('fechaDesde') . ']|validarSalonHorarioSerie['.  json_encode($arrayJson).']|validarRepeticionHorariosFilial['.$jsonDatos.']');
        } else {
            $jsonFechas2 = json_encode(array('cod_comision'=>  $this->input->post('cod_comision'),'fecha_desde'=>  $this->input->post('fechaDesde'), "fecha_hasta"=>  $this->input->post('fechaDesde')));
            $this->form_validation->set_rules('cod_comision', lang('idDia_horario'), 'validarHorarioCicloComision['.$jsonFechas2.']');
        }
        $resultado = '';
        if ($this->form_validation->run() == FALSE){
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $tipoRepeticion = $this->input->post('tipoRepeticion');
            $dia_repetir = $this->input->post('idDia') != 0 ? $this->input->post('idDia') : array();
            $finaliza_valor = $this->input->post('finalizacion') == '' ? '' : formatearFecha_mysql($this->input->post('finalizacion'));
            $diad = formatearFecha_mysql($this->input->post('fechaDesde'));
            $arrGuarda = array(
                "codigo" => $this->input->post('codigo_horario'),
                "comision" => $this->input->post('cod_comision'),
                "salon" => $this->input->post('cod_salon'),
                "profesor" => $this->input->post('profesores'),
                "materia" => $this->input->post('cod_materia'),
                "diad" => $diad,
                "diah" => $diad,
                "horad" => $this->input->post('horaDesde'),
                "horah" => $this->input->post('horaHasta'),
                "tipo_repeticion" => $tipoRepeticion,
                "repetir_cada" => $this->input->post('frecuenciaRepeticion'),
                "dia_repetir" => $dia_repetir,
                "finaliza" => $this->input->post('tipoFinalizacion'),
                "finaliza_valor" => $finaliza_valor,
                "modifica_serie" => $this->input->post('modifica_serie'),
                "usuario" => $this->session->userdata('codigo_usuario'));            
           $resultado = $this->Model_horarios->guardarHorarios($arrGuarda);
        }        
        echo json_encode($resultado);
    }

    /**
     * da de baja un horario o toda la serie del evento del calendario.
     * @access public
     * @return boolean
     */
    public function bajaHorario(){
        $this->load->library('form_validation');
        $codigoHorario = $this->input->post('codigo_horario');
        $this->form_validation->set_rules('codigo_horario',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $opcionesArray = array("soloeste" => $this->input->post('soloeste'));
            echo json_encode($this->Model_horarios->bajaHorario($codigoHorario, $opcionesArray));
        }    
    }

    /**
     * carga la vista del formulario salones
     * @access public
     * @return vista form salones
     */
    public function frm_salones(){
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $cod_salon = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $data = array();
            $arrayColores = array(
                "#bfdcea"=>'#bfdcea',
                "#99c7de"=>'#99c7de',
                "#66aacd"=>'#66aacd',
                "#338ebd"=>'#338ebd',
                "#0072ac"=>"#0072ac",
                "#f9ebc5"=>"#f9ebc5",
                "#f5dfa3"=>"#f5dfa3",
                "#f1cf75"=>"#f1cf75",
                "#ecbf47"=>"#ecbf47",
                "#e7af19"=>"#e7af19"
            );
            $this->load->model("Model_salones", "", false, $arrConfig);
            $salones = $this->Model_salones->getSalon($cod_salon);
            $data['salones'] = $salones;
            $data['colores'] = $arrayColores;
            $data["tipos_salones"] = $this->Model_salones->getTiposSalones();
            $this->load->view("horarios/frm_salones", $data);
         }
    }   
    
    /**
     * carga la vista del formulario baja horario
     * @access public
     * @return vista baja horario
     */
    public function frm_baja_horario(){
        $this->load->library('form_validation');
        $codigo_horario = $this->input->post('codigo_horario');
        $this->form_validation->set_rules('codigo_horario',lang('codigo'),'numeric');
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $data['codigo_horario'] = $codigo_horario;
            $data["existenCorrelativos"] = $this->Model_horarios->exitenEventosCorrelativos($codigo_horario);
            $data["tiene_asistencia"] = $this->Model_horarios->validarAsistenciaHorario($codigo_horario);
          
            $this->load->view('horarios/frm_baja_horario', $data);
        }  
    }

    public function frm_drag(){
         $this->load->library('form_validation');
        $codigo_horario = $this->input->post('codigo_horario');
        $this->form_validation->set_rules('codigo_horario',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $data['fechaDesde'] = $this->input->post('fechaDesde');
            $data['horaDesde'] = $this->input->post('horaDesde');
            $data['horaHasta'] = $this->input->post('horaHasta');
            $data['cod_salon'] = $this->input->post('cod_salon');
            $data['cod_comision'] = $this->input->post('cod_comision');
            $data['cod_materia'] = $this->input->post('cod_materia');
            $data['cod_profesor'] = $this->input->post('cod_profesor');
            $data['tipoRepeticion'] = $this->input->post('tipoRepeticion');
            $data['frecuenciaRepeticion'] = $this->input->post('frecuenciaRepeticion');
            $data['finalizacion'] = $this->input->post('finalizacion');
            $data['tipoFinalizacion'] = $this->input->post('tipoFinalizacion');
            $data["dias"] = $this->Model_horarios->getSerieDias($codigo_horario);
            $data["fin_repeticion"] = $this->Model_horarios->getFechaFinSerie($codigo_horario);
            $data["codigo_horario"] = $codigo_horario;
            $this->load->view('horarios/frm_confirmarModificacion', $data);
        }
    }

    /**
     * guarda o modifica un salon.
     * @access public
     * @return boolean
     */
    public function guardarSalon(){
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_salones", "", false, $config);
        $this->load->library('form_validation');
        $cod_salon = $this->input->post('codigo');
        $color = $this->input->post('color');
        $resultado = '';
        $this->form_validation->set_rules('salon', lang('salon_horarios'), 'required|validarNombreSalon['.$cod_salon.']');
        $this->form_validation->set_rules('cupo', lang('cupo_salon_horarios'), 'required|integer');
        $this->form_validation->set_rules('tipo', lang('tipo_salon_horarios'), 'required|validarCantidaTipoSalon['.$cod_salon.']');
        $objSalon = $this->Model_salones->getSalon($cod_salon);
        if($objSalon->color != $color){
            $this->form_validation->set_rules('color', lang('color_salon_horarios'), 'required');//validarColorSalon[' . $cod_salon . ']
        }
            
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => 0,
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['codigo'] = $cod_salon;
            $data_post['salon'] = $this->input->post('salon');
            $data_post['cupo'] = $this->input->post('cupo');
            $data_post['tipo'] = $this->input->post('tipo');
            if($cod_salon == -1){
                $data_post['color'] = $this->getColorNuevoSalon($this->input->post('tipo'));
            }else{
                $data_post['color'] = $objSalon->color;
            }
            $data_post['estado'] = '0';
            $resultado = $this->Model_salones->guardar($data_post);
        }
        echo json_encode($resultado);
    }

    public function cambiarEstadoSalon(){
        $this->load->library('form_validation');
        $cod_salon = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        }else{
            $cambiarEstadoSalon = $this->Model_salones->cambiarEstado($cod_salon);
            echo json_encode($cambiarEstadoSalon);
        }
    }

    /**
     * guarda o modifica un feriado  del calendario.
     * @access public
     * @return boolean
     */
    public function guardarFeriado(){        
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_feriados", "", false, $config);
        $diacompleto = $this->input->post('dia-completo');
        $repite = $this->input->post('repetir') == 'on' ? '1' : '0';
        $horadesde = $this->input->post('hora-desde');
        $horahasta = $this->input->post('hora-hasta');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nombre', lang(''), 'required');
        $datos = '';
        if ($diacompleto === 'on') {
            $datos = array('repite' => $repite);
        } else {
            $this->form_validation->set_rules('hora-desde', lang('horadesde_horario'), 'required');
            $this->form_validation->set_rules('hora-hasta', lang('horahasta_horario'), 'required|validarHora[' . $horadesde . ']');
            $datos = array('hora_desde' => $horadesde, 'hora_hasta' => $horahasta, 'repite' => $repite);
        }
        $datos['fecha']=  $this->input->post('fecha');
        $this->form_validation->set_rules('fecha', lang(''), 'required|validarFeriadoConHorario|validarFeriadoCargado[' . json_encode($datos) . ']|validarAsistenciaCargada[' . json_encode($datos) . ']');
        $resultado = '';
        if ($this->form_validation->run() == FALSE){
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $arrGuarda = array(
                "cod_feriado" => $this->input->post('cod_feriado'),
                "nombre" => $this->input->post('nombre'),
                "fecha" => formatearFecha_mysql($this->input->post('fecha')),
                "diacompleto" => $diacompleto,
                "hora_desde" => $horadesde,
                "hora_hasta" => $horahasta,
                "repite" => $repite,
                "usuario" => $this->session->userdata('codigo_usuario'));
            $resultado = $this->Model_feriados->guardarFeriado($arrGuarda);
        }
        echo json_encode($resultado);
    }

    /**
     * retorna profesores  para horario general
     * @access public
     * @return json de profesores
     */
    public function getProfesores(){
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_profesores", "", false, $config);
        echo json_encode($this->Model_profesores->getProfesores());
    }

    public function frm_inscriptos_horario(){
         $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_matriculas_horarios", "", false, $arrConfig);
        $this->load->model("Model_asistencias", "", false, $arrConfig);
        $cod_horario = $this->input->post('codigo_horario');
        $this->form_validation->set_rules('codigo_horario',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $claves = array("validacion_ok");
            $data['langFrm'] = getLang($claves);
            $data['inscriptos'] = $this->Model_matriculas_horarios->getInscriptosHorario($cod_horario);
            $data['horario'] = $this->Model_horarios->getHorario($cod_horario);
            $data['arrAsistencias'] = $this->Model_asistencias->getArrayEstadoAsistencias();
            $this->load->view('horarios/frm_inscriptos_horario', $data);
        }
    }

    public function frm_excepciones(){
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_matriculas_horarios", "", false, $arrConfig);
        $cod_horario = $this->input->post('codigo_horario');
        $cod_mat_horario = $this->input->post('cod_matricula_horario');
        $this->form_validation->set_rules('codigo_horario',lang('codigo'),'numeric');
        if ($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $data['alumnos'] = $this->Model_matriculas_horarios->getInscriptosExcepcion($cod_horario, $cod_mat_horario,$apellidoPrimero);
            $data['codigo_horario'] = $cod_horario;
            $this->load->view('horarios/frm_excepciones', $data);
        }    
    }

    public function listarHorariosCambiar(){     
        $cod_horario = $this->input->post('codigo_horario');
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = "";
        $arrFiltros["sSortDir"] = "";
        echo json_encode($this->Model_horarios->getHorariosCambiar($cod_horario, $arrFiltros));
    }

    public function guardarExcepcion(){        
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_matriculas_horarios", "", false, $config);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo_horario', lang(''), 'required');
        $this->form_validation->set_rules('cod_matricula_horario[]', lang(''), 'required|validarAsistenciaNull');
        $resultado = '';
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
        } else {
            $arrGuarda = array(
                "horario_nuevo" => $this->input->post('codigo_horario'),
                "cod_inscripciones" => $this->input->post('cod_matricula_horario'),
                "usuario" => $this->session->userdata('codigo_usuario'));
            $resultado = $this->Model_matriculas_horarios->guardarExcepcion($arrGuarda);
        }
        echo json_encode($resultado);
    }

    /**
     * carga la vista del formulario feriados
     * @access public
     * @return vista form salones
     */
    public function frm_feriados(){        
        $filial = $this->session->userdata('filial');
        $arrConfig = array('codigo_filial' => $filial['codigo']);
        $this->load->model("Model_feriados", "", false, $arrConfig);
        $data = array();
        $data['feriados'] = $this->Model_feriados->getFeriados(false, '0');
        $data['fecha_actual'] = formatearFecha_pais(date("Y-m-d"));
        $this->load->view("horarios/frm_feriados", $data);
    }

    public function cambiarEstadoFeriado(){
        $this->load->library('form_validation');
        $config = array("codigo_filial" => $this->filial["codigo"]);
        $this->load->model("Model_feriados", "", false, $config);
        $codUsuario = $this->session->userdata('codigo_usuario');
        $cod_feriado = $this->input->post('cod_feriado');        
        $this->form_validation->set_rules('codigo_feriado',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $comentarios = 'prueba baja de feriados';
            $estado = 1;
            $cambioestado = array(
                'cod_feriado' => $cod_feriado,
                'comentario' => $comentarios,
                'cod_usuario' => $codUsuario,
                'estado'=> $estado
            );
            $ferCambioEstado = $this->Model_feriados->cambioEstado($cambioestado);         
            echo json_encode($ferCambioEstado);
        }   
    }
    
    public function modificar_feriado(){
        $cod_feriado = $this->input->post('codigo');
        $objFeriado = $this->Model_horarios->getObjFeriado($cod_feriado);
        echo json_encode($objFeriado);
    }
    
    public function getFechas(){
       $filial = $this->session->userdata('filial');
       $this->load->model("Model_filiales","",true,$filial['codigo']);
    }
   
    public function retornoColorNuevoSalon(){
        $tipo_salon = $this->input->post('tipo');
        $resultado = $this->getColorNuevoSalon($tipo_salon);
        echo json_encode($resultado);
    }
   
   public function getColorNuevoSalon($tipo_salon){       
       $arrColores = '';
       if($tipo_salon == 'COCINA'){
           $arrColores = array("#0072ac","#66aacd","#bfdcea","#338ebd","#99c7de");
       } else {
           $arrColores = array("#e7af19","#f1cf75","#f9ebc5","#ecbf47","#f5dfa3");
       }
       $resultado = $this->Model_salones->getColorNuevoSalon($tipo_salon,$arrColores);
       return $resultado;
   }
   
   
   //script para actualizar los colores de los salones del sist.
   public function actualizarColorSalon(){
       $arrResp = $this->Model_horarios->actualizarColorSalon('AULA');
       $arrResp = $this->Model_horarios->actualizarColorSalon('COCINA');
       echo json_encode($arrResp);
   }
}