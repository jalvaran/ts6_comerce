<?php
/**
 * Pagina para realizar facturas electrónicas libres
 * 2020-10-06, Julian Alvaran Techno Soluciones SAS
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
$myTitulo="Facturador Electrónico";
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
        
    $css->CModal("btnModalView", "", "", "Enviar");
    $css->div("div_spinner", "", "", "", "", "", "");
    
    $css->Cdiv();
    
    $css->div("", "row", "", "", "", "", "");
        
        print('<div class="col-lg-12">
                <div class="panel panel-dark">
                    <div class="panel-head">
                        <div class="panel-title">
                            
                            <i class="fa fa-warehouse panel-head-icon font-24"></i>
                            <span class="panel-title-text">Facturador: </span>  
                            <div class="row">
                            <div class="col-md-4">
                                ');
        $css->select("empresa_id", "form-control btn-pill", "empresa_id", "", "", "onchange=formulario_facturador();actualizar_contadores();", "");
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
        
        $css->CrearBotonEvento("btnFacturar", "Hacer un Documento Electrónico", 1, "onclick", "formulario_facturador()", "azul");
        print('</div>');
        $html=$css->getHtmlPanelInfo("Terceros", 0, "sp_terceros", 2, "icon-people", "onclick=dibuja_tabla(`get`,`terceros`,`1`,`DivListados`)", "style=cursor:pointer", "primary", 1, "p_terceros");
        $html.=$css->getHtmlPanelInfo("Items", 0, "sp_inventario_items", 2, "icon-layers", "onclick=dibuja_tabla(`get`,`inventario_items_general`,`1`,`DivListados`)", "style=cursor:pointer", "warning", 1, "p_inventario_items");
        
        $html.=$css->getHtmlPanelInfo("Enviados", 0, "sp_documentos_enviados", 2, "fab fa-telegram-plane", "onclick=listado_id=1;dibujeListadoSegunID();evento_busqueda()", "style=cursor:pointer", "success", 1, "p_enviados");
        $html.=$css->getHtmlPanelInfo("Errores", 0, "sp_errores", 2, "fa fa-exclamation-triangle", "onclick=listado_id=2;dibujeListadoSegunID();evento_busqueda()", "style=cursor:pointer", "flickr", 1, "p_errores");
        
        
        print($html);
        print('</div>');
        print('</div>');
        print('</div>');
        print('     <div class="panel-wrapper">
                        <div class="panel-body">
                            <div id="DivListados">

                            </div>
                        </div>
                    </div>
                  ');
    
    $css->Cdiv();
    $css->Cdiv();
    $css->Cdiv();
$css->PageFin();
print('<script src="jsPages/facturador.js"></script>'); 
print('<script src="../../general/js/tablas.js"></script>'); 
$css->Cbody();
$css->Chtml();

?>