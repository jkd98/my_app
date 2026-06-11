<?php

namespace App\Auth\Application\DTO;


final class LoginResponseDTO {
    public function __construct(private readonly string $token){}
    public function token() : string { return $this->token; }
}