<?php

/**
* Class Vmails_consultas_reservas
*
*Class  Vmails_consultas_reservas maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vmails_consultas_reservas extends Tmails_consultas_reservas{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* La siguiente function es utilizada desde un web services y tiene como objetivo mantener la sincronizacion de la tabla entre sistemas */
    public function guardadoForzado($codigoConsulta){
        $arrTemp = $this->_getArrayDeObjeto();
        $arrTemp['id'] = $codigoConsulta;
        $this->oConnection->trans_begin();
        $this->oConnection->where("id", $codigoConsulta);
        $this->oConnection->delete($this->nombreTabla);
        $this->oConnection->insert($this->nombreTabla, $arrTemp);
        if ($this->oConnection->trans_status()){
            $this->oConnection->trans_commit();
            $this->id = $arrTemp['id'];
            return true;
        } else {
            $this->oConnection->trans_rolback();
            return false;
        }
    }
    
}

?>