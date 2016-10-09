<?php

/**
 * Class Vctacte_imputaciones
 *
 * Class  Vctacte_imputaciones maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vctacte_imputaciones extends Tctacte_imputaciones {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

//    public function cambioEstado($estado, $usuario = null) {
//        $conexion = $this->oConnection;
//        $this->estado = $estado;
//        $resp = $this->guardarCtacte_imputaciones();
//
//        switch ($estado) {
//            case 'confirmado':
////se confirma la imputacion se actualiza la ctacte
//                $ctacte = new Vctacte($this->oConnection, $this->cod_ctacte);
//                $ctacte->pagado = $ctacte->pagado + $this->valor;
//                $resp = $resp && $ctacte->guardarCtacte();
//
//                break;
//            //cuando se anula una imputacion actualiza la ctacte
//            case 'anulado':
//                $ctacte = new Vctacte($this->oConnection, $this->cod_ctacte);
//                $ctacte->pagado = $ctacte->pagado - $this->valor;
//                $resp = $ctacte->guardarCtacte();
//                $condiciones = array(
//                    'cod_cobro' => $this->cod_cobro
//                );
//                $ctacteImputaciones = Vctacte_imputaciones::listarCtacte_imputaciones($conexion, $condiciones);
//                $arrayInputacionesHistorico = array();
//                foreach ($ctacteImputaciones as $value) {
//                    $arrayInputacionesHistorico['cod_imputacion'] = $value['codigo'];
//                    $arrayInputacionesHistorico['baja'] = 1;
//                    $arrayInputacionesHistorico['fecha_hora'] = date('Y-m-d H:i:s');
//                    $arrayInputacionesHistorico['cod_usuario'] = $usuario;
//
//                    $imputacionEstadoHistorico = new Vimputaciones_estado_historico($conexion);
//                    $resp = $resp && $imputacionEstadoHistorico->setImputaciones_estado_historico($arrayInputacionesHistorico);
//                    $resp = $resp && $imputacionEstadoHistorico->guardarImputaciones_estado_historico();
//                }
//
//                break;
//        }
//        return $resp;
//    }

    public function confirmar($usuario = null, $fechareal = null) {
        $conexion = $this->oConnection;
        $this->estado = 'confirmado';
        $resp = $this->_guardar($usuario);

        $ctacte = new Vctacte($this->oConnection, $this->cod_ctacte);
        $ctacte->pagado = $ctacte->pagado + $this->valor;
        $resp = $resp && $ctacte->guardarCtacte();
        if ($ctacte->cod_concepto == 1 || $ctacte->cod_concepto == 5){
            $myMatricula = new Vmatriculas($conexion, $ctacte->concepto);
            if ($myMatricula->estado == Vmatriculas::getEstadoPrematricula()){
                $myMatricula->estado = Vmatriculas::getEstadoHabilitada();
                $myMatricula->guardarMatriculas();
            }
        }

        $historico = new Vimputaciones_estado_historico($conexion);
        $historico->cod_imputacion = $this->codigo;
        $historico->baja = 0;
        $historico->fecha_hora = date('Y-m-d H:i:s');
        $historico->cod_usuario = $usuario;
        $historico->guardarImputaciones_estado_historico();

        $myCtacte = $ctacte->getObjectMora();
        
        if ($fechareal != null && $ctacte->cod_concepto != '3') 
        {
            $ctacte->eliminarMoras($fechareal);
            $ctacte->recalcularImporteMora();
            if($myCtacte->habilitado == 1)
            {
                $ctacte->aplicarMora();
            }
        }
            
        return $resp;
    }

    public function anular($usuario = null) {
        $conexion = $this->oConnection;
        $calcularmora = false;

        if ($this->estado == 'confirmado') {
            $ctacte = new Vctacte($this->oConnection, $this->cod_ctacte);
            $ctacte->pagado = $ctacte->pagado - $this->valor;
            $resp = $ctacte->guardarCtacte();
            if ($ctacte->cod_concepto != '3') {
                $calcularmora = true;
            }
        }
        $this->estado = 'anulado';
        $resp = $this->_guardar($usuario);

        if ($calcularmora) {
            $ctacte->eliminarMoras();
            $ctacte->aplicarMora(null, true);
        }

        $historico = new Vimputaciones_estado_historico($conexion);
        $historico->cod_imputacion = $this->codigo;
        $historico->baja = 1;
        $historico->fecha_hora = date('Y-m-d H:i:s');
        $historico->cod_usuario = $usuario;
        $historico->guardarImputaciones_estado_historico();

        return $resp;
    }

}
