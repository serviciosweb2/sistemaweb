<?php

/**
 * Class Vtalonarios
 *
 * Class  Vtalonarios maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vtalonarios extends Ttalonarios {

    function __construct(CI_DB_mysqli_driver $conexion, $cod_tipo_factura, $cod_facturante, $punto_venta) {
        parent::__construct($conexion, $cod_tipo_factura, $cod_facturante, $punto_venta);
    }

    function incrementarNroFactura() {
        $this->ultimonumero = $this->ultimonumero + 1;
        $this->guardarTalonarios();
    }
    
    
   
   public function setTalonariosUsuarios($cod_usuario){
       $guardar=array(
           "cod_facturante"=>  $this->cod_facturante,
           "cod_usuario"=>$cod_usuario,
           "cod_tipo_factura"=>  $this->codtipofactura,
           "punto_venta"=>  $this->punto_venta
       );
       $this->oConnection->insert('talonarios_usuarios',$guardar);
   }
   
   public function unSetTalonariosUsuarios(){
       $this->oConnection->where('talonarios_usuarios.cod_facturante',  $this->cod_facturante);
         $this->oConnection->where('talonarios_usuarios.cod_tipo_factura',  $this->codtipofactura);
          $this->oConnection->where('talonarios_usuarios.punto_venta',  $this->punto_venta);
        $this->oConnection->delete('talonarios_usuarios');
    }
   
   public function getDetallesPuntoVenta(){
       $this->oConnection->select('talonarios_usuarios.cod_usuario');
       $this->oConnection->from('general.talonarios');
       $this->oConnection->join('talonarios_usuarios','talonarios_usuarios.cod_facturante = general.talonarios.cod_facturante and talonarios_usuarios.cod_tipo_factura = talonarios.codtipofactura and talonarios_usuarios.punto_venta = talonarios.punto_venta');
       $this->oConnection->where('general.talonarios.codtipofactura',  $this->codtipofactura);
       $this->oConnection->where('general.talonarios.cod_facturante',  $this->cod_facturante);
       $this->oConnection->where('general.talonarios.punto_venta',  $this->punto_venta);
       $query = $this->oConnection->get();
       return $query->result_array();
   }

    

}
