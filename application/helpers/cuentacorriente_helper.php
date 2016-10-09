<?php

function formatearCtaCte($conexion, &$arrCtaCte, $muestratotalcuotas = 1, $codFilial = null, $idioma = null) {
    // siwakawa - agrego formato descripcion para poder ordenar correctamente cuotas de ctacte  
    $ci = & get_instance();
    $ci->load->helper('filial');
    $session = $ci->session->userdata('filial');
    $moneda = $session['moneda'];
    if ($codFilial == null) {
        $moneda = $session['moneda'];
    } else {
        $filial = new Vfiliales($conexion, $codFilial);
        $moneda = array();
        $moneda['id'] = $filial->id_moneda;
    }

    if ($codFilial != null) {
        $objfilial = new Vfiliales($conexion, $codFilial);
        $monedas = $objfilial->getMonedaCotizacion();
        $moneda = $monedas[0];
    } else {
        $moneda = $session['moneda'];
    }
    for ($i = 0; $i < count($arrCtaCte); $i++) {
        $arrDetalle['descripcion'] = Vctacte::getDescripcion($conexion, $arrCtaCte[$i]['cod_alumno'], $arrCtaCte[$i]['nrocuota'], $arrCtaCte[$i]['cod_concepto'], $arrCtaCte[$i]['concepto'], $arrCtaCte[$i]['financiacion'], $arrCtaCte[$i]['codigo']);
        $arrDetalle['cod_concepto'] = $arrCtaCte[$i]['cod_concepto'];
        $arrCtaCte[$i]['descripcion'] = formatearDescripcion($arrDetalle, $muestratotalcuotas, $idioma);
        $arrCtaCte[$i]['fechavencSinFormatear'] = $arrCtaCte[$i]['fechavenc'];
        $arrCtaCte[$i]['fechavenc'] = $arrCtaCte[$i]['fechavenc'] == null ? '-' : formatearFecha_pais($arrCtaCte[$i]['fechavenc'], '', $codFilial);
        $arrCtaCte[$i]['importeformateado'] = isset($arrCtaCte[$i]['importe']) ? formatearImporte($arrCtaCte[$i]['importe'], false, $conexion) : '';
        $arrCtaCte[$i]['pagadoformateado'] = isset($arrCtaCte[$i]['pagado']) ? formatearImporte($arrCtaCte[$i]['pagado'], false, $conexion) : '';
        $saldo = isset($arrCtaCte[$i]['saldo']) ? $arrCtaCte[$i]['saldo'] : $arrCtaCte[$i]['importe'] - $arrCtaCte[$i]['pagado'];
        $arrCtaCte[$i]['nombreconcepto'] = lang($arrDetalle['descripcion']['nombreconcepto']);
        $arrCtaCte[$i]['saldoformateado'] = formatearImporte($saldo, false, $conexion, $codFilial);
        $arrCtaCte[$i]['saldocobrarformateado'] = isset($arrCtaCte[$i]['saldocobrar']) ? formatearImporte($arrCtaCte[$i]['saldocobrar'], false, $conexion) : '';
        $arrCtaCte[$i]['saldofaccobformateado'] = isset($arrCtaCte[$i]['saldofaccob']) ? formatearImporte($arrCtaCte[$i]['saldofaccob'], false, $conexion) : '';
        $arrCtaCte[$i]['saldofacturarformateado'] = isset($arrCtaCte[$i]['saldofacturar']) ? formatearImporte($arrCtaCte[$i]['saldofacturar'], false, $conexion) : '';
        $arrCtaCte[$i]['saldoNotaCredito'] = isset($arrCtaCte[$i]['saldoNotaCredito']) ? formatearImporte($arrCtaCte[$i]['saldoNotaCredito'], false, $conexion) : '';
        $arrCtaCte[$i]['simbolo_moneda'] = $moneda['simbolo'];
    }
}

function formatearDescripcion($detalle, $muestratotalcuotas = 1, $idioma = null) {
    $retorno = '';

    if ($idioma == null){
        $idioma = get_idioma();
    }
    $nombreconcepto = lang($detalle['descripcion']['nombreconcepto']) != '' ? lang($detalle['descripcion']['nombreconcepto']) : $detalle['descripcion']['nombreconcepto'];
//    echo "<pre>"; print_r($detalle); echo "</pre>"; die();
    switch ($detalle['cod_concepto']) {
        case 3:
            $valor = $detalle['descripcion']['rowMora'];
            $nombreconceptoMora = lang($valor['descripcion']['nombreconcepto']) != '' ? lang($valor['descripcion']['nombreconcepto']) : $valor['descripcion']['nombreconcepto'];
            $nombrecursoMora = isset($valor['descripcion']['nombrecurso'][$idioma]) && $valor['descripcion']['nombrecurso'][$idioma] != null ? $valor['descripcion']['nombrecurso'][$idioma] : '';
            $nombrecursoMora.= isset($valor['descripcion']['nbreperiodos']) ? $valor['descripcion']['nbreperiodos'] : '';
            $nrocuotaMora = $valor['descripcion']['nro_cuota'];
            $definicionMora = $nombreconceptoMora . ' ' . lang('formatearcuotas_cuota') . ' ' . $nrocuotaMora . ' ' . $nombrecursoMora;
            $retorno = $nombreconcepto . ' ' . $definicionMora . ' ' . '(' . lang('diasvencida') . ': ' . $detalle['descripcion']['dias_vencida'] . ')';
            break;

        case 1:
        case 5:
          //  print_r($detalle['descripcion']['nombrecurso']);

            $nombrecurso = $detalle['descripcion']['nombrecurso'][$idioma] != null ? $detalle['descripcion']['nombrecurso'][$idioma] : '';
            $nombrecurso.= $detalle['descripcion']['nbreperiodos'];
            $nrocuota = $detalle['descripcion']['nro_cuota'];
            $totalcuotas = $detalle['descripcion']['total_cuotas'];
            if (isset($detalle['descripcion']['clases_inscriptas'], $detalle['descripcion']['clases_inscriptas']['clases_disponibles'], $detalle['descripcion']['clases_inscriptas']['clases_inscriptas'])
                    && (int)($detalle['descripcion']['clases_inscriptas']['clases_disponibles']) > 0 && (int)($detalle['descripcion']['clases_inscriptas']['clases_inscriptas']) > 0
                    && ((int)($detalle['descripcion']['clases_inscriptas']['clases_inscriptas'])) < ((int)($detalle['descripcion']['clases_inscriptas']['clases_disponibles']))){
                $complementoClases = $detalle['descripcion']['clases_inscriptas']['clases_inscriptas']." ".lang("clases");
            } else {
                $complementoClases = '';
            }
            $retorno = $nombreconcepto . ': '. lang('formatearcuotas_cuota') . ' ' . $nrocuota;
            $retorno.= $muestratotalcuotas == 1 ? ' / ' . $totalcuotas . " " . $nombrecurso." ".$complementoClases : " " . $nombrecurso." ".$complementoClases;
            $retorno.= $detalle['descripcion']['financiacion'] != 1 ? ' -' . ($detalle['descripcion']['financiacion'] - 1) . '° ' . lang('formatearcuotas_refinanciado') . '-' : '';
            break;

        default:
            $nrocuota = $detalle['descripcion']['nro_cuota'];
            $totalcuotas = $detalle['descripcion']['total_cuotas'];
            $retorno = $nombreconcepto . ': ' . lang('formatearcuotas_cuota') . ' ' . $nrocuota;
            $retorno.= $muestratotalcuotas == 1 ? ' / ' . $totalcuotas . " " : " ";
            $retorno.= $detalle['descripcion']['financiacion'] != 1 ? ' -' . ($detalle['descripcion']['financiacion'] - 1) . '° ' . lang('formatearcuotas_refinanciado') . '-' : '';
            break;
    }
    return $retorno;
}