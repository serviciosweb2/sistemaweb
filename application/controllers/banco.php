<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Banco extends CI_Controller {
private $seccion;
    public function __construct() {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
     
        $filial = $this->session->userdata('filial');
        $this->load->model("Model_bancos", "", false, $filial["codigo"]);
        $this->load->helper("datatables");
        
    }


    
    
    
}