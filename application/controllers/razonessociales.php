<?php

/**
 * Control Alumnos.
 *
 * @package  SistemaIGA
 * @subpackage Razones Sociales
 * @author   Vane
 * @version  $Revision: 1.0 $
 * @access   public
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion razonessociales.
 */
class Razonessociales extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $validar_session = session_method();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');

        $config = array("codigo_filial" => $filial["codigo"]);

        $this->load->model("Model_razones_sociales", "", false, $config);
        /* CARGO EL LAG */
        $this->load->helper("datatables");
    }

    public function getCondicionesSociales() {
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial["codigo"], true);
        $myDocumentoTipo = new Vdocumentos_tipos($conexion, $this->input->post("tipo_identificador"));
        $arrResp = $myDocumentoTipo->getCondicionesSociales();
        echo json_encode($arrResp);
    }

    /**
     * retorna vista de razones sociales main panel
     * @access public
     * @return vista de main ponel (Razones Sociales)
     */
    public function index() {

        $this->lang->load(get_idioma(), get_idioma());
        $data['titulo_pagina'] = ''; //$validar_session;
        $data['page'] = 'razones_sociales/vista_razones'; // pasamos la vista a utilizar como parámetr
        $claves = array('estado_razon', 'codigo', "BIEN", "HABILITAR", "INHABILITAR", "HABILITADO", "INHABILITADO", 'ocurrio_error', 'tel_formato_invalido');
        $data['lang'] = getLang($claves);
        $data['menuJson'] = getMenuJson('razonessociales');
        $data['columns'] = $this->getColumns();
        $data['seccion'] = $this->seccion;
        $this->load->view('container', $data);
    }

    private function crearColumnas() {
        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        if($pais == 2)
        {
            $columnas = array(
                array("nombre" => lang('codigo'), "campo" => 'codigo'),
                array("nombre" => lang('razon_social'), "campo" => 'razon_social'),
                array("nombre" => lang('tipo_identificacion'), "campo" => 'documentos_tipos.nombre'),
                array("nombre" => lang('numero_identificacion'), "campo" => 'documento'),
                array("nombre" => lang('razon_condicion'), "campo" => 'nbrecondicion'),
                array("nombre" => lang('email'), "campo" => 'email'),
                //array("nombre" => lang('tel_empresa'), "campo" => 'telefono_empresa'),
                array("nombre" => lang('telefono'), "campo" => 'tel_numero'),
                array("nombre" => lang('fecha_alta'), "campo" => 'fecha_alta'),
                array("nombre" => lang('estado'), "campo" => 'baja', "sort" => FALSE),
                array("nombre" => lang('estado_razon'), "campo" => 'baja', 'bVisible' => false));
        }
        else
        {
            $columnas = array(
                array("nombre" => lang('codigo'), "campo" => 'codigo'),
                array("nombre" => lang('razon_social'), "campo" => 'razon_social'),
                array("nombre" => lang('tipo_identificacion'), "campo" => 'documentos_tipos.nombre'),
                array("nombre" => lang('numero_identificacion'), "campo" => 'documento'),
                array("nombre" => lang('razon_condicion'), "campo" => 'nbrecondicion'),
                array("nombre" => lang('email'), "campo" => 'email'),
                array("nombre" => lang('fecha_alta'), "campo" => 'fecha_alta'),
                array("nombre" => lang('estado'), "campo" => 'baja', "sort" => FALSE),
                array("nombre" => lang('estado_razon'), "campo" => 'baja', 'bVisible' => false));
        }

        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    /**
     * retorna lista de razones sociales para mostrar en index de main panel
     * @access public
     * @return json de listado de razones sociales
     */
    public function listar() {
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $valores = $this->Model_razones_sociales->listarRazonesDatatable($arrFiltros);
        echo json_encode($valores);
    }

    /**
     * carga la vista del formulario razon social
     * @access public
     * @return vista form razon social
     */
    public function frm_razones_sociales() {

        $cod_razon = $this->input->post('codigo');
        $modo = $this->input->post("modo") ? $this->input->post("modo") : '';
        $filial = $this->session->userdata('filial');
        $localidades = array();
        $arrTelefono = array();
        $provincia = '';
        $localidad = '';
        $esdefault = false;
        $this->load->library('form_validation');

        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {

            $this->load->model("Model_paises", "", false, $filial["pais"]);
            $empresas_tel = $this->Model_paises->getEmpresasTelefonicas();
            $provincias = $this->Model_paises->getprovincias();
            $tipo_identificacion = $this->Model_paises->getDocumentos();
            $condicion = $this->Model_paises->getCondicionesSociales();
            $tipo_telefono = Vtelefonos::getArray();
            $objRazon = $this->Model_razones_sociales->getRazonSocial($cod_razon);
            if ($objRazon->getCodigo() != -1) {
                $esdefault = $this->Model_razones_sociales->esRazonSocialDefault($cod_razon);
                $arrTelefono = $this->Model_razones_sociales->getTelefonoRazon($cod_razon);
                if ($objRazon->cod_localidad != null) {
                    $this->load->model("Model_localidades", "", false, $objRazon->cod_localidad);
                    $localidad = $this->Model_localidades->getLocalidad();
                    $this->load->model("Model_provincias", "", false, $localidad->provincia_id);
                    $localidades = $this->Model_provincias->getLocalidades();
                    $provincia = $localidad->provincia_id;
                    $conexion = $this->load->database($filial["codigo"], true);
                    $myDocumentoTipo = new Vdocumentos_tipos($conexion, $objRazon->tipo_documentos);
                    $condicion = $myDocumentoTipo->getCondicionesSociales();
                }
            } else {
                $condicion = array();
            }

            $claves = array(
                'error_requerido', 'error_max_50', 'error_max_250', 'error_numeros', 'no_se_puede_modificar',
                'error_max_11', 'error_max_4', 'error_max_2', 'detalleTel_empresa', 'detalleTel_tipo',
                'detalleTel_numero', 'detalleTel_prefijo', 'detalleTelTabla_empresa',
                'detalleTelTabla_numero', 'detalleTelTabla_eliminar', 'detalleTelTabla_tipo',
                'error_fecha', 'razon_social', 'recuperando', 'sin_registros',
                'domicilio', 'calle_numero', 'calle_complemento', "validacion_ok",
                'tipo_documento', 'numero', 'email', 'telefono', 'codigo',
                'condicion_social', 'tipo_telefono', 'datos_empresa',
                'codarea', 'baja', 'tipo_identificacion', 'numero_identificacion', 'eliminar', 'guardado_correctamente',
                'default', 'ver_editar', 'nuevo_tel', 'sinTelefono', 'razon_existente', 'tel_formato_invalido', 'completar_primer_telefono');

            $data['langFrm'] = getLang($claves);

            $data['esdefault'] = $esdefault;
            $data['razon_social'] = $objRazon;
            $data['condicion'] = $condicion;
            $data['provincias'] = $provincias;
            $data['localidades'] = $localidades;
            $data['tipo_telefono'] = $tipo_telefono;
            $data['empresas_tel'] = $empresas_tel;
            $data['telefonos_razones'] = $arrTelefono;
            $data['tipo_identificacion'] = $tipo_identificacion;
            $data["localidad"] = $localidad;
            $data["provincia"] = $provincia;
            $data['modo'] = $modo;
            $data['filial'] = $this->session->userdata('filial');
            $this->load->view('razones_sociales/frm_razon_social', $data);
        }
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

    /**
     * Guarda todos los datos de la razon social
     * @access public
     * @return json de respuesta
     */
    public function guardar() {
        $this->load->helper('formatearfecha');
        $respuesta = '';
        $this->load->library('form_validation');
        $telefonos = $this->input->post('telefonos');
        $tipo = $this->input->post('tipoIdentificacion');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'required|numeric|validarModificarRazon');
        $this->form_validation->set_rules('nombre', lang('razon_social'), 'required|max_length[40]');
        //$this->form_validation->set_rules('condicion', lang('condicion_social'), 'required');
        $this->form_validation->set_rules('calle_razon', lang('domicilio'), 'required|max_length[50]');
        $this->form_validation->set_rules('calle_num_razon', lang('numero'), 'required|max_length[50]|integer');
        $this->form_validation->set_rules('complemento_razon', lang('complemento'), 'max_length[255]');
        $this->form_validation->set_rules('tipoIdentificacion', lang('tipo_identificacion'), 'required');
        $this->form_validation->set_rules('documento', lang('numero_identificacion'), 'required|validarDocumentoIdentidad[' . $tipo . ']');
        $this->form_validation->set_rules('codpost', lang('codigo_postal'), 'required|max_length[50]');
        $this->form_validation->set_rules('email_razon', lang('email'), 'valid_email');
        $this->form_validation->set_rules('domiciLocalidad', lang('localidad'), 'required');
        $this->form_validation->set_rules('inicio_actividades', lang('inicio_actividades'), 'required');
        $this->form_validation->set_rules('domiciProvincia', lang('provincia'), 'required');
        $arrDatos = json_encode(array($this->input->post("tipoIdentificacion"), $this->input->post('documento')));
        $this->form_validation->set_rules('codigo', lang('codigo'), "validarRazonSocialRegistrada[$arrDatos]");

        //VALIDACIONES DE LOS TELEFONOS 
        if ($telefonos != '') {
            foreach ($telefonos as $tel => $valor) {
                $pos = $tel;
                $pos++;
                $_POST['tipo_tel' . $tel] = $valor['tipo_telefono'];
                $_POST['empresa' . $tel] = $valor['empresa'];
                $_POST['numero' . $tel] = $valor['numero'];
                $_POST['cod_area' . $tel] = $valor['cod_area'];
                $this->form_validation->set_rules('tipo_tel' . $tel, lang('tel_tipo_telefono'), 'required');
                $this->form_validation->set_rules('numero' . $tel, lang('tel_numero'), 'required|numeric');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $respuesta = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['razon_social']['codigo'] = $this->input->post('codigo');
            $data_post['razon_social']['razon_social'] = $this->input->post('nombre');
            $data_post['razon_social']['tipo_documentos'] = $this->input->post('tipoIdentificacion');
            $data_post['razon_social']['documento'] = $this->input->post('documento');
            $data_post['razon_social']['cod_localidad'] = $this->input->post('domiciLocalidad');
            $data_post['razon_social']['cod_postal'] = $this->input->post('codpost');
            $data_post['razon_social']['email'] = $this->input->post('email_razon');
            $data_post['razon_social']['direccion_calle'] = $this->input->post('calle_razon');
            $data_post['razon_social']['direccion_numero'] = $this->input->post('calle_num_razon');
            $data_post['razon_social']['complemento'] = $this->input->post('complemento_razon');
            $data_post['razon_social']['condicion'] = $this->input->post('condicion') ? $this->input->post('condicion') : NULL; // hay paises que no tienen
            $data_post['razon_social']['inicio_actividades'] = formatearFecha_mysql($this->input->post('inicio_actividades'));
            $data_post['razon_social']['usuario_creador'] = $this->session->userdata('usuario_creador');
            $data_post['telefonos'] = array();
            if ($telefonos != '') {
                foreach ($telefonos as $valor){
                    $data_post['telefonos'][] = array(
                        'codigo' => $valor['codigo'],
                        'baja' => $valor['baja'],
                        'empresa' => $valor['empresa'],
                        'tipo_telefono' => $valor['tipo_telefono'],
                        'cod_area' => $valor['cod_area'],
                        'numero' => $valor['numero']
                    );
                }
            }
            $respuesta = $this->Model_razones_sociales->guardar($data_post);
        }
        echo json_encode($respuesta);
    }

    public function cambiarEstado() {
        $this->load->library('form_validation');
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $codigo = $this->input->post('codigo');
        $this->form_validation->set_rules('codigo', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $respuesta = $this->Model_razones_sociales->cambiarEstado($codigo);
            echo json_encode($respuesta);
        }
    }

    /**
     * Retorna json de razon social
     * @access public
     * @return json de razon
     */
    public function getRazonSocial() {

        $codigo = $this->input->post('codigo');
        $respuesta = $this->Model_razones_sociales->getArrRazonSocial($codigo);
        echo json_encode($respuesta);
    }

    public function listarCondicionesSociales() {
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        $arrResp = $this->Model_paises->listarCondicionesSociales($this->input->post("tipo_identificador"));
        echo json_encode($arrResp);
    }

}
