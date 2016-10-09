<?php

/**
 * Class Vmatriculas_incripciones
 *
 * Class  Vmatriculas_incripciones maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmatriculas_inscripciones extends Tmatriculas_inscripciones {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

//    function bajaInscripcion() {
//        $this->baja = 1;
//        $this->guardarMatriculas_incripciones();
//    }
//    static function bajaHorarios($conexion, $arrHorarios) {
//        $where = array();
//        foreach ($arrHorarios as $row) {
//            $where[] = $row["codigo"];
//        }
//        if (count($where) != 0) {
//            $data = array("baja" => "1");
//            $conexion->where_in('codigo', $where);
//            $conexion->update("matriculas_horarios", $data);
//        }
//    }

    static function getInscripcionesMateriaComision($conexion, $comision, $materia, $datosalumno = false, $tipo_examen = '', $cod_examen_padre = null) {
        //$subquery_alumnos_inscriptos = null;
        $subquery_alumnos_desaprobados = null;

        if ($tipo_examen === "RECUPERATORIO_PARCIAL" && !is_null($cod_examen_padre)) {
            // Almacenamos la consulta (solo la consulta SQL) para obtener el codigo de estado academico de los alumnos ya inscriptos.
            /*
            $conexion->select('examenes_estado_academico.cod_estado_academico', false);
            $conexion->from('examenes_estado_academico');
            $conexion->where('examenes_estado_academico.cod_examen', $cod_examen);
            $conexion->where('examenes_estado_academico.estado <>', 'baja');
            $subquery_alumnos_inscriptos = $conexion->return_query();
            $conexion->resetear();
            */

            // Subconsulta para obtener los alumnos que no aprobaron el examen padre del recuperatorio
            $conexion->select('examenes_estado_academico.cod_estado_academico');
            $conexion->from('examenes_estado_academico');
            $conexion->where('examenes_estado_academico.cod_estado_academico = matriculas_inscripciones.cod_estado_academico');
            $conexion->where('examenes_estado_academico.cod_examen', $cod_examen_padre);
            $conexion->where("((examenes_estado_academico.estado = 'reprobado') OR (examenes_estado_academico.estado = 'ausente'))");
            $subquery_alumnos_desaprobados = $conexion->return_query();
            $conexion->resetear();
        }

        $conexion->select("matriculas_inscripciones.*");
        $conexion->select("estadoacademico.codigo as cod_estado_academico");
        $conexion->select("matriculas.codigo as cod_matricula");
        $conexion->from("matriculas_inscripciones");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        if ($datosalumno != false) {
            $conexion->select('alumnos.nombre, alumnos.apellido');
            $conexion->join('matriculas', 'matriculas.codigo = matriculas_inscripciones.cod_matricula');
            $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
            $conexion->where('email IS NOT NULL');
        }
        /*if($tipo_examen == 'RECUPERATORIO_PARCIAL'){
            $conexion->join('examenes_estado_academico', 'examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
            $conexion->where("(SELECT COUNT(*) FROM examenes_estado_academico WHERE examenes_estado_academico.cod_estado_academico = estadoacademico.codigo AND  examenes_estado_academico.estado = 'aprobado') = 0");
        */

        if (
            $tipo_examen == 'RECUPERATORIO_PARCIAL'
            && !is_null($subquery_alumnos_desaprobados)
            //&& !is_null($subquery_alumnos_inscriptos)
        ) {
            //$conexion->join('examenes_estado_academico', 'examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
            $conexion->where("estadoacademico.codigo IN ($subquery_alumnos_desaprobados)"); // se obtienen los alumnos que desaprobaron o se ausentaron en el examen padre
            //$conexion->where("estadoacademico.codigo NOT IN ($subquery_alumnos_inscriptos)"); // se filtran los alumnos que ya estan inscriptos
            $conexion->where("(SELECT COUNT(*) FROM examenes_estado_academico WHERE examenes_estado_academico.cod_estado_academico = estadoacademico.codigo AND  examenes_estado_academico.estado = 'aprobado') = 0");
        }
        
        $conexion->where("matriculas_inscripciones.cod_comision", $comision);
        $conexion->where("estadoacademico.codmateria", $materia);
        $conexion->where("matriculas_inscripciones.baja", 0);
        $conexion->group_by("estadoacademico.cod_matricula_periodo");
        $query = $conexion->get();
        //die($conexion->last_query());
        return $query->result_array();















/*
        $conexion->select("matriculas_inscripciones.*");
        $conexion->select("estadoacademico.codigo as cod_estado_academico");
        $conexion->select("matriculas.codigo as cod_matricula");
        $conexion->from("matriculas_inscripciones");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
        $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
        if ($datosalumno != false) {
            $conexion->select('alumnos.nombre, alumnos.apellido');
            $conexion->join('matriculas', 'matriculas.codigo = matriculas_inscripciones.cod_matricula');
            $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
            $conexion->where('email IS NOT NULL');
        }
        if($tipo_examen == 'RECUPERATORIO_PARCIAL'){
            $conexion->join('examenes_estado_academico', 'examenes_estado_academico.cod_estado_academico = estadoacademico.codigo');
            $conexion->where("(SELECT COUNT(*) FROM examenes_estado_academico WHERE examenes_estado_academico.cod_estado_academico = estadoacademico.codigo
                AND  examenes_estado_academico.estado = 'aprobado') = 0");
        }
        $conexion->where("matriculas_inscripciones.cod_comision", $comision);
        $conexion->where("estadoacademico.codmateria", $materia);
        $conexion->where("matriculas_inscripciones.baja", 0);
        $conexion->group_by("estadoacademico.cod_matricula_periodo");
        $query = $conexion->get();
		//die($conexion->last_query());
        return $query->result_array();
        */
    }

//
//    function refreshHorarioIncripcion(){
//
//        Vmatriculas_inscripciones::refreshInscripcionesHorarios($this->oConnection,  $this->cod_comision,  $this->cod_materia,  $this->cod_matricula);
//
//    }
//
    static function guardarMatriculasHorarios($conexion, $cod_comision, $cod_materia, $cod_usuario) {

        $inscriptos = Vmatriculas_inscripciones::getInscripcionesMateriaComision($conexion, $cod_comision, $cod_materia);

        if (count($inscriptos) > 0) {
            foreach ($inscriptos as $rowInscriptos) {

                Vmatriculas_horarios::alta($conexion, $cod_comision, $cod_materia, $rowInscriptos["cod_estado_academico"], null, '0', $cod_usuario);
            }
        }
    }

//    private function guardarArrayInscripcion($horariosinscribir){
//           foreach ($horariosinscribir as $rowInscribir) {
//               $arrGuardarHorario = array(
//                    "cod_matricula"=>$rowInscribir["cod_matricula"],
//                    "cod_horario"=>$rowInscribir["codigo"],
//                    "baja"=>"0",
//                    "fecha_hora"=>date("Y-m-d"),
//                    "usuario"=>1
//                        );
//                $matricula_horarios = new Vmatriculas_horarios($this->oConnection);
//                $matricula_horarios->setMatriculas_horarios($arrGuardarHorario);
//                $matricula_horarios->guardarMatriculas_horarios();
//           }
//           return true;
//    }
//
//    public function  guardarMatriculasHorario($cod_horario){
//         $arrMatriculaHorario = array(
//             "cod_matricula"=>  $this->cod_matricula,
//             "cod_horario"=>$cod_horario,
//             "baja"=>"0",
//             "fecha_hora"=>date("Y-m-d"),
//             "usuario"=>1
//             );
//         $matricula_horario = new Vmatriculas_incripciones($this->oConnection);
//         $matricula_horario->setMatriculas_incripciones($arrMatriculaHorario);
//         $matricula_horario->guardarMatriculas_incripciones();
//     }

    public function guardar($codestadoaca, $codcomision, $codusuario, $fechadesde = null, $motivo = false) {
        //si hubiese doy de baja todas las inscripciones de la matricula en las materias
        $condiciones = array(
            'cod_estado_academico' => $codestadoaca);

		//Tengo que indicar el motivo por el cual doy de baja en matriculas_inscripciones
        $inscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($this->oConnection, $condiciones);

        foreach ($inscripciones as $row) {
            $inscripciones = new Vmatriculas_inscripciones($this->oConnection, $row["codigo"]);
            $inscripciones->baja($fechadesde, $codusuario, $motivo);
        }

        $this->cod_estado_academico = $codestadoaca;
        $this->cod_comision = $codcomision;
        $this->cod_usuario_creador = $codusuario;
        $this->fecha_hora = date("Y-m-d H:i:s");
        $this->alta($fechadesde, $codusuario);
    }

    public function baja($fechadesde = null, $codusuario = null, $motivo = false) {
        $fechadesde = $fechadesde == null ? date("Y-m-d") : $fechadesde;
        $this->baja = "1";
		if (!$motivo){
			$this->motivo_baja = $motivo;
		}
        $this->guardarMatriculas_inscripciones();
        $estado = new Vestadoacademico($this->oConnection, $this->cod_estado_academico);

        Vmatriculas_horarios::baja($this->oConnection, $this->cod_comision, $estado->codmateria, $this->cod_estado_academico, $fechadesde,null,null,$motivo);


        return true;
    }

    public function alta($fechadesde = null, $codusuario = null) {
        $fechadesde = $fechadesde == null ? date("Y-m-d") : $fechadesde;
        $this->baja = 0;
        $this->guardarMatriculas_inscripciones();
        $estadoaca = new Vestadoacademico($this->oConnection, $this->cod_estado_academico);
        Vmatriculas_horarios::alta($this->oConnection, $this->cod_comision, $estadoaca->codmateria, $this->cod_estado_academico, $fechadesde, '0', $codusuario);
        $estadoaca->guardarCambioEstado('cursando', $codusuario);
    }

    static function getInscripcionesComision($conexion, $comision, $materia = null, $datosalumno = false, $estadoEstadoAcademico = null) {
        $nombreApellido = formatearNomApeQuery();
        $conexion->select("matriculas_inscripciones.codigo as cod_matricula_inscripcion");
        $conexion->from("matriculas_inscripciones");
        if ($datosalumno != false) {
            $conexion->select("alumnos.codigo as cod_alumno, CONCAT($nombreApellido) as nombre_apellido, alumnos.email",false);
            $conexion->join('estadoacademico', 'estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico');
            $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo');
            //Ticket 4633 -mmori- se agrega condicion para evitar traer matriculas inhabilitadas
            $conexion->join('matriculas', "matriculas.codigo = matriculas_periodos.cod_matricula and matriculas.estado <> 'inhabilitada'");
            $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        }

        $conexion->where("cod_comision", $comision);
        //Fix para ver alumnos dados de baja libre en matriculas incripciones 26-03-2015
        $conexion->where("matriculas_inscripciones.baja", "0");
        $conexion->where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE `matriculas_inscripciones`.`cod_estado_academico` = `estadoacademico`.`codigo` AND `matriculas_inscripciones`.`baja` = '0' ORDER BY `matriculas_inscripciones`.`codigo` DESC LIMIT 1) = ", $comision);

        //$conexion->where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE  matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND `matriculas_inscripciones`.`baja` = '0' AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ", $comision);

        //$conexion->or_where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo  AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ", $comision);
        //$conexion->having("count(*) > 2");
        //$conexion->where("NOT (SELECT matriculas_periodos.estado FROM matriculas_periodos WHERE matriculas_periodos.cod_tipo_periodo > 1 AND matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.estado NOT IN ('migrado', 'inhabilitada'))");
        //$conexion->where("comisiones.codigo = ", $comision);

        if ($materia != null) {
            $conexion->where("estadoacademico.codmateria", $materia);

           // $conexion->group_by('matriculas_inscripciones.cod_comision');
        }
            $conexion->group_by('matriculas.cod_alumno');

        if ($estadoEstadoAcademico != null){

            $conexion->where_in("estadoacademico.estado", $estadoEstadoAcademico);
        }

        $conexion->or_where("(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo  AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ", $comision);
        $conexion->where("NOT (SELECT matriculas_periodos.estado FROM matriculas_periodos WHERE matriculas_periodos.cod_tipo_periodo > 1 AND matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.estado NOT IN ('migrado', 'inhabilitada'))", NULL, FALSE);



        $query = $conexion->get();
        return $query->result_array();
    }
}
