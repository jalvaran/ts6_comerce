<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
class Facturador extends conexion{
    
    
    public function agregar_prefactura($db,$usuario_id) {
        $sql="SELECT COUNT(*) as total FROM $db.factura_prefactura WHERE  usuario_id='$usuario_id' ";
        
        $datos_validacion=$this->FetchAssoc($this->Query($sql));
        if($datos_validacion["total"]>=3){
            exit("E1;No puedes crear mas de 3 prefacturas");
        }
        $Tabla="factura_prefactura";
        $this->ActualizaRegistro($db.".".$Tabla, "activa", 0, "usuario_id", "$usuario_id");          
        $Datos["usuario_id"]=$usuario_id;        
        $Datos["activa"]=1;    
        $Datos["forma_pago"]=1; 
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function agregar_item_prefactura($prefactura_id,$db,$item_id,$precio,$cantidad,$impuestos_incluidos,$usuario_id) {
        
        $datos_item=$this->DevuelveValores($db.".inventario_items_general", "ID", $item_id);
        if($datos_item["ID"]==''){
            exit("E1;El Código enviado no existe en la base de datos");
        }
        $datos_impuestos=$this->DevuelveValores("porcentajes_iva", "ID", $datos_item["porcentajes_iva_id"]);
        $valor_unitario=$datos_item["Precio"];
        if($precio<>''){
            $valor_unitario=$precio;
        }
        if($impuestos_incluidos==1){
            
            $valor_unitario=($valor_unitario/($datos_impuestos["FactorMultiplicador"]+1));
            
        }
        $subtotal=$valor_unitario*$cantidad;
        $impuestos=($subtotal*$datos_impuestos["FactorMultiplicador"]);
        $total=$subtotal+$impuestos;
        $Tabla="factura_prefactura_items";
               
        $Datos["prefactura_id"]=$prefactura_id;        
        $Datos["item_id"]=$item_id;  
        $Datos["valor_unitario"]=$valor_unitario;     
        $Datos["cantidad"]=$cantidad;     
        $Datos["subtotal"]=$subtotal;     
        $Datos["impuestos"]=$impuestos;     
        $Datos["total"]=$total; 
        $Datos["porcentaje_iva_id"]=$datos_item["porcentajes_iva_id"];     
        $Datos["usuario_id"]=$usuario_id; 
        
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function crear_vista_documentos_electronicos($db) {
        $principalDb=DB;
        $sql="DROP VIEW IF EXISTS `vista_documentos_electronicos`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE VIEW vista_documentos_electronicos AS
                SELECT t1.*, 
                    
                    (SELECT SUM(subtotal) from documentos_electronicos_items t4 where t4.documento_electronico_id=t1.documento_electronico_id LIMIT 1 ) AS subtotal_documento,
                    (SELECT SUM(impuestos) from documentos_electronicos_items t4 where t4.documento_electronico_id=t1.documento_electronico_id LIMIT 1 ) AS impuestos_documento,
                    (SELECT SUM(total) from documentos_electronicos_items t4 where t4.documento_electronico_id=t1.documento_electronico_id LIMIT 1 ) AS total_documento,
                    (SELECT name FROM $principalDb.api_fe_tipo_documentos t3 WHERE t3.ID=t1.tipo_documento_id LIMIT 1) AS nombre_tipo_documento,
                    (SELECT razon_social FROM terceros t4 WHERE t4.ID=t1.tercero_id LIMIT 1) AS nombre_tercero, 
                    (SELECT identificacion FROM terceros t4 WHERE t4.ID=t1.tercero_id LIMIT 1) AS nit_tercero,
                    (SELECT CONCAT(Nombre,' ',Apellido) FROM $principalDb.usuarios t5 WHERE t5.ID=t1.usuario_id LIMIT 1) AS nombre_usuario,
                    (SELECT CONCAT(prefijo,'-',numero) from documentos_electronicos t5 where t5.documento_electronico_id=t1.documento_asociado_id LIMIT 1 ) AS documento_asociado,
                    (SELECT GROUP_CONCAT(t5.Descripcion) from inventario_items_general t5 where exists (SELECT 1 FROM documentos_electronicos_items t7 WHERE t7.documento_electronico_id=t1.documento_electronico_id and t7.item_id=t5.ID) ) as nombre_items  
                    
                FROM `documentos_electronicos` t1 ORDER BY updated DESC ";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    
    function getSQLDocumentoElectronicoLibroDiario($db,$documento_electronico_id,$idCentroCostos=1,$idSucursal=1){
        $idUser=$_SESSION["idUser"];
        $sqlValores="INSERT INTO `$db`.`contabilidad_librodiario` ( `Fecha`, `Tipo_Documento_Interno`, `Num_Documento_Interno`, `Num_Documento_Externo`, `Tercero_Tipo_Documento`, `Tercero_Identificacion`, `Tercero_DV`,  `Tercero_Razon_Social`, `Tercero_Direccion`, `Tercero_Cod_Dpto`, `Tercero_Cod_Mcipio`, `Tercero_Pais_Domicilio`, `Concepto`, `CuentaPUC`, `NombreCuenta`, `Detalle`, `Debito`, `Credito`, `Neto`, `centro_costos_id`, `sucursal_id`, `usuario_id`) VALUES ";
        
        $DatosDocumento= $this->DevuelveValores("$db.documentos_electronicos", "documento_electronico_id", $documento_electronico_id);
        $Fecha=$DatosDocumento["fecha"];
       
        $DatosTipoDocumento= $this->DevuelveValores("api_fe_tipo_documentos", "ID", $DatosDocumento["tipo_documento_id"]);
        
        $TipoDocumento=$DatosTipoDocumento["name"];
        $documento_id=$DatosDocumento["documento_electronico_id"];
        $DocumentoReferencia=$DatosDocumento["numero"];
        $db_principal=DB;
        
        $datos_tercero=$this->DevuelveValores("$db.terceros", "ID", $DatosDocumento["tercero_id"]);
        $datos_tipo_documento_tercero=$this->DevuelveValores("tipo_documento_identificacion", "ID", $datos_tercero["tipo_documento_id"]);
        $datos_codigo_municipios=$this->DevuelveValores("catalogo_municipios", "ID", $datos_tercero["municipio_id"]);
        
        if($DatosDocumento["forma_pago"]==1){
            $CuentaPUCTotal=110505;
        }
        if($DatosDocumento["forma_pago"]==2){
            $CuentaPUCTotal=130505;
        }
        
        $datos_cuenta_contable=$this->DevuelveValores("$db.contabilidad_plan_cuentas_subcuentas", "ID", $CuentaPUCTotal);
        $NombreCuentaTotal=$datos_cuenta_contable["Nombre"];
                
        $sql="SELECT t1.*,
                (SELECT t3.departamento_id FROM $db.inventario_items_general t3 WHERE t3.ID=t1.item_id LIMIT 1) as departamento_id_item,
                (SELECT t2.cuenta_puc_ventas FROM $db.inventario_items_departamentos t2 WHERE t2.ID=(SELECT departamento_id_item) LIMIT 1) as cuenta_puc_ventas,  
                (SELECT t2.cuenta_puc_iva_ventas FROM $db.inventario_items_departamentos t2 WHERE t2.ID=(SELECT departamento_id_item) LIMIT 1) as cuenta_puc_iva_ventas,  
                (SELECT t2.cuenta_puc_inventarios FROM $db.inventario_items_departamentos t2 WHERE t2.ID=(SELECT departamento_id_item) LIMIT 1) as cuenta_puc_inventarios,  
                (SELECT t2.cuenta_puc_costos_inventarios FROM $db.inventario_items_departamentos t2 WHERE t2.ID=(SELECT departamento_id_item) LIMIT 1) as cuenta_puc_costos_inventarios,  
                (SELECT t2.cuenta_puc_ventas_devoluciones FROM $db.inventario_items_departamentos t2 WHERE t2.ID=(SELECT departamento_id_item) LIMIT 1) as cuenta_puc_ventas_devoluciones,  
                (SELECT t2.cuenta_puc_iva_ventas_devoluciones FROM $db.inventario_items_departamentos t2 WHERE t2.ID=(SELECT departamento_id_item) LIMIT 1) as cuenta_puc_iva_ventas_devoluciones  
                   

                FROM $db.documentos_electronicos_items t1 WHERE t1.documento_electronico_id='$documento_electronico_id' and (t1.subtotal>0 or t1.impuestos>0 or t1.total>0)";
        $Consulta=$this->Query($sql);
        
        while($DatosItems= $this->FetchAssoc($Consulta)){
            
            
            $TerceroTipoDocumento=$datos_tipo_documento_tercero["codigo"];
            $NIT=$datos_tercero["identificacion"];
            $DV=$datos_tercero["dv"];
            
            $RazonSocial=$datos_tercero["razon_social"];
            $Direccion=$datos_tercero["direccion"];
            $CodDepartamento=$datos_codigo_municipios["codigo_departamento"];
            $CodMunicipo=$datos_codigo_municipios["codigo_municipio"];
            $codPais=119;
            if($DatosDocumento["tipo_documento_id"]==1 or $DatosDocumento["tipo_documento_id"]==6){
                $Concepto="FACTURA ELECTRONICA";
                if($DatosDocumento["tipo_documento_id"]==6){
                    $Concepto="NOTA DEBITO ELECTRONICA";
                }
                $CuentaPUCSubtotal=$DatosItems["cuenta_puc_ventas"];
                $CuentaPUCImpuestos=$DatosItems["cuenta_puc_iva_ventas"];
                
                $datos_cuenta_contable=$this->DevuelveValores("$db.contabilidad_plan_cuentas_subcuentas", "ID", $CuentaPUCSubtotal);
                $NombreCuentaSubtotal=$datos_cuenta_contable["Nombre"];
                
                
                $Debito=0;
                $Credito=$DatosItems["subtotal"];                
                $Neto=0;
                if($Debito>0){
                    $Neto=$Debito;
                }
                if($Credito>0){
                    $Neto=$Credito*(-1);
                }
                //partida con los datos del subtotal
                $sqlValores.="('$Fecha','$TipoDocumento','$documento_id','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','$Concepto','$CuentaPUCSubtotal','$NombreCuentaSubtotal','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
                
                //partida con los datos de los impuestos
                if($DatosItems["impuestos"]>0){
                    $datos_cuenta_contable=$this->DevuelveValores("$db.contabilidad_plan_cuentas_subcuentas", "ID", $CuentaPUCImpuestos);
                    $NombreCuentaIVA=$datos_cuenta_contable["Nombre"];
                    $Debito=0;
                    $Credito=$DatosItems["impuestos"];                
                    $Neto=0;
                    if($Debito>0){
                        $Neto=$Debito;
                    }
                    if($Credito>0){
                        $Neto=$Credito*(-1);
                    }
                
                    $sqlValores.="('$Fecha','$TipoDocumento','$documento_id','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','$Concepto','$CuentaPUCImpuestos','$NombreCuentaIVA','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
            
                }
                
                $Debito=$DatosItems["total"];
                $Credito=0;                
                $Neto=0;
                if($Debito>0){
                    $Neto=$Debito;
                }
                if($Credito>0){
                    $Neto=$Credito*(-1);
                }
                //contra partida con los datos del total
                $sqlValores.="('$Fecha','$TipoDocumento','$documento_id','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','$Concepto','$CuentaPUCTotal','$NombreCuentaTotal','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
                
                
            }
            if($DatosDocumento["tipo_documento_id"]==5){
                $Concepto="NOTA CREDITO ELECTRONICA";
                $CuentaPUCSubtotal=$DatosItems["cuenta_puc_ventas_devoluciones"];
                $CuentaPUCImpuestos=$DatosItems["cuenta_puc_iva_ventas_devoluciones"];
                                
                $datos_cuenta_contable=$this->DevuelveValores("$db.contabilidad_plan_cuentas_subcuentas", "ID", $CuentaPUCSubtotal);
                $NombreCuentaSubtotal=$datos_cuenta_contable["Nombre"];
                
                
                $Debito=$DatosItems["subtotal"];
                $Credito=0;                
                $Neto=0;
                if($Debito>0){
                    $Neto=$Debito;
                }
                if($Credito>0){
                    $Neto=$Credito*(-1);
                }
                //partida con los datos del subtotal
                $sqlValores.="('$Fecha','$TipoDocumento','$documento_id','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','$Concepto','$CuentaPUCSubtotal','$NombreCuentaSubtotal','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
                
                //partida con los datos de los impuestos
                if($DatosItems["impuestos"]>0){
                    $datos_cuenta_contable=$this->DevuelveValores("$db.contabilidad_plan_cuentas_subcuentas", "ID", $CuentaPUCImpuestos);
                    $NombreCuentaIVA=$datos_cuenta_contable["Nombre"];
                    $Debito=$DatosItems["impuestos"];
                    $Credito=0;                
                    $Neto=0;
                    if($Debito>0){
                        $Neto=$Debito;
                    }
                    if($Credito>0){
                        $Neto=$Credito*(-1);
                    }
                
                    $sqlValores.="('$Fecha','$TipoDocumento','$documento_id','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','$Concepto','$CuentaPUCImpuestos','$NombreCuentaIVA','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
            
                }
                
                $Debito=0;
                $Credito=$DatosItems["total"];                
                $Neto=0;
                if($Debito>0){
                    $Neto=$Debito;
                }
                if($Credito>0){
                    $Neto=$Credito*(-1);
                }
                //contra partida con los datos del total
                $sqlValores.="('$Fecha','$TipoDocumento','$documento_id','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','$Concepto','$CuentaPUCTotal','$NombreCuentaTotal','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
                
                
            }
            
            
        }  
        
        $sqlValores = substr($sqlValores, 0, -1);
        return($sqlValores);
        
    }
    
    public function contabilizar_documento_electronico($db,$documento_electronico_id,$idCentroCostos=1,$idSucursal=1) {
        $sql=$this->getSQLDocumentoElectronicoLibroDiario($db, $documento_electronico_id,$idCentroCostos,$idSucursal);
        $this->Query($sql);
    }
    
    /**
     * Fin Clase
     */
}
