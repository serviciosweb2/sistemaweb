<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Vfacturas_estado_historicos extends Tfacturas_estado_historicos{
    
    
    private static $motivos = array(
        array("id" => 'motivo_factura1', "motivo" => 'Error alumno facturado.'),
        array("id" => 'motivo_factura2', "motivo" => 'Error en monto facturado.'));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getMotivos($id = false) {
        $devolver = '';
        if ($id != false) {
            $array = self::$motivos;
            foreach ($array as $value) {
                foreach ($id as $tipoMotivo) {
                    if ($value['id'] == $tipoMotivo) {

                        $devolver[] = array(
                            'id' => $value['id'],
                            'motivo' => lang($value['id'])
                        );
                    }
                }
            }
        } else {

            $motivos = self::$motivos;

            foreach ($motivos as $key => $motivo) {
                $motivos[$key] = array('id' => $motivo['id'], 'motivo' => lang($motivo['id']));
            }

            return $motivos;
        }
        //print_r($devolver);
        return $devolver;
    }

    static function getMotivoId($motivo) {
        $id_motivo = null;
        for ($i=0; count(self::$motivos) > $i; $i++) {
            if (self::$motivos[$i]['id'] == trim($motivo)) {
                return $i;
            }
        }
    }
}


