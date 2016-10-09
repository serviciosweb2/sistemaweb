<?php

class Tcertificados {

    protected $cod_matricula_periodo;
    public $fecha_hora;
    public $estado;
    public $cod_usuario;
    public $entregado = 0;
    public $recibido = 0;
    public $id_producto_pedido = null;
    protected $cod_certificante;
    protected $existe = false;
    protected $oConnection;
    public $nombreTabla = 'certificados';

    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $connection, $cod_matricula_periodo, $cod_certificante) {
        $this->oConnection = $connection;
       
        $arrConstructor = $this->_constructor($cod_matricula_periodo, $cod_certificante);
        if (count($arrConstructor) > 0) {
            $this->cod_matricula_periodo = $arrConstructor[0]['cod_matricula_periodo'];
            $this->cod_certificante = $arrConstructor[0]['cod_certificante'];
            $this->fecha_hora = $arrConstructor[0]['fecha_hora'];
            $this->estado = $arrConstructor[0]['estado'];
            $this->cod_usuario = $arrConstructor[0]['cod_usuario'];
            $this->entregado = $arrConstructor[0]['entregado'];
            $this->recibido = $arrConstructor[0]['recibido'];
            $this->id_producto_pedido = $arrConstructor[0]['id_producto_pedido'] == '' ? null : $arrConstructor[0]['id_producto_pedido'];
            $this->existe = true;
        } else {
            $this->cod_matricula_periodo = $cod_matricula_periodo;
            $this->cod_certificante = $cod_certificante;
            $this->existe = false;
        }
    }

    /* PRIVATE FUNCTIONS */

    private function _getArrayDeObjeto() {
        $arrTemp = array();
        $arrTemp['cod_matricula_periodo'] = $this->cod_matricula_periodo;
        $arrTemp['cod_certificante'] = $this->cod_certificante;
        $arrTemp['fecha_hora'] = $this->fecha_hora == '' ? null : $this->fecha_hora;
        $arrTemp['estado'] = $this->estado;
        $arrTemp['cod_usuario'] = $this->cod_usuario;
        $arrTemp['entregado'] = $this->entregado;
        $arrTemp['recibido'] = $this->recibido;
        $arrTemp['id_producto_pedido'] = $this->id_producto_pedido;
        return $arrTemp;
    }

    private function _insertar() {
        $this->oConnection->insert($this->nombreTabla, $this->_getArrayDeObjeto());
        $this->existe = true;
        return true;
    }

    private function _actualizar() {
        $this->oConnection->where(array('cod_certificante' => $this->cod_certificante, 'cod_matricula_periodo' => $this->cod_matricula_periodo));
        return $this->oConnection->update($this->nombreTabla, $this->_getArrayDeObjeto());
    }

    private function _constructor($cod_matricula_periodo, $cod_certificante) {
        $query = $this->oConnection->select('*')
                        ->from($this->nombreTabla)
                        ->where(array(
                            'cod_matricula_periodo' => "$cod_matricula_periodo",
                            'cod_certificante' => "$cod_certificante"
                        ))->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /* PUBLIC FUNCTIONS */

    public function guardarCertificados() {
        if (!$this->existe) {
            return $this->_insertar();
        } else {
            return $this->_actualizar();
        }
    }

    /* STATIC FUNCTIONS */

    static function campos(CI_DB_mysqli_driver $connection) {
        $query = $connection->field_data('certificados');
        $arrResp = array();
        foreach ($query as $filed) {
            $arrResp[] = $filed->name;
        }
        return $arrResp;
    }

    static function listar(CI_DB_mysqli_driver $connection, $cod_certificante = null, $cod_matricula_periodo = null) {
        $condiciones = array();
        if ($cod_certificante != null && $cod_matricula_periodo != null) {
            $condiciones["cod_certificante"] = "$cod_certificante";
            $condiciones["cod_matricula_periodo"] = "$cod_matricula_periodo";
        }
        $query = $connection->select('*')
                        ->from('certificados')
                        ->where($condiciones)->get();
        $arrResp = $query->result_array();
        return $arrResp;
    }

    /**
     * Buscar registros en la tabla certificados
     *
     * @param CI_DB_mysqli_driver $connection   parametro de conexion actual.
     * @param array $condiciones    (opcional) un array en formato array(campo => valor) con las restricciones de busqueda
     * @param array $limite    (opcional) un array en formato array(limite inferior, cantidad) con las opciones de limite
     * @param array $limit    (opcional) un array en formato array(array(campo, orden)) que representa el orden de los datos a recuperar
     * @param array $grupo    (opcional) un array en formato array(grupo1, grupo2, ...) para agrupar los resultados
     * @param boolean $contar    (opcional) (default false) Indica si solo debe retornarse la cantidad de registros
     * @return mixed    Retorna la lista de conceptos o la cantdad de registros segun el parametro contar
     */
    static function listarCerfificados(CI_DB_mysqli_driver $connection, array $condiciones = null, array $limite = null, array $orden = null, array $grupo = null, $contar = false, $wherein = null) {
        if ($orden != null) {
            $arrOrder = array();
            foreach ($orden as $value) {
                $arrOrder[] = $value['campo'] . " " . $value['orden'];
            }
            $orderBy = implode(", ", $arrOrder);
            $connection->order_by($orderBy);
        }

        if ($wherein != null) {
            $connection->where_in($wherein['campo'], $wherein['valores']);
        }

        if ($limite != null) {
            $connection->limit($limite[1], $limite[0]);
        }

        if ($grupo != null) {
            $connection->group_by($grupo);
        }

        if ($condiciones == null) {
            $query = $connection->select('certificados' . '.*', false)->from('certificados')->get();
        } else {
            $query = $connection->select('certificados' . '.*', false)->from('certificados')->where($condiciones)->get();
        }

        if ($contar) {
            $arrResp = $query->num_rows();
        } else {
            $arrResp = $query->result_array();
        }

        return $arrResp;
    }

    function getExiste() {
        return $this->existe;
    }

}
