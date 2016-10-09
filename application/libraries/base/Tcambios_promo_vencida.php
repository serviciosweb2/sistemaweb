<?php

/**
* Class Tcambios_promo_vencida
*
*Class  Tcambios_promo_vencida maneja todos los aspectos de cambios_promo_vencida
*
* @package  SistemaIGA
* @subpackage Cambios_promo_vencida
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcambios_promo_vencida extends class_general{

    /**
    * codigo de cambios_promo_vencida
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_matricula de cambios_promo_vencida
    * @var cod_matricula int
    * @access public
    */
    public $cod_matricula;

    /**
    * fecha_hora de cambios_promo_vencida
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;


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
    protected $nombreTabla = 'cambios_promo_vencida';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cambios_promo_vencida
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
                $this->cod_matricula = $arrConstructor[0]['cod_matricula'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
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
        $arrTemp['cod_matricula'] = $this->cod_matricula;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cambios_promo_vencida o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCambios_promo_vencida(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cambios_promo_vencida
     *
     * @return integer
     */
    public function getCodigoCambios_promo_vencida(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cambios_promo_vencida según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cambios_promo_vencida y los valores son los valores a actualizar
     */
    public function setCambios_promo_vencida(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_matricula"]))
            $retorno = "cod_matricula";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCambios_promo_vencida");
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
    * retorna los campos presentes en la tabla cambios_promo_vencida en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCambios_promo_vencida(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "cambios_promo_vencida");
    }

    /**
    * Buscar registros en la tabla cambios_promo_vencida
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cambios_promo_vencida o la cantdad de registros segun el parametro contar
    */
    static function listarCambios_promo_vencida(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "cambios_promo_vencida", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>