<?php
namespace App\Auth\Domain\Repository;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\Entity\User;


interface UserRepositoryInterface {
    public function findById(UserId $userId):?User;
    public function findByEmail(Email $email):?User;
    public function save(User $user):void;
}