<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

/*
Primero, obtenemos el listado de e-mails
desde nuestra base de datos y la incorporamos a un Array.
*/
$email="jalvaran@gmail.com";
$name="Julian Alvaran";
$email_from="technosolucionesfe@gmail.com";
$name_from="TS5";
$mail = new PHPMailer(true);
$send_using_gmail=1;
// Send mail using Gmail
if($send_using_gmail){
    $mail->IsSMTP();//telling the class to use SMTP
    $mail->SMTPAuth = true;//enable SMTP authentication
    $mail->SMTPSecure = "ssl";//sets the prefix to the servier
    $mail->Host = "smtp.gmail.com";//sets GMAIL as the SMTP server
    $mail->Port = 465;//set the SMTP port for the GMAIL server
    $mail->Username = "technosolucionesfe@gmail.com";//GMAIL username
    $mail->Password = "pirlo1985";//GMAIL password
}

// Typical mail data
$mail->AddAddress($email, $name);
$mail->SetFrom($email_from, $name_from);
$mail->Subject = "My Subject";
$mail->Body = "Mail contents";

try{
    $mail->Send();
    echo "Success!";
} catch(Exception $e){
   //Something went bad
    echo "Fail :( <pre>$e</pre>";
}

?>