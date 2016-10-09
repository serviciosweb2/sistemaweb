<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proveedores extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_proveedores", "", false, $config);
        $this->lang->load(get_idioma(), get_idioma());
        
        
    }

    public function index() {
        $this->lang->load(get_idioma(), get_idioma());
        session_method();      
        $data = array();
        $this->load->view('proveedores/listado_proveedores', $data);
    }

    private function crearColumnas(){
        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang('domicilio'), "campo" => 'direccion'),
            array("nombre" => lang('identificacion'), "campo" => 'identificacion'),
            array("nombre" => lang('telefono'), "campo" => ''),
            array("nombre" => lang('email'), "campo" => 'proveedores.email'),
            array("nombre" => lang('descripcion'), "campo" => 'proveedores.descripcion'),
            array("nombre" => lang('estado'), "campo" => 'baja', "sort" => false)
        );
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        echo $aoColumnDefs;
    }
    
    public function listar() {
        session_method();
        $crearColumnas = $this->crearColumnas();
        $arrFiltros["iDisplayStart"] = isset($_POST['iDisplayStart']) ? $_POST['iDisplayStart'] : "";
        $arrFiltros["iDisplayLength"] = isset($_POST['iDisplayLength']) ? $_POST['iDisplayLength'] : "";
        $arrFiltros["sSearch"] = isset($_POST['sSearch']) ? $_POST['sSearch'] : "";
        $arrFiltros["sEcho"] = isset($_POST['sEcho']) ? $_POST['sEcho'] : "";
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";

        $proveedores = $this->Model_proveedores->listarProveedoresDataTable($arrFiltros);

        echo json_encode($proveedores);
    }
    
     public function frm_proveedores() {
        session_method();
        $data = '';
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $cod_proveedor = $this->input->post('cod_proveedor');
        //CARGO MODELO
        $this->load->model("Model_paises", "", false, $filial["pais"]);
        
        $claves = array(
            "recuperando",
            "proveedor_guardado_correctamente",
            "no_puede_borrar_el_telefono_predeterminado"
        );
        
        $data['langFrm'] = getLang($claves);
        
        $data['condiciones'] = $this->Model_paises->getCondicionesSociales();
        $data['empresas_tel'] = $this->Model_paises->getEmpresasTelefonicas();
        $data['provincias'] = $this->Model_paises->getprovincias();
        $data['tipo_identificacion'] = $this->Model_paises->getDocumentos();
        $data['tipo_telefono'] = Vtelefonos::getArray();        
        $data['objProveedor'] = $this->Model_proveedores->getProvedor($cod_proveedor);        
        $data['telefonosProveedores'] = $this->Model_proveedores->getTelefonosProveedor($cod_proveedor);
        $data['razonesProveedores'] = $this->Model_proveedores->getRazonesProveedores($cod_proveedor);
        $data['tipo_documentos'] = $this->Model_paises->getDocumentos();
        $data['arrProveedor'] = array();
        if($cod_proveedor != -1){
            $data['arrProveedor'] = $this->Model_proveedores->getDatosProveedores($cod_proveedor);
        }
        if ($cod_proveedor > 0){
            $this->load->model("Model_localidades", "", false, $data['arrProveedor'][0]['cod_localidad']);
            $localidad = $this->Model_localidades->getLocalidad();
            $idProvincia = $localidad->provincia_id;
        } else {
            $idProvincia = $data['provincias'][0]['id'];
        }
        $this->load->model("Model_provincias", "", false, $idProvincia);
        $localidades = $this->Model_provincias->getLocalidades();
        $data['localidades'] = $localidades;
        $data['provincia_sel'] = $idProvincia;
        $this->load->view('proveedores/frm_proveedores', $data);
    }

    public function guardar() {
//        echo json_encode(array("codigo" => "1")); die();
        session_method();
        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        $usuario = $this->session->userdata('codigo_usuario');
        $documento = $this->input->post('numero_identificacion');
        $tipoDni = $this->input->post('tipo_identificacion_proveedor');
        $cod_proveedor = $this->input->post('cod_proveedor');
        $resultado = '';
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nombre', lang('nombre'), 'required|max_length[50]');
        $this->form_validation->set_rules('descripcion', lang('descripcion'), 'max_length[255]');
        $this->form_validation->set_rules('cod_localidad', lang('cod_localidad'), 'max_length[11]|integer');
        $this->form_validation->set_rules('codpost', lang('codpost'), 'required|max_length[50]');
        $this->form_validation->set_rules('email', lang('email'), 'valid_email');
        $this->form_validation->set_rules('calle', lang('calle'), 'max_length[100]');
        $this->form_validation->set_rules('calle_numero', lang('calle_numero'), 'max_length[50]');
        $this->form_validation->set_rules('condicion',lang('condicion_social'),'required');
        $this->form_validation->set_rules('inicio_activad',lang('inicio_actividades'),'required');
        if($cod_proveedor == -1){
            $this->form_validation->set_rules('tipo_identificacion_proveedor',lang('tipo_identificacion'),'required|validarNumeroIdentificacion['.$documento.']');
        }
        $this->form_validation->set_rules('numero_identificacion',lang('numero_identificacion'),'required|validarDocumentoIdentidad[' . $tipoDni . ']');
        $razones = $this->input->post('razones') == 0 ? array() : $this->input->post('razones');
        $telefonos = $this->input->post('telefonos');
        //VALIDACION TELEFONOS
        $telDefault = '';
        if ($telefonos != '') {
            foreach ($telefonos as $t => $telefono) {
                $_POST['tel_empresa' . $t] = $telefono['empresa'];
                $_POST['tel_tipo_telefono' . $t] = $telefono['tipo_telefono'];
                $_POST['tel_cod_area' . $t] = $telefono['cod_area'];
                $_POST['tel_numero' . $t] = $telefono['numero'];
                isset($telefono['default']) ? $telDefault.='d' : $telDefault;
                $posicion = $t;
                $posicion++;
                $this->form_validation->set_rules('tel_empresa' . $t, lang('tel_empresa') . ' ' . $posicion . ' ' . lang('alumno'), 'required');
                $this->form_validation->set_rules('tel_tipo_telefono' . $t, lang('tel_tipo_telefono') . ' ' . $posicion . ' ' . lang('alumno'), 'required');
                $this->form_validation->set_rules('tel_cod_area' . $t, lang('tel_cod_area') . ' ' . $posicion . ' ' . lang('alumno'), 'required|numeric');
                $this->form_validation->set_rules('tel_numero' . $t, lang('tel_numero') . ' ' . $posicion . ' ' . lang('alumno'), 'required|numeric');

                $_POST['tel_default'] = $telDefault;
                $this->form_validation->set_rules('tel_default', lang('tel_default'), 'required|max_length[1]');
            }
        }

        foreach ($razones as $key => $razon) {
            $_POST['FrazSoc' . $key] = $razon['razon_social'];
            $this->form_validation->set_rules('FrazSoc' . $key, lang('FrazSoc') . ' ' . lang('Razon') . ' ' . $key, 'max_length[30]');
        }

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();

            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => '',
            );
        } else {

            $data_post['proveedor']['codigo'] = $this->input->post('cod_proveedor');
            $data_post['proveedor']['nombre'] = $this->input->post('nombre');
            $data_post['proveedor']['descripcion'] = $this->input->post('descripcion');
            $data_post['proveedor']['cod_localidad'] = $this->input->post('cod_localidad');
            $data_post['proveedor']['condicion'] = $this->input->post('condicion');
            $data_post['proveedor']['cod_postal'] = $this->input->post('codpost');
            $data_post['proveedor']['email'] = $this->input->post('email');
            $data_post['proveedor']['web'] = $this->input->post('web');
            $data_post['proveedor']['calle'] = $this->input->post('calle');
            $data_post['proveedor']['numero'] = $this->input->post('calle_numero');
            $data_post['proveedor']['complemento'] = $this->input->post('calle_complemento');
            $data_post['proveedor']['fecha_alta'] = date('Y-m-d H:i:s');
            $data_post['proveedor']['cod_usuario_creador'] = $usuario;
            $data_post['proveedor']['baja'] = 0;
            $data_post['proveedor']['tipo_identificacion'] = $this->input->post('tipo_identificacion_proveedor');
            $data_post['proveedor']['numero_identificacion'] = $this->input->post('numero_identificacion');
            $data_post['proveedor']['inicio_actividades'] = $this->input->post('inicio_activad');
            $data_post['telefonos'] = array();
            if ($telefonos != '') {
            foreach ($telefonos as $valor) {
                $data_post['telefonos'][] = array(
                    'codigo' => $valor['telefono_codigo'],
                    'empresa' => $valor['empresa'],
                    'tipo_telefono' => $valor['tipo_telefono'],
                    'cod_area' => $valor['cod_area'],
                    'numero' => $valor['numero'],
                    'default' => isset($valor['default']) ? $valor['default'] : 0
                );
             }
            }
            $data_post['razonsocial'] = array();
            
            foreach ($razones as $key => $valor) {
                $data_post['razonsocial'][$key] = array(
                    'tipo_documento' => $valor['tipo_doc'],
                    'codigo' => $valor['razon_codigo'],
                    'documento' => $valor['documento'],
                    'razon_social' => $valor['razon_social'],
                    'condicion' => $valor['condicion'],
                    'default' => isset($valor['default']) ? $valor['default'] : 0,
                    'baja' => "0"
                );
            }

            $resultado = $this->Model_proveedores->guardar($data_post);
        }

        echo json_encode($resultado);
    }

    public function cambiarEstado() {
        session_method();

        $codproveedor = $this->input->post('cod_proveedor');
        $resultado = $this->Model_proveedores->cambiarEstado($codproveedor);

        echo json_encode($resultado);
    }

    public function modificar(){
        session_method();
        echo "hoal";
    }
    
}

?>
