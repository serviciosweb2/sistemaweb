<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Moras_cursos_cortos extends CI_Controller {
    private $seccion;
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        $filial = $this->session->userdata('filial');
        $config = array("codigo_filial" => $filial["codigo"]);
        $this->load->model("Model_moras_cursos_cortos", "", false, $config);
    }

    public function guardarMora(){
        $this->load->library('form_validation');
        $codUsuario = $this->session->userdata('codigo_usuario');
        $resultado = '';
        $diaDesde = $this->input->post('dia_desde');
        $this->form_validation->set_rules('dia_desde',lang('dia_desde'),'required|numeric');
        $this->form_validation->set_rules('dia_hasta',lang('dia_hasta'),'required|numeric|validarDia[' . $diaDesde . ']');
        $this->form_validation->set_rules('mora',lang('MORA'),'required');
        $this->form_validation->set_rules('tipo_mora',lang('tipo_mora'),'required');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            $resultado = array(
                "·codigo"=>'0',
                "msgerrors"=>$errors
            );
        }else{
            $data_post['cod_mora'] =$this->input->post('codigo');
            $data_post['dia_desde'] = $this->input->post('dia_desde');
            $data_post['dia_hasta'] = $this->input->post('dia_hasta');
            $data_post['mora'] = $this->input->post('mora');
            $data_post['es_porcentaje'] = $this->input->post('es_porcentaje');
            $data_post['baja'] = $this->input->post('baja') == 'on' ? 0 : 1;
            $data_post['diariamente'] = $this->input->post('diariamente') == 'on' ? 1: 0;
            $data_post['tipo'] = $this->input->post('tipo_mora');
            $resultado = $this->Model_moras_cursos_cortos->guardarMora($data_post,$codUsuario);
        }
        echo json_encode($resultado);
    }

    public function bajaMora(){
        $cod_mora = 3;//$this->input->post('codigo');
        $retorno = $this->Model_moras_cursos_cortos->bajaMora($cod_mora);
        echo json_encode($retorno);
    }
}