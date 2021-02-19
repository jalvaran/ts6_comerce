ALTER TABLE `inventario_items_general` ADD `departamento_id` int(11) NOT NULL DEFAULT '99' AFTER `usuario_id`;
ALTER TABLE `inventario_items_general` ADD INDEX `departamento_id` (`departamento_id`);
CREATE TABLE `inventario_items_departamentos` (
  `ID` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `departamento` varchar(100) NOT NULL,
  `tipo_item` int NOT NULL DEFAULT '1',
  `cuenta_puc_ventas` bigint NOT NULL,
  `cuenta_puc_compras` bigint NOT NULL,
  `cuenta_puc_iva_ventas` bigint NOT NULL,
  `cuenta_puc_iva_compras` bigint NOT NULL,
  `cuenta_puc_inventarios` bigint NOT NULL,
  `cuenta_puc_costos_inventarios` bigint NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE='MyISAM';

INSERT INTO `inventario_items_departamentos` (`ID`, `departamento`, `tipo_item`, `cuenta_puc_ventas`, `cuenta_puc_compras`, `cuenta_puc_iva_ventas`, `cuenta_puc_iva_compras`, `cuenta_puc_inventarios`, `cuenta_puc_costos_inventarios`, `updated`, `created`)
VALUES ('99', 'GENERAL', '1', '41350501', '41350501', '24080101', '24080101', '14350599', '61350199', now(), now());