<?php 

/**
 * Model_talles
 * 
 * Description...
 * 
 * @package model_talles
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */

if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Model_talles extends CI_Model {

	public function __construct()
	{
		parent::__construct();
                    
       
		
	}
        public function getTalles() {
                  $this->load->database();
             $conexion = $this->db;
             $order[] = array("campo"=>"talle","orden"=>"asc");
             return Vtalles::listarTalles($conexion,null,null,$order);
        }
}

/* End of file model_talles.php */
/* Location: ./application/models/model_talles.php */