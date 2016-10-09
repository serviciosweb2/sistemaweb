<?php

/**
* Class Tcursos
*
*Class  Tcursos maneja todos los aspectos de cursos
*
* @package  SistemaIGA
* @subpackage Cursos
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tcursos extends class_general{

    /**
    * codigo de cursos
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre_es de cursos
    * @var nombre_es varchar
    * @access public
    */
    public $nombre_es;

    /**
    * nombre_pt de cursos
    * @var nombre_pt varchar
    * @access public
    */
    public $nombre_pt;

    /**
    * nombre_in de cursos
    * @var nombre_in varchar
    * @access public
    */
    public $nombre_in;

    /**
    * descripcion_es de cursos
    * @var descripcion_es text
    * @access public
    */
    public $descripcion_es;

    /**
    * descripcion_pt de cursos
    * @var descripcion_pt text
    * @access public
    */
    public $descripcion_pt;

    /**
    * descripcion_in de cursos
    * @var descripcion_in text
    * @access public
    */
    public $descripcion_in;

    /**
    * descripcion_corta_es de cursos
    * @var descripcion_corta_es text (requerido)
    * @access public
    */
    public $descripcion_corta_es;

    /**
    * descripcion_corta_pt de cursos
    * @var descripcion_corta_pt text (requerido)
    * @access public
    */
    public $descripcion_corta_pt;

    /**
    * descripcion_corta_in de cursos
    * @var descripcion_corta_in text (requerido)
    * @access public
    */
    public $descripcion_corta_in;

    /**
    * descripcion_venta_es de cursos
    * @var descripcion_venta_es text (requerido)
    * @access public
    */
    public $descripcion_venta_es;

    /**
    * descripcion_venta_pt de cursos
    * @var descripcion_venta_pt text (requerido)
    * @access public
    */
    public $descripcion_venta_pt;

    /**
    * descripcion_venta_in de cursos
    * @var descripcion_venta_in text (requerido)
    * @access public
    */
    public $descripcion_venta_in;

    /**
    * cod_subcategoria de cursos
    * @var cod_subcategoria int (requerido)
    * @access public
    */
    public $cod_subcategoria;

    /**
    * cod_categoria de cursos
    * @var cod_categoria int
    * @access public
    */
    public $cod_categoria;

    /**
    * tags de cursos
    * @var tags varchar (requerido)
    * @access public
    */
    public $tags;

    /**
    * listar_en_asuntos_mails_consultas de cursos
    * @var listar_en_asuntos_mails_consultas smallint
    * @access public
    */
    public $listar_en_asuntos_mails_consultas;

    /**
    * estado de cursos
    * @var estado enum
    * @access public
    */
    public $estado;

    /**
    * tipo_curso de cursos
    * @var tipo_curso enum
    * @access public
    */
    public $tipo_curso;

    /**
    * cant_horas de cursos
    * @var cant_horas varchar (requerido)
    * @access public
    */
    public $cant_horas;

    /**
    * cantidad_meses de cursos
    * @var cantidad_meses varchar (requerido)
    * @access public
    */
    public $cantidad_meses;

    /**
     * updated_at fecha que actualización del curso
     * @var updated_at timestamp (requerido)
     * @access public
     */
    public $updated_at;


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
    protected $nombreTabla = 'general.cursos';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase cursos
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
                $this->nombre_es = $arrConstructor[0]['nombre_es'];
                $this->nombre_pt = $arrConstructor[0]['nombre_pt'];
                $this->nombre_en = $arrConstructor[0]['nombre_in'];
                $this->descripcion_es = $arrConstructor[0]['descripcion_es'];
                $this->descripcion_pt = $arrConstructor[0]['descripcion_pt'];
                $this->descripcion_en = $arrConstructor[0]['descripcion_in'];
                $this->descripcion_corta_es = $arrConstructor[0]['descripcion_corta_es'];
                $this->descripcion_corta_pt = $arrConstructor[0]['descripcion_corta_pt'];
                $this->descripcion_corta_en = $arrConstructor[0]['descripcion_corta_in'];
                $this->descripcion_venta_es = $arrConstructor[0]['descripcion_venta_es'];
                $this->descripcion_venta_pt = $arrConstructor[0]['descripcion_venta_pt'];
                $this->descripcion_venta_en = $arrConstructor[0]['descripcion_venta_in'];
                $this->cod_subcategoria = $arrConstructor[0]['cod_subcategoria'];
                $this->cod_categoria = $arrConstructor[0]['cod_categoria'];
                $this->tags = $arrConstructor[0]['tags'];
                $this->listar_en_asuntos_mails_consultas = $arrConstructor[0]['listar_en_asuntos_mails_consultas'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->tipo_curso = $arrConstructor[0]['tipo_curso'];
                $this->cant_horas = $arrConstructor[0]['cant_horas'];
                $this->cantidad_meses = $arrConstructor[0]['cantidad_meses'];
                $this->updated_at = $arrConstructor[0]['updated_at'];
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
    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['nombre_es'] = $this->nombre_es;
        $arrTemp['nombre_pt'] = $this->nombre_pt;
        $arrTemp['nombre_in'] = $this->nombre_in;
        $arrTemp['descripcion_es'] = $this->descripcion_es;
        $arrTemp['descripcion_pt'] = $this->descripcion_pt;
        $arrTemp['descripcion_in'] = $this->descripcion_in;
        $arrTemp['descripcion_corta_es'] = $this->descripcion_corta_es == '' ? null : $this->descripcion_corta_es;
        $arrTemp['descripcion_corta_pt'] = $this->descripcion_corta_pt == '' ? null : $this->descripcion_corta_pt;
        $arrTemp['descripcion_corta_in'] = $this->descripcion_corta_in == '' ? null : $this->descripcion_corta_in;
        $arrTemp['descripcion_venta_es'] = $this->descripcion_venta_es == '' ? null : $this->descripcion_venta_es;
        $arrTemp['descripcion_venta_pt'] = $this->descripcion_venta_pt == '' ? null : $this->descripcion_venta_pt;
        $arrTemp['descripcion_venta_in'] = $this->descripcion_venta_in == '' ? null : $this->descripcion_venta_in;
        $arrTemp['cod_subcategoria'] = $this->cod_subcategoria == '' ? null : $this->cod_subcategoria;
        $arrTemp['cod_categoria'] = $this->cod_categoria;
        $arrTemp['tags'] = $this->tags == '' ? null : $this->tags;
        $arrTemp['listar_en_asuntos_mails_consultas'] = $this->listar_en_asuntos_mails_consultas == '' ? '1' : $this->listar_en_asuntos_mails_consultas;
        $arrTemp['estado'] = $this->estado == '' ? 'habilitado' : $this->estado;
        $arrTemp['tipo_curso'] = $this->tipo_curso == '' ? 'curso' : $this->tipo_curso;
        $arrTemp['cant_horas'] = $this->cant_horas == '' ? null : $this->cant_horas;
        $arrTemp['cantidad_meses'] = $this->cantidad_meses == '' ? null : $this->cantidad_meses;
        $arrTemp['updated_at'] = $this->updated_at == '' ? null : $this->updated_at;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase cursos o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarCursos(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto cursos
     *
     * @return integer
     */
    public function getCodigoCursos(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de cursos según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de cursos y los valores son los valores a actualizar
     */
    public function setCursos(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre_es"]))
            $retorno = "nombre_es";
        else if (!isset($arrCamposValores["nombre_pt"]))
            $retorno = "nombre_pt";
        else if (!isset($arrCamposValores["nombre_in"]))
            $retorno = "nombre_in";
        else if (!isset($arrCamposValores["descripcion_es"]))
            $retorno = "descripcion_es";
        else if (!isset($arrCamposValores["descripcion_pt"]))
            $retorno = "descripcion_pt";
        else if (!isset($arrCamposValores["descripcion_in"]))
            $retorno = "descripcion_in";
        else if (!isset($arrCamposValores["cod_categoria"]))
            $retorno = "cod_categoria";
        else if (!isset($arrCamposValores["listar_en_asuntos_mails_consultas"]))
            $retorno = "listar_en_asuntos_mails_consultas";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        else if (!isset($arrCamposValores["tipo_curso"]))
            $retorno = "tipo_curso";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setCursos");
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
    * retorna los campos presentes en la tabla cursos en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposCursos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.cursos");
    }

    /**
    * Buscar registros en la tabla cursos
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de cursos o la cantdad de registros segun el parametro contar
    */
    static function listarCursos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.cursos", $condiciones, $limite, $orden, $grupo, $contar);
    }
}