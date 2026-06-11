<?php

namespace App\Shared\Application;

interface TransactionManagerInterface {
    public function begin():void;
    public function commit():void;
    public function rollback():void;
}