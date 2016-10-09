<?php

define("STR_CASE_NORMAL", 1);
define("STR_CASE_LOWER", 2);
define("STR_CASE_UPPER", 3);
define("STR_CASE_UCWORDS", 4);
define("STR_CASE_UCFIRST", 5);

class export{
    
    private $title;
    private $content = array();
    private $type;
    private $fieldSeparator = ';';
    private $fieldEncapsulant = "";
    private $lineBreak = "\n";
    private $utfDecode = true;
    private $columnWidth = array();
    private $_pdf_font_size = 10;
    private $_showLineNumber = true;
    private $_imgLogo = '';
    private $_info = array();
    private $_content_height = 8;
    private $_report_title = '';
    private $_margin_top = 4;
    private $_margin_left = 8;
    private $_margin_right = null;
    private $_page_format = "L";
    private $_body_resaltar = array();
    private $_logo_x = 270;
    private $_logo_y = 8;
    private $_logo_width = 15;
    private $_logo_height = 0;
    private $_content_acumulables = array();
    
    /* CONSTRUCTOR */
    function __construct($type = 'csv'){
        $this->type = $type;
    }
    
    /* PRIVATE FUNCTIONS */
    
    static private function foramtearParaUTF($string){
        $arrAcentos1 = array("á","é","í","ó","ú","ñ","ç","ã","õ","ô","ê","â","ä","ë","ï","ö","ü","à","è","ì","ò","ú");
        $arrAcentos2 = array("Á","É","Í","Ó","Ú","Ñ","Ç","Ã","Õ","Ô","Ê","Â","Ä","Ë","Ï","Ö","Ü","À","È","Ì","Ò","Ù");
        return str_replace($arrAcentos1, $arrAcentos2, $string);
    }
    
    private function _addLineNumber(FPDF $pdf){
        $pdf->SetY(-12);            
        $pdf->Cell(0,10,'Page '.$pdf->PageNo().' / {nb}',0,0,'C');
    }
    
    private function _putPDF($fileName, $retornoObjeto){
        $pdf = new PDF_AutoPrint($this->_page_format, "mm");
        $pdf->setMargins($this->_margin_left, $this->_margin_top, $this->_margin_right);
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->AliasNbPages();
        $pdf->AddPage($this->_page_format, "A4");
        if ($this->_imgLogo <> '' && file_exists($this->_imgLogo)){
            $pdf->Image($this->_imgLogo, $this->_logo_x, $this->_logo_y, $this->_logo_width, $this->_logo_height);
            $pdf->Ln(16);
        }
        if (count($this->_info) > 0){
            foreach ($this->_info as $info){
                $txt = $this->rowFormat($info['txt'], STR_CASE_UCWORDS);
                $width = isset($info['width']) ? $info['width'] : 0;
                $fontSize = isset($info['size']) ? $info['size'] : $this->_pdf_font_size;
                $align = isset($info['align']) ? $info['align'] : "";
                $height = isset($info['height']) ? $info['height'] : "4";
                $pdf->SetFont("arial", "", $fontSize);
                $pdf->Cell($width, $height, $txt, 0, 0, $align);
                $pdf->Ln($height);
            }
        }
        if ($this->_report_title != ''){
            $pdf->setFont("arial", "B", "10");
            $pdf->Cell(0, 10, $this->rowFormat($this->_report_title, STR_CASE_UCWORDS), 0, 0, "C");
            $pdf->Ln();
        }        
        
        if (count($this->title) > 0){
            $pdf->SetFont("arial", "B", $this->_pdf_font_size);
            $widthDefaul = round(280 / count($this->title));
            foreach ($this->title as $key => $title){
                $width = isset($this->columnWidth[$key]) ? $this->columnWidth[$key] : $widthDefaul;
                $pdf->cell($width, $this->_content_height, $title, "TL");
            }
            $pdf->Cell(0, $this->_content_height, "", "L");
            $pdf->Ln();
        }
        $widthDefaul = round(280 / count($this->content));
        $nroLinea = 0;
        $arrAcumulables = array();
        foreach ($this->content as $linea){
            $nroLinea ++;
            $resaltar = in_array($nroLinea, $this->_body_resaltar) ? "B" : "";
            if ($resaltar){
                $pdf->SetFillColor(230, 230, 230);
            }
            $pdf->SetFont("arial", $resaltar, $this->_pdf_font_size);
            $maxColumnRow = 1;
            foreach ($linea as $key =>  $valor){                
                $lineaTemp = preg_split('/\n|\r\n?/', $valor);                
                if (count($lineaTemp) > $maxColumnRow){
                    $maxColumnRow = count($lineaTemp);
                }
            }
            foreach ($linea as $key =>  $valor){
                if (in_array($key, $this->_content_acumulables)){
                    if (!isset($arrAcumulables[$key])){
                        $arrAcumulables[$key] = $valor;
                    } else {
                        $arrAcumulables[$key] += $valor;
                    }
                }
                $border = "TLB";
                $width = isset($this->columnWidth[$key]) ? $this->columnWidth[$key] : $widthDefaul;
                if (strpos($valor, "\n")){
                    $lineaTemp = preg_split('/\n|\r\n?/', $valor);
                    if (count($lineaTemp) < $maxColumnRow){
                        for ($i = count($lineaTemp); $i <= $maxColumnRow; $i++){
                            $valor .= "\n";
                        }
                    }
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($width, $this->_content_height, $valor, $border, 'j', $resaltar);
                    $pdf->SetY($y);
                    $pdf->SetX($x + $width);
                } else {
                    $pdf->Cell($width, $this->_content_height * $maxColumnRow , $valor, $border, 0, '', $resaltar);
                }
                
            }
            $pdf->Cell(0, $this->_content_height * $maxColumnRow , "", "L", 0, '');
            $pdf->Ln();
            if ($this->_showLineNumber && $pdf->GetY() > 184){
                $this->_addLineNumber($pdf);
                $pdf->AddPage();
            }
        }
        
        if (count($arrAcumulables) > 0){
            $pdf->SetFont("arial", "B");
            reset($this->content);
            $first_key = key($this->content);
            foreach ($this->content[$first_key] as $key =>  $valor){
                $width = isset($this->columnWidth[$key]) ? $this->columnWidth[$key] : $widthDefaul;
                if (isset($arrAcumulables[$key])){
                    $texto = $arrAcumulables[$key];
                    $border = "RBL";
                } else {
                    $texto = '';
                    $border = 0;
                }
                $pdf->Cell($width, $this->_content_height * $maxColumnRow , $texto, $border);
            }
            $pdf->SetFont("arial", "");
            $pdf->Ln();
        }
        
        if ($this->_showLineNumber){
            $this->_addLineNumber($pdf);
        }
        if ($retornoObjeto){
            return $pdf;
        } else {
            $pdf->Output($fileName, "I");
        }
    }
    
    private function _putCVS($fileName){
        header("Content-Description: File Transfer");
        header("Content-Type: text/".$this->type);
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Transfer-Encoding: Binary");
        header("Expires:0");
        header("Cache-control:must-revalidate");
        header("Pragma:public");
        if (count($this->title) <> 0){
            echo implode($this->fieldSeparator, $this->title).$this->lineBreak;
        }
        
        foreach ($this->content as $linea){ 
            echo implode($this->fieldSeparator, $linea).$this->lineBreak;
        }
    }
    
    private function rowFormat($row, $stringFormatType){
        if ($this->fieldEncapsulant <> ''){
            $row = str_replace($this->fieldEncapsulant, '´', $row);
        }
        if ($this->fieldSeparator == ';'){
            $replacement = ',';
        } else if ($this->fieldSeparator == ','){
            $replacement = ';';
        } else {
            $replacement = '';
        }
        $row = str_replace($this->fieldSeparator, $replacement, $row);
        
        switch ($stringFormatType) {
            
            case STR_CASE_LOWER:
                $row = strtolower($row);
                break;
            
            case STR_CASE_UPPER:
                $row = strtoupper($row);
                if ($this->utfDecode){
                    $row = self::foramtearParaUTF($row);
                }
                break;
                
            case STR_CASE_UCWORDS:
                $row = ucwords(strtolower($row));
                break;
            
            case STR_CASE_UCFIRST:
                $row = ucfirst(strtolower($row));
                break;
            
            default:
                break;
        }
        if ($this->utfDecode){
            $row = utf8_decode($row);
        }
        $row = $this->fieldEncapsulant.$row.$this->fieldEncapsulant;
        return $row;
    }
        
    
    /* PUBLIC FUNCTIONS */
    
    public function setContentAcumulable(array $arrNroFiled){
        $this->_content_acumulables = $arrNroFiled;
    }
   
    public function setContentResaltar(array $arrRows){
        $this->_body_resaltar = $arrRows;
    }
    
    public function setPageFormat($format){
        $this->_page_format = $format;
    }
          
    
    public function setMargin($left = 8, $top = 4, $right = null){
        $this->_margin_left = $left;
        $this->_margin_right = $right;
        $this->_margin_top = $top;
    }
    
    public function setReportTitle($title){
        $this->_report_title = $title;
    }
    
    public function setContentHeight($height){
        $this->_content_height = $height;
    }
    
    /** Setea Informacion para el header del PDF
     * 
     * @param array $arrInfo  en formato array txt => '' width => '' align => ''
     */
    public function setInfo(array $arrInfo){
        $this->_info = $arrInfo;
    }
    
    public function setLogo($url, $x = 270, $y = 8, $width = 15, $height = 0){
        $this->_imgLogo = $url;
        $this->_logo_x = $x;
        $this->_logo_y = $y;
        $this->_logo_width = $width;
        $this->_logo_height = $height;
    }
    
    public function viewLineNumber($view){
        $this->_showLineNumber = $view;
    }
    
    public function setPDFFontSize($size){
        $this->_pdf_font_size = $size;
    }
    
    public function setColumnWidth(array $arrWidth){
        $this->columnWidth = $arrWidth;
    }
    
    public function utf8Mode($mode = true){
        $this->utfDecode = $mode;
    }
    
    public function setSeparadorDeCampo($separador = ';'){
        $this->fieldSeparator = trim($separador);
    }
    
    public function setEncapsuladorDeCampo($encapsulador = ''){
        $this->fieldEncapsulant = trim($encapsulador);
    }
    
    public function setSaltoDeLinea($salto = "\n"){
        $this->lineBreak = trim($salto);
    }
    
    public function setTitle(array $arrTitle, $stringFormatType = STR_CASE_UPPER){
        $this->title = array();
        $arrTemp = array();
        foreach ($arrTitle as $title){
            $arrTemp[] = $this->rowFormat($title, $stringFormatType);
        }
        $this->title = $arrTemp;
    }
    
    public function clear(){
        $this->title = array();
        $this->content = array();
    }
    
    public function setType($type){
        $this->type = $type;
    }
    
    public function setContent(array $arrContent, $stringFormatType = STR_CASE_NORMAL){
        $this->content = array();        
        $this->addContent($arrContent, $stringFormatType);
    }
    
    public function addContent($arrContent, $stringFormatType = STR_CASE_NORMAL){
        $ct = count($arrContent);
        foreach ($arrContent as $linea){
            $arrTemp = array();
            foreach ($linea as $row){
                $arrTemp[] = $this->rowFormat($row, $stringFormatType);
            }
            $this->content[$ct] = $arrTemp;
            $ct++;
        }
    }
    
    public function exportar($fileName = null, $retornoObjeto = false){
        if ($fileName == null) $fileName = date("YmdHis").".".$this->type;
        if ($this->type == "pdf"){
            return $this->_putPDF($fileName, $retornoObjeto);
        } else {
            $this->_putCVS($fileName);
        }
    }
}