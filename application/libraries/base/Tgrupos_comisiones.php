<?php

/**
 * Created by PhpStorm.
 * User: damian
 * Date: 24/08/16
 * Time: 16:18
 */
class Tgrupos_comisiones {
    protected $id_grupo;
    protected $cod_comision;
    protected $existe = false;
    protected $oConnection;
    protected $nombreTabla = "grupos_comisiones";

    function __construct(CI_DB_mysqli_driver $connection, $id_grupo, $cod_comision) {
        $this->oConnection = $connection;

        $arrConstructor = $this->_constructor($id_grupo, $cod_comision);
        if (count($arrConstructor) > 0) {
            $this->id_grupo = $arrConstructor[0]['id_grupo'];
            $this->cod_comision = $arrConstructor[0]['cod_comision'];
            $this->existe = true;
        } else {
            $this->id_grupo = $id_grupo;
            $this->cod_comision = $cod_comision;
            $this->existe = false;
        }
    }

    private function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['id_grupo'] = $this->id_grupo;
        $arrTemp['cod_comision'] = $this->cod_comision;
        return $arrTemp;
    }

    private function _insertar() {
        $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
        $this->existe = true;
    }

    private function _actualizar() {
        $this->oConnection->where(array('id_grupo' => $this->id_grupo, 'cod_comision' => $this->cod_comision));
        $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto());
    }

    private function _constructor($id_grupo, $cod_comision) {
        $query = $this->oConnection->select('*')
            ->from($this->nombreTabla)
            ->where(array(
                'id_grupo' => "$id_grupo",
                'cod_comision' => "$cod_comision"
            ))->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    public function guardarGrupos_comisiones() {
        if (!$this->existe) {
            return $this->_insertar();
        } else {
            return $this->_actualizar();
        }
    }

    static function campos(CI_DB_mysqli_driver $connection) {
        $query = $connection->field_data('grupos_comisiones');
        $arrResp = array();
        foreach ($query as $filed) {
            $arrResp[] = $filed->name;
        }
        return $arrResp;
    }

    static function listar(CI_DB_mysqli_driver $connection, $id_grupo = null, $cod_comision = null) {
        $condiciones = array();
        if ($id_grupo != null && $cod_comision != null) {
            $condiciones["id_grupo"] = "$id_grupo";
            $condiciones["cod_comision"] = "$cod_comision";
        }
        $query = $connection->select('*')
            ->from('grupos_comisiones')
            ->where($condiciones)->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    static function listarGrupos_comisiones(CI_DB_mysqli_driver $connection, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {

        if ($orden != null) {
            $arrOrder = array();
            foreach ($orden as $value) {
                $arrOrder[] = $value['campo'] . " " . $value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $connection->order_by($orderBy);
        }

        if ($limite != null) {
            $connection->limit($limite[1], $limite[0]);
        }

        if ($grupo != null) {
            $connection->group_by($grupo);
        }

        if ($condiciones == null) {
            $query = $connection->select('grupos_comisiones' . '.*', false)->from('grupos_comisiones')->get();
        } else {
            $query = $connection->select('grupos_comisiones' . '.*', false)->from('grupos_comisiones')->where($condiciones)->get();
        }

        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }

        return $arrResp;
    }
}