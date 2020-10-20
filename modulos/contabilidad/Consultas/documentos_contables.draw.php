<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/documentos_contables.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new DocumentosContables($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo el formulario para realizar un predocumento
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $columnas_predocumento=$obCon->ShowColums($db.".contabilidad_predocumento_contable");
            foreach ($columnas_predocumento["Field"] as $key => $value) {
                $datos_predocumento[$value]="";
            }
            $css->div("", "row widget-separator-1 mb-1", "", "", "", "", "");
                
                print('<div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Pre Documento</label>
                                        <div class="input-group">'); 
                            $css->select("predocumento_id", "form-control", "predocumento_id", "", "", "onchange=editar_registro_documentos_contables(`$empresa_id`,`contabilidad_predocumento_contable`,`get`,`activo`,`predocumento_id`)", "");
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione");
                                $css->Coption();
                                $sql="select * from $db.contabilidad_predocumento_contable where usuario_id='$idUser'";
                                $Consulta=$obCon->Query($sql);
                                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                    $sel=$datos_consulta["activo"];
                                    
                                    $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                        print("Predocumento No. ".$datos_consulta["ID"]);
                                    $css->Coption();
                                    if($sel==1){
                                        $datos_predocumento=$datos_consulta;
                                    }
                                }
                            $css->Cselect();
                            
                            if($datos_predocumento["documento_contable_id"]=='' and $datos_predocumento["ID"]>0){
                                $documento_contable_id=$obCon->getUniqId("dc_");
                                $obCon->ActualizaRegistro($db.".contabilidad_predocumento_contable", "documento_contable_id", $documento_contable_id, "ID", $datos_predocumento["ID"]);
                            }
                            
                            print('        <div class="input-group-prepend">
                                                <button id="btn_agregar_predocumento" class="input-group-text btn btn-primary" style="cursor:pointer" >+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>');
                            
            
                print('<div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Tipo de Documento</label>
                                    <div class="input-group">'); 
                        $css->select("tipo_documento_id", "form-control", "tipo_documento_id", "", "", "onchange=editar_registro_documentos_contables(`$empresa_id`,`contabilidad_predocumento_contable`,`get`,`tipo_documento_contable_id`,`tipo_documento_id`)", "");
                            $css->option("", "", "", "", "", "");
                                    print("Seleccione un tipo de documento");
                                $css->Coption();
                            $sql="select * from $db.contabilidad_catalogo_documentos_contables";
                            $Consulta=$obCon->Query($sql);
                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($datos_predocumento["tipo_documento_contable_id"]==$datos_consulta["ID"]){
                                    $sel=1;
                                }
                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                    print($datos_consulta["Nombre"]);
                                $css->Coption();
                            }
                        $css->Cselect();

                        print('        
                                    </div>
                                </div>
                            </div>');
                        
                        
                print('<div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Sucursal</label>
                                    <div class="input-group">'); 
                        $css->select("sucursal_id", "form-control", "sucursal_id", "", "", "onchange=editar_registro_documentos_contables(`$empresa_id`,`contabilidad_predocumento_contable`,`get`,`sucursal_id`,`sucursal_id`)", "");
                            $css->option("", "", "", "", "", "");
                                    print("Seleccione una sucursal");
                                $css->Coption();
                            $sql="select * from $db.empresa_sucursales";
                            $Consulta=$obCon->Query($sql);
                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($datos_predocumento["sucursal_id"]==$datos_consulta["ID"]){
                                    $sel=1;
                                }
                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                    print($datos_consulta["Sucursal"]);
                                $css->Coption();
                            }
                        $css->Cselect();

                        print('        
                                    </div>
                                </div>
                            </div>');        
                        
                        
                        print('<div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-form-label">Centro de Costos</label>
                                    <div class="input-group">'); 
                        $css->select("centro_costo_id", "form-control", "centro_costo_id", "", "", "onchange=editar_registro_documentos_contables(`$empresa_id`,`contabilidad_predocumento_contable`,`get`,`centro_costo_id`,`centro_costo_id`)", "");
                            $css->option("", "", "", "", "", "");
                                    print("Seleccione un centro de costos");
                                $css->Coption();
                            $sql="select * from $db.empresa_centro_costo";
                            $Consulta=$obCon->Query($sql);
                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($datos_predocumento["centro_costo_id"]==$datos_consulta["ID"]){
                                    $sel=1;
                                }
                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                    print($datos_consulta["centro_costo"]);
                                $css->Coption();
                            }
                        $css->Cselect();

                        print('        
                                    </div>
                                </div>
                            </div>');   
                        
                        
                $css->Cdiv();
            $css->linea();
            $css->div("", "row widget-separator-1 mb-1", "", "", "", "", "");
                
                $css->div("", "col-md-3", "", "", "", "", "");
                    print("<strong>Fecha</strong><br>");
                    $css->input("date", "txt_fecha_predocumento", "form-control", "txt_fecha_predocumento", "", $datos_predocumento["Fecha"], "Fecha", "off", "", "onchange=editar_registro_documentos_contables(`$empresa_id`,`contabilidad_predocumento_contable`,`get`,`Fecha`,`txt_fecha_predocumento`)");
                $css->Cdiv();
                
                
                
                $css->div("", "col-md-9", "", "", "", "", "");
                    print("<strong>Observaciones</strong><br>");
                    $css->textarea("observaciones_documento", "form-control", "observaciones_documento", "", "Observaciones", "", "onchange=editar_registro_documentos_contables(`$empresa_id`,`contabilidad_predocumento_contable`,`get`,`observaciones`,`observaciones_documento`)");
                        print($datos_predocumento["observaciones"]);
                    $css->Ctextarea();
                $css->Cdiv(); 
                            
            $css->Cdiv();
            $css->linea();
            ///$css->form("frm_prefactura_general", "form-control", "frm_prefactura_general", "post", "", "", "", "style=border:0px;");
                
                $css->div("", "row widget-separator-1 mb-3", "", "", "", "", "");

                                        
                    print('<div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Cuenta Contable</label>
                                        <div class="input-group">'); 
                            $css->select("cuenta_contable", "form-control", "cuenta_contable", "", "", "", "");
                                $css->option("", "", "", "", "", "");
                                    print("Seleccione una Cuenta");
                                $css->Coption();
                            $css->Cselect();

                print('        
                            </div>
                        </div>
                    </div>');
                    
                    print('<div class="col-md-3">
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
                
                $css->div("", "col-md-2", "", "", "", "", "");
                    print("<strong>Concepto</strong><br>");
                    $css->textarea("concepto", "form-control", "concepto", "", "Concepto", "", "");
                        
                    $css->Ctextarea();
                $css->Cdiv(); 

                    

                    $css->div("", "col-md-2", "", "", "", "", "");
                        print("<strong>Impuestos</strong><br>");
                        $css->select("tipo_movimiento", "form-control", "tipo_movimiento", "", "", "", "");
                            $css->option("", "", "", "DB", "", "");
                                print("Débito");
                            $css->Coption();
                            $css->option("", "", "", "CR", "", "");
                                print("Crédito");
                            $css->Coption();
                        $css->Cselect();
                        
                        $css->input("text", "referencia", "form-control", "referencia", "", "", "Referencia", "off", "", "");
                        
                    $css->Cdiv();

                    $css->div("", "col-md-2", "", "", "", "", "");
                        $css->div("DivBases", "", "", "", "", "", "style='display:none'");
                            $css->input("hidden", "TxtSolicitaBase", "form-control", "TxtSolicitaBase", "0", "0", "", "off", "", "");
                            print("<strong>Porcentaje</strong><br>");
                            $css->input("number", "Porcentaje", "form-control", "Porcentaje", "", "", "Porcentaje", "off", "", "");
                            print("<strong>Base</strong><br>");
                            $css->input("number", "Base", "form-control", "Base", "", "", "Base", "off", "", "");
                        $css->Cdiv();    
                        print("<strong>Valor</strong><br>");
                        $css->input("number", "Valor", "form-control", "Valor", "", "", "Valor", "off", "", "");
                        print('<button id="btn_agregar_item" class="btn btn-primary" style="width:100%">Agregar</button>');
                        
                    $css->Cdiv();

                $css->Cdiv();
            //$css->Cform();
            $css->linea();
            $css->div("", "row", "", "", "", "", "");
                $css->div("div_items_prefactura", "col-md-12", "", "", "", "", "");

                $css->Cdiv();
            $css->Cdiv();
        break; //Fin caso 1
        
        case 2://Dibuja el listado de items del predocumento
            
            $predocumento_id=$obCon->normalizar($_REQUEST["predocumento_id"]);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_predocumento=$obCon->DevuelveValores("$db.contabilidad_predocumento_contable", "ID", $predocumento_id);
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>ITEMS DEL PREDOCUMENTO No. $predocumento_id</strong>", 8,"C");
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Acciones</strong>", 1);
                    $css->ColTabla("<strong>Tercero</strong>", 1);
                    $css->ColTabla("<strong>Cuenta Contable</strong>", 1);
                    
                    $css->ColTabla("<strong>Débito</strong>", 1);
                    $css->ColTabla("<strong>Crédito</strong>", 1);
                    $css->ColTabla("<strong>Concepto</strong>", 1);
                    $css->ColTabla("<strong>Referencia</strong>", 1);
                    
                $css->CierraFilaTabla();
                $documento_contable_id=$datos_predocumento["documento_contable_id"];
                $sql="SELECT t1.*,(SELECT razon_social FROM $db.terceros t2 WHERE t2.identificacion=t1.Tercero) as razon_social_tercero FROM $db.contabilidad_documentos_contables_items t1 WHERE documento_contable_id='$documento_contable_id' ORDER BY ID DESC";
                $Consulta=$obCon->Query($sql);
                $debitos=0;
                $creditos=0;
                
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$datos_consulta["ID"];
                    $debitos=$debitos+$datos_consulta["Debito"];
                    $creditos=$creditos+$datos_consulta["Credito"];
                    
                    $css->FilaTabla(16);
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");                           
                            $css->li("", "far fa-times-circle", "", "onclick=EliminarItem(`1`,`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                        $css->ColTabla($datos_consulta["Tercero"]." || ".$datos_consulta["razon_social_tercero"], 1);
                        $css->ColTabla($datos_consulta["CuentaPUC"]." || ".$datos_consulta["NombreCuenta"], 1);
                        
                        $css->ColTabla(number_format($datos_consulta["Debito"],2), 1,"R");
                        $css->ColTabla(number_format($datos_consulta["Credito"],2), 1,"R");
                        $css->ColTabla(($datos_consulta["Concepto"]), 1,"L");
                        $css->ColTabla(($datos_consulta["NumDocSoporte"]), 1,"L");
                        
                    $css->CierraFilaTabla();
                }
                
               
                $diferencia=$debitos-$creditos;
                $css->FilaTabla(14);       
                    
                    $css->ColTabla("TOTALES:", 3,"R");
                    $css->ColTabla(number_format($debitos,2), 1,"R");
                    $css->ColTabla(number_format($creditos,2), 1,"R");
                    print("<td style='text-align:rigth'>");
                        print(number_format($diferencia,2));
                    print("</td>");
                    print("<td style='text-align:rigth'>");
                        $disabled="";
                        if($diferencia<>0 or ($debitos==0 and $creditos==0)){
                            $disabled="disabled='on'";
                        }
                        print('<button '.$disabled.' id="btn_guardar_documento" class="btn btn-success" style="cursor:pointer;width:100%" >Guardar Documento</button>');
                    print("</td>");
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
            
            $Condicion=" WHERE t1.is_valid='1' ";
            
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
                                                    <h5 class="title">Documentos Enviados</h5>
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
                                        <th>PDF</th>
                                        <th>ZIP</th>       
                                        <th>Tipo de Documento</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Número</th>
                                        <th>Tercero</th>
                                        <th>Orden de Compra</th>   
                                        <th>Observaciones</th>                                       
                                        <th>Usuario</th>
                                        
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