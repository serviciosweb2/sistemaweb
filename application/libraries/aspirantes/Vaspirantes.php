<?php

/**
 * Class Vaspirantes
 *
 * Class  Vaspirantes maneja todos los aspectos de aspirantes
 * @package  SistemaIGA
 * @subpackage Aspirantes
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vaspirantes extends Taspirantes {

    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */
    
    /* PUBLIC FUCNTIONS */
    
    /**
     * retorna lista de telefonos de aspirantes
     * @access public
     * @return array de telefonos
     */
    function getTelefonos($default = false) {
        $this->oConnection->select('telefonos.*,aspirantes_telefonos.default');
        $this->oConnection->from($this->nombreTabla);
        $this->oConnection->join('aspirantes_telefonos', 'aspirantes_telefonos.cod_aspirante = aspirantes.codigo and aspirantes_telefonos.default = 1');
        $this->oConnection->join('telefonos', 'aspirantes_telefonos.cod_telefono = telefonos.codigo');
        $this->oConnection->where('aspirantes.codigo', $this->codigo);
        $this->oConnection->where('telefonos.baja',0);
        if ($default){
            $this->oConnection->where("aspirantes_telefonos.default", 1);
        }       
        
        $query = $this->oConnection->get();
        return $arrResp = $query->result_array();
    }
    
    function getTodosLosTelefonos(){
        $this->oConnection->select('telefonos.*,aspirantes_telefonos.default');
        $this->oConnection->from($this->nombreTabla);
        $this->oConnection->join('aspirantes_telefonos', 'aspirantes_telefonos.cod_aspirante = aspirantes.codigo');
        $this->oConnection->join('telefonos', 'aspirantes_telefonos.cod_telefono = telefonos.codigo');
        $this->oConnection->where('aspirantes.codigo', $this->codigo);
        $this->oConnection->where('telefonos.baja',0);
        $query = $this->oConnection->get();
        return $arrResp = $query->result_array();
    }
    /**
     * Marca registro de envio de email de bienvenida a un aspirante
     * 
     * @return boolean
     */
    public function marcarEnvioBienvenida(){
        $this->oConnection->where("codigo", $this->codigo);
        return $this->oConnection->update($this->nombreTabla, array("email_enviado" => 1));        
    }
    
    function getCodigoTelefonoDefault(){
        $this->oConnection->select('aspirantes_telefonos.cod_telefono');
        $this->oConnection->from('aspirantes_telefonos');
        $this->oConnection->where('aspirantes_telefonos.cod_aspirante',  $this->codigo);
        $this->oConnection->where('aspirantes_telefonos.default',1);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /**
     * asigna un telefono a un aspirante
     * @access public
     */
    function setTelefonosAspirante($cod_telefono, $default) {
        $array=array(
            "cod_aspirante"=>  $this->codigo,
            "cod_telefono"=> $cod_telefono,
            "default"=>$default
        );
        return $this->oConnection->insert('aspirantes_telefonos', $array);
    }
    
    function updateTelefonoAspirante($cod_tel,$default){
        $array=array(
            "default"=>$default
        );
        $this->oConnection->where('cod_aspirante',  $this->codigo);
        $this->oConnection->where('cod_telefono',$cod_tel);
        $this->oConnection->update('aspirantes_telefonos',$array);
    }
    
    function vaciarTelefonos(){
        $arrTelefonos = $this->getTodosLosTelefonos();
        $this->oConnection->where('cod_aspirante', $this->codigo);
        $this->oConnection->delete('aspirantes_telefonos');
        foreach($arrTelefonos as $tele){
            $this->oConnection->where('codigo', $tele['codigo']);
            $this->oConnection->delete('telefonos');
        }
    }

        //siwakawa
    public function getTurnos2(){
        $this->oConnection->select("aspirantes_cursos.cod_turno");
        $this->oConnection->from("aspirantes_cursos");
        $this->oConnection->where("aspirantes_cursos.cod_aspirante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getModalidades2(){
        $this->oConnection->select("aspirantes_cursos.modalidad");
        $this->oConnection->from("aspirantes_cursos");
        $this->oConnection->where("aspirantes_cursos.cod_aspirante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getPeriodos(){
        $this->oConnection->select("aspirantes_cursos.periodo");
        $this->oConnection->from("aspirantes_cursos");
        $this->oConnection->where("aspirantes_cursos.cod_aspirante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getCursosDeInteres(){
        $this->oConnection->select("aspirantes_cursos.cod_curso");
        $this->oConnection->from("aspirantes_cursos");
        $this->oConnection->where("aspirantes_cursos.cod_aspirante", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function setCursosDeInteres(array $cursos_interes, array $turnos, array $periodos, array $modalidades){    
        $this->oConnection->where("aspirantes_cursos.cod_aspirante", $this->codigo);
        $resp = $this->oConnection->delete("aspirantes_cursos");

        foreach ($cursos_interes as $index => $curso){
             $resp = $resp && $this->oConnection->insert("aspirantes_cursos", array("cod_aspirante" => $this->codigo, "cod_curso" => $curso, "cod_turno" => $turnos[$index], "periodo" => $periodos[$index], "modalidad" => $modalidades[$index]) );
        }
        return $resp;
    }
    
    /* STATIC FUNCTIONS */
    
    /**
     * retorna lista de aspirantes
     * @access public
     * @return array de aspirantes
     */
    static function listarAspiranteDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike, array $arrLimit = null, 
            array $arrSort = null, $contar = false, $separador = null, $fechaDesde = null, $fechaHasta = null, $curso = null, 
            $tipoContacto = null, $medio = null, $turno = null, $esAlumno = null, $pais = null) {
        $manana = lang("manana");
        $tarde = lang("tarde");
        $noche = lang("noche");
        $indistinto = lang("indistinto");
        $descripcion = 'descripcion_'.get_idioma();
        $nombreApellido = formatearNombreAspQuery();
        $nombreUsuario = formatearNombreUsuarioSistQuery();
        $conexion->select('id_aspirante');
        $conexion->from('aspirantes_alumnos');
        $conexion->where('aspirantes_alumnos.id_aspirante = aspirantes.codigo');
        $conexion->limit(1);
        $subquery = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("general.empresas_telefonicas.nombre", false);
        $conexion->from("general.empresas_telefonicas");
        $conexion->join("telefonos", "general.empresas_telefonicas.codigo = telefonos.empresa");
        $conexion->join("aspirantes_telefonos", "aspirantes_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("aspirantes_telefonos.cod_aspirante = aspirantes.codigo");
        $conexion->order_by("aspirantes_telefonos.default", "desc");
        $conexion->limit(1);
        $sqTelefonoEmpresa = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("CONCAT(telefonos.cod_area, ' ', telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("aspirantes_telefonos", "aspirantes_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("aspirantes_telefonos.cod_aspirante = aspirantes.codigo");
        $conexion->order_by("aspirantes_telefonos.default", "DESC");
        //habia un error al devolver la consulta sin ningun filtro
        $conexion->limit(1);
        //habia un error al devolver la consulta sin ningun filtro
        $sqTelefono = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("aspirantes.*,CONCAT($nombreApellido) as nombre_apellido",false);
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ', aspirantes.documento) AS aspirante_documento", false);
        $conexion->select("general.como_nos_conocio.$descripcion as como_nos_conocio");
        $conexion->select("IFNULL(($subquery),' ') as pasado_alumno",false);
        $conexion->select("CONCAT($nombreUsuario) as nombre_usuario",false);
        $conexion->select("general.cursos.nombre_es AS nombre_curso", false);
        $conexion->select("IF (aspirantes_cursos.cod_turno = 1, '$manana', ".
	"IF (aspirantes_cursos.cod_turno = 2, '$tarde', ".
		"IF (aspirantes_cursos.cod_turno = 3, '$noche', ".
			"IF (aspirantes_cursos.cod_turno = 4, '$indistinto', '')))) AS turno", false);

        if($pais == 2)
        {
            $conexion->select("($sqTelefonoEmpresa) AS telefono_empresa", false);
        }
        $conexion->select("($sqTelefono) AS telefono", false);
        $conexion->from("aspirantes");
        $conexion->join('general.como_nos_conocio','general.como_nos_conocio.codigo = aspirantes.comonosconocio');
        $conexion->join('general.usuarios_sistema','general.usuarios_sistema.codigo = aspirantes.usuario_creador');
        $conexion->join('aspirantes_cursos','aspirantes_cursos.cod_aspirante = aspirantes.codigo', "LEFT");
        $conexion->join('general.cursos','general.cursos.codigo = aspirantes_cursos.cod_curso', "LEFT");
        $conexion->join('general.documentos_tipos', 'general.documentos_tipos.codigo = aspirantes.tipo', 'LEFT');
        if ($fechaDesde != null){
            $conexion->where("DATE(aspirantes.fechaalta) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(aspirantes.fechaalta) <=", $fechaHasta);
        }
        if ($medio != null){
            $conexion->where("aspirantes.comonosconocio", $medio);
        }
        if ($tipoContacto != null){
            $conexion->where("aspirantes.tipo_contacto", $tipoContacto);
        }
        if ($curso != null){
            $conexion->where("general.cursos.codigo", $curso);
        } 
        if ($turno != null){
            $conexion->where("aspirantes_cursos.cod_turno", $turno);
        }
        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value) {
                if($key == 'nombre_apellido'){
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";                     
                }else{
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0){
                $having = "(".implode(" OR ", $arrTemp).")";
                $conexion->having($having);
            }
        }
        if ($esAlumno !== null){
            if ($esAlumno){
                $conexion->having("pasado_alumno <>", " ");
            } else {
                $conexion->having("pasado_alumno =", " ");
            }
        }
        if (count($arrLimit) > 0) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if (count($arrSort) > 0) {

            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        $query = $conexion->get();
       // echo $conexion->last_query(); //die();
        //echo '\n\n\n';
        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
//            echo $conexion->last_query(); die();
        }
        return $arrResp;
    }

    static function getListadoCentroReportes(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false,
            $search = null, array $searchFields = null, $fechaDesde = null, $fechaHasta = null){
        
        $aColumns = array();
        $aColumns['apellido']['order'] = "aspirantes.apellido";
        $aColumns['nombre']['order'] = "aspirantes.nombre";
        $aColumns['email']['order'] = "aspirantes.email";
        $aColumns['fechaalta']['order'] = "aspirantes.fechaalta";
        $aColumns['localidad_nombre']['order'] = "general.localidades.nombre";
        $aColumns['como_nos_conocio_nombre_es']['order'] = "general.como_nos_conocio.descripcion_es";        
        $aColumns['documento']['order'] = 'documento';
        $aColumns['direccion']['order'] = 'direccion';
        $aColumns['telefono']['order'] = 'telefono';
        $aColumns['apellido']['having'] = "aspirantes.apellido";
        $aColumns['nombre']['having'] = "aspirantes.nombre";
        $aColumns['email']['having'] = "aspirantes.email";
        $aColumns['fechaalta']['having'] = "fechaalta";
        $aColumns['localidad_nombre']['having'] = "general.localidades.nombre";
        $aColumns['como_nos_conocio_nombre_es']['having'] = "general.como_nos_conocio.descripcion_es";        
        $aColumns['documento']['having'] = 'documento';
        $aColumns['direccion']['having'] = 'direccion';
        $aColumns['telefono']['having'] = 'telefono';
        
        $conexion->select("CONCAT(telefonos.cod_area, ' ', telefonos.numero)", false);
        $conexion->from("telefonos");
        $conexion->join("aspirantes_telefonos", "aspirantes_telefonos.cod_telefono = telefonos.codigo");
        $conexion->where("aspirantes_telefonos.cod_aspirante = aspirantes.codigo");
        $conexion->order_by("telefonos.codigo", "ASC");
        $conexion->limit(1, 0);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("aspirantes.codigo");
        $conexion->select("aspirantes.apellido");
        $conexion->select("aspirantes.nombre");
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ', aspirantes.documento) AS documento", false);
        $conexion->select("CONCAT(aspirantes.calle, ' ', aspirantes.calle_numero, ' ', aspirantes.calle_complemento) AS direccion", false);
        $conexion->select("aspirantes.email");
        $conexion->select("CONCAT(LPAD(DAY(aspirantes.fechaalta), 2, 0), '/', LPAD(MONTH(aspirantes.fechaalta), 2, 0), '/', YEAR(aspirantes.fechaalta)) AS fechaalta", false);
        $conexion->select("general.localidades.nombre AS localidad_nombre");
        $conexion->select("general.como_nos_conocio.descripcion_es AS como_nos_conocio_nombre_es");
        $conexion->select("($subquery) AS telefono", false);
        $conexion->from("aspirantes");
        $conexion->join("general.localidades", "general.localidades.id = aspirantes.cod_localidad", "left");
        $conexion->join("general.como_nos_conocio", "general.como_nos_conocio.codigo = aspirantes.comonosconocio");
        $conexion->join("general.documentos_tipos", "general.documentos_tipos.codigo = aspirantes.tipo", "left");
        if ($fechaDesde != null)
            $conexion->where("DATE(aspirantes.fechaalta) >=", $fechaDesde);
        if ($fechaHasta != null)
            $conexion->where("DATE(aspirantes.fechaalta) <=", $fechaHasta);
        if ($search != null){            
            foreach ($aColumns AS $key => $tableFields ){
                if ($searchFields == null || in_array($key, $searchFields)){
                    $conexion->or_having($tableFields['having']." LIKE ", "%$search%");
                }
            }            
        }        
        if (!$contar){
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
    
    public function getDomicilioFormateado(){
        return funciones::formatearDomicilio($this->calle, $this->calle_numero, $this->calle_complemento);
    }
    
    public function getPresupuestosAspirante(){
        $this->oConnection->select('IFNULL(MAX(planes_financiacion.nro_cuota), 1)',false);
        $this->oConnection->from('planes_financiacion');
        $this->oConnection->where('planes_financiacion.codigo_plan = planes_pago.codigo');
        $this->oConnection->where('planes_financiacion.codigo_financiacion = financiacion.codigo');
        $this->oConnection->where('planes_financiacion.codigo_concepto = conceptos.codigo');
        $subquery = $this->oConnection->return_query();
        $this->oConnection->resetear();
        
        $this->oConnection->select(' IFNULL(SUM(planes_financiacion.valor), 0)',false);
        $this->oConnection->from('planes_financiacion');
        $this->oConnection->where('planes_financiacion.codigo_plan = planes_pago.codigo');
        $this->oConnection->where('planes_financiacion.codigo_financiacion = financiacion.codigo');
        $this->oConnection->where('planes_financiacion.codigo_concepto = conceptos.codigo');
        $subquery2 = $this->oConnection->return_query();
        $this->oConnection->resetear();
        
        $this->oConnection->select("presupuestos.codigo as codigo_presupuesto,
                                                        presupuestos.fecha,
                                                        planes_pago.codigo as codigo_plan,
                                                        financiacion.codigo as codigo_financiacion,
                                                        conceptos.codigo as concepto_codigo,
                                                        general.cursos.nombre_es,
                                                        general.cursos.nombre_pt,
                                                        general.cursos.nombre_in,
                                                        planes_pago.nombre as nombre_plan, conceptos.key AS nombre_concepto, ($subquery) as cantidad_cuotas, ($subquery2) as valor_total_concepto ",false);
        $this->oConnection->from('aspirantes_presupuestos');
        $this->oConnection->join('aspirantes','aspirantes.codigo = aspirantes_presupuestos.cod_aspirante');
        $this->oConnection->join('presupuestos','presupuestos.codigo = aspirantes_presupuestos.cod_presupuesto');
        $this->oConnection->join('planes_pago','planes_pago.codigo = presupuestos.cod_plan');
        $this->oConnection->join('comisiones','comisiones.codigo = presupuestos.codcomision');
        $this->oConnection->join('presupuestos_detalle','presupuestos_detalle.codigo_presupuesto = presupuestos.codigo');
        $this->oConnection->join('conceptos','conceptos.codigo = presupuestos_detalle.codigo_concepto');
        $this->oConnection->join('financiacion','financiacion.codigo = presupuestos_detalle.codigo_financiacion');
        $this->oConnection->join('general.planes_academicos','general.planes_academicos.codigo = comisiones.cod_plan_academico');
        $this->oConnection->join('general.cursos','general.cursos.codigo = general.planes_academicos.cod_curso');
        $this->oConnection->where('aspirantes.codigo',  $this->codigo);
        $this->oConnection->group_by('presupuestos.codigo');
        $this->oConnection->group_by('conceptos.codigo');
        $this->oConnection->group_by('financiacion.codigo');
        $this->oConnection->group_by('planes_pago.codigo');
        $this->oConnection->order_by('presupuestos.codigo','desc');
        
        $query = $this->oConnection->get();
  
        return $query->result_array();
    }
    
    public function consultarAspiranteAlumno(){
        $this->oConnection->select('aspirantes_alumnos.id_aspirante');
        $this->oConnection->from('aspirantes');
        $this->oConnection->join('aspirantes_alumnos','aspirantes_alumnos.id_aspirante = aspirantes.codigo');
        $this->oConnection->where('aspirantes.codigo',  $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
        
    }
    
    /* La siguiente function esta siendo accedida desde un WEB SERVICES NO MODIFICAR, ELIMINAR O COMENTAR */
    static function getReporteWS(CI_DB_mysqli_driver $conexion, $fechaDesde = null){
        $conexion->select("DATE(fechaalta) AS fecha", false);
        $conexion->select("tipo_contacto");
        $conexion->select("COUNT(codigo) AS cantidad", false);
        $conexion->from("aspirantes");
        if ($fechaDesde != null) $conexion->where("DATE(fechaalta) >=", $fechaDesde);
        $conexion->group_by("DATE(fechaalta)");
        $conexion->group_by("tipo_contacto");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    //Ticket 4524 - agregar turnos
    static function getTurnos(CI_DB_mysqli_driver $conexion, $condicion)
    {
        $conexion->select("general.turnos.id, general.turnos.nombre");
        $conexion->from("general.turnos");
        $conexion->where("general.turnos.estado =", $condicion);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getModalidades(CI_DB_mysqli_driver $conexion, $codigo)
    {
        $conexion->select("general.planes_academicos.codigo");
        $conexion->from("general.planes_academicos");
        $conexion->where("cod_curso",$codigo);
        $conexion->where("estado","habilitado");
        $var = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("general.planes_academicos_filiales.modalidad");
        $conexion->distinct();
        $conexion->from("general.planes_academicos_filiales");
        $conexion->where("general.planes_academicos_filiales.cod_filial","20");
        $conexion->where("general.planes_academicos_filiales.cod_plan_academico IN (".$var.")");
        $query = $conexion->get();
        return $query->result_array();
    }
   
    
    static function setTurnos(CI_DB_mysqli_driver $conexion, $condicion)
    {
        $conexion->select("general.turnos.id, general.turnos.nombre");
        $conexion->from("general.turnos");
        $conexion->where("general.turnos.estado =", $condicion);
        $query = $conexion->get();
        return $query->result_array();
        
        
        $this->oConnection->where("aspirantes_cursos.cod_aspirante", $this->codigo);
        $resp = $this->oConnection->delete("aspirantes_cursos");
        foreach ($cursos_interes as $curso){
            $resp = $resp && $this->oConnection->insert("aspirantes_cursos", array("cod_aspirante" => $this->codigo, "cod_curso" => $curso));
        }
        return $resp;
        
    }
      
       
}

