<?php

/**
* Class Vinscriptos
*
*Class  Vinscriptos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vinscriptos extends Tinscriptos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function guardarForzado($id){
        $arrTemp = $this->_getArrayDeObjeto();
        $arrTemp['id'] = $id;
        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)){
            $this->id = $id;
            return true;
        } else {
            return false;
        }
    }
    
}

?>