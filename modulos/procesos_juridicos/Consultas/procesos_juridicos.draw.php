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
        
        case 1:// dibujo el formulario para registrar o editar un proceso administrativo
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
                                                            
                                                            if($datos_repositorio["tercero_id"]>0){
                                                                $datos_tercero=$obCon->DevuelveValores("$db.terceros", "ID", $datos_repositorio["tercero_id"]);
                                                                $css->option("", "", "",$datos_tercero["ID"], "", "",1);
                                                                    print($datos_tercero["razon_social"]." ".$datos_tercero["identificacion"]);
                                                                $css->Coption();
                                                            }
                                                            
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
                                                            
                                                            if($datos_repositorio["usuario_asignado_id"]>0){
                                                                $datos_usuario=$obCon->DevuelveValores("usuarios", "ID", $datos_repositorio["usuario_asignado_id"]);
                                                                $css->option("", "", "",$datos_usuario["ID"], "", "",1);
                                                                    print($datos_usuario["nombre_completo"]." ".$datos_usuario["Identificacion"]);
                                                                $css->Coption();
                                                            }
                                                            
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
                                        <div class="row">');
                                        
                                        print( '

                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    
                                                        <label class="col-form-label">Municipio <i class="far fa-image text-flickr" ></i></label>
                                                        ');
                                                        $disabled="";
                                                        
                                                        $css->select("codigo_dane_municipio", "form-control", "codigo_dane_municipio", "", "", "", $disabled);
                                                            
                                                            $css->option("", "", "", "", "", "");
                                                                print("Seleccione un municipio");
                                                            $css->Coption();
                                                            
                                                            
                                                        $css->Cselect();
                                            
                                            print('</div></div>');     
                                            
                                        print( '

                                            <div class="col-lg-3">
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
                                            
                                            print(' <div class="col-lg-3">
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
                                                    
                                                
                                            
                                            <div class="col-lg-3">
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
        
        case 2: //Dibuja los adjuntos de un acto administrativo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $acto_id=$obCon->normalizar($_REQUEST["acto_id"]);
            
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM $db.procesos_juridicos_acto_admin_adjuntos t1 
                        WHERE t1.acto_id='$acto_id' 
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
                            
                            $css->li("", "far fa-trash-alt", "", "onclick=EliminarItem(`1`,`$idItem`,`$acto_id`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                          
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 2
        
        case 3://lista los actos administrativos de un proceso
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $proceso_id=$obCon->normalizar($_REQUEST["proceso_id"]);
            
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            $html='<div class="row">
                        <div class="col-lg-5">
                            <div class="panel panel-default">
                                <div class="panel-head">
                                    
                                    <div class="panel-title">
                                        
                                        <i class="icon-docs panel-head-icon font-24"></i>
                                        <div class="panel-title-text">Actos Administrativos</div>
                                        
                                    </div>
                                    <div class="panel-action">
                                        <button onclick="frm_agregar_editar_acto_proceso(`'.$proceso_id.'`)" class="btn btn-primary btn-shadow btn-gradient btn-sm btn-pill">Agregar <i class="fa fa-plus-circle"></i></button>
                                    </div>
                                </div>
                                <div id="div_actos_administrativos" class="panel-body"> 
                                    
                                </div>
                            </div>
                        </div>
                        <div id="div_respuestas_actos_administrativos" class="col-lg-7">
                            
                        </div>
                    </div>';
            
            
            print($html);
        break; //Fin caso 3
    
        case 4://dibuja los usuarios de una empresa
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $sql="SELECT TipoUser,Role FROM usuarios WHERE ID='$idUser'";
            $DatosUsuario=$obCon->Query($sql);
            $DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
            $TipoUser=$DatosUsuario["TipoUser"];
            $Role=$DatosUsuario["Role"];            
                       
            $css->select("usuario_proceso", "form-control btn-pill", "usuario_proceso", "", "", "onchange=dibujeListadoSegunID();", "title='Usuario que tiene asignado el proceso'");
                $mostar_todos=0;
                if($Role=="SUPERVISOR" or $Role=="ADMINISTRADOR"){
                    $mostar_todos=1;
                    $sql="SELECT t1.ID,t1.nombre_completo,t1.Identificacion FROM usuarios t1 
                            INNER JOIN usuarios_rel_empresas t2 ON t2.usuario_id_relacion=t1.ID 
                            WHERE t1.Habilitado='SI' and t2.empresa_id='$empresa_id'";
                }else{
                    $sql="SELECT ID,nombre_completo,identificacion FROM usuarios WHERE ID='$idUser'";
                }

                $Consulta=$obCon->Query($sql);
                if($mostar_todos==1){
                    $css->option("", "", "", '', "", "");
                        print("Todos los usuarios");
                    $css->Coption();
                }
                while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                    $css->option("", "", "", $DatosConsulta["ID"], "", "");
                        print($DatosConsulta["nombre_completo"]." ".$DatosConsulta["Identificacion"]);
                    $css->Coption();
                }
            $css->Cselect();
            
        break; //Fin caso 4
        
        case 5://dibuja los actos administrativos de un proceso
        
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $proceso_id=$obCon->normalizar($_REQUEST["proceso_id"]);
            
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
             
            print('<div class="ticket-list">');
                $sql="SELECT * FROM $db.vista_actos_administrativos_procesos WHERE proceso_id='$proceso_id' ORDER BY dias_plazo DESC,estado ASC,fecha_notificacion ASC";
            
                $Consulta=$obCon->Query($sql);
                while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                    $color_circulo="primary";
                    if($datos_consulta["dias_plazo"]==''){
                        $color_circulo="primary";
                    }
                    if($datos_consulta["dias_plazo"]<='7' and $datos_consulta["dias_plazo"]<>''){
                        $color_circulo="danger";
                    }
                    if($datos_consulta["dias_plazo"]>='8' and $datos_consulta["dias_plazo"]<='15'){
                        $color_circulo="warning";
                    }
                    if($datos_consulta["estado"]==2){
                        $color_circulo="success";
                    }
                    if($datos_consulta["estado"]==3){
                        $color_circulo="dark";
                    }
                    if(strlen($datos_consulta["observaciones"])>200){
                        $observaciones=substr($datos_consulta["observaciones"],0,200)."...";
                    }else{
                        $observaciones=$datos_consulta["observaciones"];
                    }
                    print('<div onclick="ver_respuestas_actos_administrativos(`'.$datos_consulta["acto_id"].'`)" class="list">
                                <div class="tbl-cell icon"><i id="div_circulo_acto_'.$datos_consulta["acto_id"].'" class="bg-'.$color_circulo.'"></i></div>
                                <div class="tbl-cell content">
                                    <h4>'.$datos_consulta["ID"].'. '.$datos_consulta["nombre_acto_administrativo"].' <span id="sp_estado_acto_'.$datos_consulta["acto_id"].'" class="status text-'.$datos_consulta["color_estado"].'">'.$datos_consulta["nombre_estado"].'</span></h4>
                                    <p title="'.$datos_consulta["observaciones"].'">'.$observaciones.'</p>
                                </div>');
                    
                                if($datos_consulta["dias_plazo"]<>''){
                                    print(' <div id="div_dias_plazo_acto_'.$datos_consulta["acto_id"].'" class="tbl-cell date" style="color:red"><strong>'.$datos_consulta["dias_plazo"].' Días para Responder</strong></div>');
                                }
                                
                                        
                           print('            
                            </div> <div class="list"> <ul class="comment-action">
                                                            <li><a onclick="frm_agregar_editar_acto_proceso(`'.$datos_consulta["proceso_id"].'`,`'.$datos_consulta["acto_id"].'`)"><i class="fa fa-eye text-success"></i>Ver</a></li>
                                                           
                                                            <li><a onclick="frm_agregar_editar_acto_proceso_respuesta(`'.$datos_consulta["proceso_id"].'`,`'.$datos_consulta["acto_id"].'`)"><i class="fa fa-reply text-primary"></i>Responder</a></li>
                                                            <li><a onclick="ver_respuestas_actos_administrativos(`'.$datos_consulta["acto_id"].'`)"><i class="far fa-comments text-flickr"></i> Respuestas</a></li>
                                                        </ul> </div> ');
                }
                
            print('</div>');
            
        break;//Fin caso 5  
    
    
        case 6://Agregar o editar un acto administrativo de un proceso
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $proceso_id=$obCon->normalizar($_REQUEST["proceso_id"]);
            $acto_id=$obCon->normalizar($_REQUEST["acto_id"]);
            $datos_acto=$obCon->DevuelveValores("$db.procesos_juridicos_actos_administrativos", "acto_id", $acto_id);
            $datos_proceso=$obCon->DevuelveValores("$db.procesos_juridicos", "proceso_id", $proceso_id);
            
            if($acto_id==''){
                $acto_id=$obCon->getUniqId("paa_");
            }
            $titulo="Crear un Acto Administrativo para este proceso";
            if($datos_acto["ID"]<>''){
                $titulo="Editar Acto Administrativo: ".$datos_acto["ID"];
            }
            
            $css->input("hidden", "formulario_id", "", "formulario_id", "", 1, "", "", "", "");
            $css->input("hidden", "acto_id", "", "acto_id", "", $acto_id, "", "", "", "");
            $css->input("hidden", "proceso_id", "", "proceso_id", "", $proceso_id, "", "", "", "");
            
            print('<div class="col-12">
                            <div class="panel">
                                <div class="panel-head">
                                    <h5 class="panel-title">'.$titulo.'</h5>
                                </div>
                                <div class="panel-body">');
                                        
            print('<div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Entidad: <i class="fa fa-industry text-flickr"></i></label>
                                                    <div class="col-12">');
                                                        
                                                $css->select("entidad_id", "form-control", "entidad_id", "", "", "", "");
                                                    $css->option("", "", "", "", "", "");
                                                        print("Seleccione una entidad");
                                                    $css->Coption();
                                                    if($datos_acto["entidad_id"]>0){
                                                        $datos_entidad=$obCon->DevuelveValores("$db.repositorio_juridico_entidades", "ID", $datos_acto["entidad_id"]);
                                                        $css->option("", "", "", $datos_entidad["ID"], "", "",1);
                                                            print($datos_entidad["nombre_entidad"]." ".$datos_entidad["nit"]);
                                                        $css->Coption();
                                                    }
                                                    
                                                $css->Cselect();
                                        print('</div>
                                                </div>
                                            </div>
                                            
                                        </div>'); 
            
            print( '                   <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Fecha del Acto <i class="far fa-calendar-alt text-flickr"></i></label>
                                                    <div class="col-12">
                                                        <input id="fecha_acto" name="fecha_acto" class="form-control" type="date" value="'.$datos_acto["fecha_acto"].'">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Fecha de Notificación <i class="far fa-calendar-alt text-flickr"></i></label>
                                                    <div class="col-12">
                                                        <input id="fecha_notificacion" name="fecha_notificacion" class="form-control" type="date" value="'.$datos_acto["fecha_notificacion"].'">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Tipo del Acto <i class="far fa-clipboard text-flickr"></i></label>
                                                    <div class="col-12">');
            
                                                $css->select("acto_tipo_id", "form-control", "acto_tipo_id", "", "", "", "");
                                                    $css->option("", "", "", "", "", "");
                                                        print("Seleccione un tipo de Acto");
                                                    $css->Coption();
                                                    $tipo_proceso=$datos_proceso["tipo_proceso_id"];
                                                    $sql="SELECT * FROM $db.procesos_juridicos_actos_tipo WHERE proceso_tipo_id='$tipo_proceso' AND oficio_respuesta=1";
                                                    $Consulta=$obCon->Query($sql);
                                                    while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                        $sel=0;
                                                        if($datos_consulta["ID"]==$datos_acto["acto_tipo_id"]){
                                                            $sel=1;
                                                        }
                                                        $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                            print($datos_consulta["acto_administrativo"]);
                                                        $css->Coption();
                                                    }
                                                $css->Cselect();
                                                        
                                        print('    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Número Acto <i class="fa fa-bookmark text-flickr"></i></label>
                                                    <div class="col-12">
                                                        <input id="numero_acto" name="numero_acto" class="form-control" type="text" value="'.$datos_acto["numero_acto"].'" placeholder="Número de Acto">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Observaciones <i class="far fa-comment-alt text-success"></i></label>
                                            <div class="col-12">
                                                <textarea id="observaciones" name="observaciones" class="form-control" rows="5">'.$datos_acto["observaciones"].'</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Adjuntar <i class="fa fa-paperclip text-primary" ></i></label>
                                                        <div class="panel">                            
                                                            <div class="panel-body">
                                                                <form data-acto_id="'.$acto_id.'" action="/" class="dropzone dz-clickable" id="acto_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Archivos Adjuntados <i class="fa fa-paperclip text-success" ></i></label>
                                                        <div id="div_adjuntos_actos">                            

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ');
                                        
                                        print('
                                   
                                </div>
                                
                            </div>
                        </div>');
            
        break;// fin caso 6   
        
        case 7:// dibuje las respuestas de los actos administrativos
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            
            $acto_id=$obCon->normalizar($_REQUEST["acto_id"]);
            $datos_acto=$obCon->DevuelveValores("$db.vista_actos_administrativos_procesos", "acto_id", $acto_id);
            $proceso_id=$datos_acto["proceso_id"];
            $datos_proceso=$obCon->DevuelveValores("$db.procesos_juridicos", "proceso_id", $proceso_id);
            
            
            
            print('<div class="panel panel-default">
                                <div class="panel-head">
                                    <div class="panel-title">
                                        <div class="panel-title-text">Respuestas al Acto '.$datos_acto["nombre_acto_administrativo"].' No. '.$datos_acto["ID"].'</div>
                                    </div>
                                </div>
                                <div class="panel-body"> 
                                    <div class="ticket-list">');
            
                            $sql="SELECT t1.*,
                                    
                                    (SELECT t2.acto_administrativo FROM $db.procesos_juridicos_actos_tipo t2 WHERE t2.ID=t1.acto_tipo_id LIMIT 1) AS nombre_acto_administrativo 
                                    FROM $db.procesos_juridicos_actos_administrativos_respuestas t1 WHERE acto_id='$acto_id' ORDER BY fecha_radicado ASC";
                            
                            $Consulta1=$obCon->Query($sql);
                            while($datos_consulta_respuestas=$obCon->FetchAssoc($Consulta1)){
                                if(strlen($datos_consulta_respuestas["observaciones"])>200){
                                    $observaciones=substr($datos_consulta_respuestas["observaciones"],0,200)."...";
                                }else{
                                    $observaciones=$datos_consulta_respuestas["observaciones"];
                                }
                                $text_to_modal="<textarea style='width:100%;height:400px;'>".$datos_consulta_respuestas["observaciones"].'</textarea>';
                                $respuesta_id=$datos_consulta_respuestas["respuesta_id"];
                                print('<div class="list">
                                            <div class="tbl-cell icon"><i class="icon-star"></i></div>
                                            <div class="tbl-cell content">
                                                <h4>'.$datos_consulta_respuestas["nombre_acto_administrativo"].'</h4>
                                                <p title="'.$datos_consulta_respuestas["observaciones"].'" onclick="ver_texto_en_modal(`'.$text_to_modal.'`)">'.$observaciones.'</p>
                                                <div class="date text-left text-primary">'.$datos_consulta_respuestas["fecha_radicado"].'</div>
                                                <div class="date text-left text-success">Adjuntos:</div>');
                                

                                    $sql="SELECT t1.*
                                            FROM $db.procesos_juridicos_acto_admin_respuestas_adjuntos t1 
                                            WHERE t1.respuesta_id='$respuesta_id' 
                                                ";
                                    $Consulta=$obCon->Query($sql);
                                    $css->CrearTabla();
                                    while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                                        $idItem=$DatosConsulta["ID"];
                                        $Nombre=$DatosConsulta["NombreArchivo"];
                                        $Ruta= "../../".str_replace("../", "", $DatosConsulta["Ruta"]);
                                        $css->FilaTabla(12);
                                            print("<td>");
                                                print('<div class="text-left text-primary">* <a href="'.$Ruta.'" target="blank">'.$Nombre.' <li class="fa fa-paperclip"></li></a></div>');
                                            print("</td>");
                                        $css->CierraFilaTabla();
                                    }
                                    $css->CerrarTabla();
                                
                                print('
                                            </div>
                                        </div>');
                            }                     
                                         
                        print('</div>
                                </div>
                            </div>');
            
        break;//Fin caso 7    
        
        case 8://Agregar o editar una respuesta a un acto administrativo de un proceso
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $respuesta_id=$obCon->normalizar($_REQUEST["respuesta_id"]);
            $datos_respuesta=$obCon->DevuelveValores("$db.procesos_juridicos_actos_administrativos_respuestas", "respuesta_id", $respuesta_id);
            $acto_id=$obCon->normalizar($_REQUEST["acto_id"]);
            $datos_acto=$obCon->DevuelveValores("$db.vista_actos_administrativos_procesos", "acto_id", $acto_id);
            $proceso_id=$datos_acto["proceso_id"];
            $datos_proceso=$obCon->DevuelveValores("$db.procesos_juridicos", "proceso_id", $proceso_id);
            
            if($respuesta_id==''){
                $respuesta_id=$obCon->getUniqId("raa_");
            }
            $titulo="Crear una respuesta al Acto Administrativo ".$datos_acto["nombre_acto_administrativo"]." No. ".$datos_acto["ID"];
            if($datos_respuesta["ID"]<>''){
                $titulo="Editar la respuesta No. ".$datos_respuesta["ID"]." del Acto Administrativo ".$datos_acto["nombre_acto_administrativo"]." No. ".$datos_acto["ID"];
            }
            
            $css->input("hidden", "formulario_id", "", "formulario_id", "", 2, "", "", "", "");
            $css->input("hidden", "acto_id", "", "acto_id", "", $acto_id, "", "", "", "");
            $css->input("hidden", "proceso_id", "", "proceso_id", "", $proceso_id, "", "", "", "");
            $css->input("hidden", "respuesta_id", "", "respuesta_id", "", $respuesta_id, "", "", "", "");
            
            print('<div class="col-12">
                            <div class="panel">
                                <div class="panel-head">
                                    <h5 class="panel-title">'.$titulo.'</h5>
                                </div>
                                <div class="panel-body">');
                                        
            
            
            print( '                   <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Fecha del Radicado <i class="far fa-calendar-alt text-flickr"></i></label>
                                                    <div class="col-12">
                                                        <input id="fecha_radicado" name="fecha_radicado" class="form-control" type="date" value="'.$datos_respuesta["fecha_radicado"].'">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Tipo del Acto <i class="far fa-clipboard text-flickr"></i></label>
                                                    <div class="col-12">');
            
                                                $css->select("acto_tipo_id", "form-control", "acto_tipo_id", "", "", "", "");
                                                    $css->option("", "", "", "", "", "");
                                                        print("Seleccione un tipo de Acto");
                                                    $css->Coption();
                                                    $tipo_proceso=$datos_proceso["tipo_proceso_id"];
                                                    $sql="SELECT * FROM $db.procesos_juridicos_actos_tipo WHERE oficio_respuesta=2 order by acto_administrativo asc";
                                                    $Consulta=$obCon->Query($sql);
                                                    while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                                        $sel=0;
                                                        if($datos_consulta["ID"]==$datos_respuesta["acto_tipo_id"]){
                                                            $sel=1;
                                                        }
                                                        $css->option("", "", "", $datos_consulta["ID"], "", "",$sel);
                                                            print($datos_consulta["acto_administrativo"]);
                                                        $css->Coption();
                                                    }
                                                $css->Cselect();
                                                        
                                        print('    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Número Acto <i class="fa fa-bookmark text-flickr"></i></label>
                                                    <div class="col-12">
                                                        <input id="numero_acto" name="numero_acto" class="form-control" type="text" value="'.$datos_respuesta["numero_acto"].'" placeholder="Número de Acto">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Observaciones <i class="far fa-comment-alt text-success"></i></label>
                                            <div class="col-12">
                                                <textarea id="observaciones" name="observaciones" class="form-control" rows="5">'.$datos_respuesta["observaciones"].'</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Adjuntar <i class="fa fa-paperclip text-primary" ></i></label>
                                                        <div class="panel">                            
                                                            <div class="panel-body">
                                                                <form data-respuesta_id="'.$respuesta_id.'" action="/" class="dropzone dz-clickable" id="respuesta_adjuntos"><div class="dz-default dz-message"><span><i class="icon-plus"></i>Arrastre archivos aquí o de click para subir.<br> Suba cualquier tipo de archivos.</span></div></form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Archivos Adjuntados <i class="fa fa-paperclip text-success" ></i></label>
                                                        <div id="div_adjuntos_respuestas">                            

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ');
                                        
                                        print('
                                   
                                </div>
                                
                            </div>
                        </div>');
            
        break;// fin caso 8
        
        case 9: //Dibuja los adjuntos de las respuestas de un acto administrativo
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $respuesta_id=$obCon->normalizar($_REQUEST["respuesta_id"]);
            
            $DatosEmpresa=$obCon->ValorActual("empresapro", "db", " ID='$empresa_id'");
            $db=$DatosEmpresa["db"];
            
            
            $css->CrearTabla();
                
                $css->FilaTabla(16);
                
                    $css->ColTabla("ID", 1);
                    $css->ColTabla("Nombre de Archivo", 1);
                    
                    $css->ColTabla("Eliminar", 1);
                    
                $css->CierraFilaTabla();
                
                $sql="SELECT t1.*
                        FROM $db.procesos_juridicos_acto_admin_respuestas_adjuntos t1 
                        WHERE t1.respuesta_id='$respuesta_id' 
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
                            
                            $css->li("", "far fa-trash-alt", "", "onclick=EliminarItem(`2`,`$idItem`,`$respuesta_id`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                          
                    $css->CierraFilaTabla();
                }
            $css->CerrarTabla();
            
            
        break; //Fin caso 9
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>