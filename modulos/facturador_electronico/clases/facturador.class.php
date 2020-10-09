<?php
if(file_exists("../../../modelo/php_conexion.php")){
    include_once("../../../modelo/php_conexion.php");
}
class Facturador extends conexion{
    
    
    public function agregar_prefactura($db,$usuario_id) {
        $sql="SELECT COUNT(*) as total FROM $db.factura_prefactura WHERE  usuario_id='$usuario_id' ";
        
        $datos_validacion=$this->FetchAssoc($this->Query($sql));
        if($datos_validacion["total"]>=3){
            exit("E1;No puedes crear mas de 3 prefacturas");
        }
        $Tabla="factura_prefactura";
        $this->ActualizaRegistro($db.".".$Tabla, "activa", 0, "usuario_id", "$usuario_id");          
        $Datos["usuario_id"]=$usuario_id;        
        $Datos["activa"]=1;        
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    public function agregar_item_prefactura($prefactura_id,$db,$item_id,$precio,$cantidad,$impuestos_incluidos,$usuario_id) {
        
        $datos_item=$this->DevuelveValores($db.".inventario_items_general", "ID", $item_id);
        if($datos_item["ID"]==''){
            exit("E1;El Código enviado no existe en la base de datos");
        }
        $datos_impuestos=$this->DevuelveValores("porcentajes_iva", "ID", $datos_item["porcentajes_iva_id"]);
        $valor_unitario=$datos_item["Precio"];
        if($precio<>''){
            $valor_unitario=$precio;
        }
        if($impuestos_incluidos==1){
            
            $valor_unitario=($valor_unitario/($datos_impuestos["FactorMultiplicador"]+1));
            
        }
        $subtotal=$valor_unitario*$cantidad;
        $impuestos=($subtotal*$datos_impuestos["FactorMultiplicador"]);
        $total=$subtotal+$impuestos;
        $Tabla="factura_prefactura_items";
               
        $Datos["prefactura_id"]=$prefactura_id;        
        $Datos["item_id"]=$item_id;  
        $Datos["valor_unitario"]=$valor_unitario;     
        $Datos["cantidad"]=$cantidad;     
        $Datos["subtotal"]=$subtotal;     
        $Datos["impuestos"]=$impuestos;     
        $Datos["total"]=$total; 
        $Datos["porcentaje_iva_id"]=$datos_item["porcentajes_iva_id"];     
        $Datos["usuario_id"]=$usuario_id; 
        
        $sql=$this->getSQLInsert($Tabla, $Datos);
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    
    /**
     * Fin Clase
     */
}
