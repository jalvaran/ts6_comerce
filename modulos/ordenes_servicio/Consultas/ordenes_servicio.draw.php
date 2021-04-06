<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ordenes_servicio.class.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/pdf_ordenes_servicio.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new OrdenesServicio($idUser);
    $sql="SELECT Role from usuarios WHERE ID='$idUser'";
            
    $datos_role=$obCon->FetchAssoc($obCon->Query($sql));
    $Role=$datos_role["Role"];
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja el listado de ordenes de servicio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $TipoUser=$_SESSION["tipouser"];
            
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            
            $Condicional=" WHERE ID>0  ";
            $OrderBy=" ORDER BY ID DESC";            
            
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
            
            if($Busqueda<>''){
                $Condicional.=" AND ( ID='$Busqueda' or tercero_identificacion = '$Busqueda' or tercero_razon_social like '%$Busqueda%' )";

            }
            
            $estado=$obCon->normalizar($_REQUEST["estado"]);               
            if($estado<>''){
                
                $Condicional.=" AND estado='$estado'";
            }
            
            $statement=" $db.`vista_ordenes_servicio` $Condicional ";
            
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 5;
            $startpoint = ($NumPage * $limit) - $limit;
            
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num` FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT * ";
            $Consulta=$obCon->Query("$query FROM $statement $OrderBy $Limit ");
            $TotalPaginas= ceil($ResultadosTotales/$limit);
            
            print('<div class="panel panel-default">
                                <div class="mailbox-container">
                                    <div class="action">
                                                                                
                                        <div class="btn-group pull-center">
                                            
                                            <strong>'.$ResultadosTotales.' Ordenes de Servicio </strong>
                                        
                                        
                                        ');
                                        $sql="SELECT * FROM $db.ordenes_servicio_estados";
                                        $consulta_estado=$obCon->Query($sql);
                                        $css->select("estado", "form-control", "estado", "", "", "", "onchange='listado_ordenes_servicio()'");
                                            $css->option("", "", "","" , "", "");
                                                print("Filtrar por:");
                                            $css->Coption();
                                            while($datos_estados=$obCon->FetchAssoc($consulta_estado)){
                                                $sel=0;
                                                if($datos_estados["ID"]==$estado){
                                                    $sel=1;
                                                }
                                                $css->option("", "", "",$datos_estados["ID"] , "", "",$sel);
                                                    print($datos_estados["estado_orden"]);
                                                $css->Coption();
                                            }
                                        $css->Cselect();     
                                        
                            if($TotalPaginas==0){
                                $TotalPaginas=1;
                            }
                            print('</div><div class="btn-group pull-right" style="top: -10px;">Página '.$NumPage.' de '.$TotalPaginas." ");
                            if($NumPage>1){
                                $goPage=$NumPage-1;  
                                print('<button onclick="listado_ordenes_servicio('.$goPage.')" type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient"><i style="font-size:20px;height:15px;" class="far fa-arrow-alt-circle-left text-flickr"></i></button>');
                            }        
                            if($NumPage<>$TotalPaginas){  
                                $goPage=$NumPage+1;
                                print('<button onclick="listado_ordenes_servicio('.$goPage.')" type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient"><i style="font-size:20px;height:15px;" class="far fa-arrow-alt-circle-right text-success"></i></button>');
                                        
                            }                        
                                                    
                              print('   </div>
                                        
                                    </div>
                                    <div class="body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mail-table">
                                                <thead>
                                                    <tr>
                                                        <td style="text-align:center"><strong>Opciones</strong></td>
                                                        <td style="text-align:center"><strong>Imprimir</strong></td>
                                                        <td style="text-align:center"><strong>ID</strong></td>
                                                        <td style="text-align:center"><strong>Tercero</strong></td>
                                                        <td style="text-align:center"><strong>Dirección</strong></td>
                                                        <td style="text-align:center"><strong>Observaciones Iniciales</strong></td>
                                                        <td style="text-align:center"><strong>Asignado</strong></td>
                                                        <td style="text-align:center"><strong>Estado</strong></td>
                                                        <td style="text-align:center"><strong>Creación</strong></td>
                                                       
                                                    </tr>
                                                </thead>
                                                <tbody>');
                              
                                        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                            $ID=$datos_consulta["ID"];
                                            $orden_servicio_id=$datos_consulta["orden_servicio_id"];
                                            print('<tr>');
                                            print('<td>');
                                                print('<div class="btn-group">');
                                                    print('<button type="button" class="btn btn-outline btn-primary btn-pill btn-outline-1x btn-gradient dropdown-toggle"  data-toggle="dropdown" data-original-title="Opciones"><i class="icon-options-vertical"></i></button>');
                                                    print('<div class="dropdown-menu dropdown-menu-right" x-placement="top-end" style="position: absolute; transform: translate3d(-93px, -189px, 0px); top: 0px; left: 0px; will-change: transform;">');
                                                        if($Role=="ADMINISTRADOR" or $Role=="SUPERVISOR"){
                                                            print('<a class="dropdown-item" onclick="frm_crear_editar_orden_servicio(`'.$orden_servicio_id.'`)"><i class="fa fa-edit"></i> Editar</a>');
                                                            print('<a class="dropdown-item" onclick="entrega_de_materiales(`'.$orden_servicio_id.'`)"><i class="icon-layers"></i> Entregar Materiales</a>');
                                                        }
                                                        
                                                        print('<a class="dropdown-item" onclick="frm_ejecucion_orden(`'.$orden_servicio_id.'`)"><i class="icon-book-open"></i> Ejecutar Orden</a>');
                                                                                                               
                                                        print('<a class="dropdown-item" onclick="frm_cerrar_orden(`'.$orden_servicio_id.'`)"><i class="ti-save"></i> Cerrar Orden</a>');
                                                    print('</div>');
                                                print('</div>');
                                            print('</td>');  
                                            
                                            print('<td>');
                                                print('<div class="btn-group">');
                                                    print('<button type="button" class="btn btn-outline btn-dark btn-pill btn-outline-1x btn-gradient dropdown-toggle"  data-toggle="dropdown" data-original-title="Imprimir"><i class="far fa-file-pdf"></i></button>');
                                                    print('<div class="dropdown-menu dropdown-menu-right" x-placement="top-end" style="position: absolute; transform: translate3d(-93px, -189px, 0px); top: 0px; left: 0px; will-change: transform;">');
                                                        $link="Consultas/pdf_ordenes_servicio.draw.php?empresa_id=$empresa_id&Accion=1&orden_servicio_id=$orden_servicio_id";
                                                        print('<a href="'.$link.'" target="_blank" class="dropdown-item" ><i class="far fa-file"></i> Orden de Servicio</a>');
                                                        $link="Consultas/pdf_ordenes_servicio.draw.php?empresa_id=$empresa_id&Accion=2&orden_servicio_id=$orden_servicio_id";
                                                        print('<a href="'.$link.'" target="_blank" class="dropdown-item" ><i class="ti-share"></i> Entrega de Insumos</a>');
                                                        $link="Consultas/pdf_ordenes_servicio.draw.php?empresa_id=$empresa_id&Accion=3&orden_servicio_id=$orden_servicio_id";
                                                        print('<a href="'.$link.'" target="_blank" class="dropdown-item" ><i class="ti-share-alt"></i> Devolución de Insumos</a>');
                                                    print('</div>');
                                                print('</div>');
                                            print('</td>');
                                                                                      
                                                        
                                            print('     <td class="date text-primary" style="text-align:center"><strong><h5>'.$datos_consulta["ID"].'</h5></strong></td>
                                                        <td>
                                                            <span class="name">'.$datos_consulta["tercero_razon_social"].' '.$datos_consulta["tercero_identificacion"].' </span>
                                                            
                                                        </td>
                                                        
                                                        <td class="name">'.$datos_consulta["direccion"].' || '.$datos_consulta["nombre_municipio"].'</td>                                                      
                                                        <td class="date text-primary">'.$datos_consulta["observaciones_iniciales"].'</td>
                                                        <td class="date text-success">'.$datos_consulta["nombre_usuario_asignado"].'</td>
                                                        <td class="date text-flickr">'.$datos_consulta["nombre_estado"].'</td>
                                                        
                                                        <td class="date">'.$datos_consulta["created"].'</td>
                                                        
                                                    </tr>
                                                    ');
                                            
                                        }    
                              
                                print('         </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>');
            
                        
        break; //Fin caso 1
        
        case 2: //Formulario para crear o editar una orden de servicio
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            
            if($orden_servicio_id==''){                
                $orden_servicio_id=$obCon->getUniqId("os_");
            }
            
            $datos_orden=$obCon->DevuelveValores("$db.ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
            if($datos_orden["estado"]>=3){
                $link_volver='<a onclick="VerListadoSegunID()">Click para Volver <li class="far fa-list-alt"></li></>';
                $css->alerta("Esta orden se encuentra Cerrada, $link_volver", 6,0);
                exit();
            }
            $titulo="Crear una orden de servicio";
            if($datos_orden["ID"]>0){
                $titulo="Editar la orden de servicio No. ".$datos_orden["ID"];
            }
            print('<div class="col-12">
                            <div class="panel panel-default">
                                <div class="panel-head">
                                    <div class="panel-title">
                                        <span class="panel-title-text">'.$titulo.'</span>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    
                                        <div class="form-body">
                                            <div class="form-heading">Datos Generales de la Orden</div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Fecha</label>
                                                        <input id="fecha_orden" name="fecha_orden" value="'.$datos_orden["fecha_orden"].'" type="date" class="form-control" placeholder="Fecha">
                                                        <span class="form-text">Por favor digite la fecha de la Orden</span> 
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Tercero</label>');
                                                        $css->select("tercero_id", "form-control", "tercero_id", "", "", "", "");                                                        
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione un Tercero");
                                                            $css->Coption();
                                                            
                                                            if($datos_orden["tercero_id"]>0){
                                                                $datos_tercero=$obCon->DevuelveValores("$db.terceros", "ID", $datos_orden["tercero_id"]);
                                                                $css->option("", "", "", $datos_tercero["ID"], "", "",1);
                                                                    print($datos_tercero["razon_social"]." ".$datos_tercero["identificacion"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                  print('<span class="form-text">Seleccione un tercero</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Dirección del Servicio</label>
                                                        <input id="direccion" name="direccion" value="'.$datos_orden["direccion"].'" type="text" class="form-control" placeholder="Dirección del servicio">
                                                        <span class="form-text">Por favor digite una dirección</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Ciudad</label>
                                                        <div class="input-group">
                                                            ');
                                                        $css->select("municipio", "form-control", "municipio", "", "", "", "");                                                        
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione una Ciudad");
                                                            $css->Coption();
                                                            
                                                            if($datos_orden["municipio"]>0){
                                                                $datos_municipio=$obCon->DevuelveValores("catalogo_municipios", "CodigoDANE", $datos_orden["municipio"]);
                                                                $css->option("", "", "", $datos_municipio["CodigoDANE"], "", "",1);
                                                                    print($datos_municipio["Nombre"]." || ".$datos_municipio["Departamento"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                  print('</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Asignar a..</label>
                                                        <div class="input-group">
                                                        
                                                ');
                                                        $css->select("usuario_asignado", "form-control", "usuario_asignado", "", "", "", "");                                                        
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione un usuario");
                                                            $css->Coption();
                                                            
                                                            if($datos_orden["usuario_asignado"]>0){
                                                                $datos_usuario=$obCon->DevuelveValores("usuarios", "ID", $datos_orden["usuario_asignado"]);
                                                                $css->option("", "", "",$datos_usuario["ID"], "", "",1);
                                                                    print($datos_usuario["nombre_completo"]." ".$datos_usuario["Identificacion"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                  print('</div>
                                                    </div>
                                                </div>        
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Observaciones Iniciales</label>
                                                        <div class="input-group">
                                                            
                                                            <textarea id="observaciones_iniciales" class="form-control"  name="login_usuario_os">'.$datos_orden["observaciones_iniciales"].'</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                                        
                                        </div>
                                   
                                </div>
                                <div class="panel-footer text-right">
                                    <button class="btn btn-default btn-pill mr-2" onclick=VerListadoSegunID();>Cancelar</button>
                                    <button id="btn_guardar" data-orden_servicio_id="'.$orden_servicio_id.'" class="btn btn-primary m-1" onclick="crear_editar_orden_servicio()">Guardar</button>
                                </div>
                            </div>
                        </div>');
                
                
            
        break;//Fin caso 2
        
        case 3: //vista de una orden de servicio para entrega de materiales
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);            
            $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
            if($datos_orden["estado"]>=3){
                $link_volver='<a onclick="VerListadoSegunID()">Click para Volver <li class="far fa-list-alt"></li></>';
                $css->alerta("Esta orden se encuentra Cerrada, $link_volver", 6,0);
                exit();
            }
            
            $obPDF = new PDF_OrdenServicio($db);
            $html_encabezado=$obPDF->get_general_data($db, $datos_orden);
            
            $html_frm_agregar_item='<div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-head">
                                    <div class="panel-title">
                                        <i class="far fa-share-square panel-head-icon font-24"></i>
                                        <span class="panel-title-text">Entrega de Insumos</span>
                                    </div>
                                    
                                </div>
                                <div class="panel-wrapper">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="fecha_orden_insumos"><strong>Fecha</strong></label>
                                                <input type="date" id="fecha_orden_insumos" class="form-control" value="'.date("Y-m-d").'">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="insumo_id_osal"><strong>Insumo</strong></label>
                                                    <div class="input-group">
                                                        <select id="insumo_id_oi" class="form-control">
                                                            <option value="">Seleccione...</option>
                                                        </select>
                                                    </div>    
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cantidad_agregar">Cantidad</label>
                                                    <div class="input-group">                                                            
                                                        <input id="cantidad_agregar" type="text" value="1" class="form-control" placeholder="Cantidad">
                                                        <div class="input-group-prepend">
                                                            <button onclick="agregar_item_orden_insumo(`'.$orden_servicio_id.'`,`1`)" id="btn_agregar_item" class="input-group-text btn-info text-white" style="font-size:20px;cursor:pointer"><strong>+</strong></button>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>';
            
            print('     <div class="col-md-12">
                            <div class="panel br-40x panel-default">
                                <div class="panel-head">
                                    <div class="panel-title"><i class="icon-layers panel-head-icon text-primary" onclick="entrega_de_materiales(`'.$orden_servicio_id.'`)"></i>
                                        <span class="panel-title-text">Orden de servicio No. '.$datos_orden["ID"].' <small class="text-muted">Entrega de Materiales</small></span>
                                    </div>
                                </div>
                                <div class="panel-wrapper">
                                    <div class="panel-body">
                                        '.$html_encabezado.'
                                        <br><br>
                                        <div class="row">
                                            <div class="col-md-7" >
                                                <div class="alert alert-light" style="text-align:center">
                                                    <strong>Entrega de Insumos</strong> 
                                                </div>
                                                '.$html_frm_agregar_item.'
                                            </div>
                                            <div class="col-md-5">
                                                <div class="alert alert-light" style="text-align:center">
                                                    <strong>Insumos entregados para la Orden de servicio No. '.$datos_orden["ID"].' </strong>
                                                     
                                                </div>
                                                <div class="row">
                                                        <div id="items_orden_insumos" class="col-md-12">

                                                        </div>

                                                    </div>   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6 text-right">
                                            
                                            <button onclick="VerListadoSegunID();"  class="btn btn-primary btn-pill">Volver</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>');
            
        break;//Fin caso 3    
    
        case 4: //listado de insumos en una orden
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);            
            $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
            
            $tipo_registro=$obCon->normalizar($_REQUEST["tipo_registro"]);
            if($tipo_registro<>''){
                $condicion=" AND t1.tipo_registro='$tipo_registro'";
            }
            
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("Fecha", 1);
                    $css->ColTabla("Insumo", 1);
                    $css->ColTabla("Cantidad", 1);
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*,
                        (SELECT t2.nombre FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as nombre_insumo 
                        FROM $db.ordenes_servicio_insumos t1 
                        WHERE t1.orden_servicio_id='$orden_servicio_id' AND deleted='0000-00-00 00:00:00' $condicion ORDER BY t1.ID DESC
                            ";
                $Consulta=$obCon->Query($sql);
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    
                    $css->FilaTabla(14);
                
                        $css->ColTabla($DatosConsulta["fecha"], 1);
                        $css->ColTabla($DatosConsulta["nombre_insumo"], 1);
                        $css->ColTabla($DatosConsulta["cantidad"], 1);
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");
                            if($Role=='SUPERVISOR' or $Role=='ADMINISTRADOR'){
                                $css->li("", "far fa-trash-alt", "", "onclick=EliminarItem(`1`,`$idItem`,`$orden_servicio_id`,`$tipo_registro`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                                $css->Cli();
                            }
                        print("</td>");
                          
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
        break;//Fin caso 4
        
        case 5: //vista de una orden de servicio para ejecucion o gasto insumos y materiales
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);            
            $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
            
            if($datos_orden["estado"]>=3){
                $link_volver='<a onclick="VerListadoSegunID()">Click para Volver <li class="far fa-list-alt"></li></>';
                $css->alerta("Esta orden se encuentra Cerrada, $link_volver", 6,0);
                exit();
            }
            
            $obPDF = new PDF_OrdenServicio($db);
            $html_encabezado=$obPDF->get_general_data($db, $datos_orden);
            
            print('     <div class="col-md-12">
                            <div class="panel br-40x panel-default">
                                <div class="panel-head">
                                    <div class="panel-title"><i class="icon-layers panel-head-icon text-primary" onclick="frm_ejecucion_orden(`'.$orden_servicio_id.'`)"></i>
                                        <span class="panel-title-text">Orden de servicio No. '.$datos_orden["ID"].' <small class="text-muted">Entrega de Materiales</small></span>
                                    </div>
                                </div>
                                <div class="panel-wrapper">
                                    <div class="panel-body">
                                        '.$html_encabezado.'
                                        <br><br>
                                        <div class="row">
                                            <div class="col-md-7" >
                                                <div class="alert alert-light" style="text-align:center">
                                                    <strong>Insumos Disponibles para esta Orden</strong> 
                                                </div>
                                                <div id="items_orden_insumos_disponibles" class="col-md-12">

                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="alert alert-light" style="text-align:center">
                                                    <strong>Insumos utilizados para la Orden de servicio No. '.$datos_orden["ID"].' </strong>
                                                     
                                                </div>
                                                <div class="row">
                                                        <div id="items_orden_insumos" class="col-md-12">

                                                        </div>

                                                    </div>   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6 text-right">
                                            
                                            <button onclick="VerListadoSegunID();"  class="btn btn-primary btn-pill">Volver</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>');
            
        break;//Fin caso 5
    
        case 6: //listado de insumos disponibles en una orden
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);            
            $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("Fecha de Consumo:", 3);
                    print('<td>');
                        print('<input type="date" id="fecha_orden_insumos" class="form-control" value="'.date("Y-m-d").'">');
                    print('</td>');
                $css->CierraFilaTabla();
                                
                $css->FilaTabla(16);
                
                    $css->ColTabla("Insumos o Materiales disponibles para Consumir en esta Orden", 4);
                    
                $css->CierraFilaTabla();
                $css->FilaTabla(16);
                
                    $css->ColTabla("Insumo", 3);                    
                    $css->ColTabla("Cantidad", 1);
                    
                    
                $css->CierraFilaTabla();
                
                $datos_insumos=$obCon->get_insumos_orden($db, $orden_servicio_id);
                
                foreach ($datos_insumos as $insumo_id => $DatosConsulta) {
                    
                    if(isset($DatosConsulta[1])){
                        $css->FilaTabla(14);                
                                $cantidad_disponible=$DatosConsulta[1]["cantidad_disponible"];
                                if($cantidad_disponible==0){
                                    continue;
                                }
                                $css->ColTabla($DatosConsulta[1]["nombre_insumo"], 3);
                                print('<td colspan="1" >');
                                    print('<div class="form-group">

                                                <div class="input-group btn-group">  

                                                    <input id="cantidad_agregar_'.$insumo_id.'" type="number" value="'.$cantidad_disponible.'" class="form-control input-group-prepend" placeholder="Cantidad" >
                                                    <div class="input-group-prepend">
                                                        <button onclick="agregar_item_orden_insumo_consumido(`'.$orden_servicio_id.'`,`2`,`'.$insumo_id.'`)" id="btn_agregar_item" class="input-group-text btn-primary text-white" style="font-size:20px;cursor:pointer"><strong> + </strong></button>
                                                    </div>
                                                </div>
                                            </div>');
                                print('</td>');

                            $css->CierraFilaTabla();
                    }
                                        
                }
               
            $css->CerrarTabla();
            
        break;//Fin caso 6
        
        case 7://formulario para cerrar la orden
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);            
            $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
            
            if($datos_orden["estado"]>=3){
                $link_volver='<a onclick="VerListadoSegunID()">Click para Volver <li class="far fa-list-alt"></li></>';
                $css->alerta("Esta orden se encuentra Cerrada, $link_volver", 6,0);
                exit();
            }
            
            $titulo_orden="Cerrar la orden de Servicio No. ".$datos_orden["ID"];
            print('<div class="col-12">
                            <div class="panel">
                                <div class="panel-head">
                                    <h5 class="panel-title">'.$titulo_orden.'</h5>
                                </div>
                                <div class="panel-body">
                                    <input type="hidden" id="formulario_id" name="formulario_id" value="1">
                                    <input type="hidden" id="orden_servicio_id_cierre" name="formulario_id" value="'.$orden_servicio_id.'">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Fecha de Cierre <i class="far fa-calendar-alt text-danger"></i></label>
                                                    <div class="col-12">
                                                        <input type="date" id="fecha_cierre_os" disabled=true name="fecha_cierre_os" class="form-control" value="'.date("Y-m-d").'">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Observaciones de Cierre <i class="far fa-comment-alt text-danger"></i></label>
                                            <div class="col-12">
                                                <textarea id="observaciones_cierre_orden" name="observaciones_cierre_orden" class="form-control" rows="5"></textarea>
                                            </div>
                                        </div>
                                        
                                   
                                </div>
                                
                            </div>
                        </div>');
            
        break;//Fin caso 7 
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>