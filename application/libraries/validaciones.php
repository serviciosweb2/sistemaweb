<?php

/**
 * Clase Validaciones
 * @author Aquiles gonazales <sistemas1@iga-la.net>
 * @author Ivan berthillod <ivan.sys@gmail.com>
 * @package  SistemaIGA
 * @subpackage Validaciones
 * @version  $Revision: 1.1 $
 * @access   public
 */
class validaciones {
    /* CODIGO POSTAL */

    /**
     * Valida codigo postal segun el pais
     * @access static
     * @param String $codigopostal codigo postal
     * @param String $pais pais para validar el formato
     * @param String $retornoMensaje mensaje que retorna si falla
     * @return boolean
     */
    static function validarCodigoPostal($codigopostal, $pais, &$retornoMensaje = null) {
        $er = '';
        switch ($pais) {
            case "1": //argentina
                $er = "/^[A-Z]{1}[0-9]{4}[A-Z]{3}$/";
                $formatoCorrecto = "A9999AAA";
                break;

            case "2": //brasil
                $er = "/^[0-9]{5}-[0-9]{3}$/";
                $formatoCorrecto = "99999-999";
                break;

            case "3": //uruguay
                $er = "/^[0-9]{5}$/";
                $formatoCorrecto = "99999";
                break;

            case "4": // paraguay
                $er = "/^[0-9]{4}$/";
                $formatoCorrecto = "9999";
                break;

            case "5": // venezuela
                $er = "/^[0-9]{5}$/";
                $formatoCorrecto = "99999";
                break;

            case "6": //bolivia
                return true;
                break;

            case "7": //chile
                $er = "/^[0-9]{7}$/";
                $formatoCorrecto = "9999999";
                break;

            case "8": //colombia
                $er = "/^[0-9]{6}$/";
                $formatoCorrecto = "999999";
                break;

            case "9": //panama
                return true;
                break;

            case "10": // estados unidos
                $er = "/^[0-9]{5}-[0-9]{4}$/";
                $formatoCorrecto = "99999-0000";
                break;

            default:
                $retornoMensaje = lang('reglas_validacion_no_definidas') . $pais;
                return false;
                break;
        }
        $retorno = preg_match($er, $codigopostal);
        if (!$retorno) {
            $retornoMensaje = lang('el_codigo_postal') . $codigopostal . lang('es_incorrecto_formato_esperado') . $formatoCorrecto . ')';
            return false;
        } else {
            return true;
        }
    }

    /* DOCUMENTOS DE IDENTIDAD */

    /**
     * Valida documento que no esta asignado 
     * Argentina, Venezuela, Colombia, Paraguay, Bolivia
     * @access private
     * @param String $numeroDocumento documento de identidad
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     */
    static private function validarDocumentoComun($numeroDocumento, &$formatoEsperado = null) {
        $er = "/^\d+$/";
        $formatoEsperado = lang('solo_numeros_sin_rango_definido');
        return preg_match($er, $numeroDocumento);
    }

    /**
     * Valida documento pais panama
     * Formato esperado (nn-nn-nn o PE-nn-nn o E-nn-nn o N-nn-nn)
     * @link http://es.wikipedia.org/wiki/Documento_de_identidad#Panam.C3.A1 segun especificacion
     * @access private
     * @param String $numeroDocumento documento de identidad
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     */
    static private function validarDocumentoPanama($numeroDocumento, &$formatoEsperado = null) {

        $re1 = "/^\d+-\d+-\d+$/";
        $re2 = "/^(PE|N|E)-\d+-\d+$/";
        $formatoEsperado = "nn-nn-nn o PE-nn-nn o E-nn-nn o N-nn-nn";
        return preg_match($re1, $numeroDocumento) || preg_match($re2, $numeroDocumento);
    }

    /**
     * Valida numero de seguro social estados unidos
     * Formato esperado (nnn-nn-nnnn) 
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @uses ee.uu
     * @access private
     * @param String $numeroSocial numero de seguro social
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     */
    static private function validarNumeroSeguroSocialUSA($numeroSocial, &$formatoEsperado = null) {
        $re = "\b(?!000)(?!666)(?!9)[0-9]{3}[ -]?(?!00)[0-9]{2}[ -]?(?!0000)[0-9]{4}\b";
        $formatoEsperado = "nnn-nn-nnnn"; // no emopezar con 000, 666, 900 al 999, no contener 00 en el medio ni 0000 al final
        return preg_match($re, $numeroSocial);
    }

    /**
     * Valida documento Uruguguay
     * Formato esperado (1234567-8)
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @uses Uruguay
     * @access private
     * @param String $numeroDocumento documento de identidad
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     */
    static private function validarDocumentoUruguay($numeroDocumento, &$formatoEsperado = null) {
        $er = "/^[0-9]{7}-[0-9]{1}$/"; // debe respetar el formato 1234567-8
        if (!preg_match($er, $numeroDocumento)) {
            $formatoEsperado = "1234567-8";
            return false;
        } else {
            $arrPatron = array(2, 9, 8, 7, 6, 3, 4);
            $suma = 0;
            $arrTemp = explode("-", $numeroDocumento);
            $numero = $arrTemp[0];
            $digitoVerificador = $arrTemp[1];
            for ($i = 0; $i < strlen($numero); $i++) {
                $dg = substr($numero, $i, 1);
                $mult = $dg * $arrPatron[$i];
                $mult = substr($mult, -1, 1);
                $suma += $mult;
            }
            $digitoCalculado = (10 - ($suma % 10));
            $formatoEsperado = lang('digito_validador_invalido');
            return $digitoCalculado == $digitoVerificador;
        }
    }

    /**
     * Valida CPF
     * Formato esperado (nnnnnnnnnnn)
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @uses Brasil
     * @access private
     * @param String $cpf documento 
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     */
    static private function validarCPF($cpf, &$formatoEsperado = null) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            $formatoEsperado = "nnnnnnnnnnn";
            return false;
        } else {
            $formatoEsperado = lang('validacion_cpf_invalida');
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * funcion que llama a demas funciones privadas de validar
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @access static
     * @param String $numeroDocumento documento de identidad
     * @param  Integer 	$codigoTipoDocumento tipo de documento< 
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     * 
     */
    static private function validarDniArgentina($numeroDni, &$formatoEsperado = null) {
        $patron = "/^[0-9]{7,9}$/";
        if (preg_match($patron, $numeroDni)) {
            return true;
        } else {
            $formatoEsperado = '123456789';
            return false;
        }
    }

    /**
     * funcion que llama a demas funciones privadas de validar
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @access static
     * @param String $numeroDocumento documento de identidad
     * @param  Integer 	$codigoTipoDocumento tipo de documento<
     * @param String $formatoEsperado formato por referencia opcional
     * @return boolean
     *
     */
    static private function validarRuc($numeroDni, &$formatoEsperado = null) {
        $patron = '(^\d+-\d+$)';
        if (preg_match($patron, $numeroDni)) {
            return true;
        } else {
            $formatoEsperado = 'debe contener un "-"';
            return false;
        }
    }

//No esta en uso actualmente.
//    static private function validarPassArgentina($numero, &$formatoEsperado = null) {
//        $patron = "/^[0-9]{7,8}[A-Za-z]{1,3}$/";
//        $patron1 = "/^[A-Za-z]{1,3}[0-9]{7,8}$/";
//        if (preg_match($patron1, $numero) || preg_match($patron, $numero)) {
//            return true;
//        } else {
//            $formatoEsperado = '12345678N';
//            return false;
//        }
//    }

    static function validarDocumentoIdentidad($numeroDocumento, $codigoTipoDocumento, &$formatoEsperado = null) {
        switch ($codigoTipoDocumento) {
            case "1":   // DNI Argentina
            case "8":
                $respuesta = validaciones::validarDniArgentina($numeroDocumento, $formatoEsperado);
                break;
            case "2":   // Pasaporte Argentina
                $respuesta = true;
//                $respuesta = validaciones::validarPassArgentina($numeroDocumento, $formatoEsperado);
                break;
            case "3":   // cuit ARGENTINA
                $respuesta = validaciones::validarCUITCUIL($numeroDocumento, $formatoEsperado);
                break;
            case "4":   //CUIL ARGENTINA
                $respuesta = validaciones::validarCUITCUIL($numeroDocumento, $formatoEsperado);
                break;
            case "5":   // RG brasil
                $respuesta = true;
                break;
//            case "5":   // Bolivia
//                $respuesta = validaciones::validarDocumentoComun($numeroDocumento, $formatoEsperado);
//                break;
            case "11":   // panama
                $respuesta = validaciones::validarDocumentoPanama($numeroDocumento, $formatoEsperado);
                break;
            case "9":
                $respuesta = validaciones::validarDocumentoUruguay($numeroDocumento, $formatoEsperado);
                break;
            case "21":   // CPF brasil
                $respuesta = validaciones::validarCPF($numeroDocumento, $formatoEsperado);
                break;
            case '23': //RUC
                $respuesta = validaciones::validarRuc($numeroDocumento, $formatoEsperado);
                break;
            default:
                $respuesta = true;
//                $formatoEsperado = lang('tipo_documento_no_implementado');
                break;
        }
        return $respuesta;
    }

    /* VALIDACION DE RAZONES SOCIALES (cuit, cpf) */

    /**
     * Validar CNPJ
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @uses Brasil
     * @access private
     * @param String $cnpj documento 
     * @return boolean
     */
    static private function validarCNPJ($cnpj) {
        $cnpj = str_replace("/", "", str_replace("-", "", str_replace(".", "", $cnpj)));
        $cnpj = trim($cnpj);
        if (empty($cnpj) || strlen($cnpj) != 14) {
            return false;
        } else {
            if (validaciones::check_fake($cnpj, 14)) {
                return false;
            } else {
                $rev_cnpj = strrev(substr($cnpj, 0, 12));
                for ($i = 0; $i <= 11; $i++) {
                    $i == 0 ? $multiplier = 2 : $multiplier;
                    $i == 8 ? $multiplier = 2 : $multiplier;
                    $multiply = ($rev_cnpj[$i] * $multiplier);
                    $sum = $sum + $multiply;
                    $multiplier++;
                }
                $rest = $sum % 11;
                if ($rest == 0 || $rest == 1)
                    $dv1 = 0;
                else
                    $dv1 = 11 - $rest;

                $sub_cnpj = substr($cnpj, 0, 12);
                $rev_cnpj = strrev($sub_cnpj . $dv1);
                unset($sum);
                for ($i = 0; $i <= 12; $i++) {
                    $i == 0 ? $multiplier = 2 : $multiplier;
                    $i == 8 ? $multiplier = 2 : $multiplier;
                    $multiply = ($rev_cnpj[$i] * $multiplier);
                    $sum = $sum + $multiply;
                    $multiplier++;
                }
                $rest = $sum % 11;
                if ($rest == 0 || $rest == 1)
                    $dv2 = 0;
                else
                    $dv2 = 11 - $rest;
                if ($dv1 == $cnpj[12] && $dv2 == $cnpj[13])
                    return true;
                else
                    return false;
            }
        }
    }

    /**
     * Validar ayuda CNPJ
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @uses Brasil
     * @access private
     * @param String $cnpj documento 
     * @return boolean
     */
    static private function check_fake($string, $length) {
        for ($i = 0; $i <= 9; $i++) {
            $fake = str_pad("", $length, $i);
            if ($string === $fake)
                return(1);
        }
    }

    /* VALIDACION DE RAZONES SOCIALES (cuit, cpf) */

    /**
     * Validar CNPJ
     * Formato esperado
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @uses Brasil
     * @access private
     * @param String $cnpj documento 
     * @return boolean
     */
    static private function validarCUITCUIL($cuit, &$formatoEsperado = null) {
        if (!strpos($cuit, "-") && strlen($cuit) == 11) {        // las mascaras de los input quitan el caracter '-'
            $cuit = substr($cuit, 0, 2) . "-" . substr($cuit, 2, 8) . "-" . substr($cuit, 10, 1);
        }
        $er = "/^[0-9]{2}-[0-9]{8}-[0-9]{1}$/";
        if (!preg_match($er, $cuit)) {
            $formatoEsperado = "12-12345678-1";
            return false;
        } else {
            $esCuit = false;
            $cuit_rearmado = "";
            for ($i = 0; $i < strlen($cuit); $i++) {
                if ((Ord(substr($cuit, $i, 1)) >= 48) && (Ord(substr($cuit, $i, 1)) <= 57)) {
                    $cuit_rearmado = $cuit_rearmado . substr($cuit, $i, 1);
                }
            }
            $cuit = $cuit_rearmado;
            if (strlen($cuit_rearmado) <> 11) {
                $formatoEsperado = "12-12345678-1";
                return false;
            } else {
                $x = $i = $dv = 0;
                $vec[0] = (substr($cuit, 0, 1)) * 5;
                $vec[1] = (substr($cuit, 1, 1)) * 4;
                $vec[2] = (substr($cuit, 2, 1)) * 3;
                $vec[3] = (substr($cuit, 3, 1)) * 2;
                $vec[4] = (substr($cuit, 4, 1)) * 7;
                $vec[5] = (substr($cuit, 5, 1)) * 6;
                $vec[6] = (substr($cuit, 6, 1)) * 5;
                $vec[7] = (substr($cuit, 7, 1)) * 4;
                $vec[8] = (substr($cuit, 8, 1)) * 3;
                $vec[9] = (substr($cuit, 9, 1)) * 2;
                for ($i = 0; $i <= 9; $i++) {
                    $x += $vec[$i];
                }
                $dv = (11 - ($x % 11)) % 11;
                if ($dv == (substr($cuit, 10, 1))) {
                    $esCuit = true;
                }
            }
            return($esCuit);
        }
    }

    /**
     * Validar Numero de razon social
     * @author Aquiles gonazales <sistemas1@iga-la.net>
     * @access static
     * @param String $numeroRazonSocial documento 
     * @param String $tipoRazonSocial Tipo razon social 
     * @return boolean
     */
    static function validarNumeroRazonSocial($numeroRazonSocial, $tipoRazonSocial) {
        switch ($tipoRazonSocial) {
            case "1": // cuit
                $retorno = validaciones::validarCUIT($numeroRazonSocial);
                break;
            case "2": // cpf
                $retorno = validaciones::validarCNPJ($numeroRazonSocial);
            default:
                $retorno = false;
                break;
        }
        return $retorno;
    }

    /**
     * Validar codigo cupon y documento del alumno
     * @author Vane
     * @access static
     * @param String $conexion
     * @param String $codcupon 
     * @param String $documento
     * @param String $retorno mensaje
     * @return boolean
     */
     static function validaFechaCupon($fecha){//esta función compara los dias , pasa fecha x parametro
        $fechahoy = date("Y-m-d H:i:s");//lee dia hoy
        $fechaSegundos = strtotime($fechahoy);//convierte en segundos 
        $fecha = strtotime($fecha);//convierte en segundos la fecha x parametro
        $fecha = $fecha + (86400 * 3);//segundos por 3
        if ($fechaSegundos <= $fecha){//compara que no pase los tres dias
            return true;//que siga su curso,
        } else {
            return false;
        }
       
    }
    static function validarCupon($conexion, $codcupon, $documento, &$retornoMensaje = null, $conexiongral = null) {
        $arrCupon = Vcupones::listarCupones($conexion, array("codigo" => $codcupon));
        if (count($arrCupon) > 0) {
            if ($arrCupon[0]['documento'] == $documento) {//coincide cupon con documento?
                $idCupon = $arrCupon[0]['id'];
                $conexion->select("*");
                $conexion->from("cupones_canje");
                $conexion->where("codigo_cupon", $idCupon);//selecciona los cupones canje generados iguales al codigo cupon
                $query = $conexion->get();//levanta la consulta
                /*echo $conexion->last_query();*/
                $arrTemp = $query->result_array();
                echo "paso 0";
                if (count($arrTemp) > 0) {
                    $retornoMensaje = lang('cupon_ya_asignado');//
                    echo "paso 1";
                    return false;
                    
                    
                } else if (self::validaFechaCupon($arrCupon[0]['fecha'])){//llama a la funcion valida
                    echo "paso2";
                    return true;
                } else {
                    $retornoMensaje = lang('cupon_ya_vencido');
                    return false;
                }
                

            } else {
                $retornoMensaje = lang('documento_no_corresponde_con_cupon');//no coincide dni
                return false;
            }
         
        } else {
            $retornoMensaje = lang('codigo_cupon_invalido');//da invalido cuando no coincide
            return false;
        }
    }
    
    static function validarFilialCupon($codcupon, $filialalumno, &$retornoMensaje = null, $conexiongral = null) {
        $condiciones = array(
            "md5" => $codcupon
        );
        $cupones = Vcupones::listarCupones($conexiongral, $condiciones);//filial
        if (count($cupones) > 0 && $codcupon != "") {
            $cupon = $cupones[0];
            $filialcupon = $cupon['id_filial'];
            if ($filialcupon == $filialalumno) {
                return true;
            } else {
                $retornoMensaje = lang('cupon_ya_asignado_filial');
                return false;
            }
        } else {
            return false;
        }
    }

    static private function validarCodigoCupon($codcupon, $conexiongral, &$cupon) {
        $condiciones = array(
            "md5" => $codcupon
        );
        $cupones = Vcupones::listarCupones($conexiongral, $condiciones);
        if (count($cupones) == 0) {
            return false;
        } else {
            $cupon = $cupones[0];
            return true;
        }
    }

    static private function validarDocumentoCupon($cupon, $docalumno) {//coincide dni ingresado con dni datos alumno
        $doccupon = $cupon['documento'];
        if ($doccupon == $docalumno) {
            return true;
        } else {
            return false;
        }
    }

    static private function validarEstadoCupon($cupon) {//si esta pendiente
        if ($cupon['estado'] == 'pendiente') {
            return true;
        } else {
            return false;
        }
    }

    static function validarTotalFacturarCobrar($conexion, $total, $cod_cta) {
        $totImpor = Vctacte::getSumImporteFacturarCobrar($conexion, $cod_cta);
        if ($total <= $totImpor) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function validarTotalCobrar($conexion, $total, $cod_cta) {
        $saldoCobrar = Vctacte::getSumImporteCobrar($conexion, $cod_cta);
        if ($total <= $saldoCobrar) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function validarTotalNotacredito($conexion, $total, $cod_cta) {
        $saldoCobrar = Vctacte::totalNotaCredito($conexion, $cod_cta);
        if ($total <= $saldoCobrar) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function validarCambioEstadoFactura($conexion, $cod_factura, &$mensaje = null) {
        $objFactura = new Vfacturas($conexion, $cod_factura);
        if ($objFactura->anulada == 0) {//anular factura
            $fechaFactura = $objFactura->fecha;
            $mes = explode("-", $fechaFactura);
            if ($mes[1] >= date('m')) {
                return TRUE;
            } else {
                $mensaje = lang('deshabilitar_facturas_anteriores');
                return FALSE;
            }
        } else {
            $condiciones = array('cod_factura' => $cod_factura);
            $renglones = Vfacturas_renglones::listarFacturas_renglones($conexion, $condiciones);
            foreach ($renglones as $renglon) {
                $condicionCtaCte = array(
                    'cod_ctacte' => $renglon['cod_ctacte'],
                    'anulada' => 0);
                $renglonesCtaCte = Vfacturas_renglones::listarFacturas_renglones($conexion, $condicionCtaCte);
                if (count($renglonesCtaCte) > 1) {
                    $mensaje = lang('rehabilitarse_factura_ctacte_refacturada');
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    static function validarMostrarFrmImputaciones($conexion, $cod_cobro) {
        $objCobro = new Vcobros($conexion, $cod_cobro);
        $estado = $objCobro->estado;
        if ($estado == Vcobros::getEstadoanulado()) {
            return false;
        } else {
            return true;
        }
    }

    static function validarArrayFinanciacion($arrFinancia, &$mensaje) {
        $arrTodos = array();
        $arrUnicos = array();
        for ($i = 0; $i < count($arrFinancia['cuotas']); $i++) {
            if ($arrFinancia['eliminadas'][$i] == 0) {
                $arrTodos[] = $arrFinancia['cuotas'][$i]; //todos los no eliminados
            }
        }
        $arrUnicos = array_unique($arrTodos);
        if (count($arrUnicos) == count($arrTodos)) {
            return TRUE;
        } else {
            $mensaje = lang('planespago_financiacionrepetida');
            return FALSE;
        }
    }

    static function validarColorSalon($conexion, $color, $cod_salon) {
        $colorConsulta = Vsalones::getColorsalon($conexion, $color, $cod_salon);
        if ($colorConsulta != NULL) {
            return false;
        } else {
            return true;
        }
    }

    static function validarNombreApellido($palabra, &$formatoEsperado = null) {
        $patron = "/^([A-Za-z ñáéíóúäëïöüÑÄËÏÖÜÁÉÍÓÚàèìòùçÇÀÈÌÒÙÂÊÎÔÛâêîôûÃãẼẽÕõ´])+$/";
        if (preg_match($patron, $palabra) && (bool) (!strpos($palabra, "´ ") && !strpos($palabra, " ´"))) {
            return true;
        } else {
            return false;
        }
    }

    static function validarSaldoFacturar($conexion, $cod_cta) {
        $ctacte = new Vctacte($conexion, $cod_cta);
        $saldo = $ctacte->getSaldoFacturar();
        if ($saldo > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function validarImporteAbrirCaja($conexion, $codcaja, $importe) {
        $caja = new Vcaja($conexion, $codcaja);
        $saldo = $caja->getSaldoUltimoCierre();
        if ($saldo == $importe) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function validarAsistenciaCargada($conexion, $fecha, $js) {
        $datos = json_decode($js, TRUE);
        $fecha = $fecha;
        $horadesde = isset($datos['hora_desde']) ? $datos['hora_desde'] : '00:00:00';
        $horahasta = isset($datos['hora_hasta']) ? $datos['hora_hasta'] : '00:00:00';
        $horarios = Vhorarios::getHorarios($conexion, $fecha, $horadesde, $horahasta, $datos['repite'], '0');
        $arrayAsistencasComision = '';
        foreach ($horarios as $rowhorario) {
            $condiciones = array('cod_horario' => $rowhorario['codigo'], 'baja' => '0', 'estado <>' => 'null');
            $arrmathor = Vmatriculas_horarios::listarMatriculas_horarios($conexion, $condiciones);

            if (count($arrmathor) > 0) {
                $arrayAsistencasComision[$rowhorario['cod_comision']] = $arrmathor;
            }
        }
        return $arrayAsistencasComision;
    }

    static function validarFeriadoCargado($conexion, $fecha, $js) {
        $datos = json_decode($js, TRUE);
        $condiciones = array('baja' => '0');
        $arrferiados = Vferiados::listarFeriados($conexion, $condiciones);
        $habilitaguardar = true;
        if (count($arrferiados) > 0) {
            $fecha = date('Y-m-d', strtotime($fecha));
            $ptguardar = explode('-', $fecha);
            $fechaesta = false;
            foreach ($arrferiados as $feriado) {
                if ($ptguardar['1'] == $feriado['mes'] && $ptguardar['2'] == $feriado['dia']) {

                    if ($ptguardar['0'] == $feriado['anio']) {
                        $fechaesta = true;
                    } elseif ($ptguardar['0'] > $feriado['anio'] && $feriado['repite'] == '1') {
                        $fechaesta = true;
                    } elseif ($ptguardar['0'] < $feriado['anio'] && $datos['repite'] == '1') {
                        $fechaesta = true;
                    }
                }
                if ($fechaesta) {
                    if (isset($datos['hora_desde']) && isset($datos['hora_hasta'])) {//validar horas
                        if (($datos['hora_desde'] < $feriado['hora_desde'] && $datos['hora_hasta'] < $feriado['hora_desde']) ||
                                ($datos['hora_desde'] > $feriado['hora_hasta'] && $datos['hora_hasta'] > $feriado['hora_hasta'])) {
                            $habilitaguardar = true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }
        }
        return $habilitaguardar;
    }

    static function validarAusenciaFacturantes($codusuario) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $errores = '';
        $usuario = new Vusuarios_sistema($conexion, $codusuario);
        $ptoventa = array();
        $ptoventa = $usuario->getPuntosVenta($cod_filial);
        if (count($ptoventa) < 1) {
            $errores.= lang('usuario_sin_punto_venta_para_facturar');
        }
        return $errores;
    }

    static function validarCajaAbierta($codusuario) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $errores = '';
        $usuario = new Vusuarios_sistema($conexion, $codusuario);
        $cajasabierta = $usuario->getCajas('0', '1');
        if (count($cajasabierta) < 1) {
            $errores = lang('debe_abrir_una_caja_para_realizar_cobros');
        }
        return $errores;
    }

    static function validarCajaUsuario($conexion, $codusuario, $codcaja) {
        $usuario = new Vusuarios_sistema($conexion, $codusuario);
        $cajas = $usuario->getCajas();
        $esta = false;
        foreach ($cajas as $rowcaja) {
            if ($rowcaja['codigo'] == $codcaja) {
                $esta = true;
            }
        }
        if ($esta) {
            return true;
        } else {
            return false;
        }
    }

    static function validarSaldoNotaCredito($conexion, $saldo, $cod_ctacte) {
        $objCtaCte = new Vctacte($conexion, $cod_ctacte);
        $saldoGenerarNotaCredito = $objCtaCte->getImporteNotaCredito();
        if ($saldo <= $saldoGenerarNotaCredito[0]['SaldoGenerarCredito']) {
            return true;
        } else {
            return false;
        }
    }

    static function validarFechaHabil($fecha, $conexion, &$retornoMensaje = null) {
        if ($fecha == '') {
            $retorno = false;
        } else {
            $fechatemp = strtotime($fecha);
            $numDia = date("w", $fechatemp);
            $retornoMensaje = '';
            $retorno = $numDia <> 0 && !Vferiados::isFeriado($conexion, $fecha, false);
        }
        if ($retorno) {
            return true;
        } else {
            $retornoMensaje = lang("la_fecha") . " " . formatearFecha_pais($fecha) . " " . lang("no_pertenece_a_un_dia_habil");
            return false;
        }
    }

    /**
     * Valida que una fecha se encuentre dentro de un intervalo
     * 
     * @param type $fecha               la fecha en formato mySQL
     * @param type $fechaDesde          la fecha inferior del rango a comparar (en formato mySQL)
     * @param type $fechaHasta          la fecha superior del rango a comparar (en formato mySQL)
     * @param type $retornoMensaje      (si se necesita tener un mensaje de rotorno)
     * @return boolean
     */
    static function validarIntervaloDeFechas($fecha, $fechaDesde = null, $fechaHasta = null, &$retornoMensaje = null) {
        $resp = true;
        $retornoMensaje = '';
        $arrRetorno = array();
        if ($fechaDesde != null && $fecha < $fechaDesde) {
            $resp = false;
            $arrRetorno[] = lang("mayor_a") . " " . formatearFecha_pais($fechaDesde);
        }
        if ($fechaHasta != null && $fecha > $fechaHasta) {
            $resp = false;
            $arrRetorno[] = lang("menor_a") . " " . formatearFecha_pais($fechaHasta);
        }
        if (!$resp) {
            $retornoMensaje = implode(" y ", $arrRetorno);
        }
        return $resp;
    }

    static function validarMatricularCursoAlumno($cod_plan, $cod_alumno, $arrperiodos, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $retornoMensaje = '';

        $objalumno = new Valumnos($conexion, $cod_alumno);
        $puedematricular = $objalumno->getPeriodosMatricular($cod_plan, $cod_filial);
        if (count($puedematricular) < 1) {
            $retornoMensaje = lang('no_puede_matricular_curso_periodo');
            return false;
        }
        if (count($arrperiodos) < 1) {
            $retornoMensaje = lang('debe_seleccionar_al_menos_un_periodo');
            return false;
        }
        $validacion = true;
        $objplan = new Vplanes_academicos($conexion, $cod_plan);
        $padres = array();
        $periodos = array();
        foreach ($arrperiodos as $value) {
            $esta = false;
            foreach ($puedematricular as $periodo) {
                if ($periodo['codigo'] == $value['periodo']) {
                    $esta = true;
                }
            }
            if (!$esta) {
                $validacion = false;
            }
            $periodos[] = $value['periodo'];
            $planperiodo = $objplan->getPeriodos($value['periodo']);
            if (count($planperiodo) > 0) {
                if ($planperiodo[0]['padre'] != null && $planperiodo[0]['padre'] != '') {
                    $padres[] = $planperiodo[0]['padre'];
                }
            }
        }
        if (!$validacion) {
            $retornoMensaje = lang('no_puede_matricular_curso_periodo');
            return false;
        }
        $matriculapadre = true;
        foreach ($padres as $cod_padre) {
            $matriculado = $objalumno->getMatriculasPeriodosPlanAcademico($cod_plan, null, $cod_padre, false, false, true);
            if (!(in_array($cod_padre, $periodos) || count($matriculado) > 0)) {
                $matriculapadre = false;
                $detalleperpadre = $objplan->getNombrePeriodoModalidadFilial($cod_padre, 'normal', $cod_filial);
            }
        }
        if (!$matriculapadre) {
            $retornoMensaje = lang('debe_matricular_al') . $detalleperpadre;
            return false;
        }
        return true;
    }

    static function validarMatricularPlanAcademico($cod_alumno, $cod_plan_academico) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $myAlumno = new Valumnos($conexion, $cod_alumno);
        //Ticket 646
        $periodosMatricular = $myAlumno->getPeriodosMatricular($cod_plan_academico, $cod_filial);
        if (count($periodosMatricular) > 0) {
            return true;
        } else {
            return false;
        }
    }

    static function validarExistenciaEstadoCertificadoAprobar($cod_certificante, $cod_matricula_periodo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        if (!$objCertificado->getExiste()) {
            if ($cod_certificante == 2){
                return true;
//                $retornoMensaje = lang('complete_fecha_de_inicio_y_fin_para_la_matricula_nnn');
//                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
//                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
//                $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
//                $retornoMensaje = str_replace("###", $myAlumno->nombre." ".$myAlumno->apellido, $retornoMensaje);
            } else {
                $retornoMensaje = lang('no_existe_el_certificado');
            }
            return false;            
        } elseif ($objCertificado->estado != Vcertificados::getEstadoPendienteAprobar()) {
            $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
            $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
            $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
            $retornoMensaje = $myAlumno->apellido.", ".$myAlumno->nombre." ".lang('no_puede_cambiar_de_estado_el_certificado'). "(estado actual inválido)";
            return false;
        } else {
            return true;
        }
    }

    static function validarPropiedadesImpresionCertificado($cod_certificante, $cod_matricula_periodo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objMatPer = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        $objMatricula = new Vmatriculas($conexion, $objMatPer->cod_matricula);
        $objCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        $objCertificadoPlan = new Vcertificados_plan_filial($conexion, $cod_filial, $objMatricula->cod_plan_academico, $objMatPer->cod_tipo_periodo, $cod_certificante);
        $propiedades = $objCertificado->getPropiedadesImpresion();
        $propiedadescertificado = $objCertificadoPlan->getPropiedadesImprimirCertificado();
        if (count($propiedadescertificado) > 0) {
            $estafi = false;
            $estaff = false;
            foreach ($propiedades as $rowpropiedad) {
                if ($rowpropiedad['key'] == 'fecha_inicio') {
                    $fechainicio = formatearFecha_pais($rowpropiedad['valor']);
                    $estafi = true;
                } elseif ($rowpropiedad['key'] == 'fecha_fin') {
                    $fechafin = formatearFecha_pais($rowpropiedad['valor']);
                    $estaff = true;
                }
            }
            if (!$estafi) {
                $retornoMensaje = lang('faltan_propiedades_impresion');
                return false;
            }
            if (!$estaff) {
                $retornoMensaje = lang('faltan_propiedades_impresion');
                return false;
            }
            if (!validaciones::validarFechaFinPosterior($fechainicio, $fechafin, $retornoMensaje)) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    static function validarFechaFinPosterior($fechaini, $fechafin, &$retornoMensaje = null) {
        $fechaini = $fechaini != '' ? $fechaini : date('d/m/Y');
        $valoresPrimera = explode("/", $fechaini);
        $valoresSegunda = explode("/", $fechafin);
        $diaPrimera = $valoresPrimera[0];
        $mesPrimera = $valoresPrimera[1];
        $anyoPrimera = $valoresPrimera[2];
        $diaSegunda = $valoresSegunda[0];
        $mesSegunda = $valoresSegunda[1];
        $anyoSegunda = $valoresSegunda[2];
        $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
        $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);
        if (!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)) {
            $retornoMensaje = lang('formato_fecha_inicio_invalido');
            return false;
        } elseif (!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)) {
            $retornoMensaje = lang('formato_fecha_fin_invalido');
            return false;
        } else if ($diasPrimeraJuliano <= $diasSegundaJuliano) {
            return true;
        } else {
            $retornoMensaje = lang('fecha_fin_anterior_fecha_inicio');
            return false;
        }
    }

    static function validarExistenciaEstadoCertificadoRevertir($cod_certificante, $cod_matricula_periodo, &$retornoMensaje = null) {
        if ($cod_certificante == 2){
            $ci = &get_instance();
            $filial = $ci->session->userdata('filial');
            $cod_filial = $filial['codigo'];
            $conexion = $ci->load->database($cod_filial, true);
            $objCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
            if ($objCertificado->estado != Vcertificados::getEstadoPendienteImpresion()) {
                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
                $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
                $retornoMensaje = "{$myAlumno->apellido}, {$myAlumno->nombre} ".lang('no_puede_cambiar_de_estado_el_certificado')."(estado actual inválido)";
                return false;
            } else {
                return true;
            }
        } else {
            $ci = &get_instance();
            $filial = $ci->session->userdata('filial');
            $cod_filial = $filial['codigo'];
            $conexion = $ci->load->database($cod_filial, true);
            $objCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
            if (!$objCertificado->getExiste()) {
                $retornoMensaje = lang('no_existe_el_certificado');
                return false;
            } else if ($objCertificado->estado != Vcertificados::getEstadoPendienteImpresion()) {
                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
                $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
                $retornoMensaje = "{$myAlumno->apellido}, {$myAlumno->nombre} ".lang('no_puede_cambiar_de_estado_el_certificado')."(estado actual inválido)";
                return false;
            } else {
                return true;
            }
        }        
    }
    
    static function validarExistenciaEstadoCertificadoCancelar($cod_certificante, $cod_matricula_periodo, &$retornoMensaje = null) {
        if ($cod_certificante == 2){
            return true;
        } else {
            $ci = &get_instance();
            $filial = $ci->session->userdata('filial');
            $cod_filial = $filial['codigo'];
            $conexion = $ci->load->database($cod_filial, true);
            $objCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
            if (!$objCertificado->getExiste()) {
                $retornoMensaje = lang('no_existe_el_certificado');
                return false;
            } else if ($objCertificado->estado != Vcertificados::getEstadoPendienteAprobar() && $objCertificado->estado != Vcertificados::getEstadoPendiente() && $objCertificado->estado != Vcertificados::getEstadoPendienteImpresion()) {
                $myMatriculaPeriodo = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
                $myMatricula = new Vmatriculas($conexion, $myMatriculaPeriodo->cod_matricula);
                $myAlumno = new Valumnos($conexion, $myMatricula->cod_alumno);
                $retornoMensaje = "{$myAlumno->apellido}, {$myAlumno->nombre} ".lang('no_puede_cambiar_de_estado_el_certificado')."(estado actual inválido)";
                return false;
            } else {
                return true;
            }
        }
    }

    static function validarExistenciaEstadoCertificadoHabilitar($cod_certificante, $cod_matricula_periodo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objCertificado = new Vcertificados($conexion, $cod_matricula_periodo, $cod_certificante);
        if (!$objCertificado->getExiste()) {
            $retornoMensaje = lang('no_existe_el_certificado');
            return false;
        } elseif ($objCertificado->estado != Vcertificados::getEstadoCancelado()) {
            $retornoMensaje = lang('no_puede_cambiar_de_estado_el_certificado');
            return false;
        } else {
            return true;
        }
    }

    static function validarAlumnoHabilitado($cod_alumno, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        if ($objAlumno->baja == 'inhabilitada') {
            $retornoMensaje = lang('alumno_inhabilitado');
            return false;
        } else {
            return true;
        }
    }

    static function validarMatriculaPeriodoHabilitada($cod_estado_academico, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objestaca = new Vestadoacademico($conexion, $cod_estado_academico);
        $objmatriculaper = new Vmatriculas_periodos($conexion, $objestaca->cod_matricula_periodo);
        if (!$objmatriculaper->estado == 'habilitada') {
            $retornoMensaje = lang('matricula_periodo_debe_estar_habilitada');
            return false;
        } else {
            return true;
        }
    }

    static function validarCertificadosProcesados($cod_matricula_periodo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objmatriculaper = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        $arrcertiproceso = $objmatriculaper->getCertificadosProcesados();
        if (count($arrcertiproceso) > 0) {
            $retornoMensaje = lang('no_se_puede_inhabilitar_matriculas_certificados_en_proceso');
            return false;
        } else {
            return true;
        }
    }

    static function validarMatriculasBaja($cod_plan_academico, $cod_alumno, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $matriculas = $objAlumno->getMatriculasPeriodosPlanAcademico($cod_plan_academico, Vmatriculas::getEstadoHabilitada());
        if (count($matriculas) < 1) {
            $retornoMensaje = lang('no_existen_matriculas_para_deshabilitar');
            return false;
        } else {
            return true;
        }
    }

    static function validarMatriculasAlta($cod_plan_academico, $cod_alumno, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objAlumno = new Valumnos($conexion, $cod_alumno);
        $matriculas = $objAlumno->getMatriculasPeriodosPlanAcademico($cod_plan_academico, Vmatriculas::getEstadoInhabilitada());
        if (count($matriculas) < 1) {
            $retornoMensaje = lang('no_existen_matriculas_para_habilitar');
            return false;
        } else {
            return true;
        }
    }

    static function validarMatriculaPeriodoBaja($cod_matricula_periodo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objMatPer = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        if ($objMatPer->estado != Vmatriculas_periodos::getEstadoHabilitada()) {
            $retornoMensaje = lang('no_puede_inhabilitar_matricula_periodo');
            return false;
        } else {
            return true;
        }
    }

    static function validarMatriculaPeriodoAlta($cod_matricula_periodo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objMatPer = new Vmatriculas_periodos($conexion, $cod_matricula_periodo);
        if ($objMatPer->estado != Vmatriculas_periodos::getEstadoInhabilitada()) {
            $retornoMensaje = lang('no_puede_habilitar_matricula_periodo');
            return false;
        } else {
            return true;
        }
    }

    static function validarBajaCtaCte($codctacte, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $retornoMensaje = lang('no_puede_dar_de_baja_ctacte');
        $objCtacte = new Vctacte($conexion, $codctacte);
        $baja = true;
        //Ticket 4553 -mmori- habilito baja para cualquier concepto
        /*if ($objCtacte->cod_concepto == '3') {*/
            $facturas = $objCtacte->getFacturas();
            $imputacionesconf = $objCtacte->getImputacionesCtaCte('confirmado');
            $imputacionespend = $objCtacte->getImputacionesCtaCte('pendiente');
            if (count($facturas) > 0) {
                $baja = false;
                $retornoMensaje.=' ' . lang('tiene_facturas');
            }
            if (count($imputacionesconf) > 0) {
                $baja = false;
                $retornoMensaje.=' ' . lang('tiene_imputaciones');
            }
            if (count($imputacionespend) > 0) {
                $baja = false;
                $retornoMensaje.=' ' . lang('tiene_imputaciones_a_confirmar');
            }
            if ($objCtacte->cod_concepto == 1 || $objCtacte->cod_concepto == 5){
                $retornoMensaje .= ' '.lang("no_se_puede_dar_de_baja_al_concepto_seleccionado");
                $baja = false;
            }
        /*} else {
            $baja = false;
            $retornoMensaje.=' ' . lang('no_es_mora');
        }*/
        if ($baja) {
            $retornoMensaje = '';
        }
        return $baja;
    }

    static function validarEstadoAcademicoInscribirExamen($codEstadoAcademico, $codExamen, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $conexion = $ci->load->database($filial['codigo'], true);
        $myExamen = new Vexamenes($conexion, $codExamen);
        if ($myExamen->tipoexamen == 'FINAL' || $myExamen->tipoexamen == 'RECUPERATORIO_FINAL') {
            $myEstadoAcademico = new Vestadoacademico($conexion, $codEstadoAcademico);
            if ($myEstadoAcademico->estado <> Vestadoacademico::getEstadoRegular() && $myEstadoAcademico->estado <> Vestadoacademico::getEstadoLibre()) {
                $retornoMensaje = lang("solo_puede_inscribir_en_estado_regular_o_libre");
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    static function validarAlumnoInscriptoExamen($cod_estado_academico, $cod_examen, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condiciones = array('cod_examen' => $cod_examen, 'cod_estado_academico' => $cod_estado_academico, 'estado <>' => 'baja');
        $examen = Vexamenes_estado_academico::listarExamenes_estado_academico($conexion, $condiciones);
        if (count($examen) > 0) {
            $retornoMensaje = lang('alumno_ya_se_inscribio_en_este_examen');
            return false;
        } else {
            return true;
        }
    }

    static function validaCtaCteRefinanciar($codctacte, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objCtacte = new Vctacte($conexion, $codctacte);
        $refinancia = true;
        $facturas = $objCtacte->getFacturas();
        $imputacionesconf = $objCtacte->getImputacionesCtaCte('confirmado');
        $imputacionespend = $objCtacte->getImputacionesCtaCte('pendiente');
        if (count($facturas) > 0) {
            $refinancia = false;
            $retornoMensaje = lang('no_puede_refinanciar_ctacte');
            $retornoMensaje.=' ' . lang('tiene_facturas');
        }
        if (count($imputacionesconf) > 0) {
            $refinancia = false;
            $retornoMensaje = lang('no_puede_refinanciar_ctacte');
            $retornoMensaje.=' ' . lang('tiene_imputaciones');
        }
        if (count($imputacionespend) > 0) {
            $refinancia = false;
            $retornoMensaje = lang('no_puede_refinanciar_ctacte');
            $retornoMensaje.=' ' . lang('tiene_imputaciones_a_confirmar');
        }
        if ($refinancia) {
            $retornoMensaje = '';
        }
        return $refinancia;
    }

    static function validarModificarRazon($codrazon, &$retornoMensaje = null) {
        if ($codrazon == '-1') {
            return true;
        } else {
            $ci = &get_instance();
            $filial = $ci->session->userdata('filial');
            $cod_filial = $filial['codigo'];
            $conexion = $ci->load->database($cod_filial, true);
            $arrRazones = Vrazones_sociales::getRazonesSocialesNoDefault($conexion, null, $codrazon);
            if (count($arrRazones) > 0) {
                $respuesta = true;
            } else {
                $respuesta = false;
                $retornoMensaje = lang('no_se_puede_modificar_razon_default');
            }
            return $respuesta;
        }
    }

    static function validarCambioEstadoCompra($codcompra, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objcompra = new Vcompras($conexion, $codcompra);
        if ($objcompra->estado = 'confirmada') {
            $pagosconfirmados = $objcompra->getPagos('confirmado');
            $pagospendientes = $objcompra->getPagos('pendiente');
            if (count($pagospendientes) > 0 || count($pagosconfirmados)) {
                $retornoMensaje = lang('no_puede_anular_compra_pago_asignado');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    static function validarExistenciaMail($mail, $cod_alumno, &$retornoMensaje = null) {
        if ($mail == "noemail@noemail.com" || $mail = "naoemail@naoemail.com"){
            return true;
        } else {
            $ci = &get_instance();
            $filial = $ci->session->userdata('filial');
            $cod_filial = $filial['codigo'];
            $conexion = $ci->load->database($cod_filial, true);
            $condicion = array('email' => $mail, 'codigo <>' => $cod_alumno);
            $alumnos = Valumnos::listarAlumnos($conexion, $condicion);
            if (count($alumnos) > 0) {
                $retornoMensaje = lang('email_ya_cargado');
                return false;
            } else {
                return true;
            }
        }
    }

    static function validarCodigoInternoTerminal($codigo_interno, $terminal, $operador, &$retornoMensaje = null) {
        if ($codigo_interno == '') {
            $retornoMensaje = lang('codigo_interno_no_puede_ser_vacio');
            return false;
        }
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condicion = array('cod_interno' => $codigo_interno, 'estado' => Vpos_terminales::getEstadoHabilitado(), 'codigo <>' => $terminal);
        $terminales = Vpos_terminales::listarPos_terminales($conexion, $condicion);
        if (count($terminales) > 0) {
            $retornoMensaje = lang('codigo_interno_asignado');
            return false;
        } else {
            if ($terminal != '-1') {
                $terminal = new Vpos_terminales($conexion, $terminal);
                $cod_operador = $terminal->getCodigoOperador();
                $objoperador = new Vpos_operadores($conexion, $cod_operador);
            } else {
                $objoperador = new Vpos_operadores($conexion, $operador);
            }

            if (!$objoperador->validarCodigoInternoTerminal($codigo_interno)) {
                $retornoMensaje = lang('codigo_interno_formato_incorrecto');
                return false;
            }
            return true;
        }
    }

    static function validarCuponTerminal($cupon, $codigo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $terminal = new Vpos_terminales($conexion, $codigo);
        $cod_operador = $terminal->getCodigoOperador();
        $objoperador = new Vpos_operadores($conexion, $cod_operador);
        if (!$objoperador->validarCuponTerminal($cupon)) {
            $retornoMensaje = lang('cupon_formato_incorrecto');
            return false;
        }
        return true;
    }

    static function validarAutorizacionTerminal($cod_autorizacion, $codigo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $terminal = new Vpos_terminales($conexion, $codigo);
        $cod_operador = $terminal->getCodigoOperador();
        $objoperador = new Vpos_operadores($conexion, $cod_operador);
        if (!$objoperador->validarAutorizacionTerminal($cod_autorizacion)) {
            $retornoMensaje = lang('codigo_autorizacion_formato_incorrecto');
            return false;
        }
        return true;
    }

    static function validarCobroCajaMedio($cod_caja, $cod_medio, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $caja = new Vcaja($conexion, $cod_caja);
        $medios = $caja->getMediosPago($cod_medio);

        if (count($medios) < 1) {
            $retornoMensaje = lang('caja_no_acepta_medio_pago');
            return false;
        }
        if ($medios[0]['conf_automatica'] == '1' && $caja->estado == Vcaja::$estadocerrada) {
            $retornoMensaje = lang('abrir_caja_realizar_cobro');
            return false;
        }
        return true;
    }

    static function validarModificarCobro($codigo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objcobro = new Vcobros($conexion, $codigo);
        if ($objcobro->estado != Vcobros::getEstadoPendiente() && $objcobro->estado != Vcobros::getEstadoError() && $objcobro->estado != Vcobros::getEstadoanulado()) {
            $retornoMensaje = lang('no_se_puede_modificar_cobro_estado') . lang($objcobro->estado);
            return false;
        }
        return true;
    }

    static function validarEliminarImputacion($codigo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $imputacion = new Vctacte_imputaciones($conexion, $codigo);
        if ($imputacion->estado <> 'confirmado') {
            return true;
        } else {
            $retornoMensaje = lang('no_se_puede_eliminar_imputacion_confirmada');
            return false;
        }
    }

    static function validarConfirmarCobro($codigo, $cod_usuario, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objcobro = new Vcobros($conexion, $codigo);
        if ($objcobro->estado != Vcobros::getEstadoPendiente()) {
            $retornoMensaje = lang('no_se_puede_confirmar_cobro') . ' ' . lang($objcobro->estado);
            return false;
        }
        $myCaja = new Vcaja($conexion, $objcobro->cod_caja);
        $arrUsuarios = $myCaja->getUsuarios();
        $permisoUsuario = false;
        $i = 0;
        while (!$permisoUsuario && $i < count($arrUsuarios)) {
            $permisoUsuario = $arrUsuarios[$i]['codigo'] == $cod_usuario;
            $i++;
        }
        if (!$permisoUsuario) {
            $retornoMensaje = lang("no_tiene_permiso_para_utilizar_la_caja_donde_se_registro_el_cobro");
            return false;
        }
        if ($objcobro->fechareal > date('Y-m-d')) {
            $retornoMensaje = lang('fecha_cobro_no_puede_ser_posterior_hoy');
            return false;
        }
        $periodo1 = date('Y') . date('m');
        $periodo2 = date('Y') . date("m") - 1;
        if (($objcobro->periodo != $periodo1 && $objcobro->periodo != $periodo2) && $objcobro->periodo !== null) {
            $retornoMensaje = lang("periodo_del_cobro_cerrado");
            return false;
        }
        return true;
    }

    static function validarAnulacionCobro($cod_cobro, $cod_usuario, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objCobro = new Vcobros($conexion, $cod_cobro);
        if ($objCobro->estado == Vcobros::getEstadoanulado()) {
            $retornoMensaje = lang("cobro_ya_anulado");
            return false;
        }
        $registracaja = $objCobro->estado == Vcobros::getEstadoConfirmado() && $objCobro->medio_pago != '2' ? true : false;
        $condiciones = array('cod_concepto' => 'COBROS', 'concepto' => $objCobro->getCodigo());
        $movimientos = Vmovimientos_caja::listarMovimientos_caja($conexion, $condiciones);
        $debe = 0;
        $haber = 0;
        foreach ($movimientos as $mov) {
            $debe = $debe + $mov['debe'];
            $haber = $haber + $mov['haber'];
        }
        $muevacaja = $debe != $haber ? true : false;
        if ($registracaja && $muevacaja) {//registra en caja y hace movimientos en la misma
            $myCaja = new Vcaja($conexion, $objCobro->cod_caja);
            $arrUsuarios = $myCaja->getUsuarios();
            $permisoUsuario = false;
            $i = 0;
            while (!$permisoUsuario && $i < count($arrUsuarios)) {
                $permisoUsuario = $arrUsuarios[$i]['codigo'] == $cod_usuario;
                $i++;
            }
            if (!$permisoUsuario) {
                $retornoMensaje = lang("no_tiene_permiso_para_utilizar_la_caja_donde_se_registro_el_cobro");
                return false;
            }
        }
        $periodo = $objCobro->periodo;
        $anio = substr($periodo, 0, 4);
        $mes = substr($periodo, 4, 2);
        if (Vcobros::periodoCobroCerrado($conexion, $cod_filial, $mes, $anio)){
            $retornoMensaje = lang("periodo_del_cobro_cerrado");
            return false;
        }
        return true;
    }

    static function validarImporteCobro($importe, $cod_cobro, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        if ($importe <= 0) {
            $retornoMensaje = lang('importe_cobrar_no_puede_ser_0');
            return false;
        }
        if ($cod_cobro != '') {
            $objCobro = new Vcobros($conexion, $cod_cobro);
            $sumimputaciones = $objCobro->getSumValorImputacionesCobro();
            $imputaciones = $sumimputaciones[0]['totImputaciones'];
            $total = str_replace(',', '.', $importe);
            if ($imputaciones > $total) {
                $retornoMensaje = lang('importe_no_puede_ser_menor_al_total_imputado');
                return false;
            }
        }
        return true;
    }

    static function validarFechaPeriodoCerrado($fecha_cobro, &$retornoMensaje = null){
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $fecha = formatearFecha_mysql($fecha_cobro);
        $temp = explode("-", $fecha);
        if (Vcobros::periodoCobroCerrado($conexion, $cod_filial, $temp[1], $temp[0])){
            $retornoMensaje = lang("la_fecha_de_cobro_ingresada_pertenece_a_un_periodo_de_royalty_cerrado");
            return false;
        } else  {
            return true;
        }
    }
    
    static function validarFechaCobro($fecha_cobro, $cod_medio, $cod_caja, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $fecha = formatearFecha_mysql($fecha_cobro);
        $objmedio = new Vmedios_pago($conexion, $cod_medio);
        $objcaja = new Vcaja($conexion, $cod_caja);
        $medios = $objcaja->getMediosPago($objmedio->getCodigo());
        $confirma = '1';
        if (count($medios) > 0) {
            $confirma = $medios[0]['conf_automatica'];
        }
        if ($confirma == '1') {
            if ($fecha > date('Y-m-d')) {
                $retornoMensaje = lang('fecha_cobro_no_puede_ser_posterior_hoy');
                return false;
            }
        }
        $temp = explode("-", $fecha);
        if (Vcobros::periodoCobroCerrado($conexion, $cod_filial, $temp[1], $temp[0])){
            $retornoMensaje = lang("la_fecha_de_cobro_ingresada_pertenece_a_un_periodo_de_royalty_cerrado");
            return false;
        }
        return true;
    }

    static function validarPeriodoCobro($codigo, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $objCobro = new Vcobros($conexion, $codigo);
        $periodo1 = date('Y') . date('m');
        $periodo2 = date('Y') . date("m") - 1;
        if ($objCobro->estado != Vcobros::getEstadoConfirmado()) {
            if (($objCobro->periodo != $periodo1 && $objCobro->periodo != $periodo2) && $objCobro->periodo !== null) {
                $retornoMensaje = lang("periodo_del_cobro_cerrado");
                return false;
            }
        }

        return true;
    }

    static function validarPrimerPagoMatricula($fecha, $plan_pago, $financiacion, $cod_concepto, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objplan = new Vplanes_pago($conexion, $plan_pago);
        $planfinanciacion = $objplan->getPlanFinanciacionDescuento($financiacion, $cod_concepto);

        $validacion = true;
        if (count($planfinanciacion) > 0) {
            switch ($planfinanciacion[0]['limite_primer_cuota']) {
                case 'con_fecha_limite':

                    if ($fecha == '') {
                        $retornoMensaje .=lang('seleccione_fecha_primer_pago_de') . lang(Vconceptos::getKey($conexion, $cod_concepto));
                        $validacion = false;
                    } else {
                        $fecha2 = str_replace('/', '-', $fecha);
                        $fechast = strtotime($fecha2);

                        if ($fechast === false || $fechast == '') {
                            $retornoMensaje .= lang('formato_fecha_invalido') . '. ';
                            $validacion = false;
                        } elseif (formatearFecha_mysql($fecha) > $planfinanciacion[0]['fecha_limite']) {
                            $fechalimite = $planfinanciacion[0]['fecha_limite'] = '' ? '' : formatearFecha_pais($planfinanciacion[0]['fecha_limite']);
                            $retornoMensaje .= lang("fecha_primer_pago_de_la") . lang(Vconceptos::getKey($conexion, $cod_concepto)) . ' ' . lang("no_puede_ser_superior_a") . $fechalimite . '. ';
                            $validacion = false;
                        }
                    }

                    break;
                case 'sin_fecha_limite':
                    if ($fecha == '') {
                        $retornoMensaje .= lang('seleccione_fecha_primer_pago_de') . lang(Vconceptos::getKey($conexion, $cod_concepto));
                        $validacion = false;
                    } else {
                        $fecha2 = str_replace('/', '-', $fecha);
                        $fechast = strtotime($fecha2);

                        if ($fechast === false || $fechast == '') {
                            $retornoMensaje .= lang('formato_fecha_invalido') . '. ';
                            $validacion = false;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        return $validacion;
    }

    static function validarPlanPagoPeriodos($cod_plan, $plan_academico, $periodos, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $arrperiodosplan = array();
        $objplan = new Vplanes_pago($conexion, $cod_plan);
        $periodosPlan = $objplan->getPeriodosCurso($plan_academico);
        foreach ($periodosPlan as $value) {
            $arrperiodosplan[] = $value['cod_tipo_periodo'];
        }
        if ($periodos == $arrperiodosplan) {
            return true;
        } else {
            $retornoMensaje = lang('plan_de_pago_invalido');
            return false;
        }
    }

    static function validarImporteFacturaNC($cod_factura, $importe, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $factura = new Vfacturas($conexion, $cod_factura);
        $notas_factura = $factura->getNotasCredito(0);
        $total = 0;
        if ($importe == 0) {
            $retornoMensaje = lang('importe_no_cero');
            return false;
        }
        foreach ($notas_factura as $row) {
            $total = $total + $row['importe'];
        }
        $tot_factura = $factura->total;
        $puede = $tot_factura - $total;
        if (round($importe, 3) > round($puede, 3)) {
            $retornoMensaje = lang('importe_nc_superior_a') . $filial['moneda']['simbolo'] . $puede;
            return false;
        }
        return true;
    }

    static function validarConfirmarNC($codigo, $cod_usuario, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $objnc = new Vnotas_credito($conexion, $codigo);

        if ($objnc->estado != Vcobros::getEstadoPendiente()) {
            $retornoMensaje = lang('no_se_puede_confirmar_nc') . ' ' . lang($objnc->estado);
            return false;
        }
        return true;
    }

    static function validarAnularNC($codigo, $cod_usuario, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $objnc = new Vnotas_credito($conexion, $codigo);

        if ($objnc->estado == Vnotas_credito::getEstadoanulado()) {
            $retornoMensaje = lang("nc_no_puede_anular");
            return false;
        }

        return true;
    }

    static function validarImporteFacturar($total, $jsctacte, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $separador = $filial['moneda']['separadorDecimal'];
        (double) $totalFacturar = str_replace($separador, '.', $total);
        $ctactes = json_decode($jsctacte, true);
        $totalctas = 0;
        foreach ($ctactes as $ctacte) {
            $objcta = new Vctacte($conexion, $ctacte);
            $totalctas = $totalctas + $objcta->importe;
        }
        if ($totalFacturar != '') {
            if ($totalFacturar == 0) {
                $retornoMensaje = lang('importe_no_cero');
                return false;
            }

            if ((double) $totalctas < $totalFacturar) {
                $retornoMensaje = lang('importe_facturar_mayor_seleccionado');
                return false;
            }
        }

        return true;
    }

    /**
     * 
     * @param CI_DB_mysqli_driver $conexion objeto de conexion a la base de datos
     * @param array $arrComisionesOrigenDestino array en formato array[] = array("origen" => codComsion, "destino" => codComision) con las comisiones origen y destino
     * @param boolean $validarDestinoHabilitado true para validar que la comision de destino se encuentre en estado habilitado
     * @param boolean $validarCicloActivo true para validar que la comision de destino no posea ciclo vencido o sin comenzar
     * @param boolean $validarPlanesCompatibles true para validar que ambas comisiones pertenezcan al mismo plan academico
     * @param boolean $validarPeriodoSiguiente true para validar que el periodo de la comision destino sea posterior al periodo de la comision origen
     * @param boolean $validarEstadoAcademicoCursando true para validar que los ningun alumno de la comision origen se encuentre con estado academico cursando
     * @param mixed $retorno variable pasada por referencia que acumula los mensajes de error encontrados durante la validacion
     * @return type
     */
    static function validarComisionesParaPasajePeriodo(CI_DB_mysqli_driver $conexion, array $arrComisionesOrigenDestino, $validarDestinoHabilitado = true, $validarCicloActivo = true, $validarPlanesCompatibles = true, $validarPeriodoSiguiente = true, $validarEstadoAcademicoCursando = true, &$retorno = null) {
        $retorno = array();
        if ($validarEstadoAcademicoCursando) {
            $ci = &get_instance();
            $arrCantidades = $ci->Model_comisiones->getComisionesCambiar($conexion);
        }
        foreach ($arrComisionesOrigenDestino as $comisiones) {
            $codComisionOrigen = $comisiones['origen'];
            $codComisionDestino = $comisiones['destino'];
            $myComisionOrigen = new Vcomisiones($conexion, $codComisionOrigen);
            $myComisionDestino = new Vcomisiones($conexion, $codComisionDestino);
            if ($codComisionDestino == -1) {
                $retorno[] = str_replace("$$$", $myComisionOrigen->nombre, lang("no_se_ha_seleccionado_comision_de_destino_para"));
            } else {
                if ($validarDestinoHabilitado && $myComisionDestino->estado <> Vcomisiones::getEstadoHabilitada()) {
                    $retorno[] = str_replace("$$$", $myComisionDestino->nombre, lang("la_comision_de_destino_no_se_encuentra_habilitada"));
                }
                if ($validarCicloActivo) {
                    $myCiclo = new Vciclos($conexion, $myComisionDestino->ciclo);
                    if (!($myCiclo->fecha_inicio_ciclo <= date("Y-m-d") && $myCiclo->fecha_fin_ciclo >= date("Y-m-d"))) {
                        $retorno[] = str_replace("$$$", $myComisionDestino->nombre, lang("la_comision_de_destino_se_encuentra_fuera_de_ciclo"));
                    }
                }
                if ($validarPlanesCompatibles && $myComisionOrigen->cod_plan_academico <> $myComisionDestino->cod_plan_academico) {
                    $retorno[] = str_replace(array("$$$1", "$$$2"), array($myComisionOrigen->nombre, $myComisionDestino->nombre), long("las_comisiones_y_no_poseen_planes_compatibles"));
                }

                if ($validarPeriodoSiguiente) {
                    if ($myComisionOrigen->estado == Vcomisiones::getEstadoHabilitada()) { // se realiza pasaje entre comisiones del mismo periodo
                        if ($myComisionDestino->cod_tipo_periodo <> $myComisionOrigen->cod_tipo_periodo) { // se realiza pasaje a comision de periodo siguiente
                            $retorno[] = str_replace(array("$$$1", "$$$2"), array($myComisionOrigen->nombre, $myComisionDestino->nombre), lang("la_comision_no_pertenece_al_mismo_periodo_de_la_comision"));
                        }
                    } else {
                        if ($myComisionDestino->cod_tipo_periodo <> $myComisionOrigen->cod_tipo_periodo + 1) { // se realiza pasaje a comision de periodo siguiente
                            $retorno[] = str_replace(array("$$$1", "$$$2"), array($myComisionOrigen->nombre, $myComisionDestino->nombre), lang("la_comision_no_pertenece_a_un_periodo_posterior_de_la_comision"));
                        }
                    }
                }
            }
            if ($validarEstadoAcademicoCursando) {
                if (isset($arrCantidades[$myComisionOrigen->getCodigo()]) && isset($arrCantidades[$myComisionOrigen->getCodigo()]['cantidad_estado_cursando']) && $arrCantidades[$myComisionOrigen->getCodigo()]['cantidad_estado_cursando'] > 0) {
                    $retorno[] = str_replace("$$$", $myComisionOrigen->nombre, lang("la_comision_posee_alumnos_con_estado_academico_cursando"));
                }
            }
        }
        return count($retorno) == 0;
    }

    static function validarCobroFacturar($cod_cobro, &$retornoMensaje = null) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $imputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion, array("cod_cobro" => $cod_cobro, "tipo" => 'COBRO', "estado <>" => 'anulado'));
        $puedefacturar = true;
        foreach ($imputaciones as $rowimputacion) {
            $importefacturar = Vctacte::getSumImporteFacturar($conexion, array($rowimputacion['cod_ctacte']));
            if ($importefacturar < $rowimputacion['valor']) {
                $puedefacturar = false;
            }
        }
        if (!$puedefacturar) {
            $retornoMensaje = lang('importe_ya_facturado');
        }
        if ($puedefacturar && count($imputaciones) < 1) {
            $puedefacturar = false;
            $retornoMensaje = lang('no_tiene_imputaciones');
        }
        if ($puedefacturar) {
            $cobro = new Vcobros($conexion, $cod_cobro);
            $facturas = $cobro->getFacturasAsociadas();
            if (count($facturas) > 0) {
                $puedefacturar = false;
                $retornoMensaje = lang('tiene_facturas_asociadas');
            }
        }

        return $puedefacturar;
    }

    static function validarRazonSocialFacturar( array $arrCtacte, &$retornoMensaje = null){
        $retornoMensaje = '';
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $pais = $filial['pais'];
        $conexion = $ci->load->database($filial['codigo'], true);
        $arrFacturasError = array();
        $error = false;
        foreach ($arrCtacte as $ctacte){
            $myCtacte = new Vctacte($conexion, $ctacte);
            $myAlumno = new Valumnos($conexion, $myCtacte->cod_alumno);
            $razonSocial = $myAlumno->getRazonSocialDefaultFacturar();
            $myRazonSocial = new Vrazones_sociales($conexion, $razonSocial[0]['cod_razon_social']);
            if ($pais == 2){ // todas las validaciones de facturas por pais- brasil facturar a CPF o CNPJ
                if ($myRazonSocial->tipo_documentos <> 21 && $myRazonSocial->tipo_documentos <> 6){
                    $arrFacturasError[] = "({$myAlumno->getCodigo()}) {$myAlumno->nombre} {$myAlumno->apellido} "."razon social con tipo de documento inválido";
                    $error = true;
                }
            }
        }
        if (count($arrFacturasError) > 0){
            $retornoMensaje = implode("<br>", $arrFacturasError);
        }
        return !$error;
    }
    
}
