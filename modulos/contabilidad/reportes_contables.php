<?php
/**
 * Pagina para realizar los reportes financieros
 * 2020-11-15, Julian Alvaran Techno Soluciones SAS
 * 
 * 
 */
$Domain=$_SERVER['HTTP_HOST'];
$urlRequest=($_SERVER['SCRIPT_NAME']);
$urlRequest= explode("/", $urlRequest);
$Pagina=end($urlRequest);

$myPage=$Pagina;
$myTitulo="Reportes Contables";
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
                            <div class="col-md-2">
                                ');
        $css->select("empresa_id", "form-control btn-pill", "empresa_id", "", "", "onchange=dibuje_opciones_reporte();", 'style="padding-left:5px"');
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
        
        $css->select("reporte_id", "form-control btn-pill", "reporte_id", "", "", "onchange=dibuje_opciones_reporte()", 'style="padding-left:5px"');
            $css->option("", "", "", 1, "", "");
                print("Auxiliares Contables");
            $css->Coption();
            $css->option("", "", "", 2, "", "");
                print("Balance de Comprobación");
            $css->Coption();
            $css->option("", "", "", 3, "", "");
                print("Balance por Terceros");
            $css->Coption();            
            $css->option("", "", "", 4, "", "");
                print("Estado de Situación Financiera");
            $css->Coption();
            $css->option("", "", "", 5, "", "");
                print("Estado del Resultado Integral");
            $css->Coption();
        $css->Cselect();
        print('</div>');
        $css->div("div_opciones_reportes", "col-md-10", "", "", "", "", "");
        
        print('</div>');
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
print('<script src="jsPages/reportes_contables.js"></script>'); 

$css->Cbody();
$css->Chtml();

?>