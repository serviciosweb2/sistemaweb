<?php

/**
 * Description of Vfacturas_reglones
 *
 * @author Vane
 */
class Vfacturas_renglones extends Tfacturas_renglones {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function activar() {
        $this->anulada = 0;
        $this->guardarFacturas_renglones();
    }

    public function anular() {
        $this->anulada = 1;
        $this->guardarFacturas_renglones();
    }

    public function getImpuestos() {
        $this->oConnection->select('conceptos_impuestos.cod_impuesto AS cod_impuesto_filial, general.impuestos_general.codigo AS cod_impuesto_general');
        $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'valor') AS valor");
        $this->oConnection->from('ctacte');
        $this->oConnection->join('conceptos_impuestos', 'conceptos_impuestos.cod_concepto = ctacte.cod_concepto');
        $this->oConnection->join('impuestos', 'impuestos.codigo = conceptos_impuestos.cod_impuesto');
        $this->oConnection->join('general.impuestos_general', 'general.impuestos_general.codigo = impuestos.cod_impuesto');
        $this->oConnection->where('ctacte.codigo', $this->cod_ctacte);
        $this->oConnection->having('valor IS NOT NULL');
        $query = $this->oConnection->get();
        $imp_ctacte = $query->result_array();
        return $imp_ctacte;
    }

    public function getNeto() {
        $imp_ctacte = $this->getImpuestos();

        $suma_impuesto = 0;
        foreach ($imp_ctacte as $value) {
            $suma_impuesto += $value['valor'];
        }

        $neto_renglon = 100 * $this->importe / (100 + $suma_impuesto );

        return round($neto_renglon, 2, PHP_ROUND_HALF_EVEN);
    }

}
