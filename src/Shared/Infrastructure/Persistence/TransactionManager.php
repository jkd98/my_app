<?php

namespace App\Shared\Infrastructure\Persistence;
use App\Shared\Application\Port\TransactionManagerInterface;
use PDO;

final class TransactionManager implements TransactionManagerInterface {
    public function __construct(private readonly PDO $pdo){}

    public function begin():void{
        $this->pdo->beginTransaction();
    }

    public function commit():void{
        $this->pdo->commit();
    }

    public function rollback():void{
        $this->pdo->rollback();
    }
}