<?php

/**
* Class Vboletos_bancarios_lineas
*
*Class  Vboletos_bancarios_lineas maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vboletos_bancarios extends Tboletos_bancarios{

    static private $estadoPendiente = "pendiente";
    static private $estadoEntradaConfirmada = "entrada_confirmada";
    static private $estadoEntradaRechazada = "entrada_rechazada";
    static private $estadoBaja = "baja";
    static private $estadoLiquidado = "liquidado";
    static private $estadoBajaSolicitada = "baja_solicitada";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null, $numeroSeguimiento = null) {
        if ($numeroSeguimiento != null){
            $arrTemp = self::_getConstructor($conexion, $numeroSeguimiento);
            if (count($arrTemp) > 0){
                $this->oConnection = $conexion;
                foreach ($arrTemp[0] as $key => $value){
                    $this->$key = $value;
                }
            } else {
                throw new Exception("numero seguimiento inexistente");
            }            
        } else {
            parent::__construct($conexion, $codigo);
        }
    }

    static private function _getConstructor(CI_DB_mysqli_driver $conexion, $numeroSeguimiento){
        $conexion->select("*");
        $conexion->from("bancos.boletos_bancarios");
        $conexion->where("bancos.boletos_bancarios.numero_seguimiento", $numeroSeguimiento);
        $query = $conexion->get();
        return $query->result_array();
    }
    
    static public function getEstadoPendiente(){
        return self::$estadoPendiente;
    }
    
    static public function getEstadoEntradaConfirmada(){
        return self::$estadoEntradaConfirmada;
    }
    
    static public function getEstadoEntradaRechazada(){
        return self::$estadoEntradaRechazada;
    }
    
    static public function getEstadoBaja(){
        return self::$estadoBaja;
    }
    
    static public function getEstadoLiquidado(){
        return self::$estadoLiquidado;
    }
    
    static public function getEstadoBajaSolicitada(){
        return self::$estadoBajaSolicitada;
    }
    
    static function validarEmision(CI_DB_mysqli_driver $conexion, array $arrCtacte, $codFilial){
        $conexion->where("bancos.boletos_bancarios.cod_filial", $codFilial);
        $conexion->where_in("bancos.boletos_bancarios.numero_documento", $arrCtacte);
        $arrBoletos = self::listarBoletos_bancarios($conexion);
        return count($arrBoletos) == 0;
    }
    
    static function listarBoletosDataTable(CI_DB_mysqli_driver $conexion, array $arrCondindicioneslike = null, $arrLimit = null, 
        $arrSort = null, $contar = false, array $condiciones = null, $fechaVencimientoDesde = null,
        $fechaVencimientoHasta = null, $fechaEmisionDesde = null, $fechaEmisionHasta = null, $estado = null) {
        $conexion->select("bancos.remesas.fecha_documento");
        $conexion->from("bancos.remesas");
        $conexion->where("bancos.remesas.codigo = bancos.boletos_bancarios.cod_remesa");
        $sqFechaEmision = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select("bancos.boletos_bancarios.*", false);
        $conexion->select("($sqFechaEmision) AS fecha_emision", false);
        $conexion->from("bancos.boletos_bancarios");
        $conexion->where("bancos.boletos_bancarios.fecha_mora <> ''");
                
        if ($condiciones != null && count($condiciones) > 0){
            $conexion->where($condiciones);
        }
        
        if ($fechaVencimientoDesde != null){
            $conexion->where("bancos.boletos_bancarios.fecha_vencimiento >=", $fechaVencimientoDesde);
        }
        if ($fechaVencimientoHasta != null){
            $conexion->where("bancos.boletos_bancarios.fecha_vencimiento <=", $fechaVencimientoHasta);
        }
        
        if ($fechaEmisionDesde != null){
            $conexion->having("fecha_emision >=", $fechaEmisionDesde);
        }
        
        if ($fechaEmisionHasta != null){
            $conexion->having("fecha_emision <=", $fechaEmisionHasta);
        }
        
        if ($estado != null){
            $conexion->where("bancos.boletos_bancarios.estado", $estado);
        }
        
        if (count($arrCondindicioneslike) > 0) {
            $arrTemp = array();
            foreach ($arrCondindicioneslike as $key => $value){              
                    $arrTemp[] = "$key LIKE '%$value%'";                
            }
            if (count($arrTemp) > 0) {
                $having = "(" . implode(" OR ", $arrTemp) . ")";
                $conexion->having($having);
            }
        }
        
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        
        if ($arrSort != NULL) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }
        
        $query = $conexion->get();
        
        if ($contar) {
            $arrResp = $query->num_rows();
        } else { //  echo $conexion->last_query(); die();
            $arrResp = $query->result_array();
        }
        
        return $arrResp;
    }
    
    public function guardarBoletos_bancarios() {
        if ($this->cod_filial == '' || $this->numero_secuencial == '' || $this->convenio == ''){
            throw new Exception ("Debe indicar codigo de filial, numero secuencial y numero convenio banco");
        } else {
            $this->nosso_numero = str_pad($this->convenio, 10, 0, STR_PAD_LEFT).str_pad($this->cod_filial, 4, 0, STR_PAD_LEFT).  str_pad($this->numero_secuencial, 6, 0, STR_PAD_LEFT);
            $this->numero_seguimiento = str_pad($this->cod_filial, 4, 0, STR_PAD_LEFT).  str_pad($this->numero_secuencial, 6, 0, STR_PAD_LEFT);        
            parent::guardarBoletos_bancarios();            
        }        
    }
   
    public function getHTMLBoleto(){
        $conexion = $this->oConnection;
        $myRemesa = new Vremesas($conexion, $this->cod_remesa);
        $myCuentaBoleto = new Vcuentas_boletos_bancarios($conexion, $myRemesa->cod_banco, $myRemesa->cod_configuracion, $myRemesa->cod_facturante);
        $dadosboleto["charset"] = "UTF-8";
        $dadosboleto["data_documento"] = date("d/m/Y");
        $taxa_boleto = 0;
        $fecha = strtotime($this->fecha_vencimiento);
        $data_venc = date("d/m/Y", $fecha);        
//        date("d/m/Y"); // cambiar al fecha vencimiento en formato d/m/Y
        $valor_cobrado = str_replace(",", ".", $this->valor_boleto);
        $valor_boleto = number_format($valor_cobrado + $taxa_boleto, 2, ',', '');
        $numero = str_pad($this->cod_filial, 4, 0, STR_PAD_LEFT).  str_pad($this->numero_secuencial, 6, 0, STR_PAD_LEFT);
        $dadosboleto["nosso_numero"] = $numero;
        $dadosboleto["numero_documento"] = $this->numero_documento;
        $dadosboleto["data_vencimento"] = $data_venc;            
        $dadosboleto["data_processamento"] = date("d/m/Y");
        $dadosboleto["valor_boleto"] = $valor_boleto;
        // DATOS CLIENTE
        $dadosboleto["sacado"] = $this->sacado_nombre;
        $cpf_cnpj = str_replace(array(".", "-", "/"), "", $this->sacado_cpf_cnpj);
        if (strlen($cpf_cnpj) == 11){ // formatea a CPF
            $cpf_cnpj = substr($cpf_cnpj, 0, 3).".".substr($cpf_cnpj, 3, 3).".".substr($cpf_cnpj, 6, 3)."-".substr($cpf_cnpj, 9);
        } else { // formatea CNPJ
            $cpf_cnpj = substr($cpf_cnpj, 0, 2).".".substr($cpf_cnpj, 2, 3).".".substr($cpf_cnpj, 5, 3)."/".substr($cpf_cnpj, 8, 4)."/".  substr($cpf_cnpj, 12);
        }
        $dadosboleto["cpf_cnpj_sacado"] = $cpf_cnpj;
        $dadosboleto["endereco1"] = $this->sacado_direccion;
        $dadosboleto["endereco2"] = "{$this->sacado_ciudad}-{$this->sacado_codigo_estado} - {$this->sacado_cod_postal}";
        $myConfiguraacionBanco = $myCuentaBoleto->getObjectoBanco();	
        $dadosboleto["demonstrativo1"] = $this->demostrativo1; 
	$dadosboleto["demonstrativo2"] = $this->demostrativo2;
	$dadosboleto["demonstrativo3"] = $this->demostrativo3;
	$dadosboleto["instrucoes1"] = $this->instrucciones1;
	$dadosboleto["instrucoes2"] = $this->instrucciones2;
        $dadosboleto["instrucoes3"] = $this->instrucciones3;
        $dadosboleto["instrucoes4"] = $this->instrucciones4;
	$dadosboleto["quantidade"] = ""; //só deve ser preechidos quando houver valor indexador (ex: URV, UFIR, etc.)
	$dadosboleto["valor_unitario"] = ""; //só deve ser preechidos quando houver valor indexador (ex: URV, UFIR, etc.)
        $dadosboleto["aceite"] = "N";	    // N (no registrada) // S (registrada)
	$dadosboleto["uso_banco"] = "";
	$dadosboleto["especie"] = "R $";
	$dadosboleto["especie_doc"] = "DS";
	$dadosboleto["agencia"] = $myRemesa->agencia;
        $dadosboleto["agencia_dv"] = $myRemesa->digito_agencia; 
	$dadosboleto["conta"] = $myRemesa->numero_cuenta;
        $dadosboleto["conta_dv"] = $myRemesa->digito_cuenta;
	$dadosboleto["convenio"] = $myRemesa->cedente_convenio; 
	$dadosboleto["contrato"] = $myConfiguraacionBanco->contrato;
	$dadosboleto["identificacao"] = "Sistema IGA";
        $cnpj = $myRemesa->cedente_cpf_cnpj;
        $cnpj = substr($cnpj, 0, 2).".".substr($cnpj, 2, 3).".".substr($cnpj, 5, 3)."/".substr($cnpj, 8, 4)."/".  substr($cnpj, 12);
	$dadosboleto["cpf_cnpj"] = $cnpj;
	$dadosboleto["endereco"] = $myRemesa->direccion; 
        $dadosboleto["cidade_uf"] = $myRemesa->localidad."-".$myRemesa->codigo_estado;
	$dadosboleto["cedente"] = $myRemesa->razon_social;
        $dadosboleto["formatacao_convenio"] = strlen($myRemesa->cedente_convenio);
        $dadosboleto["carteira"] = $myRemesa->cartera;
        $dadosboleto["variacao_carteira"] = "-".$myCuentaBoleto->variacao_carteira;
        $dadosboleto["formatacao_nosso_numero"] = "1";
        return Vbanco_do_brasil::getHTMLBoleto($dadosboleto); // cambiar por llamado de getHTML de otros bancos (por ejemplo Vbanco_itau::getHTMLBoleto($datosboleto))
    }
    
    private function getUltimoNumeroSecuencia(){
        $this->oConnection->select("IFNULL(MAX(numero_secuencia), 0) AS ultima_secuencia", false);
        $this->oConnection->from("bancos.boletos_estados_historicos");
        $this->oConnection->where("cod_boleto", $this->codigo);
        $query = $this->oConnection->get();
        $temp = $query->result_array();
        return $temp[0]['ultima_secuencia'];
    }
    
    private function _setEstado($nuevoEstado, segmento_t $mySegmentoT, segmento_u $mySegmentoU, $codUsuario, $numeroSecuencia, &$codigoHistorico = null){
        $myHistorico = new Vboletos_estados_historicos($this->oConnection);
        $myHistorico->cod_boleto = $this->codigo;
        $myHistorico->estado = $nuevoEstado;
        $myHistorico->fecha = date("Y-m-d H:i:s");
        $myHistorico->segmento_t = serialize($mySegmentoT);
        $myHistorico->segmento_u = serialize($mySegmentoU);
        $myHistorico->cod_usuario = $codUsuario;
        $myHistorico->numero_secuencia = $numeroSecuencia;
        $resp = $this->oConnection->update($this->nombreTabla, array("estado" => $nuevoEstado), "codigo = $this->codigo");        
        $resp = $resp && $myHistorico->guardarBoletos_estados_historicos();
        $codigoHistorico = $myHistorico->getCodigo();
        if ($resp){
            $this->estado = $nuevoEstado;            
        }
        return $resp;
    }
    
    public function setEstado($codUsuario, segmento_t $mySegmentoT, segmento_u $mySegmentoU, $numeroSecuencia){
        $ultimaSecuenciaRegistrada = $this->getUltimoNumeroSecuencia();
        if ($ultimaSecuenciaRegistrada < $numeroSecuencia){
            $codigoRetrono = $mySegmentoT->servicio_codigo_movimiento;
            $resp = false;
            switch ($codigoRetrono) {
                case "02":
                    $resp = $this->setEstadoEntradaConfiramda($codUsuario, $mySegmentoT, $mySegmentoU, $numeroSecuencia);
                    break;

                case "03":
                    if ($mySegmentoT->motivo_ocurrencia = '63'){
                        $resp = $this->setEstadoEntradaConfiramda($codUsuario, $mySegmentoT, $mySegmentoU, $numeroSecuencia);
                    } else {
                        $resp = $this->setEstadoEntradaRechazada($codUsuario, $mySegmentoT, $mySegmentoU, $numeroSecuencia);
                    }
                    break;

                case "17":
                case "06":
                    $resp = $this->setEstadoLiquidado($codUsuario, $mySegmentoT, $mySegmentoU, $numeroSecuencia);
                    break;

                case "25":
                case "09":
                    $resp = $this->setEstadoBaja($codUsuario, $mySegmentoT, $mySegmentoU, $numeroSecuencia);
                    break;

                default:
                    $this->setEstado($this->estado, $mySegmentoT, $mySegmentoU, $numeroSecuencia);
                    $resp = false; // no se implemento accion para este codigo de retorno (producir Exceprion)
                    throw new Exception("No se reconoce el codido de movimiento");
                    break;
            }
            return $resp;
        } else {
            throw new Exception ("El numero de secuencia informado es anterior o igual al último cargado");
        }        
    }
    
    private function setEstadoEntradaConfiramda($codUsuario, segmento_t $mySegmentoT, segmento_u $mySegmentoU, $numeroSecuencia){
        return $this->_setEstado(self::$estadoEntradaConfirmada, $mySegmentoT, $mySegmentoU, $codUsuario, $numeroSecuencia);
    }
    
    private function setEstadoEntradaRechazada($codUsuario, segmento_t $mySegmentoT, segmento_u $mySegmentoU, $numeroSecuencia){
        return $this->_setEstado(self::$estadoEntradaRechazada, $mySegmentoT, $mySegmentoU, $codUsuario, $numeroSecuencia);
    }
    
    private function setEstadoBaja($codUsuario, segmento_t $mySegmentoT, segmento_u $mySegmentoU, $numeroSecuencia){
        return $this->_setEstado(self::$estadoBaja, $mySegmentoT, $mySegmentoU, $codUsuario, $numeroSecuencia);
    }
    
    private function setEstadoLiquidado($codUsuario, segmento_t $mySegmentoT, segmento_u $mySegmentoU, $numeroSecuencia){
        $importe = $mySegmentoU->valor_titulo_pago;
        $fechaReal = $mySegmentoU->fecha_ocurrencia;
        $myCtacte = new Vctacte($this->oConnection, $this->numero_documento);
        $myCobro = new Vcobros($this->oConnection);
        $myCobro->cod_alumno = $myCtacte->cod_alumno;
        $myCobro->cod_usuario = $codUsuario;
        $myCobro->estado = Vcobros::getEstadoPendiente();
        $myCobro->fechaalta = date("Y-m-d H:i:s");
        $myCobro->fechareal = $fechaReal;
        $myCobro->importe = $importe;
        $myCobro->medio_pago = 2; // boleto bancario
        $resp = $myCobro->guardarCobros();                /* DESCOMENAR AL TERMINAR EL DEBUG */
        $arrCtacte = Vctacte::getCtaCteOredenPrioridadImputacion($this->oConnection, $myCtacte->cod_alumno, $myCtacte->getCodigo());        $i = 0;
        $saldo = $importe;
        while ($saldo > 0 && $i < count($arrCtacte)){
            $ctacte = $arrCtacte[$i];
            $importeAimputar = $ctacte['importe'] - $ctacte['pagado'] >= $saldo ? $saldo : $ctacte['importe'] - $ctacte['pagado'];
            $saldo -= $importeAimputar;
            $resp = $resp && $myCobro->inputar($ctacte['codigo'], $importeAimputar, $codUsuario);
           
            $i++;
        }
        $myCobro->confirmarCobro($codUsuario);
        $codigoHistorico = '';
        $resp = $resp && $this->_setEstado(self::$estadoLiquidado, $mySegmentoT, $mySegmentoU, $codUsuario, $numeroSecuencia, $codigoHistorico);
        $resp = $resp && $myCobro->asociarBoleto($codigoHistorico);
        return $resp;
    }
    
    static function getEstados(){
        $arrResp = array(
            self::$estadoPendiente, self::$estadoLiquidado, self::$estadoBaja, self::$estadoBajaSolicitada,
            self::$estadoEntradaConfirmada, self::$estadoEntradaRechazada
        );
        return $arrResp;
    }
    
}
