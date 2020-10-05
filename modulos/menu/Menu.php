<?php
/**
 * Pagina base para la plataforma TSS
 * 2018-11-27, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$Domain=$_SERVER['HTTP_HOST'];
$urlRequest=($_SERVER['SCRIPT_NAME']);
$urlRequest= explode("/", $urlRequest);
$VMenu=end($urlRequest);

$myPage=$VMenu;
$myTitulo="Plataforma TS6 pro";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$DatosUsuario["Role"];
$NombreMenu="Menú";
if(isset($_REQUEST["menu_id"])){
    $DatosMenu=$obCon->DevuelveValores("menu", "ID", $obCon->normalizar($_REQUEST["menu_id"]));
    $NombreMenu= ($DatosMenu["Nombre"]);
}
$css->PageInit($myTitulo);
    $Fecha=date("Y-m-d");
    print('<div class="container" style="text-align:center" >');
        
        print('<div class="panel panel-default">'); 
        
        print('<div class="panel-head">');
            print('<div class="panel-title">');
                print('<div class="panel-title-text">');
                    print($NombreMenu);
                print('</div>');
            print('</div>');
        print('</div>');
        print('<div class="panel-body">');
            if(!isset($_REQUEST["menu_id"])){
                print('<div class="row">');
                $sql="SELECT m.Nombre, m.Pagina,m.Target,m.Image,m.Orden, c.Ruta FROM menu m "
                . "INNER JOIN menu_carpetas c ON c.ID=m.idCarpeta WHERE m.Estado=1 ORDER BY m.Orden ASC";
                $Consulta=$obCon->Query($sql);
                    while($DatosMenu=$obCon->FetchArray($Consulta)){

                        if($DatosUsuario["TipoUser"]=="administrador"){
                            $Visible=1;
                        }else{
                            $Visible=0;
                            $sql="SELECT ID FROM paginas_bloques WHERE TipoUsuario='$TipoUser' AND Pagina='$DatosMenu[Pagina]' AND Habilitado='SI'";
                            $DatosUser=$obCon->Query($sql);
                            $DatosUser=$obCon->FetchArray($DatosUser);
                            if($DatosUser["ID"]>0){
                                $Visible=1;
                            }
                        }
                        if($Visible==1){
                            print('<div class="col-md-3" style="height:200px">');


                                print('
                                    <a class="gal" href="'.$DatosMenu["Ruta"].$DatosMenu["Pagina"].'" target="'.$DatosMenu["Target"].'"><img src="../../images/'.$DatosMenu["Image"].'" alt="" style="width: 120px;height: 120px;"><span></span></a>
                                    <span class="col3"><a href="'.$DatosMenu["Ruta"].$DatosMenu["Pagina"].'" target="'.$DatosMenu["Target"].'">'.($DatosMenu["Nombre"]).'</a></span>
                                  ');

                            print('</div>');
                            
                        }    
                    }
                print('</div>');
            }else{
                $menu_id=$obCon->normalizar($_REQUEST["menu_id"]);
                
                print('<ul class="nav nav-pills nav-justified nav-pills-blue">');
                    $sql="SELECT * FROM menu_pestanas WHERE idMenu='$menu_id' AND Estado='1'";
                    $Consulta=$obCon->Query($sql);
                    $a=1;
                    $s=0;
                    while($DatosPestanas=$obCon->FetchAssoc($Consulta)){
                        $s++;
                        if($a==1){
                            $a="active show";
                        }else{
                            $a="";
                        }
                        $idPestana=$DatosPestanas["ID"];
                        print('<li class="nav-item">
                                   <a class="nav-link '.$a.'" href="#Tab_'.$DatosPestanas["ID"].'" data-toggle="tab">'.$DatosPestanas["Nombre"].'</a>
                                </li>');
                        
                        $pestanas_ids[$s]=$idPestana;
                        
                        
                    }
                    
                print('</ul>');
                
                print('<div class="tab-content">');
                    $a=1;
                    if(isset($pestanas_ids)){
                    foreach ($pestanas_ids as $key => $idPestana) {
                        if($a==1){
                            $a="active show";
                        }else{
                            $a="";
                        }
                        $sql="SELECT t1.*,(SELECT Ruta FROM menu_carpetas t2 WHERE t2.ID=t1.idCarpeta) as Ruta FROM menu_submenus t1 WHERE idPestana='$idPestana' AND Estado='1' ORDER BY Orden ";
                        $ConsultaSub=$obCon->Query($sql);
                        print('<div class="tab-pane '.$a.'" id="Tab_'.$idPestana.'">');
                            print('<div class="row">');
                                while($DatosSubMenu=$obCon->FetchAssoc($ConsultaSub)){

                                    if($DatosUsuario["TipoUser"]=="administrador"){
                                        $Visible=1;
                                    }else{
                                        $Visible=0;
                                        $sql="SELECT ID FROM paginas_bloques WHERE TipoUsuario='$TipoUser' AND Pagina='$DatosSubMenu[Pagina]' AND Habilitado='SI'";
                                        $DatosUser=$obCon->Query($sql);
                                        $DatosUser=$obCon->FetchArray($DatosUser);
                                        if($DatosUser["ID"]>0){
                                            $Visible=1;
                                        }
                                    }
                                    if($Visible==1){
                                        print('<div class="col-lg-3" style="height:200px">');
                                            
                                          
                                            print('
                                                <a class="gal" href="'.$DatosSubMenu["Ruta"].$DatosSubMenu["Pagina"].'" target="'.$DatosSubMenu["Target"].'"><img src="../../images/'.$DatosSubMenu["Image"].'" alt="" style="width: 120px;height: 120px;"></img></a>
                                                <span class="col3"><a href="'.$DatosSubMenu["Ruta"].$DatosSubMenu["Pagina"].'" target="'.$DatosSubMenu["Target"].'">'.($DatosSubMenu["Nombre"]).'</a></span>
                                              ');
                                             

                                        print('</div>');
                                        

                                    }    
                                    
                                }
                            print('</div>');
                        
                        print('</div>');
                    }
                    }
                print('</div>');
            }

        
            $css->Cdiv();
       $css->Cdiv();
    $css->Cdiv();
        
$css->PageFin();


$ip=$_SERVER['REMOTE_ADDR'];
$ipServer=$_SERVER['SERVER_ADDR'];

$css->Cbody();
$css->Chtml();

?>