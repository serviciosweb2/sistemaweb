<?php

/**
 * Model_planes_academicos
 * 
 * Description...
 * 
 * @package model_planes_academicos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_planes_academicos extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
        //var_dump($this->codigo_filial);    
    }

    public function getPeriodos($modalidad = false) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $plan = new Vplanes_academicos($conexion, $this->codigo);
        $planesPeriodos = $plan->getPeriodos();

        $periodos = array();

        for ($i = 0; $i < count($planesPeriodos); $i++) {
            $solo = false;
            if ($planesPeriodos[$i]['padre'] == null) {
                $solo = true;
            }

            $periodos[$i] = array(
                'cod_tipo_periodo' => $planesPeriodos[$i]['cod_tipo_periodo'],
                'nombre' => lang($planesPeriodos[$i]['nombre']),
                'solo' => $solo,
                'horas' => $planesPeriodos[$i]['hs_reloj'],
                'cod_titulo' => $planesPeriodos[$i]['cod_titulo'],
                'color' => $planesPeriodos[$i]['color'],
                'orden' => $planesPeriodos[$i]['orden']);

            if ($modalidad) {
                $modalidades = $plan->getPeriodosModalidadesFilial($this->codigo_filial, $planesPeriodos[$i]['cod_tipo_periodo']);
                $periodos[$i]['modalidad'] = $modalidades;
            }
        }

        return $periodos;
    }

    public function getComisionesDisponiblesMatricular($periodo, $modalidad = null) {

        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('comisiones');
        $plan = new Vplanes_academicos($conexion, $this->codigo);
        $capacidad = Vconfiguracion::getValorConfiguracion($conexion, null, 'CapacidadComision');
        $nbeviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
        $habilitaSinCupo = Vconfiguracion::getValorConfiguracion($conexion, null, 'comisionesSinCupo');

        $modalidad = $modalidad == 'intensiva' || $modalidad == 'normal' ? $modalidad : null;
        $comisiones = $plan->getComisiones(1, true, false, $periodo, true, $modalidad, null, true);

        foreach ($comisiones as $key => $row) {
            $objComision = new Vcomisiones($conexion, $row['codigo']);
            $arrcupo = $objComision->getCapacidad();
            $cupo = $arrcupo[0]['cupo'];

            $nombreCom = $row['nombre'];

            $capacidadcomi = $cupo != '-1' ? $cupo : 0;
            $disponible = $capacidadcomi - $row['inscriptos'];
            $comisiones[$key]['nombre'] = $nombreCom;
            $comisiones[$key]['nombre'] .= $nbeviejo == '1' ? ' (' . $row['descripcion'] . ')' : '';
            if ($capacidad == '1') {
                $comisiones[$key]['nombre'].=' / ';
                $comisiones[$key]['nombre'].=$cupo != '-1' ? lang('cupo') . ': ' . $disponible : lang('sin_horario');
            }
            $comisiones[$key]['tiene_horarios'] = $cupo != '-1' ? true : false;
            $comisiones[$key]['habilita'] = $habilitaSinCupo == '1' || $disponible > 0 ? true : false;
            $comisiones[$key]['inscriptos'] = $row['inscriptos'];
        }
        return $comisiones;
    }

    public function getPlanAcademico($cod_plan) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $objPlan = new Vplanes_academicos($conexion, $cod_plan);
        return $objPlan;
    }

    public function getCurso($cod_plan) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $objPlan = new Vplanes_academicos($conexion, $cod_plan);
        return new Vcursos($conexion, $objPlan->cod_curso);
    }

    public function getMateriasDatatable($codplan, $conplanperiodo = false) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $plan = new Vplanes_academicos($conexion, $codplan);
        $materias = $plan->getMaterias();

        $Periodos = array();
        $a = 0;
        $periodo = '';

        for ($i = 0; $i < count($materias); $i++) {

            $nombreperiodo = Vtipos_periodos::getNombre($conexion, $materias[$i]['cod_tipo_periodo']);

            $a = $nombreperiodo != $periodo ? 0 : $a + 1;

            $periodo = $nombreperiodo;

            $Periodos[$periodo]['materias'][$a]['codigo'] = $materias[$i]['codigo'];
            $Periodos[$periodo]['materias'][$a]['nombre_es'] = $materias[$i]['nombre_es'];
            $Periodos[$periodo]['materias'][$a]['nombre_in'] = $materias[$i]['nombre_in'];
            $Periodos[$periodo]['materias'][$a]['nombre_pt'] = $materias[$i]['nombre_pt'];
            $Periodos[$periodo]['materias'][$a]['cod_tipo_materia'] = $materias[$i]['cod_tipo_materia'];
        }
        if ($conplanperiodo) {
            foreach ($Periodos as $key => $value) {
                $condiciones = array('nombre' => $key);
                $valueperiodos = Vtipos_periodos::listarTipos_periodos($conexion, $condiciones);
                $planperiodo = $plan->getPeriodos($valueperiodos[0]['codigo']);
                $existe = count($planperiodo) > 0 ? true : false;
                $Periodos[$key]['periodo']['codigo'] = $existe ? $valueperiodos[0]['codigo'] : 0;
                $cod_titulo = $existe ? $planperiodo[0]['cod_titulo'] : '-1';
                $objTitulo = new Vtitulos($conexion, $cod_titulo);
                $Periodos[$key]['periodo']['titulo'] = $objTitulo->nombre;
                $filial = new Vfiliales($conexion, $this->codigo_filial);
                $Periodos[$key]['periodo']['hs_catedra'] = $existe ? round($planperiodo[0]['hs_reloj'] * 60 / $filial->minutos_catedra, 2) : 0;
                $Periodos[$key]['periodo']['modalidades'] = $plan->getPeriodosModalidadesFilial($this->codigo_filial, $Periodos[$key]['periodo']['codigo']);
                foreach ($Periodos[$key]['periodo']['modalidades'] as $k => $value) {
                    $oTitulo = new Vtitulos($conexion, $Periodos[$key]['periodo']['modalidades'][$k]['cod_titulo']);
                    $Periodos[$key]['periodo']['modalidades'][$k]['titulo'] = $oTitulo->nombre;
                }
            }
        }
        return $Periodos;
    }

    /**
     * retorna todas las materias del plan academico.
     * @access public
     * @return repuesta de materias.
     */
    public function getMaterias($codplan) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $plan = new Vplanes_academicos($conexion, $codplan);
        $materias = $plan->getMaterias();
        return $materias;
    }

    public function getNombre($cod_plan) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $objPlan = new Vplanes_academicos($conexion, $cod_plan);
        $objCurso = new Vcursos($conexion, $objPlan->cod_curso);
        $nombre_curso = 'nombre_' . get_idioma();
        $nombre = $objCurso->$nombre_curso;
        $condplanes = array('cod_curso' => $objCurso->getCodigo());
        $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condplanes);
        if (count($planescurso) > 1) {
            $nombre.=' / ' . $objPlan->nombre;
        }
        return $nombre;
    }

    public function getComisiones($codigoPlan) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $plan = new Vplanes_academicos($conexion, $codigoPlan);
        $nombreviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
        $comisiones = $plan->getComisiones(null, false);

        foreach ($comisiones as $key => $row) {
            $comisiones[$key]['nombre'] = $nombreviejo == 1 ? $row['nombre'] . ' ' . '(' . $row['descripcion'] . ')' : $row['nombre'];
        }
        return $comisiones;
    }

    public function getModalidades($cod_plan, $cod_periodo) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $plan = new Vplanes_academicos($conexion, $cod_plan);
        $modalidades = $plan->getPeriodosModalidadesFilial($this->codigo_filial, $cod_periodo);
        $respuesta = array();
        foreach ($modalidades as $row) {
            $respuesta[] = array('codigo' => $row['modalidad'], 'nombre' => lang($row['modalidad']));
        }
        return $respuesta;
    }

    public function getPeriodosPlanAcademico($codplan, $codalumno = null, $modalidad = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $plan = new Vplanes_academicos($conexion, $codplan);
        if ($codalumno != null) {
            $alumno = new Valumnos($conexion, $codalumno);

            $planesPeriodos = $alumno->getPeriodosMatricular($codplan, $this->codigo_filial);
        } else {
            $planesPeriodos = $plan->getPeriodosFilial($this->codigo_filial);
        }

        $periodos = array();

        for ($i = 0; $i < count($planesPeriodos); $i++) {
            $solo = false;
            if ($planesPeriodos[$i]['padre'] == null) {
                $solo = true;
            } else {
                if ($codalumno != null) {
                    $matriculado = $alumno->getMatriculasPeriodosPlanAcademico($codplan);
                    foreach ($matriculado as $rowperiodomatriculado) {
                        if ($rowperiodomatriculado['cod_tipo_periodo'] == $planesPeriodos[$i]['padre']) {
                            $solo = true;
                        }
                    }
                }
            }

            $periodos[$i] = array(
                'cod_tipo_periodo' => $planesPeriodos[$i]['cod_tipo_periodo'],
                'nombre' => lang($planesPeriodos[$i]['nombre']),
                'solo' => $solo,
                'padre' => $planesPeriodos[$i]['padre']);

            if ($modalidad) {
                $modalidades = $plan->getPeriodosModalidadesFilial($this->codigo_filial, $planesPeriodos[$i]['cod_tipo_periodo']);
                foreach ($modalidades as $key => $value) {
                    $modalidades[$key]['nombre_periodo'] = $plan->getNombrePeriodoModalidadFilial($planesPeriodos[$i]['cod_tipo_periodo'], $modalidades[$key]['modalidad'], $this->codigo_filial);
                }
                $periodos[$i]['modalidad'] = $modalidades;
            }
        }
        return $periodos;
    }

    /* La sigjuiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */

    public function getTiposPeriodos() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrPeriodos = Vtipos_periodos::listarTipos_periodos($conexion);
        for ($i = 0; $i < count($arrPeriodos); $i++) {
            $arrPeriodos[$i]['nombre_es'] = lang($arrPeriodos[$i]['nombre']);
        }
        return $arrPeriodos;
    }

    /* La sigjuiente function está siendo accedida desde un Web Services  NO MODIFICAR, COMENTAR NI ELIMINAR */

    public function getPlanAcademicoFiliales($codFilial = null, $codTipoPeriodo = null, $modalidad = null, $estado = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $myPlanAcademico = new Vplanes_academicos($conexion, $this->codigo);
        $arrTemp = $myPlanAcademico->getPlanesFilial($codFilial, $codTipoPeriodo, $modalidad, $estado);
        $arrResp = array();
        foreach ($arrTemp as $tipoPeriodo) {
            $arrResp[$tipoPeriodo['cod_filial']][$tipoPeriodo['cod_tipo_periodo']][$tipoPeriodo['modalidad']]['cant_meses'] = $tipoPeriodo['cant_meses'];
            $arrResp[$tipoPeriodo['cod_filial']][$tipoPeriodo['cod_tipo_periodo']][$tipoPeriodo['modalidad']]['nombre_periodo'] = $tipoPeriodo['nombre_periodo'];
            $arrResp[$tipoPeriodo['cod_filial']][$tipoPeriodo['cod_tipo_periodo']][$tipoPeriodo['modalidad']]['cod_titulo'] = $tipoPeriodo['cod_titulo'];
            $arrResp[$tipoPeriodo['cod_filial']][$tipoPeriodo['cod_tipo_periodo']][$tipoPeriodo['modalidad']]['estado'] = $tipoPeriodo['estado'];
            $arrResp[$tipoPeriodo['cod_filial']][$tipoPeriodo['cod_tipo_periodo']][$tipoPeriodo['modalidad']]['dias_matriculacion'] = $tipoPeriodo['dias_matriculacion'];
        }
        return $arrResp;
    }

}
