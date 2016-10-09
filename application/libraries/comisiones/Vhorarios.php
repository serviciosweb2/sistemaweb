<?php

/**
 * Class Vhorarios
 * 
 * Class  Vhorarios maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vhorarios extends Thorarios {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getAllHorarios(CI_DB_mysqli_driver $conexion, $filial,$fechaInicio = null, $fechaFin = null, array $salones = null, array $comisiones = null, array $materias = null, array $profesores = null, $pantallaHorarios = false) {
        $materia = "general.materias.nombre_" . get_idioma();
        $nombre = "general.cursos.nombre_" . get_idioma();
        $conexion->select('general.planes_academicos_periodos.color');
        $conexion->from('general.planes_academicos_periodos');
        $conexion->where('comisiones.cod_tipo_periodo = general.planes_academicos_periodos.cod_tipo_periodo');
        $conexion->where('comisiones.cod_plan_academico = general.planes_academicos_periodos.cod_plan_academico');
        $subquery = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select('general.planes_academicos_filiales.nombre_periodo');
        $conexion->from('general.planes_academicos_filiales');
        $conexion->where('general.planes_academicos_filiales.cod_plan_academico = comisiones.cod_plan_academico');
        $conexion->where('general.planes_academicos_filiales.cod_tipo_periodo = comisiones.cod_tipo_periodo');
        $conexion->where('general.planes_academicos_filiales.modalidad = comisiones.modalidad');
        $conexion->where('general.planes_academicos_filiales.cod_filial',$filial);
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("horarios.*");
        $conexion->select("salones.color");
        $conexion->select("comisiones.nombre");
        $conexion->select("$materia as materia_nombre");
        $conexion->select("comisiones.nombre as comision");
        $conexion->select("$nombre as curso");
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->select("comisiones.cod_tipo_periodo");
        $conexion->select("comisiones.ciclo");
        $conexion->select("(SELECT COUNT(codigo) FROM matriculas_horarios WHERE estado <> 'null' AND matriculas_horarios.baja = 0 AND matriculas_horarios.cod_horario = horarios.codigo) AS cantasistencia");
        //Ticket 4628  -mmori- se modifica la consulta para traer alumnos con estado "cursando" 
        if($pantallaHorarios){
            $conexion->select("(SELECT COUNT(estadoacademico.codigo) FROM estadoacademico ".
                    "INNER JOIN matriculas_inscripciones ON matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo and matriculas_inscripciones.baja = 0 ".
                    "INNER JOIN matriculas_periodos on matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo and matriculas_periodos.estado = 'habilitada' ".
                    "WHERE estadoacademico.estado NOT IN ('migrado','recursa') ".
                    "AND estadoacademico.codmateria = horarios.cod_materia AND matriculas_inscripciones.cod_comision = horarios.cod_comision) AS alumnos_inscriptos_comision", false);
        } else {
            $conexion->select("(SELECT COUNT(codigo) FROM matriculas_horarios WHERE matriculas_horarios.baja = 0 AND matriculas_horarios.cod_horario = horarios.codigo) AS alumnos_inscriptos_comision");
        }
        
        $conexion->select('salones.cupo');
        $conexion->select("($subquery) as color_curso",false);
        $conexion->select("($subquery2) as nombre_periodo",false);
        $conexion->from("horarios");
        $conexion->join("salones", " salones.codigo = horarios.cod_salon");
        $conexion->join("general.materias", " general.materias.codigo = horarios.cod_materia");
        $conexion->join("comisiones", " comisiones.codigo = horarios.cod_comision");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = comisiones.cod_plan_academico");
        $conexion->join('general.cursos', 'general.cursos.codigo = general.planes_academicos.cod_curso');
        $conexion->where("baja", 0);
        if ($fechaInicio != null)
            $conexion->where("horarios.dia >=", $fechaInicio);
        if ($fechaFin != null)
            $conexion->where("horarios.dia <=", $fechaFin);
        if ($salones != null)
            $conexion->where_in("salones.codigo", $salones);
        if ($comisiones != null)
            $conexion->where_in("comisiones.codigo", $comisiones);
        if ($materias != null)
            $conexion->where_in("materias.codigo", $materias);
        if ($profesores != null) {
            $conexion->join("horarios_profesores", "horarios_profesores.cod_horario = horarios.codigo");
            $conexion->where_in("horarios_profesores.cod_profesor", $profesores);
            $conexion->group_by("horarios.codigo");        }
        $query = $conexion->get();
        return $query->result();
    }

    function guardarHorario($comision, $salon, $materia, $dia, $horad, $horah, $baja = '0', $padre = '0', $codusuario = null) {
        $this->cod_comision = $comision;
        $this->cod_salon = $salon;
        $this->cod_materia = $materia;
        $this->dia = $dia;
        $this->horadesde = $horad;
        //Ticket 4659 -mmori- evito que hora hasta sea igual a 24 o 00 ya que el plugin no lo puede dibujar
        $this->horahasta = ($horah == "24:00:00" || $horah == "24:00" || $horah == "00:00" || $horah == "00:00:00") ? "23:59" : $horah;
        $this->baja = $baja == null ? '0' : $baja;
        $this->padre = $padre == null ? '0' : $padre;
        $this->_guardar();
        Vmatriculas_inscripciones::guardarMatriculasHorarios($this->oConnection, $this->cod_comision, $this->cod_materia, $codusuario);
        return true;
    }
    

    function unSetHorario() {
        $this->sederHijos();
        $this->baja = 1;
        $this->guardarHorarios();
        Vmatriculas_horarios::baja($this->oConnection, null, null, null, null, '1', $this->codigo);
        return $this->codigo;
    }

    function unSetRelacionados() {
        $padreUnset = 0;
        $condiciones = array();
        if ($this->padre == '0') {
            $padreUnset = $this->codigo;
        } else {
            $padreUnset = $this->padre;
        }
        $condiciones['padre'] = $padreUnset;
        $condiciones['dia >'] = $this->dia;
        $arrHorario = Vhorarios::listarHorarios($this->oConnection, $condiciones);
        foreach ($arrHorario as $rowHorario) {
            $oHorario = new Vhorarios($this->oConnection, $rowHorario["codigo"]);
            $oHorario->unSetHorario();
        }
        return $arrHorario;
    }

    function sederHijos() {
        if ($this->padre == "0") {
            $condiciones = array("padre" => $this->codigo,
                "baja" => 0);
            $hijos = Vhorarios::listarHorarios($this->oConnection, $condiciones);
            if (count($hijos) > 0) {
                $nuevoPadre = $hijos[0]["codigo"];
                $condicion = array();
                foreach ($hijos as $row) {

                    if ($hijos[0]["codigo"] !== $row["codigo"]) {

                        $condicion[] = $row["codigo"];
                    } else {
                        $data = array("padre" => null);
                        $this->oConnection->where("codigo", $row["codigo"]);
                        $this->oConnection->update($this->nombreTabla, $data);
                    }
                }
                $data = array("padre" => $nuevoPadre);
                if (count($condicion) > 0) {
                    $this->oConnection->where_in("codigo", $condicion);
                    $this->oConnection->update($this->nombreTabla, $data);
                }
            }
        }
    }

    function exitenCorrelativo() {
        $condiciones = array(
            "padre" => $this->codigo,
            "baja" => 0);
        $hijos = Vhorarios::listarHorarios($this->oConnection, $condiciones);
        if (count($hijos) == true) {
            return true;
        } else {
            $condiciones = array(
                "padre" => $this->padre,
                "baja" => 0, "padre <>" => "");
            $hermanos = Vhorarios::listarHorarios($this->oConnection, $condiciones);
            if (count($hermanos) != 0) {
                if ($hermanos[count($hermanos) - 1]["codigo"] == $this->codigo) {
                    return FALSE;
                } else {
                    return TRUE;
                }
            } else {
                return false;
            }
        }
    }

    function getHijos() {
        $condicion = array("padre" => $this->codigo, "baja" => 0);
        $retorno = Vhorarios::listarHorarios($this->oConnection, $condicion);
        return $retorno;
    }

    function getHermanos() {
        $condicion = array("padre" => $this->padre, "baja" => 0);
        return Vhorarios::listarHorarios($this->oConnection, $condicion);
    }

    function getDiasSerie() {
        $dias = array();
        $padre = $this->padre == "0" ? true : false;
        $hijos = array();
        switch ($padre) {
            
            case true:
                $hijos = $this->getHijos();
                break;
            
            case false:
                $hijos = $this->getHermanos();
                break;
            
            default:
                break;
        }
        foreach ($hijos as $h) {
            $ndia = date("N", strtotime($h["dia"]));
            $dias[$ndia] = true;
        }
        return $dias;
    }

    function getFechaFinSerie() {
        $dias = array();
        $padre = $this->padre == "0" ? true : false;
        $hijos = array();
        switch ($padre) {
            
            case true:
                $hijos = $this->getHijos();
                break;
            
            case false:
                $hijos = $this->getHermanos();
                break;
            
            default:
                break;
        }

        foreach ($hijos as $h) {
            $dias = array("fin" => $h["dia"]);
        }
        return $dias;
    }

    function NoEsMasPadre() {
        $this->oConnection->trans_start();
        $this->padre = '0';
        $this->guardarHorarios();
        $this->sederHijos();
        $this->oConnection->trans_complete();
    }

    function getAsistenciaCargada() {
        $conexion = $this->oConnection;
        $conexion->select('matriculas_horarios.*');
        $conexion->from('matriculas_horarios');
        $conexion->where('matriculas_horarios.cod_horario', $this->codigo);
        $conexion->where('matriculas_horarios.baja', 0);
        $conexion->where('matriculas_horarios.estado <>', 'NULL');
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getHorarios(CI_DB_mysqli_driver $conexion, $fecha = null, $horadesde = '00:00:00', $horahasta = '00:00:00', $repite = '0', $baja = null) {
        $conexion->select('*');
        $conexion->from('horarios');
        if ($baja != null) {
            $conexion->where('baja', $baja);
        }       
        if ($fecha != null) {
            switch ($repite) {
                case '1':
                    $date_parts = explode("-", $fecha); //0 Y 1 m 2 d
                    $conexion->where('day(dia) = ' . $date_parts[2] . ' and MONTH(dia) = ' . $date_parts[1] . ' and year(dia) >= ' . $date_parts[0]);
                    break;
                
                case '0':
                    $date_parts = explode("-", $fecha); //0 Y 1 m 2 d
                    $conexion->where('day(dia) = ' . $date_parts[2] . ' and MONTH(dia) = ' . $date_parts[1] . ' and year(dia) = ' . $date_parts[0]);
                    break;

                default:
                    break;
            }
        }
        if ($horadesde != '00:00:00' && $horadesde != null && $horahasta != '00:00:00' && $horahasta != null) {
            $conexion->where('((horadesde <= "' . $horadesde . '" and horahasta >= "' . $horadesde . '") or (horadesde >= "' . $horadesde . '" and horahasta <= "' . $horahasta . '") or (horadesde <= "' . $horahasta . '" and horahasta >= "' . $horahasta . '")or (horadesde >= "' . $horadesde . '" and horahasta <= "' . $horahasta . '"))');
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getHorariosAltaMatricula(CI_DB_mysqli_driver $conexion, $condiciones, $codestadoacademico) {
        $conexion->select('horarios.*');
        $conexion->from('horarios');
        $conexion->where($condiciones);
        $conexion->where('horarios.codigo not in (select matriculas_horarios.cod_horario from matriculas_horarios where cod_estado_academico = "' . $codestadoacademico . '" and baja = "0")');
        $query = $conexion->get();
        return $query->result_array();
    }

    function setHorarioProfesor($codprofesor) {
        $datos = array('cod_profesor' => $codprofesor,
            'cod_horario' => $this->codigo);
        $this->oConnection->insert('horarios_profesores', $datos);
    }
    
    function updateHorarioProfesor($codprofesor){
        $datos = array('cod_profesor' => $codprofesor);
        $this->oConnection->where('horarios_profesores.cod_horario',  $this->codigo);
        $this->oConnection->update('horarios_profesores', $datos);
    }

    function getProfesores() {
        $this->oConnection->select('profesores.*');
        $this->oConnection->from('profesores');
        $this->oConnection->join('horarios_profesores', 'horarios_profesores.cod_profesor = profesores.codigo');
        $this->oConnection->where('horarios_profesores.cod_horario', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    function getHorariosExcepciones($arrSearch = null, $arrLimit = null, $arrSort = null, $contar = false) {
        $conexion = $this->oConnection;
        $conexion->select('horarios.*, general.planes_academicos.cod_curso, comisiones.cod_tipo_periodo, comisiones.ciclo, comisiones.nombre');
        $conexion->from('horarios');
        $conexion->join('comisiones', 'comisiones.codigo = horarios.cod_comision');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $conexion->where('comisiones.estado', Vcomisiones::getEstadoHabilitada());
        $conexion->where('horarios.baja', 0);
        $conexion->where('horarios.dia >=', $this->dia);
        $conexion->where('horarios.cod_materia', $this->cod_materia);
        $conexion->where('comisiones.codigo <>', $this->cod_comision);
        if ($arrSearch != null) {
            foreach ($arrSearch as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {
            foreach ($arrSort as $rowsort) {
                $conexion->order_by($rowsort["0"], $rowsort["1"]);
            }
        }
        if ($contar) {
            return $conexion->count_all_results();
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }

    function getHorario() {
        $this->oConnection->select('horarios.*, salones.salon, comisiones.nombre, general.materias.nombre_es,general.materias.nombre_pt,general.materias.nombre_in');
        $this->oConnection->from('horarios');
        $this->oConnection->join('salones', 'salones.codigo = horarios.cod_salon');
        $this->oConnection->join('comisiones', 'comisiones.codigo = horarios.cod_comision');
        $this->oConnection->join('general.materias', 'general.materias.codigo = horarios.cod_materia');
        $this->oConnection->where('horarios.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    static function getProfesorComisionMateria(CI_DB_mysqli_driver $conexion, $cod_comision, $cod_materia, $fecha, $cod_horario = false) {
        $conexion->select('horarios_profesores.*');
        $conexion->from('horarios');
        
        
        if($cod_horario)
        {
            $conexion->join('horarios_profesores', 'horarios_profesores.cod_horario = horarios.codigo', 'LEFT');
            $conexion->where_in('horarios.codigo', $cod_horario);
            $conexion->group_by('horarios_profesores.cod_profesor');
        }
        else
        {
            $conexion->join('horarios_profesores', 'horarios_profesores.cod_horario = horarios.codigo');
            $conexion->where('horarios.cod_comision', $cod_comision);
            $conexion->where('horarios.cod_materia', $cod_materia);
            $conexion->where('horarios.dia', $fecha);
            $conexion->group_by('horarios_profesores.cod_profesor');
        }
        
        
        
        
        $query = $conexion->get();
        
        //echo $conexion->last_query();exit();
        
        return $query->result_array();
    }
    
    static function getHorariosEntreDias(CI_DB_mysqli_driver $conexion, $diaDesde, $diaHasta){
        $conexion->select('DISTINCT horarios.dia',false);
        $conexion->from('horarios');
        $conexion->where('horarios.baja',0);
        $conexion->where("horarios.dia BETWEEN '$diaDesde' AND '$diaHasta'");
        $conexion->order_by('horarios.dia','DESC');
        $query = $conexion->get();
        return $query->result_array();        
    }
    
    static function getProfesoresMateriaHorarios(CI_DB_mysqli_driver $conexion,$cod_materia){
        $conexion->select("concat(profesores.nombre,', ',profesores.apellido) as nombre_profesor, profesores.codigo as cod_profesor",false);
        $conexion->from('horarios');
        $conexion->join('horarios_profesores','horarios_profesores.cod_horario = horarios.codigo');
        $conexion->join('profesores','profesores.codigo = horarios_profesores.cod_profesor');
        $conexion->where('horarios.cod_materia',$cod_materia);
        $conexion->group_by('profesores.codigo');
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getFeriadosFecha(CI_DB_mysqli_driver $conexion, $fechaDesde=null, $fechaHasta = null){
        $conexion->select("feriados.codigo, feriados.nombre, CONCAT(feriados.anio, '-', LPAD(feriados.mes, 2, 0), '-', LPAD(feriados.dia, 2, 0)) dia_feriado",false);
        $conexion->from('feriados');
        if($fechaDesde != null){
           $conexion->having('dia_feriado',$fechaDesde); 
        }
        if($fechaDesde != null && $fechaHasta != null){
            $conexion->having('dia_feriado >=',$fechaDesde);
            $conexion->having('dia_feriado <=',$fechaHasta);
        }        
        $query = $conexion->get();        
        return $query->result_array();        
    }
    
    static function getRecesosFilialFechas(CI_DB_mysqli_driver $conexion,$fechaDesde = null,$fechaHasta =null){
            $conexion->select('*');
            $conexion->from('general.receso_filial');
            if($fechaDesde != NULL){
                $conexion->where('general.receso_filial.fecha_desde',$fechaDesde);
            }
            if($fechaDesde != null && $fechaHasta != null){
                $conexion->where("fecha_desde >=",$fechaDesde);
                $conexion->where("fecha_hasta <=",$fechaHasta);
            }
            $query = $conexion->get();
            return $query->result_array();
    }
    
    static function validarHorario(CI_DB_mysqli_driver $conexion, $dia, $horaDesde, $horaHasta, $cod_comision = null, $cod_salon=null, $fechaHasta = null, $cod_horario = '', $cod_materia = null){
        $conexion->select('horarios.*');
        $conexion->from('horarios');
        $conexion->where('horarios.baja',0);
        if($cod_comision != null){
            $conexion->where('horarios.cod_comision',$cod_comision);  
        }
        $conexion->where('horarios.dia',$dia);
        if($cod_salon != null){
            $conexion->where("(horarios.cod_salon = '$cod_salon') and (horarios.cod_materia <> '$cod_materia')",null,false);
        }
        $conexion->where("(('$horaHasta' >  horarios.horadesde and '$horaHasta' <= horarios.horahasta) or ('$horaDesde' >= horarios.horadesde and '$horaDesde' < horarios.horahasta) or ('$horaDesde' < horarios.horadesde and '$horaHasta' > horarios.horahasta))",null,false);
        if($fechaHasta != null){
            $conexion->where("horarios.dia BETWEEN $dia AND $fechaHasta");
        }
        if($cod_horario != ''){
            $conexion->where('horarios.codigo <>',$cod_horario);
        }
       $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getColor_InscriptosComision(CI_DB_mysqli_driver $conexion,$cod_horario,$filial){
        $conexion->select('COUNT(codigo)');
        $conexion->from('matriculas_horarios');
        $conexion->where('matriculas_horarios.baja',0);
        $conexion->where('matriculas_horarios.cod_horario = horarios.codigo');
        $subquery = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select('general.planes_academicos_periodos.color');
        $conexion->from('general.planes_academicos_periodos');
        $conexion->where('general.planes_academicos_periodos.cod_plan_academico = comisiones.cod_plan_academico');
        $conexion->where('general.planes_academicos_periodos.cod_tipo_periodo = comisiones.cod_tipo_periodo');
        $subquery2 = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select('general.planes_academicos_filiales.nombre_periodo');
        $conexion->from('general.planes_academicos_filiales');
        $conexion->where('general.planes_academicos_filiales.cod_plan_academico = comisiones.cod_plan_academico');
        $conexion->where('general.planes_academicos_filiales.cod_tipo_periodo = comisiones.cod_tipo_periodo');
        $conexion->where('general.planes_academicos_filiales.modalidad = comisiones.modalidad');
        $conexion->where('general.planes_academicos_filiales.cod_filial',$filial);
        $subquery3 = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("($subquery) as inscriptos_alumnos",false);
        $conexion->select("($subquery2) as color_curso_web",false);
        $conexion->select("($subquery3) as nombre_periodo",false);
        $conexion->select('salones.cupo');
        $conexion->from('horarios');
        $conexion->join('comisiones','comisiones.codigo = horarios.cod_comision');
        $conexion->join('salones','salones.codigo = horarios.cod_salon');
        $conexion->where('horarios.codigo',$cod_horario);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getHorariosDiaComisionMateria(CI_DB_mysqli_driver $conexion, $cod_comision, $cod_materia, $dia){
        $conexion->select("horarios.*,IFNULL(horarios_profesores.cod_profesor,'0') as profesor",false);
        $conexion->from('horarios');
        $conexion->join('horarios_profesores','horarios_profesores.cod_horario = horarios.codigo','left');
        $conexion->where('horarios.baja',0);
        $conexion->where('horarios.dia',$dia);
        $conexion->where('horarios.cod_comision',$cod_comision);
        $conexion->where('cod_materia',$cod_materia);
        $query = $conexion->get();
        return $query->result_array();        
    }
    
    static function getDetalleHorariosProfesores(CI_DB_mysqli_driver $conexion, $where_in){
        $conexion->select('horarios.*, horarios_profesores.cod_profesor');
        $conexion->from('horarios');
        $conexion->join('horarios_profesores','horarios_profesores.cod_horario = horarios.codigo','left');
        $conexion->where_in('horarios.codigo',$where_in);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    public function baja(){
        $this->baja = 1;
        return $this->guardarHorarios();
    }
    
    public function asistencia_tomada(){
        $this->asistencia = 1;
        return $this->guardarHorarios();
    }
    
    public function asistencia_notomada(){
        $this->asistencia = 0;
        return $this->guardarHorarios();
    }
    
}