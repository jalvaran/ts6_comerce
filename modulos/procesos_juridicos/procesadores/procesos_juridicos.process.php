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
            $proceso_id=$obCon->normalizar($_REQUEST["proceso_id"]);
            
            $Extension="";
            if(!empty($_FILES['adjunto_proceso']['name'])){
                
                $info = new SplFileInfo($_FILES['adjunto_proceso']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['adjunto_proceso']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta=$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$empresa_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="ProcesoJuridico/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$repositorio_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("ad_pro_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['adjunto_proceso']['tmp_name'],$destino);
                $obCon->RegistreAdjuntoRepositorio($db,$repositorio_id, $destino, $Tamano, $_FILES['adjunto_proceso']['name'], $Extension, $idUser);
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
                $tabla=$db.".repositorio_juridico_adjuntos";
                $DatosAdjunto=$obCon->DevuelveValores("$db.repositorio_juridico_adjuntos", "ID", $item_id);
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
            
            foreach ($datos_repositorio as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
                
            }
            $datos_repositorio["user_id"]=$idUser;
            
            $obCon->crear_editar_proceso($db, $datos_repositorio);
            
            print("OK;Datos Guardados");
        break;//Fin caso 4
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>