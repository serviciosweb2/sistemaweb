<?php

/**
 * Class Vcobros
 *
 * Class  Vcobros maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcobros extends Tcobros {

    static private $estadoconfirmado = 'confirmado';
    static private $estadopendiente = 'pendiente';
    static private $estadoanulado = 'anulado';
    static private $estadoerror = 'error';
    static private $arrayEstados = array("confirmado", "pendiente", "anulado", "error");

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function inputar($id_ctacte, $valor, $cod_usuario) {
        $ctacte_imputaciones = new Vctacte_imputaciones($this->oConnection);
        $ctacte_imputaciones->cod_cobro = $this->codigo;
        $ctacte_imputaciones->cod_ctacte = $id_ctacte;
        $ctacte_imputaciones->cod_usuario = $this->cod_usuario;
        $ctacte_imputaciones->tipo = 'COBRO';
        $ctacte_imputaciones->valor = $valor;
        $ctacte_imputaciones->fecha = date('Y-m-d H:i:s');
        $ctacte_imputaciones->estado = self::getEstadoPendiente();
        $resp = $ctacte_imputaciones->guardarCtacte_imputaciones();
        return $resp;
    }

    public function guardarCobro($importe, $mediopago, $estado, $alumno, $codusuario, $cod_caja = null, $fechaalta = null, $objmedio = NULL, $fechareal = null) {
        $this->importe = $importe;
        $this->medio_pago = $mediopago;
        $this->estado = $estado;
        $this->cod_alumno = $alumno;
        $this->cod_usuario = $codusuario;
        $this->cod_caja = $cod_caja;
        $this->fechaalta = $fechaalta == null ? date("Y-m-d H:i:s") : $fechaalta;
        $this->fechareal = $fechareal == null ? date("Y-m-d") : $fechareal;
        $this->guardarCobros();
        if ($objmedio != null) {
            $medio = new Vmedios_pago($this->oConnection, $mediopago);
            $medio->guardarMedio($this->getCodigo(), $objmedio);
        }
    }

    public function confirmarCobro($codusuario = null, $abrecaja = false) {
        $respuesta = 0;
        if ($this->medio_pago == '2') {
            $this->estado = Vcobros::getEstadoConfirmado();
            if ($this->periodo == '' || $this->periodo === NULL){
                $this->periodo = date('Y') . date('m');
                $this->fechaalta = date("Y-m-d H:i:s");
            }
            $respuesta = $this->guardarCobros();
            $estadoHistoricos = new Vcobro_estado_historico($this->oConnection);
            $arrGuardarEstadoHistorico = array(
                'cod_cobro' => $this->codigo,
                'estado' => $this->estado,
                'fecha_hora' => date('Y-m-d H:i:s'),
                'cod_usuario' => $codusuario
            );
            $estadoHistoricos->setCobro_estado_historico($arrGuardarEstadoHistorico);
            $estadoHistoricos->guardarCobro_estado_historico();
            $CtacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, array('cod_cobro' => $this->codigo, 'tipo' => 'COBRO', 'estado' => 'pendiente'));
            foreach ($CtacteImputaciones as $ctacte_imputaciones) {
                $ObjCtaCteImputaciones = new Vctacte_imputaciones($this->oConnection, $ctacte_imputaciones['codigo']);
                $ObjCtaCteImputaciones->confirmar($codusuario, $this->fechareal);
            }
        } else {
            $objcaja = new Vcaja($this->oConnection, $this->cod_caja);
            $medios = $objcaja->getMediosPago($this->medio_pago);
            if (count($medios) > 0) {
                if ($abrecaja && $objcaja->estado == Vcaja::$estadocerrada) {
                    $objcaja->abrir($codusuario);
                }
                if ($objcaja->estado == Vcaja::$estadoabierta) {
                    $this->estado = Vcobros::getEstadoConfirmado();
                    if ($this->periodo == '' || $this->periodo){
                        $this->periodo = date('Y') . date('m');
                        $this->fechaalta = date("Y-m-d H:i:s");
                    }
                    $respuesta = $this->guardarCobros();
                    $estadoHistoricos = new Vcobro_estado_historico($this->oConnection);
                    $arrGuardarEstadoHistorico = array(
                        'cod_cobro' => $this->codigo,
                        'estado' => $this->estado,
                        'fecha_hora' => date('Y-m-d H:i:s'),
                        'cod_usuario' => $codusuario
                    );
                    $estadoHistoricos->setCobro_estado_historico($arrGuardarEstadoHistorico);
                    $estadoHistoricos->guardarCobro_estado_historico();
                    $CtacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, array('cod_cobro' => $this->codigo, 'tipo' => 'COBRO', 'estado' => 'pendiente'));
                    foreach ($CtacteImputaciones as $ctacte_imputaciones) {
                        $ObjCtaCteImputaciones = new Vctacte_imputaciones($this->oConnection, $ctacte_imputaciones['codigo']);
                        $ObjCtaCteImputaciones->confirmar($codusuario, $this->fechareal);
                    }
                    $movCaja = new Vmovimientos_caja($this->oConnection);
                    $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, 0, $this->importe, null, $codusuario, $this->cod_caja, Vmovimientos_caja::getConceptoCobros(), $this->codigo, date("Y-m-d H:i:s"));
                    if ($medios[0]['entrada_salida'] == '1') {
                        $movCaja2 = new Vmovimientos_caja($this->oConnection);
                        $movCaja2->guardar(date("Y-m-d H:i:s"), $this->medio_pago, $this->importe, 0, null, $codusuario, $this->cod_caja, Vmovimientos_caja::getConceptoCobros(), $this->codigo, date("Y-m-d H:i:s"));
                    }
                }
            }
        }
        return $respuesta;
    }

    static function listarCobrosDataTable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike, $arrLimit, $arrSort = null, $contar = false, $order = null, $separador = null, $separadorDecimal = null, 
            $fechaDesde = null, $fechaHasta = null, $estado = null, $periodo = null, $caja = null, $saldo = null, $medio_pago = null) {
        $mostrarSubquery = '';
        $subquery2 = '';
        $conexion->select('ifnull(sum(ctacte_imputaciones.valor),0) as totImputaciones', false);
        $conexion->from('ctacte_imputaciones');
        $conexion->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
        $conexion->where('ctacte_imputaciones.estado', 'confirmado');
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $mostrarSubquery = "cobros.importe - ($subquery) as saldoRestante";
        $conexion->select("IFNULL(SUM(ctacte_imputaciones.valor), 0)", false);
        $conexion->from('ctacte_imputaciones');
        $conexion->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
        $conexion->where("ctacte_imputaciones.estado = 'confirmado'");
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("($subquery2) AS total_imputado", false);
        $nombreApellido = formatearNomApeQuery();
        $conexion->select("cobros.codigo");
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select("cobros.importe");
        $conexion->select("general.medios_pago.medio");
        $conexion->select("cobros.fechareal");
        $conexion->select("cobros.estado");
        $conexion->select($mostrarSubquery, false);
        $conexion->select("cobros.medio_pago as cod_medio", false);
        $conexion->select("cobros.periodo", false);
        $conexion->select("cobros.fechaalta");
        $conexion->select("(SELECT caja.nombre FROM caja WHERE caja.codigo = cobros.cod_caja) as caja");
        $conexion->select("concat( general.documentos_tipos.nombre, ' ', alumnos.documento ) as documento_completo", false);
        $conexion->from('cobros');
        $conexion->join('alumnos', 'alumnos.codigo = cobros.cod_alumno');
        $conexion->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
        $conexion->join('general.documentos_tipos', 'general.documentos_tipos.codigo = alumnos.tipo');
        if ($fechaDesde != null)
            $conexion->where("DATE(cobros.fechaalta) >= DATE('", $fechaDesde ."')",false);
        if ($fechaHasta != null)
            $conexion->where("DATE(cobros.fechaalta) <= DATE('", $fechaHasta ."')",false);
        if ($estado != null)
            $conexion->where("cobros.estado =", $estado);
        if ($periodo != null)
            $conexion->where("cobros.periodo", $periodo);
        if ($caja != null)
            $conexion->where("cobros.cod_caja", $caja);
        if($medio_pago != null)
            $conexion->where("cobros.medio_pago", $medio_pago);
        if (count($arrCondicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondicioneslike as $key => $value) {
                if ($key == 'nombre_apellido' || $key == 'cobros.importe') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                    $arrTemp[] = "REPLACE(cobros.importe, '$separadorDecimal ',' ') LIKE REPLACE('%$value%', '$separadorDecimal ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($saldo !== null){
            if ($saldo == 1){
                $conexion->having("saldoRestante >", "0");
            } else {
                $conexion->having("saldoRestante", "0");
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($order != NULL) {
            foreach ($order as $value) {
                $conexion->order_by($value[0], $value[1]);
            }
        }
        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        if ($contar) {
            //die($conexion->last_query());
            $arrResp = $query->num_rows();
        } else {
            //die($conexion->last_query());
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    public function getCtacteImputaciones() {
        $this->oConnection->select("ctacte.*", false);
        $this->oConnection->join("ctacte", "ctacte.codigo = ctacte_imputaciones.cod_ctacte");
        return $this->getImputaciones();
    }

    public function getImputaciones() {
        return Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, array("cod_cobro" => $this->codigo, "tipo" => 'COBRO', "estado" => 'confirmado'));
    }

    public function getSaldoImputacion() {
        $this->oConnection->select('ctacte_imputaciones.codigo, ctacte_imputaciones.cod_ctacte, ctacte_imputaciones.valor, ctacte_imputaciones.estado, ctacte_imputaciones.fecha');
        $this->oConnection->from('cobros');
        $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.tipo = "COBRO"');
        $this->oConnection->where('cobros.codigo', $this->codigo);
        $this->oConnection->where('ctacte_imputaciones.estado <>', Vcobros::getEstadoanulado());
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getDetalleCobro() {
        switch ($this->medio_pago) {
            case 1:
                $this->oConnection->select("caja.nombre", false);
                $this->oConnection->from('caja');
                $this->oConnection->where('caja.codigo = cobros.cod_caja');
                $subquery = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select('sum(ctacte_imputaciones.valor)', false);
                $this->oConnection->from('ctacte_imputaciones');
                $this->oConnection->where('ctacte_imputaciones.cod_cobro', $this->codigo);
                $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
                $subquery1 = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("cobros.cod_caja,general.medios_pago.codigo as cod_medio,general.medios_pago.medio, ($subquery) as nombreCaja, ($subquery1) as valor", false);
                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
                $this->oConnection->from('cobros');
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $query = $this->oConnection->get();
                return $query->result_array();
                break;
            
            case 2:
                $this->oConnection->select("general.medios_pago.medio");
                $this->oConnection->from("general.medios_pago");
                $this->oConnection->where("general.medios_pago.codigo", 2);
                $sqMedio = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("bancos.remesas.fecha_documento");
                $this->oConnection->select("2 AS cod_medio", false);
                $this->oConnection->select("bancos.remesas.cod_configuracion AS cod_cuenta");
                $this->oConnection->select("bancos.remesas.nombre_banco");
                $this->oConnection->select("bancos.remesas.agencia");
                $this->oConnection->select("bancos.remesas.numero_cuenta");
                $this->oConnection->select("cobros.importe AS valor");
                $this->oConnection->select("($sqMedio) AS medio");
                $this->oConnection->from("cobros");
                $this->oConnection->join("cobros_boletos", "cobros_boletos.cod_cobro = cobros.codigo");
                $this->oConnection->join("bancos.boletos_estados_historicos", "bancos.boletos_estados_historicos.codigo = cobros_boletos.cod_boleto_historico");
                $this->oConnection->join("bancos.boletos_bancarios", "bancos.boletos_bancarios.codigo = bancos.boletos_estados_historicos.cod_boleto");
                $this->oConnection->join("bancos.remesas", "bancos.remesas.codigo = bancos.boletos_bancarios.cod_remesa");
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $query = $this->oConnection->get();
                return $query->result_array();
                break;
            
            case 3:
                $this->oConnection->select('sum(ctacte_imputaciones.valor)');
                $this->oConnection->from('ctacte_imputaciones');
                $this->oConnection->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
                $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
                $subquery = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("cobros.codigo, general.medios_pago.codigo as cod_medio, general.medios_pago.medio, bancos.bancos.nombre as nombreBanco, tarjetas.tipos_tarjetas.nombre as nombreTarj, medio_tarjetas.cupon, medio_tarjetas.cod_autorizacion, caja.nombre as nombreCaja, ($subquery) as valor");
                $this->oConnection->from('cobros');
                $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.tipo = "COBRO"', "left");
                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
                $this->oConnection->join('caja', 'caja.codigo = cobros.cod_caja');
                $this->oConnection->join('medio_tarjetas', 'medio_tarjetas.cod_cobro = cobros.codigo');
                $this->oConnection->join('bancos.bancos', 'bancos.bancos.codigo = medio_tarjetas.cod_bco_emisor', "left");
                $this->oConnection->join('tarjetas.tipos_tarjetas', 'tarjetas.tipos_tarjetas.codigo = medio_tarjetas.cod_tipo', "left");
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $this->oConnection->group_by('cobros.codigo');
                $query = $this->oConnection->get();
                return $query->result_array();
                break;
            
            case 4:
                $this->oConnection->select('sum(ctacte_imputaciones.valor)');
                $this->oConnection->from('ctacte_imputaciones');
                $this->oConnection->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
                $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
                $subquery = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("cobros.codigo, general.medios_pago.codigo as cod_medio, general.medios_pago.medio,bancos.bancos.nombre, medio_cheques.fecha_cobro, medio_cheques.nro_cheque, medio_cheques.tipo_cheque, medio_cheques.emisor, caja.nombre as nombreCaja,($subquery) as valor");
                $this->oConnection->from('cobros');
                $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.tipo = "COBRO"', "left");
                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
                $this->oConnection->join('caja', 'caja.codigo = cobros.cod_caja');
                $this->oConnection->join('medio_cheques', 'medio_cheques.cod_cobro = cobros.codigo');
                $this->oConnection->join('bancos.bancos', 'bancos.bancos.codigo = medio_cheques.cod_banco_emisor', "left");
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $this->oConnection->group_by('cobros.codigo');
                $query = $this->oConnection->get();
                return $query->result_array();
                break;
            
//            case 5:
//                $this->oConnection->select('ctacte.*, notas_credito_renglones.cod_cta_cte,general.medios_pago.codigo as cod_medio, notas_credito_renglones.valor,general.medios_pago.medio,');
//                $this->oConnection->from('notas_credito_renglones');
//                $this->oConnection->join('medio_notas_credito', 'medio_notas_credito.codigo = notas_credito_renglones.cod_nota_credito');
//                $this->oConnection->join('cobros', 'cobros.codigo = medio_notas_credito.cod_cobro');
//                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
//                $this->oConnection->join('ctacte', 'ctacte.codigo = notas_credito_renglones.cod_cta_cte');
//                $this->oConnection->where('cobros.codigo', $this->codigo);
//                $query = $this->oConnection->get();
//                return $query->result_array();
//                break;
            
            case 6:
                $this->oConnection->select('sum(ctacte_imputaciones.valor)');
                $this->oConnection->from('ctacte_imputaciones');
                $this->oConnection->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
                $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
                $subquery = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("cobros.codigo,general.medios_pago.codigo as cod_medio, general.medios_pago.medio, medio_depositos.cuenta_nombre, medio_depositos.nro_transaccion, bancos.bancos.nombre, caja.nombre as nombreCaja,($subquery) as valor");
                $this->oConnection->from('cobros');
                $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.tipo = "COBRO"', "left");
                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
                $this->oConnection->join('caja', 'caja.codigo = cobros.cod_caja');
                $this->oConnection->join('medio_depositos', 'medio_depositos.cod_cobro = cobros.codigo');
                $this->oConnection->join('bancos.bancos', 'bancos.bancos.codigo = medio_depositos.cod_banco');
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $this->oConnection->group_by('cobros.codigo');
                $query = $this->oConnection->get();
                return $query->result_array();
                break;
            
            case 7:
                $this->oConnection->select('sum(ctacte_imputaciones.valor)');
                $this->oConnection->from('ctacte_imputaciones');
                $this->oConnection->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
                $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
                $subquery = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("cobros.codigo, general.medios_pago.codigo as cod_medio, general.medios_pago.medio, medio_transferencias.cuenta_nombre, medio_transferencias.nro_transaccion, bancos.bancos.nombre, caja.nombre as nombreCaja, ($subquery) as valor");
                $this->oConnection->from('cobros');
                $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.tipo = "COBRO"', "left");
                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo= cobros.medio_pago');
                $this->oConnection->join('caja', 'caja.codigo = cobros.cod_caja');
                $this->oConnection->join('medio_transferencias', 'medio_transferencias.cod_cobro = cobros.codigo');
                $this->oConnection->join('bancos.bancos', 'bancos.bancos.codigo = medio_transferencias.cod_banco');
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $this->oConnection->group_by('cobros.codigo');
                $query = $this->oConnection->get();
                return $query->result_array();
                break;

            case 8:
                $this->oConnection->select('sum(ctacte_imputaciones.valor)');
                $this->oConnection->from('ctacte_imputaciones');
                $this->oConnection->where('ctacte_imputaciones.cod_cobro = cobros.codigo');
                $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
                $subquery = $this->oConnection->return_query();
                $this->oConnection->resetear();
                $this->oConnection->select("cobros.codigo, general.medios_pago.codigo as cod_medio, general.medios_pago.medio, bancos.bancos.nombre as nombreBanco, tarjetas.tipos_debito.nombre as nombreTarj, medio_debito.cupon, medio_debito.cod_autorizacion, caja.nombre as nombreCaja, ($subquery) as valor");
                $this->oConnection->from('cobros');
                $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.tipo = "COBRO"', "left");
                $this->oConnection->join('general.medios_pago', 'general.medios_pago.codigo = cobros.medio_pago');
                $this->oConnection->join('caja', 'caja.codigo = cobros.cod_caja');
                $this->oConnection->join('medio_debito', 'medio_debito.cod_cobro = cobros.codigo');
                $this->oConnection->join('bancos.bancos', 'bancos.bancos.codigo = medio_debito.cod_bco_emisor', "left");
                $this->oConnection->join('tarjetas.tipos_debito', 'tarjetas.tipos_debito.codigo = medio_debito.cod_tipo', "left");
                $this->oConnection->where('cobros.codigo', $this->codigo);
                $this->oConnection->group_by('cobros.codigo');
                $query = $this->oConnection->get();
                return $query->result_array();
                break;
        }
    }

    public function anularCobro($motivo, $comentario, $usuario, $abrecaja = false) {
        if ($this->estado != Vcobros::getEstadoanulado()) {
            $objcaja = new Vcaja($this->oConnection, $this->cod_caja);
            $medios = $objcaja->getMediosPago($this->medio_pago);
            if (count($medios) > 0) {
                $this->estado = Vcobros::getEstadoanulado();
                $this->guardarCobros();
                $estadoHistoricos = new Vcobro_estado_historico($this->oConnection);
                $arrGuardarEstadoHistorico = array(
                    'cod_cobro' => $this->codigo,
                    'estado' => $this->estado,
                    'fecha_hora' => date('Y-m-d H:m:i'),
                    'cod_motivo' => $motivo,
                    'comentario' => $comentario,
                    'cod_usuario' => $usuario
                );
                $estadoHistoricos->setCobro_estado_historico($arrGuardarEstadoHistorico);
                $estadoHistoricos->guardarCobro_estado_historico();
                $condiciones = array(
                    'cod_cobro' => $this->codigo,
                    'tipo' => 'COBRO',
                    'estado <>' => 'anulado',
                );
                $renglonesCtacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, $condiciones);
                foreach ($renglonesCtacteImputaciones as $renglon) {
                    $renglonCtaCteImputaciones = new Vctacte_imputaciones($this->oConnection, $renglon['codigo']);
                    $renglonCtaCteImputaciones->anular($usuario);
                }
                $condiciones2 = array('cod_concepto' => 'COBROS', 'concepto' => $this->codigo);
                $movimientos = Vmovimientos_caja::listarMovimientos_caja($this->oConnection, $condiciones2);
                $debe = 0;
                $haber = 0;
                foreach ($movimientos as $mov) {
                    $debe = $debe + $mov['debe'];
                    $haber = $haber + $mov['haber'];
                }
                if ($debe != $haber) {
                    if ($abrecaja && $objcaja->estado == Vcaja::$estadocerrada) {
                        $objcaja->abrir($usuario);
                    }
                    if ($objcaja->estado == Vcaja::$estadoabierta) {
                        $movCaja = new Vmovimientos_caja($this->oConnection);
                        if ($debe > $haber) {
                            $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, 0, $debe - $haber, null, $usuario, $this->cod_caja, Vmovimientos_caja::getConceptoCobros(), $this->codigo, date("Y-m-d H:i:s"));
                        } else {
                            $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, $haber - $debe, 0, null, $usuario, $this->cod_caja, Vmovimientos_caja::getConceptoCobros(), $this->codigo, date("Y-m-d H:i:s"));
                        }
                    }
                }
            }
        }
    }
    
    //Ticket -4840- mmori - habilitar cobro al habilitar factura
    public function habilitarCobro($motivo = null, $comentario= null, $usuario = null, $abrecaja = false) 
    {
        if ($this->estado == Vcobros::getEstadoanulado()) 
        {
            $objcaja = new Vcaja($this->oConnection, $this->cod_caja);
            $medios = $objcaja->getMediosPago($this->medio_pago);
            if (count($medios) > 0) 
            {
                $this->estado = Vcobros::getEstadoConfirmado();
                $this->guardarCobros();
                $estadoHistoricos = new Vcobro_estado_historico($this->oConnection);
                $arrGuardarEstadoHistorico = array(
                    'cod_cobro' => $this->codigo,
                    'estado' => $this->estado,
                    'fecha_hora' => date('Y-m-d H:m:i'),
                    'cod_motivo' => $motivo,
                    'comentario' => $comentario,
                    'cod_usuario' => $usuario
                );
                $estadoHistoricos->setCobro_estado_historico($arrGuardarEstadoHistorico);
                $estadoHistoricos->guardarCobro_estado_historico();
                
                $condiciones = array('cod_cobro' => $this->codigo,'tipo' => 'COBRO','estado' => 'anulado',);
                $renglonesCtacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, $condiciones);
                
                foreach ($renglonesCtacteImputaciones as $renglon) 
                {
                    $renglonCtaCteImputaciones = new Vctacte_imputaciones($this->oConnection, $renglon['codigo']);
                    $renglonCtaCteImputaciones->confirmar($usuario, $this->fechareal);
                }
                
                $condiciones2 = array('cod_concepto' => 'COBROS', 'concepto' => $this->codigo);
                $movimientos = Vmovimientos_caja::listarMovimientos_caja($this->oConnection, $condiciones2);

                $movCaja = new Vmovimientos_caja($this->oConnection);
                $movCaja->guardar(date("Y-m-d H:i:s"), $this->medio_pago, 0, $this->importe, null, $usuario, $this->cod_caja, Vmovimientos_caja::getConceptoCobros(), $this->codigo, date("Y-m-d H:i:s"));

            }
        }
    }
    
    static function getCobrosEntreFechas(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $discriminarConceptos = false){
        if ($discriminarConceptos){
            $conexion->select("conceptos.key AS label");
            $conexion->select("conceptos.codigo AS cod_concepto");
            $conexion->select("SUM(ctacte_imputaciones.valor) AS data");
            $conexion->join("ctacte_imputaciones", "ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.estado = 'confirmado'");
            $conexion->join("ctacte", "ctacte.codigo = ctacte_imputaciones.cod_ctacte");
            $conexion->join("conceptos", "conceptos.codigo = ctacte.cod_concepto");
            $conexion->group_by("conceptos.key");
        } else {
            $conexion->select("SUM(cobros.importe) AS total");
        }
        $conexion->from("cobros");
        $conexion->where("cobros.estado", self::$estadoconfirmado);
        if ($fechaDesde != null){
            $conexion->where("DATE(cobros.fechaalta) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(cobros.fechaalta) <=", $fechaHasta);
        }
        $query = $conexion->get();
//        echo $conexion->last_query(); die();
        return $query->result_array();
    }
    
    static public function getReporteIngresos(CI_DB_mysqli_driver $conexion, $ingreso, $fecha_desde, $fecha_hasta){

        $conexion->select("cobros.codigo as 'Codigo Cobro'");
        $conexion->select("cobros.cod_alumno as 'Codigo Alumno'");
        $conexion->select("cobros.fechaalta as 'Fecha'");
        $conexion->select("conceptos.key as 'Concepto'");
        $conexion->select("cobros.importe as 'Monto'");
        $conexion->from("cobros");
        $conexion->join("ctacte_imputaciones", "ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.estado = 'confirmado'");
        $conexion->join("ctacte", "ctacte.codigo = ctacte_imputaciones.cod_ctacte");
        $conexion->join("conceptos", "conceptos.codigo = ctacte.cod_concepto");
        $conexion->where("DATE(cobros.fechaalta) >=", $fecha_desde);
        $conexion->where("DATE(cobros.fechaalta) <=", $fecha_hasta);
        $conexion->where("conceptos.key =", $ingreso);

        $query = $conexion->get();

        $respuesta['data']=array();
        foreach ($query->result_array() as $row)
        {
            $unarow = array($row['Codigo Cobro'],$row['Codigo Alumno'],$row['Fecha'],lang($row['Concepto']),"$".number_format((float)$row['Monto'],2,',','.'));
            array_push($respuesta['data'],$unarow);
        }

        return $respuesta;

    }
    static public function getReporteIngresos2(CI_DB_mysqli_driver $conexion, $ingreso, $fecha_desde, $fecha_hasta){

        $conexion->select("cobros.codigo as 'cod_cobro'");
        $conexion->select("cobros.cod_alumno as 'Codigo Alumno'");
        $conexion->select("DATE_FORMAT(DATE(cobros.fechaalta), '%d/%m/%Y') as 'Fecha'", false);
        $conexion->select("conceptos.key as 'Concepto'");
        $conexion->select("cobros.importe as 'Monto'");
        $conexion->select("CONCAT( alumnos.apellido ,' ', alumnos.nombre ) as 'nombreAlu'", false);
        $conexion->from("cobros");
        $conexion->join("ctacte_imputaciones", "ctacte_imputaciones.cod_cobro = cobros.codigo AND ctacte_imputaciones.estado = 'confirmado'");
        $conexion->join("ctacte", "ctacte.codigo = ctacte_imputaciones.cod_ctacte");
        $conexion->join("conceptos", "conceptos.codigo = ctacte.cod_concepto");
        $conexion->join("alumnos", "alumnos.codigo = cobros.cod_alumno");
        $conexion->where("DATE(cobros.fechaalta) >=", $fecha_desde);
        $conexion->where("DATE(cobros.fechaalta) <=", $fecha_hasta);
        $conexion->where("conceptos.codigo =", $ingreso);
        $conexion->group_by("cobros.codigo");

        $query = $conexion->get();

        $respuesta = array();
        foreach ($query->result_array() as $row)
        {
            $unarow = array($row['cod_cobro'],$row['nombreAlu'],$row['Fecha']);
            array_push($respuesta,$unarow);
        }

        return $respuesta;

    }
    
    static function getCobrosMensuales(CI_DB_mysqli_driver $conexion, array $arrPeriodos = null) {
        $conexion->select("periodo");
        $conexion->select("SUM(importe) AS total");
        $conexion->from("cobros");
        $conexion->where("estado", self::$estadoconfirmado);
        if ($arrPeriodos != null) {
            $conexion->where_in("periodo", $arrPeriodos);
        }
        $conexion->group_by("periodo");
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getSumValorImputacionesCobro() {
        $this->oConnection->select('sum(ctacte_imputaciones.valor) as totImputaciones');
        $this->oConnection->from('ctacte_imputaciones');
        $this->oConnection->where('ctacte_imputaciones.cod_cobro', $this->codigo);
        $this->oConnection->where('ctacte_imputaciones.tipo', "COBRO");
        $this->oConnection->where('ctacte_imputaciones.estado <>', Vcobros::getEstadoanulado());
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getCaja() {
        $myCaja = new Vcaja($this->oConnection, $this->cod_caja);
        return $myCaja;
    }

    public function asociarBoleto($codBoletoHistorico) {
        return $this->oConnection->insert("cobros_boletos", array("cod_cobro" => $this->codigo, "cod_boleto_historico" => $codBoletoHistorico));
    }

    static function getEstadoConfirmado() {
        return self::$estadoconfirmado;
    }

    static function getEstadoPendiente() {
        return self::$estadopendiente;
    }

    static function getEstadoanulado() {
        return self::$estadoanulado;
    }

    static function getEstadoError() {
        return self::$estadoerror;
    }

    static function getEstados() {
        return self::$arrayEstados;
    }

    public function errorCobro($motivo = null, $comentario = null, $usuario = null) {
        $this->estado = Vcobros::getEstadoError();
        $this->guardarCobros();
        $estadoHistoricos = new Vcobro_estado_historico($this->oConnection);
        $arrGuardarEstadoHistorico = array(
            'cod_cobro' => $this->codigo,
            'estado' => $this->estado,
            'fecha_hora' => date('Y-m-d H:m:i'),
            'cod_motivo' => $motivo,
            'comentario' => $comentario,
            'cod_usuario' => $usuario
        );
        $estadoHistoricos->setCobro_estado_historico($arrGuardarEstadoHistorico);
        $estadoHistoricos->guardarCobro_estado_historico();
    }

    public function desasociarFactura() {
        $this->oConnection->delete('facturas_cobros', array('cod_cobro' => $this->codigo));
    }

    public function getFacturasAsociadas($facturaCompleta = false) {
        if ($facturaCompleta){
            $this->oConnection->select("facturas_propiedades.valor");
            $this->oConnection->from("facturas_propiedades");
            $this->oConnection->where("facturas_propiedades.cod_factura = facturas.codigo");
            $sqNumeroFactura = $this->oConnection->return_query();
            $this->oConnection->resetear();
            $this->oConnection->select("facturas.*", false);
            $this->oConnection->select("($sqNumeroFactura) AS numero_factura", false);
            $this->oConnection->select("general.puntos_venta.prefijo");
            $this->oConnection->select("general.tipos_facturas.factura");
            $this->oConnection->join("facturas", "facturas.codigo = facturas_cobros.cod_factura");
            $this->oConnection->join("general.puntos_venta", "general.puntos_venta.codigo = facturas.punto_venta");
            $this->oConnection->join("general.tipos_facturas", "general.tipos_facturas.codigo = general.puntos_venta.tipo_factura");
        } else {
            $this->oConnection->select('cod_factura');
        }
        $this->oConnection->from('facturas_cobros');
        $this->oConnection->where('facturas_cobros.cod_cobro', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    static public function abrir_periodos_cobros(CI_DB_mysqli_driver $conexion, array $arrFiliales, $periodoMes, $periodoAnio){
        $periodo = $periodoAnio.$periodoMes;
        $conexion->where("periodo", $periodo);
        $conexion->where_in("id_filial", $arrFiliales);
        return $conexion->delete("general.cobros_periodos_cerrados");
    }

    static public function cerrar_periodos_cobros(CI_DB_mysqli_driver $conexion, array $arrFiliales, $periodoMes, $periodoAnio){
        $resp = self::abrir_periodos_cobros($conexion, $arrFiliales, $periodoMes, $periodoAnio);
        $periodo = $periodoAnio.$periodoMes;
        foreach ($arrFiliales as $filial){
            $arrParam = array("id_filial" => $filial, "periodo" => $periodo);
            $resp = $resp && $conexion->insert("general.cobros_periodos_cerrados", $arrParam);
        }
        return $resp;
    }
    
    static function periodoCobroCerrado(CI_DB_mysqli_driver $conexion, $idFilial, $periodoMes, $periodoAnio){
        $periodo = $periodoAnio.$periodoMes;
        $conexion->select("periodo");
        $conexion->from("general.cobros_periodos_cerrados");
        $conexion->where("id_filial", $idFilial);
        $conexion->where("periodo", $periodo);
        $query = $conexion->get();
        return $query->num_rows() > 0;
    }
}
