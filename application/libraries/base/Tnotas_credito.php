<?php

/**
* Class Tnotas_credito
*
*Class  Tnotas_credito maneja todos los aspectos de notas_credito
*
* @package  SistemaIGA
* @subpackage Notas_credito
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tnotas_credito extends class_general{

    /**
    * codigo de notas_credito
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * importe de notas_credito
    * @var importe double
    * @access public
    */
    public $importe;

    /**
    * estado de notas_credito
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * cod_usuario de notas_credito
    * @var cod_usuario int
    * @access public
    */
    public $cod_usuario;
    
      /**
    * cod_alumno de notas_credito
    * @var cod_alumno int
    * @access public
    */
    public $cod_alumno;

    /**
    * fechaalta de notas_credito
    * @var fecha_alta datetime
    * @access public
    */
    public $fechaalta;

    /**
    * fechareal de notas_credito
    * @var fecha_real date
    * @access public
    */
    public $fechareal;

    /**
    * motivo de notas_credito
    * @var motivo varchar (requerido)
    * @access public
    */
    public $motivo;


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
    protected $nombreTabla = 'notas_credito';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase notas_credito
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
                $this->estado = $arrConstructor[0]['estado'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->cod_alumno = $arrConstructor[0]['cod_alumno'];
                $this->fechaalta = $arrConstructor[0]['fechaalta'];
                $this->fechareal = $arrConstructor[0]['fechareal'];
                $this->motivo = $arrConstructor[0]['motivo'];
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
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['cod_alumno'] = $this->cod_alumno;
        $arrTemp['fechaalta'] = $this->fechaalta;
        $arrTemp['fechareal'] = $this->fechareal;
        $arrTemp['motivo'] = $this->motivo == '' ? null : $this->motivo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase notas_credito o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarNotas_credito(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto notas_credito
     *
     * @return integer
     */
    public function getCodigoNotas_credito(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de notas_credito según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de notas_credito y los valores son los valores a actualizar
     */
    public function setNotas_credito(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["importe"]))
            $retorno = "importe";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["cod_alumno"]))
            $retorno = "cod_alumno";
        else if (!isset($arrCamposValores["fechaalta"]))
            $retorno = "fechaalta";
        else if (!isset($arrCamposValores["fechareal"]))
            $retorno = "fechareal";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setNotas_credito");
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
    * retorna los campos presentes en la tabla notas_credito en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposNotas_credito(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "notas_credito");
    }

    /**
    * Buscar registros en la tabla notas_credito
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de notas_credito o la cantdad de registros segun el parametro contar
    */
    static function listarNotas_credito(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "notas_credito", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>