<?php

class Vreglamentos extends Treglamentos {
    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getReglamentosFiliales(CI_DB_mysqli_driver $conexion, $cod_filial = null, $tipo = null) {
        $conexion->select('*');
        $conexion->from('general.reglamentos');
        $conexion->join('general.reglamentos_filiales', 'general.reglamentos_filiales.cod_reglamento = general.reglamentos.id');
        if ($cod_filial != null) {
            $conexion->where('general.reglamentos_filiales.cod_filial', $cod_filial);
        }
        if ($tipo != null) {
            $conexion->where('general.reglamentos.tipo', $tipo);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

}
