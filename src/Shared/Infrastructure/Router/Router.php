<?php

namespace App\Shared\Infrastructure\Router;

final class Router {
    private static array $routers = [
        "auth" => "App\Auth\Infrastructure\Router\AuthRouter"
    ];

    private function __construct(){}

    public static function resolve(string $method, string $path): ?string {
        $uri_parts = explode('/',$path);
        $bounded_context = $uri_parts[1];
        return self::$routers[$bounded_context]::router($method,$path) ?? null;
    }

}