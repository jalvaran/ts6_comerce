<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/facturador.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new Facturador($idUser);
    
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
            if($precio_venta<>'' and (!is_numeric($cantidad) or $cantidad<=0)){
                exit("E1;El campo Precio de Venta debe ser un valor númerico mayor a cero;precio_venta");
            }
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $obCon->agregar_item_prefactura($prefactura_id,$db, $codigo_id, $precio_venta, $cantidad, $cmb_impuestos_incluidos, $idUser);
            print("OK;Item agregado a la prefactura");
            
        break;//Fin caso 2
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>