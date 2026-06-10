<?php

namespace App\Auth\Application\DTO;

final class RegisterUserRequestDTO {
    public function __construct(
        private readonly string $userName,
        private readonly string $LastName,
        private readonly string $email,
        private readonly string $rawPassword
    ) {}

    public function userName() : string { return $this->userName; }
    public function lastName() : string { return $this->LastName; }
    public function email() : string { return $this->email; }
    public function rawPassword() : string { return $this->rawPasswor; }


}