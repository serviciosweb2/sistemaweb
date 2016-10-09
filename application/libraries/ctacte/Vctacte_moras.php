<?php

/**
 * Class Vctacte_moras
 *
 * Class  Vctacte_moras maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Vctacte_moras extends Tctacte_moras {

    function __construct(CI_DB_mysqli_driver $conexion, $codCtacte, $fecha, $codMora) {
        parent::__construct($conexion, $codCtacte, $fecha, $codMora);
    }

    public function exists() {
        return $this->exists;
    }

    /* STATIC FUNCTION */

    /**
     * devuelve los registros de la tabla ctacte_mora segun los filtros enviados
     * 
     * @param CI_DB_mysqli_driver $conexion     objeto de conexion a la base de datos
     * @param array $condiciones                condiciones de filtro sobre la tabla
     * @param boolean $sumarImporte             si es true solo retorna la suma de los importes sobre los filtros
     * @param array $limite
     * @param array $orden
     * @param array $grupo
     * @param boolean $contar
     * @return mixed                            array registros si sumarImporte es false o integer si se suman los importes
     */
    static function getCtacteMora(CI_DB_mysqli_driver $conexion, array $condiciones = null, $sumarImporte = false, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {
        if ($sumarImporte) {
            $conexion->select("IFNULL(SUM(precio), 0) AS importe", false);
        } else {
            $conexion->select("*");
        }

        $conexion->from("ctacte_moras");

        if ($condiciones != null) {
            $conexion->where($condiciones);
        }

        if ($orden != null) {
            $arrOrder = array();
            foreach ($orden as $value) {
                $arrOrder[] = $value['campo'] . " " . $value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $conexion->order_by($orderBy);
        }

        if ($grupo != null) {
            $conexion->group_by($grupo);
        }

        if ($limite != null) {
            $conexion->limit($limite[1], $limite[0]);
        }

        $query = $conexion->get();

        if ($contar) {
            return $query->num_rows();
        } else {
            $arrResp = $query->result_array();
            if ($sumarImporte) {
                return $arrResp[0]['importe'];
            } else {
                return $arrResp;
            }
        }
    }

    public function eliminar() {
        $this->oConnection->delete("ctacte_moras", array('cod_ctacte' => $this->cod_ctacte, 'fecha' => $this->fecha, 'cod_mora' => $this->cod_mora));
    }

}
