<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils, string $adminEmail): Response
    {
        $user = $this->getUser();

        $theme = 'default';

        if (!$user) {
            $lastError = $authenticationUtils->getLastAuthenticationError();

            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render(sprintf('themes/%s/login.html.twig', $theme), [ // the same is login/index.html.twig
                'error' => $lastError,
                'last_username' => $lastUsername,
            ]);
        }

        if ($adminEmail === $user->getUserIdentifier()) {
            //return $this->redirectToRoute('admin');
        }

        return $this->render(sprintf('themes/%s/login.html.twig', $theme));
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
