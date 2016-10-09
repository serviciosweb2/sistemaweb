<?php

/**
 * Class Vctacte_estado_historico
 *
 * Class  Vctacte_estado_historico maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vctacte_estado_historico extends Tctacte_estado_historico {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    private static $motivos = array(
        array("id" => 1, "motivo" => 'ctacte_refinanciada', "visible" => true),
        array("id" => 2, "motivo" => 'ctacte_no_corresponde', "visible" => true),
        array("id" => 3, "motivo" => 'baja_ctacte_asociada', "visible" => false),
        array("id" => 4, "motivo" => 'baja_matricula', "visible" => false),
        array("id" => 5, "motivo" => 'refinanciacion', "visible" => false),
        array("id" => 6, "motivo" => 'perdida_descuento', "visible" => false));

    static function getmotivos( $index = false, $visible = true, $id = null) {
        if ($id != null) {
            $arrMotivos = self::$motivos;
            foreach ($arrMotivos as $motivo) {
                if ($motivo['id'] == $id) {
                    return $motivo['motivo'];
                }
            }
        } else {
            $retorno = array();
            if ($index !== false) {
                return self::$motivos[$index];
            } elseif ($visible) {
                foreach (self::$motivos as $value) {
                    if ($value['visible']) {
                        $retorno[] = $value;
                    }
                }
                return $retorno;
            } else {
                return self::$motivos;
            }
        }
    }

}
