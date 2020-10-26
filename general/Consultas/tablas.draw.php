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
            
            $Condicion=" WHERE ID<>'' ";
            
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
            $Columnas=$obCon->getColumns($db.".".$tab);
            
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
                           
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    '.$html_exportar.'
                                </div>
                                
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                
                                                                
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