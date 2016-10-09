<?php

/* metodos de consultas comunes */
        const WHERE_CRMETHOD = 0;
        const HAVING_CRMETHOD = 1;

/* tipo de datos */
        const INTEGER_CRTYPE = "integer";
        const FLOAT_CRTYPE = "float";
        const STRING_CRTYPE = "string";
        const DATE_CRTYPE = "date";
        const BOOL_CRTYPE = "boolean";
        const COMONOSCONOCIO_CRTYPE = "como_nos_conocio";
        const SEXO_CRTYPE = "sexo";
        const LOCALIDADES_PAIS_CRTYPE = "localidades_pais";
        const TIPO_CONTACTO_CRTYPE = "tipo_contacto";
        const TALLES_CRTYPE = "talle";
        const MATRIUCLAS_ESTADOS_CRTYPE = "estado_mat_per";
        const COMISIONES_NN_CRTYPE = "comsiones_nn";
        const COMISIONES_CRTYPE = "comisiones";
        const PERIODOS_CRTYPE = "periodos";
        const CONCEPTOS_CTACTE_CRTYPE = "conceptos_ctacte";
        const PLANES_PAGO_CRTYPE = "planes_pago";
        const ESTADOS_CONSULTAS_WEB_CRTYPE = "esatados_consultas_web";
        const USUARIOS_FILIAL_CRTYPE = "usuarios_filial";
        const MEDIOS_PAGO_CRTYPE = "medios_pago";
        const ESTADOS_COBROS_CRTYPE = "estado_cobro";
        const CONCEPTOS_CAJA_CRTYPE = "conceptos_caja";
        const CAJAS_CRTYPE = "cajas";
        const ESTADO_FACTURAS_CRTYPE = "estado_facturas";
        const TIPOS_FACTURAS_CRTYPE = "tipos_facturas";
        const FACTURANTES_CRTYPE = "facturantes";
        const CURSOS_CRTYPE = "cursos";
        const COMPROBANTES_CRTYPE = "comprobantes";
        const RAZONES_SOCIALES_CRTYPE = "razones_sociales";
        const ALUMNOS_NOMBRES_CRTYPE = "alumnos";
        const ALUMNOS_NOMBRES_POR_ID_CRTYPE = "alumnos_por_id";
        const MATERIAS_CRTYPE = "materias";
        const ESTADO_ACADEMICO_CRTYPE = "estado_academico_estado";
        const SALONES_CRTYPE = "salones";
        const DIA_DE_SEMANA_CRTYPE = "dia_de_semana";
        const PROFESORES_NOMBRES_CRTYPE = "profesores";
        const ESTADO_MATRICULAS_INSCRIPCIONES_CRTYPE = "estado_inscripciones";
        const ASISTENCIA_ALUMNO_CRTYPE = "asistencias";
        const TIPO_DEUDA_CRTYPE = 'tipo_deuda';
        const ESTADO_BOLETOS_BANCARIOS = 'estado_boletos';
        const SACADO_NOMBRE_BOLETOS = 'boletos';
        const SINO_CRTYPE = "si_no";
        const MEDIO_CUPONES_CRTYPE = "medio";
        const ESTADO_CUPONES_CRTYPE = "estado";
        const ESTADO_COMISIONES_CRTYPE = "estado_comisiones";
        const TITULOS_CRTYPE = "titulos";
        const CICLOS_HABILITADOS_CRTYPE = "ciclos_habilitados";
        const MODALIDAD_CRTYPE = "modalidad";
        const ESTADO_MATRICULAS_CRTYPE = "estado_matriculas_crtype";
        const ESTADO_CERTIFICADOS_CRTYPE = "estado_certificados";
        const CICLOS_LECTIVOS_CRTYPE = "ciclos_lectivos";
        const CTACTE_ESTADO_CRTYPE = "estado_ctacte";
        const DOCUMENTACION_ESTADO_CRTPE = "documentacion_estado";
        const ESTADO_ASPIRANTE_CRTYPE = "estado_aspirante";
/* tipo de filtros */
        const LIKE_CRFILTER = "contiene";
        const MAYOR_CRFILTER = "es_mayor_a";
        const MENOR_CRFILTER = "es_menor_a";
        const ENTRE_CRFILTER = "entre";
        const ES_IGUAL_CRFILTER = "es_igual_a";
        const MAYOR_IGUAL_CRFILTER = "es_mayor_igual_a";
        const MENOR_IGUAL_CRFILTER = "es_menor_igual_a";
        const NO_ES_IGUAL_CRFILTER = "no_es_igual";
        const PERIODOS_ROYALTYS_CRTYPE = "periodo_royaltys";

class reportes_sistema {

    private $join;
    private $left;
    private $campos;
    private $tabla;
    private $fields;
    private $oConnection;
    private $limit;
    private $group;
    private $paginations;
    private $order;
    private $report_name;
    private $searchLike = array();
    private $fieldView;
    private $commonFilters;
    private $applyCommonFilters;
    private $userFilters;
    private $order_apply;
    private $permanentWhere;
    private $permaneteWhereIn;
    private $permaneteWhereLineal;
    private $permaneteHavingLineal;

    /* CONSTRUCTOR */

    function __construct(CI_DB_mysqli_driver $conexion, $reportName) {
        $this->join = array();
        $this->left = array();
        $this->campos = array();
        $this->tabla = '';
        $this->fields = array();
        $this->oConnection = $conexion;
        $this->limit = array(10, 0);
        $this->paginations = array(10, 25, 50, 100);
        $this->order = array();
        $this->report_name = $reportName;
        $this->searchLike = '';
        $this->fieldView = array();
        $this->commonFilters = array();
        $this->applyCommonFilters = array();
        $this->userFilters = array();
        $this->order_apply = array();
        $this->permanentWhere = array();
        $this->permaneteWhereIn = array();
        $this->permaneteWhereLineal = array();
        $this->group = array();
    }

    /* PRIVATE FUNCTIONS */

    private function execQuery($contar = false) {
        $this->oConnection->_protect_identifiers=false;
        foreach ($this->fields as $value) {
            $this->oConnection->select($value, false);
        }
        $this->oConnection->from($this->tabla);
        foreach ($this->join as $join) {
            foreach ($join as $table => $condicion) {
                $this->oConnection->join($table, $condicion);
            }
        }
        foreach ($this->left as $join) {
            foreach ($join as $table => $condicion) {
                $this->oConnection->join($table, $condicion, "left");
            }
        }
        if (count($this->applyCommonFilters) > 0) {
            foreach ($this->applyCommonFilters as $filterID) {
                if (isset($this->commonFilters[$filterID])) {
                    $tipoConsulta = $this->commonFilters[$filterID]->method == WHERE_CRMETHOD ? "where" : "having";
                    if (is_array($this->commonFilters[$filterID]->condicion)) {
                        if (isset($this->commonFilters[$filterID]->condicion[0]) && isset($this->commonFilters[$filterID]->condicion[1])) {
                            $this->oConnection->$tipoConsulta($this->commonFilters[$filterID]->condicion[0], $this->commonFilters[$filterID]->condicion[1]);
                        } else {
                            $this->oConnection->$tipoConsulta($this->commonFilters[$filterID]->condicion[0]);
                        }
                    } else {
                        $this->oConnection->$tipoConsulta($this->commonFilters[$filterID]->condicion);
                    }
                }
            }
        }

        if ((count($this->userFilters) + count($this->applyCommonFilters)) > 1) {
            foreach ($this->userFilters as $filter) {
                if (isset($this->campos[$filter['field']])) {
                    $tipoConsulta = $this->campos[$filter['field']]->query_method == WHERE_CRMETHOD ? "where" : "having";
                    $tipoFiltro = $filter['filter'];
                    $fieldName = $this->campos[$filter['field']]->whereField <> '' ? $this->campos[$filter['field']]->whereField : $fieldName = $filter['field'];
                    $value1 = $filter['value1'];
                    $value2 = isset($filter['value2']) ? $filter['value2'] : null;

                    if($filter['field'] === 'estado'){
                        //$fieldName = "count(*) > 2 AND estado = ";
                    }


                    if(/*$fieldName === 'comisiones.codigo' || */$fieldName === 'cod_comision')
                    {
                        //$fieldName = "(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE `matriculas_inscripciones`.`cod_estado_academico` = `estadoacademico`.`codigo` AND `matriculas_inscripciones`.`baja` = '0' ORDER BY `matriculas_inscripciones`.`codigo` DESC LIMIT 1) = ".$value1." AND comisiones.codigo =";
                        $fieldName = "(SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE  matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo AND `matriculas_inscripciones`.`baja` = '0' AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = ".$value1;
                        $fieldName .= " OR (SELECT `matriculas_inscripciones`.`cod_comision` FROM `matriculas_inscripciones` WHERE matriculas_periodos.codigo = estadoacademico.cod_matricula_periodo  AND matriculas_inscripciones.cod_estado_academico IN ((SELECT `estadoacademico`.`codigo` FROM `estadoacademico` WHERE estadoacademico.cod_matricula_periodo = matriculas_periodos.codigo)) ORDER BY `matriculas_inscripciones`.`fecha_hora` DESC LIMIT 1) = 255";
                        $fieldName .= " AND NOT (SELECT matriculas_periodos.estado FROM matriculas_periodos WHERE matriculas_periodos.cod_tipo_periodo > 1 AND matriculas_periodos.cod_matricula = matriculas.codigo AND matriculas_periodos.estado NOT IN ('migrado', 'inhabilitada'))";
                        $fieldName .= " AND comisiones.codigo = ";
                    }
                    switch ($tipoFiltro) {
                        case ES_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE && $tipoConsulta == "where") {
                                $this->oConnection->$tipoConsulta("DATE({$fieldName})", $value1);
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                $this->oConnection->$tipoConsulta($fieldName, $value1);
                            }
                            break;

                        case LIKE_CRFILTER:
                            $this->oConnection->$tipoConsulta("{$fieldName} LIKE", "%{$value1}%");
                            break;

                        case MAYOR_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE && $tipoConsulta == "where") {
                                $this->oConnection->$tipoConsulta("DATE({$fieldName}) >", $value1);
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                $this->oConnection->$tipoConsulta("{$fieldName} >", $value1);
                            }
                            break;

                        case MENOR_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE && $tipoConsulta == "where") {
                                $this->oConnection->$tipoConsulta("DATE({$fieldName}) <", $value1);
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                $this->oConnection->$tipoConsulta("{$fieldName} <", $value1);
                            }
                            break;

                        case ENTRE_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE)
                            {
                                if($tipoConsulta == "where")
                                {
                                    $this->oConnection->$tipoConsulta("DATE({$fieldName}) >=", $value1);
                                    $this->oConnection->$tipoConsulta("DATE({$fieldName}) <=", $value2);
                                }
                                else
                                {
                                    $this->oConnection->$tipoConsulta("STR_TO_DATE({$fieldName},'%d/%m/%Y') >= STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                    $this->oConnection->$tipoConsulta("STR_TO_DATE({$fieldName},'%d/%m/%Y') <= STR_TO_DATE('{$value2}','%Y-%m-%d')");
                                }
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                $this->oConnection->$tipoConsulta("{$fieldName} >=", $value1);
                                $this->oConnection->$tipoConsulta("{$fieldName} <=", $value2);
                            }
                            break;

                        case MAYOR_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE)
                            {
                                if($tipoConsulta == "where")
                                {
                                    $this->oConnection->$tipoConsulta("DATE({$fieldName}) >=", $value1);
                                }
                                else
                                {
                                    $this->oConnection->$tipoConsulta("STR_TO_DATE({$fieldName},'%d/%m/%Y') >= STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }

                            } else {
                                $this->oConnection->$tipoConsulta("{$fieldName} >=", $value1);
                            }
                            break;

                        case MENOR_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE)
                            {
                                if($tipoConsulta == "where")
                                {
                                    $this->oConnection->$tipoConsulta("DATE({$fieldName}) <=", $value1);
                                }
                                else
                                {
                                    $this->oConnection->$tipoConsulta("STR_TO_DATE({$fieldName},'%d/%m/%Y') <= STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }
                            } else {
                                $this->oConnection->$tipoConsulta("{$fieldName} <=", $value1);
                            }
                            break;

                        case NO_ES_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE)
                            {
                                if($tipoConsulta == "where")
                                {
                                    $this->oConnection->$tipoConsulta("DATE({$fieldName}) <>", $value1);
                                }
                                else
                                {
                                    $this->oConnection->$tipoConsulta("STR_TO_DATE({$fieldName},'%d/%m/%Y') <> STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }

                            } else {
                                $this->oConnection->$tipoConsulta("{$fieldName} <>", $value1);
                            }
                            break;
                    }
                }
            }
        } else {
            foreach ($this->userFilters as $filter) {
                if (isset($this->campos[$filter['field']])) {
                    $tipoConsulta = $this->campos[$filter['field']]->query_method == WHERE_CRMETHOD ? "where" : "having";
                    $tipoFiltro = $filter['filter'];
                    $fieldName = $this->campos[$filter['field']]->whereField <> '' ? $this->campos[$filter['field']]->whereField : $fieldName = $filter['field'];
                    $value1 = $filter['value1'];
                    $value2 = isset($filter['value2']) ? $filter['value2'] : null;
                    switch ($tipoFiltro) {
                        case ES_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE)
                            {
                                if($tipoConsulta == "where")
                                {
                                    $this->oConnection->or_where("DATE({$fieldName})", $value1);
                                }
                                else
                                {
                                    $this->oConnection->$tipoConsulta("STR_TO_DATE({$fieldName},'%d/%m/%Y') = STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }

                            } else if ($this->campos[$filter['field']]->type == "asistencias" && $value1 == '') {
                                $this->oConnection->$tipoConsulta("($fieldName = '' OR $fieldName IS NULL)");

                            /* Inicio 601 */
                            } else if ($fieldName == "mails_consultas.mails_consultas.estado") {
                                if($value1 == "leidos")
                                {
                                    $this->oConnection->where('mails_consultas.mails_consultas.respuesta_automatica_enviada', 1);
                                    $this->oConnection->where($fieldName, 'pendiente');
                                }
                                else if($value1 == "no_leidos")
                                {
                                    $this->oConnection->where('mails_consultas.mails_consultas.notificar', 1);
                                    $this->oConnection->where($fieldName, 'pendiente');
                                }
                                else if($value1 == "respondidos")
                                {
                                    $this->oConnection->or_where($fieldName, 'cerrada');
                                    $this->oConnection->or_where($fieldName, 'concretada');
                                    $this->oConnection->or_where($fieldName, 'noconcretada');
                                    $this->oConnection->or_where($fieldName, 'abierta');
                                    //$this->oConnection->where('mails_consultas.mails_consultas.respuesta_automatica_enviada', 0);
                                    $this->oConnection->where('mails_consultas.mails_consultas.notificar', 0);
                                }
                            /* Fin 601 */

                            } else if ($tipoConsulta == "where") { // otros tipos de datos pueden necesitar diferente especificaciones
                                $this->oConnection->or_where($fieldName, $value1);
                            } else {
                                $this->oConnection->or_having($fieldName, $value1);
                            }
                            break;

                        case LIKE_CRFILTER:

                            $this->oConnection->$tipoConsulta("{$fieldName} LIKE", "%{$value1}%");
                            break;

                        case MAYOR_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE) {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("({$fieldName}) >", $value1);
                                } else {
                                    $this->oConnection->or_having("STR_TO_DATE({$fieldName},'%d/%m/%Y') > STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("{$fieldName} >", $value1);
                                } else {
                                    $this->oConnection->or_having("{$fieldName} >", $value1);
                                }
                            }
                            break;

                        case MENOR_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE) {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("DATE({$fieldName}) <", $value1);
                                } else {
                                    $this->oConnection->or_having("STR_TO_DATE({$fieldName},'%d/%m/%Y') < STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("{$fieldName} <", $value1);
                                } else {
                                    $this->oConnection->or_having("{$fieldName} <", $value1);
                                }
                            }
                            break;

                        case ENTRE_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE) {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->where("DATE({$fieldName}) >=", $value1);
                                    $this->oConnection->where("DATE({$fieldName}) <=", $value2);
                                } else {
                                    $this->oConnection->having("STR_TO_DATE({$fieldName},'%d/%m/%Y') >= STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                    $this->oConnection->having("STR_TO_DATE({$fieldName},'%d/%m/%Y') <= STR_TO_DATE('{$value2}','%Y-%m-%d')");
                                }
                            } else {                                                                // otros tipos de datos pueden necesitar diferente especificaciones
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->where("{$fieldName} >=", $value1);
                                    $this->oConnection->where("{$fieldName} <=", $value2);
                                } else {
                                    $this->oConnection->having("{$fieldName} >=", $value1);
                                    $this->oConnection->having("{$fieldName} <=", $value2);
                                }
                            }
                            break;

                        case MAYOR_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE) {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("DATE({$fieldName}) >=", $value1);
                                } else {
                                    $this->oConnection->or_having("STR_TO_DATE({$fieldName},'%d/%m/%Y') >= STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }
                            } else {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("{$fieldName} >=", $value1);
                                } else {
                                    $this->oConnection->or_having("{$fieldName} >=", $value1);
                                }
                            }
                            break;

                        case MENOR_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE) {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("DATE({$fieldName}) <=", $value1);
                                } else {
                                    $this->oConnection->or_having("STR_TO_DATE({$fieldName},'%d/%m/%Y') <= STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }
                            } else {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("{$fieldName} <=", $value1);
                                } else {
                                    $this->oConnection->or_having("{$fieldName} <=", $value1);
                                }
                            }
                            break;

                        case NO_ES_IGUAL_CRFILTER:
                            if ($this->campos[$filter['field']]->type == DATE_CRTYPE) {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("DATE({$fieldName}) <>", $value1);
                                } else {
                                    $this->oConnection->or_having("STR_TO_DATE({$fieldName},'%d/%m/%Y') <> STR_TO_DATE('{$value1}','%Y-%m-%d')");
                                }
                            } else {
                                if ($tipoConsulta == "where"){
                                    $this->oConnection->or_where("{$fieldName} <>", $value1);
                                } else {
                                    $this->oConnection->or_having("{$fieldName} <>", $value1);
                                }
                            }
                            break;
                    }
                }
            }
        }

        if (count($this->permanentWhere) > 0) {
            foreach ($this->permanentWhere as $where) {
                $this->oConnection->where($where);
            }
        }

        if (count($this->permaneteWhereLineal) > 0) {
            foreach ($this->permaneteWhereLineal as $where) {
                $this->oConnection->where($where);
            }
        }

        if (count($this->permaneteHavingLineal) > 0) {
            foreach ($this->permaneteHavingLineal as $having) {
                $this->oConnection->having($having);
            }
        }
        if (count($this->permaneteWhereIn) > 0) {
            foreach ($this->permaneteWhereIn as $field => $values) {
                $this->oConnection->where_in($field, $values);
            }
        }

        if ($this->searchLike != '') {
            foreach ($this->fieldView as $field) {
                foreach ($this->searchLike as $valor) {
                    $this->oConnection->or_having($field . " LIKE", "%{$valor}%");
                }
            }
        }

        if (count($this->group) > 0) {
            foreach ($this->group as $group) {
                $this->oConnection->group_by($group);
            }
        }

        if ($contar) {
            $query = $this->oConnection->get();
            //echo $this->oConnection->last_query();die();
            return $query->num_rows();
        } else {
            if (isset($this->order[0])) {
                $orderFiled = isset($this->order_apply[$this->order[0]]) ? $this->order_apply[$this->order[0]] : $this->order[0];

                $this->oConnection->order_by($orderFiled . " " . $this->order[1]);
            }
            if ($this->limit[0] != -1) {
                //if (isset($this->limit[0])){
                $this->oConnection->limit($this->limit[0], $this->limit[1]);
                //}
            }

            $query = $this->oConnection->get();
//           echo $this->oConnection->last_query();die();
            return $query->result_array();
        }
    }

    /* PROTECTED FUCNTIONS */

    public function setPermaneteWhereLineal($arrWhereLineal) {
        $this->permaneteWhereLineal = $arrWhereLineal;
    }

    public function setPermaneteHavingLineal($arrHavingLineal) {
        $this->permaneteHavingLineal = $arrHavingLineal;
    }

    public function setPermanentWhere($where) {
        $this->permanentWhere[] = $where;
    }

    public function setPermanenteWhereIn($campo, array $arrValues) {
        $this->permaneteWhereIn[$campo] = $arrValues;
    }

    public function setUserFilters($field, $filter, $value1, $value2 = null) {
        $ct = count($this->userFilters);
        $this->userFilters[$ct]['field'] = $field;
        $this->userFilters[$ct]['filter'] = $filter;
        $this->userFilters[$ct]['value1'] = $value1;
        if ($value2 !== null) {
            $this->userFilters[$ct]['value2'] = $value2;
        }
    }

    public function setApplyCommonFilters($commonFilters) {
        if (is_array($commonFilters)) {
            $this->applyCommonFilters = $commonFilters;
        } else {
            $this->applyCommonFilters[] = $commonFilters;
        }
    }

    public function setFiltrosComunes($display, $id, $condicion, $method, $hint = null) {
        $myFiltro = new filtros_reportes($id, $display, $condicion, $method, $hint);
        $this->commonFilters[$id] = $myFiltro;
    }

    public function setCamposVisibles($campos) {
        if (is_array($campos)) {
            $this->fieldView = $campos;
        } else {
            $this->fieldView[] = $campos;
        }
    }

    public function setField($name) {
        $this->fields[] = $name;
    }

    public function setTable($tableName) {
        $this->tabla = $tableName;
    }

    public function setJOIN($table, $condicion, $tipo = "inner") {
        if (strtoupper($tipo) == "INNER") {
            $this->join[] = array($table => $condicion);
        } else if (strtoupper($tipo) == "LEFT") {
            $this->left[] = array($table => $condicion);
        }
    }

    public function setCampo($identificador, $display, $visible = true, $dataType = STRING_CRTYPE, array $filtros = null, $dataTransform = false, $whereField = null, $orderAplica = null, $tipoConsulta = WHERE_CRMETHOD, $Pdfwidth = null, $pdfVisible = true, $acumulable = false) {
        $myCampo = new campos_reportes();
        $myCampo->display = $display;
        $myCampo->filtros = $filtros;
        $myCampo->type = $dataType;
        $myCampo->visible = $visible;
        $myCampo->transform = $dataTransform;
        $myCampo->whereField = $whereField;
        $myCampo->query_method = $tipoConsulta;
        $myCampo->Pdfwidth = $Pdfwidth;
        $myCampo->PdfVisible = $pdfVisible;
        $myCampo->acumulable = $acumulable;
        $this->campos[$identificador] = $myCampo;
        if ($orderAplica != null) {
            $this->order_apply[$identificador] = $orderAplica;
        }
    }

    /* PUBLIC FUNTIONS */

    public function setSearchLike(array $searchString) {
        foreach ($searchString as $valor) {
            $this->searchLike[] = trim($valor);
        }
    }

    public function getFiltrosComunes() {
        return $this->commonFilters;
    }

    public function getColumns() {
        return $this->campos;
    }

    public function setGroup($group) {
        $this->group[] = $group;
    }

    public function setOrder($sortColumn, $sortDir = "desc") {
        $this->order = array($sortColumn, $sortDir);
    }

    public function setLimit($limitCantidad, $limitMin = 0) {
        $this->limit = array($limitCantidad, $limitMin);
    }

    public function getReporte($agregarColumnas = false, $cargarDatos = true) {
        $arrResp = array();
        $totalReg = ($cargarDatos ? $this->execQuery(true) : 0);
        $cantidadPaginas = ceil($totalReg / $this->limit[0]);
        $limitMin = $this->limit[1] + 1;
        $limitMax = $this->limit[0];
        $paginations = $this->paginations;
        if (!in_array($this->limit[0], $this->paginations)) {
            $paginations[] = $this->limit[0];
            asort($paginations);
        }
        $arrResp['iTotalRecords'] = $totalReg;
        $arrResp['iLimitMin'] = $limitMin;
        $arrResp['iLimitMax'] = $limitMax;
        $arrResp['iPagesCount'] = $cantidadPaginas;
        $arrResp['iPaginations'] = $paginations;
        $arrResp['iPaginationSelected'] = $this->limit[0];
        $arrResp['report_name'] = $this->report_name;
        $arrResp['iCurrentPage'] = ceil((($limitMin - 1) / $limitMax) + 1);
        $arrResp['aaData'] = ($cargarDatos ? $this->execQuery(false) : array());
        if ($agregarColumnas) {
            $arrResp['columns'] = $this->getColumns();
            $arrResp['common_filters'] = $this->getFiltrosComunes();
        }
        $indiceAcumulable = '';
        $arrColumns = $this->getColumns();
        $i = 0;
        foreach ($arrColumns as $key => $colum) {
            if ($colum->acumulable) {
                $indiceAcumulable[] = $key;
            }
            $i++;
        }
        $arrResp['indice_acumulable'] = $indiceAcumulable;
        return $arrResp;
    }

}
