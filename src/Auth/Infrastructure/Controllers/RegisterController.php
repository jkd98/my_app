<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\DTO\RegisterUserRequestDTO;
use App\Auth\Application\UseCase\RegisterUser;
use App\Shared\Infrastructure\Http\Response;


final class RegisterController{
    public function __construct(
        private readonly RegisterUser $registerUser,
    ){}    

    public function execute():string{
        $data = json_decode(file_get_contents('php://input'),true);
        $registerUserRequestDTO = new RegisterUserRequestDTO(
            $data['userName'],
            $data['lastName'],
            $data['email'],
            $data['rawPassword']
        );

        $response = null;
        try {
            $this->registerUser->register($registerUserRequestDTO);
            $response = new Response(
                'Usuario registrado, confirme su cuenta vía email.'
            );
        } catch (\Throwable $th) {
            
        }

        return json_encode($response);
    }
}