<?php


namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategorieFixtures extends Fixture implements FixtureGroupInterface
{
    const CATEGORY = [
        'Routière',
        'Routière GT',
        'Routière Sportive',
        'Sportive',
        'Roadster',
        'Cruiser',
        'Motocross',
        'Super Motard',
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORY as $key => $categorieName) {
            $categorie = new Categorie();
            $categorie->setName($categorieName);
            $manager->persist($categorie);
        }
        $manager->flush();

    }

    public static function getGroups(): array
    {
        return ['categorie'];
    }
}
