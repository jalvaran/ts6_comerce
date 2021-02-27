/*
 * javascript para controlar los eventos del modulo procesos juridicos
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
        listar_procesos_juridicos(Page);
    }
       
    
}


function actualizar_contadores(){
    var empresa_id = document.getElementById('empresa_id').value;        
    var form_data = new FormData();
        form_data.append('Accion', 1);        
        form_data.append('empresa_id', empresa_id);
        
        $.ajax({
        url: './procesadores/procesos_juridicos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                document.getElementById('sp_temas').innerHTML=respuestas[1];
                document.getElementById('sp_sub_temas').innerHTML=respuestas[2];
                document.getElementById('sp_tipo_procesos').innerHTML=respuestas[3];
                document.getElementById('sp_terceros').innerHTML=respuestas[4];
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

function listar_procesos_juridicos(page){
    dibuja_tabla(`get`,`vista_procesos_juridicos`,page,`DivListados`);
    
}

function frm_crear_editar_registro_proceso(proceso_id=''){
    var idDiv="DivListados";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('proceso_id', proceso_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/procesos_juridicos.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            //add_events_dropzone_procesos();  
            convertir_selects();
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function convertir_selects(){
    var empresa_id =document.getElementById("empresa_id").value;
    $('#tema_id').select2();
    $('#sub_tema_id').select2();
    $('#tipo_proceso_id').select2();
    
    $('#tercero_id').select2({		  
        placeholder: 'Seleccione un tercero',
        ajax: {
          url: 'buscadores/terceros.search.php?empresa_id='+empresa_id,
          dataType: 'json',
          delay: 250,
          processResults: function (data) {

            return {                     
              results: data
            };
          },
         cache: true
        }
      });
      
      
      $('#usuario_asignado_id').select2({		  
        placeholder: 'Asignar a...',
        ajax: {
          url: 'buscadores/usuarios.search.php',
          dataType: 'json',
          delay: 250,
          processResults: function (data) {

            return {                     
              results: data
            };
          },
         cache: true
        }
      });
    
}

function listar_adjuntos_procesos(proceso_id=''){
    var idDiv="div_adjuntos_proceso";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('proceso_id', proceso_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/procesos_juridicos.draw.php',// se indica donde llegara la informacion del objecto
        
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


function add_events_dropzone_procesos(){
    Dropzone.autoDiscover = false;
           
    urlQuery='procesadores/procesos_juridicos.process.php';
    var proceso_id=$("#proceso_adjuntos").data("proceso_id");
    var empresa_id=document.getElementById('empresa_id').value; 
    
    var myDropzone = new Dropzone("#proceso_adjuntos", { url: urlQuery,paramName: "adjunto_proceso"});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 2);
            formData.append("proceso_id", proceso_id);
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
                listar_adjuntos_procesos(proceso_id);
            }else if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
            }else{
                alert(data);
            }

        });
    listar_adjuntos_procesos(proceso_id);
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


function confirmar_crear_editar_proceso(proceso_id){
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
                
                crear_editar_proceso_juridico(proceso_id);              
                
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function crear_editar_proceso_juridico(proceso_id){
    
    var btnEnviar = "btn_guardar";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var empresa_id = document.getElementById('empresa_id').value;  
    var tema_id = document.getElementById('tema_id').value;  
    var sub_tema_id = document.getElementById('sub_tema_id').value;  
    var tipo_proceso_id = document.getElementById('tipo_proceso_id').value; 
    var tercero_id = document.getElementById('tercero_id').value; 
    var usuario_asignado_id = document.getElementById('usuario_asignado_id').value; 
    var descripcion = document.getElementById('descripcion').value; 
    var anio_gravable = document.getElementById('anio_gravable').value; 
    var periodo = document.getElementById('periodo').value; 
    var estado = document.getElementById('estado').value; 
            
    var form_data = new FormData();
        form_data.append('Accion', 4);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('proceso_id', proceso_id);        
        form_data.append('tema_id', tema_id);
        form_data.append('sub_tema_id', sub_tema_id);
        form_data.append('tipo_proceso_id', tipo_proceso_id);
        form_data.append('tercero_id', tercero_id);
        form_data.append('usuario_asignado_id', usuario_asignado_id);
        form_data.append('descripcion', descripcion);
        form_data.append('anio_gravable', anio_gravable);
        form_data.append('periodo', periodo);
        
        form_data.append('estado', estado);
                      
        
        $.ajax({
        url: './procesadores/procesos_juridicos.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Creando proceso..");
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

dibujeListadoSegunID();
actualizar_contadores();
