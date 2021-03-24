<?php

include_once("../../../modelo/php_conexion.php");
session_start();
$idUser=$_SESSION['idUser'];
if($idUser==''){
    $json[0]['id']="";
    $json[0]['text']="Debe iniciar sesion para realizar la busqueda";
    echo json_encode($json);
    exit();
}
$obRest=new conexion($idUser);
$key=$obRest->normalizar($_REQUEST['q']);
$idProyecto=$obRest->normalizar($_REQUEST['idProyecto']);

$sql = "SELECT * FROM tickets_modulos_proyectos 
		WHERE NombreModulo LIKE '%$key%' AND idProyecto='$idProyecto'
		ORDER BY ID LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= utf8_encode($row['NombreModulo']);
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);