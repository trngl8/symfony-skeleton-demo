<?php

namespace App\Controller\Admin;

use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/market', name: 'admin_market_')]
class MarketController extends AbstractController
{
    #[Route('', name: 'default')]
    public function default(CardRepository $cardsRepository): Response
    {
        //TODO: define criteria, pagination
        $cards = $cardsRepository->findBy([]);

        return $this->render('market/default.html.twig', [
            'controller_name' => 'MarketController',
            'cards' => $cards
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(int $id, CardRepository $cardsRepository): Response
    {
        $card = $cardsRepository->find($id);

        if (!$card) {
            throw $this->createNotFoundException('No card found for id ' . $id);
        }

        return $this->render('market/show.html.twig', [
            'controller_name' => 'MarketController',
            'card' => $card
        ]);
    }
}
