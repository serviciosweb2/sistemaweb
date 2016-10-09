<?php 

/**
 * Model_estados_civiles
 * 
 * Description...
 * 
 * @package model_estados_civiles
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */

if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Model_estados_civiles extends CI_Model {

	public function __construct($arg)
	{
		parent::__construct();
                    
       
		
	}
        public function getEstados_civiles(){
            
            return Vestados_civiles::getArray();
        }
}

/* End of file model_estados_civiles.php */
/* Location: ./application/models/model_estados_civiles.php */