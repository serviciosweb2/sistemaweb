<?php

/**
* Class Vbanco_do_brasil
*
*Class  Vbanco_do_brasil maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vbanco_do_brasil extends Tbanco_do_brasil{

    private $codigoBanco = 1;
    
    static private $C047C = array(
        '01' => "por_saldo",
        '02' => "por_conta",
        '03' => "liquidacao_no_guiche_de_caixa_em_dinheiro",
        '04' => "compensacao_eletronica",
        '05' => "compensacao_convencional",
        '06' => "por_meio_eletronico",
        '07' => "apos_feriado_local",
        '08' => "em_cartorio",
        '30' => "liquidacao_no_guiche_de_caixa_em_cheque",
        '31' => "liquidacao_em_banco_correspondente",
        '32' => "liquidacao_terminal_de_auto_atendimento",
        '33' => "liquidacao_na_internet_home_banking",
        '34' => "liquidado_office_banking",
        '35' => "liquidado_correspondente_em_dinheiro",
        '36' => "liquidado_correspondente_em_cheque",
        '37' => "liquidado_por_meio_de_central_de_atendimento_telefone",
        '09' => "comandada_banco",
        '10' => "comandada_cliente_arquivo",
        '11' => "comandada_cliente_on_line",
        '12' => "decurso_prazo_cliente",
        '13' => "decurso_prazo_banco",
        '14' => "protestado",
        '15' => "titulo_excluido"
    );
    
    static private $C047B = array(
        '01' => "tarifa_de_extrato_de_posicao",
        '02' => "tarifa_de_manutencao_de_titulo_vencido",
        '03' => "tarifa_de_sustacao",
        '04' => "tarifa_de_protesto",
        '05' => "tarifa_de_outras_instrucoes",
        '06' => "tarifa_de_outras_ocorrencias",
        '07' => "tarifa_de_envio_de_duplicata_ao_sacado",
        '08' => "custas_de_protesto",
        '09' => "custas_de_sustacao_de_protesto",
        '10' => "custas_de_cartorio_distribuidor",
        '11' => "custas_de_edital",
        '12' => "tarifa_sobre_devolucao_de_titulo_vencido",
        '13' => "tarifa_sobre_registro_cobrada_na_baixa_liquidacao",
        '14' => "tarifa_sobre_reapresentacao_automatica",
        '15' => "tarifa_sobre_rateio_de_credito",
        '16' => "tarifa_sobre_informacoes_via_fax",
        '17' => "tarifa_sobre_prorrogacao_de_vencimento",
        '18' => "tarifa_sobre_alteracao_de_abatimento_desconto",
        '19' => "tarifa_sobre_arquivo_mensal_em_ser",
        '20' => "tarifa_sobre_emissao_de_bloqueto_Pre_emitido_pelo_banco"
    );
    
    static private $C047A = array(
        '01' => "codigo_do_banco_invalido",
        '02' => "codigo_do_registro_detalhe_invalido",
        '03' => "codigo_do_segmento_invalido",
        '04' => "codigo_de_movimiento_nao_permitido_para_carteira",
        '05' => "codigo_de_movimiento_invalido",
        '06' => "tipo_numero_de_inscricao_do_cedente_invalido",
        '07' => "agencia_conta_dv_invalido",
        '08' => "nosso_numero_invalido",
        '09' => "nosso_nuemro_duplicado",
        '10' => "carteira_invalida",
        '11' => "forma_de_cadastramento_do_tirulo_invalido",
        '12' => "tipo_de_documento_invalido",
        '13' => "identificacao_de_emissao_do_bloqueto_invalida",
        '14' => "identidicacao_da_distribuicao_do_bloqueto_invalida",
        '15' => "caracteristicas_da_cobranca_incompativeis",
        '16' => "data_de_vencimiento_invalida",
        '17' => "data_de_vencimiento_anterior_a_data_de_emissao",
        '18' => "vencimiento_fora_do_prazo_de_operacao",
        '19' => "titulo_a_cargo_de_bancos_correspondentes_com_vencimiento_inferior_a_xx_dias",
        '20' => "valor_do_titulo_invalido",
        '21' => "especia_do_titulo_invalida",
        '22' => "especie_do_titulo_nao_permitida_para_a_carteira",
        '23' => "aceite_invalido",
        '24' => "data_de_emissao_invalida",
        '25' => "data_de_emissao_posterior_a_data_de_entrada",
        '26' => "codigo_de_juros_de_mora_invalido",
        '27' => "valor_taxa_de_juros_de_mora_invalido",
        '28' => "codigo_do_desconto_invalido",
        '29' => "valor_do_desconto_maior_ou_igual_ao_valor_do_titulo",
        '30' => "desconto_a_conceder_nao_confere",
        '31' => "concessao_de_desconto_ja_existe_desconto_anterior",
        '32' => "valor_do_iof_invalido",
        '33' => "valor_do_abatimento_invalido",
        '34' => "valor_do_abatimento_maior_ou_igual_ao_valor_do_titulo",
        '35' => "valor_a_conceder_nao_confere",
        '36' => "concessao_de_abatimento",
        '37' => "codigo_para_protesto_invalido",
        '38' => "prazo_para_protesto_invalido",
        '39' => "pedido_de_protesto_nao_permitido_para_o_titulo",
        '40' => "titulo_com_ordem_de_protesto_emitida",
        '41' => "pedido_de_cancelamento_sustacao_para_titulos_sem_instrucao_de_protesto",
        '42' => "codigo_para_baixa_devolucao_invalido",
        '43' => "prazo_para_baixa_devolucao_invalido",
        '44' => "codigo_da_moeda_invalido",
        '45' => "nombe_do_sacado_nao_informado",
        '46' => "tipo_numero_de_inscripcao_do_sacado_invalidos",
        '47' => "endereco_do_sacado_nao_informado",
        '48' => "cep_invalido",
        '49' => "cep_sem_praca_de_cobranca_nao_localizado",
        '50' => "cep_referente_a_um_banco_correspondente",
        '51' => "cep_incompativel_com_a_unidade_da_federacao",
        '52' => "unidade_da_federacao_invalida",
        '53' => "tipo_numero_de_inscripcao_do_sacador_avalista_invalidos",
        '54' => "sacador_avalista_nao_informado",
        '55' => "nosso_numero_no_banco_correspondente_nao_informado",
        '56' => "codigo_do_banco_correspondente_nao_informado",
        '57' => "codigo_da_multa_invalido",
        '58' => "data_da_multa_invalida",
        '59' => "valor_percentual_da_multa_invalido",
        '60' => "movimiento_para_titulo_nao_cadastrado",
        '61' => "alteracao_da_agencia_cobradora_dv_invalida",
        '62' => "tipo_de_impressao_invalido",
        '63' => "entrada_para_titulo_ja_cadastrado",
        '64' => "numero_da_linha_invalido",
        '65' => "codigo_do_banco_para_debito_invalido",
        '66' => "agencia_conta_dv_para_debito_invalido",
        '67' => "dados_para_debito_incompativel_com_a_identificacao_da_emissao_fo_bloqueto",
        '68' => "debito_automatico_agendado",
        '69' => "debito_nao_agendado_erro_nos_dados_da_remessa",
        '70' => "debito_nao_agendado_sacado_nao_consta_do_cadastro_de_autorizante",
        '71' => "debito_nao_agendado_cedente_nao_autorizado_pelo_sacado",
        '72' => "debito_nao_agendado_cedente_nao_participa_da_modalidade_debito_automatico",
        '73' => "debito_nao_agendado_codigo_de_moeda_diferente_de_real",
        '74' => "debito_nao_agendado_data_vencimento_invalida",
        '75' => "debito_nao_agendado_conforme_seu_pedido_titulo_nao_regitrado",
        '76' => "debito_nao_agendado_tipo_num_inscripcao_do_debitado_invalido",
        '77' => "transferencia_para_desconto_nao_permitida_para_a_carteira_do_titulo",
        '78' => "data_inferior_ou_igual_ao_vencimiento_para_debito_automatico",
        '79' => "data_juros_de_mora_invalido",
        '80' => "data_do_desconto_invalida",
        '81' => "tentativas_de_debito_esgotadas_baixado",
        '82' => "tentativas_de_debito_esgotadas_pendente",
        '83' => "limite_excedido",
        '84' => "numero_autorizacao_inexistente",
        '85' => "titulo_com_pagamento_vinculado",
        '86' => "seu_numero_invalido",
        '87' => "email_sms_enviado",
        '88' => "email_lido",
        '89' => "email_sms_devolvido_endereco_de_email_iu_numero_do_celular_incorrecto",
        '90' => "email_devolvido_caixa_postal_cheia",
        '91' => "email_numero_do_celular_do_sacado_nao_informado",
        '92' => "sacado_optante_por_bloqueto_electronico_email_nao_enviado",
        '93' => "codigo_para_emissao_de_bloqueto_nao_permite_envio_de_email",
        '94' => "codigo_da_carteira_invalido_para_envio_email",
        '95' => "contrato_nao_permite_o_envio_de_email",
        '96' => "numero_de_contrato_invalido",
        '97' => "rejeicado_da_alteracao_do_prazo_limite_de_recebimiento_a_data_deve_ser_informada_no_campo_28_3_p",
        '98' => "rejeicao_de_dispensa_de_prazo_limite_de_recebimento",
        '99' => "rejeicao_da_alteracao_do_numero_do_titulo_dado_pelo_cedente",
        'A1' => "rejeicao_da_dalteracao_do_numero_controle_do_participante",
        'A2' => "rejeicao_da_alteracao_dos_dados_do_sacado",
        'A3' => "rejeicao_da_alteracao_dos_datos_do_sacador_avalista",
        'A4' => "sacado_dda"
    );
    
    static private $C044 = array(
        '02' => "entrada_confirmada",								
        '03' => "entrada_rejeitada",								
        '04' => "transferencia_de_carteira_entrada",
        '05' => "transferencia_de_carteira_baixa",
        '06' => "liquidacao",									
        '07' => "confirmacao_do_recebimento_da_intrucao_de_desconto",
        '08' => "confirmacao_do_recebimento_do_cancelamento_do_desconto",
        '09' => "baixa",										
        '11' => "titulos_em_carteira_em_ser",
        '12' => "confirmacao_recebimento_instrucao_de_abatimento",
        '13' => "confirmacao_recebimento_instrucao_de_cancelamento_abatimento",
        '14' => "confirmacao_recebimento_instrucao_alteracao_de_vencimento",
        '15' => "franco_de_pagamento",
        '17' => "liquidacao_apos_baixa_ou_liquidacao_titulo_nao_regiostrado",			
        '19' => "confirmacao_recebimento_instrucao_de_protesto",
        '20' => "confirmacao_recebimento_instrucao_de_sustacao_cancelamento_de_protesto",
        '23' => "remessa_a_cartorio_aponte_em_cartorio",
        '24' => "retirada_de_cartorio_e_manutencao_em_carteira",
        '25' => "protestado_e_baixado_baixa_por_ter_sido_protestado",
        '26' => "instrucao_rejeitada",								
        '27' => "confirmacao_do_pedido_de_alteracao_de_outros_dados",
        '28' => "debito_de_taarifas_custas",								
        '29' => "ocorrencias_do_sacado",
        '30' => "alteracao_de_dados_rejeitada",							
        '33' => "confirmacao_da_alteracao_dos_dados_do_rateio_de_credito",
        '34' => "confirmacao_do_cancelamento_dos_dados_do_reteio_de_credito",
        '35' => "confirmacao_do_desagendamento_do_debito_automatico",
        '36' => "confirmacao_de_encio_de_email_sms",
        '37' => "envio_de_email_sms_rejeitado",
        '38' => "confirmacao_de_alteracao_do_prazo_limite_de_recebimento_a_data_deve_ser_informada_no_campo_28_3_p",
        '39' => "confirmacao_de_dispensa_de_prazo_limite_de_recebimento",
        '40' => "confirmacao_da_alteracao_do_numero_do_titulo_dado_pelo_cedente",
        '41' => "confirmacao_da_alteracao_do_numero_controle_do_participante",
        '42' => "confirmacao_da_alteracao_dos_dados_do_sacado",
        '43' => "confirmacao_da_adlteracao_dos_dados_do_sacador_avalista",
        '44' => "titulo_pago_com_cheque_devolvido",
        '45' => "titulo_pago_com_cheque_compensado",
        '46' => "instrucao_para_cancelar_protesto_confirmada",
        '47' => "instrucao_para_protesto_para_fins_falimentares_confirmada",
        '48' => "confirmacao_de_instrucao_de_transferencia_de_carteira_modalidade_de_cobranca",
        '49' => "alteracao_de_contrato_de_cobraca",
        '50' => "titulo_pago_com_cheque_pendente_de_liquidacao",
        '51' => "titulo_dda_reconhecido_pelo_sacado",
        '52' => "titulo_dda_nao_reconhecido_pelo_sacado",
        '53' => "titulo_dda_recusado_pela_cip",
        '54' => "confirmacao_da_instrucao_de_baixa_de_titulo_negativado_sem_protesto"
    );
    
    
    
    /* CONSTRUCTOR */
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */
    
    
    /* PUBLIC FUNCTIONS */
    
    public function getToArray(){
        return $this->_getArrayDeObjeto();
    }
    
    public function getBoletoBancario($convenio = null, $carteira = null){
        $conexion = $this->oConnection;
        $conexion->select("*");
        $conexion->from("bancos.cuentas_boletos_bancarios");
        $conexion->where("cod_banco", $this->codigoBanco);
        $conexion->where("cod_configuracion", $this->codigo);
        if ($convenio !== null){
            $conexion->where("convenio", $convenio);
        }
        if ($carteira !== null){
            $conexion->where("carteira", $carteira);
        }
        $query = $conexion->get();
        return $query->result_array();        
    }
    
    public function inhabilitar(){
        $this->oConnection->where("codigo", $this->codigo);
        return $this->oConnection->update($this->nombreTabla, array("estado" => Vbancos::getEstadoCuentaInhabilitada()));
    }
    
    public function habilitar(){
        $this->oConnection->where("codigo", $this->codigo);
        return $this->oConnection->update($this->nombreTabla, array("estado" => Vbancos::getEstadoCuentaHabilitada()));
    }
    
    public function guardar(){
        return $this->guardarBanco_do_brasil();
    }
    
    public function asociarCuentaFilial($codFilial){
        $arrRegistros = array(
            "cod_filial" => $codFilial,
            "cod_banco" => $this->codigoBanco,
            "cod_cuenta" => $this->codigo
        );
        return $this->oConnection->insert("general.filiales_cuentas_bancos", $arrRegistros);
    }
    
    /* STATIC FUNCTIONS */
    
    static function getCampos(){
        $arrResp = array();
        $arrResp[] = "agencia";
        $arrResp[] = "conta";
        $arrResp[] = "contrato";
        $arrResp[] = "formatacao_convenio";
        $arrResp[] = "formatacao_nosso_numero";
        $arrResp[] = "identificacao";
        $arrResp[] = "cod_razon_social";
        $arrResp[] = "digito_agencia";
        $arrResp[] = "digito_cuenta";
        $arrResp[] = "estado";                
        return $arrResp;
    }
    
    public function getHeaderRemessa(Vremesas $myBoleto){
        $str = str_repeat(" ", 20);
        $str2 = str_repeat(" ", 10);
        $str3 = "00100000         ";
        if (strlen($myBoleto->cedente_cpf_cnpj) <= 11){
            $str3 .= "1";
        } else {
            $str3 .= "2";
        }
        $retorno = $str3.  str_pad($myBoleto->cedente_cpf_cnpj, 14, "0", STR_PAD_LEFT). str_repeat(" ", 20);
        $retorno .= str_pad($myBoleto->agencia, 5, "0", STR_PAD_LEFT). str_pad($myBoleto->digito_agencia, 1, " ", STR_PAD_RIGHT);
        $retorno .= str_pad($myBoleto->numero_cuenta, 12, "0", STR_PAD_LEFT). $myBoleto->digito_cuenta;
        $retorno .= " ".str_pad(substr($myBoleto->razon_social, 0, 30), 30, ' ', STR_PAD_RIGHT). str_pad(substr($myBoleto->nombre_banco, 0, 30), 30, ' ', STR_PAD_RIGHT);
        $retorno .= $str2."1".date("dmYhis")."000001"."08000000".$str.$str;
        $retorno = sustituirCaracteresEspeciales($retorno).  str_repeat(" ", 29);
        return $retorno;
    }
    
    public function getHeaderLoteRemessa(Vremesas $myBoleto){
        $str = str_repeat(" ", 40);
        $str2 = str_repeat(" ", 33);
        $sringToBeFit = str_pad($myBoleto->cedente_convenio, 9, "0", STR_PAD_LEFT)."0014";
        $sringToBeFit .= str_pad($myBoleto->cartera, 2, "0", STR_PAD_LEFT);
        $sringToBeFit .= str_pad($myBoleto->variacion_cartera, 3, "3", STR_PAD_LEFT);
        $str4 = "00100011R0100000 ";
        if (strlen($myBoleto->cedente_cpf_cnpj) <= 11){
            $str4 .= "1";
        } else {
            $str4 .= "2";
        }
        $retorno = $str4. str_pad($myBoleto->cedente_cpf_cnpj, 15, 0, STR_PAD_LEFT). str_pad($sringToBeFit, 20, " ", STR_PAD_RIGHT);
        $retorno .= str_pad($myBoleto->agencia, 5, "0", STR_PAD_LEFT). str_pad($myBoleto->digito_agencia, 1, " ", STR_PAD_RIGHT);
        $retorno .= str_pad($myBoleto->numero_cuenta, 12, "0", STR_PAD_LEFT). str_pad($myBoleto->digito_cuenta, 1, " ", STR_PAD_RIGHT);
        $retorno .= " ".str_pad(substr($myBoleto->razon_social, 0, 30), 30, ' ', STR_PAD_RIGHT). $str . $str;
        $retorno .= str_pad($myBoleto->getCodigo(), 8, "0", STR_PAD_LEFT).date("dmY")."00000000".$str2;        
        $retorno = sustituirCaracteresEspeciales($retorno);
        return $retorno;
    }
    
    public function getDetalleSegmentoPRemessa(Vremesas $myBoleto, Vboletos_bancarios $myLineaBoleto){
        $fechaVencimiento = strtotime($myLineaBoleto->fecha_vencimiento);
        $fechaVencimiento = date("dmY", $fechaVencimiento);
        $fechaDocumento = strtotime($myBoleto->fecha_documento);
        $fechaDocumento = date("dmY", $fechaDocumento);
        $str = "00100013";
        $codigoEstado = $myLineaBoleto->estado == Vboletos_bancarios::getEstadoBajaSolicitada() ? "02" : "01";
        $str .= str_pad(1, 5, "0", STR_PAD_LEFT)."P ".$codigoEstado;
        $str .= str_pad($myBoleto->agencia, 5, "0", STR_PAD_LEFT). str_pad($myBoleto->digito_agencia, 1, " ", STR_PAD_RIGHT);
        $str .= str_pad($myBoleto->numero_cuenta, 12, "0", STR_PAD_LEFT). str_pad($myBoleto->digito_cuenta, 1, " ", STR_PAD_RIGHT)." ";
        $sringToBeFit = $myBoleto->cedente_convenio;
        $str .= str_pad(substr($sringToBeFit, 0, 7), 7, "0", STR_PAD_RIGHT);
        $numeroSecuencia = str_pad($myLineaBoleto->cod_filial, 4, 0, STR_PAD_LEFT).str_pad($myLineaBoleto->numero_secuencial, 6, 0, STR_PAD_LEFT);
        $str .= $numeroSecuencia;
        $str .= str_repeat(" ", 3);        
        $str .= $this->limpiarCartera($myBoleto->cartera, $myBoleto->variacion_cartera)."1222".str_pad($myLineaBoleto->numero_documento, 15, " ", STR_PAD_RIGHT);
        $str .= $fechaVencimiento.str_pad(($myLineaBoleto->valor_boleto * 100), 15, "0", STR_PAD_LEFT)."00000 ";
        $str .= str_pad($myBoleto->especie_documento, 2, "0", STR_PAD_LEFT)."N";        
        $str .= $fechaDocumento;
        if ((float) ($myLineaBoleto->interes_mora) > 0){
            $str .= "2".$fechaVencimiento.str_pad(((float) ($myLineaBoleto->interes_mora)) * 100, 15, "0", STR_PAD_LEFT);
        } else {
            $str .= "3"."00000000"."000000000000000";
        }
        if ((float) ($myLineaBoleto->valor_descuento) > 0){
            $str .= "1".$fechaVencimiento.str_pad(((float) ($myLineaBoleto->valor_descuento)) * 100, 15, "0", STR_PAD_LEFT);
        } else {
            $str .= "000000000000000000000000";
        }
        $str .= "000000000000000"."000000000000000".str_pad($myLineaBoleto->numero_documento, 25, ' ', STR_PAD_RIGHT);
        if ($myLineaBoleto->instrucciones_cantidad_dias <> ''){
            $str .= "2".str_pad($myLineaBoleto->instrucoes[0]['QuantidadeDias'], 2, "0", STR_PAD_LEFT);
        } else {
            $str .= "300";
        }
        $retorno = sustituirCaracteresEspeciales($str)."2000090000000000 ";
        return $retorno;
    }
    
    public function getNossoNumero($convenio, $secuencial, $codFilial) {
        $numeroSecuencia = str_pad($codFilial, 4, 0, STR_PAD_LEFT).str_pad($secuencial, 6, 0, STR_PAD_LEFT);        
        $str_nossonumero = substr($convenio, 0, 7) . $numeroSecuencia;
        return $str_nossonumero;
    }
    
    public function getDetalleSegmentoQRemessa(Vboletos_bancarios $myLineaBoleto, $convenio){
        $str2 = str_repeat("0", 16);
        $str3 = str_repeat(" ", 40);
        $nossoNumero = $this->getNossoNumero($convenio, $myLineaBoleto->numero_secuencial, $myLineaBoleto->cod_filial);
        $str5 = "00100013";
        $codigoEstado = $myLineaBoleto->estado == Vboletos_bancarios::getEstadoBajaSolicitada() ? "02" : "01";
        $str5 .= str_pad(2, 5, "0", STR_PAD_LEFT)."Q ".$codigoEstado;
        if (strlen($myLineaBoleto->sacado_cpf_cnpj) <= 11){
            $str5 .= "1";
        } else {
            $str5 .= "2";
        }
        $nombre = sustituirCaracteresEspeciales($myLineaBoleto->sacado_nombre);
        $str6 = $str5.str_pad($myLineaBoleto->sacado_cpf_cnpj, 15, "0", STR_PAD_LEFT).  str_pad(strtoupper(substr($nombre, 0, 40)), 40, " ", STR_PAD_RIGHT);
        $direccion = sustituirCaracteresEspeciales($myLineaBoleto->sacado_direccion);
        $str6 .= str_pad(strtoupper(substr($direccion, 0, 40)), 40, " ", STR_PAD_RIGHT).  str_repeat(" ", 15);
        $cep = str_replace("-", "", $myLineaBoleto->sacado_cod_postal);
        $str6 .= str_pad(strtoupper(substr($cep, 0, 8)), 8, " ", STR_PAD_RIGHT);
        $ciudad = substr($myLineaBoleto->sacado_ciudad, 0, 15);
        $ciudad = sustituirCaracteresEspeciales($ciudad);
        $str6 .= str_pad(strtoupper($ciudad), 15, ' ', STR_PAD_RIGHT);
        $str6 .= str_pad(strtoupper($myLineaBoleto->sacado_codigo_estado), 2, ' ', STR_PAD_RIGHT).$str2.$str3."000";
        $str6 .= $nossoNumero.  str_repeat(" ", 11);
        return sustituirCaracteresEspeciales($str6);
    }
    
    public function getDetalleSegmentoRRemessa(Vboletos_bancarios $myLineaBoleto){
        $fechaMulta = strtotime($myLineaBoleto->fecha_multa);
        $fechaMulta = date("dmY", $fechaMulta);
        $retorno = "00100013".str_pad(3, 5, "0", STR_PAD_LEFT);
        $codigoEstado = $myLineaBoleto->estado == Vboletos_bancarios::getEstadoBajaSolicitada() ? "02" : "01";
        $retorno .= "R ".$codigoEstado;
        $retorno .= "000000000000000000000000"."000000000000000000000000"."2";
        $retorno .= $fechaMulta.  str_pad((float) ($myLineaBoleto->valor_multa) * 100, 15, "0", STR_PAD_LEFT);
        $retorno .= str_repeat(" ", 109)."00000000000000000000000000000   000000000 ";
        return sustituirCaracteresEspeciales($retorno);
    }
    
    public function getTrailerLoteRemessa($numeroRegistro){
        $retorno = "00100015         ".  str_pad($numeroRegistro, 6, "0", STR_PAD_LEFT);
        $retorno .= str_repeat("0", 92).str_repeat(" ", 125);
        return $retorno;
    }
    
    public function getTrailerArchivoRemessa($numeroRegistro){
        $numeroRegistro = ($numeroRegistro * 3) + 4;
        $retorno = "00199999         000001".str_pad($numeroRegistro, 6, "0", STR_PAD_LEFT)."000000".  str_repeat(" ", 205);
        return $retorno;
        
    }
    
    private function limpiarCartera($carteira, $variacion){
        return $carteira == "17" && $variacion == "019" ? "7" : "0";
    }
 
    public function getClassPrefijo(){
        return "bb";
    }
    
    static private function geraCodigoBanco($numero) {
            $parte1 = substr($numero, 0, 3);
            $parte2 = self::modulo_11($parte1);
            return $parte1 . "-" . $parte2;
        }
    
    static public function modulo_11($num, $base=9, $r=0) {
        $soma = 0;
        $fator = 2; 
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                    $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = "X";
            }

            if (strlen($num) == "43") {
                if ($digito == "0" or $digito == "X" or $digito > 9) {
                    $digito = 1;
                }
            }
            return $digito;
        } 
        elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
        
    private static function fator_vencimento($data) {
        $data = explode("/",$data);
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
        return(abs((self::_dateToDays("1997","10","07")) - (self::_dateToDays($ano, $mes, $dia))));
    }
    
    static private function _dateToDays($year,$month,$day) {
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }

        return ( floor((  146097 * $century)    /  4 ) +
            floor(( 1461 * $year)        /  4 ) +
            floor(( 153 * $month +  2) /  5 ) +
                $day +  1721119);
    }
        
        static private function formata_numero($numero,$loop,$insert,$tipo = "geral"){
            if ($tipo == "geral"){
                $numero = str_replace(",","",$numero);
                while(strlen($numero)<$loop){
                        $numero = $insert . $numero;
                }
            }
            if ($tipo == "valor"){
                $numero = str_replace(",","",$numero);
                while(strlen($numero)<$loop){
                        $numero = $insert . $numero;
                }
            }
            if ($tipo == "convenio") {
                    while(strlen($numero)<$loop){
                            $numero = $numero . $insert;
                    }
            }
            return $numero;
        }
    
    static private function monta_linha_digitavel($linha) {
        $p1 = substr($linha, 0, 4);
        $p2 = substr($linha, 19, 5);
        $p3 = self::modulo_10("$p1$p2");
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5);
        $campo1 = "$p5.$p6";

        $p1 = substr($linha, 24, 10);
        $p2 = self::modulo_10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo2 = "$p4.$p5";

        $p1 = substr($linha, 34, 10);
        $p2 = self::modulo_10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo3 = "$p4.$p5";
        $campo4 = substr($linha, 4, 1);
        $campo5 = substr($linha, 5, 14);
        return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
    }
        
    static private function modulo_10($num) { 
        $numtotal10 = 0;
        $fator = 2; 
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial10[$i] = $numeros[$i] * $fator;
            $numtotal10 .= $parcial10[$i];
            if ($fator == 2) {
                    $fator = 1;
            }
            else {
                    $fator = 2; 
            }
        }

        $soma = 0;
        for ($i = strlen($numtotal10); $i > 0; $i--) {
            $numeros[$i] = substr($numtotal10,$i-1,1);
            $soma += $numeros[$i]; 
        }
        $resto = $soma % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
                $digito = 0;
        }

        return $digito;
    }
    
    static private function tipo_documento($num_documento) {
        $num_documento = str_replace(".", "", $num_documento);
        $num_documento = str_replace("-", "", $num_documento);
        $num_documento = str_replace("/", "", $num_documento);
        if(strlen($num_documento)==11)
            return "cpf";
        else if(strlen($num_documento)==14)
            return "cnpj";
        else return "";
    }
    
    static private function fbarcode($valor){
        $fino = 1 ;
        $largo = 3 ;
        $altura = 50 ;
        $barcodes[0] = "00110" ;
        $barcodes[1] = "10001" ;
        $barcodes[2] = "01001" ;
        $barcodes[3] = "11000" ;
        $barcodes[4] = "00101" ;
        $barcodes[5] = "10100" ;
        $barcodes[6] = "01100" ;
        $barcodes[7] = "00011" ;
        $barcodes[8] = "10010" ;
        $barcodes[9] = "01010" ;
        for($f1=9;$f1>=0;$f1--){ 
            for($f2=9;$f2>=0;$f2--){  
                $f = ($f1 * 10) + $f2 ;
                $texto = "" ;
                for($i=1;$i<6;$i++){ 
                    $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
                }
                $barcodes[$f] = $texto;
            }
        }
        $html = "<img 
        src=".base_url()."assents/img/boleto_bancario/p.png width=".$fino." height=".$altura." border=0><img 
        src=".base_url()."assents/img/boleto_bancario/b.png width=".$fino." height=".$altura." border=0><img 
        src=".base_url()."assents/img/boleto_bancario/p.png width=".$fino." height=".$altura." border=0><img 
        src=".base_url()."assents/img/boleto_bancario/b.png width=".$fino." height=".$altura." border=0><img 
        ";

        $texto = $valor ;
        if((strlen($texto) % 2) <> 0){
                $texto = "0" . $texto;
        }
        while (strlen($texto) > 0) {
            $i = round(self::esquerda($texto,2));
            $texto = self::direita($texto,strlen($texto)-2);
            $f = $barcodes[$i];
            for($i=1;$i<11;$i+=2){
                if (substr($f,($i-1),1) == "0") {
                    $f1 = $fino ;
                } else {
                    $f1 = $largo ;
                }
                $html .= "src=".base_url()."assents/img/boleto_bancario/p.png width=".$f1." height=".$altura." border=0><img ";
                if (substr($f,$i,1) == "0") {
                    $f2 = $fino ;
                } else {
                    $f2 = $largo ;
                }
                $html .= "src=".base_url()."assents/img/boleto_bancario/b.png width=".$f2." height=".$altura." border=0><img ";
            }
        }
        $html .= "src=".base_url()."assents/img/boleto_bancario/p.png width=".$largo." height=".$altura." border=0><img 
        src=".base_url()."assents/img/boleto_bancario/b.png width=".$fino." height=".$altura." border=0><img 
        src=".base_url()."assents/img/boleto_bancario/p.png width=1 height=".$altura." border=0>";
        return $html;
    }
    
    static private function esquerda($entra,$comp){
        return substr($entra,0,$comp);
    }

    static private function direita($entra,$comp){
        return substr($entra,strlen($entra)-$comp,$comp);
    }
        
    static function getHTMLBoleto($dadosboleto){
        $codigobanco = "001";
        $codigo_banco_com_dv = self::geraCodigoBanco($codigobanco);
        $nummoeda = "9";
        $fator_vencimento = self::fator_vencimento($dadosboleto["data_vencimento"]);
        $valor = self::formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
        $agencia = self::formata_numero($dadosboleto["agencia"],4,0);
        $conta = self::formata_numero($dadosboleto["conta"],8,0);
        $carteira = $dadosboleto["carteira"];
        $agencia_codigo = $agencia."-". self::modulo_11($agencia) ." / ". $conta ."-". self::modulo_11($conta);
        $livre_zeros='000000';
        if ($dadosboleto["formatacao_convenio"] == "8") {
            $convenio = self::formata_numero($dadosboleto["convenio"],8,0,"convenio");
            $nossonumero = self::formata_numero($dadosboleto["nosso_numero"],9,0);
            $dv = self::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
            $linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
            $nossonumero = $convenio . $nossonumero ."-". self::modulo_11($convenio.$nossonumero);
        }

        if ($dadosboleto["formatacao_convenio"] == "7") {
            $convenio = self::formata_numero($dadosboleto["convenio"],7,0,"convenio");
            $nossonumero = self::formata_numero($dadosboleto["nosso_numero"],10,0);
            $dv = self::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
            $linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
            $nossonumero = $convenio.$nossonumero;
        }

        if ($dadosboleto["formatacao_convenio"] == "6") {
            $convenio = self::formata_numero($dadosboleto["convenio"],6,0,"convenio");	
            if ($dadosboleto["formatacao_nosso_numero"] == "1") {
                $nossonumero = self::formata_numero($dadosboleto["nosso_numero"],5,0);
                $dv = self::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira");
                $linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira";
                $nossonumero = $convenio . $nossonumero ."-". self::modulo_11($convenio.$nossonumero);
            }

            if ($dadosboleto["formatacao_nosso_numero"] == "2") {
                $nservico = "21";
                $nossonumero = self::formata_numero($dadosboleto["nosso_numero"],17,0);
                $dv = self::modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$nservico");
                $linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$nservico";
            }
        }

        $dadosboleto["codigo_barras"] = $linha;
        $dadosboleto["linha_digitavel"] = self::monta_linha_digitavel($linha);
        $dadosboleto["agencia_codigo"] = $agencia_codigo;
        $dadosboleto["nosso_numero"] = $nossonumero;
        $dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
        
        $html = '';
        $html .= '<style type="text/css">';
        $html .= '<!--';
        $html .= '.ti {font: 9px Arial, Helvetica, sans-serif}';
        $html .= '-->';
        $html .= '</style>';
        $html .= '</HEAD>';
        $html .= '<BODY>';
        $html .= '<STYLE>';
        $html .= '';
        $html .= '@media screen,print {';
        $html .= '';
        $html .= '/* *** TIPOGRAFIA BASICA *** */';
        $html .= '';
        $html .= '* {';
        $html .= '	font-family: Arial;';
        $html .= '	font-size: 12px;';
        $html .= '	margin: 0;';
        $html .= '	padding: 0;';
        $html .= '}';
        $html .= '';
        $html .= '.notice {';
        $html .= '	color: red;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= '/* *** LINHAS GERAIS *** */';
        $html .= '';
        $html .= '#container {';
        $html .= '	width: 666px;';
        $html .= '	margin: 0px auto;';
        $html .= '	padding-bottom: 30px;';
        $html .= '}';
        $html .= '';
        $html .= '#instructions {';
        $html .= '	margin: 0;';
        $html .= '	padding: 0 0 20px 0;';
        $html .= '}';
        $html .= '';
        $html .= '#boleto {';
        $html .= '	width: 666px;';
        $html .= '	margin: 0;';
        $html .= '	padding: 0;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= '/* *** CABECALHO *** */';
        $html .= '';
        $html .= '#instr_header {';
        $html .= '	background: url("'.base_url().'assents/img/boleto_bancario/logo_empresa.png") no-repeat top left;';
        $html .= '	padding-left: 160px;';
        $html .= '	height: 30px;';
        $html .= '	margin-top: 30px;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_header h1 {';
        $html .= '	font-size: 12px;';
        $html .= '	margin: 5px 0px;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_header address {';
        $html .= '	font-style: normal;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_content {';
        $html .= '';
        $html .= '}';
        $html .= '';
        $html .= '#instr_content h2 {';
        $html .= '	font-size: 10px;';
        $html .= '	font-weight: bold;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_content p {';
        $html .= '	font-size: 10px;';
        $html .= '	margin: 4px 0px;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_content ol {';
        $html .= '	font-size: 10px;';
        $html .= '	margin: 5px 0;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_content ol li {';
        $html .= '	font-size: 10px;';
        $html .= '	text-indent: 10px;';
        $html .= '	margin: 2px 0px;';
        $html .= '	list-style-position: inside;';
        $html .= '}';
        $html .= '';
        $html .= '#instr_content ol li p {';
        $html .= '	font-size: 10px;';
        $html .= '	padding-bottom: 4px;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= '/* *** BOLETO *** */';
        $html .= '';
        $html .= '#boleto .cut {';
        $html .= '	width: 666px;';
        $html .= '	margin: 0px auto;';
        $html .= '	border-bottom: 1px navy dashed;';
        $html .= '}';
        $html .= '';
        $html .= '#boleto .cut p {';
        $html .= '	margin: 0 0 0px 0;';
        $html .= '	padding: 0px;';
        $html .= '	font-family: "Arial Narrow";';
        $html .= '	font-size: 9px;';
        $html .= '	color: navy;';
        $html .= '}';
        $html .= '';
        $html .= 'table.header {';
        $html .= '	width: 666px;';
        $html .= '	height: 38px;';
        $html .= '	margin-top: 20px;';
        $html .= '	margin-bottom: 10px;';
        $html .= '	border-bottom: 2px navy solid;';
        $html .= '	';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= 'table.header div.field_cod_banco {';
        $html .= '	width: 46px;';
        $html .= '	height: 19px;';
        $html .= '  margin-left: 5px;';
        $html .= '	padding-top: 3px;';
        $html .= '	text-align: center;';
        $html .= '	font-size: 14px;';
        $html .= '	font-weight: bold;';
        $html .= '	color: navy;';
        $html .= '	border-right: 2px solid navy;';
        $html .= '	border-left: 2px solid navy;';
        $html .= '}';
        $html .= '';
        $html .= 'table.header td.linha_digitavel {';
        $html .= '	width: 464px;';
        $html .= '	text-align: right;';
        $html .= '	font: bold 15px Arial; ';
        $html .= '	color: navy';
        $html .= '}';
        $html .= '';
        $html .= 'table.line {';
        $html .= '	margin-bottom: 3px;';
        $html .= '	padding-bottom: 1px;';
        $html .= '	border-bottom: 1px black solid;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line tr.titulos td {';
        $html .= '	height: 13px;';
        $html .= '	font-family: "Arial Narrow";';
        $html .= '	font-size: 9px;';
        $html .= '	color: navy;';
        $html .= '	border-left: 5px #ffe000 solid;';
        $html .= '	padding-left: 2px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line tr.campos td {';
        $html .= '	height: 12px;';
        $html .= '	font-size: 10px;';
        $html .= '	color: black;';
        $html .= '	border-left: 5px #ffe000 solid;';
        $html .= '	padding-left: 2px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td p {';
        $html .= '	font-size: 10px;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= 'table.line tr.campos td.ag_cod_cedente,';
        $html .= 'table.line tr.campos td.nosso_numero,';
        $html .= 'table.line tr.campos td.valor_doc,';
        $html .= 'table.line tr.campos td.vencimento2,';
        $html .= 'table.line tr.campos td.ag_cod_cedente2,';
        $html .= 'table.line tr.campos td.nosso_numero2,';
        $html .= 'table.line tr.campos td.xvalor,';
        $html .= 'table.line tr.campos td.valor_doc2';
        $html .= '{';
        $html .= '	text-align: right;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line tr.campos td.especie,';
        $html .= 'table.line tr.campos td.qtd,';
        $html .= 'table.line tr.campos td.vencimento,';
        $html .= 'table.line tr.campos td.especie_doc,';
        $html .= 'table.line tr.campos td.aceite,';
        $html .= 'table.line tr.campos td.carteira,';
        $html .= 'table.line tr.campos td.especie2,';
        $html .= 'table.line tr.campos td.qtd2';
        $html .= '{';
        $html .= '	text-align: center;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.last_line {';
        $html .= '	vertical-align: top;';
        $html .= '	height: 25px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.last_line table.line {';
        $html .= '	margin-bottom: -5px;';
        $html .= '	border: 0 white none;';
        $html .= '}';
        $html .= '';
        $html .= 'td.last_line table.line td.instrucoes {';
        $html .= '	border-left: 0 white none;';
        $html .= '	padding-left: 5px;';
        $html .= '	padding-bottom: 0;';
        $html .= '	margin-bottom: 0;';
        $html .= '	height: 20px;';
        $html .= '	vertical-align: top;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.cedente {';
        $html .= '	width: 298px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.valor_cobrado2 {';
        $html .= '	padding-bottom: 0;';
        $html .= '	margin-bottom: 0;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= 'table.line td.ag_cod_cedente {';
        $html .= '	width: 126px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.especie {';
        $html .= '	width: 35px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.qtd {';
        $html .= '	width: 53px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.nosso_numero {';
        $html .= '	/* width: 120px; */';
        $html .= '	width: 115px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.num_doc {';
        $html .= '	width: 113px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.contrato {';
        $html .= '	width: 72px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.cpf_cei_cnpj {';
        $html .= '	width: 132px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.vencimento {';
        $html .= '	width: 134px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.valor_doc {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.desconto {';
        $html .= '	width: 113px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.outras_deducoes {';
        $html .= '	width: 112px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.mora_multa {';
        $html .= '	width: 113px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.outros_acrescimos {';
        $html .= '	width: 113px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.valor_cobrado {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '	background-color: #ffc ;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.sacado {';
        $html .= '	width: 659px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.local_pagto {';
        $html .= '	width: 472px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.vencimento2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '	background-color: #ffc;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.cedente2 {';
        $html .= '	width: 472px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.ag_cod_cedente2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.data_doc {';
        $html .= '	width: 93px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.num_doc2 {';
        $html .= '	width: 173px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.especie_doc {';
        $html .= '	width: 72px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.aceite {';
        $html .= '	width: 34px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.data_process{';
        $html .= '	width: 72px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.nosso_numero2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.reservado {';
        $html .= '	width: 93px;';
        $html .= '	background-color: #ffc;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.carteira {';
        $html .= '	width: 93px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.especie2 {';
        $html .= '	width: 53px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.qtd2 {';
        $html .= '	width: 133px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.xvalor {';
        $html .= '	/* width: 72px; */';
        $html .= '	width: 67px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.valor_doc2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= 'table.line td.instrucoes {';
        $html .= '	width: 475px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.desconto2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.outras_deducoes2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.mora_multa2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.outros_acrescimos2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.valor_cobrado2 {';
        $html .= '	/* width: 180px; */';
        $html .= '	width: 175px;';
        $html .= '	padding-right: 5px;';
        $html .= '	background-color: #ffc ;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.sacado2 {';
        $html .= '	width: 659px;';
        $html .= '}';
        $html .= '';
        $html .= 'table.line td.sacador_avalista {';
        $html .= '	width: 659px;';
        $html .= '}';
        $html .= '';
        $html .= '/*';
        $html .= 'table.line tr.campos td.sacador_avalista {';
        $html .= '	width: 472px;';
        $html .= '}';
        $html .= '*/';
        $html .= '';
        $html .= 'table.line tr.campos p.sacador_avalista {';
        $html .= '	height: 13px;';
        $html .= '	font-family: "Arial Narrow";';
        $html .= '	font-size: 9px;';
        $html .= '	color: navy;	';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= 'table.line td.cod_baixa {';
        $html .= '	color: navy;';
        $html .= '	width: 180px;';
        $html .= '  vertical-align:bottom;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= '';
        $html .= '';
        $html .= 'div.footer {';
        $html .= '	margin-bottom: 30px;';
        $html .= '}';
        $html .= '';
        $html .= 'div.footer p {';
        $html .= '	width: 88px;';
        $html .= '	margin: 0;';
        $html .= '	padding: 0;';
        $html .= '	padding-left: 525px;';
        $html .= '	font-family: "Arial Narro";';
        $html .= '	font-size: 9px;';
        $html .= '	color: navy;';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= 'div.barcode {';
        $html .= '	width: 666px;';
        $html .= '	margin-bottom: 20px;';
        $html .= '}';
        $html .= '';
        $html .= '}';
        $html .= '';
        $html .= '';
        $html .= '';
        $html .= '@media print {';
        $html .= '';
        $html .= '#instructions {';
        $html .= '	height: 1px;';
        $html .= '	visibility: hidden;';
        $html .= '	overflow: hidden;';
        $html .= '}';
        $html .= '';
        $html .= '}';
        $html .= '';
        $html .= '</STYLE>';
        $html .= '';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '';
        $html .= '<div id="container">';
        $html .= '';
        $html .= '	<div id="instr_header">';
        $cnpjcpf = isset($dadosboleto["cpf_cnpj"]) ? $dadosboleto["cpf_cnpj"] : '';
        $html .= '		<strong>'.$dadosboleto["identificacao"]." ".$cnpjcpf.'</strong><br/>';
        $html .= '		<address>'.$dadosboleto["endereco"] . ". " . $dadosboleto["cidade_uf"].'<br></address>';
        $html .= '	</div>	<!-- id="instr_header" -->';
        $html .= '';
        $html .= '	<div id="">';
        $html .= '<!--';
        $html .= '  Use no lugar do <div id=""> caso queira imprimir sem o logotipo e instru��es';
        $html .= '  <div id="instructions">';
        $html .= ' -->';
        $html .= '		';
        $html .= '		<div id="instr_content">';
        $html .= '			<p>';
        $html .= '				O pagamento deste boleto tamb&eacute;m poder&aacute; ser efetuado nos terminais de Auto-Atendimento BB. ';
        $html .= '				<strong>Instru&ccedil;&otilde;es abaixo:</strong>';
        $html .= '			</p>';
        $html .= '			';
        $html .= '			<ol>';
        $html .= '			<li>';
        $html .= '				Imprima em impressora jato de tinta (ink jet) ou laser, em ';
        $html .= '				qualidade normal ou alta. N&atilde;o use modo econ&ocirc;mico. ';
        $html .= '				<p class="notice">Por favor, configure margens esquerda e direita para 17mm, e as margens superior e inferior para 6mm.</p>';
        $html .= '			</li>';
        $html .= '			<li>';
        $html .= '				Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens';
        $html .= '				m&iacute;nimas &agrave; esquerda e &agrave; direita do ';
        $html .= '				formul&aacute;rio.';
        $html .= '			</li>';
        $html .= '			<li>';
        $html .= '				Corte na linha indicada. N&atilde;o rasure, risque, fure ou dobre ';
        $html .= '				a regi&atilde;o onde se encontra o c&oacute;digo de barras';
        $html .= '			</li>';
        $html .= '			</ol>';
        $html .= '		</div>	<!-- id="instr_content" -->';
        $html .= '	</div>	<!-- id="instructions" -->';
        $html .= '	';
        $html .= '	<div id="boleto">';
        $html .= '		<div class="cut">';
        $html .= '			<p>Corte na linha pontilhada</p>';
        $html .= '		</div>';
        $html .= '    <table cellspacing=0 cellpadding=0 width=666 border=0>';
        $html .= '		<TBODY>';
        $html .= '		<TR><TD class=ct width=666>';
        $html .= '		<div align=right>';
        $html .= '		<b class=cp>Recibo do Sacado</b></div></TD></tr></tbody></table>';
        $html .= '		<table class="header" border=0 cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr>';
        $html .= '                    <td width=150>';
        $html .= '                            <IMG SRC="'.base_url().'assents/img/boleto_bancario/logobb.jpg">';
        $html .= '                        </td>';
        $html .= '			<td width=50>';
        $html .= '        <div class="field_cod_banco">'.$dadosboleto["codigo_banco_com_dv"].'</div>';
        $html .= '			</td>';
        $html .= '			<td class="linha_digitavel">'.$dadosboleto["linha_digitavel"].'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="cedente">Cedente</TD>';
        $html .= '			<td class="ag_cod_cedente">Ag&ecirc;ncia / C&oacute;digo do Cedente</td>';
        $html .= '			<td class="especie">Esp&eacute;cie</TD>';
        $html .= '			<td class="qtd">Quantidade</TD>';
        $html .= '			<td class="nosso_numero">Nosso n&uacute;mero</td>';
        $html .= '		</tr>';
        $html .= '';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="cedente">'.$dadosboleto["cedente"].'&nbsp;</td>';
        $html .= '			<td class="ag_cod_cedente">'.$dadosboleto["agencia_codigo"].'&nbsp;</td>';
        $html .= '			<td class="especie">'.$dadosboleto["especie"].'&nbsp;</td>';
        $html .= '			<TD class="qtd">'.$dadosboleto["quantidade"].'&nbsp;</td>';
        $html .= '			<TD class="nosso_numero">'.$dadosboleto["nosso_numero"].'-'.self::modulo_11($dadosboleto["nosso_numero"]).'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '';
        $html .= '		<table class="line" cellspacing="0" cellPadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="num_doc">N&uacute;mero do documento</td>';
        $html .= '			<td class="contrato">Contrato</TD>';
        $html .= '			<td class="cpf_cei_cnpj">CPF/CEI/CNPJ</TD>';
        $html .= '			<td class="vencmento">Vencimento</TD>';
        $html .= '			<td class="valor_doc">Valor documento</TD>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="num_doc">'.$dadosboleto["numero_documento"].'</td>';
        $html .= '			<td class="contrato">'.$dadosboleto["contrato"].'</td>';
        $html .= '			<td class="cpf_cei_cnpj">'.$dadosboleto["cpf_cnpj"].'</td>';
        $html .= '			<td class="vencimento">'.$dadosboleto["data_vencimento"].'</td>';
        $html .= '			<td class="valor_doc">'.$dadosboleto["valor_boleto"].'</td>';
        $html .= '		</tr>';
        $html .= '      </tbody>';
        $html .= '      </table>';
        $html .= '';
        $html .= '		<table class="line" cellspacing="0" cellPadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="desconto">(-) Desconto / Abatimento</td>';
        $html .= '			<td class="outras_deducoes">(-) Outras dedu&ccedil;&otilde;es</td>';
        $html .= '			<td class="mora_multa">(+) Mora / Multa</td>';
        $html .= '			<td class="outros_acrescimos">(+) Outros acr&eacute;scimos</td>';
        $html .= '			<td class="valor_cobrado">(=) Valor cobrado</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="desconto">&nbsp;</td>';
        $html .= '			<td class="outras_deducoes">&nbsp;</td>';
        $html .= '			<td class="mora_multa">&nbsp;</td>';
        $html .= '			<td class="outros_acrescimos">&nbsp;</td>';
        $html .= '			<td class="valor_cobrado">&nbsp;</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '';
        $html .= '      ';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="sacado">Sacado</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="sacado">'.$dadosboleto["sacado"].'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '		';
        $html .= '		<div class="footer">';
        $html .= '			<p>Autentica&ccedil;&atilde;o mec&acirc;nica</p>';
        $html .= '		</div>';
        $html .= '';
        $html .= '		';
        $html .= '		<div class="cut">';
        $html .= '			<p>Corte na linha pontilhada</p>';
        $html .= '		</div>';
        $html .= '';
        $html .= '';
        $html .= '		<table class="header" border=0 cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr>';
        $html .= '			<td width=150><IMG SRC="'.base_url().'assents/img/boleto_bancario/logobb.jpg"></td>';
        $html .= '			<td width=50>';
        $html .= '        <div class="field_cod_banco">'.$dadosboleto["codigo_banco_com_dv"].'</div>';
        $html .= '			</td>';
        $html .= '			<td class="linha_digitavel">'.$dadosboleto["linha_digitavel"].'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="local_pagto">Local de pagamento</td>';
        $html .= '			<td class="vencimento2">Vencimento</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="local_pagto">PAG&Aacute;VEL EM QUALQUER BANCO AT&Eacute; O VENCIMENTO.</td>';
        $html .= '			<td class="vencimento2">'.$dadosboleto["data_vencimento"].'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '		';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="cedente2">Cedente</td>';
        $html .= '			<td class="ag_cod_cedente2">Ag&ecirc;ncia/C&oacute;digo cedente</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="cedente2">'.$dadosboleto["cedente"].'</td>';
        $html .= '			<td class="ag_cod_cedente2">'.$dadosboleto["agencia_codigo"].'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="data_doc">Data do documento</td>';
        $html .= '			<td class="num_doc2">No. documento</td>';
        $html .= '			<td class="especie_doc">Esp&eacute;cie doc.</td>';
        $html .= '			<td class="aceite">Aceite</td>';
        $html .= '			<td class="data_process">Data process.</td>';
        $html .= '			<td class="nosso_numero2">Nosso n&uacute;mero</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="data_doc">'.$dadosboleto["data_documento"].'</td>';
        $html .= '			<td class="num_doc2">'.$dadosboleto["numero_documento"].'</td>';
        $html .= '			<td class="especie_doc">'.$dadosboleto["especie_doc"].'</td>';
        $html .= '			<td class="aceite">'.$dadosboleto["aceite"].'</td>';
        $html .= '			<td class="data_process">'.$dadosboleto["data_processamento"].'</td>';
        $html .= '			<td class="nosso_numero2">'.$dadosboleto["nosso_numero"].'-'.self::modulo_11($dadosboleto["nosso_numero"]).'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '';
        $html .= '		<table class="line" cellspacing="0" cellPadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="reservado">Uso do  banco</td>';
        $html .= '			<td class="carteira">Carteira</td>';
        $html .= '			<td class="especie2">Esp&eacute;cie</td>';
        $html .= '			<td class="qtd2">Quantidade</td>';
        $html .= '			<td class="xvalor">x Valor</td>';
        $html .= '			<td class="valor_doc2">(=) Valor documento</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="reservado">&nbsp;</td>';
        $html .= '			<td class="carteira">';
        $html .= '			'.$dadosboleto["carteira"].' ';
        $html .= isset($dadosboleto["variacao_carteira"]) ? $dadosboleto["variacao_carteira"] : '&nbsp;';
        $html .= ' </td>';
        $html .= '			<td class="especie2">'.$dadosboleto["especie"].'</td>';
        $html .= '			<td class="qtd2"> '.$dadosboleto["quantidade"].' </td>';
        $html .= '			<td class="xvalor">'.$dadosboleto["valor_unitario"].'</td>';
        $html .= '			<td class="valor_doc2">'.$dadosboleto["valor_boleto"].'</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '		';
        $html .= '		';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr><td class="last_line" rowspan="6">';
        $html .= '			<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '			<tbody>';
        $html .= '			<tr class="titulos">';
        $html .= '				<td class="instrucoes">';
        $html .= '						Instru&ccedil;&otilde;es (Texto de responsabilidade do cedente)';
        $html .= '				</td>';
        $html .= '			</tr>';
        $html .= '			<tr class="campos">';
        $html .= '				<td class="instrucoes" rowspan="5">';
        $html .= '					<p>'.$dadosboleto["demonstrativo1"].'</p>		';
        $html .= '					<p>'.$dadosboleto["demonstrativo2"].'</p>';
        $html .= '					<p>'.$dadosboleto["demonstrativo3"].'</p>';
        $html .= '					<p>'.$dadosboleto["instrucoes1"].'</p>';
        $html .= '					<p>'.$dadosboleto["instrucoes2"].'</p>';
        $html .= '					<p>'.$dadosboleto["instrucoes3"].'</p>';
        $html .= '					<p>'.$dadosboleto["instrucoes4"].'</p>';
        $html .= '				</td>';
        $html .= '			</tr>';
        $html .= '			</tbody>';
        $html .= '			</table>';
        $html .= '		</td></tr>';
        $html .= '		';
        $html .= '		<tr><td>';
        $html .= '			<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '			<tbody>';
        $html .= '			<tr class="titulos">';
        $html .= '				<td class="desconto2">(-) Desconto / Abatimento</td>';
        $html .= '			</tr>';
        $html .= '			<tr class="campos">';
        $html .= '				<td class="desconto2">&nbsp;</td>';
        $html .= '			</tr>';
        $html .= '			</tbody>';
        $html .= '			</table>';
        $html .= '		</td></tr>';
        $html .= '		';
        $html .= '		<tr><td>';
        $html .= '			<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '			<tbody>';
        $html .= '			<tr class="titulos">';
        $html .= '				<td class="outras_deducoes2">(-) Outras dedu&ccedil;&otilde;es</td>';
        $html .= '			</tr>';
        $html .= '			<tr class="campos">';
        $html .= '				<td class="outras_deducoes2">&nbsp;</td>';
        $html .= '			</tr>';
        $html .= '			</tbody>';
        $html .= '			</table>';
        $html .= '		</td></tr>';
        $html .= '';
        $html .= '		<tr><td>';
        $html .= '			<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '			<tbody>';
        $html .= '			<tr class="titulos">';
        $html .= '				<td class="mora_multa2">(+) Mora / Multa</td>';
        $html .= '			</tr>';
        $html .= '			<tr class="campos">';
        $html .= '				<td class="mora_multa2">&nbsp;</td>';
        $html .= '			</tr>';
        $html .= '			</tbody>';
        $html .= '			</table>';
        $html .= '		</td></tr>';
        $html .= '';
        $html .= '		<tr><td>';
        $html .= '			<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '			<tbody>';
        $html .= '			<tr class="titulos">';
        $html .= '				<td class="outros_acrescimos2">(+) Outros Acr&eacute;scimos</td>';
        $html .= '			</tr>';
        $html .= '			<tr class="campos">';
        $html .= '				<td class="outros_acrescimos2">&nbsp;</td>';
        $html .= '			</tr>';
        $html .= '			</tbody>';
        $html .= '			</table>';
        $html .= '		</td></tr>';
        $html .= '';
        $html .= '		<tr><td class="last_line">';
        $html .= '			<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '			<tbody>';
        $html .= '			<tr class="titulos">';
        $html .= '				<td class="valor_cobrado2">(=) Valor cobrado</td>';
        $html .= '			</tr>';
        $html .= '			<tr class="campos">';
        $html .= '				<td class="valor_cobrado2">&nbsp;</td>';
        $html .= '			</tr>';
        $html .= '			</tbody>';
        $html .= '			</table>';
        $html .= '		</td></tr>';
        $html .= '		</tbody>';
        $html .= '		</table>';
        $html .= '		';
        $html .= '		';
        $html .= '		<table class="line" cellspacing="0" cellPadding="0">';
        $html .= '		<tbody>';
        $html .= '		</tbody>';
        $html .= '		</table>		';
        $html .= '		';
        $html .= '		<table class="line" cellspacing="0" cellpadding="0">';
        $html .= '		<tbody>';
        $html .= '		<tr class="titulos">';
        $html .= '			<td class="sacado2" colspan="2">Sacado</td>';
        $html .= '		</tr>';
        $html .= '		<tr class="campos">';
        $html .= '			<td class="sacado2">';
        $html .= '				<p>';
        $html .= '         '.$dadosboleto["sacado"];
        $doc = $dadosboleto["cpf_cnpj_sacado"];
        if(self::tipo_documento($doc) == "cpf")
            $doc = " / CPF $doc";
        else if(self::tipo_documento($doc) == "cnpj")
            $doc = " / CNPJ $doc";

        $html .= $doc;
        $html .= '       </p>';
        $html .= '				<p>'.$dadosboleto["endereco1"].'</p>';
        $html .= '				<p>'.$dadosboleto["endereco2"].'</p>';
        $html .= '       <p class="sacador_avalista">Sacador/Avalista</p>';
        $html .= '			</td>';
        $html .= '';
        $html .= '			<td class="cod_baixa">C&oacute;d. baixa</td>';
        $html .= '		</tr>';
        $html .= '		</tbody>';
        $html .= '		</table>		';
        $html .= '   <table cellspacing=0 cellpadding=0 width=666 border=0>';
        $html .= '		<TBODY><TR><TD width=666 align=right >';
        $html .= '			<font style="font-size: 10px;">Autentica&ccedil;&atilde;o mec&acirc;nica - Ficha de Compensa&ccedil;&atilde;o</font></TD></tr></tbody></table>';
        $html .= '		<div class="barcode">';
        $html .= '			<p>'.self::fbarcode($dadosboleto["codigo_barras"]).'</p>';
        $html .= '		</div>';
        $html .= '		<!--';
        $html .= '		<div class="cut">';
        $html .= '			<p>Corte na linha pontilhada</p>';
        $html .= '		</div>';
        $html .= '		-->';
        $html .= '	</div>';
        $html .= '';
        $html .= '</div>';
        $html .= '';
        $html .= '</body>';
        $html .= '';
        $html .= '</html>';
        return $html;
    }
    
    public function getDescripcionRetorno($codigoRetorno, $codigoMotivo = null){
        if (!isset(self::$C044[$codigoRetorno])){
            throw new Exception("No se reconoce el codigo de retorno");
        } else {
            $arrResp = array();
            $nombreRetorno = self::$C044[$codigoRetorno];
            $descripcionRetorno = "";
            if ($codigoMotivo != null){
                if (($codigoRetorno == '03' || $codigoRetorno == '26' || $codigoRetorno == '30') && ($codigoMotivo <> '00' && isset(self::$C047A[$codigoMotivo]))){
                    $descripcionRetorno = self::$C047A[$codigoMotivo];
                } else if ($codigoRetorno == '28' && isset(self::$C047B[$codigoMotivo])){
                    $descripcionRetorno = self::$C047B[$codigoMotivo];
                } else if (($codigoRetorno == '06' || $codigoRetorno == '09' || $codigoRetorno == '17') && ($codigoMotivo <> '00' && isset(self::$C047C[$codigoMotivo]))){
                    $descripcionRetorno = self::$C047C[$codigoMotivo];
                }
            }
            $arrResp['motivo'] = $nombreRetorno;
            $arrResp['descripcion'] = $descripcionRetorno;
            return $arrResp;
        }
    }
    
}
