<?php

/**
 * Class Vpos_operadores
 *
 * Class  Vpos_operadores maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vpos_operadores extends Tpos_operadores {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function ProcesarResumen($archivoTemporal, $filial, $nombreArchivoOriginal) {

        $objtarecron = new Vtareas_crons($this->oConnection);
        $objtarecron->guardar('conciliar_cobros', null, $filial);

        switch ($this->codigo) {

            case 2:

                return Vvan_cielo::procesarArchivo($this->oConnection, $archivoTemporal, $filial, $nombreArchivoOriginal);


            default:



                break;
        }
    }

    public function conciliarVenta($nsu, $ca, $terminal, $fecha) {
        switch ($this->codigo) {
            case 2:


                return Vvan_cielo::conciliar($this->oConnection, $nsu, $ca, $terminal, $fecha);

            default:



                break;
        }
    }

    public function getHeaderCV(array $arrSearch = null, array $arrSort = null, array $arrLimit = null) {
        switch ($this->codigo) {
            case 2:
                if ($arrSort != null){
                    foreach ($arrSort as $field => $order){
                        $this->oConnection->order_by($field, $order);
                    }
                }
                if ($arrSearch != null){
                    foreach ($arrSearch as $field => $search){
                        $this->oConnection->or_like($field, $search);
                    }
                }
                if ($arrLimit != null){
                    $this->oConnection->limit($arrLimit[1], $arrLimit[0]);
                }
                $resp = Vvan_cielo_header::listarVan_cielo_header($this->oConnection);
                return $resp;
                break;

            default:
                break;
        }
    }

    public function validarCodigoInternoTerminal($cod_interno) {
        $respuesta = true;

        if ($this->codigo == '2') {// CIELO
            $respuesta = strlen($cod_interno) <= 8 && preg_match("/^([0-9])+$/i", $cod_interno) ? true : false;
        }
        return $respuesta;
    }

    public function validarCuponTerminal($cupon) {
        $respuesta = true;

        if ($this->codigo == '2') {// CIELO
            $respuesta = strlen($cupon) <= 6 && preg_match("/^([a-z0-9])+$/i", $cupon) ? true : false;
        }
        return $respuesta;
    }

    public function validarAutorizacionTerminal($cod_autorizacion) {
        $respuesta = true;

        if ($this->codigo == '2') {// CIELO
            $respuesta = strlen($cod_autorizacion) <= 6 && preg_match("/^([a-z0-9])+$/i", $cod_autorizacion) ? true : false;
        }
        return $respuesta;
    }

    public function getdetalleOperacion($nsu = null, $ca = null, $terminal = null, $valorVenta = null, $tipo_captura = null, $fecha = null) {

        switch ($this->codigo) {
            case 2:


                return Vvan_cielo::conciliar($this->oConnection, $nsu, $ca, $terminal, $valorVenta, $tipo_captura, $fecha);

            default:



                break;
        }
    }

}
