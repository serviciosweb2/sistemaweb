<?php

/**
 * Class Vconceptos
 *
 * Class  Vconceptos maneja todos los aspectos de los conceptos de ctacte
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vconceptos extends Tconceptos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getKey($conexion, $codigo) {
        if ($codigo != false) {
            $condiciones = array('codigo' => $codigo);
        }
        $conceptos = Vconceptos::listarConceptos($conexion, $condiciones);
        return $conceptos[0]['key'];
    }

    public function getConceptos($sololectura = null, $wherein = null, $sonconceptos = 1, $arrpropiedades = null) {

        $this->oConnection->select('*');
        $this->oConnection->from($this->nombreTabla);
        if ($sololectura != null) {
            if ($sololectura) {//traigo los conceptos que tienen una propiedad de solo lectura 1(solo leer)
                $arrpropiedades[] = array('propiedad' => 'SOLO_LECTURA', 'valor' => '1');
            }
            if (!$sololectura) {//traigo los conceptos que no tienen una propiedad de solo lectura 1(leer y escribir)
                $this->oConnection->where('codigo NOT IN (select conceptos.codigo_padre from conceptos where conceptos.key = "SOLO_LECTURA" and  conceptos.valor = "1")');
            }
        }

        if ($sonconceptos == 1) {
            $this->oConnection->where('codigo_padre', 0);
        }

        if ($wherein != null) {
            $this->oConnection->where_in('codigo', $wherein);
        }

        if ($arrpropiedades != null) {
            foreach ($arrpropiedades as $arrpropiedad) {
                $this->oConnection->where('codigo IN (select conceptos.codigo_padre from conceptos where conceptos.key = "' . $arrpropiedad['propiedad'] . '" and  conceptos.valor = "' . $arrpropiedad['valor'] . '")');
            }
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getConceptosAcademicos() { // ESTA FUNCTION DEBERIA SER ESTATICA
        $this->oConnection->select('*');
        $this->oConnection->from($this->nombreTabla);
        $this->oConnection->where('codigo IN (select conceptos.codigo_padre from conceptos where conceptos.key = "TIPO" and  conceptos.valor = "ACADEMICO")');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getConceptosImpuesto($conexion, $cod_impuesto) {
        $conexion->select('conceptos.codigo, conceptos.key');
        $conexion->from('conceptos');
        $conexion->where("conceptos.codigo not in (select conceptos_impuestos.cod_concepto from conceptos_impuestos where conceptos_impuestos.cod_impuesto = $cod_impuesto)");
        $conexion->where('conceptos.codigo_padre', 0);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function guardar($key, $valor, $cod_padre) {
        $this->key = $key;
        $this->valor = $valor;
        $this->codigo_padre = $cod_padre;
        $this->guardarConceptos();
    }
    
     public function setearConceptoImpuesto($cod_impuesto){
        $arrGuardarConcepto = array(
            "cod_concepto"=>$this->codigo,
            "cod_impuesto"=> $cod_impuesto,
        );
        $this->oConnection->insert('conceptos_impuestos',$arrGuardarConcepto);
    }
    public function dessetearConceptoImpuesto(){
        $arrDelete = array(
            "cod_concepto"=>  $this->codigo
        );
        $this->oConnection->delete('conceptos_impuestos',$arrDelete);
    }
    
    public function getImpuestosConceptos(){
        $this->oConnection->select('conceptos_impuestos.cod_impuesto');
        $this->oConnection->from('conceptos_impuestos');
        $this->oConnection->where('conceptos_impuestos.cod_concepto',  $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

}
