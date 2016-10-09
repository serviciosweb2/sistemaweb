<?php

/**
 * Model_estadoacademico
 * 
 * Description...
 * 
 * @package model_estadoacademico
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_estadoacademico extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["filial"]["codigo"];
    }

    public function getEstadosMaterias() {
        $conexion = $this->load->database($this->codigofilial, true);
        $estados = new Vestadoacademico($conexion);
        $retorno = $estados->getEstadosMaterias();
        for ($i = 0; $i < count($retorno); $i++) {
            $retorno[$i]['nombre'] = lang($retorno[$i]['nombre']);
        }
        return $retorno;
    }

    public function getEstadosCambioMateria($codigo) {

        $conexion = $this->load->database($this->codigofilial, true);
        $objEstAca = new Vestadoacademico($conexion, $codigo);
        $estadoscambiar = array();

        if ($objEstAca->estado != 'aprobado') {

            $estados = $objEstAca->getEstadosMaterias();

            foreach ($estados as $rowestado) {
                if (!($objEstAca->estado == 'regular' && $rowestado['codigo'] == 'aprobado')) {
                    if ($objEstAca->AceptaCambioEstado($rowestado['codigo'])) {
                        $estadoscambiar[] = array('codigo' => $rowestado['codigo'], 'nombre' => lang($rowestado['nombre']));
                    }
                }
            }
        }
        return $estadoscambiar;
    }

    public function cambiarEstadoMateria($cambiomateria) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        
        $objEstAca = new Vestadoacademico($conexion, $cambiomateria['cod_estado']);
        $respuesta = $objEstAca->guardarCambioEstado($cambiomateria['estado'], $cambiomateria['cod_usuario'], $cambiomateria['motivo'], $cambiomateria['comentario']);
        
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            
            //mmori-verifico estado de certificado IGA
            $objcertificado = new Vcertificados($conexion, $objEstAca->cod_matricula_periodo, 1);
            $objcertificado->cambiarEstadoCertificadoIGA();
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getEstadoAcademico($codestado) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objEstAca = new Vestadoacademico($conexion, $codestado);
        return $objEstAca;
    }

    public function perdidaRegularidadMateria() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();

            $mesesduracion = Vconfiguracion::getValorConfiguracion($conexion, null, 'MesesDuracionRegularidad');
            $cantfinales = Vconfiguracion::getValorConfiguracion($conexion, null, 'CantMaxExamenesFinal');

            $estado = 'regular';
            $cambioestado = array();
            $a = 0;

            $condicion = array('estado' => $estado);
            $earegulares = Vestadoacademico::listarEstadoacademico($conexion, $condicion);

            foreach ($earegulares as $estadoacademico) {

                $condiciones = array('cod_estado_academico' => $estadoacademico['codigo'],
                    'estado' => $estado);
                $orden = array(array('campo' => 'fecha_hora', 'orden' => 'desc'));

                $esthistorico = Vacademico_estado_historico::listarAcademico_estado_historico($conexion, $condiciones, null, $orden);

                $fechahora = explode(' ', $esthistorico[0]['fecha_hora']);
                $fecha = $fechahora[0];
                $fechavenc = strtotime($mesesduracion . ' month', strtotime($fecha));
                $fechavenc = date('Y-m-d', $fechavenc);

                if ($fechavenc <= date('Y-m-d')) {
                    $objestado = new Vestadoacademico($conexion, $estadoacademico['codigo']);
                    for ($i = 1; $i < count($esthistorico); $i++) {
                        if ($objestado->AceptaCambioEstado($esthistorico[$i]['estado'])) {
                            $objestado->guardarCambioEstado($esthistorico[$i]['estado'], '0', 2);
                            $cambioestado[$a]['cod_estado_aca'] = $estadoacademico['codigo'];
                            $cambioestado[$a]['tipo'] = 'VENCIDA';
                            $i = count($esthistorico);
                        }
                    }
                } else {
//                $condicion2 = array('cod_matricula' => $estadoacademico['codmatricula']);
//                $inscripciones = Vmatriculas_inscripciones::listarMatriculas_inscripciones($conexion, $condicion2);
//                foreach ($inscripciones as $rowinscripcion) {
//                    $matinscripcion=new Vmatriculas_inscripciones($conexion, $rowinscripcion['codigo']);
//                }
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

    public function calcularAsistencia($cod_estado_academico = '', $cod_comision = '', $cod_materia = '', $fecha = '') {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        $respuesta = '';
        $estadosacademico = array();

        if ($fecha != '') {
            $condicion = array('dia' => $fecha);
            $horarios = Vhorarios::listarHorarios($conexion, $condicion);
            $arrhorarios = array();
            if (count($horarios) > 0) {
                foreach ($horarios as $rowhorario) {
                    $arrhorarios[] = $rowhorario['codigo'];
                }
                $where_in = array(array('campo' => 'cod_horario', 'valores' => $arrhorarios));

                $mathorarios = Vmatriculas_horarios::getMatriculasHorarios($conexion, null, $where_in);
                foreach ($mathorarios as $rowmathorario) {
                    $estadosacademico[] = array('codigo' => $rowmathorario['cod_estado_academico']);
                }
            }
        } elseif ($cod_estado_academico !== '') {//calculo solo para un estado academico
            $condicion = array('codigo' => $cod_estado_academico);
            $estadosacademico = Vestadoacademico::getEstadosAcademicos($conexion, $condicion);
        } elseif ($cod_comision !== '' && $cod_materia != '') {//alumnos de una comision y materia
            $matriculas = Vmatriculas_inscripciones::getInscripcionesMateriaComision($conexion, $cod_comision, $cod_materia, false);

            $arrestados = array();
            foreach ($matriculas as $value) {
                $arrestados[] = $value['cod_estado_academico'];
            }
            if (count($arrestados) > 0) {
                $condicion = array('codmateria' => $cod_materia);
                $wherein = array(array('campo' => 'codigo',
                        'valores' => $arrestados));
                $estadosacademico = Vestadoacademico::getEstadosAcademicos($conexion, $condicion, $wherein);
            }
        } elseif ($cod_comision != '') {//alumnos de toda una comision
            $condionescomi = array('cod_comision' => $cod_comision,
                'baja' => 0);
            $eamatriculas = Vmatriculas_inscripciones::listarMatriculas_inscripciones($conexion, $condionescomi);

            $arrestados = array();
            foreach ($eamatriculas as $value) {
                $arrestados[] = $value['cod_estado_academico'];
            }
            if (count($arrestados) > 0) {
                $wherein = array(array('campo' => 'codigo',
                        'valores' => $arrestados));
                $estadosacademico = Vestadoacademico::getEstadosAcademicos($conexion, null, $wherein);
            }
        }
//        else {//todos
//            $estadosacademico = Vestadoacademico::getEstadosAcademicos($conexion);
//        }
        foreach ($estadosacademico as $rowea) {
            $objea = new Vestadoacademico($conexion, $rowea['codigo']);
            $objmatper = new Vmatriculas_periodos($conexion, $objea->cod_matricula_periodo);
            if ($objmatper->estado == Vmatriculas_periodos::getEstadoHabilitada()) {
                $condiciones = array('matriculas_horarios.cod_estado_academico' => $rowea['codigo'],
                    'matriculas_horarios.baja' => 0);

                $horarioscursa = Vmatriculas_horarios::getMatriculasHorariosCursados($conexion, $condiciones);
                $cantclases = count($horarioscursa);

                $asistio = 0;
                foreach ($horarioscursa as $rowhorariocursa) {
                    if ($rowhorariocursa['estado'] === 'presente' || $rowhorariocursa['estado'] === 'justificado') {
                        $asistio++;
                    }
                    if ($rowhorariocursa['estado'] === 'media_falta') {
                        $asistio = $asistio + 0.5;
                    }
                }
                $objea->porcasistencia = $cantclases != 0 ? $asistio * 100 / $cantclases : 0;
                $respuesta = $objea->guardarEstadoacademico();
            }
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        echo "transaccion::" . $estadotran;
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function guardarInscripciones($arrdatos, $motivo = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        for ($i = 0; $i < count($arrdatos['cod_estado_academico']); $i++) {
            
            $estadoacademico = new Vestadoacademico($conexion, $arrdatos['cod_estado_academico'][$i]);
            if (isset($arrdatos['cod_comision'][$i]) && $arrdatos['cod_comision'][$i] != '-1') {
				//Ver si estan todos los horarios cargados, de otra forma no debe dejar realizar el cambio de comision.
                $res = Vmatriculas_horarios::isAsistenciasAlumnoCargadas($conexion, $estadoacademico->getCodigo());
                if(!$res){
                    return json_encode(array('error'=>lang('cargar_asistencias_antes_de_cambio_comision')));
                }

                $estadoacademico->inscribirComision($arrdatos['cod_comision'][$i], $arrdatos["cod_usuario"],null, $motivo);
                $parametrosasis = array('cod_estado_academico' => $estadoacademico->getCodigo(), 'cod_comision' => '', 'cod_materia' => '', 'fecha' => '');
                $objtarecron = new Vtareas_crons($conexion);
                $objtarecron->guardar('calcular_asistencia', $parametrosasis, $this->codigofilial);
            }
            $objcertificado = new Vcertificados($conexion, $estadoacademico->cod_matricula_periodo, 1);
            $objcertificado->cambiarEstadoCertificadoIGA();
        }
        
        

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function regularizarComisionesViejas() {//incompleto
        $conexion = $this->load->database($this->codigofilial, true);

        $conexion->trans_begin();

        //busco estados academicoss que esten inscriptos en comisiones de ciclos anteriores a este
        $ciclo = Vciclos::getCiclosActuales($conexion, $this->codigofilial);
        $condiciones = array('comisiones.ciclo <' => $ciclo[0]['codigo'],
            'estadoacademico.estado' => 'cursando');

        $estadosaca = Vestadoacademico::getEstadosAcademicoInscripciones($conexion, $condiciones);

        foreach ($estadosaca as $row) {
            $objEstadoAca = new Vestadoacademico($conexion, $row['codigo']);
            if ($row['porcasistencia'] >= Vconfiguracion::getValorConfiguracion($conexion, null, 'PorcentajeAsistenciaRegular')) {
                $objEstadoAca->estado = 'regular';
            } else {
                $objEstadoAca->estado = 'no_curso'; //no deberia ponerse no_curso
            }
            //  $objEstadoAca->guardarEstadoacademico();
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
    }

    public function calcularPrimerAsistencia() {

        $conexion = $this->load->database('default', true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $calculo = Vconfiguracion::getValorConfiguracion($conexion, null, 'calculoAsistenciaPrimerInicio');
            $estadosaca = array();
            if ($calculo == 0) {
                $estadosacademico = Vestadoacademico::getEstadosAcademicos($conexion);
                foreach ($estadosacademico as $rowea) {
                    $conexion->trans_begin();
                    $objea = new Vestadoacademico($conexion, $rowea['codigo']);
                    $condiciones = array('matriculas_horarios.cod_estado_academico' => $rowea['codigo'],
                        'matriculas_horarios.baja' => 0);

                    $horarioscursa = Vmatriculas_horarios::getMatriculasHorariosCursados($conexion, $condiciones);
                    $cantclases = count($horarioscursa);

                    $asistio = 0;
                    foreach ($horarioscursa as $rowhorariocursa) {
                        if ($rowhorariocursa['estado'] === 'presente' || $rowhorariocursa['estado'] === 'justificado') {
                            $asistio++;
                        }
                        if ($rowhorariocursa['estado'] === 'media_falta') {
                            $asistio = $asistio + 0.5;
                        }
                    }
                    $objea->porcasistencia = $cantclases != 0 ? $asistio * 100 / $cantclases : 0;
                    $objea->guardarEstadoacademico();

                    $estadotran = $conexion->trans_status();
                    if ($estadotran === FALSE) {
                        $conexion->trans_rollback();
                    } else {
                        $estadosaca[] = $objea->getCodigo();
                        $conexion->trans_commit();
                    }
                }
            }
            $myConfiguracion = new Vconfiguracion($conexion, 32);
            $myConfiguracion->value = 1;
            $myConfiguracion->guardarConfiguracion('19');
            $respuesta['filial'] = $estadosaca;
        }
        return $respuesta;
    }

    public function listarEstadoAcademicoDataTable($arrFiltros, $idioma, $separador, $pasarLibre = false, $pasarRegular = false, $codCurso = null,
            $codMateria = null, $fechaDesde = null, $fechaHasta = null, $comision = null, $todos = false) {
        $conexion = $this->load->database($this->codigofilial, true);

        $this->load->helper('alumnos');
        $arrCondiciones = array();
        $arrWhereIn = array();

        $porcAsistencia = Vconfiguracion::getValorConfiguracion($conexion, null, 'PorcentajeAsistenciaRegular');

        if ($pasarLibre) {
            $arrWhereIn = array("estadoacademico.estado" => array(Vestadoacademico::getEstadoCursando(), Vestadoacademico::getEstadoNoCursado()));
            $conexion->where("estadoacademico.porcasistencia < $porcAsistencia || estadoacademico.porcasistencia IS NULL");
        }
        if ($pasarRegular) {
            $arrCondiciones = array(
                array("estadoacademico.estado" => Vestadoacademico::getEstadoCursando()),
                array("estadoacademico.porcasistencia >=" => $porcAsistencia)
            );
        }
        
        if ($todos) {
            $arrCondiciones = array(
                array("estadoacademico.estado" => Vestadoacademico::getEstadoCursando()),
                array("estadoacademico.porcasistencia < " => "100")
            );
        }
        
        $arrCondicionesLike = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondicionesLike = array(
                "alumno_nombre" => $arrFiltros["sSearch"],
                "porcasistencia" => $arrFiltros["sSearch"],
                "nombre_curso" => $arrFiltros["sSearch"],
                "nombre_materia" => $arrFiltros["sSearch"]
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();
        if ($arrFiltros["SortCol"] != "" && $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }

        // Cambio por errores de filtro comision Sistema IGA // Academico // Matricula // Regularizar alumnos //
        
        $contar = Vestadoacademico::listarEstadoAcademicoDataTable($conexion, $separador, $arrCondiciones, $arrCondicionesLike, null, null, false, $idioma, 
                $arrWhereIn, $codCurso, $codMateria, $fechaDesde, $fechaHasta, $comision);

        $datos = Vestadoacademico::listarEstadoAcademicoDataTable($conexion, $separador, $arrCondiciones, $arrCondicionesLike, $arrLimit, $arrSort, false, $idioma, 
                $arrWhereIn, $codCurso, $codMateria, $fechaDesde, $fechaHasta, $comision);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => count($contar),
            "iTotalDisplayRecords" => count($contar),
            "aaData" => array()
        );

        $rows = array();


        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                $row["alumno_nombre"],
                $row['nombre_curso'],
                $row['nombre_materia'],
                //Ticket 4771 -mmori- agrego columna
                $row['nombre_comision'],
                $row['porcasistencia'],
                $row['asistenciasSinCargar']
            );
        }
        $retorno['aaData'] = $rows;

        return $retorno;
    }

    function cambiarAEstadoLibre(array $arrEstadosAcademicos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        foreach ($arrEstadosAcademicos as $estadoacademico) {
            $myEstado = new Vestadoacademico($conexion, $estadoacademico);
            $myEstado->guardarCambioEstado(Vestadoacademico::getEstadoLibre(), $this->session->userdata('codigo_usuario'));
        }
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }

    function cambiarAEstadoRegular(array $arrEstadosAcademicos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        foreach ($arrEstadosAcademicos as $estadoacademico) {
            $myEstado = new Vestadoacademico($conexion, $estadoacademico);
            $myEstado->guardarCambioEstado(Vestadoacademico::getEstadoRegular(), $this->session->userdata('codigo_usuario'));
        }
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }

    function getHorarios($codestado) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objEstAca = new Vestadoacademico($conexion, $codestado);
        $arrhorarios = $objEstAca->getHorariosCursar();
        foreach ($arrhorarios as $key => $value) {
            $arrhorarios[$key]['dia'] = formatearFecha_pais($value['dia'], '', $this->codigofilial);
        }
        return $arrhorarios;
    }

}

/* End of file model_estadoacademico.php */
/* Location: ./application/models/model_estadoacademico.php */