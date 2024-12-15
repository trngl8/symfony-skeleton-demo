<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckControllerTest extends WebTestCase
{

    public function testAdminSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/check');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Checks');

    }

    public function testSubmitSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/check/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/check/add');

        $crawler = $client->submitForm('Submit', [
            'check[title]' => 'test',
            'check[description]' => 'about item',
            'check[type]' => 'open',
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/check/edit/1');

        $client->submitForm('Submit', [
            'check[title]' => 'new name',
            'check[description]' => 'about item',
            'check[type]' => 'single',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testSubmitFail(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/check/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/check/add');

        $crawler = $client->submitForm('Submit', [
            'check[title]' => null,
            'check[description]' => 'about item',
            'check[type]' => 'single',
        ]);

        $this->assertResponseIsSuccessful();
    }
}
