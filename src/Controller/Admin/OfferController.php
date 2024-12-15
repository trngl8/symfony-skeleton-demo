<?php


namespace App\Controller\Admin;

use App\Button\LinkToRoute;
use App\Entity\Offer;
use App\Form\Admin\OfferAdminType;
use App\Form\Admin\OfferEditAdminType;
use App\Model\OfferRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/admin/offer', name: 'admin_offer_')]
class OfferController extends AbstractController
{
    private $doctrine;

    private $repository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository(Offer::class);
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $items = $this->repository->findBy([]);

        $button = new LinkToRoute('offer_add', 'button.add', 'Add');

        if (sizeof($items) === 0) {
            $this->addFlash('warning', 'flash.warning.no_items');
        }

        return $this->render('offer/admin/index.html.twig', [
            'items' => $items,
            'button' => $button
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $offerRequest = new OfferRequest();
        $form = $this->createForm(OfferAdminType::class, $offerRequest);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $offer = new Offer($offerRequest->title, $offerRequest->currency, $offerRequest->amount);
            $this->doctrine->getManager()->persist($offer);
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.offer_created');

            return $this->redirectToRoute('admin_offer_index');
        }

        return $this->render('offer/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST', 'HEAD'] )]
    public function edit(Request $request, int $id) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $offer = $this->doctrine->getRepository(Offer::class)->find($id);

        if(!$offer) {
            throw new NotFoundHttpException(sprintf("Offer %d not found", $id));
        }

        $form = $this->createForm(OfferEditAdminType::class, $offer);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.offer_updated');

            //TODO: choose next action dynamically

            $nextAction = 'admin_offer_index';

            return $this->redirectToRoute($nextAction);
        }

        return $this->render('offer/admin/add.html.twig', [
            'item' => $offer,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/remove', name: 'remove', methods: ['GET', 'POST', 'HEAD'])]
    public function remove(Offer $offer, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->doctrine->getManager()->remove($offer);
            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_offer_index');
        }

        return $this->render('offer/admin/remove.html.twig', [
            'item' => $offer,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET', 'HEAD'])]
    public function show(Offer $offer, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('offer/admin/show.html.twig', [
            'item' => $offer,
        ]);
    }
}
