<?php

/**
* Class Tmateriales_didacticos
*
*Class  Tmateriales_didacticos maneja todos los aspectos de materiales_didacticos
*
* @package  SistemaIGA
* @subpackage Materiales_didacticos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tmateriales_didacticos extends class_general{

    /**
    * id de materiales_didacticos
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * tipo de materiales_didacticos
    * @var tipo enum
    * @access public
    */
    public $tipo;

    /**
    * id_clase de materiales_didacticos
    * @var id_clase int
    * @access public
    */
    public $id_clase;

    /**
    * id_material de materiales_didacticos
    * @var id_material int
    * @access public
    */
    public $id_material;

    /**
    * estado de materiales_didacticos
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'material_didactico.materiales_didacticos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase materiales_didacticos
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
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->id_clase = $arrConstructor[0]['id_clase'];
                $this->id_material = $arrConstructor[0]['id_material'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['tipo'] = $this->tipo;
        $arrTemp['id_clase'] = $this->id_clase;
        $arrTemp['id_material'] = $this->id_material;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitada' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase materiales_didacticos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarMateriales_didacticos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto materiales_didacticos
     *
     * @return integer
     */
    public function getCodigoMateriales_didacticos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de materiales_didacticos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de materiales_didacticos y los valores son los valores a actualizar
     */
    public function setMateriales_didacticos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["tipo"]))
            $retorno = "tipo";
        else if (!isset($arrCamposValores["id_clase"]))
            $retorno = "id_clase";
        else if (!isset($arrCamposValores["id_material"]))
            $retorno = "id_material";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setMateriales_didacticos");
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
    * retorna los campos presentes en la tabla materiales_didacticos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposMateriales_didacticos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "material_didactico.materiales_didacticos");
    }

    /**
    * Buscar registros en la tabla materiales_didacticos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de materiales_didacticos o la cantdad de registros segun el parametro contar
    */
    static function listarMateriales_didacticos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "material_didactico.materiales_didacticos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>