<?php

/**
* Class Talertas
*
*Class  Talertas maneja todos los aspectos de alertas
*
* @package  SistemaIGA
* @subpackage Alertas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Talertas extends class_general{

    /**
    * codigo de alertas
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * tipo_alerta de alertas
    * @var tipo_alerta enum
    * @access public
    */
    public $tipo_alerta;

    /**
    * fecha_hora de alertas
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * mensaje de alertas
    * @var mensaje varchar
    * @access public
    */
    public $mensaje;


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
    protected $nombreTabla = 'alertas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase alertas
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
                $this->tipo_alerta = $arrConstructor[0]['tipo_alerta'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->mensaje = $arrConstructor[0]['mensaje'];
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
        $arrTemp['tipo_alerta'] = $this->tipo_alerta;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['mensaje'] = $this->mensaje;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase alertas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarAlertas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto alertas
     *
     * @return integer
     */
    public function getCodigoAlertas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de alertas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de alertas y los valores son los valores a actualizar
     */
    public function setAlertas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipo_alerta"]))
            $retorno = "tipo_alerta";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        else if (!isset($arrCamposValores["mensaje"]))
            $retorno = "mensaje";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setAlertas");
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
    * retorna los campos presentes en la tabla alertas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposAlertas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "alertas");
    }

    /**
    * Buscar registros en la tabla alertas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de alertas o la cantdad de registros segun el parametro contar
    */
    static function listarAlertas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "alertas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>