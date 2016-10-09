<?php

/**
 * Control Alumnos.
 *
 * @package  SistemaIGA comentario de prueba
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Ariel Di Cesare   <sistemas4@iga-la.net>
 * @version  $Revision: 1.0 $
 * @access   public
 */

// comentario para probar deploy

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion alumnos.
 */
class Alumnos extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_alumnos", "", false, $configAlumnos);
        /* CARGO EL LAG */
        $this->load->helper("datatables");
    }

    /**
     * retorna vista de alumnos main panel
     * @access public
     * @return vista de main ponel (Alumnos)
     */
    public function index($abrir_fancy = false, $codigo_aspirante = false) {
        $filial = $this->session->userdata("filial");
        $this->lang->load(get_idioma(), get_idioma());
        $claves = array('estado_alumno_cabecera', 'codigo', "BIEN", "HABILITAR", "INHABILITAR", "HABILITADO", "INHABILITADO", 'ocurrio_error', 'tel_formato_invalido', 'no_puede_reenviar_mail_al_alumno');
        $data['titulo_pagina'] = '';
        //modificacion franco ticket 5053->
        $this->form_alumnos();
        $this->load->model("Model_talles", "", false);
        $data['talles'] = $this->Model_talles->getTalles();
        $this->load->model("Model_provincias", "", false);
        $data['provincias'] = $this->Model_provincias->getprovincias(1);        
        $configComoNosConocio = array("idioma" => get_idioma());
        $this->load->model("Model_como_nos_conocio", "", false, $configComoNosConocio);
        $comoNosCon = $this->Model_como_nos_conocio->getComoNosConocio($filial['codigo']);
        $data['comonoscono']= $comoNosCon;
        // <-modificacion franco ticket 5053        
        $data['page'] = 'alumnos/vista_alumnos'; // pasamos la vista a utilizar como parámetr
        $data['seccion'] = $this->seccion;
        $data['abrir_fancy'] = $abrir_fancy;
        $data['cod_aspirante'] = $codigo_aspirante;
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('alumnos');
        $data['columns'] = $this->getColumns();
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
            array("nombre" => lang('fecha_nacimiento'), "campo" => 'fechanaci'),
            array("nombre" => lang('documento'), "campo" => 'documento'),
            array("nombre" => lang('localidad'), "campo" => 'localidad'),            
            //modificacion franco ticket 5053->
            array("nombre" => lang('domicilio'), "campo" => 'calle2'),
            array("nombre" => lang('como_nos_conocio'), "campo" => 'descripcion_'.get_idioma()),
            array("nombre" => lang('email'), "campo" => 'email'),         
            array("nombre" => lang('id_fiscal'), "campo" => 'razon_doc'),
            array("nombre" => lang('datos_talle'), "campo" => 'talle'),
            //<-modificacion franco ticket 5053
            array("nombre" => lang('fecha_alta'), "campo" => 'fechaalta'),
            array("nombre" => lang('estado_alumno'), "campo" => 'estado', "sort" => FALSE),            
            array("nombre" => lang('estado_alumno_cabecera'), "campo" => 'baja', 'bVisible' => false),            
            array("nombre" => lang('reenviar_mail'), "campo" => 'reenviar_mail', 'bVisible' => false));
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    /**
     * retorna lista de alumnos para mostrar en index de main panel
     * @access public
     * @return json de listado de alumno
     */
    public function listar() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $provincia = isset($_POST['provincia']) && $_POST['provincia'] <> -1 ? $_POST['provincia'] : null;
        $localidad = isset($_POST['localidad']) && $_POST['localidad'] <> -1 ? $_POST['localidad'] : null;
        $como_nos_conocio = isset($_POST['como_nos_conocio']) && $_POST['como_nos_conocio'] <> -1 ? $_POST['como_nos_conocio'] : null;
        $estado = isset($_POST['estado']) && $_POST['estado'] <> -1 ? $_POST['estado'] : null;
        $talle = isset($_POST['talle']) && $_POST['talle'] <> -1 ? $_POST['talle'] : null;
        $fechaaltaDesde = isset($_POST['fecha_alta_desde']) && $_POST['fecha_alta_desde'] <> '' ? formatearFecha_mysql($_POST['fecha_alta_desde']) : null;
        $fechaaltaHasta = isset($_POST['fecha_alta_hasta']) && $_POST['fecha_alta_hasta'] <> '' ? formatearFecha_mysql($_POST['fecha_alta_hasta']) : null;
        $valores = $this->Model_alumnos->listarAlumnosbusca($arrFiltros, $separador, $fechaaltaDesde, $fechaaltaHasta, $talle, 
                 $provincia, $localidad, $como_nos_conocio, $estado);
        if (isset($_POST['action']) && $_POST['action'] == "exportar"){
            $this->load->helper('alumnos');
            $nombreApellido = formatearNombreColumnaAlumno();
            $exp = new export($_POST['tipo_reporte']);
            $arrTemp = array();
            foreach ($valores['aaData'] as $valor) {
                $documento = $valor[3];
                if (strlen($documento) > 13){
                    if (strpos($documento, " ")){
                        $documento = str_replace(" ", "\n", $documento);
                    } else {
                        $temp = str_split($documento, 13);
                        $documento = implode("\n", $temp);
                    }
                }
                $temp = str_split($valor[6], 20);
                $domic = implode("\n", $temp);
                $arrTemp[] = array(
                    $valor[0],
                    $valor[1],
                    utf8_encode(substr($valor[2], 0, 32)),
                    $documento,
                    $valor[4],
                    $valor[5],                  
                    $domic,
                    substr($valor[7], 0, 12),
                    $valor[8],
                    $valor[9],
                    $valor[10],
                    $valor[11]);
            }
            
            $arrTitle = array(
                lang("codigo"),                
                $nombreApellido,
                lang('fecha_nacimiento'),
                substr(lang("documento"), 0, 9),
                lang('localidad'), 
                lang('domicilio'),
                lang('como_nos_conocio'),
                lang("email"),
                lang('id_fiscal'),
                lang('datos_talle'),                
                lang('fecha_alta'),
                lang('estado_alumno')
            );
            $arrWidth = array(12, 37, 18, 18, 40, 50, 27, 33, 18, 10, 17, 14);
            $filial = $this->session->userdata("filial");
            $arrInfo = array(
                array("txt" => lang("fecha_emision").": ".date("d/m/Y"), "size" => "8", "align" => "R", "width" => 286, "height" => 4),
                array("txt" => "Informe alumnos", "size" => "8", "align" => "R", "width" => 286, "height" => 4)
            );
            $exp->setTitle($arrTitle);
            $exp->setContent($arrTemp);
            $exp->setPDFFontSize(7);
            $exp->setColumnWidth($arrWidth);
            $file = FCPATH."assents\img\logo.jpg";
            $exp->setLogo($file);
            $exp->setInfo($arrInfo);
            $exp->setContentHeight(6);
            $exp->setReportTitle($filial['nombre']." - ".lang("ALUMNOS"));
            $exp->setMargin(2, 8);
            $exp->exportar();
        } else {
            echo json_encode($valores);
        }        
       // <-modificacion franco ticket 5053
    }

    /**
     * carga la vista del formulario alumno
     * @access public
     * @return vista form alumno
     */
    public function form_alumnos() {
        $provincia_alumno = '';
        $localidades = array();
        $arrRazones = array();
        $arrTelefono = array();
        $arrResponsables = array();
        $validar_session = session_method();
        $cod_aspirante = $this->input->post('codigo_aspirante');
        $cod_alumno = $this->input->post('codigo');
        $provincia_alumno = "";
        $localidadprov = "";
        $filial = $this->session->userdata('filial');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        $this->form_validation->set_rules('codigo_aspirante', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {

            //Cargo Modelos
            $id_pais_alumno = $filial['pais'];
            $this->load->model("Model_paises", "", false, $filial["pais"]);
            $paises = $this->Model_paises->getPaises();
            $configAspirante = array("filial" => $filial);
            $this->load->model("Model_aspirantes", "", false, $configAspirante);
            $this->load->model("Model_talles");
            $config = array("idioma" => get_idioma());
            $this->load->model("Model_estados_civiles", "", false, $config);
            $this->load->model("Model_Como_nos_conocio", "", false, $config);
            $this->load->model("Model_sexo", "", false, $config);
            //CARGO LOS ARRAY PARA LA VISTA
            $empresas_tel = $this->Model_paises->getEmpresasTelefonicas();
            $prov = $this->Model_paises->getprovincias();
            $tipo_dni = $this->Model_paises->getDocumentosPersonasFisicas();
            $tipo_identificacion = $this->Model_paises->getDocumentos();
            $mayoriaEdad = $this->Model_paises->getMayoriaEdadPorPais();
            $comoNosCon = $this->Model_Como_nos_conocio->getComoNosConocio($filial['codigo'], 1);
            $talles = $this->Model_talles->getTalles();
            $condicion = $this->Model_paises->getCondicionesSociales();
            $estado_c = $this->Model_estados_civiles->getEstados_civiles();
            $sexo = $this->Model_sexo->getSexos();
            $tipo_telefono = Vtelefonos::getArray();
            $pasar = $this->Model_aspirantes->ConsultarAspiranteAlumno($cod_aspirante);
            if (count($pasar) > 0) {
                echo 'El aspirante ya es un alumno';
                return;
            } else {

                //Carga de Aspirante

                if ($cod_aspirante != "") {
                    $aspirante = $this->Model_aspirantes->getAspirante($cod_aspirante);
                    $arrGenerarAlumno = array(
                        "nombre" => $aspirante->nombre,
                        "apellido" => $aspirante->apellido,
                        "tipo" => $aspirante->tipo,
                        "fechanaci" => " ",
                        "documento" => $aspirante->documento,
                        "id_localidad" => $aspirante->cod_localidad == '' ? '' : $aspirante->cod_localidad,
                        "codpost" => $aspirante->codpost,
                        "email" => $aspirante->email,
                        "comonosconocio" => $aspirante->comonosconocio,
                        "calle" => $aspirante->calle,
                        "calle_numero" => $aspirante->calle_numero,
                        "calle_complemento" => $aspirante->calle_complemento,
                        "fechaalta" => date("Y-m-d"),
                        "condicion" => 1,
                        'baja' => 0,
                        "id_lugar_nacimiento" => 1
                    );
                    $arrTelefono = $this->Model_aspirantes->getTelefonos($cod_aspirante);

                    $ObjAlumno = $this->Model_alumnos->convertirAspiranteAlumno($arrGenerarAlumno);

                    if ($ObjAlumno->id_localidad != '') {
                        $this->load->model("Model_localidades", "", false, $ObjAlumno->id_localidad);
                        $localidad = $this->Model_localidades->getLocalidad();
                        $loc = $localidad->provincia_id;
                        $this->load->model("Model_provincias", "", false, $loc);
                        $localidades = $this->Model_provincias->getLocalidades();
                        $provincia_alumno = $localidad->provincia_id;
                        $id_pais_alumno = $this->Model_provincias->getPais();
                    }
                }

                // Carga de datos del Alumno

                else {
                    $ObjAlumno = $this->Model_alumnos->getAlumno($cod_alumno);
                    $arrTelefono = $this->Model_alumnos->getTelefonos($cod_alumno);
                    $arrRazones = $this->Model_alumnos->getRazonesSociales($cod_alumno);
                    $arrResponsables = $this->Model_alumnos->getResponsables($cod_alumno);
                }
                $provincia_nacimiento = array();
                if ($ObjAlumno->getCodigo() != -1) {
                    $this->load->model("Model_localidades", "", false, $ObjAlumno->id_localidad);
                    $localidad = $this->Model_localidades->getLocalidad();
                    $this->load->model("Model_provincias", "", false, $localidad->provincia_id);
                    $localidades = $this->Model_provincias->getLocalidades();
                    $provincia_alumno = $localidad->provincia_id;
                    $this->load->model("Model_localidades", "", false, $ObjAlumno->id_lugar_nacimiento);
                    $localidadnac = $this->Model_localidades->getLocalidad();
                    $this->load->model("Model_provincias", "", false, $localidadnac->provincia_id);
                    $id_pais_alumno = $this->Model_provincias->getPais();
                    $localidadprov = $this->Model_provincias->getLocalidades();
                    $provincia_nacimiento = $localidadnac->provincia_id;
                    $prov = $this->Model_provincias->getprovincias($id_pais_alumno);
                }

                //verifica se la filial tiene el como nos conoció del alumno, se no tiene lo agrega, para que el formulario tenga la opcion
                if(!empty($ObjAlumno->comonosconocio)) {
                    $tieneCnc = false;
                    foreach ($comoNosCon as $cnc) {
                        if($cnc['codigo'] == $ObjAlumno->comonosconocio) {
                            $tieneCnc = true;
                        }
                    }
                    if(!$tieneCnc) {
                        $comoNosCon[] = $this->Model_Como_nos_conocio->getComoNosConocioArray($ObjAlumno->comonosconocio);
                    }
                }
                // Carga de claves para la traduccion

                $claves = Array(
                    'error_requerido', 'error_max_50', 'error_max_250', 'error_numeros',
                    'error_max_11', 'error_max_4', 'error_max_2', 'detalleTel_empresa', 'detalleTel_tipo',
                    'detalleTel_numero', 'detalleTel_prefijo', 'detalleTelTabla_empresa',
                    'detalleTelTabla_numero', 'detalleTelTabla_eliminar', 'detalleTelTabla_tipo',
                    'error_fecha', 'nombre', 'apellido', 'razon_social', 'identificacion', 'eliminar',
                    'domicilio', 'calle_numero', 'calle_complemento', "validacion_ok", "seleccione_razon",
                    'tipo_documento', 'numero', 'email', 'telefono', 'responsable_nrow', 'codigo',
                    'responsable_tipoDniInterno', 'responsable_condicionInterno', 'razon_condicion',
                    'tipo_telefono', 'datos_empresa', 'codarea', 'baja',
                    'tipo_identificacion', 'numero_identificacion', 'eliminar', 'guardado_correctamente',
                    'default', 'ver_editar', 'nuevo_tel', 'nuevo_responsable', 'nueva_razon', 'sinTelefono',
                    'agregar_razon', 'default_facturacion', 'vincular_seleccion',
                    'no_puede_borrar_el_telefono_predeterminado',
                    'telefono_default', 'recuperando', 'sugerencia_de_aspirante',
                    'no_tiene_telefono_default', 'crear_nueva_razon', 'tel_default_invalido', 'tel_formato_invalido', 'cargar_responsable_alumno', 'relacion',
                    'HABILITADO', 'INHABILITADO', 'telefono_default_vacio', 'direccion');

                $data['alumno'] = $ObjAlumno;
                $data['comonoscon'] = $comoNosCon;
                $data['condicion'] = $condicion;
                $data['tipo_dni'] = $tipo_dni;
                $data['prov'] = $prov;
                $data["localidades_nacimiento"] = $localidadprov;
                $data['localidades'] = $localidades;
                $data['sexo'] = $sexo;
                $data['estado_c'] = $estado_c;
                $data['talles'] = $talles;
                $data['tipo_telefono'] = $tipo_telefono;
                $data['empresas_tel'] = $empresas_tel;
                $data['session_info'] = $validar_session;
                $data['cod_aspirante'] = $cod_aspirante;
                $data['telefonos'] = $arrTelefono;
                $data['responsables'] = $arrResponsables;
                $data['tipo_identificacion'] = $tipo_identificacion;
                $data["provincia_alumno"] = $provincia_alumno;
                $data["provincia_nacimiento"] = $provincia_nacimiento;
                $data['razones'] = $arrRazones;
                $data['anios_mayoria_edad'] = $mayoriaEdad;
                $data['langFrm'] = getLang($claves);
                $data['relacion_alumno'] = array("padre" => lang('padre'), "madre" => lang('madre'), "tutor" => lang('tutor'));
                $data['tallesPais'] = $this->Model_alumnos->getTallesPais($filial['pais']);
                $data['pais'] = $filial["pais"];
                $data['paises'] = $paises;
                $data['pais_seleccionar'] = $id_pais_alumno;
                $codigoUsuario = $this->session->userdata("codigo_usuario");
                $conexion = $this->load->database($filial['codigo'], true);
                $arrSecciones = Vusuarios_sistema::getUsuariosPermisos($conexion, $filial['codigo'], $codigoUsuario, null, "nuevo_responsable");
                $data['permiso_nuevo_responsable'] = count($arrSecciones) > 0;
                $data['imagen_alumno'] = $ObjAlumno->getImagen();
                $this->load->view('alumnos/frm_alumno', $data);
            }
        }
    }

    public function getAlumnosHabilitadosSelect() {
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $buscar = $this->input->post('buscar');
        $estado = $this->input->post('estado');
        $alumnos = '';
        switch ($estado) {
            case 'habilitada':
                $alumnos = $this->Model_alumnos->getAlumnosHabilitados($buscar, $separador);
                break;

            case 'inhabilitada':
                $alumnos = $this->Model_alumnos->getAlumnos($buscar, $separador);
                break;
        }
        echo json_encode($alumnos);
    }

    /**
     * Retorna json de localides en base al post de provincia.
     * @access public
     * @return json de localidades
     */
    public function getlocalidades() {
        $nombreProv = $_POST['idprovincia'];
        $this->load->model("Model_provincias", "", false, $nombreProv);
        $localidades = $this->Model_provincias->getLocalidades();
        echo json_encode($localidades);
    }
    
    public function getProvincias(){
        $codPais = $_POST['id_pais'];
        $this->load->model("Model_paises", "", false, $codPais);
        $arrProvincias = $this->Model_paises->getprovincias();
        echo json_encode($arrProvincias);
    }

    /**
     * Guarda todos los datos de alumno , responsable , Razon social
     * @access public
     * @return json de respuesta
     */
    public function guardar() {
        $this->load->library('form_validation');
        $this->lang->load(get_idioma(), get_idioma());
        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        $errors = '';
        $telefonosAlumno = $this->input->post('telefonos');
        $documento = $this->input->post('documento');
        $responsables = $this->input->post('responsables') ? $this->input->post('responsables') : array();
        $razones = $this->input->post('razones') == 0 ? array() : $this->input->post('razones');
        $errors = '';
        $tipoDniAlumno = $this->input->post('tipoDniAlumno');
        $this->form_validation->set_rules('nombre', lang('nombre_alumno'), 'required|max_length[50]|validarNombreApellido[' . 'nombreAlumnoInvalido' . ']');
        $this->form_validation->set_rules('apellido', lang('apellido_alumno'), 'required|max_length[255]|validarNombreApellido[' . 'apellidoAlumnoInvalido' . ']');
        $this->form_validation->set_rules('sexo', lang('sexo'), 'requerido');
        $this->form_validation->set_rules('estado_civil', lang('estado_civil_alumno'), 'max_length[20]');
        $cod_alumno = $this->input->post('codigo');
        if ($cod_alumno == -1) {
            $this->form_validation->set_rules('tipoDniAlumno', lang('tipoDni'), 'required|max_length[4]|integer|validarExistenciaTipoDniNumero[' . $documento . ']');
        } else {
            $arrayAlumno = array(
                "documento" => $documento,
                "cod_alumno" => $this->input->post('codigo')
            );
            $this->form_validation->set_rules('tipoDniAlumno', lang('tipoDni'), 'required|max_length[4]|integer|validarExistenciaDniAlumnoModificado[' . json_encode($arrayAlumno) . ']');
        }
        $this->form_validation->set_rules('documento', lang('documento_alumno'), 'required|max_length[50]|validarDocumentoIdentidad[' . $tipoDniAlumno . ']');
        $this->form_validation->set_rules('fechanaci', lang('fechanaci_alumno'), 'required');
        $this->form_validation->set_rules('observaciones', lang('observaciones_alumno'), 'max_length[255]');
        $this->form_validation->set_rules('calle_alumno', lang('calle_alumno'), 'required|max_length[50]');
        $this->form_validation->set_rules('calle_num_alumno', lang('calle_num_alumno'), 'required|max_length[50]|integer');
        if ($pais != 1) {
            $this->form_validation->set_rules('codpost', lang('codpost_alumno'), 'required|max_length[50]|validarCodigoPostal[' . $pais . ']');
        } else {
            $this->form_validation->set_rules('codpost', lang('codpost_alumno'), 'required|max_length[50]');
        }
        $this->form_validation->set_rules('domiciProvincia', lang('domiciProvincia'), 'required|max_length[10]|integer');
        $this->form_validation->set_rules('domiciLocalidad', lang('domiciLocalidad'), 'required|max_length[10]|integer');
        $this->form_validation->set_rules('comonosconocio', lang('comonosconocio'), 'required');
        $this->form_validation->set_rules('email_alumno', lang('email_alumno'), 'required|valid_email|validarExistenciaMail[' . $cod_alumno . ']');
        $this->form_validation->set_rules('telefonos', lang('telefonos'), 'required');
        $this->form_validation->set_rules('talle', lang('datos_talle'), 'required');
        $telDefault = '';
        if ($this->input->post('telefonos')) {
            foreach ($telefonosAlumno as $t => $telefonoAlumno) {
                if ($telefonoAlumno['baja'] == 0) {
                    if ($telefonoAlumno['numero'] == '' && $telefonoAlumno['cod_area'] == '') {
                        $_POST['tel_empresa' . $t] = $telefonoAlumno['empresa'];
                        $_POST['tel_tipo_telefono' . $t] = $telefonoAlumno['tipo_telefono'];
                        $_POST['tel_cod_area' . $t] = $telefonoAlumno['cod_area'];
                        $_POST['tel_numero' . $t] = $telefonoAlumno['numero'];
                        $_POST['pais' . $t] = $telefonoAlumno['pais'];
                        isset($telefonoAlumno['default']) ? $telDefault.='d' : $telDefault;
                        $posicion = $t;
                        $posicion++;
                        if($pais == 2 && $telefonoAlumno['tipo_telefono'] == 'celular')
                        {
                            $this->form_validation->set_rules('tel_empresa' . $t, lang('tel_empresa') . ' ' . $posicion . ' ' . lang('alumno'), 'required');
                        }
                        $this->form_validation->set_rules('tel_tipo_telefono' . $t, lang('tel_tipo_telefono') . ' ' . $posicion . ' ' . lang('alumno'), 'required');
                        $this->form_validation->set_rules('tel_numero' . $t, lang('tel_numero') . ' ' . $posicion . ' ' . lang('alumno'), 'required|numeric');
                        $_POST['tel_default'] = $telDefault;
                        $this->form_validation->set_rules('tel_default', lang('tel_default'), 'required|max_length[1]');
                    } else {
                        if ($pais == 6){ // bolivia no tiene separacion de numero telefonico en codigo de area y numero
                            $numero = $telefonoAlumno['numero'];
                            $telefonoAlumno['numero'] = substr($numero, 1);
                            $telefonoAlumno['cod_area'] = substr($numero, 0, 1);
                            $telefonosAlumno[$t]['numero'] = substr($numero, 1);
                            $telefonosAlumno[$t]['cod_area'] = substr($numero, 0, 1);
                        }
                        if ($telefonoAlumno['numero'] == '' || $telefonoAlumno['cod_area'] == '') {
                            $errors = lang('formato_telefono_invalido');
                        }
                    }
                }
            }
        }
        //VALIDACIONES DE RAZONES SOCIALES
        $ArrNuevoRazones = array();
        foreach ($razones as $key => $razon) {
            if ($key != '') {
                $ArrNuevoRazones[] = $razon;
            }
        }
        foreach ($ArrNuevoRazones as $key => $razon) {
            $_POST['cod_razon' . $key] = $razon['codigo'];
            $this->form_validation->set_rules('cod_razon' . $key, lang('cod_razon_social') . ' ' . $key, ''); //validar que se pueda asignar
        }
        if ($this->form_validation->run() == FALSE) {
            $errors .= validation_errors();
            $resultado = array(
                'codigo' => '0',
                'respuesta' => $errors
            );
            echo json_encode($resultado);
        } else {
            $alumno["alumno"] = array(//GENERO ARRAY DE ALUMNO PARA MANDAR AL MODELO.
                'codigo' => $this->input->post('codigo'),
                'nombre' => $this->input->post('nombre'),
                'apellido' => $this->input->post('apellido'),
                'tipo' => $this->input->post('tipoDniAlumno'),
                'documento' => $this->input->post('documento'),
                'estado_civil' => ($this->input->post('estado_civil')) ? $this->input->post('estado_civil') : '',
                'email' => $this->input->post('email_alumno'),
                'sexo' => $this->input->post('sexo'),
                'observaciones' => ($this->input->post('observaciones')) ? $this->input->post('observaciones') : '',
                'prov' => ($this->input->post('prov')) ? $this->input->post('prov') : '',
                'provincia' => $this->input->post('domiciProvincia'),
                'id_lugar_nacimiento' => ($this->input->post('localidad')) ? $this->input->post('localidad') : '',
                'calle' => $this->input->post('calle_alumno'),
                'calle_numero' => $this->input->post('calle_num_alumno'),
                'calle_complemento' => $this->input->post('complemento_alumno'),
                'barrio' => $this->input->post('barrio'),
                'codpost' => $this->input->post('codpost'),
                'comonosconocio' => ($this->input->post('comonosconocio')) ? $this->input->post('comonosconocio') : '',
                'id_localidad' => $this->input->post('domiciLocalidad'),
                'id_talle' => $this->input->post('talle'),
                'fechanaci' => formatearFecha_mysql($this->input->post('fechanaci')),
                'id_usuario_creador' => $this->session->userdata('codigo_usuario'),
                "baja" => 'habilitada',
                'id_actor' => 1
            );
            $alumno['telefonos'] = array();
            if ($telefonosAlumno != '') {
                foreach ($telefonosAlumno as $key => $valor) {//variable telefonos definida al comienzo
                    $alumno['telefonos'][] = array(//GENERO ARRAY DE TELEFONOS DEL ALUMNO PARA MANDAR AL MODELO.
                        'codigo' => $valor['codigo'],
                        'baja' => $valor['baja'],
                        'empresa' => $valor['empresa'],
                        'tipo_telefono' => $valor['tipo_telefono'],
                        'cod_area' => $valor['cod_area'],
                        'numero' => $valor['numero'],
                        'default' => isset($valor['default']) ? $valor['default'] : 0,
                        'pais' => $valor['pais']
                    );
                }
            }
            $alumno['responsables'] = $responsables;

            $alumno['razonsocial'] = array();
            if ($this->input->post('razones') != '') {
                foreach ($this->input->post('razones') as $key => $valor) {
                    if (isset($valor['codigo'])){
                        $alumno['razonsocial'][$key] = array(//GENERO ARRAY DE RAZONES SOCIALES DEL ALUMNO PARA MANDAR AL MODELO.
                            'codigo' => $valor['codigo'],
                            'default' => isset($valor['default']) ? $valor['default'] : 0,
                            'default_facturacion' => isset($valor['default_facturacion']) ? $valor['default_facturacion'] : 0
                        );
                    }
                }
            }

            $alumno['alumno']['cod_aspirante'] = $this->input->post('codigoAspirante');
            $alumno['responsable_relacion'] = $this->input->post('responsable_relacion');
            $alumno['imagen'] = $this->input->post("imagen");
            $resultado = $this->Model_alumnos->guardarAlunoGeneral($alumno);

            echo json_encode($resultado);
        }
    }

    /**
     * Guarda solo datos de alumno en modificacion
     * @access public
     * @return json de respuesta
     */
    public function frm_baja() {
        $cod_alumno = $this->input->post('cod_alumno');
        $matriculas = $this->Model_alumnos->getMatriculasPeriodosInhabilitar($cod_alumno);
        $respuesta = '';
        if (count($matriculas) > 0) {
            $respuesta['respuesta'] = lang('alumnos_con_matriculas_periodos');
        } else {
            $respuesta['respuesta'] = lang('alumnos_sin_matriculas');
        }
        echo json_encode($respuesta);
    }

    public function cambiarEstado() {
        $this->load->library('form_validation');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $codigo_alumno = $this->input->post('codigo_alumno');
        $this->form_validation->set_rules('codigo_alumno', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $desactivarAlu = $this->Model_alumnos->cambiarEstado($codigo_alumno, $cod_usuario);
            echo json_encode($desactivarAlu);
        }
    }

    public function reporte_como_nos_conocio($idFilial, $fechaAlumnosDesde, $fechaAspirantesDesde) {
        $fechaAlumnosDesde = $fechaAlumnosDesde == 0 ? null : $fechaAlumnosDesde;
        $fechaAspirantesDesde = $fechaAspirantesDesde == 0 ? null : $fechaAspirantesDesde;
        $configComoNosConocio = array("idioma" => get_idioma());
        $this->load->model("Model_Como_nos_conocio", "", false, $configComoNosConocio);
        $arrResp = $this->Model_Como_nos_conocio->getReporteComoNosConocio($idFilial, $fechaAlumnosDesde, $fechaAspirantesDesde);
        echo json_encode($arrResp);
    }

    public function reporte_alumnos($idFilial, $codigoDesde) {
        $arrResp = $this->Model_alumnos->getReporteAlumnos($idFilial, $codigoDesde);
        echo json_encode($arrResp);
    }

    public function getListadoCentroReportes() {
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $searchField = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $arrResp = $this->Model_alumnos->getListadoCentroReportes($idFilial, $arrLimit, $arrSort, $search, $searchField, $fechaDesde, $fechaHasta);
        echo json_encode($arrResp);
    }

    public function getResumenCuenta() {
        $idFilial = $_POST['id_filial'];
        $codAlumno = $_POST['cod_alumno'];
        $arrResp = $this->Model_alumnos->getResumenCuenta($idFilial, $codAlumno);
        echo json_encode($arrResp);
    }

    public function ver_facturas_alumno() {
        $cod_alumno = $this->input->post('cod_alumno');
        $arrFacturasAlumno = $this->Model_alumnos->ver_facturas_alumno($cod_alumno);
        $data['facturasAlumno'] = $arrFacturasAlumno;
        $data['nombreFormateado'] = $this->Model_alumnos->getNombreAlumno($cod_alumno);
        $this->load->view('alumnos/ver_facturas', $data);
    }

    public function getAlumnosMayorQue() {
        $cod_alumno = $this->input->post('cod_alumno');
        $alumnos = $this->Model_alumnos->getAlumnosMayorQue($cod_alumno);
        echo json_encode($alumnos);
    }

    public function login() {
        $userName = trim($_POST['userName']);
        $password = trim($_POST['password']);
        $alumno = $this->Model_alumnos->login_alumno($userName, $password);
        echo json_encode($alumno);
    }

    public function api_cursosAndroidUltimaActualizacion() {
        $respuesta = array(
            'success' => 1,
            'lastUpdateDateTime' => '2015/12/16 14:26:30'
        );

        echo json_encode($respuesta);
    }

    public function api_getCursosAndroidAlumno() {
            sleep(3);
            $courses = array(
                array(
                    'id' => 1,
                    'name' => 'Express Chef',
                    'color' => '3FC380',
                    'short_description' => '"Express Chef" escape from daily routine elaborating recipes in a minute. This course reveals the secrets of fast, well presented and tasty dishes. With a huge variety of cold and warm, sweets and salty plates. Make fast, simple and super tasty recipes! ',
                    'have_access' => false,
                    'units' => array(
                        array(
                            'id' => 1,
                            'name' => 'Unit 1',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 2,
                            'name' => 'Unit 2',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 3,
                            'name' => 'Unit 3',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        )
                    )
                ),
                array(
                    'id' => 2,
                    'name' => 'Little Chef',
                    'color' => 'F1C40F',
                    'short_description' => 'Little chefs teaches in a practical and entertaining way the secrets and attributes of the good cuisine, ingredients recognition and basic values as hygiene and utensil’s care. An excellent opportunity for children to learn the art of cooking.',
                    'have_access' => true,
                    'units' => array(
                        array(
                            'id' => 4,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => true
                        ),
                        array(
                            'id' => 5,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => true
                        ),
                        array(
                            'id' => 6,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => true
                        )
                    )
                ),
                array(
                    'id' => 3,
                    'name' => 'Bartender',
                    'color' => '3E91FF',
                    'short_description' => '"Bartender" teaches about history, origins and elaboration of several drinks as well as the techniques behind the bar so you can develop yourself professionally in an international level. Set your imagination free and combine different beberages to make the best cocktails.',
                    'have_access' => false,
                    'units' => array(
                        array(
                            'id' => 7,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 8,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 9,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        )
                    )
                ),
                array(
                    'id' => 4,
                    'name' => 'Bakery & Pastry',
                    'color' => 'FF8CBE',
                    'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                    'have_access' => true,
                    'units' => array(
                        array(
                            'id' => 10,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 11,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 12,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        )
                    )
                ),
                array(
                    'id' => 5,
                    'name' => 'Sushi & Japanese Cuisine',
                    'color' => 'FF7111',
                    'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                    'have_access' => true,
                    'units' => array(
                        array(
                            'id' => 13,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 14,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 15,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        )
                    )
                ),
                array(
                    'id' => 6,
                    'name' => 'Pastry for events',
                    'color' => '9B59B6',
                    'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                    'have_access' => false,
                    'units' => array(
                        array(
                            'id' => 16,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 17,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        ),
                        array(
                            'id' => 18,
                            'name' => 'Unit name',
                            'short_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sollicitudin felis sed sem imperdiet, ut venenatis massa tempus.',
                            'have_access' => false
                        )
                    )
                )
            );

            $respuesta = array(
                'success' => 1,
                'courses' => $courses,
                'sync_config' => array(
                    'pdfs_download_url' => 'https://repo.igacloud.net/',
                    'server_now_timestamp' => time()
                )
            );

            echo json_encode($respuesta);
        }

    public function getRazonesSociales() {
        $cod_alumno = $this->input->post('codigo');
        $arrRazones = $this->Model_alumnos->getRazonesSocialesAsignar($cod_alumno);
        echo json_encode($arrRazones);
    }

    public function api_getExamenes() {
        $filial = $_POST['filial'];
        $cod_alumno = $_POST['cod_alumno'];
        $configexamen = array("codigo_filial" => $filial);
        $this->load->model("Model_examenes", "", false, $configexamen);
        $examenes = $this->Model_examenes->getExamenesAlumno($cod_alumno, $filial);
        echo json_encode($examenes);
    }

    public function api_getResultadosInscripcion() {
        $filial = $_POST['filial'];
        $cod_inscripcion = $_POST['cod_inscripcion'];
        $configexamen = array("codigo_filial" => $filial);
        $this->load->model("Model_examenes", "", false, $configexamen);
        $examenes = $this->Model_examenes->getResultadosInscripcion($cod_inscripcion);
        header('Content-Type: application/json');
        echo json_encode($examenes);
    }

    public function api_getProximasMesasExamenesDashboarCampus() {
        $cod_filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $proxMesasExamenes = $this->Model_alumnos->getProximasMesasExamenesDashboarCampus($cod_filial, $cod_alumno);
        echo json_encode($proxMesasExamenes);
    }

    public function api_getUltimasNotasCargadasDashboardCampus() {
        $cod_filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $ultimasNotasCargadas = $this->Model_alumnos->getUltimasNotasCargadasDashboardCampus($cod_filial, $cod_alumno);
        echo json_encode($ultimasNotasCargadas);
    }

    public function api_getProximasClasesDashboardCampus() {
        $cod_filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $proximasClases = $this->Model_alumnos->getProximasClasesDashboardCampus($cod_filial, $cod_alumno);
        echo json_encode($proximasClases);
    }

    public function api_getProximosVencimientosDashboardCampus() {
        $cod_filial = $_POST['filial'];
        $cod_alumno = $_POST['codigo'];
        $idioma = $_POST['idioma'];
        $this->lang->load($idioma, $idioma);
        $habilitado = $this->input->post("habilitado") ? $this->input->post("habilitado") : null;
        $getProximosVencimientos = $this->Model_alumnos->getProximosVencimientosDashboardCampus($cod_filial, $cod_alumno, $habilitado, $idioma);
        echo json_encode($getProximosVencimientos);
    }

    public function api_getValidarDatosAlumnoCampus() {
        $this->load->library('form_validation');
        $codfilial = $this->input->post('filial');
        $tipo = $this->input->post('tipo_identificacion');
        $conexion = $this->load->database($codfilial, true);
        $filial = new Vfiliales($conexion, $codfilial);
        $this->lang->load($filial->idioma, $filial->idioma);
        $this->form_validation->set_rules('identificacion', lang('documento_alumno'), 'required|max_length[50]|validarDocumentoIdentidad[' . $tipo . ']');
        $this->form_validation->set_rules('pass', lang('password'), 'required|validarPassword');
        $this->form_validation->set_rules('email', lang('email'), 'required|validMail');
        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            echo json_encode(array('codigo' => 0, 'respuesta' => $errors));
        } else {
            $arrDatos['cod_filial'] = $codfilial;
            $arrDatos['tipo_id'] = $tipo;
            $arrDatos['identificacion'] = $this->input->post('identificacion');
            $arrDatos['fecha_nacimiento'] = $this->input->post('fecha_nacimiento');
            $arrDatos['email'] = $this->input->post('email');
            $arrValidar = $this->Model_alumnos->validarDatosAlumnoLoginCampus($arrDatos);
            echo json_encode($arrValidar);
        }
    }

    public function api_registrarAlumnoCampus() {
        $this->load->library('form_validation');
        $codfilial = $this->input->post('filial');
        $tipo = $this->input->post('tipo_identificacion');
        $code = $this->input->post('code-reg');
        if ($codfilial != '') {
            $conexion = $this->load->database($codfilial, true);
            $filial = new Vfiliales($conexion, $codfilial);
            $idioma = $filial->idioma;
        } else {
            $filial = $this->Model_alumnos->getFilialLoginEnvioCampus($code);
            $idioma = $filial['idioma'];
        }
        $this->lang->load($idioma, $idioma);
        $this->form_validation->set_rules('identificacion', lang('documento_alumno'), 'required|max_length[50]|validarDocumentoIdentidad[' . $tipo . ']');
        $this->form_validation->set_rules('password', lang('password'), 'required|validarPassword');
        if ($code == '') {
            $this->form_validation->set_rules('email', lang('email'), 'required|validMail');
        }
        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            echo json_encode(array('codigo' => 0, 'respuesta' => $errors));
        } else {
            $arrDatos['cod_filial'] = $codfilial;
            $arrDatos['email'] = $this->input->post('email');
            $arrDatos['pass'] = $this->input->post('password');
            $arrDatos['tipo_id'] = $tipo;
            $arrDatos['identificacion'] = $this->input->post('identificacion');
            $arrDatos['fecha_nacimiento'] = $this->input->post('fecha_nacimiento');
            $arrDatos['code'] = $code;
            $arrDatos['lista'] = json_decode($this->input->post('lista'), true);
            $arrDatos['asistencia'] = $this->input->post('asistencia');
            $arrDatos['cursos'] = $this->input->post('cursos');
            $arrDatos['direcciones'] = $this->input->post('direcciones');
            $respuesta = $this->Model_alumnos->registrarAlumnoCampus($arrDatos);
            echo json_encode($respuesta);
        }
    }

    public function api_getTiposDocumentos() {
        $filial = $_POST['filial'];
        $this->load->model("Model_filiales", "", false, $filial);
        $arrfilial = $this->Model_filiales->getFilial();
        $this->load->model("Model_paises", "", false, $arrfilial['pais']);
        $tiposDocumentos = $this->Model_paises->getDocumentosPersonasFisicas();
        echo json_encode($tiposDocumentos);
    }

    public function api_restablecePassCampus() {
        $user = $this->input->post('email');
        $arrRespuesta = $this->Model_alumnos->restablecePassCampus($user);
        echo json_encode($arrRespuesta);
    }

    public function api_modificoPassCampus() {
        $this->load->library('form_validation');
        $code = $this->input->post('code-pass');
        $pass = $this->input->post('pass');
        $lang = $this->input->post('lang');
        $this->lang->load($lang, $lang);
        $this->form_validation->set_rules('pass', lang('password'), 'required|validarPassword');
        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            echo json_encode(array('codigo' => 0, 'respuesta' => $errors));
        } else {
            $arrRespuesta = $this->Model_alumnos->modificoPassCampus($code, $pass);
            echo json_encode($arrRespuesta);
        }
    }

    public function api_unificarEmailAlumnoCampus() {
        $cod_login = $this->input->post('cod_login');
        $email = $this->input->post('email');
        $arrRespuesta = $this->Model_alumnos->unificarEmailCampus($cod_login, $email);
        echo json_encode($arrRespuesta);
    }

    public function api_getDatosLoginEnvioCampus() {
        $this->load->library('form_validation');
        $code = $this->input->post('code-reg');
        $lang = $this->input->post('lang');
        $this->lang->load($lang, $lang);
        $errors = lang('codigo_invalido');
        $this->form_validation->set_rules('code-reg', lang('codigo'), 'required|max_length[32]|min_length[32]');
        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            echo json_encode(array('codigo' => 0, 'respuesta' => $errors));
        } else {
            if ($this->Model_alumnos->ExisteCodeReg($code)) {
                $arrRespuesta['filial'] = $this->Model_alumnos->getFilialLoginEnvioCampus($code);
                $this->load->model("Model_paises", "", false, $arrRespuesta['filial']['pais']);
                $arrRespuesta['tiposDocumentos'] = $this->Model_paises->getDocumentosPersonasFisicas();
                echo json_encode($arrRespuesta);
            } else {
                echo json_encode(array('codigo' => 0, 'respuesta' => $errors));
            }
        }
    }

    public function api_confirmarLoginCampus() {
        $code = $this->input->post('code-conf');
        $respuesta = $this->Model_alumnos->confirmarLoginCampus(trim($code));
        echo json_encode($respuesta);
    }

    public function api_restablecePassAnteriorCampus() {
        $code = $this->input->post('code-historico');
        $arrRespuesta = $this->Model_alumnos->restablecePassAnteriorCampus($code);
        echo json_encode($arrRespuesta);
    }

    public function api_getAlumno() {
        $filial = $this->input->post('filial');
        $cod_alumno = $this->input->post('codigo');
        $configAlumnos = array("codigo_filial" => $filial);
        $this->load->model("Model_configuraciones", "", false, $configAlumnos);
        $separador = $this->Model_configuraciones->getValorConfiguracion(null, $key = 'NombreSeparador', null);
        $apellidoPrimero = $this->Model_configuraciones->getValorConfiguracion(null, $key = 'NombreFormato', null);
        $alumno = $this->Model_alumnos->getAlumnoCampus($cod_alumno, $filial, $separador, $apellidoPrimero);
        echo json_encode($alumno);
    }

    public function api_modificarPassPerfil() {
        $this->load->library('form_validation');
        $pass = $this->input->post('pass');
        $lang = $this->input->post('lang');
        $userName = $this->input->post('userName');
        $cod_filial = $this->input->post('filial');
        $this->lang->load($lang, $lang);
        $this->form_validation->set_rules('pass', lang('password'), 'required|validarPassword');
        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            echo json_encode(array('codigo' => 0, 'respuesta' => $errors));
        } else {
            $arrRespuesta = $this->Model_alumnos->modificoPassCampusDesdePerfil($userName, $pass, $cod_filial);
            echo json_encode($arrRespuesta);
        }
    }

    public function api_getDetalleExamen() {
        $cod_examen = $_POST['cod_examen'];
        $cod_filial = $_POST['cod_filial'];
        $cod_alumno = $_POST['cod_alumno'];
        $cod_materia = $_POST['cod_materia'];
        $cod_inscripcion = $_POST['cod_inscripcion'];
        $detalleExamen = $this->Model_alumnos->getDetalleExamenAlumnno($cod_filial, $cod_examen, $cod_alumno, $cod_materia, $cod_inscripcion);
        echo json_encode($detalleExamen);
    }

    public function api_guardarInscripcion() {
        $cod_examen = $_POST['cod_examen'];
        $cod_filial = $_POST['cod_filial'];
        $cod_estado_academico = $_POST['cod_estado_academico'];
        $noValidar = $this->validarInscripcionExamen($cod_filial, $cod_examen, $cod_estado_academico);
        $filial['codigo_filial'] = $cod_filial;
        $this->load->model("Model_examenes", "", false, $filial);
        $respuesta = '';
        if ($noValidar) {
            $inscribir['cod_examen'] = $cod_examen;
            $inscribir['inscriptos'][] = $cod_estado_academico;
            $respuesta = $this->Model_examenes->guardarInscriptos($inscribir, false, true);
        } else {
            $respuesta = array(
                "codigo" => 0,
                "msgError" => 'Ya está inscripto a este examen'
            );
        }
        echo json_encode($respuesta);
    }

    private function validarInscripcionExamen($cod_filial, $cod_examen, $cod_estado_academico) {
        $validarInscripcion = $this->Model_alumnos->validarInscripcionExamen($cod_filial, $cod_examen, $cod_estado_academico);
        return $validarInscripcion;
    }

    public function api_getMailsAlumno() {
        $filial = $this->input->post('filial');
        $cod_alumno = $this->input->post('codigo');
        $respuesta = $this->Model_alumnos->getMailsAlumnoCampus($cod_alumno, $filial);
        echo json_encode($respuesta);
    }

    public function correccionTelefonos() {
        
    }

    public function reenviar_mail_campus_alumno() {
        $cod_alumno = $this->input->post('cod_alumno');
        $claves = array('validacion_ok','password','bien_se_envio_mail_cuenta');
        $data['langFrm'] = getLang($claves);
        $data['detalle_mails_enviados'] = $this->Model_alumnos->getDetallesAlertasEmailCampus($cod_alumno);
        $data['objAlumno'] = $this->Model_alumnos->getAlumno($cod_alumno);
        $this->load->view('alumnos/reenviar_mail_alumno_campus', $data);
    }

    public function reenviar_email_campus() {

        $cod_alumno = $this->input->post('cod_alumno');
        $filial = $this->session->userdata('filial');
        $arrResp = $this->Model_alumnos->resetear_password_campus_nuevo($cod_alumno, $filial['codigo']);
        
        echo json_encode($arrResp);
    }

    /* Inicio IM 22 - Regenerar contraseña*/
    public function regenerar_password() {

        $cod_alumno = $this->input->post('cod_alumno');
        $filial = $this->session->userdata('filial');
        $arrResp = $this->Model_alumnos->regenerar_password($cod_alumno, $filial['codigo']);

        echo json_encode($arrResp);
    }
    /* Fin    IM 22 - Regenerar contraseña*/

    public function api_getCursosDisponiblesParaAlumnoDeFilial(){
        $id_filial = $this->input->post('codigo_filial');
        $cod_alumno = $this->input->post('codigo_alumno');
        
        $arrResp = $this->Model_alumnos->getCursosDisponibles($id_filial, $cod_alumno);
        echo json_encode($arrResp);
    }
    
    public function api_getMateriasDeCursoParaAlumnoDeFilial(){
        $id_filial = $this->input->post('codigo_filial');
        $codigo_curso = $this->input->post('codigo_curso');
        $cod_alumno = $this->input->post('codigo_alumno');        
        $arrResp = $this->Model_alumnos->getMateriasDisponiblesDeCurso($id_filial, $codigo_curso, $cod_alumno);
        echo json_encode($arrResp);
    }

    public function api_getClasesDeMateriaParaAlumnoDeFilial() {
        $id_filial = $this->input->post('codigo_filial');
        $codigo_materia = $this->input->post('codigo_materia');
        $cod_alumno = $this->input->post('codigo_alumno');        
        $arrResp = $this->Model_alumnos->getClasesDisponiblesDeMateria($id_filial, $codigo_materia, $cod_alumno);
        echo json_encode($arrResp);
    }
    
    public function api_getMaterialesDidacticosDeClaseParaAlumnoDeFilial(){
        $codigo_filial = $this->input->post('codigo_filial');
        $id_clase = $this->input->post('id_clase');
        $codigo_alumno = $this->input->post('codigo_alumno');        
        $arrResp = $this->Model_alumnos->getMaterialesDidacticosDeClaseParaAlumnoDeFilial($id_clase, $codigo_alumno, $codigo_filial);
        echo json_encode($arrResp);
    }
    
    public function api_getProximosVideos(){
        $codigo_filial = $this->input->post('codigo_filial');
        $id_clase = $this->input->post('id_clase');
        $codigo_alumno = $this->input->post('codigo_alumno');        
        $arrResp = $this->Model_alumnos->getProximosVideos($id_clase, $codigo_alumno, $codigo_filial);
        echo json_encode($arrResp);
    }
	
    public function api_getVideosDeMateriaParaAlumnoDeFilial() {
        $id_materia = $this->input->post('id_materia');
        $codigo_filial = $this->input->post('codigo_filial');
        $codigo_alumno = $this->input->post('codigo_alumno');		
        $arrResp = $this->Model_alumnos->getVideosDeMateriaParaAlumnoDeFilial($id_materia, $codigo_alumno, $codigo_filial); //getProximosVideos($id_clase, $codigo_alumno, $codigo_filial);
        echo json_encode($arrResp);
    }
    
    public function api_getVideosAnteriores(){
        $codigo_filial = $this->input->post('codigo_filial');
        $id_clase = $this->input->post('id_clase');
        $codigo_alumno = $this->input->post('codigo_alumno');        
        $arrResp = $this->Model_alumnos->getVideosAnteriores($id_clase, $codigo_alumno, $codigo_filial);
        echo json_encode($arrResp);
    }
    
    public function api_alumnoTieneAccesoPlataformaElearning() {
        $cod_alumno = $this->input->post('codigo_alumno');
        $id_filial = $this->input->post('id_filial');        
        $arrResp = $this->Model_alumnos->tieneAccesoPlataformaElearning($id_filial, $cod_alumno);        
        echo json_encode($arrResp);
    }
    
    public function api_getProximoVideoEnVivo() {
        $codigo_filial = $this->input->post('codigo_filial');
        $codigo_alumno = $this->input->post('codigo_alumno');        
        $arrResp = $this->Model_alumnos->getProximoVideoEnVivo($codigo_alumno, $codigo_filial);
        echo json_encode($arrResp);
    }
        
    public function get_imagen(){
        $conexion = $this->load->database("20", true);
        $myAlumnos = new Valumnos($conexion, 6433);
        $imagen_alumno = $myAlumnos->getImagen();
        ?><img src="<?php echo str_replace(' ', '+', $imagen_alumno); ?>" width="320" height="240" id="imagen_preview"><?php
    }
	
	/**
     * Guarda la baja de una inscripcion al examen.
     * @access public
     * @return json de respuesta.
     */
    public function bajaExamen() {
        $this->load->library('form_validation');        
        $cod_inscripcion = $this->input->post('cod_inscripcion');
        $cod_filial = $this->input->post('cod_filial');		
        $this->load->model("Model_examenes", "", false, array("codigo_filial" => $cod_filial));		
        $respuesta = '';
        $this->form_validation->set_rules('cod_inscripcion', lang('codigo'), 'numeric|validarBajaInscripcionExamen['.$cod_filial.']');
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
    
    public function get_materias_alumno(){
        if ($this->input->post("codigo_filial") && $this->input->post("codigo_alumno")){
            $cod_filial = $this->input->post("codigo_filial");
            $cod_alumno = $this->input->post("codigo_alumno");
            $estado = $this->input->post("estado") ? $this->input->post("estado") : null;
            $conexion = $this->load->database($cod_filial, true);
            $myAlumno = new Valumnos($conexion, $cod_alumno);
            $arrMaterias = $myAlumno->get_materias($estado);
            $resp = array();
            foreach ($arrMaterias as $materia){
                $resp['materias'][] = $materia['codmateria'];
            }
            echo json_encode($resp);
        }
    }
    public function get_materias_plan_academico(){
        if ($this->input->post("codigo_filial") && $this->input->post("codigo_alumno")){
            $arrResp = array();
            $codigo_filial = $this->input->post("codigo_filial");
            $codigo_alumno = $this->input->post("codigo_alumno");
            $estado = $this->input->post("estado") ? $this->input->post("estado") : null;
            $idioma = $this->input->post("idioma") ? $this->input->post("idioma") : null;
            $arrResp['matriculas'] = $this->Model_alumnos->get_materias_plan_academico($codigo_filial, $codigo_alumno, $idioma, $estado);
            echo json_encode($arrResp);
        }
    }

    //EJECUTAR SOLO PARA EL LANZAMIENTO
    function alta_masiva_nuevo_campus()
    {
        $this->Model_alumnos->alta_masiva_nuevo_campus();
    }

    function api_getDatosAcademicosAlumno()
    {
        $codigo_filial = isset($_POST['filial'])?$_POST['filial']:die("Debe indicar numero de filial");
        $codigo_alumno = isset($_POST['codigo'])?$_POST['codigo']:die("Debe indicar numero de filial");
        $conexion = $this->load->database($codigo_filial, true);
        $datos = $this->Model_alumnos->getDatosAcademicosAlumno($conexion, $codigo_alumno);
        echo json_encode($datos);
    }

    function api_getEstadoAcademicoMateria(){
        $codigo_filial = isset($_POST['filial'])?$_POST['filial']:die("Debe indicar numero de filial");
        $estado_academico = isset($_POST['cod_estado_academico'])?$_POST['cod_estado_academico']:die("Debe indicar estado academico");
        $conexion = $this->load->database($codigo_filial, true);
        $datos = $this->Model_alumnos->api_getEstadoAcademicoMateria($conexion, $estado_academico);
        echo json_encode($datos);
    }
}
