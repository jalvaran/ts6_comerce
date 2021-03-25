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
                                            print('<tr style="cursor:pointer" onclick="ver_ticket(`'.$ID.'`)">
                                                        
                                                        <td class="starred-icon ">
                                                            <a class="active text-danger"><i><strong>'.$datos_consulta["ID"].'</strong></i></a>
                                                        </td>
                                                        
                                                        <td>
                                                            <span class="name">'.$datos_consulta["NombreSolicitante"].' '.$datos_consulta["ApellidoSolicitante"].'</span>
                                                            <p class="descr">'.$datos_consulta["Asunto"].'</p>
                                                        </td>
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
                            <h4 class="title">'.$DatosTickets["Asunto"].'</h4>
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
                
                
            }
                
            $css->Cdiv();
                $css->Cdiv();
                $css->Cdiv();
            $css->CrearDiv("", "panel-footer text-right", "", 1, 1);
                    print('<button id="btn_guardar" data-action="1" class="btn btn-primary m-1" onclick="frm_responder_ticket(`'.$ticket_id.'`)">Responder</button>');
                $css->Cdiv();

           $css->Cdiv();
           
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