
var tree='';
 var lang= BASE_LANG;
var clipboardNode = null;
  var pasteMode = null;

  function copyPaste(action, node) {
    switch( action ) {
    case "cut":
    case "copy":
      clipboardNode = node;
      pasteMode = action;
      break;
    case "paste":
      if( !clipboardNode ) {
        alert("Clipoard is empty.");
        break;
      }
      if( pasteMode == "cut" ) {
        // Cut mode: check for recursion and remove source
        var isRecursive = false;
        var cb = clipboardNode.toDict(true, function(dict){
          // If one of the source nodes is the target, we must not move
          if( dict.key == node.data.key )
            isRecursive = true;
        });
        if( isRecursive ) {
          alert("Cannot move a node to a sub node.");
          return;
        }
        node.addChild(cb);
        clipboardNode.remove();
      } else {
        // Copy mode: prevent duplicate keys:
        var cb = clipboardNode.toDict(true, function(dict){
          dict.title = "Copy of " + dict.title;
          delete dict.key; // Remove key, so a new one will be created
        });
        node.addChild(cb);
      }
      clipboardNode = pasteMode = null;
      break;
      
    default:
      alert("Unhandled clipboard action '" + action + "'");
    }
  };
  
  function validarEliminar(node){
      
      var retorno=true;
      
      
      if(node.data.children.length>0){
          
          $.gritter.add({
                            text: 'Esta categoria tiene articulos asignados. No se puede eliminar ni modificar',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
        
            retorno=false;
        
         return retorno;
          
      }
      
      
      return retorno;
      
      
      
  }
  
  function validarModificar(node){
      
      var retorno=true;
      
      
      if(node.data.key==''){
          
          $.gritter.add({
                            text: 'No se puede eliminar ni modificar esta categoria',
                            sticky: false,
                            time: '3000',
                            class_name:'gritter-error'
                        });
                        
           retorno = false;
           return retorno;
          
      }
      
      
      
      
      
      return retorno;
      
  };
 
  function nuevaSubcategoria(node){
      
      $.ajax({
            url: BASE_URL+"articulos/frm_categoria",
            type: "POST",
            data:{cod_padre:node.data.key,accion:'nueva'},
            dataType:"",
            cache:false,
            success:function(respuesta){
                
                $.fancybox.open(respuesta,{
                
                scrolling       :'auto',
                autoSize	: false,
                width   	: '50%',
                height      	: 'auto',
                padding         : 1,
                openEffect      :'none',
                closeEffect     :'none',
                helpers:  {
                        overlay : null
                            }
                
            });
                
            }
}       );
      
      //console.log('NODO',node);
      //alert('Nueva categoria! '+node.data.key);
  }

  function eliminar(node){
      
      if(validarModificar(node) && validarEliminar(node)){
          
          
         $.ajax({
                url: BASE_URL+"articulos/bajaCategoria",
                type: "POST",
                data: {cod_padre:node.data.key},
                dataType:"JSON",
                cache:false,
                success:function(respuesta){
                    if(respuesta.codigo==1){

                        $.gritter.add({
                                        title: 'OK!',
                                        text: 'Guardado Correctamente',
                                        //image: $path_assets+'/avatars/avatar1.png',
                                        sticky: false,
                                        time: '3000',
                                        class_name:'gritter-success'
                            });

                        $("#tree").dynatree("getTree").reload();

                    }else{

                        $.gritter.add({
                                        text: respuesta.msgerrors,
                                        //image: $path_assets+'/avatars/avatar1.png',
                                        sticky: false,
                                        time: '3000',
                                        class_name:'gritter-error'
                            });



                    }
                }
            });
          
          
      }
      
     
     
     
     
      
  }
  
  function modificar(node){
       //alert('!');
      if(validarModificar(node)){
         
          $.ajax({
            url: BASE_URL+"articulos/frm_categoria",
            type: "POST",
            data:{cod_padre:node.data.key,accion:'modificar'},
            dataType:"",
            cache:false,
            success:function(respuesta){
                
                $.fancybox.open(respuesta,{
                
                scrolling       :'auto',
                autoSize	: false,
                width   	: '50%',
                height      	: 'auto',
                padding         : 1,
                openEffect      :'none',
                closeEffect     :'none',
                helpers:  {
                        overlay : null
                            }
                
            });
                
            }
        });
          
      }
      
      
      
  };
    
  // --- Contextmenu helper --------------------------------------------------
  function bindContextMenu(span) {
    // Add context menu to this node:
    $(span).contextMenu({menu: "myMenu"}, function(action, el, pos) {
      // The event was bound to the <span> tag, but the node object
      // is stored in the parent <li> tag
      var node = $.ui.dynatree.getNode(el);
      switch( action ) {
      case "nuevo":
      case "copy":
      case "paste":
        copyPaste(action, node);
        break;
        
        case 'nuevaSubcategoria':
          nuevaSubcategoria(node);
        break;
        
      case 'eliminar':
            eliminar(node);
      break;
      
      case 'modificar':
          modificar(node);
      break;
      
      default:
        //alert("Todo: appply action '" + action + "' to node " + node);
      }
    });
  };

  // --- Init dynatree during startup ----------------------------------------

function drawTree(){
   
    
  $("#tree").dynatree({
         persist: true,
        onActivate: function(node) {
        $("#echoActivated").text(node.data.title + ", key=" + node.data.key);
      },
        initAjax: {
        url: BASE_URL+'articulos/getCategoriasSubcategorias'
        },
        onClick: function(node, event) {
        // Close menu on click
        if( $(".contextMenu:visible").length > 0 ){
          $(".contextMenu").hide();
//          return false;
        }
      },
//        onKeydown: function(node, event) {
//        // Eat keyboard events, when a menu is open
//        if( $(".contextMenu:visible").length > 0 )
//          return false;
//
//        switch( event.which ) {
//
//        // Open context menu on [Space] key (simulate right click)
//        case 32: // [Space]
//          $(node.span).trigger("mousedown", {
//            preventDefault: true,
//            button: 2
//            })
//          .trigger("mouseup", {
//            preventDefault: true,
//            pageX: node.span.offsetLeft,
//            pageY: node.span.offsetTop,
//            button: 2
//            });
//          return false;
//
//        // Handle Ctrl-C, -X and -V
//        case 67:
//          if( event.ctrlKey ) { // Ctrl-C
//            copyPaste("copy", node);
//            return false;
//          }
//          break;
//        case 86:
//          if( event.ctrlKey ) { // Ctrl-V
//            copyPaste("paste", node);
//            return false;
//          }
//          break;
//        case 88:
//          if( event.ctrlKey ) { // Ctrl-X
//            copyPaste("cut", node);
//            return false;
//          }
//          break;
//        }
//      },
      /*Bind context menu for every node when it's DOM element is created.
        We do it here, so we can also bind to lazy nodes, which do not
        exist at load-time. (abeautifulsite.net menu control does not
        support event delegation)*/
        onCreate: function(node, span){
        bindContextMenu(span);
      }
      
    });


 
    
}





$('.fancybox-wrap').ready(function(){
    
//    $.ajax({
//            url:BASE_URL+'entorno/getLang',
//            data:"claves=" + JSON.stringify(claves),
//            type:"POST",
//            dataType:"JSON",
//            async:false,
//            cache:false,
//            success:function(respuesta){
//                lang=respuesta;
//                
//            }
//    });
    
    
    init();
   
    drawTree();
    
   
    
});
function init(){
        
       $('.fancybox-wrap select').chosen({
        width:'100%',
        allow_single_deselect: true
    });
    
        
     
    }

