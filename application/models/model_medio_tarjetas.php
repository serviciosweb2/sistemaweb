<?php

/**
 * model_medio_tarjetas
 * 
 * Description...
 * 
 * @package model_medio_tarjetas
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_medio_tarjetas extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo"];
    }

    public function getTiposTarjetasPais() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $filial = new Vfiliales($conexion, $this->codigo_filial);
        //$condiciones = array('cod_pais' => $filial->pais);
        $tipostarjetas = Vtipos_tarjetas::listarTipos_tarjetas($conexion);
        return $tipostarjetas;
    }



    public function getTiposDebitoPais() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->select("*");
        $conexion->from("tarjetas.tipos_debito");
        $query = $conexion->get();

        return $query->result_array();
    }

    public function getTerminales($solohabilitadas = false, $conformato = false) {
        $condiciones = array();
        $conexion = $this->load->database($this->codigo_filial, true);
        $terminales = Vpos_terminales::getTerminales($conexion, $solohabilitadas);
        if ($conformato) {
            foreach ($terminales as $key => $value) {
                $terminales[$key]['detalle'] = $value['cod_interno'] . ' [' . $value['nombre'] . ']';
            }
        }
        return $terminales;
    }

    public function getTarjetasTerminal($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objterminal = new Vpos_terminales($conexion, $codigo);
        $arrtarjetas = $objterminal->getTarjetas();
        $tarjetas = array();
        foreach ($arrtarjetas as $row) {
            $tarjetas[] = $row['cod_tipo'];
        }
        return $tarjetas;
    }


    public function getTarjetasDebitoTerminal($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objterminal = new Vpos_terminales($conexion, $codigo);
        $arrtarjetas = $objterminal->getTarjetasDebito();
        $tarjetas = array();
        foreach ($arrtarjetas as $row) {
            $tarjetas[] = $row['cod_tipo'];
        }
        return $tarjetas;
    }


    public function getTiposCapturaTerminales() {
        $arrDatos = array(array('codigo' => 'pos', 'nombre' => lang('pos')),
            array('codigo' => 'internet', 'nombre' => lang('internet')),
            array('codigo' => 'manual', 'nombre' => lang('manual')),
            array('codigo' => 'otro', 'nombre' => lang('otro')));
        return $arrDatos;
    }

    public function getTerminal($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condicion = array('codigo' => $codigo);
        $contrato = Vpos_terminales::listarPos_terminales($conexion, $condicion);
        return $contrato[0];
    }

    public function guardarTerminal($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();

        $terminal = new Vpos_terminales($conexion, $datos['codigo']);
        $terminal->cod_punto_venta = $datos['codigo'] == '-1' ? $datos['operador_pos'] : $terminal->cod_punto_venta;
        $terminal->cod_interno = $datos['codigo_interno'];
        $terminal->tipo_captura = $datos['tipo_captura'];
        $terminal->estado = $datos['estado'] == 'on' ? Vpos_terminales::getEstadoHabilitado() : Vpos_terminales::getEstadiInhabilitado();
        $terminal->guardarPos_terminales();
        $terminal->deleteTarjetas();
        $terminal->deleteDebitos();
        //asigno tarjetas 
        foreach ($datos['tarjetas'] as $cod_tarjeta) {
            $terminal->setTarjeta($cod_tarjeta);
        }
        foreach ($datos['debitos'] as $cod_tarjeta){
            $terminal->setDebito($cod_tarjeta);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

}
