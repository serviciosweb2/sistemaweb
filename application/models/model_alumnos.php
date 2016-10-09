<?php

/**
 * Model_alumnos
 *
 * Description...
 *
 * @package model_alumnos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_alumnos extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }
    //modificacion franco ticket 5053->
    public function listarAlumnosbusca($arrFiltros, $separador, $fechaaltaDesde = null, $fechaaltaHasta = null, $talle = null,
                /*$tipoContacto = null,*/ $provincia = null, $localidad = null, $como_nos_conocio = null, $estado = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
                "alumnos.codigo"=>$arrFiltros["sSearch"],
                "alumnos.documento"=>$arrFiltros["sSearch"],
                "alumnos.email" => $arrFiltros["sSearch"]
                //"alumnos.tipo_contacto"=>$arrFiltros["sSearch"]
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
        $datos = Valumnos::listarAlumnosDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $separador,
                $fechaaltaDesde, $fechaaltaHasta, $talle, /*$tipoContacto,*/ $provincia, $localidad, $como_nos_conocio, $estado);
        $contar = Valumnos::listarAlumnosDataTable($conexion, $arrCondindiciones, null, null, true, $separador,
                $fechaaltaDesde, $fechaaltaHasta, $talle, /*$tipoContacto,*/ $provincia, $localidad, $como_nos_conocio, $estado);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                inicialesMayusculas($row["nombre_apellido"]),
                formatearFecha_pais($row["fechanaci"]),

                $row["documento"],

                $row["localidad"],

                $row["calle2"],
                $row["descripcion_".get_idioma()],

                $row["email"],

                $row["tipo_doc"]." ".$row["razon_doc"],
                $row["talle"],
                formatearFecha_pais($row["fechaalta"]),
                $row["baja"],
                $row['estado'] = '',
                $row["reenviar_mail"]
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }
    // <-modificacion franco ticket 5053

    /**
     * Lista todos los alumnos para datatable.
     * @access public
     * @param Array $arrFiltros filtros que se aplican.
     * @return Array datos alumno.
     */
    public function listarAlumnosDatatable($arrFiltros, $separador) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
                "alumnos.codigo" => $arrFiltros["sSearch"],
                "alumnos.documento" => $arrFiltros["sSearch"],
                "alumnos.email" => $arrFiltros["sSearch"],
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

        $datos = Valumnos::listarAlumnosDataTable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $separador);
        $contar = Valumnos::listarAlumnosDataTable($conexion, $arrCondindiciones, null, null, true, $separador);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();

        foreach ($datos as $row) {

            $rows[] = array(
                $row["codigo"],
                inicialesMayusculas($row["nombre_apellido"]),
                formatearFecha_pais($row["fechanaci"]),

                $row["documento"],

                $row["localidad"],
                //modificacion franco ticket 5053-> (muestro los datos correspondientes a lo ingresado en CrearColumnas())
                $row["calle2"],
                $row["descripcion_".get_idioma()],

                $row["email"],
               // $row["razon_social"],
                $row["tipo_doc"]." ".$row["razon_doc"],
                $row["talle"],
                //<-modificacion franco ticket 5053
                formatearFecha_pais($row["fechaalta"]),
                $row["baja"],

                $row['estado'] = '',
                $row["reenviar_mail"]
            );
        }

        $retorno['aaData'] = $rows;

        return $retorno;
    }

    /**
     * Recupera un alumno por codigo
     * @access public
     * @param int $codigo codigo de alumno
     * @return Objeto Alumno
     */
    public function getAlumno($codigo) {

        $conexion = $this->load->database($this->codigofilial, true);

        $alumnos = new Valumnos($conexion, $codigo);

        return $alumnos;
    }

    /**
     * convierte un array de aspirante a objeto Alumno.
     * @access public
     * @param Array $arrAspirante $array de los datos de un aspirante.
     * @return Objeto Alumno
     */
    public function convertirAspiranteAlumno($arrAspirante) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumnos = new Valumnos($conexion);
        $alumnos->setAlumnos($arrAspirante);
        return $alumnos;
    }

    /**
     * recupera todos los telefonos de un alumno en particular.
     * @access public
     * @param  int $codigo_alumno codigo de alumno que quiero recuperar telefonos.
     * @return Array Telefonos.
     */
    public function getTelefonos($codigo_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);
        return $alumnos->getTelefonos();
    }

    /**
     * recupera todas las razones sociales de un alumno
     * @access public
     * @param  int $codigo_alumno codigo de alumno que quiero recuperar razones sociales.
     * @return array razones sociales
     */
    public function getRazonesSociales($codigo_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);

        return $alumnos->getRazonesSociales();
    }

    /**
     * recupera todo/as responsables de un alumno
     * @access public
     * @param  int $codigo_alumno codigo de alumno que quiero recuperar sus responsables
     * @return array $arrNewResponsables
     */
    public function getResponsables($codigo_alumno) {
        $conexionGeneral = $this->load->database('', true);
        $conexion = $this->load->database($this->codigofilial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);

        $reponsables = $alumnos->getResponsables();

        $arrNewResponsables = array();
        $i = 0;
        foreach ($reponsables as $res) {

            $tipo_documento = new Vdocumentos_tipos($conexionGeneral, $res['tipo_documentos']);

            $res['tipo_doc'] = $tipo_documento->nombre;
            //$res['nombre']=formatearNombreApellido($res['nombre'],$res['apellido']);
            $arrNewResponsables[$i]["responsable"] = $res;
            $responsables = new Vresponsables($conexion, $res["codigo"]);



            $arrNewResponsables[$i]["telefono"] = $responsables->getTelefonos();


            $i++;
        }

        return $arrNewResponsables;
    }

    /**
     * guarda un alumno con todo lo que corresponde
     * @access public
     * @param Array $arrAlumnoGeneral todos los datos que salen del formulario alumno.
     * @return repuesta Guardar
     */
    public function guardarAlumno($arrAlumnoGeneral, $conexion = null) {
        $transInt = false;
        $estado = '';
        $arrAlumno = $arrAlumnoGeneral["alumno"];
        $arrTelefonos = $arrAlumnoGeneral["telefonos"];
        $imagen = isset($arrAlumnoGeneral['imagen']) ? $arrAlumnoGeneral['imagen'] : false;
        $vieneconexion = true;
        if ($conexion == null) {//estaba !=
            $vieneconexion = false;
            $conexion = $this->load->database($this->codigofilial, true);
            $conexion->trans_start();
            $transInt = true;
        }
        //SETEA Y GUARDA EL ALUMNO
        $alumnos = new Valumnos($conexion, $arrAlumno["codigo"]);
        $emailantes = $alumnos->email;
        if ($alumnos->baja == '') {
            $estado = 'habilitada';
        } else {
            if (isset($alumnos->baja)) {
                $estado = $alumnos->baja == 'habilitada' ? 'habilitada' : 'inhabilitada';
            }
        }
        $arrAlumno['baja'] = $estado;
        $arrAlumno['fechaalta'] = $arrAlumno["codigo"] != -1 ? $alumnos->fechaalta : date("Y-m-d H:i:s");
        $alumnos->setAlumnos($arrAlumno);
        $estado = $alumnos->guardarAlumnos();
        //Paso al aspirante a alumno
        if ($arrAlumno['cod_aspirante'] != '') {
            $arrayAspAlu = array(
                "id_alumno" => $alumnos->getCodigo(),
                "id_aspirante" => $arrAlumno['cod_aspirante']
            );
            $alumnos->setAspirante_Alumno($arrayAspAlu);
        }

        //RECORRE TODOS LOS TELEFONOS DEL ALUMNO.
        foreach ($arrTelefonos as $rowtel) {
            //GUARDA CADA TELEFONO.
            $tel = new Vtelefonos($conexion, $rowtel["codigo"]);
            $tel->setTelefonos($rowtel);
            $tel->guardarTelefonos();

            //SETEA TELEFONO A ALUMNO
            $default = $rowtel['default'];
            if ($rowtel["codigo"] == -1) {

                $alumnos->setTelefono($tel->getCodigo(), $default);
            } else {
                if ($arrAlumno['cod_aspirante'] != '') {
                    $alumnos->setTelefono($tel->getCodigo(), $default);
                } else {
                    $alumnos->updateTelefonoAlumno($tel->getCodigo(), $default);
                }
            }
        }

        $codalumno = (string) $alumnos->getCodigo();

        if ($imagen){
            $alumnos->setImagen($imagen);
        }

        $arrRespuesta = array("cod_alumno" => $codalumno);
        if (!$vieneconexion) {
            $conexion->trans_complete();
        }


        if (($emailantes == null || $emailantes == '') && ($alumnos->email != null || $alumnos->email != '')) {

            // ALTA CAMPUS
            $this->alta_campus_nuevo($alumnos->nombre, $alumnos->apellido, $alumnos->email, $alumnos->sexo, get_idioma(), $this->codigofilial, $codalumno);

        }else{
            // Actualizamos email
            $codalumno = intval($codalumno);
            $codfilial = $this->codigofilial;
            $this->actualizar_email_campus($emailantes, $alumnos->email, $codalumno, $codfilial);

        }

        return class_general::_generarRespuestaModelo($conexion, $estado, $arrRespuesta);
    }

    /**
     * guarda responsables de alumno con lo que corresponde
     * @access public
     * @param Array $arrResponsables todos los datos que salen del formulario responsable.
     * @return repuesta Guardar
     */
    public function guardarResponsables($arrResponsables, $conexion = null, $cod_alumno = null) {

        $transInt = false;
        if ($conexion == null) {
            $transInt = true;
            $conexion = $this->load->database($this->codigofilial, true);
            $conexion->trans_begin();
        }
        $this->load->helper('alumnos');

        //RECORRO TODOS LOS REPONSABLES DEL ARRAY.
        foreach ($arrResponsables as $rowResponsables) {
            //SETEO Y GUARDO LOS RESPONSABLES.
            $cod_responsable = $rowResponsables["codigo"];
            $responsables = new Vresponsables($conexion, $cod_responsable);
            $arrGuardarResp = array(
                "nombre" => $rowResponsables['nombre'],
                "apellido" => $rowResponsables['apellido'],
                "baja" => $rowResponsables['baja']
            );
            $responsables->setResponsables($arrGuardarResp);
            $responsables->guardarResponsables();

            //SETEO EL RESPONSABLE AL ALUMNO.
            if ($cod_responsable == -1) {
                $alumno = new Valumnos($conexion, $cod_alumno);
                $ArrResponsableAlumno = array(
                    "cod_alumno" => $cod_alumno,
                    "cod_responsable" => $responsables->getCodigo(),
                );
                $alumno->setResponsable($ArrResponsableAlumno);

                //GUARDO LAS RAZONES DE LOS RESPONSABLES EN LA TABLA RAZONES_SOCIALES.
                $nombre = inicialesMayusculas($rowResponsables['nombre']);
                $apellido = inicialesMayusculas($rowResponsables['apellido']);
                $arrRazonesResponsable = array(
                    'razon_social' => formatearNombreApellido($nombre, $apellido),
                    'documento' => $rowResponsables['documento'],
                    'condicion' => $rowResponsables['condicion'],
                    'baja' => 0,
                    'tipo_documentos' => $rowResponsables['tipo_doc'],
                    'direccion_calle' => $rowResponsables['calle'],
                    'direccion_numero' => $rowResponsables['calle_numero'],
                    'direccion_complemento' => $rowResponsables['calle_complemento'],
                    'email' => $rowResponsables['email'],
                    'fecha_alta' => date("Y-m-d H:i:s")
                );
                $razonSocial = new Vrazones_sociales($conexion);
                $razonSocial->setRazones_sociales($arrRazonesResponsable);
                $razonSocial->guardarRazones_sociales();

                //SETEO LA RAZON AL RESPONSABLE.
                $razResponsable = array(
                    'cod_responsable' => $responsables->getCodigo(),
                    'cod_razon_social' => $razonSocial->getCodigo(),
                    'default' => $rowResponsables['default']
                );
                $responsables->setRazonSocialResponsable($razResponsable);
            }
//            } else {
//                $arrRazonesResponsable = array(
//                    'razon_social' => $rowResponsables['razon_social'],
//                    'documento' => $rowResponsables['documento'],
//                    'condicion' => $rowResponsables['condicion'],
//                    'baja' => 0,
//                    'tipo_documentos' => $rowResponsables['tipo_doc']
//                );
//                $objRazon = new Vrazones_sociales($conexion, $rowResponsables['cod_razon_social']);
//                $objRazon->updateRazonResponsables($rowResponsables['cod_razon_social'], $arrRazonesResponsable);
//            }
            //GUARDO LOS TELEFONOS DEL RESPONSABLE.
            if ($rowResponsables["telefonos"] != 0) {
                foreach ($rowResponsables["telefonos"] as $rowtelefono) {
                    $telefonos = new Vtelefonos($conexion, $rowtelefono["codigo"]);
                    $telefonos->setTelefonos($rowtelefono);
                    $telefonos->guardarTelefonos();
                    $default = $rowtelefono['default'];

                    //SETEO TELEFONO AL RESPONSABLE.
                    if ($rowtelefono["codigo"] == -1) {
                        $responsables->setTelefono($telefonos->getCodigo(), $default);
                    } else {
                        $responsables->updateTelefonosResponsables($telefonos->getCodigo(), $default);
                    }
                }
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

    /*     * NO VA
     * guarda razones sociales de alumno con lo que corresponde
     * @access public
     * @return repuesta Guardar
     */

    public function guardarRazonesSociales($arrRazones, $conexion = null, $cod_alumno = null) {

        $transInt = false;
        $codigoRazonDefault = -1;
        $codigoRazonDefaultFacturacion = -1;
        if ($conexion != null) {
            $transInt = true;
            $conexion = $this->load->database($this->codigofilial, true);
            $conexion->trans_begin();
        }
        //RECORRE TODAS LAS RAZONES QUE TENGA EL ARRAY.

        foreach ($arrRazones as $rowRazones) {
            //SETETA Y GUARDA CADA RAZON DEL ARRAY.
            $cod_razon = $rowRazones['codigo'];
            $razones = new Vrazones_sociales($conexion, $cod_razon);
            $defaultfacturacion = '';
            $razones->setRazones_sociales($rowRazones);
            $razones->guardarRazones_sociales();
            $alumno = new Valumnos($conexion, $cod_alumno);
            $telDefaultAlumno = $alumno->getTelefonos(true);

            if ($cod_razon == -1) {
                //SETEA LA RAZON SOCIAL AL ALUMNO
                //$alumno = new Valumnos($conexion, $cod_alumno);
                $arrRazonAlumnos = array(
                    "cod_alumno" => $cod_alumno,
                    "cod_razon_social" => $razones->getCodigo(),
                );
                //                $arrRazonAlumnos["default"] = 0;
                //                $arrRazonAlumnos["default_facturacion"] = 0;

                if (isset($rowRazones["default"]) && $rowRazones['default'] == 1) {
                    $codigoRazonDefault = $razones->getCodigo();
                }

                if (isset($rowRazones["default_facturacion"]) && $rowRazones["default_facturacion"] == 1) {
                    $codigoRazonDefaultFacturacion = $razones->getCodigo();
                }
                $alumno->setRazonesSociales($arrRazonAlumnos, $defaultfacturacion);
                $razones->setTelefonoRazon($telDefaultAlumno[0]['codigo']);
            } else {


                if ($rowRazones['default'] == 1) {
                    $codigoRazonDefault = $razones->getCodigo();
                }

                if ($rowRazones["default_facturacion"] == 1) {
                    $codigoRazonDefaultFacturacion = $razones->getCodigo();
                }
            }
        }
        if ($codigoRazonDefault <> -1) {

            $alumno->setRazonDefault($codigoRazonDefault);
        }

        if ($codigoRazonDefaultFacturacion <> -1) {
            $alumno->setRazonDefaultFacturacion($codigoRazonDefaultFacturacion);
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

    /*     * NO VA
     * guarda alumno general
     * @access public
     * @return repuesta Guardar
     */

    public function guardarAlunoGeneralViejo($arrAlumnoCompleto) {

        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $this->load->helper('alumnos');
        $RAlumno = $this->guardarAlumno($arrAlumnoCompleto, $conexion);

        $codigonuevoalumno = $RAlumno["custom"]["cod_alumno"];

        if (count($arrAlumnoCompleto["razonsocial"]) != 0) {

            $this->guardarRazonesSociales($arrAlumnoCompleto["razonsocial"], $conexion, $codigonuevoalumno);
        }

        //RAZON SOCIAL POR DEFECTO
        $alumno = new Valumnos($conexion, $arrAlumnoCompleto['alumno']['codigo']);
        $resp = $alumno->getRazonSocialDefault();
        $codigorazon = isset($resp[0]['cod_razon_social']) ? $resp[0]['cod_razon_social'] : -1;


        //    recupero de la filial la condicion fiscal por defecto.
        $filial = new Vfiliales($conexion, $this->codigofilial);
        $condicion = $filial->getCondicionFiscalDefault();


        //TRAER CONDICION_SOCIAL POR DEFAULT POR PAIS
        if ($arrAlumnoCompleto['alumno']['codigo'] == -1) {

            $razon[] = array(
                'tipo_documentos' => $arrAlumnoCompleto['alumno']['tipo'],
                'documento' => $arrAlumnoCompleto['alumno']['documento'],
                'razon_social' => formatearNombreApellido($arrAlumnoCompleto['alumno']['nombre'], $arrAlumnoCompleto['alumno']['apellido']),
                'codigo' => $codigorazon,
                'baja' => '0',
                'condicion' => $condicion[0]['codigo'],
                'default' => 1,
                'default_facturacion' => 1,
                'direccion_calle' => $arrAlumnoCompleto['alumno']['calle'],
                'direccion_numero' => $arrAlumnoCompleto['alumno']['calle_numero'],
                'direccion_complemento' => $arrAlumnoCompleto['alumno']['calle_complemento'],
                'cod_localidad' => $arrAlumnoCompleto['alumno']['id_localidad'],
                'email' => $arrAlumnoCompleto['alumno']['email']
            );

            $this->guardarRazonesSociales($razon, $conexion, $codigonuevoalumno);
        } else {

            $razon_social = formatearNombreApellido($arrAlumnoCompleto['alumno']['nombre'], $arrAlumnoCompleto['alumno']['apellido']);
            $cod_razon = $codigorazon;

            //echo $cod_razon .' '.$razon_social;

            $razones = new Vrazones_sociales($conexion, $cod_razon);

            $razones->documento = $arrAlumnoCompleto['alumno']['documento'];
            $razones->tipo_documentos = $arrAlumnoCompleto['alumno']['tipo'];
            $razones->guardarRazones_sociales();

            $alumno->updateRazonSocial($cod_razon, $razon_social);
        }
        //print_r($arrAlumnoCompleto["responsables"]);
        if (count($arrAlumnoCompleto["responsables"]) != 0) {
            $this->guardarResponsables($arrAlumnoCompleto["responsables"], $conexion, $RAlumno["custom"]["cod_alumno"]);
        }



        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {

            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /**
     * guarda alumno general
     * @access public
     * @return repuesta Guardar
     */
    public function guardarAlunoGeneral($arrAlumnoCompleto) {

        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $this->load->helper('alumnos');
        $RAlumno = $this->guardarAlumno($arrAlumnoCompleto, $conexion);

        $codigonuevoalumno = $RAlumno["custom"]["cod_alumno"];
        $objalumno = new Valumnos($conexion, $codigonuevoalumno);
        $rDefault = $objalumno->getRazonSocialDefault();
        $codigorazon = isset($rDefault[0]['cod_razon_social']) ? $rDefault[0]['cod_razon_social'] : -1;
        $filial = new Vfiliales($conexion, $this->codigofilial);
        $condicion = $filial->getCondicionFiscalDefault();
        $objRazonDef = new Vrazones_sociales($conexion, $codigorazon);
        if ($objRazonDef->getCodigo() == -1){
            $razon = array(
                'tipo_documentos' => $arrAlumnoCompleto['alumno']['tipo'],
                'documento' => $arrAlumnoCompleto['alumno']['documento'],
                'razon_social' => formatearNombreApellido($arrAlumnoCompleto['alumno']['nombre'], $arrAlumnoCompleto['alumno']['apellido']),
                'baja' => '0',
                'condicion' => $condicion[0]['codigo'],
                'default' => 1,
                'default_facturacion' => 1,
                'direccion_calle' => $arrAlumnoCompleto['alumno']['calle'],
                'direccion_numero' => $arrAlumnoCompleto['alumno']['calle_numero'],
                'direccion_complemento' => $arrAlumnoCompleto['alumno']['calle_complemento'],
                'cod_localidad' => $arrAlumnoCompleto['alumno']['id_localidad'],
                'email' => $arrAlumnoCompleto['alumno']['email'],
                'fecha_alta' => date("Y-m-d H:i:s"),
                'inicio_actividades' => $arrAlumnoCompleto['alumno']['fechanaci'],
                'usuario_creador' => $arrAlumnoCompleto['alumno']['id_usuario_creador'],
                'codigo_postal' => $arrAlumnoCompleto['alumno']['codpost'],
            );
            $arrRazonesTemp = $objalumno->getRazonesAlumno();
            $arrRazones = array();
            if(!$arrRazonesTemp['error'])
            {
                foreach ($arrRazonesTemp['razon_alumno'] as $razonTemp){
                    $arrRazones[] = $razonTemp['codigo'];
                }
            }

            $objalumno->desetearRazones();
            $objRazonDef->setRazones_sociales($razon);
            $objRazonDef->guardarRazones_sociales();
            if (count($arrRazones) > 0){
                $objRazonDef->actualizar_facturacion($arrRazones);
            }
            $objalumno->setRazonSocial(false, $objRazonDef->getCodigo(), 1, 1);
        } else {
            $arrRazonesTemp = $objalumno->getRazonesAlumno();
            $arrRazones = array();

            if(!$arrRazonesTemp['error'])
            {
                foreach ($arrRazonesTemp['razon_alumno'] as $razonTemp){
                    $arrRazones[] = $razonTemp['codigo'];
                }
            }
            $objalumno->desetearRazones();
            $objRazonDef->razon_social = formatearNombreApellido($arrAlumnoCompleto['alumno']['nombre'], $arrAlumnoCompleto['alumno']['apellido']);
            $objRazonDef->documento = $arrAlumnoCompleto['alumno']['documento'];
            $objRazonDef->tipo_documentos = $arrAlumnoCompleto['alumno']['tipo'];
            $objRazonDef->guardarRazones_sociales();
            $objalumno->setRazonSocial(false, $objRazonDef->getCodigo(), 1, 1);

        }
        $arrRazonesAsignar = $arrAlumnoCompleto["razonsocial"];

        if (count($arrRazonesAsignar) > 0) {
            foreach ($arrRazonesAsignar as $row) {
                if ($row['codigo'] != $codigorazon) {
                    $default = $row['default'] != 1 ? 0 : 1;
                    $objalumno->setRazonSocial(true, $row['codigo'], $default, $row['default_facturacion']);
                    if ($row['default_facturacion'] == 1 && count($arrRazones) > 0){
                        $myRazonSocial = new Vrazones_sociales($conexion, $row['codigo']);
                        $myRazonSocial->actualizar_facturacion($arrRazones);
                    }
                } else {
                    $objalumno->updateRazonSocial($row['codigo'], 1, $row['default_facturacion']);
                    if ($row['default_facturacion'] == 1 && count($arrRazones) > 0){
                        $myRazonSocial = new Vrazones_sociales($conexion, $row['codigo']);
                        $myRazonSocial->actualizar_facturacion($arrRazones);
                    }
                }
            }
        }
        if ($arrAlumnoCompleto['responsable_relacion'] != ''){
            $this->asignarResponsables($arrAlumnoCompleto["responsables"], $conexion, $RAlumno["custom"]["cod_alumno"], $arrAlumnoCompleto['responsable_relacion']);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /**
     * cambia el estado de un alumno.
     * @access public
     * @return repuesta Guardar el cambio de estado.
     */
    public function cambiarEstado($codigo_alumno, $cod_usuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $alumnos = new Valumnos($conexion, $codigo_alumno);
        $respuesta = '';

        if ($alumnos->baja == 'habilitada') {
            $respuesta = $alumnos->bajaAlumno();
            
            //cambiar todo para passiva
            $deudas = $alumnos->getCtaCte(1, array('habilitado' => 1));
            foreach ($deudas as $deuda) {
                $cc = new Vctacte($conexion, $deuda['codigo']);
                $cc->setPasiva(false, null, 'Alumno inhabilitado!');
            }
            
            $arrmatriculas = array();
            $estado = 'habilitada';
            $matriculasper = $alumnos->getMatriculasPeriodos($estado);

            foreach ($matriculasper as $row) {
                $matriculaP = new Vmatriculas_periodos($conexion, $row['cod_matricula_periodo']);
                $arrmatriculas[$matriculaP->cod_matricula][] = $matriculaP->getCodigo();
            }
            foreach ($arrmatriculas as $key => $rowmatriculas) {
                $objmatricula = new Vmatriculas($conexion, $key);
                foreach ($rowmatriculas as $codmatper) {
                    $objmatper = new Vmatriculas_periodos($conexion, $codmatper);
                    $objmatper->baja(3, null, $cod_usuario);
                }
            }
            
            // Alta campus
            $this->habilitar_usuario_campus($codigo_alumno, $this->codigofilial);


        } else {
            // Baja campus
            $this->deshabilitar_usuario_campus($codigo_alumno, $this->codigofilial);
            $respuesta = $alumnos->altaAlumno();
            
            $deudas = $alumnos->getCtaCte(1, array('habilitado' => 2));
            
            foreach ($deudas as $deuda) {
                $cc = new Vctacte($conexion, $deuda['codigo']);
                $cc->alta(null, 'Alumno habilitado!');
            }
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    /**
     * recupera alumnos habilitados
     * @access public
     * @return array $alumnos
     */
    public function getAlumnosHabilitados($buscar, $separador) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $config = array("codigo_filial" => $this->codigofilial);
        $this->load->model("Model_configuraciones", "", false, $config);
        $arrCondindiciones = array();
        $limit = array();
        $orden = array();
        $alumnos = '';
        if ($buscar != '') {
            $arrCondindiciones = array(
                "alumnos.baja" => 'habilitada',
            );
            if (strlen($buscar) == 3) {
                $limit = 15;
            } else if (strlen($buscar) == 4) {
                $limit = 20;
            } else if (strlen($buscar) > 4) {
                $limit = null;
            } else {
                $limit = 10;
            }
            $alumnos = Valumnos::getAlumnos($conexion, $arrCondindiciones, $buscar, $separador, $limit);
        } else {
            $arrCondindiciones = array(
                "alumnos.baja" => 'habilitada',
            );
            $limit = array(0, 10);
            $orden = array(array('campo' => 'alumnos.codigo', 'orden' => 'desc'));
            $alumnos = Valumnos::listarAlumnos($conexion, $arrCondindiciones, $limit, $orden);
        }
        formatearNombre($alumnos);
        return $alumnos;
    }

    /**
     * Lista todos los alumnos para datatable de cta cte.
     * @access public
     * @param Array $arrFiltros filtros que se aplican.
     * @return Array ctacte alumnos
     */
    public function listarAlumnosDatatableCtaCte($arrFiltros, $separador, $separadorDecimal) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $arrCondindiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "nombre_apellido" => $arrFiltros["sSearch"],
                "alumnos.codigo" => $arrFiltros["sSearch"],
                "saldo" => $arrFiltros["sSearch"]
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
        $datos = Valumnos::listarAlumnosDataTableCtaCte($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $arrFiltros["debe"], $separador, $separadorDecimal);
        $contar = Valumnos::listarAlumnosDataTableCtaCte($conexion, $arrCondindiciones, null, null, true, true, $separador, $separadorDecimal);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                $row['seleccionar'] = '',
                inicialesMayusculas($row["nombre_apellido"]),
                formatearImporte($row["saldo"]),
                $row["proxvenc"] == '' ? '' : formatearFecha_pais($row["proxvenc"]),
                $row['estadoctacte'] = '',
                $row['debe'] = $row["saldo"] > 0 ? 1 : 0,
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    /**
     * recupera cuenta corriente del alumno
     * @access public
     * @return array $ctacte
     */
    public function getCtaCte($cod_alumno, $condiciones = null, $debe = false, $orden = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $condiciones['cod_alumno'] = $cod_alumno;
        $alumnosCta = Vctacte::getCtaCte($conexion, $debe, $condiciones, null, null, $orden);
        formatearCtaCte($conexion, $alumnosCta);
        $CtaCteorder = Vctacte::ordenarCtaCte($alumnosCta);
        return $CtaCteorder;
    }

    public function getCtaCteSinFacturar($codalumno, $condiciones, $orden, $separador, $soloCobradas = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumno = new Valumnos($conexion, $codalumno);
        $ctacte = $alumno->getCtaCteSinFacturar($condiciones, $orden, $separador, $soloCobradas);
        $this->load->helper('cuentacorriente');
        formatearCtaCte($conexion, $ctacte);
        $ctaCteOrder = Vctacte::ordenarCtaCte($ctacte);
        return $ctaCteOrder;
    }

    public function getCtaCteFacturaCobro($codalumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');
        $arrCondicion = array(
            'habilitado >' => '0',
            'habilitado <' => '3'
        );
        $alumno = new Valumnos($conexion, $codalumno);
        $ctaCte = $alumno->getCtaCteFacturarCobrar($arrCondicion);
        $i = 0;
        for ($i; $i < count($ctaCte); $i++) {
            $ctaCte[$i]['saldofaccob'] = $ctaCte[$i]['saldofacturar'] <= $ctaCte[$i]['saldocobrar'] ? $ctaCte[$i]['saldofacturar'] : $ctaCte[$i]['saldocobrar'];
        }
        formatearCtaCte($conexion, $ctaCte);
        $ctaCteOrder = Vctacte::ordenarCtaCte($ctaCte);
        return $ctaCteOrder;
    }

    public function getAlumnos($buscar, $separador) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $arrCondindiciones = array();
        $limit = array();
        if (strlen($buscar) == 3) {
            $limit = 15;
        } else if (strlen($buscar) == 4) {
            $limit = 20;
        } else if (strlen($buscar) > 4) {
            $limit = null;
        } else {
            $limit = 10;
        }
        $alumnos = Valumnos::getAlumnos($conexion, $arrCondindiciones, $buscar, $separador, $limit);
        foreach ($alumnos as $key => $alumno) {
            $alumnos[$key]['nombre'] = inicialesMayusculas($alumno['nombre']);
            $alumnos[$key]['apellido'] = inicialesMayusculas($alumno['apellido']);
        }
        formatearNombre($alumnos);
        return $alumnos;
    }

    public function getRazonSocialAlumno($cod_alumno, $paisFilial) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumnos = new Valumnos($conexion, $cod_alumno);
        if($paisFilial == '2')
        {
            $arrCondiciones = array(
                'baja' => 0
            );
            $arrCondicionesIn = array(
                'tipo_documentos' => array('21', '6')
            );
        }
        else
        {
            $arrCondiciones = array(
                'baja' => 0
            );
            $arrCondicionesIn = '';
        }
        return $alumnos->getRazonesAlumno($arrCondiciones, $arrCondicionesIn);
    }

//ver si se elimina
    public function getCtaCteImputar($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('alumnos');
        $alumno = new Valumnos($conexion, $cod_alumno);
        $ctaCteAlumno = $alumno->getCtaCteImputar();
        formatearCtaCte($conexion, $ctaCteAlumno);
        $CtaCteorder = Vctacte::ordenarCtaCte($ctaCteAlumno);
        return $CtaCteorder;
    }

    public function getReporteAlumnos($idFilial, $codigoDesde = null) {
        $conexion = $this->load->database($idFilial, true);
        if ($codigoDesde == null)
            $codigoDesde = 0;
        return Valumnos::listarAlumnos($conexion, array("codigo >= " => "$codigoDesde"));
    }

    public function getListadoCentroReportes($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null, $fechaDesde = null, $fechaHasta = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Valumnos::getListadoCentroReportes($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta);
        $registros = Valumnos::getListadoCentroReportes($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    public function getExamenAlumno($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $examenesAlumno = $objAlumno->getExamenesAlumnos();
        foreach ($examenesAlumno as $key => $examenAlumno) {
            $myExamen = new Vexamenes($conexion, $examenAlumno['codigo']);
            $myMateria = new Vmaterias($conexion, $myExamen->materia);
            $myInscripcion = new Vexamenes_estado_academico($conexion, $examenAlumno['codInscripcion']);
            $nombre = "nombre_" . get_idioma();
            $examenesAlumno[$key]['notas'] = $myInscripcion->getNotas();
            $examenesAlumno[$key]['tipoExamen'] = lang($myExamen->tipoexamen);
            $examenesAlumno[$key]['materia'] = $myMateria->$nombre;
        }
        return $examenesAlumno;
    }

    public function getResumenCuenta($idFilial, $codAlumno) {
        $conexion = $this->load->database($idFilial, true);
        $this->load->helper('cuentacorriente');
        $myAlumno = new Valumnos($conexion, $codAlumno);
        $myDocumentoTipo = new Vdocumentos_tipos($conexion, $myAlumno->tipo);
        $myLocalidad = new Vlocalidades($conexion, $myAlumno->id_localidad);
        $arrResp['codigo'] = $myAlumno->getCodigo();
        $arrResp['nombre'] = $myAlumno->nombre;
        $arrResp['apellido'] = $myAlumno->apellido;
        $arrResp['direccion_calle'] = $myAlumno->calle;
        $arrResp['direccion_numero'] = $myAlumno->calle_numero;
        $arrResp['direccion_complemento'] = $myAlumno->calle_complemento;
        $arrResp['documento_tipo'] = $myDocumentoTipo->nombre;
        $arrResp['documento_numero'] = $myAlumno->documento;
        $arrResp['localidad'] = $myLocalidad->nombre;
        //mmori ticket 04333
        $arrResp["estado"] = "inactivo";
        $arrMatriculas = $myAlumno->getMatriculas();
        //return $arrMatriculas;

        foreach ($arrMatriculas as $matricula) { // tikect 04333 mmori
            $codMatricula = $matricula['codigo'];
            $arrResp['matriculas'][$codMatricula]['matricula'] = $matricula;
            //mmori ticket 04333 inicio
            if($matricula["estado"] == "habilitada") $arrResp["estado"] = "activo";
            if(isset($matricula["fecha_hora"]))
            {
                $arrResp['matriculas'][$codMatricula]['matricula']["fecha_hora"] = date("Y/m/d", strtotime($matricula["fecha_hora"]));
            }
            //mmori ticket 04333 fin
            $arrCtacte = Vctacte::getReporteCobros($conexion, null, null, false, null, null, null, null, null, null, false, $codMatricula);
            formatearCtaCte($conexion, $arrCtacte);
            $arrResp['matriculas'][$codMatricula]['ctacte'] = $arrCtacte;
        }
        return $arrResp;
    }

    public function getConceptosCtaCteDebe($codalumno, $refinanciar = true, $arrmatriculas = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumno = new Valumnos($conexion, $codalumno);
        $conceptos = array();
        $conceptos = $alumno->getConceptosCtaCteDebe($refinanciar, $arrmatriculas);
        $objconceptos = new Vconceptos($conexion);
        $conceptosacademicos = $objconceptos->getConceptosAcademicos();
        for ($i = 0; $i < count($conceptos); $i++) {
            $esacademico = FALSE;
            foreach ($conceptosacademicos as $rowacademico) {
                if ($rowacademico['codigo'] == $conceptos[$i]['codigo']) {
                    $esacademico = TRUE;
                }
            }
            if ($esacademico) {
                $matricula = new Vmatriculas($conexion, $conceptos[$i]['concepto']);
                $objPlanAcademico = new Vplanes_academicos($conexion, $matricula->cod_plan_academico);
                $objcurso = new Vcursos($conexion, $objPlanAcademico->cod_curso);
                $nombre = 'nombre_' . get_idioma();
                $conceptos[$i]['nombre'] = lang($conceptos[$i]['key']) . ' (' . $objcurso->$nombre;
                $arrperiodos = $matricula->getPeriodosMatricula();
                $arrperiodosplan = $objPlanAcademico->getPeriodos();
                if (count($arrperiodosplan) > count($arrperiodos)) {
                    foreach ($arrperiodos as $rowperiodo) {
                        $nbreperiodo = Vtipos_periodos::getNombre($conexion, $rowperiodo['cod_tipo_periodo']);
                        $conceptos[$i]['nombre'].= ' ' . lang($nbreperiodo);
                    }
                }
                $conceptos[$i]['nombre'].= ')';
            } else {
                $conceptos[$i]['nombre'] = $conceptos[$i]['key'];
            }
        }
        return $conceptos;
    }

    public function getMatriculasPeriodosAlumno($codalumno, $codplan, $agruparMatricula = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumno = new Valumnos($conexion, $codalumno);
        $matriculasPeriodos = $alumno->getMatriculasPeriodosPlanAcademico($codplan, null, null, $agruparMatricula, true);
        for ($i = 0; $i < count($matriculasPeriodos); $i++) {
            $matriculasPeriodos[$i]['nombre'] = lang($matriculasPeriodos[$i]['nombre']);
            $matriculasPeriodos[$i]['fecha_emision'] = formatearFecha_pais($matriculasPeriodos[$i]['fecha_emision']);
            $matriculasPeriodos[$i]['estado'] = $matriculasPeriodos[$i]['estado'];
            $matriculasPeriodos[$i]['cod_matricula_periodo'] = $matriculasPeriodos[$i]['cod_matricula_periodo'];
            $matriculasPeriodos[$i]['modalidad'] = lang($matriculasPeriodos[$i]['modalidad']);
            $matriculasPeriodos[$i]['cod_matricula'] = $matriculasPeriodos[$i]['cod_matricula'];
            if (!$agruparMatricula) {
                $plan = new Vplanes_academicos($conexion, $codplan);
                $arrModalidades = $plan->getPeriodosModalidadesFilial($this->codigofilial, $matriculasPeriodos[$i]['cod_tipo_periodo']);
                $modifica = count($arrModalidades) > 1 && $matriculasPeriodos[$i]['estado'] == Vmatriculas_periodos::getEstadoHabilitada() ? '1' : '0';
                $matriculasPeriodos[$i]['modifica_modalidad'] = $modifica;
            }
        }
        return $matriculasPeriodos;
    }

    public function getDetalleMateriasPlan($cod_alumno, $cod_plan_academico, $cod_periodo = null, $detalle = false) {
        $this->load->helper("comisiones");
        $conexion = $this->load->database($this->codigofilial, true);
        $alumno = new Valumnos($conexion, $cod_alumno);
        if ($cod_periodo == null) {
            $materiasInscribir = $alumno->getEstadoAcademico($cod_plan_academico);
        } else {
            $materiasInscribir = $alumno->getEstadoAcademico($cod_plan_academico, null, $cod_periodo);
        }
        $separadordecimal = Vconfiguracion::getValorConfiguracion($conexion, null, 'SeparadorDecimal');
        $Periodos = array();
        $a = 0;
        $periodo = '';
        for ($i = 0; $i < count($materiasInscribir); $i++) {
            $nombreperiodo = Vtipos_periodos::getNombre($conexion, $materiasInscribir[$i]['cod_tipo_periodo']);
            $a = $nombreperiodo != $periodo ? 0 : $a + 1;
            $periodo = $nombreperiodo;
            $asistencia = null;
            if ($materiasInscribir[$i]['porcasistencia'] != null) {
                $numero = number_format($materiasInscribir[$i]['porcasistencia'], 2, $separadordecimal, '');
                $separados = explode($separadordecimal, $numero);
                $asistencia = $separados[1] == 0 ? $separados[0] : $numero;
            }
            $Periodos[$periodo]['materias'][$a]['codestadoacademico'] = $materiasInscribir[$i]['codigo'];
            $Periodos[$periodo]['materias'][$a]['codmatricula'] = $materiasInscribir[$i]['cod_matricula_periodo'];
            $Periodos[$periodo]['materias'][$a]['codmateria'] = $materiasInscribir[$i]['codmateria'];
            $Periodos[$periodo]['materias'][$a]['fecha'] = $materiasInscribir[$i]['fecha'];
            $Periodos[$periodo]['materias'][$a]['porcasistencia'] = $asistencia;
            $Periodos[$periodo]['materias'][$a]['nombre_es'] = $materiasInscribir[$i]['nombre_es'];
            $Periodos[$periodo]['materias'][$a]['nombre_in'] = $materiasInscribir[$i]['nombre_in'];
            $Periodos[$periodo]['materias'][$a]['nombre_pt'] = $materiasInscribir[$i]['nombre_pt'];
            $Periodos[$periodo]['materias'][$a]['estado'] = $materiasInscribir[$i]['estado'];
            $Periodos[$periodo]['materias'][$a]['codinscripcion'] = $materiasInscribir[$i]['inscripcion'];
        }

        $planacademico = new Vplanes_academicos($conexion, $cod_plan_academico);
        foreach ($Periodos as $nbreperiodo => $value) {
            for ($i = 0; $i < count($value['materias']); $i++) {
                $estado = $value['materias'][$i]['estado'];
                $inscripcion = new Vmatriculas_inscripciones($conexion, $value['materias'][$i]['codinscripcion']);
                $ocomision = new Vcomisiones($conexion, $inscripcion->cod_comision);
                $Periodos[$nbreperiodo]['materias'][$i]['nombreComision'] = $ocomision->nombre;
                if ($detalle) {
                    switch ($estado) {
                        case Vestadoacademico::getEstadoNoCursado():
                        case Vestadoacademico::getEstadoCursando():
                        case Vestadoacademico::getEstadoRegular():
                            if ($estado != Vestadoacademico::getEstadoNoCursado()) {
                                $Periodos[$nbreperiodo]['materias'][$i]['comision'] = $ocomision;
                                $Periodos[$nbreperiodo]['materias'][$i]['descripcion'] = $ocomision->nombre;
                            }
                            $matriculaperiodo = new Vmatriculas_periodos($conexion, $value['materias'][$i]['codmatricula']);
                            $condicion = array('nombre' => $nbreperiodo);
                            $codigoperiodo = Vtipos_periodos::listarTipos_periodos($conexion, $condicion);
                            $comisionesdestino = $planacademico->getComisiones(1, true, false, $codigoperiodo[0]['codigo'], true, $matriculaperiodo->modalidad);
                            $Periodos[$nbreperiodo]['materias'][$i]['comisiones_destino'] = array();
                            $comdest = array();
                            $m = 0;
                            for ($h = 0; $h < count($comisionesdestino); $h++) {
//                                if ($ocomision->getCodigo() == $comisionesdestino[$h]['codigo']) {
//                                    $comisionesdestino[$h] = null;
//                                }
                                if ($comisionesdestino[$h] != null) {
                                    $capacidad = Vconfiguracion::getValorConfiguracion($conexion, null, 'CapacidadComision');
                                    $nbeviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
                                    $habilitaSinCupo = Vconfiguracion::getValorConfiguracion($conexion, null, 'comisionesSinCupo');
                                    $objComision = new Vcomisiones($conexion, $comisionesdestino[$h]['codigo']);
                                    $nombre = '';
                                    $arrcupo = $objComision->getCapacidad();
                                    $arrinscriptos = $objComision->getInscriptos($Periodos[$nbreperiodo]['materias'][$i]['codmateria']);
                                    $cupo = count($arrcupo) > 0 ? $arrcupo[0]['cupo'] : '-1';
                                    $inscriptos = count($arrinscriptos) > 0 ? $arrinscriptos[0]['inscriptos'] : 0;
                                    $capacidadcomi = $cupo != '-1' ? $cupo : 0;
                                    $disponible = $capacidadcomi - $inscriptos;
                                    $nombre = $objComision->nombre;
                                    $nombre .= $nbeviejo == '1' ? ' (' . $objComision->descripcion . ')' : '';
                                    if ($capacidad == '1') {
                                        $nombre.=' / ';
                                        $nombre.=$cupo != '-1' ? lang('cupo') . ': ' . $disponible : lang('sin_horario');
                                    }
                                    $comdest[$m] = $comisionesdestino[$h];
                                    $comdest[$m]['nombre'] = $nombre;
                                    $comdest[$m]['habilita'] = $habilitaSinCupo == '1' || $disponible > 0 ? 1 : 0;
                                    $m++;
                                }
                            }
                            $Periodos[$nbreperiodo]['materias'][$i]['comisiones_destino'] = $comdest;
                            if ($estado == Vestadoacademico::getEstadoRegular()) {
                                $estadoaca = new Vestadoacademico($conexion, $value['materias'][$i]['codestadoacademico']);
                                $examenes = $estadoaca->getExamenesFinales();
                                $condiciones = array('cod_estado_academico' => $estadoaca->getCodigo(),
                                    'estado' => $estado);
                                $orden = array(array('campo' => 'fecha_hora', 'orden' => 'desc'));
                                $esthistorico = Vacademico_estado_historico::listarAcademico_estado_historico($conexion, $condiciones, null, $orden);
                                $fecha = count($esthistorico) > 0 ? formatearFecha_pais($esthistorico[0]['fecha_hora'], '', $this->codigofilial) : '';
                                $descripcion = lang('estado_academico_regularizada') . ' ' . $fecha;
                                if (count($examenes) > 0) {
                                    $descripcion.=' (' . count($examenes) . ' ' . lang('veces_rendida') . ')';
                                }
                                $Periodos[$nbreperiodo]['materias'][$i]['descripcion'] = $descripcion;
                            }
                            break;

                        case Vestadoacademico::getEstadoAprobado():
                            $arrExamen = array();
                            $estadoaca = new Vestadoacademico($conexion, $value['materias'][$i]['codestadoacademico']);
                            $arrExamen = $estadoaca->getExamenAprobo();
                            if (count($arrExamen) > 0) {
                                $Periodos[$nbreperiodo]['materias'][$i]['descripcion'] = formatearFecha_pais($arrExamen[0]['fecha']) . '. ' . lang('estado_academico_nota') . ' ' . $arrExamen[0]['nota'];
                            } else {
                                $Periodos[$nbreperiodo]['materias'][$i]['descripcion'] = '';
                            }
                            break;

                        case Vestadoacademico::getEstadoHomologado():
                        case Vestadoacademico::getEstadoRecursa():
                        case Vestadoacademico::getEstadoLibre():
                            $Periodos[$nbreperiodo]['materias'][$i]['descripcion'] = lang($estado);
                            $condiciones = array('cod_estado_academico' => $value['materias'][$i]['codestadoacademico'],
                                'estado' => $estado);
                            $orden = array(array('campo' => 'fecha_hora', 'orden' => 'desc'));
                            $esthistorico = array();
                            $esthistorico = Vacademico_estado_historico::listarAcademico_estado_historico($conexion, $condiciones, null, $orden);
                            if (count($esthistorico) > 0) {
                                $Periodos[$nbreperiodo]['materias'][$i]['descripcion'] .= ' ' . formatearFecha_pais($esthistorico[0]['fecha_hora'], '', $this->codigofilial);
                                $Periodos[$nbreperiodo]['materias'][$i]['descripcion'].= $esthistorico[0]['comentario'] != '' ? ' (' . $esthistorico[0]['comentario'] . ')' : '';
                            }
                            break;

                        case Vestadoacademico::getEstadoRecursa():

                            break;

                        default:
                            break;
                    }
                }

                $codmatper = $value['materias'][$i]['codmatricula'];
            }

            $matper = new Vmatriculas_periodos($conexion, $codmatper);
            $Periodos[$nbreperiodo]['estado_mat_per'] = $matper->estado;
            $nombre = $planacademico->getNombrePeriodoModalidadFilial($matper->cod_tipo_periodo, $matper->modalidad, $this->codigofilial);
            $Periodos[$nbreperiodo]['nombre_periodo'] = $nombre;
        }
        return $Periodos;
    }

    /**
     * recupera cuenta corriente del alumno para el frm
     * @access public
     * @return array $ctacte
     */
    public function getCtaCteFrm($cod_alumno, $filial = null, $idioma = null) {
    // siwakawa - agrego formato descripcion para poder ordenar correctamente cuotas de ctacte
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $orden[] = array(
            'campo' => 'fechavenc',
            //Ticket 4614 -mmori- modifico orden asc por desc
            'orden' => 'desc'
        );
        $orden[] = array(
            'campo' => 'cod_concepto',
            'orden' => 'desc'
        );
        $orden[] = array(
            'campo' => 'nrocuota',
            'orden' => 'asc'
        );
        $condiciones = array('cod_alumno' => $cod_alumno,
            'habilitado <>' => 3);

        $alumnosCta = Vctacte::getCtaCte($conexion, false, $condiciones, null, null, $orden);
        foreach ($alumnosCta as $key => $row) {
            if ($row['saldo'] > 0) {
                $alumnosCta[$key]['filtro'] = 'consaldo';
            } else {
                $alumnosCta[$key]['filtro'] = 'sinsaldo';
            }
            if ($row['habilitado'] === '1' || $row['habilitado'] === '2') {
                $alumnosCta[$key]['filtro2'] = 'habilitada';
            } else {
                $alumnosCta[$key]['filtro2'] = 'inhabilitada';
            }
        }
        formatearCtaCte($conexion, $alumnosCta, '', $filial, $idioma);
        $CtaCteorder = Vctacte::ordenarCtaCte($alumnosCta);
        foreach ($CtaCteorder as $key => $row) {
            $objCtacte = new Vctacte($conexion, $row['codigo']);
            $arrHabilitar = $objCtacte->getHabilitarFacturasCtaCte();
            $condicion = array('id_ctacte' => $row['codigo'], 'baja' => 0);
            $comentarios = Vctacte_comentarios::listarCtacte_comentarios($conexion, $condicion);
            $CtaCteorder[$key]['habilitar'] = $arrHabilitar[0]['habilitar'] == 0 ? 0 : 1;
            $CtaCteorder[$key]['tinecomentarios'] = count($comentarios) > 0 ? 1 : 0;
            $arrdto = $objCtacte->getMatriculacionesCtacteDto();
            $cuotapura = count($arrdto) > 0 && $arrdto[0]['estado'] == 'condicionado' && $arrdto[0]['descuento'] > 0 && $arrdto[0]['descuento'] < 100 ? round($objCtacte->importe / (1 - $arrdto[0]['descuento'] / 100), 2) : -1;
            $dtohasta = count($arrdto) > 0 && $arrdto[0]['estado'] == 'condicionado' && $arrdto[0]['descuento'] > 0 ? formatearFecha_pais(date('Y-m-d', strtotime($arrdto[0]['dias_vencido'] . " day", strtotime($objCtacte->fechavenc)))) : -1;
            $perdiodto = count($arrdto) > 0 && $arrdto[0]['estado'] == 'condicionado_perdido' && $arrdto[0]['descuento'] > 0 ? true : false;
            $CtaCteorder[$key]['cuota_pura'] = $cuotapura;
            $CtaCteorder[$key]['dto_hasta'] = $dtohasta;
            $CtaCteorder[$key]['dto_perdido'] = $perdiodto;
            $estadoRecuperar = array(
                Vmatriculaciones_ctacte_descuento::getEstadoCondicionado(),
                Vmatriculaciones_ctacte_descuento::getEstadoNoCondicionado()
            );
            $arrDescuentos = $objCtacte->getMatriculacionesCtacteDto(null, $estadoRecuperar);
            $CtaCteorder[$key]['tiene_descuentos'] = count($arrDescuentos) > 0;
        }
        return $CtaCteorder;
    }

    public function getNombreAlumno($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $nombreAlumno = formatearNombreApellido($objAlumno->nombre, $objAlumno->apellido);
        $nombreApellido = inicialesMayusculas($nombreAlumno);
        return $nombreApellido;
    }

    public function getMatriculasPeriodosInhabilitar($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $estado = 'habilitada';
        $matriculas = $objAlumno->getMatriculasPeriodos($estado);
        return $matriculas;
    }

    public function ver_facturas_alumno($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        $arrFactAlumnos = $myAlumno->ver_facturas_alumno();
        foreach ($arrFactAlumnos as $key => $valor) {
            $arrFactAlumnos[$key]['total'] = formatearImporte($valor['total']);
            $arrFactAlumnos[$key]['razon_social'] = inicialesMayusculas($valor['razon_social']);
        }
        return $arrFactAlumnos;
    }

    public function getMatriculasPeriodos($cod_alumno, $estado = null, $sinestado = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $matriculas = $objAlumno->getMatriculasPeriodos($estado, $sinestado);
        $arrmatper = array();
        foreach ($matriculas as $key => $rowmatriculas) {
            $arrmatper[$key]['codigo'] = $rowmatriculas['cod_matricula_periodo'];
            $objplan = new Vplanes_academicos($conexion, $rowmatriculas['cod_plan_academico']);
            $periodos = $objplan->getPeriodos();
            $curso = $objplan->getCurso();
            $idioma = 'nombre_' . get_idioma();
            $arrmatper[$key]['nombre'] = $curso->$idioma;
            if (count($periodos) > 1) {
                $nombrePeriodo = lang(Vtipos_periodos::getNombre($conexion, $rowmatriculas['cod_tipo_periodo']));
                $arrmatper[$key]['nombre'].= ' (' . $nombrePeriodo . ')';
            }
        }
        return $arrmatper;
    }

    public function getCtaCteCambioVenc($cod_alumno, $cod_concepto, $concepto) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $arrCtaCte = $objAlumno->getCtaCteSinMora(true, $cod_concepto, $concepto);
        formatearCtaCte($conexion, $arrCtaCte);
        $CtaCteorder = Vctacte::ordenarCtaCte($arrCtaCte);
        return $CtaCteorder;
    }

    public function getAlumnosMayorQue($cod_alumno) {
        $this->load->helper('alumnos');
        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array(
            "alumnos.codigo >" => $cod_alumno,
        );
        $alumnos = Valumnos::listarAlumnos($conexion, $arrCondindiciones);
        $alumnosRetorno = array();
        foreach ($alumnos as $alumno) {
            $alumnosRetorno[] = array('id' => $alumno["codigo"],
                'nombre' => formatearNombreApellido($alumno["nombre"], $alumno["apellido"])
            );
        }
        return $alumnosRetorno;
    }

    /**
     * recupera todas las razones sociales que pueden asignarse a un alumno
     * @access public
     * @param  int $codigo_alumno codigo de alumno que quiero recuperar razones sociales.
     * @return array razones sociales
     */
    public function getRazonesSocialesAsignar($codigo_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrRazones = array();
        if ($codigo_alumno == '-1') {
            $arrRazones = Vrazones_sociales::getRazonesSocialesNoDefault($conexion);
        } else {
            $arrRazones = Vrazones_sociales::getRazonesSocialesNoDefault($conexion, $codigo_alumno);
        }
        return $arrRazones;
    }

    public function login_alumno($userName, $password) {
        $conexion = $this->load->database('alumnos', true);
        $this->load->helper('alumnos');
        $retorno = array();
        $retorno['datos'] = array();
        if (preg_match('/^[a-f0-9]{32}$/', $password)) {
            $pass = $password;
        } else {
            $pass = md5($password);
        }
        $loginfilial = Valumnos::getLoginFilial($conexion, $userName, $pass);
        if (count($loginfilial) > 0) {
            $retorno['cod_login'] = $loginfilial[0]['cod_login'];
            $retorno['codigo'] = 1;
            $emails[] = $userName;
            foreach ($loginfilial as $rowfilial) {
                $conexionfilial = $this->load->database($rowfilial['cod_filial'], true);
                $nombreapellido = '';
                $objalumno = new Valumnos($conexionfilial, $rowfilial['cod_alumno']);
                $filial = new Vfiliales($conexionfilial, $rowfilial['cod_filial']);
                $apellidoprimero = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'NombreFormato');
                $separador = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'NombreSeparador');
                $nombreapellido = $apellidoprimero == '1' ? $objalumno->apellido . $separador . ' ' . $objalumno->nombre : $objalumno->nombre . $separador . '' . $objalumno->apellido;
                $emails[] = $objalumno->email;
                $retorno['datos'][] = array('codigo' => $objalumno->getCodigo(), 'nombreApellido' => $nombreapellido, 'codigoFilial' => $filial->getCodigo(), 'idiomaFilial' => $filial->idioma, 'paisFilial' => $filial->pais, 'nombreFilial' => $filial->nombre);
            }
            $emailuniq = array_unique($emails);
            $retorno['emails'] = $emailuniq;
            if (count($emailuniq) == 1) {
                $estado = 'normal';
            } else {
                $estado = 'unificar_mail';
            }
            $retorno['estado'] = $estado;
        } else {
            $existeSoloElEmail = Valumnos::getLoginFilial($conexion, $userName, null, null, null);
            if (count($existeSoloElEmail) >= 1) {
                $retorno['codigo'] = 2;
            } else {
                $retorno['codigo'] = 0;
            }
        }
        return $retorno;
    }

    public function alertaLoginCampus($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, TRUE);
        $conexion->trans_start();
        $this->lang->load(get_idioma(), get_idioma());
        $myTemplate = new Vtemplates($conexion, 82);
        $html = $myTemplate->html;
        $arrAlerta = array(
            'tipo_alerta' => 'login_campus',
            'fecha_hora' => date("Y-m-d H:i:s"),
            'mensaje' => $html,
        );
        $objAlerta = new Valertas($conexion);
        $objAlerta->setAlertas($arrAlerta);
        $objAlerta->guardarAlertas();
        $asunto = lang('ingreso_campus_virtual');
        $objAlerta->setAlertaAlumno($cod_alumno);
        $objAlerta->setAlertaConfiguracion('titulo', $asunto);
        $conexion->trans_complete();
        $resultado = $conexion->trans_status();
        return class_general::_generarRespuestaModelo($conexion, $resultado);
    }

    public function enviarLoginCampus($alerta, $objalerta, CI_DB_mysqli_driver $conexion = null, $cod_filial = null, &$comentario = null) {
        if ($conexion == null) {
            $conexion = $this->load->database($this->codigofilial, true);
        }
        $confalerta = $objalerta->getAlertaConfiguracion();
        foreach ($confalerta as $value) {
            if ($value['key'] == 'titulo') {
                $asunto = $value['valor'];
            }
        }
        $cuerpomail = $alerta['mensaje'];
        $objalumno = new Valumnos($conexion, $alerta['cod_alumno']);
        $config = array();
        $config['charset'] = 'iso-8859-1';
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $cod_filial);
        maquetados::desetiquetarAlumnos($conexion, $alerta['cod_alumno'], $cuerpomail);
        maquetados::desetiquetarIdioma($cuerpomail, true);
        $fecha = date('Y-m-d H:i:s');
        $md5 = md5($fecha . $objalumno->email);
        maquetados::desetiquetarMd5($md5, $cuerpomail);
        $link = $this->config->item('campus_url');
        maquetados::desetiquetarLinkCampus($link, $cuerpomail);
        $this->email->initialize($config);
        $this->email->from('noreply@iga-la.net', 'IGA noreply');
        $this->email->to($objalumno->email);
        $this->email->subject(utf8_decode($asunto));
        $this->email->message(utf8_decode($cuerpomail));
        $respuesta = $this->email->send();
        if (!$respuesta) {
            $comentario = $this->email->print_debugger();
        }
        $this->email->clear();
        if ($respuesta) {
            $conexion = $this->load->database("default", true);
            $objalumno->setLoginEnvio($md5, $this->codigofilial,$conexion);
        }
        return $respuesta;
    }

    public function getProximasMesasExamenesDashboarCampus($cod_filial, $cod_alumno) {
        $conexion = $this->load->database($cod_filial, true);
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        $proxMesasExamenes = $myAlumno->getProximasMesasExamenes(true);
        return $proxMesasExamenes;
    }

    public function getUltimasNotasCargadasDashboardCampus($cod_filial, $cod_alumno) {
        $conexion = $this->load->database($cod_filial, true);
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        $ultimasNotasCargadas = $myAlumno->getUltimasNotasCargadas();
        return $ultimasNotasCargadas;
    }

    public function getProximasClasesDashboardCampus($cod_filial, $cod_alumno) {
        $conexion = $this->load->database($cod_filial, true);
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        $proximasClases = $myAlumno->getProximasClases();
        return $proximasClases;
    }

    public function getProximosVencimientosDashboardCampus($cod_filial, $cod_alumno, $habilitado = null, $idioma = null) {
        $conexion = $this->load->database($cod_filial, TRUE);
        $this->load->helper('cuentacorriente');
        $arrCondiciones = array(
            "cod_alumno" => $cod_alumno,
            "fechavenc >" => date("Y-m-d")
        );
        if ($habilitado !== null){
            $arrCondiciones["habilitado"] = $habilitado;
        }
        $proximosVencCtaCte = Vctacte::getCtaCte($conexion, true, $arrCondiciones, null, null, null, false, null, null, null);
        formatearCtaCte($conexion, $proximosVencCtaCte, '', $cod_filial, $idioma);
        return $proximosVencCtaCte;
    }
// metodos campus viejo
    public function validarDatosAlumnoLoginCampus($arrDatos) {
        $conexion = $this->load->database('alumnos', TRUE);
        $retornovalid = $this->validarPrimerDatosAlumnoLoginCampus($conexion, $arrDatos['tipo_id'], $arrDatos['identificacion'], $arrDatos['fecha_nacimiento'], $arrDatos['cod_filial'], $arrDatos['email']);
        $validacion = $retornovalid['validacion'];
        $respuesta = $retornovalid['respuesta'];
        $objalumno = $retornovalid['alumno'];
        $retorno['codigo'] = 0;
        if ($validacion) {
            $intentos = $objalumno->getCantidadIntentosLogin($arrDatos['cod_filial']);
            if ($intentos < 3) {
                $conexion->order_by('matriculas_periodos.codigo', 'desc');
                $matriculasper = $objalumno->getMatriculasPeriodos(Vmatriculas_periodos::getEstadoHabilitada());
                if (count($matriculasper) > 0) {
                    $ninguno = lang('ninguno_de_los_anteriores');
                    $arrmatper = array();
                    foreach ($matriculasper as $value) {
                        $arrmatper[] = $value['cod_matricula_periodo'];
                    }
                    $conexion->where_in('cod_matricula_periodo', $arrmatper);
                    $arrestado = Vestadoacademico::listarEstadoacademico($conexion);
                    $estadosaca = array();
                    foreach ($arrestado as $rowea) {
                        $estadosaca[] = $rowea['codigo'];
                    }
                    $conexion->where_in("matriculas_horarios.cod_estado_academico", $estadosaca);
                    $conexion->order_by("horarios.dia", "desc");
                    $condiciones = array("matriculas_horarios.baja" => 0);
                    $mathorarios = Vmatriculas_horarios::getMatriculasHorariosCursados($conexion, $condiciones);
                    $asistencias = array();
                    $solofechas = array();
                    if (count($mathorarios) > 0) {
                        $cod_mat_hor = $mathorarios[0]['codigo'];
                        $ultdiacurso = $mathorarios[0]['dia'];
                        for ($i = 0; $i < 6; $i++) {
                            $aleatorio = rand(0, 20);
                            $codigo = $aleatorio == '0' ? $cod_mat_hor : rand($cod_mat_hor + 1, $cod_mat_hor + 100);
                            $fechaaleatoria = date('Y-m-d', strtotime("-" . $aleatorio . " day", strtotime($ultdiacurso)));
                            $fecharr = $codigo . '|' . formatearFecha_pais($fechaaleatoria, false, $arrDatos['cod_filial']);
                            if (!in_array($fechaaleatoria, $solofechas)) {
                                $asistencias[] = $fecharr;
                                $solofechas[] = $fechaaleatoria;
                            } else {
                                $i = $i - 1;
                            }
                        }
                        $asistencias[] = 0 . '|' . $ninguno;
                        for ($i = 0; $i < count($asistencias); $i++) {
                            $datos = explode('|', $asistencias[$i]);
                            $respuesta['asistencia'][] = array('codigo' => $datos[0], 'descripcion' => $datos[1]);
                        }
                    } else {
                        $respuesta['cursos'] = array();
                        $objmatper = new Vmatriculas_periodos($conexion, $matriculasper[0]['cod_matricula_periodo']);
                        $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);
                        $cursoalumno = $objmatricula->getCurso();
                        $idioma = get_idioma();
                        $cursos[] = $cursoalumno[0]['codigo'] . '|' . $cursoalumno[0]['nombre_' . $idioma];
                        $cursoshabilitados = Vcursos::getCursosHabilitados($conexion, null, null, false, null, null, 0);
                        $cantcursos = count($cursoshabilitados);
                        for ($i = 0; $i < 4; $i++) {
                            $aleatorio = rand(0, $cantcursos - 1);
                            $cursoaleatorio = $cursoshabilitados[$aleatorio]['codigo'] . '|' . $cursoshabilitados[$aleatorio]['nombre_' . $idioma];
                            if (!in_array($cursoaleatorio, $respuesta['cursos'])) {
                                $cursos[] = $cursoaleatorio;
                            } else {
                                $i = $i - 1;
                            }
                        }
                        shuffle($cursos);
                        for ($i = 0; $i < 4; $i++) {
                            $cursosfinal[] = $cursos[$i];
                        }
                        $cursosfinal[] = '0' . '|' . $ninguno;

                        for ($i = 0; $i < count($cursosfinal); $i++) {
                            $datos = explode('|', $cursosfinal[$i]);
                            $respuesta['cursos'][] = array('codigo' => $datos[0], 'descripcion' => $datos[1]);
                        }
                        $alumnos = Valumnos::listarAlumnos($conexion);
                        $cantalumnos = Valumnos::listarAlumnos($conexion, null, null, null, null, true);
                        $direcciones = array();
                        for ($i = 0; $i < 4; $i++) {
                            $aleatorio = rand(0, $cantalumnos - 1);
                            $calle = $alumnos[$aleatorio]['calle'];
                            $numero = $alumnos[$aleatorio]['calle_numero'];
                            $dir = $calle . '|' . $numero;
                            if ($calle != '' && $alumnos[$aleatorio]['codigo'] != $objalumno->getCodigo() && !in_array($dir, $direcciones)) {
                                $direcciones[] = $dir;
                            } else {
                                $i = $i - 1;
                            }
                        }
                        $calle = $objalumno->calle;
                        $numero = $objalumno->calle_numero;
                        $diralumno[] = $calle . '|' . $numero;
                        $diralumno[] = $ninguno . '|' . '0';
                        shuffle($diralumno);
                        if ($diralumno[0] == $ninguno . '|' . '0') {
                            shuffle($direcciones);
                            $direcciones[] = $diralumno[0];
                        } else {
                            $direcciones[] = $diralumno[0];
                            shuffle($direcciones);
                        }
                        for ($i = 0; $i < count($direcciones); $i++) {
                            $datos = explode('|', $direcciones[$i]);
                            $respuesta['direcciones'][] = array('calle' => $datos[0], 'numero' => $datos[1]);
                        }
                    }
                    $retorno['codigo'] = 1;
                } else {
                    $respuesta = lang('contactese_con_la_filial');
                }
            } else {
                $respuesta = lang('cantidad_intentos_superados_contacte_a_la_filial');
            }
        }
        $retorno['respuesta'] = $respuesta;
        return $retorno;
    }

    public function validarPrimerDatosAlumnoLoginCampus(&$conexion, $tipo_id, $identificacion, $fecha_nac, $cod_filial, $email, $code = null) {
        $this->load->helper('email');
        $retorno = array();
        $validacion = true;
        $respuesta = '';
        $cod_alumno = '';
        if ($code != null && $code != ' ') {
            $loginenvio = Valumnos::getLoginEnvio($conexion, $code);
            $cod_filial = $loginenvio[0]['cod_filial'];
            $cod_alumno = $loginenvio[0]['cod_alumno'];
            $vienemail = false;
        } else {
            $vienemail = true;
        }
        $conexion = $this->load->database($cod_filial, TRUE);

        $arrcondiciones = array('tipo' => $tipo_id, 'documento' => $identificacion, 'fechanaci' => formatearFecha_mysql($fecha_nac, $cod_filial));
        $arralumno = Valumnos::listarAlumnos($conexion, $arrcondiciones);
        $cod_alumno_login = count($arralumno) > 0 ? $arralumno[0]['codigo'] : '';
        if ($cod_alumno_login == '') {
            $validacion = false;
        } elseif ($cod_alumno != '') {
            $validacion = $cod_alumno == $cod_alumno_login ? true : false;
        }

        if ($validacion) {
            $objalumno = new Valumnos($conexion, $cod_alumno_login);
            $log = $objalumno->getLogin($cod_filial);
            if (count($log) < 1) {
                $email = !$vienemail ? $objalumno->email : $email;
                if (valid_email($email)) {
                    $emaillogins = Valumnos::getLoginFilial($conexion, $email);
                    if (count($emaillogins) > 0) {
                        $validacion = false;
                        $respuesta = lang('email_ya_registrado');
                    } else {
                        $loginaconfirmar = Valumnos::getLoginAConfirmar($conexion, null, 'no_confirmado', $objalumno->getCodigo(), $cod_filial);
                        if (count($loginaconfirmar) > 0) {
                            $validacion = false;
                            $respuesta = lang('registro_campus_pendiente');
                        }
                    }
                } else {
                    $validacion = false;
                    $respuesta = lang('email_formato_invalido');
                    $respuesta.=$vienemail ? ' ' : ' ' . lang('contactese_con_la_filial');
                }
            } else {
                $validacion = false;
                $respuesta = lang('alumno_ya_logueado');
            }
        } else {
            $validacion = false;
            $respuesta = lang('los_datos_informados_no_corresponden_a_un_alumno_de_la_filial');
        }
        $retorno['validacion'] = $validacion;
        $retorno['respuesta'] = $respuesta;
        $retorno['alumno'] = isset($objalumno) ? $objalumno : null;
        $retorno['cod_filial'] = $cod_filial;
        return $retorno;
    }

    public function registrarAlumnoCampus($arrDatos) {
        $conexion = $this->load->database('alumnos', TRUE);
        $retornovalid = $this->validarPrimerDatosAlumnoLoginCampus($conexion, $arrDatos['tipo_id'], $arrDatos['identificacion'], $arrDatos['fecha_nacimiento'], $arrDatos['cod_filial'], $arrDatos['email'], $arrDatos['code']);
        $validacion = $retornovalid['validacion'];
        $respuesta = $retornovalid['respuesta'];
        $objalumno = $retornovalid['alumno'];
        $cod_filial = $retornovalid['cod_filial'];
        $conexion->trans_start();
        $retorno['codigo'] = 0;
        if ($validacion) {
            if ($arrDatos['code'] == '') {
                $email = $arrDatos['email'];
                $validacion2 = $this->validarSegundoDatosAlumnoLoginCampus($conexion, $objalumno, $arrDatos['lista'], $arrDatos['asistencia'], $arrDatos['cursos'], $arrDatos['direcciones']);
                if ($validacion2) {
                    $fecha_hora = date('Y-m-d H:i:s');
                    $md5 = md5($objalumno->getCodigo() . $fecha_hora);
                    $registro = $objalumno->setLoginAConfirmar($md5, $email, md5($arrDatos['pass']), $cod_filial, $fecha_hora);
                    if ($registro) {
                        $retorno['codigo'] = 1;
                        $envio = $this->enviarMailConfirmarLogin($conexion, $email, $objalumno->getCodigo(), $cod_filial, $md5);
                        if ($envio) {
                            $respuesta = lang('bien_se_envio_mail_cuenta') . $email . lang('para_confirmar_su_registro');
                        }
                    }
                } else {
                    $respuesta = lang('los_datos_ingresados_no_son_correctos');
                    $objalumno->setIntentoFallido($arrDatos['cod_filial']);
                }
            } else {
                $email = $objalumno->email;
                $cod_login = Valumnos::setLogin($conexion, $email, md5($arrDatos['pass']));
                $registro = $objalumno->setLoginFilial($cod_login, $cod_filial);
                $fechahora = date('Y-m-d H:i:s');
                $md5hist = md5($cod_login . $fechahora);
                Valumnos::setLoginHistorico($conexion, $email, md5($arrDatos['pass']), $cod_login, $fechahora, $md5hist);
                $this->enviarMailLoginConfirmado($conexion, $email, $objalumno->getCodigo(), $cod_filial);
                if ($registro) {
                    $retorno['codigo'] = 1;
                    $retorno['datos'] = array('userName' => $email, 'password' => $arrDatos['pass']);
                }
            }
        }

        $conexion->trans_complete();
        $conexion->trans_status();
        $retorno['respuesta'] = $respuesta;
        return $retorno;
    }

    public function validarSegundoDatosAlumnoLoginCampus(&$conexion, $objalumno, $lista, $asistencias = null, $cursos = null, $direcciones = null) {
        $validacion = false;
        $conexion->order_by('matriculas_periodos.codigo', 'desc');
        $matriculasper = $objalumno->getMatriculasPeriodos(Vmatriculas_periodos::getEstadoHabilitada());
        $existematriculas = count($matriculasper) > 0 ? true : false;
        if ($existematriculas) {
            if ($asistencias != null) {
                $arrmatper = array();
                foreach ($matriculasper as $value) {
                    $arrmatper[] = $value['cod_matricula_periodo'];
                }
                $conexion->where_in('cod_matricula_periodo', $arrmatper);
                $arrestado = Vestadoacademico::listarEstadoacademico($conexion);
                $estadosaca = array();
                foreach ($arrestado as $rowea) {
                    $estadosaca[] = $rowea['codigo'];
                }
                $conexion->where_in("matriculas_horarios.cod_estado_academico", $estadosaca);
                $conexion->order_by("horarios.dia", "desc");
                $condiciones = array("matriculas_horarios.baja" => 0);
                $mathorarios = Vmatriculas_horarios::getMatriculasHorariosCursados($conexion, $condiciones);
                $cod_mat_hor = $mathorarios[0]['codigo'];
                $estainarr = false;
                foreach ($lista['asistencia'] as $value) {
                    if ($value['codigo'] == $cod_mat_hor) {
                        $estainarr = true;
                    }
                }
                if ($estainarr) {
                    $validacion = ($asistencias != $cod_mat_hor) ? false : true;
                } else {
                    $validacion = ($asistencias != 0) ? false : true;
                }
            } else {
                $objmatper = new Vmatriculas_periodos($conexion, $matriculasper[0]['cod_matricula_periodo']);
                $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);
                $cursoalumno = $objmatricula->getCurso();
                $cod_curso = $cursoalumno[0]['codigo'];
                $estainarr = false;
                foreach ($lista['cursos'] as $value) {
                    if ($value['codigo'] === $cod_curso) {
                        $estainarr = true;
                    }
                }
                if ($estainarr) {
                    $validacion = ($cursos != $cod_curso) ? false : true;
                } else {
                    $validacion = ($cursos != 0) ? false : true;
                }
                if ($validacion) {
                    $direccion = $objalumno->calle . '#' . $objalumno->calle_numero;
                    $estainarr = false;
                    foreach ($lista['direcciones'] as $value) {
                        if ($value['calle'] == $objalumno->calle && $value['numero'] == $objalumno->calle_numero) {
                            $estainarr = true;
                        }
                    }
                    if ($estainarr) {
                        $validacion = ($direccion != $direcciones) ? false : true;
                    } else {
                        $validacion = ($direcciones != lang('ninguno_de_los_anteriores') . '#' . '0' ) ? false : true;
                    }
                }
            }
        }
        return $validacion;
    }

    public function enviarMailConfirmarLogin($conexion, $email, $cod_alumno, $cod_filial, $md5) {
        $this->load->library('email');
        $myTemplate = new Vtemplates($conexion, 85);
        $cuerpomail = $myTemplate->html;
        $asunto = lang('confirmar_registro_campus');
        $config = array();
        $config['charset'] = 'iso-8859-1';
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $cod_filial);
        maquetados::desetiquetarAlumnos($conexion, $cod_alumno, $cuerpomail);
        maquetados::desetiquetarIdioma($cuerpomail, true);
        maquetados::desetiquetarMd5($md5, $cuerpomail);
        $link = $this->config->item('campus_url');
        maquetados::desetiquetarLinkCampus($link, $cuerpomail);
        $this->email->initialize($config);
        $this->email->from('noreply@iga-la.net', 'IGA noreply');
        $this->email->to($email);
        $this->email->subject(utf8_decode($asunto));
        $this->email->message(utf8_decode($cuerpomail));
        $respuesta = $this->email->send();
        $this->email->clear();
        return $respuesta;
    }

    public function confirmarLoginCampus($code) {
        $conexion = $this->load->database('alumnos', TRUE);
        $conexion->trans_start();
        $retorno['codigo'] = 0;
        $loginenvio = Valumnos::getLoginAConfirmar($conexion, $code);
        if (count($loginenvio) > 0) {
            switch ($loginenvio[0]['estado']) {
                case 'no_confirmado':
                    $cod_filial = $loginenvio[0]['cod_filial'];
                    $conexion = $this->load->database($cod_filial, TRUE);
                    $filial = new Vfiliales($conexion, $cod_filial);
                    $this->lang->load($filial->idioma, $filial->idioma);
                    $objalumno = new Valumnos($conexion, $loginenvio[0]['cod_alumno']);
                    $user = $loginenvio[0]['user'];
                    $pass = $loginenvio[0]['pass'];
                    $cod_login = Valumnos::setLogin($conexion, $user, $pass);
                    $registro = $objalumno->setLoginFilial($cod_login, $cod_filial);
                    $fechahora = date('Y-m-d H:i:s');
                    $md5hist = md5($cod_login . $fechahora);
                    Valumnos::setLoginHistorico($conexion, $user, $pass, $cod_login, $fechahora, $md5hist);
                    if ($registro) {
                        $retorno['codigo'] = 1;
                        $retorno['datos'] = array('userName' => $loginenvio[0]['user'], 'password' => $loginenvio[0]['pass']);
                        $retorno['respuesta'] = lang('logueado_con_exito');
                        Valumnos::setConfirmadoLoginAConfirmar($conexion, $code);
                        $this->enviarMailLoginConfirmado($conexion, $loginenvio[0]['user'], $objalumno->getCodigo(), $loginenvio[0]['cod_filial']);
                    }
                    break;
                case 'confirmado':
                    $retorno['codigo'] = 0;
                    $retorno['respuesta'] = lang('logueo_ya_confirmado');
                    break;
                case 'caducado':
                    $retorno['codigo'] = 2;
                    $retorno['respuesta'] = lang('logueo_caducado');
                    break;
            }
        } else {
            $retorno['codigo'] = 2;
            $retorno['respuesta'] = lang('no_existe_login_confirmar');
        }

        $conexion->trans_complete();
        $resultado = $conexion->trans_status();
        if (!$resultado) {
            $retorno['codigo'] = 0;
        }
        return $retorno;
    }

    public function enviarMailLoginConfirmado($conexion, $email, $cod_alumno, $cod_filial) {
        $this->load->library('email');
        $myTemplate = new Vtemplates($conexion, 86);
        $cuerpomail = $myTemplate->html;
        $asunto = lang('registro_campus_virtual_confirmado');
        $config = array();
        $config['charset'] = 'iso-8859-1';
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $cod_filial);
        maquetados::desetiquetarAlumnos($conexion, $cod_alumno, $cuerpomail);
        maquetados::desetiquetarIdioma($cuerpomail, true);
        $link = $this->config->item('campus_url');
        maquetados::desetiquetarLinkCampus($link, $cuerpomail);
        $this->email->initialize($config);
        $this->email->from('noreply@iga-la.net', 'IGA noreply');
        $this->email->to($email);
        $this->email->subject(utf8_decode($asunto));
        $this->email->message(utf8_decode($cuerpomail));
        $respuesta = $this->email->send();
        $this->email->clear();
        return $respuesta;
    }

    public function restablecePassCampus($user) {
        $conexion = $this->load->database('alumnos', TRUE);
        $conexion->trans_start();
        $retorno = array();
        $retorno['codigo'] = 1;
        $logins = Valumnos::getLoginFilial($conexion, $user);
        if (count($logins) > 0) {
            $cod_filial = $logins[0]['cod_filial'];
            $cod_alumno = $logins[0]['cod_alumno'];
            $cod_login = $logins[0]['cod_login'];
            $conexion = $this->load->database($cod_filial, TRUE);
            $filial = new Vfiliales($conexion, $cod_filial);
            $this->lang->load($filial->idioma, $filial->idioma);
            $fecha = date('Y-m-d H:i:s');
            $md5 = md5($cod_login . $fecha);
            Valumnos::setLoginRecuperaPass($conexion, $md5, $cod_login, $fecha);
            $envio = $this->enviarMailRestablecePassCampus($conexion, $md5, $cod_alumno, $cod_filial, $user);
            if ($envio) {
                $retorno['respuesta'] = lang('bien_se_envio_mail_cuenta') . $user . lang('para_confirmar_su_registro');
            }
        } else {
            $retorno['codigo'] = 0;
            $retorno['respuesta'] = lang('email_no_registrado');
        }
        $conexion->trans_complete();
        $retorno['codigo'] = $conexion->trans_status() && $retorno['codigo'] != 0 ? 1 : 0;
        return $retorno;
    }

    public function enviarMailRestablecePassCampus($conexion, $md5, $cod_alumno, $cod_filial, $email) {
        $this->load->library('email');
        $myTemplate = new Vtemplates($conexion, 83);
        $cuerpomail = $myTemplate->html;
        $asunto = lang('restablece_pass_campus_virtual');
        $config = array();
        $config['charset'] = 'iso-8859-1';
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $cod_filial);
        maquetados::desetiquetarAlumnos($conexion, $cod_alumno, $cuerpomail);
        maquetados::desetiquetarIdioma($cuerpomail, true);
        maquetados::desetiquetarMd5($md5, $cuerpomail);
        $link = $this->config->item('campus_url');
        maquetados::desetiquetarLinkCampus($link, $cuerpomail);
        $this->email->initialize($config);
        $this->email->from('noreply@iga-la.net', 'IGA noreply');
        $this->email->to($email);
        $this->email->subject(utf8_decode($asunto));
        $this->email->message(utf8_decode($cuerpomail));
        $respuesta = $this->email->send();
        $this->email->clear();
        return $respuesta;
    }

    public function modificoPassCampus($code, $pass) {
        $conexion = $this->load->database('alumnos', TRUE);
        $retorno['codigo'] = 0;
        $conexion->trans_start();
        $retorno = array();
        $logins = Valumnos::getLoginRecuperaPass($conexion, $code);
        if (count($logins) > 0) {
            if ($logins[0]['estado'] != 'usado') {
                $cod_login = $logins[0]['cod_login'];
                $nuevopass = md5($pass);
                $login = Valumnos::getLoginFilial($conexion, null, null, $cod_login);
                if ($login[0]['pass'] != $nuevopass) {
                    $retorno['codigo'] = Valumnos::updateLogin($conexion, $cod_login, null, $nuevopass);
                    $fechahora = date('Y-m-d H:i:s');
                    $md5hist = md5($cod_login . $fechahora);
                    Valumnos::setLoginHistorico($conexion, $login[0]['user'], $nuevopass, $cod_login, $fechahora, $md5hist);
                    $anteult = Valumnos::getAnteultimoLoginHistorico($conexion, $cod_login);
                    $this->enviarMailConfirmaCambioPass($conexion, $anteult['md5'], $login[0]['cod_alumno'], $login[0]['cod_filial'], $login[0]['user']);
                    Valumnos::setUsadoRecuperaPass($conexion, $logins[0]['md5']);
                    $retorno['codigo'] = 1;
                } else {
                    $retorno['codigo'] = 0;
                    $retorno['respuesta'] = lang('pass_igual_anterior');
                }
            } else {
                $retorno['codigo'] = 2;
                $retorno['respuesta'] = lang('pedido_modificacion_pass_usado');
            }
        } else {
            $retorno['codigo'] = 2;
            $retorno['respuesta'] = lang('no_hay_registro_modificacion_pass');
        }
        $conexion->trans_complete();
        $conexion->trans_status();
        return $retorno;
    }

    public function enviarMailConfirmaCambioPass($conexion, $md5, $cod_alumno, $cod_filial, $email) {
        $conexion = $this->load->database($cod_filial, TRUE);
        $this->load->library('email');
        $myTemplate = new Vtemplates($conexion, 84);
        $cuerpomail = $myTemplate->html;
        $asunto = lang('confirmacion_cambio_pass');
        $config = array();
        $config['charset'] = 'iso-8859-1';
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $cod_filial);
        maquetados::desetiquetarAlumnos($conexion, $cod_alumno, $cuerpomail);
        maquetados::desetiquetarIdioma($cuerpomail, true);
        maquetados::desetiquetarMd5($md5, $cuerpomail);
        $link = $this->config->item('campus_url');
        maquetados::desetiquetarLinkCampus($link, $cuerpomail);
        $this->email->initialize($config);
        $this->email->from('noreply@iga-la.net', 'IGA noreply');
        $this->email->to($email);
        $this->email->subject(utf8_decode($asunto));
        $this->email->message(utf8_decode($cuerpomail));
        $respuesta = $this->email->send();
        $this->email->clear();
        return $respuesta;
    }

    public function getFilialLoginEnvioCampus($code) {
        $conexion = $this->load->database('alumnos', TRUE);
        $retorno = '';
        if ($code != '') {
            $login = Valumnos::getLoginEnvio($conexion, $code);
            if (count($login) > 0) {
                $conexion = $this->load->database($login[0]['cod_filial'], TRUE);
                $condicion = array('codigo' => $login[0]['cod_filial']);
                $filiales = Vfiliales::listarFiliales($conexion, $condicion);
                $retorno = $filiales[0];
            }
        }
        return $retorno;
    }

    public function unificarEmailCampus($cod_login, $mail) {
        $conexion = $this->load->database('alumnos', TRUE);
        $conexion->trans_start();
        $cambios = array();
        $retorno['codigo'] = 1;
        $logins = Valumnos::getLoginFilial($conexion, null, null, $cod_login);
        foreach ($logins as $rowlogin) {
            if ($retorno['codigo'] != 0) {
                $conexion = $this->load->database($rowlogin['cod_filial'], true);
                $objalumno = new Valumnos($conexion, $rowlogin['cod_alumno']);
                if ($objalumno->email != $mail) {
                    $condicion = array('email' => $mail);
                    $alumnos = Valumnos::listarAlumnos($conexion, $condicion);
                    if (count($alumnos) > 0) {
                        $retorno['codigo'] = 0;
                    } else {
                        $cambios[] = array('cod_alumno' => $rowlogin['cod_alumno'], 'cod_filial' => $rowlogin['cod_filial'], 'emailviejo' => $objalumno->email, 'emailnuevo' => $mail);
                        $objalumno->email = $mail;
                        $objalumno->guardarAlumnos();
                    }
                }
            }
        }
        $user = $logins[0]['user'];
        if ($user != $mail && $retorno['codigo'] != 0) {
            $emaillogins = Valumnos::getLoginFilial($conexion, $mail);
            if (count($emaillogins) > 0) {
                $retorno['codigo'] = 0;
            } else {
                $cod_login = $logins[0]['codigo'];
                $pass = $logins[0]['pass'];
                Valumnos::updateLogin($conexion, $cod_login, $mail, $pass);
                $fechahora = date('Y-m-d H:i:s');
                $md5hist = md5($cod_login . $fechahora);
                Valumnos::setLoginHistorico($conexion, $mail, $pass, $cod_login, $fechahora, $md5hist);
                $retorno['codigo'] = 2;
            }
        }

        if ($retorno['codigo'] == 0) {
            foreach ($cambios as $rowfilial) {
                $conexionfilial = $this->load->database($rowfilial['cod_filial'], true);
                $objalumno = new Valumnos($conexionfilial, $rowfilial['cod_alumno']);
                $objalumno->email = $rowfilial['emailviejo'];
                $objalumno->guardarAlumnos();
            }
        }
        $conexion->trans_complete();
        $retorno['codigo'] = $conexion->trans_status() && $retorno['codigo'] != 0 ? $retorno['codigo'] : 0;
        return $retorno;
    }

    public function restablecePassAnteriorCampus($code) {
        $conexion = $this->load->database('alumnos', TRUE);
        $conexion->trans_start();
        $retorno['codigo'] = 0;
        $loginhist = Valumnos::getLoginHistorico($conexion, $code);
        if (count($loginhist) > 0) {
            $cod_login = $loginhist[0]['cod_login'];
            $user = $loginhist[0]['user'];
            $pass = $loginhist[0]['pass'];
            $respuesta = Valumnos::updateLogin($conexion, $cod_login, $user, $pass);
            if ($respuesta) {
                $fechahora = date('Y-m-d H:i:s');
                $md5hist = md5($cod_login . $fechahora);
                Valumnos::setLoginHistorico($conexion, $user, $pass, $cod_login, $fechahora, $md5hist);
                $retorno['codigo'] = 1;
            }
        } else {
            $retorno['codigo'] = 0;
        }
        $conexion->trans_complete();
        $retorno['codigo'] = $conexion->trans_status() && $retorno['codigo'] != 0 ? $retorno['codigo'] : 0;
        return $retorno;
    }

    public function modificoPassCampusDesdePerfil($userName, $pass, $cod_filial) {
        $conexion = $this->load->database('alumnos', TRUE);
        $retorno['codigo'] = 0;
        $conexion->trans_start();
        $retorno = array();
        $nuevopass = md5($pass);
        $login = Valumnos::getLoginFilial($conexion, $userName, null, null, $cod_filial);
        if ($login[0]['pass'] != $nuevopass) {
            $retorno['codigo'] = Valumnos::updateLogin($conexion, $login[0]['cod_login'], null, $nuevopass);
            $fechahora = date('Y-m-d H:i:s');
            $md5hist = md5($login[0]['cod_login'] . $fechahora);
            Valumnos::setLoginHistorico($conexion, $login[0]['user'], $nuevopass, $login[0]['cod_login'], $fechahora, $md5hist);
            $anteult = Valumnos::getAnteultimoLoginHistorico($conexion, $login[0]['cod_login']);
            $this->enviarMailConfirmaCambioPass($conexion, $anteult['md5'], $login[0]['cod_alumno'], $login[0]['cod_filial'], $login[0]['user']);
            $retorno['codigo'] = 1;
        } else {
            $retorno['codigo'] = 0;
            $retorno['respuesta'] = lang('pass_igual_anterior');
        }
        $conexion->trans_complete();
        $conexion->trans_status();
        return $retorno;
    }
// fin campus viejo

// Get alumnos es usado en el campus nuevo
    public function getAlumnoCampus($codigo, $filial, $separador, $apellidoPrimero) {
        $this->load->helper('formatearfecha');
        $this->load->helper('alumnos');
        $conexion = $this->load->database($filial, true);
        $alumno = new Valumnos($conexion, $codigo);
        //$login = $alumno->getLogin($filial);
        $filial = new Vfiliales($conexion, $filial);
        $alumno->fechanaci = formatearFecha_pais($alumno->fechanaci, false, $filial->getCodigo());
        $alumno->nombreFormateado = formatearNombreApellido($alumno->nombre, $alumno->apellido, $separador, $apellidoPrimero);
        //$alumno->password = $login[0]['pass'];
        $myLocalidad = new Vlocalidades($conexion, $alumno->id_localidad);
        $alumno->nombre_localidad = $myLocalidad->nombre;
        return $alumno;
    }



    public function getDetalleExamenAlumnno($cod_filial, $cod_examen, $cod_alumno, $cod_materia, $cod_inscripcion) {
        $conexion = $this->load->database($cod_filial, true);
        $condiciones = array(
            "codigo" => $cod_examen,
            "baja" => 0
        );
        $detalleExamenAlumno = '';
        $arrExamenes = Vexamenes::listarExamenes($conexion, $condiciones);
        if ($cod_inscripcion == '') {
            $myAlumno = new Valumnos($conexion, $cod_alumno);
            $detalleExamenAlumno = $myAlumno->getDetalleExamenAlumnno($cod_examen, $cod_materia);
            foreach ($detalleExamenAlumno as $key => $valor) {
                $arrProfesores = explode("/", $valor['profesor']);
                $arrSalones = explode('/', $valor['salon']);
                $detalleExamenAlumno[$key]['profesor'] = $arrProfesores;
                $detalleExamenAlumno[$key]['salon'] = $arrSalones;
            }
        }

        foreach ($arrExamenes as $key => $examen) {
            $condiciones2 = array('cod_examen' => $examen['codigo'], 'estado <>' => 'baja');
            $inscriptos = Vexamenes_estado_academico::listarExamenes_estado_academico($conexion, $condiciones2);
            $disponible = $examen['cupo'] - count($inscriptos);
            $detalleExamenAlumno[0]['cod_examen'] = $examen['codigo'];
            $detalleExamenAlumno[0]['fecha_examen'] = $examen['fecha'];
            $detalleExamenAlumno[0]['hora_inicio'] = $examen['hora'];
            $detalleExamenAlumno[0]['hora_fin'] = $examen['horafin'];
            $detalleExamenAlumno[0]['cupo_disponible'] = $disponible;
            if ($cod_inscripcion != '') {
                $myMateria = new Vmaterias($conexion, $examen['materia']);
                $detalleExamenAlumno[0]['estado_inscripcion'] = 'baja';
                $detalleExamenAlumno[0]['motivo_baja'] = '';
                $detalleExamenAlumno[0]['nombre_es'] = $myMateria->nombre_es;
            }
        }
        return $detalleExamenAlumno;
    }

    public function validarInscripcionExamen($cod_filial, $cod_examen, $cod_estado_academico) {
        $conexion = $this->load->database($cod_filial, true);
        $condiciones = array(
            "cod_estado_academico" => $cod_estado_academico,
            "cod_examen" => $cod_examen,
            "estado <>" => 'baja'
        );
        $validarInscripcionExamen = Vexamenes_estado_academico::listarExamenes_estado_academico($conexion, $condiciones);
        if (count($validarInscripcionExamen) > 0) {
            return false;
        } else {
            $condiciones2 = array('cod_examen' => $cod_examen, 'estado <>' => 'baja');
            $examen = new Vexamenes($conexion, $cod_examen);
            $inscriptos = Vexamenes_estado_academico::listarExamenes_estado_academico($conexion, $condiciones2);
            $disponible = $examen->cupo - count($inscriptos);
            if ($disponible > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getTallesPais($pais) {
        $conexion = $this->load->database($this->codigofilial, true);
        $listadoTallesPais = Vtalles::getTallesPais($conexion, $pais);
        $arrRetorno = array();
        $arrTallesPais = array();
        foreach ($listadoTallesPais as $value) {
            if ($value['propiedad'] == 'ancho') {
                $arrTallesPais[$value['id_talle']] = $value['valor'];
            }
        }

        foreach ($listadoTallesPais as $talle) {
            $arrRetorno[$talle['id_talle']]['talle'] = $talle['talle'];
            $arrRetorno[$talle['id_talle']]['propiedad'][$talle['propiedad']] = $talle['valor'];
        }
        foreach ($arrTallesPais as $j => $valor) {
            foreach ($arrRetorno as $ret => $dato) {
                if ($j == $ret) {
                    $arrTallesPais[$j] = $dato;
                }
            }
        }
        asort($arrTallesPais, SORT_DESC);
        return $arrTallesPais;
    }

    public function getCtaCteCobro($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('cuentacorriente');
        $arrCondicion = array(
            'habilitado >' => '0',
            'habilitado <' => '3'
        );
        $alumno = new Valumnos($conexion, $cod_alumno);
        $ctaCteAlumno = $alumno->getCtaCteCobrar($arrCondicion);
        formatearCtaCte($conexion, $ctaCteAlumno);
        $CtaCteorder = Vctacte::ordenarCtaCte($ctaCteAlumno);
        return $CtaCteorder;
    }

    public function ExisteCodeReg($code) {
        $conexion = $this->load->database('alumnos', TRUE);
        if ($code != '') {
            $login = Valumnos::getLoginEnvio($conexion, $code);
            if (count($login) > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getMailsAlumnoCampus($codigo, $filial) {
        $conexion = $this->load->database($filial, true);
        $retorno = array();
        $objAlumno = new Valumnos($conexion, $codigo);
        $login = $objAlumno->getLogin($filial);
        $cod_login = $login[0]['cod_login'];
        $loginfilial = Valumnos::getLoginFilial($conexion, null, null, $cod_login);
        if (count($loginfilial) > 0) {
            $emails[] = $login[0]['user'];
            foreach ($loginfilial as $rowfilial) {
                $conexionfilial = $this->load->database($rowfilial['cod_filial'], true);
                $objalumno = new Valumnos($conexionfilial, $rowfilial['cod_alumno']);
                $emails[] = $objalumno->email;
            }
            $emailuniq = array_unique($emails);
            $retorno['cod_login'] = $cod_login;
            $retorno['emails'] = $emailuniq;
            if (count($emailuniq) == 1) {
                $estado = 'normal';
            } else {
                $estado = 'unificar_mail';
            }
            $retorno['estado'] = $estado;
        } else {
            $retorno['codigo'] = 0;
        }
        return $retorno;
    }

    public function asignarResponsables($arrResponsables, $conexion = null, $cod_alumno = null, $responsable_relacion = null) {
        $transInt = false;
        if ($conexion == null) {
            $transInt = true;
            $conexion = $this->load->database($this->codigofilial, true);
            $conexion->trans_begin();
        }
        $this->load->helper('alumnos');
        $alumno = new Valumnos($conexion, $cod_alumno);
        $alumno->desetearResponsables();
        foreach ($arrResponsables as $key => $rowResponsables) {
            $ArrResponsableAlumno = array(
                "cod_alumno" => $cod_alumno,
                "cod_responsable" => $rowResponsables,
                "relacion_alumno" => $responsable_relacion[$key]
            );
            $alumno->setResponsable($ArrResponsableAlumno);
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

    public function getPlanesAcademicos($codalumno, $soloDatosPrincipales = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumno = new Valumnos($conexion, $codalumno);
        $cursos = Vcursos::getCursosHabilitados($conexion, null, null, false, null, $this->codigofilial, '0');
        $arrTemp = array();
        for ($i = 0; $i < count($cursos); $i++) {
            $cursos[$i]['nombre'] = $cursos[$i]['cantplanes'] > 1 ? $cursos[$i]['nombre_' . get_idioma()] : $cursos[$i]['nombre_' . get_idioma()];
            $planesPeriodos = $alumno->getPeriodosMatricularPlanAcademico($cursos[$i]['cod_plan_academico'], $this->codigofilial);
            $cursos[$i]['matricular'] = count($planesPeriodos) > 0 ? true : false;
            $arrTemp[] = array("nombre" => $cursos[$i]['nombre'],
                "matricular" => $cursos[$i]['matricular'],
                "codigo" => $cursos[$i]['codigo'],
                "cantplanes" => $cursos[$i]['cantplanes'],
                "planfilial" => $cursos[$i]['planfilial'],
                "cod_plan_academico" => $cursos[$i]['cod_plan_academico']);
        }
        if ($soloDatosPrincipales){
            return $arrTemp;
        } else {
            return $cursos;
        }
    }

    public function getDetallesAlertasEmailCampus($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, TRUE, null, true);
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        $arrDetalle = $myAlumno->getDetallesAlertasEmailCampus();
        foreach ($arrDetalle as $key => $detalle) {
            $arrDetalle[$key]['fecha_hora'] = formatearFecha_pais($detalle['fecha_hora'], true);
        }
        return $arrDetalle;
    }

    public function reenviar_email_campus($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_start();
        $parametros = array('cod_alumno' => $cod_alumno);
        $objtarecron = new Vtareas_crons($conexion);
        $objtarecron->guardar('alta_campus', $parametros, $this->codigofilial);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }


    public function getFacturasNC($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('filial');
        $alumno = new Valumnos($conexion, $cod_alumno);
        $facturas = $alumno->getFacturas();
        $respuesta = array();
        $i = 0;
        foreach ($facturas as $rowfactura) {
            $objfactura = new Vfacturas($conexion, $rowfactura['codigo']);
            $nc = $objfactura->getNotasCredito(0);
            $total = 0;
            foreach ($nc as $value) {
                $total = $total + $value['importe'];
            }
            if ($rowfactura['total'] > $total) {
                $imputar = $rowfactura['total'] - $total;
                $respuesta[$i] = $rowfactura;
                $respuesta[$i]['fecha'] = formatearFecha_pais($respuesta[$i]['fecha']);
                $respuesta[$i]['total'] = formatearImporte($respuesta[$i]['total'], TRUE);
                $respuesta[$i]['imputado'] = $total;
                $respuesta[$i]['imputado_format'] = formatearImporte($total, TRUE);
                $respuesta[$i]['importe_nc'] = formatearImporte($imputar, false);
                $i++;
            }
        }
        return $respuesta;
    }

    public function getMatriculas($cod_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $alumno = new Valumnos($conexion, $cod_alumno);
        $matriculas = $alumno->getMatriculas();
        foreach ($matriculas as $key => $rowmatriculas) {
            $objmatricula = new Vmatriculas($conexion, $rowmatriculas['codigo']);
            $arrperiodos = $objmatricula->getPeriodosMatricula();
            $matriculas[$key]['descripcion'] = $rowmatriculas['curso_nombre'] . ' ';
            foreach ($arrperiodos as $rowperiodos) {
                $nombrePeriodo = lang($rowperiodos['nombre']);
                $matriculas[$key]['descripcion'].='(' . $nombrePeriodo . '[' . lang($rowperiodos['modalidad']) . '])';
            }
        }
        return $matriculas;
    }

    public function getMateriales(){
        $conexion = $this->load->database($this->codigofilial, true);
        $arrDocumentacion = Vmateriales::listarMateriales($conexion);
        return $arrDocumentacion;
    }

    public function getDocumentacion($plan = null){
        $conexion = $this->load->database($this->codigofilial, true);
        if(!$plan){
            return Tdocumentacion_alumnos::listarDocumentacion($conexion);
        }
        $documentaciones =  Tdocumentacion_alumnos::listarDocumentacion($conexion);
        $documentacionPlan = Vdocumentacion::getDocumentacionPlan($conexion, $this->codigofilial, $plan);
        $documentacion = array();
        foreach($documentaciones as $indice =>  $documento){
            if(empty($documentacionPlan) && $documento['tipo'] == 1){
                $documentacion[] = $documento;
            } else {
                $esta = false;
                foreach($documentacionPlan as $docuPlan){
                    if($docuPlan['id_documentacion'] == $documento['codigo']){
                        $esta = true;
                        break;
                    }
                }
                if($esta)
                    $documentacion[] = $documento;
            }
        }
        foreach($documentacion as $indice => $documento){
            $documentacion[$indice]['nombre'] = lang($documento['documentacion']);
        }
        return $documentacion;
    }

    public function getDocumentacion_alumno($cod_matricula)
    {
        $conexion = $this->load->database($this->codigofilial, true);
        $condiciones = array("cod_matricula"=>$cod_matricula);
        return Tdocumentacion_alumnos::listarDocumentacion_alumnos($conexion, $condiciones);
    }

    public function deleteTodaDocumentacion($cod_matricula)
    {
        $conexion = $this->load->database($this->codigofilial, true);

        $conexion->trans_begin();
        $conexion->delete('documentacion_alumnos', array('cod_matricula' => $cod_matricula));
        $conexion->trans_commit();
    }

    public function setMaterialEntregado($cod_matricula, array $doc){
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->where("cod_matricula", $cod_matricula);
        $resp = $conexion->delete("materiales_alumnos");
        if (count($doc) > 0){
            foreach ($doc as $material){
                $resp = $resp && $conexion->insert('materiales_alumnos', array('cod_matricula'=>$cod_matricula, 'id_material' => $material));
            }
        }
        return $resp;
    }

    public function saveDocumentacion($cod_matricula, array $doc)
    {
        $conexion = $this->load->database($this->codigofilial, true);

        $conexion->trans_begin();
        if (count($doc) > 0){
            foreach ($doc as $documentacion){
                if($documentacion != '' && $documentacion != 'null')
                    $conexion->insert('documentacion_alumnos', array('cod_matricula'=>$cod_matricula, 'documentacion' => $documentacion));
            }
        }
        $conexion->trans_commit();
    }

    public function getCursosDisponibles($id_filial, $cod_alumno)
    {
        $conexion = $this->load->database($id_filial, true);
        $alumnos = new Valumnos($conexion, $cod_alumno);

        return $alumnos->getCursosDisponibles();
    }

    public function getMateriasDisponibles($id_filial, $cod_alumno)
    {
        $conexion = $this->load->database($id_filial, true);
        $alumnos = new Valumnos($conexion, $cod_alumno);

        return $alumnos->getMateriasDisponibles();
    }

    public function getMateriasDisponiblesDeCurso($id_filial, $id_curso, $cod_alumno)
    {
        $conexion = $this->load->database($id_filial, true);
        $alumnos = new Valumnos($conexion, $cod_alumno);

        return $alumnos->getMateriasDisponiblesDeCurso($id_curso);
    }

    public function getClasesDisponiblesDeMateria($id_filial, $id_materia, $cod_alumno)
    {
        $conexion = $this->load->database($id_filial, true);
        $alumnos = new Valumnos($conexion, $cod_alumno);
        //esto deberia salir de una configuración
        $config_fecha = 3;

        return $alumnos->getClasesDisponiblesDeMateria($config_fecha, $id_materia, $id_filial);
    }

    public function getMaterialesDidacticosDeClaseParaAlumnoDeFilial($id_clase, $codigo_alumno, $codigo_filial)
    {
        $conexion = $this->load->database($codigo_filial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);

        //esto deberia salir de una configuración
        $config_fecha = 3;

        return $alumnos->getMaterialesDidacticosDeClaseParaAlumnoDeFilial($id_clase, $config_fecha);
    }

	public function getVideosDeMateriaParaAlumnoDeFilial($id_materia, $codigo_alumno, $codigo_filial) {
		$conexion = $this->load->database($codigo_filial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);
        $config_fecha = 3;

        return $alumnos->getVideosDeMateriaParaAlumnoDeFilial($id_materia, $config_fecha);
	}

    public function getProximosVideos($id_clase, $codigo_alumno, $codigo_filial)
    {
        $conexion = $this->load->database($codigo_filial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);

        //esto deberia salir de una configuración
        $config_fecha = 3;

        return $alumnos->getProximosVideos($id_clase, $config_fecha);
    }

    public function getVideosAnteriores($id_clase, $codigo_alumno, $codigo_filial)
    {
        $conexion = $this->load->database($codigo_filial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);

        //esto deberia salir de una configuración
        $config_fecha = 3;

        return $alumnos->getVideosAnteriores($id_clase, $config_fecha);
    }

    public function tieneAccesoPlataformaElearning($id_filial, $cod_alumno) {
        $conexion = $this->load->database($id_filial, true);
        $alumnos = new Valumnos($conexion, $cod_alumno);

        return $alumnos->tieneAccesoPlataformaElearning();
    }

//	public function getDatosDeCurso($id_filial, $codigo_curso) {
//		$conexion = $this->load->database($id_filial, true);
//        $alumnos = new Valumnos($conexion, $cod_alumno);
//
//        return $alumnos->tieneAccesoPlataformaElearning();
//	}

    public function getProximoVideoEnVivo($codigo_alumno, $codigo_filial)
    {
        $conexion = $this->load->database($codigo_filial, true);
        $alumnos = new Valumnos($conexion, $codigo_alumno);
        //esto deberia salir de una configuración
        $config_fecha = 3;

        return $alumnos->getProximoVideoEnVivo($config_fecha);
    }

    public function get_materias_plan_academico($cod_filial, $cod_alumno, $idioma = null, $estado = null){
        $conexion = $this->load->database($cod_filial, true);
        if ($idioma == null){
            $myFilial = new Vfiliales($conexion, $cod_filial);
            $idioma = $myFilial->idioma;
        }
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        $arrMaterias = $myAlumno->get_materias_plan_academico($idioma, $estado);
        $arrResp = array();
        foreach ($arrMaterias as $materia){
            $codMatricula = $materia['codigo'];
            $codPlanacademico = $materia['cod_plan_academico'];
            $codMateria = $materia['codmateria'];
            $nombrePlan = $materia['nombre_curso'];
            $nombreMateria = $materia['nombre_materia'];
            $arrResp[$codMatricula][$codPlanacademico]['nombre_plan_academico']  = $nombrePlan;
            $arrResp[$codMatricula][$codPlanacademico]['materias'][$codMateria] = $nombreMateria;
        }
        return $arrResp;
    }


    public function check_exist_alumno_campus($email){

        // chequeamos si existe
        $conexion->select("*");
        $conexion->from("campus.usuarios");
        $conexion->where("campus.usuarios.email", $email);
        $query = $conexion->get();
        $arrResp = $query->result_array();

        return $arrResp;

    }

    /*
    Funcion actualizar original
    public function actualizar_email_campus($emailantes, $emailnuevo){

        $conexion = $this->load->database("campus", true);
        $conexion->where("email", $emailantes);

        return $conexion->update("usuarios", array("email" => $emailnuevo));
    }
    */

    /* Se le agrego la opcion de actualizar el mail en el campus especificamente enviandole el codigo de alumno y recuperando el usuario */
    public function actualizar_email_campus($emailantes, $emailnuevo, $codalumno = null, $codfilial = null){

        $conexion = $this->load->database("campus", true);

        if($codalumno != null || $codalumno != '' && $codalumnocodfilial != null || $codfilial != ''){
            $conexion->select('id_usuario');
            $conexion->where('id_interno', $codalumno);
            $conexion->where('id_filial', $codfilial);
            $codusuario = $conexion->get('usuarios_tipos_filiales')->result();
            //die(var_dump($codusuario));
            $codusuario = $codusuario[0]->id_usuario;
            $conexion->where("id", $codusuario);
        } else {
            $conexion->where("email", $emailantes);
        }

        return $conexion->update("usuarios", array("email" => $emailnuevo));
    }

    // Este metodo crea un usuario en el campus nuevo
    public function alta_campus_nuevo($nombre, $apellido, $email, $sexo, $idioma, $cod_filial, $cod_alumno){

        $conexion = $this->load->database('campus', true);
        $tempPass = $this->randomPassword();

        // Array apra la tabla usuarios
        $arrDatosUsuarios = array(
            "nombre" => $nombre,
            "apellido" => $apellido,
            "email" => $email,
            "pass" => md5($tempPass),
            "sexo" => $sexo,
            "idioma" => $idioma

        );
        $conexion->insert('campus.usuarios', $arrDatosUsuarios);

        $id = $conexion->insert_id();

        // Array para la table usuarios_tipo_filial
        $arrDatosTipoFilial = array(
            "id_usuario" => $id,
            "tipo" => "alumno",
            "id_filial" => $cod_filial,
            "id_interno" => $cod_alumno,
            "estado"    => "habilitado"
        );
        $conexion->insert('campus.usuarios_tipos_filiales', $arrDatosTipoFilial);

        // Array secciones del campus
        $arrSec = array(1,2,3,7,8,9,19);

        foreach ($arrSec as $sec){
            // Array para tabla usuarios_permisos

            $arrDatosSecciones = array(
                "id_usuario" => $id,
                "tipo" => "alumno",
                "id_filial" => $cod_filial,
                "id_seccion" => $sec
            );
            $conexion->insert('campus.usuarios_permisos', $arrDatosSecciones);
        }

        $this->send_password($email,$tempPass);
    }

    public function resetear_password_campus_nuevo($cod_alumno, $cod_filial){

        $conexion = $this->load->database("campus", true);

        // Traemos el id de usuario campus
        $conexion->select("*");
        $conexion->from("campus.usuarios_tipos_filiales");
        $conexion->where("campus.usuarios_tipos_filiales.id_filial", $cod_filial);
        $conexion->where("campus.usuarios_tipos_filiales.id_interno", $cod_alumno);
        $query = $conexion->get();
        $arrResp = $query->result_array();

       // var_dump($arrResp);

        if(!isset($arrResp[0]['id_usuario'])) return class_general::_generarRespuestaModelo($conexion, $arrResp, "No existe el usuario en el campus");

        $id_usuario= $arrResp[0]['id_usuario'];

        // Traemnos info del usuairo del campus

        $conexion->select("*");
        $conexion->from("campus.usuarios");
        $conexion->where("campus.usuarios.id", $id_usuario);
        $query =  $conexion->get();

        $arrResp = $query->result_array();

        $email= $arrResp[0]['email'];

        // Seteamos un password temporal
        $tempPass = $this->randomPassword();
        $arrPass = array(
            "pass" => md5($tempPass)
        );
        $conexion->where("campus.usuarios.id", $id_usuario);
        $conexion->update('campus.usuarios', $arrPass);

        // Le enviamos el password
        $send = $this->send_password($email, $tempPass);
        return class_general::_generarRespuestaModelo($conexion, $send, $tempPass);



    }

    public function deshabilitar_usuario_campus($cod_alumno, $cod_filial){

        $conexion = $this->load->database("campus", true);
        $conexion->where("id_interno", $cod_alumno);
        $conexion->where("id_filial", $cod_filial);

        return $conexion->update("usuarios_tipos_filiales", array("estado" => "habilitado"));
    }

    public function habilitar_usuario_campus($cod_alumno, $cod_filial){

        $conexion = $this->load->database("campus", true);
        $conexion->where("id_interno", $cod_alumno);
        $conexion->where("id_filial", $cod_filial);

        return $conexion->update("usuarios_tipos_filiales", array("estado" => "inhabilitado"));
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    function send_password($email, $pass){
        // Envia Password por mail
        $this->load->library('email');
        $config= array();
        $config['charset'] = '  UTF-8';
        $this->email->initialize($config);
        $this->email->from("noreply@igacloud.net", 'IGA Campus');
        $this->email->to($email);

        $body = file_get_contents(base_url('assents/campus_templates/campus.html'));
        $body = str_replace('#email#',$email, $body );
        $body = str_replace('#password#',$pass, $body );

        $asunto = 'IGA Campus - Nueva Contraseña';
        $asunto = utf8_decode($asunto);


        $asunto = 'IGA Campus - Nueva Contraseña';
        $asunto = utf8_decode($asunto);

        $this->email->subject($asunto);
        $this->email->message($body);

        $respuesta = $this->email->send();

        if (!$respuesta) {
            $comentario = $this->email->print_debugger();
            return $respuesta;
        }
        $this->email->clear();

        return $respuesta;
    }

    //EJECUTAR SOLO PARA EL LANZAMIENTO
    function alta_masiva_nuevo_campus()
    {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2, "baja" => "0"));

        foreach($arrFiliales as $filial)
        {
            $cod_filial = $filial['codigo'];

            //if($cod_filial > 46) {
                $conexion = $this->load->database($cod_filial, true);

                $sql = "SELECT `alumnos`.`nombre`, `apellido`, `alumnos`.`email`, `sexo`, `alumnos`.`codigo` as cod_alumno, `general`.`filiales`.`idioma` as idioma FROM (`alumnos`) LEFT JOIN `general`.`filiales` ON `general`.`filiales`.`codigo` = $cod_filial WHERE `alumnos`.`baja` = 'habilitada' AND `alumnos`.`email` != 'null' AND `sexo` != 'null'";

                $query = $conexion->query($sql);

                //$conexion->limit(1);
                //$query = $conexion->get();
                $result = $query->result_array();

                echo 'Filial: '.$cod_filial.'<br>';
                echo count($result). ' registros, <br>';
                //die(var_dump($result));

                foreach($result as $alumno)
                {
                    //if($cod_filial != '47' || ($cod_filial == '47' && $alumno['codigo'] > 1794))
                    //{
                        echo $alumno['nombre'].','. $alumno['apellido'].','. $alumno['email'].','. $alumno['sexo'].','. $alumno['idioma'].','. $cod_filial.','. $alumno['cod_alumno'].'<br>';
                        $this->alta_campus_nuevo($alumno['nombre'], $alumno['apellido'], $alumno['email'], $alumno['sexo'], $alumno['idioma'], $cod_filial, $alumno['cod_alumno']);
                    //}

                }

           //}
        }
    }

    function getDatosAcademicosAlumno($conexion, $codigo_alumno)
    {
        $datos = Valumnos::getDatosAcademicosAlumno($conexion, $codigo_alumno);
        return $datos;
    }
    
    function regenerar_password($cod_alumno, $cod_filial){
        $conexion = $this->load->database("campus", true);

        // Traemos el id de usuario campus
        $conexion->select("*");
        $conexion->from("campus.usuarios_tipos_filiales");
        $conexion->where("campus.usuarios_tipos_filiales.id_filial", $cod_filial);
        $conexion->where("campus.usuarios_tipos_filiales.id_interno", $cod_alumno);
        $query = $conexion->get();
        $arrResp = $query->result_array();

        // var_dump($arrResp);

        if(!isset($arrResp[0]['id_usuario'])) return class_general::_generarRespuestaModelo($conexion, $arrResp, "No existe el usuario en el campus");

        $id_usuario= $arrResp[0]['id_usuario'];

        // Traemnos info del usuairo del campus

        $conexion->select("*");
        $conexion->from("campus.usuarios");
        $conexion->where("campus.usuarios.id", $id_usuario);
        $query =  $conexion->get();

        $arrResp = $query->result_array();

        $email= $arrResp[0]['email'];

        // Seteamos un password temporal
        $tempPass = $this->randomPassword();
        $arrPass = array(
            "pass" => md5($tempPass)
        );
        $conexion->where("campus.usuarios.id", $id_usuario);
        $conexion->update('campus.usuarios', $arrPass);

        $password = array(
            "codigo" => "1",
            "custom" => $tempPass,
        );
        
        return $password;
    }

    function api_getEstadoAcademicoMateria($conexion, $estado_academico){
        $conexion->select("estado");
        $conexion->from("estadoacademico");
        $conexion->where("estadoacademico.codigo", $estado_academico);
        $query =  $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }
}
