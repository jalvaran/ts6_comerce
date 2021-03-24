<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

if(file_exists("../../../general/clases/mail.class.php")){
    include_once("../../../general/clases/mail.class.php");
}

/* 
 * Clase donde se realizaran procesos para construir recetas
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2018-09-26
 */
        
class Ticket extends conexion{
    
    public function CrearTicket($db,$ticket_id,$tipo_ticket,$departamento_id,$asunto,$idUser,$idUsuarioDestino) {
        $Datos["departamento_id"]=$departamento_id;
        $Datos["TipoTicket"]=$tipo_ticket;
        $Datos["ticket_id"]=$ticket_id;
        $Datos["FechaApertura"]=date("Y-m-d H:i:s");
        $Datos["FechaActualizacion"]=date("Y-m-d H:i:s");
        $Datos["Asunto"]=$asunto;
        $Datos["Estado"]=1;
        $Datos["idUsuarioSolicitante"]=$idUser;
        $Datos["idUsuarioActualiza"]=$idUser;
        $Datos["idUsuarioAsignado"]=$idUsuarioDestino;
        
        $sql= $this->getSQLInsert("$db.tickets", $Datos);
        $this->Query($sql);
        
    }
    
    public function AgregarMensajeTicket($db,$mensaje_id,$ticket_id, $mensaje, $idUser) {
        $FechaHora=date("Y-m-d H:i:s");
        $Datos["ticket_id"]=$ticket_id;
        $Datos["mensaje_id"]=$mensaje_id;
        $Datos["mensaje"]=$mensaje;
        $Datos["Estado"]=1;
        $Datos["Created"]=$FechaHora;
        $Datos["idUser"]=$idUser;
                
        $sql= $this->getSQLInsert("$db.tickets_mensajes", $Datos);
        $this->Query($sql);
        
        $sql="UPDATE $db.tickets SET FechaActualizacion='$FechaHora',idUsuarioActualiza='$idUser' WHERE ticket_id='$ticket_id' ";
        $this->Query($sql);
        
    }
    
    public function AgregarAdjuntoMensaje($db,$archivo_id,$mensaje_id,$Ruta,$Tamano,$NombreArchivo,$Extension,$idUser) {
        $Datos["Ruta"]=$Ruta;
        $Datos["archivo_id"]=$archivo_id;
        $Datos["NombreArchivo"]=$NombreArchivo;
        $Datos["Extension"]=$Extension;
        $Datos["Created"]=date("Y-m-d H:i:s");
        $Datos["idUser"]=$idUser;
        $Datos["mensaje_id"]=$mensaje_id;
        $Datos["Tamano"]=$Tamano;
        $sql= $this->getSQLInsert("$db.tickets_adjuntos", $Datos);
        $this->Query($sql);
        
    }
    
    public function NotificarTicketXMail($idTicket,$idMensaje,$idUser) {
        $obMail=new TS_Mail($idUser);
        $DatosTickets=$this->DevuelveValores("tickets", "ID", $idTicket);
        $DatosMensaje=$this->DevuelveValores("tickets_mensajes", "ID", $idMensaje);
        $idUsuarioRemitente=$DatosTickets["idUsuarioSolicitante"];
        $idUsuarioDestino=$DatosTickets["idUsuarioAsignado"];
        $sql="SELECT Nombre,Apellido,Email FROM usuarios WHERE idUsuarios = '$idUsuarioRemitente'";
        $DatosUsuarioRemitente=$this->FetchAssoc($this->Query($sql));
        $sql="SELECT Nombre,Apellido,Email FROM usuarios WHERE idUsuarios = '$idUsuarioDestino'";
        $DatosUsuarioDestino=$this->FetchAssoc($this->Query($sql));
        $Para=$DatosUsuarioRemitente["Email"].",".$DatosUsuarioDestino["Email"];
        $NombreRemitente=$DatosUsuarioRemitente["Nombre"]." ".$DatosUsuarioRemitente["Apellido"];  
        $Parametros=$this->DevuelveValores("configuracion_general", "ID", 25); //Determina el metrodo de envio del mail
        
        if($Parametros["Valor"]==1){
                
            $EstadoEnvio=$obMail->EnviarMailXPHPNativo($Para, $DatosUsuarioRemitente["Email"], $NombreRemitente, "Ticket $idTicket: ".$DatosTickets["Asunto"], $DatosMensaje["Mensaje"]);
        }else{
            $EstadoEnvio=$obMail->EnviarMailXPHPMailer($Para, $DatosUsuarioRemitente["Email"], $NombreRemitente, "Ticket $idTicket: ".$DatosTickets["Asunto"], $DatosMensaje["Mensaje"]);
        }
        return($EstadoEnvio);
    }
    //Fin Clases
}
