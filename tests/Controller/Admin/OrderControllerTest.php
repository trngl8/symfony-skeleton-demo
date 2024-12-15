<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{

    public function testAdminOrdersSuccess(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/order');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Orders');

    }

    public function testCreateOrderSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/order/add');

        $this->assertResponseIsSuccessful();

        //TODo: check name (translation)
        $crawler = $client->submitForm('Submit order', [
            'order_admin[description]' => 'test',
            'order_admin[currency]' => 'UAH',
            'order_admin[deliveryEmail]' => 'test@test.com',
            'order_admin[deliveryPhone]' => 'test@test.com',
            'order_admin[amount]' => 200,
        ]);

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/order/1/show');

        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/order/1/edit');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Submit order', [
            'order_admin[description]' => 'test',
            'order_admin[currency]' => 'UAH',
            'order_admin[deliveryEmail]' => 'test@test.com',
            'order_admin[deliveryPhone]' => 'test@test.com',
            'order_admin[amount]' => 200,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
