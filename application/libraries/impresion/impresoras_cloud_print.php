<?php

class impresoras_cloud_print{
    
    private $id_filial;
    private $printer_id;
    public $nombre;
    public $display;
    public $proxy;
    private $default;
    private $error_msg;
    private $googleAccount;
    
    private $printer_exists = false;

    static private $tableName = "impresoras_filiales";
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $id_filial, $printer_id){
        $this->id_filial = $id_filial;
        $this->printer_id = $printer_id;
        $arrImpresoras = $this->construct($conexion);
        if (isset($arrImpresoras[0]['id_filial'])){
            $this->printer_exists = true;
            $this->nombre = $arrImpresoras[0]['nombre'];
            $this->display = $arrImpresoras[0]['display'];
            $this->proxy = $arrImpresoras[0]['proxy'];
            $this->googleAccount = new cuentas_google($conexion, $id_filial);
            $this->default = $arrImpresoras[0]['default'];
        } else {
            $this->printer_exists = false;
        }
    }
    
    /* PRIVATE FUNCTIONS */
    
    private function getObjectToArray(){
        $arrResp = array();
        $arrResp['id_filial'] = $this->id_filial;
        $arrResp['printer_id'] = $this->printer_id;
        $arrResp['nombre'] = $this->nombre;
        $arrResp['display'] = $this->display;
        $arrResp['proxy'] = $this->proxy;
        $arrResp['default'] = !is_numeric($this->default) ? 0 : $this->default;
        return $arrResp;        
    }
    
    private function insert(CI_DB_mysqli_driver $conexion){
        $conexion->where("id_filial", $this->id_filial);
        $conexion->where("printer_id", $this->printer_id);
        $conexion->delete(self::$tableName);
        $this->printer_exists = $conexion->insert(self::$tableName, $this->getObjectToArray());
        return $this->printer_exists;
    }
    
    private function update(CI_DB_mysqli_driver $conexion){
        $conexion->where("id_filial", $this->id_filial);
        $conexion->where("printer_id", $this->printer_id);
        return $conexion->update(self::$tableName, $this->getObjectToArray());
    }
    
    private function construct(CI_DB_mysqli_driver $conexion){
        $conexion->select("*");
        $conexion->from("general.".self::$tableName);
        $conexion->join("general.filiales_cuentas_google", "general.filiales_cuentas_google.id_filial = general.impresoras_filiales.id_filial", "left");
        $conexion->where(self::$tableName.".id_filial", $this->id_filial);
        $conexion->where("printer_id", $this->printer_id);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /* PUBLIC FUNCTIONS */   

    
    /**
     * Asigna una impresora Cloud Print como impresora para imprimir por defecto (en todos los scripts de impresion)
     * 
     * @param CI_DB_mysqli_driver $conexion Objeto para la conexion a la base de datos
     * @return boolean
     */
    public function setDefault(CI_DB_mysqli_driver $conexion){
        $resp = impresoras_cloud_print::unsetDefault($conexion, $this->id_filial);
        $conexion->resetear();
        $conexion->where("id_filial", $this->id_filial);
        $conexion->where("printer_id", $this->printer_id);
        $resp && $conexion->update("general.".self::$tableName, array("default" => 1));
        $conexion->resetear();
        $conexion->where("id_filial", $this->id_filial);
        return $resp && $conexion->update("general.filiales_script_impresoras", array("printer_id" => $this->printer_id));
    }
    
    /**
     * Retorna el último error ocurrido
     * 
     * @return string
     */
    public function getError(){
        return $this->error_msg;
    }
    
    /**
     * Guarda o actualiza una impresora Google Cloud Print definida por la filial
     * 
     * @param CI_DB_mysqli_driver $conexion      Objeto de conexion a la base de datos
     * @return boolean
     */
    public function guardar(CI_DB_mysqli_driver $conexion){
        if ($this->printer_exists)
            return $this->update($conexion);
        else
            return $this->insert($conexion);
    }
    
    /**
     * retorna el ID de Google Cloud Print
     * 
     * @return string
     */
    public function getPrinterID(){
        return $this->printer_id;
    }
    
    
    /**
     * retorna el estado de la impresora frente al servicio de google cloud print
     * 
     * @param CI_DB_mysqli_driver $conexion      Objeto de conexion a la base de datos
     * @return mixed    array on success or false
     */
    public function getStatus(){
        
        if (!$this->printer_exists){
            return "Printer Not Found";
        } else if (!$this->googleAccount->accountExists()){
            return "Invalid Account";
        } else {
            $arrImpresoras = impresoras_cloud_print::getGoogleCloudPrinters($this->googleAccount);
            $i = 0;
            while ($i < count($arrImpresoras)) {
                if ($arrImpresoras[$i]['id'] <> $this->printer_id){
                    return $arrImpresoras[$i]['connectionStatus'];
                }
                $i++;
            }
            return false;
        }
    }
    
    
    /**
     * retorna los trabajos de impresion realizados sobre la impresora en google cloud print
     * 
     * @param string $status        el estado de los registros
     * @param string $offset        el indice desde el cual recuperar los registros
     * @param string $limit         la cantidad de registros a recuperar  * 
     * @return array
     */
    public function getJobs($status = null, $offset = null, $limit = null){
        $gcp = new GoogleCloudPrint($this->googleAccount->user, $this->googleAccount->pass);
        return $gcp->getJobs($this->printer_id, $status, $offset, $limit);        
    }
      
    
    /**
     * Retorna el proximo trabajo a imprimir pendiente en la cola de impresion de google cloud print
     * 
     * @param CI_DB_mysqli_driver $conexion  Objeto de conexion a la base de datos
     * @return array
     */
    public function getNextJobs(){
        $gcp = new GoogleCloudPrint($this->googleAccount->user, $this->googleAccount->pass);
        $arrResp = $gcp->getNextJobs($this->printer_id);
        if (isset($arrResp['errorCode']) && $arrResp['errorCode'] == 413){ // (error 413) no hay mas trabajo para imprimir
            return array();
        } else {
            return $arrResp;
        }
    }
    
    /**
     * retorna un array de descripcion de la impresora
     * 
     * @param CI_DB_mysqli_driver $conexion   Objeto de conexion a la base de datos
     * @return mixed    false en caso de error array on success
     */
    public function getDescription(){
        if (!$this->printer_exists){
            $this->error_msg = "No existe la impresora {$this->printer_id}";
            return false;
        } else if (!$this->googleAccount->accountExists()){
            $this->error_msg = "No existe la cuenta de google";
            return false;
        } else {
            $gcp = new GoogleCloudPrint($this->googleAccount->user, $this->googleAccount->pass);
            $arrResp = $gcp->getPrinterDescription($this->printer_id);
            if (isset($arrResp['errorCode'])){
                $this->error_msg = "[{$arrResp['errorCode']}] {$arrResp['message']}";
                return false;
            } else {
                return $arrResp;
            }
        }
    }
    
    
    /**
     * envia un trabajo de impresion a Google Cloud Print
     * 
     * @param string $file_url      La ubicacion del archivo a imprimir o la url de impresion
     * @param string $contentType   El tipo de impresion (application/pdf, url, etc)
     * @param string $title         El titulo del documento a imprimir
     * @return boolean
     */
    public function printer($file_url, $contentType, $title = null, $stringPDF = null){ //application/pdf - url
        
        if ($title === null)
            $title = "documento ".date("YmdHis");
        
        $gcp = new GoogleCloudPrint2();
     
        $ci = & get_instance();
        $token = $ci->session->userdata('accessToken');
        $gcp->setAuthToken($token);
        
        $retorno = $gcp->sendPrintToPrinter($this->printer_id, $title, $file_url, $contentType, $stringPDF);
        if (!$retorno)
            $this->error_msg = $gcp->getError();
        return $retorno;
    }
    
   
    /**
     * cancela el trabajo de impresion identificado por $jobsID
     * 
     * @param string $jobsID    El identificador del trabajo en GCP
     * @return boolean
     */
    public function cancelJobs($jobsID){
        if (!$this->googleAccount->accountExists()){
            throw new Exception("No se ha definido una cuenta de Google Cloud Print");
        } else {
            $gcp = new GoogleCloudPrint($this->googleAccount->user, $this->googleAccount->pass);
            $resp = $gcp->deleteJob($jobsID);
            if (!$resp){
                $this->error_msg = $gcp->getError();
            }
            return $resp;
        }
    }
    
    
    /* STATIC FUNCTIONS */
    
    /**
     * Quita la asignacion de impresoras por default
     * 
     * @param CI_DB_mysqli_driver $conexion Objeto para la conexion a la base de datos
     * @return boolean
     */
    static public function unsetDefault(CI_DB_mysqli_driver $conexion, $idFilial){
        $conexion->where("id_filial", $idFilial);
        return $conexion->update("impresoras_filiales", array("default" => 0));
    }
        
    /**
     * Retorna un lista de impresoras registradas en GCP y que estan actualmente en uso;
     * 
     * @param CI_DB_mysqli_driver $conexion          Objeto de ocnexion a la base de datos
     * @param integer $idFilial     El identificador de la filial
     * @return type
     */
    static function listarImpresoras(CI_DB_mysqli_driver $conexion, $idFilial = null, $default = null){
        $conexion->select("*");
        $conexion->from(self::$tableName);
        if ($idFilial != null)
            $conexion->where("id_filial", $idFilial);
        if ($default !== null)
            $conexion->where("default", $default);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /**
     * Retorna las impresoras cloud print que la filial utiliza para imprimir en script de impresion
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param int $idFilial
     * @return array
     */
    static function getImpresorasUtilizadas(CI_DB_mysqli_driver $conexion, $idFilial){
        $conexion->select("impresoras_filiales.*");
        $conexion->from("general.filiales_script_impresoras");
        $conexion->join("general.impresoras_filiales", "general.impresoras_filiales.printer_id = general.filiales_script_impresoras.printer_id");
        $conexion->where("general.filiales_script_impresoras.id_filial", $idFilial);
        //$conexion->group_by("general.filiales_script_impresoras.printer_id");
        $query = $conexion->get();
        return $query->result_array();
    }
    
    /**
     * Lista las impresoras agregadas en google cloud print para la cuenta especificada
     * 
     * @return boolean
     * @throws Exception    si no se encuentran los datos de usuario para GCP
     */
    static public function getGoogleCloudPrinters(cuentas_google $googleAccount){
           
        if ($googleAccount->user == '' || $googleAccount->pass == ''){
            throw new Exception("No se ha definido una cuenta de Google Cloud Print");
        } else {
            $gcp = new GoogleCloudPrint2();
            $ci = & get_instance();
            $token = $ci->session->userdata('accessToken');
            $gcp->setAuthToken($token);
            if (!$gcp){
                return false;
            } else {
                return $gcp->getPrinters();
            }
        }
    }
}

