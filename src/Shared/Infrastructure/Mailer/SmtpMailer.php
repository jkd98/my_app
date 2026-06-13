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
        $mail = new PHPMailer(true);
        try {
            
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->host;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $this->user;                     //SMTP username
            $mail->Password   = $this->pass;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS

            $mail->setFrom($this->user, 'Mailer');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}