<?php

/**
 * Model_facturas
 * 
 * Description...
 * 
 * @package model_facturas
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_facturas extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    /**
     * retorna un objeto Factura.
     * @access public
     * @param array $arrFiltros filtros del control
     * @return array de respuesta datatable
     */
    public function listarFacturasDataTable($arrFiltros, $estado = null, $fechaDesde = null, $fechaHasta = null, $medio = null,
            $tipoFactura = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        $condiciones = array();
        if ($estado != null) {
            $condiciones["facturas.estado"] = $estado;
        }
        if ($fechaDesde != null){
            $condiciones["facturas.fecha >="] = $fechaDesde;
        }
        if ($fechaHasta != null){
            $condiciones["facturas.fecha <="] = $fechaHasta;
        }
        if ($medio != null){
            $condiciones["general.puntos_venta.medio"] = $medio;
        }
        if ($tipoFactura != null){
            $condiciones["general.puntos_venta.tipo_factura"] = $tipoFactura;
        }        
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "razones_sociales.razon_social" => $arrFiltros["sSearch"],
                "general.tipos_facturas.factura" => $arrFiltros["sSearch"],
                "facturas.total" => $arrFiltros["sSearch"],
                "nrofact" => $arrFiltros['sSearch'],
                "documento" => $arrFiltros['sSearch']
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" && $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" && $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vfacturas::listarFacturasDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, false, null, $condiciones);
        $contar = Vfacturas::listarFacturasDataTable($conexion, $arrCondiciones, "", "", TRUE, null, $condiciones);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        $i = 0;
        $ci = & get_instance();
        $session = $ci->session->userdata('filial');
        $moneda = $session['moneda'];
        $simboloMoneda = $moneda['simbolo'];
        foreach ($datos as $row) {
            $facturante = new Vfacturantes($conexion, $row['cod_facturante']);
            $razonsocial = $facturante->getRazonSocial();
            $ptovta = new Vpuntos_venta($conexion, $row['cod_punto_venta']);
            $medio = $ptovta->medio == 'electronico' ? 'e' : '';
            $nombreTalonario = isset($razonsocial[0]['razon_social']) ? $razonsocial[0]['razon_social'] : '-';
            $rows[$i][] = $row["codigo"];
            $rows[$i][] = $row["codigo"];
            $rows[$i][] = formatearFecha_pais($row["fecha"]);
            $rows[$i][] = $row["nrofact"];
            $rows[$i][] = $row['punto_venta'];
            $rows[$i][] = $nombreTalonario;
            $rows[$i][] = $row["factura"] . $medio;
            $rows[$i][] = $row["razon_social"];
            $rows[$i][] = $row['documento'];
            $rows[$i][] = formatearImporte($row["total"]);
            $rows[$i][] = $row['estado'];
            $rows[$i][] = $row['email'];
            //Ticket 4658 -mmori- quito validacion para el medio
            $rows[$i][] = $row['estado'] == 'habilitada' ? '1' : '0';
            $rows[$i][] = $row["total"];
            $rows[$i][] = $simboloMoneda;
            $rows[$i][] = $ptovta->webservice == 1 && $session['pais'] == 1 ? true : false;
            $i++;
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getTiposFacturasFacturante($codFacturante, $puntoVenta = null, $usuario = null, $pais = null, $soloPuntosVentaHavilitados = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myFacturante = new Vfacturantes($conexion, $codFacturante);
        $arrTipoFacturas = $myFacturante->getTiposFacturas($puntoVenta, $usuario, null, null, $soloPuntosVentaHavilitados);
        $arrResp = array();
        foreach ($arrTipoFacturas as $key => $tipoFactura) {
            $myPuntoVenta = new Vpuntos_venta($conexion, $tipoFactura['cod_punto_venta']);
            if ($pais == 2) {
                if ($tipoFactura['codigo'] == 15 || $tipoFactura['codigo'] == 16) {        // facturas que utilizan otro porcentaje                    
                    $porcentajeFacturar = $myPuntoVenta->getPorcentajeFacturar($this->codigo_filial);
                } else {
                    $porcentajeFacturar = 100;  // cambiar cuando exista algun metodo de facturacion no definido aun
                }
            } else {
                $porcentajeFacturar = 100;  // cambiar cuando exista algun metodo de facturacion no definido aun
            }
            $arrResp[$key]['porcentaje'] = $porcentajeFacturar;
            $arrResp[$key]['prefijo'] = $myPuntoVenta->prefijo;
            $arrResp[$key]['codigo'] = $tipoFactura['cod_punto_venta'];
            $nombreFactura = lang('FACTURA') . ' ' . $tipoFactura['factura'] . ' - ' . lang('numero') . ' ' . $tipoFactura['nro'] . " [{$myPuntoVenta->prefijo}]";
            if ($porcentajeFacturar <> 100)
                $nombreFactura .= " ($porcentajeFacturar %)";
            $arrResp[$key]['factura'] = $nombreFactura;
            $arrResp[$key]['tipo'] = $tipoFactura['codigo'];
        }
        return $arrResp;
    }

    public function getTiposFacturaHabilitadas($cod_facturante, $cod_razon_alumno, $punto_venta, $usuario, $pais = null, $soloPuntosVentaHabilitados = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrtiposfactura = array();
        $myRazonSocial = new Vrazones_sociales($conexion, $cod_razon_alumno);
        $myFacturante = new Vfacturantes($conexion, $cod_facturante);
        $tiposFactura = $myFacturante->getTiposFacturas($punto_venta, $usuario, $myRazonSocial->condicion, null, $soloPuntosVentaHabilitados);
        $conexion->join("general.puntos_venta", "general.puntos_venta.codigo = facturas.punto_venta");
        $condiciones = array('cod_facturante' => $cod_facturante);
        $ordenar = array(array('campo' => 'codigo', 'orden' => 'desc'));
        $ultimafactura = Vfacturas::listarFacturas($conexion, $condiciones, array(0, 1), $ordenar);
        $i = 0;
        $orden = array();
        foreach ($tiposFactura as $value) {
            $myPuntoVenta = new Vpuntos_venta($conexion, $value['cod_punto_venta']);
            if ($pais == 2) {
                if ($value['codigo'] == 15 || $value['codigo'] == 16) {
                    $porcentajeFacturar = $myPuntoVenta->getPorcentajeFacturar($this->codigo_filial);
                } else {
                    $porcentajeFacturar = 100;  // cambiar cuando exista algun metodo de facturacion no definido aun
                }
            } else {
                $porcentajeFacturar = 100;  // cambiar cuando exista algun metodo de facturacion no definido aun
            }
            $arrtiposfactura[$i]['porcentaje'] = $porcentajeFacturar;
            $arrtiposfactura[$i]['prefijo'] = $myPuntoVenta->prefijo;
            $arrtiposfactura[$i]['codigo'] = $value['cod_punto_venta'];
            $medio = $myPuntoVenta->medio == 'electronico' ? 'e' : '';
            $nombreFactura = lang('FACTURA') . ' ' . $value['factura'] . $medio . ' - ' . lang('numero') . ' ' . $value['nro'] . " [{$myPuntoVenta->prefijo}]";
            if ($porcentajeFacturar <> 100)
                $nombreFactura .= " ($porcentajeFacturar %)";
            $arrtiposfactura[$i]['factura'] = $nombreFactura;
            $arrtiposfactura[$i]['tipo'] = $value['codigo'];
            if (count($ultimafactura) > 0) {
                $orden[$i] = $value['cod_punto_venta'] == $ultimafactura[0]['punto_venta'] ? '1' : '0';
            }
            $i++;
        }
        if (count($ultimafactura) > 0) {
            array_multisort($orden, SORT_DESC, $arrtiposfactura);
        }
        return $arrtiposfactura;
    }

    public function getRazonSocial($cod_factura) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $factura = new Vfacturas($conexion, $cod_factura);
        return $factura->getRazon();
    }

//    public function facturar($conexion, $factura) {
//        $objFactura = new Vfacturas($conexion);
//        $objFactura->facturar($factura['total'], $factura['punto_venta'], $factura['cod_usuario'], $factura['renglones'], $factura['codalumno'], $factura['fecha'], $factura['codrazsoc']);
//        return $objFactura->getCodigo();
//    }

    public function getMotivosBaja() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $facturaEstado = new Vfacturas_estado_historicos($conexion);
        return $facturaEstado->getMotivos();
    }

    public function validarCambioEstado($codfactura) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $mensaje = '';
        $retorno = array();
        $retorno['respuesta'] = validaciones::validarCambioEstadoFactura($conexion, $codfactura, $mensaje);
        $retorno['mensaje'] = $mensaje;
        return $retorno;
    }

    public function bajaFactura($cambioestado) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $factura = new Vfacturas($conexion, $cambioestado['cod_factura']);

        if ($factura->estado == Vfacturas::getEstadoHabilitado() || $factura->estado == Vfacturas::getEstadoPendiente()) {
            $respuesta = null;
            $conexion->trans_begin();

            $filial = new Vfiliales($conexion, $this->codigo_filial);
            $pto_vta_factura = new Vpuntos_venta($conexion, $factura->punto_venta);
            if ($filial->pais == 1 && $pto_vta_factura->medio == Vpuntos_venta::getMedioElectronico()) {
                $respuesta = array('codigo' => 0);
                $conexion_gral = $this->load->database('', true);
                $tipo_factura_factura = new Vtipos_facturas($conexion_gral, $pto_vta_factura->tipo_factura);

                $condiciones_tipos = array(
                                     'tipo'         => $tipo_factura_factura->tipo,
                                     'comprobante'  => 'nota_credito',
                                     
                                     );
                $lista_tipos = Vtipos_facturas::listarTipos_facturas($conexion_gral, $condiciones_tipos);

                if (isset($lista_tipos[0]['codigo'])) {
                    $condiciones_ptos = array(
                                         'cod_facturante'   => $pto_vta_factura->cod_facturante,
                                         'medio'            => Vpuntos_venta::getMedioElectronico(),
                                         'tipo_factura'     => $lista_tipos[0]['codigo'],
                                          'prefijo' => $pto_vta_factura->prefijo
                            
                                         );
                    $lista_ptos = Vpuntos_venta::listarPuntos_venta($conexion_gral, $condiciones_ptos);
                    
                    if (isset($lista_ptos[0]['codigo'])) {
                        $pto_vta_nota_cred = new Vpuntos_venta($conexion_gral, $lista_ptos[0]['codigo']);
                        $respuesta_afip = Vprestador_afip::AnularFactura($conexion_gral, $pto_vta_nota_cred, $factura, $this->config->item('ws_afip_testing'));
                        
                        if (isset($respuesta_afip['errores'])) {
                            $respuesta['codigo'] = 0;
                            $respuesta['msgerror'] = '';
                            foreach ($respuesta_afip['errores'] as $err) {
                                $respuesta['msgerror'] .= "({$err->Code}) {$err->Msg}<br />";
                            }
                        }else {
                            $respuesta['codigo'] = 1;
                            $respuesta['texto'] = "Nota de Crédito:<br />CAE: {$respuesta_afip['cae']}<br />Vencimiento CAE: {$respuesta_afip['vencimiento_cae']}";
                        }
                    }else {
                        $respuesta['msgerror'] = 'No existe el punto de venta.';
                    }
                }else {
                    $respuesta['msgerror'] = 'No se encontró el tipo factura.';
                }
            }else {
                $factura->baja();
            }

            if ($factura->estado == Vfacturas::getEstadoInhabilitado()) {
                if ($cambioestado['cobro']) {
                    $arrCobro = $factura->getCobroAsociado();
                    $objCobro = new Vcobros($conexion, $arrCobro[0]['cod_cobro']);
                    $objCobro->anularCobro(3, '', $cambioestado['cod_usuario']);
                }
                $estadoHistoricos = new Vfacturas_estado_historicos($conexion);
                $arrGuardarEstadoHistorico = array(
                    'cod_factura' => $factura->getCodigo(),
                    'estado' => $factura->estado,
                    'motivo' => Vfacturas_estado_historicos::getMotivoId($cambioestado['motivo']),
                    'fecha_hora' => date("Y-m-d H:i:s"),
                    'comentario' => $cambioestado['comentario'],
                    'cod_usuario' => $cambioestado['cod_usuario']
                );
                $estadoHistoricos->setFacturas_estado_historicos($arrGuardarEstadoHistorico);
                $estadoHistoricos->guardarFacturas_estado_historicos();
            }

            $estadoTran = $conexion->trans_status();
            if ($estadoTran === false) {
                $conexion->trans_rollback();
            } else {
                $conexion->trans_commit();
            }
            if (is_null($respuesta)) {
                return class_general::_generarRespuestaModelo($conexion, $estadoTran);
            }else {
                return $respuesta;
            }
        } else {
            $arrResp = array("codigo" => 0, "msgerror" => lang("no_se_puede_inhabilitar_la_factura_por_encontrarse_en_estado") . " " . lang($factura->estado));
            return $arrResp;
        }
    }

    public function altaFactura($cambioestado) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $factura = new Vfacturas($conexion, $cambioestado['cod_factura']);
        $factura->alta();
        $estadoHistoricos = new Vfacturas_estado_historicos($this->oConnection);
        $arrGuardarEstadoHistorico = array(
            'cod_factura' => $factura->getCodigo(),
            'baja' => $factura->anulada,
            'motivo' => '',
            'fecha_hora' => date("Y-m-d H:i:s"),
            'comentario' => '',
            'cod_usuario' => $cambioestado['cod_usuario']
        );
        $estadoHistoricos->setFacturas_estado_historicos($arrGuardarEstadoHistorico);
        $estadoHistoricos->guardarFacturas_estado_historicos();
        $estadoTran = $conexion->trans_status();
        if ($estadoTran === false) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadoTran);
    }

    public function guardarFactura($factura) {
        //facturacion segmentada
        $arrConf = array('codigo_filial' => $this->codigo_filial);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $conexion = $this->load->database($this->codigo_filial, true);

        if(empty($factura['total_facturar'])) {
            $arrValores = array();

            foreach ($factura['checkctacte'] as $idCtacte) {
                $ctacte = new Vctacte($conexion, $idCtacte);
                $arrValores[] = $ctacte->getSaldoFacturar();
            }
            $factura['total_facturar'] = $this->calcularTotal($arrValores);
        }

        $facturacion_segmentada = $this->Model_configuraciones->getValorConfiguracion('', 'facturacionSegmentada', '');
        if($facturacion_segmentada) {
            $monto_segmento = ($this->Model_configuraciones->getValorConfiguracion('', 'montoSegmento', '')-0.01);
            if (!empty($monto_segmento) && $monto_segmento < $factura['total_facturar']) {
                $facturas = array();
                $cantSegmentos = ceil($factura['total_facturar'] / $monto_segmento);
                $segmento = round($factura['total_facturar']/$cantSegmentos, 2);
                for($i = 1; $i <= $cantSegmentos; $i++) {
                    $facturas[$i] = $factura;
                    if($i == $cantSegmentos) {//al redondear, se pierde unos pocos centavos, esos centavos se añaden la última factura
                        $facturas[$i]['total_facturar'] = $segmento + ($factura['total_facturar'] - $segmento*$cantSegmentos);
                    }
                    else {
                        $facturas[$i]['total_facturar'] = $segmento;
                    }
                }
            }
        }
        if (empty($facturas)) {
            $facturas = array($factura);
            unset($factura);
        }

        $arrResp = array();
        foreach ($facturas as $factura) {
            $myAlumno = new Valumnos($conexion, $factura['alumno']);
            $arrRazones = $myAlumno->getRazonesAlumno(array("razones_sociales.codigo" => $factura['codrazsoc']));
            if (count($arrRazones) == 0) {
                $arrResp['codigo'] = 0;
                $arrResp['msgerror'] = lang("la_razon_social_seleccionada_no_corresponde_al_alumno_indicado");
            } else {
                $conexion->trans_start();
                $ctasctes = array();
                $rowsCtaCte = array();
                $codctacte = array();
                foreach ($factura['checkctacte'] as $value) {
                    $codctacte[] = $value;
                }
                if (count($codctacte) != 0) {
                    $rowsCtaCte = Vctacte::getCtaCteAFacturar($conexion, null, null, $codctacte);
                }
                $cuentas = array();
                foreach ($rowsCtaCte as $value) {
                    foreach ($codctacte as $key => $cta) {
                        if ($cta == $value['codigo']) {
                            $cuentas[$key] = $value;
                        }
                    }
                }
                $totalFacturar = $factura['total_facturar'];
                $restaFacturar = $totalFacturar;
                foreach ($cuentas as $value) {
                    $saldoCtacte = $value['saldofacturar'];
                    $restaFacturar = $restaFacturar - $saldoCtacte;
                    $importeRenglon = ($restaFacturar > 0) ? $saldoCtacte : $totalFacturar;
                    $totalFacturar = $totalFacturar - $importeRenglon;
                    if ($importeRenglon > 0) {
                        $ctasctes[] = array('id' => $value['codigo'], 'valor' => $importeRenglon);
                    }
                }
                $arrPuntosVenta = $factura['puntos_venta'];
                $arrPorcentajes = array();
                $porcentajesFijo = 0;
                $cantidadPorcentajeMovil = 0;
                foreach ($arrPuntosVenta as $puntoVenta){
                    $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
                    $porcentaje = $myPuntoVenta->getPorcentajeFacturar($this->codigo_filial);
                    if ($porcentaje <> ''){
                        $porcentajesFijo += $porcentaje;
                    } else {
                        $cantidadPorcentajeMovil ++;
                    }
                    $arrPorcentajes[$puntoVenta] =$porcentaje;
                }
                if ($cantidadPorcentajeMovil > 0){
                    $porcentajesMoviles = (100 - $porcentajesFijo) / $cantidadPorcentajeMovil;
                    foreach ($arrPorcentajes as $key => $value){
                        if ($value == ''){
                            $arrPorcentajes[$key] = $porcentajesMoviles;
                        }
                    }
                }
                $porcentaje = 0;
                foreach ($arrPorcentajes as $value){
                    $porcentaje += $value;
                }
                if ($porcentaje < 99 || $porcentaje > 101){
                    $resultado = array(
                        'codigo' => '0',
                        'msgerror' => lang('la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100'),
                        'errNo' => '',
                    );
                    echo json_encode($resultado);
                    die();
                }
                $arrResp = array();
                $imprimirSobreResponse = false;
                $filial = new Vfiliales($conexion, $this->codigo_filial);
                foreach ($arrPuntosVenta as $puntoVenta) {
                    $totalfactura = 0;
                    $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
                    $estado = $myPuntoVenta->utilizaWebServices() || ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) ? Vfacturas::getEstadoPendiente() : null;
                    $porcentajeFacturar = $arrPorcentajes[$puntoVenta];
                    $factura['renglones'] = array();
                    foreach ($ctasctes as $rowrenglon) {
                        $renglon = new Vfacturas_renglones($conexion);
                        $renglon->cod_ctacte = $rowrenglon['id'];
                        $renglon->importe = $rowrenglon['valor'] * $porcentajeFacturar / 100;
                        $factura['renglones'][] = $renglon;
                        $totalfactura = $totalfactura + $renglon->importe;
                    }
                    $factura['total'] = $totalfactura;
                    $factura['codalumno'] = null;
                    $objFactura = new Vfacturas($conexion);
                    $objFactura->facturar($factura['total'], $myPuntoVenta->getCodigo(), $factura['cod_usuario'], $factura['renglones'], $factura['codalumno'], $factura['fecha'], $factura['codrazsoc'], $estado, $filial->pais);

                    if ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) {
                        $conexion_gral = $this->load->database('', true);
                        Vprestador_afip::ValidarFactura($conexion_gral, $myPuntoVenta, $objFactura, $this->codigo_filial, $this->config->item('ws_afip_testing'));
                    }

                    if ($objFactura->estado == null || $objFactura->estado == Vfacturas::getEstadoHabilitado()) {
                        $arrResp[] = $objFactura->getCodigo();
                        $imprimirSobreResponse = true;
                    }
                }
                $estadotran = $conexion->trans_status();
                $conexion->trans_complete();
                $arrResp = class_general::_generarRespuestaModelo($conexion, $estadotran, array("factura" => $arrResp, "imprimir" => $imprimirSobreResponse ? 1 : 0));
            }
        }
        return $arrResp;
    }

    public function guardarFacturaCobro($datosfactura) {
        //facturacion segmentada
        $arrConf = array('codigo_filial' => $this->codigo_filial);
        $this->load->model("Model_configuraciones", "", false, $arrConf);
        $facturacion_segmentada = $this->Model_configuraciones->getValorConfiguracion('', 'facturacionSegmentada', '');
        if($facturacion_segmentada) {
            $monto_segmento = ($this->Model_configuraciones->getValorConfiguracion('', 'montoSegmento', '')-0.01);
            if (!empty($monto_segmento) && $monto_segmento < $datosfactura['facturar']['total-general']) {
                $datosfacturas = array();
                $datosfactura['facturar']['total-general'] = str_replace(',', '.', $datosfactura['facturar']['total-general']);
                $cantSegmentos = ceil($datosfactura['facturar']['total-general'] / $monto_segmento);
                $segmento = round($datosfactura['facturar']['total-general']/$cantSegmentos, 2);
                for($i = 1; $i <= $cantSegmentos; $i++) {
                    $datosfacturas[$i] = $datosfactura;
                    if($i == $cantSegmentos) {//al redondear, se pierde unos pocos centavos, esos centavos se añaden la última factura
                        $datosfacturas[$i]['facturar']['total-general'] = $segmento + ($datosfactura['facturar']['total-general'] - $segmento*$cantSegmentos);
                    }
                    else {
                        $datosfacturas[$i]['facturar']['total-general'] = $segmento;
                    }
                }
            }
        }
        if (empty($datosfacturas)) {
            $datosfacturas = array($datosfactura);
            unset($datosfactura);
        }

        $conexion = $this->load->database($this->codigo_filial, true);
        $arrResp = array();
        foreach ($datosfacturas as $datosfactura) {
            $myAlumno = new Valumnos($conexion, $datosfactura['facturar']['alumno']);
            $arrRazones = $myAlumno->getRazonesAlumno(array("razones_sociales.codigo" => $datosfactura['facturar']['razon_social']));
            if (count($arrRazones) == 0) {
                $arrResp["codigo"] = 0;
                $arrResp['msgerror'] = lang("la_razon_social_seleccionada_no_corresponde_al_alumno_indicado");
            } else {
                $conexion->trans_start();
                $this->load->helper('cuentacorriente');
                $this->load->helper('filial');
                $factura = array(
                    'tipofact' => $datosfactura['facturar']['tipo_factura'],
                    'fecha' => $datosfactura['facturar']['fecha-factura'],
                    'codrazsoc' => $datosfactura['facturar']['razon_social'],
                    'cod_usuario' => $datosfactura['facturar']['cod_usuario'],
                    'facturante' => $datosfactura['facturar']['facturante'],
                    'codalumno' => null
                );
                $codctacte = array();
                foreach ($datosfactura['facturar']['checkctacte'] as $rowrenglon) {
                    $codctacte[] = $rowrenglon;
                }
                $rowsctacte = Vctacte::getCtaCteFacturarCobrar($conexion, null, null, $codctacte);
                foreach ($rowsctacte as $value) {
                    foreach ($codctacte as $key => $cta) {
                        if ($cta == $value['codigo']) {
                            $codctacte[$key] = $value;
                        }
                    }
                }
                $totalfactura = $datosfactura['facturar']['total-general'];
                $restafactura = $totalfactura;
                $mediopago = new Vmedios_pago($conexion, $datosfactura['cobro']['medio_pago']);
                $objmedio = $mediopago->getObjmedio($datosfactura['medioPago']);
                $cobro = new Vcobros($conexion);
                $cobro->guardarCobro(desformatearImporte($datosfactura['facturar']['total-general']), $datosfactura['cobro']['medio_pago'], Vcobros::getEstadoPendiente(), $datosfactura['facturar']['cod_alumno'], $datosfactura['facturar']['cod_usuario'], $datosfactura['cobro']['medio-caja'], null, $objmedio, $datosfactura['facturar']['fecha-factura']);
                $codigoCobro = $cobro->getCodigo();
                $arrFacturasResp = array();
                $imprimeSobreResponse = false;
                $arrPuntosVentas = is_array($datosfactura['facturar']['punto_venta']) ? $datosfactura['facturar']['punto_venta'] : array($datosfactura['facturar']['punto_venta']);
                $arrPuntosVentas = $datosfactura['facturar']['punto_venta'];
                $arrPorcentajes = array();
                $porcentajesFijo = 0;
                $cantidadPorcentajeMovil = 0;
                foreach ($arrPuntosVentas as $puntoVenta){
                    $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
                    $porcentaje = $myPuntoVenta->getPorcentajeFacturar($this->codigo_filial);
                    if ($porcentaje <> ''){
                        $porcentajesFijo += $porcentaje;
                    } else {
                        $cantidadPorcentajeMovil ++;
                    }
                    $arrPorcentajes[$puntoVenta] =$porcentaje;
                }
                if ($cantidadPorcentajeMovil > 0){
                    $porcentajesMoviles = (100 - $porcentajesFijo) / $cantidadPorcentajeMovil;
                    foreach ($arrPorcentajes as $key => $value){
                        if ($value == ''){
                            $arrPorcentajes[$key] = $porcentajesMoviles;
                        }
                    }
                }
                $porcentaje = 0;
                foreach ($arrPorcentajes as $value){
                    $porcentaje += $value;
                }
                if ($porcentaje < 99 || $porcentaje > 101){
                    $resultado = array(
                        'codigo' => '0',
                        'msgerror' => lang('la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100'),
                        'errNo' => '',
                    );
                    echo json_encode($resultado);
                    die();
                }
                $filial = new Vfiliales($conexion, $this->codigo_filial);
                foreach ($arrPuntosVentas as $puntoVenta) {
                    $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
                    $porcentaje = $arrPorcentajes[$puntoVenta];
                    $importeFactura = desformatearImporte($datosfactura['facturar']['total-general']);
                    $factura['total'] = $importeFactura * $porcentaje / 100;
                    $factura['punto_venta'] = $puntoVenta;
                    $estado = $myPuntoVenta->utilizaWebServices() || ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) ? Vfacturas::getEstadoPendiente() : null;
                    $saldoCtacte = 0;
                    $factura['renglones'] = array();
                    foreach ($codctacte as $rowrenglon) {
                        $saldoCtacte = $rowrenglon['saldofacturar'] <= $rowrenglon['saldocobrar'] ? $rowrenglon['saldofacturar'] : $rowrenglon['saldocobrar'];
                        $restafactura = $restafactura - $saldoCtacte;
                        $importeRenglon = ($restafactura > 0) ? $saldoCtacte : $totalfactura;
                        $totalfactura = $totalfactura - $importeRenglon;
                        if ($importeRenglon != 0) {
                            $renglon = new Vfacturas_renglones($conexion);
                            $renglon->cod_ctacte = $rowrenglon['codigo'];
                            $totRenglon = str_replace(',', '.', $importeRenglon) * $porcentaje / 100;
                            $renglon->importe = $totRenglon;
                            $factura['renglones'][] = $renglon;
                        }
                    }
                    $importeRenglon = '';
                    $objFactura = new Vfacturas($conexion);
                    $objFactura->facturar($factura['total'], $factura['punto_venta'], $factura['cod_usuario'], $factura['renglones'], $factura['codalumno'], $factura['fecha'], $factura['codrazsoc'], $estado, $filial->pais);

                    if ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) {
                        $conexion_gral = $this->load->database('', true);
                        Vprestador_afip::ValidarFactura($conexion_gral, $myPuntoVenta, $objFactura, $this->codigo_filial, $this->config->item('ws_afip_testing'));
                    }

                    $objFactura->asociarCobro($codigoCobro);
                    if ($objFactura->estado == null || $objFactura->estado == Vfacturas::getEstadoHabilitado()) {
                        $arrFacturasResp[] = $objFactura->getCodigo();
                        $imprimeSobreResponse = true;
                    }
                }
                $totalfactura2 = $importeFactura;
                $restafactura2 = $totalfactura2;
                foreach ($codctacte as $rowcta) {
                    $saldoCtacte2 = $rowcta['saldofacturar'] <= $rowcta['saldocobrar'] ? $rowcta['saldofacturar'] : $rowcta['saldocobrar'];
                    $restafactura2 = $restafactura2 - $saldoCtacte2;
                    $importeRenglon2 = ($restafactura2 > 0) ? $saldoCtacte2 : $totalfactura2;
                    $totalfactura2 = $totalfactura2 - $importeRenglon2;
                    $imputacion = array();
                    $imputacion['cod_cobro'] = $cobro->getCodigo();
                    $imputacion['cod_ctacte'] = $rowcta['codigo'];
                    $imputacion['estado'] = 'pendiente';
                    $imputacion['valor'] = $importeRenglon2;
                    $imputacion['fecha'] = date('Y-m-d H:i:s');
                    $imputacion['cod_usuario'] = $datosfactura['facturar']['cod_usuario'];
                    if ($importeRenglon2 != 0) {
                        $cobro->inputar($imputacion['cod_ctacte'], $imputacion['valor'], $imputacion['cod_usuario']);
                    }
                }
                $objcaja = new Vcaja($conexion, $cobro->cod_caja);
                $medios = $objcaja->getMediosPago($cobro->medio_pago);
                if (count($medios) > 0) {
                    if ($medios[0]['conf_automatica'] == '1') {
                        $cobro->confirmarCobro($datosfactura['facturar']['cod_usuario']);
                    }
                }

                //mmori-verifico estado de certificado IGA
                foreach ($datosfactura['facturar']['checkctacte'] AS $ctacte_codigo)
                {
                    $ctacte = new Vctacte($conexion, $ctacte_codigo);
                    $matricula = new Vmatriculas($conexion, $ctacte->concepto);

                    if($ctacte->cod_concepto == 1 || $ctacte->cod_concepto == 5)
                    {
                        $matriculas_periodos = $matricula->getPeriodosMatricula();
                        foreach ($matriculas_periodos AS $periodo)
                        {
                            $objcertificado = new Vcertificados($conexion, $periodo['codigo'], 1);
                            $objcertificado->cambiarEstadoCertificadoIGA();
                        }
                    }
                }


                $estadotran = $conexion->trans_status();
                $conexion->trans_complete();
                $arrResp = class_general::_generarRespuestaModelo($conexion, $estadotran, array("imprimir" => $imprimeSobreResponse ? 1 : 0, "factura" => $arrFacturasResp, "cobro" => $codigoCobro));
            }
        }
        return $arrResp;
    }

    public function getRenglonesDescripcion($cod_factura) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $factura = new Vfacturas($conexion, $cod_factura);
        $factRenglon = $factura->getRenglonesDescripcion();
        $this->load->helper('cuentacorriente');
        $arrRetornoFactRenglones = '';
        foreach ($factRenglon as $valor) {
            $condicion = array(
                'codigo' => $valor['codigo']
            );
            $ctactes = Vctacte::getCtaCte($conexion, null, $condicion);
            formatearCtaCte($conexion, $ctactes);
            $arrRetornoFactRenglones['renglones'][] = array(
                'descripcion' => $ctactes[0]['descripcion'],
                'importe' => $valor['importe_facturado'],
                'cod_factura' => $valor['codigo'],
                'importe_formateado' => formatearImporte($valor['importe_facturado'], false),
                'simbolo_moneda' => $ctactes[0]['simbolo_moneda']
            );
        }
        $myPuntoVenta = new Vpuntos_venta($conexion, $factura->punto_venta);
        $detalle = null;
        $detalleError = null;
        $filial = new Vfiliales($conexion, $this->codigo_filial);
        if ($myPuntoVenta->utilizaWebServices()) {
            $metodoFacturacion = $myPuntoVenta->getMetodoFacturacion($this->codigo_filial);
            switch ($metodoFacturacion) {
                case "dsf":
                    $temp = Vseguimiento_dsf::getInfoFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    $detalle = is_array($temp) ? array(
                        array("name" => "RPS", "value" => $temp['numero_rps']),
                        array("name" => "LOTE", "value" => $temp['numero_lote'])
                            ) : null;
                    if ($factura->estado == Vfacturas::getEstadoError()) {
                        $detalleError = Vseguimiento_dsf::getErrorFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    }
                    break;

                case "abrasf":
                    $temp = Vseguimiento_abrasf::getInfoFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    $detalle = is_array($temp) ? array(
                        array("name" => "NFSE", "value" => $temp['numero']),
                        array("name" => "PROTOCOLO", "value" => $temp['protocolo'])
                            ) : null;
                    if ($factura->estado == Vfacturas::getEstadoError()) {
                        $detalleError = Vseguimiento_abrasf::getErrorFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    }
                    break;

                case "ginfes":
                    $temp = Vseguimiento_ginfes::getInfoFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    $detalle = is_array($temp) ? array(
                        array("name" => "LOTE", "value" => $temp['numero_lote']),
                        array("name" => "NFSE", "value" => $temp['numero_nfse']),
                        array("name" => "CODIGO VERIFICACION", "value" => $temp['codigo_verificacion'])
                            ) : null;
                    if ($factura->estado == Vfacturas::getEstadoError()) {
                        $detalleError = Vseguimiento_ginfes::getErrorFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    }
                    break;

                case "toolsnfe":
                    $temp = Vseguimiento_toolsnfe::getInfoFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    $detalle = is_array($temp) ? array(
                        array("name" => "NFE", "value" => $temp['nfe']),
                        array("name" => "RECIBO", "value" => $temp['nRec']),
                        array("name" => "PROTOCOLO", "value" => $temp['nProt'])
                            ) : null;
                    if ($factura->estado == Vfacturas::getEstadoError()) {
                        $detalleError = Vseguimiento_toolsnfe::getErrorFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    }
                    break;

                case "paulistana":
                    $temp = Vseguimiento_paulistana::getInfoFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    $detalle = is_array($temp) ? array(
                        array("name" => "LOTE", "value" => $temp['numero_lote']),
                        array("name" => "NFSe", "value" => $temp['numero_nfse']),
                        array("name" => "CÓDIGO DE VERIFICAÇÃO", "value" => $temp['codigo_verificacion'])
                    ) : null;
                    if ($factura->estado == Vfacturas::getEstadoError()) {
                        $detalleError = Vseguimiento_paulistana::getErrorFactura($conexion, $factura->getCodigo(), $this->codigo_filial);
                    }
                    break;

                default:
                    break;
            }
        }elseif ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) {
            $condiciones = array(
                                 'cod_filial'   => $this->codigo_filial,
                                 'cod_factura'  => $factura->getCodigo()
                                 );
            $seg_actual = Vseguimiento_afip::listarSeguimiento_afip($conexion, $condiciones, array(0, 1), array(array('campo' => 'fecha_hora', 'orden' => 'DESC')));
            if (!empty($seg_actual) && is_array($seg_actual)) {
                if ($factura->estado == Vfacturas::getEstadoError()) {
                    $errores = json_decode($seg_actual[0]['errores']);

                    $detalleError = '';
                    foreach ($errores as $error) {
                        $detalleError .= "({$error->Code}) {$error->Msg}<br />";
                    }
                }else {
                    $detalle = array(
                                     array('name' => 'CAE',             'value' => $seg_actual[0]['cae']),
                                     array('name' => 'VENCIMIENTO CAE', 'value' => $seg_actual[0]['vencimiento_cae']),
                                     array('name' => 'FECHA Y HORA',    'value' => $seg_actual[0]['fecha_hora'])
                                     );
                }
            }
        }
        if ($detalle != null)
            $arrRetornoFactRenglones['web_services'] = $detalle;
        if ($detalleError != null)
            $arrRetornoFactRenglones['web_services_error'] = $detalleError;
        return $arrRetornoFactRenglones;
    }

    public function getFactura($cod_factura) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objFactura = new Vfacturas($conexion, $cod_factura);
        return $objFactura;
    }

    public function guardarFacturasSerie($facturas, $separador) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $conexion->trans_start();
        
        $ctactes = Vctacte::getCtaCteSinFacturar($conexion, null, null, null, null, null, null, false, $facturas['checkctacte'], null, $separador);
        $arrFacturas = array();
        $imprimirSobreResponse = false;        
        $arrPorcentajes = array();
        $porcentajesFijo = 0;
        $cantidadPorcentajeMovil = 0;
        foreach ($facturas['punto_venta'] as $puntoVenta){
            $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
            $porcentaje = $myPuntoVenta->getPorcentajeFacturar($this->codigo_filial);
            if ($porcentaje <> ''){
                $porcentajesFijo += $porcentaje;
            } else {
                $cantidadPorcentajeMovil ++;
            }
            $arrPorcentajes[$puntoVenta] =$porcentaje;                
        }
        if ($cantidadPorcentajeMovil > 0){                
            $porcentajesMoviles = (100 - $porcentajesFijo) / $cantidadPorcentajeMovil;
            foreach ($arrPorcentajes as $key => $value){
                if ($value == ''){
                    $arrPorcentajes[$key] = $porcentajesMoviles;
                }
            }
        }
        if ($porcentaje <> 100){
            $porcentaje = 0;
            foreach ($arrPorcentajes as $value){
                $porcentaje += $value;
            }
            if ($porcentaje < 99 || $porcentaje > 101){
                $resultado = array(
                    'codigo' => '0',
                    'msgerror' => lang('la_configuracion_de_porcentajes_de_facturacion_sobre_los_puntos_de_venta_seleccionados_debe_ser_del_100'),
                    'errNo' => '',
                );
                echo json_encode($resultado);
                die();
            }
        }
        $filial = new Vfiliales($conexion, $this->codigo_filial);
        foreach ($ctactes as $rowrenglon) {
            foreach ($facturas['punto_venta'] as $puntoVenta) {
                $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);   
                $porcentaje = $arrPorcentajes[$puntoVenta];    
                $estado = $myPuntoVenta->utilizaWebServices() || ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) ? Vfacturas::getEstadoPendiente() : null;
                $facturas['renglones'] = array();
                $renglon = new Vfacturas_renglones($conexion);
                $renglon->cod_ctacte = $rowrenglon['codigo'];
                $renglon->importe = $rowrenglon['saldofacturar'] * $porcentaje / 100;
                $facturas['renglones'][] = $renglon;
                $facturas['codalumno'] = $rowrenglon['cod_alumno'];
                $facturas['total'] = $rowrenglon['saldofacturar'] * $porcentaje / 100;
                $facturas['fecha'] = null;
                $facturas['codrazsoc'] = NULL;
                $objFactura = new Vfacturas($conexion);
                $objFactura->facturar($facturas['total'], $myPuntoVenta->getCodigo(), $facturas['cod_usuario'], $facturas['renglones'], $facturas['codalumno'], $facturas['fecha'], $facturas['codrazsoc'], $estado, $filial->pais);

                if ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) {
                    $conexion_gral = $this->load->database('', true);
                    Vprestador_afip::ValidarFactura($conexion_gral, $myPuntoVenta, $objFactura, $this->codigo_filial, $this->config->item('ws_afip_testing'));
                }

                if ($objFactura->estado == null || $objFactura->estado == Vfacturas::getEstadoHabilitado()) {
                    $arrFacturas[] = $objFactura->getCodigo();
                    $imprimirSobreResponse = true;
                }
            }
        }
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, array("factura" => $arrFacturas, "imprimir" => $imprimirSobreResponse ? 1 : 0));
    }

    public function getListadoFacturacion($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null, $fechaDesde = null, $fechaHasta = null, $tipoFactura = null, $anulada = null, $facturaDesde = null, $facturaHasta = null, $idCtaCte = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vfacturas::getListadoFacturacion($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta, $tipoFactura, $anulada, $facturaDesde, $facturaHasta, $idCtaCte);
        $registros = Vfacturas::getListadoFacturacion($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta, $tipoFactura, $anulada, $facturaDesde, $facturaHasta, $idCtaCte);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    /* agrega parametro opcional codFilial porque esta function se puede acceder desde un web services NO MODIFICAR, ELIMINAR NI COMENTAR */
    public function getTiposFacturas($codFilial = null) {
        if ($codFilial != null) {
            $conexion = $this->load->database($codFilial, true);
        } else {
            $conexion = $this->load->database($this->codigo_filial, true);
        }
        $arrResp = Vtipos_facturas::listarTipos_facturas($conexion);
        return $arrResp;
    }

    public function getTiposFacturasCompras() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condicion = array(
            "habilitado" => 1
        );
        $arrResp = Vtipos_facturas::listarTipos_facturas($conexion, $condicion);
        return $arrResp;
    }

    public function getRenglones($idFilial, $idFactura) {
        $conexion = $this->load->database($idFilial, true);
        $myFactura = new Vfacturas($conexion, $idFactura);
        $arrRenglones = $myFactura->getRenglones();
        $this->load->helper('cuentacorriente');
        formatearCtaCte($conexion, $arrRenglones);
        return $arrRenglones;
    }

    public function getFacturas($arrCodigosFacturas = null, $contar = false, array $condiciones = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        return Vfacturas::listarFacturasDataTable($conexion, null, null, null, $contar, $arrCodigosFacturas, $condiciones);
    }

    public function enviar_facturas($arrFacturas, array &$arrSendError) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $facturas = $this->getFacturas($arrFacturas);
        $arrTemp = array();
        foreach ($facturas as $factura) {
            $codigo = $factura['codigo'];
            $arrCodigos[] = $codigo;
            $email = $factura['email'];
            $pdf = new PDF_AutoPrint();
            $pdf = $this->Model_impresiones->getPDFFacturas($conexion, $arrCodigos, 1);
            $fileName = $_SERVER['DOCUMENT_ROOT'] . "/sistemasiga/printer_files/view/" . md5($email . date("Y-m-d H:i:s")) . ".pdf";
            if ($pdf->Output($fileName, "F")) {
                $arrTemp[$email][] = $fileName;
            } else {
                return false;
            }
        }
        $arrSendError = array();
        foreach ($arrTemp as $email => $arrFileNames) {
            $this->email->from('noreply@iga-la.net', 'iga noreply');
            $this->email->to($email);
            $this->email->subject('Facturas');
            $this->email->message('Facturas');
            foreach ($arrFileNames as $fileName) {
                $this->email->attach($fileName);
            }
            if (!$this->email->send()) {
                $arrSendError[] = $email;
            }
        }
        return count($arrSendError) == 0;
    }

    public function getCobroAsociadoAnular($cod_factura, $cod_usuario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objFactura = new Vfacturas($conexion, $cod_factura);
        $arrcobro = $objFactura->getCobroAsociado();
        if (count($arrcobro) > 0) {
            $condiciones = array('codigo' => $arrcobro[0]['cod_cobro']);
            $acobro = Vcobros::listarCobros($conexion, $condiciones);
            $cobro['cobro'] = $acobro[0];
            $mensajeError = '';
            $validar = validaciones::validarAnulacionCobro($arrcobro[0]['cod_cobro'], $cod_usuario, $mensajeError);
            if ($validar) {
                $objcobro = new Vcobros($conexion, $arrcobro[0]['cod_cobro']);
                $caja = $objcobro->getCaja();
                $cobro['caja'] = $caja->nombre;
                $medio = new Vmedios_pago($conexion, $objcobro->medio_pago);
                $cobro['medio_pago'] = lang($medio->medio);
                return $cobro;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function habilitar_factura($codFactura) 
    {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myFactura = new Vfacturas($conexion, $codFactura);
        $retorno = true;
        $msgError = "";
        if ($myFactura->estado == Vfacturas::getEstadoInhabilitado() || $myFactura->estado == Vfacturas::getEstadoError()) {
            $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
            $filial = new Vfiliales($conexion, $this->codigo_filial);
            $estado = $myPuntoVenta->utilizaWebServices() || ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) ? Vfacturas::getEstadoPendiente() : Vfacturas::getEstadoHabilitado();
            if (!$myFactura->setEstado($estado)) {
                $retorno = false;
                $msgError = lang("error_al_cambiar_el_estado_de_la_factura");
            }
            
            //Ticket -4840- mmori - habilitar cobro al habilitar factura
            $arrCobro = $myFactura->getCobroAsociado(false);
            foreach ($arrCobro as $cobro)
            {
                $objCobro = new Vcobros($conexion, $cobro['cod_cobro']);
                $objCobro->habilitarCobro();
            }
            
            //Ticket -4840- mmori - habilitar renglon de la factura
            $arrRenglones = $myFactura->getRenglones(false);
            foreach ($arrRenglones as $renglon)
            {
                $conexion->query("UPDATE facturas_renglones SET facturas_renglones.anulada = 0 WHERE facturas_renglones.codigo = ".$renglon['codigo_renglon']);
                //echo($conexion->last_query());
            }
                        
            if ($filial->pais == 1 && $myPuntoVenta->medio == Vpuntos_venta::getMedioElectronico()) {
                $conexion_gral = $this->load->database('', true);
                Vprestador_afip::ValidarFactura($conexion_gral, $myPuntoVenta, $myFactura, $this->codigo_filial, $this->config->item('ws_afip_testing'));
            }
        } else {
            $retorno = false;
            $msgError = lang("no_se_puede_habilitar_la_factura_por_encontrarse_en_estado") . " " . lang($myFactura->estado);
        }
        return array("codigo" => $retorno ? 1 : 0, "msgerror" => $msgError);
    }

    public function puedeEnviarFacturasMail() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $respuesta = '0';
        $filial = new Vfiliales($conexion, $this->codigo_filial);
        $arrfacturantes = $filial->getFacturantes();
        foreach ($arrfacturantes as $value) {
            $facturante = new Vfacturantes($conexion, $value['codigo']);
            $arrptos = $facturante->getPuntosVenta();
            foreach ($arrptos as $ptovta) {
                if ($ptovta['medio'] == 'electronico') {
                    $respuesta = '1';
                }
            }
        }
        return $respuesta;
    }

    public function getAlumno($cod_factura) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $factura = new Vfacturas($conexion, $cod_factura);
        $arralumno = $factura->getAlumno();
        $alumno = new Valumnos($conexion, $arralumno[0]['codigo']);
        return $alumno;
    }

    public function calcularTotal($valores) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $total = 0;
        $filial = new Vfiliales($conexion, $this->codigo_filial);
        $moneda = $filial->getMonedaCotizacion();
        $monedaSimbolo = $moneda[0]['simbolo'];
        $separador = Vconfiguracion::getValorConfiguracion($conexion, null, 'SeparadorDecimal');
        foreach ($valores as $value) {
            $valor = str_replace($separador, '.', $value);
            $importe = strlen($valor) > 0 ? str_replace($monedaSimbolo, '', $valor) : $valor;
            $total = $total + $importe;
        }
        $retorno = $total;
        $totalFormateado = str_replace('.', $separador, $retorno);
        return $totalFormateado;
    }

}

/* End of file model_facturas.php */
    /* Location: ./application/models/model_facturas.php */
    
