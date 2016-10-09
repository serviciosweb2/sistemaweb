<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of func_fechas
 *
 * @author SOPORTE
 */
class func_fechas {

    static function diferenciaEntreFechas($fecha_principal, $fecha_secundaria, $obtener = 'DIAS', $redondear = true) {
        $f0 = strtotime($fecha_principal);
        $f1 = strtotime($fecha_secundaria);
        

        
        if ($f0 < $f1) {
            $tmp = $f1;
            $f1 = $f0;
            $f0 = $tmp;
        }
        $resultado = ($f0 - $f1);
        switch ($obtener) {
            default: break;
            case "MINUTOS" : $resultado = $resultado / 60;
                break;
            case "HORAS" : $resultado = $resultado / 60 / 60;
                break;
            case "DIAS" : $resultado = $resultado / 60 / 60 / 24;
                break;
            case "SEMANAS" : $resultado = $resultado / 60 / 60 / 24 / 7;
                break;
        }
        if ($redondear)
            $resultado = round($resultado);
        return $resultado;
    }

}
