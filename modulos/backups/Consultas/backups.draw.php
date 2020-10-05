<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/backups.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Backups($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo el listado de los backups
            
            $tabla="backups";
            $Limit=20;
            $db=DB;            
            $Page=$obCon->normalizar($_REQUEST["Page"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            if($Page==''){
                $Page=1;
                $NumPage=1;
            }
            
            $BusquedasGenerales=$obCon->normalizar($_REQUEST["BusquedasGenerales"]);
            $fecha_inicial=$obCon->normalizar($_REQUEST["fecha_inicial"]);
            $fecha_final=$obCon->normalizar($_REQUEST["fecha_final"]);
            
            $cmb_estado=$obCon->normalizar($_REQUEST["cmb_estado_backup"]);
            
            
            $Condicion=" WHERE ID>'0' ";
            
            if($BusquedasGenerales<>''){
                $Condicion.=" AND ( t1.nombre like '%$BusquedasGenerales%' )";
            }
            
            if($fecha_inicial<>''){
                $Condicion.=" AND t1.fecha_creacion >= '$fecha_inicial' ";
            }
            
            if($fecha_final<>''){
                $Condicion.=" AND t1.fecha_creacion <= '$fecha_final' ";
            }
                        
            if($cmb_estado<>''){
                $Condicion.=" AND t1.estado = '$cmb_estado' ";
            }
            
            $PuntoInicio = ($Page * $Limit) - $Limit;
            
            $sql = "SELECT COUNT(ID) as Items 
                   FROM $tabla t1 $Condicion;";
            
            $Consulta=$obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
            
            $totales = $obCon->FetchAssoc($Consulta);
            $ResultadosTotales = $totales['Items'];
                        
            $sql="SELECT t1.*,
                (SELECT nombre FROM backups_servidores t2 WHERE t2.ID=t1.servidor_id_origen LIMIT 1) as nombre_servidor_id_origen,
                (SELECT nombre FROM backups_servidores t2 WHERE t2.ID=t1.servidor_id_destino LIMIT 1) as nombre_servidor_id_destino
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
                                                    <h5 class="title">Backups</h5>
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
                                        
                                        <th>Editar</th>
                                        <th>DataBases</th>
                                        <th>Copiar</th>
                                        <th>Respaldar</th>
                                        <th>ID</th>
                                        <th>Fecha Registro</th>
                                        <th>Fecha Finalizacion</th>
                                        <th>Servidor Origen</th>
                                        <th>Servidor Destino</th>
                                        <th>Prefijo Origen</th>                                        
                                        <th>Prefijo Destino</th>
                                        <th>Limite de Registros</th>                                        
                                        <th>Estado</th>
                                        
                                    </tr>
                                </thead>');
                        print('<tbody>');
                            while($RegistrosTabla=$obCon->FetchAssoc($Consulta)){
                                
                                $idItem=$RegistrosTabla["ID"];
                                
                                print('<tr>');
                                    
                                    
                                    print("<td style='text-align:center'>");
                                        if($RegistrosTabla["estado"]<10){
                                            print('<a style="font-size:25px;text-align:center" title="Editar Orden" onclick="frm_crear_editar_registro(`'.$idItem.'`)" ><i class="icon-pencil text-info"></i></a>');
                                        }                                       
                                    print("</td >");
                                    print("<td style='text-align:center'>");
                                        if($RegistrosTabla["estado"]<1){
                                            print('<button style="cursor:pointer" id="btn_get_database_'.$idItem.'" onclick="obtener_databases(`'.$idItem.'`)" title="Obtener bases de datos" style="text-align:center" ><i style="font-size:25px;color:red" class="fa fa-database"></i></button>');
                                        }
                                    print("</td>");
                                    
                                    print("<td style='text-align:center'>");
                                        if($RegistrosTabla["estado"]<10){
                                            print('<button onclick="dibuja_frm_copia_bd(`'.$idItem.'`)" title="Iniciar la copia de las bases de datos" style="text-align:center" ><i style="font-size:25px;color:red" class="far fa-copy"></i></button>');
                                        }
                                    print("</td>");
                                    print("<td style='text-align:center'>");
                                        if($RegistrosTabla["estado"]<10){
                                            print('<a onclick="inicia_respaldo_databases(`'.$idItem.'`)" title="Respaldar este backup" style="text-align:center" ><i style="font-size:25px;color:red" class="fa fa-paste"></i></a>');
                                        }
                                    print("</td>");
                                    print("<td class='mailbox-name'>");
                                        print($RegistrosTabla["ID"]);
                                    print("</td>");
                                    print("<td class='mailbox-subject text-primary'>");
                                        print("<strong>".$RegistrosTabla["fecha_creacion"]."</strong>");
                                    print("</td>");
                                    
                                    print("<td class='mailbox-subject text-primary'>");
                                        print("<strong>".$RegistrosTabla["fecha_finalizacion"]."</strong>");
                                    print("</td>");
                                    
                                   
                                    print("<td class='mailbox-subject'>");
                                        print($RegistrosTabla["nombre_servidor_id_origen"]);
                                    print("</td>");    
                                    print("<td class='mailbox-subject'>");
                                        print($RegistrosTabla["nombre_servidor_id_destino"]);
                                    print("</td>");    
                                    print("<td class='mailbox-subject'>");
                                        print($RegistrosTabla["prefijo_origen"]);
                                    print("</td>");    
                                    print("<td class='mailbox-subject'>");
                                        print($RegistrosTabla["prefijo_destino"]);
                                    print("</td>");    
                                    print("<td class='mailbox-subject text-success'>");
                                        print(" <strong>".$RegistrosTabla["limite_registros"]."</strong>");
                                    print("</td>");
                                    
                                    print("<td class='mailbox-subject text-flickr'>");
                                        print($RegistrosTabla["estado"]);
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
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            $edit_id=$obCon->normalizar($_REQUEST["edit_id"]);
                
            $css->frm_form("frm_backups", "Backups", "backups", $edit_id, "");
        break; //Fin caso 2
    
        case 3: //Dibuja el layout donde se mostrará el progreso de la copia
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $sql="SELECT t1.*,(SELECT nombre FROM backups_servidores t2 WHERE t2.ID=t1.servidor_id_origen LIMIT 1) as nombre_servidor_id_origen,
                (SELECT nombre FROM backups_servidores t2 WHERE t2.ID=t1.servidor_id_destino LIMIT 1) as nombre_servidor_id_destino
                FROM backups t1 WHERE ID='$backup_id'";
            $datos_backup=$obCon->FetchAssoc($obCon->Query($sql));
            $title="Backup No. $backup_id, desde el servidor: ".$datos_backup["nombre_servidor_id_origen"].", hacia el servidor: ".$datos_backup["nombre_servidor_id_destino"];    
            print('<div class="row">');
                
                print('<div class="col-12">');
                    print('<div class="panel panel-default">');
                        print('<div class="panel-head">');
                            print('<div class="panel-title">');
                                print('<span class="panel-title-text">'.$title.'</span>');
                            print('</div>');
                            
                            print('<div class="panel-action">');
                                print('<button id="btn_copia_backup" data-backup_id="'.$backup_id.'" onclick="inicia_backup(`'.$backup_id.'`)" class="btn btn-success btn-gradient btn-shadow btn-pill"><i class="icon-plus mr-2"></i> Iniciar Copia</button>');
                            print('</div>');
                        print('</div>');
                        
                        print('<div class="panel-body">');                            
                            print('<div class="row widget-separator-1 mb-3">');
                                
                                print('<div class="col-md-4">');
                                    print('<div class="widget-1">');
                                        print('<div class="content">');
                                            print('<div class="row align-items-center">');
                                                print('<div class="col">');
                                                    print('<div id="div_status_name_database">');
                                                    
                                                        print('<h5 id="name_database" class="title">Esperando inicio...</h5>
                                                                <span id="mensajes_databases" class="descr">Esperando datos...</span>');
                                                        print('<div class="progress mt-3">
                                                                    <div id="bar_status_database" class="progress-bar progress-bar-info progress-bar-striped progress-bar-animated" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                                                </div>');
                                                    print('</div>');
                                                print('</div>');
                                            print('</div>');
                                        print('</div>');
                                    print('</div>');
                                print('</div>');
                                
                                print('<div class="col-md-4">');
                                    print('<div class="widget-1">');
                                        print('<div class="content">');
                                            print('<div class="row align-items-center">');
                                                print('<div class="col">');
                                                    print('<div id="div_status_tables_database">');
                                                    
                                                        print('<h5 id="name_table" class="title">Esperando inicio...</h5>
                                                                <span id="mensajes_tables_databases" class="descr">Esperando datos...</span>');
                                                        print('<div class="progress mt-3">
                                                                    <div id="bar_status_table_database" class="progress-bar progress-bar-info progress-bar-striped progress-bar-animated" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                                                </div>');
                                                    print('</div>');
                                                print('</div>');
                                            print('</div>');
                                        print('</div>');
                                    print('</div>');
                                print('</div>');
                                
                                print('<div class="col-md-4">');
                                    print('<div class="widget-1">');
                                        print('<div class="content">');
                                            print('<div class="row align-items-center">');
                                                print('<div class="col">');
                                                    print('<div id="div_status_mensajes">');
                                                    
                                                        
                                                    print('</div>');
                                                print('</div>');
                                            print('</div>');
                                        print('</div>');
                                    print('</div>');
                                print('</div>');
                                
                            print('</div>');
                            print('<div id="div_table_databases">');
                                
                            print('</div>');
                        print('</div>');
                        
                        
                        
                    print('</div>'); 
                print('</div>');
            print('</div>');
            
           
            
        break; //Fin caso 3
    
        case 4://Tabla con las bases de datos a copiar
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            
            $sql="SELECT t1.*, (SELECT estado_basedatos FROM backups_bases_datos_estados t2 WHERE t2.ID=t1.estado LIMIT 1) as nombre_estado 
                    FROM backups_bases_datos t1 WHERE backups_id='$backup_id' ORDER BY ID,estado ASC";
            
            print('<table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID del Backup</th>
                            <th>Base de datos</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th>Finaliza</th>
                        </tr>
                    </thead>
                    <tbody>');
            $Consulta=$obCon->Query($sql);
            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                
                print('<tr>');
                    print('<td class="text-info">');
                        print($datos_consulta["ID"]);
                    print('</td>');
                    print('<td class="text-primary">');
                        print($datos_consulta["backups_id"]);
                    print('</td>');
                    print('<td class="text-success">');
                        print($datos_consulta["nombre_base_datos"]);
                    print('</td>');
                    
                    print('<td class="text-flick">');
                        print($datos_consulta["nombre_estado"]);
                    print('</td>');
                    print('<td class="text-success">');
                        print($datos_consulta["fecha_registro"]);
                    print('</td>');
                    print('<td class="text-dribbble">');
                        print($datos_consulta["fecha_finalizacion"]);
                    print('</td>');
                print('</tr>');
           
                      
            }
            print('</tbody>
                </table>');
            
        break;//Fin caso 4
                     
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>