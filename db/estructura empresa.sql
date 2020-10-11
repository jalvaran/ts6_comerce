-- Adminer 4.7.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `documentos_electronicos`;
CREATE TABLE `documentos_electronicos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `documento_electronico_id` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'identificador unico del documento',
  `fecha` date NOT NULL COMMENT 'fecha del documento',
  `hora` time NOT NULL COMMENT 'hora del documento',
  `tipo_documento_id` int(11) NOT NULL COMMENT 'tipo del documento electronico',
  `resolucion_id` int(11) NOT NULL COMMENT 'id de la resolucion a usar',
  `prefijo` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'prefijo de la resolucion',
  `numero` bigint(20) NOT NULL COMMENT 'numero del documento',
  `tercero_id` bigint(20) NOT NULL COMMENT 'id del tercero asociado al documento',
  `uuid` varchar(200) COLLATE utf8_spanish_ci NOT NULL COMMENT 'identificador ante la dian o CUFE',
  `base64_pdf` longtext COLLATE utf8_spanish_ci NOT NULL COMMENT 'base 64 del pdf documento',
  `base64_xml` longtext COLLATE utf8_spanish_ci NOT NULL COMMENT 'base 64 del xml del documento',
  `base64_zip` longtext COLLATE utf8_spanish_ci NOT NULL COMMENT 'base 64 del zip con el xml del documento',
  `is_valid` int(11) NOT NULL COMMENT 'indica si el documento es valido o tiene errores',
  `errors` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'describe los errore que tiene el documento',
  `usuario_id` int(11) NOT NULL COMMENT 'id del usuario que realiza el documento',
  `notas` text COLLATE utf8_spanish_ci NOT NULL COMMENT 'notas u observaciones del documento',
  `orden_compra` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'orden de compra del documento',
  `forma_pago` int(11) NOT NULL COMMENT 'forma de pago de una factura electronica',
  `documento_asociado_id` varchar(90) COLLATE utf8_spanish_ci NOT NULL COMMENT 'documento asociado cuando es una nota credito o debito',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `tipo_documento_id` (`tipo_documento_id`),
  KEY `resolucion_id` (`resolucion_id`),
  KEY `prefijo` (`prefijo`),
  KEY `numero` (`numero`),
  KEY `tercero_id` (`tercero_id`),
  KEY `uuid` (`uuid`),
  KEY `is_valid` (`is_valid`),
  KEY `usuario_id` (`usuario_id`),
  KEY `documento_electronico_id` (`documento_electronico_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `documentos_electronicos_items`;
CREATE TABLE `documentos_electronicos_items` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `documento_electronico_id` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
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
  KEY `documento_electronico_id` (`documento_electronico_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `factura_prefactura`;
CREATE TABLE `factura_prefactura` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `orden_compra` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `forma_pago` int(11) NOT NULL,
  `activa` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


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


DROP VIEW IF EXISTS `vista_documentos_electronicos`;
CREATE TABLE `vista_documentos_electronicos` (`ID` bigint(20), `documento_electronico_id` varchar(90), `fecha` date, `hora` time, `tipo_documento_id` int(11), `resolucion_id` int(11), `prefijo` varchar(45), `numero` bigint(20), `tercero_id` bigint(20), `uuid` varchar(200), `base64_pdf` longtext, `base64_xml` longtext, `base64_zip` longtext, `is_valid` int(11), `errors` text, `usuario_id` int(11), `notas` text, `orden_compra` varchar(100), `forma_pago` int(11), `documento_asociado_id` varchar(90), `created` datetime, `updated` timestamp, `nombre_tipo_documento` varchar(255), `nombre_tercero` varchar(200), `nit_tercero` bigint(20), `nombre_usuario` varchar(91));


DROP TABLE IF EXISTS `vista_documentos_electronicos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`techno`@`%` SQL SECURITY DEFINER VIEW `vista_documentos_electronicos` AS select `t1`.`ID` AS `ID`,`t1`.`documento_electronico_id` AS `documento_electronico_id`,`t1`.`fecha` AS `fecha`,`t1`.`hora` AS `hora`,`t1`.`tipo_documento_id` AS `tipo_documento_id`,`t1`.`resolucion_id` AS `resolucion_id`,`t1`.`prefijo` AS `prefijo`,`t1`.`numero` AS `numero`,`t1`.`tercero_id` AS `tercero_id`,`t1`.`uuid` AS `uuid`,`t1`.`base64_pdf` AS `base64_pdf`,`t1`.`base64_xml` AS `base64_xml`,`t1`.`base64_zip` AS `base64_zip`,`t1`.`is_valid` AS `is_valid`,`t1`.`errors` AS `errors`,`t1`.`usuario_id` AS `usuario_id`,`t1`.`notas` AS `notas`,`t1`.`orden_compra` AS `orden_compra`,`t1`.`forma_pago` AS `forma_pago`,`t1`.`documento_asociado_id` AS `documento_asociado_id`,`t1`.`created` AS `created`,`t1`.`updated` AS `updated`,(select `t3`.`name` from `techno_ts6_comerce`.`api_fe_tipo_documentos` `t3` where (`t3`.`ID` = `t1`.`tipo_documento_id`) limit 1) AS `nombre_tipo_documento`,(select `t4`.`razon_social` from `techno_ts6_comerce_34606612`.`terceros` `t4` where (`t4`.`ID` = `t1`.`tercero_id`) limit 1) AS `nombre_tercero`,(select `t4`.`identificacion` from `techno_ts6_comerce_34606612`.`terceros` `t4` where (`t4`.`ID` = `t1`.`tercero_id`) limit 1) AS `nit_tercero`,(select concat(`t5`.`Nombre`,' ',`t5`.`Apellido`) from `techno_ts6_comerce`.`usuarios` `t5` where (`t5`.`idUsuarios` = `t1`.`usuario_id`) limit 1) AS `nombre_usuario` from `techno_ts6_comerce_34606612`.`documentos_electronicos` `t1` order by `t1`.`updated` desc;

-- 2020-10-11 17:47:10
