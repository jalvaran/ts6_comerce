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
$obRest=new conexion($idUser);
$key=$obRest->normalizar($_REQUEST['q']);
$empresa_id=$obRest->normalizar($_REQUEST['empresa_id']);
$campo_asociado_db=$obRest->normalizar($_REQUEST['campo_asociado_db']);
$tabla_asociada=$obRest->normalizar($_REQUEST['tabla_asociada']);
$campo_asociado=$obRest->normalizar($_REQUEST['campo_asociado']);
$campo_asociado_id=$obRest->normalizar($_REQUEST['campo_asociado_id']);
$datos_empresa=$obRest->DevuelveValores("empresapro", "ID", $empresa_id);
$db=$datos_empresa["db"];
if($campo_asociado_db==''){
    $campo_asociado_db=DB;
}else{
    $campo_asociado_db=$db;
}

$sql = "SELECT * FROM $campo_asociado_db.$tabla_asociada";

$condicion=" WHERE ";
$array_campos_asociados= explode(",", $campo_asociado);
foreach ($array_campos_asociados as $key_campo => $value) {
    $condicion.=" $value like '%$key%' or";
}
$condicion= substr($condicion, 0, -2);
$condicion.=" LIMIT 30";		  
$sql.=$condicion;	

$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto="";
    foreach ($array_campos_asociados as $key => $value) {
        $Texto.=$row[$value]." || ";
    }
    ///$Texto= utf8_encode($Texto);
    $json[] = ['id'=>$row[$campo_asociado_id], 'text'=>$Texto];
}
echo json_encode($json);