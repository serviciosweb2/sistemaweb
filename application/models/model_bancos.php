<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_bancos extends CI_Model {

    var $codigofilial = 0;

    public function __construct($codFilial) {
        parent::__construct();
        $this->codigofilial = $codFilial;
    }
    
    
    public function listarBancos($codPais = null){
        $conexion = $this->load->database($this->codigofilial, true);
        if ($codPais != null){
            $conexion->join("bancos.bancos_paises", "bancos.bancos_paises.cod_banco = bancos.bancos.codigo AND bancos.bancos_paises.cod_pais = $codPais");
        }
        $arrBancos = Vbancos::listarBancos($conexion);
        return $arrBancos;
    }
    
    public function getMaquetaCuenta($codBanco){
        $arrResp = array();
        $conexion = $this->load->database($this->codigofilial, true);
        $myBanco = new Vbancos($conexion, $codBanco);
        $arrResp[$codBanco]['nombre'] = $myBanco->nombre;
        $arrCampos = $myBanco->getCamposCuenta();
        foreach ($arrCampos as $campo){            
            $arrResp[$codBanco]['cuentas']['-1'][$campo] = '';
        }
        $arrCampos = Vcuentas_boletos_bancarios::getCampos();
        foreach ($arrCampos as $campo){
            $arrResp[$codBanco]['cuentas']['-1']['boletos_bancarios'][0][$campo] = "";
        }
        return $arrResp;
    }
    
    public function listarCuentas(){
        $arrResp = array();
        $conexion = $this->load->database($this->codigofilial, true);
        $myFilial = new Vfiliales($conexion, $this->codigofilial);
        $arrCuentasBancos = $myFilial->getCuentasBancarias();
        $arrBancosCuentas = array();        
        foreach ($arrCuentasBancos as $cuentaBanco){
            $codBanco = $cuentaBanco['cod_banco'];
            $codCuenta = $cuentaBanco['cod_configuracion'];
            $arrBancosCuentas[$codBanco][] = $codCuenta;
        }
        foreach ($arrBancosCuentas as $codBanco => $arrCodCuentas){
            $myBanco = new Vbancos($conexion, $codBanco);
            $arrResp[$codBanco]['nombre'] = $myBanco->nombre;
            foreach ($arrCodCuentas as $codCuenta){
                $myCuenta = $myBanco->getCuentaBanco($codCuenta);
                $arrResp[$codBanco]['cuentas'][$codCuenta] = $myCuenta->getToArray();
                if (method_exists($myCuenta, "getBoletoBancario")){
                    $arrResp[$codBanco]['cuentas'][$codCuenta]['boletos_bancarios'] = $myCuenta->getBoletoBancario();
                }
            }
        }
        return $arrResp;
    }
    
    public function cambiar_estado_cuenta($codBanco, $codCuenta, $nuevoEstado){
        $conexion = $this->load->database($this->codigofilial, true);
        $myBanco = new Vbancos($conexion, $codBanco);
        $myCuenta = $myBanco->getCuentaBanco($codCuenta);
        switch ($nuevoEstado) {
            case "inhabilitar":
                $resp = $myCuenta->inhabilitar();
                break;

            case "habilitar":
                $resp = $myCuenta->habilitar();
                break;
                
            default:
                $resp = false;
                break;
        }
        return $resp;
    }
    
    public function get_archivos_remessa(){
               $conexion = $this->load->database($this->codigofilial, true);
    
        $arrCuentas = Vboletos_bancarios::listarBoletos_bancarios($conexion);
        
        return $arrCuentas; 
        
        
    }
    
    
        }