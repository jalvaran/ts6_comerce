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
$empresa_id=$obRest->normalizar($_REQUEST['empresa_id']);
$datos_empresa=$obRest->DevuelveValores("empresapro", "ID", $empresa_id);
$db=$datos_empresa["db"];
$sql = "SELECT * FROM $db.terceros 
		WHERE razon_social LIKE '%$key%' or identificacion LIKE '%$key%' 
		LIMIT 30"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['razon_social']." || ".$row['identificacion']." || ".$row['telefono']." || ".$row['direccion']);
     $json[] = ['id'=>$row['identificacion'], 'text'=>$Texto];
}
echo json_encode($json);