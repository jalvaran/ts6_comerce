<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/procesos_juridicos.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new ProcesoJuridico($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo el formulario para registrar o editar un repositorio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $proceso_id=$obCon->normalizar($_REQUEST["proceso_id"]);
            $datos_repositorio=$obCon->DevuelveValores("$db.procesos_juridicos", "proceso_id", $proceso_id);
            if($proceso_id==''){
                $proceso_id=$obCon->getUniqId("prj_");
            }
            
            print('<div class="panel">
                                <div class="panel-head">
                                    <h5 class="panel-title">Crear o Editar un Proceso</h5>
                                </div>
                                <div class="panel-body">
                                    
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Tema</label>
                                                    <div class="col-12">');
                                                        $css->select("tema_id", "form-control", "tema_id", "", "", "", "");

                                                            $sql="select * from $db.procesos_juridicos_temas ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["tema_id"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["nombre_tema"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">Seleccione un Tema</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Sub Tema</label>
                                                    <div class="col-12">
                                                        ');
                                                        $css->select("sub_tema_id", "form-control", "sub_tema_id", "", "", "", "");

                                                            $sql="select * from $db.procesos_juridicos_sub_temas ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["subtema_id"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["nombre_sub_tema"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">Seleccione un Subtema del proceso</span>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            
                                                
                                            <div class="col-lg-4">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Tipo de Proceso</label>
                                                    <div class="col-12">
                                                        ');
                                                        $css->select("tipo_proceso_id", "form-control", "tipo_proceso_id", "", "", "", "");

                                                            $sql="select * from $db.procesos_juridicos_tipo ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["tipo_proceso_id"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["proceso_tipo"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">Seleccione el Tipo del proceso</span>
                                                    
                                                    </div>
                                                </div>
                                                </div>
                                                
                                            
                                        </div>
                                        <div class="form-group row">
                                            
                                            <div class="col-6">
                                                ');
                                                        $css->select("tercero_id", "form-control", "tercero_id", "", "", "", "");
                                 
                                                            $css->option("", "", "", "", "", "");
                                                                print("Tercero");
                                                            $css->Coption();
                                                            
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">Seleccione un tercero</span>
                                                    
                                            </div>');
                                            print( '
                                                
                                            <div class="col-6">
                                                ');
                                                        $css->select("usuario_asignado_id", "form-control", "usuario_asignado_id", "", "", "", "");
                                 
                                                            $css->option("", "", "", "", "", "");
                                                                print("Asignar a...");
                                                            $css->Coption();
                                                            
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">Usuario que lleva el proceso</span>
                                                    
                                            </div>');
                                            print( ' 
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Descripción <i class="far fa-comment text-flickr" ></i></label>
                                            <div class="col-12">
                                                <textarea id="descripcion" name="descripcion" class="form-control" style="height:200px;">'.$datos_repositorio["descripcion"].'</textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    
                                                        <label class="col-form-label">Año Gravable <i class="far fa-calendar-times text-flickr" ></i></label>
                                                        ');
                                                        $disabled="";
                                                        
                                                        $css->select("anio_gravable", "form-control", "anio_gravable", "", "", "", $disabled);
                                                            $anoactual=date("Y");
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            
                                                            for($i=2000;$i<=$anoactual;$i++){
                                                                $sel=0;
                                                                if($anoactual==$i and $datos_repositorio["anio_gravable"]==''){
                                                                    $sel=1;
                                                                }
                                                                if($datos_repositorio["anio_gravable"]==$i){
                                                                    $sel=1;
                                                                }
                                                                
                                                                $css->option("", "", "", $i, "", "",$sel);
                                                                    print($i);
                                                                $css->Coption();
                                                            }
                                                            
                                                        $css->Cselect();
                                            
                                            print('</div></div>');            
                                            
                                            print(' <div class="col-lg-4">
                                                <div class="form-group">
                                                    
                                                        <label class="col-form-label">Periodo <i class="far fa-calendar-times text-flickr" ></i></label>
                                                        ');
                                                        $disabled="";
                                                        
                                                        $css->select("periodo", "form-control", "periodo", "", "", "", $disabled);
                                                            
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            
                                                            for($i=1;$i<=12;$i++){
                                                                $sel=0;
                                                                
                                                                if($datos_repositorio["periodo"]==$i){
                                                                    $sel=1;
                                                                }
                                                                
                                                                $css->option("", "", "", $i, "", "",$sel);
                                                                    print($i);
                                                                $css->Coption();
                                                            }
                                                            
                                                        $css->Cselect();
                                            
                                            print('</div></div>');  
                                            
                                            print( ' 
                                                    
                                                
                                            
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    
                                                        <label class="col-form-label">Estado <i class="fa fa-tag text-flickr" ></i></label>
                                                        ');
                                                        $css->select("estado", "form-control", "estado", "", "", "", "");

                                                            $sql="select * from $db.procesos_juridicos_estados ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["estado"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["nombre_estado"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '
                                                    
                                                </div>
                                            </div>
                                            
                                            </div>
                                        </div>
                                   
                                </div>
                                <div class="panel-footer text-right">
                                    <button id="btn_guardar" name="btn_guardar" onclick="confirmar_crear_editar_proceso(`'.$proceso_id.'`)" class="btn btn-success mr-2">Guardar</button>
                                    
                                </div>
                            </div>');
            
            
                        
                
                
        break; //Fin caso 1
        
        case 2: //Dibuja los adjuntos de un repositorio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $proceso_id=$obCon->normalizar($_REQUEST["proceso_id"]);
            
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM $db.procesos_juridicos_adjuntos t1 
                        WHERE t1.proceso_id='$proceso_id' 
                            ";
                $Consulta=$obCon->Query($sql);
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosConsulta["ID"];
                    $Nombre=$DatosConsulta["NombreArchivo"];
                    $css->FilaTabla(14);
                
                        $css->ColTabla($idItem, 1);
                       
                        print('<td style="text-align:center;color:blue;font-size:18px;">');
                            $Ruta= "../../".str_replace("../", "", $DatosConsulta["Ruta"]);
                            print('<a href="'.$Ruta.'" target="blank">'.$Nombre.' <li class="fa fa-paperclip"></li></a>');
                        print('</td>');
                        
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "far fa-trash-alt", "", "onclick=EliminarItem(`1`,`$idItem`,`$repositorio_id`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                          
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 2
        
             
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>