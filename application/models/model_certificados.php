<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model_certificados
 * 
 * ...
 * 
 * @package model_matriculas
 * @author vane
 * @version 1.0.0
 */
class Model_certificados extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigofilial = $arg["filial"]["codigo"];
    }

    
    public function listarCertificados2DataTable($arrFiltros, $estado = null, $certificante = null, $mostrarEstadoPendiente = true, 
            $comision = null, $curso = null){        
        $conexion = $this->load->database($this->codigofilial, true);
        $filial = $this->session->userdata('filial');
        $separador = $filial['nombreFormato']['separadorNombre'];
        $this->load->helper('alumnos');
        $arrLike = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrLike = array(
                "cod_matricula" => $arrFiltros["sSearch"],
                "nombre_apellido" => $arrFiltros["sSearch"]
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
        if (isset($arrFiltros["SortCol"]) && $arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        } else {
            $arrSort = array(
                "0" => 'matriculas.fecha_emision',
                "1" => 'desc'
            );
        }
        
        //$datos = Vcertificados::listarCertificadosDataTable2($conexion, $this->codigofilial, $arrLike, $arrLimit, $arrSort, false, $separador, $estado, $certificante, $mostrarEstadoPendiente);
        //$contar = Vcertificados::listarCertificadosDataTable2($conexion, $this->codigofilial, $arrLike, "", "", true, $separador, $estado, $certificante, $mostrarEstadoPendiente);

        $datos = Vcertificados::listarCertificados($conexion, $this->codigofilial, $certificante, $estado, false, $arrLimit, $arrSort, $arrLike, $separador, $comision, $curso);
        $contar = Vcertificados::listarCertificados($conexion, $this->codigofilial, $certificante, $estado, true, $arrLimit, $arrSort, $arrLike, $separador, $comision, $curso);
        
        
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();

        foreach ($datos as $row) {
            $objmatper = new Vmatriculas_periodos($conexion, $row['cod_matricula_periodo']);
            $objcurso = new Vcursos($conexion, $row['cod_curso']);
            switch (get_idioma()){
                case 'es':
                    $nombrecurso = $objcurso->nombre_es;
                    break;
                case 'pt':
                    $nombrecurso = $objcurso->nombre_pt;
                    break;
                case 'in':
                    $nombrecurso = $objcurso->nombre_in;
                    break;
                default:
                    $nombrecurso = '';
                    break;
            }
            $objplan = new Vplanes_academicos($conexion, $row['cod_plan_academico']);
            $periodosplan = $objplan->getPeriodos();
            if (count($periodosplan) > 1) {
                $nombrePeriodo = lang(Vtipos_periodos::getNombre($conexion, $row['cod_tipo_periodo']));
                $nombrecurso.= ' (' . $nombrePeriodo . ')';
            }
            $condplanes = array('cod_curso' => $objcurso->getCodigo());
            $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condplanes);
            if (count($planescurso) > 1) {
                $nombrecurso.=' / ' . $objplan->nombre;
            }
            $detalle = '';
            switch ($row['estado']) {
//                case 'pendiente_aprobar':
//                case 'finalizado':
//                case 'pendiente':                  
//                    $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);
//                    $objcertificadoplan = new Vcertificados_plan_filial($conexion, $this->codigofilial, $objmatricula->cod_plan_academico, $objmatper->cod_tipo_periodo, $row['cod_certificante']);
//                    $objcertificado = new Vcertificados($conexion, $row['cod_matricula_periodo'], $row['cod_certificante']);
//                    $condiciones = array('cod_certificante' => $row['cod_certificante'], 'cod_matricula_periodo' => $row['cod_matricula_periodo'], 'estado' => Vcertificados::getEstadoPendienteImpresion());
//                    $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
//                    $historico = Vcertificados_estado_historico::listarCertificados_estado_historico($conexion, $condiciones, null, $orden);
//                    $fechahistorico = count($historico) > 0 ? $historico[0]['fecha_hora'] : date('Y-m-d');
//                    $fechaaprobo = explode(' ', $fechahistorico);
//                    $todaspropiedades = $objcertificadoplan->getPropiedadesImprimirCertificado($fechaaprobo[0]);
//                    $propiedadesimprimir = array();
//                    $propiedadesimprimir = $objcertificado->getPropiedadesImpresion();
//                    foreach ($todaspropiedades as $propiedad) {
//                        $detalle.=lang($propiedad['key']) . ': ';
//                        $encontro = false;
//                        for ($a = 0; $a < count($propiedadesimprimir); $a++) {
//                            if ($propiedadesimprimir[$a]['key'] == $propiedad['key']) {
//                                $detalle.=formatearFecha_pais($propiedadesimprimir[$a]['valor']) . '</br>';
//                                $encontro = true;
//                            }
//                        }
//                        $detalle.=!$encontro ? lang('INCOMPLETA') . '</br>' : '';
//                    }
//                    break;
                case 'pendiente':
                    $objcertificado = new Vcertificados($conexion, $row['cod_matricula_periodo'], $row['cod_certificante']);
                    $requerimientos = $objcertificado->getRequerimientos('no_cumplido');
                    $deuda = $objcertificado->alumnoDeuda();

                    foreach ($requerimientos as $rowrequerimiento) {
                        if(intval($deuda[0]['deuda']) > 0){
                            $detalle .= '-'. lang('el_alumno_tiene_deudas') . '</br>';
                        }
                        switch ($rowrequerimiento['key']) {
                            /*case 'deudas':
                                    $detalle .= '-'. lang('el_alumno_tiene_deudas') . '</br>';
                                break;   --- No_es_del_todo_fiable */
                            case 'matricula_periodo_finalizada':
                                    $detalle .= '-' . lang('no_cumple_requisitos_academicos') . '</br>';
                                break;
                            case 'pagos_conceptos':
                                    $detalle .= '-' . lang('no_cumple_pagos_de_certificado') . '</br>';
                                break;
                            default:
                                break;
                        }
                    }

                    break;
                default:
                    break;
            }
            $myComision = new Vcomisiones($conexion, $row['cod_comision']);
            if ($certificante == 1){
                $rows[] = array(
                    $row['cod_matricula_periodo'],
                    $row['cod_matricula'],
                    ucwords(strtolower($row['nombre_apellido'])),
                    $row['documento_alumno'],
                    $nombrecurso,
                    $myComision->nombre,
                    $row['fecha_inicio'],
                    $row['fecha_fin'],
                    ucwords(strtolower($row['titulo'])),
                    $row['fecha_pedido'],
                    $row['estado'],
                    $detalle,
                    ucwords(strtolower($row['usuario_aprueba'])),
                    $row['entregado']         
                );
            } else {
                $rows[] = array(
                    $row['cod_matricula_periodo'],
                    $row['cod_matricula'],
                    ucwords(strtolower($row['nombre_apellido'])),
                    $row['documento_alumno'],
                    $nombrecurso,
                    $myComision->nombre,
                    $row['fecha_inicio'],
                    $row['fecha_fin'],
                    ucwords(strtolower($row['titulo'])),
                    $row['fecha_pedido'],
                    $row['estado'],
                    ucwords(strtolower($row['usuario_aprueba'])),
                    $row['entregado']
                );
            }
        }
        $retorno['aaData'] = $rows;
        //print_r($rows);
        return $retorno;
    }
    
    
    public function listarCertificadosDataTable($arrFiltros, $pestania, $separador) {

        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $arrLike = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrLike = array(
                "nombre_apellido" => $arrFiltros["sSearch"]
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
        } else {
            $arrSort = array(
                "0" => 'matriculas.fecha_emision',
                "1" => 'desc'
            );
        }

        $datos = Vcertificados::listarCertificadosDataTable($conexion, $pestania, $this->codigofilial, $arrLike, $arrLimit, $arrSort, false, $separador);
        $contar = Vcertificados::listarCertificadosDataTable($conexion, $pestania, $this->codigofilial, $arrLike, "", "", true, $separador);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();

        foreach ($datos as $row) {
            $objmatper = new Vmatriculas_periodos($conexion, $row['cod_matricula_periodo']);
            $objcurso = new Vcursos($conexion, $row['cod_curso']);
            switch (get_idioma()){
                case 'es':
                    $nombrecurso = $objcurso->nombre_es;
                    break;
                case 'pt':
                    $nombrecurso = $objcurso->nombre_pt;
                    break;
                case 'in':
                    $nombrecurso = $objcurso->nombre_in;
                    break;
                default:
                    $nombrecurso = '';
                    break;
            }
            $objplan = new Vplanes_academicos($conexion, $row['cod_plan_academico']);
            $periodosplan = $objplan->getPeriodos();
            if (count($periodosplan) > 1) {
                $nombrePeriodo = lang(Vtipos_periodos::getNombre($conexion, $row['cod_tipo_periodo']));
                $nombrecurso.= ' (' . $nombrePeriodo . ')';
            }
            $condplanes = array('cod_curso' => $objcurso->getCodigo());
            $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condplanes);
            if (count($planescurso) > 1) {
                $nombrecurso.=' / ' . $objplan->nombre;
            }
            $detalle = '';
            switch ($pestania) {
                
                case 'aprobar':
                case 'finalizados':
                case 'pendiente_certificar':                  
                    $objmatricula = new Vmatriculas($conexion, $objmatper->cod_matricula);
                    $objcertificadoplan = new Vcertificados_plan_filial($conexion, $this->codigofilial, $objmatricula->cod_plan_academico, $objmatper->cod_tipo_periodo, $row['cod_certificante']);
                    $objcertificado = new Vcertificados($conexion, $row['cod_matricula_periodo'], $row['cod_certificante']);
                    $condiciones = array('cod_certificante' => $row['cod_certificante'], 'cod_matricula_periodo' => $row['cod_matricula_periodo'], 'estado' => Vcertificados::getEstadoPendienteImpresion());
                    $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
                    $historico = Vcertificados_estado_historico::listarCertificados_estado_historico($conexion, $condiciones, null, $orden);
                    $fechahistorico = count($historico) > 0 ? $historico[0]['fecha_hora'] : date('Y-m-d');
                    $fechaaprobo = explode(' ', $fechahistorico);
                    $todaspropiedades = $objcertificadoplan->getPropiedadesImprimirCertificado($fechaaprobo[0]);
                    $propiedadesimprimir = array();
                    $propiedadesimprimir = $objcertificado->getPropiedadesImpresion();
                    foreach ($todaspropiedades as $propiedad) {
                        $detalle.=lang($propiedad['key']) . ': ';
                        $encontro = false;
                        for ($a = 0; $a < count($propiedadesimprimir); $a++) {
                            if ($propiedadesimprimir[$a]['key'] == $propiedad['key']) {
                                $detalle.=formatearFecha_pais($propiedadesimprimir[$a]['valor']) . '</br>';
                                $encontro = true;
                            }
                        }
                        $detalle.=!$encontro ? lang('INCOMPLETA') . '</br>' : '';
                    }
                    break;

                case 'pendientes':
                    $objcertificado = new Vcertificados($conexion, $row['cod_matricula_periodo'], $row['cod_certificante']);
                    $requerimientos = $objcertificado->getRequerimientos('no_cumplido');
                    foreach ($requerimientos as $rowrequerimiento) {
                        
                        switch ($rowrequerimiento['key']) {
                            case 'deudas':
                                $detalle.='-' . lang('el_alumno_tiene_deudas') . '</br>';
                                break;
                            case 'matricula_periodo_finalizada':
                                $detalle.='-' . lang('no_cumple_requisitos_academicos') . '</br>';

                                break;
                            case 'pagos_conceptos':
                                $detalle.='-' . lang('no_cumple_pagos_de_certificado') . '</br>';

                                break;
                            default:
                                break;
                        }
                    }

                    break;

                case 'cancelados':
                    $condiciones = array('estado' => Vcertificados::getEstadoCancelado(), 'cod_matricula_periodo' => $row['cod_matricula_periodo'], 'cod_certificante' => $row['cod_certificante']);
                    $orden = array(array('campo' => 'codigo', 'orden' => 'desc'));
                    $estadohistorico = Vcertificados_estado_historico::listarCertificados_estado_historico($conexion, $condiciones, null, $orden);
                    if (count($estadohistorico) > 0) {
                        $motivo = Vcertificados_estado_historico::getmotivos($estadohistorico[0]['motivo']);
                        $detalle.=lang($motivo['motivo']);
                    }
                    break;
                default:
                    break;
            }

            $rows[] = array(
                '',
                $row['nombre_apellido'],
                $row['tipo_documento'],
                $row['documento'],
                $nombrecurso,
                $row['fecha_emision'],
                $row['titulo'],
                $row['certificante'],
                $detalle,
                $row['estado'],
                '',
                $row['cod_matricula_periodo'],
                $row['cod_certificante']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getDetalleRequerimientos($cod_matricula_periodo, $cod_certificante) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objcertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        return $objcertificado->getRequerimientos();
    }

    public function requerimientosCertificadosPendientes() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => "2"));

        foreach ($arrFiliales as $filial) {
            //echo $filial['codigo']."<br>";
            $conexion = $this->load->database($filial['codigo'], true);

            $conexion->select('codigo');
            $conexion->from('matriculas_periodos');
            //$conexion->where('estado !=', 'inhabilitada');
            $conexion->where_in('matriculas_periodos.estado', 'finalizada, certificada');
            $query = $conexion->get();
            $matriculasPeriodos = $query->result_array();

            foreach($matriculasPeriodos as $matriculaPeriodo)
            {
                $objcertificado = new Vcertificados($conexion, $matriculaPeriodo['codigo'], 1);
                $objcertificado->cambiarEstadoCertificadoIGA();
            }
        }
    }

    public function cambiarPropiedades($arrCambios, $codUsuario = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        $datos = json_decode($arrCambios['certificados'], TRUE);

        foreach ($datos as $row) {
            $objCertificado = new Vcertificados($conexion, $row['cod_matricula_periodo'], $row['cod_certificante']);
//            echo "<pre>"; print_r($objCertificado); echo "</pre>";
            $propiedades = array(array('key' => 'fecha_inicio', 'valor' => formatearFecha_mysql($arrCambios['fecha_inicio'])), array('key' => 'fecha_fin', 'valor' => formatearFecha_mysql($arrCambios['fecha_fin'])));
            $objCertificado->setPropiedadesImpresion($propiedades, $codUsuario);
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function aprobarCertificados($arrCertificados, $codusuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        foreach ($arrCertificados as $rowcertificado) {
            $codigos = json_decode($rowcertificado, TRUE);
            $objCertificado = new Vcertificados($conexion, $codigos['cod_matricula_periodo'], $codigos['cod_certificante']);
            $objCertificado->setPendienteImpresion($codusuario);
            if ($codigos['cod_certificante'] == 2){ // solo propiedades de impresion para certificante ucel                
                $arrPropiedades = $objCertificado->getPropiedadesImpresion();
                if (count($arrPropiedades) == 0){
                    $myMatricula = new Vmatriculas_periodos($conexion, $objCertificado->getCodigoMatriculaPeriodo());
                    $arrPropiedades = $myMatricula->getFechaInicioFin();
                    if (count($arrPropiedades) > 0 && isset($arrPropiedades['fecha_inicio']) && isset($arrPropiedades['fecha_fin'])){
                        $arrPropiedades = array(
                            array("key" => "fecha_inicio", "valor" => $arrPropiedades['fecha_inicio']),
                            array("key" => "fecha_fin", "valor" => $arrPropiedades['fecha_fin'])
                        );
                        $objCertificado->setPropiedadesImpresion($arrPropiedades, $codusuario);
                    } else {
                        $conexion->trans_rollback();
                        $arrResp = arraY("codigo" => "0", "msg" => "falta agregar fecha inicio y fecha fin");
                        echo json_encode($arrResp);
                        die();
                    }
                }
            }
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }
    
    public function revertirCertificados($arrCertificados, $codusuario){
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        foreach ($arrCertificados as $rowcertificado) {
            $codigos = json_decode($rowcertificado, TRUE);
            $objCertificado = new Vcertificados($conexion, $codigos['cod_matricula_periodo'], $codigos['cod_certificante']);
            $objCertificado->setPendienteAprobar($codusuario);
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }
    public function getPropiedadesFechasCertificado($cod_matricula_periodo, $cod_certificante) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objcertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        $propiedades = $objcertificado->getPropiedadesImpresion();
        $fechainicio = '';
        $fechafin = '';
        if (count($propiedades) > 0){
            foreach ($propiedades as $rowpropiedad) {
                if ($rowpropiedad['key'] == 'fecha_inicio') {
                    $fechainicio = $rowpropiedad['valor'];
                } elseif ($rowpropiedad['key'] == 'fecha_fin') {
                    $fechafin = $rowpropiedad['valor'];
                }
            }
        }
        if ($fechafin == '' && $fechainicio == ''){
            $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
            $propiedades = $myMatriculaPeriodo->getFechaInicioFin();
            if (isset($propiedades['fecha_inicio'])){
                $fechainicio = $propiedades['fecha_inicio'];
            }
            if (isset($propiedades['fecha_fin'])){
                $fechafin = $propiedades['fecha_fin'];
            }
        }
        $fechas = array('fecha_inicio' => formatearFecha_pais($fechainicio),
                        'fecha_fin' => formatearFecha_pais($fechafin)
                    );
        return $fechas;
    }

    public function guardarCertificado($datos) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        $objalumno = new Valumnos($conexion, $datos['cod_alumno']);
        foreach ($datos['certificados'] as $rowcertificado) {
            $cerficado = json_decode($rowcertificado, TRUE);

            $arrmatriculaper = $objalumno->getMatriculasPeriodosPlanAcademico($cerficado['cod_plan_academico'], null, $cerficado['cod_tipo_periodo']);

            $objcertificado = new Vcertificados($conexion, $arrmatriculaper[0]['cod_matricula_periodo'], $cerficado['cod_certificante']);
            $respuesta = $objcertificado->guardar($this->codigofilial, null, null, $datos['cod_usuario']);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function cancelarCertificados($arrCertificados, $codusuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        foreach ($arrCertificados as $rowcertificado) {
            $codigos = json_decode($rowcertificado, TRUE);
            $objCertificado = new Vcertificados($conexion, $codigos['cod_matricula_periodo'], $codigos['cod_certificante']);
            $objCertificado->setCancelado($codusuario, null, 7);
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /* Esta function es utilizada por un web services */

    public function getCertificadosPendientesImprimir($codFilial) {
        $conexion = $this->load->database($codFilial, true);
        $arrResp = Vcertificados::getCertificados_wc($conexion, $codFilial);
        foreach ($arrResp as $key => $value) {
            $hsreloj = $arrResp[$key]['cantidad_horas'];
            $filial = new Vfiliales($conexion, $codFilial);
            $arrResp[$key]['cantidad_horas'] = round($hsreloj * 60 / $filial->minutos_catedra, 1);
        }
        return $arrResp;
    }

    public function habilitarCertificados($arrCertificados, $codusuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();

        foreach ($arrCertificados as $rowcertificado) {
            $codigos = json_decode($rowcertificado, TRUE);
            $objcertificado = new Vcertificados($conexion, $codigos['cod_matricula_periodo'], $codigos['cod_certificante']);
            if ($objcertificado->getCumpleTodosRequerimientos()) {
                $objcertificado->setPendienteAprobar($codusuario);
            } else {
                $objcertificado->setPendiente($codusuario);
            }
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /* esta function está siendo accedida desde un web services */

    public function registrarCertificadoRecibidoWS($codFilial, $arrDatos) {
        $conexion = $this->load->database($codFilial, true);
        $arrResp = array();
        $resp = true;
        foreach ($arrDatos as $certificado) {
            $conexion->trans_begin();
            $myCertificado = new Vcertificados($conexion, $certificado['cod_matricula_periodo'], $certificado['cod_certificante']);
            $myCertificado->setEnProceso(null, date("Y-m-d H:i:s"));
            if ($conexion->trans_status()) {
                $conexion->trans_commit();
            } else {
                $conexion->trans_rollback();
                $resp = false;
                $arrResp['detalles'][] = array("cod_matricula_periodo" => $certificado['cod_matricula_periodo'], "cod_certificante" => $certificado['cod_certificante']);
            }
        }
        if ($resp) {
            $arrResp['success'] = "success";
        } else {
            $arrResp['error'] = "Error al registrar certificados recibidos";
        }
        return $arrResp;
    }

    /* esta function está siendo accedida desde un web services */

    function informar_certificados_finalizados($codFilial, $codMatriculaPeriodo, $codCertificante) {
        $conexion = $this->load->database($codFilial, true);
        $conexion->trans_begin();
        $myCertificado = new Vcertificados($conexion, $codMatriculaPeriodo, $codCertificante);
        $myCertificado->setFinalizado();
        if ($conexion->trans_status()) {
            $conexion->trans_commit();
            return true;
        } else {
            $conexion->trans_rollback();
            return false;
        }
    }

    function get_estado_certificados($codFilial, $codMatriculaPeriodo, $codCertificante) {
        $conexion = $this->load->database($codFilial, true);
        $myCertificado = new Vcertificados($conexion, $codMatriculaPeriodo, $codCertificante);
        return $myCertificado->estado;
    }

    function errores_migracion_certificados_1() {
        $conexion = $this->load->database('', true);
        $faltan = array();
        $conexion->select("*");
        $conexion->from("certificados_test.certificados_version1");
        if ($this->codigofilial == 93) {
            $conexion->where("certificados_test.certificados_version1.codfilial IN (9, 93)");
        } else {
            $conexion->where("certificados_test.certificados_version1.codfilial", $this->codigofilial);
        }

        $query = $conexion->get();
        $version_1 = $query->result_array();

        $conexion2 = $this->load->database($this->codigofilial, true);
        $asignados = 0;
        foreach ($version_1 as $certificado) {
            $matricula = new Vmatriculas($conexion2, $certificado['codmatri']);
            $cursocertificado = $certificado['codcurso'] == 22 || $certificado['codcurso'] == 30 || $certificado['codcurso'] == 95 ? 1 : $certificado['codcurso'];
            $cursocertificado = $certificado['codcurso'] == 2 || $certificado['codcurso'] == 20 ? 31 : $cursocertificado;
            $arrplancertificado = Vplanes_academicos::listarPlanes_academicos($conexion2, array('cod_curso' => $cursocertificado));
            $planescertificado = array();
            foreach ($arrplancertificado as $value) {
                $planescertificado[] = $value['codigo'];
            }
            $periodo = $certificado['codtipocertificado'] == 2 ? 2 : 1;
            $periodo = $certificado['codcurso'] == 31 && $certificado['horas'] == 549 ? 2 : $periodo;

            $conexion2->select("matriculas_periodos.*");
            $conexion2->from("matriculas_periodos");
            $conexion2->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
            $conexion2->where("matriculas.cod_alumno", $matricula->cod_alumno);
            $conexion2->where_in("matriculas.cod_plan_academico", $planescertificado);
            $conexion2->where("matriculas_periodos.estado <>", 'migrado');
            $conexion2->where("matriculas_periodos.cod_tipo_periodo", $periodo);
            $query = $conexion2->get();
            //echo $conexion->last_query($query);
            $datos = $query->result_array();
            echo '<pre>';
            print_r($certificado['nombre'] . ' ' . $certificado['apellido'] . ' ' . $certificado['documento'] . ' ' . $certificado['codcurso'] . ' ' . $certificado['horas']);
            print_r($datos);
            echo '</pre>';

            if (count($datos) > 0) {
                $data = array('cod_matricula_periodo' => $datos[0]['codigo']);
                print_r($data);
                $conexion->where('certificados_test.certificados_version1.id', $certificado['id']);
                $respuesta = $conexion->update('certificados_test.certificados_version1', $data);
                $asignados = $respuesta ? $asignados + 1 : $asignados;
            } else {
                $faltan[] = $certificado;
            }
        }
        echo 'Matriculas periodos asignadas ' . $asignados;
        echo 'Certificados sin matriculas ';
        print_r($faltan);
    }

    function errores_migracion_certificados_2() {
        $conexion = $this->load->database('', true);
        $conexion2 = $this->load->database($this->codigofilial, true);

        //TRAIGO LOS CERTIFICADOS DE LA VERSION 1 Y LOS COMPARO
        $conexion->select("*");
        $conexion->from("certificados_test.certificados_version1");
        if ($this->codigofilial == 93) {
            $conexion->where("certificados_test.certificados_version1.codfilial IN (9, 93)");
        } else {
            $conexion->where("certificados_test.certificados_version1.codfilial", $this->codigofilial);
        }

        $query = $conexion->get();
        $version_1 = $query->result_array();
        $certificadosSistValidados = array();
        $modificadosv1 = 0;

        foreach ($version_1 as $certificado) {
            if ($certificado['cod_matricula_periodo'] != NULL) {
                $arrcertificado = Vcertificados::listarCerfificados($conexion2, array('cod_matricula_periodo' => $certificado['cod_matricula_periodo']));
                if (count($arrcertificado) > 0) {

                    if (count($arrcertificado) > 1) {
                        //tiene dos cerfificantes
                        foreach ($arrcertificado as $value) {
                            $certificante = $certificado['es_certificacion'] == 1 ? 2 : 1;
                            if ($value['cod_certificante'] == $certificante) {
                                $certificadoSist = $value;
                            }
                        }
                    } else {
                        $certificadoSist = $arrcertificado[0];
                    }

                    if (count($arrcertificado) == 1 && $certificado['es_certificacion'] == 1 && $certificadoSist['cod_certificante'] != 2) {
                        //cambiar el certicante
                        echo'cambia certificante ';
                        print_r($certificadoSist);
                        $certificante = $certificado['es_certificacion'] == 1 ? 2 : 1;
                        $data = array('cod_certificante' => 2);
                        $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                        $respuesta = $conexion2->update('certificados', $data);
                        $certificadoSist['cod_certificante'] = $respuesta ? 2 : $certificante;
                    }
                    if ($certificado['emitido'] != 0 && $certificadoSist['estado'] != Vcertificados::getEstadoFinalizado()) {
                        //cambiar estado del certificado a finalizado
                        echo'cambia a finalizado ';
                        print_r($certificadoSist);
                        $data = array('estado' => Vcertificados::getEstadoFinalizado());
                        $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                        $conexion2->where('certificados.cod_certificante', $certificadoSist['cod_certificante']);
                        $respuesta = $conexion2->update('certificados', $data);

                        $objhistorico = new Vcertificados_estado_historico($conexion2);
                        $objhistorico->guardar($certificadoSist['cod_matricula_periodo'], $certificadoSist['cod_certificante'], Vcertificados::getEstadoFinalizado(), '19', date('Y-m-d h:m:i'), null, 'error migracion');
                        $modificadosv1 = $respuesta ? $modificadosv1 + 1 : $modificadosv1;
                    }
                    if ($certificado['emitido'] == 0 && $certificadoSist['estado'] != Vcertificados::getEstadoEnProceso()) {
                        //cambiar estado del certificado a en_proceso
                        echo'cambia a en proceso ';
                        print_r($certificadoSist);
                        $data = array('estado' => Vcertificados::getEstadoEnProceso());
                        $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                        $conexion2->where('certificados.cod_certificante', $certificadoSist['cod_certificante']);
                        $respuesta = $conexion2->update('certificados', $data);

                        $objhistorico = new Vcertificados_estado_historico($conexion2);
                        $objhistorico->guardar($certificado['cod_matricula_periodo'], $certificadoSist['cod_certificante'], Vcertificados::getEstadoEnProceso(), '19', date('Y-m-d h:m:i'), null, 'error migracion');
                        $modificadosv1 = $respuesta ? $modificadosv1 + 1 : $modificadosv1;
                    }
                    $certificadosSistValidados[] = $certificadoSist['cod_matricula_periodo'] . '-' . $certificadoSist['cod_certificante'];
                } else {
                    //insertar en el sistema
                    echo 'no esta en el sistema';
                    print_r($certificado);
                    $codcertificante = $certificado['es_certificacion'] == 1 ? 2 : 1;
                    $data = array(
                        'cod_matricula_periodo' => $certificado['cod_matricula_periodo'],
                        'cod_certificante' => $codcertificante,
                        'estado' => Vcertificados::getEstadoFinalizado(),
                        'fecha_hora' => date('Y-m-d h:m:i'),
                        'cod_usuario' => '19'
                    );

                    $respuesta = $conexion2->insert('certificados', $data);
                    if ($respuesta) {
                        $certificadosSistValidados[] = $certificado['cod_matricula_periodo'] . '-' . $codcertificante;
                    }
                }
            }
        }

        echo 'Modificados version1: ' . $modificadosv1;

        //TRAIGO LOS CERTIFICADOS DE LA VERSION 2 Y LOS COMPARO
        $conexion->select("*");
        $conexion->from("certificados_test.certificados_version2");
        if ($this->codigofilial == 93) {
            $conexion->where("certificados_test.certificados_version2.cod_filial IN (9, 93)");
        } else {
            $conexion->where("certificados_test.certificados_version2.cod_filial", $this->codigofilial);
        }

        $query = $conexion->get();
        $version_2 = $query->result_array();
        $modificadosv2 = 0;

        foreach ($version_2 as $certificado) {
            $conexion2->select("certificados.*");
            $conexion2->from("certificados");
            $conexion2->join("matriculas_periodos", "matriculas_periodos.codigo = certificados.cod_matricula_periodo");
            $conexion2->where("matriculas_periodos.cod_matricula", $certificado['cod_matricula']);
            $conexion2->where("matriculas_periodos.cod_tipo_periodo", $certificado['cod_tipo_periodo']);
            $query = $conexion2->get();
            $arrcertificado = $query->result_array();

            if (count($arrcertificado) > 0) {
                if (count($arrcertificado) > 1) {
                    //tiene dos cerfificantes
                    foreach ($arrcertificado as $value) {
                        $certificante = $certificado['cod_certificante'];
                        if ($value['cod_certificante'] == $certificante) {
                            $certificadoSist = $value;
                        }
                    }
                } else {
                    $certificadoSist = $arrcertificado[0];
                }
                if (count($arrcertificado) == 1 && $certificado['cod_certificante'] == 2 && $certificadoSist['cod_certificante'] != 2) {
                    //cambiar el certicante
                    echo'cambia certificante ';
                    print_r($certificadoSist);
                    $data = array('cod_certificante' => 2);
                    $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                    $respuesta = $conexion2->update('certificados', $data);
                    $certificadoSist['cod_certificante'] = $respuesta ? 2 : $certificadoSist['cod_certificante'];
                }
                if (($certificado['estado'] == 'emitido' || $certificado['estado'] == 'finalizado')) {

                    if ($certificadoSist['estado'] != Vcertificados::getEstadoFinalizado()) {
                        //cambiar estado del certificado a finalizado
                        echo'cambia a finalizado ';
                        print_r($certificadoSist);
                        $data = array('estado' => Vcertificados::getEstadoFinalizado());
                        $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                        $conexion2->where('certificados.cod_certificante', $certificadoSist['cod_certificante']);
                        $respuesta = $conexion2->update('certificados', $data);

                        $objhistorico = new Vcertificados_estado_historico($conexion2);
                        $objhistorico->guardar($certificadoSist['cod_matricula_periodo'], $certificadoSist['cod_certificante'], Vcertificados::getEstadoFinalizado(), '19', date('Y-m-d h:m:i'), null, 'error migracion');
                        $modificadosv2 = $respuesta ? $modificadosv2 + 1 : $modificadosv2;
                    }
                } elseif ($certificado['estado'] == 'cancelado') {
                    if ($certificadoSist['estado'] != Vcertificados::getEstadoCancelado()) {
                        //cambiar estado del certificado a en_proceso
                        echo'cambia a cancelado ';
                        print_r($certificadoSist);
                        $data = array('estado' => Vcertificados::getEstadoCancelado());
                        $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                        $conexion2->where('certificados.cod_certificante', $certificadoSist['cod_certificante']);
                        $respuesta = $conexion2->update('certificados', $data);

                        $objhistorico = new Vcertificados_estado_historico($conexion2);
                        $objhistorico->guardar($certificadoSist['cod_matricula_periodo'], $certificadoSist['cod_certificante'], Vcertificados::getEstadoCancelado(), '19', date('Y-m-d h:m:i'), null, 'error migracion');
                        $modificadosv2 = $respuesta ? $modificadosv2 + 1 : $modificadosv2;
                    }
                } else {
                    if ($certificadoSist['estado'] != Vcertificados::getEstadoEnProceso()) {
                        //cambiar estado del certificado a en_proceso
                        echo'cambia a en proceso ';
                        print_r($certificadoSist);
                        $data = array('estado' => Vcertificados::getEstadoEnProceso());
                        $conexion2->where('certificados.cod_matricula_periodo', $certificadoSist['cod_matricula_periodo']);
                        $conexion2->where('certificados.cod_certificante', $certificadoSist['cod_certificante']);
                        $respuesta = $conexion2->update('certificados', $data);

                        $objhistorico = new Vcertificados_estado_historico($conexion2);
                        $objhistorico->guardar($certificadoSist['cod_matricula_periodo'], $certificadoSist['cod_certificante'], Vcertificados::getEstadoEnProceso(), '19', date('Y-m-d h:m:i'), null, 'error migracion');
                        $modificadosv2 = $respuesta ? $modificadosv2 + 1 : $modificadosv2;
                    }
                }
                $certificadosSistValidados[] = $certificadoSist['cod_matricula_periodo'] . '-' . $certificadoSist['cod_certificante'];
            } else {
                //insertar en el sistema
                echo 'no esta en el sistema';
                print_r($certificado);
                $data = array(
                    'cod_matricula_periodo' => $certificado['cod_matricula_periodo'],
                    'cod_certificante' => $certificado['cod_certificante'],
                    'estado' => Vcertificados::getEstadoFinalizado(),
                    'fecha_hora' => date('Y-m-d h:m:i'),
                    'cod_usuario' => '19'
                );

                $respuesta = $conexion2->insert('certificados', $data);
                if ($respuesta) {
                    $certificadosSistValidados[] = $certificado['cod_matricula_periodo'] . '-' . $certificado['cod_certificante'];
                }
            }
        }
        echo 'Modificados version2: ' . $modificadosv2;
        $modificadosSis = 0;
        $todoscerticados = Vcertificados::listarCerfificados($conexion2, null, null, null, null, null, array('campo' => 'estado', 'valores' => array(Vcertificados::getEstadoFinalizado(), Vcertificados::getEstadoEnProceso())));
        foreach ($todoscerticados as $rowcertificado) {
            $dato = $rowcertificado['cod_matricula_periodo'] . '-' . $rowcertificado['cod_certificante'];
            if (!in_array($dato, $certificadosSistValidados)) {

                //cambiar a pendienteo cancelado dependiente de la matricula
                echo 'no esta arriba'; 
                print_r($dato);
                $matper = new Vmatriculas_periodos($conexion2, $rowcertificado['cod_matricula_periodo']);
                $estado = $matper->estado == 'inhabilitada' || $matper->estado == 'migrado' ? 'cancelado' : 'pendiente';

                $data = array('estado' => $estado);
                $conexion2->where('certificados.cod_matricula_periodo', $rowcertificado['cod_matricula_periodo']);
                $conexion2->where('certificados.cod_certificante', $rowcertificado['cod_certificante']);
                $respuesta = $conexion2->update('certificados', $data);

                $objhistorico = new Vcertificados_estado_historico($conexion2);
                $objhistorico->guardar($rowcertificado['cod_matricula_periodo'], $rowcertificado['cod_certificante'], $estado, '19', date('Y-m-d h:m:i'), null, 'error migracion');
                $modificadosSis = $respuesta ? $modificadosSis + 1 : $modificadosSis;
            }
        }
        echo 'Modificados sistema: ' . $modificadosSis;
    }

    function errores_migracion_certificados_vista() {
        $conexion = $this->load->database('', true);
        $conexion2 = $this->load->database($this->codigofilial, true);

        //TRAIGO LOS CERTIFICADOS DE LA VERSION 1 Y LOS COMPARO
        $conexion->select("*");
        $conexion->from("certificados_test.certificados_version1");
        if ($this->codigofilial == 93) {
            $conexion->where("certificados_test.certificados_version1.codfilial IN (9, 93)");
        } else {
            $conexion->where("certificados_test.certificados_version1.codfilial", $this->codigofilial);
        }

        $query = $conexion->get();
        $version_1 = $query->result_array();
        $certificadosSistValidados = array();
        $modificadosv1 = 0;

        foreach ($version_1 as $certificado) {
            if ($certificado['cod_matricula_periodo'] != NULL) {
                $arrcertificado = Vcertificados::listarCerfificados($conexion2, array('cod_matricula_periodo' => $certificado['cod_matricula_periodo']));
                if (count($arrcertificado) > 0) {

                    if (count($arrcertificado) > 1) {
                        //tiene dos cerfificantes
                        foreach ($arrcertificado as $value) {
                            $certificante = $certificado['es_certificacion'] == 1 ? 2 : 1;
                            if ($value['cod_certificante'] == $certificante) {
                                $certificadoSist = $value;
                            }
                        }
                    } else {
                        $certificadoSist = $arrcertificado[0];
                    }

                    if (count($arrcertificado) == 1 && $certificado['es_certificacion'] == 1 && $certificadoSist['cod_certificante'] != 2) {
                        //cambiar el certicante
                        echo'cambia certificante ';
                        print_r($certificadoSist);
                    }
                    if ($certificado['emitido'] != 0 && $certificadoSist['estado'] != Vcertificados::getEstadoFinalizado()) {
                        //cambiar estado del certificado a finalizado
                        echo'cambia a finalizado ';
                        print_r($certificadoSist);
                        $respuesta = true;
                        $modificadosv1 = $respuesta ? $modificadosv1 + 1 : $modificadosv1;
                    }
                    if ($certificado['emitido'] == 0 && $certificadoSist['estado'] != Vcertificados::getEstadoEnProceso()) {
                        //cambiar estado del certificado a en_proceso
                        echo'cambia a en proceso ';
                        print_r($certificadoSist);
                        $respuesta = true;
                        $modificadosv1 = $respuesta ? $modificadosv1 + 1 : $modificadosv1;
                    }
                    $certificadosSistValidados[] = $certificadoSist['cod_matricula_periodo'] . '-' . $certificadoSist['cod_certificante'];
                } else {
                    //insertar en el sistema
                    echo 'no esta en el sistema';
                    print_r($certificado);
                    $codcertificante = $certificado['es_certificacion'] == 1 ? 2 : 1;


                    $respuesta = true;
                    if ($respuesta) {
                        $certificadosSistValidados[] = $certificado['cod_matricula_periodo'] . '-' . $codcertificante;
                    }
                }
            }
        }

        echo 'Modificados version1: ' . $modificadosv1;

        //TRAIGO LOS CERTIFICADOS DE LA VERSION 2 Y LOS COMPARO
        $conexion->select("*");
        $conexion->from("certificados_test.certificados_version2");
        if ($this->codigofilial == 93) {
            $conexion->where("certificados_test.certificados_version2.cod_filial IN (9, 93)");
        } else {
            $conexion->where("certificados_test.certificados_version2.cod_filial", $this->codigofilial);
        }

        $query = $conexion->get();
        $version_2 = $query->result_array();
        $modificadosv2 = 0;

        foreach ($version_2 as $certificado) {
            $conexion2->select("certificados.*");
            $conexion2->from("certificados");
            $conexion2->join("matriculas_periodos", "matriculas_periodos.codigo = certificados.cod_matricula_periodo");
            $conexion2->where("matriculas_periodos.cod_matricula", $certificado['cod_matricula']);
            $conexion2->where("matriculas_periodos.cod_tipo_periodo", $certificado['cod_tipo_periodo']);
            $query = $conexion2->get();
            $arrcertificado = $query->result_array();

            if (count($arrcertificado) > 0) {
                if (count($arrcertificado) > 1) {
                    //tiene dos cerfificantes
                    foreach ($arrcertificado as $value) {
                        $certificante = $certificado['cod_certificante'];
                        if ($value['cod_certificante'] == $certificante) {
                            $certificadoSist = $value;
                        }
                    }
                } else {
                    $certificadoSist = $arrcertificado[0];
                }
                if (count($arrcertificado) == 1 && $certificado['cod_certificante'] == 2 && $certificadoSist['cod_certificante'] != 2) {
                    //cambiar el certicante
                    echo'cambia certificante ';
                    print_r($certificadoSist);
                    $respuesta = true;
                    $certificadoSist['cod_certificante'] = $respuesta ? 2 : $certificadoSist['cod_certificante'];
                }
                if (($certificado['estado'] == 'emitido' || $certificado['estado'] == 'finalizado')) {

                    if ($certificadoSist['estado'] != Vcertificados::getEstadoFinalizado()) {
                        //cambiar estado del certificado a finalizado
                        echo'cambia a finalizado ';
                        print_r($certificadoSist);
                        $respuesta = true;
                        $modificadosv2 = $respuesta ? $modificadosv2 + 1 : $modificadosv2;
                    }
                } elseif ($certificado['estado'] == 'cancelado') {
                    if ($certificadoSist['estado'] != Vcertificados::getEstadoCancelado()) {
                        //cambiar estado del certificado a en_proceso
                        echo'cambia a cancelado ';
                        print_r($certificadoSist);
                        $respuesta = true;
                        $modificadosv2 = $respuesta ? $modificadosv2 + 1 : $modificadosv2;
                    }
                } else {
                    if ($certificadoSist['estado'] != Vcertificados::getEstadoEnProceso()) {
                        //cambiar estado del certificado a en_proceso
                        echo'cambia a en proceso ';
                        print_r($certificadoSist);
                        $respuesta = true;
                        $modificadosv2 = $respuesta ? $modificadosv2 + 1 : $modificadosv2;
                    }
                }
                $certificadosSistValidados[] = $certificadoSist['cod_matricula_periodo'] . '-' . $certificadoSist['cod_certificante'];
            } else {
                //insertar en el sistema
                echo 'no esta en el sistema';
                print_r($certificado);

                $respuesta = true;
                if ($respuesta) {
                    $certificadosSistValidados[] = $certificado['cod_matricula_periodo'] . '-' . $certificado['cod_certificante'];
                }
            }
        }
        echo 'Modificados version2: ' . $modificadosv2;

        $modificadosSis = 0;
        $todoscerticados = Vcertificados::listarCerfificados($conexion2, null, null, null, null, null, array('campo' => 'estado', 'valores' => array(Vcertificados::getEstadoFinalizado(), Vcertificados::getEstadoEnProceso())));
        print_r($todoscerticados);
        foreach ($todoscerticados as $rowcertificado) {
            $dato = $rowcertificado['cod_matricula_periodo'] . '-' . $rowcertificado['cod_certificante'];
            if (!in_array($dato, $certificadosSistValidados)) {

                //cambiar a pendienteo cancelado dependiente de la matricula
                echo 'no esta arriba';
                print_r($dato);
                $modificadosSis = $respuesta ? $modificadosSis + 1 : $modificadosSis;
            }
        }
        echo 'Modificados sistema: ' . $modificadosSis;
    }


}
