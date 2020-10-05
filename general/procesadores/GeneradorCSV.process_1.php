<?php 
session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}

if(isset($_REQUEST["Opcion"])){
    $myPage="GeneradorCSV.php";
    include_once("../clases/administrador.class.php");
    
       
    $idUser=$_SESSION['idUser'];
    $obCon = new Administrador($idUser);
    
    $DatosRuta=$obCon->DevuelveValores("configuracion_general", "ID", 1);
    $OuputFile=$DatosRuta["Valor"];
    $Link1=substr($OuputFile, -17);
    $Link="../../".$Link1;
    //print($Link);
    $a='"';
    $Enclosed=" ENCLOSED BY '$a' ";
    $Opcion=$_REQUEST["Opcion"];
    
    switch ($Opcion){
        case 1: //Exportar CSV 
            if(file_exists($Link)){
                unlink($Link);
            }
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            
            $Condicion=$obCon->normalizar($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $Separador=$obCon->normalizar($_REQUEST["Separador"]);
            $NumPage="";
            $limit="";
            $startpoint="";
            $ColumnasSeleccionadas=$obCon->getColumnasVisibles($Tabla, "");  
            
            $DatosConsulta=$obCon->getConsultaTabla($Tabla,$ColumnasSeleccionadas, $Condicion, $OrdenColumna, $AscDesc, $NumPage, $limit,$startpoint);
           
            $TotalRegistros=$DatosConsulta["TotalRegistros"];
            $QueryCompleto=$DatosConsulta["QueryCompleto"];
            $QueryParcial=$DatosConsulta["QueryParcial"];
            
            
            $idTabla=$ColumnasSeleccionadas["Field"][0];        
            if($Condicion<>""){
                $Condicion=" WHERE ".$Condicion;
            }
            if($OrdenColumna==''){
                $OrdenColumna=$idTabla;
            }
            
            $Orden=" ORDER BY $OrdenColumna $AscDesc ";
            
            
            
            $sqlColumnas="SELECT ";
            $CamposShow="";
            foreach($ColumnasSeleccionadas["Field"] as $key => $value){
                $Titulo= utf8_encode($ColumnasSeleccionadas["Visualiza"][$key]);                
                $sqlColumnas.="'$Titulo' ,";
                $CamposShow.=" CONVERT(`$value` USING utf8mb4),"; 
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $CamposShow=substr($CamposShow, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            $Indice=$ColumnasSeleccionadas["Field"][0];
            
            //$sql=$sqlColumnas."SELECT $CamposShow FROM $Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            $sql=$sqlColumnas."$QueryParcial $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            
            $obCon->Query($sql);
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$Tabla.csv' target='_top' style='text-align:center;position: absolute;top:50%;left:50%;padding:5px;' onclick=document.getElementById('DivImagenDescargarTablaDB').style.display='none';><h1>Descargar: </h1><img src='../../images/descargar3.png'></img></a></div>");
            break;//Fin Caso 1
            
            case 2: //Exportar CSV directamente
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $FileName=$Tabla."_".$idUser.".csv";
            $Link.= $FileName;
            $OuputFile.=$FileName;
            
            if(file_exists($Link)){
                unlink($Link);
            }
                       
            $Condicion="";
            
            $Separador=";";
            $NumPage="";
            $limit="";
            $startpoint="";
            
            
            $sqlColumnas="SELECT  ";
            $Columnas=$obCon->ShowColums($Tabla);
            //print_r($Columnas);
            foreach ($Columnas["Field"] as $key => $value) {
                $sqlColumnas.="'$value',";
            }
            $sqlColumnas=substr($sqlColumnas, 0, -1);
            $sqlColumnas.=" UNION ALL ";
            
            $sql=$sqlColumnas." SELECT * FROM $Tabla $Condicion INTO OUTFILE '$OuputFile' FIELDS TERMINATED BY '$Separador' $Enclosed LINES TERMINATED BY '\r\n';";
            $Fecha=date("Ymd_His");
            $obCon->Query($sql);
            
            $NombreArchivo=$Tabla;
            print("<div id='DivImagenDescargarTablaDB'><a href='$Link' download='$NombreArchivo.csv' target='_top' ><h1>Descargar</h1></a></div>");
            break;//Fin caso 2
        
        }
}else{
    print("No se recibió parametro de opcion");
}

?>