<?php

/**
* Class Tmails_consultas
*
*Class  Tmails_consultas maneja todos los aspectos de mails_consultas
*
* @package  SistemaIGA
* @subpackage Mails_consultas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmails_consultas extends class_general{

    /**
    * codigo de mails_consultas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_filial de mails_consultas
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * mail de mails_consultas
    * @var mail varchar
    * @access public
    */
    public $mail;

    /**
    * nombre de mails_consultas
    * @var nombre varchar (requerido)
    * @access public
    */
    public $nombre;

    /**
    * fechahora de mails_consultas
    * @var fechahora datetime
    * @access public
    */
    public $fechahora;

    /**
    * asunto de mails_consultas
    * @var asunto varchar
    * @access public
    */
    public $asunto;

    /**
    * telefono de mails_consultas
    * @var telefono varchar
    * @access public
    */
    public $telefono;

    /**
    * estado de mails_consultas
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * generado_por_filial de mails_consultas
    * @var generado_por_filial int
    * @access public
    */
    public $generado_por_filial;

    /**
    * notificar de mails_consultas
    * @var notificar smallint
    * @access public
    */
    public $notificar;

    /**
    * cod_curso_asunto de mails_consultas
    * @var cod_curso_asunto int (requerido)
    * @access public
    */
    public $cod_curso_asunto;

    /**
    * respuesta_automatica_enviada de mails_consultas
    * @var respuesta_automatica_enviada smallint
    * @access public
    */
    public $respuesta_automatica_enviada;

    /**
    * tipo_asunto de mails_consultas
    * @var tipo_asunto enum (requerido)
    * @access public
    */
    public $tipo_asunto;

    /**
    * destacar de mails_consultas
    * @var destacar smallint
    * @access public
    */
    public $destacar;
    
    /**
     * @var int Como nos conoció
     * @access public
     */
    public $como_nos_conocio_codigo;

    /**
     * id_facebook_lead de mails_consultas
     * @var id_facebook_lead
     * @access public
     */
    public $id_facebook_lead;

    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "codigo";
    /**
    * conexion utilizada por el objeto
    * @var oConnection CI_DB_mysqli_driver
    * @access protected
    */
    protected $oConnection;

    /**
    * nombre de la tabla donde se guardan los objetos
    * @var nombreTabla varchar
    * @access protected
    */
    protected $nombreTabla = 'mails_consultas.mails_consultas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase mails_consultas
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->mail = $arrConstructor[0]['mail'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->fechahora = $arrConstructor[0]['fechahora'];
                $this->asunto = $arrConstructor[0]['asunto'];
                $this->telefono = $arrConstructor[0]['telefono'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->generado_por_filial = $arrConstructor[0]['generado_por_filial'];
                $this->notificar = $arrConstructor[0]['notificar'];
                $this->cod_curso_asunto = $arrConstructor[0]['cod_curso_asunto'];
                $this->respuesta_automatica_enviada = $arrConstructor[0]['respuesta_automatica_enviada'];
                $this->tipo_asunto = $arrConstructor[0]['tipo_asunto'];
                $this->destacar = $arrConstructor[0]['destacar'];
                $this->como_nos_conocio_codigo = $arrConstructor[0]['como_nos_conocio_codigo'];
                $this->id_facebook_lead = $arrConstructor[0]['id_facebook_lead'];
            } else {
                $this->codigo = -1;
            }
        } else {
            $this->codigo = -1;
        }
    }

    /* PORTECTED FUNCTIONS */

    /**
    * Devuelve el objeto con todas sus propiedades y valores en formato array
    * 
    * @return array
    */
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['cod_filial'] = $this->cod_filial == '' ? '0' : $this->cod_filial;
        $arrTemp['mail'] = $this->mail;
        $arrTemp['nombre'] = $this->nombre == '' ? null : $this->nombre;
        $arrTemp['fechahora'] = $this->fechahora;
        $arrTemp['asunto'] = $this->asunto;
        $arrTemp['telefono'] = $this->telefono;
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        $arrTemp['generado_por_filial'] = $this->generado_por_filial == '' ? '0' : $this->generado_por_filial;
        $arrTemp['notificar'] = $this->notificar == '' ? '1' : $this->notificar;
        $arrTemp['cod_curso_asunto'] = $this->cod_curso_asunto == '' ? null : $this->cod_curso_asunto;
        $arrTemp['respuesta_automatica_enviada'] = $this->respuesta_automatica_enviada == '' ? '0' : $this->respuesta_automatica_enviada;
        $arrTemp['tipo_asunto'] = $this->tipo_asunto == '' ? null : $this->tipo_asunto;
        $arrTemp['destacar'] = $this->destacar == '' ? '0' : $this->destacar;
        $arrTemp['como_nos_conocio_codigo'] = $this->como_nos_conocio_codigo == '' ? null : $this->como_nos_conocio_codigo;
        $arrTemp['id_facebook_lead'] = $this->id_facebook_lead = '' ? null : $this->id_facebook_lead;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase mails_consultas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMails_consultas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto mails_consultas
     *
     * @return integer
     */
    public function getCodigoMails_consultas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de mails_consultas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de mails_consultas y los valores son los valores a actualizar
     */
    public function setMails_consultas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["mail"]))
            $retorno = "mail";
        else if (!isset($arrCamposValores["fechahora"]))
            $retorno = "fechahora";
        else if (!isset($arrCamposValores["asunto"]))
            $retorno = "asunto";
        else if (!isset($arrCamposValores["telefono"]))
            $retorno = "telefono";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["generado_por_filial"]))
            $retorno = "generado_por_filial";
        else if (!isset($arrCamposValores["notificar"]))
            $retorno = "notificar";
        else if (!isset($arrCamposValores["respuesta_automatica_enviada"]))
            $retorno = "respuesta_automatica_enviada";
        else if (!isset($arrCamposValores["destacar"]))
            $retorno = "destacar";
        else if (!isset($arrCamposValores["como_nos_conocio_codigo"]))
            $retorno = "como_nos_conocio_codigo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMails_consultas");
        } else {
            foreach ($this as $key => $value){
                if (isset($arrCamposValores[$key])){
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }

    /* STATIC FUNCTIONS */

    /**
    * retorna los campos presentes en la tabla mails_consultas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMails_consultas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "mails_consultas.mails_consultas");
    }

    /**
    * Buscar registros en la tabla mails_consultas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de mails_consultas o la cantdad de registros segun el parametro contar
    */
    static function listarMails_consultas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "mails_consultas.mails_consultas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>