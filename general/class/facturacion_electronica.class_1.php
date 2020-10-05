<?php
if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}
/* 
 * Clase que realiza los procesos de facturacion electronica
 * Julian Alvaran
 * Techno Soluciones SAS
 */

class Factura_Electronica extends ProcesoVenta{
    public function ConstruyaLayoutEmitirFactura($UserWebService,$PassWebService,$idFactura) {
        $DatosFactura=$this->DevuelveValores("facturas", "idFacturas", $idFactura);
        $idEmpresaPro=$DatosFactura["EmpresaPro_idEmpresaPro"];
        $DatosEmpresaPro=$this->DevuelveValores("empresapro", "idEmpresaPro", $idEmpresaPro);
        $DatosCliente=$this->DevuelveValores("clientes", "idClientes", $DatosFactura["Clientes_idClientes"]);
        $DocumentoFE="FACTURA";
        $TipoDocumentoFE="INVOIC";        
        $NitEmisor=$DatosEmpresaPro["NIT"];
        $DVEmisor=str_pad($DatosEmpresaPro["DigitoVerificacion"], 2, "0", STR_PAD_LEFT);
        $NitEmisorCompleto=$NitEmisor."-".$DVEmisor;
        $NitAdquiriente=$DatosCliente["Num_Identificacion"];
        $UBLVersion="UBL 2.0";
        $VersionFormatoDocumento="DIAN 1.0";
        $PrefijoFactura=$DatosFactura["Prefijo"];
        $NumeroFactura=$DatosFactura["NumeroFactura"];
        $FacturaCompleta=$PrefijoFactura.$NumeroFactura;
        $FechaFactura=$DatosFactura["Fecha"];
        $HoraFactura=$DatosFactura["Hora"];
        $TipoFactura=1;//1 Factura 9 Nota Credito
        $MonedaFactura="COP";
        $Datos["Fecha"]=$DatosFactura["Fecha"];
        $Datos["Dias"]=30;
        $FechaVencimiento=$this->SumeDiasFecha($Datos);
        $EmisorTipoPersona=$DatosEmpresaPro["TipoPersona"]; //1 Juridica 2 Persona Natural
        $EmisorTipoDocumento=$DatosEmpresaPro["TipoDocumento"]; //Tabla tipos_documentos
        $EmisorNumTipoRegimen=0;
        if($DatosEmpresaPro["Regimen"]=='COMUN'){
            $EmisorNumTipoRegimen=2; //0 Simplificado 2 Comun 
        }
        
        $EmisorRazonSocial=$DatosEmpresaPro["RazonSocial"];
        $EmisorDireccion=$DatosEmpresaPro["Direccion"];
        $DatosDepartamentos=$this->DevuelveValores("cod_municipios_dptos", "Ciudad", $DatosEmpresaPro["Ciudad"]);
        $EmisorDepartamento=$DatosDepartamentos["Departamento"];
        $EmisorCiudad=$DatosEmpresaPro["Ciudad"];
        $EmisorBarrio=$DatosEmpresaPro["Barrio"];
        $EmisorCodigoPais="CO";
        $EmisorPais="Colombia";
        //Actividades del RUT
        $Actividades = explode(";", $DatosEmpresaPro["ActividadesEconomicas"]);
       
        foreach ($Actividades as $key => $value) {
            $TAC="(TAC)TAC_1:$value;(/TAC)";
        }
        
        $EmisorMatriculaMercantil="1234567";
        
        //Datos Adquiriente
        
        $AdqNit=$DatosCliente["Num_Identificacion"];
        $AdqTipoDocumento=13;
        $AdqTipoPersona=2;
        if($AdqTipoDocumento==31 or $AdqTipoDocumento==44){
            $AdqTipoPersona=1; //1 Juridica 2 Persona Natural
        }
        $adqNumTipoRegimen=0;


        $AdqRazonSocial=$DatosCliente["RazonSocial"];
        $AdqNombres=$DatosCliente["Primer_Nombre"]." ".$DatosCliente["Otros_Nombres"];
        $adqApellidos=$DatosCliente["Primer_Apellido"]." ".$DatosCliente["Segundo_Apellido"];

        $AdqDireccion=$DatosCliente["Direccion"];
        $DatosDepartamentos=$this->DevuelveValores("cod_municipios_dptos", "Ciudad", $DatosCliente["Ciudad"]);
        $AdqDepartamento=$DatosDepartamentos["Departamento"];
        $AdqBarrio=$DatosCliente["Ciudad"];
        $AdqCiudad=$DatosCliente["Ciudad"];
        $AdqCodigoPais="CO";
        $AdqCodigoComercio=0; //0 si se desconoce
        $AdqInfoTributariaAduana="O-99";  // O-99 si se desconoce
        $AdqContactoTipo=1; // 1 Persona de contacto, 2 de Entrega,3 de contabilidad, 4 de compras, 5 procesamiento del pedido
        $AdqContactoNombre=$DatosCliente["Contacto"];
        $AdqContactoTelefono=$DatosCliente["TelContacto"];
        $AdqContactoMail=$DatosCliente["Email"];
        
        //Datos Resolucion
        $DatosResolucion=$this->DevuelveValores("empresapro_resoluciones_facturacion", "ID", $DatosFactura["idResolucion"]);
        $NumeroResolucion=$DatosResolucion["NumResolucion"];
        $FechaInicioResolucion=$DatosResolucion["Fecha"];
        $FechaFinResolucion=$DatosResolucion["FechaVencimiento"];
        $PrefijoResolucion=$DatosResolucion["Prefijo"];
        $RangoInicialResolucion=$DatosResolucion["Desde"];
        $RangoFinalResolucion=$DatosResolucion["Hasta"];
        //Notas legales
        $NotasLegales=$DatosEmpresaPro["ResolucionDian"];
        
        //Totales de la factura
        
        
        $sql="SELECT SUM(ValorOtrosImpuestos) as ValorOtrosImpuestos,SUM(SubtotalItem) as Subtotal, SUM(IVAItem) as IVA, SUM(TotalItem) as Total, PorcentajeIVA FROM facturas_items "
                . " WHERE idFactura='$idFactura' GROUP BY PorcentajeIVA";
        $Consulta=$this->Query($sql);
        $SubtotalFactura=0;
        $TotalFactura=0;
        $TotalIVAFactura=0;
        $OtrosImpuestos=0;
        while($TotalesFactura= $this->FetchArray($Consulta)){
            
            $OtrosImpuestos=$OtrosImpuestos+$TotalesFactura["ValorOtrosImpuestos"];
            $SubtotalFactura=$SubtotalFactura+$TotalesFactura["Subtotal"];
            $TotalFactura=$TotalFactura+$TotalesFactura["Total"];
            $TotalIVAFactura=$TotalIVAFactura+$TotalesFactura["IVA"];
            $PorcentajeIVA=$TotalesFactura["PorcentajeIVA"];
            
            $TiposIVA[$PorcentajeIVA]=$TotalesFactura["PorcentajeIVA"];
            $IVA[$PorcentajeIVA]["Valor"]=$TotalesFactura["IVA"];
            $Bases[$PorcentajeIVA]["Valor"]=$TotalesFactura["Subtotal"];
        }
        $TotalBases=0;  
        $TotalImpuestos=0;
        $ImpuestosMoneda="COP";
        $LayoutImpuestos="";
        foreach($TiposIVA as $PorcentajeIVA){

            if($Bases[$PorcentajeIVA]["Valor"]>0){
                $TotalBases=$TotalBases+$Bases[$PorcentajeIVA]["Valor"];
            }
            if($IVA[$PorcentajeIVA]["Valor"]>0){
                $TotalImpuestos=$TotalImpuestos+$IVA[$PorcentajeIVA]["Valor"];
                
                $ImpuestosClase="01"; //01 IVA, 02 Impoconsumo, 03 ICA, 
                $ImpuestosBase=round($Bases[$PorcentajeIVA]["Valor"],2);
                $ImpuestosMonedaBase="COP";
                $ImpuestosTotalItemImpuesto=round($IVA[$PorcentajeIVA]["Valor"],2);
                $PorcentajeLimpio=str_replace( "%" , "" , $PorcentajeIVA);
                $ImpuestosPorcentaje=round($PorcentajeLimpio,2);
                $DatosImpuestos=$this->DevuelveValores("porcentajes_iva", "Valor", $ImpuestosPorcentaje/100);
                if($DatosImpuestos["ClaseImpuesto"]<>''){
                    $ImpuestosClase=$DatosImpuestos["ClaseImpuesto"];
                }
                $LayoutImpuestos.="(IMP)

                                        IMP_1:".$ImpuestosClase.";

                                        IMP_2:".$ImpuestosBase.";

                                        IMP_3:".$ImpuestosMonedaBase.";

                                        IMP_4:".$ImpuestosTotalItemImpuesto.";

                                        IMP_5:".$ImpuestosMoneda.";

                                        IMP_6:".$ImpuestosPorcentaje.";

                                    (/IMP)
                                    ";
            
            }
            //Impuestos
        
            
            

        }
        if($OtrosImpuestos>0){
            $TotalImpuestos=$TotalImpuestos+$OtrosImpuestos;
            
        }

        
        $FacturaSubtotal=round($SubtotalFactura,2);
        $FacturaMonedaSubtotal="COP";
        $FacturaBaseImpuestos=round($TotalBases,2);
        $FacturaMonedaBaseImpuestos="COP";
        $FacturaTotalSinImpuestosRetenidos=round($TotalFactura,2);
        $FacturaMonedaTotalSinImpuestosRetenidos="COP";
        $FacturaTotal=round($TotalFactura,2);
        $FacturaMonedaTotal="COP";
        $FacturaTotalDescuentos=0;
        $FacturaMonedaDescuentos="COP";
        $FacturaTotalCargos=0;
        $FacturaMonedaCargos="COP";
        
        
        
        //Tasa de Cambio
        
        $TasaDeCambioModena="COP";
        $FactorTasaCambio=1;
        
        //Descuentos
        
        $DescuentoTipo="false"; //false descuento, true cargo
        $PorcentajeDescuento=0.0;
        $ValorDescuento=0.0;
        $ModedaDescuento="COP";
        $CodigoDescuento=19;
        $IndicadorSecuenciaCalculo=1;
        //Total impuestos
        
        $ImpuestosTipo="false";//false para IVA o ImpoConsumo
        $ImpuestosTotal=round($TotalImpuestos,2);
        
        $TotalesImpuestos=round($TotalImpuestos,2);
        
        $TotalFactura= round($TotalFactura,2);
        $MonedaTotal="COP";

        //Referencias, se refiere a los documentos enviados por el proveedor ejemplo cotizaciones, ordenes de compra, etc

        $ReferenciaTipo="IV";//IV factura,NC Nota Credito, ND Nota debito
        $NumReferencia="0000200216";
        $FechaDocumentoReferencia="2017-09-26";
        
        //Codigo de la plantailla, informacion para carvajal
        
        $CodigoPlantilla="CGEN01";
        
        
        $CodigoMonedaCambio="COP";
        $TotalImporteBrutoMonedaCambio=round($TotalFactura,2);

        //Items
        
        
        
        $sql="SELECT Referencia,Nombre,ValorUnitarioItem,Cantidad,SubtotalItem FROM facturas_items WHERE idFactura='$idFactura'";
        $Consulta=$this->Query($sql);
        $ItemConsecutivo=0;
        //Descuentos items
        
        $ItemDescuentoTipo="false"; //false descuento,true cargo
        $TotalDescuentoItem=0.00;
        $MonedaDescuentoItem="COP";  
        $LayoutItems="";
        while($DatosItems=$this->FetchAssoc($Consulta)){
            $ItemConsecutivo++; //Consecutivo del item
            $TipoItem='false'; //true si el item es gratis, false si se cobra}
            $ItemCantidad=$DatosItems["Cantidad"];
            $UnidadMedida="ST"; //Ver tabla 12
            $TotalItem=round($DatosItems["SubtotalItem"],2);
            $MonedaItem="COP";
            $PrecioUnitarioItem=round($DatosItems["ValorUnitarioItem"],2);
            $MonedaItem="COP";
            $ReferenciaItem=$DatosItems["Referencia"];
            $NombreItem=$DatosItems["Nombre"];
            $UnidadMedidaEmpaque="CR";
            $TotlItemConCargos=$TotalItem;
            
            $LayoutItems.="(ITE)

                                ITE_1:".$ItemConsecutivo.";

                                ITE_2:".$TipoItem.";

                                ITE_3:".$ItemCantidad.";

                                ITE_4:".$UnidadMedida.";

                                ITE_5:".$TotalItem.";

                                ITE_6:".$MonedaItem.";

                                ITE_7:".$PrecioUnitarioItem.";

                                ITE_8:".$MonedaItem.";

                                ITE_11:".$ReferenciaItem.";

                                ITE_12:".$NombreItem.";

                                ITE_14:".$UnidadMedidaEmpaque.";

                                ITE_19:".$TotlItemConCargos.";

                                ITE_20:".$MonedaItem.";

                                ITE_21:".$TotalItem.";

                                ITE_22:".$MonedaItem.";


                                (IDE)

                                    IDE_1:".$ItemDescuentoTipo.";

                                    IDE_2:".$TotalDescuentoItem.";

                                    IDE_3:".$MonedaDescuentoItem.";

                                    IDE_8:".$MonedaDescuentoItem.";

                                (/IDE)

                         (/ITE)";
            
        }
               
        $param['LayOut'] = "[".$NitEmisor."]
            [$NitEmisorCompleto]
            [NO]
            [".$DocumentoFE."]
            [".$UserWebService."]
            [".$PassWebService."]
            (ENC)

                    ENC_1:".$TipoDocumentoFE.";

                    ENC_2:".$NitEmisor.";  

                    ENC_3:".$NitAdquiriente.";  

                    ENC_4:".$UBLVersion.";

                    ENC_5:".$VersionFormatoDocumento.";

                    ENC_6:".$FacturaCompleta."; 

                    ENC_7:".$FechaFactura.";

                    ENC_8:".$HoraFactura.";

                    ENC_9:".$TipoFactura.";

                    ENC_10:".$MonedaFactura.";

                    ENC_16:".$FechaVencimiento.";
            (/ENC)

            (EMI)

                    EMI_1:".$EmisorTipoPersona.";

                    EMI_2:".$NitEmisor."; 

                    EMI_3:".$EmisorTipoDocumento.";

                    EMI_4:".$EmisorNumTipoRegimen.";

                    EMI_6:".$EmisorRazonSocial.";

                    EMI_10:".$EmisorDireccion.";

                    EMI_11:".$EmisorDepartamento.";

                    EMI_12:".$EmisorBarrio.";

                    EMI_13:".$EmisorCiudad.";

                    EMI_15:".$EmisorCodigoPais.";

                    EMI_19:".$EmisorDepartamento.";

                    EMI_20:".$EmisorCiudad.";

                    EMI_21:".$EmisorPais.";

                    ".$TAC."

                    (ICC)

                            ICC_1:".$EmisorMatriculaMercantil.";

                    (/ICC)

            (/EMI)
            
            (DRF)

                    DRF_1:".$NumeroResolucion.";

                    DRF_2:".$FechaInicioResolucion.";

                    DRF_3:".$FechaFinResolucion.";

                    DRF_4:".$PrefijoResolucion.";

                    DRF_5:".$RangoInicialResolucion.";

                    DRF_6:".$RangoFinalResolucion.";      

            (/DRF)
            
            (NOT)

                    NOT_1: ".$NotasLegales.";

            (/NOT)

            (REF)

                    REF_1:".$ReferenciaTipo.";

                    REF_2:".$NumReferencia.";

                    REF_3:".$FechaDocumentoReferencia.";

            (/REF)

            (CTS)

                    CTS_1:".$CodigoPlantilla.";

            (/CTS)
            
            (ADQ)

                    ADQ_1:".$AdqTipoPersona.";

                    ADQ_2:".$AdqNit.";

                    ADQ_3:".$AdqTipoDocumento.";

                    ADQ_4:".$adqNumTipoRegimen.";

                    ADQ_6:".$AdqRazonSocial.";

                    ADQ_8:".$AdqNombres.";

                    ADQ_9:".$adqApellidos.";

                    ADQ_10:".$AdqDireccion.";

                    ADQ_11:".$AdqDepartamento.";

                    ADQ_12:".$AdqBarrio.";

                    ADQ_13:".$AdqCiudad.";

                    ADQ_15:".$AdqCodigoPais.";

                    ADQ_17:".$AdqCodigoComercio.";

                    (TCR)

                            TCR_1:".$AdqInfoTributariaAduana.";

                    (/TCR)

                    (ICR/)
                    (CDA)
                            CDA_1:".$AdqContactoTipo.";
                            CDA_2:".$AdqContactoNombre.";
                            CDA_3:".$AdqContactoTelefono.";
                            CDA_4:".$AdqContactoMail.";
                    (/CDA)
            (/ADQ)

            (TOT)

                    TOT_1:".$FacturaSubtotal.";

                    TOT_2:".$FacturaMonedaSubtotal.";

                    TOT_3:".$FacturaBaseImpuestos.";

                    TOT_4:".$FacturaMonedaBaseImpuestos.";

                    TOT_5:".$FacturaTotalSinImpuestosRetenidos.";

                    TOT_6:".$FacturaMonedaTotalSinImpuestosRetenidos.";

                    TOT_7:".$FacturaTotal.";

                    TOT_8:".$FacturaMonedaTotal.";
                        
                    TOT_9:".$FacturaTotalDescuentos.";
                    
                    TOT_10:".$FacturaMonedaDescuentos.";
                    
                    TOT_11:".$FacturaTotalCargos.";

                    TOT_12:".$FacturaMonedaCargos.";

            (/TOT)

            (TIM)

                    TIM_1:".$ImpuestosTipo.";

                    TIM_2:".$ImpuestosTotal.";

                    TIM_3:".$ImpuestosMoneda.";

                    $LayoutImpuestos

            (/TIM)

            (TDC)

                    TDC_1:".$TasaDeCambioModena.";

                    TDC_2:".$TasaDeCambioModena.";

                    TDC_3:".$FactorTasaCambio.";

            (/TDC)

            (DSC)

                    DSC_1:".$DescuentoTipo.";

                    DSC_2:".$PorcentajeDescuento.";

                    DSC_3:".$ValorDescuento.";

                    DSC_4:".$ModedaDescuento.";
                    
                    DSC_5:".$CodigoDescuento.";

                    DSC_8:".$ModedaDescuento.";

                    DSC_9:".$IndicadorSecuenciaCalculo.";

            (/DSC)

            (ITD)

                    ITD_1:".$TotalesImpuestos.";

                    ITD_2:".$ImpuestosMoneda.";

                    ITD_5:".$TotalFactura.";

                    ITD_6:".$MonedaTotal.";

            (/ITD)
            
            $LayoutItems

            [/FACTURA]";
        
        return($param);
    }
}