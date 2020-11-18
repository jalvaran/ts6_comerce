/*
 * javascript para controlar los eventos del modulo de reportes contables
 */

function add_events_frms(){
    
    var empresa_id=document.getElementById("empresa_id").value;
    var reporte_id=document.getElementById("reporte_id").value;    
    
    $("#tercero_id").unbind();
    $("#btnGenerar").unbind();
    $("#cmb_anio").unbind();
    $("#cuenta_contable").unbind();
        
    $('#cmb_anio').on('change',function () {
        var anio=$(this).val();
        var valor_fecha_inicial=anio+'-01-01';
        var valor_fecha_final=anio+'-12-31';
        var fecha_inicial = document.getElementById("fecha_inicial");
        
        fecha_inicial.setAttribute("min",valor_fecha_inicial);
        fecha_inicial.setAttribute("max",valor_fecha_final);
        var fecha_final = document.getElementById("fecha_final");
        fecha_final.setAttribute("min",valor_fecha_inicial);
        fecha_final.setAttribute("max",valor_fecha_final);
        
        document.getElementById("fecha_inicial").value=valor_fecha_inicial;
        document.getElementById("fecha_final").value=valor_fecha_final;
        
    });  
        
    $('#btnGenerar').on('click',function () {
        if(reporte_id==1){
            generar_auxiliar(empresa_id);
        }
        if(reporte_id==2){
            generar_balance_comprobacion(empresa_id);
        }
        if(reporte_id==3){
            generar_balance_comprobacion_terceros(empresa_id);
        }
        if(reporte_id==4){
            generar_estado_situacion_financiera(empresa_id);
        }    
        if(reporte_id==5){
            generar_estado_resultado_integral(empresa_id);
        } 
        
        
    });
     
    $('#tercero_id').select2({		  
        placeholder: 'Tercero',
        ajax: {
          url: 'buscadores/terceros_nit.search.php?empresa_id='+empresa_id,
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

function generar_auxiliar(empresa_id=''){
    if(empresa_id==''){
        var empresa_id=document.getElementById("empresa_id").value;
    }
    var fecha_inicial=document.getElementById("fecha_inicial").value;
    var fecha_final=document.getElementById("fecha_final").value;
    var cuenta_puc=document.getElementById("cuenta_puc").value;
    var tercero_id=document.getElementById("tercero_id").value;
    var centro_costos_id=document.getElementById("centro_costos_id").value;
    
    var idDiv="DivListados";
    urlQuery='Consultas/reportes_contables.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_inicial', fecha_inicial);
        form_data.append('fecha_final', fecha_final);
        form_data.append('cuenta_puc', cuenta_puc);
        form_data.append('tercero_id', tercero_id);
        form_data.append('centro_costos_id', centro_costos_id);
        
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
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            ocultar_spinner();
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}

function generar_balance_comprobacion(empresa_id=''){
    if(empresa_id==''){
        var empresa_id=document.getElementById("empresa_id").value;
    }
    var fecha_inicial=document.getElementById("fecha_inicial").value;
    var fecha_final=document.getElementById("fecha_final").value;
    var cuenta_puc=document.getElementById("cuenta_puc").value;
    var tercero_id=document.getElementById("tercero_id").value;
    var centro_costos_id=document.getElementById("centro_costos_id").value;
    var opciones_reporte=document.getElementById("opciones_reporte").value;
    
    var idDiv="DivListados";
    urlQuery='Consultas/reportes_contables.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_inicial', fecha_inicial);
        form_data.append('fecha_final', fecha_final);
        form_data.append('cuenta_puc', cuenta_puc);
        form_data.append('tercero_id', tercero_id);
        form_data.append('centro_costos_id', centro_costos_id);
        form_data.append('opciones_reporte', opciones_reporte);
        
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
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            ocultar_spinner();
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function generar_balance_comprobacion_terceros(empresa_id=''){
    if(empresa_id==''){
        var empresa_id=document.getElementById("empresa_id").value;
    }
    var fecha_inicial=document.getElementById("fecha_inicial").value;
    var fecha_final=document.getElementById("fecha_final").value;
    var cuenta_puc=document.getElementById("cuenta_puc").value;
    var tercero_id=document.getElementById("tercero_id").value;
    var centro_costos_id=document.getElementById("centro_costos_id").value;
    
    
    var idDiv="DivListados";
    urlQuery='Consultas/reportes_contables.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_inicial', fecha_inicial);
        form_data.append('fecha_final', fecha_final);
        form_data.append('cuenta_puc', cuenta_puc);
        form_data.append('tercero_id', tercero_id);
        form_data.append('centro_costos_id', centro_costos_id);
        
        
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
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            ocultar_spinner();
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}

function generar_estado_situacion_financiera(empresa_id=''){
    if(empresa_id==''){
        var empresa_id=document.getElementById("empresa_id").value;
    }
    var fecha_inicial=document.getElementById("fecha_inicial").value;
    var fecha_final=document.getElementById("fecha_final").value;
    var cmb_anio=document.getElementById("cmb_anio").value;   
    var centro_costos_id=document.getElementById("centro_costos_id").value;
    
    
    var idDiv="DivListados";
    urlQuery='Consultas/PDF_ReportesContables.draw.php';    
    var form_data = new FormData();
        form_data.append('idDocumento', 4);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_inicial', fecha_inicial);
        form_data.append('fecha_final', fecha_final);
        form_data.append('cmb_anio', cmb_anio);        
        form_data.append('centro_costos_id', centro_costos_id);
        
        
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
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            ocultar_spinner();
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });

}


function generar_estado_resultado_integral(empresa_id=''){
    if(empresa_id==''){
        var empresa_id=document.getElementById("empresa_id").value;
    }
    var fecha_inicial=document.getElementById("fecha_inicial").value;
    var fecha_final=document.getElementById("fecha_final").value;
    var cmb_anio=document.getElementById("cmb_anio").value;   
    var centro_costos_id=document.getElementById("centro_costos_id").value;
    
    
    var idDiv="DivListados";
    urlQuery='Consultas/PDF_ReportesContables.draw.php';    
    var form_data = new FormData();
        form_data.append('idDocumento', 2);
        form_data.append('empresa_id', empresa_id);
        form_data.append('fecha_inicial', fecha_inicial);
        form_data.append('fecha_final', fecha_final);
        form_data.append('cmb_anio', cmb_anio);        
        form_data.append('centro_costos_id', centro_costos_id);
        
        
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

