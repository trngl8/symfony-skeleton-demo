<?php

namespace App\Controller\Admin;

use App\Button\LinkToRoute;
use App\Entity\Invite;
use App\Form\Admin\InviteAdminType;
use App\Form\Filter\InviteAdminFilter;
use App\Service\InviteService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/invite', name: 'admin_invite_')]
class InviteController extends AbstractController
{
    CONST PAGINATOR_COUNT = 20;
    CONST START_PAGE = 1;
    CONST MIN_COUNT = 0;

    private $doctrine;

    private $inviteService;

    public function __construct(ManagerRegistry $doctrine, InviteService $inviteService)
    {
        $this->doctrine = $doctrine;
        $this->inviteService = $inviteService;
    }

    #[Route('', name: 'index')]
    public function index(Request $request) : Response
    {
        $page = $request->get('page') ?? self::START_PAGE;

        $paginator = $this->inviteService->addCriteria([])->getPaginator($page, self::PAGINATOR_COUNT);
        $c = count($paginator);

        $filters = $this->createForm(InviteAdminFilter::class);

        $filters->handleRequest($request);

        if($filters->isSubmitted() && $filters->isValid()) {
            $filterData = $filters->getData();

            if($filters->get('clear')->isClicked()) {
                $this->addFlash('warning', 'flash.warning.filter_cleared');
                return $this->redirectToRoute('admin_invite_index');
            }

            if($filters->get('save')->isClicked()) {
                $this->addFlash('success', sprintf('flash.success.filter_save %d items', $c));
                return $this->redirectToRoute('admin_invite_index');
            }

            if($filters->get('apply')->isClicked()) {
                return $this->redirectToRoute('admin_invite_index', ['filters' => $filterData]);
            }
        }

        if($c === self::MIN_COUNT) {
            $pages = $pages ?? [];
        }

        if($c > 0) {
            $pages = range(self::START_PAGE, ceil($c / (self::PAGINATOR_COUNT + 1)));
        }

        return $this->render('invite/admin/index.html.twig', [
            'button' => new LinkToRoute('invite_add', 'button.add', 'Add'),
            'paginator' => $paginator,
            'count' => $c,
            'page' => $page,
            'pages' => $pages,
            'filters' => $filters->createView(),
            'aria_filter_expanded' => true
        ]);
    }
    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $invite = new Invite();
        $form = $this->createForm(InviteAdminType::class, $invite);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($invite);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.created');

            return $this->redirectToRoute('admin_invite_index');
        }

        return $this->render('invite/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/show/{id}', name: 'show', methods: ['GET', 'HEAD'] )]
    public function show(Invite $invite) : Response
    {
        return $this->render('invite/admin/show.html.twig', [
            'item' => $invite,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST', 'HEAD'] )]
    public function edit(Request $request, int $id) : Response
    {
        $invite = $this->doctrine->getRepository(Invite::class)->find($id);

        if(!$invite) {
            throw new NotFoundHttpException(sprintf("Topic %d not found", $id));
        }

        $form = $this->createForm(InviteAdminType::class, $invite);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            $entityManager->persist($invite);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.updated');

            return $this->redirectToRoute('admin_invite_index');
        }

        return $this->render('invite/admin/edit.html.twig', [
            'item' => $invite,
            'form' => $form->createView()
        ]);
    }

    #[Route('/remove/{id}', name: 'remove', methods: ['GET', 'POST', 'HEAD'] )]
    public function remove(Invite $item, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($item);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_invite_index');
        }

        return $this->render('invite/admin/remove.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/export', name: 'export', methods: ['GET', 'POST', 'HEAD'] )]
    public function export() : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $invites = $this->inviteService->addCriteria([])->getPaginator(1, 1000);
        $rows = array_map(function ($item) {
            return [
                $item->getId(),
                $item->getName(),
                $item->getDescription(),
                $item->getEmail(),
                $item->getPhone(),
                $item->getCreatedAt()->format('Y-m-d H:i:s'),
                $item->getClosedAt() ? $item->getClosedAt()->format('Y-m-d H:i:s') : null
            ];
        }, $invites->getIterator()->getArrayCopy());

        //TODO: generate columns dynamically
        $columns = ['#', 'Name', 'Description', 'Email', 'Phone', 'Created At', 'Closed At'];
        $list =[
            $columns,
            ...$rows
        ];

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="invites-export-%s.csv"', date('Y-m-d')));

        return $response;
    }
}
