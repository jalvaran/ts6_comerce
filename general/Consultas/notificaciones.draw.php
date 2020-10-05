<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/administrador.class.php");
include_once("../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["idAccion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Administrador($idUser);
    
    switch ($_REQUEST["idAccion"]) {
        case 1: //consulta la cantidad de alertas que hay
            $sql="SELECT COUNT(ID) as NumAlertas FROM alertas WHERE Estado=0";
			$consulta=$obCon->Query($sql);
			$DatosConsulta=$obCon->FetchAssoc($consulta);
			if($DatosConsulta["NumAlertas"]=="" or $DatosConsulta["NumAlertas"]==0){ 
				print("SA");
			}else{
				print($DatosConsulta["NumAlertas"]);
			}
            
        break; //Fin caso 1
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>