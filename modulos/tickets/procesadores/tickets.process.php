<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/tickets.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Ticket($idUser);
    //$obMail= new TS_Mail($idUser);
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear un ticket
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $tipo_ticket=$obCon->normalizar($_REQUEST["tipo_ticket"]);
            $departamento_id=$obCon->normalizar($_REQUEST["departamento_id"]);
            $asunto=$obCon->normalizar($_REQUEST["asunto"]);
            $mensaje=str_replace("drop","",$_REQUEST["mensaje"]);
            $mensaje=str_replace("delete","",$mensaje);
            $mensaje_id=$obCon->normalizar($_REQUEST["mensaje_id"]);
            $ticket_id=$obCon->normalizar($_REQUEST["ticket_id"]);
            
            if($tipo_ticket==''){
                
                exit("E1;Debe seleccionar tipo de ticket;tipo_ticket");
            }
            
            if($departamento_id==''){
                exit("E1;Debe seleccionar departamento;$departamento_id");
            }
            
            if($asunto==''){
                exit("E1;Debe escribir un asunto;asunto");
            }
            if($mensaje==''){
                exit("E1;Debe escribir un mensaje;mensaje");
            }
            if($mensaje_id==''){
                exit("E1;no se recibió el id del mensaje");
            }
            if($ticket_id==''){
                exit("E1;No se recibió el id del ticket");
            }
            $datos_departamento=$obCon->DevuelveValores("$db.tickets_departamentos", "ID", $departamento_id);
            
            $obCon->CrearTicket($db,$ticket_id,$tipo_ticket,$departamento_id,$asunto,$idUser,$datos_departamento["usuario_asignado"]);
            $obCon->AgregarMensajeTicket($db,$mensaje_id,$ticket_id, $mensaje, $idUser);
            
            exit("OK;Ticket creado");
        break; //fin caso 1
        
        case 2: //Agregar un Adjunto a un mensaje
            $idMensaje=$obCon->normalizar($_REQUEST["idMensaje"]);
            $idTicket=$obCon->normalizar($_REQUEST["idTicket"]);
            if($idMensaje==''){
                exit("E1;No se recibió el id del Mensaje");
            }
            if(!empty($_FILES['upAdjuntosTickets']['name'])){
                
                $info = new SplFileInfo($_FILES['upAdjuntosTickets']['name']);
                $Extension=($info->getExtension());  
                $Tamano=filesize($_FILES['upAdjuntosTickets']['tmp_name']);
                $carpeta="../../../soportes/Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta="../../../soportes/Tickets/$idTicket/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                opendir($carpeta);                
                $idAdjunto=uniqid(true);
                $destino=$carpeta.$idMensaje."_".$idAdjunto.".".$Extension;
                move_uploaded_file($_FILES['upAdjuntosTickets']['tmp_name'],$destino);
                
                $obCon->AgregarAdjuntoMensaje($destino,$Tamano, $_FILES['upAdjuntosTickets']['name'], $Extension, $idUser, $idMensaje);
                
            }else{
                exit("E1;No se recibió un archivo");
            }
            print("OK;Adjunto Agregado");            
            
        break; //fin caso 2
        
        case 3: //Responder un ticket
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $ticket_estado=$obCon->normalizar($_REQUEST["ticket_estado"]);
            
            $mensaje=str_replace("drop","",$_REQUEST["mensaje"]);
            $mensaje=str_replace("delete","",$mensaje);
            $mensaje_id=$obCon->normalizar($_REQUEST["mensaje_id"]);
            $ticket_id=$obCon->normalizar($_REQUEST["ticket_id"]);
                    
            
            if($mensaje==''){
                exit("E1;Debe escribir un mensaje;mensaje");
            }
            if($mensaje_id==''){
                exit("E1;no se recibió el id del mensaje");
            }
            if($ticket_id==''){
                exit("E1;No se recibió el id del ticket");
            }
            
            $obCon->AgregarMensajeTicket($db,$mensaje_id,$ticket_id, $mensaje, $idUser);
            $obCon->ActualizaRegistro("$db.tickets", "Estado", $ticket_estado, "ticket_id", $ticket_id);
            exit("OK;Respuesta Agregada");
        break; //fin caso 3
        
        
        case 4://Recibir un adjunto para un mensaje
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]); 
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $mensaje_id=$obCon->normalizar($_REQUEST["mensaje_id"]);
            $ticket_id=$obCon->normalizar($_REQUEST["ticket_id"]);
            $archivo_id=$obCon->normalizar($_REQUEST["archivo_id"]);
            $upload_id="adjunto_ticket";
            $Extension="";
            
            if(!empty($_FILES[$upload_id]['name'])){
                
                $info = new SplFileInfo($_FILES[$upload_id]['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES[$upload_id]['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta=$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$empresa_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="Tickets/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$ticket_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$mensaje_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("ad_msg_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES[$upload_id]['tmp_name'],$destino);
                $obCon->AgregarAdjuntoMensaje($db,$archivo_id,$mensaje_id, $destino, $Tamano, $_FILES[$upload_id]['name'], $Extension, $idUser);
            }else{
                exit("E1;No se recibió el archivo");
            }
            print("OK;Archivo adjuntado");
           
        break;//Fin caso 4
        
        case 5://eliminar un adjunto
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $mensaje_id=$obCon->normalizar($_REQUEST["mensaje_id"]);
            $archivo_id=$obCon->normalizar($_REQUEST["archivo_id"]);
            
            $tabla="$db.tickets_adjuntos";
            
            $sql="SELECT Ruta FROM $tabla WHERE archivo_id='$archivo_id' AND mensaje_id='$mensaje_id'";
            $DatosAdjunto=$obCon->FetchAssoc($obCon->Query($sql));
            if(file_exists($DatosAdjunto["Ruta"])){
                unlink($DatosAdjunto["Ruta"]);
            }
            $sql="DELETE FROM $tabla WHERE mensaje_id='$mensaje_id' AND archivo_id='$archivo_id'";
            $obCon->Query($sql);
            print("OK;Archivo Eliminado");
        break;//Fin caso 5
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>