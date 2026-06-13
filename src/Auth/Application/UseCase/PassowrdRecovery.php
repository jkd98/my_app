<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Application\Port\TransactionManagerInterface;
use App\Shared\Application\Port\EventDispatcherInterface;
use App\Auth\Domain\Events\PasswordRecoveryRequested;
use App\Auth\Domain\Entity\VerificationToken;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\ValueObject\Email;

final class PasswordRecovery {
    public function __construct(
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TransactionManagerInterface $transactionManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    public function passwordRecoveryRequest(string $email):void{
        $emailVO = Email::create($email);
        $user = $this->userRepository->findByEmail($emailVO);
        if(!$user) return;

        $tokenType = TokenType::PasswordRecovery;
        $verificationToken = VerificationToken::create(
            $tokenType,
            $user->userId()
        );
        $this->transactionManager->begin();
        try {
            $this->verificationTokenRepository->deleteAllByTokenType($user->userId(),$tokenType);
            $this->verificationTokenRepository->save($verificationToken);
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }

        $event = PasswordRecoveryRequested::create($user->userId(),$emailVO,$user->userName(),$verificationToken->tokenValue());
        
        $this->eventDispatcher->dispatch($event);
    }
}