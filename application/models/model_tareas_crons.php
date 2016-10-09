<?php

/**
 * Model_tareas_crons
 * 
 * Description...
 * 
 * @package model_tareas_crons
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_tareas_crons extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }

    public function ejecutarTareasCrons() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));

        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $tareas = Vtareas_crons::getTareasCronsEjecutar($conexion, $filial['codigo']);
            print_r($tareas);
            foreach ($tareas as $rowtareas) {
                $objTareaCron = new Vtareas_crons($conexion, $rowtareas['codigo']);
                $config2 = array();
                $config2['filial']['codigo'] = $filial['codigo'];
                $config['codigo_filial'] = $filial['codigo'];
                switch ($rowtareas['nombre']) {
                    case 'calcular_asistencia':
                        $this->load->model("Model_estadoacademico", "", false, $config2);
                        $js = json_decode($rowtareas['parametros'], TRUE);
                        $objTareaCron->setEnEjecucion();
                        $resultado = $this->Model_estadoacademico->calcularAsistencia($js['cod_estado_academico'], $js['cod_comision'], $js['cod_materia'], $js['fecha']);

                        if ($resultado['codigo'] == 1) {
                 
                            $objTareaCron->setCompleta();
                        } else {
         
                            $objTareaCron->setError();
                        }
                        break;
                    case 'calcular_mora':

                        $this->load->model("Model_ctacte", "", false, $config);
                        $js = json_decode($rowtareas['parametros'], TRUE);
                        $objTareaCron->setEnEjecucion();
                        $resultado = $this->Model_ctacte->calcular_mora($js['cod_alumno']);

                        if ($resultado['codigo'] == 1) {
                            $objTareaCron->setCompleta();
                        } else {
                            $objTareaCron->setError();
                        }
                        break;
                    case 'alta_campus':

                        $this->load->model("Model_alumnos", "", false, $config);
                        $js = json_decode($rowtareas['parametros'], TRUE);
                        $objTareaCron->setEnEjecucion();
                        $resultado = $this->Model_alumnos->alertaLoginCampus($js['cod_alumno']);

                        if ($resultado['codigo'] == 1) {
                            $objTareaCron->setCompleta();
                        } else {
                            $objTareaCron->setError();
                        }
                        break;
                    case 'conciliar_cobros':

                        $this->load->model("Model_cobros", "", false, $config);
                        $objTareaCron->setEnEjecucion();
                        $resultado = $this->Model_cobros->conciliarCobros($objTareaCron->cod_filial);

                        if ($resultado['codigo'] == 1) {
                            $objTareaCron->setCompleta();
                        } else {
                            $objTareaCron->setError();
                        }
                        break;
                    default:
                        break;
                }
            }
            $estadotran = $conexion->trans_status();
            if ($estadotran === FALSE) {
                $conexion->trans_rollback();
            } else {
                $conexion->trans_commit();
            }
        }
        $conexion->where("estado", "completo");
        $conexion->where("DATE(fecha_hora) < DATE_ADD(CURDATE(),INTERVAL -7 DAY)"); //ver, me parece que borra todo
        $conexion->delete("general.tareas_crons");
    }

}
