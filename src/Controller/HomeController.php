<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function home() : Response
    {
        return $this->render('themes/default/home.html.twig');
    }

    public function info() : Response
    {
        return $this->render('default/info.html.twig');
    }
}
