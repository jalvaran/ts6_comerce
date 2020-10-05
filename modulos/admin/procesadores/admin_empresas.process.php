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

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new adminEmpresas($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear una empresa
            
            //$jsonForm= $_REQUEST["jsonFormulario"];                    
            //parse_str($jsonForm,$DatosFormulario);
            $DatosFormulario["RazonSocial"]=$obCon->normalizar($_REQUEST["RazonSocial"]);
            $DatosFormulario["NIT"]=$obCon->normalizar($_REQUEST["NIT"]);
            $DatosFormulario["Direccion"]=$obCon->normalizar($_REQUEST["Direccion"]);
            $DatosFormulario["Telefono"]=$obCon->normalizar($_REQUEST["Telefono"]);
            $DatosFormulario["Celular"]=$obCon->normalizar($_REQUEST["Celular"]);
            $DatosFormulario["Ciudad"]=$obCon->normalizar($_REQUEST["Ciudad"]);
            $DatosFormulario["CodigoDaneCiudad"]=$obCon->normalizar($_REQUEST["CodigoDaneCiudad"]);
            $DatosFormulario["Regimen"]=$obCon->normalizar($_REQUEST["Regimen"]);
            $DatosFormulario["TipoPersona"]=$obCon->normalizar($_REQUEST["TipoPersona"]);
            $DatosFormulario["TipoDocumento"]=$obCon->normalizar($_REQUEST["TipoDocumento"]);
            $DatosFormulario["Email"]=$obCon->normalizar($_REQUEST["Email"]);
            $DatosFormulario["WEB"]=$obCon->normalizar($_REQUEST["WEB"]);
            $edit_id=$obCon->normalizar($_REQUEST["edit_id"]); 
            
            foreach ($DatosFormulario as $key => $value) {
                
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
            
            if(!is_numeric($DatosFormulario["NIT"]) and $DatosFormulario["NIT"]>0){
                exit("E1;El campo NIT debe contener un valor numerico positivo;NIT");
            }
            if(!is_numeric($DatosFormulario["CodigoDaneCiudad"]) and $DatosFormulario["NIT"]>0){
                exit("E1;El campo CodigoDaneCiudad debe contener un valor numerico positivo;CodigoDaneCiudad");
            }
            if(!is_numeric($DatosFormulario["TipoDocumento"]) and $DatosFormulario["NIT"]>0){
                exit("E1;El campo TipoDocumento debe contener un valor numerico positivo;TipoDocumento");
            }
            $DatosFormulario["DigitoVerificacion"]=$obCon->CalcularDV($DatosFormulario["NIT"]);
            $id=$obCon->ObtenerMAX("empresapro", "ID", 1, "")+1;
            
            $DatosFormulario["db"]="techno_ts6_pro_".$id;
            if($edit_id==""){
                $sql=$obCon->getSQLInsert("empresapro", $DatosFormulario); 
            }else{
                $sql=$obCon->getSQLUpdate("empresapro", $DatosFormulario);
                $sql.=" WHERE ID='$edit_id'";
            }
            $obCon->Query($sql);
            print("OK;Datos Guardados");
            
        break;//Fin caso 1
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>