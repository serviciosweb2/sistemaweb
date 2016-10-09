<?php

/**
 * BasicSoapClient
 *
 * Clase base para WebServices SOAP.
 *
 *
 * @author Juan Pablo Candioti
 */
class BasicSoapClient
{
    /**
     * $soap_version
     *
     * @var integer     Versión SOAP del WebService.
     */
    protected $soap_version;

    /**
     * $ws_encoding
     *
     * @var string      Charset del WebService.
     */
    protected $ws_encoding;

    /**
     * $ws_url
     *
     * @var string      URL del WebService.
     */
    protected $ws_url;

    /**
     * $proxy_host
     *
     * @var string      Host del proxy.
     */
    protected $proxy_host;

    /**
     * $proxy_port
     *
     * @var integer     Puerto del proxy.
     */
    protected $proxy_port;

    /**
     * $proxy_login
     *
     * @var string      Nombre de usuario del proxy.
     */
    protected $proxy_login;

    /**
     * $proxy_password
     *
     * @var string      Contraseña del proxy.
     */
    protected $proxy_password;

    /**
     * $file_wsdl
     *
     * @var string      Ubicación dónde se almacenará un caché del WSDL del WebService.
     */
    protected $file_wsdl;

    /**
     * $soap_client
     *
     * @var \SoapClient Instancia del cliente SOAP ya configurado.
     */
    protected $soap_client;


    function __construct(array $config=array())
    {
        $this->soap_version     = isset($config['soap_version'])    ? $config['soap_version']           : SOAP_1_2;
        $this->ws_encoding      = isset($config['ws_encoding'])     ? $config['ws_encoding']            : 'UTF-8';
        $this->ws_url           = isset($config['ws_url'])          ? $config['ws_url']                 : '';
        $this->file_wsdl        = isset($config['file_wsdl'])       ? $config['file_wsdl']              : 'tmp/ws_wsdl.xml';
        $this->proxy_host       = isset($config['proxy_host'])      ? $config['proxy_host']             : null;
        $this->proxy_port       = isset($config['proxy_port'])      ? $config['proxy_port']             : null;
        $this->proxy_login      = isset($config['proxy_login'])     ? $config['proxy_login']            : null;
        $this->proxy_password   = isset($config['proxy_password'])  ? $config['proxy_password']         : null;
        $this->soap_client      = null;
    }

    /**
     * setProxyHost
     *
     * Define el host del servidor proxy.
     *
     *
     * @param       string     $proxy_host      Host del servidor proxy.
     * @return      BasicSoapClient
     */
    public function setProxyHost($proxy_host)
    {
        $this->proxy_host = $proxy_host;
        
        return $this;
    }

    /**
     * getProxyHost
     *
     * Retorna el host del servidor proxy.
     *
     *
     * @return      string                  Host del servidor proxy.
     */
    public function getProxyHost()
    {
        return $this->proxy_host;
    }

    /**
     * setProxyPort
     *
     * Define el puerto del servidor proxy.
     *
     *
     * @param       string     $proxy_port      Puerto del servidor proxy.
     * @return      BasicSoapClient
     */
    public function setProxyPort($proxy_port)
    {
        $this->proxy_port = $proxy_port;
        
        return $this;
    }

    /**
     * getProxyPort
     *
     * Retorna el puerto del servidor proxy.
     *
     *
     * @return      string                  Puerto del servidor proxy.
     */
    public function getProxyPort()
    {
        return $this->proxy_port;
    }

    /**
     * setProxyLogin
     *
     * Define el nombre de usuario del servidor proxy.
     *
     *
     * @param       string     $proxy_login      Nombre de usuario del servidor proxy.
     * @return      BasicSoapClient
     */
    public function setProxyLogin($proxy_login)
    {
        $this->proxy_login = $proxy_login;
        
        return $this;
    }

    /**
     * getProxyLogin
     *
     * Retorna el nombre de usuario del servidor proxy.
     *
     *
     * @return      string                  Nombre de usuario del servidor proxy.
     */
    public function getProxyLogin()
    {
        return $this->proxy_login;
    }

    /**
     * setProxyPassword
     *
     * Define la contraseña del servidor proxy.
     *
     *
     * @param       string     $proxy_password      Contraseña del servidor proxy.
     * @return      BasicSoapClient
     */
    public function setProxyPassword($proxy_password)
    {
        $this->proxy_password = $proxy_password;
        
        return $this;
    }

    /**
     * getProxyPassword
     *
     * Retorna la contraseña del servidor proxy.
     *
     *
     * @return      string                  Contraseña del servidor proxy.
     */
    public function getProxyPassword()
    {
        return $this->proxy_password;
    }

    /**
     * getSoapClient
     *
     * Retorna la instancia activa de SoapClient.
     *
     *
     * @return      SoapClient
     */
    public function getSoapClient()
    {
        return $this->soap_client;
    }

    /**
     * __call
     *
     * Método mágico que ejecuta las funciones definidas en el WebService.
     *
     * @param   string      $name       Nombre de la función del WebService.
     * @param   array       $arguments  Arreglo con los parámetros de la función WebService.
     * @return  \stdClass   Objeto con la estructura de la respuesta del WebService.
     */
    function __call($name, $arguments)
    {
        if (is_null($this->soap_client)) {
            $options = array(
                             'soap_version' => $this->soap_version,
                             'location'     => $this->ws_url,
                             'trace'        => 1,
                             'encoding'     => $this->ws_encoding,
                             'exceptions'   => 0
                             );
            if (!is_null($this->proxy_host)) {
                $options['proxy_host'] = $this->proxy_host;
                $options['proxy_port'] = is_null($this->proxy_port) ? 3128 : $this->proxy_port;
                if (!is_null($this->proxy_login)) {
                    $options['proxy_login'] = $this->proxy_login;
                }
                if (!is_null($this->proxy_password)) {
                    $options['proxy_password'] = $this->proxy_password;
                }
            }

            $this->soap_client = new SoapClient($this->file_wsdl, $options);
        }

        return $this->soap_client->__soapCall($name, $arguments);
    }
    
    /**
     * updateWsdl
     *
     * Actualiza el archivo XML con la información WSDL del WebService.
     *
     *
     * @return void
     */
    public function updateWsdl()
    {
        file_put_contents($this->file_wsdl, file_get_contents($this->ws_url . '?wsdl'));
    }
}
