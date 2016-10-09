<?php

/**
 * Class Vcobro_estado_historico
 *
 * Class  Vcobro_estado_historico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vcobro_estado_historico extends Tcobro_estado_historico {

    private static $motivos = array(
        array("id" => 'motivo_cobro1', "motivo" => 'error alumno seleccionado'),
        array("id" => 'motivo_cobro2', "motivo" => 'error importe cobrado'),
        array("id" => 'motivo_cobro3', "motivo" => 'factura anulada'));

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getmotivos($id = false) {
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

}

?>