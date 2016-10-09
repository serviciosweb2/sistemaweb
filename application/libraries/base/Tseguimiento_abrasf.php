<?php

/**
* Class Tseguimiento_abrasf
*
*Class  Tseguimiento_abrasf maneja todos los aspectos de seguimiento_abrasf
*
* @package  SistemaIGA
* @subpackage Seguimiento_abrasf
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tseguimiento_abrasf extends class_general{

    /**
    * id de seguimiento_abrasf
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * cod_filial de seguimiento_abrasf
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * cod_factura de seguimiento_abrasf
    * @var cod_factura int
    * @access public
    */
    public $cod_factura;

    /**
    * mensaje de seguimiento_abrasf
    * @var mensaje text (requerido)
    * @access public
    */
    public $mensaje;

    /**
    * estado de seguimiento_abrasf
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * numero de seguimiento_abrasf
    * @var numero varchar (requerido)
    * @access public
    */
    public $numero;

    /**
    * fecha_envio_lote de seguimiento_abrasf
    * @var fecha_envio_lote datetime (requerido)
    * @access public
    */
    public $fecha_envio_lote;

    /**
    * protocolo de seguimiento_abrasf
    * @var protocolo varchar (requerido)
    * @access public
    */
    public $protocolo;

    /**
    * numero_lote de seguimiento_abrasf
    * @var numero_lote varchar (requerido)
    * @access public
    */
    public $numero_lote;


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
    protected $nombreTabla = 'general.seguimiento_abrasf';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase seguimiento_abrasf
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
                $this->mensaje = $arrConstructor[0]['mensaje'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->numero = $arrConstructor[0]['numero'];
                $this->fecha_envio_lote = $arrConstructor[0]['fecha_envio_lote'];
                $this->protocolo = $arrConstructor[0]['protocolo'];
                $this->numero_lote = $arrConstructor[0]['numero_lote'];
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
        $arrTemp['mensaje'] = $this->mensaje == '' ? null : $this->mensaje;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['numero'] = $this->numero == '' ? null : $this->numero;
        $arrTemp['fecha_envio_lote'] = $this->fecha_envio_lote == '' ? null : $this->fecha_envio_lote;
        $arrTemp['protocolo'] = $this->protocolo == '' ? null : $this->protocolo;
        $arrTemp['numero_lote'] = $this->numero_lote == '' ? null : $this->numero_lote;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase seguimiento_abrasf o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSeguimiento_abrasf(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seguimiento_abrasf
     *
     * @return integer
     */
    public function getCodigoSeguimiento_abrasf(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de seguimiento_abrasf según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de seguimiento_abrasf y los valores son los valores a actualizar
     */
    public function setSeguimiento_abrasf(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["cod_factura"]))
            $retorno = "cod_factura";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSeguimiento_abrasf");
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
    * retorna los campos presentes en la tabla seguimiento_abrasf en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSeguimiento_abrasf(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.seguimiento_abrasf");
    }

    /**
    * Buscar registros en la tabla seguimiento_abrasf
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de seguimiento_abrasf o la cantdad de registros segun el parametro contar
    */
    static function listarSeguimiento_abrasf(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.seguimiento_abrasf", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>