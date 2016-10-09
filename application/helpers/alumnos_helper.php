<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function formatearNombre(&$arrAlumnos) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
        
    if ($apellidoPrimero == 1) {
        $apellido = array();
        foreach ($arrAlumnos as $key => $alumno) {
            $apellido[$key] = strtolower($arrAlumnos[$key]['apellido']);
            $arrAlumnos[$key]['nombreapellido'] = $arrAlumnos[$key]['apellido'] . $separador . ' ' . $arrAlumnos[$key]['nombre'];
        }
        array_multisort($apellido, SORT_ASC, $arrAlumnos);
    }
    if ($apellidoPrimero == 0) {
        $nombre = array();
        foreach ($arrAlumnos as $key => $alumno) {
            $nombre[$key] = strtolower($arrAlumnos[$key]['nombre']);
            $arrAlumnos[$key]['nombreapellido'] = $arrAlumnos[$key]['nombre'] . $separador . ' ' . $arrAlumnos[$key]['apellido'];
        }
        array_multisort($nombre, SORT_ASC, $arrAlumnos);
    }
}

function formatearNombreApellido($nombre, $apellido,$separador=null,$apellidoPrimero=null) {
    if($separador == null){
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');            
        $separador = $filial['nombreFormato']['separadorNombre'];
        $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
    } 
    if ($apellidoPrimero == 1) {
        $apellido = strtolower($apellido);
        if ($apellido == ""){
            $nombreapellido = $nombre;
        }
        else{ 
            $nombreapellido = $apellido . $separador . ' ' . $nombre;
        }
    }
    if ($apellidoPrimero == 0) {
        $nombre = strtolower($nombre);
        if ($apellido == ""){
            $nombreapellido = $nombre;
        }
        else{ 
            $nombreapellido = $nombre . $separador . ' ' . $apellido;
        }
    }
    return ucwords($nombreapellido);
}

function inicialesMayusculas($string){
   $retorno =strtolower($string);
    return ucwords($retorno);
}

function formatearNomApeQuery(){
    $ci = &get_instance();
    $filial = $ci->session->userdata('filial');
    $separador = $filial['nombreFormato']['separadorNombre'];
    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
    if($apellidoPrimero == 1){
        $nombreApellido = 'alumnos.apellido,"'.$separador.' ",alumnos.nombre';
    }
    if($apellidoPrimero == 0){
        $nombreApellido = 'alumnos.nombre,"'.$separador.' ",alumnos.apellido';
    }
    return $nombreApellido;
}

function formatearNomApeProf(){
    $ci = &get_instance();
    $filial = $ci->session->userdata('filial');
    $separador = $filial['nombreFormato']['separadorNombre'];
    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
    if($apellidoPrimero == 1){
        $nombreApellido = 'profesores.apellido,"'.$separador.' ",profesores.nombre';
    }
    if($apellidoPrimero == 0){
        $nombreApellido = 'profesores.nombre,"'.$separador.' ",profesores.apellido';
    }
    return $nombreApellido;
}

function formatearNombreColumnaAlumno(){
    $ci = &get_instance();
    $filial = $ci->session->userdata('filial');
    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];        
    if ($apellidoPrimero == 1) {
          $nombreapellido = lang('apellido_y_nombre');
     }
     if ($apellidoPrimero == 0) {
         $nombreapellido = lang('nombre_y_apellido');
     }
    return $nombreapellido;
}

function formatearNombreAspQuery(){
    $ci = &get_instance();
    $filial = $ci->session->userdata('filial');
    $separador = $filial['nombreFormato']['separadorNombre'];
    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
    if($apellidoPrimero == 1){
        $nombreApellido = 'aspirantes.apellido,"'.$separador.' ",aspirantes.nombre';
    }
    if($apellidoPrimero == 0){
        $nombreApellido = 'aspirantes.nombre,"'.$separador.' ",aspirantes.apellido';
    }
    return $nombreApellido;
}

function formatearNomApeResponQuery(){
    $ci = &get_instance();
    $filial = $ci->session->userdata('filial');
    $separador = $filial['nombreFormato']['separadorNombre'];
    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
    if($apellidoPrimero == 1){
        $nombreApellido = 'responsables.apellido,"'.$separador.' ",responsables.nombre';
    }
    if($apellidoPrimero == 0){
        $nombreApellido = 'responsables.nombre,"'.$separador.' ",responsables.apellido';
    }
    return $nombreApellido;
}

function formatearNombreUsuarioSistQuery(){
    $ci = &get_instance();
    $filial = $ci->session->userdata('filial');
    $separador = $filial['nombreFormato']['separadorNombre'];
    $apellidoPrimero = $filial['nombreFormato']['formatoNombre'];
    if($apellidoPrimero == 1){
        $nombreApellido = 'general.usuarios_sistema.apellido,"'.$separador.' ",general.usuarios_sistema.nombre';
    }
    if($apellidoPrimero == 0){
        $nombreApellido = 'general.usuarios_sistema.nombre,"'.$separador.' ",general.usuarios_sistema.apellido';
    }
    return $nombreApellido;
}
