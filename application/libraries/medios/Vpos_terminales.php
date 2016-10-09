<?php

/**
 * Class Vpos_terminales
 *
 * Class  Vpos_terminales maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vpos_terminales extends Tpos_terminales {

    static private $estadoHabilitado = "habilitado";
    static private $estadoInhabilitado = "inhabilitado";

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getTerminales(CI_DB_mysqli_driver $conexion, $solohabilitadas = false) {
        $conexion->select('pos_terminales.*, (SELECT tarjetas.pos_operadores.nombre FROM tarjetas.pos_puntos_venta 
                JOIN tarjetas.pos_contratos ON  tarjetas.pos_puntos_venta.cod_contrato = tarjetas.pos_contratos.codigo 
                JOIN tarjetas.pos_operadores ON tarjetas.pos_contratos.cod_operador = tarjetas.pos_operadores.codigo 
                WHERE tarjetas.pos_puntos_venta.codigo = pos_terminales.cod_punto_venta) AS nombre', false);
        $conexion->from('pos_terminales');
        if ($solohabilitadas) {
            $conexion->where('pos_terminales.estado', Vpos_terminales::getEstadoHabilitado());
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getTarjetas() {
        $conexion = $this->oConnection;
        $conexion->select('pos_terminales_tarjetas.cod_tipo');
        $conexion->from('pos_terminales_tarjetas');
        $conexion->where('pos_terminales_tarjetas.cod_terminal', $this->codigo);
        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado;
    }



    public function getTarjetasDebito() {
        $conexion = $this->oConnection;
        $conexion->select('pos_terminales_debito.cod_tipo');
        $conexion->from('pos_terminales_debito');
        $conexion->where('pos_terminales_debito.cod_terminal', $this->codigo);
        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado;
    }


    static public function getEstadoHabilitado() {
        return self::$estadoHabilitado;
    }

    static public function getEstadiInhabilitado() {
        return self::$estadoInhabilitado;
    }

    public function deleteTarjetas() {
        $this->oConnection->delete('pos_terminales_tarjetas', array('cod_terminal' => $this->codigo));
    }



    public function deleteDebitos() {
        $this->oConnection->delete('pos_terminales_debito', array('cod_terminal' => $this->codigo));
    }


    public function setTarjeta($cod_tarjeta) {
        $this->oConnection->insert('pos_terminales_tarjetas', array('cod_tipo' => $cod_tarjeta, 'cod_terminal' => $this->codigo));
    }

    public function setDebito($cod_tarjeta) {
        $this->oConnection->insert('pos_terminales_debito', array('cod_tipo' => $cod_tarjeta, 'cod_terminal' => $this->codigo));
    }
    public function getTiposTarjetas() {
        $conexion = $this->oConnection;
        $conexion->select('tarjetas.tipos_tarjetas.*', false);
        $conexion->from('pos_terminales_tarjetas');
        $conexion->join('tarjetas.tipos_tarjetas', 'tarjetas.tipos_tarjetas.codigo = pos_terminales_tarjetas.cod_tipo');
        $conexion->where('pos_terminales_tarjetas.cod_terminal', $this->codigo);

        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado;
    }



    public function getTiposDebito() {
        $conexion = $this->oConnection;
        $conexion->select('tarjetas.tipos_debito.*', false);
        $conexion->from('pos_terminales_debito');
        $conexion->join('tarjetas.tipos_debito', 'tarjetas.tipos_debito.codigo = pos_terminales_debito.cod_tipo');
        $conexion->where('pos_terminales_debito.cod_terminal', $this->codigo);

        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado;
    }


    public function getCodigoOperador() {
        $conexion = $this->oConnection;
        $conexion->select('tarjetas.pos_contratos.cod_operador AS operador');
        $conexion->from('tarjetas.pos_contratos');
        $conexion->join('tarjetas.pos_puntos_venta', 'tarjetas.pos_puntos_venta.cod_contrato = tarjetas.pos_contratos.codigo');
        $conexion->join('pos_terminales', 'tarjetas.pos_puntos_venta.codigo = pos_terminales.cod_punto_venta');
        $conexion->where('pos_terminales.codigo', $this->codigo);
        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado[0]['operador'];
    }

}
