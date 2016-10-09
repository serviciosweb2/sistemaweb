<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Control de seccion alumnos.
 */
class Ciclos_lectivos extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        session_method();
    }
    
    /* esta function se está accediendo desde un WEB SERVICES no modificar, eliminar ni comentar */
    function get_ciclos_lectivos(){
        $condiciones = array();
        $conexion = $this->load->database("default", true);
        if (isset($_POST['codigo'])){
            $condiciones['codigo'] = $_POST['codigo'];
        }
        $arrResp = Vciclos::listarCiclos($conexion, $condiciones);
        echo json_encode($arrResp);
    }
    
    /* esta function se está accediendo desde un WEB SERVICES no modificar, eliminar ni comentar */
    function guardar_ciclo_lectivo(){
        $arrResp = array();
        if (isset($_POST['codigo']) && isset($_POST['nombre']) && isset($_POST['abreviatura']) &&
            isset($_POST['fecha_inicio_ciclo']) && isset($_POST['fecha_fin_ciclo'])){
            $conexion = $this->load->database("default", true);
            $myCiclo = new Vciclos($conexion, $_POST['codigo']);
            $myCiclo->abreviatura = $_POST['abreviatura'];
            $myCiclo->fecha_fin_ciclo = $_POST['fecha_fin_ciclo'];
            $myCiclo->fecha_inicio_ciclo = $_POST['fecha_inicio_ciclo'];
            $myCiclo->nombre = $_POST['nombre'];
            if ($myCiclo->guardarCiclos()){
                $arrResp['success'] = "success";
                $arrResp['codigo'] = $myCiclo->getCodigo();
            } else {
                $arrResp['error'] = "[".$conexion->_error_number()."] ".$conexion->_error_message();
            }
        } else {
            $arrResp['error'] = "Error de parametros";
        }
        echo json_encode($arrResp);
    }
}