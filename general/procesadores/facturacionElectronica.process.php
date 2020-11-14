<?php
@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$fecha=date("Y-m-d");

include_once("../clases/facturacion_electronica.class.php");
//include_once("restclient.php");
if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Factura_Electronica($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear facturas electronicas
            if(!isset($_REQUEST["idFactura"])){
                $sql="SELECT t1.idFacturas,t1.NumeroFactura FROM facturas t1 
                    WHERE TipoFactura='FE' AND NOT EXISTS (SELECT 1 FROM facturas_electronicas_log t2 WHERE t1.idFacturas=t2.idFactura AND t2.Estado<20) LIMIT 1";

                $DatosConsulta=$obCon->FetchAssoc($obCon->Query($sql));
                $idFactura=$DatosConsulta["idFacturas"];
                $NumeroFactura=$DatosConsulta["NumeroFactura"];
            }else{
                $idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
                $sql="SELECT ID FROM facturas_electronicas_log WHERE idFactura='$idFactura' AND Estado=1";                
                $DatosLog=$obCon->FetchAssoc($obCon->Query($sql));
                if($DatosLog["ID"]>0){
                    exit("E1;La Factura $idFactura ya fue reportada");
                }
                $sql="SELECT NumeroFactura FROM facturas WHERE idFacturas='$idFactura'";
                $DatosConsulta=$obCon->FetchAssoc($obCon->Query($sql));
                $NumeroFactura=$DatosConsulta["NumeroFactura"];
            }
            
            
            $DatosServidor=$obCon->DevuelveValores("servidores", "ID", 104);            
            $url=$DatosServidor["IP"];
            if($idFactura<>''){
                $sql="SELECT COUNT(ID) AS TotalItems FROM facturas_items WHERE idFactura='$idFactura'";
                $DatosTotal=$obCon->FetchAssoc($obCon->Query($sql));
                $response="";
                $Estado=0;
                if($DatosTotal["TotalItems"]>0){ //Verifico que la factura tenga items
                    $body=$obCon->JSONFactura($idFactura);
                    
                    $response = $obCon->callAPI('POST', $url, $body); 
                    
                }else{
                    $Estado=11;
                }
                $obCon->FacturaElectronica_Registre_Respuesta_Server($idFactura,$response,$Estado);
                exit("OK;Factura $NumeroFactura Reportada");
            }else{
                exit("RE;No hay Facturas a Generar");
            }    
        break; //Fin caso 1
        
        case 2: // Verificar las facturas Electronicas, si se generaron bien o no
            
            $sql="SELECT * FROM facturas_electronicas_log WHERE Estado=0 LIMIT 1";
            
            $DatosLogFactura=$obCon->FetchAssoc($obCon->Query($sql));
            $idFactura=$DatosLogFactura["idFactura"];
            $idLog=$DatosLogFactura["ID"];
            if($idFactura<>''){
                if($DatosLogFactura["RespuestaCompletaServidor"]==''){
                    $obCon->ActualizaRegistro("facturas_electronicas_log", "Estado", 20, "ID", $idLog);
                    
                }else{
                    $JSONFactura= json_decode($DatosLogFactura["RespuestaCompletaServidor"]);
                    if((property_exists($JSONFactura, "uuid"))){
                        $CUFE=$JSONFactura->uuid;
                        $obCon->ActualizaRegistro("facturas_electronicas_log", "UUID", $CUFE, "ID", $idLog);
                    }
                    
                    
                    if((property_exists($JSONFactura, "responseDian"))){
                        $Parametros=$obCon->DevuelveValores("configuracion_general", "ID", 27); //Contiene el metodo de envio del documento a la DIAN
                        $MetodoEnvio=$Parametros["Valor"]; //1 sincrono 2 asincrono
                        if($MetodoEnvio==1){
                            $RespuestaReporte=$JSONFactura->responseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid;
                        }
                        if($MetodoEnvio==2){
                            $RespuestaReporte=$JSONFactura->responseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->_attributes->nil;
                        }
                        
                    }else{
                        
                        $obCon->ActualizaRegistro("facturas_electronicas_log", "Estado", 13, "ID", $idLog);
                        exit("OK;Factura $idFactura Verificada");
                    }
                    
                    if($RespuestaReporte=='true'){
                        
                        $obCon->ActualizaRegistro("facturas_electronicas_log", "Estado", 1, "ID", $idLog);
                        
                    }else{
                        $obCon->ActualizaRegistro("facturas_electronicas_log", "Estado", 10, "ID", $idLog);
                    }
                }
                exit("OK;Factura $idFactura Verificada");
            }else{
                exit("RE;No hay Facturas por validar");
            }    
        break;
        
        case 3://Crear los PDF
            $sql="SELECT * FROM facturas_electronicas_log WHERE Estado=1 AND PDFCreado=0 LIMIT 1";
            
            $DatosLogFactura=$obCon->FetchAssoc($obCon->Query($sql));
            $idFactura=$DatosLogFactura["idFactura"];
            $idLog=$DatosLogFactura["ID"];
            if($idFactura<>''){
                $JSONFactura= json_decode($DatosLogFactura["RespuestaCompletaServidor"]);
                $NumeroFactura=$JSONFactura->number;
                $Ruta=$obCon->CrearPDFDesdeBase64($JSONFactura->pdfBase64Bytes,$NumeroFactura);
                $obCon->ActualizaRegistro("facturas_electronicas_log", "PDFCreado", 1, "ID", $idLog);
                $obCon->ActualizaRegistro("facturas_electronicas_log", "RutaPDF", $Ruta, "ID", $idLog);
                exit("OK;PDF de la Factura Electronica $idFactura Creado Satisfactoriamente");
            }else{
                $sql="SELECT * FROM notas_credito WHERE Estado=1 AND PDFCreado=0 LIMIT 1";
            
                $DatosNota=$obCon->FetchAssoc($obCon->Query($sql));
                
                $idNota=$DatosNota["ID"];
                if($idNota<>''){
                    $Ruta="";
                    $JSONFactura= json_decode($DatosNota["RespuestaCompletaServidor"]);
                    if(is_object($JSONFactura)){
                        if(property_exists($JSONFactura, "pdfBase64Bytes")){
                            $NumeroNota="NOTC".$idNota;
                            $Ruta=$obCon->CrearPDFDesdeBase64($JSONFactura->pdfBase64Bytes,$NumeroNota);
                            $obCon->ActualizaRegistro("notas_credito", "PDFCreado", 1, "ID", $idNota);
                            $obCon->ActualizaRegistro("notas_credito", "RutaPDF", $Ruta, "ID", $idNota);
                        }else{
                            $obCon->ActualizaRegistro("notas_credito", "PDFCreado", 4, "ID", $idNota);
                        }
                        
                    }else{
                        $obCon->ActualizaRegistro("notas_credito", "PDFCreado", 3, "ID", $idNota);
                    }
                    
                    
                    exit("OK;PDF de la Nota credito $idNota Creado Satisfactoriamente");
                }else{
                    exit("RE;No hay Facturas para crear PDF");
                }
                
            }
        break;//Fin caso 3 
    
        case 4://Crear los zip con los XML adentro
            $sql="SELECT * FROM facturas_electronicas_log WHERE Estado=1 AND ZIPCreado=0 LIMIT 1";
            
            $DatosLogFactura=$obCon->FetchAssoc($obCon->Query($sql));
            $idFactura=$DatosLogFactura["idFactura"];
            $idLog=$DatosLogFactura["ID"];
            if($idFactura<>''){
                $JSONFactura= json_decode($DatosLogFactura["RespuestaCompletaServidor"]);
                $NumeroFactura=$JSONFactura->number;
                $Ruta=$obCon->CrearZIPDesdeBase64($JSONFactura->zipBase64Bytes,$NumeroFactura);
                $obCon->ActualizaRegistro("facturas_electronicas_log", "ZIPCreado", 1, "ID", $idLog);
                $obCon->ActualizaRegistro("facturas_electronicas_log", "RutaXML", $Ruta, "ID", $idLog);
                exit("OK;ZIP con XML de la Factura Electronica $idFactura Creado Satisfactoriamente");
            }else{
                exit("RE;No hay Facturas para crear XML");
            }
        break;//Fin caso 4
        
        case 5://Editar Facturas que hayan sido reparadas
            $sql="SELECT * FROM facturas_electronicas_log WHERE Estado>=10 OR Estado<20";
            $Consulta=$obCon->Query($sql);
            while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                $idFactura=$DatosConsulta["idFactura"];
                
                $sql="SELECT ID FROM facturas_electronicas_log WHERE idFactura='$idFactura' AND Estado='1'";
                $DatosValidacion=$obCon->FetchAssoc($obCon->Query($sql));
                if($DatosValidacion["ID"]>0){
                    $sql="UPDATE facturas_electronicas_log SET Estado=30 WHERE idFactura='$idFactura' AND Estado>=10 AND Estado<20";
                    $obCon->Query($sql);
                }
            }
            exit("OK;Actualizacion de Documentos corregidos Realizada");
        break;//Fin caso 5
        
        case 6://Enviar una factura electronica por correo
            include_once("../clases/mail.class.php");            
            $obMail=new TS_Mail($idUser);
            
            $idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
            $DatosFactura=$obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
            $DatosCliente=$obCon->DevuelveValores("clientes", "idClientes", $DatosFactura["Clientes_idClientes"]);
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "idEmpresaPro", $DatosFactura["EmpresaPro_idEmpresaPro"]);
            if(!filter_var($DatosCliente["Email"], FILTER_VALIDATE_EMAIL)){
                exit("E1;El tercero no cuenta con un Mail Válido: ".$DatosCliente["Email"]);
            }
            $para=$DatosCliente["Email"];
            $de="technosolucionesfe@gmail.com";
            $nombreRemitente=$DatosEmpresa["RazonSocial"];
            $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 22); //Almecena el asunto del correo
            $asunto=$Configuracion["Valor"];            
            $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 23); //Almecena el cuerpo del mensaje del correo
            $mensajeHTML=$Configuracion["Valor"];
            $mensajeHTML= str_replace("@RazonSocial", $DatosCliente["RazonSocial"], $mensajeHTML);
            $mensajeHTML= str_replace("@NumeroFactura", $DatosFactura["NumeroFactura"], $mensajeHTML);
            $sql="SELECT RutaPDF,RutaXML FROM facturas_electronicas_log WHERE idFactura='$idFactura' AND Estado='1'";
            $RutasFE=$obCon->FetchArray($obCon->Query($sql));            
            $Adjunto=$RutasFE;
            //$status=$obMail->EnviarMailXPHPNativo($para, $de, $nombreRemitente, $asunto, $mensajeHTML,$Adjunto);
            //$status=$obMail->EnviarMailXPHPMailer($para, $de, $nombreRemitente, $asunto, $mensajeHTML,$Adjunto);
            if($status=='OK'){
                exit("OK;Envío Realizado");
            }else{
                exit("E1;No se pudo realizar el envío");
            }
            
        break;//Fin caso 6
        
        case 7://Enviar las facturas electronicas por correo
            include_once("../clases/mail.class.php");            
            $obMail=new TS_Mail($idUser);
            
            $sql="SELECT * FROM facturas_electronicas_log WHERE Estado=1 AND EnviadoPorMail=0 LIMIT 1";
            
            $DatosLogFactura=$obCon->FetchAssoc($obCon->Query($sql));
            $idFactura=$DatosLogFactura["idFactura"];
            $idLog=$DatosLogFactura["ID"];
            if($idFactura<>''){
                $DatosFactura=$obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
                $DatosCliente=$obCon->DevuelveValores("clientes", "idClientes", $DatosFactura["Clientes_idClientes"]);
                $DatosEmpresa=$obCon->DevuelveValores("empresapro", "idEmpresaPro", $DatosFactura["EmpresaPro_idEmpresaPro"]);
                if(!filter_var($DatosCliente["Email"], FILTER_VALIDATE_EMAIL)){
                    exit("E1;El tercero no cuenta con un Mail Válido: ".$DatosCliente["Email"]);
                }
                $para=$DatosCliente["Email"];
                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 24); //Almecena el correo que envia
                $de=$Configuracion["Valor"];
                $nombreRemitente=$DatosEmpresa["RazonSocial"];
                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 22); //Almecena el asunto del correo
                $asunto=$Configuracion["Valor"];            
                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 23); //Almecena el cuerpo del mensaje del correo
                $mensajeHTML=$Configuracion["Valor"];
                $mensajeHTML= str_replace("@RazonSocial", $DatosCliente["RazonSocial"], $mensajeHTML);
                $mensajeHTML= str_replace("@NumeroFactura", $DatosFactura["NumeroFactura"], $mensajeHTML);
                $sql="SELECT RutaPDF,RutaXML FROM facturas_electronicas_log WHERE idFactura='$idFactura' AND Estado='1'";
                $RutasFE=$obCon->FetchArray($obCon->Query($sql));            
                $Adjunto=$RutasFE;
                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 25); //Determina el metodo a usar para enviar el correo al cliente
                if($Configuracion["Valor"]==1){
                    //$status=$obMail->EnviarMailXPHPNativo($para, $de, $nombreRemitente, $asunto, $mensajeHTML,$Adjunto);
                }
                if($Configuracion["Valor"]==2){
                    //$status=$obMail->EnviarMailXPHPMailer($para, $de, $nombreRemitente, $asunto, $mensajeHTML,$Adjunto);
                }
                $status="OK";
                if($status=='OK'){
                    $obCon->ActualizaRegistro("facturas_electronicas_log", "EnviadoPorMail", 1, "ID", $idLog);
                    exit("OK;Envío de la factura $idFactura Realizado");
                }else{
                    exit("E1;No se pudo realizar el envío de la factura $idFactura");
                }
            }else{
                exit("RE;No hay Facturas para enviar por Mail");
            }    
        break;//Fin caso 7
        
        case 8: //Crear Notas Credito Electronicas
            if(!isset($_REQUEST["idNota"])){
                $sql="SELECT ID FROM notas_credito  
                    WHERE Estado=0 LIMIT 1";

                $DatosConsulta=$obCon->FetchAssoc($obCon->Query($sql));
                $idNota=$DatosConsulta["ID"];
                
            }else{
                $idNota=$obCon->normalizar($_REQUEST["idNota"]);
                $sql="SELECT ID FROM notas_credito WHERE Estado=1 AND ID='$idNota'";                
                $DatosLog=$obCon->FetchAssoc($obCon->Query($sql));
                if($DatosLog["ID"]>0){
                    exit("E1;La Nota Credito $idNota ya fue enviada");
                }
                
            }
            
            
            $DatosServidor=$obCon->DevuelveValores("servidores", "ID", 105); //Ruta para enviar las notas credito            
            $url=$DatosServidor["IP"];
            if($idNota<>''){
                $sql="SELECT COUNT(ID) AS TotalItems FROM notas_credito_items WHERE idNotaCredito='$idNota'";
                $DatosTotal=$obCon->FetchAssoc($obCon->Query($sql));
                $response="";
                $Estado=0;
                
                if($DatosTotal["TotalItems"]>0){ //Verifico que la factura tenga items
                    $body=$obCon->JSONNotaCredito($idNota);
                    $response = $obCon->callAPI('POST', $url, $body);  
                    $sql="UPDATE notas_credito SET RespuestaCompletaServidor='$response' WHERE ID='$idNota'";
                    $obCon->Query($sql);
                    $JsonRespuesta= json_decode($response);
                    if((property_exists($JsonRespuesta, "responseDian"))){
                        
                        $Parametros=$obCon->DevuelveValores("configuracion_general", "ID", 27); //Contiene el metodo de envio del documento a la DIAN
                        $MetodoEnvio=$Parametros["Valor"]; //1 sincrono 2 asincrono
                        if($MetodoEnvio==1){
                            $RespuestaReporte=$JsonRespuesta->responseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid;
                        
                        }
                        if($MetodoEnvio==2){
                            $RespuestaReporte=$JsonRespuesta->responseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->_attributes->nil;
                        }
                        
                        if($RespuestaReporte==true){
                            $obCon->ActualizaRegistro("notas_credito", "Estado", 1, "ID", $idNota);
                        }else{
                            $obCon->ActualizaRegistro("notas_credito", "Estado", 11, "ID", $idNota);
                        }
                    }else{
                        
                        $obCon->ActualizaRegistro("notas_credito", "Estado", 11, "ID", $idNota);
                        exit("OK;Nota Credito $idNota Enviada");
                    }
                    
                }else{
                    $Estado=11;
                    $obCon->ActualizaRegistro("notas_credito", "Estado", $Estado, "ID", $idNota);
                }
                
                
                exit("OK;Nota Credito $idNota Enviada");
            }else{
                exit("RE;No hay Notas a Generar");
            }    
        break; //Fin caso 8
        
        case 9://Obtener logs de un documento electronico
            $TipoDocumento=$obCon->normalizar($_REQUEST["TipoDocumento"]);
            $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
            if($TipoDocumento==1 or $TipoDocumento==4){
                $Tabla="facturas_electronicas_log";
            }
            if($TipoDocumento==2 or $TipoDocumento==3){
                $Tabla="notas_credito";
            }
             
            $DatosServidor=$obCon->DevuelveValores("servidores", "ID", 106); //Ruta para validar los logs de un documento         
            $url=$DatosServidor["IP"];
            if($idDocumento<>''){
                $sql="SELECT RespuestaCompletaServidor FROM $Tabla WHERE ID='$idDocumento'";
                $DatosConsulta=$obCon->FetchAssoc($obCon->Query($sql));
                $response=$DatosConsulta["RespuestaCompletaServidor"];
                $JsonRespuesta= json_decode($response);
                if((property_exists($JsonRespuesta, "uuid"))){
                    $uuid=$JsonRespuesta->uuid;
                    $url=$url.$uuid;
                    $body="";
                    $response = $obCon->callAPI('POST', $url, $body);
                    $sql="UPDATE $Tabla SET LogsDocumento='$response' WHERE ID='$idDocumento'";
                    $obCon->Query($sql);
                    $JsonLogs= json_decode($response);
                    /*
                    if(!property_exists($JsonLogs, "uuid")){
                    
                        $JsonLogs=$response;
                    }
                     * 
                     */
                    print("<pre>");
                    print_r($JsonLogs);
                    print("</pre>");
                    exit();
                }else{
                    exit("E1;No se encontró el uuid del documento");
                }
                
            }else{
                exit("RE;No hay Notas para verificar");
            }
        break;//Fin caso 9    
        
        case 10://Validar los acuse de recibo de los documentos 
            $Tabla="facturas_electronicas_log";
            $sql="SELECT ID,idFactura,RespuestaCompletaServidor as Respuesta FROM $Tabla WHERE AcuseRecibo=''";
            $Consulta=($obCon->Query($sql));
            while($DatosDocumento=$obCon->FetchAssoc($Consulta)){
                if($DatosDocumento["Respuesta"]<>''){
                    $idDocumento=$DatosDocumento["ID"];
                    $response=$DatosDocumento["Respuesta"];
                    $JsonRespuesta= json_decode($response);
                    if(is_object($JsonRespuesta)){
                        if((property_exists($JsonRespuesta, "uuid"))){
                            $DatosServidor=$obCon->DevuelveValores("servidores", "ID", 106); //Ruta para validar los logs de un documento         
                            $url=$DatosServidor["IP"];
                            $uuid=$JsonRespuesta->uuid;
                            $url=$url.$uuid;
                            $body="";
                            $response2 = $obCon->callAPI('POST', $url, $body);
                            $sql="UPDATE $Tabla SET LogsDocumento='$response2' WHERE ID='$idDocumento'";
                            $obCon->Query($sql);
                            $JsonLogs= json_decode($response2);
                            $Acuse=($JsonLogs[0]->acknowledgment_received);
                            if($Acuse==''){
                                $idFactura=$DatosDocumento["idFactura"];
                                $sql="SELECT Fecha FROM facturas WHERE idFacturas='$idFactura'";
                                $DatosFactura=$obCon->FetchAssoc($obCon->Query($sql));
                                $FechaActual = date("Y-m-d");
                                $date1 = new DateTime($DatosFactura["Fecha"]);
                                $date2 = new DateTime($FechaActual);
                                $diff = $date1->diff($date2);                                
                                $DiasTranscurridos= $diff->days;
                                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 26); //Contiene el plazo que tiene un cliente para determinar el acuse de recibo
                                $DiasPlazoAcuse=$Configuracion["Valor"];
                                if($Configuracion["Valor"]==''){
                                    $DiasPlazoAcuse=2;
                                }
                                if($DiasTranscurridos>$DiasPlazoAcuse){
                                    $Acuse=11;
                                }
                                $sql="UPDATE $Tabla SET AcuseRecibo='$Acuse' WHERE ID='$idDocumento'";
                                $obCon->Query($sql);
                            }else{
                                
                                $sql="UPDATE $Tabla SET AcuseRecibo='$Acuse' WHERE ID='$idDocumento'";
                                $obCon->Query($sql);                                
                                
                            }
                            
                            
                            

                        }  
                    }
                }    
            }
            
            $Tabla="notas_credito";
            $sql="SELECT ID,Fecha,RespuestaCompletaServidor as Respuesta FROM $Tabla WHERE AcuseRecibo=''";
            $Consulta=($obCon->Query($sql));
            while($DatosDocumento=$obCon->FetchAssoc($Consulta)){
                if($DatosDocumento["Respuesta"]<>''){
                    $idDocumento=$DatosDocumento["ID"];
                    $response=$DatosDocumento["Respuesta"];
                    $JsonRespuesta= json_decode($response);
                    if(is_object($JsonRespuesta)){
                        if((property_exists($JsonRespuesta, "uuid"))){
                            $DatosServidor=$obCon->DevuelveValores("servidores", "ID", 106); //Ruta para validar los logs de un documento         
                            $url=$DatosServidor["IP"];
                            $uuid=$JsonRespuesta->uuid;
                            $url=$url.$uuid;
                            $body="";
                            $response2 = $obCon->callAPI('POST', $url, $body);
                            $sql="UPDATE $Tabla SET LogsDocumento='$response2' WHERE ID='$idDocumento'";
                            $obCon->Query($sql);
                            $JsonLogs= json_decode($response2);
                            
                            $Acuse=$JsonLogs[0]->acknowledgment_received;                        
                            
                            if($Acuse==''){
                                
                                $FechaActual = date("Y-m-d");
                                $date1 = new DateTime($DatosDocumento["Fecha"]);
                                $date2 = new DateTime($FechaActual);
                                $diff = $date1->diff($date2);                                
                                $DiasTranscurridos= $diff->days;
                                $Configuracion=$obCon->DevuelveValores("configuracion_general", "ID", 26); //Contiene el plazo que tiene un cliente para determinar el acuse de recibo
                                $DiasPlazoAcuse=$Configuracion["Valor"];
                                if($Configuracion["Valor"]==''){
                                    $DiasPlazoAcuse=2;
                                }
                                if($DiasTranscurridos>$DiasPlazoAcuse){
                                    $Acuse=11;
                                }
                                $sql="UPDATE $Tabla SET AcuseRecibo='$Acuse' WHERE ID='$idDocumento'";
                                $obCon->Query($sql);
                            }else{
                                
                                $sql="UPDATE $Tabla SET AcuseRecibo='$Acuse' WHERE ID='$idDocumento'";
                                $obCon->Query($sql);                                
                                
                            }
                            
                        } 
                    }
                }    
            }
            print("OK;Verificacion de acuse de recibo terminado");
            
        break;//Fin caso 10    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>
