<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/documentos_contables.class.php");
//include_once("../../../general/clases/contabilidad.class.php");
if( !empty($_REQUEST["Accion"]) ){
    $obCon = new DocumentosContables($idUser);
    //$obContabilidad = new contabilidad($idUser);
    switch ($_REQUEST["Accion"]) {
        
        case 1: //edita el valor de una tabla
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $tab=$obCon->normalizar($_REQUEST["tab"]);
            $item_id_edit=$obCon->normalizar($_REQUEST["item_id_edit"]);
            $campo_edit=$obCon->normalizar($_REQUEST["campo_edit"]);
            $valor_nuevo=$obCon->normalizar($_REQUEST["valor_nuevo"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            //print($campo_edit." ".$valor_nuevo." ".$item_id_edit);
            $obCon->ActualizaRegistro("$db.$tab", $campo_edit, $valor_nuevo, "ID", $item_id_edit);
            print("OK;Registro Editado");
        break; //Fin caso 1
    
        case 2:// Crear un predocumento    
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $obCon->agregar_predocumento($db,$idUser);
            
            print("OK;Predocumento Creado");
        break;//caso 2
    
        case 3: //marque un predocumento como activo
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $predocumento_id=$obCon->normalizar($_REQUEST["predocumento_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $obCon->ActualizaRegistro("$db.contabilidad_predocumento_contable", "activo", 0, "usuario_id", $idUser);
            $obCon->ActualizaRegistro("$db.contabilidad_predocumento_contable", "activo", 1, "ID", $predocumento_id);
            print("OK;Predocumento Activado");
            
        break;//Fin caso 3
    
        case 4: //consulta si una cuenta registra base
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $cuenta_contable=$obCon->normalizar($_REQUEST["cuenta_contable"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_cuenta=$obCon->DevuelveValores($db.".contabilidad_plan_cuentas_subcuentas", "ID", $cuenta_contable);
            print($datos_cuenta["SolicitaBase"]);
            
        break;//Fin caso 4
    
        case 5: //agrega un movimiento contable
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $predocumento_id=$obCon->normalizar($_REQUEST["predocumento_id"]);
            $cuenta_contable=$obCon->normalizar($_REQUEST["cuenta_contable"]);
            $tercero_id=$obCon->normalizar($_REQUEST["tercero_id"]);
            $concepto=$obCon->normalizar($_REQUEST["concepto"]);
            $tipo_movimiento=$obCon->normalizar($_REQUEST["tipo_movimiento"]);
            $referencia=$obCon->normalizar($_REQUEST["referencia"]);
            $Porcentaje=$obCon->normalizar($_REQUEST["Porcentaje"]);
            $TxtSolicitaBase=$obCon->normalizar($_REQUEST["TxtSolicitaBase"]);
            $Base=$obCon->normalizar($_REQUEST["Base"]);
            $Valor=$obCon->normalizar($_REQUEST["Valor"]);
            
            if($empresa_id==''){
                exit("E1;No se recibió el id de la empresa;empresa_id");
            }
            if($predocumento_id==''){
                exit("E1;No se recibió el id del predocumento;predocumento_id");
            }
            if($cuenta_contable==''){
                exit("E1;Debe seleccionar una cuenta contable;select2-cuenta_contable-container");
            }
            if($tercero_id==''){
                exit("E1;Debe seleccionar un tercero;select2-tercero-container");
            }
            if($concepto==''){
                exit("E1;Debe escribir un concepto;concepto");
            }
            if($tipo_movimiento==''){
                exit("E1;No se recibió el tipo de movimiento;tipo_movimiento");
            }
            if($referencia==''){
                exit("E1;Debe escribir una referencia;referencia");
            }
            if($TxtSolicitaBase==1){
                if(!is_numeric($Porcentaje) or $Porcentaje<=0){
                    exit("E1;El porcentaje debe ser un valor númerico mayor a cero;Porcentaje");
                }
           
                if(!is_numeric($Base) or $Base<=0){
                    exit("E1;La Base debe ser un valor númerico mayor a cero;Base");
                }
            }
            if(!is_numeric($Valor) or $Valor<=0){
                exit("E1;El Valor debe ser un número mayor a cero;Base");
            }
            
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $datos_predocumento=$obCon->DevuelveValores($db.".contabilidad_predocumento_contable", "ID", $predocumento_id);
            $datos_tercero=$obCon->DevuelveValores($db.".terceros", "ID", $tercero_id);
            
            $item_id=$obCon->AgregaMovimientoDocumentoContable($db, $datos_predocumento["documento_contable_id"], $datos_tercero["identificacion"], $cuenta_contable, $tipo_movimiento, $Valor, $concepto, $referencia, "");
            if($TxtSolicitaBase==1 and $Base>0){
                $obCon->agrega_base_documento_contable($db, $datos_predocumento["documento_contable_id"], $concepto, $Base, $Porcentaje, $Valor, $idUser, $item_id);
            }
            
            print("OK;Movimiento Agregado");
            
        break;//Fin caso 5
        
        
        case 6: //eliminar un item de un predocumento
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $tabla_id=$obCon->normalizar($_REQUEST["tabla_id"]);
            $item_id=$obCon->normalizar($_REQUEST["item_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $obCon->BorraReg($db.".contabilidad_documentos_contables_items", "ID", $item_id);
            $obCon->BorraReg($db.".contabilidad_documentos_contables_registro_bases", "idItemDocumentoContable", $item_id);
            print("OK;Item Borrado");
        break;//Fin caso 6
        
        case 7://Crear el documento contable
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $predocumento_id=$obCon->normalizar($_REQUEST["predocumento_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $datos_predocumento=$obCon->DevuelveValores($db.".contabilidad_predocumento_contable", "ID", $predocumento_id);
            foreach ($datos_predocumento as $key => $value) {
                if($value=='' or $value=='0' or $value=='0000-00-00'){
                    if($key=='tipo_documento_contable_id'){
                        exit("E1;No se ha seleccionado un tipo de documento;tipo_documento_id");
                    }
                    if($key=='Fecha'){
                        exit("E1;Debe digitar una Fecha;txt_fecha_predocumento");
                    }
                    if($key=='sucursal_id'){
                        exit("E1;Debe seleccionar una sucursal;sucursal_id");
                    }
                    if($key=='centro_costo_id'){
                        exit("E1;Debe seleccionar un centro de costos;centro_costo_id");
                    }
                    if($key=='observaciones'){
                        exit("E1;Debe digitar las observaciones del documento;observaciones_documento");
                    }
                    if($key=='documento_contable_id'){
                        exit("E1;No se ha asignado un id al predocumento;predocumento_id");
                    }
                    
                }
                
            }
            $obCon->CrearDocumentoContable($db,$datos_predocumento["documento_contable_id"],$datos_predocumento["tipo_documento_contable_id"], $datos_predocumento["Fecha"], $datos_predocumento["observaciones"], $datos_predocumento["sucursal_id"], $datos_predocumento["centro_costo_id"], "", $idUser);
            $nuevo_id=$obCon->getUniqId("dc_");
            $sql="UPDATE $db.contabilidad_predocumento_contable SET documento_contable_id='$nuevo_id', tipo_documento_contable_id='',Fecha='',observaciones='' where ID='$predocumento_id'  ";
            $obCon->Query($sql);
            
            print("OK;Documento Contable Creado");
        break;//Fin caso 7    
        
        case 8://obtiene los totales de las diferentes tablas
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $sql="SELECT COUNT(ID) AS total FROM $db.terceros ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $terceros=$totales["total"];
            $sql="SELECT COUNT(ID) AS total FROM $db.contabilidad_plan_cuentas_subcuentas ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $cuentas=$totales["total"];
            $sql="SELECT COUNT(ID) AS total FROM $db.contabilidad_documentos_contables ";
            $totales=$obCon->FetchAssoc($obCon->Query($sql));
            $documentos=$totales["total"];
            
            print("OK;".$terceros.";".$cuentas.";".$documentos);
            
        break;//Fin caso 8
    
        case 9://Anular un documento contable
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_id=$obCon->normalizar($_REQUEST["documento_id"]);
            $observaciones=$obCon->normalizar($_REQUEST["observaciones_anulacion"]);
            $obCon->anular_documento_contable($db, $documento_id, $observaciones, $idUser);
            
            print("OK;Documento $documento_id Anulado");
            
        break;//Fin caso 9
    
        case 10://Copiar un documento contable
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_id=$obCon->normalizar($_REQUEST["documento_id"]);
            
            $obCon->copiar_documento_contable($db, $documento_id, $idUser);
            
            print("OK;Documento $documento_id Copiado");
            
        break;//Fin caso 10
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>