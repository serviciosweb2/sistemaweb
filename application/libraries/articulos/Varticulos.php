<?php

/**
 * Class Varticulos
 *
 * Class  Varticulos maneja todos los aspectos de alumnos
 *
 * @package  SistemaIGA
 * @subpackage Alumnos
 * @author   Ivan berthillod <ivan.sys@gmail.com>
 * @author   Aquiles Gonzalez <sistemas1@iga-la.net>
 * @version  $Revision: 1.1 $
 * @access   private
 */
class Varticulos extends Tarticulos {

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    static function listarArticulosDataTable(CI_DB_mysqli_driver $conexion, $arrCondiciones = null, $arrLimit = null, $arrSort = null, $contar = false) {
        $conexion->select('articulos.*, articulos_categorias.nombre as categoria, general.tipos_unidades_medida.unidad as unidad_medida');
        $conexion->from('articulos');
        $conexion->join('articulos_categorias', 'articulos_categorias.codigo = articulos.cod_categoria','left');
        $conexion->join('general.tipos_unidades_medida', 'general.tipos_unidades_medida.codigo = articulos.cod_unidad_medida','left');

        if (count($arrCondiciones) > 0) {
            foreach ($arrCondiciones as $key => $value) {
                $conexion->or_like($key, $value);
            }
        }
        if ($arrLimit != null) {
            $conexion->limit($arrLimit[1], $arrLimit[0]);
        }
        if ($arrSort != null) {

            $conexion->order_by($arrSort['0'], $arrSort['1']);
        }

        if ($contar) {
            return $conexion->count_all_results();
        } else {

            $query = $conexion->get();

            return $query->result_array();
        }
    }

    function getImpuestos($habilitado = null) {
        $this->oConnection->select('articulos_impuestos.*');
        $this->oConnection->from('articulos_impuestos');
        if ($habilitado != null) {
            $this->oConnection->select("impuestos.nombre");
            $this->oConnection->select("impuestos.valor");
            $this->oConnection->select("impuestos.tipo");
            $this->oConnection->join('impuestos', 'impuestos.codigo = articulos_impuestos.cod_impuesto');
            $this->oConnection->where('impuestos.baja', 0);
        }
        $this->oConnection->where('cod_articulo', $this->codigo);
        $query = $this->oConnection->get();
        return $query->result_array();
    }

    function setImpuestos($codimpuesto) {
       $insert = array('cod_impuesto' => $codimpuesto,
            'cod_articulo' => $this->codigo);
        $this->oConnection->insert('articulos_impuestos', $insert);
    }

    function unSetImpuestos(){
         $delete = array('cod_articulo' => $this->codigo);
        $this->oConnection->delete('articulos_impuestos', $delete);
    }

}
