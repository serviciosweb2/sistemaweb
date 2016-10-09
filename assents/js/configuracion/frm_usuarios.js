$(document).ready(function(){    
    $('#WTREE').hide();
    treeData = '';  
  
    $('select[name="idioma"]').chosen({      
        width:'100%',
        allow_single_deselect: true      
    });  

    var $this = $('.icon-refresh');              
    var $box = $this.closest('.widget-box');                
    var event;
    $box.trigger(event = $.Event('reload.ace.widget'));
    $this.blur();
      
    function crearTree(){
        function drawTREE(){         
            tree = $("#tree").dynatree({
                checkbox: true,
                selectMode: 3,
                children: treeData,
                onSelect: function(select, node) {                 
                    var selKeys = $.map(node.tree.getSelectedNodes(), function(node){                 
                        return node.data.key;
                    });
                    $("#echoSelection3").text(selKeys.join(", "));
                    var selRootNodes = node.tree.getSelectedNodes(true);
                    var selRootKeys = $.map(selRootNodes, function(node){
                        return node.data.key;
                    });
                    $("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
                    $("#echoSelectionRoots3").text(selRootNodes.join(", "));
                },
                onDblClick: function(node, event) {
                    node.toggleSelect();
                },
                onKeydown: function(node, event) {
                    if( event.which == 32 ) {
                        node.toggleSelect();
                        return false;
                    }
                },
                cookieId: "dynatree-Cb3",
                idPrefix: "dynatree-Cb3-"
            });
        }
        tree='';
        remove = false;     
        if($box.css('position') == 'static') {
            $remove = true; $box.addClass('position-relative');
        }
        
        $box.append('<div class="widget-box-overlay"><i class="icon-spinner icon-spin icon-2x white"></i></div>');
        $.ajax({
            url: BASE_URL+'configuracion/getSeccionesPermisos',
            type: "POST",
            data: "cod_usuario="+$('input[name="cod_usuario"]').val(),
            async: false,
            dataType: "JSON",
            cache: false,
            success: function(respuesta){
                treeData = respuesta.msg;
                remove = true;
                if(respuesta.codigo == 1){
                    $('#WTREE').show();
                    drawTREE();
                }
            }
        });

        $box.find('.widget-box-overlay').remove();
        if(remove){
            $box.removeClass('position-relative');
        }
    }
    
    $('.widget-box').on('click','a',function(){        
        var a=$('#tree').parent();
        $('#tree').remove();
        a.append($('<div>',{id:'tree'}));
        crearTree();
        return false;
    });
    
    crearTree();
    
    $('button[name="volver"]').click(function(){        
        $('.active a').trigger('click');        
        return false;
    });
    
    $('#usuario').on('submit',function(){        
        var dataPOST=$('#usuario').serialize();        
        var permisosUsuario='';        
        var selKeys='';        
        if($('#WTREE').is(':visible')){            
            tree = $("#tree").dynatree("getTree");                       
            selKeys = $.map(tree.getSelectedNodes(), function(node) {            
                return node.data.key;          
            });           
            permisosUsuario = $.param({
                listaPermisos: selKeys
            });
        }        
        $.ajax({
            url: BASE_URL + 'usuarios/guardarUsuario',
            type: "POST",
            data: dataPOST+'&'+permisosUsuario,
            dataType:"JSON",
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo==1){                        
                    $.gritter.add({
                        title: 'OK!',
                        text: 'Guardado Correctamente',
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-success'
                    });
                        
                    if($('input[name="redireccionar"]').val()==1){           
                        setTimeout(function(){                            
                            $('.active a').trigger('click');                            
                        },100);
                    }   
                } else {                        
                    $.gritter.add({
                        title: 'Upps!',
                        text: respuesta.msgerror,
                        sticky: false,
                        time: '3000',
                        class_name:'gritter-error'
                    });                        
                }
            }
        });
        return false;
    });
});