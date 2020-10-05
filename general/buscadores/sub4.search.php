<?php

include_once("../../modelo/php_conexion.php");
session_start();
$idUser=$_SESSION['idUser'];
if($idUser==''){
    $json[0]['id']="";
    $json[0]['text']="Debe iniciar sesion para realizar la busqueda";
    echo json_encode($json);
    exit();
}
$obRest=new ProcesoVenta($idUser);
$key=$obRest->normalizar($_REQUEST['q']);
$idSub3=$obRest->normalizar($_REQUEST['idSub3']);

$sql = "SELECT * FROM prod_sub4 
		WHERE NombreSub4 LIKE '%$key%' AND idSub3='$idSub3'
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['NombreSub4']." ".$row['idSub4'];
     $json[] = ['id'=>$row['idSub4'], 'text'=>$Texto];
}
echo json_encode($json);