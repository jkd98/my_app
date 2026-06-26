<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\DTO\RegisterUserRequestDTO;
use App\Auth\Application\UseCase\RegisterUser;
use App\Shared\Infrastructure\Http\Response;
use App\Auth\Domain\Exception\EmailAlreadyExistsException;

final class RegisterController{
    public function __construct(
        private readonly RegisterUser $registerUser,
    ){}    

    public function execute():void{
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
                msg: 'Usuario registrado, confirme su cuenta vía email.'
            );
            $response->send(201);
        } catch (\Throwable $th) {
            if($th instanceof EmailAlreadyExistsException){
                $response = new Response(
                    msg: $th->getMessage(),
                    status: 'error'
                );
                $response->send(409);
            }else{
                $response = new Response(
                    msg: $th->getMessage(),
                    status: 'error'
                );
                $response->send(500);
            }
        }
    }
}