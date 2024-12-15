<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubscribeControllerTest extends WebTestCase
{
    public function testSubmitSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/subscribe/add');

        $client->submitForm('Submit', [
            'subscribe[name]' => 'test',
            'subscribe[email]' => 'test@example.com',
            'subscribe[agree]' => true,
        ]);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider getUris
     */
    public function testUriSuccess($uri) : void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    public function getUris() : iterable
    {
        yield ['subscribe/add'];
    }
}
