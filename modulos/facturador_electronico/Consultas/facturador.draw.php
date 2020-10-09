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
            $css->div("", "row", "", "", "", "", "");
                $css->div("", "col-md-12", "", "", "", "style='text-align:center'", "");
                    print("<strong>MODULO FACTURADOR</strong>");
                $css->Cdiv(); 
            $css->Cdiv();
            $css->linea();
            ///$css->form("frm_prefactura_general", "form-control", "frm_prefactura_general", "post", "", "", "", "style=border:0px;");
                $css->div("", "row widget-separator-1 mb-1", "", "", "", "", "");
                    print('<div class="col-md-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Pre Factura</label>
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
                                        print("Prefactura No. ".$datos_consulta["ID"]);
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
                                $css->textarea("txt_observaciones", "form-control", "txt_observaciones", "", "Observaciones de la Factura", "", "onkeyup=editar_registro_prefactura(`$empresa_id`,`factura_prefactura`,`$prefactura_id`,`observaciones`,`txt_observaciones`)");
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
                                print('<button id="btn_guardar_factura" class="btn btn-success" style="cursor:pointer;width:100%" >Guardar Factura</button>');
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
        
                  
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>