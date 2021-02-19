<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
class Proyectos extends conexion{
    
    
    function crear_vista_proyectos($db){
        $sql="DROP VIEW IF EXISTS $db.`vista_proyectos`;";
        $this->Query($sql);
        
        $sql="CREATE VIEW $db.vista_proyectos AS 
                SELECT t1.*,
                (t1.costos_mano_obra_planeacion+t1.costos_productos_planeacion+t1.gastos_fijos_planeados) as total_costos_planeacion,
                (t1.costos_mano_obra_ejecucion+t1.costos_productos_ejecucion+t1.gastos_fijos_ejecutados) as total_costos_ejecucion,
                ((select total_costos_planeacion) - (select total_costos_ejecucion)) as diferencia_costos_planeacion_ejecucion,
                (SELECT RazonSocial FROM $db.clientes t2 WHERE t2.idClientes=t1.cliente_id) as cliente_razon_social,
                (SELECT Num_Identificacion FROM $db.clientes t2 WHERE t2.idClientes=t1.cliente_id) as cliente_nit,
                (SELECT nombre_estado FROM $db.proyectos_estados t2 WHERE t2.ID=t1.estado) as nombre_estado  
                    
                FROM $db.proyectos t1
                ;
                    
           ";
        $this->Query($sql);
        
    }
    
    public function agregar_fecha_excluida($db,$proyecto_id,$fecha_excluida){
        $Datos["proyecto_id"]=$proyecto_id;
        $Datos["fecha_excluida"]=$fecha_excluida;
        $sql=$this->getSQLInsert("$db.proyectos_fechas_excluidas", $Datos);
        $this->Query($sql);
    }
    
    public function crear_editar_proyecto($db,$datos_proyecto){
        $proyecto_id=$datos_proyecto["proyecto_id"];
        $Tabla="$db.proyectos";
        $sql="SELECT ID FROM $Tabla WHERE proyecto_id='$proyecto_id'";
        $valida=$this->FetchAssoc($this->Query($sql));
        if($valida["ID"]>0){
            $sql=$this->getSQLUpdate($Tabla, $datos_proyecto);
            $sql.=" WHERE proyecto_id='$proyecto_id'";
        }else{
            $sql=$this->getSQLInsert($Tabla, $datos_proyecto);
        }
        $this->Query($sql);
    }
    
    public function RegistreAdjuntoProyecto($db,$proyecto_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="$db.proyectos_adjuntos";
        
        $Datos["proyecto_id"]=$proyecto_id;
        
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
    public function RegistreAdjuntoTarea($db,$proyecto_id,$tarea_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="$db.proyectos_tareas_adjuntos";
        
        $Datos["proyecto_id"]=$proyecto_id;
        $Datos["tarea_id"]=$tarea_id;        
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
    public function crear_editar_proyecto_tarea($db,$datos_tarea){
        $tarea_id=$datos_tarea["tarea_id"];
        $Tabla="$db.proyectos_tareas";
        $sql="SELECT ID FROM $Tabla WHERE tarea_id='$tarea_id'";
        $valida=$this->FetchAssoc($this->Query($sql));
        if($valida["ID"]>0){
            $sql=$this->getSQLUpdate($Tabla, $datos_tarea);
            $sql.=" WHERE tarea_id='$tarea_id'";
        }else{
            $sql=$this->getSQLInsert($Tabla, $datos_tarea);
        }
        $this->Query($sql);
    }
    
    public function RegistreAdjuntoActividad($db,$proyecto_id,$tarea_id,$actividad_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="$db.proyectos_actividades_adjuntos";
        
        $Datos["proyecto_id"]=$proyecto_id;
        $Datos["tarea_id"]=$tarea_id; 
        $Datos["actividad_id"]=$actividad_id; 
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
    
    public function crear_editar_proyecto_tarea_actividad($db,$datos_tarea){
        $actividad_id=$datos_tarea["actividad_id"];
        $Tabla="$db.proyectos_actividades";
        $sql="SELECT ID,titulo_actividad FROM $Tabla WHERE actividad_id='$actividad_id'";
        $valida=$this->FetchAssoc($this->Query($sql));
        if($valida["ID"]>0){
            $sql=$this->getSQLUpdate($Tabla, $datos_tarea);
            $sql.=" WHERE actividad_id='$actividad_id'";
        }else{
            $sql=$this->getSQLInsert($Tabla, $datos_tarea);
        }
        $this->Query($sql);
        if($valida["titulo_actividad"]<>$datos_tarea["titulo_actividad"]){
            $nuevo_titulo=$datos_tarea["titulo_actividad"];
            $sql="UPDATE $db.proyectos_actividades_eventos set titulo='$nuevo_titulo' WHERE actividad_id='$actividad_id'";
            $this->Query($sql);
        }
    }
    
    public function crear_editar_evento($db,$datos_tarea){
        $evento_id=$datos_tarea["evento_id"];
        $Tabla="$db.proyectos_actividades_eventos";
        $sql="SELECT ID FROM $Tabla WHERE evento_id='$evento_id'";
        $valida=$this->FetchAssoc($this->Query($sql));
        if($valida["ID"]>0){
            $sql=$this->getSQLUpdate($Tabla, $datos_tarea);
            $sql.=" WHERE evento_id='$evento_id'";
        }else{
            $sql=$this->getSQLInsert($Tabla, $datos_tarea);
        }
        $this->Query($sql);
    }
    
    function calcular_horas($fecha_inicial,$fecha_final){
        $fecha1 = new DateTime($fecha_inicial);//fecha inicial
        $fecha2 = new DateTime($fecha_final);//fecha final

        $intervalo = $fecha1->diff($fecha2);
        $dias=$intervalo->format('%d');
        $total_horas=$dias*24;
        $horas=$intervalo->format('%H');
        $minutos=$intervalo->format('%i');
        $total_horas=$total_horas+$horas+round($minutos/59,1);
        
        return($total_horas);
    }
    
    function actualizar_totales_proyecto($db,$proyecto_id){ 
        
        $sql="update $db.proyectos_actividades t1 
                set t1.total_horas_planeadas=(SELECT SUM(horas) FROM $db.proyectos_actividades_eventos t2 WHERE t1.actividad_id=t2.actividad_id and estado<10),
                 t1.costos_planeacion=(SELECT SUM(total_costos_planeacion) FROM $db.vista_proyectos_costos t2 WHERE t1.actividad_id=t2.actividad_id),
                 t1.valor_facturar=(SELECT SUM(precio_venta_planeado) FROM $db.vista_proyectos_costos t2 WHERE t1.actividad_id=t2.actividad_id),
                 t1.fecha_inicio_planeacion=(SELECT min(fecha_inicial_planeada) FROM $db.vista_proyectos_costos t2 WHERE t1.actividad_id=t2.actividad_id),
                 t1.fecha_final_planeacion=(SELECT max(fecha_final_planeada) FROM $db.vista_proyectos_costos t2 WHERE t1.actividad_id=t2.actividad_id) 
                     
                where t1.proyecto_id='$proyecto_id'";
        $this->Query($sql);
       
        $sql="update $db.proyectos_tareas t1 
                 set t1.total_horas_planeadas=(SELECT SUM(horas) FROM $db.proyectos_actividades_eventos t2 WHERE t1.tarea_id=t2.tarea_id and estado<10),
                 t1.costos_planeacion=(SELECT SUM(total_costos_planeacion) FROM $db.vista_proyectos_costos t2 WHERE t1.tarea_id=t2.tarea_id),
                 t1.valor_facturar=(SELECT SUM(precio_venta_planeado) FROM $db.vista_proyectos_costos t2 WHERE t1.tarea_id=t2.tarea_id),
                 t1.fecha_inicio_planeacion=(SELECT min(fecha_inicial_planeada) FROM $db.vista_proyectos_costos t2 WHERE t1.tarea_id=t2.tarea_id),
                 t1.fecha_final_planeacion=(SELECT max(fecha_final_planeada) FROM $db.vista_proyectos_costos t2 WHERE t1.tarea_id=t2.tarea_id) 
                   
                where t1.proyecto_id='$proyecto_id'";
        $this->Query($sql);
        
        $sql="update $db.proyectos t1 
                set t1.total_horas_planeadas=(SELECT SUM(horas) FROM $db.proyectos_actividades_eventos t2 WHERE t1.proyecto_id=t2.proyecto_id and estado<10),
                t1.costos_planeacion=(SELECT SUM(total_costos_planeacion) FROM $db.vista_proyectos_costos t2 WHERE t1.proyecto_id=t2.proyecto_id),
                t1.valor_facturar=(SELECT SUM(precio_venta_planeado) FROM $db.vista_proyectos_costos t2 WHERE t1.proyecto_id=t2.proyecto_id),
                t1.utilidad_planeada=(100/if(t1.costos_planeacion=0,1,t1.costos_planeacion)*t1.valor_facturar)-100,    
                t1.fecha_inicio_planeacion=(SELECT min(fecha_inicial_planeada) FROM $db.vista_proyectos_costos t2 WHERE t1.proyecto_id=t2.proyecto_id),
                t1.fecha_final_planeacion=(SELECT max(fecha_final_planeada) FROM $db.vista_proyectos_costos t2 WHERE t1.proyecto_id=t2.proyecto_id) 
                   
                where t1.proyecto_id='$proyecto_id'";
        $this->Query($sql);
        
        
    }
    
    public function crear_recurso_proyecto($db,$recurso_id,$nombre_recurso, $hora_o_fijo,$tipo, $user_id) {
        $tab="$db.proyectos_recursos";
        
        $Datos["recurso_id"]=$recurso_id;
        $Datos["nombre_recurso"]=$nombre_recurso;        
        $Datos["hora_o_fijo"]=$hora_o_fijo;    
        $Datos["tipo"]=$tipo;
        $Datos["user_id"]=$user_id;
        
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
        
    }
    
    public function agregar_recurso_actividad($db,$proyecto_id,$tarea_id, $actividad_id,$tipo_recurso,$nombre_recurso, $tabla_origen,$hora_fijo,$recurso_id,$cantidad_planeacion,$costo_unitario_planeacion,$utilidad_esperada,$precio_venta_unitario_planeacion_segun_utilidad,$precio_venta_total_planeado,$usuario_id) {
        
        $tab="$db.proyectos_actividades_recursos";
        
        $Datos["proyecto_id"]=$proyecto_id;
        $Datos["tarea_id"]=$tarea_id;        
        $Datos["actividad_id"]=$actividad_id;    
        $Datos["tabla_origen"]=$tabla_origen;
        $Datos["hora_fijo"]=$hora_fijo;  
        $Datos["tipo_recurso"]=$tipo_recurso;        
        $Datos["nombre_recurso"]=$nombre_recurso;        
        $Datos["recurso_id"]=$recurso_id;
        $Datos["cantidad_planeacion"]=$cantidad_planeacion;        
        $Datos["costo_unitario_planeacion"]=$costo_unitario_planeacion;  
        $Datos["utilidad_esperada"]=$utilidad_esperada;        
        $Datos["precio_venta_unitario_planeacion_segun_utilidad"]=$precio_venta_unitario_planeacion_segun_utilidad;
        $Datos["precio_venta_total_planeado"]=$precio_venta_total_planeado;        
        $Datos["usuario_id"]=$usuario_id;    
                
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
        
        
        
    }
    
    
    /**
     * Fin Clase
     */
}
