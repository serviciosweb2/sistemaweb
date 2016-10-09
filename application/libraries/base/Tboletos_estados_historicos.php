<?php

/**
* Class Tboletos_estados_historicos
*
*Class  Tboletos_estados_historicos maneja todos los aspectos de boletos_estados_historicos
*
* @package  SistemaIGA
* @subpackage Boletos_estados_historicos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tboletos_estados_historicos extends class_general{

    /**
    * codigo de boletos_estados_historicos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_boleto de boletos_estados_historicos
    * @var cod_boleto int
    * @access public
    */
    public $cod_boleto;

    /**
    * estado de boletos_estados_historicos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha de boletos_estados_historicos
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * segmento_t de boletos_estados_historicos
    * @var segmento_t varchar (requerido)
    * @access public
    */
    public $segmento_t;

    /**
    * segmento_u de boletos_estados_historicos
    * @var segmento_u varchar (requerido)
    * @access public
    */
    public $segmento_u;

    /**
    * cod_usuario de boletos_estados_historicos
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;

    /**
    * numero_secuencia de boletos_estados_historicos
    * @var numero_secuencia int (requerido)
    * @access public
    */
    public $numero_secuencia;


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
    protected $nombreTabla = 'bancos.boletos_estados_historicos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase boletos_estados_historicos
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
                $this->cod_boleto = $arrConstructor[0]['cod_boleto'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->segmento_t = $arrConstructor[0]['segmento_t'];
                $this->segmento_u = $arrConstructor[0]['segmento_u'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->numero_secuencia = $arrConstructor[0]['numero_secuencia'];
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
        $arrTemp['cod_boleto'] = $this->cod_boleto;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['segmento_t'] = $this->segmento_t == '' ? null : $this->segmento_t;
        $arrTemp['segmento_u'] = $this->segmento_u == '' ? null : $this->segmento_u;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['numero_secuencia'] = $this->numero_secuencia == '' ? null : $this->numero_secuencia;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase boletos_estados_historicos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarBoletos_estados_historicos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto boletos_estados_historicos
     *
     * @return integer
     */
    public function getCodigoBoletos_estados_historicos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de boletos_estados_historicos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de boletos_estados_historicos y los valores son los valores a actualizar
     */
    public function setBoletos_estados_historicos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_boleto"]))
            $retorno = "cod_boleto";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setBoletos_estados_historicos");
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
    * retorna los campos presentes en la tabla boletos_estados_historicos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposBoletos_estados_historicos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "bancos.boletos_estados_historicos");
    }

    /**
    * Buscar registros en la tabla boletos_estados_historicos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de boletos_estados_historicos o la cantdad de registros segun el parametro contar
    */
    static function listarBoletos_estados_historicos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "bancos.boletos_estados_historicos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>