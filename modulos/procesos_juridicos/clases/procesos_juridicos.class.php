<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
class ProcesoJuridico extends conexion{
    
    
    public function contar_registros_procesos($db) {
        $sql="SELECT COUNT(ID) as total_items FROM $db.procesos_juridicos_temas";
        $datos_consulta=$this->FetchAssoc($this->Query($sql));
        $totales["total_temas"]=$datos_consulta["total_items"];
        
        $sql="SELECT COUNT(ID) as total_items FROM $db.procesos_juridicos_sub_temas";
        $datos_consulta=$this->FetchAssoc($this->Query($sql));
        $totales["total_sub_temas"]=$datos_consulta["total_items"];
        
        $sql="SELECT COUNT(ID) as total_items FROM $db.procesos_juridicos_tipo";
        $datos_consulta=$this->FetchAssoc($this->Query($sql));
        $totales["total_procesos_tipo"]=$datos_consulta["total_items"];
        
        $sql="SELECT COUNT(ID) as total_items FROM $db.terceros";
        $datos_consulta=$this->FetchAssoc($this->Query($sql));
        $totales["total_terceros"]=$datos_consulta["total_items"];
        
        return($totales);
        
    }
    
    public function RegistreAdjuntoRepositorio($db,$repositorio_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="$db.repositorio_juridico_adjuntos";
        
        $Datos["repositorio_id"]=$repositorio_id;
        
        $Datos["Ruta"]=$destino;    
        $Datos["NombreArchivo"]=$NombreArchivo;    
        $Datos["Extension"]=$Extension;    
        $Datos["Tamano"]=$Tamano; 
        $Datos["idUser"]=$idUser;		
        $Datos["created"]=date("Y-m-d H:i:s");	
        $sql=$this->getSQLInsert($tab, $Datos);
        $this->Query($sql);
    }
    
    public function crear_editar_proceso($db,$datos_repositorio){
        $proceso_id=$datos_repositorio["proceso_id"];
        $Tabla="$db.procesos_juridicos";
        $sql="SELECT ID FROM $Tabla WHERE proceso_id='$proceso_id'";
        $valida=$this->FetchAssoc($this->Query($sql));
        if($valida["ID"]>0){
            $sql=$this->getSQLUpdate($Tabla, $datos_repositorio);
            $sql.=" WHERE proceso_id='$proceso_id'";
        }else{
            $sql=$this->getSQLInsert($Tabla, $datos_repositorio);
        }
        $this->Query($sql);
    }
    
    
    
    /**
     * Fin Clase
     */
}
