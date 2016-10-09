<?php

/**
* Class Vboletos_estados_historicos
*
*Class  Vboletos_estados_historicos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vboletos_estados_historicos extends Tboletos_estados_historicos{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function getCobro(){
        $this->oConnection->select("cod_cobro");
        $this->oConnection->from("cobros_boletos");
        $this->oConnection->where("cod_boleto_historico", $this->codigo);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        if (count($arrTemp) > 0){
            return new Vcobros($this->oConnection, $arrTemp[0]['cod_cobro']);
        } else {
            return false;
        }
    }    
}

?>