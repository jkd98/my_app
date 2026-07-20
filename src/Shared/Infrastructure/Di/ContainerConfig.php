<?php

namespace App\Shared\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;


final class ContainerConfig {
    private function __construct() {}

    public static function create():Container{
        $container = new Container();
        $config = require(__DIR__ . '/../../../../config/auth.php');
        $privateKeyPath = $config['jwt']['private_key_path'];

        $classToInstance = [
            "App\Auth\Infrastructure\Controllers\RegisterController" => "App\Auth\Infrastructure\Controllers\RegisterController",
            "App\Auth\Application\UseCase\RegisterUser" => "App\Auth\Application\UseCase\RegisterUser",
            "App\Auth\Domain\Service\VerifyEmailExist" => "App\Auth\Domain\Service\VerifyEmailExist",
            "App\Auth\Infrastructure\EventListener\SendEmailConfirmation" => "App\Auth\Infrastructure\EventListener\SendEmailConfirmation",
            
            "App\Auth\Infrastructure\Controllers\AccountConfirmController" => "App\Auth\Infrastructure\Controllers\AccountConfirmController",
            "App\Auth\Application\UseCase\AccountConfirm" => "App\Auth\Application\UseCase\AccountConfirm",

            "App\Auth\Infrastructure\Controllers\LoginController" => "App\Auth\Infrastructure\Controllers\LoginController",
            "App\Auth\Application\UseCase\Login" => "App\Auth\Application\UseCase\Login",
            
            "App\Auth\Infrastructure\Controllers\RefreshController" => "App\Auth\Infrastructure\Controllers\RefreshController",
            "App\Auth\Application\UseCase\Refresh" => "App\Auth\Application\UseCase\Refresh",

            "App\Auth\Infrastructure\Controllers\LogoutController" => "App\Auth\Infrastructure\Controllers\LogoutController",
            "App\Auth\Application\UseCase\Logout" => "App\Auth\Application\UseCase\Logout",

            "App\Auth\Infrastructure\Controllers\LogoutAllController" => "App\Auth\Infrastructure\Controllers\LogoutAllController",
            "App\Auth\Application\UseCase\LogoutAll" => "App\Auth\Application\UseCase\LogoutAll",

            "App\Auth\Infrastructure\Controllers\PasswordRecoveryController" => "App\Auth\Infrastructure\Controllers\PasswordRecoveryController",
            "App\Auth\Application\UseCase\PasswordRecovery" => "App\Auth\Application\UseCase\PasswordRecovery",
            "App\Auth\Infrastructure\EventListener\SendPasswordRecoveryEmail" => "App\Auth\Infrastructure\EventListener\SendPasswordRecoveryEmail",

            "App\Auth\Infrastructure\Controllers\ResetPasswordController" => "App\Auth\Infrastructure\Controllers\ResetPasswordController",
            "App\Auth\Application\UseCase\ResetPassword" => "App\Auth\Application\UseCase\ResetPassword",
            
            
            "App\Auth\Domain\Repository\RefreshTokenRepositoryInterface" => "App\Auth\Infrastructure\Persistence\RefreshTokenRepository",
            "App\Auth\Domain\Repository\UserRepositoryInterface" => "App\Auth\Infrastructure\Persistence\UserRepository",            
            "App\Auth\Domain\Repository\VerificationTokenRepositoryInterface" => "App\Auth\Infrastructure\Persistence\VerificationTokenRepository",
            "App\Shared\Application\Port\TransactionManagerInterface" => "App\Shared\Infrastructure\Persistence\TransactionManager",
            "App\Shared\Application\Port\EventDispatcherInterface" => "App\Auth\Infrastructure\EventDispatcher\EventDispatcher",
            "App\Auth\Domain\Service\PasswordHashInterface" => "App\Auth\Infrastructure\Security\PasswordHash",
            "PDO" => function(){ 
                return new \PDO(
                    'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME').';charset=utf8mb4',
                    getenv('DB_USER'),
                    getenv('DB_PASSWORD')
                );
            },
            "App\Shared\Application\Port\MailerInterface" => function(){
                return new \App\Shared\Infrastructure\Mailer\SmtpMailer(
                    getenv("SMTP_HOST"),
                    getenv("SMTP_USERNAME"),
                    getenv("SMTP_PASSWORD"),
                    (int) getenv("SMTP_PORT"),
                );
            },
            "App\Auth\Application\Security\TokenGeneratorInterface" => function() use($privateKeyPath) {
                return new \App\Auth\Infrastructure\Security\JWTGenerate(
                    $privateKeyPath
                );
            }
        ];

        foreach($classToInstance as $key => $value){
            $container->bind($key,$value);
        }

        return $container;
    }

}