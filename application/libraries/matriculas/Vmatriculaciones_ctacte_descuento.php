<?php

/**
* Class Vmatriculaciones_ctacte_descuento
*
*Class  Vmatriculaciones_ctacte_descuento maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vmatriculaciones_ctacte_descuento extends Tmatriculaciones_ctacte_descuento{

    static private $tipoDescuentoFormaPago = "plan_pago";
    static private $tipoDescuentoManual = "manual";
    
    static private $estadoCondicionado = "condicionado";
    static private $estadoCondicionadoPerdido = "condicionado_perdido";
    static private $estadoNoCondicionado = "no_condicionado";
    static private $estadoCondicionadoDescartado = "condicionado_descartado";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    /**
     * Recupera un descuento condicionado perdido actualizando el registro de ctacte correspondiente
     * 
     * @return boolean
     */
    public function recuperarCondicionadoPerdido(){
        $this->estado = self::$estadoNoCondicionado;
        if ($this->guardarMatriculaciones_ctacte_descuento()){
            $myCtacte = new Vctacte($this->oConnection, $this->cod_ctacte);
            $importe = round($myCtacte->importe - ($myCtacte->importe * $this->descuento / 100), 2);
            $myCtacte->importe = $importe;
            return $myCtacte->guardarCtacte();
        } else {
            return false;
        }        
    }
    
    public function descartarCondicionado(){
        $this->estado = self::$estadoCondicionadoDescartado;
        return $this->guardarMatriculaciones_ctacte_descuento();
    }
    
    static public function getEstadoCondicionadoDescartado(){
        return self::$estadoCondicionadoDescartado;
    }
    
    static public function getEstadoCondicionado(){
        return self::$estadoCondicionado;
    }
    
    static public function getEstadoCondicionadoPerdido(){
        return self::$estadoCondicionadoPerdido;
    }
    
    static public function getEstadoNoCondicionado(){
        return self::$estadoNoCondicionado;
    }
    
    static public function getTipoDescuentoFormaPago(){
        return self::$tipoDescuentoFormaPago;
    }
    
    static public function getTipoDescuentoManual(){
        return self::$tipoDescuentoManual;
    }    
    
    /**
     * Recupera registros de matriuclaciones_ctacte_descuentos
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param string $idioma            El idioma para recuperar el nombre del curso ("es", "pt", "in")
     * @param string $estado            El estado del descuento a recuperar (matriculaciones_ctacte_descuento::estado)
     * @param boolen $agrupado          Determina si se va a agrupar los resultados por codigo de matricula
     * @param integer $codMatricula     El codigo de matricula sobre el cual buscar descuento   
     * @param boolean $ctacteSinPago    Determina si se debe filtrar sobre ctacte con registro pagado o no   
     * @return array
     */
    static function getDescuentosMatricula(CI_DB_mysqli_driver $conexion, $idioma, $estado = null, $agrupado = true, 
            $codMatricula = null, $ctacteSinPago = null){
        $conceptosTemp = Vconceptos::listarConceptos($conexion, array("valor" => "ACADEMICO"));
        $arrConceptos = array();
        foreach ($conceptosTemp as $concepto){
            $arrConceptos[] = $concepto['codigo_padre'];
        }
        if ($agrupado){
            $nombreApellido = formatearNomApeQuery();
            $conexion->select("matriculas.codigo");
            $conexion->select("CONCAT($nombreApellido) as nombre_alumno", false);
            $conexion->select("general.cursos.nombre_$idioma AS nombre_curso");
            $conexion->select("ctacte.fechavenc AS fecha_perdida_descuento");
        } else {
            $conexion->select("ctacte.*");
            $conexion->select("matriculaciones_ctacte_descuento.descuento");
            $conexion->select("matriculaciones_ctacte_descuento.codigo AS codigo_descuento");
        }
        $conexion->from("matriculaciones_ctacte_descuento");
        $conexion->join("ctacte", "ctacte.codigo = matriculaciones_ctacte_descuento.cod_ctacte AND ctacte.cod_concepto IN (".implode(",", $arrConceptos).") AND ctacte.habilitado IN (1, 2)");
        $conexion->join("matriculas", "matriculas.codigo = ctacte.concepto");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        if ($estado != null){
            $tipoFiltro = is_array($estado) ? "where_in" : "where";
            $conexion->$tipoFiltro("matriculaciones_ctacte_descuento.estado", $estado);
        }
        if ($codMatricula != null){
            $conexion->where("matriculas.codigo", $codMatricula);
        }
        if ($ctacteSinPago !== null){
            if ($ctacteSinPago){
                $conexion->where("ctacte.pagado", 0);
            } else {
                $conexion->where("ctacte.pagado >", 0);
            }
        }
        if ($agrupado){
            $conexion->group_by("matriculas.codigo");
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    function desactivar(){
        $importeSumar = $this->importe;
        $myCtacte = new Vctacte($this->oConnection, $this->cod_ctacte);
        $myCtacte->importe += $importeSumar;
        $resp = $myCtacte->guardarCtacte();
        $this->activo = "0";
        $resp = $resp && $this->guardarMatriculaciones_ctacte_descuento();
        return $resp;
    }
    
    function reactivar(){
        $importeSumar = $this->importe;
        $myCtacte = new Vctacte($this->oConnection, $this->cod_ctacte);
        $myCtacte->importe -= $importeSumar;
        $resp = $myCtacte->guardarCtacte();
        $this->activo = "1";
        $resp = $resp && $this->guardarMatriculaciones_ctacte_descuento();
        return $resp;
    }
}