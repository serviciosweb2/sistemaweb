<?php

/**
 * Class Vsesiones_afip
 *
 * Class  Vsesiones_afip maneja todos los aspectos de seguimiento_afip
 *
 * @package  SistemaIGA
 * @author  Foox
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vsesiones_afip extends Tsesiones_afip {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public static function getSesionAfipFacturanteActiva(CI_DB_mysqli_driver $conexion, $cod_facturante) {
        $condiciones = array(
                             'cod_facturante' => $cod_facturante,
                             // Es necesario corregir el desfasaje horario entre MySQL y PHP producido por el uso indebido de date_default_timezone_set. De ahí el -date('Z').
                             'expirationTime >' => date('Y-m-d H:i:s')
                             );
        return self::listarSesiones_afip($conexion, $condiciones, array(0, 1), array(array('campo' => 'expirationTime', 'orden' => 'DESC')));
    }
    
    public static function iniciarSesionAfip(CI_DB_mysqli_driver $conexion, $cod_facturante, $certificado, $testing=true) {
        $respuesta = null;
        $config = array(
                        'testing'       => $testing,
                        'str_crt'       => $certificado->cert,
                        'str_pkey'      => $certificado->pry_key,
                        'tra_tpl_file'  => '/tmp/tra_%s.xml',
                        'file_wsdl'     => 'application/libraries/facturas/PhpWsAfip/tmp/wsaa_wsdl.xml'
                        );
        $wsaa = new WSAA($config);
        $ta = $wsaa->requestTa();
        
        if (!is_null($ta)) {
            $sesion = new self($conexion);
            $sesion->cod_facturante = $cod_facturante;
            // Es necesario corregir el desfasaje horario entre MySQL y PHP producido por el uso indebido de date_default_timezone_set. De ahí el -date('Z').
            $sesion->generationTime = date('Y-m-d H:i:s', strtotime($ta->header->generationTime));
            $sesion->expirationTime = date('Y-m-d H:i:s', strtotime($ta->header->expirationTime));
            $sesion->token          = (string)$ta->credentials->token;
            $sesion->sign           = (string)$ta->credentials->sign;
            $sesion->uniqueId       = (int)$ta->header->uniqueId;
            
            if ($sesion->guardarSesiones_afip()) {
                $respuesta = $sesion;
            }
        }
        
        return $respuesta;
    }
}

?>