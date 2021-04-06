<?php
/**
 * Pagina para administrar los tickets
 * 2021-03-04, Julian Alvaran Techno Soluciones SAS
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
$Pagina=end($urlRequest);

$myPage=$Pagina;
$myTitulo="Tickets";
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
    
    $css->Modal("modal_view", "TS6", "", 1, 0, 1);
        $css->div("div_modal_view", "col-md-12", "", "", "", "", "");
        
        $css->Cdiv();
        
    $css->CModal("btnModalView", "onclick=SeleccioneAccionFormularios();", "button", "Enviar");
    $css->div("div_spinner", "", "", "", "", "", "");
    
    $css->Cdiv();
    
    $css->div("", "row", "", "", "", "", "");
        $css->div("", "col-lg-12", "", "", "", "", "");
            $css->div("", "panel panel-default", "", "", "", "", "");
                $css->div("", "mailbox-container", "", "", "", "", "");
                    $css->div("", "action", "", "", "", "", 'style="height:80px;"');
                        $css->div("", "btn-group col-md-3", "", "", "", "", '');
                            $css->select("empresa_id", "btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient", "empresa_id", "", "", "onchange=dibuje_menu_lateral_tickets();", "");
                            if($TipoUser=="administrador"){
                                $sql="SELECT * FROM empresapro WHERE Estado=1";
                            }else{
                                $sql="SELECT t1.* FROM empresapro t1 WHERE t1.Estado=1 AND EXISTS (SELECT 1 FROM usuarios_rel_empresas t2 WHERE t2.usuario_id_relacion='$idUser' AND t2.empresa_id=t1.ID) ";
                            }
                            $Consulta=$obCon->Query($sql);
                            while($DatosConsulta=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "", "", $DatosConsulta["ID"], "", "");
                                    print($DatosConsulta["RazonSocial"]);
                                $css->Coption();
                            }
                            $css->Cselect();
                        $css->Cdiv();
                        $css->div("", "btn-group col-md-3", "", "", "", "", '');
            
                        $css->Cdiv();
                        if($Role=='SUPERVISOR' or $Role=='ADMINISTRADOR' ){
                            print('<div id="" class="btn-group pull-right" role="">
                                            
                                            
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline btn-default btn-pill btn-outline-1x btn-gradient dropdown-toggle tippy" data-tippy-animation="perspective" data-tippy-arrow="true" data-tippy-size="large" data-toggle="dropdown" data-tippy="" data-original-title="Labels" aria-expanded="false"><i class="fa fa-cogs"></i></button>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="top-end" style="position: absolute; transform: translate3d(-93px, -189px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" onclick=listado_tickets_departamento()>Departamentos</a>
                                                    
                                                    <a class="dropdown-item" onclick=listado_tickets_tipos()>Tipos de Tickets</a>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    ');
                            
                        }
                        $css->Cdiv();
                    $css->Cdiv();
                $css->Cdiv();
        $css->Cdiv();
    $css->Cdiv();
    
    $css->div("", "row", "", "", "", "", "");
        $css->div("", "col-lg-3", "", "", "", "", "");
            $css->div("", "panel panel-default", "", "", "", "", "");
                $css->div("div_panel_body", "panel-body", "", "", "", "", "");
            
                $css->Cdiv();
            $css->Cdiv();
        $css->Cdiv();
        
        $css->div("", "col-lg-9", "", "", "", "", "");
            
            $css->div("DivDrawTickets", "body", "", "", "", "", 'style="background-color:#ffffff"');
                    
            $css->Cdiv();
        $css->Cdiv();
        
    $css->Cdiv();
      
$css->PageFin();
$css->agregar_summernote();
print('<script src="../../general/js/tablas.js"></script>'); 
print('<script src="jsPages/tickets.js"></script>'); 

$css->Cbody();
$css->Chtml();

?>