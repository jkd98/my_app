<?php

namespace App\Auth\Application\Security;

use App\Auth\Domain\ValueObject\UserId;

interface TokenGeneratorInterface {
    public function generate(UserId $userId):string;
}