CREATE TABLE IF NOT EXISTS `tickets` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `departamento_id` int(11) NOT NULL,
  `TipoTicket` int(11) NOT NULL,
  `idModuloProyecto` int(11) NOT NULL,
  `Prioridad` int(11) NOT NULL,
  `FechaApertura` datetime NOT NULL,
  `Asunto` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `Estado` int(11) NOT NULL,
  `idUsuarioSolicitante` int(11) NOT NULL,
  `idUsuarioAsignado` int(11) NOT NULL,
  `FechaActualizacion` datetime NOT NULL,
  `idUsuarioActualiza` int(11) NOT NULL,
  `FechaCierre` datetime NOT NULL,
  `idUsuarioCierra` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `TipoTicket` (`TipoTicket`),
  KEY `idProyecto` (`departamento_id`),
  KEY `idModuloProyecto` (`idModuloProyecto`),
  KEY `Estado` (`Estado`),
  KEY `Asunto` (`Asunto`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS `tickets_adjuntos` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `archivo_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Ruta` varchar(300) COLLATE utf8_spanish_ci NOT NULL,
  `NombreArchivo` varchar(300) COLLATE utf8_spanish_ci NOT NULL,
  `Extension` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `Tamano` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `mensaje_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `idUser` int(11) NOT NULL,
  `Created` datetime NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `idMensaje` (`mensaje_id`),
  KEY `archivo_id` (`archivo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS `tickets_departamentos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Departamento` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `usuario_asignado` bigint(20) NOT NULL,
  `correo_notificacion_general` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `Estado` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS `tickets_estados` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Estado` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

REPLACE INTO `tickets_estados` (`ID`, `Estado`, `Updated`, `Sync`) VALUES
(1,	'ABIERTO',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(2,	'EN ANALISIS',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(3,	'RESPONDIDO',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(11,	'ARCHIVADO',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(10,	'CERRADO',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(12,	'ELIMINADO',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(4,	'EN DESARROLLO',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41'),
(5,	'EN PRUEBAS',	'2020-07-25 15:06:41',	'2020-07-25 10:06:41');

CREATE TABLE IF NOT EXISTS `tickets_mensajes` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `mensaje_id` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `Mensaje` longtext COLLATE utf8_spanish_ci NOT NULL,
  `Estado` int(11) NOT NULL,
  `Created` datetime NOT NULL,
  `idUser` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`),
  KEY `idTicket` (`ticket_id`),
  KEY `mensaje_id` (`mensaje_id`),
  KEY `Estado` (`Estado`),
  KEY `idUser` (`idUser`),
  FULLTEXT KEY `Mensaje` (`Mensaje`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


CREATE TABLE IF NOT EXISTS `tickets_tipo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TipoTicket` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

REPLACE INTO `tickets_tipo` (`ID`, `TipoTicket`, `Updated`, `Sync`) VALUES
(1,	'Requerimiento',	'2021-03-06 03:58:33',	'0000-00-00 00:00:00'),
(2,	'Hallazgo',	'2021-03-06 03:58:41',	'0000-00-00 00:00:00'),
(3,	'Solicitud',	'2021-03-06 03:58:48',	'0000-00-00 00:00:00'),
(4,	'Soporte Técnico',	'2021-03-20 15:42:52',	'0000-00-00 00:00:00'),
(5,	'Labor Asignada',	'2021-03-06 04:03:46',	'0000-00-00 00:00:00');