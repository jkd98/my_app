<?php

namespace App\Auth\Application\DTO;


final class LoginResponseDTO {
    public function __construct(
        private readonly string $accessToken, 
    ){}
    public function accessToken() : string { return $this->accessToken; }
}