<?php


namespace App\DataFixtures;


use App\Entity\Regions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class RegionFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        //$filename = $_FILES[regions]["tmp_name"];
        $file = fopen("var/data/regions.csv","r");

        while(($column = fgetcsv($file,10000,",")) !== false) {
            $region = New Regions();
            $region->setId($column[0]);
            $region->setCode($column[1]);
            $region->setName($column[2]);
            $region->setSlug($column[3]);
            $manager->persist($region);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['region'];
    }
}