<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * model feriados
 */

class Model_feriados extends CI_Model {

    var $codigo = 0;
    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;

        $this->codigofilial = $arg["codigo_filial"];
    }

    /**
     * retorna un objeto feriado
     * @access public
     * @param int $codigo codigo de feriado
     * @return Objeto Feriado
     */
    public function getFeriado($codigo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $feriado = new Vferiados($conexion, $codigo);
        return $feriado;
    }

    /**
     * retorna todos los feriados
     * @access public
     * @return Array feriados
     */
    public function getFeriados($evento = true, $baja = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        if ($baja != null) {
            $condicion = array('baja' => $baja);
            $feriados = Vferiados::listarFeriados($conexion, $condicion);
        } else {
            $feriados = Vferiados::listarFeriados($conexion);
        }

        $feriadosRetorno = array();
        if ($evento) {
            foreach ($feriados as $fr) {
                $allday = ($fr['hora_desde'] == '00:00:00' && $fr['hora_hasta'] == '00:00:00') ? true : false;
                $titulo = ($fr['hora_desde'] == '00:00:00' && $fr['hora_hasta'] == '00:00:00') ? $fr['nombre'] : $fr['nombre'] . ' (' . $fr['hora_desde'] . '-' . $fr['hora_hasta'] . ')';

                if ($fr['repite'] == '1' && $fr['anio'] <= date("Y")) {
                    $aniohasta = date("Y") + 3; //feriado tres años
                    for ($i = date("Y"); $i <= $aniohasta; $i++) {


                        $evento = array(
                            "id" => '',
                            "title" => $titulo,
                            "start" => date('Y-m-d H:i:s', strtotime($i . '-' . $fr['mes'] . '-' . $fr['dia'] . ' ' . $fr['hora_desde'])),
                            "end" => date('Y-m-d H:i:s', strtotime($i . '-' . $fr['mes'] . '-' . $fr['dia'] . ' ' . $fr['hora_hasta'])),
                            "color" => '#000000',
                            "allDay" => $allday,
                            "cod_salon" => '',
                            "cod_comision" => '',
                            "cod_materia" => '',
                            "nombre_comision" => '',
                            "nombre_curso" => '',
                            "editar" => false,
                            "tipo" => 'FERIADO'
                        );
                        $feriadosRetorno[] = $evento;
                    }
                } else {
                    $evento = array(
                        "id" => '',
                        "title" => $titulo,
                        "start" => date('Y-m-d H:i:s', strtotime($fr['anio'] . '-' . $fr['mes'] . '-' . $fr['dia'] . ' ' . $fr['hora_desde'])),
                        "end" => date('Y-m-d H:i:s', strtotime($fr['anio'] . '-' . $fr['mes'] . '-' . $fr['dia'] . ' ' . $fr['hora_hasta'])),
                        "color" => '#000000',
                        "allDay" => $allday,
                        "cod_salon" => '',
                        "cod_comision" => '',
                        "cod_materia" => '',
                        "nombre_comision" => '',
                        "nombre_curso" => '',
                        "editar" => false,
                        "tipo" => 'FERIADO'
                    );
                    $feriadosRetorno[] = $evento;
                }
            }
        } else {
            $i = 0;
            foreach ($feriados as $rowferiado) {
                $feriadosRetorno[$i]['codigo'] = $rowferiado['codigo'];
                $feriadosRetorno[$i]['nombre'] = $rowferiado['nombre'];
                $feriadosRetorno[$i]['fecha'] = formatearFecha_pais(date('Y-m-d', strtotime($rowferiado['anio'] . '-' . $rowferiado['mes'] . '-' . $rowferiado['dia'])));
                $feriadosRetorno[$i]['repite'] = $rowferiado['repite'];
                $feriadosRetorno[$i]['baja'] = $rowferiado['baja'];
                $feriadosRetorno[$i]['horario'] = $rowferiado['hora_desde'] == null && $rowferiado['hora_hasta'] == null ? lang('FERIADO_COMPLETO') : lang('DE') . ' ' . $rowferiado['hora_desde'] . ' ' . lang('A') . ' ' . $rowferiado['hora_hasta'];
                $i++;
            }
        }

        return $feriadosRetorno;
    }

    /**
     * guarda un feriado con todo lo que corresponde
     * @access public
     * @param Array $arrFeriado todos los datos que salen del formulario.
     * @return repuesta Guardar
     */
    public function guardarFeriado($arrFeriado) {

        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        $feriados = new Vferiados($conexion, $arrFeriado['cod_feriado']);

        $dia = date('d', strtotime($arrFeriado['fecha']));
        $mes = date('m', strtotime($arrFeriado['fecha']));
        $anio = date('Y', strtotime($arrFeriado['fecha']));
        $horadesde = $arrFeriado['diacompleto'] == 'on' ? '00:00:00' : date('H:i:s', strtotime($arrFeriado['hora_desde']));
        $horahasta = $arrFeriado['diacompleto'] == 'on' ? '00:00:00' : date('H:i:s', strtotime($arrFeriado['hora_hasta']));
        $respuesta = $feriados->guardar($arrFeriado['nombre'], $dia, $mes, $arrFeriado['usuario'], $anio, $arrFeriado['repite'], $horadesde, $horahasta);

        //habria que ver si la fecha es anterior a hoy y ahi si guardo asistencia
        $parametrosasis = array('cod_estado_academico' => "", 'cod_comision' => '', 'cod_materia' => '', 'fecha' => "{$anio}-{$mes}-{$dia}");
        $objtarecron = new Vtareas_crons($conexion);
        $objtarecron->guardar('calcular_asistencia', $parametrosasis, $this->codigofilial);

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function cambioEstado($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        $feriado = new Vferiados($conexion, $datos['cod_feriado']);

        $respuestaCustom = $feriado->baja($datos);

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuestaCustom);
    }

}
