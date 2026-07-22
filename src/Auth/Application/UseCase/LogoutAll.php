<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Shared\Application\Port\CookieManagerInterface;


final class LogoutAll {
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $tokenRefreshRepository,
        private readonly CookieManagerInterface $cookieManager
    ){}

    public function logoutAll(string $userId):void {
        $this->tokenRefreshRepository->deleteAll(UserId::fromString($userId));
        $this->cookieManager->delete('refreshTokenJKApp');
    }
}