<?php

/**
* Class Thabilitaciones_rematriculacion
*
*Class  Thabilitaciones_rematriculacion maneja todos los aspectos de las habilitaciones de rematriculacion.
*
* @package  SistemaIGA
* @subpackage Rematriculaciones
* @author   Manuel Pajon <manu.pajon@gmail.com>
* @version  $Revision: 1.1 $
* @access   private
*/
class Thabilitaciones_rematriculacion extends class_general{

    /**
    * codigo.
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * codigo de filial.
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * matricula del alumno
    * @var cod_matricula int
    * @access public
    */
    public $cod_matricula;

    /**
    * curso, se guarda por conveniencia.
    * @var cod_matricula int
    * @access public
    */
    public $cod_curso;


    /**
    * comision, se guarda por conveniencia.
    * @var cod_matricula int
    * @access public
    */
    public $cod_comision;

    /**
    * usuario habilitante
    * @var cod_usuario
    * @access public
    */
    public $cod_usuario;

    /**
    * motivo
    * @var motivo varchar
    * @access public
    */
    public $motivo;

    /**
    * fechaalta de alumnos
    * @var fecha_desde datetime
    * @access public
    */
    public $fecha_desde;



    /**
    * fechaalta de alumnos
    * @var fecha_hasta datetime
    * @access public
    */
    public $fecha_hasta;


    /**
    * tipo, "Habilitado"|"Deshabilitado"
    * @var tipo enum
    * @access public
    */
    public $tipo;

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
    protected $nombreTabla = 'general.habilitaciones_rematriculacion';

    /**
     * Constructor de la Clase habilitaciones_rematriculacion
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
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->cod_matricula = $arrConstructor[0]['cod_matricula'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
                $this->motivo = $arrConstructor[0]['motivo'];
                $this->fecha_desde = $arrConstructor[0]['fecha_desde'];
                $this->fecha_hasta = $arrConstructor[0]['fecha_hasta'];
                $this->cod_curso = $arrConstructor[0]['cod_curso'];
                $this->cod_comision = $arrConstructor[0]['cod_comision'];
                $this->tipo = $arrConstructor[0]['tipo'];
            } else {
                $this->codigo = -1;
            }
        } else {
            $this->codigo = -1;
        }
    }


    /* PROTECTED FUNCTIONS */

    /**
    * Devuelve el objeto con todas sus propiedades y valores en formato array
    * 
    * @return array
    */
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
     //   $arrTemp['codigo'] = $this->codigo;
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['cod_matricula'] = $this->cod_matricula;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['motivo'] = $this->motivo;
        $arrTemp['fecha_desde'] = $this->fecha_desde;
        $arrTemp['fecha_hasta'] = $this->fecha_hasta;
        $arrTemp['cod_curso'] = $this->cod_curso;
        $arrTemp['cod_comision'] = $this->cod_comision;
        $arrTemp['tipo'] = $this->tipo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase habilitaciones_rematriculacion o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarHabilitaciones_rematriculacion(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto habilitaciones_rematriculacion
     *
     * @return integer
     */
    public function getCodigoHabilitaciones_rematriculacion(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de alumnos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de habilitaciones_rematriculacion y los valores son los valores a actualizar
     */
    public function setHabilitaciones_rematriculacion(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["cod_matricula"]))
            $retorno = "cod_matricula";
        else if (!isset($arrCamposValores["cod_usuario"]))
            $retorno = "cod_usuario";
        else if (!isset($arrCamposValores["motivo"]))
            $retorno = "motivo";
        else if (!isset($arrCamposValores["fecha_desde"]))
            $retorno = "fecha_desde";
        else if (!isset($arrCamposValores["fecha_hasta"]))
            $retorno = "fecha_hasta";
        else if (!isset($arrCamposValores["cod_curso"]))
            $retorno = "cod_curso";
        else if (!isset($arrCamposValores["cod_comision"]))
            $retorno = "cod_comision";
        else if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setHabilitaciones_rematriculacion");
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
    * retorna los campos presentes en la tabla alumnos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposHabilitaciones_rematriculacion(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "habilitaciones_rematriculacion");
    }

    /**
    * Buscar registros en la tabla alumnos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de alumnos o la cantdad de registros segun el parametro contar
    */
    static function listarHabilitaciones_rrematriculacion(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "habilitaciones_rematriculacion", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>
