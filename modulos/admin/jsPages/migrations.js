/**
 * Controlador para cargar los egresos
 * JULIAN ALVARAN 2019-05-24
 * TECHNO SOLUCIONES SAS 
 * 
 */

function ConfirmarMigracion(){
    
    swal({   
            title: "Seguro que desea ejecutar las migraciones?",   
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
                IniciarMigraciones();
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

/**
 * Verifica Si ya fue cargado el archivo a subir
 * @returns {undefined}
 */
function IniciarMigraciones(){
    openModal('modal_view');
    var idDiv="div_modal_view";
    document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
        
    var form_data = new FormData();
        form_data.append('Accion', 1);
        
        
        
        urlQuery='procesadores/migrations.process.php';                       
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){  //SI hay migraciones por hacer
                
                toastr.success(respuestas[1]);
                EjecutarMigraciones(1);
            }else if(respuestas[0]==="E1"){ 
                document.getElementById(idDiv).innerHTML=respuestas[1];
                return;      
                
            }else{
                document.getElementById(idDiv).innerHTML=data;
                alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

/**
 * Se ejecutan las migraciones
 * @returns {undefined}
 */
function EjecutarMigraciones(MigracionActual){
    
    var idDiv="div_modal_view";
    
    var form_data = new FormData();
        form_data.append('Accion', 2);        
        
        form_data.append('MigracionActual', MigracionActual);
                
        urlQuery='procesadores/migrations.process.php';                      
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               document.getElementById(idDiv).innerHTML=(respuestas[1]);
               toastr.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                                
                document.getElementById(idDiv).innerHTML=(respuestas[1]);
                
                return;                
            }else{
                
                
                document.getElementById(idDiv).innerHTML=data;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

