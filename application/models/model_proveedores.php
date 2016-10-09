<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_proveedores extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function listarProveedoresDataTable($arrFiltros) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $this->load->helper('alumnos');
        $arrCondiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondiciones = array(
                "razones_sociales.razon_social" => $arrFiltros["sSearch"],
                "proveedores.codigo"=>$arrFiltros["sSearch"],
                "razones_sociales.documento"=>$arrFiltros['sSearch']
            );
        }

        $arrLimit = array();
        if ($arrFiltros["iDisplayStart"] != "" and $arrFiltros["iDisplayLength"] != "") {

            $arrLimit = array(
                "0" => $arrFiltros["iDisplayStart"],
                "1" => $arrFiltros["iDisplayLength"]
            );
        }

        $arrSort = array();

        if ($arrFiltros["SortCol"] != "" and $arrFiltros["sSortDir"] != "") {

            $arrSort = array(
                "0" => $arrFiltros["SortCol"],
                "1" => $arrFiltros["sSortDir"]
            );
        }

        $datos = Vproveedores::listarProveedoresDataTable($conexion, $arrCondiciones, $arrLimit, $arrSort);
        $contar = Vproveedores::listarProveedoresDataTable($conexion, $arrCondiciones, "", "", true);

        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row['codigo'],
                inicialesMayusculas($row['nombre']),
               inicialesMayusculas($row['direccion']),
                $row['identificacion'],
                $row['telefono'],
                $row['email'],
                $row['descripcion'],                
                $row['baja']
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getProveedores($solobaja) {
        $conexion = $this->load->database($this->codigo_filial, true);
        
        $proveedores = Vproveedores::listarProveedores_razones($conexion, $solobaja);
        return $proveedores;
    }

    public function getProvedor($cod_proveedor) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objProveedor = new Vproveedores($conexion, $cod_proveedor);
        return $objProveedor;
    }
    public function getDatosProveedores($cod_proveedor){
        $conexion = $this->load->database($this->codigo_filial, true);
        $myProveedor = new Vproveedores($conexion, $cod_proveedor);
        $arrProveedor = $myProveedor->getDatosProveedores();
        return $arrProveedor;
    }

    public function getTelefonosProveedor($cod_proveedor) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objProveedor = new Vproveedores($conexion, $cod_proveedor);
        $telProveedor = $objProveedor->getProveedoresTelefonos();
        return $telProveedor;
    }

    public function getRazonesProveedores($cod_proveedor) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $objProveedor = new Vproveedores($conexion, $cod_proveedor);
        $razonesProveedor = $objProveedor->getRazonesProveedores();
        return $razonesProveedor;
    }

    public function guardar($datos) {

        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();
        $proveedor = new Vproveedores($conexion, $datos['proveedor']['codigo']);
         $proveedor->guardar_proveedores($conexion,$datos);   
            
          
        //GUARDO TELEFONO
        foreach ($datos["telefonos"] as $row) {
            $telefono = new Vtelefonos($conexion, $row["codigo"]);
            $row['baja'] = 0;
            $telefono->setTelefonos($row);
            $telefono->guardarTelefonos();

            //SETEO EL TELEFONO AL PROVEEDOR
            if ($row["codigo"] == -1) {
                $cod_telefono = $telefono->getCodigo();
                
                $proveedor->setTelefonos($cod_telefono, $row['default']); //ver el por default
            }else{
                $default = isset($row['default']) ? 1 : 0;
                $proveedor->updateTelefonos($row["codigo"],$row['default']);
            }
        }
  
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        $respuesta = $proveedor->getCodigo();

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }

    public function cambiarEstado($cod_proveedor) {
        $conexion = $this->load->database($this->codigo_filial, true);
        $conexion->trans_begin();

        $proveedor = new Vproveedores($conexion, $cod_proveedor);
        $baja = $proveedor->baja == '0' ? '1' : '0';
        $proveedor->baja = $baja;
        $proveedor->guardarProveedores();
        
        $myRazon_social = new Vrazones_sociales($conexion, $proveedor->cod_razon_social);
        $estado = $myRazon_social->baja == 0 ? 1 : 0;
        $myRazon_social->baja = $estado;
        $respuesta = $myRazon_social->guardarRazones_sociales();

        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {

            $conexion->trans_commit();
        }

        return class_general::_generarRespuestaModelo($conexion, $estadotran, $respuesta);
    }
    
   

}

