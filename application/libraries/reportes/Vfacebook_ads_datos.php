<?php

/**
 * Description of Vfacebook_ads_datos
 *
 * @author romario
 */
class Vfacebook_ads_datos extends Tfacebook_ads_datos {
      
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    static function buscar_ultima_fecha(CI_DB_mysqli_driver $conexion) {
        $conexion->select("MAX(fecha) as ultima_fecha");
        $conexion->from('general.facebook_ads_datos', false);
        $query = $conexion->get();
        return $query->row(0)->ultima_fecha;
    }
    
    static function listarFacebookDatosDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, $arrLimit = null, $arrSort = null, $contar = false, $fechaDesde = null, $fechaHasta = null, $campana = null, $campanas = null) {
        $conexion->select("ifnull(pc.nombre, '".lang('no_definido')."') as nombre, SUM(alcance) as alcance, SUM(resultados) as resultados, tipo_resultado as tipo_resultado, filial_codigo as filial_codigo", false);
        $conexion->from("facebook_ads_datos as fa");
        $conexion->join("publicidad_campanas as pc", "pc.codigo = fa.campana_codigo", 'left');
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
                $arrResp[$key]['matriculados'] = Vmails_consultas::listarAlumnosMatriculadosFacebook($conexion, $resp['filial_codigo'], $fechaDesde, $fechaHasta, true);
                $arrResp[$key]['resultados'] = $arrResp[$key]['resultados'] . ' (' . lang($arrResp[$key]['tipo_resultado']) . ')';
            }
        }
        
        return $arrResp;
    }
    
    static function remove_por_fecha(CI_DB_mysqli_driver $conexion, $fecha){
        $conexion->where('fecha', $fecha);
        $ret = $conexion->delete('general.facebook_ads_datos');
        return $ret;
    }
    
    static function insere_array(CI_DB_mysqli_driver $conexion, $datos) {
        $ret = $conexion->insert_batch('general.facebook_ads_datos', $datos);
        return $ret;
    }
}
