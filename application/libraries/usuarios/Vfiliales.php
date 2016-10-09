<?php

/**
 * Class Vfiliales
 *
 * Class  Vfiliales maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vfiliales extends Tfiliales {

    static public $estadoActiva = "activa";
    static public $estadoSuspendida = "activa";

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    public function certifica($codCertificante){
        $this->oConnection->select("cod_plan_academico");
        $this->oConnection->from("general.certificados_plan_filial");
        $this->oConnection->where("cod_filial", $this->codigo);
        $this->oConnection->where("cod_certificante", $codCertificante);
        $query = $this->oConnection->get();
        return $query->num_rows() > 0;
    }

    function getMonedaCotizacion() {
        $this->oConnection->select('general.cotizaciones.id, general.cotizaciones.simbolo');
        $this->oConnection->from('general.filiales');
        $this->oConnection->join('general.filiales_cotizaciones', 'general.filiales_cotizaciones.cod_filial = filiales.codigo');
        $this->oConnection->join('general.cotizaciones', 'general.cotizaciones.id = general.filiales_cotizaciones.cod_cotizacion');
        $this->oConnection->where('general.filiales.codigo', $this->codigo);
        $this->oConnection->group_by('general.cotizaciones.id');
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    function getCondicionFiscalDefault() {
        $conexion = $this->oConnection;
        $condiciones = new Vcondiciones_sociales($conexion);
        return $condiciones->getCondicionSocial($this->pais);
    }

    public function getTiposFactura($habilitado = null) {
        $this->oConnection->select('*');
        $this->oConnection->from('general.filiales_tipos_factura');
        $this->oConnection->join('general.tipos_facturas', 'general.tipos_facturas.codigo = general.filiales_tipos_factura.cod_tipo_factura');
        $this->oConnection->where('cod_filial', $this->codigo);
        if ($habilitado != null) {
            $this->oConnection->where('general.tipos_facturas.habilitado', $habilitado);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function getCuentasBancarias() {
        $this->oConnection->select("cuentas_boletos_bancarios.cod_banco");
        $this->oConnection->select("cuentas_boletos_bancarios.cod_configuracion");
        $this->oConnection->select("cuentas_boletos_bancarios.cod_facturante");
        $this->oConnection->from("bancos.cuentas_boletos_bancarios");
        $this->oConnection->join("general.facturantes_filiales", "general.facturantes_filiales.cod_facturante = bancos.cuentas_boletos_bancarios.cod_facturante AND general.facturantes_filiales.cod_filial = $this->codigo");
        $query = $this->oConnection->get();
        $arrCuentasBancos = $query->result_array();
        return $arrCuentasBancos;
    }

    public function insertSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $arrTemp[$primary] = $this->$primary;
        if ($this->oConnection->insert($this->nombreTabla, $arrTemp)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSincronizacion() {
        $arrTemp = array();
        $arrTemp = $this->_getArrayDeObjeto();
        $primary = $this->primaryKey;
        $primaryVal = $this->$primary;
        return $this->oConnection->update($this->nombreTabla, $arrTemp, "$primary = $primaryVal");
    }

    public function getListadoRecesoFilial($cod_receso = null) {
        $this->oConnection->select('general.receso_filial.*', false);
        $this->oConnection->from('general.receso_filial');
        $this->oConnection->where('general.receso_filial.cod_filial', $this->codigo);
        $this->oConnection->where('general.receso_filial.estado', 'habilitada');
        if ($cod_receso != null) {
            $this->oConnection->select("CONCAT(LPAD(DAY(general.receso_filial.fecha_desde), 2, 0), '/', LPAD(MONTH(general.receso_filial.fecha_desde), 2, 0), '/', YEAR(general.receso_filial.fecha_desde)) AS fecha_desde_formateada", false);
            $this->oConnection->select("CONCAT(LPAD(DAY(general.receso_filial.fecha_hasta), 2, 0), '/', LPAD(MONTH(general.receso_filial.fecha_hasta), 2, 0), '/', YEAR(general.receso_filial.fecha_hasta)) AS fecha_hasta_formateada", false);
            $this->oConnection->where('general.receso_filial.codigo', $cod_receso);
        }
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    public function insertReceso($arrGuardar) {
        return $this->oConnection->insert('general.receso_filial', $arrGuardar);
    }

    public function updateReceso($cod_receso, $arrGuardar) {
        $this->oConnection->where('general.receso_filial.codigo', $cod_receso);
        return $this->oConnection->update('general.receso_filial', $arrGuardar);
    }

    public function baja_receso_filial($cod_receso) {
        $arrayBaja = array(
            "estado" => 'inhabilitada'
        );
        $this->oConnection->where('general.receso_filial.codigo', $cod_receso);
        return $this->oConnection->update('general.receso_filial', $arrayBaja);
    }

    public function validarEstablecimientoTarjeta($numeroEstablecimiento) {
        $this->oConnection->select("tarjetas.pos_contratos.cod_facturante");
        $this->oConnection->from("tarjetas.pos_contratos");
        $this->oConnection->join("general.facturantes", "general.facturantes.cod_facturante_matriz = tarjetas.pos_contratos.cod_facturante");
        $this->oConnection->join("general.facturantes_filiales", "general.facturantes_filiales.cod_facturante = general.facturantes.codigo AND general.facturantes_filiales.cod_filial = $this->codigo");
        $this->oConnection->where("tarjetas.pos_contratos.numero_establecimiento", $numeroEstablecimiento);
        $query = $this->oConnection->get();
        $arrTemp = $query->result_array();
        return count($arrTemp) > 0;
    }

    function getFacturantes($default = false) {
        $condiciones = array("cod_filial" => $this->codigo);
        if ($default) {
            $condiciones['default'] = 1;
        }
        $this->oConnection->join("general.facturantes_filiales", "facturantes_filiales.cod_facturante = codigo ");
        return Vfacturantes::listarFacturantes($this->oConnection, $condiciones);
    }

    static public function getFilialesDisponiblesAFacturar(CI_DB_mysqli_driver $conexion, $codFacturante = null, $codMatriz = null) {
        $sqFacturante = null;
        $conexion->_protect_identifiers = false;
        if ($codFacturante != null) {
            $conexion->select("cod_filial");
            $conexion->from("general.facturantes_filiales");
            $conexion->where("general.facturantes_filiales.cod_facturante", $codFacturante);
            $sqFacturante = $conexion->return_query();
            $conexion->resetear();
        } else if ($codMatriz != null) {
            $conexion->select("general.facturantes.codigo");
            $conexion->from("general.facturantes");
            $conexion->where("general.facturantes.cod_facturante_matriz", $codMatriz);
            $sqMatriz = $conexion->return_query();
            $conexion->resetear();
            $conexion->select("cod_filial");
            $conexion->from("general.facturantes_filiales");
            $conexion->where("general.facturantes_filiales.cod_facturante IN ($sqMatriz)");
            $sqFacturante = $conexion->return_query();
            $conexion->resetear();
        }
        $conexion->select("general.facturantes_filiales.cod_filial");
        $conexion->from("general.facturantes_filiales");
        $sqNOTIN = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("general.filiales.codigo");
        $conexion->select("general.filiales.nombre");
        $conexion->from("general.filiales");
        $conexion->where("general.filiales.codigo NOT IN ($sqNOTIN)");
        if ($sqFacturante != null) {
            $conexion->or_where("codigo IN ($sqFacturante)");
        }
        $query = $conexion->get();
        $arrResp = $query->result_array();
        $conexion->_protect_identifiers = true;
        return $arrResp;
    }

    /**
     * Retorno todos los contratos de proveedores de tarjeta de todos los facturantes de la filial
     * 
     * @return array
     */
    public function getContratosTarjetas() {
        $arrResp = array();
        $arrFacturantes = $this->getFacturantes();
        foreach ($arrFacturantes as $facturante) {
            $myFacturante = new Vfacturantes($this->oConnection, $facturante['codigo']);
            $arrContratos = $myFacturante->getContratosFacturante();
            foreach ($arrContratos as $contrato) {
                $arrResp[] = $contrato;
            }
        }
        return $arrResp;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
        $condicion = array("cod_filial" => $this->codigo);
        $usuarios = Vusuarios_sistema::listarUsuarios_sistema($this->oConnection, $condicion);
        foreach ($usuarios as $usuario) {
            $user = new Vusuarios_sistema($this->oConnection, $usuario["codigo"]);
            $user->destroySessionID(null, true);
        }
        return $this->guardarFiliales();
    }

    public function getMetodoFacturacion() {
        $conexion = $this->oConnection;
        $conexion->select("*");
        $conexion->from("general.filiales_metodos_facturacion");
        $conexion->where("general.filiales_metodos_facturacion.cod_filial", $this->codigo);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    public function set_como_nos_conocio($id_conocio){
        $resp = $this->unset_como_nos_conocio($id_conocio);
        $param = array(
            "id_filial" => $this->codigo,
            "id_conocio" => $id_conocio,
            "activo" => "1"
        );
        return $resp && $this->oConnection->insert("general.como_nos_conocio_filiales", $param);
    }

    public function unset_como_nos_conocio($id_conocio){
        $this->oConnection->where("general.como_nos_conocio_filiales.id_filial", $this->codigo);
        $this->oConnection->where("general.como_nos_conocio_filiales.id_conocio", $id_conocio);
        return $this->oConnection->delete("general.como_nos_conocio_filiales");
    }    

    public static function getFiliales($conexion, $pais = null){
        $conexion->select("*");
        $conexion->from("general.filiales");
        $conexion->where("baja", "0");
        $conexion->where("estado", "activa");
        if($pais != null){
            $conexion->where("pais", $pais);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static function buscarFilialesPorCodigo($conexion, $codigos = null) {
        $conexion->select("*");
        $conexion->from("general.filiales");
        if($codigos != null){
            $conexion->where_in("codigo", $codigos);
        }
        $query = $conexion->get();
        return $query->result_array();
    }
}
