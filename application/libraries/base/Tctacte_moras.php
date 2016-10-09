<?php

/**
* Class Tctacte_moras
*
*Class  Tctacte_moras maneja todos los aspectos de ctacte_moras
*
* @package  SistemaIGA
* @subpackage Ctacte_moras
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tctacte_moras{

    /**
    * cod_ctacte de ctacte_moras
    * @var cod_ctacte int
    * @access public
    */
    public $cod_ctacte;

    /**
    * fecha de ctacte_moras
    * @var fecha date
    * @access public
    */
    public $fecha;

    /**
    * precio de ctacte_moras
    * @var precio double
    * @access public
    */
    public $precio;

    /**
    * mora de ctacte_moras
    * @var mora double
    * @access public
    */
    public $cod_mora;

    
    /**
     * mora de fecha_creacion
    * @var fecha_creacion datetime
    * @access public
     */
    public $fecha_creacion;

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
    protected $nombreTabla = 'ctacte_moras';

    
    protected $exists = false;
    
    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase ctacte_moras
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $codCtacte, $fecha, $codMora){
        $this->oConnection = $conexion;
        $this->cod_ctacte = $codCtacte;
        $this->fecha = $fecha;
        $this->cod_mora = $codMora;
        $arrConstructor = $this->_constructor();
        if (count($arrConstructor) > 0){
            $this->cod_ctacte = $arrConstructor[0]['cod_ctacte'];
            $this->fecha = $arrConstructor[0]['fecha'];
            $this->precio = $arrConstructor[0]['precio'];
            $this->cod_mora = $arrConstructor[0]['cod_mora'];
            $this->fecha_creacion = $arrConstructor[0]['fecha_creacion'];
            $this->exists = true;
        } else {
            $this->exists = false;
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
        $arrTemp['cod_ctacte'] = $this->cod_ctacte;
        $arrTemp['fecha'] = $this->fecha;
        $arrTemp['precio'] = $this->precio == '' ? '0.0000' : $this->precio;
        $arrTemp['cod_mora'] = $this->cod_mora;
        $arrTemp['fecha_creacion'] = $this->fecha_creacion;
        return $arrTemp;
    }

    /**
     * contructor de la clase (no exriende de general por tener tres claves)
     * 
     * @return array
     */
    protected function _constructor(){
        $this->oConnection->select("*");
        $this->oConnection->from("ctacte_moras");
        $this->oConnection->where("cod_ctacte", $this->cod_ctacte);
        $this->oConnection->where("fecha", $this->fecha);
        $this->oConnection->where("cod_mora", $this->cod_mora);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    
    protected function _insertar(){
        $this->exists = $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
        return $this->exists;
    }

    protected function _actualizar(){
        //anteriores
        $this->oConnection->where('cod_ctacte =', $this->cod_ctacte);
        $this->oConnection->where('fecha_creacion !=', date('Y-m-d'));
        $this->oConnection->delete($this->nombreTabla);
        $arrCondiciones = array(
            "cod_ctacte" => $this->cod_ctacte,
            "fecha" => $this->fecha,
            "cod_mora" => $this->cod_mora//,
            //"fecha_creacion"=>  $this->fecha_creacion
        );
        return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto(), $arrCondiciones);
    }

    /* PUBLIC FUNCTIONS */
    
    
    public function guardar(){
        if ($this->exists){
            return $this->_actualizar();
        } else {
            return $this->_insertar();
        }
    }
    
    
    /**
     * actualiza los campos de ctacte_moras según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de ctacte_moras y los valores son los valores a actualizar
     */
    public function setCtacte_moras(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_ctacte"]))
            $retorno = "cod_ctacte";
        else if (!isset($arrCamposValores["fecha"]))
            $retorno = "fecha";
        else if (!isset($arrCamposValores["precio"]))
            $retorno = "precio";
        else if (!isset($arrCamposValores["mora"]))
            $retorno = "cod_mora";
         else if (!isset($arrCamposValores["fecha_creacion"]))
            $retorno = "fecha_creacion";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCtacte_moras");
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


}
?>