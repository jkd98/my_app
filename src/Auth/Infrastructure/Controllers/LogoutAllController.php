<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\LogoutAll;
use App\Shared\Infrastructure\Http\Response;

final class LogoutAllController {
    public function __construct(
        private readonly LogoutAll $logoutAll
    ) {}

    public function execute():void {
        $data = json_decode(file_get_contents('php://input'),true);
        try {
            $this->logoutAll->logoutAll($data['userId']);
            $response = new Response(
                msg: "Todas las sesiones han sido cerradas"
            );
            $response->send(200);
        } catch (\Throwable $th) {
            $response = new Response(
                msg: $th->getMessage(),
                status:"error"
            );
            $response->send(500);
        }
    }
}