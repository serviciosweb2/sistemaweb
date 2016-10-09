<?php

/**
 * Class Vproveedores
 *
 * Class  Vproveedores maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vproveedores extends Tproveedores {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarProveedoresDataTable(CI_DB_mysqli_driver $conexion, $arrCondiciones = null, $arrLimit = null, $arrSort = null, $contar = false) {
        $conexion->select('proveedores.codigo');
        $conexion->select('razones_sociales.razon_social as nombre');
        $conexion->select('CONCAT(general.documentos_tipos.nombre," ", razones_sociales.documento) as identificacion',false);
        $conexion->select('IFNULL(CONCAT(razones_sociales.direccion_calle, " ", IFNULL(razones_sociales.direccion_numero, " ")," ", IFNULL(razones_sociales.direccion_complemento, " "))," ") as direccion',false);
        $conexion->select('IFNULL(CONCAT("(",telefonos.cod_area, ") ", telefonos.numero)," ") as telefono',false);
        $conexion->select('IFNULL((razones_sociales.email)," ") as email',false);
        $conexion->select('IFNULL((proveedores.descripcion)," ") as descripcion',false);
        $conexion->select('proveedores.baja');
        $conexion->from('proveedores');
        $conexion->join('proveedores_telefonos', 'proveedores_telefonos.cod_proveedor = proveedores.codigo and proveedores_telefonos.default = "1"','left');
        $conexion->join('telefonos', 'telefonos.codigo = proveedores_telefonos.cod_telefono','left');
        $conexion->join('razones_sociales','razones_sociales.codigo = proveedores.cod_razon_social');
        $conexion->join('general.documentos_tipos','general.documentos_tipos.codigo = razones_sociales.tipo_documentos');

        if (count($arrCondiciones) > 0) {
            foreach ($arrCondiciones as $key => $value){
                $conexion->or_like($key, $value);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {

            $conexion->order_by($arrSort['0'], $arrSort['1']);
        }

        if ($contar) {
            return $conexion->count_all_results();
        } else {

            $query = $conexion->get();
            //echo $conexion->last_query();
            return $query->result_array();
        }
    }

    public function getProveedoresTelefonos() {
        $this->oConnection->select('telefonos.*,proveedores_telefonos.*');
        $this->oConnection->from('telefonos');
        $this->oConnection->join('proveedores_telefonos', 'proveedores_telefonos.cod_telefono = telefonos.codigo');
        // $this->oConnection->join('proveedores', 'proveedores.codigo = proveedores_telefonos.cod_proveedor');
        $this->oConnection->where('proveedores_telefonos.cod_proveedor', $this->codigo);
        $query = $this->oConnection->get();
        //echo $this->oConnection->last_query();
        return $query->result_array();
    }

    public function getRazonesProveedores() {
        $this->oConnection->select('*');
        $this->oConnection->from('razones_sociales');
        $this->oConnection->join('proveedores_razones', 'proveedores_razones.cod_razon = razones_sociales.codigo');
        $this->oConnection->join('proveedores', 'proveedores.codigo = proveedores_razones.cod_proveedor');
        $this->oConnection->where('proveedores.codigo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    /**
     * asigna un telefono a un proveedor
     * @access public
     */
    function setTelefonos($codtelefono, $default) {
        $arr = array(
            "cod_proveedor" => $this->codigo,
            "cod_telefono" => $codtelefono,
            "default" => $default
        );
        $this->oConnection->insert('proveedores_telefonos', $arr);
    }
    
    public function updateTelefonos($cod_telefono, $default){
        $array = array(
            "default"=>$default
        );
        $this->oConnection->where('proveedores_telefonos.cod_proveedor',  $this->codigo);
        $this->oConnection->where('proveedores_telefonos.cod_telefono',  $cod_telefono);
        $this->oConnection->update('proveedores_telefonos',$array);
    }
    
   

    function setRazonesSociales($codrazon,$default) {
        $arr = array(
            "cod_proveedor" => $this->codigo,
            "cod_razon" => $codrazon,
            "default"=>$default
        );
        $this->oConnection->insert('proveedores_razones', $arr);
    }
    
    public function updateRazonSocial($cod_razon, $default){
        $array = array(
            "default"=>$default
        );
        $this->oConnection->where('proveedores_razones.cod_proveedor',  $this->codigo);
        $this->oConnection->where('proveedores_razones.cod_razon',  $cod_razon);
        $this->oConnection->update('proveedores_razones',$array);
    }
    
    public function getDatosProveedores(){
        $this->oConnection->select('proveedores.*, razones_sociales.*');
        $this->oConnection->from('proveedores');
        $this->oConnection->join('razones_sociales','razones_sociales.codigo = proveedores.cod_razon_social');
        $this->oConnection->where('proveedores.codigo',  $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }
    
    public function guardar_proveedores($conexion, $datos){
        //Guardo la razon social del proveedor
        $razones = new Vrazones_sociales($conexion, $this->cod_razon_social);
        $arrRazones_sociales = array(
            "razon_social"=>$datos['proveedor']['nombre'],
            "documento"=>$datos['proveedor']['numero_identificacion'],
            "condicion"=> $datos['proveedor']['condicion'],
            "baja"=>0,
            "tipo_documentos"=>$datos['proveedor']['tipo_identificacion'],
            "direccion_calle"=>$datos['proveedor']['calle'],
            "direccion_numero"=>$datos['proveedor']['numero'],
            "direccion_complemento"=>$datos['proveedor']['complemento'],
            "cod_localidad"=>$datos['proveedor']['cod_localidad'],
            "email"=>$datos['proveedor']['email'],
            "fecha_alta"=>date("Y-m-d H:i:s"),
            "usuario_creador"=>$datos['proveedor']['cod_usuario_creador'],
            "inicio_actividades"=>  formatearFecha_mysql($datos['proveedor']['inicio_actividades']),
            "codigo_postal"=>$datos['proveedor']['cod_postal']
        );
            $razones->setRazones_sociales($arrRazones_sociales);
            $razones->guardarRazones_sociales();
            
            $this->baja = 0;
            $this->cod_postal = $datos['proveedor']['cod_postal'];
            $this->cod_razon_social = $razones->getCodigo();
            $this->cod_usuario_creador = $datos['proveedor']['cod_usuario_creador'];
            $this->descripcion = $datos['proveedor']['descripcion'];
            $this->fecha_alta = $datos['proveedor']['fecha_alta'];
            $this->guardarProveedores();
    }
    
    static function listarProveedores_razones(CI_DB_mysqli_driver $conexion, $solobaja){
        $conexion->select('proveedores.codigo, razones_sociales.razon_social as nombre');
        $conexion->from('proveedores');
        $conexion->join('razones_sociales','razones_sociales.codigo = proveedores.cod_razon_social');
        if($solobaja){
            $conexion->where('proveedores.baja',0);
            $conexion->where('razones_sociales.baja',0);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

}
