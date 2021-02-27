<?php
ob_start();
@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$TipoUser=$_SESSION['tipouser'];

include_once("../modelo/php_tablas.php");  //Clases con el contenido del manejo de las tablas
$obVenta = new ProcesoVenta($idUser);
$VectorPermisos["Page"]=$myPage;
/* 
 * Aqui se revisan los permisos segun el tipo de usuario
 */
$Permiso=$obVenta->VerificaPermisos($VectorPermisos);
//$Permiso=1;
if ($Permiso==0){
  exit("<a href='../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}

?>