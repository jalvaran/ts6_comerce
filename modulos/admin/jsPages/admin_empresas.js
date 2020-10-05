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
    
    var RazonSocial=document.getElementById('RazonSocial').value;
    var NIT=document.getElementById('NIT').value;
    var Direccion=document.getElementById('Direccion').value;
    var Telefono=document.getElementById('Telefono').value;
    var Celular=document.getElementById('Celular').value;
    var Ciudad=document.getElementById('Ciudad').value;
    var CodigoDaneCiudad=document.getElementById('CodigoDaneCiudad').value;
    var Regimen=document.getElementById('Regimen').value;
    var TipoPersona=document.getElementById('TipoPersona').value;
    var TipoDocumento=document.getElementById('TipoDocumento').value;    
    var Email=document.getElementById('Email').value;
    var WEB=document.getElementById('WEB').value;
    
    //var jsonFormulario=$('.ts_form').serialize();
      //  console.log("Datos: "+jsonFormulario);
    var form_data = new FormData();
        form_data.append('Accion', '1');  
        form_data.append('edit_id', edit_id);
        form_data.append('RazonSocial', RazonSocial);
        form_data.append('NIT', NIT);
        form_data.append('Direccion', Direccion);
        form_data.append('Telefono', Telefono);
        form_data.append('Celular', Celular);
        form_data.append('Ciudad', Ciudad);
        form_data.append('CodigoDaneCiudad', CodigoDaneCiudad);
        form_data.append('Regimen', Regimen);
        form_data.append('TipoPersona', TipoPersona);
        form_data.append('TipoDocumento', TipoDocumento);
        form_data.append('Email', Email);
        form_data.append('WEB', WEB);
               
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


dibujeListadoEmpresas();

