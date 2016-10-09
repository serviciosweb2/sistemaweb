<?php

/**
* Class Vimpuestos
*
*Class  Vimpuestos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vimpuestos extends Timpuestos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    public function getDetallesImpuestos(){
        $this->oConnection->select('conceptos.codigo as cod_concepto, conceptos.key as nom_concepto,  impuestos.nombre, impuestos.valor, impuestos.baja');
        $this->oConnection->from('conceptos_impuestos');
        $this->oConnection->join('impuestos','impuestos.codigo = conceptos_impuestos.cod_impuesto');
        $this->oConnection->join('conceptos','conceptos.codigo = conceptos_impuestos.cod_concepto');
        $this->oConnection->where('conceptos_impuestos.cod_impuesto',  $this->codigo);
        $this->oConnection->where('impuestos.baja',0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    function getValorImpuesto() {
        $this->oConnection->select('*');
        $this->oConnection->from('impuestos');
        $this->oConnection->where('codigo', $this->codigo);
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        return $resultado[0]['valor'];
    }
    
   


}

?>