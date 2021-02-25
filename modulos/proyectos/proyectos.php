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
$myTitulo="Módulo de proyectos";
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
    
    $css->Modal("ModalAcciones", "TS6", "", 1, 0, 1);
        $css->div("DivFrmModalAcciones", "col-md-12", "", "", "", "", "");
        
        $css->Cdiv();
        
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Enviar");
    $css->div("div_spinner", "", "", "", "", "", "");
    
    $css->Cdiv();
    
    $css->div("", "row", "", "", "", "", "");
        
        print('<div class="col-lg-12">
                <div class="panel panel-dark">
                    <div class="panel-head">
                        <div class="panel-title">
                            
                            <i class="fa fa-warehouse panel-head-icon font-24"></i>
                            <span class="panel-title-text">Proyectos: </span>  
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
        
        $css->select("cmb_filtro_proyectos", "form-control btn-pill", "cmb_filtro_proyectos", "", "", "onchange=MostrarListadoSegunID();", "");
            $sql="SELECT * FROM proyectos_estados";
            
            $Consulta=$obCon->Query($sql);
            $css->option("", "", "", "", "", "");
                print("Todos los proyectos");
            $css->Coption();
            while($datos_consulta=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $datos_consulta["ID"], "", "");
                    print($datos_consulta["nombre_estado"]);
                $css->Coption();
            }
        $css->Cselect();
        
        print('</div>');
         
        print(' 
                    <div class="col-md-4">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text " style="font-size:14px;">Fecha inicial: </span>
                            </div>    
                                <input id="FechaInicialRangos" type="date" class="form-control" value="">


                        </div>

                            <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text " style="font-size:14px;">Fecha final: &nbsp;</span>
                            </div>    
                                <input id="FechaFinalRangos" type="date" class="form-control" value="">

                        </div>
                        </div>
                        <div class="panel-action panel-action-background" style="rigth:10px;" ;>
                        <button id="btnFrmNuevaEmpresa" title="Nuevo" class="btn btn-primary btn-gradient btn-pill m-1" onclick=frm_crear_editar_proyecto(``,`1`)>Crear <i class="fa fa-plus-circle"></i></button>
                        <button id="btnActualizar" title="Actualizar" class="btn btn-success btn-gradient btn-pill m-1" onclick=MostrarListadoSegunID()>Actualizar <i class="fa fa-sync"></i></button>
                        <button id="btnNuevoTercero" title="Crear Cliente" class="btn btn-warning btn-gradient btn-pill m-1" onclick="dibuja_tabla(`get`,`terceros`,`1`,`DivGeneralDraw`)">Tercero <i class="fa fa-plus-circle"></i></button>
                        </div>
                        
                                            
                    ');
        //print('</div>');
        
        print('</div>');
        print('</div>');
        print('</div>');
        print('     <div class="panel-wrapper">
                        <div class="panel-body">
                            <div id="DivGeneralDraw">

                            </div>
                        </div>
                    </div>
                  ');
    
    $css->Cdiv();
    $css->Cdiv();
    $css->Cdiv();
$css->PageFin();
print('<script src="../../general/js/tablas.js"></script>'); 
print('<script src="jsPages/proyectos.js"></script>'); 
$css->agregar_full_calendar();


$css->Cbody();
$css->Chtml();

?>