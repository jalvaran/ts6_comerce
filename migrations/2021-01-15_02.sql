ALTER TABLE `documentos_electronicos` ADD `contabilizado` int NOT NULL AFTER `documento_asociado_id`;
ALTER TABLE `documentos_electronicos` ADD INDEX `contabilizado` (`contabilizado`);
ALTER TABLE `inventario_items_departamentos`
ADD `cuenta_puc_ventas_devoluciones` bigint(20) NOT NULL AFTER `cuenta_puc_ventas`,
ADD `cuenta_puc_iva_ventas_devoluciones` bigint(20) NOT NULL AFTER `cuenta_puc_iva_ventas`;
UPDATE `inventario_items_departamentos` SET `cuenta_puc_ventas_devoluciones` = '41750101' WHERE `ID` = '99';
UPDATE `inventario_items_departamentos` SET `cuenta_puc_iva_ventas_devoluciones` = '24081201' WHERE `ID` = '99';