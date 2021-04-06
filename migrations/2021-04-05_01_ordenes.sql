CREATE TABLE `ordenes_servicio` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `orden_servicio_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `fecha_orden` date NOT NULL,
  `tercero_id` bigint(20) NOT NULL,
  `direccion` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `municipio` bigint(20) NOT NULL COMMENT 'codigo dane de la ciudad',
  `fecha_ejecucion_inicial` date NOT NULL,
  `fecha_ejecucion_final` date NOT NULL,
  `usuario_asignado` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_cierre` date NOT NULL,
  `usuario_id_cierre` int(11) NOT NULL,
  `observaciones_iniciales` text COLLATE utf8_spanish_ci NOT NULL,
  `observaciones_finales` text COLLATE utf8_spanish_ci NOT NULL,
  `estado` int(11) NOT NULL,
  `usuario_anulacion` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `orden_id` (`orden_servicio_id`),
  KEY `usuario_asignado` (`usuario_asignado`),
  KEY `estado` (`estado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `ordenes_servicio_catalogo_insumos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `referencia` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `costo_unitario` double NOT NULL,
  `venta_unitario` double NOT NULL,
  `porcentajes_iva_id` int(11) NOT NULL,
  `tipo_insumo` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `tipo_insumo` (`tipo_insumo`),
  KEY `referencia` (`referencia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `ordenes_servicio_catalogo_insumos_tipo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_insumo` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `maneja_inventario` int(11) NOT NULL COMMENT '0 no maneja inventario, 1 si maneja inventario',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `ordenes_servicio_catalogo_insumos_tipo` (`ID`, `tipo_insumo`, `maneja_inventario`) VALUES
(1,	'materiales',	1),
(2,	'productos',	1),
(3,	'servicio',	0);

CREATE TABLE `ordenes_servicio_catalogo_tecnicos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `identificacion` bigint(20) NOT NULL,
  `nombre_completo` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `cargo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `estado` int(11) NOT NULL COMMENT '1 activo, 0 inactivo',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE `ordenes_servicio_estados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `estado_orden` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `ordenes_servicio_estados` (`ID`, `estado_orden`) VALUES
(1,	'Abierta'),
(2,	'En Ejecucion'),
(3,	'Cerrada'),
(10,	'Anulada');

CREATE TABLE `ordenes_servicio_insumos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `tipo_registro` int(11) NOT NULL COMMENT '1 entregado, 2 utilizado, 3 retornado',
  `orden_servicio_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `insumo_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `cantidad` double NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` datetime NOT NULL,
  `user_delete` int(11) NOT NULL COMMENT 'usuario que borra el registro',
  PRIMARY KEY (`ID`),
  KEY `insumo_id` (`insumo_id`),
  KEY `tipo_registro` (`tipo_registro`),
  KEY `orden_servicio_id` (`orden_servicio_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `formatos_calidad` (`ID`, `Nombre`, `Version`, `Codigo`, `Fecha`, `CuerpoFormato`, `NotasPiePagina`, `Updated`, `Sync`) VALUES
(100,	'ORDEN DE SERVICIO',	'001',	'F-GC-004',	'2021-02-16',	'',	'',	'2021-02-17 10:43:25',	'2020-07-25 10:03:57'),
(101,	'ORDEN DE SERVICIO ENTREGA DE INSUMOS O SUMINISTROS',	'001',	'F-GC-005',	'2021-02-16',	'',	'',	'2021-02-17 10:43:25',	'2020-07-25 10:03:57'),
(102,	'ORDEN DE SERVICIO DEVOLUCION DE INSUMOS O SUMINISTROS',	'001',	'F-GC-006',	'2021-02-16',	'',	'',	'2021-04-05 11:13:18',	'2020-07-25 10:03:57');