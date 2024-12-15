<?php

namespace App\Service;

use App\Entity\Invite;
use App\Entity\Profile;
use App\Model\Subscribe;
use App\Repository\ProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Subscribe Service is a default module.
 * Should be implemented as interface (ModuleInterface) in a next release
 */
class SubscribeService
{
    private $doctrine;

    private $profiles;

    private $messageService;

    private $adminEmail;

    public function __construct(ManagerRegistry $doctrine, ProfileRepository $profiles, MessageService $messageService, string $adminEmail)
    {
        $this->doctrine = $doctrine;
        $this->profiles = $profiles;
        $this->messageService = $messageService;
        $this->adminEmail = $adminEmail;
    }

    public function validateSubscription(Subscribe $subscribe) : bool
    {
        //TODO: add validation criteria
        if(!$subscribe->getType()) {
            return false;
        }

        return true;
    }

    public function initSubscribe(Subscribe $subscribe) : Invite
    {
        if(!$this->validateSubscription($subscribe)) {
            //TODO: throw validation results
            throw new \RuntimeException("Subscribe invalid");
        }

        //TODO: check find criteria
        $profile = $this->profiles->findOneBy([$subscribe->getType() => $subscribe->email]);

        if(!$profile) {
            $profile = (new Profile())
                ->setName($subscribe->name)
                ->setEmail($subscribe->email)
                ->setTimezone(date_default_timezone_get())
                ->setLocale($subscribe->locale)
                ->setActive(false)
            ;
        }

        $this->doctrine->getManager()->persist($profile);

        $token = bin2hex(random_bytes(16));

        //subscribe request
        $invite = (new Invite())
            ->setEmail($subscribe->email)
            ->setName($subscribe->name)
            ->setDescription($token) //TODO: find a better way to store token
            ->setLifetime(1000)
            ->setProfile($profile)
        ;

        $this->doctrine->getManager()->persist($invite);
        $this->doctrine->getManager()->flush();

        //TODO: choose email renderer
        $email = (new TemplatedEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->from($this->adminEmail) //TODO: default sender
            ->to(new Address($subscribe->email))
            ->subject('Invite or subscribe request') //TODO: translate
            ->htmlTemplate('email/confirm.html.twig')
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'message' => 'You have been invited to a service', //TODO: Service name
                'subscribe_token' => $token
            ])
        ;

        $this->messageService->send($email);

        return $invite;
    }
}
