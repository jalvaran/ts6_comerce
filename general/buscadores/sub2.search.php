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
$idSub1=$obRest->normalizar($_REQUEST['idSub1']);

$sql = "SELECT * FROM prod_sub2 
		WHERE NombreSub2 LIKE '%$key%' AND idSub1='$idSub1'
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['NombreSub2']." ".$row['idSub2'];
     $json[] = ['id'=>$row['idSub2'], 'text'=>$Texto];
}
echo json_encode($json);