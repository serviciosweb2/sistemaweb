<?php

/**
* Class Tarticulos
*
*Class  Tarticulos maneja todos los aspectos de articulos
*
* @package  SistemaIGA
* @subpackage Articulos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tarticulos extends class_general{

    /**
    * codigo de articulos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de articulos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * costo de articulos
    * @var costo double (requerido)
    * @access public
    */
    public $costo;

    /**
    * cod_unidad_medida de articulos
    * @var cod_unidad_medida int (requerido)
    * @access public
    */
    public $cod_unidad_medida;

    /**
    * estado de articulos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * cod_categoria de articulos
    * @var cod_categoria int (requerido)
    * @access public
    */
    public $cod_categoria;

    /**
    * stock de articulos
    * @var stock double (requerido)
    * @access public
    */
    public $stock;


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
    protected $nombreTabla = 'articulos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase articulos
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
                $this->costo = $arrConstructor[0]['costo'];
                $this->cod_unidad_medida = $arrConstructor[0]['cod_unidad_medida'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->cod_categoria = $arrConstructor[0]['cod_categoria'];
                $this->stock = $arrConstructor[0]['stock'];
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
        $arrTemp['costo'] = $this->costo == '' ? null : $this->costo;
        $arrTemp['cod_unidad_medida'] = $this->cod_unidad_medida == '' ? null : $this->cod_unidad_medida;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['cod_categoria'] = $this->cod_categoria == '' ? null : $this->cod_categoria;
        $arrTemp['stock'] = $this->stock == '' ? null : $this->stock;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase articulos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarArticulos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto articulos
     *
     * @return integer
     */
    public function getCodigoArticulos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de articulos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de articulos y los valores son los valores a actualizar
     */
    public function setArticulos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setArticulos");
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
    * retorna los campos presentes en la tabla articulos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposArticulos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "articulos");
    }

    /**
    * Buscar registros en la tabla articulos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de articulos o la cantdad de registros segun el parametro contar
    */
    static function listarArticulos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "articulos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>