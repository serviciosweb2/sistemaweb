<?php

/**
 * Class Vusuarios_sistema
 *
 * Class  Vusuarios_sistema maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vusuarios_sistema extends Tusuarios_sistema {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /**
     * Retorna la filial de la conexion
     * @access public
     * @return Array de la filial
     */
    function getFilial() {
        $filial = new Vfiliales($this->oConnection, $this->cod_filial);
        $arrayFilial = (array) $filial;
        $arrayFilial["moneda"] = $filial->getMonedaCotizacion();
        return $arrayFilial;
    }
    
     /**
     * Se aplica cuando un usuario tiene varias filiales bajo su mando
     * @access public
     * @return Array de la filiales
     */
    function getFiliales($id_usu,$conexion) {
        $conexion->select('*');
        $conexion->from('general.usuarios_filiales');
        $conexion->where('general.usuarios_filiales.id_usuario', $id_usu);
        $query = $conexion->get();
        return $query->result_array();
    }

    /**
     * Retorna todos los permisos del usuario
     * @access public
     * @return Array con los permisos del usuario
     */
    function getPermisisosSecciones() {
        if ($this->administra == 1){
            $this->oConnection->select("general.secciones.*, 1 AS habilitado", false);
        } else {
            $this->oConnection->select('count(*)', false);
            $this->oConnection->from('general.usuarios_permisos');
            $this->oConnection->where('general.secciones.codigo = general.usuarios_permisos.id_seccion');
            $this->oConnection->where('general.usuarios_permisos.id_usuario', $this->codigo);
            $subquery = $this->oConnection->return_query();
            $this->oConnection->resetear();
            $this->oConnection->select("general.secciones.*, ($subquery) as habilitado", false);
        }
        $this->oConnection->from("general.secciones");
        $this->oConnection->order_by("general.secciones.id_seccion_padre", "asc");
        $this->oConnection->order_by("general.secciones.prioridad", "desc");
        $query = $this->oConnection->get();


        return $query->result_array();
    }

    /**
     * Retorna los talonarios del usuario
     * @access public
     * @return Array con los talonarios
     */
    public function getPuntosVenta($codFilial) {
        $conexion = $this->oConnection;
        $conexion->select('general.puntos_venta.*', false);
        $conexion->from('general.puntos_venta');
        $conexion->join("usuarios_puntos_venta", "usuarios_puntos_venta.cod_punto_venta = general.puntos_venta.codigo AND usuarios_puntos_venta.cod_usuario = {$this->codigo}");
        $conexion->join("general.puntos_venta_filiales", "general.puntos_venta_filiales.cod_punto_venta = general.puntos_venta.codigo AND general.puntos_venta_filiales.cod_filial = $codFilial");

        $query = $conexion->get();

        $tal = $query->result_array();

        return $tal;
    }

    /**
     * Retorna todos los rubros del usuario
     * @access public
     * @return Array de los rubros
     */
//    public function getRubrosCaja() {
//        $conexion->select('rubros.*');
//        $conexion->from('rubros');
//        $conexion->join('usuarios_rubros', 'usuarios_rubros.codrubro = rubros.codigo');
//        $conexion->where('coduser', $this->codigo);
//        $conexion->where('activo', 1);
//        $query = $conexion->get();
//        $rubro = $query->result_array();
//        return $rubro;
//    }

    /**
     * setea rubro a usuario.
     * @access public
     * return array con los rubros 
     */
    public function setRubros($codrubro) {
        $arrayRubro = array(
            "coduser" => $this->codigo,
            "codrubro" => $codrubro
        );
        return $conexion->insert('usuarios_rubros', $arrayRubro);
    }

    /**
     * setea los permisos al usuario.
     * @access public
     * return array con los permisos
     */
    public function setPermiso($id_usuario) {
        $arrayPermiso = array(
            "id_usuario" => $this->codigo,
            "id_seccion" => $id_seccion
        );
        return $conexion->insert('usuarios_permisos', $arrayPermiso);
    }

    /**
     * setea la caja del usuario
     * @access public
     * return array con los tipo de caja
     */
    public function setCajas($coduser) {
        $arrayCaja = array(
            "coduser" => $this->codigo,
            "codtiposcaja" => $this->codigo
        );
        return $conexion->insert("caja_usuario", $arrayCaja);
    }

    /**
     * retorna la caja del usuario
     * @access public
     * return array con la caja
     */
    public function getCajas($desactivada = null, $abierta = null, $contar = false) {
        $conexion = $this->oConnection;
        $conexion->select('caja.*, caja_usuario.*');
        $conexion->from('caja');
        $conexion->join('caja_usuario', 'caja_usuario.codtiposcaja = caja.codigo');
        $conexion->where('coduser', $this->codigo);
        if ($desactivada != null) {
            $conexion->where('desactivada', $desactivada);
        }
        if ($abierta !== null) {
            if ($abierta == '1') {
                $conexion->where("estado", 'abierta');
            }
            if ($abierta == '0') {
                $conexion->where("estado", 'cerrada');
            }
        }
        $conexion->order_by('default', 'desc');
        if ($contar) {
            return $conexion->count_all_results();
        } else {
            $query = $conexion->get();
            $caja = $query->result_array();
            return $caja;
        }
    }

//    public function getCajasAbrir() {
//        $conexion = $this->oConnection;
//        $caja = new Vcaja($conexion);
//        $conexion->select('caja.*, caja_usuario.*');
//        $conexion->select('(select saldo from movimientos_caja where cod_caja = caja.codigo and cod_concepto <> '.Vmovimientos_caja::$tipocierre .' order by codigo desc limit 1) as ultimosaldo');
//        $conexion->from('caja');
//        $conexion->join('caja_usuario', 'caja_usuario.codtiposcaja = caja.codigo');
//        $conexion->where('coduser', $this->codigo);
//        $conexion->where('desactivada', 0);
//        $conexion->where('caja.estado', $caja->estadocerrada);
//        $conexion->order_by('default', 'desc');
//        $query = $conexion->get();
//        $cajas = $query->result_array();
//        return $cajas;
//    }

    public function getCajasCerrar() {
        $conexion = $this->oConnection;
        $conexion->select('caja.*, caja_usuario.*');
        $conexion->from('caja');
        $conexion->join('caja_usuario', 'caja_usuario.codtiposcaja = caja.codigo');
        $conexion->where('coduser', $this->codigo);
        $conexion->where('desactivada', 0);
        $conexion->where('caja.estado', Vcaja::$estadoabierta);
        $conexion->order_by('default', 'desc');
        $query = $conexion->get();
        $cajas = $query->result_array();
        return $cajas;
    }

    public function getFacturantesHabilitadosFacturar($codFilial) {

        $this->oConnection->select("general.facturantes.codigo");
        $this->oConnection->select("general.razones_sociales_general.razon_social");
        $this->oConnection->from("general.facturantes");
        $this->oConnection->join("general.facturantes_filiales", "general.facturantes_filiales.cod_facturante = general.facturantes.codigo AND general.facturantes_filiales.cod_filial = $codFilial");
        $this->oConnection->join("general.razones_sociales_general", "general.razones_sociales_general.codigo = general.facturantes.cod_razon_social");
        $this->oConnection->where("general.facturantes.estado", "habilitado");
        $query = $this->oConnection->get(); // echo $this->oConnection->last_query();
        return $query->result_array();
    }

    static function listarUsariosDataTable(CI_DB_mysqli_driver $conexion, $filial, $arrCondindiciones = null, $arrLimit = null, $arrSort = null, $contar = false) {
        $conexion->select('general.usuarios_sistema.codigo, general.usuarios_sistema.nombre, general.usuarios_sistema.apellido, general.usuarios_sistema.fecha_creacion,general.usuarios_sistema.email, general.usuarios_sistema.baja');
        $conexion->from('general.usuarios_sistema');
        $conexion->where('general.usuarios_sistema.cod_filial', $filial);
        $arrTemp = array();
        if ($arrCondindiciones != null) {
            foreach ($arrCondindiciones as $key => $value) {
                $arrTemp[] = "$key LIKE '%$value%'";
            }
        }
        if (count($arrTemp) > 0) {
            $where = "(" . implode(" OR ", $arrTemp) . ")";
            $conexion->where($where);
        }

        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort > 0) {

            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            return $conexion->count_all_results();
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }

    public function getPermisoUsuario($filial) {
        $this->oConnection->select('general.usuarios_permisos.id_seccion, general.usuarios_permisos.id_usuario, general.secciones.id_seccion_padre');
        $this->oConnection->from('general.usuarios_sistema');
        $this->oConnection->join('general.usuarios_permisos', 'general.usuarios_permisos.id_usuario = general.usuarios_sistema.codigo');
        $this->oConnection->join('general.secciones', 'general.secciones.codigo = general.usuarios_permisos.id_seccion');
        $this->oConnection->where('general.usuarios_sistema.codigo', $this->codigo);
        $this->oConnection->where('general.usuarios_sistema.cod_filial', $filial);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function cambiarEstadoUsuario($cambioEstado) {
        $this->oConnection->trans_begin();
        $this->baja = $this->baja == 1 ? 0 : 1;
        $estado = $this->guardarUsuarios_sistema();
        $estadoHistorico = new Vusuarios_estado_historico($this->oConnection);
        $arrayGuardarEstadoHistorico = array(
            "cod_usuario" => $this->codigo,
            "baja" => $this->baja,
            "motivo" => $cambioEstado['motivo'],
            "fecha_hora" => $cambioEstado["fecha"],
            "comentario" => $cambioEstado['comentario'],
            "cod_usuario_creador" => $cambioEstado['cod_usuario_creador']
        );
        $estadoHistorico->setUsuarios_estado_historico($arrayGuardarEstadoHistorico);
        $estadoHistorico->guardarUsuarios_estado_historico();
        $estadoTran = $this->oConnection->trans_status();

        if ($estadoTran === false) {
            $this->oConnection->trans_rollback();
        } else {
            $this->oConnection->trans_commit();
        }
        return $estadoTran;
    }

    public function unSetPermisos() {
        $this->oConnection->where('general.usuarios_permisos.id_usuario', $this->codigo);
        $this->oConnection->delete('general.usuarios_permisos');
    }

    public function setPermisos($id_seccion) {
        $setpermisos = array(
            'id_usuario' => $this->codigo,
            'id_seccion' => $id_seccion
        );
        $this->oConnection->insert('general.usuarios_permisos', $setpermisos);
    }

    public function getHash() {
        $hash = md5(date("Y-m-d h:i:s") . $this->codigo);
        return $hash;
    }

    static function getHashUsuario($conexion, $cod_usuario = null, $hash = null, $estado = null) {
        $conexion->select('general.usuarios_recuperar_hash.*, general.usuarios_sistema.email', false);
        $conexion->from('general.usuarios_recuperar_hash');
        $conexion->join("general.usuarios_sistema", "general.usuarios_sistema.codigo = general.usuarios_recuperar_hash.id_usuario");
        if ($cod_usuario != null) {
            $conexion->where('usuarios_recuperar_hash.id_usuario', $cod_usuario);
        }
        if ($hash != null) {
            $conexion->where('usuarios_recuperar_hash.hash', $hash);
        }
        if ($estado != null) {
            $conexion->where('usuarios_recuperar_hash.estado', $estado);
        }
        $query = $conexion->get();
        return $query->result_array();
    }

    public function insertarHash($hash) {
        $guardarHash = array(
            "hash" => $hash,
            "id_usuario" => $this->codigo,
            "estado" => 'habilitado'
        );
        return $this->oConnection->insert('usuarios_recuperar_hash', $guardarHash);
    }

    public function guardarCambioContraseña($password) {
        $updateContraseña = array(
            'pass' => md5($password)
        );
        $this->oConnection->where('usuarios_sistema.codigo', $this->codigo);
        $this->oConnection->update('usuarios_sistema', $updateContraseña);
    }

    public function cambiarEstadoHash($hash) {
        $estado = array(
            "estado" => 'inhabilitado'
        );
        $this->oConnection->where('usuarios_recuperar_hash.hash', $hash);
        $this->oConnection->where('usuarios_recuperar_hash.id_usuario', $this->codigo);
        $this->oConnection->update('usuarios_recuperar_hash', $estado);
    }

    static function getUsuariosSincronizacionNuevos(CI_DB_mysqli_driver $conexion) {
        $conexion->select("id_usuario");
        $conexion->from("usuarios_recuperar_hash");

        $subqueryUsuariosHash = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("*");
        $conexion->from("usuarios_sistema");
        $conexion->where("email IS NOT NULL");
        $conexion->where("pass", "");
        $conexion->where("administra", "1");
        $conexion->where("codigo NOT IN ($subqueryUsuariosHash)");
        $query = $conexion->get();
        return $query->result_array();

        // SELECT * FROM usuarios_sistema WHERE email IS NOT NULL AND pass = '' AND administra = 1 AND codigo NOT IN (SELECT id_usuario FROM usuarios_recuperar_hash);
    }

    public function setCajaDefault($cod_caja) {
        $arrunset = array('default' => 0);
        $this->oConnection->where('coduser', $this->codigo);
        $this->oConnection->update('caja_usuario', $arrunset);

        $arrset = array('default' => 1);
        $this->oConnection->where('coduser', $this->codigo);
        $this->oConnection->where('codtiposcaja', $cod_caja);
        $this->oConnection->update('caja_usuario', $arrset);
    }

    public function inserHistoricoSession($session_id, $fecha_hora) {
        $arrguardar = array(
            "id_usuario" => $this->codigo,
            "session_id_usuario" => $session_id,
            "fecha_hora" => $fecha_hora
        );
        return $this->oConnection->insert('historico_inicio_session', $arrguardar);
    }

    static function getSessions_IdUsuario($conexion, $id_usuario) {
        $conexion->select('general.historico_inicio_session.session_id_usuario');
        $conexion->from('general.historico_inicio_session');
        $conexion->join('general.ci_sessions', 'general.ci_sessions.session_id = general.historico_inicio_session.session_id_usuario');
        $conexion->where('historico_inicio_session.id_usuario', $id_usuario);
        $query = $conexion->get();
        return $query->result_array();
    }

    public function destroySessionID($session_id = null, $usuarioclase = false) {
        $se_id = null;

        if ($usuarioclase == true) {

            $usuarioarr = $this->getSessions_IdUsuario($this->oConnection, $this->codigo);

            if (count($usuarioarr) != 0) {
                $se_id = $usuarioarr[0]["session_id_usuario"];
            }
        } else {
            $se_id = $session_id;
        }

       if($se_id != ""){
        $arrayDelete = array("session_id" => $se_id);

            return $this->oConnection->delete('general.ci_sessions', $arrayDelete);
        } else {

            return false;
        }
    }

    static function getUsuariosPermisos($conexion, $filial, $cod_usuario = null, $secciones_in = null, $slugs_in = null) {
        $conexion->select('general.usuarios_permisos.id_seccion, general.usuarios_permisos.id_usuario, general.secciones.id_seccion_padre');
        $conexion->from('general.usuarios_sistema');
        $conexion->join('general.usuarios_permisos', 'general.usuarios_permisos.id_usuario = general.usuarios_sistema.codigo');
        $conexion->join('general.secciones', 'general.secciones.codigo = general.usuarios_permisos.id_seccion');
        if ($cod_usuario != null) {
            $conexion->where('general.usuarios_sistema.codigo', $cod_usuario);
        }
        if ($secciones_in != null) {
            $conexion->where_in("general.secciones.codigo", $secciones_in);
        }
        if ($slugs_in != null) {
            $conexion->where_in("general.secciones.slug", $slugs_in);
        }
        $conexion->where('general.usuarios_sistema.cod_filial', $filial);
        $conexion->group_by('general.usuarios_sistema.codigo');
        $query = $conexion->get();
        return $query->result_array();
    }

    public function setFiliales(array $arrFiliales, $id_usuario_iga, $nombre_usuario_iga){
        $arrTemp = $this->getFiliales($this->getCodigo(), $this->oConnection);
        $arrFilialesQuita = array();
        $arrFilialesActuales = array();
        foreach ($arrTemp as $filial){
            $arrFilialesActuales[] = $filial['cod_filial'];
            if (!in_array($filial['cod_filial'], $arrFiliales)){
                $arrFilialesQuita[] = $filial['cod_filial'];
            }
        }
        $arrFilialesAgrega = array();
        $this->oConnection->where("id_usuario", $this->codigo);
        $resp = $this->oConnection->delete("general.usuarios_filiales");
        foreach ($arrFiliales as $filial){
            $resp = $resp && $this->oConnection->insert("general.usuarios_filiales",
                    array("id_usuario" => $this->codigo,
                        "cod_filial" => $filial));
            if (!in_array($filial, $arrFilialesActuales)){
                $arrFilialesAgrega[] = $filial;
            }
        }
        $fechaHora = date("Y-m-d H:i:s");
        foreach ($arrFilialesQuita as $filial){
            $myHistorico = new Vusuarios_filiales_historico($this->oConnection);
            $myHistorico->accion = 'baja';
            $myHistorico->fecha = $fechaHora;
            $myHistorico->id_filial = $filial;
            $myHistorico->id_usuario = $this->codigo;
            $myHistorico->id_usuario_iga = $id_usuario_iga;
            $myHistorico->nombre_usuario_iga = $nombre_usuario_iga;
            $resp = $resp && $myHistorico->guardarUsuarios_filiales_historico();
        }
        foreach ($arrFilialesAgrega as $filial){
            $myHistorico = new Vusuarios_filiales_historico($this->oConnection);
            $myHistorico->accion = 'alta';
            $myHistorico->fecha = $fechaHora;
            $myHistorico->id_filial = $filial;
            $myHistorico->id_usuario = $this->codigo;
            $myHistorico->id_usuario_iga = $id_usuario_iga;
            $myHistorico->nombre_usuario_iga = $nombre_usuario_iga;
            $resp = $resp && $myHistorico->guardarUsuarios_filiales_historico();
        }
        return $resp;
    }
    
    public function getCajasMedio($cod_medio, $desactivada = null) {
        $conexion = $this->oConnection;
        $conexion->select('*');
        $conexion->from('caja');
        $conexion->join('caja_usuario', 'caja_usuario.codtiposcaja = caja.codigo');
        $conexion->join('cajas_medios_pago', 'cajas_medios_pago.cod_caja = caja.codigo and cajas_medios_pago.cod_medio = ' . $cod_medio);
        $conexion->where('caja_usuario.coduser', $this->codigo);
        if ($desactivada !== null) {
            $conexion->where("caja.desactivada", $desactivada);
        }
        $conexion->order_by('default', 'desc');

        $query = $conexion->get();
        $cajas = $query->result_array();
        return $cajas;
    }

    static function cod_usuarioAdministrador(CI_DB_mysqli_driver $conexion, $cod_filial) {
        $conexion->select('general.usuarios_sistema.codigo as cod_usuario');
        $conexion->from('general.usuarios_sistema');
        $conexion->where('general.usuarios_sistema.cod_filial', $cod_filial);
        $conexion->order_by('general.usuarios_sistema.codigo', 'ASC');
        $conexion->limit(1);
        $query = $conexion->get();

        return $query->result_array();
    }

    static function actualizarTablas(CI_DB_mysqli_driver $conexion, $tabla, $cod_usuario) {
        $arrActualizar = array(
            "usuario_creador" => $cod_usuario
        );
        $conexion->where('usuario_creador', 0);
        $conexion->update("$tabla", $arrActualizar);
    }

}
