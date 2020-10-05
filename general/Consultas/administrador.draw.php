<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/administrador.class.php");
include_once("../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Administrador($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //dibujar la lista de administradores
            //$consulta=$obCon->ConsultarTabla("plataforma_administradores", "WHERE Habilitado=1");
            $consulta=$obCon->ConsultarTabla("menu_submenus", "WHERE Estado=1 AND idMenu=1");
            while($DatosConsulta=$obCon->FetchAssoc($consulta)){               
                print('<li><a href="#" onclick="DibujeTabla(`'.$DatosConsulta["TablaAsociada"].'`);"><i class="fa fa-circle-o"></i> '.$DatosConsulta["Nombre"].'</a></li>');
            }
        break; 
        case 2: //dibuja los datos de la tabla
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $DivTablas=$obCon->normalizar($_REQUEST["DivTablas"]);
            $Condicion=$obCon->normalizarSubQuerys($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            $limit=$obCon->normalizar($_REQUEST["Limit"]);
            if($limit==''){
                $limit=10;
            }
            
            if($AscDesc==''){
                $AscDesc="ASC";
            }
                        
            $Condicion= utf8_decode($Condicion);
            $DatosSubMenu=$obCon->DevuelveValores("menu_submenus", "TablaAsociada", $Tabla);
            $TituloTabla=$DatosSubMenu["Nombre"];
            $startpoint = ($NumPage * $limit) - $limit;
            
            $ColumnasSeleccionadas=$obCon->getColumnasVisibles($Tabla, "");            
            $DatosConsulta=$obCon->getConsultaTabla($Tabla,$ColumnasSeleccionadas, $Condicion, $OrdenColumna, $AscDesc, $NumPage, $limit,$startpoint);
           
            $TotalRegistros=$DatosConsulta["TotalRegistros"];
            $QueryCompleto=$DatosConsulta["QueryCompleto"];
            $QueryParcial=$DatosConsulta["QueryParcial"];
            
            if($TotalRegistros>$limit){                
                $css->PaginadorTablas($Tabla, $limit, $NumPage, $TotalRegistros, "",$DivTablas);
            }
            $js="";
            $css->CrearTablaDB($Tabla, $Tabla, "100%", $js, "");
            
                $css->CabeceraTabla($Tabla,$limit,$TituloTabla,$ColumnasSeleccionadas, $js,$TotalRegistros,$NumPage, "",$DivTablas);
                
                $consulta=$obCon->Query($QueryCompleto);
                 
                while($DatosConsulta=$obCon->FetchAssoc($consulta)){
                    $css->FilaTablaDB($Tabla,$DivTablas,$DatosConsulta, "", "");
                }    
            $css->CerrarTablaDB();
            
            
        break;  
        
         
        case 3: //dibujar los filtros
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $Columnas=$obCon->getColumnasVisibles($Tabla, "");
                   
            $js="";
            
            $css->fieldset("fBuscar", "", "fBuscar", "", "Buscar", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivBusquedasTablas`)'>Buscar</a>");
            $css->Clegend();   
            $css->CrearDiv("DivBusquedasTablas", "", "", 0, 0);
            $css->select("CmbColumna", "form-control", "CmbColumna", "", "", $js,"");
            foreach ($Columnas["Field"] as $key => $value) {
                $css->option("", "", $value, $value, "", "");
                    print(utf8_encode($Columnas["Visualiza"][$key]));
                $css->Coption();
            }
            $css->Cselect();
            
            $css->select("CmbCondicion", "form-control", "CmbCondicion", " ", "", $js,"");
                $value="=";
                $Display="Igual";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="*";
                $Display="Contiene";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value=">";
                $Display="Mayor que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="<";
                $Display="Menor que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                
                $value=">=";
                $Display="Mayor o Igual que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                
                $value="<=";
                $Display="Menor o Igual que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="#%";
                $Display="Empieza por";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="<>";
                $Display="Diferente";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                    
            $css->Cselect();
            //$css->input("text", "TxtBusquedaTablas", "form-control", "TxtBusquedaTablas", "Valor", "", "", "", "", "");
            $Script="";
            
            $ScriptButton="onclick='AgregaCondicional()'";
            //$Script="onchange='AgregaCondicional()'";
            $css->CrearInputTextButton("text", "TxtBusquedaTablas", "BtnBuscarEnTabla", "form-control", "TxtBusquedaTablas", "BtnBuscarEnTabla", "Buscar", "Buscar", "", "Buscar", "", "", "", $Script, $ScriptButton, "", "");
            
            $css->CrearDiv("DivFiltrosAplicados", "", "", 1, 1);
                    
            $css->CerrarDiv();
            
            $css->CerrarDiv();
            
            $css->Cfieldset();
            
            
        break; 
        
        case 4: //Acciones
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $Columnas=$obCon->getColumnasVisibles($Tabla, "");
                   
            $js="";
            
            $css->fieldset("fOperaciones", "", "fOperaciones", "", "Operaciones", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivAccionesTablas`)'>Operaciones</a>");
            $css->Clegend();   
            $css->CrearDiv("DivAccionesTablas", "", "", 0, 0);
            
            $css->select("CmbAccionTabla", "form-control", "CmbAccionTabla", " ", "", $js,"");
                $value="SUM";
                $Display="SUMAR";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="COUNT";
                $Display="CONTAR";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="AVG";
                $Display="PROMEDIAR";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="MAX";
                $Display="MAXIMO";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="MIN";
                $Display="MINIMO";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                
            $css->Cselect();    
            $css->select("CmbColumnaAcciones", "form-control", "CmbColumnaAcciones", "", "", $js,"");
            foreach ($Columnas["Field"] as $key => $value) {
                $css->option("", "", $value, $value, "", "");
                    print(utf8_encode($Columnas["Visualiza"][$key]));
                $css->Coption();
            }
            $css->Cselect();
            
            $Script="";
            
            $ScriptButton="ConsultaAccionesTablas()";
            $css->CrearBotonEvento("BtnAccionTabla", "Ejecutar", 1, "onclick", $ScriptButton, "verde", "");
            $css->CrearDiv("DivResultadosAcciones", "", "", 1, 1);
                    
            $css->CerrarDiv();            
            $css->CerrarDiv();            
            $css->Cfieldset();
        break;
        
        case 5: //Realiza las consultas solicitadas
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $Columna=$obCon->normalizar($_REQUEST["Columna"]); 
            $AccionTabla=$obCon->normalizar($_REQUEST["AccionTabla"]); 
            $CondicionActual=$obCon->normalizar($_REQUEST["CondicionActual"]); 
            $ColumnaSeleccionada=$obCon->normalizar($_REQUEST["ColumnaSeleccionada"]); 
            $TxtAccionSeleccionada=$obCon->normalizar($_REQUEST["TxtAccionSeleccionada"]); 
            $Condicion="";
            if($CondicionActual<>''){
                $Condicion=" WHERE $CondicionActual";
            }
            $sql="SELECT $AccionTabla($Columna) AS Resultado FROM $Tabla $Condicion";
            //print($sql);
            
            $Consulta=$obCon->Query($sql);
            $DatosConsulta=$obCon->FetchAssoc($Consulta);
            if(is_numeric($DatosConsulta["Resultado"])){
                $Resultado=number_format($DatosConsulta["Resultado"],2);
            }else{
                $Resultado=$DatosConsulta["Resultado"];
            }
            $Mensaje='<i class="fa fa-circle-o text-red"></i><span> '.$TxtAccionSeleccionada;
            $Mensaje.=" ".$ColumnaSeleccionada." = ".$Resultado;
            $Mensaje.="</span><br>";
            print($Mensaje);
             
            break;
            
        case 6: //Ocultar o Muestra un campo
            $tbl=$obCon->normalizar($_REQUEST["Tabla"]);
            $Campo=$obCon->normalizar($_REQUEST["Campo"]);
            
            $consulta=$obCon->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$tbl' AND Campo='$Campo' AND Habilitado=1");
            $DatosCampo=$obCon->FetchAssoc($consulta);
            if($DatosCampo["Visible"]<>""){
                if($DatosCampo["Visible"]==1){
                    $obCon->ActualizaRegistro("tablas_campos_control", "Visible", 0, "ID", $DatosCampo["ID"]);
                    
                }
                if($DatosCampo["Visible"]==0){
                    $obCon->ActualizaRegistro("tablas_campos_control", "Visible", 1, "ID", $DatosCampo["ID"]);
                    
                }
            }else{
                $sql="INSERT INTO `tablas_campos_control` (`ID`, `NombreTabla`, `Campo`, `Visible`, `Editable`, `Habilitado`, `TipoUser`, `idUser`) 
                    VALUES ('', '$tbl', '$Campo', 0, 1, 1, 'administrador', 3);";
                $obCon->Query($sql);

            }
            print("OK");
        break;    
        
        case 7://Dibuja la tabla que activa o desactiva los campos
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            $ColumnasDisponibles=$obCon->getColumnasDisponibles($Tabla, "");
            
            $css->fieldset("fControlCampos", "", "fControlCampos", "", "Control de Campos", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivControlCamposTabla`)'>Campos</a>");
            $css->Clegend();   
            $css->CrearDiv("DivControlCamposTabla", "", "", 0, 0);
            
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Columna</strong>", 1);
                    $css->ColTabla("<strong>Estado</strong>", 1);
                $css->CierraFilaTabla();
                
                foreach ($ColumnasDisponibles["Field"] as $key => $value) {
                    if($key>0){
                        $idCampo="ck".$value;
                        $NombreCampo=$value;
                        $css->FilaTabla(13);
                            $css->ColTabla(utf8_encode($ColumnasDisponibles["Visualiza"][$key]), 1);
                            $css->td("", "", 1, 1, "", "","");
                                $js="onchange='OcultaMuestraCampoTabla(`$Tabla`,`$NombreCampo`);'";
                                $Estado=0;                                
                                if(array_search($value, $ColumnasVisibles["Field"])){
                                    $Estado=1; 
                                }                                                       
                                $css->CheckBoxTS($idCampo, $idCampo, "", $Estado, 1, $js,"", "", "");
                            $css->Ctd();
                        $css->CierraFilaTabla();
                    }
                }
                
            $css->CerrarTabla();
            $css->CerrarDiv();            
            $css->Cfieldset();
        break;      
        
        case 8://Dibuja las acciones
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            $ColumnasDisponibles=$obCon->getColumnasDisponibles($Tabla, "");
            
            $css->fieldset("fAcciones", "", "fAcciones", "", "Acciones", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivAcciones`)'>Acciones</a>");
            $css->Clegend();   
            $css->CrearDiv("DivAcciones", "", "", 0, 0);
            $js="onclick=DibujaFormularioNuevoRegistro(`$Tabla`)";
            $DatosControlTablas=$obCon->DevuelveValores("configuracion_control_tablas", "TablaDB", $Tabla);
            if($DatosControlTablas["Agregar"]<>0){
                $css->CrearBotonEvento("BtnNuevoRegistroTabla", "Agregar Registro", 1, "onclick", "DibujaFormularioNuevoRegistro(`$Tabla`)", "azulclaro", "");
            }
            
            $css->CerrarDiv();            
            $css->Cfieldset();
        break;  
    
        case 9://Dibuja el formulario para agregar un registro
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $DivTablas=$obCon->normalizar($_REQUEST["idDivTabla"]);
            $ColumnasVisibles=$obCon->getColumnasDisponibles($Tabla, "");
            
            $css->DibujaCamposFormularioInsert($Tabla,$DivTablas,$ColumnasVisibles, "", "");
            
        break;  
    
        case 10://Dibuja el formulario para editar un registro
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $idEditar=$obCon->normalizar($_REQUEST["idEditar"]);
            $DivTablas=$obCon->normalizar($_REQUEST["idDivTabla"]);
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            
            $css->DibujaCamposFormularioEdit($Tabla,$DivTablas,$idEditar,$ColumnasVisibles, "", "");
            
        break;  
    
        case 11://Dibuja las opciones para las tablas 

            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $DivTablas=$obCon->normalizar($_REQUEST["DivTablas"]);
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            $ColumnasDisponibles=$obCon->getColumnasDisponibles($Tabla, "");
            $TipoCajaOpciones="hidden";
            /**
             * dibujo los campos que van a contener las opciones para dibujar la tabla
             */
            $css->input($TipoCajaOpciones, $Tabla."_condicion", "", "", "", "", "", "", "", "","","","");
            $css->input($TipoCajaOpciones, $Tabla."_orden", "", "", "", "", "", "", "", "","","","");
            $css->input($TipoCajaOpciones, $Tabla."_ordenColumna", "", "", "", "", "", "", "", "","","","");
            $css->input($TipoCajaOpciones, $Tabla."_limit", "", "", "", "10", "", "", "", "","","","");
            $css->input($TipoCajaOpciones, $Tabla."_page", "", "", "", "1", "", "", "", "","","","");
            $css->ModalFormulariosTablasDB(1,$Tabla,$DivTablas);
            /**
             * Dibujo El control de campos
             */
            
            $css->div($Tabla."_Paginador", "col-sm-1", "", "", "", "", "");
            $css->Cdiv();
            
            $css->div("", "col-sm-3", "", "", "", "", "");
                $css->fieldset("fControlCampos", "", "fControlCampos", "", "Control de Campos", "");
                $css->legend("", "");
                    print("<a name='".$Tabla."_aControlCampos'><a href='#".$Tabla."_aControlCampos' onclick='MuestraOcultaXID(`".$Tabla."_DivControlCamposTabla`)'>Campos</a>");
                $css->Clegend();   
                $css->CrearDiv($Tabla."_DivControlCamposTabla", "", "", 0, 0);

                $css->CrearTabla();
                    $css->FilaTabla(14);
                        $css->ColTabla("<strong>Columna</strong>", 1);
                        $css->ColTabla("<strong>Estado</strong>", 1);
                    $css->CierraFilaTabla();

                    foreach ($ColumnasDisponibles["Field"] as $key => $value) {
                        if($key>0){
                            $idCampo="ck".$value;
                            $NombreCampo=$value;
                            $css->FilaTabla(13);
                                $css->ColTabla(utf8_encode($ColumnasDisponibles["Visualiza"][$key]), 1);
                                $css->td("", "", 1, 1, "", "","");
                                    $js="onchange='OcultaMuestraCampoTabla(`$Tabla`,`$NombreCampo`,`$DivTablas`);'";
                                    $ng_app="";
                                    $Estado=0;                                
                                    if(array_search($value, $ColumnasVisibles["Field"])){
                                        $Estado=1; 
                                    }     
                                    
                                   $css->CheckBoxTS($Tabla."_".$idCampo, $Tabla."_".$idCampo, "", $Estado, 1, "",$js, "", "");
                                    
                                $css->Ctd();
                            $css->CierraFilaTabla();
                        }
                    }
                    
                $css->CerrarTabla();
                $css->CerrarDiv();            
                $css->Cfieldset();
                
                
            $css->CerrarDiv(); 
            
            /**
             * Dibujo las opciones de busqueda
             */
            $css->div("", "col-sm-3", "", "", "", "", "");
                $js="";
            
                $css->fieldset("fBuscar", "", "fBuscar", "", "Buscar", "");
                $css->legend("", "");
                    print("<a name='".$Tabla."_aBusquedas'><a href='#".$Tabla."_aBusquedas' onclick='MuestraOcultaXID(`".$Tabla."_DivBusquedasTablas`)'>Buscar</a>");
                $css->Clegend();   
                $css->CrearDiv($Tabla."_DivBusquedasTablas", "", "", 0, 0);
                $css->select($Tabla."_CmbColumna", "form-control", $Tabla."_CmbColumna", "", "", "","");
                foreach ($ColumnasDisponibles["Field"] as $key => $value) {
                    $css->option("", "", $value, $value, "", "");
                        print(utf8_encode($ColumnasDisponibles["Visualiza"][$key]));
                    $css->Coption();
                }
                $css->Cselect();

                $css->select($Tabla."_CmbCondicion", "form-control", $Tabla."_CmbCondicion", " ", "", $js,"");
                    $value="=";
                    $Display="Igual";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();

                    $value="*";
                    $Display="Contiene";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();

                    $value=">";
                    $Display="Mayor que";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();

                    $value="<";
                    $Display="Menor que";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();


                    $value=">=";
                    $Display="Mayor o Igual que";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();


                    $value="<=";
                    $Display="Menor o Igual que";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();

                    $value="#%";
                    $Display="Empieza por";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();

                    $value="<>";
                    $Display="Diferente";
                    $css->option("", "", $Display, $value , "", "");
                        print($Display." (".$value.")");
                    $css->Coption();

                $css->Cselect();
                $Script="";

                $ScriptButton="onclick=AgregaCondicionalDB('$Tabla','$DivTablas')";
                $ng_button="";
                //$Script="onchange='AgregaCondicional()'";
                $css->CrearInputTextButton("text", $Tabla."_TxtBusquedaTablas", $Tabla."_BtnBuscarEnTabla", "form-control", $Tabla."_TxtBusquedaTablas", $Tabla."_BtnBuscarEnTabla", "Buscar", "Buscar", "", "Buscar", "", "", "", $Script, $ScriptButton, "", "","");

                $css->CrearDiv($Tabla."_DivFiltrosAplicados", "", "", 1, 1);

                $css->CerrarDiv();

                $css->CerrarDiv();

                $css->Cfieldset();
            $css->CerrarDiv(); 
            
            /**
             * Dibujo las operaciones
             */
            $css->div("", "col-sm-3", "", "", "", "", "");
                $css->fieldset("fOperaciones", "", "fOperaciones", "", "Operaciones", "");
                $css->legend("", "");
                    print("<a name='".$Tabla."_aOperaciones'><a href='#".$Tabla."_Operaciones'  onclick='MuestraOcultaXID(`".$Tabla."_DivOperacionesTablas`)'>Operaciones</a>");
                $css->Clegend();   
                $css->CrearDiv($Tabla."_DivOperacionesTablas", "", "", 0, 0);

                $css->select($Tabla."_CmbAccionTabla", "form-control", $Tabla."_CmbAccionTabla", " ", "", $js,"");
                    $value="SUM";
                    $Display="SUMAR";
                    $css->option("", "", $Display,$value, "", "");
                        print($Display);
                    $css->Coption();

                    $value="COUNT";
                    $Display="CONTAR";
                    $css->option("", "", $Display,$value, "", "");
                        print($Display);
                    $css->Coption();

                    $value="AVG";
                    $Display="PROMEDIAR";
                    $css->option("", "", $Display,$value, "", "");
                        print($Display);
                    $css->Coption();

                    $value="MAX";
                    $Display="MAXIMO";
                    $css->option("", "", $Display,$value, "", "");
                        print($Display);
                    $css->Coption();

                    $value="MIN";
                    $Display="MINIMO";
                    $css->option("", "", $Display,$value, "", "");
                        print($Display);
                    $css->Coption();


                $css->Cselect();    
                $css->select($Tabla."_CmbColumnaAcciones", "form-control", $Tabla."_CmbColumnaAcciones", "", "", $js,"");
                foreach ($ColumnasDisponibles["Field"] as $key => $value) {
                    $css->option("", "", $value, $value, "", "");
                        print(utf8_encode($ColumnasDisponibles["Visualiza"][$key]));
                    $css->Coption();
                }
                $css->Cselect();

                $Script="";

                $ScriptButton="ConsultaAccionesTablas('$Tabla')";
                $css->CrearBotonEvento($Tabla."_BtnAccionTabla", "Ejecutar", 1, "onclick", $ScriptButton, "verde", "");
                $css->CrearDiv($Tabla."_DivResultadosAcciones", "", "", 1, 1);

                $css->CerrarDiv();            
                $css->CerrarDiv();            
                $css->Cfieldset();
            $css->CerrarDiv(); 
            
            /**
             * Dibujo las acciones
             */
            $css->div("", "col-sm-2", "", "", "", "", "");
                $css->fieldset("fAcciones", "", "fAcciones", "", "Acciones", "");
            $css->legend("", "");
                print("<a name='".$Tabla."_aAciones'><a href='#".$Tabla."_aAciones' onclick='MuestraOcultaXID(`".$Tabla."_DivAcciones`)'>Acciones</a>");
                $css->Clegend();   
                $css->CrearDiv($Tabla."_DivAcciones", "", "", 0, 0);
                $js="onclick=DibujaFormularioNuevoRegistro(`$Tabla`)";
                $DatosControlTablas=$obCon->DevuelveValores("configuracion_control_tablas", "TablaDB", $Tabla);
                if($DatosControlTablas["Agregar"]<>0){
                    $css->CrearBotonEvento("BtnNuevoRegistroTabla", "Agregar Registro", 1, "onclick", "DibujaFormularioNuevoRegistro(`$Tabla`,`$DivTablas`)", "azulclaro", "");
                }
                
                if($DatosControlTablas["Exportar"]<>0 or $DatosControlTablas["Exportar"]==""){
                    $css->CrearBotonEvento("BtnExportarTablaCSV", "Exportar a CSV (;)", 1, "onclick", "ExportarTablaDBCSV(`$Tabla`)", "verde", "");
                    $css->CrearBotonEvento("BtnExportarTablaCSV", "Exportar a CSV (,)", 1, "onclick", "ExportarTablaDBCSV(`$Tabla`,`,`)", "verde", "");
                }
                
                
                $css->CerrarDiv();            
                $css->Cfieldset();
            $css->CerrarDiv();
            
            
            break; //Fin Case 11
    
            case 12: //Obtengo datos en json de una consulta a una tabla
            
               
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $DivTablas=$obCon->normalizar($_REQUEST["DivTablas"]);
            $Condicion=$obCon->normalizar($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            $limit=$obCon->normalizar($_REQUEST["Limit"]);
            if($limit==''){
                $limit=10;
            }
            
              
            $Condicion= utf8_decode($Condicion);
            $DatosSubMenu=$obCon->DevuelveValores("menu_submenus", "TablaAsociada", $Tabla);
            $TituloTabla=$DatosSubMenu["Nombre"];
            $startpoint = ($NumPage * $limit) - $limit;
            $DatosTabla[]="";
            
            $ColumnasSeleccionadas=$obCon->getColumnasVisibles($Tabla, "");  
            $DatosTabla[$Tabla]["Columnas"]=$ColumnasSeleccionadas;
            $sql="";
            foreach ($ColumnasSeleccionadas["Field"] as $key => $value) {
            if($ColumnasSeleccionadas["SubQuery"][$key]<>''){
                $value=$ColumnasSeleccionadas["SubQuery"][$key];
                }
                $sql.="$value,";
            }

            $sql = substr($sql, 0, -1);
            $Seleccion=$sql;
            $idTabla=$ColumnasSeleccionadas["Field"][0];        
            if($Condicion<>""){
                $Condicion=" WHERE ".$Condicion;
            }
            if($OrdenColumna==''){
                $OrdenColumna=$idTabla;
            }
            $Orden=" ORDER BY $OrdenColumna $AscDesc ";
            $Limite="LIMIT $startpoint,$limit";
        
            $sql="SELECT $Seleccion FROM $Tabla $Condicion  $Orden $Limite";
            $consulta=$obCon->Query($sql);
            $i=0;
            
            while($DatosConsulta=$obCon->FetchAssoc($consulta)){
                $i++;               
                $DatosTabla[$Tabla]["Valores"][$i]=$DatosConsulta;
            }
            
            
            
            print(json_encode($DatosTabla));
            
            
        break; //Fin case 12
        
        case 13: //Dibujo la cabecera de la tabla
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $DivTablas=$obCon->normalizar($_REQUEST["DivTablas"]);
            $Condicion=$obCon->normalizar($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            $limit=$obCon->normalizar($_REQUEST["Limit"]);
            if($limit==''){
                $limit=10;
            }
            if($AscDesc==''){
                $AscDesc="DESC";
            }
              
            $Condicion= utf8_decode($Condicion);
            $DatosSubMenu=$obCon->DevuelveValores("menu_submenus", "TablaAsociada", $Tabla);
            $TituloTabla= utf8_encode($DatosSubMenu["Nombre"]);
            $startpoint = ($NumPage * $limit) - $limit;
            $DatosTabla[]="";
            
            $ColumnasSeleccionadas=$obCon->getColumnasVisibles($Tabla, "");  
            $DatosTabla[$Tabla]["Columnas"]=$ColumnasSeleccionadas;
            $sql="";
            foreach ($ColumnasSeleccionadas["Field"] as $key => $value) {
            if($ColumnasSeleccionadas["SubQuery"][$key]<>''){
                $value=$ColumnasSeleccionadas["SubQuery"][$key];
                }
                $sql.="$value,";
            }

            $sql = substr($sql, 0, -1);
            $Seleccion=$sql;
            $idTabla=$ColumnasSeleccionadas["Field"][0];        
            if($Condicion<>""){
                $Condicion=" WHERE ".$Condicion;
            }
            if($OrdenColumna==''){
                $OrdenColumna=$idTabla;
            }
            $Orden=" ORDER BY $OrdenColumna $AscDesc ";
            $Limite="LIMIT $startpoint,$limit";
        
            $sql="SELECT $Seleccion FROM $Tabla $Condicion  $Orden $Limite";
            
                        
            $css->CrearTablaDB($Tabla, $Tabla, "100%", "", "");
             
            $css->CabeceraTabla($Tabla,$limit,"$TituloTabla",$ColumnasSeleccionadas, "","","", "",$DivTablas);
            $consulta=$obCon->Query($sql);
                 
                while($DatosConsulta=$obCon->FetchAssoc($consulta)){
                    $css->FilaTablaDB($Tabla,$DivTablas,$DatosConsulta, "", "");
                }  
            $css->CerrarTablaDB();  
           
        break; //Fin Case 13
        
        case 14://Dibuja el paginador
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $DivTablas=$obCon->normalizar($_REQUEST["DivTablas"]);
            $Condicion=$obCon->normalizar($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            $limit=$obCon->normalizar($_REQUEST["Limit"]);
            if($Condicion<>""){
                $Condicion=" WHERE ".$Condicion;
            }
            $sqlConteo="SELECT COUNT(*) as TotalRegistros FROM $Tabla $Condicion";
            $consulta= $obCon->Query($sqlConteo);
            $DatosConteo=$obCon->FetchAssoc($consulta);

            
            $TotalRegistros=$DatosConteo["TotalRegistros"];
                if($TotalRegistros>$limit){      

                    $css->PaginadorTablas($Tabla, $limit, $NumPage, $TotalRegistros, "",$DivTablas);

                }else{
                    print("<br>");
                }
            
       
        break; //Fin case 14
        
        case 15://obtengo informacion de los campos asociados si los hay
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $Columna=$obCon->normalizar($_REQUEST["Columna"]);
            
            $sql="SELECT * FROM configuracion_campos_asociados WHERE TablaOrigen='$Tabla' AND CampoTablaOrigen='$Columna' LIMIT 1";
            $Consulta=$obCon->Query($sql);
            $DatosCampos=$obCon->FetchAssoc($Consulta);
            print('OK;'.$DatosCampos["TablaAsociada"].";".$DatosCampos["CampoAsociado"].";".$DatosCampos["IDCampoAsociado"]);
       
        break; //Fin case 15
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>