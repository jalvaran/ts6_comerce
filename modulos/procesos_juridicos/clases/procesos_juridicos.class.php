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
    
    public function RegistreAdjuntoProcesoJuridico($db,$acto_id, $destino, $Tamano, $NombreArchivo, $Extension, $idUser) {
        
        $tab="$db.procesos_juridicos_acto_admin_adjuntos";
        
        $Datos["acto_id"]=$acto_id;
        
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
    
    public function crear_editar_acto_administrativo_proceso($db,$datos_repositorio){
        $acto_id=$datos_repositorio["acto_id"];
        $Tabla="$db.procesos_juridicos_actos_administrativos";
        $sql="SELECT ID FROM $Tabla WHERE acto_id='$acto_id'";
        $valida=$this->FetchAssoc($this->Query($sql));
        if($valida["ID"]>0){
            $sql=$this->getSQLUpdate($Tabla, $datos_repositorio);
            $sql.=" WHERE acto_id='$acto_id'";
        }else{
            $sql=$this->getSQLInsert($Tabla, $datos_repositorio);
        }
        $this->Query($sql);
    }
    
    function sume_dias_fecha($fecha,$dias){
        $fecha_recibida = date($fecha);
        //sumo 1 día
        return(date("Y-m-d",strtotime($fecha_recibida."+ $dias days"))); 
    }
    
    
    
    /**
     * Fin Clase
     */
}
