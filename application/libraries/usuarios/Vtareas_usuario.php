<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Vtareas_usuario extends Ttareas_usuario{
     function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    /**
     * Asigna uno o varios usuarios a la tarea
     * 
     * @param mixed $usuario (array o integer) el/los usuarios a asignar
     * @return boolean
     */
    public function setUsuario($usuario){
        if (is_array($usuario)){
            $resp = true;
            foreach ($usuario as $usr){
                $resp = $resp && $this->oConnection->insert("tareas_usuario_usuarios", array("cod_tarea" => $this->codigo, "cod_usuario" => $usr));
            }
            return $resp;
        } else {
            return $this->oConnection->insert("tareas_usuario_usuarios", array("cod_tarea" => $this->codigo, "cod_usuario" => $usuario));
        }
    }
 
    static public function listar(CI_DB_mysqli_driver $conexion, $codigoUsuario = null, $estado = null){
        $conexion->select("tareas_usuario.*");
        $conexion->from("tareas_usuario");
        if ($codigoUsuario != null){
            $conexion->join("tareas_usuario_usuarios", "tareas_usuario_usuarios.cod_tarea = tareas_usuario.codigo");
            if (is_array($codigoUsuario)){
                $conexion->where_in("tareas_usuario_usuarios.cod_usuario", $codigoUsuario);
            } else {
                $conexion->where("tareas_usuario_usuarios.cod_usuario", $codigoUsuario);
            }
        }
        if ($estado != null){
            if (is_array($estado)){
                $conexion->where_in("tareas_usuario.estado", $estado);
            } else {
                $conexion->where("tareas_usuario.estado", $estado);
            }
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
}