<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils, string $appTheme, string $defaultModule): Response
    {
        $user = $this->getUser();

        if($user->getUsername() === $authenticationUtils->getLastUsername()) {
            $this->addFlash('warning', 'flash.warning.already_logged_in');
            return $this->redirectToRoute($defaultModule);
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        if($error) {
            $this->addFlash('error', 'Error!');
        }

        $template = 'login/index.html.twig';

        if($appTheme === 'market') {
            $template = 'market/index.html.twig';
        }

        return $this->render($template, [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
