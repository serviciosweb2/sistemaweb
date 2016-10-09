<?php

/**
 * Model_horarios
 *  
 * Description...
 * 
 * @package model_horarios
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_horarios extends CI_Model {

    var $codigo_filial = 0;
    var $repeticion = array("0" => "NO_REPETIR", "1" => "CADA_SEMANA");
    var $tipo_finaliza = array("1" => "FECHA");

    public function __construct($arg) {

        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getRepeticion() {

        return $this->repeticion;
    }

    public function getTipoFinalizacion() {
        return $this->tipo_finaliza;
    }

    public function getHorarios($fechaInicio = null, $fechaFin = null, $salones = null, array $comisiones = null, array $materias = null, array $profesores = null) {

        $conexion = $this->load->database($this->codigo_filial, true);
        $horarios = Vhorarios::getAllHorarios($conexion,  $this->codigo_filial ,$fechaInicio, $fechaFin, $salones, $comisiones, $materias, $profesores, true);
        $this->load->helper('comisiones');
        $horariosRetorno = array();
        $index = 0;
        foreach ($horarios as $hs) {
            
            $sepuede = $hs->cantasistencia > 0 ? false : true;
            $evento = array(
                "id" => $hs->codigo,
                "title" => "",
                "start" => $hs->dia . " " . $hs->horadesde,
                "end" => $hs->dia . " " . $hs->horahasta,
                "color" => $hs->color,
                "allDay" => false,
                "cod_salon" => $hs->cod_salon,
                "cod_comision" => $hs->cod_comision,
                "cod_materia" => $hs->cod_materia,
                "nombre_comision" => $hs->nombre,
                "nombre_curso" => $hs->curso,
                "editar" => $sepuede,
                "tipo" => 'CURSADO',
                "nombre_materia" => $hs->materia_nombre,
                "inscriptos_comision"=> $hs->alumnos_inscriptos_comision.' '.'de'.' '.$hs->cupo,
                "color_curso_plan"=> $hs->color_curso,
                "title_tooltip"=> $hs->curso.' '.lang($hs->nombre_periodo)
            );
//            switch (get_idioma()) {
//                case 'es':
//                    $evento["nombre_materia"] = $hs->materia_nombre;
//                    break;
//                case 'in':
//                    $evento["nombre_materia"] = $hs->materia_in;
//                    break;
//                case 'pt':
//                    $evento["nombre_materia"] = $hs->materia_pt;
//                    break;
//                default:
//                    $evento["nombre_materia"] = '';
//                    break;
//            }
            $horariosRetorno[$index] = $evento;
            $index++;
        }

        $config = array("codigo_filial" => $this->codigo_filial);
        $this->load->model("Model_feriados", "", false, $config);
        $this->load->model("Model_filiales","",false,  $this->codigo_filial);
        $listadoRecesoFilial= $this->Model_filiales->getReceso_filial();
        $feriados = $this->Model_feriados->getFeriados(true, '0');
        foreach ($feriados as $value) {
            $horariosRetorno[$index] = $value;
            $index ++;
        }
        foreach ($listadoRecesoFilial as $receso) {
            $horariosRetorno[$index] = $receso;
            $index ++;
        }
        
        return $horariosRetorno;
    }

    public function getObjHorario($codigo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        return $horario = new Vhorarios($conexion, $codigo);
    }

    public function getFormatEvento($conexion = null,$arrHorario = null, $objHorario = null) {
        $conexionGral = '';
        if($conexion == null){
          $conexionGral = $this->load->database($this->codigo_filial, true);  
        }else{
            $conexionGral = $conexion;
        }
        

        $codigo = $arrHorario["codigo"] == FALSE ? $objHorario->getCodigo() : $arrHorario["codigo"];
        $dia = $arrHorario["dia"] == FALSE ? $objHorario->dia : $arrHorario["dia"];
        $horadesde = $arrHorario["horadesde"] == FALSE ? $objHorario->horadesde : $arrHorario["horadesde"];
        $horahasta = $arrHorario["horahasta"] == FALSE ? $objHorario->horahasta : $arrHorario["horahasta"];
        $cod_salon = $arrHorario["cod_salon"] == FALSE ? $objHorario->cod_salon : $arrHorario["cod_salon"];
        $cod_materia = $arrHorario["cod_materia"] == FALSE ? $objHorario->cod_materia : $arrHorario["cod_materia"];
        $cod_comision = $arrHorario["cod_comision"] == FALSE ? $objHorario->cod_comision : $arrHorario["cod_comision"];

        $salones = new Vsalones($conexionGral, $cod_salon);
        $materia = new Vmaterias($conexionGral, $cod_materia);
        $comision = new Vcomisiones($conexionGral, $cod_comision);
        $planacademico = new Vplanes_academicos($conexionGral, $comision->cod_plan_academico);
        $curso = $planacademico->getCurso();
        $nombrecomision = $comision->nombre;
        switch (get_idioma()) {
            case 'es':
                $nombremateria = $materia->nombre_es;
                $nombrecurso = $curso->nombre_es;

                break;
            case 'pt':
                $nombremateria = $materia->nombre_pt;
                $nombrecurso = $curso->nombre_pt;

                break;
            case 'in':
                $nombremateria = $materia->nombre_in;
                $nombrecurso = $curso->nombre_in;

                break;

            default:
                break;
        }
        
      $inscriptos_colorCurso = Vhorarios::getColor_InscriptosComision($conexionGral, $codigo,  $this->codigo_filial);
      
        $evento = array(
            "id" => $codigo,
            "title" => "",
            "start" => $dia . " " . $horadesde,
            "end" => $dia . " " . $horahasta,
            "color" => $salones->color,
            "allDay" => false,
            "cod_comision" => $cod_comision,
            "cod_materia" => $cod_materia,
            "cod_salon" => $cod_salon,
            "nombre_comision" => $nombrecomision,
            "nombre_materia" => $nombremateria,
            "nombre_curso" => $nombrecurso,
            "editar" => true,
            "tipo" => 'CURSADO',
            "inscriptos_comision"=> $inscriptos_colorCurso[0]['inscriptos_alumnos'].' '.'de'.' '.$inscriptos_colorCurso[0]['cupo'],
             "color_curso_plan"=> $inscriptos_colorCurso[0]['color_curso_web'],
            "title_tooltip"=> $nombrecurso
        );

        return $evento;
    }

    public function guardarHorarios($arrdatos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();

        $respuestaAsistencia = $this->validaPosibilidadModificar($conexion, $arrdatos['codigo'], $arrdatos['modifica_serie']);
//        echo '<pre>';
//        print_r($respuestaAsistencia);
//        echo '<pre>';
        if ($respuestaAsistencia["modifica"] === TRUE) {
            $arrRespuesta = array();
            $unsetrepuesta = array();
            $arrFinal = array();
            $arrDiasRepetir = array();
            $arrDiasGuardar = array();

            $horario = new Vhorarios($conexion, $arrdatos["codigo"]);

            if ($arrdatos["modifica_serie"] == "true") {
                $unset = $horario->unSetRelacionados();
            }

            if ($arrdatos["codigo"] != -1) {
                $unsetrepuesta[]["id"] = $horario->unSetHorario();
            }

            $arrDiasRepetir = $this->getDiasRepeticion($arrdatos);
            $arrDiasGuardar[] = array("dia" => $arrdatos["diad"], "horad" => $arrdatos["horad"], "horah" => $arrdatos["horah"], "codigo" => $arrdatos["codigo"]);
            foreach ($arrDiasRepetir as $repetir) {
                $arrDiasGuardar[] = array("dia" => $repetir, "horad" => $arrdatos["horad"], "horah" => $arrdatos["horah"], "codigo" => -1);
            }

            //Verfico si se superponen con horarios ya guardados
            $respuestaSuperpo = $this->validaExisteEnRango($conexion, $arrdatos["comision"], $arrdatos["materia"], $arrDiasGuardar);
            if ($respuestaSuperpo["existe"] == false) {

                ///Guarda
                $nuevohorario = $this->guardarHorario($conexion, -1, $arrdatos["comision"], $arrdatos["salon"], $arrdatos["profesor"], $arrdatos["materia"], $arrdatos["diad"], $arrdatos["horad"], $arrdatos["horah"], '0', '0', $arrdatos["usuario"]);

                $arrRespuesta["nuevo"][] = $this->getFormatEvento($conexion,null, $nuevohorario);

                if ($arrdatos["modifica_serie"] == "true") {
                    $unset = $horario->unSetRelacionados();
                    $funset = $this->UnsetArray($unset);

                    foreach ($funset as $u) {

                        $unsetrepuesta[]["id"] = $u["id"];
                    }
                } else {
                    $nuevohorario->NoEsMasPadre();
                }

                $arrRespuesta["unset"] = $unsetrepuesta;

                //Busca la relacion para formar la serie.
                $relacion = $nuevohorario->padre != "0" ? $nuevohorario->padre : $nuevohorario->getCodigo();

                foreach ($arrDiasRepetir as $dia) {

                    $horarios = $this->guardarHorario($conexion, -1, $arrdatos["comision"], $arrdatos["salon"], $arrdatos["profesor"], $arrdatos["materia"], $dia, $arrdatos["horad"], $arrdatos["horah"], '0', $relacion, $arrdatos["usuario"]);

                    //RETORNO PARA QUE EL CALENDARIO DIBUJE
                    $arrRespuesta["nuevo"][] = $this->getFormatEvento($conexion,null, $horarios);
                }
                $bajaferiados = $this->BajaHorariosFeriados($conexion, $arrdatos["diad"], $arrdatos["finaliza_valor"], $arrdatos["horad"], $arrdatos["horah"]);

                foreach ($bajaferiados as $value) {
                    $arrRespuesta["unset"][]["id"] = $value['codigo'];
                }
//                print_r($arrdatos);
                $parametrosasis = array('cod_estado_academico' => '', 'cod_comision' => $arrdatos["comision"], 'cod_materia' => $arrdatos["materia"], 'fecha' => '');
                $objtarecron = new Vtareas_crons($conexion);
                $objtarecron->guardar('calcular_asistencia', $parametrosasis, $this->codigo_filial);

                $conexion->trans_complete();
                $repuesta = $conexion->trans_status();
            } else {

                $repuesta = 2;
                $conexion->trans_rollback();

                $arrRespuesta = $respuestaSuperpo;
            }
        } else {
            $repuesta = 3;
//            $conexion->trans_rollback();
//
            $arrRespuesta = $respuestaAsistencia;
        }

        return class_general::_generarRespuestaModelo($conexion, $repuesta, $arrRespuesta);
    }

    private function getDiasRepeticion($arrdatos) {

        $arrDiasRepetir = array();
        $fecha = $arrdatos["diad"];
        switch ($arrdatos["tipo_repeticion"]) {

            case "0":

                break;
            case "1":

                $parar = false;

                $dia = $ndia = date("N", strtotime($fecha));
                $fecha = date("Y-m-d", strtotime($fecha));

                if ($arrdatos["diad"] != $arrdatos["finaliza_valor"]) {

                    while ($parar === false) {
                        $suma = " + 1 day";
                        $ndia = date("N", strtotime($fecha . $suma));

                        if ($ndia === $dia && $arrdatos["repetir_cada"] > '1') {
                            $cantdias = (7 * ($arrdatos["repetir_cada"] - 1)) + 1;
                            $sumafecha = " + " . $cantdias . " day";
                            $datefecha = strtotime($fecha . $sumafecha);
                            $fecha = date("Y-m-d", $datefecha);
                        } else {
                            $datefecha = strtotime($fecha . $suma);
                        }

                        $fecha = date("Y-m-d", $datefecha);

                        if (in_array($ndia, $arrdatos["dia_repetir"])) {
                            $arrDiasRepetir[] = $fecha;
                        }

                        switch ('1') {//$arrdatos["finaliza"]
                            case "0":

                                if (count($arrDiasRepetir) === $arrdatos["finaliza_valor"]) {
                                    $parar = true;
                                }

                                break;
                            case "1":

                                $fecha_finaliza = strtotime($arrdatos["finaliza_valor"]);
                                if ($datefecha >= $fecha_finaliza) {



                                    $parar = true;
                                }

                                break;
                        }
                    }
                }
                break;
        }
        return $arrDiasRepetir;
    }

    public function bajaHorario($id_horario, $arrayOption, $conexionN = null) {
        if ($conexionN == null) {
            $conexion = $this->load->database($this->codigo_filial, true);
            $conexion->trans_start();
        } else {
            $conexion = $conexionN;
        }
        $unsetrepuesta = array();
        $unset = array();
        $respuestaAsistencia = '';
        if($arrayOption["soloeste"] === false){
            $modserie = $arrayOption["soloeste"] == 'false' ? true : false;
            $respuestaAsistencia = $this->validaPosibilidadModificar($conexion, $id_horario, $modserie);
        }else{
            $respuestaAsistencia["modifica"] = true;
        }
        
        
        if ($respuestaAsistencia["modifica"] === TRUE) {

            switch ($arrayOption["soloeste"]) {
                case "true":
                    $horarios = new Vhorarios($conexion, $id_horario);
                    $horarios->unSetHorario();
                    break;
                case "false":
                    $horarios = new Vhorarios($conexion, $id_horario);
                    $unset = $horarios->unSetRelacionados();
                    $horarios->unSetHorario();

                    break;
                default:
                    break;
            }

            $unsetrepuesta[]["id"] = $horarios->getCodigo();
            foreach ($unset as $u) {
                $unsetrepuesta[]["id"] = $u["codigo"];
            }

            $parametrosasis = array('cod_estado_academico' => '', 'cod_comision' => $horarios->cod_comision, 'cod_materia' => $horarios->cod_materia, 'fecha' => '');
            $objtarecron = new Vtareas_crons($conexion);
            $objtarecron->guardar('calcular_asistencia', $parametrosasis, $this->codigo_filial);

            $retorno["unset"] = $unsetrepuesta;
            if ($conexionN == null) {
                $conexion->trans_complete();
            }

            $respuesta = $conexion->trans_status();
        } else {
            $respuesta = 3;
            if ($conexionN == null) {
                $conexion->trans_rollback();
            }
            $retorno["asistencia"] = $respuestaAsistencia;
        }

        return class_general::_generarRespuestaModelo($conexion, $respuesta, $retorno);
    }

    public function exitenEventosCorrelativos($codigo_horario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $horarios = new Vhorarios($conexion, $codigo_horario);
        return $horarios->exitenCorrelativo();
    }

    public function getSerieDias($codigo_horario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $horarios = new Vhorarios($conexion, $codigo_horario);
        return $horarios->getDiasSerie();
    }

    public function getFechaFinSerie($codigo_horario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $horarios = new Vhorarios($conexion, $codigo_horario);
        return $horarios->getFechaFinSerie();
    }

    private function validaExisteEnRango($conexion, $codcomision, $codmateria, $arrValidar) {
        $condiciones = array("cod_comision" => $codcomision, "cod_materia" => $codmateria, "baja" => "0");

        $arrHorarios = $horariosAsignados = Vhorarios::listarHorarios($conexion, $condiciones);

        $arrRespuesta["existe"] = false;
        foreach ($arrHorarios as $rhorario) {

            foreach ($arrValidar as $rValidar) {
                $date1 = $rValidar["dia"] . " " . $rValidar["horad"];
                $date2 = $rValidar["dia"] . " " . $rValidar["horah"];
                $date3 = $rhorario["dia"] . " " . $rhorario["horadesde"];
                $date4 = $rhorario["dia"] . " " . $rhorario["horahasta"];
                
                $resp = ($date2 > $date3 && $date2 < $date4) || ($date4 > $date1 && $date4 < $date2);
                
                if ($resp) {
                    if ($rValidar["codigo"] <> $rhorario["codigo"]) {
                        $arrRespuesta["COD1"] = $rValidar["codigo"];
                        $arrRespuesta["COD2"] = $rhorario["codigo"];
                        $arrRespuesta["existe"] = true;
                        $arrRespuesta["dia"] = $date3;
                        $arrRespuesta["dia2"] = $date4;
                        return $arrRespuesta;
                    }
                }
            }
        }
        return $arrRespuesta;
    }

    private function UnsetArray($unset) {
        $unsetrepuesta = array();
        foreach ($unset as $u) {
            $unsetrepuesta[]["id"] = $u["codigo"];
        }

        return $unsetrepuesta;
    }

    /**
     * La siguiente funcion agrega el idFilial porque es llamda desde el web services
     */
    public function getAsistencias($idFilial, $codMateria = null, $codComision = null, $fechaDesde = null, $fechaHasta = null,
            $vista=false, $filtar_no_cursa=false, $baja = false, array $estadoEstadoAcademico = null) {
        $conexion = $this->load->database($idFilial, true);
        
        $this->load->helper('alumnos');
        $arrAsistencias = Vmatriculas_horarios::getAsistenciaComision($conexion, $codMateria, $codComision, $fechaDesde, 
                $fechaHasta, true, $vista, $filtar_no_cursa, $baja, $estadoEstadoAcademico);
        $arrResp = array();
        $arrFechas = array();
        $arrMatriculas = array();
        $arrHorarios = array();
        $arrEstadosAcademicos = array();
        $arrPorcAsistencia = array();
        foreach ($arrAsistencias as $asistencia) {
            if ($asistencia['baja'] == 0) {
                $arrResp['asistencia'][$asistencia['dia']][$asistencia['cod_materia']][$asistencia['cod_comision']][$asistencia['cod_horario']][$asistencia['matricula_codigo']] = $asistencia['estado'];
            } else {
                $arrExcepcion = Vmatriculas_horarios::listarExcepciones($conexion, $arrExcepciones[] = $asistencia['codigo']);
             
                if (count($arrExcepcion) > 0) {
                    $arrResp['asistencia'][$asistencia['dia']][$asistencia['cod_materia']][$asistencia['cod_comision']][$asistencia['cod_horario']][$asistencia['matricula_codigo']]["excepcion"] = $arrExcepcion;
                } else {
                    $arrResp['asistencia'][$asistencia['dia']][$asistencia['cod_materia']][$asistencia['cod_comision']][$asistencia['cod_horario']][$asistencia['matricula_codigo']]["cambio_comision"] = array();
                }
            }            
            if (!in_array($asistencia['dia'], $arrFechas)){
                $arrFechas[] = $asistencia['dia'];
            }
            $arrMatriculas[$asistencia['matricula_codigo']] = inicialesMayusculas($asistencia['alumno_nombre']);
            $arrHorarios[$asistencia['cod_horario']]['horadesde'] = $asistencia['horadesde'];
            $arrHorarios[$asistencia['cod_horario']]['horahasta'] = $asistencia['horahasta'];
            $arrPorcAsistencia[$asistencia['matricula_codigo']] = $asistencia['porcasistencia'];
            $arrEstadosAcademicos[$asistencia['matricula_codigo']] = $asistencia['estado_academico'];
        }
        $arrResp['dias'] = $arrFechas;
        $arrResp['matriculas'] = $arrMatriculas;
        $arrResp['estados_academicos'] = $arrEstadosAcademicos;
        $arrResp['horarios'] = $arrHorarios;
        $arrResp['info'] = "asistencia_format:{dia}{cod_materia}{cod_comision}{cod_horario}{cod_matricula}=estado";
        $arrResp['porcasistencia'] = $arrPorcAsistencia;
        return $arrResp;
    }

    private function validaPosibilidadModificar($conexion, $codhorario, $modificaserie) {

        $respuesta = array();
        $respuesta['modifica'] = TRUE;

        if ($codhorario != -1) {
            $horario = new Vhorarios($conexion, $codhorario);
            $i = 0;
            if ($modificaserie) {
                if ($horario->padre == "0") {//es padre
                    $padre = $horario->getCodigo();
                } else {
                    $padre = $horario->padre;
                }
                $condiciones = array('padre' => $padre, 'dia >' => $horario->dia,"baja"=>0);
                $horariosRelacionados = Vhorarios::listarHorarios($conexion, $condiciones);

                if ($horario->padre == "0") {
                    $horariosRelacionados[]['codigo'] = $horario->getCodigo();
                }

                foreach ($horariosRelacionados as $rowhorario) {
                    $hora = new Vhorarios($conexion, $rowhorario['codigo']);
                    $respuesta[$i]['horario'] = $hora;
                    $respuesta[$i]['asistencia'] = $hora->getAsistenciaCargada();

                    if (count($respuesta[$i]['asistencia']) > 0) {
                        $respuesta['modifica'] = FALSE;
                    }
                    $i++;
                }
            } else {
                $respuesta[$i]['horario'] = $horario;
                $respuesta[$i]['asistencia'] = $horario->getAsistenciaCargada();
                if (count($respuesta[$i]['asistencia']) > 0) {
                    $respuesta['modifica'] = FALSE;
                }
            }
        }
        return $respuesta;
    }

    public function BajaHorariosFeriados(CI_DB_mysqli_driver $conexion, $fechadesde = null, $fechahasta = null, $horadesde = null, $horahasta = null) {
        $condiciones = array("baja"=>0);
        $todosferiados = Vferiados::listarFeriados($conexion,$condiciones);
        $feriados = array();

//        BUSCA FERIADOS DEL PERIODO
//        
//        $fechahasta = $fechahasta == false || $fechahasta == null ? $fechadesde : $fechahasta;
//
//        if ($fechadesde != null && $fechahasta != null) {
//            $date_parts1 = explode("-", $fechadesde); //0 Y 1 m 2 d
//            $date_parts2 = explode("-", $fechahasta);
//            $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
//            $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
//
//            foreach ($todosferiados as $feriado) {
//                if ($feriado['anio'] <= $date_parts1[0] && $feriado['repite'] == 1) {
//                    $feriado['anio'] = $date_parts1[0];
//                }
//                $fechaferiado = gregoriantojd($feriado['mes'], $feriado['dia'], $feriado['anio']);
//                if ($fechaferiado >= $start_date && $fechaferiado <= $end_date) {
//
//                    if ($feriado['hora_desde'] == '00:00:00' && $feriado['hora_hasta'] == '00:00:00') {
//
//                        $feriados[] = $feriado;
//                    } else {
//
//                        if ($feriado['hora_desde'] <= $horadesde && $feriado['hora_hasta'] >= $horadesde || $feriado['hora_desde'] >= $horadesde && $feriado['hora_hasta'] <= $horahasta || $feriado['hora_desde'] <= $horahasta && $feriado['hora_hasta'] >= $horahasta || $feriado['hora_desde'] >= $horadesde && $feriado['hora_hasta'] <= $horahasta) {
//                            $feriados[] = $feriado;
//                        }
//                    }
//                }
//            }
//        }
        $horariosbaja = array();
        if (count($todosferiados) > 0) {
            $m = 0;
            foreach ($todosferiados as $rowferiado) {
                $fechafer = date('Y-m-d', strtotime($rowferiado['anio'] . '-' . $rowferiado['mes'] . '-' . $rowferiado['dia']));

                $arrhorariosbaja = Vhorarios::getHorarios($conexion, $fechafer, $rowferiado['hora_desde'], $rowferiado['hora_hasta'], $rowferiado['repite'], '0');
                foreach ($arrhorariosbaja as $value) {
                    $horariosbaja[$m] = $value;
                    $m++;
                }
            }
        }
        foreach ($horariosbaja as $rowhorario) {
            $arropcion = array('soloeste' => true);
            $this->bajaHorario($rowhorario['codigo'], $arropcion, $conexion);
        }
        return $horariosbaja;
    }

    static function guardarHorario($conexion, $codigo, $comision, $salon, $arrprofesores, $materia, $dia, $horadesde, $horahasta, $baja, $padre, $codusuario) {
        $nuevohorario = new Vhorarios($conexion, $codigo);
        $nuevohorario->guardarHorario($comision, $salon, $materia, $dia, $horadesde, $horahasta, $baja, $padre, $codusuario);
        if ($arrprofesores != '') {
            foreach ($arrprofesores as $codprofesor) {
                $nuevohorario->setHorarioProfesor($codprofesor);
            }
        }
        return $nuevohorario;
    }

    public function getProfesoresHorario($cod_horario) {

        $conexion = $this->load->database($this->codigo_filial, true);

        $horario = new Vhorarios($conexion, $cod_horario);
        $profeshorarios = $horario->getProfesores();

        $todosprofesores = Vprofesores::listarProfesores($conexion);
        for ($i = 0; $i < count($todosprofesores); $i++) {
            foreach ($profeshorarios as $value) {
                if ($value['codigo'] == $todosprofesores[$i]['codigo']) {
                    $todosprofesores[$i]['selec'] = true;
                }
            }
        }
        return $todosprofesores;
    }

    public function getHorariosCambiar($codhorario, $arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('formatearfecha');
        $arrSearch = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrSearch = array(
                "comisiones.nombre" => $arrFiltros["sSearch"],
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

        $arrSort[] = array(
            "0" => 'horarios.dia',
            "1" => 'asc');

        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {

            $arrSort[] = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }

        $horario = new Vhorarios($conexion, $codhorario);
        $arrhorarios = $horario->getHorariosExcepciones($arrSearch, $arrLimit, $arrSort);
        $contar = $horario->getHorariosExcepciones('', '', '', true);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $datos = array();
        for ($i = 0; $i < count($arrhorarios); $i++) {
            $nrodia = date('N', strtotime($arrhorarios[$i]['dia']));
            $hs = array($nrodia => array('desde' => $arrhorarios[$i]['horadesde'], 'hasta' => $arrhorarios[$i]['horahasta']));

            $datos[] = array($arrhorarios[$i]['codigo'],
                formatearFecha_pais($arrhorarios[$i]['dia']),
                horario($hs),
                $arrhorarios[$i]['nombre']
            );
        }

        $retorno['aaData'] = $datos;

        return $retorno;
    }

    public function getHorario($cod_horario) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $objhorario = new Vhorarios($conexion, $cod_horario);
        $horario = $objhorario->getHorario();
        $comision = new Vcomisiones($conexion, $horario[0]['cod_comision']);

        $horario[0]['nombre'] = $comision->nombre;
        $horario[0]['materia'] = $horario[0]['nombre_' . get_idioma()];

        $nrodia = date('N', strtotime($horario[0]['dia']));
        $hs = array($nrodia => array('desde' => $horario[0]['horadesde'], 'hasta' => $horario[0]['horahasta']));
        $horario[0]['horario'] = horario($hs);
        $horario[0]['dia'] = formatearFecha_pais($horario[0]['dia']);
        return $horario[0];
    }
    
    public function getObjFeriado($cod_feriado){
        $conexion = $this->load->database($this->codigo_filial,true);
        $myFeriado =  new Vferiados($conexion, $cod_feriado);
        $mes = strlen($myFeriado->mes) == 1 ? '0'.$myFeriado->mes : $myFeriado->mes;
        $dia = strlen($myFeriado->dia) == 1 ? '0'.$myFeriado->dia : $myFeriado->dia;
        $año = substr($myFeriado->anio, 2);
        $myFeriado->dia = $dia;
        $myFeriado->mes= $mes;
        $myFeriado->año = $año;
        return $myFeriado;
    }
    
    public function getHorariosDiaComisionMateria($cod_comision, $cod_materia, $dia){
        $conexion = $this->load->database($this->codigo_filial,true,null,true);
        $this->load->helper('alumnos');
        
        $horarios = Vhorarios::getHorariosDiaComisionMateria($conexion, $cod_comision, $cod_materia, $dia);
        
        foreach($horarios as $key=>$horario){
            $mySalon = new Vsalones($conexion, $horario['cod_salon']);
            $nombreSalon = $mySalon->salon;
            $nrodia = date('N', strtotime($horario['dia']));
            $hs = array($nrodia => array('desde' => $horario['horadesde'], 'hasta' => $horario['horahasta']));
            $horarios[$key]['horario'] = horario($hs).' '.lang('salon').' '.inicialesMayusculas($nombreSalon);
        }
        
        return $horarios;
    }
    
    public function validarAsistenciaHorario($cod_horario){
        $conexion = $this->load->database($this->codigo_filial,true);
        $condiciones = array(
            "cod_horario"=>$cod_horario,
            "estado <>"=>'',
            "baja"=>0
        );
        $listarMatriculasHorarios = Vmatriculas_horarios::listarMatriculas_horarios($conexion, $condiciones);
        if(count($listarMatriculasHorarios) > 0){
            return true;
        }else{
            return false;
        }
    }
    
    public function configuracionDiasFilial(){
        $conexion = $this->load->database($this->codigo_filial,true);
        
        $configuracionHorarios = Vconfiguracion::getValorConfiguracion($conexion, null, 'HorariosDeAtencion');
        $diasHabilitados = array();
        foreach($configuracionHorarios as $dia=>$horarios){
            
                switch ($dia) {
                    case 'domingo':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] = 0;
                        }
                        break;
                    case 'lunes':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] = 1;
                        }
                        break;
                    case 'martes':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] =  2;
                        }
                        break;
                    case 'miercoles':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] = 3;
                        }
                        break;
                    case 'jueves':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] =  4;
                        }
                        break;
                    case 'viernes':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] = 5;
                        }
                        break;
                    case 'sabado':
                        if($horarios['cerrado'] == 1){
                            $diasHabilitados[] = 6;
                        }
                        break;

                    default:
                        break;
                }
        }
        
         return json_encode($diasHabilitados);
    }
    
    public function getHorariosFilial(){
        $conexion = $this->load->database($this->codigo_filial,true);
        
        $configuracionHorarios = Vconfiguracion::getValorConfiguracion($conexion, null, 'HorariosDeAtencion');
       
        $menoresHorarios = array();
        $mayoresHorarios = array();
        foreach($configuracionHorarios as $key=>$horarios){
            if(isset($horarios['e1']) && $horarios['e1'] != ''){
                $menoresHorarios[] = $horarios['e1'];
           }
           if($configuracionHorarios['doble_horario'] == 1){
              if(isset($horarios['e2']) && $horarios['e2'] != ''){
                $menoresHorarios[] = $horarios['e2'];
            } 
           }
            
            
             if(isset($horarios['s1']) && $horarios['s1'] != ''){
                $mayoresHorarios[] = $horarios['s1'];
           }
           if($configuracionHorarios['doble_horario'] == 1){
            if(isset($horarios['s2']) && $horarios['s2'] != ''){
                $mayoresHorarios[] = $horarios['s2'];
            }
           }
        }
        $arrRetorno['menor_horario'] = min($menoresHorarios);
        $arrRetorno['mayor_horario'] = max($mayoresHorarios);
      return $arrRetorno;
    }
    
    
    //script para actualizar los colores de los salones del sist.
    public function actualizarColorSalon($tipo_salon){
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
     
        
        foreach($arrFiliales as $filial){
                $conexion = $this->load->database($filial['codigo'], true);
                
                $condiciones = array(
                    "tipo"=>$tipo_salon,
                    "estado"=>0
                );
                if($tipo_salon == 'COCINA'){
                   $arrColores = array("#0072ac","#66aacd","#bfdcea","#338ebd","#99c7de");
               }else{
                   $arrColores = array("#e7af19","#f1cf75","#f9ebc5","#ecbf47","#f5dfa3");
               }
                $salonesTipo = Vsalones::listarSalones($conexion, $condiciones);
               
               foreach($salonesTipo as $key=>$salon){
                  $mySalon = new Vsalones($conexion,$salon['codigo']);
                    $mySalon->updateColorSalon($arrColores[$key]);
               }    
        }
           
        }
    
    
    public function getDetalleHorariosProfesores($where_in){
        $conexion = $this->load->database($this->codigo_filial,true,null,true);
        $this->load->helper('alumnos');
        $horarios = Vhorarios::getDetalleHorariosProfesores($conexion, $where_in);
         foreach($horarios as $key=>$horario){
            $mySalon = new Vsalones($conexion, $horario['cod_salon']);
            $nombreSalon = $mySalon->salon;
            $nrodia = date('N', strtotime($horario['dia']));
            $hs = array($nrodia => array('desde' => $horario['horadesde'], 'hasta' => $horario['horahasta']));
            $horarios[$key]['horario'] = horario($hs).' '.lang('salon').' '.inicialesMayusculas($nombreSalon);
        }
        return $horarios;
    }
    
   public function guardarProfesorHorario($data_post){
       $conexion = $this->load->database($this->codigo_filial,true);
       $conexion->trans_start();
       $myHorarios = new Vhorarios($conexion, $data_post['cod_horario']);
       if($data_post['accion'] == 'insert'){
           $myHorarios->setHorarioProfesor($data_post['cod_profesor']);
       }else{
           $myHorarios->updateHorarioProfesor($data_post['cod_profesor']);
       }
       $estadotran = $conexion->trans_status();

        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
   }
        
    
    
 
     

}

/* End of file model_horarios.php */
/* Location: ./application/models/model_horarios.php */
