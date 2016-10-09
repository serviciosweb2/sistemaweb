<?php

/**
 * Class Vcertificados
 *
 * Class  Vcertificados maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Certificados
 * @author   Vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcertificados extends Tcertificados {

    static public $estadopendiente = 'pendiente';
    static public $estadopendiente_aprobar = 'pendiente_aprobar';
    static public $estadopendiente_impresion = 'pendiente_impresion';
    static public $estadoen_proceso = 'en_proceso';
    static public $estadofinalizado = 'finalizado';
    static public $estadocancelado = 'cancelado';
    static private $arrayEstados = array("pendiente", "pendiente_aprobar", "pendiente_impresion", "en_proceso", "finalizado", "cancelado");

    function __construct(CI_DB_mysqli_driver $conexion, $cod_matricula_periodo, $cod_certificante) {
        parent::__construct($conexion, $cod_matricula_periodo, $cod_certificante);
    }

    public function setEntregado($codUsuario){
        $this->oConnection->trans_begin();
        $this->oConnection->where("cod_matricula_periodo", $this->cod_matricula_periodo);
        $this->oConnection->where("cod_certificante", $this->cod_certificante);
        $this->oConnection->update("certificados", array("entregado" => 1));
        $myCertificadoHistorico = new Vcertificados_estado_historico($this->oConnection);
        $myCertificadoHistorico->cod_certificante = $this->cod_certificante;
        $myCertificadoHistorico->cod_matricula_periodo = $this->cod_matricula_periodo;
        $myCertificadoHistorico->cod_usuario = $codUsuario;
        $myCertificadoHistorico->estado = 'entregado';
        $myCertificadoHistorico->fecha_hora = date("Y-m-d H:i:s");
        $myCertificadoHistorico->guardarCertificados_estado_historico();
        if ($this->oConnection->trans_status()){
            $this->oConnection->trans_commit();
            $this->entregado = 1;
            return true;
        } else {
            $this->oConnection->trans_rollback();
            return false;
        }
    }
    
    public function unsetEntragado($codUsuario){
        $this->oConnection->trans_begin();
        $this->oConnection->where("cod_matricula_periodo", $this->cod_matricula_periodo);
        $this->oConnection->where("cod_certificante", $this->cod_certificante);
        $this->oConnection->update("certificados", array("entregado" => 0));
        $myCertificadoHistorico = new Vcertificados_estado_historico($this->oConnection);
        $myCertificadoHistorico->cod_certificante = $this->cod_certificante;
        $myCertificadoHistorico->cod_matricula_periodo = $this->cod_matricula_periodo;
        $myCertificadoHistorico->cod_usuario = $codUsuario;
        $myCertificadoHistorico->estado = 'quita_entregado';
        $myCertificadoHistorico->fecha_hora = date("Y-m-d H:i:s");
        $myCertificadoHistorico->guardarCertificados_estado_historico();
        if ($this->oConnection->trans_status()){
            $this->oConnection->trans_commit();
            $this->entregado = 0;
            return true;
        } else {
            $this->oConnection->trans_rollback();
            return false;
        }
    }
    
    public function setRecibido($codUsuario){
        $this->oConnection->where("cod_matricula_periodo", $this->cod_matricula_periodo);
        $this->oConnection->where("cod_certificante", $this->cod_certificante);
        $this->oConnection->update("certificados", array("recibido" => 1));
        $myCertificadoHistorico = new Vcertificados_estado_historico($this->oConnection);
        $myCertificadoHistorico->cod_certificante = $this->cod_certificante;
        $myCertificadoHistorico->cod_matricula_periodo = $this->cod_matricula_periodo;
        $myCertificadoHistorico->cod_usuario = $codUsuario;
        $myCertificadoHistorico->estado = 'recibido';
        $myCertificadoHistorico->fecha_hora = date("Y-m-d H:i:s");
        $myCertificadoHistorico->guardarCertificados_estado_historico();
        if ($this->oConnection->trans_status()){
            $this->recibido = 1;
            $this->oConnection->trans_commit();
            return true;
        } else {
            $this->trans_rollback();
            return false;
        }
    }
    
    public function unsetRecibido($codUsuario){
        $this->oConnection->where("cod_matricula_periodo", $this->cod_matricula_periodo);
        $this->oConnection->where("cod_certificante", $this->cod_certificante);
        $this->oConnection->update("certificados", array("recibido" => 0));
        $myCertificadoHistorico = new Vcertificados_estado_historico($this->oConnection);
        $myCertificadoHistorico->cod_certificante = $this->cod_certificante;
        $myCertificadoHistorico->cod_matricula_periodo = $this->cod_matricula_periodo;
        $myCertificadoHistorico->cod_usuario = $codUsuario;
        $myCertificadoHistorico->estado = 'quita_recibido';
        $myCertificadoHistorico->fecha_hora = date("Y-m-d H:i:s");
        $myCertificadoHistorico->guardarCertificados_estado_historico();
        if ($this->oConnection->trans_status()){
            $this->entregado = 0;
            $this->oConnection->trans_commit();
            return true;
        } else {
            $this->oConnection->trans_rollback();
            return false;
        }
    }
    
    static function getEstadoPendiente() {
        return self::$estadopendiente;
    }

    static function getEstadoPendienteAprobar() {
        return self::$estadopendiente_aprobar;
    }

    static function getEstadoPendienteImpresion() {
        return self::$estadopendiente_impresion;
    }

    static function getEstadoEnProceso() {
        return self::$estadoen_proceso;
    }

    static function getEstadoFinalizado() {
        return self::$estadofinalizado;
    }

    static function getEstadoCancelado() {
        return self::$estadocancelado;
    }

    
    static private function _getSubQueryNombreTitulo(CI_DB_mysqli_driver $conexion, $codFilial){
        $conexion->select("general.titulos.nombre");
        $conexion->from("general.titulos");
        $conexion->join("general.planes_academicos_filiales", "general.planes_academicos_filiales.cod_titulo = general.titulos.codigo");
        $conexion->where("general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico");
        $conexion->where("general.planes_academicos_filiales.cod_filial", $codFilial);
        $conexion->where("general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo");
        $conexion->where("general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad");
        $sq1 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("general.titulos.nombre");
        $conexion->from("general.titulos");
        $conexion->join("general.planes_academicos_periodos", "general.planes_academicos_periodos.cod_titulo = general.titulos.codigo");
        $conexion->where("planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo");
        $conexion->where("general.planes_academicos_periodos.cod_plan_academico = matriculas.cod_plan_academico");
        $sq2 = $conexion->return_query();
        $conexion->resetear();
        $resp = "IFNULL(($sq1), ($sq2))";
        return $resp;
    }
    
    static private function _getSubQueryFechaInicio(CI_DB_mysqli_driver $conexion, $leerDesdePropiedad = true, $certificante = 1){
        if ($leerDesdePropiedad){
            if ($certificante == 1){
                $sq = "certificados.cod_matricula_periodo";
            } else {
                $sq = "matriculas_periodos.codigo";
            }
            $conexion->select("DATE_FORMAT(certificados_propiedades_impresion.valor, '%d/%m/%Y')", false);
            $conexion->from("certificados_propiedades_impresion");
            $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = $sq");
            $conexion->where("certificados_propiedades_impresion.cod_certificante = $certificante");
            $conexion->where("certificados_propiedades_impresion.key", "fecha_inicio");
            $sq1 = $conexion->return_query();
            $conexion->resetear();
        }
        $conexion->select("DATE_FORMAT(MIN(horarios.dia), '%d/%m/%Y')", false);
        $conexion->from("horarios");
        $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_comision = horarios.cod_comision");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $conexion->where("horarios.baja = 0");
        $conexion->where("estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $sq2 = $conexion->return_query();
        $conexion->resetear();
        if ($leerDesdePropiedad){
            $resp = "IFNULL(($sq1), ($sq2))";
        } else {
            $resp = "($sq2)";
        }
        return $resp;
    }
    
    static private function _getSubQueryFechaFin(CI_DB_mysqli_driver $conexion, $leerDesdePropiedad = true, $certificante = 1){
        if ($leerDesdePropiedad){
            if ($certificante == 1){
                $sq = "certificados.cod_matricula_periodo";
            } else {
                $sq = "matriculas_periodos.codigo";
            }
            $conexion->select("DATE_FORMAT(certificados_propiedades_impresion.valor, '%d/%m/%Y')", false);
            $conexion->from("certificados_propiedades_impresion");
            $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = $sq");
            $conexion->where("certificados_propiedades_impresion.cod_certificante = $certificante");
            $conexion->where("certificados_propiedades_impresion.key", "fecha_fin");
            $sq1 = $conexion->return_query();
            $conexion->resetear();
        }
        $conexion->select("DATE_FORMAT(MAX(horarios.dia), '%d/%m/%Y')", false);
        $conexion->from("horarios");
        $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_comision = horarios.cod_comision");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
        $conexion->where("horarios.baja = 0");
        $conexion->where("estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $sq2 = $conexion->return_query();
        $conexion->resetear();
        if ($leerDesdePropiedad){
            $resp = "IFNULL(($sq1),($sq2))";
        } else {
            $resp = "($sq2)";
        }
        return $resp;
    }
    
    static private function _getSubQueryFechaPedido(CI_DB_mysqli_driver $conexion, $estado){
        if (is_array($estado) || $estado != "pendiente_aprobar"){
            $conexion->select("DATE_FORMAT(certificados_estado_historico.fecha_hora, '%d/%m/%Y')", false);
            $conexion->from("certificados_estado_historico");
            $conexion->where("certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo");
            $conexion->where("certificados_estado_historico.cod_certificante = certificados.cod_certificante");
            $conexion->where("certificados_estado_historico.estado", "pendiente_impresion");
            $conexion->order_by("codigo", "DESC");
            $conexion->limit(1);
            $sq1 = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("DATE_FORMAT(MAX(horarios.dia), '%d/%m/%Y')", false);
            $conexion->from("horarios");
            $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_comision = horarios.cod_comision");
            $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
            $conexion->where("horarios.baja = 0");
            $conexion->where("estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
            $sq2 = $conexion->return_query();
            $conexion->resetear();
            $resp = "IF (certificados.estado IN ('finalizado', 'en_proceso', 'pendiente_impresion'), IFNULL(($sq1),($sq2)), NULL)";
        } else {
            $resp = "NULL";
        }
        return $resp;
    }
    
    static private function _getSubQueryUsuarioAprueba(CI_DB_mysqli_driver $conexion, $codFilial, $estado){
        if ($estado != 'pendiente_aprobar'){
            $conexion->select("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido)", false);
            $conexion->from("general.usuarios_sistema");
            $conexion->join("certificados_estado_historico", "certificados_estado_historico.cod_usuario = general.usuarios_sistema.codigo ".
                    "AND certificados_estado_historico.estado = 'pendiente_impresion'");
            $conexion->where("certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo");
            $conexion->where("certificados_estado_historico.cod_certificante = certificados.cod_certificante");
            $conexion->order_by("certificados_estado_historico.codigo", "DESC");
            $conexion->limit(1);
            $sq1 = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido)", false);
            $conexion->from("general.usuarios_sistema");
            $conexion->where("general.usuarios_sistema.cod_filial", $codFilial);
            $conexion->order_by("general.usuarios_sistema.codigo_interno", "DESC");
            $conexion->limit(1);
            $sq2 = $conexion->return_query();
            $conexion->resetear();
            $resp = "IF (certificados.estado IN ('finalizado', 'en_proceso', 'pendiente_impresion'), IFNULL(($sq1),($sq2)), NULL)";
        } else {
            $resp = 'NULL';
        }
        return $resp;
    }
    
    static function _getSubQueryCertificanteNombre(CI_DB_mysqli_driver $conexion, $codCertificante){
        $conexion->select("general.certificados_certificantes.nombre");
        $conexion->from("general.certificados_certificantes");
        $conexion->where("general.certificados_certificantes.codigo = $codCertificante");
        $resp = $conexion->return_query();
        $resp = "($resp)";
        $conexion->resetear();
        return $resp;
    }
    
    static private function _getSubQueryCodComision(CI_DB_mysqli_driver $conexion){
        $conexion->select("matriculas_inscripciones.cod_comision");
        $conexion->from("matriculas_inscripciones");
        $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico AND ".
                "estadoacademico.estado <> 'recursa'");
        $conexion->where("estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->order_by("matriculas_inscripciones.baja", "ASC");
        $conexion->order_by("matriculas_inscripciones.codigo", "DESC");
        $conexion->limit(1);
        $resp = $conexion->return_query();
        $resp = "($resp)";
        $conexion->resetear();
        return $resp;
    }
    
    static private function _getSubQueryFechaEntragado(CI_DB_mysqli_driver $conexion, $codCertificante){
        $conexion->select("DATE_FORMAT(certificados_estado_historico.fecha_hora, '%d/%m/%Y')", false);
        $conexion->from("certificados_estado_historico");
        $conexion->where("certificados_estado_historico.cod_matricula_periodo = matriculas_periodos.codigo");
        $conexion->where("certificados_estado_historico.cod_certificante", $codCertificante);
        $conexion->where("certificados_estado_historico.estado", "entregado");
        $conexion->order_by("certificados_estado_historico.codigo", "DESC");
        $conexion->limit(1);
        $resp = $conexion->return_query();
        $resp = "($resp)";
        $conexion->resetear();
        return $resp;
    }
    
    static function listarCertificados(CI_DB_mysqli_driver $conexion, $codFilial, $codCertificante, $estado = null, $contar = false,
            $arrLimit = null, $arrSort = null, array $arrCondicioneslike = null, $separador = null, $comision = null, $curso = null){            
        if ($codCertificante == 2 && (!is_array($estado) && ($estado == 'pendiente_aprobar' || $estado == null))){
            // recupera consulta para certificados ucel pendiente_aprobar
            if ($estado == 'pendiente_aprobar'){
                self::_armarConsultaCertificadosUCELPendienteAprobar($conexion, $codFilial, $codCertificante, $arrCondicioneslike, $separador,
                        $comision, $curso);
                if ($contar){
                    $query = $conexion->return_query();
                    $conexion->resetear();
                    $conexion->select('COUNT(query.cod_alumno) AS numero FROM (' . $query . ') as query', false);
                    $query2 = $conexion->get();
                    $cantidad = $query2->result_array();
                    return $cantidad[0]['numero'];
                } else {
                    if ($arrLimit != null) {
                        $conexion->limit($arrLimit[1], $arrLimit[0]);
                    }
                    if ($arrSort != null && isset($arrSort[0]) && isset($arrSort[1])) {
                        $conexion->order_by($arrSort["0"], $arrSort["1"]);
                    }
                    $query = $conexion->get();
                    return $query->result_array();
                }                
            } else {
            // recupera consulta para certificados ucel en todos los estados
                self::_armarConsultaCertificadosUCELPendienteAprobar($conexion, $codFilial, $codCertificante, $arrCondicioneslike, 
                        $separador, $comision, $curso);
                $sq1 = $conexion->return_query();
                $conexion->resetear();
                self::_armarConsultaCertificados($conexion, $codFilial, $codCertificante, $estado, $arrCondicioneslike, $separador,
                        $comision, $curso);
                $sq2 = $conexion->return_query();
                $conexion->resetear();
                $conexion->_protect_identifiers = false;
                if ($contar){
                    $query = $conexion->query("SELECT * FROM ($sq1 UNION $sq2) AS tb1");
                    $arrResp = $query->result_array();
                    $conexion->_protect_identifiers = true;
                    return count($arrResp);
                } else {
                    $query = "SELECT * FROM ($sq1 UNION $sq2) AS tb1";
                    if ($arrSort != null && isset($arrSort[0]) && isset($arrSort[1])) {
                        $query .= " ORDER BY {$arrSort["0"]} {$arrSort["1"]}";
                    }
                    if ($arrLimit != null) {
                        $query .= " LIMIT {$arrLimit[0]}, {$arrLimit[1]}";                        
                    }
                    $query = $conexion->query($query);
                    //echo $conexion->last_query(); die();
                    $arrResp = $query->result_array();
                    $conexion->_protect_identifiers = true;
                    return $arrResp;
                }                
            }
        } else {
            // recupera consulta para certificados iga en todos los estado o certificados ucel en estadoa diferentes a pendiente_aprobar
            self::_armarConsultaCertificados($conexion, $codFilial, $codCertificante, $estado, $arrCondicioneslike, $separador, 
                    $comision, $curso);
            if ($contar){
                $query = $conexion->return_query();
                $conexion->resetear();
                $conexion->select('COUNT(query.cod_alumno) AS numero FROM (' . $query . ') as query', false);
                $query2 = $conexion->get();
                $cantidad = $query2->result_array();
                return $cantidad[0]['numero'];
            } else {
                if ($arrLimit != null) {
                    $conexion->limit($arrLimit[1], $arrLimit[0]);
                }
                if ($arrSort != null && isset($arrSort[0]) && isset($arrSort[1])) {
                    $conexion->order_by($arrSort["0"], $arrSort["1"]);
                }
//                echo $conexion->return_query(); die();
                $query = $conexion->get();
                //print_r($query->result_array());
                return $query->result_array();
            }
        }
    }
    
    static private function _armarConsultaCertificadosUCELPendienteAprobar(CI_DB_mysqli_driver $conexion, $codFilial, $codCertificante, 
            array $arrCondicioneslike = null, $separador = null, $comision = null, $curso = null){
        $nombreApellido = formatearNomApeQuery();
        $sqTitulo = self::_getSubQueryNombreTitulo($conexion, $codFilial);
        $sqFechaInicio = self::_getSubQueryFechaInicio($conexion, true, 2);
        $sqFechaFin = self::_getSubQueryFechaFin($conexion, true, 2);
        $sqFechaPedido = self::_getSubQueryFechaPedido($conexion, 'pendiente_aprobar');
        $sqUsuarioAprueba = self::_getSubQueryUsuarioAprueba($conexion, $codFilial, 'pendiente_aprobar');
        $sqCertificanteNombre = self::_getSubQueryCertificanteNombre($conexion, $codCertificante);
        $sqCodComision = self::_getSubQueryCodComision($conexion);
        $conexion->select("c1.cod_matricula_periodo");
        $conexion->from("certificados AS c1");
        $conexion->where("c1.cod_certificante", "1");
        $conexion->where_in("c1.estado", array('finalizado', 'en_proceso', 'pendiente_impresion', 'pendiente_aprobar')); // agregar pendiente_aprobar
        $sqWhereIN = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("c2.cod_matricula_periodo");
        $conexion->from("certificados AS c2");
        $conexion->where("c2.cod_certificante", "2");
        $conexion->where_not_in("c2.estado", array('pendiente_aprobar'));
        $sqWhereNOTIN = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select("matriculas.cod_alumno");
        $conexion->select("matriculas.codigo as cod_matricula");
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento) AS documento_alumno", false);
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->select("matriculas.cod_plan_academico");        
        $conexion->select("matriculas_periodos.cod_tipo_periodo");
        $conexion->select("matriculas_periodos.codigo AS cod_matricula_periodo", false);
        $conexion->select("$sqTitulo AS titulo", false);
        $conexion->select("DATE_FORMAT(matriculas.fecha_emision,'%d/%m/%Y') AS fecha_emision", false);
        $conexion->select("$sqFechaInicio AS fecha_inicio", false);
        $conexion->select("$sqFechaFin AS fecha_fin", false);
        $conexion->select("$sqFechaPedido AS fecha_pedido", false);
        $conexion->select("$sqUsuarioAprueba AS usuario_aprueba", false);
        $conexion->select("0 AS entregado", false);
        $conexion->select("2 AS cod_certificante", false);
        $conexion->select("'pendiente_aprobar' AS estado", false);
        $conexion->select("$sqCertificanteNombre AS certificante", false);
        $conexion->select("$sqCodComision AS cod_comision", false);
        $conexion->from("matriculas_periodos");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula AND matriculas.estado <> 'migrado'");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico AND general.planes_academicos.cod_curso IN (1, 2, 20, 22, 31, 63)");
        $conexion->where_in("matriculas_periodos.estado", array('habilitada', 'certificada', 'finalizada'));
        $conexion->where("matriculas_periodos.codigo IN ($sqWhereIN)");
        $conexion->where("matriculas_periodos.codigo NOT IN ($sqWhereNOTIN)");
        if ($arrCondicioneslike != null && count($arrCondicioneslike) > 0) {
            foreach ($arrCondicioneslike as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($curso != null){
            $conexion->where("general.planes_academicos.cod_curso", $curso);
        }
        if ($comision != null){
            $conexion->having("cod_comision", $comision);
        }
    }
    
    /**
     * recupera registros de la tabla certificados. Si el certificante es UCEL y el estado = null no recupera los registros
     * para certificados ucel pendiente de aprobar
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param type $codFilial
     * @param type $codCertificante
     * @param type $estado
     * @param type $arrLimit
     * @param type $arrSort
     * @param array $arrCondicioneslike
     * @param type $separador
     */
    static private function _armarConsultaCertificados(CI_DB_mysqli_driver $conexion, $codFilial, $codCertificante, $estado = null, 
        array $arrCondicioneslike = null, $separador = null, $comision = null, $curso = null){
        $nombreApellido = formatearNomApeQuery();
        $sqTitulo = self::_getSubQueryNombreTitulo($conexion, $codFilial);
        $sqFechaInicio = self::_getSubQueryFechaInicio($conexion);
        $sqFechaFin = self::_getSubQueryFechaFin($conexion);
        $sqFechaPedido = self::_getSubQueryFechaPedido($conexion, $estado);
        $sqUsuarioAprueba = self::_getSubQueryUsuarioAprueba($conexion, $codFilial, $codCertificante);
        $sqCertificanteNombre = self::_getSubQueryCertificanteNombre($conexion, $codCertificante);
        $sqCodComision = self::_getSubQueryCodComision($conexion);
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select("matriculas.cod_alumno");
        $conexion->select("matriculas.codigo as cod_matricula");
        $conexion->select("CONCAT(general.documentos_tipos.nombre, ' ', alumnos.documento) AS documento_alumno", false);
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->select("matriculas.cod_plan_academico");
        $conexion->select("matriculas_periodos.cod_tipo_periodo");
        $conexion->select("matriculas_periodos.codigo AS cod_matricula_periodo", false);
        $conexion->select("$sqTitulo AS titulo", false);
        $conexion->select("DATE_FORMAT(matriculas.fecha_emision,'%d/%m/%Y') AS fecha_emision", false);
        $conexion->select("$sqFechaInicio AS fecha_inicio", false);
        $conexion->select("$sqFechaFin AS fecha_fin", false);
        $conexion->select("$sqFechaPedido AS fecha_pedido", false);
        $conexion->select("$sqUsuarioAprueba AS usuario_aprueba", false);
        $conexion->select("certificados.entregado");
        $conexion->select("certificados.cod_certificante");
        $conexion->select("certificados.estado");        
        $conexion->select("$sqCertificanteNombre AS certificante", false);
        $conexion->select("$sqCodComision AS cod_comision", false);
        $conexion->from("certificados");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = certificados.cod_matricula_periodo");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = matriculas.cod_plan_academico");
        $conexion->where("certificados.cod_certificante", $codCertificante);
        if ($arrCondicioneslike != null && count($arrCondicioneslike) > 0) {
            foreach ($arrCondicioneslike as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        if ($curso != null){
            $conexion->where("general.planes_academicos.cod_curso", $curso);
        }
        if ($comision != null){
            $conexion->having("cod_comision", $comision);
        }
        if ($codCertificante == 1){
            if ($estado != null){
                $metodo = is_array($estado) ? "where_in" : "where";
                $conexion->$metodo("certificados.estado", $estado);
            } else {
//                $conexion->where("certificados.estado <>", "pendiente"); // certificante 1 esconde los certificados pendientes
            }
        } else if ($codCertificante == 2){
            if ($estado != null){
                $metodo = is_array($estado) ? "where_in" : "where";
                $conexion->$metodo("certificados.estado", $estado);
            } else {
                $conexion->where("certificados.estado NOT IN ('pendiente_aprobar', 'pendiente')");
            }
        }
    }
    
    
        /**
     * retorna lista de certificados
     * @access public
     * @return array de matriculas
     */
    static function listarCertificadosDataTable2(CI_DB_mysqli_driver $conexion, $codfilial, $arrCondicioneslike = null, 
            $arrLimit = null, $arrSort = null, $contar = false, $separador = null, $estado = null, $certificante = null,
            $mostrarEstadoPendiente = true) {
        $nombreApellido = formatearNomApeQuery();
        if (!$contar){
            if ($certificante == 2 && $estado == 'pendiente_aprobar'){
                $conexion->select("DATE_FORMAT(valor, '%d/%m/%Y')", false);
                $conexion->from("certificados_propiedades_impresion");
                $conexion->where("certificados_propiedades_impresion.cod_certificante", 2);
                $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = matriculas_periodos.codigo");
                $conexion->where("key", "fecha_inicio");
            } else {
                $conexion->select("DATE_FORMAT(certificados_propiedades_impresion.valor, '%d/%m/%Y')", false);
                $conexion->from("certificados_propiedades_impresion");
                $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = certificados.cod_matricula_periodo");
                $conexion->where("certificados_propiedades_impresion.cod_certificante = certificados.cod_certificante");
                $conexion->where("certificados_propiedades_impresion.key", "fecha_inicio");
            }
            $sqFechaInicio = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("DATE_FORMAT(MIN(horarios.dia), '%d/%m/%Y')", false);
            $conexion->from("horarios");
            $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_comision = horarios.cod_comision");
            $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
            $conexion->where("horarios.baja = 0");
            $conexion->where("estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
            $sqFechaInicioComision = $conexion->return_query();
            $conexion->resetear();
            if ($certificante == 2 && $estado == 'pendiente_aprobar'){
                $conexion->select("DATE_FORMAT(valor, '%d/%m/%Y')", false);
                $conexion->from("certificados_propiedades_impresion");
                $conexion->where("certificados_propiedades_impresion.cod_certificante", 2);
                $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = matriculas_periodos.codigo");
                $conexion->where("key", "fecha_fin");
            } else {
                $conexion->select("DATE_FORMAT(certificados_propiedades_impresion.valor, '%d/%m/%Y')", false);
                $conexion->from("certificados_propiedades_impresion");
                $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = certificados.cod_matricula_periodo");
                $conexion->where("certificados_propiedades_impresion.cod_certificante = certificados.cod_certificante");
                $conexion->where("certificados_propiedades_impresion.key", "fecha_fin");
            }
            $sqFechaFin = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("DATE_FORMAT(MAX(horarios.dia), '%d/%m/%Y')", false);
            $conexion->from("horarios");
            $conexion->join("matriculas_inscripciones", "matriculas_inscripciones.cod_comision = horarios.cod_comision");
            $conexion->join("estadoacademico", "estadoacademico.codigo = matriculas_inscripciones.cod_estado_academico");
            $conexion->where("horarios.baja = 0");
            $conexion->where("estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo");
            $fechaFinComision = $conexion->return_query();
            $conexion->resetear();
            
            if ($certificante == 2 && $estado == 'pendiente_aprobar'){
                $conexion->select('null', false);
            } else {
                $conexion->select("DATE_FORMAT(certificados_estado_historico.fecha_hora, '%d/%m/%Y')", false);
                $conexion->from("certificados_estado_historico");
                $conexion->where("certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo");
                $conexion->where("certificados_estado_historico.cod_certificante = certificados.cod_certificante");
                $conexion->where("certificados_estado_historico.estado", "pendiente_impresion");
                $conexion->order_by("codigo", "desc");
                $conexion->limit(1);
            }
            $sqFechaPedido = $conexion->return_query();
            $conexion->resetear();

            if ($certificante == 2 && $estado == 'pendiente_aprobar'){
                $conexion->select('null', false);
            } else {
                $conexion->select("CONCAT(general.usuarios_sistema.nombre, ' ', general.usuarios_sistema.apellido)", false);
                $conexion->from("general.usuarios_sistema");
                $conexion->join("certificados_estado_historico", "certificados_estado_historico.cod_usuario = general.usuarios_sistema.codigo ".
                        "AND certificados_estado_historico.estado = 'pendiente_impresion'");
                $conexion->where("certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo");
                $conexion->where("certificados_estado_historico.cod_certificante = certificados.cod_certificante");
                $conexion->order_by("certificados_estado_historico.codigo", "desc");
                $conexion->limit(1);
            }
            $sqUsuarioApruba = $conexion->return_query();
            $conexion->resetear();
            if ($certificante == 2 && $estado == 'pendiente_aprobar'){
                $conexion->select('null', false);
            } else {
                $conexion->select("CONCAT(general.usuarios_sistema.nombre, ' ', usuarios_sistema.apellido)", false);
                $conexion->from("general.usuarios_sistema");
                $conexion->where("general.usuarios_sistema.cod_filial", $codfilial);
                $conexion->order_by("general.usuarios_sistema.codigo_interno DESC");
                $conexion->limit(1);
            }
            $sqUsuarioApruebaDefault = $conexion->return_query();
            $conexion->resetear();
        }
      
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select('alumnos.codigo as cod_alumno');
        $conexion->select('CONCAT(general.documentos_tipos.nombre, " ", alumnos.documento) AS documento_alumno', false);
        $conexion->select('general.planes_academicos.cod_curso');
        $conexion->select('matriculas.cod_plan_academico');
        $conexion->select('matriculas_periodos.cod_tipo_periodo');
        $conexion->select('matriculas_periodos.codigo AS cod_matricula_periodo');
                
        $conexion->select('IFNULL((SELECT general.titulos.nombre FROM general.titulos 
                    JOIN general.planes_academicos_filiales ON general.planes_academicos_filiales.cod_titulo = general.titulos.codigo 
                    WHERE general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico AND general.planes_academicos_filiales.cod_filial = '.$codfilial.' 
                    AND general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad
                    ), (SELECT general.titulos.nombre FROM general.titulos 
                    JOIN general.planes_academicos_periodos ON general.planes_academicos_periodos.cod_titulo = general.titulos.codigo
                    WHERE planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_periodos.cod_plan_academico =  matriculas.cod_plan_academico
                    )) AS titulo', false);
        $conexion->select("CONCAT(LPAD(DAY(matriculas.fecha_emision), 2, 0), '/', LPAD(MONTH(matriculas.fecha_emision), 2, 0), '/', YEAR(matriculas.fecha_emision)) AS fecha_emision",false);
        
        if (!$contar){
            $conexion->select("IFNULL(($sqFechaInicio), ($sqFechaInicioComision)) AS fecha_inicio", false);
            $conexion->select("IFNULL(($sqFechaFin), ($fechaFinComision)) AS fecha_fin", false);
            $conexion->select("IF (certificados.estado in ('finalizado', 'en_proceso', 'pendiente_impresion'), IFNULL(($sqFechaPedido), ($fechaFinComision)), NULL) AS fecha_pedido", false);
            $conexion->select("IF (certificados.estado in ('finalizado', 'en_proceso', 'pendiente_impresion'), IFNULL(($sqUsuarioApruba), ($sqUsuarioApruebaDefault)), NULL) AS usuario_aprueba", false);
        }
        if ($certificante == 2 && $estado == 'pendiente_aprobar'){
            $conexion->select('2 AS cod_certificante', false);
            $conexion->select('"pendiente_aprobar" AS estado', false);
            $conexion->select('general.certificados_certificantes.nombre as certificante');
            $conexion->from('matriculas_periodos');
            $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
            $on = "general.certificados_plan_filial.cod_plan_academico = matriculas.cod_plan_academico";
            $on .= " AND general.certificados_plan_filial.cod_certificante = 2";
            $on .= " AND general.certificados_plan_filial.cod_filial = $codfilial";
            $on .= " AND general.certificados_plan_filial.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo";
            $conexion->join("general.certificados_plan_filial", $on); 
            $conexion->join('general.certificados_certificantes', 'general.certificados_certificantes.codigo = general.certificados_plan_filial.cod_certificante');
            $conexion->select("0 AS recibido", false);
            $conexion->select("0 AS entregado", false);
            $conexion->select("'' AS id_producto_pedido", false);            
        } else {            
            $conexion->select("certificados.recibido");
            $conexion->select("certificados.entregado");
            $conexion->select("certificados.id_producto_pedido");
            $conexion->select('certificados.cod_certificante');
            $conexion->select('certificados.estado');
            $conexion->select('general.certificados_certificantes.nombre as certificante');
            $conexion->from('certificados');
            $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = certificados.cod_matricula_periodo');
            $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
            $conexion->join('general.certificados_certificantes', 'general.certificados_certificantes.codigo = certificados.cod_certificante');
        }
        
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->join('general.documentos_tipos', 'general.documentos_tipos.codigo = alumnos.tipo');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
        
        if ($estado != null ){
            if ($estado == "pendiente_aprobar" && $certificante == 2){
                $conexion->where("matriculas_periodos.codigo NOT IN (SELECT cod_matricula_periodo FROM certificados WHERE cod_certificante = 2 AND estado NOT IN ('pendiente_imprimir', 'pendiente'))");
            } else {
                $conexion->where("certificados.estado", $estado);
            }
        } else {
            if (!$mostrarEstadoPendiente){
                $conexion->where("certificados.estado <>", "pendiente");
            }
        }
        if ($certificante == 2 && $estado == 'pendiente_aprobar') {
            $conexion->where_in("matriculas_periodos.estado", array(Vmatriculas_periodos::getEstadoFinalizada(), Vmatriculas_periodos::getEstadoCertificada()));
        } else if ($certificante != null && $estado != 'pendiente_aprobar'){
            $conexion->where("certificados.cod_certificante", $certificante);
        }
        $conexion->where("matriculas_periodos.estado <>", "migrado");
        if ($arrCondicioneslike != null && count($arrCondicioneslike) > 0) {
            foreach ($arrCondicioneslike as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }       

        if ($contar) {
            $query = $conexion->return_query();
            $conexion->resetear();
            $conexion->select('COUNT(query.cod_alumno) as numero from (' . $query . ') as query', false);
            $query2 = $conexion->get();
            $cantidad = $query2->result_array();
            return $cantidad[0]['numero'];
        } else {
            if ($arrSort != null && isset($arrSort[0]) && isset($arrSort[1])) {
                $conexion->order_by($arrSort["0"], $arrSort["1"]);
            }
            $query = $conexion->get();
//            echo $conexion->last_query(); die();
            return $query->result_array();
        }
    }
    
    
    
    /**
     * retorna lista de certificados
     * @access public
     * @return array de matriculas
     */
    static function listarCertificadosDataTable(CI_DB_mysqli_driver $conexion, $pestania, $codfilial, $arrCondicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $separador = null) {
        $nombreApellido = formatearNomApeQuery();
        $conexion->select("CONCAT($nombreApellido) as nombre_apellido", false);
        $conexion->select('alumnos.codigo as cod_alumno');
        $conexion->select('general.documentos_tipos.nombre as tipo_documento');
        $conexion->select('alumnos.documento');
        $conexion->select('general.planes_academicos.cod_curso');
        $conexion->select('matriculas.cod_plan_academico');
        $conexion->select('matriculas_periodos.cod_tipo_periodo');
        $conexion->select('matriculas_periodos.codigo AS cod_matricula_periodo');
        
        $conexion->select('IFNULL((SELECT general.titulos.nombre FROM general.titulos 
                    JOIN general.planes_academicos_filiales ON general.planes_academicos_filiales.cod_titulo = general.titulos.codigo 
                    WHERE general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico AND general.planes_academicos_filiales.cod_filial = '.$codfilial.' 
                    AND general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad
                    ), (SELECT general.titulos.nombre FROM general.titulos 
                    JOIN general.planes_academicos_periodos ON general.planes_academicos_periodos.cod_titulo = general.titulos.codigo
                    WHERE planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_periodos.cod_plan_academico =  matriculas.cod_plan_academico
                    )) AS titulo', false);
        $conexion->select("CONCAT(LPAD(DAY(matriculas.fecha_emision), 2, 0), '/', LPAD(MONTH(matriculas.fecha_emision), 2, 0), '/', YEAR(matriculas.fecha_emision)) AS fecha_emision",false);
            
        if ($pestania == 'pendiente_certificar'){
            $conexion->select('2 AS cod_certificante', false);
            $conexion->select('"pendiente_aprobar" AS estado', false);
            $conexion->select('general.certificados_certificantes.nombre as certificante');
            $conexion->from('matriculas_periodos');
            $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
            $on = "general.certificados_plan_filial.cod_plan_academico = matriculas.cod_plan_academico";
            $on .= " AND general.certificados_plan_filial.cod_certificante = 2";
            $on .= " AND general.certificados_plan_filial.cod_filial = $codfilial";
            $on .= " AND general.certificados_plan_filial.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo";
            $conexion->join("general.certificados_plan_filial", $on); 
            $conexion->join('general.certificados_certificantes', 'general.certificados_certificantes.codigo = general.certificados_plan_filial.cod_certificante');
        } else {            
            $conexion->select('certificados.cod_certificante');
            $conexion->select('certificados.estado');
            $conexion->select('general.certificados_certificantes.nombre as certificante');
            $conexion->from('certificados');
            $conexion->join('matriculas_periodos', 'matriculas_periodos.codigo = certificados.cod_matricula_periodo');
            $conexion->join('matriculas', 'matriculas.codigo = matriculas_periodos.cod_matricula');
            $conexion->join('general.certificados_certificantes', 'general.certificados_certificantes.codigo = certificados.cod_certificante');
        }
        
        $conexion->join('alumnos', 'alumnos.codigo = matriculas.cod_alumno');
        $conexion->join('general.documentos_tipos', 'general.documentos_tipos.codigo = alumnos.tipo');
        $conexion->join('general.planes_academicos', 'general.planes_academicos.codigo = matriculas.cod_plan_academico');
//          
            switch ($pestania) {
                case 'aprobar':
                    $conexion->where('certificados.estado', Vcertificados::getEstadoPendienteAprobar());
                    break;
                case 'finalizados':
                    $conexion->where_in('certificados.estado', array(Vcertificados::getEstadoEnProceso(), Vcertificados::getEstadoFinalizado(), Vcertificados::getEstadoPendienteImpresion()));
                    break;
                case 'pendientes':
                    $conexion->where('certificados.estado', Vcertificados::getEstadoPendiente());
                    break;
                case 'cancelados':
                    $conexion->where('certificados.estado', Vcertificados::getEstadoCancelado());
                    break;

                case 'pendiente_impresion':
                    $conexion->where("certificados.estado", Vcertificados::getEstadoPendienteImpresion());

                case 'pendiente_certificar':
                    $conexion->where_in("matriculas_periodos.estado", array('certificada', 'finalizada'));
                    $conexion->where('matriculas_periodos.codigo NOT IN (SELECT certificados.cod_matricula_periodo FROM certificados WHERE certificados.cod_certificante = 2 AND certificados.estado <> "pendiente")', null, false);
                    break;
                
                case '':
                    break;

                default:
                    break;
            }

        if ($arrCondicioneslike != null && count($arrCondicioneslike) > 0) {
            foreach ($arrCondicioneslike as $key => $value) {
                if ($key == 'nombre_apellido') {
                    $arrTemp[] = "REPLACE(nombre_apellido, '$separador ',' ') LIKE REPLACE('%$value%', '$separador ',' ')";
                } else {
                    $arrTemp[] = "$key LIKE '%$value%'";
                }
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort != null) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            $query = $conexion->return_query();
            $conexion->resetear();
            $conexion->select('COUNT(query.cod_alumno) as numero from (' . $query . ') as query', false);
            $query2 = $conexion->get();
            $cantidad = $query2->result_array();
            return $cantidad[0]['numero'];
        } else {
            $query = $conexion->get();
//            echo $conexion->last_query(); die();
            return $query->result_array();
        }
    }

    public function guardar($codfilial, $estado = null, $fechahora = null, $codusuario = null) {
        $this->estado = $estado != null ? $estado : Vcertificados::getEstadoPendiente();
        $this->fecha_hora = $fechahora != null ? $fechahora : date('Y-m-d H:m:i');
        $this->cod_usuario = $codusuario;
        $this->guardarCertificados();

        $objmatriculaper = new Vmatriculas_periodos($this->oConnection, $this->cod_matricula_periodo);
        $objmatricula = new Vmatriculas($this->oConnection, $objmatriculaper->cod_matricula);
        $certificadoplan = new Vcertificados_plan_filial($this->oConnection, $codfilial, $objmatricula->cod_plan_academico, $objmatriculaper->cod_tipo_periodo, $this->cod_certificante);

        $propiedades = $certificadoplan->getPropiedadesNuevoCertificado();
        foreach ($propiedades as $propiedad) {
            switch ($propiedad['key']) {
                case 'costo':
                    //ver plan

                    break;

                default:
                    break;
            }
        }

        $requerimientos = $certificadoplan->getRequerimientosHabilitadosAprobarCertificado();
        foreach ($requerimientos as $requerimiento) {
            $this->oConnection->insert('certificados_matriculas_requerimientos', array('cod_matricula_periodo' => $objmatriculaper->getCodigo(), 'cod_requerimiento' => $requerimiento['codigo']));
        }
    }

    function setPendiente($codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->estado = Vcertificados::getEstadoPendiente();
        $this->guardarCertificados();

        $objhistorico = new Vcertificados_estado_historico($this->oConnection);
        $objhistorico->guardar($this->cod_matricula_periodo, $this->cod_certificante, $this->estado, $codusuario, $fechahora, $motivo, $comentario);
    }

    function setPendienteAprobar($codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->estado = Vcertificados::getEstadoPendienteAprobar();
        $this->guardarCertificados();
        $objhistorico = new Vcertificados_estado_historico($this->oConnection);
        $objhistorico->guardar($this->cod_matricula_periodo, $this->cod_certificante, $this->estado, $codusuario, $fechahora, $motivo, $comentario);
        return true;        
    }

    function setPendienteImpresion($codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->estado = Vcertificados::getEstadoPendienteImpresion();
        if ($this->fecha_hora == ''){ // certificados UCEL pendiente de impresion no esta guardado aun por lo que no tiene fecha
            $this->fecha_hora = date("Y-m-d H:i:s");
        }
        $this->guardarCertificados();
        $objhistorico = new Vcertificados_estado_historico($this->oConnection);
        $objhistorico->guardar($this->cod_matricula_periodo, $this->cod_certificante, $this->estado, $codusuario, $fechahora, $motivo, $comentario);
        return true;        
    }

    function setEnProceso($codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->estado = Vcertificados::getEstadoEnProceso();
        $this->guardarCertificados();

        $objhistorico = new Vcertificados_estado_historico($this->oConnection);
        $objhistorico->guardar($this->cod_matricula_periodo, $this->cod_certificante, $this->estado, $codusuario, $fechahora, $motivo, $comentario);
    }

    function setFinalizado($codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->estado = Vcertificados::getEstadoFinalizado();
        $this->guardarCertificados();

        $objhistorico = new Vcertificados_estado_historico($this->oConnection);
        $objhistorico->guardar($this->cod_matricula_periodo, $this->cod_certificante, $this->estado, $codusuario, $fechahora, $motivo, $comentario);
    }

    function setCancelado($codusuario = null, $fechahora = null, $motivo = null, $comentario = null) {
        $this->estado = Vcertificados::getEstadoCancelado();
        if (!$this->getExiste()){
            $this->cod_usuario = $codusuario;
            $this->fecha_hora = date("Y-m-d H:i:s");
        }
        $this->guardarCertificados();
        $objhistorico = new Vcertificados_estado_historico($this->oConnection);
        $objhistorico->guardar($this->cod_matricula_periodo, $this->cod_certificante, $this->estado, $codusuario, $fechahora, $motivo, $comentario);
    }

    public function getRequerimientos($cumplido = null) {
        $this->oConnection->select('general.certificados_requerimientos.codigo, general.certificados_requerimientos.key, general.certificados_requerimientos.valor, certificados_matriculas_requerimientos.estado');
        $this->oConnection->from('certificados_matriculas_requerimientos');
        $this->oConnection->join('general.certificados_requerimientos', 'certificados_matriculas_requerimientos.cod_requerimiento = general.certificados_requerimientos.codigo');
        if ($cumplido != null) {
            $this->oConnection->where('certificados_matriculas_requerimientos.estado', $cumplido);
        }
        $this->oConnection->where('certificados_matriculas_requerimientos.cod_matricula_periodo', $this->cod_matricula_periodo);
        $this->oConnection->where('general.certificados_requerimientos.cod_certificante', $this->cod_certificante);
        $query = $this->oConnection->get();
        return $query->result_array();


    }
    public function alumnoDeuda() {
        $this->oConnection->select('COUNT(ctacte.codigo) as deuda');
        $this->oConnection->from('ctacte');
        $this->oConnection->join('matriculas_periodos', 'matriculas_periodos.cod_matricula = ctacte.concepto');
        $this->oConnection->where('matriculas_periodos.codigo', $this->cod_matricula_periodo);
        $this->oConnection->where('ctacte.habilitado', 1);
        $this->oConnection->where('ctacte.fechavenc >', 'CURDATE()', false);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function setNoCumpleRequerimiento($codrequerimiento) {
        $arrRequerimiento = array('estado' => 'no_cumplido');
        $this->oConnection->where('cod_matricula_periodo', $this->cod_matricula_periodo);
        $this->oConnection->where('cod_requerimiento', $codrequerimiento);
        $this->oConnection->update('certificados_matriculas_requerimientos', $arrRequerimiento);
    }

    public function setCumpleRequerimiento($codrequerimiento) {
        $arrRequerimiento = array('estado' => 'cumplido');
        $this->oConnection->where('cod_matricula_periodo', $this->cod_matricula_periodo);
        $this->oConnection->where('cod_requerimiento', $codrequerimiento);
        $this->oConnection->update('certificados_matriculas_requerimientos', $arrRequerimiento);
    }

    public function getCumpleTodosRequerimientos() {
        $this->oConnection->select('certificados_matriculas_requerimientos.cod_requerimiento');
        $this->oConnection->from('certificados_matriculas_requerimientos');
        $this->oConnection->join("general.certificados_requerimientos", "general.certificados_requerimientos.codigo = certificados_matriculas_requerimientos.cod_requerimiento");
        $this->oConnection->where('certificados_matriculas_requerimientos.cod_matricula_periodo', $this->cod_matricula_periodo);
        $this->oConnection->where('certificados_matriculas_requerimientos.estado', 'no_cumplido');
        $query = $this->oConnection->get();
        $resultado = $query->result_array();
        if (count($resultado) > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getPropiedadesImpresion() {
        $this->oConnection->select('*');
        $this->oConnection->from('certificados_propiedades_impresion');
        $this->oConnection->where('cod_matricula_periodo', $this->cod_matricula_periodo);
        $this->oConnection->where('cod_certificante', $this->cod_certificante);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function setPropiedadesImpresion($arrkeyvalor, $codUsuario = null){
        $resp = true;
        if (!$this->existe){
            $this->estado = self::$estadopendiente;
            $this->fecha_hora = date("Y-m-d H:i:s");
            $this->cod_usuario = $codUsuario;
            if ($this->cod_certificante == 2){
                $this->estado = Vcertificados::getEstadoPendienteAprobar();
            }
            $resp = $resp && $this->guardarCertificados();
        }
//        die();
        foreach ($arrkeyvalor as $row) {
            $datosdelete = array('cod_matricula_periodo' => $this->cod_matricula_periodo, 'cod_certificante' => $this->cod_certificante, 'key' => $row['key']);
            $resp = $resp && $this->oConnection->delete('certificados_propiedades_impresion', $datosdelete);
            $datosinsert = array('cod_matricula_periodo' => $this->cod_matricula_periodo, 'cod_certificante' => $this->cod_certificante, 'key' => $row['key'], 'valor' => $row['valor']);
            $resp = $resp && $this->oConnection->insert('certificados_propiedades_impresion', $datosinsert);
        }
        return $resp;
    }

    public function getCodigoMatriculaPeriodo() {
        return $this->cod_matricula_periodo;
    }

    public function getCodigoCertificante() {
        return $this->cod_certificante;
    }
    
    //mmori - modificacion en certificados
    public function cambiarEstadoCertificadoIGA()
    {
        $requerimientos = $this->getRequerimientos();
        $conexion = $this->oConnection;
        $objmatriculaper = new Vmatriculas_periodos($conexion, $this->getCodigoMatriculaPeriodo());
        $cumple = true;
        
        
        if($objmatriculaper->tieneDeudasAcademicas(true))
        {
            $cumple = false;
        }
        
        $cumple2 = true;
        
        if ($objmatriculaper->estado == Vmatriculas_periodos::getEstadoFinalizada() || $objmatriculaper->estado == Vmatriculas_periodos::getEstadoCertificada())
        {
            $anteriores = $objmatriculaper->getAntecesores();
            foreach ($anteriores as $rowmatper) 
            {
                $objmatper = new Vmatriculas_periodos($conexion, $rowmatper['codigo']);
                if (!($objmatper->estado == Vmatriculas_periodos::getEstadoFinalizada() || $objmatper->estado == Vmatriculas_periodos::getEstadoCertificada())) 
                {
                    $cumple2 = false;
                }
            }
            if (!$cumple2)
            {
                $cumple = false;
            }
        }
        
        $materias = $objmatriculaper->getEstadoAcademico();
        $todoAprobado = true;
                    
        foreach ($materias as $materia)
        {
            if($materia['estado'] != 'aprobado' && $materia['estado'] != 'homologado' && $materia['estado'] != 'migrado')
            {
                $todoAprobado = false;
            }
        }
                    
        if (!$todoAprobado)
        {
            $cumple = false;
        }
                    
        switch ($this->estado)
        {
            case Vcertificados::getEstadoPendiente():
                if ($cumple) 
                {
                    $this->setPendienteAprobar(null, null, 2);
                }
            break;
                       
            case Vcertificados::getEstadoPendienteAprobar():
                if (!$cumple) 
                {
                    $this->setPendiente(null, null, 1);
                }
            break;
                        
            case Vcertificados::getEstadoPendienteImpresion():
                if (!$cumple) 
                {
                    $this->setPendiente(null, null, 1);
                }
            break;
                        
            default:
            break;
        }
    }
 
    

    static public function getCertificados_wc(CI_DB_mysqli_driver $conexion, $codfilial) {
        $conexion->select("certificados_propiedades_impresion.valor");
        $conexion->from("certificados_propiedades_impresion");
        $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = certificados.cod_matricula_periodo");
        $conexion->where("certificados_propiedades_impresion.cod_certificante = certificados.cod_certificante");
        $conexion->where("certificados_propiedades_impresion.`key` = 'fecha_inicio'");
        $subQueryFechaInicio = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("certificados_propiedades_impresion.valor");
        $conexion->from("certificados_propiedades_impresion");
        $conexion->where("certificados_propiedades_impresion.cod_matricula_periodo = certificados.cod_matricula_periodo");
        $conexion->where("certificados_propiedades_impresion.cod_certificante = certificados.cod_certificante");
        $conexion->where("certificados_propiedades_impresion.`key` = 'fecha_fin'");
        $subQueryFechaFin = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("MAX(fecha_hora)", false);
        $conexion->from("certificados_estado_historico");
        $conexion->where("certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo");
        $conexion->where("certificados_estado_historico.cod_certificante = certificados.cod_certificante");
        $conexion->where("certificados_estado_historico.estado = certificados.estado");
        $sqFechaPedido = $conexion->return_query();
        $conexion->resetear();
        /*
         * SELECT MAX(fecha_hora) FROM certificados_estado_historico WHERE certificados_estado_historico.cod_matricula_periodo = certificados.cod_matricula_periodo
AND certificados_estado_historico.cod_certificante = certificados.cod_certificante AND certificados_estado_historico.estado = certificados.estado
         */

        $conexion->select("certificados.*");
        $conexion->select("($subQueryFechaInicio) AS fecha_inicio", false);
        $conexion->select("($subQueryFechaFin) AS fecha_fin", false);
        $conexion->select("($sqFechaPedido) AS fecha_pedido");
        $conexion->select("matriculas.codigo AS cod_matricula");
        $conexion->select("matriculas.cod_plan_academico");
        $conexion->select("matriculas_periodos.cod_tipo_periodo AS cod_tipo_periodo");
        $conexion->select("IF(general.planes_academicos_periodos.cod_tipo_periodo <> 1, general.planes_academicos_periodos.hs_reloj + 
            IFNULL((SELECT SUM(t1.hs_reloj) 
            FROM general.planes_academicos_periodos AS t1 
            WHERE t1.cod_plan_academico = general.planes_academicos_periodos.cod_plan_academico 
            AND t1.cod_tipo_periodo < general.planes_academicos_periodos.cod_tipo_periodo),0), general.planes_academicos_periodos.hs_reloj ) AS cantidad_horas", false);
        
        $conexion->select('IFNULL((SELECT general.titulos.nombre FROM general.titulos 
                JOIN general.planes_academicos_filiales ON general.planes_academicos_filiales.cod_titulo = general.titulos.codigo 
                WHERE general.planes_academicos_filiales.cod_plan_academico = matriculas.cod_plan_academico AND general.planes_academicos_filiales.cod_filial = '. $codfilial .' 
                AND general.planes_academicos_filiales.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_filiales.modalidad = matriculas_periodos.modalidad
                ), (SELECT general.titulos.nombre FROM general.titulos 
                JOIN general.planes_academicos_periodos ON general.planes_academicos_periodos.cod_titulo = general.titulos.codigo
                WHERE planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo AND general.planes_academicos_periodos.cod_plan_academico =  matriculas.cod_plan_academico
                )) as titulo', false);

        $conexion->select("alumnos.nombre AS nombre_alumno");
        $conexion->select("alumnos.apellido AS apellido_alumno");
        $conexion->select("alumnos.documento");
        $conexion->select("general.documentos_tipos.nombre AS tipo_documento");
        $conexion->select("general.planes_academicos.cod_curso");
        $conexion->from("certificados");
        $conexion->join("matriculas_periodos", "matriculas_periodos.codigo = certificados.cod_matricula_periodo");
        $conexion->join("matriculas", "matriculas.codigo = matriculas_periodos.cod_matricula");
        $conexion->join("general.planes_academicos_periodos", "general.planes_academicos_periodos.cod_plan_academico = matriculas.cod_plan_academico");
        $conexion->join("alumnos", "alumnos.codigo = matriculas.cod_alumno");
        $conexion->join("general.documentos_tipos", "general.documentos_tipos.codigo = alumnos.tipo");
        $conexion->join("general.planes_academicos", "general.planes_academicos.codigo = general.planes_academicos_periodos.cod_plan_academico AND general.planes_academicos_periodos.cod_tipo_periodo = matriculas_periodos.cod_tipo_periodo");
        $conexion->where("certificados.estado", "pendiente_impresion");
        $query = $conexion->get();
        return $query->result_array();
    }

    static function getMaxCodigoPedido(CI_DB_mysqli_driver $conexion){        
        $conexion->select("MAX(certificados.id_producto_pedido) AS id_producto_pedido", false);
        $conexion->from("certificados");
        $query = $conexion->get();
        $arrResp = $query->result_array();
        return isset($arrResp[0]) && isset($arrResp[0]['id_producto_pedido']) ? $arrResp[0]['id_producto_pedido'] : 0;
    }
    
}
