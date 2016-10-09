<?php

class Vfirma_rematricula extends Trematricula {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarFirmaRematriculaDataTable(CI_DB_mysqli_driver $conexion, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $arrFiltros_nuevos = null) {
        $conexion->select("alumnos.documento, alumnos.nombre, alumnos.apellido, matriculas.codigo, comisiones.nombre as comision, general.ciclos.nombre as ciclo, control_rematricula.firmo, control_rematricula.ano, control_rematricula.trimestre, control_rematricula.fecha", false);
        $conexion->from("alumnos");
        $conexion->join("matriculas", "matriculas.cod_alumno = alumnos.codigo");
        $conexion->join("control_rematricula", "control_rematricula.cod_matricula = matriculas.codigo", "left");

        $conexion->join("matriculas_periodos", "matriculas_periodos.cod_matricula = matriculas.codigo");
        $conexion->join("estadoacademico", "estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo");
        $conexion->join("comisiones", "comisiones.codigo = matriculas_inscripciones.cod_comision");
        $conexion->join("general.ciclos", "general.ciclos.codigo = comisiones.ciclo");

        $conexion->where("alumnos.baja", 'habilitada');

        if(isset($arrFiltros_nuevos["condic_doc"]) && isset($arrCondicioneslike['alumnos.documento']) && $arrFiltros_nuevos["condic_doc"] != "") {
            $conexion->where("alumnos.documento", $arrCondicioneslike['alumnos.documento']);
        }
        if(isset($arrFiltros_nuevos["condic_mat"]) && isset($arrCondicioneslike['matriculas.codigo']) && $arrFiltros_nuevos["condic_mat"] != "") {
            $conexion->where("matriculas.codigo", $arrCondicioneslike['matriculas.codigo']);
        }
        if(isset($arrFiltros_nuevos["condic_cic"]) && isset($arrCondicioneslike['general.ciclos.nombre']) && $arrFiltros_nuevos["condic_cic"] != "") {
            $conexion->where("general.ciclos.nombre", $arrCondicioneslike['general.ciclos.nombre']);
        }
        if(isset($arrFiltros_nuevos["condic_fir"]) && isset($arrCondicioneslike['control_rematricula.firmo']) && $arrFiltros_nuevos["condic_fir"] != "") {
            if($arrCondicioneslike['control_rematricula.firmo']== 'si') {
                $conexion->where("control_rematricula.firmo", 'si');
            }else{
                $conexion->where("control_rematricula.firmo is null");
            }
        }
        if(isset($arrFiltros_nuevos["condic_ano"]) && isset($arrCondicioneslike['control_rematricula.ano']) && $arrFiltros_nuevos["condic_ano"] != "") {
            $conexion->where("control_rematricula.ano", $arrCondicioneslike['control_rematricula.ano']);
        }
        if(isset($arrFiltros_nuevos["condic_tri"]) && isset($arrCondicioneslike['control_rematricula.trimestre']) && $arrFiltros_nuevos["condic_tri"] != "") {
            $conexion->where("control_rematricula.trimestre", $arrCondicioneslike['control_rematricula.trimestre']);
        }
        if(isset($arrFiltros_nuevos["condic_fec"]) && isset($arrCondicioneslike['control_rematricula.fecha']) && $arrFiltros_nuevos["condic_fec"] != "") {
            $conexion->where("control_rematricula.fecha", $arrCondicioneslike['control_rematricula.fecha']);
        }

        $conexion->group_by("matriculas.codigo");
        $conexion->group_by("control_rematricula.ano");
        $conexion->group_by("control_rematricula.trimestre");

        if ($arrCondicioneslike != null) {
            foreach ($arrCondicioneslike as $key => $value) {
                if($key == 'alumnos.documento' OR $key == 'alumnos.nombre' OR $key == 'alumnos.apellido' OR $key == 'comisiones.nombre' OR $key == 'matriculas.codigo' OR $key == 'general.ciclos.nombre'){
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (isset($arrTemp) && count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($arrLimit !== null) {
            $conexion->limit($arrLimit["1"], $arrLimit["0"]);
        }
        if ($arrSort !== null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
        $listarFirmas = $query->result_array();
        if ($contar) {
            return count($listarFirmas);
        } else {
            return $listarFirmas;
        }
    }

    static function insertar(CI_DB_mysqli_driver $conexion, $datos) {
        $ret = $conexion->insert('control_rematricula', $datos);
        return $ret;
    }
}
