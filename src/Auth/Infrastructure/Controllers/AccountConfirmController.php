<?php

namespace App\Auth\Infrastructure\Controllers;
use App\Auth\Application\UseCase\AccountConfirm;
use App\Shared\Infrastructure\Http\Response;
use App\Auth\Domain\Exception\UserNotFoundException;
use App\Auth\Domain\Exception\InvalidTokenException;

final class AccountConfirmController {
    public function __construct(
        private readonly AccountConfirm $accountConfirm
    ) {}

    public function execute():void {
        $token = $_GET['confirmation'];
        try {
            $this->accountConfirm->confirmAccount($token);
            $response = new Response(
                "Cuenta confirmada, ya puede iniciar sesión"
            );
            $response->send(200);
        } catch (\Throwable $th) {
            if($th instanceof UserNotFoundException || $th instanceof InvalidTokenException ){
                $response = new Response(
                    msg:$th->getMessage(),
                    status:'error'
                );
                $response->send(404);
            }else{
                $response = new Response(
                    msg:$th->getMessage(),
                    status:'error'
                );
                $response->send(500);
            }
        }
    }
}