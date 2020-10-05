/*
 * javascript para controlar los eventos del administrador de backups
 */

/*
 * Agregamos eventos a los botones o formularios
 */
/**
 * asignamos el evento click al boton para crear una nueva empresa
 * @type type
 */

$('#btnFrmNuevoRegistro').on('click',function () {        
    
    frm_crear_editar_registro();

});

$('#btnActualizarListado').on('click',function () {        

    dibujeListadoSegunID(1);

});

$('#empresa_id').on('change',function () {        

    dibujeListadoSegunID(1);

});

$('#cmb_estado_backup').on('change',function () {        

    dibujeListadoSegunID(1);

});

$("#txtBusquedasGenerales").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            dibujeListadoSegunID(1);   
        }
    });
        
function add_events_frms(){
    $('#btn_frm_backups').on('click',function () { 
        
        ConfirmarCrearEditar();
        
        
    });
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
        dibujeListadoSegunID(Page);
    }
    
    
}


function dibujeListadoSegunID(Page=1){
    var listado_id = document.getElementById('listado_id').value;
    if(listado_id==1){
        listado_backups(Page);
    }
    
    
}

/*
 * Funciones generales para crear formularios
 */

function frm_crear_editar_registro(edit_id=''){
    var idDiv="DivListado";
    var empresa_id=document.getElementById('empresa_id').value;
    var listado_id=document.getElementById('listado_id').value;
    urlQuery='Consultas/backups.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('edit_id', edit_id);
        form_data.append('listado_id', listado_id);
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


function dibuja_frm_copia_bd(backup_id=''){
    var idDiv="DivListado";
    var empresa_id=document.getElementById('empresa_id').value;
    var listado_id=document.getElementById('listado_id').value;
    urlQuery='Consultas/backups.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 3);  
        form_data.append('backup_id', backup_id);
        form_data.append('listado_id', listado_id);
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
            
            dibuja_listado_bases_datos_backup(backup_id);
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function dibuja_listado_bases_datos_backup(backup_id=''){
    var idDiv="div_table_databases";
    var empresa_id=document.getElementById('empresa_id').value;
    var listado_id=document.getElementById('listado_id').value;
    urlQuery='Consultas/backups.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 4);  
        form_data.append('backup_id', backup_id);
        form_data.append('listado_id', listado_id);
        form_data.append('empresa_id', empresa_id);
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
                      
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
/*
 * Funciones generales para guargar o editar registros
 */

function ConfirmarCrearEditar(){
    swal({   
            title: "Seguro que desea guardar?",   
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
                
                var lista = document.getElementById("listado_id").value;
                
                if(lista==1){
                    guardar_backup();
                }
                               
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}


function guardar_backup(){
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_frm_backups";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    var edit_id=$("#btn_frm_backups").data("edit_id");
    var nombre=document.getElementById('nombre').value;
    
    var servidor_id_origen=document.getElementById('servidor_id_origen').value;
    var servidor_id_destino=document.getElementById('servidor_id_destino').value;
    var prefijo_origen=document.getElementById('prefijo_origen').value; 
    var prefijo_destino=document.getElementById('prefijo_destino').value; 
    var limite_registros=document.getElementById('limite_registros').value; 
    
    var form_data = new FormData();
        form_data.append('Accion', '1');  
        form_data.append('edit_id', edit_id);
        form_data.append('nombre', nombre);
        
        form_data.append('servidor_id_origen', servidor_id_origen);
        form_data.append('servidor_id_destino', servidor_id_destino);
        form_data.append('prefijo_origen', prefijo_origen);
        form_data.append('prefijo_destino', prefijo_destino);
        form_data.append('limite_registros', limite_registros);
                                       
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
                
                dibujeListadoSegunID();
                
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


function obtener_databases(backup_id){
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_get_database_"+backup_id;
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '2');  
        form_data.append('backup_id', backup_id);
                                               
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
                
                dibujeListadoSegunID();
                
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

function inicia_backup(backup_id){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '3');  
        form_data.append('backup_id', backup_id);
                                               
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_database').innerHTML=respuestas[2];
                document.getElementById('mensajes_databases').innerHTML=respuestas[1];
                var porcentaje=respuestas[4];
                $('#bar_status_database').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('bar_status_database').innerHTML=porcentaje+'%';
                registrar_tablas_backup(backup_id,respuestas[3]);
            }else if(respuestas[0]=="FIN"){ 
                var porcentaje=100;
                $('#bar_status_database').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('bar_status_database').innerHTML=porcentaje+'%';
                document.getElementById('mensajes_databases').innerHTML=respuestas[1];
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



function registrar_tablas_backup(backup_id,database_id){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '4');  
        form_data.append('database_id', database_id);
        form_data.append('backup_id', backup_id);
                                               
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_database').innerHTML=respuestas[2];
                document.getElementById('mensajes_databases').innerHTML=respuestas[1];
                obtener_total_tablas_database(backup_id,database_id);
                
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


function obtener_total_tablas_database(backup_id,database_id){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '5');  
        form_data.append('database_id', database_id);
        form_data.append('backup_id', backup_id);
                                               
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_database').innerHTML=respuestas[2];
                document.getElementById('mensajes_databases').innerHTML=respuestas[1];
                crear_tablas_backup(backup_id,database_id,respuestas[5]);
                
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

function crear_tablas_backup(backup_id,database_id,total_tablas){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '6');  
        form_data.append('database_id', database_id);
        form_data.append('backup_id', backup_id);
        form_data.append('total_tablas', total_tablas);
                                               
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_table').innerHTML=respuestas[2];
                document.getElementById('mensajes_tables_databases').innerHTML=respuestas[1];
                var porcentaje=respuestas[5];
                $('#bar_status_table_database').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('bar_status_table_database').innerHTML=porcentaje+'%';
                crear_tablas_backup(backup_id,database_id,total_tablas);
            }else if(respuestas[0]=="FIN"){  
                toastr.success(respuestas[1],'',2000);
                inicia_copia_registros(backup_id);
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


function inicia_copia_registros(backup_id){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '7');  
        
        form_data.append('backup_id', backup_id);
                                               
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_database').innerHTML=respuestas[2];
                document.getElementById('mensajes_databases').innerHTML=respuestas[1];
                obtenga_tabla_copia_registros(backup_id,respuestas[4]);
            }else if(respuestas[0]=="FIN"){  
                inicia_backup(backup_id);
                dibuja_listado_bases_datos_backup(backup_id);
                toastr.success(respuestas[1]);
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


function obtenga_tabla_copia_registros(backup_id,base_datos_id){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '8');  
        
        form_data.append('backup_id', backup_id);
        form_data.append('base_datos_id', base_datos_id);
                                               
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_table').innerHTML=respuestas[2];
                document.getElementById('mensajes_tables_databases').innerHTML=respuestas[1];
                var porcentaje=0;
                var tabla_id=respuestas[3];
                var total_registros=respuestas[4];
                $('#bar_status_table_database').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('bar_status_table_database').innerHTML=porcentaje+'%';
                copiar_registros(backup_id,base_datos_id,tabla_id,total_registros);
            }else if(respuestas[0]=="FIN"){ 
                toastr.success(respuestas[1]);
                inicia_copia_registros(backup_id);
                dibuja_listado_bases_datos_backup(backup_id);
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


function copiar_registros(backup_id,base_datos_id,tabla_id,total_registros){   //Obtengo las bases de datos de donde se deben crear tablas
    
    urlQuery='procesadores/backups.process.php';    
    
    var btnEnviar = "btn_copia_backup";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
        
    var form_data = new FormData();
        form_data.append('Accion', '9');  
        
        form_data.append('backup_id', backup_id);
        form_data.append('base_datos_id', base_datos_id);
        form_data.append('tabla_id', tabla_id);
        form_data.append('total_registros', total_registros);
                                                       
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
                //toastr.success(respuestas[1]);
                document.getElementById('name_table').innerHTML=respuestas[2];
                document.getElementById('mensajes_tables_databases').innerHTML=respuestas[1];
                var porcentaje=respuestas[4];
                $('#bar_status_table_database').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('bar_status_table_database').innerHTML=porcentaje+'%';
                copiar_registros(backup_id,base_datos_id,tabla_id,total_registros);
            }else if(respuestas[0]=="FIN"){  
                document.getElementById('name_table').innerHTML=respuestas[2];
                //document.getElementById('mensajes_tables_databases').innerHTML=respuestas[1];
                var porcentaje=100;
                $('#bar_status_table_database').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('bar_status_table_database').innerHTML=porcentaje+'%';
                obtenga_tabla_copia_registros(backup_id,base_datos_id);
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

function listado_backups(Page=1){
    var idDiv="DivListado";
    urlQuery='Consultas/backups.draw.php';  
    var empresa_id=document.getElementById('empresa_id').value;    
    var BusquedasGenerales=document.getElementById('txtBusquedasGenerales').value;
    var fecha_inicial=document.getElementById('fecha_inicial').value;    
    var fecha_final=document.getElementById('fecha_final').value;    
    var cmb_estado_backup=document.getElementById('cmb_estado_backup').value;    
    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('Page', Page);       
        form_data.append('empresa_id', empresa_id);  
        form_data.append('BusquedasGenerales', BusquedasGenerales);
        form_data.append('fecha_inicial', fecha_inicial);  
        form_data.append('fecha_final', fecha_final);  
        form_data.append('cmb_estado_backup', cmb_estado_backup);  
        
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

dibujeListadoSegunID();

