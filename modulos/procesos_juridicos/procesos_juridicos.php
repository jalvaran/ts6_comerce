<?php
/**
 * Pagina para administrar los procesos juridicos
 * 2021-02-24, Julian Alvaran Techno Soluciones SAS
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
$myTitulo="Procesos Jurídicos";
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
        
        print('<div class="col-lg-12">
                <div class="panel panel-dark">
                    <div class="panel-head">
                        <div class="panel-title">
                            
                            <i class="fa fa-warehouse panel-head-icon font-24"></i>
                            <span class="panel-title-text">Empresa: </span>  
                            <div class="row">
                            <div class="col-md-4">
                                ');
        $css->select("empresa_id", "form-control btn-pill", "empresa_id", "", "", "onchange=actualizar_contadores();dibujeListadoSegunID();", "");
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
        print('<button class="btn btn-success m-1" onclick="listado_id=1;dibujeListadoSegunID()">Ver Listado</button>');
        print('<button class="btn btn-primary m-1" onclick="frm_crear_editar_registro_proceso()">Crear Registro</button>');
        //$css->CrearBotonEvento("btnFacturar", "Hacer un Documento Electrónico", 1, "onclick", "formulario_facturador()", "azul");
        print('</div>');
        
        print('<div id="div_usuarios" class="col-md-3" style="text-align:center" >');
            
        
        
        print('</div>');
        
        print('<div class="dropdown d-inline-block m-1 col-md-4" style="text-align:right">
                                        <button class="btn btn-primary dropdown-toggle " data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cogs"></i> Catalogos </button>
                                        <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 44px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <li><a onclick=dibuja_tabla(`get`,`procesos_juridicos_temas`,`1`,`DivListados`)>Temas</a></li>
                                            <li class="dropdown-divider"></li>
                                            <li><a onclick=dibuja_tabla(`get`,`procesos_juridicos_sub_temas`,`1`,`DivListados`)>Subtemas</a></li>
                                            <li class="dropdown-divider"></li>
                                            <li><a onclick=dibuja_tabla(`get`,`procesos_juridicos_tipo`,`1`,`DivListados`)>Tipos de Procesos</a></li>
                                            <li class="dropdown-divider"></li>
                                            <li><a onclick=dibuja_tabla(`get`,`terceros`,`1`,`DivListados`)>Terceros</a></li>
                                            <li class="dropdown-divider"></li>
                                            <li><a onclick=dibuja_tabla(`get`,`procesos_juridicos_actos_tipo`,`1`,`DivListados`)>Tipos de Actos</a></li>
                                        </ul>
                                    </div>');
        
        
        /*
        $html=$css->getHtmlPanelInfo("Temas", 0, "sp_temas", 2, "far fa-object-group", "onclick=dibuja_tabla(`get`,`procesos_juridicos_temas`,`1`,`DivListados`)", "style=cursor:pointer", "primary", 1, "p_temas");
        $html.=$css->getHtmlPanelInfo("SubTemas", 0, "sp_sub_temas", 2, "far fa-object-ungroup", "onclick=dibuja_tabla(`get`,`procesos_juridicos_sub_temas`,`1`,`DivListados`)", "style=cursor:pointer", "warning", 1, "p_sub_temas");
        $html.=$css->getHtmlPanelInfo("Tipos", 0, "sp_tipo_procesos", 2, "far fa-clone", "onclick=dibuja_tabla(`get`,`procesos_juridicos_tipo`,`1`,`DivListados`)", "style=cursor:pointer", "success", 1, "p_tipo_procesos");
        $html.=$css->getHtmlPanelInfo("Terceros", 0, "sp_terceros", 2, "far fa-building", "onclick=dibuja_tabla(`get`,`terceros`,`1`,`DivListados`)", "style=cursor:pointer", "danger", 1, "p_terceros");
        print($html);
         * 
         */
        
        
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
print('<script src="../../general/js/tablas.js"></script>'); 
print('<script src="jsPages/procesos_juridicos.js"></script>'); 

$css->Cbody();
$css->Chtml();

?>