<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Application\DTO\LoginRequestDTO;
use App\Auth\Application\DTO\LoginResponseDTO;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\RawPassword;
use App\Auth\Domain\Service\PasswordHashInterface;
use App\Auth\Application\Security\TokenGeneratorInterface;
use App\Auth\Domain\Exception\InvalidCredentialsException;

final class Login {
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHashInterface $passwordHash,
        private readonly TokenGeneratorInterface $tokenGenerator
    ){}

    public function login(LoginRequestDTO $data): LoginResponseDTO {
        // Buscar al usuario
        $userExist = $this->userRepository->findByEmail(Email::create($data->email()));
        if(!$userExist) throw new InvalidCredentialsException();
        // Validar la confirmacion de la cuenta
        if(!$userExist->isVerified()) throw new InvalidCredentialsException("La cuenta no ha sido confirmada");
        // Validar la contraseña
        if( !$this->passwordHash->verify(RawPassword::fromString($data->rawPassword()),$userExist->password()) ) throw new InvalidCredentialsException();
        // Generar el token y retornarlo
        $token = $this->tokenGenerator->generate($userExist->userId());
        return new LoginResponseDTO($token);
    }
}