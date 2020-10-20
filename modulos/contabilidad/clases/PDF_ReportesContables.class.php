<?php

if(file_exists("../../../general/class/ClasesPDFDocumentos.class.php")){
    include_once("../../../general/class/ClasesPDFDocumentos.class.php");
}

class PDF_ReportesContables extends Documento{
    
    public function EstadosResultadosAnio_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,$Vector ) {
        $TipoReporte="Rango";
        $idEmpresaEncabezado=$idEmpresa;
        if($idEmpresa=="ALL"){
            $idEmpresaEncabezado=1;
        }
        
        $FechaReporte="Del $FechaInicial al $FechaFinal";
        
        
        $this->PDF_Ini("Estado de Resultados", 8, "",1,"../../../");
        $this->PDF_Encabezado($FechaFinal,$idEmpresaEncabezado, 26, "","","../../../");
        $TotalClases=$this->ArmeTemporalSubCuentas($TipoReporte,$FechaFinal,$FechaInicial,$CentroCosto,$idEmpresa,$Vector);
        
        $html= $this->HTMLEstadoResultadosDetallado($TotalClases,$FechaReporte);
        $this->PDF_Write($html);
             
        $this->PDF_Output("Estado_Resultados_$FechaFinal");
    }
    
    //Armar el html para el estado de resultados
    public function HTMLEstadoResultadosDetallado($TotalClases,$FechaCorte) {
        $Back="#CEE3F6";
        $html='<table id="EstadoResultados" class="table table-bordered table table-hover" cellspacing="1" cellpadding="2" border="0"  align="center" >';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>Estado del Resultado Integral <br> '.$FechaCorte.'</strong></td></tr>'; 
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>INGRESOS</strong></td></tr>';
        
        ///Se dibujan los ingresos
        $h=0;  
        $Back="white";
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=4");
        $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>SALDO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
        
        $html.='</tr>';   
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]*(-1));
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]*(-1));
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]*(-1));
           
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           //$html.='<td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>SALDO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>'; 
        }
        
        $TotalIngresos=0;
        if($TotalClases[4]<>""){
            $TotalIngresos=  number_format($TotalClases[4]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total de Ingresos:</strong></td><td><strong>'.$TotalIngresos.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
         ///Se dibujan los costos de venta y produccion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>COSTOS DE VENTA Y/O PRODUCCION</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=6 OR Clase=7");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>'; 
        }
        
        
        $TotalCostos=$TotalClases[6]+$TotalClases[7];
        $TotalCostosN=0;
        if($TotalCostos<>""){
            $TotalCostosN=  number_format($TotalCostos);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total Costos de Venta y/o Produccion:</strong></td><td><strong>'.$TotalCostosN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos Utilidad Bruta
        
        $UtilidadBruta=0;
        if($TotalClases["UB"]<>""){
            $UtilidadBruta=  number_format($TotalClases["UB"]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Utilidad Bruta:</strong></td><td><strong>'.$UtilidadBruta.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        
        ///Se dibujan los gastos y utilidad de la operacion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>GASTOS</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=5");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>';  
        }
        
        $TotalGastos=0;
        if($TotalClases[5]<>""){
            $TotalGastos=  number_format($TotalClases[5]);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total Gastos:</strong></td><td><strong>'.$TotalGastos.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos Utilidad Bruta
        
        $UtilidadOperacional=0;
        if($TotalClases["UO"]<>""){
            $UtilidadOperacional=  number_format($TotalClases["UO"]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Utilidad de la Operacion:</strong></td><td><strong>'.$UtilidadOperacional.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        $html.="</table>";
        return($html);
    }
    
    //Crear Estados Financieros en PDF
 
    public function ArmeTemporalSubCuentas($TipoReporte,$FechaFinal,$FechaInicial,$CentroCostos,$EmpresaPro,$Vector){
        
        $Condicion=" WHERE Fecha<='$FechaFinal'";
        if($CentroCostos<>"ALL"){
            $Condicion.=" AND idCentroCosto='$CentroCostos'";
        }
        if($EmpresaPro<>"ALL"){
            $Condicion.=" AND idEmpresa='$EmpresaPro'";
        }
        $Clase=0;
        $this->obCon->VaciarTabla("estadosfinancieros_mayor_temporal");
        $sql="SELECT `CuentaPUC` AS Cuenta,NombreCuenta AS NombreCuenta ,sum(`Neto`) as TotalCuenta FROM `vista_estado_resultados_anio` $Condicion  GROUP BY `CuentaPUC` ORDER BY `CuentaPUC`";
        $Consulta=$this->obCon->Query($sql);
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($DatosMayor["Cuenta"]>0){
                $Cuenta=$DatosMayor["Cuenta"];
                if($TipoReporte=="Corte"){
                    $SaldoAnterior=0;
                }else{
                    $SaldoAnterior=$this->obCon->Sume("vista_estado_resultados_anio", "Neto", "WHERE Fecha<'$FechaInicial' AND `CuentaPUC`='$Cuenta'");
                }
                $Clase=substr($DatosMayor["Cuenta"], 0, 1);
                //$DatosCuenta=$this->obCon->DevuelveValores("cuentas", "idPUC", $DatosMayor["Cuenta"]);
                $tab="estadosfinancieros_mayor_temporal";
                $NumRegistros=7;
                $Columnas[0]="FechaCorte";        $Valores[0]=$FechaFinal;
                $Columnas[1]="Clase";             $Valores[1]=$Clase;
                $Columnas[2]="CuentaPUC";         $Valores[2]=$DatosMayor["Cuenta"];
                $Columnas[3]="NombreCuenta";      $Valores[3]=$DatosMayor["NombreCuenta"];
                $Columnas[4]="Neto";              $Valores[4]=$DatosMayor["TotalCuenta"]-$SaldoAnterior;
                $Columnas[5]="SaldoAnterior";     $Valores[5]=$SaldoAnterior;
                $Columnas[6]="SaldoFinal";        $Valores[6]=$DatosMayor["TotalCuenta"];
                $this->obCon->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
            }
        }
        
        $Activos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='1'");
        $Pasivos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='2'");
        $Patrimonio=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='3'");
        $Ingresos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='4'");
        $GastosOperativos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='5'");
        $CostosVentas=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='6'");
        $CostosProduccion=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='7'");
        
        $TotalClases[1]=$Activos;
        $TotalClases[2]=$Pasivos*(-1);    //Es naturaleza credito por lo tanto debe multiplicarse por -1
        $TotalClases[3]=$Patrimonio*(-1);
        $TotalClases[4]=$Ingresos*(-1);
        $TotalClases[5]=$GastosOperativos;
        $TotalClases[6]=$CostosVentas;
        $TotalClases[7]=$CostosProduccion;
        $TotalClases["RE"]=($TotalClases[1]-$TotalClases[2]-$TotalClases[3])*(-1);//resultado del ejercicio
        $TotalClases["UB"]=$TotalClases[4]-$TotalClases[6]-$TotalClases[7];//Utilidad Bruta
        $TotalClases["UO"]=$TotalClases["UB"]-$TotalClases[5]; //Utilidad de la Operacion
             
        return($TotalClases);
    }
    
    //Armar el html para el estado de resultados
    public function HTMLReporteMovimientoDeCuentas($sql) {
        $Back="#CEE3F6";
        $html='<table id="ReporteMovimientoCuentas" class="table table-bordered table table-hover" cellspacing="1" cellpadding="2" border="0"  align="center" >';
                
        ///Se dibujan los ingresos
        $h=0;  
        $Back="white";
        $Consulta=$this->obCon->Query($sql);
        $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td><strong>FECHA</strong></td><td><strong>TERCERO</strong></td><td><strong>DOCUMENTO</strong></td><td><strong>REFERENCIA</strong></td><td><strong>DETALLE</strong></td><td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>DEBITO</strong></td><td><strong>CREDITO</strong></td><td><strong>SALDO MOVIMIENTO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
        
        $html.='</tr>';   
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
                      
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $html.="<td>".$DatosMayor["Fecha"]."</td>";
           $html.="<td>".$DatosMayor["Tercero_Razon_Social"]." ".$DatosMayor["Tercero_Identificacion"]."</td>";
           $html.="<td>".$DatosMayor["Tipo_Documento_Intero"]." ".$DatosMayor["Num_Documento_Interno"]."</td>";
           $html.="<td>".$DatosMayor["Num_Documento_Externo"]."</td>";
           $html.="<td>".$DatosMayor["Detalle"]."</td>";
           $html.="<td>".$DatosMayor["CuentaPUC"]."</td>";
           $html.="<td>".$DatosMayor["NombreCuenta"]."</td>";
           $html.="<td>".number_format($DatosMayor["SaldoInicialCuenta"])."</td>";
           $html.="<td>".number_format($DatosMayor["Debitos"])."</td>";
           $html.="<td>".number_format($DatosMayor["Creditos"])."</td>";
           $html.="<td>".number_format($DatosMayor["SaldoMovimiento"])."</td>";
           $html.="<td>".number_format($DatosMayor["SaldoFinalCuenta"])."</td>";
           
           $html.='</tr>'; 
        }
        $html.='</table>'; 
        
        return($html);
    }
    
    
    //Armar el html para el estado de resultados
    public function HTMLBalanceGeneralDetallado($TotalClases,$FechaCorte) {
        $Back="#CEE3F6";
        $html='<table id="BalanceGeneral" class="table table-bordered table table-hover" cellspacing="1" cellpadding="2" border="0"  align="center" >';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>Estado de Situacion Financiera <br> '.$FechaCorte.'</strong></td></tr>'; 
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>ACTIVOS</strong></td></tr>';
        
        ///Se dibujan los activos
        $h=0;  
        $Back="white";
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=1");
        $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>SALDO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
        
        $html.='</tr>';   
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           //$html.='<td><strong>CUENTA</strong></td><td><strong>NOMBRE</strong></td><td><strong>SALDO ANTERIOR</strong></td><td><strong>SALDO</strong></td><td><strong>SALDO FINAL</strong></td>'; 
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.utf8_encode($DatosMayor["NombreCuenta"]).'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>'; 
        }
        
        $TotalActivos=$TotalClases[1];
        if($TotalClases[1]<>""){
            $TotalActivosN=  number_format($TotalClases[1]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total de Activos:</strong></td><td><strong>'.$TotalActivosN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
         ///Se dibujan los pasivos
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>PASIVOS</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=2");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.utf8_encode($DatosMayor["NombreCuenta"]).'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>'; 
        }
        
        
    $TotalPasivos=$TotalClases[2]*(-1);
        $TotalPasivosN=0;
        if($TotalPasivos<>""){
            $TotalPasivosN=  number_format($TotalPasivos);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total Pasivos:</strong></td><td><strong>'.$TotalPasivosN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        ///Se dibujan los gastos y utilidad de la operacion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="5"><strong>PATRIMONIO</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=3");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $SaldoAnterior=  number_format($DatosMayor["SaldoAnterior"]);
           $SaldoFinal=  number_format($DatosMayor["SaldoFinal"]);
           
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.utf8_encode($DatosMayor["NombreCuenta"]).'</td><td align="right">'.$SaldoAnterior.'</td>'.'<td align="right">'.$Valor.'</td>'.'<td align="right">'.$SaldoFinal.'</td>' ; 
           $html.='</tr>';  
        }
        
        $TotalPatrimonio=$TotalClases[3]*(-1);
        $TotalPatrimonioN=0;
        if($TotalPatrimonio<>""){
            $TotalPatrimonioN=  number_format($TotalPatrimonio);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>Total Patrimonio:</strong></td><td><strong>'.$TotalPatrimonioN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos la suma de los pasivos mas el patrimonio
        
        $PasivoPatrimonio=$TotalPasivos+$TotalPatrimonio;
        $PasivoPatrimonioN=0;
        if($PasivoPatrimonio<>""){
            $PasivoPatrimonioN=  number_format($PasivoPatrimonio);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>PASIVO + PATRIMONIO:</strong></td><td><strong>'.$PasivoPatrimonioN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        
        //Diferencia
        
        $Diferencia=$TotalActivos+$PasivoPatrimonio;
        $DiferenciaN=0;
        if($Diferencia<>""){
            $DiferenciaN=  number_format($Diferencia);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="3"><strong>DIFERENCIA:</strong></td><td><strong>'.$DiferenciaN.'</strong></td><td> </td>'; 
        $html.='</tr>'; 
        
        $html.="</table>";
        return($html);
    }
    
    
    public function HtmlBalanceComprobacionXTerceros() {
        $sql="SELECT * FROM vista_balance_comprobacion_terceros";
        $Consulta=$this->obCon->Query($sql);
        $TotalValorCuotas=0;
        $TotalPagos=0;
        $TotalSaldo=0;
        
        $Back="#CEE3F6";
        $html='<table id="TableBalanceComprobacionXTerceros" class="table table-bordered table table-hover" cellspacing="1" cellpadding="2" border="0"  align="left" >';
        $html.='<thead>';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';        
            $html.='<th><strong>CUENTA</strong></th>';
            $html.='<th><strong>NOMBRE</strong></th>';
            $html.='<th><strong>TERCERO</strong></th>';
            $html.='<th><strong>RAZON SOCIAL</strong></th>';
            $html.='<th><strong>DIRECCION</strong></th>';
            $html.='<th><strong>DEBITO</strong></th>';
            $html.='<th><strong>CREDITO</strong></th>';
            $html.='<th><strong>SALDO</strong></th>';
        $html.='</tr></thead><tbody>';
        $h=0;
        while($DatosConsulta=$this->obCon->FetchAssoc($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
            
            $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
                $html.='<td>'.$DatosConsulta["CuentaPUC"].'</td>';
                $html.='<td>'.utf8_encode($DatosConsulta["NombreCuenta"]).'</td>';
                $html.='<td>'.($DatosConsulta["Tercero_Identificacion"]).'</td>';
                $html.='<td>'.utf8_encode($DatosConsulta["Tercero_Razon_Social"]).'</td>';
                $html.='<td>'.utf8_encode($DatosConsulta["Tercero_Direccion"]).'</td>';
                $html.='<td>'.number_format($DatosConsulta["Debitos"]).'</td>';
                $html.='<td>'.number_format($DatosConsulta["Creditos"]).'</td>';
                $html.='<td>'.number_format($DatosConsulta["Debitos"]-$DatosConsulta["Creditos"]).'</td>';
            $html.='</tr>';
        }
        
        $html.="</tbody></table>";
        return($html);
    }
    
    public function BalanceComprobacionXTerceros_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto) {
        $idEmpresaEncabezado=$idEmpresa;
        if($idEmpresa=="ALL"){
            $idEmpresaEncabezado=1;
        }
        $this->PDF_Ini("Balance de Comprobacion por Terceros", 8, "",1,"../../../");
        $this->PDF_Encabezado($FechaFinal,$idEmpresaEncabezado, 38, "","","../../../");
        
        $html= $this->HtmlBalanceComprobacionXTerceros();
        $this->PDF_Write($html);
             
        $this->PDF_Output("BalanceComprobacionTerceros_$FechaFinal");
    }
    
    
    public function EstadoSituacionFinaciera_PDF($FechaInicial,$FechaFinal,$idEmpresa,$CentroCosto,$Vector ) {
        $TipoReporte="Rango";
        $idEmpresaEncabezado=$idEmpresa;
        if($idEmpresa=="ALL"){
            $idEmpresaEncabezado=1;
        }
        
        $FechaReporte="Del $FechaInicial al $FechaFinal";
        
        
        $this->PDF_Ini("Estado de Situacion Finaciera", 8, "",1,"../../../");
        $this->PDF_Encabezado($FechaFinal,$idEmpresaEncabezado, 39, "","","../../../");
        $TotalClases=$this->ArmeTemporalSubCuentas($TipoReporte,$FechaFinal,$FechaInicial,$CentroCosto,$idEmpresa,$Vector);
        
        $html= $this->HTMLBalanceGeneralDetallado($TotalClases,$FechaReporte);
        $this->PDF_Write($html);
             
        $this->PDF_Output("Estado_Situacion_Financiera_$FechaFinal");
    }
    
    
    
    public function documento_contable_pdf($datos_empresa,$db,$idDocumento,$Vector) {
        $DatosDocumento=$this->obCon->DevuelveValores("$db.contabilidad_documentos_contables", "ID", $idDocumento);
        $DescripcionDocumento=$this->obCon->DevuelveValores("$db.contabilidad_catalogo_documentos_contables", "ID", $DatosDocumento["tipo_documento_contable_id"]);
        $Documento=$DescripcionDocumento["Nombre"]." ".$DatosDocumento["Consecutivo"];
        $NombreDocumento=$DescripcionDocumento["Nombre"];
        $Consecutivo=$DatosDocumento["Consecutivo"];
        //$this->PDF_Ini("Estado de Resultados", 8, "",1,"../../../");
        $this->PDF_Ini($Documento, 8, "",1,"../../../");
        $idFormato=32;
        $this->PDF_Encabezado($DatosDocumento["Fecha"],$datos_empresa["ID"], $idFormato, "",$Documento,$datos_empresa,"../../../");
        $this->PDF_Encabezado_Documento_Contable($DatosDocumento, $DescripcionDocumento, "");
        
        
        $Position=$this->PDF->SetY(60);
        
        
        $sql="SELECT Tercero_Identificacion,NombreCuenta,Tercero_Razon_Social ,Num_Documento_Externo,CuentaPUC,Debito,Credito FROM $db.contabilidad_librodiario "
                . "WHERE Tipo_Documento_Interno='$NombreDocumento' AND Num_Documento_Interno='$Consecutivo'";
        $html=$this->HTML_Movimientos_Resumen($sql, $Vector);
        $this->PDF_Write("<BR><BR><BR><strong>MOVIMIENTOS CONTABLES:</strong><BR>".$html);
        $html=$this->FirmaDocumentos();
        $this->PDF_Write("<BR>".$html);
        
        $this->PDF_Output("$Documento");
    }
    
    
    public function PDF_Encabezado_Documento_Contable($DatosDocumento,$DescripcionDocumento,$Vector) {
        
        $DatosUsuario=$this->obCon->DevuelveValores("usuarios", "ID", $DatosDocumento["idUser"]);
        $Usuario=$DatosUsuario["Nombre"]." ".$DatosUsuario["Apellido"];
        $tbl = <<<EOD
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td><strong>Fecha:</strong></td>
        <td colspan="3">$DatosDocumento[Fecha]</td>
        
    </tr>
    <tr>
    	<td><strong>Documento:</strong></td>
        <td colspan="3">$DescripcionDocumento[Nombre]</td>
    </tr>
    <tr>
        <td><strong>Numero:</strong></td>
        <td colspan="3">$DatosDocumento[Consecutivo]</td>
    </tr>
    
    
</table>
        
EOD;


$this->PDF->MultiCell(93, 25, $tbl, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');


////Concepto
////
////

$tbl = <<<EOD
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td align="left" >$DatosDocumento[Descripcion]</td> 
    </tr>
     
</table>
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td align="center" ><strong>Realizó: </strong></td>
        
    </tr>
    <tr>
        <td align="center" >$Usuario</td>
        
    </tr>
     
</table>
<br>  <br><br><br>      
EOD;

$this->PDF->MultiCell(93, 25, $tbl, 0, 'R', 1, 0, '', '', true,0, true, true, 10, 'M');

    
    }
    
    /**
     * Fin Clase
     */
}
