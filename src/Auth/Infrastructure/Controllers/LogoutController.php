<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Logout;
use App\Shared\Infrastructure\Http\Response;
use InvalidArgumentException;


final class LogoutController {
    public function __construct(
        private readonly Logout $logout
    ){}

    public function execute(): void {
        $token = json_decode(file_get_contents("php://input"),true);
        try {
            $this->logout->logout($token['refreshToken']);
            $response = new Response(
                msg: "Sesión cerrada."
            );
            $response->send(200);
        } catch (\Throwable $th) {
            $response = new Response(
                msg: $th->getMessage()
            );
            if($th instanceof InvalidArgumentException){
                $response->send(401);
            }else{
                $response->send(500);
            }
        }
    }
}