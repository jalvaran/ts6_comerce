<?php

@session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
include_once("../../../modelo/php_conexion.php");
include_once("../../../constructores/paginas_constructor.php");

include_once("../clases/html_reportes_contables.class.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new conexion($idUser);
    $obHtml = new html_reportes_contables();
    switch ($_REQUEST["Accion"]) {
        
        case 1:// dibujo los formularios para los diferentes reportes
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            $reporte_id=$obCon->normalizar($_REQUEST["reporte_id"]);
            if($reporte_id==1 or $reporte_id==3){// auxiliares, balance por tercero
                $html=$obHtml->opciones_filtro_reportes_1_html($empresa_id);                
            }
            if($reporte_id==2){// balance de comprobacion
                $html=$obHtml->opciones_balance_comprobacion_html($empresa_id);                
            }
            if($reporte_id==4 or $reporte_id==5){// balance general y estado de resultados
                $html=$obHtml->opciones_filtro_reportes_2_html($empresa_id);                
            }
            print($html);
        break; //Fin caso 1
        
        
        case 2:// html con el movimiento de cuentas
            
            $fecha_inicial=$obCon->normalizar($_REQUEST["fecha_inicial"]);
            $fecha_final=$obCon->normalizar($_REQUEST["fecha_final"]);
            $cuenta_puc=$obCon->normalizar($_REQUEST["cuenta_puc"]);
            $tercero_id=$obCon->normalizar($_REQUEST["tercero_id"]);
            $centro_costos_id=$obCon->normalizar($_REQUEST["centro_costos_id"]);
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);
            
            if($fecha_inicial==""){
                exit("Debe seleccionar una fecha inicial");
            }
            if($fecha_final==""){
                exit("Debe seleccionar una fecha final");
            }
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            
            $tab="$db.contabilidad_librodiario";
            $Condicional="";
            if($tercero_id<>''){
                $Condicional.=" AND Tercero_Identificacion='$tercero_id'";
            }
            
            if($centro_costos_id<>''){
                $Condicional.=" AND centro_costos_id='$centro_costos_id'";
            }
            
            $sql="SELECT Fecha,Tipo_Documento_Interno,Num_Documento_Interno,Num_Documento_Externo,
                Tercero_Identificacion,Tercero_Razon_Social,CuentaPUC, NombreCuenta,Concepto,Detalle,
                @SaldoInicial as SaldoInicialCuenta,
                Debito AS Debitos,Credito AS Creditos, ( ((SELECT Debitos) - (SELECT Creditos)) ) as Saldo,
                 @SaldoFinal := @SaldoFinal + (SELECT IFNULL(Saldo,0)) AS SaldoMovimiento,
                 
                @SaldoInicial := @SaldoInicial+(SELECT IFNULL(Saldo,0)) as SaldoFinalCuenta

                FROM $tab JOIN (SELECT @SaldoFinal:=0) tb2 
                JOIN (SELECT @SaldoInicial:=(SELECT ifnull(SUM(Debito-Credito),0) FROM $tab WHERE Fecha < '$fecha_inicial' AND CuentaPUC = (select CuentaPUC))) tb3 
                WHERE Fecha>='$fecha_inicial' AND Fecha<='$fecha_final' AND CuentaPUC like '$cuenta_puc%' $Condicional ORDER BY Fecha,ID ;";
            
            print("<input type='button' class='btn btn-success' value='Exportar a Excel' onclick=tableToExcel('ReporteMovimientoCuentas','auxiliar','auxiliar.xlsx')> ");
            
            $html=$obHtml->movimiento_cuentas_html($sql);
            
            print($html);
        break;// fin caso 2    
        
        
             
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>