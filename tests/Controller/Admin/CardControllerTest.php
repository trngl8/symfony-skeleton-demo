<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Response;

class CardControllerTest extends WebTestCase
{

    public function testAdminSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/card');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Items');

    }

    public function testSubmitSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/card/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/card/add');

        $crawler = $client->submitForm('Submit', [
            'card_admin[title]' => 'test',
            'card_admin[code]' => 12,
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/card/2/edit');

        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', '/admin/card/1/edit');

        $client->submitForm('Submit', [
            'card_admin[title]' => 'new name',
            'card_admin[code]' => 34,
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/card/1/show');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/card/1/remove');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Yes');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/card/1/show');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSubmitFail(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/card/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/card/add');

        $crawler = $client->submitForm('Submit', [
            'card_admin[title]' => null,
            'card_admin[code]' => null,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
