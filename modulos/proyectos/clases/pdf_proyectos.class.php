<?php

if(file_exists("../../../general/class/ClasesPDFDocumentos.class.php")){
    include_once("../../../general/class/ClasesPDFDocumentos.class.php");
}

class PDF_Proyectos extends Documento{
    public $color_titulos_tablas="#000aa0";
    public $color_fuente_titulos_tablas="white";
    public $color_linea1_tablas="#f2f2f2"; 
    public $color_linea2_tablas="white";   
    
    public function pdf_informe_proyecto($db,$empresa_id,$proyecto_id ) {
        $obCon=new conexion($_SESSION["idUser"]);
        $datos_formato_calidad=$obCon->DevuelveValores("$db.formatos_calidad", "ID", 42);
        $datos_proyecto=$obCon->DevuelveValores("$db.vista_proyectos", "proyecto_id", $proyecto_id);
        $this->PDF_Ini("Proyectos", 8, "",1,"../../../");
        $this->PDF_Encabezado($datos_proyecto["created"],$empresa_id, 42, "","","","../../../");
        $html='<BR><BR><BR><center><strong>INFORME GENERAL DEL PROYECTO: '.($datos_proyecto["nombre"]).', PARA EL CLIENTE '.$datos_proyecto["cliente_razon_social"].'</strong></center><br>';
        $this->PDF_Write($html);
        $cuerpo_formato= str_replace("@nombre_proyecto", ($datos_proyecto["nombre"]), $datos_formato_calidad["CuerpoFormato"]);
        $html='<p style="text-align: justify;">'.($cuerpo_formato).'</p>';
        $this->PDF_Write($html);
        
        $html=$this->get_resumen_general($db,$datos_proyecto) ;
        $this->PDF_Write('<br><br><br>'.$html);
        
        $html=$this->get_tareas_actividades($db,$datos_proyecto) ;
        $this->PDF_Write('<br><br><br>'.$html);
        
        $html=$this->get_recursos_actividades($db,$datos_proyecto) ;
        $this->PDF_Write('<br><br><br>'.$html);
        
        $html=$this->get_resumen_recursos($db,$datos_proyecto) ;
        $this->PDF_Write('<br><br><br>'.$html);
        
        $html=$this->get_flujo_caja($db,$datos_proyecto) ;
        $this->PDF_Write('<br><br><br>'.$html);
        
         
        $html='<p style="text-align: justify;">'.($datos_formato_calidad["NotasPiePagina"]).'</p>';
        $this->PDF_Write($html);
        
        $this->get_cronograma($db,$datos_proyecto) ;
        
        $this->PDF_Output("Informe_proyecto_$datos_proyecto[ID]");
    }
    
    public function pdf_cronograma_proyecto($db,$empresa_id,$proyecto_id ) {
        $obCon=new conexion($_SESSION["idUser"]);
        $datos_formato_calidad=$obCon->DevuelveValores("$db.formatos_calidad", "ID", 42);
        $datos_proyecto=$obCon->DevuelveValores("$db.vista_proyectos", "proyecto_id", $proyecto_id);
        $this->PDF_Ini("Proyectos", 8, "",1,"../../../");
        $this->PDF_Encabezado($datos_proyecto["created"],$empresa_id, 42, "","","","../../../");
        $html='<BR><BR><BR><center><strong>CRONOGRAMA DEL PROYECTO: '.($datos_proyecto["nombre"]).', PARA EL CLIENTE '.$datos_proyecto["cliente_razon_social"].'</strong></center><br>';
        $this->PDF_Write($html);
           
        
        $this->get_cronograma($db,$datos_proyecto,0) ;
        
         
        $this->PDF_Output("Informe_proyecto_$datos_proyecto[ID]");
    }
    
    public function get_cronograma($db,$datos_proyecto,$salto_inicial=1) {
        $proyecto_id=$datos_proyecto["proyecto_id"];
        $obCon=new conexion($_SESSION["idUser"]);
        $sql="SELECT t1.*,
                 (SELECT t2.titulo_actividad FROM $db.proyectos_actividades t2 WHERE t2.actividad_id=t1.actividad_id LIMIT 1) as nombre_actividad 
                        
                   FROM $db.proyectos_actividades_eventos t1 WHERE proyecto_id='$proyecto_id' and estado<10 ORDER BY fecha_inicial asc";
        
        $Consulta=$obCon->Query($sql);
        $eventos=[];
        $i=0;
        while($datos_eventos=$obCon->FetchAssoc($Consulta)){
            
            $eventos[$i]=$datos_eventos;
            $i++;
        }
        
        $sql="SELECT SUBSTR(fecha_inicial, 1, 4) as anio, SUBSTR(fecha_inicial, 6, 2) as mes 
                 FROM $db.proyectos_actividades_eventos WHERE proyecto_id='$proyecto_id' and estado<10 
                 GROUP BY SUBSTR(fecha_inicial, 1, 4),SUBSTR(fecha_inicial, 6, 2) ORDER BY fecha_inicial asc
                 ";
        $Consulta=$obCon->Query($sql);
        $inicio=1;
        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
            if($salto_inicial==1 or $inicio==0){
                $this->PDF->AddPage();
                $salto_inicial=0;
                
            }
            $inicio=0;
            $html=$this->get_calendar_mes($db, $datos_consulta["anio"], $datos_consulta["mes"],$eventos);
            $this->PDF_Write($html);
            //$this->PDF->AddPage();
        }
        //return($html);
    }
    
    public function get_calendar_mes($db,$anio,$mes,$eventos) {
        if($mes<>10){
            $numero_mes= str_replace("0", "", $mes);
        }
        $dias_semana=['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
        $meses=['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $datos_dia=$this->get_datos_dia_mes($anio, $mes);
        
        $html =' <br><br>
                <table cellspacing="1" cellpadding="2" border="1">
                    <tr>
                        <td align="center" colspan="7"  style="border-bottom: 1px solid #ddd;"><strong>CRONOGRAMA MES DE '.strtoupper($meses[$numero_mes]).' </strong></td>
                    </tr>  ';
        $html.='<tr>';
        foreach ($dias_semana as $key => $value) {
            $html.='<td style="text-align:center;border-bottom: 1px solid #ddd;">';
                $html.="<strong>".$value."</strong>";
            $html.='</td>';
        }
        $html.='</tr>';
        $d=1;
        $contador_dia=$datos_dia["primer_dia_semana"];
        $flag_conteo=0;
        for($i=1;$i<=$datos_dia["total_semanas"];$i++){
            $html.='<tr>';
                foreach ($dias_semana as $key => $value) {
                    $html.='<td height="100px;" style="text-align:center;border: 1px solid #ddd;">';
                        
                        if($flag_conteo==1 and $d<=$datos_dia["ultimo_dia"]){
                            $fecha_compuesta=$anio."-".$mes."-".str_pad($d, 2, "0", STR_PAD_LEFT)." 00:00:00";
                            $fecha_calendario_inicial=new DateTime($fecha_compuesta);
                            $fecha_compuesta=$anio."-".$mes."-".str_pad($d, 2, "0", STR_PAD_LEFT)." 23:59:59";
                            $fecha_calendario_final=new DateTime($fecha_compuesta);
                            
                            $html.='<div style="text-align:rigth">'.$d.'</div>';
                            foreach ($eventos as $key => $array_eventos) {
                                $fecha_inicial=new DateTime($array_eventos["fecha_inicial"]);                                
                                $fecha_final=new DateTime($array_eventos["fecha_final"]);
                                
                                if($fecha_inicial>=$fecha_calendario_inicial and $fecha_final<=$fecha_calendario_final){
                                    $hora_inicial= substr($array_eventos["fecha_inicial"], 11,5);
                                    $hora_final= substr($array_eventos["fecha_final"], 11,5);
                                    $html.='<div style="text-align:left">'.$hora_inicial."-$hora_final ".$array_eventos["titulo"].'</div>';
                                }
                            }
                            
                            
                            $d++;
                        }
                        if($key==$contador_dia and $flag_conteo==0){
                            $fecha_compuesta=$anio."-".$mes."-".str_pad($d, 2, "0", STR_PAD_LEFT)." 00:00:00";
                            $fecha_calendario_inicial=new DateTime($fecha_compuesta);
                            $fecha_compuesta=$anio."-".$mes."-".str_pad($d, 2, "0", STR_PAD_LEFT)." 23:59:59";
                            $fecha_calendario_final=new DateTime($fecha_compuesta);
                            
                            $html.='<div style="text-align:rigth">'.$d.'</div>';
                            foreach ($eventos as $key => $array_eventos) {
                                $fecha_inicial=new DateTime($array_eventos["fecha_inicial"]);                                
                                $fecha_final=new DateTime($array_eventos["fecha_final"]);
                                
                                if($fecha_inicial>=$fecha_calendario_inicial and $fecha_final<=$fecha_calendario_final){
                                    $hora_inicial= substr($array_eventos["fecha_inicial"], 11,5);
                                    $hora_final= substr($array_eventos["fecha_final"], 11,5);
                                    $html.='<div style="text-align:left">'.$hora_inicial."-$hora_final ".$array_eventos["titulo"].'</div>';
                                }
                            }
                            
                            
                            
                            $flag_conteo=1;
                            $d++;
                        }
                        
                    $html.='</td>';
                }
            $html.='</tr>';
        }
        
        
        $html .='</table>';
        return($html);
    }
    
    function get_datos_dia_mes($anio,$mes) {
        $dia["ultimo_dia"]= date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));
        $dia["ultimo_dia_semana"]= date("w",(mktime(0,0,0,$mes+1,1,$anio)-1));
        $dia["primer_dia_semana"]= date("w", strtotime("$anio-$mes-01"));
        $dia["total_semanas"]=date('W',mktime(0,0,0,$mes,date('t', mktime(0,0,0,$mes,1,$anio)),$anio))-date('W',mktime(0,0,0,$mes,1,$anio));
        $dia["total_semanas"]++;
        if($dia["ultimo_dia_semana"]==0){
            $dia["ultimo_dia_semana"]=6;
        }else{
            $dia["ultimo_dia_semana"]=$dia["ultimo_dia_semana"]-1;
        }
        if($dia["primer_dia_semana"]==0){
            $dia["primer_dia_semana"]=6;
        }else{
            $dia["primer_dia_semana"]=$dia["primer_dia_semana"]-1;
        }
        return($dia);
    }
    
    public function get_flujo_caja($db,$datos_proyecto) {
        $proyecto_id=$datos_proyecto["proyecto_id"];
        $obCon=new conexion($_SESSION["idUser"]);
        
        $sql="select SUBSTRING(t1.fecha_inicio,1,10) AS fecha,
                    sum(t1.costo_unitario_planeacion*t1.cantidad_planeacion*if(t1.hora_fijo=0,t1.total_horas_planeadas,1) ) as costo_total, 
                    sum(t1.total_facturar) as precio_venta 
                    from $db.vista_proyectos_informe_recursos t1 
                    where  t1.proyecto_id='$proyecto_id' 
                    group by SUBSTRING(t1.fecha_inicio,1,10) order by t1.fecha_inicio asc;
             ";
        
        $Back=$this->color_titulos_tablas;
        $html =' 
                <table cellspacing="1" cellpadding="2" border="0">
                    <tr>
                        <td align="center" colspan="5"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>INFORME DE FLUJO DE CAJA</strong></td>
                    </tr>   
                    <tr>
                        <td align="center" ><strong>FECHA</strong></td>                        
                        <td align="center" ><strong>COSTO TOTAL</strong></td>
                        <td align="center" ><strong>VENTA TOTAL</strong></td>
                        <td align="center" ><strong>COSTOS ACUMULADOS</strong></td>
                        <td align="center" ><strong>VENTA ACUMULADA</strong></td>                       
                    </tr>
      
                ';
        $Back=$this->color_linea1_tablas;
        $costo_total=0;
        $venta_total=0;
        $Consulta=$obCon->Query($sql);
        $z=0;
        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
            $costo_total=$costo_total+$datos_consulta["costo_total"];
            $venta_total=$venta_total+$datos_consulta["precio_venta"];
            if($z==0){
                $z=1;
                $Back=$this->color_linea1_tablas;
            }else{
                $z=0;
                $Back=$this->color_linea2_tablas;
            }
            $html .=' <tr>
                        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.$datos_consulta["fecha"].'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["costo_total"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["precio_venta"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($costo_total).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($venta_total).'</td>
                        
                    </tr>';
        }
        
        $html .='</table>';
            
        return($html);
    }
    
    
    public function get_resumen_recursos($db,$datos_proyecto) {
        $proyecto_id=$datos_proyecto["proyecto_id"];
        $obCon=new conexion($_SESSION["idUser"]);
        
        $sql="select t1.nombre_recurso,sum(t1.cantidad_planeacion) as cantidad,
                    sum(t1.costo_unitario_planeacion*t1.cantidad_planeacion*if(t1.hora_fijo=0,t1.total_horas_planeadas,1) ) as costo_total, 
                    sum(t1.total_facturar) as precio_venta 
                    from $db.vista_proyectos_informe_recursos t1 
                    where t1.proyecto_id='$proyecto_id'    
                    group by t1.recurso_id,tipo_recurso order by nombre_recurso asc;
             ";
        
        $Back=$this->color_titulos_tablas;
        $html =' 
                <table cellspacing="1" cellpadding="2" border="0">
                    <tr>
                        <td align="center" colspan="5"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>RESUMEN DE RECURSOS EN EL PROYECTO</strong></td>
                    </tr>   
                    <tr>
                        <td align="center" colspan="2" ><strong>NOMBRE</strong></td>                        
                        <td align="center" ><strong>CANTIDAD</strong></td>
                        <td align="center" ><strong>COSTO TOTAL</strong></td>
                        <td align="center" ><strong>VENTA</strong></td>
                                               
                    </tr>
      
                ';
        $Back=$this->color_linea1_tablas;
        $costo_total=0;
        $venta_total=0;
        $Consulta=$obCon->Query($sql);
        $z=0;
        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
            $costo_total=$costo_total+$datos_consulta["costo_total"];
            $venta_total=$venta_total+$datos_consulta["precio_venta"];
            if($z==0){
                $z=1;
                $Back=$this->color_linea1_tablas;
            }else{
                $z=0;
                $Back=$this->color_linea2_tablas;
            }
            $html .=' <tr>
                        <td align="left" colspan="2" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.$datos_consulta["nombre_recurso"].'</td>
                        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["cantidad"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["costo_total"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["precio_venta"]).'</td>
                        
                    </tr>';
        }
        $html .=' <tr>
                        <td align="rigth" colspan="3" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >Totales</td>
                        
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" ><strong>'.number_format($costo_total).'</strong></td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" ><strong>'.number_format($venta_total).'</strong></td>
                        
                    </tr>';
        $html .='</table>';
            
        return($html);
    }
    
    
    public function get_recursos_actividades($db,$datos_proyecto) {
        $proyecto_id=$datos_proyecto["proyecto_id"];
        $obCon=new conexion($_SESSION["idUser"]);
        
        $sql="select t2.titulo_actividad,t1.nombre_recurso,t1.costo_unitario_planeacion,t1.cantidad_planeacion,t1.utilidad_esperada,
                t1.precio_venta_unitario_planeacion_segun_utilidad, t1.precio_venta_total_planeado,t2.total_horas_planeadas,
                if(t1.hora_fijo=0,'X Hora','Fijo') as tipo_recurso, 
                if(t1.hora_fijo=0,(t1.precio_venta_total_planeado*t2.total_horas_planeadas),t1.precio_venta_total_planeado) as total_facturar 
                from $db.proyectos_actividades_recursos t1 
                inner join $db.proyectos_actividades t2 on t1.actividad_id=t2.actividad_id

                where t2.estado<10 and t2.proyecto_id='$proyecto_id' order by t2.fecha_inicio_planeacion ASC";
        
        $Back=$this->color_titulos_tablas;
        $html =' 
                <table cellspacing="1" cellpadding="2" border="0">
                    <tr>
                        <td align="center" colspan="11"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>RECURSOS A UTILIZAR EN EL PROYECTO</strong></td>
                    </tr>   
                    <tr>
                        <td align="center" colspan="2" ><strong>ACTIVIDAD</strong></td>
                        <td align="center" colspan="2" ><strong>RECURSO</strong></td>
                        <td align="center" ><strong>VALOR</strong></td>
                        <td align="center" ><strong>COSTO UNITARIO</strong></td>
                        <td align="center" ><strong>CANTIDAD</strong></td>
                        <td align="center" ><strong>UTILIDAD</strong></td>
                        <td align="center" ><strong>PRECIO VENTA</strong></td>
                        <td align="center" ><strong>HORAS</strong></td>
                        <td align="center" ><strong>FACTURA</strong></td>
                    </tr>
      
                ';
        $Back=$this->color_linea1_tablas;
        
        $Consulta=$obCon->Query($sql);
        $z=0;
        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
            if($z==0){
                $z=1;
                $Back=$this->color_linea1_tablas;
            }else{
                $z=0;
                $Back=$this->color_linea2_tablas;
            }
            $html .=' <tr>
                        <td align="left" colspan="2" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.$datos_consulta["titulo_actividad"].'</td>
                        <td align="left" colspan="2" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.$datos_consulta["nombre_recurso"].'</td>
                        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.$datos_consulta["tipo_recurso"].'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["costo_unitario_planeacion"]).'</td>
                        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["cantidad_planeacion"]).'</td>
                        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["utilidad_esperada"],2).'%</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["precio_venta_unitario_planeacion_segun_utilidad"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["total_horas_planeadas"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';" >'.number_format($datos_consulta["total_facturar"]).'</td>
                    </tr>';
        }
        $html .='</table>';
            
        return($html);
    }
    
    public function get_tareas_actividades($db,$datos_proyecto) {
        $obCon=new conexion($_SESSION["idUser"]);
        $Back=$this->color_titulos_tablas;
        $proyecto_id=$datos_proyecto["proyecto_id"];
        $datos_tareas=[];
        $sql="SELECT t1.tarea_id,t1.nombre_tarea, SUM(t1.total_costos_planeacion) as total_costos,
                SUM(t1.precio_venta_planeado) as total_venta,
                 MIN(t1.fecha_inicial_planeada) as fecha_inicio,
                 MAX(t1.fecha_final_planeada) as fecha_final,
                 (SELECT SUM(horas) FROM $db.proyectos_actividades_eventos t2 WHERE t1.tarea_id=t2.tarea_id and estado<10) as total_horas
                 
                 
                FROM $db.vista_proyectos_costos t1 WHERE t1.proyecto_id='$proyecto_id' group by t1.tarea_id order by t1.fecha_inicial_planeada asc";
        
        $Consulta=$obCon->Query($sql);
        
        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
            $tarea_id=$datos_consulta["tarea_id"];
            $datos_tareas[$tarea_id]["nombre_tarea"]=$datos_consulta["nombre_tarea"];
            $datos_tareas[$tarea_id]["total_costos"]=$datos_consulta["total_costos"];
            $datos_tareas[$tarea_id]["total_venta"]=$datos_consulta["total_venta"];
            $datos_tareas[$tarea_id]["fecha_inicio"]=$datos_consulta["fecha_inicio"];
            $datos_tareas[$tarea_id]["fecha_final"]=$datos_consulta["fecha_final"];
            $datos_tareas[$tarea_id]["total_horas"]=$datos_consulta["total_horas"];
        }
        
        $sql="SELECT t1.tarea_id,t1.actividad_id,t1.nombre_tarea,t1.nombre_actividad, SUM(t1.total_costos_planeacion) as total_costos,
                SUM(t1.precio_venta_planeado) as total_venta,
                 MIN(t1.fecha_inicial_planeada) as fecha_inicio,
                 MAX(t1.fecha_final_planeada) as fecha_final,
                (SELECT SUM(horas) FROM $db.proyectos_actividades_eventos t2 WHERE t1.actividad_id=t2.actividad_id and estado<10) as total_horas
                 
                FROM $db.vista_proyectos_costos t1 WHERE t1.proyecto_id='$proyecto_id' group by t1.actividad_id order by t1.fecha_inicial_planeada asc";
        
        $Consulta=$obCon->Query($sql);
        $datos_actividades=[];
        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
            $tarea_id=$datos_consulta["tarea_id"];
            $actividad_id=$datos_consulta["actividad_id"];
            $datos_actividades[$tarea_id][$actividad_id]["nombre_actividad"]=$datos_consulta["nombre_actividad"];
            $datos_actividades[$tarea_id][$actividad_id]["total_costos"]=$datos_consulta["total_costos"];
            $datos_actividades[$tarea_id][$actividad_id]["total_venta"]=$datos_consulta["total_venta"];
            $datos_actividades[$tarea_id][$actividad_id]["fecha_inicio"]=$datos_consulta["fecha_inicio"];
            $datos_actividades[$tarea_id][$actividad_id]["fecha_final"]=$datos_consulta["fecha_final"];
            $datos_actividades[$tarea_id][$actividad_id]["total_horas"]=$datos_consulta["total_horas"];
        }
       
        $html =' 
                <table cellspacing="1" cellpadding="2" border="0">
                    <tr>
                        <td align="center" colspan="7"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>TAREAS Y ACTIVIDADES DE ESTE PROYECTO</strong></td>
                    </tr>   
                    <tr>
                        <td align="center" colspan="2" width="30%" style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>TAREA</strong></td>
                        <td align="center" colspan="1" width="16%" style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>FECHA INICIAL</strong></td>
                        <td align="center" colspan="1" width="16%" style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>FECHA FINAL</strong></td>
                        <td align="center" colspan="1" width="8%"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>HORAS</strong></td>
                        <td align="center" colspan="1" width="15%" style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>TOTAL COSTOS</strong></td>
                        <td align="center" colspan="1" width="15%" style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>TOTAL VENTA</strong></td>    
                    </tr>   
                    
      
                ';
        
        $i=0;
        foreach ($datos_tareas as $key => $array_tareas) {
            $i++;
            $Back='#f6fcfc';
            $tarea_id=$key;
            $html .=' 
                
                    <tr>
                        <td colspan="2" align="left" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';"><span style="color:white;background-color: #000aa0;"> <strong>'.$i.'</strong> </span> '.$array_tareas["nombre_tarea"].' </td>
                        <td align="center" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">'.$array_tareas["fecha_inicio"].'</td>
                        <td align="center" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">'.$array_tareas["fecha_final"].'</td>
                        <td align="center" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';"><strong>'.$array_tareas["total_horas"].'</strong></td>
                        <td align="right" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';"><span style="color:white;background-color: #0f6d0a;"> <strong>$'.number_format($array_tareas["total_costos"]).'</strong></span></td>
                        <td align="right" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';"><span style="color:white;background-color: #005892;"> <strong>$'.number_format($array_tareas["total_venta"]).'</strong></span></td>
                        
                    </tr>

                  
                ';
            foreach ($datos_actividades[$tarea_id] as $key => $array_actividades) {
                
                $Back='white';
                $html .=' 
                
                    <tr>
                        <td align="right" width="5%">*</td>
                        <td align="left" width="25%" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';"><span style="color:white;background-color: #000aa0;"></span> '.$array_actividades["nombre_actividad"].' </td>
                        <td align="center" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">'.$array_actividades["fecha_inicio"].'</td>
                        <td align="center" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">'.$array_actividades["fecha_final"].'</td>
                        <td align="center" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">'.$array_actividades["total_horas"].'</td>     
                        <td align="right" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">$'.number_format($array_actividades["total_costos"]).'</td>
                        <td align="right" style="border-bottom: 0px solid #ddd;background-color: '.$Back.';">$'.number_format($array_actividades["total_venta"]).'</td>
                        
                    </tr>

                  
                ';
            }
        }
        
        $html .='</table>';
        return($html);
    }
    
    public function get_resumen_general($db,$datos_proyecto) {
        
        $Back=$this->color_titulos_tablas;
        $html =' 
                <table cellspacing="1" cellpadding="2" border="0">
                    <tr>
                        <td align="center" colspan="6"  style="color:'.$this->color_fuente_titulos_tablas.';border-bottom: 1px solid #ddd;background-color: '.$Back.';"><strong>RESUMEN DEL PROYECTO</strong></td>
                    </tr>   
                    <tr>
                        <td align="center" ><strong>FECHA DE INICIO</strong></td>
                        <td align="center" ><strong>FECHA DE FINALIZACIÓN</strong></td>
                        <td align="center" ><strong>TOTAL DE HORAS</strong></td>
                        <td align="center" ><strong>TOTAL COSTOS</strong></td>
                        <td align="center" ><strong>TOTAL A FACTURAR</strong></td>
                        <td align="center" ><strong>UTILIDAD ESPERADA</strong></td>
                    </tr>
      
                ';
        $Back=$this->color_linea1_tablas;
        $html .=' 
                
                    <tr>
                        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$datos_proyecto["fecha_inicio_planeacion"].'</td>
                        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$datos_proyecto["fecha_final_planeacion"].'</td>
                        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($datos_proyecto["total_horas_planeadas"]).'</td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"><span style="color:white;background-color: #0f6d0a;"> <strong>$'.number_format($datos_proyecto["costos_planeacion"]).'</strong></span></td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"><span style="color:white;background-color: #005892;"> <strong>$'.number_format($datos_proyecto["valor_facturar"]).'</strong></span></td>
                        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($datos_proyecto["utilidad_planeada"],2).'</td>
                    </tr>

                 </table>        
                ';
        return($html);
        
    }
    
    
    
    /**
     * Fin Clase
     */
}
