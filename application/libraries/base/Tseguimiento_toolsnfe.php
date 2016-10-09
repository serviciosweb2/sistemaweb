<?php

/**
* Class Tseguimiento_toolsnfe
*
*Class  Tseguimiento_toolsnfe maneja todos los aspectos de seguimiento_toolsnfe
*
* @package  SistemaIGA
* @subpackage Seguimiento_toolsnfe
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tseguimiento_toolsnfe extends class_general{

    /**
    * id de seguimiento_toolsnfe
    * @var id int
    * @access protected
    */
    protected $id;

    /**
    * cod_filial de seguimiento_toolsnfe
    * @var cod_filial int
    * @access public
    */
    public $cod_filial;

    /**
    * cod_factura de seguimiento_toolsnfe
    * @var cod_factura int
    * @access public
    */
    public $cod_factura;

    /**
    * nfe de seguimiento_toolsnfe
    * @var nfe varchar
    * @access public
    */
    public $nfe;

    /**
    * bStat de seguimiento_toolsnfe
    * @var bStat varchar (requerido)
    * @access public
    */
    public $bStat;

    /**
    * cStat de seguimiento_toolsnfe
    * @var cStat int (requerido)
    * @access public
    */
    public $cStat;

    /**
    * xMotivo de seguimiento_toolsnfe
    * @var xMotivo varchar (requerido)
    * @access public
    */
    public $xMotivo;

    /**
    * dhRecbto de seguimiento_toolsnfe
    * @var dhRecbto datetime
    * @access public
    */
    public $dhRecbto;

    /**
    * nRec de seguimiento_toolsnfe
    * @var nRec varchar (requerido)
    * @access public
    */
    public $nRec;

    /**
    * tMed de seguimiento_toolsnfe
    * @var tMed varchar (requerido)
    * @access public
    */
    public $tMed;

    /**
    * tpAmb de seguimiento_toolsnfe
    * @var tpAmb smallint
    * @access public
    */
    public $tpAmb;

    /**
    * verAplic de seguimiento_toolsnfe
    * @var verAplic varchar
    * @access public
    */
    public $verAplic;

    /**
    * cUF de seguimiento_toolsnfe
    * @var cUF int (requerido)
    * @access public
    */
    public $cUF;

    /**
    * nProt de seguimiento_toolsnfe
    * @var nProt varchar (requerido)
    * @access public
    */
    public $nProt;

    /**
    * estado de seguimiento_toolsnfe
    * @var estado enum (requerido)
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
    protected $nombreTabla = 'general.seguimiento_toolsnfe';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase seguimiento_toolsnfe
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
                $this->cod_filial = $arrConstructor[0]['cod_filial'];
                $this->cod_factura = $arrConstructor[0]['cod_factura'];
                $this->nfe = $arrConstructor[0]['nfe'];
                $this->bStat = $arrConstructor[0]['bStat'];
                $this->cStat = $arrConstructor[0]['cStat'];
                $this->xMotivo = $arrConstructor[0]['xMotivo'];
                $this->dhRecbto = $arrConstructor[0]['dhRecbto'];
                $this->nRec = $arrConstructor[0]['nRec'];
                $this->tMed = $arrConstructor[0]['tMed'];
                $this->tpAmb = $arrConstructor[0]['tpAmb'];
                $this->verAplic = $arrConstructor[0]['verAplic'];
                $this->cUF = $arrConstructor[0]['cUF'];
                $this->nProt = $arrConstructor[0]['nProt'];
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
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['cod_factura'] = $this->cod_factura;
        $arrTemp['nfe'] = $this->nfe;
        $arrTemp['bStat'] = $this->bStat == '' ? null : $this->bStat;
        $arrTemp['cStat'] = $this->cStat == '' ? null : $this->cStat;
        $arrTemp['xMotivo'] = $this->xMotivo == '' ? null : $this->xMotivo;
        $arrTemp['dhRecbto'] = $this->dhRecbto;
        $arrTemp['nRec'] = $this->nRec == '' ? null : $this->nRec;
        $arrTemp['tMed'] = $this->tMed == '' ? null : $this->tMed;
        $arrTemp['tpAmb'] = $this->tpAmb;
        $arrTemp['verAplic'] = $this->verAplic;
        $arrTemp['cUF'] = $this->cUF == '' ? null : $this->cUF;
        $arrTemp['nProt'] = $this->nProt == '' ? null : $this->nProt;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase seguimiento_toolsnfe o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarSeguimiento_toolsnfe(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto seguimiento_toolsnfe
     *
     * @return integer
     */
    public function getCodigoSeguimiento_toolsnfe(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de seguimiento_toolsnfe según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de seguimiento_toolsnfe y los valores son los valores a actualizar
     */
    public function setSeguimiento_toolsnfe(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["cod_filial"]))
            $retorno = "cod_filial";
        else if (!isset($arrCamposValores["cod_factura"]))
            $retorno = "cod_factura";
        else if (!isset($arrCamposValores["nfe"]))
            $retorno = "nfe";
        else if (!isset($arrCamposValores["dhRecbto"]))
            $retorno = "dhRecbto";
        else if (!isset($arrCamposValores["tpAmb"]))
            $retorno = "tpAmb";
        else if (!isset($arrCamposValores["verAplic"]))
            $retorno = "verAplic";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setSeguimiento_toolsnfe");
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
    * retorna los campos presentes en la tabla seguimiento_toolsnfe en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposSeguimiento_toolsnfe(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.seguimiento_toolsnfe");
    }

    /**
    * Buscar registros en la tabla seguimiento_toolsnfe
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de seguimiento_toolsnfe o la cantdad de registros segun el parametro contar
    */
    static function listarSeguimiento_toolsnfe(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.seguimiento_toolsnfe", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>