<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_sincronizacion extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function sincronizarTablas() {
        $conexion = $this->load->database("default", true);
        $conexion->trans_begin();

        $arrversiones = Vsincronizacion::getVersionesSincronizacion($conexion);
        $arrBD = array();
        foreach ($arrversiones as $rowversion) {
            $arrBD[$rowversion['base_datos']] = $rowversion['cod_sincronizacion'];
        }
        $url = "http://iga-la.com/webservice/cloud.php";
        $postData = array('procedimiento' => 'sincronizar_cloud', 'parametros' => json_encode($arrBD));
        /* Convierte el array en el formato adecuado para cURL */

        $elements = '';
        foreach ($postData as $name => $value) {
            $elements .= "{$name}=" . urlencode($value);
            $elements.='&';
        }
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $elements);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($handler);
        curl_close($handler);

        $sincronizar = json_decode($response, true);

        foreach ($sincronizar as $bd => $datos) {
            if (count($datos) > 0) {
                foreach ($datos as $rowdato) {
                    $nbreinstancia = 'V' . $rowdato['tabla'];
                    $objeto = new $nbreinstancia($conexion, $rowdato['key_sinc']);
                    $insert = $objeto->getCodigo() == '-1' ? true : false;
                    
                    $setear = 'set' . ucfirst($rowdato['tabla']);
                    $arrsetear = array($objeto, $setear);
                    call_user_func($arrsetear, $rowdato['sincronizar']);
                    
                    if($insert){
                        $objeto->insertSincronizacion();
                    }  else {
                        $objeto->updateSincronizacion();
                    }
//                    $guardar = 'guardar' . ucfirst($rowdato['tabla']);
//                    $arrguardar = array($objeto, $guardar);
//                    call_user_func($arrguardar);
                }
                Vsincronizacion::setUltimaSincronizacion($conexion, $bd, $rowdato['codigo']);
            }
        }


        if ($conexion->trans_status()) {
            $conexion->trans_commit();
        } else {
            $conexion->trans_rollback();
        }
    }

}
