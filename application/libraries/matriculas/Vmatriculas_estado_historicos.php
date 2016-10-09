<?php

/**
 * Class Vmatriculas_estado_historicos
 *
 * Class  Vmatriculas_estado_historicos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vmatriculas_estado_historicos extends Tmatriculas_estado_historicos {

      private static $motivos = array(
        array("id" => 1, "motivo" => 'abandono_de_curso', "visible" => true),
        array("id" => 2, "motivo" => 'falta_de_pago', "visible" => true),
        array("id" => 3, "motivo" => 'alumno_inhabilitado', "visible" => false),
        array("id" => 4, "motivo" => 'adeuda_mas_tres_meses', "visible" => false),
        array("id" => 5, "motivo" => 'aprobadas_todas_materias', "visible" => false),
        array("id" => 6, "motivo" => 'cursando_comision_con_ciclo_vencido', "visible" => false),
        //Ticket -4710- mmori - agrego motivos para dar de baja una matricula 
        array("id" => 7, "motivo" =>'dificultad', "visible" => true),
        array("id" => 8, "motivo" =>'financiero', "visible" => true),
        array("id" => 9, "motivo" =>'insatisfaccion', "visible" => true),
        array("id" => 10, "motivo" =>'rematriculacion', "visible" => true),
        array("id" => 11, "motivo" =>'cambio_de_escuela', "visible" => true),
        array("id" => 12, "motivo" =>'cambio_de_institucion', "visible" => true),
        array("id" => 13, "motivo" =>'laboral', "visible" => true),
        array("id" => 14, "motivo" =>'enfermedad', "visible" => true),
        array("id" => 15, "motivo" =>'embarazo', "visible" => true),
        array("id" => 16, "motivo" =>'cambio_misma_escuela', "visible" => true),
        array("id" => 17, "motivo" =>'no_se_ abrio_curso', "visible" => true),
        array("id" => 18, "motivo" =>'desempleo', "visible" => true),
        array("id" => 19, "motivo" =>'horarios', "visible" => true),
        array("id" => 20, "motivo" =>'Motivos personales', "visible" => true),
        array("id" => 21, "motivo" =>'sin_motivo', "visible" => true),
        array("id" => 22, "motivo" =>'alumno_no_empezo_curso', "visible" => true),
        array("id" => 23, "motivo" =>'no_era_lo_esperado', "visible" => true),
        array("id" => 24, "motivo" =>'mudanza_de_ciudad', "visible" => true),
        );

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    function getmotivos($index = false, $visible = true, $id = null) { // esta funcion deberia ser estática
        if ($id != null){
            $arrMotivos = self::$motivos;
            foreach ($arrMotivos as $motivo){
                if ($motivo['id'] == $id){
                    return $motivo['motivo'];
                }
            }
        } else {        
            $retorno = array();
            if ($index !== false) {
                return self :: $motivos[$index];
            }
            if ($visible) {
                foreach (self ::$motivos as $value) {
                    if ($value['visible']) {
                        $retorno[] = $value;
                    }
                }
                return $retorno;
            }
        }
    }
    
    /**
     * Retorna la matricula perteneciente al registro de historico
     * 
     * @return \Vmatriculas|null
     */
    function getMatricula(){
        $this->oConnection->select("matriculas_periodos.cod_matricula");
        $this->oConnection->from("matriculas_estado_historicos");
        $this->oConnection->join("matriculas_periodos", "matriculas_periodos.codigo = matriculas_estado_historicos.cod_matricula_periodo");
        $this->oConnection->where("matriculas_estado_historicos.codigo", $this->codigo);
        $query = $this->oConnection->get();
        $arrCodigo = $query->result_array();
        if (isset($arrCodigo[0]['cod_matricula']) && $arrCodigo[0]['cod_matricula'] > 0){
            $myMatricula = new Vmatriculas($this->oConnection, $arrCodigo[0]['cod_matricula']);
            return $myMatricula;
        } else {
            return null;
        }
    }
    
    /**
     * retorna la descripcion del motivo
     * 
     * @return string
     */
    function getMotivoNombre(){
        return $this->getmotivos($this->motivo);
    }
    
}

