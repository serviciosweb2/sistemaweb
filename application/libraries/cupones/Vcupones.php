<?php

/**
* Class Vcupones
*
*Class  Vcupones maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vcupones extends Tcupones{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* La siguiente function está siendo accedida desde un Web Services, no modificar, eliminar ni comentar */
    /**
     * Permite guardar un cupon forzando su codigo de identificacion
     * 
     * @param integer $id
     * @return boolean
     */
    public function guardar($id){ // debe mantener el id de la sincronizacion
        $this->oConnection->where("id", $id);
        $resp = $this->oConnection->delete("general.cupones");
        $arrField = $this->_getArrayDeObjeto();
        $arrField['id'] = $id;
        $resp = $resp && $this->oConnection->insert("general.cupones", $arrField);
        if ($resp){
            $this->id = $id;
        }
        return $resp;
    }
}