<?php

/**
 * Class Vestadoacademico
 *
 * Class  Vestadoacademico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vestadoacademico extends Testadoacademico {

    static private $estadoNoCursado = 'no_curso';
    static private $estadoCursando = 'cursando';
    static private $estadoRegular = 'regular';
    static private $estadoAprobado = 'aprobado';
    static private $estadoHomologado = 'homologado';
    static private $estadoRecursa = 'recursa';
    static private $estadoLibre = 'libre';
    private static $Estados = array(
        array('codigo' => 'no_curso', 'nombre' => 'no_curso'),
        array('codigo' => 'cursando', 'nombre' => 'cursando'),
        array('codigo' => 'regular', 'nombre' => 'regular'),
        array('codigo' => 'aprobado', 'nombre' => 'aprobado'),
        array('codigo' => 'homologado', 'nombre' => 'homologado'),
        array('codigo' => 'recursa', 'nombre' => 'recursa'),
        array('codigo' => 'libre', 'nombre' => 'libre'),
    );

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public static function getEstadosMaterias($index = false, $codigo = null) {

        if ($codigo != null) {
            foreach (self::$Estados as $key => $estado) {
                if ($estado['codigo'] == $codigo) {
                    return self::$Estados[$key];
                }
            }
        }
        return $index !== false ? self::$Estados[$index] : self::$Estados;
    }

    public function AceptaCambioEstado($estadoCambiar) {
        $estados = array();
        switch ($this->estado) {
            case 'no_curso':
                $estados[] = $this->getEstadosMaterias(null, 'homologado');
                $estados[] = $this->getEstadosMaterias(null, 'recursa');
                $porcAsistencia = Vconfiguracion::getValorConfiguracion($this->oConnection, null, 'PorcentajeAsistenciaRegular');
                if ($this->porcasistencia >= $porcAsistencia) {
                    $estados[] = $this->getEstadosMaterias(null, 'regular');
                } else {
                    $estados[] = $estados[] = $this->getEstadosMaterias(null, 'libre');
                }
                $condiciones = array('cod_estado_academico' => $this->codigo, 'baja' => 0);
                $inscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($this->oConnection, $condiciones);
                if (count($inscripciones) > 0) {
                    $estados[] = $this->getEstadosMaterias(null, 'cursando');
                }
                break;
                
            case 'cursando':                
                $estados[] = $this->getEstadosMaterias(null, 'homologado');
                $estados[] = $this->getEstadosMaterias(null, 'recursa');
                $estados[] = $this->getEstadosMaterias(null, 'regular');
                $estados[] = $this->getEstadosMaterias(null, 'libre');
                $estados[] = $this->getEstadosMaterias(null, 'no_curso');
                break;
                
            case 'regular':
                $condiciones = array('cod_estado_academico' => $this->codigo, 'baja' => 0);
                $inscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($this->oConnection, $condiciones);
                if (count($inscripciones) > 0) {
                    $estados[] = $this->getEstadosMaterias(null, 'cursando');
                } else {
                    $estados[] = $this->getEstadosMaterias(null, 'no_curso');
                }
                $estados[] = $this->getEstadosMaterias(null, 'aprobado');
                $estados[] = $this->getEstadosMaterias(null, 'recursa');
                break;
                
            case 'homologado':
                $objmatriculaper = new Vmatriculas_periodos($this->oConnection, $this->cod_matricula_periodo);
                $arrcertificados = $objmatriculaper->getCertificadosProcesados();
                if (count($arrcertificados) < 1) {// se puede cambiar el estado porque no tiene certificados procesados
                    $estados[] = $this->getEstadosMaterias(null, 'no_curso');
                }
                break;
                
            case 'aprobado':
                $estados[] = $this->getEstadosMaterias(null, self::$estadoCursando);
                $objmatriculaper = new Vmatriculas_periodos($this->oConnection, $this->cod_matricula_periodo);
                $arrcertificados = $objmatriculaper->getCertificadosProcesados();
                if (count($arrcertificados) < 1) {// se puede cambiar el estado porque no tiene certificados procesados
                    $estados[] = $this->getEstadosMaterias(null, 'regular');
                }
                break;
                
            case 'libre':
                $estados[] = $this->getEstadosMaterias(null, 'no_curso');
                $estados[] = $this->getEstadosMaterias(null, 'homologado');
                $estados[] = $this->getEstadosMaterias(null, 'recursa');
                $estados[] = $this->getEstadosMaterias(null, 'aprobado');
                break;
            
            default:
                break;
        }
        foreach ($estados as $estadoposible) {
            if ($estadoposible['codigo'] == $estadoCambiar) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function guardarCambioEstado($estado, $codusuario, $codmotivo = null, $comentario = null) {
        if ($this->estado != $estado) {
            if ($this->AceptaCambioEstado($estado)) {
                $estadooriginal = $this->estado;
                $this->estado = $estado;
                $respuesta = $this->guardarEstadoacademico();
                $estadohistorico = new Vacademico_estado_historico($this->oConnection);
                $estadohistorico->guardar($this->codigo, $codusuario, $this->estado, $codmotivo, $comentario);
                switch ($estado) 
                {
                    case Vestadoacademico::getEstadoNoCursado():
                    case Vestadoacademico::getEstadoHomologado():
                    case Vestadoacademico::getEstadoLibre():
                        $this->bajaInscripciones($codusuario);
						$this->bajaInscripciones($codusuario, "cambio_estadoacademico");
                        break;
                    
                    case Vestadoacademico::getEstadoRecursa():
                        $this->bajaInscripciones($codusuario);
						$this->bajaInscripciones($codusuario, "cambio_estadoacademico");
                        $nuevoestado = new Vestadoacademico($this->oConnection);
                        $nuevoestado->guardar($this->cod_matricula_periodo, $this->codmateria, Vestadoacademico::getEstadoNoCursado(), null, null, $codusuario);
                        
                    default:                        
                        break;
                }
                if(($estadooriginal == Vestadoacademico::getEstadoAprobado() && $estado != Vestadoacademico::getEstadoHomologado()) 
                        || ($estadooriginal == Vestadoacademico::getEstadoHomologado() && $estado !=  Vestadoacademico::getEstadoAprobado())) 
                {
                    $objmatper = new Vmatriculas_periodos($this->oConnection, $this->cod_matricula_periodo);
                    if($objmatper->estado == Vmatriculas_periodos::getEstadoFinalizada()) 
                    {
                        $objmatper->alta($codmotivo, $comentario, $codusuario);
                    }
                }
            } else {
                $respuesta = array(
                                "codigo" => 0,
                                "msgerror" => lang('estado_academico_error2')
                            );
            }
        } else {
            $respuesta = array(
                            "codigo" => 2,
                            "msgerror" => lang('estado_academico_error1')
                        );
        }
        return $respuesta;
    }

    static function getEstadoacademicoPeriodo(CI_DB_mysqli_driver $conexion, $codmatricula, $periodo, $estado = null) {
        $conexion->select('estadoacademico.*');
        $conexion->from('estadoacademico');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->where('matriculas_periodos.cod_tipo_periodo', $periodo);
        $conexion->where('matriculas_periodos.cod_matricula', $codmatricula);
        if($estado != null) {
            $conexion->where('estadoacademico.estado', $estado);
        }
        $query = $conexion->get();
        $resultado = $query->result_array();
        return $resultado;
    }

    public function guardar($codmatriculaper, $codmateria, $estado = null, $fecha = null, $porcasistencia = null, $cod_usuario = null, $codcomision = null) {
        $this->cod_matricula_periodo = $codmatriculaper;
        $this->codmateria = $codmateria;
        $this->estado = $estado != null ? $estado : 'no_curso';
        $this->fecha = $fecha == null ? date('Y-m-d') : $fecha;
        $this->porcasistencia = $porcasistencia != null ? $porcasistencia : 0;
        $this->cursado = 1;
        $this->guardarEstadoacademico();

        if ($codcomision != null) {
            $comision = new Vcomisiones($this->oConnection, $codcomision);
            $fechadesde = $comision->getFechaInicio($this->codmateria);
            $matriculainsc = new Vmatriculas_inscripciones($this->oConnection);
            $matriculainsc->guardar($this->getCodigo(), $codcomision, $cod_usuario, $fechadesde);
        }
    }

    static function getEstadosAcademicos(CI_DB_mysqli_driver $conexion, $condiciones = null, $where_in = null) {
        if ($where_in != null) {
            foreach ($where_in as $row) {
                $conexion->where_in($row['campo'], $row['valores']);
            }
        }
        return Vestadoacademico::listarEstadoacademico($conexion, $condiciones);
    }

    public function getExamenAprobo() {
        $this->oConnection->select('examenes.*, notas_resultados.*');
        $this->oConnection->from('examenes');
        $this->oConnection->join('examenes_estado_academico', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->join('notas_resultados', 'notas_resultados.cod_inscripcion = examenes_estado_academico.codigo');
        $this->oConnection->where('examenes_estado_academico.cod_estado_academico', $this->codigo);
        $this->oConnection->where('examenes.baja', 0);
        $this->oConnection->where('examenes_estado_academico.estado', 'aprobado');
        $this->oConnection->where('notas_resultados.tipo_resultado', 'definitivo');
        $this->oConnection->where('(examenes.tipoexamen = "FINAL" OR examenes.tipoexamen = "RECUPERATORIO_FINAL")');
        $this->oConnection->order_by('examenes_estado_academico.codigo', 'desc');
        $query = $this->oConnection->get();

        return $query->result_array();
    }

    /**
     * Inscribe un estado academico a  una comision,
     * @access public
     * @param 
     * @return 
     */
    public function inscribirComision($cod_comision, $codusuario, $fechadesde = null, $motivo = false) {

        $inscripciones = new Vmatriculas_inscripciones($this->oConnection);
        $inscripciones->guardar($this->codigo, $cod_comision, $codusuario, $fechadesde, $motivo);
    }

    public function bajaInscripciones($codusuario = null, $motivo = false) {
        $resp = true;
        //baja matriculas inscripciones
        $condiciones = array('cod_estado_academico' => $this->codigo, 'baja' => 0);
        $inscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($this->oConnection, $condiciones);
        foreach ($inscripciones as $inscripcion) {
            $matinscripcion = new Vmatriculas_inscripciones($this->oConnection, $inscripcion['codigo']);
            $resp = $resp && $matinscripcion->baja(null, $codusuario);

        }
        if ($this->estado == 'cursando') {
            $this->guardarCambioEstado('no_curso', $codusuario);
        }
        //baja de matriculas_horarios
        $this->oConnection->where("cod_estado_academico", $this->codigo);
        $this->oConnection->where("baja", 0);
        $this->oConnection->where("estado IS NULL", null, false);
		
		$cambios = array("baja" => 1);
		if($motivo){
			$cambios["motivo_baja"] = $motivo;
		}
		
		$resp = $resp && $this->oConnection->update('matriculas_horarios', $cambios);

        return $resp;
    }

    public function getExamenesFinales() {
        $this->oConnection->select('examenes.*');
        $this->oConnection->from('examenes');
        $this->oConnection->join('examenes_estado_academico', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $this->oConnection->where('examenes_estado_academico.cod_estado_academico', $this->codigo);
        $this->oConnection->where('examenes.baja', 0);
        $this->oConnection->where('(examenes.tipoexamen = "FINAL" OR examenes.tipoexamen = "RECUPERATORIO_FINAL")');
        $this->oConnection->order_by('examenes_estado_academico.codigo', 'desc');
        $query = $this->oConnection->get();

        return $query->result_array();
    }

    static function getEstadosAcademicoInscripciones(CI_DB_mysqli_driver $conexion, $condiciones = null) {
        $conexion->select('estadoacademico.*');
        $conexion->from('estadoacademico');
        $conexion->join('matriculas_inscripciones', 'matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo');
        $conexion->join('comisiones', 'matriculas_inscripciones.cod_comision = comisiones.codigo');
        if ($condiciones != null) {
            $conexion->where($condiciones);
        }
        $conexion->group_by('estadoacademico.codigo');
        $query = $conexion->get();

        return $query->result_array();
    }

    //si se cambia algun parametro de arrcondiciones o $arrWhereIn cambiar en la consulta que cuenta los registros (contarConsultaEstadoAcademico )
    static function listarEstadoAcademicoDataTable(CI_DB_mysqli_driver $conexion, $separador, array $arrCondiciones = null, array $arrCondindicioneslike = null, array $arrLimit = null, array $arrSort = null, 
            $contar = false, $idioma = "es", array $arrWhereIn = null, $codCurso = null, $codMateria = null, $fechaDesde = null, $fechaHasta = null, $comision = null) {
        $nombreApellido = formatearNomApeQuery();        
        
        $conexion->select('count(*)');
        $conexion->from('matriculas_horarios');
        $conexion->where('matriculas_horarios.cod_estado_academico = estadoacademico.codigo  and baja = 0 and estado is null');
        $subQueryAsistenciasSinCargar = $conexion->return_query();
        $conexion->resetear();   
        
        //Obtengo codigo comision
        $conexion->select('cod_comision');
        $conexion->from('matriculas_inscripciones');
        $conexion->where('matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo  and baja = 0');
        $conexion->order_by('codigo', 'DESC');
        $conexion->limit(1);
        $subQueryCodigoComision = $conexion->return_query();
        $conexion->resetear();
        
        //Obtengo nombre comision
        $conexion->select('nombre');
        $conexion->from('comisiones');
        $conexion->where('comisiones.codigo = codigo_comision');
        $subQueryNombreComision = $conexion->return_query();
        $conexion->resetear();

        //Obtengo estado comision
        $conexion->select('estado');
        $conexion->from('comisiones');
        $conexion->where('comisiones.codigo = codigo_comision');
        $subQueryEstadoComision = $conexion->return_query();
        $conexion->resetear();

        
        $conexion->select("estadoacademico.*");
        $conexion->select("matriculas.cod_alumno");
        $conexion->select("CONCAT($nombreApellido) AS alumno_nombre", false);
        $conexion->select("general.cursos.nombre_$idioma AS nombre_curso");
        $conexion->select("general.materias.nombre_$idioma AS nombre_materia");
        $conexion->select("($subQueryAsistenciasSinCargar) as asistenciasSinCargar");
        $conexion->select("($subQueryCodigoComision) as codigo_comision");
        $conexion->select("($subQueryNombreComision) as nombre_comision");
        $conexion->select("($subQueryEstadoComision) as estado_comision");
        //$conexion->select("comisiones.codigo as codigo_comision");
        //$conexion->select("comisiones.nombre as nombre_comision");
        $conexion->from("estadoacademico");
        $conexion->join("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->join("general.cursos", "general.cursos.codigo = general.planes_academicos.cod_curso");
        
        if ($codCurso != null){
            $conexion->where("general.planes_academicos.cod_curso", $codCurso);            
        }
        if ($codMateria != null){
            $conexion->where("estadoacademico.codmateria", $codMateria);
        }
        if ($fechaDesde != null){
            $conexion->where("matriculas.fecha_emision >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("matriculas.fecha_emision <=", $fechaHasta);
        }
        if (count($arrCondindicioneslike) > 0) {
            foreach ($arrCondindicioneslike as $key => $value) {
                if ($key == 'alumno_nombre') {
                    $arrTemp[] = "REPLACE(alumno_nombre, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        
        if ($comision != null){
            $conexion->having("codigo_comision = " . $comision);
        }

        if ($arrCondiciones != null) {
            foreach ($arrCondiciones as $condiciones) {
                $conexion->where($condiciones);
            }
        }

        if ($arrWhereIn != null) {
            foreach ($arrWhereIn as $key => $where_in) {
                $conexion->where_in($key, $where_in);
            }
        }

        $conexion->having("estado_comision","habilitado");
        
        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    static function contarConsultaEstadoAcademico(CI_DB_mysqli_driver $conexion, $arrCondiciones = null, $arrWhereIn = null, $codCurso = null,
            $codMateira = null, $fechaDesde = null, $fechaHasta = null, $comision = null) {
        $conexion->select('count(estadoacademico.codigo) as resultado');
        $conexion->from('estadoacademico');
        if ($fechaDesde != null || $fechaHasta != null || $codCurso != null){
            $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
            $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        }
        if ($codCurso != null){
            $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
            $conexion->where("general.planes_academicos.cod_curso", $codCurso);
        }
        if ($codMateira != null){
            $conexion->where("codmateria", $codMateira);
        }
        if ($fechaDesde != null){
            $conexion->where("matriculas.fecha_emision >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("matriculas.fecha_emision <=", $fechaHasta);
        }
        if ($arrCondiciones != null) {
            foreach ($arrCondiciones as $condiciones) {
                $conexion->where($condiciones);
            }
        }
        if ($arrWhereIn != null) {
            foreach ($arrWhereIn as $key => $where_in) {
                $conexion->where_in($key, $where_in);
            }
        }
        $query = $conexion->get();

        return $query->result_array();
    }

    static function getEstadoNoCursado() {
        return self::$estadoNoCursado;
    }

    static function getEstadoCursando() {
        return self::$estadoCursando;
    }

    static function getEstadoRegular() {
        return self::$estadoRegular;
    }

    static function getEstadoAprobado() {
        return self::$estadoAprobado;
    }

    static function getEstadoHomologado() {
        return self::$estadoHomologado;
    }

    static function getEstadoRecursa() {
        return self::$estadoRecursa;
    }

    static function getEstadoLibre() {
        return self::$estadoLibre;
    }

    public function getHorariosCursar() {
        $conexion = $this->oConnection;
        $conexion->select("matriculas_horarios.codigo as cod_mat_horario, horarios.dia, horarios.horadesde, horarios.horahasta, comisiones.nombre, matriculas_horarios.estado");
        $conexion->from("matriculas_horarios");
        $conexion->join("horarios", "horarios.codigo = matriculas_horarios.cod_horario");
        $conexion->join("comisiones", "comisiones.codigo = horarios.cod_comision");
        $conexion->where("matriculas_horarios.cod_estado_academico", $this->codigo);
        $conexion->where("matriculas_horarios.baja", 0);
        $conexion->where("horarios.baja", 0);
        $conexion->group_by('horarios.dia');
        $query = $conexion->get();
        return $query->result_array();
    }

    public function getEstadoAnterior() {
        $condicion = array('estado <>' => $this->estado,
            'codigo' => $this->codigo);

        $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));

        $arrHistorico = Vestadoacademico::listarEstadoacademico($this->oConnection, $condicion, null, $orden);

        if (count($arrHistorico) > 0) {
            $estado = $arrHistorico[0]['estado'];
        } else {
            $condicion2 = array('cod_estado_academico' => $this->codigo, 'baja' => 0);
            $arrInscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($this->oConnection, $condicion2);

            $estado = count($arrInscripciones) > 0 ? Vestadoacademico::getEstadoCursando() : Vestadoacademico::getEstadoNoCursado();
        }
        return $estado;
    }

    public function getInscripciones($habilitadas = true, $notmodalidad = null) {
        $this->oConnection->select('matriculas_inscripciones.*');
        $this->oConnection->from('matriculas_inscripciones');
        $this->oConnection->where('matriculas_inscripciones.cod_estado_academico', $this->codigo);
        if ($habilitadas) {
            $this->oConnection->where('matriculas_inscripciones.baja', 0);
        }
        if ($notmodalidad != null) {
            $this->oConnection->join('comisiones', 'comisiones.codigo = matriculas_inscripciones.cod_comision');
            $this->oConnection->where_not_in('comisiones.modalidad', $notmodalidad);
        }

        $query = $this->oConnection->get();

        return $query->result_array();
    }

    public static function getEstadoAcademicoDetalles(CI_DB_mysqli_driver $conexion, $idioma = "es", $cicloVencido = null, $matriculaPeriodoEstado = null,
            $matriculaEstado = null, $estadoAcademicoEstado = null, $agruparAlumnos = false, $codEstadoAcademico = null){
        $nombreApellido = formatearNomApeQuery(); // helper alumnos
        $conexion->select("estadoacademico.codigo");
        $conexion->select("concat($nombreApellido) AS alumno_nombre", false);
        $conexion->select("alumnos.codigo AS alumno_codigo");
        $conexion->select("estadoacademico.estado");
        $conexion->select("estadoacademico.porcasistencia");
        $conexion->select("general.materias.nombre_$idioma AS materia_nombre");
        $conexion->select("general.materias.codigo AS materia_codigo");
        $conexion->select("matriculas_periodos.codigo AS matricula_periodo_codigo");
        $conexion->select("matriculas.codigo AS matricula_codigo");
        $conexion->select("comisiones.codigo AS comision_codigo");
        $conexion->select("comisiones.nombre AS comision_nombre");
        $conexion->from("estadoacademico");
        $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo");
        $conexion->join("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision");
        if ($cicloVencido !== null){
            $conexion->join("general.ciclos", "general.ciclos.codigo = comisiones.ciclo");
            if ($cicloVencido){
                $conexion->where("general.ciclos.fecha_fin_ciclo < CURDATE()");
            } else {
                $conexion->where("general.ciclos.fecha_fin_ciclo >= CURDATE()");
                $conexion->where("general.ciclos.fecha_inicio_ciclo <= CURDATE()");
            }
        }
        if ($codEstadoAcademico != null){
            $conexion->where("estadoacademico.codigo", $codEstadoAcademico);
        }
        $complemento = $matriculaPeriodoEstado != null ? "AND matriculas_periodos.estado = '$matriculaPeriodoEstado'" : "";
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo $complemento");
        $complemento = $matriculaEstado != null ? "AND matriculas.estado = '$matriculaEstado'" : "";
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula $complemento");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.materias", "general.materias.codigo = estadoacademico.codmateria");
        if ($estadoAcademicoEstado != null){
            $tipoConsulta = is_array($estadoAcademicoEstado) ? "where_in" : "where";
            $conexion->$tipoConsulta("estadoacademico.estado", $estadoAcademicoEstado);
            $conexion->where("matriculas_inscripciones.baja", "0");
        }
        if ($agruparAlumnos){
            $conexion->group_by("alumnos.codigo");
        }        
        $query = $conexion->get();
        return $query->result_array();
    }    
    
    public function getAsistenciasPendientesCaragar($contar = false){
        $this->oConnection->where("cod_estado_academico = $this->codigo");
        $this->oConnection->where("baja", 0);
        $this->oConnection->where("estado IS NULL", null, false);
        $arrResp = Vmatriculas_horarios::listarMatriculas_horarios($this->oConnection);
        if ($contar){
            return count($arrResp);
        } else {
            return $arrResp;
        }
    }
    
    public function calcular_porcentaje_asistencia(){

            $condiciones = array('matriculas_horarios.cod_estado_academico' => $this->codigo,
                'matriculas_horarios.baja' => 0);
            $horarioscursa = Vmatriculas_horarios::getMatriculasHorariosCursados($this->oConnection, $condiciones);
            $cantclases = count($horarioscursa);
            //$condiciones = array('matriculas_horarios.cod_estado_academico' => $this->codigo);
            $horarioscursa = Vmatriculas_horarios::getMatriculasHorariosCursados($this->oConnection, $condiciones);
            $asistio = 0;
            foreach ($horarioscursa as $rowhorariocursa) {
                if ($rowhorariocursa['estado'] == Vmatriculas_horarios::getEstadoPresente() || $rowhorariocursa['estado'] == Vmatriculas_horarios::getEstadoJustificado()) {
                    $asistio++;
                } else  if ($rowhorariocursa['estado'] == Vmatriculas_horarios::getEstadoMediaFalta()){
                    $asistio = $asistio + 0.5;
                }
            }
            $porcentajeAsistencia = $cantclases != 0 ? ($asistio * 100) / $cantclases : 0;
//            echo '<br>'.$asistio. 'por 100 dividido' . $cantclases.'<br>';
            $this->porcasistencia = $porcentajeAsistencia > 100 ? 100 : $porcentajeAsistencia;
            return  $this->guardarEstadoacademico();

    }
    
    public function getNotasParciales($cod_materia){
        $conexion = $this->oConnection;
        $conexion->select('`notas_resultados`.`nota`');
        $conexion->from('examenes');
        $conexion->join('examenes_estado_academico', 'examenes.codigo = examenes_estado_academico.cod_examen');
        $conexion->join('notas_resultados', 'notas_resultados.cod_inscripcion = examenes_estado_academico.codigo');
        $conexion->where('examenes_estado_academico.cod_estado_academico', $this->codigo);
        $conexion->where('examenes.baja', 0);
        $conexion->where('examenes_estado_academico.estado', 'aprobado');
        $conexion->where('notas_resultados.tipo_resultado', 'definitivo');
        $conexion->where('(examenes.tipoexamen = "PARCIAL" OR examenes.tipoexamen = "RECUPERATORIO_PARCIAL")');
        $conexion->order_by('examenes_estado_academico.codigo', 'desc');
        $query = $conexion->get();
        return $query->result_array();
    }

    public static function getEstadosGrupoDeComision(CI_DB_mysqli_driver $conexion, $grupo_comision) {
        $conexion->select('ea.codigo AS cod_ea');
        $conexion->from('estadoacademico ea');
        $conexion->join('matriculas_inscripciones mi', 'mi.cod_estado_academico = ea.codigo');
        $conexion->join('grupos_comisiones gc', 'gc.cod_comision = mi.cod_comision');
        $conexion->join('`general` . grupos_plataforma_educativa gpe', 'gpe.id = gc.id_grupo');
        $conexion->join('`general` . grupos_plataforma_materias gma', 'gma.id_grupo = gpe.id AND gma.cod_materia = ea.codmateria');
        $conexion->where('gc.cod_comision', $grupo_comision['cod_comision']);
        $conexion->where('gc.id_grupo', $grupo_comision['id_grupo']);
        $conexion->where_in('ea.estado', array('cursando','regular','recursa')); //ver homologado y aprobado
        $query = $conexion->get();
        return $query->result_array();

    }

    public static function setGrupo(CI_DB_mysqli_driver $conexion, $cod_ea, $id_grupo) {
        $conexion->where('codigo', $cod_ea);
        $conexion->update('estadoacademico', array("id_grupo"=>$id_grupo));   
    }

    public static function getEstadosAcadConGrupos(CI_DB_mysqli_driver $conexion, $codigoAlumno = null, $id_grupo = null) {
        $conexion->select("ea.codigo as cod_ea");
        $conexion->select("ea.id_grupo");
        $conexion->select("mat.cod_alumno as cod_alumno");
        $conexion->from('alumnos alu');
        $conexion->join("matriculas mat", "mat.cod_alumno = alu.codigo");
        $conexion->join("matriculas_periodos mp", "mp.cod_matricula = mat.codigo");
        $conexion->join("estadoacademico ea","ea.cod_matricula_periodo = mp.codigo");
        $conexion->where("ea.id_grupo IS NOT NULL", null, false);
        if($codigoAlumno != null) {
            $conexion->where("alu.codigo", $codigoAlumno);
        }
        if($id_grupo != null) {
            $conexion->where("ea.id_grupo", $id_grupo);
        }

        $query = $conexion->get();
        return $query->result_array();
    }

    public static function getGruposEstadosAcademicos(CI_DB_mysqli_driver $conexion, $codigoAlumno) {
        $conexion->select('ea.codigo');
        $conexion->select('ea.id_grupo as grupo_actual');
        $conexion->select('gc.id_grupo');
        $conexion->from('estadoacademico ea');
        $conexion->join('matriculas_periodos mp', 'mp.codigo = ea.cod_matricula_periodo');
        $conexion->join('matriculas m', 'm.codigo = mp.cod_matricula');
        $conexion->join('matriculas_inscripciones mi', 'mi.cod_estado_academico = ea.codigo');
        $conexion->join('grupos_comisiones gc', 'gc.cod_comision = mi.cod_comision');
        $conexion->join('general.grupos_plataforma_educativa gpe', 'gpe.id = gc.id_grupo');
        $conexion->join('general.grupos_plataforma_materias gpm', 'gpm.id_grupo = gpe.id');
        $conexion->where('m.cod_alumno', $codigoAlumno);
        $conexion->where('gpm.cod_materia = ea.codmateria', null, false);
        $conexion->group_by('gc.id_grupo');
        $query = $conexion->get();
        return $query->result_array();
    }
}
