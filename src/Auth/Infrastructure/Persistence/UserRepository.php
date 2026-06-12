<?php

namespace App\Auth\Infrastructure\Persistence;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\UserName;
use App\Auth\Domain\ValueObject\LastName;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\Password;
use App\Auth\Domain\Entity\User;
use PDO;
use DateTimeImmutable;

final class UserRepository implements UserRepositoryInterface {
    public function __construct(private readonly PDO $pdo){
    }

    public function findById(UserId $userId):?User{
    }
    
    public function findByEmail(Email $email):?User{
        $data = [
            "email"=>$email->value()
        ];
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){
            return User::reconstitute(
                UserId::fromString($user['userId']),
                UserName::create($user['userName']),
                LastName::create($user['lastName']),
                Email::create($user['email']),
                Password::create($user['pass']),
                (bool)$user['isVerified'],
                new DateTimeImmutable($user['createdAt']),
                new DateTimeImmutable($user['updatedAt'])
            );
        }else{
            return null;
        }
    }
    
    public function save(User $user):void{
        $data = [
            "userId"=>$user->userId()->value(), 
            "userName"=>$user->userName()->value(), 
            "lastName"=>$user->lastName()->value(), 
            "email"=>$user->email()->value(), 
            "pass"=>$user->password()->value(), 
            "isVerified"=>(int)$user->isVerified(), 
            "createdAt"=>$user->createdAt()->format('Y-m-d H:i:s'), 
            "updatedAt"=>$user->updatedAt()->format('Y-m-d H:i:s'),
        ];
        $sql = "INSERT INTO users (userId, userName, lastName, email, pass, isVerified, createdAt, updatedAt) 
                VALUES (:userId, :userName, :lastName, :email, :pass, :isVerified, :createdAt, :updatedAt)
                ON DUPLICATE KEY UPDATE
                    userName = VALUES(userName),
                    lastName = VALUES(lastName),
                    pass = VALUES(pass),
                    isVerified = VALUES(isVerified),
                    updatedAt = VALUES(updatedAt)
                ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }
}