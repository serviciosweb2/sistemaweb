<?php

function formatearFecha_mysql($fechaString,$codFilial=null,$len = true) {
    
    $ci = & get_instance();
    
    $session = $ci->session->userdata('filial');
    
    if( $session != false )
    {
        $region = $session['pais'];
    }
    else
    {// RESTful
         // nueva instancia de filial 
        $conexion = $ci->load->database($codFilial,true);
        $filial = new Vfiliales($conexion,$codFilial);
        $region = $filial->pais; 
    }
    
    $getFormato = '';

    switch ($region) 
    {
        

        default:
            $getFormato = "d/m/Y";
    }
    if (strlen($fechaString) > 10)
    {
        $getFormato .= " H:i:s";
    }
    $objetoFecha = DateTime::createFromFormat($getFormato, $fechaString);

//el formato ahora es acorde a mysql:
    if (strlen($fechaString) > 10 && $len == true)
    {
        $fechaRetorno = $objetoFecha->format("Y-m-d H:i:s");
    } 
    else 
    {
        $fechaRetorno = $objetoFecha->format("Y-m-d");
    }
    return $fechaRetorno;
}

function formatearFecha_pais($fechaString, $agregarHora = false,$codFilial=null) {
    
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    
    if( $session != false)
    {
        $region = $session['pais'];
        
    }
    else
    {//RESTful
        $conexion = $ci->load->database((string)$codFilial,true);
        $filial = new Vfiliales($conexion,$codFilial);
        $region = $filial->pais; 

    }
    
    $validado = true;

    $getFormato = '';
//el formato va acorde a la fecha que recibe la funcion 
    switch ($region) {
        case '1':
        case '2':
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
        case '8':
        case '9':
        case '10':            
            $getFormato = "d/m/Y";
            break;
        default:                        // si se accede desde un web services no hay session
            $getFormato = "d/m/Y";
            break;
    }

    $date = new DateTime($fechaString);    
    $fechaRetorno = date_format($date, $getFormato);
    if ($agregarHora){
        $fechaRetorno .= substr($fechaString, 10);
    }
    return $fechaRetorno;
}

function formatearFecha_descripciondia($nrodia) {
    $dia = '';
    switch ($nrodia) {
        case 0:
            $dia = lang('dia_lunes');

            break;
        case 1:
            $dia = lang('dia_martes');

            break;
        case 2:
            $dia = lang('dia_miercoles');

            break;
        case 3:
            $dia = lang('dia_jueves');

            break;
        case 4:
            $dia = lang('dia_viernes');

            break;
        case 5:
            $dia = lang('dia_sabado');

            break;
        case 6:
            $dia = lang('dia_domingo');

            break;

        default:
            $dia = lang('dia_sindia');
            break;
    }
    return $dia;
}

function getPrimerFechaHabil(CI_DB_mysqli_driver $conexion, $fechaMySQL){
    $fecha = strtotime($fechaMySQL);    
    $numDia =  date("w", $fecha);    
    $esFeriado = Vferiados::isFeriado($conexion, $fechaMySQL, false);    
    while ($numDia == 0 || $esFeriado){    
        $cambiaDia = false;
//        if ($numDia == 6){ // si es sabado
//            $fecha += 86400;
//            $numDia = 0;
//            $cambiaDia = true;
//        }
        if ($numDia == 0){ // si es domingo
            $fecha += 86400;
            $numDia = 1;
            $cambiaDia = true;
        }
        if (!$cambiaDia){
            $fecha += 86400;
            $numDia ++;
        }
        
        $fechaMySQL = date("Y-m-d", $fecha);
        $esFeriado = Vferiados::isFeriado($conexion, $fechaMySQL, false);
    }
    return $fechaMySQL;
}

function getFechaFormat(){
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    switch ($session['pais']) { // agregar los formatos para otros paises
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
        case 7:
        case 8:
        case 9:
            $formato = "%d/%m/%Y %H:%i:%s";
            break;

        default:
            $formato = "%d/%m/%Y %H:%i:%s";
            break;
    }
    return $formato;
}

function sumarMeses($mySQLFecha, $cantidadMeses){
    $fecha = strtotime($mySQLFecha);
    $fechaNueva = strtotime("$cantidadMeses month", $fecha);
    $fechaNueva = date("Y-m-d", $fechaNueva);
    return $fechaNueva;
}

function getFechaTextual($fechaMYSQL, $verHora = true, $verAnio = true){
    $fechats = strtotime($fechaMYSQL);
    $dia = date("d", $fechats);
    $mes = date("m", $fechats);
    $anio = date("Y", $fechats);
    $nombreDia = '';       
    $nombreMes = '';
    $nroDia = date('w', $fechats);
    if ($nroDia == 0)
        $nroDia = 7;
    $nombreDia = getDiaNombre($nroDia);
    $nombreMes = getMesNombre($mes, null);
    $hora = '';
    if ($verHora){
        $hh = date("H", $fechats);
        $mi = date("i", $fechats);
        $hora = "{$hh}:{$mi} HS";
    }
    $complemento = $verAnio ?  " de $anio $hora" : '';
    return "$nombreDia $dia de $nombreMes".$complemento;

} 

function getDiaNombre($nroDia){
    switch ($nroDia) {
        case 1:
            $responder = lang("dia_lunes");
            break;
        case 2:
            $responder = lang("dia_martes");
            break;
        case 3:
            $responder = lang("dia_miercoles");
            break;
        case 4:
            $responder = lang("dia_jueves");
            break;
        case 5:
            $responder = lang("dia_viernes");
            break;
        case 6:
            $responder = lang("dia_sabado");
            break;
        case 7:
            $responder = lang("dia_domingo");
            break;
        default:
            $responder = '';
            break;
    }
    return $responder;
}

function getMesNombre($nroMes){
    $meses = array(1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre');
    if (isset($meses[(integer) $nroMes])){
        return lang($meses[(integer) $nroMes]);
    } else {
        return $nroMes;
    }
}

function getMeses(){
    $meses = array(
            1 => lang('enero'), 
            2 => lang('febrero'), 
            3 => lang('marzo'), 
            4 => lang('abril'), 
            5 => lang('mayo'), 
            6 => lang('junio'),
            7 => lang('julio'), 
            8 => lang('agosto'), 
            9 => lang('septiembre'), 
            10 => lang('octubre'), 
            11 => lang('noviembre'), 
            12 => lang('diciembre')
        );
    return $meses;
}


function sumarDias($mySQLFecha, $cantidadDias){        
    $timestampInicio = strtotime($mySQLFecha);
    $timestampFin = $timestampInicio + (86400 * $cantidadDias);
    $fechaFin = date("Y-m-d H:i:s", $timestampFin);
    return $fechaFin;
}

function get_mascara_fecha($codFilial, $incluir_hora = false){
    $ci = & get_instance();
    $conexion = $ci->load->database("general", true);
    $myFilial = new Vfiliales($conexion, $codFilial);
    switch ($myFilial->pais) {
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
        case 7:
        case 8:
        case 9:
            if ($incluir_hora){
                $retorno = '%d/%m/%Y %H:%i:%s';
            } else {
                $retorno = '%d/%m/%Y';
            }
            break;

        case 10:
            if ($incluir_hora){
                $retorno = '%m/%d/%Y %H:%i:%s';
            } else {
                $retorno = '%m/%d/%Y';
            }
            break;
        
        default:
            if ($incluir_hora){
                $retorno = '%d/%m/%Y %H:%i:%s';
            } else {
                $retorno = '%d/%m/%Y';
            }
            break;
    }
    return $retorno;
}