DROP VIEW IF EXISTS `vista_procesos_juridicos`;
CREATE VIEW vista_procesos_juridicos AS 
        SELECT t1.ID,t2.razon_social,t2.identificacion,
        (SELECT proceso_tipo FROM procesos_juridicos_tipo t4 WHERE t4.ID=t1.tipo_proceso_id) as nombre_tipo_proceso, 
        t1.descripcion,
        (SELECT nombre_tema FROM procesos_juridicos_temas t2 WHERE t2.ID=t1.tema_id) as nombre_tema,  
        
        (SELECT nombre_sub_tema FROM procesos_juridicos_sub_temas t3 WHERE t3.ID=t1.subtema_id) as nombre_sub_tema,  
        
        
        t1.anio_gravable,t1.periodo,t1.usuario_asignado_id,
        (SELECT nombre_estado FROM procesos_juridicos_estados t5 WHERE t5.ID=t1.estado) as nombre_estado,  
        t1.proceso_id,t1.tema_id,t1.subtema_id,t1.tipo_proceso_id,t1.estado,t1.user_id,t1.created,t1.updated 
        FROM procesos_juridicos t1 INNER JOIN terceros t2 ON t1.tercero_id=t2.ID;

