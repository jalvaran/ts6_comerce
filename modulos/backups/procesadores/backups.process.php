<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
function validateDate($date, $format = 'Y-m-d H:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

include_once("../clases/backups.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new Backups($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear o editar un backup
            
            $DatosFormulario["nombre"]=$obCon->normalizar($_REQUEST["nombre"]);
            
            $DatosFormulario["servidor_id_origen"]=$obCon->normalizar($_REQUEST["servidor_id_origen"]);
            $DatosFormulario["servidor_id_destino"]=$obCon->normalizar($_REQUEST["servidor_id_destino"]);
            $DatosFormulario["prefijo_origen"]=$obCon->normalizar($_REQUEST["prefijo_origen"]);
            $DatosFormulario["prefijo_destino"]=$obCon->normalizar($_REQUEST["prefijo_destino"]);
            $DatosFormulario["limite_registros"]=$obCon->normalizar($_REQUEST["limite_registros"]);
                        
            $edit_id=$obCon->normalizar($_REQUEST["edit_id"]); 
            
            foreach ($DatosFormulario as $key => $value) {
                
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
            
            if(!is_numeric($DatosFormulario["limite_registros"]) or $DatosFormulario["limite_registros"]<0){
                exit("E1;El campo Limite de registros debe ser un numero mayor o igual a cero;$key");
            }
            
            $DatosFormulario["fecha_creacion"]=date("Y-m-d H:i:s");
            
            $DatosFormulario["estado"]=0;
            $db=DB;
            if($edit_id==""){
                $sql=$obCon->getSQLInsert("backups", $DatosFormulario); 
            }else{
                $sql=$obCon->getSQLUpdate("backups", $DatosFormulario);
                $sql.=" WHERE ID='$edit_id'";
            }
            $obCon->QueryExterno($sql, HOST, USER, PW, $db, "");
            print("OK;Datos Guardados");
            
        break;//Fin caso 1
        
        case 2: //Obtener las bases de datos del servidor a copiar
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            $prefijo_origen=$datos_backup["prefijo_origen"]."_%";
            $datos_servidor_origen=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_origen"]);
            $obCon->BorraReg("backups_bases_datos", "backups_id", $backup_id);
            $sql="show databases like '$prefijo_origen'";
            
            $Consulta=$obCon->QueryExterno($sql, $datos_servidor_origen["direccion"], $datos_servidor_origen["usuario"], $datos_servidor_origen["contrasena"], $datos_servidor_origen["basedatos"], "");
            while($datos_consulta=$obCon->FetchArray($Consulta)){
                $obCon->backup_registra_database($backup_id, $datos_consulta[0]);
            }
            $obCon->update("backups", "estado", 1, "WHERE ID='$backup_id'");
            print("OK;Bases de datos Guardadas");
            
        break;//Fin caso 2
             
        case 3: //Obtener la base de datos de donde se necesita crear tablas
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $sql="SELECT * FROM backups_bases_datos WHERE backups_id='$backup_id' AND estado=0 ORDER BY ID,estado ASC LIMIT 1";
            $datos_database=$obCon->FetchAssoc($obCon->Query($sql));
            
            $sql="SELECT COUNT(*) as total_bases FROM backups_bases_datos WHERE backups_id='$backup_id'  LIMIT 1";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $bases_totales=$totales["total_bases"];
            $sql="SELECT COUNT(*) as total_bases FROM backups_bases_datos WHERE backups_id='$backup_id' AND estado=3 LIMIT 1";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $bases_copiadas=$totales["total_bases"];
            
            $porcentaje=round((100/$bases_totales)*$bases_copiadas);
            if($datos_database["ID"]>0){
                print("OK;$bases_copiadas de $bases_totales Bases de datos copiadas;$datos_database[nombre_base_datos];$datos_database[ID];$porcentaje");
            }else{
                print("FIN;Se han copiado todas las base de datos");
            }
            
        break;//Fin caso 3
        
        case 4: //registrar las tablas que tiene una base de datos con su estructura
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $base_datos_id=$obCon->normalizar($_REQUEST["database_id"]);
            $datos_database=$obCon->DevuelveValores("backups_bases_datos", "ID", $base_datos_id);
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            $prefijo_origen=$datos_backup["prefijo_origen"]."_%";
            $datos_servidor_origen=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_origen"]);
            $obCon->BorraReg("backups_bases_datos_tablas", "base_datos_id", $base_datos_id);
            $sql="SHOW FULL TABLES WHERE Table_type='BASE TABLE'";
            $Consulta=$obCon->QueryExterno($sql, $datos_servidor_origen["direccion"], $datos_servidor_origen["usuario"], $datos_servidor_origen["contrasena"], $datos_database["nombre_base_datos"], "");
            while($datos_consulta=$obCon->FetchArray($Consulta)){                
                
                $obCon->backup_registra_tablas_database($base_datos_id, $datos_consulta[0],$datos_consulta['Table_type']);
            }
            print("OK;Tablas de la base de datos $datos_database[nombre_base_datos], registradas;$datos_database[nombre_base_datos];$backup_id;$base_datos_id");
            
        break;//Fin caso 4
        
        case 5: //obtener el total de tablas de una base de datos y la creo si no existe
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $base_datos_id=$obCon->normalizar($_REQUEST["database_id"]);
            $datos_database=$obCon->DevuelveValores("backups_bases_datos", "ID", $base_datos_id);
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            $datos_servidor_destino=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_destino"]);            
            $prefijo_destino=$datos_backup["prefijo_destino"];
            $nombre_database=$prefijo_destino.$datos_database["nombre_base_datos"];
            $sql="CREATE DATABASE IF NOT EXISTS $nombre_database;";
            $obCon->QueryExterno($sql, $datos_servidor_destino["direccion"], $datos_servidor_destino["usuario"], $datos_servidor_destino["contrasena"], $datos_servidor_destino["basedatos"], "");
            
            $sql="SELECT count(*) as total_tablas FROM backups_bases_datos_tablas WHERE base_datos_id='$base_datos_id'";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            print("OK;Se encontraron un total de ". number_format($totales["total_tablas"])." tablas en la base de datos $datos_database[nombre_base_datos];$datos_database[nombre_base_datos];$backup_id;$base_datos_id;$totales[total_tablas]");
            
        break;//Fin caso 5
        
        case 6: //Crear tablas en base de datos destino
            $total_tablas=$obCon->normalizar($_REQUEST["total_tablas"]);
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $base_datos_id=$obCon->normalizar($_REQUEST["database_id"]);
            $datos_database=$obCon->DevuelveValores("backups_bases_datos", "ID", $base_datos_id);
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            $prefijo_origen=$datos_backup["prefijo_origen"];
            $prefijo_destino=$datos_backup["prefijo_destino"];
            $base_datos_destino=  $prefijo_destino.$datos_database["nombre_base_datos"];     
            $datos_servidor_origen=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_origen"]);
            $datos_servidor_destino=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_destino"]);            
            $sql="SELECT ID,nombre_tabla,Table_type FROM backups_bases_datos_tablas WHERE base_datos_id='$base_datos_id' and estado=0 ORDER BY Table_type ASC LIMIT 1 ";
            $datos_tabla=$obCon->FetchAssoc($obCon->Query($sql));
            if($datos_tabla["ID"]>0){
                $idItem=$datos_tabla["ID"];
                $nombre_tabla=$datos_tabla["nombre_tabla"];
                /*
                if($datos_tabla["Table_type"]=='VIEW'){
                    $sql="SHOW CREATE VIEW $nombre_tabla";
                }else{
                    $sql="SHOW CREATE TABLE $nombre_tabla";
                }
                 * 
                 */
                //$sql="DROP TABLE IF EXISTS $nombre_tabla";
                //$obCon->QueryExterno($sql, $datos_servidor_destino["direccion"], $datos_servidor_destino["usuario"], $datos_servidor_destino["contrasena"], $base_datos_destino, "");
                
                $sql="SHOW CREATE TABLE $nombre_tabla";
                
                $Consulta=$obCon->QueryExterno($sql, $datos_servidor_origen["direccion"], $datos_servidor_origen["usuario"], $datos_servidor_origen["contrasena"], $datos_database["nombre_base_datos"], "");
                $datos_consulta=$obCon->FetchArray($Consulta);                
                $estructura=$datos_consulta[1];
                /*
                if($datos_tabla["Table_type"]=='VIEW'){
                    $sql="DROP VIEW IF EXISTS `$nombre_tabla`;";
                    $obCon->QueryExterno($sql, $datos_servidor_destino["direccion"], $datos_servidor_destino["usuario"], $datos_servidor_destino["contrasena"], $base_datos_destino, "");
                
                    $estructura= str_replace("root", $datos_servidor_destino["usuario"], $estructura);
                }else{
                    $estructura= str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $estructura);
                }
                 * 
                 */
                
                $estructura= str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $estructura);
                $obCon->QueryExterno($estructura, $datos_servidor_destino["direccion"], $datos_servidor_destino["usuario"], $datos_servidor_destino["contrasena"], $base_datos_destino, "");
                
                
                //$estructura_base64= base64_encode($estructura);
                $fecha_fin=date("Y-m-d H:i:s");
                $sql="UPDATE backups_bases_datos_tablas SET estado=1,finaliza='$fecha_fin' WHERE ID='$idItem' ";
                $obCon->Query($sql);
                $sql="SELECT COUNT(*) as total_tablas_creadas FROM backups_bases_datos_tablas WHERE base_datos_id='$base_datos_id' and estado=1";
                $totales=$obCon->FetchAssoc($obCon->Query($sql));
                $tablas_creadas=$totales["total_tablas_creadas"];
                $porcentaje=round((100/$total_tablas) * $tablas_creadas);
                print("OK;$tablas_creadas de $total_tablas tablas creadas;$nombre_tabla;$backup_id;$base_datos_id;$porcentaje");
            
            }else{
               $obCon->ActualizaRegistro("backups_bases_datos", "estado", 1, "ID", $base_datos_id);
               print("FIN;Se crearon todas las tablas de la base de datos $datos_database[nombre_base_datos];$datos_database[nombre_base_datos];$backup_id;$base_datos_id");
            }
            
        break;//Fin caso 6
        
        case 7: //obtener una base de datos para iniciar la copia de los registros
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            
            $sql="SELECT ID,nombre_base_datos FROM backups_bases_datos WHERE estado=1 ORDER BY ID ASC LIMIT 1";
            $datos_database=$obCon->FetchAssoc($obCon->Query($sql));
            $base_datos_id=$datos_database["ID"];
            if($base_datos_id>0){
                print("OK;Se inicia con la copia de los registros de la base de datos: $datos_database[nombre_base_datos];$datos_database[nombre_base_datos];$backup_id;$base_datos_id");
            
            }else{
                print("FIN;No hay bases de datos listas para copiar registros;$backup_id");
            }
            
            
        break;//Fin caso 7
        
        case 8: //obtener una tabla y el total de registros a copiar
            
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $base_datos_id=$obCon->normalizar($_REQUEST["base_datos_id"]);
            $datos_database=$obCon->DevuelveValores("backups_bases_datos", "ID", $base_datos_id);
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            $prefijo_origen=$datos_backup["prefijo_origen"];
            $prefijo_destino=$datos_backup["prefijo_destino"];
            $datos_servidor_origen=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_origen"]);
            $datos_servidor_destino=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_destino"]);
            
            $sql="SELECT * FROM backups_bases_datos_tablas WHERE base_datos_id='$base_datos_id' AND estado='1' LIMIT 1";
            $datos_tabla=$obCon->FetchAssoc($obCon->Query($sql));
            if($datos_tabla["ID"]==''){
                $fecha_hora=date("Y-m-d H:i:s");
                $sql="UPDATE backups_bases_datos SET estado=3,fecha_finalizacion='$fecha_hora' WHERE ID='$base_datos_id'";
                $obCon->Query($sql);
                exit("FIN;No hay tablas por copiar registros");
            }
            $nombre_tabla=$datos_tabla["nombre_tabla"];
            $tabla_id=$datos_tabla["ID"];
            $sql="SELECT COUNT(*) as total_registros FROM $nombre_tabla";
            $Consulta=$obCon->QueryExterno($sql, $datos_servidor_origen["direccion"], $datos_servidor_origen["usuario"], $datos_servidor_origen["contrasena"], $datos_database["nombre_base_datos"], "");
            $totales=$obCon->FetchAssoc($Consulta);
            $Limit=$datos_backup["limite_registros"];
            $ResultadosTotales=$totales["total_registros"];
            $TotalPaginas= ceil($ResultadosTotales/$Limit);
            $set_adicional="";
            if($ResultadosTotales==0){
                $set_adicional=",estado=2";
            }
            $sql="UPDATE backups_bases_datos_tablas SET total_paginas='$TotalPaginas',total_registros='$ResultadosTotales',limite_para_copia='$Limit' $set_adicional WHERE ID='$tabla_id' ";
            $obCon->Query($sql);
            print("OK;Iniciando copia de registros para la tabla: $nombre_tabla;$nombre_tabla;$tabla_id;".$totales["total_registros"]);
            
        break;//Fin caso 8
        
        case 9: //copiar los registros de una tabla de una base de datos a otra
            
            
            $backup_id=$obCon->normalizar($_REQUEST["backup_id"]);
            $base_datos_id=$obCon->normalizar($_REQUEST["base_datos_id"]);
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $datos_database=$obCon->DevuelveValores("backups_bases_datos", "ID", $base_datos_id);
            $datos_backup=$obCon->DevuelveValores("backups", "ID", $backup_id);
            $datos_tabla=$obCon->DevuelveValores("backups_bases_datos_tablas", "ID", $tabla_id);
            $prefijo_origen=$datos_backup["prefijo_origen"];
            $prefijo_destino=$datos_backup["prefijo_destino"];
            $datos_servidor_origen=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_origen"]);
            $datos_servidor_destino=$obCon->DevuelveValores("backups_servidores", "ID", $datos_backup["servidor_id_destino"]);
            $nombre_tabla=$datos_tabla["nombre_tabla"];
            $Limit=$datos_backup["limite_registros"];
            $Page=$datos_tabla["paginas_copiadas"]+1;
            $PuntoInicio = ($Page * $Limit) - $Limit;
            $ResultadosTotales=$datos_tabla["total_registros"];
            $TotalPaginas= $datos_tabla["total_paginas"];
            if($Page>$TotalPaginas){
                $sql="UPDATE backups_bases_datos_tablas SET estado=2 WHERE ID='$tabla_id' ";
                $obCon->Query($sql);
                exit("FIN;No hay registros por respaldar en este tabla;$nombre_tabla");
            }
            
            $sql="SELECT * FROM $nombre_tabla LIMIT $PuntoInicio,$Limit;";
            $Consulta=$obCon->QueryExterno($sql, $datos_servidor_origen["direccion"], $datos_servidor_origen["usuario"], $datos_servidor_origen["contrasena"], $datos_database["nombre_base_datos"], "");
            
            $sql_copy="REPLACE INTO $nombre_tabla VALUES ";
            
            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                $sql_copy.="(";
                foreach ($datos_consulta as $key => $value) {
                    $sql_copy.="'$value',";
                }
                $sql_copy = substr($sql_copy, 0, -1);
                $sql_copy.="),";
            }
            $sql_copy = substr($sql_copy, 0, -1);
            $sql_copy= str_replace("\\", '/', $sql_copy);
            
            $base_datos_destino=$prefijo_destino.$datos_database["nombre_base_datos"];            
            $obCon->QueryExterno($sql_copy, $datos_servidor_destino["direccion"], $datos_servidor_destino["usuario"], $datos_servidor_destino["contrasena"], $base_datos_destino, "");
            $set_adicional="";
            if($Page>=$TotalPaginas){
                $set_adicional=",estado=2";
            }
            
            $sql="UPDATE backups_bases_datos_tablas SET paginas_copiadas='$Page' $set_adicional WHERE ID='$tabla_id' ";
            $obCon->Query($sql);
            
            $porcentaje=round((100/$TotalPaginas)*$Page);
           
            print("OK;$Page de $TotalPaginas Bloques de la tabla $nombre_tabla copiados;$nombre_tabla;$tabla_id;".$porcentaje);
            
        break;//Fin caso 9
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>