<?php

/**
 * Created by PhpStorm.
 * User: damian
 * Date: 24/08/16
 * Time: 16:17
 */
class Tgrupos extends class_general {
    protected $id;
    public $nombre;
    public $descripcion;
    public $estado;
    public $id_usuario_administrador;
    public $idioma;
    public $responsables_acceden_admin;
    public $tipo;
    public $id_agrupacion;
    public $id_grupo_cabecera;
    public $orden_agrupado;
    public $dato_adicional;
    public $id_curso_externo;
    public $materia_codigo;
    public $nombre_agrupacion;

    protected $primaryKey = "id";
    protected $nombreTabla = "general.grupos_plataforma_educativa";


    public function __construct(CI_DB_mysqli_driver $conexion, $id = null) {
        $this->oConnection = $conexion;

        if ($id != null && $id != -1){
            $arrConstructor = $this->_constructor($id);
            if (count($arrConstructor) > 0){
                $this->id = $arrConstructor[0]['id'];
                $this->nombre = $arrConstructor[0]['nombre'];
                $this->descripcion = $arrConstructor[0]['descripcion'];
                $this->estado = $arrConstructor[0]['estado'];
                $this->id_usuario_administrador = $arrConstructor[0]['id_usuario_administrador'];
                $this->idioma = $arrConstructor[0]['idioma'];
                $this->responsables_acceden_admin = $arrConstructor[0]['responsables_acceden_admin'];
                $this->tipo = $arrConstructor[0]['tipo'];
                $this->id_agrupacion = $arrConstructor[0]['id_agrupacion'];
                $this->id_grupo_cabecera = $arrConstructor[0]['id_grupo_cabecera'];
                $this->orden_agrupado = $arrConstructor[0]['orden_agrupado'];
                $this->dato_adicional = $arrConstructor[0]['dato_adicional'];
                $this->id_curso_externo = $arrConstructor[0]['id_curso_externo'];
                $this->materia_codigo = $arrConstructor[0]['materia_codigo'];
                $this->nombre_agrupacion = $arrConstructor[0]['nombre_agrupacion'];
            }
            else {
                $this->id = -1;
            }
        }
        else {
            $this->id = -1;
        }
    }

    protected function _getArrayDeObjeto(){
        $arrTemp = array();
        $arrTemp['nombre'] = $this->nombre == '' ? null : $this->nombre;
        $arrTemp['descripcion'] = $this->descripcion == '' ? null : $this->descripcion;
        $arrTemp['estado'] = $this->estado == '' ? null : $this->estado;
        $arrTemp['id_usuario_administrador'] = $this->id_usuario_administrador == '' ? null : $this->id_usuario_administrador;
        $arrTemp['idioma'] = $this->idioma == '' ? null : $this->idioma;
        $arrTemp['responsables_acceden_admin'] = $this->responsables_acceden_admin == '' ? null : $this->responsables_acceden_admin;
        $arrTemp['tipo'] = $this->tipo == '' ? null : $this->tipo;
        $arrTemp['id_agrupacion'] = $this->id_agrupacion == '' ? null : $this->id_agrupacion;
        $arrTemp['id_grupo_cabecera'] = $this->id_grupo_cabecera == '' ? null : $this->id_grupo_cabecera;
        $arrTemp['orden_agrupado'] = $this->orden_agrupado == '' ? null : $this->orden_agrupado;
        $arrTemp['dato_adicional'] = $this->dato_adicional == '' ? null : $this->dato_adicional;
        $arrTemp['id_curso_externo'] = $this->id_curso_externo == '' ? null : $this->id_curso_externo;
        $arrTemp['materia_codigo'] = $this->materia_codigo;
        $arrTemp['nombre_agrupacion'] = $this->nombre_agrupacion == '' ? null : $this->nombre_agrupacion;
        return $arrTemp;
    }

    public function guardarGrupos(){
        return $this->_guardar();
    }

    public function getIdGrupo(){
        return $this->_getId();
    }

    public function setGrupos(array $arrCamposValores){
        $retorno = "";

        if(!isset($arrCamposValores['nombre'])) {
            $retorno = "nombre";
        }
        else if(!isset($arrCamposValores['descripcion'])) {
            $retorno = "descripcion";
        }
        else if(!isset($arrCamposValores['estado'])) {
            $retorno = "estado";
        }
        else if(!isset($arrCamposValores['id_usuario_administrador'])) {
            $retorno = "id_usuario_administrador";
        }
        else if(!isset($arrCamposValores['idioma'])) {
            $retorno = "idioma";
        }
        else if(!isset($arrCamposValores['responsables_acceden_admin'])) {
            $retorno = "responsables_acceden_admin";
        }
        else if(!isset($arrCamposValores['tipo'])) {
            $retorno = "tipo";
        }
        else if(!isset($arrCamposValores['id_agrupacion'])) {
            $retorno = "id_agrupacion";
        }
        else if(!isset($arrCamposValores['id_grupo_cabecera'])) {
            $retorno = "id_grupo_cabecera";
        }
        else if(!isset($arrCamposValores['orden_agrupado'])) {
            $retorno = "orden_agrupado";
        }
        else if(!isset($arrCamposValores['dato_adicional'])) {
            $retorno = "dato_adicional";
        }
        else if(!isset($arrCamposValores['id_curso_externo'])) {
            $retorno = "id_curso_externo";
        }
        else if(!isset($arrCamposValores['materia_codigo'])) {
            $retorno = "materia_codigo";
        }
        else if(!isset($arrCamposValores['nombre_agrupacion'])) {
            $retorno = "nombre_agrupacion";
        }

        if ($retorno <> ""){
            die("falta el parametro ".$retorno." en setGrupos");
        } else {
            foreach ($this as $key => $value){
                if (isset($arrCamposValores[$key])){
                    $this->$key = $arrCamposValores[$key];
                }
            }
            return true;
        }
    }

    static function camposGrupos(CI_DB_mysqli_driver $conexion){
        return parent::_campos($conexion, "general.grupos_plataforma_educativa");
    }

    static function listarGrupos(CI_DB_mysqli_driver $conexion, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false){
        return parent::_listar($conexion, "general.grupos_plataforma_educativa", $condiciones, $limite, $orden, $grupo, $contar);
    }
}