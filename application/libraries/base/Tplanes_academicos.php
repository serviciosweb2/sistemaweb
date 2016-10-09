<?php

/**
* Class Tplanes_academicos
*
*Class  Tplanes_academicos maneja todos los aspectos de planes_academicos
*
* @package  SistemaIGA
* @subpackage Planes_academicos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tplanes_academicos extends class_general{

    /**
    * codigo de planes_academicos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de planes_academicos
    * @var nombre varchar (requerido)
    * @access public
    */
    public $nombre;

    /**
    * cod_curso de planes_academicos
    * @var cod_curso int
    * @access public
    */
    public $cod_curso;

    /**
    * estado de planes_academicos
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'general.planes_academicos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase planes_academicos
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->cod_curso = $arrConstructor[0]['cod_curso'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['nombre'] = $this->nombre == '' ? null : $this->nombre;
        $arrTemp['cod_curso'] = $this->cod_curso;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase planes_academicos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPlanes_academicos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto planes_academicos
     *
     * @return integer
     */
    public function getCodigoPlanes_academicos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de planes_academicos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de planes_academicos y los valores son los valores a actualizar
     */
    public function setPlanes_academicos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_curso"]))
            $retorno = "cod_curso";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPlanes_academicos");
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
    * retorna los campos presentes en la tabla planes_academicos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPlanes_academicos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.planes_academicos");
    }

    /**
    * Buscar registros en la tabla planes_academicos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de planes_academicos o la cantdad de registros segun el parametro contar
    */
    static function listarPlanes_academicos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.planes_academicos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>