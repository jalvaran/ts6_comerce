<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/facturador.class.php");
include_once("../../../general/class/facturacion_electronica.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new Facturador($idUser);
    $obFe=new Factura_Electronica($idUser);
    switch ($_REQUEST["Accion"]) {
        
        case 1: //agregar una prefactura
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $obCon->agregar_prefactura($db,$idUser);
            
            print("OK;Prefactura Creada");
            
        break;//Fin caso 1
    
        case 2: //marque una prefactura como activa
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $prefactura_id=$obCon->normalizar($_REQUEST["prefactura_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $obCon->ActualizaRegistro("$db.factura_prefactura", "activa", 0, "usuario_id", $idUser);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "activa", 1, "ID", $prefactura_id);
            print("OK;Prefactura Activada");
            
        break;//Fin caso 2
    
        case 3: //agrega un item a una prefactura
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $prefactura_id=$obCon->normalizar($_REQUEST["prefactura_id"]);
            
            $codigo_id=$obCon->normalizar($_REQUEST["codigo_id"]);
            $cantidad=$obCon->normalizar($_REQUEST["cantidad"]);
            $precio_venta=$obCon->normalizar($_REQUEST["precio_venta"]);
            $cmb_impuestos_incluidos=$obCon->normalizar($_REQUEST["cmb_impuestos_incluidos"]);
            if($codigo_id==""){
                exit("E1;El campo Código no puede estar vacío;codigo_id");
            }
            if(!is_numeric($cantidad) or $cantidad<=0){
                exit("E1;El campo Cantidad debe ser un valor númerico mayor a cero;cantidad");
            }
            if($precio_venta<>'' and (!is_numeric($precio_venta) or $precio_venta<=0)){
                exit("E1;El campo Precio de Venta debe ser un valor númerico mayor a cero;precio_venta");
            }
            
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $obCon->agregar_item_prefactura($prefactura_id,$db, $codigo_id, $precio_venta, $cantidad, $cmb_impuestos_incluidos, $idUser);
            print("OK;Item agregado a la prefactura");
            
        break;//Fin caso 3
        
        case 4: //eliminar un item de una prefactura
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            if($tabla_id==1){
                $tab="factura_prefactura_items";
            }
            $obCon->BorraReg($db.".".$tab, "ID", $item_id);
            print("OK;Item Borrado");
        break;//Fin caso 4    
        
        case 5: //editar un registro
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $tab=$obCon->normalizar($_REQUEST["tab"]);
            $item_id_edit=$obCon->normalizar($_REQUEST["item_id_edit"]);
            $campo_edit=$obCon->normalizar($_REQUEST["campo_edit"]);
            $valor_nuevo=$obCon->normalizar($_REQUEST["valor_nuevo"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $obCon->ActualizaRegistro("$db.$tab", $campo_edit, $valor_nuevo, "ID", $item_id_edit);
            print("OK;Registro Editado");
        break;//Fin caso 5
            
        case 6://crea un documento electronico
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $prefactura_id=$obCon->normalizar($_REQUEST["prefactura_id"]);
            $tercero_id=$obCon->normalizar($_REQUEST["tercero_id"]);
            $resolucion_id=$obCon->normalizar($_REQUEST["resolucion_id"]);
            $tipo_documento_id=$obCon->normalizar($_REQUEST["tipo_documento_id"]);
            $documento_asociado_id=$obCon->normalizar($_REQUEST["documento_asociado_id"]);
            if($empresa_id==''){
                exit("E1;No se recibió el id de la empresa");
            }
            if($prefactura_id==''){
                exit("E1;No se recibió el id de la prefactura;prefactura_id");
            }
            if($tercero_id==''){
                exit("E1;Debe seleccionar un Tercero;tercero_id");
            }
            if($resolucion_id==''){
                exit("E1;Debe Seleccionar una resolución;resolucion_id");
            }
            if($tipo_documento_id==''){
                exit("E1;Debe Seleccionar un tipo de documento;tipo_documento_id");
            }
            
            if($tipo_documento_id=='5' or $tipo_documento_id=='6'){
                if($documento_asociado_id==''){
                    exit("E1;Debe Seleccionar una factura electrónica a asociar;select2-documento_asociado_id-container");
                }
                
            }
            
            $sql="SELECT COUNT(*) total_items FROM $db.factura_prefactura_items WHERE prefactura_id='$prefactura_id'";
            $datos_validacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($datos_validacion["total_items"]==0){
                exit("E1;El documento no tiene items agregados");
            }
            
            $documento_electronico_id=$obFe->crear_documento_electronico_desde_prefactura($empresa_id,$tipo_documento_id, $prefactura_id, $tercero_id, $resolucion_id,$documento_asociado_id, $idUser);
                  
            $obCon->BorraReg("$db.factura_prefactura_items", "prefactura_id", $prefactura_id);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "observaciones", "", "ID", $prefactura_id);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "orden_compra", "", "ID", $prefactura_id);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "forma_pago", "1", "ID", $prefactura_id);
            
            if($datos_empresa["contabilizar_documentos_automaticamente"]==1){
                $obCon->contabilizar_documento_electronico($db, $documento_electronico_id);
                $obCon->ActualizaRegistro("$db.documentos_electronicos", "contabilizado", 1, "documento_electronico_id", $documento_electronico_id);
            }
            
            print("OK;Documento creado correctamente;$documento_electronico_id");
        break;//fin caso 6    
        
        case 7://Reportar un documento electrónico
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_electronico_id=$obCon->normalizar($_REQUEST["documento_electronico_id"]);
            $datos_documento=$obCon->DevuelveValores("$db.documentos_electronicos", "documento_electronico_id", $documento_electronico_id);
            if($datos_documento["ID"]==''){
                exit("E1;El documento no existe en la base de datos");
            }
            
            $obFe->reporta_documento_electronico($datos_empresa,$db, $documento_electronico_id,$datos_documento["tipo_documento_id"]);
            exit("OK;Documento Electrónico Reportado");
            
            
        break;//Fin caso 7    
        
        case 8://Ver pdf en handler con base 64
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_electronico_id=$obCon->normalizar($_REQUEST["documento_electronico_id"]);
            $sql="SELECT base64_pdf FROM $db.documentos_electronicos WHERE documento_electronico_id='$documento_electronico_id'";
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $base_64=$datos_consulta["base64_pdf"];
            $data = base64_decode($base_64);
            header('Content-Type: application/pdf');
            echo $data;
            
        break;//Fin caso 8    
    
        case 9://Ver zip en handler con base 64
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_electronico_id=$obCon->normalizar($_REQUEST["documento_electronico_id"]);
            $sql="SELECT base64_zip FROM $db.documentos_electronicos WHERE documento_electronico_id='$documento_electronico_id'";
            $datos_consulta=$obCon->FetchAssoc($obCon->Query($sql));
            $base_64=$datos_consulta["base64_zip"];
            $data = base64_decode($base_64);
            header('Content-Type: application/zip');
            header('Content-disposition: filename="xml_file.zip"');
            echo $data;
            
        break;//Fin caso 9
        
        case 10:// ver el json de un documento
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_electronico_id=$obCon->normalizar($_REQUEST["documento_electronico_id"]);
            $datos_documento=$obCon->DevuelveValores("$db.documentos_electronicos", "documento_electronico_id", $documento_electronico_id);
            if($datos_documento["tipo_documento_id"]==1){
                $json=$obFe->json_factura_electronica($datos_empresa, $db, $documento_electronico_id);
            }
            if($datos_documento["tipo_documento_id"]==5 or $datos_documento["tipo_documento_id"]==6){
                $json=$obFe->json_nota_credito_debito($datos_empresa, $db, $documento_electronico_id);
            }
            print("<pre>".$json."</pre>");
        break;//Fin caso 10    
            
        case 11://obtiene los totales de las diferentes tablas
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $sql="SELECT COUNT(ID) AS total FROM $db.terceros ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $terceros=$totales["total"];
            $sql="SELECT COUNT(ID) AS total FROM $db.inventario_items_general ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $items=$totales["total"];
            $sql="SELECT COUNT(ID) AS total FROM $db.documentos_electronicos WHERE is_valid=1 ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $documentos_ok=$totales["total"];
            $sql="SELECT COUNT(ID) AS total FROM $db.documentos_electronicos WHERE is_valid=0 ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $documentos_error=$totales["total"];
            print("OK;".$terceros.";".$items.";".$documentos_ok.";".$documentos_error);
            
        break;//Fin caso 11    
    
        case 12://Obtiene los datos de las resoluciones
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $tipo_documento_id=$obCon->normalizar($_REQUEST["tipo_documento_id"]);
            
            $sql="SELECT * FROM empresa_resoluciones where empresa_id='$empresa_id'  and tipo_documento_id='$tipo_documento_id' and estado=1";
            $consulta=$obCon->Query($sql);
            $i=0;
            while($datos_consulta=$obCon->FetchAssoc($consulta)){
                $resoluciones[$i]=$datos_consulta;
            }
            if(!isset($resoluciones[0])){
                exit("E1;No hay resoluciones disponibles para este tipo de documento");
            }
            
            $json_resoluciones= json_encode($resoluciones, true);
            print("OK;".$json_resoluciones);
        break;//Fin caso 12    
        
        case 13://Contabilizar todos los documentos electronicos sin contabilizar
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $sql="SELECT documento_electronico_id FROM $db.documentos_electronicos WHERE contabilizado=0";
            
            $consulta=$obCon->Query($sql);
            
            while($datos_consulta=$obCon->FetchAssoc($consulta)){
                $obCon->contabilizar_documento_electronico($db, $datos_consulta["documento_electronico_id"]);
                $obCon->ActualizaRegistro("$db.documentos_electronicos", "contabilizado", 1, "documento_electronico_id", $datos_consulta["documento_electronico_id"]);
                
            }
            exit("OK;Documentos contabilizados");
        break;//fin caso 13    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>