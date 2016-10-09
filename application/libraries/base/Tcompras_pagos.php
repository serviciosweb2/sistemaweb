<?php

/**
* Class Tcompras_pagos
*
*Class  Tcompras_pagos maneja todos los aspectos de compras_pagos
*
* @package  SistemaIGA
* @subpackage Compras_pagos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcompras_pagos extends class_general{

    /**
    * codigo de compras_pagos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_compra de compras_pagos
    * @var cod_compra int
    * @access public
    */
    public $cod_compra;

    /**
    * fecha_pago de compras_pagos
    * @var fecha_pago date
    * @access public
    */
    public $fecha_pago;

    /**
    * total de compras_pagos
    * @var total double
    * @access public
    */
    public $total;

    /**
    * estado de compras_pagos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha de compras_pagos
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * cod_usuario de compras_pagos
    * @var cod_usuario int (requerido)
    * @access public
    */
    public $cod_usuario;


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
    protected $nombreTabla = 'compras_pagos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase compras_pagos
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
                $this->cod_compra = $arrConstructor[0]['cod_compra'];
                $this->fecha_pago = $arrConstructor[0]['fecha_pago'];
                $this->total = $arrConstructor[0]['total'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
        $arrTemp['cod_compra'] = $this->cod_compra;
        $arrTemp['fecha_pago'] = $this->fecha_pago;
        $arrTemp['total'] = $this->total;
        $arrTemp['estado'] = $this->estado == '' ? 'confirmado' : $this->estado;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase compras_pagos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCompras_pagos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto compras_pagos
     *
     * @return integer
     */
    public function getCodigoCompras_pagos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de compras_pagos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de compras_pagos y los valores son los valores a actualizar
     */
    public function setCompras_pagos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_compra"]))
            $retorno = "cod_compra";
        else if (!isset($arrCamposValores["fecha_pago"]))
            $retorno = "fecha_pago";
        else if (!isset($arrCamposValores["total"]))
            $retorno = "total";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCompras_pagos");
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
    * retorna los campos presentes en la tabla compras_pagos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCompras_pagos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "compras_pagos");
    }

    /**
    * Buscar registros en la tabla compras_pagos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de compras_pagos o la cantdad de registros segun el parametro contar
    */
    static function listarCompras_pagos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "compras_pagos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>