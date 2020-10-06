<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja el formulario para crear una empresa
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $css->frm_form("frm_empresapro", "Empresa","empresapro",$empresa_id, "");
        break; //Fin caso 1
        
        case 2:// dibujo el listado de las empresas
            
            $Limit=5;
            $Page=$obCon->normalizar($_REQUEST["Page"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            if($Page==''){
                $Page=1;
                $NumPage=1;
            }
            //$Busquedas=$obCon->normalizar($_REQUEST["Busquedas"]);
            $BusquedasGenerales=$obCon->normalizar($_REQUEST["BusquedasGenerales"]);
            
            $Condicion=" WHERE ID>0 ";
            /*
            if($Busquedas<>''){
                $Condicion.=" AND ( t1.RazonSocial like '%$Busquedas%' or t1.NIT like '%$Busquedas%' or t1.Telefono like '%$Busquedas%')";
            }
            
             * 
             */
            if($BusquedasGenerales<>''){
                $Condicion.=" AND ( t1.RazonSocial like '%$BusquedasGenerales%' or t1.NIT like '%$BusquedasGenerales%' or t1.Telefono like '%$BusquedasGenerales%')";
            }
            
            $PuntoInicio = ($Page * $Limit) - $Limit;
            
            $sql = "SELECT COUNT(ID) as Items 
                   FROM empresapro t1 $Condicion;";
            
            $Consulta=$obCon->Query($sql);
            $totales = $obCon->FetchAssoc($Consulta);
            $ResultadosTotales = $totales['Items'];
                        
            $sql="SELECT t1.* 
                  FROM empresapro t1 $Condicion ORDER BY ID DESC LIMIT $PuntoInicio,$Limit;";
            $Consulta=$obCon->Query($sql);
                    
            $css->div("", "box-body no-padding", "", "", "", "", "");
                $css->div("", "mailbox-controls", "", "", "", "", "");
                
                    print('<div class="row widget-separator-1 mb-3">
                                <div class="col-md-3">
                                    <div class="widget-1">
                                        <div class="content">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="title">Registros</h5>
                                                    <span class="descr">Totales</span>
                                                </div>
                                                <div class="col text-right">
                                                    <div class="number text-primary">'.number_format($ResultadosTotales).'</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                
                                </div>
                                <div class="col-md-3">
                                
                                </div>
                                <div class="col-md-3">
                                
                                
                            ');
                   
                    $css->div("", "pull-right", "", "", "", "", "");
                        if($ResultadosTotales>$Limit){
                            $TotalPaginas= ceil($ResultadosTotales/$Limit);                               
                            print('<div class="btn-group">');
                            $disable='disabled="true"';
                            $Color="dark";
                            $NumPage1=$NumPage;
                            if($NumPage>1){
                                $disable="";
                                $Color="info";
                                $NumPage1=$NumPage-1;
                                print('<button class="btn btn-'.$Color.' btn-pill" onclick=CambiePagina(`1`,`'.$NumPage1.'`) style="cursor:pointer" '.$disable.'><i class="fa fa-chevron-left" '.$disable.'></i></button>');
                            }
                            
                            
                            $FuncionJS="onchange=CambiePagina(`1`);";
                            $css->select("CmbPage", "btn btn-light text-dark btn-pill", "CmbPage", "", "", $FuncionJS, "");

                                for($p=1;$p<=$TotalPaginas;$p++){
                                    if($p==$NumPage){
                                        $sel=1;
                                    }else{
                                        $sel=0;
                                    }

                                    $css->option("", "", "", $p, "", "",$sel);
                                        print($p);
                                    $css->Coption();

                                }

                            $css->Cselect();
                            $disable='disabled="true"';
                            $Color="dark";
                            if($ResultadosTotales>($PuntoInicio+$Limit)){
                                $disable="";
                                $Color="info";
                                $NumPage1=$NumPage+1;
                                print('<span class="btn btn-info btn-pill" onclick=CambiePagina(`1`,`'.$NumPage1.'`) style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
                            }
                             
                            
                            print("</div>");
                        }    
                        $css->Cdiv();
                        $css->Cdiv();
                    $css->Cdiv();
                $css->Cdiv();
                   
                $css->CrearDiv("", "table-responsive mailbox-messages", "", 1, 1);
                    print('<table class="table table-hover table-striped">');
                        print('<thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>ID</th>
                                        <th>RazonSocial</th>
                                        <th>NIT</th>
                                        <th>DV</th>
                                        <th>Dirección</th>
                                        <th>Telefonos</th>
                                        <th>Base de datos</th>
                                    </tr>
                                </thead>');
                        print('<tbody>');
                            while($RegistrosTabla=$obCon->FetchAssoc($Consulta)){
                                
                                $idItem=$RegistrosTabla["ID"];
                                
                                print('<tr>');
                                    print("<td>");
                                        print('<a onclick="frm_crear_empresa(`'.$idItem.'`)" title="Editar"><i class="icon-pencil text-info"></i></a>');
                                        print(' || <a onclick="frm_crear_cliente_factura_electronica(`'.$idItem.'`)" title="Crear como facturador electrónico" ><i class="fa fa-cogs text-warning"></i></a>');
                                    print("</td>");
                                    print("<td class='mailbox-name'>");
                                        print($RegistrosTabla["ID"]);
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        print("<strong>".$RegistrosTabla["RazonSocial"]."</strong>");
                                    print("</td>");
                                    print("<td class='mailbox-subject'>");
                                        print($RegistrosTabla["NIT"]);
                                    print("</td>");
                                    print("<td class='mailbox-subject text-success'>");
                                        print($RegistrosTabla["DigitoVerificacion"]);
                                    print("</td>");
                                    print("<td class='mailbox-subject'>");
                                        print(" <strong>".$RegistrosTabla["Direccion"]."</strong>");
                                    print("</td>");
                                    
                                    print("<td class='mailbox-subject'>");
                                        print(($RegistrosTabla["Telefono"]." || ".$RegistrosTabla["Celular"]));
                                    print("</td>");
                                    
                                    print("<td class='mailbox-subject text-flickr'>");
                                        print(($RegistrosTabla["db"]));
                                    print("</td>");
                                    
                                print('</tr>');

                            }

                        print('</tbody>');
                    print('</table>');
                $css->Cdiv();
            $css->Cdiv();
            
            
            
        break; //Fin caso 2   
           
        
        case 3: //Dibuja el formulario para crear el cliente en el api de facturación electrónica
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $css->div("", "panel panel-default", "", "", "", "", "");
                $css->div("", "panel-head", "", "", "", "", "");
                    $css->div("", "panel-title", "", "", "", "", "");
                        print('<span class="panel-title-text">Crear a '.$datosEmpresa["RazonSocial"].' || '.$datosEmpresa["NIT"].' en el API de Facturación Electrónica</span>');
                    $css->Cdiv();
                $css->Cdiv();   
                $css->div("", "panel-body", "", "", "", "", "");
                    print('<p>Seleccione uno de los procesos</p>');
                        print('<ul class="nav nav-tabs">');
                            print('<li class="nav-item">');
                                print('<a class="nav-link active show" href="#tab_empresa" data-toggle="tab" onclick=dibuje_json_empresa(`'.$empresa_id.'`)><i class="far fa-building mr-2"></i>Crear Empresa</a>');
                            print('</li>');
                            print('<li class="nav-item">');
                                print('<a class="nav-link" href="#tab_software" data-toggle="tab" onclick=dibuje_json_software(`'.$empresa_id.'`)><i class="fa fa-code-branch mr-2"></i>Crear Software</a>');
                            print('</li>');
                            print('<li class="nav-item">');
                                print('<a class="nav-link" href="#tab_certificado" data-toggle="tab" onclick=dibuje_json_certificado(`'.$empresa_id.'`);><i class="fa fa-certificate mr-2"></i>Crear Certificado Digital</a>');
                            print('</li>');
                            print('<li class="nav-item">');
                                print('<a class="nav-link" href="#tab_resolucionfe" data-toggle="tab"><i class="ti-view-list mr-2"></i>Crear Resolución de Facturación</a>');
                            print('</li>');
                            print('<li class="nav-item">');
                                print('<a class="nav-link" href="#tab_resolucionnc" data-toggle="tab"><i class="ti-view-list-alt mr-2"></i>Crear Resolución de Notas Crédito</a>');
                            print('</li>');
                            print('<li class="nav-item">');
                                print('<a class="nav-link" href="#tab_resolucionnd" data-toggle="tab"><i class="ti-view-grid mr-2"></i>Crear Resolución de Notas Débito</a>');
                            print('</li>');
                        print('</ul>'); 
                        
                        $css->div("", "tab-content", "", "", "", "", "");
                            $css->div("tab_empresa", "tab-pane active show", "", "", "", "", "");
                                print("<h5>Crear Empresa</h5>");
                                $css->div("", "row", "", "", "", "", "");  
                                
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                    $css->Cdiv();
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        $css->CrearBotonEvento("btnCrearEmpresa", "Click para Crear la Empresa en el API", 1, "onclick", "confirmaAccion(`1`,`$empresa_id`)", "rojo");
                                    $css->Cdiv();
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                    $css->Cdiv();
                                $css->Cdiv();
                                $css->div("", "row", "", "", "", "", "");  
                                    $css->div("div_crearEmpresa", "col-md-12", "", "", "", "", "");
                                    $css->Cdiv();
                                $css->Cdiv();    
                            $css->Cdiv();
                        
                            $css->div("tab_software", "tab-pane", "", "", "", "", "");
                                print("<h5>Crear Software</h5>");
                                $css->div("", "row", "", "", "", "", "");
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        
                                        print('<div class="form-group">
                                                <label class="col-form-label">ID del Software</label>
                                                    <input id="software_id" name="software_id" value="" type="text" class="form-control" placeholder="ID del software">
                                                <span class="form-text">Digite el ID que suministró la DIAN del Software</span> 
                                            </div>');
                                    $css->Cdiv();
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        
                                        print('<div class="form-group">
                                                <label class="col-form-label">PIN del Software</label>
                                                    <input id="software_pin" name="software_pin" value="" type="text" class="form-control" placeholder="PIN del software">
                                                <span class="form-text">Digite el PIN del Software</span> 
                                            </div>');
                                    $css->Cdiv();
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Click para Crear</label>');
                                            $css->CrearBotonEvento("btnCrearSoftware", "Crear Software", 1, "onclick", "confirmaAccion(`2`,`$empresa_id`)", "rojo");
                                        $css->Cdiv();
                                    $css->Cdiv();
                                    $css->div("", "row", "", "", "", "", "");  
                                        $css->div("div_crear_software", "col-md-12", "", "", "", "", "");
                                        $css->Cdiv();
                                    $css->Cdiv(); 
                                $css->Cdiv();
                            $css->Cdiv();
                        
                            $css->div("tab_certificado", "tab-pane", "", "", "", "", "");
                                print("<h5>Crear Certificado</h5>");
                                print('<div class="row">');
                                    print('<div class="col-md-4">
                                        <div class="panel">
                                            <div class="panel-head">
                                                <h5 class="panel-title">Por favor adjuntar el Certificado digital con extension .p12</h5>
                                            </div>
                                            <div class="panel-body">
                                                <form data-empresa_id="'.$empresa_id.'" action="/" class="dropzone dz-clickable" id="certificado_empresa"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre aqui el certificado digital con extension .p12<br> Suba solo un archivo con extension .p12</span></div></form>
                                            </div>
                                        </div>
                                    </div>');
                                
                                    $css->div("", "col-md-4", "", "", "", "", "");

                                        print('<div class="form-group">
                                                <label class="col-form-label">Clave del certificado</label>
                                                    <input id="clave_certificado" name="clave_certificado" value="" type="text" class="form-control" placeholder="Clave del certificado digital">
                                                <span class="form-text">Digite la clave del certificado digital</span> 
                                            </div>');
                                    $css->Cdiv();

                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Click para Crear el certificado digital</label>');
                                            $css->CrearBotonEvento("btnCrearCertificado", "Crear Certificado", 1, "onclick", "confirmaAccion(`3`,`$empresa_id`)", "rojo");
                                        $css->Cdiv();
                                    $css->Cdiv();
                                $css->Cdiv();    
                                $css->div("", "row", "", "", "", "", "");  
                                    $css->div("div_crear_certificado", "col-md-12", "", "", "", "", "");
                                    $css->Cdiv();
                                $css->Cdiv(); 
                                    
                            $css->Cdiv();
                        
                            $css->div("tab_resolucionfe", "tab-pane", "", "", "", "", "");
                                print("<h5>Crear Resolución de Facturación Electrónica</h5>");
                            $css->Cdiv();
                        
                            $css->div("tab_resolucionnc", "tab-pane", "", "", "", "", "");
                                print("<h5>Crear Resolución de Notas Crédito</h5>");
                            $css->Cdiv();
                        
                            $css->div("tab_resolucionnd", "tab-pane", "", "", "", "", "");
                                print("<h5>Crear Resolución de Notas Débito</h5>");
                            $css->Cdiv();
                        $css->Cdiv(); 
                $css->Cdiv();
            $css->Cdiv();     
                
                                    
                                    
        break; //Fin caso 3
    
        case 4://Dibuja el json de la creacion de la empresa
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosRespuestasApi=$obCon->DevuelveValores("api_factura_electronica_respuestas_procesos", "empresa_id", $empresa_id);
            $respuesta=$DatosRespuestasApi["jsonCreacionEmpresa"];
            $arrayRespuesta = json_decode($respuesta,true);
            if(is_array($arrayRespuesta)){
                foreach ($arrayRespuesta as $key => $value) {
                    if(is_array($value)){
                        foreach ($value as $key2 => $value2) {
                            print("<li><strong>$key2: </strong> ".$value2."</li>");
                        }
                    }else{
                        print("<br><strong>$key: </strong> ".$value);
                    }

                }
            }else{
                print("aún no se ha creado esta empresa en el API");
            }
        break; //Fin caso 4    
        
        case 5://Dibuja el json de la creacion del software
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosRespuestasApi=$obCon->DevuelveValores("api_factura_electronica_respuestas_procesos", "empresa_id", $empresa_id);
            $respuesta=$DatosRespuestasApi["jsonSoftware"];
            $arrayRespuesta = json_decode($respuesta,true);
            
            if(is_array($arrayRespuesta)){
                foreach ($arrayRespuesta as $key => $value) {
                    if(is_array($value)){
                        foreach ($value as $key2 => $value2) {
                            print("<li><strong>$key2: </strong> ".$value2."</li>");
                        }
                    }else{
                        print("<br><strong>$key: </strong> ".$value);
                    }

                }
            }else{
                print("No hay software creado para esta empresa");
            }
        break; //Fin caso 5
        
        case 6://Dibuja el json de la creacion del certificado digital
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosRespuestasApi=$obCon->DevuelveValores("api_factura_electronica_respuestas_procesos", "empresa_id", $empresa_id);
            $respuesta=$DatosRespuestasApi["jsonCertificado"];
            $arrayRespuesta = json_decode($respuesta,true);
            
            if(is_array($arrayRespuesta)){
                foreach ($arrayRespuesta as $key => $value) {
                    if(is_array($value)){
                        foreach ($value as $key2 => $value2) {
                            print("<li><strong>$key2: </strong> ".$value2."</li>");
                        }
                    }else{
                        print("<br><strong>$key: </strong> ".$value);
                    }

                }
            }else{
                print("No hay certificados creados para esta empresa");
            }
        break; //Fin caso 6
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>