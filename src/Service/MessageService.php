<?php

namespace App\Service;

use App\Model\Message;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MessageService implements ServiceInterface
{
    CONST MESSAGES_DB = 'messages.db';

    private MailerInterface $mailer;

    private $adminEmail;

    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->adminEmail = $adminEmail;
    }

    public function create() : Message
    {
        return new Message($this->adminEmail);
    }

    /**
     * @deprecated
     */
    public function compose(Message $message) : Email
    {
        //TODO: choose email renderer
        //$email = (new Email());

        //TODO: store message
        $email = (new TemplatedEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->from($this->adminEmail) //TODO: default sender
            ->to(new Address($message->to))
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('reply@example.com')
            ->subject($message->subject)
            ->htmlTemplate('email/confirm.html.twig')
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'message' => $message->text,
                'subscribe_token' =>  ''
            ])
        ;
        return $email;
    }

    public function send(Email $email) : void
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        $this->logger->info(sprintf("Message sent to %s", implode(',', array_map(function($item) {return $item->getAddress();}, $email->getTo()))));

    }

    public function save(Message $message): void
    {
        if($message->to === $this->adminEmail) {
            $this->logger->debug(sprintf("Enable debug to %s", $message->to), ['message' => $message->subject]);
        }

        $this->logger->info(sprintf("Message sent to %s", $message->to), ['test' => 'test']);


        $db = new \SQLite3(sprintf('../var/%s', self::MESSAGES_DB), SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);

        //TODO: move to the migration script
        $db->query('CREATE TABLE IF NOT EXISTS messages(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            recipient VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            status VARCHAR(32) NOT NULL,
            text TEXT,
            time DATETIME
        )');

        $date = date('Y-m-d H:i:s');

        $db->exec('BEGIN');
        $db->query(sprintf('INSERT INTO messages(recipient, subject, status, text, time) VALUES ("%s", "%s", "%s", "%s", "%s")', $message->to, $message->subject, 'new', $message->text, $date));
        $db->exec('COMMIT');

        //TODO: should be a prepared statement
        //$db->exec("INSERT INTO messages(recipient, subject, text, time) VALUES(
        //                                        'rec', 'subj', 'text', '2021-01-01 00:00:00')");


        $db->close();


        //Write to csv file
        $list = [
            [$message->to, $message->subject, $message->text, $date],
        ];

        $fp = fopen(sprintf('../var/%s.csv', self::MESSAGES_DB), 'w');

        //TODO: add records
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

    }

    public function findIncomingCount(string $address) : int
    {
        try {
            $db = new \SQLite3(sprintf('../var/%s', self::MESSAGES_DB), SQLITE3_OPEN_READONLY);
            $db->enableExceptions(true);
            $usersCount = $db->querySingle(sprintf('SELECT COUNT(id) FROM messages  WHERE recipient="%s"', $address));
            $db->close();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $usersCount = 0;
        }

        return $usersCount;
    }

    public function findIncoming(string $adress): array
    {
        $list = [];

        try {
            $db = new \SQLite3(sprintf('../var/%s', self::MESSAGES_DB), SQLITE3_OPEN_READONLY);

            $db->enableExceptions(true);

            //$usersCount = $db->querySingle('SELECT COUNT(DISTINCT recipient) FROM messages');

            $statement = $db->prepare('SELECT * FROM messages WHERE recipient = ?');
            $statement->bindValue(1, $adress);

            $messages = $statement->execute();


            while ($item = $messages->fetchArray(SQLITE3_ASSOC)) {
                $list[] = $item;
            }

            $db->close();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $list;
    }
}
