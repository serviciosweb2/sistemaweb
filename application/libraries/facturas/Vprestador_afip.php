<?php

/**
* Class Vprestador_afip
*
*Class  Vprestador_afip maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @version  $Revision: 1.1 $
* @access   private
*/
class Vprestador_afip //extends Tprestador_no_factura
{
    public static function ValidarFactura($conexion, $punto_venta, $objFactura, $cod_filial, $testing=true) {
        $facturante = new Vfacturantes($conexion, $punto_venta->cod_facturante);
        $tipo_factura = new Vtipos_facturas($conexion, $punto_venta->tipo_factura);
        $razon = $objFactura->getRazon();
        $tipo_doc = new Vdocumentos_tipos($conexion, $razon->tipo_documentos);

        // Consulto los impuestos correspondientes al comprobante.
        $impuestos = $objFactura->getImpuestosFactura();

        $imp_tot_conc   = 0;
        $imp_op_ex      = 0;
        $imp_iva        = 0;
        $imp_trib       = 0;
        $iva            = array();
        $tributos       = array();
        foreach ($impuestos as $imp) {
            if ($imp['tipo'] == 'IVA') {
                if ($imp['cod_afip'] == 1) {
                    $imp_tot_conc = $imp['total_calculo'];
                }elseif ($imp['cod_afip'] == 2) {
                    $imp_op_ex = $imp['total_calculo'];
                }else {
                    $iva[] = array(
                                   'Id'         => $imp['cod_afip'],
                                   'BaseImp'    => $imp['total_calculo'],
                                   'Importe'    => $imp['total']
                                   );
                    $imp_iva += $imp['total'];
                }
            }else {
                $tributos[] = array(
                                    'Id'        => $imp['cod_afip'],
                                    'BaseImp'   => $imp['total_calculo'],
                                    'Alic'      => $imp['porcentaje'],
                                    'Importe'   => $imp['total']
                               );
                $imp_trib += $imp['total'];
            }
        }

        // El importe neto no debe incluir lo exento. Por eso se resta.
        $imp_neto = $objFactura->getNeto() - $imp_op_ex;

        $solicitud = array(
                           'FeCAEReq' => array(
                                               'FeCabReq' => array(
                                                                   'CantReg'      => 1,
                                                                   'CbteTipo'     => $tipo_factura->cod_afip,
                                                                   'PtoVta'       => $punto_venta->prefijo
                                                                   ),
                                               'FeDetReq' => array(
                                                                   'FECAEDetRequest' => array(
                                                                                              'Concepto'     => 3,
                                                                                              'DocTipo'      => $tipo_doc->cod_afip,
                                                                                              'DocNro'       => (float)$razon->documento,
                                                                                              'CbteDesde'    => $punto_venta->nro,
                                                                                              'CbteHasta'    => $punto_venta->nro,
                                                                                              'CbteFch'      => preg_replace('|(\d{4})-(\d{2})-(\d{2})|', '$1$2$3', $objFactura->fecha),
                                                                                              'ImpTotal'     => $objFactura->total,
                                                                                              'ImpTotConc'   => $imp_tot_conc,
                                                                                              'ImpNeto'      => $imp_neto,
                                                                                              'ImpOpEx'      => $imp_op_ex,
                                                                                              'ImpIVA'       => $imp_iva,
                                                                                              'ImpTrib'      => $imp_trib,
                                                                                              'FchServDesde' => preg_replace('|(\d{4})-(\d{2})-(\d{2})|', '$1$2$3', $objFactura->fecha),
                                                                                              'FchServHasta' => preg_replace('|(\d{4})-(\d{2})-(\d{2})|', '$1$2$3', $objFactura->fecha),
                                                                                              'FchVtoPago'   => preg_replace('|(\d{4})-(\d{2})-(\d{2})|', '$1$2$3', $objFactura->fecha),
                                                                                              'MonId'        => 'PES',
                                                                                              'MonCotiz'     => 1
                                                                                              )
                                                                   )
                                               )
                           );
        if ($imp_iva > 0) {
            $solicitud['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'] = array('AlicIva' => $iva);
        }
        if ($imp_trib > 0) {
            $solicitud['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Tributos'] = array('Tributo' => $tributos);
        }
        
        // Solicito al facturante una conexión con el WebService AFIP.
        $wsfe = $facturante->getWebServiceAfip($testing);
        // Envió la solicitud de creación del comprobante.
        $comprobante = $wsfe->FECAESolicitar($solicitud);

        // Defino un seguimiento para el comprobante.
        $seguimiento = new Vseguimiento_afip($conexion);
        $seguimiento->cod_filial    = $cod_filial;
        $seguimiento->cod_factura   = $objFactura->getCodigo();
        $seguimiento->cod_sesion    = $facturante->cod_sesion;
        $seguimiento->fecha_hora    = preg_replace('|(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})|', '$1-$2-$3 $4:$5:$6', $comprobante->FECAESolicitarResult->FeCabResp->FchProceso);
        
        $response = $comprobante->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
        if ($comprobante->FECAESolicitarResult->FeCabResp->Resultado == 'A') {
            $seguimiento->estado = 'aprobado';
            $seguimiento->cae = $response->CAE;
            $seguimiento->vencimiento_cae = preg_replace('|(\d{4})(\d{2})(\d{2})|', '$1-$2-$3', $response->CAEFchVto);

            $objFactura->estado = 'habilitada';
            $objFactura->setPropiedad(Vfacturas::getPropiedadNumeroFactura(), $punto_venta->nro);

            $punto_venta->incrementarNumero();
        }else {
            $errores = array();
            if (property_exists($response, 'Observaciones')) {
                if (is_array($response->Observaciones->Obs)) {
                    foreach($response->Observaciones->Obs as $obs) {
                        $errores[] = $obs;
                    }
                }else {
                    $errores[] = $response->Observaciones->Obs;
                }
            }

            if (property_exists($comprobante->FECAESolicitarResult, 'Errors')) {
                if (is_array($comprobante->FECAESolicitarResult->Errors->Err)) {
                    foreach($comprobante->FECAESolicitarResult->Errors->Err as $err) {
                        $errores[] = $err;
                    }
                }else {
                    $errores[] = $comprobante->FECAESolicitarResult->Errors->Err;
                }
            }

            $seguimiento->estado = 'rechazado';
            $seguimiento->errores = json_encode($errores);

            $objFactura->estado = 'error';
        }

        $objFactura->guardarFacturas();
        $seguimiento->guardarSeguimiento_afip();
    }

    public static function AnularFactura($conexion, $punto_venta, $objFactura, $testing=true) {
        $facturante = new Vfacturantes($conexion, $punto_venta->cod_facturante);
        $tipo_factura = new Vtipos_facturas($conexion, $punto_venta->tipo_factura);
        $razon = $objFactura->getRazon();
        $tipo_doc = new Vdocumentos_tipos($conexion, $razon->tipo_documentos);

        // Consulto los impuestos correspondientes al comprobante.
        $impuestos = $objFactura->getImpuestosFactura();

        $imp_tot_conc   = 0;
        $imp_op_ex      = 0;
        $imp_iva        = 0;
        $imp_trib       = 0;
        $iva            = array();
        $tributos       = array();

        $respuesta = array();

        foreach ($impuestos as $imp) {
            if ($imp['tipo'] == 'IVA') {
                if ($imp['cod_afip'] == 1) {
                    $imp_tot_conc = $imp['total_calculo'];
                }elseif ($imp['cod_afip'] == 2) {
                    $imp_op_ex = $imp['total_calculo'];
                }else {
                    $iva[] = array(
                                   'Id'         => $imp['cod_afip'],
                                   'BaseImp'    => $imp['total_calculo'],
                                   'Importe'    => $imp['total']
                                   );
                    $imp_iva += $imp['total'];
                }
            }else {
                $tributos[] = array(
                                    'Id'        => $imp['cod_afip'],
                                    'BaseImp'   => $imp['total_calculo'],
                                    'Alic'      => $imp['porcentaje'],
                                    'Importe'   => $imp['total']
                               );
                $imp_trib += $imp['total'];
            }
        }

        // El importe neto no debe incluir lo exento. Por eso se resta.
        $imp_neto = $objFactura->getNeto() - $imp_op_ex;
        $fecha = date('Ymd');

        $solicitud = array(
                           'FeCAEReq' => array(
                                               'FeCabReq' => array(
                                                                   'CantReg'      => 1,
                                                                   'CbteTipo'     => $tipo_factura->cod_afip,
                                                                   'PtoVta'       => $punto_venta->prefijo
                                                                   ),
                                               'FeDetReq' => array(
                                                                   'FECAEDetRequest' => array(
                                                                                              'Concepto'     => 3,
                                                                                              'DocTipo'      => $tipo_doc->cod_afip,
                                                                                              'DocNro'       => (float)$razon->documento,
                                                                                              'CbteDesde'    => $punto_venta->nro,
                                                                                              'CbteHasta'    => $punto_venta->nro,
                                                                                              'CbteFch'      => $fecha,
                                                                                              'ImpTotal'     => $objFactura->total,
                                                                                              'ImpTotConc'   => $imp_tot_conc,
                                                                                              'ImpNeto'      => $imp_neto,
                                                                                              'ImpOpEx'      => $imp_op_ex,
                                                                                              'ImpIVA'       => $imp_iva,
                                                                                              'ImpTrib'      => $imp_trib,
                                                                                              'FchServDesde' => $fecha,
                                                                                              'FchServHasta' => $fecha,
                                                                                              'FchVtoPago'   => $fecha,
                                                                                              'MonId'        => 'PES',
                                                                                              'MonCotiz'     => 1
                                                                                              )
                                                                   )
                                               )
                           );
        if ($imp_iva > 0) {
            $solicitud['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'] = array('AlicIva' => $iva);
        }
        if ($imp_trib > 0) {
            $solicitud['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Tributos'] = array('Tributo' => $tributos);
        }

        $pto_vta_factura = new Vpuntos_venta($conexion, $objFactura->punto_venta);
        $tipo_factura_factura = new Vtipos_facturas($conexion, $pto_vta_factura->tipo_factura);
        $solicitud['FeCAEReq']['FeDetReq']['FECAEDetRequest']['CbtesAsoc'] = array(
                                                                                   'CbteAsoc' => array(
                                                                                                       'Tipo'   => $tipo_factura_factura->cod_afip,
                                                                                                       'PtoVta' => $pto_vta_factura->prefijo,
                                                                                                       'Nro'    => $objFactura->getPropiedad(Vfacturas::getPropiedadNumeroFactura())
                                                                                                       )
                                                                                   );
        // Solicito al facturante una conexión con el WebService AFIP.
        $wsfe = $facturante->getWebServiceAfip($testing);
        // Envió la solicitud de creación del comprobante.
        $comprobante = $wsfe->FECAESolicitar($solicitud);

        $response = $comprobante->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
        if ($comprobante->FECAESolicitarResult->FeCabResp->Resultado == 'A') {
            $objFactura->baja();
            $punto_venta->incrementarNumero();

            $respuesta['cae']               = $response->CAE;
            $respuesta['vencimiento_cae']   = preg_replace('|(\d{4})(\d{2})(\d{2})|', '$1-$2-$3', $response->CAEFchVto);
        }else {
            $errores = array();
            if (property_exists($response, 'Observaciones')) {
                if (is_array($response->Observaciones->Obs)) {
                    foreach($response->Observaciones->Obs as $obs) {
                        $errores[] = $obs;
                    }
                }else {
                    $errores[] = $response->Observaciones->Obs;
                }
            }

            if (property_exists($comprobante->FECAESolicitarResult, 'Errors')) {
                if (is_array($comprobante->FECAESolicitarResult->Errors->Err)) {
                    foreach($comprobante->FECAESolicitarResult->Errors->Err as $err) {
                        $errores[] = $err;
                    }
                }else {
                    $errores[] = $comprobante->FECAESolicitarResult->Errors->Err;
                }
            }
            
            if (!empty($errores)) {
                $respuesta['errores'] = $errores;
            }
        }
        
        return $respuesta;
    }
}
