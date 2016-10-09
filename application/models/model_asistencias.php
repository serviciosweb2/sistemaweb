<?php

Class Model_asistencias extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getArrayEstadoAsistencias() {
        $asistencias = Vasistencias::getArrayEstadoAsistencias();

        return $asistencias;
    }
    
    public function actualizar_estadoacademico($arrPost, $cod_usuario){
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $estadosacademicos = array();
        
        foreach ($arrPost as $alumno) {
            $codEstadoAcademico = $alumno['cod_estado_academico'];
            $myEstadoAcademcio = new Vestadoacademico($conexion, $codEstadoAcademico);
            $myEstadoAcademcio->calcular_porcentaje_asistencia();
            $porcentaje = $myEstadoAcademcio->porcasistencia;

            if ($porcentaje >= 80){
                $resp = $myEstadoAcademcio->guardarCambioEstado(Vestadoacademico::getEstadoRegular(), $this->session->userdata('codigo_usuario'));
            }
            else{
                $resp = $myEstadoAcademcio->guardarCambioEstado(Vestadoacademico::getEstadoLibre(), $this->session->userdata('codigo_usuario'));
            }
            if($resp != true)
                return $resp; 
        }
        
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
        
    }

    public function guardarAsistencias($arrPost, $fecha, $cod_usuario, $codEstadoAcademico = null, $codComision = null, $codMateria = null, $asistencias = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $estadosacademicos = array();
        
        foreach ($arrPost as $alumno) {
            $cod_matHorario = $alumno['cod_matricula_horario'];
            $objMatHorarios = new Vmatriculas_horarios($conexion, $cod_matHorario);
            if ($objMatHorarios->getCodigo() == -1){
                $arrHorarios = Vhorarios::listarHorarios($conexion, array("cod_comision" => $codComision, "dia" => $fecha, "cod_materia" => $codMateria, "baja"=>'0'));
                $objMatHorarios->cod_estado_academico = $alumno['cod_estado_academico'];
                $objMatHorarios->cod_horario = $arrHorarios[0]['codigo'];
                $objMatHorarios->baja = 0;
                $objMatHorarios->fecha_hora = date("Y-m-d H:i:s");
                $objMatHorarios->usuario = $cod_usuario;
            }
    
            //if (guardar_horario){}
            $horarios = new Vhorarios($conexion, $objMatHorarios->cod_horario);
            if ($asistencias == true){
                $horarios->asistencia_tomada();
            }
            else {
                $horarios->asistencia_notomada();
            }
            
            if (isset($alumno['estado'])){
                $objMatHorarios->baja = 0;
                $respuesta = $objMatHorarios->cambiarEstado($alumno['estado']);
                if ($alumno['estado'] != '') {
                    $matHorEstadoHistorico = new Vmatriculas_horarios_estados_historicos($conexion);
                    $arrMatHorEstHistorico = array(
                        "cod_matricula_horario" => $objMatHorarios->getCodigo(),
                        "fecha_hora" => date("Y-m-d H:i:s"),
                        "motivo" => '',
                        "comentario" => '',
                        "usuario_creador" => $cod_usuario,
                        "estado" => $alumno['estado']
                    );
                    $matHorEstadoHistorico->setMatriculas_horarios_estados_historicos($arrMatHorEstHistorico);
                    $matHorEstadoHistorico->guardarMatriculas_horarios_estados_historicos();
                }
                $estadosacademicos[] = $objMatHorarios->cod_estado_academico;
            }
            //Guardamos asistencia
            //$objMatHorarios->asistencia_tomada();
        }
        //calcular asistencia
        if ($codEstadoAcademico == null){
            $unicos = array_unique($estadosacademicos);
            foreach ($unicos as $rowea) {
                $parametrosasis = array('cod_estado_academico' => $rowea, 'cod_comision' => '', 'cod_materia' => '', 'fecha' => '');
                $objtarecron = new Vtareas_crons($conexion);
                $objtarecron->guardar('calcular_asistencia', $parametrosasis, $this->codigo_filial);
                //Ticket 4584 -mmori- se calcula el porcentaje de asistencias al momento de guardarla.
                $myEstadoAcademcio = new Vestadoacademico($conexion, $rowea);
                $myEstadoAcademcio->calcular_porcentaje_asistencia();
            }
        } else {
            $myEstadoAcademcio = new Vestadoacademico($conexion, $codEstadoAcademico);
            $myEstadoAcademcio->calcular_porcentaje_asistencia();
        }
        $estadotran = $conexion->trans_status();

        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    
    
    public function getInscriptosAsistencias($cod_materia, $cod_comision, $fecha, $cod_horario = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $apellidoPrimero = Vconfiguracion::getValorConfiguracion($conexion, null, 'NombreFormato');
        $orden = $apellidoPrimero == 1 ? array('campo' => 'apellido', 'valor' => 'asc') : array('campo' => 'nombre', 'valor' => 'asc');
        $alumnosAsistencias = Vmatriculas_horarios::getInscripcionHorariosComision2($conexion, $cod_materia, $cod_comision, $fecha, $cod_horario);
        $alumnosAsistencias2 = array();

        foreach ($alumnosAsistencias as $key => $rowAlumno) {
            if($rowAlumno['cod_matricula_horario'] == -1)
                continue;
            $nombre = inicialesMayusculas($rowAlumno['nombre']);
            $apellido = inicialesMayusculas($rowAlumno['apellido']);
            $alumnosAsistencias[$key]['nombre_apellido'] = formatearNombreApellido($nombre, $apellido);
            $alumnosAsistencias[$key]['nombre_estado_academico'] = lang($rowAlumno['estado_academico']);
            //siwakawa - billete
            if($rowAlumno['estado_academico'] == "cursando" || $rowAlumno['estado_academico'] != '')
                $alumnosAsistencias2[] = $alumnosAsistencias[$key];
        }

        return $alumnosAsistencias2;
        //return $conexion->last_query();
    }

    public function arreglarInscriptosSinHorarios($conexion){
        $queryEstadoAcademico = $conexion->query("select * from matriculas_inscripciones
        join comisiones on matriculas_inscripciones.cod_comision = comisiones.codigo and comisiones.estado = 'habilitado'
        join estadoacademico on estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico
        where  matriculas_inscripciones.baja = 0 and estadoacademico.estado = 'cursando';")->result_array();
        foreach($queryEstadoAcademico  as $inscripcion){
            //Ahora, me fijo si hay horarios que no tengan la inscipcion correspondiente. De ser asi, los agrego.
            $comision = $inscripcion['cod_comision'];
            $materia = $inscripcion['codmateria'];
            $estadoacademico = $inscripcion['cod_estado_academico'];
            $queryHorariosComision = $conexion->query("SELECT codigo FROM horarios where cod_comision = $comision AND cod_materia = $materia and baja = 0")->result_array();
            $queryHorariosAlumnos = $conexion->query("select cod_horario from matriculas_horarios where cod_estado_academico = $estadoacademico and baja = 0")->result_array();
            foreach($queryHorariosComision as $horario){
                $codHorario = $horario['codigo'];
                if(!in_array(array('cod_horario' => $codHorario), $queryHorariosAlumnos)){
                    $insertquery ="INSERT INTO matriculas_horarios(cod_estado_academico, cod_horario, fecha_hora, baja, usuario)
                                   VALUES($estadoacademico, $codHorario, NOW(), 0, 2)";
                    echo $insertquery . PHP_EOL;
                    $conexion->query($insertquery);
                }
            }

        }


    }

}
