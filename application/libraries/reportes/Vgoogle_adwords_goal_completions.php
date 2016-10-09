<?php

class Vgoogle_adwords_goal_completions extends Tgoogle_adwords_goal_completions {
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function buscar_ultima_fecha(CI_DB_mysqli_driver $conexion) {
        $conexion->select("MAX(fecha) as ultima_fecha");
        $conexion->from('general.google_adwords_goal_completions', false);
        $query = $conexion->get();
        return $query->row(0)->ultima_fecha;
    }
    
    static function insere_array(CI_DB_mysqli_driver $conexion, $datos) {
        $ret = $conexion->insert_batch('general.google_adwords_goal_completions', $datos);
        return $ret;
    }
    
    static function remove_por_fecha(CI_DB_mysqli_driver $conexion, $fecha){
        $conexion->where('fecha', $fecha);
        $ret = $conexion->delete('general.google_adwords_goal_completions');
        return $ret;
    }

    static function listarGoalCompletionsDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false, $fechaDesde = null, $fechaHasta = null, $campana = null, $campanas = null) {
        $conexion->select("ifnull(gc.nombre, '".lang('no_definido')."') as nombre, SUM(envios) as envios, SUM(clics) as clics, gc.filial_codigo as filial_codigo", false);
        $conexion->from("google_adwords_goal_completions as gg");
        $conexion->join("publicidad_campanas as gc", "gc.codigo = gg.campana_codigo", 'left');
        if ($fechaDesde != null){
            $conexion->where("DATE(fecha) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(fecha) <=", $fechaHasta);
        }
        if ($campana != null){
            $conexion->where("campana_codigo", $campana);
        }
        if ($campanas != null) {
            $conexion->where_in("campana_codigo", $campanas);
        }
        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value) {
                $arrTemp[] = "$key LIKE '%$value%'";
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($arrLimit != null && $arrLimit[1] != -1) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $conexion->group_by('campana_codigo');
        $query = $conexion->get();
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
            
            foreach($arrResp as $key => $resp) {
                if(!empty($resp['filial_codigo'])) {
                    $matriculados = Vmails_consultas::listarAlumnosMatriculados($conexion, $resp['filial_codigo'], array(71), $fechaDesde, $fechaHasta, true);
                }
                else {
                    $matriculados = lang('no_es_posible_obtener_la_informacion');
                }
                $arrResp[$key]['matriculados'] = $matriculados;
            }
        }
        
        return $arrResp;
    }
}
