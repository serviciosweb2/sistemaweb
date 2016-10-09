<?php

/**
* Class Tciclos
*
*Class  Tciclos maneja todos los aspectos de ciclos
*
* @package  SistemaIGA
* @subpackage Ciclos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tciclos extends class_general{

    /**
    * codigo de ciclos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * fecha_inicio_ciclo de ciclos
    * @var fecha_inicio_ciclo date
    * @access public
    */
    public $fecha_inicio_ciclo;

    /**
    * fecha_fin_ciclo de ciclos
    * @var fecha_fin_ciclo date
    * @access public
    */
    public $fecha_fin_ciclo;

    /**
    * nombre de ciclos
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * abreviatura de ciclos
    * @var abreviatura varchar
    * @access public
    */
    public $abreviatura;


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
    protected $nombreTabla = 'general.ciclos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ciclos
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
                $this->fecha_inicio_ciclo = $arrConstructor[0]['fecha_inicio_ciclo'];
                $this->fecha_fin_ciclo = $arrConstructor[0]['fecha_fin_ciclo'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->abreviatura = $arrConstructor[0]['abreviatura'];
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
        $arrTemp['fecha_inicio_ciclo'] = $this->fecha_inicio_ciclo;
        $arrTemp['fecha_fin_ciclo'] = $this->fecha_fin_ciclo;
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['abreviatura'] = $this->abreviatura;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ciclos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCiclos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ciclos
     *
     * @return integer
     */
    public function getCodigoCiclos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ciclos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ciclos y los valores son los valores a actualizar
     */
    public function setCiclos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["fecha_inicio_ciclo"]))
            $retorno = "fecha_inicio_ciclo";
        else if (!isset($arrCamposValores["fecha_fin_ciclo"]))
            $retorno = "fecha_fin_ciclo";
        else if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["abreviatura"]))
            $retorno = "abreviatura";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCiclos");
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
    * retorna los campos presentes en la tabla ciclos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCiclos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.ciclos");
    }

    /**
    * Buscar registros en la tabla ciclos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ciclos o la cantdad de registros segun el parametro contar
    */
    static function listarCiclos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.ciclos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>