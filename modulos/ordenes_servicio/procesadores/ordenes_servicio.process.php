<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ordenes_servicio.class.php");



if( !empty($_REQUEST["Accion"]) ){
    $obCon = new OrdenesServicio($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear o editar una Orden de servicio
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            $tabla="$db.ordenes_servicio";
            $datos_orden=$obCon->DevuelveValores($tabla, "orden_servicio_id", $orden_servicio_id);
            
            $Datos["orden_servicio_id"]=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            $Datos["fecha_orden"]=$obCon->normalizar($_REQUEST["fecha_orden"]);
            $Datos["tercero_id"]=$obCon->normalizar($_REQUEST["tercero_id"]);
            $Datos["direccion"]=$obCon->normalizar($_REQUEST["direccion"]);
            $Datos["municipio"]=$obCon->normalizar($_REQUEST["municipio"]);
            $Datos["usuario_asignado"]=$obCon->normalizar($_REQUEST["usuario_asignado"]);
            $Datos["usuario_id"]=$idUser;
            $Datos["observaciones_iniciales"]=$obCon->normalizar($_REQUEST["observaciones_iniciales"]);
            
            foreach ($Datos as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
                        
            if($datos_orden["ID"]==''){
                
                $Datos["estado"]=1;
                $sql=$obCon->getSQLInsert($tabla, $Datos);
                $obCon->Query($sql);
            }else{
                $sql=$obCon->getSQLUpdate($tabla, $Datos);
                $sql.=" WHERE orden_servicio_id='$orden_servicio_id'";
                $obCon->Query($sql);
            }
            unset($Datos);
            exit("OK;Registro guardado");
        break; //fin caso 1
        
        case 2: //Agregar un insumo a una orden de servicio
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            $fecha_orden_insumos=$obCon->normalizar($_REQUEST["fecha_orden_insumos"]);
            $tipo_registro=$obCon->normalizar($_REQUEST["tipo_registro"]);
            $insumo_id_oi=$obCon->normalizar($_REQUEST["insumo_id_oi"]);
            $cantidad_agregar=$obCon->normalizar($_REQUEST["cantidad_agregar"]);
            
            if($fecha_orden_insumos==''){
               exit("E1;Debe seleccionar una fecha;fecha_orden_insumos");
            }
            if($insumo_id_oi=='' or $insumo_id_oi=='get'){
               exit("E1;Debe seleccionar un insumo;insumo_id_oi");
            }
            if(!is_numeric($cantidad_agregar) or $cantidad_agregar<=0){
               exit("E1;El campo cantidad debe ser un valor númerico mayor a cero;cantidad_agregar");
            }
            
            if($tipo_registro==2){
                $datos_insumos=$obCon->get_insumos_orden($db, $orden_servicio_id, $insumo_id_oi);
                if(!isset($datos_insumos[$insumo_id_oi][1])){
                    exit("E1;este insumo no está disponible en esta orden de servicio");
                }else{
                    $cantidad_disponible=$datos_insumos[$insumo_id_oi][1]["cantidad_disponible"];
                    if($cantidad_disponible<$cantidad_agregar){
                        exit("E1;Sólo tiene $cantidad_disponible unidad(es) disponibles para consumir");
                    }
                }
                $sql="SELECT MIN(fecha) as min_fecha,MAX(fecha) as max_fecha FROM $db.ordenes_servicio_insumos WHERE orden_servicio_id='$orden_servicio_id' AND deleted<>'0000-00-00 00:00:00'  ";
                
                $datos_fechas=$obCon->FetchAssoc($obCon->Query($sql));
                
                $fecha_inicio=$datos_fechas["min_fecha"];
                $fecha_fin=$datos_fechas["max_fecha"];
                $sql="UPDATE $db.ordenes_servicio SET fecha_ejecucion_inicial='$fecha_inicio', fecha_ejecucion_final='$fecha_fin',estado='2' WHERE orden_servicio_id='$orden_servicio_id'  ";
                $obCon->Query($sql);
                
                
            }
                        
            $obCon->agregar_insumo_orden_servicio($db, $fecha_orden_insumos, $tipo_registro, $orden_servicio_id, $insumo_id_oi, $cantidad_agregar, $idUser);
            exit("OK;Registro agregado");
        break; //fin caso 2
    
        case 3: //Eliminar un registro
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            if($tabla_id=='1'){
                $fecha=date("Y-m-d H:i:s");
                $sql="UPDATE $db.ordenes_servicio_insumos set deleted='$fecha', user_delete='$idUser' WHERE ID='$item_id'";
                $obCon->Query($sql);
            }
            
            exit("OK;Registro Eliminado");
        break; //fin caso 3
        
        case 4: //Cerrar una orden de servicio
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $fecha_cierre_os=$obCon->normalizar($_REQUEST["fecha_cierre_os"]);
            $orden_servicio_id=$obCon->normalizar($_REQUEST["orden_servicio_id"]);
            $observaciones_cierre_orden=$obCon->normalizar($_REQUEST["observaciones_cierre_orden"]);
            
            if($fecha_cierre_os==''){
                exit("E1;El campo Fecha de Cierre no puede estar vacío;fecha_cierre_os");
            }
            
            if($observaciones_cierre_orden==''){
                exit("E1;El campo Observaciones no puede estar vacío;observaciones_cierre_orden");
            }
            
            $obCon->cerrar_orden_servicio($db,$orden_servicio_id, $fecha_cierre_os, $observaciones_cierre_orden, $idUser);
            
            exit("OK;Orden de Servicio Cerrada");
        break; //fin caso 4
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>