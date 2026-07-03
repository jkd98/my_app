<?php

namespace App\Shared\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;


final class ContainerConfig {
    private function __construct() {}

    public static function create():Container{
        $container = new Container();

        $classToInstance = [
            "App\Auth\Domain\Repository\UserRepositoryInterface" => "App\Auth\Infrastructure\Persistence\UserRepository",
            "App\Auth\Domain\Repository\VerificationTokenRepositoryInterface" => "App\Auth\Infrastructure\Persistence\VerificationTokenRepository",
            "App\Auth\Domain\Service\PasswordHashInterface" => "App\Auth\Infrastructure\Security\PasswordHash",
            "App\Auth\Domain\Service\VerifyEmailExist" => "App\Auth\Domain\Service\VerifyEmailExist",
            "App\Shared\Application\Port\EventDispatcherInterface" => "App\Auth\Infrastructure\EventDispatcher\EventDispatcher",
            "App\Shared\Application\Port\TransactionManagerInterface" => "App\Shared\Infrastructure\Persistence\TransactionManager",
            "PDO" => function(){ 
                return new \PDO(
                    'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME').';charset=utf8mb4',
                    getenv('DB_USER'),
                    getenv('DB_PASSWORD')
                );
            },
            "App\Auth\Application\UseCase\RegisterUser" => "App\Auth\Application\UseCase\RegisterUser",
            "App\Auth\Infrastructure\Controllers\RegisterController" => "App\Auth\Infrastructure\Controllers\RegisterController",
            "App\Shared\Application\Port\MailerInterface" => function(){
                return new App\Shared\Infrastructure\Mailer(
                    getenv("SMTP_HOST"),
                    getenv("SMTP_USERNAME"),
                    getenv("SMTP_PASS"),
                    (int) getenv("SMTP_PORT"),
                );
            }
        ];

        foreach($classToInstance as $key => $value){
            $container->bind($key,$value);
        }

        return $container;
    }

}