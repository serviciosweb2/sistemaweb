<?php

/**
* Class Vimpuestos_general
*
*Class  Vimpuestos_general maneja todos los aspectos de impuestos_general
*
* @package  SistemaIGA
* @subpackage Impuestos
* @author   Foox
* @version  $Revision: 1.1 $
* @access   private
*/

class Vimpuestos_general extends Timpuestos_general{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
     
    public function getImpuestos_operacionIVA() {
         $this->oConnection->select("codigo, nombre, pais");
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'cod_afip') as cod_afip");
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'tipo') as tipo");
        $this->oConnection->from("general.impuestos_general");
        $this->oConnection->having("tipo", "IVA");
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
     static function getOtros_tributos() {
         $this->oConnection->select("codigo, nombre, pais");
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'cod_afip') as cod_afip");
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'tipo') as tipo");
        $this->oConnection->from("general.impuestos_general");
        $this->oConnection->having("tipo", "otro_tributo");
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCodigoAfip() {
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'cod_afip') as cod_afip");
        $this->oConnection->from("general.impuestos_general");
        $this->oConnection->where("general.impuestos_general.codigo", $this->codigo);
        $query = $this->oConnection->get();
        $resultado= $query->result_array();
        return $resultado[0]['cod_afip'];
    }
    
     public function getTipo() {
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'tipo') as tipo");
        $this->oConnection->from("general.impuestos_general");
        $this->oConnection->where("general.impuestos_general.codigo", $this->codigo);
        $query = $this->oConnection->get();
        $resultado= $query->result_array();
        return $resultado[0]['tipo'];
    }
    
    public function getValor() {
         $this->oConnection->select("(SELECT general.impuestos_propiedades.valor FROM general.impuestos_propiedades 
                 WHERE general.impuestos_propiedades.cod_impuesto = general.impuestos_general.codigo && general.impuestos_propiedades.propiedad = 'valor') as valor");
        $this->oConnection->from("general.impuestos_general");
        $this->oConnection->where("general.impuestos_general.codigo", $this->codigo);
        $query = $this->oConnection->get();
        $resultado= $query->result_array();
        return $resultado[0]['valor'];
    }

}

?>