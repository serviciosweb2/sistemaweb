<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->lang->load(get_idioma(), get_idioma());
        $this->load->model("Model_usuario","",false);
    }
    
    public function index(){
        $this->load->library('user_agent');
        $session = $this->session->userdata;
        //if(!$this->agent->is_browser('Chrome')){
        //    $this->load->view('soloChrome');
        //    return;
        //}        
        if(isset($session['codigo_usuario'])){
            redirect('dashboard');
        }else{
            $this->load->view('welcome_login');      
        }
    }

    public function validarUsuario($usuario,$pass){
        $usuario=array(
            'usuario'=>$usuario,
            'pass'=>$pass
        );
        return $resultado;
    }
        
    public function crearSession($data){
       return  $this->session->set_userdata($data);          
    }

    public function logOut($msj=''){
        $this->lang->load(get_idioma(), get_idioma());
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function verSession(){           
        $this->lang->load(get_idioma(), get_idioma());
        $ver=$this->session->all_userdata();
    }
    
    public function validaLogin(){
        $this->lang->load(get_idioma(), get_idioma()); 
        $this->form_validation->set_rules('usuario',lang('usuario'),'required|valid_email');
        $this->form_validation->set_rules('pass',lang('password'),'required|validarPassword');            
        if ( $this->form_validation->run() == FALSE ){
            $this->load->view('welcome_login');                   
        } else {
            $data_post['usuario'] = $this->input->post( 'usuario' );
            $data_post['pass'] = md5($this->input->post( 'pass' ));
            $data_post['trabajaOffline']=$this->input->post( 'trabajaOffline' );
            $validar = $this->Model_usuario->getUsuarioSession($data_post);
            if($validar==false){
                $data['respuesta'] =lang('usuario_password_incorrecta');
                $this->load->view('welcome_login',$data);
            } else {

                if ($validar[0]["estadofilial"] == "activa"){                      
                    $sePuedeCrearSession = $this->validarArraySession($validar);
                    if ($sePuedeCrearSession['codigo']){
                        $this->crearSession($validar);                            
                        $session = $this->session->all_userdata();                       
                        $this->Model_usuario->insertar_historico_session($session);
                        
                        if($validar["filiales"] != false) {
                            $data['filiales_usu'] = $validar['filiales'];
                            //$this->load->view('elegir_filial',$data);
                        }       
                        else {    
                            //redirect('dashboard');
                        }
                        redirect('dashboard');
                    } else {
                        $data['errores'] = $sePuedeCrearSession['errores'];
                        $this->load->view('errores_inicializacion',$data);
                    }                    
                } else {                            
                    $data['respuesta'] =lang('filial_temporalmente_fuera_de servicio');
                    $this->load->view('welcome_login',$data);   
                }
            }
        }   
    }
        
    public function frm_recuperarPassword(){
        $this->load->view('frm_recuperarPassword');
    }
        
    public function recuperarPassword(){
        $email = $this->input->post('email');
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email',lang('email'),'required|valid_email');
        $resultado='';
        $emailUsuarioValido = $this->Model_usuario->emailUsuarioValido($email);           
        if ($this->form_validation->run() == FALSE ){                
            $resultado = array(
                "codigo"=>0,
                "msgerror"=>''
            );
        } else {
            if(count($emailUsuarioValido) >0){                    
                $datos = $this->Model_usuario->recuperarPassword($email);
                $resultado = array(
                    "codigo"=>1,
                    "msgerror"=>0,
                    "datos"=>$datos
                );
            } else {                    
                $resultado = array(
                    "codigo"=>0,
                    "msgerror"=>'El email no corresponde a un usuario del sistema'
                );
            }                
        }

        if($resultado['codigo']==0){
             $this->load->view('frm_recuperarPassword',$resultado);
        }else{
            redirect('login');
        }
    }
        
    public function cambiarPassword(){
        $data['hash'] = $this->input->get('code');            
        $this->load->view('cambiarPassword',$data);
    }
       
    public function guardarCambioPassword(){
        $password = $this->input->post('password');
        $password2 = $this->input->post('password2');            
        $hash = $this->input->post('hash');
        $this->load->library('form_validation');
        $respuesta ='';
        $usuario = $this->Model_usuario->retornoUsuarioHash($hash);
        $data['hash'] = $hash;
        $data['respuesta']= '';            
        if(count($usuario) > 0){
            $this->form_validation->set_rules('password',lang('password'),'validarPassword');
            $this->form_validation->set_rules('password2',lang('password'),'validarPassword');
            if($this->form_validation->run() == false){
               $respuesta=array(
                         'codigo'=>0,
                    );                        
                $data['respuesta'] = $respuesta;                    
            } else {                    
                if($password == $password2){
                     $respuesta = $this->Model_usuario->guardarCambioContraseña($password, $hash, $usuario);
                     $data['respuesta']=$respuesta;
                }else{
                    $respuesta=array(
                        'codigo'=>0,
                        'msgerrors'=>lang('password_desiguales')
                    );                        
                    $data['respuesta']=$respuesta;                        
                }
            }            
        } else {              
            $respuesta=array(
                'codigo'=>0,
                'msgerrors'=>lang('hash_sin_usuario')
            );                
            $data['respuesta']=$respuesta;              
        }

        if($data['respuesta']['codigo'] == 0){
            $this->load->view('cambiarPassword',$data);
        } else {
            $resultado = array(
                "usuario"=>$usuario[0]['email'],
                "pass"=>  md5($password)
            );
            $session = $this->Model_usuario->getUsuarioSession($resultado);
            $this->crearSession($session);
            redirect('dashboard');
        }           
    }
        
    public function validarArraySession($arraySession){
        $retorno  = array('codigo'=>1,'errores'=>array());            
        $filial = $arraySession['filial'];            

        /*-------------------------------
         * MONEDA
         --------------------------------*/
        if( $filial['moneda']['id'] == '' || $filial['moneda']['simbolo'] =='' ){
            $retorno['codigo'] = 0;
            $retorno['errores'][] = lang('sin_moneda_configurada');
        }
        return $retorno;        
    }
}