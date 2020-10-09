/**
 * Controlador para los formularios generales del sistema
 * JULIAN ALVARAN 2020-10-06
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */

function asigne_eventos_busqueda_general(empresa_id,tab,page,idDiv){
    $("#txtBusquedasGenerales").unbind();
    $('#txtBusquedasGenerales').on('keyup',function() {
        
        dibuja_tabla(empresa_id,tab,page,idDiv);
        
    });
}

function add_events_frms_ts6(){
    
    $('#btn_frm_ts6_registros').on('click',function () {         
        confirma_crear_editar_registro_db();
        return false;
    });
    
    $('.ts_select').select2();
    
}

function confirma_crear_editar_registro_db(){
    swal({   
            title: "Seguro que desea realizar esta acción?",   
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
                crear_editar_registro_db();
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}


function crear_editar_registro_db(){
    
    
    urlQuery='../../general/procesadores/tablas.process.php';    
    
    var btnEnviar = "btn_frm_ts6_registros";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    var edit_id=$("#btn_frm_ts6_registros").data("edit_id");
    var idDiv=$("#btn_frm_ts6_registros").data("div_id");    
    var db=$("#btn_frm_ts6_registros").data("db");
    var tab=$("#btn_frm_ts6_registros").data("table_ts6");
    
    var jsonFormulario=$('.ts_form').serialize();
        console.log("Datos: "+jsonFormulario);
    var form_data = new FormData();
        form_data.append('Accion', '1');  
        form_data.append('edit_id', edit_id);
        form_data.append('db', db);
        form_data.append('tab', tab);
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
                
                dibuja_tabla('get',tab,1,idDiv);
                
                if(typeof actualizar_contadores === 'function') {
                    
                    actualizar_contadores();
                }
                
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

function cambie_pagina_tb_ts6(page,db,table,idDiv){
    if(page==''){
        page=$("#cmb_page_tb_ts6").val();
    }
    dibuja_tabla('get',table,page,idDiv);
}

function dibuja_tabla(empresa_id,tab,page,idDiv){
    
    
    asigne_eventos_busqueda_general(empresa_id,tab,page,idDiv);
 
    urlQuery='../../general/Consultas/tablas.draw.php';  
    if(empresa_id=='get'){
        var empresa_id=$("#empresa_id").val();
    }
    
    var BusquedasGenerales=$("#txtBusquedasGenerales").val();
    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('page', page);
        form_data.append('idDiv', idDiv);     
        form_data.append('empresa_id', empresa_id);     
        form_data.append('tab', tab);        
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
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

function frm_agregar_editar_registro_ts6(db,tab,idEdit,idDiv){
    
    urlQuery='../../general/Consultas/tablas.draw.php';  
      
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('db', db);
        form_data.append('idDiv', idDiv);     
        form_data.append('tab', tab);     
        form_data.append('idEdit', idEdit);        
        
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
            add_events_frms_ts6();
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

