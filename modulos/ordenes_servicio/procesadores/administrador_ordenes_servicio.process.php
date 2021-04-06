<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/administrador_ordenes_servicio.class.php");



if( !empty($_REQUEST["Accion"]) ){
    $obCon = new AdminOrdenesServicio($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear o editar un usuario
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $usuario_id=$obCon->normalizar($_REQUEST["usuario_id"]);
            
            $datos_usuario=$obCon->DevuelveValores("usuarios", "ID", $usuario_id);
            
            $Datos["Nombre"]=$obCon->normalizar($_REQUEST["nombre_usuario_os"]);
            $Datos["Apellido"]=$obCon->normalizar($_REQUEST["apellido_usuario_os"]);
            $Datos["nombre_completo"]=$Datos["Nombre"]." ".$Datos["Apellido"];
            $Datos["Identificacion"]=$obCon->normalizar($_REQUEST["identificacion_usuario_os"]);
            $Datos["Habilitado"]=$obCon->normalizar($_REQUEST["cmb_habilitado"]);
            $Datos["Login"]=$obCon->normalizar($_REQUEST["login_usuario_os"]);
            $Datos["TipoUser"]='ordenes_servicio';
            $Datos["Email"]=$obCon->normalizar($_REQUEST["email_usuario_os"]);
            if($_REQUEST["password_usuario_os"]<>$datos_usuario["Password"]){
                $Datos["Password"]=md5($obCon->normalizar($_REQUEST["password_usuario_os"]));
            }
            foreach ($Datos as $key => $value) {
                if($value==''){
                    exit("E1;El campo $key no puede estar vacío;$key");
                }
            }
            
            if(!is_numeric($Datos["Identificacion"]) or $Datos["Identificacion"]<1 ){
                exit("E1;La Identifiación del usuario debe ser un valor numerico mayor a cero;Identificacion");
            }
            $validacion=$obCon->DevuelveValores("usuarios", "Identificacion", $Datos["Identificacion"]);
            if($validacion["ID"]>0 and $usuario_id==""){
                exit("E1;Ya existe un usuario con esta identificación;Identificacion");
            }
            $validacion=$obCon->DevuelveValores("usuarios", "Login", $Datos["Login"]);
            if($validacion["ID"]>0 and $usuario_id==""){
                exit("E1;Ya existe un usuario con este login;Login");
            }
            
            if($usuario_id==''){
                $sql=$obCon->getSQLInsert("usuarios", $Datos);
                $obCon->Query($sql);
                $usuario_id=$obCon->ObtenerMAX("usuarios", "ID", 1, "");
                unset($Datos);
                $Datos["usuario_id_relacion"]=$usuario_id;
                $Datos["empresa_id"]=$empresa_id;
                $sql=$obCon->getSQLInsert("usuarios_rel_empresas", $Datos);
                $obCon->Query($sql);
            }else{
                $sql=$obCon->getSQLUpdate("usuarios", $Datos);
                $sql.=" WHERE ID='$usuario_id'";
                $obCon->Query($sql);
            }
            unset($Datos);
            exit("OK;Registro guardado");
        break; //fin caso 1
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>