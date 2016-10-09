<?php

/**
 * Class Vplanes_academicos
 *
 * Class  Vplanes_academicos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vplanes_academicos extends Tplanes_academicos {

    static private $_estadoInhabilitado = "inhabilitado";
    static private $_estadoHabilitado = "habilitado";

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function setfilialClasesPropiedades($idFilial, $idMateria, $duracion, $frecuencia){
        $this->oConnection->where("id_plan_academico", $this->codigo);
        $this->oConnection->where("id_filial", $idFilial);
        $this->oConnection->where("id_materia", $idMateria);
        $resp = $this->oConnection->delete("general.filiales_clases_propiedades");
        $param = array("id_plan_academico" => $this->codigo,
                "id_filial" => $idFilial,
                "id_materia" => $idMateria,
                "duracion" => $duracion,
                "frecuencia" => $frecuencia);
        return $resp && $this->oConnection->insert("general.filiales_clases_propiedades", $param);
    }
    
    public function inhabilitar() {
        $this->oConnection->where("general.planes_academicos.codigo", $this->codigo);
        if ($this->oConnection->update("general.planes_academicos", array("estado" => self::$_estadoInhabilitado))) {
            $this->estado = self::$_estadoInhabilitado;
            return true;
        } else {
            return false;
        }
    }

    public function habilitar() {
        $this->oConnection->where("general.planes_academicos.codigo", $this->codigo);
        if ($this->oConnection->update("general.planes_academicos", array("estado" => self::$_estadoHabilitado))) {
            $this->estado = self::$_estadoHabilitado;
            return true;
        } else {
            return false;
        }
    }

    function getPeriodos($codTipoPeriodo = null) {
        $conexion = $this->oConnection;
        $conexion->select('general.planes_academicos_periodos.cod_tipo_periodo');
        $conexion->select('general.tipos_periodos.nombre');
        $conexion->select('general.planes_academicos_periodos.padre');
        $conexion->select('general.planes_academicos_periodos.hs_reloj');
//$conexion->select('general.titulos.nombre');
        $conexion->select('general.planes_academicos_periodos.cod_titulo');
        $conexion->select('general.planes_academicos_periodos.color');
        $conexion->select('general.planes_academicos_periodos.orden');
        $conexion->from('general.planes_academicos_periodos');
        $conexion->join('general.tipos_periodos', 'general.planes_academicos_periodos.cod_tipo_periodo = general.tipos_periodos.codigo');
        //$conexion->join('general.titulos', 'general.titulos.codigo = general.planes_academicos_periodos.cod_tipo_periodo');
        $conexion->where('general.planes_academicos_periodos.cod_plan_academico', $this->codigo);
        if ($codTipoPeriodo != null) {
            $conexion->where("general.planes_academicos_periodos.cod_tipo_periodo", $codTipoPeriodo);
        }
        $conexion->order_by('general.planes_academicos_periodos.orden', 'asc');
        $query = $conexion->get();
        return $query->result_array();
    }

    function getComisiones($activa = 1, $ciclohabilitado = true, $cicloactual = false, $tipoperiodo = null, $inscriptos = true, $modalidad = null, $cod_ciclo = null, $puedematricular = false) {
        $conexion = $this->oConnection;

        $conexion->select('comisiones.*');

        if ($inscriptos) {
            $conexion->select("(IFNULL((SELECT count( DISTINCT matriculas_periodos.cod_matricula ) FROM matriculas_periodos "
                    . "JOIN matriculas ON matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado <> '".Vmatriculas::getEstadoPrematricula()."' AND matriculas.estado <> '".Vmatriculas::getEstadoFinalizada()."' "
                    . "JOIN estadoacademico ON estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo "
                    . "JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo "
                    . "WHERE matriculas_inscripciones.cod_comision = comisiones.codigo and matriculas_inscripciones.baja = 0 "
                    . "GROUP BY matriculas_inscripciones.cod_comision ), 0)) as inscriptos ", false);
        }

        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $conexion->from('comisiones');
        $conexion->where('general.planes_academicos.codigo', (int) $this->codigo);
        if ($ciclohabilitado || $cicloactual || $puedematricular) {
            $conexion->join('general.ciclos', 'general.ciclos.codigo = comisiones.ciclo');
            $conexion->join('general.filiales_ciclos_academicos', 'general.filiales_ciclos_academicos.cod_ciclo = comisiones.ciclo AND general.filiales_ciclos_academicos.cod_filial = ' . $conexion->database);
            if ($ciclohabilitado) {
                $conexion->where('general.filiales_ciclos_academicos.estado', 'habilitada');
            }
            if ($cicloactual) {
                $conexion->where('(general.ciclos.fecha_inicio_ciclo <= curdate() AND general.ciclos.fecha_fin_ciclo >= curdate())');
            }
            if ($puedematricular) {
                $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_plan_academico = comisiones.cod_plan_academico AND '
                        . 'general.planes_academicos_filiales.cod_tipo_periodo = comisiones.cod_tipo_periodo AND general.planes_academicos_filiales.cod_filial = ' . $conexion->database . ' AND '
                        . 'general.planes_academicos_filiales.modalidad = comisiones.modalidad');
                $conexion->where('IF(general.planes_academicos_filiales.dias_matriculacion IS NULL, true, DATE_ADD(general.ciclos.fecha_inicio_ciclo, INTERVAL general.planes_academicos_filiales.dias_matriculacion DAY) >= CURDATE())');
            }
        }

        if ($activa != null) {
            $conexion->where('comisiones.estado', Vcomisiones::getEstadoHabilitada());
        }

        if ($tipoperiodo != null) {
            $conexion->where('comisiones.cod_tipo_periodo', (int) $tipoperiodo);
        }
        if ($modalidad != null) {
            $conexion->where('comisiones.modalidad', $modalidad);
        }
        if ($cod_ciclo != null) {
            $conexion->where('comisiones.ciclo', $cod_ciclo);
        }

        $conexion->group_by('comisiones.codigo');

        $query = $conexion->get();

        return $query->result_array();
    }

    function getCurso() {
        $objCurso = new Vcursos($this->oConnection, $this->cod_curso);
        return $objCurso;
    }

    function getMaterias($periodo = null) {

        $this->oConnection->select("general.materias.*", false);
        $this->oConnection->select("general.materias_plan_academico.cod_tipo_periodo");
        $this->oConnection->select("general.materias_plan_academico.cantidad_clases");
        $this->oConnection->select("general.planes_academicos.nombre as nombreplan");
        $this->oConnection->from("general.materias");
        $this->oConnection->join("general.materias_plan_academico", "general.materias_plan_academico.cod_materia = general.materias.codigo");
        $this->oConnection->join("general.planes_academicos", "general.planes_academicos.codigo = general.materias_plan_academico.cod_plan");
        $this->oConnection->where("general.planes_academicos.codigo", $this->codigo);
        if ($periodo != null) {
            $this->oConnection->where("general.materias_plan_academico.cod_tipo_periodo", $periodo);
        }
        $this->oConnection->order_by("general.materias_plan_academico.cod_tipo_periodo", "asc");
        $query = $this->oConnection->get();

        return $query->result_array();
    }

    function getPeriodosAnteceden($cod_tipo_periodo) {
        $conexion = $this->oConnection;
        $conexion->select('general.planes_academicos_periodos.cod_tipo_periodo');
        $conexion->from('general.planes_academicos_periodos');
        $conexion->where('general.planes_academicos_periodos.cod_plan_academico', $this->codigo);
        $conexion->where('general.planes_academicos_periodos.orden < (select orden from general.planes_academicos_periodos '
                . 'where general.planes_academicos_periodos.cod_plan_academico = ' . $this->codigo . ' '
                . 'and general.planes_academicos_periodos.cod_tipo_periodo = ' . $cod_tipo_periodo . ')');
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getCantHorasPlanAcademico(CI_DB_mysqli_driver $conexion, $cod_plan_academico = null, $cod_tipo_periodo = null) {
        $conexion->select('sum(general.planes_academicos_periodos.hs_reloj) as cant_horas');
        $conexion->from('general.planes_academicos_periodos');
        if ($cod_plan_academico != null) {
            $conexion->where('general.planes_academicos_periodos.cod_plan_academico', $cod_plan_academico);
        }
        if ($cod_tipo_periodo != null) {
            if (is_array($cod_tipo_periodo)) {
                $conexion->where_in('general.planes_academicos_periodos.cod_tipo_periodo', $cod_tipo_periodo);
            } else {
                $conexion->where('general.planes_academicos_periodos.cod_tipo_periodo', $cod_tipo_periodo);
            }
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    function getPeriodosModalidadesFilial($cod_filial, $cod_periodo = null, $modalidad = null, $habilitados = true) {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from('general.planes_academicos_filiales');
        $conexion->where('general.planes_academicos_filiales.cod_plan_academico', $this->codigo);
        $conexion->where("general.planes_academicos_filiales.cod_filial", $cod_filial);
        if ($cod_periodo != null) {
            $conexion->where('general.planes_academicos_filiales.cod_tipo_periodo', $cod_periodo);
        }
        if ($modalidad != null) {
            $conexion->where('general.planes_academicos_filiales.modalidad', $modalidad);
        }
        if ($habilitados) {
            $conexion->where('general.planes_academicos_filiales.estado', 'habilitado');
        }

        $conexion->order_by('general.planes_academicos_filiales.modalidad', 'desc');
        $query = $conexion->get();
        return $query->result_array();
    }

    function getNombrePeriodoModalidadFilial($cod_periodo, $modalidad, $cod_filial) {
        $conexion = $this->oConnection;
        $conexion->select('general.planes_academicos_filiales.nombre_periodo');
        $conexion->from('general.planes_academicos_filiales');
        $conexion->where('general.planes_academicos_filiales.cod_plan_academico', $this->codigo);
        $conexion->where("general.planes_academicos_filiales.cod_filial", $cod_filial);
        $conexion->where('general.planes_academicos_filiales.cod_tipo_periodo', $cod_periodo);
        $conexion->where('general.planes_academicos_filiales.modalidad', $modalidad);
        $query = $conexion->get();
        $resultado = $query->result_array();
        $respuesta = count($resultado) > 0 && $resultado[0]['nombre_periodo'] != '' ? lang($resultado[0]['nombre_periodo']) : lang(Vtipos_periodos::getNombre($conexion, $cod_periodo));
        return $respuesta;
    }

    function getPeriodosFilial($cod_filial) {
        $conexion = $this->oConnection;

        $conexion->select('*');
        $conexion->from('general.tipos_periodos');
        $conexion->join('general.planes_academicos_periodos', 'general.planes_academicos_periodos.cod_tipo_periodo = general.tipos_periodos.codigo');
        $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_tipo_periodo = general.tipos_periodos.codigo AND general.planes_academicos_filiales.cod_plan_academico = ' . $this->codigo . ' AND general.planes_academicos_filiales.cod_filial = ' . $cod_filial . '');
        $conexion->where('general.planes_academicos_periodos.cod_plan_academico', $this->codigo);
        $conexion->where('general.planes_academicos_filiales.estado', 'habilitado');
        $conexion->group_by('general.tipos_periodos.codigo');

        $query = $conexion->get();
        return $query->result_array();
    }

    public function getPlanesFilial($codFilial = null, $codTipoPeriodo = null, $modalidad = null, $estado = null) {
        $this->oConnection->select("*");
        $this->oConnection->from("general.planes_academicos_filiales");
        $this->oConnection->where("general.planes_academicos_filiales.cod_plan_academico", $this->codigo);
        if ($codFilial != null)
            $this->oConnection->where("general.planes_academicos_filiales.cod_filial", $codFilial);
        if ($codTipoPeriodo != null)
            $this->oConnection->where("general.planes_academicos_filiales.cod_tipo_periodo", $codTipoPeriodo);
        if ($modalidad != null)
            $this->oConnection->where("general.planes_academicos_filiales.modalidad", $modalidad);
        if ($estado != null)
            $this->oConnection->where("general.planes_academicos_filiales.modalidad", $estado);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /* La siguiente function esta siendo accedida desde un web services NO MODIFICAR, ELIMINAR NI COMENTAR */

    /**
     * Set de tabla planes_academicos filiales para el codigo de plan academico de la instancia
     * 
     * 
     * @param array $arrFiliales    formato [cod_filial][estado] = 'habilitado' (cualquier otro estado es eliminado)
     *                              Optativo [cod_filial][periodo] (actualiza los registros para los periodos indicados como cod_tipo_periodo)(cualquier otro periodo será eliminado)
     *                              NOTA: los codigos de filiales no presentes en este array seran eliminados.
     *                              Para agregar filiales sin alterar las ya insertadas crear la fnuctoin addFiliales    
     * @return boolean
     */
    public function setFiliales(array $arrFiliales) {
        $resp = true;
        $arrFilialesNoEliminar = array();
        foreach ($arrFiliales as $codFilial => $filial) {
            if ($filial['estado'] == 'habilitado') {
                if (isset($filial['periodo']) && is_array($filial['periodo'])) {
                    $this->oConnection->where("cod_plan_academico", $this->codigo);
                    $this->oConnection->where("cod_filial", $codFilial);
                    $resp = $resp && $this->oConnection->delete("planes_academicos_filiales");
                    foreach ($filial['periodo'] as $periodo) {
                        $fields = array();
                        $fields['cod_plan_academico'] = $this->codigo;
                        $fields['cod_tipo_periodo'] = $periodo['cod_tipo_periodo'];
                        $fields['cod_filial'] = $codFilial;
                        $fields['modalidad'] = $periodo['modalidad'];
                        if (isset($periodo['cant_meses']) && $periodo['cant_meses'] <> '') {
                            $fields['cant_meses'] = $periodo['cant_meses'];
                        }
                        if (isset($periodo['nombre_periodo']) && $periodo['nombre_periodo'] <> '') {
                            $fields['nombre_periodo'] = $periodo['nombre_periodo'];
                        }
                        if (isset($periodo['cod_titulo']) && $periodo['cod_titulo'] <> '') {
                            $fields['cod_titulo'] = $periodo['cod_titulo'];
                        }
                        if (isset($periodo['dias_matriculacion']) && $periodo['dias_matriculacion'] <> '') {
                            $fields['dias_matriculacion'] = $periodo['dias_matriculacion'];
                        }
                        $resp = $resp && $this->oConnection->insert("planes_academicos_filiales", $fields);
                    }
                }
                $arrFilialesNoEliminar[] = $codFilial;
            }
        }
        $this->oConnection->where("cod_plan_academico", $this->codigo);
        $this->oConnection->where_not_in("cod_filial", $arrFilialesNoEliminar);
        $resp = $resp && $this->oConnection->delete("planes_academicos_filiales");
        return $resp;
    }

    /* La siguiente function esta siendo accedida desde un Web Services NO MODIFICAR, ELIMINAR NI COMENTAR */

    public function setPeriodos(array $arrPeriodos) {
        $this->oConnection->where("cod_plan_academico", $this->codigo);
        $resp = $this->oConnection->delete("planes_academicos_periodos");
        $this->oConnection->where("cod_plan", $this->codigo);
        $resp = $resp && $this->oConnection->delete("materias_plan_academico");
        foreach ($arrPeriodos as $periodo) {
            $fields = array();
            $fields['cod_plan_academico'] = $this->codigo;
            $fields['cod_tipo_periodo'] = $periodo['cod_tipo_periodo'];
            $fields['orden'] = $periodo['orden'];
            $fields['hs_reloj'] = $periodo['hs_reloj'];
            $fields['cod_titulo'] = $periodo['cod_titulo'];
            if (isset($periodo['padre']) && $periodo['padre'] <> '') {
                $fields['padre'] = $periodo['padre'];
            }
            if (isset($periodo['color']) && $periodo['color'] <> '') {
                $fields['color'] = $periodo['color'];
            }
            $resp = $resp && $this->oConnection->insert("planes_academicos_periodos", $fields);
            foreach ($periodo['materias'] as $materia) {
                if (is_array($materia)){
                    $fields = array(
                        "cod_materia" => $materia['materia'],
                        "cod_tipo_periodo" => $periodo['cod_tipo_periodo'],
                        "cod_plan" => $this->codigo,
                        "cantidad_clases" => $materia['clases']
                    );
                } else {
                    $fields = array(
                        "cod_materia" => $materia,
                        "cod_tipo_periodo" => $periodo['cod_tipo_periodo'],
                        "cod_plan" => $this->codigo,
                        "cantidad_clases" => 0
                    );
                }
                $resp = $resp && $this->oConnection->insert("materias_plan_academico", $fields);
            }
        }
        return $resp;
    }

    /* La siguiente function esta siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */

    static public function getReporte(CI_DB_mysqli_driver $conexion, $curso = null, $estado = null, $cantidadPeriodos = null, $codigo = null) {
        $conexion->select("COUNT(DISTINCT general.planes_academicos_periodos.cod_tipo_periodo)", false);
        $conexion->from("general.planes_academicos_periodos");
        $conexion->where("general.planes_academicos_periodos.cod_plan_academico = general.planes_academicos.codigo");
        $sqPeriodos = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("general.cursos.nombre_es AS curso_nombre", false);
        $conexion->select("($sqPeriodos) AS cantidad_periodos", false);
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $condiciones = array();
        if ($curso != null)
            $condiciones["general.planes_academicos.cod_curso"] = $curso;
        if ($estado != null)
            $condiciones["general.planes_academicos.estado"] = $estado;
        if ($codigo != null)
            $condiciones['general.planes_academicos.codigo'] = $codigo;
        if ($cantidadPeriodos !== null)
            $conexion->having("cantidad_periodos", $cantidadPeriodos);
        return self::listarPlanes_academicos($conexion, $condiciones);
    }

    /* La siguiente function esta siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */

    static public function getModalidades(CI_DB_mysqli_driver $conexion) {
        $query = $conexion->query("SHOW COLUMNS FROM planes_academicos_filiales WHERE Field = 'modalidad'");
        $arrTemp = $query->result_array();
        $type = $arrTemp[0]['Type'];
        $ocurrencias = '';
        $enum = array();
        preg_match('/^enum\((.*)\)$/', $type, $ocurrencias);
        foreach (explode(',', $ocurrencias[1]) as $value) {
            $enum[] = trim($value, "'");
        }
        return $enum;
    }

    /* La siguiente function esta siendo accedida desde un Web Services NO MODIFICAR, COMENTAR NI ELIMINAR */

    static public function getNombresPeriodos(CI_DB_mysqli_driver $conexion) {
        $query = $conexion->query("SHOW COLUMNS FROM planes_academicos_filiales WHERE Field = 'nombre_periodo'");
        $arrTemp = $query->result_array();
        $type = $arrTemp[0]['Type'];
        $ocurrencias = '';
        $enum = array();
        preg_match('/^enum\((.*)\)$/', $type, $ocurrencias);
        foreach (explode(',', $ocurrencias[1]) as $value) {
            $enum[] = trim($value, "'");
        }
        return $enum;
    }

    static public function getPlanesAcademicosCantidadPeriodos(CI_DB_mysqli_driver $conexion, $codPlanAcademico = null, $cantidadPeriodosDesde = null, $cantidadPeriodosHasta = null) {
        $conexion->select("COUNT(general.planes_academicos_periodos.cod_tipo_periodo)", false);
        $conexion->from("general.planes_academicos_periodos");
        $conexion->where("general.planes_academicos_periodos.cod_plan_academico = general.planes_academicos.codigo");
        $sqCantidad = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("general.planes_academicos.*", false);
        $conexion->select("($sqCantidad) AS cantidad");
        $conexion->from("general.planes_academicos");
        if ($codPlanAcademico != null) {
            $tipoFiltro = is_array($codPlanAcademico) ? "where_in" : "where";
            $conexion->$tipoFiltro("general.planes_academicos.codigo", $codPlanAcademico);
        }
        if ($cantidadPeriodosDesde != null) {
            $conexion->having("cantidad >=", $cantidadPeriodosDesde);
        }
        if ($cantidadPeriodosHasta != null) {
            $conexion->having("cantidad <=", $cantidadPeriodosHasta);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getPlanesPago($vigentes = true) {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from('planes_pago');
        $conexion->join('planes_cursos_periodos', 'planes_cursos_periodos.cod_plan_pago = planes_pago.codigo');
        $conexion->where('planes_cursos_periodos.cod_curso', $this->codigo);
        $conexion->where("planes_pago.baja", 0);
        if ($vigentes) {
            $conexion->where('planes_pago.fechainicio <=', date("Y-m-d"));
            $conexion->where('(planes_pago.fechavigencia >= "' . date("Y-m-d") . '" OR planes_pago.fechavigencia IS NULL )');
        }
        $conexion->group_by('planes_pago.codigo');
        $query = $conexion->get();
        return $query->result_array();
    }

    static public function getPlanesAcademicosFilial(CI_DB_mysqli_driver $conexion, $codFilial, $idioma = null){
        $conexion->select("general.planes_academicos.codigo");
        $conexion->select("general.planes_academicos.cod_curso");
        if ($idioma == null){
            $conexion->select("general.cursos.nombre_es");
            $conexion->select("general.cursos.nombre_pt");
            $conexion->select("general.cursos.nombre_in");
            $conexion->order_by("general.cursos.nombre_es");
        } else {
            $conexion->select("general.cursos.nombre_$idioma");
            $conexion->order_by("general.cursos.nombre_$idioma");
        }
        $conexion->from("general.planes_academicos");
        $conexion->join("general.planes_academicos_filiales", "general.planes_academicos_filiales.cod_plan_academico = general.planes_academicos.codigo".
                         " AND general.planes_academicos_filiales.cod_filial = $codFilial AND general.planes_academicos_filiales.estado = 'habilitado'");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->where("general.planes_academicos.estado", "habilitado");
        $conexion->group_by("general.planes_academicos_filiales.cod_plan_academico");        
        $query = $conexion->get();
        return $query->result_array();
    }
    
    public function getTitulos($codFilial, $codTipoPeriodo = null){
        $this->oConnection->select("general.titulos.*", false);
        $this->oConnection->from("general.planes_academicos_filiales");
        $this->oConnection->join("general.titulos", "general.titulos.codigo = general.planes_academicos_filiales.cod_titulo");
        $this->oConnection->where("planes_academicos_filiales.cod_plan_academico", $this->codigo);
        $this->oConnection->where("planes_academicos_filiales.cod_filial", $codFilial);
        if ($codTipoPeriodo != null){
            $this->oConnection->where("planes_academicos_filiales.cod_tipo_periodo", $codTipoPeriodo);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }    
    
    static public function listar(CI_DB_mysqli_driver $conexion){
        $conexion->select("general.planes_academicos.*", false);
        $conexion->select("general.cursos.nombre_es");
        $conexion->from("general.planes_academicos");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        $conexion->where("general.planes_academicos.estado", self::$_estadoHabilitado);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /**
     * Recupera las materias perteneciente al plan academico indicado (si se especifica mas de un plan academico recupera la interseccion entre ellos y no la union de sus materias)
     * 
     * @param CI_DB_mysqli_driver $conexion     Objeto de conexion a la base de datos
     * @param mixed $codPlanAcademico           Un codigo de plan academico o un array de ellos
     * @return array
     */
    static public function listar_materias(CI_DB_mysqli_driver $conexion, $codPlanAcademico = null){
        $conexion->select("general.materias.*", false);
        $conexion->from("general.materias");
        if ($codPlanAcademico != null){
            if (!is_array($codPlanAcademico)){
                $codPlanAcademico = array($codPlanAcademico);
            }
            foreach ($codPlanAcademico as $planAcademico){
                $conexion->where("materias.codigo in (select cod_materia from materias_plan_academico where materias_plan_academico.cod_plan = $planAcademico)");
            }
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function datosPlan_academico(CI_DB_mysqli_driver $conexion, $curso = null) {
        $conexion->select("general.cursos.cant_horas");
        $conexion->select("general.cursos.cantidad_meses");
        $conexion->from("general.cursos");
        $conexion->where("general.cursos.codigo", $curso);
        $query = $conexion->get();
        return $query->result_array();
    }
}
