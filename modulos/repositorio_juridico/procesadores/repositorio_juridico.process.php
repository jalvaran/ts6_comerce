<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/repositorio_juridico.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new RepositorioJuridico($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //contar los registros de las tablas relacionadas con el modulo de repositorio juridico
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $totales=$obCon->contar_registros_repositorios($db);
            
            print("OK;".$totales["total_temas"].";"
                        .$totales["total_sub_temas"].";"
                        .$totales["total_tipo_documentos"].";"
                        .$totales["total_entidades"]);
            
        break;//Fin caso 1
    
        case 2://Recibir un adjunto para un repositorio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]); 
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $repositorio_id=$obCon->normalizar($_REQUEST["repositorio_id"]);
            
            $Extension="";
            if(!empty($_FILES['adjunto_repositorio']['name'])){
                
                $info = new SplFileInfo($_FILES['adjunto_repositorio']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['adjunto_repositorio']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 38);
                
                $carpeta=$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$empresa_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="RepositorioJuridico/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.=$repositorio_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                opendir($carpeta);
                $idAdjunto=$obCon->getUniqId("ad_rep_");
                $destino=$carpeta.$idAdjunto.".".$Extension;
                
                move_uploaded_file($_FILES['adjunto_repositorio']['tmp_name'],$destino);
                $obCon->RegistreAdjuntoRepositorio($db,$repositorio_id, $destino, $Tamano, $_FILES['adjunto_repositorio']['name'], $Extension, $idUser);
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
        
        case 4://crear o editar un repositorio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_repositorio["repositorio_id"]=$obCon->normalizar($_REQUEST["repositorio_id"]);
            $datos_repositorio["tema_id"]=$obCon->normalizar($_REQUEST["tema_id"]);
            $datos_repositorio["sub_tema_id"]=$obCon->normalizar($_REQUEST["sub_tema_id"]);
            
            $datos_repositorio["fecha"]=$obCon->normalizar($_REQUEST["fecha_documento"]);
            $datos_repositorio["tipo_documento_id"]=$obCon->normalizar($_REQUEST["tipo_documento_id"]);
            $datos_repositorio["numero_documento"]=$obCon->normalizar($_REQUEST["numero_documento"]);
            $datos_repositorio["entidad_id"]=$obCon->normalizar($_REQUEST["entidad_id"]);
            $datos_repositorio["extracto"]=$obCon->normalizar($_REQUEST["extracto"]);
            $datos_repositorio["fuentes_formales"]=$obCon->normalizar($_REQUEST["fuentes_formales"]);
            $datos_repositorio["ano_recopilacion"]=$obCon->normalizar($_REQUEST["ano_recopilacion"]);
            $datos_repositorio["estado"]=$obCon->normalizar($_REQUEST["estado"]);
            
            foreach ($datos_repositorio as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
                
            }
            $datos_repositorio["user_id"]=$idUser;
            
            $obCon->crear_editar_repositorio($db, $datos_repositorio);
            
            print("OK;Datos Guardados");
        break;//Fin caso 4
    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>