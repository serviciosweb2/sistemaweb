<?php 

/**
 * Como_nos_conocio
 * 
 * Description...
 * 
 * @package como_nos_conocio
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */

if (!defined('BASEPATH')){ 
    exit('No direct script access allowed');
}

class Model_Como_nos_conocio extends CI_Model {
        var $idioma ="";
        
	public function __construct($arg){
            parent::__construct();              
            $this->idioma = $arg["idioma"];		
	}

    public function getComoNosConocio($codFilial = null, $activo = null, $pais = null) {
        $this->load->database();
        $conexion = $this->db;
        $condiciones = array();
        if ($activo != null){
            $condiciones["general.como_nos_conocio_filiales.activo"] = $activo;
        }
        $arrComonosConocio = Vcomo_nos_conocio::listarComo_nos_conocio($conexion, $condiciones, $codFilial);
        $arrNew = array();
        $i =0;
        foreach ($arrComonosConocio as $value) {                 
            $arrNew[$i]["codigo"] =  $value["codigo"];
            $arrNew[$i]["nombre"] =  $value["descripcion_" .$this->idioma];
            $i++;
        }
        return $arrNew;    
    }

    public function getComoNosConocioArray($codigo) {
        $this->load->database();
        return (new Tcomo_nos_conocio($this->db, $codigo))->_getArrayDeObjeto($this->idioma);
    }
        
    public function getReporteComoNosConocio($idFilial, $fechaAlumnosDesde = null, $fechaAspirantesDesde = null){
        $conexion = $this->load->database($idFilial, true);
        return Vcomo_nos_conocio::getReporteComoNosConocio($conexion, $fechaAlumnosDesde, $fechaAspirantesDesde);
    }

    public function listarArbolComoNosConocio() {
        $conexion = $this->load->database("general", true);
        return Vcomo_nos_conocio::listarArbolComoNosConocio($conexion);
    }
}