<?php
@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/administrador.class.php");

$obCon=new conexion($idUser);

if(isset($_REQUEST['Login'])){
    $key=$obCon->normalizar($_REQUEST['Login']);
    $sql="SELECT Login FROM usuarios WHERE Login LIKE '$key'";
    $consulta=$obCon->Query($sql);
    $Datos=$obCon->FetchArray($consulta);
    if($Datos["Login"]<>''){
        print("Error");
    }else{
        print("OK");
    }
}

if(isset($_REQUEST['Tipo'])){
    $key=$obCon->normalizar($_REQUEST['Tipo']);
    $sql="SELECT Tipo FROM usuarios_tipo WHERE Tipo LIKE '$key'";
    $consulta=$obCon->Query($sql);
    $Datos=$obCon->FetchArray($consulta);
    if($Datos["Tipo"]<>''){
        print("Error");
    }else{
        print("OK");
    }
}
