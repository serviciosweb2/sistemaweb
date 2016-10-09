<?php

/**
 * Class Tbancos
 *
 * Class  Tbancos maneja todos los aspectos de bancos
 *
 * @package  SistemaIGA
 * @subpackage Boleto    
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @version  $Revision: 1.1 $
 * @access   public
 */
class Vboleto extends class_general {

    public $carteira = "";
    public $variacaoCarteira = "";
    public $nossoNumero = "";
    public $digitoNossoNumero = "";
    public $dataVencimento;
    public $dataDocumento;
    public $dataProcessamento;
    public $numeroParcela = 0;
    public $valorBoleto = 0;
    public $valorCobrado = 0; // no completar.
    public $localPagamento = "Ate o vencimento, preferencialmente no ";
    public $quantidadeMoeda = 1;
    public $valorMoeda = ""; // no completar
    public $instrucoes = "";
    public $especieDocumento = "DM";
    public $aceite = "N";
    public $numeroDocumento = "";
    public $especie = "R$";
    public $moeda = 9;
    public $usoBanco = "";
    public $codigoBarra = "";
    public $cedente;
    public $categoria = 0;
    public $banco;
    public $valorDesconto;
    public $sacado;
    public $jurosPermanente;
    public $percJurosMora = 0;
    public $jurosMora;
    public $iof;
    public $abatimento;
    public $percMulta = 0;
    public $valorMulta = 0;
    public $outrosAcrescimos;
    public $outrosDescontos;
    public $dataJurosMora;
    public $dataMulta;
    public $dataDesconto;
    public $dataOutrosAcrescimos;
    public $dataOutrosDescontos;
    public $percentualIOS = 0;
    public $tipoModalidade = "";
    protected $oConnection;

    /* CONSTRUCTOR */

    /**
     * Constructor de la Clase bancos
     *
     * @param CI_DB_mysqli_driver $connection
     * @param integer $codigo (opcional) el codigo del objeto a crear
     */
    function __construct(CI_DB_mysqli_driver $conexion, $dataVencimento, $numeroParcela, Vcobros $cobro, $instrucciones, $especieDocumento, Vcedentes $cedente, Vsacados $sacado, Vbancos $banco) {
        $this->oConnection = $conexion;
        $this->dataVencimento = $dataVencimento;



        $this->variacaoCarteira = $cedente->variacao_carteira;
        $this->valorBoleto = $cobro->importe;
        $this->carteira = $cedente->carteira;
        $this->numeroDocumento = $cobro->getCodigo(); ///ver que va...
        $this->dataProcessamento = date("Ymd");
        $this->dataDocumento = date("Ymd");
        $this->instrucoes = $instrucciones;
        $this->especieDocumento = $especieDocumento;
        $this->sacado = $sacado;
        $this->banco = $banco;
        $this->nossoNumero = $this->getNossoNumero($cedente->convenio, $cedente->numeroSequencial + 1);
        $this->jurosPermanente = true;
        $this->percMulta = $this->getMoraPerc();
        $this->percJurosMora = $this->getMoraPerc();
        $this->dataMulta = "00000000";
        $this->dataJurosMora = $dataVencimento;
        $this->numeroParcela = $numeroParcela;
        $this->digitoNossoNumero = $cedente->cuenta_bancaria->digito_cuenta;
        $this->cedente= $cedente;
    }

    public function getMoraPerc() {
        $condiciones = array("baja" => 0,
            "diariamente" => 1);
        $moras = Vmoras::listarMoras($this->oConnection, $condiciones);

        return $moras[0]["mora"];
    }

    public function getMultaPerc() {
        $condiciones = array("baja" => 0,
            "diariamente" => 0);
        $moras = Vmoras::listarMoras($this->oConnection, $condiciones);
        return $moras[0]["mora"];
    }

    public function getNossoNumero($convenio, $secuencial) {
        $str_nossonumero = substr($convenio, 0, 7) . str_pad($secuencial, 10, "0", STR_PAD_LEFT);

        return $str_nossonumero;
    }

    public function guardarBoleto() {

        $data = array(
            'cod_cobro' => $this->numeroDocumento,
            'nossonumero' => $this->nossoNumero,
            "variacao_carteira" => $this->variacaoCarteira,
            "valor_boleto" => $this->valorBoleto,
            "carteira" => $this->carteira,
            "data_processamento" => $this->dataProcessamento,
            "data_documento" => $this->dataDocumento,
            "instrucoes" => $this->instrucoes[0],
            "especie_documento" => $this->especieDocumento,
            "cedente" => $this->cedente->convenio,
            "sacado_nombre" => $this->sacado->nombre,
            "sacado_cpf_cnpj" => $this->sacado->cpf_cnpj,
            "sacado_direccion" => $this->sacado->direccion,
            "juros_permanente" => $this->jurosPermanente,
            "perc_multa" => $this->percMulta,
            "perc_Juros_Mora" => $this->percJurosMora,
            "data_multa" => $this->dataMulta,
            "data_Juros_mora" => $this->dataJurosMora,
            "numero_parcela" => $this->numeroParcela,
            "digito_nosso_numero" => $this->digitoNossoNumero,
            "cedente_endereco"=>  $this->cedente->endereco,
            "cedente_razon_social"=>  $this->cedente->razon_social,
            "cedente_digito"=>  $this->cedente->digitoCedente
        );
//        print_r($data);
        $this->oConnection->insert('medio_boleto', $data);
    }

}

?>