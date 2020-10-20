<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
    
}
if(file_exists("../../../librerias/Excel/PHPExcel.php")){
    
    require_once '../../../librerias/Excel/PHPExcel.php';
}


class ExportReportes extends conexion{
    
    public $Campos = array("A","B","C","D","E","F","G","H","I","J","K","L",
    "M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP");
    
    public function ExportarBalanceXTercerosAExcel($Detallado=1,$Encabezados=1){
        
        $objPHPExcel = new PHPExcel();  
        
        $objPHPExcel->getActiveSheet()->getStyle('A:C')->getNumberFormat()->setFormatCode('#');
        $objPHPExcel->getActiveSheet()->getStyle('H:L')->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle("A:Z")->getFont()->setSize(10);
        
        $f=1;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[1].$f,"BALANCE DE COMPROBACION")
                        
            ;
        $f=2;
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[0].$f,"CUENTA")
            ->setCellValue($this->Campos[1].$f,"NOMBRE")
            ->setCellValue($this->Campos[2].$f,"TERCERO")
            ->setCellValue($this->Campos[3].$f,"RAZON SOCIAL")
            ->setCellValue($this->Campos[4].$f,"FECHA")
            ->setCellValue($this->Campos[5].$f,"DOCUMENTO")
            ->setCellValue($this->Campos[6].$f,"DOC. REFERENCIA")     
            ->setCellValue($this->Campos[7].$f,"SALDO ANTERIOR")
            ->setCellValue($this->Campos[8].$f,"DEBITOS")
            ->setCellValue($this->Campos[9].$f,"CREDITOS")
            ->setCellValue($this->Campos[10].$f,"NUEVO SALDO")
            
            
            ;
        
         
        $sql="SELECT * FROM vista_balancextercero2";
        $Consulta=$this->Query($sql);
        $TotalDebitos=0;
        $TotalCreditos=0;
        $SaldoAnterior=0;
        $f=3;
        $ClaseAnterior="";
        $GrupoAnterior="";
        $CuentaPadreAnterior="";
        $identificacionAnterior="";
        $SaldoInicialClase=0;
        $SaldoInicialGrupo=0;
        $LineaActualClase=$f;
        $LineaActualGrupo=$f;
        $CuentaPUCAnterior="";
        $TotalDebitosTercero=0;
        $TotalCreditosTercero=0;
        while($DatosLibro= $this->FetchArray($Consulta)){
                        
            if($Detallado==1){
                if($DatosLibro["CuentaPUC"]<>$CuentaPUCAnterior){
                    $CuentaPUCAnterior=$DatosLibro["CuentaPUC"];
                    if(($TotalDebitosTercero<>0 or $TotalCreditosTercero<>0) ){
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[7].$f,"TOTALES:")     

                        ->setCellValue($this->Campos[8].$f,$TotalDebitosTercero)
                        ->setCellValue($this->Campos[9].$f,$TotalCreditosTercero)

                        ;
                        $f++;
                    }
                    $SaldoAnterior=$DatosLibro["SaldoInicialSubCuenta"];
                    $TotalDebitosTercero=0;
                    $TotalCreditosTercero=0;
                }
                if($DatosLibro["Identificacion"]<>$identificacionAnterior){
                    if(($TotalDebitosTercero<>0 or $TotalCreditosTercero<>0) ){
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[7].$f,"TOTALES:")     

                        ->setCellValue($this->Campos[8].$f,$TotalDebitosTercero)
                        ->setCellValue($this->Campos[9].$f,$TotalCreditosTercero)

                        ;
                        $f++;
                    }
                    
                    $f++;
                    $CuentaPUCAnterior=$DatosLibro["CuentaPUC"];
                    $identificacionAnterior=$DatosLibro["Identificacion"];
                    $SaldoAnterior=$DatosLibro["SaldoInicialSubCuenta"];
                    $TotalDebitosTercero=0;
                    $TotalCreditosTercero=0;
                }

            }
            if($Encabezados==1){
                if($DatosLibro["Clase"]<>$ClaseAnterior){



                    $ClaseAnterior=$DatosLibro["Clase"];
                    $Clase=$DatosLibro["Clase"];

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[0].$f,$DatosLibro["Clase"])
                        ->setCellValue($this->Campos[1].$f,$DatosLibro["NombreClase"])

                        ->setCellValue($this->Campos[7].$f,$DatosLibro["SaldoInicialClase"])
                        ->setCellValue($this->Campos[8].$f,$DatosLibro["DebitosClase"])
                        ->setCellValue($this->Campos[9].$f,$DatosLibro["CreditosClase"])
                        ->setCellValue($this->Campos[10].$f,$DatosLibro["SaldoInicialClase"]+$DatosLibro["DebitosClase"]-$DatosLibro["CreditosClase"])

                        ;

                    $f++;
                }

                if($DatosLibro["Grupo"]<>$GrupoAnterior){

                    $GrupoAnterior=$DatosLibro["Grupo"];
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[0].$f,$DatosLibro["Grupo"])
                        ->setCellValue($this->Campos[1].$f,$DatosLibro["NombreGrupo"])

                        ->setCellValue($this->Campos[7].$f,$DatosLibro["SaldoInicialGrupo"])
                        ->setCellValue($this->Campos[8].$f,$DatosLibro["DebitosGrupo"])
                        ->setCellValue($this->Campos[9].$f,$DatosLibro["CreditosGrupo"])
                        ->setCellValue($this->Campos[10].$f,$DatosLibro["SaldoInicialGrupo"]+$DatosLibro["DebitosGrupo"]-$DatosLibro["CreditosGrupo"])

                        ;

                    $f++;
                }

                if($DatosLibro["CuentaPadre"]<>$CuentaPadreAnterior){

                    $CuentaPadreAnterior=$DatosLibro["CuentaPadre"];
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[0].$f,$DatosLibro["CuentaPadre"])
                        ->setCellValue($this->Campos[1].$f,$DatosLibro["NombreCuentaPadre"])

                        ->setCellValue($this->Campos[7].$f,$DatosLibro["SaldoInicialCuentaPadre"])
                        ->setCellValue($this->Campos[8].$f,$DatosLibro["DebitosCuentaPadre"])
                        ->setCellValue($this->Campos[9].$f,$DatosLibro["CreditosCuentaPadre"])
                        ->setCellValue($this->Campos[10].$f,$DatosLibro["SaldoInicialCuentaPadre"]+$DatosLibro["DebitosCuentaPadre"]-$DatosLibro["CreditosCuentaPadre"])

                        ;

                    $f++;
                }
            }
             
            $NuevoSaldo=$SaldoAnterior+$DatosLibro["Debito"]-$DatosLibro["Credito"];
            
            if($Detallado==1){
            
                if($DatosLibro["Debito"]<>0 or $DatosLibro["Credito"]<>0){

                     $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue($this->Campos[0].$f,$DatosLibro["CuentaPUC"])
                     ->setCellValue($this->Campos[1].$f,$DatosLibro["NombreCuenta"])             
                     ->setCellValue($this->Campos[2].$f,$DatosLibro["Identificacion"])
                     ->setCellValue($this->Campos[3].$f,$DatosLibro["Razon_Social"])
                     ->setCellValue($this->Campos[4].$f,$DatosLibro["Fecha"])
                     ->setCellValue($this->Campos[5].$f,$DatosLibro["TipoDocumento"]." No. ".$DatosLibro["NumDocumento"]) 
                     ->setCellValue($this->Campos[6].$f,$DatosLibro["DocumentoExterno"])        
                     ->setCellValue($this->Campos[7].$f,$SaldoAnterior)
                     ->setCellValue($this->Campos[8].$f,$DatosLibro["Debito"])
                     ->setCellValue($this->Campos[9].$f,$DatosLibro["Credito"])
                     ->setCellValue($this->Campos[10].$f,$NuevoSaldo)

                     ;
                     $f++;

                }
            
             
            }
            
            $SaldoAnterior=$SaldoAnterior+$DatosLibro["Debito"]-$DatosLibro["Credito"]; 
            
           
            $TotalDebitos=$TotalDebitos+$DatosLibro["Debito"];
            $TotalCreditos=$TotalCreditos+$DatosLibro["Credito"];
            
            $TotalDebitosTercero=$TotalDebitosTercero+$DatosLibro["Debito"];
            $TotalCreditosTercero=$TotalCreditosTercero+$DatosLibro["Credito"];
            
            
        }
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[7].$f,"TOTALES:")
            ->setCellValue($this->Campos[8].$f,$TotalDebitos)
            ->setCellValue($this->Campos[9].$f,$TotalCreditos)
            ->setCellValue($this->Campos[10].$f,"DIFERENCIA:")
            ->setCellValue($this->Campos[11].$f,$TotalDebitos-$TotalCreditos)
                     
            ;
        
        $objPHPExcel->
        getProperties()
            ->setCreator("www.technosoluciones.com.co")
            ->setLastModifiedBy("www.technosoluciones.com.co")
            ->setTitle("Relacion de Facturas")
            ->setSubject("Informe")
            ->setDescription("Documento generado con PHPExcel")
            ->setKeywords("techno soluciones sas")
            ->setCategory("Relacion de Facturas");    

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'."Balance".'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
    
    }
    
    /**
     * Fin Clase
     */
}
