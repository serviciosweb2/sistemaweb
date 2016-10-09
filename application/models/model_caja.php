<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_caja extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }

    public function getMovimientosCaja($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null, $fechaDesde = null, $fechaHasta = null, $codCaja = null, $codUser = null, $medioPago = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vmovimientos_caja::getMovimientosCaja($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta, $codCaja, $codUser, $medioPago);
        $registros = Vmovimientos_caja::getMovimientosCaja($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta, $codCaja, $codUser, $medioPago);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    public function get_saldo_por_medios($codigoCaja, $formatear = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $myCaja = new Vcaja($conexion, $codigoCaja);
        $arrSaldos = $myCaja->get_saldos_por_concepto(null, false, false);
        foreach ($arrSaldos as $key => $saldo){
            $saldoApertura = $myCaja->getUltimoSaldo($saldo['codigo'], Vmovimientos_caja::getConceptoApertura());
            $arrSaldos[$key]['saldo_apertura'] = $saldoApertura;
        }
        if ($formatear){
            formatearConceptoCaja($conexion, $arrSaldos);
        }
        return $arrSaldos;
    }

    public function get_saldos_de_cierre($codigoCaja) {
        $conexion = $this->load->database($this->codigofilial, true);
        $myCaja = new Vcaja($conexion, $codigoCaja);
        $arrSaldos = $myCaja->get_saldos_por_concepto(0, true, false);
        for ($i = 0; $i < count($arrSaldos); $i++) {
            $arrSaldos[$i]['medio'] = lang($arrSaldos[$i]['medio']);
            $arrSaldos[$i]['saldo_concepto'] = formatearImporte($arrSaldos[$i]['saldo_concepto']);
        }
        return $arrSaldos;
    }

    public function listarMovimientosDatatable($arrFiltros, $excluirRegistrosApertura = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $arrSearch = array();
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrSearch = array(
                "debe_format" => $arrFiltros["sSearch"],
                "haber_format" => $arrFiltros["sSearch"],
                "medio" => $arrFiltros["sSearch"],
                "user_name" => $arrFiltros['sSearch'],
                "fecha_hora_movimiento" => $arrFiltros['sSearch']
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {

            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        if ($arrFiltros["fechaDesde"] != "" && $arrFiltros["fechaHasta"] != "") {
            $arrCondiciones[] = array('fecha_hora_real >=' => $arrFiltros["fechaDesde"]);
            $arrCondiciones[] = array('fecha_hora_real <=' => $arrFiltros["fechaHasta"]);
        }
        if ($excluirRegistrosApertura) {
            $arrCondiciones[] = array("cod_concepto <>" => Vmovimientos_caja::getConceptoApertura());
        }
        $soloMovimientosDesdeUltimaApertura = $arrFiltros['fechaDesde'] === false && $arrFiltros['fechaHasta'] === false;
        $arrCondiciones[] = array('cod_caja' => $arrFiltros["cod_caja"]);
        $estadoCaja = isset($arrFiltros['estado_caja']) && $arrFiltros['estado_caja'] <> '' ? $arrFiltros['estado_caja'] : null;
        $datos = Vmovimientos_caja::listarMovimientosDataTable($conexion, $arrSearch, $arrLimit, $arrSort, false, $arrCondiciones, $soloMovimientosDesdeUltimaApertura, $estadoCaja);
        $contar = Vmovimientos_caja::listarMovimientosDataTable($conexion, $arrSearch, null, null, true, $arrCondiciones, $soloMovimientosDesdeUltimaApertura, $estadoCaja);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        formatearConceptoCaja($conexion, $datos);
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row['fecha_hora_movimiento'],
                $row["descripcion"],
                $row["haber_format"],
                $row["debe_format"],
                lang($row['medio']),
                $row["user_name"],
                $row['codigo']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    /* ESTA FUNCTION ESTA SIENDO ACCEDIDA DESDE UN WEB SERVICES */
    public function getCajas($codFilial = null, $estado = null) {
        if ($codFilial == null) {
            $conexion = $this->load->database($this->codigofilial, true);
        } else {
            $conexion = $this->load->database($codFilial, true);
        }
        $condiciones = array();
        if ($estado != null) {
            $condiciones['desactivada'] = $estado == '1' ? 0 : 1;
        }
        $arrCajas = Vcaja::listarCaja($conexion, $condiciones);
        return $arrCajas;
    }

    public function getCaja($codigo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objCaja = new Vcaja($conexion, $codigo);
        return $objCaja;
    }

    public function abrirCaja($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_start();
        $caja = new Vcaja($conexion, $datos['cod_caja']);
        $caja->abrir($datos['usuario']);
        $conexion->trans_complete();
        $estadotran = $conexion->trans_status();
        $respuesta = array();
        $respuesta['cajas'] = $data['cajas'] = $this->Model_usuario->getCajas($datos['usuario'], 0);
        $respuesta['caja_abierta'] = $caja->getCodigo();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function cerrarCaja($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_start();
        $codigoUsuario = $datos['codigo_usuario'];
        $caja = new Vcaja($conexion, $datos['codigo_caja']);
        $arrMedios = $caja->getMediosCaja();
        foreach ($arrMedios as $medio) {
            $codigoMedio = $medio['codigo'];
            $valor = isset($datos['valores'][$codigoMedio]) ? $datos['valores'][$codigoMedio]['debe'] : 0;
            $caja->cerrarMedio($valor, $codigoMedio, $codigoUsuario);
        }
        $caja->estado = Vcaja::$estadocerrada;
        $caja->guardarCaja();
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        $respuesta = array();
        $respuesta['cajas'] = $data['cajas'] = $this->Model_usuario->getCajas($datos['codigo_usuario'], 0);
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function guardarMovimiento($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_start();
        $movcaja = new Vmovimientos_caja($conexion);
        $debe = 0;
        $haber = 0;
        if ($datos['movimiento'] == 'entrada') {
            $haber = $datos['importe'];
        } else {
            $debe = $datos['importe'];
        }
        $respuesta = $movcaja->guardar($datos['fecha'], $datos['cod_medio'], $debe, $haber, $datos['observacion'], $datos['usuario'], $datos['cod_caja'], Vmovimientos_caja::getConceptoParticulares(), $datos['subrubro'], null, null);
        //$respuesta = $movcaja->guardar($datos['fecha'], $datos['cod_medio'], $debe, $haber, $datos['observacion'], $datos['usuario'], $datos['cod_caja'], Vmovimientos_caja::getConceptoParticulares(), null, null, null);
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getUltimoMovimiento($codigoCaja, $concepto = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $condiciones = array();
        $condiciones["cod_caja"] = $codigoCaja;
        if ($concepto != null)
            $condiciones["cod_concepto"] = $concepto;
        $orden = array();
        $orden[0]['campo'] = "codigo";
        $orden[0]['orden'] = "DESC";
        $limite = array(0, 1);
        $arrResp = Vmovimientos_caja::listarMovimientos_caja($conexion, $condiciones, $limite, $orden);
        return $arrResp;
    }

    public function guardarTransferencia($cajaOrigen, $cajaDestino, $descripcion, $importe, $medioPago, $coduser) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $myMovimiento1 = new Vmovimientos_caja($conexion);
        $fechaHora = date("Y-m-d H:i:s");
        $myMovimiento1->guardar($fechaHora, $medioPago, $importe, 0, $descripcion, $coduser, $cajaOrigen, Vmovimientos_caja::getConceptoTransferencia());
        $myMovimiento2 = new Vmovimientos_caja($conexion);
        $myMovimiento2->guardar($fechaHora, $medioPago, 0, $importe, $descripcion, $coduser, $cajaDestino, Vmovimientos_caja::getConceptoTransferencia());
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarCaja($data_post) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_start();
        $objCaja = new Vcaja($conexion, $data_post['cod_caja']);
        $objCaja->nombre = $data_post['nombre_caja'];
        $objCaja->estado = $objCaja->getCodigo() <> -1 ? $objCaja->estado : 'cerrada';
        if ($objCaja->estado == "cerrada"){ // no se permite habilitar o deshabilitar cajas cerradas
            $objCaja->desactivada = $data_post['habilitado'] == 'on' ? '0' : '1';
            $objCaja->cod_moneda = $data_post['cod_moneda'];
        }        
        $objCaja->guardarCaja();
        $array = array(
            'codtiposcaja' => $objCaja->getCodigo()
        );
        $objCaja->unSetUsuarioCaja($array);
        $array1 = array(
            'cod_caja' => $objCaja->getCodigo()
        );       

        foreach ($data_post['usuarios_caja'] as $usuario) {
            $UsuarioCaja = array(
                "coduser" => $usuario,
                "codtiposcaja" => $objCaja->getCodigo(),
                "default" => '0'
            );
            $objCaja->setUsuariosCaja($UsuarioCaja);
        }

        if (isset($data_post['medios']['medio']) && $data_post['medios']['medio'] != '') {
            $objCaja->unSetMediosCaja($array1);
            foreach ($data_post['medios']['medio'] as $i => $value) {
                $mediosCaja = array(
                    "cod_caja" => $objCaja->getCodigo(),
                    "cod_medio" => $data_post['medios']['medio'][$i],
                    "cod_apertura" => '0',
                    "entrada_salida" => isset($data_post['medios']['entsal'][$i]) && $data_post['medios']['entsal'][$i] == 'e' ? 0 : 1,
                    "conf_automatica" => isset($data_post['medios']['confir'][$i]) && $data_post['medios']['confir'][$i] ? 1 : 0
                );
                $objCaja->setMedioPagoCaja($mediosCaja);
            }
        }
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getMediosCaja($codCaja, $entradaSalida = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $myCaja = new Vcaja($conexion, $codCaja);
        return $myCaja->getMediosCaja($entradaSalida);
    }

    public function getCajasAbiertas() {
        $conexion = $this->load->database($this->codigofilial, true);
        $condicion = array('estado' => Vcaja::$estadoabierta, 'desactivada' => 0);
        $arrCajas = Vcaja::listarCaja($conexion, $condicion);
        return $arrCajas;
    }

    public function getMediosPagosConfiguracion($cod_caja) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrRespuesta = array();
        $filial = new Vfiliales($conexion, $this->codigofilial);
        $myCaja = new Vcaja($conexion, $cod_caja);
        $mediosasignados = $myCaja->getMediosPago();
        $medios = Vmedios_pago::listarMedios_pago($conexion, null, null, null, null, null, $filial->pais);
        foreach ($medios as $rowmedio) {
            $esta = false;
            $ent_sal = 0;
            $conf_auto = 0;
            foreach ($mediosasignados as $value) {
                if ($rowmedio['codigo'] == $value['cod_medio']) {
                    $esta = true;
                    $ent_sal = $value['entrada_salida'];
                    $conf_auto = $value['conf_automatica'];
                }
            }
            $habilitado = $esta ? 1 : 0;
            $arrRespuesta[] = array('codigo' => $rowmedio['codigo'], 'medio' => lang($rowmedio['medio']), 'habilitado' => $habilitado, 'ent_sal' => $ent_sal, 'conf_auto' => $conf_auto);
        }
        return $arrRespuesta;
    }

    public function getCajasMedio($cod_user, $cod_medio, $desactivada = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $usuarios = new Vusuarios_sistema($conexion, $cod_user);
        $cajas = $usuarios->getCajasMedio($cod_medio, $desactivada);
        return $cajas;
    }
}