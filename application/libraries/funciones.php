<?php

class funciones {
    /* PRIVATE FUNCTION */

    /* PUBLIC FUNCTIONS */

    /* STATIC FUNCTIONS */

    /**
     * Retorna un domicilio en formato listo para ser impreso
     * 
     * @param string $calle
     * @param string $numero
     * @param string $complemento
     * @return string
     */
    static function formatearDomicilio($calle, $numero, $complemento = null) {
        $retorno = "$calle";
        if ($numero != '0')
            $retorno .= " $numero";
        if ($complemento != null && trim($complemento) && $complemento != '0')
            $retorno .= " ($complemento)";
        return $retorno;
    }

    /**
     * Retorna un documento en formato listo para ser impreso (agregando el tipo delante del numero si es que corresponde)
     * 
     * @param CI_DB_mysqli_driver $conexion
     * @param integer $tipo
     * @param string $numero
     * @return string
     */
    static function formatearDocumentos(CI_DB_mysqli_driver $conexion, $tipo, $numero) {
        $conexion->select("general.documentos_tipos.nombre");
        $conexion->from("general.documentos_tipos");
        $conexion->where("general.documentos_tipos.codigo", $tipo);
        $query = $conexion->get();
        $arrTemp = $query->result_array();
        if (isset($arrTemp[0]['nombre'])) {
            return "{$arrTemp[0]['nombre']} $numero";
        } else {
            return $numero;
        }
    }

    static function formatearNumeroDocumneto($numeroDocumento, $tipoDocumneto){
        switch ($tipoDocumneto) {
            case "23":
                $documento = $numeroDocumento;
                $documento = str_replace(array(".", "-"), "", $documento);
                $documento = substr($documento, 0, strlen($documento) - 1)."-".substr($documento, strlen($documento) - 1);
                break;

            default:
                $documento = $numeroDocumento;
                break;
        }
        return $documento;
    }
    
}
