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
$key=$obRest->normalizar($_REQUEST['key']);
$tipo_recurso=$obRest->normalizar($_REQUEST['tipo_recurso']);
$empresa_id=$obRest->normalizar($_REQUEST['empresa_id']);
$datos_empresa=$obRest->DevuelveValores("empresapro", "ID", $empresa_id);
$db=$datos_empresa["db"];
$sql = "SELECT t1.*,
                (select tipo_recurso FROM $db.proyectos_recursos_tipo t2 WHERE t1.tipo=t2.ID LIMIT 1) as nombre_tipo_recurso 
                FROM $db.proyectos_recursos t1  
		WHERE tipo='$tipo_recurso' and (nombre_recurso LIKE '%$key%' OR  ID LIKE '$key%')
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];
$html="";
while($row = $obRest->FetchAssoc($result)){
    $html .= '<div style="width: 100%;border-top: 1px solid #d6d4d4;background-color:#f5f9f9"><a style="cursor:pointer;" class="suggest-element" data-recurso_nombre="'.($row['nombre_recurso']).'"  data-recurso_id="'.($row['recurso_id']).'" id="recurso_'.$row['ID'].'">'.($row['nombre_recurso']).' || '.($row['nombre_tipo_recurso']).'</a></div>';
}
echo $html;