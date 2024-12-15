<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalendarControllerTest extends WebTestCase
{
    public function testCalendar(): void
    {
        $client = static::createClient();
        $client->request('GET', '/calendar');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Calendar');
        $this->assertSelectorTextContains('h2', 'Today');
    }

    public function testCalendarOrder(): void
    {
        $client = static::createClient();
        $client->request('GET', '/calendar/order');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tomorrow');
    }

    public function testCalendarOrderSubmitSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/calendar/order');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Submit', [
            'form[date]' => '10.00',
            'form[name]' => 'name',
            'form[email]' => 'test@test.com',
            'form[phone]' => '+38525777525',
        ]);

        $this->assertResponseRedirects('/calendar/order/success');
    }

    public function testCalendarOrderSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/calendar/order/success');

        $this->assertResponseIsSuccessful();
    }
}
