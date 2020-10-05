<?php


include_once("../../../modelo/php_mysql_i.php");

if( !empty($_REQUEST["Accion"]) ){
    
    $obCon=new db_conexion(1);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //crear el usuario
            
            $sql="CREATE USER 'techno'@'localhost' IDENTIFIED BY 'techno';";
            $obCon->Query($sql);
            
            $sql="GRANT ALL PRIVILEGES ON * . * TO 'techno'@'localhost';";
            $obCon->Query($sql);
            
            $sql="FLUSH PRIVILEGES;";
            $obCon->Query($sql);
            
            $sql="select User from mysql.user ";
            $Consulta=$obCon->Query($sql);
            
            while($dataUser=$obCon->FetchAssoc($Consulta)){
                print_r($dataUser);
                print("<br>");
            }
        break; // fin caso 1 
        
        
        case 2: //Cambiar la contraseña del root
            $sql="update mysql.user set password=PASSWORD('pirlo1985') where user='root';";
            $obCon->Query($sql);
            $sql="FLUSH PRIVILEGES;";
            $obCon->Query($sql);
            
            $sql="select User,password from mysql.user ";
            $Consulta=$obCon->Query($sql);
            
            while($dataUser=$obCon->FetchAssoc($Consulta)){
                print_r($dataUser);
                print("<br>");
            }
        break; // fin caso 2    
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>