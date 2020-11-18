<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/export_reportes_contables.class.php");
include_once("../clases/reportes_contables.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $obExport = new ExportReportes($idUser);
    $obCon = new Contabilidad($idUser);
    switch ($_REQUEST["Accion"]) {
        
        case 1: //exportar el balance de comprobacion
            $Opciones=$obCon->normalizar($_REQUEST["Opciones"]);
            $Encabezado=$obCon->normalizar($_REQUEST["Encabezado"]);
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $obExport->ExportarBalanceXTercerosAExcel($db,$Opciones,$Encabezado);
            print("OKBXT");
           
        break; //fin caso 1
    
                
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>