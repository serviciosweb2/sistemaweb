
    $(document).ready(function(){
       
        // FUNCION QUE CONTRUYE EL MENU DESPLEGABLE
        function frmDesplegable(codigo_matricula,codigo_comision,codigo_materia,options){
            var botonAceptar='';
            var vistaFrm='<div class="row-fluid form">';
                vistaFrm+=   '<form id="nuevaComision">';
                vistaFrm+=          ' <div class="row-fluid">';
                vistaFrm+=               '<div class="span12 margin-top alert alert-info">';
                vistaFrm+=               '<div class="span10">';
                if(options!=''){
                   
                botonAceptar=' <input class="span12 btn" type="submit" value="Confirmar">';
                vistaFrm+=                  ' <select name="nuevaComision" data-placeholder="seleccione horario">';
                vistaFrm+=                   '   <option></option>';
                
                $(options).each(function(){
                    //alert(this.horario);
                    if(codigo_comision!=this.cod_comision){
                        vistaFrm+='<option value="'+this.cod_comision+'">cod: '+this.cod_comision+' '+this.horario+'</option>'    
                    }
                });
              
                vistaFrm+=                     '   </select>';
                }else{
                  vistaFrm+= 'no hay horarios';  
                    
                }
                vistaFrm+=                 '</div>';
                                
                vistaFrm+=                  '<div class="span1">';
                vistaFrm+=                    botonAceptar;
                vistaFrm+=                    ' <input  type="hidden" name="codigo_matricula" value="'+codigo_matricula+'">';
                vistaFrm+=                    ' <input  type="hidden" name="codigo_materia"   value="'+codigo_materia+'">';
                vistaFrm+=                '</div>';
                vistaFrm+=                  '<div class="span1">';
                vistaFrm+=                    ' <a class="span12 btn" href="#" name="cerrar"><i class="icon-remove"></i></a>';
                vistaFrm+=                '</div>';
                vistaFrm+=          '</div>';
                vistaFrm+=     ' </div>';
                vistaFrm+=   ' </form>';
                            
                vistaFrm+= '</div>';
                
                return vistaFrm;
        }
        
        // LLAMADA A CHOSEN
        $('#nuevaComision select').each(function(){
            $(this).chosen({
                width: "100%"
            });
        });
        
        //CLICK EN EL BOTON MODIFICAR
        var parentName='';
        var codigo_matricula=$('input[name="codigo_matricula"]').val();
        
       
        $('#contenedorGeneral').on('click','input[name="modificar"]',function(){
           
           parentName =$(this).parent().attr('name');//codigo de materia
           var codigo_comision=$(this).parent().find("input[name='codigo_comision']").val();
            $(this).attr('disabled',true);
            
            $.ajax({
                url: BASE_URL +'matriculas/getHorariosDisponiblesMateria',
                data:'codigo_matricula='+codigo_matricula+'&codigo_materia='+parentName,
                type:'POST',
                dataType:'json',
                success:function(respuesta){
                    console.log(respuesta);
            
                        $('#'+parentName).append(frmDesplegable(codigo_matricula,codigo_comision,parentName,respuesta));
                        $('#nuevaComision select').chosen({
                        width:'100%'
                        });
                        $('#'+parentName).find('.form').slideDown('fast');
                }
            });
            return false;
        });
        
        //CLICK EN EL BOTON CERRAR
        $('#contenedorGeneral').on('click','a[name="cerrar"]',function(){
            
            $(this).parent().parent().parent().parent().parent().parent().find('input[name="modificar"]').attr('disabled',false);
            $(this).parent().parent().parent().parent().parent().slideUp('fast',function(){
                $(this).remove();
            });
            //alert('click');
            return false;
        });
        
        // SUBMIT DEL FORMULARIO 
        $('#contenedorGeneral').on('submit','#nuevaComision',function(){
        if($(this).find('select').val()!=''){;    
        var datosPOST=$(this).serialize();
        //alert(datosPOST);
        
        $.ajax({
            url:BASE_URL + 'matriculas/guardarCambiodeHorario',
            data:datosPOST,
            type:'POST',
            dataType:'json',
            cache:false,
            success:function(respuesta){
                if(respuesta.codigo=1){
                $.ajax({
                    url:BASE_URL + 'matriculas/frm_inscribirMaterias',
                    data:'codigo_matricula='+codigo_matricula,
                    type:'POST',
                    success:function(respuesta){
                        $('.fancybox-inner').html(respuesta);
                    }
                    
                });

            }else{
                alert('error en el primer ajax');
            }
           }
        });
        
    }else{
    
    alert('seleccione un horario');
    }
        return false;
        });
        
    });


