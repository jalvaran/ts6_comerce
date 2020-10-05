<?php
/* 
 * Clase donde se realizaran la generacion de archivos en excel para el modulo de inteligencia de negocios
 * Julian Alvaran 
 * Techno Soluciones SAS
 */
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}

if(file_exists("../../librerias/Excel/PHPExcel2.php")){
    include_once("../../librerias/Excel/PHPExcel2.php");
}

class Excel extends conexion{
    
        
   //Fin Clases
}
    