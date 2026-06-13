<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Application\DTO\LoginRequestDTO;
use App\Auth\Application\DTO\LoginResponseDTO;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\RawPassword;
use App\Auth\Domain\Service\PasswordHashInterface;
use App\Auth\Application\Security\TokenGeneratorInterface;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Entity\RefreshToken;
use App\Shared\Application\Port\TransactionManagerInterface;

final class Login {
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHashInterface $passwordHash,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function login(LoginRequestDTO $data): LoginResponseDTO {
        // Buscar al usuario
        $userExist = $this->userRepository->findByEmail(Email::create($data->email()));
        if(!$userExist) throw new InvalidCredentialsException();
        // Validar la confirmacion de la cuenta
        if(!$userExist->isVerified()) throw new InvalidCredentialsException("La cuenta no ha sido confirmada");
        // Validar la contraseña
        if( !$this->passwordHash->verify(RawPassword::fromString($data->rawPassword()),$userExist->password()) ) throw new InvalidCredentialsException();
        
        $this->transactionManager->begin();
        try {
            // Generar el access token 
            $accessToken = $this->tokenGenerator->generate($userExist->userId());
            // Generar el refresh token
            $refreshToken = RefreshToken::generate(
                $userExist->userId(),
                $data->userAgent()
            );
            // Persistir el refresh token
            $this->refreshTokenRepository->save($refreshToken);
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }
        
        if( $this->passwordHash->needsRehash($userExist->password()) ){
            $nwHash = $this->passwordHash->hash(RawPassword::fromString($data->rawPassword()));
            $userExist->changePassword($nwHash);
            try {
                $this->userRepository->save($userExist);
            } catch (\Throwable $th) {
                error_log(json_encode($th));
            }
        };

        // Retornar el DTO
        return new LoginResponseDTO($accessToken,$refreshToken->tokenValue()->value());
    }
}