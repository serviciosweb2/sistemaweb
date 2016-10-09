<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of model_facebook_ads_datos
 *
 * @author romario
 */
class model_facebook_ads_datos extends CI_Model {
    
    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }
    
    public function sincroniza_tabla() {
        $conexion = $this->load->database("general", true);
        
        $ultima_fecha = Vfacebook_ads_datos::buscar_ultima_fecha($conexion);
        if(empty($ultima_fecha)) {
            $ultima_fecha = null;
        }

        $facebook_api = new facebook_api();
        $campanas = $facebook_api->getAllFacebookCampaigns($ultima_fecha);
        $campanas_datos = $facebook_api->getAllFacebookCampaignsDataByDate($campanas, $ultima_fecha);
//           
//        //siempre tiene remover los registros de la ultima_fecha (los datos de la ultima fecha nunca son completos)
        Vpublicidad_campanas::insere_update_array($conexion, $campanas);
        if(!empty($campanas_datos)) {
            Vfacebook_ads_datos::remove_por_fecha($conexion, $ultima_fecha);
            Vfacebook_ads_datos::insere_array($conexion, $campanas_datos);
        }

        return true;
    }
    
    public function listarFacebookDatosBusca($arrFiltros, $fechaDesde = null, $fechaHasta = null, $campana = null, $campanas = null) {
        $conexion = $this->load->database('general', true);
        $arrCondindiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre" => $arrFiltros["sSearch"],
                "alcance"=>$arrFiltros["sSearch"],
                "resultados"=>$arrFiltros["sSearch"]
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
        $datos = Vfacebook_ads_datos::listarFacebookDatosDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $fechaDesde, $fechaHasta, $campana, $campanas);
        $contar = Vfacebook_ads_datos::listarFacebookDatosDataTable($conexion, $arrCondindiciones, null, null, true, $fechaDesde, $fechaHasta, $campana, $campanas);
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
                $row["alcance"],
                $row["resultados"],
                $row["matriculados"]
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }
    
}
