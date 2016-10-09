<?php

class Vtickets{
    
    private $id;
    public $id_usuario_igacloud;
    public $nombre;
    public $descripcion;
    
    /* CONSTRUCTOR */
    
    function __construct(wsc $wsc = null, $id = null) {
        if ($id != null){           
            $arrTemp = self::listar($wsc, null, null, null, null, null, null, null, null, null, null, null, $id);
            if (is_array($arrTemp) && isset($arrTemp['transport'], $arrTemp['transport']['aaData'], $arrTemp['transport']['aaData'][0])){
                $temp = $arrTemp['transport']['aaData'][0];
                $this->descripcion = $temp['descripcion'];
                $this->id_usuario_igacloud = $temp['id_usuario_iga'];
                $this->nombre = $temp['nombre'];
                $this->id = $temp['id'];
            } else {
                $this->id = -1;
            }
        }
    }
    
    /* PRIVATE FUNCTIONS */
    
    /* PUBLIC FUNCTIONS */
    
    /* STATIC FUNCTIONS */
    
    static public function listar(wsc $wsc = null, $id_filial = null, $seccion = null, $area = null, $prioridad = null, $estado = null,
            $fecha_desde = null, $fecha_hasta = null, $search = null, array $order = null, $limitInf = null,  $limitCant = null, $id = null){
        wsc::validar($wsc);
        $param = array();
        $param['action'] = "get_tickets";
        if ($id != null){
            $param['id'] = $id;
        }
        if ($id_filial != null){
            $param['id_filial'] = $id_filial;
        }
        if ($seccion != null){
            $param['seccion'] = $seccion;
        }
        if ($area != null){
            $param['area'] = $area;
        }
        if ($prioridad != null){
            $param['prioridad'] = $prioridad;
        }
        if ($estado != null){
            $param['estado'] = $estado;
        }
        if ($fecha_desde != null){
            $param['fecha_desde'] = $fecha_desde;
        }
        if ($fecha_hasta != null){
            $param['fecha_hasta'] = $fecha_hasta;
        }
        if ($search != null){
            $param['sSearch'] = $search;
        }
        if ($order != null){
            $param['iSortCol_0'] = $order[0];
            $param['sSortDir_0'] = isset($order[1]) ? $order[1] : "ASC";
        }
        if ($limitInf != null){
            $param['iDisplayStart'] = $limitInf;
        }
        if ($limitCant != null){
            $param['iDisplayLength'] = $limitCant;
        }
        $wsc->set_param($param);
        $arrResp = $wsc->exec();
        return $arrResp;
    }    
}