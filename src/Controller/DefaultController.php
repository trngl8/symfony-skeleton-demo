<?php

namespace App\Controller;

use App\Button\LinkToRoute;
use App\Exception\ThemeLayoutNotFoundException;
use App\Repository\BlockRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;

class DefaultController
{
    private BlockRepository $blockRepository;

    private Environment $twig;

    private LoggerInterface $logger;

    public function __construct(Environment $twig, LoggerInterface $logger, BlockRepository $blockRepository)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->blockRepository = $blockRepository;
    }

    public function default(Request $request) : Response
    {
        if (!$request->cookies->has('APP_THEME')) {
            return $this->index();
        }

        $templateName = sprintf('%s.html.twig', $request->cookies->get('APP_THEME'));
        try {
            $template = $this->twig->load($templateName);
        } catch (LoaderError $e) {
            $this->logger->error(sprintf("Custom theme not found: %s", $e->getMessage()));
            throw new ThemeLayoutNotFoundException(sprintf("Template %s not found", $templateName));
        }

        $buttons = [
            new LinkToRoute('login', 'first', 'Enter'),
            new LinkToRoute('app_index', 'second', 'Reset'),
        ];

        $content = $template->render([
            'buttons' => $buttons,
            'has_login' => false,
        ]);

        return $this->createResponse($content);
    }

    public function index() : Response
    {
        try {
            $template = $this->twig->load('default.html.twig');
        } catch (LoaderError $e) {
            $this->logger->error($e->getMessage());
            throw new ThemeLayoutNotFoundException("Template not found");
        }

        $blocks = $this->blockRepository->findByRoute('/');

        $buttons = [
            new LinkToRoute('app_index', 'first', 'Enter')
        ];

        $content = $template->render([
            'buttons' => $buttons,
            'blocks' => $blocks,
            'has_login' => false,
        ]);

        return $this->createResponse($content);
    }

    private function createResponse(string $content) : Response
    {
        return (new Response())->setContent($content);
    }
}
