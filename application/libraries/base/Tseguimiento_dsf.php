<?php

/**
* Class Tseguimiento_dsf
*
*Class  Tseguimiento_dsf maneja todos los aspectos de seguimiento_dsf
*
* @package  SistemaIGA
* @subpackage Seguimiento_dsf
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tseguimiento_dsf extends class_general{

    /**
    * id de seguimiento_dsf
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * cod_filial de seguimiento_dsf
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * cod_factura de seguimiento_dsf
    * @var cod_factura int
    * @access public
    */
    public $cod_factura;

    /**
    * numero_rps de seguimiento_dsf
    * @var numero_rps varchar (requerido)
    * @access public
    */
    public $numero_rps;

    /**
    * numero_lote de seguimiento_dsf
    * @var numero_lote varchar (requerido)
    * @access public
    */
    public $numero_lote;

    /**
    * fecha_envio_lote de seguimiento_dsf
    * @var fecha_envio_lote datetime (requerido)
    * @access public
    */
    public $fecha_envio_lote;

    /**
    * respuesta de seguimiento_dsf
    * @var respuesta text (requerido)
    * @access public
    */
    public $respuesta;

    /**
    * estado de seguimiento_dsf
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * numero_nfe de seguimiento_dsf
    * @var numero_nfe varchar (requerido)
    * @access public
    */
    public $numero_nfe;

    /**
    * codigo_verificacion de seguimiento_dsf
    * @var codigo_verificacion varchar (requerido)
    * @access public
    */
    public $codigo_verificacion;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "id";
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
    protected $nombreTabla = 'general.seguimiento_dsf';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase seguimiento_dsf
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $id = null){
        $this->oConnection = $conexion;
        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->cod_factura = $arrConstructor[0]['cod_factura'];
                $this->numero_rps = $arrConstructor[0]['numero_rps'];
                $this->numero_lote = $arrConstructor[0]['numero_lote'];
                $this->fecha_envio_lote = $arrConstructor[0]['fecha_envio_lote'];
                $this->respuesta = $arrConstructor[0]['respuesta'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->numero_nfe = $arrConstructor[0]['numero_nfe'];
                $this->codigo_verificacion = $arrConstructor[0]['codigo_verificacion'];
            } else {
                $this->id = -1;
            }
        } else {
            $this->id = -1;
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
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['cod_factura'] = $this->cod_factura;
        $arrTemp['numero_rps'] = $this->numero_rps == '' ? null : $this->numero_rps;
        $arrTemp['numero_lote'] = $this->numero_lote == '' ? null : $this->numero_lote;
        $arrTemp['fecha_envio_lote'] = $this->fecha_envio_lote == '' ? null : $this->fecha_envio_lote;
        $arrTemp['respuesta'] = $this->respuesta == '' ? null : $this->respuesta;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['numero_nfe'] = $this->numero_nfe == '' ? null : $this->numero_nfe;
        $arrTemp['codigo_verificacion'] = $this->codigo_verificacion == '' ? null : $this->codigo_verificacion;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase seguimiento_dsf o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSeguimiento_dsf(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seguimiento_dsf
     *
     * @return integer
     */
    public function getCodigoSeguimiento_dsf(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de seguimiento_dsf según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de seguimiento_dsf y los valores son los valores a actualizar
     */
    public function setSeguimiento_dsf(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["cod_factura"]))
            $retorno = "cod_factura";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSeguimiento_dsf");
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
    * retorna los campos presentes en la tabla seguimiento_dsf en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSeguimiento_dsf(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.seguimiento_dsf");
    }

    /**
    * Buscar registros en la tabla seguimiento_dsf
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de seguimiento_dsf o la cantdad de registros segun el parametro contar
    */
    static function listarSeguimiento_dsf(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.seguimiento_dsf", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>