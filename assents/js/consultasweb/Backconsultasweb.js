
			jQuery(function($){
			//$('#inbox-tabs a').eq(2).tab('show')
				//handling tabs and loading/displaying relevant messages and forms
				//not needed if using the alternative view, as described in docs
				var prevTab = 'inbox';
                                
                                
                                
				$('#inbox-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                  
                                      
					var currentTab = $(this).attr('href');
                                        alert(currentTab);
                                        
                                        if(currentTab=='#cerradas'){
                                            Cerradas.show_list();
                                        }
                                      
                                       
                                       if(currentTab=='#inbox'){
                                           Inbox.show_list();
                                       }
					
                                        if(currentTab == '#write') {
                                           
						Inbox.show_form();
                                                
                                                
//                                            
                                        }else {
                                            
                                         
                                            
						if(prevTab == 'write'){
							Inbox.show_list();
                                                        
                                                         
                                                        
                                                    }
			
						//load and display the relevant messages 
					}
					
                                    prevTab = currentTab;
				})
			
			
				
				//basic initializations
				$('.message-list .message-item input[type=checkbox]').removeAttr('checked');
				$('.message-list').delegate('.message-item input[type=checkbox]' , 'click', function() {
					$(this).closest('.message-item').toggleClass('selected');
					if(this.checked) Inbox.display_bar(1);//display action toolbar when a message is selected
					else {
						Inbox.display_bar($('.message-list input[type=checkbox]:checked').length);
						//determine number of selected messages and display/hide action toolbar accordingly
					}		
				});
                                
                                
                                
                                
                                /*@EventosINBOX*/
                                
                                //next page
                                $('#nexPage').on('click',function(){
                                    
                                    Inbox.numerador++;
                                  
                                    
                                    Inbox.show_list();
                                    
                                    return false;
                                });
                                
                                $('#prevPage').on('click',function(){
                                    
                                    
                                    Inbox.numerador--;
                                  
                                    
                                   Inbox.show_list();
                                    
                                    return false;
                                });
                                
			
				//check/uncheck all messages
				$('#id-toggle-all').removeAttr('checked').on('click', function(){
					if(this.checked) {
						Inbox.select_all();
					} else Inbox.select_none();
				});
				
				//select all
				$('#id-select-message-all').on('click', function(e) {
					e.preventDefault();
					Inbox.select_all();
				});
				
				//select none
				$('#id-select-message-none').on('click', function(e) {
					e.preventDefault();
					Inbox.select_none();
				});
				
				//select read
				$('#id-select-message-read').on('click', function(e) {
					e.preventDefault();
					Inbox.select_read();
				});
			
				//select unread
				$('#id-select-message-unread').on('click', function(e) {
					e.preventDefault();
					Inbox.select_unread();
				});
			
				/////////
			
				//display first message in a new area
				$('#inbox .message-list').on('click','.message-item .text', function() {
                                    alert('loadMensaje,falta ID');
					//show the loading icon
					$('.message-container').append('<div class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');
					
					$('.message-inline-open').removeClass('message-inline-open').find('.message-content').remove();
			
					var message_list = $(this).closest('.message-list');
			
					//some waiting
					setTimeout(function() {
			
						//hide everything that is after .message-list (which is either .message-content or .message-form)
						message_list.next().addClass('hide');
						$('.message-container').find('.message-loading-overlay').remove();
			
						//close and remove the inline opened message if any!
			
						//hide all navbars
						$('.message-navbar').addClass('hide');
						//now show the navbar for single message item
						$('#id-message-item-navbar').removeClass('hide');
			
						//hide all footers
						$('.message-footer').addClass('hide');
						//now show the alternative footer
						$('.message-footer-style2').removeClass('hide');
			
						
						//move .message-content next to .message-list and hide .message-list
						message_list.addClass('hide').after($('.message-content')).next().removeClass('hide');
			
						//add scrollbars to .message-body
						$('.message-content .message-body').slimScroll({
							height: 200,
							railVisible:true
						});
			
					}, 500 + parseInt(Math.random() * 500));
				});
			
			
				//display second message right inside the message list
				$('.message-list .message-item:eq(1) .text').on('click', function(){
					var message = $(this).closest('.message-item');
			
					//if message is open, then close it
					if(message.hasClass('message-inline-open')) {
						message.removeClass('message-inline-open').find('.message-content').remove();
						return;
					}
			
					$('.message-container').append('<div class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');
					setTimeout(function() {
						$('.message-container').find('.message-loading-overlay').remove();
						message
							.addClass('message-inline-open')
							.append('<div class="message-content" />')
						var content = message.find('.message-content:last').html( $('#id-message-content').html() );
			
						content.find('.message-body').slimScroll({
							height: 200,
							railVisible:true
						});
				
					}, 500 + parseInt(Math.random() * 500));
					
				});
			
			
			
				//back to message list
				$('.btn-back-message-list').on('click', function(e) {
                                    alert('volver');
					e.preventDefault();
					Inbox.show_list();
					$('#inbox-tabs a[data-target="inbox"]').tab('show'); 
				});
			
			
			
				//hide message list and display new message form
				/**
				$('.btn-new-mail').on('click', function(e){
					e.preventDefault();
					Inbox.show_form();
				});
				*/
			
                                /*@FuncionesINBOX*/
			
			
				var Inbox = {
					//displays a toolbar according to the number of selected messages
					display_bar : function (count) {
						if(count == 0) {
							$('#id-toggle-all').removeAttr('checked');
							$('#id-message-list-navbar .message-toolbar').addClass('hide');
							$('#id-message-list-navbar .message-infobar').removeClass('hide');
						}
						else {
							$('#id-message-list-navbar .message-infobar').addClass('hide');
							$('#id-message-list-navbar .message-toolbar').removeClass('hide');
						}
					}
					,
					select_all : function() {
						var count = 0;
						$('.message-item input[type=checkbox]').each(function(){
							this.checked = true;
							$(this).closest('.message-item').addClass('selected');
							count++;
						});
						
						$('#id-toggle-all').get(0).checked = true;
						
						Inbox.display_bar(count);
					}
					,
					select_none : function() {
						$('.message-item input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
						$('#id-toggle-all').get(0).checked = false;
						
						Inbox.display_bar(0);
					}
					,
					select_read : function() {
						$('.message-unread input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
						
						var count = 0;
						$('.message-item:not(.message-unread) input[type=checkbox]').each(function(){
							this.checked = true;
							$(this).closest('.message-item').addClass('selected');
							count++;
						});
						Inbox.display_bar(count);
					}
					,
					select_unread : function() {
						$('.message-item:not(.message-unread) input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
						
						var count = 0;
						$('.message-unread input[type=checkbox]').each(function(){
							this.checked = true;
							$(this).closest('.message-item').addClass('selected');
							count++;
						});
						
						Inbox.display_bar(count);
					}
				}
			
				//show message list (back from writing mail or reading a message)
                                Inbox.numerador=1;
				Inbox.show_list = function() {
                                        
                                    
                                        
                                        $('#inbox .message-container').append('<div class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');

                                        
                                       
                                        var iDisplayStart= Inbox.numerador == 1 ? '' : Inbox.numerador*10;
                                        
                                        var dataPOST='tipoConsulta=inbox&iDisplayStart='+iDisplayStart;
                                        
                                        var registros='';
                                        
                                        $.ajax({
                                                url:BASE_URL+'consultasweb/listar',
                                                type: "POST",
                                                data: dataPOST,
                                                dataType:'JSON',
                                                cache:false,
                                                async:true,
                                                success:function(respuesta){
                                                   
                                                    
                                         
                                       
                                        $('#inbox .message-list').empty();
                                       
                                        $('#inbox#TotalMensajes').html(respuesta.iTotalRecords +' mensajes');
                                        
                                        $(respuesta.aaData).each(function(k,consulta){
                                            
                                            
                                            
                                            var leido= consulta.notificar == 0 ? 'message-unread' : '';
                                            
                                            var estrellaLeido= consulta.notificar == 0 ? 'icon-star  orange2' : ' icon-star-empty light-grey';
                                            
                                            var fila='<div class="message-item '+leido+'">';
                                                               fila+= '<label class="inline">';
                                                                       fila+=' <input type="checkbox" class="ace">';
                                                                       fila+='<span class="lbl"></span>';
                                                                fila+='</label>';

                                                                fila+='<i class="message-star '+estrellaLeido+'"></i>';
                                                                fila+='<span class="sender" title="'+consulta.nombre+'">'+consulta.nombre+'</span>';
                                                                fila+='<span class="time">'+consulta.fechahora+'</span>';

                                                                fila+='<span class="summary">';
                                                                        fila+='<span class="text">'
                                                                                fila+= consulta.asunto;
                                                                        fila+='</span>';
                                                                fila+='</span>';
                                                        fila+='</div>';
                                            
                                        $('#inbox .message-list').append(fila);
                                        $('#inbox .message-loading-overlay').remove();
                                       
                                       
                                        
                                        
                                        
                                        
                                        });
                                        
                                        
                                                   
                                                }
                                        });
                                        
//                                        $('input[name="iDisplayStart"]').val((iDisplayStart+1)*Inbox.numerador);
                                        
                                        $('input[name="numeroPagina"]').val(Inbox.numerador);
                                        
                                        $('.message-navbar').addClass('hide');
					
                                        $('#id-message-list-navbar').removeClass('hide');
			
					
                                        $('.message-footer').addClass('hide');
					
                                        $('.message-footer:not(.message-footer-style2)').removeClass('hide');
			
					$('.message-list').removeClass('hide').next().addClass('hide');
					//hide the message item / new message window and go back to list
				}
			
				//show write mail form
				Inbox.show_form = function() {
                                       
					//if($('.message-form').is(':visible')) return;
					if(!form_initialized) {
                                            alert('iniFom');
						initialize_form();
					}
					
					
					var message = $('.message-list');
					
					
//					
				}
			
                                var form_initialized = false;
				function initialize_form() {
					if(form_initialized) return;
					form_initialized = false;
					$.fancybox.open(['http://iga-la.net/intranet/crm_filiales/control_generar_templates.php?id=-1&codfilial=36&code=a8a4e1a488e41594b9987563ac113d3d'],{
                                                    
                                                    type:'iframe',
                                                    padding:0,
                                                    width:650,
                                                    height:380,
                                                    ajax:{
                                                            dataType : 'html',
                                                            headers  : { 'X-fancyBox': true }
                                                            }
                                                    });
					
				}
                                
                               /*@eventosCERRADAS*/
                               
                               
                               
                               
                                
                                /*
                                 * @FuncionesCERRADAS
                                 */
                                
                                
                                
                                
				var Cerradas = {
					//displays a toolbar according to the number of selected messages
					display_bar : function (count) {
						if(count == 0) {
							$('#id-toggle-all').removeAttr('checked');
							$('#id-message-list-navbar .message-toolbar').addClass('hide');
							$('#id-message-list-navbar .message-infobar').removeClass('hide');
						}
						else {
							$('#id-message-list-navbar .message-infobar').addClass('hide');
							$('#id-message-list-navbar .message-toolbar').removeClass('hide');
						}
					}
					,
					select_all : function() {
						var count = 0;
						$('.message-item input[type=checkbox]').each(function(){
							this.checked = true;
							$(this).closest('.message-item').addClass('selected');
							count++;
						});
						
						$('#id-toggle-all').get(0).checked = true;
						
						Inbox.display_bar(count);
					}
					,
					select_none : function() {
						$('.message-item input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
						$('#id-toggle-all').get(0).checked = false;
						
						Inbox.display_bar(0);
					}
					,
					select_read : function() {
						$('.message-unread input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
						
						var count = 0;
						$('.message-item:not(.message-unread) input[type=checkbox]').each(function(){
							this.checked = true;
							$(this).closest('.message-item').addClass('selected');
							count++;
						});
						Inbox.display_bar(count);
					}
					,
					select_unread : function() {
						$('.message-item:not(.message-unread) input[type=checkbox]').removeAttr('checked').closest('.message-item').removeClass('selected');
						
						var count = 0;
						$('.message-unread input[type=checkbox]').each(function(){
							this.checked = true;
							$(this).closest('.message-item').addClass('selected');
							count++;
						});
						
						Inbox.display_bar(count);
					}
				}
			
				//show message list (back from writing mail or reading a message)
                                Cerradas.numerador=1;
				Cerradas.show_list = function() {
                                        alert('cerradas');
                                    
                                        
                                        $('#cerradas .message-container').append('<div class="message-loading-overlay"><i class="icon-spin icon-spinner orange2 bigger-160"></i></div>');

                                        
                                       
                                        var iDisplayStart= Cerradas.numerador == 1 ? '' : Cerradas.numerador*10;
                                        
                                        var dataPOST='tipoConsulta=cerradas&iDisplayStart='+iDisplayStart;
                                        
                                        var registros='';
                                        
                                        $.ajax({
                                                url:BASE_URL+'consultasweb/listar',
                                                type: "POST",
                                                data: dataPOST,
                                                dataType:'JSON',
                                                cache:false,
                                                async:true,
                                                success:function(respuesta){
                                                   
                                                    
                                         
                                       
                                        $('#cerradas .message-list').empty();
                                       
                                        $('#cerradas#TotalMensajes').html(respuesta.iTotalRecords +' mensajes');
                                        
                                        $(respuesta.aaData).each(function(k,consulta){
                                            
                                            
                                            
                                            var leido= consulta.notificar == 0 ? 'message-unread' : '';
                                            
                                            var estrellaLeido= consulta.notificar == 0 ? 'icon-star  orange2' : ' icon-star-empty light-grey';
                                            
                                            var fila='<div class="message-item '+leido+'">';
                                                               fila+= '<label class="inline">';
                                                                       fila+=' <input type="checkbox" class="ace">';
                                                                       fila+='<span class="lbl"></span>';
                                                                fila+='</label>';

                                                                fila+='<i class="message-star '+estrellaLeido+'"></i>';
                                                                fila+='<span class="sender" title="'+consulta.nombre+'">'+consulta.nombre+'</span>';
                                                                fila+='<span class="time">'+consulta.fechahora+'</span>';

                                                                fila+='<span class="summary">';
                                                                        fila+='<span class="text">'
                                                                                fila+= consulta.asunto;
                                                                        fila+='</span>';
                                                                fila+='</span>';
                                                        fila+='</div>';
                                            
                                        $('#cerradas .message-list').append(fila);
                                        $('#cerradas .message-loading-overlay').remove();
                                       
                                       
                                        
                                        
                                        
                                        
                                        });
                                        
                                        
                                                   
                                                }
                                        });
                                        
//                                        $('input[name="iDisplayStart"]').val((iDisplayStart+1)*Inbox.numerador);
                                        
                                        $('#cerradas input[name="numeroPagina"]').val(Cerradas.numerador);
                                        
                                        $('#cerradas .message-navbar').addClass('hide');
					
                                        $('#cerradas#id-message-list-navbar').removeClass('hide');
			
					
                                        $('#cerradas .message-footer').addClass('hide');
					
                                        $('#cerradas .message-footer:not(.message-footer-style2)').removeClass('hide');
			
					$('#cerradas .message-list').removeClass('hide').next().addClass('hide');
					//hide the message item / new message window and go back to list
				}
			
				//show write mail form
				Cerradas.show_form = function() {
                                       
					//if($('.message-form').is(':visible')) return;
					if(!form_initialized) {
                                            alert('iniFom');
						initialize_form();
					}
					
					
					var message = $('.message-list');
					
					
//					
				}
			
			
			
			
				var form_initialized = false;
//				function initialize_form() {
//					if(form_initialized) return;
//					form_initialized = false;
//					$.fancybox.open(['http://iga-la.net/intranet/crm_filiales/control_generar_templates.php?id=-1&codfilial=36&code=a8a4e1a488e41594b9987563ac113d3d'],{
//                                                    
//                                                    type:'iframe',
//                                                    padding:0,
//                                                    width:650,
//                                                    height:380,
//                                                    ajax:{
//                                                            dataType : 'html',
//                                                            headers  : { 'X-fancyBox': true }
//                                                            }
//                                                    });
//					
//				}
                                
                                
                                
                                
                                
                                
                                
			
			
				Inbox.show_list();
			
			});
	



