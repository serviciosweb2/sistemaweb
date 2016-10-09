<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of model_publicidad_campanas
 *
 * @author romario
 */
class Model_publicidad_campanas extends CI_Model {
    
    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }
    
    public function buscarCampanasPorCodFiliales($filiales, $origen = 'google') {
        $conexion = $this->load->database("general", true);
        return Vpublicidad_campanas::buscarCampanasPorCodFiliales($conexion, $filiales, $origen);
    }
    
    public function buscarCampanas($origen = 'google') {
        $conexion = $this->load->database("general", true);
        return Vpublicidad_campanas::buscarCampanas($conexion, $origen);
    }
    
    public function buscarCampanasPermitidasParaUsuario($codigo_usuario, $origen = 'google') {
        $conexion = $this->load->database("general", true);
        $this->load->helper("filial");
        $usuario = new Vusuarios_sistema($conexion, $codigo_usuario);
        
        if($usuario->email == 'a_milberg@hotmail.com' || strpos($usuario->email, '@iga-la.net') !== false || $usuario->email == 'admguarulhos@igabrasil.com') {
            $campanas = $this->buscarCampanas($origen);
        }  
        else {
            $codigos = buscarCodigoFilialesUsuario();
            if(empty($codigos)) {
                $codigos = array($usuario->cod_filial);
            }
            $campanas = $this->buscarCampanasPorCodFiliales($codigos, $origen);
        }
        
        return $campanas;
    }
    
    public function buscarCampanasCodPermitidasParaUsuario($codigo_usuario, $origen = 'google') {
        $campanas_cod = array();
        $conexion = $this->load->database("general", true);
        $usuario = new Vusuarios_sistema($conexion, $codigo_usuario);
        
        if($usuario->email == 'a_milberg@hotmail.com' || strpos($usuario->email, '@iga-la.net') !== false || $usuario->email == 'admguarulhos@igabrasil.com') {
            $campanas_cod = null;
        }
        else {
            $campanas = $this->buscarCampanasPermitidasParaUsuario($codigo_usuario, $origen);
            $campanas_cod[] = 0;
            foreach ($campanas as $campana) {
                $campanas_cod[] = $campana['codigo'];
            }
        }
        
        return $campanas_cod;
    }
}
