<?php


namespace App\DataFixtures;


use App\Entity\Departments;
use App\Entity\Regions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Repository\RegionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Type;

class DepartementFixtures extends Fixture implements FixtureGroupInterface
{
 public function load(ObjectManager $manager)
 {
     {
        $file = fopen("var/data/departments.csv","r");
        $regionRepo =$manager->getRepository(Regions::class);
        while(($column = fgetcsv($file,10000,",")) !== false) {
//            $region = New Regions();
//            $region->setId($column[0]);
//            $region->setCode($column[1]);
//            $region->setName($column[2]);
//            $region->setSlug($column[3]);
//            $manager->persist($region);
            if (($column[0]) !== 'id') {
                $departement = new Departments();
                $departement->setRegionCode($regionRepo->findOneBy(['code' => $column[1]]));
                $departement->setCode($column[2]);
                $departement->setName($column[3]);
                $departement->setSlug($column[4]);
                $manager->persist($departement);
            }
        }
         $manager->flush();
     }
 }
    public static function getGroups(): array
    {
        return ['department'];
    }
}