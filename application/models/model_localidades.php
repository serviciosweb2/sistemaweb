<?php 

/**
 * Model_localidades
 * 
 * Description...
 * 
 * @package model_localidades
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */

if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Model_localidades extends CI_Model {
        var $id = 0;
	public function __construct($arg)
	{
		parent::__construct();
                $this->id = $arg;    
       
		
	}
        public function getLocalidad(){
               $this->load->database(); 
              
               $localidades = new Vlocalidades($this->db,$this->id);
               
               return $localidades ;
        }
        
        public function getProvincia($idlocalidad){
            $this->load->database();
            $localidades = new Vlocalidades($this->db,$idlocalidad);
            return $localidades->provincia_id;
        }
       
}

/* End of file model_localidades.php */
/* Location: ./application/models/model_localidades.php */