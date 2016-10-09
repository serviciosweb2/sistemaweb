<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_alertas extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["codigo_filial"];
    }

    function resumen_alertas_usuario(){
        $idioma = $this->session->userdata("idioma");
        $conexion = $this->load->database($this->codigofilial, true);
        $arrRegistros = Valertas::getAlertasCantidadesUsuarios($conexion, $this->session->userdata['codigo_usuario'], null, 0);
        $cantidad = 0;
        $i = 0;
        foreach ($arrRegistros as $key => $registro){
            $cantidad += $registro['cantidad'];
            $arrRegistros[$key]['nombre_alerta'] = lang($arrRegistros[$key]['tipo_alerta']);
            $i++;
        }
        $arrResp = Vestadoacademico::getEstadoAcademicoDetalles($conexion, $idioma, true, Vmatriculas_periodos::getEstadoHabilitada(), Vmatriculas::getEstadoHabilitada(), Vestadoacademico::getEstadoCursando(), true);
        $cantidades = count($arrResp);
        if ($cantidades > 0){
            $i++;
            $arrRegistros[$i]['tipo_alerta'] = "cambio_estado_academico";
            $arrRegistros[$i]['cantidad'] = $cantidades;
            $arrRegistros[$i]['visto'] = 0;
            $arrRegistros[$i]['nombre_alerta'] = lang("cambios_en_estado_academico");
            $arrRegistros[$i]['url_notificacion'] = "matriculas/cambios_estado_academico";
            $cantidad ++;
        }        
        $cantidadFacturasError = $this->Model_facturas->getFacturas(null, true, array("facturas.estado" => Vfacturas::getEstadoError()));
        if ($cantidadFacturasError > 0){
            $arrRegistros[$i + 1]['tipo_alerta'] = "facturas_con_errores";
            $arrRegistros[$i + 1]['cantidad'] = $cantidadFacturasError;
            $arrRegistros[$i + 1]['visto'] = "0";
            $arrRegistros[$i + 1]['nombre_alerta'] = lang("facturas_con_errores");
            $arrRegistros[$i + 1]['url_notificacion'] = "facturacion/index/" . Vfacturas::getEstadoError();
            $cantidad += $cantidadFacturasError;
        }
        $arrResp = array();
        $arrResp['alertas'] = $arrRegistros;
        $arrResp['total'] = $cantidad;
        return $arrResp;
    }

    function listar_alertas($codUsuario = null, $visto = null, $tipoAlerta = null, array $arrOrder = null, $limitMin = 0, $limitCant = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $arrAlertas = Valertas::listarAlertasUsuarios($conexion, $codUsuario, $visto, $tipoAlerta, $arrOrder, $limitMin, $limitCant);
        $cantidad = Valertas::listarAlertasUsuarios($conexion, $codUsuario, $visto, $tipoAlerta, null, null, null, true);
        $arrResp = array();
        if (is_array($arrAlertas)) {
            foreach ($arrAlertas as $alerta) {
                $alerta['nombre_alerta'] = lang($alerta['tipo_alerta']);
                $alerta['mensaje'] = maquetados::desetiquetarIdioma($alerta['mensaje']);
                $arrResp['data'][$alerta['fecha']][$alerta['tipo_alerta']][] = $alerta;
            }
            $arrResp['last'] = $limitCant === null ? $cantidad : $limitMin + $limitCant;
            $arrResp['count'] = $cantidad;
        } else {
            $arrResp['error'] = "error";
        }
        return $arrResp;
    }

    function marcar_como_leida($codigoAlerta, $codigoUsuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $myAlerta = new Valertas($conexion, $codigoAlerta);
        return $myAlerta->marcarLeida($codigoUsuario);
    }

    //PARCHE ALERTAS AL ENVIAR DEUDORES!!!!
    function enviarAlertasAlumnosConDeuda($cod_filial) {
        $conexion = $this->load->database("default", true);
        $this->load->library('email');
        $this->lang->load(get_idioma(), get_idioma());

        $conexion = $this->load->database($cod_filial, true);
        $config = array("codigo_filial" => $cod_filial);
        $alertasenviar = Valertas::getAlertasAlumnosNoEnviadas($conexion);
        $resultado = false;
        $comentario = '';
        foreach ($alertasenviar as $alerta) {
            $objalerta = new Valertas($conexion, $alerta['cod_alerta']);
            switch ($objalerta->tipo_alerta) {
                case 'deuda_ctacte'://mail
                    $this->load->model("Model_ctacte", "", false, $config);
                    $resultado = $this->Model_ctacte->enviarDeudasCtaCte($alerta, $objalerta, $conexion, $comentario);
                    break;
            }
            if ($resultado) {
                $objalerta->setAlertaAlumnoEnviada($alerta['cod_alumno'], $comentario);
            } else {
                $objalerta->setAlertaAlumnoError($alerta['cod_alumno'], $comentario);
            }
            sleep(5);
        }
    }

    function enviarAlertasAlumnos() {
        $conexion = $this->load->database("default", true);
        $this->load->library('email');
        $this->lang->load(get_idioma(), get_idioma());
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        foreach ($arrFiliales as $filial) {
            echo $filial['codigo']."<br>";
            $conexion = $this->load->database($filial['codigo'], true);
            $config = array("codigo_filial" => $filial['codigo']);
            $alertasenviar = Valertas::getAlertasAlumnosNoEnviadas($conexion);
            $resultado = false;
            $comentario = '';
            foreach ($alertasenviar as $alerta) {
                $objalerta = new Valertas($conexion, $alerta['cod_alerta']);
                switch ($objalerta->tipo_alerta) {
                    case 'deuda_ctacte'://mail
                        $this->load->model("Model_ctacte", "", false, $config);
                        $resultado = $this->Model_ctacte->enviarDeudasCtaCte($alerta, $objalerta, $conexion, $comentario);
                        break;
                    case 'comunicado_alumnos'://mail
                        $this->load->model("Model_comunicados", "", false, $config);
                        $resultado = $this->Model_comunicados->enviarComunicadosAlumnos($alerta, $objalerta, $conexion, $comentario);
                        break;
                    case 'recordatorio_examen'://mail
                        $this->load->model("Model_examenes", "", false, $config);
                        $resultado = $this->Model_examenes->enviarRecordatorioExamen($alerta, $objalerta, $conexion, $comentario);
                        break;
                    case 'login_campus'://mail
                        $this->load->model("Model_alumnos", "", false, $config);
                        $resultado = $this->Model_alumnos->enviarLoginCampus($alerta, $objalerta, $conexion, $filial['codigo'], $comentario);
                        break;
                    default:
                        break;
                }
                if ($resultado) {
                    $objalerta->setAlertaAlumnoEnviada($alerta['cod_alumno'], $comentario);
                } else {
                    $objalerta->setAlertaAlumnoError($alerta['cod_alumno'], $comentario);
                }
                sleep(10);
            }
        }
    }

    function listarAlertasNoEnviadas($contar = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        return Valertas::listarAlertasNoEnviadas($conexion, 2, $contar);
    }

    public function bajaAlertaAlumnos($arrCodigo_alerta) {
        $conexion = $this->load->database($this->codigofilial, true);
        $resultado = '';
        $comentario = 'cod_usuario= ' . $this->session->userdata('codigo_usuario');
        foreach ($arrCodigo_alerta as $cod_alerta) {
            $arrAlertas = json_decode($cod_alerta, true);
            $myAlerta = new Valertas($conexion, $arrAlertas['cod_alerta']);
            $resultado = $myAlerta->setAlertaAlumnoCancelada($arrAlertas['cod_alumno'], $comentario);
        }
        return class_general::_generarRespuestaModelo($conexion, $resultado);
    }    
}
