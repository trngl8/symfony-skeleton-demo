<?php

namespace App\Controller\Admin;

use App\Button\LinkToRoute;
use App\Entity\Card;
use App\Entity\Product;
use App\Form\Admin\ProductAdminType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/admin/product', name: 'admin_product_')]
class ProductController extends AbstractController
{
    const DEFAULT_LEVEL = 1;

    private $doctrine;

    private $repository;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository(Product::class);
    }

    #[Route('', name: 'index', methods: ['GET', 'HEAD'])]
    public function index(Request $request) : Response
    {
        $level = self::DEFAULT_LEVEL;

        if($request->get('level') > $level) {
            $level = $request->get('level');
        }

        $items = $this->repository->findBy(['level' => $level]);

        $button = new LinkToRoute('product_add', 'button.add', 'Add');

        $template = 'product/admin/index.html.twig';

        if('tree' === $request->get('view')) {
            $template = 'product/admin/tree.html.twig';
        }

        return $this->render($template, [
            'items' => $items,
            'button' => $button
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductAdminType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if($product->getParent()) {
                $product->setLevel($product->getParent()->getLevel() + 1);
            }

            $this->doctrine->getManager()->persist($product);

            $this->processUpload($product, $form);

            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.created');

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('product/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST', 'HEAD'] )]
    public function edit(Request $request, int $id) : Response
    {
        $product =$this->repository->find($id);

        if(!$product) {
            throw new NotFoundHttpException(sprintf("Product %d not found", $id));
        }

        $form = $this->createForm(ProductAdminType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if($product->getParent()) {
                $product->setLevel($product->getParent()->getLevel() + 1);
            }

            $this->processUpload($product, $form);

            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'flash.success.updated');

//            $nextAction = $form->get('saveAndAdd')->isClicked()
//                ? 'admin_product_add'
//                : 'admin_product_index';

            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('product/admin/edit.html.twig', [
            'item' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET', 'HEAD'] )]
    public function show(int $id) : Response
    {
        $product =$this->repository->find($id);

        if(!$product) {
            throw new NotFoundHttpException(sprintf("Product %d not found", $id));
        }

        $subItems = $this->repository->findBy(['parent' => $product->getId()]);

        return $this->render('product/admin/show.html.twig', [
            'item' => $product,
            'subitems' => $subItems,
        ]);
    }

    #[Route('/{id}/remove', name: 'remove', methods: ['GET', 'POST', 'HEAD'] )]
    public function remove(Product $product, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'flash.success.removed');

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('product/admin/remove.html.twig', [
            'item' => $product,
        ]);
    }

    private function processUpload(Product $product, FormInterface $form) : void
    {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    sprintf("%s/%s/%s", $this->getParameter('upload_directory'), 'products', $product->getId()),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', $e->getMessage());

                return;
            }

            $product->setFilename($newFilename);
        }
    }
}
