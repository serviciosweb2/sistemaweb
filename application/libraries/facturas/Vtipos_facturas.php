<?php

/**
 * Class Vtiposfacturas
 *
 * Class  Vtiposfacturas maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vtipos_facturas extends Ttipos_facturas {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

//    public function getTalonarioActivo($tipo, $facturante) {
//        $condiciones = array(
//            'codtipofactura' => $tipo,
//            'activo' => 1,
//            'cod_facturante' => $facturante
//        );
//
//        $talonario = Vtalonarios::listarTalonarios($this->oConnection, $condiciones);
//        return $talonario[0]['codigo'];
//    } NO VA PORQUE TALONARIOS 


    static function getTiposFacturantePtoVta(CI_DB_mysqli_driver $conexion, $codfacturante, $ptoventa, $codusuario = null, $tipohabilitado = 1, $talhabilitado = 1) {//o gettalonarios
        $conexion->select('* ,(general.talonarios.ultimonumero + 1) AS nroproxfact ');
        $conexion->from('general.tipos_facturas');
        $conexion->join('general.talonarios', 'general.talonarios.codtipofactura = general.tipos_facturas.codigo');
        $conexion->where('general.talonarios.cod_facturante', $codfacturante);
        $conexion->where('general.talonarios.punto_venta', $ptoventa);
        $conexion->where('general.tipos_facturas.habilitado', $tipohabilitado);
        $conexion->where('general.talonarios.activo', $talhabilitado);
        if ($codusuario != null) {
            $conexion->join('talonarios_usuarios', 'talonarios_usuarios.cod_facturante = general.talonarios.cod_facturante AND talonarios_usuarios.cod_tipo_factura = general.talonarios.codtipofactura AND talonarios_usuarios.punto_venta = general.talonarios.punto_venta');
            $conexion->where('talonarios_usuarios.cod_usuario', $codusuario);
        }

        $query = $conexion->get();
        return $query->result_array();
    }

}
