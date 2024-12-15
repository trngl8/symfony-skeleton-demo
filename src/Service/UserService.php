<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private $em;
    private $passwordHasher;

    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $doctrine->getManager();
        $this->passwordHasher = $passwordHasher;
    }

    public function create(string $username, string $password) : User
    {
        $user = (new User($username))
            ->setUsername($username)
        ;

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    public function save(User $user) : void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user) : void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findBy(array $criteria) : array
    {
        return $this->em->getRepository(User::class)->findBy($criteria);
    }

    public function findOneBy(array $criteria) : User
    {
        return $this->em->getRepository(User::class)->findOneBy($criteria);
    }
}
