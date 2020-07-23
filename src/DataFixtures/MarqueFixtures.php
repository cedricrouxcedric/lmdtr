<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MarqueFixtures extends Fixture implements FixtureGroupInterface
{
    const MARQUES = [
        'Aprilia',
        'Benelli',
        'Beta',
        'Bimota',
        'Bmw',
        'Bombardier',
        'Bull',
        'Cagiva',
        'Can Am',
        'Derbi',
        'Ducati',
        'Gas Gas',
        'Gilera',
        'Guzzi',
        'Harley',
        'Davidson',
        'Honda',
        'Husaberg',
        'Husqvarna',
        'Hyosung',
        'Indian',
        'Jinlong',
        'Kawasaki',
        'Keeway',
        'Ktm',
        'Kymco',
        'Laverda',
        'Mash',
        'Mbk',
        'Minibike',
        'Morini',
        'Mv-agusta',
        'Norton',
        'Peugeot',
        'Pgo',
        'Piaggio',
        'Polaris',
        'Quadro',
        'Rieju',
        'Roxon',
        'Royal-enfield',
        'Secma',
        'Sky Team',
        'Smc',
        'Suzuki',
        'Sym',
        'Tnt Motor',
        'Triumph',
        'Vespa',
        'Victory',
        'Voxan',
        'Xingyue',
        'Yamaha',
        'Ycf',
        'Zero',
        'Znen',
        'Zongshen',
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::MARQUES as $key => $marqueName) {
            $marque = new Marque();
            $marque->setName($marqueName);
            $manager->persist($marque);
        }
        $manager->flush();

    }
     public static function getGroups(): array
     {
         return ['marque'];
     }
}
