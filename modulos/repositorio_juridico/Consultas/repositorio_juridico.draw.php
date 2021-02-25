<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");
include_once("../clases/repositorio_juridico.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new RepositorioJuridico($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo el formulario para registrar o editar un repositorio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $repositorio_id=$obCon->normalizar($_REQUEST["repositorio_id"]);
            $datos_repositorio=$obCon->DevuelveValores("$db.repositorio_juridico", "repositorio_id", $repositorio_id);
            if($repositorio_id==''){
                $repositorio_id=$obCon->getUniqId("rep_");
            }
            
            print('<div class="panel">
                                <div class="panel-head">
                                    <h5 class="panel-title">Crear o Editar un Registro</h5>
                                </div>
                                <div class="panel-body">
                                    
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Tema</label>
                                                    <div class="col-12">');
                                                        $css->select("tema_id", "form-control", "tema_id", "", "", "", "");

                                                            $sql="select * from $db.repositorio_juridico_temas ";
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
                                                        
                                                        
                                            print( '<span class="form-text">'.$datos_repositorio["tema_referencia"].'</span>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Sub Tema</label>
                                                    <div class="col-12">
                                                        ');
                                                        $css->select("sub_tema_id", "form-control", "sub_tema_id", "", "", "", "");

                                                            $sql="select * from $db.repositorio_juridico_sub_temas ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["sub_tema_id"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["nombre_sub_tema"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">'.$datos_repositorio["sub_tema_referencia"].'</span>
                                                    
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-3">
                                                        <div class="form-group row">
                                                            <label class="col-12 col-form-label">Número de Documento</label>
                                                            <div class="col-12">
                                                                <input id="fecha_documento" name="fecha_documento" class="form-control" type="date" value="'.date("Y-m-d").'">
                                                            <span class="form-text">'.$datos_repositorio["fecha_referencia"].'</span>
                                                        </div>
                                                        </div>
                                                </div>
                                                
                                            <div class="col-lg-5">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Tipo de Documento</label>
                                                    <div class="col-12">
                                                        ');
                                                        $css->select("tipo_documento_id", "form-control", "sub_tema_id", "", "", "", "");

                                                            $sql="select * from $db.repositorio_juridico_tipo_documentos ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["tipo_documento_id"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["tipo_documento"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">'.$datos_repositorio["tipo_documento_referencia"].'</span>
                                                    
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="col-lg-4">
                                                        <div class="form-group row">
                                                            <label class="col-12 col-form-label">Número de Documento</label>
                                                            <div class="col-12">
                                                                <input id="numero_documento" value="'.$datos_repositorio["numero_documento"].'" name="numero_documento" type="text" class="form-control" placeholder="Número Documento">
                                                            </div>
                                                        </div>
                                                </div>
                                            
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Entidad </label>
                                            <div class="col-12">
                                                ');
                                                        $css->select("entidad_id", "form-control", "entidad_id", "", "", "", "");

                                                            $sql="select * from $db.repositorio_juridico_entidades ";
                                                            $Consulta=$obCon->Query($sql);
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                                $sel=0;
                                                                if($datos_repositorio["entidad_id"]==$datos_consulta["ID"]){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                                    print($datos_consulta["nombre_entidad"]);
                                                                $css->Coption();
                                                            }
                                                        $css->Cselect();
                                                        
                                                        
                                            print( '<span class="form-text">'.$datos_repositorio["entidad_referencia"].'</span>
                                                    
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Extracto <i class="icon-eyeglass text-flickr" ></i></label>
                                            <div class="col-12">
                                                <textarea id="extracto" name="extracto" class="form-control autoHeightDone" style="height:200px;">'.$datos_repositorio["extracto"].'</textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-7">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Fuentes Formales <i class="fa fa-book text-success"></i></label>
                                                    <div class="col-12">
                                                        <input class="form-control" id="fuentes_formales" name="fuentes_formales" type="text" value="'.$datos_repositorio["fuentes_formales"].'" placeholder="Fuentes Formales" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <fieldset disabled="">
                                                        <label class="col-form-label">Año Recopilación <i class="far fa-calendar-times text-flickr" ></i></label>
                                                        ');
                                                        $disabled="disabled";
                                                        
                                                        $css->select("ano_recopilacion", "form-control", "ano_recopilacion", "", "", "", $disabled);
                                                            $anoactual=date("Y");
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione...");
                                                            $css->Coption();
                                                            
                                                            for($i=2015;$i<=2050;$i++){
                                                                $sel=0;
                                                                if($datos_repositorio["ano_recopilacion"]==$i){
                                                                    $sel=1;
                                                                }
                                                                if($anoactual==$i){
                                                                    $sel=1;
                                                                }
                                                                $css->option("", "", "", $i, "", "",$sel);
                                                                    print($i);
                                                                $css->Coption();
                                                            }
                                                            
                                                        $css->Cselect();
                                            
                                            print( ' 
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <fieldset >
                                                        <label class="col-form-label">Estado <i class="fa fa-tag text-flickr" ></i></label>
                                                        ');
                                                        $css->select("estado", "form-control", "estado", "", "", "", "");

                                                            $sql="select * from $db.repositorio_juridico_estados ";
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
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="col-form-label">Adjuntar <i class="fa fa-paperclip text-primary" ></i></label>
                                                    <div class="panel">                            
                                                        <div class="panel-body">
                                                            <form data-repositorio_id="'.$repositorio_id.'" action="/" class="dropzone dz-clickable" id="repositorio_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="col-form-label">Archivos Adjuntados <i class="fa fa-paperclip text-success" ></i></label>
                                                    <div id="div_adjuntos_repositorio">                            
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                   
                                </div>
                                <div class="panel-footer text-right">
                                    <button id="btn_guardar" name="btn_guardar" onclick="confirmar_crear_editar_repositorio(`'.$repositorio_id.'`)" class="btn btn-success mr-2">Guardar</button>
                                    
                                </div>
                            </div>');
            
            
                        
                
                
        break; //Fin caso 1
        
        case 2: //Dibuja los adjuntos de un repositorio
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $repositorio_id=$obCon->normalizar($_REQUEST["repositorio_id"]);
            
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM $db.repositorio_juridico_adjuntos t1 
                        WHERE t1.repositorio_id='$repositorio_id' 
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