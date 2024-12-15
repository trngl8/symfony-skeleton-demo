<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $job = new Job();
        $job->setTitle('Backup database');
        $job->setCrontab('01:05');
        //$job->setCommand('php bin/console app:backup');
        $manager->persist($job);
        $manager->flush();
    }
}
