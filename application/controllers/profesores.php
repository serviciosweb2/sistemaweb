<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profesores extends CI_Controller {
    
    private $seccion;
    
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $configProfesores = array("codigo_filial" => $filial['codigo']);
        $this->load->model("Model_profesores", "", false, $configProfesores);
    }
    
    /**
     * retorna vista de profesores main panel
     * @access public
     * @return vista de main ponel (Profesores)
     */
    public function index() {
        $data['page'] = 'profesores/vista_profesores';
        $claves = array("BIEN", "codigo", "estado_profesor_cabecera", "INHABILITADO", "HABILITADO", "INHABILITAR",
            "HABILITAR", "PROFESOR_HABILITADO_CORRECTAMENTE", "PROFESOR_INHABILITADO_CORRECTAMENTE");
        $data['lang'] = getLang($claves);
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('profesores');
        $data['columns'] = $this->getColumns();
        $data['seccion']= $this->seccion;
        $this->load->view('container', $data);
    }
    
    private function crearColumnas(){
        $this->load->helper('alumnos');
        $nombreApellido = formatearNombreColumnaAlumno();
            $filial = $this->session->userdata('filial');
            $pais = $filial['pais'];
        if($pais == 2)
        {
            $columnas = array(
                array("nombre" => lang('codigo'), "campo" => 'codigo'),
                array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
                array("nombre" => lang('email'), "campo" => 'mail'),
                array("nombre" => lang('nro_documento'), "campo" => 'nrodocumento'),
                array("nombre" => lang('telefono'), "campo" => 'tel_numero',"sort" => false),
                // array("nombre" => lang('fecha_alta'), "campo" => 'fechaalta'),
                array("nombre"=>  lang('baja_profesor'),"campo"=>'baja',"sort" => false),
                array("nombre" => lang('estado_profesor_cabecera'), "campo"=>'estado','bVisible'=>false));
        }
        else
        {
            $columnas = array(
                array("nombre" => lang('codigo'), "campo" => 'codigo'),
                array("nombre" => $nombreApellido, "campo" => 'nombre_apellido'),
                array("nombre" => lang('email'), "campo" => 'mail'),
                array("nombre" => lang('nro_documento'), "campo" => 'nrodocumento'),
                //array("nombre" => lang('telefono'), "campo" => 'tel_numero',"sort" => false),
                array("nombre" => lang('fecha_alta'), "campo" => 'fechaalta'),
                array("nombre"=>  lang('baja_profesor'),"campo"=>'baja',"sort" => false),
                array("nombre" => lang('estado_profesor_cabecera'), "campo"=>'estado','bVisible'=>false));
        }
        return $columnas;
    }
    
    public function getColumns(){
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    /**
     * carga la vista del formulario profesores
     * @access public
     * @return vista form profesores
     */
    public function frm_profesores() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $codigo = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            //CARGO MODELOS
            $this->load->model("Model_paises", "", false, $filial["pais"]);
            $this->load->model("Model_telefonos","",false,$filial["pais"]);
            //CARGO ARRAY FRM
            $tipoTelefonos = $this->Model_telefonos->getTelefonosTipos();
            $empresas_tel = $this->Model_paises->getEmpresasTelefonicas();
            $prov = $this->Model_paises->getprovincias();
            $tipo_dni = $this->Model_paises->getDocumentosPersonasFisicas();
            $condiciones = $this->Model_paises->getCondicionesSociales();
            $razonSociales = $this->Model_profesores->getRazonesSociales($codigo);
            //CARGO EL ARRAY DE TELEFONOS.
            $telefonos = $this->Model_profesores->getTelefonos($codigo);
            //CARGO EL OBJETO PROFESOR
            $profesor = $this->Model_profesores->getProfesor($codigo);
            if ($codigo != -1) {
                $this->load->model("Model_localidades", "", false, $profesor->cod_localidad);
                $localidadpro = $this->Model_localidades->getLocalidad();
                $data['provincia_profesor'] = $localidadpro->provincia_id;
                $this->load->model("Model_provincias", "", false, $data['provincia_profesor']);
                $localidades = $this->Model_provincias->getLocalidades();
                $data['localidades'] = $localidades;
            }
            $claves = array("error_fecha", "error_requerido", "error_max_100", "error_numeros", "error_email", "error_max_50", "error_fecha","eliminar","nuevo_tel","nueva_razon","sinTelefono","no_se_puede_eliminar_un_telefono_default");
            $data['langFrm'] = getLang($claves);
            $data['codigo']=$codigo;
            $data['empresas_tel'] = $empresas_tel;
            $data['tipoTelefonos'] = $tipoTelefonos;
            $data['tipo_dni'] = $tipo_dni;
            $data['provincias'] = $prov;
            $data['profesor'] = $profesor;
            $data['telefonos'] = $telefonos;
            $data['condiciones']=$condiciones;
            $data['razonSociales']= $razonSociales;
            $this->load->view('profesores/frm_profesor', $data);
        }
    }

    /**
     * Retorna json de localides en base al post de provincia.
     * @access public
     * @return json de localidades
     */
    public function getlocalidades() {
        $nombreProv = $this->input->post('idprovincia');
        $this->load->model("Model_provincias", "", false, $nombreProv);
        $localidades = $this->Model_provincias->getLocalidades();
        echo json_encode($localidades);
    }

    /**
     * retorna lista de profesores para mostrar en index de main panel
     * @access public
     * @return json de listado de profesores
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
        $valores = $this->Model_profesores->listarProfesoresdataTable($arrFiltros,$separador);
        echo json_encode($valores);
    }

    /**
     * Guarda todos los datos del profesor
     * @access public
     * @return json de respuesta
     */
    public function guardar() {
        $this->load->helper('formatearfecha');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $this->load->library('form_validation');
        $razonesSociales=$this->input->post('razonesSociales');
        $telefonosProfesor=$this->input->post('telefonos');
        $tipoDocu = $this->input->post('tipoProfesor');
        $this->form_validation->set_rules('nombre', lang('nombre_profesor'), 'required|max_length[50]|validarNombreApellido[' . 'nombreProfesorInvalido' . ']');
        $this->form_validation->set_rules('apellido', lang('apellido_profesor'), 'required|max_length[255]|validarNombreApellido[' . 'apellidoProfesorInvalido' . ']');
        $this->form_validation->set_rules('fechanac',lang('fechanac_profesor'),'required|validarFechaFormato');
        $this->form_validation->set_rules('tipoProfesor', lang('tipodoc_profesor'), 'required');
        $this->form_validation->set_rules('observaciones', lang('observaciones_profesor'), 'max_length[255]');
        $this->form_validation->set_rules('calle', lang('calle_profesor'), 'required|max_length[50]');
        $this->form_validation->set_rules('calle_numero', lang('numcalle_profesor'), 'required|max_length[50]|integer');
        $this->form_validation->set_rules('complemento', lang('complemento_profesor'), 'max_length[255]');
        $this->form_validation->set_rules('documento', lang('nrodocumento_profesor'), 'required|validarDocumentoIdentidad[' . $tipoDocu . ']');
        $this->form_validation->set_rules('cod_localidad', lang('cod_localidad_profesor'), 'required');
        $this->form_validation->set_rules('codigopostal', lang('codpostal_profesor'), 'required|max_length[50]');
        $this->form_validation->set_rules('mail', lang('mail_profesor'), 'required|valid_email');
        $this->form_validation->set_rules('telefonos',lang('telefonosProfesor'),'required');
        
        //VALIDACIONES DE LOS TELEFONOS DEL PROFESOR
        if($telefonosProfesor['telefonos'] != ''){
            foreach($telefonosProfesor['telefonos'] as $tel=>$valor){
                $pos = $tel;
                $pos++;
                $_POST['tipo_tel'.$tel] = $valor['tipo'];
                $_POST['empresa'.$tel] = $valor['empresa'];
                $_POST['numero'.$tel] = $valor['numero'];
                $_POST['cod_area'.$tel] = $valor['cod_area'];
                $this->form_validation->set_rules('tipo_tel'.$tel, lang('tipo_telProfesor').' '.$pos.' '.lang('profesor'),'required');
                $this->form_validation->set_rules('empresa'.$tel, lang('empresaTel_Profesor').' '.$pos.' '.lang('profesor'),'required');
                $this->form_validation->set_rules('numero'.$tel, lang('numeroTel_profesor').' '.$pos.' '.lang('profesor'),'required|numeric');
                $this->form_validation->set_rules('cod_area'.$tel,lang('codArea_profesor').' '.$pos.' '.lang('profesor'),'required|numeric');
            }
            $_POST['default'] = $telefonosProfesor['default'];
            $this->form_validation->set_rules('default',lang('telDefault_profesor').' '.lang('profesor'),'required');
        }
        //VALIDACIONES DE LA RAZONES SOCIALES DEL PROFESOR
        if($razonesSociales['razonesSociales'] != ''){
            foreach($razonesSociales['razonesSociales'] as $r=>$razonSocial){
                $posicion = $r;
                $posicion++;
                $_POST['razSocial'.$r] = $razonSocial['razon_social'];
                $_POST['docRazon'.$r]= $razonSocial['documento'];
                $_POST['condRazon'.$r]= $razonSocial['condicion'];
                $_POST['tipoDocRazon'.$r]=$razonSocial['tipo_documento'];
                $this->form_validation->set_rules('razSocial'.$r,lang('razonProf').' '.$posicion.' '.lang('profesor'),'required');
                $this->form_validation->set_rules('docRazon'.$r,lang('docRazProf').' '.$posicion.' '.lang('profesor'),'required');
                $this->form_validation->set_rules('condRazon'.$r,lang('condRazProf').' '.$posicion.' '.lang('profesor'),'required');
                $this->form_validation->set_rules('tipoDocRazon'.$r,lang('tipoDocRazProf').' '.$posicion.' '.  lang('profesor'),'required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['profesor']['codigo'] = $this->input->post('codigo');
            $data_post['profesor']['nombre'] = $this->input->post('nombre');
            $data_post['profesor']['fechanac'] = formatearFecha_mysql($this->input->post('fechanac'));
            $data_post['profesor']['tipodocumento'] = $this->input->post('tipoProfesor');
            if($this->input->post('fechaalta') != ''){
                $data_post['profesor']['fechaalta'] = $this->input->post('fechaalta');
            } else {
                $data_post['profesor']['fechaalta'] = date("Y-m-d H:i:s");
            }
            $data_post['profesor']['observaciones'] = $this->input->post('observaciones');
            $data_post['profesor']['nrodocumento'] = $this->input->post('documento');
            $data_post['profesor']['cod_localidad'] = $this->input->post('cod_localidad');
            $data_post['profesor']['codigopostal'] = $this->input->post('codigopostal');
            $data_post['profesor']['mail'] = $this->input->post('mail');
            $data_post['profesor']['barrio']=  $this->input->post('barrio');
            $data_post['profesor']['apellido'] = $this->input->post('apellido');
            $data_post['profesor']['calle'] = $this->input->post('calle');
            $data_post['profesor']['numero'] = $this->input->post('calle_numero');
            $data_post['profesor']['complemento'] = $this->input->post('calle_complemento');
            $data_post['profesor']['estado'] = 'habilitado';
            
            //telefonos
            $data_post['telefonos'] = array();
            if($telefonosProfesor != ''){
                foreach ($telefonosProfesor['telefonos'] as $key => $valor) {//variable telefonos definida al comienzo
                    $data_post['telefonos'][] = array(
                        'codigo' => $valor['codigo'],
                        'baja' => $valor['baja'],
                        'empresa' => $valor['empresa'],
                        'tipo_telefono' => $valor['tipo'],
                        'cod_area' => $valor['cod_area'],
                        'numero' => $valor['numero'],
                        'default'=> $telefonosProfesor['default']==$key ? 1 : 0
                    );
                }
            }
            $data_post['razonsocial'] = array();
            if($razonesSociales != ''){
                foreach ($razonesSociales['razonesSociales'] as $raz => $value) {
                    $data_post['razonsocial'][] =array(
                        'codigo'=>$value['codigo'],
                        'razon_social'=>$value['razon_social'],
                        'documento'=>$value['documento'],
                        'tipo_documentos'=> $data_post['profesor']['tipodocumento'],
                        'direccion_calle'=>$data_post['profesor']['calle'],
                        'direccion_numero'=>$data_post['profesor']['numero'],
                        'direccion_complemento'=>$data_post['profesor']['complemento'],
                        'cod_localidad'=>$data_post['profesor']['cod_localidad'],
                        'email'=>$data_post['profesor']['mail'],
                        'codigo_postal'=>$data_post['profesor']['codigopostal'],
                        'fecha_alta'=>$data_post['profesor']['fechaalta'],
                        'inicio_actividades'=>$data_post['profesor']['fechanac'],
                        'barrio'=>$data_post['profesor']['barrio'],
                        'condicion'=>$value['condicion'],
                        'baja'=>$value['baja'],
                        'tipo_documentos'=>$value['tipo_documento'],
                        'default'=>isset($razonesSociales['default']) && $razonesSociales['default']==$raz ? 1: 0,
                        "usuario_creador"=>$cod_usuario
                    );
                }
            }
            $resultado = $this->Model_profesores->guardarProfesorGeneral($data_post,$cod_usuario);
        }
        echo json_encode($resultado);
    }

    /**
     * carga la vista del formulario baja de profesores
     * @access public
     * @return vista form baja profesores
     */
    public function frm_baja() {
        $this->load->library('form_validation');
        $codigo_profesor = $this->input->post('codigo_profesor');
        $this->form_validation->set_rules('codigo_profesor',lang('codigo'),'numeric');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            echo $errors;
        } else {
            $Profesor = $this->Model_profesores->getProfesor($codigo_profesor);
            $motivos = $this->Model_profesores->getMotivosBaja();
            $claves = array("validacion_ok","PROFESOR_INHABILITADO_CORRECTAMENTE","BIEN");
            $data['langFrm'] = getLang($claves);
            $data['profesor'] = $Profesor;
            $data['motivos'] = $motivos;
            $this->load->view('profesores/frm_baja',$data);
        }
    }

    /**
     * cambia el estado de profesores
     * @access public
     * @return json de respuesta
     */
    public function cambioEstado() {
        $usuario = $this->session->userdata('codigo_usuario');
        $codigo_profesor = $this->input->post('codigo');
        $motivo = $this->input->post('motivo');
        $comentario = $this->input->post('comentario');
        $cambioestado = array('codprofesor' => $codigo_profesor,
            'motivo' => $motivo, 'comentario' => $comentario, 'cod_usuario'=>$usuario);
        $procambioestado = $this->Model_profesores->cambioEstadoProfesor($cambioestado);
        echo json_encode($procambioestado);
    }
    
    public function getReporteProfesores(){
        $idFilial = $_POST['id_filial'];
        $arrLimit = isset($_POST['limit']) ? $_POST['limit'] : null;
        $arrSort = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : null;
        $search = isset($_POST['search']) ? $_POST['search'] : null;
        $searchFields = isset($_POST['search']) && isset($_POST['search_fileds']) && is_array($_POST['search_fileds']) ? $_POST['search_fileds'] : null;
        $fechaDesde = isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : null;
        $fechaHasta = isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : null;
        $arrResp = $this->Model_profesores->getReporteProfesores($idFilial,  $arrLimit, $arrSort, $search, $searchFields, $fechaDesde, $fechaHasta);
        echo json_encode($arrResp);
    }
    
    public function get_materias_profesor(){
        if ($this->input->post("cod_profesor") && $this->input->post("cod_filial")){
            $arrResp = array();
            $cod_profesor = $this->input->post("cod_profesor");
            $cod_filial = $this->input->post("cod_filial");
            $conexion = $this->load->database($cod_filial, true);
            $myFilial = new Vfiliales($conexion, $cod_filial);
            $myProfesor = new Vprofesores($conexion, $cod_profesor);
            $cod_plan_academico = $this->input->post("cod_plan_academico") ? $this->input->post("cod_plan_academico") : null;
            $arrResp['materias'] = $myProfesor->get_materias_dadas($myFilial->idioma, $cod_plan_academico);
            echo json_encode($arrResp);
        }
    }
    
    public function get_cursos_profesor(){
        if ($this->input->post("cod_profesor") && $this->input->post("cod_filial")){
            $arrResp = array();
            $cod_profesor = $this->input->post("cod_profesor");
            $cod_filial = $this->input->post("cod_filial");
            $conexion = $this->load->database($cod_filial, true);
            if ($this->input->post("idioma")){
                $idioma = $this->input->post("idioma");
            } else {
                $myFilial = new Vfiliales($conexion, $cod_filial);
                $idioma = $myFilial->idioma;
            }            
            $myProfesor = new Vprofesores($conexion, $cod_profesor);
            $arrResp['cursos'] = $myProfesor->get_cursos_dados($idioma);
            echo json_encode($arrResp);
        }
    }
    
}