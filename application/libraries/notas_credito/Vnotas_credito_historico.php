<?php

/**
 * Class Vnotas_credito_historico
 *
 * Class  Vnotas_credito_historico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vnotas_credito_historico extends Tnotas_credito_historico {

    private static $motivos = array(
        array("id" => '1', "motivo" => 'error_nc'),
        array("id" => '2', "motivo" => 'no_corresponde_nc'),
        array("id" => '3', "motivo" => 'no_autorizada_nc'));

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
                            'motivo' => lang($value['motivo'])
                        );
                    }
                }
            }
        } else {

            $motivos = self::$motivos;

            foreach ($motivos as $key => $motivo) {
                $motivos[$key] = array('id' => $motivo['id'], 'motivo' => lang($motivo['motivo']));
            }

            return $motivos;
        }
        return $devolver;
    }

}
