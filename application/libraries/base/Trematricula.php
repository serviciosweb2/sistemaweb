<?php

/**
* Class Trematricula
*
*Class  Trematricula maneja aspectos de la rematrícula en Brasil
*
* @package  SistemaIGA
* @subpackage Rematricula
* @author
* @version  $Revision: 1.1 $
* @access   private
*/
class Trematricula extends class_general{

    /**
     * id de la tabla
     * @var id int (incremental)
     * @access protected
     */
    protected $id;

    /**
     * codigo de la matricula de alumnos
     * @var cod_matricula int
     * @access public
     */
    public $cod_matricula;

    /**
     * firmo la carta compromiso
     * @var firmo var
     * @access public
     */
    public $firmo;

    /**
     * trimestre
     * @var trimestre var
     * @access public
     */
    public $trimestre;

    /**
     * fecha de entrega de firma
     * @var fecha date (requerido)
     * @access public
     */
    public $fecha;

    /**
     * codigo de usuario receptor
     * @var codigo_usuario varchar (requerido)
     * @access public
     */
    public $codigo_usuario;

    /**
     * Año
     * @var ano varchar (requerido)
     * @access public
     */
    public $ano;

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
    protected $nombreTabla = 'control_rematricula';


    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase rematricula
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    public function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->cod_matricula = $arrConstructor[0]['cod_matricula'];
                $this->firmo = $arrConstructor[0]['firmo'];
                $this->trimestre = $arrConstructor[0]['trimestre'];
                $this->fecha = $arrConstructor[0]['fecha'];
                $this->codigo_usuario = $arrConstructor[0]['codigo_usuario'];
                $this->ano = $arrConstructor[0]['ano'];
            }
            else {
                $this->codigo = -1;
            }
        }
        else {
            $this->codigo = -1;
        }
    }


    /**
     * Retorna el codigo del objeto rematricula
     *
     * @return integer
     */
    public function getCodigoFirmaRematricula(){
        return $this->_getCodigo();
    }


    /* PORTECTED FUNCTIONS */

    /**
    * Devuelve el objeto con todas sus propiedades y valores en formato array
    *
    * @return array
    */
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['cod_matricula'] = $this->cod_matricula;
        $arrTemp['firmo'] = $this->firmo == '' ? null : $this->firmo;
        $arrTemp['trimestre'] = $this->trimestre == '' ? null : $this->trimestre;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['codigo_usuario'] = $this->codigo_usuario;
        $arrTemp['ano'] = $this->ano == '' ? null : $this->ano;
        return $arrTemp;
    }


    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase rematricula o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFirma(){
        return $this->_guardar();
    }


     public function setFirmaRematricula(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["filial_codigo"]))
            $retorno = "filial_codigo";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFirmaRematricula");
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

    static function camposFirmaRematricula(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "control_rematricula");
    }

    static function listarFirmaRematricula(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "control_rematricula", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>