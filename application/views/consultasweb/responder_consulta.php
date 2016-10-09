<style>    
    .vistaDetalle .message-body{        
        padding: 0px !important;
    }    
</style>
<input type ="hidden" value="<?php echo $arrConsulta['codigo']; ?>" id="codigo_consulta_responder">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" onclick="cancelarResponderConsulta()">&times;</button>
        <h4 class="blue bigger">
            <?php echo lang("responder_consulta"); ?>
            <small>
                <i class="icon-double-angle-right"></i>
                <?php echo $arrConsulta['nombre'];
                echo "&nbsp;{$arrConsulta['mail']}";
                if ($arrConsulta['telefono'] <> ''){
                    echo "&nbsp;({$arrConsulta['telefono']})";
                } ?>
            </small>
        </h4>
    </div>
    <div class="modal-body overflow-visible">
        <div class="row" id="tdContenedorResponder">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-11  col-xs-6">
                        <h4><?php echo lang("seleccion_de_cursos"); ?></h4>
                    </div>
                    <div class="col-md-1 col-xs-6">
                        <div class="nav-search minimized">
                            <span class="input-icon">
                                <input id="search_template_name" class="input-small nav-search-input" type="" onkeydown="buscarTemplates(event);" placeholder="Search template ..." autocomplete="off" value="">
                                <i class="icon-search nav-search-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="lista_templates" class="col-md-12">
                        <?php 
                        $a=4;
                        $i=0;
                        //$arpa = $this->session->userdata('filial');
                        
                        foreach ($arrTemplates as $idx => $template)
                        {
                            $i++;
                            if($i==1)
                            {
                                echo '<div class="row">';
                            } ?>
                        <div class="col-md-<?php echo $a?>">
                            <input type="checkbox" style="border: 0 white none" name="templates" value="<?php echo $template['cod_template'] ?>"
                            <?php if (isset($templates_seleccionados) && in_array($template['cod_template'], $templates_seleccionados)){ ?> checked="true" <?php } ?>>
                                                        
                            <?php if($template["nombre_curso"] != null){ echo $template["nombre_curso"]; } else{ echo $template["nombre_mostrar"]; }/*echo $template[$nombre_campo] == "" ? $template['nombre'] : $template[$nombre_campo];*/
                                
                            if ($idx < 5 && $template['cantidad'] > 0){ ?>
                                <i class="light-orange icon-asterisk"></i>
                            <?php } ?>
                        </div>
                        <?php if($i== (12 / $a)){
                                echo '</div>';
                                $i=0;
                            }
                        } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="area_de_notificacion" class="help-block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-sm hide" id="BTNAnterior"  data-last="Finish" onclick="cancelarResponderConsulta();">
            <i class="icon-remove"></i>
            <?php echo lang("volver"); ?>
        </button>
        <button class="btn btn-sm btn-primary" id="BTNGuardar" data-last="Finish" type="submit" onclick="responderConsultaSeleccionaTemplates();">
            <i class="icon-ok"></i>
            <?php echo lang("siguiente"); ?>
        </button>
    </div>
</div>