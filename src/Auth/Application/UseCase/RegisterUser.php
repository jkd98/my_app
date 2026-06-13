<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Application\DTO\RegisterUserRequestDTO;

use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\UserName;
use App\Auth\Domain\ValueObject\LastName;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\RawPassword;
use App\Auth\Domain\ValueObject\TokenType;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Entity\VerificationToken;

use App\Auth\Domain\Service\VerifyEmailExist;
use App\Auth\Domain\Service\PasswordHashInterface;

use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;

use App\Shared\Application\Port\EventDispatcherInterface;
use App\Shared\Application\Port\TransactionManagerInterface;


final class RegisterUser {
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
        private readonly PasswordHashInterface $passwordHashed,
        private readonly VerifyEmailExist $verifyEmailExist,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function register(RegisterUserRequestDTO $userData){
        // Tranformar datos a los VO correspondientes
        $userId = UserId::generate();
        $userName = UserName::create($userData->userName());
        $lastName = LastName::create($userData->lastName());
        $email = Email::create($userData->email());
        $rawPassword = RawPassword::create($userData->rawPassword());
        
        // Validar la existencia del email
        $this->verifyEmailExist->execute($email);
        
        // Hashear la contraseña
        $passHash = $this->passwordHashed->hash($rawPassword);

        // Instanciar la entidad
        $nwUser = User::register(
            $userId,
            $userName,
            $lastName,
            $email,
            $passHash
        );

        // Generar el token de verificación
        $nwToken = VerificationToken::create(
            TokenType::EmailConfirmation,
            $userId
        );

        $this->transactionManager->begin();
        try {
            // Enviar la instancia de usuario al repository
            $this->userRepository->save($nwUser);
            // Enviar la instancia del token al repository
            $this->verificationTokenRepository->save($nwToken);
            $this->transactionManager->commit();
        }catch(\Throwable $e){
            $this->transactionManager->rollback();
            throw $e;
        }
        
        // Despachar eventos
        foreach ($nwUser->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

    }
}