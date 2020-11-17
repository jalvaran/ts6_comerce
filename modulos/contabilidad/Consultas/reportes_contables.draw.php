<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

include_once("../clases/html_reportes_contables.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    $obHtml = new html_reportes_contables();
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo los formularios para los diferentes reportes
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $reporte_id=$obCon->normalizar($_REQUEST["reporte_id"]);
            if($reporte_id==1){
                $html=$obHtml->opciones_auxiliares_contables_html($empresa_id);                
            }
            print($html);
        break; //Fin caso 1
        
        
        
             
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>