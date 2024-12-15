<?php

namespace App\Menu;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class Menu
{
    public function __construct(
        private readonly RouterInterface $router
    )
    {
    }

    public function getTopMenu(int $max): array
    {
        $menu = [
            'index' => [
                'title' => 'Home',
                'route' => 'default',
                'url' => '/',
            ],
            'meetup' => [
                'title' => 'Meetups',
                'route' => 'app_meetups_index',
                'url' => '/meetup',
            ],
        ];

        $router = $this->router;

        $menu = array_filter($menu, function ($item) use ($router) {
            try {
                $router->generate($item['route']);
                return true;
            } catch (RouteNotFoundException $e) {
                //TODO: log error
                return false;
            }
        });

        return array_slice($menu, 0, $max);
    }
}
