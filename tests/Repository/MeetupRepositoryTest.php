<?php

namespace App\Tests\Repository;

use App\Entity\Meetup;
use App\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MeetupRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $repository;
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Meetup::class);
    }

    public function testMeetupOperations()
    {
        $meetup = new Meetup('Meetup 4', new \DateTime('2024-01-01 20:30:00'));
        $this->repository->add($meetup);
        $this->repository->save();

        $this->assertNotNull($meetup->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->repository = null;
    }
}
