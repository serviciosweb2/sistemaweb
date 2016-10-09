<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_razones_sociales extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function getRazonesSocialesHabilitadas() {
        $conexion = $this->load->database($this->codigo_filial, true);

        $arrCondindiciones = array(
            "razones_sociales.baja" => 0,
        );

        $arrOrden = array(array(
                "campo" => "razones_sociales.razon_social", "orden" => "asc"
        ));

        $condiciones = Vrazones_sociales::listarRazones_sociales($conexion, $arrCondindiciones, null, $arrOrden);
        return $condiciones;
    }

    public function getCursosRazones($cod_razon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $curso = new Vrazones_sociales($conexion, $cod_razon);
        return $cursoRazonSocial = $curso->getCursoRazonSocial();
    }

    public function getRazonSocial($cod_razon_social) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objRazonSocial = new Vrazones_sociales($conexion, $cod_razon_social);
        return $objRazonSocial;
    }

    public function getTelefonoRazon($cod_razon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objRazonSocial = new Vrazones_sociales($conexion, $cod_razon);
        $telefonoRazon = $objRazonSocial->telfonoRazonSocial();
        return $telefonoRazon;
    }

    /**
     * Lista todos las razones sociales para datatable.
     * @access public
     * @param Array $arrFiltros filtros que se aplican.
     * @return Array datos razones
     */
    public function listarRazonesDatatable($arrFiltros) {
        $filial = $this->session->userdata('filial');
        $pais = $filial['pais'];
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrCondindiciones = array();

        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "razones_sociales.razon_social" => $arrFiltros["sSearch"],
                "razones_sociales.codigo" => $arrFiltros["sSearch"],
                "documento" => $arrFiltros["sSearch"],
                "email" => $arrFiltros["sSearch"],
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
        $datos = Vrazones_sociales::listarRazonesDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false);
        $contar = Vrazones_sociales::listarRazonesDataTable($conexion, $arrCondindiciones, null, null, true);

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

                $fecha = $row["fecha_alta"] == '0000-00-00 00:00:00' ? '-' : formatearFecha_pais($row["fecha_alta"]);
                $rows[] = array(
                    $row["codigo"],
                    $row["razon_social"],
                    $row["nombre"],
                    $row["documento"],
                    $row["nbrecondicion"],
                    $row["email"],
                    //$row["telefono_empresa"],
                    $telefono,
                    $fecha,
                    $row['estado'] = '',
                    $row["baja"]
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

                $fecha = $row["fecha_alta"] == '0000-00-00 00:00:00' ? '-' : formatearFecha_pais($row["fecha_alta"]);
                $rows[] = array(
                    $row["codigo"],
                    $row["razon_social"],
                    $row["nombre"],
                    $row["documento"],
                    $row["nbrecondicion"],
                    $row["email"],
                    $fecha,
                    $row['estado'] = '',
                    $row["baja"]
                );
            }
        }


        $retorno['aaData'] = $rows;

        return $retorno;
    }

    public function guardar($arrdatos) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();

        $datos = $arrdatos['razon_social'];

        $objRazon = new Vrazones_sociales($conexion, $datos['codigo']); //ver fecha alta
        $fechaalta = $objRazon->getCodigo() != '-1' ? $objRazon->fecha_alta : date('Y-m-d H:i:s');
        $objRazon->guardar($datos['razon_social'], $fechaalta, $datos['tipo_documentos'], $datos['documento'], $datos['cod_localidad'], 
                $datos['direccion_calle'], $datos['direccion_numero'], $datos['complemento'], $datos['email'], $datos['cod_postal'], 
                $datos['condicion'], $datos['inicio_actividades'], $datos['usuario_creador']);

        $arrTelefonos = $arrdatos['telefonos'];

        foreach ($arrTelefonos as $rowtel) {
            //GUARDA CADA TELEFONO.
            $tel = new Vtelefonos($conexion, $rowtel["codigo"]);
            $tel->setTelefonos($rowtel);
            $tel->guardarTelefonos();
            if ($rowtel["codigo"] == '-1') {
                $objRazon->setTelefonoRazon($tel->getCodigo());
            }
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, array("codigo_razon_social" => $objRazon->getCodigo(), "nombre_razon_social" => $objRazon->razon_social));
    }

    /**
     * cambia el estado de una razon
     * @access public
     * @return repuesta Guardar el cambio de estado.
     */
    public function cambiarEstado($cod_razon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $razon = new Vrazones_sociales($conexion, $cod_razon);
        $respuesta = '';

        if ($razon->baja == '0') {
            $respuesta = $razon->baja();
        } else {
            $respuesta = $razon->alta();
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {

            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function getArrRazonSocial($cod_razon_social) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrRazones = Vrazones_sociales::getRazonSocial($conexion, $cod_razon_social);
        return $arrRazones[0];
    }

    public function esRazonSocialDefault($cod_razon) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrRazones = Vrazones_sociales::getRazonesSocialesNoDefault($conexion, null, $cod_razon);
        $respuesta = count($arrRazones) > 0 ? false : true;
        return $respuesta;
    }

}
