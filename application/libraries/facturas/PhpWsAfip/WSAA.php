<?php
include_once('BasicSoapClient.php');


/**
 * WSAA (WebService de Autenticación y Autorización)
 *
 * Genera clave privadas y certificados CSR para poder registrarse ante los WebServices AFIP.
 * Genera TRA (Ticket de Requerimiento de Acceso) e interactua con el WSAA. Si la solicitud
 * fue aceptada devuelve el TA (Ticket de Acceso).
 *
 *
 * @author Juan Pablo Candioti
 */
class WSAA extends BasicSoapClient
{
    /**
     * $ws_name
     *
     * @var string      Nombre del WebService al que se desea acceder.
     */
    private $ws_name;

    /**
     * $testing
     *
     * @var boolean     ¿Es servidor de homologación?.
     */
    private $testing;

    /**
     * $ta_expiration
     *
     * @var integer     Segundos de duración de los TA solicitados en los TRA.
     */
    private $ta_expiration;

    /**
     * $sec_tolerance
     *
     * @var integer     Segundos de tolerancia en el tiempo de generación de los TRA.
     */
    private $sec_tolerance;

    /**
     * $str_crt
     *
     * @var string      Texto del certificado X.509 firmado por la AFIP.
     */
    private $str_crt;

    /**
     * $str_pkey
     *
     * @var string      Texto de la clave privada.
     */
    private $str_pkey;

    /**
     * $passphrase
     *
     * @var string      Frase secreta.
     */
    private $passphrase;

    /**
     * $tra_tpl_file
     *
     * @var string      Plantilla dónde se expresa la ubicación de los archivos temporarios.
     */
    private $tra_tpl_file;

    /**
     * $tra_id
     *
     * @var string      Hash identificador de la solicitud de un TA.
     */
    private $tra_id;

    /**
     * $ta
     *
     * @var \SimpleXMLElement   TA activo.
     */
    private $ta;


    function __construct(array $config=array())
    {
        $this->tra_id               = null;
        $this->ta                   = null;
        
        $this->ws_name              = isset($config['ws_name'])         ? $config['ws_name']                : 'wsfe';
        $this->testing              = isset($config['testing'])         ? $config['testing']                : true;
        $this->str_crt              = isset($config['str_crt'])         ? $config['str_crt']                : file_get_contents('certs/default.crt');
        $this->str_pkey             = isset($config['str_pkey'])        ? $config['str_pkey']               : file_get_contents('certs/default.key');
        $this->passphrase           = isset($config['passphrase'])      ? $config['passphrase']             : '';
        $this->tra_tpl_file         = isset($config['tra_tpl_file'])    ? $config['tra_tpl_file']           : 'tmp/tra_%s.xml';
        $this->sec_tolerance        = isset($config['sec_tolerance'])   ? (int) $config['sec_tolerance']    : 5;
        $this->ta_expiration        = isset($config['ta_expiration'])   ? (int) $config['ta_expiration']    : 120;

        if (!isset($config['ws_url'])) {
            $config['ws_url']       = $this->testing ? 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms' : 'https://wsaa.afip.gov.ar/ws/services/LoginCms';
        }

        if (!isset($config['file_wsdl'])) {
            $config['file_wsdl']    = 'tmp/wsaa_wsdl.xml';
        }

        if (!isset($config['ws_encoding'])) {
            $config['ws_encoding']  = 'ISO-8859-1';
        }

        parent::__construct($config);
    }

    /**
     * setWsName
     *
     * Define el nombre del WebService.
     *
     *
     * @param       string     $ws_name     Nombre del WebService.
     * @return      WSAA
     */
    public function setWsName($ws_name)
    {
        $this->ws_name = $ws_name;
        
        return $this;
    }

    /**
     * getWsName
     *
     * Retorna el nombre del WebService.
     *
     *
     * @return      string                  Nombre del WebService.
     */
    public function getWsName()
    {
        return $this->ws_name;
    }

    /**
     * generatePrivateKey
     *
     * Genera una clave privada.
     *
     *
     * @return  string   Clave privada.
     */
    public static function generatePrivateKey()
    {
        if (!class_exists('Crypt_RSA')) {
            include_once(__DIR__ . '/lib/phpseclib/Crypt/RSA.php');
        }

        $rsa = new Crypt_RSA();
        $pkey = $rsa->createKey(1024);

        return $pkey['privatekey'] . "\n";
    }

    /**
     * generateCsr
     *
     * Genera un Certificate signing request.
     *
     *
     * @return  string   Certificate signing request.
     */
    public static function generateCsr($pkey, $passphrase, $dn)
    {
        $privkey = array($pkey, $passphrase);
        $csr = openssl_csr_new($dn, $privkey);
        openssl_csr_export($csr, $str_csr);

        return $str_csr;
    }

    /**
     * getTa
     *
     * Retorna el TA activo.
     *
     *
     * @return  \SimpleXMLElement   TA activo.
     */
    public function getTa()
    {
        if (strtotime($this->ta->header->expirationTime) < time()) {
            $this->ta = null;
        }

        return $this->ta;
    }

    /**
     * requestTa
     *
     * Solicita un TA nuevo.
     *
     *
     * @return  \SimpleXMLElement   TA activo.
     */
    public function requestTa()
    {
        if ($this->createTRA() && $this->signTRA()) {
            $ta = $this->callWSAA();
            $this->ta = $ta;
        }else {
            $this->ta = null;
        }
        
        return $this->ta;
    }

    /**
     * createTRA
     *
     * Crea un TRA nuevo y lo almacena en un archivo temporario.
     *
     *
     * @return      boolean      Retorna TRUE si fue exitoso, o FALSE si hubo un error.
     */
    private function createTRA()
    {
        $now = time();
        $this->tra_id = md5(mt_rand() . $now . mt_rand());
        $id = $now; //mt_rand();

        $TRA = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">' .
            '</loginTicketRequest>');
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', $now);
        $TRA->header->addChild('generationTime', date('c', $now - $this->sec_tolerance));
        $TRA->header->addChild('expirationTime', date('c', $now + $this->ta_expiration));
        $TRA->addChild('service', $this->ws_name);

        return $TRA->asXML(sprintf($this->tra_tpl_file, $this->tra_id));
    }

    /**
     * signTRA
     *
     * Firma el TRA generado y lo almacena en un archivo temporario.
     *
     *
     * @return      boolean      Retorna TRUE si fue exitoso, o FALSE si hubo un error.
     */
    private function signTRA()
    {
        $success = false;

        if (!is_null($this->tra_id)) {
            $tra_file = sprintf($this->tra_tpl_file, $this->tra_id);
            if (file_exists($tra_file)) {
               error_reporting(E_ERROR);
                try {
                  $respuesta =   openssl_pkcs7_sign(
                                        $tra_file,
                                        sprintf($this->tra_tpl_file . '.cms', $this->tra_id),
                                        $this->str_crt,
                                        array($this->str_pkey, $this->passphrase),
                                        array(),
                                        !PKCS7_DETACHED
                                       );  
                } catch (Exception $ex) {
                   
                }
                  
                 if ($respuesta) {
                    $success = true;
                }else {
                    throw new Exception('ERROR al generar la firma.Certificado no compatible');
                }
            }
        }
        
        return $success;
    }

    /**
     * callWSAA
     *
     * Envía el CMS al método "loginCms" del WSAA.
     *
     *
     * @return      string      Retorna Ticket de Acceso (TA).
     */
    private function callWSAA()
    {
        $ta = false;

        if (!is_null($this->tra_id)) {
            $tra_file = sprintf($this->tra_tpl_file . '.cms', $this->tra_id);
            if (file_exists($tra_file)) {
                $cms = preg_split("|\n\n|", file_get_contents($tra_file));
                $results = $this->loginCms(array('in0' => $cms[1]));
                if (is_soap_fault($results)) {
                    throw new Exception("ERROR: {$results->faultcode} - {$results->faultstring}");
                }

                $ta = new SimpleXMLElement($results->loginCmsReturn);
            }
        }
        
        return $ta;
    }
}
