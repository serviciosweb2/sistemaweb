<?php

function formatearStringCelda($materia,$pdf,$cortarCada){
    $mostrarDetalle = isset($materia['descripcion']) ? explode(" ",$materia['descripcion']) : '';     
    $salida[0]='';     
    $retorno='';
    $x  = 0;
    $tr = 0;
    if (is_array($mostrarDetalle)){
        foreach($mostrarDetalle as $valor){              
            $aConcatenar = strlen($valor);              
            if( $aConcatenar+1 <= $cortarCada  &&  ( $x + $aConcatenar ) <= $cortarCada){
                $retorno.=$valor.' ';                        
                $salida[$tr].=$valor.' ';                        
            } else {    
                $tr++;                          
                $salida[$tr] = '';                        
                $retorno = '';                        
                $retorno .= $valor;                        
                $salida[$tr] .= $valor;
            }            
            $x = strlen($retorno);        
        }         
        foreach($salida as $row=>$renglon){
            $pdf->Cell(90, 6, $row!=1 ? utf8_decode($materia['nombre_'.  get_idioma()]) : "",'LTRB');
            $pdf->Cell(25, 6, $row!=1 ? lang($materia['estado']):"",'LTRB');
            $pdf->Cell(60, 6, utf8_decode($renglon),'LTRB');
         }
         
    } else {         
        $pdf->Cell(90, 6, utf8_decode($materia['nombre_'.  get_idioma()]),'LTRB');
        $pdf->Cell(25, 6, utf8_decode(lang($materia['estado'])),'LTRB');
        $pdf->Cell(60, 6, "",'LTRB');         
    }
}
    
function contarLineasString($string,$cantCaracteres){
    $arrTemp = explode(" ", $string);
    $arrLineas = array();
    $i = 0;
    while ($i < count($arrTemp)){
        $str = $arrTemp[$i];
        while (isset($arrTemp[$i + 1]) && strlen($str.$arrTemp[$i + 1]) < $cantCaracteres){
            $str .= " ".$arrTemp[$i + 1];
            $i++;
        }
        $arrLineas[] = $str;
        $i++;
    }
    return $arrLineas;
}
    
function cortarString($string, $cantCaracteres){
    $arrString = explode(" ",$string);
    $retorno ='';
    foreach($arrString as $row){
        if(strlen($retorno)<$cantCaracteres){
            $retorno .= $row.' ';
        }
    }
    return $retorno;
}
