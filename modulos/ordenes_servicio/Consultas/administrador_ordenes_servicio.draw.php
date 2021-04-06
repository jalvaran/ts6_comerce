<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //Dibuja el listado de usuarios asociados a la empresa seleccionada
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $TipoUser=$_SESSION["tipouser"];
            $Busqueda=$obCon->normalizar($_REQUEST["Busqueda"]);
            
            $Condicional=" WHERE t2.empresa_id='$empresa_id'  ";
            $OrderBy=" ORDER BY t1.ID DESC";            
            
            //Paginacion
            if(isset($_REQUEST['Page'])){
                $NumPage=$obCon->normalizar($_REQUEST['Page']);
            }else{
                $NumPage=1;
            }
            
            if(isset($_REQUEST['Busqueda'])){
                $Busqueda=$obCon->normalizar($_REQUEST['Busqueda']);
                if($Busqueda<>''){
                    $Condicional.=" AND ( t1.ID='$Busqueda' or t1.Identificacion = '$Busqueda' or t1.nombre_completo like '%$Busqueda%' )";
                    
                }
                
            }
            
            $statement=" `usuarios` t1 INNER JOIN usuarios_rel_empresas t2 ON t1.ID=t2.usuario_id_relacion $Condicional ";
            
            if(isset($_REQUEST['st'])){

                $statement= urldecode($_REQUEST['st']);
                //print($statement);
            }
            
            $limit = 15;
            $startpoint = ($NumPage * $limit) - $limit;
            
            $VectorST = explode("LIMIT", $statement);
            $statement = $VectorST[0]; 
            $query = "SELECT COUNT(*) as `num` FROM {$statement}";
            $row = $obCon->FetchArray($obCon->Query($query));
            $ResultadosTotales = $row['num'];
            
            $st_reporte=$statement;
            $Limit=" LIMIT $startpoint,$limit";
            
            $query="SELECT t1.ID,t1.nombre_completo,t1.Identificacion,t1.created,t1.Updated,t1.Habilitado,t1.Login  ";
            $Consulta=$obCon->Query("$query FROM $statement $OrderBy $Limit ");
            $TotalPaginas= ceil($ResultadosTotales/$limit);
            
            print('<div class="panel panel-default">
                                <div class="mailbox-container">
                                    <div class="action">
                                                                                
                                        <div class="btn-group pull-center">
                                            
                                            <strong>'.$ResultadosTotales.' Usuarios</strong>
                                        </div>
                                        
                                        <div class="btn-group pull-right" style="top: -10px;"><button class="btn btn-primary btn-gradient btn-pill m-1" onclick="frm_crear_editar_usuario_os();">Agregar <li class="fa fa-plus-circle"></li></button>');
                                            
                                        
                            if($TotalPaginas==0){
                                $TotalPaginas=1;
                            }
                            if($NumPage>1){
                                $goPage=$NumPage-1;  
                                print('<button onclick="listado_usuarios('.$goPage.')" type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient"><i style="font-size:20px;height:15px;" class="far fa-arrow-alt-circle-left text-flickr"></i></button>');
                            }        
                            if($NumPage<>$TotalPaginas){  
                                $goPage=$NumPage+1;
                                print('<button onclick="listado_usuarios('.$goPage.')" type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient"><i style="font-size:20px;height:15px;" class="far fa-arrow-alt-circle-right text-success"></i></button>');
                                        
                            }                        
                                                    
                              print('   </div>
                                        
                                    </div>
                                    <div class="body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mail-table">
                                                <thead>
                                                    <tr>
                                                        <td><strong>Editar</strong></td>
                                                        <td><strong>Nombre</strong></td>
                                                        <td><strong>Identificación</strong></td>
                                                        <td><strong>Login</strong></td>
                                                        <td><strong>Habilitado</strong></td>
                                                        <td><strong>Creado</strong></td>
                                                        <td><strong>Actualizado</strong></td>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                              
                                        while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                                            $ID=$datos_consulta["ID"];
                                            
                                            print('<tr >
                                                        
                                                        <td class="starred-icon ">
                                                            <a class="active text-warning" style="cursor:pointer" onclick="frm_crear_editar_usuario_os(`'.$ID.'`)"><i class="fa fa-edit"></i></a>
                                                        </td>
                                                        
                                                        <td>
                                                            <span class="name">'.$datos_consulta["nombre_completo"].'</span>
                                                            
                                                        </td>
                                                        
                                                        <td class="date">'.$datos_consulta["Identificacion"].'</td>                                                      

                                                        <td class="date text-primary">'.$datos_consulta["Login"].'</td>
                                                        <td class="date text-primary">'.$datos_consulta["Habilitado"].'</td>
                                                        <td class="date">'.$datos_consulta["created"].'</td>
                                                        <td class="date">'.$datos_consulta["Updated"].'</td>
                                                    </tr>
                                                    ');
                                            
                                        }    
                              
                                print('         </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>');
            
                        
        break; //Fin caso 1
        
        case 2: //Formulario para crear o editar un usuario
            
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $usuario_id=$obCon->normalizar($_REQUEST["usuario_id"]);
            
            $datos_usuario=$obCon->DevuelveValores("usuarios", "ID", $usuario_id);
            
            print('<div class="col-12">
                            <div class="panel panel-default">
                                <div class="panel-head">
                                    <div class="panel-title">
                                        <span class="panel-title-text">Crear o editar un Usuario</span>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    
                                        <div class="form-body">
                                            <div class="form-heading">Información Personal</div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Nombres</label>
                                                        <input id="nombre_usuario_os" name="nombre_usuario_os" value="'.$datos_usuario["Nombre"].'" type="text" class="form-control" placeholder="Nombres">
                                                        <span class="form-text">Por favor digite el nombre</span> 
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Apellidos</label>
                                                        <input id="apellido_usuario_os" name="apellido_usuario_os" value="'.$datos_usuario["Apellido"].'" type="text" class="form-control" placeholder="Apellido">
                                                        <span class="form-text">Por favor digite el apellido</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Email</label>
                                                        <input id="email_usuario_os" name="email_usuario_os" value="'.$datos_usuario["Email"].'" type="text" class="form-control" placeholder="Email">
                                                        <span class="form-text">Por favor digite el Email</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Identificación</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">I</span>
                                                            </div>
                                                            <input value="'.$datos_usuario["Identificacion"].'" id="identificacion_usuario_os" name="identificacion_usuario_os" type="text" class="form-control" placeholder="Identificación">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Login</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">@</span>
                                                            </div>
                                                            <input id="login_usuario_os" value="'.$datos_usuario["Login"].'" name="login_usuario_os" type="text" class="form-control" placeholder="Login">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Contraseña</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">P</span>
                                                            </div>
                                                            <input id="password_usuario_os" value="'.$datos_usuario["Password"].'" name="password_usuario_os" type="password" class="form-control" placeholder="Password">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="col-form-label">Habilitado</label>
                                                        ');
            
                                            $css->select("cmb_habilitado", "form-control", "cmb_habilitado", "", "", "", "");            
                                                $sel=0;
                                                if($datos_usuario["Habilitado"]=="SI"){
                                                    $sel=1;
                                                }
                                                $css->option("", "", "", "SI", "", "",$sel);
                                                    print("SI");
                                                $css->Coption();
                                                $sel=0;
                                                if($datos_usuario["Habilitado"]=="NO"){
                                                    $sel=1;
                                                }
                                                $css->option("", "", "", "NO", "", "",$sel);
                                                    print("NO");
                                                $css->Coption();
                                            $css->Cselect();
                                            
                                            print('    
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                            
                                            
                                        </div>
                                   
                                </div>
                                <div class="panel-footer text-right">
                                    <button class="btn btn-default btn-pill mr-2" onclick=VerListadoSegunID();>Cancelar</button>
                                    <button id="btn_guardar" data-usuario_id="'.$usuario_id.'" class="btn btn-primary m-1" onclick="crear_editar_usuario_os()">Enviar</button>
                                </div>
                            </div>
                        </div>');
                
                
            
        break;//Fin caso 2
        
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>