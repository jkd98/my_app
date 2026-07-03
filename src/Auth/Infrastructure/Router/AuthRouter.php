<?php

namespace App\Auth\Infrastructure\Router;

final class AuthRouter {
    private static array $routes = [
        'GET' => [
            "/auth/confirm" => "App\Auth\Infrastructure\Controllers\AccountConfirmController",
        ],
        'POST' => [
            "/auth/register" => "App\Auth\Infrastructure\Controllers\RegisterController",
            "/auth/login" => "App\Auth\Infrastructure\Controllers\LoginController",
            "/auth/request-new-password" => "App\Auth\Infrastructure\Controllers\PasswordRecoveryController",
            "/auth/reset-password" => "App\Auth\Infrastructure\Controllers\ResetPasswordController",
            "/auth/logout" => "App\Auth\Infrastructure\Controllers\LogoutController",
            "/auth/logout-all-sessions" => "App\Auth\Infrastructure\Controllers\LogoutAllController"
        ]
    ];

    private function __construct(){}

    public static function router(string $method, string $path):?string {
        return self::$routes[$method][$path] ?? null;
    }
}
