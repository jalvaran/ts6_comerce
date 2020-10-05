<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/administrador.class.php");

if( !empty($_REQUEST["idAccion"]) ){
    
    $obCon = new Administrador($idUser);
    
    switch ($_REQUEST["idAccion"]) {
        
        case 1: //insertar datos en una tabla
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);            
            $Columnas=$obCon->getColumnasVisibles($Tabla, ""); 
            foreach($Columnas["Field"] as $key => $value) {
                if($key>0){
                    $Datos[$value]=$obCon->normalizar($_REQUEST["$value"]);   
                    if($value=="Password"){
                        $Datos[$value]= md5($obCon->normalizar($_REQUEST["$value"]));
                    }
                    
                }
            }
            
            $sql=$obCon->getSQLInsert($Tabla, $Datos);
            $obCon->Query($sql);
            if($Tabla=="clientes"){
                $sql=$obCon->getSQLInsert("proveedores", $Datos);
                $obCon->Query($sql);
            }
            if($Tabla=="proveedores"){
                $sql=$obCon->getSQLInsert("clientes", $Datos);
                $obCon->Query($sql);
            }
            print("OK");
            
        break; 
        
        case 2: //editar datos en una tabla
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);  
            $idEditar=$obCon->normalizar($_REQUEST["idEditar"]); 
            $Columnas=$obCon->getColumnasVisibles($Tabla, ""); 
            $DatosActuales=$obCon->DevuelveValores($Tabla, $Columnas["Field"][0], $idEditar);
            foreach($Columnas["Field"] as $key => $value) {
                if($key>0){
                    $ValorEditado=$obCon->normalizar($_REQUEST["$value"]);     
                    if($DatosActuales[$key]<>$ValorEditado){
                        if($value=="Password"){
                            $ValorEditado= md5($obCon->normalizar($_REQUEST["$value"]));
                        }
                        $obCon->ActualizaRegistro($Tabla, $value, $ValorEditado, $Columnas["Field"][0], $idEditar,0); 
                    }                  
                    
                }
            }
            
            print("OK");
            
        break; 
         
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>