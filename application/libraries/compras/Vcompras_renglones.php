<?php

/**
 * Class Vcomprasreglones
 *
 * Class  Vcomprasreglones maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage compras renglones
 * @author   VAne
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcompras_renglones extends Tcompras_renglones {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardar($codcompra, $codarticulo, $cantidad, $preciounitario, $total, $impuestos) {
        $this->cod_compra = $codcompra;
        $this->cod_articulo = $codarticulo;
        $this->cantidad = $cantidad;
        $this->precio_unitario = $preciounitario;
        $this->precio_total = $total;
        $this->guardarCompras_renglones();

        $this->oConnection->delete('compras_renglones_impuestos', array('cod_compras_renglon' => $this->codigo));
        foreach ($impuestos as $rowimpuesto) {
            $datos = array('cod_compras_renglon' => $this->codigo,
                'cod_impuesto' => $rowimpuesto);
            $this->oConnection->insert('compras_renglones_impuestos', $datos);
        }
    }

    public function getImpuestos() {
        $this->oConnection->select('*');
        $this->oConnection->from('compras_renglones_impuestos');
        $this->oConnection->join('impuestos', 'impuestos.codigo = compras_renglones_impuestos.cod_impuesto');
        $this->oConnection->where('compras_renglones_impuestos.cod_compras_renglon', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function baja(){
        $this->baja =1;
        return  $this->guardarCompras_renglones();
    }

}
