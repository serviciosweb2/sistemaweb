<?php

function formatearImporte($importe, $agregarSimbolo = true, $conexion = null, $cod_filial = null) {
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    if ($conexion == null) {
        $conexion = $ci->load->database($session['codigo'], true);
    }

    if ($cod_filial != null) {
        $objfilial = new Vfiliales($conexion, $cod_filial);
        $monedas = $objfilial->getMonedaCotizacion();
        $moneda = $monedas[0];
    } else {
        $moneda = $session['moneda'];
    }

    $separador = Vconfiguracion::getValorConfiguracion($conexion, null, 'SeparadorDecimal');

    $resultado = number_format($importe, 2, $separador, '');
    if ($agregarSimbolo)
        return $moneda['simbolo'] . ' ' . $resultado;
    else
    return $resultado;
}

function formatearPorcentajeIVA($importe) {
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    $conexion = $ci->load->database($session['codigo'], true);


    $separador = Vconfiguracion::getValorConfiguracion($conexion, null, 'SeparadorDecimal');
    $decimales = 2;

    $resultado = number_format($importe, $decimales, $separador, '');
    return '%' . '' . $resultado;
}

function desformatearImporte($importe) {
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    $moneda = $session['moneda']['simbolo'];
    $separador = $session['moneda']['separadorDecimal'];
    $importeSinMoneda = str_replace($moneda, ' ', $importe);
    $resultado = str_replace($separador, '.', $importeSinMoneda);
    return (float) $resultado;
}

function buscarCodigoFilialesUsuario() {
    $ci = & get_instance();
    $filiales = $ci->session->userdata('filiales');
    $datos = array();
    
    foreach ($filiales as $key => $filial) {
        $datos[] = $filial['cod_filial'];
    }
    
    return $datos;
}
