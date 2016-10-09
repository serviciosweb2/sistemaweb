<?php

/**
 * Class Vplanes_pago
 *
 * Class  Vplanes_pago maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vplanes_pago extends Tplanes_pago {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarPlanesDataTable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike, $arrLimit = null, $arrSort = null, 
            $contar = false, $planAcademico = null, $modalidad = null, $periodo = null, $baja = null, $fechaInicioDesde = null, 
            $fechaInicioHasta = null, $fechaVigenciaDesde = null, $fechaVigenciaHasta = null) {
        $conexion->select("count(cod_tipo_periodo)", false);
        $conexion->from("planes_cursos_periodos");
        $conexion->where("cod_plan_pago = planes_pago.codigo");
        $conexion->where("cod_curso = general.cursos.codigo");
        $subqueryCantidad = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("MAX(codigo)", false);
        $conexion->from("general.planes_academicos");
        $conexion->where("estado", 'habilitado');
        $conexion->where("cod_curso = planes_cursos_periodos.cod_curso");
        $subqueryPlanAcademico = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("COUNT(cod_tipo_periodo)", false);
        $conexion->from("general.planes_academicos_periodos");
        $conexion->where("cod_plan_academico = ($subqueryPlanAcademico)");
        $subqueryCantidadPeriodos = $conexion->return_query();
        $conexion->resetear();      
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("general.cursos.nombre_pt");
        $conexion->select("general.cursos.nombre_in");
        $conexion->select("($subqueryCantidad) AS cantidad_periodos_plan");
        $conexion->select("($subqueryCantidadPeriodos) AS cantidad_periodos");        
        $conexion->join('planes_cursos_periodos', 'planes_cursos_periodos.cod_plan_pago = planes_pago.codigo');//no lista planes que no tiene asignados cursos.
        $conexion->join('general.planes_academicos','general.planes_academicos.codigo = planes_cursos_periodos.cod_curso');
        $conexion->join('general.cursos', 'general.cursos.codigo = general.planes_academicos.cod_curso', 'left');
        $conexion->join('general.tipos_periodos', 'general.tipos_periodos.codigo = planes_cursos_periodos.cod_tipo_periodo', 'left');
        if (count($arrCondicioneslike) > 0) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }
        if ($baja !== null){
            $conexion->where("planes_pago.baja", $baja);
        }
        if ($periodo != null){
            $conexion->where("planes_cursos_periodos.cod_tipo_periodo", $periodo);
        }
        if ($modalidad != null){
            $conexion->where("planes_cursos_periodos.modalidad", $modalidad);
        }
        if ($planAcademico != null){
            $conexion->where("general.planes_academicos.codigo", $planAcademico);
        }
        if ($fechaInicioDesde != null){
            $conexion->where("planes_pago.fechainicio >=", $fechaInicioDesde);
        }
        if ($fechaInicioHasta != null){
            $conexion->where("planes_pago.fechainicio <=", $fechaInicioHasta);
        }
        if ($fechaVigenciaDesde != null){
            $conexion->where("planes_pago.fechavigencia >=", $fechaVigenciaDesde);
        }
        if ($fechaVigenciaHasta != null){
            $conexion->where("planes_pago.fechavigencia <=", $fechaVigenciaHasta);
        }
        $conexion->group_by('planes_pago.codigo');
        $arrResp = Vplanes_pago::listarPlanes_pago($conexion, null, $arrLimit, $arrSort, null, $contar);
//        echo $conexion->last_query(); die();
        return $arrResp;
    }

    public function getCuotasPlan($estado = null, $orden = null) {
        $condiciones = array(
            "codigo_plan" => $this->codigo
        );
        $this->oConnection->select("descuento");
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto");
        $sqDescuento = $this->oConnection->return_query();
        $this->oConnection->resetear();        
        $this->oConnection->select("planes_financiaciones_descuentos.interes");
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto");
        $sqInteres = $this->oConnection->return_query();
        $this->oConnection->resetear();        
        $this->oConnection->select("planes_financiaciones_descuentos.limite_primer_cuota");
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto");
        $sqLimit = $this->oConnection->return_query();
        $this->oConnection->resetear();        
        $this->oConnection->select("planes_financiaciones_descuentos.fecha_limite");
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto");
        $sqFechaLimit = $this->oConnection->return_query();

        $this->oConnection->resetear();
        $this->oConnection->select("planes_financiaciones_descuentos.limite_vigencia");
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto");
        $sqLimitFinanciacion = $this->oConnection->return_query();
        $this->oConnection->resetear();
        $this->oConnection->select("planes_financiaciones_descuentos.fecha_vigencia");
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto");
        $sqFechaLimitFinanciacion = $this->oConnection->return_query();

        $this->oConnection->resetear();
        $this->oConnection->select('financiacion.*');
        $this->oConnection->select("IFNULL(($sqDescuento), 0) AS descuento", false);
        $this->oConnection->select("IFNULL(($sqInteres), 0) AS interes", false);
        $this->oConnection->select("($sqLimit) AS limite_primer_cuota" , false);
        $this->oConnection->select("($sqFechaLimit) AS fecha_limite", false);

        $this->oConnection->select("($sqLimitFinanciacion) AS limite_financiacion" , false);
        $this->oConnection->select("($sqFechaLimitFinanciacion) AS fecha_limite_financiacion", false);

        $this->oConnection->join('financiacion', 'financiacion.codigo = planes_financiacion.codigo_financiacion');
        $this->oConnection->join('planes_financiaciones_descuentos','planes_financiaciones_descuentos.cod_plan = planes_financiacion.codigo_plan AND planes_financiaciones_descuentos.cod_concepto = planes_financiacion.codigo_concepto AND planes_financiaciones_descuentos.cod_financiacion = planes_financiacion.codigo_financiacion');
        if ($estado !== null) {
            $this->oConnection->where('financiacion.estado', $estado);
        }
        $cuotas = Vplanes_financiacion::listarPlanes_financiacion($this->oConnection, $condiciones, null, $orden);
        return $cuotas;
    }

    /* PUBLIC FUNCTIONS */

    public function getDetallePlan(CI_DB_mysqli_driver $conexion) {
        $conexion->select("COUNT(nro_cuota)");
        $conexion->from("planes_financiacion AS pc1");
        $conexion->where("pc1.codigo_plan = planes_financiacion.codigo_plan");
        $conexion->where('pc1.codigo_financiacion = planes_financiacion.codigo_financiacion');
        $conexion->where("pc1.codigo_concepto = planes_financiacion.codigo_concepto");
        $queryCantCuotas = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("SUM(valor)");
        $conexion->from("planes_financiacion AS pc1");
        $conexion->where("pc1.codigo_plan = planes_financiacion.codigo_plan");
        $conexion->where('pc1.codigo_financiacion = planes_financiacion.codigo_financiacion');
        $conexion->where("pc1.codigo_concepto = planes_financiacion.codigo_concepto");
        $queryValorCuotas = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("DISTINCT codigo_concepto", false);
        $conexion->select("($queryCantCuotas) AS cantidad_cuotas");
        $conexion->select("($queryValorCuotas) AS valor_cuotas");
        $conexion->from("planes_financiacion");
        $conexion->where(array("codigo_plan = " => $this->codigo));
        $query = $conexion->get();
        return $query->result_array();
    }
    
    public function getVigenciasPresupuesto(){
        $this->oConnection->select('*');
        $this->oConnection->from('planes_pago');
        $this->oConnection->join('planes_cursos_periodos','planes_cursos_periodos.cod_plan_pago = planes_pago.codigo');
        $this->oConnection->join('presupuestos','presupuestos.cod_plan = planes_cursos_periodos.cod_plan_pago');
        $this->oConnection->where('planes_pago.codigo',  $this->codigo);
        $this->oConnection->where('presupuestos.fecha_vigencia >= curdate()');
        $query = $this->oConnection->get();      
        return $query->result_array();
    }
    
    public function setPreciosConceptos($codConcepto, $precio){
        $this->oConnection->where("cod_plan", $this->codigo);
        $this->oConnection->where("cod_concepto", $codConcepto);
        $resp = $this->oConnection->delete("planes_conceptos_precios");
        $arrInsert = array(
            "cod_plan" => $this->codigo,
            "cod_concepto" => $codConcepto,
            "precio_lista" => $precio
        );
        return $resp && $this->oConnection->insert("planes_conceptos_precios", $arrInsert);
    }
    
    public function getPlanFinanciacionDescuento($codFinanciacion = null, $codConcepto = null){
        $this->oConnection->select("planes_financiaciones_descuentos.*", false);
        if ($codFinanciacion != null && $codConcepto != null){
            $this->oConnection->select("planes_conceptos_precios.precio_lista");
            $this->oConnection->join("planes_conceptos_precios", "planes_conceptos_precios.cod_concepto = planes_financiaciones_descuentos.cod_concepto AND planes_conceptos_precios.cod_plan = planes_financiaciones_descuentos.cod_plan");
        }
        $this->oConnection->from("planes_financiaciones_descuentos");
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan", $this->codigo);
        if ($codFinanciacion != null){
            $this->oConnection->where("planes_financiaciones_descuentos.cod_financiacion", $codFinanciacion);
        }
        if ($codConcepto != null){
            $this->oConnection->where("planes_financiaciones_descuentos.cod_concepto", $codConcepto);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function deleteFinanciaciones(){
         $this->oConnection->where("planes_financiacion.codigo_plan", $this->codigo);
        return $this->oConnection->delete("planes_financiacion");
    }
    
    public function deleteFinanciacionesDescuentos(){
        $this->oConnection->where("planes_financiaciones_descuentos.cod_plan", $this->codigo);
        return $this->oConnection->delete("planes_financiaciones_descuentos");
    }
    
    public function setFinanciacion($codFinanciacion, $codConcepto, $detalleCuotas, $descuento, $interes, $tipoLimite, $fechaLimite = null, $tipoLimiteFinanciacion, $fechaLimiteFinanciacion = null){
        foreach ($detalleCuotas as $cuota){
            $arrInsert = array(
                "codigo_plan" => $this->codigo,
                "codigo_financiacion" => $codFinanciacion,
                "codigo_concepto" => $codConcepto,
                "valor" => $cuota['valor'],
                "valor_neto" => $cuota['valor_neto'],
                "nro_cuota" => $cuota['nrocuota'],
                "orden" => $codConcepto == 5 ? 1 : 2
            );
            $resp = $this->oConnection->insert("planes_financiacion", $arrInsert);
        }
        $arrInsert = array(
                "cod_plan" => $this->codigo,
                "cod_financiacion" => $codFinanciacion,
                "cod_concepto" => $codConcepto,
                "interes" => $interes,
                "descuento" => $descuento,
                "limite_primer_cuota" => $tipoLimite,
                "fecha_limite" => $fechaLimite == '' ? null : $fechaLimite,
                "limite_vigencia" => $tipoLimiteFinanciacion,
                "fecha_vigencia" => $fechaLimiteFinanciacion
            );
        $resp = $resp && $this->oConnection->insert("planes_financiaciones_descuentos", $arrInsert);
        return $resp;
    }
    
    public function setCursoPeriodo($codCurso, $codTipoPeriodo, $modalidad){
        $this->oConnection->where("cod_curso", $codCurso);
        $this->oConnection->where("cod_tipo_periodo", $codTipoPeriodo);
        $this->oConnection->where("cod_plan_pago", $this->codigo);
        $resp = $this->oConnection->delete("planes_cursos_periodos");
        $arrInsert = array(
            "cod_plan_pago" => $this->codigo,
            "cod_tipo_periodo" => $codTipoPeriodo,
            "cod_curso" => $codCurso,
            "modalidad" => $modalidad
        );
        return $resp && $this->oConnection->insert("planes_cursos_periodos", $arrInsert);
    }
    
    public function setPlanCurso($setearPlanCurso){
        $this->oConnection->insert('planes_cursos_periodos',$setearPlanCurso);
    }
    
    public function getPeriodosCurso($cod_curso){
        $this->oConnection->select('planes_cursos_periodos.cod_tipo_periodo');
        $this->oConnection->from('planes_cursos_periodos');
        $this->oConnection->where('planes_cursos_periodos.cod_plan_pago',  $this->codigo);
        $this->oConnection->where('planes_cursos_periodos.cod_curso',$cod_curso);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCursosPeriodosPlan(){
        $this->oConnection->select('*');
        $this->oConnection->from('planes_cursos_periodos');
        $this->oConnection->where('planes_cursos_periodos.cod_plan_pago',  $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getConceptosPrecios(){
        $this->oConnection->select("planes_conceptos_precios.*", false);
        $this->oConnection->from("planes_conceptos_precios");
        $this->oConnection->where("planes_conceptos_precios.cod_plan", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    /**
     * Clona un objeto con todas sus propiedades dejando su identificador como objeto nuevo (codigo = -1)
     * 
     * @return \Vplanes_pago
     */
    public function clonar(){
        $myClon = new Vplanes_pago($this->oConnection);
        $arrFields = $this->_getArrayDeObjeto();
        foreach ($arrFields as $field => $value){
            $myClon->$field = $value;
        }
        return $myClon;
    }
    
    static function getNombrePeriodos(CI_DB_mysqli_driver $conexion,$cod_plan_pago,$cod_filial){
        $conexion->select('general.tipos_periodos.nombre');
        $conexion->from('general.tipos_periodos');
        $conexion->where('general.tipos_periodos.codigo = planes_cursos_periodos.cod_tipo_periodo');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("IFNULL(general.planes_academicos_filiales.nombre_periodo,($subquery2)) as nombre_periodo, planes_academicos_filiales.modalidad",false);
        $conexion->from('planes_cursos_periodos');
        $conexion->join("general.planes_academicos_filiales","general.planes_academicos_filiales.cod_plan_academico = planes_cursos_periodos.cod_curso and general.planes_academicos_filiales.cod_tipo_periodo = planes_cursos_periodos.cod_tipo_periodo and general.planes_academicos_filiales.modalidad = planes_cursos_periodos.modalidad and general.planes_academicos_filiales.cod_filial = $cod_filial");
        $conexion->where('planes_cursos_periodos.cod_plan_pago',$cod_plan_pago);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    public function getEsquemaCuotas($numeroCuotas){
        $this->oConnection->select("planes_financiacion.valor");
        $this->oConnection->select("planes_financiacion.nro_cuota", false);
        $this->oConnection->select("planes_financiacion.valor_neto");
        $this->oConnection->from("planes_financiacion");
        $this->oConnection->join("financiacion", "financiacion.codigo = planes_financiacion.codigo_financiacion AND financiacion.numero_cuotas = $numeroCuotas");
        $this->oConnection->where("planes_financiacion.codigo_plan", $this->codigo);
        $this->oConnection->order_by("planes_financiacion.nro_cuota", "ASC");
        $query = $this->oConnection->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    static function validarNombrePlanPago(CI_DB_mysqli_driver $conexion, $nombre){
        $conexion->select('planes_pago.nombre');
        $conexion->from('planes_pago');
        $conexion->where('planes_pago.nombre',  $nombre);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }
    
    static function getCantidadMatriculasPlanPago(CI_DB_mysqli_driver $conexion, $codigo) {
        $conexion->select('count(*) as count');
        $conexion->from('planes_pago p');
        $conexion->join('matriculas m', 'm.cod_plan_pago = p.codigo');
        $conexion->where('p.codigo', $codigo);
        $conexion->group_by('p.codigo');
        $query = $conexion->get();
        $resp = $query->row();
        if(empty($resp)) {
            $resp = 0;
        }
        else {
            $resp = $resp->count;
        }
        return $resp;
    }
}