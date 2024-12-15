<?php

namespace App\EventSubscriber;

use App\Service\MessageService;
use App\Service\OfferService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;

    private $security;

    private $messageService;

    private $offerService;

    public function __construct(Environment $twig, Security $security, MessageService $messageService, OfferService $offerService)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->messageService = $messageService;
        $this->offerService = $offerService;
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        // Check cart in cookies
        $request = $event->getRequest();
        $cookie = $request->cookies->get('cart');
        $cart = $cookie ? json_decode($cookie, true) : [];

        if($cart) {
            $cartItems = $this->offerService->getCartOrders($cart);

            if(count($cartItems) > 0) {
                //$this->twig->addGlobal('cart_items_count', count($cartItems));
            }
        }

        $topMenu = [
            'index' => [
                'title' => 'menu.home',
                'route' => 'default_index',
                'url' => '/index',
            ],
            'products' => [
                'title' => 'menu.products',
                'route' => 'app_product_index',
                'url' => '/product',
            ],
            'projects' => [
                'title' => 'menu.projects',
                'route' => 'app_project_index',
                'url' => '/project',
            ],
            'offers' => [
                'title' => 'menu.offers',
                'route' => 'app_offer_index',
                'url' => '/offer',
            ],
            'features' => [
                'title' => 'menu.features',
                'route' => 'topic_index',
                'url' => '/topic',
            ],
            'calendar' => [
                'title' => 'menu.calendar',
                'route' => 'app_calendar_default',
                'url' => '/calendar',
            ],
        ];

        $this->twig->addGlobal('top_menu', $topMenu);

        // Check user messages
        $user = $this->security->getUser();
        if(!$user)  {
            return;
        }

        $countMessages = $this->messageService->findIncomingCount($user->getUserIdentifier());

        if($countMessages > 0) {
            $this->twig->addGlobal('incoming_unread_count', $countMessages);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
