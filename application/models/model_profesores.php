<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * model profesores
 */

class Model_profesores extends CI_Model {

    var $codigo = 0;
    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;

        $this->codigofilial = $arg["codigo_filial"];
    }

    /**
     * retorna un objeto profesor.
     * @access public
     * @param int $codigo codigo de profesor
     * @return Objeto Aspirante
     */
    public function getProfesor($codigo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $profesor = new Vprofesores($conexion, $codigo);
        return $profesor;
    }

    /**
     * retorna todos los telefonos de un profesores
     * @access public
     * @param int $codigo_profesor codigo del profesor
     * @return Array de telefonos.
     */
    public function getTelefonos($codigo_profesor) {
        $conexion = $this->load->database($this->codigofilial, true);
        $profesor = new Vprofesores($conexion, $codigo_profesor);
        return $arrTelefonos = $profesor->getTelefonos();
    }

    /**
     * retorna todos los profesores para el data table
     * @access public
     * @return Array de profesores
     */
    public function listarProfesoresdataTable($arrFiltros,$separador) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');

        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];

        $arrCondindiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
                "profesores.mail" => $arrFiltros["sSearch"],
                "profesores.codigo" => $arrFiltros["sSearch"],
                "profesores.nrodocumento" => $arrFiltros["sSearch"]
            );
        }
        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {

            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }
        $arrSort = array();

        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {

            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vprofesores::listarProfesoresDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort,false, $separador);
        $contar = Vprofesores::listarProfesoresDataTable($conexion, $arrCondindiciones, "", "", true,$separador);
       
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        if($pais == 2)
        {
            foreach ($datos as $row) {
                $telefono = $row["tel_cod_area"]. " ". $row["tel_numero"];
                if($row["telefono_empresa"] != '')
                {
                    $telefono = $row["telefono_empresa"]. ': <br> '. $row["tel_cod_area"]. " ". $row["tel_numero"];
                }
                $rows[] = array(
                    $row["codigo"],
                    inicialesMayusculas($row["nombre_apellido"]),
                    $row["mail"],
                    $row["nrodocumento"],
                    $telefono,
                    formatearFecha_pais($row["fechaalta"]),
                    $row['baja'] = '',
                    $row["estado"]
                );
            }
        }
        else
        {
            foreach ($datos as $row) {
                $telefono = $row["tel_cod_area"]. " ". $row["tel_numero"];
                if($row["telefono_empresa"] != '')
                {
                    $telefono = $row["telefono_empresa"]. ': <br> '. $row["tel_cod_area"]. " ". $row["tel_numero"];
                }
                $rows[] = array(
                    $row["codigo"],
                    inicialesMayusculas($row["nombre_apellido"]),
                    $row["mail"],
                    $row["nrodocumento"],
                    formatearFecha_pais($row["fechaalta"]),
                    $row['baja'] = '',
                    $row["estado"]
                );
            }
        }

        $retorno['aaData'] = $rows;
        return $retorno;
    }

    /**
     * Cambia el estado del profesor
     * @access public
     * @return respuesta cambiar estado.
     */
    public function cambioEstadoProfesor($cambioprofesor) {
        $conexion = $this->load->database($this->codigofilial, true);
        $profesores = new Vprofesores($conexion, $cambioprofesor['codprofesor']);
        $estado = $profesores->cambiarEstado($cambioprofesor);
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    /**
     * retorna todos los profesores.
     * @access public
     * @return Array profesores.
     */
    public function getProfesores() {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
//        $apellidoprimero = Vconfiguracion::getValorConfiguracion($conexion, null, 'NombreFormato');
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $apellidoprimero = $filial['nombreFormato']['formatoNombre'];
        

        $condicion = array('estado' => 'habilitado');
        if ($apellidoprimero == '1') {
            $orden = array(array('campo' => 'apellido', 'orden' => 'asc'));
        } else {
            $orden = array(array('campo' => 'nombre', 'orden' => 'asc'));
        }

        $profesores = Vprofesores::listarProfesores($conexion, $condicion, null, $orden);
        
       foreach($profesores as $key => $profesor){
           $nombre = inicialesMayusculas($profesor['nombre']);
           $apellido = inicialesMayusculas($profesor['apellido']);
           $profesores[$key]['nombre'] = formatearNombreApellido($nombre, $apellido, $separador, $apellidoprimero);
       }
        return $profesores;
    }

    /**
     * guarda un profesor con todo lo que corresponde
     * un profesor tiene relacionado telefonos en trasaccion.
     * @access public
     * @param Array $arrProfesor todos los datos que salen del formulario profesor
     * @return repuesta Guardar
     */
    public function guardarProfesorGeneral($arrProfesor,$cod_usuario) {
        //print_r($arrProfesor);
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $RProfesor = $this->guardarProfesor($arrProfesor, $conexion);
        $codigonuevoprofesor = $RProfesor["custom"]["codigo"];
        if (count($arrProfesor["razonsocial"]) != 0) {
            $this->guardarRazonesSociales($arrProfesor["razonsocial"], $conexion, $codigonuevoprofesor);
        }
        //RAZON SOCIAL POR DEFECTO
        $profesor = new Vprofesores($conexion, $arrProfesor['profesor']['codigo']);
        $resp = $profesor->getRazonSocialDefault();
        $codigorazon = isset($resp[0]['cod_razon']) ? $resp[0]['cod_razon'] : -1;

        //recupero de la filial la condicion fiscal por defecto
        $filial = new Vfiliales($conexion, $this->codigofilial);
        $condicion = $filial->getCondicionFiscalDefault();

        //TRAER CONDICION_SOCIAL POR DEFAULT POR PAIS
        $razon[] = array(
            'tipo_documentos' => $arrProfesor['profesor']['tipodocumento'],
            'documento' => $arrProfesor['profesor']['nrodocumento'],
            'razon_social' => $arrProfesor['profesor']['apellido'] . ', ' . $arrProfesor['profesor']['nombre'],
            'tipo_documentos'=>$arrProfesor['profesor']['tipodocumento'],
            'direccion_calle' => $arrProfesor['profesor']['calle'],
            'direccion_numero'=> $arrProfesor['profesor']['numero'],
            'direccion_complemento'=> $arrProfesor['profesor']['complemento'],
            'cod_localidad'=>$arrProfesor['profesor']['cod_localidad'],
            'email'=>$arrProfesor['profesor']['mail'],
            'codigo_postal'=>$arrProfesor['profesor']['codigopostal'],
            'fecha_alta'=> $arrProfesor['profesor']['fechaalta'],
            'inicio_actividades'=>$arrProfesor['profesor']['fechanac'],
            'barrio'=>$arrProfesor['profesor']['barrio'],
            'codigo' => $codigorazon,
            'baja' => 0,
            'condicion' => $condicion[0]['codigo'],
            'default' => 1,
            'usuario_creador'=>$cod_usuario
        );
        $this->guardarRazonesSociales($razon, $conexion, $codigonuevoprofesor);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /**
     * guarda un profesor con todo lo que corresponde
     * @access public
     * @param Array $arrProfesorGeneral todos los datos que salen del formulario alumno.
     * @return repuesta Guardar
     */
    public function guardarProfesor($arrProfesorGeneral, $conexion = null) {
        $transInt = false;
        $arrProfesor = $arrProfesorGeneral["profesor"];
        $arrTelefonos = $arrProfesorGeneral["telefonos"];
        if ($conexion != null) {
            $conexion = $this->load->database($this->codigofilial, true);
            $transInt = true;
        }
        $profesores = new Vprofesores($conexion, $arrProfesor["codigo"]);
        $profesores->setProfesores($arrProfesor);
        $estado = $profesores->guardarProfesores();

        foreach ($arrTelefonos as $rowtel) {
            $tel = new Vtelefonos($conexion, $rowtel["codigo"]);
            $tel->setTelefonos($rowtel);
            $tel->guardarTelefonos();
            $default = $rowtel['default'];
            if ($rowtel["codigo"] == -1) {
                $profesores->setTelefonos($tel->getCodigo(), $default);
            } else {
                $profesores->updateTelefonos($tel->getCodigo(), $default);
            }
        }
        $arrRespuesta = array("codigo" => $profesores->getCodigo());
        return class_general::_generarRespuestaModelo($conexion, $estado, $arrRespuesta);
    }

    /**
     * guarda razones sociales del profesor con lo que corresponde
     * @access public
     * @return repuesta Guardar
     */
    public function guardarRazonesSociales($arrRazones, $conexion = null, $cod_profesor = null) {
        $transInt = false;
        if ($conexion != null) {
            $transInt = true;
            $conexion = $this->load->database($this->codigofilial, true);
            $conexion->trans_begin();
        }
        foreach ($arrRazones as $rowRazones) {
            $cod_razon = $rowRazones['codigo'];
            $razones = new Vrazones_sociales($conexion, $cod_razon);
            $razones->setRazones_sociales($rowRazones);
            $razones->guardarRazones_sociales();
            if ($cod_razon == -1) {
                $profesor = new Vprofesores($conexion, $cod_profesor);
                $arrRazonProfesor = array(
                    "cod_profesor" => $cod_profesor,
                    "cod_razon" => $razones->getCodigo(),
                );
                if (isset($rowRazones["default"])) {
                    $arrRazonProfesor["default"] = $rowRazones['default'] == 1 ? '1' : '0';
                } else {
                    $arrRazonProfesor["default"] = '0';
                }
                $profesor->setRazonesSociales($arrRazonProfesor);
            }
        }
        if ($transInt) {
            $estadotran = $conexion->trans_status();
            if ($estadotran === FALSE) {
                $conexion->trans_rollback();
            } else {
                $conexion->trans_commit();
            }
        }
    }

    /**
     * retorna todos los motivos de las baja
     * @access public
     * @return Array motivos baja
     */
    public function getMotivosBaja() {
        $conexion = $this->load->database($this->codigofilial, true);
        $proestado = new Vprofesores_estado_historico($conexion);
        return $proestado->getmotivos();
    }

    public function getRazonesSociales($codigo_profesor) {
        $conexion = $this->load->database($this->codigofilial, true);
        $profesor = new Vprofesores($conexion, $codigo_profesor);
        return $profesor->getRazonSocialprofesor();
    }

    public function getReporteProfesores($idFilial, $arrLimit = null, $arrSort = null, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vprofesores::getReporteProfesores($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta);
        $registros = Vprofesores::getReporteProfesores($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    /**
     * retorna todos los profesores.
     * @access public
     * @return Array profesores.
     */
    public function getProfesoresconHorarios() {
        $conexion = $this->load->database($this->codigofilial, true);
        $apellidoprimero = Vconfiguracion::getValorConfiguracion($conexion, null, 'NombreFormato');
        $separador = Vconfiguracion::getValorConfiguracion($conexion, null, 'NombreSeparador');
        if ($apellidoprimero == '1') {
            $orden = array('campo' => 'apellido', 'orden' => 'asc');
        } else {
            $orden = array('campo' => 'nombre', 'orden' => 'asc');
        }
        $profesores = Vprofesores::getProfesoresconHorarios($conexion, $orden);

        for ($i = 0; $i < count($profesores); $i++) {
            $profesores[$i]['nombre'] = $apellidoprimero == '1' ? $profesores[$i]['apellido'] . $separador . ' ' . $profesores[$i]['nombre'] : $profesores[$i]['nombre'] . $separador . ' ' . $profesores[$i]['apellido'];
        }
        return $profesores;
    }

}
