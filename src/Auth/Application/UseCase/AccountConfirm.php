<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\ValueObject\TokenValue;

use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;

use App\Shared\Application\TransactionManagerInterface;
use App\Auth\Domain\Exception\InvalidTokenException;

final class AccountConfirm {
    
    public function __construct(
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function confirmAccount(string $tokenValue){
        $tknValueObj = TokenValue::fromString($tokenValue);

        $tokenExist = $this->verificationTokenRepository->findByTokenValue($tknValueObj);
        if(!$tokenExist) throw new InvalidTokenException("El token no existe");
        
        if($tokenExist->isExpired()) throw new InvalidTokenException("El token ya expiró, para validar la cuenta se deberá solicitar uno nuevo.");
        
        $userExist = $this->userRepository->findById($tokenExist->userId());
        if (!$userExist) throw new \RuntimeException("Usuario no encontrado");
       
        $userExist->confirmAccount();

        $this->transactionManager->begin();
        try {
            $this->userRepository->save($userExist);
            $this->verificationTokenRepository->delete($tokenExist->tokenId());
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }
    }
}