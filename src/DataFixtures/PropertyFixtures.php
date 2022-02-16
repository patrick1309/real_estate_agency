<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Property;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PropertyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $property = new Property();
            $property->setName(ucfirst($faker->words(3, true)))
                ->setSurface($faker->numberBetween(10, 400))
                ->setPrice($faker->numberBetween(50000, 1000000))
                ->setRooms($faker->numberBetween(1, 10))
                ->setBedrooms($faker->numberBetween(1, 8))
                ->setHeat($faker->numberBetween(0, 1))
                ->setFloor($faker->numberBetween(0, 25))
                ->setAddress($faker->address)
                ->setPostalCode(preg_replace('/ /', '', $faker->postcode))
                ->setCity($faker->city)
                ->setDescription($faker->paragraph());
            $manager->persist($property);
        }

        $manager->flush();
    }
}
