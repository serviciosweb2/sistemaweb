<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Model_moras_cursos_cortos extends CI_Model {
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getMoras(){
        $conexion = $this->load->database($this->codigo_filial,true);
        $condiciones = array("baja"=> 0);
        $ListaMoras = VmorasCursosCortos::listarMoras($conexion,$condiciones);
        foreach($ListaMoras as $key=>$mora){
            $ListaMoras[$key]['tipo'] = lang($mora['tipo']);
        }
        return $ListaMoras;
    }

    public function guardarMora($data_post,$codUsuario){
        $conexion = $this->load->database($this->codigo_filial,true);
        $objMora = new VmorasCursosCortos($conexion,$data_post['cod_mora']);
        $objMora->setMoras($data_post);
        $respuesta = $objMora->guardarMoras();
        $data_post['fecha_hora'] = date("Y-m-d H:i:s");
        $data_post['usuario_creador'] = $codUsuario;
        $data_post['cod_mora'] = $objMora->getCodigo();
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    public function bajaMora($cod_mora){
        $conexion = $this->load->database($this->codigo_filial,true);
        $objMora = new VmorasCursosCortos($conexion, $cod_mora);
        $objMora->baja = 1;
        $respuesta = $objMora->guardarMoras();
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    public function getObjMora($cod_mora){
        $conexion = $this->load->database($this->codigo_filial,true);
        $objMora = new VmorasCursosCortos($conexion, $cod_mora);
        return $objMora;
    }
}