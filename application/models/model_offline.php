<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_offline extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();

        $this->codigo_filial = $arg["codigo_filial"];
    }
    
    public function actualizar_Tabla_SincronizarOffline_Bancos($conexion)
    {
        $conexion->trans_begin();
        
    /*---------------------------------------------------------
    * SELECT'S
    ---------------------------------------------------------*/
        
        $arrBancos = Vbancos::listarBancos($conexion);
        
    /*---------------------------------------------------------
    * INSERT'S
    ---------------------------------------------------------*/
        foreach ($arrBancos as $banco) 
        {
            $arr = array('nombre_tabla' => 'bancos', 'id_registro' => $banco['codigo']);

            $conexion->insert('bancos.offline_sincronizacion', $arr);
        }
    
        $estadotran = $conexion->trans_status();
            
        
        if ($estadotran === FALSE)
        {

            $conexion->trans_rollback();

            return false;
        } 
        else 
        {

            $conexion->trans_commit();

            return true;
        }
        
    } 
    
    public function actualizar_Tabla_SincronizarOffline_Tarjetas($conexion)
    {
        $filial = $this->session->userdata('filial');
        $conexion->trans_begin();
        
    /*---------------------------------------------------------
    * SELECT'S
    ---------------------------------------------------------*/
        
        $arrConf = array('pais' => $filial['pais']);
        $this->load->model("Model_tipos_tarjetas", "", false, $arrConf);
        $tarjetas = $this->Model_tipos_tarjetas->getTipos();
        
    /*---------------------------------------------------------
    * INSERT'S
    ---------------------------------------------------------*/
        foreach ($tarjetas as $tarjeta)
        {

            $arr = array('nombre_tabla' => 'tipos_tarjeta', 'id_registro' => $tarjeta['codigo']);

            $conexion->insert('tarjetas.offline_sincronizacion', $arr);
        }
    
        $estadotran = $conexion->trans_status();
            
        
        if ($estadotran === FALSE)
        {

            $conexion->trans_rollback();

            return false;
        } 
        else 
        {

            $conexion->trans_commit();

            return true;
        }
        
    } 
    
    public function actualizar_Tabla_SincronizarOffline($conexion) 
    {

    $conexion->trans_begin();

    $filial = $this->session->userdata('filial');

    /*---------------------------------------------------------
     * SELECT'S
     ---------------------------------------------------------*/


    //ALUMNOS
            $conexion->select('alumnos.codigo');
            $conexion->from('alumnos');
            $query = $conexion->get();
            $arrAlumnos = $query->result_array();

   
    //TARJETAS
            $arrConf = array('pais' => $filial['pais']);
            $this->load->model("Model_tipos_tarjetas", "", false, $arrConf);
            $tarjetas = $this->Model_tipos_tarjetas->getTipos();

    //CAJAS
            $cajas = Vcaja::listarCaja($conexion);
            
    // TERMINALES TARJETAS
            
            $terminalesHabilitadas = Vpos_terminales::getTerminales($conexion,true);
            

    
            
            
    /*---------------------------------------------------------
     * INSERT'S
    ---------------------------------------------------------*/



    //alumnos
            foreach ($arrAlumnos as $alumno) 
            {
                $arr = array('nombre_tabla' => 'alumnos', 'id_registro' => $alumno['codigo']);

                $conexion->insert('offline_sincronizacion', $arr);
            }


    //tarjetas
            foreach ($tarjetas as $tarjeta)
            {

                $arr = array('nombre_tabla' => 'tipos_tarjeta', 'id_registro' => $tarjeta['codigo']);

                $conexion->insert('offline_sincronizacion', $arr);
            }
     //caja       
            foreach($cajas as $caja)
            {

                $arr = array('nombre_tabla' => 'caja', 'id_registro' => $caja['codigo']);

                $conexion->insert('offline_sincronizacion', $arr);
            }

            
    // terminales tarjetas
            
            foreach ($terminalesHabilitadas as $terminal)
            {
                $arr = array('nombre_tabla' =>'pos_terminales','id_registro' =>$terminal['codigo']);

                $conexion->insert('offline_sincronizacion', $arr);
            }
            
            $estadotran = $conexion->trans_status();
            
    

            if ($estadotran === FALSE)
            {

                $conexion->trans_rollback();

                return false;
            } 
            else 
            {

                $conexion->trans_commit();

                return true;
            }
    }

    public function comprobarRegistros_server()
    {
        /*---------------------------------------------------- 
        *VERIFICA QUE LA TABLA EN LA FILIAL NO ESTE VACIA       
        -----------------------------------------------------*/
        
        $conexion = $this->load->database($this->codigo_filial, true);
        
        $conexion->select('COUNT(id) AS actualizaciones');
        $conexion->from('offline_sincronizacion');
        $query = $conexion->get();
        $actualizaciones = $query->result_array();
        $retorno = true;
        
        if ($actualizaciones[0]['actualizaciones'] == 0) {// si la tabla esta vacia
           $retorno = $this->actualizar_Tabla_SincronizarOffline($conexion);
        }
        
        
        
        
        /*----------------------------------------------------- 
        *VERIFICA QUE LA TABLA EN LA BASE BANCOS NO ESTE VACIA        
        ------------------------------------------------------*/
        $conexion->resetear();
        //$conexion = $this->load->database('bancos', true);
        $conexion->select('COUNT(id) AS actualizaciones');
        $conexion->from('bancos.offline_sincronizacion');
        $query = $conexion->get();
        $actualizacionesBanco= $query->result_array();
        
        if ($actualizacionesBanco[0]['actualizaciones'] == 0) {// si la tabla esta vacia
           $retorno = $this->actualizar_Tabla_SincronizarOffline_Bancos($conexion);
        }
        
        
        
         /*----------------------------------------------------- 
        *VERIFICA QUE LA TABLA EN LA BASE TARJETAS NO ESTE VACIA        
        ------------------------------------------------------*/
        $conexion->resetear();
        //$conexion = $this->load->database('tarjetas', true);
        $conexion->select('COUNT(id) AS actualizaciones');
        $conexion->from('tarjetas.offline_sincronizacion');
        $query = $conexion->get();
        $actualizacionesBanco= $query->result_array();
        
        if ($actualizacionesBanco[0]['actualizaciones'] == 0) {// si la tabla esta vacia
           $retorno = $this->actualizar_Tabla_SincronizarOffline_Tarjetas($conexion);
        }
        
        return $retorno;
    }

    public function getRegistrosMayorQue($ultimoId = null ,$ultimoIdBancos = null , $ultimoIdTarjetas = null)
    {
        $this->load->helper('cuentacorriente');
        $this->load->helper('filial');
        $this->load->helper('alumnos');
        $this->load->helper('formatearfecha');

        $respuesta = array('alumnos' => array(), 'cobros' => array(), 'bancos' => array(), "tipos_tarjeta" => array(),"cajas" => array(), 'ultimoId' => array(),'terminales_tarjetas'=>array());
        
        /*---------------------------
         * CONEXIONES
         ----------------------------*/
        $conexion = $this->load->database($this->codigo_filial, true);
       //$conexionBancos = $this->load->database('bancos', true);
        //$conexionTarjetas = $this->load->database('tarjetas',true);
        
        
        /*---------------------------
         * REGISTROS A SINCRONIZAR
         ----------------------------*/
        //$registros = Voffline_sincronizacion::getRegistrosSincronizar($conexion, $ultimoId);
        $registrosFilial = Voffline_sincronizacion::getRegistrosSincronizarTest($conexion, $ultimoId);
        $registrosBancos = Voffline_sincronizacion::getRegistrosSincronizarTest($conexion,$ultimoIdBancos,50,'bancos');
        $registrosTarjetas = Voffline_sincronizacion::getRegistrosSincronizarTest($conexion,$ultimoIdTarjetas,50,'tarjetas');
        

        $registros = array_merge($registrosFilial,$registrosBancos,$registrosTarjetas);
        

        
        $ultimoregistro = end($registrosFilial);
        
        $ultimoIdConsulta = count($registrosFilial) == 0 ? $ultimoId : $ultimoregistro["id"];
        
        $ultimoIdServer = count($registrosFilial) == 0 ? $ultimoId : $ultimoregistro["total_registros"];
        
        
        
        $ultimoregistroBancos = end($registrosBancos);
        
        $ultimoIdConsultaBancos = count($registrosBancos) == 0 ? $ultimoIdBancos : $ultimoregistroBancos["id"];
        
        $ultimoIdServerBancos = count($registrosBancos) == 0 ? $ultimoIdBancos : $ultimoregistroBancos["total_registros"];
        
        
        
        $ultimoregistroTarjetas = end($registrosTarjetas);
        
        $ultimoIdConsultaTarjetas = count($registrosTarjetas) == 0 ? $ultimoIdTarjetas : $ultimoregistroTarjetas["id"];
        
        $ultimoIdServerTarjetas = count($registrosTarjetas) == 0 ? $ultimoIdTarjetas : $ultimoregistroTarjetas["total_registros"];
        
        
        $arraySinc = array();
        
        
        foreach ($registros as $registro)
        {
            $arraySinc[$registro['nombre_tabla']][] = $registro['id_registro'];
        }

        foreach ($arraySinc as $key => $tablaSinc) 
        {

            switch ($key)
            {

                case "alumnos" :

                    $i = 0;
                   
                    $conexion->where_in("codigo", $tablaSinc);
               
                    $alumnosSincro = Valumnos::listarAlumnos($conexion);


                    $conexion->resetear();
                    //$alumnosCtacteSincro = $ctacte = Vctacte::getCtaCte($conexion, true, null, null, null, null, false, array(array('campo' => 'cod_alumno', 'valores' => $tablaSinc)));
                    //$alumnosCtacteSincro = $ctacte = Vctacte::getCtaCteImputar($conexion,null,null,null,null,null,false, null,false,$alumnoForzado);

                    
                    $alumnosCtacteSincro = Vctacte::getCtaCteCobrar($conexion,null, null,null,$tablaSinc);
                    
                    
                    formatearCtaCte($conexion, $alumnosCtacteSincro);
                    
                   
                    
                    
                    foreach ($alumnosSincro as $rowAlumnos)
                    {
                        $respuesta['alumnos'][$i] = array("id" => $rowAlumnos["codigo"], "nombre" => formatearNombreApellido($rowAlumnos["nombre"],$rowAlumnos["apellido"]), "ctacte" => $this->search($alumnosCtacteSincro, 'cod_alumno', $rowAlumnos["codigo"]));
                        $i++;
                    }

                    break;
                
                case "cobros":
                    $j = 0;
                    //$conexion->where_in("codigo", $tablaSinc);
                    //$cobrosSincro = Vcobros::listarCobros($conexion);
                    //$conexion->where_in("cod_cobro", $tablaSinc);
                    //$ctacte_imputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion);

//                    foreach ($cobrosSincro as $rowCobro) {
//                        $respuesta['cobros'][$j] = array('codigo' => $rowCobro['codigo'], 'cod_alumno' => $rowCobro['cod_alumno'], 'importe' => formatearImporte($rowCobro['importe']), 'saldo' => formatearImporte($this->getSaldo($rowCobro, $ctacte_imputaciones)), 'medio_pago' => $rowCobro['medio_pago'], 'fecha' => formatearFecha_pais($rowCobro['fechaalta']), 'estado' => $rowCobro['estado']);
//                        $j++;
//                    }

                    break;
               
                case "bancos" :
                    
                    $f = 0;
                    
                    $conexion->where_in("codigo", $tablaSinc);
                    $bancosSincro = Vbancos::listarBancos($conexion);
                    
                    foreach ($bancosSincro as $rowBanco)
                    {
                        $respuesta['bancos'][$f] = array('codigo' => $rowBanco['codigo'], 'nombre' => $rowBanco['nombre']);
                        $f++;
                    }
                
                    break;
                
                case "tipos_tarjeta":
                    $w = 0;
                    
                    $f = 0;
                    
                    $conexion->where_in("codigo", $tablaSinc);
                    
                    $tarjetasSincro = Vtipos_tarjetas::listarTipos_tarjetas($conexion);

                    foreach ($tarjetasSincro as $rowTarjeta)
                    {
                        $respuesta['tipos_tarjeta'][$f] = array('codigo' => $rowTarjeta['codigo'], 'nombre' => $rowTarjeta['nombre']);
                        $f++;
                    }
                    
                    $w++;
                    
                    break;
                
                case "caja":
                
                    $i=0;
                    
                    $con = $conexion;

                    $conexion->where_in("codigo", $tablaSinc);
                    
                    $cajas = Vcaja::listarCaja($conexion);



                foreach ($cajas as $caja)
                {
                    
                    $respuesta['cajas'][$i] = array('codigo' => $caja['codigo'], 'nombre' => $caja['nombre'],'estado'=>$caja['estado'],'desactivada'=>$caja['desactivada'],'medios'=>array());
                    
                    $getMedios = Vcaja::getCajasMedios($con,null, $caja['codigo']);

                    $respuesta['cajas'][$i]['medios'] = json_encode($getMedios);

                    //$respuesta['cajas'][$i]['medios'] = Vcaja::getCajasMedios($con,null, $caja['codigo']);
                    $i++;
                }

                break;
                
                case 'pos_terminales':
                     
                    $conexion->where_in("codigo", $tablaSinc);
                    
                    $terminalesHabilitadas = Vpos_terminales::getTerminales($conexion,true);
                    
                    $conexion->resetear();
                    
                    foreach ($terminalesHabilitadas as $key => $terminal) 
                    {
                        $myTerminal = new Vpos_terminales($conexion,$terminal['codigo']);

                        $tarjetasDeTerminal = $myTerminal->getTarjetas();

                        $terminal['tarjetas'] = json_encode($tarjetasDeTerminal);
                        $terminal['cod_operador'] = $myTerminal->getCodigoOperador();
                       

                        $respuesta['terminales_tarjetas'][] = $terminal ;
                    }
                    
                    
                    
                    break;
                
                default :

            }
        
            
        }
        

        $respuesta['ultimoId'] = $ultimoIdConsulta;
        $respuesta['ultimoId_server'] = $ultimoIdServer;
        
        $respuesta['ultimoId_bancos'] = $ultimoIdConsultaBancos;
        $respuesta['ultimoId_server_bancos'] = $ultimoIdServerBancos;
        
        $respuesta['ultimoId_Tarjetas'] = $ultimoIdConsultaTarjetas;
        $respuesta['ultimoId_server_Tarjetas'] = $ultimoIdServerTarjetas;
                
        return $respuesta;
    }

    private function getSaldo($rowCobro, $ctacte_imputaciones)
    {

        $totCtaCteImuputacion = array();
        foreach ($ctacte_imputaciones as $ctacte) {

            if ($ctacte['cod_cobro'] == $rowCobro['codigo']) {

                $totCtaCteImuputacion[] = $ctacte['valor'];
            }
        }

        $saldo = (int) $rowCobro['importe'] - (int) array_sum($totCtaCteImuputacion);

//    echo '<pre>'; 
//    print_r($rowCobro);
//    echo '</pre>';



        return $saldo;
    }

    private function search($array, $key, $value)
    {

        $results = array();
        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
                
            }
            
            foreach ($array as $subarray) {
                
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }
 
        return $results;
    }
    
}
