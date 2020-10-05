<?php

include_once("modelo/php_conexion.php");

/* 
 * Clase que realiza los procesos de facturacion electronica
 * Julian Alvaran
 * Techno Soluciones SAS
 */

class TS_Telegram extends conexion{
    
    public function EnviarMensajeTelegram($idTelefono,$msg,$TelegramToken) {
        
        $msg= str_replace("*", " ", $msg);
        $msg= str_replace("[", " ", $msg);
        $msg= str_replace("]", " ", $msg);
        $msg= str_replace("(", " ", $msg);
        $msg= str_replace(")", " ", $msg);
        $msg= str_replace("~", " ", $msg);
        $msg= str_replace("`", " ", $msg);
        $msg= str_replace("#", " ", $msg);
        $msg= str_replace("+", " ", $msg);
        $msg= str_replace("-", " ", $msg);
        $msg= str_replace("|", " ", $msg);
        $msg= str_replace("{", " ", $msg);
        $msg= str_replace("}", " ", $msg);
        $msg= str_replace("!", " ", $msg);
        $msg= str_replace("&", " ", $msg);
        $token = $TelegramToken;
        $id = $idTelefono;
        $urlMsg = "https://api.telegram.org/bot{$token}/sendMessage";
        //$msg = '<a href="http://www.domibuga.com/domi/general/Consultas/PDF_Documentos.draw.php?idPedido=15e9688cdaa4eb172260897"> ver pedido</a>';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlMsg);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id={$id}&parse_mode=HTML&text=$msg");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);
        return($server_output);
    }
    
}