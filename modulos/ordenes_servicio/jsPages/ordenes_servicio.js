/**
 * Controlador para realizar la administracion de los tickets
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

var div_general="DivListados";
var modal_div="div_modal_view";
var modal_id="modal_view";
var listado_id=1;
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

function SeleccioneAccionFormularios(){
    var formulario_id=document.getElementById('formulario_id').value;
    if(formulario_id==1){
        confirma_cierre_orden();
    }
}

function VerListadoSegunID(Page=1){
    if(listado_id==1){
        listado_ordenes_servicio(Page);
    }
        
}

function ordenes_servicio_init(){
    
    VerListadoSegunID();
    
    $("#txtBusquedasGenerales").unbind();
    
    $("#txtBusquedasGenerales").keypress(function(e) {
        if(e.which == 13) {
          VerListadoSegunID();
        }
      });
}


function listado_ordenes_servicio(Page=1){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var Busqueda=document.getElementById('txtBusquedasGenerales').value;
    if($("#estado").length > 0) {
       var estado=document.getElementById('estado').value;
    }else{
       var estado='';
    }  
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('Page', Page);
        form_data.append('empresa_id', empresa_id);
        form_data.append('Busqueda', Busqueda);
        form_data.append('estado', estado);
                
        $.ajax({
        url: './Consultas/ordenes_servicio.draw.php',
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

function convertir_selects2(){
    var empresa_id=document.getElementById("empresa_id").value;
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
      
      $('#usuario_asignado').select2({		  
        placeholder: 'Seleccione un Usuario',
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
      
      $('#municipio').select2({		  
        placeholder: 'Seleccione una Ciudad',
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

function frm_crear_editar_orden_servicio(orden_servicio_id=''){
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_servicio_id', orden_servicio_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/ordenes_servicio.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){  
            ocultar_spinner();
            document.getElementById(div_general).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            convertir_selects2();
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function crear_editar_orden_servicio(){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var fecha_orden=document.getElementById('fecha_orden').value;
    var tercero_id=document.getElementById('tercero_id').value;
    var direccion=document.getElementById('direccion').value;
    var municipio=document.getElementById('municipio').value;
    var usuario_asignado=document.getElementById('usuario_asignado').value;
    var observaciones_iniciales=document.getElementById('observaciones_iniciales').value;
    
    var orden_servicio_id=$("#btn_guardar").data("orden_servicio_id");
    
    document.getElementById('btn_guardar').disabled=true;
    document.getElementById('btn_guardar').value="Guardando...";
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_orden', fecha_orden);
        form_data.append('tercero_id', tercero_id);
        form_data.append('direccion', direccion);
        form_data.append('municipio', municipio);
        form_data.append('usuario_asignado', usuario_asignado);
        form_data.append('observaciones_iniciales', observaciones_iniciales);
        form_data.append('orden_servicio_id', orden_servicio_id);
                
        $.ajax({
        url: 'procesadores/ordenes_servicio.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando...");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                VerListadoSegunID();
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


function entrega_de_materiales(orden_servicio_id){
    
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('empresa_id', empresa_id);
                        
        $.ajax({
        url: './Consultas/ordenes_servicio.draw.php',
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
           listado_insumos_orden(orden_servicio_id,1);
           $('#insumo_id_oi').select2({		  
            placeholder: 'Seleccione un Insumo',
            ajax: {
              url: 'buscadores/insumos.search.php?empresa_id='+empresa_id,
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
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function frm_ejecucion_orden(orden_servicio_id){
    
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('empresa_id', empresa_id);
                        
        $.ajax({
        url: './Consultas/ordenes_servicio.draw.php',
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
           listado_insumos_orden(orden_servicio_id,2);
           listado_insumos_disponibles_orden(orden_servicio_id);
                     
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function agregar_item_orden_insumo(orden_servicio_id,tipo_registro){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var fecha_orden_insumos=document.getElementById('fecha_orden_insumos').value;
    var insumo_id_oi=document.getElementById('insumo_id_oi').value;
    var cantidad_agregar=document.getElementById('cantidad_agregar').value;
        
    document.getElementById('btn_agregar_item').disabled=true;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_orden_insumos', fecha_orden_insumos);
        form_data.append('insumo_id_oi', insumo_id_oi);
        form_data.append('cantidad_agregar', cantidad_agregar);        
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('tipo_registro', tipo_registro);
                
        $.ajax({
        url: 'procesadores/ordenes_servicio.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando...");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_agregar_item').disabled=false;
            
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listado_insumos_orden(orden_servicio_id,1);
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_agregar_item').disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}

function agregar_item_orden_insumo_consumido(orden_servicio_id,tipo_registro,insumo_id_oi){
    
    var caja_id_cantidad="cantidad_agregar_"+insumo_id_oi;
    var empresa_id=document.getElementById('empresa_id').value;
    var fecha_orden_insumos=document.getElementById('fecha_orden_insumos').value;    
    var cantidad_agregar=document.getElementById(caja_id_cantidad).value;
        
    document.getElementById('btn_agregar_item').disabled=true;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_orden_insumos', fecha_orden_insumos);
        form_data.append('insumo_id_oi', insumo_id_oi);
        form_data.append('cantidad_agregar', cantidad_agregar);        
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('tipo_registro', tipo_registro);
                
        $.ajax({
        url: 'procesadores/ordenes_servicio.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando...");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_agregar_item').disabled=false;
            
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listado_insumos_orden(orden_servicio_id,2);
                listado_insumos_disponibles_orden(orden_servicio_id);
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_agregar_item').disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}


function listado_insumos_orden(orden_servicio_id,tipo_registro=''){
    
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('empresa_id', empresa_id);
        form_data.append('tipo_registro', tipo_registro);
                        
        $.ajax({
        url: './Consultas/ordenes_servicio.draw.php',
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
           document.getElementById('items_orden_insumos').innerHTML=data;
                     
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function listado_insumos_disponibles_orden(orden_servicio_id){
    
    var empresa_id=document.getElementById('empresa_id').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('empresa_id', empresa_id);
        
                        
        $.ajax({
        url: './Consultas/ordenes_servicio.draw.php',
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
           document.getElementById('items_orden_insumos_disponibles').innerHTML=data;
                     
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function EliminarItem(tabla_id,item_id,orden_servicio_id,tipo_registro=""){
    
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('empresa_id', empresa_id);
        form_data.append('tabla_id', tabla_id);
        form_data.append('item_id', item_id);
                        
        $.ajax({
        url: 'procesadores/ordenes_servicio.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando...");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_agregar_item').disabled=false;
            
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listado_insumos_orden(orden_servicio_id,tipo_registro);
                listado_insumos_disponibles_orden(orden_servicio_id);
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_agregar_item').disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}

function frm_cerrar_orden(orden_servicio_id){
    openModal(modal_id);
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 7);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_servicio_id', orden_servicio_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/ordenes_servicio.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){
            
            ocultar_spinner();
            document.getElementById(modal_div).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function confirma_cierre_orden(){
    
               
    alertify.confirm('Esta Seguro que desea cerrar esta orden? ',
        function (e) {
            if (e) {
                
                cerrar_orden_servicio();
                
                
            }else{
                alertify.error("Se canceló el proceso");
                return;
            }
        });
        
}

function cerrar_orden_servicio(){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var orden_servicio_id=document.getElementById('orden_servicio_id_cierre').value;
    var fecha_cierre_os=document.getElementById('fecha_cierre_os').value;
    var observaciones_cierre_orden=document.getElementById('observaciones_cierre_orden').value;
         
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_servicio_id', orden_servicio_id);
        form_data.append('fecha_cierre_os', fecha_cierre_os);
        form_data.append('observaciones_cierre_orden', observaciones_cierre_orden);
                        
        $.ajax({
        url: 'procesadores/ordenes_servicio.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando...");
        },
        success: function(data){
            ocultar_spinner();
                        
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                closeModal(modal_id);
                alertify.success(respuestas[1]);
                listado_ordenes_servicio();
                
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_agregar_item').disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}

ordenes_servicio_init();
