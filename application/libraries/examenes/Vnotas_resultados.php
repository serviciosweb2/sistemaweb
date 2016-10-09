<?php

/**
* Class Vnotas_resultados
*
*Class  Vnotas_resultados maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vnotas_resultados extends Tnotas_resultados{

    function __construct(CI_DB_mysqli_driver $conexion, $cod_inscripcion, $tipo_resultado) {
        parent::__construct($conexion, $cod_inscripcion, $tipo_resultado);
    }
    
    public function getResultados($cod_inscripcion){
        $this->oConnection->select('notas_resultados.*');
        $this->oConnection->from('notas_resultados');
        $this->oConnection->where('notas_resultados.cod_inscripcion',$cod_inscripcion);
        $query = $this->oConnection->get();
        return $query->num_rows();
    }
    
    static function updateNotasResultados(CI_DB_mysqli_driver $conexion, $cod_inscripcion, $tipo_resultado, $porcentaje_apobado){
        $arrayGuardar = array(
            'porcentaje_aprobado'=>$porcentaje_apobado
        );
        $conexion->where('notas_resultados.cod_inscripcion',$cod_inscripcion);
        $conexion->where('notas_resultados.tipo_resultado',$tipo_resultado);
        return $conexion->update('notas_resultados',$arrayGuardar);
    }
    
    

}

