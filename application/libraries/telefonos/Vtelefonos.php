<?php

/**
 * Class Vtelefonos
 *
 * Class  Vtelefonos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vtelefonos extends Ttelefonos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    private static $array = array(
        array('id' => 'fijo', 'nombre' => 'FIJO'),
        array('id' => 'celular', 'nombre' => 'CELULAR'),
    );

    public static function getArray($id = false) {

        $devolver = '';
        if ($id != false) {
            $array = self::$array;
            foreach ($array as $value) {
                foreach ($id as $tipoTel) {
                    if ($value['id'] == $tipoTel) {

                        $devolver[] = array(
                            'id' => $value['id'],
                            'nombre' => lang($value['id'])
                        );
                    }
                }
            }
        } else {
            $tiposTelefono = self::$array;
            foreach ($tiposTelefono as $key => $tipoTelefono) {
                $tiposTelefono[$key]['nombre'] = lang($tipoTelefono['id']);
            }
            return $tiposTelefono;
        }
        //print_r($devolver);
        return $devolver;
    }

    /**
     * Retorna un telefono en formato listo para ser impreso
     * 
     * @param array $arrTelefono   Un array con el formato como se recupera de la BD (puede contener o no el indice 0)
     * @return string
     */
    static function formatearNumero($arrTelefono) {
        if (isset($arrTelefono[0]))
            $arrTelefono = $arrTelefono[0];
        $codigoArea = isset($arrTelefono['cod_area']) && $arrTelefono['cod_area'] != '0' ? $arrTelefono['cod_area'] : '';
        $numero = isset($arrTelefono['numero']) ? $arrTelefono['numero'] : '';
        return "{$codigoArea} {$numero}";
    }

}
