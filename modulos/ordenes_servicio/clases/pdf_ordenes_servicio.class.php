<?php

if(file_exists("../../../general/class/ClasesPDFDocumentos.class.php")){
    include_once("../../../general/class/ClasesPDFDocumentos.class.php");
}

class PDF_OrdenServicio extends Documento{
    public $color_titulos_tablas="#c9cffc";
    public $color_fuente_titulos_tablas="black";
    public $color_linea1_tablas="#f2f2f2"; 
    public $color_linea2_tablas="white";   
    
    public function pdf_orden_servicio($db,$empresa_id,$orden_servicio_id,$datos_items_orden ) {
        $obCon=new conexion($_SESSION["idUser"]);
        $datos_formato_calidad=$obCon->DevuelveValores("$db.formatos_calidad", "ID", 100);
        $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
        $this->PDF_Ini("Orden Servicio", 8, "",1,"../../../");
        $this->PDF_Encabezado($datos_orden["created"],$empresa_id, 100, "",$datos_formato_calidad["Nombre"]." ".$datos_orden["ID"],"","../../../");
        
        $html=$this->get_general_data($db, $datos_orden);
        $this->PDF_Write($html);
        
        $html=$this->get_suministros_entregados($db, $datos_orden);
        $this->PDF_Write($html);
        
        $html=$this->get_suministros_ejecutados($db, $datos_orden);
        $this->PDF_Write($html);
        
        $html=$this->get_suministros_sobrantes($db, $datos_orden,$datos_items_orden);
        $this->PDF_Write($html);
        
        $html=$this->firmas_orden_servicio($db, $datos_orden);
        $this->PDF_Write($html);
        
        $this->PDF_Output("orden_servicio_".$datos_orden["ID"]);
    }
    
    public function pdf_orden_servicio_entrega_suministros($db,$empresa_id,$orden_servicio_id,$datos_items_orden ) {
        $obCon=new conexion($_SESSION["idUser"]);
        $datos_formato_calidad=$obCon->DevuelveValores("$db.formatos_calidad", "ID", 101);
        $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
        $this->PDF_Ini("Orden Servicio", 8, "",1,"../../../");
        $this->PDF_Encabezado($datos_orden["created"],$empresa_id, 101, "",$datos_formato_calidad["Nombre"]." ".$datos_orden["ID"],"","../../../");
        
        $html=$this->get_general_data($db, $datos_orden);
        $this->PDF_Write($html);
        
        $html=$this->get_suministros_entregados($db, $datos_orden);
        $this->PDF_Write($html);
        
        $html=$this->firmas_orden_servicio($db, $datos_orden);
        $this->PDF_Write($html);
        
        $this->PDF_Output("orden_servicio_".$datos_orden["ID"]);
    }
    
    public function pdf_orden_servicio_devolucion_suministros($db,$empresa_id,$orden_servicio_id,$datos_items_orden ) {
        $obCon=new conexion($_SESSION["idUser"]);
        $datos_formato_calidad=$obCon->DevuelveValores("$db.formatos_calidad", "ID", 102);
        $datos_orden=$obCon->DevuelveValores("$db.vista_ordenes_servicio", "orden_servicio_id", $orden_servicio_id);
        $this->PDF_Ini("Orden Servicio", 8, "",1,"../../../");
        $this->PDF_Encabezado($datos_orden["created"],$empresa_id, 102, "",$datos_formato_calidad["Nombre"]." ".$datos_orden["ID"],"","../../../");
        
        $html=$this->get_general_data($db, $datos_orden);
        $this->PDF_Write($html);
        
        $html=$this->get_suministros_sobrantes($db, $datos_orden,$datos_items_orden);
        $this->PDF_Write($html);
        
        $html=$this->firmas_orden_servicio($db, $datos_orden);
        $this->PDF_Write($html);
        
        $this->PDF_Output("orden_servicio_".$datos_orden["ID"]);
    }
    
    public function firmas_orden_servicio() {
        
   
        $tbl='<table cellspacing="0" cellpadding="2" border="1">';
            $tbl.='<tr>';
                $tbl.='<th>';
                    $tbl.='<strong>Entrega:</strong><br>';
                    $tbl.='<br><br><br>';
                    
                $tbl.='</th>';
                $tbl.='<th>';
                    $tbl.='<strong>Recibe:</strong><br>';
                    $tbl.='<br><br><br>';
                    
                $tbl.='</th>';
            $tbl.='</tr>';  
        $tbl.='</table>'; 
        return($tbl);
    }
    
    
    public function get_suministros_sobrantes($db,$datos_orden,$datos_items_orden) {
        
        $obCon=new conexion(1);
        $orden_servicio_id=$datos_orden["orden_servicio_id"];
        
        $Back=$this->color_titulos_tablas;
        $html ='<table class="table table-striped table-bordered table-responsive" cellspacing="1" cellpadding="2" border="0"> ';
        $html .=' 
                
                    <tr>
                        <td align="center" colspan="3"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>INSUMOS Y/O MATERIALES SOBRANTES PARA DEVOLUCIÓN:</strong></td>
                    </tr>   
                    <tr>
                        
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>REFERENCIA</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>INSUMO</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>CANTIDAD</strong></td>
                        
                    </tr>
                    
                 
                ';
        $b=1;
        foreach ($datos_items_orden as $insumo_id => $datos_consulta) {
            if(isset($datos_consulta[1])){
                $cantidad_disponible=$datos_consulta[1]["cantidad_disponible"];
                if($cantidad_disponible==0){
                    continue;
                }
                if($b==1){
                $back=$this->color_linea1_tablas;
                $b=0;
            }else{
                $back=$this->color_linea2_tablas;
                $b=1;
            }
            $html.='<tr>
                        
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta[1]["referencia_insumo"].'</td>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta[1]["nombre_insumo"].'</td>
                        <td align="rigth" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$cantidad_disponible.'</td>
                        
                    </tr> ';
            }
        }
                
        $html.='</table>';
        return($html);
        
    }
    
    public function get_suministros_ejecutados($db,$datos_orden) {
        
        $obCon=new conexion(1);
        $orden_servicio_id=$datos_orden["orden_servicio_id"];
        
        $Back=$this->color_titulos_tablas;
        $html ='<table class="table table-striped table-bordered table-responsive" cellspacing="1" cellpadding="2" border="0"> ';
        $html .=' 
                
                    <tr>
                        <td align="center" colspan="4"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>INSUMOS UTILIZADOS EN LA EJECUCIÓN:</strong></td>
                    </tr>   
                    <tr>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>FECHA</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>REFERENCIA</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>INSUMO</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>CANTIDAD</strong></td>
                        
                    </tr>
                    
                 
                ';
        
        $sql="SELECT t1.*,
                (SELECT t2.nombre FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as nombre_insumo,
                (SELECT t2.referencia FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as referencia_insumo     
                FROM $db.ordenes_servicio_insumos t1 
                WHERE t1.orden_servicio_id='$orden_servicio_id' AND tipo_registro=2 AND deleted='0000-00-00 00:00:00' ORDER BY t1.ID ASC";
        
        $Consulta=$obCon->Query($sql);
        $b=1;
        while($datos_consulta=$obCon->FetchArray($Consulta)){
            if($b==1){
                $back=$this->color_linea1_tablas;
                $b=0;
            }else{
                $back=$this->color_linea2_tablas;
                $b=1;
            }
            $html.='<tr>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["fecha"].'</td>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["referencia_insumo"].'</td>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["nombre_insumo"].'</td>
                        <td align="rigth" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["cantidad"].'</td>
                        
                    </tr> ';
        }
        
        
        $html.='</table>';
        return($html);
        
    }
    
    public function get_suministros_entregados($db,$datos_orden) {
        
        $obCon=new conexion(1);
        $orden_servicio_id=$datos_orden["orden_servicio_id"];
        
        $Back=$this->color_titulos_tablas;
        $html ='<table class="table table-striped table-bordered table-responsive" cellspacing="1" cellpadding="2" border="0"> ';
        $html .=' 
                
                    <tr>
                        <td align="center" colspan="4"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>INSUMOS ENTREGADOS PARA LA EJECUCIÓN:</strong></td>
                    </tr>   
                    <tr>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>FECHA</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>REFERENCIA</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>INSUMO</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>CANTIDAD</strong></td>
                        
                    </tr>
                    
                 
                ';
        
        $sql="SELECT t1.*,
                (SELECT t2.nombre FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as nombre_insumo,
                (SELECT t2.referencia FROM $db.ordenes_servicio_catalogo_insumos t2 WHERE t1.insumo_id=t2.ID LIMIT 1) as referencia_insumo     
                FROM $db.ordenes_servicio_insumos t1 
                WHERE t1.orden_servicio_id='$orden_servicio_id' AND tipo_registro=1 AND deleted='0000-00-00 00:00:00' ORDER BY t1.ID ASC";
        
        $Consulta=$obCon->Query($sql);
        $b=1;
        while($datos_consulta=$obCon->FetchArray($Consulta)){
            if($b==1){
                $back=$this->color_linea1_tablas;
                $b=0;
            }else{
                $back=$this->color_linea2_tablas;
                $b=1;
            }
            $html.='<tr>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["fecha"].'</td>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["referencia_insumo"].'</td>
                        <td align="left" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["nombre_insumo"].'</td>
                        <td align="rigth" style="background-color:'.$back.';border-bottom-width: 1px solid #000;">'.$datos_consulta["cantidad"].'</td>
                        
                    </tr> ';
        }
        
        
        $html.='</table>';
        return($html);
        
    }
    
    public function get_general_data($db,$datos_orden) {
        
        $Back=$this->color_titulos_tablas;
        $html =' 
                <table class="table table-striped table-bordered table-responsive" cellspacing="1" cellpadding="2" border="0">
                    <tr>
                        <td align="center" colspan="6"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>ORDEN DE SERVICIO</strong></td>
                    </tr>   
                    <tr>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>FECHA:</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;">'.$datos_orden["fecha_orden"].'</td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>TERCERO:</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;">'.$datos_orden["tercero_razon_social"].'</td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>NIT: </strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;">'.$datos_orden["tercero_identificacion"].'</td>
                    </tr>
                    <tr>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>DIRECCIÓN:</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;">'.$datos_orden["direccion"].'</td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>MUNICIPIO:</strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;">'.$datos_orden["nombre_municipio"].'</td>
                        <td align="left" style="border-bottom-width: 1px solid #000;"><strong>ASIGNADA A: </strong></td>
                        <td align="left" style="border-bottom-width: 1px solid #000;">'.$datos_orden["nombre_usuario_asignado"].'</td>
                    </tr>
                    <tr>
                        <td align="left" colspan="1" style="border-bottom-width: 1px solid #000;"><strong>OBSERVACIONES INICIALES:</strong></td>                        
                        <td align="left" colspan="1" style="border-bottom-width: 1px solid #000;">'.$datos_orden["observaciones_iniciales"].'</td>
                        <td align="left" colspan="1" style="border-bottom-width: 1px solid #000;"><strong>OBSERVACIONES DE CIERRE:</strong></td>                        
                        <td align="left" colspan="1" style="border-bottom-width: 1px solid #000;">'.$datos_orden["observaciones_finales"].'</td>
                        <td align="left" colspan="1" style="border-bottom-width: 1px solid #000;"><strong>ESTADO DE LA ORDEN:</strong></td>                        
                        <td align="left" colspan="1" style="border-bottom-width: 1px solid #000;">'.$datos_orden["nombre_estado"].'</td>
                            
                    </tr>
                 </table>
                ';
        
        return($html);
        
    }
    
    
    
    /**
     * Fin Clase
     */
}
