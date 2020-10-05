<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
class Backups extends conexion{
    
    
    public function backup_registra_database($backups_id,$nombre_base_datos) {
        $Tabla="backups_bases_datos";
        $created=date("Y-m-d H:i:s");        
        $Datos["backups_id"]=$backups_id;
        $Datos["nombre_base_datos"]=$nombre_base_datos;
        $Datos["estado"]=0;        
        
        $Datos["fecha_registro"]=$created;
        
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->Query($sql);
    }
    
    public function backup_registra_tablas_database($database_id,$nombre_tabla,$Table_type) {
        $Tabla="backups_bases_datos_tablas";
        $created=date("Y-m-d H:i:s");        
        $Datos["base_datos_id"]=$database_id;
        $Datos["nombre_tabla"]=$nombre_tabla;
        $Datos["Table_type"]=$Table_type;
        $Datos["estado"]=0;        
        
        $Datos["inicia"]=$created;
        
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->Query($sql);
    }
    
    
    /**
     * Fin Clase
     */
}
