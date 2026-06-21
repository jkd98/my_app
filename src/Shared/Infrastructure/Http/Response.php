<?php

namespace App\Shared\Infrastructure\Http;

final class Response {
    public function __construct(
        private readonly string $msg,
        private readonly ?array $data,
        private readonly ?array $metadata,
        private readonly string $status = 'success'
    ) { }

    public function send(int $code) {
        http_response_code($code);
        header("Content-Type: application/json");
        $data = [
            "status"=>$this->status,
            "msg" => $this->msg,
            "data" => $this->data,
            "metadata" => $this->metadata
        ];
        echo(json_encode($data));
    }
}