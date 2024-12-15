<?php

namespace App\EventListener;

use App\Entity\Invite;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Invite::class)]
class InviteCreatedListener
{
    private $mailer;

    private $adminEmail;

    public function __construct(MailerInterface $mailer, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function postPersist(Invite $invite, LifecycleEventArgs $event): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail, $this->adminEmail))
            ->to($invite->getEmail())
            ->subject('Somebody invited you to join the site') //TODO: write inviter and site in subject
            ->htmlTemplate('invite/email_new.html.twig')
            ->context([
                'invite' => $invite,
            ])
        ;

        $this->mailer->send($email);
    }
}
