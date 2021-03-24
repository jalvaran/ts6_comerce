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
            
            $CmbEstadoTicketsListado=$obCon->normalizar($_REQUEST["CmbEstadoTicketsListado"]);
            $CmbFiltroUsuario=$obCon->normalizar($_REQUEST["CmbFiltroUsuario"]);
            $Condicional=" WHERE ID>0 ";
            $OrderBy=" ORDER BY FechaActualizacion DESC";            
            if($CmbEstadoTicketsListado<>''){
                $Condicional.=" AND Estado=='$CmbEstadoTicketsListado' ";
                
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
                                            print('<tr style="cursor:pointer" onclick="ver_ticket(`'.$ID.'`)">
                                                        
                                                        <td class="starred-icon ">
                                                            <a class="active text-danger"><i><strong>'.$datos_consulta["ID"].'</strong></i></a>
                                                        </td>
                                                        
                                                        <td>
                                                            <span class="name">'.$datos_consulta["NombreSolicitante"].' '.$datos_consulta["ApellidoSolicitante"].'</span>
                                                            <p class="descr">'.$datos_consulta["Asunto"].'</p>
                                                        </td>
                                                        <td class="starred-icon ">
                                                            <a class="active text-primary"><i><strong>'.$datos_consulta["NombreEstado"].'</strong></i></a>
                                                        </td>
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
            $DatosTickets=$obCon->DevuelveValores("$db.tickets", "ticket_id", $ticket_id);
            
            $sql="SELECT t1.Nombre,t1.Apellido 
                    
                    FROM usuarios t1 WHERE t1.ID='".$DatosTickets["idUsuarioSolicitante"]."'";
            $DatosUsuarioCreador=$obCon->FetchAssoc($obCon->Query($sql));
            $NombreSolicitante=$DatosUsuarioCreador["Nombre"]." ".$DatosUsuarioCreador["Apellido"]; 
            $ExtensionesImagenes=array("png", "bmp", "jpg", "jpeg");
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                print('<a href="#" onclick=ver_ticket('.$ticket_id.')><h3>Ticket No.'.$ticket_id.'</h3></a>');
                //print("<h3 class='box-title'>Ticket No. $idTicket</h3>");
            $css->CerrarDiv();
            $css->CrearDiv("", "mailbox-read-info", "left", 1, 1);
            print('
                <h3>'.$DatosTickets["Asunto"].'</h3>
                <h5>De: '.utf8_encode($NombreSolicitante).'
                  <span class="mailbox-read-time pull-right">'.$DatosTickets["FechaApertura"].'</span></h5>
              </div>');
            $Consulta=$obCon->ConsultarTabla("$db.tickets_mensajes", "WHERE ticket_id='$ticket_id'");
            $i=0;
            while($DatosMensajes=$obCon->FetchAssoc($Consulta)){
                $i=$i+1;
                if($i==1){
                    $css->CrearTitulo("Mensaje No. $i");
                }else{
                    $NoRespuesta=$i-1;
                    $sql="SELECT Nombre,Apellido FROM usuarios WHERE ID='".$DatosMensajes["idUser"]."'";
                    $DatosUsuarioCreador=$obCon->FetchAssoc($obCon->Query($sql));
                    $NombreUsuarioRespuesta=$DatosUsuarioCreador["Nombre"]." ".$DatosUsuarioCreador["Apellido"]; 
                    $css->CrearTitulo("Respuesta No. $NoRespuesta por <strong>$NombreUsuarioRespuesta</strong>, el ".$DatosMensajes["Created"],"verde");
                }
                $idMensaje=$DatosMensajes["ID"];
                $css->CrearDiv("", "mailbox-read-message", "left", 1, 1);
                    echo($DatosMensajes["Mensaje"]);
                $css->CerrarDiv();
                print("<hr>");
                $css->CrearDiv("", "col-md-4", "left", 1, 1);
                    $css->input("file", "upAdjuntosMensajes_$idMensaje", "form-control", "upAdjuntosMensajes_$idMensaje", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
                $css->CerrarDiv();
                
                $css->CrearDiv("", "col-md-2", "left", 1, 1);
                    $css->CrearBotonEvento("BtnAgregarAdjunto_$idMensaje", "Adjuntar", 1, "onclick", "AgregarAdjunto(`$idMensaje`,`$idTicket`)", "verde");
                $css->CerrarDiv();
                print("<br><br>");
                $css->CrearDiv("", "box-footer", "left", 1, 1);
                    $ConsultaAdjuntos=$obCon->ConsultarTabla("tickets_adjuntos", "WHERE idMensaje='$idMensaje'");
                    if($obCon->NumRows($ConsultaAdjuntos)){
                        print('<ul class="mailbox-attachments clearfix">');
                            while($DatosAdjuntos=$obCon->FetchAssoc($ConsultaAdjuntos)){

                                print('<li>');
                                    $ClassIcon="fa fa-file-o";
                                    $Extension=strtolower($DatosAdjuntos["Extension"]);
                                    if(!in_array($Extension,$ExtensionesImagenes)){
                                        if($Extension=='pdf'){
                                            $ClassIcon="fa fa-file-pdf-o";
                                        }
                                        if($Extension=='xls' or $Extension=='xlsx'){
                                            $ClassIcon="fa fa-file-excel-o";
                                        }
                                        if($Extension=='doc' or $Extension=='docx'){
                                            $ClassIcon="fa fa-file-word-o";
                                        }
                                        if($Extension=='zip' or $Extension=='rar'){
                                            $ClassIcon="fa fa-file-zip-o";
                                        }
                                        print('<span class="mailbox-attachment-icon"><i class="'.$ClassIcon.'"></i></span>');
                                    }else{
                                        print('<span class="mailbox-attachment-icon has-img"><img src="'.substr($DatosAdjuntos["Ruta"], 3).'" alt="NO"></span>');
                                                
                                    }
                                    $css->CrearDiv("", "mailbox-attachment-info", "center", 1, 1);
                                        print('<a href="'.substr($DatosAdjuntos["Ruta"], 3).'" target="blank" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> '.$DatosAdjuntos["NombreArchivo"].'</a>');
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
                                        print('<span class="mailbox-attachment-size">'.$Tamano.'</span>');
                                    $css->CerrarDiv();
                                print('</li>');
                            }
                        print("</ul>");
                    }
                
                $css->CerrarDiv();
            }
                        
            $css->CrearDiv("", "col-md-2", "left", 1, 1);
            
            $css->CrearBotonEvento("BtnResponderTicket", "Responder", 1, "onclick", "FormularioResponderTicket($idTicket)", "azul");
            $css->CerrarDiv();
        break;//Fin caso 3
        
        case 4: //Formulario Nueva Respuesta
            $idTicket=$obCon->normalizar($_REQUEST["idTicket"]);
            $DatosTickets=$obCon->DevuelveValores("tickets", "ID", $idTicket);
            $css->CrearDiv("", "box-header with-border", "", 1, 1);
                $css->CrearDiv("", "col-md-6", "left", 1, 1);
                    print("<h3 class='box-title'><strong>Responder el Ticket $idTicket</strong></h3>");
                    print("<h6 >".$DatosTickets["Asunto"]."</h6>");
                $css->CerrarDiv();
                $css->CrearDiv("", "col-md-2", "left", 1, 1);
                    $css->select("CmbCerrarTicket", "form-control", "CmbCerrarTicket", "Estado:", "", "", "");
                        $sql="SELECT * FROM tickets_estados ORDER BY ID";
                        $Consulta=$obCon->Query($sql);
                        while($DatosEstados=$obCon->FetchAssoc($Consulta)){
                            if($DatosTickets["Estado"]==$DatosEstados["ID"]){
                                $Seleccionar=1;
                            }else{
                                $Seleccionar=0;
                            }
                            //$css->option($id, $class, $title, $value, $vectorhtml, $Script, $Seleccionar, $ng_app);
                        
                            $css->option("", "", "", $DatosEstados["ID"], "", "",$Seleccionar);
                                print($DatosEstados["Estado"]);
                            $css->Coption();
                        }
                        
                        
                    $css->Cselect();
                $css->CerrarDiv();
            $css->CerrarDiv();
            print("<br>");
            
            $css->CrearDiv("", "form-group", "left", 1, 1);
                $css->textarea("TxtMensaje", "form-control", "TxtMensaje", "", "Mensaje", "", "", "style='height:400px;'");
                       
            $css->Ctextarea();
            $css->CerrarDiv();    
            //print("<br>");
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 1: </strong>");
            $css->input("file", "upAdjuntosTickets1", "form-control", "upAdjuntosTickets1", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 2: </strong>");
            $css->input("file", "upAdjuntosTickets2", "form-control", "upAdjuntosTickets2", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Adjunto 3: </strong>");
            $css->input("file", "upAdjuntosTickets3", "form-control", "upAdjuntosTickets3", "Adjuntar:", "Adjuntar", "adjuntar", "", "", "");
            
            $css->CerrarDiv();
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
            print("<strong>Guardar esta Respuesta: </strong>");
            $css->CrearBotonEvento("BtnGuardarTicket", "Guardar Respuesta", 1, "onclick", "GuardarRespuesta($idTicket)", "azul");
            $css->CerrarDiv();
        break;//Fin caso 4
        
        case 5://dibuja el menu lateral de los tickets
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $css->div("", "", "", "", "", "", "");
                print('<a onclick="FormularioNuevoTicket();" class="btn btn-red btn-block">Redactar</a>');
            $css->Cdiv();
            print('<ul class="mailbox-menu">
                        <li><a onclick="VerListadoTickets();" class="active"><i class="icon-envelope-letter"></i> <span>Mi bandeja</span></a></li>
                    </ul>');
            $css->select("CmbEstadoTicketsListado", "form-control", "CmbEstadoTicketsListado", "", "", "", "");
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
            
            $css->select("departamentos_tickets", "form-control", "departamentos_tickets", "", "", "", "");
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
                    
            $css->select("CmbTiposTicketsListado", "form-control", "CmbTiposTicketsListado", "", "", "", "");
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
            
            $css->select("CmbFiltroUsuario", "form-control", "CmbFiltroUsuario", "", "", "", "");
                
                $css->option("", "", "", "1", "", "");
                    print("Todos los usuarios");
                $css->Coption();
                $css->option("", "", "", "2", "", "");
                    print("Solo mios");
                $css->Coption();
                
            $css->Cselect();
            
            
            
        break;//Fin caso 5    
      
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>