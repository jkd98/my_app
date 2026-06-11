<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;


final class LogoutAll {
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $tokenRefreshRepository,   
    ){}

    public function logoutAll(string $userId):void {
        $this->tokenRefreshRepository->deleteAll(UserId::fromString($userId));
    }
}