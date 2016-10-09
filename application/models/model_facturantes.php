<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_facturantes extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function guardarFacturante($data_post) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $objRazonSocial = new Vrazones_sociales($conexion, $data_post['cod_razon_social']);
        $arrayGuardarRazonSocial = array(
            "razon_social" => $data_post['razon'],
            "documento" => $data_post['numero_Doc'],
            "condicion" => $data_post['condicion'],
            "baja" => 0,
            "tipo_documentos" => $data_post['tipo_doc'],
            "cod_localidad" => $data_post['localidad'],
            "direccion_calle" => $data_post['direccion'],
            "direccion_numero" => $data_post['numero'],
            "direccion_complemento" => $data_post['complemento'],
            "estado" => $data_post['estado']
        );
        $objRazonSocial->setRazones_sociales($arrayGuardarRazonSocial);
        $objRazonSocial->guardarRazones_sociales();
        $objFacturante = new Vfacturantes($conexion, $data_post['cod_facturante']);
        $arrayGuardarFacturante = array(
            "cod_razon_social" => $objRazonSocial->getCodigo(),
            "inicio_actividades" => $data_post['inicioActividad'],
        );
        $objFacturante->setFacturantes($arrayGuardarFacturante);
        $objFacturante->guardarFacturantes();
        $objTelefono = new Vtelefonos($conexion, $data_post['cod_telefono']);
        $arrTelefono = array(
            "cod_area" => $data_post['cod_area'],
            "numero" => $data_post['numero'],
            "tipo_telefono" => 'fijo',
            "empresa" => $data_post['empresa'],
            "baja" => 0
        );
        $objTelefono->setTelefonos($arrTelefono);
        $objTelefono->guardarTelefonos();
        if ($data_post['cod_telefono'] == -1) {
            $objRazonSocial->setTelefonoRazon($objTelefono->getCodigo());
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getFacturante($cod_facturante) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objFacturante = new Vfacturantes($conexion, $cod_facturante);
        return $objFacturante;
    }

    public function listarFacturantesRazones() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrResp = Vfacturantes::getFacturantes($conexion, $this->codigo_filial);
        return $arrResp;
    }

    public function getFacturantes($conPuntosVenta = true, $conpais = false, $validcertificado = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $baja = 0;
        $facturantes = Vfacturantes::getFacturantes($conexion, $this->codigo_filial, $baja, false, null, $conPuntosVenta, Vfacturantes::getEstadoHabilitado(), $conpais);
        foreach ($facturantes as $key => $valor) {
            $facturantes[$key]['inicio_actividades'] = formatearFecha_pais($valor['inicio_actividades']);
            if ($validcertificado) {
                $certificado = new Vfacturantes_certificados($conexion, $valor['codigofacturante']);
                $facturantes[$key]['tiene_certificado'] = !empty($certificado->cert);
            }
        }
        return $facturantes;
    }

    public function getRemesas($arrFiltros) {
        $conexion = $this->load->database('', true);
        $baja = 0;
        $facturantes = Vfacturantes::getFacturantes($conexion, $this->codigo_filial, $baja);
        $archivosRemessa = array();
        foreach ($facturantes as $facturante) {
            $facturante = new Vfacturantes($conexion, $facturante["codigofacturante"]);
            $archivoRemessaFacturante = $facturante->getArchivosRemessa($this->codigo_filial);
            foreach ($archivoRemessaFacturante as $archivo) {
                $archivosRemessa[] = $archivo;
            }
        }
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => count($archivosRemessa),
            "iTotalDisplayRecords" => count($archivosRemessa),
            "aaData" => array()
        );

        $rows = array();
        foreach ($archivosRemessa as $row) {
            $rows[] = array(
                $row["codigo"],
                $row["cedente_convenio"],
                $row["fecha_documento"],
                "",
                ""
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }


    public function getRemesasEnviadas($arrFiltros) {
        $conexion = $this->load->database('', true);
        $baja = 0;
        $facturantes = Vfacturantes::getFacturantes($conexion, $this->codigo_filial, $baja);
        $archivosRemessa = array();
        foreach ($facturantes as $facturante) {
            $facturante = new Vfacturantes($conexion, $facturante["codigofacturante"]);
            $archivoRemessaFacturante = $facturante->getArchivosRemessaEnviados($this->codigo_filial);
            foreach ($archivoRemessaFacturante as $archivo) {
                $archivosRemessa[] = $archivo;
            }
        }
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => count($archivosRemessa),
            "iTotalDisplayRecords" => count($archivosRemessa),
            "aaData" => array()
        );

        $rows = array();
        foreach ($archivosRemessa as $row) {
            $rows[] = array(
                $row["codigo"],
                $row["cedente_convenio"],
                $row["fecha_documento"],
                "",
                ""
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }


    public function getRetornos($arrFiltros) {
        $conexion = $this->load->database('', true);
        $baja = 0;
        $facturantes = Vfacturantes::getFacturantes($conexion, $this->codigo_filial, $baja);
        $archivosRetorno = array();
        foreach ($facturantes as $facturante) {
            $facturante = new Vfacturantes($conexion, $facturante["codigofacturante"]);
            $archivoRetornoFacturante = $facturante->getArchivosRetorno($this->codigo_filial);
            foreach ($archivoRetornoFacturante as $archivo) {
                $archivosRetorno[] = $archivo;
            }
        }
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => count($archivosRetorno),
            "iTotalDisplayRecords" => count($archivosRetorno),
            "aaData" => array()
        );

        $rows = array();
        foreach ($archivosRetorno as $row) {
            $rows[] = array(
                $row["archivo_secuencia"],
                $row["nombre_archivo_usuario"],
                $row["fecha_retorno"],
                $row["secuencia_verificada"]
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getRemesa($codigo) {
        $conexion = $this->load->database('', true);
        $this->load->helper('strings');
        $remesa = new Vremesas($conexion, $codigo);
        $respuesta = array();
        if ($remesa->esDeFilial($this->codigo_filial) == true) {
            $respuesta = array("codigo" => 1, "respuesta" => $remesa->generarArchivoRemessa());
        } else {
            $respuesta = array("codigo" => 0);
        }
        return $respuesta;
    }

    public function getConfirmarcionRetorno($archivo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $repuesta["codigo"] = 1;
        $repuesta["respuesta"] = "";
        try {
            $retorno = new Vretornos($conexion, $archivo);
            $boletos = $retorno->boletosArchivo;
            $banco = new Vbancos($conexion, $retorno->cod_banco);
            $cuentaBanco = $banco->getCuentaBanco($retorno->cod_configuracion);
            $arrBoletos = array();
            foreach ($boletos as $boleto) {
                $nombreSacado = "";
                $msgError = "";
                $arrOcurrencia = array();
                try {
                    $ObjetoBoleto = new Vboletos_bancarios($conexion, null, $boleto["T"]->numero_seguimiento);
                    $nombreSacado = $ObjetoBoleto->sacado_nombre;
                } catch (Exception $exc) {
                    $msgError = $exc->getMessage();
                }
                $arrOcurrencia = $cuentaBanco->getDescripcionRetorno($boleto["U"]->servicio_codigo_movimiento, $boleto["T"]->motivo_ocurrencia);
                $arrBoletos[] = array(
                    "nosso_numero" => $boleto["T"]->nosso_numero,
                    "sacado_nombre" => $nombreSacado,
                    "valor_titulo" => $boleto["T"]->valor_titulo,
                    "valor_titulo_pago" => $boleto["U"]->valor_titulo_pago,
                    "motivo" => $arrOcurrencia["motivo"],
                    "descripcion" => $arrOcurrencia["descripcion"],
                    "estadointerono" => $msgError
                );
            }
            $repuesta["respuesta"] = "OK";
            $repuesta["boletos"] = $arrBoletos;
        } catch (Exception $exc) {
            $repuesta["codigo"] = 0;
            $repuesta["respuesta"] = $exc->getMessage();
        }
        return $repuesta;
    }

    public function confirmarRetorno($archivo, $nombreArchivoUsuario, $filial = null, $usarFilial = false) {
        $conexion = null;
        if($usarFilial)
            $conexion = $this->load->database($filial, true);
        else
            $conexion = $this->load->database($this->codigo_filial, true);

            
        $repuesta["codigo"] = 1;
        $repuesta["respuesta"] = "";
        try {
            $retorno = new Vretornos($conexion, $archivo);
            $repuesta["boletos"] = $retorno->ProcessarArchivo($this->session->userdata('codigo_usuario'), $nombreArchivoUsuario, $filial, $usarFilial);
        } catch (Exception $exc) {
            $repuesta["codigo"] = 0;
            $repuesta["respuesta"] = $exc->getMessage();
        }
        return $repuesta;
    }

    public function getContratosPosFacturantes() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $contratosPos = Vfacturantes::getContratosPosFacturantes($conexion, $this->codigo_filial);
        return $contratosPos;
    }

    public function getAchivosResumenCobros(array $arrSearch = null, array $arrSort = null, array $arrLimit = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $filial = new Vfiliales($conexion, $this->codigo_filial);
        $arrayArchivos = array();
        $arrFacturantes = $filial->getFacturantes();
        $cantidadTotal = 0;
        foreach ($arrFacturantes as $facturante) {
            $facturante = new Vfacturantes($conexion, $facturante["codigo"]);
            $arrResumen = $facturante->getArchivoResumenCobros($arrSearch, $arrSort, $arrLimit);
            $arrResumen1 = $facturante->getArchivoResumenCobros();
            if (isset($arrResumen1[0])) {
                $cantidadTotal += count($arrResumen1[0]);
            }
            foreach ($arrResumen as $resumen) {
                if (count($resumen) != 0) {
                    foreach ($resumen as $r) {
                        $arrayArchivos[] = $r;
                    }
                }
            }
        }
        $retorno = array(
            "iTotalRecords" => $cantidadTotal,
            "iTotalDisplayRecords" => $cantidadTotal,
            "aaData" => array()
        );
        $rows = array();
        foreach ($arrayArchivos as $row) {
            $rows[] = array(
                $row['nombre_archivo'],
                $row["establecimiento_matriz"],
                $row["secuencia"],
                $row["periodo_inicial"],
                $row["periodo_final"],
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function sendArchivoResumenCobro($archivo, $proveedor, $archivoOriginal) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $operadores = new Vpos_operadores($conexion, $proveedor);
        return $operadores->ProcesarResumen($archivo, $this->codigo_filial, $archivoOriginal);
    }

    public function getCuentasBoleto($facturante) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $facturantes = new Vfacturantes($conexion, $facturante);
        return $facturantes->getCuentasBoletoBancario();
    }

    public function getCertificado($facturante, $activo = true) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myCertificado = new Vfacturantes_certificados($conexion, $facturante);
        if ($activo) {
            if ($myCertificado->fecha_expiracion > date('Y-m-d') && $myCertificado->cert != '' && $myCertificado->cert != NULL) {
                return $myCertificado;
            } else {
                return false;
            }
        } else {
            return $myCertificado;
        }
    }

    public function actualizarPuntosVenta() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $respuesta =array();
        $facturantes = Vfacturantes::getFacturantes($conexion, $this->codigo_filial, 0, false, null, false, null, true);
        foreach ($facturantes as $rowfacturante) {
            $objcertificado = new Vfacturantes_certificados($conexion, $rowfacturante['codigofacturante']);
            if ($objcertificado->getActivo() && $rowfacturante['cod_pais'] == '1') {
                $objfacturante = new Vfacturantes($conexion, $rowfacturante['codigofacturante']);
                $respuesta[] = array('facturante'=> $objfacturante->getCodigo(), 'razon' => $rowfacturante['razon_social'], 'resultado'=> $objfacturante->actualizarPuntosVentaElectronico($this->config->item('ws_afip_testing')));
            }
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function moverAFTP($ids){
        //Necesito primero tener los archivos que voy a mover al ftp
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('strings');
        $fechas = array();
        $conexion->trans_begin();
        $estado = true;
        $archivos = array();
        foreach($ids as $id){
            $remesa = new Vremesas($conexion, $id);
            $ret = $remesa->enviar();
            if($ret === false){
                $estado = false;
                break;
            } else {
                $archivos[] = $ret;
            }
        }
        if($estado){
            $estadotran = $conexion->trans_status();
            if ($estadotran === FALSE) {
                $conexion->trans_rollback();
                return false;
            } else {
                $conexion->trans_commit();
                return true;
            }
        } else {
            $conexion->trans_rollback();
            return false;
        }
        
    }

    public function moverTodoAFTP(){
        $conexion = $this->load->database('', true);
        $baja = 0;
        $facturantes = Vfacturantes::getFacturantes($conexion, $this->codigo_filial, $baja);
        $archivosRemessa = array();
        foreach ($facturantes as $facturante) {
            $facturante = new Vfacturantes($conexion, $facturante["codigofacturante"]);
            $archivoRemessaFacturante = $facturante->getArchivosRemessa($this->codigo_filial);
            foreach ($archivoRemessaFacturante as $archivo) {
                $archivosRemessa[] = $archivo['codigo'];
            }
        }
        return $this->moverAFTP($archivosRemessa);
    }

}
