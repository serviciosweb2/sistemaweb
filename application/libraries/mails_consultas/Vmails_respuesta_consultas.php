<?php

/**
* Class Vmails_respuesta_consultas
*
*Class  Vmails_respuesta_consultas maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vmails_respuesta_consultas extends Tmails_respuesta_consultas{

    private static $estadoEnviada = "enviada";
    private static $estadoNoEnviar = "no_enviar";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarRespuestasEnvios(CI_DB_mysqli_driver $conexion){
        $conexion->select("mails_consultas.mails_consultas.mail");
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->where("mails_consultas.mails_consultas.codigo = mails_consultas.mails_respuesta_consultas.cod_consulta");
        $sqEmail = $conexion->return_query();
        $conexion->resetear();      
        
        $conexion->select("mails_consultas.mails_consultas.asunto");
        $conexion->from("mails_consultas.mails_consultas");
        $conexion->where("mails_consultas.mails_consultas.codigo = mails_consultas.mails_respuesta_consultas.cod_consulta");
        $sqAsunto = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("($sqEmail) AS email", false);
        $conexion->select("($sqAsunto) AS asunto", false);
    }
    
    public function marcarEnviada(){
        $this->oConnection->where("codigo", $this->codigo);
        return $this->oConnection->update("mails_consultas.mails_respuesta_consultas", array("estado" => self::$estadoEnviada));
    }
    
    static public function getEstadoEnviada(){
        return self::$estadoEnviada;
    }
    
    static public function getEstadoNoEnviar(){
        return self::$estadoNoEnviar;
    }
    
    static function consultaYaRegistrada(CI_DB_mysqli_driver $conexion, $codigoConsultaOriginal){
        $conexion->select("codigo");
        $conexion->from("mails_consultas.mails_respuesta_consultas");
        $conexion->where("id_respuesta_origen", $codigoConsultaOriginal);
        $query = $conexion->get();
        $resp = $query->result_array();
        return count($resp) > 0; 
    }
    
}

?>