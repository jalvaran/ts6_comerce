ALTER TABLE `contabilidad_plan_cuentas_cuentas`
ADD  `consecutivo` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY AFTER `Cuenta`;

DELETE t1 FROM contabilidad_plan_cuentas_cuentas t1
INNER JOIN contabilidad_plan_cuentas_cuentas t2 
WHERE t1.consecutivo > t2.consecutivo AND t1.ID = t2.ID; 

ALTER TABLE contabilidad_plan_cuentas_cuentas ADD UNIQUE INDEX (ID);