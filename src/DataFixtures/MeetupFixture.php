<?php

namespace App\DataFixtures;

use App\Entity\Meetup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MeetupFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $meetup = new Meetup('Introduction to programming', new \DateTime());
        $manager->persist($meetup);
        $manager->flush();
    }
}
