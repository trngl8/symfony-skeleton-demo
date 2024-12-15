<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/admin/user', name: 'admin_user_')]
class AdminUserController extends AbstractController
{
    private $doctrine;

    private $repository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository(User::class);
    }

    #[Route('', name: 'index', methods: ["GET"])]
    public function index(): Response
    {
        $items = $this->repository->findBy([]);

        //$button = new LinkToRoute('user_add', 'button.add');

        return $this->render('admin/user/index.html.twig', [
            'users' => $items,
        ]);
    }
    #[Route('/{id}/show', name: 'show', methods: ["GET"])]
    public function show(int $id) : Response
    {
        $item = $this->repository->findOneBy(['id' => $id]);

        return $this->render('admin/user/show.html.twig', [
            'user' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ["GET", "POST", "PATCH"])]
    public function edit(int $id, Request $request) : Response
    {
        $item = $this->repository->findOneBy(['id' => $id]);

        $form = $this->createForm(UserType::class, $item);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.updated');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $item,
            'form' => $form->createView()
        ]);
    }

    #[Route('/add', name: 'add', methods: ["GET", "POST", "PUT"])]
    public function add(Request $request) : Response
    {
        $item = new User();

        $form = $this->createForm(UserType::class, $item);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->persist($item);
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.created');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/add.html.twig', [
            'user' => $item,
            'form' => $form->createView()
        ]);
    }
}
