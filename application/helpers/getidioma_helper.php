<?php
//ESTA FUNCION ME RETORNA EL LENGUAJE DEL EXPLORADOR
function get_idioma(){
    $ci = & get_instance();
    $usuario = $ci->session->userdata('codigo_usuario');
    $languaje= $ci->session->userdata('idioma');
    
    $idioma = isset( $_SERVER["HTTP_ACCEPT_LANGUAGE"]) ? substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2) :"";
    if($languaje == ''){
        if($idioma == ''){
            $idioma = 'es';
        }else{
            $idioma;
            
        }
       
    }else{
        $idioma = $languaje;
        
    }
    
return $idioma;

}

