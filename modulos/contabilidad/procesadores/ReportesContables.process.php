<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/Export_ReportesContables.class.php");
include_once("../clases/ReportesContables.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $obExport = new ExportReportes($idUser);
    $obCon = new Contabilidad($idUser);
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crea un prestamo a un tercero
            $Opciones=$obCon->normalizar($_REQUEST["Opciones"]);
            $Encabezado=$obCon->normalizar($_REQUEST["Encabezado"]);
            $obExport->ExportarBalanceXTercerosAExcel($Opciones,$Encabezado);
            print("OKBXT");
           
        break; //fin caso 1
    
                
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>