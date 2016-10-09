<?php

/**
 * Class Vexamenes
 *
 * Class  Vexamenes maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vexamenes extends Texamenes {

    private static $array = array(
        array('id' => 'PARCIAL', 'nombre' => 'PARCIAL'),
        array('id' => 'RECUPERATORIO_PARCIAL', 'nombre' => 'RECUPERATORIO_PARCIAL'),
        array('id' => 'FINAL', 'nombre' => 'FINAL'),
        array('id' => 'RECUPERATORIO_FINAL', 'nombre' => 'RECUPERATORIO_FINAL'));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getArrayExamenes($id = false) {
        $devolver = '';
        if ($id != false) {
            $array = self::$array;
            foreach ($array as $value) {
                foreach ($id as $tipoExamen) {
                    if ($value['id'] == $tipoExamen) {

                        $devolver[] = array(
                            'id' => $value['id'],
                            'nombre' => lang($value['id'])
                        );
                    }
                }
            }
        } else {

            $examenes = self::$array;
            foreach ($examenes as $key => $examen) {
                $examenes[$key] = array('id' => $examen['id'], 'nombre' => lang($examen['id']));
            }
            return $examenes;
        }
        //print_r($devolver);
        return $devolver;
    }
	
	/**
     * Retorna array de examenes parciales (no recuperatorios) de una materia
	 * que cumplan con las condiciones recibidas en el parámetro $where.
	 * 
     * @access public
     * @return Array de Parciales.
     */
	static function getArrayExamenesWhere(CI_DB_mysqli_driver $conexion, $where, $limit = null) {
		$conexion->select("examenes.*");
		$conexion->from("examenes");
		$conexion->where($where);
		if (!is_null($limit)) {
			$conexion->limit("".$limit);
		}
		$query_result = $conexion->get();
		
		if ($query_result !== false) {
			$query_result = $query_result->result_array();
		}
		
		return $query_result;
	}

    static function listarExamenesParcialesDataTable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $wherein = false) {
        
        $conexion->select('count(examenes_estado_academico.cod_estado_academico)');
        $conexion->from('examenes_estado_academico');
        $conexion->where('examenes.codigo = examenes_estado_academico.cod_examen');
        $conexion->where('examenes_estado_academico.estado <>', 'baja');
        $subQuery = $conexion->return_query();
        $conexion->resetear();

        $conexion->select('examenes.codigo');
        $conexion->select('general.materias.nombre_es as nomMateria');
        $conexion->select('examenes.tipoexamen');
        $conexion->select('general.materias.nombre_in');
        $conexion->select('general.materias.nombre_pt');
        $conexion->select("comisiones.nombre as nomComision");
        $conexion->select('examenes.fecha');
        $conexion->select('examenes.hora');
        $conexion->select('examenes.horafin');
        $conexion->select('examenes.cupo');
        $conexion->select("( $subQuery ) as cantinscriptos", false);
        $conexion->select('examenes.baja');
        $conexion->from('examenes');
        $conexion->join('comisiones', 'comisiones.codigo = examenes.cod_comision');
        $conexion->join('general.materias', 'general.materias.codigo = examenes.materia');
        $conexion->where_in('examenes.tipoexamen', $wherein);
        $conexion->group_by('examenes.codigo');
        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_having("$key like '%$value%'");
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();

        $arrespParciales = $query->result_array();
        if ($contar) {
            return count($arrespParciales);
        } else {
            return $arrespParciales;
        }
    }

    static function listarExamenesFinalesDataTable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $wherein = false) {
        $nombreMateria = "general.materias.nombre_" . get_idioma();
        $conexion->select('count(examenes_estado_academico.cod_estado_academico)');
        $conexion->from('examenes_estado_academico');
        $conexion->where('examenes.codigo = examenes_estado_academico.cod_examen');
        $conexion->where('examenes_estado_academico.estado <>', 'baja');
        $subQuery = $conexion->return_query();
        $conexion->resetear();

        $conexion->select('examenes.codigo');
        $conexion->select("$nombreMateria as nomMateria");
        $conexion->select('examenes.tipoexamen');
        $conexion->select('examenes.fecha');
        $conexion->select('examenes.hora');
        $conexion->select('examenes.horafin');
        $conexion->select('examenes.cupo');
        $conexion->select('comisiones.nombre as nomComision');
        $conexion->select('(' . $subQuery . ') as cantinscriptos,examenes.baja', false);
        $conexion->from('examenes');
        $conexion->join('general.materias', 'general.materias.codigo = examenes.materia');
        $conexion->join('comisiones', 'comisiones.codigo = examenes.cod_comision', 'LEFT');
        $conexion->where_in('examenes.tipoexamen', $wherein);
        $conexion->group_by('examenes.codigo');

        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_having("$key like '%$value%'");
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {

            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        $arrespFinales = $query->result_array();
        if ($contar) {
            return count($arrespFinales);
        } else {
            return $arrespFinales;
        }
    }

    static function getReporteExamenes(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null) {

        $aColumns = array();
        $aColumns['fecha']['order'] = "examenes.fecha";
        $aColumns['codigo']['order'] = "examenes.codigo";
        $aColumns['cantidad_inscriptos']['order'] = "cantidad_inscriptos";
        $aColumns['cantidad_pasados']['order'] = "cantidad_pasados";
        $aColumns['porcentaje_a_pasar']['order'] = "porcentaje_a_pasar";
        $aColumns['materia_nombre_es']['order'] = "materias.nombre_es";
        $aColumns['fecha']['having'] = "fecha";
        $aColumns['codigo']['having'] = "examenes.codigo";
        $aColumns['cantidad_inscriptos']['having'] = "cantidad_inscriptos";
        $aColumns['cantidad_pasados']['having'] = "cantidad_pasados";
        $aColumns['porcentaje_a_pasar']['having'] = "porcentaje_a_pasar";
        $aColumns['materia_nombre_es']['having'] = "materias.nombre_es";

        $conexion->select("COUNT(examenes_estado_academico.cod_estado_academico)");
        $conexion->from("examenes_estado_academico");
        $conexion->where("examenes_estado_academico.cod_examen = examenes.codigo");
        $queryCantidadInscriptos = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("COUNT(cod_inscripcion)");
        $conexion->from("notas_resultados");
        $conexion->join("examenes_estado_academico", "examenes_estado_academico.codigo = notas_resultados.cod_inscripcion");
        $conexion->where("examenes_estado_academico.cod_examen = examenes.codigo");
        $queryCantidadPasados = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("CONCAT(LPAD(DAY(examenes.fecha), 2, 0), '/', LPAD(MONTH(examenes.fecha), 2, 0), '/', YEAR(examenes.fecha)) AS fecha", false);
        $conexion->select("examenes.codigo");
        $conexion->select("general.materias.nombre_es AS materia_nombre_es");
        $conexion->select("($queryCantidadInscriptos) AS cantidad_inscriptos", false);
        $conexion->select("($queryCantidadPasados) AS cantidad_pasados", false);
        
        $conexion->select("CONCAT(TRUNCATE(( SELECT(SELECT count(distinct notas_resultados.cod_inscripcion) FROM notas_resultados INNER JOIN examenes_estado_academico ON examenes_estado_academico.codigo = notas_resultados.cod_inscripcion
        WHERE examenes_estado_academico.cod_examen = examenes.codigo AND notas_resultados.nota is not null) * 100 / (SELECT count(examenes_estado_academico.codigo)
        FROM examenes_estado_academico WHERE examenes_estado_academico.cod_examen = examenes.codigo and examenes_estado_academico.estado <> 'baja' )), 2), ' %') AS porcentaje_a_pasar", false);
        
        $conexion->from("examenes");
        $conexion->join("general.materias", "general.materias.codigo = examenes.materia");

        if ($fechaDesde != null)
            $conexion->where("DATE(examenes.fecha) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(examenes.fecha) <=", $fechaHasta);
        if ($search != null) {
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $conexion->or_having($tableFields['having'] . " LIKE ", "%$search%");
                }
            }
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    public function getCantidadNotasCargadas(){
        $this->oConnection->select("COUNT(codigo) AS cantidad", false);
        $this->oConnection->from("examenes_estado_academico");
        $this->oConnection->join("notas_resultados", "notas_resultados.cod_inscripcion = examenes_estado_academico.codigo AND notas_resultados.nota IS NOT NULL");
        $this->oConnection->where("examenes_estado_academico.estado <>", "baja");
        $this->oConnection->where("examenes_estado_academico.cod_examen", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        return $arrTemp[0]['cantidad'];
    }
    
    public function getInscriptosExamen() {
        $nombreApellido = formatearNomApeQuery();
        $this->oConnection->select('examenes_estado_academico.codigo');
        $this->oConnection->select("CONCAT($nombreApellido) AS nombre_apellido", false);
        $this->oConnection->select('examenes_estado_academico.fechadeinscripcion');
        $this->oConnection->select('examenes.fecha');
        $this->oConnection->select('alumnos.codigo AS cod_alumno');
        $this->oConnection->select("examenes_estado_academico.cod_estado_academico");
        $this->oConnection->select('estadoacademico.porcasistencia');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('examenes', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND matriculas_periodos.estado NOT IN ("migrado", "inhabilitada")');
        $this->oConnection->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado NOT IN ("migrado", "inhabilitada")'); // quita inhabilitada ver ticket 5062
        $this->oConnection->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $this->codigo);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        if ($this->tipoexamen == 'FINAL' || $this->tipoexamen == 'RECUPERATORIO_FINAL'){
            $this->oConnection->where_in('estadoacademico.estado', array("regular", "libre", "aprobado", "cursando"));
        }
        $this->oConnection->order_by('nombre_apellido', 'ASC');
        $query = $this->oConnection->get();
//        die($this->oConnection->last_query());
        return $query->result_array();
    }
    
    public function getCantidadInscriptosExamen(){
        $this->oConnection->select('COUNT(examenes_estado_academico.codigo) as cant_inscripto');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('examenes', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $this->codigo);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getCursoInscripos() {
        $this->oConnection->select('general.cursos.codigo, general.cursos.nombre_es, general.cursos.nombre_in, general.cursos.nombre_pt');
        $this->oConnection->from('examenes');
        $this->oConnection->join('comisiones', 'comisiones.codigo = examenes.cod_comision');
        $this->oConnection->join('general.planes_academicos', 'general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $this->oConnection->join('general.cursos', 'general.cursos.codigo = general.planes_academicos.cod_curso');
        $this->oConnection->where('examenes.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getDatosInscribirExamen() {
        $this->oConnection->select('general.materias.nombre_es, general.materias.nombre_pt, general.materias.nombre_pt, examenes.fecha, examenes.hora');
        $this->oConnection->from('examenes');
        $this->oConnection->join('general.materias', 'general.materias.codigo = examenes.materia');
        $this->oConnection->where('examenes.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getAlumnosInscribirExamen($codmateria) {
        $nombreApellido = formatearNomApeQuery();
        $this->oConnection->select('examenes_estado_academico.cod_estado_academico', false);
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $this->codigo);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $subquery = $this->oConnection->return_query();
        $this->oConnection->resetear();
      
        $this->oConnection->select('matriculas.codigo');
        $this->oConnection->select("CONCAT($nombreApellido) AS nombre_apellido", false);
        $this->oConnection->select('estadoacademico.estado');
        $this->oConnection->select('estadoacademico.codigo as cod_estado_academico');

        $this->oConnection->from('alumnos');
        $this->oConnection->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo');
        $this->oConnection->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $this->oConnection->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');

        $this->oConnection->where('estadoacademico.codmateria', $codmateria);
        $this->oConnection->where("((estadoacademico.estado = 'libre') OR (estadoacademico.estado = 'regular') OR (estadoacademico.estado = 'cursando')OR (estadoacademico.estado = 'ausente') OR (estadoacademico.estado = 'desaprobado'))");
        $this->oConnection->where("estadoacademico.codigo not in ($subquery)");

        if ($this->tipoexamen == 'PARCIAL' || $this->tipoexamen == 'RECUPERATORIO_PARCIAL') {
            $this->oConnection->select('comisiones.nombre as nomComision');
            $this->oConnection->from('comisiones');
            $this->oConnection->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_comision = comisiones.codigo');
            $this->oConnection->where('matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
            $this->oConnection->where('matriculas_inscripciones.baja', 0);
        } else {
            $this->oConnection->select('(SELECT comisiones.nombre from comisiones join matriculas_inscripciones on matriculas_inscripciones.cod_comision = comisiones.codigo '
                    . 'WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo AND matriculas_inscripciones.baja = 0) as nomComision');
        }

        $query = $this->oConnection->get();
        //die($this->oConnection->last_query());
        return $query->result_array();
    }

    public function cambioEstadoExamen($cambioEstadoExamen) {
        $this->oConnection->trans_begin();

        $this->baja = $this->baja == 1 ? '0' : '1';

        $estado = $this->guardarExamenes();

        $examenEstadoHistorico = new Vexamenes_estado_historicos($this->oConnection);

        $arrayGuardarEstadoHistorico = array(
            'cod_examen' => $this->codigo,
            'baja' => $this->baja,
            'motivo' => $cambioEstadoExamen['motivo'],
            'fecha_hora' => date("Y-m-d H:i:s"),
            'comentario' => $cambioEstadoExamen['comentario'],
            'cod_usuario' => $cambioEstadoExamen['cod_usuario']
        );
        $examenEstadoHistorico->setExamenes_estado_historicos($arrayGuardarEstadoHistorico);
        $examenEstadoHistorico->guardarExamenes_estado_historicos();
        $estadoTran = $this->oConnection->trans_status();

        if ($estadoTran === false) {
            $this->oConnection->trans_rollback();
        } else {
            $this->oConnection->trans_commit();
        }
        return $estadoTran;
    }

    public function setNotasResultado($notasResultado) {
        $this->oConnection->insert('notas_resultados', $notasResultado);
    }

    public function updateNotasResultados($notasResultado, $cod_inscripto, $tiporesultado) {
        $this->oConnection->where('notas_resultados.cod_inscripcion', $cod_inscripto);
        $this->oConnection->where('notas_resultados.tipo_resultado', $tiporesultado);
        $this->oConnection->update('notas_resultados', $notasResultado);
    }

    public function getSalonesExamen() {
        $this->oConnection->select('examenes_salones.cod_salon, examenes_salones.codigo as cod_examen_salon');
        $this->oConnection->from('examenes_salones');
        $this->oConnection->where('examenes_salones.cod_examen', $this->codigo);
        $this->oConnection->where('examenes_salones.baja', 0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getProfesoresExamen() {
        $this->oConnection->select('examenes_profesor.codprofesor, examenes_profesor.codigo as cod_examen_profesor');
        $this->oConnection->select("concat(profesores.nombre,', ',profesores.apellido) as nombre_profesor",false);
        $this->oConnection->from('examenes_profesor');
        $this->oConnection->join('profesores', 'profesores.codigo = examenes_profesor.codprofesor');
        $this->oConnection->where('examenes_profesor.codexamen', $this->codigo);
        $this->oConnection->where('examenes_profesor.baja', 0);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

//    public function setExamenesComision($cod_comision) {
//        $arrExamen_comision = array(
//            'cod_examen' => $this->codigo,
//            'cod_comision' => $cod_comision
//        );
//        $this->oConnection->insert('examenes_comision', $arrExamen_comision);
//    }

    public function getComisionCursoExamenParcial() {
        $this->oConnection->select('comisiones.codigo as cod_comision,general.planes_academicos.cod_curso');
        $this->oConnection->from('examenes');
        $this->oConnection->join('comisiones', 'comisiones.codigo = examenes.cod_comision');
        $this->oConnection->join('general.planes_academicos', 'general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $this->oConnection->where('examenes.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function cupoRestante() {

        $this->oConnection->select('(examenes.cupo - count(examenes_estado_academico.cod_estado_academico)) as cantRestantesInscribir ');
        $this->oConnection->from('examenes_estado_academico');
        $this->oConnection->join('examenes', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->where('examenes_estado_academico.cod_examen', $this->codigo);
        $this->oConnection->where('examenes_estado_academico.estado <>', 'baja');
        $query = $this->oConnection->get();
        //echo $this->oConnection->last_query();
        return $query->result_array();
    }

    public function unSetsalones() {
        $array = array('baja' => 1);
        $this->oConnection->where('examenes_salones.cod_examen', $this->codigo);
        $this->oConnection->update('examenes_salones', $array);
    }

    public function setSalones($cod_salon) {
        $arrExamenSalon = array(
            'cod_examen' => $this->codigo,
            'cod_salon' => $cod_salon,
            'baja' => 0
        );
        $objExamenSalon = new Vexamenes_salones($this->oConnection);
        $objExamenSalon->setExamenes_salones($arrExamenSalon);
        $objExamenSalon->guardarExamenes_salones();
    }

    public function unSetProfesores() {
        $array = array('baja' => 1);
        $this->oConnection->where('examenes_profesor.codexamen', $this->codigo);
        $this->oConnection->update('examenes_profesor', $array);
    }

    public function setProfesores($codprofesor) {
        $arrExamenProfesor = array(
            'codexamen' => $this->codigo,
            'codprofesor' => $codprofesor,
            'baja' => 0
        );
        $objExamenProf = new Vexamenes_profesor($this->oConnection);
        $objExamenProf->setExamenes_profesor($arrExamenProfesor);
        $objExamenProf->guardarExamenes_profesor();
    }

    static function getExamenesDictarse($conexion, $diasantes = null, $tipoexamen = null) {
        $condiciones = array(
            'fecha >' => date('Y-m-d'),
            'baja' => 0);
        if ($tipoexamen != null) {
            $condiciones['tipoexamen'] = $tipoexamen;
        }
        if ($diasantes != null) {
            $conexion->where('fecha = date_add(curdate(), interval ' . $diasantes . ')');
        }

        return Vexamenes::listarExamenes($conexion, $condiciones);
    }

    static function getVerificarSalonHoraExamen($conexion, $salones, $fecha, $horaInicio, $horaFin) {
        $conexion->select('count(examenes_salones.codigo) as cantSalones, examenes.codigo as cod_examen');
        $conexion->from('examenes');
        $conexion->join('examenes_salones', 'examenes_salones.cod_examen = examenes.codigo');
        $conexion->where_in('examenes_salones.cod_salon', $salones);
        $conexion->where('examenes_salones.baja',0);
        $conexion->where('examenes.fecha', $fecha);
        $conexion->where('examenes.baja',0);
        $conexion->where("((examenes.hora <= '$horaInicio' and examenes.horafin >= '$horaInicio') or (examenes.hora <= '$horaFin' and examenes.horafin >= '$horaFin'))");
        $query = $conexion->get();
        
        return $query->result_array();
    }

    static function listarInscriptosExamen(CI_DB_mysqli_driver $conexion, $cod_examen, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false) {
        $nombreApellido = formatearNomApeQuery();

        $conexion->select('examenes_estado_academico.codigo');
        $conexion->select("CONCAT($nombreApellido) AS nombre_apellido", false);
        $conexion->select('examenes_estado_academico.fechadeinscripcion');
        $conexion->select('examenes.fecha');
        $conexion->select('alumnos.codigo AS cod_alumno');
        $conexion->select("examenes_estado_academico.cod_estado_academico");
        $conexion->select('matriculas.codigo as cod_matricula');
        $conexion->select("(IF ((SELECT COUNT(*) FROM ctacte WHERE ctacte.cod_alumno = alumnos.codigo AND ctacte.pagado < ctacte.importe AND ctacte.fechavenc < CURDATE()) > 0, '".lang('debe_ctacte')."', '".lang('no_debe_ctacte')."')) AS estado", false);
        $conexion->from('examenes_estado_academico');
        $conexion->join('examenes', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $conexion->join('estadoacademico', 'estadoacademico.codigo = examenes_estado_academico.cod_estado_academico');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->where('examenes_estado_academico.cod_examen', $cod_examen);
        $conexion->where('examenes_estado_academico.estado <>', 'baja');
        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_having("$key like '%$value%'");
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        
        //die($conexion->last_query());
        
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        return $arrResp;
    }

    static function listarAlumnosInscribirDataTable(CI_DB_mysqli_driver $conexion, $cod_examen, $tipoExamen, $codmateria, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $comision = false) {
        $nombreApellido = formatearNomApeQuery();
        $conexion->select('examenes_estado_academico.cod_estado_academico', false);
        $conexion->from('examenes_estado_academico');
        $conexion->where('examenes_estado_academico.cod_examen', $cod_examen);
        $conexion->where('examenes_estado_academico.estado <>', 'baja');
        //$conexion->where("examenes_estado_academico.estado IN ('ausente', 'cursando', 'desaprobado')");
        //$conexion->where('examenes_estado_academico.estado = cursando');
        //$conexion->where('examenes_estado_academico.estado = desaprobado');
        $subquery = $conexion->return_query();
        $conexion->resetear();

        $conexion->select('examenes_estado_academico.cod_examen',false);
        $conexion->from('examenes_estado_academico');
        $conexion->join('examenes','examenes.codigo = examenes_estado_academico.cod_examen');
        $conexion->where('examenes.materia',$codmateria);
        $conexion->where('examenes_estado_academico.estado','pendiente');
        $conexion->where('examenes_estado_academico.cod_examen <>',$cod_examen);
        $conexion->where('examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
        $conexion->where('examenes.tipoexamen in("FINAL","RECUPERATORIO_FINAL")');
        $conexion->limit(1,0);
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select('matriculas.codigo as cod_matricula');
        $conexion->select("CONCAT($nombreApellido) AS nombre_apellido", false);
        $conexion->select('estadoacademico.estado');
        $conexion->select('estadoacademico.codigo as cod_estado_academico');
        $conexion->select("IF(($subquery2) <> ' ',1,0) as noPuedeInscribir",false);
        $conexion->from('alumnos');
        $conexion->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo AND matriculas.estado = "habilitada"');
        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');

        $conexion->where('estadoacademico.codmateria', $codmateria);
        $conexion->where("((estadoacademico.estado = 'libre') OR (estadoacademico.estado = 'regular') OR (estadoacademico.estado = 'cursando'))");
        $conexion->where("estadoacademico.codigo not in ($subquery)");

        if ($tipoExamen == 'PARCIAL' || $tipoExamen == 'RECUPERATORIO_PARCIAL') {
            $conexion->select('comisiones.nombre as nomComision');
            $conexion->from('comisiones');
            $conexion->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_comision = comisiones.codigo');
            $conexion->where('matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
            $conexion->where('matriculas_inscripciones.baja', 0);
        } else {
            $conexion->select('(SELECT comisiones.nombre '.
                    'FROM comisiones '.
                    'JOIN matriculas_inscripciones on matriculas_inscripciones.cod_comision = comisiones.codigo '.
                    'WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo '.
                    'AND matriculas_inscripciones.baja = 0 '.
                    'ORDER BY matriculas_inscripciones.codigo DESC '.
                    'LIMIT 0, 1) AS nomComision', false);
            if ($comision) {
                $conexion->select('(SELECT comisiones.codigo '.
                    'FROM comisiones '.
                    'JOIN matriculas_inscripciones on matriculas_inscripciones.cod_comision = comisiones.codigo '.
                    'WHERE matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo '.
                    'AND matriculas_inscripciones.baja = 0 '.
                    'ORDER BY matriculas_inscripciones.codigo DESC '.
                    'LIMIT 0, 1) AS codComision', false);
            }
        }
        
        if ($comision){
            $conexion->having("codComision",$comision);
        }

        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_having("$key like '%$value%'");
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        //die($conexion->last_query());
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        
        return $arrResp;
    }
    
    static function listarAlumnosParcialMateriaComision(CI_DB_mysqli_driver $conexion, $cod_examen,$cod_materia,$cod_comision, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false){        
        $nombreApellido = formatearNomApeQuery();
        $conexion->select('examenes_estado_academico.cod_estado_academico');
        $conexion->from('examenes_estado_academico');
        $conexion->where('examenes_estado_academico.cod_estado_academico = matriculas_inscripciones.cod_estado_academico');
        $conexion->where('examenes_estado_academico.cod_examen',$cod_examen);
        $conexion->where('examenes_estado_academico.estado <>', 'baja');                
        //$conexion->where("((examenes_estado_academico.estado = 'libre') OR (estadoacademico.estado = 'regular') OR (estadoacademico.estado = 'cursando')OR (estadoacademico.estado = 'ausente') OR (estadoacademico.estado = 'desaprobado'))");
        $subquery = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select('matriculas.codigo as cod_matricula');
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido",false);
        $conexion->select('estadoacademico.estado');
        $conexion->select('estadoacademico.codigo as cod_estado_academico');
        $conexion->select('comisiones.nombre as nomComision');
        $conexion->from('matriculas_inscripciones');
        $conexion->join('comisiones','comisiones.codigo = matriculas_inscripciones.cod_comision');
        $conexion->join('estadoacademico','estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico');
        $conexion->join('matriculas_periodos','matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas','matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos','alumnos.codigo = matriculas.cod_alumno');
        $conexion->where('matriculas_inscripciones.cod_comision',$cod_comision);
        $conexion->where('estadoacademico.codmateria',$cod_materia);
        $conexion->where('matriculas_inscripciones.baja',0);
        $conexion->where("matriculas_inscripciones.cod_estado_academico NOT IN ($subquery)");
        
        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_having("$key like '%$value%'");
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        //echo $conexion->last_query();die();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        
        return $arrResp;
        
    }

    static function listarAlumnosInscribirRecuperatorioParcialMateriaComision(CI_DB_mysqli_driver $conexion, $cod_examen,$cod_materia,$cod_comision, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false){
        // Obtenemos el examen padre del examen
        $conexion->select('examenes.codigo_examen_padre');
        $conexion->from('examenes');
        $conexion->where('examenes.codigo', $cod_examen);
        $conexion->limit('1');
        $codigo_examen_padre = $conexion->get();

        if ($codigo_examen_padre !== false) {
            $codigo_examen_padre = $codigo_examen_padre->result_array();

            if (
                is_array($codigo_examen_padre)
                && isset($codigo_examen_padre[0])
                && isset($codigo_examen_padre[0]['codigo_examen_padre'])
                && ctype_digit($codigo_examen_padre[0]['codigo_examen_padre'])
            ) {
                $codigo_examen_padre = $codigo_examen_padre[0]['codigo_examen_padre'];
            }
            else
            {
                $codigo_examen_padre = false;
            }
        }

        // Almacenamos la consulta (solo la consulta SQL) para obtener el codigo de estado academico de los alumnos ya inscriptos.
        $conexion->select('examenes_estado_academico.cod_estado_academico', false);
        $conexion->from('examenes_estado_academico');
        $conexion->where('examenes_estado_academico.cod_examen', $cod_examen);
        $conexion->where('examenes_estado_academico.estado <>', 'baja');
        $subquery_alumnos_inscriptos = $conexion->return_query();
        $conexion->resetear();

        // Se hace esto por retrocompatibilidad
        $codigo_examen = null;
        if ($codigo_examen_padre !== false) {
            $codigo_examen = $codigo_examen_padre;
        }
        else
        {
            $codigo_examen = $cod_examen;
        }

        $nombreApellido = formatearNomApeQuery();
        
        // Subconsulta para obtener los alumnos que no aprobaron el examen padre del recuperatorio
        $conexion->select('examenes_estado_academico.cod_estado_academico');
        $conexion->from('examenes_estado_academico');
        $conexion->where('examenes_estado_academico.cod_estado_academico = matriculas_inscripciones.cod_estado_academico');
        $conexion->where('examenes_estado_academico.cod_examen', $codigo_examen);
        $conexion->where("((examenes_estado_academico.estado = 'reprobado') OR (examenes_estado_academico.estado = 'ausente'))");
        $subquery = $conexion->return_query();
        $conexion->resetear();

        // Consulta para obtener los datos de los alumnos que pueden rendir el recuperatorio
        $conexion->select('matriculas.codigo as cod_matricula');
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select('estadoacademico.estado');
        $conexion->select('estadoacademico.codigo as cod_estado_academico');
        $conexion->select('comisiones.nombre as nomComision');
        $conexion->from('matriculas_inscripciones');
        $conexion->join('comisiones','comisiones.codigo = matriculas_inscripciones.cod_comision');
        $conexion->join('estadoacademico','estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico');
        $conexion->join('matriculas_periodos','matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas','matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos','alumnos.codigo = matriculas.cod_alumno');
        $conexion->where('matriculas_inscripciones.cod_comision', $cod_comision);
        $conexion->where('estadoacademico.codmateria', $cod_materia);
        $conexion->where('matriculas_inscripciones.baja', 0);
        // Se usa subconsulta en WHERE para obtener los alumnos que no aprobaron el examen
        $conexion->where('matriculas_inscripciones.cod_estado_academico IN ('.$subquery.')');
        $conexion->where('matriculas_inscripciones.cod_estado_academico NOT IN ('.$subquery_alumnos_inscriptos.')');
        
        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                $conexion->or_having("$key like '%$value%'");
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        //echo $conexion->last_query();die();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }
        
        return $arrResp;
        
    }
    
    public  function verificarInscripcionAlumno($cod_estado_academico,$materia){
        $this->oConnection->select('*');
        $this->oConnection->from('examenes');
        $this->oConnection->join('examenes_estado_academico','examenes_estado_academico.cod_examen = examenes.codigo');
        $this->oConnection->where('examenes.materia',$materia);
        $this->oConnection->where('examenes_estado_academico.cod_estado_academico',$cod_estado_academico);
        $this->oConnection->where('examenes_estado_academico.estado','pendiente');
        $this->oConnection->where('examenes.codigo <>',  $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

}
