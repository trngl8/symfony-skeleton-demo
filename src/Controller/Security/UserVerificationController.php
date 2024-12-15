<?php

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserVerifyFormType;
use App\Model\UserVerify;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class UserVerificationController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $em,
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly LoggerInterface $logger,
        private readonly string $adminEmail,
        private readonly string $appName
    )
    {
    }

    #[Route('/user/verify', name: 'app_user_verify')]
    public function verify(Request $request): Response
    {
        $userVerify = new UserVerify();
        $form = $this->createForm(UserVerifyFormType::class, $userVerify);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $userVerify->email]);

            if ($user) {
                try {
                    $verifyToken = $this->resetPasswordHelper->generateResetToken($user);
                } catch (TooManyPasswordRequestsException $e) {
                    $this->logger->error($e->getMessage());
                    $this->addFlash('warning', $e->getReason());
                    return $this->redirectToRoute('app_user_verify', ['available' => $e->getAvailableAt()->format('H:i:s')]);
                }

                $email = (new TemplatedEmail())
                    ->from(new Address($this->adminEmail, $this->appName))
                    ->to($user->getUsername())
                    ->subject('Your account verify link')
                    ->htmlTemplate('email/verify.html.twig')
                    ->context([
                        'verifyToken' => $verifyToken,
                    ])
                ;

                $this->mailer->send($email);
            }

            $this->addFlash('success', 'message.check_email');
            return $this->redirectToRoute('app_check_email');
        }

        return $this->render('themes/default/user_verify.html.twig', [
            'verifyForm' => $form->createView(),
        ]);
    }

    #[Route('/user/confirm', name: 'app_user_confirm')]
    public function confirm(Request $request): Response
    {
        $token = $request->query->get('token');
        $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);

        if (!$user) {
            $this->addFlash('error', 'notice.invalid_token');
            return $this->redirectToRoute('app_user_verify');
        }

        $user->setIsVerified(true);
        $this->em->flush();

        $this->addFlash('success', 'message.account_verified');
        return $this->redirectToRoute('default');
    }
}
