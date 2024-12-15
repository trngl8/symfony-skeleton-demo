<?php

namespace App\Controller\Admin;

use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminDashboardController extends AbstractController
{
    private $messageService; //TODO: should be interface

    public function __construct(MessageService $messageService)
    {
        $this->messageService  = $messageService;
    }

    public function admin(): Response
    {
        return $this->render('admin.html.twig');
    }

    public function inbox(): Response
    {
        $user = $this->getUser();

        $messages = $this->messageService->findIncoming($user->getUserIdentifier());

        return $this->render('admin/inbox.html.twig', ['inbox' => $messages]);
    }
}
