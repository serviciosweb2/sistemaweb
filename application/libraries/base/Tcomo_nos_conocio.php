<?php

class Tcomo_nos_conocio extends class_general{

    protected $codigo;
    public $descripcion_es;
    public $descripcion_pt;
    public $descripcion_en;
    public $activo;
    public $lft;
    public $rgt;
    protected $primaryKey = "codigo";
    protected $oConnection;
    protected $nombreTabla = 'general.como_nos_conocio';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null){
        $this->oConnection = $conexion;
        if ($codigo != null && $codigo != -1){
            $arrConstructor = $this->_constructor($codigo);
            if (count($arrConstructor) > 0){
                $this->codigo = $arrConstructor[0]['codigo'];
                $this->descripcion_es = $arrConstructor[0]['descripcion_es'];
                $this->descripcion_pt = $arrConstructor[0]['descripcion_pt'];
                $this->descripcion_en = $arrConstructor[0]['descripcion_en'];
                $this->activo = $arrConstructor[0]['activo'];
                $this->lft = $arrConstructor[0]['lft'];
                $this->rgt = $arrConstructor[0]['rgt'];
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
    public function _getArrayDeObjeto($idioma){
        $nombre = 'descripcion_'.$idioma;
        $arrTemp = array();
        $arrTemp['descripcion_es'] = $this->descripcion_es;
        $arrTemp['descripcion_pt'] = $this->descripcion_pt;
        $arrTemp['descripcion_en'] = $this->descripcion_en;
        $arrTemp['nombre'] = $this->$nombre;
        $arrTemp['activo'] = $this->activo == '' ? '1' : $this->activo;
        $arrTemp['lft'] = $this->lft;
        $arrTemp['rgt'] = $this->rgt;
        if($this->codigo != -1) {
            $arrTemp['codigo'] = $this->codigo;
        }
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase como_nos_conocio o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarComo_nos_conocio(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto como_nos_conocio
     *
     * @return integer
     */
    public function getCodigoComo_nos_conocio(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de como_nos_conocio según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de como_nos_conocio y los valores son los valores a actualizar
     */
    public function setComo_nos_conocio(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["descripcion_es"]))
            $retorno = "descripcion_es";
        else if (!isset($arrCamposValores["descripcion_pt"]))
            $retorno = "descripcion_pt";
        else if (!isset($arrCamposValores["descripcion_en"]))
            $retorno = "descripcion_en";
        else if (!isset($arrCamposValores["activo"]))
            $retorno = "activo";
        else if (!isset($arrCamposValores["lft"]))
            $retorno = "lft";
        else if (!isset($arrCamposValores["rgt"]))
            $retorno = "rgt";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setComo_nos_conocio");
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
    * retorna los campos presentes en la tabla como_nos_conocio en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposComo_nos_conocio(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.como_nos_conocio");
    }

    /**
    * Buscar registros en la tabla como_nos_conocio
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de como_nos_conocio o la cantdad de registros segun el parametro contar
    */
    static function listarComo_nos_conocio(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.como_nos_conocio", $condiciones, $limite, $orden, $grupo, $contar);
    }
}