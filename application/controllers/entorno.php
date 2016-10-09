<?php 

if (!defined('BASEPATH')) 
	exit('No direct script access allowed');

class Entorno extends CI_Controller {

	public function __construct() {
		parent::__construct();
            $this->lang->load(get_idioma(), get_idioma());	
                
	}
	
	public function index()
	{
		
	}
        public function getLang(){
            
            $claves = $this->input->post("claves");
  
            $claves = json_decode($claves);
            
            $arrRetorno = array();
            foreach ($claves as $value) {

                $arrRetorno["$value"] = lang($value);
            }
            echo json_encode($arrRetorno);
        }
        
        public function getMenuJson(){
           
            
            $seccion=$this->input->post('seccion');
            
            $session=$this->session->all_userdata();
            
            $contextual=Array();
            
            foreach($session['secciones'][$seccion]['subcategorias'] as $valor){
         
                $valor_tipo=$valor['menu_tipo'];
         
                $valor_habilitado=$valor['habilitado'];
         
                if($valor_tipo=='menu_contextual'){
            
                    $slug=$valor['slug'];
            

            
                    $accion= $slug ;
            
                    $contextual[]=array(
                        'habilitado'=>$valor_habilitado,
                        'accion'=>$accion,
                        'text'=>lang($slug)
                        );


                 }
            }
    
   
            /**SUPERIOR**/
            $superior=array();
            
            foreach($session['secciones'][$seccion]['subcategorias'] as $valor){
                $valor_tipo=$valor['menu_tipo'];
                $valor_habilitado=$valor['habilitado'];
                if($valor_tipo=='menu_superior'){
                    
                    $slug= $valor['slug'];
                    
                    $accion= $slug;
                    
                    $habilitado= $valor_habilitado;
            
                    
            
                    $superior[]=array(
                        'habilitado'=>$valor_habilitado,
                        'accion'=>$accion,
                        'text'=>lang($slug)
                        );
         }
     }
     
            
            
            
            
            $menu=array(
              
                'superior'=>$superior,
                  
                'contextual'=>$contextual
                
            );
            
            
            echo json_encode($menu);
        }
        
        
        public function getMenu(){
            $seccion=$this->input->post('seccion');
            //$seccion='comisiones';
         
            $menu=array(
              
                'superior'=>session_menu_superior($seccion),
                  
                'contextual'=>session_menu_contextual($seccion)
                
            );
            
            echo json_encode($menu);
          
        }
        
        public function detalleAjaxError(){
            
        }
        
 
}

/* End of file configuracion.php */
/* Location: ./application/controllers/configuracion.php */
