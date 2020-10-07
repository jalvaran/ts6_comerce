<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/mantenimiento.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Mantenimiento($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo el listado de las ordenes de trabajo
            
            $tabla="vista_ordenes_trabajo";
            $Limit=20;
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $obCon->CrearVistaOrdenTrabajo($db);
            
            $Page=$obCon->normalizar($_REQUEST["Page"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            if($Page==''){
                $Page=1;
                $NumPage=1;
            }
            
            $BusquedasGenerales=$obCon->normalizar($_REQUEST["BusquedasGenerales"]);
            $fecha_inicial=$obCon->normalizar($_REQUEST["fecha_inicial"]);
            $fecha_final=$obCon->normalizar($_REQUEST["fecha_final"]);
            $cmb_tipo_mantenimiento=$obCon->normalizar($_REQUEST["cmb_tipo_mantenimiento"]);
            $cmb_estado=$obCon->normalizar($_REQUEST["cmb_estado"]);
            
            
            $Condicion=" WHERE ID>'0' ";
            
            if($BusquedasGenerales<>''){
                $Condicion.=" AND ( t1.nombre_maquina like '%$BusquedasGenerales%' or t1.codigo_maquina = '%$BusquedasGenerales%' or t1.ID = '$BusquedasGenerales')";
            }
            
            if($fecha_inicial<>''){
                $Condicion.=" AND t1.fecha_programada >= '$fecha_inicial' ";
            }
            
            if($fecha_final<>''){
                $Condicion.=" AND t1.fecha_programada <= '$fecha_final' ";
            }
            
            if($cmb_tipo_mantenimiento<>''){
                $Condicion.=" AND t1.tipo_mantenimiento = '$cmb_tipo_mantenimiento' ";
            }
            
            if($cmb_estado<>''){
                $Condicion.=" AND t1.estado_ejecucion = '$cmb_estado' ";
            }
            /*
            if($cmb_estado=='1'){
                $Condicion.=" AND t1.estado_ejecucion = '1' ";
            }
            
             * 
             */
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
                                                    <h5 class="title">Órdenes de Trabajo</h5>
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
                                        <th>PDF</th>
                                        <th>Ver Adjuntos</th>');
                        
                        
                        if($cmb_tipo_mantenimiento<>''){
                            print('<th>Editar</th>');
                        }                
                        print('         <th>Cerrar</th>
                                        <th>ID</th>
                                        <th>Fecha Programada</th>
                                        <th>Hrs Pasadas</th>
                                        <th>Kms Pasados</th>
                                        <th>Mantenimiento</th>
                                        <th>Máquina</th>                                        
                                        <th>Componente</th>
                                        <th>Ubicación</th>
                                        <th>Observaciones</th>
                                        <th>Estado</th>
                                        <th>Fecha de Cierre</th>
                                        <th>Técnico</th>                                        
                                        <th>Observaciones de Cierre</th>
                                        
                                    </tr>
                                </thead>');
                        print('<tbody>');
                            while($RegistrosTabla=$obCon->FetchAssoc($Consulta)){
                                
                                $idItem=$RegistrosTabla["ID"];
                                
                                print('<tr>');
                                    print("<td style='text-align:center'>");
                                        $link="../../general/Consultas/PDF_Documentos.draw.php?idDocumento=1&empresa_id=$empresa_id&id=$idItem";
                                        print('<a style="font-size:25px;color:green" href="'.$link.'" target="_blank" title="Ver PDF" title="Ver PDF" ><i class="far fa-file-pdf text-error"></i></a>');
                                    print("</td>");
                                    print("<td style='text-align:center'>");
                                        print('<a style="font-size:25px;text-align:center" title="Ver Adjuntos" onclick="listar_adjuntos_ot(`'.$RegistrosTabla["orden_trabajo_id"].'`,`div_modal_view`,`modal_view`)" ><i class="fa fa-paperclip text-primary"></i></a>');
                                                                                
                                    print("</td style='text-align:center'>");
                                        if($cmb_tipo_mantenimiento<>''){
                                            print("<td>");
                                            if($RegistrosTabla["estado"]<3){
                                                print('<a style="font-size:25px;text-align:center" title="Editar Orden" onclick="frm_crear_editar_registro(`'.$idItem.'`)" ><i class="icon-pencil text-info"></i></a>');
                                                
                                            }
                                            print("</td>");
                                        }                                       
                                    
                                    print("<td style='text-align:center'>");
                                        if($RegistrosTabla["estado"]<3){
                                            print('<a onclick="form_cerrar_orden(`'.$idItem.'`)" title="Cerrar Orden" style="text-align:center" ><i style="font-size:25px;color:red" class="fa fa-clipboard-list"></i></a>');
                                        }
                                    print("</td>");
                                    print("<td class='mailbox-name'>");
                                        print($RegistrosTabla["ID"]);
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        print("<strong>".$RegistrosTabla["fecha_programada"]."</strong>");
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        if($RegistrosTabla["diferencia_horas"]<0){
                                            $horas_pasadas=abs($RegistrosTabla["diferencia_horas"]);
                                        }else{
                                            $horas_pasadas=0;
                                        }
                                        print("<strong>".number_format($horas_pasadas)."</strong>");
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        if($RegistrosTabla["diferencia_kilometros"]<0){
                                            $kilometros_pasados=abs($RegistrosTabla["diferencia_kilometros"]);
                                        }else{
                                            $kilometros_pasados=0;
                                        }
                                        print("<strong>".number_format($kilometros_pasados)."</strong>");
                                    print("</td>");
                                    
                                    print("<td class='mailbox-subject'>");
                                        print($RegistrosTabla["nombre_tipo_mantenimiento"]);
                                    print("</td>");                                    
                                    print("<td class='mailbox-subject text-success'>");
                                        print(" <strong>".$RegistrosTabla["nombre_maquina"]."</strong>");
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        print(" <strong>".$RegistrosTabla["nombre_componente"]."</strong>");
                                    print("</td>");
                                    print("<td class='mailbox-subject text-flickr'>");
                                        print($RegistrosTabla["nombre_ubicacion"]);
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        print(" <strong>".$RegistrosTabla["observaciones_orden"]."</strong>");
                                    print("</td>");
                                    print("<td class='mailbox-subject text-flickr'>");
                                        print(" <strong>".$RegistrosTabla["nombre_estado"]."</strong>");
                                    print("</td>");
                                    
                                    print("<td class='mailbox-subject'>");
                                        print(($RegistrosTabla["fecha_cierre"]));
                                    print("</td>");
                                    print("<td class='mailbox-subject'>");
                                        print(($RegistrosTabla["nombre_tecnico"]));
                                    print("</td>");
                                                                        
                                    print("<td class='mailbox-subject'>");
                                        print(($RegistrosTabla["observaciones_cierre"]));
                                    print("</td>");
                                    
                                print('</tr>');

                            }

                        print('</tbody>');
                    print('</table>');
                $css->Cdiv();
            $css->Cdiv();
            
            
            
        break; //Fin caso 1
        
        
        case 2: //Dibuja el formulario para crear o editar una orden de trabajo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $cmb_tipo_mantenimiento=$obCon->normalizar($_REQUEST["cmb_tipo_mantenimiento"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            $edit_id=$obCon->normalizar($_REQUEST["edit_id"]);
                
            $css->frm_form_orden_mantenimiento($db,$edit_id,$cmb_tipo_mantenimiento);
        break; //Fin caso 2
    
        case 3: //Dibuja las tareas en una orden de trabajo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Código", 1);
                    $css->ColTabla("Tarea", 1);
                    $css->ColTabla("Tipo Tarea", 1);
                    $css->ColTabla("Eliminar", 1);
                $css->CierraFilaTabla();
                
                $sql="SELECT t2.*,t1.ID as ordenes_trabajo_tareas_id, t1.estado as ordenes_trabajo_tareas_estado  
                        FROM ordenes_trabajo_tareas t1 INNER JOIN catalogo_tareas t2 ON t1.tarea_id=t2.ID 
                        WHERE t1.orden_trabajo_id='$orden_trabajo_id' 
                            ";
                $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
                while($DatosTareas=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosTareas["ordenes_trabajo_tareas_id"];
                    $css->FilaTabla(14);
                
                        $css->ColTabla($DatosTareas["ordenes_trabajo_tareas_id"], 1);
                        $css->ColTabla($DatosTareas["CodigoTarea"], 1);
                        $css->ColTabla($DatosTareas["NombreTarea"], 1);
                        $css->ColTabla($DatosTareas["TipoTarea"], 1);
                        print('<td style="text-align:center;color:red;font-size:18px;">');
                            print('<li class="far fa-times-circle" style="cursor:pointer;" onclick="eliminarItem(`1`,`'.$idItem.'`)"></li>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 3
        
        case 4://dibuja el formulario para cerrar una orden
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            $DatosOT=$obCon->DevuelveValores("$db.ordenes_trabajo", "ID", $orden_trabajo_id);
            
            if($DatosOT["tipo_mantenimiento"]==1){
                $css->frm_cerrar_orden_trabajo_correctivo($db, $DatosOT);
            }
            
            if($DatosOT["tipo_mantenimiento"]==2){
                $css->frm_cerrar_orden_trabajo_preventivo($db, $DatosOT);
            }
            
            if($DatosOT["tipo_mantenimiento"]==3){
                $css->frm_cerrar_orden_trabajo_ruta_verificacion($db, $DatosOT);
            }
            
        break;//Fin caso 4    
        
        case 5://Buscar una parte o suministro para agregar a una orden de trabajo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            $BusquedaSuministros=$obCon->normalizar($_REQUEST["BusquedaSuministros"]);
            if($BusquedaSuministros==''){
                exit(" ");
            }
            $arrayBusqueda= explode(" ", $BusquedaSuministros);
            $sql="SELECT * FROM $db.equipos_partes ";
            $Condicion=" WHERE Codigo='$BusquedaSuministros' ";
            foreach ($arrayBusqueda as $key => $value) {
               $Condicion.=" OR DescripcionPrimaria LIKE '%$value%' "; 
            }
            $sql.=$Condicion." LIMIT 30";
            
            $Consulta=$obCon->Query($sql);
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("Codigo", 1);
                    $css->ColTabla("Nombre", 1);
                    $css->ColTabla("ValorUnitario", 1);
                    $css->ColTabla("Cantidad", 1);
                    $css->ColTabla("Agregar", 1);
                $css->CierraFilaTabla();
                
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosConsulta["Codigo"], 1);
                        $css->ColTabla($DatosConsulta["DescripcionPrimaria"]." ".$DatosConsulta["Unidad"], 1);
                        print('<td>');
                            print('<input type="number" id="TxtValorUnitario_'.$idItem.'" class="form-control" placeholder="Valor Unitario" value="'.$DatosConsulta["Costo"].'">');
                        print('</td>');
                        print('<td>');
                            print('<input type="number" id="TxtCantidad_'.$idItem.'" class="form-control" placeholder="Cantidad" value="1">');
                        print('</td>');
                        print('<td>');
                            print('<li id="btn_agregar_insumo_'.$idItem.'" class="fa fa-plus-circle" style="color:green;font-size:25px;cursor:pointer" onclick=AgregarInsumoAOrdenTrabajo(`'.$idItem.'`,`'.$orden_trabajo_id.'`)></li>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;// fin caso 5    
        
        
        case 6://listar las partes o suministros agregados a una orden de trabajo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $sql="SELECT t1.*,t2.DescripcionPrimaria,t2.Unidad, t2.Codigo   
                     FROM $db.ordenes_trabajo_insumos t1 
                    INNER JOIN $db.equipos_partes t2 ON t2.ID=t1.insumo_id 
                    WHERE t1.orden_trabajo_id='$orden_trabajo_id'  
                    
                    ";
                        
            $Consulta=$obCon->Query($sql);
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("Codigo", 1);
                    $css->ColTabla("Nombre", 1);
                    $css->ColTabla("ValorUnitario", 1);
                    $css->ColTabla("Cantidad", 1);
                    $css->ColTabla("Total", 1);
                    $css->ColTabla("Eliminar", 1);
                $css->CierraFilaTabla();
                
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosConsulta["Codigo"], 1);
                        $css->ColTabla($DatosConsulta["DescripcionPrimaria"]." ".$DatosConsulta["Unidad"], 1);
                        $css->ColTabla(number_format($DatosConsulta["valor_unitario"]), 1);
                        $css->ColTabla(number_format($DatosConsulta["cantidad"]), 1);
                        $css->ColTabla(number_format($DatosConsulta["total"]), 1);
                        
                        print('<td>');
                            print('<li class="far fa-times-circle" style="color:red;font-size:25px;cursor:pointer" onclick=eliminarItem(`2`,`'.$idItem.'`)></li>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;// fin caso 6
        
        case 7://listar las fallas agregadas a una orden de trabajo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            $dbPrincipal=DB;
            $sql="SELECT t1.*,
                    (SELECT Falla FROM $dbPrincipal.catalogo_fallas t2 WHERE t2.ID=t1.falla_id LIMIT 1) AS Falla,
                    (SELECT Causa FROM $dbPrincipal.catalogo_causas t2 WHERE t2.ID=t1.causa_falla_id LIMIT 1) AS Causa,
                    (SELECT Nombre FROM $db.equipos_componentes t2 WHERE t2.ID=t1.componente_id LIMIT 1) AS Nombre,
                    (SELECT Marca FROM $db.equipos_componentes t2 WHERE t2.ID=t1.componente_id LIMIT 1) AS Marca,
                    (SELECT NumeroSerie FROM $db.equipos_componentes t2 WHERE t2.ID=t1.componente_id LIMIT 1) AS NumeroSerie 
                    
                     FROM $db.ordenes_trabajo_fallas t1 
                    
                    WHERE t1.orden_trabajo_id='$orden_trabajo_id'  
                    
                    ";
                        
            $Consulta=$obCon->Query($sql);
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("Falla agregada a esta OT", 6,"C");
                    
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(14);
                    
                    $css->ColTabla("Componente", 1);
                    $css->ColTabla("Marca", 1);
                    $css->ColTabla("NumeroSerie", 1);
                    $css->ColTabla("Falla", 1);
                    $css->ColTabla("Causa", 1);
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $css->FilaTabla(14);
                        $css->ColTabla($DatosConsulta["Nombre"], 1);
                        $css->ColTabla($DatosConsulta["Marca"], 1);
                        $css->ColTabla($DatosConsulta["NumeroSerie"], 1);
                        $css->ColTabla($DatosConsulta["Falla"], 1);
                        $css->ColTabla($DatosConsulta["Causa"], 1);
                        
                        print('<td>');
                            print('<li class="far fa-times-circle" style="color:red;font-size:25px;cursor:pointer" onclick=eliminarItem(`3`,`'.$idItem.'`)></li>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
                
            $css->CerrarTabla();
        break;// fin caso 7
        
        case 8: //Dibuja las verificaciones realizadas a una maquina
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Maquina", 1);
                    $css->ColTabla("Horas de Trabajo", 1);
                    $css->ColTabla("Kilometros de trabajo", 1);
                    $css->ColTabla("Eliminar", 1);
                $css->CierraFilaTabla();
                
                $sql="SELECT t2.*,t1.ID as verificacion_id,t1.horas_trabajo, t1.kilometros_trabajo, 
                        (SELECT NombreSeccion FROM catalogo_secciones t3 WHERE t3.ID=t2.ubicacion_id LIMIT 1) as Ubicacion 
                        FROM ordenes_trabajo_maquinas_verificadas t1 INNER JOIN equipos_maquinas t2 ON t1.maquina_id=t2.ID 
                        WHERE t1.orden_trabajo_id='$orden_trabajo_id' 
                            ";
                $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["verificacion_id"];
                    $Nombre=$DatosConsulta["Nombre"]." || Marca: <strong>".$DatosConsulta["Marca"]."</strong> || Ubicación: ".$DatosConsulta["Ubicacion"];
                    $css->FilaTabla(14);
                
                        $css->ColTabla($DatosConsulta["verificacion_id"], 1);
                        $css->ColTabla($Nombre, 1);
                        $css->ColTabla($DatosConsulta["horas_trabajo"], 1);
                        $css->ColTabla($DatosConsulta["kilometros_trabajo"], 1);
                        print('<td style="text-align:center;color:red;font-size:18px;">');
                            print('<li class="far fa-times-circle" style="cursor:pointer;" onclick="eliminarItem(`4`,`'.$idItem.'`)"></li>');
                        print('</td>');
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 8
        
        case 9: //Dibuja los adjuntos en una orden de trabajo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["orden_trabajo_id"]);
            $idModal=$obCon->normalizar($_REQUEST["idModal"]);
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            $DatosOT=$obCon->DevuelveValores("$db.ordenes_trabajo", "orden_trabajo_id", $orden_trabajo_id);
            $css->CrearTabla();
                $css->FilaTabla(16);
                
                    $css->ColTabla("Adjuntos de la OT ".$DatosOT["ID"], 4,"C");
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    //$css->ColTabla("Link", 1);   
                    if($idModal==""){
                        $css->ColTabla("Eliminar", 1);
                    }
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM ordenes_trabajo_adjuntos t1 
                        WHERE t1.orden_trabajo_id='$orden_trabajo_id' 
                            ";
                $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $Nombre=$DatosConsulta["NombreArchivo"];
                    $css->FilaTabla(14);
                
                        $css->ColTabla($idItem, 1);
                        //$css->ColTabla($Nombre, 1);
                        print('<td style="text-align:center;color:blue;font-size:18px;">');
                            $Ruta= "../../".str_replace("../", "", $DatosConsulta["Ruta"]);
                            print('<a href="'.$Ruta.'" target="blank">'.$Nombre.' <li class="fa fa-paperclip"></li></a>');
                        print('</td>');
                        if($idModal==""){
                            print('<td style="text-align:center;color:red;font-size:18px;">');
                                print('<li class="far fa-times-circle" style="cursor:pointer;" onclick="eliminarItem(`5`,`'.$idItem.'`)"></li>');
                            print('</td>');
                        }    
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 9
                     
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>