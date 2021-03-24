DROP VIEW IF EXISTS `vista_tickets`;
CREATE VIEW vista_tickets AS 
SELECT *,
    (SELECT Nombre FROM techno_ts6_comerce.usuarios WHERE ID=t1.idUsuarioSolicitante) AS NombreSolicitante,
    (SELECT Apellido FROM techno_ts6_comerce.usuarios WHERE ID=t1.idUsuarioSolicitante) AS ApellidoSolicitante,
    (SELECT Nombre FROM techno_ts6_comerce.usuarios WHERE ID=t1.idUsuarioAsignado) AS NombreAsignado,
    (SELECT Apellido FROM techno_ts6_comerce.usuarios WHERE ID=t1.idUsuarioAsignado) AS ApellidoAsignado,
    (SELECT Estado FROM tickets_estados t2 WHERE t2.ID=t1.Estado) AS NombreEstado, 
    
    (SELECT Departamento FROM tickets_departamentos t2 WHERE t2.ID=t1.departamento_id) AS NombreDepartamento,
    
    (SELECT TipoTicket FROM tickets_tipo t2 WHERE t2.ID=t1.TipoTicket) AS NombreTipoTicket
FROM tickets t1 ;