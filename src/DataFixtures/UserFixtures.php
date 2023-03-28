<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{

    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $admin1 = new User();
        $admin1->setEmail('admin1@gmail.com');
        $admin1->setPassword($this->hasher->hashPassword($admin1,'admin1'));
        $admin1->setRoles(['ROLE_ADMIN']);
        $admin1->setName('dupont');
        $admin1->setFirstname('jean');
        $admin1->setAge('39');
        $admin1->setPseudo('jeanno');
        $admin1->setStatus('1');
        $date1 = new \DateTime();
        $admin1->setCreatedAt($date1);

        $admin2 = new User();
        $admin2->setEmail('admin2@gmail.com');
        $admin2->setPassword($this->hasher->hashPassword($admin2,'admin2'));
        $admin2->setRoles(['ROLE_ADMIN']);
        $admin2->setName('dumont');
        $admin2->setFirstname('luc');
        $admin2->setAge('40');
        $admin2->setPseudo('sky');
        $admin2->setStatus('1');
        $date2 = new \DateTime();
        $admin2->setCreatedAt($date2);

        $manager->persist($admin1);
        $manager->persist($admin2);


        $user = new User();
        $user->setEmail("user@gmail.com");
        $user->setPassword($this->hasher->hashPassword($user,'user'));
        $user->setRoles(["ROLE_USER"]);
        $user->setName("dupond");
        $user->setFirstname("martin");
        $user->setAge("20");
        $user->setPseudo("marty");
        $user->setStatus('1');
        $date3 = new \DateTime();
        $user->setCreatedAt($date3);

        $manager->persist($user);


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['userGroup'];
    }
}