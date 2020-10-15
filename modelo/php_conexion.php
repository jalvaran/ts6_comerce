<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'php_mysql_i.php';

class conexion extends db_conexion{
    
    public $idUser;
    public $TipoUser;
    
    function __construct($idUserR){
        
        $idUserR=$this->normalizar($idUserR);		
        $this->consulta =$this->Query("SELECT Nombre, TipoUser FROM usuarios WHERE ID='$idUserR'");
        $this->fetch = $this->FetchArray($this->consulta);
        $this->NombreUser = $this->fetch['Nombre'];
        $this->idUser=$idUserR;
        $this->TipoUser=$this->fetch['TipoUser'];
        $this->COMBascula="/dev/ttyUSB0";
        $this->COMPrinter="COM3";
        
    }
    
    public function getUniqId($prefijo='') {
        return (str_replace(".","",uniqid($prefijo, true)));
    }
     
    public function getDataBaseLocal($idLocal) {
        $sql="SELECT db FROM locales WHERE ID='$idLocal'";
        $Datos= $this->FetchAssoc($this->Query($sql));
        return($Datos["db"]);
    }
    
    public function logVisit($client_user_id,$idPantalla,$idLocal,$IP,$city_id) {
        if($client_user_id<>''){
            
       
            $tab="log_visits";
            $Datos["client_user_id"]=$client_user_id;
            $Datos["idPantalla"]=$idPantalla;
            $Datos["idLocal"]=$idLocal;
            $Datos["IP"]=$IP;
            $Datos["city_id"]=$city_id;
            $Datos["Created"]=date("Y-m-d H:i:s");
            $sql= $this->getSQLInsert($tab, $Datos);
            $this->Query($sql);
         }
        
    }
    
    public function validaTokenGoogle($token,$action,$Key) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $Key, 'response' => $token)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);
        return($arrResponse);
    }
    
    public function VerificaSesion($Token_user) {
        if(isset($_SESSION["idLocal"])){
            //if($_SESSION["Token"]==$Token_user){
                $DatosSesion["Estado"]="OK";
                $DatosSesion["Mensaje"]="Sesion iniciada correctamente";
            //}else{
              //  $DatosSesion["Estado"]="E1";
                //$DatosSesion["Mensaje"]="El token ha cambiado, debe iniciar sesion de nuevo";
            //}
            
        }else{
            $DatosSesion["Estado"]="E1";
            $DatosSesion["Mensaje"]="No se ha iniciado sesion";
        }
        return($DatosSesion);
    }
    
    
        
public function VerificaPermisos($VectorPermisos) {
    if($this->TipoUser<>"administrador"){
        $Page=$VectorPermisos["Page"];
        
        $Consulta=  $this->ConsultarTabla("paginas_bloques", " WHERE Pagina='$Page' AND TipoUsuario='$this->TipoUser' AND Habilitado='SI'");
        $PaginasUser=  $this->FetchArray($Consulta);
        if($PaginasUser["Pagina"]==$Page){
            return true;
        }
        return false;
    }
    return true;
}

    //calcular DV
    public function CalcularDV($nit) {
        $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19, 
        8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';
        
        for ($i=0; $i<$z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y*$arr[$z-$i]);
        }
        
        $y = $x%11;
        
        if ($y > 1) {
            $dv = 11-$y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }
    }
    
    public function SumeDiasFecha($Fecha,$Dias){		
        
        $nuevafecha = date('Y-m-d', strtotime($Fecha) + 86400);
        $nuevafecha = date('Y-m-d', strtotime("$Fecha + $Dias day"));

        return($nuevafecha);

    }
    
    public function obtenerDiferenciaFechasDias($Fecha1,$Fecha2) {
        $date1 = new DateTime($Fecha1);
        $date2 = new DateTime($Fecha2);
        $diff = $date1->diff($date2);        
        return($diff->days);
    }
    
    public function getColumns($table) {
        $Cols=$this->ShowColums($table);
        
        foreach ($Cols["Field"] as $key => $value) {
            
            $sql="SELECT muestre FROM tablas_nombres_campos WHERE nombreOriginalCampo='$value'";
            $dataQuery=$this->FetchAssoc($this->Query($sql));
            if($dataQuery["muestre"]==''){
                $Cols["titleField"][$key]=$value;
            }else{
                $Cols["titleField"][$key]=$dataQuery["muestre"];
            }
        }
        return($Cols);
        
    }
    
}
