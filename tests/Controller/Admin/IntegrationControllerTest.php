<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntegrationControllerTest extends WebTestCase
{
    public function testIntegrationSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');
        $client->loginUser($testUser);

        $client->request('GET', '/admin/integration');
        $client->followRedirects();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Integration');
    }
}
