<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProcessingControllerTest extends WebTestCase
{
    public function testCreateOrderSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('POST', '/api/order/create', [
                'firstname' => 'First',
                'lastname' => 'Last',
                'email' => 'test@email.com',
                'phone' => '1234567890',
                'address' => 'City, Street, 1',
                'period' => 2,
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateOrderFail(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('POST', '/api/order/create', [
            'firstname' => 'First',
            'lastname' => 'Last',
            'phone' => '1234567890',
            'address' => 'City, Street, 1',
            'period' => 2,
        ]);

        $this->assertResponseStatusCodeSame(400);
    }
}
