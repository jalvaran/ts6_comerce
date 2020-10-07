<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
class Mantenimiento extends conexion{
    
    public function CrearVistaOrdenTrabajo($db) {
        $principalDb=DB;
        $sql="DROP VIEW IF EXISTS `vista_ordenes_trabajo`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE VIEW vista_ordenes_trabajo AS
                SELECT t1.*,
                    
                    (SELECT tipo_mantenimiento FROM $principalDb.ordenes_trabajo_tipo_mantenimiento t3 WHERE t3.ID=t1.tipo_mantenimiento LIMIT 1) AS nombre_tipo_mantenimiento,
                    (SELECT NombreTecnico FROM catalogo_tecnicos t4 WHERE t4.ID=t1.tecnico_id LIMIT 1) AS nombre_tecnico, 
                    (SELECT Nombre FROM equipos_maquinas t5 WHERE t5.ID=t1.maquina_id LIMIT 1) AS nombre_maquina,
                    (SELECT Codigo FROM equipos_maquinas t5 WHERE t5.ID=t1.maquina_id LIMIT 1) AS codigo_maquina,
                    (SELECT ubicacion_id FROM equipos_maquinas t5 WHERE t5.ID=t1.maquina_id LIMIT 1) AS ubicacion_id,
                    (SELECT NombreSeccion FROM catalogo_secciones t6 WHERE t6.ID=(SELECT ubicacion_id) LIMIT 1) AS nombre_ubicacion,
                    (SELECT Nombre FROM equipos_componentes t7 WHERE t7.ID=t1.componente_id LIMIT 1) AS nombre_componente,
                    (SELECT NumeroSerie FROM equipos_componentes t7 WHERE t7.ID=t1.componente_id LIMIT 1) AS serie_componente,
                    (SELECT IF( Estado<3 AND t1.tipo_mantenimiento=2,(SELECT (SELECT t1.horas_programadas)-horas_trabajo FROM equipos_componentes t7 WHERE t7.ID=t1.componente_id LIMIT 1),0)) AS diferencia_horas,
                    (SELECT IF( Estado<3 AND t1.tipo_mantenimiento=2,(SELECT (SELECT t1.kilometros_programados)-kilometros_trabajo FROM equipos_componentes t7 WHERE t7.ID=t1.componente_id LIMIT 1),0)) AS diferencia_kilometros,
                    
                    (SELECT DATEDIFF(now(),t1.`fecha_programada` )) as DiferenciaDias, 
                    (SELECT IF(estado<3 and (DiferenciaDias>=0 or diferencia_horas < 0 or diferencia_kilometros < 0 ),'1',( t1.estado))) as estado_ejecucion,
                    (SELECT nombre_estado FROM $principalDb.ordenes_trabajo_estados t2 WHERE t2.ID=(SELECT estado_ejecucion) LIMIT 1) AS nombre_estado 
                FROM `ordenes_trabajo` t1 ORDER BY estado_ejecucion,`tipo_mantenimiento`;";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function agregarTareaOT($db,$orden_trabajo_id,$tarea_id) {
        $Tabla="ordenes_trabajo_tareas";
        $created=date("Y-m-d H:i:s");        
        $Datos["orden_trabajo_id"]=$orden_trabajo_id;
        $Datos["tarea_id"]=$tarea_id;
        $Datos["estado"]=1;
        $Datos["created"]=$created;
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function crearEditarOrdenTrabajo($db,$edit_id,$orden_trabajo_id,$orden_tabajo_tipo_id,$fecha_programada,$maquina_id,$componente_id,$fecha_ultimo_mantenimiento,$frecuencia_dias,$ruta_verificacion_id,$frecuencia_ruta_verificacion,$frecuencia_horas,$horas_programadas,$frecuencia_kilometros,$kilometros_programados,$observaciones_orden,$idUser,$ActualizaComponente=1) {
        
        if($ruta_verificacion_id==''){
            $ruta_verificacion_id=0;
        }
        $frecuencia_orden=0;
        
        if($orden_tabajo_tipo_id=='3'){
            $frecuencia_orden=$frecuencia_ruta_verificacion;
        }
        if($orden_tabajo_tipo_id=='2'){
            $frecuencia_orden=$frecuencia_dias;
        }
        
        $Tabla="ordenes_trabajo";
        $created=date("Y-m-d H:i:s");   
        
        $Datos["fecha_programada"]=$fecha_programada;
        $Datos["fecha_cierre"]="0000-00-00";
        $Datos["tipo_mantenimiento"]=$orden_tabajo_tipo_id;
        $Datos["orden_trabajo_id"]=$orden_trabajo_id;
        $Datos["maquina_id"]=$maquina_id;
        $Datos["componente_id"]=$componente_id;
        $Datos["observaciones_orden"]=$observaciones_orden;
        $Datos["ruta_verificacion_id"]=$ruta_verificacion_id;
        $Datos["frecuencia_orden"]=$frecuencia_orden;
        $Datos["frecuencia_horas"]=$frecuencia_horas;
        $Datos["frecuencia_kilometros"]=$frecuencia_kilometros;
        $Datos["horas_programadas"]=$horas_programadas;
        $Datos["kilometros_programados"]=$kilometros_programados;
        $Datos["observaciones_cierre"]="";
        $Datos["observaciones_anulacion"]="";
        $Datos["tecnico_id"]=0;
        $Datos["estado"]=2;
        $Datos["usuario_creador_id"]=$idUser;
        $Datos["usuario_anulacion_id"]=0;
        
        $Datos["created"]=$created;
        if($edit_id==''){
            $sql=$this->getSQLInsert($Tabla, $Datos);
        }else{
            $sql=$this->getSQLUpdate($Tabla, $Datos);
            $sql.=" WHERE ID='$edit_id'";
        }
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        if($orden_tabajo_tipo_id==2 and $ActualizaComponente==1){  //si es una orden de mantenimiento preventivo, actualizo los valores del componente
            $sql="UPDATE equipos_componentes 
                    SET fecha_ultimo_mantenimiento='$fecha_ultimo_mantenimiento', frecuencia_mtto_dias='$frecuencia_dias'  
                    
                    WHERE ID='$componente_id'

                         ";
            $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        }
        
        if($orden_tabajo_tipo_id==1 or $orden_tabajo_tipo_id==3){//Si es una orden de trabajo de mantenimiento correctivo o una ruta de verificacion borro las tareas que se hubiesen podido agregar
            $this->BorraReg($db.".ordenes_trabajo_tareas", "orden_trabajo_id", $orden_trabajo_id);
        }
    }
    
    public function agregarInsumoOT($db,$orden_trabajo_id,$insumo_id,$cantidad,$valor_unitario,$idUser) {
        $Tabla="ordenes_trabajo_insumos";
        $created=date("Y-m-d H:i:s");        
        $Datos["orden_trabajo_id"]=$orden_trabajo_id;
        $Datos["insumo_id"]=$insumo_id;
        $Datos["valor_unitario"]=$valor_unitario;        
        $Datos["cantidad"]=$cantidad;
        $Datos["total"]=round($valor_unitario*$cantidad,2);
        $Datos["idUser"]=$idUser;
        $Datos["created"]=$created;
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function agregarFallaOT($db, $orden_trabajo_id, $componente_id, $falla_id, $causa_falla_id, $idUser) {
        $Tabla="ordenes_trabajo_fallas";
        $created=date("Y-m-d H:i:s");        
        $Datos["orden_trabajo_id"]=$orden_trabajo_id;
        $Datos["componente_id"]=$componente_id;
        $Datos["falla_id"]=$falla_id;        
        $Datos["causa_falla_id"]=$causa_falla_id;        
        $Datos["idUser"]=$idUser;
        $Datos["created"]=$created;
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function agregarVerificacionOT($db, $orden_trabajo_id, $maquina_id, $horas_trabajo, $kilometros_trabajo, $idUser) {
        $Tabla="ordenes_trabajo_maquinas_verificadas";
        $created=date("Y-m-d H:i:s");        
        $Datos["orden_trabajo_id"]=$orden_trabajo_id;
        $Datos["maquina_id"]=$maquina_id;
        $Datos["horas_trabajo"]=$horas_trabajo;        
        $Datos["kilometros_trabajo"]=$kilometros_trabajo;    
        $Datos["idUser"]=$idUser;
        $Datos["created"]=$created;
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    
    public function cerrar_orden_trabajo_preventivo($db,$DatosOrden,$DatosComponente,$orden_trabajo_id,$fecha_cierre,$horas_ultimo_mantenimiento,$kilometros_ultimo_mantenimiento,$tecnico_id,$observaciones_cierre,$tiempo_parada,$idUser) {
        
        $sql="UPDATE ordenes_trabajo SET fecha_cierre='$fecha_cierre', tiempo_parada='$tiempo_parada',tiempo_dedicado='$tiempo_parada',
                observaciones_cierre='$observaciones_cierre',tecnico_id='$tecnico_id', usuario_cierre_id='$idUser',estado=3 
               WHERE ID='$orden_trabajo_id';
                ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
       
        $setFrecuenciaHoras="";
        if($DatosComponente["frecuencia_mtto_horas"]>0){
            $setFrecuenciaHoras="horas_ultimo_mantenimiento='$horas_ultimo_mantenimiento',";
            $setFrecuenciaHoras.="horas_trabajo='$horas_ultimo_mantenimiento',";
        }
        $setFrecuenciaKilometros="";
        if($DatosComponente["frecuencia_mtto_kilometros"]>0){
            $setFrecuenciaKilometros="kilometros_ultimo_mantenimiento='$kilometros_ultimo_mantenimiento',";
            $setFrecuenciaKilometros.="kilometros_trabajo='$kilometros_ultimo_mantenimiento',";
        }
        $diasDiferencia=$this->obtenerDiferenciaFechasDias($fecha_cierre , date("Y-m-d"));

        $sql="UPDATE equipos_componentes SET 
                $setFrecuenciaHoras
                $setFrecuenciaKilometros
                usuario_id_update='$idUser'    
                WHERE maquina_id='".$DatosComponente["maquina_id"]."'
                   ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="UPDATE equipos_componentes SET fecha_ultimo_mantenimiento='$fecha_cierre',
                dias_ultimo_mantenimiento='$diasDiferencia',
                
                usuario_id_update='$idUser'    
                WHERE ID='".$DatosComponente["ID"]."'
                   ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        
        
        
    }
    
    
    public function cerrar_orden_trabajo_correctiva($db, $orden_trabajo_id, $fecha_cierre, $tecnico_id, $observaciones_cierre,$tiempo_parada, $idUser) {
        
        $sql="UPDATE ordenes_trabajo SET fecha_cierre='$fecha_cierre', tiempo_parada='$tiempo_parada',tiempo_dedicado='$tiempo_parada',
                observaciones_cierre='$observaciones_cierre',tecnico_id='$tecnico_id', usuario_cierre_id='$idUser',estado=3 
               WHERE ID='$orden_trabajo_id';
                ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        
    }
    
    public function cerrar_orden_trabajo_verificacion($db, $orden_trabajo_id, $fecha_cierre, $tecnico_id, $observaciones_cierre,$tiempo_dedicado, $idUser) {
        
        $sql="UPDATE ordenes_trabajo SET fecha_cierre='$fecha_cierre', tiempo_dedicado='$tiempo_dedicado',
                observaciones_cierre='$observaciones_cierre',tecnico_id='$tecnico_id', usuario_cierre_id='$idUser',estado=3 
               WHERE ID='$orden_trabajo_id';
                ";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="SELECT * FROM ordenes_trabajo_maquinas_verificadas WHERE orden_trabajo_id='$orden_trabajo_id'";
        $Consulta=$this->QueryExterno($sql, HOST, USER, PW, $db, "");
        while($DatosConsulta=$this->FetchAssoc($Consulta)){
            $maquina_id=$DatosConsulta["maquina_id"];
            $horas_trabajo=$DatosConsulta["horas_trabajo"];
            $kilometros_trabajo=$DatosConsulta["kilometros_trabajo"];
            
            $sql="UPDATE equipos_componentes SET horas_trabajo='$horas_trabajo', kilometros_trabajo='$kilometros_trabajo' WHERE maquina_id='$maquina_id'";
            $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        }
    }
    
    
    public function RegistreAdjuntoOT($db,$orden_trabajo_id, $destino, $Tamano, $NombreArchivo, $Extension,$cierre_orden, $idUser) {
        
        $tab="ordenes_trabajo_adjuntos";
        
        $Datos["orden_trabajo_id"]=$orden_trabajo_id;
        $Datos["cierre_orden"]=$cierre_orden;
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    /**
     * Fin Clase
     */
}
