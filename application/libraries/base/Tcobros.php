<?php

/**
* Class Tcobros
*
*Class  Tcobros maneja todos los aspectos de cobros
*
* @package  SistemaIGA
* @subpackage Cobros
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcobros extends class_general{

    /**
    * codigo de cobros
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * importe de cobros
    * @var importe double
    * @access public
    */
    public $importe;

    /**
    * medio_pago de cobros
    * @var medio_pago int
    * @access public
    */
    public $medio_pago;

    /**
    * estado de cobros
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * cod_usuario de cobros
    * @var cod_usuario int (requerido)
    * @access public
    */
    public $cod_usuario;

    /**
    * fechaalta de cobros
    * @var fechaalta datetime
    * @access public
    */
    public $fechaalta;

    /**
    * cod_alumno de cobros
    * @var cod_alumno int
    * @access public
    */
    public $cod_alumno;

    /**
    * fechareal de cobros
    * @var fechareal date
    * @access public
    */
    public $fechareal;

    /**
    * cod_caja de cobros
    * @var cod_caja int (requerido)
    * @access public
    */
    public $cod_caja;

    /**
    * periodo de cobros
    * @var periodo int (requerido)
    * @access public
    */
    public $periodo;


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
    protected $nombreTabla = 'cobros';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cobros
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
                $this->importe = $arrConstructor[0]['importe'];
                $this->medio_pago = $arrConstructor[0]['medio_pago'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->fechaalta = $arrConstructor[0]['fechaalta'];
                $this->cod_alumno = $arrConstructor[0]['cod_alumno'];
                $this->fechareal = $arrConstructor[0]['fechareal'];
                $this->cod_caja = $arrConstructor[0]['cod_caja'];
                $this->periodo = $arrConstructor[0]['periodo'];
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
        $arrTemp['importe'] = $this->importe == '' ? '0.00' : $this->importe;
        $arrTemp['medio_pago'] = $this->medio_pago;
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        $arrTemp['fechaalta'] = $this->fechaalta;
        $arrTemp['cod_alumno'] = $this->cod_alumno;
        $arrTemp['fechareal'] = $this->fechareal;
        $arrTemp['cod_caja'] = $this->cod_caja == '' ? null : $this->cod_caja;
        $arrTemp['periodo'] = $this->periodo == '' ? null : $this->periodo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cobros o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCobros(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cobros
     *
     * @return integer
     */
    public function getCodigoCobros(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cobros según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cobros y los valores son los valores a actualizar
     */
    public function setCobros(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["importe"]))
            $retorno = "importe";
        else if (!isset($arrCamposValores["medio_pago"]))
            $retorno = "medio_pago";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fechaalta"]))
            $retorno = "fechaalta";
        else if (!isset($arrCamposValores["cod_alumno"]))
            $retorno = "cod_alumno";
        else if (!isset($arrCamposValores["fechareal"]))
            $retorno = "fechareal";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCobros");
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
    * retorna los campos presentes en la tabla cobros en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCobros(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "cobros");
    }

    /**
    * Buscar registros en la tabla cobros
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cobros o la cantdad de registros segun el parametro contar
    */
    static function listarCobros(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "cobros", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>