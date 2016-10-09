<?php

class Tcertificados_plan_filial {

    protected $cod_plan_academico;
    protected $cod_tipo_periodo;
    protected $cod_certificante;
    protected $cod_filial;
    public $estado;
    public $opcional;
    private $existe = false;
    protected $oConnection;
    protected $nombreTabla = 'general.certificados_plan_filial';

    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $connection, $cod_filial, $cod_plan_academico, $cod_tipo_periodo, $cod_certificante) {
        $this->oConnection = $connection;

        $arrConstructor = $this->_constructor($cod_filial, $cod_plan_academico, $cod_tipo_periodo, $cod_certificante);
        if (count($arrConstructor) > 0) {
            $this->cod_filial = $arrConstructor[0]['cod_filial'];
            $this->cod_plan_academico = $arrConstructor[0]['cod_plan_academico'];
            $this->cod_tipo_periodo = $arrConstructor[0]['cod_tipo_periodo'];
            $this->cod_certificante = $arrConstructor[0]['cod_certificante'];
            $this->estado = $arrConstructor[0]['estado'];
            $this->opcional = $arrConstructor[0]['opcional'];
            $this->existe = true;
        } else {
            $this->cod_filial = $cod_filial;
            $this->cod_plan_academico = $cod_plan_academico;
            $this->cod_tipo_periodo = $cod_tipo_periodo;
            $this->cod_certificante = $cod_certificante;
            $this->existe = false;
        }
    }

    /* PRIVATE FUNCTIONS */

    private function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['cod_plan_academico'] = $this->cod_plan_academico;
        $arrTemp['cod_tipo_periodo'] = $this->cod_tipo_periodo;
        $arrTemp['cod_filial'] = $this->cod_filial;
        $arrTemp['cod_certificante'] = $this->cod_certificante;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['opcional'] = $this->opcional;
        return $arrTemp;
    }

    private function _insertar() {
        $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
        $this->existe = true;
    }

    private function _actualizar() {
        $this->oConnection->where(array('cod_filial' => $this->cod_filial, 'cod_certificante' => $this->cod_certificante, 'cod_plan_academico' => $this->cod_plan_academico, 'cod_tipo_periodo' => $this->cod_tipo_periodo));
        $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto());
    }

    private function _constructor($cod_filial, $cod_plan_academico, $cod_tipo_periodo, $cod_certificante) {
        $query = $this->oConnection->select('*')
                        ->from($this->nombreTabla)
                        ->where(array(
                            'cod_filial' => "$cod_filial",
                            'cod_plan_academico' => "$cod_plan_academico",
                            'cod_tipo_periodo' => "$cod_tipo_periodo",
                            'cod_certificante' => "$cod_certificante"
                        ))->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /* PUBLIC FUNCTIONS */

    public function guardarCertificados_plan_filial() {
        if (!$this->existe) {
            return $this->_insertar();
        } else {
            return $this->_actualizar();
        }
    }

    /* STATIC FUNCTIONS */

    static function campos(CI_DB_mysqli_driver $connection) {
        $query = $connection->field_data('general.certificados_plan_filial');
        $arrResp = array();
        foreach ($query as $filed) {
            $arrResp[] = $filed->name;
        }
        return $arrResp;
    }

    static function listar(CI_DB_mysqli_driver $connection, $cod_filial = null, $cod_plan_academico = null, $cod_tipo_periodo = null, $cod_certificante = null) {
        $condiciones = array();
        if ($cod_certificante != null && $cod_filial != null && $cod_plan_academico != null && $cod_tipo_periodo != null) {
            $condiciones["cod_filial"] = "$cod_filial";
            $condiciones["cod_plan_academico"] = "$cod_plan_academico";
            $condiciones["cod_certificante"] = "$cod_certificante";
            $condiciones["cod_tipo_periodo"] = "$cod_tipo_periodo";
        }
        $query = $connection->select('*')
                        ->from('general.certificados_plan_filial')
                        ->where($condiciones)->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /**
     * Buscar registros en la tabla certificados_plan_filial
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de conceptos o la cantdad de registros segun el parametro contar
     */
    static function listarCerfificados_plan_filial(CI_DB_mysqli_driver $connection, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false) {

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
            $query = $connection->select('general.certificados_plan_filial' . '.*', false)->from('general.certificados_plan_filial')->get();
        } else {
            $query = $connection->select('general.certificados_plan_filial' . '.*', false)->from('general.certificados_plan_filial')->where($condiciones)->get();
        }

        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }

        return $arrResp;
    }

}
