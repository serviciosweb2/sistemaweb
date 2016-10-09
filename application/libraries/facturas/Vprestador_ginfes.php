<?php

/**
 * Class Vprestador_ginfes
 *
 * Class  Vprestador_ginfes maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vprestador_ginfes extends Tprestador_ginfes {

    public $naturaleza_operacion = 1; // siempre es servicio ??
    private $status = 1;        // ver este valor
    public $iss_retido = 2;    // ver este valor
    public $valor_iss = 0;
    public $valor_iss_retido = 0;
    public $otras_retenciones = 0;
    public $valor_deducciones = 0;
    public $descuento_condicionado = 0;
    public $descuento_incondicionado = 0;
    private $nombre_servicio = "PAGAMENTO DO MENSUALIDADE";
    private $URLxsi = 'http://www.w3.org/2001/XMLSchema-instance';
    private $urlXmlns = "http://www.ginfes.com.br/";
    private $urlXsdTipos = "http://www.ginfes.com.br/tipos_v03.xsd";
    private $URLdsig = 'http://www.w3.org/2000/09/xmldsig#';
    private $URLCanonMeth = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
    private $URLSigMeth = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
    private $URLTransfMeth_1 = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';
    private $URLTransfMeth_2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
    private $URLDigestMeth = 'http://www.w3.org/2000/09/xmldsig#sha1';
    private $schemeVer = "PL_006n";
    private $sAmb = "producao"; // puede cambiar a homologacao
    private $url_servico = "";
    private $mURL = array();
    private $raizDir = '';
    private $xsdDir;
    private $errMsg = '';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
        $this->url_servico = "https://{$this->sAmb}.ginfes.com.br/ServiceGinfesImpl";
        $this->mURL['EnviarLoteRpsEnvio']['url'] = $this->url_servico;
        $this->mURL['EnviarLoteRpsEnvio']['version'] = 'v03';
        $this->mURL['EnviarLoteRpsEnvio']['method'] = 'RecepcionarLoteRpsV3';
        $this->mURL['EnviarLoteRpsEnvio']['xsd'] = 'servico_enviar_lote_rps_envio_v03.xsd';

        $this->mURL['ConsultarLoteRpsEnvio']['url'] = $this->url_servico;
        $this->mURL['ConsultarLoteRpsEnvio']['version'] = 'v03';
        $this->mURL['ConsultarLoteRpsEnvio']['method'] = 'ConsultarLoteRpsV3';
        $this->mURL['ConsultarLoteRpsEnvio']['xsd'] = 'servico_consultar_lote_rps_envio_v03.xsd';

        $this->mURL['ConsultarSituacaoLoteRpsEnvio']['url'] = $this->url_servico;
        $this->mURL['ConsultarSituacaoLoteRpsEnvio']['version'] = 'v03';
        $this->mURL['ConsultarSituacaoLoteRpsEnvio']['method'] = 'ConsultarSituacaoLoteRpsV3';
        $this->mURL['ConsultarSituacaoLoteRpsEnvio']['xsd'] = 'servico_consultar_situacao_lote_rps_envio_v03.xsd';

        $this->mURL['ConsultarNfseRpsEnvio']['url'] = $this->url_servico;
        $this->mURL['ConsultarNfseRpsEnvio']['version'] = 'v03';
        $this->mURL['ConsultarNfseRpsEnvio']['method'] = 'ConsultarNfsePorRpsV3';
        $this->mURL['ConsultarNfseRpsEnvio']['xsd'] = 'servico_consultar_nfse_rps_envio_v03.xsd';

        $this->mURL['ConsultarNfseEnvio']['url'] = $this->url_servico;
        $this->mURL['ConsultarNfseEnvio']['version'] = 'v03';
        $this->mURL['ConsultarNfseEnvio']['method'] = 'ConsultarNfseV3';
        $this->mURL['ConsultarNfseEnvio']['xsd'] = 'servico_consultar_nfse_envio_v03.xsd';

        $this->mURL['CancelarNfseEnvio']['url'] = $this->url_servico;
        $this->mURL['CancelarNfseEnvio']['version'] = 'v02';
        $this->mURL['CancelarNfseEnvio']['method'] = 'CancelarNfse';
        $this->mURL['CancelarNfseEnvio']['xsd'] = 'servico_cancelar_nfse_envio_v02.xsd';

        $this->raizDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
        $this->xsdDir = $this->raizDir . 'schemes' . DIRECTORY_SEPARATOR;
    }

    /* PRIVATE FUNCIOTNS */

    private function __cleanCerts($certFile) {
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

    private function signXML($docxml, $certificadoPri, $certificadoPub, $tagid = '', $appendTag = false, $ns = '') {
        if ($tagid == '') {
            $msg = "Uma tag deve ser indicada para que seja assinada!!";
            $this->__setError($msg);
            if ($this->exceptions) {
                throw new nfephpException($msg);
            }
            return false;
        }
        if ($docxml == '') {
            $msg = "Um xml deve ser passado para que seja assinado!!";
            $this->__setError($msg);
            if ($this->exceptions) {
                throw new nfephpException($msg);
            }
            return false;
        }
        $priv_key = $certificadoPri;
        $pkeyid = openssl_get_privatekey($priv_key);
        $order = array("\r\n", "\n", "\r", "\t");
        $replace = '';
        $docxml = str_replace($order, $replace, $docxml);
        $xmldoc = new DOMDocument('1.0', 'utf-8');
        $xmldoc->preservWhiteSpace = false;
        $xmldoc->formatOutput = false;
        if ($xmldoc->loadXML($docxml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
            $root = $xmldoc->documentElement;
        } else {
            $msg = "Erro ao carregar XML, provavel erro na passagem do parâmetro docXML!!";
            $this->__setError($msg);
            if ($this->exceptions) {
                throw new nfephpException($msg);
            }
            return false;
        }
        $node = $xmldoc->getElementsByTagName($tagid)->item(0);
        $id = trim($node->getAttribute("Id"));
        $idnome = preg_replace('/[^0-9]/', '', $id);
        $dados = $node->C14N(false, false, NULL, NULL);
        $hashValue = hash('sha1', $dados, true);
        $digValue = base64_encode($hashValue);
        $Signature = $xmldoc->createElementNS($this->URLdsig, 'Signature');
        if (!$appendTag) {
            $root->appendChild($Signature);
        } else {
            $appendNode = $xmldoc->getElementsByTagName($appendTag)->item(0);
            $appendNode->appendChild($Signature);
        }
        $SignedInfo = $xmldoc->createElement($ns . 'SignedInfo');
        $Signature->appendChild($SignedInfo);
        $newNode = $xmldoc->createElement($ns . 'CanonicalizationMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->URLCanonMeth);
        $newNode = $xmldoc->createElement($ns . 'SignatureMethod');
        $SignedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->URLSigMeth);
        $Reference = $xmldoc->createElement($ns . 'Reference');
        $SignedInfo->appendChild($Reference);
        if (empty($id)) {
            $Reference->setAttribute('URI', '');
        } else {
            $Reference->setAttribute('URI', '#' . $id);
        }
        $Transforms = $xmldoc->createElement($ns . 'Transforms');
        $Reference->appendChild($Transforms);
        $newNode = $xmldoc->createElement($ns . 'Transform');
        $Transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->URLTransfMeth_1);
        $newNode = $xmldoc->createElement($ns . 'Transform');
        $Transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->URLTransfMeth_2);
        $newNode = $xmldoc->createElement($ns . 'DigestMethod');
        $Reference->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->URLDigestMeth);
        $newNode = $xmldoc->createElement($ns . 'DigestValue', $digValue);
        $Reference->appendChild($newNode);
        $dados = $SignedInfo->C14N(false, false, NULL, NULL);
        $signature = '';
        $resp = openssl_sign($dados, $signature, $pkeyid);
        $signatureValue = base64_encode($signature);
        $newNode = $xmldoc->createElement($ns . 'SignatureValue', $signatureValue);
        $Signature->appendChild($newNode);
        $KeyInfo = $xmldoc->createElement($ns . 'KeyInfo');
        $Signature->appendChild($KeyInfo);
        $X509Data = $xmldoc->createElement($ns . 'X509Data');
        $KeyInfo->appendChild($X509Data);
        $cert = $this->__cleanCerts($certificadoPub);
        $newNode = $xmldoc->createElement($ns . 'X509Certificate', $cert);
        $X509Data->appendChild($newNode);
        $docxml = $xmldoc->saveXML();
        openssl_free_key($pkeyid);
        return $docxml;
    }

    static private function limparXML($xml) {
        $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', $xml);
        $xml = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $xml);
        $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml);
        $xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml);
        $xml = str_replace("\n", "", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("  ", " ", $xml);
        $xml = str_replace("> <", "><", $xml);
        $xml = trim(str_replace("\n", "", $xml));
        return $xml;
    }

    static private function limparString($texto) {
        $aFind = array('&', 'á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç', 'Á', 'À', 'Ã', 'Â', 'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'Ü', 'Ç');
        $aSubs = array('e', 'a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c', 'A', 'A', 'A', 'A', 'E', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'C');
        $novoTexto = str_replace($aFind, $aSubs, $texto);
        $novoTexto = preg_replace("/[^a-zA-Z0-9 @,-.;:\/]/", "", $novoTexto);
        return $novoTexto;
    }

    // codigoMunicipio es el campo cMun
    private function montarLoteRps($idLote, Vfacturas $myFactura, $codigoMunicipio, $documentoFacturante, Vrazones_sociales $myRazonSocialTomador, $cMunTomador, $codUFTomador, $certificadoPri, $certificadoPub) {
        $numeroLote = date("ym") . sprintf("%011s", $idLote);
        $baseCalculo = $myFactura->total - $this->descuento_incondicionado - $this->valor_deducciones;
        $valorIss = $baseCalculo * $this->alicuota;
        $valorLiquidoNfse = $myFactura->total - $this->valor_pis - $this->valor_cofins - $this->valor_inss - $this->valor_csll - $this->otras_retenciones - $this->valor_iss - $this->descuento_condicionado - $this->descuento_incondicionado;
        $caracteresQuitar = array("-", ".");
        $documentoTomador = str_replace($caracteresQuitar, "", $myRazonSocialTomador->documento);


        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $x = "p1:";
        $count_rps = 1;
        $ListaRps = $dom->createElement($x . "ListaRps");
        $Rps = $dom->createElement($x . "Rps");
        $infRps = $dom->createElement($x . "InfRps");
        $IdentificacaoRps = $dom->createElement($x . "IdentificacaoRps");
        $Numero = $dom->createElement($x . "Numero", $myFactura->getPropiedad(Vfacturas::getPropiedadNumeroRps()));
        $Serie = $dom->createElement($x . "Serie", $this->numero_serie);
        $Tipo = $dom->createElement($x . "Tipo", $this->tipo_nota);

        $IdentificacaoRps->appendChild($Numero);
        $IdentificacaoRps->appendChild($Serie);
        $IdentificacaoRps->appendChild($Tipo);

        $infRps->appendChild($IdentificacaoRps);
        $infRps->appendChild($dom->createElement($x . "DataEmissao", date("Y-m-d") . "T" . "00:00:00"));

        $infRps->appendChild($dom->createElement($x . "NaturezaOperacao", $this->naturaleza_operacion));
        $infRps->appendChild($dom->createElement($x . "RegimeEspecialTributacao", $this->regimen_especial_tibutario));
        $infRps->appendChild($dom->createElement($x . "OptanteSimplesNacional", $this->optante_simples_nacional));
        $infRps->appendChild($dom->createElement($x . "IncentivadorCultural", $this->incentivador_cultural));
        $infRps->appendChild($dom->createElement($x . "Status", $this->status));
        $Servico = $dom->createElement($x . "Servico");
        $Valores = $dom->createElement($x . "Valores");

        $ValorServicos = $dom->createElement($x . "ValorServicos", number_format($myFactura->total, 2, '.', ''));
        $ValorDeducoes = $dom->createElement($x . "ValorDeducoes", number_format($this->valor_deducciones, 2, '.', ''));
        $ValorPis = $dom->createElement($x . "ValorPis", number_format($this->valor_pis, 2, '.', ''));
        $ValorCofins = $dom->createElement($x . "ValorCofins", number_format($this->valor_cofins, 2, '.', ''));
        $ValorIr = $dom->createElement($x . "ValorIr", number_format($this->valor_ir, 2, '.', ''));
        $ValorInss = $dom->createElement($x . "ValorInss", number_format($this->valor_inss, 2, '.', ''));
        $ValorCsll = $dom->createElement($x . "ValorCsll", number_format($this->valor_csll, 2, '.', ''));
        $IssRetido = $dom->createElement($x . "IssRetido", $this->iss_retido);
        $ValorIss = $dom->createElement($x . "ValorIss", number_format($valorIss, 2, '.', ''));
        $ValorIssRetido = $dom->createElement($x . "ValorIssRetido", number_format($this->valor_iss_retido, 2, '.', ''));
        $OutrasRetencoes = $dom->createElement($x . "OutrasRetencoes", number_format($this->otras_retenciones, 2, '.', ''));
        $BaseCalculo = $dom->createElement($x . "BaseCalculo", number_format($baseCalculo, 2, '.', ''));
        $Aliquota = $dom->createElement($x . "Aliquota", number_format($this->alicuota, 3, '.', ''));
        $ValorLiquidoNfse = $dom->createElement($x . "ValorLiquidoNfse", number_format($valorLiquidoNfse, 2, '.', ''));
        $DescontoIncondicionado = $dom->createElement($x . "DescontoIncondicionado", number_format($this->descuento_incondicionado, 2, '.', ''));
        $DescontoCondicionado = $dom->createElement($x . "DescontoCondicionado", number_format($this->descuento_condicionado, 2, '.', ''));

        $Valores->appendChild($ValorServicos);
        $Valores->appendChild($ValorDeducoes);
        $Valores->appendChild($ValorPis);
        $Valores->appendChild($ValorCofins);
        $Valores->appendChild($ValorInss);
        $Valores->appendChild($ValorIr);
        $Valores->appendChild($ValorCsll);
        $Valores->appendChild($IssRetido);
        $Valores->appendChild($ValorIss);
        $Valores->appendChild($ValorIssRetido);
        $Valores->appendChild($OutrasRetencoes);
        $Valores->appendChild($BaseCalculo);
        $Valores->appendChild($Aliquota);
        $Valores->appendChild($ValorLiquidoNfse);
        $Valores->appendChild($DescontoIncondicionado);
        $Valores->appendChild($DescontoCondicionado);

        // Detalhes do serviço
        $ItemListaServico = $dom->createElement($x . "ItemListaServico", trim($this->item_lista_servicio));
        $CodigoTributacaoMunicipio = $dom->createElement($x . "CodigoTributacaoMunicipio", trim($this->codigo_tributacion_municipio));
        $Discriminacao = $dom->createElement($x . "Discriminacao", self::limparString($this->nombre_servicio));
        $CodigoMunicipio = $dom->createElement($x . "CodigoMunicipio", $codigoMunicipio);

        $Servico->appendChild($Valores);
        $Servico->appendChild($ItemListaServico);
        $Servico->appendChild($CodigoTributacaoMunicipio);
        $Servico->appendChild($Discriminacao);
        $Servico->appendChild($CodigoMunicipio);
        $infRps->appendChild($Servico);

        $Prestador = $dom->createElement($x . "Prestador");
        $Cnpj = $dom->createElement($x . "Cnpj", $documentoFacturante);
        $InscricaoMunicipal = $dom->createElement($x . "InscricaoMunicipal", $this->inscripcion_municipal);
        $Prestador->appendChild($Cnpj);
        $Prestador->appendChild($InscricaoMunicipal);

        $Tomador = $dom->createElement($x . "Tomador");
        $IdentificacaoTomador = $dom->createElement($x . "IdentificacaoTomador");
        $CpfCnpj = $dom->createElement($x . "CpfCnpj");

        if ($myRazonSocialTomador->tipo_documentos == 21) {
            $TomadorCpf = $dom->createElement($x . "Cpf", $documentoTomador);
            $CpfCnpj->appendChild($TomadorCpf);
        } elseif ($myRazonSocialTomador->tipo_documentos == 6) {
            $TomadorCnpj = $dom->createElement($x . "Cnpj", $documentoTomador);
            $CpfCnpj->appendChild($TomadorCnpj);
        }

        $IdentificacaoTomador->appendChild($CpfCnpj);
        $RazaoSocial = $dom->createElement($x . "RazaoSocial", $myRazonSocialTomador->razon_social);
        $EEndereco = $dom->createElement($x . "Endereco");
        $Endereco = $dom->createElement($x . "Endereco", $myRazonSocialTomador->direccion_calle);
        $Numero = $dom->createElement($x . "Numero", $myRazonSocialTomador->direccion_numero);
        $Bairro = $dom->createElement($x . "Bairro", "NO BARRIO");
        $CodigoMunicipio = $dom->createElement($x . "CodigoMunicipio", $cMunTomador);
        $Uf = $dom->createElement($x . "Uf", $codUFTomador);
        $caracteresQuitar = array("-");
        $Cep = $dom->createElement($x . "Cep", str_replace($caracteresQuitar, "", $myRazonSocialTomador->codigo_postal));
        $EEndereco->appendChild($Endereco);
        $EEndereco->appendChild($Numero);
        $EEndereco->appendChild($Bairro);
        $EEndereco->appendChild($CodigoMunicipio);
        $EEndereco->appendChild($Uf);
        $EEndereco->appendChild($Cep);
        $Tomador->appendChild($IdentificacaoTomador);
        $Tomador->appendChild($RazaoSocial);
        $Tomador->appendChild($EEndereco);

        $infRps->appendChild($Prestador);
        $infRps->appendChild($Tomador);

        $Rps->appendChild($infRps);
        $ListaRps->appendChild($Rps);

        $LoteRps = $dom->createElement("p:LoteRps");
        $LoteRps->setAttribute("Id", $numeroLote);

        $NumeroLote = $dom->createElement($x . "NumeroLote", $numeroLote);
        $QuantidadeRps = $dom->createElement($x . "QuantidadeRps", $count_rps);
        $Cnpj = $dom->createElement($x . "Cnpj", $documentoFacturante);
        $InscricaoMunicipal = $dom->createElement($x . "InscricaoMunicipal", $this->inscripcion_municipal);

        $EnviarLoteRpsEnvio = $dom->createElement("p:EnviarLoteRpsEnvio");
        $EnviarLoteRpsEnvio->setAttribute("xmlns:xsi", $this->URLxsi);
        $EnviarLoteRpsEnvio->setAttribute("xmlns:p", $this->urlXmlns . $this->mURL['EnviarLoteRpsEnvio']['xsd']);
        $EnviarLoteRpsEnvio->setAttribute("xmlns:" . str_replace(":", "", $x), $this->urlXsdTipos);
        $LoteRps->appendChild($NumeroLote);
        $LoteRps->appendChild($Cnpj);
        $LoteRps->appendChild($InscricaoMunicipal);
        $LoteRps->appendChild($QuantidadeRps);
        $LoteRps->appendChild($ListaRps);
        $EnviarLoteRpsEnvio->appendChild($LoteRps);
        $dom->appendChild($EnviarLoteRpsEnvio);
        $xml = $dom->saveXML();
        $xml = self::limparXML($xml);
        $xml = $this->signXML($xml, $certificadoPri, $certificadoPub, "LoteRps");
        return $xml;
    }

    private function validXML($xml = '', $xsdFile = '', &$aError = '') {
        $flagOK = true;
        libxml_use_internal_errors(true);
        if ($xml == '') {
            $this->errStatus = true;
            $this->errMsg = 'Você deve passar o conteudo do xml assinado como parâmetro.';
            $aError[] = 'Você deve passar o conteudo do xml assinado como parâmetro.';
            return false;
        }
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preservWhiteSpace = false;
        $dom->formatOutput = false;

        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $errors = libxml_get_errors();
        if (!empty($errors)) {
            echo "<pre>"; print_r($errors); echo "<pre>"; die();
            $this->errStatus = true;
            $this->errMsg = 'O dado informado não é um XML ou não foi encontrado. Você deve passar o conteudo de um arquivo xml assinado como parâmetro.';
            $aError[] = 'O dado informado não é um XML ou não foi encontrado. Você deve passar o conteudo de um arquivo xml assinado como parâmetro.';
            return false;
        }

        $Signature = $dom->getElementsByTagName('Signature')->item(0);

        if ($xsdFile == '') {
            $aFile = $this->listDir($this->xsdDir . $this->schemeVer . DIRECTORY_SEPARATOR, 'nfe_v*.xsd', true);
            if (!$aFile[0]) {
                echo "No se encuentra el schema para validar el XML<br>";
                echo "Archivo buscado " . $this->xsdDir . $this->schemeVer . DIRECTORY_SEPARATOR, 'nfe_v*.xsd' . "<br>";
                return false;
            } else {
                $xsdFile = $aFile[0];
            }
        }

        libxml_clear_errors();

        if (!$dom->schemaValidate($xsdFile)) {
            $aIntErrors = libxml_get_errors();
            $flagOK = false;
            if (!isset($Signature)) {
                foreach ($aIntErrors as $k => $intError) {
                    if (strpos($intError->message, '( {http://www.w3.org/2000/09/xmldsig#}Signature )') !== false) {
                        unset($aIntErrors[$k]);
                    }
                }
                reset($aIntErrors);
                $flagOK = true;
            }

            foreach ($aIntErrors as $intError) {
                $flagOK = false;
                $en = array("{http://www.portalfiscal.inf.br/nfe}"
                    , "[facet 'pattern']"
                    , "The value"
                    , "is not accepted by the pattern"
                    , "has a length of"
                    , "[facet 'minLength']"
                    , "this underruns the allowed minimum length of"
                    , "[facet 'maxLength']"
                    , "this exceeds the allowed maximum length of"
                    , "Element"
                    , "attribute"
                    , "is not a valid value of the local atomic type"
                    , "is not a valid value of the atomic type"
                    , "Missing child element(s). Expected is"
                    , "The document has no document element"
                    , "[facet 'enumeration']"
                    , "one of"
                    , "This element is not expected. Expected is"
                    , "is not an element of the set");

                $pt = array(""
                    , "[Erro 'Layout']"
                    , "O valor"
                    , "não é aceito para o padrão."
                    , "tem o tamanho"
                    , "[Erro 'Tam. Min']"
                    , "deve ter o tamanho mínimo de"
                    , "[Erro 'Tam. Max']"
                    , "Tamanho máximo permitido"
                    , "Elemento"
                    , "Atributo"
                    , "não é um valor válido"
                    , "não é um valor válido"
                    , "Elemento filho faltando. Era esperado"
                    , "Falta uma tag no documento"
                    , "[Erro 'Conteúdo']"
                    , "um de"
                    , "Este elemento não é esperado. Esperado é"
                    , "não é um dos seguintes possiveis");

                switch ($intError->level) {
                    case LIBXML_ERR_WARNING:
                        $aError[] = " Atençao $intError->code: " . str_replace($en, $pt, $intError->message);
                        break;
                    case LIBXML_ERR_ERROR:
                        $aError[] = " Erro $intError->code: " . str_replace($en, $pt, $intError->message);
                        break;
                    case LIBXML_ERR_FATAL:
                        $aError[] = " Erro Fatal $intError->code: " . str_replace($en, $pt, $intError->message);
                        break;
                }
                $this->errMsg .= str_replace($en, $pt, $intError->message);
            }
        } else {
            $flagOK = true;
        }
        return $flagOK;
    }

//fim validXML

    private function validarXML($xml, $servico, &$error) {
        $schema = $this->mURL[$servico]['xsd'];
        $version = $this->mURL[$servico]['version'];
        //$schema = (empty($schema)) ? $this->NFeSschema : $schema;
        $xsd = $this->xsdDir . $this->schemeVer . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . $schema;
        $aError = array();
        $resp = $this->validXML($xml, $xsd, $aError);
        if (!$resp) {
            $error = implode("<br>", $aError);
            return false;
        } else {
            $error = '';
            return true;
        }
    }

    private function __sendSOAPNFSe($urlwebservice, $dados, $metodo, $certificadoPriKey, $certificadoCert, $cabecalho = '') {
        $wsdl = $urlwebservice . '?wsdl';
        if (empty($cabecalho)) {
            $cabecalho = '<ns2:cabecalho versao="3" xmlns:ns2="http://www.ginfes.com.br/cabecalho_v03.xsd" ><versaoDados>3</versaoDados></ns2:cabecalho>';
        }
        /* como es un sistema distribuido debemos guardar el archivo de certificados en la catrpeta temportal para poder enviarlo por CURL */
        $tempDir = sys_get_temp_dir();
        $filePub = $tempDir . "/" . md5($certificadoCert) . ".pem";
        if (!file_exists($filePub)) {
            file_put_contents($filePub, $certificadoCert);
        }
        $filePry = $tempDir . "/" . md5($certificadoPriKey) . ".pem";    // md5 para ver si el certificado ya fue guardado en el tempotral (el nombre es unico para cada certificado)
        if (!file_exists($filePry)) {
            file_put_contents($filePry, $certificadoPriKey);       // md5 para ver si el certificado ya fue guardado en el tempotral (el nombre es unico para cada certificado)
        }

        $client = new nusoap_client($wsdl, true, false, false, false, false, 100, 100);
        $client->soap_defencoding = 'UTF-8';
        $client->authtype = 'certificate';
        $client->certRequest['sslcertfile'] = $filePub;
        $client->certRequest['sslkeyfile'] = $filePry;
        $client->certRequest['verifypeer'] = 0;
        $client->certRequest['verifyhost'] = 0;
//        $client->timeout = 100;
//        $client->response_timeout = 100;
        if ($metodo != 'CancelarNfse') {
            $param = array('arg0' => $cabecalho, 'arg1' => $dados);
        } else {
            $param = array('arg0' => $dados);
        }
        $retorno = $client->call($metodo, $param);
        return $retorno;
    }

    static private function limparRetornoSOAP($soap) {
        $soap = str_replace('&lt;', '<', $soap);
        $soap = str_replace('&gt;', '>', $soap);
        $soap = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $soap);
        $soap = utf8_encode($soap);
        return $soap;
    }

    static private function verificarProcessamentoOk($soap, &$error) {
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = FALSE;
        $doc->loadXML($soap, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $erros = $doc->getElementsByTagName('ListaMensagemRetorno');
        if ($erros->length > 0) {
            if ($doc->getElementsByTagName('Codigo')->item(0)->nodeValue == 'A01') { // no se pudo atender la solicitud
                $error = '';
                return false;
            } else {
                $error = "Código de erro: " . $doc->getElementsByTagName('Codigo')->item(0)->nodeValue . "\n";
                $error .= "Mensagem: " . $doc->getElementsByTagName('Mensagem')->item(0)->nodeValue . "\n";
                $error .= "Correção: " . $doc->getElementsByTagName('Correcao')->item(0)->nodeValue . "\n";
                return false;
            }
        }
        return true;
    }

    /* PUBLIC FUNCTIONS */

    /* todas las facturas deben ser del mismo punto de venta (optimiza la funcion) */

    public function getXMLFacturaAprobada(CI_DB_mysqli_driver $conexion, $codFactura, $codFilial){

        $myFactura = new Vfacturas($conexion, $codFactura);
        $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);

        $myRazonSocialFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonSocialFacturante->cod_localidad);
        $myProvinciaFacturante = new Vprovincias($conexion, $myLocalidadFacturante->provincia_id);
        $myFactura = new Vfacturas($conexion, $codFactura);
        $myRazonSocialTomador = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
        $myLocalidadTomador = new Vlocalidades($conexion, $myRazonSocialTomador->cod_localidad);
        $myProvinciaTomador = new Vprovincias($conexion, $myLocalidadTomador->provincia_id);
        $arrSeguimientoEnviada = Vseguimiento_ginfes::listarSeguimiento_ginfes($conexion, array("cod_factura" => $codFactura, "cod_filial" => $codFilial, "estado" => "enviado"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
        $arrSeguimientoAprobada = Vseguimiento_ginfes::listarSeguimiento_ginfes($conexion, array("cod_factura" => $codFactura, "cod_filial" => $codFilial, "estado" => "habilitada"), array(0, 1), array(array("campo" => "id", "orden" => "desc")));
        $baseCalculo = $myFactura->total - $this->descuento_incondicionado - $this->valor_deducciones;
        $baseCalculo = $myFactura->total - $this->descuento_incondicionado - $this->valor_deducciones;
        $valorIss = $baseCalculo * $this->alicuota;
        $valorIss = number_format($valorIss, 2, '.', '');
        
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<ns3:ConsultarNfseRpsResposta xmlns:ns2="http://www.w3.org/2000/09/xmldsig#" xmlns:ns4="http://www.ginfes.com.br/tipos_v03.xsd" xmlns:ns3="http://www.ginfes.com.br/servico_consultar_nfse_rps_resposta_v03.xsd">';
        $xml .= '<ns3:CompNfse>';
        $xml .= '<ns4:Nfse>';
        $xml .= '<ns4:InfNfse>';
        $xml .= '<ns4:Numero>'.$myFactura->getPropiedad(Vfacturas::getPropiedadNumeroFactura()).'</ns4:Numero>';
        $xml .= '<ns4:CodigoVerificacao>'.$arrSeguimientoAprobada[0]['codigo_verificacion'].'</ns4:CodigoVerificacao>';
        $xml .= '<ns4:DataEmissao>'.str_replace(' ', 'T', $arrSeguimientoEnviada[0]['fecha_envio']).'</ns4:DataEmissao>';
        $xml .= '<ns4:IdentificacaoRps>';
        $xml .= '<ns4:Numero>'.$arrSeguimientoEnviada[0]['numero_lote'].'</ns4:Numero>';
        $xml .= '<ns4:Serie>'.$myPuntoVenta->prefijo.'</ns4:Serie>';
        $xml .= '<ns4:Tipo>1</ns4:Tipo>';
        $xml .= '</ns4:IdentificacaoRps>';
        $xml .= '<ns4:DataEmissaoRps>'.substr($arrSeguimientoEnviada[0]['fecha_envio'], 0, 10).'</ns4:DataEmissaoRps>';
        $xml .= '<ns4:NaturezaOperacao>'.$this->naturaleza_operacion.'</ns4:NaturezaOperacao>';
        $xml .= '<ns4:RegimeEspecialTributacao>'.$this->regimen_especial_tibutario.'</ns4:RegimeEspecialTributacao>';
        $xml .= '<ns4:OptanteSimplesNacional>'.$this->optante_simples_nacional.'</ns4:OptanteSimplesNacional>';
        $xml .= '<ns4:IncentivadorCultural>'.$this->incentivador_cultural.'</ns4:IncentivadorCultural>';
        $xml .= '<ns4:Competencia>'.substr($arrSeguimientoEnviada[0]['fecha_envio'], 0, 4).substr($arrSeguimientoEnviada[0]['fecha_envio'], 5, 2).'</ns4:Competencia>';
        $xml .= '<ns4:Servico>';
        $xml .= '<ns4:Valores>';
        $xml .= '<ns4:ValorServicos>'.$myFactura->total.'</ns4:ValorServicos>';
        $xml .= '<ns4:ValorDeducoes>'.$this->valor_deducciones.'</ns4:ValorDeducoes>';
        $xml .= '<ns4:ValorPis>'.(float)($this->valor_pis).'</ns4:ValorPis>';
        $xml .= '<ns4:ValorCofins>'.(float)($this->valor_cofins).'</ns4:ValorCofins>';
        $xml .= '<ns4:ValorInss>'.(float)($this->valor_iss).'</ns4:ValorInss>';
        $xml .= '<ns4:ValorIr>'.(float)($this->valor_ir).'</ns4:ValorIr>';
        $xml .= '<ns4:ValorCsll>'.(float)($this->valor_csll).'</ns4:ValorCsll>';
        $xml .= '<ns4:IssRetido>'.(float)($this->iss_retido).'</ns4:IssRetido>';
        $xml .= '<ns4:ValorIss>'.(float)($valorIss).'</ns4:ValorIss>';
        $xml .= '<ns4:ValorIssRetido>'.(float)($this->valor_iss_retido).'</ns4:ValorIssRetido>';
        $xml .= '<ns4:OutrasRetencoes>'.(float)($this->otras_retenciones).'</ns4:OutrasRetencoes>';
        $xml .= '<ns4:BaseCalculo>'.$myFactura->total.'</ns4:BaseCalculo>';
        $xml .= '<ns4:Aliquota>'.(float)($this->alicuota).'</ns4:Aliquota>';
        $xml .= '<ns4:ValorLiquidoNfse>'.$myFactura->total.'</ns4:ValorLiquidoNfse>';
        $xml .= '<ns4:DescontoIncondicionado>'.$this->descuento_incondicionado.'</ns4:DescontoIncondicionado>';
        $xml .= '<ns4:DescontoCondicionado>'.$this->descuento_condicionado.'</ns4:DescontoCondicionado>';
        $xml .= '</ns4:Valores>';
        $xml .= '<ns4:ItemListaServico>'.$this->item_lista_servicio.'</ns4:ItemListaServico>';
        $xml .= '<ns4:CodigoTributacaoMunicipio>'.$this->codigo_tributacion_municipio.'</ns4:CodigoTributacaoMunicipio>';
        $xml .= '<ns4:Discriminacao>'.self::limparString($this->nombre_servicio).'</ns4:Discriminacao>';
        $xml .= '<ns4:CodigoMunicipio>'.$myLocalidadFacturante->get_codigo_municipio().'</ns4:CodigoMunicipio>';
        $xml .= '</ns4:Servico>';
        $xml .= '<ns4:ValorCredito>0</ns4:ValorCredito>';
        $xml .= '<ns4:PrestadorServico>';
        $xml .= '<ns4:IdentificacaoPrestador>';
        $xml .= '<ns4:Cnpj>'.$myRazonSocialFacturante->documento.'</ns4:Cnpj>';
        $xml .= '<ns4:InscricaoMunicipal>'.$this->inscripcion_municipal.'</ns4:InscricaoMunicipal>';
        $xml .= '</ns4:IdentificacaoPrestador>';
        $xml .= '<ns4:RazaoSocial>'.$myRazonSocialFacturante->razon_social.'</ns4:RazaoSocial>';
        $xml .= '<ns4:Endereco>';
        $xml .= '<ns4:Endereco>'.$myRazonSocialFacturante->direccion_calle.'</ns4:Endereco>';
        $xml .= '<ns4:Numero>'.$myRazonSocialFacturante->direccion_numero.'</ns4:Numero>';
        $complemento = trim($myRazonSocialFacturante->direccion_complemento) <> '' 
                ? trim($myRazonSocialFacturante->direccion_complemento)
                : '0000';
        $xml .= '<ns4:Complemento>'.$complemento.'</ns4:Complemento>';
        $xml .= '<ns4:Bairro>NO BARRIO</ns4:Bairro>';
        $xml .= '<ns4:CodigoMunicipio>'.$myLocalidadFacturante->get_codigo_municipio().'</ns4:CodigoMunicipio>';
        $xml .= '<ns4:Uf>'.$myProvinciaFacturante->get_codigo_estado().'</ns4:Uf>';
        $cep = str_replace(array('-', '.'), '', $myRazonSocialFacturante->codigo_postal);    
        $xml .= '<ns4:Cep>'.$cep.'</ns4:Cep>';
        $xml .= '</ns4:Endereco>';
        $xml .= '<ns4:Contato>';
        $xml .= '<ns4:Telefone>'.$myRazonSocialFacturante->telefono_cod_area.$myRazonSocialFacturante->telefono_numero.'</ns4:Telefone>';
        $xml .= '<ns4:Email>'.$myRazonSocialFacturante->email.'</ns4:Email>';
        $xml .= '</ns4:Contato>';        
        $xml .= '</ns4:PrestadorServico>';
        $xml .= '<ns4:TomadorServico>';
        $xml .= '<ns4:IdentificacaoTomador>';
        $xml .= '<ns4:CpfCnpj>';
        if ($myRazonSocialTomador->tipo_documentos == 21) {
           $xml .= '<ns4:Cpf>'.$myRazonSocialTomador->documento.'</ns4:Cpf>';
        } elseif ($myRazonSocialTomador->tipo_documentos == 6) {
            $xml .= '<ns4:Cnpj>'.$myRazonSocialTomador->documento.'</ns4:Cnpj>';
        }
        $xml .= '</ns4:CpfCnpj>';        
        $xml .= '</ns4:IdentificacaoTomador>';
        $xml .= '<ns4:RazaoSocial>'.$myRazonSocialTomador->razon_social.'</ns4:RazaoSocial>';
        $xml .= '<ns4:Endereco>';
        $xml .= '<ns4:Endereco>'.$myRazonSocialTomador->direccion_calle.'</ns4:Endereco>';
        $xml .= '<ns4:Numero>'.$myRazonSocialTomador->direccion_numero.'</ns4:Numero>';
        $xml .= '<ns4:Bairro>NO BARRIO</ns4:Bairro>';
        $xml .= '<ns4:CodigoMunicipio>'.$myLocalidadTomador->get_codigo_municipio().'</ns4:CodigoMunicipio>';
        $xml .= '<ns4:Uf>'.$myProvinciaTomador->get_codigo_estado().'</ns4:Uf>';
        $cep = str_replace(array('-', '.'), '', $myRazonSocialTomador->codigo_postal); 
        $xml .= '<ns4:Cep>'.$cep.'</ns4:Cep>';
        $xml .= '</ns4:Endereco>';
        $xml .= '</ns4:TomadorServico>';
        $xml .= '<ns4:OrgaoGerador>';
        $xml .= '<ns4:CodigoMunicipio>'.$myLocalidadFacturante->get_codigo_municipio().'</ns4:CodigoMunicipio>';
        $xml .= '<ns4:Uf>'.$myProvinciaFacturante->get_codigo_estado().'</ns4:Uf>';
        $xml .= '</ns4:OrgaoGerador>';
        $xml .= '</ns4:InfNfse>';
        $xml .= '</ns4:Nfse>';
        $xml .= '</ns3:CompNfse>';
        $xml .= '</ns3:ConsultarNfseRpsResposta>';
        return $xml;
    }
    
    private function validarDatosFactura(Vrazones_sociales $myRazonSocialTomador, &$error = null){ // seguir agregaqndo validaciones
        if ($myRazonSocialTomador->tipo_documentos <> 6 && $myRazonSocialTomador->tipo_documentos <> 21){
            $error = "Documento do Tomador no valido (solo CPF o CNPJ)";
            echo $error." - tipo documento ".$myRazonSocialTomador->tipo_documentos."<br>";
            return false;
        } else {
            return true;
        }
    }
    
    public function enviarFacturas(CI_DB_mysqli_driver $conexion, $arrFacturas) {
        $myPuntoVenta = new Vpuntos_venta($conexion, $arrFacturas[0]['punto_venta']);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myRazonSocialFacturante = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $myLocalidadFacturante = new Vlocalidades($conexion, $myRazonSocialFacturante->cod_localidad);
        $myCertificado = $myFacturante->getCertificado();
        $certificadoPri = $myCertificado->pry_key;
        $certificadoPub = $myCertificado->pub_key;
        $certificadoCert = $myCertificado->cert;
        foreach ($arrFacturas as $factura) {
            $myFactura = new Vfacturas($conexion, $factura['codigo']);
            if ($myFactura->estado == Vfacturas::getEstadoPendiente()){
                $idLote = $myFactura->getPropiedad(Vfacturas::getPropiedadNumeroRps());
                $myRazonSocialTomador = new Vrazones_sociales($conexion, $myFactura->codrazsoc);
                $error = '';
                if (!$this->validarDatosFactura($myRazonSocialTomador, $error)){
                    $myFactura->setEstado(Vfacturas::getEstadoError());
                    $mySeguimiento = new Vseguimiento_ginfes($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->estado = Vfacturas::getEstadoError();
                    $mySeguimiento->mensaje = $error;
                    $mySeguimiento->guardarSeguimiento_ginfes();
                    echo "se guarda factura con error por error de documento<br>";
                } else {
                    $myLocalidadTomador = new Vlocalidades($conexion, $myRazonSocialTomador->cod_localidad);
                    $myProvinciaTomador = new Vprovincias($conexion, $myLocalidadTomador->provincia_id);
                    $xml = $this->montarLoteRps($idLote, $myFactura, $myLocalidadFacturante->get_codigo_municipio(), $myRazonSocialFacturante->documento, $myRazonSocialTomador, $myLocalidadFacturante->get_codigo_municipio(), $myProvinciaTomador->get_codigo_estado(), $certificadoPri, $certificadoPub);
                    $servicio = "EnviarLoteRpsEnvio";
                    $error = '';
                    if (!$this->validarXML($xml, $servicio, $error)) {
                        $myFactura->setEstado(Vfacturas::getEstadoError());
                        $mySeguimiento = new Vseguimiento_ginfes($conexion);
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->estado = Vfacturas::getEstadoError();
                        $mySeguimiento->mensaje = $error;
                        $mySeguimiento->guardarSeguimiento_ginfes();
                    } else {
                        $urlservico = $this->mURL[$servicio]['url'];
                        $metodo = $this->mURL[$servicio]['method'];
                        $datos = self::limparXML($xml);
                        echo "VPrestadorGinfes : COD. FACTURA.: " . $factura['codigo'] . "<br/>";
                        print_r($datos);
                        $retorno = $this->__sendSOAPNFSe($urlservico, $datos, $metodo, $certificadoPri, $certificadoCert);
                        echo "VPrestadorGinfes : RETORNO DO ENVIO.: ";
                        print_r($retorno);
                        if ($retorno <> '') {
                            $retorno = self::limparRetornoSOAP($retorno);
                            echo "VPrestadorGinfes : RETORNO LIMPO.: "; print_r($retorno);
                            $error = '';
                            if (!self::verificarProcessamentoOk($retorno, $error)) {
                                if ($error != '') {
                                    $myFactura->setEstado(Vfacturas::getEstadoError());
                                    $mySeguimiento = new Vseguimiento_ginfes($conexion);
                                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                                    $mySeguimiento->cod_filial = $conexion->database;
                                    $mySeguimiento->estado = Vfacturas::getEstadoError();
                                    $mySeguimiento->mensaje = $error;
                                    $mySeguimiento->guardarSeguimiento_ginfes();
                                    //echo "VPrestadorGinfes : ERRO PROCESSAMENTO: ";
                                    //print_r($mySeguimiento);
                                } else {
                                    echo "no se pudo atender la solicitud. Volver a intentar mas tarde<br>";
                                }
                            } else {
                                $doc = new DOMDocument();
                                $doc->formatOutput = FALSE;
                                $doc->preserveWhiteSpace = FALSE;
                                $doc->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
                                $numeroLote = $doc->getElementsByTagName("NumeroLote")->item(0)->nodeValue;
                                $fechaRecibo = $doc->getElementsByTagName("DataRecebimento")->item(0)->nodeValue;
                                $protocolo = $doc->getElementsByTagName("Protocolo")->item(0)->nodeValue;
                                $mySeguimiento = new Vseguimiento_ginfes($conexion);
                                $mySeguimiento->cod_factura = $myFactura->getCodigo();
                                $mySeguimiento->cod_filial = $conexion->database;
                                $mySeguimiento->estado = Vfacturas::getEstadoEnviado();
                                $mySeguimiento->fecha_envio = str_replace("T", " ", $fechaRecibo);
                                $mySeguimiento->numero_lote = $numeroLote;
                                $mySeguimiento->protocolo = $protocolo;
                                $mySeguimiento->guardarSeguimiento_ginfes();
                                $myFactura->setEstado(Vfacturas::getEstadoEnviado());
                                echo "VPrestadorGinfes : XML Retorno.: ";
                                print_r($doc);
                                //echo "VPrestadorGinfes : Seguimiento GINFES";
                                //print_r($mySeguimiento);
                            }
                        } else {
                            echo "hubo error en la comunicacion con el servicio";
                        }
                    }
                }
            }
        }
    }

    public function verificar(CI_DB_mysqli_driver $conexion, $protocolo) {
        $myPuntoVenta = new Vpuntos_venta($conexion, $this->cod_punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $servicio = 'ConsultarLoteRpsEnvio';
        $urlservico = $this->mURL[$servicio]['url'];
        $metodo = $this->mURL[$servicio]['method'];
        $xml = "<p:ConsultarLoteRpsEnvio Id=\"$protocolo\" xmlns:p=\"http://www.ginfes.com.br/servico_consultar_lote_rps_envio_v03.xsd\" xmlns:p1=\"http://www.ginfes.com.br/tipos_v03.xsd\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" >";
        $xml .= "<p:Prestador>";
        $xml .= "<p1:Cnpj>{$myRazonSocial->documento}</p1:Cnpj>";
        $xml .= "<p1:InscricaoMunicipal>{$this->inscripcion_municipal}</p1:InscricaoMunicipal>";
        $xml .= "</p:Prestador><p:Protocolo>$protocolo</p:Protocolo>";
        $xml .= "</p:ConsultarLoteRpsEnvio>";
        $xml = $this->signXML($xml, $myCertificado->pry_key, $myCertificado->pub_key, 'ConsultarLoteRpsEnvio');
        $error = '';
        if (!$this->validarXML($xml, $servicio, $error)) {
            echo "Error en la validacion de schema con el mensaje $error<br>";
            return false;
        }
        $datos = self::limparXML($xml);        
        $retorno = $this->__sendSOAPNFSe($urlservico, $datos, $metodo, $myCertificado->pry_key, $myCertificado->cert);
        echo "VPrestadorGinfes..: ";
        echo "<pre> XML RETORNO VALIDACAO ENVIO: "; print_r($retorno); echo "</pre>";
        if (!empty($retorno)) {
            $arrFacturas = Vseguimiento_ginfes::listarSeguimiento_ginfes($conexion, array("protocolo" => $protocolo));
            $error = '';
            $retorno = self::limparRetornoSOAP($retorno);
            if (!self::verificarProcessamentoOk($retorno, $error)) {
                if ($error <> '') {
                    foreach ($arrFacturas as $factura) {
                        $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                        $myFactura->setEstado(Vfacturas::getEstadoError());
                        $mySeguimiento = new Vseguimiento_ginfes($conexion);
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->estado = Vfacturas::getEstadoError();
                        $mySeguimiento->mensaje = $error;
                        $mySeguimiento->protocolo = $protocolo;
                        $mySeguimiento->guardarSeguimiento_ginfes();
                    }
                    return false;
                } else {
//                    echo "No se ha procesado el lote<br>";
                    return false;
                }
            } else {
                $doc = new DOMDocument();
                $doc->formatOutput = FALSE;
                $doc->preserveWhiteSpace = FALSE;
                $doc->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
                $codigoVerificacion = $doc->getElementsByTagName("CodigoVerificacao")->item(0)->nodeValue;
                $numeroNfse = $doc->getElementsByTagName("Numero")->item(0)->nodeValue;
                foreach ($arrFacturas as $factura) {
                    $myFactura = new Vfacturas($conexion, $factura['cod_factura']);
                    $resp = $myFactura->setEstado(Vfacturas::getEstadoHabilitado());
                    $myFactura->setPropiedad(Vfacturas::getPropiedadNumeroFactura(), $numeroNfse);
                    $mySeguimiento = new Vseguimiento_ginfes($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->codigo_verificacion = $codigoVerificacion;
                    $mySeguimiento->estado = Vfacturas::getEstadoHabilitado();
                    $mySeguimiento->numero_nfse = $numeroNfse;
                    $resp = $resp && $mySeguimiento->guardarSeguimiento_ginfes();
                }
                return $resp;
            }
        } else {
            echo "Error en la comunicacion entre servicios<br>";
            return false;
        }
    }

    public function cancelarFactura($conexion, Vfacturas $myFactura, &$error = null) {
        $myPuntoVenta = new Vpuntos_venta($conexion, $myFactura->punto_venta);
        $myFacturante = new Vfacturantes($conexion, $myPuntoVenta->cod_facturante);
        $myCertificado = $myFacturante->getCertificado();
        $myRazonSocial = new Vrazones_sociales_general($conexion, $myFacturante->cod_razon_social);
        $condiciones = array(
            "cod_factura" => $myFactura->getCodigo(),
            "cod_filial" => $conexion->database,
            "estado" => Vfacturas::getEstadoEnviado()
        );

        $arrFactura = Vseguimiento_ginfes::listarSeguimiento_ginfes($conexion, $condiciones, array(0, 1), array(array("campo" => "id", "orden" => "desc")));
        if (count($arrFactura) == 0) {
            throw new Exception("No se puede encontrar el codigo de cancelamiento para la factura indicada");
        }
        $numNFSe = $arrFactura[0]['numero_nfse'];
        $servicio = 'CancelarNfseEnvio';
        $urlservico = $this->mURL[$servicio]['url'];
        $metodo = $this->mURL[$servicio]['method'];
        $xml = '<tns:CancelarNfseEnvio xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:tipos="http://www.ginfes.com.br/tipos" xmlns:tns="http://www.ginfes.com.br/servico_cancelar_nfse_envio" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $xml .= "<tns:Prestador><tipos:Cnpj>{$myRazonSocial->documento}</tipos:Cnpj>";
        $xml .= "<tipos:InscricaoMunicipal>{$this->inscripcion_municipal}</tipos:InscricaoMunicipal>";
        $xml .= "</tns:Prestador>";
        $xml .= "<tns:NumeroNfse>$numNFSe</tns:NumeroNfse>";
        $xml .= "</tns:CancelarNfseEnvio>";
        $xml = $this->signXML($xml, $myCertificado->pry_key, $myCertificado->pub_key, 'CancelarNfseEnvio', false, "ds:");
        $error = '';
        if (!$this->validarXML($xml, $servicio, $error)) {
            $error = "error en la validacion del schema con el mensaje $error";
            return false;
        }
        $datos = self::limparXML($xml);
        $retorno = $this->__sendSOAPNFSe($urlservico, $datos, $metodo, $myCertificado->pry_key, $myCertificado->cert);
        if (!empty($retorno)) {
            $error = '';
            $retorno = self::limparRetornoSOAP($retorno);
            if (!$this->verificarProcessamentoOk($retorno, $error)) {
                $error = "error $error"; // error entre cliente y servidor (ver de que tipo puede suceder)
                return false;
            } else {
                $doc = new DOMDocument();
                $doc->formatOutput = FALSE;
                $doc->preserveWhiteSpace = FALSE;
                $doc->loadXML($retorno, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
                $suceso = $doc->getElementsByTagName("Sucesso")->item(0)->nodeValue;
                if ($suceso == "false") {
                    $codigo = $doc->getElementsByTagName("Codigo")->item(0)->nodeValue;
                    if ($codigo = "E79") { // la factura ya se habia cancelado
                        $fechaHora = $doc->getElementsByTagName("DataHora")->item(0)->nodeValue;
                        $myFactura->setEstado(Vfacturas::getEstadoInhabilitado());
                        $mySeguimiento = new Vseguimiento_ginfes($conexion);
                        $mySeguimiento->cod_factura = $myFactura->getCodigo();
                        $mySeguimiento->cod_filial = $conexion->database;
                        $mySeguimiento->fecha_envio = str_replace("T", " ", $fechaHora);
                        $mySeguimiento->estado = Vfacturas::getEstadoInhabilitado();
                        $mySeguimiento->guardarSeguimiento_ginfes();
                        return true;
                    } else {
                        $codigo = $doc->getElementsByTagName("Codigo")->item(0)->nodeValue;
                        $mensaje = $doc->getElementsByTagName("Mensagem")->item(0)->nodeValue;
                        $correccion = $doc->getElementsByTagName("Correcao")->item(0)->nodeValue;
                        $error = "[codigo: $codigo] $mensaje\nCorreccion: $correccion";
                        return false;
                    }
                } else {
                    $fechaHora = $doc->getElementsByTagName("DataHora")->item(0)->nodeValue;
                    $myFactura->setEstado(Vfacturas::getEstadoInhabilitado());
                    $mySeguimiento = new Vseguimiento_ginfes($conexion);
                    $mySeguimiento->cod_factura = $myFactura->getCodigo();
                    $mySeguimiento->cod_filial = $conexion->database;
                    $mySeguimiento->fecha_envio = str_replace("T", " ", $fechaHora);
                    $mySeguimiento->estado = Vfacturas::getEstadoInhabilitado();
                    $mySeguimiento->guardarSeguimiento_ginfes();
                    return true;
                }
            }
        } else {
            $error = "No pudo establecerse comunicacion entre servicios (volver a intentar)";
            return false;
        }
    }

    /* STATIC FUNCTIONS */
}
