DROP VIEW IF EXISTS `vista_repositorio_juridico`;
CREATE VIEW vista_repositorio_juridico AS 
        SELECT t1.ID,t1.tema_referencia,
        (SELECT nombre_tema FROM repositorio_juridico_temas t2 WHERE t2.ID=t1.tema_id) as nombre_tema,  
        t1.sub_tema_referencia,
        (SELECT nombre_sub_tema FROM repositorio_juridico_sub_temas t3 WHERE t3.ID=t1.sub_tema_id) as nombre_sub_tema,  
        t1.tipo_documento_referencia,
        (SELECT tipo_documento FROM repositorio_juridico_tipo_documentos t4 WHERE t4.ID=t1.tipo_documento_id) as nombre_tipo_documento, 
        t1.numero_documento,t1.fecha_referencia,t1.fecha,t1.entidad_referencia,
        (SELECT nombre_entidad FROM repositorio_juridico_entidades t6 WHERE t6.ID=t1.entidad_id) as nombre_entidad, 
        t1.extracto,t1.fuentes_formales,t1.ano_recopilacion,
        (SELECT nombre_estado FROM repositorio_juridico_estados t5 WHERE t5.ID=t1.estado) as nombre_estado,  
        t1.repositorio_id,t1.tema_id,t1.sub_tema_id,t1.tipo_documento_id,t1.estado,t1.user_id,t1.created,t1.updated 
        FROM repositorio_juridico t1;

