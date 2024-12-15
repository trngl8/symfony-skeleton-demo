<?php

namespace App\Controller\Admin;

use App\Button\LinkToRoute;
use App\Entity\Profile;
use App\Form\Admin\ProfileAdminType;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/admin/profile', name: 'admin_profile_')]
class ProfileController extends AbstractController
{
    private $doctrine;

    private $repository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        /** @var ProfileRepository repository */
        $this->repository = $this->doctrine->getRepository(Profile::class);
    }

    #[Route('', name: 'index')]
    public function index() : Response
    {
        $topics = $this->repository->findBy([]);

        return $this->render('profile/admin/index.html.twig', [
            'button' => new LinkToRoute('admin_profile_add', 'button.add', 'Add'),
            'items' => $topics,
        ]);
    }

    #[Route('/current', name: 'current_show')]
    public function current() : Response
    {
        $user = $this->getUser();
        $profile = $this->repository->findOneBy(['email' => $user->getUserIdentifier()]);

        return $this->render('profile/admin/current.html.twig', [
            'item' => $profile,
        ]);
    }
    #[Route('/current/edit', name: 'current_edit')]
    public function currentEdit(Request $request) : Response
    {
        $user = $this->getUser();
        $profile = $this->repository->findOneBy(['email' => $user->getUserIdentifier()]);

        //TODO: action if profile does not exists
        $form = $this->createForm(ProfileAdminType::class, $profile);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $session->set('_locale', $profile->getLocale());
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.updated');

            return $this->redirectToRoute('admin_profile_current_show');
        }

        return $this->render('profile/admin/edit.html.twig', [
            'item' => $profile,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/show', name: 'show')]
    public function show(int $id) : Response
    {
        $profile = $this->doctrine->getRepository(Profile::class)->find($id);

        if(!$profile) {
            throw new NotFoundHttpException(sprintf("Profile %d not found", $id));
        }

        return $this->render('profile/admin/show.html.twig', [
            'item' => $profile,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileAdminType::class, $profile);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.created');

            return $this->redirectToRoute('admin_profile_index');
        }

        return $this->render('profile/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST', 'HEAD'] )]
    public function edit(Request $request, int $id) : Response
    {
        $profile = $this->doctrine->getRepository(Profile::class)->find($id);

        if(!$profile) {
            throw new NotFoundHttpException(sprintf("Profile %d not found", $id));
        }

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.updated');

            return $this->redirectToRoute('admin_profile_index');
        }

        return $this->render('profile/admin/edit.html.twig', [
            'item' => $profile,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/remove', name: 'remove', methods: ['GET', 'POST', 'HEAD'] )]
    public function remove(Profile $check, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($check);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_profile_index');
        }

        return $this->render('profile/admin/remove.html.twig', [
            'item' => $check,
        ]);
    }
}
