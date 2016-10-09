<?php

class maquetados {
    /* PRIVATE FUNCTIONS */

    /**
     * Desetiqueta INPUTTEXTREQUIRED
     *
     * @param string $id
     * @param string $html
     * @param array $arrDefaultValues
     * @return boolean
     */
    static private function desetiquetarINPUTTEXTREQUIRED($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--INPUTTEXTREQUIRED', $pos) && $maximosReemplazos < 30) {
            $valor = $arrDefaultValues != null && isset($arrDefaultValues['INPUTTEXTREQUIRED'][$maximosReemplazos]) ? $arrDefaultValues['INPUTTEXTREQUIRED'][$maximosReemplazos] : 0;
            $pos = strpos($html, '[!--INPUTTEXTREQUIRED', $pos);
            $pos1 = strpos($html, '--]', $pos) + 3;
            $string = substr($html, $pos, $pos1 - $pos);
            $tablePOS = strpos($string, 'STYLE');
            $tablePOS1 = strpos($string, '"', $tablePOS) + 1;
            $tablePOS2 = strpos($string, '"', $tablePOS1 + 1);
            $style = substr($string, $tablePOS1, $tablePOS2 - $tablePOS1);
            //SIZE para el campo descuento
            $tablePOS = strpos($string, 'SIZE');
            $tablePOS1 = strpos($string, '"', $tablePOS) + 1;
            $tablePOS2 = strpos($string, '"', $tablePOS1 + 1);
            $size = substr($string, $tablePOS1, $tablePOS2 - $tablePOS1);

            $largo = strlen($string);
            $valorReemplazo = $modoInput ? $valorReemplazo = "<input type='text' required='' name='INPUTTEXTREQUIRED' id='INPUTTEXTREQUIRED_{$id}_{$maximosReemplazos}' value='$valor' style='border: 1px solid red; $style' size='$size' >" : "<span>{$valor}</span>";
            $title = strpos($string, 'TITLE');
            if ($title) {
                $title1 = strpos($string, '"', $title) + 1;
                $title2 = strpos($string, '"', $title1 + 1);
                $title = substr($string, $title1, $title2 - $title1);
            } else {
                $title = '';
            }
            $string = "$title $valorReemplazo";
            $html = substr_replace($html, $string, $pos, $largo);
            $maximosReemplazos++;
        }
        return true;
    }

    /**
     * Desetiqueta INPUTCALENDARREQUIRED
     *
     * @param string $id
     * @param string $html
     * @param array $arrDefaultValues
     * @return boolean
     */
    static private function desetiquetarINPUTCALENDARREQUIRED($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--INPUTTEXTCALENDARREQUIRED', $pos) && $maximosReemplazos < 30) {
            $valor = $arrDefaultValues != null && isset($arrDefaultValues['INPUTTEXTCALENDARREQUIRED'][$maximosReemplazos]) ? $arrDefaultValues['INPUTTEXTCALENDARREQUIRED'][$maximosReemplazos] : 0;
            if ($valor == '')
                $valor = "A Definir";
            $pos = strpos($html, '[!--INPUTTEXTCALENDARREQUIRED', $pos);
            $pos1 = strpos($html, '--]', $pos) + 3;
            $string = substr($html, $pos, $pos1 - $pos);
            $tablePOS = strpos($string, 'STYLE');
            $tablePOS1 = strpos($string, '"', $tablePOS) + 1;
            $tablePOS2 = strpos($string, '"', $tablePOS1 + 1);
            $style = substr($string, $tablePOS1, $tablePOS2 - $tablePOS1);
            $largo = strlen($string);
            $valorReemplazo = $modoInput ? $valorReemplazo = "<input type='text' class='date_picker_templates' name='INPUTTEXTCALENDARREQUIRED' id='INPUTTEXTCALENDARREQUIRED_{$id}_{$maximosReemplazos}' value='$valor' style='$style'>" : "<span style='$style'>{$valor}</span>";
            $title = strpos($string, 'TITLE');
            if ($title) {
                $title1 = strpos($string, '"', $title) + 1;
                $title2 = strpos($string, '"', $title1 + 1);
                $title = substr($string, $title1, $title2 - $title1);
            } else {
                $title = '';
            }
            $string = "$title $valorReemplazo";
            $html = substr_replace($html, $string, $pos, $largo);
            $maximosReemplazos++;
        }
        return true;
    }

    /**
     * desetiqueta SELECT
     *
     * @param string $id
     * @param string $html
     * @param array $arrDefaultValues
     * @return boolean
     */
    static private function desetiquetarSELECT($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--SELECT', $pos) && $maximosReemplazos < 30) {
            $valor = $arrDefaultValues != null && isset($arrDefaultValues['SELECT'][$maximosReemplazos]) ? $arrDefaultValues['SELECT'][$maximosReemplazos] : 0;
            $pos = strpos($html, '[!--SELECT', $pos);
            $pos1 = strpos($html, '--]', $pos) + 3;
            $string = substr($html, $pos, $pos1 - $pos);
            $largo = strlen($string);
            $cantidadVeces = substr_count($string, "OPTION");
            $offset = 0;
            for ($ov = 0; $ov < $cantidadVeces; $ov++) {
                $campoPOS = strpos($string, 'OPTION', $offset);
                $campoPOS1 = strpos($string, '"', $campoPOS) + 1;
                $campoPOS2 = strpos($string, '"', $campoPOS1 + 1);
                $offset = $campoPOS2;
                $campo = substr($string, $campoPOS1, $campoPOS2 - $campoPOS1);
                $arrOptions[] = $campo;
            }
            if ($modoInput) {
                $string1 = "<select name='SELECT' id='SELECT_{$id}_{$maximosReemplazos}'>";
                for ($ov = 0; $ov < count($arrOptions); $ov++) {
                    $string1 .= "<option value='$arrOptions[$ov]' ";
                    if ($valor == $arrOptions[$ov])
                        $string1 .= " selected='true'";
                    $string1 .= ">$arrOptions[$ov]</option>";
                }
                $string1 .= "</select>";
                $string = $string1;
                $title = strpos($string, 'TITLE');
                if ($title) {
                    $title1 = strpos($string, '"', $title) + 1;
                    $title2 = strpos($string, '"', $title1 + 1);
                    $title = substr($string, $title1, $title2 - $title1);
                } else {
                    $title = '';
                }
                $string = "$title $string";
            } else {
                $string = "<span>$valor</span>";
            }

            $html = substr_replace($html, $string, $pos, $largo);
            $maximosReemplazos++;
        }
        return true;
    }

    /**
     * Desetiqueta TEXTAREA
     *
     * @param string $id
     * @param string $html
     * @param array $arrDefaultValues
     * @return boolean
     */
    static private function desetiquetarTEXTAREA($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--TEXTAREA', $pos) && $maximosReemplazos < 30) {
            $valor = $arrDefaultValues != null && isset($arrDefaultValues['TEXTAREA'][$maximosReemplazos]) ? $arrDefaultValues['TEXTAREA'][$maximosReemplazos] : '';
            $pos = strpos($html, '[!--TEXTAREA', $pos);
            $pos1 = strpos($html, '--]', $pos) + 3;
            $string = substr($html, $pos, $pos1 - $pos);
            $largo = strlen($string);
            if ($modoInput) {
                $string = str_replace("[!--TEXTAREA", "<textarea name='TEXTAREA' id='TEXTAREA_{$id}_{$maximosReemplazos}'", $string);
                $string = str_replace("--]", ">$valor</textarea>", $string);
                $title = strpos($string, 'TITLE');
                if ($title) {
                    $title1 = strpos($string, '"', $title) + 1;
                    $title2 = strpos($string, '"', $title1 + 1);
                    $title = substr($string, $title1, $title2 - $title1);
                } else {
                    $title = '';
                }
                $string = "$title $string";
            } else {
                $string = "<span>".nl2br($valor)."</span>";
            }
            $html = substr_replace($html, $string, $pos, $largo);
            $maximosReemplazos++;
        }
        return true;
    }

    /**
     * Desetiqueta TEXT
     *
     * @param string $id
     * @param string $html
     * @param array $arrDefaultValues
     * @return boolean
     */
    static private function desetiquetarTEXT($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--INPUTTEXT', $pos) && $maximosReemplazos < 30) {
            $valor = $arrDefaultValues != null && isset($arrDefaultValues['INPUTTEXT'][$maximosReemplazos]) ? $arrDefaultValues['INPUTTEXT'][$maximosReemplazos] : 0;
            $pos = strpos($html, '[!--INPUTTEXT', $pos);
            $pos1 = strpos($html, '--]', $pos) + 3;
            $string = substr($html, $pos, $pos1 - $pos);
            $largo = strlen($string);
            if ($modoInput) {
                $string = str_replace("[!--INPUTTEXT", "<input type='text' name='INPUTTEXT' value='$valor' id='INPUTTEXT_{$id}_{$maximosReemplazos}' ", $string);
                $string = str_replace("--]", ">", $string);
                $title = strpos($string, 'TITLE');
                if ($title) {
                    $title1 = strpos($string, '"', $title) + 1;
                    $title2 = strpos($string, '"', $title1 + 1);
                    $title = substr($string, $title1, $title2 - $title1);
                } else {
                    $title = '';
                }
                $string = "$title $string";
            } else {
                $string = "<span>$valor</span>";
            }
            $html = substr_replace($html, $string, $pos, $largo);
            $maximosReemplazos++;
        }
        return true;
    }

    /**
     * Desetiqueta TEXTAREAREQUIRED
     *
     * @param string $id
     * @param string $html
     * @param array $arrDefaultValues
     * @return boolean
     */
    static private function desetiquetarTEXTAREAREQUIRED($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--TEXTAREAREQUIRED', $pos) && $maximosReemplazos < 30) {
            $valor = $arrDefaultValues != null && isset($arrDefaultValues['TEXTAREAREQUIRED'][$maximosReemplazos]) ? $arrDefaultValues['TEXTAREAREQUIRED'][$maximosReemplazos] : 0;
            $pos = strpos($html, '[!--TEXTAREAREQUIRED', $pos);
            $pos1 = strpos($html, '--]', $pos) + 3;
            $string = substr($html, $pos, $pos1 - $pos);
            $largo = strlen($string);

            if ($valor === "0") {
                $valor = "";
            }

            if ($modoInput) {
                $string = str_replace("[!--TEXTAREAREQUIRED", "<textarea name='TEXTAREAREQUIRED' id='TEXTAREAREQUIRED_{$id}_{$maximosReemplazos}' style='border: 1px solid red'", $string);
                
                $string = str_replace("--]", ">$valor</textarea>", $string);
                $title = strpos($string, 'TITLE');
                if ($title) {
                    $title1 = strpos($string, '"', $title) + 1;
                    $title2 = strpos($string, '"', $title1 + 1);
                    $title = substr($string, $title1, $title2 - $title1);
                } else {
                    $title = '';
                }
                $string = "$title<br>$string";
            } else {
                $string = "<span>".nl2br($valor)."</span>";
            }
            $html = substr_replace($html, $string, $pos, $largo);
            $maximosReemplazos++;
        }
        
        return true;
    }

    /**
     * Retorna el valor de 'campo' para un 'registro' en una 'tabla' de la 'dataBase'
     *
     * @param CI_DB_mysqli_driver $conexion Objeto de conexion a la base de datos
     * @param string $dataBase                nombre de la base de datos
     * @param string $table                   nombre de la tabla
     * @param string $campo                   nombre del campo
     * @param array $condicion               condicion/es a cumplir para recuperar el valor del registro
     * @return string
     */
    static private function getReemplazoDB(CI_DB_mysqli_driver $conexion, $dataBase, $table, $campo, array $condicion) {
        $conexion->select($campo);
        $conexion->from("{$dataBase}.{$table}");
        $conexion->where($condicion);
        $query = $conexion->get();

        if($conexion->_error_message() != ""){
            echo $conexion->last_query()."<br>";
            echo $conexion->_error_message();die();
        }

        $arrResp = $query->result_array();

        return isset($arrResp[0][$campo]) ? $arrResp[0][$campo] : "";
    }

    /* PUBLIC FUNCTIONS */

    /* STATIC FUNCTIONS */

    /**
     * Quita las etiquetas y realiza la traduccion de textos previamente maquetados
     *
     * @param string $string    La cadena a desetiquetar
     * @param type $openTag     El tag de apertura del maquetado (ejemplo: "[!--" )
     * @param type $closeTag    El tag de cierre del maquetado (ejemplo: "--!]" )
     * @return string
     */
    static function desetiquetarIdioma(&$string, $aplicaHtmlEntities = false) {
        $offset = 0;
        $openTag = "[!--";
        $closeTag = "--]";
        while (strpos(" " . $string, $openTag, $offset) && strpos(" " . $string, $closeTag, $offset)) {
            $pos1 = strpos($string, $openTag, $offset);
            $pos2 = strpos($string, $closeTag, $pos1);
            if ($pos2) {
                $substring = substr($string, $pos1, $pos2 - $pos1 + strlen($closeTag));
                $substring = str_replace($openTag, "", $substring);
                $substring = str_replace($closeTag, "", $substring);
                if (strpos(" $substring", $openTag)) { // probar antes de descomentar el codigo
//                    echo $substring."<br>";
//                    $substring = self::desetiquetar($string, $openTag, $closeTag);
//                    echo $substring."<br>";
                }
                if ($aplicaHtmlEntities) {
                    $substring1 = htmlentities(lang($substring));
                } else {
                    $substring1 = lang($substring);
                }
                if ($substring1) {
                    $string = substr_replace($string, $substring1, $pos1, $pos2 - $pos1 + strlen($closeTag));
                } else {
                    $offset = $pos1 + strlen($openTag);
                }
            }
        }
        return $string;
    }

    /**
     * desetiqueta reemplazando por valores segun el el array $arrEtiquetasValores($etiqueta => $valor);
     *
     * @param array $arrEtiquetasValores
     * @param string $html
     * @return boolean
     */
    static function desetiquetar($arrEtiquetasValores, &$html) {
//        echo "$html<br>";
        foreach ($arrEtiquetasValores as $etiqueta => $valor) {
            $valor = str_replace('"',"'",$valor);
            $html = str_replace($etiqueta, htmlentities($valor, null, 'ISO-8859-1'), $html);
        }
//        echo "$html";
        return true;
    }

    /**
     * desetiqueta sobre etiquetas de base de datos
     *
     * @param CI_DB_mysqli_driver $conexion
     * @param string $html
     * @return boolean
     */
    static function desetiquetarDesdeDB(CI_DB_mysqli_driver $conexion, &$html) {
        $pos = 0;
        $maximosReemplazos = 0;
        while (strpos($html, '[!--DB', $pos) && $maximosReemplazos < 30) {
            $pos = strpos($html, '[!--DB', $pos) + 6;
            $pos1 = strpos($html, '--]', $pos);
            $string = substr($html, $pos, $pos1 - $pos);
            $dbPOS = strpos($string, 'DATABASE');
            $dbPOS1 = strpos($string, '"', $dbPOS) + 1;
            $dbPOS2 = strpos($string, '"', $dbPOS1 + 1);
            $dataBase = substr($string, $dbPOS1, $dbPOS2 - $dbPOS1);

            $tablePOS = strpos($string, 'TABLA');
            $tablePOS1 = strpos($string, '"', $tablePOS) + 1;
            $tablePOS2 = strpos($string, '"', $tablePOS1 + 1);
            $table = substr($string, $tablePOS1, $tablePOS2 - $tablePOS1);

            $campoPOS = strpos($string, 'CAMPO');
            $campoPOS1 = strpos($string, '"', $campoPOS) + 1;
            $campoPOS2 = strpos($string, '"', $campoPOS1 + 1);
            $campo = substr($string, $campoPOS1, $campoPOS2 - $campoPOS1);

            /*$condicionPOS = strpos($string, 'CONDICION');
            $condicionPOS1 = strpos($string, '"', $condicionPOS) + 1;
            $condicionPOS2 = strpos($string, '"', $condicionPOS1 + 1);
            $condicion = substr($string, $condicionPOS1, $condicionPOS2 - $condicionPOS1);
            $arrTemp = explode("=", $condicion);
            $nombreCampocondicion = trim(str_replace("=", "", $arrTemp[0]));
            $valorCondicion = trim($arrTemp[1]);
            $arrCondicion = array($nombreCampocondicion => $valorCondicion);*/
            //-mmori- modifico para permitir más de una condición
            $condicionPOS = strpos($string, 'CONDICION');
            $string = substr($string, $condicionPOS + 10);

            if(strpos($string, "AND") != 0)
            {
                $arrTemp1 = explode("AND", $string);
                foreach ($arrTemp1 as $value)
                {
                    $arrTemp = explode("=", $value);
                    $nombreCampocondicion = trim($arrTemp[0], '"\ ');
                    $valorCondicion = trim($arrTemp[1], '"\ ');
                    $arrCondicion[$nombreCampocondicion] = $valorCondicion;
                }
            }
            else
            {
                $arrTemp = explode("=", $string);
                $nombreCampocondicion = trim($arrTemp[0], '"\ ');
                $valorCondicion = trim($arrTemp[1], '"\ ');
                $arrCondicion[$nombreCampocondicion] = $valorCondicion;
            }

            //die(json_encode($arrCondicion));

            $reemplazo = self::getReemplazoDB($conexion, $dataBase, $table, $campo, $arrCondicion);
            $arrCondicion = array();
            $html = substr_replace($html, $reemplazo, $pos - 6, $pos1 - $pos + 9);
            $maximosReemplazos++;
        }
        return true;
    }

//    static function desetiquetarCtaCte(CI_DB_mysqli_driver $conexion, $codCtacte, &$html){
//        $arrCtaCte = Vctacte::listarCtacte($conexion, array("codigo" => $codCtacte), null, null, null, false);
//        formatearCtaCte($conexion, $arrCtaCte);
//        $html = str_replace("[!--CTACTEDESCRIPCION--]", $arrCtaCte[0]['descripcion'], $html);
//        $html = str_replace("[!--CTACTEFECHAVENCIMIENTO--]", $arrCtaCte[0]['fechavenc'], $html);
//        $html = str_replace("[!--CTACTESALDO--]", $arrCtaCte[0]['saldoformateado'], $html);
//        return true;
//    }

    static function desetiquetarCtaCte(CI_DB_mysqli_driver $conexion, $arrctacte, &$html) {
        $reemplazar = '';
        $total = 0;
        $wherein = array(array('campo' => 'codigo', 'valores' => $arrctacte));
        $ctactes = Vctacte::getCtaCte($conexion, null, null, null, null, null, null, $wherein);
        formatearCtaCte($conexion, $ctactes);
        foreach ($ctactes as $rowctacte) {
            $reemplazar.= '<tr><td style=" padding-bottom: 13px; padding-top: 13px;">' . $rowctacte['descripcion'] . '</td>
			<td style=" padding-bottom: 13px; padding-top: 13px;">' . $rowctacte['fechavenc'] . '</td>
			<td style=" padding-bottom: 13px; padding-top: 13px;">' . formatearImporte($rowctacte['saldo'], true, $conexion) . '</td></tr>';
            $total = $total + $rowctacte['saldo'];
        }
        $html = str_replace("[!--DESCRIPCIONCTASCTES--]", $reemplazar, $html);
        $html = str_replace("[!--CTACTETOTAL--]", formatearImporte($total, true, $conexion), $html);
        return true;
    }

    /*
     * Obtiene el HTML para un input de cuota en el template.
     */
    static private function getInputCuota($template_id, $input_id, $cantidad_de_cuotas, $precio_de_cuotas, $observacion, $include_delete_button = false) {
        return '<span class="cuotas_'.$template_id.' cuotas" style="color: #033E8A; display: block; clear: both; margin: 7px 0;">'.
            '<!-- Cuota -->'.
            '<input type="text" maxlength="3" name="CANTIDADDECUOTAS" id="CANTIDADDECUOTAS_'.$template_id.'_'.$input_id.'" value="'.$cantidad_de_cuotas.'" class="cantidaddecuotas cuotas_template_'.$template_id.'" style="font-size: 12px; border: 1px solid red; width: 32px" placeholder="[!--cuotas--]">'.
            '&nbsp;[!--de--]&nbsp;[!--MONEDAPAIS--]&nbsp;'.
            '<input type="text" maxlength="10" name="PRECIODECUOTAS" id="PRECIODECUOTAS_'.$template_id.'_'.$input_id.'" value="'.$precio_de_cuotas.'" class="preciodecuotas cuotas_template_'.$template_id.'" style="font-size: 12px; border: 1px solid red; width: 55px" placeholder="[!--Precio--]">*&nbsp;'.
            '<input type="text" maxlength="42" name="OBSERVACION" class="observacion" style="font-size: 12px; width: 90px;" placeholder="[!--Observacion--]" value="'.$observacion.'">'.
        '</span>';
    }

    /*
     * Obtiene el texto de un grupo de cuotas para reemplazar el campo en
     * el template.
     */
    static private function getTextCuota($cantidad_de_cuotas, $precio_de_cuotas, $observacion) {
        $cuotas_text_index = ($cantidad_de_cuotas > 1) ? '_cuotas' : '_cuota';
        $text = $cantidad_de_cuotas . "&nbsp;[!--".$cuotas_text_index."--]&nbsp;[!--de--]&nbsp;[!--MONEDAPAIS--]&nbsp;" . $precio_de_cuotas;

        if (is_string($observacion) && strlen($observacion)) {
            $text .= "&nbsp;<i>" . $observacion . "</i>";
        }

        //return $cantidad_de_cuotas . "&nbsp;[!--".$cuotas_text_index."--]&nbsp;[!--de--]&nbsp;[!--MONEDAPAIS--]&nbsp;" . $precio_de_cuotas . "<i>" . $observacion . "</i>";
        return $text;
    }

    /*
     * Obtiene el contenido de planes de pagos o el formulario en caso de edicion.
     */
    static private function getPlanesDePagosContent($cantidad_de_cuotas, $precio_de_cuotas, $observacion, $modoInput=false, $template_id=null, $input_id=null, $include_delete_button = false) {
        if ($modoInput) {
            return self::getInputCuota($template_id, $input_id, $cantidad_de_cuotas, $precio_de_cuotas, $observacion, $include_delete_button);
        }

        return self::getTextCuota($cantidad_de_cuotas, $precio_de_cuotas, $observacion);
    }

    /**
     * Desetiqueta los planes de pago en un template.
     *
     * @param  int  $template_id
     * @param  array  $array_default_values
     * @param  string  $html
     * @param  boolean  $modoInput
     *
     * @return boolean
     */
    static function desetiquetarPlanesDePago($template_id, $array_default_values, &$html, $modoInput = true) {
        if (is_array($array_default_values)) {
            $htmls_planes = [];
            
            $cuotas = [];
            if (isset($array_default_values['CUOTAS']) && isset($array_default_values["CUOTAS"][0]) && !is_null($array_default_values["CUOTAS"][0])) {
                $cuotas = html_entity_decode($array_default_values["CUOTAS"][0]);
                $cuotas = json_decode($cuotas);
            }

            if ( !is_array($cuotas) || (is_array($cuotas) && count($cuotas) == 0) ) {
                $cuotas = new stdClass;
                $cuotas->CANTIDADDECUOTAS = '';
                $cuotas->PRECIODECUOTAS = '';
                $cuotas->OBSERVACION = '';

                $cuotas = array(
                    0 => array(
                        $cuotas
                    )
                );
            }
            else // se considera por retrocompatibilidad
            {
                if ( is_object($cuotas[0]) && count($cuotas) === 0 ) {
                    $cuotas = array(
                        0 => $cuotas
                    );
                }
            }

            $cantidad_planes = count($cuotas);
            $current_plan_index = 0;
            foreach ($cuotas as $current_plan_pagos) {
                $html_current_plan = "[!--CUOTAS--]";

                self::desetiquetarCuotas(
                    $template_id,
                    $current_plan_pagos,
                    $html_current_plan,
                    $modoInput
                );

                if ($modoInput) {
                    $htmls_planes[] = '<span class="plan_de_pagos_container" style="display: block; padding: 8px 6px; margin: 10px; color: #033E8A;background-color: aliceblue; border: 1px solid #CACACA;"><div class="cuotas_plan_container">'
                        .$html_current_plan
                        .'</div></span>';
                }
                else
                {
                    /*if ($current_plan_index < ($cantidad_planes - 1)) {
                        $html_current_plan .= '&nbsp;[!--O--]&nbsp;';
                        //$html_current_plan .= '&nbspO&nbsp;';
                    }*/

                    $htmls_planes[] = $html_current_plan;
                }

                $current_plan_index++;
            }

            // Esto es tmp para testing:
            $htmls_cuotas = "";
            foreach ($htmls_planes as $key => $value) {
                $htmls_cuotas .= $value;
            }
            $html = str_replace("[!--CUOTAS--]", $htmls_cuotas, $html);

            if ($modoInput) {
                $html .= '<script type="text/javascript"> initFormCuotasForTemplate('.$template_id.'); </script>';
            }

            return true;
        }

        return false;
    }


    /**
     * Obtiene los campos para edición de descuentos para un template.
     * 
     * @param  int  
     * @param  array
     * 
     * @return string
     */
    function getDescuentosHTML($template_id, $config = null, $modoInput = false) {
        $html = null;

        if ( is_null($config) ) {
            $config = new stdClass;
            
            $config->presupuesto = new stdClass;
            $config->presupuesto->vencimiento = '';

            $config->matricula = new stdClass;
            $config->matricula->activado = false;
            $config->matricula->porcentaje = '';
            $config->matricula->vencimiento = '';

            $config->curso = new stdClass;
            $config->curso->activado = false;
            $config->curso->porcentaje = '';
            $config->curso->vencimiento = '';
        }

        if ($modoInput) {
            $status_vigencia_presupuesto = (!$config->matricula->activado && !$config->curso->activado) ? '' : 'disabled';
            $status_descuento_matricula = ($config->matricula->activado) ? '' : 'disabled';
            $status_descuento_curso = ($config->curso->activado) ? '' : 'disabled';

            $vigencia_presupuesto = '<span style="float: right; color: #033E8A">
                        [!--Hasta_el--]&nbsp;<input type="text" class="date_picker_templates field_required" name="fecha_limite_precupuesto" style="width: 70px" value="'.$config->presupuesto->vencimiento.'" ' . $status_vigencia_presupuesto . '>
                    </span>';

            $descuento_matricula = '<span style="float: left; color: #033E8A; font-weight: bold;">
                        <input class="checkbox_descuento_matricula checkbox_descuento" type="checkbox" ' . (($config->matricula->activado) ? 'checked' : '') . '> [!--descuento_vigente_sobre_matricula--]:
                    </span>
                    <span style="float: right; color: #033E8A">&nbsp;
                        <input type="text" name="descuento_porcentaje_matricula" class="input_descuento_porcentaje_matricula field_required" required="" value="'.$config->matricula->porcentaje.'" maxlength="3" style="width: 35px; text-align: center;" '.$status_descuento_matricula.'>% ([!--hasta_el--] 
                        <input type="text" name="fecha_vencimiento_descuento_matricula" class="date_picker_templates input_descuento_fecha_limite_matricula field_required" value="'.$config->matricula->vencimiento.'" style="width: 70px" '.$status_descuento_matricula.'>)
                    </span>';

            $descuento_curso = '<span style="float: left; color: #033E8A; font-weight: bold;">
                        <input class="checkbox_descuento_curso checkbox_descuento" type="checkbox" ' . (($config->curso->activado) ? 'checked' : '') . '> [!--descuento_vigente_sobre_el_curso--]:
                    </span>
                    <span style="float: right; color: #033E8A">&nbsp;
                        <input type="text" name="descuento_porcentaje_curso" class="field_required" required="" value="'.$config->curso->porcentaje.'" maxlength="3" style="width: 35px; text-align: center;" '.$status_descuento_curso.'>% ([!--hasta_el--] 
                        <input type="text" name="fecha_vencimiento_descuento_curso" class="date_picker_templates field_required" name="fecha_vencimiento_descuento_curso" value="'.$config->curso->vencimiento.'" style="width: 70px" '.$status_descuento_curso.'>)
                    </span>';

            $html = '<tr height="24px;"><td id="descuentos_template_'.$template_id.'" style="background-color: #E1E9F6; padding: 15px;">';
            $html .= $vigencia_presupuesto . "<br>\n" . $descuento_matricula . "<br>\n" . $descuento_curso;
            $html .= '<script type="text/javascript"> initFormDescuentos('.$template_id.'); </script>';
            $html .= '</td></tr>';
        }
        else
        {
            $vigencia_presupuesto = '<span style="float: right; color: #033E8A">
                        [!--Hasta_el--]&nbsp;'.$config->presupuesto->vencimiento.'
                    </span>';

            $descuento_matricula = '<span style="float: left; color: #033E8A; font-weight: bold;">
                        [!--descuento_vigente_sobre_matricula--]:
                    </span>
                    <span style="float: right; color: #033E8A">&nbsp;
                        '.$config->matricula->porcentaje.'% ([!--hasta_el--] '.$config->matricula->vencimiento.')
                    </span>';

            $descuento_curso = '<span style="float: left; color: #033E8A; font-weight: bold;">
                        [!--descuento_vigente_sobre_el_curso--]:
                    </span>
                    <span style="float: right; color: #033E8A">&nbsp;
                        '.$config->curso->porcentaje.'% ([!--hasta_el--] '.$config->curso->vencimiento.')
                    </span>';

            if (!$config->matricula->activado && !$config->curso->activado) {
                $html = '<tr height="24px;"><td style="background-color: #E1E9F6; padding-right: 15px;">';
                $html .= $vigencia_presupuesto . "\n";
                $html .= '</td></tr>';
            }
            else
            {
                $html .= '<tr height="24px;"><td style="background-color: #E1E9F6; padding-right: 15px;">';
                $html .= $descuento_matricula . "\n";
                $html .= '</td></tr>';

                $html .= '<tr height="24px;"><td style="background-color: #E1E9F6; padding-right: 15px;">';
                $html .= $descuento_curso . "\n";
                $html .= '</td></tr>';
            }
        }

        return $html;
    }

    /**
     * Desetiqueta el campo descuentos vigentes.
     * 
     * @param  int  $template_id
     * @param  array  $array_default_values
     * @param  string  $html
     * @param  boolean  $modoInput
     * 
     * @return boolean
     */
    static function desetiquetarDescuentosVigentes($template_id, $array_default_values, &$html, $modoInput = true) {
        $html_descuentos = "";
        $config = null;

        if (isset($array_default_values['DESCUENTOS_VIGENTES'])
            && is_array($array_default_values['DESCUENTOS_VIGENTES'])
            && isset($array_default_values['DESCUENTOS_VIGENTES'][0])
            && !is_null($array_default_values['DESCUENTOS_VIGENTES'][0])
        ) {
            $config = html_entity_decode($array_default_values['DESCUENTOS_VIGENTES'][0]);
            $config = json_decode($config);
        }

        $html_descuentos .= self::getDescuentosHTML($template_id, $config, $modoInput);

        $html = str_replace("[!--DESCUENTOS_VIGENTES--]", $html_descuentos, $html);
        return false;
    }

    /*
     * Obtiene el grupo de cuotas en inputs o directamente en texto
     */
    static private function getCuotasContent($cantidad_de_cuotas, $precio_de_cuotas, $observacion, $modoInput=false, $template_id=null, $input_id=null, $include_delete_button = false) {
        if ($modoInput) {
            return self::getInputCuota($template_id, $input_id, $cantidad_de_cuotas, $precio_de_cuotas, $observacion, $include_delete_button);
        }

        return self::getTextCuota($cantidad_de_cuotas, $precio_de_cuotas, $observacion);
    }

    /**
     * Desetiqueta el campo [!--CUOTAS--] en el template $html utilizando el array $cuotas.
     *
     * @param  int
     * @param  array
     * @param  string
     * @param  boolean
     *
     * @return boolean
     */
    static function desetiquetarCuotas($template_id, $cuotas, &$html, $modoInput = true) {
        $html_cuotas = "";
        
        // Arreglos para mantener retrocompatibilidad
        if (is_object($cuotas) && !isset($cuotas->TITULO) && !isset($cuotas->CUOTAS)) {
            $new_cuotas = new stdClass;
            $new_cuotas->TITULO = '';
            $new_cuotas->CUOTAS = array();

            if (isset($cuotas->CANTIDADDECUOTAS) && isset($cuotas->PRECIODECUOTAS)) {
                if (!isset($cuotas->OBSERVACION)) {
                    $cuotas->OBSERVACION = '';
                }

                $new_cuotas->CUOTAS[] = $cuotas;
            }

            $cuotas = $new_cuotas;
        }
        else
        {
            if (is_array($cuotas) && isset($cuotas[0]) && is_object($cuotas[0]) && isset($cuotas[0]->CANTIDADDECUOTAS)) {
                $new_cuotas = new stdClass;
                $new_cuotas->TITULO = '';
                $new_cuotas->CUOTAS = $cuotas;

                $cuotas = $new_cuotas;
            }
        }

        if ( is_object($cuotas) && isset($cuotas->TITULO) ) {
            if ($modoInput) {
                $html_cuotas .= '<span style="margin: 10px 0; clear: both; display: inline-block;">[!--Titulo_plan_pagos--]: <input class="titulo_plan_pagos" maxlength="40" value="'.$cuotas->TITULO.'"></input></span>';
            }
            else
            {
                $html_cuotas .= "<br>";
                if (strlen($cuotas->TITULO) > 0) {
                    $html_cuotas .= '<b>'.$cuotas->TITULO.':</b><br>';
                }
            }

            //$html_cuotas .= ($modoInput) ? '<span style="margin: 10px 0; clear: both; display: inline-block;">[!--Titulo_plan_pagos--]: <input class="titulo_plan_pagos" value="'.$cuotas->TITULO.'"></input></span>' : '<br><b>'.$cuotas->TITULO.'</b><br>';
        }
        else
        {
            $html_cuotas .= ($modoInput) ? '<span style="margin: 10px 0; clear: both; display: inline-block;">[!--Titulo_plan_pagos--]: <input class="titulo_plan_pagos" maxlength="40" value=""></input></span>' : '<br>';
        }

        if ( isset($cuotas->CUOTAS) && count($cuotas->CUOTAS) > 0 ) {
            $cuotas = $cuotas->CUOTAS;
            
            $cuotas_count = count($cuotas);
            $last = null;

            // si la cantidad de cuotas es mayor a 1, almacenamos el ultimo grupo de cuotas
            // para concatenarlo fuera del foreach con la cadena " y "
            if ($cuotas_count > 1) {
                $last = array();

                end($cuotas);
                $last["key"] = key($cuotas);
                reset($cuotas);

                $last["CANTIDADDECUOTAS"] = $cuotas[$last["key"]]->CANTIDADDECUOTAS;
                $last["PRECIODECUOTAS"] = $cuotas[$last["key"]]->PRECIODECUOTAS;
                $last["OBSERVACION"] = (isset($cuotas[$last["key"]]->OBSERVACION)) ? $cuotas[$last["key"]]->OBSERVACION : '';

                array_pop($cuotas);
            }

            $is_first = true;
            foreach ($cuotas as $key => $current_cuota) {
                if (!$is_first && !$modoInput) {
                    $html_cuotas .= "<br>";
                }

                if (!isset($current_cuota->OBSERVACION)) {
                    $current_cuota->OBSERVACION = '';
                }

                $html_cuotas .= self::getCuotasContent(
                    $current_cuota->CANTIDADDECUOTAS,
                    $current_cuota->PRECIODECUOTAS,
                    $current_cuota->OBSERVACION,
                    $modoInput,
                    $template_id,
                    $key,
                    ((!$is_first && $modoInput) ? true : false)
                );

                $is_first = false;
            }

            if (!is_null($last)) {
                if ($modoInput) {
                    $html_cuotas .= self::getInputCuota($template_id, $last["key"], $last["CANTIDADDECUOTAS"], $last["PRECIODECUOTAS"], $last["OBSERVACION"], true);
                }
                else
                {
                    $html_cuotas .= "&nbsp;<br>[!--y--]&nbsp;";
                    $html_cuotas .= self::getTextCuota($last["CANTIDADDECUOTAS"], $last["PRECIODECUOTAS"], $last["OBSERVACION"]);
                }
            }

            if (!$modoInput) {
                $html_cuotas .= ".<br>";
            }
        }
        else
        {
            if ($modoInput) {
                $html_cuotas .= self::getCuotasContent("1", "0", $modoInput, $template_id, "0");
            }
        }

        /*
        if ($modoInput) {
            $html_cuotas .= '<script type="text/javascript"> initFormCuotasForTemplate('.$template_id.'); </script>';
        }
        */

        $html_cuotas = str_replace("&nbsp;", " ", $html_cuotas);
        $html = str_replace("[!--CUOTAS--]", $html_cuotas, $html);

        return true;
    }

    static function desetiquetarAlumnos(CI_DB_mysqli_driver $conexion, $codAlumno, &$html) {
        $myAlumno = new Valumnos($conexion, $codAlumno);
        $html = str_replace("[!--ALUMNONOMBRE--]", $myAlumno->apellido . ", " . $myAlumno->nombre, $html);
        return true;
    }

    static function desetiquetarAspirantes(CI_DB_mysqli_driver $conexion, $codAspirante, &$html) {
        $myAspirante = new Vaspirantes($conexion, $codAspirante);
        $html = str_replace("[!--ASPIRANTENOMBRE--]", $myAspirante->nombre . " " . $myAspirante->apellido, $html);
        return true;
    }

    /**
     * desetiqueta sobre etiquetas de datos de filial
     *
     * @param CI_DB_mysqli_driver $conexion
     * @param Vfiliales $myFilial
     * @param string $html
     * @return boolean
     */
    static function desetiquetarDatosFilial(CI_DB_mysqli_driver $conexion, Vfiliales $myFilial = null, &$html = null, $codFilial = null) {
        if ($myFilial == null)
            $myFilial = new Vfiliales($conexion, $codFilial);
        $myCotizacionPais = new Vcotizaciones($conexion, $myFilial->pais);
        $html = str_replace("[!--FILIAL--]", $myFilial->getCodigo(), $html);
        $html = str_replace("[!--FILIALNOMBRE--]", htmlentities($myFilial->nombre), $html);
        $html = str_replace("[!--FILIALDIRECCION--]", htmlentities($myFilial->domicilio), $html);
        $html = str_replace("[!--FILIALTELEFONO--]", $myFilial->telefono, $html);
        $html = str_replace("[!--FILIALEMAIL--]", $myFilial->email, $html);
        $html = str_replace("[!--MONEDAPAIS--]", $myCotizacionPais->simbolo, $html);
        return true;
    }

    static function desetiquetarHINTS(&$html, $modoInput = true) {
        $maximosReemplazos = 0;

        while (strpos($html, '[!--HINT') && $maximosReemplazos < 30) {
            $pos = strpos($html, "[!--HINT");
            $pos1 = strpos($html, "HINT--]", $pos);
            if ($modoInput) {
                $string = substr($html, $pos + 8, $pos1 - $pos - 8);
                $html = substr_replace($html, $string, $pos, $pos1 - $pos + 7);
            } else {
                $html = substr_replace($html, "", $pos, $pos1 - $pos + 7);
            }
        }
        return true;
    }

    static function desetiquetarFacturante(CI_DB_mysqli_driver $conexion, $codFactura, &$html) {
        $myFactura = new Vfacturas($conexion, $codFactura);
        $myFacturante = new Vfacturantes($conexion, $myFactura->cod_facturante);
        $direccion = funciones::formatearDomicilio($myFacturante->direccion_calle, $myFacturante->direccion_numero, $myFacturante->direccion_complemento);
        $myLocalidad = new Vlocalidades($conexion, $myFacturante->cod_localidad);
        $myProvincia = new Vprovincias($conexion, $myLocalidad->provincia_id);
        $myRazonSocial = new Vrazones_sociales($conexion, $myFacturante->cod_razon_social);
        $myCondicionSocial = new Vcondiciones_sociales($conexion, $myRazonSocial->condicion);
        $myTalonario = new Vtalonarios($conexion, $myFactura->cod_tipo_factura, $myFacturante->getCodigo(), $myFactura->punto_venta);
        $html = str_replace("[!--FACTURANTESUCURSAL--]", $myTalonario->comentarios, $html); // se decide que el campo comentarios de talonarios refiere a sucursal
        $html = str_replace("[!--FACTURANTEDIRECCION--]", $direccion, $html);
        $html = str_replace("[!--FACTURANTELOCALIDAD--]", $myLocalidad->nombre, $html);
        $html = str_replace("[!--FACTURANTEPROVINCIA--]", $myProvincia->nombre, $html);
        $html = str_replace("[!--FACTURANTENUMEROIDENTIFICADORFISCAL--]", $myRazonSocial->documento, $html);
        $html = str_replace("[!--FACTURANTECONDICIONFACTURACION--]", $myCondicionSocial->condicion, $html);
        $html = str_replace("[!--FACTURANTEFECHAINICIOACTIVIDADES--]", formatearFecha_pais($myFacturante->inicio_actividades), $html);
        $html = str_replace("[!--FACTURANTERAZONSOCIAL--]", $myRazonSocial->razon_social, $html);
        return true;
    }

    /**
     * Desetiqueta datos de la factura
     *
     * @param CI_DB_mysqli_driver $conexion
     * @param int $codFactura
     * @param string $html
     */
    static function desetiquetarDatosFactura(CI_DB_mysqli_driver $conexion, $codFactura, &$html) {
        $myFactura = new Vfacturas($conexion, $codFactura);
        $myTipoFactura = new Vtipos_facturas($conexion, $myFactura->cod_tipo_factura);
        $html = str_replace("[!--FACTURATIPOFACTURA--]", $myTipoFactura->factura, $html);
        $serie = str_pad($myFactura->punto_venta, 4, "0", STR_PAD_LEFT);
        $nroFactura = str_pad($myFactura->nrofact, 8, "0", STR_PAD_LEFT);
        $serieTalonario = $serie . "-" . $nroFactura;
        $html = str_replace("[!--FACTURATALONARIOSERIE--]", $serieTalonario, $html);
        return true;
    }

    /**
     * desetiqueta sobre etiquetas de entrada de texto (inputs) generando el codigo html del tipo de input maquetado
     *
     * @param int $id                   El id dato a la etiqueta input
     * @param string $html              el html maquetado
     * @param array $arrDefaultValues   array que contiene los valores por defecto en formato ([tipo_etiqueta][numero_etiqueta] = [valor])
     * @return boolean
     */
    static function desetiquetarINPUTS($id, &$html, array $arrDefaultValues = null, $modoInput = true) {
        $resp = self::desetiquetarHINTS($html, $modoInput);
        $resp = $resp && self::desetiquetarINPUTTEXTREQUIRED($id, $html, $arrDefaultValues, $modoInput);
        $resp = $resp && self::desetiquetarINPUTCALENDARREQUIRED($id, $html, $arrDefaultValues, $modoInput);
        $resp = $resp && self::desetiquetarTEXTAREAREQUIRED($id, $html, $arrDefaultValues, $modoInput);
        $resp = $resp && self::desetiquetarSELECT($id, $html, $arrDefaultValues, $modoInput);
        $resp = $resp && self::desetiquetarTEXTAREA($id, $html, $arrDefaultValues, $modoInput);
        $resp = $resp && self::desetiquetarTEXT($id, $html, $arrDefaultValues, $modoInput);

        return $resp;
    }

    static function desetiquetarMd5($md5, &$html = null) {
        $html = str_replace("[!--MD5--]", $md5, $html);
        return true;
    }

    static function desetiquetarLinkCampus($link, &$html = null) {
        $html = str_replace("[!--LINKCAMPUSVIRTUAL--]", $link, $html);
        return true;
    }

    static function desetiquetarDatosReglamento(CI_DB_mysqli_driver $conexion, Vfiliales $myFilial = null, &$html = null, $codFilial = null) {
        if ($myFilial == null) {
            $myFilial = new Vfiliales($conexion, $codFilial);
        }

        $facturante = $myFilial->getFacturantes(true);

        $condiciones = array('codigo' => $facturante[0]['cod_razon_social']);
        $razonessociales = Vrazones_sociales_general::listarRazones_sociales_general($conexion, $condiciones);
        $razon = $razonessociales[0];
        $html = str_replace("[!--RAZONFILIAL--]", $razon['razon_social'], $html);
        $html = str_replace("[!--RAZONDIRECCIONCALLE--]", $razon['direccion_calle'], $html);
        $html = str_replace("[!--RAZONDIRECCIONNRO--]", $razon['direccion_numero'], $html);
        $html = str_replace("[!--RAZONDIRECCIONCOMPLEMENTO--]", $razon['direccion_complemento'], $html);
        $barrio = !isset($razon['barrio']) || $razon['barrio'] == '' || $razon['barrio'] == null ? '' : ', Bairro ' . $razon['barrio'];
        $html = str_replace("[!--RAZONDIRECCIONBARRIO--]", $barrio, $html);
        $myLocalidad = new Vlocalidades($conexion, $razon['cod_localidad']);
        $html = str_replace("[!--RAZONLOCALIDAD--]", $myLocalidad->nombre, $html);
        $myProvincia = new Vprovincias($conexion, $myLocalidad->provincia_id);
        $html = str_replace("[!--RAZONPROVINCIA--]", $myProvincia->nombre, $html);
        $cod_estado = $myProvincia->get_codigo_estado();
        $html = str_replace("[!--RAZONCODESTADO--]", $cod_estado, $html);
        $cod_postal = $razon['codigo_postal'] == '' || $razon['codigo_postal'] == null ? '' : ', CEP ' . $razon['codigo_postal'];
        $html = str_replace("[!--RAZONCODPOSTAL--]", $cod_postal, $html);
        $html = str_replace("[!--RAZONCNPJ--]", $razon['documento'], $html); //ACA
        $myLocalidadFilial = new Vlocalidades($conexion, $myFilial->id_localidad);
        $html = str_replace("[!--FILIALLOCALIDAD--]", $myLocalidadFilial->nombre, $html);
        $myProvinciaFilial = new Vprovincias($conexion, $myLocalidadFilial->provincia_id);
        $cod_estado_filial = $myProvinciaFilial->get_codigo_estado();
        $html = str_replace("[!--FILIALCODESTADO--]", $cod_estado_filial, $html);

        $condiciones = array('cod_facturante' => $facturante[0]['codigo'], 'tipo_factura' => '15', 'estado' => 'habilitado');
        $ptovta = Vpuntos_venta::listarPuntos_venta($conexion, $condiciones);
        $porcentajeProducto = '';
        if (count($ptovta) > 0) {
            $myPuntoVenta = new Vpuntos_venta($conexion, $ptovta[0]['codigo']);
            $porcentajeFacturar = $myPuntoVenta->getPorcentajeFacturar($myFilial->getCodigo());
            $porcentajeProducto = 'O material didático representará ' . $porcentajeFacturar . '% do valor total do curso.';
        }
        $html = str_replace("[!--PORCPRODUCTOS--]", $porcentajeProducto, $html);
        $cod_localidad = Vconfiguracion::getValorConfiguracion($conexion, null, 'localidadContratoForo');
        if ($cod_localidad != 0 && $cod_localidad != null) {
            $myLocalidadForo = new Vlocalidades($conexion, $cod_localidad);
            $localidad_foro = $myLocalidadForo->nombre;
            $myProvinciaForo = new Vprovincias($conexion, $myLocalidadForo->provincia_id);
            $cod_foro = $myProvinciaForo->get_codigo_estado();
        } else {
            $localidad_foro = $myLocalidadFilial->nombre;
            $cod_foro = $cod_estado_filial;
        }
        $html = str_replace("[!--FOROLOCALIDAD--]", $localidad_foro, $html);
        $html = str_replace("[!--FOROCODESTADO--]", $cod_foro, $html);
        return true;
    }

}
