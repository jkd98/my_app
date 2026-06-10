<?php

namespace App\Auth\Domain\Service;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;


final class VerifyEmailExist {
    
public function __construct(private UserRepositoryInterface $userRepository){}
    
    public function execute(Email $email):void{
        if($this->userRepository->findByEmail($email)){
            throw new EmailAlreadyExistsException();
        }
    }
}