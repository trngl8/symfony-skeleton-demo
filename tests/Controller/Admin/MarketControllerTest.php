<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MarketControllerTest extends WebTestCase
{
    public function testMarketSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/market');
        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', '1.0.1-test');
    }
}
