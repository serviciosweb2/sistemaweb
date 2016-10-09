<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_google_adwords_goal_completions extends CI_Model {
    
    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }
    
    public function listarGoalCompletionsBusca($arrFiltros, $fechaDesde = null, $fechaHasta = null, $campana = null, $campanas = null) {
        $conexion = $this->load->database('general', true);
        $arrCondindiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre" => $arrFiltros["sSearch"],
                "clics"=>$arrFiltros["sSearch"],
                "envios"=>$arrFiltros["sSearch"]
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
            $arrSort = array(
            "0" => $arrFiltros["SortCol"],
            "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vgoogle_adwords_goal_completions::listarGoalCompletionsDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $fechaDesde, $fechaHasta, $campana, $campanas);
        $contar = Vgoogle_adwords_goal_completions::listarGoalCompletionsDataTable($conexion, $arrCondindiciones, null, null, true, $fechaDesde, $fechaHasta, $campana, $campanas);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["nombre"],
                $row["clics"],
                $row["envios"],
                $row["matriculados"],
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }
    
    public function sincroniza_tabla() {
        $conexion = $this->load->database("general", true);
        
        $ultima_fecha = Vgoogle_adwords_goal_completions::buscar_ultima_fecha($conexion);
        if(empty($ultima_fecha)) {
            $ultima_fecha = "2005-01-01";
        }
        
        $ga_reporte = new google_analytics_reports();
        $campanas = $ga_reporte->getReportCampaings();
        $campanas = $ga_reporte->formatear_el_retorno_campanas($campanas);
        $campanas_ret = $ga_reporte->getReportConsecucionesObjetivoCampana(null, $ultima_fecha);
        $campanas_data = $ga_reporte->formatear_el_retorno($campanas_ret, $campanas['codigos']);
           
        //siempre tiene remover los registros de la ultima_fecha (los datos de la ultima fecha nunca son completos)
        Vgoogle_adwords_goal_completions::remove_por_fecha($conexion, $ultima_fecha);
        Vpublicidad_campanas::insere_update_array($conexion, $campanas['campanas']);
        Vgoogle_adwords_goal_completions::insere_array($conexion, $campanas_data['data']);
        return true;
    }
}
