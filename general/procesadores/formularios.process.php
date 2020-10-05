<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$fecha=date("Y-m-d");

include_once("../clases/formularios.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $obCon = new formularios($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1://Crear un tercero
            $nit=$obCon->normalizar($_REQUEST['Num_Identificacion']);
            $idCiudad=$obCon->normalizar($_REQUEST['CodigoMunicipio']);
            $DatosCiudad=$obCon->DevuelveValores("cod_municipios_dptos", "ID", $idCiudad);
            $DV=$obCon->CalcularDV($nit);
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            if($DatosCliente["idClientes"]<>''){
                print("E1;El Nit Digitado ya existe");
                exit();
            }
            $Datos["Tipo_Documento"]=$obCon->normalizar($_REQUEST['TipoDocumento']);  
            $Datos["Num_Identificacion"]=$nit;    
            $Datos["DV"]=$DV;  
            $Datos["Primer_Apellido"]=$obCon->normalizar($_REQUEST['PrimerApellido']);    
            $Datos["Segundo_Apellido"]=$obCon->normalizar($_REQUEST['SegundoApellido']);    
            $Datos["Primer_Nombre"]=$obCon->normalizar($_REQUEST['PrimerNombre']);    
            $Datos["Otros_Nombres"]=$obCon->normalizar($_REQUEST['OtrosNombres']);    
            $Datos["RazonSocial"]=$obCon->normalizar($_REQUEST['RazonSocial']);    
            $Datos["Direccion"]=$obCon->normalizar($_REQUEST['Direccion']);    
            $Datos["Cod_Dpto"]=$DatosCiudad["Cod_Dpto"];    
            $Datos["Cod_Mcipio"]=$DatosCiudad["Cod_mcipio"];    
            $Datos["Pais_Domicilio"]=169;   
            $Datos["Telefono"]=$obCon->normalizar($_REQUEST['Telefono']);             
            $Datos["Ciudad"]=$DatosCiudad["Ciudad"];    
            $Datos["Email"]=$obCon->normalizar($_REQUEST['Email']); 
            $Datos["Cupo"]=$obCon->normalizar($_REQUEST['Cupo']);    
            $Datos["CodigoTarjeta"]=$obCon->normalizar($_REQUEST['CodigoTarjeta']); 
            
            $sqlClientes=$obCon->getSQLInsert("clientes", $Datos);
            $sqlProveedores=$obCon->getSQLInsert("proveedores", $Datos);
            $obCon->Query($sqlClientes);
            $obCon->Query($sqlProveedores);
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            
            print("OK;Se ha creado el tercero ".$Datos["RazonSocial"].", con Identificación: ".$nit.";".$DatosCliente["idClientes"].";".$Datos["RazonSocial"]);
            
        break;//FIn caso 1
        
        case 2://Verifica si ya existe un nit
            $nit=$obCon->normalizar($_REQUEST['Num_Identificacion']);
            
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            if($DatosCliente["idClientes"]<>''){
                print("E1;El Nit Digitado ya existe");
                exit();
            }
            print("OK;El cliente no existe aún");
        break;//Fin caso 2
        
        case 3://Verifica si ya existe el codigo de una tarjeta
            $Codigo=$obCon->normalizar($_REQUEST['CodigoTarjeta']);
            
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " CodigoTarjeta='$Codigo'");
            if($DatosCliente["idClientes"]<>''){
                print("E1;El Código de la tarjeta Digitado ya existe");
                exit();
            }
            print("OK;Código disponible");
        break;//Fin caso 3
        
        case 4://Editar un tercero
            $idTercero=$obCon->normalizar($_REQUEST['idTercero']);
            $Tabla=$obCon->normalizar($_REQUEST['Tabla']);
            $nit=$obCon->normalizar($_REQUEST['Num_Identificacion']);
            $idCiudad=$obCon->normalizar($_REQUEST['CodigoMunicipio']);
            $DatosCiudad=$obCon->DevuelveValores("cod_municipios_dptos", "ID", $idCiudad);
            $DV=$obCon->CalcularDV($nit);
            
            $Datos["Tipo_Documento"]=$obCon->normalizar($_REQUEST['TipoDocumento']);  
            $Datos["Num_Identificacion"]=$nit;    
            $Datos["DV"]=$DV;  
            $Datos["Primer_Apellido"]=$obCon->normalizar($_REQUEST['PrimerApellido']);    
            $Datos["Segundo_Apellido"]=$obCon->normalizar($_REQUEST['SegundoApellido']);    
            $Datos["Primer_Nombre"]=$obCon->normalizar($_REQUEST['PrimerNombre']);    
            $Datos["Otros_Nombres"]=$obCon->normalizar($_REQUEST['OtrosNombres']);    
            $Datos["RazonSocial"]=$obCon->normalizar($_REQUEST['RazonSocial']);    
            $Datos["Direccion"]=$obCon->normalizar($_REQUEST['Direccion']);    
            $Datos["Cod_Dpto"]=$DatosCiudad["Cod_Dpto"];    
            $Datos["Cod_Mcipio"]=$DatosCiudad["Cod_mcipio"];    
            $Datos["Pais_Domicilio"]=169;   
            $Datos["Telefono"]=$obCon->normalizar($_REQUEST['Telefono']);             
            $Datos["Ciudad"]=$DatosCiudad["Ciudad"];    
            $Datos["Email"]=$obCon->normalizar($_REQUEST['Email']); 
            $Datos["Cupo"]=$obCon->normalizar($_REQUEST['Cupo']);    
            $Datos["CodigoTarjeta"]=$obCon->normalizar($_REQUEST['CodigoTarjeta']); 
            if(!filter_var($Datos["Email"], FILTER_VALIDATE_EMAIL)){
                exit("E1;El campo Email debe ser del tipo Correo Electronico;Email");
            }
            $sqlUpdate=$obCon->getSQLUpdate($Tabla, $Datos);
            $idTabla="idClientes";
            if($Tabla=="proveedores"){
                $idTabla="idProveedores";
            }
            $Condicion=" WHERE $idTabla='$idTercero'";
            $obCon->Query($sqlUpdate.$Condicion);
            
            $DatosCliente=$obCon->ValorActual("clientes", "idClientes", " Num_Identificacion='$nit'");
            
            print("OK;Se ha editado el tercero ".$Datos["RazonSocial"].", con Identificación: ".$nit.";".$DatosCliente["idClientes"].";".$Datos["RazonSocial"]);
            
        break;//FIn caso 4
        
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>
