<?php

/**
 * Class Vfiliales
 *
 * Class  Vfiliales maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vfacturantes extends Tfacturantes {

    static private $estadoHabiitado = "habilitado";
    static private $estadoInhabilitado = "inhabilitado";
    public $cod_sesion = null;

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getCertificado() {
        $myCertificado = new Vfacturantes_certificados($this->oConnection, $this->codigo);
        return $myCertificado;
    }

    public function getRazonSocial() {
        $this->oConnection->select("*");
        $this->oConnection->from("general.razones_sociales_general");
        $this->oConnection->where("codigo", $this->cod_razon_social);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getFiliales() {
        $this->oConnection->select("cod_filial");
        $this->oConnection->from("general.facturantes_filiales");
        $this->oConnection->where("cod_facturante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getTiposFacturas($puntoVenta = null, $usuario = null, $condicionFacturado = null, $habilitado = null, $soloPuntosVentaHabilitado = false) {
        $codFilial = $this->oConnection->database;
        $this->oConnection->select("general.tipos_facturas.*", false);
        $this->oConnection->select("general.puntos_venta.nro");
        $this->oConnection->select("general.puntos_venta.codigo AS cod_punto_venta");
        $this->oConnection->select("general.puntos_venta.prefijo AS prefijo");
        $this->oConnection->from("general.puntos_venta");
        $this->oConnection->join("general.tipos_facturas", "general.tipos_facturas.codigo = general.puntos_venta.tipo_factura");
        if ($condicionFacturado != null) {
            $this->oConnection->join("general.facturantes", "general.facturantes.codigo = general.puntos_venta.cod_facturante");
            $this->oConnection->join("general.razones_sociales_general", "general.razones_sociales_general.codigo = general.facturantes.cod_razon_social");
            $this->oConnection->join("general.condiciones_facturacion", "general.condiciones_facturacion.cod_cond_facturante = general.razones_sociales_general.condicion" .
                    " AND general.condiciones_facturacion.cod_cond_facturado = $condicionFacturado AND general.condiciones_facturacion.cod_tipo_factura = general.puntos_venta.tipo_factura");
        }
        if ($usuario != null) {
            $this->oConnection->join("usuarios_puntos_venta", "usuarios_puntos_venta.cod_punto_venta = general.puntos_venta.codigo AND usuarios_puntos_venta.cod_usuario = $usuario");
        }
        $this->oConnection->join("general.puntos_venta_filiales", "general.puntos_venta_filiales.cod_punto_venta = general.puntos_venta.codigo AND general.puntos_venta_filiales.cod_filial = $codFilial");
        if ($habilitado !== null) {
            $this->oConnection->where("general.tipos_facturas", $habilitado);
        }
        $this->oConnection->where("puntos_venta.cod_facturante", $this->codigo);
        if ($puntoVenta != null) {
            $this->oConnection->where("general.puntos_venta.codigo", $puntoVenta);
        }
        if ($soloPuntosVentaHabilitado) {
            $this->oConnection->where("general.puntos_venta.estado", "habilitado");
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getPuntosVenta() {
        $this->oConnection->select("general.puntos_venta.*", false);
        $this->oConnection->from("general.puntos_venta");
        $this->oConnection->where("general.puntos_venta.cod_facturante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getFacturantes(CI_DB_mysqli_driver $conexion, $codFilial = null, $baja = 0, $todasLasFiliales = false, $facturanteMatriz = null, $conPuntosVenta = false, $estado = null, $pais = false) {
        $conexion->select('general.facturantes.codigo AS codigofacturante');
        $conexion->select('general.facturantes.inicio_actividades');
        $conexion->select('general.razones_sociales_general.*', false);
        $conexion->from('general.facturantes');
        if (!$todasLasFiliales) {
            $conexion->join("general.facturantes_filiales", "general.facturantes_filiales.cod_facturante = general.facturantes.codigo AND general.facturantes_filiales.cod_filial = $codFilial");
        }
        $conexion->join('general.razones_sociales_general', 'general.facturantes.cod_razon_social = general.razones_sociales_general.codigo');
        if ($conPuntosVenta) {
            $conexion->join("general.puntos_venta", "general.puntos_venta.cod_facturante = facturantes.codigo");
        }
        $conexion->where('general.razones_sociales_general.baja', $baja);
        if ($facturanteMatriz !== null) {
            if ($facturanteMatriz)
                $conexion->where("general.facturantes.cod_facturante_matriz IS NULL");
            else
                $conexion->where("general.facturantes.cod_facturante IS NOT NULL");
        }
        if ($estado != null)
            $conexion->where("general.facturantes.estado", $estado);
        if ($pais) {
            $conexion->select('(SELECT pais FROM general.provincias JOIN general.localidades ON general.localidades.provincia_id = general.provincias.id WHERE general.localidades.id = general.razones_sociales_general.cod_localidad) as cod_pais');
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getCuentasBoletoBancario() {
        $this->oConnection->select("*");
        $this->oConnection->from("bancos.cuentas_boletos_bancarios");
        $this->oConnection->where("cod_facturante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getArchivosRemessa($cod_filial, $contar = false) {
        $this->oConnection->select("remesas.*");
        $this->oConnection->from("bancos.remesas");
        $this->oConnection->join("bancos.boletos_bancarios", "boletos_bancarios.cod_remesa = remesas.codigo");
        $this->oConnection->where("remesas.cod_facturante", $this->codigo);
        $this->oConnection->where("remesas.enviada", 0);
        $this->oConnection->where("boletos_bancarios.cod_filial", $cod_filial);
        $this->oConnection->group_by("remesas.codigo");
        $this->oConnection->order_by("remesas.codigo", "DESC");
        if ($contar) {
            return $this->oConnection->count_all_results();
        } else {
            $query = $this->oConnection->get();
            return $query->result_array();
        }
    }




    public function getArchivosRemessaEnviados($cod_filial, $contar = false) {
        $this->oConnection->select("remesas.*");
        $this->oConnection->from("bancos.remesas");
        $this->oConnection->join("bancos.boletos_bancarios", "boletos_bancarios.cod_remesa = remesas.codigo");
        $this->oConnection->where("remesas.cod_facturante", $this->codigo);
        $this->oConnection->where("remesas.enviada", 1);
        $this->oConnection->where("boletos_bancarios.cod_filial", $cod_filial);
        $this->oConnection->group_by("remesas.codigo");
        $this->oConnection->order_by("remesas.codigo", "DESC");
        if ($contar) {
            return $this->oConnection->count_all_results();
        } else {
            $query = $this->oConnection->get();
            return $query->result_array();
        }
    }



    public function getArchivosRetorno($cod_filial, $contar = false) {
        $this->oConnection->select("retornos.*");
        $this->oConnection->from("bancos.retornos");
        $this->oConnection->where("retornos.cod_facturante", $this->codigo);
        $this->oConnection->order_by("id", "DESC");
        if ($contar) {
            return $this->oConnection->count_all_results();
        } else {
            $query = $this->oConnection->get();
            return $query->result_array();
        }
    }

    static function getContratosPosFacturantes(CI_DB_mysqli_driver $conexion, $cod_filial) {
        $conexion->select('tarjetas.pos_contratos.codigo AS cod_contrato, tarjetas.pos_contratos.estado AS estado_contrato', false);
        $conexion->select('tarjetas.pos_operadores.nombre, tarjetas.pos_puntos_venta.codigo, tarjetas.pos_puntos_venta.estado AS estado_ptovta', false);
        $conexion->select('general.razones_sociales_general.razon_social AS facturante', false);
        $conexion->from('tarjetas.pos_contratos');
        $conexion->join('tarjetas.pos_puntos_venta', 'tarjetas.pos_puntos_venta.cod_contrato = tarjetas.pos_contratos.codigo');
        $conexion->join('general.facturantes', 'tarjetas.pos_puntos_venta.cod_facturante = general.facturantes.codigo');
        $conexion->join('general.facturantes_filiales', 'general.facturantes_filiales.cod_facturante = general.facturantes.codigo');
        $conexion->join('general.razones_sociales_general', 'general.facturantes.cod_razon_social = general.razones_sociales_general.codigo');
        $conexion->join('tarjetas.pos_operadores', 'tarjetas.pos_operadores.codigo = tarjetas.pos_contratos.cod_operador');
        $conexion->where('general.facturantes_filiales.cod_filial', $cod_filial);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getArchivoResumenCobros(array $arrSearch = null, array $arrSort = null, array $arrLimit = null) {
        $archivos = array();
        $arrContratosFacturantes = $this->getContratosFacturante();
        foreach ($arrContratosFacturantes as $contratos) {
            $operador = new Vpos_operadores($this->oConnection, $contratos["cod_operador"]);
            $archivos[] = $operador->getHeaderCV($arrSearch, $arrSort, $arrLimit);
        }
        return $archivos;
    }

    public function getPuntosVentaFacturante() {
        return Vpos_puntos_venta::listarPos_puntos_venta($this->oConnection, array("cod_facturante" => $this->codigo));
    }

    public function getContratosFacturante() {
        return Vpos_contratos::listarPos_contratos($this->oConnection, array("cod_facturante" => $this->codigo, "estado" => "habilitado"));
    }

    static function getEstadoHabilitado() {
        return self::$estadoHabiitado;
    }

    static function getEstadoInhabilitado() {
        return self::$estadoInhabilitado;
    }

    public function generarCsr() {
        $pkcs10 = null;
        $certificado = $this->getCertificado();
        if (is_null($certificado->pry_key)) {
            $certificado->pry_key = WSAA::generatePrivateKey();
            if (!$certificado->guardar()) {
                die('Error al gurdar la clave privada.');
            }
        }
        if (($razon_social = $this->getRazonSocial()) !== null) {
            if ( !empty($razon_social[0]['tipo_documento']) && $razon_social[0]['tipo_documento'] == 3 && !empty($razon_social[0]['documento']) && validaciones::validarDocumentoIdentidad($razon_social[0]['documento'], 3)) {
                $documento = str_replace(array('-',"."," "),array(''),$razon_social[0]['documento']);
                $dn = array(
                    'countryName' => 'AR',
                    'organizationName' => $razon_social[0]['razon_social'],
                    'commonName' => 'IgaCloud',
                    'serialNumber' => 'CUIT ' . $documento
                );
                $pkcs10 = WSAA::generateCsr($certificado->pry_key, null, $dn);
            } else {
                return false;
            }
        }
        return $pkcs10;
    }

    public function registrarCrt($str_crt) {
        $resultado = false;
        $certificado = $this->getCertificado();
        if ($certificado->getExists()) {
            $certificado->cert = $str_crt;
            $info = openssl_x509_parse($certificado->cert);
            $certificado->fecha_expiracion = date('Y-m-d', $info['validTo_time_t']);
            $sesion = Vsesiones_afip::iniciarSesionAfip($this->oConnection, $this->codigo, $certificado, false);
            if ($certificado->guardar()) {
                $resultado = true;
            }
        }
        return $resultado;
    }

    private function _getWebServiceAfipSesion($testing=true) {
        $sesion = null;
        $sesiones = Vsesiones_afip::getSesionAfipFacturanteActiva($this->oConnection, $this->codigo);
        if (empty($sesiones)) {
            $certificado = $this->getCertificado();
            if ($certificado->getExists() && !empty($certificado->pry_key) && !empty($certificado->cert) && !empty($certificado->fecha_expiracion)) {
                $expiracion = DateTime::createFromFormat('Y-m-d', $certificado->fecha_expiracion)->getTimestamp();
                if ($expiracion > time()) {
                    $sesion = Vsesiones_afip::iniciarSesionAfip($this->oConnection, $this->codigo, $certificado, $testing);
                }
            }
        } else {
            $sesion = new Vsesiones_afip($this->oConnection, $sesiones[0]['codigo']);
        }
        return $sesion;
    }

    public function getWebServiceAfip($testing=true) {
        $resultado = null;
        if (($razon_social = $this->getRazonSocial()) !== null) {
            $sesion = $this->_getWebServiceAfipSesion($testing);
            if (!empty($sesion)) {
                $config = array(
                    'testing' => $testing,
                    'file_wsdl' => 'application/libraries/facturas/PhpWsAfip/tmp/wsfe_wsdl.xml'
                );
                $wsfe = new WSFE($config);
                $wsfe->setTaExpirationTime(strtotime($sesion->expirationTime));
                $wsfe->setTaCuit($razon_social[0]['documento']);
                $wsfe->setTaToken($sesion->token);
                $wsfe->setTaSign($sesion->sign);
                $resultado = $wsfe;
                $this->cod_sesion = $sesion->getCodigo();
            }
        }
        return $resultado;
    }

    public function actualizarPuntosVentaElectronico($testing=true) {
        $retorno = array();
        $retorno['comentarios'] ='';
        $retorno['mod_numero'] ='';
        $retorno['mod_estado'] ='';
        $retorno['nuevos'] ='';
        $condiciones = array("cod_facturante" => $this->codigo, "medio" => "electronico");
        $puntos_actuales = Vpuntos_venta::listarPuntos_venta($this->oConnection, $condiciones);
        try {
            $wsfe = $this->getWebServiceAfip($testing);
            $puntos_venta = $wsfe->FEParamGetPtosVenta();
            //los numeros de punto de venta (prefijo)
            $arrPuntosdeVentaAFIP = array();
            if(isset($puntos_venta->FEParamGetPtosVentaResult->ResultGet)){
                $arrPuntosdeVentaAFIP  = $puntos_venta->FEParamGetPtosVentaResult->ResultGet;
                $arrayNuevo[0] = $arrPuntosdeVentaAFIP->PtoVenta;
                if(count($arrPuntosdeVentaAFIP->PtoVenta) === 1){
                         $arrPuntosdeVentaAFIP->PtoVenta = $arrayNuevo ;
                }
            }  else {
                $retorno['comentarios'] = utf8_encode($puntos_venta->FEParamGetPtosVentaResult->Errors->Err->Msg);
            }
            if(isset($arrPuntosdeVentaAFIP->PtoVenta)){
                foreach ($arrPuntosdeVentaAFIP->PtoVenta as $rowptovta) {
                    $prefijo = $rowptovta->Nro;
                    $bloqueado = $rowptovta->Bloqueado == 'N' ? FALSE : TRUE;
                    $baja = $rowptovta->FchBaja == 'NULL' ? FALSE : TRUE;
                    //comprobantes habilitados para el punto de venta
                    $comprobantes = $wsfe->FEParamGetTiposCbte();
                    foreach ($comprobantes->FEParamGetTiposCbteResult->ResultGet->CbteTipo as $rowcbte) {
                        $cod_afip_tipo = $rowcbte->Id;
                        //ver si esta en tipos facturas
                        $tipos = Vtipos_facturas::listarTipos_facturas($this->oConnection, array('cod_afip' => $cod_afip_tipo));
                        $cbte_habilitado = count($tipos) > 0 ? true : false;
                        if ($cbte_habilitado) {
                            $cod_tipo_factura = $tipos[0]['codigo'];
                            //ultimo numero emitido
                            $ultimo_comprobante = $wsfe->FECompUltimoAutorizado(array('PtoVta' => $prefijo, 'CbteTipo' => $cod_afip_tipo));
                            //ver si ya existe o hay que modificarlo
                            $numero_ultimo_comprobante = $ultimo_comprobante->FECompUltimoAutorizadoResult->CbteNro;
                            $esta = false;
                            foreach ($puntos_actuales as $value) {
                                if ($value['prefijo'] == $prefijo && $value['tipo_factura'] == $cod_tipo_factura) {//esta creado el punto de venta
                                    $esta = true;
                                    $objPtoVenta = new Vpuntos_venta($this->oConnection, $value['codigo']);
                                    if ($value['nro'] - 1 != ($numero_ultimo_comprobante)) {
                                        //tiene que modificar el nro del ultimo comprobante
                                        $objPtoVenta->nro = $numero_ultimo_comprobante + 1;
                                        $respuesta= $objPtoVenta->guardarPuntos_venta();
                                        if ($respuesta) {
                                            $retorno['mod_numero'][] = $objPtoVenta;
                                        }
                                    }
                                    if ($baja || $bloqueado) {
                                        //dar de baja al punto de venta
                                        $objPtoVenta->estado = 'inhabilitado';
                                        $respuesta=$objPtoVenta->guardarPuntos_venta();
                                        if ($respuesta) {
                                            $retorno['mod_estado'][] = $objPtoVenta;
                                        }
                                    }
                                }
                            }
                            if (!$esta && !$baja && !$bloqueado) {
                                //crear el punto de venta para el comprobante y asignarlo a las filiales del facturante
                                $newPtoVenta = new Vpuntos_venta($this->oConnection, null);
                                $newPtoVenta->cod_facturante = $this->codigo;
                                $newPtoVenta->medio = 'electronico';
                                $newPtoVenta->tipo_factura = $cod_tipo_factura;
                                $newPtoVenta->nro = $numero_ultimo_comprobante + 1;
                                $newPtoVenta->prefijo = $prefijo;
                                $newPtoVenta->webservice = 1;
                                $respuesta=$newPtoVenta->guardarPuntos_venta();
                                if ($respuesta) {
                                    $retorno['nuevos'][] = $newPtoVenta;
                                }
                                $filiales = $this->getFiliales();
                                $arrfiliales = array();
                                foreach ($filiales as $rowfilial) {
                                    $arrfiliales[] = $rowfilial['cod_filial'];
                                }
                                $newPtoVenta->setFiliales($arrfiliales);
                            }
                        }
                    }
                }
            }
        } catch (Exception $exc) {
            $retorno['comentarios'] = $exc->getMessage();
            $retorno['codigo'] = 1;
        }
        return $retorno;
    }
}
