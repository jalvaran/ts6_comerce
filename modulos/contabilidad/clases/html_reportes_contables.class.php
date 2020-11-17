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
    
    function opciones_auxiliares_contables_html($empresa_id) {
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
        
    /**
     * Fin Clase
     */
}
