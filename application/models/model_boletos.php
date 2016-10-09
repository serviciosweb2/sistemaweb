<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_boletos extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($codigoFilial) {
        parent::__construct();
        $this->codigo_filial = $codigoFilial;
    }
   
    public function getBoletosReimprimir($matriculas, $desde, $hasta){
        $conexion = $this->load->database($this->codigo_filial, true);
        $boletos = Vboletos_bancarios::getBoletosReimprimir($conexion, $matriculas, $desde, $hasta);
        return $boletos;
    }

     public function listarBoletosDataTable($arrFiltros, $estado = null, $fechaVencimientoDesde = null, $fechaVencimientoHasta = null,
             $fechaEmisionDesde = null, $fechaEmisionHasta = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        $condiciones = array();
        $condiciones['cod_filial'] = $this->codigo_filial;
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "sacado_nombre" => $arrFiltros["sSearch"],
                "nosso_numero" => $arrFiltros["sSearch"],
                "numero_documento" => $arrFiltros["sSearch"]
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" && $arrFiltros["iDisplayLength"] != "") {
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
        
        $datos = Vboletos_bancarios::listarBoletosDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, false, $condiciones, 
                $fechaVencimientoDesde, $fechaVencimientoHasta, $fechaEmisionDesde, $fechaEmisionHasta, $estado);
        $contar = Vboletos_bancarios::listarBoletosDataTable($conexion, $arrCondiciones, "", "", true, $condiciones,
                $fechaVencimientoDesde, $fechaVencimientoHasta, $fechaEmisionDesde, $fechaEmisionHasta, $estado);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $key => $row) {
            $arrCtaCte = Vctacte::listarCtacte($conexion, array("codigo" => $row['numero_documento']));
            formatearCtaCte($conexion, $arrCtaCte);
            $rows[$key][] = $row['codigo'];
            $rows[$key][] = formatearFecha_pais($row['fecha_emision']);
            $rows[$key][] = $row["sacado_nombre"];
            $rows[$key][] = $row["sacado_cpf_cnpj"];
            $rows[$key][] = $arrCtaCte[0]['descripcion'];
            $rows[$key][] = formatearImporte($row["valor_boleto"]);
            $rows[$key][] = $row['fecha_vencimiento'] <> '' ? formatearFecha_pais($row['fecha_vencimiento']) : '';
            $rows[$key][] = ltrim($row["nosso_numero"], "0")."-".Vbanco_do_brasil::modulo_11(ltrim($row["nosso_numero"], 0));
            $rows[$key][] = lang($row["estado"]);
            $rows[$key][] = $row['valor_boleto'];
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }
    
    public function getDetalleBoleto($codigoBoleto){
        $arrResp = array();
        $conexion = $this->load->database($this->codigo_filial, true);
        $myBoleto = new Vboletos_bancarios($conexion, $codigoBoleto);
        $arrCtacte = Vctacte::listarCtacte($conexion, array("codigo" => $myBoleto->numero_documento));
        
        formatearCtaCte($conexion, $arrCtacte);
        $arrResp['ctacte_original']['descripcion'] = $arrCtacte[0]['descripcion'];
        $arrResp['ctacte_original']['importe'] = $arrCtacte[0]['simbolo_moneda']." ".$arrCtacte[0]['importeformateado'];
        $arrResp['ctacte_original']['fecha_vencimiento'] = $arrCtacte[0]['fechavenc'];
        $arrHistorico = Vboletos_estados_historicos::listarBoletos_estados_historicos($conexion, array("cod_boleto" => $myBoleto->getCodigo()));
        $codigoHistorico = null;
        if (count($arrHistorico) > 0){
            foreach ($arrHistorico as $key => $historico){
                $arrResp['historico'][$key]['estado'] = lang($historico['estado']);
                $arrResp['historico'][$key]['fecha'] = formatearFecha_pais($historico['fecha'], true);
                if ($historico['estado'] == Vboletos_bancarios::getEstadoLiquidado()){
                    $codigoHistorico = $historico['codigo'];
                }
            }
        }
        if ($myBoleto->estado == Vboletos_bancarios::getEstadoLiquidado()){
            $myHistorico = new Vboletos_estados_historicos($conexion, $codigoHistorico);
            $myCobro = $myHistorico->getCobro();
            if ($myCobro){
                $arrImputaciones = $myCobro->getCtacteImputaciones();
                if (count($arrImputaciones) > 0){
                    formatearCtaCte($conexion, $arrImputaciones);
                    foreach ($arrImputaciones as $key => $imputacion){
                        $arrResp['imputaciones'][$key]['descripcion'] = $imputacion['descripcion'];
                        $arrResp['imputaciones'][$key]['fecha_vencimiento'] = $imputacion['fechavenc'];
                        $arrResp['imputaciones'][$key]['imputado'] = formatearImporte($imputacion['valor'], true);
                        $arrResp['imputaciones'][$key]['importe_original'] = formatearImporte($imputacion['importe'], true);                        
                    }
                }
            }
        }
        return $arrResp;
    }
    
}
