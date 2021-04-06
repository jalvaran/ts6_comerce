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

$sql = "SELECT t1.ID,t1.nombre_completo,t1.Identificacion FROM usuarios t1 
                INNER JOIN usuarios_rel_empresas t2 ON t2.usuario_id_relacion=t1.ID 
               
		WHERE t1.Habilitado='SI' and t2.empresa_id='$empresa_id' and (t1.nombre_completo LIKE '%$key%' or t1.Identificacion LIKE '%$key%') 
		LIMIT 30"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['nombre_completo']." || ".$row['Identificacion']);
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);