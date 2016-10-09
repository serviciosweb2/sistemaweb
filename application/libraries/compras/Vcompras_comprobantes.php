<?php

/**
 * Class Vcompras_comprobantes
 *
 * Class  Vcompras_comprobantes maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage compras renglones
 * @author   VAne
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcompras_comprobantes extends Tcompras_comprobantes {

    var $estadohabilitado = 'habilitado';
    var $estadoinhabilitado = 'inhabilitado';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function guardar($codcompra, $codusuario, $codcomprobante, $total, $nrocomprobante = null, $estado = null, $fecha = null, $tipo = null,$punto_venta=null) {
        $this->cod_compra = $codcompra;
        $this->cod_usuario = $codusuario;
        $this->cod_comprobante = $codcomprobante;
        $this->estado = $estado == null ? $this->estadohabilitado : $estado;
        $this->fecha = date('Y-m-d H:m:s');
        $this->fecha_comprobante = $fecha == null ? date('Y-m-d') : $fecha;
        $this->nro_comprobante = $nrocomprobante;
        $this->total = $total;
        $this->guardarCompras_comprobantes();
        
        $array = array('cod_compra_comprobante' => $this->codigo);
        $this->oConnection->delete('compras_tipos_factura',$array);
       
        if ($tipo != null) {
            $datos = array('cod_compra_comprobante' => $this->codigo,
                'cod_tipo_factura' => $tipo,
                'punto_venta' => $punto_venta,
                );
            $this->oConnection->insert('compras_tipos_factura', $datos);
        }
    }
    
    public function baja(){
        $this->estado = $this->estadoinhabilitado;
        return $this->guardarCompras_comprobantes();
    }

}
