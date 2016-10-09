<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_webservices extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    function modelarFacturantes(Vfacturantes $myFacturante, $razonSocial, $puntos_venta, Vfacturantes_certificados $myCertificado){
        $conexion = $this->load->database("default", true);
        $arrResp = array();
        $arrResp['codigo'] =  $myFacturante->getCodigo();
        $arrResp['inicio_actividades'] = $myFacturante->inicio_actividades;
        $arrResp['cod_razon_social'] = $myFacturante->cod_razon_social;
        $arrResp['cod_facturante_matriz'] = $myFacturante->cod_facturante_matriz;
        $arrResp['estado'] = $myFacturante->estado;
        $arrResp['certificado']['cod_facturante'] = $myCertificado->getCodigoFacturante();
        $arrResp['certificado']['cert'] = $myCertificado->cert;
        $arrResp['certificado']['pry_key'] = $myCertificado->pry_key;
        $arrResp['certificado']['pub_key'] = $myCertificado->pub_key;
        $arrResp['certificado']['password'] = $myCertificado->password;
        $arrResp['certificado']['fecha_expiracion'] = $myCertificado->fecha_expiracion;
        foreach ($puntos_venta as $key => $puntoVenta){
            $myPuntoVenta = new Vpuntos_venta($conexion, $puntoVenta['codigo']);
            $arrFiliales = $myPuntoVenta->getFiliales();
            foreach ($arrFiliales as $filial){
                $puntos_venta[$key]['filiales'][] = $filial['cod_filial'];
            }
        }
        $arrResp['puntos_venta'] = $puntos_venta;
        $arrResp['razon_social'] = $razonSocial[0];        
        return $arrResp;
    }
    
    function modelarConfiguracionFacturacionElectronica($object, $proveedor){
        //var_dump($object);
        $arrResp = array();
        foreach ($object as $key => $value){
            $arrResp['configuracion'][$key] = $value;
        }
        $arrResp['configuracion']['codigo'] = $object->getCodigo();
        $arrResp['proveedor'] = $proveedor;
        return $arrResp;
    }
    
}