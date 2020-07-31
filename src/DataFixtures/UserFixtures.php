<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {

        // Création d’un utilisateur de type “auteur”

        $subscriber = new User();
        $subscriber->setUsername('sub');
        $subscriber->setEmail('subscriber@monsite.com');
        $subscriber->setRoles(['ROLE_SUBSCRIBER']);
        $subscriber->setValidateAccount(true);
        $subscriber->setPassword($this->passwordEncoder->encodePassword(
            $subscriber,
            'subscriberpassword'
        ));

        $manager->persist($subscriber);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setValidateAccount(true);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
