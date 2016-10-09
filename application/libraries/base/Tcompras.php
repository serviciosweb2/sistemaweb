<?php

/**
* Class Tcompras
*
*Class  Tcompras maneja todos los aspectos de compras
*
* @package  SistemaIGA
* @subpackage Compras
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcompras extends class_general{

    /**
    * codigo de compras
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_proveedor de compras
    * @var cod_proveedor int
    * @access public
    */
    public $cod_proveedor;

    /**
    * cod_usuario_creador de compras
    * @var cod_usuario_creador int
    * @access public
    */
    public $cod_usuario_creador;

    /**
    * fecha_real de compras
    * @var fecha_real datetime
    * @access public
    */
    public $fecha_real;

    /**
    * estado de compras
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha de compras
    * @var fecha date
    * @access public
    */
    public $fecha;


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
    protected $nombreTabla = 'compras';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase compras
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
                $this->cod_proveedor = $arrConstructor[0]['cod_proveedor'];
                $this->cod_usuario_creador = $arrConstructor[0]['cod_usuario_creador'];
                $this->fecha_real = $arrConstructor[0]['fecha_real'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha = $arrConstructor[0]['fecha'];
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
        $arrTemp['cod_proveedor'] = $this->cod_proveedor;
        $arrTemp['cod_usuario_creador'] = $this->cod_usuario_creador;
        $arrTemp['fecha_real'] = $this->fecha_real;
        $arrTemp['estado'] = $this->estado == '' ? 'confirmada' : $this->estado;
        $arrTemp['fecha'] = $this->fecha;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase compras o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCompras(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto compras
     *
     * @return integer
     */
    public function getCodigoCompras(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de compras según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de compras y los valores son los valores a actualizar
     */
    public function setCompras(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_proveedor"]))
            $retorno = "cod_proveedor";
        else if (!isset($arrCamposValores["cod_usuario_creador"]))
            $retorno = "cod_usuario_creador";
        else if (!isset($arrCamposValores["fecha_real"]))
            $retorno = "fecha_real";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCompras");
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
    * retorna los campos presentes en la tabla compras en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCompras(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "compras");
    }

    /**
    * Buscar registros en la tabla compras
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de compras o la cantdad de registros segun el parametro contar
    */
    static function listarCompras(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "compras", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>