<?php


namespace App\DataFixtures;


use App\Entity\Departments;
use App\Entity\Towns;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TownFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        {
            $file = fopen("var/data/cities.csv", "r");
            $departmentRepo = $manager->getRepository(Departments::class);
            while (($column = fgetcsv($file, 100000, ",")) !== false) {

                if (($column[0]) !== 'id') {
                    $town = new Towns();
                    $town->setDepartmentCode($departmentRepo->findOneBy(['code' => $column[1]]));
                    $town->setZipcode($column[3]);
                    $town->setName($column[4]);
                    $town->setSlug($column[5]);
                    $town->setLat($column[6]);
                    $town->setLng($column[7]);
                    $manager->persist($town);

                }
            }
            $manager->flush();

        }
    }

    public static function getGroups(): array
    {
        return ['town'];
    }
}