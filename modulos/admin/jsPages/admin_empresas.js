/*
 * javascript para controlar los eventos de creacion de empresas
 */

/*
 * Agregamos eventos a los botones o formularios
 */
/**
 * asignamos el evento click al boton para crear una nueva empresa
 * @type type
 */

$('#btnFrmNuevaEmpresa').on('click',function () {        

    frm_crear_empresa();

});

$('#btnActualizarListado').on('click',function () {        

    dibujeListadoEmpresas();

});

$("#txtBusquedasGenerales").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            dibujeListadoEmpresas(1);   
        }
    });
    
    
    
function add_events_frms(){
    $('#btn_frm_empresapro').on('click',function () {         
        ConfirmarCreacionEmpresa();
        return false;
    });
    
    $('.ts_select').select2();
    
}


/**
 * Funciones de proposito general
 * @param {type} idElemento
 * @returns {undefined}
 */

function MarqueErrorElemento(idElemento){
    
    if(idElemento==undefined){
       return; 
    }
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
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
        dibujeListadoEmpresas(Page);
    }
    
    
}


/*
 * Funciones generales para crear formularios
 */

function frm_crear_empresa(empresa_id=''){
    var idDiv="DivListado";
    urlQuery='Consultas/admin_empresas.draw.php';    
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
            document.getElementById(idDiv).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
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

/*
 * Funciones generales para guargar o editar registros
 */

function ConfirmarCreacionEmpresa(){
    swal({   
            title: "Seguro que desea Realizar guardar?",   
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
                GuardarEmpresa();
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function GuardarEmpresa(){
    
    var idDiv="DivListado";
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btn_frm_empresapro";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    var edit_id=$("#btn_frm_empresapro").data("edit_id");
        
    var jsonFormulario=$('.ts_form').serialize();
        console.log("Datos: "+jsonFormulario);
    var form_data = new FormData();
        form_data.append('Accion', '1');  
        form_data.append('edit_id', edit_id);
        form_data.append('jsonFormulario', jsonFormulario);
                       
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Enviar";
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                dibujeListadoEmpresas();
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
            }else{
                toastr.error(data,2000);          
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Enviar";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


/*
 * Funciones generales para listar tablas
 */

function dibujeListadoEmpresas(Page=1){
    var idDiv="DivListado";
    urlQuery='Consultas/admin_empresas.draw.php';  
    //var Busquedas=document.getElementById('TxtBusquedas').value;
    var BusquedasGenerales=document.getElementById('txtBusquedasGenerales').value;
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('Page', Page);
        //form_data.append('Busquedas', Busquedas);   
        form_data.append('BusquedasGenerales', BusquedasGenerales);   
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
            //add_events_frms();
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

/**
 * Dibuja los formularios para la creacion de una empresa en la plataforma de facturacion electronica
 * @param {type} empresa_id
 * @returns {undefined}
 */
function frm_crear_cliente_factura_electronica(empresa_id){
    var idDiv="DivListado";
    urlQuery='Consultas/admin_empresas.draw.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', 3);  
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
            dibuje_json_empresa(empresa_id);
            add_events_dropzone_centificado();
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function confirmaAccion(funcion,empresa_id){
    swal({   
            title: "Seguro que desea Realizar esta acción?",   
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
                if(funcion==1){
                    crear_empresa_api(empresa_id);
                }
                if(funcion==2){
                    crear_software_empresa_api(empresa_id);
                }
                if(funcion==3){
                    crear_certificado_digital_api(empresa_id);
                }
                
                if(funcion==4){
                    crear_resolucion_facturacion_api(empresa_id);
                }
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}


function crear_resolucion_facturacion_api(empresa_id){
    
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btnCrearResolucion";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var resolucion_prefijo=document.getElementById('resolucion_prefijo').value;    
    var resolucion_numero=document.getElementById('resolucion_numero').value;     
    var resolucion_fecha=document.getElementById('resolucion_fecha').value;
    var resolucion_llave=document.getElementById('resolucion_llave').value;
    var resolucion_rango_desde=document.getElementById('resolucion_rango_desde').value;
    var resolucion_rango_hasta=document.getElementById('resolucion_rango_hasta').value;
    var resolucion_fecha_desde=document.getElementById('resolucion_fecha_desde').value;
    var resolucion_fecha_hasta=document.getElementById('resolucion_fecha_hasta').value;
    var cmb_tipo_accion=document.getElementById('cmb_tipo_accion').value;
    var resolucion_api_id=document.getElementById('resolucion_api_id').value;
    var cmb_tipo_documento=document.getElementById('cmb_tipo_documento').value;
    
    var form_data = new FormData();
        form_data.append('Accion', '6');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('resolucion_prefijo', resolucion_prefijo);
        form_data.append('resolucion_numero', resolucion_numero);
        form_data.append('cmb_tipo_documento', cmb_tipo_documento);
        form_data.append('resolucion_fecha', resolucion_fecha);
        form_data.append('resolucion_llave', resolucion_llave);
        form_data.append('resolucion_rango_desde', resolucion_rango_desde);
        form_data.append('resolucion_rango_hasta', resolucion_rango_hasta);
        form_data.append('resolucion_fecha_desde', resolucion_fecha_desde);
        form_data.append('resolucion_fecha_hasta', resolucion_fecha_hasta);
        form_data.append('cmb_tipo_accion', cmb_tipo_accion);
        form_data.append('resolucion_api_id', resolucion_api_id);
        
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Ejecutar";
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                dibuje_resoluciones(empresa_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
                dibuje_resoluciones(empresa_id);
            }else{
                var idDiv="div_crear_resoluciones";
                document.getElementById(idDiv).innerHTML=data;
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function obtenerResoluciones(empresa_id){
    
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btnObtenerResoluciones";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Obteniendo...";
    
    var idDiv="div_crear_resoluciones";    
    
    var form_data = new FormData();
        form_data.append('Accion', '7');  
        form_data.append('empresa_id', empresa_id);
                               
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Obtener Resoluciones";
            
                
            document.getElementById(idDiv).innerHTML=data;
          
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Obtener Resoluciones";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function obtenerNumeraciones(empresa_id){
    
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btnObtenerNumeraciones";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Obteniendo...";
    
    var idDiv="div_crear_resoluciones";    
    
    var form_data = new FormData();
        form_data.append('Accion', '8');  
        form_data.append('empresa_id', empresa_id);
                               
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Obtener Numeraciones";
            
                
            document.getElementById(idDiv).innerHTML=data;
          
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Obtener Numeraciones";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function crear_empresa_api(empresa_id){
    
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btnCrearEmpresa";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
        
    
    var form_data = new FormData();
        form_data.append('Accion', '2');  
        form_data.append('empresa_id', empresa_id);
                               
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Click para Crear la Empresa en el API";
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                dibuje_json_empresa(empresa_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
            }else{
                var idDiv="div_crearEmpresa";
                document.getElementById(idDiv).innerHTML=data;
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Click para Crear la Empresa en el API";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function crear_software_empresa_api(empresa_id){
    
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btnCrearSoftware";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var software_id=document.getElementById('software_id').value;    
    var software_pin=document.getElementById('software_pin').value; 
    
    var form_data = new FormData();
        form_data.append('Accion', '3');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('software_id', software_id);
        form_data.append('software_pin', software_pin);
        
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Crear Software";
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                dibuje_json_software(empresa_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
                dibuje_json_software(empresa_id);
            }else{
                var idDiv="div_crear_software";
                document.getElementById(idDiv).innerHTML=data;
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Crear Software";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function crear_certificado_digital_api(empresa_id){
    
    urlQuery='procesadores/admin_empresas.process.php';    
    
    var btnEnviar = "btnCrearCertificado";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var clave_certificado=document.getElementById('clave_certificado').value;    
        
    var form_data = new FormData();
        form_data.append('Accion', '5');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('clave_certificado', clave_certificado);
        
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Crear Certificado";
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);                
                dibuje_json_certificado(empresa_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
                dibuje_json_certificado(empresa_id);
            }else{
                var idDiv="div_crear_certificado";
                document.getElementById(idDiv).innerHTML=data;
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            document.getElementById(btnEnviar).value="Crear Certificado";
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function dibuje_resoluciones(empresa_id){
    var idDiv="div_crear_resoluciones";
    urlQuery='Consultas/admin_empresas.draw.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', 7);  
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

function dibuje_json_empresa(empresa_id){
    var idDiv="div_crearEmpresa";
    urlQuery='Consultas/admin_empresas.draw.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', 4);  
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

function dibuje_json_software(empresa_id){
    var idDiv="div_crear_software";
    urlQuery='Consultas/admin_empresas.draw.php';  
    
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

function dibuje_json_certificado(empresa_id){
    var idDiv="div_crear_certificado";
    urlQuery='Consultas/admin_empresas.draw.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', 6);  
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function add_events_dropzone_centificado(){
    Dropzone.autoDiscover = false;
           
    urlQuery='procesadores/admin_empresas.process.php';
    var empresa_id=$("#certificado_empresa").data("empresa_id");
        
    var myDropzone = new Dropzone("#certificado_empresa", { url: urlQuery,paramName: "certificado_empresa",maxFiles: 1,acceptedFiles: '.p12'});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 4);            
            formData.append("empresa_id", empresa_id);
            
        });

        myDropzone.on("addedfile", function(file) {
            file.previewElement.addEventListener("click", function() {
                myDropzone.removeFile(file);
            });
        });

        myDropzone.on("success", function(file, data) {

            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                toastr.success(respuestas[1]);
                
            }else if(respuestas[0]=="E1"){
                toastr.warning(respuestas[1]);
            }else{
                swal(data);
            }

        });
   
}

dibujeListadoEmpresas();

