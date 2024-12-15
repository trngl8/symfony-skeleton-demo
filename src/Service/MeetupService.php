<?php

namespace App\Service;

use App\Entity\Meetup;
use App\Entity\Subscribe;
use App\Repository\MeetupRepository;
use App\Service\Http\TelegramHttpClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;

class MeetupService
{
    const SUCCESS = 1;
    const ALREADY_JOINED = 2;

    public function __construct(
        private MeetupRepository $meetupRepository,
        private TelegramHttpClient $telegramClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private string $adminEmail
    )
    {
    }

    public function join(Meetup $meetup, UserInterface $user): int
    {
        $exists = $this->entityManager->getRepository(Subscribe::class)->findOneBy([
            'type' => 'meetup',
            'target' => $meetup->getId(),
            'email' => $user->getUserIdentifier()
        ]);

        if ($exists) {
            return self::ALREADY_JOINED;
        }

        $subscribe = new Subscribe(
            'meetup',
            $meetup->getId(),
            $user->getUserIdentifier(),
            $user->getUserIdentifier()
        );

        $this->entityManager->persist($subscribe);
        $this->entityManager->flush();

        $token = bin2hex(random_bytes(16));
        $email = (new TemplatedEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->from($this->adminEmail)
            ->to(new Address($user->getUserIdentifier()))
            ->subject('Confirm Meetup Join')
            ->htmlTemplate('email/confirm_meetup.html.twig')
            ->context([
                'meetup' => $meetup,
                'expiration_date' => new \DateTime('+7 days'),
                'token' => $token
            ])
        ;

        $this->mailer->send($email);

        if ($_ENV['TELEGRAM_CHAT_ID']) {
            $this->telegramClient->sendMessage($_ENV['TELEGRAM_CHAT_ID'], sprintf(
                'New user joined for the meetup #%d: <br>%s',
                $meetup->getId(),
                $user->getUserIdentifier(),
            ));
        } else {
            $this->logger->warning('Telegram chat id not configured!');
        }

        return self::SUCCESS;
    }

    public function subscribe(Meetup $meetup, array $data): void
    {
        $subscribe = new Subscribe(
            'meetup',
            $meetup->getId(),
            $data['email'],
            $data['name']
        );

        $this->entityManager->persist($subscribe);
        $this->entityManager->flush();

        $token = bin2hex(random_bytes(16));
        $email = (new TemplatedEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->from($this->adminEmail)
            ->to(new Address($data['email'],  $data['name']))
            ->subject('Join Meetup Request')
            ->htmlTemplate('email/confirm_meetup.html.twig')
            ->context([
                'meetup' => $meetup,
                'expiration_date' => new \DateTime('+7 days'),
                'token' => $token
            ])
        ;
        $this->mailer->send($email);

        if ($_ENV['TELEGRAM_CHAT_ID']) {
            $this->telegramClient->sendMessage($_ENV['TELEGRAM_CHAT_ID'], sprintf(
                'New subscriber for the meetup #%d: <br>%s %s',
                $meetup->getId(),
                $data['email'],
                $data['name']
            ));
        } else {
            $this->logger->warning('Telegram chat id not configured!');
        }
    }

    public function getSubscribers(Meetup $meetup): array
    {
        return $this->entityManager->getRepository(Subscribe::class)->findBy([
            'type' => 'meetup',
            'target' => $meetup->getId()
        ]);
    }
}
