<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../class/tablas.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon = new tablas($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //insertar o editar datos en una tabla
            
            $jsonForm= $_REQUEST["jsonFormulario"];                    
            parse_str($jsonForm,$DatosFormulario);
            
            $edit_id=$obCon->normalizar($_REQUEST["edit_id"]); 
            $db_table_ts6=$obCon->normalizar($_REQUEST["db"]); 
            $tab=$obCon->normalizar($_REQUEST["tab"]); 
            
            foreach ($DatosFormulario as $key => $value) {
                
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
                
                if($tab=='usuarios' and $key=="Password"){
                    $DatosFormulario["Password"]=md5($DatosFormulario["Password"]);
                }
            }
            
            
            
            if($edit_id==""){
                $sql=$obCon->getSQLInsert($tab, $DatosFormulario); 
            }else{
                $sql=$obCon->getSQLUpdate($tab, $DatosFormulario);
                $sql.=" WHERE ID='$edit_id'";
            }
            
            $obCon->QueryExterno($sql, HOST, USER, PW, $db_table_ts6, "");
            
            print("OK;Datos Guardados");
            
        break; //Fin caso 1
              
        case 2:// Consulta si una columna  tiene vinculo o no
            $tab=$obCon->normalizar($_REQUEST["table"]);
            $column=$obCon->normalizar($_REQUEST["column"]);
            
            $sql="SELECT * FROM tablas_campos_asociados WHERE TablaOrigen='$tab' AND CampoTablaOrigen='$column'";
            $datos_asociacion=$obCon->FetchAssoc($obCon->Query($sql));
            if($datos_asociacion["ID"]==''){
                exit("OK;0");
            }else{
                exit("OK;1;".$datos_asociacion["TablaAsociada"].";".$datos_asociacion["CampoAsociado"].";".$datos_asociacion["IDCampoAsociado"].";".$datos_asociacion["dbCampoAsociado"]);
            }
        break;//Fin caso dos    
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>