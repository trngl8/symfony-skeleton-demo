<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserVerifyFormType;
use App\Model\UserVerify;

class UserVerificationController extends AbstractController
{
    #[Route('/user/verify', name: 'app_user_verify')]
    public function userVerify(Request $request): Response
    {
        $userVerify = new UserVerify();
        $form = $this->createForm(UserVerifyFormType::class, $userVerify);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'notice.check_code');
            return $this->redirectToRoute('default');
        }

        return $this->render('themes/default/user_verify.html.twig', [
            'verifyForm' => $form->createView(),
        ]);
    }
}
