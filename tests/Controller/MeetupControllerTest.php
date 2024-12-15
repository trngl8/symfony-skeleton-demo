<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetupControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/meetups');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meetups');
    }

    public function testShow(): void
    {
        $this->client->request('GET', '/meetups/1/show');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Meetup 1');
    }

    public function testCreateSuccess(): void
    {
        $this->client->request('GET', '/meetups/create');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Create', [
            'meetup[title]' => 'Meetup 4',
            'meetup[plannedDayAt]' => '2024-01-01',
            'meetup[plannedTimeAt][hour]' => '20',
            'meetup[plannedTimeAt][minute]' => '30',
            'meetup[duration]' => '1',
        ]);
        $this->assertResponseRedirects();
    }

    public function testJoinError(): void
    {
        $this->client->request('POST', '/meetups/1/join');
        $this->assertResponseRedirects();
    }

    public function testSubscribeError(): void
    {
        $this->client->request('POST', '/meetups/1/subscribe');
        $this->assertResponseIsSuccessful();
    }
}
