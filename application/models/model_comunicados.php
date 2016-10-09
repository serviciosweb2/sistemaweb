<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_comunicados extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function guardarComunicados($mensaje, $alumnosMandarComunicado, $cod_usuario, $asunto, $cod_comision, $cod_materia) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $conexion->trans_start();

        $myTemplate = new Vtemplates($conexion, 81);
        $html = $myTemplate->html;
        $texto = str_replace('[!textocomunicado]', $mensaje, $html);
        $arrayComunicadosMsj = array(
            'tipo_alerta' => 'comunicado_alumnos',
            'fecha_hora' => date("Y-m-d H:i:s"),
            'mensaje' => $texto,
        );
        $objAlerta = new Valertas($conexion);
        $objAlerta->setAlertas($arrayComunicadosMsj);
        $objAlerta->guardarAlertas();

        foreach ($alumnosMandarComunicado as $cod_alumno) {
            $objAlerta->setAlertaAlumno($cod_alumno);
            if ($cod_materia != '') {
                $arrayAluConfiguracion = array(
                    'cod_alerta' => $objAlerta->getCodigo(),
                    'cod_alumno' => $cod_alumno,
                    'key' => 'cod_comision',
                    'valor' => $cod_comision
                );
                $objAlerta->setAlertaAlumnoConfiguracion($arrayAluConfiguracion);
                $arrayguardarMateria = array(
                    'cod_alerta' => $objAlerta->getCodigo(),
                    'cod_alumno' => $cod_alumno,
                    'key' => 'cod_materia',
                    'valor' => $cod_materia
                );
                $objAlerta->setAlertaAlumnoConfiguracion($arrayguardarMateria);
            } else {
                $arrayAluConfiguracion = array(
                    'cod_alerta' => $objAlerta->getCodigo(),
                    'cod_alumno' => $cod_alumno,
                    'key' => 'cod_comision',
                    'valor' => $cod_comision
                );
                $objAlerta->setAlertaAlumnoConfiguracion($arrayAluConfiguracion);
            }
        }
        $objAlerta->setAlertaConfiguracion('titulo', $asunto);
        $objAlerta->setAlertaConfiguracion('cod_usuario_creador', $cod_usuario);

        $conexion->trans_complete();
        $resultado = $conexion->trans_status();
        return class_general::_generarRespuestaModelo($conexion, $resultado);
    }

    public function getComunicadosEmailComisionMateria($cod_comision, $arrFiltros, $filtro) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $arrCondiciones = '';

        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "comunicados_mensaje.asunto" => $arrFiltros["sSearch"],
            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] !== "" && $arrFiltros["iDisplayLength"] !== "") {
            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }

        $datos = Valertas::getComunicadosEmailComisionMateria($conexion, $cod_comision, false, $arrCondiciones, $arrLimit, $filtro);
        $contar = Valertas::getComunicadosEmailComisionMateria($conexion, $cod_comision, true, $arrCondiciones, null, $filtro);

        $retorno = array(
            "iTotalRecords" => $contar,
            "aaData" => $datos
        );
        return $retorno;
    }

    public function getAlumnosComunicado($cod_comision, $cod_materia, $cod_alerta) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $objAlerta = new Valertas($conexion, $cod_alerta);

        if ($cod_materia == 'null') {
            $alumnosComunicados = $objAlerta->getAlumnosMensajeComision($cod_comision);
        } else {
            $alumnosComunicados = $objAlerta->getAlumnosMensajeComision($cod_comision, $cod_materia);
        }
        return $alumnosComunicados;
    }

    public function enviarComunicadosAlumnos($alerta, $objalerta, CI_DB_mysqli_driver $conexion = null, &$comentario) {
        if ($conexion == null) {
            $conexion = $this->load->database($this->codigo_filial, true);
        }
        $confalerta = $objalerta->getAlertaConfiguracion();
        foreach ($confalerta as $value) {
            if ($value['key'] == 'titulo') {
                $asunto = $value['valor'];
            }
        }
        $cuerpomail = $alerta['mensaje'];
        $objalumno = new Valumnos($conexion, $alerta['cod_alumno']);        //envio mail
        $config = array();
        $config['charset'] = 'iso-8859-1';
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $this->codigo_filial);
        maquetados::desetiquetarIdioma($cuerpomail, true);
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
        return $respuesta;
    }
    
    public function listar_comunicados($cod_filial, $cod_alumno, $codComunicado = null){
        $conexion = $this->load->database($cod_filial, true);
        $arrComunicados = Valertas::listar_alertas_alumno($conexion, $cod_filial, $cod_alumno, $codComunicado);
        $link = $this->config->item('campus_url');
        foreach ($arrComunicados as $key => $comunicado){
            $mensaje = $comunicado['mensaje'];
            maquetados::desetiquetarAlumnos($conexion, $cod_alumno, $mensaje);
            maquetados::desetiquetarDatosFilial($conexion, null, $mensaje, $cod_filial);
            maquetados::desetiquetarIdioma($mensaje, true);            
            maquetados::desetiquetarLinkCampus($link, $mensaje);
            maquetados::desetiquetarMd5(md5(date("Y-m-d H:i:s")), $mensaje);
            $arrComunicados[$key]['mensaje'] = $mensaje;
        }
        return $arrComunicados;
    }
}