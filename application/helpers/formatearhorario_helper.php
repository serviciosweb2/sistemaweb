<?php

function horario($h) {

    $retorno = ''; // declaro la unica variable que retorna esta funcion

    foreach ($h as $key => $value) {// entro en cada dia 
        $retorno.= lang('dia_' . $key); //$dias[$key].' '; // recupero el nombre del dia 


        if ($value['desde'] != '') {// si el horario esta vacio no lo tengo en cuenta
            // Formateo los horarios que ecuentra el if_:
            $retorno.= ' - ' . lang('DE') . ' ' . date('H:i', strtotime($value['desde'])); 
            $retorno.= ' ' . lang('A') . ' ' . date('H:i', strtotime($value['hasta'])) . ' ' . lang('HS'); 
        }
    }
    return $retorno; // SALIDA ya convertida en string 
}
