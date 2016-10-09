<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model_matriculas
 * 
 * ...
 * 
 * @package model_matriculas
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
class Model_matriculas extends CI_Model {

    var $codigofilial = 0;
    var $codigo = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigofilial = $arg["filial"]["codigo"];
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
    }

    /**
     * retorna un objeto matricula.
     * @access public
     * @param array $arrFiltros filtros del control
     * @return array de respuesta datatable
     */
    public function listarMatriculas($arrFiltros, $muestraperiodo = 1, $idioma, $separador, $pasarLibre = false) {

        $conexion = $this->load->database($this->codigofilial, true);
        $arrCondindiciones = array();
        $this->load->helper('alumnos');

        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "cod_matricula" => $arrFiltros["sSearch"],
                "nombre_apellido" => $arrFiltros["sSearch"],
                "alumnos.codigo" => $arrFiltros["sSearch"],
                "fecha_emision" => $arrFiltros["sSearch"]
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

        $datos = Vmatriculas::listarMatriculaDataTable($conexion, $separador, $arrCondindiciones, $arrLimit, $arrSort);
//        die($conexion->last_query());
        $contar = Vmatriculas::listarMatriculaDataTable($conexion, $separador, $arrCondindiciones, "", "", true);
//echo "<pre>"; print_r($datos); echo "</pre>"; die();
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );

        $rows = array();
        $periodos = new Vtipos_periodos($conexion);

        foreach ($datos as $row) {
            $puedematricular = array();
            $rematricular = $this->periodosRematricular($row['cod_alumno'], $row["cod_plan_academico"], $puedematricular);
            $objCurso = new Vcursos($conexion, $row['cod_curso']);
            $nombre_curso = 'nombre_' . get_idioma();
            $nombre = $objCurso->$nombre_curso;
            $periodosmatricula = explode(',', $row["periodos_matriculada"]);

            if ($rematricular == 1) {
                foreach ($periodosmatricula as $rowperiodo) {
                    $nombrePeriodo = lang($periodos->getNombre($conexion, $rowperiodo));
                    $nombre.= ' (' . $nombrePeriodo . ')';
                }
            }
            $estadosperiodos = explode(',', $row["estados_periodos"]);
            $unico = array_unique($estadosperiodos);
            if ($row['estado'] == Vmatriculas::getEstadoPrematricula()){
                $estado = 'prematricula';
            } else {
                $estado = '';
                if (count($unico) == 1) {
                    $estado = $estadosperiodos[0];
                }
            }
            //si tiene mas de un plan el curso mostrar plan
            $condplanes = array('cod_curso' => $row['cod_curso']);
            $planescurso = Vplanes_academicos::listarPlanes_academicos($conexion, $condplanes);
            if (count($planescurso) > 1) {
                $objplan = new Vplanes_academicos($conexion, $row['cod_plan_academico']);
                $nombre.=' / ' . $objplan->nombre;
            }
            if ($pasarLibre) {
                $rows[] = array(
                    $row["cod_alumno"],
                    inicialesMayusculas($row["nombre_apellido"]),
                    $nombre,
                    $row["fecha_emision"]
                );
            } else {
                $rows[] = array(
                    $row["cod_alumno"],
                    $row["cod_matricula"],
                    inicialesMayusculas($row["nombre_apellido"]),
                    $nombre,
                    $row["fecha_emision"],
                    '',
                    count($periodosmatricula),
                    $rematricular,
                    $row['cod_plan_academico'],
                    $estado,
                    $row['observaciones']
                );
            }
        }
        $retorno['aaData'] = $rows;

        return $retorno;
    }

    /**
     * guarda una matricula/inscripcion
     * @access public
     * @param Array $arrMatricula todos los datos que salen del formulario aspirante
     * @return repuesta Guardar
     */
    public function guardarMatricula($arrMatricula, array $documentacion = null, array $materiales = null, $medioPagoCuotas = null) {        
        $this->load->model("Model_cobros", "", false, array("codigo_filial" => $this->codigofilial));
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $matricula = new Vmatriculas($conexion);
        $plan = new Vplanes_pago($conexion, $arrMatricula['ctacte']['cod_plan']);
        $periodo = $plan->periodo;
        $arg = array();
        $arg["codigo_filial"] = $this->codigofilial;
        $this->load->model("Model_configuraciones", "", false, $arg);
        $arrMatricula['ctacte']['periodo'] = $this->Model_configuraciones->getValorConfiguracion(null, 'PeriodoCtacte', null, $periodo);
        $arrMatricula['filial'] = $this->codigofilial;
        
        $matricula->matricular($arrMatricula, $this->Model_cobros);
        if ($arrMatricula["cupon"] && $arrMatricula['cupon'] <> ''){
            $matricula->asociarCupon($arrMatricula['cupon']);
        }
        if ($documentacion != null){
            $matricula->setDocumentacionEntragada($documentacion);
        }
        if ($materiales != null){
            $matricula->setMaterialEntregado($materiales);
        }
        if ($medioPagoCuotas != null){
            $matricula->set_medio_pago_cuotas($medioPagoCuotas);
        }
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        $arrRespuesta = array("cod_matricula" => $matricula->getCodigo(), "cod_alumno" => $matricula->cod_alumno);

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $arrRespuesta);
    }

    public function getMatricula($codigo_matricula) {
        $conexion = $this->load->database($this->codigofilial, true);
        return $matricula = new Vmatriculas($conexion, $codigo_matricula);
    }

    public function setCuponPromo($codigo_matricula, $cod_cupon) {//cupon es por matricula o por matricula periodo
        $conexion = $this->load->database();
        $matricula = new Vmatriculas($conexion, $codigo_matricula);
        $respuesta = $matricula->setCupon($cod_cupon, $this->codigofilial);
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    public function getAlumno($cod_matricula) {
        $conexion = $this->load->database($this->codigofilial, true);
        $matricula = new Vmatriculas($conexion, $cod_matricula);
        $objAlumno = $matricula->getAlumno();

        return $objAlumno;
    }

    public function getCurso($cod_matricula) {
        $conexion = $this->load->database($this->codigofilial, true);
        $matricula = new Vmatriculas($conexion, $cod_matricula);
        return $matricula->getCurso();
    }

    public function getReporteAlumnosActivos($filial) {
        $conexion = $this->load->database($filial, true);
        $arrMatriculas = Vmatriculas::getReporteAlumnosActivos($conexion);
        return $arrMatriculas;
    }

    public function getReporteDeserciones($filial, $fechaDesde = null) {
        $conexion = $this->load->database($filial, true);
        $arrDeserciones = Vmatriculas::getDeserciones($conexion, $fechaDesde);
        return $arrDeserciones;
    }

    public function getMatriculasFechaCantidad($filial, $fechaDesde = null) {
        $conexion = $this->load->database($filial, true);
        $arrDeserciones = Vmatriculas::getMatriculasFechaCantidad($conexion, $fechaDesde);
        return $arrDeserciones;
    }

    public function getReporteMatriculas($idFilial, $arrLimit = null, $arrSort = null, $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null, array $arrCursos = null, $estado = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vmatriculas::getReporteMatriculas($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta, $arrCursos, $estado);
        $registros = Vmatriculas::getReporteMatriculas($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta, $arrCursos, $estado);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    function periodosRematricular($codalumno, $codplan, &$puedematricular) {
        $conexion = $this->load->database($this->codigofilial, true);

        $alumnos = new Valumnos($conexion, $codalumno);
        $periodosmat = $alumnos->getMatriculasPeriodosPlanAcademico($codplan);
        $codperiodosmat = array();
        $periodosmatbaja = array();

        for ($a = 0; $a < count($periodosmat); $a++) {
            $codperiodosmat[$a] = $periodosmat[$a]['cod_tipo_periodo'];
            $periodosmatbaja[$a] = $periodosmat[$a]['estado'];
        }

        $plan = new Vplanes_academicos($conexion, $codplan);
        $periodosplan = $plan->getPeriodos(); //en que periodos se puede rematricular

        foreach ($periodosplan as $rowPeriodo) {
            $estamatriculado = FALSE;

            foreach ($periodosmat as $rowPerMat) {

                if ($rowPeriodo['cod_tipo_periodo'] == $rowPerMat['cod_tipo_periodo']) {
                    $estamatriculado = TRUE;
                }
            }

            if (!$estamatriculado) {
                if ($rowPeriodo['padre'] != null) {

                    $key = array_search($rowPeriodo['padre'], $codperiodosmat);

                    if ($key !== FALSE && $periodosmatbaja[$key] == 'habilitada') {
                        $rowPeriodo['nombre'] = lang($rowPeriodo['nombre']);
                        $puedematricular[] = $rowPeriodo;
                    }
                } else {
                    $rowPeriodo['nombre'] = lang($rowPeriodo['nombre']);
                    $puedematricular[] = $rowPeriodo;
                }
            }
        }

        if (count($puedematricular) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function listarMatriculasParaBaja(array $arrFiltros = null) {
        $conexion = $this->load->database($this->codigofilial, true);
        $this->load->helper('alumnos');
        $cantMesesConfig = Vconfiguracion::getValorConfiguracion($conexion, null, 'MesesBajaDeudores');
        $MesesVencida = Vconfiguracion::getValorConfiguracion($conexion, null, 'mesesVencidaBaja');
        if ($arrFiltros != null && $arrFiltros["iDisplayStart"] != "" && $arrFiltros["iDisplayLength"] != "") {
            $limitInf = $arrFiltros["iDisplayStart"];
            $limitCant = $arrFiltros["iDisplayLength"];
        } else {
            $limitInf = null;
            $limitCant = null;
        }
        $deudoresCtaCte = Vmatriculas::getMatriculasSugerenciaBajaGeneral($conexion, $cantMesesConfig, $MesesVencida, $limitInf, $limitCant, false);
        $contarDeudoresCtaCte = Vmatriculas::getMatriculasSugerenciaBajaGeneral($conexion, $cantMesesConfig, $MesesVencida, null, null, true);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contarDeudoresCtaCte,
            "iTotalDisplayRecords" => $contarDeudoresCtaCte,
            "aaData" => array()
        );
        $rows = array();
        foreach ($deudoresCtaCte as $row) {
            $objCurso = new Vcursos($conexion, $row['cod_curso']);
            $nombre_curso = 'nombre_' . get_idioma();
            $nombre = $objCurso->$nombre_curso;
            $nomCurso = $nombre;
            $nombreApellido = formatearNombreApellido($row['nombre'], $row['apellido']);
            $rows[] = array(
                $row['check'] = '',
                $row["cod_matricula"],
                $row['nombre_apellido'] = $nombreApellido,
                $row['nombre_curso'] = $nomCurso,
                $row['motivo'] = $row['motivo'] . " " . lang($row['tipo_motivo']),
                $row['prioridad']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    function bajaMatriculas($datos, CI_DB_mysqli_driver $conexion = null) {
        if ($conexion == null){
            $conexion = $this->load->database($this->codigofilial, true);
        }
        $conexion->trans_begin();
        $respuesta = '';
        foreach ($datos as $rowmatricula) {
            $objMatricula = new Vmatriculas($conexion, $rowmatricula['cod_matricula']);
            $arrmatriper = $objMatricula->getPeriodosMatricula(Vmatriculas_periodos::getEstadoHabilitada());
            foreach ($arrmatriper as $row) {
                $objmatper = new Vmatriculas_periodos($conexion, $row['codigo']);
                $objmatper->baja($rowmatricula['motivo'], $rowmatricula['comentario'], $rowmatricula['cod_usuario']);
                $respuesta['bajas'][] = $objmatper->getCodigo();
            }

            $objMatricula->bajaCtaCte();
        }

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    function baja_matricula_automatica() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        $fecha = date('Y-m-d');
        $fecha = strtotime('-6 month', strtotime($fecha));
        $fecha = date('Y-m-d', $fecha);
        $arrCondiciones = array(
            "fechavenc <" => $fecha,
            "pagado" => 0,
            "habilitado" => 1,
            "cod_concepto <>" => 3
        );
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $arrCtaCteVencidos = Vctacte::getCtaCte($conexion, true, $arrCondiciones);
            $arrPasados = array();
            foreach ($arrCtaCteVencidos as $ctacte) {
                if (($ctacte['cod_concepto'] == 1 || $ctacte['cod_concepto'] == 5) && !in_array($ctacte['concepto'], $arrPasados)) {
                    $codigoMatricula = $ctacte['concepto'];
                    $myMatricula = new Vmatriculas($conexion, $codigoMatricula);
                    if ($myMatricula->baja(null, 1, true)) {
                        $arrPasados[] = $codigoMatricula;
                    }
                }
            }
        }
    }

    /* esta function esta siendo accedida desde un web services NO BORRAR NI COMENTAR NI QUITAR NUEVAMENTE DE LUGAR */

    function getCodigoMatriculaPeriodo($codMatricula, $codTipoPeriodo) {
        $conexion = $this->load->database($this->codigofilial, true);
        $condiciones = array(
            "cod_matricula" => $codMatricula,
            "cod_tipo_periodo" => $codTipoPeriodo
        );
        $arrMatriculaPeriodo = Vmatriculas_periodos::listarMatriculas_periodos($conexion, $condiciones);
        return isset($arrMatriculaPeriodo[0]['codigo']) ? $arrMatriculaPeriodo[0]['codigo'] : null;
    }

//
//    //    function bajaMatriculasPlan($datos) {
////        $conexion = $this->load->database($this->codigofilial, true);
////        $conexion->trans_begin();
////
////
////        $objAlumno = new Valumnos($conexion, $datos['cod_alumno']);
////        $matriculas = $objAlumno->getMatriculasPlanAcademico($datos['cod_plan_academico'], Vmatriculas::getEstadoHabilitada());
////
////        foreach ($matriculas as $rowmatricula) {
////            $objMatricula = new Vmatriculas($conexion, $rowmatricula['codigo']);
////            $objMatricula->bajaMatriculasPeriodos($datos['motivo'], $datos['comentario'], $datos['cod_usuario']);
////        }
////
////        $estadotran = $conexion->trans_status();
////        if ($estadotran === FALSE) {
////            $conexion->trans_rollback();
////        } else {
////            $conexion->trans_commit();
////        }
////        $cod_matricula = array("custom" => $rowmatricula['codigo']);
////        return class_general::_generarRespuestaModelo($conexion, $estadotran, $cod_matricula);
////    }
////    public function getMatriculasAltaAlumno($cod_alumno, $cod_plan_academico) {
////        $conexion = $this->load->database($this->codigofilial, true);
////
////        $objAlumno = new Valumnos($conexion, $cod_alumno);
////        $matriculas = $objAlumno->getMatriculasPlanAcademico($cod_plan_academico, Vmatriculas::getEstadoInhabilitada());
////        $datos = array();
////        $objplan = new Vplanes_academicos($conexion, $cod_plan_academico);
////        $objCurso = $objplan->getCurso();
////        $nombre_curso = 'nombre_' . get_idioma();
////        $nombre = $objCurso->$nombre_curso;
////        $periodos = new Vtipos_periodos($conexion);
////        $periodosplan = $objplan->getPeriodos();
////        foreach ($matriculas as $rowinscripcion) {
////            $objmatricula = new Vmatriculas($conexion, $rowinscripcion['codigo']);
////            $periodos = $objmatricula->getPeriodosMatricula();
////            $i = 0;
////            $datos[$i] = array('matricula' => $objmatricula);
////            foreach ($periodos as $rowperiodo) {
////                $nombre.= count($periodosplan) > 1 ? ' (' . lang($rowperiodo['nombre']) . ')' : '';
////                $datos[$i]['periodos'] = array('cod_matricula_perido' => $rowperiodo['codigo'], 'fecha_emision' => $rowperiodo['fecha_emision'], 'estado' => $rowperiodo['estado'], 'cod_tipo_periodo' => $rowperiodo['cod_tipo_periodo'], 'nombre' => $nombre);
////                $i++;
////            }
////        }
////        return $datos;
////    }
////    
////    public function getMatriculasBajaAlumno($cod_alumno, $cod_plan_academico) {
//        $conexion = $this->load->database($this->codigofilial, true);
//
//        $objAlumno = new Valumnos($conexion, $cod_alumno);
//        $matriculas = $objAlumno->getMatriculasPlanAcademico($cod_plan_academico, Vmatriculas::getEstadoHabilitada());
//        $datos = array();
//        $objplan = new Vplanes_academicos($conexion, $cod_plan_academico);
//        $objCurso = $objplan->getCurso();
//        $nombre_curso = 'nombre_' . get_idioma();
//        $nombre = $objCurso->$nombre_curso;
//        $periodos = new Vtipos_periodos($conexion);
//        $periodosplan = $objplan->getPeriodos();
//        foreach ($matriculas as $rowinscripcion) {
//            $objmatricula = new Vmatriculas($conexion, $rowinscripcion['codigo']);
//            $periodos = $objmatricula->getPeriodosMatricula();
//            $i = 0;
//            $datos[$i] = array('matricula' => $objmatricula);
//            foreach ($periodos as $rowperiodo) {
//                $nombre.= count($periodosplan) > 1 ? ' (' . lang($rowperiodo['nombre']) . ')' : '';
//                $datos[$i]['periodos'] = array('cod_matricula_perido' => $rowperiodo['codigo'], 'fecha_emision' => $rowperiodo['fecha_emision'], 'estado' => $rowperiodo['estado'], 'cod_tipo_periodo' => $rowperiodo['cod_tipo_periodo'], 'nombre' => $nombre);
//                $i++;
//            }
//        }
//        return $datos;
//    }
//    
//    function altaMatriculasPlan($datos) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $conexion->trans_begin();
//        $respuesta = '';
//
//        $objAlumno = new Valumnos($conexion, $datos['cod_alumno']);
//        $matriculas = $objAlumno->getMatriculasPlanAcademico($datos['cod_plan_academico'], Vmatriculas::getEstadoInhabilitada());
//
//        foreach ($matriculas as $rowmatricula) {
//            $objMatricula = new Vmatriculas($conexion, $rowmatricula['codigo']);
//            $respuesta = $objMatricula->alta($datos['cod_usuario']);
//        }
//
//        $estadotran = $conexion->trans_status();
//        if ($estadotran === FALSE) {
//            $conexion->trans_rollback();
//        } else {
//            $conexion->trans_commit();
//        }
//        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
//    }
//    function getCtaCteBaja($arrmatriculas) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $this->load->helper('cuentacorriente');
//        $ctacte = array();
//        $cuentas = array();
//        foreach ($arrmatriculas as $cod_matricula) {
//            $objmatricula = new Vmatriculas($conexion, $cod_matricula);
//            $condiciones = array('habilitado' => 1);
//            $ctacte[] = $objmatricula->getCtaCte(true, $condiciones);
//        }
//        foreach ($ctacte as $arrcuentas) {
//            foreach ($arrcuentas as $row) {
//                $cuentas[] = $row;
//            }
//        }
//        formatearCtaCte($conexion, $cuentas);
//        $ctaCteOrder = Vctacte::ordenarCtaCte($cuentas);
//        return $ctaCteOrder;
//    }

    public function getComentarios($cod_alumno, $cod_plan_academico) {
        $conexion = $this->load->database($this->codigofilial, true, NULL, true);

        $arrCondiciones = array(
            "cod_alumno" => $cod_alumno,
            "cod_plan_academico" => $cod_plan_academico,
            "baja" => 0
        );
        $arrComentarios = Vmatriculas_comentarios::listarMatriculas_comentarios($conexion, $arrCondiciones);

        return $arrComentarios;
    }

    public function guardarComentario($comentario) {
        $conexion = $this->load->database($this->codigofilial, true);

        $conexion->trans_begin();
        $arrResp = array();
        $datoscomentario = array(
            'fecha_hora' => date("Y-m-d H:i:s"),
            'comentario' => $comentario['comentario'],
            'usuario_creador' => $comentario['cod_usuario'],
            "cod_alumno" => $comentario['cod_alumno'],
            "cod_plan_academico" => $comentario['cod_plan_academico'],
            "baja" => 0,
            "cod_matricula" => (isset($comentario['cod_matricula'])) ? $comentario['cod_matricula'] : ''
        );
        
        $matriculaComentario = new Vmatriculas_comentarios($conexion);
        $arrResp['obj'] = $matriculaComentario;
        $matriculaComentario->setMatriculas_comentarios($datoscomentario);

        $matriculaComentario->guardarMatriculas_comentarios();

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $arrResp);
    }

    public function bajaComentario($data_post) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $matriculaCometario = new Vmatriculas_comentarios($conexion, $data_post['codigo']);
        $matriculaCometario->baja = 1;
        $matriculaCometario->guardarMatriculas_comentarios();
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    //script para actualizar los comentarios de las matriculas.
    public function actualizarMatriculasComentarios() {
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        $estadotran = '';
        foreach ($arrFiliales as $key => $filial) {
            $conexion2 = $this->load->database($filial['codigo'], true);
            $condiciones = array(
                "observaciones <>" => ''
            );
            $arrMatriculas = Vmatriculas::listarMatriculas($conexion2, $condiciones);
            foreach ($arrMatriculas as $matricula) {
                $arrMatComentario = array(
                    "fecha_hora" => $matricula['fecha_emision'],
                    "comentario" => $matricula['observaciones'],
                    "usuario_creador" => $matricula['usuario_creador'],
                    "cod_alumno" => $matricula['cod_alumno'],
                    "cod_plan_academico" => $matricula['cod_plan_academico'],
                    "baja" => 0,
                    "cod_matricula" => $matricula['codigo']
                );
                $myMatricula_comentario = new Vmatriculas_comentarios($conexion2);
                $myMatricula_comentario->setMatriculas_comentarios($arrMatComentario);
                $estadotran = $myMatricula_comentario->guardarMatriculas_comentarios();
            }


            return class_general::_generarRespuestaModelo($conexion, $estadotran);
        }
    }

    public function getPlanesVigentesMatricula($cod_matricula) {
        $conexion = $this->load->database($this->codigofilial, true);
        $planes = array();
        $respuesta = array();
        $condicion = array('cod_matricula' => $cod_matricula);
        $arrperiodos = Vmatriculas_periodos::listarMatriculas_periodos($conexion, $condicion);
        $periodos = array();

        foreach ($arrperiodos as $value) {
            $periodos[$value['cod_tipo_periodo']] = $value['modalidad'];
        }
        $objmatricula = new Vmatriculas($conexion, $cod_matricula);
        $planacademico = new Vplanes_academicos($conexion, $objmatricula->cod_plan_academico);
        $arrplanes = $planacademico->getPlanesPago(true);
        foreach ($arrplanes as $key => $plan) {
            $periodosP = array();
            $objplan = new Vplanes_pago($conexion, $plan['codigo']);
            $periodosPlan = $objplan->getCursosPeriodosPlan();
            foreach ($periodosPlan as $value) {
                $periodosP[$value['cod_tipo_periodo']] = $value['modalidad'];
            }

            if ($periodosP == $periodos) {
                $arrplanes[$key]['nombre'].=$objplan->descon == '1' ? ' *' . lang('pierde_descuento') : '';
                $respuesta[] = $arrplanes[$key];
            }
        }

        return $respuesta;
    }

    public function getMatriculasAlumnosSelect($codigo_alumno) {
        $conexion = $this->load->database($this->codigofilial, true);
        $matriculas = Vmatriculas::getMatriculasSelect($conexion, $codigo_alumno);
        return $matriculas;

    }
    

}
