<?php

function formatearCuotas($arrcuotasplan, $conceptos) {
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    $moneda = $session['moneda'];
    $cuotasplan = array();
    $salida = '';
    $id = 0;
    foreach ($arrcuotasplan as $codconcepto => $arrFinanciaciones) {
        foreach ($arrFinanciaciones as $codfinanciacion => $detalles) {

            if($arrcuotasplan[$codconcepto][$codfinanciacion]['limite_vigencia'] != 'sin_fecha_limite' && strtotime(explode('/',$arrcuotasplan[$codconcepto][$codfinanciacion]['fecha_vigencia'])[2].'-'.explode('/',$arrcuotasplan[$codconcepto][$codfinanciacion]['fecha_vigencia'])[1].'-'.explode('/',$arrcuotasplan[$codconcepto][$codfinanciacion]['fecha_vigencia'])[0]) < strtotime(date('Y-m-d')))
            {
                continue(1);
            }
            $cuotasplan[$id][$codconcepto]['concepto'] = $conceptos[$codconcepto];
            $nombrefinanciacion = $arrcuotasplan[$codconcepto][$codfinanciacion]['cantcuotas'] . ' ';
            $nombrefinanciacion.= $arrcuotasplan[$codconcepto][$codfinanciacion]['cantcuotas'] < 2 ? lang('formatearcuotas_cuota') : lang('formatearcuotas_cuotas');
            $nombrefinanciacion.= $arrcuotasplan[$codconcepto][$codfinanciacion]['nombre'] != null ? ' "' . $arrcuotasplan[$codconcepto][$codfinanciacion]['nombre'] . '"' : '';
            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['nombre'] = $nombrefinanciacion;
            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['limite_primer_cuota'] = $arrcuotasplan[$codconcepto][$codfinanciacion]['limite_primer_cuota'];
            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['fecha_limite'] = $arrcuotasplan[$codconcepto][$codfinanciacion]['fecha_limite'];
            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['cantcuotas'] = $arrcuotasplan[$codconcepto][$codfinanciacion]['cantcuotas'];
            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['total'] = $arrcuotasplan[$codconcepto][$codfinanciacion]['total'];
            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['fecha_hoy'] = $arrcuotasplan[$codconcepto][$codfinanciacion]['fecha_hoy'];

            if ($detalles['descuento'] != 0 && $detalles['descuento'] != '') {

                $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['nombre'].=' (' . lang('formatearcuotas_descuento') . ' ' . round($detalles['descuento'], 2) . '%)';
            }

//            if ($detalles['interes'] != 0 && $detalles['interes'] != '') {
//
//                $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['nombre'].=' (' . lang('formatearcuotas_interes') . ' ' . $detalles['interes'] . '%)';
//            }
            $cuotas = array();
            $valores = array();
            $final = lang('formatearcuotas_cuotas') . ': ';

            for ($i = 0; $i < count($detalles['detalle']); $i++) {
                $final.=' ' . $detalles['detalle'][$i]['nrocuota'] . '/' . $moneda['simbolo'] . $detalles['detalle'][$i]['valor'];
                $valores[] = $detalles['detalle'][$i]['valor'];
                $cuotas[$i] = lang('formatearcuotas_cuota') . ' ' . $detalles['detalle'][$i]['nrocuota'] . ' ' . $moneda['simbolo'] . $detalles['detalle'][$i]['valor'];
            }

            if (count(array_unique($valores)) == 1) {//solo tiene un valor de cuota
                $final = $detalles['cantcuotas'] . ' ' . lang('formatearcuotas_cuota') . '/s $' . $valores[0];
            }

            $cuotasplan[$id][$codconcepto]['financiaciones'][$codfinanciacion]['descripcion'] = $final;
        }
        $cuotasplan[$id][$codconcepto]['fecha'] = formatearFecha_pais(date("Y-m-d"));
        $id++;
    }

    return $cuotasplan;
}
