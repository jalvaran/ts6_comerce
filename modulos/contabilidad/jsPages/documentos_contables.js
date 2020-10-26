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

function MuestraXID(id){
    
    
    document.getElementById(id).style.display="block";
    
    
}


function OcultaXID(id){
    
    
    document.getElementById(id).style.display="none";
    
    
}

function add_events_frms(){
    
    var empresa_id=document.getElementById("empresa_id").value;
        
    $("#btn_agregar_predocumento").unbind();    
    $("#tercero_id").unbind();
    $("#predocumento_id").unbind();
    $("#cuenta_contable").unbind();
    $("#Base").unbind();
    $("#Porcentaje").unbind();
    $("#Valor").unbind();
    $("#btn_agregar_item").unbind();
    $("#btn_guardar_documento").unbind();
    
    $('#btn_guardar_documento').on('click',function () {
        confirma_crear_documento_contable(1);
    });
    
    $('#btn_agregar_item').on('click',function () {
        agregar_movimiento();
    });
        
    $('#Valor').keypress(function(e) {
            
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code==13){
                agregar_movimiento();
            }
            
        });
        
    
    
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
      
      $('#cuenta_contable').select2({		  
        placeholder: 'Seleccione una Cuenta',
        ajax: {
          url: 'buscadores/cuentas_contables.search.php?empresa_id='+empresa_id,
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
      $('#cuenta_contable').on('change',function () {
            verifique_solicita_base();
        });
      
        $('#btn_agregar_predocumento').on('click',function () {
            agregar_predocumento();
        });
        
        $('#Base').on('keyup',function () {
            calcule_base();
        });
        
        $('#Porcentaje').on('keyup',function () {
            calcule_base();
        });
        $('#predocumento_id').on('change',function () {
            marque_activo_predocumento();
            formulario_documento_contable();
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
        dibuja_tabla(`get`,`terceros`,`1`,`DivListados`);
    }
    if(listado_id==2){
        dibuja_tabla(`get`,`contabilidad_plan_cuentas_subcuentas`,`1`,`DivListados`);
    }
    if(listado_id==3){
        dibuja_tabla(`get`,`contabilidad_documentos_contables`,`1`,`DivListados`);
    }
    
    
}


function confirma_crear_documento_contable(funcion_id){
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
                    crear_documento_contable();
                }
                
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function confirma_copiar_documento(empresa_id, documento_id){
    swal({   
            title: "Seguro que desea copiar este documento?",   
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
                
                copiar_documento_contable(empresa_id, documento_id);
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function confirma_anular_documento(empresa_id,documento_id){
    alertify.prompt('Por qué anula este documento?:',
    
    function(evt, value) { 
        if(value==undefined){
            alertify.error("Accion cancelada");
        }else{
            anular_documento_contable(empresa_id,documento_id,value);
            
        }
    }
            
            
    );
}

function copiar_documento_contable(empresa_id,documento_id){    
    
    var form_data = new FormData();
        form_data.append('Accion', 10);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('documento_id', documento_id);
                
        $.ajax({
        url: 'procesadores/documentos_contables.process.php',
        //dataType: 'json',
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Guardando...");
        },
        success: function(data){
            ocultar_spinner();
            
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);
                formulario_documento_contable();
                                
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


function anular_documento_contable(empresa_id,documento_id,observaciones_anulacion){
    
    
    var form_data = new FormData();
        form_data.append('Accion', 9);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('documento_id', documento_id);
        form_data.append('observaciones_anulacion', observaciones_anulacion);
        
        $.ajax({
        url: 'procesadores/documentos_contables.process.php',
        //dataType: 'json',
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Guardando...");
        },
        success: function(data){
            ocultar_spinner();
            
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.error(respuestas[1]);
                dibujeListadoSegunID();
                                
            }else if(respuestas[0]=="E1"){                
                toastr.error(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
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

function crear_documento_contable(){
    var boton_id="btn_guardar_documento";
    document.getElementById(boton_id).disabled=true;
    document.getElementById(boton_id).value="Guardando";
    var empresa_id = document.getElementById('empresa_id').value;  
    var predocumento_id = document.getElementById('predocumento_id').value;  
    var form_data = new FormData();
        form_data.append('Accion', 7);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('predocumento_id', predocumento_id);
        
        $.ajax({
        url: 'procesadores/documentos_contables.process.php',
        //dataType: 'json',
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Guardando...");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById(boton_id).disabled=false;
            document.getElementById(boton_id).value="Guardar Documento";
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);
                formulario_documento_contable();
                actualizar_contadores();
            }else if(respuestas[0]=="E1"){                
                toastr.error(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
            }else{
                alert(data);
            }
                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            document.getElementById(boton_id).disabled=false;
            document.getElementById(boton_id).value="Guardar Documento";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function editar_registro_documentos_contables(empresa_id,tab,item_id_edit,campo_edit,objeto_id){
    var valor_nuevo = document.getElementById(objeto_id).value;  
    if(item_id_edit=='get'){
        item_id_edit = document.getElementById('predocumento_id').value;
    }
    var form_data = new FormData();
        form_data.append('Accion', 1);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('tab', tab);
        form_data.append('item_id_edit', item_id_edit);
        form_data.append('campo_edit', campo_edit);
        form_data.append('valor_nuevo', valor_nuevo);
        
        $.ajax({
        url: './procesadores/documentos_contables.process.php',
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

function agregar_predocumento(){
    
    var empresa_id = document.getElementById('empresa_id').value;
   
    var form_data = new FormData();
        form_data.append('Accion', 2);        
        form_data.append('empresa_id', empresa_id);
        
        
        $.ajax({
        url: './procesadores/documentos_contables.process.php',
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
                formulario_documento_contable();
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
        form_data.append('Accion', 6);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('tabla_id', tabla_id);
        form_data.append('item_id', item_id);
        
        $.ajax({
        url: './procesadores/documentos_contables.process.php',
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
                dibuje_predocumento();
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

function marque_activo_predocumento(){
    var empresa_id = document.getElementById('empresa_id').value;  
    var predocumento_id = document.getElementById('predocumento_id').value;  
    var form_data = new FormData();
        form_data.append('Accion', 3);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('predocumento_id', predocumento_id);
        
        $.ajax({
        url: './procesadores/documentos_contables.process.php',
        //dataType: 'json',
        async: false,
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


function agregar_movimiento(){
    var empresa_id = document.getElementById('empresa_id').value;  
    var predocumento_id = document.getElementById('predocumento_id').value; 
    
    var cuenta_contable = document.getElementById('cuenta_contable').value;  
    var tercero_id = document.getElementById('tercero_id').value;  
    var concepto = document.getElementById('concepto').value;  
    var tipo_movimiento = document.getElementById('tipo_movimiento').value;  
    var referencia = document.getElementById('referencia').value;  
    var Porcentaje = document.getElementById('Porcentaje').value;  
    var TxtSolicitaBase = document.getElementById('TxtSolicitaBase').value;  
    var Base = document.getElementById('Base').value;  
    var Valor = document.getElementById('Valor').value;  
    
    
    var form_data = new FormData();
        form_data.append('Accion', 5);        
        form_data.append('empresa_id', empresa_id);
        form_data.append('predocumento_id', predocumento_id);
        
        form_data.append('cuenta_contable', cuenta_contable);
        form_data.append('tercero_id', tercero_id);
        form_data.append('concepto', concepto);
        form_data.append('tipo_movimiento', tipo_movimiento);
        form_data.append('referencia', referencia);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('TxtSolicitaBase', TxtSolicitaBase);
        form_data.append('Base', Base);
        form_data.append('Valor', Valor);
        
        
        $.ajax({
        url: './procesadores/documentos_contables.process.php',
        //dataType: 'json',
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);  
                dibuje_predocumento();
            }else if(respuestas[0]=="E1"){                
                toastr.error(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
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


function formulario_documento_contable(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var idDiv="DivListados";
    urlQuery='Consultas/documentos_contables.draw.php';    
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
            mostrar_spinner("Cargando...");
        },
        complete: function(){
           
        },
        success: function(data){    
            ocultar_spinner();
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_frms();
            dibuje_predocumento();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            ocultar_spinner();
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function dibuje_predocumento(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var predocumento_id=document.getElementById("predocumento_id").value;
    var idDiv="div_items_prefactura";
    urlQuery='Consultas/documentos_contables.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('empresa_id', empresa_id);  
        form_data.append('predocumento_id', predocumento_id);    
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
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function verifique_solicita_base(){
    var empresa_id=document.getElementById("empresa_id").value;
    var cuenta_contable = document.getElementById('cuenta_contable').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('cuenta_contable', cuenta_contable);
        form_data.append('empresa_id', empresa_id);
                
        $.ajax({
        url: 'procesadores/documentos_contables.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            if(data=='1'){
                MuestraXID('DivBases');
                document.getElementById("Valor").disabled=true;
                document.getElementById("Valor").value=0;
                document.getElementById("TxtSolicitaBase").value=1;
            }else{
                OcultaXID('DivBases');
                document.getElementById("Valor").disabled=false;
                document.getElementById("Valor").value=0;
                document.getElementById("TxtSolicitaBase").value=0;
            }
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function calcule_base(){
    
    var base = document.getElementById("Base").value;
    var porcentaje = document.getElementById("Porcentaje").value;
    
    if(porcentaje==0 || porcentaje==0 || porcentaje>100){
        var multiplo=1;
        document.getElementById("Porcentaje").value=100;
    }else{
        var multiplo=porcentaje/100;
    }
    document.getElementById("Valor").value=Math.round(base*multiplo).toFixed(2);
}

function actualizar_contadores(){
    var empresa_id = document.getElementById('empresa_id').value;        
    var form_data = new FormData();
        form_data.append('Accion', 8);        
        form_data.append('empresa_id', empresa_id);
        
        $.ajax({
        url: 'procesadores/documentos_contables.process.php',
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
                document.getElementById('sp_cuentas_contables').innerHTML=respuestas[2];
                document.getElementById('sp_documentos').innerHTML=respuestas[3];
                
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
actualizar_contadores();
formulario_documento_contable();
