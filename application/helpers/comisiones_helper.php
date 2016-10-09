<?php
function formatearNombreComision($objComision = null, $arrComision = null){
    $ci = & get_instance();
    $session = $ci->session->userdata('filial');
    $cod_plan_academico = !isset($arrComision['cod_plan_academico']) ? $objComision->cod_plan_academico : $arrComision['cod_plan_academico'];
    $conexion = $ci->load->database($session['codigo'], true);
    $plan_academico = new Vplanes_academicos($conexion, $cod_plan_academico);
    $curso = $plan_academico->getCurso();
    $cc = $curso->tipo_curso;
    $periodos = $plan_academico->getPeriodos();
    $periodo = !isset($arrComision['cod_tipo_periodo']) ? $objComision->cod_tipo_periodo : $arrComision['cod_tipo_periodo'];
    $ciclo = !isset($arrComision['ciclo']) ? $objComision->ciclo : $arrComision['ciclo'];
    $abrCurso = Vcursos::getAbreviaturaCursoHabilitado($conexion, $plan_academico->cod_curso);
    $modalidad = isset($arrComision['modalidad']) && $arrComision['modalidad'] == 'intensiva' ? lang('abreviatura_intensiva') : '';
    if ($cc == 'curso') {
        $nombrecomision = count($periodos) > 1 ? $periodo . 'K' : '';
    } elseif ($cc == 'seminario') {
        $nombrecomision = lang('ABREVIA_SEMINARIO');
    } else {
        $nombrecomision = lang('ABREVIA_CURSOCORTO');
    }
    if ($modalidad != '') {
        $nombrecomision.= $ciclo . $abrCurso[0]['abreviatura'] . $modalidad;
    } else {
        $nombrecomision.= $ciclo . $abrCurso[0]['abreviatura'];
    }
    return $nombrecomision;
}