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
$Departamento=$obRest->normalizar($_REQUEST['idDepartamento']);

$sql = "SELECT * FROM prod_sub1 
		WHERE NombreSub1 LIKE '%$key%' AND idDepartamento='$Departamento'
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['NombreSub1']." ".$row['idSub1'];
     $json[] = ['id'=>$row['idSub1'], 'text'=>$Texto];
}
echo json_encode($json);