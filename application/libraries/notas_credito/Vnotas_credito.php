<?php

/**
 * Class Vnotas_credito
 *
 * Class  Vnotas_credito maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vnotas_credito extends Tnotas_credito {

    static private $estadoconfirmado = 'confirmado';
    static private $estadopendiente = 'pendiente';
    static private $estadoanulado = 'anulado';
    private static $motivos = array(
        array("id" => '1', "motivo" => 'error_facturacion'),
        array("id" => '2', "motivo" => 'bonificacion'));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarNotasCreditoDataTable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike, $arrLimit, $arrSort = null, $contar = false, $order, $separador = null, $separadorDecimal = null) {
        $mostrarSubquery = '';
        if ($contar == false) {
            $conexion->select('ifnull(sum(ctacte_imputaciones.valor),0) as totImputaciones', false);
            $conexion->from('ctacte_imputaciones');
            $conexion->where('ctacte_imputaciones.cod_cobro = notas_credito.codigo');
            $conexion->where('ctacte_imputaciones.tipo = "NOTA_CREDITO"');
            $conexion->where('ctacte_imputaciones.estado', 'confirmado');
            $subquery = $conexion->return_query();
            $conexion->resetear();
            $mostrarSubquery = "notas_credito.importe - ($subquery) as saldoRestante";
        }
        $nombreApellido = formatearNomApeQuery();

        $conexion->select("notas_credito.codigo,CONCAT($nombreApellido) as nombre_apellido, notas_credito.importe, notas_credito.fechareal, notas_credito.estado, $mostrarSubquery", false);
        $conexion->from('notas_credito');
        $conexion->join('alumnos', 'alumnos.codigo = notas_credito.cod_alumno');

        if (count($arrCondicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondicioneslike as $key => $value) {
                if ($key == 'nombre_apellido' || $key == 'notas_credito.importe') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                    $arrTemp[] = "REPLACE(notas_credito.importe, '$separadorDecimal ',' ') LIKE REPLACE('%$value%', '$separadorDecimal ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
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
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    public function getImputaciones() {
        $this->oConnection->select('ctacte_imputaciones.codigo, ctacte_imputaciones.cod_ctacte, ctacte_imputaciones.valor, ctacte_imputaciones.estado, ctacte_imputaciones.fecha');
        $this->oConnection->from('notas_credito');
        $this->oConnection->join('ctacte_imputaciones', 'ctacte_imputaciones.cod_cobro = notas_credito.codigo AND ctacte_imputaciones.tipo = "NOTA_CREDITO"');
        $this->oConnection->where('notas_credito.codigo', $this->codigo);
        $this->oConnection->where('ctacte_imputaciones.estado <>', 'anulado');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function guardarRenglon($cod_factura, $importe) {

        $arr = array(
            "cod_nota_credito" => $this->codigo,
            "cod_factura" => $cod_factura,
            "importe" => $importe
        );
        $this->oConnection->insert('notas_credito_renglones', $arr);
    }

    public function imputar($id_ctacte, $valor, $cod_usuario) {
        $ctacte_imputaciones = new Vctacte_imputaciones($this->oConnection);
        $ctacte_imputaciones->cod_cobro = $this->codigo;
        $ctacte_imputaciones->cod_ctacte = $id_ctacte;
        $ctacte_imputaciones->cod_usuario = $this->cod_usuario;
        $ctacte_imputaciones->tipo = 'NOTA_CREDITO';
        $ctacte_imputaciones->valor = $valor;
        $ctacte_imputaciones->fecha = date('Y-m-d H:i:s');
        $ctacte_imputaciones->estado = 'pendiente';
        $ctacte_imputaciones->guardarCtacte_imputaciones();

        return $ctacte_imputaciones->getCodigo();
    }

    public function getSumValorImputaciones() {
        $this->oConnection->select('sum(ctacte_imputaciones.valor) as totImputaciones');
        $this->oConnection->from('ctacte_imputaciones');
        $this->oConnection->where('ctacte_imputaciones.cod_cobro', $this->codigo);
        $this->oConnection->where('ctacte_imputaciones.tipo', "NOTA_CREDITO");
        $this->oConnection->where('ctacte_imputaciones.estado <>', Vcobros::getEstadoanulado());
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getRenglones() {
        $conexion = $this->oConnection;
        $conexion->select('notas_credito_renglones.*, facturas_propiedades.valor as numero, general.puntos_venta.prefijo, general.tipos_facturas.factura as tipo');
        $conexion->from("facturas");
        $conexion->join("notas_credito_renglones", "notas_credito_renglones.cod_factura = facturas.codigo");
        $conexion->join("facturas_propiedades", "facturas_propiedades.cod_factura = facturas.codigo and facturas_propiedades.propiedad = 'numero_factura'");
        $conexion->join("general.puntos_venta", "general.puntos_venta.codigo = facturas.punto_venta");
        $conexion->join("general.tipos_facturas", "general.tipos_facturas.codigo = general.puntos_venta.tipo_factura");
        $conexion->where("notas_credito_renglones.cod_nota_credito", $this->codigo);
        $conexion->order_by("facturas.codigo", "desc");
        $query = $conexion->get();

        return $query->result_array();
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

    public function confirmar($codusuario = null) {
        $respuesta = 0;

        $this->estado = Vnotas_credito::getEstadoConfirmado();
        $respuesta = $this->guardarNotas_credito();

        $estadoHistoricos = new Vnotas_credito_historico($this->oConnection);
        $arrGuardarEstadoHistorico = array(
            'cod_nc' => $this->codigo,
            'estado' => $this->estado,
            'fecha_hora' => date('Y-m-d H:i:s'),
            'cod_usuario' => $codusuario,
            'motivo' => $this->motivo
        );

        $estadoHistoricos->setNotas_credito_historico($arrGuardarEstadoHistorico);
        $estadoHistoricos->guardarNotas_credito_historico();
        // cambio todas las imputaciones al estado de NC
        $CtacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, array('cod_cobro' => $this->codigo, 'tipo' => 'NOTA_CREDITO', 'estado' => 'pendiente'));

        foreach ($CtacteImputaciones as $ctacte_imputaciones) {
            $ObjCtaCteImputaciones = new Vctacte_imputaciones($this->oConnection, $ctacte_imputaciones['codigo']);
            $ObjCtaCteImputaciones->confirmar($codusuario, $this->fechareal);
        }

        return $respuesta;
    }

    public function anular($motivo, $comentario, $usuario) {
        $respuesta = 0;
        if ($this->estado != Vnotas_credito::getEstadoanulado()) {

            $this->estado = Vnotas_credito::getEstadoanulado();
            $respuesta = $this->guardarNotas_credito();

            $estadoHistoricos = new Vnotas_credito_historico($this->oConnection);
            $arrGuardarEstadoHistorico = array(
                'cod_nc' => $this->codigo,
                'estado' => $this->estado,
                'fecha_hora' => date('Y-m-d H:m:i'),
                'cod_motivo' => $motivo,
                'comentario' => $comentario,
                'cod_usuario' => $usuario,
                'motivo' => $this->motivo
            );
            $estadoHistoricos->setNotas_credito_historico($arrGuardarEstadoHistorico);
            $estadoHistoricos->guardarNotas_credito_historico();

            $condiciones = array(
                'cod_cobro' => $this->codigo,
                'tipo' => 'NOTA_CREDITO',
                'estado <>' => 'anulado',
            );

            $renglonesCtacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($this->oConnection, $condiciones);

            foreach ($renglonesCtacteImputaciones as $renglon) {
                $renglonCtaCteImputaciones = new Vctacte_imputaciones($this->oConnection, $renglon['codigo']);
                $renglonCtaCteImputaciones->anular($usuario);
            }

            $this->bajaRenglones();
        }
        return $respuesta;
    }

    public function bajaRenglones() {
        $datos = array(
            'baja' => 1
        );

        $this->oConnection->where('cod_nota_credito', $this->codigo);
        $this->oConnection->update('notas_credito_renglones', $datos);
    }

    static function getMotivos($id = false) {
        $devolver = '';
        if ($id != false) {
            $array = self::$motivos;
            foreach ($array as $value) {
                foreach ($id as $tipoMotivo) {
                    if ($value['id'] == $tipoMotivo) {

                        $devolver[] = array(
                            'id' => $value['id'],
                            'motivo' => lang($value['motivo'])
                        );
                    }
                }
            }
        } else {

            $motivos = self::$motivos;

            foreach ($motivos as $key => $motivo) {
                $motivos[$key] = array('id' => $motivo['id'], 'motivo' => lang($motivo['motivo']));
            }

            return $motivos;
        }
        return $devolver;
    }

}
