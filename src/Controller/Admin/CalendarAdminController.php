<?php

namespace App\Controller\Admin;

use App\Entity\TimeData;
use App\Form\Admin\TimeDataAdminType;
use App\Repository\TimeDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/calendar', name: 'admin_calendar_')]
class CalendarAdminController extends AbstractController
{
    public function __construct(
        private readonly TimeDataRepository $timeDataRepository
    )
    {
    }

    #[Route('', name: 'index')]
    public function default() : Response
    {
        $items = $this->timeDataRepository->findAll();

        return $this->render('admin/calendar/default.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $timeData = new TimeData();
        $form = $this->createForm(TimeDataAdminType::class, $timeData);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->timeDataRepository->save($timeData, true);

            $this->addFlash('success', 'flash.success.created');

            return $this->redirectToRoute('admin_calendar_index');
        }

        return $this->render('admin/calendar/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/remove', name: 'remove', methods: ['GET', 'POST', 'HEAD'] )]
    public function remove(TimeData $timeData, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->timeDataRepository->remove($timeData, true);

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_calendar_index');
        }

        return $this->render('admin/calendar/remove.html.twig', [
            'item' => $timeData,
        ]);
    }
    #[Route('/{id}/show', name: 'show')]
    public function show(TimeData $timeData): Response
    {
        return $this->render('admin/calendar/item.html.twig', [
            'item' => $timeData
        ]);
    }
}
