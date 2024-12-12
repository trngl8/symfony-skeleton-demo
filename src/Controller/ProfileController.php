<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private EntityManagerInterface $em;

    private ProfileRepository $repository;

    public function __construct(ProfileRepository $repository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $profile = $this->repository->findOneBy(['email' => $user->getUserIdentifier()]);

        if(!$profile) {
            //TODO: or 404 ?

            $this->addFlash('warning', 'no_active_profile');

            return $this->redirectToRoute('app_profile_edit');
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/profile/invites', name: 'app_profile_invites')]
    public function invites(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $profile = $this->repository->findOneBy(['email' => $user->getUserIdentifier()]);

        if(!$profile) {
            return $this->redirectToRoute('app_profile_edit');
        }

        return $this->render('profile/invites.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $profile = $this->repository->findOneBy(['email' => $user->getUserIdentifier()]);

        if(!$profile) {
            $profile = (new Profile())
                ->setEmail($user->getUserIdentifier())
                ->setActive(true);
        }

        if ($request->cookies->has('APP_THEME')) {
            $profile->theme = $request->cookies->get('APP_THEME');
        }

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $profile->setEmail($user->getUserIdentifier());

            $this->em->persist($profile);
            $this->em->flush();

            $this->addFlash('success', 'flash.success.profile_updated');
            $response = $this->redirectToRoute('app_profile_edit');
            $response->headers->setCookie(Cookie::create('APP_THEME', $profile->theme));
            $request->cookies->set('APP_THEME', $profile->theme);

            return $response;
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
        ]);
    }

    #[Route('/profile/remove', name: 'app_profile_remove')]
    public function remove(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'no_active_profile');
            return $this->redirectToRoute('app_profile_edit');
        }

        $userRecord = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUserIdentifier()]);
        $userRecord->setVerified(false);

        $profile = $this->repository->findOneBy(['email' => $user->getUserIdentifier()]);

        $this->em->remove($profile);
        $this->em->flush();

        return $this->redirectToRoute('logout');
    }
}
