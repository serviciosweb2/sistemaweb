<?php

/**
* Class Tfinanciacion
*
*Class  Tfinanciacion maneja todos los aspectos de financiacion
*
* @package  SistemaIGA
* @subpackage Financiacion
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfinanciacion extends class_general{

    /**
    * codigo de financiacion
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de financiacion
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * estado de financiacion
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * numero_cuotas de financiacion
    * @var numero_cuotas smallint
    * @access public
    */
    public $numero_cuotas;

    /**
    * interes de financiacion
    * @var interes decimal (requerido)
    * @access public
    */
    public $interes;


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
    protected $nombreTabla = 'financiacion';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase financiacion
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
                $this->estado = $arrConstructor[0]['estado'];
                $this->numero_cuotas = $arrConstructor[0]['numero_cuotas'];
                $this->interes = $arrConstructor[0]['interes'];
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
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        $arrTemp['numero_cuotas'] = $this->numero_cuotas;
        $arrTemp['interes'] = $this->interes == '' ? null : $this->interes;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase financiacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFinanciacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto financiacion
     *
     * @return integer
     */
    public function getCodigoFinanciacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de financiacion según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de financiacion y los valores son los valores a actualizar
     */
    public function setFinanciacion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["numero_cuotas"]))
            $retorno = "numero_cuotas";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFinanciacion");
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
    * retorna los campos presentes en la tabla financiacion en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFinanciacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "financiacion");
    }

    /**
    * Buscar registros en la tabla financiacion
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de financiacion o la cantdad de registros segun el parametro contar
    */
    static function listarFinanciacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "financiacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>