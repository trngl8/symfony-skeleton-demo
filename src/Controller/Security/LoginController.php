<?php

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
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

        $userRecord = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);

        if (!$userRecord->isVerified()) {
            return $this->redirectToRoute('app_user_verify');
        }

        return $this->render(sprintf('themes/%s/login.html.twig', $theme));
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
