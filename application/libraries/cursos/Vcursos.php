<?php

/**
 * Class Vcursos
 *
 * Class  Vcursos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcursos extends Tcursos {

    static private $estadoHabilitado = "habilitado";
    static private $estadoInhabilitado = "inhabilitado";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getAllCursosDatatable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $cod_filial = null) {
        $conexion->select('count(*)', false);
        $conexion->from('cursos_habilitados');
        $conexion->where('general.cursos.codigo = cursos_habilitados.cod_curso');
        $conexion->where('cursos_habilitados.baja', 0);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        if ($cod_filial != null) {
            $conexion->select('count(*)', false);
            $conexion->from('general.planes_academicos');
            $conexion->join('general.planes_academicos_filiales', 'general.planes_academicos_filiales.cod_plan_academico = general.planes_academicos.codigo');
            $conexion->where('general.planes_academicos.estado', 'habilitado');
            $conexion->where('general.planes_academicos_filiales.cod_filial', $cod_filial);
            $conexion->where('general.planes_academicos_filiales.estado', 'habilitado');
            $conexion->where('general.planes_academicos.cod_curso = general.cursos.codigo');
            $subquery2 = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("($subquery2) uso", false);
        }
        $conexion->select("general.cursos.*,($subquery) habilitado, cursos_habilitados.abreviatura", false);
        $conexion->from('general.cursos');
        $conexion->join('cursos_habilitados', 'cursos_habilitados.cod_curso = general.cursos.codigo', 'left');
        if ($arrCondindicioneslike != null) {
            foreach ($arrCondindicioneslike as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort > 0) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            return $conexion->count_all_results();
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }

    /**
     * retorna las comisiones asignadas a un curso
     * 
     * return boolean
     */
    public function getComisiones($orden = null, $habilitadas = null,$wiestado  = null) {
        $comisiones = '';
        $cod_curso = $this->codigo;
        if ($habilitadas != null) {
            $condiciones = array(
                "comisiones.estado" => Vcomisiones::getEstadoHabilitada());            
        } else {            
            $condiciones = null;
        }   
        
        
        
        $comisiones = Vcomisiones::getComisiones($this->oConnection, $condiciones, $cod_curso,null
                ,null,null,null,null,$wiestado);
        return $comisiones;
    }

    /**
     * retorna las comisiones asignadas a un curso
     * 
     * return boolean
     */
    public function getComisionesConAlumnos($orden = null, $habilitadas = null,$wiestado  = null) {
        $comisiones = '';
        $cod_curso = $this->codigo;
        if ($habilitadas != null) {
            $condiciones = array(
                "comisiones.estado" => Vcomisiones::getEstadoHabilitada());            
        } else {            
            $condiciones = null;
        }   
        
        
        
        $comisiones = Vcomisiones::getComisiones($this->oConnection, $condiciones, $cod_curso,null
                ,null,null,null,null,$wiestado);
        $this->oConnection->resetear();
        $comisionesConInscriptos = array();
        foreach($comisiones as $comision){
            $inscriptos = Vcomisiones::getComisionesCantidadesInscriptos($this->oConnection, null, $comision['codigo'], false);
            if(count($inscriptos) > 0)
                $comisionesConInscriptos[] = $comision;
        }
        return $comisionesConInscriptos;
    }
    /* STATIC FUNCTIONS */

    static function getCursosHabilitadosFilial(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null) {
        $aColumns = array();
        $aColumns['nombre_es']['order'] = "general.cursos.nombre_es";
        $aColumns['cantidad_anios']['order'] = "general.cursos.cantidad_anios";
        $aColumns['cantidad_meses']['order'] = "general.cursos.cantidad_meses";
        $aColumns['cant_horas']['order'] = "general.cursos.cant_horas";
        $aColumns['nombre_es']['having'] = "nombre_es";
        $aColumns['cantidad_anios']['having'] = "cantidad_anios";
        $aColumns['cantidad_meses']['having'] = "cantidad_meses";
        $aColumns['cant_horas']['having'] = "cant_horas";
        $conexion->select("general.cursos.codigo");
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("general.cursos.cantidad_meses");
        $conexion->select("general.cursos.cant_horas");
        $conexion->from("cursos_habilitados");
        $conexion->join("general.cursos", "general.cursos.codigo = cursos_habilitados.cod_curso");
        if ($search != null) {
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $conexion->or_having($tableFields['having'] . " LIKE ", "%$search%");
                }
            }
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    static function getCursosHabilitadosFilialWS(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, $search = null, array $searchFields = null) {
        $aColumns = array();
        $aColumns['nombre_es']['order'] = "general.cursos.nombre_es";
        $aColumns['nombre_pt']['order'] = "general.cursos.nombre_pt";
        $aColumns['nombre_in']['order'] = "general.cursos.nombre_in";
        $aColumns['cantidad_anios']['order'] = "general.cursos.cantidad_anios";
        $aColumns['cantidad_meses']['order'] = "general.cursos.cantidad_meses";
        $aColumns['cant_horas']['order'] = "general.cursos.cant_horas";
        $aColumns['nombre_es']['having'] = "nombre_es";
        $aColumns['nombre_pt']['having'] = "nombre_pt";
        $aColumns['nombre_in']['having'] = "nombre_in";
        $aColumns['cantidad_anios']['having'] = "cantidad_anios";
        $aColumns['cantidad_meses']['having'] = "cantidad_meses";
        $aColumns['cant_horas']['having'] = "cant_horas";
        $conexion->select("general.cursos.codigo");
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("general.cursos.nombre_pt");
        $conexion->select("general.cursos.nombre_in");
        $conexion->select("general.cursos.cantidad_meses");
        $conexion->select("general.cursos.cant_horas");
        $conexion->from("cursos_habilitados");
        $conexion->join("general.cursos", "general.cursos.codigo = cursos_habilitados.cod_curso");
        if ($search != null) {
            foreach ($aColumns AS $key => $tableFields) {
                if ($searchFields == null || in_array($key, $searchFields)) {
                    $conexion->or_having($tableFields['having'] . " LIKE ", "%$search%");
                }
            }
        }
        if (!$contar) {
            if ($arrLimit != null && is_array($arrLimit))
                $conexion->limit($arrLimit[1], $arrLimit[0]);
            if ($arrSort != null && is_array($arrSort) && isset($aColumns[$arrSort[0]]['order']))
                $conexion->order_by($aColumns[$arrSort[0]]['order'], $arrSort[1]);
        }
        $query = $conexion->get();
        if ($contar)
            return $query->num_rows();
        else
            return $query->result_array();
    }

    function getAbreviatura() {
        $this->oConnection->select('abreviatura');
        $this->oConnection->from('general.cursos_abreviatura');
        $this->oConnection->where('cod_curso', $this->codigo);
        $this->oConnection->where('idioma', get_idioma());
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        return $resultado[0]['abreviatura'];
    }

    static function getCursosHabilitados(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike = null, $arrLimit = null, $contar = false, 
            $solo = null, $cod_filial = null, $baja = null, $forzarPlan = null) {
        $conexion->select('cursos.*');
        $conexion->from("$conexion->database.cursos_habilitados");
        if ($solo == null) {
            $conexion->select('planes_academicos.codigo as cod_plan_academico, general.planes_academicos.nombre as nombreplan, (select count(codigo) from general.planes_academicos where general.planes_academicos.cod_curso = cursos_habilitados.cod_curso) as cantplanes');
            $conexion->join('general.planes_academicos', 'general.planes_academicos.cod_curso = cursos_habilitados.cod_curso');
            if ($forzarPlan != null){
                $conexion->where("(general.planes_academicos.estado = 'habilitado' OR general.planes_academicos.codigo = $forzarPlan)");
            } else {
                $conexion->where('general.planes_academicos.estado = "habilitado"');
            }            
            if ($cod_filial != null) {
                $conexion->select('(SELECT COUNT(general.planes_academicos_filiales.cod_plan_academico) FROM general.planes_academicos_filiales 
                        WHERE general.planes_academicos_filiales.cod_plan_academico = general.planes_academicos.codigo AND general.planes_academicos_filiales.cod_filial = '.$cod_filial.
                        ' AND general.planes_academicos_filiales.estado = "habilitado") AS planfilial', false);
                $conexion->having('planfilial > 0');
            }
        }
        $conexion->join('general.cursos', 'general.cursos.codigo = ' . $conexion->database . ".cursos_habilitados.cod_curso");
        if ($arrCondindicioneslike != null) {
            foreach ($arrCondindicioneslike as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }        
        if ($baja !== null){
            $conexion->where("baja", $baja);
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($contar) {
            return $conexion->count_all_results();
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }

    static function getCursosConComisionesActivas(CI_DB_mysqli_driver $conexion, $idioma = null) {
        if ($idioma != null){
            $nombre = "general.cursos.nombre_" . $idioma;
        } else {
            $nombre = "general.cursos.nombre_" . get_idioma();
        }
        $conexion->select('COUNT(comisiones.codigo)');
        $conexion->from('comisiones');
        $conexion->where('comisiones.cod_plan_academico = general.planes_academicos.codigo');
        $conexion->where('comisiones.estado', Vcomisiones::getEstadoHabilitada());
        $subquery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select('general.cursos.codigo');
        $conexion->select("$nombre");
        $conexion->select('general.planes_academicos.codigo as cod_plan_academico');
        $conexion->select("($subquery) as comisiones_activas_curso");
        $conexion->from('general.cursos');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.cod_curso = general.cursos.codigo');
        $conexion->having('comisiones_activas_curso >', 0);
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getMateriasCursosHabilitados(CI_DB_mysqli_driver $conexion) {
        $conexion->select('general.materias.*, general.materias_curso.cod_tipo_periodo', FALSE);
        $conexion->from('general.materias');
        $conexion->join('general.materias_curso', 'general.materias_curso.cod_materia = general.materias.codigo');
        $conexion->join('cursos_habilitados', 'cursos_habilitados.cod_curso = general.materias_curso.cod_curso');
        $conexion->where('cursos_habilitados.baja', 0);
        $conexion->group_by('general.materias.codigo');
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getAbreviaturaCursoHabilitado(CI_DB_mysqli_driver $conexion, $cod_curso) {
        $conexion->select('cod_curso, abreviatura');
        $conexion->from('cursos_habilitados');
        $conexion->where('cod_curso', $cod_curso);
        $query = $conexion->get();

        return $query->result_array();
    }

    public function guardarAbreviaturaCursoHabilitado($abreviatura) {
        $dato = array('abreviatura' => $abreviatura);
        $this->oConnection->where('cod_curso', $this->codigo);
        return $this->oConnection->update('cursos_habilitados', $dato);
    }

    public function getCursoHabilitado() {
        $this->oConnection->select('*');
        $this->oConnection->from('cursos_habilitados');
        $this->oConnection->where('cod_curso', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function bajaCursoHabilitado() {
        $dato = array('baja' => 1);
        $this->oConnection->where('cod_curso', $this->codigo);
        $this->oConnection->update('cursos_habilitados', $dato);
    }

    public function altaCursoHabilitado() {
        $dato = array('baja' => 0);
        $this->oConnection->where('cod_curso', $this->codigo);
        return $this->oConnection->update('cursos_habilitados', $dato);
    }

    public function nuevoCursoHabilitado() {
        $datos = array('cod_curso' => $this->codigo, 'baja' => 0, 'abreviatura' => $this->getAbreviatura());
        return $this->oConnection->insert('cursos_habilitados', $datos);
    }

    public function getHorariosComisiones() {
        $this->oConnection->select('(WEEKDAY(horarios.dia)) as DIA_SEMANA,horarios.horadesde, horarios.horahasta, comisiones.codigo');
        $this->oConnection->from('horarios');
        $this->oConnection->join('comisiones', 'comisiones.codigo = horarios.cod_comision');
        $this->oConnection->where('comisiones.cod_curso', $this->codigo);
        $this->oConnection->order_by('DIA_SEMANA', 'asc');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getPeriodosHabilitados() {
        $this->oConnection->select('general.planes_academicos_periodos.cod_tipo_periodo, general.tipos_periodos.nombre');
        $this->oConnection->from('general.planes_academicos');
        $this->oConnection->join('general.planes_academicos_periodos', 'general.planes_academicos_periodos.cod_plan_academico = general.planes_academicos.codigo');
        $this->oConnection->join('general.tipos_periodos', 'general.planes_academicos_periodos.cod_tipo_periodo = general.tipos_periodos.codigo');
        $this->oConnection->where('general.planes_academicos.cod_curso', $this->codigo);
        $this->oConnection->where('general.planes_academicos.estado', 'habilitado');
        $this->oConnection->group_by('general.tipos_periodos.codigo');
        $this->oConnection->order_by('general.planes_academicos_periodos.orden', 'asc');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function insertSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $arrTemp[$primary] = $this->$primary;
        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $primaryVal = $this->$primary;
        return $this->oConnection->update($this->nombreTabla, $arrTemp, "$primary = $primaryVal");
    }

    static function getListaAbreviaturaCursosHabilitados(CI_DB_mysqli_driver $conexion, $cod_curso = null) {
        $conexion->select('cursos_habilitados.abreviatura');
        $conexion->from('cursos_habilitados');
        if ($cod_curso != null) {
            $conexion->where('cursos_habilitados.cod_curso', $cod_curso);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static public function getEstadoHabilitado(){
        return self::$estadoHabilitado;
    }
    
    static public function getEstadoInhabilitado(){
        return self::$estadoInhabilitado;
    }
    
    public function getMaterias(){
        $this->oConnection->select("general.materias.*", false);
        $this->oConnection->from("general.planes_academicos");
        $this->oConnection->join("general.materias_plan_academico", "general.materias_plan_academico.cod_plan = general.planes_academicos.codigo");
        $this->oConnection->join("general.materias", "general.materias.codigo = general.materias_plan_academico.cod_materia");
        $this->oConnection->where("general.planes_academicos.cod_curso", $this->codigo);
        $this->oConnection->group_by("general.materias.codigo");
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCursosPaises($codPais = null){
        $this->oConnection->select("general.cursos_paises.id_pais");
        $this->oConnection->select("general.cursos_paises.horas");
        $this->oConnection->select("general.cursos_paises.meses");
        $this->oConnection->from("general.cursos_paises");
        $this->oConnection->where("general.cursos_paises.id_curso", $this->codigo);
        if ($codPais != null){
            $this->oConnection->where("id_pais", $codPais);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function setCursoPais($codPais, $horas, $meses){
        $this->oConnection->where("id_curso", $this->codigo);
        $this->oConnection->where("id_pais", $codPais);
        $resp = $this->oConnection->delete("general.cursos_paises");
        $param = array(
            "id_curso" => $this->codigo,
            "id_pais" => $codPais,
            "horas" => $horas,
            "meses" => $meses
        );
        return $resp && $this->oConnection->insert("general.cursos_paises", $param);
    }

    public static function getCursosFaqCampus($conexion, $condiciones)
    {
        //$condiciones['tipo_usuario']='alumno';
        //$condiciones['codigo_usuario']='2';
        if($condiciones['tipo_usuario'] == 'profesor')
        {
            $conexion->select('horarios_profesores.cod_horario, general.cursos.codigo as codigo_curso, general.cursos.nombre_es as nombre_curso, general.materias.codigo as codigo_materia, general.materias.nombre_es as nombre_materia');
            $conexion->from('horarios_profesores');
            $conexion->join('horarios','horarios.codigo = horarios_profesores.cod_horario');
            $conexion->join('general.materias','general.materias.codigo = horarios.cod_materia');
            $conexion->join('comisiones','comisiones.codigo = horarios.cod_comision');
            $conexion->join('general.planes_academicos','general.planes_academicos.codigo = comisiones.cod_plan_academico');
            $conexion->join('general.cursos','general.cursos.codigo = general.planes_academicos.cod_curso');
            $conexion->where('comisiones.estado = "habilitado"');
            $conexion->where('horarios.baja = 0');
            $conexion->where('horarios_profesores.cod_profesor = '.$condiciones['codigo_usuario']);
            $conexion->group_by('cod_materia');
            $query = $conexion->get();
            return $query->result_array();
        }
        else
        {

            $conexion->select('matriculas.cod_plan_academico as plan');
            $conexion->from('matriculas');
            $conexion->where('cod_alumno = '.$condiciones['codigo_usuario']);
            $planes = $conexion->get();
            $plan = $planes->result_array();

            $conexion->select('matriculas.codigo as codigo_matricula, general.cursos.codigo as codigo_curso, general.cursos.nombre_es as nombre_curso, general.materias.codigo as codigo_materia, general.materias.nombre_es as nombre_materia');
            $conexion->from('matriculas');
            $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
            $conexion->join('general.cursos', 'general.cursos.codigo = general.planes_academicos.cod_curso');
            $conexion->join('general.materias_plan_academico', 'general.materias_plan_academico.cod_plan = matriculas.cod_plan_academico');
            $conexion->join('general.materias', 'general.materias.codigo = general.materias_plan_academico.cod_materia');
            $conexion->where('matriculas.cod_plan_academico = '.$plan[0]['plan']);
            $conexion->group_by('codigo_materia');
            $query = $conexion->get();

            return $query->result_array();

        }


    }

    public function getNombreIdioma($idioma) {
        if($idioma == 'pt') {
            return $this->nombre_pt;
        }
        else if($idioma == 'in' || $idioma == 'en') {
            return $this->nombre_en;
        }
        else if($idioma == 'es') {
            return $this->nombre_es;
        }
    }
}
