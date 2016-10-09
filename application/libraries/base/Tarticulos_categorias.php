<?php

/**
* Class Tarticulos_categorias
*
*Class  Tarticulos_categorias maneja todos los aspectos de articulos_categorias
*
* @package  SistemaIGA
* @subpackage Articulos_categorias
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tarticulos_categorias extends class_general{

    /**
    * codigo de articulos_categorias
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de articulos_categorias
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * cod_padre de articulos_categorias
    * @var cod_padre int (requerido)
    * @access public
    */
    public $cod_padre;

    /**
    * baja de articulos_categorias
    * @var baja smallint
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
    protected $nombreTabla = 'articulos_categorias';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase articulos_categorias
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
                $this->cod_padre = $arrConstructor[0]['cod_padre'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['cod_padre'] = $this->cod_padre == '' ? null : $this->cod_padre;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase articulos_categorias o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarArticulos_categorias(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto articulos_categorias
     *
     * @return integer
     */
    public function getCodigoArticulos_categorias(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de articulos_categorias según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de articulos_categorias y los valores son los valores a actualizar
     */
    public function setArticulos_categorias(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setArticulos_categorias");
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
    * retorna los campos presentes en la tabla articulos_categorias en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposArticulos_categorias(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "articulos_categorias");
    }

    /**
    * Buscar registros en la tabla articulos_categorias
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de articulos_categorias o la cantdad de registros segun el parametro contar
    */
    static function listarArticulos_categorias(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "articulos_categorias", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>