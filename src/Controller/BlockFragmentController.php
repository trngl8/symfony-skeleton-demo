<?php

namespace App\Controller;

use App\Entity\Block;
use App\Repository\MeetupRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/block/fragment', name: 'app_block_fragment_')]
class BlockFragmentController extends AbstractController
{
    #[Route('/meetup/list', name: 'meetup_list', methods: ['GET'])]
    public function meetupList(MeetupRepository $meetupRepository): Response
    {
        return $this->render('meetup/_list.html.twig', [
            'items' => $meetupRepository->findCurrent()
        ]);
    }

    #[Route('/product/list/{id}', name: 'product_list')]
    public function internalList(ProductRepository $productRepository, Block $block): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/_features.html.twig', [
            'products' => $products,
            'block' => $block
        ]);
    }
}
