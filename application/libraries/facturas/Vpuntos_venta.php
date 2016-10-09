<?php

/**
* Class Vpuntos_venta
*
*Class  Vpuntos_venta maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vpuntos_venta extends Tpuntos_venta{

    static private $estadoHabiliatdo = "habilitado";
    static private $estadoInhabilitado = "inhabilitado";
    
    static private $medioElectronico = "electronico";
    static private $medioConvencional = "convencional";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    public function utilizaWebServices(){
        return $this->medio == self::$medioElectronico && ($this->tipo_factura == 15 || $this->tipo_factura == 16);
    }
    
    public function getMetodoFacturacion($codFilial){
        if ($this->tipo_factura == 15){
            $metodos_configuracion = Vfiliales_metodos_facturacion::get_metodos_facturacion_producto($this->oConnection, array($codFilial));
        } else if ($this->tipo_factura == 16){
            $metodos_configuracion = Vfiliales_metodos_facturacion::get_metodos_facturacion_servicio($this->oConnection, array($codFilial));
        }
        return isset($metodos_configuracion[0]) && isset($metodos_configuracion[0]['proveedor'])
                    ? $metodos_configuracion[0]['proveedor']
                    : "";
    }
    
    public function getPorcentajeFacturar($codFilial){
        $metodos_configuracion = array();
        if ($this->tipo_factura == 15){
            $metodos_configuracion = Vfiliales_metodos_facturacion::get_metodos_facturacion_producto($this->oConnection, array($codFilial));
        } else if ($this->tipo_factura == 16){
            if ($this->medio == Vpuntos_venta::getMedioElectronico()){
                $metodos_configuracion = Vfiliales_metodos_facturacion::get_metodos_facturacion_servicio($this->oConnection, array($codFilial));
            } else {
                $metodos_configuracion[0]['porcentaje'] = '';
            }
        }
        
        if (isset($metodos_configuracion[0]) && isset($metodos_configuracion[0]['proveedor'])){
            $myPrestador = $this->getConfiguracionFacturacionElectronica($metodos_configuracion[0]['proveedor']);
            $porcentajeFacturar = isset($myPrestador->porcentaje_facturar) ? $myPrestador->porcentaje_facturar : 100;
        } else if (isset($metodos_configuracion[0]) && isset($metodos_configuracion[0]['porcentaje'])){
            $porcentajeFacturar = '';
        } else {
            $porcentajeFacturar = 100;  // cambiar cuando exista otro metodo de facturacion no definido
        }
        return $porcentajeFacturar;
    }
    
    public function incrementarNumero(){
        $this->nro ++;
        return $this->guardarPuntos_venta();
    }
    
    public function setFiliales(array $arrCodFiliales){
        $this->oConnection->where("cod_punto_venta", $this->codigo);
        $resp = $this->oConnection->delete("general.puntos_venta_filiales");
        foreach ($arrCodFiliales as $filial){
            $resp = $resp && $this->oConnection->insert("general.puntos_venta_filiales", array("cod_punto_venta" => $this->codigo, "cod_filial" => $filial));
        }
        return $resp;
    }
    
    public function getFiliales(){
        $this->oConnection->select("cod_filial");
        $this->oConnection->from("puntos_venta_filiales");
        $this->oConnection->where("cod_punto_venta", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function getUsuariosHabilitados(){
        $this->oConnection->select("cod_usuario");
        $this->oConnection->from("usuarios_puntos_venta");
        $this->oConnection->where("cod_punto_venta", $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function habilitarUsuario($codUsuario){
        $this->oConnection->select("*");
        $this->oConnection->from("usuarios_puntos_venta");
        $this->oConnection->where("cod_punto_venta", $this->codigo);
        $this->oConnection->where("cod_usuario", $codUsuario);
        $query = $this->oConnection->get();
        $temp = $query->result_array();
        if (count($temp) == 0){
            return $this->oConnection->insert("usuarios_puntos_venta", array("cod_punto_venta" => $this->codigo, "cod_usuario" => $codUsuario));
        } else {
            return true;
        }
    }
    
    public function deshabilitarUsuario($codUsuario){
        $this->oConnection->where("cod_punto_venta", $this->codigo);
        $this->oConnection->where("cod_usuario", $codUsuario);
        return  $this->oConnection->delete("usuarios_puntos_venta");
    }
    
    public function getConfiguracionFacturacionElectronica($proveedor){
        $nombreTabla = "";
        $className = "";
        switch ($proveedor){
            case Vfiliales_metodos_facturacion::getProveedorProducto():
                $nombreTabla = "prestador_toolsnfe";
                $className = "Vprestador_toolsnfe";
                break;
            
            case Vfiliales_metodos_facturacion::getProveedorDSF():
                $nombreTabla = "prestador_dsf";
                $className = "Vprestador_dsf";
                break;
            
            case Vfiliales_metodos_facturacion::getProveedorAbrasf():
                $nombreTabla = "prestador_abrasf";
                $className = "Vprestador_abrasf";
                break;
            
            case Vfiliales_metodos_facturacion::getProveedorGinfes():
                $nombreTabla = "prestador_ginfes";
                $className = "Vprestador_ginfes";
                break;

            case Vfiliales_metodos_facturacion::getProveedorPaulistana():
                $nombreTabla = "prestador_paulistana";
                $className = "Vprestador_paulistana";

                break;

            case Vfiliales_metodos_facturacion::getProveedorNoFactura():
                $nombreTabla = "prestador_no_factura";
                $className = "Vprestador_no_factura";
                break;
            default:
                $nombreTabla = "";
                $className = "";
                break;
        }
        if ($nombreTabla == ""){
            throw new Exception("No existe proveedor asociado al metodo de facturacion");
        } else {
            $this->oConnection->select("codigo");
            $this->oConnection->from("general.".$nombreTabla);
            $this->oConnection->where("cod_punto_venta", $this->codigo);
            $query = $this->oConnection->get();
            $arrConfig = $query->result_array();
            $codigo = count($arrConfig) > 0 ? $arrConfig[0]['codigo'] : null;
            //echo "<pre>".var_dump($this->oConnection->last_query()) ."</pre>";
            return new $className ($this->oConnection, $codigo);
        }
    }
    
   static function getPuntosVentas(CI_DB_mysqli_driver $conexion, $codFilial, $estado=null){
        $conexion->select("COUNT(general.puntos_venta_filiales.cod_filial)");
        $conexion->from("general.puntos_venta_filiales");
        $conexion->where("general.puntos_venta_filiales.cod_punto_venta = general.puntos_venta.codigo");
        $sqCantidadFiliales = $conexion->return_query();
        $conexion->resetear();        
        $conexion->select('general.tipos_facturas.codigo AS cod_tipo_factura');
        $conexion->select('general.tipos_facturas.factura');
        $conexion->select('general.razones_sociales_general.razon_social');
        $conexion->select('general.puntos_venta.codigo AS punto_venta');
        $conexion->select('general.puntos_venta.nro AS ultimonumero');
        $conexion->select('general.puntos_venta.cod_facturante');
        $conexion->select("general.puntos_venta.prefijo");
        $conexion->select("($sqCantidadFiliales) AS cantidad_filiales", false);
        $conexion->from('general.puntos_venta');
        $conexion->join('general.tipos_facturas','general.tipos_facturas.codigo = general.puntos_venta.tipo_factura');
        $conexion->join('general.facturantes','general.facturantes.codigo = general.puntos_venta.cod_facturante');
        $conexion->join('general.razones_sociales_general','general.razones_sociales_general.codigo = general.facturantes.cod_razon_social');
        $conexion->join("general.puntos_venta_filiales", "general.puntos_venta_filiales.cod_punto_venta = general.puntos_venta.codigo AND general.puntos_venta_filiales.cod_filial = $codFilial");
        $estado = $estado == null ? "habilitado" : "inhabilitado";
        $conexion->where("general.puntos_venta.estado", $estado);
        $conexion->where("general.tipos_facturas.comprobante", 'factura');//no muestra notas de credito
        $conexion->group_by('general.tipos_facturas.codigo');
        $conexion->group_by('general.facturantes.codigo');
        $conexion->group_by('general.puntos_venta.codigo');
        $query = $conexion->get();
        return $query->result_array();
   }
    
   static function getMedioElectronico(){
       return self::$medioElectronico;
   }
   
   static function getMedioConvencional(){
       return self::$medioConvencional;
   }
   
   static function getEstadoHabilitado(){
       return self::$estadoHabiliatdo;
   }
   
   static function getEstadoInhabilitado(){
       return self::$estadoInhabilitado;
   }
}

?>