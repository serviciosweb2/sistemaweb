$('.fancybox-wrap').ready(function(){    
    $('button[name="submit"]').hide();    
    function initTab(){
        $('button[name="submit"]').hide();    
        var prevTab='';    
        var inlineTab='';    
        var idAsuntoDetalle='';    
        var mensajes='';    
        var fecha= new Date();
        var myEditor;
        $('.fancybox-wrap select').chosen({
            width:'100%',
            allow_single_deselect: true
        });

        $('.messagebar-item-right, .page-header').hide();   
        var form_initialized = false;

        function initialize_form() {
            if(form_initialized) return;
            form_initialized = true;					
            myEditor = $('.message-form .wysiwyg-editor').ace_wysiwyg({
                    toolbar:
                    [
                        'bold',
                        'italic',
                        'strikethrough',
                        'underline',
                        null,
                        'justifyleft',
                        'justifycenter',
                        'justifyright',
                        null,
                        'createLink',
                        'unlink',
                        null,
                        'undo',
                        'redo'
                    ]
            }).prev().addClass('wysiwyg-style1');

            //file input
            $('.message-form input[type=file]').ace_file_input()
            //and the wrap it inside .span7 for better display, perhaps
            .closest('.ace-file-input').addClass('width-90 inline').wrap('<div class="row file-input-container"><div class="col-sm-7"></div></div>');

            //the button to add a new file input
            $('#id-add-attachment').on('click', function(){
                var file = $('<input type="file" name="attachment[]" />').appendTo('#form-attachments');
                file.ace_file_input();
                file.closest('.ace-file-input').addClass('width-90 inline').wrap('<div class="row file-input-container"><div class="col-sm-7"></div></div>')
                .parent(/*.span7*/).append('<div class="action-buttons pull-right col-xs-1">\
                        <a href="#" data-action="delete" class="middle">\
                                <i class="icon-trash red bigger-130 middle"></i>\
                        </a>\
                </div>').find('a[data-action=delete]').on('click', function(e){
                    //the button that removes the newly inserted file input
                    e.preventDefault();			
                    $(this).closest('.row').hide(300, function(){
                            $(this).remove();
                    });
                });
            });

            $('.wysiwyg-speech-input').hide();

        }//initialize_form

        function Tab(id){        
            this.id=id;        
            this.search='';        
            this.selector='#'+this.id;        
            this.numerador=0;        
            this.cRegistros=0;        
            this.page=0;

            Tab.prototype.listar=function(){                
                var tab=this.selector;                
                var noLeidos='';                
                $(this.selector+' .detalleList').show();                  
                $('.vistaDetalle').addClass('hide');
                //$('.detalleList').hide();
                $('#id-message-new-navbar').addClass('hide');                
                $('#id-message-form').addClass('hide');                
                //$('button[name="submit"]').prop('disabled',true);
                $(tab+' .message-container').append('<div class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');

                if(instancia.numerador > 0){
                    // estamos + de le primer pagina
                    $(tab+' .message-footer').find('.nexPage').closest('li').removeClass('disabled');
                    $(tab+' .message-footer').find('.prevPage').closest('li').removeClass('disabled');
                    $(tab+' .message-footer').find('.firthPage').closest('li').removeClass('disabled');
                    $(tab+' .message-footer').find('.lastPage').closest('li').removeClass('disabled');
                } else {
                    // estamos en la  primer pagina
                    $(tab+' .message-footer').find('.nexPage').closest('li').removeClass('disabled');
                    $(tab+' .message-footer').find('.lastPage').closest('li').removeClass('disabled');
                    $(tab+' .message-footer').find('.prevPage').closest('li').addClass('disabled');
                    $(tab+' .message-footer').find('.firthPage').closest('li').addClass('disabled');
                }
                var iDisplayStart = this.numerador == 0 ? '' : this.numerador * 5;             
                var dataPOST=$('#inboxSearch').serialize()+'&iDisplayStart='+iDisplayStart+'&sSearch='+this.search;
                var registros=this.page;
                $.ajax({
                    url:BASE_URL+'comisiones/listarComunicadosEmail',
                    type: "POST",
                    data: dataPOST,
                    dataType:'JSON',
                    cache:false,
                    async:true,
                    success:function(respuesta){
                        mensajes=respuesta;
                        $(tab + ' .message-list').empty();
                        Tab.prototype.totalRecords=respuesta.iTotalRecords;
                        if( instancia.numerador+1 == Math.ceil(respuesta.iTotalRecords/5)){                  
                            $(tab+' .message-footer').find('.nexPage').closest('li').addClass('disabled');
                            $(tab+' .message-footer').find('.lastPage').closest('li').addClass('disabled');
                        }                
                        var cantRegistros=respuesta.aaData.length;
                        if(respuesta.aaData.length==0){
                            $(tab+' .message-infobar .grey').html('(No tiene mensajes)');
                        } else {
                            $(respuesta.aaData).each(function(k,consulta){
                                noLeidos=consulta.noLeidos;                    
                                var estado='';                    
                                if(instancia.id=='cerradas'){
                                    estado=consulta.estado=='cerrado' ? '<span class="label label-success arrowed">Concretada&nbsp;</span>' : '<span class="label label-info arrowed">No concretada&nbsp;</span>';
                                }
                                var leido = consulta.notificar == 0 ? 'message-unread' : '';
                                var nombreMateria = consulta.nombre_es ? consulta.nombre_es : 'Todas la Comision';
                                var estrellaLeido = consulta.destacar == 1 ? 'icon-star  orange2' : ' icon-star-empty light-grey';
                                var fila='<div class="message-item ' + leido + '" data-estado="' + consulta.notificar + '" value="' + consulta.codigo+'" data-codMateria="' + consulta.cod_materia + '">';
                                fila += '<label class="inline">';                                               
                                fila += '<span class="lbl"></span>';
                                fila += '</label>';                                        
                                fila += '<span class="sender" title="'+nombreMateria+'">'+nombreMateria+'</span>';
                                fila += '<span class="time" title="'+moment(consulta.fecha_hora).lang('es').format('LLL')+'">'+moment(consulta.fecha_hora, "YYYY-MM-DD h:mm:ss").lang('es').calendar()+'</span>';
                                fila += '<span class="summary">';
                                fila += '<span class="text">';
                                fila += consulta.asunto;
                                fila += '</span>';
                                fila += '</span>'+estado;
                                fila += '</div>';                
                                $(tab+' .message-list').append(fila);
                                cantRegistros > 10 ? '' : 'disabled';
                            });
                            $(tab+' .message-infobar .grey').html('('+noLeidos +' No leidos)');                
                        }
                        $(tab+' #TotalMensajes').html(respuesta.iTotalRecords +' mensajes');
                        $(tab+' .message-loading-overlay').remove();
                        $.fancybox.update();
                    }
                });
                $(this.selector+' input[name="numeroPagina"]').val(this.numerador+1);
            };       

            Tab.prototype.select_all = function() {          
                var count = 0;
                $(this.selector+' .message-item input[type=checkbox]').each(function(){
                    this.checked = true;
                    $(this).closest('.message-item').addClass('selected');
                    count++;
                });
                $(this.selector+' .id-toggle-all').get(0).checked = true;
                this.display_bar(count);
            };                              

            Tab.prototype.display_bar = function (count){           
                if(count == 0) {
                    $(this.selector+' .id-toggle-all').removeAttr('checked');
                    $(this.selector+' #id-message-list-navbar .message-toolbar').addClass('hide');
                    $(this.selector+' #id-message-list-navbar .message-infobar').removeClass('hide');
                } else {
                    $(this.selector+' #id-message-list-navbar .message-infobar').addClass('hide');
                    $(this.selector+' #id-message-list-navbar .message-toolbar').removeClass('hide');
                }
            };                                        

            Tab.prototype.select_none = function() {
                $(this.selector+' .message-item input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
                $(this.selector+' .id-toggle-all').get(0).checked = false;						
                this.display_bar(0);
            };                                        

            Tab.prototype.select_read = function() {
                $(this.selector+' .message-unread input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
                var count = 0;
                $(this.selector+' .message-item:not(.message-unread) input[type=checkbox]').each(function(){
                    this.checked = true;
                    $(this).closest('.message-item').addClass('selected');
                    count++;
                });
                this.display_bar(count);
            };                                        

            Tab.prototype.select_unread = function() {
                $(this.selector+' .message-item:not(.message-unread) input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
                var count = 0;
                $(this.selector+' .message-unread input[type=checkbox]').each(function(){
                    this.checked = true;
                    $(this).closest('.message-item').addClass('selected');
                    count++;
                });						
                this.display_bar(count);
            };

            Tab.prototype.show_form = function() {            
                $('button[name="submit"]').prop('disabled',false);
                if($('.message-form').is(':visible')){ 
                    return;
                }
                if(!form_initialized) {
                    initialize_form();
                }			
                var message = $(this.selector+' .message-list');
                $('.message-container').append('<div class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');

                $('.detalleList').hide();
                $('.vistaDetalle').addClass('hide');
                $('#id-message-new-navbar').removeClass('hide');
                $('#id-message-form').removeClass('hide');
                $('.message-container').find('.message-loading-overlay').remove();
                //reset form??
                $('.message-form .wysiwyg-editor').empty();
                $('.message-form .ace-file-input').closest('.file-input-container:not(:first-child)').remove();
                $('.message-form input[type=file]').ace_file_input('reset_input');
                $('.message-form').get(0).reset();
                $.fancybox.update();
            };
        }

        var instancia='';    
        var inbox= new Tab('inbox');    
        instancia=inbox;    
        inlineTab=inbox.id;    
        instancia.listar();    
    //    Eventos
        $('#inbox-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var currentTab = $(this).attr('href');           
               console.log('LISTAR'+currentTab);            
                switch(currentTab){
                     case '#inbox':
                        inlineTab=inbox.id;
                        inbox.listar(); 
                        instancia=inbox;
                        $('button[name="submit"]').hide();               
                    break;

                    case '#write':
                        initialize_form ();                   
                        instancia.show_form();					
                        getAlumnosComision('');                    
                         $('button[name="submit"]').show();            
                    break;
                 }
        });

        $('.nexPage').on('click',function(){        
            if($(this).attr('class') == "nexPage disabled"){
                return false;
            };
            instancia.page += 10;
            instancia.numerador++;
            instancia.listar();
            return false;
        });

        $('.prevPage').on('click',function(){        
            if($(this).attr('class')== "prevPage disabled"){
                return false;
            };
            instancia.page-=10;
            instancia.numerador--;
            instancia.listar();
            return false;
        });

        $('.lastPage').on('click',function(){       
            if($(this).attr('class')== "lastPage disabled"){             
            return false;
        };

        instancia.numerador = Math.ceil(instancia.totalRecords/5)-1;
        instancia.listar(); 
            return false;
        });

        $('.firthPage').on('click',function(){
            if($(this).attr('class')== "firthPage disabled"){              
                return false;
            };    
            instancia.numerador=0;
            instancia.listar();
            return false;
        });

        $('.message-list').on('click','.message-item', function() {        
            idAsuntoDetalle=$(this).attr('value');         
            var materia=$(this).attr('data-codMateria');       
            var elemento=this;        
            $('.vistaDetalle').removeClass('hide');        
            $('.message-toolbar').show();        
            $('.vistaDetalle').find('#menuMover').show();        
            $('.vistaDetalle').find('a[href="eliminado"]').show();            
            var dataPOST='cod_comision='+codigo+'&idAsunto='+idAsuntoDetalle+'&cod_materia='+materia;        
            var destinatarios='<p>';     
            $.ajax({
                url: BASE_URL+'comisiones/getAlumnosComunicado',
                type: "POST",
                data: dataPOST,
                dataType:"JSON",
                cache:false,
                async:false,
                success:function(respuesta){                    
                    if(respuesta.length!=0){
                        $(respuesta).each(function(k,alumno){
                            destinatarios+="<b>"+alumno.nombre+" "+alumno.apellido+"</b><<a>"+alumno.email+"</a>>";
                            respuesta.length-1 == k ? destinatarios+='.</p>' : destinatarios+=", ";
                        });
                    }                  
                }
            });        
            $(mensajes.aaData).each(function(k,msj){
                if(msj.codigo==idAsuntoDetalle){                
                    $('.vistaDetalle').find('.message-body').html(msj.mensaje);                
                }
            });
            var asunto=$(elemento).find('.text').text();        
            var nombre=$(elemento).find('.sender').attr('title');        
            var fechaHora=$(elemento).find('.time').attr('title');      
            $('#timeCabezera1').html(fechaHora);
            $('#timeCabezera2').html(fechaHora);
            $('#asuntoMensaje').html(asunto);
            $('#nombreSender').html(nombre);
            $('#destinatarios').html(destinatarios);     
            $('#'+inlineTab).find('.detalleList').hide();        
            $.fancybox.update();
        });                                
        $('.btn-back-message-list').on('click', function(e) {            
            $('.fancybox-wrap a[href="#inbox"]').tab('show');            
            instancia.listar();
            return false;           
        });

        $('#mover').on('click','a',function(){        
            var cambiarEstado=$(this).attr('href');        
            var dataPOST='cambiarEstado='+cambiarEstado+'&idAsunto%5B%5D='+idAsuntoDetalle;        
            $.ajax({
                url: BASE_URL+'consultasweb/cambiarEstadoAsunto',
                type: "POST",
                data:dataPOST ,
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
                        instancia.select_none();
                        instancia.listar();
                    } else {
                        $.gritter.add({
                            title: 'Upps',
                            text: 'ocurrio un error',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        }); 
                    }
                }
            });
            return false;
        });


        $('.eliminar').on('click',function(){
            var cambiarEstado=$(this).attr('href');
            var dataPOST='cambiarEstado='+cambiarEstado+'&idAsunto%5B%5D='+idAsuntoDetalle;        
                $.ajax({
                    url: BASE_URL+'consultasweb/cambiarEstadoAsunto',
                    type: "POST",
                    data: dataPOST,
                    dataType:"JSON",
                    cache:false,
                    success:function(respuesta){
                        if(respuesta.codigo==1){                        
                            $.gritter.add({
                                title: 'OK!',
                                text: 'Borrado Correctamente',
                                sticky: false,
                                time: '3000',
                                class_name:'gritter-success'
                            });
                            instancia.select_none();
                            instancia.listar();
                        } else {
                            $.gritter.add({
                                title: 'Upps',
                                text: 'ocurrio un error',
                                sticky: false,
                                time: '3000',
                                class_name:'gritter-error'
                            });
                        }
                    }
                });        
            return false;
        });

        $('.id-toggle-all').removeAttr('checked').on('click', function(){
            if(this.checked) {
                    instancia.select_all();
            } else instancia.select_none();
        });

        $('.id-select-message-read').on('click', function(e) {
            e.preventDefault();
            instancia.select_read();
        });

        //select unread
        $('.id-select-message-unread').on('click', function(e) {            
            instancia.select_unread();            
            e.preventDefault();
        });    

        //select none
        $('.id-select-message-none').on('click', function(e) {
            e.preventDefault();
            instancia.select_none();
        });

        //select all
        $('.id-select-message-all').on('click', function(e) {            
            instancia.select_all();
            e.preventDefault();
        });

        //click en un chekbox en especial
        $('.message-list').delegate('.message-item input[type=checkbox]' , 'click', function() {
            $(this).closest('.message-item').toggleClass('selected');
            if(this.checked){
                instancia.display_bar(1);
            } else {
                instancia.display_bar($('.message-list input[type=checkbox]:checked').length);
            }		
        });        

        $('.dropdownList').on('click','a',function(){            
            var cambiarEstado=$(this).attr('href');            
            var idAsunto=$('#frm'+instancia.id).serialize();            
            var dataPOST='cambiarEstado='+cambiarEstado+'&'+idAsunto; 
            $.ajax({
                url: BASE_URL+'consultasweb/cambiarEstadoAsunto',
                type: "POST",
                data:dataPOST ,
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
                        instancia.select_none();
                        instancia.listar();
                    } else {
                        $.gritter.add({
                            title: 'Upps',
                            text: 'ocurrio un error',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
                    }
                }
            }); 
            return false;
        });


        $('.borrarList').on('click',function(){        
            var cambiarEstado=$(this).attr('href');        
            var cantEliminados=$('#frm'+instancia.id).serializeArray().length;        
            var idAsunto=$('#frm'+instancia.id).serialize();
            var dataPOST='cambiarEstado='+cambiarEstado+'&'+idAsunto;
            $.ajax({
                url: BASE_URL+'consultasweb/cambiarEstadoAsunto',
                type: "POST",
                data:dataPOST ,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){                        
                        $.gritter.add({
                            title: 'OK!',
                            text: 'Borrado correctamente',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-success'
                        });                        
                        cantEliminados == 10 ? instancia.numerador-- : '';                        
                        instancia.listar();                        
                        instancia.select_none();                        
                    } else {
                        $.gritter.add({
                            title: 'Upps',
                            text: 'ocurrio un error',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
                    }
                }
            });        
            return false;
        });


        $('.borrarListdesdeDetalle').on('click',function(){        
            var cambiarEstado=$(this).attr('href');            
            var dataPOST = 'cambiarEstado=' + cambiarEstado + '&idAsunto%5B%5D=' + idAsuntoDetalle;
            $.ajax({
                url: BASE_URL+'consultasweb/cambiarEstadoAsunto',
                type: "POST",
                data:dataPOST ,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){
                        $.gritter.add({
                            title: 'OK!',
                            text: 'Borrado correctamente',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-success'
                        });
                        instancia.select_none();
                        instancia.listar();
                    } else {
                        $.gritter.add({
                            title: 'Upps',
                            text: 'ocurrio un error',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        }); 
                    }
                }
            });
            return false;
        });    

        $('.responder').on('click',function(){        
            alert('FALTA GENERAL URL');        
            $.fancybox.open(['http://iga-la.net/intranet/crm_filiales/control_generar_templates.php?id=236850&codfilial=36&code=1db2d2b4e185db11520c7691070cf078'],{
                type:'iframe',
                padding:0,
                width:620,
                height:770,
                ajax:{
                    dataType : 'html',
                    headers  : { 'X-fancyBox': true }
                }
            });
            return false;
        });

        $('.message-list').on('click','.message-star',function(){        
            var destacar=1;      
            var element=this;      
            var aplicarClase='message-star icon-star  orange2';        
            var destacarClass=$(this).attr('class');      
            var idAsunto=$(this).parent().find('input[name="idAsunto[]"]').val();
            if(destacarClass=='message-star icon-star  orange2'){
                destacar=0;
                var aplicarClase='message-star  icon-star-empty light-grey';
            }
            $.ajax({
                url:BASE_URL+'consultasweb/destacarAsunto',
                type: "POST",
                data: "destacar="+destacar+"&idAsunto="+idAsunto,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){
                        $(element).attr('class',aplicarClase);
                        $.gritter.add({
                            title: 'OK!',
                            text: 'Modificado correctamente',
                            sticky: false,
                            time: '2500',
                            class_name:'gritter-success'
                        });
                    } else {
                        $.gritter.add({
                            title: 'Upss!',
                            text: 'algo no salio bien',
                            sticky: false,
                            time: '2500',
                            class_name:'gritter-error'
                        });
                    }
                }
            });
            return false;
        });    

        var focus=0;
        $('#inboxSearch').on('focus','input[name="filtrar"]',function(){
            focus = 1;
        });

        $('#inboxSearch').on('focusout','input[name="filtrar"]',function(){
           focus=0;
        });

        $('.fancybox-wrap .tab-pane').on('keydown','#inboxSearch',function(e){
            if(focus==1 && e.keyCode==13){
                instancia.search=$(this).find('input[name="filtrar"]').val();
                instancia.listar();
                return false;            
            }      
        });

        $('.fancybox-wrap').on('change','select[name="cod_materia"]',function(){
            instancia.listar();
            return false;
        });

        function getAlumnosComision(codMateria){
            var dataPOST = 'cod_comision='+codigo+'&cod_materia='+codMateria;
            $('select[name="alumnosComunicados[]"]').empty();
            $.ajax({
                url: BASE_URL+'comisiones/getAlumnosMateriaComision',
                type: "POST",
                data: dataPOST,
                dataType:"JSON",
                cache:false,
                success:function(respuesta){                    
                    if(respuesta.length!=0){                        
                        $(respuesta).each(function(k,alumno){
                            if (alumno.enviarComunicado == "Si"){
                                $('select[name="alumnosComunicados[]"]').append("<option value='" + alumno.cod_alumno + "' selected>" + alumno.nombre_apellido + "</option>");
                            } else {
                                $('select[name="alumnosComunicados[]"]').append("<option value='" + alumno.cod_alumno + "' disabled='true'>" + alumno.nombre_apellido + "(" + alumno.motivo + ")" + "</option>");
                            }
                        });                       
                    }                    
                    $('select[name="alumnosComunicados[]"]').trigger('chosen:updated');
                    $.fancybox.update();
                }              
            });
        }
        $('.fancybox-wrap').on('change','select[name="codMateria"]',function(){
            getAlumnosComision($(this).val());        
            return false;
        });    

        $('.fancybox-wrap').on('click','button[name="submit"]',function(){        
            var mensajeMail = $('.wysiwyg-editor').cleanHtml();
            var alumnos = $("[name='alumnosComunicados[]']").val();
            var asunto = $("[name=asunto]").val();
            var codMateria = $('select[name="codMateria"]').val();
            $.ajax({
                url: BASE_URL+'comisiones/guardarComunicados',
                type: "POST",
                data: {
                    alumnos: alumnos,
                    asunto: asunto,
                    cod_comision: codigo,
                    mensaje: mensajeMail,
                    codMateria: codMateria
                },
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo == 1){
                        $.gritter.add({
                            title: 'OK!',
                            text: 'Guardado Correctamente',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-success'
                        });
                        $('.fancybox-wrap a[href="#inbox"]').tab('show');
                        instancia.listar();
                    } else {
                        $.gritter.add({
                            title: 'Upss!',
                            text: respuesta.respuesta,
                            sticky: false,
                            time: '3000',
                            class_name: 'gritter-error'
                        });                        
                    }
                }
            });          
            return false;
        });    		
    }        
        
    $('.mensaje').hide();
    $('.vista').hide();        
    var cantaALumnos = $('input[name="cantAlumnos"]').val();
        
    if(cantaALumnos==0){            
        $('.mensaje').show();            
    } else {
        $('.vista').show();  
        initTab();             
    }   
        
});




