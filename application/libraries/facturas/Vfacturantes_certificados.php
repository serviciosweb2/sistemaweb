<?php

/**
 * Class Vfacturantes_certificados
 *
 * Class  Vfacturantes_certificados maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vfacturantes_certificados {

    private $cod_facturante;
    public $password;
    public $fecha_expiracion;
    public $pub_key;
    public $pry_key;
    public $cert;
    private $exists = false;
    private $oConnection;
    static private $pathToCertificado = "igacloud/certificados/";
    static private $extensionPriKey = "_priKEY.pem";
    static private $extensionPubKey = "_pubKEY.pem";
    static private $extensionCertKey = "_certKEY.pem";
    static private $tableName = "facturantes_certificados";

    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $conexion, $codFacturante) {
        $this->oConnection = $conexion;
        $conexion->select("*");
        $conexion->from("general." . self::$tableName);
        $conexion->where("cod_facturante", $codFacturante);
        $query = $conexion->get();
        $arrCertificado = $query->result_array();
        if (count($arrCertificado) > 0) {
            $this->cod_facturante = $arrCertificado[0]['cod_facturante'];
            $this->password = $arrCertificado[0]['password'];
            $this->fecha_expiracion = $arrCertificado[0]['fecha_expiracion'];
            $this->pry_key = $arrCertificado[0]['pry_key'];
            $this->pub_key = $arrCertificado[0]['pub_key'];
            $this->cert = $arrCertificado[0]['cert'];
            $this->exists = true;
        } else {
            $this->cod_facturante = $codFacturante;
            $this->exists = false;
        }
    }

    /* PRIVATE FUNCTIONS */

    private function _getArrayDeObjeto() {
        $arrResp = array();
        $arrResp['cod_facturante'] = $this->cod_facturante;
        $arrResp['password'] = $this->password;
        $arrResp['fecha_expiracion'] = $this->fecha_expiracion == '' ? null : $this->fecha_expiracion;
        $arrResp['pry_key'] = $this->pry_key;
        $arrResp['pub_key'] = $this->pub_key;
        $arrResp['cert'] = $this->cert;
        return $arrResp;
    }

    private function _insertar() {
        if ($this->oConnection->insert(self::$tableName, $this->_getArrayDeObjeto())) {
            $this->exists = true;
            return true;
        } else {
            return false;
        }
    }

    private function _actualizar() {
        return $this->oConnection->update(self::$tableName, $this->_getArrayDeObjeto(), "cod_facturante = $this->cod_facturante");
    }

    /* PUBLIC FUNCTIONS */

    public function getCodigoFacturante() {
        return $this->cod_facturante;
    }

    public function guardar() {
        if ($this->exists) {
            return $this->_actualizar();
        } else {
            return $this->_insertar();
        }
    }

    static private function getBasePATH() {
        return $_SERVER['HTTP_HOST'] == "localhost" ? "C:/AppServ/www/sistemasiga/" : "/var/www";
    }

    public function getPathCertificadoPri($incluirAbsoluto = false) {
        if ($incluirAbsoluto)
            return self::getBasePATH() . self::$pathToCertificado . $this->certificado . self::$extensionPriKey;
        else
            return self::$pathToCertificado . $this->certificado . self::$extensionPriKey;
    }

    public function getPathCertificadoPub($incluirAbsoluto = false) {
        if ($incluirAbsoluto)
            return self::getBasePATH() . self::$pathToCertificado . $this->certificado . self::$extensionPubKey;
        else
            return self::$pathToCertificado . $this->certificado . self::$extensionPubKey;
    }

    public function getPathCertificadoCert($incluirAbsoluto = false) {
        if ($incluirAbsoluto)
            return self::getBasePATH() . self::$pathToCertificado . $this->certificado . self::$extensionCertKey;
        else
            return self::$pathToCertificado . $this->certificado . self::$extensionCertKey;
    }

    public function getExists() {
        return $this->exists;
    }

    public function getInfoCert() {
        $resultado = false;

        if (!is_null($this->cert)) {
            $resultado = openssl_x509_parse($this->cert);
        }

        return $resultado;
    }

    /* STATIC FUNCTIONS */

    static public function getPathToFile() {
        return self::$pathToCertificado;
    }

    static public function getPriKeyExtension() {
        return self::$extensionPriKey;
    }

    static public function getPubKeyExtension() {
        return self::$extensionPubKey;
    }

    static public function getCertKeyExtension() {
        return self::$extensionCertKey;
    }

    public function getActivo() {
        if ($this->exists && $this->fecha_expiracion > date('Y-m-d') && $this->cert != null) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
