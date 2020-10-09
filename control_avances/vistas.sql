DROP VIEW IF EXISTS `vista_documentos_electronicos`;
CREATE VIEW vista_documentos_electronicos AS
SELECT t1.
  FROM `facturas_items` GROUP BY `FechaFactura`,`Referencia`;

