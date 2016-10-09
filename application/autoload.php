<?php

function myAutoload($className){
    $arrClases["Valertas"] = APPPATH."libraries/alertas/Valertas.php";
    $arrClases["Talertas"] = APPPATH."libraries/base/Talertas.php";
    $arrClases["Valertas_tipo"] = APPPATH."libraries/alertas/Valertas_tipo.php";
    $arrClases["Talertas_tipo"] = APPPATH."libraries/base/Talertas_tipo.php";
    $arrClases["Valumnos"] = APPPATH."libraries/alumnos/Valumnos.php";
    $arrClases["Talumnos"] = APPPATH."libraries/base/Talumnos.php";
    $arrClases["Vcomo_nos_conocio"] = APPPATH."libraries/alumnos/Vcomo_nos_conocio.php";
    $arrClases["Tcomo_nos_conocio"] = APPPATH."libraries/base/Tcomo_nos_conocio.php";
    $arrClases["Vdocumentacion_alumnos"] = APPPATH."libraries/alumnos/Vdocumentacion_alumnos.php";
    $arrClases["Tdocumentacion_alumnos"] = APPPATH."libraries/base/Tdocumentacion_alumnos.php";
    $arrClases["Vnota_credito"] = APPPATH."libraries/alumnos/Vnota_credito.php";
    $arrClases["Tnota_credito"] = APPPATH."libraries/base/Tnota_credito.php";
    $arrClases["Vproductos_alumnos"] = APPPATH."libraries/alumnos/Vproductos_alumnos.php";
    $arrClases["Tproductos_alumnos"] = APPPATH."libraries/base/Tproductos_alumnos.php";
    $arrClases["Vtalles"] = APPPATH."libraries/alumnos/Vtalles.php";
    $arrClases["Ttalles"] = APPPATH."libraries/base/Ttalles.php";
    $arrClases["Varticulos"] = APPPATH."libraries/articulos/Varticulos.php";
    $arrClases["Tarticulos"] = APPPATH."libraries/base/Tarticulos.php";
    $arrClases["Vasistencias"] = APPPATH."libraries/asistencias/Vasistencias.php";
    $arrClases["Tasistencias"] = APPPATH."libraries/base/Tasistencias.php";
    $arrClases["Vaspirantes"] = APPPATH."libraries/aspirantes/Vaspirantes.php";
    $arrClases["Taspirantes"] = APPPATH."libraries/base/Taspirantes.php";
    $arrClases["Vinteresados"] = APPPATH."libraries/aspirantes/Vinteresados.php";
    $arrClases["Tinteresados"] = APPPATH."libraries/base/Tinteresados.php";
    $arrClases["Vpresupuestos"] = APPPATH."libraries/aspirantes/Vpresupuestos.php";
    $arrClases["Tpresupuestos"] = APPPATH."libraries/base/Tpresupuestos.php";
    $arrClases["Vcaja"] = APPPATH."libraries/caja/Vcaja.php";
    $arrClases["Tcaja"] = APPPATH."libraries/base/Tcaja.php";
    $arrClases["Vcaja_historico"] = APPPATH."libraries/caja/Vcaja_historico.php";
    $arrClases["Tcaja_historico"] = APPPATH."libraries/base/Tcaja_historico.php";
    $arrClases["Vcaja_usuario"] = APPPATH."libraries/caja/Vcaja_usuario.php";
    $arrClases["Tcaja_usuario"] = APPPATH."libraries/base/Tcaja_usuario.php";
    $arrClases["Vmovimientos_caja"] = APPPATH."libraries/caja/Vmovimientos_caja.php";
    $arrClases["Tmovimientos_caja"] = APPPATH."libraries/base/Tmovimientos_caja.php";
    $arrClases["Vrubros"] = APPPATH."libraries/caja/Vrubros.php";
    $arrClases["Trubros"] = APPPATH."libraries/base/Trubros.php";
    $arrClases["Vusuarios_rubros"] = APPPATH."libraries/caja/Vusuarios_rubros.php";
    $arrClases["Tusuarios_rubros"] = APPPATH."libraries/base/Tusuarios_rubros.php";
    $arrClases["Vcertificados"] = APPPATH."libraries/certificados/Vcertificados.php";
    $arrClases["Tcertificados"] = APPPATH."libraries/base/Tcertificados.php";
    $arrClases["class_general"] = APPPATH."libraries/class_general.php";
    $arrClases["Vcomision"] = APPPATH."libraries/comisiones/Vcomision.php";
    $arrClases["Tcomision"] = APPPATH."libraries/base/Tcomision.php";
    $arrClases["Vhorarios"] = APPPATH."libraries/comisiones/Vhorarios.php";
    $arrClases["Thorarios"] = APPPATH."libraries/base/Thorarios.php";
    $arrClases["Vcompras"] = APPPATH."libraries/compras/Vcompras.php";
    $arrClases["Tcompras"] = APPPATH."libraries/base/Tcompras.php";
    $arrClases["Vcomprasreglones"] = APPPATH."libraries/compras/Vcomprasreglones.php";
    $arrClases["Tcomprasreglones"] = APPPATH."libraries/base/Tcomprasreglones.php";
    $arrClases["Vproductos"] = APPPATH."libraries/compras/Vproductos.php";
    $arrClases["Tproductos"] = APPPATH."libraries/base/Tproductos.php";
    $arrClases["Vconfiguracion"] = APPPATH."libraries/configuracion/Vconfiguracion.php";
    $arrClases["Tconfiguracion"] = APPPATH."libraries/base/Tconfiguracion.php";
    $arrClases["Vcotizaciones"] = APPPATH."libraries/configuracion/Vcotizaciones.php";
    $arrClases["Tcotizaciones"] = APPPATH."libraries/base/Tcotizaciones.php";
    $arrClases["Vferiados"] = APPPATH."libraries/configuracion/Vferiados.php";
    $arrClases["Tferiados"] = APPPATH."libraries/base/Tferiados.php";
    $arrClases["Valertas_ctacte"] = APPPATH."libraries/ctacte/Valertas_ctacte.php";
    $arrClases["Talertas_ctacte"] = APPPATH."libraries/base/Talertas_ctacte.php";
    $arrClases["Vctacte"] = APPPATH."libraries/ctacte/Vctacte.php";
    $arrClases["Tctacte"] = APPPATH."libraries/base/Tctacte.php";
    $arrClases["Vctacte_concepto"] = APPPATH."libraries/ctacte/Vctacte_concepto.php";
    $arrClases["Tctacte_concepto"] = APPPATH."libraries/base/Tctacte_concepto.php";
    $arrClases["Vctacte_imputaciones"] = APPPATH."libraries/ctacte/Vctacte_imputaciones.php";
    $arrClases["Tctacte_imputaciones"] = APPPATH."libraries/base/Tctacte_imputaciones.php";
    $arrClases["Vctacte_moras"] = APPPATH."libraries/ctacte/Vctacte_moras.php";
    $arrClases["Tctacte_moras"] = APPPATH."libraries/base/Tctacte_moras.php";
    $arrClases["Vmoras"] = APPPATH."libraries/ctacte/Vmoras.php";
    $arrClases["Tmoras"] = APPPATH."libraries/base/Tmoras.php";
    $arrClases["VmorasCursosCortos"] = APPPATH."libraries/ctacte/VmorasCursosCortos.php";
    $arrClases["TmorasCursosCortos"] = APPPATH."libraries/base/TmorasCursosCortos.php";
    $arrClases["Vcursos"] = APPPATH."libraries/cursos/Vcursos.php";
    $arrClases["Tcursos"] = APPPATH."libraries/base/Tcursos.php";
    $arrClases["Vcursos_habilitado"] = APPPATH."libraries/cursos/Vcursos_habilitado.php";
    $arrClases["Tcursos_habilitado"] = APPPATH."libraries/base/Tcursos_habilitado.php";
    $arrClases["Vmaterias"] = APPPATH."libraries/cursos/Vmaterias.php";
    $arrClases["Tmaterias"] = APPPATH."libraries/base/Tmaterias.php";
    $arrClases["Vdocumentacion"] = APPPATH."libraries/documentos/Vdocumentacion.php";
    $arrClases["Tdocumentacion"] = APPPATH."libraries/base/Tdocumentacion.php";
    $arrClases["Vdocumentos_tipos"] = APPPATH."libraries/documentos/Vdocumentos_tipos.php";
    $arrClases["Tdocumentos_tipos"] = APPPATH."libraries/base/Tdocumentos_tipos.php";
    $arrClases["Vexamenes"] = APPPATH."libraries/examenes/Vexamenes.php";
    $arrClases["Texamenes"] = APPPATH."libraries/base/Texamenes.php";
    $arrClases["Vexamenes_matriculas_inscripciones"] = APPPATH."libraries/examenes/Vexamenes_matriculas_inscripciones.php";
    $arrClases["Texamenes_matriculas_inscripciones"] = APPPATH."libraries/base/Texamenes_matriculas_inscripciones.php";
    $arrClases["Vnotas_resultados"] = APPPATH."libraries/examenes/Vnotas_resultados.php";
    $arrClases["Tnotas_resultados"] = APPPATH."libraries/base/Tnotas_resultados.php";
    $arrClases["Vcheques"] = APPPATH."libraries/facturas/Vcheques.php";
    $arrClases["Tcheques"] = APPPATH."libraries/base/Tcheques.php";
    $arrClases["Vcomprobantes"] = APPPATH."libraries/facturas/Vcomprobantes.php";
    $arrClases["Tcomprobantes"] = APPPATH."libraries/base/Tcomprobantes.php";
    $arrClases["Vconceptos_factura_renglones"] = APPPATH."libraries/facturas/Vconceptos_factura_renglones.php";
    $arrClases["Tconceptos_factura_renglones"] = APPPATH."libraries/base/Tconceptos_factura_renglones.php";
    $arrClases["Vfacturas"] = APPPATH."libraries/facturas/Vfacturas.php";
    $arrClases["Tfacturas"] = APPPATH."libraries/base/Tfacturas.php";
    $arrClases["Vfacturas_anuladas"] = APPPATH."libraries/facturas/Vfacturas_anuladas.php";
    $arrClases["Tfacturas_anuladas"] = APPPATH."libraries/base/Tfacturas_anuladas.php";
    $arrClases["Vfacturas_descuentos"] = APPPATH."libraries/facturas/Vfacturas_descuentos.php";
    $arrClases["Tfacturas_descuentos"] = APPPATH."libraries/base/Tfacturas_descuentos.php";
    $arrClases["Vfacturas_estado_historicos"] = APPPATH."libraries/facturas/Vfacturas_estado_historicos";
    $arrClases["Tfacturas_estado_historicos"]= APPPATH."libraries/base/Tfacturas_estado_historicos";    
    $arrClases["Vmedios_pago"] = APPPATH."libraries/facturas/Vmedios_pago.php";
    $arrClases["Tmedios_pago"] = APPPATH."libraries/base/Tmedios_pago.php"; 
    $arrClases["Vtalonarios"] = APPPATH."libraries/facturas/Vtalonarios.php";
    $arrClases["Ttalonarios"] = APPPATH."libraries/base/Ttalonarios.php";
    $arrClases["Vtiposfacturas"] = APPPATH."libraries/facturas/Vtiposfacturas.php";
    $arrClases["Ttiposfacturas"] = APPPATH."libraries/base/Ttiposfacturas.php";
    $arrClases["Vlocalidades"] = APPPATH."libraries/locacion/Vlocalidades.php";
    $arrClases["Tlocalidades"] = APPPATH."libraries/base/Tlocalidades.php";
    $arrClases["Vpaises"] = APPPATH."libraries/locacion/Vpaises.php";
    $arrClases["Tpaises"] = APPPATH."libraries/base/Tpaises.php";
    $arrClases["Vprovincias"] = APPPATH."libraries/locacion/Vprovincias.php";
    $arrClases["Tprovincias"] = APPPATH."libraries/base/Tprovincias.php";
    $arrClases["Vcupones_canje"] = APPPATH."libraries/matriculas/Vcupones_canje.php";
    $arrClases["Tcupones_canje"] = APPPATH."libraries/base/Tcupones_canje.php";
    $arrClases["Vestadoacademico"] = APPPATH."libraries/matriculas/Vestadoacademico.php";
    $arrClases["Testadoacademico"] = APPPATH."libraries/base/Testadoacademico.php";
    $arrClases["Vmatriculas"] = APPPATH."libraries/matriculas/Vmatriculas.php";
    $arrClases["Tmatriculas"] = APPPATH."libraries/base/Tmatriculas.php";
    $arrClases["Vmatriculas_certificacion"] = APPPATH."libraries/matriculas/Vmatriculas_certificacion.php";
    $arrClases["Tmatriculas_certificacion"] = APPPATH."libraries/base/Tmatriculas_certificacion.php";
    $arrClases["Vmatriculas_comision"] = APPPATH."libraries/matriculas/Vmatriculas_comision.php";
    $arrClases["Tmatriculas_comision"] = APPPATH."libraries/base/Tmatriculas_comision.php";
    $arrClases["Vmatriculas_estado_historicos"] = APPPATH."libraries/matriculas/Vmatriculas_estado_historicos.php";
    $arrClases["Tmatriculas_estado_historicos"] = APPPATH."libraries/base/Tmatriculas_estado_historicos.php";
    $arrClases["Vmatriculas_incripciones"] = APPPATH."libraries/matriculas/Vmatriculas_incripciones.php";
    $arrClases["Tmatriculas_incripciones"] = APPPATH."libraries/base/Tmatriculas_incripciones.php";
    $arrClases["MY_Form_validation"] = APPPATH."libraries/MY_Form_validation.php";
    $arrClases["MY_Session"] = APPPATH."libraries/MY_Session.php";
    $arrClases["Vcambios_planes"] = APPPATH."libraries/planes/Vcambios_planes.php";
    $arrClases["Tcambios_planes"] = APPPATH."libraries/base/Tcambios_planes.php";
    $arrClases["Vcambios_promo_vencida"] = APPPATH."libraries/planes/Vcambios_promo_vencida.php";
    $arrClases["Tcambios_promo_vencida"] = APPPATH."libraries/base/Tcambios_promo_vencida.php";
    $arrClases["Vplanes_certificacion"] = APPPATH."libraries/planes/Vplanes_certificacion.php";
    $arrClases["Tplanes_certificacion"] = APPPATH."libraries/base/Tplanes_certificacion.php";
    $arrClases["Vplanes_cuotas"] = APPPATH."libraries/planes/Vplanes_cuotas.php";
    $arrClases["Tplanes_cuotas"] = APPPATH."libraries/base/Tplanes_cuotas.php";
    $arrClases["Vplanes_pago"] = APPPATH."libraries/planes/Vplanes_pago.php";
    $arrClases["Tplanes_pago"] = APPPATH."libraries/base/Tplanes_pago.php";
    $arrClases["Vprofesores"] = APPPATH."libraries/profesores/Vprofesores.php";
    $arrClases["Tprofesores_estado_historico"] = APPPATH."libraries/base/Tprofesores_estado_historico.php";
    $arrClases["Vprofesores_estado_historico"] = APPPATH."libraries/profesores/Vprofesores_estado_historico.php";
    $arrClases["Tprofesores"] = APPPATH."libraries/base/Tprofesores.php";
    $arrClases["Vproveedores"] = APPPATH."libraries/proveedores/Vproveedores.php";
    $arrClases["Tproveedores"] = APPPATH."libraries/base/Tproveedores.php";
    $arrClases["Vproveedores_razones"] = APPPATH."libraries/proveedores/Vproveedores_razones.php";
    $arrClases["Tproveedores_razones"] = APPPATH."libraries/base/Tproveedores_razones.php";
    $arrClases["Vcondiciones_sociales"] = APPPATH."libraries/razones/Vcondiciones_sociales.php";
    $arrClases["Tcondiciones_sociales"] = APPPATH."libraries/base/Tcondiciones_sociales.php";
    $arrClases["Vrazones_sociales"] = APPPATH."libraries/responsables/Vrazones_sociales.php";
    $arrClases["Trazones_sociales"] = APPPATH."libraries/base/Trazones_sociales.php";
    $arrClases["Vresponsables"] = APPPATH."libraries/responsables/Vresponsables.php";
    $arrClases["Tresponsables"] = APPPATH."libraries/base/Tresponsables.php";
    $arrClases["Vresponsables_telefonos"] = APPPATH."libraries/responsables/Vresponsables_telefonos.php";
    $arrClases["Tresponsables_telefonos"] = APPPATH."libraries/base/Tresponsables_telefonos.php";
    $arrClases["Vsalones"] = APPPATH."libraries/salones/Vsalones.php";
    $arrClases["Tsalones"] = APPPATH."libraries/base/Tsalones.php";
    $arrClases["Tcertificados"] = APPPATH."libraries/Tcertificados.php";
    $arrClases["Vempresas_telefonicas"] = APPPATH."libraries/telefonos/Vempresas_telefonicas.php";
    $arrClases["Tempresas_telefonicas"] = APPPATH."libraries/base/Tempresas_telefonicas.php";
    $arrClases["Vtelefonos"] = APPPATH."libraries/telefonos/Vtelefonos.php";
    $arrClases["Ttelefonos"] = APPPATH."libraries/base/Ttelefonos.php";
    $arrClases["Vtelefonos_tipos"] = APPPATH."libraries/telefonos/Vtelefonos_tipos.php";
    $arrClases["Ttelefonos_tipos"] = APPPATH."libraries/base/Ttelefonos_tipos.php";
    $arrClases["Ttipos_razones_sociales"] = APPPATH."libraries/Ttipos_razones_sociales.php";
    $arrClases["Vactores_sistema"] = APPPATH."libraries/usuarios/Vactores_sistema.php";
    $arrClases["Tactores_sistema"] = APPPATH."libraries/base/Tactores_sistema.php";
    $arrClases["Vfiliales"] = APPPATH."libraries/usuarios/Vfiliales.php";
    $arrClases["Tfiliales"] = APPPATH."libraries/base/Tfiliales.php";
    $arrClases["Vhorarios_filiales"] = APPPATH."libraries/usuarios/Vhorarios_filiales.php";
    $arrClases["Thorarios_filiales"] = APPPATH."libraries/base/Thorarios_filiales.php";
    $arrClases["Vperfiles"] = APPPATH."libraries/usuarios/Vperfiles.php";
    $arrClases["Tperfiles"] = APPPATH."libraries/base/Tperfiles.php";
    $arrClases["Vsecciones"] = APPPATH."libraries/usuarios/Vsecciones.php";
    $arrClases["Tsecciones"] = APPPATH."libraries/base/Tsecciones.php";
    $arrClases["Vusuarios_sistema"] = APPPATH."libraries/usuarios/Vusuarios_sistema.php";
    $arrClases["Tusuarios_sistema"] = APPPATH."libraries/base/Tusuarios_sistema.php";
    $arrClases["validaciones"] = APPPATH."libraries/validaciones.php";
    $arrClases['GoogleCloudPrint'] = APPPATH."libraries/impresion/GoogleCloudPrint.php";
    $arrClases['impresiones'] = APPPATH."libraries/impresion/impresiones.php";
    $arrClases['impresoras_filiales'] = APPPATH."libraries/impresion/impresoras_filiales.php";

    
    if (isset($arrClases[$className]) && file_exists($arrClases[$className])){
        include_once $arrClases[$className];
    }

}

spl_autoload_register("myAutoload");

?>