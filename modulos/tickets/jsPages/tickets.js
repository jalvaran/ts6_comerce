/**
 * Controlador para realizar la administracion de los tickets
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

var div_general="DivDrawTickets";
var timer_listado_tickets;
/**
 * Cierra una ventana modal
 * @param {type} idModal
 * @returns {undefined}
 */
function CierraModal(idModal) {
    $("#"+idModal).modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
}


/**
 * Muestra u oculta un elemento por su id
 * @param {type} id
 * @returns {undefined}
 */

function MuestraOcultaXID(id){
    
    var estado=document.getElementById(id).style.display;
    if(estado=="none" | estado==""){
        document.getElementById(id).style.display="block";
    }
    if(estado=="block"){
        document.getElementById(id).style.display="none";
    }
    
}


/*
$('#CmbBusquedas').bind('change', function() {
    
    document.getElementById('CodigoBarras').value = document.getElementById('CmbBusquedas').value;
    BusquePrecioVentaCosto();
    
});

*/


function limpiar_timers_tickets(){
    clearTimeout(timer_listado_tickets);
}

function VerListadoTickets(Page=1){
    limpiar_timers_tickets();
    var empresa_id=document.getElementById('empresa_id').value;
    var Busqueda=document.getElementById('txtBusquedasGenerales').value;
    var CmbEstadoTicketsListado=document.getElementById('CmbEstadoTicketsListado').value;  
    var CmbFiltroUsuario=document.getElementById('CmbFiltroUsuario').value; 
    var departamentos_tickets=document.getElementById('departamentos_tickets').value; 
     
    var CmbTiposTicketsListado=document.getElementById('CmbTiposTicketsListado').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('Page', Page);
        form_data.append('empresa_id', empresa_id);
        form_data.append('Busqueda', Busqueda);
        form_data.append('CmbEstadoTicketsListado', CmbEstadoTicketsListado);
        form_data.append('CmbFiltroUsuario', CmbFiltroUsuario);
        form_data.append('departamentos_tickets', departamentos_tickets);
        form_data.append('CmbTiposTicketsListado', CmbTiposTicketsListado);
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById(div_general).innerHTML=data;
            
           timer_listado_tickets=setTimeout(VerListadoTickets, 60000,Page); 
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePagina(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPage').value;
    }
    VerListadoTickets(Page);
}

function FormularioNuevoTicket(){
    limpiar_timers_tickets();
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('empresa_id', empresa_id);
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Cargando...");
        },
        success: function(data){
            ocultar_spinner();
           document.getElementById('DivDrawTickets').innerHTML=data;
           //$("#CmbUsuarioDestino").select2();
           summernote_init();
           add_events_dropzone_tickets();
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function ver_ticket(ticket_id){
    limpiar_timers_tickets();
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('empresa_id', empresa_id);
        form_data.append('ticket_id', ticket_id);
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Cargando...");
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById('DivDrawTickets').innerHTML=data;
           
           summernote_init();
           add_events_dropzone_tickets();
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function frm_responder_ticket(ticket_id){
    limpiar_timers_tickets();
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('empresa_id', empresa_id);
        form_data.append('ticket_id', ticket_id);
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Cargando...");
        },
        success: function(data){
            ocultar_spinner();
           document.getElementById('DivDrawTickets').innerHTML=data;
           //$("#CmbUsuarioDestino").select2();
           summernote_init();
           add_events_dropzone_tickets();
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function responder_ticket(){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var ticket_estado=document.getElementById('ticket_estado').value;
    
    var mensaje=document.getElementById('mensaje').value;
    var mensaje_id=$("#btn_guardar").data("mensaje_id");
    var ticket_id=$("#btn_guardar").data("ticket_id");
    document.getElementById('btn_guardar').disabled=true;
    document.getElementById('btn_guardar').value="Guardando...";
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('empresa_id', empresa_id);
        form_data.append('ticket_estado', ticket_estado);
        
        form_data.append('mensaje', mensaje);
        form_data.append('mensaje_id', mensaje_id);
        form_data.append('ticket_id', ticket_id);
        
        $.ajax({
        url: 'procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                ver_ticket(ticket_id);
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}


function MarqueErrorElemento(idElemento){
    console.log(idElemento);
    if(idElemento==undefined){
       return; 
    }
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
}

function dibuje_menu_lateral_tickets(){
    var div_id="div_panel_body";
    var empresa_id=document.getElementById('empresa_id').value;
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('empresa_id', empresa_id);
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Construyendo menú');
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById(div_id).innerHTML=data;
           VerListadoTickets(1);
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function summernote_init(){
    $('.summernote-ts').summernote({
        height: 300
    });
}

function add_events_dropzone_tickets(){
    Dropzone.autoDiscover = false;
    
    
    
    urlQuery='procesadores/tickets.process.php';
    var mensaje_id=$("#tickets_adjuntos").data("mensaje_id");
    var ticket_id=$("#tickets_adjuntos").data("ticket_id");
    var empresa_id=document.getElementById('empresa_id').value; 
    
    var myDropzone = new Dropzone("#tickets_adjuntos", 
        {   url: urlQuery,
            paramName: "adjunto_ticket",
            addRemoveLinks: true
        
        } );
        
        myDropzone.on("sending", function(file, xhr, formData) { 
            var archivo_id=file.upload.uuid;
            formData.append("Accion", 4);
            formData.append("mensaje_id", mensaje_id);
            formData.append("ticket_id", ticket_id);
            formData.append("empresa_id", empresa_id);
            formData.append("archivo_id", archivo_id);
            
        });

        myDropzone.on("addedfile", function(file) {
            
            file.previewElement.addEventListener("click", function() {
                myDropzone.removeFile(file);
            });
            
        });
        
        myDropzone.on("removedfile", function(file) {
            var archivo_id=file.upload.uuid;
            eliminar_adjunto(mensaje_id,archivo_id)
            
        });

        myDropzone.on("success", function(file, data) {

            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
            }else{
                alert(data);
            }
            
        });
        
        myDropzone.on("complete", function(file) {
            $(".dz-remove").text("Click para eliminar");
        });
    
}


function eliminar_adjunto(mensaje_id,archivo_id){    
    
    var empresa_id =document.getElementById("empresa_id").value;
         
    var form_data = new FormData();
        
        form_data.append('Accion', 5);
        form_data.append('empresa_id', empresa_id);  
        form_data.append('mensaje_id', mensaje_id);  
        form_data.append('archivo_id', archivo_id);
                        
        $.ajax({
        url: 'procesadores/tickets.process.php',
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


function crear_ticket_mensaje(){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var tipo_ticket=document.getElementById('tipo_ticket').value;
    var departamento_id=document.getElementById('departamento_id').value;
    var asunto=document.getElementById('asunto').value;
    var mensaje=document.getElementById('mensaje').value;
    var mensaje_id=$("#btn_guardar").data("mensaje_id");
    var ticket_id=$("#btn_guardar").data("ticket_id");
    document.getElementById('btn_guardar').disabled=true;
    document.getElementById('btn_guardar').value="Guardando...";
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('empresa_id', empresa_id);
        form_data.append('tipo_ticket', tipo_ticket);
        form_data.append('departamento_id', departamento_id);
        form_data.append('asunto', asunto);
        form_data.append('mensaje', mensaje);
        form_data.append('mensaje_id', mensaje_id);
        form_data.append('ticket_id', ticket_id);
        
        $.ajax({
        url: 'procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                VerListadoTickets(1);
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}


function frm_tickets_departamento(item_edit=''){
    limpiar_timers_tickets();
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', 6);
        
        form_data.append('empresa_id', empresa_id);
        form_data.append('item_edit', item_edit);
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById(div_general).innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function listado_tickets_tipos(){
    limpiar_timers_tickets();
    dibuja_tabla(`get`,`tickets_tipo`,`1`,`DivDrawTickets`);
}

function listado_tickets_departamento(){
    limpiar_timers_tickets();
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', 7);
        
        form_data.append('empresa_id', empresa_id);
        
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById(div_general).innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function crear_editar_departamento_ticket(item_id){
    
    document.getElementById('btn_guardar_departamento').disabled=true;
    document.getElementById('btn_guardar_departamento').value="Guardando...";
    
    var empresa_id=document.getElementById('empresa_id').value;
    var nombre_departamento=document.getElementById('nombre_departamento').value;
    var correo_notificacion_general=document.getElementById('correo_notificacion_general').value;
    var cmb_usuario_asignado=document.getElementById('cmb_usuario_asignado').value;
    var cmb_estado_departamento=document.getElementById('cmb_estado_departamento').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('empresa_id', empresa_id);
        form_data.append('nombre_departamento', nombre_departamento);
        form_data.append('item_id', item_id);
        form_data.append('correo_notificacion_general', correo_notificacion_general);
        form_data.append('cmb_usuario_asignado', cmb_usuario_asignado);
        form_data.append('cmb_estado_departamento', cmb_estado_departamento);
        
        $.ajax({
        url: 'procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_guardar_departamento').disabled=false;
            document.getElementById('btn_guardar_departamento').value="Guardar";
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listado_tickets_departamento();
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_guardar_departamento').disabled=false;
            document.getElementById('btn_guardar_departamento').value="Guardar";
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}

function tickets_init(){
    dibuje_menu_lateral_tickets();
    $("#txtBusquedasGenerales").unbind();
    
    $("#txtBusquedasGenerales").keypress(function(e) {
        if(e.which == 13) {
          VerListadoTickets();
        }
      });
}

tickets_init();



