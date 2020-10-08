/*
 * javascript para controlar los eventos del modulo facturador
 */

function add_events_frms(){
    
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
                //toastr.success(respuestas[1]);
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


formulario_facturador();