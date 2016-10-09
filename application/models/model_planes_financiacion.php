<?php

/**
 * Model_planes_financiacion
 * 
 * Description...
 * 
 * @package model_planes_financiacion
 * @author vane
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_planes_financiacion extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getDetalle($codplan, $codconcepto, $codfinanciacion, $fechapago = null, $moneda) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $plan = new Vplanes_pago($conexion, $codplan);
        $arrConfig["codigo_filial"] = $this->codigo_filial;
        $this->load->model("Model_configuraciones", "", false, $arrConfig);
        $periodosTemp = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte');
        foreach ($periodosTemp as $periodos){
            if ($periodos['codigo'] == $plan->periodo){
                $periodo =  $periodos['valor'].' '.$periodos['unidadTiempo'];
            }
        }
        $arrDetalle = array();

        $condiciones = array(
            'codigo_plan' => $codplan,
            'codigo_financiacion' => $codfinanciacion,
            'codigo_concepto' => $codconcepto
        );

        $planesfinanciacion = Vplanes_financiacion::listarPlanes_financiacion($conexion, $condiciones);

        $vencimiento = $fechapago == null ? date('Y-m-d') : $fechapago;
        $i = 0;
        foreach ($planesfinanciacion as $rowfinanciacion) {
            $arrDetalle[$i]['concepto'] = lang(Vconceptos::getKey($conexion, $codconcepto));
            $arrDetalle[$i]['nrocuota'] = $rowfinanciacion['nro_cuota'];
            $arrDetalle[$i]['valor'] = $moneda['simbolo'] . $rowfinanciacion['valor'];
            $arrDetalle[$i]['orden'] = $rowfinanciacion['orden'];
            $arrDetalle[$i]['cod_concepto'] = $codconcepto;
           
            $vencimientoTest = strtotime($vencimiento);
            
     
            
            
           $vencimiento = $rowfinanciacion['nro_cuota'] == 1 ? $fechapago : date("Y-m-d", strtotime('+' . $periodo, $vencimientoTest));
           $vencimientoValido = getPrimerFechaHabil($conexion, $vencimiento);
  
           
           
           $arrDetalle[$i]['fecha'] = formatearFecha_pais($vencimientoValido);
            $arrDetalle[$i]['periodo_tiempo'] = $periodo;
            $i++;
        }
        
        return $arrDetalle;
    }

    public function getDetallesFinanciaciones($arrDatos) {
        $arrDetalle = array();

        foreach ($arrDatos['financiaciones'] as $value) {
            $fecha = isset($value['fecha_primer_pago']) ? $value['fecha_primer_pago'] : date('Y-m-d');
            $arrDetalle[$value['cod_concepto']] = $this->getDetalle($arrDatos['cod_plan'], $value['cod_concepto'], $value['cod_financiacion'], $fecha, $arrDatos['moneda']);
        }
        //ordena array
        $todosdet = array();
        $i = 0;
       
        foreach ($arrDetalle as $arrConceptos) {
            foreach ($arrConceptos as $key => $detalles) {
                
                $todosdet[$i] = $detalles;
                $orden[$i] = $detalles['orden'];
                $cuotas[$i] = $detalles['nrocuota'];
                $i++;
            }
        }

        array_multisort($orden, SORT_ASC, $cuotas, SORT_ASC, $todosdet);

        return $todosdet;
    }

}
