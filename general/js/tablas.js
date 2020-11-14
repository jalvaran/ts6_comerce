/**
 * Controlador para los formularios generales del sistema
 * JULIAN ALVARAN 2020-10-06
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */
var json_filters=[]; 
        
function mostrar_spinner(mensaje){
    var cadena = '';            

        cadena += '<div id="spinner1" class="m-2 d-inline-block" style="position:fixed;top: 50%;left: 50%;z-index:1;text-align:center;color:red"> ';
            cadena += '<strong>'+mensaje+'</strong><br> ';
                cadena += '<div class="spinner-3">';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
                cadena += '<div class="bg-primary"></div>';
            cadena += '</div>';
        cadena += '</div>'; 
        var spinner = $(cadena);
        $("#div_spinner").prepend(spinner);
}

function ocultar_spinner(){
    $("#spinner1").remove();    
}

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
    var json_busquedas= JSON.stringify(json_filters);
    
    var BusquedasGenerales=$("#txtBusquedasGenerales").val();
    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('page', page);
        form_data.append('idDiv', idDiv);     
        form_data.append('empresa_id', empresa_id);     
        form_data.append('tab', tab);        
        form_data.append('BusquedasGenerales', BusquedasGenerales);  
        form_data.append('json_busquedas', json_busquedas);  
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

function add_filter_tab(empresa_id,table,idDiv,dibujante=1){
    var array_filter={};
    array_filter.tab=table;
    var column=document.getElementById('cmb_col_filtro').value;
    var condition=document.getElementById('cmb_condicion_filtro').value;
    var txt_filter=document.getElementById('txt_filtro').value;
    
    array_filter.col=column;
    array_filter.cond=condition;
    array_filter.txt_fil=txt_filter;
    json_filters.push(array_filter);
    if(dibujante==1){
        dibuja_tabla(empresa_id,table,1,idDiv);
    }
    if(dibujante==2){
        dibujeListadoSegunID();
    }
    
}

function clean_filter_tab(empresa_id,table,idDiv,dibujante=1){
    delete json_filters;
    json_filters=[];
    if(dibujante==1){
        dibuja_tabla(empresa_id,table,1,idDiv);
    }
    if(dibujante==2){
        dibujeListadoSegunID();
    }
}

function delete_filter(empresa_id,table,idDiv,id,dibujante=1){
    delete json_filters[id];
    
    if(dibujante==1){
        dibuja_tabla(empresa_id,table,1,idDiv);
    }
    if(dibujante==2){
        dibujeListadoSegunID();
    }
}

function consultar_vinculo_columna(empresa_id,table,idDiv){
    
    urlQuery='../../general/procesadores/tablas.process.php';    
    var column=document.getElementById('cmb_col_filtro').value;
    
    var form_data = new FormData();
        form_data.append('Accion', '2');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('table', table);
        form_data.append('column', column);
        
        $.ajax({
        url: urlQuery,
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                var html_input="";
                if(respuestas[1]=='0'){
                    html_input='<input type="text" id="txt_filtro" class="form-control ts_busqueda_filtro" style="width:150px;padding: 12px;"></input>';
                    document.getElementById('div_valor_filtro').innerHTML=html_input;
                }
                if(respuestas[1]=='1'){
                    html_input='<select id="txt_filtro" class="form-control ts_busqueda_filtro" style="width:150px;padding: 12px;"><option value="">Selecciona...</option></select>';
                    document.getElementById('div_valor_filtro').innerHTML=html_input;
                    
                    $('#txt_filtro').select2({		  
                        placeholder: 'Seleccione...',
                        ajax: {
                          url: '../../general/buscadores/campos_vinculados.search.php?empresa_id='+empresa_id+'&tabla_asociada='+respuestas[2]+'&campo_asociado='+respuestas[3]+'&campo_asociado_id='+respuestas[4]+'&campo_asociado_db='+respuestas[5],
                          dataType: 'json',
                          delay: 250,
                          
                          processResults: function (data) {

                            return {                     
                              results: data
                            };
                          },
                          error: function (e){
                              console.log(e);
                            
                          },
                         cache: true
                        }
                      });
                      
                      $('#txt_filtro').height(51);
                }
                
                
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
            }else{
                toastr.error(data,2000);          
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
}