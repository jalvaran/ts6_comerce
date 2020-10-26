CREATE TABLE `empresa_centro_costo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `centro_costo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `empresa_centro_costo` (`ID`, `centro_costo`, `usuario_id`, `updated`, `created`) VALUES
(1,	'CENTRO DE COSTOS PRINCIPAL',	1,	'2020-10-16 13:58:55',	'2020-10-16 08:58:55');

CREATE TABLE `empresa_sucursales` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Sucursal` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `empresa_sucursales` (`ID`, `Sucursal`, `usuario_id`, `updated`, `created`) VALUES
(1,	'PRINCIPAL',	1,	'2020-10-16 13:59:11',	'2020-10-16 08:59:11');