<?php

/**
 * Model_filiales
 * 
 * Description...
 * 
 * @package model_filiales
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_filiales extends CI_Model {

    var $codigo = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = $arg;
    }

    /**
     * Cursos habilitados para la filial
     * @access public
     * @return Array de cursos.
     */
    public function getCursosHabilitados() {
        $conexion = $this->load->database($this->codigo, true);
        return Vcursos::getCursosHabilitados($conexion, null, null, false, null, null, 0);
    }
public function SetEstado($codigo,$estado){
    
        $conexion = $this->load->database($codigo, true);
        $filial = new Vfiliales($conexion,$codigo);
        $respuesta = $filial->setEstado($estado);
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
}

public function getComunicadosFilial($filial, $codigo) {
//        $conexion = $this->load->database($this->codigo, true);
//        $comunicadosFilial = Vtablas_comunicado::getComunicadosFilial($conexion, $filial, $codigo);
//        return $comunicadosFilial;
        $conexion = $this->load->database("general", true);
        $comunicadosFilial = Vcomunicados::listar($conexion, null, null, null, array("fecha_creacion", "DESC"), null, null, null, 'habilitada', $this->codigo);
        foreach ($comunicadosFilial as $key => $comunicado){
            $comunicadosFilial[$key]['fecha_hora_mostrar'] = formatearFecha_pais($comunicado['fecha_creacion'], true);
        }
        return $comunicadosFilial;
    }

    public function getCuentasBancariasFilial() {
        $conexion = $this->load->database($this->codigo, true);
        $filial = new Vfiliales($conexion, $this->codigo);
        return $filial->getCuentasBancarias();
    }

    public function getCuentasBancariasBoletos() {

        $conexion = $this->load->database($this->codigo, true);
        $filial = new Vfiliales($conexion, $this->codigo);

        $i = 0;
        $arrRespuesta = array();
        foreach ($filial->getCuentasBancarias() as $cuenta) {



            $arrRespuesta[$i]["cod_banco"] = $cuenta["cod_banco"];
            $arrRespuesta[$i]["cod_cuenta"] = $cuenta["cod_cuenta"];

            $arrRespuesta[$i]["cuentaboletos"] = Vcuentas_boletos_bancarios::getCuentas($conexion, $cuenta["cod_banco"], $cuenta["cod_cuenta"]);
            $i++;
        }

        return $arrRespuesta;
    }

    public function getFiliales($pais = null, $version = null) {

        $conexion = $this->load->database('', TRUE);

        $arrcondiciones = array('pais' => $pais);

        if ($version != null) {
            $arrcondiciones['version_sistema'] = $version;
        }

        $filiales = Vfiliales::listarfiliales($conexion, $arrcondiciones);

        return $filiales;
    }

    public function getFilialesActivas() {
        $conexion = $this->load->database('', TRUE);
        $arrcondiciones = array('estado' => 'activa', 'visibleweb' => '1');
        $filiales = Vfiliales::listarfiliales($conexion, $arrcondiciones);

        return $filiales;
    }

    public function getListadoRecesoFilial() {
        $conexion = $this->load->database($this->codigo, true);
        $this->load->helper('alumnos');
        $filial = new Vfiliales($conexion, $this->codigo);
        $listadoRecesoFilial = $filial->getListadoRecesoFilial();

        foreach ($listadoRecesoFilial as $key => $valor) {
            $listadoRecesoFilial[$key]['nombre'] = inicialesMayusculas($valor['nombre']);
            $listadoRecesoFilial[$key]['fecha_desde'] = formatearFecha_pais($valor['fecha_desde'], true);
            $listadoRecesoFilial[$key]['fecha_hasta'] = formatearFecha_pais($valor['fecha_hasta'], true);
        }

        return $listadoRecesoFilial;
    }

    public function getArrayRecesoFilial($cod_receso) {
        $conexion = $this->load->database($this->codigo, true);
        $this->load->helper('alumnos');
        $filial = new Vfiliales($conexion, $this->codigo);
        $listadoRecesoFilial = $filial->getListadoRecesoFilial($cod_receso);
        foreach ($listadoRecesoFilial as $key => $valor) {
            $listadoRecesoFilial[$key]['nombre'] = inicialesMayusculas($valor['nombre']);
        }
        return $listadoRecesoFilial;
    }

    public function guardarRecesoFilial($data_post) {
        $conexion = $this->load->database($this->codigo, true);
        $conexion->trans_begin();

        $myFilial = new Vfiliales($conexion, $this->codigo);
        $fecha_desde = formatearFecha_mysql($data_post['fecha_desde']);
        $fecha_hasta = formatearFecha_mysql($data_post['fecha_hasta']);
        $arrGuardarReceso = array(
            "fecha_desde" => $fecha_desde,
            "fecha_hasta" => $fecha_hasta,
            "hora_desde" => $data_post['hora_desde'],
            "hora_hasta" => $data_post['hora_hasta'],
            "nombre" => $data_post['nombre_receso'],
            "cod_filial" => $this->codigo,
            "estado" => 'habilitada'
        );

        if ($data_post['cod_receso'] == -1) {
            $myFilial->insertReceso($arrGuardarReceso);
        } else {
            $myFilial->updateReceso($data_post['cod_receso'], $arrGuardarReceso);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function baja_receso_filial($cod_receso) {
        $conexion = $this->load->database($this->codigo, true);
        $myFilial = new Vfiliales($conexion, $this->codigo);
        $respuesta = $myFilial->baja_receso_filial($cod_receso);
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    public function getReceso_filial() {
        $conexion = $this->load->database($this->codigo, true);
        $myFilial = new Vfiliales($conexion, $this->codigo);
        $listadoRecesoFilial = $myFilial->getListadoRecesoFilial();

        $fecha_receso_retorno = array();
        foreach ($listadoRecesoFilial as $receso) {
            $arrayFechasReceso[] = $this->getArrayFechasRecesoFilial($receso['fecha_desde'], $receso['fecha_hasta'], $receso['hora_desde'], $receso['hora_hasta']);

            foreach ($arrayFechasReceso as $key => $arrFechasRecesos) {
                foreach ($arrFechasRecesos as $arrFechas) {
                    //foreach($arrFechas as $fecha_receso){
                    $evento = array(
                        "id" => '',
                        "title" => $receso['nombre'],
                        "start" => $arrFechas['fecha'] . ' ' . $arrFechas['hora_desde'],
                        "end" => $arrFechas['fecha'] . ' ' . $arrFechas['hora_hasta'],
                        "color" => '#c459d6',
                        "allDay" => false,
                        "cod_salon" => '',
                        "cod_comision" => '',
                        "cod_materia" => '',
                        "nombre_comision" => '',
                        "nombre_curso" => '',
                        "editar" => false,
                        "tipo" => 'RECESO_FILIAL'
                    );
                    $fecha_receso_retorno[] = $evento;
                    //}
                }
            }
        }

        return $fecha_receso_retorno;
    }

    public function getArrayFechasRecesoFilial($fechaInicio, $fechaFin, $horaDesde, $horaHasta) {

        $fechaDesde = explode(' ', $fechaInicio);
        $fecha1 = new DateTime($fechaInicio);
        $fecha2 = new DateTime($fechaFin);
        $intervalo = $fecha2->diff($fecha1);
        $arrFechas = array();
        for ($i = 0; $i <= $intervalo->days; $i++) {
            $nuevafecha = strtotime($i . " day", strtotime($fechaDesde[0]));
            $nuevafecha = date('Y-m-d', $nuevafecha);
            $arrFechas[$i] = array(
                "fecha" => $nuevafecha,
                "hora_desde" => $horaDesde,
                "hora_hasta" => $horaHasta
            );
        }
        return $arrFechas;
    }

    public function getFilial() {
        $conexion = $this->load->database($this->codigo, TRUE);

        $arrcondiciones = array('codigo' => $this->codigo);

        $filiales = Vfiliales::listarfiliales($conexion, $arrcondiciones);

        return $filiales[0];
    }

    public function actualizarTablasUsuarioCreadorFilial() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        $tablasActualizar = array("matriculas", "aspirantes", "razones_sociales");
        foreach ($arrFiliales as $filial) {

            $conexion2 = $this->load->database($filial['codigo'], true);
            $cod_usuario = Vusuarios_sistema::cod_usuarioAdministrador($conexion2, $filial['codigo']);

            foreach ($tablasActualizar as $tabla) {
                $updateTabla = Vusuarios_sistema::actualizarTablas($conexion2, $tabla, $cod_usuario[0]['cod_usuario']);
            }
        }
    }

    public function getReglamentosFilial($tipo = null) {
        $conexion = $this->load->database($this->codigo, TRUE);
        $reglamentos = Vreglamentos::getReglamentosFiliales($conexion, $this->codigo, $tipo);
        return $reglamentos;
    }

    public function imprimeReglamento($nombre_reglamento) {
        $conexion = $this->load->database($this->codigo, TRUE);
        $reglamentos = Vreglamentos::getReglamentosFiliales($conexion, $this->codigo);
        $esta = false;
        foreach ($reglamentos as $value) {
            if ($value['nombre'] == $nombre_reglamento) {
                $esta = true;
            }
        }
        return $esta;
    }
    
    public function buscarCiudadesPorCodFiliales($codigos) {
        $conexion = $this->load->database('general', TRUE);
        $filiales = Vfiliales::buscarFilialesPorCodigo($conexion, $codigos);
        
        $ciudades = array();
        
        foreach ($filiales as $key => $filial) {
            if(!in_array($filial['ciudad'], $ciudades)){
                $ciudades[] = $filial['ciudad'];
            }
        }
        
        return $ciudades;
    }

}

/* End of file model_filiales.php */
/* Location: ./application/models/model_filiales.php */
