<?php
/**
 * Pagina para administrar los usuarios
 * 2020-10-13, Julian Alvaran Techno Soluciones SAS
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
$myTitulo="Administrar Usuarios";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE ID='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$DatosUsuario["Role"];

$css->PageInit($myTitulo);
    $css->div("", "row", "", "", "", "", "");
        
        print('<div class="col-lg-12">
                <div class="panel panel-dark">
                    <div class="panel-head">
                        <div class="panel-title">
                            
                            <i class="far fa-building panel-head-icon font-24"></i>
                            <span class="panel-title-text">Lista de Usuarios Creados</span>
                        </div>
                        <div class="panel-action panel-action-background">
                            
                            
                            <button id="btnActualizarListado" class="btn btn-success btn-gradient btn-pill m-1" onclick=dibuja_tabla(`0`,`usuarios`,`1`,`DivListado`)><i class="fa fa-sync"></i></button>

                        </div>  
                    </div>
                    <div class="panel-wrapper">
                        <div class="panel-body">
                            <div id="DivListado">

                            </div>
                        </div>
                    </div>
                   </div> 
                </div>');
    
    $css->Cdiv();
        
$css->PageFin();
print('<script src="../../general/js/tablas.js"></script>'); 

$css->Cbody();
$css->Chtml();

?>

<script> dibuja_tabla(`0`,`usuarios`,`1`,`DivListado`);</script>