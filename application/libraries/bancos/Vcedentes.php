<?php

class Vcedentes{
    
    public $cpf_cnpj;
    public $razon_social;
    public $cuenta_bancaria;
    public $convenio;
    public $numeroSequencial;
    public $numeroBordero;
    public $digitoCedente;
    public $carteira;
    public $endereco;
    public $variacao_carteira;
    public $Objbanco ;
    public $objCuenta;
    protected $oConnection;
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigoBanco, $codigoCuenta, $convenio = null, $carteira = null) {
        $this->oConnection = $conexion;
        if ($convenio === null && $carteira === null){
            throw new Exception('Debe indicar convenio o cartera');
        } else {
            $myBanco = new Vbancos($conexion, $codigoBanco);
            
            $myBancoCuenta = $myBanco->getCuentaBanco($codigoCuenta);
            $myFacturante = new Vfacturantes($conexion, $myBancoCuenta->cod_razon_social);
            $myRazonSocial = new Vrazones_sociales($conexion, $myFacturante->cod_razon_social);
            $arrBoleto = $myBancoCuenta->getBoletoBancario($convenio, $carteira);
            if (count($arrBoleto) == 0){
                throw new Exception('Banco, cuenta, convenio o carteira incorrectos');
            }            
            $this->cpf_cnpj = $myRazonSocial->documento;
            $this->razon_social = $myRazonSocial->razon_social;
            $this->cuenta_bancaria = $myBancoCuenta;            
            $this->convenio = $arrBoleto[0]['convenio'];
            $this->numeroSequencial = $arrBoleto[0]['numero_secuencia'];
            $this->carteira = $arrBoleto[0]['carteira'];
            $this->endereco = $myRazonSocial->getDireccionFormal();
            $this->variacao_carteira = $arrBoleto[0]['variacao_carteira'];
            $this->Objbanco = $myBanco;

            $this->objCuenta = $myBancoCuenta;
            
        }
    }
    
    
    

   
}