<?php

namespace App\Controller;

use App\Entity\Check;
use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\CheckRepository;
use App\Service\CheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;

class CheckController extends AbstractController
{
    private $doctrine;

    private $repository;

    private $checkSevice;

    public function __construct(ManagerRegistry $doctrine, CheckService $checkService)
    {
        $this->doctrine = $doctrine;
        $this->checkSevice = $checkService;
        /** @var CheckRepository repository */
        $this->repository = $this->doctrine->getRepository(Check::class);
    }

    #[Route('/check', name: 'check_index')]
    public function index(): Response
    {
        $categories = ['php', 'python', 'java', 'javascript', 'go'];

        $topics = $this->repository->findBy([]);

        return $this->render('check/index.html.twig', [
            'items' => $topics,
            'categories' => $categories,
        ]);
    }

    #[Route('/check/show/{id}', name: 'check_show', methods: ['GET', 'POST', 'HEAD'] )]
    public function show(Request $request, int $id): Response
    {
        $check = $this->doctrine->getRepository(Check::class)->find($id);

        if(!$check) {
            throw new NotFoundHttpException(sprintf("Check %d not found", $id));
        }

        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $position = $this->repository->getMaxOptionPosition($check);
            $option->setPosition(++$position);
            $option->setType($check->getType());

            $check->addOption($option);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($option);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.option_created');

            return $this->redirectToRoute('check_show', ['id' => $check->getId()]);
        }

        return $this->render('check/show.html.twig', [
            'item' => $check,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/check/run/{id}', name: 'check_run', methods: ['GET', 'POST', 'HEAD'] )]
    public function run(Request $request, int $id): Response
    {
        $check = $this->doctrine->getRepository(Check::class)->find($id);

        $builder = $this->createFormBuilder();

        $options = $check->getOptions()->toArray();
        $choices = array_combine(
            array_map(function($item) {
                return $item->getTitle();
                }, $options),
            array_map(function($item) {
                return $item->getId();
            }, $options)
        );

        $builder->add('features', ChoiceType::class, [
            'label' => $check->getDescription(),
            'choices' => $choices,
            'multiple' => $check->getType() === 'multiply',
            'expanded' => true,
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ]);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $entityManager = $this->doctrine->getManager();
            $user = $this->getUser();

            if(is_array($data['features'])) {
                foreach ($data['features'] as $feature) {
                    $option =  $this->checkSevice->getOption($feature);
                    $result = $this->checkSevice->createCheckResult($user, $option);
                    $entityManager->persist($result);
                }
            } else {
                $option =  $this->checkSevice->getOption($data['features']);
                $result = $this->checkSevice->createCheckResult($user, $option);
                $entityManager->persist($result);
            }

            $entityManager->flush();

            $this->addFlash('success', 'flash.success.next');

            return $this->redirectToRoute('check_index');
        }

        return $this->render('check/run.html.twig', [
            'item' => $check,
            'form' => $form->createView()
        ]);
    }

}
