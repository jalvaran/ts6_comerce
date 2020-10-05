<?php
if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}

class Backups extends ProcesoVenta{
    /**
     * Clase para obtener los nombres de las columnas habilitadas de una tabla
     * @param type $tabla
     * @param type $vector
     */
    public function getColumnasVisibles($Tabla,$vector){
        unset($Columnas);
        $Columnas= $this->ShowColums($Tabla);
        
        $i=0;
        $z=0;
        $ColumnasSeleccionadas["Field"]=[];
        foreach ($Columnas["Field"] as $key => $value) {

            $Consulta=$this->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$Tabla' AND Campo='$value' AND (Visible=0 OR Habilitado=0)");
            $DatosExcluidas=$this->FetchAssoc($Consulta);
            if($DatosExcluidas["ID"]=='' AND $value<>'Updated' AND $value<>'Sync'){
                $DatosNombres=$this->DevuelveValores("configuraciones_nombres_campos", "NombreDB", $value);
                $sql="SELECT * FROM configuracion_campos_asociados WHERE TablaOrigen='$Tabla' AND CampoTablaOrigen='$value'";
                $consulta= $this->Query($sql);
                $DatosCamposAsociados=$this->FetchAssoc($consulta);
                $TablaAsociada=$DatosCamposAsociados["TablaAsociada"];
                $CampoAsociado=$DatosCamposAsociados["CampoAsociado"];
                $IDCampoAsociado=$DatosCamposAsociados["IDCampoAsociado"];
                $ColumnasSeleccionadas["Field"][$i]=$value;
                $ColumnasSeleccionadas["Visualiza"][$i]=$value;
                $ColumnasSeleccionadas["TablaAsociada"][$i]=$TablaAsociada;
                $ColumnasSeleccionadas["CampoAsociado"][$i]=$CampoAsociado;
                $ColumnasSeleccionadas["IDCampoAsociado"][$i]=$IDCampoAsociado;
                if($DatosNombres["Visualiza"]<>''){
                    $ColumnasSeleccionadas["Visualiza"][$i]=$DatosNombres["Visualiza"];
                }

                $ColumnasSeleccionadas["Type"][$i]=$Columnas["Type"][$z];
                $ColumnasSeleccionadas["SubQuery"][$i]="";
                if($IDCampoAsociado<>'' and $TablaAsociada<>'' and $CampoAsociado<>''){
                    $SubQuery="(SELECT $CampoAsociado FROM $TablaAsociada WHERE $TablaAsociada.$IDCampoAsociado=$Tabla.$value LIMIT 1) as $value";
                    $ColumnasSeleccionadas["SubQuery"][$i]=$SubQuery;
                }
                
            }
            $i++;
            $z++;
        }
        
        return($ColumnasSeleccionadas);
            
    }
    /**
     * Obtiene las columnas disponibles en una tabla
     * @param type $Tabla
     * @param type $vector
     * @return type
     */
    public function getColumnasDisponibles($Tabla,$vector){
        unset($Columnas);
        $Columnas= $this->ShowColums($Tabla);
        
        $i=0;
        $z=0;
        $ColumnasSeleccionadas["Field"]=[];
        foreach ($Columnas["Field"] as $key => $value) {

            $Consulta=$this->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$Tabla' AND Campo='$value' AND Habilitado=0");
            $DatosExcluidas=$this->FetchAssoc($Consulta);
            if($DatosExcluidas["ID"]=='' AND $value<>'Updated' AND $value<>'Sync'){
                $DatosNombres=$this->DevuelveValores("configuraciones_nombres_campos", "NombreDB", $value);
                $ColumnasSeleccionadas["Field"][$i]=$value;
                $ColumnasSeleccionadas["Visualiza"][$i]=$value;
                if($DatosNombres["Visualiza"]<>''){
                    $ColumnasSeleccionadas["Visualiza"][$i]=$DatosNombres["Visualiza"];
                }

                $ColumnasSeleccionadas["Type"][$i]=$Columnas["Type"][$z];

            }
            $i++;
            $z++;
        }
        
        return($ColumnasSeleccionadas);
            
    }
    /**
     * Funcion que crea las consultas para dibujar las tablas
     * @param type $Tabla->Tabla a consultar
     * @param string $Condicion->Condicion para la consulta
     * @param type $OrdenColumna->Columna por la que se ordena
     * @param type $AscDesc->Orden ASC o DESC
     * @param type $NumPage->Pagina Actual
     * @param type $limit->Numero que determina el limite de la consulta
     * @return type->Retorna un Array con el QueryParcial, QueryCompleto,TotalRegistros,Orden,Limite
     */
    public function getConsultaTabla($Tabla,$ColumnasSeleccionadas,$Condicion,$OrdenColumna,$AscDesc,$NumPage,$limit,$startpoint) {
         
        $idTabla=$ColumnasSeleccionadas["Field"][0];        
        if($Condicion<>""){
            $Condicion=" WHERE ".$Condicion;
        }
        if($OrdenColumna==''){
            $OrdenColumna=$idTabla;
        }
        $sql="SELECT ";
       
        foreach ($ColumnasSeleccionadas["Field"] as $key => $value) {
            if($ColumnasSeleccionadas["SubQuery"][$key]<>''){
                $value=$ColumnasSeleccionadas["SubQuery"][$key];
            }
            $sql.="$value,";
        }
         
        $sql = substr($sql, 0, -1);
        $Seleccion=$sql;
        $sql = $sql." FROM $Tabla ";
        $QueryParcial=$sql;
        $sqlConteo="SELECT COUNT(*) as TotalRegistros FROM $Tabla $Condicion";
        $consulta= $this->Query($sqlConteo);
        $DatosConteo=$this->FetchAssoc($consulta);
         
         
        $TotalRegistros=$DatosConteo["TotalRegistros"];

        $Orden=" ORDER BY $OrdenColumna $AscDesc ";
        $Limite="LIMIT $startpoint,$limit";
        
        $QueryCompleto=$sql." ".$Condicion." ".$Orden." ".$Limite;
        $DatosConsulta["QueryParcial"]=$QueryParcial;
        $DatosConsulta["QueryCompleto"]=$QueryCompleto;
        $DatosConsulta["TotalRegistros"]=$TotalRegistros;
        $DatosConsulta["Orden"]=$Orden;
        $DatosConsulta["Limite"]=$Limite;
        return($DatosConsulta);
          
    }
    
    /**
     * Fin Clase
     */
}
