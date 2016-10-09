<?php

class Model_responsables extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }

    public function getResponsable($codigo = null, $tipo_identificacion = null, $numero_identificacion = null) {
        $conexion = $this->load->database($this->codigofilial, true);

        $retorno = array('responsable' => array(), 'telefono' => array());

        $arrCondiciones = array();

        if ($codigo == null || $codigo == '') {
            // por tipo y numero de documento
            $responsable = Vresponsables::buscarPorIdentificacion($conexion, $tipo_identificacion, $numero_identificacion);

            foreach ($responsable as $resp) {
                $myResponsable = new Vresponsables($conexion, $resp["codigo"]);

                if ($resp['inicio_actividades'] != '') {
                    $resp['inicio_actividades'] = formatearFecha_pais($resp['inicio_actividades']);
                }

                $retorno['responsable'] = $resp;
                $retorno['telefono'] = $myResponsable->getTelefonos();
            }
        } else {

            $conexionGeneral = $this->load->database('', true);

            $myResponsable = new Vresponsables($conexion, $codigo);
            $razon_responsables = $myResponsable->getRazonSocial();
            $tipo_documento = new Vdocumentos_tipos($conexionGeneral, $razon_responsables[0]['tipo_documentos']);

            $retorno = $razon_responsables[0];
            $retorno['nombre_identificacion'] = $tipo_documento->nombre;
            $retorno['baja_responsable'] = $myResponsable->baja;
            $retorno['direccion'] = $razon_responsables[0]['direccion'];
            $condicion = Vcondiciones_sociales::listarCondiciones_sociales($conexion, array('codigo' => $retorno['condicion']));
            $retorno['nombre_condicion'] = $condicion[0]['condicion'];
        }


        return $retorno;
    }

    public function guardarResponsables($arrResponsables, $conexion = null, $cod_alumno = null) {

        $transInt = false;
        if ($conexion == null) {
            $transInt = true;
            $conexion = $this->load->database($this->codigofilial, true);
            $conexion->trans_begin();
        }

        $this->load->helper('alumnos');

        //RECORRO TODOS LOS REPONSABLES DEL ARRAY.
        //SETEO Y GUARDO LOS RESPONSABLES.
        $cod_responsable = $arrResponsables["codigo"];
        $responsables = new Vresponsables($conexion, $cod_responsable);
        $arrGuardarResp = array(
            "nombre" => $arrResponsables['nombre'],
            "apellido" => $arrResponsables['apellido'],
            "baja" => $arrResponsables['baja']
        );
        $responsables->setResponsables($arrGuardarResp);
        $responsables->guardarResponsables();
        $razon = $responsables->getRazonSocial();
        $filial = new Vfiliales($conexion, $this->codigofilial);

        $condiciones = array('cod_pais' => $filial->pais, 'default' => '1');
        $condicionfiscal = Vcondiciones_sociales::listarCondiciones_sociales($conexion, $condiciones);

        //prepara array de razon
        $nombre = inicialesMayusculas($arrResponsables['nombre']);
        $apellido = inicialesMayusculas($arrResponsables['apellido']);
        $arrRazonesResponsable = array(
            'razon_social' => formatearNombreApellido($nombre, $apellido),
            'documento' => $arrResponsables['documento'],
            'condicion' => $condicionfiscal[0]['codigo'],
            'baja' => $arrResponsables['baja'],
            'tipo_documentos' => $arrResponsables['tipo_documentos'],
            'direccion_calle' => $arrResponsables['direccion_calle'],
            'direccion_numero' => $arrResponsables['direccion_numero'],
            'direccion_complemento' => $arrResponsables['direccion_complemento'],
            'email' => $arrResponsables['email'],
            'cod_localidad' => $arrResponsables['cod_localidad'],
            'inicio_actividades' => $arrResponsables['fecha_naci'],
            'codigo_postal' => $arrResponsables['cod_postal'],
            'fecha_alta' => date("Y-m-d H:i:s"),
            'usuario_creador' => $arrResponsables['usuario_creador']
        );

        // pregunta si hay o no razon para el responsable
        $cod_razon = count($razon) > 0 ? $razon[0]['cod_razon_social'] : -1;


        $razonSocial = new Vrazones_sociales($conexion, $cod_razon);
        $razonSocial->setRazones_sociales($arrRazonesResponsable);
        $razonSocial->guardarRazones_sociales();

        //SETEO LA RAZON AL RESPONSABLE.
        $razResponsable = array(
            'cod_responsable' => $responsables->getCodigo(),
            'cod_razon_social' => $razonSocial->getCodigo(),
            'default' => 0
        );


        if ($cod_razon == -1) {
            $responsables->setRazonSocialResponsable($razResponsable);
        }








//SETEO EL RESPONSABLE AL ALUMNO.
        if ($cod_responsable == -1) {

//                $alumno = new Valumnos($conexion, $cod_alumno);
//                $ArrResponsableAlumno = array(
//                    "cod_alumno" => $cod_alumno,
//                    "cod_responsable" => $responsables->getCodigo(),
//                );
//                $alumno->setResponsable($ArrResponsableAlumno);
            //GUARDO LAS RAZONES DE LOS RESPONSABLES EN LA TABLA RAZONES_SOCIALES.
        }

        //GUARDO LOS TELEFONOS DEL RESPONSABLE.
        if ($arrResponsables["telefonos"] != 0) {
            foreach ($arrResponsables["telefonos"] as $rowtelefono) {
                $telefonos = new Vtelefonos($conexion, $rowtelefono["codigo"]);
                $telefonos->setTelefonos($rowtelefono);
                $telefonos->guardarTelefonos();
                $default = isset($rowtelefono['default']) ? $rowtelefono['default'] : 0;

                //SETEO TELEFONO AL RESPONSABLE.
                if ($rowtelefono["codigo"] == -1) {
                    $responsables->setTelefono($telefonos->getCodigo(), $default);
                } else {
                    $responsables->updateTelefonosResponsables($telefonos->getCodigo(), $default);
                }
            }
        }


        $estadotran = $conexion->trans_status();

        if ($transInt) {
            $estadotran = $conexion->trans_status();
            if ($estadotran === FALSE) {
                $conexion->trans_rollback();
            } else {
                $conexion->trans_commit();
            }
        }

        $respuesta = class_general::_generarRespuestaModelo($conexion, $estadotran);

        $respuesta['cod_responsable'] = $responsables->getCodigo();
        $respuesta['razones_sociales_responsable'] = array(
            "cod_razon_social" => $razonSocial->getCodigo(),
            'condicion' => $arrResponsables['condicion'],
            'documento' => $arrResponsables['documento'],
            "razon_social" => $arrRazonesResponsable['razon_social'],
            'direccion_calle' => $arrResponsables['direccion_calle'],
            'email' => $arrResponsables['email'],
        );
        return $respuesta;
    }

    public function listarResponsablesDatatable($arrFiltros, $separador) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
//                "responsable.codigo" => $arrFiltros["sSearch"],
//                "alumnos.documento" => $arrFiltros["sSearch"],
//                "alumnos.email" => $arrFiltros["sSearch"],
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

        $datos = Vresponsables::listarResponsablesDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $separador);
        $contar = Vresponsables::listarResponsablesDataTable($conexion, $arrCondindiciones, null, null, true, $separador);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
//        echo '<pre>'; 
//        print_r($datos);
//        echo '</pre>';
//        die();

        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                inicialesMayusculas($row["nombre_apellido"]),
                $row["nombre_identificacion"],
                $row['email'],
                $row['direccion'],
                '', //estado
                $row["responsable_baja"],
                $row['cod_razon_social'],
                $row['nombre_condicion']
            );
        }

        $retorno['aaData'] = $rows;

        return $retorno;
    }

}
