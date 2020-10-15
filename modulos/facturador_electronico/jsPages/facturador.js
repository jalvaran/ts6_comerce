/*
 * javascript para controlar los eventos del modulo facturador
 */
var listado_id=1;
function evento_busqueda(){
    $("#txtBusquedasGenerales").unbind();
    $('#txtBusquedasGenerales').on('keyup',function () {
        dibujeListadoSegunID();        
    });
}
evento_busqueda();
function add_events_frms(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var tipo_documento_id=document.getElementById("tipo_documento_id").value;
    
    $("#btn_agregar_prefactura").unbind();
    $("#prefactura_id").unbind();
    $("#item_id").unbind();
    $("#btn_agregar_item").unbind();
    $("#codigo_id").unbind();
    $("#tercero_id").unbind();
    $("#item_id").unbind();
    $("#tipo_documento_id").unbind();
    $("#precio_venta").unbind();
    $("#cantidad").unbind();
        
    $('#tercero_id').select2({		  
        placeholder: 'Seleccione un Tercero',
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
      
      $('#item_id').select2({		  
        placeholder: 'Seleccione un Item para Agregar',
        ajax: {
          url: 'buscadores/inventario_items_general.search.php?empresa_id='+empresa_id,
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
      
      $('#tipo_documento_id').on('change',function () {
            if($(this).val()==5 || $(this).val()==6){
                $('#div_documento_asociar').show("slow");
            }else{
                $('#div_documento_asociar').hide("slow");
            }
            $("#documento_asociado_id").unbind();
            $('#documento_asociado_id').select2({		  
                placeholder: 'Seleccione una factura a asociar',
                ajax: {
                  url: 'buscadores/vista_documentos_electronicos.search.php?empresa_id='+empresa_id,
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
              
            obtenga_resoluciones();
                  
            
        });
      
        $('#btn_agregar_prefactura').on('click',function () {
            agregar_prefactura();
        });
        
        $('#prefactura_id').on('change',function () {
            marque_activa_prefactura();
            dibuje_prefactura();
        });
        
        $('#item_id').on('change',function () {
            document.getElementById('codigo_id').value=$(this).val();
        });
        
        $('#btn_agregar_item').on('click',function () {
            agregar_item_prefactura();
            
        });
        
        $('#codigo_id').keypress(function(e) {
            
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code==13){
                agregar_item_prefactura();
            }
            
        });
        
        $('#precio_venta').keypress(function(e) {
            
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code==13){
                agregar_item_prefactura();
            }
            
        });
        
        $('#cantidad').keypress(function(e) {
            
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code==13){
                agregar_item_prefactura();
            }
            
        });
}


function add_event_list_items(){
       
    $("#btn_guardar_factura").unbind();
    $('#btn_guardar_factura').on('click',function () {
        confirma_crear_documento_electronico(1);        
    });
        
        
}

function mostrar_spinner(mensaje){
    var cadena = '';            

        cadena += '<div id="spinner1" class="m-2 d-inline-block" style="position:fixed;top: 50%;left: 40%;z-index:1;text-align:center;color:red"> ';
            cadena += '<strong>'+mensaje+'</strong><br> ';
                cadena += '<div class="spinner-3">';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
            cadena += '</div>';
        cadena += '</div>'; 
        var spinner = $(cadena);
        $("#div_spinner").prepend(spinner);
}

function ocultar_spinner(){
    $("#spinner1").remove();    
}



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
        listar_documentos_enviados(Page);
    }
    if(listado_id==2){
        listar_documentos_error(Page);
    }
    
    
}


function confirma_crear_documento_electronico(funcion_id){
    swal({   
            title: "Seguro que desea crear este documento?",   
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
                if(funcion_id==1){
                    crear_documento_electronico();
                }
                
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}


function obtenga_resoluciones(){
    
    var empresa_id = document.getElementById('empresa_id').value;   
    var tipo_documento_id = document.getElementById('tipo_documento_id').value;    
        
    var form_data = new FormData();
        form_data.append('Accion', 12);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('tipo_documento_id', tipo_documento_id);        
         
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Obteniento resoluciones..");
        },
        complete: function(){
           
        },
        success: function(data){
            ocultar_spinner();
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var resoluciones=$("#resolucion_id");
                resoluciones.find('option').remove();
                var opciones_resolucion=jQuery.parseJSON(respuestas[1]);
                $(opciones_resolucion).each(function(i, v){ // indice, valor
                    resoluciones.append('<option value="' + v.ID + '">' + v.prefijo +' '+v.numero_resolucion +' || '+v.desde+' - '+v.hasta+ '</option>');
                });
                
            }else if(respuestas[0]=="E1"){                
                alert(respuestas[1]);
            }else{
                alert(data);
            }
                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function reportar_documento_electronico_api(documento_electronico_id){
    
    var empresa_id = document.getElementById('empresa_id').value;    
        
    var form_data = new FormData();
        form_data.append('Accion', 7);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('documento_electronico_id', documento_electronico_id);        
         
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Reportando documento electrónico..");
        },
        complete: function(){
           
        },
        success: function(data){
            ocultar_spinner();
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                
                toastr.success(respuestas[1]);
                actualizar_contadores();
                
            }else if(respuestas[0]=="E1"){                
                alert(respuestas[1]);
                actualizar_contadores();
            }else{
                alert(data);
                actualizar_contadores();
            }
                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            actualizar_contadores();
            ocultar_spinner();            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function crear_documento_electronico(){
    
    var btnEnviar = "btn_guardar_factura";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var empresa_id = document.getElementById('empresa_id').value;  
    var prefactura_id = document.getElementById('prefactura_id').value;  
    var tercero_id = document.getElementById('tercero_id').value;  
    var resolucion_id = document.getElementById('resolucion_id').value; 
    var tipo_documento_id = document.getElementById('tipo_documento_id').value; 
    var documento_asociado_id = document.getElementById('documento_asociado_id').value; 
        
    var form_data = new FormData();
        form_data.append('Accion', 6);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('prefactura_id', prefactura_id);        
        form_data.append('tercero_id', tercero_id);
        form_data.append('resolucion_id', resolucion_id);
        form_data.append('tipo_documento_id', tipo_documento_id);
        form_data.append('documento_asociado_id', documento_asociado_id);
                
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner("Creando Documento Electrónico..");
        },
        complete: function(){
           ocultar_spinner();
        },
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Guardar Factura";
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                
                toastr.success(respuestas[1]);
                dibuje_prefactura();
                reportar_documento_electronico_api(respuestas[2]);
            }else if(respuestas[0]=="E1"){                
                toastr.error(respuestas[1]);
            }else{
                alert(data);
            }
                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Guardar Factura";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function agregar_item_prefactura(){
    
    var empresa_id = document.getElementById('empresa_id').value;  
    var prefactura_id = document.getElementById('prefactura_id').value;  
    var tercero_id = document.getElementById('tercero_id').value;  
    var resolucion_id = document.getElementById('resolucion_id').value;  
    var item_id = document.getElementById('item_id').value;  
    var codigo_id = document.getElementById('codigo_id').value;  
    var cantidad = document.getElementById('cantidad').value;  
    var precio_venta = document.getElementById('precio_venta').value; 
    var cmb_impuestos_incluidos = document.getElementById('cmb_impuestos_incluidos').value;  
    
    var form_data = new FormData();
        form_data.append('Accion', 3);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('prefactura_id', prefactura_id);        
        form_data.append('tercero_id', tercero_id);
        form_data.append('resolucion_id', resolucion_id);
        form_data.append('item_id', item_id);
        form_data.append('codigo_id', codigo_id);
        form_data.append('cantidad', cantidad);
        form_data.append('precio_venta', precio_venta);
        form_data.append('cmb_impuestos_incluidos', cmb_impuestos_incluidos);
        
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);
                dibuje_prefactura();
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

function editar_registro_prefactura(empresa_id,tab,item_id_edit,campo_edit,objeto_id){
    var valor_nuevo = document.getElementById(objeto_id).value;  
    
    var form_data = new FormData();
        form_data.append('Accion', 5);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('tab', tab);
        form_data.append('item_id_edit', item_id_edit);
        form_data.append('campo_edit', campo_edit);
        form_data.append('valor_nuevo', valor_nuevo);
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                //toastr.success(respuestas[1]);  
                
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

function EliminarItem(tabla_id,item_id){
    var empresa_id = document.getElementById('empresa_id').value;  
    
    var form_data = new FormData();
        form_data.append('Accion', 4);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('tabla_id', tabla_id);
        form_data.append('item_id', item_id);
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.error(respuestas[1]);  
                dibuje_prefactura();
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

function marque_activa_prefactura(){
    var empresa_id = document.getElementById('empresa_id').value;  
    var prefactura_id = document.getElementById('prefactura_id').value;  
    var form_data = new FormData();
        form_data.append('Accion', 2);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('prefactura_id', prefactura_id);
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);                
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


function agregar_prefactura(){
    var empresa_id = document.getElementById('empresa_id').value;        
    var form_data = new FormData();
        form_data.append('Accion', 1);        
        form_data.append('empresa_id', empresa_id);
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);
                formulario_facturador();
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

function actualizar_contadores(){
    var empresa_id = document.getElementById('empresa_id').value;        
    var form_data = new FormData();
        form_data.append('Accion', 11);        
        form_data.append('empresa_id', empresa_id);
        
        $.ajax({
        url: './procesadores/facturador.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                document.getElementById('sp_terceros').innerHTML=respuestas[1];
                document.getElementById('sp_inventario_items').innerHTML=respuestas[2];
                document.getElementById('sp_documentos_enviados').innerHTML=respuestas[3];
                document.getElementById('sp_errores').innerHTML=respuestas[4];
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

function formulario_facturador(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var idDiv="DivListados";
    urlQuery='Consultas/facturador.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('empresa_id', empresa_id);       
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            //document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_frms();
            dibuje_prefactura();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function formulario_nota_credito_debito(empresa_id,documento_electronico_id){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var idDiv="DivListados";
    urlQuery='Consultas/facturador.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 5);  
        form_data.append('empresa_id', empresa_id);       
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            //document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_frms();
            dibuje_prefactura();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function dibuje_prefactura(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var prefactura_id=document.getElementById("prefactura_id").value;
    var idDiv="div_items_prefactura";
    urlQuery='Consultas/facturador.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('empresa_id', empresa_id);  
        form_data.append('prefactura_id', prefactura_id);    
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            //document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_frms();
            add_event_list_items();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function listar_documentos_enviados(Page){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var txtBusquedasGenerales=document.getElementById("txtBusquedasGenerales").value;
    var idDiv="DivListados";
    urlQuery='Consultas/facturador.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 3);  
        form_data.append('empresa_id', empresa_id);  
        form_data.append('Page', Page); 
        form_data.append('txtBusquedasGenerales', txtBusquedasGenerales); 
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            //document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function listar_documentos_error(Page){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var txtBusquedasGenerales=document.getElementById("txtBusquedasGenerales").value;
    var idDiv="DivListados";
    urlQuery='Consultas/facturador.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 4);  
        form_data.append('empresa_id', empresa_id);  
        form_data.append('Page', Page); 
        form_data.append('txtBusquedasGenerales', txtBusquedasGenerales); 
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            //document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function ver_json_documento(empresa_id,documento_electronico_id){
    
    if(empresa_id==''){
        var empresa_id=document.getElementById("empresa_id").value;
    }
    
    openModal('modal_view');
    var idDiv="div_modal_view";
    urlQuery='procesadores/facturador.process.php';    
    var form_data = new FormData();
        form_data.append('Accion', 10);  
        form_data.append('empresa_id', empresa_id);  
        form_data.append('documento_electronico_id', documento_electronico_id); 
        
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            //document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}

actualizar_contadores();

formulario_facturador();