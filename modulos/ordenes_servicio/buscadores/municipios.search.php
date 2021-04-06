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

$sql = "SELECT * FROM catalogo_municipios 
		WHERE Nombre LIKE '%$key%' or CodigoDANE = '$key' 
		LIMIT 100"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['Nombre']." || ".$row['Departamento']);
     $json[] = ['id'=>$row['CodigoDANE'], 'text'=>$Texto];
}
echo json_encode($json);