<?php

class json_templates{
    private $document = array();    
    private $cantidadCeldas = 0;
    private $cantidadLineas = 0;
    private $oldFontFamily = 'arial';
    private $oldFontSize = 0;
    private $oldFontStyle = '';    
    private $FontFamily = 'arial';
    private $FontSize = 0;
    private $FontStyle = '';
    private $oldMarginLeft = 0;
    private $oldMarginTop = 0;
    private $oldMarginRight = null;
    private $MarginLeft = 0;
    private $MarginTop = 0;
    private $MarginRight = null;    
    private $textColorR = 0;
    private $oldTextColorR = 0;
    private $textColorG = 0;
    private $oldTextColorG = 0;
    private $textColorB = 0;
    private $oldTextColorB = 0;


    function __construct($orientation = "P", $unit = "mm", $format = "A4") {
        $this->document[0]['orientation'] = $orientation;
        $this->document[0]['unit'] = $unit;
        $this->document[0]['format'] = $format;
    }

    /**
     * Retorna el template construido
     * 
     * @return json
     */
    private function getJSON(){
        return json_encode($this->document[0]);
    }
    
    /**
     * Retorna el pdf object con los valores del template reemplazados
     * 
     * @param int $copias
     * @return \FPDF
     */
    private function getPDF($copias = 1){
        $obj =& get_instance();
        $filial = $obj->session->userdata['filial']['codigo'];


        $doc = $this->document;
        $pdf = new PDF_AutoPrint($doc[0]['orientation'], $doc[0]['unit'], $doc[0]['format']);        

        if ($doc[0]['format'] == "folio1") {
            $pdf->SetAutoPageBreak(false, -10);
        } else {
            $pdf->SetAutoPageBreak(true, -1);
        }



        foreach ($doc as $documents){
            for ($i = 0; $i < $copias; $i++){            
                foreach ($documents['pages'] as $page){
                    $orientation = $page['format']['orientation'];
                    $format = $page['format']['format'];
                    if (isset($page['margin'])){
                        $left = $page['margin']['left'];
                        $top = $page['margin']['top'];
                        $right = isset($page['right']) ? $page['right'] : null;
                        $pdf->SetMargins($left, $top, $right);
                    }
                    $pdf->AddPage($orientation, $format);
                    $j = 0;
                    //echo 'json template';
                    foreach ($page['content'] as $linea){

                        foreach ($linea as $celda){

                            if($filial == 16){
                                $j = $j + 1;
                                if($i >= 1 and $j == 1){
                                    continue;
                                }
                            }

                            if (isset($celda['margin'])){
                                $top = $celda['margin']['top'];
                                $left = $celda['margin']['left'];
                                $right = isset($celda['margin']['right']) ? $celda['margin']['right'] : null;
                                $pdf->SetMargins($left, $top, $right);
                            }
                            if (isset($celda['color'])){
                                $r = $celda['color']['r'];
                                $b = isset($celda['color']['b']) ? $celda['color']['b'] : null;
                                $g = isset($celda['color']['g']) ? $celda['color']['g'] : null;                        
                                $pdf->SetTextColor($r, $g, $b);
                            }
                            if (isset($celda['font'])){
                                $family = isset($celda['font']['family']) ? $celda['font']['family'] : 'arial';
                                $size = isset($celda['font']['size']) ? $celda['font']['size'] : 10;
                                $style = isset($celda['font']['style']) ? $celda['font']['style'] : '';
                                $pdf->SetFont($family, $style, $size);
                            }
                            if (isset($celda['Ln'])){
                                $heightLn = $celda['Ln'] == '' ? null : $celda['Ln'];
                                $pdf->Ln($heightLn);
                            } else {
                                $texto = isset($celda['txt']) ? $celda['txt'] : '';
                                $h = isset($celda['height']) ? $celda['height'] : 0;
                                $border = isset($celda['border']) ? $celda['border'] : 0;
                                $ln = isset($celda['ln']) ? $celda['ln'] : 0;
                                $align = isset($celda['align']) ? $celda['align'] : '';
                                $fill = isset($celda['fill']) ? $celda['fill'] : false;
                                $link = isset($celda['link']) ? $celda['link'] : '';
                                $pdf->Cell($celda['width'], $h, utf8_decode(html_entity_decode($texto)), $border, $ln, $align, $fill, $link);
                            }
                        }
                    }
                }
            }
        }
        return $pdf;
    }
    
    /**
     * Resetea el objeto
     * 
     * @return boolean
     */
    private function reset(){
        $this->document = array();    
        $this->cantidadCeldas = 0;
        $this->cantidadLineas = 0;
        $this->oldFontFamily = 'arial';
        $this->oldFontSize = 0;
        $this->oldFontStyle = '';    
        $this->FontFamily = 'arial';
        $this->FontSize = 0;
        $this->FontStyle = '';
        $this->oldMarginLeft = 0;
        $this->oldMarginTop = 0;
        $this->oldMarginRight = null;
        $this->MarginLeft = 0;
        $this->MarginTop = 0;
        $this->MarginRight = null;        
        $this->textColorR = 0;
        $this->oldTextColorR = 0;
        $this->textColorG = 0;
        $this->oldTextColorG = 0;
        $this->textColorB = 0;
        $this->oldTextColorB = 0;        
    }
    
    /**
     * Setea un json de templates al objeto
     * 
     * @param type $json
     * @return boolean
     */
    public function setJSON($json){        
        $this->reset();
        if (is_array($json)){
            foreach ($json as $document){
                $this->document[] = json_decode($document, true);
            }
        } else {
            $this->document[0] = json_decode($json, true);
        }
        if (isset($this->document[0]['pages'])){
            $maxPage = count($this->document[0]['pages']) - 1;
            $this->cantidadCeldas = isset($this->document[0]['pages'][$maxPage]['content']) 
                    ? count($this->document[0]['pages'][$maxPage]['content']) - 1
                    : 0;
            $this->cantidadLineas = isset($this->document[0]['pages'][$maxPage]['content'][$this->cantidadCeldas])
                    ? count($this->document[0]['pages'][$maxPage]['content'][$this->cantidadCeldas])
                    : 0;
        }
        return true;
    }
    
    /**
     * Setea margenes del template
     * 
     * @param int $left
     * @param int $top
     * @param int $right
     */
    public function SetMargins($left, $top, $right = null){
        $this->MarginLeft = $left;
        $this->MarginTop = $top;
        $this->MarginRight = $right;
    }
    
    /**
     * Agrega una nueva pagina al template
     * 
     * @param string $orientation
     * @param string $format
     */
    public function AddPage($orientation = '', $format = ''){
        $ct = isset($this->document[0]['pages']) ? count($this->document[0]['pages']) : 0;
        $this->document[0]['pages'][$ct]['format']['orientation'] = $orientation;
        $this->document[0]['pages'][$ct]['format']['format'] = $format;
        if ($this->MarginTop != $this->oldMarginTop || $this->MarginLeft != $this->oldMarginLeft || $this->MarginRight !== $this->oldMarginRight){
            $this->oldMarginTop = $this->MarginTop;
            $this->oldMarginLeft = $this->MarginLeft;
            $this->oldMarginRight = $this->MarginRight;
            $this->document[0]['pages'][$ct]['margin']['top'] = $this->MarginTop;
            $this->document[0]['pages'][$ct]['margin']['left'] = $this->MarginLeft;
            if ($this->MarginRight !== null)
                $this->document[0]['pages'][$ct]['margin']['right'] = $this->MarginRight;
        }
        $this->cantidadCeldas = 0;
        $this->cantidadLineas = 0;
    }
    
    /**
     * Imprime una celda en el template
     * 
     * @param int $w
     * @param int $h
     * @param string $txt
     * @param string $border
     * @param int $ln
     * @param string $align
     * @param boolean $fill
     * @param strinf $link
     */
    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = ''){
        $cp = isset($this->document[0]['pages']) ? count($this->document[0]['pages']) -1 : 0;
        $cc = $this->cantidadCeldas;
        $cl = $this->cantidadLineas;
        if ($this->oldFontFamily <> $this->FontFamily || $this->oldFontSize <> $this->FontSize || $this->oldFontStyle <> $this->FontStyle){
            $this->oldFontFamily = $this->FontFamily;
            $this->oldFontSize = $this->FontSize;
            $this->oldFontStyle = $this->FontStyle;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['font']['family'] = $this->FontFamily;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['font']['style'] = $this->FontStyle;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['font']['size'] = $this->FontSize;
        }
        
        if ($this->textColorB <> $this->oldTextColorB || $this->textColorG <> $this->oldTextColorG || $this->textColorR <> $this->oldTextColorR){
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['color']['r'] = $this->textColorR;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['color']['b'] = $this->textColorB;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['color']['g'] = $this->textColorG;
            $this->oldTextColorB = $this->textColorB;
            $this->oldTextColorG = $this->textColorG;
            $this->oldTextColorR = $this->textColorR;            
        }
        
        if ($this->MarginTop != $this->oldMarginTop || $this->MarginLeft != $this->oldMarginLeft || $this->MarginRight !== $this->oldMarginRight){
            $this->oldMarginTop = $this->MarginTop;
            $this->oldMarginLeft = $this->MarginLeft;
            $this->oldMarginRight = $this->MarginRight;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['margin']['top'] = $this->MarginTop;
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['margin']['left'] = $this->MarginLeft;
            if ($this->MarginRight !== null)
                $this->document[0]['pages'][$cp]['content'][$cl][$cc]['margin']['right'] = $this->MarginRight;
        }
        $this->document[0]['pages'][$cp]['content'][$cl][$cc]['width'] = $w;
        
        if ($h <> 0)
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['height'] = $h;
        if ($txt <> '')
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['txt'] = $txt;
        if ($border <> "0")
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['border'] = $border;        
        if ($ln <> 0)
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['ln'] = $ln;
        if ($align <> '')
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['align'] = $align;
        if ($fill <> '')
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['fill'] = $fill;
        if ($link <> '')
            $this->document[0]['pages'][$cp]['content'][$cl][$cc]['link'] = $link;
        $this->cantidadCeldas ++;
    }      
    
    /**
     * Genera un salto de linea en el template
     * 
     * @param int $height
     */
    public function Ln($height = null){
        $cp = isset($this->document[0]['pages']) ? count($this->document[0]['pages']) - 1 : 0;
        $cc = $this->cantidadCeldas;
        $cl = $this->cantidadLineas;
        $this->document[0]['pages'][$cp]['content'][$cl][$cc]['Ln'] = $height == null ? '' : $height;
        $this->cantidadCeldas = 0;
        $this->cantidadLineas ++;        
    }
    
    /**
     * Setea la tipografia para utilizar en las celdas siguientes
     * 
     * @param string $family
     * @param string $style
     * @param int $size
     */
    public function SetFont($family = 'arial', $style = '', $size = 0){
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSize = $size;
    }
    
    /**
     * setea el color de fuente segun sus valores RGB
     * 
     * @param type $r
     * @param type $g
     * @param type $b
     */
    public function SetTextColor($r, $g = null, $b = null){
        $this->textColorR = $r;
        if ($g !== '') $this->textColorG = $g;
        if ($b !== '') $this->textColorB = $b;
    }
    
    /**
     * Retorna el template segun el formato especificado (json, pdf, etc.)
     * 
     * @param string $format
     * @param int $copias
     * @return mixed
     */
    public function Output($format = 'json', $copias = 1){        
        $retorno = '';
        switch ($format) {
            case "json":
                $retorno = $this->getJSON();
                break;

            case "pdf":
                $retorno = $this->getPDF($copias);
                break;
            
            default:
                break;
        }
        return $retorno;
    }    
}
