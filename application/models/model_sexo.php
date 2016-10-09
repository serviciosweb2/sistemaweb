<?php 

/**
 * Model_sexo
 * 
 * Description...
 * 
 * @package model_sexo
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */

if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Model_sexo extends CI_Model {

	public function __construct($arg)
	{
		parent::__construct();
                    
       
		
	}
       public function getSexos(){
           
           return  Vsexo::getArray();
           
       }
}

/* End of file model_sexo.php */
/* Location: ./application/models/model_sexo.php */