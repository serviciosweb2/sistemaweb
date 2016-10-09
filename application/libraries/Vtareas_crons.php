<?php

/**
 * Class Vtareas_crons
 *
 * Class  Vtareas_crons maneja todos los aspectos de tareas_crons
 *
 * @package  SistemaIGA
 * @subpackage tareas_crons
 * @author   vane
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vtareas_crons extends Ttareas_crons {

    var $estadopendiente = 'pendiente';
    var $estadocompleto = 'completo';
    var $estadoejecucion = 'en_ejecucion';
    var $estadocancelado = 'cancelado';
    var $estadodetenido = 'detenido';
    var $estadoerror = 'error';

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function getTareasCronsEjecutar(CI_DB_mysqli_driver $conexion, $codfilial) {
        $conexion->select('*');
        $conexion->from('general.tareas_crons');
        $conexion->where('cod_filial', $codfilial);
        $conexion->where('estado', 'pendiente');
        $conexion->where_not_in('nombre', '(select general.tareas_crons.nombre from general.tareas_crons where general.tareas_crons.estado = "en_ejecucion" and general.tareas_crons.cod_filial = ' . $codfilial . ')');
        $conexion->group_by('nombre');
        $conexion->order_by('codigo', 'asc');
        $query = $conexion->get();
        return $query->result_array();
    }

    function setEnEjecucion() {
        $this->estado = $this->estadoejecucion;
        $this->completado = 0;
        $this->guardarTareas_crons();
    }

    function setError() {
        $this->estado = $this->estadoerror;
        $this->completado = 0;
        $this->guardarTareas_crons();
    }

    function setPendiente(){
        $this->estado = $this->estadopendiente;
    }
    
    function setCompleta() {
        
        
        
           echo "Completa ;; " . $this->getCodigo();
        $this->estado = $this->estadocompleto;
        $this->completado = 100;
        
       
        
        
        $this->guardarTareas_crons();
    }

    function guardar($nombre, $parametros = null, $codfilial = null) {
        $this->nombre = $nombre;
        if ($parametros != null) {
            $this->parametros = json_encode($parametros);
        }
        $this->fecha_hora = date('Y-m-d H:i:s');
        $this->estado = $this->estadopendiente;
        $this->completado = 0;
        $this->cod_filial = $codfilial;

        $condiciones = array('nombre' => $this->nombre, 'estado' => $this->estado, 'parametros' => $this->parametros, 'cod_filial' => $this->cod_filial);
        $tareaigual = Vtareas_crons::listarTareas_crons($this->oConnection, $condiciones);
        if (count($tareaigual) < 1) {//no guarda repetidas
            return $this->guardarTareas_crons();
        } else {
            return false;
        }
    }

}
