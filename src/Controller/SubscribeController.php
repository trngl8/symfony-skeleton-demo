<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\Type\SubscribeType;
use App\Form\UserPasswordType;
use App\Model\Subscribe;
use App\Repository\InviteRepository;
use App\Service\SubscribeService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subscribe', name: 'subscribe_')]
class SubscribeController extends AbstractController
{
    private $doctrine;

    private $subscribeService;

    private $inviteRepository;

    public function __construct(ManagerRegistry $doctrine, SubscribeService $subscribeService, InviteRepository $inviteRepository)
    {
        $this->doctrine = $doctrine;
        $this->subscribeService = $subscribeService;
        $this->inviteRepository = $inviteRepository;
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request) : Response
    {
        $user = $this->getUser();

        if($user) {
            $this->addFlash('warning', 'flash.warning.already_logged_in');

            //return $this->redirectToRoute('app_profile_invites');
        }

        $subscription = new Subscribe();
        $form = $this->createForm(SubscribeType::class,  $subscription);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //TODO: check if subscription exists
            //TODO: check an exception
            $invite = $this->subscribeService->initSubscribe($subscription);

            $this->addFlash('success', 'flash.success.subscribe_created');

            return $this->redirectToRoute('subscribe_success', ['id' => $invite->getId()]); //TODO: redirect ot uuid
        }

        return $this->render('subscribe/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/success/{id}', name: 'success')]
    public function success(int $id) : Response
    {
        $item = $this->inviteRepository->findOneBy(['id' => $id]);

        return $this->render('subscribe/success.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/verify', name: 'verify')]
    public function verify(Request $request) : Response
    {
        $token = $request->query->get('token');

        $invite = $this->inviteRepository->findOneBy(['description' => $token]);

        if($token && $invite) {
            //TODO: implement verified flag
            $invite->setDescription('');
            $invite->setVerified(true);

            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.verify');

            return $this->redirectToRoute('subscribe_password', ['id' => $invite->getId()]);
        }

        if(!$invite) {
            $this->addFlash('error', 'flash.error.invalid_token');
        }

        return $this->redirectToRoute('login');
    }

    #[Route('/password/{id}', name: 'password')]
    public function changePassword(int $id, UserPasswordHasherInterface $passwordHasher, Request $request) : Response
    {
        $invite = $this->inviteRepository->findOneBy(['id' => $id]);

        if(!$invite) {
            $this->addFlash('error', 'flash.error.invalid_token');

            return $this->redirectToRoute('login');
        }

        $user = (new User())
            ->setUsername($invite->getEmail());

        $form = $this->createForm(UserPasswordType::class,  $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $form->get('plainPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.password_changed');

            return $this->redirectToRoute('login');
        }

        return $this->render('subscribe/change_password.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
