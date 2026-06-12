<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Domain\Service\PasswordHashInterface;
use App\Auth\Domain\ValueObject\Password;
use App\Auth\Domain\ValueObject\RawPassword;

final class PasswordHash implements PasswordHashInterface {
    private array $options = [
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST, // Memoria en bytes
            'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,   // Número de iteraciones
            'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,     // Hilos de procesamiento
    ];

    public function hash(RawPassword $rawPassword):Password{
        $password = password_hash($rawPassword->value(),PASSWORD_ARGON2ID,$this->options);
        return Password::create($password);
    }

    public function verify(RawPassword $rawPassword, Password $passwordHashed):bool{
        return password_verify($rawPassword->value(),$passwordHashed->value());
    }

    public function needsRehash(Password $password): bool {
        return password_needs_rehash($password->value(),PASSWORD_ARGON2ID,$this->options);
    }
}