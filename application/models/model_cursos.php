<?php

/**
 * Model_cursos
 * 
 * Description...
 * 
 * @package model_cursos
 * @author ivan <ivan.sys@gmail.com>
 * @version 1.0.0
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_cursos extends CI_Model {

    var $codigo = 0;
    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo = isset($arg["codigo"]) ? $arg["codigo"] : 0;
        $this->codigo_filial = $arg["codigo_filial"]; 
    }

    /**
     * Lista todos los cursos para datatable.
     * @access public
     * @param Array $arrFiltros filtros que se aplican.
     * @return Array de cursos.
     */
    public function listaCursosDatable($arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrCondindiciones = array();
        $nombre = "nombre_" . get_idioma();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "cursos." . $nombre => $arrFiltros["sSearch"],
                "cursos.codigo" => $arrFiltros['sSearch']
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
        $datos = Vcursos::getAllCursosDatatable($conexion, $arrCondindiciones, $arrLimit, $arrSort, false, $this->codigo_filial);
        $contar = Vcursos::getAllCursosDatatable($conexion, $arrCondindiciones, "", "", true, $this->codigo_filial);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array(),
            "aoColumns" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $uso = $row['estado'] == 'habilitado' && $row['uso'] > 0 ? true : false;
            $rows[] = array(
                $row["codigo"],
                $row["nombre_" . get_idioma()],
                $row["tipo_curso"] == "curso_corto" ? 1 : 0,
                $row["abreviatura"],
                $row["habilitado"],
                '',
                $uso,
                $row["cant_horas"]
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    /**
     * cambia el estado de un curso.
     * @access public
     * @return repuesta Guardar el cambio de estado.
     */
    public function cambioEstadoCurso($codcurso) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $curso = new Vcursos($conexion, $codcurso);
        $cursohabilitado = $curso->getCursoHabilitado();
        $esta = count($cursohabilitado) > 0 ? true : false;
        if ($esta) {
            $baja = $cursohabilitado[0]['baja'];
            $respuesta = $baja == '0' ? $curso->bajaCursoHabilitado() : $curso->altaCursoHabilitado();
        } else {
            $respuesta = $curso->nuevoCursoHabilitado();
        }
        return class_general::_generarRespuestaModelo($conexion, $respuesta);
    }

    /**
     * retorna los cursos habilitados.
     * @access public
     * @return repuesta array de cursos habilitados.
     */
    public function getCursosHabilitados($format = null, $solo = null, $baja = null, $forzarPlan = null, $codFilial = null) {
        if($codFilial == null) {
            $codFilial = $this->codigo_filial;
        }
        $conexion = $this->load->database($codFilial, TRUE);
        $cursos = Vcursos::getCursosHabilitados($conexion, null, null, null, $solo, $codFilial, $baja, $forzarPlan);
        if ($format !== null) {
            for ($i = 0; $i < count($cursos); $i++) {
                //modificacion ticket 5186 franco ocultar plan->
                $cursos[$i]['nombre'] = $cursos[$i]['cantplanes'] > 1 ? $cursos[$i]['nombre_' . get_idioma()] /*. ' / ' . $cursos[$i]['nombreplan'] */: $cursos[$i]['nombre_' . get_idioma()];
                //modificacion ticket 5186 franco ocultar plan->
            }
        }
        return $cursos;
    }

    public function getCursosConComisionesActivas() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $cursosConComisionesActivas = Vcursos::getCursosConComisionesActivas($conexion);
        return $cursosConComisionesActivas;
    }

    /**
     * Retorna los cursos habilitados para una filial (esta funcion se utiliza desde un web services)
     * 
     * @param int $idFilial
     * @param array $arrLimit
     * @param array $arrSort
     * @param array $search
     * @param array $searchFields
     * @return array
     */
    function getCursosHabilitadosFilial($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vcursos::getCursosHabilitadosFilial($conexion, $arrLimit, $arrSort, true, $search, $searchFields);
        $registros = Vcursos::getCursosHabilitadosFilial($conexion, $arrLimit, $arrSort, false, $search, $searchFields);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    function getCursosHabilitadosFilialWS($idFilial, $arrLimit = null, $arrSort = null, $search = null, $searchFields = null) {
        $conexion = $this->load->database($idFilial, true);
        $cantRegistros = Vcursos::getCursosHabilitadosFilialWS($conexion, $arrLimit, $arrSort, true, $search, $searchFields);
        $registros = Vcursos::getCursosHabilitadosFilialWS($conexion, $arrLimit, $arrSort, false, $search, $searchFields);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    /**
     * Retorna las comisiones para una filial (esta function es utilizada desde un web services)
     * 
     * @param int $idFilial
     * @param int $codigoCurso
     * @param int $habilitadas
     * @return array
     */
    public function getComisiones($idFilial, $codigoCurso, $habilitadas = null, $wiestado = null){
        $conexion = $this->load->database($idFilial, true);
        $this->load->helper('comisiones');
        $arrorden = array(array('campo' => 'codigo', 'orden' => 'desc'));
        $nombreviejo = Vconfiguracion::getValorConfiguracion($conexion, null, 'verNombreViejoComision');
        $myCurso = new Vcursos($conexion, $codigoCurso);
        $estado = null;
        if ($habilitadas != null) {
            $estado = array(Vcomisiones::getEstadoHabilitada());
        }
        if ($wiestado != null) {
            $estado = $wiestado;
        }
        $comisiones = $myCurso->getComisiones($arrorden,null,$estado);
        foreach ($comisiones as $key => $row) {
            $nombreComision = $row['nombre'];
            $comisiones[$key]['nombre'] = $nombreComision;
            $comisiones[$key]['nombre'].= $nombreviejo == '1' ? ' (' . $row['descripcion'] . ')' : '';
        }
        return $comisiones;
    }

    public function getArrCurso($codcurso) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array('codigo' => $codcurso);
        $curso = Vcursos::listarCursos($conexion, $condiciones);
        return $curso[0];
    }
	
	public function getArrDatosDeCurso($codcurso) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array('codigo' => $codcurso);
        $curso = Vcursos::listarCursos($conexion, $condiciones);
        return $curso[0];
    }

    public function getCurso($cod_curso) {
        $conexion = $this->load->database($this->codigo_filial, TRUE);
        $objCurso = new Vcursos($conexion, $cod_curso);
        return $objCurso;
    }

    public function getAbreviaturaCursoHabilitado($cod_curso) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $curso_habilitado = Vcursos::getAbreviaturaCursoHabilitado($conexion, $cod_curso);
        return $curso_habilitado;
    }

    public function guardarAbreviatura($cod_curso, $abreviatura) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objCurso = new Vcursos($conexion, $cod_curso);
        $estado = $objCurso->guardarAbreviaturaCursoHabilitado($abreviatura);
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    public function getPeriodosHabilitados($cod_curso) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objCurso = new Vcursos($conexion, $cod_curso);
        $retorno = $objCurso->getPeriodosHabilitados();
        foreach ($retorno as $key => $curso) {
            $retorno[$key]['nombre_periodo'] = lang($curso['nombre']);
        }
        return $retorno;
    }

    public function getPlanesAcademicos($codCurso) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $arrCondiciones = array(
            "cod_curso" => $codCurso
        );
        $arrPlanesAcademicos = Vplanes_academicos::listarPlanes_academicos($conexion, $arrCondiciones);
        return $arrPlanesAcademicos;
    }

    /**
     * Lista sobre la tabla cursos      Seguir agregando parametros de ser necesario
     * 
     * @param string $estado
     * @return array
     */
    public function listar($estado = null) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array();
        if ($estado != null)
            $condiciones['estado'] = $estado;
        $arrCursos = Vcursos::listarCursos($conexion, $condiciones);
        $arrResp = array();
        foreach ($arrCursos as $curso) {
            $arrResp[] = array(
                "codigo" => $curso['codigo'],
                "nombre" => $curso['nombre_' . get_idioma()]
            );
        }
        return $arrResp;
    }
    
    public function getMaterias($codCurso = null){
		// Webservice no instancia la clase solo consulta el metodo estatico
		if ($this->codigo_filial > 0){
			$conexion = $this->load->database($this->codigo_filial, true);
		}else{
			$conexion = $this->load->database("default", true);
		}  
		   
        if ($codCurso != null){
            $myCurso = new Vcursos($conexion, $codCurso);
            $arrMaterias = $myCurso->getMaterias();
        } else {
            $arrMaterias = Vmaterias::listarMaterias($conexion);
        }
        
        $idioma = get_idioma();
        $arrResp = array();
        foreach ($arrMaterias as $materia){
            $arrResp[] = array(
                "codigo" => $materia['codigo'],
                "nombre" => $materia["nombre_$idioma"]
            );
        }
        return $arrResp;
    }
}