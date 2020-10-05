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
$idSub4=$obRest->normalizar($_REQUEST['idSub4']);

$sql = "SELECT * FROM prod_sub6 
		WHERE NombreSub6 LIKE '%$key%' AND idSub5='$idSub4'
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['NombreSub6']." ".$row['idSub6'];
     $json[] = ['id'=>$row['idSub6'], 'text'=>$Texto];
}
echo json_encode($json);