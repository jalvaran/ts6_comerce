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
    public function ConstruirVistaBalanceTercero($Tipo,$FechaInicial,$FechaFinal,$Empresa,$CentroCostos,$Tercero,$CuentaContable,$vector){
        
        
        $sql="DROP VIEW IF EXISTS `vista_saldos_iniciales_clase`;";
        $this->Query($sql);
        $sql="DROP VIEW IF EXISTS `vista_movimientos_clase`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS `vista_saldos_iniciales_grupo`;";
        $this->Query($sql);
        $sql="DROP VIEW IF EXISTS `vista_movimientos_grupo`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS `vista_saldos_iniciales_cuenta_padre`;";
        $this->Query($sql);
        $sql="DROP VIEW IF EXISTS `vista_movimientos_cuenta_padre`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS `vista_balancextercero2`;";
        $this->Query($sql);
        
        $sql="DROP VIEW IF EXISTS `vista_saldo_inicial_cuentapuc`;";
        $this->Query($sql);
        
        $CondicionEmpresa="";
        $Condicion=" WHERE ";
        
        if($Tipo==1){
            $Condicion.="Fecha>='$FechaInicial' AND Fecha <='$FechaFinal'";
            $CondicionSaldos=" WHERE Fecha <'$FechaInicial'";
        }else{
            $Condicion.="Fecha <='$FechaFinal'";
            $CondicionSaldos=" WHERE Fecha <'0000-00-00'";
        }
        if($Empresa<>"ALL"){
            $CondicionEmpresa=" AND idEmpresa = '$Empresa'";
        }
        $CondicionTercero="";
        if($Tercero<>""){
            $CondicionTercero=" AND Tercero_Identificacion = '$Tercero'";
        }
        $CondicionCuenta="";
        if($CuentaContable<>""){
            $CondicionCuenta=" AND CuentaPUC LIKE '$CuentaContable%'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>"ALL"){
            $CondicionCentroCostos=" AND idCentroCosto = '$CentroCostos'";
        }
        
        
        $sql="
            CREATE VIEW vista_saldos_iniciales_clase AS
            SELECT SUBSTRING(CuentaPUC,1,1) as Clase,SUM(Debito - Credito) as SaldoInicialClase FROM librodiario $CondicionSaldos  
              GROUP BY SUBSTRING(CuentaPUC,1,1);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW vista_movimientos_clase AS
            SELECT SUBSTRING(CuentaPUC,1,1) as Clase,SUM(Debito) as DebitosClase,SUM(Credito) as CreditosClase FROM librodiario $Condicion "
                . " GROUP BY SUBSTRING(CuentaPUC,1,1);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW vista_saldos_iniciales_grupo AS
            SELECT SUBSTRING(CuentaPUC,1,2) as Grupo,SUM(Debito - Credito) as SaldoInicialGrupo FROM librodiario $CondicionSaldos  
              GROUP BY SUBSTRING(CuentaPUC,1,2);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW vista_movimientos_grupo AS
            SELECT SUBSTRING(CuentaPUC,1,2) as Grupo,SUM(Debito) as DebitosGrupo,SUM(Credito) as CreditosGrupo FROM librodiario $Condicion "
                . " GROUP BY SUBSTRING(CuentaPUC,1,2);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW vista_saldos_iniciales_cuenta_padre AS
            SELECT SUBSTRING(CuentaPUC,1,4) as CuentaPadre,SUM(Debito - Credito) as SaldoInicialCuentaPadre FROM librodiario $CondicionSaldos  GROUP BY SUBSTRING(CuentaPUC,1,4);";         
        $this->Query($sql);
        
        $sql="
            CREATE VIEW vista_movimientos_cuenta_padre AS
            SELECT SUBSTRING(CuentaPUC,1,4) as CuentaPadre,SUM(Debito) as DebitosCuentaPadre,SUM(Credito) as CreditosCuentaPadre FROM librodiario $Condicion "
                . " GROUP BY SUBSTRING(CuentaPUC,1,4);";         
        $this->Query($sql);
        
        
        $sql="CREATE VIEW vista_saldo_inicial_cuentapuc AS
            SELECT CuentaPUC as ID,Tercero_Identificacion,SUM(Debito-Credito) as SaldoInicial
            FROM `librodiario`
            $Condicion $CondicionEmpresa $CondicionCentroCostos
            GROUP BY CuentaPUC,Tercero_Identificacion";         
        $this->Query($sql);
        
        
        
        $sql="CREATE VIEW vista_balancextercero2 AS
            SELECT (SUBSTRING(CuentaPUC,1,8)) as ID,Fecha,`Tercero_Identificacion` as Identificacion,`Tercero_Razon_Social` AS Razon_Social,
            `CuentaPUC` , `NombreCuenta`, Tipo_Documento_Intero as TipoDocumento,Num_Documento_Interno as NumDocumento,Num_Documento_Externo as DocumentoExterno, 
            (SELECT SaldoInicial FROM vista_saldo_inicial_cuentapuc WHERE librodiario.CuentaPUC=vista_saldo_inicial_cuentapuc.ID AND librodiario.Tercero_Identificacion=vista_saldo_inicial_cuentapuc.Tercero_Identificacion LIMIT 1) AS SaldoInicialSubCuenta, 
            
            SUBSTRING(CuentaPUC,1,1) AS Clase,
            (SELECT Clase FROM clasecuenta WHERE PUC=SUBSTRING(CuentaPUC,1,1)) AS NombreClase, 
            
            (SELECT SaldoInicialClase FROM vista_saldos_iniciales_clase WHERE Clase=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS SaldoInicialClase,
            (SELECT DebitosClase FROM vista_movimientos_clase WHERE Clase=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS DebitosClase,
            (SELECT CreditosClase FROM vista_movimientos_clase WHERE Clase=SUBSTRING(CuentaPUC,1,1) LIMIT 1) AS CreditosClase,
            
            SUBSTRING(CuentaPUC,1,2) AS Grupo,
            (SELECT Nombre FROM gupocuentas WHERE PUC=SUBSTRING(CuentaPUC,1,2)) AS NombreGrupo,
            
            (SELECT SaldoInicialGrupo FROM vista_saldos_iniciales_grupo WHERE Grupo=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS SaldoInicialGrupo,
            (SELECT DebitosGrupo FROM vista_movimientos_grupo WHERE Grupo=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS DebitosGrupo,
            (SELECT CreditosGrupo FROM vista_movimientos_grupo WHERE Grupo=SUBSTRING(CuentaPUC,1,2) LIMIT 1) AS CreditosGrupo,
            
            SUBSTRING(CuentaPUC,1,4) AS CuentaPadre,
            (SELECT Nombre FROM cuentas WHERE idPUC=SUBSTRING(CuentaPUC,1,4)) AS NombreCuentaPadre,
            (SELECT SaldoInicialCuentaPadre FROM vista_saldos_iniciales_cuenta_padre WHERE CuentaPadre=SUBSTRING(CuentaPUC,1,4) LIMIT 1) AS SaldoInicialCuentaPadre,
            (SELECT DebitosCuentaPadre FROM vista_movimientos_cuenta_padre WHERE CuentaPadre=SUBSTRING(CuentaPUC,1,4) LIMIT 1) AS DebitosCuentaPadre,
            (SELECT CreditosCuentaPadre FROM vista_movimientos_cuenta_padre WHERE CuentaPadre=SUBSTRING(CuentaPUC,1,4) LIMIT 1) AS CreditosCuentaPadre,
            


            `Debito`,`Credito`,
            idEmpresa,idCentroCosto
            FROM `librodiario`
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
     public function ConstruirVistaEstadoResultados($CmbAnio, $Empresa, $CentroCostos,$Vector) {
        $FechaInicial= $CmbAnio."-01-01";
        $FechaFinal = $CmbAnio."-12-31";
        $sql="DROP VIEW IF EXISTS `vista_estado_resultados_anio`;";
        $this->Query($sql);
        $CondicionEmpresa="";
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' ";
        
        if($Empresa<>"ALL"){
            $CondicionEmpresa=" AND idEmpresa = '$Empresa'";
        }
        
        $CondicionCentroCostos="";
        if($CentroCostos<>"ALL"){
            $CondicionCentroCostos=" AND idCentroCosto = '$CentroCostos'";
        }
        $sql="CREATE VIEW vista_estado_resultados_anio AS
            SELECT *
            FROM librodiario $Condicion;";         
        $this->Query($sql);
    }
    
    
    public function ConstruirVistaBalanceComprobacionXTercero($FechaInicial, $FechaFinal,$CmbTercero, $Empresa, $CentroCostos,$CuentaPUC) {
        $sql="DROP VIEW IF EXISTS `vista_balance_comprobacion_terceros`;";
        $this->Query($sql);
        
        $Condicion=" WHERE Fecha>='$FechaInicial' AND Fecha <='$FechaFinal' ";
        
        if($Empresa<>"ALL"){
            $Condicion.=" AND idEmpresa = '$Empresa'";
        }
        
        if($CmbTercero<>""){
            $Condicion.=" AND Tercero_Identificacion = '$CmbTercero'";
        }
        
        if($CuentaPUC<>""){
            $Condicion.=" AND CuentaPUC LIKE '$CuentaPUC%'";
        }
        
        if($CentroCostos<>"ALL"){
            $Condicion.=" AND idCentroCosto = '$CentroCostos'";
        }
        $sql="CREATE VIEW vista_balance_comprobacion_terceros AS 
                SELECT CuentaPUC,NombreCuenta, 
                    Tercero_Identificacion,Tercero_DV,Tercero_Razon_Social,
                    Tercero_Direccion,Tercero_Cod_Mcipio,
                    SUM(Debito) as Debitos,SUM(Credito) as Creditos
                    FROM librodiario $Condicion 
                    GROUP BY CuentaPUC,Tercero_Identificacion;";         
        $this->Query($sql);
    }
    
    /**
     * Fin Clase
     */
}
