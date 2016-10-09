<?php

class Vexamenes_estado_historicos extends Texamenes_estado_historicos{
   
    private static $motivos = array(
        array("id" => 1, "motivo" => 'Se ha dictado el examen'),
        array("id" => 2, "motivo" => 'Examen suspendido'));
      
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

     function getmotivos($index = false) {
        return $index !== false ? self :: $motivos[$index] : self ::$motivos;
    }
    
 }

