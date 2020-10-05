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
$idSub2=$obRest->normalizar($_REQUEST['idSub2']);

$sql = "SELECT * FROM prod_sub3 
		WHERE NombreSub3 LIKE '%$key%' AND idSub2='$idSub2'
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['NombreSub3']." ".$row['idSub3'];
     $json[] = ['id'=>$row['idSub3'], 'text'=>$Texto];
}
echo json_encode($json);