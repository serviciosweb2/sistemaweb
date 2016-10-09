<?php

/**
* Class Tmedio_transferencias
*
*Class  Tmedio_transferencias maneja todos los aspectos de medio_transferencias
*
* @package  SistemaIGA
* @subpackage Medio_transferencias
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmedio_transferencias extends class_general{

    /**
    * codigo de medio_transferencias
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_banco de medio_transferencias
    * @var cod_banco int (requerido)
    * @access public
    */
    public $cod_banco;

    /**
    * nro_transaccion de medio_transferencias
    * @var nro_transaccion bigint (requerido)
    * @access public
    */
    public $nro_transaccion;

    /**
    * fecha_hora de medio_transferencias
    * @var fecha_hora datetime (requerido)
    * @access public
    */
    public $fecha_hora;

    /**
    * cuenta_nombre de medio_transferencias
    * @var cuenta_nombre varchar (requerido)
    * @access public
    */
    public $cuenta_nombre;

    /**
    * cod_cobro de medio_transferencias
    * @var cod_cobro int (requerido)
    * @access public
    */
    public $cod_cobro;


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
    protected $nombreTabla = 'medio_transferencias';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase medio_transferencias
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
                $this->cod_banco = $arrConstructor[0]['cod_banco'];
                $this->nro_transaccion = $arrConstructor[0]['nro_transaccion'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->cuenta_nombre = $arrConstructor[0]['cuenta_nombre'];
                $this->cod_cobro = $arrConstructor[0]['cod_cobro'];
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
        $arrTemp['cod_banco'] = $this->cod_banco == '' ? null : $this->cod_banco;
        $arrTemp['nro_transaccion'] = $this->nro_transaccion == '' ? null : $this->nro_transaccion;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        $arrTemp['cuenta_nombre'] = $this->cuenta_nombre == '' ? null : $this->cuenta_nombre;
        $arrTemp['cod_cobro'] = $this->cod_cobro == '' ? null : $this->cod_cobro;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase medio_transferencias o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMedio_transferencias(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto medio_transferencias
     *
     * @return integer
     */
    public function getCodigoMedio_transferencias(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de medio_transferencias según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de medio_transferencias y los valores son los valores a actualizar
     */
    public function setMedio_transferencias(array $arrCamposValores){
        $retorno = "";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMedio_transferencias");
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
    * retorna los campos presentes en la tabla medio_transferencias en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMedio_transferencias(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "medio_transferencias");
    }

    /**
    * Buscar registros en la tabla medio_transferencias
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de medio_transferencias o la cantdad de registros segun el parametro contar
    */
    static function listarMedio_transferencias(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "medio_transferencias", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>