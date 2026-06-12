<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Domain\Entity\RefreshToken;
use App\Auth\Application\Security\TokenGeneratorInterface;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Shared\Application\TransactionManagerInterface;
use App\Auth\Application\DTO\LoginResponseDTO;


final class Refresh {
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly TransactionManagerInterface $transactionManager
    ) {}

    public function refresh(string $strRefresTokenValue,string $userAgent): LoginResponseDTO {
        $oldRefreshToken = $this->refreshTokenRepository->findByTokenValue(TokenValue::fromString($strRefresTokenValue));
        if(!$oldRefreshToken) throw new InvalidTokenException();
        if($oldRefreshToken->isExpired()) throw new InvalidTokenException("El token ha expirado");
        
        $nwAccessToken = $this->tokenGenerator->generate($oldRefreshToken->userId());
        $nwRefreshToken = RefreshToken::generate($oldRefreshToken->userId(),$userAgent);

        $this->transactionManager->begin();
        try {
            $this->refreshTokenRepository->save($nwRefreshToken);
            $this->refreshTokenRepository->delete($oldRefreshToken->tokenId());
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }

        return new LoginResponseDTO($nwAccessToken,$nwRefreshToken->tokenValue()->value());
    }
}