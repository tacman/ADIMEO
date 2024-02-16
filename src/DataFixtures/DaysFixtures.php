<?php

namespace App\DataFixtures;

use App\Entity\Day;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class DaysFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        for ( $i = 1; $i <= 24; $i++ ) { 
            $day = new Day();
            
            $day->setNumber($i)
                ->setOpen(false)
            ;
            
            $manager->persist($day) ;
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['days'];
    }

}
