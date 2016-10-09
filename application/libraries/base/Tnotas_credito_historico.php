<?php

/**
 * Class Tnotas_credito_historico
 *
 * Class  Tnotas_credito_historico maneja todos los aspectos de notas_credito_historico
 *
 * @package  SistemaIGA
 * @subpackage Notas_credito_historico
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Tnotas_credito_historico extends class_general {

    /**
     * codigo de notas_credito_historico
     * @var codigo int
     * @access protected
     */
    protected $codigo;

    /**
     * cod_nc de notas_credito_historico
     * @var cod_nc int
     * @access public
     */
    public $cod_nc;

    /**
     * estado de notas_credito_historico
     * @var estado enum
     * @access public
     */
    public $estado;

    /**
     * fecha_hora de notas_credito_historico
     * @var fecha_hora datetime
     * @access public
     */
    public $fecha_hora;

    /**
     * cod_motivo de notas_credito_historico
     * @var cod_motivo int (requerido)
     * @access public
     */
    public $cod_motivo;

    /**
     * comentario de notas_credito_historico
     * @var comentario varchar (requerido)
     * @access public
     */
    public $comentario;

    /**
     * cod_usuario de notas_credito_historico
     * @var cod_usuario int (requerido)
     * @access public
     */
    public $cod_usuario;

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
    protected $nombreTabla = 'notas_credito_historico';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase notas_credito_historico
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1) {
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0) {
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->cod_nc = $arrConstructor[0]['cod_nc'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
                $this->cod_motivo = $arrConstructor[0]['cod_motivo'];
                $this->comentario = $arrConstructor[0]['comentario'];
                $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
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
    protected function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['cod_nc'] = $this->cod_nc;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['fecha_hora'] = $this->fecha_hora;
        $arrTemp['cod_motivo'] = $this->cod_motivo == '' ? null : $this->cod_motivo;
        $arrTemp['comentario'] = $this->comentario == '' ? null : $this->comentario;
        $arrTemp['cod_usuario'] = $this->cod_usuario == '' ? null : $this->cod_usuario;
        $arrTemp['motivo'] = $this->motivo == '' ? null : $this->motivo;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */

    /**
     * Guarda un objeto nuevo de la clase notas_credito_historico o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarNotas_credito_historico() {
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto notas_credito_historico
     *
     * @return integer
     */
    public function getCodigoNotas_credito_historico() {
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de notas_credito_historico según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de notas_credito_historico y los valores son los valores a actualizar
     */
    public function setNotas_credito_historico(array $arrCamposValores) {
        $retorno = "";
        if (!isset($arrCamposValores["cod_nc"]))
            $retorno = "cod_nc";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["fecha_hora"]))
            $retorno = "fecha_hora";
        if ($retorno <> "") {
            die("falta el parametro " . $retorno . " en setNotas_credito_historico");
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
     * retorna los campos presentes en la tabla notas_credito_historico en formato array
     * 
     * @param CI_DB_mysqli_driver $connection   La conexion actual
     * @return array
     */
    static function camposNotas_credito_historico(CI_DB_mysqli_driver $conexion) {
        return parent::_campos($conexion, "notas_credito_historico");
    }

    /**
     * Buscar registros en la tabla notas_credito_historico
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de notas_credito_historico o la cantdad de registros segun el parametro contar
     */
    static function listarNotas_credito_historico(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        return parent::_listar($conexion, "notas_credito_historico", $condiciones, $limite, $orden, $grupo, $contar);
    }

}

?>