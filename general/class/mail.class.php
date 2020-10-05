<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once("modelo/php_conexion.php");

/* 
 * Clase que realiza los procesos de facturacion electronica
 * Julian Alvaran
 * Techno Soluciones SAS
 */

class TS_Mail extends conexion{
    
    public function EnviarMailXPHPNativo($para,$de,$nombreRemitente, $asunto, $mensajeHTML, $Adjuntos='') {
        
        //$DatosParametrosFE=$this->DevuelveValores("facturas_electronicas_parametros", "ID", 4);
        
        //recipient
        $to = $para;

        //sender
        $from = $de;
        $fromName = $nombreRemitente;

        //email subject
        $subject = $asunto; 
        //email body content
        $htmlContent = $mensajeHTML;

        //header for sender info
        $headers = "From: $fromName"." <".$from.">";

        //boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

        //headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

        //multipart boundary 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 

        //preparing attachment
        if($Adjuntos<>''){
            foreach($Adjuntos as $file){
                if(!empty($file) > 0){
                    if(is_file($file)){
                        $message .= "--{$mime_boundary}\n";
                        $fp =    @fopen($file,"rb");
                        $data =  @fread($fp,filesize($file));

                        @fclose($fp);
                        $data = chunk_split(base64_encode($data));
                        $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
                        "Content-Description: ".basename($file)."\n" .
                        "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
                        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                    }
                }
            }
        }
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $from;

        //send email
        $mail = @mail($to, $subject, $message, $headers, $returnpath); 

        //email sending status
        return $mail?"OK":"E1";
        
    }
    
    public function EnviarMailXPHPMailer($para,$de,$nombreRemitente, $asunto, $mensajeHTML, $Adjuntos='') {
        
        require 'librerias/phpmailer/src/Exception.php';
        require 'librerias/phpmailer/src/PHPMailer.php';
        require 'librerias/phpmailer/src/SMTP.php';

        /*
        Primero, obtenemos el listado de e-mails
        desde nuestra base de datos y la incorporamos a un Array.
        */
        $email=$para;
        $name="";
        $email_from=$de;
        $name_from=$nombreRemitente;
        $mail = new PHPMailer(true);
        
        $DatosSMTP=$this->DevuelveValores("configuracion_correos_smtp", "ID", 1);
        $mail->IsSMTP();//telling the class to use SMTP
        $mail->SMTPAuth = true;//enable SMTP authentication
        $mail->SMTPSecure = $DatosSMTP["SMTPSecure"];//sets the prefix to the servier
        $mail->Host = $DatosSMTP["Host"];//sets GMAIL as the SMTP server
        $mail->Port = $DatosSMTP["Port"];//set the SMTP port for the GMAIL server
        $mail->Username = $DatosSMTP["Username"];//GMAIL username
        $mail->Password = $DatosSMTP["Password"];//GMAIL password
        

        // Typical mail data
        $Destinatarios= explode(",", $email);
        foreach ($Destinatarios as $value) {
            $mail->AddAddress($value, $name);
        }
        
        $mail->SetFrom($email_from, $name_from);
        $mail->IsHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHTML;
        if($Adjuntos<>''){
            foreach ($Adjuntos as $value) {
                $Vector=explode('/',$value);
                $Total=count($Vector);
                $NombreArchivo=$Vector[$Total-1];
                $mail->AddAttachment($value,$NombreArchivo);
            }
        }
        
        
        try{
            $mail->Send();
            return("OK");
        } catch(Exception $e){           
            return("E1");
        }
        
    }
    
    //Fin Clases
}