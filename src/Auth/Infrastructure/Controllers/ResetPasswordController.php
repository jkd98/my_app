<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\ResetPassword;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Shared\Infrastructure\Http\Response;


final class ResetPasswordController {
    public function __construct(
        private readonly ResetPassword $resetPassword
    ){}

    public function execute(): void {
        $token = $_GET['confirmation'];
        $data = json_decode(file_get_contents('php://input'),true);

        try {
            $this->resetPassword->reset(
                $token,
                $data['rawPassword']
            );
            $response = new Response(
                msg: "Contraseña actualizada",
            );
            $response->send(200);
        } catch (\Throwable $th) {
            if($th instanceof InvalidTokenException){
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