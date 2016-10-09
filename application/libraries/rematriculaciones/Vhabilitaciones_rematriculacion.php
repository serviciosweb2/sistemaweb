<?php

class Vhabilitaciones_rematriculacion extends Thabilitaciones_rematriculacion{
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null, $numeroSeguimiento = null) {
        if ($numeroSeguimiento != null){
            $arrTemp = self::_getConstructor($conexion, $numeroSeguimiento);
            if (count($arrTemp) > 0){
                $this->oConnection = $conexion;
                foreach ($arrTemp[0] as $key => $value){
                    $this->$key = $value;
                }
            } else {
                throw new Exception("numero seguimiento inexistente");
            }            
        } else {
            parent::__construct($conexion, $codigo);
        }
    }

    public function getCodigo(){
        return $this->codigo;
    }
    static private function _getConstructor(CI_DB_mysqli_driver $conexion, $numeroSeguimiento){
        $conexion->select("*");
        $conexion->from("general.habilitaciones_rematriculacion");
        $conexion->where("general.habilitaciones_rematriculacion.codigo", $numeroSeguimiento);
        $query = $conexion->get();
        return $query->result_array();
    }

    static public function test(){
        echo "Hola";
    }
}

?>
