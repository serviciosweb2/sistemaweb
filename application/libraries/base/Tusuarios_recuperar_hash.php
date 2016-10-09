<?php

/**
* Class Tusuarios_recuperar_hash
*
*Class  Tusuarios_recuperar_hash maneja todos los aspectos de usuarios_recuperar_hash
*
* @package  SistemaIGA
* @subpackage Usuarios_recuperar_hash
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tusuarios_recuperar_hash{

    /**
    * hash de usuarios_recuperar_hash
    * @var hash varchar
    * @access protected
    */
    protected $hash;

    /**
    * id_usuario de usuarios_recuperar_hash
    * @var id_usuario int
    * @access public
    */
    public $id_usuario;

    /**
    * estado de usuarios_recuperar_hash
    * @var estado enum
    * @access public
    */
    public $estado;


    /**
    * primaryKey de la tabla
    * @var primaryKey var
    * @access protected
    */
    protected $primaryKey = "hash";
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
    protected $nombreTabla = 'general.usuarios_recuperar_hash';

    protected $exists = false;
    
    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase usuarios_recuperar_hash
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $hash){
        $this->hash = $hash;
        $this->oConnection = $conexion;        
        $arrConstructor = $this->_constructor($hash);
        if (count($arrConstructor) > 0){
            $this->id_usuario = $arrConstructor[0]['id_usuario'];
            $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['hash'] = $this->hash;
        $arrTemp['id_usuario'] = $this->id_usuario;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        return $arrTemp;
    }


    private function _insert(){
        return $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
    }
    
    private function _update(){
        $this->oConnection->where("hash", $this->hash);
        return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto());
    }

    public function guardar(){
        if (!$this->exists){
            return $this->_insert();
        } else {
            return $this->_update();
        }
    }


    /* STATIC FUNCTIONS */

    /**
    * retorna los campos presentes en la tabla usuarios_recuperar_hash en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposUsuarios_recuperar_hash(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.usuarios_recuperar_hash");
    }

    /**
    * Buscar registros en la tabla usuarios_recuperar_hash
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de usuarios_recuperar_hash o la cantdad de registros segun el parametro contar
    */
    static function listarUsuarios_recuperar_hash(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.usuarios_recuperar_hash", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>