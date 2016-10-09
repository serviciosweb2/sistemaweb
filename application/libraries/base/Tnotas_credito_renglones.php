<?php

/**
* Class Tnotas_credito_renglones
*
*Class  Tnotas_credito_renglones maneja todos los aspectos de notas_credito_renglones
*
* @package  SistemaIGA
* @subpackage Notas_credito_renglones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tnotas_credito_renglones extends class_general{

    /**
    * codigo de notas_credito_renglones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_nota_credito de notas_credito_renglones
    * @var cod_nota_credito int
    * @access public
    */
    public $cod_nota_credito;

    /**
    * cod_cta_cte de notas_credito_renglones
    * @var cod_cta_cte int
    * @access public
    */
    public $cod_cta_cte;

    /**
    * valor de notas_credito_renglones
    * @var valor double
    * @access public
    */
    public $valor;


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
    protected $nombreTabla = 'notas_credito_renglones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase notas_credito_renglones
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
                $this->cod_nota_credito = $arrConstructor[0]['cod_nota_credito'];
                $this->cod_cta_cte = $arrConstructor[0]['cod_cta_cte'];
                $this->valor = $arrConstructor[0]['valor'];
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
        $arrTemp['cod_nota_credito'] = $this->cod_nota_credito;
        $arrTemp['cod_cta_cte'] = $this->cod_cta_cte;
        $arrTemp['valor'] = $this->valor == '' ? '0.00' : $this->valor;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase notas_credito_renglones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarNotas_credito_renglones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto notas_credito_renglones
     *
     * @return integer
     */
    public function getCodigoNotas_credito_renglones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de notas_credito_renglones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de notas_credito_renglones y los valores son los valores a actualizar
     */
    public function setNotas_credito_renglones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_nota_credito"]))
            $retorno = "cod_nota_credito";
        else if (!isset($arrCamposValores["cod_cta_cte"]))
            $retorno = "cod_cta_cte";
        else if (!isset($arrCamposValores["valor"]))
            $retorno = "valor";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setNotas_credito_renglones");
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
    * retorna los campos presentes en la tabla notas_credito_renglones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposNotas_credito_renglones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "notas_credito_renglones");
    }

    /**
    * Buscar registros en la tabla notas_credito_renglones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de notas_credito_renglones o la cantdad de registros segun el parametro contar
    */
    static function listarNotas_credito_renglones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "notas_credito_renglones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>