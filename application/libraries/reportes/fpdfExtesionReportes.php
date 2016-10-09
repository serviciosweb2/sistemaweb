<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class PDFReportes extends PDF_AutoPrint
{
function Footer($apellido_nombre='')
{
    $this->SetX(10);
    $this->Cell(10, 6, utf8_decode(lang("fecha_emision"))." ".formatearFecha_pais(date("Y-m-d")), 0, 0, "L");
   $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
   //$this->Cell(0,6,'aaaaa', 0,0,"R");
  
//   if($apellido_nombre != ''){
      
       $this->Cell(0,6, $apellido_nombre, 0,0,"R");
  // }
   
}
}
?>
