<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/formularios.class.php");
include_once("../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new formularios($idUser);
    
    switch ($_REQUEST["Accion"]) {
       case 1://Dibuja formulario para crear un tercero de manera general
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 100, "", "", "", ""); //100 sirve para indicarle al sistema que debe guardar el formulario de crear un tercero
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Tipo de Documento</strong>", 1);
                    $css->ColTabla("<strong>Identificación</strong>", 1);
                    $css->ColTabla("<strong>Ciudad</strong>", 1);
                    $css->ColTabla("<strong>Teléfono</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("TipoDocumento", "form-control", "TipoDocumento", "", "", "", "style=width:300px");
                        $Consulta=$obCon->ConsultarTabla("cod_documentos", "");
                        while($DatosTipoDocumento=$obCon->FetchAssoc($Consulta)){
                            $sel=0;
                            if($DatosTipoDocumento["Codigo"]==13){
                                $sel=1;
                            }
                            $css->option("", "", "", $DatosTipoDocumento["Codigo"], "", "", $sel);
                                print($DatosTipoDocumento["Codigo"]." ".$DatosTipoDocumento["Descripcion"]);
                            $css->Coption();
                        }    
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Num_Identificacion", "form-control", "Num_Identificacion", "", "", "Identificación", "off", "", "onchange=VerificaNIT()");
                    print("</td>");
                    print("<td>");
                        $css->select("CodigoMunicipio", "form-control", "CodigoMunicipio", "", "", "", "");
                            $Consulta=$obCon->ConsultarTabla("cod_municipios_dptos", "");
                            while($DatosMunicipios=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($DatosMunicipios["ID"]==1011){
                                    $sel=1;
                                }
                                $css->option("", "", "", $DatosMunicipios["ID"], "", "", $sel);
                                    print($DatosMunicipios["Ciudad"]." ".$DatosMunicipios["Cod_mcipio"]);
                                $css->Coption();
                            }    
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "Telefono", "form-control", "Telefono", "", "", "Teléfono", "off", "", "");
                    print("</td>");
                    
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Nombres</strong>", 4,"C");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "PrimerNombre", "form-control", "PrimerNombre", "Primer Nombre", "", "Primer Nombre", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "OtrosNombres", "form-control", "OtrosNombres", "Otros Nombres", "", "Otros Nombres", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "PrimerApellido", "form-control", "PrimerApellido", "Primer Apellido", "", "Primer Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "SegundoApellido", "form-control", "SegundoApellido", "Segundo Apellido", "", "Segundo Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    $css->FilaTabla(16);
                        print("<td colspan=4>");
                            $css->input("text", "RazonSocial", "form-control", "RazonSocial", "Razon Social", "", "RazonSocial", "off", "", "", "");
                        print("</td>");
                    $css->CierraFilaTabla(); 
                    
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Dirección</strong>", 1);
                    $css->ColTabla("<strong>Email</strong>", 1);
                    $css->ColTabla("<strong>Cupo</strong>", 1);
                    $css->ColTabla("<strong>Código Tarjeta</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "Direccion", "form-control", "Direccion", "Direccion", "", "Dirección", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "Email", "form-control", "Email", "Email", "", "Email", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Cupo", "form-control", "Cupo", "Cupo", 0, "Cupo Crédito", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "CodigoTarjeta", "form-control", "CodigoTarjeta", "Codigo Tarjeta", "", "Código Tarjeta", "off", "", "", "onchange=VerificaCodigoTarjeta()");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 1
        
        case 2://Dibuja formulario para crear un producto o un servicio
            
            $css->CrearTitulo("<strong>Crear un producto para la venta</strong>", "azul");
            
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 101, "", "", "", ""); //100 sirve para indicarle al sistema que debe guardar el formulario de crear un tercero
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Departamento</strong>", 1);
                    $css->ColTabla("<strong>Sub Grupo 1</strong>", 1);
                    $css->ColTabla("<strong>Sub Grupo 2</strong>", 1);
                $css->CierraFilaTabla();   
                
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("CmbDepartamento", "form-control", "CmbDepartamento", "", "", "onchange=ConvierteSelectoresSubgrupos('D')", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione un departamento");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->select("CmbSub1", "form-control", "CmbSub1", "", "", "onchange=ConvierteSelectoresSubgrupos(1)", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el subgrupo 1");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->select("CmbSub2", "form-control", "CmbSub2", "", "", "onchange=ConvierteSelectoresSubgrupos(2)", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el subgrupo 2");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    $css->FilaTabla(16);
                    $css->ColTabla("<strong>Sub Grupo 3</strong>", 1);
                    $css->ColTabla("<strong>Sub Grupo 4</strong>", 1);
                    $css->ColTabla("<strong>Sub Grupo 6</strong>", 1);

                $css->CierraFilaTabla();
                    print("</tr>");
                    print("<tr>");
                    print("<td>");
                        $css->select("CmbSub3", "form-control", "CmbSub3", "", "", "onchange=ConvierteSelectoresSubgrupos(3)", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el subgrupo 3");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $css->select("CmbSub4", "form-control", "CmbSub4", "", "", "onchange=ConvierteSelectoresSubgrupos(4)", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el subgrupo 4");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->select("CmbSub6", "form-control", "CmbSub6", "", "", "onchange=ConvierteSelectoresSubgrupos(6)", "");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione el subgrupo 6");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");

                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Referencia</strong>", 1);
                    $css->ColTabla("<strong>Nombre</strong>", 1);
                    $css->ColTabla("<strong>Existencias</strong>", 1);
                    $css->CierraFilaTabla();
                    
                $css->FilaTabla(16);
                    print("<td style='text-align:center'>");
                   // $css->CrearInputText($nombre, $type, $label, $value, $placeh, $color, $TxtEvento, $TxtFuncion, $Ancho, $Alto, $ReadOnly, $Required)
                    $css->input("text", "TxtReferencia", "form-control", "Referencia", "", "", "Referencia", "off", "", "onchange=ValidaReferencia()");
                    //$css->CrearInputText("TxtReferencia", "text", "", "", "Referencia", "", "onchange", "ValidaReferencia()", 100, 30, 0, 0);
                    print("</td>");
                    print("<td style='text-align:center'>");
                    $css->input("text", "TxtNombre", "form-control", "TxtNombre", "", "", "Nombre", "off", "", "");
                    //$css->CrearInputText("TxtNombre", "text", "", "", "Nombre", "", "", "", 300, 30, 0, 1);
                    print("</td>");
                    print("<td style='text-align:center'>");
                    
                    $css->CrearInputNumber("TxtExistencias", "number", "", 0, "Existencias", "", "", "", 100, 30, 1, 1, 0, "", "any");
                    print("</td>");
                    print("</tr>");
                    
                    $css->FilaTabla(16);
                    
                    $css->ColTabla("<strong>PrecioVenta</strong>", 1);
                    $css->ColTabla("<strong>PrecioMayorista</strong>", 1);
                    $css->ColTabla("<strong>CostoUnitario</strong>", 1);

                $css->CierraFilaTabla();
                    
                    print("<tr>");
                    print("<td style='text-align:center'>");
                    $css->input("text", "TxtPrecioVenta", "form-control", "TxtPrecioVenta", "", "", "PrecioVenta", "off", "", "");
                    //$css->CrearInputNumber("TxtPrecioVenta", "number", "", "", "PrecioVenta", "", "", "", 100, 30, 0, 1, 0, "", "any");

                    print("</td>");
                    print("<td style='text-align:center'>");
                    $css->input("text", "TxtPrecioMayorista", "form-control", "TxtPrecioMayorista", "", "", "PrecioMayor", "off", "", "");
                    //$css->CrearInputNumber("TxtPrecioMayorista", "number", "", "", "PrecioMayor", "", "", "", 100, 30, 0, 1, 0, "", "any");
                    print("<br><strong>+ Precios:</strong>");
                        $css->ImageOcultarMostrar("ImgMostrarPrecios", "", "DivPrecios", 30, 30, "");
                        $css->CrearDiv("DivPrecios", "", "center", 0, 1);
                            $consulta=$obCon->ConsultarTabla("productos_lista_precios", "");
                            while($DatosListas=$obCon->FetchArray($consulta)){
                                $css->CrearInputNumber("TxtLista".$DatosListas["ID"], "number", $DatosListas["Nombre"]."<br>", 0, "", "", "", "", 100, 30, 0, 0, 0, "", "any");
                                print("<br>");
                            }
                        $css->CerrarDiv();
                    print("</td>");
                    print("<td style='text-align:center'>");
                    $css->input("text", "TxtCostoUnitario", "form-control", "TxtCostoUnitario", "", "", "CostoUnitario", "off", "", "");
                   // $css->CrearInputNumber("TxtCostoUnitario", "number", "", "", "CostoUnitario", "", "", "", 100, 30, 0, 1, 0, "", "any");
                    print("</td>");

                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                $css->ColTabla("<strong>IVA</strong>", 1);
                $css->ColTabla("<strong>CuentaPUC</strong>", 1);
                $css->ColTabla("<strong>Codigo Barras</strong>", 1);
                
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                print("<td style='text-align:center'>");
                    $DatosEmpresa=$obCon->DevuelveValores("empresapro", "idEmpresaPro", 1);
                    $IVADefecto=0;
                    if($DatosEmpresa["Regimen"]=="COMUN"){
                        $IVADefecto=0.19;
                    }
                    $css->CrearSelect("CmbIVA", "");
                    $consulta=$obCon->ConsultarTabla("porcentajes_iva", "");
                    $css->CrearOptionSelect("", "Seleccione un IVA", 0);
                    while($DatosIVA=$obCon->FetchArray($consulta)){
                        $sel=0;
                        if($DatosIVA["Valor"]=="$IVADefecto"){
                            $sel=1;
                        }
                        $css->CrearOptionSelect($DatosIVA["Valor"], $DatosIVA["Nombre"], $sel);
                    }
                    $css->CerrarSelect();
                    print("</td>");
                    print("<td colspan='1' style='text-align:center'>");
                    $css->select("CmbCuentaPUC", "form-control", "CmbCuentaPUC", "", "", "", "");
                        $css->option("", "", "", "413505", "", "");
                            print("Mercancias no fabricadas por la empresa");
                        $css->Coption();
                    $css->Cselect();
                print("</td>");
                print("<td colspan='1' style='text-align:center'>");
                    $css->CrearInputText("TxtCodigoBarras", "text", "", "", "Codigo de Barras", "", "", "", 200, 30, 0, 0);
                    print("</td>");
                    /*
                print("<td style='text-align:center'>");
                    $css->ImageOcultarMostrar("ImgMostraTalla", "", "DivTallas", 30, 30, "");
                    $css->CrearDiv("DivTallas", "", "center", 0, 1);
                        $css->CrearMultiSelectTable("Sub5", "prod_sub5", "", "idSub5", "NombreSub5", "", "", "", "",0);
                    $css->CerrarDiv();
                print("</td>");
                     * 
                     */
                
                $css->CierraFilaTabla();
            $css->CerrarTabla();
            if($_REQUEST["CrearBotonGuardar"]==1){
                $css->CrearBotonEvento("btnGuardar", "Guardar", 1, "onclick", "CrearProductoVenta(`2`)", "rojo");
            }
        break;//Fin caso 2
        
        case 3://Dibuja formulario para editar un tercero de manera general
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 103, "", "", "", ""); //103 sirve para indicarle al sistema que debe guardar el formulario para editar un tercero
            $idTercero=$obCon->normalizar($_REQUEST["idTercero"]);
            $TablaTercero=$obCon->normalizar($_REQUEST["Tabla"]);
            $css->input("hidden", "idTercero", "", "idTercero", "", $idTercero, "", "", "", "");
            $idTabla="idClientes";
            if($TablaTercero=='proveedores'){
                $idTabla="idProveedores";
            }
            $DatosTercero=$obCon->DevuelveValores($TablaTercero, $idTabla, $idTercero);
            //print_r($DatosTercero);
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Tipo de Documento</strong>", 1);
                    $css->ColTabla("<strong>Identificación</strong>", 1);
                    $css->ColTabla("<strong>Ciudad</strong>", 1);
                    $css->ColTabla("<strong>Teléfono</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("TipoDocumento", "form-control", "TipoDocumento", "", "", "", "style=width:300px");
                        $Consulta=$obCon->ConsultarTabla("cod_documentos", "");
                        while($DatosTipoDocumento=$obCon->FetchAssoc($Consulta)){
                            $sel=0;
                            if($DatosTipoDocumento["Codigo"]==$DatosTercero["Tipo_Documento"]){
                                $sel=1;
                            }
                            $css->option("", "", "", $DatosTipoDocumento["Codigo"], "", "", $sel);
                                print($DatosTipoDocumento["Codigo"]." ".$DatosTipoDocumento["Descripcion"]);
                            $css->Coption();
                        }    
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Num_Identificacion", "form-control", "Num_Identificacion", $DatosTercero["Num_Identificacion"], $DatosTercero["Num_Identificacion"], "Identificación", "off", "", "");
                    print("</td>");
                    print("<td>");
                        $css->select("CodigoMunicipio", "form-control", "CodigoMunicipio", "", "", "", "");
                            $Consulta=$obCon->ConsultarTabla("cod_municipios_dptos", "");
                            while($DatosMunicipios=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($DatosMunicipios["Cod_mcipio"]==$DatosTercero["Cod_Mcipio"] and $DatosMunicipios["Cod_Dpto"]==$DatosTercero["Cod_Dpto"]){
                                    $sel=1;
                                }
                                $css->option("", "", "", $DatosMunicipios["ID"], "", "", $sel);
                                    print($DatosMunicipios["Ciudad"]." ".$DatosMunicipios["Cod_mcipio"]);
                                $css->Coption();
                            }    
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "Telefono", "form-control", "Telefono", "", $DatosTercero["Telefono"], "Teléfono", "off", "", "");
                    print("</td>");
                    
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Nombres</strong>", 4,"C");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "PrimerNombre", "form-control", "PrimerNombre", "Primer Nombre", $DatosTercero["Primer_Nombre"], "Primer Nombre", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "OtrosNombres", "form-control", "OtrosNombres", "Otros Nombres", $DatosTercero["Otros_Nombres"], "Otros Nombres", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "PrimerApellido", "form-control", "PrimerApellido", "Primer Apellido", $DatosTercero["Primer_Apellido"], "Primer Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "SegundoApellido", "form-control", "SegundoApellido", "Segundo Apellido", $DatosTercero["Segundo_Apellido"], "Segundo Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    $css->FilaTabla(16);
                        print("<td colspan=4>");
                            $css->input("text", "RazonSocial", "form-control", "RazonSocial", "Razon Social", $DatosTercero["RazonSocial"], "RazonSocial", "off", "", "", "");
                        print("</td>");
                    $css->CierraFilaTabla(); 
                    
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Dirección</strong>", 1);
                    $css->ColTabla("<strong>Email</strong>", 1);
                    $css->ColTabla("<strong>Cupo</strong>", 1);
                    $css->ColTabla("<strong>Código Tarjeta</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "Direccion", "form-control", "Direccion", "Direccion", $DatosTercero["Direccion"], "Dirección", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "Email", "form-control", "Email", "Email", $DatosTercero["Email"], "Email", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Cupo", "form-control", "Cupo", "Cupo", $DatosTercero["Cupo"], "Cupo Crédito", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "CodigoTarjeta", "form-control", "CodigoTarjeta", "Codigo Tarjeta", $DatosTercero["CodigoTarjeta"], "Código Tarjeta", "off", "", "", "");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 3
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>