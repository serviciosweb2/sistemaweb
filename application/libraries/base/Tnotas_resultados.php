<?php

/**
 * Class Tnotas_resultados
 *
 * Class  Tnotas_resultados maneja todos los aspectos de notas_resultados
 *
 * @package  SistemaIGA
 * @subpackage Notas_resultados
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Tnotas_resultados extends class_general {

    /**
     * cod_inscripcion de notas_resultados
     * @var cod_inscripcion int
     * @access protected
     */
    public $cod_inscripcion;

    /**
     * tipo_resultado de notas_resultados
     * @var tipo_resultado enum
     * @access public
     */
    public $tipo_resultado;

    /**
     * nota de notas_resultados
     * @var nota double (requerido)
     * @access public
     */
    public $nota;

    /**
     * primaryKey de la tabla
     * @var primaryKey var
     * @access protected
     */
    
    public $porcentaje_aprobado;

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
    protected $nombreTabla = 'notas_resultados';
    
    protected $exists = false;
    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase notas_resultados
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $cod_inscripcion, $tipo_resultado) {
        $this->oConnection = $conexion;
        $this->cod_inscripcion = $cod_inscripcion;
        $this->tipo_resultado = $tipo_resultado;
        $arrConstructor = $this->_constructor($cod_inscripcion,$tipo_resultado);
            if (count($arrConstructor) > 0) {
                $this->cod_inscripcion = $arrConstructor[0]['cod_inscripcion'];
                $this->tipo_resultado = $arrConstructor[0]['tipo_resultado'];
                $this->nota = $arrConstructor[0]['nota'];
                $this->porcentaje_aprobado = $arrConstructor[0]['porcentaje_aprobado'];
                $this->exists = true;
            } else {
               $this->exists = false;
            }
        
    }

    /* PORTECTED FUNCTIONS */
    protected function _constructor($cod_inscripcion, $tipo_resultado){
        $query = $this->oConnection->select('*')
                        ->from($this->nombreTabla)
                        ->where(array(
                            "cod_inscripcion" => $cod_inscripcion,
                            "tipo_resultado" => $tipo_resultado
                        ))->get();
        $arrConstructor = $query->result_array();   
        return $arrConstructor;
    }
    /**
     * Devuelve el objeto con todas sus propiedades y valores en formato array
     * 
     * @return array
     */
    protected function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['cod_inscripcion'] = $this->cod_inscripcion;
        $arrTemp['tipo_resultado'] = $this->tipo_resultado;
        $arrTemp['nota'] = $this->nota == '' ? null : $this->nota;
        $arrTemp['porcentaje_aprobado'] = $this->porcentaje_aprobado == '' ? null : $this->porcentaje_aprobado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */

    /**
     * Guarda un objeto nuevo de la clase notas_resultados o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarNotas_resultados() {
        if ($this->exists){
            $condiciones = array("cod_inscripcion" => $this->cod_inscripcion, 
                "tipo_resultado" => $this->tipo_resultado);
            return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto(), $condiciones);
        } else {
            $this->exists = $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
            return $this->exists;
        }
    }

    /**
     * Retorna el codigo del objeto notas_resultados
     *
     * @return integer
     */
    public function getCodigoNotas_resultados() {
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de notas_resultados según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de notas_resultados y los valores son los valores a actualizar
     */
    public function setNotas_resultados(array $arrCamposValores) {
        $retorno = "";
        if (!isset($arrCamposValores["nota"]))
            $retorno = "nota";
        else if (!isset($arrCamposValores["porcentaje_aprobado"]))
            $retorno = "porcentaje_aprobado";
        if ($retorno <> "") {
            die("falta el parametro " . $retorno . " en setNotas_resultados");
        } else {
            foreach ($this as $key => $value) {
                if (isset($arrCamposValores[$key])) {
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }

    /* STATIC FUNCTIONS */

    /**
     * retorna los campos presentes en la tabla notas_resultados en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposNotas_resultados(CI_DB_mysqli_driver $conexion) {
        return parent::_campos($conexion, "notas_resultados");
    }

    /**
     * Buscar registros en la tabla notas_resultados
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de notas_resultados o la cantdad de registros segun el parametro contar
     */
    static function listarNotas_resultados(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        return parent::_listar($conexion, "notas_resultados", $condiciones, $limite, $orden, $grupo, $contar);
    }

}

?>