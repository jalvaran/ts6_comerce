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
$condicional="";
if(isset($_REQUEST['tipo_insumo'])){
    $tipo_insumo=$obRest->normalizar($_REQUEST['tipo_insumo']);
    $condicional=" AND tipo_insumo='$tipo_insumo'";
}
$db=$datos_empresa["db"];
$sql = "SELECT * FROM $db.ordenes_servicio_catalogo_insumos 
		WHERE nombre LIKE '%$key%' or ID = '$key' $condicional 
		LIMIT 100"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['ID']." || ".$row['nombre']);
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);