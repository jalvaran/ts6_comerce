<?php 
session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
if(isset($_REQUEST["idDocumento"])){
    
    include_once("../../modelo/php_conexion.php");    
    include_once("../class/ClasesPDFDocumentos.class.php");
    
    $idUser=1;
    $obCon = new conexion($idUser);
    
    $obDoc = new Documento(DB);
    $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
    
    
    switch ($idDocumento){
        case 1: //PDF para una orden de trabajo
            
            $orden_trabajo_id=$obCon->normalizar($_REQUEST["id"]);
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);                  
            $obDoc->orden_trabajo_pdf($empresa_id,$orden_trabajo_id);            
        break;//Fin caso 1
        case 2: //PDF para los indicadores de mantenimiento
            
            $Condicion=($_REQUEST['c']);            
            $Condicion= urldecode(base64_decode($Condicion));
            $rango=($_REQUEST['rango']);            
            $rango= urldecode(base64_decode($rango));
            $empresa_id=$obCon->normalizar($_REQUEST['empresa_id']);
        
            $obDoc->indicadores_mantenimiento_pdf($empresa_id,$Condicion,$rango);        
        break;//Fin caso 2
       
    }
}else{
    print("No se recibió parametro de documento");
}

?>