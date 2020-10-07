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
                                print('<a class="nav-link" href="#tab_resolucionfe" data-toggle="tab" onclick=dibuje_resoluciones(`'.$empresa_id.'`)><i class="ti-view-list mr-2" ></i>Crear Resolución de Facturación</a>');
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
                                
                                
                                $css->div("", "row", "", "", "", "", ""); 
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        $css->select("cmb_tipo_documento", "form-control", "cmb_tipo_documento", "", "", "", "");
                                            $css->option("", "", "", 1, "", "");
                                                print("Facturación Electrónica");
                                            $css->Coption();
                                            
                                            $css->option("", "", "", 5, "", "");
                                                print("Nota Crédito");
                                            $css->Coption();
                                            $css->option("", "", "", 6, "", "");
                                                print("Nota Débito");
                                            $css->Coption();
                                        $css->Cselect();
                                    $css->Cdiv();
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        
                                    $css->Cdiv();
                                    $css->div("", "col-md-4", "", "", "", "", "");
                                        
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-2", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Prefijo</label>
                                                    <input id="resolucion_prefijo" name="resolucion_prefijo" value="SETP" type="text" class="form-control" placeholder="Prefijo">
                                                <span class="form-text">Prefijo de la Resolución</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Numero de Resolución</label>
                                                    <input id="resolucion_numero" name="resolucion_numero" value="18760000001" type="text" class="form-control" placeholder="Número de la Resolución">
                                                <span class="form-text">Número de la Resolución</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-2", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Fecha</label>
                                                    <input type="text" id="resolucion_fecha" name="resolucion_fecha" value="0001-01-01" class="form-control" placeholder="Fecha de la Resolución">
                                                <span class="form-text">Fecha de la Resolución</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-5", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Llave Técnica</label>
                                                    <input type="text" id="resolucion_llave" name="resolucion_llave" value="fc8eac422eba16e22ffd8c6f94b3f40a6e38162c" class="form-control" placeholder="llave técnica de la Resolución">
                                                <span class="form-text">Llave técnica</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Rango desde</label>
                                                    <input id="resolucion_rango_desde" name="resolucion_rango_desde" value="990000000" type="text" class="form-control" placeholder="Desde">
                                                <span class="form-text">Numero Inicial</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Rango hasta</label>
                                                    <input id="resolucion_rango_hasta" name="resolucion_rango_hasta" value="995000000" type="text" class="form-control" placeholder="Hasta">
                                                <span class="form-text">Numero Final</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Fecha desde</label>
                                                    <input id="resolucion_fecha_desde" name="resolucion_fecha_desde" value="2019-01-19" type="text" class="form-control" placeholder="Fecha desde">
                                                <span class="form-text">Fecha inicial</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Fecha hasta</label>
                                                    <input id="resolucion_fecha_hasta" name="resolucion_fecha_hasta" value="2030-01-19" type="text" class="form-control" placeholder="Fecha hasta">
                                                <span class="form-text">Fecha hasta</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Tipo de Acción</label>');
                                            
                                        $css->select("cmb_tipo_accion", "form-control", "cmb_tipo_accion", "", "", "", "");
                                            $css->option("", "", "", 1, "", "");
                                                print("Individual Creación y Actualización");
                                            $css->Coption();
                                            
                                            $css->option("", "", "", 2, "", "");
                                                print("Multiple Creación");
                                            $css->Coption();
                                            $css->option("", "", "", 3, "", "");
                                                print("Multiple Actualización");
                                            $css->Coption();
                                        $css->Cselect();
                                        
                                        print(' <span class="form-text">Acción a Ejecutar</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">ID de la Resolución</label>
                                                    <input id="resolucion_api_id" name="resolucion_api_id" value="" type="text" class="form-control" placeholder="ID de la Resolución">
                                                <span class="form-text">ID en el API de la Resolución</span> 
                                            </div>');
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Obtener las resoluciones</label>');
                                            $css->CrearBotonEvento("btnObtenerResoluciones", "Obtener Resoluciones", 1, "onclick", "obtenerResoluciones(`$empresa_id`)", "verde");
                                        $css->Cdiv();
                                    $css->Cdiv();
                                    
                                    $css->div("", "col-md-3", "", "", "", "", "");
                                        print('<div class="form-group">
                                                <label class="col-form-label">Click para Crear una resolución</label>');
                                            $css->CrearBotonEvento("btnCrearResolucion", "Ejecutar", 1, "onclick", "confirmaAccion(`4`,`$empresa_id`)", "rojo");
                                        $css->Cdiv();
                                    $css->Cdiv();
                                    
                                $css->Cdiv(); 
                                
                                $css->div("", "row", "", "", "", "", "");  
                                    $css->div("div_crear_resoluciones", "col-md-12", "", "", "", "", "");
                                    $css->Cdiv();
                                $css->Cdiv(); 
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
        
        case 7://Dibuja la tabla de resoluciones de facturacion
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>RESOLUCIONES CREADAS PARA LA ENTIDAD</strong>", 10,"C");
                $css->CierraFilaTabla();
                $css->FilaTabla(12);
                    $css->ColTabla("<strong>Tipo</strong>", 1,"C");
                    $css->ColTabla("<strong>prefijo</strong>", 1,"C");
                    $css->ColTabla("<strong>numero_resolucion</strong>", 1,"C");
                    $css->ColTabla("<strong>fecha_resolucion</strong>", 1,"C");
                    $css->ColTabla("<strong>llave_tecnica</strong>", 1,"C");
                    $css->ColTabla("<strong>desde</strong>", 1,"C");
                    $css->ColTabla("<strong>hasta</strong>", 1,"C");
                    $css->ColTabla("<strong>fecha_desde</strong>", 1,"C");
                    $css->ColTabla("<strong>fecha_hasta</strong>", 1,"C");
                    $css->ColTabla("<strong>resolucion_id_api</strong>", 1,"C");
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*,(SELECT t2.name FROM api_fe_tipo_documentos t2 WHERE t2.ID=t1.tipo_documento_id) as nombre_tipo_documento FROM empresa_resoluciones t1 WHERE t1.empresa_id='$empresa_id'";
                $Consulta=$obCon->Query($sql);
                while ($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $css->FilaTabla(12);
                        $css->ColTabla($datos_consulta["nombre_tipo_documento"], 1);
                        $css->ColTabla($datos_consulta["prefijo"], 1);
                        $css->ColTabla($datos_consulta["numero_resolucion"], 1);
                        $css->ColTabla($datos_consulta["fecha_resolucion"], 1);
                        $css->ColTabla($datos_consulta["llave_tecnica"], 1);
                        $css->ColTabla($datos_consulta["desde"], 1);
                        $css->ColTabla($datos_consulta["hasta"], 1);
                        $css->ColTabla($datos_consulta["fecha_desde"], 1);
                        $css->ColTabla($datos_consulta["fecha_hasta"], 1);
                        $css->ColTabla($datos_consulta["resolucion_id_api"], 1);
                        
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break; //Fin caso 7
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>