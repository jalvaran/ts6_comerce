<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use \Mailjet\Resources;

if(file_exists("../../modelo/php_conexion.php")){
    include_once("../../modelo/php_conexion.php");
}
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
    
    public function EnviarMailXPHPMailer($datos_empresa,$para,$de,$nombreRemitente, $asunto, $mensajeHTML, $Adjuntos='') {
        
        require '../../../librerias/phpmailer/src/Exception.php';
        require '../../../librerias/phpmailer/src/PHPMailer.php';
        require '../../../librerias/phpmailer/src/SMTP.php';
        $empresa_id=$datos_empresa["ID"];
        /*
        Primero, obtenemos el listado de e-mails
        desde nuestra base de datos y la incorporamos a un Array.
        */
        $email=$para;
        $name="";
        $email_from=$de;
        $name_from=$nombreRemitente;
        $mail = new PHPMailer(true);
        $sql="SELECT * FROM configuracion_correos_smtp WHERE empresa_id='$empresa_id'";
        $DatosSMTP=$this->FetchAssoc($this->Query($sql));
        if($DatosSMTP["Username"]==''){
            $DatosSMTP=$this->DevuelveValores("configuracion_correos_smtp", "ID", 1);
        }
        
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
    
    public function enviar_mail_mailjet($datos_empresa,$array_destinatarios,$de,$nombreRemitente, $asunto, $mensajeHTML, $Adjuntos='') {
        require '../../../librerias/mailjet/vendor/autoload.php';
  
        $mj = new \Mailjet\Client('68d3fe4fcfa27fde0f361e4382fd7897','3eb8bbe05a61ba2d7d7fec27a5b8e332',true,['version' => 'v3.1']);
        
        $body["Messages"][0]["From"]["Email"]="notificaciones@technosoluciones.com.co";
        $body["Messages"][0]["From"]["Name"]="Notificaciones TS";
        $i=0;
        foreach ($array_destinatarios as $key => $value) {
            $body["Messages"][0]["To"][$i]["Email"]=$value["mail"];
            $body["Messages"][0]["To"][$i]["Name"]=$value["name"];
            $i=$i+1;
        }
        
        $body["Messages"][0]["Subject"]=$asunto;
        $body["Messages"][0]["TextPart"]="Notificaciones TS";
        $body["Messages"][0]["HTMLPart"]=$mensajeHTML;
        $body["Messages"][0]["CustomID"]="AppGettingStartedTest";
        /*
        print("<pre>");
        print_r($body);
        print("</pre>");
        exit();
         * 
         */
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if($response->success()){
            return("OK");
        }else{
            return("E1");
        }
        //$response->success() && var_dump($response->getData());
    }
    
    public function enviar_mail_sendinblue($datos_empresa,$array_destinatarios,$de,$nombreRemitente, $asunto, $mensajeHTML, $Adjuntos='') {
        require_once('../../../librerias/sendinblue/vendor/autoload.php');

        $credentials = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
        $apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(new GuzzleHttp\Client(),$credentials);
        
        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail([
             'subject' => $asunto,
             'sender' => ['name' => 'Notificaciones TS', 'email' => 'notificaciones@technosoluciones.com.co'],
             'replyTo' => $array_destinatarios[1],
             'to' => $array_destinatarios,
             'htmlContent' => $mensajeHTML,
             'params' => ['bodyMessage' => 'Techno Soluciones SAS']
                 
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            return("OK");
        } catch (Exception $e) {
            //echo $e->getMessage(),PHP_EOL;
            return("E1");
        }
    }
    
    //Fin Clases
}