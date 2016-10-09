<?php
/**
 * Description of Vpublicidad_campanas
 *
 * @author romario
 */
class Vpublicidad_campanas extends Tpublicidad_campanas {
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
        
    static function insere_update_array(CI_DB_mysqli_driver $conexion, $datos) {
        $ret = array();
        foreach($datos as $campana) {
            $query = "INSERT INTO general.publicidad_campanas
                    VALUES ({$campana['codigo']}, '{$campana['nombre']}', {$campana['filial_codigo']}, '{$campana['origen']}')
                    ON DUPLICATE KEY UPDATE nombre = '{$campana['nombre']}', filial_codigo = {$campana['filial_codigo']}";
                    $ret[] = $conexion->query($query);
        }
        return $ret;
    }
    
    static function buscarCampanasPorCodFiliales($conexion ,$filiales, $origen = 'google') {
        $conexion->select("codigo, nombre");
        $conexion->from('general.publicidad_campanas', false);
        $conexion->where_in('filial_codigo', $filiales);
        if($origen != null) {
            $conexion->where('origen', $origen);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function buscarCampanas($conexion, $origen = 'google') {
        $conexion->select("codigo, nombre");
        $conexion->from('general.publicidad_campanas', false);
        if($origen != null) {
            $conexion->where('origen', $origen);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
}
