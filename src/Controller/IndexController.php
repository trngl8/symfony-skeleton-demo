<?php

namespace App\Controller;

use App\Repository\BlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    private $blockRepository;
    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    #[Route('/index', name: 'app_index')]
    public function index(Request $request): Response
    {
        $defaultHero = 'scrum';
        $params = $request->query->all();

        $form = $this->createFormBuilder()
            ->add('theme', ChoiceType::class, [
                'choices' => [
                    'Default' => 'default',
                    'Simple' => 'simple',
                    'Blog' => 'blog',
                ],
                'label' => 'Choose theme',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $theme = $form->getData()['theme'];
            $response = $this->redirectToRoute('app_index', ['hero' => $params['hero'] ?? $defaultHero]);
            $response->headers->setCookie(Cookie::create('APP_THEME', $theme));
            return $response;
        }

        $blocks = $this->blockRepository->findByRoute('/index'); // TODO: get URI from request

        return $this->render('index/index.html.twig', [
            'hero' => $params['hero'] ?? $defaultHero,
            'blocks' => $blocks,
            'form' => $form->createView(),
        ]);
    }
}
