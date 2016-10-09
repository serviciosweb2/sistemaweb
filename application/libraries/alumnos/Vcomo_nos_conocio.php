<?php

/**
* Class Vcomo_nos_conocio
*
*Class  Vcomo_nos_conocio maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vcomo_nos_conocio extends Tcomo_nos_conocio{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    
    /* STATIC FUNCTIONS */
    
    static public function getReporteComoNosConocio(CI_DB_mysqli_driver $conexion, $fechaAlumnosDesde = null, $fechaAspirantesDesde = null){
        $conexion->select("MAX(codigo)");
        $conexion->from("alumnos AL");
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("DATE(fechaalta) AS fechaalta, 
                            comonosconocio,  
                            count(DISTINCT codigo) AS cantidad,
                            'alumnos' AS actor,
                            ($subQuery) AS ultimo_registro", false);
        $conexion->from("alumnos");
        if ($fechaAlumnosDesde != null){
            $conexion->where("DATE(fechaalta) >= '$fechaAlumnosDesde'");
        }
        $conexion->group_by("DATE(fechaalta), comonosconocio");
        $query = $conexion->get();        
        $arrAlumnos = $query->result_array();

        $conexion->resetear();
        $conexion->select("MAX(codigo)");
        $conexion->from("aspirantes AP");
        $subQuery = $conexion->return_query();
        $conexion->resetear();
        $conexion->select("DATE(fechaalta) AS fechaalta, 
                            comonosconocio,  
                            count(DISTINCT codigo) AS cantidad,
                            'aspirantes' AS actor,
                            ($subQuery) AS ultimo_registro", false);
        $conexion->from("aspirantes");
        if ($fechaAspirantesDesde != null){
            $conexion->where("DATE(fechaalta) >= '$fechaAspirantesDesde'");
        }
        $conexion->group_by("DATE(fechaalta), comonosconocio");
        $query = $conexion->get();        
        $arrAspirantes = $query->result_array();
        return array_merge($arrAspirantes, $arrAlumnos);
    }
    
    static public function listarComo_nos_conocio(CI_DB_mysqli_driver $conexion, array $condiciones = null, $filial = null) {
        $conexion->select('cnc_node.*');
        $conexion->select('(count(cnc_parent.codigo) -1) as profundidad', false);
        $conexion->from('como_nos_conocio cnc_node');
        $conexion->from('como_nos_conocio cnc_parent');
        if($filial != null || $condiciones != null) {
            $conexion->join('como_nos_conocio_filiales', 'como_nos_conocio_filiales.id_conocio = cnc_node.codigo');
        }
        if($filial != null) {
            $conexion->where('como_nos_conocio_filiales.id_filial', $filial);
        }
        if($condiciones != null) {
            $conexion->where($condiciones);
        }
        $conexion->where('cnc_node.lft between cnc_parent.lft and cnc_parent.rgt', null, false);
        $conexion->group_by('cnc_node.codigo');
        $conexion->order_by('cnc_node.lft');
        return $conexion->get()->result_array();
    }

    static public function listarComo_nos_conocio_config(CI_DB_mysqli_driver $conexion, array $condiciones = null, $filial = null) {
        $conexion->select('cnc_node.*');
        $conexion->select('(count(cnc_parent.codigo) -1) as profundidad', false);
        $conexion->from('como_nos_conocio cnc_node');
        $conexion->from('como_nos_conocio cnc_parent');
        if($filial != null) {
            $conexion->select('como_nos_conocio_filiales.activo as habilitado');
            $conexion->join('como_nos_conocio_filiales', 'como_nos_conocio_filiales.id_conocio = cnc_node.codigo and `como_nos_conocio_filiales`.`id_filial` =  '.$filial.'', 'left');
        }
        else if($condiciones != null) {
            $conexion->join('como_nos_conocio_filiales', 'como_nos_conocio_filiales.id_conocio = cnc_node.codigo', 'left');
            $conexion->where($condiciones);
        }
        $conexion->where('cnc_node.lft between cnc_parent.lft and cnc_parent.rgt', null, false);
        $conexion->group_by('cnc_node.codigo');
        $conexion->having('profundidad = 2');
        $conexion->order_by('cnc_node.descripcion_'.get_idioma());
        return $conexion->get()->result_array();
    }

    static function listarArbolComoNosConocio(CI_DB_mysqli_driver $conexion) {
        $conexion->select('cnc_node.*');
        $conexion->select('(count(cnc_parent.codigo) -1) as profundidad', false);
        $conexion->from('como_nos_conocio cnc_node');
        $conexion->from('como_nos_conocio cnc_parent');
        $conexion->where('cnc_node.lft between cnc_parent.lft and cnc_parent.rgt', null, false);
        $conexion->group_by('cnc_node.codigo');
        $conexion->order_by('cnc_node.lft');
        return $conexion->get()->result_array();
    }

    public function insert($codigo_padre) {
        $this->oConnection->trans_begin();
        $this->oConnection->query('SELECT @myRight := rgt FROM como_nos_conocio WHERE codigo = '.$codigo_padre.';');
        $this->oConnection->query('UPDATE como_nos_conocio SET rgt = rgt + 2 WHERE rgt >= @myRight;');
        $this->oConnection->query('UPDATE como_nos_conocio SET lft = lft + 2 WHERE lft >= @myRight;');
        $this->oConnection->query('INSERT INTO como_nos_conocio(descripcion_es, descripcion_pt, descripcion_en, activo, lft, rgt) VALUES("'.$this->descripcion_es.'", "'.$this->descripcion_pt.'", "'.$this->descripcion_en.'", '.$this->activo.', @myRight, @myRight + 1)');
        return $this->oConnection->trans_commit();
    }

    public function delete($codigo) {
        $this->oConnection->trans_begin();
        $this->oConnection->query('SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1 FROM como_nos_conocio WHERE codigo = '.$codigo.';');
        $this->oConnection->query('DELETE FROM como_nos_conocio WHERE lft BETWEEN @myLeft AND @myRight;');
        $this->oConnection->query('UPDATE como_nos_conocio SET rgt = rgt - @myWidth WHERE rgt > @myRight;');
        $this->oConnection->query('UPDATE como_nos_conocio SET lft = lft - @myWidth WHERE lft > @myRight;');
        return $this->oConnection->trans_commit();
    }
}