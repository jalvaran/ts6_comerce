DROP VIEW IF EXISTS `vista_ordenes_servicio`;
CREATE VIEW vista_ordenes_servicio AS 
SELECT t1.*,
    (SELECT CONCAT(Nombre,' ',Departamento) FROM techno_ts6_comerce.catalogo_municipios WHERE CodigoDANE=t1.municipio) AS nombre_municipio,
    (SELECT nombre_completo FROM techno_ts6_comerce.usuarios WHERE ID=t1.usuario_asignado) AS nombre_usuario_asignado,
    (SELECT razon_social FROM terceros WHERE ID=t1.tercero_id) AS tercero_razon_social,
    (SELECT identificacion FROM terceros WHERE ID=t1.tercero_id) AS tercero_identificacion,
    (SELECT estado_orden FROM ordenes_servicio_estados WHERE ID=t1.estado) AS nombre_estado 
FROM ordenes_servicio t1 ;