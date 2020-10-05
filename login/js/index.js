/* 
 * Archivo controlador para verificar si los datos ingresados son validos
 */
function validar(e) {

  tecla = (document.all) ? e.keyCode : e.which;

  if (tecla==13) VerificaInicioSesion();

}

function VerificaInicioSesion(){
    
    var form_data = new FormData();
        form_data.append('US', document.getElementById('l-form-username').value);
        form_data.append('PA', document.getElementById('l-form-password').value);
    $.ajax({
        
        url: 'verificar.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                location.href ="../modulos/menu/Menu.php";
            }else if(data=='E1'){
                alertify.alert("El usuario está vacio");
                document.getElementById('l-form-username').focus();
            }else if(data=='E2'){
                alertify.alert("El password está vacio");
                document.getElementById('l-form-password').focus();    
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
