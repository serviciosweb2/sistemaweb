<?php

/**
* Class Tlanding
*
*Class  Tlanding maneja todos los aspectos de landing
*
* @package  SistemaIGA
* @subpackage Landing
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tlanding extends class_general{

    /**
    * id de landing
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * id_curso de landing
    * @var id_curso int
    * @access public
    */
    public $id_curso;

    /**
    * descuento de landing
    * @var descuento double
    * @access public
    */
    public $descuento;

    /**
    * fecha_cierre de landing
    * @var fecha_cierre date
    * @access public
    */
    public $fecha_cierre;

    /**
    * nombre de landing
    * @var nombre varchar
    * @access public
    */
    public $nombre;


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
    protected $nombreTabla = 'general.landing';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase landing
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
                $this->id_curso = $arrConstructor[0]['id_curso'];
                $this->descuento = $arrConstructor[0]['descuento'];
                $this->fecha_cierre = $arrConstructor[0]['fecha_cierre'];
                $this->nombre = $arrConstructor[0]['nombre'];
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
        $arrTemp['id_curso'] = $this->id_curso;
        $arrTemp['descuento'] = $this->descuento;
        $arrTemp['fecha_cierre'] = $this->fecha_cierre;
        $arrTemp['nombre'] = $this->nombre;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase landing o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarLanding(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto landing
     *
     * @return integer
     */
    public function getCodigoLanding(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de landing según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de landing y los valores son los valores a actualizar
     */
    public function setLanding(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_curso"]))
            $retorno = "id_curso";
        else if (!isset($arrCamposValores["descuento"]))
            $retorno = "descuento";
        else if (!isset($arrCamposValores["fecha_cierre"]))
            $retorno = "fecha_cierre";
        else if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setLanding");
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
    * retorna los campos presentes en la tabla landing en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposLanding(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.landing");
    }

    /**
    * Buscar registros en la tabla landing
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de landing o la cantdad de registros segun el parametro contar
    */
    static function listarLanding(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.landing", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>