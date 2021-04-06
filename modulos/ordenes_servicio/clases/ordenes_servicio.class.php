<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

/* 
 * Clase donde se usan los procesadores de las ordenes de servicio
 * Julian Alvaran
 * Techno Soluciones SAS
 * 2021-03-31
 */
        
class OrdenesServicio extends conexion{
    
    public function agregar_insumo_orden_servicio($db,$fecha,$tipo_registro,$orden_servicio_id,$insumo_id,$cantidad,$user_id) {
        $tab="$db.ordenes_servicio_insumos";        
        $Datos["fecha"]=$fecha;        
        $Datos["tipo_registro"]=$tipo_registro;    
        $Datos["orden_servicio_id"]=$orden_servicio_id;    
        $Datos["insumo_id"]=$insumo_id;    
        $Datos["cantidad"]=$cantidad; 
        $Datos["user_id"]=$user_id;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
    public function get_insumos_orden($db,$orden_servicio_id,$insumo_id='') {
        $condicion="";
        if($insumo_id<>''){
            $condicion="AND insumo_id='$insumo_id'";
        }
        $sql="SELECT t1.tipo_registro,t1.insumo_id,sum(t1.cantidad) as cantidad,
                (SELECT t2.nombre FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as nombre_insumo,
                (SELECT t2.referencia FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as referencia_insumo     
                FROM $db.ordenes_servicio_insumos t1 
                WHERE t1.orden_servicio_id='$orden_servicio_id' AND deleted='0000-00-00 00:00:00' $condicion GROUP BY t1.tipo_registro,t1.insumo_id ORDER BY t1.ID DESC
                    ";


        $Consulta=$this->Query($sql);

        $datos_insumos=[];
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $i=$DatosConsulta["insumo_id"];
            $tipo_registro=$DatosConsulta["tipo_registro"];
            $datos_insumos[$i][$tipo_registro]=$DatosConsulta;
            if(!isset($datos_insumos[$i][1]["cantidad_disponible"]) and $tipo_registro==1){
                $datos_insumos[$i]["cantidad_disponible"]=$DatosConsulta["cantidad"];
            }
            if(!isset($datos_insumos[$i][1]["cantidad_disponible"])){
                $datos_insumos[$i]["cantidad_disponible"]=0;
            }
            if(!isset($datos_insumos[$i][1]["cantidad"])){
                $datos_insumos[$i][1]["cantidad"]=0;
            }
            if(!isset($datos_insumos[$i][2]["cantidad"])){
                $datos_insumos[$i][2]["cantidad"]=0;
            }
            $datos_insumos[$i][1]["cantidad_disponible"]= $datos_insumos[$i][1]["cantidad"] - $datos_insumos[$i][2]["cantidad"];           
        }
        
        return($datos_insumos);
    }
    
    public function cerrar_orden_servicio($db,$orden_servicio_id,$fecha_cierre,$observaciones_finales,$usuario_id_cierre) {
        
        $tab="$db.ordenes_servicio";        
        $Datos["fecha_cierre"]=$fecha_cierre;        
        $Datos["usuario_id_cierre"]=$usuario_id_cierre;    
        $Datos["observaciones_finales"]=$observaciones_finales;    
        $Datos["estado"]=3;    
        
        $sql=$this->getSQLUpdate($tab, $Datos);
        $sql.="WHERE orden_servicio_id='$orden_servicio_id'";
        $this->Query($sql);
    }
    //Fin Clases
}
