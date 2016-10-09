<?php

/**
 * Model_planes_pagos
 * 
 * Planes de pago de cursos.
 * 
 * @package model_planes_pagos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_planes_pagos extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    /**
     * retorna la cuotas de un plan de pago-.
     * @access public
     * @return Array cuotas 
     */
    public function getCuotasPlan($cod_plan, $orden = null, $estado = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $planes = new Vplanes_pago($conexion, $cod_plan);
        $cuotas = $planes->getCuotasPlan($estado, $orden);
        $arrNewCuotas = array();
        foreach ($cuotas as $ct) {
            $arrNewCuotas[$ct['codigo_concepto']][$ct['codigo_financiacion']][] = $ct;
        }
        $arrFinal = array();
        foreach ($arrNewCuotas as $concepto => $financiacion) {
            foreach ($financiacion as $key => $rowfinanciacion) {
                $descuento = $arrNewCuotas[$concepto][$key][0]['descuento'];
                $interes = $arrNewCuotas[$concepto][$key][0]['interes'];
                $suma = 0;
                $suma_neto = 0;
                $arrFinal[$concepto][$key]['cantcuotas'] = $arrNewCuotas[$concepto][$key][0]['numero_cuotas'];
                $arrFinal[$concepto][$key]['nombre'] = '';
                $arrFinal[$concepto][$key]['estado'] = $arrNewCuotas[$concepto][$key][0]['estado'];
                $arrFinal[$concepto][$key]['interes'] = $interes;
                $arrFinal[$concepto][$key]['descuento'] = $descuento;
                $arrFinal[$concepto][$key]['concepto'] = $concepto;
                $arrFinal[$concepto][$key]['limite_primer_cuota'] = $arrNewCuotas[$concepto][$key][0]['limite_primer_cuota'];
                $arrFinal[$concepto][$key]['fecha_limite'] = formatearFecha_pais($arrNewCuotas[$concepto][$key][0]['fecha_limite']);
                $arrFinal[$concepto][$key]['limite_vigencia'] = $arrNewCuotas[$concepto][$key][0]['limite_financiacion'];
                $arrFinal[$concepto][$key]['fecha_vigencia'] = formatearFecha_pais($arrNewCuotas[$concepto][$key][0]['fecha_limite_financiacion']);
                $arrFinal[$concepto][$key]['fecha_hoy'] = formatearFecha_pais(date('Y-m-d'));
                for ($i = 0; $i < count($arrNewCuotas[$concepto][$key]); $i++) {
                    $arrFinal[$concepto][$key]['detalle'][$i]['nrocuota'] = $arrNewCuotas[$concepto][$key][$i]['nro_cuota'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['descuento'] = $arrNewCuotas[$concepto][$key][$i]['descuento'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['interes'] = $arrNewCuotas[$concepto][$key][$i]['interes'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['limite_primer_cuota'] = $arrNewCuotas[$concepto][$key][$i]['limite_primer_cuota'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['fecha_limite'] = $arrNewCuotas[$concepto][$key][$i]['fecha_limite'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['fecha_hoy'] = date('Y-m-d');
                    $arrFinal[$concepto][$key]['detalle'][$i]['valor_neto'] = $arrNewCuotas[$concepto][$key][$i]['valor_neto'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['valor'] = $arrNewCuotas[$concepto][$key][$i]['valor'];
                    $arrFinal[$concepto][$key]['detalle'][$i]['concepto'] = lang(Vconceptos::getKey($conexion, $concepto));
                    $suma_neto += $arrNewCuotas[$concepto][$key][$i]['valor_neto'];
                    $suma = $suma + $arrNewCuotas[$concepto][$key][$i]['valor'];
                }
                $arrFinal[$concepto][$key]['total_neto'] = $suma_neto;
                if ($suma != '0') {
                    if ($descuento != null && $descuento != 0) {
                        if ($descuento == 100) {
                            $suma = 0;
                        } else {
                            $suma = $suma * 100 / (100 - $descuento);
                        }
                    }
                    if ($interes != null && $interes != 0) {
                        $suma = $suma * 100 / (100 + $interes);
                    }
                }
                $arrFinal[$concepto][$key]['total'] = round($suma, 2);
            }
        }
        return $arrFinal;
    }

    public function listarPlanesPagoDataTable($arrFiltros, $planAcademico = null, $modalidad = null, $periodo = null, $baja = null,
            $fechaInicioDesde = null, $fechaInicioHasta = null, $fechaVigenciaDesde = null, $fechaVigenciaHasta = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "planes_pago.nombre" => $arrFiltros["sSearch"],
                "general.cursos.nombre_" . get_idioma() => $arrFiltros["sSearch"],
                "planes_pago.codigo" => $arrFiltros['sSearch']
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
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort[] = array(
                'campo' => $arrFiltros["SortCol"],
                'orden' => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vplanes_pago::listarPlanesDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, false, $planAcademico, 
                $modalidad, $periodo, $baja, $fechaInicioDesde, $fechaInicioHasta, $fechaVigenciaDesde, $fechaVigenciaHasta);
        $contar = Vplanes_pago::listarPlanesDataTable($conexion, $arrCondiciones, null, null, true, $planAcademico, 
                $modalidad, $periodo, $baja, $fechaInicioDesde, $fechaInicioHasta, $fechaVigenciaDesde, $fechaVigenciaHasta);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array(),
            "aoColumns" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $nombrePeriodos = Vplanes_pago::getNombrePeriodos($conexion, $row['codigo'], $this->codigo_filial);
            $nombrePeriodoMostrar = '';
            foreach($nombrePeriodos as $periodo){
                $nombrePeriodoMostrar .= lang($periodo['nombre_periodo']).'['.lang($periodo['modalidad']).'] ';
            }            
            $nombrecurso = $row["nombre_" . get_idioma()] != '' ? $row["nombre_" . get_idioma()] : '';
            $fechainicio = $row["fechainicio"] = '' ? '' : formatearFecha_pais($row["fechainicio"]);
            $fechavigencia = $row["fechavigencia"] == '' ? '' : formatearFecha_pais($row["fechavigencia"]);
            $rows[] = array(
                $row["codigo"],
                $row["nombre"],
                $fechainicio,
                $fechavigencia,
                $nombrecurso,
                $nombrePeriodoMostrar,
                $row["baja"],
                ''
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getPlan($codplan) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $plan = new Vplanes_pago($conexion, $codplan);
        return $plan;
    }

    public function getConceptosPrecios(Vplanes_pago $myPlanPago) {
        $arrConceptosPrecios = $myPlanPago->getConceptosPrecios();
        $arrResp = array();
        foreach ($arrConceptosPrecios as $conceptoPrecio) {
            $arrResp[$conceptoPrecio['cod_concepto']] = $conceptoPrecio['precio_lista'];
        }
        return $arrResp;
    }

    public function getPeriodosPlan(Vplanes_pago $myPlanDePago) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrPeriodos = $myPlanDePago->getCursosPeriodosPlan();
        foreach ($arrPeriodos as $key => $periodo) {
            $myPlanAcademico = new Vplanes_academicos($conexion, $periodo['cod_curso']);
            $temp = $myPlanAcademico->getNombrePeriodoModalidadFilial($periodo['cod_tipo_periodo'], $periodo['modalidad'], $this->codigo_filial);
            $arrPeriodos[$key]['nombre_periodo'] = $temp;
            $arrPeriodos[$key]['nombre_modalidad'] = lang($periodo['modalidad']);
        }
        return $arrPeriodos;
    }

    public function getPlanesAcademicosAsignados(Vplanes_pago $myPlanDePago) {
        $arrPeriodos = $myPlanDePago->getCursosPeriodosPlan();
        $arrResp = array();
        foreach ($arrPeriodos as $periodo) {
            if (!in_array($periodo['cod_curso'], $arrResp)) {
                $arrResp[] = $periodo['cod_curso'];
            }
        }
        return $arrResp;
    }

    public function getFinanciaciones($estado = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array();
        if ($estado != null) {
            $condiciones['estado'] = $estado;
        }
        return Vfinanciacion::listarFinanciacion($conexion, $condiciones, null, array(array("campo" => "numero_cuotas", "orden" => "asc")), array("numero_cuotas"));
    }

    public function guardarPlan($datos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $myPlanPago = new Vplanes_pago($conexion, $datos['plan']['codigo']);
        $myPlanPago->deleteFinanciaciones();
        $myPlanPago->deleteFinanciacionesDescuentos();
        $myPlanPago->descon = $datos['plan']['descuento_condicionado'];
        $myPlanPago->fechainicio = isset($datos['plan']['fecha_inicio']) && $datos['plan']['fecha_inicio'] <> '' ? formatearFecha_mysql($datos['plan']['fecha_inicio']) : date("Y-m-d");
        $myPlanPago->fechavigencia = isset($datos['plan']['fecha_fin']) && $datos['plan']['fecha_fin'] <> '' ? formatearFecha_mysql($datos['plan']['fecha_fin']) : null;
        $myPlanPago->nombre = $datos['plan']['nombre_plan'];
        $myPlanPago->periodo = $datos['plan']['periodicidad'];
        $myPlanPago->baja = "0";
        if ($datos['plan']['codigo'] < 1) {
            $myPlanPago->fechaalta = date("Y-m-d H:i:s");
            $myPlanPago->cod_usuario = $datos['plan']['cod_usuario'];
        }
        $myPlanPago->guardarPlanes_pago();
        $myPlanPago->setPreciosConceptos(1, $datos['plan']['curso_precio_lista']);
        $myPlanPago->setPreciosConceptos(5, $datos['plan']['matricula_precio_lista']);
        foreach ($datos['periodos'] as $periodo) {
            $myPlanPago->setCursoPeriodo($datos['plan']['plan_academico'], $periodo['codigo_periodo'], $periodo['modalidad']);
        }
        foreach ($datos['financiacion'] as $financiacion) {
            if (isset($financiacion['detalle'])) {
                $financiacion['detalle'] = json_decode($financiacion['detalle'], true);
            }
            if (!isset($financiacion['detalle']) || !is_array($financiacion['detalle']) || count($financiacion['detalle']) < 1) {
                $myFinanciacion = new Vfinanciacion($conexion, $financiacion['codigo_financiacion']);
                $detalle = $this->getDetalleNuevo($myFinanciacion->numero_cuotas, $financiacion['valor_neto_concepto'], $financiacion['descuento_financiacion']);
            } else {
                $detalle = $financiacion['detalle'];
            }
            $fechaLimmite = !isset($financiacion['fecha_fecha_limite']) || $financiacion['fecha_fecha_limite'] == '' ? null : formatearFecha_mysql($financiacion['fecha_fecha_limite']);

            $fechaLimiteFinanciacion = !isset($financiacion['fecha_financiacion_limite']) || $financiacion['fecha_financiacion_limite'] == '' ? null : formatearFecha_mysql($financiacion['fecha_financiacion_limite']);

            $myPlanPago->setFinanciacion($financiacion['codigo_financiacion'], $financiacion['concepto_financiacion'], $detalle, $financiacion['descuento_financiacion'], $financiacion['recargo_financiacion'], $financiacion['tipo_fecha_limite'], $fechaLimmite, $financiacion['tipo_fecha_financiacion_limite'], $fechaLimiteFinanciacion);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getDetalleNuevo($cuotas, $valorNeto, $descuento) {
        $arg = array();
        $arg["codigo_filial"] = $this->codigo_filial;
        $this->load->model("Model_configuraciones", "", false, $arg);
        $decimales = 2;
        $valorNetoCuota = round(($valorNeto / $cuotas), $decimales);        
        $calculoRedondeo = $valorNetoCuota * $cuotas;
        $diferenciaDecimales = $valorNeto - $calculoRedondeo; // se calcula el sobrante o faltante de decimales perdidos en el redondeo
        $detalle = array();
        for ($i = 0; $i < $cuotas; $i++) {
            $detalle[$i]['nrocuota'] = $i + 1;
            $valorNetoCuotaGuardar = $i == $cuotas - 1 ? $valorNetoCuota + $diferenciaDecimales : $valorNetoCuota; // por si se cambia y los sobrantes de decimales se quieren aplicar a otra cuota que no sea la última
            $detalle[$i]['valor_neto'] = $valorNetoCuotaGuardar;
            $valorDescuento = round($valorNetoCuotaGuardar - ($valorNetoCuotaGuardar * $descuento / 100), $decimales);
            $detalle[$i]['valor'] = $valorDescuento;
            $detalle[$i]['descuento'] = $descuento;            
        }        
        return $detalle;
    }

    public function cambiarEstado($codplan) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $plan = new Vplanes_pago($conexion, $codplan);
        $plan->baja = $plan->baja == 0 ? 1 : 0;
        $respuesta = $plan->guardarPlanes_pago();
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    public function getPlanesPago($idFilial, $vigente = null) {
        $conexion = $this->load->database($idFilial, true);
        $condiciones = array();
        if ($vigente !== null && $vigente == 1) {
            $conexion->where("fechavigencia >= ", "DATE(NOW())", false);
            $conexion->or_where("fechavigencia IS NULL");
        } else if ($vigente !== null && $vigente == 0) {
            $conexion->where("fechavigencia < ", "DATE(NOW())", false);
            $conexion->where("fechavigencia IS NOT NULL");
        }
        $arrRegistros = Vplanes_pago::listarPlanes_pago($conexion, $condiciones);
        $arrResp = array();
        $arrResp['rows'] = $arrRegistros;
        $arrResp['total_rows'] = count($arrRegistros);
        return $arrResp;
    }

    public function guardarConfigPeriodicidad($periodos, $usuario) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $objConfiguracion = new Vconfiguracion($conexion, 1);
        $guardarConfiguracion = array(
            "key" => 'PeriodoCtacte',
            "value" => json_encode($periodos),
            "fecha_hora" => date('Y-m-d H:i:s')
        );
        $objConfiguracion->setConfiguracion($guardarConfiguracion);
        $objConfiguracion->guardarConfiguracion($usuario);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarConfiguracionesPlanesPago($data_post) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $objConfiguracion = new Vconfiguracion($conexion, 9);
        $arrDiasVigencia = array(
            'key' => 'DiasVigenciaPresupuesto',
            'value' => $data_post['vigencia'],
            'fecha_hora' => date('Y-m-d H:i:s')
        );
        $objConfiguracion->setConfiguracion($arrDiasVigencia);
        $objConfiguracion->guardarConfiguracion($data_post['usuario']);
        $objConfiguracion2 = new Vconfiguracion($conexion, 22);
        $arrDtoCondicionado = array(
            'key' => 'descuentosCondicionados',
            'value' => $data_post['descuentosCondicionados'],
            'fecha_hora' => date('Y-m-d H:i:s')
        );
        $objConfiguracion2->setConfiguracion($arrDtoCondicionado);
        $objConfiguracion2->guardarConfiguracion($data_post['usuario']);
        $objConfiguracion3 = new Vconfiguracion($conexion, 23);
        $arrBajaMorosos = array(
            'key' => 'bajaDirectaMorosos',
            'value' => $data_post['bajaMorosos'],
            'fecha_hora' => date('Y-m-d H:i:s')
        );
        $objConfiguracion3->setConfiguracion($arrBajaMorosos);
        $objConfiguracion3->guardarConfiguracion($data_post['usuario']);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function getPresupuestosVigentes($cod_plan_pago) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objPlanPago = new Vplanes_pago($conexion, $cod_plan_pago);
        $dejarModificar = $objPlanPago->getVigenciasPresupuesto();
        $retorno = 1;
        if (count($dejarModificar) > 0) {
            $retorno = 0;
        }
        return $retorno;
    }

    public function detallePlanPago($idFilial, $codPlan) {
        $conexion = $this->load->database($idFilial, true);
        $myPlanPago = new Vplanes_pago($conexion, $codPlan);
        $arrResp = $myPlanPago->getDetallePlan($conexion);
        for ($i = 0; $i < count($arrResp); $i++) {
            $arrResp[$i]['nombre_concepto'] = lang(Vconceptos::getKey($conexion, $arrResp[$i]['codigo_concepto']));
        }
        return $arrResp;
    }

    public function getCantidadMatriculasPlanPago($idFilial, $codigo) {
        $conexion = $this->load->database($idFilial, true);
        return Vplanes_pago::getCantidadMatriculasPlanPago($conexion, $codigo);
    }
}