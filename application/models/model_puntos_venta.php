<?php

/**
 * Model_talonarios
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_puntos_venta extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arrCodigoFilial) {
        parent::__construct();
        $this->codigo_filial = $arrCodigoFilial['codigo_filial'];
    }
    
    public function guardarPuntoVenta($codigo, $proximoNumero, $activo, $arrUsuariosPermisos){
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $myPuntoVenta = new Vpuntos_venta($conexion, $codigo);
        $myPuntoVenta->nro = $proximoNumero;
        $myPuntoVenta->estado = $activo == 1 ? Vpuntos_venta::getEstadoHabilitado() : Vpuntos_venta::getEstadoInhabilitado();
        $myPuntoVenta->guardarPuntos_venta();
        foreach ($arrUsuariosPermisos as $usuarioPermiso){
            if ($usuarioPermiso['usuario_habilitado'] == 1){
                $myPuntoVenta->habilitarUsuario($usuarioPermiso['cod_usuario']);
            } else {
                $myPuntoVenta->deshabilitarUsuario($usuarioPermiso['cod_usuario']);
            }
        }
        if ($conexion->trans_status()){
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }
    
    public function getUsuariosHabilitados($puntoVenta){
        $conexion = $this->load->database($this->codigo_filial, true);
        $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta);
        $arrUsuarios = $myPuntoVenta->getUsuariosHabilitados();
        $arrResp = array();
        foreach ($arrUsuarios as $usuario){
            $arrResp[] = $usuario['cod_usuario'];
        }
        return $arrResp;
    }
    
    public function getPuntosVentas($estado){
        $conexion = $this->load->database($this->codigo_filial, true);
        $puntosVentas = Vpuntos_venta::getPuntosVentas($conexion, $this->codigo_filial, $estado);
        return $puntosVentas;
    }
    
    public function getDetallePuntoVenta($arrayCodigo){
        $conexion = $this->load->database($this->codigo_filial, true);
        $objTalonario = new Vtalonarios($conexion, $arrayCodigo['cod_tipo_factura'], $arrayCodigo['cod_facturante'], $arrayCodigo['punto_venta']);
        $usuarios = $objTalonario->getDetallesPuntoVenta();
        $arrayUsuarios = '';
        foreach($usuarios as $usuario){
            $arrayUsuarios[] = $usuario['cod_usuario'];
        }
        return $arrayUsuarios;
    }
    
    public function getObjPuntoVenta($codigo){
        $conexion = $this->load->database($this->codigo_filial, true);
        $myPuntoVenta = new Vpuntos_venta($conexion, $codigo);
        return $myPuntoVenta;
    }
    
    public function getFacturantesFacturar($codusuario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $usuario = new Vusuarios_sistema($conexion, $codusuario);
        $arrFacturantes = $usuario->getFacturantesHabilitadosFacturar($this->codigo_filial);
        return $arrFacturantes;
    } 
}