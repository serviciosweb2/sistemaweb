<?php

class Vscript_impresiones extends Tscript_impresiones{
  
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    /* PRIVATE FUNCTIONS */
    
    /* PUBLIC FUCNTIONS */
    
    /* STATIC FUNCTIONS */
    
    /**
     * Retorna los scripts de impresion habilitados en el sistema. Alternativamente, si se envia codFilial, retorna el metodo
     * seleccionado por la misma para cada script (imprimir siempre, no imprimir nunca, preguntar siempre) y el id de impresora seleccionado
     * 
     * @param CI_DB_mysqli_driver $conexion Objeto de conexion a la base de datos
     * @param int $codFilial                el codigo de la filial a la que se quiere recuperar el metodo de impresion de cada script
     * @return array
     */
    static function listarScript_impresiones_filiales(CI_DB_mysqli_driver $conexion, $codFilial = null){
        if ($codFilial != null){
            $conexion->select("general.filiales_script_impresoras.metodo");
            $conexion->from("general.filiales_script_impresoras");
            $conexion->where("general.filiales_script_impresoras.id_script = script_impresiones.id");
            $conexion->where("general.filiales_script_impresoras.id_filial", $codFilial);
            $subquery1 = $conexion->return_query();
            $conexion->resetear();
            
            $conexion->select("general.filiales_script_impresoras.printer_id");
            $conexion->from("general.filiales_script_impresoras");
            $conexion->where("general.filiales_script_impresoras.id_script = script_impresiones.id");
            $conexion->where("general.filiales_script_impresoras.id_filial", $codFilial);
            $subquery2 = $conexion->return_query();
            $conexion->resetear();
        }
        
        $conexion->select("general.script_impresiones.*", false);
        if ($codFilial != null){
            $conexion->select("IFNULL(($subquery1), 'imprimir') AS metodo", false);
            $conexion->select("IFNULL(($subquery2), -1) AS printer_id", false);
        }
        $conexion->from("general.script_impresiones");
        $query = $conexion->get();
        return $query->result_array();
    }
}

