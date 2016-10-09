<?php

/**
* Class Tctacte
*
*Class  Tctacte maneja todos los aspectos de ctacte
*
* @package  SistemaIGA
* @subpackage Ctacte
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tctacte extends class_general{

    /**
    * codigo de ctacte
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_alumno de ctacte
    * @var cod_alumno int
    * @access public
    */
    public $cod_alumno;

    /**
    * nrocuota de ctacte
    * @var nrocuota int
    * @access public
    */
    public $nrocuota;

    /**
    * importe de ctacte
    * @var importe double
    * @access public
    */
    public $importe;

    /**
    * fechavenc de ctacte
    * @var fechavenc date (requerido)
    * @access public
    */
    public $fechavenc;

    /**
    * pagado de ctacte
    * @var pagado double
    * @access public
    */
    public $pagado;

    /**
    * habilitado de ctacte
    * @var habilitado smallint
    * @access public
    */
    public $habilitado;

    /**
    * cod_concepto de ctacte
    * @var cod_concepto int
    * @access public
    */
    public $cod_concepto;

    /**
    * concepto de ctacte
    * @var concepto int
    * @access public
    */
    public $concepto;

    /**
    * financiacion de ctacte
    * @var financiacion int
    * @access public
    */
    public $financiacion;

    /**
    * fecha_creacion de ctacte
    * @var fecha_creacion datetime
    * @access public
    */
    public $fecha_creacion;

    /**
    * descripcion de ctacte
    * @var descripcion varchar (requerido)
    * @access public
    */
    public $descripcion;


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
    protected $nombreTabla = 'ctacte';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ctacte
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
                $this->cod_alumno = $arrConstructor[0]['cod_alumno'];
                $this->nrocuota = $arrConstructor[0]['nrocuota'];
                $this->importe = $arrConstructor[0]['importe'];
                $this->fechavenc = $arrConstructor[0]['fechavenc'];
                $this->pagado = $arrConstructor[0]['pagado'];
                $this->habilitado = $arrConstructor[0]['habilitado'];
                $this->cod_concepto = $arrConstructor[0]['cod_concepto'];
                $this->concepto = $arrConstructor[0]['concepto'];
                $this->financiacion = $arrConstructor[0]['financiacion'];
                $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
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
        $arrTemp['cod_alumno'] = $this->cod_alumno;
        $arrTemp['nrocuota'] = $this->nrocuota;
        $arrTemp['importe'] = $this->importe;
        $arrTemp['fechavenc'] = $this->fechavenc == '' ? null : $this->fechavenc;
        $arrTemp['pagado'] = $this->pagado == '' ? '0.00' : $this->pagado;
        $arrTemp['habilitado'] = $this->habilitado == '' ? '1' : $this->habilitado;
        $arrTemp['cod_concepto'] = $this->cod_concepto == '' ? '1' : $this->cod_concepto;
        $arrTemp['concepto'] = $this->concepto;
        $arrTemp['financiacion'] = $this->financiacion == '' ? '1' : $this->financiacion;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        $arrTemp['descripcion'] = $this->descripcion == '' ? null : $this->descripcion;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase ctacte o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCtacte(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto ctacte
     *
     * @return integer
     */
    public function getCodigoCtacte(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de ctacte según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ctacte y los valores son los valores a actualizar
     */
    public function setCtacte(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_alumno"]))
            $retorno = "cod_alumno";
        else if (!isset($arrCamposValores["nrocuota"]))
            $retorno = "nrocuota";
        else if (!isset($arrCamposValores["importe"]))
            $retorno = "importe";
        else if (!isset($arrCamposValores["pagado"]))
            $retorno = "pagado";
        else if (!isset($arrCamposValores["habilitado"]))
            $retorno = "habilitado";
        else if (!isset($arrCamposValores["cod_concepto"]))
            $retorno = "cod_concepto";
        else if (!isset($arrCamposValores["concepto"]))
            $retorno = "concepto";
        else if (!isset($arrCamposValores["financiacion"]))
            $retorno = "financiacion";
        else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCtacte");
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
    * retorna los campos presentes en la tabla ctacte en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCtacte(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "ctacte");
    }

    /**
    * Buscar registros en la tabla ctacte
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de ctacte o la cantdad de registros segun el parametro contar
    */
    static function listarCtacte(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "ctacte", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>