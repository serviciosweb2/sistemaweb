<?php

function formatiarReferencia($referencia){
//    echo '<pre>'; 
//    
//     print_r($referencia);
//    
//    echo '</pre>';
    $string='';
    
    switch($referencia[0]['cod_medio']){
        
        case 1:
        
            $string.=$referencia[0]['medio'];
            $string.= ' ('.$referencia[0]['nombreCaja'].')';
            $string.= '   de '.$referencia[0]['valor'];
            break;
        
        case 3:
            
            $string.=$referencia[0]['medio'];
            $string.= '   ('.$referencia[0]['nombreBanco'].'-'.$referencia[0]['nombreTarj'].')';
           
            $string.= '  de '.$referencia[0]['valor'];
            break;
        
        case 4:
            
            
            $string.=$referencia[0]['medio'];
            //$string.= '   Fecha cobro:'.$referencia[0]['fecha_cobro'];
            $string.= '('.$referencia[0]['nro_cheque'].') ';
            $string.= $referencia[0]['nombre_cheque'][0]['nombre'];
            //$string.= '   Emisor:'.$referencia[0]['emisor'];
            //$string.= '   Nombre caja:'.$referencia[0]['nombreCaja'];
            $string.= '   de '.$referencia[0]['valor'];
            break;
        
        
        case 5:
            
            
            $string.=$referencia[0]['medio'];
            //$string.= '   Fecha cobro:'.$referencia[0]['fecha_cobro'];
            //$string.= '('.$referencia[0]['nro_cheque'].') ';
            //$string.= $referencia[0]['tipo_cheque'];
            //$string.= '   Emisor:'.$referencia[0]['emisor'];
            //$string.= '   Nombre caja:'.$referencia[0]['nombreCaja'];
            $string.= '   de '.$referencia[0]['valor'];
            break;
        
        
        case 6:
            
            
            $string.=$referencia[0]['medio'];
            //$string.= '   Fecha cobro:'.$referencia[0]['fecha_cobro'];
            //$string.= '('.$referencia[0]['nro_cheque'].') ';
            //$string.= $referencia[0]['tipo_cheque'];
            //$string.= '   Emisor:'.$referencia[0]['emisor'];
            $string.= ' ('.$referencia[0]['nro_transaccion'].') ';
             $string.= ' ('.$referencia[0]['nombre'].') ';
            $string.= '   de '.$referencia[0]['valor'];
            break;
        
        
        case 7:
            
            
            $string.=$referencia[0]['medio'];
            //$string.= '   Fecha cobro:'.$referencia[0]['fecha_cobro'];
            //$string.= '('.$referencia[0]['nro_cheque'].') ';
            //$string.= $referencia[0]['tipo_cheque'];
            //$string.= '   Emisor:'.$referencia[0]['emisor'];
            $string.= ' ('.$referencia[0]['nombre'].') ';
            $string.= '   de '.$referencia[0]['valor'];
            break;
            
    }
    
    
    return $string;
}


?>

