<?php

/**
* Class Tctacte_comentarios
*
*Class  Tctacte_comentarios maneja todos los aspectos de ctacte_comentarios
*
* @package  SistemaIGA
* @subpackage Ctacte_comentarios
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tctacte_comentarios extends class_general{

    /**
    * codigo de ctacte_comentarios
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * id_ctacte de ctacte_comentarios
    * @var id_ctacte int
    * @access public
    */
    public $id_ctacte;

    /**
    * fecha_hora de ctacte_comentarios
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * comentario de ctacte_comentarios
    * @var comentario varchar
    * @access public
    */
    public $comentario;

    /**
    * id_usuario de ctacte_comentarios
    * @var id_usuario int
    * @access public
    */
    public $id_usuario;

    /**
    * baja de ctacte_comentarios
    * @var baja int
    * @access public
    */
    public $baja;


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
    protected $nombreTabla = 'ctacte_comentarios';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ctacte_comentarios
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
                $this->id_ctacte = $arrConstructor[0]['id_ctacte'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->comentario = $arrConstructor[0]['comentario'];
                $this->id_usuario = $arrConstructor[0]['id_usuario'];
                $this->baja = $arrConstructor[0]['baja'];
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
        $arrTemp['id_ctacte'] = $this->id_ctacte;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['comentario'] = $this->comentario;
        $arrTemp['id_usuario'] = $this->id_usuario;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ctacte_comentarios o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCtacte_comentarios(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ctacte_comentarios
     *
     * @return integer
     */
    public function getCodigoCtacte_comentarios(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ctacte_comentarios según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ctacte_comentarios y los valores son los valores a actualizar
     */
    public function setCtacte_comentarios(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_ctacte"]))
            $retorno = "id_ctacte";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["comentario"]))
            $retorno = "comentario";
        else if (!isset($arrCamposValores["id_usuario"]))
            $retorno = "id_usuario";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCtacte_comentarios");
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
    * retorna los campos presentes en la tabla ctacte_comentarios en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCtacte_comentarios(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "ctacte_comentarios");
    }

    /**
    * Buscar registros en la tabla ctacte_comentarios
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ctacte_comentarios o la cantdad de registros segun el parametro contar
    */
    static function listarCtacte_comentarios(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "ctacte_comentarios", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>