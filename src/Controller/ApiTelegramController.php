<?php

namespace App\Controller;

use App\Service\Http\TelegramHttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiTelegramController extends AbstractController
{
    #[Route('/api/telegram', name: 'api_telegram', methods: ["POST"])]
    public function index(Request $request, TelegramHttpClient $telegramService): JsonResponse
    {
        //TODO: check in the service
        if(array_key_exists('TELEGRAM_BOT_ID', $_ENV) && array_key_exists('TELEGRAM_BOT_TOKEN', $_ENV)) {
            return $this->json(['success' => true]);
        }

        $data = json_decode($request->getContent(), true);

        if(isset($data['message'])) {
            $chatId = $data['message']['chat']['id'];
            // TODO: create chat room with $data['message']['chat']['username']
            // TODO: save creation date
            $chat = $data['message']['chat'];
            $db = new \SQLite3('../var/api.telegram.org.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
            $db->enableExceptions(true);

            $db->query('CREATE TABLE IF NOT EXISTS telegram_chats(
                    id INTEGER PRIMARY KEY NOT NULL,
                    first_name VARCHAR(64) NOT NULL,
                    last_name VARCHAR(64) NOT NULL,
                    username VARCHAR(64) NOT NULL,
                    type VARCHAR(32) NOT NULL,
                    created_at DATETIME
                )');
            $date = date('Y-m-d H:i:s');

            $db->exec('BEGIN');
            $db->query(sprintf('INSERT 
                                        INTO telegram_chats(id, first_name, last_name, username, type, created_at) 
                                        VALUES ("%d", "%s", "%s", "%s", "%s", "%s")',
                                $chat['id'], $chat['first_name'], $chat['last_name'], $chat['username'], $chat['type'], $date));
            $db->exec('COMMIT');
            $db->close();

            if('/start' === $data['message']['text']) {
                // input here
                $telegramService->sendMessage($chatId, sprintf("Hello %s", $data['message']['chat']['first_name']));
                $telegramService->sendMessage($chatId, "Write your question: ");
            } else {
                // TODo: save message before sending

                $telegramService->sendMessage($chatId, "Write your question: ");
            }
        }

        return $this->json(['success' => false]);
    }
}
