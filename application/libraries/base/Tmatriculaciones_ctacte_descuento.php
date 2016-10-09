<?php

/**
* Class Tmatriculaciones_ctacte_descuento
*
*Class  Tmatriculaciones_ctacte_descuento maneja todos los aspectos de matriculaciones_ctacte_descuento
*
* @package  SistemaIGA
* @subpackage Matriculaciones_ctacte_descuento
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmatriculaciones_ctacte_descuento extends class_general{

    /**
    * codigo de matriculaciones_ctacte_descuento
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_ctacte de matriculaciones_ctacte_descuento
    * @var cod_ctacte int
    * @access public
    */
    public $cod_ctacte;

    /**
    * descuento de matriculaciones_ctacte_descuento
    * @var descuento double (requerido)
    * @access public
    */
    public $descuento;

    /**
    * estado de matriculaciones_ctacte_descuento
    * @var estado enum (requerido)
    * @access public
    */
    public $estado;

    /**
    * dias_vencido de matriculaciones_ctacte_descuento
    * @var dias_vencido int
    * @access public
    */
    public $dias_vencido;

    /**
    * descripcion de matriculaciones_ctacte_descuento
    * @var descripcion varchar (requerido)
    * @access public
    */
    public $descripcion;

    /**
    * cod_usuario de matriculaciones_ctacte_descuento
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;

    /**
    * forma_descuento de matriculaciones_ctacte_descuento
    * @var forma_descuento enum
    * @access public
    */
    public $forma_descuento;

    /**
    * fecha de matriculaciones_ctacte_descuento
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * importe de matriculaciones_ctacte_descuento
    * @var importe double
    * @access public
    */
    public $importe;

    /**
    * activo de matriculaciones_ctacte_descuento
    * @var activo smallint
    * @access public
    */
    public $activo;


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
    protected $nombreTabla = 'matriculaciones_ctacte_descuento';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase matriculaciones_ctacte_descuento
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
                $this->cod_ctacte = $arrConstructor[0]['cod_ctacte'];
                $this->descuento = $arrConstructor[0]['descuento'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->dias_vencido = $arrConstructor[0]['dias_vencido'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->forma_descuento = $arrConstructor[0]['forma_descuento'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->importe = $arrConstructor[0]['importe'];
                $this->activo = $arrConstructor[0]['activo'];
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
        $arrTemp['cod_ctacte'] = $this->cod_ctacte;
        $arrTemp['descuento'] = $this->descuento == '' ? null : $this->descuento;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        $arrTemp['dias_vencido'] = $this->dias_vencido == '' ? '0' : $this->dias_vencido;
        $arrTemp['descripcion'] = $this->descripcion == '' ? null : $this->descripcion;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['forma_descuento'] = $this->forma_descuento == '' ? 'plan_pago' : $this->forma_descuento;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['importe'] = $this->importe == '' ? '0.00' : $this->importe;
        $arrTemp['activo'] = $this->activo == '' ? '1' : $this->activo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase matriculaciones_ctacte_descuento o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMatriculaciones_ctacte_descuento(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto matriculaciones_ctacte_descuento
     *
     * @return integer
     */
    public function getCodigoMatriculaciones_ctacte_descuento(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de matriculaciones_ctacte_descuento según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de matriculaciones_ctacte_descuento y los valores son los valores a actualizar
     */
    public function setMatriculaciones_ctacte_descuento(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_ctacte"]))
            $retorno = "cod_ctacte";
        else if (!isset($arrCamposValores["dias_vencido"]))
            $retorno = "dias_vencido";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["forma_descuento"]))
            $retorno = "forma_descuento";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["importe"]))
            $retorno = "importe";
        else if (!isset($arrCamposValores["activo"]))
            $retorno = "activo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMatriculaciones_ctacte_descuento");
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
    * retorna los campos presentes en la tabla matriculaciones_ctacte_descuento en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMatriculaciones_ctacte_descuento(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "matriculaciones_ctacte_descuento");
    }

    /**
    * Buscar registros en la tabla matriculaciones_ctacte_descuento
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de matriculaciones_ctacte_descuento o la cantdad de registros segun el parametro contar
    */
    static function listarMatriculaciones_ctacte_descuento(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "matriculaciones_ctacte_descuento", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>