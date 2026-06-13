<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Service\PasswordHashInterface;
use App\Shared\Application\Port\TransactionManagerInterface;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\ValueObject\RawPassword;
use App\Auth\Domain\Exception\InvalidTokenException;

final class ResetPassword {
    public function __construct(
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHashInterface $passwordHasher,
        private readonly TransactionManagerInterface $transactionManager,
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository
    ){}

    public function reset(string $strToken, string $nwRawPassword):void {
        $tokenType = TokenType::PasswordRecovery;
        $tokenValue = TokenValue::fromString($strToken);
        $verificationToken = $this->verificationTokenRepository->findByTokenValue($tokenValue);
        
        if(!$verificationToken) throw new InvalidTokenException();
        if( $verificationToken->tokenType() !== $tokenType ) throw new InvalidTokenException();
        if( $verificationToken->isExpired() ) throw new InvalidTokenException("El token ha expirado");
        
        $userId = $verificationToken->userId();
        $rawPassword = RawPassword::create($nwRawPassword);
        $user = $this->userRepository->findById($userId);
        if(!$user) throw new InvalidTokenException();
        $nwPasswordHashed = $this->passwordHasher->hash($rawPassword);
        $user->changePassword($nwPasswordHashed);

        $this->transactionManager->begin();
        try {
            $this->userRepository->save($user);
            $this->verificationTokenRepository->delete($verificationToken->tokenId());
            $this->refreshTokenRepository->deleteAll($userId);
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }
    }
}