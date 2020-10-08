-- Adminer 4.7.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `factura_prefactura`;
CREATE TABLE `factura_prefactura` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `activa` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `factura_prefactura` (`ID`, `usuario_id`, `activa`, `created`) VALUES
(1,	3,	0,	'0000-00-00 00:00:00'),
(2,	3,	0,	'2020-10-07 21:26:31'),
(3,	3,	1,	'2020-10-07 21:26:34'),
(4,	8,	0,	'2020-10-07 21:26:34');

DROP TABLE IF EXISTS `factura_prefactura_items`;
CREATE TABLE `factura_prefactura_items` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `prefactura_id` int(11) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `valor_unitario` double NOT NULL,
  `cantidad` double NOT NULL,
  `subtotal` double NOT NULL,
  `impuestos` double NOT NULL,
  `total` double NOT NULL,
  `porcentaje_iva_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `prefactura_id` (`prefactura_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `factura_prefactura_items` (`ID`, `prefactura_id`, `item_id`, `valor_unitario`, `cantidad`, `subtotal`, `impuestos`, `total`, `porcentaje_iva_id`, `usuario_id`, `created`) VALUES
(1,	3,	1,	1000,	1,	1000,	190,	1190,	6,	3,	'2020-10-07 22:34:02'),
(2,	3,	1,	840.34,	1,	840.34,	159.66,	1000,	6,	3,	'2020-10-07 22:35:07'),
(3,	3,	1,	840.3361,	4,	3361.3444,	638.6554,	3999.9998,	6,	3,	'2020-10-07 22:36:57'),
(4,	3,	1,	840.34,	4,	3361.36,	638.66,	4000.02,	6,	3,	'2020-10-07 22:37:29'),
(5,	3,	1,	840,	4,	3360,	638,	3998,	6,	3,	'2020-10-07 22:38:52'),
(6,	3,	1,	840.34,	4,	3361.36,	638.66,	4000.02,	6,	3,	'2020-10-07 22:40:06'),
(7,	3,	1,	840.3361,	4,	3361.3444,	638.66,	4000.0044,	6,	3,	'2020-10-07 22:40:31'),
(8,	3,	1,	840.3361,	4,	3361.3444,	638.6554,	3999.9998,	6,	3,	'2020-10-07 22:40:53'),
(9,	3,	1,	840,	4,	3360,	638,	3998,	6,	3,	'2020-10-07 22:41:18'),
(10,	3,	1,	840.33613445378,	4,	3361.3445378151,	638.65546218487,	4000,	6,	3,	'2020-10-07 22:41:37'),
(11,	3,	1,	84.033613445378,	4,	336.13445378151,	63.865546218487,	400,	6,	3,	'2020-10-07 22:42:46'),
(12,	3,	1,	420.16806722689,	4,	1680.6722689076,	319.32773109244,	2000,	6,	3,	'2020-10-07 22:43:06');

DROP TABLE IF EXISTS `inventario_items_general`;
CREATE TABLE `inventario_items_general` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Referencia` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Referencia del Item',
  `Descripcion` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre del Item',
  `Precio` double NOT NULL COMMENT 'Precio de Venta',
  `porcentajes_iva_id` int(11) NOT NULL COMMENT 'id el porcentaje de IVA',
  `usuario_id` int(11) NOT NULL COMMENT 'id del usuario que crea el item',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'fecha de creacion',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'fecha de actualizacion',
  PRIMARY KEY (`ID`),
  KEY `Referencia` (`Referencia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `inventario_items_general` (`ID`, `Referencia`, `Descripcion`, `Precio`, `porcentajes_iva_id`, `usuario_id`, `created`, `updated`) VALUES
(1,	'co33',	'CAMARA DE SEGURIDAD',	1000,	6,	3,	'2020-10-07 10:15:30',	'2020-10-07 15:15:30'),
(2,	'ref1',	'memoria usb',	15000,	6,	3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(3,	'12',	'CAMARA WEB 2',	150000,	6,	3,	'2020-10-07 10:24:28',	'2020-10-07 15:24:55'),
(4,	'2122',	'MOUSE',	12000,	6,	3,	'2020-10-07 10:25:58',	'2020-10-07 15:25:58');

DROP TABLE IF EXISTS `terceros`;
CREATE TABLE `terceros` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `tipo_tercero` int(11) NOT NULL COMMENT 'Tipo de Tercero',
  `tipo_organizacion_id` int(11) NOT NULL COMMENT 'Tipo de Persona',
  `tipo_regimen_id` int(11) NOT NULL COMMENT 'Tipo de Regimen',
  `tipo_documento_id` int(11) NOT NULL COMMENT 'Tipo de Documento de Identificación',
  `identificacion` bigint(20) NOT NULL COMMENT 'Documento de Identificación',
  `dv` int(11) NOT NULL COMMENT 'Dígito de Verificación',
  `razon_social` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Razón Social del tercero',
  `direccion` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Dirección del tercero',
  `telefono` bigint(20) NOT NULL COMMENT 'Teléfono del tercero',
  `email` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Correo Electrónico del Tercero',
  `municipio_id` int(11) NOT NULL COMMENT 'Ciudad del tercero',
  `usuario_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `identificacion` (`identificacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `terceros` (`ID`, `tipo_tercero`, `tipo_organizacion_id`, `tipo_regimen_id`, `tipo_documento_id`, `identificacion`, `dv`, `razon_social`, `direccion`, `telefono`, `email`, `municipio_id`, `usuario_id`, `created`, `updated`) VALUES
(1,	1,	1,	1,	1,	3213,	1,	'dsada',	'dsadsa',	321321,	'dsadsa',	4,	0,	'2020-10-07 10:37:10',	'2020-10-07 15:37:10'),
(2,	3,	1,	1,	6,	900833180,	7,	'TECHNO SOLUCIONES SAS',	'CALLE 6 SUR 13B 66',	3177740609,	'jalvaran@gmail.com',	1013,	0,	'2020-10-07 10:37:10',	'2020-10-07 15:37:10'),
(3,	1,	2,	2,	3,	94481747,	0,	'JULIAN ANDRES ALVARAN VALENCIA',	'CRA 17 7 24',	3177740610,	'jalvaran@gmail.com',	1013,	0,	'2020-10-07 10:37:10',	'2020-10-07 15:37:10'),
(4,	1,	2,	2,	3,	29284348,	0,	'ERIKA LICETH ALVARAN VALENCIA',	'CALLE 6A SUR 13a 24',	3156669898,	'jalvaran@gmail.com',	1013,	3,	'2020-10-07 10:38:37',	'2020-10-07 15:38:59');

-- 2020-10-08 03:44:34
