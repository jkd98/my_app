<?php 

namespace App\Shared\Application\DTO;

final class MailDTO {
    public function __construct(
        private readonly array $recipients,
        private readonly string $subject,
        private readonly string $messageBody,
        private readonly string $altBody
    ) {}

    public function recipients() :array  { return $this->recipients; }
    public function subject() :string { return $this->subject; }
    public function messageBody() :string { return $this->messageBody; }
    public function altBody() :string { return $this->altBody; }
}