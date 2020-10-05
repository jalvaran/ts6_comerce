/**
 * Controlador para el administrador de notificaciones
 * JULIAN ALVARAN 2018-12-11
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */

function ConsultaAlertas(){
    
    var form_data = new FormData();
        form_data.append('idAccion', 1);
                

    $.ajax({
    url: '../../general/consultas/notificaciones.draw.php',
    //dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(data){

      if (data != "") {  
          if(data>0){
              document.getElementById("spNotificacionAlertas").innerHTML=data;
          }else if(data=="SA"){
              document.getElementById("spNotificacionAlertas").innerHTML="";
          }else{
              document.getElementById("spNotificacionAlertas").innerHTML="ERROR"+data;
          }
          
          setTimeout('ConsultaAlertas()',10*1000);
      }else {
        
        alertify.alert("No se pudo obtener informacion de las alertas");
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {

        alertify.alert('Error Al Exportar la tabla a CSV: '+xhr.status);
        alertify.alert(thrownError);
      }
    });    
}
ConsultaAlertas();

function AbreNotificaciones(){
    console.log("entra");
    $("#ModalNotificaciones").modal();
}
