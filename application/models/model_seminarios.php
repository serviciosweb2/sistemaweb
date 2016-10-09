<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_seminarios extends CI_Model {

    public function listarSeminariosDatatable($arrFiltros, $fechaDesde = null, $fechaHasta = null, $idFilial = null, $idSeminario = null) {
        $conexion = $this->load->database("seminarios", true);        
        $arrCondindiciones = array();
        $this->load->helper('alumnos');
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "horario" => $arrFiltros["sSearch"],
                "cupo" => $arrFiltros["sSearch"],
                "nombre" => $arrFiltros["sSearch"],
                "telefono" => $arrFiltros["sSearch"],
                "documento" => $arrFiltros["sSearch"],
                "email" => $arrFiltros["sSearch"],
                "fecha_inscripto" => $arrFiltros["sSearch"]                
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
        } else {
            $arrSort = array(
                "0" => "seminarios.seminarios.id",
                "1" => "asc",
                "2" => "nombre",
                "3" => "asc"
            );
        }
        $datos = Vseminarios::listarHorariosDataTable($conexion, $fechaDesde, $fechaHasta, $idFilial, $idSeminario, $arrCondindiciones, $arrLimit, $arrSort, false);
        $contar = Vseminarios::listarHorariosDataTable($conexion, $fechaDesde, $fechaHasta, $idFilial, $idSeminario, $arrCondindiciones, null, null, true);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["horario"],
                $row["cupo"],
                ucwords(strtolower($row["nombre"])),
                $row["telefono"],
                $row["documento"],
                $row["email"],
                $row['fecha_inscripto']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }
}