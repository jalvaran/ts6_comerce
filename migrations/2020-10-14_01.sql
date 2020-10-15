CREATE TABLE IF NOT EXISTS `documentos_electronicos` (
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

CREATE TABLE IF NOT EXISTS `documentos_electronicos_items` (
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


CREATE TABLE IF NOT EXISTS `factura_prefactura` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  `orden_compra` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `forma_pago` int(11) NOT NULL,
  `activa` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS `factura_prefactura_items` (
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


CREATE TABLE IF NOT EXISTS `inventario_items_general` (
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


CREATE TABLE IF NOT EXISTS `terceros` (
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