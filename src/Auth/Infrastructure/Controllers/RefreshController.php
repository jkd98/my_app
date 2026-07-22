<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\Refresh;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Shared\Infrastructure\Http\Response;

final class RefreshController {
    public function __construct(
        private readonly Refresh $refresh
    ) {}

    public function execute():void {
        $data = json_decode(file_get_contents('php://input'),true);
        try {
            $loginResponse = $this->refresh->refresh(
                $data['strRefreshTokenValue'],
                $_SERVER['HTTP_USER_AGENT']
            );
            $response = new Response(
                msg: "Sesión actualizada",
                data: [
                    "accessToken" => $loginResponse->accessToken()
                ]
            );
            $response->send(200);
        } catch (\Throwable $th) {
            $response = new Response(
                msg: $th->getMessage(),
                status: "error"
            );
            if($th instanceof InvalidTokenException){
                $response->send(401);
            }else{
                $response->send(500);
            }
        }
    }
}