<?php

class GoogleCloudPrint {
	
    const LOGIN_URL = "https://www.google.com/accounts/ClientLogin";
    const PRINTERS_SEARCH_URL = "https://www.google.com/cloudprint/interface/search";
    const PRINT_URL = "https://www.google.com/cloudprint/interface/submit";
    const JOBS_URL = "https://www.google.com/cloudprint/interface/jobs";
    const DELETEJOB_URL = "https://www.google.com/cloudprint/interface/deletejob";
    const CONTROL_URL = "https://www.google.com/cloudprint/interface/control";
    const FETCH_URL = "https://www.google.com/cloudprint/interface/fetch";
    const PRINTER = "https://www.google.com/cloudprint/interface/printer";
    
    
    private $emailaddress;
    private $password;
    private $authtoken;
    private $mensajeError;
    private $errorCode;
    
    /* CONSTRUCTOR */

    function __construct($email, $password) {
        $this->emailaddress = $email;
        $this->password = $password;
        $this->authtoken = $_SESSION["OAUTH_TOKEN"];
        /*if (!$this->loginToGoogle()){
            return false;
        } else {
            return true;
        }*/
    }
	
    /* PRIVATE FUNCTIONS */
    
    private function loginToGoogle() {
        $loginpostfileds = array(
            "accountType" => "HOSTED_OR_GOOGLE",
            "Email" => $this->emailaddress,
            "Passwd" => $this->password,
            "service" => "cloudprint",
            "source" => "GCP"
        );
        $loginresponse = $this->makeHttpCall(self::LOGIN_URL,$loginpostfileds);
        $token = $this->getAuthToken($loginresponse);
        if(!empty($token)&&!is_null($token)){
            $this->authtoken = $token;
            return true;
        } else {
            return false;
        }
    }
    
    private function parseJobs($jsonobj){
        $jobs = array();
        if (isset($jsonobj->jobs)){
            foreach ($jsonobj->jobs as $gcpjobs){
                $jobs[] = array(
                    'status' => $gcpjobs->status,
                    'printerType' => $gcpjobs->printerType,
                    'contentType' => $gcpjobs->contentType,
                    'title' => $gcpjobs->title,
                    'numberOfPages' => $gcpjobs->numberOfPages,
                    'id' => $gcpjobs->id,
                    'printerid' => $gcpjobs->printerid
                );
            }
        }
        return $jobs;        
    }
    
    private function parsePrinters($jsonobj) {
        $printers = array();
        if (isset($jsonobj->printers)){
            foreach ($jsonobj->printers as $gcpprinter){
                $printers[] = array(
                    'name' => $gcpprinter->name,
                    'id' =>$gcpprinter->id, 
                    'status' => $gcpprinter->connectionStatus,
                    'displayName' => $gcpprinter->displayName,
                    'proxy' => $gcpprinter->proxy);
            }
        }
        return $printers;
    }
	
    private function getAuthToken($response){
        $matches = '';
        preg_match("/Auth=([a-z0-9_-]+)/i", $response, $matches);
        $authtoken = @$matches[1];
        return $authtoken;
    }
 	
    private function makeHttpCall($url,$postfields=array(),$headers=array()){
        $curl = curl_init($url);
        if(!empty($postfields)){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        }
        if(!empty($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }		
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);		
        return $response;
    }
    
    /* PUBLIC FUNCTIONS */
    
    public function getPrinterDescription($printerid){
        if (empty($this->authtoken)){
            throw new Exception("Login Incorrecto");
        } else {
            $authheaders = array(
                "Authorization: GoogleLogin auth=" . $this->authtoken,
                "GData-Version: 3.0",
            );
            $post_fields = array(				
                'printerid' => $printerid
            );
            $response = json_decode($this->makeHttpCall(self::PRINTER, $post_fields, $authheaders), true);
            return $response;
        }
    }
    
    public function getError(){
        return "[{$this->errorCode}] {$this->mensajeError}";
    }
    
    public function getNextJobs($printerid){
        if (empty($this->authtoken)){
            throw new Exception("Login Incorrecto");
        } else {
            $authheaders = array(
                "Authorization: GoogleLogin auth=" . $this->authtoken,
                "GData-Version: 3.0",
            );
            $post_fields = array(				
                'printerid' => $printerid
            );
            $response = json_decode($this->makeHttpCall(self::FETCH_URL, $post_fields, $authheaders), true);
            return $response;
        }
    }
    
    public function setJobsStatus($jobid, $jobstatus){ // no probado por falta de trabajos en cola
        if (empty($this->authtoken)){
            throw new Exception("Login Incorrecto");
        } else {
            $authheaders = array(
                "Authorization: GoogleLogin auth=" . $this->authtoken,
                "GData-Version: 3.0",
            );            
            $post_fields = array(				
                'jobid' => $jobid,
                'status' => $jobstatus // status puede ser: QUEUED, IN_PROGRESS, DONE, ERROR
            );
            $response = json_decode($this->makeHttpCall(self::CONTROL_URL, $post_fields, $authheaders));
            echo "<pre>"; print_r($response); echo "</pre>";
        }
    }
    
    public function deleteJob($jobid){
        if (empty($this->authtoken)){
            throw new Exception("Login Incorrecto");
        } else {
            $post_fields = array(				
                'jobid' => $jobid	
            );
            $authheaders = array(
                "Authorization: GoogleLogin auth=" . $this->authtoken,
                "GData-Version: 3.0",
            );
            $response = json_decode($this->makeHttpCall(self::DELETEJOB_URL, $post_fields, $authheaders));
            if ($response->success){
                return true;
            } else {
                $this->mensajeError = $response->message;
                $this->errorCode = $response->errorCode;
                return false;
            }
        }
    }
   
    public function getJobs($printerid, $status = null, $offset = null, $limit = null){
        if (empty($this->authtoken)){
            throw new Exception("Login Incorrecto");
        } else {
            $arrTemp = array();
            $arrTemp['printerid'] = $printerid;
            if ($status !== null)
                $arrTemp['status'] = $status;
            if ($offset !== null)
                $arrTemp['offset'] = $offset;
            if ($limit !== null)
                $arrTemp['limit'] = $limit;
            $post_fields = $arrTemp;
            $authheaders = array(
                "Authorization: GoogleLogin auth=" . $this->authtoken,
                "GData-Version: 3.0",
            );
            $response = json_decode($this->makeHttpCall(self::JOBS_URL, $post_fields, $authheaders));
            return $this->parseJobs($response);
        }
    }
    
    public function getPrinters() {
        if(empty($this->authtoken)) {
            return false;
        }
        $authheaders = array(
            "Authorization: GoogleLogin auth=" . $this->authtoken,
            "GData-Version: 3.0",
        );
        $responsedata = $this->makeHttpCall(self::PRINTERS_SEARCH_URL, array(), $authheaders);
        $printers = json_decode($responsedata);
        if(is_null($printers)) {
            return array();
        } else {
            return $this->parsePrinters($printers);
        }
    }
	
    public function sendPrintToPrinter($printerid, $printjobtitle, $filepath, $contenttype, $stringPDF = null){
        if(empty($this->authtoken)) {
            throw new Exception("Login Incorrecto");
        }
        if(empty($printerid)) {
            throw new Exception("Falta parametro printer ID");	
        }
        
        if ($contenttype <> "url"){ // si es url se envia la url del documento en lugar del contenido del archivo
            
            if ($stringPDF == null){
                $handle = fopen($filepath, "rb");
                if(!$handle){
                    throw new Exception("No se puede leer el archivo");
                }
                $contents = fread($handle, filesize($filepath));
                fclose($handle);
                $contents = base64_encode($contents);
            } else {
                $contents = base64_encode($stringPDF);
            }
            
        } else {
            $contents = base64_encode($filepath);            
        }
        
        $post_fields = array(				
            'printerid' => $printerid,
            'title' => $printjobtitle,
            'contentTransferEncoding' => 'base64',
            'content' => $contents, //base64 del contenido del archivo
            'contentType' => $contenttype		
        );
        $authheaders = array(
            "Authorization: GoogleLogin auth=" . $this->authtoken
        );
        $response = json_decode($this->makeHttpCall(self::PRINT_URL, $post_fields, $authheaders));
        if($response->success=="1") {
//            echo "<pre>"; print_r($response); echo "</pre>";
            return true;
        } else {
            $this->mensajeError = $response->message;
            $this->errorCode = $response->errorCode;
//            echo "<pre>"; print_r($response); echo "</pre>";
            return false;
        }
    }
}