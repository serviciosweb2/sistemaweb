<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Impuestos extends CI_Controller{
    public function __construct() {
        parent::__construct();
         $filial = $this->session->userdata('filial');
         $this->lang->load(get_idioma(), get_idioma());
         $config = array("codigo_filial" => $filial["codigo"]);
         $this->load->model("Model_impuestos","",false,$config);
     }
     
     public function guardarImpuesto(){
         session_method();
          $this->load->library('form_validation');
          $this->form_validation->set_rules('nombre_impuesto',lang('nombre_impuesto'),'required');
          $this->form_validation->set_rules('valor_impuesto',lang('valor'),'required|numeric');
          $resultado = '';
          if($this->form_validation->run() == false){
               $errors = validation_errors();
                 $resultado = array(
                     'codigo'=>'0',
                     'msgerror'=> $errors,
                     'errNo'=>''
                 );
          }else{
              $data_post['cod_impuesto']            = $this->input->post('cod_impuesto');
              $data_post['nombre_impuesto']         = $this->input->post('nombre_impuesto');
              $data_post['valor_impuesto']          = $this->input->post('valor_impuesto');
              $data_post['tipo_impuesto']           = $this->input->post('tipo_impuesto');
              $data_post['cod_impuesto_impuesto']   = $this->input->post('cod_impuesto_general');
              $data_post['estado_impuesto']         = $this->input->post('estado_impuesto');
              $resultado = $this->Model_impuestos->guardarImpuesto($data_post);
          }
          echo json_encode($resultado);
     }
     
//     public function guardarConceptoImpuesto(){
//         session_method();
//         $this->load->library('form_validation');
//          $filial = $this->session->userdata('filial');
//         $conceptos = $this->input->post('concepto');
//         
//         $resultado = '';
//         $separador = $filial['moneda']['separadorDecimal'];
//         foreach($conceptos as $key=>$concepto){
//             $_POST['cod_concepto'.$key] = $concepto['cod_concepto'];
//               $posicion = $key;
//              $posicion++;
//              $this->form_validation->set_rules('cod_concepto'.$key, lang('cod_concepto'),'required');
//             
//         }
//         if($this->form_validation->run() == false){
//             $errors = validation_errors();
//                $resultado = array(
//                    'codigo' => '0', 
//                    'respuesta' => $errors
//                );
//         }else{
//             $cod_impuesto = $this->input->post('cod_impuesto');
//             $nombreImpuesto = $this->input->post('nombre_impuesto');
//             $valorImpuesto = $this->input->post('valor');
//             $resultado = $this->Model_impuestos->guardarConceptoImpuesto($cod_impuesto,$conceptos,$separador,$nombreImpuesto,$valorImpuesto);
//         }
//         echo json_encode($resultado);
//     }
     
    
}
?>
