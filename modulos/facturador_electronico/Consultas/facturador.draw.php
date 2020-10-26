<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/facturador.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Facturador($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo el formulario para realizar una prefactura
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $sql="UPDATE empresa_resoluciones SET estado=3 WHERE fecha_hasta < CURDATE() AND empresa_id='$empresa_id' AND tipo_documento_id=1";
            $obCon->Query($sql);
            $css->div("", "row widget-separator-1 mb-1", "", "", "", "", "");
                
                print('<div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Tipo de Documento</label>
                                    <div class="input-group">'); 
                        $css->select("tipo_documento_id", "form-control", "tipo_documento_id", "", "", "", "");

                            $sql="select * from api_fe_tipo_documentos where activo='1'";
                            $Consulta=$obCon->Query($sql);
                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $datos_consulta["ID"], "", "");
                                    print($datos_consulta["name"]);
                                $css->Coption();
                            }
                        $css->Cselect();

                        print('        
                                    </div>
                                </div>
                            </div>');
                        
                
                print('<div id="div_documento_asociar" class="col-md-9" style="display:none">
                                <div class="form-group">
                                    <label class="col-form-label">Asociar una factura electrónica a éste documento:</label>
                                    <div class="input-group">'); 
                        $css->select("documento_asociado_id", "form-control", "documento_asociado_id", "", "", "", "");

                            $css->option("", "", "", "", "", "");
                                print("Seleccione una Factura");
                            $css->Coption();
                           
                        $css->Cselect();

                        print('        
                                    </div>
                                </div>
                            </div>');        
                        
                
            $css->Cdiv();
            $css->linea();
            ///$css->form("frm_prefactura_general", "form-control", "frm_prefactura_general", "post", "", "", "", "style=border:0px;");
                $css->div("", "row widget-separator-1 mb-1", "", "", "", "", "");
                    print('<div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Pre Documento</label>
                                        <div class="input-group">'); 
                            $css->select("prefactura_id", "form-control", "prefactura_id", "", "", "", "");
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione");
                                $css->Coption();
                                $sql="select * from $db.factura_prefactura where usuario_id='$idUser'";
                                $Consulta=$obCon->Query($sql);
                                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                    $sel=$datos_consulta["activa"];

                                    $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                        print("Predocumento No. ".$datos_consulta["ID"]);
                                    $css->Coption();
                                }
                            $css->Cselect();

                            print('        <div class="input-group-prepend">
                                                <button id="btn_agregar_prefactura" class="input-group-text btn btn-primary" style="cursor:pointer" >+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>');

                    print('<div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Tercero</label>
                                        <div class="input-group">'); 
                            $css->select("tercero_id", "form-control", "tercero_id", "", "", "", "");
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione un Tercero");
                                $css->Coption();
                            $css->Cselect();

                            print('        
                                        </div>
                                    </div>
                                </div>');


                    print('<div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Resolución</label>
                                        <div class="input-group">'); 
                            $css->select("resolucion_id", "form-control", "resolucion_id", "", "", "", "");

                                $sql="select * from empresa_resoluciones where empresa_id='$empresa_id' and tipo_documento_id=1 and estado=1";
                                $Consulta=$obCon->Query($sql);
                                $entra=0;
                                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                    $entra=1;
                                    $css->option("", "", "", $datos_consulta["ID"], "", "");
                                        print($datos_consulta["prefijo"]." ".$datos_consulta["numero_resolucion"]);
                                    $css->Coption();
                                }
                                if($entra==0){
                                    $css->option("", "", "", "", "", "");
                                        print("No hay Resoluciones disponibles");
                                    $css->Coption();
                                }
                            $css->Cselect();

                            print('        
                                        </div>
                                    </div>
                                </div>');        

                $css->Cdiv();
                $css->linea();
                $css->div("", "row widget-separator-1 mb-3", "", "", "", "", "");

                    $css->div("", "col-md-4", "", "", "", "", "");
                        print("<strong>Producto o Servicio</strong><br>");
                        $css->select("item_id", "form-control", "item_id", "", "", "", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione un item para agregar");
                            $css->Coption();
                        $css->Cselect();
                    $css->Cdiv();

                    $css->div("", "col-md-2", "", "", "", "", "");
                        print("<strong>Código</strong><br>");
                        $css->input("text", "codigo_id", "form-control", "codigo_id", "", "", "Código", "off", "", "");
                    $css->Cdiv();

                    $css->div("", "col-md-1", "", "", "", "", "");
                        print("<strong>Cantidad</strong><br>");
                        $css->input("text", "cantidad", "form-control", "cantidad", "", "1", "Cantidad", "off", "", "");
                    $css->Cdiv();

                    $css->div("", "col-md-2", "", "", "", "", "");
                        print("<strong>Precio de Venta</strong><br>");
                        $css->input("text", "precio_venta", "form-control", "precio_venta", "", "", "Precio de venta", "off", "", "");
                    $css->Cdiv();

                    $css->div("", "col-md-2", "", "", "", "", "");
                        print("<strong>Impuestos</strong><br>");
                        $css->select("cmb_impuestos_incluidos", "form-control", "cmb_impuestos_incluidos", "", "", "", "");
                            $css->option("", "", "", 0, "", "");
                                print("IVA No Incluido");
                            $css->Coption();
                            $css->option("", "", "", 1, "", "");
                                print("IVA INC");
                            $css->Coption();
                        $css->Cselect();
                    $css->Cdiv();

                    $css->div("", "col-md-1", "", "", "", "", "");
                        print("<strong>Agregar</strong><br>");
                        $css->CrearBotonEvento("btn_agregar_item", "+", 1, "", "", "verde");
                    $css->Cdiv();

                $css->Cdiv();
            //$css->Cform();
            $css->linea();
            $css->div("", "row", "", "", "", "", "");
                $css->div("div_items_prefactura", "col-md-12", "", "", "", "", "");

                $css->Cdiv();
            $css->Cdiv();
        break; //Fin caso 1
        
        case 2://Dibuja el listado de items de la prefactura
            
            $prefactura_id=$obCon->normalizar($_REQUEST["prefactura_id"]);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_prefactura=$obCon->DevuelveValores("$db.factura_prefactura", "ID", $prefactura_id);
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>ITEMS DE LA PREFACTURA No. $prefactura_id</strong>", 8,"C");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                    $css->ColTabla("<strong>Código</strong>", 1);
                    $css->ColTabla("<strong>Descripción</strong>", 1);
                    $css->ColTabla("<strong>Vr. Unitario</strong>", 1);
                    $css->ColTabla("<strong>Cantidad</strong>", 1);
                    $css->ColTabla("<strong>Subtotal</strong>", 1);
                    $css->ColTabla("<strong>Impuestos</strong>", 1);
                    $css->ColTabla("<strong>Total</strong>", 1);
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*,(SELECT Descripcion FROM $db.inventario_items_general t2 WHERE t2.ID=t1.item_id) as nombre_item FROM $db.factura_prefactura_items t1 WHERE prefactura_id='$prefactura_id' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                $subtotal=0;
                $impuestos=0;
                $total=0;
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$datos_consulta["ID"];
                    $subtotal=$subtotal+$datos_consulta["subtotal"];
                    $impuestos=$impuestos+$datos_consulta["impuestos"];
                    $total=$total+$datos_consulta["total"];
                    $css->FilaTabla(16);
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");                           
                            $css->li("", "far fa-times-circle", "", "onclick=EliminarItem(`1`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                        $css->ColTabla($datos_consulta["item_id"], 1);
                        $css->ColTabla($datos_consulta["nombre_item"], 1);
                        $css->ColTabla(number_format($datos_consulta["valor_unitario"]), 1,"R");
                        $css->ColTabla(number_format($datos_consulta["cantidad"]), 1,"R");
                        $css->ColTabla(number_format($datos_consulta["subtotal"]), 1,"R");
                        $css->ColTabla(number_format($datos_consulta["impuestos"]), 1,"R");
                        $css->ColTabla(number_format($datos_consulta["total"]), 1,"R");
                    $css->CierraFilaTabla();
                }
                
                $css->FilaTabla(14);
                    print("<td colspan=6 rowspan=4>");
                        print("<div class='row'>");
                            print("<div class='col-md-9'>");
                                $css->textarea("txt_observaciones", "form-control", "txt_observaciones", "", "Observaciones del documento", "", "onkeyup=editar_registro_prefactura(`$empresa_id`,`factura_prefactura`,`$prefactura_id`,`observaciones`,`txt_observaciones`)");
                                    print($datos_prefactura["observaciones"]);
                                $css->Ctextarea();
                            print("</div>");
                            print("<div class='col-md-3'>");
                                $css->input("text", "orden_compra", "form-control", "orden_compra", "", $datos_prefactura["orden_compra"], "Orden de Compra", "off", "", "onkeyup=editar_registro_prefactura(`$empresa_id`,`factura_prefactura`,`$prefactura_id`,`orden_compra`,`orden_compra`)");
                                
                                $css->select("forma_pago", "form-control", "forma_pago", "", "", "onchange=editar_registro_prefactura(`$empresa_id`,`factura_prefactura`,`$prefactura_id`,`forma_pago`,`forma_pago`)", "");
                                    $sel=0;
                                    if($datos_prefactura["forma_pago"]==1){
                                        $sel=1;
                                    }
                                    $css->option("", "", "", 1, "", "",$sel);
                                        print("Contado");
                                    $css->Coption();
                                    $sel=0;
                                    if($datos_prefactura["forma_pago"]==2){
                                        $sel=1;
                                    }
                                    $css->option("", "", "", 2, "", "",$sel);
                                        print("Crédito");
                                    $css->Coption();
                                $css->Cselect();
                                print('<button id="btn_guardar_factura" class="btn btn-success" style="cursor:pointer;width:100%" >Guardar Documento</button>');
                            print("</div>");
                        print("</div>");
                        
                    print("</td>");
                $css->FilaTabla(14);                
                    $css->ColTabla("<strong>Subtotal</strong>", 1,"R");
                    $css->ColTabla(number_format($subtotal), 1,"R");
                $css->CierraFilaTabla(); 
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Impuestos</strong>", 1,"R");
                    $css->ColTabla(number_format($impuestos), 1,"R");
                $css->CierraFilaTabla(); 
                $css->FilaTabla(16);    
                    $css->ColTabla("<strong>Total</strong>", 1,"R");
                    $css->ColTabla(number_format($total), 1,"R");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 2   
        
        case 3://Listado de documentos electronicos enviados
            
            $tabla="vista_documentos_electronicos";
            $Limit=20;
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $obCon->crear_vista_documentos_electronicos($db);
            
            $Page=$obCon->normalizar($_REQUEST["Page"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            if($Page==''){
                $Page=1;
                $NumPage=1;
            }
            
            $BusquedasGenerales=$obCon->normalizar($_REQUEST["txtBusquedasGenerales"]);
            
            $Condicion=" WHERE is_valid='1' ";
            
            if($BusquedasGenerales<>''){
                $Condicion.=" AND ( numero = '$BusquedasGenerales' or uuid = '$BusquedasGenerales' or nombre_tercero like '%$BusquedasGenerales%' or nit_tercero = '$BusquedasGenerales')";
            }
            
            
            $PuntoInicio = ($Page * $Limit) - $Limit;
            
            $sql = "SELECT COUNT(ID) as Items 
                   FROM $tabla t1 $Condicion;";
            
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
            
            $totales = $obCon->FetchAssoc($Consulta);
            $ResultadosTotales = $totales['Items'];
            $limit_condition=" LIMIT $PuntoInicio,$Limit;";
            $colsQuery="ID, documento_electronico_id,fecha,hora,tipo_documento_id,nombre_tipo_documento,prefijo,numero,nombre_tercero,nit_tercero,subtotal_documento,impuestos_documento,total_documento,documento_asociado,notas,orden_compra,nombre_tercero,uuid,nombre_usuario,nombre_items ";
            $sql="SELECT $colsQuery 
                  FROM $tabla t1 $Condicion ";
            $statement=$sql;
            $sql.=$limit_condition;
            
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
              
            $css->div("", "box-body no-padding", "", "", "", "", "");
                $css->div("", "mailbox-controls", "", "", "", "", "");
                
                    print('<div class="row widget-separator-1 mb-3">
                                <div class="col-md-3">
                                    <div class="widget-1">
                                        <div class="content">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="title">Documentos Enviados</h5>
                                                    <span class="descr">Total Registros</span>
                                                </div>
                                                <div class="col text-right">
                                                    <div class="number text-primary">'.number_format($ResultadosTotales).'</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>');
                    $statement= base64_encode(urlencode($statement));
                    $colsQuery= base64_encode(urlencode($colsQuery));
                    $html_boton_exportar='<a target="_blank" href="../../general/procesadores/GeneradorCSV.process.php?Opcion=3&empresa_id='.$empresa_id.'&tb='.$tabla.'&st='.$statement.'&colsQuery='.$colsQuery.'" style="font-size:40px;"><i class="far fa-file-excel text-success"></i></a>';

                    $html_exportar='<div class="icon-widget">
                                        <h5 class="icon-widget-heading">Exportar</h5>
                                        <div class="icon-widget-body tbl">
                                            '.$html_boton_exportar.'
                                            <p class="tbl-cell text-right">CSV</p>
                                        </div>
                                    </div>';
                    print(' 
                                <div class="col-md-3">
                                    '.$html_exportar.'
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
                                      
                        print(' <thead>
                                    <tr>
                                        <th>PDF</th>
                                        <th>ZIP</th>       
                                        <th>Tipo de Documento</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Número</th>
                                        <th>Subtotal</th>
                                        <th>Impuestos</th>
                                        <th>Total</th>
                                        <th>Tercero</th>
                                        <th>Orden de Compra</th>   
                                        <th>Observaciones</th>                                       
                                        <th>Usuario</th>
                                        <th>Documento Asociado</th>
                                        <th>Items</th>
                                        <th>UUID</th>
                                        
                                                                                
                                    </tr>
                                </thead>');
                        print('<tbody>');
                        
                        while($RegistrosTabla=$obCon->FetchAssoc($Consulta)){

                            $idItem=$RegistrosTabla["documento_electronico_id"];

                            print('<tr>');
                                print("<td style='text-align:center'>");
                                    $link="procesadores/facturador.process.php?Accion=8&empresa_id=$empresa_id&documento_electronico_id=$idItem";
                                    print('<a style="font-size:25px;text-align:center" title="Ver PDF" href="'.$link.'" target="_blank")" ><i class="far fa-file-pdf text-danger"></i></a>');

                                print("</td>");
                                print("<td style='text-align:center'>");
                                    $link="procesadores/facturador.process.php?Accion=9&empresa_id=$empresa_id&documento_electronico_id=$idItem";
                                    print('<a style="font-size:25px;text-align:center" title="Ver ZIP" href="'.$link.'" target="_blank" ><i class="far fa-file-archive text-primary"></i></a>');

                                print("</td>");
                                                                      

                                print("<td class='mailbox-name'>");
                                    print($RegistrosTabla["nombre_tipo_documento"]);
                                print("</td>");
                                print("<td class='mailbox-subject text-primary'>");
                                    print("<strong>".$RegistrosTabla["fecha"]."</strong>");
                                print("</td>");
                                print("<td class='mailbox-subject text-primary'>");
                                    print("<strong>".$RegistrosTabla["hora"]."</strong>");
                                print("</td>");
                                
                                print("<td class='mailbox-subject'>");
                                    print("<strong>".$RegistrosTabla["prefijo"]."-".$RegistrosTabla["numero"]."</strong>");
                                print("</td>");   
                                
                                print("<td class='mailbox-subject text-flickr'>");
                                    print("<strong>".number_format($RegistrosTabla["subtotal_documento"])."</strong>");
                                print("</td>");
                                
                                print("<td class='mailbox-subject text-flickr'>");
                                    print("<strong>".number_format($RegistrosTabla["impuestos_documento"])."</strong>");
                                print("</td>");
                                
                                print("<td class='mailbox-subject text-flickr'>");
                                    print("<strong>".number_format($RegistrosTabla["total_documento"])."</strong>");
                                print("</td>");
                                
                                print("<td class='mailbox-subject text-success'>");
                                    print(" ".$RegistrosTabla["nombre_tercero"]." || <strong>" .$RegistrosTabla["nit_tercero"]."</strong>");
                                print("</td>");
                                print("<td class='mailbox-subject text-primary'>");
                                    print(" <strong>".$RegistrosTabla["orden_compra"]."</strong>");
                                print("</td>");
                                print("<td class='mailbox-subject text-flickr'>");
                                    print($RegistrosTabla["notas"]);
                                print("</td>");
                                
                                
                                print("<td class='mailbox-name'>");
                                    print($RegistrosTabla["nombre_usuario"]);
                                print("</td>");
                                
                                print("<td class='mailbox-name'>");
                                    print($RegistrosTabla["documento_asociado"]);
                                print("</td>");
                                print("<td class='mailbox-name text-primary'>");
                                    print($RegistrosTabla["nombre_items"]);
                                print("</td>");
                                
                                print("<td class='mailbox-name'>");
                                    print($RegistrosTabla["uuid"]);
                                print("</td>");
                                
                                

                            print('</tr>');

                        }

                    print('</tbody>');
                print('</table>');
            $css->Cdiv();
        $css->Cdiv();
            
        break;//Fin caso 3   
        
        case 4://Listado de documentos electronicos con error
            
            $tabla="vista_documentos_electronicos";
            $Limit=20;
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $obCon->crear_vista_documentos_electronicos($db);
            
            $Page=$obCon->normalizar($_REQUEST["Page"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            if($Page==''){
                $Page=1;
                $NumPage=1;
            }
            
            $BusquedasGenerales=$obCon->normalizar($_REQUEST["txtBusquedasGenerales"]);
            
            $Condicion=" WHERE t1.is_valid<>'1' ";
            
            if($BusquedasGenerales<>''){
                $Condicion.=" AND ( t1.numero = '$BusquedasGenerales' or t1.uuid = '$BusquedasGenerales' or t1.nombre_tercero like '%$BusquedasGenerales%' or t1.nit_tercero = '$BusquedasGenerales')";
            }
            
            
            $PuntoInicio = ($Page * $Limit) - $Limit;
            
            $sql = "SELECT COUNT(ID) as Items 
                   FROM $tabla t1 $Condicion;";
            
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
            
            $totales = $obCon->FetchAssoc($Consulta);
            $ResultadosTotales = $totales['Items'];
                        
            $sql="SELECT t1.*
                  FROM $tabla t1 $Condicion LIMIT $PuntoInicio,$Limit;";
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
                    
            $css->div("", "box-body no-padding", "", "", "", "", "");
                $css->div("", "mailbox-controls", "", "", "", "", "");
                
                    print('<div class="row widget-separator-1 mb-3">
                                <div class="col-md-3">
                                    <div class="widget-1">
                                        <div class="content">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="title">Errores</h5>
                                                    <span class="descr">Total Registros</span>
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
                                      
                        print(' <thead>
                                    <tr>
                                        <th>Reenviar</th>
                                        <th>Código</th>
                                        <th>Tipo de Documento</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Número</th>
                                        <th>Tercero</th>
                                        <th>Orden de Compra</th>   
                                        <th>Observaciones</th>                                       
                                        <th>Usuario</th>
                                        
                                                                                
                                    </tr>
                                </thead>');
                        print('<tbody>');
                        
                        while($RegistrosTabla=$obCon->FetchAssoc($Consulta)){

                            $idItem=$RegistrosTabla["documento_electronico_id"];

                            print('<tr>');
                                print("<td style='text-align:center'>");
                                    print('<a style="font-size:25px;text-align:center" title="Reenviar" onclick="reportar_documento_electronico_api(`'.$idItem.'`)" ><i class="fa fa-paper-plane text-warning"></i></a>');

                                print("</td>");
                                
                                print("<td style='text-align:center'>");
                                    print('<a style="font-size:25px;text-align:center" title="Ver PDF" onclick="ver_json_documento(`'.$empresa_id.'`,`'.$idItem.'`)" ><i class="fa fa-code text-primary"></i></a>');

                                print("</td>");
                                    
                                print("<td class='mailbox-name'>");
                                    print($RegistrosTabla["nombre_tipo_documento"]);
                                print("</td>");
                                print("<td class='mailbox-subject text-primary'>");
                                    print("<strong>".$RegistrosTabla["fecha"]."</strong>");
                                print("</td>");
                                print("<td class='mailbox-subject text-primary'>");
                                    print("<strong>".$RegistrosTabla["hora"]."</strong>");
                                print("</td>");
                                
                                print("<td class='mailbox-subject'>");
                                    print("<strong>".$RegistrosTabla["prefijo"]."-".$RegistrosTabla["numero"]."</strong>");
                                print("</td>");   
                                
                                print("<td class='mailbox-subject text-success'>");
                                    print(" ".$RegistrosTabla["nombre_tercero"]." || <strong>" .$RegistrosTabla["nit_tercero"]."</strong>");
                                print("</td>");
                                print("<td class='mailbox-subject text-primary'>");
                                    print(" <strong>".$RegistrosTabla["orden_compra"]."</strong>");
                                print("</td>");
                                print("<td class='mailbox-subject text-flickr'>");
                                    print($RegistrosTabla["notas"]);
                                print("</td>");
                                
                                
                                print("<td class='mailbox-name'>");
                                    print($RegistrosTabla["nombre_usuario"]);
                                print("</td>");
                                                                
                            print('</tr>');

                        }

                    print('</tbody>');
                print('</table>');
            $css->Cdiv();
        $css->Cdiv();
            
        break;//Fin caso 4
        
             
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>