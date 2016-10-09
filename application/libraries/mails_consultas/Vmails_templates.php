<?php

/**
* Class Vmails_templates
*
*Class  Vmails_templates maneja todos los aspectos de alumnos
*
* @package  SistemaIGA
* @subpackage Alumnos
* @author   Ivan berthillod <ivan.sys@gmail.com>
* @author   Aquiles Gonzalez <sistemas1@iga-la.net>
* @version  $Revision: 1.1 $
* @access   private
*/
class Vmails_templates extends Tmails_templates{

    function __construct(CI_DB_mysqli_driver $conexion, $codigo = null) {
        parent::__construct($conexion, $codigo);
    }

    /* PRIVATE FUNCTIONS */

    /**
     * retorna un array con los indices y los valores necesarios para el desetiquetado de valores personalizados
     *
     * @param string $imagen            el path de la imagen de cabecera
     * @param string $idioma            el idioma utilizado (es, in, pr, etc.)
     * @param int $idMailsConsultas     El codigo de mails cosnualtas
     * @return array
     */
    static private function getArrayEtiquetasPersonalizadas($imagen, $idioma, $idMailsConsultas){
        return array(
            "[!--IMAGENCABECERA--]" => $imagen,
            "[!--CAMPODESCRIPCION--]" => "descripcion_{$idioma}",
            "[!--CAMPONOMBRE--]" => "nombre_{$idioma}",
            "[!--IDMAILCONSULTAS--]" => $idMailsConsultas
        );
    }

    private function getDefaultValues($codFilial){
        $arrCondiciones = array(
                "cod_filial" => $codFilial,
                "cod_template" => $this->cod_template
            );
        $arrValores = Vmails_consultas_default_values::listar($this->oConnection, $arrCondiciones);
        $arrResp = array();
        foreach($arrValores as $valor){
            $arrResp[$valor['tipo_campo']][$valor['numero_campo']] = $valor['valor_campo'];
        }
        return $arrResp;
    }

    /* PUBLIC FUNCTIONS */

    /**
     * retorna le html del template desempaquetado
     *
     * @param int $idMailsConsultas
     * Vfiliales $myFilial
     * @return string
     */
    public function getHTML($idMailsConsultas, Vfiliales $myFilial, $modoInput = true){
        if ($this->cod_curso != '' ||$this->cod_template == 9){
            $arrDefaultValues = $this->getDefaultValues($myFilial->getCodigo());
        }
        //print_r($this->getDefaultValues($myFilial->getCodigo()));
        $idFilial = $myFilial->getCodigo();
        //echo '<br>';
        //echo  $this->cod_curso;
        $codCurso = $this->cod_curso;
        $data = array();
        if ($this->cod_curso != ''){
            $arrCondiciones = array(
                "cod_curso" => $this->cod_curso
            );
            $arrPlanesAcademicos = Vplanes_academicos::listarPlanes_academicos($this->oConnection, $arrCondiciones);

            //$conexion = $this->load->database($myFilial->getCodigo(),true);
          /*  $conexion = $this->load->database($idFilial, true);

            $conexion->select("general.cursos.cant_horas");
            $conexion->select("general.cursos.cantidad_meses");
            $conexion->from("general.cursos");
            $conexion->where("general.cursos.codigo", $codCurso);
            $query = $conexion->get();
            $datos_curso = $query->result_array();
*/
            $this->oConnection->select("general.cursos.cant_horas");
            $this->oConnection->select("general.cursos.cantidad_meses");
            $this->oConnection->from("general.cursos");
            $this->oConnection->where("general.cursos.codigo", $codCurso);
            $query = $this->oConnection->get();
            $datos_curso = $query->result_array();
            /*
            $this->oConnection->select('telefonos.*,alumnos_telefonos.default');
            $this->oConnection->from("alumnos_telefonos");
            $this->oConnection->join('telefonos', 'alumnos_telefonos.cod_telefono = telefonos.codigo');
            $this->oConnection->where('alumnos_telefonos.cod_alumno', $this->codigo);
            $this->oConnection->where('telefonos.baja', 0);
            if ($soloPrincipal) {
                $this->oConnection->where("alumnos_telefonos.default", 1);
            }
            $query = $this->oConnection->get();
            return $arrResp = $query->result_array();*/
            //$datos_curso = getDatosCurso($idFilial, $codCurso);


            foreach ($arrPlanesAcademicos as $key => $plan) {
                $data['plan'][$key] = $plan;
                $data['plan'][$key]['periodos'] = $this->getMateriasDatatable($plan['codigo'], $myFilial->getCodigo(), true);
            }

            $horasTotal = 0;
            $mesesTotal = 0;
            foreach($data['plan'][0]['periodos'] as $key => $per)
            {
                //$horasTotal += $per['periodo']['hs_catedra'];
                if(isset($per['periodo']['modalidades'][0]))
                {
               //     $mesesTotal = $per['periodo']['modalidades'][0]['cant_meses'];
                }

            }
            /* Llenar estas horas */
            $horasTotal = $datos_curso[0]['cant_horas'];
            $mesesTotal = $datos_curso[0]['cantidad_meses'];
        }
        $myTemplate = new Vtemplates($this->oConnection, $this->cod_template);
        $html = $myTemplate->html;
        if ($this->cod_curso != ''){
            $myCurso = new Vcursos($this->oConnection, $this->cod_curso);
            $arrPropiedades = $myCurso->getCursosPaises($myFilial->pais);
            $propiedades = isset($arrPropiedades[0]) ? $arrPropiedades[0] : array();

            $cantHoras = isset($propiedades['horas']) ? $propiedades['horas'] : $myCurso->cant_horas;
            $cantMeses = isset($propiedades['meses']) ? $propiedades['meses'] : $myCurso->cantidad_meses;


            if ($this->cod_curso != 6 and $myFilial->pais != 2 and $this->cod_curso != 3 and $myFilial->pais != 4 ){
                if(isset($horasTotal) && $horasTotal > 0.00)
                {
                    $cantHoras = $horasTotal;
                }

                if(isset($mesesTotal) && $mesesTotal > 0.00)
                {
                    $cantMeses = $mesesTotal;
                }
         }

            $cantHoras = str_replace(".00", "", $cantHoras);
            $cantMeses = str_replace(".00", "", $cantMeses);
            $cantHoras = str_replace(".50", "½", $cantHoras);
            $cantMeses = str_replace(".50", "½", $cantMeses);
            $cantHoras = str_replace(".25", "¼", $cantHoras);
            $cantMeses = str_replace(".25", "¼", $cantMeses);
            $cantHoras = str_replace(".75", "¾", $cantHoras);
            $cantMeses = str_replace(".75", "¾", $cantMeses);
            $html = str_replace('[!--CANTIDAD_DE_MESES--]', $cantMeses, $html);
            $html = str_replace("[!--CANTIDAD_DE_HORAS--]", $cantHoras, $html);
        }
        if ($myFilial->idioma == "es")
            $imagen = "mails_templates_Cabecera.jpg";
        else if ($myFilial->idioma == "pt")
            $imagen = "Cabecera_Brasil.jpg";

        if ($this->cod_curso != '' || $this->cod_template == 9) {
            //maquetados::desetiquetarCuotas($this->cod_template, $arrDefaultValues, $html, $modoInput);
            maquetados::desetiquetarPlanesDePago($this->cod_template, $arrDefaultValues, $html, $modoInput);
            maquetados::desetiquetarDescuentosVigentes($this->cod_template, $arrDefaultValues, $html, $modoInput);
        }

        maquetados::desetiquetar(Vmails_templates::getArrayEtiquetasPersonalizadas($imagen, $myFilial->idioma, $idMailsConsultas), $html);
        maquetados::desetiquetar(array("[!--TEMPLATEID--]" => $this->cod_template), $html);
        maquetados::desetiquetarIdioma($html, true);
        maquetados::desetiquetarDatosFilial($this->oConnection, $myFilial, $html);
        maquetados::desetiquetarDesdeDB($this->oConnection, $html);
        if ($this->cod_curso != '' || $this->cod_template == 9){
            maquetados::desetiquetarINPUTS($this->cod_template, $html, $arrDefaultValues, $modoInput);
        }
        return $html;
    }


    public function getDatosCurso (CI_DB_mysqli_driver $conexion, $codCurso = null){
        $conexion->select("general.cursos.cant_horas");
        $conexion->select("general.cursos.cantidad_meses");
        $conexion->from("general.cursos");
        $conexion->where("general.cursos.codigo", $codCurso);
        $query = $conexion->get();
        return $query->result_array();
    }
    /**
     * retorna el html del templay en formato original (con todas sus maquetas y etiquetas)
     *
     * @return string
     */
    public function getHTMLEtiquetado(){
        $myTemplate = new Vtemplates($this->oConnection, $this->cod_template);
        return $myTemplate->html;
    }

    /* STATIC FUNTIONS */

    /**
     * retorna los templates con las cantidades de veces que han sido utilizados
     *
     * @param CI_DB_mysqli_driver $conexion
     * @param int $idFilial
     * @param mixed $orderBy    (string o array)
     * @param array $arrCondicionesOrLike
     * @return array
     */
    static function getTemplatesCantidades(CI_DB_mysqli_driver $conexion, $idFilial = null, $orderBy = null,
            array $arrCondicionesOrLike = null, $cursoActivo = null){

        //Ticket 4624 -mmori- se cambia el manejo de las respuestas a las consultas web
        /*$conexion->select("SUM(mails_consultas.mails_respuesta_consultas_historico.cantidad)", false);
        $conexion->from("mails_consultas.mails_respuesta_consultas_historico");
        $conexion->where("mails_consultas.mails_respuesta_consultas_historico.cod_template = mails_consultas.mails_templates.cod_template");
        if ($idFilial != null)
            $conexion->where("mails_consultas.mails_respuesta_consultas_historico.cod_filial", $idFilial);
        $subquery = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("COUNT(cod_curso)", false);
        $conexion->from("cursos_habilitados");
        $conexion->where("cursos_habilitados.cod_curso = general.cursos.codigo");
        $sqCursosHabilitados = $conexion->return_query();
        $conexion->resetear();

        $conexion->select("mails_consultas.mails_templates.cod_template");
        $conexion->select("general.templates.nombre");
        $conexion->select("general.templates.html");
        $conexion->select("general.cursos.nombre_es");
        $conexion->select("general.cursos.nombre_pt");
        $conexion->select("general.cursos.nombre_in");
        $conexion->select("($subquery) AS cantidad", false);
        $conexion->select("IF (general.cursos.codigo IS NULL, 1, ($sqCursosHabilitados)) AS curso_habilitado", false);
        $conexion->from("mails_consultas.mails_templates");
        $conexion->join("general.cursos", "general.cursos.codigo = mails_consultas.mails_templates.cod_curso", "LEFT");
        $conexion->join("general.templates", "general.templates.codigo = mails_consultas.mails_templates.cod_template");
        $conexion->where("mails_consultas.mails_templates.activo", 1);*/
        //-mmori- modifico para recuperar el nombre de filiales_templates (para gestionar el nombre a mostrar de cursos por filial)
        $conexion->select("mails_consultas.filiales_templates.cod_template");
        $conexion->select("mails_consultas.filiales_templates.nombre_mostrar");
        $conexion->select("general.templates.html");
        $conexion->select("general.cursos.nombre_".get_idioma()." as nombre_curso");
        //$conexion->select("general.cursos.nombre_es");
        //$conexion->select("general.cursos.nombre_pt");
        //$conexion->select("general.cursos.nombre_in");
        $conexion->select("(SELECT SUM(mails_consultas.mails_respuesta_consultas_historico.cantidad)
                            FROM (mails_consultas.mails_respuesta_consultas_historico)
                            WHERE mails_consultas.mails_respuesta_consultas_historico.cod_template = mails_consultas.mails_templates.cod_template
                            AND mails_consultas.mails_respuesta_consultas_historico.cod_filial = '19') AS cantidad");
        $conexion->select("mails_consultas.filiales_templates.estado");
        $conexion->from("mails_consultas.filiales_templates");
        $conexion->join("general.templates", "general.templates.codigo = mails_consultas.filiales_templates.cod_template", "LEFT");
        $conexion->join("mails_consultas.mails_templates", "mails_consultas.mails_templates.cod_template = general.templates.codigo", "LEFT");
        $conexion->join("general.cursos", "general.cursos.codigo = mails_consultas.mails_templates.cod_curso", "LEFT");
        $conexion->where("mails_consultas.filiales_templates.estado", "habilitado");
        if ($idFilial != null)
            $conexion->where("mails_consultas.filiales_templates.cod_filial", $idFilial);

        if($arrCondicionesOrLike != null){
            $conexion->or_like($arrCondicionesOrLike);
        }
        if ($orderBy != null){
            if (is_array($orderBy)){
                $conexion->order_by($orderBy[0], $orderBy[1]);
            } else {
                $conexion->order_by($orderBy);
            }
        }
        /*if ($cursoActivo !== null){
            if ($cursoActivo){
                $conexion->having("curso_habilitado", 1);
            } else {
                $conexion->having("curso_habilitado", 0);
            }
        }*/

        $query = $conexion->get();

        //die($conexion->last_query());
        return $query->result_array();
    }


    /**
     * Retorna el codigo HTML de un template (con sus inputs de entrada y sus etiquetas traducidas)
     *
     * @param CI_DB_mysqli_driver $conexion
     * @param array $codigosTemplates
     * @param int $codFilial
     * @param int $idMailsConsultas
     * @return string
     */
    static function armarTemplates(CI_DB_mysqli_driver $conexion, array $codigosTemplates, $codFilial, $idMailsConsultas, $modoInput = true){
        $myFilial = new Vfiliales($conexion, $codFilial);
        $myContenedor = new Vmails_templates($conexion, 5);
        $myEncabezado = new Vmails_templates($conexion, 3);
        $myBienvenida = new Vmails_templates($conexion, 7);
        $mySaludoFinal = new Vmails_templates($conexion, 4);
        $htmlCurso = '';
        foreach ($codigosTemplates as $curso){
            $myTemplateCurso = new Vmails_templates($conexion, $curso);
            $htmlCurso .= $myTemplateCurso->getHTML($idMailsConsultas, $myFilial, $modoInput);
        }
        $etiquetasValores = array(
            "[!--/TEMPLATEENCABEZADO/--]" => $myEncabezado->getHTML($idMailsConsultas, $myFilial),
            "[!--/TEMPLATEBIENVENIDA/--]" => $myBienvenida->getHTML($idMailsConsultas, $myFilial),
            "[!--/TEMPLATECURSOS/--]" => utf8_decode($htmlCurso),//$htmlCurso,
            "[!--/TEMPLATEFIN/--]" => $mySaludoFinal->getHTML($idMailsConsultas, $myFilial)
        );
        $html = $myContenedor->getHTMLEtiquetado();
        maquetados::desetiquetar($etiquetasValores, $html);
        return $html;
    }

    public function getMateriasDatatable($codplan, $cod_filial, $conplanperiodo = false) {
        //$conexion = $this->load->database($this->codigo_filial, true);

        $plan = new Vplanes_academicos($this->oConnection, $codplan);
        $materias = $plan->getMaterias();

        $Periodos = array();
        $a = 0;
        $periodo = '';

        for ($i = 0; $i < count($materias); $i++) {

            $nombreperiodo = Vtipos_periodos::getNombre($this->oConnection, $materias[$i]['cod_tipo_periodo']);

            $a = $nombreperiodo != $periodo ? 0 : $a + 1;

            $periodo = $nombreperiodo;

            $Periodos[$periodo]['materias'][$a]['codigo'] = $materias[$i]['codigo'];
            $Periodos[$periodo]['materias'][$a]['nombre_es'] = $materias[$i]['nombre_es'];
            $Periodos[$periodo]['materias'][$a]['nombre_in'] = $materias[$i]['nombre_in'];
            $Periodos[$periodo]['materias'][$a]['nombre_pt'] = $materias[$i]['nombre_pt'];
            $Periodos[$periodo]['materias'][$a]['cod_tipo_materia'] = $materias[$i]['cod_tipo_materia'];
        }
        if ($conplanperiodo) {
            foreach ($Periodos as $key => $value) {
                $condiciones = array('nombre' => $key);
                $valueperiodos = Vtipos_periodos::listarTipos_periodos($this->oConnection, $condiciones);
                $planperiodo = $plan->getPeriodos($valueperiodos[0]['codigo']);
                $existe = count($planperiodo) > 0 ? true : false;
                $Periodos[$key]['periodo']['codigo'] = $existe ? $valueperiodos[0]['codigo'] : 0;
                $cod_titulo = $existe ? $planperiodo[0]['cod_titulo'] : '-1';
                $objTitulo = new Vtitulos($this->oConnection, $cod_titulo);
                $Periodos[$key]['periodo']['titulo'] = $objTitulo->nombre;
                $filial = new Vfiliales($this->oConnection, $cod_filial);
                $Periodos[$key]['periodo']['hs_catedra'] = $existe ? $planperiodo[0]['hs_reloj'] : 0;
                $Periodos[$key]['periodo']['modalidades'] = $plan->getPeriodosModalidadesFilial($cod_filial, $Periodos[$key]['periodo']['codigo']);
                foreach ($Periodos[$key]['periodo']['modalidades'] as $k => $value) {
                    $oTitulo = new Vtitulos($this->oConnection, $Periodos[$key]['periodo']['modalidades'][$k]['cod_titulo']);
                    $Periodos[$key]['periodo']['modalidades'][$k]['titulo'] = $oTitulo->nombre;
                }
            }
        }
        return $Periodos;
    }
}

?>