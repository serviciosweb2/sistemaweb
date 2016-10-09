<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_cuentas_google extends CI_Model {
    
    public function __construct($arg = null) {
        parent::__construct();
        if ($arg != null){
            $this->codigofilial = $arg["codigo_filial"];
        }
    }
    
    public function getGoogleAccount($idFilial){
        $conexion = $this->load->database("default", true);
        $myGoogleAccount = new cuentas_google($conexion, $idFilial);
        return $myGoogleAccount;
    }
 
    public function saveGoogleAccount($idFilial, $userName, $password, $baja){
        $conexion = $this->load->database("default", true);
        $myGoogleAccount = new cuentas_google($conexion, $idFilial);
        $myGoogleAccount->user = $userName;
        $myGoogleAccount->pass = $password;
        $myGoogleAccount->baja = $baja;
        return $myGoogleAccount->guardar($conexion);        
    }
    
    public function disableAccount($idFilial){
        $conexion = $this->load->database("default", true);
        $myGoogleAccount = new cuentas_google($conexion, $idFilial);
        return $myGoogleAccount->disableAccount($conexion);
    }
}
?>