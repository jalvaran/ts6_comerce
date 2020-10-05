<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/facturacion_electronica.class.php");

if( !empty($_REQUEST["idAccion"]) ){
    
function ContabilizarFacturas(){
    
    $idUser=$_SESSION['idUser'];
    $obCon = new Factura_Electronica($idUser);
    
    $Consulta=$obCon->ConsultarTabla("facturas_kardex", "WHERE Kardex='NO'");  //Sin el usuario porque será para todas las cajas
    $Mensaje="";
    while ($DatosFactura=$obCon->FetchArray($Consulta)){
        $idFactura=$DatosFactura["idFacturas"];
        $sql="SELECT Num_Documento_Interno FROM librodiario WHERE Num_Documento_Interno='$idFactura'";
        $Verificacion=$obCon->Query($sql);
        $Verificacion=$obCon->FetchAssoc($Verificacion);
        if($Verificacion["Num_Documento_Interno"]==''){
            $Datos["ID"]=$DatosFactura["idFacturas"];
            $Datos["CuentaDestino"]=$DatosFactura["CuentaDestino"];
            $obCon->InsertarFacturaLibroDiario($Datos);
            $obCon->DescargueFacturaInventarios($DatosFactura["idFacturas"],"");
            $Mensaje.="Factura $DatosFactura[idFacturas] Contabilizada<br>";
            //print("Factura $DatosFactura[idFacturas] Contabilizada<br>");
            //print("Factura $DatosFactura[idFacturas] descargada de inventarios<br>");
            $obCon->BorraReg("facturas_kardex", "idFacturas", $DatosFactura["idFacturas"]);
        }
        
    }
    print("OK;$Mensaje");
     
}
    //$obCon = new Backups($idUser);
    
    switch ($_REQUEST["idAccion"]) {
        
        case 1: //Contabilizacion de facturas
            
            if($TipoKardex=="Servidor"){
                register_shutdown_function('ContabilizarFacturas');
            }else{
                print("OK;Contabilizacion Automatica Deshabilitada");
            }
            
        break; //Fin caso 1
        
       
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>