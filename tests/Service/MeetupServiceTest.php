<?php

namespace App\Tests\Service;

use App\Entity\Meetup;
use App\Security\User;
use App\Service\MeetupService;
use PHPUnit\Framework\TestCase;

class MeetupServiceTest extends TestCase
{
    public function testJoin(): void
    {
        $service = new MeetupService();
        $meetup = new Meetup('Meetup 3', new \DateTimeImmutable('2020-01-01'));
        $user = new User('test@test.com');
        $service->join($meetup, $user);
        $this->assertTrue(true);
    }

    public function testSubscribe(): void
    {
        $service = new MeetupService();
        $meetup = new Meetup('Meetup 3', new \DateTimeImmutable('2020-01-01'));
        $service->subscribe($meetup, []);
        $this->assertTrue(true);
    }
}
