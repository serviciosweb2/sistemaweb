<?php
/**
 * Class Vcomision
 *
 * Class  Vcomision maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcomisiones extends Tcomisiones {

    static private $estadoHabilitada = 'habilitado';
    static private $estadoInhabilitada = 'inhabilitado';
    static private $estadoDesuso = 'desuso';
    static private $estadoAPasar = 'a_pasar';
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /**
     * Retorna los horarios de una comision.
     * @access public
     * @return Array horarios.
     */
    public function getHorarios($materia = null, $disponible = 1) {
        $this->oConnection->select('(WEEKDAY(horarios.dia)) AS DIA_SEMANA , horarios.horadesde,horahasta ');
        $this->oConnection->from("horarios");
        $this->oConnection->where('cod_comision', $this->getCodigo());
        if ($materia != null) {
            $this->oConnection->where('cod_materia', $materia);
        }
        if ($disponible == 1) {
            $this->oConnection->where('baja', 0);
        }
        $this->oConnection->group_by(array("DIA_SEMANA", "horadesde", "horahasta"));
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /**
     * Retorna todos los horarios disponibles.
     * @access public
     * @return Array horarios disponibles.
     */
    public function getHorariosDisponibles($materia = null) {

        $arrcondiciones = array(
            "cod_comision" => $this->codigo,
            "baja" => 0
        );

        if ($materia != null) {
            $arrcondiciones["cod_materia"] = $materia;
        }
        return Vhorarios::listarHorarios($this->oConnection, $arrcondiciones);
    }

    /**
     * lista todos las comisiones con distintos filtros para datatable plugin
     * @access public
     * @return Array comisiones.
     */
    static function getAllComisionesDatatable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $cod_filial = '', $arrFiltros_nuevos = null) {
        //el $arrFiltros_nuevos trae condiciones de filtros que no tienen correspondencia con algun campo de la base de datos
        $conexion->select('salones.cupo');
        $conexion->from('salones');
        $conexion->join('horarios','horarios.cod_salon = salones.codigo');
        $conexion->where('horarios.cod_comision = comisiones.codigo');
        $conexion->where('horarios.baja', 0);
        $conexion->order_by("CAST(tipo AS CHAR) DESC, cupo DESC");
        $conexion->limit(1);
        $subquery = $conexion->return_query();
        $conexion->resetear();                    
        
        $conexion->select('general.tipos_periodos.nombre');
        $conexion->from('general.tipos_periodos');
        $conexion->where('general.tipos_periodos.codigo = comisiones.cod_tipo_periodo');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        
        // modificacion ticket 5149 se agrega la subquery_count para hacer el where de inscriptos y para agregar proligidad->
        /*
        $conexion->select('count( DISTINCT matriculas_periodos.cod_matricula )');
        $conexion->from('matriculas_periodos');
        $conexion->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $conexion->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
        $conexion->where('matriculas_inscripciones.cod_comision = comisiones.codigo');
        $conexion->where('matriculas_inscripciones.baja', 0);
        $conexion->group_by('matriculas_inscripciones.cod_comision');
        */

        $conexion->select('count(DISTINCT matriculas_periodos.cod_matricula)');
        $conexion->from('matriculas_inscripciones');
        $conexion->join('estadoacademico','estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico');
        $conexion->join('matriculas_periodos',"matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND matriculas_periodos.estado <> 'inhabilitada' AND matriculas_periodos.estado <> 'finalizada'");
        $conexion->join('matriculas',"matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado <> 'inhabilitada' AND matriculas.estado <> 'finalizada'");
        $conexion->join('alumnos','alumnos.codigo = matriculas.cod_alumno');
        $conexion->where('cod_comision = comisiones.codigo');
        $conexion->where("matriculas_inscripciones.baja = '0'");
        $conexion->where("(SELECT matriculas_inscripciones.cod_comision FROM matriculas_inscripciones WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo AND matriculas_inscripciones.baja = '0' ORDER BY `matriculas_inscripciones`.`codigo` DESC LIMIT 1) = comisiones.codigo");
        $conexion->where("estadoacademico.estado IN ('cursando')");
        $conexion->or_where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo  AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = comisiones.codigo");
        //$conexion->where("NOT (SELECT matriculas_periodos.estado FROM matriculas_periodos WHERE matriculas_periodos.cod_tipo_periodo > 1 AND matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.estado NOT IN ('migrado', 'inhabilitada'))", NULL, FALSE);
        $conexion->where('matriculas_inscripciones.cod_comision = comisiones.codigo');
        $conexion->where("comisiones.estado NOT IN ('desuso', 'inhabilitado')");
        $conexion->group_by('matriculas_inscripciones.cod_comision');

        $subquery_count = $conexion->return_query();
        $conexion->resetear();
        // <-modificacion ticket 5149 
        $conexion->select("comisiones.*,"
                . "general.cursos.nombre_es, general.cursos.nombre_in, general.cursos.nombre_pt, general.cursos.codigo as cod_curso, general.planes_academicos.nombre as nombre_plan", false);
        $conexion->select("IFNULL(($subquery),'sin_salon') as cupo_disponible", false);
        $conexion->select("IFNULL(general.planes_academicos_filiales.nombre_periodo, ($subquery2)) as nombre_periodo", false);
        $conexion->from('comisiones');
        $subquery_cupo = $conexion->return_query();
        $conexion->resetear();        
        
        $conexion->select("comisiones.*,"
                . "general.cursos.nombre_es, general.cursos.nombre_in, general.cursos.nombre_pt, general.cursos.codigo as cod_curso, general.planes_academicos.nombre as nombre_plan", false);
        $conexion->select("IFNULL(($subquery),'sin_salon') as cupo_disponible", false);
        $conexion->select("IFNULL(general.planes_academicos_filiales.nombre_periodo, ($subquery2)) as nombre_periodo", false);
        if (!$contar) {
            /*$conexion->select("(SELECT count( DISTINCT matriculas_periodos.cod_matricula ) FROM matriculas_periodos "
                    . "JOIN estadoacademico ON estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo "
                    . "JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo "
                    . "WHERE matriculas_inscripciones.cod_comision = comisiones.codigo and matriculas_inscripciones.baja = 0 "
                    . "GROUP BY matriculas_inscripciones.cod_comision ) as inscriptos ");*/
            $conexion->select("IFNULL(($subquery_count), '0') as inscriptos ", false);
            //$conexion->select("($subquery_count) as inscriptos ");
        }
        $conexion->from('comisiones');   
        $conexion->join('horarios', 'horarios.cod_comision = comisiones.codigo', 'left');
        $conexion->join('general.planes_academicos', 'comisiones.cod_plan_academico = general.planes_academicos.codigo');
        $conexion->join('general.cursos', 'general.cursos.codigo = general.planes_academicos.cod_curso');
        $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_plan_academico = comisiones.cod_plan_academico and general.planes_academicos_filiales.cod_tipo_periodo = comisiones.cod_tipo_periodo and general.planes_academicos_filiales.modalidad = comisiones.modalidad and general.planes_academicos_filiales.cod_filial = ' . $cod_filial . '', false);
        // modificacion ticket 5149 ->
        if(isset($arrFiltros_nuevos["condic_cod"]) && isset($arrCondicioneslike['comisiones.codigo']) && $arrFiltros_nuevos["condic_cod"] != "") {
            $conexion->where("comisiones.codigo", $arrCondicioneslike['comisiones.codigo']);
        }
        if(isset($arrFiltros_nuevos["condic_est"]) && isset($arrCondicioneslike['comisiones.estado']) && $arrFiltros_nuevos["condic_est"] != "") {
            $conexion->where("comisiones.estado ", $arrCondicioneslike['comisiones.estado']);
        }
        if (!$contar) {
            if(isset($arrFiltros_nuevos["condic_cant_ins"]) && isset($arrCondicioneslike['inscriptos']) && $arrFiltros_nuevos["condic_cant_ins"] != "") {
                               
                switch( $arrFiltros_nuevos["condic_cant_ins"] ){
                   case "es_igual_a":
                       $conexion->where("($subquery_count) =", $arrCondicioneslike['inscriptos']);
                   break;
                   case "mayor_o_igual_a":
                       $conexion->where("($subquery_count) >=", $arrCondicioneslike['inscriptos']);
                   break;
                   case "mayor":
                       $conexion->where("($subquery_count) >", $arrCondicioneslike['inscriptos']);
                   break;
                   case "menor_o_igual_a":
                       $conexion->where("($subquery_count) <=", $arrCondicioneslike['inscriptos']);
                   break;
                   case "menor":
                       $conexion->where("($subquery_count) <", $arrCondicioneslike['inscriptos']);
                   break;
                }
             //   $conexion->where("inscriptos <>", 'sin_salon');
            }
        }
        if(isset($arrFiltros_nuevos["condic_capac"]) && isset($arrCondicioneslike['cupo_disponible']) && $arrFiltros_nuevos["condic_capac"] != "") {
            switch($arrFiltros_nuevos["condic_capac"]){
               case "es_igual_a":
                   $conexion->where("($subquery) =", $arrCondicioneslike['cupo_disponible']);
               break;
               case "mayor_o_igual_a":
                   $conexion->where("($subquery) >=", $arrCondicioneslike['cupo_disponible']);
               break;
               case "mayor":
                   $conexion->where("($subquery) >", $arrCondicioneslike['cupo_disponible']);
               break;
               case "menor_o_igual_a":
                   $conexion->where("($subquery) <=", $arrCondicioneslike['cupo_disponible']);
               break;
               case "menor":
                   $conexion->where("($subquery) <", $arrCondicioneslike['cupo_disponible']);
               break;
            }
        }
        // <- modificacion ticket 5149 
        $conexion->group_by('comisiones.codigo');
        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                if($key != 'cupo_disponible' && $key != 'inscriptos'  && $key != 'comisiones.estado'){
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (isset($arrTemp) && count($arrTemp) > 0) {
            // modificacion ticket 5149 -> se modifica el OR por el and para que los filtros valgan todos a la vez
                $having = "(" . implode(" OR ", $arrTemp) . ")";
            // <- modificacion ticket 5149
                $conexion->having($having);
            }
        }
        if ($arrLimit !== null) {
            $conexion->limit($arrLimit["1"], $arrLimit["0"]);
        }
        if ($arrSort !== null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
//        echo $conexion->last_query() . "\n\n\n"; die();
        $listarComisiones = $query->result_array();
        if ($contar) {
            return count($listarComisiones);
        } else {
            return $listarComisiones;
        }
    }

    public function getInscriptosComision() {
        $this->oConnection->select('COUNT(DISTINCT matriculas_periodos.cod_matricula) AS inscriptos');
        $this->oConnection->from('matriculas_periodos');
        $this->oConnection->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $this->oConnection->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
        $this->oConnection->where('matriculas_inscripciones.cod_comision', $this->codigo);
        $this->oConnection->where('matriculas_inscripciones.baja', 0);
        $this->oConnection->group_by('matriculas_inscripciones.cod_comision');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    private function setEstado($estado){
        $this->estado = $estado;
        return $this->guardarComisiones();
    }
    
    /**
     * Setea el estado de la comision como a_pasar (comision debe pasar de periodo)
     * 
     * @return boolean
     */
    public function setAPasar(){
        return $this->setEstado(self::$estadoAPasar);
    }
    
    /**
     * Setea el estado de la comision como desuso (La comision no puede utilizarse - Ej. se ha vencido el ciclo)
     * 
     * @return boolean
     */
    public function setDesuso(){
        $this->oConnection->where("cod_comision", $this->codigo);
        $resp = $this->oConnection->update("matriculas_inscripciones", array("baja" => 1));
        return $resp && $this->setEstado(self::$estadoDesuso);
    }
    
    /** 
     * Retorna la baja de la comision.
     * @access public
     * 
     */

    public function baja($fechaDesde = null){
        $resp = true;
        if ($fechaDesde != null){
            $condiciones = array(
                "cod_comision" => $this->codigo,
                "dia >=" => $fechaDesde
            );
            $arrHorarios = Vhorarios::listarHorarios($this->oConnection, $condiciones);
            foreach ($arrHorarios as $horario){
                $myHorario = new Vhorarios($this->oConnection, $horario['codigo']);
                $resp = $resp && $myHorario->baja();
            }
        }
        return $resp && $this->setEstado(self::$estadoInhabilitada);
    }

    /**
     * Retorna el alta de la comision.
     * @access public
     * 
     */
    public function activar(){
        return $this->setEstado(self::$estadoHabilitada);
    }

    /**
     * Retorna todos los planes asignados de la comision.
     * @access public
     * @return Array planes de pago asignados.
     */
    public function getPlanesAsignados() {
        $this->oConnection->select("planes_pago.*");
        $this->oConnection->select("planes_cursos_periodos.*");
        $this->oConnection->select("planes_comisiones.mostrar_web");
        $this->oConnection->select("planes_comisiones.mostrar_financiacion_web");
        $this->oConnection->select("comisiones.dias_prorroga");
        $this->oConnection->from("planes_pago");
        $this->oConnection->join("planes_comisiones", "planes_comisiones.cod_plan = planes_pago.codigo");
        $this->oConnection->join("comisiones", "comisiones.codigo = planes_comisiones.cod_comision");
        $this->oConnection->join('planes_cursos_periodos', 'planes_cursos_periodos.cod_plan_pago = planes_pago.codigo');
        $this->oConnection->where("cod_comision", $this->codigo);
        $this->oConnection->where("planes_comisiones.baja", 0);
        $this->oConnection->group_by('planes_pago.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /**
     * Retorna todos los planes no asignados de la comision.
     * @access public
     * @return Array planes de pago no asignados.
     */
    public function getPlanesNoAsignados() {
        $this->oConnection->select("planes_pago.*, planes_cursos_periodos.*");
        $this->oConnection->from("planes_pago");
        $this->oConnection->join('planes_cursos_periodos', 'planes_cursos_periodos.cod_plan_pago = planes_pago.codigo');
        $this->oConnection->where('(planes_pago.fechavigencia >= curdate() or planes_pago.fechavigencia IS NULL)');
        $this->oConnection->where('planes_cursos_periodos.cod_curso = ' . $this->cod_plan_academico . '');
        $this->oConnection->where('planes_cursos_periodos.cod_tipo_periodo', $this->cod_tipo_periodo);
        $this->oConnection->where('planes_cursos_periodos.modalidad', $this->modalidad);
        $this->oConnection->group_by('planes_pago.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /**
     * Setea plan a comision.
     * @access public
     * @return Array de plan.
     */
    public function setPlan($plan) {
        $conexion = $this->oConnection;
        $data = array(
            'cod_plan' => $plan,
            'cod_comision' => $this->codigo,
            'baja' => '0',
            'mostrar_web' => '0'
        );
        $conexion->insert("planes_comisiones", $data);
    }

    /**
     * Desetea pla a comision.
     * @access public
     * @return Array de desetear plan.
     */
    public function unSetPlan($plan) {
        $conexion = $this->oConnection;
        $data = array(
            'cod_comision' => $this->codigo,
            'cod_plan' => $plan
        );
        $conexion->delete("planes_comisiones", $data);
    }

    public function setMostrarWeb($cod_plan_pago) {
        $array = array(
            "mostrar_web" => 1
        );
        $this->oConnection->where('planes_comisiones.cod_comision', $this->codigo);
        $this->oConnection->where('planes_comisiones.cod_plan', $cod_plan_pago);
        return $this->oConnection->update('planes_comisiones', $array);
    }    
    
    public function unSetMostrarWeb($cod_plan_pago = null) {
        $mostrar = array(
            "mostrar_web" => 0
        );
        $this->oConnection->where('planes_comisiones.cod_comision', $this->codigo);
        if ($cod_plan_pago != null) {
            $this->oConnection->where('planes_comisiones.cod_plan', $cod_plan_pago);
        }
        return $this->oConnection->update('planes_comisiones', $mostrar);
    }

    public function setMostrarFinanciacionWeb($cod_plan_pago){
        $this->oConnection->where("planes_comisiones.cod_comision", $this->codigo);
        $this->oConnection->where("planes_comisiones.cod_plan", $cod_plan_pago);
        return $this->oConnection->update("planes_comisiones", array("mostrar_financiacion_web" => 1));
    }
    
    public function unSetMostrarFinanciacionWeb($cod_plan_pago){
        $this->oConnection->where("planes_comisiones.cod_comision", $this->codigo);
        $this->oConnection->where("planes_comisiones.cod_plan", $cod_plan_pago);
        return $this->oConnection->update("planes_comisiones", array("mostrar_financiacion_web" => 0));
    }    
    
    public function getMateriasHorariosComision() {
        $this->oConnection->select('general.materias.codigo');
        $this->oConnection->select('general.materias.nombre_es');
        $this->oConnection->select('general.materias.nombre_in');
        $this->oConnection->select('general.materias.nombre_pt');
        $this->oConnection->from('horarios');
        $this->oConnection->join('general.materias', 'general.materias.codigo = horarios.cod_materia');
        $this->oConnection->where('horarios.cod_comision', $this->codigo);
        $this->oConnection->where('horarios.baja', 0);
        $this->oConnection->group_by('horarios.cod_materia');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getDiasCursadoComision($cod_materia, $asistencia = false) {
        $this->oConnection->select('DISTINCT horarios.dia, asistencia', false);
        $this->oConnection->from('horarios');
        $this->oConnection->where('horarios.cod_comision', $this->codigo);
        $this->oConnection->where('horarios.cod_materia', $cod_materia);
        $this->oConnection->where('horarios.baja', 0);
        $query = $this->oConnection->get();

        return $query->result_array();
    }

    public function getFechaInicio($cod_materia = null) {
        $this->oConnection->select('horarios.dia');
        $this->oConnection->from('horarios');
        $this->oConnection->join('comisiones', 'comisiones.codigo = horarios.cod_comision');
        $this->oConnection->where('horarios.cod_comision', $this->codigo);
        if ($cod_materia != null) {
            $this->oConnection->where('horarios.cod_materia', $cod_materia);
        }
        $this->oConnection->where('horarios.baja', 0);
        $this->oConnection->order_by('horarios.dia', 'asc');
        $query = $this->oConnection->get();
        $respuesta = $query->result_array();
        if (count($respuesta) > 0) {
            return $respuesta[0]['dia'];
        } else {
            return null;
        }
    }

    public function getInscriptos($cod_materia = null) {
        $this->oConnection->select("COUNT(matriculas_inscripciones.codigo) as inscriptos");
        $this->oConnection->from('matriculas_inscripciones');
        $this->oConnection->join('comisiones', 'matriculas_inscripciones.cod_comision = comisiones.codigo');
        $this->oConnection->where('matriculas_inscripciones.cod_comision', $this->codigo);
        $this->oConnection->where('matriculas_inscripciones.baja', 0);
        if ($cod_materia != null) {
            $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico');
            $this->oConnection->where('estadoacademico.codmateria', $cod_materia);
        } else {
            $this->oConnection->group_by('matriculas_inscripciones.cod_estado_academico');
        }
        $query = $this->oConnection->get();
        $respuesta = $query->result_array();
        return $respuesta;
    }

    public function getCapacidad() {
        $this->oConnection->select('IFNULL(salones.cupo,-1) as cupo', false);
        $this->oConnection->from('horarios');
        $this->oConnection->join('salones', 'salones.codigo = horarios.cod_salon');
        $this->oConnection->where('horarios.cod_comision', $this->codigo);
        $this->oConnection->where('horarios.baja', 0);
        $this->oConnection->order_by("CAST(tipo AS CHAR) DESC, cupo DESC");
        $this->oConnection->limit(1);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getComisionesconHorarios(CI_DB_mysqli_driver $conexion) {
        $conexion->select('comisiones.*');
        $conexion->from('comisiones');
        $conexion->join('horarios', 'comisiones.codigo = horarios.cod_comision');
        $conexion->where('horarios.baja', 0);
        $conexion->order_by('comisiones.codigo', 'desc');
        $conexion->group_by('comisiones.codigo');
        $query = $conexion->get();
        return$query->result_array();
    }

    public function getMateriasComision() {
        $this->oConnection->select('general.materias.codigo');
        $this->oConnection->select('general.materias.nombre_es');
        $this->oConnection->select('general.materias.nombre_in');
        $this->oConnection->select('general.materias.nombre_pt');
        $this->oConnection->from('comisiones');
        $this->oConnection->join('general.materias_plan_academico', 'general.materias_plan_academico.cod_plan = comisiones.cod_plan_academico');
        $this->oConnection->join('general.materias', 'general.materias.codigo = general.materias_plan_academico.cod_materia');
        $this->oConnection->where('comisiones.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getAlumnosComision() {
        $this->oConnection->select("IFNULL(COUNT(matriculas_inscripciones.codigo), 0) AS cantInscriptos", false);
        $this->oConnection->from('matriculas_inscripciones');
        $this->oConnection->where('matriculas_inscripciones.cod_comision', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getCurso() {
        $this->oConnection->select('general.planes_academicos.cod_curso as codigo');
        $this->oConnection->from('general.planes_academicos');
        $this->oConnection->where('general.planes_academicos.codigo', $this->cod_plan_academico);
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        return $resultado[0]['codigo'];
    }

    public function tienePadre() {
        $this->oConnection->select("*");
        $this->oConnection->from('general.planes_academicos_periodos');
        $this->oConnection->where('general.planes_academicos_periodos.cod_plan_academico', $this->cod_plan_academico);
        $this->oConnection->where('general.planes_academicos_periodos.cod_tipo_periodo', $this->cod_tipo_periodo);
        $query = $this->oConnection->get();
        $planAcademicoPeriodo = $query->result_array();
        if (is_array($planAcademicoPeriodo) && isset($planAcademicoPeriodo[0]['padre'])) {
            $retorno = $planAcademicoPeriodo[0]['padre'] != '' ? true : false;
            return $retorno;
        } else {
            return false;
        }
    }

    /**
     * lista todos las comisiones
     * $conexion CI_DB_mysqli_driver    Objeto de conexion a la base de datos
     * $arrCondiciones array    Un array con condiciones de filtro
     * $codcurso    integer El codigo del curso al que pertenece la comision
     * $cicloVencido    boolean true para recuperar comisiones con elciclo vencido, false para comisiones con ciclo actual, null para descartar el filtro
     * $cantidadCursantes   boolean true para recuperar la cantidad de alumnos que cursan en la comision 
     * $conCursantes    boolean true para recuperar comisiones con al menos un cursante, false para comisiones sin alumnos cursando, null para descartar el filtro
     * 
     * @access public
     * @return Array comisiones.
     */
    static function getComisiones(CI_DB_mysqli_driver $conexion, $arrCondiciones = null, $codcurso = null, $cicloVencido = null, 
            $cantidadCursantes = false, $conCursantes = null, $codPlanAcademico = null, $codTipoPeriodo = null,$wiEstado =null ) {
        if ($cantidadCursantes || $conCursantes !== null){
            $conexion->select("COUNT(DISTINCT matriculas_periodos.codigo)");
            $conexion->from("matriculas_periodos");
            $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
            $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo AND matriculas_inscripciones.baja = 0");
            $conexion->where("matriculas_periodos.estado", Vmatriculas_periodos::getEstadoHabilitada());
            $conexion->where("matriculas_inscripciones.cod_comision = comisiones.codigo");
            $sqCursantes = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("($sqCursantes) AS cantidad_cursantes");
        }        
        $conexion->select('comisiones.*');
        $conexion->select('general.planes_academicos.nombre AS nombre_plan_academico');
        $conexion->from('comisiones');
        if ($cicloVencido !== null){
            $conexion->join("general.ciclos", "general.ciclos.codigo = comisiones.ciclo");
            if ($cicloVencido){
                $conexion->where("general.ciclos.fecha_fin_ciclo < CURDATE()");
            } else {
                $conexion->where("(general.ciclos.fecha_fin_ciclo >= CURDATE() AND general.ciclos.fecha_inicio_ciclo <= CURDATE())");
            }
        }        
        $conexion->join('general.planes_academicos', 'comisiones.cod_plan_academico = general.planes_academicos.codigo');
        $conexion->order_by('comisiones.nombre');
        if ($arrCondiciones != null) {
            $conexion->where($arrCondiciones);
        }
        if ($codPlanAcademico != null){
            $conexion->where("comisiones.cod_plan_academico", $codPlanAcademico);
        }        
        if ($codTipoPeriodo != null){
            $tipoOperador = is_array($codTipoPeriodo) ? "where_in" : "where";
            $conexion->$tipoOperador("comisiones.cod_tipo_periodo", $codTipoPeriodo);
        }        
        if ($codcurso != null) {
            $conexion->where('general.planes_academicos.cod_curso', $codcurso);
        }
        if ($conCursantes !== null){
            if ($conCursantes){
                $conexion->having("cantidad_cursantes >", 0);
            } else {
                $conexion->having("cantidad_cursantes", 0);
            }
        }
        if($wiEstado !== null){            
               $conexion->where_in('comisiones.estado', $wiEstado);            
        }
        $query = $conexion->get();
        $listarComisiones = $query->result_array();
        return $listarComisiones;
    }

    //Esta query esta haciendo utilizada para un web service no modificar.
    static function getReportesComisionesActivas(CI_DB_mysqli_driver $conexion) {
        $conexion->select('general.planes_academicos.cod_curso');
        $conexion->from('general.planes_academicos');
        $conexion->where('general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $subquery1 = $conexion->return_query();        
        
        $conexion->resetear();
        $conexion->select('count(DISTINCT matriculas_periodos.cod_matricula)');
        $conexion->from('matriculas_periodos');
        $conexion->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $conexion->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
        $conexion->where('matriculas_inscripciones.cod_comision = comisiones.codigo');
        $conexion->where('matriculas_inscripciones.baja', 0);
        $conexion->group_by('matriculas_inscripciones.cod_comision');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('MIN(horarios.dia)');
        $conexion->from('horarios');
        $conexion->where('horarios.cod_comision = comisiones.codigo');
        $conexion->where('horarios.baja', 0);
        $conexion->order_by('horarios.dia', 'DESC');
        $subquery3 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('max(planes_financiacion.nro_cuota)');
        $conexion->from('comisiones as c1');
        $conexion->join('planes_comisiones', 'planes_comisiones.cod_comision = c1.codigo AND planes_comisiones.mostrar_web = 1');
        $conexion->join('planes_financiacion', 'planes_financiacion.codigo_plan = planes_comisiones.cod_plan AND planes_financiacion.codigo_concepto = 1');
        $conexion->join('financiacion', "financiacion.codigo = planes_financiacion.codigo_financiacion AND financiacion.estado ='habilitada'");
        $conexion->where('c1.codigo = comisiones.codigo');
        $subquery4 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('planes_financiacion.valor');
        $conexion->from('comisiones as c1');
        $conexion->join('planes_comisiones', 'planes_comisiones.cod_comision = c1.codigo AND planes_comisiones.mostrar_web = 1');
        $conexion->join('planes_financiacion', 'planes_financiacion.codigo_plan = planes_comisiones.cod_plan AND planes_financiacion.codigo_concepto = 1');
        $conexion->join('financiacion', "financiacion.codigo = planes_financiacion.codigo_financiacion AND financiacion.estado ='habilitada'");
        $conexion->where('c1.codigo = comisiones.codigo');
        $conexion->order_by('financiacion.numero_cuotas', 'DESC');
        $conexion->order_by('planes_financiacion.valor', 'DESC');
        $conexion->order_by('planes_financiacion.nro_cuota', 'ASC');
        $conexion->limit(1, 0);
        $subquery5 = $conexion->return_query();
        $conexion->resetear();

        //
        $conexion->select('planes_financiacion.valor_neto');
        $conexion->from('comisiones as c1');
        $conexion->join('planes_comisiones', 'planes_comisiones.cod_comision = c1.codigo AND planes_comisiones.mostrar_web = 1');
        $conexion->join('planes_financiacion', 'planes_financiacion.codigo_plan = planes_comisiones.cod_plan AND planes_financiacion.codigo_concepto = 1');
        $conexion->join('financiacion', "financiacion.codigo = planes_financiacion.codigo_financiacion AND financiacion.estado ='habilitada'");
        $conexion->where('c1.codigo = comisiones.codigo');
        $conexion->order_by('financiacion.numero_cuotas', 'DESC');
        $conexion->order_by('planes_financiacion.valor', 'DESC');
        $conexion->order_by('planes_financiacion.nro_cuota', 'ASC');
        $conexion->limit(1, 0);
        $subquery8 = $conexion->return_query();
        $conexion->resetear();
        //

        $conexion->select('SUM(planes_financiacion.valor)');
        $conexion->from('comisiones as c1');
        $conexion->join('planes_comisiones', 'planes_comisiones.cod_comision = c1.codigo AND planes_comisiones.mostrar_web = 1');
        $conexion->join('planes_financiacion', 'planes_financiacion.codigo_plan = planes_comisiones.cod_plan AND planes_financiacion.codigo_concepto = 5');
        $conexion->join('financiacion', "financiacion.codigo = planes_financiacion.codigo_financiacion AND financiacion.estado ='habilitada'");
        $conexion->where('c1.codigo = comisiones.codigo');
        $subquery6 = $conexion->return_query();
        $conexion->resetear();
        //
        $conexion->select('SUM(planes_financiacion.valor_neto)');
        $conexion->from('comisiones as c1');
        $conexion->join('planes_comisiones', 'planes_comisiones.cod_comision = c1.codigo AND planes_comisiones.mostrar_web = 1');
        $conexion->join('planes_financiacion', 'planes_financiacion.codigo_plan = planes_comisiones.cod_plan AND planes_financiacion.codigo_concepto = 5');
        $conexion->join('financiacion', "financiacion.codigo = planes_financiacion.codigo_financiacion AND financiacion.estado ='habilitada'");
        $conexion->where('c1.codigo = comisiones.codigo');
        $subquery9 = $conexion->return_query();
        $conexion->resetear();
        //

        $conexion->select('salones.cupo as cupo');
        $conexion->from('salones');
        $conexion->join('horarios', 'horarios.cod_salon = salones.codigo');
        $conexion->where('horarios.cod_comision = comisiones.codigo');
        $conexion->where('horarios.baja', 0);
        $conexion->order_by("CAST(tipo AS CHAR) DESC, cupo DESC");
        $conexion->limit(1);
        $subquery7 = $conexion->return_query();
        $conexion->resetear();       
        $conexion->select('comisiones.codigo');
        $conexion->select("($subquery7) as cupo", false);
        $conexion->select("IF (comisiones.modalidad = 'intensiva', 95, (SELECT general.planes_academicos.cod_curso ".
                            "FROM general.planes_academicos ".
                            "WHERE general.planes_academicos.codigo = comisiones.cod_plan_academico)) as codigocurso ", false);
        $conexion->select("IFNULL(($subquery2),0) as inscriptos", false);
        $conexion->select("($subquery3) as inicio_clases", false);
        $conexion->select("($subquery4) as nro_cuotas", false);
        $conexion->select("($subquery5) as valor_cuota", false);
        $conexion->select("($subquery8) as valor_cuota_neto", false);/////
        $conexion->select("($subquery6) as valormatricula", false);
        $conexion->select("($subquery9) as valormatriculaneto", false);/////
        $conexion->select('planes_pago.codigo as id_plan');
        $conexion->select('planes_pago.fechainicio  as fechainicio');
        $conexion->select('planes_pago.fechavigencia as fechavigencia');
        $conexion->select('comisiones.dias_prorroga');
        $conexion->select('planes_comisiones.mostrar_financiacion_web');
        $conexion->from('comisiones');
        $conexion->join('planes_comisiones', 'planes_comisiones.cod_comision = comisiones.codigo AND planes_comisiones.mostrar_web = 1 AND planes_comisiones.baja = 0');
        $conexion->join('planes_pago', 'planes_pago.codigo = planes_comisiones.cod_plan AND planes_pago.baja = 0');
        $conexion->where('comisiones.estado', self::$estadoHabilitada);
        $conexion->where('planes_pago.fechavigencia >= CURDATE()', null, false);
        /*$conexion->having('(DATE(inicio_clases) >= DATE_ADD(CURDATE(), INTERVAL - IFNULL(comisiones.dias_prorroga,1) DAY))');
        $conexion->having('nro_cuotas > 0');
        $conexion->having('inscriptos < cupo');*/
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getComisionesActualizarNombre(CI_DB_mysqli_driver $conexion) {
        $conexion->select('count(cn.codigo) + 1');
        $conexion->from('comisiones as cn');
        $conexion->where('cn.cod_plan_academico = comisiones.cod_plan_academico');
        $conexion->where('cn.codigo < comisiones.codigo');
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("comisiones.*, ($subquery) as cantidad", false);
        $conexion->from('comisiones');
        $conexion->where('comisiones.fecha_creacion >= curdate()'); // solamente traemos las comisiones a updatear donde la fecha de creacion sea mayor o igual a hoy-
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getPlanesPago($vigentes = null) {
        $this->oConnection->select("planes_pago.*");
        $this->oConnection->from("planes_pago");
        $this->oConnection->join("planes_comisiones", "planes_comisiones.cod_plan = planes_pago.codigo");
        $this->oConnection->where("planes_comisiones.cod_comision", $this->codigo);
        $this->oConnection->where("planes_comisiones.baja", 0);
        $this->oConnection->where("planes_pago.baja", 0);
        if ($vigentes) {
            $this->oConnection->where('planes_pago.fechainicio <=', date("Y-m-d"));
            $this->oConnection->where('(planes_pago.fechavigencia >= "' . date("Y-m-d") . '" OR planes_pago.fechavigencia IS NULL )');
        }
        $this->oConnection->group_by('planes_pago.codigo');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getCiclosComisiones(CI_DB_mysqli_driver $conexion, $cod_filial, $forzarCiclo = null, $fechaFinCicloDesde = null) {
        $conexion->select('*');
        $conexion->from('general.ciclos');
        $conexion->join('general.filiales_ciclos_academicos', 'general.filiales_ciclos_academicos.cod_ciclo = general.ciclos.codigo');
        $conexion->where('general.filiales_ciclos_academicos.cod_filial', $cod_filial);
        if ($forzarCiclo == null){
            $conexion->where('general.filiales_ciclos_academicos.estado', 'habilitada');
        } else {
            $conexion->where("(general.filiales_ciclos_academicos.estado = 'habilitada' OR general.ciclos.codigo = $forzarCiclo)");
        }
        if ($fechaFinCicloDesde != null){
            $conexion->where("general.ciclos.fecha_fin_ciclo >=", $fechaFinCicloDesde);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getFechaInicioComision() {
        $this->oConnection->select("IFNULL(MIN(horarios.dia),'no_tiene_horarios') as fecha_inicio", false);
        $this->oConnection->from('horarios');
        $this->oConnection->where('horarios.cod_comision', $this->codigo);
        $this->oConnection->where('horarios.baja', '0');
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    static public function getEstadoHabilitada(){
        return self::$estadoHabilitada;
    }
    
    static public function getEstadoInhabilitada(){
        return self::$estadoInhabilitada;
    }
    
    static public function getEstadoDesuso(){
        return self::$estadoDesuso;
    }    
    
    static public function getEstadoAPasar(){
        return self::$estadoAPasar;
    }
    
    /**
     * Recupera las comisiones que poseen alumnos inscriptos, considerando que un alumno está inscripto solo en la comisión en la que más materias cursa
     * 
     * @param mixed $codEstadoAcademico uno o mas (en array) codigos de estados academicos al que pertenece la comision
     * @param mixes $codComision uno o mas (en array) codigo de comisiones
     * @param CI_DB_mysqli_driver $conexion
     * @return array
     */
    static function getComisionesCantidadesInscriptos(CI_DB_mysqli_driver $conexion, $codPlanAcademico = null, $codComision = null,
        $validarPeriodoVencido = true, $codTipoPeriodo = null, $estadoEstadoAcademico = null){
        $estadoAPasar = self::$estadoAPasar;
        $estadoHabilitado = self::$estadoHabilitada;
        $conexion->select("COUNT(ea.codigo)");
        $conexion->from("estadoacademico AS ea");
   		$conexion->where("ea.cod_matricula_periodo = estadoacademico.cod_matricula_periodo");
   		$conexion->where_in("ea.estado" , array('cursando','libre'));
        $sqCantidadCursando = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("estadoacademico.cod_matricula_periodo");
        $conexion->select("estadoacademico.codigo as estadoacademico_codigo");
        $conexion->select("matriculas_inscripciones.cod_comision");
        $conexion->select("COUNT(matriculas_inscripciones.codigo) AS cantidad");
        $conexion->select("($sqCantidadCursando) AS cantidad_estado_cursando", false);
        $conexion->select("matriculas.codigo as codigo_matricula");
        $conexion->from("matriculas_inscripciones");
        $conexion->join("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision AND comisiones.estado IN ('$estadoAPasar', '$estadoHabilitado')");
        $conexion->join("general.ciclos", "general.ciclos.codigo = comisiones.ciclo");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND  matriculas_periodos.estado IN ('habilitada', 'certificada', 'finalizada')");
        // Fix para traer usuarios tambien dado de baja (en matriculas _inscripciones) 26-03-2015
        //$conexion->where("matriculas_inscripciones.baja", 0);
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado IN ('habilitada', 'certificada', 'finalizada')");
        if ($validarPeriodoVencido){
            $conexion->where("general.ciclos.fecha_fin_ciclo < CURDATE()");
        }
        if ($codTipoPeriodo != null){
            $conexion->where("comisiones.cod_tipo_periodo", $codTipoPeriodo);
            $conexion->where("matriculas_periodos.cod_tipo_periodo", $codTipoPeriodo);
        }
        if ($estadoEstadoAcademico != null){
            $tipoFiltro = is_array($estadoEstadoAcademico) ? "where_in" : "where";
            $conexion->$tipoFiltro("estadoacademico.estado", $estadoEstadoAcademico);
        }        
        $conexion->group_by("estadoacademico.cod_matricula_periodo");
        $conexion->group_by("matriculas_inscripciones.cod_comision");
        $conexion->order_by("cod_matricula_periodo ASC");
        $conexion->order_by("cantidad", "DESC");
        if ($codPlanAcademico != null){
            $tipoFiltro = is_array($codPlanAcademico) ? "where_in" : "where";
            $conexion->$tipoFiltro("comisiones.cod_plan_academico", $codPlanAcademico);            
        }
        if ($codComision != null){
            $tipoFiltro = is_array($codComision) ? "where_in" : "where";
            $conexion->$tipoFiltro("matriculas_inscripciones.cod_comision", $codComision);
            //$conexion->where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE `matriculas_inscripciones`.`cod_estado_academico` = `estadoacademico`.`codigo` AND `matriculas_inscripciones`.`baja` = '0' ORDER BY `matriculas_inscripciones`.`codigo` DESC LIMIT 1) = ".$codComision." AND comisiones.codigo =", $codComision);
            //$conexion->where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE  matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND `matriculas_inscripciones`.`baja` = '0' AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ".$codComision." AND comisiones.codigo = ", $codComision);
            //$conexion->having("count(*) > 2");
            $conexion->where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE  matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND `matriculas_inscripciones`.`baja` = '0' AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ", $codComision);

            $conexion->or_where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo  AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ", $codComision);
            //$conexion->having("count(*) > 2");
            $conexion->where("NOT (SELECT matriculas_periodos.estado FROM matriculas_periodos WHERE matriculas_periodos.cod_tipo_periodo > 1 AND matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.estado NOT IN ('migrado', 'inhabilitada'))", NULL, FALSE);
            $conexion->where("comisiones.codigo = ", $codComision);
        }//
   
        $query = $conexion->get();
          // return $conexion->last_query();
        return $query->result_array();
    }
    
    public function getCiclo(){
        $arrResp = Vciclos::listarCiclos($this->oConnection, array("codigo" => $this->ciclo));
        return isset($arrResp[0]) ? $arrResp[0] : array();
    }
    
    public function pasarComision($comisionDestino, $codUsuario, $fechaDesde = null){
        $resp = true;
        if ($this->estado == Vcomisiones::getEstadoAPasar()){
            $validarPeriodoVencido = true;
            $codTipoPeriodo = 1;
            $estadoEstadoAcademico = array(
                Vestadoacademico::getEstadoAprobado(),
                Vestadoacademico::getEstadoHomologado(),
                Vestadoacademico::getEstadoLibre(),
                Vestadoacademico::getEstadoRecursa(),
                Vestadoacademico::getEstadoRegular()
            );
            $arrPlanesAcademicos = Vplanes_academicos::getPlanesAcademicosCantidadPeriodos($this->oConnection, null, 2);
            $planesAcademicos = array();
            foreach ($arrPlanesAcademicos as $plan){
                $planesAcademicos[] = $plan['codigo'];
            }
        } else {
            $validarPeriodoVencido = false;
            $codTipoPeriodo = null;
            $planesAcademicos = null;
            $estadoEstadoAcademico = Vestadoacademico::getEstadoCursando();
        }        
        $arrMatriculasPeriodos = self::getComisionesCantidadesInscriptos($this->oConnection, $planesAcademicos, $this->codigo, $validarPeriodoVencido, $codTipoPeriodo, $estadoEstadoAcademico);
        foreach ($arrMatriculasPeriodos as $matriculaPeriodo){
            $codMatriculaPeriodo = $matriculaPeriodo['cod_matricula_periodo'];
            $myMatriculaPeriodo = new Vmatriculas_periodos($this->oConnection, $codMatriculaPeriodo);
            $resp = $resp && $myMatriculaPeriodo->pasarDeComision($comisionDestino, $codUsuario, $this->codigo, $fechaDesde);
        }
        $arrCiclo = $this->getCiclo();
        $fechaFinCiclo = $arrCiclo['fecha_fin_ciclo'];
        if ($fechaFinCiclo < date("Y-m-d")){
            $resp = $resp && $this->setDesuso();
        } else {
            $resp = $resp && $this->baja($fechaDesde);
        }
        return $resp;
    }
    
    public function getPrefijo(){
        $arrTemp = explode(" ", $this->nombre);
        return $arrTemp[0];
    }
    
    public function getNombre(){
        $prefijo = $this->getPrefijo();
        return trim(str_replace("$prefijo", "", $this->nombre));
    }
    
    static function listarComisiones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false, $incluirComision = null){
        if ($incluirComision != null){
            if ($condiciones != null){
                $conexion->where($condiciones);
                $condiciones = null;
            }            
            $conexion->or_where("codigo", $incluirComision);                
        }
        return parent::listarComisiones($conexion, $condiciones, $limite, $orden, $grupo, $contar);
    }
    
    static function listarComisionesMateria(CI_DB_mysqli_driver $conexion, $cod_materia){    
        $conexion->select('*');
        $conexion->from('comisiones');
        $conexion->where("comisiones.estado = 'habilitado'");
        $conexion->where("comisiones.cod_plan_academico IN (SELECT general.materias_plan_academico.cod_plan FROM general.materias_plan_academico WHERE general.materias_plan_academico.cod_materia = ".$cod_materia.")");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getComisionesRematricular($conexion, $cursos){
        /*
            Pido todas las comisiones, con las fecha de inicio y fin de ciclo. De ahí se sacan los años, carreras y comsiones.
        */
        $conexion->select('comisiones.codigo');
        $conexion->select('comisiones.nombre');
        $conexion->selecT('cursos.codigo as cod_curso');
        $conexion->select('comisiones.ciclo as ciclo');
        $conexion->select('ciclos.fecha_inicio_ciclo');
        $conexion->select('ciclos.fecha_fin_ciclo');
        $conexion->select('cursos.nombre_pt as nombre_curso');
        $conexion->from('comisiones');
        $conexion->join('general.ciclos', 'ciclos.codigo = comisiones.ciclo');
        $conexion->join('general.planes_academicos', 'planes_academicos.codigo = comisiones.cod_plan_academico');
        $conexion->join('general.cursos', 'planes_academicos.cod_curso = cursos.codigo');
        $conexion->where('comisiones.estado', 'habilitado');
        $conexion->where('cursos.codigo in (' . implode(',', $cursos) . ')');
        $conexion->order_by('cod_plan_academico');
        $query = $conexion->get();
        $comisiones = $query->result_array();
        $conexion->resetear();
        $comisionesConInscriptos = array();
        foreach($comisiones as $comision){
            $inscriptos = Vcomisiones::getComisionesCantidadesInscriptos($conexion, null, $comision['codigo'], false);
            $conexion->resetear();
            if(count($inscriptos) > 0)
                $comisionesConInscriptos[] = $comision;
        }
        return $comisionesConInscriptos;
     }

    public static function getComisionesFiltro(CI_DB_mysqli_driver $conexion, $arrCondiciones = null) {
        $conexion->select('comisiones.codigo');
        $conexion->from('comisiones');
        $conexion->join('general.ciclos', 'ciclos.codigo = comisiones.ciclo');
        $conexion->join('general.planes_academicos', 'planes_academicos.codigo = comisiones.cod_plan_academico');
        $conexion->join('general.cursos', 'planes_academicos.cod_curso = cursos.codigo');
//        $conexion->where('comisiones.estado', 'habilitado');
        if ($arrCondiciones != null) {
                $conexion->where_in('cursos.codigo',$arrCondiciones['cursos.codigo']);
                $conexion->where_in('ciclos.codigo',$arrCondiciones['ciclos.codigo']);
                $conexion->where_in('comisiones.modalidad',$arrCondiciones['comisiones.modalidad']);
                $conexion->where_in('comisiones.cod_tipo_periodo', $arrCondiciones['comisiones.cod_tipo_periodo']);
            
        }
//        echo "<pre>";
//        print_r($conexion->return_query());
//        die();
        $query = $conexion->get();
        $comisiones = $query->result_array();
        return $comisiones;
    }
    
}
