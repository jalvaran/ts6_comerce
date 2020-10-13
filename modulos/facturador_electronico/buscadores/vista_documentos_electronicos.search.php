<?php

include_once("../clases/facturador.class.php");
@session_start();
$idUser=$_SESSION['idUser'];
if($idUser==''){
    $json[0]['id']="";
    $json[0]['text']="Debe iniciar sesion para realizar la busqueda";
    echo json_encode($json);
    exit();
}
$obRest=new Facturador($idUser);

$key=$obRest->normalizar($_REQUEST['q']);
$empresa_id=$obRest->normalizar($_REQUEST['empresa_id']);
$datos_empresa=$obRest->DevuelveValores("empresapro", "ID", $empresa_id);
$db=$datos_empresa["db"];
$obRest->crear_vista_documentos_electronicos($db);
$sql = "SELECT * FROM $db.vista_documentos_electronicos  
		WHERE is_valid=1 and tipo_documento_id=1 and (numero='$key' or fecha like '$key%' or nombre_tercero LIKE '%$key%' or nit_tercero LIKE '%$key%' )  
		LIMIT 50"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto= ($row['fecha']." || ".$row['prefijo']." || ".$row['numero']." || ".$row['nombre_tercero']." || ".$row['nit_tercero']);
     $json[] = ['id'=>$row['documento_electronico_id'], 'text'=>$Texto];
}
echo json_encode($json);