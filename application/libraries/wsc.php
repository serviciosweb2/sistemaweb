<?php

class wsc{
    
    private $_user = "admin";
    private $_pass = "adminCibernet77";
    private $_md_comp = "y_h23oo0154__m";
    private $_url;
    private $_param = array();
    private $_error = '';
    private $_errno = '';
    private $_info = '';
    private $_status = '';
    private $_response = '';
    
    /* CONSTRUCTOR */
    
    function __construct(array $param = null) {
        $this->_url = $_SERVER['HTTP_HOST'] == 'localhost' 
                ? "http://localhost/panelcontrol/ws_server/wss.php"
                //? "https://panelcontrol.iga-la.com/panelcontrol/ws_server/wss.php"
                : "https://panelcontrol.iga-la.com/panelcontrol/ws_server/wss.php";
        if ($param != null){
            $this->_param = $param;
        }
    }
    
    /* PRIVATE FUNCTIONS */
    
    /* PUBLIC FUNCTIONS */
    
    public function set_param(array $param){
        $this->_param = $param;
    }
    
    public function get_status(){
        return $this->_status;
    }
    
    public function get_error(){
        if ($this->_errno <> ''){
            return "[".$this->_errno."] ".$this->_error;
        } else if ($this->_status <> 200){
            return "[".$this->_status."] ".self::_get_message_status($this->_status);
        } else {
            return '';
        }
    }
    
    public function is_error(){
        return $this->_errno <> '' || $this->_status <> 200;
    }
    
    public function get_response(){
        return $this->_response;
    }
    
    public function getInfo(){
        return $this->_info;
    }
    
    public function exec(){
        $this->_errno;
        $this->_error = '';
        $this->_status = '';
        $this->_info = array();
        $header = array (
            'Connection: close', 
            'Accept-Language : es-ar,es;q=0.8,en-us;q=0.5,en;q=0.3',
            'X_AUTHORIZATION_TOKEN: 343b1b831066a40e308e0af92e0f06f0',
            'CONTENT: '.md5(md5(md5($this->_user.md5($this->_pass).$this->_md_comp)))
        );
        $soap_do = curl_init();
        if (strpos(" {$this->_url}", "https")){
            curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($soap_do, CURLOPT_URL, $this->_url);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        curl_setopt($soap_do, CURLOPT_USERPWD, $this->_user.":".md5($this->_pass));
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($soap_do,CURLOPT_POSTFIELDS,http_build_query($this->_param));
        $this->_response = curl_exec($soap_do);
        $this->_errno = curl_errno($soap_do);
        $this->_error = curl_error($soap_do);
        $this->_info = curl_getinfo($soap_do);
        curl_close($soap_do);
        if (is_array($this->_info) && isset($this->_info['http_code'])){
            $this->_status = $this->_info['http_code'];
        }        
        if ($this->_errno <> 0 || $this->_status <> 200){
            return false;
        } else{
            $arrTemp = json_decode($this->_response, true);
            return is_array($arrTemp) ? $arrTemp : $this->_response;
        }
    }
    
    /* STATIC FUNCTIONS */
    
    static private function _get_message_status($statusCode){
        $status = array(
                200	=> 'OK',
                201	=> 'Created',
                202	=> 'Accepted',
                203	=> 'Non-Authoritative Information',
                204	=> 'No Content',
                205	=> 'Reset Content',
                206	=> 'Partial Content',

                300	=> 'Multiple Choices',
                301	=> 'Moved Permanently',
                302	=> 'Found',
                304	=> 'Not Modified',
                305	=> 'Use Proxy',
                307	=> 'Temporary Redirect',

                400	=> 'Bad Request',
                401	=> 'Unauthorized',
                403	=> 'Forbidden',
                404	=> 'Not Found',
                405	=> 'Method Not Allowed',
                406	=> 'Not Acceptable',
                407	=> 'Proxy Authentication Required',
                408	=> 'Request Timeout',
                409	=> 'Conflict',
                410	=> 'Gone',
                411	=> 'Length Required',
                412	=> 'Precondition Failed',
                413	=> 'Request Entity Too Large',
                414	=> 'Request-URI Too Long',
                415	=> 'Unsupported Media Type',
                416	=> 'Requested Range Not Satisfiable',
                417	=> 'Expectation Failed',

                500	=> 'Internal Server Error',
                501	=> 'Not Implemented',
                502	=> 'Bad Gateway',
                503	=> 'Service Unavailable',
                504	=> 'Gateway Timeout',
                505	=> 'HTTP Version Not Supported'
        );
        return isset($status[$statusCode]) ? $status[$statusCode] : '';
    }    
    
    static function validar(&$wsc = null){
        if ($wsc == null){
            $wsc = new wsc();
        }
    }
    
}