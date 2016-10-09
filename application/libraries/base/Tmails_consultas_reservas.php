<?php

/**
* Class Tmails_consultas_reservas
*
*Class  Tmails_consultas_reservas maneja todos los aspectos de mails_consultas_reservas
*
* @package  SistemaIGA
* @subpackage Mails_consultas_reservas
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmails_consultas_reservas extends class_general{

    /**
    * id de mails_consultas_reservas
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * id_mails_consultas de mails_consultas_reservas
    * @var id_mails_consultas int
    * @access public
    */
    public $id_mails_consultas;

    /**
    * id_plan de mails_consultas_reservas
    * @var id_plan int
    * @access public
    */
    public $id_plan;

    /**
    * id_comision de mails_consultas_reservas
    * @var id_comision int
    * @access public
    */
    public $id_comision;


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
    protected $nombreTabla = 'inscripcionesweb.mails_consultas_reservas';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase mails_consultas_reservas
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
                $this->id_mails_consultas = $arrConstructor[0]['id_mails_consultas'];
                $this->id_plan = $arrConstructor[0]['id_plan'];
                $this->id_comision = $arrConstructor[0]['id_comision'];
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
        $arrTemp['id_mails_consultas'] = $this->id_mails_consultas;
        $arrTemp['id_plan'] = $this->id_plan;
        $arrTemp['id_comision'] = $this->id_comision;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase mails_consultas_reservas o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMails_consultas_reservas(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto mails_consultas_reservas
     *
     * @return integer
     */
    public function getCodigoMails_consultas_reservas(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de mails_consultas_reservas según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de mails_consultas_reservas y los valores son los valores a actualizar
     */
    public function setMails_consultas_reservas(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["id_mails_consultas"]))
            $retorno = "id_mails_consultas";
        else if (!isset($arrCamposValores["id_plan"]))
            $retorno = "id_plan";
        else if (!isset($arrCamposValores["id_comision"]))
            $retorno = "id_comision";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMails_consultas_reservas");
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
    * retorna los campos presentes en la tabla mails_consultas_reservas en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMails_consultas_reservas(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "inscripcionesweb.mails_consultas_reservas");
    }

    /**
    * Buscar registros en la tabla mails_consultas_reservas
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de mails_consultas_reservas o la cantdad de registros segun el parametro contar
    */
    static function listarMails_consultas_reservas(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "inscripcionesweb.mails_consultas_reservas", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>