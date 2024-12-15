<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InviteControllerTest extends WebTestCase
{

    public function testAdminSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/invite');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Invites');

    }

    public function testSubmitSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/invite/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/invite/add');

        $crawler = $client->submitForm('Submit', [
            'invite_admin[name]' => 'test',
            'invite_admin[email]' => 'test@test.com',
            'invite_admin[description]' => 'set other description',
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/invite/show/1');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/invite/edit/1');

        $client->submitForm('Submit', [
            'invite_admin[name]' => 'new name',
            'invite_admin[email]' => 'test2@test.com',
            'invite_admin[description]' =>  'set another description',
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/invite/remove/1');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Yes');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/invite/show/1');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSubmitFail(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/invite/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/invite/add');

        //TODO: valid node for the form submit
        $crawler = $client->submitForm('Submit', [
            'invite_admin[name]' => null,
            'invite_admin[email]' => null,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
