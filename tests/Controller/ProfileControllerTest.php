<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testGetProfileSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
    }

    public function testEditProfileSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/profile/edit');

        $this->assertResponseIsSuccessful();

        $crawler = $client->submitForm('Submit', [
            'profile[name]' => 'test',
            'profile[email]' => 'test@test.com',
            'profile[locale]' => 'uk',
            'profile[timezone]' => 'Europe/Kyiv',
            'profile[active]' => true,
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();

    }
}
