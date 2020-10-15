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
        $Datos["forma_pago"]=1; 
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
    
    public function crear_vista_documentos_electronicos($db) {
        $principalDb=DB;
        $sql="DROP VIEW IF EXISTS `vista_documentos_electronicos`;";
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
        
        $sql="CREATE VIEW vista_documentos_electronicos AS
                SELECT t1.*,                    
                    (SELECT name FROM $principalDb.api_fe_tipo_documentos t3 WHERE t3.ID=t1.tipo_documento_id LIMIT 1) AS nombre_tipo_documento,
                    (SELECT razon_social FROM terceros t4 WHERE t4.ID=t1.tercero_id LIMIT 1) AS nombre_tercero, 
                    (SELECT identificacion FROM terceros t4 WHERE t4.ID=t1.tercero_id LIMIT 1) AS nit_tercero,
                    (SELECT CONCAT(Nombre,' ',Apellido) FROM $principalDb.usuarios t5 WHERE t5.ID=t1.usuario_id LIMIT 1) AS nombre_usuario 
                    
                FROM `documentos_electronicos` t1 ORDER BY updated DESC ";
        
        $this->QueryExterno($sql, HOST, USER, PW, $db, "");
    }
    
    /**
     * Fin Clase
     */
}
