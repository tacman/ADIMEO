<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UsersFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct( private UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR') ;
    }

    public function load( ObjectManager $manager ): void
    {
        // Super Admin
        $admin = new User();

        $admin
            ->setNom( "Bany" )
            ->setPrenom( "Blanchard" )
            ->setPhone( $this->faker->phoneNumber() )
            ->setEmail( "blancho@gmail.com" )
            ->setRoles(['ROLE_ADMIN'])
            ->setDone(true)
            ->setPassword(
                $this->hasher->hashPassword( $admin, "azerty" )
            )
        ;

        $manager->persist($admin); 

        // All users
        for ( $i=1; $i < 15; $i++ ) { 
            $user = new User();

            $user
                ->setNom( $this->faker->lastName() )
                ->setPrenom( $this->faker->firstName() )
                ->setPhone( $this->faker->phoneNumber() )
                ->setEmail( $this->faker->unique()->email() )
                ->setDone(mt_rand(0, 1))
                ->setRoles(['ROLE_USER'])
                ->setPassword(
                    $this->hasher->hashPassword( $user, "azerty" )
                )
            ;

            $manager->persist($user); 
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['users'];
    }
    
}
