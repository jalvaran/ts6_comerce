<?php

include_once("../../../modelo/php_conexion.php");
@session_start();
$idUser=$_SESSION['idUser'];
if($idUser==''){
    $json[0]['id']="";
    $json[0]['text']="Debe iniciar sesion para realizar la busqueda";
    echo json_encode($json);
    exit();
}
$obRest=new conexion($idUser);
$key=$obRest->normalizar($_REQUEST['q']);

$sql = "SELECT ID,nombre_completo,Identificacion FROM usuarios 
		WHERE nombre_completo LIKE '%$key%' or Identificacion LIKE '%$key%' 
		LIMIT 30"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['nombre_completo']." || ".$row['Identificacion']);
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);