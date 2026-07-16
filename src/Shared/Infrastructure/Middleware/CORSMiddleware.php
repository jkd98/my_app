<?php

namespace App\Shared\Infrastructure\Middleware;

final class CORSMiddleware {
    public static function handle(){
        $allowedOrigins = [getenv('ORIGIN_ONE')];
        error_log("[DEBUG_ORIGIN]: ".getenv('ORIGIN_ONE'));
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;  // ← Responde 200 y termina
        }
    }
}