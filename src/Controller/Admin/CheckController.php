<?php

namespace App\Controller\Admin;

use App\Button\LinkToRoute;
use App\Entity\Check;
use App\Entity\Result;
use App\Form\CheckType;
use App\Repository\CheckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/admin/check', name: 'admin_check_')]
class CheckController extends AbstractController
{
    private $doctrine;

    private $repository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        /** @var CheckRepository repository */
        $this->repository = $this->doctrine->getRepository(Check::class);
    }

    #[Route('', name: 'index')]
    public function index() : Response
    {
        $items = $this->repository->findBy([]);

        $button = new LinkToRoute('check_add', 'button.add', 'Add');

        if(sizeof($items) === 0) {
            $this->addFlash('warning', 'flash.warning.no_items');
        }

        return $this->render('check/admin/index.html.twig', [
            'items' => $items,
            'button' => $button
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $check = new Check();
        $form = $this->createForm(CheckType::class, $check);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($check);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.check_created');

            return $this->redirectToRoute('admin_check_index');
        }

        return $this->render('check/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST', 'HEAD'] )]
    public function show(Request $request, int $id) : Response
    {
        $check = $this->doctrine->getRepository(Check::class)->find($id);

        if(!$check) {
            throw new NotFoundHttpException(sprintf("Check %d not found", $id));
        }

        $form = $this->createForm(CheckType::class, $check);

        $originalOptions = new ArrayCollection();

        foreach ($check->getOptions() as $option) {
            $originalOptions->add($option);
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            foreach ($originalOptions as $option) {
                if (false === $check->getOptions()->contains($option)) {
                    $entityManager->remove($option);
                }
            }
            $entityManager->persist($check);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.check_updated');

            $nextAction = $form->get('saveAndAdd')->isClicked()
                ? 'admin_check_add'
                : 'admin_check_index';

            return $this->redirectToRoute($nextAction);
        }

        return $this->render('check/admin/edit.html.twig', [
            'item' => $check,
            'form' => $form->createView()
        ]);
    }

    #[Route('/remove/{id}', name: 'remove', methods: ['GET', 'POST', 'HEAD'] )]
    public function remove(Check $check, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($check);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_check_index');
        }

        return $this->render('check/admin/remove.html.twig', [
            'item' => $check,
        ]);
    }

    #[Route('/run/{id}', name: 'run')]
    public function run(Check $check, Request $request) : Response
    {
        $form = $this->createFormBuilder()
            ->add('options', ChoiceType::class, [
                'label' => false, //$check->getDescription(),
                'choices' => array_combine($check->getOptions()->toArray(), $check->getOptions()->toArray()),
                'multiple' => $check->getType() === 'extended',
                'expanded' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            $data = $form->getData();
            $result = (new Result())
                ->setCheckItem($check)
                ->setUsername($this->getUser()->getUserIdentifier())
            ;

            foreach ($data as $option) {
                $result
                    ->setCheckOption($option)
                    ->setIsValid($option->getCorrect())
                ;
            }

            $entityManager->persist($result);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.answer');

            return $this->redirectToRoute('admin_check_index');
        }

        return $this->render('check/admin/run.html.twig', [
            'item' => $check,
            'form' => $form->createView()
        ]);
    }
}
