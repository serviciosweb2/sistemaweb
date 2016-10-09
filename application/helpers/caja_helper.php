<?php
    function formatearConceptoCaja(CI_DB_mysqli_driver $conexion, &$arrCaja){ // echo "<pre>"; print_r($arrCaja); echo "</pre>"; die();
        $ci = & get_instance();
        $ci->load->helper('filial');
        $session = $ci->session->userdata('filial');
        $moneda = $session['moneda'];
        for ($i = 0; $i < count($arrCaja); $i++){
            if (isset($arrCaja[$i]['fecha_hora'])) $arrCaja[$i]['fecha_hora_real_formateado'] = formatearFecha_pais($arrCaja[$i]["fecha_hora"], true);
            if (isset($arrCaja[$i]['debe'])) $arrCaja[$i]['debe_formateado'] = formatearImporte($arrCaja[$i]['debe'], false);
            if (isset($arrCaja[$i]['haber'])) $arrCaja[$i]['haber_formateado'] = formatearImporte($arrCaja[$i]['haber'], false);
            if (isset($arrCaja[$i]['saldo'])) $arrCaja[$i]['saldo_formateado'] = formatearImporte($arrCaja[$i]['saldo'], false);
            if (isset($arrCaja[$i]['saldo_apertura'])) $arrCaja[$i]['saldo_apertura'] = formatearImporte($arrCaja[$i]['saldo_apertura'], false);
            $arrCaja[$i]['simbolo_moneda'] = $moneda['simbolo'];
            if (isset($arrCaja[$i]['cod_user'])){
                $myUsuario = new Vusuarios_sistema($conexion, $arrCaja[$i]['cod_user']);
                $arrCaja[$i]['nombre_usuario'] = $myUsuario->nombre." ".$myUsuario->apellido;
            }
            if (isset($arrCaja[$i]['nombre_medio'])) $arrCaja[$i]['nombre_medio'] = lang($arrCaja[$i]['nombre_medio']);
            if (isset($arrCaja[$i]['cod_concepto'])){
                $descripcion = Vmovimientos_caja::getDescripcion($conexion, $arrCaja[$i]['cod_concepto'], $arrCaja[$i]['concepto']);
                if (trim($arrCaja[$i]['observacion'] <> '')){
                    $descripcion .= "({$arrCaja[$i]['observacion']})";
                }                        
                $arrCaja[$i]['descripcion'] = $descripcion;
            }
            if (isset($arrCaja[$i]['medio'])) $arrCaja[$i]['nombre_medio'] = lang($arrCaja[$i]['medio']);
            if (isset($arrCaja[$i]['saldo_concepto'])) $arrCaja[$i]['saldo_concepto_formateado'] = formatearImporte($arrCaja[$i]['saldo_concepto'], false);
            
        }
    }