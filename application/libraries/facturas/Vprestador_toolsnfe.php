<?php

/**
 * Class Vprestador_toolsnfe
 *
 * Class  Vprestador_toolsnfe maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vprestador_toolsnfe extends Tprestador_toolsnfe {

    static private $URLPortal = "http://www.portalfiscal.inf.br/nfe";
    static private $aURL = array(35 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.fazenda.sp.gov.br/ws/nfeautorizacao.asmx", "method" => "nfeAutorizacao", "version" => "3.10"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.fazenda.sp.gov.br/ws/nferetautorizacao.asmx", "method" => "NfeRetAutorizacao", "version" => "3.10"),
            "NfeCancelamento" => Array("URL" => "https://nfe.fazenda.sp.gov.br/nfeweb/services/nfecancelamento2.asmx", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.fazenda.sp.gov.br/nfeweb/services/nfeinutilizacao2.asmx", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.fazenda.sp.gov.br/ws/nfeconsulta2.asmx", "method" => "nfeConsultaNF2", "version" => "2.00"),
            "NfeStatusServico" => Array("URL" => "https://nfe.fazenda.sp.gov.br/nfeweb/services/nfestatusservico2.asmx", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.fazenda.sp.gov.br/nfeweb/services/cadconsultacadastro2.asmx", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.fazenda.sp.gov.br/eventosWEB/services/RecepcaoEvento.asmx", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        41 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/NFeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/NFeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/NFeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/NFeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/NFeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/NFeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe/CadConsultaCadastro2", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe2.fazenda.pr.gov.br/nfe-evento/NFeRecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        13 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/NfeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.am.gov.br/services2/services/RecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        29 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeRecepcao2.asmx", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeRetRecepcao2.asmx", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeCancelamento2.asmx", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeInutilizacao2.asmx", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeConsulta2.asmx", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/NfeStatusServico2.asmx", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/nfenw/CadConsultaCadastro2.asmx", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.ba.gov.br/webservices/sre/nferecepcaoevento.asmx", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        23 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/NfeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/CadConsultaCadastro2", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.ce.gov.br/nfe2/services/RecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        52 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/CadConsultaCadastro2?wsdl", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.go.gov.br/nfe/services/v2/NfeRecepcaoEvento?wsdl", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        31 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "3.10"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/NfeRetRecepcao2", "method" => "NfeRetRecepcao2", "version" => "3.10"),
            "NfeCancelamento" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/cadconsultacadastro2", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.fazenda.mg.gov.br/nfe2/services/RecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        50 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/CadConsultaCadastro2", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.fazenda.ms.gov.br/producao/services2/NfeRecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        51 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/CadConsultaCadastro2", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.mt.gov.br/nfews/v2/services/RecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        20 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeRecepcao2", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeRetRecepcao2", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeCancelamento2", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeInutilizacao2", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeConsulta2", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/NfeStatusServico2", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/CadConsultaCadastro2", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.pe.gov.br/nfe-service/services/RecepcaoEvento", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
        43 => array(
            "NfeRecepcao" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/Nferecepcao/NFeRecepcao2.asmx", "method" => "nfeRecepcaoLote2", "version" => "2.00"),
            "NfeRetRecepcao" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/NfeRetRecepcao/NfeRetRecepcao2.asmx", "method" => "nfeRetRecepcao2", "version" => "2.00"),
            "NfeCancelamento" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/NfeCancelamento/NfeCancelamento2.asmx", "method" => "nfeCancelamentoNF2", "version" => "2.00"),
            "NfeInutilizacao" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/nfeinutilizacao/nfeinutilizacao2.asmx", "method" => "nfeInutilizacaoNF2", "version" => "2.00"),
            "NfeConsulta" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/NfeConsulta/NfeConsulta2.asmx", "method" => "nfeConsultaNF2", "version" => "2.01"),
            "NfeStatusServico" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/NfeStatusServico/NfeStatusServico2.asmx", "method" => "nfeStatusServicoNF2", "version" => "2.00"),
            "CadConsultaCadastro" => Array("URL" => "https://sef.sefaz.rs.gov.br/ws/cadconsultacadastro/cadconsultacadastro2.asmx", "method" => "consultaCadastro2", "version" => "2.00"),
            "RecepcaoEvento" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/recepcaoevento/recepcaoevento.asmx", "method" => "nfeRecepcaoEvento", "version" => "1.00"),
            "NfeConsultaDest" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/nfeConsultaDest/nfeConsultaDest.asmx", "method" => "nfeConsultaNFDest", "version" => "2.00"),
            "NfeDownloadNF" => Array("URL" => "https://nfe.sefaz.rs.gov.br/ws/nfeDownloadNF/nfeDownloadNF.asmx", "method" => "nfeDownloadNF", "version" => "2.00")
        ),
    );

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */

    private function __limpaString($texto) {
        $aFind = array('&', 'á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç', 'Á', 'À', 'Ã', 'Â', 'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'Ü', 'Ç');
        $aSubs = array('e', 'a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c', 'A', 'A', 'A', 'A', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'C');
        $novoTexto = str_replace($aFind, $aSubs, $texto);
        $novoTexto = preg_replace("/[^a-zA-Z0-9 @,-.;:\/_]/", "", $novoTexto);
        return $novoTexto;
    }

    static private function txt_to_xml(array $arrayComAsLinhasDoArquivo, $exportar = false) {
        $arquivo = $arrayComAsLinhasDoArquivo;
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $NFe = $dom->createElement("NFe");
        $NFe->setAttribute("xmlns", self::$URLPortal);
        $clave = '';
        for ($l = 0; $l < count($arquivo); $l++) {
            $dados = explode("|", $arquivo[$l]);
            for ($x = 0; $x < count($dados); $x++) {
                if (!empty($dados[$x])) {
                    $dados[$x] = preg_replace('/\s\s+/', " ", $dados[$x]);
                    $dados[$x] = self::__limpaString(trim($dados[$x]));
                }
            }

            switch ($dados[0]) {
                case "NOTA FISCAL":
                    break;

                case "A":  //atributos da NFe, campos obrigatórios [NFe]
                    $infNFe = $dom->createElement("infNFe");
                    
                    $infNFe->setAttribute("Id", $dados[2]);
                    $infNFe->setAttribute("versao", "3.10");
                    //pega a chave de 44 digitos excluindo o a sigla NFe
                    $clave = substr($dados[2], 3, 44);
                    $tipoAmbiente = '';
                    break;

                case "B": //identificadores [infNFe]
                    //B|cUF|cNF|NatOp|indPag|mod|serie|nNF|dEmi|dSaiEnt|hSaiEnt|tpNF|cMunFG|TpImp|TpEmis|cDV|tpAmb|finNFe|procEmi|VerProc|dhCont|xJust
                    $ide = $dom->createElement("ide");
                    $cUF = $dom->createElement("cUF", $dados[1]);
                    $ide->appendChild($cUF);
                    $cNF = $dom->createElement("cNF", $dados[2]);
                    $ide->appendChild($cNF);
                    $NatOp = $dom->createElement("natOp", $dados[3]);
                    $ide->appendChild($NatOp);
                    $indPag = $dom->createElement("indPag", $dados[4]);
                    $ide->appendChild($indPag);
                    $mod = $dom->createElement("mod", $dados[5]);
                    $ide->appendChild($mod);
                    $serie = $dom->createElement("serie", $dados[6]);
                    $ide->appendChild($serie);
                    $nNF = $dom->createElement("nNF", $dados[7]);
                    $ide->appendChild($nNF);
                    if ($exportar){
                        $dEmi = $dom->createElement("dhEmi", $dados[8]."T01:00:00-03:00"); 
                    } else {
                        $dEmi = $dom->createElement("dhEmi", date("Y-m-d")."T01:00:00-03:00"); 
                    }
//                    $dEmi = $dom->createElement("dEmi", $dados[8]);
                                       
                    $ide->appendChild($dEmi);
//                    if (!empty($dados[9])) {
////                        $dSaiEnt = $dom->createElement("dSaiEnt", $dados[9]);
//                        $dSaiEnt = $dom->createElement("dhSaiEnt", date("Y-m-d")."T".date("H:i:s"));
//                        
//                        $ide->appendChild($dSaiEnt);
//                    }
//                    if (!empty($dados[10])) {
//                        $hSaiEnt = $dom->createElement("hSaiEnt", $dados[10]);
//                        $ide->appendChild($hSaiEnt);
//                    }
                    $tpNF = $dom->createElement("tpNF", $dados[11]);
                    $ide->appendChild($tpNF);
                    $temp = explode("|", $arquivo[10]);
                    $idDesti = $temp[6] == '6101' || $temp[6] == '6102' || $temp[6] == '6107' || $temp[6] == '6108' ? "2" : "1";
                    $idDest = $dom->createElement("idDest", $idDesti);
                    $ide->appendChild($idDest);                   
                    
                    $cMunFG = $dom->createElement("cMunFG", $dados[12]);
                    $ide->appendChild($cMunFG);
                    $tpImp = $dom->createElement("tpImp", $dados[13]);
                    $ide->appendChild($tpImp);
                    $tpEmis = $dom->createElement("tpEmis", $dados[14]);
                    $ide->appendChild($tpEmis);
                    $CDV = $dom->createElement("cDV", $dados[15]);
                    $ide->appendChild($CDV);
                    $tpAmb = $dom->createElement("tpAmb", $dados[16]);
                    $tipoAmbiente = $dados[16];
                    $ide->appendChild($tpAmb);
                    $finNFe = $dom->createElement("finNFe", $dados[17]);
                    $ide->appendChild($finNFe);
                    $indFinal = $dom->createElement("indFinal", "1");
                    $ide->appendChild($indFinal);
                    $indPres = $dom->createElement("indPres", $dados[11]);
                    $ide->appendChild($indPres);                    
                    $procEmi = $dom->createElement("procEmi", $dados[18]);
                    $ide->appendChild($procEmi);
                    if (empty($dados[19])) {
                        $dados[19] = "NfePHP";
                    }
                    $verProc = $dom->createElement("verProc", $dados[19]);
                    $ide->appendChild($verProc);
                    if (!empty($dados[20])) {
                        $dhCont = $dom->createElement("dhCont", $dados[20]);
                        $ide->appendChild($dhCont);
                    }
                    if (!empty($dados[21])) {
                        $xJust = $dom->createElement("xJust", $dados[21]);
                        $ide->appendChild($xJust);
                    }
                    $infNFe->appendChild($ide);
                    break;

                case "B13": //NFe referenciadas [ide]
                    if (!isset($NFref)) {
                        $NFref = $dom->createElement("NFref");
                        $ide->insertBefore($ide->appendChild($NFref), $tpImp);
                    }
                    $refNFe = $dom->createElement("refNFe", $dados[1]);
                    $NFref->appendChild($refNFe);
                    break;

                case "B14": //NF referenciadas [NFref]
                    //B14|cUF|AAMM(ano mês)|CNPJ|Mod|serie|nNF|
                    if (!isset($NFref)) {
                        $NFref = $dom->createElement("NFref");
                        $ide->insertBefore($ide->appendChild($NFref), $tpImp);
                    }
                    $refNF = $dom->createElement("refNF");
                    $cUF = $dom->createElement("cUF", $dados[1]);
                    $refNF->appendChild($cUF);
                    $AAMM = $dom->createElement("AAMM", $dados[2]);
                    $refNF->appendChild($AAMM);
                    $CNPJ = $dom->createElement("CNPJ", $dados[3]);
                    $refNF->appendChild($CNPJ);
                    $mod = $dom->createElement("mod", $dados[4]);
                    $refNF->appendChild($mod);
                    $serie = $dom->createElement("serie", $dados[5]);
                    $refNF->appendChild($serie);
                    $nNF = $dom->createElement("nNF", $dados[6]);
                    $refNF->appendChild($nNF);
                    $NFref->appendChild($refNF);
                    break;

                case "B20a": //Grupo de informações da NF [NFref]
                    if (!isset($NFref)) {
                        $NFref = $dom->createElement("NFref");
                        $ide->insertBefore($ide->appendChild($NFref), $tpImp);
                    }
                    $refNFP = $dom->createElement("refNFP");
                    $cUF = $dom->createElement("cUF", $dados[1]);
                    $refNFP->appendChild($cUF);
                    $AAMM = $dom->createElement("AAMM", $dados[2]);
                    $refNFP->appendChild($AAMM);
                    $IE = $dom->createElement("IE", $dados[3]);
                    $refNFP->appendChild($IE);
                    $mod = $dom->createElement("mod", $dados[4]);
                    $refNFP->appendChild($mod);
                    $serie = $dom->createElement("serie", $dados[5]);
                    $refNFP->appendChild($serie);
                    $nNF = $dom->createElement("nNF", $dados[6]);
                    $refNFP->appendChild($nNF);
                    $NFref->appendChild($refNFP);
                    break;

                case "B20d": //CNPJ [refNFP]
                    //B20d|CNPJ
                    if (!isset($refNFP)) {
                        $CNPJ = $dom->createElement("CNPJ", $dados[1]);
                        $refNFP->appendChild($CNPJ);
                    }
                    break;

                case "B20e": //CPF [refNFP]
                    //B20e|CPF
                    if (!isset($refNFP)) {
                        $CPF = $dom->createElement("CPF", $dados[1]);
                        $refNFP->appendChild($CPF);
                    }
                    break;

                case "B20i": // CTE [NFref]
                    if (!isset($NFref)) {
                        $NFref = $dom->createElement("NFref");
                        $ide->insertBefore($ide->appendChild($NFref), $tpImp);
                    }
                    //B20i|refCTe|
                    $refCTe = $dom->createElement("refCTe", $dados[1]);
                    $NFref->appendChild($refCTe);
                    break;

                case "B20j": // ECF [NFref]
                    if (!isset($NFref)) {
                        $NFref = $dom->createElement("NFref");
                        $ide->insertBefore($ide->appendChild($NFref), $tpImp);
                    }
                    //B20j|mod|nECF|nCOO|
                    $refECF = $dom->createElement("refECF");
                    $mod = $dom->createElement("mod", $dados[1]);
                    $refECF->appendChild($mod);
                    $nECF = $dom->createElement("nECF", $dados[2]);
                    $refECF->appendChild($nECF);
                    $nCOO = $dom->createElement("nCOO", $dados[3]);
                    $refECF->appendChild($nCOO);
                    $NFref->appendChild($refECF);
                    break;

                case "C": //dados do emitente [infNFe]
                    //C|XNome|XFant|IE|IEST|IM|CNAE|CRT|
                    $emit = $dom->createElement("emit");
                    $xNome = $dom->createElement("xNome", $dados[1]);
                    $emit->appendChild($xNome);
                    if (!empty($dados[2])) {
                        $xFant = $dom->createElement("xFant", $dados[2]);
                        $emit->appendChild($xFant);
                    }
                    $IE = $dom->createElement("IE", $dados[3]);
                    $emit->appendChild($IE);
                    if (!empty($dados[4])) {
                        $IEST = $dom->createElement("IEST", $dados[4]);
                        $emit->appendChild($IEST);
                    }
                    if (!empty($dados[5])) {
                        $IM = $dom->createElement("IM", $dados[5]);
                        $emit->appendChild($IM);
                    }
                    if (!empty($dados[6])) {
                        $cnae = $dom->createElement("CNAE", $dados[6]);
                        $emit->appendChild($cnae);
                    }
                    if (!empty($dados[7])) {
                        $CRT = $dom->createElement("CRT", $dados[7]);
                        $emit->appendChild($CRT);
                    }
                    $infNFe->appendChild($emit);
                    break;

                case "C02": //CNPJ [emit]
                    $cnpj = $dom->createElement("CNPJ", $dados[1]);
                    $emit->insertBefore($emit->appendChild($cnpj), $xNome);
                    break;

                case "C02a": //CPF [emit]
                    $cpf = $dom->createElement("CPF", $dados[1]);
                    $emit->insertBefore($emit->appendChild($cpf), $xNome);
                    break;

                case "C05"://Grupo do Endereço do emitente [emit]
                    //C05|XLgr|Nro|Cpl|Bairro|CMun|XMun|UF|CEP|cPais|xPais|fone|
                    $enderEmi = $dom->createElement("enderEmit");
                    $xLgr = $dom->createElement("xLgr", $dados[1]);
                    $enderEmi->appendChild($xLgr);
                    $dados[2] = abs((int) $dados[2]);
                    $nro = $dom->createElement("nro", $dados[2]);
                    $enderEmi->appendChild($nro);
                    if (!empty($dados[3])) {
                        $xCpl = $dom->createElement("xCpl", $dados[3]);
                        $enderEmi->appendChild($xCpl);
                    }
                    $xBairro = $dom->createElement("xBairro", $dados[4]);
                    $enderEmi->appendChild($xBairro);
                    $cMun = $dom->createElement("cMun", $dados[5]);
                    $enderEmi->appendChild($cMun);
                    $xMun = $dom->createElement("xMun", $dados[6]);
                    $enderEmi->appendChild($xMun);
                    $UF = $dom->createElement("UF", $dados[7]);
                    $enderEmi->appendChild($UF);
                    if (!empty($dados[8])) {
                        $CEP = $dom->createElement("CEP", $dados[8]);
                        $enderEmi->appendChild($CEP);
                    }
                    if (!empty($dados[9])) {
                        $cPais = $dom->createElement("cPais", $dados[9]);
                        $enderEmi->appendChild($cPais);
                    }
                    if (!empty($dados[10])) {
                        $xPais = $dom->createElement("xPais", $dados[10]);
                        $enderEmi->appendChild($xPais);
                    }
                    if (!empty($dados[11])) {
                        $fone = $dom->createElement("fone", $dados[11]);
                        $enderEmi->appendChild($fone);
                    }
                    $emit->insertBefore($emit->appendChild($enderEmi), $IE);
                    break;

                case "E": //Grupo de identificação do Destinatário da NF-e [infNFe]
                    //E|xNome|IE|ISUF|email|
                    $dest = $dom->createElement("dest");
                    //se ambiente homologação preencher conforme NT2011.002
                    //válida a partir de 01/05/2011
                    if ($tipoAmbiente == '2') {
                        $xNome = $dom->createElement("xNome", 'NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL');
                        $dest->appendChild($xNome);
                        $IE = $dom->createElement("indIEDest", '2');
                        $dest->appendChild($IE);
                    } else {
                        $xNome = $dom->createElement("xNome", $dados[1]);
                        $dest->appendChild($xNome);
                        $IE = $dom->createElement("indIEDest", "2");
                        $dest->appendChild($IE);
                    }
                    if (!empty($dados[3])) {
                        $ISUF = $dom->createElement("ISUF", $dados[3]);
                        $dest->appendChild($ISUF);
                    }
                    if (!empty($dados[4])) {
                        $email = $dom->createElement("email", $dados[4]);
                        $dest->appendChild($email);
                    }
                    $infNFe->appendChild($dest);
                    break;

                case "E02": //CNPJ [dest]
                    //se ambiente homologação preencher conforme NT2011.002,
                    //válida a partir de 01/05/2011
                    if ($tipoAmbiente == '2') {
                        if ($dados[1] != '') {
                            //operação nacional em ambiente homologação usar 99999999000191
                            $CNPJ = $dom->createElement("CNPJ", '99999999000191');
                        } else {
                            //operação com o exterior CNPJ vazio
                            $CNPJ = $dom->createElement("CNPJ", '');
                        }
                    } else {
                        $CNPJ = $dom->createElement("CNPJ", $dados[1]);
                    }//fim teste ambiente
                    $dest->insertBefore($dest->appendChild($CNPJ), $xNome);
                    break;

                case "E03": //CPF [dest]
                    //se ambiente homologação preencher conforme NT2011.002,
                    //válida a partir de 01/05/2011
                    if ($tipoAmbiente == '2') {
                        if ($dados[1] != '') {
                            //operação nacional em ambiente homologação usar 99999999000191
                            $CNPJ = $dom->createElement("CNPJ", '99999999000191');
                        } else {
                            //operação com o exterior CNPJ vazio
                            $CNPJ = $dom->createElement("CNPJ", '');
                        }
                        $dest->insertBefore($dest->appendChild($CNPJ), $xNome);
                    } else {
                        $CPF = $dom->createElement("CPF", $dados[1]);
                        $dest->insertBefore($dest->appendChild($CPF), $xNome);
                    } //fim teste ambiente
                    break;

                case "E05": //Grupo de endereço do Destinatário da NF-e [dest]
                    //E05|xLgr|nro|xCpl|xBairro|cMun|xMun|UF|CEP|cPais|xPais|fone|
                    $enderDest = $dom->createElement("enderDest");
                    $xLgr = $dom->createElement("xLgr", $dados[1]);
                    $enderDest->appendChild($xLgr);
                    $dados[2] = abs((int) $dados[2]);
                    $nro = $dom->createElement("nro", $dados[2]);
                    $enderDest->appendChild($nro);
                    if (!empty($dados[3])) {
                        $xCpl = $dom->createElement("xCpl", $dados[3]);
                        $enderDest->appendChild($xCpl);
                    }
                    $xBairro = $dom->createElement("xBairro", $dados[4]);
                    $enderDest->appendChild($xBairro);
                    $cMun = $dom->createElement("cMun", $dados[5]);
                    $enderDest->appendChild($cMun);
                    $xMun = $dom->createElement("xMun", $dados[6]);
                    $enderDest->appendChild($xMun);
                    $UF = $dom->createElement("UF", $dados[7]);
                    $enderDest->appendChild($UF);
                    if (!empty($dados[8])) {
                        $CEP = $dom->createElement("CEP", $dados[8]);
                        $enderDest->appendChild($CEP);
                    }
                    if (!empty($dados[9])) {
                        $cPais = $dom->createElement("cPais", $dados[9]);
                        $enderDest->appendChild($cPais);
                    }
                    if (!empty($dados[10])) {
                        $xPais = $dom->createElement("xPais", $dados[10]);
                        $enderDest->appendChild($xPais);
                    }
                    if (!empty($dados[11])) {
                        $fone = $dom->createElement("fone", $dados[11]);
                        $enderDest->appendChild($fone);
                    }
                    $dest->insertBefore($dest->appendChild($enderDest), $IE);
                    break;

                case "F": //Grupo de identificação do Local de retirada [infNFe]
                    //F|xLgr|nro|xCpl|xBairro|cMun|xMun|UF|
                    $retirada = $dom->createElement("retirada");
                    if (!empty($dados[1])) {
                        $xLgr = $dom->createElement("xLgr", $dados[1]);
                        $retirada->appendChild($xLgr);
                    }
                    if (!empty($dados[2])) {
                        $dados[2] = abs((int) $dados[2]);
                        $nro = $dom->createElement("nro", $dados[2]);
                        $retirada->appendChild($nro);
                    }
                    if (!empty($dados[3])) {
                        $xCpl = $dom->createElement("xCpl", $dados[3]);
                        $retirada->appendChild($xCpl);
                    }
                    if (!empty($dados[4])) {
                        $xBairro = $dom->createElement("xBairro", $dados[4]);
                        $retirada->appendChild($xBairro);
                    }
                    if (!empty($dados[5])) {
                        $cMun = $dom->createElement("cMun", $dados[5]);
                        $retirada->appendChild($cMun);
                    }
                    if (!empty($dados[6])) {
                        $xMun = $dom->createElement("xMun", $dados[6]);
                        $retirada->appendChild($xMun);
                    }
                    if (!empty($dados[7])) {
                        $UF = $dom->createElement("UF", $dados[7]);
                        $retirada->appendChild($UF);
                    }
                    $infNFe->appendChild($retirada);
                    break;

                case "F02": //CNPJ [retirada]
                    if (!empty($dados[1])) {
                        $CNPJ = $dom->createElement("CNPJ", $dados[1]);
                        $retirada->insertBefore($retirada->appendChild($CNPJ), $xLgr);
                    }
                    break;

                case "F02a": //CPF [retirada]
                    if (!empty($dados[1])) {
                        $CPF = $dom->createElement("CPF", $dados[1]);
                        $retirada->insertBefore($retirada->appendChild($CPF), $xLgr);
                    }
                    break;

                case "G": // Grupo de identificação do Local de entrega [entrega]
                    //G|xLgr|nro|xCpl|xBairro|cMun|xMun|UF|
                    $entrega = $dom->createElement("entrega");
                    if (!empty($dados[1])) {
                        $xLgr = $dom->createElement("xLgr", $dados[1]);
                        $entrega->appendChild($xLgr);
                    }
                    if (!empty($dados[2])) {
                        $dados[2] = abs((int) $dados[2]);
                        $nro = $dom->createElement("nro", $dados[2]);
                        $entrega->appendChild($nro);
                    }
                    if (!empty($dados[3])) {
                        $xCpl = $dom->createElement("xCpl", $dados[3]);
                        $entrega->appendChild($xCpl);
                    }
                    if (!empty($dados[4])) {
                        $xBairro = $dom->createElement("xBairro", $dados[4]);
                        $entrega->appendChild($xBairro);
                    }
                    if (!empty($dados[5])) {
                        $cMun = $dom->createElement("cMun", $dados[5]);
                        $entrega->appendChild($cMun);
                    }
                    if (!empty($dados[6])) {
                        $xMun = $dom->createElement("xMun", $dados[6]);
                        $entrega->appendChild($xMun);
                    }
                    if (!empty($dados[7])) {
                        $UF = $dom->createElement("UF", $dados[7]);
                        $entrega->appendChild($UF);
                    }
                    $infNFe->appendChild($entrega);
                    break;

                case "G02": // CNPJ [entrega]
                    if (!empty($dados[1])) {
                        $CNPJ = $dom->createElement("CNPJ", $dados[1]);
                        $entrega->insertBefore($entrega->appendChild($CNPJ), $xLgr);
                    }
                    break;

                case "G02a": // CPF [entrega]
                    if (!empty($dados[1])) {
                        $CPF = $dom->createElement("CPF", $dados[1]);
                        $entrega->insertBefore($entrega->appendChild($CPF), $xLgr);
                    }
                    break;

                case "H": // Grupo do detalhamento de Produtos e Serviços da NF-e [infNFe]
                    $det = $dom->createElement("det");
                    $det->setAttribute("nItem", $dados[1]);
                    if (!empty($dados[2])) {
                        $infAdProd = $dom->createElement("infAdProd", $dados[2]);
                        $det->appendChild($infAdProd);
                    }
                    $infNFe->appendChild($det);
                    break;

                case "I": //PRODUTO SERVICO [det]
                    //I|CProd|CEAN|XProd|NCM|EXTIPI|CFOP|UCom|QCom|VUnCom|VProd|CEANTrib|UTrib|QTrib|VUnTrib|VFrete|VSeg|VDesc|vOutro|indTot|xPed|nItemPed|
                    $prod = $dom->createElement("prod");
                    $cProd = $dom->createElement("cProd", $dados[1]);
                    $prod->appendChild($cProd);
                    $cEAN = $dom->createElement("cEAN", $dados[2]);
                    $prod->appendChild($cEAN);
                    $xProd = $dom->createElement("xProd", $dados[3]);
                    $prod->appendChild($xProd);
                    $NCM = $dom->createElement("NCM", $dados[4]);
                    $prod->appendChild($NCM);
                    if (!empty($dados[5])) {
                        $EXTIPI = $dom->createElement("EXTIPI", $dados[5]);
                        $prod->appendChild($EXTIPI);
                    }
                    $CFOP = $dom->createElement("CFOP", $dados[6]);
                    $prod->appendChild($CFOP);
                    $uCom = $dom->createElement("uCom", $dados[7]);
                    $prod->appendChild($uCom);
                    $qCom = $dom->createElement("qCom", $dados[8]);
                    $prod->appendChild($qCom);
                    $vUnCom = $dom->createElement("vUnCom", $dados[9]);
                    $prod->appendChild($vUnCom);
                    $vProd = $dom->createElement("vProd", $dados[10]);
                    $prod->appendChild($vProd);
                    $cEANTrib = $dom->createElement("cEANTrib", $dados[11]);
                    $prod->appendChild($cEANTrib);
                    if (!empty($dados[12])) {
                        $uTrib = $dom->createElement("uTrib", $dados[12]);
                    } else {
                        $uTrib = $dom->createElement("uTrib", $dados[7]);
                    }
                    $prod->appendChild($uTrib);
                    if (!empty($dados[13])) {
                        $qTrib = $dom->createElement("qTrib", $dados[13]);
                    } else {
                        $qTrib = $dom->createElement("qTrib", $dados[8]);
                    }
                    $prod->appendChild($qTrib);
                    if (!empty($dados[14])) {
                        $vUnTrib = $dom->createElement("vUnTrib", $dados[14]);
                    } else {
                        $vUnTrib = $dom->createElement("vUnTrib", $dados[9]);
                    }
                    $prod->appendChild($vUnTrib);
                    if (!empty($dados[15])) {
                        $vFrete = $dom->createElement("vFrete", $dados[15]);
                        $prod->appendChild($vFrete);
                    }
                    if (!empty($dados[16])) {
                        $vSeg = $dom->createElement("vSeg", $dados[16]);
                        $prod->appendChild($vSeg);
                    }
                    if (!empty($dados[17])) {
                        $vDesc = $dom->createElement("vDesc", $dados[17]);
                        $prod->appendChild($vDesc);
                    }
                    if (!empty($dados[18])) {
                        $vOutro = $dom->createElement("vOutro", $dados[18]);
                        $prod->appendChild($vOutro);
                    }
                    if (!empty($dados[19]) || $dados[19] == 0) {
                        $indTot = $dom->createElement("indTot", $dados[19]);
                        $prod->appendChild($indTot);
                    } else {
                        $indTot = $dom->createElement("indTot", '0');
                        $prod->appendChild($indTot);
                    }
                    if (sizeof($dados) > 19) {
                        if (!empty($dados[20])) {
                            $xPed = $dom->createElement("xPed", $dados[20]);
                            $prod->appendChild($xPed);
                        }
                        if (!empty($dados[21])) {
                            $nItemPed = $dom->createElement("nItemPed", $dados[21]);
                            $prod->appendChild($nItemPed);
                        }
                    }
                    if (!isset($infAdProd)) {
                        $det->appendChild($prod);
                    } else {
                        $det->insertBefore($det->appendChild($prod), $infAdProd);
                    }
                    break;

                case "I18": //Tag da Declaração de Importação [prod]
                    //I18|NDI|DDI|XLocDesemb|UFDesemb|DDesemb|CExportador|
                    $DI = $dom->createElement("DI");
                    if (!empty($dados[1])) {
                        $nDI = $dom->createElement("nDI", $dados[1]);
                        $DI->appendChild($nDI);
                    }
                    if (!empty($dados[2])) {
                        $dDI = $dom->createElement("dDI", $dados[2]);
                        $DI->appendChild($dDI);
                    }
                    if (!empty($dados[3])) {
                        $xLocDesemb = $dom->createElement("xLocDesemb", $dados[3]);
                        $DI->appendChild($xLocDesemb);
                    }
                    if (!empty($dados[4])) {
                        $UFDesemb = $dom->createElement("UFDesemb", $dados[4]);
                        $DI->appendChild($UFDesemb);
                    }
                    if (!empty($dados[5])) {
                        $dDesemb = $dom->createElement("dDesemb", $dados[5]);
                        $DI->appendChild($dDesemb);
                    }
                    if (!empty($dados[6])) {
                        $cExportador = $dom->createElement("cExportador", $dados[6]);
                        $DI->appendChild($cExportador);
                    }
                    if (!isset($xPed)) {
                        $prod->appendChild($DI);
                    } else {
                        $prod->insertBefore($prod->appendChild($DI), $xPed);
                    }
                    break;

                case "I25": //Adições [DI]
                    //I25|NAdicao|NSeqAdic|CFabricante|VDescDI|
                    $adi = $dom->createElement("adi");
                    if (!empty($dados[1])) {
                        $nAdicao = $dom->createElement("nAdicao", $dados[1]);
                        $adi->appendChild($nAdicao);
                    }
                    if (!empty($dados[2])) {
                        $nSeqAdic = $dom->createElement("nSeqAdic", $dados[2]);
                        $adi->appendChild($nSeqAdic);
                    }
                    if (!empty($dados[3])) {
                        $cFabricante = $dom->createElement("cFabricante", $dados[3]);
                        $adi->appendChild($cFabricante);
                    }
                    if (!empty($dados[4])) {
                        $vDescDI = $dom->createElement("vDescDI", $dados[4]);
                        $adi->appendChild($vDescDI);
                    }
                    $DI->appendChild($adi);
                    break;

                case "J": //Grupo do detalhamento de veículos novos [prod]
                    //J|TpOp|Chassi|CCor|XCor|Pot|cilin|pesoL|pesoB|NSerie|TpComb|NMotor|CMT|Dist|anoMod|anoFab|tpPint|tpVeic|espVeic|VIN|condVeic|cMod|cCorDENATRAN|lota|tpRest|
                    $veicProd = $dom->createElement("veicProd");
                    if (!empty($dados[1])) {
                        $tpOP = $dom->createElement("tpOp", $dados[1]);
                        $veicProd->appendChild($tpOP);
                    }
                    if (!empty($dados[2])) {
                        $chassi = $dom->createElement("chassi", $dados[2]);
                        $veicProd->appendChild($chassi);
                    }
                    if (!empty($dados[3])) {
                        $cCor = $dom->createElement("cCor", $dados[3]);
                        $veicProd->appendChild($cCor);
                    }
                    if (!empty($dados[4])) {
                        $xCor = $dom->createElement("xCor", $dados[4]);
                        $veicProd->appendChild($dVal);
                    }
                    if (!empty($dados[5])) {
                        $pot = $dom->createElement("pot", $dados[5]);
                        $veicProd->appendChild($pot);
                    }
                    if (!empty($dados[6])) {
                        $cilin = $dom->createElement("cilin", $dados[6]);
                        $veicProd->appendChild($cilin);
                    }
                    if (!empty($dados[7])) {
                        $pesoL = $dom->createElement("pesL", $dados[7]);
                        $veicProd->appendChild($pesoL);
                    }
                    if (!empty($dados[8])) {
                        $pesoB = $dom->createElement("pesoB", $dados[8]);
                        $veicProd->appendChild($pesoB);
                    }
                    if (!empty($dados[9])) {
                        $nSerie = $dom->createElement("nSerie", $dados[9]);
                        $veicProd->appendChild($nSerie);
                    }
                    if (!empty($dados[10])) {
                        $tpComb = $dom->createElement("tpComb", $dados[10]);
                        $veicProd->appendChild($tpComb);
                    }
                    if (!empty($dados[11])) {
                        $nMotor = $dom->createElement("nMotor", $dados[11]);
                        $veicProd->appendChild($nMotor);
                    }
                    if (!empty($dados[12])) {
                        $CMT = $dom->createElement("CMT", $dados[12]);
                        $veicProd->appendChild($CMKG);
                    }
                    if (!empty($dados[13])) {
                        $dist = $dom->createElement("dist", $dados[13]);
                        $veicProd->appendChild($dist);
                    }
                    if (!empty($dados[14])) {
                        $anoMod = $dom->createElement("anoMod", $dados[14]);
                        $veicProd->appendChild($anoMod);
                    }
                    if (!empty($dados[15])) {
                        $anoFab = $dom->createElement("anoFab", $dados[15]);
                        $veicProd->appendChild($anoFab);
                    }
                    if (!empty($dados[16])) {
                        $tpPint = $dom->createElement("tpPint", $dados[16]);
                        $veicProd->appendChild($tpPint);
                    }
                    if (!empty($dados[17])) {
                        $tpVeic = $dom->createElement("tpVeic", $dados[17]);
                        $veicProd->appendChild($tpVeic);
                    }
                    if (!empty($dados[18])) {
                        $espVeic = $dom->createElement("espVeic", $dados[18]);
                        $veicProd->appendChild($espVeic);
                    }
                    if (!empty($dados[19])) {
                        $VIN = $dom->createElement("VIN", $dados[19]);
                        $veicProd->appendChild($VIN);
                    }
                    if (!empty($dados[20])) {
                        $condVeic = $dom->createElement("condVeic", $dados[20]);
                        $veicProd->appendChild($condVeic);
                    }
                    if (!empty($dados[21])) {
                        $cMod = $dom->createElement("cMod", $dados[21]);
                        $veicProd->appendChild($cMod);
                    }
                    if (!empty($dados[22])) {
                        $cCorDENATRAN = $dom->createElement("cCorDENATRAN", $dados[22]);
                        $veicProd->appendChild($cCorDENATRAN);
                    }
                    if (!empty($dados[23])) {
                        $lota = $dom->createElement("lota", $dados[23]);
                        $veicProd->appendChild($lota);
                    }
                    if (!empty($dados[24])) {
                        $tpRest = $dom->createElement("tpRest", $dados[24]);
                        $veicProd->appendChild($tpRest);
                    }
                    $prod->appendChild($veicProd);
                    break;

                case "K": //Grupo do detalhamento de Medicamentos e de matériasprimas farmacêuticas [prod]
                    //K|NLote|QLote|DFab|DVal|VPMC|
                    $med = $dom->createElement("med");
                    if (!empty($dados[1])) {
                        $nLote = $dom->createElement("nLote", $dados[1]);
                        $med->appendChild($nLote);
                    }
                    if (!empty($dados[2])) {
                        $qLote = $dom->createElement("qLote", $dados[2]);
                        $med->appendChild($qLote);
                    }
                    if (!empty($dados[3])) {
                        $dFab = $dom->createElement("dFab", $dados[3]);
                        $med->appendChild($dFab);
                    }
                    $dVal = $dom->createElement("dVal", $dados[4]);
                    $med->appendChild($dVal);
                    if (!empty($dados[5])) {
                        $vPMC = $dom->createElement("vPMC", $dados[5]);
                        $med->appendChild($vPMC);
                    }
                    $prod->appendChild($med);
                    break;

                case "L": //Grupo do detalhamento de Armamento [prod]
                    //L|TpArma|NSerie|NCano|Descr|
                    $arma = $dom->createElement("arma");
                    if (!empty($dados[1])) {
                        $tpArma = $dom->createElement("tpArma", $dados[1]);
                        $arma->appendChild($tpArma);
                    }
                    if (!empty($dados[2])) {
                        $nSerie = $dom->createElement("nSerie", $dados[2]);
                        $arma->appendChild($nSerie);
                    }
                    if (!empty($dados[3])) {
                        $nCano = $dom->createElement("nCano", $dados[3]);
                        $arma->appendChild($nCano);
                    }
                    if (!empty($dados[4])) {
                        $descr = $dom->createElement("descr", $dados[4]);
                        $arma->appendChild($descr);
                    }
                    $prod->appendChild($arma);
                    break;

                case "L101": //Grupo de informações específicas para combustíveis líquidos e lubrificantes [prod]
                    $comb = $dom->createElement("comb");
                    $cProdANP = $dom->createElement("cProdANP", $dados[1]);
                    $comb->appendChild($cProdANP);
                    if (!empty($dados[2])) {
                        $CODIF = $dom->createElement("CODIF", $dados[2]);
                        $comb->appendChild($CODIF);
                    }
                    if (!empty($dados[3])) {
                        $qTemp = $dom->createElement("qTemp", $dados[3]);
                        $comb->appendChild($qTemp);
                    }
                    $UFCons = $dom->createElement("UFCons", $dados[4]);
                    $comb->appendChild($UFCons);
                    $prod->appendChild($comb);
                    break;

                case "L105": //Grupo da CIDE [comb]
                    $CIDE = $dom->createElement("CIDE");
                    $qBCprod = $dom->createElement("qBCprod", $dados[1]);
                    $CIDE->appendChild($qBCprod);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[2]);
                    $CIDE->appendChild($vAliqProd);
                    $vCIDE = $dom->createElement("vCIDE", $dados[3]);
                    $CIDE->appendChild($vCIDE);
                    $comb->appendChild($CIDE);
                    break;

                case "M"://GRUPO DE TRIBUTOS INCIDENTES NO PRODUTO SERVICO
                    $imposto = $dom->createElement("imposto");
                    if (!isset($infAdProd)) {
                        $det->appendChild($imposto);
                    } else {
                        $det->insertBefore($det->appendChild($imposto), $infAdProd);
                    }
                    $infAdProd = null;
                    break;

                case "N"://ICMS
                    $ICMS = $dom->createElement("ICMS");
                    $imposto->appendChild($ICMS);
                    break;

                case "N02"://CST 00 TRIBUTADO INTEGRALMENTE [ICMS]
                    $ICMS00 = $dom->createElement("ICMS00");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS00->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS00->appendChild($CST);
                    $modBC = $dom->createElement("modBC", $dados[3]);
                    $ICMS00->appendChild($modBC);
                    $vBC = $dom->createElement("vBC", $dados[4]);
                    $ICMS00->appendChild($vBC);
                    $pICMS = $dom->createElement("pICMS", $dados[5]);
                    $ICMS00->appendChild($pICMS);
                    $vICMS = $dom->createElement("vICMS", $dados[6]);
                    $ICMS00->appendChild($vICMS);
                    $ICMS->appendChild($ICMS00);
                    break;

                case "N03"://CST 010 TRIBUTADO E COM COBRANCAO DE ICMS POR SUBSTUICAO TRIBUTARIA [ICMS]
                    $ICMS10 = $dom->createElement("ICMS10");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS10->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS10->appendChild($CST);
                    $modBC = $dom->createElement("modBC", $dados[3]);
                    $ICMS10->appendChild($modBC);
                    $vBC = $dom->createElement("vBC", $dados[4]);
                    $ICMS10->appendChild($vBC);
                    $pICMS = $dom->createElement("pICMS", $dados[5]);
                    $ICMS10->appendChild($pICMS);
                    $vICMS = $dom->createElement("vICMS", $dados[6]);
                    $ICMS10->appendChild($vICMS);
                    $modBCST = $dom->createElement("modBCST", $dados[7]);
                    $ICMS10->appendChild($modBCST);
                    if (!empty($dados[8])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[8]);
                        $ICMS10->appendChild($pMVAST);
                    }
                    if (!empty($dados[9])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[9]);
                        $ICMS10->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[10]);
                    $ICMS10->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[11]);
                    $ICMS10->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[12]);
                    $ICMS10->appendChild($vICMSST);
                    $ICMS->appendChild($ICMS10);
                    break;

                case "N04": //CST 020 COM REDUCAO DE BASE DE CALCULO [ICMS]
                    $ICMS20 = $dom->createElement("ICMS20");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS20->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS20->appendChild($CST);
                    $modBC = $dom->createElement("modBC", $dados[3]);
                    $ICMS20->appendChild($modBC);
                    $pRedBC = $dom->createElement("pRedBC", $dados[4]);
                    $ICMS20->appendChild($pRedBC);
                    $vBC = $dom->createElement("vBC", $dados[5]);
                    $ICMS20->appendChild($vBC);
                    $pICMS = $dom->createElement("pICMS", $dados[6]);
                    $ICMS20->appendChild($pICMS);
                    $vICMS = $dom->createElement("vICMS", $dados[7]);
                    $ICMS20->appendChild($vICMS);
                    $ICMS->appendChild($ICMS20);
                    break;

                case "N05": //CST 030 ISENTA OU NAO TRIBUTADO E COM COBRANCA DO ICMS POR ST [ICMS]
                    $ICMS30 = $dom->createElement("ICMS30");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS30->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS30->appendChild($CST);
                    $modBCST = $dom->createElement("modBCST", $dados[3]);
                    $ICMS30->appendChild($modBCST);
                    if (!empty($dados[4])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[4]);
                        $ICMS30->appendChild($pMVAST);
                    }
                    if (!empty($dados[5])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[5]);
                        $ICMS30->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[6]);
                    $ICMS30->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[7]);
                    $ICMS30->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[8]);
                    $ICMS30->appendChild($vICMSST);
                    $ICMS->appendChild($ICMS30);
                    break;

                case "N06": //Grupo de Tributação do ICMS 40, 41 ou 50 [ICMS]
                    //N06|Orig|CST|vICMS|motDesICMS|
                    $ICMS40 = $dom->createElement("ICMS40");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS40->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS40->appendChild($CST);
                    if (!empty($dados[3])) {
                        $vICMS = $dom->createElement("vICMSDeson", $dados[3]);
                        $ICMS40->appendChild($vICMS);
                    }
                    if (!empty($dados[4])) {
                        $motDesICMS = $dom->createElement("motDesICMS", $dados[4]);
                        $ICMS40->appendChild($motDesICMS);
                    }
                    $ICMS->appendChild($ICMS40);
                    break;

                case "N07": //Grupo de Tributação do ICMS = 51 [ICMS]
                    //N07|Orig|CST|ModBC|PRedBC|VBC|PICMS|VICMS|
                    $ICMS51 = $dom->createElement("ICMS51");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS51->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS51->appendChild($CST);
                    if (!empty($dados[3])) {
                        $modBC = $dom->createElement("modBC", $dados[3]);
                        $ICMS51->appendChild($modBC);
                    }
                    if (!empty($dados[4])) {
                        $pRedBC = $dom->createElement("pRedBC", $dados[4]);
                        $ICMS51->appendChild($pRedBC);
                    }
                    if (!empty($dados[5])) {
                        $vBC = $dom->createElement("vBC", $dados[5]);
                        $ICMS51->appendChild($vBC);
                    }
                    if (!empty($dados[6])) {
                        $pICMS = $dom->createElement("pICMS", $dados[6]);
                        $ICMS51->appendChild($pICMS);
                    }
                    if (!empty($dados[7])) {
                        $vICMS = $dom->createElement("vICMS", $dados[7]);
                        $ICMS51->appendChild($vICMS);
                    }
                    $ICMS->appendChild($ICMS51);
                    break;

                case "N08": //Grupo de Tributação do ICMS = 60 [ICMS]
                    $ICMS60 = $dom->createElement("ICMS60");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS60->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS60->appendChild($CST);
                    $vBCST = $dom->createElement("vBCSTRet", $dados[3]);
                    $ICMS60->appendChild($vBCST);
                    $vICMSST = $dom->createElement("vICMSSTRet", $dados[4]);
                    $ICMS60->appendChild($vICMSST);
                    $ICMS->appendChild($ICMS60);
                    break;

                case "N09": //Grupo de Tributação do ICMS 70 [ICMS]
                    $ICMS70 = $dom->createElement("ICMS70");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS70->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS70->appendChild($CST);
                    $modBC = $dom->createElement("modBC", $dados[3]);
                    $ICMS70->appendChild($modBC);
                    $pRedBC = $dom->createElement("pRedBC", $dados[4]);
                    $ICMS70->appendChild($pRedBC);
                    $vBC = $dom->createElement("vBC", $dados[5]);
                    $ICMS70->appendChild($vBC);
                    $pICMS = $dom->createElement("pICMS", $dados[6]);
                    $ICMS70->appendChild($pICMS);
                    $vICMS = $dom->createElement("vICMS", $dados[7]);
                    $ICMS70->appendChild($vICMS);
                    $modBCST = $dom->createElement("modBCST", $dados[8]);
                    $ICMS70->appendChild($modBCST);
                    if (!empty($dados[9])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[9]);
                        $ICMS70->appendChild($pMVAST);
                    }
                    if (!empty($dados[10])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[10]);
                        $ICMS70->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[11]);
                    $ICMS70->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[12]);
                    $ICMS70->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[13]);
                    $ICMS70->appendChild($vICMSST);
                    $ICMS->appendChild($ICMS70);
                    break;

                case "N10": //Grupo de Tributação do ICMS 90 Outros [ICMS]
                    $ICMS90 = $dom->createElement("ICMS90");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMS90->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMS90->appendChild($CST);
                    $modBC = $dom->createElement("modBC", $dados[3]);
                    $ICMS90->appendChild($modBC);
                    if (!empty($dados[4])) {
                        $pRedBC = $dom->createElement("pRedBC", $dados[4]);
                        $ICMS90->appendChild($pRedBC);
                    }
                    $vBC = $dom->createElement("vBC", $dados[5]);
                    $ICMS90->appendChild($vBC);
                    $pICMS = $dom->createElement("pICMS", $dados[6]);
                    $ICMS90->appendChild($pICMS);
                    $vICMS = $dom->createElement("vICMS", $dados[7]);
                    $ICMS90->appendChild($vICMS);
                    $modBCST = $dom->createElement("modBCST", $dados[8]);
                    $ICMS90->appendChild($modBCST);
                    if (!empty($dados[9])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[9]);
                        $ICMS90->appendChild($pMVAST);
                    }
                    if (!empty($dados[10])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[10]);
                        $ICMS90->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[11]);
                    $ICMS90->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[12]);
                    $ICMS90->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[13]);
                    $ICMS90->appendChild($vICMSST);
                    $ICMS->appendChild($ICMS90);
                    break;

                case "N10a": //Partilha do ICMS entre a UF de origem e UF de destino ou a UF definida na legislação [ICMS]
                    //N10a|Orig|CST|ModBC|PRedBC|VBC|PICMS|VICMS|ModBCST|PMVAST|PRedBCST|VBCST|PICMSST|VICMSST|pBCOp|UFST|
                    $ICMSPart = $dom->createElement("ICMSPart");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSPart->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMSPart->appendChild($CST);
                    $modBC = $dom->createElement("modBC", $dados[3]);
                    $ICMSPart->appendChild($modBC);
                    if (!empty($dados[4])) {
                        $pRedBC = $dom->createElement("pRedBC", $dados[4]);
                        $ICMSPart->appendChild($pRedBC);
                    }
                    $vBC = $dom->createElement("vBC", $dados[5]);
                    $ICMSPart->appendChild($vBC);
                    $pICMS = $dom->createElement("pICMS", $dados[6]);
                    $ICMSPart->appendChild($pICMS);
                    $vICMS = $dom->createElement("vICMS", $dados[7]);
                    $ICMSPart->appendChild($vICMS);
                    $modBCST = $dom->createElement("modBCST", $dados[8]);
                    $ICMSPart->appendChild($modBCST);
                    if (!empty($dados[9])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[9]);
                        $ICMSPart->appendChild($pMVAST);
                    }
                    if (!empty($dados[10])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[10]);
                        $ICMSPart->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[11]);
                    $ICMSPart->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[12]);
                    $ICMSPart->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[13]);
                    $ICMSPart->appendChild($vICMSST);
                    $pBCOp = $dom->createElement("pBCOp", $dados[14]);
                    $ICMSPart->appendChild($pBCOp);
                    $UFST = $dom->createElement("UFST", $dados[15]);
                    $ICMSPart->appendChild($UFST);
                    $ICMS->appendChild($ICMSPart);
                    break;

                case "N10b": //ICMS ST – repasse de ICMS ST retido anteriormente em operações interestaduais com repasses através do Substituto Tributário [ICMS]
                    //N10b|Orig|CST|vBCSTRet|vICMSSTRet|vBCSTDest|vICMSSTDest|
                    $ICMSST = $dom->createElement("ICMSST");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSST->appendChild($orig);
                    $CST = $dom->createElement("CST", $dados[2]);
                    $ICMSST->appendChild($CST);
                    $vBCSTRet = $dom->createElement("vBCSTRet", $dados[3]);
                    $ICMSST->appendChild($vBCSTRet);
                    $vICMSSTRet = $dom->createElement("vICMSSTRet", $dados[4]);
                    $ICMSST->ppendChild($vICMSSTRet);
                    $vBCSTDest = $dom->createElement("vBCSTDest", $dados[5]);
                    $ICMSST->appendChild($vBCSTDest);
                    $vICMSSTDest = $dom->createElement("vICMSSTDest", $dados[6]);
                    $ICMSST->appendChild($vICMSSTDest);
                    $ICMS->appendChild($ICMSST);
                    break;

                case "N10c": //Grupo CRT=1 – Simples Nacional e CSOSN=101 [ICMS]
                    //N10c|Orig|CSOSN|pCredSN|vCredICMSSN|
                    $ICMSSN101 = $dom->createElement("ICMSSN101");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSSN101->appendChild($orig);
                    $CSOSN = $dom->createElement("CSOSN", $dados[2]);
                    $ICMSSN101->appendChild($CSOSN);
                    $pCredSN = $dom->createElement("pCredSN", $dados[3]);
                    $ICMSSN101->appendChild($pCredSN);
                    $vCredICMSSN = $dom->createElement("vCredICMSSN", $dados[4]);
                    $ICMSSN101->appendChild($vCredICMSSN);
                    $ICMS->appendChild($ICMSSN101);
                    break;

                case "N10d": //Grupo CRT=1 – Simples Nacional e CSOSN=102, 103,300 ou 400 [ICMS]
                    //N10d|Orig|CSOSN|
                    $ICMSSN102 = $dom->createElement("ICMSSN102");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSSN102->appendChild($orig);
                    $CSOSN = $dom->createElement("CSOSN", $dados[2]);
                    $ICMSSN102->appendChild($CSOSN);
                    $ICMS->appendChild($ICMSSN102);
                    break;

                case "N10e": //Grupo CRT=1 – Simples Nacional e CSOSN=201 [ICMS]
                    //N10e|Orig|CSOSN|modBCST|pMVAST|pRedBCST|vBCST|pICMSST|vICMSST|pCredSN|vCredICMSSN|
                    $ICMSSN201 = $dom->createElement("ICMSSN201");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSSN201->appendChild($orig);
                    $CSOSN = $dom->createElement("CSOSN", $dados[2]);
                    $ICMSSN201->appendChild($CSOSN);
                    $modBCST = $dom->createElement("modBCST", $dados[3]);
                    $ICMSSN201->appendChild($modBCST);
                    if (!empty($dados[4])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[4]);
                        $ICMSSN201->appendChild($pMVAST);
                    }
                    if (!empty($dados[5])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[5]);
                        $ICMSSN201->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[6]);
                    $ICMSSN201->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[7]);
                    $ICMSSN201->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[8]);
                    $ICMSSN201->appendChild($vICMSST);
                    $pCredSN = $dom->createElement("pCredSN", $dados[9]);
                    $ICMSSN201->appendChild($pCredSN);
                    $vCredICMSSN = $dom->createElement("vCredICMSSN", $dados[10]);
                    $ICMSSN201->appendChild($vCredICMSSN);
                    $ICMS->appendChild($ICMSSN201);
                    break;

                case "N10f": //Grupo CRT=1 – Simples Nacional e CSOSN=202 ou 203 [ICMS]
                    //N10f|Orig|CSOSN|modBCST|pMVAST|pRedBCST|vBCST|pICMSST|vICMSST|
                    $ICMSSN202 = $dom->createElement("ICMSSN202");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSSN202->appendChild($orig);
                    $CSOSN = $dom->createElement("CSOSN", $dados[2]);
                    $ICMSSN202->appendChild($CSOSN);
                    $modBCST = $dom->createElement("modBCST", $dados[3]);
                    $ICMSSN202->appendChild($modBCST);
                    if (!empty($dados[4])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[4]);
                        $ICMSSN202->appendChild($pMVAST);
                    }
                    if (!empty($dados[5])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[5]);
                        $ICMSSN202->appendChild($pRedBCST);
                    }
                    $vBCST = $dom->createElement("vBCST", $dados[6]);
                    $ICMSSN202->appendChild($vBCST);
                    $pICMSST = $dom->createElement("pICMSST", $dados[7]);
                    $ICMSSN202->appendChild($pICMSST);
                    $vICMSST = $dom->createElement("vICMSST", $dados[8]);
                    $ICMSSN202->appendChild($vICMSST);
                    $ICMS->appendChild($ICMSSN202);
                    break;

                case "N10g": //Grupo CRT=1 – Simples Nacional e CSOSN = 500 [ICMS]
                    //N10g|orig|CSOSN|vBCSTRet|vICMSSTRet|
                    // todos esses campos sao obrigatorios
                    $ICMSSN500 = $dom->createElement("ICMSSN500");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSSN500->appendChild($orig);
                    $CSOSN = $dom->createElement("CSOSN", $dados[2]);
                    $ICMSSN500->appendChild($CSOSN);
                    $vBCSTRet = $dom->createElement("vBCSTRet", $dados[3]);
                    $ICMSSN500->appendChild($vBCSTRet);
                    $vICMSSTRet = $dom->createElement("vICMSSTRet", $dados[4]);
                    $ICMSSN500->appendChild($vICMSSTRet);
                    $ICMS->appendChild($ICMSSN500);
                    break;

                case "N10h": //TAG de Grupo CRT=1 Simples Nacional e CSOSN=900 [ICMS]
                    //N10h|Orig|CSOSN|modBC|vBC|pRedBC|pICMS|vICMS|modBCST|pMVAST|pRedBCST|vBCST|pICMSST|vICMSST|pCredSN|vCredICMSSN|
                    $ICMSSN900 = $dom->createElement("ICMSSN900");
                    $orig = $dom->createElement("orig", $dados[1]);
                    $ICMSSN900->appendChild($orig);
                    $CSOSN = $dom->createElement("CSOSN", $dados[2]);
                    $ICMSSN900->appendChild($CSOSN);
                    if (!empty($dados[3])) {
                        $modBC = $dom->createElement("modBC", $dados[3]);
                        $ICMSSN900->appendChild($modBC);
                    }
                    if (!empty($dados[4])) {
                        $vBC = $dom->createElement("vBC", $dados[4]);
                        $ICMSSN900->appendChild($vBC);
                    }
                    if (!empty($dados[5])) {
                        $pRedBC = $dom->createElement("pRedBC", $dados[5]);
                        $ICMSSN900->appendChild($pRedBC);
                    }
                    if (!empty($dados[6])) {
                        $pICMS = $dom->createElement("pICMS", $dados[6]);
                        $ICMSSN900->appendChild($pICMS);
                    }
                    if (!empty($dados[7])) {
                        $vICMS = $dom->createElement("vICMS", $dados[7]);
                        $ICMSSN900->appendChild($vICMS);
                    }
                    if (!empty($dados[8])) {
                        $modBCST = $dom->createElement("modBCST", $dados[8]);
                        $ICMSSN900->appendChild($modBCST);
                    }
                    if (!empty($dados[9])) {
                        $pMVAST = $dom->createElement("pMVAST", $dados[9]);
                        $ICMSSN900->appendChild($pMVAST);
                    }
                    if (!empty($dados[10])) {
                        $pRedBCST = $dom->createElement("pRedBCST", $dados[10]);
                        $ICMSSN900->appendChild($pRedBCST);
                    }
                    if (!empty($dados[11])) {
                        $vBCST = $dom->createElement("vBCST", $dados[11]);
                        $ICMSSN900->appendChild($vBCST);
                    }
                    if (!empty($dados[12])) {
                        $pICMSST = $dom->createElement("pICMSST", $dados[12]);
                        $ICMSSN900->appendChild($pICMSST);
                    }
                    if (!empty($dados[13])) {
                        $vICMSST = $dom->createElement("vICMSST", $dados[13]);
                        $ICMSSN900->appendChild($vICMSST);
                    }
                    if (!empty($dados[14])) {
                        $pCredSN = $dom->createElement("pCredSN", $dados[14]);
                        $ICMSSN900->appendChild($pCredSN);
                    }
                    if (!empty($dados[15])) {
                        $vCredICMSSN = $dom->createElement("vCredICMSSN", $dados[15]);
                        $ICMSSN900->appendChild($vCredICMSSN);
                    }
                    $ICMS->appendChild($ICMSSN900);
                    break;

                case "O": //Grupo do IPI 0 ou 1 [imposto]
                    $IPI = $dom->createElement("IPI");
                    if (!empty($dados[1])) {
                        $clEnq = $dom->createElement("clEnq", $dados[1]);
                        $IPI->appendChild($clEnq);
                    }
                    if (!empty($dados[2])) {
                        $CNPJProd = $dom->createElement("CNPJProd", $dados[2]);
                        $IPI->appendChild($CNPJProd);
                    }
                    if (!empty($dados[3])) {
                        $cSelo = $dom->createElement("cSelo", $dados[3]);
                        $IPI->appendChild($cSelo);
                    }
                    if (!empty($dados[4])) {
                        $qSelo = $dom->createElement("qSelo", $dados[4]);
                        $IPI->appendChild($qSelo);
                    }
                    if (!empty($dados[5])) {
                        $cEnq = $dom->createElement("cEnq", $dados[5]);
                        $IPI->appendChild($cEnq);
                    }
                    $imposto->appendChild($IPI);
                    break;

                case "O07": //Grupo do IPITrib CST 00, 49, 50 e 99 0 ou 1 [IPI]
                    // todos esses campos sao obrigatorios
                    $IPITrib = $dom->createElement("IPITrib");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $IPITrib->appendChild($CST);
                    $vIPI = $dom->createElement("vIPI", $dados[2]);
                    $IPITrib->appendChild($vIPI);
                    $IPI->appendChild($IPITrib);
                    break;

                case "O10": //BC e Percentagem de IPI 0 ou 1 [IPITrib]
                    // todos esses campos sao obrigatorios
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $IPITrib->insertBefore($IPITrib->appendChild($vBC), $vIPI);
                    $pIPI = $dom->createElement("pIPI", $dados[2]);
                    $IPITrib->insertBefore($IPITrib->appendChild($pIPI), $vIPI);
                    break;

                case "O11": //Quantidade total e Valor 0 ou 1 [IPITrib]
                    // todos esses campos sao obrigatorios
                    $qUnid = $dom->createElement("qUnid", $dados[1]);
                    $IPITrib->insertBefore($IPITrib->appendChild($qUnid), $vIPI);
                    $vUnid = $dom->createElement("vUnid", $dados[2]);
                    $IPITrib->insertBefore($IPITrib->appendChild($vUnid), $vIPI);
                    break;

                case "O08": //Grupo IPI Não tributavel 0 ou 1 [IPI]
                    // todos esses campos sao obrigatorios
                    $IPINT = $dom->createElement("IPINT");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $IPINT->appendChild($CST);
                    $IPI->appendChild($IPINT);
                    break;

                case "P": //Grupo do Imposto de Importação 0 ou 1 [imposto]
                    // todos esses campos sao obrigatorios
                    $II = $dom->createElement("II");
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $II->appendChild($vBC);
                    $vDespAdu = $dom->createElement("vDespAdu", $dados[2]);
                    $II->appendChild($vDespAdu);
                    $vII = $dom->createElement("vII", $dados[3]);
                    $II->appendChild($vII);
                    $vIOF = $dom->createElement("vIOF", $dados[4]);
                    $II->appendChild($vIOF);
                    $imposto->appendChild($II);
                    break;

                case "Q": //Grupo do PIS obrigatorio [imposto]
                    $PIS = $dom->createElement("PIS");
                    $imposto->appendChild($PIS);
                    break;

                case "Q02": //Grupo de PIS tributado pela alíquota 0 pou 1 [PIS]
                    // todos esses campos sao obrigatorios
                    $PISAliq = $dom->createElement("PISAliq");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $PISAliq->appendChild($CST);
                    $vBC = $dom->createElement("vBC", $dados[2]);
                    $PISAliq->appendChild($vBC);
                    $pPIS = $dom->createElement("pPIS", $dados[3]);
                    $PISAliq->appendChild($pPIS);
                    $vPIS = $dom->createElement("vPIS", $dados[4]);
                    $PISAliq->appendChild($vPIS);
                    $PIS->appendChild($PISAliq);
                    break;

                case "Q03": //Grupo de PIS tributado por Qtde 0 ou 1 [PIS]
                    // todos esses campos sao obrigatorios
                    $PISQtde = $dom->createElement("PISQtde");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $PISQtde->appendChild($CST);
                    $qBCProd = $dom->createElement("qBCProd", $dados[2]);
                    $PISQtde->appendChild($qBCProd);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[3]);
                    $PISQtde->appendChild($vAliqProd);
                    $vPIS = $dom->createElement("vPIS", $dados[4]);
                    $PISQtde->appendChild($vPIS);
                    $PIS->appendChild($PISQtde);
                    break;

                case "Q04": //Grupo de PIS não tributado 0 ou 1 [PIS]
                    // todos esses campos sao obrigatorios
                    $PISNT = $dom->createElement("PISNT");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $PISNT->appendChild($CST);
                    $PIS->appendChild($PISNT);
                    break;

                case "Q05": //Grupo de PIS Outras Operações 0 ou 1 [PIS]
                    //Q05|CST|vPIS|
                    $PISOutr = $dom->createElement("PISOutr");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $PISOutr->appendChild($CST);
                    $vPIS = $dom->createElement("vPIS", $dados[2]);
                    $PISOutr->appendChild($vPIS);
                    $PIS->appendChild($PISOutr);
                    break;

                case "Q07": //Valor da Base de Cálculo do PIS e Alíquota do PIS (em percentual) 0 pu 1 [PISOutr]
                    // todos esses campos sao obrigatorios
                    //Q07|vBC|pPIS|
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $PISOutr->insertBefore($vBC, $vPIS);
                    $pPIS = $dom->createElement("pPIS", $dados[2]);
                    $PISOutr->insertBefore($pPIS, $vPIS);
                    break;

                case "Q10": //Quantidade Vendida e Alíquota do PIS (em reais) 0 ou 1 [PISOutr]
                    // todos esses campos sao obrigatorios
                    //Q10|qBCProd|vAliqProd|
                    $qBCProd = $dom->createElement("qBCProd", $dados[1]);
                    $PISOutr->insertBefore($qBCProd, $vPIS);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[2]);
                    $PISOutr->insertBefore($vAliqProd, $vPIS);
                    break;

                case "R": //Grupo de PIS Substituição Tributária 0 ou 1 [imposto]
                    // todos esses campos sao obrigatorios
                    $PISST = $dom->createElement("PISST");
                    $vPIS = $dom->createElement("vPIS", $dados[1]);
                    $PISST->appendChild($vPIS);
                    $imposto->appendChild($PISST);
                    break;

                case "R02": //Valor da Base de Cálculo do PIS e Alíquota do PIS (em percentual) 0 ou 1 [PISST]
                    // todos esses campos sao obrigatorios
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $PISST->appendChild($vBC);
                    $pPIS = $dom->createElement("pPIS", $dados[2]);
                    $PISST->appendChild($pPIS);
                    break;

                case "R04": //Quantidade Vendida e Alíquota do PIS (em reais) 0 ou 1 [PISST]
                    // todos esses campos sao obrigatorios
                    $qBCProd = $dom->createElement("qBCProd", $dados[1]);
                    $PISST->appendChild($qBCProd);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[2]);
                    $PISST->appendChild($vAliqProd);
                    break;

                case "S": //Grupo do COFINS obrigatório [imposto]
                    $COFINS = $dom->createElement("COFINS");
                    $imposto->appendChild($COFINS);
                    break;

                case "S02": //Grupo de COFINS tributado pela alíquota 0 ou 1 [COFINS]
                    // todos esses campos sao obrigatorios
                    $COFINSAliq = $dom->createElement("COFINSAliq");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $COFINSAliq->appendChild($CST);
                    $vBC = $dom->createElement("vBC", $dados[2]);
                    $COFINSAliq->appendChild($vBC);
                    $pCOFINS = $dom->createElement("pCOFINS", $dados[3]);
                    $COFINSAliq->appendChild($pCOFINS);
                    $vCOFINS = $dom->createElement("vCOFINS", $dados[4]);
                    $COFINSAliq->appendChild($vCOFINS);
                    $COFINS->appendChild($COFINSAliq);
                    break;

                case "S03": //Grupo de COFINS tributado por Qtde 0 ou 1 [COFINS]
                    // todos esses campos sao obrigatorios
                    $COFINSQtde = $dom->createElement("COFINSQtde");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $COFINSQtde->appendChild($CST);
                    $qBCProd = $dom->createElement("qBCProd", $dados[2]);
                    $COFINSQtde->appendChild($qBCProd);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[3]);
                    $COFINSQtde->appendChild($vAliqProd);
                    $vCOFINS = $dom->createElement("vCOFINS", $dados[4]);
                    $COFINSQtde->appendChild($vCOFINS);
                    $COFINS->appendChild($COFINSQtde);
                    break;

                case "S04": //Grupo de COFINS não tributado 0 ou 1 [COFINS]
                    // todos esses campos sao obrigatorios
                    $COFINSNT = $dom->createElement("COFINSNT");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $COFINSNT->appendChild($CST);
                    $COFINS->appendChild($COFINSNT);
                    break;

                case "S05"://Grupo de COFINS Outras Operações 0 ou 1 [COFINS]
                    //S05|CST|vCOFINS|
                    $COFINSOutr = $dom->createElement("COFINSOutr");
                    $CST = $dom->createElement("CST", $dados[1]);
                    $COFINSOutr->appendChild($CST);
                    $vCOFINS = $dom->createElement("vCOFINS", $dados[2]);
                    $COFINSOutr->appendChild($vCOFINS);
                    $COFINS->appendChild($COFINSOutr);
                    break;

                case "S07": //Valor da Base de Cálculo da COFINS e Alíquota da COFINS (em percentual) 0 ou 1 [COFINSOutr]
                    // todos esses campos sao obrigatorios
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $COFINSOutr->insertBefore($vBC, $vCOFINS);
                    $pCOFINS = $dom->createElement("pCOFINS", $dados[2]);
                    $COFINSOutr->insertBefore($pCOFINS, $vCOFINS);
                    break;

                case "S09": //Quantidade Vendida e Alíquota da COFINS (em reais) 0 ou 1 [COFINSOutr]
                    // todos esses campos sao obrigatorios
                    $qBCProd = $dom->createElement("qBCProd", $dados[1]);
                    $COFINSOutr->insertBefore($qBCProd, $vCOFINS);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[2]);
                    $COFINSOutr->insertBefore($vAliqProd, $vCOFINS);
                    break;

                case "T": //Grupo de COFINS Substituição Tributária 0 ou 1 [imposto]
                    // todos esses campos sao obrigatorios
                    $COFINSST = $dom->createElement("COFINSST");
                    $vCOFINS = $dom->createElement("vCOFINS", $dados[1]);
                    $COFINSST->appendChild($vCOFINS);
                    $imposto->appendChild($COFINSST);
                    break;

                case "T02": //Valor da Base de Cálculo da COFINS e Alíquota da COFINS (em percentual) 0 ou 1 [COFINSST]
                    // todos esses campos sao obrigatorios
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $COFINSST->insertBefore($vBC, $vCOFINS);
                    $pCOFINS = $dom->createElement("pCOFINS", $dados[2]);
                    $COFINSST->insertBefore($pCOFINS, $vCOFINS);
                    break;

                case "T04": //Quantidade Vendida e Alíquota da COFINS (em reais) 0 u 1 [COFINSST]
                    // todos esses campos sao obrigatorios
                    $qBCProd = $dom->createElement("qBCProd", $dados[1]);
                    $COFINSST->appendChild($qBCProd);
                    $vAliqProd = $dom->createElement("vAliqProd", $dados[2]);
                    $COFINSST->appendChild($vAliqProd);
                    break;

                case "U": //Grupo do ISSQN 0 ou 1 [imposto]
                    // todos esses campos sao obrigatorios
                    $ISSQN = $dom->createElement("ISSQN");
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $ISSQN->appendChild($vBC);
                    $vAliq = $dom->createElement("vAliq", $dados[2]);
                    $ISSQN->appendChild($vAliq);
                    $vISSQN = $dom->createElement("vISSQN", $dados[3]);
                    $ISSQN->appendChild($vISSQN);
                    $cMunFG = $dom->createElement("cMunFG", $dados[4]);
                    $ISSQN->appendChild($cMunFG);
                    $cListServ = $dom->createElement("cListServ", $dados[5]);
                    $ISSQN->appendChild($cListServ);
                    $cSitTrib = $dom->createElement("cSitTrib", $dados[6]);
                    $ISSQN->appendChild($cSitTrib);
                    $imposto->appendChild($ISSQN);
                    break;

                case "W": // Grupo de Valores Totais da NF-e obrigatorio [infNFe]
                    $total = $dom->createElement("total");
                    $infNFe->appendChild($total);
                    break;

                case "W02": //Grupo de Valores Totais referentes ao ICMS obrigatorio [total]
                    // todos esses campos sao obrigatorios
                    $ICMSTot = $dom->createElement("ICMSTot");
                    $vBC = $dom->createElement("vBC", $dados[1]);
                    $ICMSTot->appendChild($vBC);
                    $vICMS = $dom->createElement("vICMS", $dados[2]);
                    $ICMSTot->appendChild($vICMS);
                    
                    $vICMS = $dom->createElement("vICMSDeson", "0.00");
                    $ICMSTot->appendChild($vICMS);
                    $vBCST = $dom->createElement("vBCST", $dados[3]);
                    $ICMSTot->appendChild($vBCST);
                    
                    $vST = $dom->createElement("vST", $dados[4]);
                    $ICMSTot->appendChild($vST);
                    $vProd = $dom->createElement("vProd", $dados[5]);
                    $ICMSTot->appendChild($vProd);
                    $vFrete = $dom->createElement("vFrete", $dados[6]);
                    $ICMSTot->appendChild($vFrete);
                    $vSeg = $dom->createElement("vSeg", $dados[7]);
                    $ICMSTot->appendChild($vSeg);
                    $vDesc = $dom->createElement("vDesc", $dados[8]);
                    $ICMSTot->appendChild($vDesc);
                    $vII = $dom->createElement("vII", $dados[9]);
                    $ICMSTot->appendChild($vII);
                    $vIPI = $dom->createElement("vIPI", $dados[10]);
                    $ICMSTot->appendChild($vIPI);
                    $vPIS = $dom->createElement("vPIS", $dados[11]);
                    $ICMSTot->appendChild($vPIS);
                    $vCOFINS = $dom->createElement("vCOFINS", $dados[12]);
                    $ICMSTot->appendChild($vCOFINS);
                    $vOutro = $dom->createElement("vOutro", $dados[13]);
                    $ICMSTot->appendChild($vOutro);
                    $vNF = $dom->createElement("vNF", $dados[14]);
                    $ICMSTot->appendChild($vNF);
                    $total->appendChild($ICMSTot);
                    break;

                case "W17": // Grupo de Valores Totais referentes ao ISSQN 0 ou 1 [total]
                    $ISSQNtot = $dom->createElement("ISSQNtot");
                    if (!empty($dados[1])) {
                        $vServ = $dom->createElement("vServ", $dados[1]);
                        $ISSQNtot->appendChild($vServ);
                    }
                    if (!empty($dados[2])) {
                        $vBC = $dom->createElement("vBC", $dados[2]);
                        $ISSQNtot->appendChild($vBC);
                    }
                    if (!empty($dados[3])) {
                        $vISS = $dom->createElement("vISS", $dados[3]);
                        $ISSQNtot->appendChild($vISS);
                    }
                    if (!empty($dados[4])) {
                        $vPIS = $dom->createElement("vPIS", $dados[4]);
                        $ISSQNtot->appendChild($vPIS);
                    }
                    if (!empty($dados[5])) {
                        $vCOFINS = $dom->createElement("vCOFINS", $dados[5]);
                        $ISSQNtot->appendChild($vCOFINS);
                    }
                    $total->appendChild($ISSQNtot);
                    break;

                case "W23": //Grupo de Retenções de Tributos 0 ou 1 [total]
                    $retTrib = $dom->createElement("retTrib");
                    if (!empty($dados[1])) {
                        $vRetPIS = $dom->createElement("vRetPIS", $dados[1]);
                        $retTrib->appendChild($vRetPIS);
                    }
                    if (!empty($dados[2])) {
                        $vRetCOFINS = $dom->createElement("vRetCOFINS", $dados[2]);
                        $retTrib->appendChild($vRetCOFINS);
                    }
                    if (!empty($dados[3])) {
                        $vRetCSLL = $dom->createElement("vRetCSLL", $dados[3]);
                        $retTrib->appendChild($vRetCSLL);
                    }
                    if (!empty($dados[4])) {
                        $vBCIRRF = $dom->createElement("vBCIRRF", $dados[4]);
                        $retTrib->appendChild($vBCIRRF);
                    }
                    if (!empty($dados[5])) {
                        $vIRRF = $dom->createElement("vIRRF", $dados[5]);
                        $retTrib->appendChild($vIRRF);
                    }
                    if (!empty($dados[6])) {
                        $vBCRetPrev = $dom->createElement("vBCRetPrev", $dados[6]);
                        $retTrib->appendChild($vBCRetPrev);
                    }
                    if (!empty($dados[7])) {
                        $vRetPrev = $dom->createElement("vRetPrev", $dados[7]);
                        $retTrib->appendChild($vRetPrev);
                    }
                    $total->appendChild($retTrib);
                    break;

                case "X": // Grupo de Informações do Transporte da NF-e obrigatorio [infNFe]
                    // todos esses campos são obrigatórios
                    $transp = $dom->createElement("transp");
                    $modFrete = $dom->createElement("modFrete", $dados[1]);
                    $transp->appendChild($modFrete);
                    $infNFe->appendChild($transp);
                    break;

                case "X03": //Grupo Transportador 0 ou 1 [transp]
                    $transporta = $dom->createElement("transporta");
                    if (!empty($dados[1])) {
                        $xNome = $dom->createElement("xNome", $dados[1]);
                        $transporta->appendChild($xNome);
                    }
                    if (!empty($dados[2])) {
                        $IE = $dom->createElement("IE", $dados[2]);
                        $transporta->appendChild($IE);
                    }
                    if (!empty($dados[3])) {
                        $xEnder = $dom->createElement("xEnder", $dados[3]);
                        $transporta->appendChild($xEnder);
                    }
                    if (!empty($dados[5])) {
                        $xMun = $dom->createElement("xMun", $dados[5]);
                        $transporta->appendChild($xMun);
                    }
                    if (!empty($dados[4])) {
                        $UF = $dom->createElement("UF", $dados[4]);
                        $transporta->appendChild($UF);
                    }
                    $transp->appendChild($transporta);
                    break;

                case "X04": //CNPJ 0 ou 1 [transporta]
                    if (!empty($dados[1])) {
                        $CNPJ = $dom->createElement("CNPJ", $dados[1]);
                        $transporta->insertBefore($transporta->appendChild($CNPJ), $xNome);
                    }
                    break;

                case "X05": //CPF 0 ou 1 [transporta]
                    if (!empty($dados[1])) {
                        $CPF = $dom->createElement("CPF", $dados[1]);
                        $transporta->insertBefore($transporta->appendChild($CPF), $xNome);
                    }
                    break;

                case "X11": //Grupo de Retenção do ICMS do transporte 0 ou 1 [transp]
                    // todos esses campos são obrigatórios
                    $retTransp = $dom->createElement("retTransp");
                    $vServ = $dom->createElement("vServ", $dados[1]);
                    $retTransp->appendChild($vServ);
                    $vBCRet = $dom->createElement("vBCRet", $dados[2]);
                    $retTransp->appendChild($vBCRet);
                    $pICMSRet = $dom->createElement("pICMSRet", $dados[3]);
                    $retTransp->appendChild($pICMSRet);
                    $vICMSRet = $dom->createElement("vICMSRet", $dados[4]);
                    $retTransp->appendChild($vICMSRet);
                    $CFOP = $dom->createElement("CFOP", $dados[5]);
                    $retTransp->appendChild($CFOP);
                    $cMunFG = $dom->createElement("cMunFG", $dados[6]);
                    $retTransp->appendChild($cMunFG);
                    $transp->appendChild($retTransp);
                    break;

                case "X18": //Grupo Veículo 0 ou 1 [transp]
                    if (!empty($dados[1])) {
                        $veicTransp = $dom->createElement("veicTransp");
                        $placa = $dom->createElement("placa", $dados[1]);
                        $veicTransp->appendChild($placa);
                        $UF = $dom->createElement("UF", $dados[2]);
                        $veicTransp->appendChild($UF);
                        if (!empty($dados[3])) {
                            $RNTC = $dom->createElement("RNTC", $dados[3]);
                            $veicTransp->appendChild($RNTC);
                        }
                        $transp->appendChild($veicTransp);
                    }
                    break;

                case "X22": //Grupo Reboque 0 a 5 [transp]
                    $reboque = $dom->createElement("reboque");
                    $placa = $dom->createElement("placa", $dados[1]);
                    $reboque->appendChild($placa);
                    $UF = $dom->createElement("UF", $dados[2]);
                    $reboque->appendChild($UF);
                    if (!empty($dados[3])) {
                        $RNTC = $dom->createElement("RNTC", $dados[3]);
                        $reboque->appendChild($RNTC);
                    }
                    if (!empty($dados[4])) {
                        $vagao = $dom->createElement("vagao", $dados[4]);
                        $reboque->appendChild($vagao);
                    }
                    if (!empty($dados[5])) {
                        $balsa = $dom->createElement("balsa", $dados[5]);
                        $reboque->appendChild($balsa);
                    }
                    $transp->appendChild($reboque);
                    break;

                case "X26": //Grupo Volumes 0 a N [transp]
                    if (!empty($dados[1])) {
                        $vol = $dom->createElement("vol");
                        $qVol = $dom->createElement("qVol", $dados[1]);
                        $vol->appendChild($qVol);

                        if (!empty($dados[2])) {
                            $esp = $dom->createElement("esp", $dados[2]);
                            $vol->appendChild($esp);
                        }
                        if (!empty($dados[3])) {
                            $marca = $dom->createElement("marca", $dados[3]);
                            $vol->appendChild($marca);
                        }
                        if (!empty($dados[4])) {
                            $nVol = $dom->createElement("nVol", $dados[4]);
                            $vol->appendChild($nVol);
                        }
                        if (!empty($dados[5])) {
                            $pesoL = $dom->createElement("pesoL", $dados[5]);
                            $vol->appendChild($pesoL);
                        }
                        if (!empty($dados[6])) {
                            $pesoB = $dom->createElement("pesoB", $dados[6]);
                            $vol->appendChild($pesoB);
                        }
                        $transp->appendChild($vol);
                    }
                    break;

                case "X33": //Grupo de Lacres 0 a N [vol]
                    //todos os campos são obrigatorios
                    $lacres = $dom->createElement("lacres");
                    $nLacre = $dom->createElement("nLacre", $dados[1]);
                    $lacres->appendChild($nLacre);
                    $vol->appendChild($lacres);
                    break;

                case "Y": //Grupo de Cobrança 0 ou 1 [infNFe]
                    $cobr = $dom->createElement("cobr");
                    $infNFe->appendChild($cobr);
                    break;

                case "Y02": //Grupo da Fatura 0 ou 1 [cobr]
                    if (!isset($cobr)) {
                        $cobr = $dom->createElement("cobr");
                        $infNFe->appendChild($cobr);
                    }
                    $fat = $dom->createElement("fat");
                    if (!empty($dados[1])) {
                        $nFat = $dom->createElement("nFat", $dados[1]);
                        $fat->appendChild($nFat);
                    }
                    if (!empty($dados[2])) {
                        $vOrig = $dom->createElement("vOrig", $dados[2]);
                        $fat->appendChild($vOrig);
                    }
                    if (!empty($dados[3])) {
                        $vDesc = $dom->createElement("vDesc", $dados[3]);
                        $fat->appendChild($vDesc);
                    }
                    if (!empty($dados[4])) {
                        $vLiq = $dom->createElement("vLiq", $dados[4]);
                        $fat->appendChild($vLiq);
                    }
                    $cobr->appendChild($fat);
                    break;

                case "Y07": //Grupo da Duplicata 0 a N [cobr]
                    if (!isset($cobr)) {
                        $cobr = $dom->createElement("cobr");
                        $infNFe->appendChild($cobr);
                    }
                    $dup = $dom->createElement("dup");
                    if (!empty($dados[1])) {
                        $nDup = $dom->createElement("nDup", $dados[1]);
                        $dup->appendChild($nDup);
                    }
                    if (!empty($dados[2])) {
                        $dVenc = $dom->createElement("dVenc", $dados[2]);
                        $dup->appendChild($dVenc);
                    }
                    if (!empty($dados[3])) {
                        $vDup = $dom->createElement("vDup", $dados[3]);
                        $dup->appendChild($vDup);
                    }
                    $cobr->appendChild($dup);
                    break;

                case "Z": //Grupo de Informações Adicionais 0 ou 1 [infNFe]
                    $infAdic = $dom->createElement("infAdic");
                    if (!empty($dados[1])) {
                        $infAdFisco = $dom->createElement("infAdFisco", $dados[1]);
                        $infAdic->appendChild($infAdFisco);
                    }
                    if (!empty($dados[2])) {
                        $infCpl = $dom->createElement("infCpl", $dados[2]);
                        $infAdic->appendChild($infCpl);
                    }
                    $infNFe->appendChild($infAdic);
                    break;

                case "Z04": //Grupo do campo de uso livre do contribuinte 0-10 [infAdic]
                    //todos os campos são obrigatorios
                    $obsCont = $dom->createElement("obsCont");
                    $obsCont->setAttribute("xCampo", $dados[1]);
                    $xTexto = $dom->createElement("xTexto", $dados[2]);
                    $obsCont->appendChild($xTexto);
                    $infAdic->appendChild($obsCont);
                    break;

                case "Z07": //Grupo do campo de uso livre do Fisco 0-10 [infAdic]
                    //todos os campos são obrigatorios
                    $obsFisco = $dom->createElement("obsFisco");
                    $obsFisco->setAttribute("xCampo", $dados[1]);
                    $xTexto = $dom->createElement("xTexto", $dados[2]);
                    $obsFisco->appendChild($xTexto);
                    $infAdic->appendChild($obsFisco);
                    break;

                case "Z10": //Grupo do processo referenciado 0 ou N [infAdic]
                    //todos os campos são obrigatorios
                    $procRef = $dom->createElement("procRef");
                    $nProc = $dom->createElement("nProc", $dados[1]);
                    $procRef->appendChild($nProc);
                    $procRef = $dom->createElement("indProc", $dados[2]);
                    $procRef->appendChild($indProc);
                    $infAdic->appendChild($proRef);
                    break;

                case "ZA": //Grupo de Exportação 0 ou 1 [infNFe]
                    //todos os campos são obrigatorios
                    $exporta = $dom->createElement("exporta");
                    $UFEmbarq = $dom->createElement("UFEmbarq", $dados[1]);
                    $exporta->appendChild($UFEmbarq);
                    $xLocEmbarq = $dom->createElement("xLocEmbarq", $dados[2]);
                    $exporta->appendChild($xLocEmbarq);
                    $infNFe->appendChild($exporta);
                    break;

                case "ZB": //Grupo de Compra 0 ou 1 [infNFe]
                    $compra = $dom->createElement("compra");
                    if (!empty($dados[1])) {
                        $xNEmp = $dom->createElement("xNEmp", $dados[1]);
                        $compra->appendChild($xNEmp);
                    }
                    if (!empty($dados[2])) {
                        $xPed = $dom->createElement("xPed", $dados[2]);
                        $compra->appendChild($xPed);
                    }
                    if (!empty($dados[3])) {
                        $xCont = $dom->createElement("xCont", $dados[3]);
                        $compra->appendChild($xCont);
                    }
                    $infNFe->appendChild($compra);
                    break;

                case "ZC01": //0 ou 1 Grupo de Cana [infNFe]
                    //todos os campos são obrigatorios
                    //ZC01|safra|ref|qTotMes|qTotAnt|qTotGer|vFor|vTotDed|vLiqFor|
                    $cana = $dom->createElement("cana");
                    $safra = $dom->createElement("safra", $dados[1]);
                    $cana->appendChild($safra);
                    $ref = $dom->createElement("ref", $dados[2]);
                    $cana->appendChild($ref);
                    $qTotMes = $dom->createElement("qTotMes", $dados[3]);
                    $cana->appendChild($qTotMes);
                    $qTotAnt = $dom->createElement("qTotAnt", $dados[4]);
                    $cana->appendChild($qTotAnt);
                    $qTotGer = $dom->createElement("qTotGer", $dados[5]);
                    $cana->appendChild($qTotGer);
                    $vFor = $dom->createElement("vFor", $dados[6]);
                    $cana->appendChild($vFor);
                    $vTotDed = $dom->createElement("vTotDed", $dados[7]);
                    $cana->appendChild($vTotDed);
                    $vLiqFor = $dom->createElement("vLiqFor", $dados[8]);
                    $cana->appendChild($vLiqFor);
                    $infNFe->appendChild($cana);
                    break;

                case "ZC04": //1 a 31 Grupo de Fornecimento diário de cana [cana]
                    //ZC04|dia|qtde|
                    //todos os campos são obrigatorios
                    $forDia = $dom->createElement("forDia");
                    $dia = $dom->createElement("dia", $dados[1]);
                    $forDia->appendChild($dia);
                    $qtde = $dom->createElement("qtde", $dados[2]);
                    $forDia->appendChild($qtde);
                    $cana->appendChild($forDia);
                    break;

                case "ZC10": //0 a 10 Grupo de Deduções – Taxas e Contribuições [cana]
                    //ZC10|xDed|vDed|
                    //todos os campos são obrigatorios
                    $deduc = $dom->createElement("deduc");
                    $xDed = $dom->createElement("xDed", $dados[1]);
                    $deduc->appendChild($xDed);
                    $vDed = $dom->createElement("vDed", $dados[2]);
                    $deduc->appendChild($vDed);
                    $cana->appendChild($deduc);
                    break;
            }
        }

        if (!empty($infNFe)) {

            $NFe->appendChild($infNFe);
            $dom->appendChild($NFe);
            self::__montaChaveXML($dom);
            $xml = $dom->saveXML();
//            $this->xml = $dom->saveXML();
            $xml = str_replace('<?xml version="1.0" encoding="UTF-8  standalone="no"?>', '<?xml version="1.0" encoding="UTF-8"?>', $xml);
            $xml = preg_replace('/\s\s+/', ' ', $xml);
            $xml = preg_replace("/\n/", "", $xml);
            $xml = str_replace("> <", "><", $xml);
            return $xml;
        } else {
            return '';
        }
    }

//end function

    static private function __montaChaveXML($dom) {
        $ide = $dom->getElementsByTagName("ide")->item(0);
        $emit = $dom->getElementsByTagName("emit")->item(0);
        $cUF = $ide->getElementsByTagName('cUF')->item(0)->nodeValue;
        $dEmi = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
        $CNPJ = $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $mod = $ide->getElementsByTagName('mod')->item(0)->nodeValue;
        $serie = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $nNF = $ide->getElementsByTagName('nNF')->item(0)->nodeValue;
        $tpEmis = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
        $cNF = $ide->getElementsByTagName('cNF')->item(0)->nodeValue;
        if (strlen($cNF) != 8) {
            $cNF = $ide->getElementsByTagName('cNF')->item(0)->nodeValue = rand(10000001, 99999999);
        }
        $tempData = $dt = explode("-", $dEmi);
        $forma = "%02d%02d%02d%s%02d%03d%09d%01d%08d"; //%01d";
        $tempChave = sprintf($forma, $cUF, $tempData[0] - 2000, $tempData[1], $CNPJ, $mod, $serie, $nNF, $tpEmis, $cNF);

        $cDV = $ide->getElementsByTagName('cDV')->item(0)->nodeValue = self::getDV($tempChave);
        $chave = $tempChave .= $cDV;
        $infNFe = $dom->getElementsByTagName("infNFe")->item(0);
        $infNFe->setAttribute("Id", "NFe" . $chave);
    }

    static private function getDV($nfe) {
        $multiplicadores = array(2, 3, 4, 5, 6, 7, 8, 9);
        $i = 42;
        $sumaPonderada = 0;
        while ($i >= 0) {
            for ($m = 0; $m < count($multiplicadores) && $i >= 0; $m++) {
                if (isset($nfe[$i]))
                    $sumaPonderada += $nfe[$i] * $multiplicadores[$m];
                $i--;
            }
        }
        $resto = $sumaPonderada % 11;
        if ($resto == '0' || $resto == '1') {
            return 0;
        } else {
            return (11 - $resto);
        }
    }

    static private function signXML($docxml, $certificadoPriKEY, $certificadoPubKey, $tagid = '') {
        if ($tagid == '') {
            throw new Exception("Uma tag deve ser indicada para que seja assinada!!\n");
        }
        if ($docxml == '') {
            throw new Exception("Um xml deve ser passado para que seja assinado!!\n");
        }
        $priv_key = $certificadoPriKEY;
        $pkeyid = openssl_get_privatekey($priv_key);
        $order = array("\r\n", "\n", "\r", "\t");
        $replace = '';
        $docxml = str_replace($order, $replace, $docxml);
        $xmldoc = new DOMDocument('1.0', 'utf-8');
        $xmldoc->preservWhiteSpace = false; //elimina espaços em branco
        $xmldoc->formatOutput = false;
        if ($xmldoc->loadXML($docxml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
            $root = $xmldoc->documentElement;
        } else {
            throw new Exception("Erro ao carregar XML, provavel erro na passagem do parâmetro docXML!!\n");
        }
        $node = $xmldoc->getElementsByTagName($tagid)->item(0);
        $id = trim($node->getAttribute("Id"));
        $idnome = preg_replace('/[^0-9]/', '', $id);
        $dados = $node->C14N(false, false, NULL, NULL);
        $hashValue = hash('sha1', $dados, true);
        $digValue = base64_encode($hashValue);
        $Signature = $xmldoc->createElementNS("http://www.w3.org/2000/09/xmldsig#", 'Signature');
        $root->appendChild($Signature);
        $SignedInfo = $xmldoc->createElement('SignedInfo');
        $Signature->appendChild($SignedInfo);
        $newNode = $xmldoc->createElement('CanonicalizationMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        $newNode = $xmldoc->createElement('SignatureMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/2000/09/xmldsig#rsa-sha1");
        $Reference = $xmldoc->createElement('Reference');
        $SignedInfo->appendChild($Reference);
        $Reference->setAttribute('URI', '#' . $id);
        $Transforms = $xmldoc->createElement('Transforms');
        $Reference->appendChild($Transforms);
        $newNode = $xmldoc->createElement('Transform');
        $Transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/2000/09/xmldsig#enveloped-signature");
        $newNode = $xmldoc->createElement('Transform');
        $Transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/TR/2001/REC-xml-c14n-20010315");
        $newNode = $xmldoc->createElement('DigestMethod');
        $Reference->appendChild($newNode);
        $newNode->setAttribute('Algorithm', "http://www.w3.org/2000/09/xmldsig#sha1");
        $newNode = $xmldoc->createElement('DigestValue', $digValue);
        $Reference->appendChild($newNode);
        $dados = $SignedInfo->C14N(false, false, NULL, NULL);
        $signature = '';
        $resp = openssl_sign($dados, $signature, $pkeyid);
        $signatureValue = base64_encode($signature);
        $newNode = $xmldoc->createElement('SignatureValue', $signatureValue);
        $Signature->appendChild($newNode);
        $KeyInfo = $xmldoc->createElement('KeyInfo');
        $Signature->appendChild($KeyInfo);
        $X509Data = $xmldoc->createElement('X509Data');
        $KeyInfo->appendChild($X509Data);
        $cert = self::__cleanCerts($certificadoPubKey);
        $newNode = $xmldoc->createElement('X509Certificate', $cert);
        $X509Data->appendChild($newNode);
        $docxml = $xmldoc->saveXML();
        openssl_free_key($pkeyid);
        return $docxml;
    }

    static private function __cleanCerts($certFile) {
        $pubKey = $certFile;
        $data = '';
        $arCert = explode("\n", $pubKey);
        foreach ($arCert AS $curData) {
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0 && strncmp($curData, '-----END CERTIFICATE', 20) != 0) {
                $data .= trim($curData);
            }
        }
        return $data;
    }

    private function getMaquetado(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura, Vpuntos_venta $myPuntoVenta, vRazones_sociales $myRazonSocial, Vrazones_sociales_general $myRazonFacturante, Vlocalidades $localidadFacturante, VProvincias $myProvinciaFacturante) {
        $myLocalidad = $localidadFacturante;
        $barrioFacturante = trim($myRazonFacturante->barrio) == '' ? "SEMINARIO" : strtoupper($myRazonFacturante->barrio);
        $barrioFacturado = trim($myRazonSocial->barrio) == '' ? "NO BARRIO" : strtoupper($myRazonSocial->barrio);
        $myLocalidadAlumno = new Vlocalidades($conexion, $myRazonSocial->cod_localidad);
        $myProvincia = $myProvinciaFacturante;
        $myProvinciaAlumnos = new Vprovincias($conexion, $myLocalidadAlumno->provincia_id);
        $facturanteTelefono = str_replace(" ", "", $myRazonFacturante->telefono_cod_area . $myRazonFacturante->telefono_numero);
        $numeroFactura = $myFactura->getPropiedad(Vfacturas::getPropiedadNumeroFactura());
        $claveNfe = $myProvincia->get_identificador_estado() . date("ym") . $myRazonFacturante->documento . "55" . $myPuntoVenta->prefijo . $numeroFactura . "1" . $this->codigo_numerico;
        $dv = self::getDV($claveNfe);
        $inscripcionEstadual = $myRazonSocial->condicion <> 1 ? "ISENTO" : $myRazonSocial->documento;
        $tipoE = $myRazonSocial->condicion <> 9 ? "E03" : "E02";
//        $idCFOP = $myProvincia->get_codigo_estado() == $myProvinciaAlumnos->get_codigo_estado() ? "5" : "6";
        if ($myProvincia->get_codigo_estado() == $myProvinciaAlumnos->get_codigo_estado()){
            $idCFOP = "5";
            $CFOP = $this->cfop;
        } else {
            $idCFOP = "6";
            $CFOP = $myRazonSocial->condicion == 9 ? $this->cfop_juridico : $this->cfop_fisico;
        }
//        $CFOP = $myRazonSocial->condicion == 9 ? $this->cfop_juridico : $this->cfop;
        $valorFactura = round($myFactura->total, 2);
        $arrTemp = explode(".", $valorFactura);
        $parteEntera = $arrTemp[0];
        $parteDecimal = isset($arrTemp[1]) ? str_pad($arrTemp[1], 2, "0") : "00";
        $valorFactura = $parteEntera . "." . $parteDecimal;
        $valorFacturaDecimal = strpos($valorFactura, '.') ? $valorFactura . "00000000" : $valorFactura . ".0000000000";
        $arrTemp = array(".", "-", " ");
        $codigoPostal = str_replace($arrTemp, "", trim($myRazonSocial->codigo_postal));
        $codigoPostal = str_pad($codigoPostal, 8, "0", STR_PAD_LEFT);
        $codigoPostal = substr($codigoPostal, 0, 8);
        $codigoPostalFacturante = str_replace($arrTemp, "", $myRazonFacturante->codigo_postal);        
        $documentoAlumno = str_replace($arrTemp, "", trim($myRazonSocial->documento));
        $maquetado = '';
        $maquetado .= "NOTAFISCAL|1\n";
        $maquetado .= "A|2.00|NFe" . $claveNfe . "$dv|" . "\n";
        $maquetado .= "B|{$myProvincia->get_identificador_estado()}|$this->codigo_numerico|$this->nombre_producto|0|55|$myPuntoVenta->prefijo|$numeroFactura|" . $myFactura->fecha . "|" . $myFactura->fecha . "|" . substr($myFactura->fechareal, 10) . "|1|{$myLocalidad->get_codigo_municipio()}|2|1|7|1|1|3|3.10|||" . "\n";
        $maquetado .= "C|$myRazonFacturante->razon_social|$myRazonFacturante->razon_social|$this->ie||$this->inscripcion_municipal|$this->cnae|$this->regimen_tributario|" . "\n";
        $maquetado .= "C02|$myRazonFacturante->documento|" . "\n";
        $maquetado .= "C05|$myRazonFacturante->direccion_calle|$myRazonFacturante->direccion_numero||$barrioFacturante|{$myLocalidad->get_codigo_municipio()}|$myProvincia->nombre|{$myProvincia->get_codigo_estado()}|$codigoPostalFacturante|1058|BRASIL|$facturanteTelefono|" . "\n";
        $maquetado .= "E|$myRazonSocial->razon_social|$inscripcionEstadual||$myRazonSocial->email|" . "\n";
        $maquetado .= "$tipoE|$documentoAlumno|" . "\n";
        $maquetado .= "E05|$myRazonSocial->direccion_calle|$myRazonSocial->direccion_numero||$barrioFacturado|{$myLocalidadAlumno->get_codigo_municipio()}|$myLocalidadAlumno->nombre|{$myProvinciaAlumnos->get_codigo_estado()}|$codigoPostal|1058|BRASIL||" . "\n";
        $maquetado .= "H|1||" . "\n";
        $maquetado .= "I|21600||1|$this->ncm||{$idCFOP}{$CFOP}|UN|1.0000|$valorFacturaDecimal|$valorFactura||UN|1.0000|$valorFacturaDecimal|||||1|||" . "\n";
        $maquetado .= "M|" . "\n";
        $maquetado .= "N|" . "\n";
        $maquetado .= "N06|0|$this->situacion_tributaria|$this->icms|$this->motivo|" . "\n";
        $maquetado .= "Q|" . "\n";
        $maquetado .= "Q04|06|" . "\n";
        $maquetado .= "S|" . "\n";
        $maquetado .= "S04|06|" . "\n";
        $maquetado .= "W|" . "\n";
        $maquetado .= "W02|0.00|0.00|0.00|0.00|$valorFactura|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|$valorFactura|" . "\n";
        $maquetado .= "X|$this->transporte|" . "\n";
        $maquetado .= "Z|$this->descripcion||" . "\n";
        return $maquetado;
    }

    
//    private function getXML(){
//        $xml = '';
//        
//        
//        return $xml;
//    }
    
    private function getXML(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura, Vpuntos_venta $myPuntoVenta, Vrazones_sociales $myRazonSocial, Vrazones_sociales_general $myRazonFacturante, Vlocalidades $myLocalidadFacturante, Vprovincias $myProvinciaFacturante, $certificadoPriKEY, $certificadoPubKEY, $exportar = false) {
        $maquetado = $this->getMaquetado($conexion, $myFactura, $myPuntoVenta, $myRazonSocial, $myRazonFacturante, $myLocalidadFacturante, $myProvinciaFacturante);
        $xml = self::txt_to_xml(explode("\n", $maquetado), $exportar);
        $xml = self::signXML($xml, $certificadoPriKEY, $certificadoPubKEY, "infNFe");
        return $xml;
    }

    static private function __sendSOAP2($urlsefaz, $namespace, $cabecalho, $dados, $metodo, $certificadoPri, $certificadoPub) {
        if ($urlsefaz == '') {
            throw new Exception("URL do webservice não disponível.\n");
        }
        $data = '';
        $data .= '<?xml version="1.0" encoding="utf-8"?>';
        $data .= '<soap12:Envelope '; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        $data .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $data .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
        $data .= 'xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">'; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        $data .= '<soap12:Header>'; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        $data .= $cabecalho;
        $data .= '</soap12:Header>'; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        $data .= '<soap12:Body>'; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        $data .= $dados;
        $data .= '</soap12:Body>'; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        $data .= '</soap12:Envelope>'; // se cambia soap12 a soap (ver para otros envios si esto es correcto)
        
//        echo "<pre>"; print_r($data); echo "</pre>"; die();
        
        $cCode['100'] = "Continue";
        $cCode['101'] = "Switching Protocols";
        $cCode['200'] = "OK";
        $cCode['201'] = "Created";
        $cCode['202'] = "Accepted";
        $cCode['203'] = "Non-Authoritative Information";
        $cCode['204'] = "No Content";
        $cCode['205'] = "Reset Content";
        $cCode['206'] = "Partial Content";
        $cCode['300'] = "Multiple Choices";
        $cCode['301'] = "Moved Permanently";
        $cCode['302'] = "Found";
        $cCode['303'] = "See Other";
        $cCode['304'] = "Not Modified";
        $cCode['305'] = "Use Proxy";
        $cCode['306'] = "(Unused)";
        $cCode['307'] = "Temporary Redirect";
        $cCode['400'] = "Bad Request";
        $cCode['401'] = "Unauthorized";
        $cCode['402'] = "Payment Required";
        $cCode['403'] = "Forbidden";
        $cCode['404'] = "Not Found";
        $cCode['405'] = "Method Not Allowed";
        $cCode['406'] = "Not Acceptable";
        $cCode['407'] = "Proxy Authentication Required";
        $cCode['408'] = "Request Timeout";
        $cCode['409'] = "Conflict";
        $cCode['410'] = "Gone";
        $cCode['411'] = "Length Required";
        $cCode['412'] = "Precondition Failed";
        $cCode['413'] = "Request Entity Too Large";
        $cCode['414'] = "Request-URI Too Long";
        $cCode['415'] = "Unsupported Media Type";
        $cCode['416'] = "Requested Range Not Satisfiable";
        $cCode['417'] = "Expectation Failed";
        $cCode['500'] = "Internal Server Error";
        $cCode['501'] = "Not Implemented";
        $cCode['502'] = "Bad Gateway";
        $cCode['503'] = "Service Unavailable";
        $cCode['504'] = "Gateway Timeout";
        $cCode['505'] = "HTTP Version Not Supported";
        $tamanho = strlen($data);
        $parametros = Array('Content-Type: application/soap+xml;charset=utf-8;action="' . $namespace . "/" . $metodo . '"', 'SOAPAction: "' . $metodo . '"', "Content-length: $tamanho");

        /* como es un sistema distribuido debemos guardar el archivo de certificados en la catrpeta temportal para poder enviarlo por CURL */
        $tempDir = sys_get_temp_dir();
        $filePub = $tempDir . "/" . md5($certificadoPub) . ".pem";
        if (!file_exists($filePub)) {
            file_put_contents($filePub, $certificadoPub);
        }
        $filePry = $tempDir . "/" . md5($certificadoPri) . ".pem";    // md5 para ver si el certificado ya fue guardado en el tempotral (el nombre es unico para cada certificado)
        if (!file_exists($filePry)) {
            file_put_contents($filePry, $certificadoPri);       // md5 para ver si el certificado ya fue guardado en el tempotral (el nombre es unico para cada certificado)
        }
        $oCurl = curl_init();
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_URL, $urlsefaz . '');
        curl_setopt($oCurl, CURLOPT_PORT, 443);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 1);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 3);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($oCurl, CURLOPT_SSLCERT, FCPATH.$certificadoPub);
//        curl_setopt($oCurl, CURLOPT_SSLKEY, FCPATH.$certificadoPri);
        curl_setopt($oCurl, CURLOPT_SSLCERT, $filePub);         // ahora el certificado se lee desde la carpeta temporal (ver si el path es completo o ver el valor de FCPATH de las lineas anteriores)
        curl_setopt($oCurl, CURLOPT_SSLKEY, $filePry);

        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parametros);
        $__xml = curl_exec($oCurl);
        $info = curl_getinfo($oCurl);
        $txtInfo = "";
        $txtInfo .= "URL={$info['url']}\n";
        $txtInfo .= "Content type={$info['content_type']}\n";
        $txtInfo .= "Http Code={$info['http_code']}\n";
        $txtInfo .= "Header Size={$info['header_size']}\n";
        $txtInfo .= "Request Size={$info['request_size']}\n";
        $txtInfo .= "Filetime={$info['filetime']}\n";
        $txtInfo .= "SSL Verify Result={$info['ssl_verify_result']}\n";
        $txtInfo .= "Redirect Count={$info['redirect_count']}\n";
        $txtInfo .= "Total Time={$info['total_time']}\n";
        $txtInfo .= "Namelookup={$info['namelookup_time']}\n";
        $txtInfo .= "Connect Time={$info['connect_time']}\n";
        $txtInfo .= "Pretransfer Time={$info['pretransfer_time']}\n";
        $txtInfo .= "Size Upload={$info['size_upload']}\n";
        $txtInfo .= "Size Download={$info['size_download']}\n";
        $txtInfo .= "Speed Download={$info['speed_download']}\n";
        $txtInfo .= "Speed Upload={$info['speed_upload']}\n";
        $txtInfo .= "Download Content Length={$info['download_content_length']}\n";
        $txtInfo .= "Upload Content Length={$info['upload_content_length']}\n";
        $txtInfo .= "Start Transfer Time={$info['starttransfer_time']}\n";
        $txtInfo .= "Redirect Time={$info['redirect_time']}\n";
//        $txtInfo .= "Certinfo=$info[certinfo]\n";
        $n = strlen($__xml);
        $x = stripos($__xml, "<");
        $xml = substr($__xml, $x, $n - $x);
        if ($__xml === false) {
            $mensaje = isset($cCode[$info['http_code']]) ? curl_error($oCurl) . $info['http_code'] . $cCode[$info['http_code']] : curl_error($oCurl) . $info['http_code'];
            throw new Exception($mensaje);
        } else {
//            echo $info['http_code'] . $cCode[$info['http_code']]."\n";
        }
        curl_close($oCurl);
        return $xml;
    }

    static private function sendLot($aNFe, $id, $codigoUF, $certificadoPriKEY, $certificadoPubKEY) {
        $aRetorno = array('bStat' => false, 'cStat' => '', 'xMotivo' => '', 'dhRecbto' => '', 'nRec' => '', 'tMed' => '', 'tpAmb' => '', 'verAplic' => '', 'cUF' => '');
        $aURL = self::$aURL[$codigoUF];
        $servico = 'NfeRecepcao';
        $versao = $aURL[$servico]['version'];
        $urlservico = $aURL[$servico]['URL'];
        $metodo = 'nfeAutorizacaoLote';
        $complemento = '';
        switch ($codigoUF) {
            case 35:
                $complemento = "NfeAutorizacao";
                break;
            
            case 31:
                $complemento = "NfeRecepcao2";
                break;

            default:
                $complemento = $metodo;
                break;
        }
        $namespace = self::$URLPortal . '/wsdl/'.$complemento;
        $sNFe = '';
        if (count($aNFe) > 50) {
            throw new Exception("No maximo 50 NFe devem compor um lote de envio!!\n");
        }
        $sNFe = implode('', $aNFe);
        $sNFe = str_replace(array('<?xml version="1.0" encoding="utf-8"?>', '<?xml version="1.0" encoding="UTF-8"?>'), '', $sNFe);
        $sNFe = str_replace(array("\r", "\n", "\s"), "", $sNFe);
        $cabec = '<nfeCabecMsg xmlns="' . $namespace . '">';
        $cabec .= '<cUF>' . $codigoUF . '</cUF>';
        $cabec .= '<versaoDados>'.$versao.'</versaoDados>';
        $cabec .= '</nfeCabecMsg>';
        $dados = '<nfeDadosMsg xmlns="' . $namespace . '">';
        $dados .= '<enviNFe versao="'.$versao.'" xmlns="' . self::$URLPortal . '">';
        $dados .= '<idLote>' . $id . '</idLote>';
        $dados .= '<indSinc>0</indSinc>';
        $dados .= $sNFe;
        $dados .= '</enviNFe>';
        $dados .= '</nfeDadosMsg>';
        $retorno = self::__sendSOAP2($urlservico, $namespace, $cabec, $dados, $metodo, $certificadoPriKEY, $certificadoPubKEY);
        if ($retorno) {
            $doc = new DOMDocument('1.0', 'utf-8'); //cria objeto DOM
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            $cStat = !empty($doc->getElementsByTagName('cStat')->item(0)->nodeValue) ? $doc->getElementsByTagName('cStat')->item(0)->nodeValue : '';
            if ($cStat == '') {
                throw new Exception("O retorno não contêm cStat verifique o debug do soap !!");
            } else {
                if ($cStat == '103') {
                    $aRetorno['bStat'] = true;
                }
            }
            $aRetorno['cStat'] = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
            $aRetorno['xMotivo'] = !empty($doc->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';
            $aRetorno['dhRecbto'] = !empty($doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue) ? date("d/m/Y H:i:s", self::__convertTime($doc->getElementsByTagName('dhRecbto')->item(0)->nodeValue)) : '';
            $aRetorno['nRec'] = !empty($doc->getElementsByTagName('nRec')->item(0)->nodeValue) ? $doc->getElementsByTagName('nRec')->item(0)->nodeValue : '';
            $aRetorno['tMed'] = !empty($doc->getElementsByTagName('tMed')->item(0)->nodeValue) ? $doc->getElementsByTagName('tMed')->item(0)->nodeValue : '';
            $aRetorno['tpAmb'] = !empty($doc->getElementsByTagName('tpAmb')->item(0)->nodeValue) ? $doc->getElementsByTagName('tpAmb')->item(0)->nodeValue : '';
            $aRetorno['verAplic'] = !empty($doc->getElementsByTagName('verAplic')->item(0)->nodeValue) ? $doc->getElementsByTagName('verAplic')->item(0)->nodeValue : '';
            $aRetorno['cUF'] = !empty($doc->getElementsByTagName('cUF')->item(0)->nodeValue) ? $doc->getElementsByTagName('cUF')->item(0)->nodeValue : '';
        } else {
            throw new Exception("Nao houve retorno Soap verifique a mensagem de erro e o debug!!");
        }
        return $aRetorno;
    }

    static private function __convertTime($DH) {
        if ($DH) {
            $aDH = explode('T', $DH);
            $adDH = explode('-', $aDH[0]);
            $time = explode("-", $aDH[1]);
            $atDH = explode(':', $time[0]);
            $timestampDH = mktime($atDH[0], $atDH[1], $atDH[2], $adDH[1], $adDH[2], $adDH[0]);
            return $timestampDH;
        }
    }

    /* PUBLIC FUNCTIONS */

    public function actualizarNumeroLote() {
        $this->oConnection->update($this->nombreTabla, array("ultimo_lote" => $this->ultimo_lote), "codigo = $this->codigo");
    }

    static function getXMLFacturaAprobadaRevision(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){
        $myFactura = new Vfacturas($conexion, $codFactura);
        $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
        $myRazonSocial = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
        if($myRazonSocial->tipo_documentos != 21)
        {
            $conexion->select('raz1.cod_razon_social');
            $conexion->from('alumnos_razones raz1');
            $conexion->join('alumnos_razones raz2', 'raz2.cod_alumno = raz1.cod_alumno AND raz2.cod_razon_social = '. $myRazonSocial->getCodigo());
            $conexion->where('raz1.default_facturacion = 1');
            $result = $conexion->get();
            $nroRazonDefault = $result->row();
        }
        if(isset($nroRazonDefault->cod_razon_social))
        {
            $myRazonSocial = new Vrazones_sociales($conexion, $nroRazonDefault->cod_razon_social);
        }
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myRazonFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonFacturante->cod_localidad);
        $myProvinciaFacturante = new Vprovincias($conexion, $myLocalidadFacturante->provincia_id);
        $myCertificado = $myFacturante->getCertificado();
        $certificadoPriKEY = $myCertificado->pry_key;
        $certificadoPubKEY = $myCertificado->pub_key;
        $arrPrestadores = Vprestador_toolsnfe::listarPrestador_toolsnfe($conexion, array("cod_punto_venta" => $myPuntoVenta->getCodigo()));
        $myPrestador = new Vprestador_toolsnfe($conexion, $arrPrestadores[0]['codigo']);
        $arrSeguimiento = Vseguimiento_toolsnfe::listarSeguimiento_toolsnfe($conexion, array("cod_factura" => $codFactura, "cod_filial" => $codFilial, "estado" => "habilitada"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
        $nProt = $arrSeguimiento[0]['nProt'];
        $verApli = $arrSeguimiento[0]['verAplic'];
        $chNFe = $arrSeguimiento[0]['nfe'];
        $dataRecibo = str_replace(" ", "T", $arrSeguimiento[0]['dhRecbto'])."-03:00";
        $cStat = $arrSeguimiento[0]['cStat'];
        $xMotivo = $arrSeguimiento[0]['xMotivo'];
        $xml = $myPrestador->getXML($conexion, $myFactura, $myPuntoVenta, $myRazonSocial, $myRazonFacturante, $myLocalidadFacturante, $myProvinciaFacturante, $certificadoPriKEY, $certificadoPubKEY, true);
        $pos = strpos($xml, "<DigestValue>") + 13;
        $pos1 = strpos($xml, "</DigestValue>");
        $digestValue = substr($xml, $pos, $pos1 - $pos);
        $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', "", $xml);
        $xml = '<nfeProc versao="3.10" xmlns="http://www.portalfiscal.inf.br/nfe">'.$xml;
        $xml .= '<protNFe versao="3.10">';
        $xml .= '<infProt Id="ID'.$nProt.'">';
        $xml .= '<tpAmb>1</tpAmb>';
        $xml .= '<verAplic>'.$verApli.'</verAplic>';
        $xml .= '<chNFe>'.$chNFe.'</chNFe>';
        $xml .= '<dhRecbto>'.$dataRecibo.'</dhRecbto>';
        $xml .= '<nProt>'.$nProt.'</nProt>';        
        $xml .= '<digVal>'.$digestValue.'</digVal>';
        $xml .= '<cStat>'.$cStat.'</cStat>';
        $xml .= '<xMotivo>'.$xMotivo.'</xMotivo>';        
        $xml .= '</infProt>';
        $xml .= '</protNFe>';
        $xml .= "</nfeProc>";
        $xml = '<?xml version="1.0" encoding="utf-8"?>'.$xml;
        return $xml;
    }
    
    static public function getXMLFacturaAprobada(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial, $exportar = false) {
        $myFactura = new Vfacturas($conexion, $codFactura);
        if ($myFactura->estado <> Vfacturas::getEstadoHabilitado()) {
            throw new Exception("La factura no se encuentra habilitada");
        } else {
            $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
            $myRazonSocial = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
            if($myRazonSocial->tipo_documentos != 21)
            {
                $conexion->select('raz1.cod_razon_social');
                $conexion->from('alumnos_razones raz1');
                $conexion->join('alumnos_razones raz2', 'raz2.cod_alumno = raz1.cod_alumno AND raz2.cod_razon_social = '. $myRazonSocial->getCodigo());
                $conexion->where('raz1.default_facturacion = 1');
                $result = $conexion->get();
                $nroRazonDefault = $result->row();
            }
            if(isset($nroRazonDefault->cod_razon_social))
            {
                $myRazonSocial = new Vrazones_sociales($conexion, $nroRazonDefault->cod_razon_social);
            }
            $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
            $myRazonFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
            $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonFacturante->cod_localidad);
            $myProvinciaFacturante = new Vprovincias($conexion, $myLocalidadFacturante->provincia_id);
            $myCertificado = $myFacturante->getCertificado();
            $certificadoPriKEY = $myCertificado->pry_key;
            $certificadoPubKEY = $myCertificado->pub_key;
            $arrPrestadores = Vprestador_toolsnfe::listarPrestador_toolsnfe($conexion, array("cod_punto_venta" => $myPuntoVenta->getCodigo()));
            $myPrestador = new Vprestador_toolsnfe($conexion, $arrPrestadores[0]['codigo']);
            $arrSeguimiento = Vseguimiento_toolsnfe::listarSeguimiento_toolsnfe($conexion, array("cod_factura" => $codFactura, "cod_filial" => $codFilial, "estado" => "habilitada"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
            $xml = $myPrestador->getXML($conexion, $myFactura, $myPuntoVenta, $myRazonSocial, $myRazonFacturante, $myLocalidadFacturante, $myProvinciaFacturante, $certificadoPriKEY, $certificadoPubKEY, $exportar);
            $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', "", $xml);
            $pos1 = strpos($xml, "<DigestValue>") + 13;
            $pos2 = strpos($xml, "</DigestValue>", $pos1);
            $dv = substr($xml, $pos1, $pos2 - $pos1);
            $xml = '<nfeProc versao="2.00" xmlns="http://www.portalfiscal.inf.br/nfe">' . $xml;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . $xml;
            $xml .= '<protNFe versao="2.00">';
            $xml .= '<infProt Id="' . $arrSeguimiento[0]['nProt'] . '">';
            $xml .= "<tpAmb>{$arrSeguimiento[0]['tpAmb']}</tpAmb>";
            $xml .= "<verAplic>{$arrSeguimiento[0]['verAplic']}</verAplic>";
            $xml .= "<chNFe>{$arrSeguimiento[0]['nfe']}</chNFe>";
            $xml .= "<dhRecbto>" . str_replace(" ", "T", $arrSeguimiento[0]['dhRecbto']) . "</dhRecbto>";
            $xml .= "<nProt>{$arrSeguimiento[0]['nProt']}</nProt>";
            $xml .= "<digVal>$dv</digVal>";
            $xml .= "<cStat>{$arrSeguimiento[0]['cStat']}</cStat>";
            $xml .= "<xMotivo>{$arrSeguimiento[0]['xMotivo']}</xMotivo>";
            $xml .= '</infProt>';
            $xml .= '</protNFe>';
            $xml .= '</nfeProc>';
            return $xml;
        }
    }       

    static public function getPDF(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial) {
        $xml = self::getXMLFacturaAprobada($conexion, $codFactura, $codFilial, true);
        $danfe = new DanfeNFePHP($xml, 'P', 'A4', BASEPATH . '../assents/img/logo.jpg', 'I', '');
        $agregaProcom = $codFilial == 100;
        $myFactura = new Vfacturas($conexion, $codFactura);
        $tributoIBPT = $myFactura->getImporte();
        $porcentajeIBPTEstadual = 0;
        $danfe->montaDANFE('', 'A4', 'C', NFEPHP_SITUACAO_EXTERNA_NONE, false, $agregaProcom, $tributoIBPT, $porcentajeIBPTEstadual);
        return $danfe->printDANFE();
    }
    
    static private function validarDatosFactura(Vrazones_sociales $myRazonSocialTomador, Vprovincias $myProvinciaTomador, &$error = null){ // seguir agregaqndo validaciones
        if ($myRazonSocialTomador->tipo_documentos <> 6 && $myRazonSocialTomador->tipo_documentos <> 21){
            $error = "Documento do Tomador no valido (solo CPF o CNPJ)";
            return false;
        }
        $arrTemp = array(".", "-", " ");
        $documentoAlumno = str_replace($arrTemp, "", trim($myRazonSocialTomador->documento));
        if ($myRazonSocialTomador->tipo_documentos == 6 && strlen($documentoAlumno) <> 14){
            $error = "CNPJ do Tomador Invalido";
            return false;
        }
        if ($myRazonSocialTomador->tipo_documentos == 21 && strlen($documentoAlumno) <> 11){
            $error = "CPF do Tomador Invalido";
            return false;
        }
        if ($myProvinciaTomador->pais <> 2){
            $error = "Tomador não é no Brasil";
            echo $error;
            return false;
        }
        return true;        
    }

    public function enviarFacturas(CI_DB_mysqli_driver $conexion, array $facturas) {
        $arrXML = array();
        $arrCodigoFcaturas = array();
        $codFilial = $conexion->database;
        foreach ($facturas as $factura) {
            $myFactura = new Vfacturas($conexion, $factura['codigo']);
            if ($myFactura->estado == Vfacturas::getEstadoPendiente()){
                $conexion->where("cod_filial", $codFilial);
                $conexion->where("cod_factura", $factura['codigo']);
                $conexion->delete("general.seguimiento_toolsnfe");
                $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
                $myRazonSocial = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
                $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
                $myRazonFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
                $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonFacturante->cod_localidad);
                $myProvinciaFacturante = new Vprovincias($conexion, $myLocalidadFacturante->provincia_id);
                $myLocalidadTomador = new Vlocalidades($conexion, $myRazonSocial->cod_localidad);
                $myProvinciaTomador = new Vprovincias($conexion, $myLocalidadTomador->provincia_id);
                $numeroFactura = $myFactura->getPropiedad(Vfacturas::getPropiedadNumeroFactura());
                $claveNfe = $myProvinciaFacturante->get_identificador_estado() . date("ym") . $myRazonFacturante->documento . "55" . str_pad($myPuntoVenta->prefijo, 3, "0", STR_PAD_LEFT) . str_pad($numeroFactura, 9, "0", STR_PAD_LEFT) . "1" . $this->codigo_numerico;
                $dv = self::getDV($claveNfe);
                $claveNfe .= $dv;
                $error = '';
                if (!self::validarDatosFactura($myRazonSocial, $myProvinciaTomador, $error)){
                    $myFactura->setEstado(Vfacturas::getEstadoError());
                    $mySeguimiento = new Vseguimiento_toolsnfe($conexion);
                    $mySeguimiento->cod_filial = $codFilial;
                    $mySeguimiento->cod_factura = $factura['codigo'];
                    $mySeguimiento->nfe = $claveNfe;
                    $mySeguimiento->tpAmb = 1;
                    $mySeguimiento->verAplic = 'local';
                    $mySeguimiento->dhRecbto = date("Y-m-d H:i:s");
                    $mySeguimiento->xMotivo = $error;
                    $mySeguimiento->estado = Vfacturas::getEstadoError();
                    $mySeguimiento->guardarSeguimiento_toolsnfe();
                } else {
                    $myCertificado = $myFacturante->getCertificado();
                    $certificadoPriKEY = $myCertificado->pry_key;
                    $certificadoPubKEY = $myCertificado->pub_key;
                    $certificadoCERTKEY = $myCertificado->cert;                
                    $arrXML[] = $this->getXML($conexion, $myFactura, $myPuntoVenta, $myRazonSocial, $myRazonFacturante, $myLocalidadFacturante, $myProvinciaFacturante, $certificadoPriKEY, $certificadoPubKEY);
                    $arrCodigoFcaturas[] = $myFactura->getCodigo();
                    $arrClaves[] = $claveNfe;
                }
            }
        }
        $n = count($arrXML);
        if ($n > 0) {
            $k = ceil($n / 10);
            for ($i = 0; $i < $k; $i++) {
                $aNFe = array();
                $arrFacturas = array();
                $arrClavesNfe = array();
                for ($x = $i * 10; $x < (($i + 1) * 10); $x++) {
                    if ($x < $n) {
                        $aNFe[] = $arrXML[$x];
                        $arrFacturas[] = $arrCodigoFcaturas[$x];
                        $arrClavesNfe[] = $arrClaves[$x];
                    }
                }
                $num = $this->ultimo_lote;
                $num++;
                if ($ret = self::sendLot($aNFe, $num, $myProvinciaFacturante->get_identificador_estado(), $certificadoPriKEY, $certificadoCERTKEY)) {
                    $this->ultimo_lote ++;
                    foreach ($arrFacturas as $key => $codigo_factura_enviada) {
                        $myFactura = new Vfacturas($conexion, $codigo_factura_enviada);
                        $fechaTemp = explode("/", substr($ret['dhRecbto'], 0, 10));
                        $horaTemp = substr($ret['dhRecbto'], 11);
                        $fecha = "{$fechaTemp[2]}-{$fechaTemp[1]}-{$fechaTemp[0]} $horaTemp";
                        $mySeguimiento = new Vseguimiento_toolsnfe($conexion);
                        $mySeguimiento->nfe = $arrClavesNfe[$key];
                        $mySeguimiento->bStat = $ret['bStat'];
                        $mySeguimiento->cStat = $ret['cStat'];
                        $mySeguimiento->cUF = $ret['cUF'];
                        $mySeguimiento->cod_factura = $codigo_factura_enviada;
                        $mySeguimiento->cod_filial = $codFilial;
                        $mySeguimiento->dhRecbto = $fecha;
                        $mySeguimiento->nRec = $ret['nRec'];
                        $mySeguimiento->tMed = $ret['tMed'];
                        $mySeguimiento->tpAmb = $ret['tpAmb'];
                        $mySeguimiento->verAplic = $ret['verAplic'];
                        $mySeguimiento->xMotivo = $ret['xMotivo'];
                        if ($ret['bStat'] == 1) {
                            $myFactura->setEstado(Vfacturas::getEstadoEnviado());
                            $mySeguimiento->estado = Vfacturas::getEstadoEnviado();
                            $this->actualizarNumeroLote();
                        } else {
                            $myFactura->setEstado(Vfacturas::getEstadoError());
                            $mySeguimiento->estado = Vfacturas::getEstadoError();
                        }
                        $mySeguimiento->guardarSeguimiento_toolsnfe();
                    }
                } else {
                    throw new Exception("Erro no envio do lote de NFe!!");
                }
            }
        } else {
            echo "Não existem notas prontas para envio na pasta das validadas!!\n";
        }
    }

    public function cancelarFactura(CI_DB_mysqli_driver $conexion, Vfacturas $myFactura, $motivo) {
        if ($myFactura->estado <> Vfacturas::getEstadoHabilitado()) {
            throw new Exception("La factura debe estar en estado habilitado para poder cancelarse");
        }
        if (strlen($motivo) < 15 || strlen($motivo) > 255) {
            throw new Exception("El motivo para cancelar debe contener entre 15 y 255 caracteres");
        }
        $codFilial = $conexion->database;
        $condiciones = array("cod_filial" => $codFilial, "cod_factura" => $myFactura->getCodigo(), "estado" => Vfacturas::getEstadoHabilitado());
        $arrTemp = Vseguimiento_toolsnfe::listarSeguimiento_toolsnfe($conexion, $condiciones);
        if (count($arrTemp) > 0 && isset($arrTemp[0]['nfe']) && $arrTemp[0]['nfe'] <> '' && isset($arrTemp[0]['nProt']) && $arrTemp[0]['nProt'] <> '') {
            $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
            $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
            $myRazonFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
            $myCertificado = $myFacturante->getCertificado();
            $certificadoPriKEY = $myCertificado->pry_key;
            $certificadoPubKEY = $myCertificado->pub_key;
            $chNFe = $arrTemp[0]['nfe'];
            $nProt = $arrTemp[0]['nProt'];
            $tpEvento = '110111';
            $descEvento = 'Cancelamento';
            $nSeqEvento = '1';
            $xJust = $this->__limpaString($motivo);
            $aURL = self::$aURL;
            $numLote = substr(str_replace(',', '', number_format(microtime(true) * 1000000, 0)), 0, 15);
            $dhEvento = date('Y-m-d') . 'T' . date('H:i:s') . "-03:00";
            $cOrgao = "41"; // ¿viene de cUF ?
            $servico = 'RecepcaoEvento';
            $versao = $aURL[$servico]['version'];
            $urlservico = $aURL[$servico]['URL'];
            $metodo = $aURL[$servico]['method'];
            $namespace = self::$URLPortal . '/wsdl/' . $servico;
            $zenSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);
            $id = "ID" . $tpEvento . $chNFe . $zenSeqEvento;
            $xml = '';
            $xml .= "<evento xmlns=\"" . self::$URLPortal . "\" versao=\"$versao\">";
            $xml .= "<infEvento Id=\"$id\">";
            $xml .= "<cOrgao>$cOrgao</cOrgao>";
            $xml .= "<tpAmb>1</tpAmb>";
            $xml .= "<CNPJ>$myRazonFacturante->documento</CNPJ>";
            $xml .= "<chNFe>$chNFe</chNFe>";
            $xml .= "<dhEvento>$dhEvento</dhEvento>";
            $xml .= "<tpEvento>$tpEvento</tpEvento>";
            $xml .= "<nSeqEvento>$nSeqEvento</nSeqEvento>";
            $xml .= "<verEvento>$versao</verEvento>";
            $xml .= "<detEvento versao=\"$versao\">";
            $xml .= "<descEvento>$descEvento</descEvento>";
            $xml .= "<nProt>$nProt</nProt>";
            $xml .= "<xJust>$xJust</xJust>";
            $xml .= "</detEvento></infEvento></evento>";
            $tagid = 'infEvento';
            $xml = $this->signXML($xml, $certificadoPriKEY, $certificadoPubKEY, $tagid);
            $xml = str_replace('<?xml version="1.0"?>', '', $xml);
            $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $xml);
            $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml);
            $xml = str_replace(array("\r", "\n", "\s"), "", $xml);
            $datos = '';
            $datos .= "<envEvento xmlns=\"" . self::$URLPortal . "\" versao=\"$versao\">";
            $datos .= "<idLote>$numLote</idLote>";
            $datos .= $xml;
            $datos .= "</envEvento>";
            $cabec = "<nfeCabecMsg xmlns=\"$namespace\"><cUF>41</cUF><versaoDados>$versao</versaoDados></nfeCabecMsg>";
            $datos = "<nfeDadosMsg xmlns=\"$namespace\">$datos</nfeDadosMsg>";
            $retorno = $this->__sendSOAP2($urlservico, $namespace, $cabec, $datos, $metodo, $certificadoPriKEY, $certificadoPubKEY);
            if (!$retorno) {
                echo "no hubo retorno";
                return false;
            } else {
                $xmlretEvent = new DOMDocument('1.0', 'utf-8'); //cria objeto DOM
                $xmlretEvent->formatOutput = false;
                $xmlretEvent->preserveWhiteSpace = false;
                $xmlretEvent->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
                $retEnvEvento = $xmlretEvent->getElementsByTagName("retEnvEvento")->item(0);
                $cStat = !empty($retEnvEvento->getElementsByTagName('cStat')->item(0)->nodeValue) ? $retEnvEvento->getElementsByTagName('cStat')->item(0)->nodeValue : '';
                $xMotivo = !empty($retEnvEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $retEnvEvento->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';
                if ($cStat == '') {
                    echo "cStat esta en blanco (error en la comunicacion SOAP)";
                    return false;
                }
                if ($cStat == '238' || $cStat == '239') {
//                    $this->__trata239($retorno, $this->UF, $tpAmb, $servico, $versao);
                    echo "entra en cStat 238 - 239 Version de XML no soportada por el webservices<br>";
                    return false;
                }
                if ($cStat != 128) {
                    echo "Retorno de error ($cStat) - $xMotivo";
                    return false;
                } else {
                    $retEvento = $retEnvEvento->getElementsByTagName('retEvento')->item(0);
                    $infEvento = $retEvento->getElementsByTagName('infEvento')->item(0);
                    $verAplic = $infEvento->getElementsByTagName('verAplic')->item(0)->nodeValue;
                    $nProt = $infEvento->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $fechaRetorno = str_replace('T', ' ', substr($infEvento->getElementsByTagName('dhRegEvento')->item(0)->nodeValue, 0, 10));
                    $mySeguimiento = new Vseguimiento_toolsnfe($conexion);
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->cStat = $cStat;
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->dhRecbto = $fechaRetorno;
                    $mySeguimiento->estado = Vfacturas::getEstadoInhabilitado();
                    $mySeguimiento->nProt = $nProt;
                    $mySeguimiento->nfe = $chNFe;
                    $mySeguimiento->verAplic = $verAplic;
                    $mySeguimiento->xMotivo = $xMotivo;
                    $resp = $mySeguimiento->guardarSeguimiento_toolsnfe();
                    $resp = $resp && $myFactura->setEstado(Vfacturas::getEstadoInhabilitado());
                }
                return $resp;
            }
        } else {
            throw new Exception("No se encuantra la factura en seguimiento de facturas");
        }
    }

    /* STATIC FUNCTIONS */

    static public function verificar(CI_DB_mysqli_driver $conexion, $recibo, $certificadoPriKey, $certificadoPubKey, $codigoUF) {
        $tpAmb = '1';
        $aRetorno = array('bStat' => false, 'cStat' => '', 'xMotivo' => '', 'aProt' => '', 'aCanc' => '');
        $aURL = self::$aURL[$codigoUF];
        $servico = 'NfeRetRecepcao';        
        $versao = $aURL[$servico]['version'];
        $urlservico = $aURL[$servico]['URL'];
        $metodo = $aURL[$servico]['method'];
        $servico = $metodo;
        $namespace = self::$URLPortal . '/wsdl/';
        $cabec = '<nfeCabecMsg xmlns="'.$namespace.$metodo.'">';
        $cabec .= '<cUF>'.$codigoUF.'</cUF>';
        $cabec .= '<versaoDados>'.$versao.'</versaoDados>';
        $cabec .= '</nfeCabecMsg>';
        $dados = '<nfeDadosMsg xmlns="'.$namespace.$metodo.'">';
        $dados .= '<consReciNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="' . $versao . '">';
        $dados .= '<tpAmb>' . $tpAmb . '</tpAmb>';
        $dados .= '<nRec>' . $recibo . '</nRec>';
        $dados .= '</consReciNFe>';
        $dados .= '</nfeDadosMsg>';
        $retorno = self::__sendSOAP2($urlservico, $namespace, $cabec, $dados, $metodo, $certificadoPriKey, $certificadoPubKey);
        if ($retorno) {
            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->formatOutput = false;
            $doc->preserveWhiteSpace = false;
            if ($doc->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)){
                $cStat = !empty($doc->getElementsByTagName('cStat')->item(0)->nodeValue) ? $doc->getElementsByTagName('cStat')->item(0)->nodeValue : '';
                if ($cStat == '') {
                    echo "error en la consulta del protocolo<br>";
                    return false;
                }
                $aRetorno['bStat'] = true;
                $aRetorno['cStat'] = $doc->getElementsByTagName('cStat')->item(0)->nodeValue;
                $aRetorno['xMotivo'] = !empty($doc->getElementsByTagName('xMotivo')->item(0)->nodeValue) ? $doc->getElementsByTagName('xMotivo')->item(0)->nodeValue : '';
                $aRetorno['nRec'] = !empty($doc->getElementsByTagName('nRec')->item(0)->nodeValue) ? $doc->getElementsByTagName('nRec')->item(0)->nodeValue : '';
                $aRetorno['tpAmb'] = !empty($doc->getElementsByTagName('tpAmb')->item(0)->nodeValue) ? $doc->getElementsByTagName('tpAmb')->item(0)->nodeValue : '';
                $aRetorno['verAplic'] = !empty($doc->getElementsByTagName('verAplic')->item(0)->nodeValue) ? $doc->getElementsByTagName('verAplic')->item(0)->nodeValue : '';
                $aRetorno['cUF'] = !empty($doc->getElementsByTagName('cUF')->item(0)->nodeValue) ? $doc->getElementsByTagName('cUF')->item(0)->nodeValue : '';
                $aRetorno['cMsg'] = !empty($doc->getElementsByTagName('cMsg')->item(0)->nodeValue) ? $doc->getElementsByTagName('cMsg')->item(0)->nodeValue : '';
                $aRetorno['xMsg'] = !empty($doc->getElementsByTagName('xMsg')->item(0)->nodeValue) ? $doc->getElementsByTagName('xMsg')->item(0)->nodeValue : '';
                if ($cStat == '104') {
                    $protNfe = $doc->getElementsByTagName('protNFe');
                    $conexion->trans_begin();
                    foreach ($protNfe as $d) {
                        $codNfe = $d->getElementsByTagName('chNFe')->item(0)->nodeValue;
                        $arrTemp = Vseguimiento_toolsnfe::listarSeguimiento_toolsnfe($conexion, array("nfe" => $codNfe, "estado" => Vfacturas::getEstadoEnviado()), array(0, 1), array(array("campo" => "id", "orden" => "DESC")));
                        if (count($arrTemp) > 0) {
                            if (isset($arrTemp[0]['id']) && $arrTemp[0]['id'] > 0) {
                                $codFilial = $arrTemp[0]['cod_filial'];
                                if ($codFilial == $conexion->database) {
                                    $nProt = $d->getElementsByTagName('nProt')->item(0)->nodeValue;
                                    $cStat = $d->getElementsByTagName('cStat')->item(0)->nodeValue;
                                    $xMotivo = $d->getElementsByTagName('xMotivo')->item(0)->nodeValue;
                                    $mySeguimiento = new Vseguimiento_toolsnfe($conexion, $arrTemp[0]['id']);
                                    $mySeguimiento->nProt = $nProt;
                                    $mySeguimiento->cStat = $cStat;
                                    $mySeguimiento->xMotivo = $xMotivo;
                                    $myFactura = new Vfacturas($conexion, $arrTemp[0]['cod_factura']);
                                    if ($nProt <> '') {
                                        $myFactura->setEstado(Vfacturas::getEstadoHabilitado());
                                        $mySeguimiento->estado = Vfacturas::getEstadoHabilitado();
                                    } else {
                                        $myFactura->setEstado(Vfacturas::getEstadoError());
                                        $mySeguimiento->estado = Vfacturas::getEstadoError();
                                    }
                                    $mySeguimiento->guardarSeguimiento_toolsnfe();
                                } else {
                                    throw new Exception("La filial a la que se le esta intentando actualizar la factura es distinta a la conexion enviada");
                                }
                            }
                        } else {
                            throw new Exception("No se ha podido localizar la factura con codigo de nfe = $codNfe en estado enviado");
                        }
                    }
                    if ($conexion->trans_status()) {
                        $conexion->trans_commit();
                    } else {
                        $conexion->trans_rollback();
                    }
                }
            } else {
                echo "No se ha popido cargar XML con loadXML<br>";
            }
        } else {
            echo "Nao houve retorno Soap verifique a mensagem de erro e o debug!!!<br>";
        }
        return $aRetorno;
    }
}