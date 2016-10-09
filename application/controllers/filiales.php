<?php

/**
 * Control Alumnos.
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Ariel Di Cesare   <sistemas4@iga-la.net>
 * @version  $Revision: 1.0 $
 * @access   public
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion alumnos.
 */
class Filiales extends CI_Controller {

    //private $seccion;

    public function __construct()
    {
        parent::__construct();
        $this->lang->load(get_idioma(), get_idioma());
        $this->seccion = session_method();
        //$filial = $this->session->userdata('filial');

        //$configAlumnos = array("codigo_filial" => $filial["codigo"]);

        //$this->load->model("Model_alumnos", "", false, $configAlumnos);
    }

    public function api_getFiliales()
    {
        $pais = $_POST['pais']; 
        $this->load->model("Model_filiales", "", false,'');
        $filiales = $this->Model_filiales->getFiliales($pais,2);
//        echo '<pre>'; 
//        print_r($filiales);
//        echo '</pre>';
        echo json_encode($filiales);
    }
    
    public function api_setEstadoFilial()
    {
          $codigo = $_POST['codigo']; 
        $estado = $_POST['estado'];
//
//              $codigo ="999";
//    $estado = "suspendida";
//        
//            $codigo ="54";
//    $estado = "suspendida";
        
        $this->load->model("Model_filiales", "", false,'');
        $filiales = $this->Model_filiales->SetEstado($codigo,$estado);
        
        echo json_encode($filiales);
    }
    
    
    
    
    
    
    public function api_getPaises()
    {
        $conexion = $this->load->database('',TRUE);
        $paises = Vpaises::listarpaises($conexion);
        echo json_encode($paises);
    }

   public function actualizarTablasUsuarioCreadorFilial(){
       $this->load->model("Model_filiales", "", false,'');
       $arrResp = $this->Model_filiales->actualizarTablasUsuarioCreadorFilial();
       echo json_encode($arrResp);
   }

}

