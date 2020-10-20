<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class DocumentosContables extends conexion{
    
    public function agregar_predocumento($db,$usuario_id){
        $tab="contabilidad_predocumento_contable";
        $sql="SELECT COUNT(*) as total FROM $db.$tab WHERE  usuario_id='$usuario_id' ";
        
        $datos_validacion=$this->FetchAssoc($this->Query($sql));
        if($datos_validacion["total"]>=3){
            exit("E1;No puedes crear mas de 3 predocumentos");
        }
        $this->ActualizaRegistro($db.".".$tab, "activo", 0, "usuario_id", $usuario_id);
        $documento_contable_id=$this->getUniqId("dc_");
        $Datos["documento_contable_id"]=$documento_contable_id;
        $Datos["tipo_documento_contable_id"]="";
        $Datos["Fecha"]="";
        $Datos["sucursal_id"]=0;
        $Datos["centro_costo_id"]=0;
        $Datos["observaciones"]="";
        $Datos["usuario_id"]=$usuario_id;
        $Datos["activo"]=1;
        
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
    }
    
    /**
     * Crea un documento contable
     * @param type $TipoDocumento
     * @param type $Fecha
     * @param type $Descripcion
     * @param type $idEmpresa
     * @param type $idSucursal
     * @param type $idCentroCostos
     * @param type $Soporte
     * @param type $idUser
     * @return type
     */
    function CrearDocumentoContable($db,$documento_contable_id,$TipoDocumento,$Fecha,$Descripcion,$idSucursal,$idCentroCostos,$Soporte,$idUser){
        
        $Tabla="$db.contabilidad_documentos_contables";
        $datos_validacion=$this->DevuelveValores($Tabla, "documento_contable_id", $documento_contable_id);
        if($datos_validacion["documento_contable_id"]<>''){
            exit("E1;Error el ID del Documento ya fué usado anteriormente");
        }
        $Consecutivo= $this->ObtenerMAX($Tabla, "Consecutivo", "tipo_documento_contable_id", $TipoDocumento);
        $Consecutivo++;
        $Datos["tipo_documento_contable_id"]=$TipoDocumento;
        $Datos["documento_contable_id"]=$documento_contable_id;
        $Datos["Consecutivo"]=$Consecutivo;
        $Datos["Fecha"]=$Fecha;
        $Datos["Descripcion"]=$Descripcion;
        $Datos["Estado"]=1;
        $Datos["idUser"]=$idUser;        
        $Datos["idSucursal"]=$idSucursal;
        $Datos["idCentroCostos"]=$idCentroCostos;
        $Datos["Soporte"]=$Soporte;
        $sql= $this->getSQLInsert($Tabla, $Datos);
        $this->Query($sql);
        
        $sql2= $this->getSQLDocumentoContableLibroDiario($db, $documento_contable_id);
        $this->Query($sql2);
        
    }
    /**
     * Agrega un movimiento contable a un documento
     * @param type $idDocumento
     * @param type $Tercero
     * @param type $CuentaPUC
     * @param type $TipoMovimiento
     * @param type $Valor
     * @param type $Concepto
     * @param type $NumDocSoporte
     * @param type $Soporte
     */
    function AgregaMovimientoDocumentoContable($db,$documento_contable_id,$Tercero,$CuentaPUC,$TipoMovimiento,$Valor,$Concepto,$NumDocSoporte,$Soporte,$Fecha='0000-00-00'){
        $DatosCuentas=$this->DevuelveValores("$db.contabilidad_plan_cuentas_subcuentas", "ID", $CuentaPUC);
        $NombreCuenta=$DatosCuentas["Nombre"];
        if($TipoMovimiento=="DB"){
            $Debito=$Valor;
            $Credito=0;
        }else{
            $Debito=0;
            $Credito=$Valor;
        }
        $Tabla="$db.contabilidad_documentos_contables_items";
        
        $Datos["documento_contable_id"]=$documento_contable_id;
        $Datos["Fecha"]=$Fecha;
        $Datos["Tercero"]=$Tercero;
        $Datos["CuentaPUC"]=$CuentaPUC;
        $Datos["NombreCuenta"]=$NombreCuenta;
        $Datos["Debito"]=$Debito;
        $Datos["Credito"]=$Credito;
        $Datos["Concepto"]=$Concepto;
        $Datos["NumDocSoporte"]=$NumDocSoporte;
        $Datos["Soporte"]=$Soporte;
        
        $sql= $this->getSQLInsert($Tabla, $Datos);
        $this->Query($sql);
        
        
          
    }
    /**
     * Devuelve el sql para insertar los movimientos de un documento contable al librodiario
     * @param type $idDocumento
     * @return type
     */
    function getSQLDocumentoContableLibroDiario($db,$documento_contable_id){
        $idUser=$_SESSION["idUser"];
        $sqlValores="INSERT INTO `$db`.`contabilidad_librodiario` ( `Fecha`, `Tipo_Documento_Interno`, `Num_Documento_Interno`, `Num_Documento_Externo`, `Tercero_Tipo_Documento`, `Tercero_Identificacion`, `Tercero_DV`,  `Tercero_Razon_Social`, `Tercero_Direccion`, `Tercero_Cod_Dpto`, `Tercero_Cod_Mcipio`, `Tercero_Pais_Domicilio`, `Concepto`, `CuentaPUC`, `NombreCuenta`, `Detalle`, `Debito`, `Credito`, `Neto`, `centro_costos_id`, `sucursal_id`, `usuario_id`) VALUES ";
        
        $DatosDocumento= $this->DevuelveValores("$db.contabilidad_documentos_contables", "documento_contable_id", $documento_contable_id);
        $DatosTipoDocumento= $this->DevuelveValores("$db.contabilidad_catalogo_documentos_contables", "ID", $DatosDocumento["tipo_documento_contable_id"]);
        $idCentroCostos=$DatosDocumento["idCentroCostos"];        
        $idSucursal=$DatosDocumento["idSucursal"];
        $TipoDocumento=$DatosTipoDocumento["Nombre"];
        $Consecutivo=$DatosDocumento["Consecutivo"];
        $db_principal=DB;
        $sql="SELECT t1.*,
                (SELECT t2.tipo_documento_id FROM $db.terceros t2 WHERE t2.identificacion=t1.Tercero LIMIT 1) as tipo_documento_id,
                (SELECT t2.dv FROM $db.terceros t2 WHERE t2.identificacion=t1.Tercero LIMIT 1) as dv,  
                (SELECT t2.razon_social FROM $db.terceros t2 WHERE t2.identificacion=t1.Tercero LIMIT 1) as razon_social,  
                (SELECT t2.direccion FROM $db.terceros t2 WHERE t2.identificacion=t1.Tercero LIMIT 1) as direccion,                
                (SELECT t2.municipio_id FROM $db.terceros t2 WHERE t2.identificacion=t1.Tercero LIMIT 1) as municipio_id,
                (SELECT t3.codigo_municipio FROM $db_principal.catalogo_municipios t3 WHERE t3.ID=(SELECT municipio_id) LIMIT 1) as CodigoMunicipio,
                (SELECT t3.codigo_departamento FROM $db_principal.catalogo_municipios t3 WHERE t3.ID=(SELECT municipio_id) LIMIT 1) as CodigoDepartamento,
                (SELECT t4.codigo FROM $db_principal.tipo_documento_identificacion t4 WHERE t4.ID=(SELECT tipo_documento_id) LIMIT 1) as Tercero_Tipo_Documento     
                    

                FROM $db.contabilidad_documentos_contables_items t1 WHERE t1.documento_contable_id='$documento_contable_id'";
        $Consulta=$this->Query($sql);
        
        while($DatosItems= $this->FetchAssoc($Consulta)){
            $Fecha=$DatosItems["Fecha"];
            if($DatosItems["Fecha"]=='0000-00-00'){
                $Fecha=$DatosDocumento["Fecha"];
            }
            
            $TerceroTipoDocumento=$DatosItems["Tercero_Tipo_Documento"];
            $NIT=$DatosItems["Tercero"];
            $DV=$DatosItems["dv"];
            
            $RazonSocial=$DatosItems["razon_social"];
            $Direccion=$DatosItems["direccion"];
            $CodDepartamento=$DatosItems["CodigoDepartamento"];
            $CodMunicipo=$DatosItems["CodigoMunicipio"];
            $codPais=119;
            $Concepto=$DatosItems["Concepto"];
            $CuentaPUC=$DatosItems["CuentaPUC"];
            $NombreCuenta=$DatosItems["NombreCuenta"];
            $Debito=$DatosItems["Debito"];
            $Credito=$DatosItems["Credito"];
            $DocumentoReferencia=$DatosItems["NumDocSoporte"];
            $Neto=0;
            if($Debito>0){
                $Neto=$Debito;
            }
            if($Credito>0){
                $Neto=$Credito*(-1);
            }
            $sqlValores.="('$Fecha','$TipoDocumento','$Consecutivo','$DocumentoReferencia','$TerceroTipoDocumento','$NIT','$DV','$RazonSocial','$Direccion','$CodDepartamento','$CodMunicipo','$codPais','Documentos Contables','$CuentaPUC','$NombreCuenta','$Concepto',$Debito,$Credito,$Neto,$idCentroCostos,$idSucursal,$idUser),";
            
        }  
        
        $sqlValores = substr($sqlValores, 0, -1);
        return($sqlValores);
        
    }
    /**
     * Guarda un documento contable
     * @param type $idDocumento
     */
    function GuardarDocumentoContable($idDocumento) {
        $sql=$this->getSQLDocumentoContableLibroDiario($idDocumento);
        //print($sql);
        $this->Query($sql);
        $this->ActualizaRegistro("documentos_contables_control", "Estado", "CERRADO", "ID", $idDocumento);
    }
    
    public function CopiarItemsDocumento($TipoDocumento,$idDocumentoACopiar,$idDocumentoDestino) {
        $sql="SELECT ite.Tercero,ite.Credito,ite.Debito,ite.CuentaPUC,ite.Concepto,ite.NumDocSoporte,ite.Soporte FROM documentos_contables_items ite "
                . "INNER JOIN documentos_contables_control c ON ite.idDocumento=c.ID WHERE c.idDocumento='$TipoDocumento' AND c.Consecutivo='$idDocumentoACopiar'";    
        $Consulta=$this->Query($sql);
        while($DatosMovimiento= $this->FetchAssoc($Consulta)){
            $TipoMovimiento="CR";
            $Valor=$DatosMovimiento["Credito"];
            if($DatosMovimiento["Debito"]<>0){
                $TipoMovimiento="DB";
                $Valor=$DatosMovimiento["Debito"];
            }
            $this->AgregaMovimientoDocumentoContable($idDocumentoDestino, $DatosMovimiento["Tercero"], $DatosMovimiento["CuentaPUC"], $TipoMovimiento, $Valor, $DatosMovimiento["Concepto"], $DatosMovimiento["NumDocSoporte"], $DatosMovimiento["Soporte"]);
        }
            
        }
        
        function AgregueBaseAMovimientoContable($idDocumento,$Concepto,$Base,$Porcentaje,$Valor,$idUser,$idItem){
        
            $Tabla="documentos_contables_registro_bases";
            
            $Datos["idDocumentoContable"]=$idDocumento;
            
            $Datos["Concepto"]=$Concepto;
            $Datos["Base"]=$Base;
            $Datos["Porcentaje"]=$Porcentaje;
            $Datos["ValorPorcentaje"]=$Porcentaje/100;
            $Datos["Valor"]=$Valor;
            $Datos["idUser"]=$idUser;
            $Datos["idItemDocumentoContable"]=$idItem;
            $Datos["Estado"]="ABIERTO";
            $sql= $this->getSQLInsert($Tabla, $Datos);
            $this->Query($sql);


        }
        
        public function VerificaSiCuentaSolicitaBase($CuentaPUC) {
            $DatosCuenta=$this->DevuelveValores("subcuentas","PUC",$CuentaPUC);
            return($DatosCuenta["SolicitaBase"]);
        }
        
        public function AbrirDocumentoContable($idDocumento) {
            $sql="SELECT t1.Consecutivo, 
                    (SELECT t2.Nombre FROM documentos_contables t2 WHERE t1.idDocumento=t2.ID LIMIT 1) AS NombreDocumento 
                    FROM documentos_contables_control t1 WHERE t1.ID='$idDocumento';";
            $DatosDocumento= $this->FetchAssoc($this->Query($sql));
            $Documento=$DatosDocumento["NombreDocumento"];
            $Consecutivo=$DatosDocumento["Consecutivo"];
            $sql="DELETE FROM librodiario WHERE Tipo_Documento_Intero='$Documento' AND Num_Documento_Interno='$Consecutivo'";
            $this->Query($sql);
            $this->ActualizaRegistro("documentos_contables_control", "Estado", "ABIERTO", "ID", $idDocumento);
        }
        
        public function anular_documento_contable($db,$documento_id,$observaciones,$idUser) {
            $datos_documento=$this->DevuelveValores("$db.contabilidad_documentos_contables", "ID", $documento_id);
            $datos_tipo_documento= $this->DevuelveValores("$db.contabilidad_catalogo_documentos_contables", "ID", $datos_documento["tipo_documento_contable_id"]);
            
            $this->anule_movimientos_libro_diario($db, $datos_tipo_documento["Nombre"], $datos_documento["Consecutivo"]);
            $sql="UPDATE $db.contabilidad_documentos_contables SET Estado=2, observaciones_anulacion='$observaciones', usuario_anulacion_id='$idUser' WHERE ID='$documento_id'";
            $this->Query($sql);
        }
        
        public function anule_movimientos_libro_diario($db,$DocumentoInterno,$NumDocumentoInterno) {
            $sql="UPDATE $db.contabilidad_librodiario SET Debito=0,Credito=0,Neto=0,Estado='2' WHERE Tipo_Documento_Interno='$DocumentoInterno' AND Num_Documento_Interno='$NumDocumentoInterno'";
            $this->Query($sql);
        }
        
        public function copiar_documento_contable($db,$documento_id,$idUser) {
            
            $datos_predocumento=$this->DevuelveValores("$db.contabilidad_predocumento_contable", "Activo", 1);
            if($datos_predocumento["ID"]==''){
                exit("E1;Para poder copiar este documento debe tener un predocumento activo");
            }
            $llave_nueva=$datos_predocumento["documento_contable_id"];
            /*
            $sql="SELECT COUNT(ID) as Total FROM $db.contabilidad_documentos_contables_items WHERE documento_contable_id='$llave_nueva'";
            $datos_validacion=$this->FetchAssoc($this->Query($sql));
            if($datos_validacion["Total"]>0){
                exit("E1;Para poder copiar este documento el predocumento que está activo debe estar vacío");
            }
             * 
             */
            $datos_documento=$this->DevuelveValores("$db.contabilidad_documentos_contables", "ID", $documento_id);
            $llave_anterior=$datos_documento["documento_contable_id"];
            //$datos_tipo_documento= $this->DevuelveValores("$db.contabilidad_catalogo_documentos_contables", "ID", $datos_documento["tipo_documento_contable_id"]);
            
            $sql="UPDATE $db.contabilidad_predocumento_contable 
                    SET tipo_documento_contable_id='".$datos_documento["tipo_documento_contable_id"]."' 
                        
                    WHERE documento_contable_id='$llave_nueva'

                    ";
            $this->Query($sql);
            
            $sql="INSERT INTO $db.contabilidad_documentos_contables_items (`documento_contable_id`,`Fecha`,`Tercero`,`CuentaPUC`,`NombreCuenta`,`Debito`,`Credito`,`Concepto`,`NumDocSoporte`,`Soporte`)  
                    SELECT '$llave_nueva',`Fecha`,`Tercero`,`CuentaPUC`,`NombreCuenta`,`Debito`,`Credito`,`Concepto`,`NumDocSoporte`,`Soporte` 
                    FROM  $db.contabilidad_documentos_contables_items WHERE documento_contable_id='$llave_anterior'
                    ";
            
            $this->Query($sql);
        }
        
    /**
     * Fin Clase
     */
}
