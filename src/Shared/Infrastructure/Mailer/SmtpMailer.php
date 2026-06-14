<?php

namespace App\Shared\Infrastructure\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use App\Shared\Application\Port\MailerInterface; 
use App\Shared\Application\DTO\MailDTO;


final class SmtpMailer implements MailerInterface {
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $pass
    ){}

    public function send(MailDTO $data):void {
        $appName="JKApp";
        $mail = new PHPMailer(true);
        
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host       = $this->host; //Set the SMTP server to send through
        $mail->SMTPAuth   = true; //Enable SMTP authentication
        $mail->Username   = $this->user; //SMTP username
        $mail->Password   = $this->pass; //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable implicit TLS encryption
        $mail->Port       = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
        $mail->SMTPKeepAlive = true; // agrégalo para mantener la conexión SMTP abierta después de cada email enviado
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $data->subject().' - '.$appName;

        $mail->setFrom($this->user, $appName); //Remitente
        //Content
        $mail->Body = $data->messageBody();
        $mail->AltBody = 'Confirme su cuenta '.$appName;

        foreach ($data->recipients() as $userName => $email) {
            $mail->addAddress($email,$userName);
            try {
                $mail->send();
                error_log("[SmtpMailer]: Email enviado");
            } catch (\Throwable $th) {
                error_log("[SmtpMailer]: Email no enviado".$th->getMessage());
            }
            $mail->clearAddresses();
        }
        $mail->smtpClose();
    }
}