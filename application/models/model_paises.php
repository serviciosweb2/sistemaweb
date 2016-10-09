<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Paises
 * 
 * Modelo dedicado a gestionar todo lo relacionado con  paises
 * 
 * @package paises
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.1.0
 */
class Model_paises extends CI_Model {

    /**
     * id de pais para instanciar el modelo
     * @access public
     * @var $id int
     */
    var $id;

    public function __construct($arg) {
        parent::__construct();
        $this->id = $arg;
    }

    /**
     * retorna todas las provincias de un pais
     * @access public
     * @return array Provincias
     */
    public function getprovincias() {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array("pais" => $this->id);

        return Vprovincias::listarProvincias($conexion, $condiciones);
    }

    /**
     * retorna todas las empresas telefonicas de un pais
     * @access public
     * @return array Empresas
     */
    public function getEmpresasTelefonicas() {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array("pais" => $this->id);
        return Vempresas_telefonicas::listarEmpresas_telefonicas($conexion, $condiciones);
    }

    /**
     * retorna todos los tipos de documentos de un pais
     * @access public
     * @return array documentos
     */
    public function getDocumentos() {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array("pais" => $this->id);
        return Vdocumentos_tipos::listarDocumentos_tipos($conexion, $condiciones);
    }

    /**
     * retorna todos los tipos de documentos de un pais
     * @access public
     * @return array documentos
     */
    public function getDocumentosPersonasFisicas() {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array("pais" => $this->id,
            "personafisica" => 1);
        return Vdocumentos_tipos::listarDocumentos_tipos($conexion, $condiciones);
    }

    public function getCondicionesSociales() {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array("cod_pais" => $this->id);
        return Vcondiciones_sociales::listarCondiciones_sociales($conexion, $condiciones);
    }

    /* ESTA FUNCTION ESTA SIENDO ACCEDIDA DESDE UN WEB SERVICES, no modificar, eliminar o comentar */

    public function getMediosPagoWS() {
        $conexion = $conexion = $this->load->database("default", true);
        $arrTemp = Vmedios_pago::listarMedios_pago($conexion, null, null, null, null, false, $this->id);
        foreach ($arrTemp as $key => $value) {
            $arrTemp[$key]['medio'] = lang($value['medio']);
        }
        return $arrTemp;
    }

    public function getMediosPagos($traducir = false, $cobrar = true, $disponiblesParaFilial = false) {
        $filial = $this->session->userdata('filial');
        $conexion = $this->load->database($filial['codigo'], true);
        $condiciones = array();
//        if ($cobrar) {
//            $condiciones = array(
//                "cobrar" => 1
//            );
//        }
        $arrResp = array();
        $arrTemp = Vmedios_pago::listarMedios_pago($conexion, $condiciones, null, null, null, false, $this->id);
        $cantidadTerminales = $disponiblesParaFilial ? Vpos_terminales::listarPos_terminales($conexion, array("estado" => Vpos_terminales::getEstadoHabilitado()), null, null, null, true) : 1;
        $this->lang->load(get_idioma(), get_idioma());
        for ($i = 0; $i < count($arrTemp); $i++) {
            if (($arrTemp[$i]['medio'] <> "TARJETA" && $arrTemp[$i]['medio'] <> 'TDEBITO') || $cantidadTerminales > 0) {
                $arrResp[$i]['medio'] = $traducir ? lang($arrTemp[$i]['medio']) : $arrTemp[$i]['medio'];
                $arrResp[$i]['codigo'] = $arrTemp[$i]['codigo'];
                //$arrResp[$i]['cobrar'] = $arrTemp[$i]['cobrar'];
            }
        }
        return $arrResp;
    }

    public function getMayoriaEdadPorPais() {
        $this->load->database();
        $conexion = $this->db;
        $myPais = new Vpaises($conexion, $this->id);
        $mayoriaEdad = $myPais->getAniosMayoriaEdad();
        return $mayoriaEdad;
    }

    public function listarCondicionesSociales($tipo_identificador) {
        $this->load->database();
        $conexion = $this->db;
        $myDocumentoTipo = new Vdocumentos_tipos($conexion, $tipo_identificador);
        $arrResp = $myDocumentoTipo->getCondicionesSociales();
        return $arrResp;
    }

    public function getPaises(){
        $conexion = $this->load->database("default", true);
        $arrPaises = Vpaises::listarPaises($conexion);
        return $arrPaises;
    }
    
}

/* End of file paises.php */
/* Location: ./application/models/paises.php */
