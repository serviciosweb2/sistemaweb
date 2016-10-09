<?php

/**
 * Model_cobros
 * 
 * Description...
 * 
 * @package model_cobros
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_cobros extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function listarCobrosDataTable($arrFiltros, $separador, $separadorDecimal, $idFilial = null) {
        if ($idFilial == null) {
            $conexion = $this->load->database($this->codigo_filial, true);
        } else {
            $conexion = $this->load->database($idFilial, true);
        }
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $this->load->helper('formatearfecha');
        $arrCondiciones = array();

        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "cobros.codigo" => $arrFiltros["sSearch"],
                "nombre_apellido" => $arrFiltros["sSearch"],
                "general.medios_pago.medio" => $arrFiltros["sSearch"],
                "cobros.importe" => $arrFiltros["sSearch"],
                "caja" => $arrFiltros["sSearch"],
                "documento_completo" => $arrFiltros["sSearch"],
            );
        }
        $estado = $arrFiltros['selectEstado'];		
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        $order = array();
        if ($arrFiltros["SortCol"] != '' and $arrFiltros["sSortDir"] != '') {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        } else {

            $order[] = array(
                0 => 'codigo',
                1 => 'desc'
            );
        }
        $fechaDesde = isset($arrFiltros['fecha_desde']) ? $arrFiltros['fecha_desde'] : null;
        $fechaHasta = isset($arrFiltros['fecha_hasta']) ? $arrFiltros['fecha_hasta'] : null;
        $medio_pago = isset($arrFiltros['medio_pago']) ? $arrFiltros['medio_pago'] : null;
        $saldo = isset($arrFiltros['saldo']) ? $arrFiltros['saldo'] : null;
        $caja = isset($arrFiltros['caja']) ? $arrFiltros['caja'] : null;
        $periodo = null;
        if (isset($arrFiltros['periodo_mes']) && $arrFiltros['periodo_mes'] != null 
                && isset($arrFiltros['periodo_anio']) && $arrFiltros['periodo_anio'] != null){
            $periodo = $arrFiltros['periodo_anio'].str_pad($arrFiltros['periodo_mes'], 2, 0, STR_PAD_LEFT);
        }
        $datos = Vcobros::listarCobrosDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, '', $order, $separador, $separadorDecimal, $fechaDesde, $fechaHasta, $estado, $periodo, $caja, $saldo, $medio_pago);
        $contar = Vcobros::listarCobrosDataTable($conexion, $arrCondiciones, '', '', TRUE, '', $separador, $separadorDecimal, $fechaDesde, $fechaHasta, $estado, $periodo, $caja, $saldo, $medio_pago);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $detalle_medio = $row['cod_medio'] == 1 ? false : true;
            $periodo = $row['periodo'] != '' ? substr($row['periodo'], 4) . '/' . substr($row['periodo'], 0, 4) : '-';
            $rows[] = array(
                $row["codigo"],
                inicialesMayusculas($row["nombre_apellido"]),
                $row['documento_completo'],
                formatearImporte($row["importe"], true, $conexion),
                formatearImporte($row['total_imputado'], true, $conexion),
                formatearImporte($row['saldoRestante'], true, $conexion),
                lang($row["medio"]),
                $row['caja'],
                $row['fechaalta'] = $row['fechaalta'] == '0000-00-00' ? '-' : formatearFecha_pais($row['fechaalta']),
                $periodo,
                $row["estado"],
                $row["baja"] = $row['estado'],
                $detalle_medio,
                $row["importe"],
                $row['total_imputado'],
                $row['saldoRestante']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getImputacionesCobro($cod_cobro, $formato = true) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $imputacionesCobro = $objCobro->getSaldoImputacion();
        if ($formato) {
            $arrRetornoImputaciones = '';
            foreach ($imputacionesCobro as $valor) {
                $condicion = array(
                    'codigo' => $valor['cod_ctacte']
                );
                $ctactes = Vctacte::getCtaCte($conexion, null, $condicion);
                formatearCtaCte($conexion, $ctactes);
                $fecha = formatearFecha_pais(substr($valor['fecha'], 0, 10));
                $arrRetornoImputaciones[] = array(
                    'codigo' => $valor['codigo'],
                    'descripcion' => $ctactes[0]['descripcion'],
                    'valorImputacion' => formatearImporte($valor['valor']),
                    'vencimiento' => $ctactes[0]['fechavenc'],
                    'fecha_imputacion' => $fecha,
                    'cod_ctacte' => $valor['cod_ctacte'],
                    'cod_concepto' => $ctactes[0]['cod_concepto'],
                    'estado' => lang($valor['estado'])
                );
            }
            return $arrRetornoImputaciones;
        } else {
            return $imputacionesCobro;
        }
    }

    public function guardarCobro($datoscobro, CI_DB_mysqli_driver $conexion = null) {
        if ($conexion == null){
            $conexion = $this->load->database($this->codigo_filial, true);
            $conexion->trans_start();
            $cerrarTransaccion = true;
        } else {
            $cerrarTransaccion = false;
        }
        $this->load->helper('cuentacorriente');
        $cobro = new Vcobros($conexion, $datoscobro['cobrar']['codigo']);
        $fechacreacion = date("Y-m-d H:i:s");
        $imputadocobro = $cobro->getSumValorImputacionesCobro();
        $totalimputado = count($imputadocobro) > 0 ? $imputadocobro[0]['totImputaciones'] : 0;
        $cod_alumno = $datoscobro['cobrar']['cod_alumno'];
        $medioCobro = new Vmedios_pago($conexion, $datoscobro['cobrar']['medio_cobro']);
        if ($cobro->getCodigo() == '-1') {
            $objMedio = $medioCobro->getObjmedio($datoscobro['medio_cobro']);
        } else {
            $fechacreacion = $cobro->fechaalta;
            $cod_alumno = $cobro->cod_alumno;
            $totali = $cobro->getSumValorImputacionesCobro();
            $totalimputado = $totali[0]['totImputaciones'];
            if ($datoscobro['cobrar']['medio_cobro'] == $cobro->medio_pago) {
                $condicion = array('cod_cobro' => $cobro->getCodigo());
                $cod_medio = -1;
                switch ($datoscobro['cobrar']['medio_cobro']) {
                    case '2': //BOLETO BANCARIO
                        break;
                    
                    case '3'://TARJETA
                        $respuesta = Vmedio_tarjetas::listarMedio_tarjetas($conexion, $condicion);
                        $cod_medio = count($respuesta) > 0 ? $respuesta[0]['codigo'] : -1;
                        break;
                    
                    case '4'://CHEQUE
                        $respuesta = Vmedio_cheques::listarMedio_cheques($conexion, $condicion);
                        $cod_medio = count($respuesta) > 0 ? $respuesta[0]['codigo'] : -1;
                        break;
//                    case '5'://NOTA CREDITO
//                        $respuesta = Vmedio_notas_credito::listarMedio_notas_credito($conexion, $condicion);
//                        $cod_medio = count($respuesta) > 0 ? $respuesta[0]['codigo'] : -1;
//
//                        break;
                    
                    case '6'://DEPOSITO BANCARIO
                        $respuesta = Vmedio_depositos::listarMedio_depositos($conexion, $condicion);
                        $cod_medio = count($respuesta) > 0 ? $respuesta[0]['codigo'] : -1;
                        break;
                    
                    case '7'://TRANSFERENCIA
                        $respuesta = Vmedio_transferencias::listarMedio_transferencias($conexion, $condicion);
                        $cod_medio = count($respuesta) > 0 ? $respuesta[0]['codigo'] : -1;
                        break;

                    case '8'://TARJETA DEBITO
                        $respuesta = Vmedio_tarjeta_debito::listarMedio_debito($conexion, $condicion);
                        $cod_medio = count($respuesta) > 0 ? $respuesta[0]['codigo'] : -1;
                        break;
                }

                $objMedio = $medioCobro->getObjmedio($datoscobro['medio_cobro'], $cod_medio);
            } else {

                $objMedio = $medioCobro->getObjmedio($datoscobro['medio_cobro']);
            }
        }

        $rowsCtaCte = array();
        $codctacte = array();
        foreach ($datoscobro['cobrar']['checkctacte'] as $value) {
            $codctacte[] = $value;
        }
        if (count($codctacte) != 0) {
            $rowsCtaCte = Vctacte::getCtaCteCobrar($conexion, '', '', $codctacte);
        }
        $cuentas = array();
        
        foreach ($rowsCtaCte as $value) {
            foreach ($codctacte as $key => $cta) {
                if ($cta == $value['codigo']) {
                    $cuentas[$key] = $value;
                }
            }
        }
        //GUARDO COBRO
        $cobro->desasociarFactura();
        $cobro->guardarCobro($datoscobro['cobrar']['total_cobrar'], $datoscobro['cobrar']['medio_cobro'], Vcobros::getEstadoPendiente(), $cod_alumno, $datoscobro['cobrar']['cod_usuario'], $datoscobro['cobrar']['caja'], $fechacreacion, $objMedio, $datoscobro['cobrar']['fecha_cobro']);
        $totalCobrar = $datoscobro['cobrar']['total_cobrar'] - $totalimputado;
        $restaCobrar = $totalCobrar;
        if (count($cuentas) > 0) {
            ksort($cuentas);
        }

        foreach ($cuentas as $value) {
            $saldoCtacte = $value['saldocobrar'];
            $restaCobrar = $restaCobrar - $saldoCtacte;
            $importeRenglonImputacion = ($restaCobrar > 0) ? $saldoCtacte : $totalCobrar;
            $totalCobrar = $totalCobrar - $importeRenglonImputacion;
            if ($importeRenglonImputacion > 0) {
                $cobro->inputar($value['codigo'], $importeRenglonImputacion, $datoscobro['cobrar']['cod_usuario']);
            }
        }
        if ($datoscobro['cobrar']['estado'] == '1') {
            $objcaja = new Vcaja($conexion, $cobro->cod_caja);
            $medios = $objcaja->getMediosPago($cobro->medio_pago);
            if (count($medios) > 0) {
                if ($medios[0]['conf_automatica'] == '1') {
                    $cobro->confirmarCobro($datoscobro['cobrar']['cod_usuario']);
                }
            }
        } else {
            $errores = isset($datoscobro['cobrar']['errores']) ? $datoscobro['cobrar']['errores'] : '-';
            $cobro->errorCobro(null, $errores);
        }
        
        //mmori-verifico estado de certificado IGA
        foreach ($datoscobro['cobrar']['checkctacte'] AS $ctacte_codigo)
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
        $arrRespuesta = $cobro->getCodigo();
        if ($cerrarTransaccion){
            $conexion->trans_complete();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $arrRespuesta);
    }

    public function getCobro($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objCobro = new Vcobros($conexion, $cod_cobro);
        return $objCobro;
    }

    public function getDetallesMedio($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('alumnos');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $detalleCobro = $objCobro->getDetalleCobro();
        switch ($objCobro->medio_pago) {
            case 2:
                $detalleCobro[0]['nombre_banco'] = inicialesMayusculas($detalleCobro[0]['nombre_banco']);
                $detalleCobro[0]['fecha_documento'] = formatearFecha_pais($detalleCobro[0]['fecha_documento']);
                break;
            
            case 3:
                $detalleCobro[0]['cupon'] = $detalleCobro[0]['cupon'];
                $detalleCobro[0]['autorizacion'] = $detalleCobro[0]['cod_autorizacion'];
                $detalleCobro[0]['nombreBanco'] = $detalleCobro[0]['nombreBanco'] == 'null' ? '-' : inicialesMayusculas($detalleCobro[0]['nombreBanco']);
                break;
            
            case 4:
                foreach ($detalleCobro as $key => $value) {
                    $detalleCobro[$key]['nombre_cheque'] = Vmedio_cheques::getTipos(array($value['tipo_cheque']));
                    $detalleCobro[$key]['fecha_cobro'] = formatearFecha_pais($value['fecha_cobro']);
                }
                break;
                
            case 5:
                formatearCtaCte($conexion, $detalleCobro);
                break;


            case 8:
                $detalleCobro[0]['cupon'] = $detalleCobro[0]['cupon'];
                $detalleCobro[0]['autorizacion'] = $detalleCobro[0]['cod_autorizacion'];
                $detalleCobro[0]['nombreBanco'] = $detalleCobro[0]['nombreBanco'] == 'null' ? '-' : inicialesMayusculas($detalleCobro[0]['nombreBanco']);
                break;

            default:
                break;
        }
        $detalleCobro[0]['medio'] = lang($detalleCobro[0]['medio']);
        return $detalleCobro;
    }

    public function getMotivosBaja() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $cobroEstado = new Vcobro_estado_historico($conexion);
        return $cobroEstado->getMotivos();
    }

    public function bajaCobro($cod_cobro, $codigoUsuario, &$mensajeError = null) {        
        $validar = validaciones::validarAnulacionCobro($cod_cobro, $codigoUsuario, $mensajeError);
        return $validar;
    }

    public function mostrarFrmImputaciones($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $validar = validaciones::validarMostrarFrmImputaciones($conexion, $cod_cobro);
        return $validar;
    }

//      public function cambioEstadoCobro($cambioEstado){
//          $conexion = $this->load->database($this->codigo_filial, TRUE);
//          $cobro = new Vcobros($conexion, $cambioEstado['cod_cobro']);
//          $estado = $cobro->cambiarEstadoCobro($cambioEstado);
//          return class_general::_generarRespuestaModelo($conexion, $estado);
//      }
    public function cambioEstadoCobro($cambioEstado) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $conexion->trans_begin();
        $cobro = new Vcobros($conexion, $cambioEstado['cod_cobro']);
        if ($cobro->estado == Vcobros::getEstadoConfirmado() || $cobro->estado == Vcobros::getEstadoPendiente() || $cobro->estado == Vcobros::getEstadoError()) {
            $respuesta = $cobro->anularCobro($cambioEstado['motivo'], $cambioEstado['comentario'], $cambioEstado['cod_usuario'], true);
            if (isset($cambioEstado['facturas_anuladas']) && is_array($cambioEstado['facturas_anuladas'])){
                foreach ($cambioEstado['facturas_anuladas'] as $factura){
                    $myFactura = new Vfacturas($conexion, $factura);
                    $myFactura->baja();
                }
            }
        }
        $estadoTran = $conexion->trans_status();
        if ($estadoTran === false) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadoTran, $respuesta);
    }

    public function getCobrosMensuales($idFilial, array $arrPeriodos = null) {
        $conexion = $this->load->database($idFilial, true);
        return Vcobros::getCobrosMensuales($conexion, $arrPeriodos);
    }

    public function getCtaImputar($cod_cobro, $descripcion = true, $wherein = false) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $this->load->helper('cuentacorriente');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $cod_alumno = $objCobro->cod_alumno;
        if ($descripcion) {
            $ctacteAlu = Vctacte::getCtaCteImputar($conexion, $cod_alumno, '', '');
            formatearCtaCte($conexion, $ctacteAlu);
            return $ctacteAlu;
        } else {
            $ctacte = Vctacte::getCtaCteImputar($conexion, null, '', $wherein);
            return $ctacte;
        }
    }

    public function getSaldo($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $this->load->helper('filial');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $totImputaciones = $objCobro->getSumValorImputacionesCobro();
        $saldo = $objCobro->importe - $totImputaciones[0]['totImputaciones'];
        $saldoFormateaedo = formatearImporte($saldo);
        return $saldoFormateaedo;
    }

    public function guardarCobroImputaciones($datos, $dataCodigo, $dataValor) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $conexion->trans_start();
        $Mycobro = new Vcobros($conexion, $datos['cobro']);
        foreach ($dataCodigo['codigoImputar'] as $key => $valor) {
            $Mycobro->inputar($valor, $dataValor['valorImputar'][$key], $datos['usuario']);
        }
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

//SE PASO A CTACTE
//    public function getImputacionesCtacte($idFilial, $idCtaCte) {
//        $conexion = $this->load->database($idFilial, true);
//        $myCtacte = new Vctacte($conexion, $idCtaCte);
//        $arrImputaciones = $myCtacte->getImputacionesCtaCte();
//        return $arrImputaciones;
//    }

    public function guardarNotaCredito($arrayCtaCte, $arrayValorCtaCte, $data_post) {
        foreach ($arrayCtaCte as $key => $ctacte) {
            $data_post['medio_cobro']['renglones'][$key]['cod_ctacte'] = $ctacte;
            $data_post['medio_cobro']['renglones'][$key]['valor'] = $arrayValorCtaCte[$key];
        }
        return $this->guardarCobro($data_post);
    }

    public function getReferenciaImputaciones($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $detalleCobro = $objCobro->getDetalleCobro();
        switch ($objCobro->medio_pago) {
            case 4:
                $detalleCobro[0]['nombre_cheque'] = Vmedio_cheques::getTipos(array($detalleCobro[0]['tipo_cheque']));
                break;

            default:
                break;
        }
        $detalleCobro[0]['valor'] = formatearImporte($detalleCobro[0]['valor']);
        $detalleCobro[0]['medio'] = lang($detalleCobro[0]['medio']);
        return $detalleCobro;
    }

    public function getTotalImputaciones($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $total_imputaciones = $objCobro->getSumValorImputacionesCobro();
        $total_imputaciones[0]['totImputaciones'] = formatearImporte($total_imputaciones[0]['totImputaciones']);
        return $total_imputaciones;
    }

    public function getCajaCobro($codCobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myCobro = new Vcobros($conexion, $codCobro);        
        $myCaja = $myCobro->getCaja();
        $arrResp[0] = json_decode(json_encode($myCaja), true);
        $arrResp[0]['codigo'] = $myCaja->getCodigo();
        return $arrResp;
    }

    public function getnombreAlumno($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $objAlumno = new Valumnos($conexion, $objCobro->cod_alumno);
        $nombreApellido = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
        $nombreFormateado = inicialesMayusculas($nombreApellido);
        return $nombreFormateado;
    }

    public function generarBoletoBancario($arrCtactes, $codusario, $cod_banco, $cod_cuenta, $convenio = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexio_cedente = $this->load->database($this->codigo_filial, true);
        $ctactes = Vctacte::listarCtacte($conexion, null, null, null, null, null, $arrCtactes);
        $cuenta = new Vcuentas_boletos_bancarios($conexio_cedente, $cod_banco, $cod_cuenta, null, $convenio);
        $cedente = new Vcedentes($conexio_cedente, $cod_banco, $cod_cuenta, $convenio);
        $myBanco = new Vbancos($conexion, $cod_banco);
        $myBoleto = new Vboletos_bancarios($conexion);
        $myCuentaBanco = $myBanco->getCuentaBanco($cod_cuenta);
        $condiciones = array("baja" => 0,
            "diariamente" => 1);
        $moras = Vmoras::listarMoras($conexion, $condiciones);
        $interesMora = isset($moras[0]) && isset($moras[0]["mora"]) ? $moras[0]["mora"] : 0;
        $conexion->trans_begin();
        $myBoleto->agencia = $myCuentaBanco->agencia;
        $myBoleto->cartera = $cuenta->getCarteira();
        $myBoleto->cedente_convenio = $cedente->convenio;
        $myBoleto->cedente_cpf_cnpj = $cedente->cpf_cnpj;
        $myBoleto->cod_banco = $cod_banco;
        $myBoleto->cod_cuenta = $cod_cuenta;
        $myBoleto->digito_agencia = $myCuentaBanco->digito_agencia;
        $myBoleto->digito_cuenta = $myCuentaBanco->digito_cuenta;
        $myBoleto->especie_documento = "DM";
        $myBoleto->fecha_documento = date("Y-m-d");
        $myBoleto->nombre_banco = $myBanco->nombre;
        $myBoleto->numero_cuenta = $myCuentaBanco->conta;
        $myBoleto->razon_social = $cedente->razon_social;
        $myBoleto->variacion_cartera = $cuenta->variacao_carteira;
        $myBoleto->guardarBoletos_bancarios();
        foreach ($ctactes as $ctacte) {
            $alumno = new Valumnos($conexion, $ctacte["cod_alumno"]);
            $sacado = $alumno->getSacado();
            $myLineaBoleto = new Vboletos_bancarios_lineas($conexion);
            $myLineaBoleto->cod_boleto = $myBoleto->getCodigo();
            $myLineaBoleto->fecha_mora = $ctacte["fechavenc"];
            $myLineaBoleto->fecha_vencimiento = $ctacte["fechavenc"];
            $myLineaBoleto->numero_documento = $ctacte['codigo'];
            $myLineaBoleto->numero_secuencial = $cuenta->numero_secuencia;
            $myLineaBoleto->porcentaje_mora = $interesMora;
            $myLineaBoleto->sacado_ciudad = $sacado->ciudad->nombre;
            $myLineaBoleto->sacado_cod_postal = $sacado->cod_postal;
            $codigoEstado = $sacado->provincia->get_codigo_estado();
            $myLineaBoleto->sacado_codigo_estado = $codigoEstado == '' ? "" : $codigoEstado;
            $myLineaBoleto->sacado_cpf_cnpj = $sacado->cpf_cnpj;
            $myLineaBoleto->sacado_direccion = $sacado->direccion;
            $myLineaBoleto->sacado_nombre = $sacado->nombre;
            $myLineaBoleto->valor_boleto = $ctacte["importe"];
            $myLineaBoleto->guardarBoletos_bancarios_lineas();
            $cuenta->incremetarNumeroSequencial();
        }
        if ($conexion->trans_status()) {
            $conexion->trans_commit();           
            return $myBoleto;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }

    public function conciliarCobros($cod_filial = null) {
        if ($cod_filial != null) {
            $arrFiliales[] = array('codigo' => $cod_filial);
        } else {
            $conexion = $this->load->database("default", true);
            $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        }
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $conexion->where_in('cobros.estado', array(Vcobros::getEstadoPendiente(), Vcobros::getEstadoError()));
            $arrCobros = Vcobros::listarCobros($conexion);
            foreach ($arrCobros as $cobro) {
                $objcobro = new Vcobros($conexion, $cobro['codigo']);
                switch ($objcobro->medio_pago) {
                    case '3'://TARJETA
                        $mTarjeta = Vmedio_tarjetas::listarMedio_tarjetas($conexion, array('cod_cobro' => $objcobro->getCodigo()));
                        $objterminal = new Vpos_terminales($conexion, $mTarjeta[0]['cod_terminal']);
                        $cod_operador = $objterminal->getCodigoOperador();
                        $objoperador = new Vpos_operadores($conexion, $cod_operador);
                        $retorno = $objoperador->conciliarVenta($mTarjeta[0]['cupon'], $mTarjeta[0]['cod_autorizacion'], $objterminal->cod_interno, $objcobro->fechareal);
                        if ($retorno['valor'] != 0) {
                            $errores = '';
                            $errores.=$retorno['valor'] == $objcobro->importe ? '' : '***error_importe';
                            $errores.=$retorno['fecha'] == $objcobro->fechareal ? '' : '***error_fecha';
                            if ($errores == '') {
                                $objcobro->confirmarCobro(null, true);
                            } else {
                                if ($objcobro->estado == $objcobro->getEstadoPendiente()) {
                                    $objcobro->errorCobro(null, $errores);
                                }
                            }
                        }
                        break;

                    default:
                        break;
                }
            }

            $estadotran = $conexion->trans_status();
            if ($estadotran === FALSE) {
                $conexion->trans_rollback();
            } else {
                $conexion->trans_commit();
            }
        }
        return $estadotran;
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

    public function getMedioCobro($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $condicion = array('cod_cobro' => $objCobro->getCodigo());        
        $respuesta = array();
        switch ($objCobro->medio_pago) {
            case 2:
                break;
            
            case 3:
                $medio = Vmedio_tarjetas::listarMedio_tarjetas($conexion, $condicion);
                $respuesta = count($medio) > 0 ? $medio[0] : array();
                break;
            
            case 4:
                $medio = Vmedio_cheques::listarMedio_cheques($conexion, $condicion);
                $respuesta = count($medio) > 0 ? $medio[0] : array();
                $respuesta['fecha_cobro'] = isset($respuesta['fecha_cobro']) && $respuesta['fecha_cobro'] != '0000-00-00' ? formatearFecha_pais($respuesta['fecha_cobro']) : '';
                break;
            
            case 5:
                $medio = Vmedio_notas_credito::listarMedio_notas_credito($conexion, $condicion);
                $respuesta = count($medio) > 0 ? $medio[0] : array();
                break;
            
            case 6:
                $medio = Vmedio_depositos::listarMedio_depositos($conexion, $condicion);
                $respuesta = count($medio) > 0 ? $medio[0] : array();
                $respuesta['fecha_hora'] = isset($respuesta['fecha_hora']) && $respuesta['fecha_hora'] != '0000-00-00 00:00:00' ? formatearFecha_pais(substr($respuesta['fecha_hora'], 0, 10)) : '';
                break;
            
            case 7:
                $medio = Vmedio_transferencias::listarMedio_transferencias($conexion, $condicion);
                $respuesta = count($medio) > 0 ? $medio[0] : array();
                $respuesta['fecha_hora'] = isset($respuesta['fecha_hora']) && $respuesta['fecha_hora'] != '0000-00-00 00:00:00' ? formatearFecha_pais(substr($respuesta['fecha_hora'], 0, 10)) : '';
                break;
            
            default:
                break;
        }
        return $respuesta;
    }

    public function confirmarCobro($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objCobro = new Vcobros($conexion, $datos['cod_cobro']);
        return $objCobro->confirmarCobro($datos['cod_usuario'], $datos['abrecaja']);
    }

    public function getErroresCobro($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $respuesta = array();
        $objCobro = new Vcobros($conexion, $cod_cobro);
        if ($objCobro->estado == Vcobros::getEstadoError()) {
            $condicion = array('cod_cobro' => $objCobro->getCodigo());
            $order = array(array('campo' => 'codigo', 'orden' => 'desc'));
            $historico = Vcobro_estado_historico::listarCobro_estado_historico($conexion, $condicion, null, $order);
            if (count($historico) > 0) {
                if ((substr_count($historico[0]['comentario'], '***')) > 0) {
                    $comentarios = explode('***', $historico[0]['comentario']);
                    foreach ($comentarios as $value) {
                        if ($value != '') {
                            $respuesta[] = array('error' => lang($value));
                        }
                    }
                } else {
                    $respuesta[] = array('error' => $historico[0]['comentario']);
                }
            }
        }
        return $respuesta;
    }

    public function guardarImputacionesCobro($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $respuesta = '';
        $this->load->helper('cuentacorriente');
        $cobro = new Vcobros($conexion, $datos['cod_cobro']);
        $cobro->desasociarFactura();
        $totalimputado = $cobro->getSumValorImputacionesCobro();
        $rowsCtaCte = array();
        $codctacte = array();
        foreach ($datos['checkctacte'] as $value) {
            $codctacte[] = $value;
        }
        if (count($codctacte) != 0) {
            $rowsCtaCte = Vctacte::getCtaCteCobrar($conexion, '', '', $codctacte);
        }
        $cuentas = array();
        foreach ($rowsCtaCte as $value) {
            foreach ($codctacte as $key => $cta) {
                if ($cta == $value['codigo']) {
                    $cuentas[$key] = $value;
                }
            }
        }
        $totalCobrar = $cobro->importe - $totalimputado[0]['totImputaciones'];
        $restaCobrar = $totalCobrar;
        if (count($cuentas) > 0) {
            ksort($cuentas);
        }
        foreach ($cuentas as $value) {
            $saldoCtacte = $value['saldocobrar'];
            $restaCobrar = $restaCobrar - $saldoCtacte;
            $importeRenglonImputacion = ($restaCobrar > 0) ? $saldoCtacte : $totalCobrar;
            $totalCobrar = $totalCobrar - $importeRenglonImputacion;
            if ($importeRenglonImputacion > 0) {
                $respuesta = $cobro->inputar($value['codigo'], $importeRenglonImputacion, $datos['cod_usuario']);
                if ($respuesta && $cobro->estado != Vcobros::getEstadoPendiente()) {
                    $condiciones = array('cod_ctacte' => $value['codigo'], 'cod_cobro' => $cobro->getCodigo());
                    $order = array(array('campo' => 'codigo', 'orden' => 'desc'));
                    $imputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion, $condiciones, null, $order);
                    $objimputacion = new Vctacte_imputaciones($conexion, $imputaciones[0]['codigo']);
                    $objimputacion->confirmar($datos['cod_usuario'], $cobro->fechareal);
                }
            }
        }

        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getCajasMedio($cod_user, $cod_medio, $cod_cobro = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $usuarios = new Vusuarios_sistema($conexion, $cod_user);
        $cajas = $usuarios->getCajasMedio($cod_medio, 0);
        if ($cod_cobro != null && $cod_cobro != '') {
            $objCobro = new Vcobros($conexion, $cod_cobro);
            $esta = false;
            for ($i = 0; $i < count($cajas); $i++) {
                if ($cajas[$i]['codigo'] == $objCobro->cod_caja) {
                    $esta = true;
                }
            }
            if (!$esta) {
                $condicion = array('codigo' => $objCobro->cod_caja);
                $caja = Vcaja::listarCaja($conexion, $condicion);
                $cajas[] = $caja[0];
            }
        }
        return $cajas;
    }

    public function getRestaImputar($cod_cobro, $total) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $total_imputaciones = $objCobro->getSumValorImputacionesCobro();
        $total_cobro = str_replace(',', '.', $total);
        $resto = formatearImporte($total_cobro - $total_imputaciones[0]['totImputaciones']);
        return $resto;
    }

    public function getHistoricoCobro($cod_cobro) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $respuesta = array();
        $condicion = array('cod_cobro' => $cod_cobro);
        $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
        $historico = Vcobro_estado_historico::listarCobro_estado_historico($conexion, $condicion, null, $orden);
        foreach ($historico as $value) {
            if ($value['cod_usuario'] != null) {
                $usuario = new Vusuarios_sistema($conexion, $value['cod_usuario']);
                $nombre = $usuario->nombre . ' ' . $usuario->apellido;
            } else {
                $nombre = '-';
            }
            $respuesta[] = array('estado' => lang($value['estado']), 'fecha' => formatearFecha_pais($value['fecha_hora'], true), 'usuario' => $nombre);
        }
        return $respuesta;
    }

    public function facturarCobro($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrResp = array();
        $myCobro = new Vcobros($conexion, $datos['cod_cobro']);
        $myAlumno = new Valumnos($conexion, $myCobro->cod_alumno);
        $arrRazones = $myAlumno->getRazonesAlumno();
        if (count($arrRazones) == 0) {
            $arrResp['codigo'] = 0;
            $arrResp['msgerror'] = lang("la_razon_social_seleccionada_no_corresponde_al_alumno_indicado");
        } else {
            $conexion->trans_start();
            $imputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion, array("cod_cobro" => $datos['cod_cobro'], "tipo" => 'COBRO', "estado <>" => 'anulado'));
            $arrPuntosVenta = $datos['puntos_venta'];
            $arrResp = array();
            foreach ($arrPuntosVenta as $puntoVenta) {
                $totalfactura = 0;
                $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
                $estado = $myPuntoVenta->utilizaWebServices() ? Vfacturas::getEstadoPendiente() : null;
                $porcentajeFacturar = $myPuntoVenta->getPorcentajeFacturar($this->codigo_filial);
                $renglones = array();
                foreach ($imputaciones as $rowimputacion) {
                    $renglon = new Vfacturas_renglones($conexion);
                    $renglon->cod_ctacte = $rowimputacion['cod_ctacte'];
                    $renglon->importe = $rowimputacion['valor'] * $porcentajeFacturar / 100;
                    $renglones[] = $renglon;
                    $totalfactura = $totalfactura + $renglon->importe;
                }
                $objFactura = new Vfacturas($conexion);
                $objFactura->facturar($totalfactura, $myPuntoVenta->getCodigo(), '19', $renglones, null, date('Y-m-d'), $arrRazones[0]['codigo'], $estado);
                $objFactura->asociarCobro($datos['cod_cobro']);
                if ($estado == null || $estado == Vfacturas::getEstadoHabilitado()) {
                    $arrResp[] = $objFactura->getCodigo();
                }
            }
            $estadotran = $conexion->trans_status();
            $conexion->trans_complete();
            $arrResp = class_general::_generarRespuestaModelo($conexion, $estadotran);
        }
        return $arrResp;
    }
}
