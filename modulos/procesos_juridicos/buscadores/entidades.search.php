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
$sql = "SELECT * FROM $db.repositorio_juridico_entidades 
		WHERE nombre_entidad LIKE '%$key%' or nit LIKE '$key' 
		LIMIT 30"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['nombre_entidad']." || ".$row['nit']);
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);