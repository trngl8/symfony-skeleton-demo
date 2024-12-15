<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TopicControllerTest extends WebTestCase
{
    public function testTopicExportSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/topic');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Topics');

        $client->request('GET', '/admin/topic/export');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'text/csv; charset=UTF-8');
        $this->assertResponseHeaderSame('content-disposition', sprintf('attachment; filename="topics-export-%s.csv"', date('Y-m-d')));

        //TODO: check file content
    }

    public function testTopicEditSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/topic/1/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit topic');
    }

    public function testTopicSubmitSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/topic/1/edit');
        $crawler = $client->submitForm('Submit', [
            'topic_admin[description]' => 'changed description',
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/topic/1/show');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/topic/1/remove');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Yes');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/topic/1/show');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testTopic404NotFound(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/topic/10000000/edit');
        $this->assertResponseStatusCodeSame(404);
    }
}
