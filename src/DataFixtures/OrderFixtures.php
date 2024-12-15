<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Order();
        $profile->setDeliveryName('Name');
        $profile->setDeliveryPhone('+380000000000');
        $profile->setCurrency('UAH');
        $profile->setDeliveryEmail('movchan@gmail.com');
        $profile->setStatus('new');
        $profile->setAmount(25);
        $profile->setDescription('some descr');
        $manager->persist($profile);
        $manager->flush();
    }
}
