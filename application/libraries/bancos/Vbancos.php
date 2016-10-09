<?php

/**
* Class Vbancos
*
*Class  Vbancos maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vbancos extends Tbancos{

    static private $estadoCuentasHabilitado = "habilitada";
    static private $estadoCuentasInhabilitado = "inhabilitada";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    public function getCuentaBanco($codigoConfiguracion){
        $className = "V".$this->banco_asociado;        
        return new $className($this->oConnection, $codigoConfiguracion);
    }
    
    public function listarCuentas($codCuentas = null, $codFilial = null){
        $this->oConnection->select("bancos.{$this->banco_asociado}.*");
        $this->oConnection->from("bancos.{$this->banco_asociado}");
        if ($codFilial != null)
            $this->oConnection->join("general.filiales_cuentas_bancos", "general.filiales_cuentas_bancos.cod_cuenta = banco_do_brasil.codigo 
                                        AND general.filiales_cuentas_bancos.cod_filial = {$codFilial}
                                        AND general.filiales_cuentas_bancos.cod_banco = {$this->codigo}");
        if ($codCuentas !== null){
            if (is_array($codCuentas))
                $this->oConnection->where_in("codigo", $codCuentas);
            else
                $this->oConnection->where("codigo", $codCuentas);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCamposCuenta(){
        $class = "V".$this->banco_asociado;
        return $class::getCampos();
    }
    
    /* STATIC FUNCTIONS */
    
    static function getEstadoCuentaHabilitada(){
        return self::$estadoCuentasHabilitado;
    }
    
    static function getEstadoCuentaInhabilitada(){
        return self::$estadoCuentasInhabilitado;
    }
}