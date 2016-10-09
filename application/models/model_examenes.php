<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_examenes extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    /**
     * Recupera la lista de examenes parciales y recuperatorios segun filtro de datatable-
     * @access public
     * @param Array $arrFiltros filtro de lista examenes.
     * @return Array de  examenes.
     */
    public function listarExamenesParcialesDataTable($arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('comisiones');
        $wherein = array('PARCIAL', 'RECUPERATORIO_PARCIAL');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "nomComision" => $arrFiltros["sSearch"],
                "nomMateria" => $arrFiltros["sSearch"],
                "examenes.codigo" => $arrFiltros["sSearch"]
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
        $datos = Vexamenes::listarExamenesParcialesDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, "", $wherein);
        $contar = Vexamenes::listarExamenesParcialesDataTable($conexion, $arrCondiciones, '', '', TRUE, $wherein);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $tipoExamen = Vexamenes::getArrayExamenes(array($row['tipoexamen']));
            $rows[] = array(
                $row["codigo"],
                $row["nomMateria"],
                $row["tipoexamen"] = $tipoExamen[0]['nombre'],
                $row["nomComision"],
                formatearFecha_pais($row["fecha"]),
                $row["hora"],
                $row['horafin'],
                $row['cupo'],
                $row['cantinscriptos'],
                $row['estado'] = '',
                $row['baja']
            );
        }
        $retorno['aaData'] = $rows;

        return $retorno;
    }

    /**
     * Recupera la lista de examenes finales y recuperatorios segun filtro de datatable-
     * @access public
     * @param Array $arrFiltros filtro de lista examenes.
     * @return Array de  examenes.
     */
    public function listarExamenesFinalesDataTable($arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $wherein = array('FINAL', 'RECUPERATORIO_FINAL');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "nomMateria" => $arrFiltros["sSearch"],
                "examenes.codigo" => $arrFiltros['sSearch']
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
        $datos = Vexamenes::listarExamenesFinalesDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort, "", $wherein);
        $contar = Vexamenes::listarExamenesFinalesDataTable($conexion, $arrCondiciones, '', '', true, $wherein);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $tipoExamen = Vexamenes::getArrayExamenes(array($row['tipoexamen']));
            $rows[] = array(
                $row["codigo"],
                $row["nomMateria"],
                $row['tipoexamen'] = $tipoExamen[0]['nombre'],
                $row["nomComision"],
                formatearFecha_pais($row["fecha"]),
                $row["hora"],
                $row['horafin'],
                $row['cupo'],
                $row['cantinscriptos'],
                $row['estado'] = '',
                $row['baja'],
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getReporteExamenes($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null, $fechaDesde = null, $fechaHasta = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vexamenes::getReporteExamenes($conexion, $arrLimit, $arrSort, true, $search, $searchFields, $fechaDesde, $fechaHasta);
        $registros = Vexamenes::getReporteExamenes($conexion, $arrLimit, $arrSort, false, $search, $searchFields, $fechaDesde, $fechaHasta);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    /**
     * retorna array de examenes Final y recuperatorio con sus traducciones.
     * @access public
     * @return Array de examenes.
     */
    public function getExamenFinalRecFinal() {
        $id = array('FINAL', 'RECUPERATORIO_FINAL');
        $examenes = Vexamenes::getArrayExamenes($id);
        return $examenes;
    }
	
    /**
     * retorna array de examenes Parcial y recuperatorio con sus traducciones.
     * @access public
     * @return Array de examenes.
     */
    public function getExamenParcialRecParcal() {
        $id = array('PARCIAL', 'RECUPERATORIO_PARCIAL');
        $examenes = Vexamenes::getArrayExamenes($id);
        return $examenes;
    }

	/**
     * Retorna array de examenes parciales (no recuperatorios) de una materia
	 * para una comision.
	 * 
     * @access public
     * @return Array de Parciales.
     */
    public function getParcialesPasadosDeMateriaParaComision($cod_materia, $cod_comision) {
		$conexion = $this->load->database($this->codigo_filial, true);
		
		$antes_de_fecha = date("Y-m-d", time());
		
		return Vexamenes::getArrayExamenesWhere(
			$conexion,
			array(
				'tipoexamen' => 'PARCIAL',
				'materia' => $cod_materia,
				'baja' => 0,
				'cod_comision' => $cod_comision,
				'fecha <' => $antes_de_fecha
			)
		);
    }
	
    /**
     * retorna todos los inscriptos a un examen.
     * @access public
     * @param int $cod_examen codigo del examen.
     * @return Array de inscripctos.
     */
    public function getInscriptosExamenes($cod_examen, $separadorDecimal = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        if ($separadorDecimal == false) {
            $separadorDecimal = Vconfiguracion::getValorConfiguracion($conexion, null, 'SeparadorDecimal');
        }
        $examen = new Vexamenes($conexion, $cod_examen);
        $this->load->helper('alumnos');
        $this->load->helper('formatearfecha');
        $inscriptos = $examen->getInscriptosExamen();

        foreach ($inscriptos as $key => $inscripto) {
            $inscriptos[$key]['fecha'] = formatearFecha_pais($inscripto['fecha']);
            $inscriptos[$key]['fechadeinscripcion'] = formatearFecha_pais($inscripto['fechadeinscripcion']);
            $inscriptos[$key]['nombre_apellido'] = inicialesMayusculas($inscripto['nombre_apellido']);
            $objInscripcionExamen = new Vexamenes_estado_academico($conexion, $inscripto['codigo']);
            $condicion = array(
                'cod_inscripcion' => $inscripto['codigo']
            );
            $notaInscriptos = Vnotas_resultados::listarNotas_resultados($conexion, $condicion);
            foreach ($notaInscriptos as $tipo => $nota) {
                $nota0 = '0' . $separadorDecimal . '00';

                if (str_replace('.', $separadorDecimal, $nota['nota']) == $nota0) {
                    $notaInscriptos[$tipo]['nota'] = '';
                } else {
                    $notaInscriptos[$tipo]['nota'] = str_replace('.', $separadorDecimal, $nota['nota']);
                }
            }
            $inscriptos[$key]['notas'] = $notaInscriptos;
            $inscriptos[$key]['ausente'] = $objInscripcionExamen->estado;
        }
        return $inscriptos;
    }

    /**
     * retorna el curso del examen.
     * @access public
     * @param int $cod_examen codigo del examen.
     * @return Array de curso.
     */
    public function getCursoInscriptos($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $curso = '';
        switch ($objExamen->tipoexamen) {
            case "PARCIAL":
                $curso = $objExamen->getCursoInscripos();
                break;
            case "RECUPERATORIO_PARCIAL":
                $curso = $objExamen->getCursoInscripos();
                break;
            case "FINAL":
                $id = array($objExamen->tipoexamen);
                $curso = Vexamenes::getArrayExamenes($id);
                break;
            case "RECUPERATORIO_FINAL":
                $id = array($objExamen->tipoexamen);
                $curso = Vexamenes::getArrayExamenes($id);
                break;
        }
        return $curso;
    }

    /**
     * retorna los datos del examenes para inscribir matriculas
     * @access public
     * @param int $cod_examen codigo del examen.
     * @return Array de datos.
     */
    public function getDatosInscribirExamen($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $datosExamen = $objExamen->getDatosInscribirExamen();
        return $datosExamen;
    }

    /**
     * retorna los posibles alumnos a inscribir a examen
     * @access public
     * @param int $cod_examen codigo del examen.
     * @return Array de alumno.
     */
    public function getAlumnosInscribirExamen($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $cod_materia = $objExamen->materia;
        $alumnosInscribir = $objExamen->getAlumnosInscribirExamen($cod_materia);
        foreach ($alumnosInscribir as $key => $row) {
            $alumnosInscribir[$key]['estado'] = lang($alumnosInscribir[$key]['estado']);
        }
        return $alumnosInscribir;
    }

    /**
     * retorna los motivos de baja de examen
     * @access public
     * @return Array de motivos.
     */
    public function getMotivosBaja() {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $examenEstado = new Vexamenes_estado_historicos($conexion);
        return $examenEstado->getmotivos();
    }

    /**
     * retorna objeto examen.
     * @access public
     * @param int $cod_examen codigo del examen.
     * @return Objeto examen.
     */
    public function getExamen($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $examen = new Vexamenes($conexion, $cod_examen);
        return $examen;
    }

    /**
     * retorna el cambio de estado del examen.
     * @access public
     * @param int $cambioEstadoExamen datos del examen a dar de baja.
     * @return repuesta Guardar el cambio de estado.
     */
    public function cambioEstadoExamen($cambioEstadoExamen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $examen = new Vexamenes($conexion, $cambioEstadoExamen['cod_examen']);
        $estado = $examen->cambioEstadoExamen($cambioEstadoExamen);
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    /**
     * guarda examenes
     * @access public
     * @param Array $examen todos los datos que salen del formulario examen.
     * @return repuesta Guardar
     */
    public function guardarExamen($examen) {

        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $guardarExamen = '';
        $cod_examen = $examen['examen']['cod_examen'];
        $objexamen = new Vexamenes($conexion, $cod_examen);
        if (isset($examen['examen']['preinscripcionWeb']) && $examen['examen']['preinscripcionWeb'] == 'on') {
            $inscripcionWeb = '1';
        } else {
            $inscripcionWeb = '0';
        }
        $cod_comision = isset($examen['examen']['Comision']) ? $examen['examen']['Comision'] : null;
        $guardarExamen = array(
            'tipoexamen' => $examen['examen']['tipoExamen'],
            'hora' => $examen['examen']['horaInicio'],
            'horafin' => $examen['examen']['horaFin'],
            'fecha' => $examen['examen']['fecha'],
            'materia' => $examen['examen']['materia'],
            'observaciones' => $examen['examen']['observaciones'],
            'inscripcionweb' => $inscripcionWeb,
            'cupo' => $examen['examen']['cupo'],
            'baja' => 0,
            'cod_comision' => $cod_comision,
            'ver_campus'  => $examen['examen']['ver_campus']
        );

        if ( isset($examen['examen']['codigo_examen_padre']) ) {
            $guardarExamen['codigo_examen_padre'] = $examen['examen']['codigo_examen_padre'];
        }

        $objexamen->setExamenes($guardarExamen);
        
        $objexamen->guardarExamenes();
        if ($cod_examen != -1) {
            $objexamen->unSetsalones();
        }
        foreach ($examen['examen']['salon'] as $valor) {
            $arrSalones = json_decode($valor, TRUE);
            $cod_salon = $arrSalones['cod_salon'];
            $objexamen->setSalones($cod_salon);
        }
        if (isset($examen['examen']['profesor'])) {
            if ($cod_examen != -1) {
                $objexamen->unSetProfesores();
            }
            foreach ($examen['examen']['profesor'] as $valor) {
                $arrProfesores = json_decode($valor, true);
                $codprofesor = $arrProfesores['codprofesor'];
                $objexamen->setProfesores($codprofesor);
            }
        }
        if ($cod_examen == -1) {
            if ($objexamen->tipoexamen == 'PARCIAL' || $objexamen->tipoexamen == 'RECUPERATORIO_PARCIAL') {
                $inscriptos['cod_examen'] = $objexamen->getCodigo();
                $cod_materia = $examen['examen']['materia'];
                $codcomision = $examen['examen']['Comision'];
                $codigo_examen_padre = (isset($examen['examen']['codigo_examen_padre'])) ? $examen['examen']['codigo_examen_padre'] : null;

                $listaAlumnos = $this->getDetallesInscriptos($codcomision, $cod_materia, $objexamen->tipoexamen, $codigo_examen_padre);

                if ($listaAlumnos != '') {
                    foreach ($listaAlumnos as $alumno) {
                      // Inscribe alumnos
                        $inscriptos['inscriptos'][] = $alumno['cod_estado_academico'];
                    }
                    $this->guardarInscriptos($inscriptos, $conexion);
                }
            }
        }
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        error_log('DUMP '.$conexion->_error_message());
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /**
     * guarda inscriptos a examenes
     * @access public
     * @param Array $inscriptos todos los inscriptos a examenes.
     * @return repuesta Guardar
     */
    public function guardarInscriptos($inscriptos, $conexion = false, $web = false) {
        if ($conexion == false) {
            $conexion = $this->load->database($this->codigo_filial, true);
            $conexion->trans_start();
        }
        $inscribeweb = $web ? '1' : '0';
        if (isset($inscriptos['inscriptos']) && is_array($inscriptos['inscriptos']) && count($inscriptos['inscriptos']) > 0) {
            foreach ($inscriptos['inscriptos'] as $valor) {
                $arrInscribirAlumnos = array(
                    'cod_examen' => $inscriptos['cod_examen'],
                    'cod_estado_academico' => $valor,
                    'fechadeinscripcion' => date('Y-m-d'),
                    'estado' => 'pendiente',
                    'inscripcion_web' => $inscribeweb,
                    'baja' => '0'
                );
                $inscribirAlumnos = new Vexamenes_estado_academico($conexion);
                $inscribirAlumnos->setExamenes_estado_academico($arrInscribirAlumnos);
                $inscribirAlumnos->guardarExamenes_estado_academico();
            }
        }

        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    /**
     * guarda notas de los exameness
     * @access public
     * @param Array $arrGuardarNota todas las notas del examen
     * @param $notaAprobar nota de la configuracion con la que se apruba un examen
     * @return repuesta Guardar
     */
    public function guardarNotaExamen($arrGuardarNota, $arrConfigNotasExamen, $separadorDecimal) {
        $codigoUsuario = $this->session->userdata('codigo_usuario');
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $cod_examen = $arrGuardarNota['cod_examen'];
        $objExamen = new Vexamenes($conexion, $cod_examen);
        if (isset($arrGuardarNota['guardarnota']) && is_array($arrGuardarNota['guardarnota']) && count($arrGuardarNota['guardarnota']) > 0) {
            foreach ($arrGuardarNota['guardarnota'] as $valor) {
                $objExaMatricula = new Vexamenes_estado_academico($conexion, $valor['cod_inscripto']);
                if ($objExaMatricula->estado == 'ausente'){
                    $objExaMatricula->estado = 'pendiente';
                    $objExaMatricula->guardarExamenes_estado_academico();
                }
                $cod_estado_academico = $objExaMatricula->cod_estado_academico;
                $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);
                foreach ($valor['notas'] as $key => $nota) {
                    $porcentaje_aprobado_nota = $this->porcentaje_nota_aprobada($nota, $arrConfigNotasExamen, $separadorDecimal);
                    $objNotaResultado = new Vnotas_resultados($conexion, $valor['cod_inscripto'], $key);
                    $arrayGuardarNota = '';
                    if ($arrConfigNotasExamen['formato_nota'] == 'numerico') {
                        $arrayGuardarNota = array(
                            "nota" => $nota == '' ? 0.00 : str_replace($separadorDecimal, '.', $nota),
                            "porcentaje_aprobado" => $porcentaje_aprobado_nota
                        );
                    } else {
                        $arrayGuardarNota = array(
                            "nota" => $nota == '' ? '' : str_replace($separadorDecimal, '.', $nota)
                        );
                    }

                    $objNotaResultado->setNotas_resultados($arrayGuardarNota);
                    $objNotaResultado->guardarNotas_resultados();
                }
                $estado = '';
                $res = '';
                $tipoExamen = $objExamen->tipoexamen;
                switch ($arrConfigNotasExamen['formato_nota']) {
                    case 'numerico':
                        if ($tipoExamen == 'FINAL' || $tipoExamen == 'RECUPERATORIO_FINAL') {
                            if (floatval($valor['notas']['definitivo']) >= $arrConfigNotasExamen['nota_aprueba_final'] && $valor['notas']['definitivo'] != '') {
                                $res = $myEstadoAcademico->guardarCambioEstado(Vestadoacademico::getEstadoAprobado(), $codigoUsuario);
                                $estado = 'aprobado';
                            } else if ($valor['notas']['definitivo'] != ''){
//                                $estadoAnterior = $myEstadoAcademico->getEstadoAnterior();
//                                $res = $myEstadoAcademico->guardarCambioEstado($estadoAnterior, $codigoUsuario);
                                $estado = 'reprobado';
                            }
                            if (isset($res['codigo']) && !$res['codigo'])
                                return $res; 
                            
                        } else {
                            if ($valor['notas']['definitivo'] >= $arrConfigNotasExamen['nota_aprueba_parcial']) {
                                $estado = 'aprobado';
                            } else {
                                $estado = 'reprobado';
                            }
                        }
                        if ($valor['notas']['definitivo'] != '') {
                            $objExaMatricula->cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado);
                        } else if ($objExaMatricula->estado == 'reprobado' || $objExaMatricula->estado == 'aprobado'){
                            $objExaMatricula->estado = 'pendiente';
                            $objExaMatricula->guardarExamenes_estado_academico();
                        }

                        break;
                    case 'alfabetico':
                        if ($tipoExamen == 'FINAL' || $tipoExamen == 'RECUPERATORIO_FINAL') {
                            $keyNota_ApruebaFinal = array_search($arrConfigNotasExamen['nota_aprueba_final'], $arrConfigNotasExamen['array_notas']);
                            $keyNota_definitiva = array_search($valor['notas']['definitivo'], $arrConfigNotasExamen['array_notas']);

                            if ($keyNota_definitiva >= $keyNota_ApruebaFinal) {
                                $res = $myEstadoAcademico->guardarCambioEstado(Vestadoacademico::getEstadoAprobado(), $codigoUsuario);
                                $estado = 'aprobado';
                            } else {
                                $estadoAnterior = $myEstadoAcademico->getEstadoAnterior();
                                $res = $myEstadoAcademico->guardarCambioEstado($estadoAnterior, $codigoUsuario);
                                $estado = 'reprobado';
                            }
                        } else {
                            $keyNota_ApruebaParcial = array_search($arrConfigNotasExamen['nota_aprueba_parcial'], $arrConfigNotasExamen['array_notas']);
                            $keyNota_definitiva = array_search($valor['notas']['definitivo'], $arrConfigNotasExamen['array_notas']);
                            if ($keyNota_definitiva >= $keyNota_ApruebaParcial) {
                                $estado = 'aprobado';
                            } else {
                                $estado = 'reprobado';
                            }
                        }
                        if ($valor['notas']['definitivo'] != '') {
                            $objExaMatricula->cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado); //cambio el estado a examenes_estado_academico $estado variable que se modifica segun las distintas clausulas
                        } else if ($objExaMatricula->estado == 'reprobado' || $objExaMatricula->estado == 'aprobado'){
                            $objExaMatricula->estado = 'pendiente';
                            $objExaMatricula->guardarExamenes_estado_academico();
                        }
                        break;
                }
                if (isset($valor['ausente'])) {
                    $estado = 'ausente';
                    $objExaMatricula->cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado);
                }
            }
        }
        //die($conexion->last_query());
        //die(print_r($objExaMatricula));
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function porcentaje_nota_aprobada($nota, $arrConfigNotasExamen, $separadorDecimal) {
        $porcentaje_aprobado = '';
        if ($nota != '') {
            $arrayNotasConfiguracion = array();
            switch ($arrConfigNotasExamen['formato_nota']) {
                case 'numerico':
                    $arrNota = explode($separadorDecimal, $nota);
                    $j = 1;
                    for ($i = $arrConfigNotasExamen['numero_desde']; $i <= $arrConfigNotasExamen['numero_hasta']; $i++) {
                        $arrayNotasConfiguracion[$j] = $i;
                        $j++;
                    }
                    $key = array_keys($arrayNotasConfiguracion, $arrNota[0]);
                    $keyNota = $key[0];
                    if (count($arrNota) > 1) {
                        $valor_nota = $keyNota . '.' . $arrNota[1];
                    } else {
                        $valor_nota = $keyNota;
                    }
                    $porcentaje_aprobado = $valor_nota * 100 / count($arrayNotasConfiguracion);
                    break;

                case 'alfabetico':
                    break;
            }
        }
        return $porcentaje_aprobado;
    }

    /**
     * guarda la baja de matriculas de un examen.
     * @access public
     * @param Array $cod_inscripcion codigo de inscripcion que se dio de baja al examen.
     * @return repuesta Guardar
     */
    public function bajaMatriculaExamen($cod_inscripcion) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objExamenEstadoAcademico = new Vexamenes_estado_academico($conexion, $cod_inscripcion);
        $objExamenEstadoAcademico->estado = 'baja';
        $retorno = $objExamenEstadoAcademico->guardarExamenes_estado_academico();
        return class_general::_generarRespuestaModelo($conexion, $retorno);
    }

    /**
     * guarda notas de los exameness
     * @access public
     * @param Array $arrGuardarNota todas las notas de los examenes de un alumno
     * @param $notaAprobar nota de la configuracion con la que se apruba un examen
     * @return repuesta Guardar
     */
    public function guardarNotaAlumno($guardarnotas, $arrConfigNotasExamen, $separadorDecimal) {
        $codigoUsuario = $this->session->userdata('codigo_usuario');
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        foreach ($guardarnotas['guardarnota'] as $guardarnota) {
            $cod_examen = $guardarnota['codigoExamen'];
            $objExamen = new Vexamenes($conexion, $cod_examen);
            $cod_inscripcion = $guardarnota['codInscripcion'];
            $tipoExamen = $objExamen->tipoexamen;
            $objExamenInscripcion = new Vexamenes_estado_academico($conexion, $cod_inscripcion);
            $cod_estado_academico = $objExamenInscripcion->cod_estado_academico;
            $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);
            foreach ($guardarnota['notas'] as $key => $nota) {
                $objNotaResultado = new Vnotas_resultados($conexion, $guardarnota['codInscripcion'], $key);
                $porcentaje_aprobado_nota = $nota != '' ? $this->porcentaje_nota_aprobada($nota, $arrConfigNotasExamen, $separadorDecimal) : 0;
                $notasResultado = '';
                if ($arrConfigNotasExamen['formato_nota'] == 'numerico') {
                    $notasResultado = array(
                        "nota" => $nota == '' ? '' : str_replace($separadorDecimal, '.', $nota),
                        "porcentaje_aprobado" => $porcentaje_aprobado_nota
                    );
                } else {
                    $notasResultado = array(
                        "nota" => $nota == '' ? '' : $nota
                    );
                }
                $objNotaResultado->setNotas_resultados($notasResultado);
                $objNotaResultado->guardarNotas_resultados();
            }

            $estado = '';
            switch ($arrConfigNotasExamen['formato_nota']) {
                case 'numerico':
                    if ($tipoExamen == 'FINAL' || $tipoExamen = 'RECUPERATORIO_FINAL') {
                        if ($guardarnota['notas']['definitivo'] >= $arrConfigNotasExamen['nota_aprueba_final']) {
                            $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);
                            $myEstadoAcademico->guardarCambioEstado(Vestadoacademico::getEstadoAprobado(), $codigoUsuario);
                            $estado = 'aprobado';
                        } else {
//                            $estadoAnterior = $myEstadoAcademico->getEstadoAnterior();
//                            $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);
//                            $myEstadoAcademico->guardarCambioEstado($estadoAnterior, $codigoUsuario);
                            $estado = 'reprobado';
                        }
                    } else {
                        if ($guardarnota['notas']['definitivo'] >= $arrConfigNotasExamen['nota_aprueba_parcial']) {
                            $estado = 'aprobado';
                        } else {
                            $estado = 'reprobado';
                        }
                    }
                    if ($guardarnota['notas']['definitivo'] != '') {
                        $objExamenInscripcion->cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado); //cambio el estado a examenes_estado_academico $estado variable que se modifica segun las distintas clausulas
                    }

                    break;

                case 'alfabetico':
                    if ($tipoExamen == 'FINAL' || $tipoExamen = 'RECUPERATORIO_FINAL') {
                        $keyNota_ApruebaFinal = array_search($arrConfigNotasExamen['nota_aprueba_final'], $arrConfigNotasExamen['array_notas']);
                        $keyNota_definitiva = array_search($guardarnota['notas']['definitivo'], $arrConfigNotasExamen['array_notas']);

                        if ($keyNota_definitiva >= $keyNota_ApruebaFinal) {
                            $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);
                            $myEstadoAcademico->guardarCambioEstado(Vestadoacademico::getEstadoAprobado(), $codigoUsuario);
                            $estado = 'aprobado';
                        } else {
                            $estadoAnterior = $myEstadoAcademico->getEstadoAnterior();
                            $myEstadoAcademico = new Vestadoacademico($conexion, $codigoUsuario);
                            $myEstadoAcademico->guardarCambioEstado($conexion, $codigoUsuario);
                            $estado = 'reprobado';
                        }
                    } else {
                        $keyNota_ApruebaParcial = array_search($arrConfigNotasExamen['nota_aprueba_parcial'], $arrConfigNotasExamen['array_notas']);
                        $keyNota_definitiva = array_search($guardarnota['notas']['definitivo'], $arrConfigNotasExamen['array_notas']);
                        if ($keyNota_definitiva >= $keyNota_ApruebaParcial) {
                            $estado = 'aprobado';
                        } else {
                            $estado = 'reprobado';
                        }
                    }
                    if ($guardarnota['notas']['definitivo'] != '') {
                        $objExamenInscripcion->cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado);
                    }
                    break;
            }
            if (isset($guardarnota['ausente'])) {
                $estado = 'ausente';
                $objExamenInscripcion->cambiarEstadoExamen($cod_examen, $cod_estado_academico, $estado);
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

    public function getSalonesExamen($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $salonesExamen = $objExamen->getSalonesExamen($cod_examen);
        return $salonesExamen;
    }

    public function getProfesoresExamen($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $profesoresExamen = $objExamen->getProfesoresExamen($cod_examen);
        return $profesoresExamen;
    }

    public function getComisionCursoExamenParcial($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $comisionCursoExamen = $objExamen->getComisionCursoExamenParcial($cod_examen);
        return $comisionCursoExamen;
    }

    public function getDetallesInscriptos($cod_comision, $cod_materia, $tipo_examen = '', $cod_examen_padre = null) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $matriculasInscriptas = Vmatriculas_inscripciones::getInscripcionesMateriaComision($conexion, $cod_comision, $cod_materia, false, $tipo_examen, $cod_examen_padre);
        $this->load->helper('alumnos');
        $detallesAlumnos = '';
        foreach ($matriculasInscriptas as $matriculaInscripta) {
            $objMatricula = new Vmatriculas($conexion, $matriculaInscripta['cod_matricula']);
            $alumno = $objMatricula->getAlumno();
            $nombre = inicialesMayusculas($alumno->nombre);
            $apellido = inicialesMayusculas($alumno->apellido);
            $detallesAlumnos[] = array('codigo' => $matriculaInscripta['cod_matricula'],
                'nombre_apellido' => formatearNombreApellido($nombre, $apellido),
                'cod_estado_academico' => $matriculaInscripta['cod_estado_academico']
            );
        }
        return $detallesAlumnos;
    }

    public function recordatorioExamenes() {
        $conexion = $this->load->database("default", true);
        $arrFiliales = Vfiliales::listarFiliales($conexion, array("version_sistema" => 2));
        foreach ($arrFiliales as $filial) {
            $conexion = $this->load->database($filial['codigo'], true);
            $conexion->trans_begin();
            $configuracion = Vconfiguracion::getValorConfiguracion($conexion, null, 'ConfiguracionAlertaExamenes');
            $examenes = array();
            $hayinscriptos = false;
            foreach ($configuracion as $tipoexamen) {
                if ($tipoexamen['baja'] == '0') {
                    $examenes[$tipoexamen['tipo']] = Vexamenes::getExamenesDictarse($conexion, $tipoexamen['valor'] . ' ' . $tipoexamen['unidadTiempo'], $tipoexamen['tipo']);
                }
            }
            foreach ($examenes as $key => $rowexamenes) {
                for ($i = 0; $i < count($rowexamenes); $i++) {
                    $objexamen = new Vexamenes($conexion, $rowexamenes[$i]['codigo']);
                    $alumnos = array();
                    $alumnos = $objexamen->getInscriptosExamen();
                    if (count($alumnos) > 0) {
                        $hayinscriptos = true;
                    }

                    if ($hayinscriptos) {
                        //$myTemplate = new Vtemplates($conexion, 61); //ver id de templete cuando arme Aquiles
                        $html = $myTemplate->html;
                        $objAlerta = new Valertas($conexion);
                        $alerta = array(
                            'tipo_alerta' => 'recordatorio_examen',
                            'fecha_hora' => date("Y-m-d H:i:s"),
                            'mensaje' => $html
                        );

                        $objAlerta->setAlertas($alerta);
                        $objAlerta->guardarAlertas();
                        $objAlerta->setAlertaConfiguracion('titulo', 'Recordatorio examen ' . $key);
                        $objAlerta->setAlertaConfiguracion('cod_examen', $rowexamenes[$i]['codigo']);
                        foreach ($alumnos as $alumno) {
                            $objAlerta->setAlumnosComunicado($alumno['cod_alumno']);
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
        }
    }

    public function getTiposExamenes() {
        $tiposExamenes = Vexamenes::getArrayExamenes();
        return $tiposExamenes;
    }

    public function enviarRecordatorioExamen($alerta, $objalerta, CI_DB_mysqli_driver $conexion = null, &$comentario = null) {
        if ($conexion == null) {
            $conexion = $this->load->database($this->codigo_filial, true);
        }
        $objalerta = new Valertas($conexion, $alerta['codigo']);
        $confalerta = $objalerta->getAlertaConfiguracion();
        foreach ($confalerta as $value) {
            if ($value['key'] == 'titulo') {
                $asunto = $value['valor'];
            }
        }
        $cuerpomail = $alerta['mensaje'];
        maquetados::desetiquetarAlumnos($conexion, $alerta['cod_alumno'], $cuerpomail);
        maquetados::desetiquetarDatosFilial($conexion, null, $cuerpomail, $this->codigo_filial);
        maquetados::desetiquetarIdioma($cuerpomail, true);
        $objalumno = new Valumnos($conexion, $alerta['cod_alumno']);
        $this->email->from('noreply@iga-la.net', 'iga noreply');
        $this->email->to($objalumno->email);
        $this->email->subject($asunto);
        $this->email->message($cuerpomail);
        $respuesta = $this->email->send();
        if (!$respuesta) {
            $comentario = $this->email->print_debugger();
        }
        $this->email->clear();
        return $respuesta;
    }

    public function getMateriaExamen($cod_examen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('getidioma');
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $objMateria = new Vmaterias($conexion, $objExamen->materia);
        $nombre = 'nombre_' . get_idioma();
        return $objMateria->$nombre;
    }

    public function getExamenesAlumno($cod_alumno, $filial = null) {
        $this->load->helper('formatearFecha');
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->join("examenes", "examenes.codigo = examenes_estado_academico.cod_examen");
        $conexion->join("estadoacademico", "estadoacademico.codigo = examenes_estado_academico.cod_estado_academico");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->select("examenes.*");
        $conexion->join("general.materias", "general.materias.codigo = examenes.materia");
        $conexion->select("general.materias.nombre_pt,general.materias.nombre_es,general.materias.nombre_in");
        $condiciones = array("cod_alumno" => $cod_alumno);
        $examenes = Vexamenes_estado_academico::listarExamenes_estado_academico($conexion, $condiciones);
        foreach ($examenes as $key => $examen) {
            $examenes[$key]['fechaFormateada'] = formatearFecha_pais($examen['fecha'], '', $filial);
            $examenes[$key]['fechaInscripcionFormateada'] = formatearFecha_pais($examen['fechadeinscripcion'], '', $filial);
        }
        return $examenes;
    }

    public function listarInscriptosExamenDataTable($cod_examen, $arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
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
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }
        $datos = Vexamenes::listarInscriptosExamen($conexion, $cod_examen, $arrCondiciones, $arrLimit, $arrSort, false);
        $contar = Vexamenes::listarInscriptosExamen($conexion, $cod_examen, $arrCondiciones, "", "", true);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row['codigo'],
                $row['cod_matricula'],
                $row['nombre_apellido'],
                formatearFecha_pais($row['fechadeinscripcion']),
                $row['check'] = '',
                $row['estado']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function listarAlumnosInscribirDataTable($cod_examen, $arrFiltros, $comision = false) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
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
        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {
            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
            $datos = '';
            $contar = '';
            $myExamen = new Vexamenes($conexion, $cod_examen);
            if ($myExamen->tipoexamen == 'PARCIAL') {
                $arrCursoComision = $myExamen->getComisionCursoExamenParcial();
                $cod_comision = $arrCursoComision[0]['cod_comision'];
                
                $datos = Vexamenes::listarAlumnosParcialMateriaComision($conexion, $cod_examen, $myExamen->materia, $cod_comision, $arrCondiciones, $arrLimit, $arrSort, false);
                $contar = Vexamenes::listarAlumnosParcialMateriaComision($conexion, $cod_examen, $myExamen->materia, $cod_comision, $arrCondiciones, "", "", true);

            } elseif ($myExamen->tipoexamen == 'RECUPERATORIO_PARCIAL') {
                $arrCursoComision = $myExamen->getComisionCursoExamenParcial();
                $cod_comision = $arrCursoComision[0]['cod_comision'];
                
                $datos = Vexamenes::listarAlumnosInscribirRecuperatorioParcialMateriaComision($conexion, $cod_examen, $myExamen->materia, $cod_comision, $arrCondiciones, $arrLimit, $arrSort, false);
                $contar = Vexamenes::listarAlumnosInscribirRecuperatorioParcialMateriaComision($conexion, $cod_examen, $myExamen->materia, $cod_comision, $arrCondiciones, "", "", true);
            
            } else {
                $datos = Vexamenes::listarAlumnosInscribirDataTable($conexion, $cod_examen, $myExamen->tipoexamen, $myExamen->materia, $arrCondiciones, $arrLimit, $arrSort, false, $comision);
                $contar = Vexamenes::listarAlumnosInscribirDataTable($conexion, $cod_examen, $myExamen->tipoexamen, $myExamen->materia, $arrCondiciones, "", "", true, $comision);
            }
            $retorno = array(
                "sEcho" => $arrFiltros["sEcho"],
                "iTotalRecords" => $contar,
                "iTotalDisplayRecords" => $contar,
                "aaData" => array()
            );
            $rows = array();

            foreach ($datos as $row) {
                if ($myExamen->tipoexamen == 'FINAL' || $myExamen->tipoexamen == 'RECUPERATORIO_FINAL') {
                    $nombre = $row['noPuedeInscribir'] == 1 ? $row['nombre_apellido'] . ' ' . lang('no_puede_inscribirse_examen') : $row['nombre_apellido'];
                } else {
                    $nombre = $row['nombre_apellido'];
                }
                $noPuedeInscribir = $myExamen->tipoexamen == 'PARCIAL' || $myExamen->tipoexamen == 'RECUPERATORIO_PARCIAL' ? 0 : $row['noPuedeInscribir'];
                $rows[] = array(
                    $row['cod_estado_academico'],
                    $row['check'] = "",
                    $row['cod_matricula'],
                    $row['nombre_apellido'] = $nombre,
                    lang($row['estado']),
                    //$row['rindio'] = '',
                    $row['nomComision'],
                    $row['noPuedeInscribir'] = $noPuedeInscribir
                );
            }

            if ($myExamen->baja == 0) {
                $retorno['aaData'] = $rows;

            } else {
                $retorno = array(
                    "sEcho" => $arrFiltros["sEcho"],
                    "iTotalRecords" => 0,
                    "iTotalDisplayRecords" => 0,
                    "aaData" => array()
                );
            }
            return $retorno;
        }

    }

    public function getResultadosInscripcion($cod_inscripcion) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array("cod_inscripcion" => $cod_inscripcion);
        return Vnotas_resultados::listarNotas_resultados($conexion, $condiciones);
    }

    public function getProfesoresMateriaHorarios($cod_materia) {
        $conexion = $this->load->database($this->codigo_filial, true);

        $profesoresMateriaHorarios = Vhorarios::getProfesoresMateriaHorarios($conexion, $cod_materia);
        return $profesoresMateriaHorarios;
    }

    //Funcion que3 se corre para actualizar el porcetaje de aprobado de la tabla notas_resultados.
    public function scriptUpdatePorcentaje_aprobados($separadorDecimal) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrayNota = array('nota' => 1);
        $conexion->where('notas_resultados.nota < 1 and notas_resultados.nota > 0');
        $conexion->update('notas_resultados', $arrayNota);
        $arrayNotas = Vnotas_resultados::listarNotas_resultados($conexion);
        $configuracionNotas = '{"formato_nota":"numerico","numero_desde":"1","numero_hasta":"10","nota_aprueba_final":"6","nota_aprueba_parcial":"4"}';
        $arrConfiguracion = json_decode($configuracionNotas, true);
        $resultado = '';
        foreach ($arrayNotas as $valor) {
            $porcentaje_aprobado = '';
            if ($valor['nota'] != '0.00' and $valor['nota'] != '') {
                $porcentaje_aprobado = $this->porcentaje_nota_aprobada($valor['nota'], $arrConfiguracion, '.');
            } else {
                if ($valor['nota'] == '') {
                    $porcentaje_aprobado = '';
                } else {
                    $porcentaje_aprobado = 0;
                }
            }
            $resultado = Vnotas_resultados::updateNotasResultados($conexion, $valor['cod_inscripcion'], $valor['tipo_resultado'], $porcentaje_aprobado);
        }
        return $resultado;
    }

    /* Inicio Ticket 747 - Actualizar estado de certificado */
    public function actualizarCertificado($arrGuardarNota, $arrConfigNotasExamen) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_start();
        $cod_examen = $arrGuardarNota['cod_examen'];
        $objExamen = new Vexamenes($conexion, $cod_examen);

        foreach ($arrGuardarNota['guardarnota'] as $valor) {
            $objExaMatricula = new Vexamenes_estado_academico($conexion, $valor['cod_inscripto']);
            $cod_estado_academico = $objExaMatricula->cod_estado_academico;
            $myEstadoAcademico = new Vestadoacademico($conexion, $cod_estado_academico);

            $tipoExamen = $objExamen->tipoexamen;
            switch ($arrConfigNotasExamen['formato_nota']) {
                case 'numerico':
                    if ($tipoExamen == 'FINAL' || $tipoExamen == 'RECUPERATORIO_FINAL') {
                        $conexiones = $this->load->database($this->codigo_filial, true);
                        $objcertificado = new Vcertificados($conexiones, $myEstadoAcademico->cod_matricula_periodo, 1);
                        $objcertificado->cambiarEstadoCertificadoIGA();
                    }

                    break;
                case 'alfabetico':
                    if ($tipoExamen == 'FINAL' || $tipoExamen == 'RECUPERATORIO_FINAL') {
                        $conexiones = $this->load->database($this->codigo_filial, true);
                        $objcertificado = new Vcertificados($conexiones, $myEstadoAcademico->cod_matricula_periodo, 1);
                        $objcertificado->cambiarEstadoCertificadoIGA();
                    }

                    break;
            }

        }
        $estadotran = $conexion->trans_status();
        $conexion->trans_complete();
    }
    /* Fin Ticket 747 - Actualizar estado de certificado */
    
}
