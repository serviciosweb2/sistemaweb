<?php

/**
* Class Tmails_respuesta_consultas
*
*Class  Tmails_respuesta_consultas maneja todos los aspectos de mails_respuesta_consultas
*
* @package  SistemaIGA
* @subpackage Mails_respuesta_consultas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmails_respuesta_consultas extends class_general{

    /**
    * codigo de mails_respuesta_consultas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * html_respuesta de mails_respuesta_consultas
    * @var html_respuesta longtext
    * @access public
    */
    public $html_respuesta;

    /**
    * cod_consulta de mails_respuesta_consultas
    * @var cod_consulta int
    * @access public
    */
    public $cod_consulta;

    /**
    * emisor de mails_respuesta_consultas
    * @var emisor smallint
    * @access public
    */
    public $emisor;

    /**
    * vista de mails_respuesta_consultas
    * @var vista datetime (requerido)
    * @access public
    */
    public $vista;

    /**
    * fecha_hora de mails_respuesta_consultas
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * estado de mails_respuesta_consultas
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * id_usuario de mails_respuesta_consultas
    * @var id_usuario int (requerido)
    * @access public
    */
    public $id_usuario;

    /**
    * id_respuesta_origen de mails_respuesta_consultas
    * @var id_respuesta_origen int (requerido)
    * @access public
    */
    public $id_respuesta_origen;


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
    protected $nombreTabla = 'mails_consultas.mails_respuesta_consultas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase mails_respuesta_consultas
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
                $this->html_respuesta = $arrConstructor[0]['html_respuesta'];
                $this->cod_consulta = $arrConstructor[0]['cod_consulta'];
                $this->emisor = $arrConstructor[0]['emisor'];
                $this->vista = $arrConstructor[0]['vista'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->id_usuario = $arrConstructor[0]['id_usuario'];
                $this->id_respuesta_origen = $arrConstructor[0]['id_respuesta_origen'];
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
        $arrTemp['html_respuesta'] = $this->html_respuesta;
        $arrTemp['cod_consulta'] = $this->cod_consulta;
        $arrTemp['emisor'] = $this->emisor == '' ? '0' : $this->emisor;
        $arrTemp['vista'] = $this->vista == '' ? null : $this->vista;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['id_usuario'] = $this->id_usuario == '' ? null : $this->id_usuario;
        $arrTemp['id_respuesta_origen'] = $this->id_respuesta_origen == '' ? null : $this->id_respuesta_origen;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase mails_respuesta_consultas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMails_respuesta_consultas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto mails_respuesta_consultas
     *
     * @return integer
     */
    public function getCodigoMails_respuesta_consultas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de mails_respuesta_consultas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de mails_respuesta_consultas y los valores son los valores a actualizar
     */
    public function setMails_respuesta_consultas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["html_respuesta"]))
            $retorno = "html_respuesta";
        else if (!isset($arrCamposValores["cod_consulta"]))
            $retorno = "cod_consulta";
        else if (!isset($arrCamposValores["emisor"]))
            $retorno = "emisor";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMails_respuesta_consultas");
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
    * retorna los campos presentes en la tabla mails_respuesta_consultas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMails_respuesta_consultas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "mails_consultas.mails_respuesta_consultas");
    }

    /**
    * Buscar registros en la tabla mails_respuesta_consultas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de mails_respuesta_consultas o la cantdad de registros segun el parametro contar
    */
    static function listarMails_respuesta_consultas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "mails_consultas.mails_respuesta_consultas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>