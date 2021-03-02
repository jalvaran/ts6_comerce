<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/procesos_juridicos.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new ProcesoJuridico($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //contar los registros de las tablas relacionadas con el modulo de repositorio juridico
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $totales=$obCon->contar_registros_procesos($db);
            
            print("OK;".$totales["total_temas"].";"
                        .$totales["total_sub_temas"].";"
                        .$totales["total_procesos_tipo"].";"
                        .$totales["total_terceros"]);
            
        break;//Fin caso 1
    
        case 2://Recibir un adjunto para un proceso
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]); 
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $acto_id=$obCon->normalizar($_REQUEST["acto_id"]);
            
            $Extension="";
            if(!empty($_FILES['acto_adjunto']['name'])){
                
                $info = new SplFileInfo($_FILES['acto_adjunto']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['acto_adjunto']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta=$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$empresa_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="ProcesosJuridicos/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="ActosAdministrativos/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$acto_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("ad_ap_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['acto_adjunto']['tmp_name'],$destino);
                $obCon->RegistreAdjuntoProcesoJuridico($db,$acto_id, $destino, $Tamano, $_FILES['acto_adjunto']['name'], $Extension, $idUser);
            }else{
                exit("E1;No se recibió el archivo");
            }
            print("OK;Archivo adjuntado");
           
        break;//Fin caso 2
        
        case 3://eliminar un registro
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            if($tabla_id==''){
                exit("E1;No se envio tabla");
            }
            
            if($tabla_id==1){
                $tabla=$db.".procesos_juridicos_acto_admin_adjuntos";
                $DatosAdjunto=$obCon->DevuelveValores("$db.procesos_juridicos_acto_admin_adjuntos", "ID", $item_id);
                if(file_exists($DatosAdjunto["Ruta"])){
                    unlink($DatosAdjunto["Ruta"]);
                }
            }
            
            if($tabla_id==2){
                $tabla=$db.".procesos_juridicos_acto_admin_respuestas_adjuntos";
                $DatosAdjunto=$obCon->DevuelveValores("$db.procesos_juridicos_acto_admin_respuestas_adjuntos", "ID", $item_id);
                if(file_exists($DatosAdjunto["Ruta"])){
                    unlink($DatosAdjunto["Ruta"]);
                }
            }
            
            $obCon->BorraReg($tabla, "ID", $item_id);
            print("OK;Registro Eliminado");
        break;//Fin caso 3
        
        case 4://crear o editar un proceso
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_repositorio["proceso_id"]=$obCon->normalizar($_REQUEST["proceso_id"]);
            $datos_repositorio["tema_id"]=$obCon->normalizar($_REQUEST["tema_id"]);
            $datos_repositorio["subtema_id"]=$obCon->normalizar($_REQUEST["sub_tema_id"]);
            
            $datos_repositorio["tipo_proceso_id"]=$obCon->normalizar($_REQUEST["tipo_proceso_id"]);
            $datos_repositorio["tercero_id"]=$obCon->normalizar($_REQUEST["tercero_id"]);
            $datos_repositorio["usuario_asignado_id"]=$obCon->normalizar($_REQUEST["usuario_asignado_id"]);
            $datos_repositorio["descripcion"]=$obCon->normalizar($_REQUEST["descripcion"]);
            $datos_repositorio["anio_gravable"]=$obCon->normalizar($_REQUEST["anio_gravable"]);
            $datos_repositorio["periodo"]=$obCon->normalizar($_REQUEST["periodo"]);
            $datos_repositorio["estado"]=$obCon->normalizar($_REQUEST["estado"]);
            $datos_repositorio["codigo_dane_municipio"]=$obCon->normalizar($_REQUEST["codigo_dane_municipio"]);
            
            foreach ($datos_repositorio as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
                
            }
            $datos_repositorio["user_id"]=$idUser;
            
            $obCon->crear_editar_proceso($db, $datos_repositorio);
            
            print("OK;Datos Guardados");
        break;//Fin caso 4
        
        case 5://crear o editar un acto administrativo de un proceso
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_repositorio["proceso_id"]=$obCon->normalizar($_REQUEST["proceso_id"]);
            $datos_repositorio["acto_id"]=$obCon->normalizar($_REQUEST["acto_id"]);
            $datos_repositorio["entidad_id"]=$obCon->normalizar($_REQUEST["entidad_id"]);
            
            $datos_repositorio["fecha_acto"]=$obCon->normalizar($_REQUEST["fecha_acto"]);
            $datos_repositorio["fecha_notificacion"]=$obCon->normalizar($_REQUEST["fecha_notificacion"]);
            $datos_repositorio["acto_tipo_id"]=$obCon->normalizar($_REQUEST["acto_tipo_id"]);
            $datos_repositorio["numero_acto"]=$obCon->normalizar($_REQUEST["numero_acto"]);
            $datos_repositorio["observaciones"]=$obCon->normalizar($_REQUEST["observaciones"]);
            
            foreach ($datos_repositorio as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
                
            }
            $datos_repositorio["user_id"]=$idUser;
            
            $datos_acto_admin_tipo=$obCon->DevuelveValores("$db.procesos_juridicos_actos_tipo", "ID", $datos_repositorio["acto_tipo_id"]);
            if($datos_acto_admin_tipo["dias_respuesta"]>0){
                $datos_repositorio["fecha_plazo_atencion"]=$obCon->sume_dias_fecha($datos_repositorio["fecha_notificacion"], $datos_acto_admin_tipo["dias_respuesta"]);
            }else{
                $datos_repositorio["fecha_plazo_atencion"]="0000-00-00";
            }
            
            $datos_repositorio["estado"]=1;
            
            $obCon->crear_editar_acto_administrativo_proceso($db, $datos_repositorio);
            
            print("OK;Datos Guardados");
        break;//Fin caso 5
        
        
        case 6://Recibir un adjunto para una respuesta de un acto administrativo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]); 
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $respuesta_id=$obCon->normalizar($_REQUEST["respuesta_id"]);
            
            $Extension="";
            if(!empty($_FILES['respuesta_adjunto']['name'])){
                
                $info = new SplFileInfo($_FILES['respuesta_adjunto']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['respuesta_adjunto']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta=$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$empresa_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="ProcesosJuridicos/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="ActosAdministrativos/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="Respuestas/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$respuesta_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("ad_re_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['respuesta_adjunto']['tmp_name'],$destino);
                $obCon->RegistreAdjuntoRespuestaActo($db,$respuesta_id, $destino, $Tamano, $_FILES['respuesta_adjunto']['name'], $Extension, $idUser);
            }else{
                exit("E1;No se recibió el archivo");
            }
            print("OK;Archivo adjuntado");
           
        break;//Fin caso 6
        
        case 7://crear o editar la respuesta a un acto administrativo de un proceso
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_repositorio["proceso_id"]=$obCon->normalizar($_REQUEST["proceso_id"]);
            $datos_repositorio["acto_id"]=$obCon->normalizar($_REQUEST["acto_id"]);
           
            $datos_repositorio["respuesta_id"]=$obCon->normalizar($_REQUEST["respuesta_id"]);
            $datos_repositorio["fecha_radicado"]=$obCon->normalizar($_REQUEST["fecha_radicado"]);
            
            $datos_repositorio["acto_tipo_id"]=$obCon->normalizar($_REQUEST["acto_tipo_id"]);
            $datos_repositorio["numero_acto"]=$obCon->normalizar($_REQUEST["numero_acto"]);
            $datos_repositorio["observaciones"]=$obCon->normalizar($_REQUEST["observaciones"]);
            
            foreach ($datos_repositorio as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
                
            }
            $datos_repositorio["user_id"]=$idUser;
               
            $obCon->crear_editar_acto_administrativo_proceso_respuesta($db, $datos_repositorio);
            $acto_id=$datos_repositorio["acto_id"];
            $sql="update $db.procesos_juridicos_actos_administrativos set estado=2 WHERE acto_id='$acto_id'";
            $obCon->Query($sql);
            print("OK;Datos Guardados");
        break;//Fin caso 7
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>