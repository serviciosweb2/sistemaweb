<?php

/**
* Class Tcobro_estado_historico
*
*Class  Tcobro_estado_historico maneja todos los aspectos de cobro_estado_historico
*
* @package  SistemaIGA
* @subpackage Cobro_estado_historico
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcobro_estado_historico extends class_general{

    /**
    * codigo de cobro_estado_historico
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * cod_cobro de cobro_estado_historico
    * @var cod_cobro int
    * @access public
    */
    public $cod_cobro;

    /**
    * estado de cobro_estado_historico
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * fecha_hora de cobro_estado_historico
    * @var fecha_hora datetime
    * @access public
    */
    public $fecha_hora;

    /**
    * cod_motivo de cobro_estado_historico
    * @var cod_motivo int (requerido)
    * @access public
    */
    public $cod_motivo;

    /**
    * comentario de cobro_estado_historico
    * @var comentario varchar (requerido)
    * @access public
    */
    public $comentario;

    /**
    * cod_usuario de cobro_estado_historico
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
    protected $nombreTabla = 'cobro_estado_historico';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cobro_estado_historico
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
                $this->cod_cobro = $arrConstructor[0]['cod_cobro'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->cod_motivo = $arrConstructor[0]['cod_motivo'];
                $this->comentario = $arrConstructor[0]['comentario'];
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
        $arrTemp['cod_cobro'] = $this->cod_cobro;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['cod_motivo'] = $this->cod_motivo == '' ? null : $this->cod_motivo;
        $arrTemp['comentario'] = $this->comentario == '' ? null : $this->comentario;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cobro_estado_historico o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCobro_estado_historico(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cobro_estado_historico
     *
     * @return integer
     */
    public function getCodigoCobro_estado_historico(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cobro_estado_historico según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cobro_estado_historico y los valores son los valores a actualizar
     */
    public function setCobro_estado_historico(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_cobro"]))
            $retorno = "cod_cobro";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCobro_estado_historico");
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
    * retorna los campos presentes en la tabla cobro_estado_historico en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCobro_estado_historico(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "cobro_estado_historico");
    }

    /**
    * Buscar registros en la tabla cobro_estado_historico
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cobro_estado_historico o la cantdad de registros segun el parametro contar
    */
    static function listarCobro_estado_historico(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "cobro_estado_historico", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>