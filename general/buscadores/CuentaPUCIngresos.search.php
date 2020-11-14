<?php

include_once("../../modelo/php_conexion.php");
@session_start();
$idUser=$_SESSION['idUser'];
if($idUser==''){
    $json[0]['id']="";
    $json[0]['text']="Debe iniciar sesion para realizar la busqueda";
    echo json_encode($json);
    exit();
}
$obRest=new ProcesoVenta($idUser);
$key=$obRest->normalizar($_REQUEST['q']);

$sql = "SELECT * FROM subcuentas 
		WHERE (Nombre LIKE '%$key%' or PUC LIKE '$key%') AND LENGTH(PUC)>=6 AND PUC LIKE '4%'
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['Nombre']." ".$row['PUC'];
     $json[] = ['id'=>$row['PUC'], 'text'=>$Texto];
}
echo json_encode($json);