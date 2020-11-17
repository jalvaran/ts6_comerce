/*
 * javascript para controlar los eventos del modulo de reportes contables
 */

function add_events_frms(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var reporte_id=document.getElementById("reporte_id").value;    
    
    $("#tercero_id").unbind();
    $("#btnGenerar").unbind();
    $("#cuenta_contable").unbind();
        
    $('#btnGenerar').on('click',function () {
        confirma_crear_documento_contable(1);
    });
     
    $('#tercero_id').select2({		  
        placeholder: 'Tercero',
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
        placeholder: 'Cuenta Contable',
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
              
}

function MuestraXID(id){
    
    
    document.getElementById(id).style.display="block";
    
    
}


function OcultaXID(id){
    
    
    document.getElementById(id).style.display="none";
    
    
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


function dibuje_opciones_reporte(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var reporte_id=document.getElementById("reporte_id").value;
    var idDiv="div_opciones_reportes";
    urlQuery='Consultas/reportes_contables.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('empresa_id', empresa_id);    
        form_data.append('reporte_id', reporte_id);       
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
        
        success: function(data){    
            ocultar_spinner();
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            add_events_frms();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            ocultar_spinner();
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}

dibuje_opciones_reporte();

