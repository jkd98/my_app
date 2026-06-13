<?php

namespace App\Shared\Application\Port;
use App\Shared\Application\DTO\MailDTO;

interface MailerInterface {
    public function send(MailDTO $data):void;
}