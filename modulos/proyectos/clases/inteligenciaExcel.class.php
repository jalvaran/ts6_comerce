<?php
/* 
 * Clase donde se realizaran la generacion de archivos en excel general
 * Julian Alvaran 
 * Techno Soluciones SAS
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}

class ExcelInteligencia extends ProcesoVenta{
    
    // Clase para generar excel de un balance de comprobacion
    
    public function ListadoClientesExcel($Condicion) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getActiveSheet()->getStyle('H:K')->getNumberFormat()->setFormatCode('#,##0');
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
            
        ];
                
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","LISTADO DE CLIENTES")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:M1');
        //$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->applyFromArray( [ 'borderStyle' => Border::BORDER_DASHDOT, 'color' => [ 'rgb' => '808080' ] ] ); 
        //$objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleTitle);
        $z=0;
        $i=3;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($Campos[$z++].$i,"ID")
            ->setCellValue($Campos[$z++].$i,"Razon Social")
            ->setCellValue($Campos[$z++].$i,"NIT")    
            ->setCellValue($Campos[$z++].$i,"Direccion")
            ->setCellValue($Campos[$z++].$i,"Telefono")
            ->setCellValue($Campos[$z++].$i,"Email")
            ->setCellValue($Campos[$z++].$i,"Cumpleaños")
            ->setCellValue($Campos[$z++].$i,"Puntaje")
            ->setCellValue($Campos[$z++].$i,"Creado")
            ->setCellValue($Campos[$z++].$i,"Actualizado")                        
            ;
            
        $sql="SELECT * FROM clientes $Condicion";
        $Consulta=$this->Query($sql);
        $i=3;
        while($DatosVista= $this->FetchAssoc($Consulta)){
            
            $i++;
            $z=0;
            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,$DatosVista["idClientes"])
                ->setCellValue($Campos[$z++].$i, utf8_encode($DatosVista["RazonSocial"]))
                ->setCellValue($Campos[$z++].$i,$DatosVista["Num_Identificacion"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Direccion"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Telefono"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Email"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["DiaNacimiento"]." de ".$DatosVista["MesNacimiento"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Puntaje"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Created"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["Updated"])
                ;
            
        }
        
        
        $objPHPExcel->getActiveSheet()->getStyle("A3:M3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth('10');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth('45');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth('22');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(4)->setWidth('28');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(5)->setWidth('16');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(6)->setWidth('30');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(7)->setWidth('13');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(8)->setWidth('10');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(9)->setWidth('18');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(10)->setWidth('18');
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("Lista de Clientes")
        ->setSubject("Clientes")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("Lista de Clientes");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."Clientes".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    public function ListadoProductosClientes($Condicion) {
        require_once('../../../librerias/Excel/PHPExcel2.php');
        
        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getActiveSheet()->getStyle('C:D')->getNumberFormat()->setFormatCode('#,##0');
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
            
        ];
                
        $Campos=["A","B","C","D","E","F","G","H","I","J","K","L","M",
                 "N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB"];
        
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A1","LISTADO DE PRODUCTOS ADQUIRIDOS POR LOS CLIENTES")
             
                ;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:M1');
        //$objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->applyFromArray( [ 'borderStyle' => Border::BORDER_DASHDOT, 'color' => [ 'rgb' => '808080' ] ] ); 
        //$objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleTitle);
        $z=0;
        $i=3;
        $objPHPExcel->setActiveSheetIndex(0)
            
            ->setCellValue($Campos[$z++].$i,"Referencia")
            ->setCellValue($Campos[$z++].$i,"Nombre")    
            ->setCellValue($Campos[$z++].$i,"Cantidad")
            ->setCellValue($Campos[$z++].$i,"Total")
            ->setCellValue($Campos[$z++].$i,"Cliente")
            ->setCellValue($Campos[$z++].$i,"Identificacion")
            ->setCellValue($Campos[$z++].$i,"FormaPago")
                                                
            ;
            
        $sql="SELECT * FROM vista_productos_x_cliente $Condicion ORDER BY Cantidad DESC";
        $Consulta=$this->Query($sql);
        $i=3;
        while($DatosVista= $this->FetchAssoc($Consulta)){
            
            $i++;
            $z=0;
            $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue($Campos[$z++].$i,utf8_encode($DatosVista["Referencia"]))
                ->setCellValue($Campos[$z++].$i, utf8_encode($DatosVista["Nombre"]))
                ->setCellValue($Campos[$z++].$i,$DatosVista["Cantidad"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["TotalItem"])
                ->setCellValue($Campos[$z++].$i,utf8_encode($DatosVista["RazonSocial"]))
                ->setCellValue($Campos[$z++].$i,$DatosVista["Num_Identificacion"])
                ->setCellValue($Campos[$z++].$i,$DatosVista["FormaPago"])

                ;
            
        }
        
        
        $objPHPExcel->getActiveSheet()->getStyle("A3:G3")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth('15');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(2)->setWidth('70');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth('10');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(4)->setWidth('14');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(5)->setWidth('40');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(6)->setWidth('14');
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(6)->setWidth('30');
        
        
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com.co")
        ->setLastModifiedBy("www.technosoluciones.com.co")
        ->setTitle("Lista de Clientes")
        ->setSubject("Clientes")
        ->setDescription("Documento generado por Techno Soluciones SAS")
        ->setKeywords("techno soluciones sas")
        ->setCategory("Lista de Clientes");    
 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'."ProductosVendidos".'.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objWriter=IOFactory::createWriter($objPHPExcel,'Xlsx');
    $objWriter->save('php://output');
    exit; 
   
    }
    
    
   //Fin Clases
}
    