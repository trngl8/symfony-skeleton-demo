<?php


namespace App\Controller\Admin;

use App\Button\LinkToRoute;
use App\Entity\Order;
use App\Form\Admin\OrderAdminType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/admin/order', name: 'admin_order_')]
class OrderController extends AbstractController
{
    private $doctrine;

    private $repository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository(Order::class);
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $items = $this->repository->findBy([]);

        $button = new LinkToRoute('order_add', 'button.add', 'Add');

        if (sizeof($items) === 0) {
            $this->addFlash('warning', 'flash.warning.no_items');
        }

        return $this->render('order/admin/index.html.twig', [
            'items' => $items,
            'button' => $button
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $order = new Order();
        $form = $this->createForm(OrderAdminType::class, $order);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->doctrine->getManager()->persist($order);
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.order_created');

            return $this->redirectToRoute('admin_order_index');
        }

        return $this->render('order/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST', 'HEAD'] )]
    public function edit(Request $request, int $id) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $order = $this->doctrine->getRepository(Order::class)->find($id);

        if(!$order) {
            throw new NotFoundHttpException(sprintf("Order %d not found", $id));
        }

        $form = $this->createForm(OrderAdminType::class, $order);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.offer_updated');

            //TODO: choose next action dynamically

            $nextAction = 'admin_order_index';

            return $this->redirectToRoute($nextAction);
        }

        return $this->render('order/admin/add.html.twig', [
            'item' => $order,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/remove', name: 'remove', methods: ['GET', 'POST', 'HEAD'])]
    public function remove(Order $order, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->doctrine->getManager()->remove($order);
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_order_index');
        }

        return $this->render('order/admin/remove.html.twig', [
            'item' => $order,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET', 'HEAD'])]
    public function show(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('order/admin/show.html.twig', [
            'item' => $order,
        ]);
    }
}
