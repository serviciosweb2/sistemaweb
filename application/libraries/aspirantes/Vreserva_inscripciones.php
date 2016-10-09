<?php

/**
* Class Vreserva_inscripciones
*
*Class  Vreserva_inscripciones maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vreserva_inscripciones extends Treserva_inscripciones{

    static private $estadoPendiente = "pendiente";
    static private $estadoConfirmada = "confirmado";
    
    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }
    
    /* La siguiente function es utilizada desde un web services y tiene como objetivo mantener la sincronizacion de la tabla entre sistemas */
    public function guardadoForzado($codigoConsulta){
        $arrTemp = $this->_getArrayDeObjeto();
        $arrTemp['id'] = $codigoConsulta;
        $this->oConnection->trans_begin();
        $this->oConnection->where("id", $codigoConsulta);
        $this->oConnection->delete($this->nombreTabla);
        $this->oConnection->insert($this->nombreTabla, $arrTemp);
        if ($this->oConnection->trans_status()){
            $this->oConnection->trans_commit();
            $this->id = $arrTemp['id'];
            return true;
        } else {
            $this->oConnection->trans_rolback();
            return false;
        }
    }
    
    
    public function setConfirmacionEnviada(){
        $this->oConnection->where("id", $this->id);
        return $this->oConnection->update($this->nombreTabla, array("confirmacion_enviada" => 1));
    }


    static function listarReservarInscripcionesDataTable(CI_DB_mysqli_driver $conexion, $arrCondindicioneslike = null, $arrLimit = null, $arrSort = null, $contar = false, $codigoFilial = null){
        $nombreCurso = 'cursos.nombre_'.get_idioma();
        $conexion->select('inscripcionesweb.reserva_inscripciones.id');
        $conexion->select('inscripcionesweb.reserva_inscripciones.nombre');
        $conexion->select('inscripcionesweb.reserva_inscripciones.email');
        $conexion->select('inscripcionesweb.reserva_inscripciones.telefono');
        $conexion->select('inscripcionesweb.reserva_inscripciones.fecha');
        $conexion->select('comisiones.nombre as nombre_comision');
        $conexion->select("general.$nombreCurso as nombre_curso");
        $conexion->select('inscripcionesweb.reserva_inscripciones.estado');
        $conexion->from('inscripcionesweb.reserva_inscripciones');
        $conexion->join('comisiones','comisiones.codigo = inscripcionesweb.reserva_inscripciones.id_comision');
        $conexion->join('general.cursos','general.cursos.codigo = inscripcionesweb.reserva_inscripciones.id_curso');
        if ($codigoFilial != null) $conexion->where("id_filial", $codigoFilial);
        $arrTemp = array();
         if ($arrCondindicioneslike != null) {
            foreach ($arrCondindicioneslike as $key => $value) {
                $arrTemp[] = "$key LIKE '%$value%'";
            }
        }
        if (count($arrTemp) > 0){
            $where = "(".implode(" OR ", $arrTemp).")";
            $conexion->where($where);
        }
        
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }

        if ($arrSort > 0) {
            $conexion->order_by($arrSort["0"], $arrSort["1"]);
        }

        if ($contar) {
            return $conexion->count_all_results();
        } else {
            $query = $conexion->get();
            return $query->result_array();
        }
    }
    
    static function getInformacionReservaInscripcion(CI_DB_mysqli_driver $conexion,$cod_reserva,$cod_plan_pago){
        $conexion->select('SUM(planes_financiacion.valor)');
        $conexion->from('planes_financiacion');
        $conexion->where('planes_financiacion.codigo_financiacion',1);
        $conexion->where('planes_financiacion.codigo_plan',1);
        $conexion->where('planes_financiacion.codigo_concepto',5);
        $subquery = $conexion->return_query();
        $conexion->resetear();
        
        $conexion->select('MAX(planes_financiacion.nro_cuota)');
        $conexion->from('planes_financiacion');
        $conexion->where('planes_financiacion.codigo_plan',$cod_plan_pago);
        $conexion->where('planes_financiacion.codigo_concepto',1);
        $subquery2 = $conexion->return_query();
        $conexion->resetear();
        
         $conexion->select('planes_financiacion.valor');
        $conexion->from('planes_financiacion');
        $conexion->where('planes_financiacion.codigo_plan',$cod_plan_pago);
        $conexion->where('planes_financiacion.codigo_concepto',1);
        $conexion->order_by('planes_financiacion.nro_cuota', 'DESC');
        $conexion->limit(1, 0);
        $subquery3 = $conexion->return_query();
        $conexion->resetear();
        
        $nombreCurso = 'cursos.nombre_'.get_idioma();
        $conexion->select('inscripcionesweb.reserva_inscripciones.id');
        $conexion->select('comisiones.nombre as nombre_comision');
        $conexion->select("general.$nombreCurso as nombre_curso");
        $conexion->select('planes_pago.nombre as plan_pago');
        $conexion->select("($subquery) as valormatricula", false);
        $conexion->select("($subquery2) as nro_cuotas", false);
        $conexion->select("($subquery3) as valor_cuota", false);
        $conexion->select('planes_pago.fechavigencia');
        $conexion->from('inscripcionesweb.reserva_inscripciones');
        $conexion->join('comisiones','comisiones.codigo = inscripcionesweb.reserva_inscripciones.id_comision','left');
        $conexion->join('general.cursos','general.cursos.codigo = inscripcionesweb.reserva_inscripciones.id_curso','left');
        $conexion->join('planes_pago','planes_pago.codigo = inscripcionesweb.reserva_inscripciones.id_plan','left');
        $conexion->where('inscripcionesweb.reserva_inscripciones.id',  $cod_reserva);
        $query = $conexion->get();
        return $query->result_array();
    }

    static public function reserva_ya_registrada(CI_DB_mysqli_driver $conexion, $codFilial, $codComision, $email){
        $conexion->select("inscripcionesweb.reserva_inscripciones.id");
        $conexion->from("inscripcionesweb.reserva_inscripciones");
        $conexion->where("id_filial", $codFilial);
        $conexion->where("id_comision", $codComision);
        $conexion->where("email", $email);
        $query = $conexion->get();
        $temp = $query->result_array();
        return count($temp) > 0;
    }
    
    static public function getEstadoPendiente(){
        return self::$estadoPendiente;
    }
    
    static public function getEstadoConfirmado(){
        return self::$estadoConfirmada;
    }
    
}

?>