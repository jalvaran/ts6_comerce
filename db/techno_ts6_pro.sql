-- Adminer 4.7.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `backups`;
CREATE TABLE `backups` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre del backup',
  `fecha_creacion` datetime NOT NULL COMMENT 'Fecha Creacion',
  `fecha_finalizacion` datetime NOT NULL COMMENT 'Fecha de finalizacion del backup',
  `servidor_id_origen` int(11) NOT NULL COMMENT 'id del servidor al cual se le quiere realizar el backup',
  `servidor_id_destino` int(11) NOT NULL COMMENT 'id del servidor donde se desea respaldar la informacion',
  `prefijo_origen` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'prefijo con el cual se identifican las base de datos en el servidor origen',
  `prefijo_destino` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'prefijo de las base de datos en el servidor destino',
  `limite_registros` int(11) NOT NULL COMMENT 'limite que se usara para copiar los registros',
  `estado` int(11) NOT NULL COMMENT 'estado del backup',
  PRIMARY KEY (`ID`),
  KEY `servidor_id_origen` (`servidor_id_origen`),
  KEY `servidor_id_destino` (`servidor_id_destino`),
  KEY `prefijo_origen` (`prefijo_origen`),
  KEY `prefijo_destino` (`prefijo_destino`),
  KEY `estado` (`estado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `backups_bases_datos`;
CREATE TABLE `backups_bases_datos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `backups_id` bigint(20) NOT NULL COMMENT 'id del backup',
  `nombre_base_datos` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre de la base de datos',
  `estado` int(11) NOT NULL COMMENT 'estado en que se encuentra la base de datos',
  `fecha_registro` datetime NOT NULL,
  `fecha_finalizacion` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `backups_id` (`backups_id`),
  KEY `estado` (`estado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `backups_bases_datos_estados`;
CREATE TABLE `backups_bases_datos_estados` (
  `ID` int(11) NOT NULL,
  `estado_basedatos` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `backups_bases_datos_tablas`;
CREATE TABLE `backups_bases_datos_tablas` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `base_datos_id` bigint(20) NOT NULL COMMENT 'id de la base de datos',
  `nombre_tabla` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre de la tabla',
  `Table_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'define si es una tabla o una vista',
  `estructura_tabla` mediumtext CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'estructura de la tabla',
  `total_registros` bigint(20) NOT NULL COMMENT 'total de registros encontrados',
  `limite_para_copia` int(11) NOT NULL COMMENT 'limite usado para copiar los registros',
  `total_paginas` int(11) NOT NULL COMMENT 'total de paginas de los registros a respaldar',
  `paginas_copiadas` int(11) NOT NULL COMMENT 'total de paginas de los registros copiados',
  `estado` int(11) NOT NULL,
  `inicia` datetime NOT NULL COMMENT 'indica fecha y hora de inicializacion de la copia',
  `finaliza` datetime NOT NULL COMMENT 'indica fecha y hora de finalizacion de la copia',
  PRIMARY KEY (`ID`),
  KEY `base_datos_id` (`base_datos_id`),
  KEY `Table_type` (`Table_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `backups_bases_datos_tablas_estados`;
CREATE TABLE `backups_bases_datos_tablas_estados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `estado_tabla` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `backups_estados`;
CREATE TABLE `backups_estados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `estado_backup` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'estado del backup',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `backups_servidores`;
CREATE TABLE `backups_servidores` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre del servidor',
  `direccion` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'direccion de conexion',
  `usuario` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'usuario',
  `contrasena` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'contrasena',
  `basedatos` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'base de datos de entrada',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `catalogo_causas`;
CREATE TABLE `catalogo_causas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `falla_id` int(11) NOT NULL DEFAULT '0' COMMENT 'id de la falla',
  `Causa` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'Causante de la falla',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `catalogo_causas` (`ID`, `falla_id`, `Causa`) VALUES
(1,	1,	'Deformación o contractura'),
(2,	1,	'Componentes o conexiones flojas'),
(3,	1,	'Pegado o atascado'),
(4,	1,	'Lubricación deficiente'),
(5,	1,	'Fuga de líquido o gas'),
(6,	1,	'Vibración anormal'),
(7,	1,	'Alineación o separación incorrecta'),
(8,	2,	'Cavitación'),
(9,	2,	'Corrosión'),
(10,	2,	'Desgaste'),
(11,	2,	'Fractura'),
(12,	2,	'Fatiga'),
(13,	2,	'Sobrecalentamiento'),
(14,	2,	'Explosión'),
(15,	3,	'Cortocircuito'),
(16,	3,	'Circuito abierto por disparo/interrupción '),
(17,	3,	'No hay energía/voltaje'),
(18,	3,	'Fallas en el suministro de energía'),
(19,	3,	'Falla en tierra o aislamiento'),
(20,	4,	'Falla en el control'),
(21,	4,	'Señal indicación o alarma fallida'),
(22,	4,	'Error en la calibración'),
(23,	4,	'Falla en el software'),
(24,	4,	'Fallas de modo común'),
(25,	5,	'Capacidad insuficiente'),
(26,	5,	'Material inadecuado'),
(27,	5,	'Diseño inapropiado'),
(28,	5,	'Error de fabricación o montaje'),
(29,	6,	'Error operativo'),
(30,	6,	'Falla de mantenimiento'),
(31,	6,	'Desgaste o rupturas esperadas'),
(32,	7,	'Error en la documentación'),
(33,	7,	'Error administrativo'),
(34,	7,	'Evento externo contaminación'),
(35,	7,	'Bloqueo o atoramiento externo'),
(36,	7,	'Influencias externas varias'),
(37,	7,	'Causa desconocida');

DROP TABLE IF EXISTS `catalogo_fallas`;
CREATE TABLE `catalogo_fallas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Falla` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Nombre de la Falla',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `catalogo_fallas` (`ID`, `Falla`) VALUES
(1,	'Falla Mecánica'),
(2,	'Falla de Material'),
(3,	'Falla Eléctrica'),
(4,	'Falla de Instrumentación'),
(5,	'Falla de diseño/selección'),
(6,	'Falla de operación/mantenimiento'),
(7,	'Fallas diversas');

DROP TABLE IF EXISTS `configuracion_general`;
CREATE TABLE `configuracion_general` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` text CHARACTER SET utf8 COLLATE utf8_spanish_ci COMMENT 'Descripcion del registro',
  `Valor` text CHARACTER SET utf8 COLLATE utf8_spanish_ci COMMENT 'Valor del registro',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'ultima actualizacion del registro',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'creacion',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `configuracion_general` (`ID`, `Descripcion`, `Valor`, `updated`, `created`) VALUES
(3000,	'ruta para guardar los adjuntos del ts6 pro',	'../../../adjuntos/',	'2020-07-20 18:43:14',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `empresapro`;
CREATE TABLE `empresapro` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RazonSocial` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `NIT` bigint(20) DEFAULT NULL,
  `DigitoVerificacion` int(1) NOT NULL DEFAULT '0',
  `Direccion` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Barrio` varchar(70) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Celular` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Ciudad` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `CodigoDaneCiudad` int(5) unsigned zerofill NOT NULL DEFAULT '00000',
  `ResolucionDian` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `Regimen` enum('SIMPLIFICADO','COMUN') CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT 'SIMPLIFICADO',
  `TipoPersona` enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL COMMENT '1 Persona jurica, 2 persona natural,3 grandes contribuyentes',
  `TipoDocumento` int(11) NOT NULL DEFAULT '0',
  `MatriculoMercantil` bigint(20) NOT NULL DEFAULT '0',
  `ActividadesEconomicas` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `Email` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `WEB` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `ObservacionesLegales` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `PuntoEquilibrio` bigint(20) NOT NULL DEFAULT '0',
  `DatosBancarios` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `RutaImagen` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'LogosEmpresas/logotipo1.png',
  `FacturaSinInventario` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `CXPAutomaticas` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'SI',
  `TokenAPIFE` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `db` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Estado` int(11) NOT NULL DEFAULT '1',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `empresapro` (`ID`, `RazonSocial`, `NIT`, `DigitoVerificacion`, `Direccion`, `Barrio`, `Telefono`, `Celular`, `Ciudad`, `CodigoDaneCiudad`, `ResolucionDian`, `Regimen`, `TipoPersona`, `TipoDocumento`, `MatriculoMercantil`, `ActividadesEconomicas`, `Email`, `WEB`, `ObservacionesLegales`, `PuntoEquilibrio`, `DatosBancarios`, `RutaImagen`, `FacturaSinInventario`, `CXPAutomaticas`, `TokenAPIFE`, `db`, `Estado`, `Updated`, `Sync`) VALUES
(1,	'TECHNO SOLUCIONES SAS',	900833180,	7,	'CALLE 6 SUR 13 B 66',	NULL,	'3177740609',	'3177740609',	'BUGA',	76111,	NULL,	'COMUN',	'1',	31,	0,	NULL,	'jalvaran@gmail.com',	'www.ni.com',	NULL,	0,	NULL,	'LogosEmpresas/logotipo1.png',	NULL,	'SI',	NULL,	'techno_ts6_pro_1',	1,	'2020-07-11 02:53:03',	'0000-00-00 00:00:00'),
(2,	'PRODG',	900303583,	8,	'Cali',	NULL,	'por alli',	'si',	'cali',	03213,	NULL,	'COMUN',	'1',	13,	0,	NULL,	'ese',	'esa',	NULL,	0,	NULL,	'LogosEmpresas/logotipo1.png',	NULL,	'SI',	NULL,	'techno_ts6_pro_3',	1,	'2020-07-11 22:09:41',	'0000-00-00 00:00:00'),
(3,	'Terranova Servicios S.A E.S.P',	805028418,	7,	'Calle 17 No 50 Sur - 22',	NULL,	' ',	' ',	'CALI',	76001,	NULL,	'COMUN',	'1',	31,	0,	NULL,	'terranova@gmail.com',	'www.ni.com',	NULL,	0,	NULL,	'LogosEmpresas/logotipo1.png',	NULL,	'SI',	NULL,	'techno_ts6_pro_3',	1,	'2020-07-11 02:53:03',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `formatos_calidad`;
CREATE TABLE `formatos_calidad` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Version` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Codigo` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Fecha` date NOT NULL,
  `CuerpoFormato` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `NotasPiePagina` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `formatos_calidad` (`ID`, `Nombre`, `Version`, `Codigo`, `Fecha`, `CuerpoFormato`, `NotasPiePagina`, `Updated`, `Sync`) VALUES
(3000,	'ORDEN DE TRABAJO',	'001',	'F-GA-001',	'2020-07-12',	'',	'',	'2020-07-21 09:45:44',	'2019-01-13 09:11:00'),
(3001,	'INDICADORES DE MANTENIMIENTO',	'001',	'F-GA-002',	'2020-07-12',	'',	'',	'2020-07-21 09:45:44',	'2019-01-13 09:11:00');

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(80) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `idCarpeta` int(11) NOT NULL DEFAULT '0',
  `Pagina` varchar(80) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Target` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '_SELF',
  `Estado` int(1) NOT NULL DEFAULT '1',
  `Image` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `CSS_Clase` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Orden` int(11) NOT NULL DEFAULT '1',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `menu` (`ID`, `Nombre`, `idCarpeta`, `Pagina`, `Target`, `Estado`, `Image`, `CSS_Clase`, `Orden`, `Updated`, `Sync`) VALUES
(1,	'Administrar',	2,	'Menu.php?menu_id=1',	'_SELF',	1,	'admin.png',	'icon icon-user',	1,	'2020-07-10 20:47:24',	'0000-00-00 00:00:00'),
(2,	'Parametros',	2,	'Menu.php?menu_id=2',	'_SELF',	1,	'configuracion.png',	'fab fa-whmcs',	2,	'2020-08-07 00:07:01',	'0000-00-00 00:00:00'),
(3,	'Mantenimiento',	2,	'Menu.php?menu_id=3',	'_SELF',	1,	'servicios.png',	'fa fa-warehouse',	3,	'2020-07-15 00:00:38',	'0000-00-00 00:00:00'),
(4,	'Informes',	2,	'Menu.php?menu_id=4',	'_SELF',	1,	'informes5.png',	'fa fa-book',	4,	'2020-07-15 00:00:38',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `menu_carpetas`;
CREATE TABLE `menu_carpetas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Ruta` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `menu_carpetas` (`ID`, `Ruta`, `Updated`, `Sync`) VALUES
(1,	NULL,	'2020-07-09 16:47:44',	'0000-00-00 00:00:00'),
(2,	'../../modulos/menu/',	'2020-07-09 16:48:23',	'0000-00-00 00:00:00'),
(3,	'../../modulos/admin/',	'2020-07-09 16:48:23',	'0000-00-00 00:00:00'),
(4,	'../../modulos/equipos/',	'2020-07-09 16:48:23',	'0000-00-00 00:00:00'),
(5,	'../../modulos/catalogos/',	'2020-07-09 16:48:23',	'0000-00-00 00:00:00'),
(6,	'../../modulos/mantenimiento/',	'2020-07-09 16:48:23',	'0000-00-00 00:00:00'),
(7,	'../../modulos/informes_mantenimiento/',	'2020-07-09 16:48:23',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `menu_pestanas`;
CREATE TABLE `menu_pestanas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `idMenu` int(11) NOT NULL DEFAULT '0',
  `Orden` int(11) NOT NULL DEFAULT '0',
  `Estado` bit(1) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `menu_pestanas` (`ID`, `Nombre`, `idMenu`, `Orden`, `Estado`, `Updated`, `Sync`) VALUES
(1,	'Software',	1,	1,	CONV('1', 2, 10) + 0,	'2020-07-09 16:49:40',	'0000-00-00 00:00:00'),
(2,	'Parámetros',	2,	1,	CONV('1', 2, 10) + 0,	'2020-08-07 00:06:56',	'0000-00-00 00:00:00'),
(3,	'Catálogos',	2,	1,	CONV('0', 2, 10) + 0,	'2020-08-07 00:05:44',	'0000-00-00 00:00:00'),
(4,	'Mantenimiento',	3,	1,	CONV('1', 2, 10) + 0,	'2020-08-06 23:39:07',	'0000-00-00 00:00:00'),
(5,	'Informes',	4,	1,	CONV('1', 2, 10) + 0,	'2020-07-11 21:10:49',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `menu_submenus`;
CREATE TABLE `menu_submenus` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `idPestana` int(11) NOT NULL DEFAULT '0',
  `idCarpeta` int(11) NOT NULL DEFAULT '0',
  `idMenu` int(11) NOT NULL DEFAULT '0',
  `TablaAsociada` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoLink` int(1) NOT NULL DEFAULT '0',
  `JavaScript` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Pagina` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Target` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Estado` int(1) NOT NULL DEFAULT '0',
  `Image` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Orden` int(11) NOT NULL DEFAULT '0',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `menu_submenus` (`ID`, `Nombre`, `idPestana`, `idCarpeta`, `idMenu`, `TablaAsociada`, `TipoLink`, `JavaScript`, `Pagina`, `Target`, `Estado`, `Image`, `Orden`, `Updated`, `Sync`) VALUES
(1,	'Empresas',	1,	3,	1,	NULL,	0,	NULL,	'admin_empresas.php',	'_self',	1,	'empresa.png',	1,	'2020-07-09 16:51:42',	'0000-00-00 00:00:00'),
(2,	'Maquinas',	2,	4,	2,	NULL,	0,	NULL,	'admin_equipos.php',	'_self',	1,	'settings.gif',	3,	'2020-08-13 13:22:06',	'0000-00-00 00:00:00'),
(3,	'Catalogos Empresa',	2,	5,	2,	NULL,	0,	NULL,	'admin_catalogos.php',	'_self',	1,	'listasprecios.png',	1,	'2020-08-13 13:21:35',	'0000-00-00 00:00:00'),
(4,	'Ordenes de Trabajo',	4,	6,	3,	NULL,	0,	NULL,	'mantenimiento.php',	'_self',	1,	'ordentrabajo.png',	1,	'2020-07-09 16:51:42',	'0000-00-00 00:00:00'),
(5,	'Hojas de Vida',	5,	7,	4,	NULL,	0,	NULL,	'hojas_vida.php',	'_self',	1,	'kardex_alquiler.png',	1,	'2020-07-09 16:51:42',	'0000-00-00 00:00:00'),
(6,	'Analisis de fallas',	5,	7,	4,	NULL,	0,	NULL,	'analisis_fallas.php',	'_self',	1,	'cerrarturno.png',	2,	'2020-07-23 11:58:47',	'0000-00-00 00:00:00'),
(7,	'Indicadores de Gestion',	5,	7,	4,	NULL,	0,	NULL,	'indicadores_gestion.php',	'_self',	1,	'estadorips.png',	3,	'2020-07-23 11:58:47',	'0000-00-00 00:00:00'),
(9,	'Fallas y Causas',	1,	3,	1,	NULL,	0,	NULL,	'catalogo_fallas.php',	'_self',	1,	'bajaalta.jpg',	2,	'2020-08-08 14:28:19',	'0000-00-00 00:00:00'),
(10,	'Mtto Correctivo',	4,	6,	3,	NULL,	0,	NULL,	'mantenimiento_correctivo.php',	'_self',	1,	'documentos_contables.png',	2,	'2020-07-09 16:51:42',	'0000-00-00 00:00:00'),
(11,	'Plan Mtto Preventivo',	4,	6,	3,	NULL,	0,	NULL,	'mantenimiento_preventivo.php',	'_self',	1,	'gestion.png',	3,	'2020-07-09 16:51:42',	'0000-00-00 00:00:00'),
(12,	'Rutinas',	4,	6,	3,	NULL,	0,	NULL,	'mantenimiento_rutinas.php',	'_self',	1,	'modelos_admin.png',	4,	'2020-07-09 16:51:42',	'0000-00-00 00:00:00'),
(13,	'Catalogos Tecnicos',	2,	5,	2,	NULL,	0,	NULL,	'admin_catalogos_tecnicos.php',	'_self',	1,	'inventarios_titulos.png',	2,	'2020-08-13 13:22:52',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `ordenes_trabajo_estados`;
CREATE TABLE `ordenes_trabajo_estados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_estado` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'estado de la orden de trabajo',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `ordenes_trabajo_estados` (`ID`, `nombre_estado`) VALUES
(1,	'Vencidas'),
(2,	'Abierta'),
(3,	'Cerradas'),
(4,	'Facturada'),
(10,	'Anulada');

DROP TABLE IF EXISTS `ordenes_trabajo_tipo_mantenimiento`;
CREATE TABLE `ordenes_trabajo_tipo_mantenimiento` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_mantenimiento` varchar(25) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `ordenes_trabajo_tipo_mantenimiento` (`ID`, `tipo_mantenimiento`) VALUES
(1,	'Correctivo'),
(2,	'Preventivo'),
(3,	'Rutinas');

DROP TABLE IF EXISTS `paginas_bloques`;
CREATE TABLE `paginas_bloques` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TipoUsuario` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Pagina` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Habilitado` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'SI',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `Pagina` (`Pagina`),
  KEY `Habilitado` (`Habilitado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `paginas_bloques` (`ID`, `TipoUsuario`, `Pagina`, `Habilitado`, `Updated`, `Sync`) VALUES
(1,	'gerente',	'Menu.php',	'SI',	'2020-08-06 21:32:24',	'0000-00-00 00:00:00'),
(2,	'gerente',	'Menu.php?menu_id=2',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(3,	'gerente',	'Menu.php?menu_id=3',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(4,	'gerente',	'Menu.php?menu_id=4',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(5,	'gerente',	'admin_equipos.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(6,	'gerente',	'admin_catalogos.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(7,	'gerente',	'mantenimiento.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(8,	'gerente',	'hojas_vida.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(9,	'gerente',	'analisis_fallas.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(10,	'gerente',	'indicadores_gestion.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(11,	'gerente',	'indicadores_gestion.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(12,	'gerente',	'admin_catalogos_tecnicos.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(13,	'gerente',	'mantenimiento_correctivo.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(14,	'gerente',	'mantenimiento_preventivo.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00'),
(15,	'gerente',	'mantenimiento_rutinas.php',	'SI',	'2020-08-06 21:39:24',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `tablas_campos_asociados`;
CREATE TABLE `tablas_campos_asociados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TablaOrigen` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `CampoTablaOrigen` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `TablaAsociada` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `CampoAsociado` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `IDCampoAsociado` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `TablaOrigen` (`TablaOrigen`),
  KEY `CampoTablaOrigen` (`CampoTablaOrigen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `tablas_campos_asociados` (`ID`, `TablaOrigen`, `CampoTablaOrigen`, `TablaAsociada`, `CampoAsociado`, `IDCampoAsociado`, `Updated`, `Sync`) VALUES
(1,	'catalogo_procesos',	'unidadNegocio_id',	'catalogo_unidades_negocio ',	'UnidadNegocio',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00'),
(2,	'equipos_maquinas',	'proceso_id',	'catalogo_procesos',	'Nombre',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00'),
(3,	'equipos_maquinas',	'ubicacion_id',	'catalogo_secciones',	'NombreSeccion',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00'),
(4,	'equipos_maquinas',	'representante_id',	'catalogo_representante',	'NombreRepresentante',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00'),
(5,	'equipos_componentes',	'maquina_id',	'equipos_maquinas',	'Nombre',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00'),
(6,	'catalogo_tareas',	'TipoTarea',	'catalogo_tareas_tipos',	'tipo_tarea',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00'),
(7,	'catalogo_causas',	'falla_id',	'catalogo_fallas',	'Falla',	'ID',	'2020-07-12 13:14:29',	'0000-00-00 00:00:00');

DROP TABLE IF EXISTS `tablas_campos_control`;
CREATE TABLE `tablas_campos_control` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NombreTabla` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Campo` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Visible` int(1) NOT NULL,
  `Editable` int(1) NOT NULL,
  `Habilitado` int(1) NOT NULL,
  `TipoUser` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `Campo` (`Campo`),
  KEY `NombreTabla` (`NombreTabla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `tablas_campos_control` (`ID`, `NombreTabla`, `Campo`, `Visible`, `Editable`, `Habilitado`, `TipoUser`, `idUser`, `Updated`, `Sync`) VALUES
(1,	'usuarios',	'Password',	0,	1,	0,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(3,	'usuarios',	'Nombre',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(4,	'usuarios',	'Apellido',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(6,	'usuarios',	'Sync',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(7,	'usuarios',	'idUsuarios',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(8,	'usuarios',	'Updated',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(9,	'usuarios',	'Telefono',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(10,	'usuarios',	'Login',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(11,	'usuarios',	'Role',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(12,	'usuarios',	'Identificacion',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(13,	'clientes',	'CIUU',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(14,	'clientes',	'Lugar_Expedicion_Documento',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(15,	'clientes',	'Cod_Dpto',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(16,	'clientes',	'Pais_Domicilio',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(17,	'clientes',	'Cod_Mcipio',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(18,	'clientes',	'Contacto',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(19,	'clientes',	'TelContacto',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(20,	'clientes',	'Soporte',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(21,	'clientes',	'Updated',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(22,	'clientes',	'Sync',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(27,	'usuarios',	'TipoUser',	0,	1,	1,	'administrador',	3,	'2019-02-01 21:23:16',	'2019-02-01 16:23:16'),
(28,	'usuarios',	'Email',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(29,	'clientes',	'Tipo_Documento',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(30,	'clientes',	'Telefono',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(31,	'clientes',	'Ciudad',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(32,	'clientes',	'Email',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(33,	'clientes',	'Cupo',	1,	1,	1,	'administrador',	3,	'2020-03-05 13:21:00',	'2019-01-13 09:14:12'),
(34,	'costos',	'Updated',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(35,	'costos',	'Sync',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(36,	'costos',	'ValorCosto',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(37,	'costos',	'idCostos',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(38,	'costos',	'NombreCosto',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(39,	'facturas',	'idResolucion',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(40,	'facturas',	'TipoFactura',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(41,	'facturas',	'OCompra',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(42,	'facturas',	'OSalida',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(43,	'facturas',	'Descuentos',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(44,	'facturas',	'Cotizaciones_idCotizaciones',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(45,	'facturas',	'EmpresaPro_idEmpresaPro',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(46,	'facturas',	'CentroCosto',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(47,	'facturas',	'idSucursal',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(48,	'facturas',	'Usuarios_idUsuarios',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(49,	'facturas',	'CerradoDiario',	1,	1,	1,	'administrador',	3,	'2019-05-06 12:44:22',	'2019-05-06 07:44:22'),
(50,	'facturas',	'FechaCierreDiario',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(51,	'facturas',	'HoraCierreDiario',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(52,	'facturas',	'Efectivo',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(53,	'facturas',	'Devuelve',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(54,	'facturas',	'Cheques',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(55,	'facturas',	'Otros',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(56,	'facturas',	'Tarjetas',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(57,	'facturas',	'idTarjetas',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(58,	'facturas',	'Updated',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(59,	'facturas',	'Sync',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(60,	'modelos_db',	'Updated',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(61,	'modelos_db',	'Sync',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(62,	'vista_cierres_restaurante',	'Fecha',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(63,	'documentos_contables_items',	'idDocumento',	0,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(64,	'empresapro',	'CXPAutomaticas',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(65,	'empresapro',	'FacturaSinInventario',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(66,	'empresapro',	'RutaImagen',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(67,	'empresapro',	'DatosBancarios',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(68,	'empresapro',	'PuntoEquilibrio',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(69,	'empresapro',	'ObservacionesLegales',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(70,	'empresapro',	'WEB',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(71,	'empresapro',	'Email',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(72,	'empresapro',	'MatriculoMercantil',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(73,	'empresapro',	'Regimen',	1,	1,	1,	'administrador',	3,	'2019-01-13 14:14:12',	'2019-01-13 09:14:12'),
(74,	'vista_balancextercero2',	'idCentroCosto',	0,	1,	1,	'administrador',	3,	'2019-04-25 18:56:17',	'2019-04-25 13:56:17'),
(75,	'vista_balancextercero2',	'idEmpresa',	0,	1,	1,	'administrador',	3,	'2019-04-25 18:56:17',	'2019-04-25 13:56:17'),
(76,	'librodiario',	'Num_Documento_Externo',	1,	1,	1,	'administrador',	3,	'2019-05-30 16:20:05',	'2019-01-16 09:43:47'),
(77,	'librodiario',	'Tercero_Tipo_Documento',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:43:47',	'2019-01-16 09:43:47'),
(78,	'librodiario',	'Tercero_Identificacion',	1,	1,	1,	'administrador',	3,	'2019-03-02 04:40:40',	'2019-03-01 23:40:40'),
(79,	'librodiario',	'Tercero_DV',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:43:47',	'2019-01-16 09:43:47'),
(80,	'librodiario',	'Tercero_Primer_Apellido',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:43:50',	'2019-01-16 09:43:50'),
(81,	'librodiario',	'Tercero_Segundo_Apellido',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(82,	'librodiario',	'Tercero_Primer_Nombre',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(83,	'librodiario',	'Tercero_Otros_Nombres',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(84,	'librodiario',	'Tercero_Razon_Social',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(85,	'librodiario',	'Tercero_Direccion',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(86,	'librodiario',	'Tercero_Cod_Dpto',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(87,	'librodiario',	'Tercero_Cod_Mcipio',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(88,	'librodiario',	'Tercero_Pais_Domicilio',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(89,	'librodiario',	'Mayor',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(90,	'librodiario',	'Esp',	0,	1,	1,	'administrador',	3,	'2019-01-16 14:44:54',	'2019-01-16 09:44:54'),
(91,	'librodiario',	'Detalle',	1,	1,	1,	'administrador',	3,	'2019-01-16 15:04:57',	'2019-01-16 10:04:57'),
(92,	'librodiario',	'Concepto',	0,	1,	1,	'administrador',	3,	'2019-04-09 17:18:01',	'2019-04-09 12:18:01'),
(93,	'librodiario',	'Tipo_Documento_Intero',	1,	1,	1,	'administrador',	3,	'2019-03-02 04:40:40',	'2019-03-01 23:40:40'),
(94,	'productosventa',	'ValorComision3',	1,	1,	1,	'administrador',	3,	'2019-09-26 22:48:32',	'2019-02-26 17:22:26'),
(95,	'productosventa',	'ValorComision2',	1,	1,	1,	'administrador',	3,	'2019-09-26 22:48:32',	'2019-02-26 17:22:26'),
(96,	'productosventa',	'ValorComision1',	1,	1,	1,	'administrador',	3,	'2019-09-26 22:48:31',	'2019-02-26 17:22:26'),
(97,	'librodiario',	'Num_Documento_Interno',	1,	1,	1,	'administrador',	3,	'2019-03-02 04:40:40',	'2019-03-01 23:40:40'),
(98,	'facturas',	'Prefijo',	0,	1,	1,	'administrador',	3,	'2019-03-13 14:16:10',	'2019-03-13 09:16:10'),
(99,	'facturas',	'NumeroFactura',	1,	1,	1,	'administrador',	3,	'2019-10-18 02:34:41',	'2019-03-13 09:16:10'),
(100,	'facturas',	'Fecha',	0,	1,	1,	'administrador',	3,	'2019-03-13 14:16:10',	'2019-03-13 09:16:10'),
(101,	'empresapro',	'CodigoDaneCiudad',	1,	1,	1,	'administrador',	3,	'2020-07-09 21:00:18',	'2019-01-13 09:14:12'),
(102,	'empresapro',	'Barrio',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(103,	'empresapro',	'ResolucionDian',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(104,	'empresapro',	'TokenAPIFE',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(105,	'empresapro',	'ActividadesEconomicas',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(106,	'empresapro',	'db',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(107,	'empresapro',	'DigitoVerificacion',	1,	1,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(108,	'equipos_componentes',	'fecha_ultimo_mantenimiento',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(109,	'equipos_componentes',	'frecuencia_mtto_dias',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(110,	'equipos_componentes',	'frecuencia_verificacion_dias',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(111,	'equipos_componentes',	'dias_ultimo_mantenimiento',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(112,	'equipos_componentes',	'dias_trabajo',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(113,	'equipos_componentes',	'frecuencia_mtto_horas',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(114,	'equipos_componentes',	'horas_ultimo_mantenimiento',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(115,	'equipos_componentes',	'horas_trabajo',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(116,	'equipos_componentes',	'frecuencia_mtto_kilometros',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(117,	'equipos_componentes',	'kilometros_ultimo_mantenimiento',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(118,	'equipos_componentes',	'kilometros_trabajo',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(119,	'equipos_componentes',	'usuario_id_create',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12'),
(120,	'equipos_componentes',	'usuario_id_update',	0,	0,	0,	'administrador',	3,	'2020-07-09 20:56:15',	'2019-01-13 09:14:12');

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `idUsuarios` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Apellido` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Identificacion` varchar(40) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Telefono` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Login` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Password` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `TipoUser` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Cargo` int(5) unsigned zerofill NOT NULL,
  `Proceso` int(5) unsigned zerofill NOT NULL,
  `Email` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `Role` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Habilitado` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'SI',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idUsuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuarios` (`idUsuarios`, `Nombre`, `Apellido`, `Identificacion`, `Telefono`, `Login`, `Password`, `TipoUser`, `Cargo`, `Proceso`, `Email`, `Role`, `Habilitado`, `Updated`, `Sync`) VALUES
(1,	'TECHNO ',	'SOLUCIONES',	'900833180',	'3177740609',	'admin',	'techno',	'administrador',	00000,	00000,	'info@technosoluciones.com',	'SUPERVISOR',	'SI',	'2019-04-27 15:38:18',	'2019-04-27 10:38:18'),
(2,	'ADMINISTRADOR',	'SOFTCONTECH',	'1',	'1',	'administrador',	'91f5167c34c400758115c2a6826ec2e3',	'operador',	00000,	00000,	'no@no.com',	'SUPERVISOR',	'SI',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(3,	'JULIAN ANDRES',	'ALVARAN',	'94481747',	'3177740609',	'jalvaran',	'pirlo1985',	'administrador',	00000,	00000,	'jalvaran@gmail.com',	'SUPERVISOR',	'SI',	'2020-08-06 21:44:30',	'2019-04-08 15:35:50'),
(4,	'ARLEY',	'QUIJANO',	'1',	'1',	'aquijano',	'quimar',	'gerente',	00000,	00000,	'no',	'SUPERVISOR',	'SI',	'2020-08-13 13:25:12',	'2019-01-13 09:14:14');

DROP TABLE IF EXISTS `usuarios_rel_empresas`;
CREATE TABLE `usuarios_rel_empresas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL DEFAULT '0',
  `empresa_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `usuario_id` (`usuario_id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuarios_rel_empresas` (`ID`, `usuario_id`, `empresa_id`) VALUES
(1,	4,	3),
(2,	3,	3);

DROP TABLE IF EXISTS `usuarios_tipo`;
CREATE TABLE `usuarios_tipo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuarios_tipo` (`ID`, `Tipo`, `Updated`, `Sync`) VALUES
(1,	'administrador',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(2,	'operador',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(3,	'comercial',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(4,	'cajero',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14'),
(5,	'bodega',	'2019-01-13 14:14:14',	'2019-01-13 09:14:14');

-- 2020-08-13 23:49:00
