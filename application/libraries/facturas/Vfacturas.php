<?php

/**
 * Class Vfacturas
 *
 * Class  Vfacturas maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vfacturas extends Tfacturas {

    static private $estadoHabilitada = "habilitada";
    static private $estadoInhabilitada = "inhabilitada";
    static private $estadoPendiente = "pendiente";
    static private $estadoArchivoCreado = "archivo_creado";
    static private $estadoEnviado = "enviado";
    static private $estadoError = "error";
    static private $estadoPendienteCancelar = "pendiente_cancelar";
    static private $propiedadNumeroFactura = "numero_factura";
    static private $propiedadNumeroRps = "numero_rps";

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarFacturasDataTable(CI_DB_mysqli_driver $conexion, array $arrCondindicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, array $facturasIN = null, array $condiciones = null) {
        $conexion->select("facturas_propiedades.valor");
        $conexion->from("facturas_propiedades");
        $conexion->where("facturas_propiedades.cod_factura = facturas.codigo");
        $conexion->where("facturas_propiedades.valor IS NOT NULL");
        $conexion->where_in("facturas_propiedades.propiedad", array("numero_factura", "numero_rps"));
        $conexion->order_by("facturas_propiedades.valor", "desc");
        $conexion->limit(1);
        $sqNroFact = $conexion->return_query();
        $conexion->resetear();

        $conexion->select('general.tipos_facturas.factura');
        $conexion->select('facturas.codigo');
        $conexion->select('facturas.estado');
        $conexion->select("($sqNroFact) AS nrofact", false);
        $conexion->select('facturas.fecha');
        $conexion->select('facturas.total');
        $conexion->select('razones_sociales.razon_social');
        $conexion->select('puntos_venta.tipo_factura AS cod_tipo_factura');
        $conexion->select('general.puntos_venta.prefijo as punto_venta');
        $conexion->select('general.puntos_venta.codigo as cod_punto_venta');
        $conexion->select('razones_sociales.email');
        $conexion->select('general.puntos_venta.cod_facturante');
        $conexion->select("concat( general.documentos_tipos.nombre, ' ',razones_sociales.documento ) as documento", false);
        $conexion->from('facturas');
        $conexion->join('razones_sociales', 'razones_sociales.codigo = facturas.codrazsoc');
        $conexion->join('general.puntos_venta', 'general.puntos_venta.codigo = facturas.punto_venta');
        $conexion->join('general.tipos_facturas', 'general.tipos_facturas.codigo = puntos_venta.tipo_factura');
        $conexion->join('general.documentos_tipos', 'general.documentos_tipos.codigo = razones_sociales.tipo_documentos');

        if ($condiciones != null && count($condiciones) > 0) {
            $conexion->where($condiciones);
        }

        if ($facturasIN != null) {
            $conexion->where_in("facturas.codigo", $facturasIN);
        }

        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value) {

                $arrTemp[] = "$key LIKE '%$value%'";
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != NULL) {

            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        $query = $conexion->get();

        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    
    public function getImporte(){
        $this->oConnection->select("IFNULL(SUM(importe), 0) AS total", false);
        $this->oConnection->from("facturas_renglones");
        $this->oConnection->where("cod_factura", $this->codigo);
        $query = $this->oConnection->get();
        $temp = $query->result_array();
        return isset($temp[0], $temp[0]['total']) ? $temp[0]['total'] : 0;
    }
    
    public function setEstado($nuevoEstado) {
        return $this->oConnection->update($this->nombreTabla, array("estado" => $nuevoEstado), "codigo = $this->codigo");
    }

    public function getPropiedad($propiedad) {
        $this->oConnection->select("valor");
        $this->oConnection->from("facturas_propiedades");
        $this->oConnection->where("propiedad", $propiedad);
        $this->oConnection->where("cod_factura", $this->codigo);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        if (isset($arrResp[0]['valor']))
            return $arrResp[0]['valor'];
        else
            return '';
    }

    public function getRazon() {
        return $razonSocial = new Vrazones_sociales($this->oConnection, $this->codrazsoc);
    }

    public function setPropiedad($propiedad, $valor) {
        return $this->oConnection->insert("facturas_propiedades", array("cod_factura" => $this->codigo,
                    "propiedad" => $propiedad,
                    "valor" => $valor));
    }

    public function facturar($total, $puntoventa, $codusuario, $arrenglones, $codalumno = null, $fecha = null, $razonsocial = null, $estado = null, $pais = null) {
        $myPuntoVenta = new Vpuntos_venta($this->oConnection, $puntoventa);
        $numeroFactura = $myPuntoVenta->nro;
        $estado == null ? self::$propiedadNumeroFactura : $estado;
        if ($razonsocial == null) {
            $alumno = new Valumnos($this->oConnection, $codalumno);
            $razondefault = $alumno->getRazonSocialDefaultFacturar();
        }
        $this->fecha = $fecha == null ? date('Y-m-d') : $fecha;
        $this->fechareal = date('Y-m-d H:i:s');
        $this->cod_usuario = $codusuario;
        $this->codrazsoc = $razonsocial == null ? $razondefault[0]['cod_razon_social'] : $razonsocial;
        $this->total = $total;
        $this->punto_venta = $puntoventa;
        $this->estado = $estado;
        $this->guardarFacturas();
        $condicion = array('cod_punto_venta' => $puntoventa);

        $arrGinfes = Vprestador_ginfes::listarPrestador_ginfes($this->oConnection, $condicion);
        $arrDsf = Vprestador_ginfes::listarPrestador_ginfes($this->oConnection, $condicion);
        $arrPaulistana = Vprestador_paulistana::listarPrestador_paulistana($this->oConnection, $condicion);

        foreach ($arrenglones as $objrenglon) {
            $objrenglon->cod_factura = $this->codigo;
            $objrenglon->guardarFacturas_renglones();
        }

        if (!($myPuntoVenta->medio == 'electronico' && $pais == 1)  )  {
            if (count($arrDsf) > 0 || count($arrGinfes) > 0 || count($arrPaulistana) > 0) {
                $this->setPropiedad(self::$propiedadNumeroRps, $numeroFactura);
            } else {
                $this->setPropiedad(self::$propiedadNumeroFactura, $numeroFactura);
            }

            $myPuntoVenta->incrementarNumero();
        }
    }

    public function baja() {
        $condicion = array('cod_factura' => $this->codigo);
        $renglonesfactura = Vfacturas_renglones::listarFacturas_renglones($this->oConnection, $condicion);

        foreach ($renglonesfactura as $renglon) {
            $renglonfactura = new Vfacturas_renglones($this->oConnection, $renglon['codigo']);
            $renglonfactura->anular();
        }
        $myPuntoVenta = new Vpuntos_venta($this->oConnection, $this->punto_venta);
        if ($myPuntoVenta->utilizaWebServices()) {
            $this->estado = Vfacturas::getEstadoPendienteCancelar();
        } else {
            $this->estado = Vfacturas::getEstadoInhabilitado();
        }
        $this->guardarFacturas();
    }

    public function alta() {
        $condicion = array('cod_factura' => $this->codigo);
        $renglonesfactura = Vfacturas_renglones::listarFacturas_renglones($this->oConnection, $condicion);

        foreach ($renglonesfactura as $renglon) {
            $renglonfactura = new Vfacturas_renglones($this->oConnection, $renglon['codigo']);
            $renglonfactura->activar();
        }

        $this->anulada = 0;
        $this->guardarFacturas();
    }

    public function getRenglonesDescripcion() {
        $this->oConnection->select('ctacte.codigo, facturas_renglones.importe as importe_facturado, facturas_renglones.codigo as cod_renglon');
        $this->oConnection->from('facturas_renglones');
        $this->oConnection->join('ctacte', 'ctacte.codigo = facturas_renglones.cod_ctacte');
        $this->oConnection->where('facturas_renglones.cod_factura', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getRenglones($activos = true) {
        $conexion = $this->oConnection;
        $conexion->select("ctacte.*");
        $conexion->select("facturas_renglones.codigo AS codigo_renglon");
        $conexion->select("facturas_renglones.importe AS facturas_renglones_importe");
        $conexion->select("IFNULL(ROUND((facturas_renglones.importe * impuestos.valor / 100), 3), 0) AS impuesto_valor", false);
        $conexion->select("impuestos.nombre AS impuesto_nombre");
        $conexion->select("IFNULL(impuestos.valor, 0) AS impuesto_porcentaje", false);
        $conexion->from("facturas_renglones");
        $conexion->join("ctacte", "ctacte.codigo = facturas_renglones.cod_ctacte");
        $conexion->join("ctacte_impuestos", "ctacte_impuestos.cod_ctacte = facturas_renglones.cod_ctacte", "left");
        $conexion->join("impuestos", "impuestos.codigo = ctacte_impuestos.cod_impuesto", "left");
        $conexion->where("facturas_renglones.cod_factura =", $this->codigo);
        if($activos)
        {
            $conexion->where("facturas_renglones.anulada =", 0);
        }    
        $query = $conexion->get();
        //die($conexion->last_query());
        return $query->result_array();
    }

    /* STATIC FUNCTIONS */

    static function getListadoFacturacion(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null, $tipoFactura = null, $anulada = null, $facturaDesde = null, $facturaHasta = null, $idCtaCte = null) {
        $aColumns = array();
        $aColumns['codigo']['order'] = "facturas.codigo";
        $aColumns['fecha']['order'] = "facturas.fecha";
        $aColumns['factura']['order'] = "general.tipos_facturas.factura";
        $aColumns['nrofact']['order'] = "nrofact";
        $aColumns['talonario_nombre']['order'] = "talonario_nombre";
        $aColumns['razon_social']['order'] = 'razones_sociales.razon_social';
        $aColumns['identificador_fiscal']['order'] = 'identificador_fiscal';
        $aColumns['total']['order'] = 'total';
        $aColumns['anulada']['order'] = "anulada";
        $aColumns['total_impuestos']['order'] = "total_impuestos";
        $aColumns['subtotal']['order'] = "subtotal";

        $aColumns['codigo']['having'] = "facturas.codigo";
        $aColumns['fecha']['having'] = "fecha";
        $aColumns['factura']['having'] = "general.tipos_facturas.factura";
        $aColumns['nrofact']['having'] = "facturas.nrofact";
        $aColumns['talonario_nombre']['having'] = "talonarios.punto_venta";
        $aColumns['razon_social']['having'] = 'razones_sociales.razon_social';
        $aColumns['identificador_fiscal']['having'] = 'identificador_fiscal';
        $aColumns['total']['having'] = 'total';
        $aColumns['anulada']['having'] = "anulada";
        $aColumns['total_impuestos']['having'] = "total_impuestos";
        $aColumns['subtotal']['having'] = "subtotal";

        $conexion->select("SUM(facturas_renglones.importe)");
        $conexion->from("facturas_renglones");
        $conexion->where("facturas_renglones.cod_factura = facturas.codigo");
        $queryTotal = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("SUM(IFNULL(impuestos.valor * facturas_renglones.importe / 100, 0)) AS total_impuesto", false);
        $conexion->from("facturas_renglones");
        $conexion->join("ctacte_impuestos", "ctacte_impuestos.cod_ctacte = facturas_renglones.cod_ctacte", "left");
        $conexion->join("impuestos", "impuestos.codigo = ctacte_impuestos.cod_impuesto");
        $conexion->where("facturas_renglones.cod_factura = facturas.codigo");
        $queryTotalImpuestos = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("SUM(facturas_renglones.importe) - SUM(IFNULL(impuestos.valor * facturas_renglones.importe / 100, 0)) AS total_impuestos", false);
        $conexion->from("facturas_renglones");
        $conexion->join("ctacte_impuestos", "ctacte_impuestos.cod_ctacte = facturas_renglones.cod_ctacte", "left");
        $conexion->join("impuestos", "impuestos.codigo = ctacte_impuestos.cod_impuesto");
        $conexion->where("facturas_renglones.cod_factura = facturas.codigo");
        $querySubtotal = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("facturas_propiedades.valor");
        $conexion->from("facturas_propiedades");
        $conexion->where("facturas_propiedades.cod_factura = facturas.codigo");
        $conexion->where("facturas_propiedades.propiedad = 'numero_factura'");
        $sqNumeroFactura = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("facturas.codigo");
        $conexion->select("CONCAT(LPAD(DAY(facturas.fecha), 2, 0), '/', LPAD(MONTH(facturas.fecha), 2, 0), '/', YEAR(facturas.fecha)) AS fecha", false);
        $conexion->select("general.tipos_facturas.factura");
        $conexion->select("($sqNumeroFactura) AS nrofact");
        $conexion->select("general.puntos_venta.prefijo AS talonario_nombre", false);
        $conexion->select("razones_sociales.razon_social");
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ', razones_sociales.documento) AS identificador_fiscal", false);
        $conexion->select("($queryTotal) AS total");
        $conexion->select("IF(facturas.estado = 'inhabilitada', 'si', 'no') AS anulada", false);
        $conexion->select("ROUND(($queryTotalImpuestos), 3) AS total_impuestos", false);
        $conexion->select("ROUND(($querySubtotal), 3) AS subtotal", false);
        $conexion->from("facturas");
        $conexion->join("razones_sociales", "razones_sociales.codigo = facturas.codrazsoc");
        $conexion->join("general.documentos_tipos", "general.documentos_tipos.codigo = razones_sociales.tipo_documentos");
        $conexion->join("general.puntos_venta", "general.puntos_venta.codigo = facturas.punto_venta");
        $conexion->join("general.tipos_facturas", "general.tipos_facturas.codigo = general.puntos_venta.tipo_factura");
        $conexion->join("general.facturantes", "general.facturantes.codigo = general.puntos_venta.cod_facturante");
        if ($idCtaCte != null)
            $conexion->join("facturas_renglones", "facturas_renglones.cod_factura = facturas.codigo AND facturas_renglones.cod_ctacte = $idCtaCte");
        if ($fechaDesde != null)
            $conexion->where("DATE(facturas.fecha) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(facturas.fecha) <=", $fechaHasta);
        if ($tipoFactura != null)
            $conexion->where("general.tipos_facturas.codigo", $tipoFactura);
        if ($anulada !== null) {
            if ($anulada == 1) {
                $conexion->where("facturas.estado", self::$estadoInhabilitada);
            } else {
                $conexion->where("facturas.estado", self::$estadoHabilitada);
            }
        }
        if ($facturaDesde !== null)
            $conexion->where("facturas.nrofact >=", $facturaDesde);
        if ($facturaHasta !== null)
            $conexion->where("facturas.nrofact <=", $facturaHasta);

        if ($search != null) {
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $conexion->or_having($tableFields['having'] . " LIKE ", "%$search%");
                }
            }
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($arrSort[0]) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    public function asociarCobro($cod_cobro) {
        $this->oConnection->insert('facturas_cobros', array('cod_factura' => $this->codigo, 'cod_cobro' => $cod_cobro));
    }

    public function getCobroAsociado($sin_anulados = true) {
        $this->oConnection->select('cod_cobro');
        $this->oConnection->from('facturas_cobros');
        $this->oConnection->join('cobros', 'cobros.codigo = facturas_cobros.cod_cobro');
        $this->oConnection->where('cod_factura', $this->codigo);
        if($sin_anulados) $this->oConnection->where('cobros.estado <>', 'anulado');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getPropiedadNumeroFactura() {
        return self::$propiedadNumeroFactura;
    }

    static function getPropiedadNumeroRps() {
        return self::$propiedadNumeroRps;
    }

    static public function getEstadoPendiente() {
        return self::$estadoPendiente;
    }

    static public function getEstadoHabilitado() {
        return self::$estadoHabilitada;
    }

    static public function getEstadoInhabilitado() {
        return self::$estadoInhabilitada;
    }

    static public function getEstadoArchivoCreado() {
        return self::$estadoArchivoCreado;
    }

    static public function getEstadoError() {
        return self::$estadoError;
    }

    static public function getEstadoEnviado() {
        return self::$estadoEnviado;
    }

    static public function getEstadoPendienteCancelar() {
        return self::$estadoPendienteCancelar;
    }

    public function getAlumno() {
        $this->oConnection->select("alumnos.*");
        $this->oConnection->from("alumnos");
        $this->oConnection->join("alumnos_razones", "alumnos_razones.cod_alumno = alumnos.codigo");
        $this->oConnection->where("alumnos_razones.cod_razon_social", $this->codrazsoc);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function getNotasCredito($baja = null) {
        $this->oConnection->select("notas_credito_renglones.*, notas_credito.cod_alumno, notas_credito.estado, notas_credito.cod_usuario, notas_credito.fechaalta, notas_credito.fechareal, notas_credito.motivo");
        $this->oConnection->from("notas_credito_renglones");
        $this->oConnection->join("notas_credito", "notas_credito.codigo = notas_credito_renglones.cod_nota_credito");
        $this->oConnection->where("notas_credito.estado <>", 'anulado');
        $this->oConnection->where("cod_factura", $this->codigo);
        if ($baja != null) {
            $this->oConnection->where("baja", $baja);
        }
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function getImpuestosFactura() {
        $respuesta = array();
        $impuestos = array();
        //impuestos en lineas de factura
        $renglones = Vfacturas_renglones::listarFacturas_renglones($this->oConnection, array('cod_factura' => $this->codigo)); // $this->getRenglonesDescripcion();

        foreach ($renglones as $rowrenglon) {
            $objRenglon = new Vfacturas_renglones($this->oConnection, $rowrenglon['codigo']);
            $imp_ctacte = $objRenglon->getImpuestos();

            $suma_impuesto = 0;
            foreach ($imp_ctacte as $value) {
                $suma_impuesto = $value['valor'] + $suma_impuesto;
            }
            $neto_renglon = 100 * $objRenglon->importe / (100 + $suma_impuesto );

            foreach ($imp_ctacte as $value) {
                $cod_imp_gral = $value['cod_impuesto_general'];
                $impuestos[$cod_imp_gral][] = round($neto_renglon, 2, PHP_ROUND_HALF_EVEN);
            }
        }

        foreach ($impuestos as $codigo => $arrnetos) {
            $total = 0;
            foreach ($arrnetos as $valor) {
                $total += $valor;
            }

            $objimpuesto = new Vimpuestos_general($this->oConnection, $codigo);
            $porcentaje = $objimpuesto->getValor();
            $calculado = $porcentaje == 0 ? $total : $total * $porcentaje / 100;
            $respuesta[] = array('cod_impuesto_general' => $objimpuesto->getCodigo(),
                'nombre' => $objimpuesto->nombre,
                'cod_afip' => $objimpuesto->getCodigoAfip(),
                'tipo' => $objimpuesto->getTipo(),
                'pais' => $objimpuesto->pais,
                'porcentaje' => $porcentaje,
                'total_calculo' => $total,
                'total' => round($calculado, 2, PHP_ROUND_HALF_EVEN));
        }
        return $respuesta;
    }

    public function getNeto() {
        $renglones = Vfacturas_renglones::listarFacturas_renglones($this->oConnection, array('cod_factura' => $this->codigo)); // $this->getRenglonesDescripcion();
        $neto = 0;
        foreach ($renglones as $rowrenglon) {
            $objRenglon = new Vfacturas_renglones($this->oConnection, $rowrenglon['codigo']);
            $neto += $objRenglon->getNeto();
        }
        return $neto;
    }

    public function getComision($matricula = null) {
        $this->oConnection->select("comisiones.nombre");
        $this->oConnection->from("comisiones");
        $this->oConnection->join("planes_comisiones", "planes_comisiones.cod_comision = comisiones.codigo");
        $this->oConnection->join("matriculas", "matriculas.cod_plan_pago = planes_comisiones.cod_plan");
        $this->oConnection->where("matriculas.codigo", $matricula);

        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        if (isset($arrResp[0]['nombre']))
            return $arrResp[0]['nombre'];
        else
            return '';

    }



}
