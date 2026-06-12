<?php

namespace App\Auth\Domain\Service;
use App\Auth\Domain\ValueObject\Password;
use App\Auth\Domain\ValueObject\RawPassword;

interface PasswordHashInterface {
    public function hash(RawPassword $rawPassword):Password; 
    public function verify(RawPassword $rawPassword, Password $passwordHashed):bool;
    public function needsRehash(Password $password): bool;
}