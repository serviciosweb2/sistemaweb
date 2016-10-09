<style>
    
   
    
    
    .chosen-results{
        
        
        max-height: 80px !important;
        
    }
  
    
</style>

<link rel="stylesheet" href="<?php echo base_url('assents/css/dynatree/ui.dynatree.css')?>"/>
<script src="<?php echo base_url('assents/js/configuracion/jquery.contextMenu-custom.js')?>"></script>
<script src="<?php echo base_url('assents/js/librerias/dynatree/jquery.cookie.js');?>"></script>
<script src="<?php echo base_url('assents/js/librerias/dynatree/jquery.dynatree.js');?>"></script>
<script src="<?php echo base_url('assents/js/configuracion/config_compras.js')?>"></script>
<style>
    
    .widget-body{
        
        max-height: 400px !important;
        overflow: auto !important;
        
    }
    
    
</style>



<style>
    
    
    
    
    /* Generic context menu styles */
.contextMenu {
	position: absolute;
	width: auto;
	z-index: 99999;
	border: solid 1px #CCC;
	background: #EEE;
	padding: 0px;
	margin: 0px;
	display: none;
}

.contextMenu LI {
	list-style: none;
	padding: 0px;
	margin: 0px;
}

.contextMenu A {
	color: #333;
	text-decoration: none;
	display: block;
	line-height: 20px;
	height: 20px;
	background-position: 6px center;
	background-repeat: no-repeat;
	outline: none;
	padding: 1px 5px;
	padding-left: 28px;
}

.contextMenu LI.hover A {
	color: #FFF;
	background-color: #3399FF;
}

.contextMenu LI.disabled A {
	color: #AAA;
	cursor: default;
}

.contextMenu LI.hover.disabled A {
	background-color: transparent;
}

.contextMenu LI.separator {
	border-top: solid 1px #CCC;
}

/*
	Adding Icons

	You can add icons to the context menu by adding
	classes to the respective LI element(s)
*/

.contextMenu LI.edit A { background-image: url(images/page_white_edit.png); }
.contextMenu LI.cut A { background-image: url(images/cut.png); }
.contextMenu LI.copy A { background-image: url(images/page_white_copy.png); }
.contextMenu LI.paste A { background-image: url(images/page_white_paste.png); }
.contextMenu LI.delete A { background-image: url(images/page_white_delete.png); }
.contextMenu LI.quit A { background-image: url(images/door.png); }

    
</style>

                                                <!-- Definition of context menu -->
                                                         <ul id="myMenu" class="contextMenu">
                                                           <li class=""><a href="#nuevaSubcategoria">Agregar subcategoria</a></li>
                                                           <li class=""><a href="#modificar">Modificar</a></li>
                                                           <li class=""><a href="#eliminar">eliminar</a></li>
                                 <!--                          <li class="edit"><a href="#edit">Edit</a></li>
                                                           <li class="cut separator"><a href="#cut">Cut</a></li>
                                                           <li class="copy"><a href="#copy">Copy</a></li>
                                                           <li class="paste"><a href="#paste">Paste</a></li>
                                                           <li class="delete"><a href="#delete">Delete</a></li>
                                                           <li class="quit separator"><a href="#quit">Quit</a></li>-->
                                                         </ul>
<div class="col-md-12 col-xs-12">
        <div id="areaTablas" class="">
        <div class="tabbable">
            <?php
             $data['tab_activo']='config_compras';
             $this->load->view('configuracion/vista_tabs',$data);
            ?>               
            <div class="tab-content">
                
                <div id="tabConfigCompras" class="tab-pane in active">
                    <div class="row">
                    <div class="col-xs-12 col-sm-12 widget-container-span">
                                    <div class="widget-box">
                                            <div class="widget-header header-color-orange">
                                                    <h5><?php echo lang('articulos');?></h5>

                                                    <div class="widget-toolbar">
                                                            <a href="#" data-action="collapse">
                                                                    <i class="1 icon-chevron-up bigger-125"></i>
                                                            </a>
                                                    </div>

<!--                                                    <div class="widget-toolbar no-border">
                                                            <button class="btn btn-xs btn-light bigger">
                                                                    <i class="icon-arrow-left"></i>
                                                                    Prev
                                                            </button>

                                                            <button class="btn btn-xs bigger btn-yellow dropdown-toggle" data-toggle="dropdown">
                                                                    Next
                                                                    <i class="icon-chevron-down icon-on-right"></i>
                                                            </button>

                                                            <ul class="dropdown-menu dropdown-yellow pull-right dropdown-caret dropdown-close">
                                                                    <li>
                                                                            <a href="#">Action</a>
                                                                    </li>

                                                                    <li>
                                                                            <a href="#">Another action</a>
                                                                    </li>

                                                                    <li>
                                                                            <a href="#">Something else here</a>
                                                                    </li>

                                                                    <li class="divider"></li>

                                                                    <li>
                                                                            <a href="#">Separated link</a>
                                                                    </li>
                                                            </ul>
                                                    </div>-->
                                            </div>

                                            <div class="widget-body">
                                                    <div class="widget-main">
                                                        
                                        

                                                         <div id="tree"></div>
                                                        
                                                        
                                                        
                                                    </div>

                               
                                            </div>
                                    </div>
                            </div>
                        
                        
                        
                        
                        
                        
                        
                    
                    
                    
                  
                  </div>

                </div>
        
       <li class="text-warning bigger-110 orange">
        <i class="icon-warning-sign"></i>
        <?php echo lang('referencia_configuracion_compras');?>
        </li>														
            </div>
            
        </div>
    </div>
</div>



