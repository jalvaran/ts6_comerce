/**
 * Controlador para realizar la administracion de los tickets
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

var div_general="DivListados";
var timer_listado_tickets;
var listado_id=1;
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

function VerListadoSegunID(Page=1){
    if(listado_id==1){
        listado_usuarios(Page);
    }
    if(listado_id==2){
        dibuja_tabla(`get`,`ordenes_servicio_catalogo_tecnicos`,`1`,`DivListados`);
    }
    if(listado_id==3){
        dibuja_tabla(`get`,`ordenes_servicio_catalogo_insumos_tipo`,`1`,`DivListados`);
    }
    if(listado_id==4){
        dibuja_tabla(`get`,`ordenes_servicio_catalogo_insumos`,`1`,`DivListados`);
    }
    
}

function admin_init(){
    
    VerListadoSegunID();
    
    $("#txtBusquedasGenerales").unbind();
    
    $("#txtBusquedasGenerales").keypress(function(e) {
        if(e.which == 13) {
          VerListadoSegunID();
        }
      });
}


function listado_usuarios(Page=1){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var Busqueda=document.getElementById('txtBusquedasGenerales').value;
        
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('Page', Page);
        form_data.append('empresa_id', empresa_id);
        form_data.append('Busqueda', Busqueda);
                
        $.ajax({
        url: './Consultas/administrador_ordenes_servicio.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){
           ocultar_spinner();
           document.getElementById(div_general).innerHTML=data;
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            ocultar_spinner();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function frm_crear_editar_usuario_os(usuario_id=''){
    
    var empresa_id =document.getElementById("empresa_id").value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        form_data.append('empresa_id', empresa_id);
        form_data.append('usuario_id', usuario_id);
                
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/administrador_ordenes_servicio.draw.php',// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
           mostrar_spinner('Cargando...');
        },
        success: function(data){  
            ocultar_spinner();
            document.getElementById(div_general).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function crear_editar_usuario_os(){
    
    var empresa_id=document.getElementById('empresa_id').value;
    var nombre_usuario_os=document.getElementById('nombre_usuario_os').value;
    var apellido_usuario_os=document.getElementById('apellido_usuario_os').value;
    var identificacion_usuario_os=document.getElementById('identificacion_usuario_os').value;
    var login_usuario_os=document.getElementById('login_usuario_os').value;
    var email_usuario_os=document.getElementById('email_usuario_os').value;
    var password_usuario_os=document.getElementById('password_usuario_os').value;
    var cmb_habilitado=document.getElementById('cmb_habilitado').value;
    var usuario_id=$("#btn_guardar").data("usuario_id");
    
    document.getElementById('btn_guardar').disabled=true;
    document.getElementById('btn_guardar').value="Guardando...";
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('empresa_id', empresa_id);
        form_data.append('nombre_usuario_os', nombre_usuario_os);
        form_data.append('email_usuario_os', email_usuario_os);
        form_data.append('apellido_usuario_os', apellido_usuario_os);
        form_data.append('identificacion_usuario_os', identificacion_usuario_os);
        form_data.append('login_usuario_os', login_usuario_os);
        form_data.append('password_usuario_os', password_usuario_os);
        form_data.append('cmb_habilitado', cmb_habilitado);
        form_data.append('usuario_id', usuario_id);
        
        $.ajax({
        url: 'procesadores/administrador_ordenes_servicio.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Procesando");
        },
        success: function(data){
            ocultar_spinner();
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                VerListadoSegunID();
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);

                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
                
            }
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btn_guardar').disabled=false;
            document.getElementById('btn_guardar').value="Enviar";
            alert(xhr.status);
            alert(thrownError);
            
          }
      });
      
      
}

admin_init();



