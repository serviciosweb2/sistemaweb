<?php 

/**
 * Mod_provincias
 * 
 * Description...
 * 
 * @package mod_provincias
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */

if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Model_provincias extends CI_Model {
        var $cod_provincia =0;
	public function __construct($arg = 0)
	{
		parent::__construct();
                //echo'constructor'.$arg;
                $this->cod_provincia =  $arg;
                
	}
        
     
   
        /**
         * retorna vista de alumnos main panel
         * @access public
         * @param $id_pais pais de las que se quieren recuperar provincias
         * @return array de provicias
         */
        public function getprovincias($id_pais) {
             $this->load->database();
             $conexion = $this->db;
             $condiciones = array(
                 "pais"=>$id_pais
             );
             //modificacion franco ticket 5053 ->
             return Tprovincias::listarProvincias($conexion,$condiciones);
            //<- modificacion franco ticket 5053
        }
        
         /**
         * retorna arr localidades segun la provincia 
         * @access public
         * @return array de localidades
         */
        public function getLocalidades(){
             $this->load->database();
             $conexion = $this->db;
             
             $condiciones = array(
                 "provincia_id"=>  $this->cod_provincia
             );
             $localidades = Vlocalidades::listarLocalidades($conexion,$condiciones);
          
             return $localidades;
        }
        
        public function getProvinciaLocalidad($cod_localidad){
            $this->load->database();
            $conexion = $this->db;
            $localidad = new Vlocalidades($conexion, $cod_localidad);
          
            return $localidad;
        }
 
        public function getPais(){
            $conexion = $this->load->database("default", true);
            $myProvincia = new Vprovincias($conexion, $this->cod_provincia);
            return $myProvincia->pais;
        }
}

/* End of file mod_provincias.php */
/* Location: ./application/models/mod_provincias.php */