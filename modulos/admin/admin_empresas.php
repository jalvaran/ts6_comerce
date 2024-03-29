<?php
/**
 * Pagina para administrar la empresas
 * 2020-07-09, Julian Alvaran Techno Soluciones SAS
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
$myTitulo="Administrar empresas";
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
        $css->Modal("modal_view", "TS6", "", 1, 0, 1);
            $css->div("div_modal_view", "col-md-12", "", "", "", "", "");

            $css->Cdiv();

        $css->CModal("btnModalView", "", "", "Enviar");
        print('<div class="col-lg-12">
                <div class="panel panel-dark">
                    <div class="panel-head">
                        <div class="panel-title">
                            
                            <i class="far fa-building panel-head-icon font-24"></i>
                            <span class="panel-title-text">Lista de Empresas Creadas</span>
                        </div>
                        <div class="panel-action panel-action-background">
                            
                            <button id="btnFrmNuevaEmpresa" title="Nuevo" class="btn btn-primary btn-gradient btn-pill m-1">Crear <i class="fa fa-plus-circle"></i></button>
                            <button id="btnActualizarListado" title="Actualizar" class="btn btn-success btn-gradient btn-pill m-1"><i class="fa fa-sync"></i></button>
                            <button id="btnMigrations" title="Ejecutar migraciones" class="btn btn-flickr btn-gradient btn-pill m-1" onclick=ConfirmarMigracion()><i class="far fa-object-ungroup"></i></button>
                            
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
print('<script src="jsPages/admin_empresas.js"></script>');
print('<script src="jsPages/migrations.js"></script>');
$css->Cbody();
$css->Chtml();

?>