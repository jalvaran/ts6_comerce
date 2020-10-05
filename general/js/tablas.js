/**
 * Controlador para el administrador
 * JULIAN ALVARAN 2018-07-19
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */

/**
 * Escribe en una caja de texto
 * @param {type} idCaja
 * @param {type} Valor
 * @returns {undefined}
 */
function EscribirEnCaja(idCaja,Valor){
    document.getElementById(idCaja).value=Valor;
}
/**
 * Funcion para cambiar la palabra desc x asc y viceversa en la caja de texto utilizada para enviar el orden al dobujador de la tabla
 * @returns {undefined}
 */
function CambiarOrden(Tabla){
    var OrdenActual=document.getElementById(Tabla+'_orden').value;
    if(OrdenActual=='DESC'){
        document.getElementById(Tabla+'_orden').value='ASC';
    }else{
        document.getElementById(Tabla+'_orden').value='DESC';
    }
}


/**
 * Limpia las cajas de texto utilizadas para los filtros
 * @returns {undefined}
 */
function LimpiarFiltros(Tabla,DivTablas){
    document.getElementById(Tabla+'_orden').value='DESC';
    document.getElementById(Tabla+'_condicion').value='';
    document.getElementById(Tabla+'_ordenColumna').value='';
    document.getElementById(Tabla+'_page').value='1';
    if ($('#'+Tabla+'_DivFiltrosAplicados').length){
        document.getElementById(Tabla+'_DivFiltrosAplicados').innerHTML='';        
        DibujeTablaDB(Tabla,DivTablas);
    }
    
}
/**
 * Muestra u oculta un elemento por su id
 * @param {type} id
 * @returns {undefined}
 */

function MuestraOcultaXID(id){
    
    var estado=document.getElementById(id).style.display;
    if(estado=="none" | estado==""){
        document.getElementById(id).style.display="block";
    }
    if(estado=="block"){
        document.getElementById(id).style.display="none";
    }
    
}

/*
$(document).on("click",function(e) {
    var id='DivSubMenuLateral';              
    var container = $("#DivSubMenuLateral");
    var container2 = $("#aMenuPrincipal");

       if (!container.is(e.target) && container.has(e.target).length === 0) { 
           if(!container2.is(e.target) && container2.has(e.target).length === 0){
               document.getElementById(id).style.display="none";
           }
                       
       }
});
*/

/**
 * Agrega un condicional a la caja de texto utilizada para ese fin
 * @returns {undefined}
 */

function AgregaCondicionalDB(Tabla,DivTablas){
    var Columna = document.getElementById(Tabla+'_CmbColumna').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 15);
        form_data.append('Tabla', Tabla);
        form_data.append('Columna', Columna);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          if (respuestas[0] = "OK") { 
                var TablaAsociada = respuestas[1];
                var CampoAsociado = respuestas[2];
                var IDCampoAsociado = respuestas[3];
                
                var Columna = document.getElementById(Tabla+'_CmbColumna').value
                var Condicional = document.getElementById(Tabla+'_CmbCondicion').value
                var Busqueda = document.getElementById(Tabla+'_TxtBusquedaTablas').value
                var CondicionActual = document.getElementById(Tabla+'_condicion').value
                var CondicionFinal="";
                var Operador = "";
                var combo = document.getElementById(Tabla+"_CmbColumna");
                var ColumnaSeleccionada = combo.options[combo.selectedIndex].text;
                document.getElementById(Tabla+'_page').value=1;
                if(CondicionActual != ''){
                    Operador = " AND ";
                }
                switch(Condicional) {
                    case '=':   
                        if(TablaAsociada==''){
                            CondicionFinal= Operador + Columna + " = '" + Busqueda + "'";  
                        }else{
                            CondicionFinal= Operador + Columna + " = (SELECT "+IDCampoAsociado+" FROM "+ TablaAsociada +" WHERE "+ CampoAsociado +" = '"+ Busqueda + "' limit 1)";
                        }
                                    
                        break;
                    case '*': 
                        if(TablaAsociada==''){
                            CondicionFinal= Operador + Columna + " LIKE '%" + Busqueda + "%'"; 
                        }else{
                            CondicionFinal= Operador + Columna + " LIKE (SELECT "+IDCampoAsociado+" FROM "+ TablaAsociada +" WHERE "+ CampoAsociado +" LIKE '%"+ Busqueda + "%' limit 1)";
                        }
                        break;
                    case '>':  
                        if(TablaAsociada==''){
                            CondicionFinal= Operador + Columna + " > '" + Busqueda + "'"; 
                        }else{
                            CondicionFinal= Operador + Columna + " > (SELECT "+IDCampoAsociado+" FROM "+ TablaAsociada +" WHERE "+ CampoAsociado +" = '"+ Busqueda + "' limit 1)";
                        }
                        break;
                    case '<':            
                        CondicionFinal= Operador + Columna + " < '" + Busqueda + "'";            
                        break;
                    case '>=':            
                        CondicionFinal= Operador + Columna + " >= '" + Busqueda + "'";            
                        break;
                    case '<=':            
                        CondicionFinal= Operador + Columna + " <= '" + Busqueda + "'";            
                        break;
                    case '#%':    
                        if(TablaAsociada==''){
                            CondicionFinal= Operador + Columna + " LIKE '" + Busqueda + "%'"; 
                        }else{
                            CondicionFinal= Operador + Columna + " = (SELECT "+IDCampoAsociado+" FROM "+ TablaAsociada +" WHERE "+ CampoAsociado +" LIKE '"+ Busqueda + "%' limit 1)";
                        }
                        break;
                    case '<>':            
                        CondicionFinal= Operador + Columna + " <> '" + Busqueda + "'";            
                        break;    
                } 
                document.getElementById(Tabla+'_condicion').value=document.getElementById(Tabla+'_condicion').value+" "+CondicionFinal;

                DibujeTablaDB(Tabla,DivTablas);
                if(document.getElementById(Tabla+'_DivFiltrosAplicados').innerHTML==''){
                    document.getElementById(Tabla+'_DivFiltrosAplicados').innerHTML='<a href="#" id="aBorrarFiltros" onclick=LimpiarFiltros(`'+Tabla+'`,`'+DivTablas+'`); style="color:green"><span class="btn btn-block btn-primary btn-xs"><strong>Limpiar Filtros</strong></span></a> <strong>Filtros Aplicados: </strong><br>';
                }
                var lista='<i class="fa fa-circle-o text-aqua"></i><span> '+ColumnaSeleccionada+' '+ Condicional + ' '+Busqueda+' </span><br>';
                document.getElementById(Tabla+'_DivFiltrosAplicados').innerHTML=document.getElementById(Tabla+'_DivFiltrosAplicados').innerHTML+" "+lista;


                
          }else {
            alert("No hay resultados para la consulta");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })       
}


/*
 * Dibuja las operaciones que se pueden realizar en una tabla
 * @returns {undefined}
 */
function DibujaOperacionesTablas(Tabla){
       
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('Tabla', Tabla);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              document.getElementById('DivOpciones2').innerHTML=data;
              
          }else {
            alert("No hay resultados para la consulta");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Envia la peticion para realizar una consulta proveniente de las acciones
 * @returns {undefined}
 */
function ConsultaAccionesTablas(Tabla){
       
    var Columna = document.getElementById(Tabla+'_CmbColumnaAcciones').value
    var AccionTabla = document.getElementById(Tabla+'_CmbAccionTabla').value
    var CondicionActual = document.getElementById(Tabla+'_condicion').value    
    var combo = document.getElementById(Tabla+'_CmbColumnaAcciones');
    var ColumnaSeleccionada = combo.options[combo.selectedIndex].text;   
    
    var combo2 = document.getElementById(Tabla+'_CmbAccionTabla');
    var TxtAccionSeleccionada = combo2.options[combo2.selectedIndex].text;   
       
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('Tabla', Tabla);
        form_data.append('Columna', Columna);
        form_data.append('AccionTabla', AccionTabla);
        form_data.append('CondicionActual', CondicionActual);
        form_data.append('ColumnaSeleccionada', ColumnaSeleccionada);
        form_data.append('TxtAccionSeleccionada', TxtAccionSeleccionada);
        
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
                if(document.getElementById(Tabla+'_DivResultadosAcciones').innerHTML==''){
                    document.getElementById(Tabla+'_DivResultadosAcciones').innerHTML='<strong>Resultados: </strong><br>';
                }
                
                document.getElementById(Tabla+'_DivResultadosAcciones').innerHTML=document.getElementById(Tabla+'_DivResultadosAcciones').innerHTML+" "+data;
              
              
          }else {
            alert("No hay resultados para la consulta");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  
/**
 * Cambia el limite de las tablas
 * @returns {undefined}
 */
function CambiarLimiteTablas(Tabla,DivTablas){
    
    var Limite = document.getElementById(Tabla+'_CmbLimit').value;
    document.getElementById(Tabla+'_page').value=1;
    document.getElementById(Tabla+'_limit').value=Limite;
    DibujeTablaDB(Tabla,DivTablas);
}

function AvanzarPagina(Tabla,DivTablas){
    
    var PaginaActual = document.getElementById(Tabla+'_page').value;
    PaginaActual++;
    document.getElementById(Tabla+'_page').value=PaginaActual;
    DibujeTablaDB(Tabla,DivTablas);
}

function RetrocederPagina(Tabla,DivTablas){
    
    var PaginaActual = document.getElementById(Tabla+'_page').value;
    PaginaActual--;
    if(PaginaActual>0){
        document.getElementById(Tabla+'_page').value=PaginaActual;
        DibujeTablaDB(Tabla,DivTablas);
    }
}

function SeleccionaPagina(Tabla,DivTablas){
    
    var PaginaActual = document.getElementById(Tabla+'_CmbPage').value;
    document.getElementById(Tabla+'_page').value=PaginaActual;
    DibujeTablaDB(Tabla,DivTablas);
}

/**
 * Dibuja el control de campos
 * @returns {undefined}
 */
function DibujaControlCampos(){
    var Tabla = document.getElementById('TxtTabla').value;   
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('Tabla', Tabla);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              document.getElementById('DivControlCampos').innerHTML=data;
              //SeleccionarTabla(Tabla);
              //iCheckCSS(); //Esta funcion no permite realizar funciones, se deshabilita hasta que se encuentre solucion
              
          }else {
                alertify.alert("No se pudo dibujar el control de campos para la tabla");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  


/**
 * Dibuja las acciones
 * @param {type} Tabla
 * @returns {undefined}
 */
function DibujaAccionesTablas(){
    var Tabla = document.getElementById('TxtTabla').value;   
    var form_data = new FormData();
        form_data.append('Accion', 8);
        form_data.append('Tabla', Tabla);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              document.getElementById('DivOpciones3').innerHTML=data;
              //SeleccionarTabla(Tabla);
              //iCheckCSS(); //Esta funcion no permite realizar funciones, se deshabilita hasta que se encuentre solucion
              
          }else {
                alertify.alert("No se pudo dibujar las acciones para la tabla");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Oculta o muestra un campo de una tabla
 * @param {type} Tabla
 * @param {type} Campo
 * @param {type} DivTablas
 * @returns {undefined}
 */
function OcultaMuestraCampoTabla(Tabla,Campo,DivTablas){
       
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('Tabla', Tabla);
        form_data.append('Campo', Campo);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              if(data=="OK"){
                  DibujeTablaDB(Tabla,DivTablas);
                  alertify.success("Estado de la columna "+Campo+" Cambiado");
              }else{
                  alertify.alert(data);
              }
                            
          }else{
                alertify.alert("No se pudo dibujar el control de campos para la tabla");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  


/**
 * Dibuja el formulario para ingresar un registro nuevo
 * @param {type} Tabla
 * @returns {undefined}
 */
function DibujaFormularioNuevoRegistro(Tabla,idDivTabla,idModal='ModalAccionesConstructor',DivFormularios='DivFormularios'){
    
    $("#"+idModal).modal()

    var form_data = new FormData();
        form_data.append('Accion', 9);
        form_data.append('Tabla', Tabla);
        form_data.append('idDivTabla', idDivTabla);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              document.getElementById(DivFormularios).innerHTML=data;
              ConvierteSelects();
              EnfocaPrimerCampo('TxtNuevoRegistro');              
              AgregaEventosCamposEspeciales();
              ValidacionContrasenaSegura();
          }else {
                alertify.alert("No se pudo dibujar el formulario");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Dibuja el formulario para editar un registro
 * @param {type} Tabla
 * @param {type} DivTabla
 * @param {type} idEditar
 * @returns {undefined}
 */
function DibujaFormularioEditarRegistro(Tabla,DivTabla,idEditar){
    
    $("#ModalAccionesConstructor").modal()

    var form_data = new FormData();
        form_data.append('Accion', 10);
        form_data.append('Tabla', Tabla);
        form_data.append('idEditar', idEditar);
        form_data.append('idDivTabla', DivTabla);
        $.ajax({
        url: '../../general/Consultas/administrador.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              document.getElementById('DivFormularios').innerHTML=data;
              ConvierteSelects();
              EnfocaPrimerCampo('TxtNuevoRegistro');              
              AgregaEventosCamposEspeciales();
              ValidacionContrasenaSegura();
          }else {
              alertify.alert("No se pudo dibujar el formulario de edición");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Enfoca el primer campo de un formulario
 * @param {type} NombreCampos
 * @returns {undefined}
 */
function EnfocaPrimerCampo(NombreCampos){
  
  var form1Inputs = document.getElementsByName(NombreCampos);
  var idElemento=form1Inputs[0].id;
  document.getElementById(idElemento).focus();
    
}
/**
 * Obtiene los campos de un formulario
 * @param {type} NombreCampos
 * @returns {FormData|getFormInsert.form_data}
 */
function getFormInsert(NombreCampos){
  var form_data = new FormData();  
  var form1Inputs = document.getElementsByName(NombreCampos);
  
  for(let i=0; i<form1Inputs.length; i++){  
        
        form_data.append(form1Inputs[i].id, form1Inputs[i].value);
  }
  
  var Selects = document.getElementsByName("CmbInserts");
  
  for(let i=0; i<Selects.length; i++){  
        
        form_data.append(Selects[i].id, Selects[i].value);
  }
  
  return form_data;
}

/**
 * Obtiene los campos de un formulario
 * @param {type} NombreCampos
 * @returns {FormData|getFormInsert.form_data}
 */
function ConvierteSelects(){
  var form_data = new FormData();  
  var form1Inputs = document.getElementsByName("CmbInserts");
  var idElemento="";
  for(let i=0; i<form1Inputs.length; i++){  
        idElemento=form1Inputs[i].id;
        $('#'+idElemento).select2();        
  }
  
  return form_data;
}


/**
 * Cierra una ventana modal
 * @param {type} idModal
 * @returns {undefined}
 */
function CierraModal(idModal) {
    $("#"+idModal).modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
}
/**
 * Guarga un registro 
 * @param {type} event
 * @returns {undefined}
 */
function GuardarRegistro(event){
    event.preventDefault();
    var TipoFormulario = document.getElementById('TxtTipoFormulario').value;    
    var Tabla = document.getElementById('TxtTablaDB').value;    
    var DivTabla = document.getElementById('TxtIdDivTablaDB').value;    
    var form_data = getFormInsert('TxtNuevoRegistro');
    
    if(TipoFormulario == 'Insertar'){
        var idAccion = 1;
    }
    
    if(TipoFormulario == 'Editar'){
        var idEditar = document.getElementById('TxtIdEdit').value;
        var idAccion = 2;
        form_data.append('idEditar', idEditar);
    }
    
    
        form_data.append('idAccion', idAccion);
        form_data.append('Tabla', Tabla);
        $.ajax({
        url: '../../general/procesadores/tablas.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data == "OK") { 
              alertify.success("Datos Registrados correctamente");
              document.getElementById('DivFormularios').innerHTML="";
              
              CierraModal("ModalAccionesConstructor");
              DibujeTablaDB(Tabla,DivTabla);
              
          }else {
                alertify.alert("Error: "+data);
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}
    
    
/**
 * Agrega eventos a campos que requieran acciones especificas se dibujan desde ajax
 * @returns {undefined}
 */    
function AgregaEventosCamposEspeciales(){
    
    if ($('#Login').length) {
        document.getElementById("Login").addEventListener("change", VerificaLogin);
    }

    if ($('#RutaImagen').length) {
        document.getElementById("RutaImagen").addEventListener("change", ValideImagenEmpresa);
    }

    if ($('#Tipo').length) {
        document.getElementById("Tipo").addEventListener("change", VerificaTipoUsuario);
    }
}    

/**
 * Verifica si ya existe un usuario con el mismo login
 * @returns {undefined}
 */
function VerificaLogin(){
    var form_data = new FormData();
        form_data.append('idAccion', 1);
        form_data.append('Login', $('#Login').val());
      
    $.ajax({
        
        url: '../../general/buscadores/ConsultarLogin.search.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            //console.log(data)
           if(data=="Error"){
                alertify.alert("El usuario ya Existe");
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=true;
                }
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=true;
                }
            }else{
                alertify.success("Usuario disponible");
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=false;
                }
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=false;
                }
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            //alert("Error al tratar de borrar el archivo");
            alert(xhr.status);
            alert(thrownError);
          }
      })
}
/**
 * Verifica si ya existe un mismo tipo de usuario
 * @returns {undefined}
 */
function VerificaTipoUsuario(){
    var form_data = new FormData();
        form_data.append('idAccion', 1);
        form_data.append('Tipo', $('#Tipo').val());
      
    $.ajax({
        
        url: '../../general/buscadores/ConsultarLogin.search.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            //console.log(data)
           if(data=="Error"){
                alertify.alert("El Tipo de Usuario ya Existe");
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=true;
                }
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=true;
                }
            }else{
                alertify.success("Tipo de usuario disponible");
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=false;
                }
                if ($('#BtnModalGuardar').length) {
                    document.getElementById('BtnModalGuardar').disabled=false;
                }
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            //alert("Error al tratar de borrar el archivo");
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

/**
 * Verifica si la extension de la imagen de la empresa es valida
 * @returns {Boolean}
 */
function ValideImagenEmpresa(){
    var fileInput = document.getElementById('RutaImagen');
    var filePath = fileInput.value;
    var allowedExtensions = /(.jpg|.jpeg|.png|.gif)$/i;
    if(!allowedExtensions.exec(filePath)){
        alertify.alert('Solo se permiten archivos con extension .jpeg/.jpg/.png/.gif');
        fileInput.value = '';
        if ($('#BtnModalGuardar').length) {
            document.getElementById('BtnModalGuardar').disabled=true;
        }
        
        if ($('#BtnModalGuardar').length) {
            document.getElementById('BtnModalGuardar').disabled=true;
        }
        
        return false;
    }else{
        if ($('#BtnModalGuardar').length) {
            document.getElementById('BtnModalGuardar').disabled=false;
        }
        if ($('#BtnModalGuardar').length) {
            document.getElementById('BtnModalGuardar').disabled=false;
        }
        
        alertify.success("Imagen permitida");
    }
}

/**
 * Valida que la contraseña sea segura
 * @returns {undefined}
 */
function ValidacionContrasenaSegura(){
  var longitud = false,
    minuscula = false,
    numero = false,
    mayuscula = false;
  $('input[type=password]').keyup(function() {
    var pswd = $(this).val();
    if (pswd.length < 8) {
      $('#length').removeClass('valid').addClass('invalid');
      longitud = false;
    } else {
      $('#length').removeClass('invalid').addClass('valid');
      longitud = true;
    }

    //validate letter
    if (pswd.match(/[A-z]/)) {
      $('#letter').removeClass('invalid').addClass('valid');
      minuscula = true;
    } else {
      $('#letter').removeClass('valid').addClass('invalid');
      minuscula = false;
    }

    //validate capital letter
    if (pswd.match(/[A-Z]/)) {
      $('#capital').removeClass('invalid').addClass('valid');
      mayuscula = true;
    } else {
      $('#capital').removeClass('valid').addClass('invalid');
      mayuscula = false;
    }

    //validate number
    if (pswd.match(/\d/)) {
      $('#number').removeClass('invalid').addClass('valid');
      numero = true;
    } else {
      $('#number').removeClass('valid').addClass('invalid');
      numero = false;
    }
    if(longitud==true && minuscula == true && mayuscula == true && numero == true ){
        if ($('#BtnModalGuardar').length) {
                document.getElementById('BtnModalGuardar').disabled=false;
            }
        }else{
        if ($('#BtnModalGuardar').length) {
                document.getElementById('BtnModalGuardar').disabled=true;
            }
        }
  }).focus(function() {
    $('#pswd_info').show();
  }).blur(function() {
    $('#pswd_info').hide();
  });

  
}

/**
 * Dibuja una tabla en un div
 * @param {type} tabla
 * @param {type} idDiv
 * @returns {undefined}
 */
function DibujeTablaDB(tabla,idDiv){
    document.getElementById('DivCentralMensajes').innerHTML='<div id="GifProcess" style="text-align:center;position: absolute;top:50%;left:50%;padding:5px;"><img   src="../../images/loading.gif" alt="Cargando" height="100" width="100"></div>';  
    var condicion="";
    var OrdenColumna="";
    var Orden="";
    var Limit=10;
    var Page=1;
    
    var idCondicion=tabla+"_condicion"; 
    var idOrdenColumna=tabla+"ordenColumna";
    var idOrden=tabla+"_orden";
    var idLimit=tabla+"_limit";
    var idPage = tabla + "_page";
    
    if(document.getElementById(idCondicion)){  
        
        condicion=document.getElementById(idCondicion).value;
    }
    
    if(document.getElementById(idOrdenColumna)){  
        
        OrdenColumna=document.getElementById(idOrdenColumna).value;
    }
    
    if(document.getElementById(idOrden)){  
        
        Orden=document.getElementById(idOrden).value;
    }
    
    if(document.getElementById(idLimit)){  
        
        Limit=document.getElementById(idLimit).value;
        
    }
    
    if(document.getElementById(idPage)){  
        
        Page=document.getElementById(idPage).value;
        
    }
    var form_data = new FormData();
        form_data.append('Accion', 13);
        form_data.append('Tabla', tabla);
        form_data.append('Condicion', condicion);
        form_data.append('OrdenColumna', OrdenColumna);
        form_data.append('Orden', Orden);
        form_data.append('Limit', Limit);
        form_data.append('Page', Page);
        form_data.append('DivTablas', idDiv);

    $.ajax({
    url: '../../general/Consultas/administrador.draw.php',
    //dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(data){

      if (data != "") { 
          
          document.getElementById(idDiv).innerHTML=data;
          DibujePaginadorTablaDB(tabla,idDiv)
          document.getElementById('DivCentralMensajes').innerHTML="";
      }else {
        alert("No hay resultados para la consulta");
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {

        alertify.alert('Error en SeleccionarTabla'+xhr.status);
        alertify.alert(thrownError);
      }
    });    
}
/**
 * Dibuja las diferentes opciones para las tablas
 * @param {type} tabla
 * @param {type} idDivOpcionesTabla
 * @param {type} idDivTablas
 * @returns {undefined}
 */
function DibujeOpcionesTablaDB(tabla,idDivOpcionesTabla,idDivTablas){
            
    var form_data = new FormData();
        form_data.append('Accion', 11);
        form_data.append('Tabla', tabla);
        form_data.append('DivTablas', idDivTablas);
        form_data.append('DivOpcionesTablas', idDivOpcionesTabla);

    $.ajax({
    url: '../../general/Consultas/administrador.draw.php',
    //dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(data){

      if (data != "") { 
          document.getElementById(idDivOpcionesTabla).innerHTML=data;

      }else {
        alert("No hay resultados para la consulta");
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {

        alertify.alert('Error en SeleccionarTabla'+xhr.status);
        alertify.alert(thrownError);
      }
    }); 

}
/**
 * Selecciona y dibuja una tabla en un div
 * @param {type} Tabla
 * @param {type} idDivTabla
 * @param {type} idDivOpcionesTabla
 * @returns {undefined}
 */        
function SeleccioneTablaDB(Tabla,idDivTabla="DivTablaDB",idDivOpcionesTabla="DivOpcionesTablasDB"){
    if(document.getElementById('DivColapsableTablas')){         
        document.getElementById('DivColapsableTablas').style.display="block";
    }
    DibujeOpcionesTablaDB(Tabla,idDivOpcionesTabla,idDivTabla);
    DibujeTablaDB(Tabla,idDivTabla);
}


/**
 * Dibuja una tabla en un div
 * @param {type} tabla
 * @param {type} idDiv
 * @returns {undefined}
 */
function DibujePaginadorTablaDB(tabla,idDiv){
    //document.getElementById('DivCentralMensajes').innerHTML='<div id="GifProcess" style="text-align:center;position: absolute;top:50%;left:50%;padding:5px;"><img   src="../../images/cargando.gif" alt="Cargando" height="100" width="100"></div>';  
    var condicion="";
    var OrdenColumna="";
    var Orden="";
    var Limit=10;
    var Page=1;
    
    var idCondicion=tabla+"_condicion"; 
    var idOrdenColumna=tabla+"ordenColumna";
    var idOrden=tabla+"_orden";
    var idLimit=tabla+"_limit";
    var idPage = tabla + "_page";
    
    if(document.getElementById(idCondicion)){  
        
        condicion=document.getElementById(idCondicion).value;
    }
    
    if(document.getElementById(idOrdenColumna)){  
        
        OrdenColumna=document.getElementById(idOrdenColumna).value;
    }
    
    if(document.getElementById(idOrden)){  
        
        Orden=document.getElementById(idOrden).value;
    }
    
    if(document.getElementById(idLimit)){  
        
        Limit=document.getElementById(idLimit).value;
        
    }
    
    if(document.getElementById(idPage)){  
        
        Page=document.getElementById(idPage).value;
        
    }
    var form_data = new FormData();
        form_data.append('Accion', 14);
        form_data.append('Tabla', tabla);
        form_data.append('Condicion', condicion);
        form_data.append('OrdenColumna', OrdenColumna);
        form_data.append('Orden', Orden);
        form_data.append('Limit', Limit);
        form_data.append('Page', Page);
        form_data.append('DivTablas', idDiv);

    $.ajax({
    url: '../../general/Consultas/administrador.draw.php',
    //dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(data){

      if (data != "") {           
          document.getElementById(tabla+'_Paginador').innerHTML=data;
          
      }else {
        alert("No hay resultados para la consulta en el paginador");
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {

        alertify.alert('Error en SeleccionarTabla'+xhr.status);
        alertify.alert(thrownError);
      }
    });    
}

/**
 * Exporta una tabla a CSV
 * @param {type} tabla
 * @param {type} Separador
 * @param {type} idDivExport
 * @returns {undefined}
 */
function ExportarTablaDBCSV(tabla,Separador=";",idDivExport='DivCentralMensajes'){
    document.getElementById(idDivExport).innerHTML='<div id="GifProcess" style="text-align:center;position: absolute;top:50%;left:50%;padding:5px;"><img   src="../../images/loading.gif" alt="Cargando" height="100" width="100"></div>';   var condicion="";
    var OrdenColumna="";
    var Orden="";
    var Limit=10;
    var Page=1;
    
    var idCondicion=tabla+"_condicion"; 
    var idOrdenColumna=tabla+"ordenColumna";
    var idOrden=tabla+"_orden";
        
    if(document.getElementById(idCondicion)){  
        
        condicion=document.getElementById(idCondicion).value;
    }
    
    if(document.getElementById(idOrdenColumna)){  
        
        OrdenColumna=document.getElementById(idOrdenColumna).value;
    }
    
    if(document.getElementById(idOrden)){  
        
        Orden=document.getElementById(idOrden).value;
    }
    
   
    var form_data = new FormData();
        form_data.append('Opcion', 1);
        form_data.append('Tabla', tabla);
        form_data.append('Condicion', condicion);
        form_data.append('OrdenColumna', OrdenColumna);
        form_data.append('Orden', Orden);
        form_data.append('Separador', Separador);
        

    $.ajax({
    url: '../../general/procesadores/GeneradorCSV.process.php',
    //dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(data){

      if (data != "") {    
          //console.log(data);
          document.getElementById(idDivExport).innerHTML=data;
          
      }else {
        document.getElementById(idDivExport).innerHTML="";
        alertify.alert("No se pudo exportar la tabla "+tabla+" a CSV");
      }
    },
    timeout: 20000,
    error: function (xhr, ajaxOptions, thrownError) {

        alertify.alert('Error Al Exportar la tabla a CSV: '+xhr.status);
        alertify.alert(thrownError);
      }
    });    
}

//Se soluciona problema al cargar select2 en un modal (No borrar)
$.fn.modal.Constructor.prototype.enforceFocus = $.noop;