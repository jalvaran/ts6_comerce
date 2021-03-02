DROP VIEW IF EXISTS `vista_actos_administrativos_procesos`;
CREATE VIEW vista_actos_administrativos_procesos AS 
        SELECT t1.*,
        (SELECT t2.acto_administrativo FROM procesos_juridicos_actos_tipo t2 WHERE t2.ID=t1.acto_tipo_id LIMIT 1) AS nombre_acto_administrativo,
        (SELECT t2.nombre FROM procesos_juridicos_actos_administrativos_estados t2 WHERE t2.ID=t1.estado LIMIT 1) AS nombre_estado,
        (SELECT t2.color FROM procesos_juridicos_actos_administrativos_estados t2 WHERE t2.ID=t1.estado LIMIT 1) AS color_estado,
        if(t1.estado=1,(SELECT DATEDIFF(t1.fecha_plazo_atencion,now())),null) as dias_plazo 
        FROM procesos_juridicos_actos_administrativos t1;


DROP VIEW IF EXISTS `vista_procesos_juridicos`;
CREATE VIEW vista_procesos_juridicos AS 
        SELECT t1.ID,
        (SELECT MIN(dias_plazo) FROM vista_actos_administrativos_procesos t6 WHERE t6.proceso_id=t1.proceso_id) as dias_plazo, 
        t2.razon_social,t2.identificacion,t1.codigo_dane_municipio,
        (SELECT proceso_tipo FROM procesos_juridicos_tipo t4 WHERE t4.ID=t1.tipo_proceso_id) as nombre_tipo_proceso, 
        t1.descripcion,
        (SELECT nombre_tema FROM procesos_juridicos_temas t2 WHERE t2.ID=t1.tema_id) as nombre_tema,  
        
        (SELECT nombre_sub_tema FROM procesos_juridicos_sub_temas t3 WHERE t3.ID=t1.subtema_id) as nombre_sub_tema,  
        
        
        t1.anio_gravable,t1.periodo,t1.usuario_asignado_id,
        (SELECT nombre_estado FROM procesos_juridicos_estados t5 WHERE t5.ID=t1.estado) as nombre_estado,  
        t1.proceso_id,t1.tema_id,t1.subtema_id,t1.tipo_proceso_id,t1.estado,t1.user_id,t1.created,t1.updated 
        FROM procesos_juridicos t1 INNER JOIN terceros t2 ON t1.tercero_id=t2.ID;




