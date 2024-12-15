<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomeSuccess(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider getUris
     */
    public function testUriSuccess($uri) : void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    public function getUris() : iterable
    {
        yield ['index'];
        yield ['project'];
        yield ['info'];
        yield ['features'];
        yield ['offer'];
        yield ['contact'];
        yield ['register'];
        yield ['restricted'];
        yield ['product'];
        yield ['cart'];
        yield ['order'];
    }
}
