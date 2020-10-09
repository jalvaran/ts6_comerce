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
            
        case 6://crea una factura electronica
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $prefactura_id=$obCon->normalizar($_REQUEST["prefactura_id"]);
            $tercero_id=$obCon->normalizar($_REQUEST["tercero_id"]);
            $resolucion_id=$obCon->normalizar($_REQUEST["resolucion_id"]);
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
            
            $sql="SELECT COUNT(*) total_items FROM $db.factura_prefactura_items WHERE prefactura_id='$prefactura_id'";
            $datos_validacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($datos_validacion["total_items"]==0){
                exit("E1;El documento no tiene items agregados");
            }
            
            $documento_electronico_id=$obFe->crear_factura_electronica_desde_prefactura($empresa_id, $prefactura_id, $tercero_id, $resolucion_id, $idUser);
            $obCon->BorraReg("$db.factura_prefactura_items", "prefactura_id", $prefactura_id);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "observaciones", "", "ID", $prefactura_id);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "orden_compra", "", "ID", $prefactura_id);
            $obCon->ActualizaRegistro("$db.factura_prefactura", "forma_pago", "1", "ID", $prefactura_id);
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
            if($datos_documento["tipo_documento_id"]==1){
                $obFe->reporta_factura_electronica($datos_empresa,$db, $documento_electronico_id);
                exit("OK;Documento Reportado");
            }
            
        break;//Fin caso 7    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>