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
                                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                    $css->option("", "", "", $datos_consulta["ID"], "", "");
                                        print($datos_consulta["prefijo"]." ".$datos_consulta["numero_resolucion"]);
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
        
                  
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>