<?php

namespace App\Tests\Service;

use App\DataFixtures\ProfileFixtures;
use App\Entity\Invite;
use App\Entity\Profile;
use App\Model\Subscribe;
use App\Service\MessageService;
use App\Service\SubscribeService;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubscribeServiceTest extends KernelTestCase

{
    private $doctrine;
    private $profiles;

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();

        $this->doctrine = $kernel->getContainer()->get('doctrine');
        $this->profiles = $this->doctrine->getManager()->getRepository(Profile::class);

        $this->databaseTool = $kernel->getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadFixtures([
            ProfileFixtures::class
        ]);
    }

    public function testProcessSubscribeSuccess() : void
    {
        $messageService = $this->getMockBuilder(MessageService::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $subscribeService = new SubscribeService($this->doctrine, $this->profiles, $messageService, 'admin@admin.com');

        //TODO: send and check confirm email link
        //TODO: choose confirmations levels
        $subscription = new Subscribe();
        $subscription->email = 'test@test.com';
        $subscription->name = 'Test';
        $subscription->locale = 'uk';

        $result = $subscribeService->initSubscribe($subscription);

        $this->assertInstanceOf( Invite::class, $result);
        $this->assertNotNull($result->getLifetime());
        $this->assertFalse($result->getProfile()->getActive());

    }
}
