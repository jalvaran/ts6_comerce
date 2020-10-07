/*
 * javascript para controlar los eventos de la administracion de los equipos
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

$('#tipo_equipo').on('change',function () {        

    dibujeListadoSegunID(1);

});

$("#txtBusquedasGenerales").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            dibujeListadoSegunID(1);   
        }
    });
        
function add_events_frms(){
    
    $('#btn_form_orden_trabajo').on('click',function () {
        ConfirmarCrearEditar();
    });
    
    $('#orden_tabajo_tipo_id').on('change',function () {
        OcultarMostrarOpcionesPreventivo();        
    });
    
    $('#btn_agregar_tarea').on('click',function () {
        AgregarTareaOT();
    });
    
    $('#maquina_id').on('change',function () {
        obtenerComponentesMaquinas($(this).val());
    });
    /*
    $('#componente_id').on('change',function () {
        obtenerFrecuenciasComponente($(this).val());
    });
    
    $('#fecha_ultimo_mantenimiento').on('change',function () {
        CalculeFechaProgramada();
    });
    $('#frecuencia_dias').on('change',function () {
        CalculeFechaProgramada();
    });
    
    */
    
}


function add_events_close_order(){
    $("#BusquedaSuministros").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            dibujeListadoPartes();   
        }
    });
    
    $('#falla_id').on('change',function () {
        obtenerCausasFalla($(this).val());
    });
    
}

/**
 * Funciones de proposito general
 * @param {type} idElemento
 * @returns {undefined}
 */

function CalculeFechaProgramada(){
    var ultimoMantenimiento=document.getElementById('fecha_ultimo_mantenimiento').value;    
    var frecuencia_dias=document.getElementById('frecuencia_dias').value;
    
    urlQuery='procesadores/mantenimiento.process.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', '10');        
        form_data.append('ultimoMantenimiento', ultimoMantenimiento);
        form_data.append('frecuencia_dias', frecuencia_dias);
                     
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
                toastr.success(respuestas[1]);
                document.getElementById('fecha_programada').value=respuestas[2];
                
            }else if(respuestas[0]=="E1"){  
                //toastr.error(respuestas[1],'',2000);
               // MarqueErrorElemento(respuestas[2]);
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

function OcultarMostrarOpcionesPreventivo(){
    
    var tipo_mantenimiento=document.getElementById('orden_tabajo_tipo_id').value;  
        
    if(tipo_mantenimiento=='1' || tipo_mantenimiento==''){
        
        document.getElementById('divOpcionesPreventivo').style.display="none";
        document.getElementById('divMaquinas').style.display="block";
        document.getElementById('divOpcionesRutaVerificacion').style.display="none";
    }

    if(tipo_mantenimiento == '2'){
        
        document.getElementById('divOpcionesPreventivo').style.display="block";
        document.getElementById('divMaquinas').style.display="block";
        document.getElementById('divOpcionesRutaVerificacion').style.display="none";
    }
    
    if(tipo_mantenimiento == '3'){
        
        document.getElementById('divOpcionesPreventivo').style.display="none";
        document.getElementById('divMaquinas').style.display="none";
        document.getElementById('divOpcionesRutaVerificacion').style.display="block";
    }
    
}
        
function AgregarTareaOT(){
    urlQuery='procesadores/mantenimiento.process.php';    
    
    var orden_trabajo_id=document.getElementById('orden_trabajo_id').value;  
    var cmb_tarea_mantenimiento=document.getElementById('cmb_tarea_mantenimiento').value;  
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', '8'); 
        
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('cmb_tarea_mantenimiento', cmb_tarea_mantenimiento);
             
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
                toastr.success(respuestas[1]);
                listarTareasOT();
                
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

function eliminarItem(tabla_id,item_id){
    urlQuery='procesadores/mantenimiento.process.php';    
    
    
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', '9'); 
        
        form_data.append('empresa_id', empresa_id);
        form_data.append('tabla_id', tabla_id);
        form_data.append('item_id', item_id);
             
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
                toastr.error(respuestas[1]);
                if(tabla_id==1){
                    listarTareasOT();
                }
                if(tabla_id==2){
                    listar_insumos_agregados_ot();
                }
                if(tabla_id==3){
                    listar_fallas_agregadas_ot();
                }
                if(tabla_id==4){
                    listar_verificacion_agregadas_ot();
                }
                if(tabla_id==5){
                    listar_adjuntos_ot();
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


function obtenerFrecuenciasComponente(componente_id=''){
    
    urlQuery='procesadores/mantenimiento.process.php';    
    if(componente_id==''){
        var componente_id=document.getElementById('componente_id').value;
    }
    
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', '7'); 
        
        form_data.append('empresa_id', empresa_id);
        form_data.append('componente_id', componente_id);
                               
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
                var jsonComponente=JSON.parse(respuestas[1]);
                console.log(jsonComponente)
                if(jsonComponente.fecha_ultimo_mantenimiento == '' || jsonComponente.fecha_ultimo_mantenimiento == null ){
                    //$("#fecha_ultimo_mantenimiento").prop('disabled', false);
                    document.getElementById('fecha_ultimo_mantenimiento').value='';                    
                }else{
                    //$("#fecha_ultimo_mantenimiento").prop('disabled', true);
                    
                }
                document.getElementById('fecha_ultimo_mantenimiento').value=jsonComponente.fecha_ultimo_mantenimiento;
                //document.getElementById('frecuencia_dias').value=jsonComponente.frecuencia_mtto_dias;
                //document.getElementById('frecuencia_verificacion_dias').value=jsonComponente.frecuencia_verificacion_dias;
                //document.getElementById('frecuencia_horas').value=jsonComponente.frecuencia_mtto_horas;
                //document.getElementById('frecuencia_kilometros').value=jsonComponente.frecuencia_mtto_kilometros;
                CalculeFechaProgramada();
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


function obtenerComponentesMaquinas(maquina_id=''){
    urlQuery='procesadores/mantenimiento.process.php';    
    if(maquina_id==''){
        var maquina_id=document.getElementById('maquina_id').value;
    }
    
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', '6'); 
        
        form_data.append('empresa_id', empresa_id);
        form_data.append('maquina_id', maquina_id);
                               
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
                var jsonComponentes=JSON.parse(respuestas[1]);
                
                var $el = $("#componente_id");
                    $el.empty(); // remove old options
                    $.each(jsonComponentes, function(key,value) {
                         $el.append($("<option></option>")
                            .attr("value", value.ID).text(value.Nombre+" || Marca: "+value.Marca+" || Modelo: "+value.Modelo+" || Serie: "+value.NumeroSerie));
                    });
                //obtenerFrecuenciasComponente();
                
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


function obtenerCausasFalla(falla_id=''){
    urlQuery='procesadores/mantenimiento.process.php';    
    if(falla_id==''){
        var falla_id=document.getElementById('falla_id').value;
    }
    
    var empresa_id=document.getElementById('empresa_id').value;
        
    var form_data = new FormData();
        form_data.append('Accion', '19'); 
        
        form_data.append('empresa_id', empresa_id);
        form_data.append('falla_id', falla_id);
                               
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
                var jsonComponentes=JSON.parse(respuestas[1]);
                
                var $el = $("#causa_falla_id");
                    $el.empty(); // remove old options
                    $.each(jsonComponentes, function(key,value) {
                         $el.append($("<option></option>")
                            .attr("value", value.ID).text(value.Causa));
                    });
                
                
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
        dibuja_ordenes_trabajo(Page);
    }
    
    
}

/*
 * Funciones generales para crear formularios
 */

function frm_crear_editar_registro(edit_id=''){
    var idDiv="DivListado";
    var empresa_id=document.getElementById('empresa_id').value;
    var listado_id=document.getElementById('listado_id').value;
    var cmb_tipo_mantenimiento=document.getElementById('cmb_tipo_mantenimiento').value;
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('edit_id', edit_id);
        form_data.append('listado_id', listado_id);
        form_data.append('empresa_id', empresa_id);
        form_data.append('cmb_tipo_mantenimiento', cmb_tipo_mantenimiento);
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
            listarTareasOT();
            //obtenerFrecuenciasComponente();
            add_events_dropzone_ot();
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function form_cerrar_orden(orden_trabajo_id){
    var idDiv="DivListado";
    var empresa_id=document.getElementById('empresa_id').value;
    
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 4);  
        form_data.append('orden_trabajo_id', orden_trabajo_id);
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
            add_events_close_order();
            listar_insumos_agregados_ot();
            listar_fallas_agregadas_ot();
            listar_verificacion_agregadas_ot();
            add_events_dropzone_ot(1);
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
                    GuardarEditarOrdenTrabajo();
                }
                               
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}


function confirma_cierre_ot_preventiva(orden_trabajo_id){
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
                
                
                CerrarOrdenTrabajoPreventiva(orden_trabajo_id);
              
                               
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function CerrarOrdenTrabajoPreventiva(orden_trabajo_id){
    
    urlQuery='procesadores/mantenimiento.process.php';    
    
    var btnEnviar = "btn_form_cierre_orden_trabajo";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var empresa_id=document.getElementById('empresa_id').value;
        
    var fecha_cierre=document.getElementById('fecha_cierre').value;
    //var verificacion_orden=document.getElementById('verificacion_orden').value;
    var horas_ultimo_mantenimiento=document.getElementById('horas_ultimo_mantenimiento').value;
    var tiempo_parada=document.getElementById('tiempo_parada').value;
    var kilometros_ultimo_mantenimiento=document.getElementById('kilometros_ultimo_mantenimiento').value;
    var tecnico_id=document.getElementById('tecnico_id').value;
    var observaciones_cierre=document.getElementById('observaciones_cierre').value;
            
    var form_data = new FormData();
        form_data.append('Accion', '13');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('fecha_cierre', fecha_cierre);
        //form_data.append('verificacion_orden', verificacion_orden);
        form_data.append('horas_ultimo_mantenimiento', horas_ultimo_mantenimiento);
        form_data.append('tiempo_parada', tiempo_parada);
        form_data.append('kilometros_ultimo_mantenimiento', kilometros_ultimo_mantenimiento);
        form_data.append('tecnico_id', tecnico_id);
        form_data.append('observaciones_cierre', observaciones_cierre);
                                       
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

function confirma_cierre_ot_correctiva(orden_trabajo_id){
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
                
                
                CerrarOrdenTrabajoCorrectiva(orden_trabajo_id);
              
                               
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function CerrarOrdenTrabajoCorrectiva(orden_trabajo_id){
    
    urlQuery='procesadores/mantenimiento.process.php';    
    
    var btnEnviar = "btn_form_cierre_orden_trabajo";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var empresa_id=document.getElementById('empresa_id').value;        
    var fecha_cierre=document.getElementById('fecha_cierre').value;   
    var tiempo_parada=document.getElementById('tiempo_parada').value;    
    var tecnico_id=document.getElementById('tecnico_id').value;
    var observaciones_cierre=document.getElementById('observaciones_cierre').value;
            
    var form_data = new FormData();
        form_data.append('Accion', '15');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('fecha_cierre', fecha_cierre);        
        form_data.append('tiempo_parada', tiempo_parada);        
        form_data.append('tecnico_id', tecnico_id);
        form_data.append('observaciones_cierre', observaciones_cierre);
                                       
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


function confirma_cierre_ot_verificacion(orden_trabajo_id){
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
                
                
                CerrarOrdenTrabajoVerificacion(orden_trabajo_id);
              
                               
                              
            } else {     
                swal("Cancelado", "Se ha cancelado el proceso :)", "error");   
            } 
        });
}

function CerrarOrdenTrabajoVerificacion(orden_trabajo_id){
    
    urlQuery='procesadores/mantenimiento.process.php';    
    
    var btnEnviar = "btn_form_cierre_orden_trabajo";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    
    var empresa_id=document.getElementById('empresa_id').value;        
    var fecha_cierre=document.getElementById('fecha_cierre').value;   
    var tiempo_dedicado=document.getElementById('tiempo_dedicado').value;    
    var tecnico_id=document.getElementById('tecnico_id').value;
    var observaciones_cierre=document.getElementById('observaciones_cierre').value;
            
    var form_data = new FormData();
        form_data.append('Accion', '17');  
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('fecha_cierre', fecha_cierre);        
        form_data.append('tiempo_dedicado', tiempo_dedicado);        
        form_data.append('tecnico_id', tecnico_id);
        form_data.append('observaciones_cierre', observaciones_cierre);
                                       
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

function GuardarEditarOrdenTrabajo(){
    
    urlQuery='procesadores/mantenimiento.process.php';    
    
    var btnEnviar = "btn_form_orden_trabajo";
    document.getElementById(btnEnviar).disabled=true;
    document.getElementById(btnEnviar).value="Enviando...";
    var edit_id=$("#btn_form_orden_trabajo").data("edit_id");
    var empresa_id=document.getElementById('empresa_id').value;
    var orden_trabajo_id=document.getElementById('orden_trabajo_id').value;
    
    var orden_tabajo_tipo_id=document.getElementById('orden_tabajo_tipo_id').value;
    var fecha_programada=document.getElementById('fecha_programada').value;
    var maquina_id=document.getElementById('maquina_id').value;
    var componente_id=document.getElementById('componente_id').value;
    var fecha_ultimo_mantenimiento=document.getElementById('fecha_ultimo_mantenimiento').value;
    var frecuencia_dias=document.getElementById('frecuencia_dias').value;
    //var frecuencia_verificacion_dias=document.getElementById('frecuencia_verificacion_dias').value;
    var ruta_verificacion_id=document.getElementById('ruta_verificacion_id').value;
    var frecuencia_ruta_verificacion=document.getElementById('frecuencia_ruta_verificacion').value;
    
    var frecuencia_horas=document.getElementById('frecuencia_horas').value;
    var frecuencia_kilometros=document.getElementById('frecuencia_kilometros').value;
    var horas_programadas=document.getElementById('horas_programadas').value;
    var kilometros_programados=document.getElementById('kilometros_programados').value;
    var observaciones_orden=document.getElementById('observaciones_orden').value;
        
    var form_data = new FormData();
        form_data.append('Accion', '11');  
        form_data.append('edit_id', edit_id);
        form_data.append('empresa_id', empresa_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('orden_tabajo_tipo_id', orden_tabajo_tipo_id);
        form_data.append('fecha_programada', fecha_programada);
        form_data.append('maquina_id', maquina_id);
        form_data.append('componente_id', componente_id);
        form_data.append('fecha_ultimo_mantenimiento', fecha_ultimo_mantenimiento);
        form_data.append('frecuencia_dias', frecuencia_dias);
        form_data.append('frecuencia_horas', frecuencia_horas);
        form_data.append('frecuencia_kilometros', frecuencia_kilometros);
        
        form_data.append('horas_programadas', horas_programadas);
        form_data.append('kilometros_programados', kilometros_programados);
        
        form_data.append('ruta_verificacion_id', ruta_verificacion_id);
        form_data.append('frecuencia_ruta_verificacion', frecuencia_ruta_verificacion);
        form_data.append('observaciones_orden', observaciones_orden);
                               
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

/*
 * Funciones generales para listar tablas
 */

function dibuja_ordenes_trabajo(Page=1){
    var idDiv="DivListado";
    urlQuery='Consultas/mantenimiento.draw.php';  
    var empresa_id=document.getElementById('empresa_id').value;    
    var BusquedasGenerales=document.getElementById('txtBusquedasGenerales').value;
    var fecha_inicial=document.getElementById('fecha_inicial').value;    
    var fecha_final=document.getElementById('fecha_final').value;    
    var cmb_tipo_mantenimiento=document.getElementById('cmb_tipo_mantenimiento').value;    
    var cmb_estado=document.getElementById('cmb_estado').value;    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('Page', Page);       
        form_data.append('empresa_id', empresa_id);  
        form_data.append('BusquedasGenerales', BusquedasGenerales);
        form_data.append('fecha_inicial', fecha_inicial);  
        form_data.append('fecha_final', fecha_final);  
        form_data.append('cmb_tipo_mantenimiento', cmb_tipo_mantenimiento);  
        form_data.append('cmb_estado', cmb_estado);  
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


function listarTareasOT(orden_trabajo_id=''){
    var idDiv="divTareasOrdenTrabajo";
    
    if($('#'+idDiv).length<=0){
        return;
    }  
    
    $("#btn_agregar_tarea").prop('disabled', true);
    if(orden_trabajo_id===''){
        var orden_trabajo_id=document.getElementById('orden_trabajo_id').value;
    }
    var empresa_id=document.getElementById('empresa_id').value;
    
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 3);        
        form_data.append('orden_trabajo_id', orden_trabajo_id);
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
            $("#btn_agregar_tarea").prop('disabled', false);
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            $("#btn_agregar_tarea").prop('disabled', false);
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

function dibujeListadoPartes(){
    var idDiv="div_suministros_busqueda";
       
    var orden_trabajo_id=$("#btn_form_cierre_orden_trabajo").data("orden_trabajo_id");
    var empresa_id=document.getElementById('empresa_id').value;
    var BusquedaSuministros=document.getElementById('BusquedaSuministros').value;
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 5);        
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('empresa_id', empresa_id);
        form_data.append('BusquedaSuministros', BusquedaSuministros);
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


function AgregarInsumoAOrdenTrabajo(insumo_id,orden_trabajo_id){
    
    var btnEnviar = "btn_agregar_insumo_"+insumo_id;
    document.getElementById(btnEnviar).disabled=true;
    
    var id_caja_valor="TxtValorUnitario_"+insumo_id;
    var id_caja_cantidad="TxtCantidad_"+insumo_id;
    
    var valor_unitario=document.getElementById(id_caja_valor).value;    
    var cantidad=document.getElementById(id_caja_cantidad).value;
    var empresa_id=document.getElementById('empresa_id').value; 
    urlQuery='procesadores/mantenimiento.process.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', '12');   
        form_data.append('empresa_id', empresa_id);
        form_data.append('insumo_id', insumo_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('valor_unitario', valor_unitario);    
        form_data.append('cantidad', cantidad);
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
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                listar_insumos_agregados_ot(orden_trabajo_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
            }else{
                toastr.error(data,2000);          
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function listar_insumos_agregados_ot(orden_trabajo_id=''){
    
    var idDiv="div_suministros_agregados_ot";
    if($('#'+idDiv).length<=0){
        return;
    }   
    var empresa_id=document.getElementById('empresa_id').value;
    if(orden_trabajo_id==""){
        var orden_trabajo_id=$("#btn_form_cierre_orden_trabajo").data("orden_trabajo_id");
    }
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 6);        
        form_data.append('orden_trabajo_id', orden_trabajo_id);
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
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function agregar_falla_ot_correctivo(orden_trabajo_id){
    
    var btnEnviar = "btn_agregar_falla_ot_correctivo";
    document.getElementById(btnEnviar).disabled=true;
        
    var componente_id=document.getElementById('componente_id').value;    
    var falla_id=document.getElementById('falla_id').value;
    var causa_falla_id=document.getElementById('causa_falla_id').value;
    var empresa_id=document.getElementById('empresa_id').value; 
    urlQuery='procesadores/mantenimiento.process.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', '14');   
        form_data.append('empresa_id', empresa_id);
        form_data.append('componente_id', componente_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('falla_id', falla_id);    
        form_data.append('causa_falla_id', causa_falla_id);
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
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                listar_fallas_agregadas_ot(orden_trabajo_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
            }else{
                toastr.error(data,2000);          
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function listar_fallas_agregadas_ot(orden_trabajo_id=''){
    var idDiv="div_fallas_ot";
    if($('#'+idDiv).length<=0){
        return;
    }    
    var empresa_id=document.getElementById('empresa_id').value;
    if(orden_trabajo_id==""){
        var orden_trabajo_id=$("#btn_form_cierre_orden_trabajo").data("orden_trabajo_id");
    }
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 7);        
        form_data.append('orden_trabajo_id', orden_trabajo_id);
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
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function agregar_verificacion_ot(orden_trabajo_id){
    
    var btnEnviar = "btn_agregar_verificacion";
    document.getElementById(btnEnviar).disabled=true;
        
    var maquina_id=document.getElementById('maquina_id').value;    
    var horas_trabajo=document.getElementById('horas_trabajo').value;
    var kilometros_trabajo=document.getElementById('kilometros_trabajo').value;
    var empresa_id=document.getElementById('empresa_id').value; 
    urlQuery='procesadores/mantenimiento.process.php';  
    
    var form_data = new FormData();
        form_data.append('Accion', '16');   
        form_data.append('empresa_id', empresa_id);
        form_data.append('maquina_id', maquina_id);
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('horas_trabajo', horas_trabajo);    
        form_data.append('kilometros_trabajo', kilometros_trabajo);
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
            var respuestas = data.split(';'); //Armamos un vector separando los punto y coma de la cadena de texto
            if(respuestas[0]=="OK"){ 
                toastr.success(respuestas[1]);
                
                listar_verificacion_agregadas_ot(orden_trabajo_id);
                
            }else if(respuestas[0]=="E1"){  
                toastr.error(respuestas[1],'',2000);
                MarqueErrorElemento(respuestas[2]);
            }else{
                toastr.error(data,2000);          
            }
                    
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(btnEnviar).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function listar_verificacion_agregadas_ot(orden_trabajo_id=''){
    var idDiv="div_verificaciones_ot";
    if($('#'+idDiv).length<=0){
        return;
    }    
    var empresa_id=document.getElementById('empresa_id').value;
    if(orden_trabajo_id==""){
        var orden_trabajo_id=$("#btn_form_cierre_orden_trabajo").data("orden_trabajo_id");
    }
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 8);        
        form_data.append('orden_trabajo_id', orden_trabajo_id);
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
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}


function add_events_dropzone_ot(cierre_orden=0){
    Dropzone.autoDiscover = false;
           
    urlQuery='procesadores/mantenimiento.process.php';
    var orden_trabajo_id=$("#soportes_ot").data("ot_id");
    var empresa_id=document.getElementById('empresa_id').value; 
    
    var myDropzone = new Dropzone("#soportes_ot", { url: urlQuery,paramName: "adjunto_ot"});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 18);
            formData.append("orden_trabajo_id", orden_trabajo_id);
            formData.append("empresa_id", empresa_id);
            formData.append("cierre_orden", cierre_orden);
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
                listar_adjuntos_ot(orden_trabajo_id);
            }else if(respuestas[0]=="E1"){
                toastr.warning(respuestas[1]);
            }else{
                swal(data);
            }

        });
    listar_adjuntos_ot(orden_trabajo_id);
}


function listar_adjuntos_ot(orden_trabajo_id="",idDiv="div_adjuntos_ot",idModal=""){
      
    var empresa_id=document.getElementById('empresa_id').value;
    if(orden_trabajo_id==''){
        var orden_trabajo_id=$("#soportes_ot").data("ot_id");
    }
    if(idModal!=""){
        openModal(idModal);
    }
    urlQuery='Consultas/mantenimiento.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 9);        
        form_data.append('orden_trabajo_id', orden_trabajo_id);
        form_data.append('empresa_id', empresa_id);
        form_data.append('idModal', idModal);
        $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', 
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            
        },
        complete: function(){
           
        },
        success: function(data){    
            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(idDiv).innerHTML=alertMensanje;
            swal("Error de Conexión");
          }
      });
}

dibujeListadoSegunID();

