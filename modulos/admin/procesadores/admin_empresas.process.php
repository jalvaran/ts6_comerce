<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/admin_empresas.class.php");
include_once("../../../general/class/facturacion_electronica.class.php");
if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new adminEmpresas($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear una empresa
            
            $jsonForm= $_REQUEST["jsonFormulario"];                    
            parse_str($jsonForm,$DatosFormulario);
            
            $edit_id=$obCon->normalizar($_REQUEST["edit_id"]); 
            
            foreach ($DatosFormulario as $key => $value) {
                
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
            
            if(!is_numeric($DatosFormulario["NIT"]) and $DatosFormulario["NIT"]>0){
                exit("E1;El campo NIT debe contener un valor numerico positivo;NIT");
            }
            if(!is_numeric($DatosFormulario["CodigoDaneCiudad"]) and $DatosFormulario["CodigoDaneCiudad"]>0){
                exit("E1;El campo CodigoDaneCiudad debe contener un valor numerico positivo;CodigoDaneCiudad");
            }
            if(!is_numeric($DatosFormulario["TipoDocumento"]) and $DatosFormulario["TipoDocumento"]>0){
                exit("E1;El campo TipoDocumento debe contener un valor numerico positivo;TipoDocumento");
            }
            if($DatosFormulario["Estado"]<>0 and $DatosFormulario["Estado"]<>1){
                exit("E1;El campo Estado debe ser 1 o 0;Estado");
            }
            $DatosFormulario["DigitoVerificacion"]=$obCon->CalcularDV($DatosFormulario["NIT"]);
            
            $NIT=$DatosFormulario["NIT"];
            
            $DatosFormulario["db"]="techno_ts6_comerce_".$NIT;
            if($edit_id==""){
                $sql=$obCon->getSQLInsert("empresapro", $DatosFormulario); 
            }else{
                $sql=$obCon->getSQLUpdate("empresapro", $DatosFormulario);
                $sql.=" WHERE ID='$edit_id'";
            }
            $obCon->Query($sql);
            print("OK;Datos Guardados");
            
        break;//Fin caso 1
        
        case 2://Crear empresa en el API de Factura electronica
            $obFe=new Factura_Electronica($idUser);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $parametros=$obCon->DevuelveValores("servidores", "ID", 100); //Ruta para crear una empresa
            $url=$parametros["IP"].$DatosEmpresa["NIT"]."/".$DatosEmpresa["DigitoVerificacion"];
            $parametros=$obCon->DevuelveValores("configuracion_general", "ID", 4000); //Ruta con el token del api
            $TokenTS5=$parametros["Valor"];
            $data=$obFe->JSONCrearEmpresa($empresa_id);
            $respuesta=$obFe->callAPI("POST", $url, $TokenTS5, $data);
            //$respuesta= json_decode($respuesta);
            $arrayRespuesta = json_decode($respuesta,true);
            if(isset($arrayRespuesta["errors"])){
                foreach ($arrayRespuesta["errors"] as $key => $value) {
                    print("<br><strong>".$value[0]."</strong>");                    
                }
            }else{
                $Datos["empresa_id"]=$empresa_id;
                $Datos["jsonCreacionEmpresa"]=$respuesta;
                $sql=$obCon->getSQLInsert("api_factura_electronica_respuestas_procesos", $Datos);
                $obCon->Query($sql);
                if(isset($arrayRespuesta["token"])){
                    $token=$arrayRespuesta["token"];
                    $obCon->ActualizaRegistro("empresapro", "TokenAPIFE", $token, "ID", $empresa_id);
                }
                print("OK;Empresa Creada Exitósamente");
            }
            
            
            
            
        break;//Fin caso 2    
        
        case 3://Crear software de una empresa en el API de Factura electronica
            $obFe=new Factura_Electronica($idUser);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $software_id=$obCon->normalizar($_REQUEST["software_id"]);
            $software_pin=$obCon->normalizar($_REQUEST["software_pin"]);
            if($software_id==''){
                exit("E1;El campo id del software no puede estar vacío;software_id");
            }
            if($software_pin==''){
                exit("E1;El campo pin del software no puede estar vacío;software_pin");
            }
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $parametros=$obCon->DevuelveValores("servidores", "ID", 101); //Ruta para crear una empresa
            $url=$parametros["IP"];
            
            $TokenTS5=$DatosEmpresa["TokenAPIFE"];
            $data=$obFe->JSONCrearSoftware($software_id,$software_pin);
            $respuesta=$obFe->callAPI("PUT", $url, $TokenTS5, $data);
            //$respuesta= json_decode($respuesta);
            $arrayRespuesta = json_decode($respuesta,true);
            if(isset($arrayRespuesta["errors"])){
                foreach ($arrayRespuesta["errors"] as $key => $value) {
                    print("<br><strong>".$value[0]."</strong>");                    
                }
            }else{
                //$respuesta= str_replace("\\", '/', $respuesta);
                $sql="UPDATE api_factura_electronica_respuestas_procesos SET jsonSoftware='$respuesta' WHERE empresa_id='$empresa_id'";
                $obCon->Query($sql);
                
                if(isset($arrayRespuesta["software"])){
                    $DatosSoftware=$obCon->DevuelveValores("api_fe_software", "empresa_id", $empresa_id);
                    $Datos["empresa_id"]=$empresa_id;
                    $Datos["software_id"]=$software_id;
                    $Datos["software_pin"]=$software_pin;
                    if($DatosSoftware["ID"]==''){
                        $sql=$obCon->getSQLInsert("api_fe_software", $Datos);
                    }else{
                        $sql=$obCon->getSQLUpdate("api_fe_software", $Datos);
                        $sql.=" WHERE empresa_id='$empresa_id'";
                    }
                    
                    $obCon->Query($sql);
                    exit("OK;Software Creado Exitósamente");
                }else{
                    exit("E1;Ocurrió algún error en la creacion del software");
                }
                
            }
            
            
        break;//Fin caso 3
        
        case 4://recibe el certificado digital de una empresa
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]); 
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            
            $Extension="";
            if(!empty($_FILES['certificado_empresa']['name'])){
                
                $info = new SplFileInfo($_FILES['certificado_empresa']['name']);
                $Extension=($info->getExtension()); 
                
                $Tamano=filesize($_FILES['certificado_empresa']['tmp_name']);
                $DatosConfiguracion=$obCon->DevuelveValores("configuracion_general", "ID", 3000);
                
                $carpeta=$DatosConfiguracion["Valor"];
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                
                $carpeta.=$empresa_id."/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                $carpeta.="certificado_digital/";
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777);
                }
                                
                opendir($carpeta);
                
                $destino=$carpeta."Certificado.p12";
                
                move_uploaded_file($_FILES['certificado_empresa']['tmp_name'],$destino);  
                $im = file_get_contents($destino);               
                $CertificadoBase64=base64_encode($im);
                $DatosCertificado=$obCon->DevuelveValores("api_fe_certificado", "empresa_id", $empresa_id);
                
                $Datos["empresa_id"]=$empresa_id;
                $Datos["certificadoBase64"]=$CertificadoBase64;
                if($DatosCertificado["ID"]==''){
                    $sql=$obCon->getSQLInsert("api_fe_certificado", $Datos);
                }else{
                    $sql=$obCon->getSQLUpdate("api_fe_certificado", $Datos);
                    $sql.=" WHERE empresa_id='$empresa_id'";
                }
                $obCon->Query($sql);
            }else{
                exit("E1;No se recibió El Certificado");
            }
            print("OK;Certificado subido");
        break;//Fin caso 4  
        
        case 5://Crear el certificado digital de una empresa en el API de Factura electronica
            $obFe=new Factura_Electronica($idUser);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $DatosCertificado=$obCon->DevuelveValores("api_fe_certificado", "empresa_id", $empresa_id);
            $CertificadoBase64=$DatosCertificado["certificadoBase64"];
            if($CertificadoBase64==""){
                exit("E1;No se ha subido un certificado digital para esta empresa");
            }
            $clave_certificado=$obCon->normalizar($_REQUEST["clave_certificado"]);
            if($clave_certificado==''){
                exit("E1;El campo Clave del Certificado no puede estar vacío;clave_certificado");
            }
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $parametros=$obCon->DevuelveValores("servidores", "ID", 102); //Ruta para crear una empresa
            $url=$parametros["IP"];            
            $TokenTS5=$DatosEmpresa["TokenAPIFE"];
            $data=$obFe->JSONCrearCertificado($CertificadoBase64,$clave_certificado);
            $respuesta=$obFe->callAPI("PUT", $url, $TokenTS5, $data);
            //$respuesta= json_decode($respuesta);
            $arrayRespuesta = json_decode($respuesta,true);
            if(isset($arrayRespuesta["errors"])){
                foreach ($arrayRespuesta["errors"] as $key => $value) {
                    print("<br><strong>".$value[0]."</strong>");                    
                }
            }else{
                //$respuesta= str_replace("\\", '/', $respuesta);
                $sql="UPDATE api_factura_electronica_respuestas_procesos SET jsonCertificado='$respuesta' WHERE empresa_id='$empresa_id'";
                $obCon->Query($sql);
                
                if(isset($arrayRespuesta["certificado"])){
                    
                    $obCon->ActualizaRegistro("api_fe_certificado", "Clave", $clave_certificado, "empresa_id", $empresa_id);
                    exit("OK;Certificado Digital Creado Exitósamente");
                }else{
                    exit("E1;Ocurrió algún error en la creacion del certificado");
                }
                
            }
            
            
        break;//Fin caso 5
        
        case 6://Crear una resolucion de facturacion electrónica
            $obFe=new Factura_Electronica($idUser);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $resolucion_prefijo=$obCon->normalizar($_REQUEST["resolucion_prefijo"]);
            $resolucion_numero=$obCon->normalizar($_REQUEST["resolucion_numero"]);
            $cmb_tipo_documento=$obCon->normalizar($_REQUEST["cmb_tipo_documento"]);
            $resolucion_fecha=$obCon->normalizar($_REQUEST["resolucion_fecha"]);
            $resolucion_llave=$obCon->normalizar($_REQUEST["resolucion_llave"]);
            $resolucion_rango_desde=$obCon->normalizar($_REQUEST["resolucion_rango_desde"]);
            $resolucion_rango_hasta=$obCon->normalizar($_REQUEST["resolucion_rango_hasta"]);
            $resolucion_fecha_desde=$obCon->normalizar($_REQUEST["resolucion_fecha_desde"]);
            $resolucion_fecha_hasta=$obCon->normalizar($_REQUEST["resolucion_fecha_hasta"]);
            $cmb_tipo_accion=$obCon->normalizar($_REQUEST["cmb_tipo_accion"]);
            $resolucion_api_id=$obCon->normalizar($_REQUEST["resolucion_api_id"]);
            
            if($empresa_id==""){
                exit("E1;No se recibió el id de la empresa");
            }
            if($resolucion_prefijo==""){
                exit("E1;No se recibió el prefijo de la resolución;resolucion_prefijo");
            }
            if(!is_numeric($resolucion_rango_desde) or $resolucion_rango_desde<=0){
                exit("E1;El rango inicial debe ser un numero mayor a cero;resolucion_rango_desde");
            }
            if(!is_numeric($resolucion_rango_hasta) or $resolucion_rango_hasta<=0){
                exit("E1;El rango final debe ser un numero mayor a cero;resolucion_rango_hasta");
            }
            if($resolucion_rango_desde>=$resolucion_rango_hasta){
                exit("E1;El Rango hasta debe ser un numero mayor al rango desde;resolucion_rango_hasta");
            }
            if($resolucion_numero=="" and $cmb_tipo_documento==1){
                exit("E1;No se recibió el número de la resolución;resolucion_numero");
            }
            if($resolucion_llave=="" and $cmb_tipo_documento==1){
                exit("E1;No se recibió la llave técnica de la resolución;resolucion_llave");
            }
            if($resolucion_fecha=="" and $cmb_tipo_documento==1){
                exit("E1;No se recibió la fecha de la resolución;resolucion_fecha");
            }
            if($resolucion_fecha_desde=="" and $cmb_tipo_documento==1){
                exit("E1;No se recibió la fecha desde de la resolución;resolucion_fecha_desde");
            }
            if($resolucion_fecha_hasta=="" and $cmb_tipo_documento==1){
                exit("E1;No se recibió la fecha hasta de la resolución;resolucion_fecha_hasta");
            }
            
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $TokenTS5=$DatosEmpresa["TokenAPIFE"];
            
            if($cmb_tipo_accion==1){
                $parametros=$obCon->DevuelveValores("servidores", "ID", 103); //Ruta para crear una resolucion indivual
                $url=$parametros["IP"];
                $metodo_envio="PUT";
                if($cmb_tipo_documento==1){
                    $data=$obFe->JSONCrearResolucionFacturacion($cmb_tipo_documento, $resolucion_prefijo, $resolucion_rango_desde, $resolucion_rango_hasta, $resolucion_numero, $resolucion_fecha, $resolucion_llave, $resolucion_fecha_desde, $resolucion_fecha_hasta);
                }    
                if($cmb_tipo_documento==5 or $cmb_tipo_documento==6){
                    $data=$obFe->JSONCrearResolucionNotas($cmb_tipo_documento, $resolucion_prefijo, $resolucion_rango_desde, $resolucion_rango_hasta);
                }
                //print($data);
                $respuesta=$obFe->callAPI($metodo_envio, $url, $TokenTS5, $data);                
                $arrayRespuesta = json_decode($respuesta,true);
                if(isset($arrayRespuesta["errors"])){
                    foreach ($arrayRespuesta["errors"] as $key => $value) {
                        print("<br><strong>".$value[0]."</strong>");                    
                    }
                }else{
                    
                    $sql="UPDATE api_factura_electronica_respuestas_procesos SET jsonLastResolution='$respuesta' WHERE empresa_id='$empresa_id'";
                    $obCon->Query($sql);

                    if(isset($arrayRespuesta["resolution"])){
                        $resolucion_id_api=$arrayRespuesta["resolution"]["id"];
                        $datos_resolucion=$obCon->DevuelveValores("empresa_resoluciones", "resolucion_id_api", $resolucion_id_api);
                        $condition="";
                        if($datos_resolucion["ID"]<>''){
                            $condition=" WHERE resolucion_id_api='$resolucion_id_api'";
                        }
                        if($cmb_tipo_documento==1){
                            $obFe->crear_actualizar_resolucion_db($empresa_id, $cmb_tipo_documento, $resolucion_prefijo, $resolucion_rango_desde, $resolucion_rango_hasta, $resolucion_numero, $resolucion_fecha, $resolucion_llave, $resolucion_fecha_desde, $resolucion_fecha_hasta, $resolucion_id_api, $condition);
                        }
                        if($cmb_tipo_documento==5 or $cmb_tipo_documento==6){
                            $obFe->crear_actualizar_resolucion_db($empresa_id, $cmb_tipo_documento, $resolucion_prefijo, $resolucion_rango_desde, $resolucion_rango_hasta, "", "", "", "", "", $resolucion_id_api, $condition);
                        }
                        exit("OK;Resolución creada satisfactoriamente");
                    }else{
                        exit("E1;Ocurrió algún error en la creacion de la resolución");
                    }

                }
            }
            
            if($cmb_tipo_accion==2){
                $parametros=$obCon->DevuelveValores("servidores", "ID", 107); //Ruta para crear una resolucion multiple
                $url=$parametros["IP"];                
                $metodo_envio="POST";
            }
            if($cmb_tipo_accion==3){
                $parametros=$obCon->DevuelveValores("servidores", "ID", 107); //Ruta para crear una resolucion multiple
                $url=$parametros["IP"];                
                $metodo_envio="PUT";
                if($resolucion_api_id==''){
                    exit("E1;Para actualizar una resolucion debe indicar el ID de la resolucion en el API;resolucion_api_id");
                }
                $url.="$resolucion_api_id";
            }
            
            
            
            
        break;//Fin caso 6
        
        case 7://Obtener las resoluciones de facturacion creadas en el software
            $obFe=new Factura_Electronica($idUser);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            
            if($empresa_id==""){
                exit("E1;No se recibió el id de la empresa");
            }
                        
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $TokenTS5=$DatosEmpresa["TokenAPIFE"];
            $parametros=$obCon->DevuelveValores("servidores", "ID", 107); //Ruta para crear una resolucion multiple
            $url=$parametros["IP"];                
            $metodo_envio="GET";
            $respuesta=$obFe->callAPI($metodo_envio, $url, $TokenTS5, "");                
            $arrayRespuesta = json_decode($respuesta,true);
            if(is_array($arrayRespuesta)){
                foreach ($arrayRespuesta as $key => $value) {
                    if(is_array($value)){
                        print("<ul>");
                        foreach ($value as $key2 => $value2) {
                            print("<li><strong>$key2: </strong> ".$value2."</li>");
                        }
                        print("</ul>");
                    }else{
                        print("<br><strong>$key: </strong> ".$value);
                    }

                }
            }else{
                print("No se obtuvo respuesta del API");
            }
        break;//Fin caso 7
        
        case 8://Obtener las resoluciones de facturacion creadas en el software
            $obFe=new Factura_Electronica($idUser);            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            
            if($empresa_id==""){
                exit("E1;No se recibió el id de la empresa");
            }
                        
            $DatosEmpresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $TokenTS5=$DatosEmpresa["TokenAPIFE"];
            $datos_software=$obCon->DevuelveValores("api_fe_software", "empresa_id", $empresa_id);
            
            $parametros=$obCon->DevuelveValores("servidores", "ID", 109); //Ruta para crear una resolucion multiple
            $url=$parametros["IP"].$DatosEmpresa["NIT"]."/".$DatosEmpresa["NIT"]."/".$datos_software["software_id"];               
            $metodo_envio="POST";
            $respuesta=$obFe->callAPI($metodo_envio, $url, $TokenTS5, "");                
            $arrayRespuesta = json_decode($respuesta,true);
            if(is_array($arrayRespuesta)){
                foreach ($arrayRespuesta as $key => $value) {
                    if(is_array($value)){
                        print("<ul>");
                        foreach ($value as $key2 => $value2) {
                            if(is_array($value2)){
                                print("<ul>");
                                foreach ($value2 as $key => $value3) {
                                    
                                    if(is_array($value3)){
                                        print("<ul>");
                                        foreach ($value3 as $key => $value4) {
                                            if(is_array($value4)){
                                                print("<ul>");
                                                foreach ($value4 as $key => $value5) {
                                                    if(is_array($value5)){
                                                        print("<ul>");
                                                        foreach ($value5 as $key => $value6) {
                                                            if(is_array($value6)){
                                                                print("<ul>");
                                                                foreach ($value6 as $key => $value7) {
                                                                    if(is_array($value7)){
                                                                        print("<ul>");
                                                                        foreach ($value7 as $key => $value8) {
                                                                            if(is_array($value8)){
                                                                                print("<ul>");
                                                                                foreach ($value8 as $key => $value9) {
                                                                                    if(is_array($value9)){
                                                                                        print("<ul>");
                                                                                        foreach ($value9 as $key => $value10) {
                                                                                            if(is_array($value10)){
                                                                                                print("<ul>");
                                                                                                foreach ($value10 as $key => $value11) {
                                                                                                    print("<li><strong>$key: </strong> ".$value11."</li>");
                                                                                                }
                                                                                                print("</ul>");
                                                                                            }else{
                                                                                                print("<li><strong>$key: </strong> ".$value10."</li>");
                                                                                            }
                                                                                        }
                                                                                        print("</ul>");
                                                                                    }else{
                                                                                        print("<li><strong>$key: </strong> ".$value9."</li>");
                                                                                    }
                                                                                    
                                                                                    
                                                                                }
                                                                                print("</ul>");
                                                                            }else{
                                                                                print("<li><strong>$key: </strong> ".$value8."</li>");
                                                                            }
                                                                            
                                                                        }
                                                                        print("</ul>");
                                                                    }else{
                                                                        print("<li><strong>$key: </strong> ".$value7."</li>");
                                                                    }
                                                                    
                                                                }
                                                                print("</ul>");
                                                            }else{
                                                                print("<li><strong>$key: </strong> ".$value6."</li>");
                                                            }
                                                            
                                                        }
                                                        print("</ul>");
                                                    }else{
                                                        print("<li><strong>$key: </strong> ".$value5."</li>");
                                                    }
                                                    
                                                }
                                                print("</ul>");
                                            }else{
                                                print("<li><strong>$key: </strong> ".$value4."</li>");
                                            }
                                            
                                        }
                                        print("</ul>");
                                    }else{
                                        print("<li><strong>$key: </strong> ".$value3."</li>");
                                    }
                                    
                                }
                                print("</ul>");
                            }else{
                                print("<li><strong>$key2: </strong> ".$value2."</li>");
                            }
                            
                        }
                        print("</ul>");
                    }else{
                        print("<br><strong>$key: </strong> ".$value);
                    }

                }
            }else{
                print("No se obtuvo respuesta del API");
            }
        break;//Fin caso 8
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>