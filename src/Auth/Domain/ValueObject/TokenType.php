<?php

namespace App\Auth\Domain\ValueObject;

enum TokenType: string {
    case EmailConfirmation = 'email_confirmation';
    case PasswordRecovery = 'password_recovery';
}