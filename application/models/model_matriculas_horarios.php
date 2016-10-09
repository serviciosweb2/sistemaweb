<?php

Class Model_matriculas_horarios extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getDetallesAsistencias($cod_mat_horarios, $fecha) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('formatearfecha');
        $objMatHorarios = new Vmatriculas_horarios($conexion, $cod_mat_horarios);
        $cod_horario = $objMatHorarios->cod_horario;
        $cod_estado_academico = $objMatHorarios->cod_estado_academico;
        $objHorario = new Vhorarios($conexion, $cod_horario);
        $cod_materia = $objHorario->cod_materia;
        $cod_comision = $objHorario->cod_comision;

        $detalleAsistencas = $objMatHorarios->getDetallesAsistencias($cod_comision, $cod_materia, $cod_estado_academico, $fecha);
        foreach ($detalleAsistencas as $key => $row) {
            $detalleAsistencas[$key]['dia'] = formatearFecha_pais($row['dia']);
            $detalleAsistencas[$key]['estado'] = $row['estado'] != '' ? lang($row['estado']) : '';
        }
        return $detalleAsistencas;
    }

    public function getInscriptosHorario($codigo_horario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $apellidoprimero = Vconfiguracion::getValorConfiguracion($conexion, null, 'NombreFormato');
        if ($apellidoprimero == '1') {
            $orden = array('campo' => 'alumnos.apellido', 'valor' => 'asc');
        } else {
            $orden = array('campo' => 'alumnos.nombre', 'valor' => 'asc');
        }
        
        $myHorario = new Vhorarios($conexion, $codigo_horario);
        
        if($codigo_horario == -1)
        {
            $inscriptos = Vmatriculas_horarios::getInscriptosHorarios($conexion, null, null, null, $codigo_horario, $orden, true);
        }
        else
        {
            $inscriptos = Vmatriculas_horarios::getInscripcionHorariosComision($conexion, $myHorario->cod_materia, $myHorario->cod_comision, $myHorario->dia);
        }
        

        for ($i = 0; $i < count($inscriptos); $i++) {
            $inscriptos[$i]['nombreapellido'] = formatearNombreApellido($inscriptos[$i]['nombre'], $inscriptos[$i]['apellido']);
            $comialumno = new Vcomisiones($conexion, $inscriptos[$i]['comisionalumno']);
            $inscriptos[$i]['comisionalumno'] = $comialumno->nombre;
        }

        return $inscriptos;
    }

    public function guardarExcepcion($arrdatos) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $conexion->trans_start();

        $respuesta = '';
        foreach ($arrdatos['cod_inscripciones'] as $mathorario) {
            $inscripcion = new Vmatriculas_horarios($conexion, $mathorario);
            $inscripcion->bajaMatriculaHorario();

            $nuevainscripcion = new Vmatriculas_horarios($conexion, -1);
            $nuevainscripcion->altaMatriculaHorario($inscripcion->cod_estado_academico, $arrdatos['horario_nuevo'], $arrdatos['usuario']);

            $inscripcion->guardaExcepcion($nuevainscripcion->getCodigo());

            //calcular asistencia
            $estadoaca = new Vestadoacademico($conexion, $inscripcion->cod_estado_academico);
            $parametrosasis = array('cod_estado_academico' => $estadoaca->getCodigo(), 'cod_comision' => '', 'cod_materia' => '', 'fecha' => '');
            $objtarecron = new Vtareas_crons($conexion);
            $objtarecron->guardar('calcular_asistencia', $parametrosasis, $this->codigo_filial);
        }

        $conexion->trans_complete();
        $resultado = $conexion->trans_status();
        return class_general::_generarRespuestaModelo($conexion, $resultado, $respuesta);
    }

    public function getInscriptosExcepcion($codigo_horario, $arrmathor = null, $apellidoPrimero) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');

        if ($arrmathor == null) {
            $arrmathor = array();
        }

        if ($apellidoPrimero == '1') {
            $orden = array('campo' => 'alumnos.apellido', 'valor' => 'asc');
        } else {
            $orden = array('campo' => 'alumnos.nombre', 'valor' => 'asc');
        }

        $arrinscriptos = Vmatriculas_horarios::getInscriptosHorarios($conexion, null, null, null, $codigo_horario, $orden);

        $i = 0;
        foreach ($arrinscriptos as $inscripto) {
            if ($inscripto['estado'] == null) {

                $inscriptos[$i]['nombreapellido'] = formatearNombreApellido($inscripto['nombre'], $inscripto['apellido']);
                $inscriptos[$i]['cod_matricula_horario'] = $inscripto['cod_matricula_horario'];
                $esta = false;
                foreach ($arrmathor as $codmathorario) {
                    if ($inscripto['cod_matricula_horario'] == $codmathorario) {
                        $esta = true;
                        break;
                    }
                }
                $inscriptos[$i]['selected'] = $esta;
                $i++;
            }
        }
        return $inscriptos;
    }

    public function alertarCargarAsistencia() {
        $conexion = $this->load->database("default", true);
        $this->lang->load(get_idioma(), get_idioma());
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $fecha = date('Y-m-d');
            $alumnos = Vmatriculas_horarios::getComisionesSinAsistencia($conexion, $fecha, true, '2014-10-01', true);

            if ($alumnos > 0) {
                //veo si hay alerta creada para esto
                $condiciones = array('tipo_alerta' => 'falta_carga_asistencia');
                $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
                $arrAlerta = Valertas::listarAlertas($conexion, $condiciones, null, $orden);
                $codalerta = isset($arrAlerta[0]['codigo']) ? $arrAlerta[0]['codigo'] : -1;
                $alerta = new Valertas($conexion, $codalerta);
                $alerta->fecha_hora = date('Y-m-d H:i:s');
                $alerta->mensaje = lang('falta_cargar_asistencia_a') . $alumnos . ' ' . lang('comisiones_min') . '. ' . lang('puede_obtener_mas_informacion') . '<a href="#" onclick="VerAsistencias()">' . lang('reporte_de_asistencias') . ' </a>';
                $alerta->tipo_alerta = 'falta_carga_asistencia';
                $alerta->guardarAlertas();

                //traigo los usuarios habilitados de este sistema
                $usuarios = Vusuarios_sistema::getUsuariosPermisos($conexion, $filial['codigo'], null, null, array('asistencias'));

                foreach ($usuarios as $rowusuario) {
                    if ($alerta->existeAlertaUsuario($rowusuario['id_usuario'])) {
                        $alerta->marcarNoLeida($rowusuario['id_usuario']);
                    } else {
                        $alerta->setUsuario($rowusuario['id_usuario']);
                    }
                    $condiciones = array('reporte' => 'asistencia', 'solo_lectura' => 1, 'codigo_usuario' => $rowusuario['id_usuario']);
                    $filtros = Vfiltros_reportes::listarFiltros_reportes($conexion, $condiciones);
                    
                    if (count($filtros) <= 0) {
                        $arrFiltro = array("field_view" => array("alumno_nombre", "materia_nombre", "comision_nombre", "dia_cursado", "horas", "salon", "profesor_nombre", "asistencia"), "advanced_filters" => array(array("field" => "dia", "filter" => "es_mayor_igual_a", "value1" => "01/10/2014", "dataType" => "date")), "common_filters" => array("falta_carga_asistencia"));
                        $myFiltro = new Vfiltros_reportes($conexion);
                        $myFiltro->nombre = lang('alerta_carga_asistencia');
                        $myFiltro->default = 0;
                        $myFiltro->compartido = 0;
                        $myFiltro->codigo_usuario = $rowusuario['id_usuario'];
                        $myFiltro->reporte = 'asistencia';
                        $myFiltro->valores = json_encode($arrFiltro);
                        $myFiltro->solo_lectura = 1;
                        $myFiltro->guardarFiltros_reportes();
                    }
                }
            }

            $estadotran = $conexion->trans_status();
            if ($estadotran === FALSE) {
                $conexion->trans_rollback();
            } else {
                $conexion->trans_commit();
            }
        }
    }

}
