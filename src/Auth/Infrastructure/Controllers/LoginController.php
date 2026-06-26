<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Login;
use App\Auth\Application\DTO\LoginRequestDTO;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Shared\Infrastructure\Http\Response;

final class LoginController {
    public function __construct(
        private readonly Login $login
    ){}

    public function execute(): void {
        $data = json_decode(file_get_contents("php://input"),true);
        $loginDTO = new LoginRequestDTO(
            $data['email'],
            $data['rawPassword'],
            $_SERVER['HTTP_USER_AGENT']
        );

        try {
            $loginResponseDTO = $login->login($loginDTO);
            $response = new Response(
                msg: "Inicio de sessión exitoso",
                data: [
                    "accessToken" => $loginResponseDTO->accessToken(),
                    "refreshToken" => $loginResponseDTO->refreshToken()
                ]
            );
            $response->send(200);
        } catch (\Throwable $th) {
            if($th instanceof InvalidCredentialsException){
                $response = new Response(
                    msg: $th->getMessage(),
                    status: "error"
                );
                $response->send(401);
            }else{
                $response = new Response(
                    msg: $th->getMessage(),
                    status: "error"
                );
                $response->send(500);
            }
        }
    }
}