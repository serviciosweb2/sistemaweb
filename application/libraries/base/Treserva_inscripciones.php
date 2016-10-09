<?php

/**
* Class Treserva_inscripciones
*
*Class  Treserva_inscripciones maneja todos los aspectos de reserva_inscripciones
*
* @package  SistemaIGA
* @subpackage Reserva_inscripciones
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Treserva_inscripciones extends class_general{

    /**
    * id de reserva_inscripciones
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * nombre de reserva_inscripciones
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * email de reserva_inscripciones
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * telefono de reserva_inscripciones
    * @var telefono varchar
    * @access public
    */
    public $telefono;

    /**
    * id_comision de reserva_inscripciones
    * @var id_comision int
    * @access public
    */
    public $id_comision;

    /**
    * id_filial de reserva_inscripciones
    * @var id_filial int
    * @access public
    */
    public $id_filial;

    /**
    * id_plan de reserva_inscripciones
    * @var id_plan int
    * @access public
    */
    public $id_plan;

    /**
    * fecha de reserva_inscripciones
    * @var fecha datetime
    * @access public
    */
    public $fecha;

    /**
    * estado de reserva_inscripciones
    * @var estado varchar
    * @access public
    */
    public $estado;

    /**
    * confirmacion_enviada de reserva_inscripciones
    * @var confirmacion_enviada smallint
    * @access public
    */
    public $confirmacion_enviada;

    /**
    * id_curso de reserva_inscripciones
    * @var id_curso int (requerido)
    * @access public
    */
    public $id_curso;


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
    protected $nombreTabla = 'inscripcionesweb.reserva_inscripciones';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase reserva_inscripciones
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->email = $arrConstructor[0]['email'];
                $this->telefono = $arrConstructor[0]['telefono'];
                $this->id_comision = $arrConstructor[0]['id_comision'];
                $this->id_filial = $arrConstructor[0]['id_filial'];
                $this->id_plan = $arrConstructor[0]['id_plan'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->confirmacion_enviada = $arrConstructor[0]['confirmacion_enviada'];
                $this->id_curso = $arrConstructor[0]['id_curso'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['email'] = $this->email;
        $arrTemp['telefono'] = $this->telefono;
        $arrTemp['id_comision'] = $this->id_comision;
        $arrTemp['id_filial'] = $this->id_filial;
        $arrTemp['id_plan'] = $this->id_plan;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['estado'] = $this->estado == '' ? 'pendiente' : $this->estado;
        $arrTemp['confirmacion_enviada'] = $this->confirmacion_enviada == '' ? '0' : $this->confirmacion_enviada;
        $arrTemp['id_curso'] = $this->id_curso == '' ? null : $this->id_curso;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase reserva_inscripciones o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarReserva_inscripciones(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto reserva_inscripciones
     *
     * @return integer
     */
    public function getCodigoReserva_inscripciones(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de reserva_inscripciones según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de reserva_inscripciones y los valores son los valores a actualizar
     */
    public function setReserva_inscripciones(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["telefono"]))
            $retorno = "telefono";
        else if (!isset($arrCamposValores["id_comision"]))
            $retorno = "id_comision";
        else if (!isset($arrCamposValores["id_filial"]))
            $retorno = "id_filial";
        else if (!isset($arrCamposValores["id_plan"]))
            $retorno = "id_plan";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["confirmacion_enviada"]))
            $retorno = "confirmacion_enviada";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setReserva_inscripciones");
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
    * retorna los campos presentes en la tabla reserva_inscripciones en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposReserva_inscripciones(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "inscripcionesweb.reserva_inscripciones");
    }

    /**
    * Buscar registros en la tabla reserva_inscripciones
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de reserva_inscripciones o la cantdad de registros segun el parametro contar
    */
    static function listarReserva_inscripciones(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "inscripcionesweb.reserva_inscripciones", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>