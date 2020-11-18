<?php 
if(isset($_REQUEST["idDocumento"])){
    
    //include_once("../../../modelo/php_conexion.php");
    //include_once("../../modelo/PrintPos.php");
    include_once("../clases/reportes_contables.class.php");
    include_once("../clases/PDF_ReportesContables.class.php");
    include_once("../clases/html_reportes_contables.class.php");
    @session_start();
    $idUser=$_SESSION["idUser"];
    $obCon = new Contabilidad($idUser);
    
    $obDoc = new PDF_ReportesContables(DB);
    $idDocumento=$obCon->normalizar($_REQUEST["idDocumento"]);
    
    
    switch ($idDocumento){
        case 1://Genera el PDF de un estado de resultados
            
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            $idEmpresa=$obCon->normalizar($_REQUEST["CmbEmpresa"]);
            $CentroCosto=$obCon->normalizar($_REQUEST["CmbCentroCosto"]);             
            //$Anio=$obCon->normalizar($_REQUEST["CmbAnio"]);
            
            $obDoc->EstadosResultadosAnio_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,"" );
    
            
        break;//Fin caso 1
    
        case 2://Genera el html con los datos del estado de resultados
            
            $FechaInicial=$obCon->normalizar($_REQUEST["fecha_inicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["fecha_final"]);
            $idEmpresa=$obCon->normalizar($_REQUEST["empresa_id"]);
            $CentroCosto=$obCon->normalizar($_REQUEST["centro_costos_id"]);             
            $Anio=$obCon->normalizar($_REQUEST["cmb_anio"]);
            if($Anio==''){
                exit("<h2>Debes seleccionar un año</h2>");
            }
            if($FechaInicial==''){
                exit("<h2>Debes seleccionar una fecha inicial</h2>");
            }
            if($FechaFinal==''){
                exit("<h2>Debes seleccionar una fecha final</h2>");
            }
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $idEmpresa);
            $db=$datos_empresa["db"];
            
            $obCon->construir_vista_estado_resultados($Anio, $idEmpresa, $CentroCosto, "");
            $FechaReporte="Del $FechaInicial al $FechaFinal";
            $TotalClases=$obDoc->ArmeTemporalSubCuentas("Rango", $FechaFinal, $FechaInicial, $CentroCosto, $idEmpresa, "");
            $html=$obDoc->html_estado_resultados_detallado($db,$TotalClases, $FechaReporte);
            $page="Consultas/PDF_ReportesContables.draw.php?idDocumento=1&TxtFechaInicial=$FechaInicial&TxtFechaFinal=$FechaFinal"; 
            $page.="&CmbEmpresa=$idEmpresa&CmbCentroCosto=$CentroCosto&CmbAnio=$Anio";
            print("<a href='$page' target='_blank'><button class='btn btn-warning' >Exportar a PDF</button></a>");
            print("<input type='button' class='btn btn-success' value='Exportar a Excel' onclick=tableToExcel('EstadoResultados','estado_resultados','estado_resultado_integral.xlsx')> ");
            
            //$css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblReporte')", "verde", "");
            print($html);
            //$obDoc->EstadosResultadosAnio_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,"" );
    
            
        break;//Fin caso 2
    
        case 4://Genera el html con los datos del balance general
            
            $FechaInicial=$obCon->normalizar($_REQUEST["fecha_inicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["fecha_final"]);
            $idEmpresa=$obCon->normalizar($_REQUEST["empresa_id"]);
            $CentroCosto=$obCon->normalizar($_REQUEST["centro_costos_id"]);             
            $Anio=$obCon->normalizar($_REQUEST["cmb_anio"]);
            if($Anio==''){
                exit("<h2>Debes seleccionar un año</h2>");
            }
            if($FechaInicial==''){
                exit("<h2>Debes seleccionar una fecha inicial</h2>");
            }
            if($FechaFinal==''){
                exit("<h2>Debes seleccionar una fecha final</h2>");
            }
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $idEmpresa);
            $db=$datos_empresa["db"];
            
            $obCon->construir_vista_estado_resultados($Anio, $idEmpresa, $CentroCosto, "");
            $FechaReporte="Del $FechaInicial al $FechaFinal";
            $TotalClases=$obDoc->ArmeTemporalSubCuentas("Rango", $FechaFinal, $FechaInicial, $CentroCosto, $idEmpresa, "");
            $html=$obDoc->HTMLBalanceGeneralDetallado($db,$TotalClases, $FechaReporte);
            
            $page="Consultas/PDF_ReportesContables.draw.php?idDocumento=7&TxtFechaInicial=$FechaInicial&TxtFechaFinal=$FechaFinal"; 
            $page.="&CmbEmpresa=$idEmpresa&CmbCentroCosto=$CentroCosto&CmbAnio=$Anio";
            print("<a href='$page' target='_blank'><button class='btn btn-warning' >Exportar a PDF</button></a>");
            
            print("<input type='button' class='btn btn-success' value='Exportar a Excel' onclick=tableToExcel('BalanceGeneral','balance','estado_situacion_financiera.xlsx')> ");
            
            //$css->CrearBotonEvento("BtnExportar", "Exportar", 1, "onclick", "ExportarTablaToExcel('TblReporte')", "verde", "");
            print($html);
            //$obDoc->EstadosResultadosAnio_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,"" );
    
            
        break;//Fin caso 4
    
        case 5://Genera el html con los datos del balance de comprobacion por tercerso
            
           
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            $Empresa=$obCon->normalizar($_REQUEST["CmbEmpresa"]);
            $CentroCostos=$obCon->normalizar($_REQUEST["CmbCentroCosto"]);             
            $CmbTercero=$obCon->normalizar($_REQUEST["CmbTercero"]);
            $TxtCuentaContable=$obCon->normalizar($_REQUEST["TxtCuentaContable"]);
            
            if($FechaInicial==""){
                exit("E1;Debe elegir una fecha inicial;TxtFechaInicial");
            }
            if($FechaFinal==""){
                exit("E1;Debe elegir una fecha Final;TxtFechaFinal");
            }
                        
            $obCon->ConstruirVistaBalanceComprobacionXTercero($FechaInicial, $FechaFinal, $CmbTercero, $Empresa, $CentroCostos, $TxtCuentaContable);
            $html=$obDoc->HtmlBalanceComprobacionXTerceros();
            $page="Consultas/PDF_ReportesContables.draw.php?idDocumento=6&TxtFechaInicial=$FechaInicial&TxtFechaFinal=$FechaFinal"; 
            $page.="&CmbEmpresa=$Empresa&CmbCentroCosto=$CentroCostos";
            print("<a href='$page' target='_blank'><button class='btn btn-warning' >Exportar a PDF</button></a>");
            print("<input type='button' class='btn btn-success' value='Exportar a Excel' onclick=ExportarTablaToExcel('TableBalanceComprobacionXTerceros')> ");
            print($html);
            
        break;//Fin caso 5
        
        case 6://Genera el PDF de un balance de comprobacion por terceros
            
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            $idEmpresa=$obCon->normalizar($_REQUEST["CmbEmpresa"]);
            $CentroCosto=$obCon->normalizar($_REQUEST["CmbCentroCosto"]);             
            
            $obDoc->BalanceComprobacionXTerceros_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto );
    
            
        break;//Fin caso 6
    
        case 7://Genera el PDF de un balance general
            
            $FechaInicial=$obCon->normalizar($_REQUEST["TxtFechaInicial"]);
            $FechaFinal=$obCon->normalizar($_REQUEST["TxtFechaFinal"]);
            $idEmpresa=$obCon->normalizar($_REQUEST["CmbEmpresa"]);
            $CentroCosto=$obCon->normalizar($_REQUEST["CmbCentroCosto"]);             
            $Anio=$obCon->normalizar($_REQUEST["CmbAnio"]);
            $obCon->construir_vista_estado_resultados($Anio, $idEmpresa, $CentroCosto, "");
            $FechaReporte="Del $FechaInicial al $FechaFinal";
            //$TotalClases=$obDoc->ArmeTemporalSubCuentas("Rango", $FechaFinal, $FechaInicial, $CentroCosto, $idEmpresa, "");
            //$html=$obDoc->HTMLBalanceGeneralDetallado($TotalClases, $FechaReporte);
            
            $obDoc->estado_situacion_finaciera_pdf($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,"" );
    
            
        break;//Fin caso 7
    
        case 8:// pdf de un documento contable
            $empresa_id=$obCon->normalizar($_REQUEST["empresa_id"]);            
            $datos_empresa=$obCon->DevuelveValores("empresapro", "ID", $empresa_id);
            $db=$datos_empresa["db"];
            $documento_id=$obCon->normalizar($_REQUEST["ID"]);   
            $obDoc->documento_contable_pdf($datos_empresa,$db, $documento_id, "");
            
        break;//fin caso 8    
        
    }
}else{
    print("No se recibió parametro de documento");
}

?>