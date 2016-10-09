<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Model_telefonos extends CI_Model {

	public function __construct($arg)
	{
		parent::__construct();
                    
       
		
	}
        public function getTelefonosTipos(){
            
            return Vtelefonos::getArray();
        }
      
}
?>
