<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class Contabilidad extends conexion{
    /**
     * Crea la vista para el balance x terceros
     * @param type $Tipo
     * @param type $FechaInicial
     * @param type $FechaFinal
     * @param type $Empresa
     * @param type $CentroCostos
     * @param type $vector
     * @return type
     */
    public function construir_vistas_balance_comprobacion($db,$FechaInicial,$FechaFinal,$CentroCostos,$Tercero,$CuentaContable,$vector){
        
        
        $sql="DROP VIEW IF EXISTS $db.`vista_saldos_iniciales_clase`;";
        $this->Query($sql);
        $sql="DROP VIEW IF EXISTS $db.`vista_movimientos_clase`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS $db.`vista_saldos_iniciales_grupo`;";
        $this->Query($sql);
        $sql="DROP VIEW IF EXISTS $db.`vista_movimientos_grupo`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS $db.`vista_saldos_iniciales_cuenta_padre`;";
        $this->Query($sql);
        $sql="DROP VIEW IF EXISTS $db.`vista_movimientos_cuenta_padre`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS $db.`vista_balancextercero2`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS $db.`vista_saldo_inicial_cuentapuc`;";
        $this->Query($sql);
        
        $CondicionEmpresa="";
        $Condicion=" WHERE ";
        
        
        $Condicion.="Fecha>='$FechaInicial' AND Fecha <='$FechaFinal'";
        $CondicionSaldos=" WHERE Fecha <'$FechaInicial'";
        
        
        $CondicionTercero="";
        if($Tercero<>""){
            $CondicionTercero=" AND Tercero_Identificacion = '$Tercero'";
        }
        $CondicionCuenta="";
        if($CuentaContable<>""){
            $CondicionCuenta=" AND CuentaPUC LIKE '$CuentaContable%'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>""){
            $CondicionCentroCostos=" AND centro_costos_id = '$CentroCostos'";
        }
        
        
        $sql="
            CREATE VIEW $db.vista_saldos_iniciales_clase AS
            SELECT SUBSTRING(CuentaPUC,1,1) as Clase,SUM(Debito - Credito) as SaldoInicialClase FROM $db.contabilidad_librodiario $CondicionSaldos  
              GROUP BY SUBSTRING(CuentaPUC,1,1);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW $db.vista_movimientos_clase AS
            SELECT SUBSTRING(CuentaPUC,1,1) as Clase,SUM(Debito) as DebitosClase,SUM(Credito) as CreditosClase FROM $db.contabilidad_librodiario $Condicion "
                . " GROUP BY SUBSTRING(CuentaPUC,1,1);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW $db.vista_saldos_iniciales_grupo AS
            SELECT SUBSTRING(CuentaPUC,1,2) as Grupo,SUM(Debito - Credito) as SaldoInicialGrupo FROM $db.contabilidad_librodiario $CondicionSaldos  
              GROUP BY SUBSTRING(CuentaPUC,1,2);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW $db.vista_movimientos_grupo AS
            SELECT SUBSTRING(CuentaPUC,1,2) as Grupo,SUM(Debito) as DebitosGrupo,SUM(Credito) as CreditosGrupo FROM $db.contabilidad_librodiario $Condicion "
                . " GROUP BY SUBSTRING(CuentaPUC,1,2);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW $db.vista_saldos_iniciales_cuenta_padre AS
            SELECT SUBSTRING(CuentaPUC,1,4) as CuentaPadre,SUM(Debito - Credito) as SaldoInicialCuentaPadre FROM $db.contabilidad_librodiario $CondicionSaldos  GROUP BY SUBSTRING(CuentaPUC,1,4);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW $db.vista_movimientos_cuenta_padre AS
            SELECT SUBSTRING(CuentaPUC,1,4) as CuentaPadre,SUM(Debito) as DebitosCuentaPadre,SUM(Credito) as CreditosCuentaPadre FROM $db.contabilidad_librodiario $Condicion "
                . " GROUP BY SUBSTRING(CuentaPUC,1,4);";         
        $this->Query($sql);
        
        
        $sql="CREATE VIEW $db.vista_saldo_inicial_cuentapuc AS
            SELECT CuentaPUC as ID,Tercero_Identificacion,SUM(Debito-Credito) as SaldoInicial
            FROM $db.contabilidad_librodiario
            $Condicion $CondicionEmpresa $CondicionCentroCostos
            GROUP BY CuentaPUC,Tercero_Identificacion";         
        $this->Query($sql);
        
        
        
        $sql="CREATE VIEW $db.vista_balancextercero2 AS
            SELECT (SUBSTRING(CuentaPUC,1,8)) as ID,Fecha,`Tercero_Identificacion` as Identificacion,`Tercero_Razon_Social` AS Razon_Social,
            `CuentaPUC` , `NombreCuenta`, Tipo_Documento_Interno as TipoDocumento,Num_Documento_Interno as NumDocumento,Num_Documento_Externo as DocumentoExterno, 
            (SELECT SaldoInicial FROM $db.vista_saldo_inicial_cuentapuc WHERE $db.contabilidad_librodiario.CuentaPUC=$db.vista_saldo_inicial_cuentapuc.ID AND $db.contabilidad_librodiario.Tercero_Identificacion=$db.vista_saldo_inicial_cuentapuc.Tercero_Identificacion LIMIT 1) AS SaldoInicialSubCuenta, 
            
            SUBSTRING(CuentaPUC,1,1) AS Clase,
            (SELECT Clase FROM $db.contabilidad_plan_cuentas_clases t2 WHERE t2.ID=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS NombreClase, 
            
            (SELECT SaldoInicialClase FROM $db.vista_saldos_iniciales_clase WHERE Clase=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS SaldoInicialClase,
            (SELECT DebitosClase FROM $db.vista_movimientos_clase WHERE Clase=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS DebitosClase,
            (SELECT CreditosClase FROM $db.vista_movimientos_clase WHERE Clase=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS CreditosClase,
            
            SUBSTRING(CuentaPUC,1,2) AS Grupo,
            (SELECT Grupo FROM $db.contabilidad_plan_cuentas_grupos t2 WHERE t2.ID=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS NombreGrupo,
            
            (SELECT SaldoInicialGrupo FROM $db.vista_saldos_iniciales_grupo WHERE Grupo=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS SaldoInicialGrupo,
            (SELECT DebitosGrupo FROM $db.vista_movimientos_grupo WHERE Grupo=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS DebitosGrupo,
            (SELECT CreditosGrupo FROM $db.vista_movimientos_grupo WHERE Grupo=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS CreditosGrupo,
            
            SUBSTRING(CuentaPUC,1,4) AS CuentaPadre,
            (SELECT Cuenta FROM $db.contabilidad_plan_cuentas_cuentas t2 WHERE t2.ID=SUBSTRING(CuentaPUC,1,4)) AS NombreCuentaPadre,
            (SELECT SaldoInicialCuentaPadre FROM $db.vista_saldos_iniciales_cuenta_padre WHERE CuentaPadre=SUBSTRING(CuentaPUC,1,4) LIMIT 1) AS SaldoInicialCuentaPadre,
            (SELECT DebitosCuentaPadre FROM $db.vista_movimientos_cuenta_padre WHERE CuentaPadre=SUBSTRING(CuentaPUC,1,4) LIMIT 1) AS DebitosCuentaPadre,
            (SELECT CreditosCuentaPadre FROM $db.vista_movimientos_cuenta_padre WHERE CuentaPadre=SUBSTRING(CuentaPUC,1,4) LIMIT 1) AS CreditosCuentaPadre,
            


            `Debito`,`Credito`,
            centro_costos_id
            FROM $db.contabilidad_librodiario
            $Condicion $CondicionEmpresa $CondicionCentroCostos $CondicionTercero $CondicionCuenta
            ORDER BY SUBSTRING(CuentaPUC,1,8),Identificacion,CuentaPUC,Fecha ASC";         
        $this->Query($sql);
        
        
        
    }
    /**
     * Constuye una vista con la informacion de las retenciones practicadas a un tercero
     * @param type $FechaInicial
     * @param type $FechaFinal
     * @param type $CmbTercero
     * @param type $Empresa
     * @param type $CentroCostos
     * @param type $CmbCiudadRetencion
     * @param type $CmbCiudadPago
     * @param type $Vector
     */
    public function ConstruirVistaRetencionesXTercero($FechaInicial, $FechaFinal,$CmbTercero, $Empresa, $CentroCostos,$CmbCiudadRetencion,$CmbCiudadPago, $Vector) {
        $sql="DROP VIEW IF EXISTS `vista_retenciones_tercero`;";
        $this->Query($sql);
        $CondicionEmpresa="";
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' AND Tercero='$CmbTercero' ";
        
        if($Empresa<>"ALL"){
            $CondicionEmpresa=" AND idEmpresa = '$Empresa'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>"ALL"){
            $CondicionCentroCostos=" AND idCentroCosto = '$CentroCostos'";
        }
        $sql="CREATE VIEW vista_retenciones_tercero AS
            SELECT *
            FROM vista_retenciones $Condicion;";         
        $this->Query($sql);
    }
    /**
     * Construye la vista para el estado de resultados por año
     * @param type $FechaInicial
     * @param type $FechaFinal
     * @param type $CmbAnio
     * @param type $Empresa
     * @param type $CentroCostos
     * @param type $Vector
     */
     public function construir_vista_estado_resultados($CmbAnio, $Empresa, $CentroCostos,$Vector) {
        $FechaInicial= $CmbAnio."-01-01";
        $FechaFinal = $CmbAnio."-12-31";
        $datos_empresa=$this->DevuelveValores("empresapro", "ID", $Empresa);
        $db=$datos_empresa["db"];
        
        $sql="DROP VIEW IF EXISTS $db.`vista_estado_resultados_anio`;";
        $this->Query($sql);
        $CondicionEmpresa="";
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' ";
                
        $CondicionCentroCostos="";
        if($CentroCostos<>""){
            $CondicionCentroCostos=" AND centro_costos_id = '$CentroCostos'";
        }
        $sql="CREATE VIEW $db.vista_estado_resultados_anio AS
            SELECT *
            FROM $db.contabilidad_librodiario $Condicion;";         
        $this->Query($sql);
    }
    
    
    public function construir_vista_balance_comprobacion_tercero($db,$FechaInicial, $FechaFinal,$CmbTercero, $CentroCostos,$CuentaPUC) {
        $sql="DROP VIEW IF EXISTS $db.`vista_balance_comprobacion_terceros`;";
        $this->Query($sql);
        
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' ";
        
                
        if($CmbTercero<>""){
            $Condicion.=" AND Tercero_Identificacion = '$CmbTercero'";
        }
        
        if($CuentaPUC<>""){
            $Condicion.=" AND CuentaPUC LIKE '$CuentaPUC%'";
        }
        
        if($CentroCostos<>""){
            $Condicion.=" AND centro_costos_id = '$CentroCostos'";
        }
        $sql="CREATE VIEW $db.vista_balance_comprobacion_terceros AS 
                SELECT CuentaPUC,NombreCuenta, 
                    Tercero_Identificacion,Tercero_DV,Tercero_Razon_Social,
                    Tercero_Direccion,Tercero_Cod_Mcipio,
                    SUM(Debito) as Debitos,SUM(Credito) as Creditos
                    FROM $db.contabilidad_librodiario $Condicion 
                    GROUP BY CuentaPUC,Tercero_Identificacion;";         
        $this->Query($sql);
    }
    
    /**
     * Fin Clase
     */
}
