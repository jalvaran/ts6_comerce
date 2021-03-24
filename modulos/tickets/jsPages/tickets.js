/**
 * Controlador para realizar la administracion de los tickets
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

var div_general="DivDrawTickets";

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

/**
 * Limpia los divs de la compra despues de guardar
 * @returns {undefined}
 */
function LimpiarDivs(){
    document.getElementById('DivItemsCompra').innerHTML='';
    document.getElementById('DivTotalesCompra').innerHTML='';
}

/*
$('#CmbBusquedas').bind('change', function() {
    
    document.getElementById('CodigoBarras').value = document.getElementById('CmbBusquedas').value;
    BusquePrecioVentaCosto();
    
});

*/


function VerListadoTickets(Page=1){
    
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
           mostrar_spinner();
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

function CambiePagina(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPage').value;
    }
    VerListadoTickets(Page);
}

function FormularioNuevoTicket(){
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


function AgregarAdjunto(idMensaje,idTicket){
    var idBoton='BtnAgregarAdjunto_'+idMensaje;
    var UpFile='upAdjuntosMensajes_'+idMensaje;
    document.getElementById(idBoton).disabled=true;
    document.getElementById(idBoton).value="Guardando...";
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('idMensaje', idMensaje);
        form_data.append('idTicket', idTicket);
        form_data.append('upAdjuntosTickets', $('#'+UpFile).prop('files')[0]);
        
                
    $.ajax({
        //async:false,
        url: './procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                
                VerTicket(idTicket);
                
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                document.getElementById(idBoton).value="Adjuntar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                document.getElementById(idBoton).value="Adjuntar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            document.getElementById(idBoton).value="Adjuntar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function FormularioResponderTicket(idTicket){
    document.getElementById("DivDrawTickets").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idTicket', idTicket);
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivDrawTickets').innerHTML=data;
           
           $("#TxtMensaje").wysihtml5(); 
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function GuardarRespuesta(idTicket){
    document.getElementById('BtnGuardarTicket').disabled=true;
    document.getElementById('BtnGuardarTicket').value="Guardando...";
    
    
    var CmbCerrarTicket=document.getElementById('CmbCerrarTicket').value;
    var TxtMensaje=document.getElementById('TxtMensaje').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idTicket', idTicket);
        form_data.append('CmbCerrarTicket', CmbCerrarTicket);
        form_data.append('TxtMensaje', TxtMensaje);
        form_data.append('upAdjuntosTickets1', $('#upAdjuntosTickets1').prop('files')[0]);
        form_data.append('upAdjuntosTickets2', $('#upAdjuntosTickets2').prop('files')[0]);
        form_data.append('upAdjuntosTickets3', $('#upAdjuntosTickets3').prop('files')[0]);
                
    $.ajax({
        //async:false,
        url: './procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                
                VerTicket(idTicket);
                
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('BtnGuardarTicket').disabled=false;
                document.getElementById('BtnGuardarTicket').value="Guardar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnGuardarTicket').disabled=false;
                document.getElementById('BtnGuardarTicket').value="Guardar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('BtnGuardarTicket').disabled=false;
            document.getElementById('BtnGuardarTicket').value="Guardar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function MarqueErrorElemento(idElemento){
    console.log(idElemento);
    if(idElemento==undefined){
       return; 
    }
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
}

function CargarModulosProyectosEnSelect(SelectorACambiar){
    if(SelectorACambiar==1){
        var idSelector="CmbModuloProyecto";
        var SelectorPadre="CmbProyecto";
    }
    if(SelectorACambiar==2){
        var idSelector="CmbModulosTicketsListado";
        var SelectorPadre="CmbProyectosTicketsListado";
    }
    
    document.getElementById(idSelector).value='';
    if(document.getElementById("select2-"+idSelector+"-container")){
        document.getElementById("select2-"+idSelector+"-container").innerHTML='Seleccione un módulo';
    }
    
    var CmbProyectosTicketsListado=document.getElementById(SelectorPadre).value;
        $('#'+idSelector).select2({
            theme: "classic",
            placeholder: 'Seleccione un Módulo',
            ajax: {
              url: './buscadores/modulos_proyectos.search.php?idProyecto='+CmbProyectosTicketsListado,
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
           mostrar_spinner();
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById(div_id).innerHTML=data;
           
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



dibuje_menu_lateral_tickets();