<?php

/**
* Class Tpaises
*
*Class  Tpaises maneja todos los aspectos de paises
*
* @package  SistemaIGA
* @subpackage Paises
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tpaises extends class_general{

    /**
    * id de paises
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * pais de paises
    * @var pais varchar
    * @access public
    */
    public $pais;

    /**
    * moneda de paises
    * @var moneda int
    * @access public
    */
    public $moneda;

    /**
    * id_alf de paises
    * @var id_alf varchar (requerido)
    * @access public
    */
    public $id_alf;

    /**
    * url_imagen de paises
    * @var url_imagen varchar (requerido)
    * @access public
    */
    public $url_imagen;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'general.paises';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase paises
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->pais = $arrConstructor[0]['pais'];
                $this->moneda = $arrConstructor[0]['moneda'];
                $this->id_alf = $arrConstructor[0]['id_alf'];
                $this->url_imagen = $arrConstructor[0]['url_imagen'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['pais'] = $this->pais;
        $arrTemp['moneda'] = $this->moneda;
        $arrTemp['id_alf'] = $this->id_alf == '' ? null : $this->id_alf;
        $arrTemp['url_imagen'] = $this->url_imagen == '' ? null : $this->url_imagen;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase paises o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarPaises(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto paises
     *
     * @return integer
     */
    public function getCodigoPaises(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de paises según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de paises y los valores son los valores a actualizar
     */
    public function setPaises(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["pais"]))
            $retorno = "pais";
        else if (!isset($arrCamposValores["moneda"]))
            $retorno = "moneda";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setPaises");
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
    * retorna los campos presentes en la tabla paises en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposPaises(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.paises");
    }

    /**
    * Buscar registros en la tabla paises
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de paises o la cantdad de registros segun el parametro contar
    */
    static function listarPaises(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.paises", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>