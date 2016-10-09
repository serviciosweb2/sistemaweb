<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. 
 */

/**
 * Description of Tmatriculas_horarios
 *
 * @author ivan
 */
class Vmatriculas_horarios extends Tmatriculas_horarios {

    static private $estadoPresente = "presente";
    static private $estadoAusente = "ausente";
    static private $estadoJustificado = "justificado";
    static private $estadoMediaFalta = "media_falta";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function listarExcepciones(CI_DB_mysqli_driver $conexion, $codigoHorarioInicio = null) {
        $conexion->select("excepciones_matriculas_horarios.*");
        $conexion->select("matriculas_horarios.estado");
        $conexion->from("excepciones_matriculas_horarios");
        $conexion->join("matriculas_horarios", "matriculas_horarios.codigo = excepciones_matriculas_horarios.cod_horario_destino");
        if ($codigoHorarioInicio != null) {
            if (is_array($codigoHorarioInicio)) {
                $conexion->where_in("excepciones_matriculas_horarios.cod_horario_inicio", $codigoHorarioInicio);
            } else {
                $conexion->where("excepciones_matriculas_horarios.cod_horario_inicio", $codigoHorarioInicio);
            }
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getUltimaAsistenciaPasada(CI_DB_mysqli_driver $conexion, $codmateria, $codComision) {
        $conexion->select("COUNT(cod_horario)", false);
        $conexion->from("matriculas_horarios");
        $conexion->where("cod_horario = horarios.codigo");
        $conexion->where("matriculas_horarios.baja", "0");
        $conexion->where("matriculas_horarios.estado IS NOT NULL");
        $sQAsistenciasPasadas = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("dia");
        $conexion->select("($sQAsistenciasPasadas) AS asistencias_pasadas", false);
        $conexion->from("horarios");
        $conexion->where("horarios.cod_comision", $codComision);
        $conexion->where("horarios.cod_materia", $codmateria);
        $conexion->where("horarios.baja", "0");
        $conexion->having("asistencias_pasadas >", "0");
        $sQAsistencias = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("MAX(dia) AS dia");
        $conexion->from("($sQAsistencias) AS asistencias");
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return isset($arrResp[0]['dia']) ? $arrResp[0]['dia'] : null;
    }

    
    static function getAsistenciaComision(CI_DB_mysqli_driver $conexion, $cod_materia, $cod_comision, $fecha_desde = null, 
        $fecha_hasta = null, $nombreConfiguracion = false, $vista = false, $filtar_no_cursa=false, $baja=false) {
        $conexion->select("matriculas_horarios.cod_horario");
        $conexion->select("matriculas_horarios.estado");
        $conexion->select("matriculas_horarios.baja");
        $conexion->select("horarios.dia");
        $conexion->select("horarios.horadesde");
        $conexion->select("horarios.horahasta");
        $conexion->select("estadoacademico.porcasistencia");
        $conexion->select("estadoacademico.estado AS estado_academico", false);
        if ($nombreConfiguracion) {
            $nombreApellido = formatearNomApeQuery();
            $conexion->select("CONCAT($nombreApellido) AS alumno_nombre", false);
        } else {
            $conexion->select("CONCAT(alumnos.apellido, ', ', alumnos.nombre) AS alumno_nombre", false);
        }
        $conexion->select("matriculas_periodos.cod_matricula AS matricula_codigo");
        $conexion->select("horarios.cod_materia");
        $conexion->select("horarios.cod_comision");
        $conexion->select("IF (matriculas_horarios.estado IS NULL, 0, 1) AS pasada", false);
        $conexion->from("matriculas_inscripciones");
        $no_cursa = '';
        if($filtar_no_cursa)
        {
            $no_cursa = " AND estadoacademico.estado <> 'no_curso'";
        }
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico AND estadoacademico.codmateria = $cod_materia{$no_cursa}");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $conexion->join("horarios", "horarios.cod_comision = matriculas_inscripciones.cod_comision AND horarios.cod_materia = estadoacademico.codmateria AND horarios.baja = 0");
        //mmori : cambio LEFT para que anda bien la impresion de asistencias - billete
        $conexion->join("matriculas_horarios", "matriculas_horarios.cod_estado_academico = estadoacademico.codigo AND matriculas_horarios.cod_horario = horarios.codigo");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->where("matriculas_inscripciones.cod_comision", $cod_comision);
        if(!$baja)
            $conexion->where("matriculas_inscripciones.baja = 0");
        $conexion->where("matriculas_periodos.estado = 'habilitada'");
        if ($vista){
            $conexion->where("matriculas_periodos.estado <>", Vmatriculas_periodos::getEstadoInhabilitada());
        }
        if ($fecha_desde != null){
            $conexion->where("horarios.dia >=", $fecha_desde);            
        }
        if ($fecha_hasta != null){
            $conexion->where("horarios.dia <=", $fecha_hasta);
        }
        $conexion->where("(matriculas_horarios.baja IS NULL OR matriculas_horarios.baja = 0)");
        //$conexion->group_by('dia, cod_matricula');
        //$conexion->group_by('matriculas.cod_alumno, matriculas_horarios.cod_horario');
        $conexion->order_by("horarios.dia", "ASC");
        $conexion->order_by("alumno_nombre", "ASC");
        $query = $conexion->get();
//        echo $conexion->last_query(); die();
        return $query->result_array();
    }
    
    static function getAsistencias(CI_DB_mysqli_driver $conexion, $codMateria = null, $codComision = null, $fechaDesde = null, 
            $fechaHasta = null, $nombreConfiguracion = false, $vista = false, $soloCursando = true, $bajaMatriculasHorarios = 0, $validarComisionInscripcion = false) {
        $complementoJoin = '';
        if ($codMateria != null)
            $complementoJoin .= " AND horarios.cod_materia = $codMateria";
        if ($codComision != null)
            $complementoJoin .= " AND horarios.cod_comision = $codComision";
        $complementoEstadoAcademico = $soloCursando ? "AND estadoacademico.estado = 'cursando'" : "";
        if ($validarComisionInscripcion){
            $conexion->select("matriculas_inscripciones.cod_comision");
            $conexion->from("matriculas_inscripciones");
            $conexion->where("matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo");
            $conexion->order_by("baja", "asc");
            $conexion->limit(1);
            $sqComisionInscripcion = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("($sqComisionInscripcion) AS comision_inscripcion", false);
            $conexion->having("comision_inscripcion = cod_comision");
        }
        $conexion->select("matriculas_horarios.codigo");
        $conexion->select("matriculas_horarios.cod_horario");
        $conexion->select("matriculas_horarios.estado");
        $conexion->select("matriculas_horarios.baja");
        $conexion->select("horarios.dia");
        $conexion->select("horarios.horadesde");
        $conexion->select("horarios.horahasta");
        $conexion->select("estadoacademico.porcasistencia");
        $conexion->select("estadoacademico.estado AS estado_academico", false);
        if ($nombreConfiguracion) {
            $nombreApellido = formatearNomApeQuery();
            $conexion->select("CONCAT($nombreApellido) AS alumno_nombre", false);
        } else {
            $conexion->select("CONCAT(alumnos.apellido, ', ', alumnos.nombre) AS alumno_nombre", false);
        }
        $conexion->select("matriculas.codigo AS matricula_codigo");
        $conexion->select("horarios.cod_materia");
        $conexion->select("horarios.cod_comision");
        $conexion->select("IF (matriculas_horarios.estado IS NULL, 0, 1) AS pasada", false);
        $conexion->from("matriculas_horarios");
        $conexion->join("horarios", "horarios.codigo = matriculas_horarios.cod_horario AND horarios.baja = 0 $complementoJoin");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_horarios.cod_estado_academico $complementoEstadoAcademico");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        if ($vista) {
            $conexion->where('matriculas_periodos.estado <>', Vmatriculas_periodos::getEstadoInhabilitada());
        }
        if ($bajaMatriculasHorarios !== null){
            $conexion->where('matriculas_horarios.baja', 0);
        }
        if ($fechaDesde != null)
            $conexion->where("horarios.dia >= ", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("horarios.dia <= ", $fechaHasta);
        $conexion->where("estadoacademico.codigo in (SELECT estadoacademico.codigo FROM estadoacademico 
inner join matriculas_inscripciones on matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo and matriculas_inscripciones.baja = 0
WHERE estadoacademico.codmateria = horarios.cod_materia and matriculas_inscripciones.cod_comision = $codComision)");
        $conexion->order_by("horarios.dia ASC, horarios.codigo ASC");
        $conexion->order_by("alumno_nombre", "ASC");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getInscripcionHorariosComision(CI_DB_mysqli_driver $conexion, $cod_materia, $cod_comision, $dia, $cod_horario = false){
        $conexion->select("matriculas_inscripciones.cod_comision AS comision_inscripcion", false);
        $conexion->select("matriculas_inscripciones.cod_estado_academico");
        $conexion->select("estadoacademico.cod_matricula_periodo");
        $conexion->select("horarios.dia");
        $conexion->select("horarios.horadesde");
        $conexion->select("horarios.horahasta");
        $conexion->select("alumnos.nombre");
        $conexion->select("alumnos.apellido");
        $conexion->select("matriculas_horarios.estado");
        $conexion->select("IFNULL(matriculas_horarios.codigo, -1) AS cod_matricula_horario", false);
        $conexion->select("matriculas_inscripciones.cod_comision AS comisionalumno", false);
        $conexion->select("alumnos.codigo AS codigo_alumno", false);
        $conexion->select("estadoacademico.estado AS estado_academico", false);
        $conexion->select("matriculas_periodos.cod_matricula");
        $conexion->from("matriculas_inscripciones");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico ".
                "AND estadoacademico.codmateria = $cod_materia AND estadoacademico.estado NOT IN ('migrado')");
        $temp = "";
        if ($cod_horario){
            $temp = " AND horarios.codigo = ".$cod_horario;
        }
        $conexion->join("horarios", "horarios.cod_comision = matriculas_inscripciones.cod_comision AND horarios.baja = 0 ".
                "AND horarios.cod_materia = estadoacademico.codmateria AND horarios.dia = '$dia'".$temp);
        
        $conexion->join("matriculas_horarios", "matriculas_horarios.cod_estado_academico = estadoacademico.codigo AND ".
                "matriculas_horarios.cod_horario = horarios.codigo", "left");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND matriculas_periodos.estado = 'habilitada'");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->where("matriculas_inscripciones.cod_comision", $cod_comision);
        $conexion->where("matriculas_inscripciones.baja = 0");
        $conexion->group_by("alumnos.codigo");
        $conexion->order_by("alumnos.apellido", "ASC");
        $conexion->order_by("alumnos.nombre", "ASC");
        $query = $conexion->get();
        //die($conexion->last_query());
        return $query->result_array();        
    }
    
    static function getInscriptosHorarios(CI_DB_mysqli_driver $conexion, $cod_materia = null, $cod_comision = null, $fecha = null, $cod_horario = null, 
            $orden = null, $soloCursando = false, $bajaMatriculasHorarios = 0, $validarComisionInscripcion = false) {
        $complemento = $soloCursando ? " AND estadoacademico.estado = 'cursando'" : "";
        if ($validarComisionInscripcion){
            $conexion->select("matriculas_inscripciones.cod_comision");
            $conexion->from("matriculas_inscripciones");
            $conexion->where("matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo");
            $conexion->order_by("baja", "asc");
            $conexion->limit(1);
            $sqComisionInscripcion = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("($sqComisionInscripcion) AS comision_inscripcion", false);
            $conexion->having("comision_inscripcion = comisionalumno");
            $conexion->where("estadoacademico.estado <>", "recursa");
        }
        $conexion->select('estadoacademico.codigo AS cod_estado_academico');
        $conexion->select('matriculas_periodos.codigo as cod_matricula_periodo');
        $conexion->select('horarios.dia');
        $conexion->select('horarios.horadesde');
        $conexion->select('horarios.horahasta');
        $conexion->select('alumnos.nombre');
        $conexion->select('alumnos.apellido');
        $conexion->select('matriculas_horarios.estado');
        $conexion->select('matriculas_horarios.codigo AS cod_matricula_horario');
        $conexion->select('horarios.cod_comision AS comisionalumno');
        $conexion->select('alumnos.codigo AS codigo_alumno');
        $conexion->select("estadoacademico.estado AS estado_academico");
        $conexion->from('matriculas_horarios');
        $conexion->join('horarios', 'horarios.codigo = matriculas_horarios.cod_horario');
        $conexion->join('estadoacademico', 'estadoacademico.codigo = matriculas_horarios.cod_estado_academico'.$complemento);
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        if ($bajaMatriculasHorarios != null){
            $conexion->where('matriculas_horarios.baja', $bajaMatriculasHorarios);
        }
        $conexion->where('matriculas_periodos.estado <>', Vmatriculas_periodos::getEstadoInhabilitada());

        if ($cod_comision != NULL) {
            $conexion->where('horarios.cod_comision', $cod_comision);
        }

        if ($cod_materia != NULL) {
            $conexion->where('horarios.cod_materia', $cod_materia);
        }

        if ($fecha != NULL) {
            $conexion->where('horarios.dia', $fecha);
        }

        if ($cod_horario != NULL) {
            $conexion->where('horarios.codigo', $cod_horario);
        }

        if ($orden != NULL) {
            $conexion->order_by($orden['campo'], $orden['valor']);
        }

        $conexion->group_by('matriculas_periodos.codigo');
        $conexion->order_by('matriculas_periodos.codigo', 'asc');

        $query = $conexion->get();
        
//        echo $conexion->last_query();exit();

        return $query->result_array();
    }

    static function baja(CI_DB_mysqli_driver $conexion, $cod_comision = null, $cod_materia = null, $cod_estado_academico = null, $fecha_desde = null, $bajahorario = null, $cod_horario = null, $motivo= false) {
        //Aca tengo que indicar el motivo por el cual lo doy de baja
		if ($cod_horario == null) {
            $condiciones = array('cod_comision' => $cod_comision,
                'cod_materia' => $cod_materia);
        } else {
            $condiciones = array('codigo' => $cod_horario);
        }

        if ($fecha_desde != null) {
            $condiciones['dia >='] = $fecha_desde;
        }
        if ($bajahorario != null) {
            $condiciones['baja'] = $bajahorario;
        }

        $horarios = Vhorarios::listarHorarios($conexion, $condiciones);
        $arrmatbaja = array();

        if (count($horarios) > 0) {
            $condicioneshorarios = array();
            $condicionesmathor = array();

            for ($i = 0; $i < count($horarios); $i++) {
                $condicioneshorarios[] = $horarios[$i]['codigo'];
            }
            $arrwherein = array(array('campo' => 'cod_horario', 'valores' => $condicioneshorarios));

            if ($cod_estado_academico != null) {
                $condicionesmathor = array('cod_estado_academico' => $cod_estado_academico);
            }

            $arrmatbaja = Vmatriculas_horarios::getMatriculasHorarios($conexion, $condicionesmathor, $arrwherein);

            foreach ($arrmatbaja as $row) {
                $objmathor = new Vmatriculas_horarios($conexion, $row['codigo']);
                $objmathor->bajaMatriculaHorario($motivo);
                $objmathor->bajaExcepciones();
            }
        }
    }

    static function alta(CI_DB_mysqli_driver $conexion, $cod_comision = null, $cod_materia = null, $cod_estado_academico = null, $fecha_desde = null, $bajahorario = null, $cod_usuario = null) {

        $condiciones = array('horarios.cod_comision' => $cod_comision,
            'horarios.cod_materia' => $cod_materia);

        if ($fecha_desde != null) {
            $condiciones['horarios.dia >='] = $fecha_desde;
        }
        if ($bajahorario != null) {
            $condiciones['horarios.baja'] = $bajahorario;
        }

        $horarios = Vhorarios::getHorariosAltaMatricula($conexion, $condiciones, $cod_estado_academico);

        $datos = array();
        if (count($horarios) > 0) {

            foreach ($horarios as $horario) {
                $datos[] = array('cod_estado_academico' => $cod_estado_academico,
                    'cod_horario' => $horario['codigo'],
                    'fecha_hora' => date('Y-m-d H:i:s'),
                    'usuario' => $cod_usuario,
                    'baja' => '0');
            }

            $conexion->insert_batch('matriculas_horarios', $datos);
        }
    }

    public function cambiarEstado($estado) {
        $this->estado = $estado;
        $this->guardarMatriculas_horarios();
    }

    public function getDetallesAsistencias($cod_comision, $cod_materia, $codestadoacademico, $fecha) {
        $this->oConnection->select('horarios.dia, matriculas_horarios.estado');
        $this->oConnection->from('horarios');
        $this->oConnection->join('matriculas_horarios', 'matriculas_horarios.cod_horario = horarios.codigo');
        $this->oConnection->where('horarios.dia <', $fecha);
        $this->oConnection->where('horarios.cod_comision', $cod_comision);
        $this->oConnection->where('horarios.cod_materia', $cod_materia);
        $this->oConnection->where('matriculas_horarios.cod_estado_academico', $codestadoacademico);
        $this->oConnection->order_by('horarios.dia', 'desc');
        $this->oConnection->limit(4);
        $query = $this->oConnection->get();

        return $query->result_array();
    }

    function bajaMatriculaHorario($motivo = false) {
        if ($this->estado == NULL) {
            $this->baja = "1";
			$this->motivo_baja = $motivo ? $motivo : null;
            $this->guardarMatriculas_horarios();
        }
    }

    function altaMatriculaHorario($codestadoacademico, $codhorario, $codusuario, $baja = 0, $fechahora = null, $estado = null) {
        $this->cod_estado_academico = $codestadoacademico;
        $this->cod_horario = $codhorario;
        $this->usuario = $codusuario;
        $this->baja = $baja;
        $this->fecha_hora = $fechahora != null ? $fechahora : date('Y-m-d');
        $this->estado = $estado;
        $this->guardarMatriculas_horarios();
    }

    static function getMatriculasHorarios(CI_DB_mysqli_driver $conexion, $condiciones = null, $where_in = null) {

        if ($where_in != null) {
            foreach ($where_in as $value) {
                $conexion->where_in($value['campo'], $value['valores']);
            }
        }

        return Vmatriculas_horarios::listarMatriculas_horarios($conexion, $condiciones);
    }

    function getExcepciones() {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from('excepciones_matriculas_horarios');
        $conexion->where('cod_horario_inicio', $this->codigo);
        $conexion->or_where('cod_horario_destino', $this->codigo);
        $query = $conexion->get();
        return $query->result_array();
    }

    function bajaExcepciones() {
        $conexion = $this->oConnection;
        $conexion->where('cod_horario_inicio', $this->codigo);
        $conexion->or_where('cod_horario_destino', $this->codigo);
        $conexion->delete('excepciones_matriculas_horarios');
    }

    function guardaExcepcion($codnuevohorario) {
        $datos = array('cod_horario_inicio' => $this->codigo,
            'cod_horario_destino' => $codnuevohorario);
        $this->oConnection->insert('excepciones_matriculas_horarios', $datos);
    }

    function getInscriptos($wherein = null, $orden = null) {
        $conexion = $this->oConnection;
        $conexion->select('matriculas_horarios.codigo as cod_matricula_horario, alumnos.*');
        $conexion->from('alumnos');
        $conexion->join('matriculas', 'matriculas.cod_alumno = alumnos.codigo');
        $conexion->join('matriculas_periodos', 'matriculas_periodos.cod_matricula= matricula.codigo');
        $conexion->join('estadoacademico', 'estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo');
        $conexion->join('matriculas_horarios', 'matriculas_horarios.cod_estado_academico = estadoacademico.codigo');
        $conexion->where('matriculas_horarios.baja', 0);

        if ($orden != NULL) {
            $conexion->order_by($orden['campo'], $orden['valor']);
        }

        if ($wherein != NULL) {
            $conexion->where_in('matriculas_horarios.codigo', $wherein);
        } else {
            $conexion->where('matriculas_horarios.codigo', $this->codigo);
        }
        $conexion->group_by('alumnos.codigo');

        $query = $conexion->get();
        return $query->result_array();
    }

    static function getMatriculasHorariosCursados(CI_DB_mysqli_driver $conexion, $condiciones = null) {
        $conexion->select('matriculas_horarios.*, horarios.dia, horarios.cod_comision');
        $conexion->from('matriculas_horarios');
        $conexion->join('horarios', 'horarios.codigo = matriculas_horarios.cod_horario');
        $conexion->where($condiciones);
        $conexion->where('horarios.dia <=', date('Y-m-d'));
        $conexion->group_by('matriculas_horarios.cod_horario');
        //$conexion->group_by('horarios.cod_comision');
        //$conexion->group_by('horarios.horadesde');
        //$conexion->group_by('horarios.cod_salon');
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getComisionesSinAsistencia(CI_DB_mysqli_driver $conexion, $fechahasta, $contar = false, $fechadesde = null, $habilitadas = false) {
        $conexion->select('matriculas_horarios.*');
        $conexion->from('matriculas_horarios');
        $conexion->join('horarios', 'horarios.codigo = matriculas_horarios.cod_horario');
        if ($habilitadas){
            $conexion->join('comisiones', 'comisiones.codigo = horarios.cod_comision');
            $conexion->where('comisiones.estado', 'habilitado');
        }
        $conexion->where('matriculas_horarios.baja', 0);
        $conexion->where('matriculas_horarios.estado IS NULL');
        $conexion->where("horarios.dia < '$fechahasta'");
        if ($fechadesde != null) {
            $conexion->where("horarios.dia > '$fechadesde'" );
        }
        $conexion->group_by('horarios.cod_comision');
        $query = $conexion->get();
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    static public function getEstadoPresente(){
        return self::$estadoPresente;
    }
    
    static public function getEstadoAusente(){
        return self::$estadoAusente;
    }
    
    static public function getEstadoJustificado(){
        return self::$estadoJustificado;
    }
    
    static public function getEstadoMediaFalta(){
        return self::$estadoMediaFalta;
    }
    
	//siwakawa
    static public function isAsistenciasAlumnoCargadas($conexion, $cod_estadoacademico){
        $conexion->select('COUNT(*) as n');
        $conexion->from('matriculas_horarios');
        $conexion->join('horarios', 'matriculas_horarios.cod_horario = horarios.codigo');
		$conexion->where('matriculas_horarios.baja = 0');
        $conexion->where('cod_estado_academico = '.$cod_estadoacademico);
        $conexion->where('horarios.dia < NOW()');
        $conexion->where('matriculas_horarios.estado <> "NULL"');
        
		$query = $conexion->get();
        $a1 = $query->result_array();
		
        $conexion->select('COUNT(*) as n');
        $conexion->from('matriculas_horarios');
        $conexion->join('horarios', 'matriculas_horarios.cod_horario = horarios.codigo');
		$conexion->where('matriculas_horarios.baja = 0');
        $conexion->where('cod_estado_academico = '.$cod_estadoacademico);
        $conexion->where('horarios.dia < NOW()');
        
		$query = $conexion->get();
        $a2 = $query->result_array();
		
        if($a2[0]['n'] > $a1[0]['n']){
            return false; 
        }
        return true;
    }


    static function getInscripcionHorariosComision2(CI_DB_mysqli_driver $conexion, $cod_materia, $cod_comision, $dia, $cod_horario = false){
        $conexion->select("matriculas_inscripciones.cod_comision AS comision_inscripcion", false);
        $conexion->select("matriculas_inscripciones.cod_estado_academico");
        $conexion->select("estadoacademico.cod_matricula_periodo");
        $conexion->select("horarios.dia");
        $conexion->select("horarios.horadesde");
        $conexion->select("horarios.horahasta");
        $conexion->select("alumnos.nombre");
        $conexion->select("alumnos.apellido");
        $conexion->select("matriculas_horarios.estado");
        $conexion->select("IFNULL(matriculas_horarios.codigo, -1) AS cod_matricula_horario", false);
        $conexion->select("matriculas_inscripciones.cod_comision AS comisionalumno", false);
        $conexion->select("alumnos.codigo AS codigo_alumno", false);
        $conexion->select("estadoacademico.estado AS estado_academico", false);
        $conexion->select("matriculas_periodos.cod_matricula");
        $conexion->from("matriculas_inscripciones");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico ".
                "AND estadoacademico.codmateria = $cod_materia AND estadoacademico.estado NOT IN ('migrado')");
        $temp = "";
        if ($cod_horario){
            $temp = " AND horarios.codigo = ".$cod_horario;
        }
        $conexion->join("horarios", "horarios.cod_comision = matriculas_inscripciones.cod_comision AND horarios.baja = 0 ".
                "AND horarios.cod_materia = estadoacademico.codmateria AND horarios.dia = '$dia'".$temp);
        
        $conexion->join("matriculas_horarios", "matriculas_horarios.cod_estado_academico = estadoacademico.codigo AND ".
                "matriculas_horarios.cod_horario = horarios.codigo", "left");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND matriculas_periodos.estado = 'habilitada'");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->where("matriculas_inscripciones.cod_comision", $cod_comision);
        $conexion->where("matriculas_horarios.baja", "0");
        $conexion->group_by("alumnos.codigo");
        $conexion->order_by("alumnos.apellido", "ASC");
        $conexion->order_by("alumnos.nombre", "ASC");
        $query = $conexion->get();
        return $query->result_array();        
    }

}
