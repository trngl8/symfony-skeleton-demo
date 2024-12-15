<?php

namespace App\Tests\Repository;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $repository;
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Order::class);
    }

    public function testSearchBySlug()
    {
        $page = $this->repository
            ->findOneBy(['action' => 'pay']);

        $this->assertSame(25, $page->getAmount());
    }

    public function testOrderOperations()
    {
        $order =new Order();
        $order->setAction('pay');
        $order->setCurrency('UAH');
        $order->setAmount(200);
        $order->setDeliveryEmail('delivery@test.com');
        $order->setDeliveryPhone('delivery@test.com');
        $order->setDescription('description');
        $order->setDeliveryName('name');
        $this->repository->add($order, true);

        $result = $this->repository->findOneBy(['action' => 'pay']);

        $this->assertSame('pay', $result->getAction());

        $this->repository->remove($order, true);

        $result = $this->repository->findOneBy(['action' => 'pay']);

        $this->assertEquals('pay', $result->getAction(), 'pay');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->repository = null;
    }
}
