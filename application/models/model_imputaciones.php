<?php

/**
 * Model_imputaciones
 * 
 * Description...
 * 
 * @package model_imputaciones
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_imputaciones extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function eliminarImputacion($codigo, $cod_usuario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();                                       // llevar, de ser posible, la conexion y la transaccion al controlador
        $imputacion = new Vctacte_imputaciones($conexion, $codigo);
        $objCobro = new Vcobros($conexion, $imputacion->cod_cobro);
        $objCobro->desasociarFactura();
        $imputacion->anular($cod_usuario);
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }        
    }

}
