<?php

class livestream{
    private $_account;
    private $_url = 'http://api.new.livestream.com/accounts/';
    private $_error_status = false;
    private $_error = "";
    private $_status = 200;
    private $_content = "";
    private $_info = array();
    private $_account_info = null;
   
    /* CONSTRUCTOR */
    function __construct($account) {
        $this->_account = $account;
    }
   
   
    /* PRIVATE FUNCTIONS */
   
    private function _exec($url){
        $this->_error = '';
        $this->_info = array();
        $this->_error_status = false;
        $this->_content = '';
        $this->_info = array();
        $this->_status = 200;
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($soap_do);
        $this->_content = $respuesta;
        if (curl_errno($soap_do) <> 0){
            $this->_error_status = true;
            $this->_error = "[".curl_errno($soap_do)."] ".curl_error($soap_do);
        } else {
            $this->_info = curl_getinfo($soap_do);
            $this->_status = is_array($this->_info) && isset($this->_info['http_code']) ? $this->_info['http_code'] : null;
        }
        curl_close($soap_do);
        return $respuesta;
    }
   
    private function _get_respuesta($content){
        if ($this->_status == 200 && !$this->_error_status){
            $arrTemp = json_decode($content, true);
            return self::_arrayToObject($arrTemp);
        } else {
            return false;
        }
    }
   
    static private function _array_to_obj($array, &$obj){
        foreach ($array as $key => $value){
            if (is_array($value)){
                $obj->$key = new stdClass();
                self::_array_to_obj($value, $obj->$key);
            } else {
                $obj->$key = $value;
            }
        }
        return $obj;
    }

    static private function _arrayToObject($array){
        $object = new stdClass();
        return self::_array_to_obj($array,$object);
    }
   
   
    /* PUBLIC FUNCTIONS */
   
    public function error_status(){
        return $this->_error_status;
    }
   
    public function get_status(){
        return $this->_status;
    }
   
    public function get_error(){
        return $this->_error;
    }
   
    public function get_info(){
        return $this->_info;
    }
   
    public function get_response_data(){
        return $this->_content;
    }
   
    public function get_event($eventID){
        $url = $this->_url.$this->_account."/events/".$eventID."/";
        $content = $this->_exec($url);
        return $this->_get_respuesta($content, $url);
    }
   
    public function get_account(){
        if ($this->_account_info == null){
            $url = $this->_url.$this->_account."/";
            $content = $this->_exec($url);
            $this->_account_info = $this->_get_respuesta($content, $url);       
        }
        return $this->_account_info;
    }
   
    public function list_events(){
        $resp = array();
        $account_info = $this->get_account();
        if ($account_info){
            if (isset($account_info->past_events, $account_info->past_events->data) && count($account_info->past_events->data) > 0){
                foreach ($account_info->past_events->data as $data){
                    $start = $data->start_time;
                    $end = $data->end_time;
                    $start_date = substr($start, 0, 10);
                    $end_date = substr($end, 0, 10);
                    $start_time = substr($start, 11, 8);
                    $end_time = substr($end, 11, 8);
                    $resp[] = array(
                        'id' => $data->id,
                        'short_name' => $data->short_name,
                        'full_name' => $data->full_name,
                        'type' => 'past_events',
                        'start_time' => $start_date." ".$start_time,
                        'end_time' => $end_date." ".$end_time
                    );
                }
            }
            if (isset($account_info->upcoming_events, $account_info->upcoming_events->data) && count($account_info->upcoming_events->data) > 0){
                foreach ($account_info->upcoming_events->data as $data){
                    $start = $data->start_time;
                    $end = $data->end_time;
                    $start_date = substr($start, 0, 10);
                    $end_date = substr($end, 0, 10);
                    $start_time = substr($start, 11, 8);
                    $end_time = substr($end, 11, 8);
                    $resp[] = array(
                        'id' => $data->id,
                        'short_name' => $data->short_name,
                        'full_name' => $data->full_name,
                        'type' => 'upcoming_events',
                        'start_time' => $start_date." ".$start_time,
                        'end_time' => $end_date." ".$end_time
                    );
                }
            }
            return $resp;
        } else {
            return false;
        }
    }
   
    public function get_video($eventID, $videoID){
        $url = $this->_url.$this->_account."/events/".$eventID."/videos/".$videoID."/";
        $content = $this->_exec($url);
        return $this->_get_respuesta($content, $url);
    }
   
    /* STATIC FUNCTIONS */
      
}