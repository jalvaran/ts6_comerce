<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ordenes_servicio.class.php");
include_once("../clases/pdf_ordenes_servicio.class.php");
if( !empty($_REQUEST["Accion"]) ){
    
    $obCon = new OrdenesServicio($idUser);
    $obPDF=new PDF_OrdenServicio(DB);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja el pdf de una orden de servicio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            
            $datos_items_orden=$obCon->get_insumos_orden($db, $orden_servicio_id);
            
            $obPDF->pdf_orden_servicio($db, $empresa_id, $orden_servicio_id,$datos_items_orden);
        break; //Fin caso 1
    
        case 2: //Dibuja el pdf de una orden de servicio entrega de suministros o insumos
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            
            $datos_items_orden=$obCon->get_insumos_orden($db, $orden_servicio_id);
            
            $obPDF->pdf_orden_servicio_entrega_suministros($db, $empresa_id, $orden_servicio_id,$datos_items_orden);
        break; //Fin caso 2
    
        case 3: //Dibuja el pdf de una orden de servicio devolucion de suministros o insumos
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            
            $datos_items_orden=$obCon->get_insumos_orden($db, $orden_servicio_id);
            
            $obPDF->pdf_orden_servicio_devolucion_suministros($db, $empresa_id, $orden_servicio_id,$datos_items_orden);
        break; //Fin caso 3
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>