<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model_matriculas_periodos
 * 
 * ...
 * 
 * @package model_matriculas
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
class Model_matriculas_periodos extends CI_Model {

    var $codigofilial = 0;
    var $codigo = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigofilial = $arg["filial"]["codigo"];
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
    }

    public function getMotivosBaja() {
        $conexion = $this->load->database($this->codigofilial, true);
        $matestado = new Vmatriculas_estado_historicos($conexion);
        $motivos = $matestado->getmotivos();
        for ($i = 0; $i < count($motivos); $i++) {
            $motivos[$i]['motivo'] = lang($motivos[$i]['motivo']);
        }
        return $motivos;
    }

    public function finalizarMatriculasPeriodos() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();

            $condmatriculas = array('estado' => Vmatriculas_periodos::getEstadoHabilitada());
            $matriculasperiodos = Vmatriculas_periodos::listarMatriculas_periodos($conexion, $condmatriculas);

            foreach ($matriculasperiodos as $rowmatricula) {
                $objmatper = new Vmatriculas_periodos($conexion, $rowmatricula['codigo']);

                $estadoacademico = $objmatper->getEstadoAcademico();

                $aprobada = true;

                for ($i = 0; $i < count($estadoacademico); $i++) {
                    if ($estadoacademico[$i]['estado'] != 'aprobado' && $estadoacademico[$i]['estado'] != 'homologado' && $estadoacademico[$i]['estado'] != 'recursa' && $estadoacademico[$i]['estado'] != 'migrado') {
                        $aprobada = false;
                        $i = count($estadoacademico);
                    }
                }

                if ($aprobada) {
                    $objmatper->finalizada();
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

    public function certificarMatriculasPeriodos() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();

            $condmatriculas = array('estado' => Vmatriculas_periodos::getEstadoFinalizada());
            $matriculasperiodos = Vmatriculas_periodos::listarMatriculas_periodos($conexion, $condmatriculas);

            foreach ($matriculasperiodos as $rowmatricula) {
                $objmatper = new Vmatriculas_periodos($conexion, $rowmatricula['codigo']);
                $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);

                $condiciones = array('cod_filial' => $filial['codigo'], 'cod_tipo_periodo' => $objmatper->cod_tipo_periodo, 'cod_plan_academico' => $objmatricula->cod_plan_academico, 'opcional' => '0');
                $certificadosobligatorios = Vcertificados_plan_filial::listarCerfificados_plan_filial($conexion, $condiciones);
                $certifica = true;
                foreach ($certificadosobligatorios as $certificado) {
                    $objcertificado = new Vcertificados($conexion, $objmatper->getCodigo(), $certificado['cod_certificante']);
                    if ($objcertificado->estado != Vcertificados::getEstadoFinalizado()) {
                        $certifica = false;
                    }
                }

                if ($certifica && count($certificadosobligatorios) > 0) {
                    $objmatper->certificada();
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

    public function baja($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $arrmatriculas = array();
        $respuestaCustom = array();
        $respuesta = '';

        foreach ($datos['arrmatriculasper'] as $cod_matricula_periodo) {
            $matriculaP = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
            $arrmatriculas[$matriculaP->cod_matricula][] = $matriculaP->getCodigo();
        }
        foreach ($arrmatriculas as $key => $rowmatriculas) {
            $objmatricula = new Vmatriculas($conexion, $key);

            foreach ($rowmatriculas as $codmatper) {
                $objmatper = new Vmatriculas_periodos($conexion, $codmatper);
                $codigoHistorico = $objmatper->baja($datos['motivo'], $datos['comentario'], $datos['cod_usuario']);
                $respuestaCustom['bajas'][] = $objmatper->getCodigo();
                $respuestaCustom['codigo_historico'] = $codigoHistorico;
            }

            $existe = $objmatricula->existenMasNoInhabilitadas($rowmatriculas);
            if ($existe) {
                $respuesta = '2';
                $respuestaCustom['refinancia'][] = $key;
                $respuestaCustom['codigo_alumno'] = $datos['cod_alumno'];
                $objmatricula->bajaCtaCte(4);
            } else {
                $objmatricula->bajaCtaCte(4);
//                $arrCtacte = $objmatricula->getCtaCte(true, array('habilitado' => 1, 'importe >' => 'pagado'));
//                foreach ($arrCtacte as $ctacte) {
//                    $myCtacte = new Vctacte($conexion, $ctacte['codigo']);
//                    $myCtacte->setPasiva(true, 4);
//                }
            }
        }

        if ($conexion->trans_status() === FALSE) {
            $estadotran = 0;
            $conexion->trans_rollback();
        } else {
            $estadotran = $respuesta != '' ? $respuesta : $conexion->trans_status();
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuestaCustom);
    }

    public function getCtaCteHabilitarMatricula($cod_matricula_periodo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');

        $matriculaper = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        $objmatricula = new Vmatriculas($conexion, $matriculaper->cod_matricula);
        $condiciones = array('habilitado' => 0);
        $ctacte = $objmatricula->getCtaCte(true, $condiciones, 1);

        formatearCtaCte($conexion, $ctacte);
        $ctaCteOrder = Vctacte::ordenarCtaCte($ctacte);
        return $ctaCteOrder;
    }

    public function getAlumno($cod_matricula_periodo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objmatper = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);
        return $objmatricula->getAlumno();
    }

    public function getNombreMatriculaPeriodo($cod_matricula_periodo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objmatper = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);
        $objplan = new Vplanes_academicos($conexion, $objmatricula->cod_plan_academico);
        $objCurso = $objplan->getCurso();
        $nombre_curso = 'nombre_' . get_idioma();
        $nombre = $objCurso->$nombre_curso;
        $objplan->getPeriodos();
        $periodosplan = $objplan->getPeriodosModalidadesFilial($this->codigofilial, null, $objmatper->modalidad, false);

        if (count($periodosplan) > 1) {
            $nombrePeriodo = $objplan->getNombrePeriodoModalidadFilial($objmatper->cod_tipo_periodo, $objmatper->modalidad, $this->codigofilial);
            $nombre.= ' (' . $nombrePeriodo . ')';
        }

        //si tiene mas de un plan el curso mostrar plan
        $condplanes = array('cod_curso' => $objCurso->getCodigo());
        $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condplanes);
        if (count($planescurso) > 1) {
            $nombre.=' / ' . $objplan->nombre;
        }
        return $nombre;
    }

    public function alta($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        foreach ($datos['arrmatriculasper'] as $cod_matricula_periodo) {
            $matriculaP = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
            $arrmatriculas[$matriculaP->cod_matricula][] = $matriculaP->getCodigo();
        }

        foreach ($arrmatriculas as $key => $rowmatriculas) {
            $objmatricula = new Vmatriculas($conexion, $key);
            $objmatricula->altaCtaCte();

            foreach ($rowmatriculas as $codmatper) {
                $objmatper = new Vmatriculas_periodos($conexion, $codmatper);
                $objmatper->alta(null, $datos['comentario'], $datos['cod_usuario']);
                $respuestaCustom['altas'][] = $objmatper->getCodigo();
            }
        }
        //calcular mora
        $parametroMora = array('cod_alumno' => $objmatricula->cod_alumno);
        $objtarecron = new Vtareas_crons($conexion);
        $objtarecron->guardar('calcular_mora', $parametroMora, $this->codigofilial);

        $respuestaCustom['ctacte'][] = $key;

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        //  $respuestaCustom = $respuesta ? $idHistorico : false;
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuestaCustom);
    }

    public function getMaterias($codmatper, $estado = null, $sinestado = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrMateria = array();
        $condiciones = array('cod_matricula_periodo' => $codmatper);
        if ($estado != null) {
            $condiciones['estado'] = $estado;
        }
        if ($sinestado != null) {
            $condiciones['estado <>'] = $sinestado;
        }
        $condiciones['estado <>'] = "recursa";
        $estadoaca = Vestadoacademico::listarEstadoacademico($conexion, $condiciones);
        foreach ($estadoaca as $row) {
            $objmateria = new Vmaterias($conexion, $row['codmateria']);
            $nombre = 'nombre_' . get_idioma();
            $arrMateria[] = array('codigo' => $row['codigo'], 'nombre' => $objmateria->$nombre);
        }
        $nombre = array();
        foreach ($arrMateria as $clave => $fila) {
            $nombre[$clave] = $fila['nombre'];
        }
        array_multisort($nombre, SORT_ASC,  $arrMateria);
        return $arrMateria;
    }

    public function getModalidades($codmatper, $modalidadActual = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $respuesta = array();
        $matriculaperiodo = new Vmatriculas_periodos($conexion, $codmatper);
        $matricula = new Vmatriculas($conexion, $matriculaperiodo->cod_matricula);
        $plan = new Vplanes_academicos($conexion, $matricula->cod_plan_academico);
        $modalidades = $plan->getPeriodosModalidadesFilial($this->codigofilial, $matriculaperiodo->cod_tipo_periodo);

        foreach ($modalidades as $value) 
        {
            if(!$modalidadActual)
            {
                if ($value['modalidad'] != $matriculaperiodo->modalidad) 
                {
                    $respuesta[] = array('codigo' => $value['modalidad'], 'modalidad' => lang($value['modalidad']));
                }
            }
            else
            {
                $respuesta[] = array('codigo' => $value['modalidad'], 'modalidad' => lang($value['modalidad']));
            }
        }

        return $respuesta;
    }

    public function cambiarModalidad($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        $matriculaperiodo = new Vmatriculas_periodos($conexion, $datos['cod_mat_periodo']);
        $respuesta = $matriculaperiodo->setModalidad($datos['modalidad'], $datos['cod_usuario']);

        $materias = $matriculaperiodo->getEstadoAcademico();

        foreach ($materias as $rowmateria) {
            $objea = new Vestadoacademico($conexion, $rowmateria['codigo']);
            $inscripciones = $objea->getInscripciones(true, array($datos['modalidad']));

            foreach ($inscripciones as $rowinscripcion) {
                $objinscripcion = new Vmatriculas_inscripciones($conexion, $rowinscripcion['codigo']);
                $objinscripcion->baja(null, $datos['cod_usuario']);
            }
            foreach ($datos['arrcomisiones'] as $rowcomision) {
                if ($objea->getCodigo() == $rowcomision['estaca'] && $rowcomision['comision'] != '' && $rowcomision['comision'] != '-1') {
                    $objea->inscribirComision($rowcomision['comision'], $datos['cod_usuario']);
                }
            }
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getDetalleMateriasCambioModalidad($cod_mat_per, $modalidad) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper("comisiones");

        $mat_per = new Vmatriculas_periodos($conexion, $cod_mat_per);
        $materiasInscribir = $mat_per->getEstadoAcademico(true);

        $materias = array();
        $matricula = new Vmatriculas($conexion, $mat_per->cod_matricula);
        $cod_plan_academico = $matricula->cod_plan_academico;
        $planacademico = new Vplanes_academicos($conexion, $cod_plan_academico);
        $arrfecha = date_parse($mat_per->fecha_emision);
        $ciclo = $arrfecha['year'];

        $comisionesdestino = $modalidad != '' ? $planacademico->getComisiones(1, true, false, $mat_per->cod_tipo_periodo, true, $modalidad) : array();

        for ($i = 0; $i < count($materiasInscribir); $i++) {
            $estado = $materiasInscribir[$i]['estado'];
            if ($estado == Vestadoacademico::getEstadoNoCursado() || $estado == Vestadoacademico::getEstadoCursando()) {

                $cod_est_aca = $materiasInscribir[$i]['codigo'];
                $cod_materia = $materiasInscribir[$i]['codmateria'];
                $cod_inscripcion = $materiasInscribir[$i]['inscripcion'];

                $materias[$i]['codestadoacademico'] = $cod_est_aca;
                $materias[$i]['codmatricula'] = $cod_mat_per;
                $materias[$i]['codmateria'] = $cod_materia;
                $materias[$i]['fecha'] = $materiasInscribir[$i]['fecha'];
                switch (get_idioma()) {
                    case 'es':
                        $materias[$i]['nombre'] = $materiasInscribir[$i]['nombre_es'];
                        break;
                    case 'in':

                        $materias[$i]['nombre'] = $materiasInscribir[$i]['nombre_in'];
                        break;
                    case 'pt':
                        $materias[$i]['nombre'] = $materiasInscribir[$i]['nombre_pt'];

                        break;

                    default:
                        $materias[$i]['nombre'] = $materiasInscribir[$i]['nombre_es'];
                        break;
                }
                $materias[$i]['estado'] = lang($estado);
                $materias[$i]['codinscripcion'] = $cod_inscripcion;
                $materias[$i]['nombreComision'] = '-';

                $inscripcion = new Vmatriculas_inscripciones($conexion, $cod_inscripcion);

                //comision destino

                if ($inscripcion->getCodigo() != '-1') {
                    $ocomision = new Vcomisiones($conexion, $inscripcion->cod_comision);
                    $materias[$i]['nombreComision'] = $ocomision->nombre;
                    for ($b = 0; $b < count($comisionesdestino); $b++) {
                        if ($ocomision->getCodigo() == $comisionesdestino[$b]['codigo']) {
                            $comisionesdestino[$b] = null;
                        }
                    }
                }
                $comdest = array();
                $m = 0;
                for ($h = 0; $h < count($comisionesdestino); $h++) {
                    if ($comisionesdestino[$h] != null) {

                        $capacidad = Vconfiguracion::getValorConfiguracion($conexion, null, 'CapacidadComision');
                        $nbeviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
                        $habilitaSinCupo = Vconfiguracion::getValorConfiguracion($conexion, null, 'comisionesSinCupo');

                        $objComision = new Vcomisiones($conexion, $comisionesdestino[$h]['codigo']);
                        $nombre = '';
                        $arrcupo = $objComision->getCapacidad();
                        $arrinscriptos = $objComision->getInscriptos($cod_materia);

                        $cupo = count($arrcupo) > 0 ? $arrcupo[0]['cupo'] : '-1';
                        $inscriptos = count($arrinscriptos) > 0 ? $arrinscriptos[0]['inscriptos'] : 0;

                        $capacidadcomi = $cupo != '-1' ? $cupo : 0;
                        $disponible = $capacidadcomi - $inscriptos;
                        $nombre = $objComision->nombre;
                        $nombre .= $nbeviejo == '1' ? ' (' . $objComision->descripcion . ')' : '';
                        if ($capacidad == '1') {
                            $nombre.=' / ';
                            $nombre.=$cupo != '-1' ? lang('cupo') . ': ' . $disponible : lang('sin_horario');
                        }

                        $comdest[$m] = $comisionesdestino[$h];
                        $comdest[$m]['nombre'] = $nombre;
                        $comdest[$m]['habilita'] = $habilitaSinCupo == '1' || $disponible > 0 ? 1 : 0;
                        $m++;
                    }
                }
                $materias[$i]['comisiones_destino'] = $comdest;
            }
        }

        return $materias;
    }

    //    public function getCtaCteInhabilitarMatricula($cod_matricula_periodo) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $this->load->helper('cuentacorriente');
//
//        $matriculaper = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
//        $objmatricula = new Vmatriculas($conexion, $matriculaper->cod_matricula);
//        $condiciones = array('habilitado' => 1);
//        $ctacte = $objmatricula->getCtaCte(true, $condiciones, 1);
//
//        formatearCtaCte($conexion, $ctacte);
//        $ctaCteOrder = Vctacte::ordenarCtaCte($ctacte);
//        return $ctaCteOrder;
//    }
//    public function getCtaCteHabilitarMatriculasPeriodos($codalumno, $codplanacademico, $debe = false) {
//
//        $conexion = $this->load->database($this->codigofilial, true);
//
//        $alumno = new Valumnos($conexion, $codalumno);
//        $ctacte = array();
//
//        $this->load->helper('cuentacorriente');
//
//        $matriculas = $alumno->getMatriculasPlanAcademico($codplanacademico, Vmatriculas::getEstadoInhabilitada());
//        $ctactePer = array();
//        for ($i = 0; $i < count($matriculas); $i++) {
//            $objmatricula = new Vmatriculas($conexion, $matriculas[$i]['codigo']);
//            $ctactePer[$i] = $objmatricula->getCtaCte(true, null, 1);
//        }
//
//        $index = 0;
//        for ($a = 0; $a < count($ctactePer); $a++) {
//            for ($v = 0; $v < count($ctactePer[$a]); $v++) {
//
//                $ctacte[$index] = $ctactePer[$a][$v];
//                $index++;
////                $ctacteMora = new Vctacte($conexion, $ctactePer[$a][$v]['codigo']);
////                $moras = $ctacteMora->getMoras();
////
////                foreach ($moras as $mora) {
////                    $ctacte[$index] = $mora;
////                    $index++;
////                }
//            }
//        }
//
//        formatearCtaCte($conexion, $ctacte);
//        $ctaCteOrder = Vctacte::ordenarCtaCte($ctacte);
//
//        return $ctaCteOrder;
//    }
//    public function bajaMatriculasCtaCte($datos) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $conexion->trans_begin();
//
//        foreach ($datos['arrmatriculasper'] as $cod_matricula_periodo) {
//            $objmatper = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
//            $objmatper->baja($datos['motivo'], $datos['comentario'], $datos['cod_usuario']);
//            $arrmatriculas[$matriculaP->cod_matricula][] = $matriculaP->getCodigo();
//        }
//
//        foreach ($arrmatriculas as $key => $value) {
//            $objmatricula = new Vmatriculas($conexion, $key);
//            $objmatricula->bajaCtaCte();
//        }
//        foreach ($datos['cta_cte'] as $cod_ctacte) {
//            $objctacte = new Vctacte($conexion, $cod_ctacte);
//            $objctacte->alta();
//        }
//
//        if ($conexion->trans_status() === FALSE) {
//            $estadotran = 0;
//            $conexion->trans_rollback();
//        } else {
//            $estadotran = $respuesta != '' ? $respuesta : $conexion->trans_status();
//            $conexion->trans_commit();
//        }
//        return class_general::_generarRespuestaModelo($conexion, $estadotran);
//    }
    //    public function getMatriculasPeriodosBajaAlumno($cod_alumno, $cod_plan_academico) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $rows = array();
//
//        $objalumno = new Valumnos($conexion, $cod_alumno);
//        $matperiodos = $objalumno->getMatriculasPeriodosPlanAcademico($cod_plan_academico, 'habilitada');
//
//        foreach ($matperiodos as $row) {
//
//            $rows[] = array(
//                $row["cod_tipo_periodo"],
//                lang($row["nombre"]),
//                formatearFecha_pais($row["fecha_emision"]),
//                $row["estado"]
//            );
//        }
//
//        return $rows;
//    }
//    public function cambiarEstado($cambiomatricula) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $conexion->trans_begin();
//
//        $objAlumno = new Valumnos($conexion, $cambiomatricula['cod_alumno']);
//        $matperiodo = $objAlumno->getMatriculasPeriodosPlanAcademico($cambiomatricula['cod_plan_academico'], null, $cambiomatricula['periodo']);
//
//        $matriculaP = new Vmatriculas_periodos($conexion, $matperiodo[0]['cod_matricula_periodo']);
//
//        if ($matriculaP->estado == Vmatriculas_periodos::getEstadoHabilitada()) {
//            $respuesta = $matriculaP->baja($cambiomatricula['motivo'], $cambiomatricula['comentario'], $cambiomatricula['cod_usuario']);
//            $idHistorico = $conexion->insert_id();
//        } elseif ($matriculaP->estado == Vmatriculas_periodos::getEstadoInhabilitada()) {
//            $respuesta = $matriculaP->alta($cambiomatricula['motivo'], $cambiomatricula['comentario'], $cambiomatricula['cod_usuario']);
//            $idHistorico = $conexion->insert_id();
//        }
//
//        $estadotran = $conexion->trans_status();
//        if ($estadotran === FALSE) {
//            $conexion->trans_rollback();
//        } else {
//            $conexion->trans_commit();
//        }
//        $respuestaCustom = $respuesta ? $idHistorico : false;
//        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuestaCustom);
//    }
}
