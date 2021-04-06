<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja el listado general de tickets
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $TipoUser=$_SESSION["tipouser"];
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            $departamentos_tickets=$obCon->normalizar($_REQUEST["departamentos_tickets"]);
            $CmbEstadoTicketsListado=$obCon->normalizar($_REQUEST["CmbEstadoTicketsListado"]);
            $CmbFiltroUsuario=$obCon->normalizar($_REQUEST["CmbFiltroUsuario"]);
            $Condicional=" WHERE ID>0 ";
            $OrderBy=" ORDER BY FechaActualizacion DESC";            
            if($CmbEstadoTicketsListado<>''){
                $Condicional.=" AND Estado='$CmbEstadoTicketsListado' ";
                
            }
            
            if($departamentos_tickets<>''){
                $Condicional.=" AND (departamento_id='$departamentos_tickets') ";
            }
            if($CmbFiltroUsuario==2){
                $Condicional.=" AND (idUsuarioSolicitante='$idUser' or idUsuarioAsignado='$idUser') ";
            }
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional.=" AND ( ID='$Busqueda' or Asunto like '%$Busqueda%' )";
                    //$Condicional.=" AND ( ID='$Busqueda' or MATCH(Asunto) AGAINST ('%$Busqueda%') )";
                    
                }
                
            }
            
            if(isset($_REQUEST['departamentos_tickets'])){
                $departamento_id=$obCon->normalizar($_REQUEST['departamentos_tickets']);
                if($departamento_id<>''){
                    $Condicional.=" AND departamento_id='$departamento_id' ";
                    
                    
                }
                
            }
            
            if(isset($_REQUEST['CmbTiposTicketsListado'])){
                $TipoTicket=$obCon->normalizar($_REQUEST['CmbTiposTicketsListado']);
                if($TipoTicket<>''){
                    $Condicional.=" AND TipoTicket='$TipoTicket' ";
                    
                    
                }
                
            }
            
            if($TipoUser=="ips"){
                $Condicional.=" AND idUsuarioSolicitante='$idUser' ";
            }
            
            
            $statement=" $db.`vista_tickets` $Condicional ";
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 15;
            $startpoint = ($NumPage * $limit) - $limit;
            
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num` FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $OrderBy $Limit ");
            $TotalPaginas= ceil($ResultadosTotales/$limit);
            
            print('<div class="panel panel-default">
                                <div class="mailbox-container">
                                    <div class="action">
                                        <div class="btn-group mr-2">
                                            <strong>'.$ResultadosTotales.' Tickets</strong>
                                        </div>
                                        
                                        <div class="btn-group pull-right" style="top: -10px;">');
                                            
                                        
                            if($TotalPaginas==0){
                                $TotalPaginas=1;
                            }
                            if($NumPage>1){
                                $goPage=$NumPage-1;  
                                print('<button onclick="VerListadoTickets('.$goPage.')" type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient"><i style="font-size:20px;height:15px;" class="far fa-arrow-alt-circle-left text-flickr"></i></button>');
                            }        
                            if($NumPage<>$TotalPaginas){  
                                $goPage=$NumPage+1;
                                print('<button onclick="VerListadoTickets('.$goPage.')" type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient"><i style="font-size:20px;height:15px;" class="far fa-arrow-alt-circle-right text-success"></i></button>');
                                        
                            }                        
                                                    
                              print('   </div>
                                        
                                    </div>
                                    <div class="body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mail-table">
                                                <tbody>');
                              
                                        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                            $ID=$datos_consulta["ticket_id"];
                                            $notificacion="";
                                            $visualizacion="";
                                            if($datos_consulta["idUsuarioSolicitante"]==$idUser){
                                                $visualizacion="R";
                                            }
                                            if($datos_consulta["idUsuarioAsignado"]==$idUser){
                                                $visualizacion="D";
                                            }
                                            if($datos_consulta["idUsuarioSolicitante"]==$idUser and $datos_consulta["idUsuarioAsignado"]==$idUser){
                                                $visualizacion="RD";
                                            }
                                            if($visualizacion=="R"){
                                                if($datos_consulta["leido_remitente"]==0 and $datos_consulta["leido_destinatario"]==1){
                                                    $notificacion='<span class="badge badge-success badge-sm badge-pill">Respuesta</span>';
                                                }
                                                if($datos_consulta["leido_remitente"]==1 and $datos_consulta["leido_destinatario"]==1){
                                                    $notificacion='<span class="badge badge-primary badge-sm badge-pill">leído</span>';
                                                }
                                            }
                                            if($visualizacion=="D"){
                                                if($datos_consulta["leido_remitente"]==2 and $datos_consulta["leido_destinatario"]==0){
                                                    $notificacion='<span class="badge badge-danger badge-sm badge-pill">Nuevo</span>';
                                                }
                                                if($datos_consulta["leido_remitente"]==1 and $datos_consulta["leido_destinatario"]==1){
                                                    $notificacion='<span class="badge badge-primary badge-sm badge-pill">leído</span>';
                                                }
                                                if($datos_consulta["leido_remitente"]==1 and $datos_consulta["leido_destinatario"]==0){
                                                    $notificacion='<span class="badge badge-success badge-sm badge-pill">Respuesta</span>';
                                                }
                                                
                                            }
                                            
                                            print('<tr style="cursor:pointer" onclick="ver_ticket(`'.$ID.'`)">
                                                        
                                                        <td class="starred-icon ">
                                                            <a class="active text-danger"><i><strong>'.$datos_consulta["ID"].'</strong></i></a>
                                                        </td>
                                                        
                                                        <td>
                                                            <span class="name">'.$datos_consulta["NombreSolicitante"].' '.$datos_consulta["ApellidoSolicitante"].'</span>
                                                            <p class="descr">'.$datos_consulta["Asunto"].'</p>
                                                        </td>
                                                        
                                                        <td class="date">'.$notificacion.'</td>
                                                        

                                                        <td class="date text-primary">'.$datos_consulta["NombreEstado"].'</td>
                                                        <td class="date text-primary">'.$datos_consulta["NombreDepartamento"].'</td>
                                                        <td class="date">'.$datos_consulta["FechaApertura"].'</td>
                                                        <td class="date">'.$datos_consulta["FechaActualizacion"].'</td>
                                                    </tr>
                                                    ');
                                            
                                        }    
                              
                                print('         </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>');
            
                        
        break; //Fin caso 1
        
        case 2: //Formulario Nuevo Ticket
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $mensaje_id=$obCon->getUniqId("msg_");
            $ticket_id=$obCon->getUniqId("tk_");
            $TipoUser=$_SESSION["tipouser"];
            
            $css->CrearDiv("", "panel panel-default", "", 1, 1);
                $css->CrearDiv("", "panel-head bg-light", "", 1, 1);
                    $css->CrearDiv("", "panel-title", "", 1, 1);
                        print('<span class="panel-title-text font-24">Nuevo Ticket</span>');
                    $css->Cdiv();
                    
                $css->Cdiv();
                
                $css->CrearDiv("", "mailbox-container", "", 1, 1);
                    $css->CrearDiv("", "compose", "", 1, 1);
                        $css->div("", "row", "", "", "", "", "");
                            $css->div("", "col-lg-6", "", "", "", "", "");
                                $css->div("", "form-group row", "", "", "", "", "");
                                    print('<label class="col-12 col-form-label">Tipo de Ticket <i class="fa fa-list-alt text-flickr" ></i></label>');
                                    $css->div("", "col-12", "", "", "", "", "");
                                    
                                        $css->select("tipo_ticket", "form-control", "tipo_ticket", "", "", "", "");
                                            $sql="SELECT * FROM $db.tickets_tipo";
                                            $Consulta=$obCon->Query($sql);
                                            $css->option("", "", "", "", "", "");
                                                print("Seleccione:");
                                            $css->Coption();

                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                $css->option("", "", "", $datos_consulta["ID"], "", "");
                                                    print($datos_consulta["TipoTicket"]);
                                                $css->Coption();
                                            }

                                        $css->Cselect();
                                    $css->Cdiv(); 
                                $css->Cdiv();
                            $css->Cdiv(); 
                            $css->div("", "col-lg-6", "", "", "", "", "");
                                $css->div("", "form-group row", "", "", "", "", "");
                                    print('<label class="col-12 col-form-label">Departamento <i class="fa fa-crop text-flickr" ></i></label>');
                                    $css->div("", "col-12", "", "", "", "", "");
                                           
                                        $css->select("departamento_id", "form-control", "departamento_id", "", "", "", "");
                                            $sql="SELECT * FROM $db.tickets_departamentos WHERE Estado=1";
                                            $Consulta=$obCon->Query($sql);
                                            $css->option("", "", "", "", "", "");
                                                print("Seleccione:");
                                            $css->Coption();

                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                $css->option("", "", "", $datos_consulta["ID"], "", "");
                                                    print($datos_consulta["Departamento"]);
                                                $css->Coption();
                                            }

                                        $css->Cselect();  
                                    $css->Cdiv(); 
                                $css->Cdiv();
                            $css->Cdiv();  
                            $css->CrearDiv("", "col-md-12", "", 1, 1);
                            $css->CrearDiv("", "row", "", 1, 1);
                                                                
                                $css->CrearDiv("", "col-md-12", "left", 1, 1);
                                    $css->CrearDiv("", "form-group", "", 1, 1);
                                        print('<label for="asunto">Asunto</label>
                                                <input type="email" class="form-control" id="asunto" placeholder="Asunto">');
                                    $css->Cdiv();
                                $css->Cdiv();
                                $css->CrearDiv("", "col-md-12", "left", 1, 1);
                                    $css->CrearDiv("", "form-group", "", 1, 1);
                                        print('<textarea id="mensaje" class="summernote-ts"></textarea>');
                                    $css->Cdiv();
                                $css->Cdiv();
                                $css->CrearDiv("", "col-md-12", "left", 1, 1);
                                print('<div class="panel">
                            
                                        <div class="panel-body">
                                            <form data-mensaje_id="'.$mensaje_id.'" data-ticket_id="'.$ticket_id.'" action="/" class="dropzone dz-clickable" id="tickets_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                                        </div>
                                    </div>');
                                
                                    
                                $css->Cdiv();    
                               
                            $css->Cdiv(); 
                        $css->Cdiv();
                    $css->Cdiv();                              
                    
                $css->Cdiv();
                
                $css->CrearDiv("", "panel-footer text-right", "", 1, 1);
                    print('<button id="btn_guardar" data-action="1" data-mensaje_id="'.$mensaje_id.'" data-ticket_id="'.$ticket_id.'" class="btn btn-primary m-1" onclick="crear_ticket_mensaje()">Enviar</button>');
                $css->Cdiv();
            $css->Cdiv();
            
        break;//Fin caso 2
        
        case 3: //Ver  los mensajes de un ticket
            $ticket_id=$obCon->normalizar($_REQUEST["ticket_id"]);
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $DatosTickets=$obCon->DevuelveValores("$db.vista_tickets", "ticket_id", $ticket_id);
            
            print('<div class="panel panel-default">
                    <div class="mailbox-container">

                        <div class="mail-details">
                            <h4 class="title">Ticket: <strong class="text-flickr">'.$DatosTickets["ID"].'</strong>, '.$DatosTickets["Asunto"].'</h4>
                            <div class="mail-body">
                                <div class="header">

                                    <div class="tbl-cell details">
                                        <div class="from"> de: '.$DatosTickets["NombreSolicitante"].' '.$DatosTickets["ApellidoSolicitante"].'</div>
                                        <div class="to"> para: '.$DatosTickets["NombreAsignado"].' '.$DatosTickets["ApellidoAsignado"].'</div>


                                    </div>
                                    <div class="btn-group pull-right">
                                        '.$DatosTickets["FechaApertura"].'
                                    </div>

                                </div>

                            ');
            
            $ExtensionesImagenes=array("png", "bmp", "jpg", "jpeg");
                     
            
            $Consulta=$obCon->ConsultarTabla("$db.tickets_mensajes", "WHERE ticket_id='$ticket_id'");
            $i=0;
            while($DatosMensajes=$obCon->FetchAssoc($Consulta)){
                                
                $i=$i+1;
                if($i==1){
                    $titulo="Mensaje No. $i";
                }else{
                    $NoRespuesta=$i-1;
                    $sql="SELECT Nombre,Apellido FROM usuarios WHERE ID='".$DatosMensajes["idUser"]."'";
                    $DatosUsuarioCreador=$obCon->FetchAssoc($obCon->Query($sql));
                    $NombreUsuarioRespuesta=$DatosUsuarioCreador["Nombre"]." ".$DatosUsuarioCreador["Apellido"]; 
                    $titulo="Respuesta No. $NoRespuesta por <strong>$NombreUsuarioRespuesta</strong>, el ".$DatosMensajes["Created"];
                }
                $mensaje_id=$DatosMensajes["mensaje_id"];
                print('
                        <div class="content">
                            '.$titulo.'<br>
                            '.$DatosMensajes["Mensaje"].'
                        </div>
                        ');
               
                
               
                    $ConsultaAdjuntos=$obCon->ConsultarTabla("$db.tickets_adjuntos", "WHERE mensaje_id='$mensaje_id'");
                    if($obCon->NumRows($ConsultaAdjuntos)){
                        
                        print('<div class="attachments"><div class="row">');
                            while($DatosAdjuntos=$obCon->FetchAssoc($ConsultaAdjuntos)){
      
                                $ClassIcon="fa fa-file-o";
                                $Extension=strtolower($DatosAdjuntos["Extension"]);
                                if(!in_array($Extension,$ExtensionesImagenes)){
                                    $ClassIcon="fa fa-file text-dark";
                                    if($Extension=='pdf'){
                                        $ClassIcon="fa fa-file-pdf text-flickr";
                                    }
                                    if($Extension=='xls' or $Extension=='xlsx'){
                                        $ClassIcon="fa fa-file-excel text-success";
                                    }
                                    if($Extension=='doc' or $Extension=='docx'){
                                        $ClassIcon="fa fa-file-word text-primary";
                                    }
                                    if($Extension=='zip' or $Extension=='rar'){
                                        $ClassIcon="far fa-file-archive text-warning";
                                    }
                                    $previus='<li class="'.$ClassIcon.'"></li>';
                                    
                                }else{
                                    $previus='<img class="img-thumbnail" src="'.substr($DatosAdjuntos["Ruta"], 3).'" alt="">';
                                    

                                }
                                
                                    $Tamano=$DatosAdjuntos["Tamano"]." Bytes";
                                    if($DatosAdjuntos["Tamano"]>=1000 and $DatosAdjuntos["Tamano"]<1000000){
                                        $Tamano= number_format($DatosAdjuntos["Tamano"]/1000,2)." KB";
                                    }
                                    if($DatosAdjuntos["Tamano"]>=1000000 and $DatosAdjuntos["Tamano"]<1000000000){
                                        $Tamano= number_format($DatosAdjuntos["Tamano"]/1000000,2)." MB";
                                    }
                                    if($DatosAdjuntos["Tamano"]>=1000000000 ){
                                        $Tamano= number_format($DatosAdjuntos["Tamano"]/1000000000,2)." GB";
                                    }
                                    print('<div class="col-md-3" style="text-align:center;">');
                                        print('<div class="panel panel-default">');
                                            print('<div style="height:100px;font-size:50px;">');
                                                print($previus);
                                            print('</div>');   
                                            print('<div class="panel panel-body">');
                                                print('<a href="'.substr($DatosAdjuntos["Ruta"], 3).'" target="blank"><h5>'.$DatosAdjuntos["NombreArchivo"].'<br>'.$Tamano.'</h5></a>');
                                            print('</div>');    
                                        print('</div>');
                                    print('</div>');
                                   
                                   // print('<a href="'.substr($DatosAdjuntos["Ruta"], 3).'" target="blank">'.$DatosAdjuntos["NombreArchivo"].'</a>');
                                    
                                    
                            }
                        print("</div></div>");
                    }
                
                print('<div class="form-seperator"></div>');
            }
                
            $css->Cdiv();
                $css->Cdiv();
                $css->Cdiv();
            $css->CrearDiv("", "panel-footer text-right", "", 1, 1);
                    print('<button id="btn_guardar" data-action="1" class="btn btn-primary m-1" onclick="frm_responder_ticket(`'.$ticket_id.'`)">Responder</button>');
                $css->Cdiv();

           $css->Cdiv();
           
           if($DatosTickets["idUsuarioAsignado"]==$idUser){
               $obCon->ActualizaRegistro("$db.tickets", "leido_destinatario", 1, "ticket_id", $ticket_id);
               if($DatosTickets["leido_remitente"]==2){//Si es un mensaje nuevo y lo ve el destino se debe poner en 1 1 para que aparezca leido por los dos
                   $obCon->ActualizaRegistro("$db.tickets", "leido_remitente", 1, "ticket_id", $ticket_id);
               }
           }
           if($DatosTickets["idUsuarioSolicitante"]==$idUser){
               $obCon->ActualizaRegistro("$db.tickets", "leido_remitente", 1, "ticket_id", $ticket_id);
               
           }
           
        break;//Fin caso 3
        
        case 4: //respuesta ticket
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $ticket_id=$obCon->normalizar($_REQUEST["ticket_id"]);
            $mensaje_id=$obCon->getUniqId("msg_");
            $datos_ticket=$obCon->DevuelveValores("$db.tickets", "ticket_id", $ticket_id);
            $TipoUser=$_SESSION["tipouser"];
            
            $css->CrearDiv("", "panel panel-default", "", 1, 1);
                $css->CrearDiv("", "panel-head bg-light", "", 1, 1);
                    $css->CrearDiv("", "panel-title", "", 1, 1);
                        print('<span class="panel-title-text font-24">Respuesta al Ticket: <strong>'.$datos_ticket["ID"].'</strong> '.$datos_ticket["Asunto"].'</span>');
                    $css->Cdiv();
                    
                $css->Cdiv();
                
                $css->CrearDiv("", "mailbox-container", "", 1, 1);
                    $css->CrearDiv("", "compose", "", 1, 1);
                        $css->div("", "row", "", "", "", "", "");
                            $css->div("", "col-lg-6", "", "", "", "", "");
                                $css->div("", "form-group row", "", "", "", "", "");
                                    print('<label class="col-12 col-form-label">Cambiar al estado: <i class="fa fa-outdent text-flickr" ></i></label>');
                                    $css->div("", "col-12", "", "", "", "", "");
                                    
                                        $css->select("ticket_estado", "form-control", "ticket_estado", "", "", "", "");
                                            $sql="SELECT * FROM $db.tickets_estados";
                                            $Consulta=$obCon->Query($sql);
                                            

                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                $sel=0;
                                                if($datos_ticket["Estado"]==$datos_consulta["ID"]){
                                                    $sel=1;
                                                }
                                                $css->option("", "", "", $datos_consulta["ID"], "", '',$sel);
                                                    print($datos_consulta["Estado"]);
                                                $css->Coption();
                                            }

                                        $css->Cselect();
                                    $css->Cdiv(); 
                                $css->Cdiv();
                            $css->Cdiv(); 
                            
                            $css->CrearDiv("", "col-md-12", "", 1, 1);
                            $css->CrearDiv("", "row", "", 1, 1);                                
                                $css->CrearDiv("", "col-md-12", "left", 1, 1);
                                    $css->CrearDiv("", "form-group", "", 1, 1);
                                        print('<textarea id="mensaje" class="summernote-ts"></textarea>');
                                    $css->Cdiv();
                                $css->Cdiv();
                                $css->CrearDiv("", "col-md-12", "left", 1, 1);
                                print('<div class="panel">
                            
                                        <div class="panel-body">
                                            <form data-mensaje_id="'.$mensaje_id.'" data-ticket_id="'.$ticket_id.'" action="/" class="dropzone dz-clickable" id="tickets_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                                        </div>
                                    </div>');
                                
                                    
                                $css->Cdiv();    
                               
                            $css->Cdiv(); 
                        $css->Cdiv();
                    $css->Cdiv();                              
                    
                $css->Cdiv();
                
                $css->CrearDiv("", "panel-footer text-right", "", 1, 1);
                    print('<button id="btn_guardar" data-action="1" data-mensaje_id="'.$mensaje_id.'" data-ticket_id="'.$ticket_id.'" class="btn btn-primary m-1" onclick="responder_ticket()">Enviar</button>');
                $css->Cdiv();
            $css->Cdiv();
            
        break;//Fin caso 4
        
        case 5://dibuja el menu lateral de los tickets
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $sql="SELECT Role FROM usuarios WHERE ID='$idUser'";
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $Role=$datos_consulta['Role'];
            $css->div("", "", "", "", "", "", "");
                print('<a onclick="FormularioNuevoTicket();" class="btn btn-red btn-block">Redactar</a>');
            $css->Cdiv();
            print('<ul class="mailbox-menu">
                        <li><a onclick="VerListadoTickets();" class="active"><i class="icon-envelope-letter"></i> <span>Mi bandeja</span></a></li>
                    </ul>');
            $css->select("CmbEstadoTicketsListado", "form-control", "CmbEstadoTicketsListado", "", "", "", "onchange=VerListadoTickets()");
                $sql="SELECT * FROM $db.tickets_estados ORDER BY ID ASC";
                $Consulta=$obCon->Query($sql);
                $css->option("", "", "", "", "", "");
                    print("Todos los estados");
                $css->Coption();
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $datos_consulta["ID"], "", "");
                        print($datos_consulta["Estado"]);
                    $css->Coption();
                }
            $css->Cselect();
            
            $css->select("departamentos_tickets", "form-control", "departamentos_tickets", "", "", "", "onchange=VerListadoTickets()");
                $sql="SELECT * FROM $db.tickets_departamentos WHERE Estado=1";
                $Consulta=$obCon->Query($sql);
                $css->option("", "", "", "", "", "");
                    print("Todos los Departamentos");
                $css->Coption();
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $datos_consulta["ID"], "", "");
                        print($datos_consulta["Departamento"]);
                    $css->Coption();
                }
            $css->Cselect();
                    
            $css->select("CmbTiposTicketsListado", "form-control", "CmbTiposTicketsListado", "", "", "", "onchange=VerListadoTickets()");
                $sql="SELECT * FROM $db.tickets_tipo";
                $Consulta=$obCon->Query($sql);
                $css->option("", "", "", "", "", "");
                    print("Todos los tipos");
                $css->Coption();
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $datos_consulta["ID"], "", "");
                        print($datos_consulta["TipoTicket"]);
                    $css->Coption();
                }
            $css->Cselect();
            
            $css->select("CmbFiltroUsuario", "form-control", "CmbFiltroUsuario", "", "", "", "onchange=VerListadoTickets()");
                if($Role=='SUPERVISOR' or $Role=='ADMINISTRADOR'){
                    $css->option("", "", "", "1", "", "");
                        print("Todos los usuarios");
                    $css->Coption();
                }
                
                
                $css->option("", "", "", "2", "", "");
                    print("Solo mios");
                $css->Coption();
                
            $css->Cselect();
            
        break;//Fin caso 5    
      
        case 6://dibuja el formulario para crear o editar un departamento
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $item_id=$obCon->normalizar($_REQUEST["item_edit"]);
            $datos_departamento=$obCon->DevuelveValores("$db.tickets_departamentos", "ID", $item_id);
            print('<div class="col-12">
                            <div class="panel">
                                <div class="panel-head">
                                    <h5 class="panel-title">Crear o editar un Departamento</h5>
                                </div>
                                
                                    <div class="panel-body">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nombre_departamento">Nombre del Departamento</label>
                                                <input type="text" class="form-control" id="nombre_departamento" placeholder="Nombre" value="'.$datos_departamento["Departamento"].'">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="correo_notificacion_general">Correo de notificación general</label>
                                                <input type="text" class="form-control" id="correo_notificacion_general" placeholder="Correo de Notificación" value="'.$datos_departamento["correo_notificacion_general"].'">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="cmb_usuario_asignado">Asignar a:</label>');
            
                                    $css->select("cmb_usuario_asignado", "form-control", "cmb_usuario_asignado", "", "", "", "");
                                        $css->option("", "", "", "", "", "");
                                            print("Seleccione un usuario");
                                        $css->Coption();
                                        
                                        $sql="SELECT t1.usuario_id_relacion,(SELECT nombre_completo FROM usuarios t2 WHERE t2.ID=t1.usuario_id_relacion LIMIT 1 ) as nombre_usuario FROM usuarios_rel_empresas t1 WHERE empresa_id='$empresa_id'";
                                        $Consulta=$obCon->Query($sql);
                                        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                            $sel=0;
                                            if($datos_departamento["usuario_asignado"]==$datos_consulta["usuario_id_relacion"]){
                                                $sel=1;
                                            }
                                            $css->option("", "", "", $datos_consulta["usuario_id_relacion"], "", "",$sel);
                                                print($datos_consulta["nombre_usuario"]);
                                            $css->Coption();
                                        }
                                    $css->Cselect();
                                                
                                    print('</div>
                                            <div class="col-md-6 mb-3">
                                                <label for="cmb_estado_departamento">Estado:</label>');
                                    
                                    $css->select("cmb_estado_departamento", "form-control", "cmb_estado_departamento", "", "", "", "");
                                        
                                        $sel=0;
                                        if($datos_departamento["Estado"]==0){
                                            $sel=1;
                                        }
                                        $css->option("", "", "", "0", "", "",$sel);
                                            print("Inactivo");
                                        $css->Coption();
                                        
                                        $sel=0;
                                        if($datos_departamento["Estado"]==1 or $item_id==''){
                                            $sel=1;
                                        }
                                        $css->option("", "", "", "1", "", "",$sel);
                                            print("Activo");
                                        $css->Coption();
                                        
                                    $css->Cselect();            
                                                
                                    print(' </div>
                                            
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button id="btn_guardar_departamento" class="btn btn-success mr-3" onclick=crear_editar_departamento_ticket(`'.$item_id.'`)>Guardar</button>
                                        <button class="btn btn-default" onclick=listado_tickets_departamento()>Cancelar</button>
                                    </div>
                                
                            </div>
                        </div>');
            
        break;//Fin caso 6
    
        case 7://dibuja el listado de departamentos
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $sql="SELECT t1.*,(SELECT nombre_completo FROM usuarios t2 WHERE t2.ID=t1.usuario_asignado) as nombre_usuario_asignado FROM $db.tickets_departamentos t1 order by t1.ID DESC";
            $Consulta=$obCon->Query($sql);
            
            $css->div("", "row", "", "", "", "", "");
                $css->div("", "col-md-12", "", "", "", "", "");
                    $css->div("", "panel", "", "", "", "", "");
                        $css->div("", "panel-head", "", "", "", "", "");
                            print("Listado de departamentos");
                            $css->div("", "pull-right", "", "", "", "", "");
                                print('<button class="btn btn-primary btn-pill m-1" onclick=frm_tickets_departamento()>Crear <li class="fa fa-plus-circle" ></li></button>');
                            $css->Cdiv();
                        $css->Cdiv();
                        $css->div("", "panel-body", "", "", "", "", "");
                            $css->CrearTabla();
                                $css->FilaTabla(16);
                                    $css->ColTabla("<strong>Editar</strong>", 1);
                                    $css->ColTabla("<strong>ID</strong>", 1);
                                    $css->ColTabla("<strong>Departamento</strong>", 1);                                    
                                    $css->ColTabla("<strong>Usuario Asignado</strong>", 1);
                                    $css->ColTabla("<strong>Correo de Notificación</strong>", 1);
                                    $css->ColTabla("<strong>Estado</strong>", 1);
                                $css->CierraFilaTabla();
                                
                                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                    $item_id=$datos_consulta["ID"];
                                    $css->FilaTabla(16);
                                    
                                        print('<td>');
                                            print('<li class="far fa-edit text-warning" style="font-size:20px;cursor:pointer;" onclick=frm_tickets_departamento(`'.$item_id.'`)></li>');
                                        print('</td>');
                                        $css->ColTabla($datos_consulta["ID"], 1);
                                        $css->ColTabla($datos_consulta["Departamento"], 1);                                    
                                        $css->ColTabla($datos_consulta["nombre_usuario_asignado"], 1);
                                        $css->ColTabla($datos_consulta["correo_notificacion_general"], 1);
                                        $css->ColTabla($datos_consulta["Estado"], 1);
                                    $css->CierraFilaTabla();
                                }
                                
                            $css->CerrarTabla();
                        $css->Cdiv();
                    $css->Cdiv();
                $css->Cdiv();
            $css->Cdiv();
            
        break;//Fin caso 7
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>