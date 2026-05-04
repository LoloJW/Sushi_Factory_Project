<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userAdmin = new User();

        $userAdmin->setFirstName('Admin');
        $userAdmin->setLastName('Admin');
        $userAdmin->setEmail('admin@example.com');
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin, 'azerty1234A*'));
        $userAdmin->setIsVerified(true);
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setJoinedAt(new \DateTimeImmutable());

        $manager->persist($userAdmin);
        $userPublic = new User();

        $userPublic->setFirstName('Public');
        $userPublic->setLastName('Public');
        $userPublic->setEmail('Public@example.com');
        $userPublic->setPassword($this->hasher->hashPassword($userPublic, 'azerty1234A*'));
        $userPublic->setIsVerified(true);
        $userPublic->setRoles(['ROLE_USER']);
        $userPublic->setJoinedAt(new \DateTimeImmutable());

        $manager->persist($userPublic);

        $manager->flush();
    }
}
