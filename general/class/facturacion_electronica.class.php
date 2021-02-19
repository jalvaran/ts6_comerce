<?php
if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}
/* 
 * Clase que realiza los procesos de facturacion electronica
 * Julian Alvaran
 * Techno Soluciones SAS
 */

class Factura_Electronica extends conexion{
    
    
    public function limpiar_cadena($string) {
        $string = htmlentities($string);
        $string = preg_replace('/\&(.)[^;]*;/', '', $string);
        $string = str_replace('\n', '', $string);
        $string = trim(preg_replace('/[\r\n|\n|\r]+/', '', $string));
        return $string;
    }
        
    public function callAPI($method, $url,$TokenTS5, $data) {
        
               
        $curl = curl_init();

        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Authorization: Bearer '.$TokenTS5,
           'Content-Type: application/json',
           'Accept: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result){die("Connection Failure");}
        curl_close($curl);
        return $result;
    }
    /**
     * Funcion para retornar el json para crear la empresa
     * @param type $empresa_id
     * @return type
     */
    public function JSONCrearEmpresa($empresa_id) {
        $DatosEmpresa=$this->DevuelveValores("empresapro", "ID", $empresa_id);
        $parametros=$this->DevuelveValores("tipo_documento_identificacion", "codigo", $DatosEmpresa["TipoDocumento"]);
        $tipo_identificacion_id=$parametros["ID"];
        $parametros=$this->DevuelveValores("catalogo_municipios", "CodigoDANE", $DatosEmpresa["CodigoDaneCiudad"]);
        $municipality_id=$parametros["ID"];
        $json_empresa='{ 
            "type_document_identification_id": '.$tipo_identificacion_id.',
            "type_organization_id": '.$DatosEmpresa["TipoPersona"].',
            "type_regime_id": '.$DatosEmpresa["Regimen"].',
            "type_liability_id": '.$DatosEmpresa["Obligaciones"].',
            "business_name": "'.$DatosEmpresa["RazonSocial"].'",
            "merchant_registration": "'.$DatosEmpresa["MatriculoMercantil"].'",
            "municipality_id": '.$municipality_id.',
            "address": "'.$DatosEmpresa["Direccion"].'",
            "phone": '.$DatosEmpresa["Telefono"].',
            "ciius": "'.$DatosEmpresa["ActividadesEconomicas"].'",    
            "email": "'.$DatosEmpresa["Email"].'"
        }' ;
        return($json_empresa);
    }
    
    public function JSONCrearSoftware($software_id,$software_pin) {
        
        $json_data='{ 
            "id": "'.$software_id.'",
            "pin": '.$software_pin.'
            
        }' ;
        return($json_data);
    }
    
    public function JSONCrearCertificado($CertificadoBase64,$clave_certificado) {
        
        $json_data='{ 
            "certificate": "'.$CertificadoBase64.'",
            "password": "'.$clave_certificado.'"
            
        }' ;
        return($json_data);
    }
    
    public function JSONCrearResolucionFacturacion($type_document_id,$prefix,$from,$to,$number_resolution,$resolution_date,$technical_key,$date_from,$date_to) {
        
        $json_data='{ 
            "type_document_id": '.$type_document_id.',
            "prefix": "'.$prefix.'",
            "from": '.$from.',
            "to": '.$to.',
            "resolution":'.$number_resolution.',
            "resolution_date":"'.$resolution_date.'",
            "technical_key":"'.$technical_key.'",
            "date_from":"'.$date_from.'",
            "date_to":"'.$date_to.'"            
        }' ;
        return($json_data);
    }
    
    public function crear_actualizar_resolucion_db($empresa_id,$type_document_id,$prefix,$from,$to,$number_resolution,$resolution_date,$technical_key,$date_from,$date_to,$resolucion_id_api,$condition) {
        $Tabla="empresa_resoluciones";
        $Datos["empresa_id"]=$empresa_id;
        $Datos["tipo_documento_id"]=$type_document_id;
        $Datos["prefijo"]=$prefix;
        $Datos["numero_resolucion"]=$number_resolution;
        $Datos["fecha_resolucion"]=$resolution_date;
        $Datos["llave_tecnica"]=$technical_key;
        $Datos["desde"]=$from;
        $Datos["hasta"]=$to;
        $Datos["proximo_numero_documento"]=$from;
        $Datos["fecha_desde"]=$date_from;
        $Datos["fecha_hasta"]=$date_to;
        $Datos["resolucion_id_api"]=$resolucion_id_api;
        if($condition==""){
            $sql=$this->getSQLInsert($Tabla, $Datos);
        }else{
            $sql=$this->getSQLUpdate($Tabla, $Datos);
            $sql.=" ".$condition;
        }
        $this->Query($sql);
    }
    
    public function JSONCrearResolucionNotas($type_document_id,$prefix,$from,$to) {
        
        $json_data='{ 
            "type_document_id": '.$type_document_id.',
            "prefix": "'.$prefix.'",
            "from": '.$from.',
            "to": '.$to.'            
        }' ;
        return($json_data);
    }
        
    public function CrearPDFDesdeBase64($pdf_base64,$NumeroFactura) {
        
        $DatosRuta=$this->DevuelveValores("configuracion_general", "ID", 16);
        $Ruta=$DatosRuta["Valor"];
        $NombreArchivo=$DatosRuta["Valor"].$NumeroFactura."_FE.pdf";
        $pdf_decoded = base64_decode($pdf_base64);
        
        $pdf = fopen ($NombreArchivo,'w');
        fwrite ($pdf,$pdf_decoded);
        fclose ($pdf);
        return($NombreArchivo);
    }
    
    public function CrearZIPDesdeBase64($zip_base64,$NumeroFactura) {
        
        $DatosRuta=$this->DevuelveValores("configuracion_general", "ID", 16);
        $Ruta=$DatosRuta["Valor"];
        $NombreArchivo=$DatosRuta["Valor"].$NumeroFactura."_FE.zip";
        $pdf_decoded = base64_decode($zip_base64);
        
        $pdf = fopen ($NombreArchivo,'w');
        fwrite ($pdf,$pdf_decoded);
        fclose ($pdf);
        return($NombreArchivo);
    }
    
    public function registra_documento_electronico($db,$resolucion_id,$tipo_documento_id,$prefijo,$numero,$tercero_id,$usuario_id,$notas,$orden_compra,$forma_pago,$documento_asociado_id) {
        $tab="$db.documentos_electronicos";
        if($tipo_documento_id==1){
            $prefijo_llave="fv_";
        }
        if($tipo_documento_id==5){
            $prefijo_llave="nc_";
        }
        if($tipo_documento_id==6){
            $prefijo_llave="nd_";
        }
        
        $documento_electronico_id=$this->getUniqId($prefijo_llave);
        $Datos["documento_electronico_id"]=$documento_electronico_id;
        $Datos["fecha"]=date("Y-m-d");
        $Datos["hora"]=date("H:i:s");
        $Datos["tipo_documento_id"]=$tipo_documento_id;
        $Datos["resolucion_id"]=$resolucion_id;
        $Datos["prefijo"]=$prefijo;
        $Datos["numero"]=$numero;
        $Datos["tercero_id"]=$tercero_id;
        $Datos["usuario_id"]=$usuario_id;
        $Datos["notas"]=$notas;  
        $Datos["orden_compra"]=$orden_compra;  
        $Datos["forma_pago"]=$forma_pago;  
        $Datos["documento_asociado_id"]=$documento_asociado_id;  
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
        return($documento_electronico_id);
    }
    
    public function copiar_items_prefactura_items_documento($db,$prefactura_id,$documento_electronico_id) {
        $sql="INSERT INTO $db.documentos_electronicos_items (`documento_electronico_id`,`item_id`,`valor_unitario`,`cantidad`,`subtotal`,`impuestos`,`total`,`porcentaje_iva_id`,`usuario_id`) 
                SELECT '$documento_electronico_id',item_id,valor_unitario,cantidad,subtotal,impuestos,total,porcentaje_iva_id,usuario_id 
                FROM $db.factura_prefactura_items WHERE prefactura_id='$prefactura_id' 
                
                ";
        $this->Query($sql);
    }
    
    public function crear_documento_electronico_desde_prefactura($empresa_id,$tipo_documento_id,$prefactura_id,$tercero_id,$resolucion_id,$documento_asociado_id,$usuario_id) {
        $datos_empresa=$this->DevuelveValores("empresapro", "ID", $empresa_id);
        $empresa_db=$datos_empresa["db"];
        $datos_prefactura=$this->DevuelveValores("$empresa_db.factura_prefactura", "ID", $prefactura_id);
        //$datos_tercero=$this->DevuelveValores("$empresa_db.terceros", "ID", $tercero_id);
        
        $datos_resolucion=$this->DevuelveValores("empresa_resoluciones", "ID", $resolucion_id);
        
        if($datos_resolucion["estado"]==2){
            exit("E1;La resolución seleccionada ya fué completada");
        }
        if($datos_resolucion["estado"]==3){
            exit("E1;La resolución seleccionada ya está vencida");
        }
        $prefijo_resolucion=$datos_resolucion["prefijo"];
        $sql="SELECT MAX(numero) as numero FROM $empresa_db.documentos_electronicos WHERE tipo_documento_id='$tipo_documento_id' and resolucion_id='$resolucion_id' and prefijo='$prefijo_resolucion'";
        $datos_validacion=$this->FetchAssoc($this->Query($sql));
        if($datos_validacion["numero"]=='' or $datos_validacion["numero"]==0){
            $datos_validacion["numero"]=$datos_resolucion["proximo_numero_documento"]-1;
        }
        $numero=$datos_validacion["numero"]+1;
        if($numero>$datos_resolucion["hasta"]){
            exit("E1;la resolución no ya fué completada");
        }
        $notas=$this->limpiar_cadena($datos_prefactura["observaciones"]);
        $orden_compra=$this->limpiar_cadena($datos_prefactura["orden_compra"]);
        
        $documento_electronico_id=$this->registra_documento_electronico($empresa_db,$resolucion_id,$tipo_documento_id,$datos_resolucion["prefijo"],$numero,$tercero_id,$usuario_id,$notas,$orden_compra,$datos_prefactura["forma_pago"],$documento_asociado_id);
        $this->copiar_items_prefactura_items_documento($empresa_db, $prefactura_id, $documento_electronico_id);
        return($documento_electronico_id);
        
    }
    
    public function json_datos_generales_documento($numero_documento,$sync,$send,$fecha,$hora,$tipo_documento,$tipo_organizacion_id,$tipo_regimen_id,$resolution_id) {
        $json=' 
            "number": '.$numero_documento.',
            "sync": '.$sync.',
            "send": '.$send.',
            "date": "'.$fecha.'", 
            "time": "'.$hora.'", 
            "type_document_id": '.$tipo_documento.',
            "type_organization_id": '.$tipo_organizacion_id.',
            "type_regime_id": '.$tipo_regimen_id.',
            "resolution_id": '.$resolution_id.'    
    
                   ';
        return($json);
    }
    
    public function json_datos_tercero($identification_number,$type_organization_id,$type_document_identification_id,$type_regime_id,$name,$phone,$address,$email,$municipality_id,$merchant_registration="NA") {
        $json='
            
            "customer": {
                "identification_number": '.$identification_number.',
                "type_organization_id": '.$type_organization_id.',
                "type_document_identification_id": '.$type_document_identification_id.',    
                "type_regime_id": '.$type_regime_id.',     
                "name": "'.$name.'",
                "phone": "'.$phone.'",
                "address": "'.$address.'",
                "email": "'.$email.'",
                "municipality_id": "'.$municipality_id.'",
                "merchant_registration": "'.$merchant_registration.'"
            } 
    
                   ';
        return($json);
    }
    
    public function json_orden_compra($orden_compra) {
        $json='
            "order_reference": {
                "id": "'.$orden_compra.'" 
            }';
        return($json);
    }
    
    public function json_forma_pago($forma_pago_id,$fecha_vencimiento,$payment_method_id=10,$duration_measure=30) {
        $json='
            "payment_forms": [{
                "payment_form_id": "'.$forma_pago_id.'",
                "payment_method_id": "'.$payment_method_id.'",
                "payment_due_date": "'.$fecha_vencimiento.'",
                "duration_measure": "'.$duration_measure.'"
            }]';
        return($json);
    }
    
    public function json_notas($notas) {
        $json=' 
            "notes": [{
                "text":"'.$notas.'"
            }]';
        return($json);
    }
    
    public function json_impuestos_totales($db,$documento_electronico_id) {
        
        $Totales["subtotal"]=0;
        $Totales["base_gravable"]=0;
        $Totales["impuestos"]=0;
        $json=' 
                "tax_totals": [';
        
        $sql="SELECT SUM(t1.subtotal) as subtotal, SUM(t1.impuestos) as impuestos, SUM(t1.total) as total,t1.porcentaje_iva_id, 
                (SELECT impuesto_api_id FROM porcentajes_iva t2 WHERE t2.ID=t1.porcentaje_iva_id) as impuesto_api_id,
                (SELECT round(porcentaje,2) FROM porcentajes_iva t2 WHERE t2.ID=t1.porcentaje_iva_id) as porcentaje
                FROM $db.documentos_electronicos_items t1 
                WHERE t1.documento_electronico_id='$documento_electronico_id' GROUP BY t1.porcentaje_iva_id ";
        $Consulta=$this->Query($sql);
        
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            
            $Totales["subtotal"]=$Totales["subtotal"]+$datos_consulta["subtotal"];
            //if($datos_consulta["impuestos"]>0){
            $Totales["base_gravable"]=$Totales["base_gravable"]+$datos_consulta["subtotal"];
            //}            
            $Totales["impuestos"]=$Totales["impuestos"]+$datos_consulta["impuestos"];
            
            $json.='
                    {
                        "tax_id": '.$datos_consulta["impuesto_api_id"].',
                        "percent": "'.$datos_consulta["porcentaje"].'",
                        "tax_amount": "'.round($datos_consulta["impuestos"],2).'",
                        "taxable_amount": "'.round($datos_consulta["subtotal"],2).'"
                    },';
        }
        $json= substr($json, 0,-1);
        $json.="]";
        $Totales["json"]=$json;
        return($Totales);
    }
        
    public function json_totales_documento($subtotal,$base_gravable,$total_pagar,$tipo_documento_id=1) {
        
        $titulo="legal_monetary_totals";
        if($tipo_documento_id==6){//si es una nota credito
            $titulo="requested_monetary_totals";
        }
        $json='
            "'.$titulo.'":
                { 
                    "line_extension_amount": "'.round($subtotal,2).'",
                    "tax_exclusive_amount": "'.round($base_gravable,2).'",
                    "tax_inclusive_amount": "'.round($total_pagar,2).'",
                    "allowance_total_amount": "0.00",
                    "charge_total_amount": "0.00",    
                    "payable_amount": "'.round($total_pagar,2).'"
                }';
        return($json);
    }
    
    public function json_items_documento_electronico($db,$documento_electronico_id,$tipo_documento_id=1) {
        if($tipo_documento_id==1){//Factura electronica
            $json='
                    "invoice_lines":[';
        }
        if($tipo_documento_id==5){//nota credito
            $json='
                    "credit_note_lines":[';
        }
        
        if($tipo_documento_id==6){//nota debito
            $json='
                    "debit_note_lines":[';
        }
        
        $sql="SELECT t1.subtotal, t1.impuestos , t1.total ,t1.porcentaje_iva_id,t1.cantidad, t1.valor_unitario,
                (SELECT impuesto_api_id FROM porcentajes_iva t2 WHERE t2.ID=t1.porcentaje_iva_id) as impuesto_api_id,
                (SELECT round(porcentaje,2) FROM porcentajes_iva t2 WHERE t2.ID=t1.porcentaje_iva_id) as porcentaje,
                (SELECT t3.Descripcion FROM $db.inventario_items_general t3 WHERE t3.ID=t1.item_id) as nombre_item,
                (SELECT t3.Referencia FROM $db.inventario_items_general t3 WHERE t3.ID=t1.item_id) as referencia_item 
                FROM $db.documentos_electronicos_items t1 
                WHERE t1.documento_electronico_id='$documento_electronico_id' ";
        $Consulta=$this->Query($sql);
        
        while($datos_consulta=$this->FetchAssoc($Consulta)){
            
            $json.='{ 
                    "unit_measure_id": 642, 
                    "invoiced_quantity": "'.round($datos_consulta["cantidad"],6).'", 
                    "line_extension_amount": "'.round($datos_consulta["subtotal"],2).'", 
                    "free_of_charge_indicator": false,
                    "tax_totals": [{
                        "tax_id": '.$datos_consulta["impuesto_api_id"].',
                        "tax_amount": "'.round($datos_consulta["impuestos"],2).'",  
                        "taxable_amount": "'.round($datos_consulta["subtotal"],2).'",
                        "percent": "'.round($datos_consulta["porcentaje"],2).'" 
                    }],                    
                    "description": "'.str_replace("\n","",$this->limpiar_cadena($datos_consulta["nombre_item"])).'",
                        "code": "'.trim(preg_replace("/[\r\n|\n|\r]+/", "", $this->limpiar_cadena($datos_consulta["referencia_item"]))).'",
                        "type_item_identification_id": 3,
                        "price_amount": "'.round($datos_consulta["valor_unitario"],2).'",
                        "base_quantity": "1.000000"
                    },';
        }
        
        $json=substr($json, 0, -1);
        $json.=']';
        return($json);
    }
        
    public function json_factura_electronica($datos_empresa,$db,$documento_electronico_id) {
        
        $datos_documento_electronico=$this->DevuelveValores("$db.documentos_electronicos", "documento_electronico_id", $documento_electronico_id);
        $datos_resolucion= $this->DevuelveValores("empresa_resoluciones", "ID", $datos_documento_electronico["resolucion_id"]);
        $datos_tercero=$this->DevuelveValores("$db.terceros", "ID", $datos_documento_electronico["tercero_id"]);
        if($datos_documento_electronico["forma_pago"]==1){
            $fecha_vencimiento=$this->SumeDiasFecha($datos_documento_electronico["fecha"],1);
        }else{
            $fecha_vencimiento=$this->SumeDiasFecha($datos_documento_electronico["fecha"],30);
        }
        
        $sync="false";
        if($datos_empresa["metodo_envio"]==1){
            $sync="true";
        }
        $send="false";
        if($datos_empresa["enviar_documento"]==1){
            $send="true";
        }
        $json="{ 
                   ";
        $json.=$this->json_datos_generales_documento($datos_documento_electronico["numero"], $sync, $send, $datos_documento_electronico["fecha"], $datos_documento_electronico["hora"], $datos_documento_electronico["tipo_documento_id"], $datos_empresa["TipoPersona"],  $datos_empresa["Regimen"], $datos_resolucion["resolucion_id_api"]);
        $json.=",";
        
        $json.=$this->json_datos_tercero($datos_tercero["identificacion"], $datos_tercero["tipo_organizacion_id"], $datos_tercero["tipo_documento_id"], $datos_tercero["tipo_regimen_id"], $this->limpiar_cadena($datos_tercero["razon_social"]), $datos_tercero["telefono"], $datos_tercero["direccion"], $datos_tercero["email"], $datos_tercero["municipio_id"]);
        if($datos_documento_electronico["orden_compra"]<>''){
            $json.=",";
            $json.=$this->json_orden_compra($this->limpiar_cadena($datos_documento_electronico["orden_compra"]));
        }
        if($datos_documento_electronico["notas"]<>''){
            $json.=",";
            $json.=$this->json_notas($this->limpiar_cadena($datos_documento_electronico["notas"]));
        }
        
        $json.=",";
        $json.= $this->json_forma_pago($datos_documento_electronico["forma_pago"], $fecha_vencimiento);
        
        $json.=",";
        $totales= $this->json_impuestos_totales($db, $documento_electronico_id);
        $json.=$totales["json"];
        
        $json.=",";
        $json.=$this->json_totales_documento($totales["subtotal"], $totales["base_gravable"], ($totales["subtotal"]+$totales["impuestos"]));
        
        $json.=",";
        $json.=$this->json_items_documento_electronico($db, $documento_electronico_id);
        
        $json.="}";
        return($json);
       
    }
    
    public function json_documento_referencia($numero_documento,$uuid,$fecha) {
        $json=' 
            "billing_reference": {
                "number": "'.$numero_documento.'",
                "uuid": "'.$uuid.'",
                "issue_date": "'.$fecha.'"
                    
                }';
        return($json);
    }
    
    public function json_concepto_correccion($correction_concept_id) {
        $json=' 
            "discrepancy_response": {
                "correction_concept_id": '.$correction_concept_id.'                                    
            }';
        return($json);
    }
    
    public function json_numero_nota_credito_debito($number,$sync,$send,$type_document_id) {
        $json='             
                "number": '.$number.',
                "sync": '.$sync.',
                "send": '.$send.',
                "type_document_id": '.$type_document_id.'
            ';
        return($json);
    }
    
    public function json_nota_credito_debito($datos_empresa,$db,$documento_electronico_id) {
        
        $datos_documento_electronico=$this->DevuelveValores("$db.documentos_electronicos", "documento_electronico_id", $documento_electronico_id);
        $datos_resolucion= $this->DevuelveValores("empresa_resoluciones", "ID", $datos_documento_electronico["resolucion_id"]);
        $datos_tercero=$this->DevuelveValores("$db.terceros", "ID", $datos_documento_electronico["tercero_id"]);
        
        $sync="false";
        if($datos_empresa["metodo_envio"]==1){
            $sync="true";
        }
        $send="false";
        if($datos_empresa["enviar_documento"]==1){
            $send="true";
        }
        $json="{ 
                   ";
        $datos_documento_referencia=$this->DevuelveValores("$db.documentos_electronicos", "documento_electronico_id", $datos_documento_electronico["documento_asociado_id"]);
        if(!isset($datos_documento_referencia["uuid"]) or $datos_documento_referencia["uuid"]==''){
            exit("E1;No existe un documento referencia valido");
        }
        $json.=$this->json_documento_referencia($datos_documento_referencia["prefijo"].$datos_documento_referencia["numero"],$datos_documento_referencia["uuid"],$datos_documento_electronico["fecha"]);
        $json.=",";
        if($datos_documento_electronico["tipo_documento_id"]==5){
            $concepto_correccion_id=1;
        }else{
            $concepto_correccion_id=10;
        }
        $json.=$this->json_concepto_correccion($concepto_correccion_id);
        $json.=",";
        if($datos_documento_electronico["notas"]<>''){
            
            $json.=$this->json_notas($this->limpiar_cadena($datos_documento_electronico["notas"]));
            $json.=",";
        }
        $json.=$this->json_numero_nota_credito_debito($datos_documento_electronico["numero"],$sync,$send,$datos_documento_electronico["tipo_documento_id"]);
        $json.=",";
        $json.=$this->json_datos_tercero($datos_tercero["identificacion"], $datos_tercero["tipo_organizacion_id"], $datos_tercero["tipo_documento_id"], $datos_tercero["tipo_regimen_id"], $this->limpiar_cadena($datos_tercero["razon_social"]), $datos_tercero["telefono"], $datos_tercero["direccion"], $datos_tercero["email"], $datos_tercero["municipio_id"]);
          
        $json.=",";        
        $totales= $this->json_impuestos_totales($db, $documento_electronico_id);        
        $json.=$totales["json"];
        
        $json.=",";        
        $json.=$this->json_totales_documento($totales["subtotal"], $totales["base_gravable"], ($totales["subtotal"]+$totales["impuestos"]),$datos_documento_electronico["tipo_documento_id"]);
        
        $json.=",";        
        $json.=$this->json_items_documento_electronico($db, $documento_electronico_id,$datos_documento_electronico["tipo_documento_id"]);
        
        $json.="}";
        return($json);
       
    }
    public function reporta_documento_electronico($datos_empresa,$db,$documento_electronico_id,$tipo_documento) {
        
        if($tipo_documento==1){
            $json_factura=$this->json_factura_electronica($datos_empresa,$db,$documento_electronico_id);        
            $parametros=$this->DevuelveValores("servidores", "ID", 104); //Ruta para reportar una factura electronica
            
        }
        
        if($tipo_documento==5){
            $json_factura=$this->json_nota_credito_debito($datos_empresa,$db,$documento_electronico_id);
            $parametros=$this->DevuelveValores("servidores", "ID", 105); //Ruta para reportar una factura electronica
        }
        if($tipo_documento==6){
            $json_factura=$this->json_nota_credito_debito($datos_empresa,$db,$documento_electronico_id);
            $parametros=$this->DevuelveValores("servidores", "ID", 108); //Ruta para reportar una factura electronica
        }
        
        $url=$parametros["IP"];
        if($datos_empresa["test_set_dian"]<>''){
            $url.=$datos_empresa["test_set_dian"]."/";
        }
        $TokenTS5=$datos_empresa["TokenAPIFE"];
        $respuesta=$this->callAPI("POST", $url, $TokenTS5, $json_factura);
        
        $arrayRespuesta = json_decode($respuesta,true);
       
        if($datos_empresa["metodo_envio"]==1){
            if(isset($arrayRespuesta["isValid"])){
                if($arrayRespuesta["isValid"]==1){
                    $uuid=$arrayRespuesta["uuid"];
                    $zipBase64Bytes=$arrayRespuesta["zipBase64Bytes"];
                    $pdfBase64Bytes=$arrayRespuesta["pdfBase64Bytes"];
                    $sql="UPDATE $db.documentos_electronicos SET uuid='$uuid', 
                            base64_pdf='$pdfBase64Bytes', 
                            base64_zip='$zipBase64Bytes',
                            is_valid='1' 
                         WHERE documento_electronico_id='$documento_electronico_id'";
                    $this->Query($sql);

                }else{
                    $error=$arrayRespuesta["responseDian"]["Envelope"]["Body"]["SendBillSyncResponse"]["SendBillSyncResult"]["ErrorMessage"]["string"];
                    exit("E1;$error");
                }
            }
        }
        
        if($datos_empresa["metodo_envio"]==2){
            if(isset($arrayRespuesta["uuid"])){
                if($arrayRespuesta["uuid"]<>''){
                    $uuid=$arrayRespuesta["uuid"];
                    $zipBase64Bytes=$arrayRespuesta["zipBase64Bytes"];
                    $pdfBase64Bytes=$arrayRespuesta["pdfBase64Bytes"];
                    $sql="UPDATE $db.documentos_electronicos SET uuid='$uuid', 
                            base64_pdf='$pdfBase64Bytes', 
                            base64_zip='$zipBase64Bytes',
                            is_valid='1' 
                         WHERE documento_electronico_id='$documento_electronico_id'";
                    $this->Query($sql);
                }
            }else{
                exit("E1;El documento no se pudo procesar");
            }
        }
        
        if(isset($arrayRespuesta["errors"])){
            $error="<strong>Error en la estructura de los datos: </strong><ul>";
            foreach ($arrayRespuesta["errors"] as $key => $value) {
                $error.="<li>$key : ".$value[0]." </li>";
            }
            $error.="</ul>";
            exit("E1;$error");
        }
        
    }
    
    //Fin Clases
}