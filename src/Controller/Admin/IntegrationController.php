<?php

namespace App\Controller\Admin;

use App\Form\ChatMessageType;
use App\Model\ChatMessage;
use App\Service\Http\TelegramHttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/integration', name: 'admin_integration_')]
class IntegrationController extends AbstractController
{
    private $telegramClient;

    public function __construct(TelegramHttpClient $telegramService)
    {
        $this->telegramClient = $telegramService;
    }

    #[Route('', name: 'index')]
    public function index() : Response
    {
        // TODO get bot ids from the storage
        return $this->render('admin/integration/index.html.twig', [
            'providers' => [
                'telegram' => [
                    $this->telegramClient->getBotId()
                ]
            ]
        ]);
    }

    #[Route('/telegram/{id}/show', name: 'telegram_show')]
    public function provider() : Response
    {
        $me = $this->telegramClient->responseToArray($this->telegramClient->getMe());
        $webhook = $this->telegramClient->responseToArray($this->telegramClient->getWebhookInfo());

        $result = [
            'me' =>  $me,
            'webhook' => $webhook
        ];

        if(!$webhook['url']) {
            //TODO: check url valid
        }

        $updates = $this->telegramClient->responseToArray($this->telegramClient->getUpdates());

        return $this->render('admin/integration/show.html.twig', [
            'result' => $result,
            'updates' => $updates
        ]);
    }

    #[Route('/telegram/{id}/chat', name: 'telegram_chat')]
    public function chat(int $id, Request $request) : Response
    {
        $updates = $this->telegramClient->responseToArray($this->telegramClient->getUpdates());

        //TODO: looks like filter here
        $result = [];
        foreach ($updates as $item) {
            if($item['message']['chat']['id'] === $id) {
                $result[] = $item;
            }
        }

        $message = new ChatMessage($id);
        $form = $this->createForm(ChatMessageType::class, $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $result = $this->telegramClient->responseToArray($this->telegramClient->sendMessage($message->chatId, $message->text));

            //TODO: save result into message storage
            $this->addFlash('success', 'flash.success.sent');

            return $this->redirectToRoute('admin_integration_telegram_chat', ['id' => $id]);
        }

        return $this->render('admin/integration/chat.html.twig', [
            'updates' => $result,
            'form' => $form->createView()
        ]);
    }
}
