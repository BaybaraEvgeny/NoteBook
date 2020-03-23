<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Note;
use Faker\Factory;

class NoteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $note = new Note();
            $note->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $note->setText($faker->realText);
            $manager->persist($note);
        }

        $manager->flush();
    }
}