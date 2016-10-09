<?php

/**
 * Class Vcaja
 *
 * Class  Vcaja maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcaja extends Tcaja {

    static public $estadoabierta = 'abierta';
    static public $estadocerrada = 'cerrada';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getMediosCaja($entradaSalida = null) {
        $this->oConnection->select("general.medios_pago.*", false);
        $this->oConnection->from("cajas_medios_pago");
        $this->oConnection->join("general.medios_pago", "general.medios_pago.codigo = cajas_medios_pago.cod_medio");
        $this->oConnection->where("cajas_medios_pago.cod_caja", $this->codigo);
        if ($entradaSalida !== null) {
            $this->oConnection->where("cajas_medios_pago.entrada_salida", $entradaSalida);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getUltimoSaldo($codigoMedio = null, $codConcepto = null) {
        $this->oConnection->select_max('codigo');
        $this->oConnection->from('movimientos_caja');
        $this->oConnection->where('cod_caja', $this->codigo);
        if ($codigoMedio != null){
            $this->oConnection->where("cod_medio", $codigoMedio);
        }
        if ($codConcepto != null){
            $this->oConnection->where("cod_concepto", $codConcepto);
        }
        $query = $this->oConnection->get();
        $result = $query->result_array();
        if (isset($result[0]) && isset($result[0]['codigo'])){
            $this->oConnection->select('saldo');
            $this->oConnection->from('movimientos_caja');
            $this->oConnection->where('codigo', $result[0]['codigo']);
            $query1 = $this->oConnection->get();
            $saldo = $query1->result();
        return $saldo[0]->saldo;
        } else {
            return 0;
        }
    }

    public function abrir($usuario = null) {
        $this->estado = Vcaja::$estadoabierta;
        $arrSaldos = $this->getDebePorMedioConcepto(Vmovimientos_caja::getConceptoCierre());
        $fechaApertura = date("Y-m-d H:i:s");
        foreach ($arrSaldos as $saldo) {
            $movcaja = new Vmovimientos_caja($this->oConnection);
            $codmedio = $saldo['codigo'];
            $importe = $saldo['saldo_concepto'];
            $movcaja->guardar($fechaApertura, $codmedio, 0, $importe, null, $usuario, $this->codigo, Vmovimientos_caja::getConceptoApertura(), null, date('Y-m-d H:i:s'), $importe);
            $data = array("cod_apertura" => $movcaja->getCodigo());
            $this->oConnection->where("cod_caja", $this->codigo);
            $this->oConnection->where("cod_medio", $codmedio);
            $this->oConnection->update("cajas_medios_pago", $data);
            $this->oConnection->where("codigo", $movcaja->getCodigo());
            $data = array("codigo_apertura" => $movcaja->getCodigo());
            $this->oConnection->update("movimientos_caja", $data);
        }
        return $this->guardarCaja();
    }

    function getCodigoApertura($codMedio) {
        $this->oConnection->select("cod_apertura");
        $this->oConnection->from("cajas_medios_pago");
        $this->oConnection->where("cod_caja", $this->codigo);
        $this->oConnection->where("cod_medio", $codMedio);
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return isset($arrResp[0]) && isset($arrResp[0]['cod_apertura']) ? $arrResp[0]['cod_apertura'] : 0;
    }

    public function cerrarMedio($importedebe, $codmedio, $usuario) {
        $movcaja = new Vmovimientos_caja($this->oConnection);
        $movcaja->guardar(date('Y-m-d H:i:s'), $codmedio, $importedebe, 0, null, $usuario, $this->codigo, Vmovimientos_caja::getConceptoCierre(), null, date('Y-m-d H:i:s'), 0);
    }

    public function getUltimosMovimientos() {
        $this->oConnection->select('*');
        $this->oConnection->from('movimientos_caja');
        $this->oConnection->where('cod_caja', $this->codigo);
        $this->oConnection->where('codigo >= (select codigo from movimientos_caja as mc where mc.cod_concepto ="' . Vmovimientos_caja::getConceptoApertura() . '" ORDER BY codigo desc limit 0,1) ');
        $this->oConnection->order_by('fecha_hora', 'asc');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getDebePorMedioConcepto($codConcepto) {
        $this->oConnection->select("movimientos_caja.debe");
        $this->oConnection->from("movimientos_caja");
        $this->oConnection->where("movimientos_caja.cod_caja = cajas_medios_pago.cod_caja");
        $this->oConnection->where("movimientos_caja.cod_medio = cajas_medios_pago.cod_medio");
        $this->oConnection->where("movimientos_caja.cod_concepto = '$codConcepto'");
        $this->oConnection->order_by("codigo", "desc");
        $this->oConnection->limit(1, 0);
        $subquery = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("general.medios_pago.*", false);
        $this->oConnection->select("IFNULL(($subquery), 0) AS saldo_concepto", false);
        $this->oConnection->from("cajas_medios_pago");
        $this->oConnection->join("general.medios_pago", "general.medios_pago.codigo = cajas_medios_pago.cod_medio");
        $this->oConnection->where("cajas_medios_pago.cod_caja", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function get_saldos_por_concepto($mediosEntradaSalida = null, $conceptoDeCierre = true, $conceptoDeApertura = false) {
        $campoSaldo = $conceptoDeCierre ? "debe" : "saldo";
        $this->oConnection->select("movimientos_caja.$campoSaldo", false);
        $this->oConnection->from("movimientos_caja");
        $this->oConnection->where("movimientos_caja.cod_caja", $this->codigo);
        $this->oConnection->where("movimientos_caja.cod_medio = general.medios_pago.codigo");
        if ($conceptoDeCierre || $conceptoDeApertura) {
            if ($conceptoDeCierre)
                $codConcepto = Vmovimientos_caja::getConceptoCierre();
            else
                $codConcepto = Vmovimientos_caja::getConceptoApertura();
            $this->oConnection->where("movimientos_caja.cod_concepto", (string) $codConcepto);
        } else {
            $arrCopnceptos = array((string) Vmovimientos_caja::getConceptoApertura(), (string) Vmovimientos_caja::getConceptoCierre());
            $this->oConnection->where_not_in("movimientos_caja.cod_concepto", $arrCopnceptos);
        }
        $this->oConnection->order_by("movimientos_caja.codigo DESC");
        $this->oConnection->limit(1, 0);
        $subquery = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("general.medios_pago.*", false);
        $this->oConnection->select("IFNULL(($subquery), 0) AS saldo_concepto", false);
        $this->oConnection->from("general.medios_pago");
        if ($mediosEntradaSalida !== null) {
            $this->oConnection->join("cajas_medios_pago", "cajas_medios_pago.cod_medio = general.medios_pago.codigo AND cajas_medios_pago.cod_caja = $this->codigo");
            $this->oConnection->where("cajas_medios_pago.entrada_salida", $mediosEntradaSalida);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getUsuarios() {
        $this->oConnection->select("general.usuarios_sistema.*", false);
        $this->oConnection->from("caja_usuario");
        $this->oConnection->join("general.usuarios_sistema", "general.usuarios_sistema.codigo = caja_usuario.coduser");
        $this->oConnection->where("caja_usuario.codtiposcaja", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function unSetUsuarioCaja($array) {
        $this->oConnection->delete('caja_usuario', $array);
    }

    public function setUsuariosCaja($UsuarioCaja) {
        $this->oConnection->insert('caja_usuario', $UsuarioCaja);
    }

    public function unSetMediosCaja($array) {
        $this->oConnection->delete('cajas_medios_pago', $array);
    }

    public function setMedioPagoCaja($mediosCaja) {
        $this->oConnection->insert('cajas_medios_pago', $mediosCaja);
    }

    public function continuaAbierta($codmovimiento) {
        $this->oConnection->select('*');
        $this->oConnection->from('movimientos_caja');
        $this->oConnection->where('cod_concepto', Vmovimientos_caja::getConceptoCierre());
        $this->oConnection->where('codigo >', $codmovimiento);
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        if (count($resultado) > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getMediosPago($cod_medio = null) {
        $this->oConnection->select("*");
        $this->oConnection->from("cajas_medios_pago");
        $this->oConnection->where("cajas_medios_pago.cod_caja", $this->codigo);
        if ($cod_medio != null) {
            $this->oConnection->where("cajas_medios_pago.cod_medio", $cod_medio);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getCajasMedios(CI_DB_mysqli_driver $conexion, $cod_medio = null, $cod_caja = null) {
        $conexion->select('*');
        $conexion->from('cajas_medios_pago');
        if ($cod_medio != null) {
            $conexion->where('cod_medio', $cod_medio);
        }
        if ($cod_caja != null) {
            $conexion->where('cod_caja', $cod_caja);
        }
        $query = $conexion->get();
        $cajas = $query->result_array();
        return $cajas;
    }
}