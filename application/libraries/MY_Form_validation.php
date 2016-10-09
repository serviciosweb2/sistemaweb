<?php
class MY_Form_validation extends CI_Form_validation {

    function __construct($rules = array()) {
        parent::__construct($rules);
    }

    /**
     * limpia un string recuperado como parametro cuando el POST es array (llega a la class en formato de string ["1","2","3"]) y formatea a array => (1,2,3)
     * 
     * @param string $param
     * @return array
     */
    private function clearParam($param) {
        $param = str_replace(array('"', '"', "[", "]"), "", $param);
        return explode(",", $param);
    }

    function validarCodigoPostal($codigopostal, $pais) {
        $retornoMensaje = '';

        if (!validaciones::validarCodigoPostal($codigopostal, $pais, $retornoMensaje)) {
            $this->CI->form_validation->set_message('validarCodigoPostal', $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarDocumentoIdentidad($numeroDocumento, $codigoTipo) {
        $formatoEsperado = '';
        if (!validaciones::validarDocumentoIdentidad($numeroDocumento, $codigoTipo, $formatoEsperado)) {
            $this->CI->form_validation->set_message('validarDocumentoIdentidad', lang('documento_invalido') . '(' . $formatoEsperado . ')');
            return false;
        } else {
            return true;
        }
    }
    
    function validarDniDuplicado($numeroDocumento, $codigoTipo) {
        //$formatoEsperado = '';
        //if (!validaciones::validarDocumentoIdentidad($numeroDocumento, $codigoTipo, $formatoEsperado)) {
        $this->CI->form_validation->set_message('validarDniDuplicado', "Prueba!!!");
        return false; 
//    return false;
        //} else {
        //    return true;
        //}
    }

    function validarNumeroRazonSocial($numeroRazonSocial, $codigoTipo) {
        if (!validaciones::validarNumeroRazonSocial($numeroRazonSocial, $codigoTipo)) {
            $this->CI->form_validation->set_message(lang('nro_razon_social_invalido'));
            return false;
        } else {
            return true;
        }
    }

    function validarFilialCupon($codigoCupon, $filial) {

        $retornoMensaje = '';

        $ci = &get_instance();
        $ci->load->database();
        $conexiongral = $ci->db;

        if (validaciones::validarFilialCupon($codigoCupon, $filial, $retornoMensaje, $conexiongral)) {
            return true;
        } else {
            $this->CI->form_validation->set_message('validarFilialCupon', $retornoMensaje);
            return false;
        }
    }

    function validarCupon($codigoCupon, $documento) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $retornoMensaje = '';

        if (validaciones::validarCupon($conexion, $codigoCupon, $documento, $retornoMensaje, null)) {
            return true;
        } else {
            $this->CI->form_validation->set_message('validarCupon', $retornoMensaje);
            return false;
        }
    }

    function validarHora($horaFin, $horaInicio) {

        $retornoMensaje = lang('hora_desde_menor_hora_hasta');
        if ($horaInicio < $horaFin) {

            return true;
        } else {
            $this->CI->form_validation->set_message('validarHora', $retornoMensaje);
            return false;
        }
    }

    function validarfecha($mes_factura) {
        $retornoMens = lang('fecha_cobro_no_corresponde_mes_en_curso');
        $fechaMes = formatearFecha_mysql($mes_factura);        
        $arrMes = explode("-", $fechaMes);
        if (($arrMes[1] == date('m') && $arrMes[0] == date("Y"))) {
            return true;
        } else {
            $this->CI->form_validation->set_message('validarfecha', $retornoMens);
            return false;
        }
    }

    function validarImporteFacturarCobrar($total, $ctactecheck) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $retorno = lang('importe_cobrar_mayor_seleccionado');
        $cod_cta = json_decode($ctactecheck, true);

        if (count($cod_cta) != 0) {
            if (validaciones::validarTotalFacturarCobrar($conexion, $total, $cod_cta)) {

                return true;
            } else {
                $this->CI->form_validation->set_message('validarImporteFacturarCobrar', $retorno);
                return false;
            }
        } else {
            $retorno = lang('importe_cobrar_no_puede_ser_0');
            $this->CI->form_validation->set_message('validarImporteFacturarCobrar', $retorno);
            return false;
        }
    }

    function validarImporteCobrar($total, $ctactecheck) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $cod_cta = json_decode($ctactecheck);

        if (count($cod_cta) != 0) {
            if (validaciones::validarTotalCobrar($conexion, $total, $cod_cta)) {

                return true;
            } else {
                $retorno = lang('importe_cobrar_mayor_seleccionado');
                $this->CI->form_validation->set_message('validarImporteCobrar', $retorno);
                return false;
            }
        } else {
            $retorno = lang('importe_cobrar_no_puede_ser_0');
            $this->CI->form_validation->set_message('validarImporteCobrar', $retorno);
            return false;
        }
    }

    function validarRazonDefault($ar) {
        $bandera = 0;
        $razones = json_decode($ar);
        foreach ($razones as $razon) {
            if ($razon == 1) {
                $bandera++;
            }
        }



        if ($bandera == 1) {
            return true;
        } else {
            $retorno = lang('razon_default_mayor_1');
            if ($bandera == 0) {
                $retorno = lang('seleccionar_razon_por_defecto');
            }
            $this->CI->form_validation->set_message('validarRazonDefault', $retorno);
            return false;
        }
    }

    function validarColorSalon($color, $cod_salon) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        if (validaciones::validarColorSalon($conexion, $color, $cod_salon)) {
            return true;
        } else {
            $retorno = lang('salon_con_color_elegido');
            $this->CI->form_validation->set_message('validarColorSalon', $retorno);
            return false;
        }
    }

    function validarMontoDescuento($descuento) {
        if ($descuento > '100') {
            $this->CI->form_validation->set_message('validarMontoDescuento', lang('descuento_superior_100'));
            return false;
        } else {
            return true;
        }
    }

    function validarConFechaHoy($fecha){
        $arrFecha = is_array($fecha) ? $fecha : array($fecha);
        foreach ($arrFecha as $fecha){
            if (trim($fecha) == ''){
                $retorno = lang('formato_fecha_invalido');
                $this->CI->form_validation->set_message('validarConFechaHoy', $retorno);
                return false;
            } else {
                $fecha = formatearFecha_mysql($fecha);
                if ($fecha < date('Y-m-d')){
                    $retorno = lang('la_fecha_no_puede_ser_menor_al_dia_actual');
                    $this->CI->form_validation->set_message('validarConFechaHoy', $retorno);
                    return false;
                }
            }
        }
        return true;
    }
    
    function validarFechaFormato($fecha) {
        if (trim($fecha) == ''){
            $retorno = lang('formato_fecha_invalido');
            $this->CI->form_validation->set_message('validarFechaFormato', $retorno);
            return false;
        } else {
            formatearFecha_mysql($fecha);
            $fecha = str_replace('/', '-', $fecha);
            $fechast = strtotime($fecha);
            if ($fechast === false || $fechast == '') {
                $retorno = lang('formato_fecha_invalido');
                $this->CI->form_validation->set_message('validarFechaFormato', $retorno);
                return false;
            } else {
                return true;
            }
        }
    }

    function validarFechaFinPosterior($fechafin, $fechaini) {
        $retornoMensaje = '';
        if (!validaciones::validarFechaFinPosterior($fechaini, $fechafin, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarFechaFinPosterior", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCuotasPlan($numero) {
        $numeroint = (int) $numero;
        if (is_int($numeroint)) {
            if ($numero == 0) {
                $retorno = lang('cuotas_financiacion_no_puede_0');
                $this->CI->form_validation->set_message('validarCuotasPlan', $retorno);
                return false;
            } elseif ($numero > 100) {
                $retorno = lang('cuotas_financiacion_no_puede_ser_mayor_100');
                $this->CI->form_validation->set_message('validarCuotasPlan', $retorno);
                return false;
            } else {
                return true;
            }
        } else {
            if ($numero == -1) {
                return true;
            } else {
                $retorno = lang('cuotas_financiacion_debe_ser_un_entero');
                $this->CI->form_validation->set_message('validarCuotasPlan', $retorno);
                return false;
            }
        }
    }

    function validarNombreApellido($palabra, $lang) {

        if (!validaciones::validarNombreApellido($palabra)) {
        	
            $retorno = lang('formato_nombre_invalido'); // $this->CI->lang->language[$lang];

            $this->CI->form_validation->set_message('validarNombreApellido', $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarInscriptosExamen($cod_examen, $inscriptos) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $cantaInscribir = count(json_decode($inscriptos));
        $objExamen = new Vexamenes($conexion, $cod_examen);
        $cupoRestante = $objExamen->cupoRestante();

        $cantidad = $cupoRestante[0]['cantRestantesInscribir'] - $cantaInscribir;

        if ($cantidad >= 0) {
            return true;
        }
        $retorno = lang('cupo_maximo_examen_superado');
        $this->CI->form_validation->set_message('validarInscriptosExamen', $retorno);
        return false;
    }

    function validarDuracionFechas($fechafin, $fechaini) {

        $date_parts1 = explode("/", $fechaini);
        $date_parts2 = explode("/", $fechafin);
        $start_date = gregoriantojd($date_parts1[1], $date_parts1[0], $date_parts1[2]);
        $end_date = gregoriantojd($date_parts2[1], $date_parts2[0], $date_parts2[2]);

        if (($end_date - $start_date) > 360) {
            $retorno = lang('fecha_fin_supera_anio_fecha_inicio');
            $this->CI->form_validation->set_message('validarDuracionFechas', $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarAsistenciaNull($codmathorario) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $objmathor = new Vmatriculas_horarios($conexion, $codmathorario);
        if ($objmathor->estado != null) {
            $retorno = lang('no_puede_cambiarse_horario');
            $this->CI->form_validation->set_message('validarAsistenciaNull', $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarImporteAbrirCaja($codCaja, $importe) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        if (validaciones::validarImporteAbrirCaja($conexion, $codCaja, $importe)) {
            return true;
        } else {
            $retorno = lang('importe_igual_ultimo_cierre_caja');
            $this->CI->form_validation->set_message('validarImporteAbrirCaja', $retorno);
            return false;
        }
    }

    function validarSaldoFacturar($codCtaCte) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        if (validaciones::validarSaldoFacturar($conexion, $codCtaCte)) {

            return true;
        } else {
            $retorno = lang('ctacte_sin_saldo_facturar');
            $this->CI->form_validation->set_message('validarSaldoFacturar', $retorno);
            return false;
        }
    }

    function validarAsistenciaCargada($fecha, $jshoras) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrJson = json_decode($jshoras, true);
        $arrayAsisComision = validaciones::validarAsistenciaCargada($conexion, formatearFecha_mysql($arrJson['fecha']), $jshoras);
        $comisiones = '';

        if ($arrayAsisComision != null) {
            foreach ($arrayAsisComision as $key => $arrAsistencias) {
                $myComision = new Vcomisiones($conexion, $key);
                $comisiones .=$myComision->nombre . ' ';
            }
            $retorno = lang('asistencias_cargadas_dia_seleccionado') . ' ' . $comisiones;
            $this->CI->form_validation->set_message('validarAsistenciaCargada', $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarFeriadoCargado($fecha, $jshoras) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        if (validaciones::validarFeriadoCargado($conexion, $fecha, $jshoras)) {
            return true;
        } else {
            $retorno = lang('feriado_asignado_mismo_dia');
            $this->CI->form_validation->set_message('validarFeriadoCargado', $retorno);
            return false;
        }
    }

    function validarCajaUsuario($codcaja, $codusuario) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        if (validaciones::validarCajaUsuario($conexion, $codusuario, $codcaja)) {
            return true;
        } else {
            $retorno = lang('usuario_movimientos_caja');
            $this->CI->form_validation->set_message('validarCajaUsuario', $retorno);
            return false;
        }
    }

    function validarImporteMayorA($valor, $valorBase) {
        if ((float) $valor > $valorBase) {
            return true;
        } else {
            $this->CI->form_validation->set_message('validarImporteMayorA', lang("el_importedebe_ser_mayor_a") . " " . $valorBase);
            return false;
        }
    }

    function validarExpresionTotal($total) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $separador = "\\" . $filial['moneda']['separadorDecimal'];
        $patron = "/^([0-9]{0,10})$separador?([0-9]{1,2})$/ ";
        if (preg_match($patron, $total)) {
            return true;
        } else {
            $retorno = lang('importe_invalido');
            $this->CI->form_validation->set_message('validarExpresionTotal', $retorno);
            return false;
        }
    }

    function validarSaldoNotaCredito($valor, $cod_ctacte) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $separador = $filial['moneda']['separadorDecimal'];
        $conexion = $ci->load->database($cod_filial, true);
        $saldo = str_replace($separador, '.', $valor);
        if (validaciones::validarSaldoNotaCredito($conexion, $saldo, $cod_ctacte)) {
            return true;
        } else {
            $retorno = lang('importe_supera_nota_credito');
            $this->CI->form_validation->set_message('validarSaldoNotaCredito', $retorno);
            return false;
        }
    }

    function validarPassword($password) {
        $patron = '/(?=[a-zA-Z0-9]*?[A-Z])(?=[a-zA-Z0-9]*?[a-z])(?=[a-zA-Z0-9]*?[0-9])[a-zA-Z0-9]{6,}/';
        if (preg_match($patron, $password)) {
            return true;
        } else {
            $retorno = lang('password_invalida');
            $this->CI->form_validation->set_message('validarPassword', $retorno);
            return false;
        }
    }

    function validarFechaHabil($fecha) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $fecha = formatearFecha_mysql($fecha);
        $retornoMensaje = '';
        if (!validaciones::validarFechaHabil($fecha, $conexion, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarFechaHabil", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    /**
     * valida que una fecha se encuentre dentro de un intervalo
     * 
     * @param type $fechaPais   La fecha ingresada en el input
     * @param type $fechas      El intervalo de fechas separadas por coma (fechaDesde, fechaHasta)
     * @return boolean
     */
    function validarIntervaloDeFechas($fechaPais, $fechas) {
        $arrTemp = explode(",", $fechas);
        $fechaDesde = isset($arrTemp[0]) ? $arrTemp[0] : null;
        $fechaHasta = isset($arrTemp[1]) ? $arrTemp[1] : null;
        $fecha = formatearFecha_mysql($fechaPais);
        $retornoMensaje = '';
        if (!validaciones::validarIntervaloDeFechas($fecha, $fechaDesde, $fechaHasta, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarIntervaloDeFechas", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarDia($diaHasta, $diaDesde) {
        $dia_desde = strtotime($diaDesde);
        $dia_hasta = strtotime($diaHasta);
        if ($dia_hasta < $dia_desde) {
            $retorno = lang('dia_error');
            $this->CI->form_validation->set_message('validarDia', $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarMatricularCursoPeriodo($codplan, $jscursoperiodo) {
        $arr = json_decode($jscursoperiodo, true);
        $arrPeriodos = array();
        foreach ($arr['periodos'] as $key => $value) {
            $modalidad = isset($value['modalidad']) ? $value['modalidad'] : '';
            $periodo = isset($value['seleccionado']) ? $key : '';
            if ($periodo != '') {
                $arrPeriodos[] = array('periodo' => $periodo, 'modalidad' => $modalidad);
            }
        }
        $retornoMensaje = '';
        if (!validaciones::validarMatricularCursoAlumno($codplan, $arr['cod_alumno'], $arrPeriodos, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarMatricularCursoPeriodo", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarMatricularAlumnoCurso($cod_alumno, $arrCodigo) {
        $arrayCodigo = json_decode($arrCodigo, true);
        $cod_plan_academico = $arrayCodigo['cod_plan_academico'];

        if (!validaciones::validarMatricularPlanAcademico($cod_alumno, $cod_plan_academico)) {
            $retornoMensaje = 'No se puede matricular en el plan academico';
            $this->CI->form_validation->set_message("validarMatricularAlumnoCurso", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarExistenciaEstadoCertificadoAprobar($cod_certificante, $cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarExistenciaEstadoCertificadoAprobar($cod_certificante, $cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarExistenciaEstadoCertificadoAprobar", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarPropiedadesImpresionCertificado($cod_certificante, $cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarPropiedadesImpresionCertificado($cod_certificante, $cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarPropiedadesImpresionCertificado", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarExistenciaEstadoCertificadoRevertir($cod_certificante, $cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarExistenciaEstadoCertificadoRevertir($cod_certificante, $cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarExistenciaEstadoCertificadoRevertir", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }
    
    
    function validarExistenciaEstadoCertificadoCancelar($cod_certificante, $cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarExistenciaEstadoCertificadoCancelar($cod_certificante, $cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarExistenciaEstadoCertificadoCancelar", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarExistenciaEstadoCertificadoHabilitar($cod_certificante, $cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarExistenciaEstadoCertificadoHabilitar($cod_certificante, $cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarExistenciaEstadoCertificadoHabilitar", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarMatriculaPeriodoHabilitada($cod_estado_academico) {
        $retornoMensaje = '';
        if (!validaciones::validarMatriculaPeriodoHabilitada($cod_estado_academico, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarMatriculaPeriodoHabilitada", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarAlumnoHabilitado($cod_alumno) {
        $retornoMensaje = '';
        if (!validaciones::validarAlumnoHabilitado($cod_alumno, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarAlumnoHabilitado", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCertificadosProcesados($cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarCertificadosProcesados($cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarCertificadosProcesados", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarMatriculasAlta($cod_plan_academico, $cod_alumno) {
        $retornoMensaje = '';
        if (!validaciones::validarMatriculasAlta($cod_plan_academico, $cod_alumno, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarMatriculasAlta", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarMatriculasBaja($cod_plan_academico, $cod_alumno) {
        $retornoMensaje = '';
        if (!validaciones::validarMatriculasBaja($cod_plan_academico, $cod_alumno, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarMatriculasBaja", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarValorPorcentaje($valor) {
        if ($valor > '100') {
            $this->CI->form_validation->set_message('validarValorPorcentaje', lang('porcentaje_superior_100'));
            return false;
        } else {
            return true;
        }
    }

    function validarNombreComision($nombre) {
        $patron = "/^([A-Za-zñáéíóúäëïöüÑÄËÏÖÜÁÉÍÓÚ´+0-9])+$/";
        if (preg_match($patron, $nombre)) {
            return true;
        } else {
            $retorno = lang('nombre_invalido');
            $this->CI->form_validation->set_message('validarNombreComision', $retorno);
            return false;
        }
    }

    function validarMatriculaPeriodoAlta($cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarMatriculaPeriodoAlta($cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarMatriculaPeriodoAlta", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarMatriculaPeriodoBaja($cod_matricula_periodo) {
        $retornoMensaje = '';
        if (!validaciones::validarMatriculaPeriodoBaja($cod_matricula_periodo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarMatriculaPeriodoBaja", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarPeriodicidad($unidadTiempo, $valor) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $periodos = Vconfiguracion::getValorConfiguracion($conexion, null, 'PeriodoCtacte');
        foreach ($periodos as $periodo) {
            if ($periodo['unidadTiempo'] == $unidadTiempo && $periodo['valor'] == $valor) {

                $retorno = lang('periodicidad_existente');
                $this->CI->form_validation->set_message('validarPeriodicidad', $retorno);
                return false;
            } else {

                return true;
            }
        }
    }

    function validarHoraSalonExamen($horaInicio, $arrDatos) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrayDatos = json_decode($arrDatos, true);
        $salones = array();
        foreach ($arrayDatos['salones'] as $row) {
            $salon = json_decode($row, true);
            $salones[] = $salon['cod_salon'];
        }

        $permitirGuardar = Vexamenes::getVerificarSalonHoraExamen($conexion, $salones, $arrayDatos['fecha'], $horaInicio, $arrayDatos['horaFin']);

        if ($permitirGuardar[0]['cantSalones'] > 0) {
            $retorno = lang('no_puede_cargarse_examen') . ' ' . '(' . $horaInicio . ' - ' . $arrayDatos['horaFin'] . ') ' . lang('codigo') . ': ' . $permitirGuardar[0]['cod_examen'];
            $this->CI->form_validation->set_message('validarHoraSalonExamen', $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarBajaCtaCte($codctacte) {
        $retornoMensaje = '';
        if (!validaciones::validarBajaCtaCte($codctacte, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarBajaCtaCte", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarBajaExamen($cod_examen) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrCondiciones = array(
            "cod_examen" => $cod_examen
        );
        $inscriptos = Vexamenes_estado_academico::listarExamenes_estado_academico($conexion, $arrCondiciones);

        $baja = true;
        foreach ($inscriptos as $alumno) {
            if ($alumno['estado'] <> 'pendiente' && $alumno['estado'] <> 'baja') {
                $baja = false;
            }
        }
        if ($baja == false) {
            $retorno = lang('no_puede_dar_baja_examen');
            $this->CI->form_validation->set_message("validarBajaExamen", $retorno);
            return false;
        } else {
            return true;
        }
    }

    function validarEstadoAcademicoInscribirExamen($codEstadoAcademico, $codExamen) {
        $codEstadoAcademico = $this->clearParam($codEstadoAcademico);
        $retornoMensaje = '';
        $resp = true;
        foreach ($codEstadoAcademico as $estadoAcademico) {
            $resp = $resp && validaciones::validarEstadoAcademicoInscribirExamen($estadoAcademico, $codExamen, $retornoMensaje);
        }
        if (!$resp) {
            $this->CI->form_validation->set_message("validarEstadoAcademicoInscribirExamen", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarAlumnoInscriptoExamen($cod_estado_academico, $cod_examen) {
        $retornoMensaje = '';
        if (!validaciones::validarAlumnoInscriptoExamen($cod_estado_academico, $cod_examen, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarAlumnoInscriptoExamen", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarNotaExamen($nota, $key) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrConfigNotasExamen = Vconfiguracion::getValorConfiguracion($conexion, null, 'configuracionNotaExamen');
        $separador = $filial['moneda']['separadorDecimal'];
        $nota_examen = str_replace($separador, ".", $nota);
        if ($nota == '') {
            return true;
        } else if ($nota_examen >= $arrConfigNotasExamen['numero_desde'] && $nota_examen <= $arrConfigNotasExamen['numero_hasta']) {
            return true;
        } else {
            $retornoMensaje = lang($key) . ': ' . lang('nota_mayor_menor');
            $this->CI->form_validation->set_message("validarNotaExamen", $retornoMensaje);
            return false;
        }
    }

    function validarExistenciaTipoDniNumero($tipo, $documento) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condicion = array(
            "tipo" => $tipo,
            "documento" => $documento
        );
        $alumno = Valumnos::listarAlumnos($conexion, $condicion);
        if (count($alumno) > 0) {
            $retornoMensaje = lang('alumno_ya_existe');
            $this->CI->form_validation->set_message("validarExistenciaTipoDniNumero", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarBajaInscripcionExamen($cod_inscripcion, $cod_filial = null) {
        $ci = &get_instance();

		if (is_null($cod_filial) || $cod_filial === false) {
			$filial = $ci->session->userdata('filial');
			$cod_filial = $filial['codigo'];
		}
        
        $conexion = $ci->load->database($cod_filial, true);
        $myExamenEstadoAcademico = new Vexamenes_estado_academico($conexion, $cod_inscripcion);

        if ($myExamenEstadoAcademico->estado == 'pendiente') {

            return true;
        } else {

            $retornoMensaje = lang('inscripcion_baja');
            $this->CI->form_validation->set_message("validarBajaInscripcionExamen", $retornoMensaje);
            return false;
        }
    }

    function validaCtaCteRefinanciar($codctacte) {
        $retornoMensaje = '';
        if (!validaciones::validaCtaCteRefinanciar($codctacte, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validaCtaCteRefinanciar", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarNumeroIdentificacion($tipo_identificacion, $numero_identificacion) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condicion = array(
            "tipo_documentos" => $tipo_identificacion,
            "documento" => $numero_identificacion
        );
        $arrRazones_sociales = Vrazones_sociales::listarRazones_sociales($conexion, $condicion);
        if (count($arrRazones_sociales) > 0) {
            $retornoMensaje = lang('razon_social_ya_existe');
            $this->CI->form_validation->set_message("validarNumeroIdentificacion", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarNotaAprubaExamenAlbetico($nota_aprueba, $escala_notas) {
        $arrEscalaNotas = explode(",", $escala_notas);
        $igualNota = false;
        foreach ($arrEscalaNotas as $k => $value) {
            $arrEscalaNotas[$k] = trim($value);
        }
        foreach ($arrEscalaNotas as $nota) {
            if ($nota == $nota_aprueba) {
                $igualNota = true;
            }
        }

        if ($igualNota == false) {
            $retornoMensaje = lang('nota_aprueba_examen');
            $this->CI->form_validation->set_message("validarNotaAprubaExamenAlbetico", $retornoMensaje);
            return false;
        }
    }

    function validarNotaAprubaExamenNumerico($notaAprueba, $jsonNotas) {
        $arrayNotas = json_decode($jsonNotas, true);
        $notaIgual = false;

        for ($i = $arrayNotas['nota_desde']; $i <= $arrayNotas['nota_hasta']; $i++) {
            if ($notaAprueba == $i) {
                $notaIgual = true;
            }
        }

        if ($notaIgual == false) {
            $retornoMensaje = lang('nota_aprueba_examen');
            $this->CI->form_validation->set_message("validarNotaAprubaExamenNumerico", $retornoMensaje);
            return false;
        }
    }

    function validarTipoFormatoNota($tipoFormato) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrConfiguracionExamen = Vconfiguracion::getValorConfiguracion($conexion, null, 'configuracionNotaExamen');
        if ($arrConfiguracionExamen['formato_nota'] == '') {
            return true;
        } else if ($arrConfiguracionExamen['formato_nota'] == $tipoFormato) {
            return true;
        } else {
            $retornoMensaje = lang('eligio_configuracion_anteriormente');
            $this->CI->form_validation->set_message("validarTipoFormatoNota", $retornoMensaje);
            return false;
        }
    }

    function validarAbreviaturaCurso($abreviatura, $cod_curso) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrAbreviaturas = Vcursos::getListaAbreviaturaCursosHabilitados($conexion);
        $arrCurso = Vcursos::getListaAbreviaturaCursosHabilitados($conexion, $cod_curso);
        $iguales = '';
        if ($abreviatura == $arrCurso[0]['abreviatura']) {
            $iguales = false;
            return true;
        } else {
            foreach ($arrAbreviaturas as $abreviatura_curso_habilitado) {
                if ($abreviatura_curso_habilitado['abreviatura'] == $abreviatura) {
                    $iguales = true;
                }
            }
        }


        if ($iguales) {
            $retornoMensaje = lang('abreviatura_ya_utilizada');
            $this->CI->form_validation->set_message("validarAbreviaturaCurso", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarModificarRazon($cod_razon) {
        $retornoMensaje = '';
        if (!validaciones::validarModificarRazon($cod_razon, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarModificarRazon", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCambioEstadoCompra($cod_compra) {
        $retornoMensaje = '';
        if (!validaciones::validarCambioEstadoCompra($cod_compra, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarCambioEstadoCompra", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarPresupuestosVigente($nombre, $cod_plan) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $objPlanPago = new Vplanes_pago($conexion, $cod_plan);
        $dejarModificar = $objPlanPago->getVigenciasPresupuesto();

        if (count($dejarModificar) > 0) {
            $retornoMensaje = lang('plan_vigente');
            $this->CI->form_validation->set_message("validarPresupuestosVigente", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarDiasReceso($fechaDesde, $jsonFechas) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrayFechas = json_decode($jsonFechas, true);
        $horarios = Vhorarios::getHorariosEntreDias($conexion, $arrayFechas['fecha_desde'], $arrayFechas['fecha_hasta']);
        $fechas = '';
        if (count($horarios) > 0) {
            $i = 0;
            foreach ($horarios as $valor) {
                $i++;
                $separador = ',';
                if ($i == count($horarios)) {
                    $separador = '';
                }
                $fechas .= formatearFecha_pais($valor['dia']) . $separador . ' ';
            }
            $retornoMensaje = 'Los siguientes dias tienen comisiones cursando, ' . $fechas;
            $this->CI->form_validation->set_message("validarDiasReceso", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarExistenciaMail($mail, $cod_alumno) {
        $retornoMensaje = '';
        if (!validaciones::validarExistenciaMail($mail, $cod_alumno, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarExistenciaMail", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCodigoInternoTerminal($codigo_interno, $js) {
        $retornoMensaje = '';
        $arrcodigos = json_decode($js, true);

        if (!validaciones::validarCodigoInternoTerminal($codigo_interno, $arrcodigos['terminal'], $arrcodigos['operador'], $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarCodigoInternoTerminal", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCuponTerminal($cupon, $codigo) {
        $retornoMensaje = '';
        if (!validaciones::validarCuponTerminal($cupon, $codigo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarCuponTerminal", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarAutorizacionTerminal($codigo_autorizacion, $codigo) {
        $retornoMensaje = '';
        if (!validaciones::validarAutorizacionTerminal($codigo_autorizacion, $codigo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarAutorizacionTerminal", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCobroCajaMedio($cod_caja, $cod_medio) {
        $retornoMensaje = '';
        if (!validaciones::validarCobroCajaMedio($cod_caja, $cod_medio, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarCobroCajaMedio", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarExistenciaDniAlumnoModificado($tipo, $json_arrAlumno) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrAlumno = json_decode($json_arrAlumno, true);
        $documento = $arrAlumno['documento'];
        $cod_alumno = $arrAlumno['cod_alumno'];
        $condiciones = array(
            "codigo" => $cod_alumno
        );
        $alumno = Valumnos::listarAlumnos($conexion, $condiciones);
        if ($alumno[0]['documento'] == $documento) {
            return true;
        } else {
            $arrCondicion = array(
                "tipo" => $tipo,
                "documento" => $documento
            );
            $alumno = Valumnos::listarAlumnos($conexion, $arrCondicion);
            if (count($alumno) > 0) {
                $retornoMensaje = lang('alumno_ya_existe');
                $this->CI->form_validation->set_message("validarExistenciaDniAlumnoModificado", $retornoMensaje);
                return false;
            } else {
                return true;
            }
        }
    }

    function validarEliminarImputacion($codigo) {
        $retornoMensaje = '';
        if (!validaciones::validarEliminarImputacion($codigo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarEliminarImputacion", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarModificarCobro($codigo) {
        $retornoMensaje = '';
        if (!validaciones::validarModificarCobro($codigo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarModificarCobro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarConfirmarCobro($codigo, $cod_usuario) {
        $retornoMensaje = '';
        if (!validaciones::validarConfirmarCobro($codigo, $cod_usuario, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarConfirmarCobro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarAnularCobro($cod_cobro, $cod_usuario) {
        $retornoMensaje = '';
        if (!validaciones::validarAnulacionCobro($cod_cobro, $cod_usuario, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarAnularCobro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarImporteCobro($importe, $codigo) {

        $retornoMensaje = '';
        if (!validaciones::validarImporteCobro($importe, $codigo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarImporteCobro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarFeriadoRecesoDia($fecha) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $feriados = Vhorarios::getFeriadosFecha($conexion, formatearFecha_mysql($fecha));
        $recesosFilial = Vhorarios::getRecesosFilialFechas($conexion, formatearFecha_mysql($fecha));
        $mostrarMensaje = '';

        if (count($feriados) > 0) {
            $mostrarMensaje = lang('existe_feriado_horario') . '<br>';
        }
        if (count($recesosFilial) > 0) {
            $mostrarMensaje .= lang('existe_receso_filial_horario');
        }
        if ($mostrarMensaje != '') {
            $this->CI->form_validation->set_message("validarFeriadoRecesoDia", $mostrarMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarFeriadoRecesoDesdeHasta($fechaHasta, $fechaDesde) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $feriados = Vhorarios::getFeriadosFecha($conexion, formatearFecha_mysql($fechaDesde), formatearFecha_mysql($fechaHasta));
        $recesosFilial = Vhorarios::getRecesosFilialFechas($conexion, formatearFecha_mysql($fechaDesde), formatearFecha_mysql($fechaHasta));
        $mostrarMensaje = '';

        if (count($feriados) > 0) {
            $mostrarMensaje = lang('existe_feriado_horario') . '<br>';
        }
        if (count($recesosFilial) > 0) {
            $mostrarMensaje .= lang('existe_receso_filial_horario');
        }
        if ($mostrarMensaje != '') {
            $this->CI->form_validation->set_message("validarFeriadoRecesoDia", $mostrarMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarFeriadoConHorario($fecha) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condicion = array(
            "dia" => formatearFecha_mysql($fecha),
            "baja" => 0
        );
        $arrHorarios = Vhorarios::listarHorarios($conexion, $condicion);
        if (count($arrHorarios) > 0) {
            $retornoMensaje = lang('feriados_horarios_con_dias');
            $this->CI->form_validation->set_message("validarFeriadoConHorario", $retornoMensaje);
            return false;
        }
    }

    function validarNombreFiltro($nombre_filtro) {
        if (strstr($nombre_filtro, ',')) {
            $retornoMensaje = lang('nombre_filtro_sin_coma');
            $this->CI->form_validation->set_message("validarNombreFiltro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarHorariosComision($cod_comision) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condicion = array(
            "cod_comision" => $cod_comision,
//            "dia >=" => date("Y-m-d"),
            "baja" => 0
        );
        $horarios = Vhorarios::listarHorarios($conexion, $condicion);
        if (count($horarios) > 0) {
            return true;
        } else {
            $retornoMensaje = 'La comision no posee horarios, no puede asignarla para ver en web';
            $this->CI->form_validation->set_message("validarHorariosComision", $retornoMensaje);
            return false;
        }
    }

    function validarHorarioDiaComision($cod_comision, $jsonDatos) {
        $arrDatos = json_decode($jsonDatos, true);
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        if ($arrDatos['codigo_horario'] != -1) {
            $cod_horario = $arrDatos['codigo_horario'];
        } else {
            $cod_horario = '';
        }
        $horarios = Vhorarios::validarHorario($conexion, formatearFecha_mysql($arrDatos['fechaDesde']), $arrDatos['hora_desde'], $arrDatos['hora_hasta'], $cod_comision, null, null, $cod_horario, $arrDatos['cod_materia']);
        if (count($horarios) > 0) {
            $retornoMensaje = lang('evento_coincide_con_hora_dia');
            $this->CI->form_validation->set_message("validarHorarioDiaComision", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCaja($cod_caja) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $condicion = array(
            "codigo" => $cod_caja
        );
        $arrCaja = Vcaja::listarCaja($conexion, $condicion);
        if ($arrCaja[0]['estado'] == 'abierta') {
            return true;
        } else {
            $retornoMensaje = lang('pagos_caja_cerrada');
            $this->CI->form_validation->set_message("validarCaja", $retornoMensaje);
            return false;
        }
    }

    function validarSalonHorario($cod_salon, $json) {
        $arrDatos = json_decode($json, true);
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        if ($arrDatos['codigo_horario'] != -1) {
            $cod_horario = $arrDatos['codigo_horario'];
        } else {
            $cod_horario = '';
        }
        $horarios = Vhorarios::validarHorario($conexion, formatearFecha_mysql($arrDatos['fechaDesde']), $arrDatos['hora_desde'], $arrDatos['hora_hasta'], null, $cod_salon, null, $cod_horario, $arrDatos['cod_materia']);
        if (count($horarios) > 0) {
            $retornoMensaje = lang('horarios_mismo_salon');
            $this->CI->form_validation->set_message("validarSalonHorario", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarSalonHorarioSerie($fechaHasta, $arrayJson) {
        $arrDatos = json_decode($arrayJson, true);
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        if ($arrDatos['codigo_horario'] != -1) {
            $cod_horario = $arrDatos['codigo_horario'];
        } else {
            $cod_horario = '';
        }
        $horarios = Vhorarios::validarHorario($conexion, formatearFecha_mysql($arrDatos['fechaDesde']), $arrDatos['hora_desde'], $arrDatos['hora_hasta'], null, $arrDatos['cod_salon'], formatearFecha_mysql($fechaHasta), $cod_horario, $arrDatos['cod_materia']);
        if (count($horarios) > 0) {
            $retornoMensaje = lang('horarios_mismo_salon_serie');
            $this->CI->form_validation->set_message("validarSalonHorarioSerie", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarNombreSalon($nombreSalon, $cod_salon) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        if ($cod_salon != -1) {
            $codigo_salon = $cod_salon;
        } else {
            $codigo_salon = '';
        }
        $salones = Vsalones::validarNombreSalon($conexion, $nombreSalon, $codigo_salon);
        if (count($salones) > 0) {
            $retornoMensaje = lang('existe_nombre_salon');
            $this->CI->form_validation->set_message("validarNombreSalon", $retornoMensaje);
            return false;
        }
    }

    function validarFechaPeriodoCerrado($fechaCobro){
        $retornoMensaje = '';
        if (!validaciones::validarFechaPeriodoCerrado($fechaCobro, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarFechaPeriodoCerrado", $retornoMensaje);
            return false;
        } else {
            return true;
        }        
    }
    
    
    function validarFechaCobro($fecha, $datos) {
        $retornoMensaje = '';
        $arrdatos = json_decode($datos, true);

        if (!validaciones::validarFechaCobro($fecha, $arrdatos['cod_medio'], $arrdatos['caja'], $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarFechaCobro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarPeriodoCobro($codigo) {
        $retornoMensaje = '';
        if (!validaciones::validarPeriodoCobro($codigo, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarPeriodoCobro", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarHorarioCicloComision($cod_comision, $jsonFechas) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $arrFechas = json_decode($jsonFechas, true);
        $codigo_comision = '';
        if (isset($arrFechas['cod_comision'])) {
            $codigo_comision = $arrFechas['cod_comision'];
        } else {
            $codigo_comision = $cod_comision;
        }
        $myComision = new Vcomisiones($conexion, $codigo_comision);
        $listadoCiclos = Vciclos::getCiclos($conexion, $cod_filial, $myComision->ciclo, false);
        $cicloAnioDesde = date('Y', strtotime($listadoCiclos[0]['fecha_inicio_ciclo']));
        $cicloAnioHasta = date('Y', strtotime($listadoCiclos[0]['fecha_fin_ciclo']));
        $retorno = true;
        $temp = explode("/", $arrFechas['fecha_desde']);
        $año_evento = $temp[2];        
        if (isset($arrFechas['fecha_hasta'])) {
            $temp = explode("/", $arrFechas['fecha_hasta']);
            $año_evento_hasta = $temp[2];
            if ($año_evento > $cicloAnioDesde || $año_evento > $cicloAnioHasta) {
                $retorno = false;
            }
        } else {
            if ($cicloAnioDesde > $año_evento) {
                $retorno = false;
            }
        }
        if ($retorno) {
            return true;
        } else {
            $retornoMensaje = lang('ciclo_comision_no_pertenece_ciclo_lectivo');
            $this->CI->form_validation->set_message("validarHorarioCicloComision", $retornoMensaje);
            return false;
        }
    }

    function validarRazonSocialRegistrada($codigoRazonSocial, $jsonTipoyNumero) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $conexion = $ci->load->database($filial['codigo'], true);
        $datos = @json_decode($jsonTipoyNumero);
        if (isset($datos[0]) && isset($datos[1]) && $datos[0] <> '' && trim($datos[1]) <> '') {
            $condiciones = array();
            $condiciones["tipo_documentos"] = $datos[0];
            $condiciones["documento"] = $datos[1];
            $condiciones["codigo <>"] = $codigoRazonSocial;
            $ocurrencias = Vrazones_sociales::listarRazones_sociales($conexion, $condiciones, null, null, null, true);
            if ($ocurrencias > 0) {
                //$this->CI->form_validation->set_message("validarRazonSocialRegistrada", lang("tipo_y_numero_de_identificador_fiscal_repetidos"));
                $this->CI->form_validation->set_message("validarRazonSocialRegistrada", lang("razon_social_ya_existe"));
                return false;
            } else {
                return true;
            }
        } else {
            $this->CI->form_validation->set_message("validarRazonSocialRegistrada", lang("tipo_y_o_numero_de_identificador_fiscal_invalidos"));
            return false;
        }
    }

    function validarRepeticionHorariosFilial($fechaHasta, $jsonDatos) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $arrDatos = json_decode($jsonDatos, true);
        $diasRepetecion = $arrDatos['dias_repeticion'];
        $configuracionHorarios = Vconfiguracion::getValorConfiguracion($conexion, null, 'HorariosDeAtencion');

        $nuevoArray = array();
        foreach ($configuracionHorarios as $dia => $horarios) {

            switch ($dia) {
                case 'lunes':
                    $nuevoArray[1] = $horarios;
                    break;
                case 'martes':
                    $nuevoArray[2] = $horarios;
                    break;
                case 'miercoles':
                    $nuevoArray[3] = $horarios;
                    break;
                case 'jueves':
                    $nuevoArray[4] = $horarios;
                    break;
                case 'viernes':
                    $nuevoArray[5] = $horarios;
                    break;
                case 'sabado':
                    $nuevoArray[6] = $horarios;
                    break;
                case 'domingo':
                    $nuevoArray[7] = $horarios;
                    break;
                default:
                    break;
            }
        }
        $arrDiasSemana = array(
            1 => lang('dia_lunes'),
            2 => lang('dia_martes'),
            3 => lang('dia_miercoles'),
            4 => lang('dia_jueves'),
            5 => lang('dia_viernes'),
            6 => lang('dia_sabado'),
            7 => lang('dia_domingo'),
        );
        $mostrarDias = '';
        $validarHorario = true;
        foreach ($nuevoArray as $dia => $horarios) { //valido primero que los dias de repeticion de la serie, la filial no este cerrada.
            foreach ($diasRepetecion as $dia_repeticion) {
                if ($horarios['cerrado'] == 1 && $dia == $dia_repeticion) {
                    $mostrarDias .= $arrDiasSemana[$dia_repeticion] . ' ';
                    $validarHorario = false;
                }
            }
        }

        $horaDesde = $arrDatos['hora_desde'];
        $horaHasta = $arrDatos['hora_hasta'];
        if ($validarHorario) { //si la filial tiene todos esos dias abiertos valida que el horario indicado para el evento sea los horarios de apertura y cierra de la filial.
            $retorno = true;

            foreach ($nuevoArray as $key => $horario_filial) {
                foreach ($diasRepetecion as $value) {
                    if ($horario_filial['cerrado'] == 0 && $key == $value) {
                        $abre1 = $horario_filial['e1'] . ':' . '00';
                        $cierra1 = $horario_filial['s1'] . ':' . '00';

                        if ($horaDesde < $abre1 || $horaHasta > $cierra1) {
                            $retorno = false;
                        }
                    }
                }
            }
            if ($retorno) {
                return true;
            } else {
                $retornoMensaje = lang('horarios_filial_no_coinciden');
                $this->CI->form_validation->set_message("validarRepeticionHorariosFilial", $retornoMensaje);
                return false;
            }
        } else {
            $retornoMensaje = $mostrarDias . ' ' . lang('filial_permanece_cerrada');
            $this->CI->form_validation->set_message("validarRepeticionHorariosFilial", $retornoMensaje);
            return false;
        }
    }

    function validarHorarioFilial($fechaDesde, $jsonHoras) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $configuracionHorarios = Vconfiguracion::getValorConfiguracion($conexion, null, 'HorariosDeAtencion');

        $nuevoArray = array();
        foreach ($configuracionHorarios as $dia => $horarios) {

            switch ($dia) {
                case 'lunes':
                    $nuevoArray[1] = $horarios;
                    break;
                case 'martes':
                    $nuevoArray[2] = $horarios;
                    break;
                case 'miercoles':
                    $nuevoArray[3] = $horarios;
                    break;
                case 'jueves':
                    $nuevoArray[4] = $horarios;
                    break;
                case 'viernes':
                    $nuevoArray[5] = $horarios;
                    break;
                case 'sabado':
                    $nuevoArray[6] = $horarios;
                    break;
                case 'domingo':
                    $nuevoArray[0] = $horarios;
                    break;
                default:
                    break;
            }
        }
        $arrDiasSemana = array(
            0 => lang('dia_domingo'),
            1 => lang('dia_lunes'),
            2 => lang('dia_martes'),
            3 => lang('dia_miercoles'),
            4 => lang('dia_jueves'),
            5 => lang('dia_viernes'),
            6 => lang('dia_sabado')
        );
        $arrHoras = json_decode($jsonHoras, true);

        $fechaDesdeMYsql = formatearFecha_mysql($fechaDesde);
        $year = date('Y', strtotime($fechaDesdeMYsql));
        $month = date('m', strtotime($fechaDesdeMYsql));
        $day = date('d', strtotime($fechaDesdeMYsql));

        $diaSemana = date("w", mktime(0, 0, 0, $month, $day, $year));
        $validarHorario = true;
        $mostrarDia = '';
        foreach ($nuevoArray as $dia => $horario) {
            if ($horario['cerrado'] == 1 && $dia == $diaSemana) {
                $validarHorario = false;
                $mostrarDia = $arrDiasSemana[$diaSemana];
            }
        }
        if ($validarHorario) {
            $retorno = true;
            $horaDesde = $arrHoras['horaDesde'];
            $horaHasta = $arrHoras['horaHasta'];

            foreach ($nuevoArray as $key => $horario_filial) {
                if ($horario_filial['cerrado'] == 0 && $key == $diaSemana) {
                    $abre1 = $horario_filial['e1'] . ':' . '00';
                    $cierra1 = $horario_filial['s1'] . ':' . '00';

                    if ($horaDesde < $abre1 || $horaHasta > $cierra1) {
                        $retorno = false;
                    }
                }
            }
            if ($retorno) {
                return true;
            } else {
                $retornoMensaje = lang('horarios_filial_no_coinciden');
                $this->CI->form_validation->set_message("validarHorarioFilial", $retornoMensaje);
                return false;
            }
        } else {
            $retornoMensaje = $mostrarDia . ' ' . lang('filial_permanece_cerrada');
            $this->CI->form_validation->set_message("validarHorarioFilial", $retornoMensaje);
            return false;
        }
    }

    function validarPrimerPagoMatricula($cod_concepto, $jsdatos) {

        $arr = json_decode($jsdatos, true);
        $retornoMensaje = '';
        if (!validaciones::validarPrimerPagoMatricula($arr['fecha'], $arr['plan'], $arr['financiacion'], $cod_concepto, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarPrimerPagoMatricula", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCantidaTipoSalon($tipo_salon, $cod_salon) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);
        $codigo = '';
        if ($cod_salon != -1) {
            $codigo = $cod_salon;
        } else {
            $codigo = '';
        }
        $cantidadSalon = Vsalones::cantidadSalonesPorTipo($conexion, $tipo_salon, $codigo);

        if ($cantidadSalon[0]['cantidad_salones_tipo'] >= 5) {
            $retornoMensaje = 'Ah superado el limite de salones a crear';
            $this->CI->form_validation->set_message("validarCantidaTipoSalon", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarPlanPagoPeriodos($plan_pago, $jsperiodos) {
        $arrPeriodos = json_decode($jsperiodos, true);
        $periodos = array();
        $retornoMensaje = '';
        foreach ($arrPeriodos['periodos'] as $key => $value) {
            $periodo = isset($value['seleccionado']) ? $key : '';
            if ($periodo != '') {
                $periodos[] = $periodo;
            }
        }
        if (!validaciones::validarPlanPagoPeriodos($plan_pago, $arrPeriodos['cod_plan'], $periodos, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarPlanPagoPeriodos", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarFinanciaciones($financiaciones) {
        $retorno = true;
        $retornoMensaje = '';
        if (count($financiaciones) > 0) {
            foreach ($financiaciones as $key => $financiacion) {
                foreach ($financiaciones as $k => $financiacion2) {
                    if ($k != $key) {
                        if ($financiacion['concepto_financiacion'] == $financiacion2['concepto_financiacion'] && $financiacion['codigo_financiacion'] == $financiacion2['codigo_financiacion']) {
                            $retorno = false;
                            $retornoMensaje = 'No puede cargar mismos conceptos con iguales financiaciones';
                        }
                    }
                }
            }
        } else {
            $retorno = false;
            $retornoMensaje = 'No puede guardar un plan sin financiaciones';
        }

        if ($retorno) {
            return true;
        } else {
            $this->CI->form_validation->set_message("validarFinanciaciones", $retornoMensaje);
            return false;
        }
    }

    /* Inicio Ticket  667 - Validar nombre de plan de pago */
    function validarNombrePlan($nombre_plan) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);

        $retorno = true;
        $retornoMensaje = '';
        $retorno = Vplanes_pago::validarNombrePlanPago($conexion, $nombre_plan);

        if (empty($retorno)){
            return true;
        } else {
            $retornoMensaje = lang('cambiar_nombre_planpago');
            $this->CI->form_validation->set_message("validarNombrePlan", $retornoMensaje);
            return false;
        }
    }
    /* Fin Ticket  667 */

    function validarFechaFin_FechaInicio($fecha_inicio, $fecha_fin) {
        $inicio = formatearFecha_mysql($fecha_inicio);
        $fin = formatearFecha_mysql($fecha_fin);
        if ($fin >= $inicio) {
            return true;
        } else {
            $retornoMensaje = 'La fecha Fin tiene que ser igual o mayor a la inicio';
            $this->CI->form_validation->set_message("validarFechaFin_FechaInicio", $retornoMensaje);
            return false;
        }
    }

    function validarImporteFacturaNC($cod_factura, $importe) {
        $retornoMensaje = '';
        if (!validaciones::validarImporteFacturaNC($cod_factura, $importe, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarImporteFacturaNC", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarConfirmarNC($codigo, $cod_usuario) {
        $retornoMensaje = '';
        if (!validaciones::validarConfirmarNC($codigo, $cod_usuario, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarConfirmarNC", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarAnularNC($codigo, $cod_usuario) {
        $retornoMensaje = '';
        if (!validaciones::validarAnularNC($codigo, $cod_usuario, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarAnularNC", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarImporteFacturar($total, $jsctacte) {
        $retornoMensaje = '';
        if (!validaciones::validarImporteFacturar($total, $jsctacte, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarImporteFacturar", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

    function validarCupoModificarExamen($cupo, $cod_examen) {
        $ci = &get_instance();
        $filial = $ci->session->userdata('filial');
        $cod_filial = $filial['codigo'];
        $conexion = $ci->load->database($cod_filial, true);


        $myExamen = new Vexamenes($conexion, $cod_examen);
        $cantInscriptos = $myExamen->getCantidadInscriptosExamen();

        if ($cupo >= $cantInscriptos[0]['cant_inscripto']) {
            return true;
        } else {
            $retornoMensaje = 'El cupo tiene que ser mayor o igual a la cantidad de Inscriptos Actual';
            $this->CI->form_validation->set_message("validarCupoModificarExamen", $retornoMensaje);
            return false;
        }
    }

    function validarCobroFacturar($cod_cobro) {
        $retornoMensaje = '';
        if (!validaciones::validarCobroFacturar($cod_cobro, $retornoMensaje)) {
            $this->CI->form_validation->set_message("validarCobroFacturar", $retornoMensaje);
            return false;
        } else {
            return true;
        }
    }

}
