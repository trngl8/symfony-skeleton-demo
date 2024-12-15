<?php

namespace App\DataFixtures;

use App\Entity\TimeData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TimeDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $today = new TimeData();
        $today->setTitle('Today');
        $today->setStartAt(new \DateTime('now'));
        $today->setType('type');
        $today->setDuration(25);

        $tomorrow = new TimeData();
        $tomorrow->setTitle('Tomorrow');
        $tomorrow->setStartAt(new \DateTime('now + 1 day'));
        $tomorrow->setType('type');
        $tomorrow->setDuration(25);

        $manager->persist($today);
        $manager->persist($tomorrow);
        $manager->flush();
    }
}
