<?php

namespace App\Auth\Infrastructure\EventListener;

use App\Shared\Application\Port\EventListenerInterface;
use App\Shared\Application\Port\MailerInterface;
use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Shared\Application\DTO\MailDTO;
use App\Shared\Domain\Event\DomainEventInterface;
use App\Auth\Domain\Events\UserRegistered;
use App\Auth\Domain\ValueObject\TokenType;

final class SendEmailConfirmation implements EventListenerInterface {
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository
    ) {}

    public function handle(DomainEventInterface $event):void {
        if(!$event instanceof UserRegistered) return;

        $verificationToken = $this->verificationTokenRepository->findByTokenTypeAndUserId(
            TokenType::EmailConfirmation,
            $event->userRegisteredId()
        );

        if(!$verificationToken) return;
        $recipients[] = $event->email()->value();
        $body =<<<HTML
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Confirmación de la cuenta</title>
            </head>

            <body
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #13011d; width: 100%;">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#13011d" style="table-layout: fixed;">
                    <tr>
                        <td align="center" style="padding: 20px 10px;">

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
                                <tr>
                                    <td align="left" style="padding: 10px 24px;">
                                        <h1
                                            style="font-size: 32px; color: #ffffff; font-weight: 400; margin: 0; font-family: 'Segoe UI', sans-serif;">
                                            JK<strong style="font-weight: 900;">App</strong>
                                        </h1>
                                    </td>
                                </tr>

                                <tr>
                                    <td height="10"></td>
                                </tr>

                                <tr>
                                    <td align="center" bgcolor="#0d0613" style="padding: 40px 30px; border-radius: 8px;">

                                        <h2
                                            style="font-size: 24px; color: #ffffff; margin: 0 0 20px 0; text-align: center; font-family: 'Segoe UI', sans-serif;">
                                            Confirma que eres tú
                                        </h2>

                                        <p
                                            style="font-size: 16px; color: #ffffff; margin: 0 0 10px 0; text-align: center; line-height: 1.5;">
                                            Gracias por usar <strong>JKApp</strong>
                                        </p>

                                        <p
                                            style="font-size: 16px; color: #ffffff; margin: 0 0 30px 0; text-align: center; line-height: 1.5; padding: 0 10%;">
                                            Para confirmar que eres tú y empezar a usar la aplicación, haz clic en el botón de
                                            abajo.
                                        </p>

                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" bgcolor="#0d166b" style="border-radius: 8px;">
                                                    <a href="example.html?confirmation={$verificationToken->tokenValue()->value()}"
                                                        target="_blank"
                                                        style="font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 15px 30px; display: inline-block; font-family: 'Segoe UI', sans-serif;">
                                                        Confirmar Cuenta
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>

            </html>
        HTML;
        $mailData = new MailDTO(
            $recipients,
            "Confirma tu cuenta",
            $body,
            "Por favor confirme su cuenta para poder usa el sistema"
        );
        $this->mailer->send($mailData);
    }
}