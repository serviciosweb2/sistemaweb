<?php

Class Model_matriculas_horarios extends CI_Model {

    var $codigo_filial = 0;

    public function __construct($arg) {
        parent::__construct();
        $this->codigo_filial = $arg["codigo_filial"];
    }

    public function controlarBajaMatriculasInscripciones() {
        $conexion = $this->load->database($this->codigo_filial, true);
        $condiciones = array('matriculas_inscripciones.baja' => 0,);

        $estadosaca = Vestadoacademico::getEstadosAcademicoInscripciones($conexion, $condiciones);
//        select matriculas_inscripciones.codigo, 
//(select count(matriculas_inscripciones.codigo) from matriculas_inscripciones where matriculas_inscripciones.baja=0 and matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo) as cantinscripciones
// from matriculas_inscripciones
//join estadoacademico on  matriculas_inscripciones.cod_estado_academico = estadoacademico.codigo
//where matriculas_inscripciones.baja = 0 
//having cantinscripciones >1
    }

}
