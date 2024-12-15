<?php

namespace App\Controller;

use App\Menu\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    public function __construct(
        private readonly Menu $menu,
    )
    {
    }

    public function mainMenu(int $max): Response
    {
        $topMenu = $this->menu->getTopMenu($max);

        return $this->render('menu/main_menu.html.twig', [
            'max' => $max,
            'top_menu' => $topMenu
        ]);
    }
}
