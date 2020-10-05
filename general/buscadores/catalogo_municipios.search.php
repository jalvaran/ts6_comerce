<?php

include_once("modelo/php_conexion.php");

$obRest=new conexion(1);
$key=$obRest->normalizar($_REQUEST['q']);

$sql = "SELECT * FROM catalogo_municipios WHERE Nombre like '%$key%' or CodigoDANE like '%$key%' LIMIT 100"; 

$result = $obRest->Query($sql);

$json = [];

while($row = $obRest->FetchAssoc($result)){
    
    $Texto= utf8_encode($row['Nombre']." || ".$row['Departamento']." || ".$row['CodigoDANE']);
    $json[] = ['id'=>$row['CodigoDANE'], 'text'=>$Texto];
}
echo json_encode($json);