<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{

    public function testAdminProductSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/project');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Projects');

    }

    public function testCreateProjectSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/project/add');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/project/add');

        $crawler = $client->submitForm('Submit', [
            'project[title]' => 'test',
            'project[type]' => 'type',
            'project[description]' => 'set other description',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/project/1/edit');

        $client->submitForm('Submit', [
            'project[title]' => 'test',
            'project[type]' => 'type',
            'project[description]' => 'set one more description',
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/project/1/show');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/project/1/remove');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Yes');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/project/1/show');

        $this->assertResponseStatusCodeSame(404);
    }
}
