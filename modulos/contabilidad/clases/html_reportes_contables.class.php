<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class html_reportes_contables extends conexion{
    
    public $obCon;
    /**
     * Constructor de la clase html reportes contables
     */
    function __construct(){
        $idUser=$_SESSION["idUser"];
        $this->obCon=new conexion($idUser);
        
    }   
    
    function opciones_filtro_reportes_1_html($empresa_id) {
        $datos_empresa=$this->obCon->DevuelveValores("empresapro", "ID", $empresa_id);
        $db=$datos_empresa["db"];
        
        $html=' <div class="row">
                <div class="col-md-3">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text " style="font-size:14px;padding-left:5px">Fecha inicial: </span>
                        </div>
                        <input id="fecha_inicial" type="date" class="form-control" value="'.date("Y-m-d").'" style="padding-left:5px">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text " style="font-size:14px;padding-left:5px">Fecha final: &nbsp;</span>
                        </div>
                        <input id="fecha_final" type="date" class="form-control" value="'.date("Y-m-d").'" style="padding-left:5px">
                    </div>
                </div>';
        $html.='<div class="col-md-4">  
                    <div class="input-group mb-2">   
                        <input id="cuenta_puc" type="text" class="form-control" value="" placeholder="Cuenta Contable" style="padding-left:12px">
                    </div> 
                    <div class="input-group mb-2">    
                        <select id="tercero_id"  name="tercero_id" class="form-control" >
                            <option value="">Tercero</option>
                        </select>  
                        <button id="btn_limpiar_terceros" class="btn btn-light" onclick="limpiar_select2(`tercero_id`)"><li class="far fa-times-circle" style="font-size:20px;color:red"></li></button>
                    </div>
                      
                    
                </div>';
        
        $html.='<div class="col-md-3">                      
                    <div class="input-group mb-2">    
                        <select id="centro_costos_id"  name="centro_costos_id" class="form-control" style="padding-left:5px">
                            ';
                        $sql="SELECT * FROM $db.empresa_centro_costo";
                        $consulta=$this->obCon->Query($sql);
                        
                        while($datos_consulta=$this->FetchAssoc($consulta)){
                            $html.='<option value="'.$datos_consulta["ID"].'">'.$datos_consulta["centro_costo"].'</option>';
                        }
                        
        $html.=        '</select>                        
                    </div>
                                        
                </div>';
        $html.='<div class="col-md-2">   
                    
                    <div class="panel-action panel-action-background">
                        <button id="btnGenerar" title="Generar" class="btn btn-info btn-gradient btn-pill m-1" style="font-size:20px;">Generar <i class="ti-import"></i></button>
                     
                    </div>
                
                </div>';
        
        $html.= '</div>';       
        return($html);
    }
    
    function opciones_balance_comprobacion_html($empresa_id) {
        $datos_empresa=$this->obCon->DevuelveValores("empresapro", "ID", $empresa_id);
        $db=$datos_empresa["db"];
        
        $html=' <div class="row">
                <div class="col-md-3">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text " style="font-size:14px;padding-left:5px">Fecha inicial: </span>
                        </div>
                        <input id="fecha_inicial" type="date" class="form-control" value="'.date("Y-m-d").'" style="padding-left:5px">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text " style="font-size:14px;padding-left:5px">Fecha final: &nbsp;</span>
                        </div>
                        <input id="fecha_final" type="date" class="form-control" value="'.date("Y-m-d").'" style="padding-left:5px">
                    </div>
                </div>';
        $html.='<div class="col-md-4">  
                    <div class="input-group mb-2">   
                        <input id="cuenta_puc" type="text" class="form-control" value="" placeholder="Cuenta Contable" style="padding-left:12px">
                    </div> 
                    <div class="input-group mb-2">    
                        <select id="tercero_id"  name="tercero_id" class="form-control" >
                            <option value="">Tercero</option>
                        </select>   
                        <button id="btn_limpiar_terceros" class="btn btn-light" onclick="limpiar_select2(`tercero_id`)"><li class="far fa-times-circle" style="font-size:20px;color:red"></li></button>
                    </div>
                      
                    
                </div>';
        
        $html.='<div class="col-md-3">                      
                    <div class="input-group mb-2">    
                        <select id="centro_costos_id"  name="centro_costos_id" class="form-control" style="padding-left:5px">
                            ';
                        $sql="SELECT * FROM $db.empresa_centro_costo";
                        $consulta=$this->obCon->Query($sql);
                        
                        while($datos_consulta=$this->FetchAssoc($consulta)){
                            $html.='<option value="'.$datos_consulta["ID"].'">'.$datos_consulta["centro_costo"].'</option>';
                        }
                        
        $html.=        '</select>                        
                    </div>
                    <div class="input-group mb-2">    
                        <select id="opciones_reporte"  name="opciones_reporte" class="form-control" >
                            <option value="1">Detallado</option>
                            <option value="0">Sin Detalles</option>
                        </select>                        
                    </div>
                                        
                </div>';
        $html.='<div class="col-md-2">   
                    
                    <div class="panel-action panel-action-background">
                        <button id="btnGenerar" title="Generar" class="btn btn-info btn-gradient btn-pill m-1" style="font-size:20px;">Generar <i class="ti-import"></i></button>
                     
                    </div>
                
                </div>';
        
        $html.= '</div>';       
        return($html);
    }
        
    
    function opciones_filtro_reportes_2_html($empresa_id) {
        $datos_empresa=$this->obCon->DevuelveValores("empresapro", "ID", $empresa_id);
        $db=$datos_empresa["db"];
        
        
        $html=' <div class="row">
                    <div class="col-md-3">  ';
        
        $html.='                    
                    <div class="input-group mb-2">    
                        <select id="cmb_anio"  name="cmb_anio" class="form-control" style="padding-left:5px">
                            ';
                        $sql="SELECT DISTINCT(SUBSTRING(Fecha,1,4)) as Anio FROM $db.contabilidad_librodiario GROUP BY SUBSTRING(Fecha,1,4)";
                        $Consulta=$this->obCon->Query($sql);
                        $html.='<option value="">Seleccione un año</option>';
                        while($datos_consulta=$this->FetchAssoc($Consulta)){
                            $html.='<option value="'.$datos_consulta["Anio"].'">'.$datos_consulta["Anio"].'</option>';
                        }
                        
        $html.=        '</select>                        
                    </div>';
        
        $html.='                    
                    <div class="input-group mb-2">    
                        <select id="centro_costos_id"  name="centro_costos_id" class="form-control" style="padding-left:5px">
                            ';
                        $sql="SELECT * FROM $db.empresa_centro_costo";
                        $consulta=$this->obCon->Query($sql);
                        
                        while($datos_consulta=$this->FetchAssoc($consulta)){
                            $html.='<option value="'.$datos_consulta["ID"].'">'.$datos_consulta["centro_costo"].'</option>';
                        }
                        
        $html.=        '</select>                        
                    </div>';
                                        
        $html.='    </div>';
                    
        $html.=' 
                <div class="col-md-3">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text " style="font-size:14px;padding-left:5px">Fecha inicial: </span>
                        </div>
                        <input id="fecha_inicial" type="date" class="form-control" value="'.date("Y-m-d").'" style="padding-left:5px">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text " style="font-size:14px;padding-left:5px">Fecha final: &nbsp;</span>
                        </div>
                        <input id="fecha_final" type="date" class="form-control" value="'.date("Y-m-d").'" style="padding-left:5px">
                    </div>
                </div>';
        
        
        $html.='<div class="col-md-2">   
                    
                    <div class="panel-action panel-action-background">
                        <button id="btnGenerar" title="Generar" class="btn btn-info btn-gradient btn-pill m-1" style="font-size:20px;">Generar <i class="ti-import"></i></button>
                     
                    </div>
                
                </div>';
        
        $html.= '</div>';       
        return($html);
    }
    
    public function movimiento_cuentas_html($sql) {
        $Back="#CEE3F6";
        $html='<table id="ReporteMovimientoCuentas" class="table table-bordered table table-hover table-responsive" cellspacing="1" cellpadding="2" border="0"  align="center" >';
                
        ///Se dibujan los ingresos
        $h=0;  
        $Back="white";
        $Consulta=$this->obCon->Query($sql);
        $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="12"><strong>Auxiliar</strong></td>'; 
        $html.='</tr>'; 
        $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td><strong>FECHA</strong></td><td><strong>TERCERO</strong></td><td><strong>DOCUMENTO</strong></td><td><strong>REFERENCIA</strong></td><td><strong>DETALLE</strong></td><td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>DEBITO</strong></td><td><strong>CREDITO</strong></td><td><strong>SALDO MOVIMIENTO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
        
        $html.='</tr>';   
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
                      
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $html.="<td>".$DatosMayor["Fecha"]."</td>";
           $html.="<td>".$DatosMayor["Tercero_Razon_Social"]." ".$DatosMayor["Tercero_Identificacion"]."</td>";
           $html.="<td>".$DatosMayor["Tipo_Documento_Interno"]." ".$DatosMayor["Num_Documento_Interno"]."</td>";
           $html.="<td>".$DatosMayor["Num_Documento_Externo"]."</td>";
           $html.="<td>".$DatosMayor["Detalle"]."</td>";
           $html.="<td>".$DatosMayor["CuentaPUC"]."</td>";
           $html.="<td>".$DatosMayor["NombreCuenta"]."</td>";
           $html.="<td>".number_format($DatosMayor["SaldoInicialCuenta"])."</td>";
           $html.="<td>".number_format($DatosMayor["Debitos"])."</td>";
           $html.="<td>".number_format($DatosMayor["Creditos"])."</td>";
           $html.="<td>".number_format($DatosMayor["SaldoMovimiento"])."</td>";
           $html.="<td>".number_format($DatosMayor["SaldoFinalCuenta"])."</td>";
           
           $html.='</tr>'; 
        }
        $html.='</table>'; 
        
        return(utf8_encode($html));
    }
    
    /**
     * Fin Clase
     */
}
