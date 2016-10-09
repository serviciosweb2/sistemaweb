<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Aspirantes extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configAspirantes = array("filial" => $filial);
        $this->load->model("Model_aspirantes", "", false, $configAspirantes);
    }

    /**
     * retorna vista de aspirantes main panel
     * @access public
     * @return vista de main panel (Aspirantes)
     */
    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata("filial");
        $configComoNosConocio = array("idioma" => get_idioma());
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_como_nos_conocio", "", false, $configComoNosConocio);        
        $this->load->model("Model_cursos", "", false, $arrConf);
        $data['page'] = 'aspirantes/vista_aspirantes'; // pasamos la vista a utilizar como parámetro
        $data['js'] = 'aspirantes';
        $claves = array(
            'codigo',
            'pasado_alumno',
            'detalle',
            'usuario',
            'estado'
        );
        $menu = getMenuJson('aspirantes');
        $data['menuJson'] = $menu;
        $data['lang'] = getLang($claves);
        $data['columns'] = $this->getColumns();
        $data['seccion'] = $this->seccion;
        $data['tipo_contactos'] = $this->Model_aspirantes->getTiposContacto();
        $data['arrMedios'] = $this->Model_como_nos_conocio->getComoNosConocio($filial["codigo"]);
        $data['arrCursos'] = $this->Model_cursos->listar();
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();

        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        if($pais == 2)
        {
            $columnas = array(array("nombre" => lang('codigo_de_aspirante'), "campo" => 'codigo'),
                array("nombre" => lang("estado_aspirante"), "campo" => "pasado_alumno"),
                array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
                array("nombre" => lang('documento'), "campo" => 'aspirante_documento'),
                array("nombre" => lang("email"), "campo" => "email"),
                array("nombre" => lang("tel_empresa"), "campo" => "telefono_empresa"),
                array("nombre" => lang("telefono"), "campo" => "telefono"),
                array("nombre" => lang('tipo_contacto'), "campo" => 'tipo_contacto'),
                array("nombre" => lang('medio'), "campo" => 'como_nos_conocio'),
                array("nombre" => lang("cursos_de_interes"), "campo" => 'nombre_curso'),
                array("nombre" => lang("turno"), "campo" => "turno"),
                array("nombre" => lang("fecha_alta"), "campo" => "fechaalta")
            );
        }
        else
        {
            $columnas = array(array("nombre" => lang('codigo_de_aspirante'), "campo" => 'codigo'),
                array("nombre" => lang("estado_aspirante"), "campo" => "pasado_alumno"),
                array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
                array("nombre" => lang('documento'), "campo" => 'aspirante_documento'),
                array("nombre" => lang("email"), "campo" => "email"),
                array("nombre" => lang("telefono"), "campo" => "telefono"),
                array("nombre" => lang('tipo_contacto'), "campo" => 'tipo_contacto'),
                array("nombre" => lang('medio'), "campo" => 'como_nos_conocio'),
                array("nombre" => lang("cursos_de_interes"), "campo" => 'nombre_curso'),
                array("nombre" => lang("turno"), "campo" => "turno"),
                array("nombre" => lang("fecha_alta"), "campo" => "fechaalta")
            );
        }
        return $columnas;
    }

    public function get_aspirantes(){
        $arrResp = array();
        $nombre = $this->input->post('nombre') ? $this->input->post("nombre") : null;
        $apellido = $this->input->post('apellido') ? $this->input->post("apellido") : null;
        $codigo = $this->input->post("codigo");
        $aspirantes = $this->Model_aspirantes->getAspirantes($codigo, $nombre, $apellido);
        $arrResp['data']['aspirantes'] = $aspirantes;
        echo json_encode($arrResp);
    }
    
    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    /**
     * retorna lista de aspirantes para mostrar en index de main panel
     * @access public
     * @return json de listado de aspirantes
     */
    public function listar() {// FUNCION QUE ENVIA LOS RESULTADOS("<td>") A LA TABLA
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) && $_POST['iDisplayLength'] <> -1 ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $tipoContacto = isset($_POST['tipo_contacto']) && $_POST['tipo_contacto'] <> -1 ? $_POST['tipo_contacto'] : null;
        $medio = isset($_POST['medio']) && $_POST['medio'] <> -1 ? $_POST['medio'] : null;
        $curso = isset($_POST['curso_interes']) && $_POST['curso_interes'] <> -1 ? $_POST['curso_interes'] : null;
        $turno = isset($_POST['turno']) && $_POST['turno'] <> -1 ? $_POST['turno'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) && $_POST['fecha_desde'] <> '' ? formatearFecha_mysql($_POST['fecha_desde']) : null;
        $fechaHasta = isset($_POST['fecha_hasta']) && $_POST['fecha_hasta'] <> '' ? formatearFecha_mysql($_POST['fecha_hasta']) : null;
        $esAlumno = null;
        if (isset($_POST['es_alumno']) && $_POST['es_alumno'] <> -1){
            $esAlumno = $_POST['es_alumno'] == "es_alumno";
        }
        $valores = $this->Model_aspirantes->listarAspirantes($arrFiltros, $separador, $fechaDesde, $fechaHasta, $curso, 
                $tipoContacto, $medio, $turno, $esAlumno);
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $this->load->helper('alumnos');
            $nombreApellido = formatearNombreColumnaAlumno();
            $tipoReporte = $_POST['tipo_reporte'];
            $exp = new export($tipoReporte);
            $arrTemp = array();
            foreach ($valores['aaData'] as $valor) {
                $telefono = $valor[5];
                if (strlen($telefono) > 13 && $tipoReporte == 'pdf'){
                    if (strpos($telefono, " ")){
                        $telefono = str_replace(" ", "\n", $telefono);
                    } else {
                        $temp = str_split($telefono, 13);
                        $telefono = implode("\n", $temp);
                    }
                }
                $documento = $valor[3];
                if (strlen($documento) > 13 && $tipoReporte == 'pdf'){
                    if (strpos($documento, " ")){
                        $documento = str_replace(" ", "\n", $documento);
                    } else {
                        $temp = str_split($documento, 13);
                        $documento = implode("\n", $temp);
                    }
                }
                
                if ($tipoReporte == 'pdf'){
                    $temp = str_split($valor[4], 34);
                    $email = implode("\n", $temp);
                } else {
                    $email = $valor[4];
                }
                if ($tipoReporte == 'pdf'){
                    $temp = str_split($valor[8], 26);
                    $curso = implode("\n", $temp);
                } else {
                    $curso = $valor[8];
                }
                
                $arrTemp[] = array(
                    $valor[0],                  // codigo
                    ucfirst(substr($valor[1], 0, 13)),   // codigo alumno                    
                    substr($valor[2], 0, 32),   // nombre
                    $documento,                 // documento                    
                    $email,                     // emial
                    $telefono,                  // telefono
                    substr($valor[6], 0, 18),   // tipo contacto
                    substr($valor[7], 0, 12),   // medio
                    $curso,   // curso de interes                    
                    $valor[9],                  // turno
                    $valor[10]);                // fecha_alta
            }
            $arrTitle = array(
                lang("codigo"),                
                lang("ALUMNO"),
                $nombreApellido,
                substr(lang("documento"), 0, 9),
                lang("email"),
                lang("telefono"),
                substr(lang("tipo_contacto"), 0, 16),
                lang("medio"),
                lang("cursos_de_interes"),
                lang("turno"),
                lang("prioridad_alta")
            );
            $arrWidth = array(14, 20, 42, 20, 42, 30, 28, 20, 42, 14, 16);
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
            $exp->setReportTitle($filial['nombre']." - ".lang("informe_de_aspirantes"));
            $exp->setMargin(2, 8);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }
    }

    /**
     * Retorna json de localides en base al post de provincia.
     * @access public
     * @return json de localidades
     */
    public function getlocalidades() {
        $codigoProvincia = $_POST['provincia'];
        $this->load->model("Model_provincias", "", false, $codigoProvincia);
        $localidades = $this->Model_provincias->getLocalidades();
        echo json_encode($localidades);
    }
    
    
    public function getmodalidades() {
        $codigo = $_POST['cod_curso'];
        $modalidades = $this->Model_aspirantes->getModalidades($codigo);
         foreach ($modalidades as $key => $value) {
            $modalidades[$key]['nombre'] = lang($value['modalidad']);
        }
        if(!sizeOf($modalidades)){
           $modalidades[0]['nombre'] = lang('normal');
           $modalidades[0]['modalidad'] = 'normal';
        }
        echo json_encode($modalidades);
    }

    /**
     * carga la vista del formulario aspirante
     * @access public
     * @return vista frm_aspirante
     */
    public function form_aspirante() {
        $this->load->helper("array");
        $filial = $this->session->userdata('filial');       
        $this->load->library('form_validation');
        $data = '';
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $configComoNosConocio = array("idioma" => get_idioma());
        $this->load->model("Model_Como_nos_conocio", "", false, $configComoNosConocio);
        $arrConf = array('codigo_filial' => $filial["codigo"]);
        $this->load->model("Model_cursos", "", false, $arrConf);
        $empresas_tel = $this->Model_paises->getEmpresasTelefonicas();
        $prov = $this->Model_paises->getprovincias();
        $tipo_dni = $this->Model_paises->getDocumentosPersonasFisicas();
        $comoNosCon = $this->Model_Como_nos_conocio->getComoNosConocio($filial['codigo'], 1);
        $codigo = $this->input->post('codigo');
        $telefonos = $this->Model_aspirantes->getTodosLosTelefonos($codigo);
        $aspirante = $this->Model_aspirantes->getAspirante($codigo);
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            if ($codigo != -1) {
                if ($aspirante->cod_localidad != '') {
                    $this->load->model("Model_localidades", "", false, $aspirante->cod_localidad);
                    $localidadasp = $this->Model_localidades->getLocalidad();
                    $data['provincia_aspirante'] = $localidadasp->provincia_id;
                    $this->load->model("Model_provincias", "", false, $data['provincia_aspirante']);
                    $localidades = $this->Model_provincias->getLocalidades();
                    $data['localidades'] = $localidades;
                }
            }
            //verifica se la filial tiene el como nos conoció del alumno, se no tiene lo agrega, para que el formulario tenga la opcion
            if(!empty($aspirante->comonosconocio)) {
                $tieneCnc = false;
                foreach ($comoNosCon as $cnc) {
                    if($cnc['codigo'] == $aspirante->comonosconocio) {
                        $tieneCnc = true;
                    }
                }
                if(!$tieneCnc) {
                    $comoNosCon[] = $this->Model_Como_nos_conocio->getComoNosConocioArray($aspirante->comonosconocio);
                }
            }

            $tipo_telefono = Vtelefonos::getArray();
            $claves = Array("BIEN", "ERROR", "validacion_ok", "no_se_puede_eliminar_un_telefono_default", "cambielo_e_intente_nuevamente", 'nuevo_tel','tel_default_invalido');
            $data['tipo_telefonos'] = $tipo_telefono;
            $data['empresas_tel'] = $empresas_tel;
            $data['comoNosCon'] = $comoNosCon;
            $data['tipo_dni'] = $tipo_dni;
            $data['provincias'] = $prov;
            $data['aspirante'] = $aspirante;
            $data['telefonos'] = $telefonos;
            $data['langFrm'] = getLang($claves);
            $data['arrCursos'] = $this->Model_cursos->listar(Vcursos::getEstadoHabilitado());
            $data['cursos_interes'] = getArraySimple($aspirante->getCursosDeInteres());
            $data['pais'] = $filial["pais"];
            //$data['turnos'] = $this->Model_aspirantes->getTurnos();
            $data['arrTurnos'] = $this->Model_aspirantes->getTurnos();
            $data['turnos'] = getArraySimple($aspirante->getTurnos2());
            //$data['arrModalidades'] = $this->Model_aspirantes->getModalidades("1");
            $data['modalidades'] = getArraySimple($aspirante->getModalidades2());
            $data['periodos'] = getArraySimple($aspirante->getPeriodos());
            
            foreach ($data['cursos_interes'] as $index=>$valor){
                $data['arrModalidades'][$index] = $this->Model_aspirantes->getModalidades($valor);
            }
            
            $this->load->view('aspirantes/frm_aspirante', $data);
        }
    }

    /**
     * Guarda todos los datos de aspirante
     * @access public
     * @return json de respuesta
     */
    public function guardar() {      
        $this->load->helper('formatearfecha');
        $resultado = '';
        $this->load->library('form_validation');
        //$telefonos = json_decode($this->input->post('telefonos'),true);
        $errors = '';
        $tipoDniAlumno = $this->input->post('tipo');
        $documento = $this->input->post('documento');
        $this->form_validation->set_rules('nombre', lang('nombre'), 'required|max_length[50]|validarNombreApellido[' . 'nombreAspiranteInvalido' . ']');
        $this->form_validation->set_rules('apellido', lang('apellido'), 'required|max_length[100]|validarNombreApellido[' . 'apellidoAspiranteInvalido' . ']');
        $this->form_validation->set_rules('observaciones', lang('observaciones'), 'max_length[255]');
        $this->form_validation->set_rules('comoNosConocio', lang('comonosconocio'), 'required');
        
        $this->form_validation->set_rules('tipo', lang('tipoDni'), 'max_length[4]|integer |validarExistenciaTipoDniNumero[' . $documento . ']');
        $this->form_validation->set_rules('documento', lang('documento_alumno'), 'max_length[50]|validarDocumentoIdentidad[' . $tipoDniAlumno . ']');

        //$this->form_validation->set_rules('cursos_interes[]', lang('cursos_de_interes'), 'required');
        $this->form_validation->set_rules('tipo_contacto', lang("tipo_de_contacto"), 'required');
        $this->form_validation->set_rules('cursos_interes', lang("cursos_de_interes"), 'required');
        if ($this->input->post('email') <> '') {
            $this->form_validation->set_rules('email', lang('email'), 'valid_email');
        } else {
            if ($telefonos != '' && isset($telefonos['numero'])) {
                     $_POST['tel_numero' ] = $telefonos['numero'];
                     $this->form_validation->set_rules('tel_numero', lang('tel_numero'), 'required|numeric|integer');
            } else {
                $errors = lang('indique_email_telefono');
            }
        }
        if ($this->form_validation->run() == FALSE || $errors <> '') {
            $errors .= validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else if (!is_array($this->input->post("cursos_interes")) || 
                count($this->input->post("cursos_interes")) == 0 ||
                $this->input->post('cursos_interes')[0] == ''){
            $base = lang("__es_requerido");
            $resultado = array(
                'codigo' => '0',
                'msgerror' => str_replace("$$$", lang("cursos_de_interes"), $base),
                'errNo' => '',
            );
        } else {
            $data_post['aspirante']['codigo'] = $this->input->post('cod_aspirante');
            $data_post['aspirante']['usuario_creador'] =  $codUsuario = $this->session->userdata('codigo_usuario');
            $data_post['aspirante']['nombre'] = $this->input->post('nombre');
            $data_post['aspirante']['fechanaci'] = $this->input->post('fechanaci') ? formatearFecha_mysql($this->input->post('fechanaci')) : '';
            $data_post['aspirante']['tipo'] = $this->input->post('tipo');
            $data_post['aspirante']['documento'] = $this->input->post('documento');
            $data_post['aspirante']['fechaalta'] = date("Y-m-d H:i:s");
            $data_post['aspirante']['observaciones'] = $this->input->post('observaciones');
            $data_post['aspirante']['cod_localidad'] = $this->input->post('cod_localidad') == '' ? null : $this->input->post('cod_localidad');
            $data_post['aspirante']['codpost'] = $this->input->post('codpost');
            $data_post['aspirante']['email'] = $this->input->post('email');
            $data_post['aspirante']['comonosconocio'] = $this->input->post('comoNosConocio');
            $data_post['aspirante']['apellido'] = $this->input->post('apellido');
            $data_post['aspirante']['calle'] = $this->input->post('calle');
            $data_post['aspirante']['calle_numero'] = $this->input->post('calle_numero');
            $data_post['aspirante']['calle_complemento'] = $this->input->post('calle_complemento');
            $data_post['accion'] = $this->input->post('accion');
            $arrTemp = [];
            if (isset($_POST['telefonos'])){
                $tels = $this->input->post('telefonos');
                if(is_array($tels)){
                    foreach($tels as $eltel){
                        $arrTemp[] = $eltel;
                    }
                }
                /*
                if (isset($_POST['telefono']) && $_POST['telefono_aspirante'] <> ''){
                    $tel = explode(" ", $_POST['telefono_aspirante']);
                    if (count($tel) > 1){
                        $arrTemp['cod_area'] = $tel[0];
                        $arrTemp['numero'] = $tel[1];
                    } else {
                        $arrTemp['cod_area'] = '';
                        $arrTemp['numero'] = $tel[0];
                    }
                    $arrTemp['tipo_telefono'] = $_POST['tipo_telefono'];
                }
                 */
            }
            $data_post['telefonos'] = $arrTemp;
            $data_post['aspirante']['cursos_interes'] = $this->input->post("cursos_interes");
            
            $data_post['aspirante']['turnos'] = $this->input->post("turnos");
            $data_post['aspirante']['modalidades'] = $this->input->post("modalidades");
            $data_post['aspirante']['periodos'] = $this->input->post("periodos");
            
            $data_post['aspirante']['tipo_contacto'] = $this->input->post("tipo_contacto");
            $data_post['aspirante']['barrio'] = $this->input->post('barrio') == '' ? null : $this->input->post("barrio");
            
            //$data_post['aspirante']['turnos'] = $this->input->post('turnos') == '' ? null : $this->input->post("turnos");
            $resultado = $this->Model_aspirantes->guardarAspirante($data_post);
            $resultado['accion'] = $data_post['accion'];
            //die($data_post['aspirante']['cursos_interes']);
        }
        echo json_encode($resultado);
    }

    /**
     * carga la vista del formulario para presupuestar a un aspirante
     * @access public
     * @return vista vista_form_presupuestar_aspirante
     */
    public function presupuestar_aspirante() {
        $this->load->library('form_validation');
        $cod_aspirante = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $filial = $this->session->userdata('filial');
            $arrConf = array('codigo_filial' => $filial["codigo"]);
            $this->load->model("Model_filiales", "", false, $filial["codigo"]);
            $this->load->model("Model_cursos", "", false, $arrConf);
            $this->load->model("Model_configuraciones", "", false, $arrConf);
            $cursos = $this->Model_cursos->getCursosHabilitados(true, null, 0);
            $aspirante = $this->Model_aspirantes->getAspirante($cod_aspirante);
            $data['diasVigenciaPresupuesto'] = $this->Model_configuraciones->getValorConfiguracion(null, 'DiasVigenciaPresupuesto');
            $data["aspirante"] = $aspirante;
            $data['nombre'] = '';
            $data['cursos'] = $cursos;
            $data['apellido'] = '';
            $this->load->view('vista_form_presupuestar_aspirante', $data);
        }
    }

    /**
     * retorna lista de comisiones para mostrar en vista de (presupuestar_aspirante)
     * @access public
     * @return json de listado de comiisones
     */
    public function listarComisiones() {// funcion accedida por ajax
        $this->load->helper('formatearhorario');
        $cod_plan_academico = $this->input->post("cod_plan_academico");
        $codigo_periodo = $this->input->post("periodo") ? $this->input->post("periodo") : -1;
        $modalidad = $this->input->post("modalidad");
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo" => $cod_plan_academico,
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_planes_academicos", "", false, $arrConfig);
        $comisiones = $this->Model_planes_academicos->getComisionesDisponiblesMatricular($codigo_periodo, $modalidad);
        echo json_encode($comisiones);
    }

    /**
     * retorna lista de planes para mostrar en vista de (presupuestar_aspirante)
     * @access public
     * @return json de listado de planes
     */
    public function listarPlan() {// Funcion accedida por ajax
        $filial = $this->session->userdata('filial');
        $cod_comision = $this->input->post('codigo');
        $periodos = $this->input->post('periodos');
        $arrConfig = array(
            "codigo" => $cod_comision,
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_comisiones", "", false, $arrConfig);
        $formasDePago = $this->Model_comisiones->getPlanesVigentesMatricular($periodos);
        echo json_encode($formasDePago);
    }

    /**
     * retorna lista de cuotas de acuerdo al plan seleccionado.
     * @access public
     * @return json cuotas de un plan.,
     */
    public function listarCuotas() {
        $this->load->helper('formatearCuotas');
        $filial = $this->session->userdata('filial');
        $arrConfig["codigo_filial"] = $filial["codigo"];
        $this->load->model("Model_planes_pagos", "", false, $arrConfig);
        $this->load->model("Model_conceptos", "", false, $arrConfig);
        $cod_plan = $this->input->post('codigo');
        $orden = array(array('campo' => 'orden',
                'orden' => 'ASC'));
        $arrcuotas = $this->Model_planes_pagos->getCuotasPlan($cod_plan, $orden, 'habilitada');
        $codconceptos = array();
        foreach ($arrcuotas as $key => $value) {
            $codconceptos[] = $key;
        }
        $conceptos = $this->Model_conceptos->getConceptos(null, $codconceptos);
        $cuotas = formatearCuotas($arrcuotas, $conceptos);
        echo json_encode($cuotas);
    }

    /**
     * Guarda todos los datos del form (presupuestar_aspirante)
     * @access public
     * @return json de respuesta
     */
    public function guardar_presupuesto() {
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_presupuestos", "", false, $arrConfig);
        $this->load->model("Model_configuraciones", "", false, $arrConfig);
        $this->form_validation->set_rules('cursos', lang('cursos_presupuesto'), 'required');
        $this->form_validation->set_rules('periodos', lang('periodos_presupuesto'), 'required');
        $this->form_validation->set_rules('plan', lang('plan_presupuesto'), 'required');
        $this->form_validation->set_rules('observaciones', lang('nombre'), 'max_length[255]');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'estado' => '0',
                'respuesta' => $errors
            );
        } else {
            $comision = $this->input->post('periodos');
            $data_post['cod_aspirante'] = $this->input->post('cod_aspirante');
            $data_post['presupuesto'] = array(
                'cod_plan' => $this->input->post('plan'),
                'codcomision' => $comision[1]['comision'],
                'observaciones' => $this->input->post('observaciones'),
                'fecha' => date("Y-m-d H:i:s"),
                'fechavigencia' => formatearFecha_mysql($this->input->post('fechaVigencia'))
            );
            $codfinanciaciones = $this->input->post('codigo-financiacion');
            $codconceptos = $this->input->post('plan-concepto');
            for ($i = 0; $i < count($codfinanciaciones); $i++) {
                $data_post['detalle'][$i]['financiacion'] = $codfinanciaciones[$i];
                $data_post['detalle'][$i]['concepto'] = $codconceptos[$i];
            }
            $resultado = $this->Model_presupuestos->guardarPresupuesto($data_post);
        }
        echo json_encode($resultado);
    }

    public function getListadoCentroReportes() {
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $arrResp = $this->Model_aspirantes->getListadoCentroReportes($idFilial, $arrLimit, $arrSort, $search, $searchField, $fechaDesde, $fechaHasta);
        echo json_encode($arrResp);
    }

    /* ESTA FNUCTION SE ACCEDE DESDE UN WEB SERVICES, no modificar, comentar ni eliminar */
    public function getDetallePresupuestos($idFilial, $codAspirante) {
        $arrResp = $this->Model_aspirantes->getDetallePresupuestos($idFilial, $codAspirante);
        echo json_encode($arrResp);
    }
    
    /** 
     * Retorna los horarios de una comision
     * @access public 
     * @return json de horarios de una comision
     */
    public function getHorarioComision() {
        $filial = $this->session->userdata('filial');
        $cod_comision = $this->input->post('cod_comision');
        $arrConfig = array(
            "codigo" => $cod_comision,
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_comisiones", "", false, $arrConfig);
        $horario = $this->Model_comisiones->getHorario();
        echo json_encode($horario);
    }

    /** 
     * Retorna los periodos de un curso
     * @access public
     * @return json de periodos de un curso
     */
    public function getPeriodosCurso() {
        $filial = $this->session->userdata('filial');
        $cod_plan_academico = $this->input->post('cod_plan_academico');
        $arrConfig = array(
            "codigo" => $cod_plan_academico,
            "codigo_filial" => $filial["codigo"]
        );
        $periodos = array();
        $this->load->model("Model_planes_academicos", "", false, $arrConfig);
        $periodos = $this->Model_planes_academicos->getPeriodosPlanAcademico($cod_plan_academico, null, true);
        echo json_encode($periodos);
    }

    public function detallesPlanPresupuesto() {
        $filial = $this->session->userdata('filial');
        $arrConfig = array(
            "codigo_filial" => $filial["codigo"]
        );
        $this->load->model("Model_planes_financiacion", "", false, $arrConfig);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('plan', lang('detalleplan_plan'), 'required');
        $this->form_validation->set_rules('codigo-financiacion[]', lang('detalleplan_financiacion'), 'required');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $detalles['cod_plan'] = $this->input->post('plan');
            $detalles['moneda'] = $filial['moneda'];
            $financiaciones = $this->input->post('codigo-financiacion');
            $conceptos = $this->input->post('plan-concepto');
            for ($i = 0; $i < count($financiaciones); $i++) {
                $detalles['financiaciones'][$i]['cod_financiacion'] = $financiaciones[$i];
                $detalles['financiaciones'][$i]['cod_concepto'] = $conceptos[$i];
            }
            $datosdetalle = $this->Model_planes_financiacion->getDetallesFinanciaciones($detalles);
            echo json_encode($datosdetalle);
        }
    }

    public function ver_presupuestos() {
        $cod_aspirante = $this->input->post('codigo_aspirante');
        $data['presupuestoAspirante'] = $this->Model_aspirantes->getPresupuestosAspirante($cod_aspirante);
        $data['nombreFormateado'] = $this->Model_aspirantes->getNombreAspirante($cod_aspirante);
        $this->load->view('aspirantes/ver_presupuestos', $data);
    }

    public function getDetallePresupuestoPlan() {
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial['codigo']);
        $codigos = json_decode($this->input->post('codigos'), true);
        $this->load->model("Model_presupuestos", "", false, $config);
        $retorno = $this->Model_presupuestos->getDetallePresupuestoPlan($codigos);
        return $retorno;
    }

    /* Esta function esta siendo accedida desde un WEB SERVICES NO MODIFICAR; ELIMINAR NI COMENTAR */
    public function getReporteWS(){
        $filial = $this->input->post("id_filial");
        $fechaDesde = $this->input->post("fecha_desde") && $this->input->post("fecha_desde") <> '' ? $this->input->post("fecha_desde") : null;
        $conexion = $this->load->database($filial, true);
        $arrResp = Vaspirantes::getReporteWS($conexion, $fechaDesde);
        echo json_encode($arrResp);
    }
    
    
    
}
