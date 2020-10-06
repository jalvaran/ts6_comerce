<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
function validateDate($date, $format = 'Y-m-d H:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

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
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>