<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Dashboard extends CI_Controller {

    private $seccion;

    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_configuraciones", "", false, $config);
    }

    public function index() {
        //$this->actualizarCache();
        $this->load->helper('file');
        $array = array(
            "titulo" => '',
            "categoria" => 'home'
        );
        $claves=Array(
                '_idioma',
                'upps',
                'ok',
                'validacion_error',
                "validacion_ok",
                'ayer',
                'hoy',
                'maniana',
                'no_concretadas',
                'concretadas',
                'proxima_semana',
                'a_las',
                'la_semana_pasada',
                'nombre_y_apellido',
                'cursos',
                'meses_adeudados',
                'casa_central',
                'no_tiene_tareas_pendientes',
                "no_tiene_comunicados",
                "nombre_de_la_tarea_es_requerido",
                "debe_indicar_algun_usuario",
                "prioridad_baja",
                "prioridad_media",
                "prioridad_alta"
                );
        
        $filial = $this->session->userdata('filial');
        
        if($filial['offline']['habilitado']=='1' and $this->config->item('modo_offline')){
           
            $data['cachear']=true; 
        }
        
        $filial = $this->session->userdata('filial');
        $configAlumnos = array("filial" => $filial["codigo"]);
        $this->load->model("Model_usuario", "", false, $configAlumnos);
        $data['usuarios_tareas'] = $this->Model_usuario->getUsuarios(0);
        $data['lang']= getLang($claves);
        $data['getColumns']= $this->getColumns();
        $data['page'] = 'dashboard'; // pasamos la vista a utilizar como parámetr
        $data['tienePermiso_consultasWeb'] = $this->Model_usuario->tienePermisoSeccion('consultasweb',null);
        
        $data['seccion'] = $array;
        $this->load->view('container', $data);
    }
    
    public function actualizarCache(){//metodo creado a modo de prueba, no esta en uso
        
        $archivo = file_get_contents('../sistemasiga/cache.manifest');
        $cache = explode("\n",$archivo);
        $cache[1] = '#version '.date('Y:m:d h:m:s');
        file_put_contents('../sistemasiga/cache.manifest',implode("\n",$cache));
        
    }

    public function crearColumnas() {
        $columnas = array(
            array("nombre" => '', "campo" => 'check', "sort" => false),
            array("nombre" => lang('codigo'), "campo" => 'cod_matricula', 'bVisible' => false),
            array("nombre" => lang('nombre_y_apellido'), "campo" => 'nombre_apellido', "sort" => true),
            array("nombre" => lang('cursos'), "campo" => 'nombre_curso'),
            array("nombre" => lang('motivo_baja'), "campo" => 'cantMesesVencidos'),
            array("nombre" => lang('prioridad'), "campo" => 'vencimientoviejo'),
        );
        return $columnas;
    }

    public function getColumns() {
        $this->load->helper("datatables");
        $aoColumnDefs = json_encode(getColumnsDatatable($this->crearColumnas()));
        return $aoColumnDefs;
    }

    public function deudoresCtaCte() {
        $crearColumnas = $this->crearColumnas();
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_matriculas", "", false, $configMatriculas);
        $arrFiltros["iDisplayStart"] = $this->input->post("iDisplayStart");
        $arrFiltros["iDisplayLength"] = $this->input->post("iDisplayLength");
        $arrFiltros["sEcho"] = $this->input->post("sEcho");
        $arrFiltros["SortCol"] = isset($_POST['iSortCol_0']) ? $crearColumnas[$_POST['iSortCol_0']]["campo"] : "";
        $arrFiltros["sSortDir"] = isset($_POST['sSortDir_0']) ? $_POST['sSortDir_0'] : "";
        $deudores = $this->Model_matriculas->listarMatriculasParaBaja($arrFiltros);

        /*
         * 
         */
        
        echo json_encode($deudores);
    }

    public function getMailsConsultas() {
        $filial = $this->session->userdata('filial');
        $codigo = $this->input->post('codigo');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_consultasweb", "", false, $config);
        $consultasWeb = $this->Model_consultasweb->getUltimosMailsConsultas($filial['codigo'], $codigo);

        echo json_encode($consultasWeb);
    }

    public function guardarSugerenciaBaja() {
        $usuario = $this->session->userdata('codigo_usuario');
         $this->load->library('form_validation');
        $cod_matricula = $this->input->post('cod_matricula');
        $filial = $this->session->userdata('filial');
        $configMatriculas = array("filial" => $filial);
        $this->load->model("Model_matriculas", "", false, $configMatriculas);
        $resultado = '';
        $this->form_validation->set_rules('cod_matricula',lang('seleccione_alumno'),'required');
        if($this->form_validation->run() == FALSE){
            $errors = validation_errors();
            $resultado = array(
                "codigo"=>0,
                "msgError"=>$errors
            );
        }else{
            foreach ($cod_matricula as $matricula) {
            $cambiomatricula[] = array(
                "cod_matricula" => $matricula,
                "comentario" => '',
                "motivo" => 4,
                "cod_usuario" => $usuario
            );
        }
        $resultado = $this->Model_matriculas->bajaMatriculas($cambiomatricula);
        }
        echo json_encode($resultado);
    }

    public function getComunicadosFilial() {
        $codigo = $this->input->post('codigo');
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_filiales", "", false, $filial['codigo']);
        $comunicados = $this->Model_filiales->getComunicadosFilial($filial['codigo'], $codigo);
        echo json_encode($comunicados);
    }

}
