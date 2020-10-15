<?php
@session_start();

if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/migrations.class.php");
include_once("../../../general/class/facturacion_electronica.class.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new Migrations($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Verificamos los archivos que hay para migrar
            
            
            
            $obCon->VaciarTabla("temp_migrations");
            $Pendientes=0;
            $directorio = opendir("../../../migrations/"); //ruta actual
            while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
            {
               if(substr($archivo,0,-3)){
                  
                      $sql="SELECT * FROM empresapro";
                      $Consulta=$obCon->Query($sql);
                      while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                          $db=$datos_consulta["db"];
                          $sql="SELECT * FROM `migrations` WHERE `migration`='$archivo' AND `database`='$db';";
                          $DatosMigration=$obCon->FetchAssoc($obCon->Query($sql));
                          if($DatosMigration["ID"]==''){
                              $Datos["migration"]=$archivo;
                              $Datos["database"]=$db;
                              $sql=$obCon->getSQLInsert("temp_migrations", $Datos);
                              $obCon->Query($sql);
                              $Pendientes=$Pendientes+1;
                          }
                      }
                  
                   
               }
                
                                
            }
            $sql="UPDATE temp_migrations t1 INNER JOIN migrations t2 ON t1.migration=t2.migration SET t1.Estado=1 WHERE t1.database=t2.database;";
            $obCon->Query($sql);
            if($Pendientes==0){
                print("E1;No se encontraron migraciones pendientes"); 
            }else{
                print("OK;Migraciones pendientes obtenidas"); 
            }
            
                       
            
        break; //fin caso 1
        
        case 2: //Ejecutar las migraciones en que estén pendientes
            
            $MigracionActual=$obCon->normalizar($_REQUEST["MigracionActual"]);
            $Migraciones=$obCon->Count("temp_migrations", "ID", "WHERE Estado=0");
            
            $directorio = "../../../migrations/";
            $Errores="";
            
                
                    $sql="SELECT *
                        FROM temp_migrations   
                       WHERE Estado=0";
                    
                    $Consulta2=$obCon->Query($sql);
                     
                    while($DatosMigration=$obCon->FetchAssoc($Consulta2)){
                        $db=$DatosMigration["database"];
                        
                        $RegistreMigracion=1;
                        //print($DatosMigration["ID"]);
                        $sql="CREATE DATABASE IF NOT EXISTS $db CHARSET=utf8 COLLATE=utf8_spanish_ci; ";
                       
                        $obCon->Query($sql);
                        
                        $fileSQL = file_get_contents($directorio.$DatosMigration["migration"]);
                        $Consultas= explode(";", $fileSQL);
                        foreach ($Consultas as $key => $query) {
                            if($query==''){
                                continue;
                            }
                            $Resultados=$obCon->Query2($query, HOST, USER, PW, $db, "");
                            //print($query);
                            if(isset($Resultados["Error"])){
                                //$NumErrores++;
                                $Error=$Resultados["Error"];
                                $Errores.="<br><strong style='color:red'>Error: $Error, en la Base de datos: $db,  al ejecutar:</strong> $query<br>";
                            
                               $RegistreMigracion=0; 
                            }
                            
                        }
                        if($RegistreMigracion==1){
                            $Datos["migration"]=$DatosMigration["migration"];
                            $Datos["database"]=$DatosMigration["database"];
                            $Datos["created"]=date("Y-m-d");
                            $sql=$obCon->getSQLInsert("migrations", $Datos);
                            $obCon->Query($sql);
                        }
                              
                                                              
                    }
                    
                
            
            if($Errores==''){
                print("OK;Migraciones Realizadas");
            }else{
                print("E1;<pre>$Errores</pre>");
            }
            
        break;
                
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>