<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion alumnos.
 */
class Usuarios extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');

        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
    }

    private function crearColumnas() {

        $columnas = array(
            array("nombre" => lang('codigo'), "campo" => 'codigo'),
            array("nombre" => lang('nombre'), "campo" => 'nombre'),
            array("nombre" => lang('apellido'), "campo" => 'apellido'),
            array("nombre" => lang('fecha_creacion'), "campo" => 'fecha_creacion'),
            array("nombre" => lang('email'), "campo" => 'email'),
            array("nombre" => lang('baja_usuario'), "campo" => 'baja', "sort" => false, 'bVisible' => false),
            array("nombre" => lang('estado_usuario'), "campo" => 'estado', "sort" => false));
        return $columnas;
    }

    public function WS_getUsuarios() {
        $idFilial = isset($_POST['id_filial']) && $_POST['id_filial'] > 0 ? $_POST['id_filial'] : null;
        $conEmail = null;
        if (isset($_POST['con_email']) && $_POST['con_email'] == true){
            $conEmail = true;
        } else if (isset($_POST['con_email']) && $_POST['con_email'] == false){
            $conEmail = false;
        }
        $baja = isset($_POST['baja']) ? $_POST['baja'] : null;
        $administra = isset($_POST['administra']) ? $_POST['administra'] : null;        
        $arrResp = $this->Model_usuario->WS_getUsuarios($idFilial, $baja, $conEmail, $administra);
        echo json_encode($arrResp);
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        echo $aoColumnDefs;
    }

    public function listarUsuarios() {

        $filial = $this->session->userdata('filial');
        $crear = $this->crearColumnas();
        $cod_filial = $filial['codigo'];
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sSearch"] = $this->input->post("sSearch");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crear [$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $respuesta = $this->Model_usuario->listarUsuariosDataTable($cod_filial, $arrFiltros);
        echo json_encode($respuesta);
    }

    public function frm_baja() {
        $this->load->library('form_validation');
        $cod_usuario = $this->input->post('cod_usuario');
        $this->form_validation->set_rules('cod_usuario', lang('codigo'), 'numeric');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            echo $errors;
        } else {
            $motivos = $this->Model_usuario->getMotivosUsuarios();
            $data['motivos'] = $motivos;
            $data['cod_usuario'] = $cod_usuario;
            $this->load->view('configuracion/frm_baja', $data);
        }
    }

    public function cambioEstadoUsuario() {

        $this->load->library('form_validation');
        $resultado = '';
        $codUsuario = $this->session->userdata('codigo_usuario');
        $cod_usuario = $this->input->post('cod_usuario');
        $motivo = $this->input->post('motivo');
        $comentario = $this->input->post('comentario');
        $fecha = date("Y-m-d H:i:s");
        $this->form_validation->set_rules('motivo', lang('motivo'), 'required');
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $cambioEstado = array(
                'cod_usuario' => $cod_usuario,
                'motivo' => $motivo,
                'comentario' => $comentario,
                'fecha' => $fecha,
                'cod_usuario_creador' => $codUsuario,
            );
            $resultado = $this->Model_usuario->cambioEstadoUsuario($cambioEstado);
        }
        echo json_encode($resultado);
    }

    public function guardarUsuario() {
        $this->load->library('form_validation');
        $filial = $this->session->userdata('filial');
        $cod_usuario = $this->input->post('cod_usuario');
        if ($cod_usuario == -1) {
            $this->form_validation->set_rules('nombre', lang('nombre_usuario'), 'required|validarNombreApellido[' . 'nombreAlumnoInvalido' . ']');
            $this->form_validation->set_rules('apellido', lang('apellido_usuario'), 'required|validarNombreApellido[' . 'apellidoAlumnoInvalido' . ']');
            $this->form_validation->set_rules('email', lang('email_usuario'), 'required|valid_email|is_unique[usuarios_sistema.email]');
            $this->form_validation->set_rules('idioma', lang('idioma_usuario'), 'required');
        } else {
            $this->form_validation->set_rules('nombre', lang('nombre_usuario'), 'required|validarNombreApellido[' . 'nombreAlumnoInvalido' . ']');
            $this->form_validation->set_rules('apellido', lang('apellido_usuario'), 'required|validarNombreApellido[' . 'apellidoAlumnoInvalido' . ']');
            $this->form_validation->set_rules('idioma', lang('idioma_usuario'), 'required');
        }
        if(strlen($this->input->post('pass')) != 32){
           $this->form_validation->set_rules('pass', lang('password_usuario'), 'required|validarPassword'); 
        }
        
        $resultado = '';
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['id_usuario_session'] = $this->session->userdata['codigo_usuario'];
            $data_post['cod_usuario'] = $this->input->post('cod_usuario');
            $data_post['nombre_usuario'] = $this->input->post('nombre');
            $data_post['apellido_usuario'] = $this->input->post('apellido');
            $data_post['idioma_usuario'] = $this->input->post('idioma');
            $data_post['password_usuario'] = $this->input->post('pass');
            $data_post['password_usuario_old'] = $this->input->post('pass_old');
            $data_post['calle_usuario'] = $this->input->post('calle');
            $data_post['numero_calle'] = $this->input->post('numero');
            $data_post['calle_complemento'] = $this->input->post('complemento');
            $data_post['fecha_creacion'] = date('Y-m-d H:i:s');
            $data_post['filial'] = $filial['codigo'];
            $data_post['cod_filial'] = $filial["codigo"];
            $data_post['baja'] = 0;
            $data_post['email_usuario'] = $this->input->post('email');
            $data_post['caja_default'] = $this->input->post('caja_default');
            $data_post['listaPermisos'] = $this->input->post('listaPermisos') != '' ? $this->input->post('listaPermisos') : '';
            $resultado = $this->Model_usuario->guardarUsuario($data_post);
        }
        
        $this->Model_usuario->refrescarSession($this->session->userdata['codigo_usuario']);
        echo json_encode($resultado);
    }

    public function getTareasUsuario() {
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $estado = $this->input->post('estado');
        $tareasUsuario = $this->Model_usuario->getTareasUsuario($cod_usuario, $estado);
        echo json_encode($tareasUsuario);
    }

    public function guardarTareaUsuario() {
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('respuesta', lang('respuesta'), 'required|max_length[50]');
        $this->form_validation->set_rules('usuarios_asignados[]', lang("nombre_de_tarea"), 'required');
        $resultado = '';
        if ($this->form_validation->run() == false) {
            $errors = validation_errors();
            $resultado = array(
                'codigo' => '0',
                'msgerror' => $errors,
                'errNo' => ''
            );
        } else {
            $data_post['codigo'] = $this->input->post('codigo');
            $data_post['estado'] = 'noconcretadas';
            $data_post['respuesta'] = $this->input->post('respuesta');
            $data_post['cod_usuario'] = $cod_usuario;
            $data_post['usuarios_asignados'] = $this->input->post("usuarios_asignados");
            $resultado = $this->Model_usuario->guardarTareaUsuario($data_post);
//            echo "<pre>"; print_r($_POST); echo "</pre>";
        }
        echo json_encode($resultado);
    }

    public function cambiarEstadoTareaUsuario() {
        $codigo = $this->input->post('codigo');
        $estado = $this->input->post('estado');
        $cambiarEstado = array(
            'codigo' => $codigo,
            'estado' => $estado
        );
        $resultado = $this->Model_usuario->cambiarEstadoTareaUsario($cambiarEstado);
        echo json_encode($resultado);
    }
    
    public function getNombreUsuario()
    {
        $filial = $this->session->userdata('filial');
                        
        $retorno = array('nombre'=>$this->session->userdata('nombre'),'filial'=> $filial['nombre']);
        
        echo json_encode($retorno);
        
    }
    
    public function setFilial(){
        $data_post['filial'] = $this->input->post('filial');
        $this->Model_usuario->setFilial($data_post['filial']);
    }
    
    /* la siguiente function es accedida desde un web services NO MODIFICAR, COMENTAR NI ELIMINAR */
    public function get_filiales(){        
        if ($this->input->post("cod_usuario")){
            $arrResp = array();
            $conexion = $this->load->database("general", true);
            $myUsuario = new Vusuarios_sistema($conexion, $this->input->post("cod_usuario"));
            $arrFiliales = $myUsuario->getFiliales($this->input->post("cod_usuario"), $conexion);
            $arrTemp = array();
            $i = 0;
            foreach ($arrFiliales as $filial){
                if ($filial['cod_filial'] <> $myUsuario->cod_filial){
                    $arrTemp[$i]['codigo'] = $filial['cod_filial'];
                    $i++;
                }
            }            
            $arrResp['transport']['usuario'] = array(
                "codigo" => $myUsuario->getCodigo(),
                "nombre" => $myUsuario->nombre,
                "apellido" => $myUsuario->apellido,
                "email" => $myUsuario->email,
                "cod_filial" => $myUsuario->cod_filial
            );
            $arrResp['transport']['aaData'] = $arrTemp;
            $arrResp['transport']['iTotalRecords'] = count($arrTemp);
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400); // Bad Request
            die();
        }
    }
    
    public function set_filiales_usuario(){
        if ($this->input->post("cod_usuario") && $this->input->post("filiales") 
                && $this->input->post("id_usuario_iga") && $this->input->post("nombre_usuario_iga")){
            $conexion = $this->load->database("default", true);
            $codUsuario = $this->input->post("cod_usuario");
            $id_usuario_iga = $this->input->post("id_usuario_iga");
            $nombre_usuario_iga = $this->input->post("nombre_usuario_iga");
            $arrFiliales = is_array($this->input->post("filiales")) ? $this->input->post("filiales") : array();
            $myUsuario = new Vusuarios_sistema($conexion, $codUsuario);
            $arrFiliales[] = $myUsuario->cod_filial;
            $conexion->trans_begin();
            $arrResp = array();
            if ($myUsuario->setFiliales($arrFiliales, $id_usuario_iga, $nombre_usuario_iga)){
                $conexion->trans_commit();
                $arrResp['success'] = "success";
                $arrResp['cod_usuario'] = $myUsuario->getCodigo();
                $arrResp['filiales'] = $arrFiliales;
            } else {
                $conexion->trans_rolback();
                $arrResp['error'] = "error";
                $arrResp['msg'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
            echo json_encode($arrResp);
        } else {
            header("Status: 400", true, 400); // Bad Request
            die();
        }
    }
    
}

?>
