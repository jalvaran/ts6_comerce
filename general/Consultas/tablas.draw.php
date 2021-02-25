<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../class/tablas.class.php");
include_once("../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new tablas($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1://Dibuja una tabla con todos sus componentes
            
            
            
            $Limit=20;
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            if($empresa_id==0){
                $db=DB;
            }else{
                         
                $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
                $db=$DatosEmpresa["db"];
            }
            
            
            
            $tab=$obCon->normalizar($_REQUEST["tab"]);
            $idDiv=$obCon->normalizar($_REQUEST["idDiv"]);
            
            $config_tabla=$obCon->DevuelveValores("tablas_configuracion_permisos", "TablaDB", $tab);
            
            $sql="SELECT * FROM tablas_acciones_adicionales WHERE TablaDB='$tab'";
            $Consulta=$obCon->Query($sql);
            $i=0;
            while($datos_acciones_adicionales=$obCon->FetchAssoc($Consulta)){
                $acciones_adicionales[$i]=$datos_acciones_adicionales;
                $i=$i+1;
            }
            
            $Page=$obCon->normalizar($_REQUEST["page"]);
            $NumPage=$obCon->normalizar($_REQUEST["page"]);
            if($Page==''){
                $Page=1;
                $NumPage=1;
            }
            
            $BusquedasGenerales=$obCon->normalizar($_REQUEST["BusquedasGenerales"]);
            $json_busquedas = json_decode($_REQUEST["json_busquedas"],1);
            $Condicion=" WHERE ID<>'' ";
            $Condicion_busqueda_general="";
            $flag_in=0;
            $filter_active=0;
            foreach ($json_busquedas as $key => $value) {
                if($value["tab"]==$tab){
                    $flag_in=1;
                    $filter_active=1;
                    $Condicion_busqueda_general.=" AND (";                
                    $nombre_campo=$value["col"];
                    $txt_busqueda_json=$obCon->normalizar($value["txt_fil"]);
                    $datos_condicion=$obCon->DevuelveValores("catalogo_condiciones", "ID", $value["cond"]);
                    $condicion_final= str_replace("@busqueda", $txt_busqueda_json, $datos_condicion["Valor"]);
                    $Condicion_busqueda_general.=" t1.$nombre_campo ".$condicion_final;
                    
                    $Condicion_busqueda_general.=")";
                }
                
            }
            
            $Condicion.=$Condicion_busqueda_general;
                        
            if($BusquedasGenerales<>''){
                $sql="SELECT * FROM tablas_campos_busquedas WHERE nombre_tabla='$tab'";
                $Consulta=$obCon->Query($sql);
                $flag_in=0;
                $Condicion_busqueda_general=" AND ( ";
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $flag_in=1;
                    $nombre_campo=$datos_consulta["nombre_campo"];
                    if($datos_consulta["condicion"]==1){
                        $Condicion_busqueda_general.=" t1.$nombre_campo = '$BusquedasGenerales' or ";
                    }
                    if($datos_consulta["condicion"]==2){
                        $Condicion_busqueda_general.=" t1.$nombre_campo like '$BusquedasGenerales' or ";
                    }
                    if($datos_consulta["condicion"]==3){
                        $Condicion_busqueda_general.=" t1.$nombre_campo like '%$BusquedasGenerales%' or ";
                    }
                    if($datos_consulta["condicion"]==4){
                        $Condicion_busqueda_general.=" t1.$nombre_campo like '$BusquedasGenerales%' or ";
                    }
                    if($datos_consulta["condicion"]==5){
                        $Condicion_busqueda_general.=" t1.$nombre_campo like '%$BusquedasGenerales' or ";
                    }
                    
                }
                if($flag_in==1){
                    $Condicion_busqueda_general = substr($Condicion_busqueda_general, 0, -3);
                    $Condicion_busqueda_general.=") ";
                }else{
                    $Condicion_busqueda_general="";
                }
                
                
                $Condicion.=$Condicion_busqueda_general;
            }
            
            $PuntoInicio = ($Page * $Limit) - $Limit;
            
            $sql = "SELECT COUNT(*) as Items 
                   FROM $tab t1 $Condicion;";
            
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
            
            $totales = $obCon->FetchAssoc($Consulta);
            $ResultadosTotales = $totales['Items'];
            $Columnas=$obCon->getColumnsVisibles($db.".".$tab);
            
            
            $sql="SELECT ";
            
            foreach ($Columnas["Field"] as $key => $NombreCol) {
                
                $sql2="SELECT TablaAsociada,CampoTablaOrigen,CampoAsociado,IDCampoAsociado,dbCampoAsociado FROM tablas_campos_asociados WHERE TablaOrigen='$tab' AND CampoTablaOrigen='$NombreCol'";
                $CamposAsociados= $obCon->FetchAssoc($obCon->Query($sql2));
                if($CamposAsociados["CampoAsociado"]<>''){
                    $arrayField= explode(",", $CamposAsociados["CampoAsociado"]);
                    $campo_visible=$arrayField[0];
                    $tabla_asociada=$CamposAsociados["TablaAsociada"];
                    $campo_asociado=$CamposAsociados["CampoAsociado"];
                    $campo_tabla_origen=$CamposAsociados["CampoTablaOrigen"];
                    $campo_asociado_id=$CamposAsociados["IDCampoAsociado"];
                    $campo_asociado_db=$CamposAsociados["dbCampoAsociado"];
                    if($campo_asociado_db==''){
                        $campo_asociado_db=DB;
                    }else{
                        $campo_asociado_db=$db;
                    }
                    $sql.="(SELECT $campo_visible FROM $campo_asociado_db.$tabla_asociada t2 WHERE t2.$campo_asociado_id=t1.$campo_tabla_origen LIMIT 1) AS $campo_tabla_origen,";
                }else{
                    $sql.=$NombreCol.",";
                }
                
                
            }
            $sql= substr($sql, 0,-1);
            $order_by="ORDER BY ID DESC LIMIT $PuntoInicio,$Limit;";
            $sql.=" FROM $tab t1 $Condicion ";
            $statement=$sql;
            $sql.=$order_by;
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
                    
            $css->div("", "box-body no-padding", "", "", "", "", "");
                $css->div("", "mailbox-controls", "", "", "", "", "");
                    $html_boton_agregar="";
                    if($config_tabla["Agregar"]==1 or $config_tabla["Agregar"]==''){
                        
                        $html_boton_agregar='<p class="tbl-cell" style="cursor:pointer" onclick="frm_agregar_editar_registro_ts6(`'.$db.'`,`'.$tab.'`,``,`'.$idDiv.'`)"><i class="fa fa-plus-circle text-primary"></i></p>';
                    }
                    $html_exportar="";
                    if($config_tabla["Exportar"]==1 or $config_tabla["Exportar"]==''){
                        $statement= base64_encode(urlencode($statement));
                        $html_boton_exportar='<a target="_blank" href="../../general/procesadores/GeneradorCSV.process.php?Opcion=2&empresa_id='.$empresa_id.'&tb='.$tab.'&st='.$statement.'" style="font-size:40px;"><i class="far fa-file-excel text-success"></i></a>';
                    
                        $html_exportar='<div class="icon-widget">
                                            <h5 class="icon-widget-heading">Exportar</h5>
                                            <div class="icon-widget-body tbl">
                                                '.$html_boton_exportar.'
                                                <p class="tbl-cell text-right">CSV</p>
                                            </div>
                                        </div>';
                        
                    }
                    
                    $html_filtros='<div class="icon-widget">
                                            <h5 class="icon-widget-heading" >Filtros <li class="far fa-times-circle text-danger" style="cursor:pointer;" onclick="clean_filter_tab(`'.$empresa_id.'`,`'.$tab.'`,`'.$idDiv.'`)"></li></h5>
                                            <div class="icon-widget-body tbl">
                                                '
                            . '                     <div class="input-group">';
                    $html_filtros.='<div class="input-group-prepend">';
                    $html_filtros.='<div id="div_col_filtro">';
                    $html_filtros.='<select id="cmb_col_filtro" name="cmb_col_filtros" class="ts_col_filtro form-control" style="width:150px;padding: 12px;" onchange="consultar_vinculo_columna(`'.$empresa_id.'`,`'.$tab.'`,`'.$idDiv.'`)">';
                    
                    foreach ($Columnas["Field"] as $key => $value) {
                        $html_filtros.='<option value="'.$value.'">'.$Columnas["titleField"][$key].'</option>';                                                
                    }
                    $html_filtros.='</select>';
                    $html_filtros.='</div>';
                    $html_filtros.='<div id="div_condicion_filtro">';
                        $html_filtros.='<select id="cmb_condicion_filtro" name="cmb_condicion_filtro" class="ts_condicion_filtro form-control" style="width:100px;padding: 12px;">';
                            $sql_filtro="SELECT * FROM catalogo_condiciones";
                            $Consulta_filtro=$obCon->Query($sql_filtro);
                            while ($data_condicion=$obCon->FetchAssoc($Consulta_filtro)){
                                $html_filtros.='<option value="'.$data_condicion["ID"].'"> '.$data_condicion["Descripcion"].' </option>';
                            }
                        
                             
                        $html_filtros.='</select>';
                    $html_filtros.='</div>';
                    $html_filtros.='<div id="div_valor_filtro">';    
                    $html_filtros.='<input type="text" id="txt_filtro" class="form-control ts_busqueda_filtro" style="width:150px;padding: 12px;"></input>';    
                    $html_filtros.='</div>';
                    $html_filtros.='<button type="submit" class="btn btn-success input-group-text text-white" style="font-size:24px;height:51px;width:50px;padding: 12px;" onclick="add_filter_tab(`'.$empresa_id.'`,`'.$tab.'`,`'.$idDiv.'`)"><li class="fa fa-filter" ></li></button>';
                    
                    $html_filtros.='</div>';//Fin input group prepend
                    $html_filtros.='</div>';//Fin input group
                    if($filter_active==1){
                        $html_filtros."<div class='row'>";
                            $html_filtros.='<table class="table table-condensed">';
                            $html_filtros.='<tr>';
                                $html_filtros.='<td colspan="3">Filtros Aplicados:</td>';
                            $html_filtros.='</tr>';
                            $html_filtros.='<tr>';
                                $html_filtros.='<td><strong>Borrar:</strong></td>';
                                $html_filtros.='<td><strong>Columna:</strong></td>';
                                $html_filtros.='<td><strong>Condición:</strong></td>';
                                $html_filtros.='<td><strong>Valor:</strong></td>';
                            $html_filtros.='</tr>';
                            foreach ($json_busquedas as $key => $value) {
                                $data_condicion=$obCon->DevuelveValores("catalogo_condiciones", "ID", $value["cond"]);
                                $data_nombres_campos=$obCon->DevuelveValores("tablas_nombres_campos", "nombreOriginalCampo", $value["col"]);
                                $nombre_campo=$value["col"];
                                if($data_nombres_campos["muestre"]<>''){
                                    $nombre_campo=$data_nombres_campos["muestre"];
                                }
                                if($value["tab"]==$tab){
                                    $html_filtros.='<tr>';
                                        $html_filtros.='<td><li class="far fa-times-circle text-danger" style="cursor:pointer;" onclick="delete_filter(`'.$empresa_id.'`,`'.$tab.'`,`'.$idDiv.'`,`'.$key.'`)"></li></td>';
                                        $html_filtros.='<td>'.$nombre_campo.'</td>';
                                        $html_filtros.='<td>'.$data_condicion["Descripcion"].'</td>';
                                        $html_filtros.='<td>'.$value["txt_fil"].'</td>';
                                    $html_filtros.='</tr>';
                                }

                            }
                            $html_filtros.='</table>';
                        $html_filtros.'</div>';
                    }
                    $html_filtros.=            '
                                
                                            </div>
                                        </div>';
                    
                    print('<div class="row widget-separator-1 mb-3">
                                
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="icon-widget">
                                        <h5 class="icon-widget-heading">'.$tab.'</h5>
                                        <div class="icon-widget-body tbl">
                                            '.$html_boton_agregar.'
                                            <p class="tbl-cell text-right">'.$ResultadosTotales.'</p>
                                        </div>
                                    </div>
                                </div>
                           
                                <div class="col-sm-12 col-md-6 col-lg-2">
                                    '.$html_exportar.'
                                </div>
                                
                                <div class="col-sm-12 col-md-6 col-lg-5">
                                    '.$html_filtros.'
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-2">
                                
                                                                
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
                                print('<button class="btn btn-'.$Color.' btn-pill" onclick=cambie_pagina_tb_ts6(`'.$NumPage1.'`,`'.$db.'`,`'.$tab.'`,`'.$idDiv.'`) style="cursor:pointer" '.$disable.'><i class="fa fa-chevron-left" '.$disable.'></i></button>');
                            }
                            
                            
                            $FuncionJS="onchange=cambie_pagina_tb_ts6(``,`$db`,`$tab`,`$idDiv`);";
                            $css->select("cmb_page_tb_ts6", "btn btn-light text-dark btn-pill", "cmb_page_tb_ts6", "", "", $FuncionJS, "");

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
                                print('<span class="btn btn-info btn-pill" onclick=cambie_pagina_tb_ts6(`'.$NumPage1.'`,`'.$db.'`,`'.$tab.'`,`'.$idDiv.'`) style=cursor:pointer><i class="fa fa-chevron-right" ></i></span>');
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
                                    <tr>');
                        print('<th>
                                <strong>Acciones</strong>
                               </th>');
                        foreach ($Columnas["Field"] as $key => $value) {
                            print("<th>");
                                print("<strong>");
                                    print(($Columnas["titleField"][$key]));
                                print("</strong>");
                            print("</th>");
                        }                
                        
                                        
                        print('    </tr>
                                </thead>');
                        print('<tbody>');
                        
                            while($RegistrosTabla=$obCon->FetchAssoc($Consulta)){
                                
                                $idItem=$RegistrosTabla["ID"];
                                
                                print('<tr>');
                                    print('<td class="mailbox-name">');
                                        
                                        if($config_tabla["Editar"]==1 or $config_tabla["Editar"]==""){
                                            print('<a onclick="frm_agregar_editar_registro_ts6(`'.$db.'`,`'.$tab.'`,`'.$idItem.'`,`'.$idDiv.'`)" title="Editar"><i class="icon-pencil text-info"></i></a>');
                                        }else{
                                            print(' ');
                                        }
                                        if($config_tabla["Ver"]=='1'){
                                            print(" || ");
                                            $link=$config_tabla["LinkVer"];
                                            $link= str_replace("@empresa_id", $empresa_id, $link);
                                            foreach ($Columnas["Field"] as $key => $value) {
                                                $link= str_replace("@".$value, $RegistrosTabla[$value], $link);
                                            }
                                            print('<a href="'.$link.'" target="_blank" title="Ver" style="font-size:20px;"><i class="fa fa-eye text-success"></i></a>');
                                        }
                                        if(isset($acciones_adicionales[0])){
                                            foreach ($acciones_adicionales as $key => $datos_acciones) {
                                                print(" || ");
                                                $js=$datos_acciones["JavaScript"];
                                                $js= str_replace("@empresa_id", $empresa_id, $js);
                                                $link=$datos_acciones["Ruta"];
                                                $link= str_replace("@empresa_id", $empresa_id, $link);
                                                foreach ($Columnas["Field"] as $key => $value) {
                                                    $js= str_replace("@".$value, $RegistrosTabla[$value], $js);
                                                    $link= str_replace("@".$value, $RegistrosTabla[$value], $link);
                                                }
                                                $ruta="";
                                                if($js==''){
                                                    $ruta='href="'.$link.'" target="'.$datos_acciones["Target"].'"';
                                                }
                                                if($datos_acciones["Ruta"]<>''){
                                                    $js='';
                                                }
                                                print('<a '.$ruta.' title="'.$datos_acciones["Titulo"].'" '.$js.' style="font-size:20px;"><i class="'.$datos_acciones["ClaseIcono"].' text-'.$datos_acciones["Color"].'"></i></a>');
                                            }
                                        }
                                    print('</td>');
                                foreach ($RegistrosTabla as $key => $value) {
                                    print("<td class='mailbox-name'>");
                                        $longitud= strlen($value);
                                        if($longitud>50){
                                            $value= substr($value, 0, 50)."...";
                                        }
                                        print($value);
                                    print("</td>");
                                }
                                   
                                print('</tr>');

                            }

                        print('</tbody>');
                    print('</table>');
                $css->Cdiv();
            $css->Cdiv();
            
        break;//Fin caso 1

        case 2://Dibuja formulario para crear o editar un registro
            
            $tab=$obCon->normalizar($_REQUEST["tab"]);
            $db=$obCon->normalizar($_REQUEST["db"]);
            $idEdit=$obCon->normalizar($_REQUEST["idEdit"]);
            $idDiv=$obCon->normalizar($_REQUEST["idDiv"]);
            $css->frm_form("frm_ts6_registros", "Crear o Editar Registro", $db.".".$tab, $idEdit, "",$idDiv);
            
        break;//Fin caso 2
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>