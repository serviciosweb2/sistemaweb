<?php

/**                             
* Class Vmails_consultas
*
*Class  Vmails_consultas maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/

class Vmails_consultas extends Tmails_consultas{

    static private $estadonoconcretada = 'noconcretada';
    static private $estadoeliminada = 'eliminada';
    static private $estadoabierta = "abierta";
    static private $estadoconcretada = "concretada";
    static private $estadocerrado = "cerrado";
    static private $estadopendiente = "pendiente";
    
    static private $arrayEstados = array('noconcretada','eliminada','abierta','concretada','cerrado','pendiente');
    static private $arrayNuevosEstados = array('leidos','no_leidos','respondidos');

    /* CONSTRUCTOR */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    /**
     * Esta funcion solo debe utilizarse en el modulo de actualizacion ya que es necesario mantener el codigo con la tabla original
     * 
     * @param int $codigo
     */
        
    /**
     * NO BORRAR ESTA FUNCION
     * Esta funcion solo debe utilizarse en el modulo de actualizacion ya que es necesario mantener el codigo con la tabla original
     * NO UTILIZAR SIN VER SU FUNCIONAMIENTO
     */
    public function guardadoForzado($codigoConsulta){
        $arrTemp = $this->_getArrayDeObjeto();
        $arrTemp['codigo'] = $codigoConsulta;
        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)){
            return true;
        } else {
            return false;
        }
    }
    
    /* PUBLIC FUNCTIONS */
    
    /**
     * Lista el historico de seguimiento de respuestas de la consulta instanciada
     * 
     * @return array;
     */
    function getRespuestas(){
        $this->oConnection->select("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido)", false);
        $this->oConnection->from("general.usuarios_sistema");
        $this->oConnection->where("general.usuarios_sistema.codigo = mails_respuesta_consultas.id_usuario");
        $subQuery = $this->oConnection->return_query();
        $this->oConnection->resetear();
        
        $this->oConnection->select("mails_consultas.mails_consultas.asunto");
        $this->oConnection->select("mails_consultas.mails_respuesta_consultas.codigo");
        $this->oConnection->select("mails_consultas.mails_respuesta_consultas.html_respuesta");
        $this->oConnection->select("mails_consultas.mails_respuesta_consultas.cod_consulta");
        $this->oConnection->select("mails_consultas.mails_respuesta_consultas.emisor");
        $this->oConnection->select("mails_consultas.mails_respuesta_consultas.fecha_hora");
        $this->oConnection->select("IF (mails_consultas.mails_respuesta_consultas.emisor = 0, mails_consultas.mails_consultas.nombre, ($subQuery)) AS nombre_contacto", false);
        $this->oConnection->from("mails_consultas.mails_consultas");
        $this->oConnection->join("mails_consultas.mails_respuesta_consultas", "mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.mails_consultas.codigo");
        $this->oConnection->where("mails_consultas.mails_consultas.codigo", $this->codigo);
        $this->oConnection->order_by("mails_consultas.mails_respuesta_consultas.fecha_hora", "DESC");
        $query = $this->oConnection->get();
        return $query->result_array();        
    }
    
    /**
     * Marca una consulta como leida
     * 
     * @return boolean
     */
    function marcarLeida($marcarRespuestas = false){
        $this->oConnection->trans_begin();
        $this->oConnection->update($this->nombreTabla, array("notificar" => 0), array("codigo" => $this->codigo));
        if ($marcarRespuestas){
            $this->oConnection->where("cod_consulta", $this->codigo);
            $this->oConnection->where("vista IS NULL");
            $this->oConnection->update("mails_consultas.mails_respuesta_consultas", array("vista" => date("Y-m-d H:i:s")));
        }
        if ($this->oConnection->trans_status()){
            $this->oConnection->trans_commit();
            return true;
        } else {
            $this->oConnection->trans_rollback();
            return false;
        }
    }
    

    
    /* STATIC FUNCTIONS */
    
    /**
     * Lista los registros de mails_consultas segun los filtros especificados
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param array $wherein
     * @param int $filial
     * @param array $arrLimit
     * @param boolean $contar
     * @param array $arrCondiciones
     * @return array
     */
    static function listarMailsConsultas(CI_DB_mysqli_driver $conexion, $wherein, $filial, $arrLimit = null, $contar = false,$arrCondiciones = null, $having = null, $order = null){
        $conexion->select('count(mails_consultas.mails_consultas.codigo)',false);
        $conexion->from('mails_consultas.mails_consultas');
        $conexion->join("mails_consultas.mails_respuesta_consultas", "mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.mails_consultas.codigo");
        $conexion->where('mails_consultas.mails_consultas.cod_filial',$filial);
        $conexion->where('mails_consultas.mails_consultas.notificar',1);
        $conexion->where_in('mails_consultas.mails_consultas.estado',$wherein);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("COUNT(codigo)", false);
        $conexion->from("mails_consultas.mails_respuesta_consultas");
        $conexion->where("mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.mails_consultas.codigo");
        $conexion->where("mails_consultas.mails_respuesta_consultas.emisor", 1);
        $sqCantidadRespuestas = $conexion->return_query();
        $conexion->resetear();
        //(SELECT count(codigo) - 1 FROM mails_consultas.mails_respuesta_consultas WHERE mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.mails_consultas.codigo) AS cantidad_respuestas
        
        $conexion->select('mails_consultas.mails_consultas.codigo');
        $conexion->select('mails_consultas.mails_consultas.cod_filial');
        $conexion->select('ifnull(mails_consultas.mails_consultas.nombre,"'.lang('no_definido').'") as nombre', false, null);
        $conexion->select('mails_consultas.mails_consultas.estado');
        $conexion->select('mails_consultas.mails_consultas.cod_curso_asunto');
        $conexion->select('mails_consultas.mails_consultas.tipo_asunto');
        $conexion->select('mails_consultas.mails_consultas.asunto');
        $conexion->select('max(mails_consultas.mails_respuesta_consultas.fecha_hora) as fechahora');//fecha de respuesta
        $conexion->select('min(mails_consultas.mails_respuesta_consultas.fecha_hora) as fechahoraconsulta');//fecha de consulta
        $conexion->select('mails_consultas.mails_consultas.mail');
        $conexion->select('mails_consultas.mails_consultas.notificar');
        $conexion->select("($subquery) as noLeidos", false);
        $conexion->select('mails_consultas.mails_consultas.destacar');
        $conexion->select('mails_consultas.mails_consultas.telefono');
        $conexion->select("($sqCantidadRespuestas) AS cantidad_respuestas", false);
        $conexion->from('mails_consultas.mails_consultas');
        $conexion->join("mails_consultas.mails_respuesta_consultas", "mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.mails_consultas.codigo");
        $conexion->where_in('mails_consultas.mails_consultas.estado',$wherein);
        $conexion->where('mails_consultas.mails_consultas.cod_filial',$filial);
        $conexion->group_by('mails_consultas.mails_consultas.codigo');
        if($order != null && $order != '') {
            $conexion->order_by($order['field'], $order['order']);
        }
        else {
            if($wherein == 'eliminado'){
                $conexion->order_by('mails_consultas.mails_consultas.fechahora','desc');
            }
            $conexion->order_by('mails_consultas.mails_consultas.notificar','desc');
            if(!$contar) {
                //con esta ordernación muestra primero no leidos, leidos no respondidos , respondidos
                $conexion->order_by('(cantidad_respuestas = 0)','desc', false);
            }
            $conexion->order_by('mails_consultas.mails_consultas.fechahora','desc');
        }

        if($arrCondiciones != null){
            $conexion->where($arrCondiciones);
        }
        if ($arrLimit != null && !$contar) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if($having != null) {
            $conexion->having($having);
        }
        $query = $conexion->get();
        if($contar) {
            return $query->num_rows();
        } else {
            return $query->result_array();
        }
    }
    
    //'noconcretada','eliminada','abierta','concretada','cerrado','pendiente'
    static function getEstadoNoconcretado(){
        return self::$estadonoconcretada;
    }
    
    static function getEstadoEliminado(){
        return self::$estadoeliminada;
    }
    
    static function getEstadoAbierto(){
        return self::$estadoabierta;
    }
    
    static function getEstadoConcretado(){
        return self::$estadoconcretada;
    }
    
    static function getEstadoCerrado(){
        return self::$estadocerrado;
    }
    
    static function getEstadoPendiente(){
        return self::$estadopendiente;
    }
    
    static function getEstados(){
        return self::$arrayEstados;
    }

    static function getNuevosEstados(){
        return self::$arrayNuevosEstados;
    }

    static function getMaxCodigo(CI_DB_mysqli_driver $conexion, $idFilial){
        $conexion->select("IFNULL(max(codigo), 0) AS max_codigo", false);
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->where("cod_filial", $idFilial);
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return $arrResp[0]['max_codigo'];
    }
 
    static function listarMails_consultas_sincronizar(CI_DB_mysqli_driver $conexion, array $condiciones = null){
        $conexion->select("centralsistema.mails_consultas.*", false);
        $conexion->from("centralsistema.mails_consultas");
        if ($condiciones != null)
            $conexion->where($condiciones);
        $query = $conexion->get();
        return $query->result_array();
    }                        
    static function getCantidadesPorFilial(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null){
        $conexion->select("mails_consultas.mails_consultas.cod_filial");
        $conexion->select("general.filiales.nombre");
        $conexion->select("COUNT(mails_consultas.mails_consultas.codigo) AS cantidad", false);
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->join("general.filiales", "general.filiales.codigo = mails_consultas.mails_consultas.cod_filial");
        $conexion->group_by("mails_consultas.mails_consultas.cod_filial");
        $conexion->order_by("cantidad DESC");
        if ($fechaDesde != null) $conexion->where("DATE(mails_consultas.mails_consultas.fechahora) >=", $fechaDesde);
        if ($fechaHasta != null) $conexion->where("DATE(mails_consultas.mails_consultas.fechahora) <=", $fechaHasta);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function getCantidadesPorAsunto(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $codFilial = null){
        $condiciones = array();
        $sqTBL = "SELECT IF (tipo_asunto = 'curso', 'Cursos', IF (cod_curso_asunto IS NULL, asunto, 'Atencion al alumno')) AS asunto_consulta";
        $sqTBL .= " FROM (`mails_consultas`.`mails_consultas`)";
        if ($fechaDesde != null) $condiciones[] = "DATE(fechahora) >= '$fechaDesde'";
        if ($fechaHasta != null) $condiciones[] = "DATE(fechahora) <= '$fechaHasta'";
        if ($codFilial != null) $condiciones[] = "cod_filial = $codFilial";
        if (count($condiciones) > 0) $sqTBL .= " WHERE ".implode(" AND ", $condiciones);        
        $query = "SELECT asunto_consulta, COUNT(asunto_consulta) AS cantidad";
        $query .= " FROM ($sqTBL) AS tbl";
        $query .= " GROUP BY asunto_consulta";        
        $query .= " ORDER BY cantidad DESC";
        $query = $conexion->query($query);
        return $query->result_array();
    }    
    
    static function getConsultasPorCurso(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $codFilial = null){
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("COUNT(mails_consultas.cod_curso_asunto) AS cantidad");
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->join("general.cursos", "general.cursos.codigo = mails_consultas.cod_curso_asunto");
        $conexion->where("tipo_asunto", "curso");
        if ($fechaDesde != null) $conexion->where("DATE(fechahora) >=", $fechaDesde);
        if ($fechaHasta != null) $conexion->where("DATE(fechahora) <=", $fechaHasta);
        if ($codFilial != null) $conexion->where("cod_filial", $codFilial);
        $conexion->group_by("mails_consultas.cod_curso_asunto");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function respuestaYaRegistrada(CI_DB_mysqli_driver $conexion, $codConsulta, $fechaHora){
        $conexion->select('codigo');
        $conexion->from('mails_respuesta_consultas');
        $conexion->where("cod_consulta", $codConsulta);
        $conexion->where("fecha_hora", $fechaHora);
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        return count($arrTemp) > 0 && isset($arrTemp[0]['codigo']) && $arrTemp[0]['codigo'] > 0;
    }
    
    static public function getCodigoConsutaDesdeEmail(CI_DB_mysqli_driver $conexion, $email){
        $conexion->select("MAX(codigo) AS cod_consulta" , false);
        $conexion->from("mails_consultas");
        $conexion->where("mail", $email);
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        if (count($arrTemp) > 0 && isset($arrTemp[0]['cod_consulta'])) {
            return $arrTemp[0]['cod_consulta'];
        } else {
            return -1;
        }
    }
    
    static function listarDataTables(CI_DB_mysqli_driver $conexion, $arrLimit = null, $arrSort = null, $contar = false, 
            $search = null, array $searchFields = null, $codFilial = null, $fechaDesde = null, $fechaHasta = null, $estado = null, $codConsulta = null){
        $aColumns = array();
        $aColumns['fecha']['order'] = "fechahora";
        $aColumns['nombre']['order'] = "mails_consultas.mails_consultas.nombre";
        $aColumns['asunto']['order'] = "mails_consultas.mails_consultas.asunto";
        $aColumns['mensaje']['order'] = "mensaje";
        $aColumns['mail']['order'] = "mails_consultas.mails_consultas.mail";
        $aColumns['telefono']['order'] = "mails_consultas.mails_consultas.telefono";
        $aColumns['estado']['order'] = "mails_consultas.mails_consultas.estado";
        $aColumns['fecha']['having'] = "fechahora";
        $aColumns['nombre']['having'] = "mails_consultas.mails_consultas.nombre";
        $aColumns['asunto']['having'] = "mails_consultas.mails_consultas.asunto";
        $aColumns['mensaje']['having'] = "mensaje";
        $aColumns['mail']['having'] = "mails_consultas.mails_consultas.mail";
        $aColumns['telefono']['having'] = "mails_consultas.mails_consultas.telefono";
        $aColumns['estado']['having'] = "mails_consultas.mails_consultas.estado";
        
        
        $conexion->select("mails_consultas.mails_respuesta_consultas.html_respuesta");
        $conexion->from("mails_consultas.mails_respuesta_consultas");
        $conexion->where("mails_consultas.mails_respuesta_consultas.cod_consulta = mails_consultas.mails_consultas.codigo");
        $conexion->where("mails_consultas.mails_respuesta_consultas.emisor = 0");
        $conexion->order_by("mails_consultas.mails_respuesta_consultas.codigo", "DESC");
        $conexion->limit(1);
        $sqMensaje = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("mails_consultas.mails_consultas.*", false);
        $conexion->select("DATE_FORMAT(DATE_SUB(mails_consultas.mails_consultas.fechahora, INTERVAL 3 HOUR), '%d/%m/%Y %H:%i:%s') as fecha", false);
        $conexion->select("($sqMensaje) AS mensaje", false);
        $conexion->from("mails_consultas.mails_consultas");
        if ($codFilial != null){
            $conexion->where("mails_consultas.mails_consultas.cod_filial", $codFilial);
        }
        if ($fechaDesde != null){
            $conexion->where("DATE_SUB(mails_consultas.mails_consultas.fechahora, INTERVAL 3 HOUR) >=", $fechaDesde . " 00:00:00");
        }
        if ($fechaHasta != null){
            $conexion->where("DATE_SUB(mails_consultas.mails_consultas.fechahora, INTERVAL 3 HOUR) <=", $fechaHasta . " 23:59:59");
        }
        if ($estado != null){
            $conexion->where("mails_consultas.mails_consultas.estado", $estado);
        }
        if ($codConsulta != null){
            $conexion->where("mails_consultas.mails_consultas.codigo", $codConsulta);
        }        
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
    
    static function getFilialesEstados(CI_DB_mysqli_driver $conexion, $fechaDesde = null, $fechaHasta = null, $codFilial = null){
        $conexion->select("general.filiales.codigo");
        $conexion->select("general.filiales.nombre");
        $conexion->select("mails_consultas.mails_consultas.estado");
        $conexion->select("COUNT(mails_consultas.mails_consultas.codigo) AS cantidad");
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->join("general.filiales", "general.filiales.codigo = mails_consultas.mails_consultas.cod_filial");
        if ($fechaDesde != null){
            $conexion->where("DATE(mails_consultas.mails_consultas.fechahora) >=", $fechaDesde);
        }
        if ($fechaHasta != null){
            $conexion->where("DATE(mails_consultas.mails_consultas.fechahora) <=", $fechaHasta);
        }
        if ($codFilial != null){
            $tipoConsulta = is_array($codFilial) ? "where_in" : "where";
            $conexion->$tipoConsulta("mails_consultas.mails_consultas.cod_filial", $codFilial);
        }
        $conexion->group_by("mails_consultas.mails_consultas.cod_filial");
        $conexion->group_by("mails_consultas.mails_consultas.estado");
        $conexion->order_by("general.filiales.nombre ASC");
        $query = $conexion->get();
        return $query->result_array();
    }
    

    //Todo: Acordarme de buscar un nombre decente.
    public static function listarConsultasWebWS(CI_DB_mysqli_driver $conexion, 
                                                $fechaDesde, 
                                                $fechaHasta, 
                                                $cursos, 
                                                $cursosCortos, 
                                                $tipo=null){
        $todosLosCursos = array_merge(
            $cursos?$cursos:array(),
            $cursos?array():$cursosCortos
        );
        file_put_contents('/var/www/html/logsigacloud/reporte_consultasweb_datatable_queries',"-------------Begin query-----------\n",FILE_APPEND);
        $conexion->select("cur0.codigo as codigo_curso");
        $conexion->select("cur0.nombre_es as nombre_es");
        $conexion->select("cur0.nombre_in as nombre_in");
        $conexion->select("cur0.nombre_pt as nombre_pt");
        $conexion->select("mc0.cod_filial as codigo_filial");
        $conexion->select("fil0.nombre as nombre_filial");
        $conexion->select("fil0.pais as pais_filial");
        $conexion->select("COUNT(mc0.cod_curso_asunto) AS cantidad");
        $conexion->from("mails_consultas.mails_consultas as mc0");
        $conexion->join("general.cursos as cur0", "cur0.codigo = mc0.cod_curso_asunto");
        $conexion->join("general.filiales as fil0", "fil0.codigo = mc0.cod_filial");
        $conexion->where("mc0.tipo_asunto", "curso");
        if(count($todosLosCursos) > 0)
            $conexion->where("mc0.cod_curso_asunto in (".implode(",", $todosLosCursos). ")");
        if ($fechaDesde != null) $conexion->where("DATE(mc0.fechahora) >=", $fechaDesde);
        if ($fechaHasta != null) $conexion->where("DATE(mc0.fechahora) <=", $fechaHasta);
        $conexion->group_by("mc0.cod_filial");
        $conexion->group_by("mc0.cod_curso_asunto");
        $queryCursos = $conexion->return_query();
        $conexion->resetear();
        if($cursosCortos != null && count($cursosCortos) > 0){
            $conexion->select("'300' as codigo_curso", false);
            $conexion->select("'Cursos cortos' as nombre_es", false);
            $conexion->select("'Cursos cortos' as nombre_in", false);
            $conexion->select("'Cursos cortos' as nombre_pt", false);
            $conexion->select("mc1.cod_filial as codigo_filial", false);
            $conexion->select("fil1.nombre as nombre_filial", false);
            $conexion->select("fil1.pais as pais_filial");
            $conexion->select("COUNT(*) as cantidad", false);
            $conexion->from("mails_consultas.mails_consultas as mc1");
            $conexion->join("general.cursos as cur1", "cur1.codigo = mc1.cod_curso_asunto");
            $conexion->join("general.filiales as fil1", "fil1.codigo = mc1.cod_filial");
            $conexion->where("mc1.tipo_asunto", "curso");
            $conexion->where("mc1.cod_curso_asunto in (".implode(",", $cursosCortos). ")");
            if ($fechaDesde != null) $conexion->where("DATE_SUB(mc1.fechahora, INTERVAL 3 HOUR) >=", $fechaDesde . "00:00:00");
            if ($fechaHasta != null) $conexion->where("DATE_SUB(mc1.fechahora, INTERVAL 3 HOUR) <=", $fechaHasta . "23:59:59");
            $conexion->group_by("mc1.cod_filial");
            $queryCursosCortos = $conexion->return_query();
            $conexion->resetear();
            $queryCursos = "($queryCursos) UNION ($queryCursosCortos)";
        }
        $query = null;
        if($cursos != null && in_array(301, $cursos)){
            $conexion->select("'301' as codigo_curso", false);
            $conexion->select("'Atencion al alumno' as nombre_es", false);
            $conexion->select("'Student care' as nombre_in", false);
            $conexion->select("'Atencion al alumninho?' as nombre_pt", false);
            $conexion->select("mc2.cod_filial as codigo_filial", false);
            $conexion->select("general.filiales.nombre as nombre_filial", false);
            $conexion->select("general.filiales.pais as pais_filial");
            $conexion->select("COUNT(*) AS cantidad", false);
            $conexion->from("mails_consultas.mails_consultas as mc2");
            $conexion->join("general.filiales", "general.filiales.codigo = mc2.cod_filial");
            $conexion->where("mc2.tipo_asunto <> 'curso'");
            if ($fechaDesde != null) $conexion->where("DATE(mc2.fechahora) >=", $fechaDesde);
            if ($fechaHasta != null) $conexion->where("DATE(mc2.fechahora) <=", $fechaHasta);
            $conexion->group_by("mc2.cod_filial");
            $conexion->_escape_identifiers(false);
            $queryGeneral = $conexion->return_query();
            $conexion->resetear();
            $conexion->_escape_identifiers(true);
            if($tipo = "por_pais"){
                $queryText = "($queryCursos) UNION ($queryGeneral) ORDER BY pais_filial, nombre_filial, codigo_curso";
                file_put_contents('/var/www/html/logsigacloud/reporte_consultasweb_datatable_queries',$queryText,FILE_APPEND);
                $query = $conexion->query($querytext);
            } else {
                $queryText = "($queryCursos) UNION ($queryGeneral) ORDER BY nombre_filial, codigo_curso";
                file_put_contents('/var/www/html/logsigacloud/reporte_consultasweb_datatable_queries',$queryText,FILE_APPEND);
                $query = $conexion->query("($queryCursos) UNION ($queryGeneral) ORDER BY nombre_filial, codigo_curso");
            }
        }
        else {
            if($tipo == "por_pais"){
                $queryText = "SELECT * FROM (($queryCursos) as eachderivedtablemusthaveitsownalias) ORDER BY pais_filial, nombre_filial, codigo_curso";
                file_put_contents('/var/www/html/logsigacloud/reporte_consultasweb_datatable_queries',$queryText,FILE_APPEND);
                $query = $conexion->query($queryText);
            } else {
                file_put_contents('/var/www/html/logsigacloud/reporte_consultasweb_datatable_queries',$queryCursos,FILE_APPEND);
                $query = $conexion->query($queryCursos);
            }
        }
        file_put_contents('/var/www/html/logsigacloud/reporte_consultasweb_datatable_queries',"\n-------------End query-----------\n",FILE_APPEND);
        $conexion->resetear();
        $mails =  $query->result_array();
        $categorias = array();
        if($cursos != null){
            $conexion->select("cursos.codigo as codigo");
            $conexion->select("cursos.nombre_es as nombre_es");
            $conexion->select("cursos.nombre_in as nombre_in");
            $conexion->select("cursos.nombre_pt as nombre_pt");
            $conexion->from("general.cursos");
            $conexion->where("cursos.codigo in (" . implode(",", $cursos ) . ")");
            $query = $conexion->get();
            $categorias = $query->result_array();
        }
        $ret = array(
            'cursos' => $categorias,
            'mails' => $mails
        );
        return $ret;
    }
    
    public static function listarAlumnosMatriculadosFacebook(CI_DB_mysqli_driver $conexion, $filial, $fechaDesde = null, $fechaHasta = null, $contar = null) {
        $filial_obj = new Vfiliales($conexion, $filial);
        if(databaseExists($conexion, $filial) && isset($filial_obj->baja) && $filial_obj->baja == 0) {
            $conexion->select('alu.codigo AS cod_alumno');
            $conexion->from('(mails_consultas.mails_consultas mc)');
            $conexion->join(''.$filial.'.aspirantes aspi', 'aspi.email = mc.mail');
            $conexion->join(''.$filial.'.aspirantes_alumnos aa ', 'aa.id_aspirante = aspi.codigo');
            $conexion->join(''.$filial.'.alumnos alu', 'alu.codigo = aa.id_alumno');
            $conexion->where('DATE(mc.fechahora) >=', $fechaDesde);
            $conexion->where('DATE(mc.fechahora) <=', $fechaHasta);
            $conexion->where('mc.cod_filial', $filial);
            $conexion->where('(alu.comonosconocio in(69) OR mc.como_nos_conocio_codigo in(69))');
            $conexion->group_by('mc.mail');
            $conexion->order_by('mc.fechahora DESC');
            $query = $conexion->get();
            if ($contar) {
                $datos = $query->num_rows();
            }
            else {
                $datos = $query->result_array();
            }
            return $datos;
        }
        else {
            return 0;
        }
    }
    
    public static function listarAlumnosMatriculados(CI_DB_mysqli_driver $conexion, $filial, $como_nos_conocio_array, $fechaDesde = null, $fechaHasta = null, $contar = null) {
        if(databaseExists($conexion, $filial)) {
            $conexion->select('alu.codigo AS cod_alumno');
            $conexion->from('(mails_consultas.mails_consultas mc)');
            $conexion->join(''.$filial.'.aspirantes aspi', 'aspi.email = mc.mail');
            $conexion->join(''.$filial.'.aspirantes_alumnos aa ', 'aa.id_aspirante = aspi.codigo');
            $conexion->join(''.$filial.'.alumnos alu', 'alu.codigo = aa.id_alumno');
            $conexion->where('DATE(mc.fechahora) >=', $fechaDesde);
            $conexion->where('DATE(mc.fechahora) <=', $fechaHasta);
            $conexion->where('mc.cod_filial', $filial);
            $conexion->where('(alu.comonosconocio in('.implode(',', $como_nos_conocio_array).') OR mc.como_nos_conocio_codigo in('.implode(',', $como_nos_conocio_array).'))');
            $conexion->group_by('mc.mail');
            $conexion->order_by('mc.fechahora DESC');
            $query = $conexion->get();
            if ($contar) {
                $datos = $query->num_rows();
            }
            else {
                $datos = $query->result_array();
            }
            return $datos;
        }
        else {
            return 0;
        }
    }

    public static function contarMailsConsultasIdFacebookLead(CI_DB_mysqli_driver $conexion, $id_facebook_lead) {
        $conexion->select('*');
        $conexion->from('mails_consultas.mails_consultas');
        $conexion->where('id_facebook_lead', $id_facebook_lead);
        $query = $conexion->get();
        return $query->num_rows();
    }

    public static function ultimaFechaConsultaFacebook(CI_DB_mysqli_driver $conexion) {
        $conexion->select('max(date(fechahora)) as fecha');
        $conexion->from('mails_consultas.mails_consultas');
        $conexion->where('id_facebook_lead is not null', null, false);
        $query = $conexion->get();
        return $query->row_array();
    }
}
