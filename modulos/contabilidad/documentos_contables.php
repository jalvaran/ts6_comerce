<?php
/**
 * Pagina para realizar documentos contables
 * 2020-10-15, Julian Alvaran Techno Soluciones SAS
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
$myTitulo="Documentos Contables";
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
    $css->div("div_spinner", "", "", "", "", "", "");
    $css->Modal("modal_view", "TS6", "", 1, 0, 1);
        $css->div("div_modal_view", "col-md-12", "", "", "", "", "");
        
        $css->Cdiv();
        
    $css->CModal("btnModalView", "", "", "Enviar");
    
    
    $css->Cdiv();
    
    $css->div("", "row", "", "", "", "", "");
        
        print('<div class="col-lg-12">
                <div class="panel panel-dark">
                    <div class="panel-head">
                        <div class="panel-title">
                            
                            <i class="fa fa-warehouse panel-head-icon font-24"></i>
                            <span class="panel-title-text">Empresa: </span>  
                            <div class="row">
                            <div class="col-md-4">
                                ');
        $css->select("empresa_id", "form-control btn-pill", "empresa_id", "", "", "onchange=formulario_documento_contable();", "");
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
        
        $css->CrearBotonEvento("btnFacturar", "Hacer un Documento Contable", 1, "onclick", "formulario_documento_contable()", "azul");
        print('</div>');
        $html=$css->getHtmlPanelInfo("Terceros", 0, "sp_terceros", 2, "icon-people", "onclick=dibuja_tabla(`get`,`terceros`,`1`,`DivListados`)", "style=cursor:pointer", "primary", 1, "p_terceros");
        $html.=$css->getHtmlPanelInfo("Cuentas", 0, "sp_cuentas_contables", 2, "icon-layers", "onclick=dibuja_tabla(`get`,`contabilidad_plan_cuentas_subcuentas`,`1`,`DivListados`)", "style=cursor:pointer", "warning", 1, "p_cuentas_contables");
        $html.=$css->getHtmlPanelInfo("Documentos", 0, "sp_documentos", 2, "icon-docs", "onclick=dibuja_tabla(`get`,`contabilidad_documentos_contables`,`1`,`DivListados`)", "style=cursor:pointer", "success", 1, "p_documentos");
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
print('<script src="jsPages/documentos_contables.js"></script>'); 
print('<script src="../../general/js/tablas.js"></script>'); 
$css->Cbody();
$css->Chtml();

?>