<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Application\DTO\RegisterUserRequestDTO;

use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\UserName;
use App\Auth\Domain\ValueObject\LastName;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\Password;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\Events\UserRegistered;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Entity\VerificationToken;
use App\Auth\Domain\Service\VerifyEmailExist;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Repository\VerificationTokenRepository;

final class RegisterUser {
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly VerificationTokenRepository $verificationTokenRepository,
        private readonly PasswordHashInterface $passwordHashed
    ){}

    public function register(RegisterUserRequestDTO $userData){
        // Tranformar datos a los VO correspondientes
        $userId = UserId::generate();
        $userName = UserName::create($userData->userName());
        $lastName = LastName::create($userData->lastName());
        $email = Email::create($userData->email());
        
        // Validar la existencia del email
        VerifyEmailExist::execute($email);
        
        // Hashear la contraseña
        $passHash = $this->passwordHashed::hash($userData->rawPassword());
        $password = Password::create($passHash);

        // Instanciar la entidad
        $nwUser = User::register(
            $userId,
            $userName,
            $lastName,
            $email,
            $password
        );

        // Generar el token de verificación
        $nwToken = VerificationToken::create(
            TokenType::EmailConfirmation,
            $userId
        );

        // Enviar la instancia de usuario al repository
        $this->userRepository->save($nwUser);
        // Enviar la instancia del token al repository
        $this->verificationTokenRepository->save($nwToken);

        

    }
}