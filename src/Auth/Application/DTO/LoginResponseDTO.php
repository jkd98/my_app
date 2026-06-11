<?php

namespace App\Auth\Application\DTO;


final class LoginResponseDTO {
    public function __construct(
        private readonly string $accessToken, 
        private readonly string $refreshToken
    ){}
    public function accessToken() : string { return $this->accessToken; }
    public function refreshToken() : string { return $this->refreshToken; }
}