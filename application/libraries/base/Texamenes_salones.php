<?php

/**
* Class Texamenes_salones
*
*Class  Texamenes_salones maneja todos los aspectos de examenes_salones
*
* @package  SistemaIGA
* @subpackage Examenes_salones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Texamenes_salones extends class_general{

    /**
    * codigo de examenes_salones
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_salon de examenes_salones
    * @var cod_salon int
    * @access public
    */
    public $cod_salon;

    /**
    * cod_examen de examenes_salones
    * @var cod_examen int
    * @access public
    */
    public $cod_examen;

    /**
    * baja de examenes_salones
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
    protected $nombreTabla = 'examenes_salones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase examenes_salones
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
                $this->cod_salon = $arrConstructor[0]['cod_salon'];
                $this->cod_examen = $arrConstructor[0]['cod_examen'];
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
        $arrTemp['cod_salon'] = $this->cod_salon;
        $arrTemp['cod_examen'] = $this->cod_examen;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase examenes_salones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarExamenes_salones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto examenes_salones
     *
     * @return integer
     */
    public function getCodigoExamenes_salones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de examenes_salones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de examenes_salones y los valores son los valores a actualizar
     */
    public function setExamenes_salones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_salon"]))
            $retorno = "cod_salon";
        else if (!isset($arrCamposValores["cod_examen"]))
            $retorno = "cod_examen";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setExamenes_salones");
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
    * retorna los campos presentes en la tabla examenes_salones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposExamenes_salones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "examenes_salones");
    }

    /**
    * Buscar registros en la tabla examenes_salones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de examenes_salones o la cantdad de registros segun el parametro contar
    */
    static function listarExamenes_salones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "examenes_salones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>