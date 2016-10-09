<?php

/**
* Class Tfiliales
*
*Class  Tfiliales maneja todos los aspectos de filiales
*
* @package  SistemaIGA
* @subpackage Filiales
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Tfiliales extends class_general{

    /**
    * codigo de filiales
    * @var codigo int
    * @access protected
    */
    protected $codigo;

    /**
    * nombre de filiales
    * @var nombre varchar
    * @access public
    */
    public $nombre;

    /**
    * domicilio de filiales
    * @var domicilio varchar
    * @access public
    */
    public $domicilio;

    /**
    * telefono de filiales
    * @var telefono varchar (requerido)
    * @access public
    */
    public $telefono;

    /**
    * codigopostal de filiales
    * @var codigopostal varchar
    * @access public
    */
    public $codigopostal;

    /**
    * email de filiales
    * @var email varchar
    * @access public
    */
    public $email;

    /**
    * emails_franquiciados de filiales
    * @var emails_franquiciados text (requerido)
    * @access public
    */
    public $emails_franquiciados;

    /**
    * pass de filiales
    * @var pass varchar
    * @access public
    */
    public $pass;

    /**
    * ciudad de filiales
    * @var ciudad varchar
    * @access public
    */
    public $ciudad;

    /**
    * provincia de filiales
    * @var provincia varchar
    * @access public
    */
    public $provincia;

    /**
    * pais de filiales
    * @var pais int
    * @access public
    */
    public $pais;

    /**
    * fechainicio de filiales
    * @var fechainicio date
    * @access public
    */
    public $fechainicio;

    /**
    * fechafin de filiales
    * @var fechafin date
    * @access public
    */
    public $fechafin;

    /**
    * royalty de filiales
    * @var royalty double
    * @access public
    */
    public $royalty;

    /**
    * iva de filiales
    * @var iva decimal (requerido)
    * @access public
    */
    public $iva;

    /**
    * iva_fijo de filiales
    * @var iva_fijo decimal (requerido)
    * @access public
    */
    public $iva_fijo;

    /**
    * banco de filiales
    * @var banco int (requerido)
    * @access public
    */
    public $banco;

    /**
    * banco2 de filiales
    * @var banco2 int (requerido)
    * @access public
    */
    public $banco2;

    /**
    * rutavermapa de filiales
    * @var rutavermapa text (requerido)
    * @access public
    */
    public $rutavermapa;

    /**
    * xlatinoamerica de filiales
    * @var xlatinoamerica varchar
    * @access public
    */
    public $xlatinoamerica;

    /**
    * ylatinoamerica de filiales
    * @var ylatinoamerica varchar
    * @access public
    */
    public $ylatinoamerica;

    /**
    * xpais de filiales
    * @var xpais varchar
    * @access public
    */
    public $xpais;

    /**
    * ypais de filiales
    * @var ypais varchar
    * @access public
    */
    public $ypais;

    /**
    * nom_pc_server de filiales
    * @var nom_pc_server varchar (requerido)
    * @access public
    */
    public $nom_pc_server;

    /**
    * codcdinstalacion de filiales
    * @var codcdinstalacion varchar (requerido)
    * @access public
    */
    public $codcdinstalacion;

    /**
    * ultimaconexion de filiales
    * @var ultimaconexion datetime (requerido)
    * @access public
    */
    public $ultimaconexion;

    /**
    * actualizaFranquicia de filiales
    * @var actualizaFranquicia tinyint
    * @access public
    */
    public $actualizaFranquicia;

    /**
    * rutaimg de filiales
    * @var rutaimg char (requerido)
    * @access public
    */
    public $rutaimg;

    /**
    * ultimo_acceso_intranet de filiales
    * @var ultimo_acceso_intranet datetime (requerido)
    * @access public
    */
    public $ultimo_acceso_intranet;

    /**
    * version_bd de filiales
    * @var version_bd varchar (requerido)
    * @access public
    */
    public $version_bd;

    /**
    * condicion de filiales
    * @var condicion int (requerido)
    * @access public
    */
    public $condicion;

    /**
    * codmodulo de filiales
    * @var codmodulo varchar (requerido)
    * @access public
    */
    public $codmodulo;

    /**
    * fecha_datos_server_actualizado de filiales
    * @var fecha_datos_server_actualizado datetime (requerido)
    * @access public
    */
    public $fecha_datos_server_actualizado;

    /**
    * recargo_banco_royalty de filiales
    * @var recargo_banco_royalty decimal (requerido)
    * @access public
    */
    public $recargo_banco_royalty;

    /**
    * recargo_cheque_royalty de filiales
    * @var recargo_cheque_royalty decimal (requerido)
    * @access public
    */
    public $recargo_cheque_royalty;

    /**
    * iva_sistema de filiales
    * @var iva_sistema float (requerido)
    * @access public
    */
    public $iva_sistema;

    /**
    * otroimpuesto de filiales
    * @var otroimpuesto float (requerido)
    * @access public
    */
    public $otroimpuesto;

    /**
    * vma de filiales
    * @var vma decimal (requerido)
    * @access public
    */
    public $vma;

    /**
    * codgrupo de filiales
    * @var codgrupo int (requerido)
    * @access public
    */
    public $codgrupo;

    /**
    * razon_social de filiales
    * @var razon_social varchar (requerido)
    * @access public
    */
    public $razon_social;

    /**
    * visibleweb de filiales
    * @var visibleweb int (requerido)
    * @access public
    */
    public $visibleweb;

    /**
    * transporte de filiales
    * @var transporte int (requerido)
    * @access public
    */
    public $transporte;

    /**
    * cod_masterfranquicias de filiales
    * @var cod_masterfranquicias int (requerido)
    * @access public
    */
    public $cod_masterfranquicias;

    /**
    * zona de filiales
    * @var zona int (requerido)
    * @access public
    */
    public $zona;

    /**
    * lat de filiales
    * @var lat double (requerido)
    * @access public
    */
    public $lat;

    /**
    * lng de filiales
    * @var lng double (requerido)
    * @access public
    */
    public $lng;

    /**
    * puntos de filiales
    * @var puntos tinyint (requerido)
    * @access public
    */
    public $puntos;

    /**
    * puntosvigencia de filiales
    * @var puntosvigencia mediumint (requerido)
    * @access public
    */
    public $puntosvigencia;

    /**
    * baja de filiales
    * @var baja int
    * @access public
    */
    public $baja;

    /**
    * fechaBaja de filiales
    * @var fechaBaja datetime (requerido)
    * @access public
    */
    public $fechaBaja;

    /**
    * id_moneda de filiales
    * @var id_moneda int
    * @access public
    */
    public $id_moneda;

    /**
    * baja_intranet de filiales
    * @var baja_intranet int (requerido)
    * @access public
    */
    public $baja_intranet;

    /**
    * recibir_bd de filiales
    * @var recibir_bd int (requerido)
    * @access public
    */
    public $recibir_bd;

    /**
    * habilitado_modulo de filiales
    * @var habilitado_modulo int (requerido)
    * @access public
    */
    public $habilitado_modulo;

    /**
    * idioma de filiales
    * @var idioma varchar
    * @access public
    */
    public $idioma;

    /**
    * id_tipos_facturacion_royaltys de filiales
    * @var id_tipos_facturacion_royaltys int (requerido)
    * @access public
    */
    public $id_tipos_facturacion_royaltys;

    /**
    * valor_grupo_facturacion de filiales
    * @var valor_grupo_facturacion decimal (requerido)
    * @access public
    */
    public $valor_grupo_facturacion;

    /**
    * recibir_comando de filiales
    * @var recibir_comando int (requerido)
    * @access public
    */
    public $recibir_comando;

    /**
    * dominio de filiales
    * @var dominio varchar (requerido)
    * @access public
    */
    public $dominio;

    /**
    * id_localidad de filiales
    * @var id_localidad int
    * @access public
    */
    public $id_localidad;

    /**
    * barrio de filiales
    * @var barrio varchar (requerido)
    * @access public
    */
    public $barrio;

    /**
    * version_sistema de filiales
    * @var version_sistema int
    * @access public
    */
    public $version_sistema;

    /**
    * perfil_sistema de filiales
    * @var perfil_sistema int
    * @access public
    */
    public $perfil_sistema;

    /**
    * zona_horaria de filiales
    * @var zona_horaria varchar (requerido)
    * @access public
    */
    public $zona_horaria;

    /**
    * minutos_catedra de filiales
    * @var minutos_catedra int
    * @access public
    */
    public $minutos_catedra;

    /**
    * estado de filiales
    * @var estado enum
    * @access public
    */
    public $estado;


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
    protected $nombreTabla = 'general.filiales';

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase filiales
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
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->domicilio = $arrConstructor[0]['domicilio'];
                $this->telefono = $arrConstructor[0]['telefono'];
                $this->codigopostal = $arrConstructor[0]['codigopostal'];
                $this->email = $arrConstructor[0]['email'];
                $this->emails_franquiciados = $arrConstructor[0]['emails_franquiciados'];
                $this->pass = $arrConstructor[0]['pass'];
                $this->ciudad = $arrConstructor[0]['ciudad'];
                $this->provincia = $arrConstructor[0]['provincia'];
                $this->pais = $arrConstructor[0]['pais'];
                $this->fechainicio = $arrConstructor[0]['fechainicio'];
                $this->fechafin = $arrConstructor[0]['fechafin'];
                $this->royalty = $arrConstructor[0]['royalty'];
                $this->iva = $arrConstructor[0]['iva'];
                $this->iva_fijo = $arrConstructor[0]['iva_fijo'];
                $this->banco = $arrConstructor[0]['banco'];
                $this->banco2 = $arrConstructor[0]['banco2'];
                $this->rutavermapa = $arrConstructor[0]['rutavermapa'];
                $this->xlatinoamerica = $arrConstructor[0]['xlatinoamerica'];
                $this->ylatinoamerica = $arrConstructor[0]['ylatinoamerica'];
                $this->xpais = $arrConstructor[0]['xpais'];
                $this->ypais = $arrConstructor[0]['ypais'];
                $this->nom_pc_server = $arrConstructor[0]['nom_pc_server'];
                $this->codcdinstalacion = $arrConstructor[0]['codcdinstalacion'];
                $this->ultimaconexion = $arrConstructor[0]['ultimaconexion'];
                $this->actualizaFranquicia = $arrConstructor[0]['actualizaFranquicia'];
                $this->rutaimg = $arrConstructor[0]['rutaimg'];
                $this->ultimo_acceso_intranet = $arrConstructor[0]['ultimo_acceso_intranet'];
                $this->version_bd = $arrConstructor[0]['version_bd'];
                $this->condicion = $arrConstructor[0]['condicion'];
                $this->codmodulo = $arrConstructor[0]['codmodulo'];
                $this->fecha_datos_server_actualizado = $arrConstructor[0]['fecha_datos_server_actualizado'];
                $this->recargo_banco_royalty = $arrConstructor[0]['recargo_banco_royalty'];
                $this->recargo_cheque_royalty = $arrConstructor[0]['recargo_cheque_royalty'];
                $this->iva_sistema = $arrConstructor[0]['iva_sistema'];
                $this->otroimpuesto = $arrConstructor[0]['otroimpuesto'];
                $this->vma = $arrConstructor[0]['vma'];
                $this->codgrupo = $arrConstructor[0]['codgrupo'];
                $this->razon_social = $arrConstructor[0]['razon_social'];
                $this->visibleweb = $arrConstructor[0]['visibleweb'];
                $this->transporte = $arrConstructor[0]['transporte'];
                $this->cod_masterfranquicias = $arrConstructor[0]['cod_masterfranquicias'];
                $this->zona = $arrConstructor[0]['zona'];
                $this->lat = $arrConstructor[0]['lat'];
                $this->lng = $arrConstructor[0]['lng'];
                $this->puntos = $arrConstructor[0]['puntos'];
                $this->puntosvigencia = $arrConstructor[0]['puntosvigencia'];
                $this->baja = $arrConstructor[0]['baja'];
                $this->fechaBaja = $arrConstructor[0]['fechaBaja'];
                $this->id_moneda = $arrConstructor[0]['id_moneda'];
                $this->baja_intranet = $arrConstructor[0]['baja_intranet'];
                $this->recibir_bd = $arrConstructor[0]['recibir_bd'];
                $this->habilitado_modulo = $arrConstructor[0]['habilitado_modulo'];
                $this->idioma = $arrConstructor[0]['idioma'];
                $this->id_tipos_facturacion_royaltys = $arrConstructor[0]['id_tipos_facturacion_royaltys'];
                $this->valor_grupo_facturacion = $arrConstructor[0]['valor_grupo_facturacion'];
                $this->recibir_comando = $arrConstructor[0]['recibir_comando'];
                $this->dominio = $arrConstructor[0]['dominio'];
                $this->id_localidad = $arrConstructor[0]['id_localidad'];
                $this->barrio = $arrConstructor[0]['barrio'];
                $this->version_sistema = $arrConstructor[0]['version_sistema'];
                $this->perfil_sistema = $arrConstructor[0]['perfil_sistema'];
                $this->zona_horaria = $arrConstructor[0]['zona_horaria'];
                $this->minutos_catedra = $arrConstructor[0]['minutos_catedra'];
                $this->estado = $arrConstructor[0]['estado'];
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
        $arrTemp['nombre'] = $this->nombre;
        $arrTemp['domicilio'] = $this->domicilio;
        $arrTemp['telefono'] = $this->telefono == '' ? null : $this->telefono;
        $arrTemp['codigopostal'] = $this->codigopostal;
        $arrTemp['email'] = $this->email;
        $arrTemp['emails_franquiciados'] = $this->emails_franquiciados == '' ? null : $this->emails_franquiciados;
        $arrTemp['pass'] = $this->pass;
        $arrTemp['ciudad'] = $this->ciudad;
        $arrTemp['provincia'] = $this->provincia;
        $arrTemp['pais'] = $this->pais;
        $arrTemp['fechainicio'] = $this->fechainicio == '' ? '2009-03-16' : $this->fechainicio;
        $arrTemp['fechafin'] = $this->fechafin == '' ? '2009-11-22' : $this->fechafin;
        $arrTemp['royalty'] = $this->royalty == '' ? '0.08' : $this->royalty;
        $arrTemp['iva'] = $this->iva == '' ? null : $this->iva;
        $arrTemp['iva_fijo'] = $this->iva_fijo == '' ? null : $this->iva_fijo;
        $arrTemp['banco'] = $this->banco == '' ? null : $this->banco;
        $arrTemp['banco2'] = $this->banco2 == '' ? null : $this->banco2;
        $arrTemp['rutavermapa'] = $this->rutavermapa == '' ? null : $this->rutavermapa;
        $arrTemp['xlatinoamerica'] = $this->xlatinoamerica;
        $arrTemp['ylatinoamerica'] = $this->ylatinoamerica;
        $arrTemp['xpais'] = $this->xpais;
        $arrTemp['ypais'] = $this->ypais;
        $arrTemp['nom_pc_server'] = $this->nom_pc_server == '' ? null : $this->nom_pc_server;
        $arrTemp['codcdinstalacion'] = $this->codcdinstalacion == '' ? null : $this->codcdinstalacion;
        $arrTemp['ultimaconexion'] = $this->ultimaconexion == '' ? null : $this->ultimaconexion;
        $arrTemp['actualizaFranquicia'] = $this->actualizaFranquicia;
        $arrTemp['rutaimg'] = $this->rutaimg == '' ? null : $this->rutaimg;
        $arrTemp['ultimo_acceso_intranet'] = $this->ultimo_acceso_intranet == '' ? null : $this->ultimo_acceso_intranet;
        $arrTemp['version_bd'] = $this->version_bd == '' ? null : $this->version_bd;
        $arrTemp['condicion'] = $this->condicion == '' ? null : $this->condicion;
        $arrTemp['codmodulo'] = $this->codmodulo == '' ? null : $this->codmodulo;
        $arrTemp['fecha_datos_server_actualizado'] = $this->fecha_datos_server_actualizado == '' ? null : $this->fecha_datos_server_actualizado;
        $arrTemp['recargo_banco_royalty'] = $this->recargo_banco_royalty == '' ? null : $this->recargo_banco_royalty;
        $arrTemp['recargo_cheque_royalty'] = $this->recargo_cheque_royalty == '' ? null : $this->recargo_cheque_royalty;
        $arrTemp['iva_sistema'] = $this->iva_sistema == '' ? null : $this->iva_sistema;
        $arrTemp['otroimpuesto'] = $this->otroimpuesto == '' ? null : $this->otroimpuesto;
        $arrTemp['vma'] = $this->vma == '' ? null : $this->vma;
        $arrTemp['codgrupo'] = $this->codgrupo == '' ? null : $this->codgrupo;
        $arrTemp['razon_social'] = $this->razon_social == '' ? null : $this->razon_social;
        $arrTemp['visibleweb'] = $this->visibleweb == '' ? null : $this->visibleweb;
        $arrTemp['transporte'] = $this->transporte == '' ? null : $this->transporte;
        $arrTemp['cod_masterfranquicias'] = $this->cod_masterfranquicias == '' ? null : $this->cod_masterfranquicias;
        $arrTemp['zona'] = $this->zona == '' ? null : $this->zona;
        $arrTemp['lat'] = $this->lat == '' ? null : $this->lat;
        $arrTemp['lng'] = $this->lng == '' ? null : $this->lng;
        $arrTemp['puntos'] = $this->puntos == '' ? null : $this->puntos;
        $arrTemp['puntosvigencia'] = $this->puntosvigencia == '' ? null : $this->puntosvigencia;
        $arrTemp['baja'] = $this->baja == '' ? '0' : $this->baja;
        $arrTemp['fechaBaja'] = $this->fechaBaja == '' ? null : $this->fechaBaja;
        $arrTemp['id_moneda'] = $this->id_moneda == '' ? '0' : $this->id_moneda;
        $arrTemp['baja_intranet'] = $this->baja_intranet == '' ? null : $this->baja_intranet;
        $arrTemp['recibir_bd'] = $this->recibir_bd == '' ? null : $this->recibir_bd;
        $arrTemp['habilitado_modulo'] = $this->habilitado_modulo == '' ? null : $this->habilitado_modulo;
        $arrTemp['idioma'] = $this->idioma == '' ? 'es' : $this->idioma;
        $arrTemp['id_tipos_facturacion_royaltys'] = $this->id_tipos_facturacion_royaltys == '' ? null : $this->id_tipos_facturacion_royaltys;
        $arrTemp['valor_grupo_facturacion'] = $this->valor_grupo_facturacion == '' ? null : $this->valor_grupo_facturacion;
        $arrTemp['recibir_comando'] = $this->recibir_comando == '' ? null : $this->recibir_comando;
        $arrTemp['dominio'] = $this->dominio == '' ? null : $this->dominio;
        $arrTemp['id_localidad'] = $this->id_localidad;
        $arrTemp['barrio'] = $this->barrio == '' ? null : $this->barrio;
        $arrTemp['version_sistema'] = $this->version_sistema == '' ? '1' : $this->version_sistema;
        $arrTemp['perfil_sistema'] = $this->perfil_sistema;
        $arrTemp['zona_horaria'] = $this->zona_horaria == '' ? null : $this->zona_horaria;
        $arrTemp['minutos_catedra'] = $this->minutos_catedra;
        $arrTemp['estado'] = $this->estado == '' ? 'activa' : $this->estado;
        return $arrTemp;
    }

    /* PUBLIC FUNCTIONS */
    /**
     * Guarda un objeto nuevo de la clase filiales o actualiza uno ya existente en la base de datos
     *
     * @return boolean
     */
    public function guardarFiliales(){
        return $this->_guardar();
    }

    /**
     * Retorna el codigo del objeto filiales
     *
     * @return integer
     */
    public function getCodigoFiliales(){
        return $this->_getCodigo();
    }

    /**
     * actualiza los campos de filiales según los datos enviados en el array de parametro
     * 
     * @param array $arrCamposValores   un array cuyos indices son las propiedades de filiales y los valores son los valores a actualizar
     */
    public function setFiliales(array $arrCamposValores){
        $retorno = "";
        if (!isset($arrCamposValores["nombre"]))
            $retorno = "nombre";
        else if (!isset($arrCamposValores["domicilio"]))
            $retorno = "domicilio";
        else if (!isset($arrCamposValores["codigopostal"]))
            $retorno = "codigopostal";
        else if (!isset($arrCamposValores["email"]))
            $retorno = "email";
        else if (!isset($arrCamposValores["pass"]))
            $retorno = "pass";
        else if (!isset($arrCamposValores["ciudad"]))
            $retorno = "ciudad";
        else if (!isset($arrCamposValores["provincia"]))
            $retorno = "provincia";
        else if (!isset($arrCamposValores["pais"]))
            $retorno = "pais";
        else if (!isset($arrCamposValores["fechainicio"]))
            $retorno = "fechainicio";
        else if (!isset($arrCamposValores["fechafin"]))
            $retorno = "fechafin";
        else if (!isset($arrCamposValores["royalty"]))
            $retorno = "royalty";
        else if (!isset($arrCamposValores["xlatinoamerica"]))
            $retorno = "xlatinoamerica";
        else if (!isset($arrCamposValores["ylatinoamerica"]))
            $retorno = "ylatinoamerica";
        else if (!isset($arrCamposValores["xpais"]))
            $retorno = "xpais";
        else if (!isset($arrCamposValores["ypais"]))
            $retorno = "ypais";
        else if (!isset($arrCamposValores["actualizaFranquicia"]))
            $retorno = "actualizaFranquicia";
        else if (!isset($arrCamposValores["baja"]))
            $retorno = "baja";
        else if (!isset($arrCamposValores["id_moneda"]))
            $retorno = "id_moneda";
        else if (!isset($arrCamposValores["idioma"]))
            $retorno = "idioma";
        else if (!isset($arrCamposValores["id_localidad"]))
            $retorno = "id_localidad";
        else if (!isset($arrCamposValores["version_sistema"]))
            $retorno = "version_sistema";
        else if (!isset($arrCamposValores["perfil_sistema"]))
            $retorno = "perfil_sistema";
        else if (!isset($arrCamposValores["minutos_catedra"]))
            $retorno = "minutos_catedra";
        else if (!isset($arrCamposValores["estado"]))
            $retorno = "estado";
        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setFiliales");
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
    * retorna los campos presentes en la tabla filiales en formato array
    * 
    * @param CI_DB_mysqli_driver $connection   La conexion actual
    * @return array
    */
    static function camposFiliales(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.filiales");
    }

    /**
    * Buscar registros en la tabla filiales
    *
    * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
    * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
    * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
    * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
    * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
    * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
    * @return mixed    Retorna la lista de filiales o la cantdad de registros segun el parametro contar
    */
    static function listarFiliales(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.filiales", $condiciones, $limite, $orden, $grupo, $contar);
    }
}
?>