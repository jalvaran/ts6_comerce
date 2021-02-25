/*
 * javascript para controlar los eventos del modulo repositorio juridico
 */
var listado_id=1;
function evento_busqueda(){
    $("#txtBusquedasGenerales").unbind();
    $('#txtBusquedasGenerales').on('keyup',function () {
        dibujeListadoSegunID();        
    });
}
evento_busqueda();


function CambiePagina(Funcion,Page=""){
    
    if(Page==""){
        if(document.getElementById('CmbPage')){
            Page = document.getElementById('CmbPage').value;
        }else{
            Page=1;
        }
    }
    if(Funcion==1){
        dibujeListadoSegunID(Page);
    }
    
    
}


function dibujeListadoSegunID(Page=1){
    
    if(listado_id==1){
        listar_repositorio_juridico(Page);
    }
       
    
}


function actualizar_contadores(){
    var empresa_id = document.getElementById('empresa_id').value;        
    var form_data = new FormData();
        form_data.append('Accion', 1);        
        form_data.append('empresa_id', empresa_id);
        
        $.ajax({
        url: './procesadores/repositorio_juridico.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                document.getElementById('sp_repositorio_juridico_temas').innerHTML=respuestas[1];
                document.getElementById('sp_sub_temas').innerHTML=respuestas[2];
                document.getElementById('sp_tipo_documentos').innerHTML=respuestas[3];
                document.getElementById('sp_entidades').innerHTML=respuestas[4];
            }else if(respuestas[0]=="E1"){
                
                toastr.error(respuestas[1]);
            }else{
                swal(data);
            }
                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function listar_repositorio_juridico(page){
    dibuja_tabla(`get`,`vista_repositorio_juridico`,page,`DivListados`);
    
}

function frm_crear_editar_registro_repositorio(repositorio_id=''){
    var idDiv="DivListados";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('repositorio_id', repositorio_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/repositorio_juridico.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_dropzone_repositorio();           
            listar_adjuntos_repositorio(repositorio_id);
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function listar_adjuntos_repositorio(repositorio_id=''){
    var idDiv="div_adjuntos_repositorio";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('repositorio_id', repositorio_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/repositorio_juridico.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function add_events_dropzone_repositorio(){
    Dropzone.autoDiscover = false;
           
    urlQuery='procesadores/repositorio_juridico.process.php';
    var repositorio_id=$("#repositorio_adjuntos").data("repositorio_id");
    var empresa_id=document.getElementById('empresa_id').value; 
    
    var myDropzone = new Dropzone("#repositorio_adjuntos", { url: urlQuery,paramName: "adjunto_repositorio"});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 2);
            formData.append("repositorio_id", repositorio_id);
            formData.append("empresa_id", empresa_id);
            
        });

        myDropzone.on("addedfile", function(file) {
            file.previewElement.addEventListener("click", function() {
                myDropzone.removeFile(file);
            });
        });

        myDropzone.on("success", function(file, data) {

            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listar_adjuntos_repositorio(repositorio_id);
            }else if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
            }else{
                alert(data);
            }

        });
    listar_adjuntos_repositorio(repositorio_id);
}


function EliminarItem(tabla_id,item_id,repositorio_id){    
    
    var empresa_id =document.getElementById("empresa_id").value;
         
    var form_data = new FormData();
        
        form_data.append('Accion', 3);
        form_data.append('empresa_id', empresa_id);  
        form_data.append('item_id', item_id);  
        form_data.append('tabla_id', tabla_id);
                        
        $.ajax({
        url: 'procesadores/repositorio_juridico.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                if(tabla_id==1){
                    listar_adjuntos_repositorio(repositorio_id);
                }
                                
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);
                
                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
            }
               
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  


function confirmar_crear_editar_repositorio(repositorio_id){
    swal({   
            title: "Seguro que desea realizar esta acción?",   
            //text: "You will not be able to recover this imaginary file!",   
            type: "warning",   
            showCancelButton: true,  
            
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Claro que Siii!",   
            cancelButtonText: "Espera voy a revisar algo!",   
            closeOnConfirm: true,   
            closeOnCancel: true 
        }, function(isConfirm){   
            if (isConfirm) {
                
                crear_editar_repositorio_juridico(repositorio_id);              
                
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function crear_editar_repositorio_juridico(repositorio_id){
    
    var btnEnviar = "btn_guardar";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var empresa_id = document.getElementById('empresa_id').value;  
    var tema_id = document.getElementById('tema_id').value;  
    var sub_tema_id = document.getElementById('sub_tema_id').value;  
    var fecha_documento = document.getElementById('fecha_documento').value; 
    var tipo_documento_id = document.getElementById('tipo_documento_id').value; 
    var numero_documento = document.getElementById('numero_documento').value; 
    var entidad_id = document.getElementById('entidad_id').value; 
    var extracto = document.getElementById('extracto').value; 
    var fuentes_formales = document.getElementById('fuentes_formales').value; 
    var ano_recopilacion = document.getElementById('ano_recopilacion').value; 
    var estado = document.getElementById('estado').value; 
        
    var form_data = new FormData();
        form_data.append('Accion', 4);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('repositorio_id', repositorio_id);        
        form_data.append('tema_id', tema_id);
        form_data.append('sub_tema_id', sub_tema_id);
        form_data.append('tipo_documento_id', tipo_documento_id);
        form_data.append('fecha_documento', fecha_documento);
        
        form_data.append('numero_documento', numero_documento);
        form_data.append('entidad_id', entidad_id);
        form_data.append('extracto', extracto);
        form_data.append('fuentes_formales', fuentes_formales);
        form_data.append('ano_recopilacion', ano_recopilacion);
        form_data.append('estado', estado);
                      
        
        $.ajax({
        url: './procesadores/repositorio_juridico.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Creando Repositorio..");
        },
        complete: function(){
           ocultar_spinner();
        },
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Guardar";
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                
                toastr.success(respuestas[1]);
                dibujeListadoSegunID();
                
            }else if(respuestas[0]=="E1"){                
                toastr.error(respuestas[1]);
            }else{
                alert(data);
            }
                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Guardar";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


actualizar_contadores();
