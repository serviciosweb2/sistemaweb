<?php

class impresiones{
    
    private $id_filial;
    private $account_exists;
    private $error_msg;
    private $print_files_dir;
    private $print_files_url;
    private $print_files_view_dir;
    private $print_files_view_url;
    
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $idFilial){
        $this->id_filial = $idFilial;
        $this->print_files_dir = $_SERVER['HTTP_HOST'] == "localhost" ? $_SERVER['DOCUMENT_ROOT']."/sistemasiga/printer_files/" : $_SERVER['DOCUMENT_ROOT']."/printer_files/";
        $this->print_files_url = $_SERVER['HTTP_HOST'] == "localhost" ? "http://localhost/sistemasiga/printer_files/" : "http://192.168.1.77:2082/printer_files/";
        $this->print_files_view_dir = $_SERVER['HTTP_HOST'] == "localhost" ? $_SERVER['DOCUMENT_ROOT']."/sistemasiga/printer_files/view/" : $_SERVER['DOCUMENT_ROOT']."/printer_files/view/";
        $this->print_files_view_url = $_SERVER['HTTP_HOST'] == "localhost" ? "http://localhost/sistemasiga/printer_files/view/" : "http://192.168.1.77:2082/printer_files/view/";
        $myGoogleAccount = new cuentas_google($conexion, $idFilial);
        if ($myGoogleAccount->user <> '' && $myGoogleAccount->pass <> ''){
            $this->account_exists = true;
        } else {
            $this->account_exists = false;
        }
    }
    
    
    /* PRIVATE FUNCTIONS */
    
    private function getPrinterScript(CI_DB_mysqli_driver $conexion, $idScript){
        $conexion->select("printer_id");
        $conexion->from("general.filiales_script_impresoras");
        $conexion->where("id_filial", $this->id_filial);
        $conexion->where("id_script", $idScript);
        $subquery1 = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("printer_id");
        $conexion->from("general.impresoras_filiales");
        $conexion->where("id_filial", $this->id_filial);
        $conexion->where("default", 1);
        $conexion->limit(1, 0);
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("IFNULL(($subquery1), ($subquery2)) AS printer_id", false);        
        
        $query = $conexion->get();
        $respuesta = $query->result_array();
        if (count($respuesta) > 0){
            return $respuesta[0]['printer_id'];
        } else {
            return '';
        }
    }
    
    
    /* PUBLIC FUNCTIONS */
        
    /**
     * retorna el ultimo error ocurrido
     * 
     * @return string
     */
    
    /**
     * retorna el directorio temporal donde se guardan los documentos a imprimir
     * 
     * @return string
     */
    public function getFilesDir(){
        return $this->print_files_dir;
    }
    
    /**
     * retorna el ultimo error ocurrido
     * 
     * @return string
     */
    public function getError(){
        return $this->error_msg;
    }
    
    
    /**
     * setea el uso de una impresora de google cloud print para ser utilizada en un script
     * 
     * @param CI_DB_mysqli_driver $conexion          Objeto de conexion a la base de datos
     * @param string $printerID     el identificador de la impresora en GCP
     * @param integer $idScript     el identificador del script que intenta imprimir
     * @return boolean
     */
    public function setPrinterScript(CI_DB_mysqli_driver $conexion, $printerID, $idScript, $metodo){
        $conexion->where("id_filial", $this->id_filial);
        $conexion->where("id_script", $idScript);
        $resp = $conexion->delete("general.filiales_script_impresoras");
        
        $arrTemp = array(
                        "id_filial" => $this->id_filial, 
                        "id_script" => $idScript, 
                        "printer_id" => $printerID,
                        "metodo" => $metodo
                    );
        
        $conexion->resetear();
        $resp = $resp && $conexion->insert("general.filiales_script_impresoras", $arrTemp);        
    }
    
    /**
     * Imprime un documento HTML teniendo en cuenta la configuracion del script de impresion (utiliza GCP o metodo standar)
     * 
     * @param CI_DB_mysqli_driver $conexion          Objeto de conexion a la base de datos
     * @param integer $scriptID     el identificador del script que intenta imprimir
     * @param string $htmlContent   contenido HTML a imprimir
     * @return boolean
     */
    public function printerHTML(CI_DB_mysqli_driver $conexion, $scriptID, $htmlContent, $printerID = null){
        if ($printerID == null){
            $printerID = $this->getPrinterScript($conexion, $scriptID);
        }
        $filename = md5("printer html ".$this->id_filial.$scriptID.date("H-m-d H:i:s").microtime(true)).".html";
        if ($this->account_exists && $printerID <> -1){
            $myImpresora = new impresoras_cloud_print($conexion, $this->id_filial, $printerID);
            $status = $myImpresora->getStatus();
            if ($status <> "ONLINE"){
                $this->error_msg = "La impresora no se encuentra ONLINE (status: $status)";
                return false;
            } else {
                file_put_contents($this->print_files_dir.$filename, $htmlContent);
                $resp = $myImpresora->printer($this->print_files_url.$filename, "url", $filename);
                if (!$resp)
                    $this->error_msg = $myImpresora->getError();
                return $resp;
            }
        } else { // de ser necesario estilos css deben agregarse a $htmlContent como link o entre etiquetas
            echo $htmlContent."<script>window.print();</script>";
            ?>

            <?php
            return true;
        }
    }
    
    
    /**
     * Imprime un documentoPDF teniendo en cuenta la configuracion del script de impresion (utiliza GCP o metodo standar)
     * 
     * @param integer $scriptID     el identificador del script que llama a la impresion
     * @return boolean;
     */
    public function printerPDF(CI_DB_mysqli_driver $conexion, $scriptID, PDF_AutoPrint $objectPDF, $printerID = null){
        $printerID = $printerID != null ? $printerID : $this->getPrinterScript($conexion, $scriptID);
        $filename = md5("printer pdf ".$this->id_filial.$scriptID.date("H-m-d H:i:s").microtime(true)).".pdf";
        if ($this->account_exists && $printerID <> -1){
            $myImpresora = new impresoras_cloud_print($conexion, $this->id_filial, $printerID);
         
            $status = $myImpresora->getStatus();
          
            if ($status <> "ONLINE"){
                $this->error_msg = "La impresora no se encuentra ONLINE (status: $status)";
                return false;
            } else {
//                $objectPDF->Output($this->print_files_dir.$filename, "F");
       
                $stringPDF = $objectPDF->Output("temp_pdf", "S");
                
                $resp = $myImpresora->printer($this->print_files_dir.$filename, "application/pdf", $filename, $stringPDF);
                if (!$resp)
                    $this->error_msg = $myImpresora->getError();
                return $resp;
            }
        } else {
            if (ob_get_contents()){
                ini_set('zlib.output_compression', '0');
                ob_end_clean();
            }            
            $objectPDF->Output($this->print_files_view_dir.$filename, "I");            
            return true;
        }
    }   

    public function printerFile(CI_DB_mysqli_driver $conexion, $scriptID, $filename){
        $printerID = $this->getPrinterScript($conexion, $scriptID);
        if ($this->account_exists && $printerID <> ''){
            $myImpresora = new impresoras_cloud_print($conexion, $this->id_filial, $printerID);
            $status = $myImpresora->getStatus();
            if ($status <> "ONLINE"){
                $this->error_msg = "La impresora no se encuentra ONLINE (status: $status)";
                return false;
            } else {
                $resp = $myImpresora->printer($filename, "application/pdf", $filename);
                if (!$resp)
                    $this->error_msg = $myImpresora->getError();
                return $resp;
            }
        } else {
            ?>
                <script>
                    var printer = window.open('','','width=930,height=600');
                    printer.document.open("text/html" , "replace");
                    printer.document.write('<html><head><title><?php echo $filename; ?></title></head><body>');
                    printer.document.write('<embed width="100%" height="100%" src="<?php echo $filename; ?>"></embed>');
                    printer.document.write('</body></html>');
                </script>
            <?php
            return true;
        }
    }
    
    /* STATIC FUNCTIONS */
    
    static function listarImpresorasScript(CI_DB_mysqli_driver $conexion, $idFilial = null, $idScript = null){
        $conexion->select("*");
        $conexion->from("general.filiales_script_impresoras");
        if ($idFilial != null) $conexion->where("id_filial", $idFilial);
        if ($idScript != null) $conexion->where("id_script", $idScript);
        $query = $conexion->get();
        return $query->result_array();
    }
}

