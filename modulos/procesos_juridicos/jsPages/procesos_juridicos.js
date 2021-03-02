/*
 * javascript para controlar los eventos del modulo procesos juridicos
 */
var listado_id=1;
var modal_id="modal_view";
var modal_body="div_modal_view";
var modal_btn="btnModalView";
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
    if(listado_id==2){
        dibuje_actos_administrativos(Page);
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
                /*
                document.getElementById('sp_temas').innerHTML=respuestas[1];
                document.getElementById('sp_sub_temas').innerHTML=respuestas[2];
                document.getElementById('sp_tipo_procesos').innerHTML=respuestas[3];
                document.getElementById('sp_terceros').innerHTML=respuestas[4];
                */
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
    if ( !$("#usuario_proceso").length ) {
        dibuje_usuarios();
      }
    
    var usuario_asignado=document.getElementById('usuario_proceso').value;
    if(usuario_asignado!=''){
        //console.log("entra");
        json_filters=[];
        var array_filter={};
        array_filter.tab="vista_procesos_juridicos";
        array_filter.col="usuario_asignado_id";
        array_filter.cond=1;
        array_filter.txt_fil=usuario_asignado;
        array_filter.visible=0;
        json_filters.push(array_filter);
        console.log(json_filters);
    }else{
        json_filters=[];
    }
    
    dibuja_tabla(`get`,`vista_procesos_juridicos`,page,`DivListados`);
    
}
function dibuje_usuarios(){
    var idDiv="div_usuarios";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 4);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/procesos_juridicos.draw.php',// se indica donde llegara la informacion del objecto
        async:false,
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
            //dibuja_procesos_juridicos(page);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
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
          url: 'buscadores/usuarios.search.php?empresa_id='+empresa_id,
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
      
      $('#codigo_dane_municipio').select2({		  
        placeholder: 'Seleccione un Municipio',
        ajax: {
          url: 'buscadores/municipios.search.php',
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

function listar_adjuntos_actos_admin(acto_id=''){
    var idDiv="div_adjuntos_actos";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('acto_id', acto_id);
                
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

function EliminarItem(tabla_id,item_id,acto_id){    
    
    var empresa_id =document.getElementById("empresa_id").value;
         
    var form_data = new FormData();
        
        form_data.append('Accion', 3);
        form_data.append('empresa_id', empresa_id);  
        form_data.append('item_id', item_id);  
        form_data.append('tabla_id', tabla_id);
                        
        $.ajax({
        url: 'procesadores/procesos_juridicos.process.php',
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
                    listar_adjuntos_actos_admin(acto_id);
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
    var codigo_dane_municipio = document.getElementById('codigo_dane_municipio').value; 
            
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
        form_data.append('codigo_dane_municipio', codigo_dane_municipio);
        
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


function dibuje_actos_administrativos(proceso_id=''){
    listado_id=2;
    var idDiv="DivListados";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
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
            liste_actos_administrativos(proceso_id);
                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function liste_actos_administrativos(proceso_id=''){
    
    var idDiv="div_actos_administrativos";
    //document.getElementById(idDiv).innerHTML='<div id="GifProcess">procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 5);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
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


function frm_agregar_editar_acto_proceso(proceso_id='',acto_id=""){
    var idDiv=modal_body;
    openModal(modal_id);
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 6);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('proceso_id', proceso_id);
        form_data.append('acto_id', acto_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/procesos_juridicos.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_dropzone_actos_admin();
            convertir_selects_frm_actos();            
            listar_adjuntos_actos_admin(acto_id);
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function add_events_dropzone_actos_admin(){
    Dropzone.autoDiscover = false;
           
    urlQuery='procesadores/procesos_juridicos.process.php';
    var acto_id=$("#acto_adjuntos").data("acto_id");
    var empresa_id=document.getElementById('empresa_id').value; 
    
    var myDropzone = new Dropzone("#acto_adjuntos", { url: urlQuery,paramName: "acto_adjunto"});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 2);
            formData.append("acto_id", acto_id);
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
                listar_adjuntos_actos_admin(acto_id);
            }else if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
            }else{
                alert(data);
            }

        });
    listar_adjuntos_actos_admin(acto_id);
}


function convertir_selects_frm_actos(){
    var empresa_id =document.getElementById("empresa_id").value;
    
      $('#entidad_id').select2({
        
        placeholder: 'Seleccione una entidad',
        dropdownParent: $("#"+modal_id),
        ajax: {
          url: 'buscadores/entidades.search.php?empresa_id='+empresa_id,
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


function SeleccioneAccionFormularios(){
    
    var formulario_id=document.getElementById('formulario_id').value; //determina el tipo de formulario que se va a guardar
    
    if(formulario_id==1){
        crear_editar_acto_administrativo();
    }
    
    
}

function crear_editar_acto_administrativo(){
    
    var btnEnviar = modal_btn;
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Guardando...";
    
    var empresa_id = document.getElementById('empresa_id').value;  
    var acto_id = document.getElementById('acto_id').value;  
    var proceso_id = document.getElementById('proceso_id').value;  
    var entidad_id = document.getElementById('entidad_id').value; 
    var fecha_acto = document.getElementById('fecha_acto').value; 
    var fecha_notificacion = document.getElementById('fecha_notificacion').value; 
    var acto_tipo_id = document.getElementById('acto_tipo_id').value; 
    var numero_acto = document.getElementById('numero_acto').value; 
    var observaciones = document.getElementById('observaciones').value; 
                
    var form_data = new FormData();
        form_data.append('Accion', 5);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('proceso_id', proceso_id);        
        form_data.append('acto_id', acto_id);
        form_data.append('entidad_id', entidad_id);
        form_data.append('fecha_acto', fecha_acto);
        form_data.append('fecha_notificacion', fecha_notificacion);        
        form_data.append('acto_tipo_id', acto_tipo_id);
        form_data.append('numero_acto', numero_acto);
        form_data.append('observaciones', observaciones);
            
        
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
                closeModal(modal_id);
                toastr.success(respuestas[1]);
                liste_actos_administrativos(proceso_id);
                
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
//actualizar_contadores();
